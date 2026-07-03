<?php

use App\Http\Controllers\SnippetController;
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

Route::get('/{alias}/edit', [SnippetController::class, 'edit'])->name('snippets.edit');
Route::put('/{alias}', [SnippetController::class, 'update'])->name('snippets.update');
Route::delete('/{alias}', [SnippetController::class, 'destroy'])->name('snippets.destroy');

Route::get('/{alias}', [SnippetController::class, 'show'])->name('snippets.show');
Route::post('/{alias}', [SnippetController::class, 'show'])->name('snippets.show.password');
