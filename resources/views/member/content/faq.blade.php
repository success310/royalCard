@extends('member.layouts.default')

@section('page_title', trans('faq.title') . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')
    <div class="w-full">
        <section class="w-full">
            <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6 ">
                <h2 class="mb-6 lg:mb-8 text-3xl lg:text-4xl tracking-tight font-extrabold text-center text-gray-900 dark:text-white">{{ trans('faq.title') }}</h2>
                <div class="mx-auto max-w-screen-md">
                    <div id="accordion-flush" data-accordion="open"
                        data-active-classes="bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                        data-inactive-classes="text-gray-500 dark:text-gray-400">
                        @foreach(trans('faq.qa') as $faq)
                        <h2 id="accordion-flush-heading-{{ $loop->index }}">
                            <button type="button"
                                class="flex justify-between items-center px-4 py-8 w-full font-medium text-left text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400"
                                data-accordion-target="#accordion-flush-body-{{ $loop->index }}" aria-expanded="false"
                                aria-controls="accordion-flush-body-{{ $loop->index }}">
                                <span>{!! $faq['q'] !!}</span>
                                <svg data-accordion-icon="" class="w-6 h-6 shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </h2>
                        <div id="accordion-flush-body-{{ $loop->index }}" class="hidden" aria-labelledby="accordion-flush-heading-{{ $loop->index }}">
                            <div class="py-5 border-b border-gray-200 dark:border-gray-700">
                                <p class="mb-2 text-gray-500 dark:text-gray-400">{!! $faq['a'] !!}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop
