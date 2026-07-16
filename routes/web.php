<?php

use App\Http\Controllers\SnippetController;
use App\Http\Middleware\NoIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', [SnippetController::class, 'index'])->name('home');

Route::post('/snippets', [SnippetController::class, 'store'])
    ->middleware('throttle:snippet-create')
    ->name('snippets.store');

Route::post('/snippets/reroll', [SnippetController::class, 'reroll'])
    ->middleware('throttle:alias-check')
    ->name('snippets.reroll');

Route::get('/snippets/check-alias/{alias}', [SnippetController::class, 'checkAlias'])
    ->middleware('throttle:alias-check')
    ->name('snippets.check-alias');

// Cortitos: efímeros → nunca indexar (X-Robots-Tag: noindex via NoIndex).
Route::middleware(NoIndex::class)->group(function () {
    Route::get('/{alias}/edit', [SnippetController::class, 'edit'])
        ->middleware('throttle:snippet-edit')
        ->name('snippets.edit');
    Route::put('/{alias}', [SnippetController::class, 'update'])
        ->middleware('throttle:snippet-edit')
        ->name('snippets.update');
    Route::delete('/{alias}', [SnippetController::class, 'destroy'])
        ->middleware('throttle:snippet-delete')
        ->name('snippets.destroy');

    Route::get('/{alias}', [SnippetController::class, 'show'])
        ->middleware('throttle:snippet-view')
        ->name('snippets.show');
    Route::post('/{alias}', [SnippetController::class, 'show'])
        ->middleware('throttle:password-check')
        ->name('snippets.show.password');
});
