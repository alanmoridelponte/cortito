<?php

use App\Models\Snippet;
use App\Support\AliasGenerator;
use App\Support\OwnerToken;

test('home page shows form with generated alias', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('cortito');
    $response->assertSee('Crear cortito');
});

test('reroll endpoint returns new alias', function () {
    $response = $this->postJson('/snippets/reroll');

    $response->assertStatus(200);
    $response->assertJsonStructure(['alias']);
    $response->assertJson(fn ($json) => $json->has('alias'));
});

test('check alias returns available when unique', function () {
    $alias = app(AliasGenerator::class)->generate();

    $response = $this->getJson("/snippets/check-alias/{$alias}");

    $response->assertStatus(200);
    $response->assertJson(['available' => true]);
});

test('check alias returns unavailable when taken', function () {
    $snippet = Snippet::factory()->create(['alias' => 'test.taken.alias']);

    $response = $this->getJson('/snippets/check-alias/test.taken.alias');

    $response->assertStatus(200);
    $response->assertJson(['available' => false]);
});

test('check alias returns invalid format for bad format', function () {
    $response = $this->getJson('/snippets/check-alias/BAD FORMAT!');

    $response->assertStatus(200);
    $response->assertJson(['available' => false, 'reason' => 'invalid_format']);
});

test('anonymous user can create snippet', function () {
    $alias = app(AliasGenerator::class)->generate();

    $response = $this->post('/snippets', [
        'alias' => $alias,
        'content' => 'Hello from anonymous',
        'content_type' => 'text',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('snippets', [
        'alias' => $alias,
        'content' => 'Hello from anonymous',
        'user_id' => null,
    ]);
});

test('anonymous snippet forces is_public true', function () {
    $alias = app(AliasGenerator::class)->generate();

    $this->post('/snippets', [
        'alias' => $alias,
        'content' => 'Public only',
        'content_type' => 'text',
    ]);

    $snippet = Snippet::where('alias', $alias)->first();
    expect($snippet->is_public)->toBeTrue();
});

test('anonymous snippet expires in 24 hours', function () {
    $alias = app(AliasGenerator::class)->generate();

    $this->post('/snippets', [
        'alias' => $alias,
        'content' => 'Expiring soon',
        'content_type' => 'text',
    ]);

    $snippet = Snippet::where('alias', $alias)->first();
    expect($snippet->expires_at)->not->toBeNull();
    expect($snippet->expires_at->diffInHours(now()))->toBeLessThanOrEqual(24);
});

test('duplicate alias is rejected', function () {
    Snippet::factory()->create(['alias' => 'duplicate.existing.alias']);

    $response = $this->post('/snippets', [
        'alias' => 'duplicate.existing.alias',
        'content' => 'Should fail',
        'content_type' => 'text',
    ]);

    $response->assertSessionHasErrors('alias');
});

test('show snippet increments views count', function () {
    $snippet = Snippet::factory()->anonymous()->create(['views_count' => 0]);

    $this->get("/{$snippet->alias}")->assertStatus(200);

    expect($snippet->fresh()->views_count)->toBe(1);
});

test('expired snippet returns 410', function () {
    $snippet = Snippet::factory()->expired()->create();

    $response = $this->get("/{$snippet->alias}");

    $response->assertStatus(410);
});

test('protected snippet shows password form', function () {
    $snippet = Snippet::factory()->protected()->create();

    $response = $this->get("/{$snippet->alias}");

    $response->assertStatus(200);
    $response->assertSee('protegido');
    $response->assertSee('Contraseña');
});

test('protected snippet with correct password shows content', function () {
    $snippet = Snippet::factory()->protected()->create(['password' => 'secret123']);

    $response = $this->post("/{$snippet->alias}", ['password' => 'secret123']);

    $response->assertStatus(200);
    $response->assertSee($snippet->content);
});

test('protected snippet with wrong password shows error', function () {
    $snippet = Snippet::factory()->protected()->create(['password' => 'secret123']);

    $response = $this->post("/{$snippet->alias}", ['password' => 'wrong']);

    $response->assertSessionHasErrors('password');
});

test('home page shows snippets list for guest', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Mis cortitos');
    $response->assertSee('Crear cortito');
});

test('anonymous snippets are listed on home with owner cookie', function () {
    $token = OwnerToken::generate();
    $alias = app(AliasGenerator::class)->generate();

    Snippet::create([
        'alias' => $alias,
        'content' => 'List me',
        'content_type' => 'text',
        'owner_token' => OwnerToken::hash($token),
        'expires_at' => now()->addHours(24),
    ]);

    $response = $this->withCookie('cortito_owner', $token)->get('/');

    $response->assertStatus(200);
    $response->assertSee($alias);
});

test('missing snippet returns 404', function () {
    $response = $this->get('/no.existe.alias');

    $response->assertStatus(404);
});

test('store validates content is required', function () {
    $response = $this->post('/snippets', [
        'content' => '',
        'content_type' => 'text',
    ]);

    $response->assertSessionHasErrors('content');
});

test('store validates content_type for anonymous', function () {
    $alias = app(AliasGenerator::class)->generate();

    $response = $this->post('/snippets', [
        'alias' => $alias,
        'content' => 'test',
        'content_type' => 'markdown',
    ]);

    $response->assertSessionHasErrors('content_type');
});

test('url snippet redirects to stored url', function () {
    $alias = app(AliasGenerator::class)->generate();

    Snippet::create([
        'alias' => $alias,
        'content' => 'https://laravel.com/docs',
        'content_type' => 'url',
        'is_public' => true,
        'expires_at' => now()->addDays(7),
    ]);

    $response = $this->get("/{$alias}");

    $response->assertRedirect('https://laravel.com/docs');
});

test('url snippet does not increment views count', function () {
    $alias = app(AliasGenerator::class)->generate();

    $snippet = Snippet::create([
        'alias' => $alias,
        'content' => 'https://example.com',
        'content_type' => 'url',
        'views_count' => 0,
        'is_public' => true,
        'expires_at' => now()->addDays(7),
    ]);

    $this->get("/{$alias}");

    expect($snippet->fresh()->views_count)->toBe(0);
});

test('store validates alias format', function () {
    $response = $this->post('/snippets', [
        'alias' => 'BAD FORMAT!',
        'content' => 'test',
        'content_type' => 'text',
    ]);

    $response->assertSessionHasErrors('alias');
});
