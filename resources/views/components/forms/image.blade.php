<div {!! $class ? 'class="' . $class . '"' : '' !!}>
    <div class="flex">
        @if ($label)
            <label for="{{ $id }}" class="input-label @error($name) is-invalid-label @enderror">
                {!! $label !!}
                @if ($help)
                    <div data-fb="tooltip" title="{!! parse_attr($help) !!}" class="ml-2">
                        <x-ui.icon icon="info" class="h-4 w-4 text-gray-400 hover:text-gray-500" />
                    </div>
                @endif
            </label>
        @endif
    </div>
    <div class="flex items-center justify-center relative w-full">
        <label
            class="dropzone-label relative overflow-hidden flex items-center justify-center w-full {{ $height ?? 'h-32' }} min-h-[80px] border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
            <div class="upload-text flex flex-col items-center justify-center">
                <x-ui.icon :icon="$icon ?? 'upload-cloud'" class="w-10 h-10 mb-2 text-gray-400" />
                <p class="mb-1 text-sm text-center text-gray-500 dark:text-gray-400">
                    <span class="font-semibold">{{ $placeholder ?? trans('common.click_to_upload') }}</span>
                </p>
                @if($requirements)
                    <p class="text-xs text-center text-gray-500 dark:text-gray-400">{{ $requirements }}</p>
                @endif
            </div>
            <input id="{{ $id }}" name="{{ $name }}" class="dropzone-file absolute inset-0 z-10 w-full h-full opacity-0 cursor-pointer" accept="{{ $accept }}" type="file" {{ $attributes }} />
            <input name="{{ $name }}_changed" class="image-changed" type="hidden" value="" />
            <input name="{{ $name }}_deleted" class="image-deleted" type="hidden" value="" />

            <div class="image-wrapper flex items-center absolute inset-0 hidden">
                <img class="image-preview object-cover mx-auto max-h-[240px] p-4" src="{{ $value === null ? 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=' : $value }}" alt="{{ trans('common.image_preview') }}" />
            </div>
        </label>
        <button type="button"
            class="remove-image hidden absolute top-2 right-2 z-20 text-red-600 inline-flex items-center bg-white hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg px-3 py-2 text-xs text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
            <svg class="mr-1 -ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                    clip-rule="evenodd"></path>
            </svg>
            {{ trans('common.remove_image') }}
        </button>
    </div>

    <div class="flex space-x-2">
        @error($name)
            <div class="invalid-msg">
                {{ $errors->first($name) }}
            </div>
        @else
            @if ($text)
                <p class="form-help-text">{!! $text !!}</p>
            @endif
        @enderror
    </div>
</div>
