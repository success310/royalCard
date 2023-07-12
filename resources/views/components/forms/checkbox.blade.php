<div class="{{ $class }}">
    <div class="flex items-center">
        <input type="hidden" name="{{ $name }}" id="{{ $id }}_value" value="{{ $checked ? '1' : '0' }}"
            @if ($model) x-model="{{ $model }}" @endif>
        <input
            class="w-4 h-4 bg-gray-100 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 @error($name) is-invalid @enderror"
            type="checkbox" value="{{ $value }}" id="{{ $id }}"
            onchange="document.getElementById('{{ $id }}_value').value = (document.getElementById('{{ $id }}').checked ? '1' : '0');@error($name) this.classList.remove('is-invalid') @enderror"
            @if ($autofocus) autofocus @endif @if ($checked) checked @endif
            {{ $attributes }}>
        @if ($label)
            <label for="{{ $id }}" class="@if($help) flex @endif items-center ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                {!! $label !!}
                @if ($help)
                    <div data-fb="tooltip" title="{!! parse_attr($help) !!}" class="ml-2 flex">
                        <x-ui.icon icon="info" class="h-4 w-4 text-gray-400 hover:text-gray-500" />
                    </div>
                @endif
            </label>
        @endif
    </div>
    @error($name)
        <div class="invalid-feedback flex">
            {{ $errors->first($name) }}
        </div>
    @else
        @if ($text)
            <p class="form-help-text">{!! $text !!}</p>
        @endif
    @enderror
</div>
