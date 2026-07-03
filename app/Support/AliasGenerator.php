<?php

namespace App\Support;

use App\Models\Snippet;
use Illuminate\Support\Str;

class AliasGenerator
{
    private array $words;

    private int $maxAttempts = 10;

    public function __construct()
    {
        $this->words = config('wordlist', []);
    }

    /**
     * Genera un alias único estilo bancario: palabra.palabra.palabra
     */
    public function generate(): string
    {
        for ($attempt = 1; $attempt <= $this->maxAttempts; $attempt++) {
            $alias = $this->composeAlias();

            if ($this->isUnique($alias)) {
                return $alias;
            }
        }

        throw new \RuntimeException("No se pudo generar un alias único tras {$this->maxAttempts} intentos.");
    }

    /**
     * Verifica si un alias es único (para uso al editar).
     */
    public function isUnique(string $alias, ?int $exceptId = null): bool
    {
        $query = Snippet::where('alias', $alias);

        if ($exceptId !== null) {
            $query->where('id', '!=', $exceptId);
        }

        return ! $query->exists();
    }

    /**
     * Valida el formato de un alias.
     */
    public function isValid(string $alias): bool
    {
        return (bool) preg_match('/^[a-z0-9][a-z0-9.\-]*$/', $alias);
    }

    private function composeAlias(): string
    {
        $word1 = $this->randomWord();
        $word2 = $this->randomWord();
        $word3 = $this->randomWord();

        return "{$word1}.{$word2}.{$word3}";
    }

    private function randomWord(): string
    {
        return Str::lower($this->words[array_rand($this->words)]);
    }
}
