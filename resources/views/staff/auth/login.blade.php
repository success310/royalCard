@extends('staff.layouts.default')

@section('page_title', trans('common.login_title') . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')
    <section class="w-full place-self-center mx-auto lg:h-full">
        <div class="grid lg:grid-cols-2 lg:h-full">
            <div class="flex justify-center items-center py-6 px-4 lg:py-6 sm:px-0">
                <div class="w-full space-y-4 md:space-y-6 max-w-lg xl:max-w-lg p-6">

                    @if(config('default.app_demo'))
                    <div class="flex p-4 mb-4 text-sm text-blue-800 border-2 border-blue-300 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400 dark:border-blue-800 cursor-pointer" role="alert" onclick="document.getElementById('email').value='staff@example.com';document.getElementById('password').value='welcome123';">
                        <x-ui.icon icon="info" class="flex-shrink-0 w-5 h-5" />
                        <div class="ml-3 text-sm font-medium">
                            To access the demo, use the following login credentials: <span class="font-extrabold">staff@example.com / welcome123</span> (click to autofill).
                        </div>
                      </div>
                    @endif

                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ trans('common.login_title') }}</h2>

                    <x-forms.messages />

                    <x-forms.form-open class="space-y-4 md:space-y-6" :action="route('staff.login.post')" method="POST" />
                    <x-forms.input
                        type="email"
                        name="email"
                        :value="$email"
                        icon="envelope"
                        :label="trans('common.email_address')"
                        :placeholder="trans('common.your_email')"
                        :required="true"
                    />
                    <x-forms.input
                        type="password"
                        name="password"
                        :value="$password"
                        icon="key"
                        :label="trans('common.password')"
                        :placeholder="trans('common.password')"
                        :required="true"
                    />
                    <div class="flex items-center justify-between">
                        <div class="flex items-start">
                            <x-forms.checkbox
                                name="remember"
                                :checked="($email && $password) ? true : false"
                                :label="trans('common.remember_me')"
                             />
                        </div>
                        <a href="{{ route('staff.forgot_password') }}" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">{{ trans('common.forgot_password') }}</a>
                    </div>
                    <x-forms.button :label="trans('common.log_in')" button-class="w-full" />
                    <x-forms.form-close />
                </div>
            </div>
            <div class="flex justify-center items-center py-6 px-4 bg-primary-600 lg:py-0 sm:px-0">
                <div class="max-w-md xl:max-w-xl p-6">
                    <h1 class="mb-4 text-3xl font-extrabold tracking-tight leading-none text-white xl:text-5xl">
                        {!! trans('common.staff_login_block_title') !!}</h1>
                    @foreach (trans('common.staff_login_block_text') as $text)
                        <p class="mb-4 font-light text-primary-200 lg:mb-8">{!! $text !!}</p>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@stop
