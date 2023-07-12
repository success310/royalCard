<div class="hidden w-full max-w-md p-12 lg:h-full lg:block bg-primary-600">
    <div class="flex items-center mb-8 space-x-4">
        <a href="{{ route('redir.locale') }}" class="flex items-center">
            <img src="{{ asset('assets/img/logo-white.svg') }}" class="mr-3 h-6 sm:h-9 block"
                alt="{{ config('default.app_name') }} Logo" />
        </a>
    </div>
    <div class="block p-8 text-white rounded-lg bg-primary-500">
        <h3 class="text-2xl font-semibold">{{ trans('install.installation') }}</h3>
    </div>
</div>
