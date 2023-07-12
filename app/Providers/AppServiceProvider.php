<?php

namespace App\Providers;

use App\Services\I18nService;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // No services need to be registered.
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request, I18nService $i18nService, UrlGenerator $url)
    {
        // Log all queries
        /*
        \DB::listen(function ($query) {
            \Log::info([
                $query->sql,
                $query->bindings,
                $query->time
            ]);
        });
        */

        if (! app()->runningInConsole()) {
            // Force SSL if required
            if (config('default.force_ssl')) {
                $url->forceScheme('https');
            }
        }
    }
}
