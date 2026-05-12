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
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Set timezone from settings
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $timezone = \App\Models\Setting::where('key', 'timezone')->value('value') ?: 'Asia/Jakarta';
                config(['app.timezone' => $timezone]);
                date_default_timezone_set($timezone);
            }
        } catch (\Exception $e) {
            // Silently fail if table doesn't exist yet
        }
    }
}
