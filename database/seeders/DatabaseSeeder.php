<?php

namespace Database\Seeders;

use App\Models\Snippet;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Snippets del usuario logueado
        Snippet::factory(5)->forUser($user)->create();

        // Snippets anónimos
        Snippet::factory(10)->anonymous()->create();

        // Snippet anónimo con contraseña
        Snippet::factory()->anonymous()->protected()->create();

        // Snippet expirado
        Snippet::factory()->anonymous()->expired()->create();
    }
}
