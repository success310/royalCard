<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\File;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        if (! app()->runningInConsole()) {
            // Parse locale url segment
            $locales = explode('-', request()->segment(1));
            $locale = (isset($locales[1]))
                ? $locales[0].'_'.strtoupper($locales[1])
                : config('app.locale');

            // Verify if translation exists
            if (! File::exists(lang_path().'/'.$locale)) {
                $locale = config('app.locale');
            }

            app()->setLocale($locale);
        }

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
