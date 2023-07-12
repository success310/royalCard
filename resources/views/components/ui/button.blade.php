
@if ($type == 'pink-orange')
    @if ($href)
        <a {{ $attributes->except('class') }} href="{{ $href }}" 
            class="relative w-full inline-flex items-center justify-center p-0.5 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-pink-500 to-orange-400 group-hover:from-pink-500 group-hover:to-orange-400 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-pink-200 dark:focus:ring-pink-800 {{ $attributes->get('class') }}">
            <span class="flex items-center justify-center relative w-full px-5 py-2.5 transition-all ease-in duration-75 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0">
                @if ($icon)
                    <x-ui.icon :icon="$icon" class="w-5 h-5 mr-2" />
                @endif
                {!! $text !!}
            </span>
        </a>
    @else
        <button {{ $attributes->except('class') }} class="relative w-full inline-flex items-center justify-center p-0.5 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-pink-500 to-orange-400 group-hover:from-pink-500 group-hover:to-orange-400 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-pink-200 dark:focus:ring-pink-800 {{ $attributes->get('class') }}">
            <span class="flex items-center justify-center relative w-full px-5 py-2.5 transition-all ease-in duration-75 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0">
                @if ($icon)
                    <x-ui.icon :icon="$icon" class="w-5 h-5 mr-2" />
                @endif
                {!! $text !!}
            </span>
        </button>
    @endif
@endif