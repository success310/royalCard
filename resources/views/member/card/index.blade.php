@extends('member.layouts.default')

@section('page_title', $card->head . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')

    <div class="flex flex-col w-full p-6">
        <div class="grid- space-y-6 h-full w-full place-items-center">
            <div class="max-w-md mx-auto">
                <x-ui.breadcrumb :crumbs="[['url' => route('member.index'), 'icon' => 'home', 'title' => trans('common.home')], ['text' => $card->head]]" />
            </div>

            @if(session('followed'))
                <div class="max-w-md mx-auto">
                    <div id="alert-success"
                        class="flex p-4 mb-4 text-green-800 rounded-lg bg-green-100 dark:bg-green-950 dark:text-green-400"
                        role="alert">
                        <x-ui.icon icon="info" class="flex-shrink-0 w-5 h-5"/>
                        <div class="ml-3 text-sm font-normal">
                            <p>
                                {!! trans('common.following_card_message') !!}
                            </p>
                            @if($card->initial_bonus_points > 0)
                            <p class="mt-6 font-semibold">
                                {!! trans('common.following_card_message_bonus_points', ['initial_bonus_points' => '<span class=\'format-number\'>' . $card->initial_bonus_points . '</span>']) !!}
                           </p>
                           @endif
                    </div>
                        <button type="button"
                            class="ml-auto -mx-1.5 -my-1.5 bg-green-100 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex h-8 w-8 dark:bg-green-950 dark:text-green-400 dark:hover:bg-green-700"
                            data-dismiss-target="#alert-success" aria-label="Close">
                            <span class="sr-only">{{ trans('common.close') }}</span>
                            <x-ui.icon icon="close" class="w-5 h-5"/>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('unfollowed'))
                <div class="max-w-md mx-auto">
                    <div id="alert-info"
                        class="flex p-4 mb-4 text-blue-800 rounded-lg bg-blue-100 dark:bg-blue-950 dark:text-blue-400"
                        role="alert">
                        <x-ui.icon icon="info" class="flex-shrink-0 w-5 h-5"/>
                        <div class="ml-3 text-sm font-normal">
                            <p>
                                {!! trans('common.unfollowed_card_message') !!}
                            </p>
                        </div>
                        <button type="button"
                            class="ml-auto -mx-1.5 -my-1.5 bg-blue-100 text-blue-500 rounded-lg focus:ring-2 focus:ring-blue-400 p-1.5 hover:bg-blue-200 inline-flex h-8 w-8 dark:bg-blue-950 dark:text-blue-400 dark:hover:bg-blue-700"
                            data-dismiss-target="#alert-info" aria-label="Close">
                            <span class="sr-only">{{ trans('common.close') }}</span>
                            <x-ui.icon icon="close" class="w-5 h-5"/>
                        </button>
                    </div>
                </div>
            @endif

            <x-member.card
                :card="$card"
                :flippable="true"
            />

            <div class="max-w-md mx-auto">

                <x-member.card-contact
                    :card="$card"
                />
  
                <x-ui.share class="mt-6" :text="$card->head" />

                <x-member.follow-card class="mt-6" :card="$card" />

                <x-ui.tabs :tabs="[trans('common.rewards'), trans('common.history'), trans('common.rules')]" active-tab="1" style="tabs-underline-full-width" tab-class="mt-6">
                    <x-slot name="tab1">
                        <x-member.rewards :card="$card" :show-claimable="true" />
                    </x-slot>
                    <x-slot name="tab2">
                        <x-member.history class="mt-6" :card="$card" :member="auth('member')->user() ?? null" :show-expired-and-used-transactions="false" />
                    </x-slot>
                    <x-slot name="tab3">
                        <div class="mt-6 format format-sm sm:format-base lg:format-md dark:format-invert">
                            <h3>{{ trans('common.rules_and_conditions') }}</h3>
                            <ul>
                                @if($card->initial_bonus_points > 0)
                                    <li>{!! trans('common.rules_1', ['initial_bonus_points' => '<span class=\'format-number\'>' . $card->initial_bonus_points . '</span>']) !!}</li>
                                @endif
                                <li>{{ trans('common.rules_2', ['points_expiration_months' => $card->points_expiration_months]) }}</li>
                                <li>{!! trans('common.rules_3', ['currency_unit_amount' => '<span class=\'format-number\'>' . $card->currency_unit_amount . '</span>', 'currency' => $card->currency, 'points_per_currency' => $card->points_per_currency]) !!}</li>
                                <li>{!! trans('common.rules_4', ['min_points_per_purchase' => '<span class=\'format-number\'>' . $card->min_points_per_purchase . '</span>', 'max_points_per_purchase' => '<span class=\'format-number\'>' . $card->max_points_per_purchase . '</span>']) !!}</li>
                            </ul>
                        </div>
                    </x-slot>
                </x-ui.tabs>

            </div>
        </div>
    </div>
@stop