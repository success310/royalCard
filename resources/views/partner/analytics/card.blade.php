@extends('partner.layouts.default')

@section('page_title', $card->name . config('default.page_title_delimiter') . trans('common.analytics') . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')
<div class="flex flex-col w-full p-6">
    <div class="space-y-6 w-full">
        <div class="mx-auto w-full">
        
            <div class="flex items-center">
                <a href="{{ route('partner.analytics') }}"
                    class="flex text-sm rounded-full mr-3 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <x-ui.icon icon="left" class="m-1 w-7 h-7 text-gray-900 dark:text-gray-300" />
                </a>
                <div>
                    <select id="range" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        <option value="day" @if($range == 'day') selected @endif>{{ trans('common.show_analytics_from_today') }}</option>
                        <option value="day,-1" @if($range == 'day,-1') selected @endif>{{ trans('common.show_analytics_from_yesterday') }}</option>
                        <option value="week" @if($range == 'week') selected @endif>{{ trans('common.show_analytics_from_this_week') }}</option>
                        <option value="week,-1" @if($range == 'week,-1') selected @endif>{{ trans('common.show_analytics_from_last_week') }}</option>
                        <option value="month" @if($range == 'month') selected @endif>{{ trans('common.show_analytics_from_this_month') }}</option>
                        <option value="month,-1" @if($range == 'month,-1') selected @endif>{{ trans('common.show_analytics_from_last_month') }}</option>
                        <option value="year" @if($range == 'year') selected @endif>{{ trans('common.show_analytics_from_this_year') }}</option>
                        <option value="year,-1" @if($range == 'year,-1') selected @endif>{{ trans('common.show_analytics_from_last_year') }}</option>
                    </select>
                    <script>
                        document.addEventListener('DOMContentLoaded', (event) => {
                            // Get the select element
                            const rangeSelect = document.querySelector('#range');
                        
                            // Add event listener for the 'change' event
                            rangeSelect.addEventListener('change', reloadWithQueryString);
                        
                            function reloadWithQueryString() {
                                // Get the selected value from the select element
                                const rangeValue = rangeSelect.value;
                        
                                // Reload the page with the new query string parameter
                                window.location.href = window.location.pathname + '?range=' + encodeURIComponent(rangeValue);
                            }
                        });
                    </script>
                </div>
                <div class="ml-4">
                    @if(!$resultsFound)
                    <h5 class="text-xl font-bold dark:text-white w-full">{!! trans('common.no_results_found') !!}</h5>
                    @else
                    <h5 class="text-xl font-bold dark:text-white w-full">{!! $cardViews['label'] !!}</h5>
                    @endif
                </div>
            </div>

            <div class="py-8 sm:py-6">
                <dl class="grid gap-1 overflow-hidden rounded-2xl text-center grid-cols-2 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="flex flex-col bg-gray-400/5 dark:bg-gray-800 p-8">
                        <dt class="text-sm font-semibold leading-6 text-gray-600 dark:text-gray-400">{{ trans('common.card_views') }} (<span class="format-number">{{ $cardViews['total'] }}</span>)</dt>
                        <dd class="flex justify-center items-center order-first text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                            @if ($cardViewsDifference == 0) 
                            {{ $cardViewsDifference }}%
                            @elseif ($cardViewsDifference > 0)
                            <x-ui.icon icon="arrow-trending-up" class="w-6 h-6 mr-2" /> +{{ $cardViewsDifference }}%
                            @elseif ($cardViewsDifference < 0 && $cardViewsDifference != '-')
                            <x-ui.icon icon="arrow-trending-down" class="w-6 h-6 mr-2" /> {{ $cardViewsDifference }}%
                            @else
                            {{ $cardViewsDifference }}
                            @endif
                        </dd>
                    </div>
                    <div class="flex flex-col bg-gray-400/5 dark:bg-gray-800 p-8">
                        <dt class="text-sm font-semibold leading-6 text-gray-600 dark:text-gray-400">{{ trans('common.reward_views') }} (<span class="format-number">{{ $rewardViews['total'] }}</span>)</dt>
                        <dd class="flex justify-center items-center order-first text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                            @if ($rewardViewsDifference == 0) 
                            {{ $rewardViewsDifference }}%
                            @elseif ($rewardViewsDifference > 0)
                            <x-ui.icon icon="arrow-trending-up" class="w-6 h-6 mr-2" /> +{{ $rewardViewsDifference }}%
                            @elseif ($rewardViewsDifference < 0 && $rewardViewsDifference != '-')
                            <x-ui.icon icon="arrow-trending-down" class="w-6 h-6 mr-2" /> {{ $rewardViewsDifference }}%
                            @else
                            {{ $rewardViewsDifference }}
                            @endif
                        </dd>
                    </div>
                    <div class="flex flex-col bg-gray-400/5 dark:bg-gray-800 p-8">
                        <dt class="text-sm font-semibold leading-6 text-gray-600 dark:text-gray-400">{{ trans('common.points_issued') }} (<span class="format-number">{{ $pointsIssued['total'] }}</span>)</dt>
                        <dd class="flex justify-center items-center order-first text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                            @if ($pointsIssuedDifference == 0) 
                            {{ $pointsIssuedDifference }}%
                            @elseif ($pointsIssuedDifference > 0)
                            <x-ui.icon icon="arrow-trending-up" class="w-6 h-6 mr-2" /> +{{ $pointsIssuedDifference }}%
                            @elseif ($pointsIssuedDifference < 0 && $pointsIssuedDifference != '-')
                            <x-ui.icon icon="arrow-trending-down" class="w-6 h-6 mr-2" /> {{ $pointsIssuedDifference }}%
                            @else
                            {{ $pointsIssuedDifference }}
                            @endif
                        </dd>
                    </div>
                    <div class="flex flex-col bg-gray-400/5 dark:bg-gray-800 p-8">
                        <dt class="text-sm font-semibold leading-6 text-gray-600 dark:text-gray-400">{{ trans('common.rewards_claimed') }} (<span class="format-number">{{ $rewardsClaimed['total'] }}</span>)</dt>
                        <dd class="flex justify-center items-center order-first text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                            @if ($rewardsClaimedDifference == 0) 
                            {{ $rewardsClaimedDifference }}%
                            @elseif ($rewardsClaimedDifference > 0)
                            <x-ui.icon icon="arrow-trending-up" class="w-6 h-6 mr-2" /> +{{ $rewardsClaimedDifference }}%
                            @elseif ($rewardsClaimedDifference < 0 && $rewardsClaimedDifference != '-')
                            <x-ui.icon icon="arrow-trending-down" class="w-6 h-6 mr-2" /> {{ $rewardsClaimedDifference }}%
                            @else
                            {{ $rewardsClaimedDifference }}
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="space-y-8 md:grid md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 md:gap-8 xl:gap-8 md:space-y-0">

                <div class="flex justify-center items-center">
                    <x-member.card
                        class="max-w-md"
                        :card="$card"
                        :flippable="false"
                        :links="false"
                        :show-qr="false"
                        :auth-check="false"
                        :show-balance="false"
                    />
                </div>

                @if($resultsFound)
                <div class="p-4 border border-gray-200 rounded-lg dark:border-gray-700">
                    <?php /*
                    <dl class="grid gap-1 overflow-hidden rounded-2xl text-center grid-cols-2">
                        <div class="flex flex-col bg-gray-400/5 p-8">
                            <dt class="text-sm font-semibold leading-6 text-gray-600 dark:text-gray-400">{{ trans('common.card_views') }} (<span class="format-number">{{ $cardViews['total'] }}</span>)</dt>
                            <dd class="flex justify-center items-center order-first text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                                @if ($cardViewsDifference == 0) 
                                {{ $cardViewsDifference }}%
                                @elseif ($cardViewsDifference > 0)
                                <x-ui.icon icon="arrow-trending-up" class="w-6 h-6 mr-2" /> +{{ $cardViewsDifference }}%
                                @elseif ($cardViewsDifference < 0)
                                <x-ui.icon icon="arrow-trending-down" class="w-6 h-6 mr-2" /> {{ $cardViewsDifference }}%
                                @else
                                {{ $cardViewsDifference }}
                                @endif
                            </dd>
                        </div>
                        <div class="flex flex-col bg-gray-400/5 p-8">
                            <dt class="text-sm font-semibold leading-6 text-gray-600 dark:text-gray-400">{{ trans('common.reward_views') }} (<span class="format-number">{{ $rewardViews['total'] }}</span>)</dt>
                            <dd class="flex justify-center items-center order-first text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                                @if ($rewardViewsDifference == 0) 
                                {{ $rewardViewsDifference }}%
                                @elseif ($rewardViewsDifference > 0)
                                <x-ui.icon icon="arrow-trending-up" class="w-6 h-6 mr-2" /> +{{ $rewardViewsDifference }}%
                                @elseif ($rewardViewsDifference < 0)
                                <x-ui.icon icon="arrow-trending-down" class="w-6 h-6 mr-2" /> {{ $rewardViewsDifference }}%
                                @else
                                {{ $rewardViewsDifference }}
                                @endif
                            </dd>
                        </div>
                    </dl>*/ ?>
                    <canvas id="analytics-views-chart" 
                        data-labels='{!! '["' . implode('","', $cardViews['units']) . '"]' !!}'
                        data-label1="{{ trans('common.card_views') }}"
                        data-tooltip1="{{ trans('common.chart_tooltip_card_views') }}"
                        data-values1="{{ '[' . implode(',', $cardViews['views']) . ']' }}"
                        data-label2="{{ trans('common.reward_views') }}"
                        data-tooltip2="{{ trans('common.chart_tooltip_reward_views') }}"
                        data-values2="{{ '[' . implode(',', $rewardViews['views']) . ']' }}"
                    >
                    </canvas>
                </div>

                <div class="p-4 border border-gray-200 rounded-lg dark:border-gray-700">
                    <?php /*
                    <dl class="grid gap-1 overflow-hidden rounded-2xl text-center grid-cols-2">
                        <div class="flex flex-col bg-gray-400/5 p-8">
                            <dt class="text-sm font-semibold leading-6 text-gray-600 dark:text-gray-400">{{ trans('common.points_issued') }} (<span class="format-number">{{ $pointsIssued['total'] }}</span>)</dt>
                            <dd class="flex justify-center items-center order-first text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                                @if ($pointsIssuedDifference == 0) 
                                {{ $pointsIssuedDifference }}%
                                @elseif ($pointsIssuedDifference > 0)
                                <x-ui.icon icon="arrow-trending-up" class="w-6 h-6 mr-2" /> +{{ $pointsIssuedDifference }}%
                                @elseif ($pointsIssuedDifference < 0 && $pointsIssuedDifference != '-')
                                <x-ui.icon icon="arrow-trending-down" class="w-6 h-6 mr-2" /> {{ $pointsIssuedDifference }}%
                                @else
                                {{ $pointsIssuedDifference }}
                                @endif
                            </dd>
                        </div>
                    </dl>*/ ?>
                    <canvas
                        data-te-chart="bar"
                        data-te-dataset-label="{{ trans('common.points_issued') }}"
                        data-te-labels="{!! '[\'' . implode('\',\'', $pointsIssued['units']) . '\']' !!}"
                        data-te-dataset-data="{{ '[' . implode(',', $pointsIssued['points']) . ']' }}"
                        data-te-dataset-background-color="['rgba(255, 99, 132, 0.2)']" 
                        data-te-dataset-border-color="['rgba(255, 99, 132, 1)']" 
                        data-te-dataset-border-width="1" 
                    >
                    </canvas>
                </div>

                <div class="p-4 border border-gray-200 rounded-lg dark:border-gray-700">
                    <?php /*<dl class="grid gap-1 overflow-hidden rounded-2xl text-center grid-cols-2">
                        <div class="flex flex-col bg-gray-400/5 p-8">
                            <dt class="text-sm font-semibold leading-6 text-gray-600 dark:text-gray-400">{{ trans('common.rewards_claimed') }} (<span class="format-number">{{ $rewardsClaimed['total'] }}</span>)</dt>
                            <dd class="flex justify-center items-center order-first text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                                @if ($rewardsClaimedDifference == 0) 
                                {{ $rewardsClaimedDifference }}%
                                @elseif ($rewardsClaimedDifference > 0)
                                <x-ui.icon icon="arrow-trending-up" class="w-6 h-6 mr-2" /> +{{ $rewardsClaimedDifference }}%
                                @elseif ($rewardsClaimedDifference < 0)
                                <x-ui.icon icon="arrow-trending-down" class="w-6 h-6 mr-2" /> {{ $rewardsClaimedDifference }}%
                                @else
                                {{ $rewardsClaimedDifference }}
                                @endif
                            </dd>
                        </div>
                    </dl>*/ ?>
                    <canvas
                        data-te-chart="bar"
                        data-te-dataset-label="{{ trans('common.rewards_claimed') }}"
                        data-te-labels="{!! '[\'' . implode('\',\'', $rewardsClaimed['units']) . '\']' !!}"
                        data-te-dataset-data="{{ '[' . implode(',', $rewardsClaimed['rewards']) . ']' }}"
                        data-te-dataset-background-color="['rgba(153, 102, 255, 0.2)']" 
                        data-te-dataset-border-color="['rgba(153, 102, 255, 1)']" 
                        data-te-dataset-border-width="1" 
                    >
                    </canvas>
                </div>
                
                @endif
            </div>
        <?php /*

                <div class="max-w-md w-full mx-auto">
                    <div class="p-4 border border-gray-200 rounded dark:border-gray-700">
                        <canvas id="analytics-card-chart" 
                            data-labels='{!! '["' . implode('","', $cardViews['units']) . '"]' !!}'
                            data-label1="{{ trans('common.visits') }} ({{ $cardViews['total'] }})"
                            data-tooltip1="{{ trans('common.chart_tooltip_visits') }}"
                            data-values1="{{ '[' . implode(',', $cardViews['views']) . ']' }}"
                            data-label2="{{ trans('common.points_issuances') }} ({{ $pointsIssued['total'] }})"
                            data-tooltip2="{{ trans('common.chart_tooltip_points') }}"
                            data-values2="{{ '[' . implode(',', $pointsIssued['points']) . ']' }}"
                            data-label3="{{ trans('common.rewards') }} ({{ $rewardsClaimed['total'] }})"
                            data-tooltip3="{{ trans('common.chart_tooltip_rewards') }}"
                            data-values3="{{ '[' . implode(',', $rewardsClaimed['rewards']) . ']' }}"
                        >
                        </canvas>
                    </div>
                </div>

        <div class="space-y-8 md:grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 md:gap-8 xl:gap-8 md:space-y-0">
            <div>
                <h6 class="text-lg font-bold dark:text-white">{{ trans('common.visits') }} (<span class="format-number">{{ $cardViews['total'] }}</span>)</h6>
                <canvas
                    data-te-chart="bar"
                    data-te-dataset-label="{{ trans('common.visits') }}"
                    data-te-labels="{!! '[\'' . implode('\',\'', $cardViews['units']) . '\']' !!}"
                    data-te-dataset-data="{{ '[' . implode(',', $cardViews['views']) . ']' }}"
                    data-te-dataset-background-color="['rgba(255, 99, 132, 0.2)']" 
                    data-te-dataset-border-color="['rgba(255,99,132,1)']" 
                    data-te-dataset-border-width="1" 
                >
                </canvas>
            </div>

            <div>
                <h6 class="text-lg font-bold dark:text-white">{{ trans('common.points_issued') }} (<span class="format-number">{{ $pointsIssued['total'] }}</span>)</h6>
                <canvas
                    data-te-chart="bar"
                    data-te-dataset-label="{{ trans('common.points') }}"
                    data-te-labels="{!! '[\'' . implode('\',\'', $pointsIssued['units']) . '\']' !!}"
                    data-te-dataset-data="{{ '[' . implode(',', $pointsIssued['points']) . ']' }}"
                    data-te-dataset-background-color="['rgba(54, 162, 235, 0.2)']" 
                    data-te-dataset-border-color="['rgba(54, 162, 235,1)']" 
                    data-te-dataset-border-width="1" 
                >
                </canvas>
            </div>

            <div>
                <h6 class="text-lg font-bold dark:text-white">{{ trans('common.rewards_claimed') }} (<span class="format-number">{{ $rewardsClaimed['total'] }}</span>)</h6>
                <canvas
                    data-te-chart="bar"
                    data-te-dataset-label="{{ trans('common.rewards') }}"
                    data-te-labels="{!! '[\'' . implode('\',\'', $rewardsClaimed['units']) . '\']' !!}"
                    data-te-dataset-data="{{ '[' . implode(',', $rewardsClaimed['rewards']) . ']' }}"
                    data-te-dataset-background-color="['rgba(255, 206, 86, 0.2)']" 
                    data-te-dataset-border-color="['rgba(255, 206, 86,1)']" 
                    data-te-dataset-border-width="1" 
                >
                </canvas>
            </div>
        </div>
    </div>
        */ ?>
        </div>
    </div>
</div>
@stop
