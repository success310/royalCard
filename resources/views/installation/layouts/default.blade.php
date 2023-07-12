<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>@yield('page_title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-meta.favicons />
</head>

<body class="antialiased bg-white dark:bg-gray-800 text-gray-900 dark:text-white h-screen" x-data="{}" x-cloak
    x-show="true">
    <div class="flex flex-wrap justify-between mx-auto max-w-screen-xl h-full">
        <main class="flex-1">
            @yield('content')
        </main>
    </div>
</body>

</html>
