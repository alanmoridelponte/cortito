<?php

namespace App\Http\Requests;

use App\Support\AliasGenerator;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnonymousSnippetRequest extends FormRequest
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
                'min:6',
                'max:50',
                'regex:/^[a-z0-9]+(\.[a-z0-9]+){2}$/',
                function ($attribute, $value, $fail) use ($aliasGenerator) {
                    if (! $aliasGenerator->isUnique($value)) {
                        $fail('Este alias ya está en uso. Por favor, elige otro.');
                    }
                },
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:65535'],
            'content_type' => ['required', 'in:code,text'],
            'language' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:4', 'max:255'],
            'remember_owner' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'alias.regex' => 'El alias debe tener el formato: palabra.palabra.palabra (solo minúsculas, números y puntos).',
            'content.required' => 'El contenido del snippet es obligatorio.',
            'content.max' => 'El contenido no puede exceder los 64KB.',
            'content_type.in' => 'Los snippets anónimos solo soportan código y texto.',
        ];
    }
}
