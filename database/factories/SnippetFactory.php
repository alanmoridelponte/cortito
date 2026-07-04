<?php

namespace Database\Factories;

use App\Models\Snippet;
use App\Models\User;
use App\Support\AliasGenerator;
use App\Support\OwnerToken;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Snippet>
 */
class SnippetFactory extends Factory
{
    public function definition(): array
    {
        $aliasGenerator = app(AliasGenerator::class);

        $isUrl = fake()->boolean(30);

        return [
            'alias' => $aliasGenerator->generate(),
            'title' => fake()->sentence(3),
            'content' => $isUrl ? fake()->url() : fake()->text(500),
            'content_type' => $isUrl ? 'url' : 'text',
            'language' => null,
            'is_public' => true,
            'password' => null,
            'views_count' => fake()->numberBetween(0, 1000),
            'expires_at' => fake()->dateTimeBetween('+1 day', '+30 days'),
        ];
    }

    /**
     * Snippet anónimo (sin usuario, con owner_token).
     */
    public function anonymous(): static
    {
        return $this->state(fn () => [
            'user_id' => null,
            'owner_token' => OwnerToken::hash(OwnerToken::generate()),
            'content_type' => fake()->randomElement(['text', 'url']),
        ]);
    }

    /**
     * Snippet de usuario logueado.
     */
    public function forUser(?User $user = null): static
    {
        return $this->state(fn () => [
            'user_id' => $user?->id ?? User::factory(),
            'owner_token' => null,
        ]);
    }

    /**
     * Snippet expirado.
     */
    public function expired(): static
    {
        return $this->state(fn () => [
            'expires_at' => fake()->dateTimeBetween('-7 days', '-1 hour'),
        ]);
    }

    /**
     * Snippet con contraseña.
     */
    public function protected(): static
    {
        return $this->state(fn () => [
            'password' => 'secret123',
        ]);
    }

    /**
     * Snippet privado (solo usuarios logueados).
     */
    public function private(): static
    {
        return $this->state(fn () => [
            'is_public' => false,
        ]);
    }
}
