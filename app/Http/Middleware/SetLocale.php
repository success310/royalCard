<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SetLocale
{
    /**
     * Handle an incoming request and set the application's locale.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Set locale based on console status, request header or use default locale
        $locale = app()->runningInConsole()
            ? config('app.locale')
            : $request->header('locale', null);

        if (! $locale && $request->segment(1)) {
            // Extract locale from URL segment
            $locales = explode('-', $request->segment(1));
            $locale = isset($locales[1])
                ? $locales[0].'_'.strtoupper($locales[1])
                : config('app.locale');

            // Check for existing translation file and compare segment to locale
            // Redirect to 'redir.locale' route if conditions are not met
            if (! File::exists(lang_path().'/'.$locale) || $request->segment(1) !== strtolower(str_replace('_', '-', $locale))) {
                return redirect()->route('redir.locale');
            } 
            else {
                // Check if the matched locale is active
                if (!$this->localeIsActive($locale)) {
                    return redirect()->route('redir.locale');
                }
            }
        }

        if ($locale) {
            // Verify if locale is active, if not redirect to 'redir.locale' route
            if (!$this->localeIsActive($locale)) {
                return redirect()->route('redir.locale');
            }

            // Set the application's locale
            app()->setLocale($locale);
            \Carbon\Carbon::setLocale($locale);
        }

        return $next($request);
    }

    /**
     * Check if locale is active.
     *
     * @param  string $locale
     * @return bool
     */
    private function localeIsActive(string $locale): bool
    {
        // If we are running in demo mode, consider all locales active.
        if (config('default.app_demo')) {
            return true;
        }

        if (File::exists(lang_path().'/'.$locale . '/config.php')) {
            $config = require lang_path($locale . '/config.php');
            return array_key_exists('active', $config) && $config['active'];
        }

        return false;
    }
}
