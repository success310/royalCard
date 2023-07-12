<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <x-meta.favicons />
</head>

<body class="antialiased h-full" x-data="{}" x-cloak x-show="true">
    <div class="bg-white min-h-full px-4 py-16 sm:px-6 sm:py-24 md:grid md:place-items-center lg:px-8">
        <div class="max-w-max mx-auto">
            <main class="sm:flex">
                <p class="text-4xl font-extrabold text-{{ config('default.app_color_primary') }}-600 sm:text-5xl">
                    @yield('code')</p>
                <div class="sm:ml-6">
                    <div class="sm:border-l sm:border-gray-200 sm:pl-6">
                        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">@yield('title')
                        </h1>
                        <p class="mt-1 text-base text-gray-500">@yield('message')</p>
                    </div>
                    <div class="mt-10 flex space-x-3 sm:border-l sm:border-transparent sm:pl-6">

                        <a href="{{ route('redir.locale') }}" class="link">{{ trans('common.go_back_home') }}</a>

                    </div>
                </div>
            </main>
        </div>
    </div>

</body>

</html>
