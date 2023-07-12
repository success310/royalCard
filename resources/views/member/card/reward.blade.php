@extends('member.layouts.default')

@section('page_title', $reward->title . config('default.page_title_delimiter') . $card->head . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')

    <div class="flex flex-col w-full p-6">
        <div class="grid- space-y-6 h-full w-full place-items-center">
            <div class="max-w-md mx-auto">
                <x-ui.breadcrumb :crumbs="[
                    ['url' => route('member.index'), 'icon' => 'home', 'title' => trans('common.home')],
                    ['url' => route('member.card', ['card_id' => $card->id]), 'text' => $card->head],
                    ['text' => $reward->title]
                ]" />
            </div>

            <div class="max-w-md mx-auto">
                <div class="w-full mt-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                    @if ($reward->images)
                        @if (count($reward->images) == 1)
                            <img src="{{ $reward->images[0]['md'] }}" class="rounded-t-lg w-full" style="width: 100%; aspect-ratio: {{ $reward->images[0]['ratio'] }};" alt="{{ parse_attr($reward->title) }}">
                        @else
                        <div class="relative w-full" data-carousel="slide">
                            <!-- Carousel wrapper -->
                            <div class="relative overflow-hidden rounded-t-lg -h-56 md:-h-96" style="width: 100%; aspect-ratio: {{ $reward->images[0]['ratio'] }};">
                                @foreach ($reward->images as $image)
                                <div class="hidden duration-700 ease-in-out" @if($loop->first) data-carousel-item="active" @else data-carousel-item @endif>
                                    <img src="{{ $image['md'] }}" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="{{ parse_attr($reward->title) }}">
                                </div>
                                @endforeach
                            </div>
                            <!-- Slider indicators -->
                            <div class="absolute z-30 flex space-x-3 -translate-x-1/2 bottom-5 left-1/2">
                                @foreach ($reward->images as $image)
                                    <button type="button" class="w-3 h-3 rounded-full" @if($loop->first) aria-current="true" @else aria-current="false" @endif aria-label="{{ parse_attr($reward->title) }}" data-carousel-slide-to="{{ $loop->index }}"></button>
                                @endforeach
                            </div>
                            <!-- Slider controls -->
                            <button type="button" class="absolute top-0 left-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full sm:w-10 sm:h-10 bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-white group-focus:outline-none">
                                    <svg aria-hidden="true" class="w-5 h-5 text-white sm:w-6 sm:h-6 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    <span class="sr-only">{{ trans('common.previous') }}</span>
                                </span>
                            </button>
                            <button type="button" class="absolute top-0 right-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full sm:w-10 sm:h-10 bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-white group-focus:outline-none">
                                    <svg aria-hidden="true" class="w-5 h-5 text-white sm:w-6 sm:h-6 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    <span class="sr-only">{{ trans('common.next') }}</span>
                                </span>
                            </button>
                        </div>
                        @endif
                    @endif
                    <div class="px-5 pb-5 mt-5">
                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $reward->title }}</h5>
                        @if($reward->description != '')
                        <p class="mb-5 font-normal text-gray-700 dark:text-gray-400">{!! $reward->description !!}</p>
                        @endif
                        @if(auth('member')->check())
                            <p class="text-sm font-normal text-gray-400 dark:text-gray-500 mb-2">
                                {!! trans('common.you_have_amount_points', ['points' => '<span class=\'format-number\'>' . $balance . '</span>']) !!}
                            </p>
                        @endif
                        <div class="flex items-center justify-between">
                            <div class="flex items-center justify-end mr-3">
                                <x-ui.icon icon="coins" class="text-gray-900 dark:text-white w-7 h-7 mr-2" />
                                <span class="text-3xl font-bold text-gray-900 dark:text-white format-number">{{ $reward->points }}</span>
                            </div>
                            @if(auth('member')->check())
                                <a rel="nofollow" href="{{ route('member.card.reward.claim', ['card_id' => $card->id, 'reward_id' => $reward->id]) }}" class="btn-primary flex items-center justify-between"><x-ui.icon icon="arrow-right" class="w-5 h-5 mr-2" /> {{ trans('common.claim_reward') }}</a>
                            @else
                            <a rel="nofollow" href="{{ route('member.login') }}" class="flex items-center text-link">
                                {{ trans('common.log_in_to_claim_reward') }}
                                <x-ui.icon icon="arrow-right" class="w-5 h-5 ml-2"/>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                <x-ui.share class="mt-6" :text="$card->head . ' / ' . $reward->title" />

                <x-member.follow-card class="mt-6" :card="$card" />

                <x-member.rewards :card="$card" :current-reward="$reward" :show-claimable="true" />

            </div>
        </div>
    </div>
@stop



