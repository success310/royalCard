<div {{ $attributes->except('class') }} class="w-full p-2 sm:p-4 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 {{ $attributes->get('class') }}">
    <div class="flow-root">
        <ul>
            <li>
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        @if ($avatar)
                            <img class="w-8 h-8 rounded-full" src="{{ $avatar }}" alt="{{ ($transaction) ? parse_attr($transaction->staff_name) : parse_attr($staff->name) }}">
                        @else
                            <x-ui.icon icon="user-circle" class="m-1 w-8 h-8 text-gray-900 dark:text-gray-300" />
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                            {{ ($transaction) ? $transaction->staff_name : $staff->name }}
                        </p>
                        <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                            {{ ($transaction) ? $transaction->staff_email : $staff->email }}
                        </p>
                    </div>
                    <div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>