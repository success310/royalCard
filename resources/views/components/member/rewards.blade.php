<div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-6">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <tbody>
            @foreach ($card->activeRewards as $reward)
                <tr data-clickable-href="{{ route('member.card.reward', ['card_id' => $card->id, 'reward_id' => $reward->id]) }}" class="@if(!$loop->last) border-b @endif dark:border-gray-700 @if($currentReward && $currentReward->id == $reward->id) bg-gray-200 hover:bg-gray-200 dark:bg-gray-900 dark:hover:bg-800 @else hover:bg-gray-100 bg-white dark:bg-gray-800 dark:hover:bg-gray-800/50 @endif cursor-pointer">
                    @if($reward->image1)
                        <td class="w-32">
                            <div class="p-3 sm:p-4 flex items-center justify-center h-full">
                                @if($reward->image1)
                                    <img src="{{ $reward->getImageUrl('image1', 'xs') }}" style="width: 100%; aspect-ratio: {{ $reward->image1AspectRatio }};" alt="{{ parse_attr($reward->title) }}" class="rounded-md">
                                @else
                                    <x-ui.icon icon="gift" class="w-12 h-12 text-gray-900 dark:text-white" />
                                @endif
                            </div>
                        </td>
                    @endif
                    <td class="px-3 py-2 sm:px-6 sm:py-4 font-semibold text-gray-900 dark:text-white" @if(!$reward->image1) colspan="2" @endif>
                        <a href="{{ route('member.card.reward', ['card_id' => $card->id, 'reward_id' => $reward->id]) }}">{{ $reward->title }}</a>
                    </td>
                    <td class="px-3 py-2 sm:px-6 sm:py-4 font-semibold text-gray-900 dark:text-white">
                        <div class="flex items-center">
                            <x-ui.icon icon="coins" class="w-4 h-4 mr-1 text-gray-900 dark:text-white" />
                            <div class="format-number">{{ $reward->points }}</div>
                            @if($showClaimable && $card->getMemberBalance(null) >= $reward->points)
                                <div class="h-2.5 w-2.5 rounded-full bg-green-500 ml-2"></div>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>