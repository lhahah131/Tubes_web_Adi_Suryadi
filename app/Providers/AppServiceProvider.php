<?php

namespace App\Providers;

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
        // Prevent infinite redirect loop - check if already HTTPS
        if (request()->isSecure()) {
            return; // Already HTTPS, no need to force
        }

        // Force HTTPS when accessed via Cloudflare tunnel
        $host = request()->header('Host', '');
        $isCloudflared = str_contains($host, 'trycloudflare.com');
        
        // Check X-Forwarded-Proto header (from Cloudflared proxy)
        $forwardedProto = request()->header('X-Forwarded-Proto', '');
        $isHttpsForwarded = $forwardedProto === 'https';
        
        // Only force HTTPS if accessed via Cloudflared tunnel
        if ($isCloudflared && !request()->isSecure()) {
            \URL::forceScheme('https');
        }
        
        // For local development (192.168.x.x / localhost), use HTTP
        // This prevents forcing HTTPS on local network access
    }
}
