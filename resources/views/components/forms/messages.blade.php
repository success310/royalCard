<div {{ $attributes }}>
    @if (Session::has('error'))
        <div id="messages-alert-component"
            class="flex p-4 mb-6 text-primary-800 rounded-lg bg-primary-100 dark:bg-primary-700 dark:text-white"
            role="alert">
            <svg aria-hidden="true" class="flex-shrink-0 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd"></path>
            </svg>
            <div class="ml-3 text-sm font-medium">
                {!! Session::get('error') !!}
            </div>
            <button type="button"
                class="ml-auto -mx-1.5 -my-1.5 bg-primary-100 text-primary-500 rounded-lg focus:ring-2 focus:ring-primary-400 p-1.5 hover:bg-primary-200 inline-flex h-8 w-8 dark:bg-primary-700 dark:text-white dark:hover:bg-primary-800"
                data-dismiss-target="#messages-alert-component" aria-label="Close">
                <span class="sr-only">{{ trans('common.close') }}</span>
                <x-ui.icon icon="close" class="w-5 h-5"/>
            </button>
        </div>
    @else
        @if ($errors->any())
            <div id="messages-alert-component"
                class="flex p-4 mb-6 text-primary-800 rounded-lg bg-primary-100 dark:bg-primary-700 dark:text-white"
                role="alert">
                <svg aria-hidden="true" class="flex-shrink-0 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3 text-sm font-medium">
                    {!! $msg !!}
                    @if (1 == 2)
                        <ul class="mt-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <button type="button"
                    class="ml-auto -mx-1.5 -my-1.5 bg-primary-100 text-primary-500 rounded-lg focus:ring-2 focus:ring-primary-400 p-1.5 hover:bg-primary-200 inline-flex h-8 w-8 dark:bg-primary-700 dark:text-white dark:hover:bg-primary-800"
                    data-dismiss-target="#messages-alert-component" aria-label="Close">
                    <span class="sr-only">{{ trans('common.close') }}</span>
                    <x-ui.icon icon="close" class="w-5 h-5"/>
                </button>
            </div>
        @endif
    @endif
    @if (Session::has('success'))
        <div id="alert-success"
            class="flex p-4 mb-6 text-green-800 rounded-lg bg-green-100 dark:bg-green-950 dark:text-green-400"
            role="alert">
            <x-ui.icon icon="info" class="flex-shrink-0 w-5 h-5"/>
            <div class="ml-3 text-sm font-medium">
                {!! Session::get('success') !!}
            </div>
            <button type="button"
                class="ml-auto -mx-1.5 -my-1.5 bg-green-100 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex h-8 w-8 dark:bg-green-950 dark:text-green-400 dark:hover:bg-green-700"
                data-dismiss-target="#alert-success" aria-label="Close">
                <span class="sr-only">{{ trans('common.close') }}</span>
                <x-ui.icon icon="close" class="w-5 h-5"/>
            </button>
        </div>
    @endif
    @if (Session::has('info'))
        <div id="alert-info"
            class="flex p-4 mb-6 text-blue-800 rounded-lg bg-blue-100 dark:bg-blue-950 dark:text-blue-400"
            role="alert">
            <x-ui.icon icon="info" class="flex-shrink-0 w-5 h-5"/>
            <div class="ml-3 text-sm font-medium">
                {!! Session::get('success') !!}
            </div>
            <button type="button"
                class="ml-auto -mx-1.5 -my-1.5 bg-blue-100 text-blue-500 rounded-lg focus:ring-2 focus:ring-blue-400 p-1.5 hover:bg-blue-200 inline-flex h-8 w-8 dark:bg-blue-950 dark:text-blue-400 dark:hover:bg-blue-700"
                data-dismiss-target="#alert-info" aria-label="Close">
                <span class="sr-only">{{ trans('common.close') }}</span>
                <x-ui.icon icon="close" class="w-5 h-5"/>
            </button>
        </div>
    @endif
    @if (Session::has('warning'))
        <div id="alert-warning"
            class="flex p-4 mb-6 text-primary-800 rounded-lg bg-primary-100 dark:bg-primary-700 dark:text-white"
            role="alert">
            <x-ui.icon icon="exclamation-triangle" class="flex-shrink-0 w-5 h-5"/>
            <div class="ml-3 text-sm font-medium">
                {!! Session::get('warning') !!}
            </div>
            <button type="button"
                class="ml-auto -mx-1.5 -my-1.5 bg-primary-100 text-primary-500 rounded-lg focus:ring-2 focus:ring-primary-400 p-1.5 hover:bg-primary-200 inline-flex h-8 w-8 dark:bg-primary-700 dark:text-white dark:hover:bg-primary-800"
                data-dismiss-target="#alert-warning" aria-label="Close">
                <span class="sr-only">{{ trans('common.close') }}</span>
                <x-ui.icon icon="close" class="w-5 h-5"/>
            </button>
        </div>
    @endif
</div>
