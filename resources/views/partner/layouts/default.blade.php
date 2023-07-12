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
    <meta name="robots" content="noindex, nofollow" />
    <x-meta.generic />
    <x-meta.favicons />
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900" x-data="{}" x-cloak x-show="true">
    <div class="flex flex-col col-span-1 h-screen">
        <!-- header -->
        <header class="">
            <nav class="bg-white border-gray-200 dark:border-gray-700 dark:bg-gray-800 border-b">
                <div class="flex flex-wrap justify-between items-center px-3 md:px-3 py-2.5">

                    @auth('partner')
                    <div class="block md:hidden flex-initial mr-3">
                        <button data-drawer-target="drawer-navigation" data-drawer-show="drawer-navigation" aria-controls="drawer-navigation" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-expanded="false">
                            <span class="sr-only">{{ trans('common.open') }}</span>
                            <x-ui.icon icon="bars-4" class="w-6 h-6" />
                            <x-ui.icon icon="close" class="hidden w-6 h-6" />
                        </button>
                    </div>
                    @endauth

                    <a href="{{ route('partner.index') }}" class="flex-1 items-center">
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
                        @auth('partner')
                        <div class="hidden md:flex items-center">
                            <button type="button" class="flex text-sm rounded-full md:mr-3 mr-2 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown" data-dropdown-placement="bottom">
                                <span class="sr-only">Open user menu</span>
                                @if(auth('partner')->user()->avatar)
                                    <img class="w-8 h-8 rounded-full" src="{{ auth('partner')->user()->avatar }}">
                                @else
                                    <x-ui.icon icon="user-circle" class="m-1 w-7 h-7 text-gray-900 dark:text-gray-300"/>
                                @endif
                            </button>
                            <!-- Dropdown menu -->
                            <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-2xl dark:bg-gray-700 dark:divide-gray-600" id="user-dropdown">
                              <div class="px-4 py-3">
                                <span class="block text-sm text-gray-900 dark:text-white">{{ auth('partner')->user()->name }}</span>
                                <span class="block text-sm font-medium text-gray-500 truncate dark:text-gray-400">{{ auth('partner')->user()->email }}</span>
                              </div>

                              <ul class="py-1 font-light text-gray-500 dark:text-gray-400" aria-labelledby="user-menu-button">
                                <li>
                                  <a href="{{ route('partner.index') }}" class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white @if ($routeName == 'partner.index') text-black dark:text-white @endif">{{ trans('common.dashboard') }}</a>
                                </li>
                                <li>
                                  <a href="{{ route('partner.data.list', ['name' => 'account']) }}" class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white @if ($routeDataDefinition == 'account') text-black dark:text-white @endif">{{ trans('common.account_settings') }}</a>
                                </li>
                              </ul>
                              <ul class="py-1 font-light text-gray-500 dark:text-gray-400" aria-labelledby="dropdown">
                                  <li>
                                      <a href="{{ route('partner.logout') }}" class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">{{ trans('common.logout') }}</a>
                                  </li>
                              </ul>
                            </div>
                        </div>
                        <span class="hidden md:block mr-3 ml-1 w-px h-5 bg-gray-200 dark:bg-gray-600 lg:inline"></span>
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
                                            <a href="{{ $language['partnerIndex'] }}"
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
            @auth('partner')
                <nav class="hidden md:block bg-gray-100 border-gray-200 dark:bg-gray-700 dark:border-gray-600 border-b">
                    <div class="grid py-4 px-4 mx-auto max-w-screen-2xl lg:grid-cols-2 md:px-6">

                        <div class="flex items-center">
                            <ul class="flex flex-row items-center mt-0 space-x-8 text-sm font-medium">
                                <li>
                                    <a href="{{ route('partner.index') }}" class="hover:text-black dark:hover:text-white @if ($routeName == 'partner.index') text-gray-900 dark:text-white @else text-gray-700 dark:text-gray-400 @endif">{{ trans('common.dashboard') }}</a>
                                </li>
                                <li>
                                    <button data-dropdown-toggle="navDropDown1" data-dropdown-trigger="click" class="flex items-center justify-between w-full hover:text-black dark:hover:text-white @if (in_array($routeDataDefinition, ['clubs', 'cards', 'rewards'])) text-gray-900 dark:text-white @else text-gray-700 dark:text-gray-400 @endif">{{ trans('common.campaigns') }} <x-ui.icon icon="carrot" class="w-5 h-5 ml-1" /></button>
                                    <!-- Dropdown menu -->
                                    <div id="navDropDown1" class="hidden font-normal bg-white divide-y divide-gray-100 rounded-lg shadow-2xl z-20 w-44 dark:bg-gray-700 dark:divide-gray-600">
                                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-400">
                                          <li>
                                            <a href="{{ route('partner.data.list', ['name' => 'clubs']) }}" class="block px-4 py-2 hover:text-black dark:hover:text-white @if ($routeDataDefinition == 'clubs') text-gray-900 dark:text-white @else text-gray-700 dark:text-gray-400 @endif">{{ trans('common.clubs') }}</a>
                                          </li>
                                          <li>
                                            <a href="{{ route('partner.data.list', ['name' => 'cards']) }}" class="block px-4 py-2 hover:text-black dark:hover:text-white @if ($routeDataDefinition == 'cards') text-gray-900 dark:text-white @else text-gray-700 dark:text-gray-400 @endif">{{ trans('common.loyalty_cards') }}</a>
                                          </li>
                                          <li>
                                            <a href="{{ route('partner.data.list', ['name' => 'rewards']) }}" class="block px-4 py-2 hover:text-black dark:hover:text-white @if ($routeDataDefinition == 'rewards') text-gray-900 dark:text-white @else text-gray-700 dark:text-gray-400 @endif">{{ trans('common.rewards') }}</a>
                                          </li>
                                        </ul>
                                    </div>
                                </li>

                                <li>
                                    <button data-dropdown-toggle="navDropDown2" data-dropdown-trigger="click" class="flex items-center justify-between w-full hover:text-black dark:hover:text-white @if (in_array($routeDataDefinition, ['staff', 'members'])) text-gray-900 dark:text-white @else text-gray-700 dark:text-gray-400 @endif">{{ trans('common.users') }} <x-ui.icon icon="carrot" class="w-5 h-5 ml-1" /></button>
                                    <!-- Dropdown menu -->
                                    <div id="navDropDown2" class="hidden font-normal bg-white divide-y divide-gray-100 rounded-lg shadow-2xl z-20 w-44 dark:bg-gray-700 dark:divide-gray-600">
                                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-400">
                                          <li>
                                            <a href="{{ route('partner.data.list', ['name' => 'staff']) }}" class="block px-4 py-2 hover:text-black dark:hover:text-white @if ($routeDataDefinition == 'staff') text-gray-900 dark:text-white @else text-gray-700 dark:text-gray-400 @endif">{{ trans('common.staff') }}</a>
                                          </li>
                                          <li>
                                            <a href="{{ route('partner.data.list', ['name' => 'members']) }}" class="block px-4 py-2 hover:text-black dark:hover:text-white @if ($routeDataDefinition == 'members') text-gray-900 dark:text-white @else text-gray-700 dark:text-gray-400 @endif">{{ trans('common.members') }}</a>
                                          </li>
                                        </ul>
                                    </div>
                                </li>

                                <li>
                                    <a href="{{ route('partner.analytics') }}" class="hover:text-black dark:hover:text-white @if (Str::startsWith($routeName, 'partner.analytics')) text-gray-900 dark:text-white @else text-gray-700 dark:text-gray-400 @endif">{{ trans('common.analytics') }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            @endauth
        </header>

        @auth('partner')
            <!-- drawer -->
            <div id="drawer-navigation" class="fixed z-40 h-screen p-4 overflow-y-auto bg-white w-80 dark:bg-gray-800 transition-transform left-0 top-0 -translate-x-full" tabindex="-1">
                <h5 class="text-base font-semibold text-gray-500 uppercase dark:text-gray-400">{{ trans('common.menu') }}</h5>
                <button type="button" data-drawer-hide="drawer-navigation" aria-controls="drawer-navigation" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-full text-sm p-1.5 absolute top-2.5 right-2.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <x-ui.icon icon="close" class="w-5 h-5" />
                    <span class="sr-only">{{ trans('common.close') }}</span>
                </button>
                <div class="py-4 overflow-y-auto">

                    <ul class="space-y-2 site-drawer-nav">
                        <li @if ($routeName == 'partner.index') class="active" @endif>
                            <a href="{{ route('partner.index') }}"><x-ui.icon icon="home" class="w-6 h-6" /><span>{{ trans('common.dashboard') }}</span></a>
                        </li>
                        <li><hr class="h-px mt-5 bg-gray-200 border-0 dark:bg-gray-700"></li>
                        <li><h5 class="inline-flex items-center ml-2 my-4 text-sm font-medium text-gray-400 dark:text-gray-400">{{ trans('common.campaigns') }}</h5></li>
                        <li @if ($routeDataDefinition == 'clubs') class="active" @endif>
                            <a href="{{ route('partner.data.list', ['name' => 'clubs']) }}"><x-ui.icon icon="funnel" class="w-6 h-6" /><span>{{ trans('common.clubs') }}</span></a>
                        </li>
                        <li @if ($routeDataDefinition == 'cards') class="active" @endif>
                           <a href="{{ route('partner.data.list', ['name' => 'cards']) }}"><x-ui.icon icon="qr-code" class="w-6 h-6" /><span>{{ trans('common.loyalty_cards') }}</span></a>
                        </li>
                        <li @if ($routeDataDefinition == 'rewards') class="active" @endif>
                            <a href="{{ route('partner.data.list', ['name' => 'rewards']) }}"><x-ui.icon icon="gift" class="w-6 h-6" /><span>{{ trans('common.rewards') }}</span></a>
                        </li>
                        <li><hr class="h-px mt-5 bg-gray-200 border-0 dark:bg-gray-700"></li>
                        <li><h5 class="inline-flex items-center ml-2 my-4 text-sm font-medium text-gray-400 dark:text-gray-400">{{ trans('common.users') }}</h5></li>
                        <li @if ($routeDataDefinition == 'staff') class="active" @endif>
                          <a href="{{ route('partner.data.list', ['name' => 'staff']) }}"><x-ui.icon icon="briefcase" class="w-6 h-6" /><span>{{ trans('common.staff') }}</span></a>
                        </li>
                        <li @if ($routeDataDefinition == 'members') class="active" @endif>
                          <a href="{{ route('partner.data.list', ['name' => 'members']) }}"><x-ui.icon icon="user-group" class="w-6 h-6" /><span>{{ trans('common.members') }}</span></a>
                        </li>
                        <li><hr class="h-px my-5 bg-gray-200 border-0 dark:bg-gray-700"></li>
                        <li @if (Str::startsWith($routeName, 'partner.analytics')) class="active" @endif>
                            <a href="{{ route('partner.analytics') }}"><x-ui.icon icon="presentation-chart-line" class="w-6 h-6" /><span>{{ trans('common.analytics') }}</span></a>
                        </li>
                        <li><hr class="h-px mt-5 bg-gray-200 border-0 dark:bg-gray-700"></li>
                        <li><h5 class="inline-flex items-center ml-2 my-4 text-sm font-medium text-gray-400 dark:text-gray-400">{{ auth('partner')->user()->name }}</h5></li>
                        <li @if ($routeDataDefinition == 'account') class="active" @endif>
                            <a href="{{ route('partner.data.list', ['name' => 'account']) }}"><x-ui.icon icon="user-circle" class="w-6 h-6" /><span>{{ trans('common.account_settings') }}</span></a>
                        </li>
                        <li><a href="{{ route('partner.logout') }}"><x-ui.icon icon="power" class="w-6 h-6" /><span>{{ trans('common.logout') }}</span></a></li>
                    </ul>

                </div>
            </div>
        @endauth

        <div class="w-full mx-auto flex flex-grow max-w-screen-2xl">
            @yield('content')
        </div>
    </div>

    <x-ui.toast />
    <x-ui.lightbox />
    @include('includes.demo')

</body>
</html>