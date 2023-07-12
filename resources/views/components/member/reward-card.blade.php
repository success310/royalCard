<div {{ $attributes->except('class') }}
    class="w-full p-2 sm:p-4 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 {{ $attributes->get('class') }}">
    <div class="flow-root">
        <ul>
            <li>
                <div class="flex items-center space-x-4">
                    @if ($reward->images)
                    <div class="flex-shrink-0" style="min-width: 120px; aspect-ratio: {{ $reward->images[0]['ratio'] }};">
                        <img class="rounded-md" src="{{ $reward->images[0]['xs'] }}" style="width: 100%; aspect-ratio: {{ $reward->images[0]['ratio'] }};" alt="{{ parse_attr($reward->title) }}">
                    </div>
                    @else
                    <div class="flex-shrink-0">
                        <x-ui.icon icon="gift" class="m-1 w-8 h-8 text-gray-900 dark:text-gray-300" />
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                            {{ $reward->title }}
                        </p>
                        <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                            {{ $reward->description }}
                        </p>
                    </div>
                    <div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                        <div class="flex items-center">
                            <x-ui.icon icon="coins" class="w-4 h-4 mr-1 text-gray-900 dark:text-white" />
                            <div class="format-number">{{ $reward->points }}</div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
