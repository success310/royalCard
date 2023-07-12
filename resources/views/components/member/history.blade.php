@if(!$member)
<div {{ $attributes->except('class') }} class="mt-6 format format-sm sm:format-base lg:format-md dark:format-invert {{ $attributes->get('class') }}">
    <h3>{!! trans('common.log_in_to_see_history', ['log_in' => "<a rel=\"nofollow\" href=\"".route('member.login')."\" class=\"text-link underline\">".trans('common.log_in')."</a>"]) !!}</h3>
</div>
@else
    @if($transactions->count() == 0)
    <div {{ $attributes->except('class') }} class="mt-6 format format-sm sm:format-base lg:format-md dark:format-invert {{ $attributes->get('class') }}">
        <h3>{{ trans('common.no_history_yet') }}</h3>
    </div>
    @else

    <ol {{ $attributes->except('class') }} class="relative border-l border-gray-200 dark:border-gray-700 ml-6 {{ $attributes->get('class') }}">
        @foreach($transactions as $transaction)
        @php
        $transactionExpired = ($transaction->reward_points === null && ($transaction->expires_at->isPast() || $transaction->points == $transaction->points_used)) ? true : false;
        @endphp
        <li class="mb-10 ml-6">
            <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -left-3 ring-8 ring-gray-50 dark:ring-gray-900 @if($transactionExpired) bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-300 @else bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-300 @endif">
                @if($transaction->event == 'initial_bonus_points')
                    <x-ui.icon icon="star" class="w-3 h-3" />
                @elseif($transaction->event == 'staff_credited_points_for_purchase')
                    <x-ui.icon icon="coins" class="w-3 h-3" />
                @elseif($transaction->event == 'staff_redeemed_points_for_reward')
                    <x-ui.icon icon="gift" class="w-3 h-3" />
                @endif
            </span>
            <div class="flex justify-between w-full">
                <h3 class="mb-1 text-lg font-semibold @if($transactionExpired) text-gray-400 dark:text-gray-600 @else text-gray-900 dark:text-white @endif">
                    @if($transaction->event == 'initial_bonus_points')
                        {!! trans('common.received_initial_bonus_points', ['points' => '<span class=\'format-number\'>' . $transaction->points . '</span>']) !!}
                    @elseif($transaction->event == 'staff_credited_points_for_purchase')
                        {!! trans('common.purchase') !!}
                        <span class="text-sm font-medium mr-2 px-2.5 py-0.5 rounded ml-1 @if($transactionExpired) bg-gray-200 text-gray-800 dark:bg-gray-800 dark:text-gray-300 @else bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-300 @endif">{{ $transaction->purchase_amount_formatted }}</span>
                    @elseif($transaction->event == 'staff_redeemed_points_for_reward')
                        {!! trans('common.reward') !!}
                    @endif
                </h3>
                <time class="ml-2 mb-2 text-right text-sm font-normal leading-none mt-2 order-last @if($transactionExpired) text-gray-300 dark:text-gray-600 @else text-gray-400 dark:text-gray-500 @endif">{{ $transaction->created_at->diffForHumans() }}</time>
            </div>
            <p class="text-base font-normal @if($transactionExpired) text-gray-300 dark:text-gray-600 @else text-gray-500 dark:text-gray-400 @endif">
                {{ ($transaction->points > 0) ? '+' : '' }}{!! trans('common.amount_points', ['points' => '<span class=\'format-number\'>' . $transaction->points . '</span>']) !!}
            </p>
            <p class="text-sm font-normal @if($transactionExpired) text-gray-300 dark:text-gray-600 @else text-gray-400 dark:text-gray-500 @endif mt-2">
                @if(in_array($transaction->event, ['staff_redeemed_points_for_reward']))
                    {{ $transaction->reward_title }}
                @endif
                @if(in_array($transaction->event, ['initial_bonus_points', 'staff_credited_points_for_purchase']))
                    @if($transaction->expires_at->isPast() && $transaction->points_used == 0)
                        {!! trans('common.points_expired', ['points' => '<span class=\'format-number\'>' . $transaction->points . '</span>', 'dateDiffFromNow' => $transaction->expires_at->diffForHumans()]) !!}
                    @elseif($transaction->expires_at->isPast() && $transaction->points_used > 0)
                        @if($transaction->points - $transaction->points_used == 0)
                            {!! trans('common.points_used', ['points_used' => '<span class=\'format-number\'>' . $transaction->points_used . '</span>', 'points' => '<span class=\'format-number\'>' . ($transaction->points - $transaction->points_used) . '</span>']) !!}
                        @else
                            {!! trans('common.points_used_and_expired', ['points_used' => '<span class=\'format-number\'>' . $transaction->points_used . '</span>', 'points' => '<span class=\'format-number\'>' . ($transaction->points - $transaction->points_used) . '</span>', 'dateDiffFromNow' => $transaction->expires_at->diffForHumans()]) !!}
                        @endif
                    @elseif($transaction->expires_at->isFuture() && $transaction->points_used == 0)
                        {!! trans('common.points_expire', ['points' => '<span class=\'format-number\'>' . $transaction->points . '</span>', 'dateDiffFromNow' => $transaction->expires_at->diffForHumans()]) !!}
                    @elseif($transaction->expires_at->isFuture() && $transaction->points_used > 0)
                        @if($transaction->points - $transaction->points_used == 0)
                            {!! trans('common.points_used', ['points_used' => '<span class=\'format-number\'>' . $transaction->points_used . '</span>', 'points' => '<span class=\'format-number\'>' . ($transaction->points - $transaction->points_used) . '</span>']) !!}
                        @else
                            {!! trans('common.points_used_points_expire', ['points_used' => '<span class=\'format-number\'>' . $transaction->points_used . '</span>', 'points' => '<span class=\'format-number\'>' . ($transaction->points - $transaction->points_used) . '</span>', 'dateDiffFromNow' => $transaction->expires_at->diffForHumans()]) !!}
                        @endif
                    @endif
                @endif
            </p>

            @if($showStaff)
                <x-staff.staff-card class="mt-3" :transaction="$transaction" :staff="$transaction->staff" />
            @endif

            @if($showNotes && $transaction->note)
            <div class="flex p-4 mt-4 text-blue-800 rounded-lg bg-blue-100 dark:bg-blue-950 dark:text-blue-400">
                <x-ui.icon icon="info" class="flex-shrink-0 w-5 h-5"/>
                <div class="ml-3 text-sm font-normal">
                    {{ $transaction->note }}
                </div>
            </div>
            @endif
            @if($showAttachments && $transaction->image)
            
            <a target="_blank" href="{{ $transaction->image }}" class="mt-3 flex items-center text-link">
                <x-ui.icon icon="paper-clip" class="w-5 h-5 mr-1"/>
                {{ trans('common.attachment') }}
            </a>
            @endif

        </li>
        @endforeach
    </ol>
    @endif
@endif


