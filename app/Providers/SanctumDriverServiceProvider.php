<?php

namespace App\Providers;

use Laravel\Sanctum\Sanctum;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;

class SanctumDriverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Tidak perlu ignoreMigrations() di versi baru
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        
        // Jika perlu custom token expiration (opsional)
        Sanctum::authenticateAccessTokensUsing(
            fn ($accessToken, $isValid) => $isValid && !$accessToken->expires_at?->isPast()
        );
    }
}