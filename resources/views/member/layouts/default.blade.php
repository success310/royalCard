@php
$routeName = request()->route() ? request()->route()->getName() : null;
$routeDataDefinition = (isset($dataDefinition)) ? $dataDefinition->name : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <title>@yield('page_title')</title>
    <script src="{{ route('javascript.include.language') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (isset($robots) && $robots === false)
        <meta name="robots" content="noindex, nofollow" />
    @else
        <meta name="robots" content="index, follow" />
    @endif
    <x-meta.generic />
    <x-meta.favicons />
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900" x-data="{}" x-cloak x-show="true">
    <div class="flex flex-col col-span-1 h-screen">
        <!-- header -->
        <header class="">
            <nav class="bg-white border-gray-200 dark:border-gray-700 dark:bg-gray-800 border-b">
                <div class="flex flex-wrap justify-between items-center px-3 md:px-3 py-2.5">

                    @auth('member')
                    <div class="block md:hidden flex-initial mr-3">
                        <button data-drawer-target="drawer-navigation" data-drawer-show="drawer-navigation" aria-controls="drawer-navigation" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-expanded="false">
                            <span class="sr-only">{{ trans('common.open') }}</span>
                            <x-ui.icon icon="bars-4" class="w-6 h-6" />
                            <x-ui.icon icon="close" class="hidden w-6 h-6" />
                        </button>
                    </div>
                    @endauth

                    <a href="{{ route('member.index') }}" class="flex-1 items-center">
                        @if(config('default.app_demo'))
                            <img src="{{ asset('assets/img/logo-light.svg') }}" class="h-6 sm:h-7 block dark:hidden" alt="{{ config('default.app_name') }} Logo" />
                            <img src="{{ asset('assets/img/logo-dark.svg') }}" class="h-6 sm:h-7 hidden dark:block" alt="{{ config('default.app_name') }} Logo" />
                        @elseif(config('default.app_logo') != '')
                            @if(config('default.app_logo_dark') != '')
                                <img src="{{ config('default.app_logo') }}" class="h-6 sm:h-7 block dark:hidden" alt="{{ config('default.app_name') }} Logo" />
                                <img src="{{ config('default.app_logo_dark') }}" class="h-6 sm:h-7 hidden dark:block" alt="{{ config('default.app_name') }} Logo" />
                            @else
                                <img src="{{ config('default.app_logo') }}" class="h-6 sm:h-7 block" alt="{{ config('default.app_name') }} Logo" />
                            @endif
                        @else
                            <div class="text-lg font-bold text-gray-900 dark:text-gray-50">{{ config('default.app_name') }}</div>
                        @endif
                    </a>

                    <div class="flex items-center">
                        @auth('member')
                        <div class="hidden md:flex items-center">
                            <button type="button" class="flex text-sm rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown" data-dropdown-placement="bottom">
                              <span class="sr-only">Open user menu</span>
                              @if(auth('member')->user()->avatar)
                                <img class="w-8 h-8 rounded-full" src="{{ auth('member')->user()->avatar }}">
                              @else
                                <x-ui.icon icon="user-circle" class="m-1 w-7 h-7 text-gray-900 dark:text-gray-300"/>
                              @endif
                            </button>
                            <!-- Dropdown menu -->
                            <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-2xl dark:bg-gray-700 dark:divide-gray-600" id="user-dropdown">
                              <div class="px-4 py-3">
                                <span class="block text-sm text-gray-900 dark:text-white">{{ auth('member')->user()->name }}</span>
                                <span class="block text-sm font-medium text-gray-500 truncate dark:text-gray-400">{{ auth('member')->user()->email }}</span>
                              </div>

                              <ul class="py-1 font-light text-gray-500 dark:text-gray-400" aria-labelledby="user-menu-button">
                                <li>
                                    <a href="{{ route('member.index') }}" class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white">{{ trans('common.home') }}</a>
                                  </li>
                                <li>
                                  <a href="{{ route('member.dashboard') }}" class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white">{{ trans('common.dashboard') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('member.data.list', ['name' => 'account']) }}" class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white">{{ trans('common.account_settings') }}</a>
                                  </li>
                              </ul>
                              <ul class="py-1 font-light text-gray-500 dark:text-gray-400" aria-labelledby="dropdown">
                                  <li>
                                      <a href="{{ route('member.logout') }}" class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">{{ trans('common.logout') }}</a>
                                  </li>
                              </ul>
                            </div>
                        </div>
                        <span class="hidden md:block mr-3 ml-1 w-px h-5 bg-gray-200 dark:bg-gray-600 lg:inline"></span>
                        @else
                            <a rel="nofollow" href="{{ route('member.login') }}"
                                class="text-sm font-medium text-primary-600 md:mr-0 mr-3 dark:text-primary-500 hover:underline">{{ trans('common.log_in') }}</a>
                            <a rel="nofollow" href="{{ route('member.register') }}"
                                class="ml-5 mr-2 hidden md:block text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-3 py-2 lg:px-4 lg:py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">{{ trans('common.register') }}</a>
                        @endauth

                        @if (count($languages['all']) > 1)
                            <button type="button" data-dropdown-toggle="language-dropdown"
                                class="inline-flex items-center text-gray-900 dark:text-gray-300 hover:bg-gray-50 focus:ring-4 focus:ring-gray-300 font-medium rounded-full text-sm px-2 lg:px-2 py-2 lg:py-2 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800">
                                <div
                                    class="fi-{{ strtolower($languages['current']['countryCode']) }} fis w-5 h-5 rounded-full md:mr-2">
                                </div>
                                <x-ui.icon icon="carrot" class="hidden w-4 h-4 md:inline" />
                            </button>
                            <!-- Dropdown -->
                            <div class="hidden z-50 my-4 w-48 text-base list-none bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700"
                                id="language-dropdown">
                                <ul class="py-1" role="none">
                                    @foreach ($languages['all'] as $language)
                                        <li>
                                            <a href="{{ $language['memberIndex'] }}"
                                                class="flex items-center py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                                                role="menuitem">
                                                <div class="inline-flex items-center">
                                                    <div
                                                        class="w-4 h-4 mr-2 rounded-full fis fi-{{ strtolower($language['countryCode']) }}">
                                                    </div>
                                                    {{ $language['languageName'] }}
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <span class="mr-3 ml-3 w-px h-5 bg-gray-200 dark:bg-gray-600 lg:inline"></span>
                        @endif

                        <button id="theme-toggle" type="button"
                            class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-full text-sm p-2.5">
                            <x-ui.icon icon="moon" class="hidden w-4 h-4" id="theme-toggle-dark-icon" />
                            <x-ui.icon icon="sun" class="hidden w-4 h-4" id="theme-toggle-light-icon" />
                        </button>
                    </div>
                </div>
            </nav>
        </header>

        @auth('member')
            <!-- drawer -->
            <div id="drawer-navigation" class="fixed z-40 h-screen p-4 overflow-y-auto bg-white w-80 dark:bg-gray-800 transition-transform left-0 top-0 -translate-x-full" tabindex="-1">
                <h5 class="text-base font-semibold text-gray-500 uppercase dark:text-gray-400">{{ trans('common.menu') }}</h5>
                <button type="button" data-drawer-hide="drawer-navigation" aria-controls="drawer-navigation" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-full text-sm p-1.5 absolute top-2.5 right-2.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <x-ui.icon icon="close" class="w-5 h-5" />
                    <span class="sr-only">{{ trans('common.close') }}</span>
                </button>
                <div class="py-4 overflow-y-auto">

                    <ul class="space-y-2 site-drawer-nav">
                        <li @if ($routeName == 'member.index') class="active" @endif>
                            <a href="{{ route('member.index') }}"><x-ui.icon icon="home" class="w-6 h-6" /><span>{{ trans('common.home') }}</span></a>
                        </li>
                        <li><hr class="h-px mt-5 bg-gray-200 border-0 dark:bg-gray-700"></li>
                        <li><h5 class="inline-flex items-center ml-2 my-4 text-sm font-medium text-gray-400 dark:text-gray-400">{{ auth('member')->user()->name }}</h5></li>
                        <li @if ($routeDataDefinition == 'account') class="active" @endif>
                            <a href="{{ route('member.data.list', ['name' => 'account']) }}"><x-ui.icon icon="user-circle" class="w-6 h-6" /><span>{{ trans('common.account_settings') }}</span></a>
                        </li>
                        <li><a href="{{ route('member.logout') }}"><x-ui.icon icon="power" class="w-6 h-6" /><span>{{ trans('common.logout') }}</span></a></li>
                    </ul>

                </div>
            </div>
        @endauth

        <!-- content -->
        <div class="w-full mx-auto flex flex-grow max-w-screen-2xl">
            @yield('content')
        </div>

        <!-- footer -->
        <footer class="bg-white dark:bg-gray-800">
            <div class="p-4 py-6 mx-auto max-w-screen-xl md:p-8 lg:p-10">
                <div class="grid grid-cols-2 gap-8 md:grid-cols-3 lg:grid-cols-3">
                    <div>
                        <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">{{ trans('common.company') }}</h2>
                        <ul class="text-gray-500 dark:text-gray-400">
                            <li class="mb-4">
                                <a href="{{ route('member.index') }}" class=" hover:underline">{{ trans('common.home') }}</a>
                            </li>
                            <li class="mb-4">
                                <a href="{{ route('member.about') }}" class="hover:underline">{{ trans('common.about') }}</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">{{ trans('common.support') }}</h2>
                        <ul class="text-gray-500 dark:text-gray-400">
                            <li class="mb-4">
                                <a href="{{ route('member.faq') }}" class="hover:underline">{{ trans('common.faq') }}</a>
                            </li>
                            <li class="mb-4">
                                <a href="{{ route('member.contact') }}" class="hover:underline">{{ trans('common.contact') }}</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">{{ trans('common.legal') }}</h2>
                        <ul class="text-gray-500 dark:text-gray-400">
                            <li class="mb-4">
                                <a rel="nofollow" href="{{ route('member.terms') }}" class="hover:underline">{{ trans('common.terms') }}</a>
                            </li>
                            <li class="mb-4">
                                <a rel="nofollow" href="{{ route('member.privacy') }}" class="hover:underline ">{{ trans('common.privacy_policy') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <hr class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8">
                <div class="text-center">
                    <a href="{{ route('redir.locale') }}" class="flex-1 items-center">
                        @if(config('default.app_demo'))
                            <img src="{{ asset('assets/img/logo-light.svg') }}" class="mx-auto h-8 block dark:hidden" alt="{{ config('default.app_name') }} Logo" />
                            <img src="{{ asset('assets/img/logo-dark.svg') }}" class="mx-auto h-8 hidden dark:block" alt="{{ config('default.app_name') }} Logo" />
                        @elseif(config('default.app_logo') != '')
                            @if(config('default.app_logo_dark') != '')
                                <img src="{{ config('default.app_logo') }}" class="mx-auto h-8 block dark:hidden" alt="{{ config('default.app_name') }} Logo" />
                                <img src="{{ config('default.app_logo_dark') }}" class="mx-auto h-8 hidden dark:block" alt="{{ config('default.app_name') }} Logo" />
                            @else
                                <img src="{{ config('default.app_logo') }}" class="mx-auto h-8 block" alt="{{ config('default.app_name') }} Logo" />
                            @endif
                        @else
                            <div class="mx-auto text-lg font-bold text-gray-900 dark:text-gray-50">{{ config('default.app_name') }}</div>
                        @endif
                    </a>
                    <span class="block text-sm text-center text-gray-500 dark:text-gray-400 mt-4">
                        &copy; {{ date('Y') }} {{ config('app.name') }} - {!! trans('common.copyright_footer') !!}
                    </span>
                </div>
            </div>
          </footer>
    </div>

    <x-ui.toast />
    <x-ui.lightbox />
    @include('includes.demo')

</body>
</html>