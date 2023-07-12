@extends('installation.layouts.default')

@section('page_title', trans('install.install') . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')

    <section class="py-8 bg-white dark:bg-gray-900 lg:py-0 h-full" x-data="{ installing: false }">
        <div class="lg:flex h-full">

            @include('installation.includes.sidebar')

            <button id="theme-toggle" type="button"
                class="absolute right-2 top-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-full text-sm p-2.5">
                <x-ui.icon icon="moon" class="hidden w-4 h-4" id="theme-toggle-dark-icon" />
                <x-ui.icon icon="sun" class="hidden w-4 h-4" id="theme-toggle-light-icon" />
            </button>

            <div class="flex mx-auto md:w-[42rem] py-8 px-4 md:px-8 xl:px-0">
                <div class="w-full" x-data="{ tab: 1 }" id="tabs"
                    @@show-tab="tab = $event.detail.tab">
                    <form id="form1" method="POST">
                        <div class="flex items-center justify-center mb-4 space-x-4 lg:hidden">
                            <a href="{{ route('redir.locale') }}" class="flex items-center">
                                <img src="{{ asset('assets/img/logo-light.svg') }}" class="h-8 mr-2 block dark:hidden"
                                    alt="{{ config('default.app_name') }} Logo" />
                                <img src="{{ asset('assets/img/logo-dark.svg') }}" class="h-8 mr-2 hidden dark:block"
                                    alt="{{ config('default.app_name') }} Logo" />
                            </a>
                        </div>
                        <ol
                            class="flex items-center mb-6 text-sm font-medium text-center text-gray-500 dark:text-gray-400 lg:mb-12 sm:text-base">
                            <li :class="(tab == 1) ? 'text-primary-600 dark:text-primary-500' : ''"
                                class="flex items-center sm:after:content-[''] after:w-12 after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10 dark:after:border-gray-700">
                                <div
                                    class="flex items-center sm:block after:content-['/'] sm:after:hidden after:mx-2 after:font-light after:text-gray-200 dark:after:text-gray-500">
                                    @if ($requirements['allMet'])
                                        <svg class="w-4 h-4 mr-2 sm:mb-2 sm:w-6 sm:h-6 sm:mx-auto" fill="currentColor"
                                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-4 h-4 mr-2 sm:mb-2 sm:w-6 sm:h-6 sm:mx-auto" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                    {{ trans('install.requirements') }}
                                </div>
                            </li>
                            <li :class="(tab == 2) ? 'text-primary-600 dark:text-primary-500' : ''"
                                class="flex items-center after:content-[''] after:w-12 after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10 dark:after:border-gray-700">
                                <div
                                    class="flex items-center sm:block after:content-['/'] sm:after:hidden after:mx-2 after:font-light after:text-gray-200 dark:after:text-gray-500">
                                    <div class="mr-2 sm:mb-2 sm:mx-auto">2</div>
                                    {{ trans('install.configuration') }}
                                </div>
                            </li>
                            <li :class="(tab == 3) ? 'text-primary-600 dark:text-primary-500' : ''"
                                class="flex items-center sm:block">
                                <div class="mr-2 sm:mb-2 sm:mx-auto">3</div>
                                {{ trans('install.install') }}
                            </li>
                        </ol>

                        <div id="tab-1" x-show="tab == 1">
                            <h1 class="mb-4 text-2xl font-extrabold tracking-tight text-gray-900 sm:mb-6 leding-tight dark:text-white">{{ trans('install.server_requirements') }}</h1>
                            <p class="mb-4 font-light">{{ trans('install.server_requirements_text') }}</p>

                            @if (!$requirements['allMet'])
                                <div class="alert-danger flex" role="alert">
                                    <x-ui.icon icon="exclamation-triangle" class="flex-shrink-0 inline w-5 h-5 mr-3" />
                                    <div>
                                        {{ trans('install.resolve_missing_requirements') }}
                                    </div>
                                </div>
                            @endif

                            <div class="grid my-4">
                                <div class="space-y-8">
                                        <ul role="list" class="my-6 lg:mb-0 gap-2 grid grid-cols-2 md:grid-cols-3">
                                            @foreach ($requirements['requirements'] as $requirement => $met)
                                                <li class="flex space-x-2.5 text-sm">
                                                    @if ($met)
                                                        <svg class="flex-shrink-0 w-4 h-4 text-green-600 dark:text-green-400"
                                                            fill="currentColor" viewBox="0 0 20 20"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="flex-shrink-0 w-4 h-4 text-red-600 dark:text-red-500"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                    <span class="leading-tight text-gray-500 dark:text-gray-400">{{ $requirement }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                </div>
                            </div>

                            <div class="flex space-x-3 mt-10">
                                <button type="button"
                                    @if (!$requirements['allMet']) disabled @else x-on:click="tab = 2" @endif
                                    class="btn-primary">{{ trans('install.next') }}: {{ trans('install.configuration') }}</button>
                            </div>
                        </div>
                        <div id="tab-2" x-cloak x-show="tab == 2">
                            <h1
                                class="mb-4 text-2xl font-extrabold tracking-tight text-gray-900 sm:mb-6 leding-tight dark:text-white">
                                {{ trans('install.app') }}</h1>
                            <div class="grid gap-5 my-6 sm:grid-cols-2">
                                <div>
                                    <label for="APP_NAME"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ trans('install.name') }} <sup
                                            class="form-required">*</sup></label>
                                    <input type="text" name="APP_NAME" id="APP_NAME" class="input-text"
                                        placeholder="{{ config('default.app_name') }}"
                                        value="{{ old('APP_NAME', env('APP_NAME', config('default.app_name'))) }}" required="">
                                </div>
                            </div>
                            <div class="grid gap-5 my-6 sm:grid-cols-2">
                                <div>
                                    <label for="APP_LOGO"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ trans('install.logo') }} {{ trans('install._optional_') }}</label>
                                    <input type="text" name="APP_LOGO" id="APP_LOGO" class="input-text"
                                        placeholder="https://example.com/logo.svg"
                                        value="{{ old('APP_LOGO', env('APP_LOGO')) }}">
                                </div>
                                <div>
                                    <label for="APP_LOGO_DARK"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ trans('install.logo_dark') }} {{ trans('install._optional_') }}</label>
                                    <input type="text" name="APP_LOGO_DARK" id="APP_LOGO_DARK" class="input-text"
                                        placeholder="https://example.com/logo-dark.svg"
                                        value="{{ old('APP_LOGO_DARK', env('APP_LOGO_DARK')) }}">
                                </div>
                            </div>
                            <h1
                                class="mb-4 mt-10 text-2xl font-extrabold tracking-tight text-gray-900 sm:mb-6 leding-tight dark:text-white">
                                {{ trans('install.admin_login') }}</h1>
                            <div class="grid gap-5 my-6 sm:grid-cols-2">
                                <div>
                                    <label for="ADMIN_MAIL"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ trans('install.email_address') }}
                                        <sup class="form-required">*</sup></label>
                                    <input type="email" name="ADMIN_MAIL" id="ADMIN_MAIL" class="input-text"
                                        placeholder="info@example.com"
                                        value="{{ old('ADMIN_MAIL', env('MAIL_FROM_ADDRESS')) }}"
                                        required="">
                                </div>
                                <div x-data="{ ADMIN_TIMEZONE: window.timezone }">
                                    <label for="ADMIN_TIMEZONE"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ trans('install.time_zone') }} <sup
                                            class="form-required">*</sup></label>
                                    <select name="ADMIN_TIMEZONE" id="ADMIN_TIMEZONE" class="input-select"
                                        x-model="ADMIN_TIMEZONE">
                                        @foreach ($timezones as $value => $timezone)
                                            <option value="{{ $value }}">{{ $timezone }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="ADMIN_PASS"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ trans('install.password') }} <sup
                                            class="form-required">*</sup></label>
                                    <input type="password" name="ADMIN_PASS" minlength="8" id="ADMIN_PASS"
                                        value="{{ old('ADMIN_PASS', '') }}" class="input-text" required="">
                                </div>
                                <div>
                                    <label for="ADMIN_PASS_CONFIRM"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ trans('install.confirm_password') }} <sup class="form-required">*</sup></label>
                                    <input type="password" minlength="8" name="ADMIN_PASS_CONFIRM"
                                        onkeydown="this.setCustomValidity('')" id="ADMIN_PASS_CONFIRM"
                                        value="{{ old('ADMIN_PASS_CONFIRM', '') }}" class="input-text" required="">
                                </div>
                            </div>
                            <h1 class="mb-4 mt-10 text-2xl font-extrabold tracking-tight text-gray-900 sm:mb-6 leding-tight dark:text-white">{{ trans('install.email') }}</h1>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="MAIL_FROM_ADDRESS"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ trans('install.email_address_app') }}
                                        <sup class="form-required">*</sup></label>
                                    <input type="email" name="MAIL_FROM_ADDRESS" id="MAIL_FROM_ADDRESS"
                                        class="input-text" placeholder="noreply@example.com"
                                        value="{{ old('MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS')) }}" required>
                                </div>
                                <div>
                                    <label for="MAIL_FROM_NAME"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ trans('install.email_address_name_app') }} 
                                        <sup class="form-required">*</sup></label>
                                    <input type="text" name="MAIL_FROM_NAME" id="MAIL_FROM_NAME" class="input-text"
                                        placeholder="{{ env('MAIL_FROM_NAME') }}"
                                        value="{{ old('MAIL_FROM_NAME', env('MAIL_FROM_NAME', config('default.app_name'))) }}" required>
                                </div>
                            </div>

                            <div class="grid gap-5 my-6 sm:grid-cols-1">
                                <div x-data="{ MAIL_MAILER: '{{ old('MAIL_MAILER', env('MAIL_MAILER'), 'smtp') }}' }">
                                    <label for="MAIL_MAILER"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mailer <sup
                                            class="form-required">*</sup></label>
                                    <select id="MAIL_MAILER" name="MAIL_MAILER" class="input-select"
                                        x-model="MAIL_MAILER">
                                        <option value="smtp">SMTP</option>
                                        <option value="mail">mail</option>
                                        <option value="sendmail">sendmail</option>
                                    </select>
                                </div>
                                <p>{{ trans('install.optional_email_config') }}</p>
                                <div class="flex gap-5">
                                    <div class="flex-1 w-auto">
                                        <label for="MAIL_HOST"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Host</label>
                                        <input type="text" name="MAIL_HOST" id="MAIL_HOST" class="input-text"
                                            placeholder="{{ env('MAIL_HOST') }}"
                                            value="{{ old('MAIL_HOST', env('MAIL_HOST')) }}">
                                    </div>
                                    <div class="w-24">
                                        <label for="MAIL_PORT"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Port</label>
                                        <input type="text" name="MAIL_PORT" id="MAIL_PORT" class="input-text"
                                            placeholder="{{ env('MAIL_PORT') }}"
                                            value="{{ old('MAIL_PORT', env('MAIL_PORT')) }}">
                                    </div>
                                    <div class="w-24" x-data="{ MAIL_ENCRYPTION: '{{ old('MAIL_ENCRYPTION', env('MAIL_ENCRYPTION'), '') }}' }">
                                        <label for="MAIL_ENCRYPTION"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Encryption</label>
                                        <select id="MAIL_ENCRYPTION" name="MAIL_ENCRYPTION" class="input-select"
                                            x-model="MAIL_ENCRYPTION">
                                            <option value=""></option>
                                            <option value="ssl">SSL</option>
                                            <option value="tls">TLS</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div>
                                        <label for="MAIL_USERNAME"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ trans('install.user') }}</label>
                                        <input type="text" name="MAIL_USERNAME" id="MAIL_USERNAME" class="input-text"
                                            placeholder="{{ env('MAIL_USERNAME') }}"
                                            value="{{ old('MAIL_USERNAME', env('MAIL_USERNAME')) }}">
                                    </div>
                                    <div>
                                        <label for="MAIL_PASSWORD"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ trans('install.password') }}</label>
                                        <input type="text" name="MAIL_PASSWORD" id="MAIL_PASSWORD" class="input-text"
                                            value="{{ old('MAIL_PASSWORD', env('MAIL_PASSWORD')) }}">
                                    </div>
                                </div>
                            </div>
                            <h1
                                class="mb-4 mt-10 text-2xl font-extrabold tracking-tight text-gray-900 sm:mb-6 leding-tight dark:text-white">
                                Database</h1>
                            <div class="grid gap-5 my-6 sm:grid-cols-1" x-data="{ db: 'sqlite' }">
                                <div>
                                    <select id="DB_CONNECTION" name="DB_CONNECTION" class="input-select" x-model="db">
                                        <option value="sqlite">SQLite</option>
                                        <option value="mysql">MySQL / MariaDB</option>
                                    </select>
                                </div>
                                <div class="alert-info" x-show="db == '{{ env('DB_CONNECTION', 'sqlite') }}'">
                                    {{ trans('install.database_info') }}
                                </div>
                                <div class="grid gap-5 sm:grid-cols-2" x-show="db == 'mysql'">
                                    <div x-show="db == 'mysql'">
                                        <label for="DB_HOST"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Host <sup
                                                class="form-required">*</sup></label>
                                        <input type="text" name="DB_HOST" id="DB_HOST" class="input-text"
                                            placeholder="127.0.0.1"
                                            value="{{ old('DB_HOST', env('DB_HOST'), '127.0.0.1') }}"
                                            :required="db == 'mysql'">
                                    </div>
                                    <div x-show="db == 'mysql'">
                                        <label for="DB_PORT"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Port <sup
                                                class="form-required">*</sup></label>
                                        <input type="text" name="DB_PORT" id="DB_PORT" class="input-text"
                                            placeholder="3306" value="{{ old('DB_PORT', env('DB_PORT'), '3306') }}"
                                            :required="db == 'mysql'">
                                    </div>
                                </div>
                                <div x-show="db == 'mysql'">
                                    <label for="DB_DATABASE"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Database
                                        name <sup class="form-required">*</sup></label>
                                    <input type="text" name="DB_DATABASE" id="DB_DATABASE" class="input-text"
                                        placeholder="" value="{{ old('DB_DATABASE', env('DB_DATABASE')) }}"
                                        :required="db == 'mysql'">
                                </div>
                                <div class="grid gap-5 sm:grid-cols-2" x-show="db == 'mysql'">
                                    <div x-show="db == 'mysql'">
                                        <label for="DB_USERNAME"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">User <sup
                                                class="form-required">*</sup></label>
                                        <input type="text" name="DB_USERNAME" id="DB_USERNAME" class="input-text"
                                            placeholder="" value="{{ old('DB_USERNAME', env('DB_USERNAME')) }}"
                                            :required="db == 'mysql'">
                                    </div>
                                    <div x-show="db == 'mysql'">
                                        <label for="DB_PASSWORD"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                                        <input type="text" name="DB_PASSWORD" id="DB_PASSWORD" class="input-text"
                                            placeholder="" value="{{ old('DB_PASSWORD', env('DB_PASSWORD')) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="flex space-x-3 mt-10">
                                <button x-on:click="tab = 1" type="button" class="btn">{{ trans('install.prev') }}: {{ trans('install.requirements') }}</button>
                                <button x-on:click="validateForm" type="button" class="btn-primary">{{ trans('install.next') }}: {{ trans('install.confirm') }}</button>
                                <script>
                                    function validateForm() {
                                        const form1 = document.getElementById('form1');
                                        const ADMIN_PASS_CONFIRM = document.getElementById('ADMIN_PASS_CONFIRM');

                                        if (ADMIN_PASS_CONFIRM.value != document.getElementById('ADMIN_PASS').value) {
                                            ADMIN_PASS_CONFIRM.setCustomValidity("{{ trans('install.passwords_must_match') }}");
                                        } else {
                                            // input is valid -- reset the error message
                                            ADMIN_PASS_CONFIRM.setCustomValidity('');
                                        }

                                        if (form1.reportValidity()) {
                                            this.tab = 3;
                                        }
                                    }
                                </script>
                            </div>
                        </div>
                        <div id="tab-3" x-cloak x-show="tab == 3" x-data="{ response: 0 }"
                            @@show-error="installing = false; response = 500">
                            <h1 class="mb-4 text-2xl font-extrabold tracking-tight text-gray-900 sm:mb-6 leding-tight dark:text-white">{{ trans('install.install_script') }}</h1>

                            <p class="mb-4 font-bold" x-show="response == 0">{!! trans('install.after_installation', ['admin_url' => '<span class="underline">' . request()->getSchemeAndHttpHost() . '/' . request()->segment(1) . '/admin' . '</span>']) !!}</p>
                            <p class="mb-4 font-body" x-show="response == 0">{!! trans('install.install_acknowledge') !!}</p>

                            <div class="alert-danger" role="alert" x-show="response == 500">
                                <div class="flex space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        {{ trans('install.install_error') }}
                                    </div>
                                </div>
                                <div class="mt-5 flex">
                                    <a href="{{ route('installation.log') }}"
                                        class="flex justify-center btn-sm btn-secondary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        {{ trans('install.download_log') }}</a>
                                </div>
                            </div>

                            <div x-show="response != 500" class="flex space-x-3 mt-10">
                                <button x-on:click="tab = 2" type="button" class="btn disable-on-call">{{ trans('install.prev') }}: {{ trans('install.configuration') }}</button>
                                <button type="button" id="submitForm" class="btn-primary"
                                    hx-post="{{ route('installation.install') }}" hx-include="#form1"
                                    x-on:click="installing = true; submitForm()">
                                    <svg aria-hidden="true" role="status" x-show="installing"
                                        class="inline mr-3 w-4 h-4 text-white animate-spin" viewBox="0 0 100 101"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                            fill="#E5E7EB" />
                                        <path
                                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                            fill="currentColor" />
                                    </svg>
                                    {{ trans('install.install') }}</button>
                            </div>
                            <div x-show="response == 500" class="flex space-x-3 mt-10">
                                <button x-on:click="location.reload()" type="button" class="btn">{{ trans('install.refresh_page') }}</button>
                            </div>

                            <script>
                                window.submitForm = function(e) {
                                    document.getElementById('submitForm').setAttribute('disabled', '');
                                    const buttons = document.querySelectorAll('.disable-on-call');
                                    buttons.forEach((button) => {
                                        button.disabled = true;
                                    });
                                }

                                document.body.addEventListener('htmx:beforeSwap', function(evt) {
                                    evt.detail.shouldSwap = false;
                                    if (evt.detail.xhr.status === 500) {
                                        document.getElementById('tab-3').dispatchEvent(new CustomEvent('show-error'));
                                        evt.detail.isError = false;
                                    } else if (evt.detail.xhr.status === 422) {
                                        document.getElementById('tabs').dispatchEvent(new CustomEvent('show-tab', {
                                            detail: {
                                                tab: 2
                                            }
                                        }));
                                        processFormValidation(evt.detail.serverResponse);
                                        evt.detail.isError = true;
                                    } else {
                                        // All fine
                                        evt.detail.isError = false;
                                        document.location.replace('{{ route('redir.locale') }}');
                                    }
                                });
                            </script>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

@stop
