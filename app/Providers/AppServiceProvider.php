<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\View;
use App\Models\Setting;
use URL;

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
        if (config('app.debug')) {
            DB::listen(function ($query) {
                Log::channel('query')->info(
                    $query->sql,
                    $query->bindings,
                    $query->time
                );
                if (config('app.env') !== 'local' || env('FORCE_HTTPS', false)) {
                    URL::forceScheme('https');
                }
            });
        }

        // Share settings to all views
        View::composer('*', function ($view) {
            $view->with('setting', Setting::first());
        });
    }
}
