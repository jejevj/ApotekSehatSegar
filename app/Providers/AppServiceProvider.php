<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use App\Models\Setting;
use App\Services\BusinessConfigService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BusinessConfigService::class, function () {
            return new BusinessConfigService();
        });
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
            try {
                $setting = Setting::first() ?? Setting::withoutGlobalScopes()->first() ?? new Setting();
            } catch (\Exception $e) {
                $setting = new Setting();
            }
            $view->with('setting', $setting);
        });

        // Share businessConfig to all views
        View::composer('*', function ($view) {
            $view->with('businessConfig', app(BusinessConfigService::class));
        });
    }
}
