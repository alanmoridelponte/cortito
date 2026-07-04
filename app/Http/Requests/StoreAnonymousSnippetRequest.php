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
            'content' => ['required', 'string', 'max:5120'],
            'content_type' => ['required', 'in:text,url'],
            'language' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:4', 'max:255'],
            'remember_owner' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'alias.regex' => 'El alias solo puede contener minúsculas, números, puntos y guiones.',
            'content.required' => 'El contenido de la nota es obligatorio.',
            'content.max' => 'El contenido no puede exceder los 5KB.',
            'content_type.in' => 'Las notas anónimas solo soportan código y texto.',
        ];
    }
}
