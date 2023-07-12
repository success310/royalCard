@if(!empty($buttons))
<div {{ $attributes->except('class') }} class="flex rounded-md shadow-sm {{ $attributes->get('class') }}" role="group">
    @foreach($buttons as $button)
    <a href="{{ $button['url'] }}" @if(isset($button['attr'])) @foreach($button['attr'] as $attr => $value) {{ $attr }}="{{ $value }}" @endforeach @endif class="grow inline-flex items-center justify-center px-4 py-3 text-sm font-medium text-gray-900 bg-white border border-gray-200 @if($loop->first) rounded-l-md @endif @if($loop->last) rounded-r-md @endif hover:bg-gray-100 hover:text-gray-700 focus:z-10 focus:ring-2 focus:ring-gray-700 focus:text-gray-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-500 dark:focus:text-white">
        <x-ui.icon :icon="$button['icon']" class="w-4 h-4 mr-2" />
        {{ $button['text'] }}
    </a>
    @endforeach
</div>
@endif