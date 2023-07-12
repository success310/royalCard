@extends('admin.layouts.default')

@section('page_title', trans('common.forgot_password') . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')
    <section class="bg-gray-50 dark:bg-gray-900 w-full">
        <div class="flex flex-col md:items-center justify-center mx-auto md:h-full">
            <div
                class="w-full p-6 bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md dark:bg-gray-800 dark:border-gray-700 sm:px-8 space-y-4 md:space-y-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{!! trans('common.forgot_password_title') !!}</h2>
                <x-forms.messages />

                @if (!Session::has('success'))
                    <x-forms.form-open class="space-y-4 md:space-y-6" :action="route('admin.forgot_password.post')" method="POST" />
                    <x-forms.input type="email" name="email" icon="envelope" :label="trans('common.email_address')" :placeholder="trans('common.your_email')"
                        :required="true" />

                    <x-forms.button :label="trans('common.send_reset_link')" button-class="w-full" />
                    <x-forms.form-close />
                @endif

                <div class="space-y-2 md:space-y-3 divide-y divide-gray-200 dark:divide-gray-700 pt-4">
                    <p class="text-sm font-light text-gray-500 dark:text-gray-300 pt-2">
                        {{ trans('common.login_text') }} <a href="{{ route('admin.login') }}"
                            class="font-medium text-primary-600 hover:underline dark:text-primary-500">{{ trans('common.login_link') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
@stop
