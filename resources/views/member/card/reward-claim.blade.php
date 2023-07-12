@extends('member.layouts.default')

@section('page_title', trans('common.claim_reward') . config('default.page_title_delimiter') . $reward->title . config('default.page_title_delimiter') . $card->head . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')

    <div class="flex flex-col w-full p-6">
        <div class="grid- space-y-6 h-full w-full place-items-center">
            <div class="max-w-md mx-auto">
                <x-ui.breadcrumb :crumbs="[
                    ['url' => route('member.index'), 'icon' => 'home', 'title' => trans('common.home')],
                    ['url' => route('member.card', ['card_id' => $card->id]), 'text' => $card->head],
                    ['url' => route('member.card.reward', ['card_id' => $card->id, 'reward_id' => $reward->id]), 'text' => $reward->title],
                    ['text' => trans('common.claim_reward')]
                ]" />
            </div>

            @if(auth('member')->check())
                @if($reward->points < $balance)
                    <div class="max-w-md mx-auto">
                        <div id="alert-info" class="flex p-4 mb-4 text-blue-800 rounded-lg bg-blue-100 dark:bg-blue-950 dark:text-blue-400" role="alert">
                            <x-ui.icon icon="info" class="flex-shrink-0 w-5 h-5"/>
                            <div class="ml-3 text-sm font-medium">
                                <p class="mb-4">
                                    {!! trans('common.claim_reward_qr_code_text') !!}
                                </p>
                                <p class="font-normal">
                                    {!! trans('common.you_have_amount_points', ['points' => '<span class=\'format-number\'>' . $balance . '</span>']) !!}
                                    {!! trans('common.you_have_amount_after', ['points' => '<span class=\'format-number\'>' . ($balance - $reward->points) . '</span>']) !!}
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="max-w-md mx-auto">
                        <div class="flex p-4 mb-4 text-primary-800 rounded-lg bg-primary-100 dark:bg-primary-700 dark:text-white" role="alert">
                            <x-ui.icon icon="exclamation-triangle" class="flex-shrink-0 w-5 h-5"/>
                            <div class="ml-3 text-sm font-normal">
                                <p>
                                    {!! trans('common.you_have_amount_points', ['points' => '<span class=\'format-number\'>' . $balance . '</span>']) !!}
                                    @if($reward->points > $balance)
                                        {!! trans('common.you_need_amount_more', ['points' => '<span class=\'format-number\'>' . ($reward->points - $balance) . '</span>']) !!}
                                    @else
                                        {!! trans('common.you_have_amount_after', ['points' => '<span class=\'format-number\'>' . ($balance - $reward->points) . '</span>']) !!}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <div class="max-w-md mx-auto">
                <div class="flex items-center justify-center">
                    @if(auth('member')->check() && $reward->points < $balance)
                        <img
                            src="about:blank"
                            style="background-color: #fff"
                            class="w-full rounded-lg shadow" 
                            data-qr-url="{{ $claimRewardUrl }}"
                            data-qr-color-light="#ffffff"
                            data-qr-color-dark="#111827"
                            data-qr-scale="7"
                        />
                    @else
                        <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                            <x-ui.icon icon="qr-code" class="text-gray-500 dark:text-gray-400 h-40 w-40 mx-auto" />
                        </div>
                    @endif
                </div>
            </div>

            <div class="max-w-md mx-auto">
                <x-member.reward-card class="mt-6" :reward="$reward" />
            </div>
        </div>
    </div>
@stop



