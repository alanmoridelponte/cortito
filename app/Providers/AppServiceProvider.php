<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('alias-check', fn (Request $request) => Limit::perMinute(30)->by($request->ip()));

        RateLimiter::for('snippet-create', fn (Request $request) => auth()->check()
                ? Limit::perMinute(30)->by($request->user()?->id ?? $request->ip())
                : Limit::perMinute(3)->by($request->ip())
        );

        RateLimiter::for('snippet-view', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
        RateLimiter::for('snippet-edit', fn (Request $request) => Limit::perMinute(30)->by($request->ip()));
        RateLimiter::for('snippet-delete', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
        RateLimiter::for('password-check', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
    }
}
