@extends('partner.layouts.default')

@section('page_title', trans('common.forgot_password') . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')
    <section class="bg-gray-50 dark:bg-gray-900 w-full">
        <div class="flex flex-col md:items-center justify-center mx-auto md:h-full">
            <div
                class="w-full p-6 bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md dark:bg-gray-800 dark:border-gray-700 sm:px-8 space-y-4 md:space-y-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{!! trans('common.reset_password_title') !!}</h2>
                <x-forms.messages />
                @if (!Session::has('success'))
                    <x-forms.form-open class="space-y-4 md:space-y-6" :action="$postResetLink" method="POST"/>
                        <x-forms.input
                            type="password"
                            name="password"
                            icon="key"
                            :label="trans('common.password')"
                            :placeholder="trans('common.password')"
                            :required="true"
                        />
                        <x-forms.button :label="trans('common.save_password')" button-class="w-full" />
                    <x-forms.form-close/>
                @endif
            </div>
        </div>
    </section>
@stop
