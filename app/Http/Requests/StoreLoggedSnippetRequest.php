<?php

namespace App\Http\Requests;

use App\Support\AliasGenerator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class StoreLoggedSnippetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $aliasGenerator = app(AliasGenerator::class);

        return [
            'alias' => [
                'nullable',
                'string',
                'min:5',
                'max:50',
                'regex:/^[a-z0-9][a-z0-9.\-]*$/',
                function ($attribute, $value, $fail) use ($aliasGenerator) {
                    if (! $aliasGenerator->isUnique($value)) {
                        $fail('Este alias ya está en uso. Por favor, elige otro.');
                    }
                },
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:1048576'],
            'content_type' => ['required', 'in:text,url'],
            'language' => ['nullable', 'string', 'max:50'],
            'is_public' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:4', 'max:255'],
            'ttl' => ['required', 'in:7d,30d,90d,1y,never'],
        ];
    }

    public function messages(): array
    {
        return [
            'alias.regex' => 'El alias solo puede contener minúsculas, números, puntos y guiones.',
            'content.required' => 'El contenido de la nota es obligatorio.',
            'content.max' => 'El contenido no puede exceder los 1MB.',
            'content_type.in' => 'Tipo de contenido no válido.',
            'ttl.in' => 'Opción de expiración no válida.',
        ];
    }

    /**
     * Calcula la fecha de expiración según el TTL seleccionado.
     */
    public function getExpiresAt(): ?Carbon
    {
        return match ($this->input('ttl')) {
            '7d' => now()->addDays(7),
            '30d' => now()->addDays(30),
            '90d' => now()->addDays(90),
            '1y' => now()->addYear(),
            'never' => null,
            default => now()->addHours(24),
        };
    }
}
