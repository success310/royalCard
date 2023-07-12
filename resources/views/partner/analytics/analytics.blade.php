@extends('partner.layouts.default')

@section('page_title', trans('common.analytics') . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')
<div class="flex flex-col w-full p-6">
    <div class="space-y-6 w-full">
        <div class="mx-auto w-full">

            <div class="flex items-center mb-4">
                <div>
                    <select id="sort" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        <option value="views,desc" @if($sort == 'views,desc') selected @endif>{{ trans('common.sort_by_most_viewed') }}</option>
                        <option value="views,asc" @if($sort == 'views,asc') selected @endif>{{ trans('common.sort_by_least_viewed') }}</option>
                        <option value="last_view,desc" @if($sort == 'last_view,desc') selected @endif>{{ trans('common.sort_by_most_recently_viewed') }}</option>
                        <option value="last_view,asc" @if($sort == 'last_view,asc') selected @endif>{{ trans('common.sort_by_least_recently_viewed') }}</option>
                        <option value="total_amount_purchased,desc" @if($sort == 'total_amount_purchased,desc') selected @endif>{{ trans('common.sort_by_highest_revenue') }}</option>
                        <option value="total_amount_purchased,asc" @if($sort == 'total_amount_purchased,asc') selected @endif>{{ trans('common.sort_by_lowest_revenue') }}</option>
                        <option value="number_of_points_issued,desc" @if($sort == 'number_of_points_issued,desc') selected @endif>{{ trans('common.sort_by_most_points') }}</option>
                        <option value="number_of_points_issued,asc" @if($sort == 'number_of_points_issued,asc') selected @endif>{{ trans('common.sort_by_fewest_points') }}</option>
                        <option value="number_of_rewards_redeemed,desc" @if($sort == 'number_of_rewards_redeemed,desc') selected @endif>{{ trans('common.sort_by_most_rewards_claimed') }}</option>
                        <option value="number_of_rewards_redeemed,asc" @if($sort == 'number_of_rewards_redeemed,asc') selected @endif>{{ trans('common.sort_by_fewest_rewards_claimed') }}</option>
                        <option value="last_points_issued_at,desc" @if($sort == 'last_points_issued_at,desc') selected @endif>{{ trans('common.sort_by_most_recently_issued_points') }}</option>
                        <option value="last_points_issued_at,asc" @if($sort == 'last_points_issued_at,asc') selected @endif>{{ trans('common.sort_by_least_recently_issued_points') }}</option>
                        <option value="last_reward_redeemed_at,desc" @if($sort == 'last_reward_redeemed_at,desc') selected @endif>{{ trans('common.sort_by_most_recently_claimed_reward') }}</option>
                        <option value="last_reward_redeemed_at,asc" @if($sort == 'last_reward_redeemed_at,asc') selected @endif>{{ trans('common.sort_by_least_recently_claimed_reward') }}</option>
                    </select>
                </div>
                <div>
                    <div class="flex items-center pl-4">
                        <input id="active_only" type="checkbox" value="true" @if($active_only == 'true') checked @endif name="active_only" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="active_only" class="w-full py-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ trans('common.only_show_active_cards') }}</label>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', (event) => {
                    // Get the select element and checkbox
                    const sortSelect = document.querySelector('#sort');
                    const activeOnlyCheckbox = document.querySelector('#active_only');
                
                    // Add event listener for the 'change' event
                    sortSelect.addEventListener('change', reloadWithQueryString);
                    activeOnlyCheckbox.addEventListener('change', reloadWithQueryString);
                
                    function reloadWithQueryString() {
                        // Get the selected value from the select element and the checked status of the checkbox
                        const sortValue = sortSelect.value;
                        const activeOnlyValue = activeOnlyCheckbox.checked ? 'true' : 'false';
                
                        // Reload the page with the new query string parameters
                        window.location.href = window.location.pathname + '?sort=' + encodeURIComponent(sortValue) + '&active_only=' + encodeURIComponent(activeOnlyValue);
                    }
                });
            </script>

            <div class="space-y-8 md:grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 md:gap-8 xl:gap-8 md:space-y-0">
            @foreach($cards as $card)
                <div class="max-w-md w-full mx-auto">
                    <x-member.card
                        :card="$card"
                        :flippable="false"
                        :links="false"
                        :show-qr="false"
                        :auth-check="false"
                        :show-balance="false"
                        :custom-link="route('partner.analytics.card', ['card_id' => $card->id])"
                    />
                    <div class="text-gray-900 dark:text-gray-100">
                        <div class="mt-4"><span class="text-gray-600 dark:text-gray-400">{{ trans('common.name') }}:</span> <span class="font-semibold">{{ $card->name }}</span></div>
                        <div><span class="text-gray-600 dark:text-gray-400">{{ trans('common.club') }}:</span> <span class="font-semibold">{{ $card->club->name }}</span></div>
                        <div><span class="text-gray-600 dark:text-gray-400">{{ trans('common.views') }}:</span> <span class="font-semibold format-number">{{ $card->views }}</span></div>
                        <div><span class="text-gray-600 dark:text-gray-400">{{ trans('common.last_view') }}:</span> <span class="format-date font-semibold">{{ ($card->last_view) ? $card->last_view->diffForHumans() : trans('common.never') }}</span></div>
                        <div><span class="text-gray-600 dark:text-gray-400">{{ trans('common.total_purchased') }}:</span> <span class="font-semibold">{{ $card->parseMoney($card->total_amount_purchased) }}</span></div>
                        <div><span class="text-gray-600 dark:text-gray-400">{{ trans('common.points_issued') }}:</span> <span class="format-number font-semibold">{{ $card->number_of_points_issued }}</span></div>
                        <div><span class="text-gray-600 dark:text-gray-400">{{ trans('common.last_points_issued') }}:</span> <span class="format-date font-semibold">{{ ($card->last_points_issued_at) ? $card->last_points_issued_at->diffForHumans() : trans('common.never') }}</span></div>
                        <div><span class="text-gray-600 dark:text-gray-400">{{ trans('common.rewards_claimed') }}:</span> <span class="format-number font-semibold">{{ $card->number_of_rewards_redeemed }}</span></div>
                        <div><span class="text-gray-600 dark:text-gray-400">{{ trans('common.last_reward_claimed') }}:</span> <span class="format-date font-semibold">{{ ($card->last_reward_redeemed_at) ? $card->last_reward_redeemed_at->diffForHumans() : trans('common.never') }}</span></div>
                        <div><span class="text-gray-600 dark:text-gray-400">{{ trans('common.active') }}:</span> <span class="font-semibold">{!! ($card->is_active) ? ''.trans('common.yes').''
                            : ''.trans('common.no').'' !!}</span></div>
                    </div>
                        <a href="{{ route('member.card', ['card_id' => $card->id]) }}" target="_blank" class="mt-4 mb-2 flex items-center text-link">
                            <x-ui.icon icon="arrow-top-right-on-square" class="w-5 h-5 mr-2"/>
                            {{ trans('common.view_card_on_website') }}
                        </a>
                        <a href="{{ route('partner.data.edit', ['name' => 'cards', 'id' => $card->id]) }}" class="mb-2 flex items-center text-link">
                            <x-ui.icon icon="pencil-square" class="w-5 h-5 mr-2"/>
                            {{ trans('common.edit_card') }}
                        </a>
                        <a href="{{ route('partner.analytics.card', ['card_id' => $card->id]) }}" class="mb-4 flex items-center text-link">
                            <x-ui.icon icon="presentation-chart-line" class="w-5 h-5 mr-2"/>
                            {{ trans('common.analytics') }}
                        </a>
                </div>
            @endforeach
            </div>
        </div>
    </div>
</div>
@stop
