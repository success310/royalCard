<?php

namespace App\Http\Controllers\I18n;

use App\Http\Controllers\Controller;
use App\Services\I18nService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class LocaleController extends Controller
{
    /**
     * Redirect to the preferred locale.
     *
     * @param  Request  $request The incoming HTTP request.
     * @param  I18nService  $i18nService The service for handling internationalization.
     * @return \Symfony\Component\HttpFoundation\Response The redirect response to the preferred locale.
     */
    public function redirectToLocale(Request $request, I18nService $i18nService)
    {
        // Get the preferred locale from the I18n service
        $locale = $i18nService->getPreferredLocale($parsedForUrl = true);

        if ($locale) {
            // Use URL::to() method to create a fully qualified URL for the locale
            $domain = URL::to($locale);

            // Return a redirect to the fully qualified URL
            // This will be a 301 redirect which indicates the resource has been permanently moved
            // No caching will be used as indicated by the 'Cache-Control' header
            return redirect($domain, 301)
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        }

        // If no locale was found, this will result in a 404 error.
        // In production systems this should never happen if locales are properly configured.
        abort(404, 'No translation found.');
    }
}
