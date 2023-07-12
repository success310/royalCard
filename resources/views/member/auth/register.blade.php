@extends('member.layouts.default', ['robots' => false])

@section('page_title', trans('common.registration_title') . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')

    <section class="w-full max-w-7xl- place-self-center mx-auto lg:h-full">
        <div class="grid lg:grid-cols-2 lg:h-full">
            <div class="flex justify-center items-center py-6 px-4 lg:py-6 sm:px-0">
                <div class="w-full space-y-4 md:space-y-6 max-w-lg xl:max-w-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ trans('common.registration_title') }}</h2>
                    <x-forms.messages />
                    @if (!Session::has('success'))
                        <x-forms.form-open class="space-y-4 md:space-y-6" :action="route('member.register.post')" method="POST" />
                        <input type="hidden" name="time_zone" id="time_zone" />
                        @if (Session::has('from.member'))
                            <input type="hidden" name="from" value="{{ Session::get('from.member') }}" />
                        @endif
                        <script>
                            window.onload = function() {
                                var timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                                if (!timeZone) {
                                    timeZone = '{{ app()->make('i18n')->time_zone }}';
                                }
                                document.getElementById('time_zone').value = timeZone;
                            }
                        </script>
                        <x-forms.input
                            type="text"
                            name="name"
                            icon="user"
                            :label="trans('common.name')"
                            :placeholder="trans('common.your_name')"
                            :required="true"
                        />
                        <x-forms.input
                            type="email"
                            name="email"
                            icon="envelope"
                            :label="trans('common.email_address')"
                            :placeholder="trans('common.your_email')"
                            :required="true"
                        />
                        <x-forms.checkbox 
                            name="consent" 
                            :label="trans('common.registration_consent', [
                                'terms_of_use' => '<a rel=\'nofollow\' tabindex=\'-1\' target=\'_blank\' class=\'text-link underline\' href=\''.route('member.terms').'\'>'.trans('common.terms').'</a>',
                                'privacy_policy' => '<a rel=\'nofollow\' tabindex=\'-1\' target=\'_blank\' class=\'text-link underline\' href=\''.route('member.privacy').'\'>'.trans('common.privacy_policy').'</a>',
                            ])" 
                        />
                    
                        <x-forms.checkbox name="accepts_emails" :label="trans('common.registration_accepts_emails')" />
                        <x-forms.button :label="trans('common.create_account')" button-class="w-full" />
                        <x-forms.form-close />
                    @endif
                    <p class="text-sm font-light text-gray-500 dark:text-gray-300">
                        {{ trans('common.login_text') }} <a href="{{ route('member.login') }}"
                            class="font-medium text-primary-600 hover:underline dark:text-primary-500">{{ trans('common.login_link') }}</a>
                    </p>
                </div>
            </div>
            <div class="flex justify-center items-center py-6 px-4 bg-primary-600 lg:py-0 sm:px-0">
                <div class="max-w-md xl:max-w-xl p-6">
                    <h1 class="mb-4 text-3xl font-extrabold tracking-tight leading-none text-white xl:text-5xl">
                        {!! trans('common.register_block_title') !!}</h1>
                    @foreach (trans('common.register_block_text') as $text)
                        <p class="mb-4 font-light text-primary-200 lg:mb-8">{!! $text !!}</p>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@stop
