<div {{ $attributes->except('class') }} class="w-full p-2 sm:p-4 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 {{ $attributes->get('class') }}">
    <div class="flow-root">
        <ul>
            <li>
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        @if ($member->avatar)
                            <img class="w-8 h-8 rounded-full" src="{{ $member->avatar }}" alt="{{ parse_attr($member->name) }}">
                        @else
                            <x-ui.icon icon="user-circle" class="m-1 w-8 h-8 text-gray-900 dark:text-gray-300" />
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                            {{ $member->name }}
                        </p>
                        <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                            {{ trans('common.joined_in_month_year', ['month_year' => $member->created_at->setTimezone(app()->make('i18n')->time_zone)->translatedFormat('F Y')]) }}
                        </p>
                    </div>
                    <div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                        {{ $member->unique_identifier }}
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
