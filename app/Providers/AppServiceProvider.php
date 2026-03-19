<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use App\Models\Setting;

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
        // Force HTTPS if served via Cloudflare tunnel or in production
        if (str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        if (config('app.debug')) {
            DB::listen(function ($query) {
                Log::channel('query')->info(
                    $query->sql,
                    $query->bindings,
                    $query->time
                );
            });
        }

        // Share settings to all views
        View::composer('*', function ($view) {
            $view->with('setting', Setting::first());
        });
    }
}
