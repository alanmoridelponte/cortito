<?php

namespace App\Http\Controllers;

use App\Models\Snippet;
use App\Support\AliasGenerator;
use App\Support\OwnerToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class SnippetController extends Controller
{
    private const ANONYMOUS_SNIPPET_LIMIT = 10;

    public function index()
    {
        $alias = app(AliasGenerator::class)->generate();

        $data = [
            'alias' => $alias,
            'contentTypes' => $this->contentTypesForUser(),
            'maxChars' => auth()->check() ? 1048576 : 5120,
        ];

        if (auth()->check()) {
            $data['snippets'] = Snippet::where('user_id', auth()->id())
                ->active()
                ->orderByDesc('created_at')
                ->paginate(20);
        } else {
            $hash = OwnerToken::getHashFromRequest(request());
            $data['anonymousCount'] = $hash
                ? Snippet::where('owner_token', $hash)->whereNull('user_id')->active()->count()
                : 0;
            $data['anonymousLimit'] = self::ANONYMOUS_SNIPPET_LIMIT;
            $data['hasOwnerCookie'] = OwnerToken::hasCookie(request());

            $data['snippets'] = OwnerToken::getSnippetsForRequest(request());
        }

        return view('snippets.home', $data);
    }

    public function store(Request $request)
    {
        if (! $request->user() && ! $request->boolean('cookie_consent_accepted') && ! OwnerToken::hasCookie($request)) {
            throw ValidationException::withMessages([
                'cookie_consent' => 'Debés aceptar las cookies de propiedad para crear cortitos.',
            ]);
        }

        $validated = $this->validateSnippet($request);

        $token = null;

        if (auth()->check()) {
            $validated['expires_at'] = $this->resolveExpiresAt($request->input('ttl'));
            $validated['user_id'] = auth()->id();
            $validated['is_public'] = $request->boolean('is_public', true);
        } else {
            if (OwnerToken::hasCookie($request)) {
                $tokenHash = OwnerToken::getHashFromRequest($request);
                $token = null;
            } else {
                $token = OwnerToken::generate();
                $tokenHash = OwnerToken::hash($token);
            }

            $count = Snippet::where('owner_token', $tokenHash)
                ->whereNull('user_id')
                ->count();

            if ($count >= self::ANONYMOUS_SNIPPET_LIMIT) {
                throw ValidationException::withMessages([
                    'content' => 'Alcanzaste el límite de '.self::ANONYMOUS_SNIPPET_LIMIT.' cortitos gratuitos. Registrate para crear ilimitados.',
                ]);
            }

            $validated['expires_at'] = now()->addHours(24);
            $validated['is_public'] = true;
            $validated['owner_token'] = $tokenHash;
        }

        $snippet = Snippet::create($validated);

        \Log::info('Snippet created', [
            'alias' => $snippet->alias,
            'content_type' => $snippet->content_type,
            'has_password' => $snippet->isProtected(),
            'password_in_validated' => isset($validated['password']),
        ]);

        $redirectTo = $snippet->content_type === 'url'
            ? route('home')
            : route('snippets.show', $snippet->alias);

        $response = Redirect::to($redirectTo);

        if ($token) {
            $response = OwnerToken::setCookie($response, $token);
        }

        return $response;
    }

    public function show(string $alias)
    {
        $snippet = Snippet::where('alias', $alias)->firstOrFail();

        \Log::info('Snippet show', [
            'alias' => $snippet->alias,
            'is_protected' => $snippet->isProtected(),
            'method' => request()->method(),
            'has_password_input' => request()->has('password'),
        ]);

        if ($snippet->isExpired()) {
            abort(410, 'Este cortito ha expirado.');
        }

        if ($snippet->isProtected()) {
            if (request()->isMethod('post')) {
                $passwordInput = request()->input('password', '');
                $verified = $snippet->verifyPassword($passwordInput);

                \Log::info('Password verification', [
                    'alias' => $snippet->alias,
                    'input_empty' => empty($passwordInput),
                    'verified' => $verified,
                ]);

                if ($verified) {
                    return $this->resolveShowResponse($snippet);
                }

                throw ValidationException::withMessages([
                    'password' => 'La contraseña es incorrecta.',
                ]);
            }

            return view('snippets.show', ['snippet' => $snippet, 'unlocked' => false]);
        }

        return $this->resolveShowResponse($snippet);
    }

    private function resolveShowResponse(Snippet $snippet)
    {
        if ($snippet->content_type === 'url') {
            return redirect()->to($snippet->content, 302);
        }

        $snippet->increment('views_count');

        return view('snippets.show', ['snippet' => $snippet, 'unlocked' => true]);
    }

    public function edit(string $alias)
    {
        $snippet = Snippet::where('alias', $alias)->firstOrFail();

        if (! $snippet->canBeEditedBy(request())) {
            abort(403, 'No tenés permiso para editar este cortito.');
        }

        if ($snippet->isExpired()) {
            abort(410, 'Este cortito ha expirado.');
        }

        return response()->json([
            'content' => $snippet->content,
            'content_type' => $snippet->content_type,
            'language' => $snippet->language,
            'title' => $snippet->title,
            'ttl' => $snippet->expires_at
                ? match (true) {
                    $snippet->expires_at->diffInDays(now()) <= 7 => '7d',
                    $snippet->expires_at->diffInDays(now()) <= 30 => '30d',
                    $snippet->expires_at->diffInDays(now()) <= 90 => '90d',
                    default => '1y',
                }
                : 'never',
            'is_public' => $snippet->is_public,
            'has_password' => $snippet->isProtected(),
        ]);
    }

    public function update(Request $request, string $alias)
    {
        $snippet = Snippet::where('alias', $alias)->firstOrFail();

        if (! $snippet->canBeEditedBy($request)) {
            abort(403, 'No tenés permiso para editar este cortito.');
        }

        if ($snippet->isExpired()) {
            abort(410, 'Este cortito ha expirado.');
        }

        $validated = $this->validateSnippet($request, $snippet->id);

        if ($request->boolean('remove_password')) {
            $validated['password'] = null;
        } elseif (! isset($validated['password']) || $validated['password'] === '') {
            unset($validated['password']);
        }

        $validated['alias'] = $snippet->alias;

        if (auth()->check()) {
            $validated['is_public'] = $request->boolean('is_public', $snippet->is_public);
            if (isset($validated['ttl'])) {
                $validated['expires_at'] = $this->resolveExpiresAt($validated['ttl']);
                unset($validated['ttl']);
            }
        }

        unset($validated['remember_owner']);

        $snippet->update($validated);
        $snippet->markAsEdited();

        $redirectTo = $snippet->content_type === 'url'
            ? route('home')
            : route('snippets.show', $snippet->alias);

        return redirect()->to($redirectTo)
            ->with('success', 'Cortito actualizado correctamente.');
    }

    public function destroy(Request $request, string $alias)
    {
        $snippet = Snippet::where('alias', $alias)->firstOrFail();

        if (! $snippet->canBeEditedBy($request)) {
            abort(403, 'No tenés permiso para eliminar este cortito.');
        }

        $snippet->delete();

        return redirect()->route('home')
            ->with('success', 'Cortito eliminado correctamente.');
    }

    public function reroll(): JsonResponse
    {
        $alias = app(AliasGenerator::class)->generate();

        return response()->json(['alias' => $alias]);
    }

    public function checkAlias(string $alias): JsonResponse
    {
        $generator = app(AliasGenerator::class);

        if (! $generator->isValid($alias)) {
            return response()->json(['available' => false, 'reason' => 'invalid_format']);
        }

        return response()->json(['available' => $generator->isUnique($alias)]);
    }

    private function validateSnippet(Request $request, ?int $ignoreId = null): array
    {
        $rules = [
            'alias' => [
                'nullable',
                'string',
                'min:5',
                'max:250',
                'regex:/^[a-z0-9][a-z0-9.\-]*$/',
                function ($attribute, $value, $fail) use ($ignoreId) {
                    if (! app(AliasGenerator::class)->isUnique($value, $ignoreId)) {
                        $fail('Este alias ya está en uso. Por favor, elige otro.');
                    }
                },
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:'.(auth()->check() ? '1048576' : '5120')],
            'content_type' => ['required', 'in:text,url'],
            'language' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:4', 'max:255'],
        ];

        if (auth()->check()) {
            $rules['ttl'] = ['nullable', 'in:7d,30d,90d,1y,never'];
            $rules['is_public'] = ['nullable', 'boolean'];
            $rules['password'] = ['nullable', 'string', 'min:4', 'max:255'];
        }

        return $request->validate($rules);
    }

    private function contentTypesForUser(): array
    {
        return ['url' => 'Enlace Acortador', 'text' => 'Texto'];
    }

    private function resolveExpiresAt(?string $ttl)
    {
        return match ($ttl) {
            '7d' => now()->addDays(7),
            '30d' => now()->addDays(30),
            '90d' => now()->addDays(90),
            '1y' => now()->addYear(),
            'never' => null,
            default => now()->addDays(7),
        };
    }
}
