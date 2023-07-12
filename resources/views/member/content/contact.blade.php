@extends('member.layouts.default')

@section('page_title', trans('common.contact') . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')
    <section class="w-full max-w-7xl place-self-center mx-auto lg:h-full">
        <div class="p-10 format format-sm sm:format-base lg:format-lg format-blue dark:format-invert">
            {!! $content !!}
        </div>
    </section>
@stop
