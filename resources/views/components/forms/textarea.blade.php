<div {!! $class ? 'class="' . $class . '"' : '' !!}>
    <div class="flex">
        @if ($label)
            <label for="{{ $id }}" class="input-label {{ $classLabel }} @error($nameToDotNotation) is-invalid-label @enderror">
                {!! $label !!}
                @if ($help)
                    <div data-fb="tooltip" title="{!! parse_attr($help) !!}" class="ml-2">
                        <x-ui.icon icon="info" class="h-4 w-4 text-gray-400 hover:text-gray-500" />
                    </div>
                @endif
            </label>
        @endif
        @if ($rightText && $rightPosition == 'top')
            <div class="flex-1 items-center text-right text mb-2">
                @if ($rightLink)
                    <a href="{{ $rightLink }}" class="text-link">
                @endif
                {!! $rightText !!}
                @if ($rightLink)
                    </a>
                @endif
            </div>
        @endif
    </div>
    <div class="flex space-x-2" x-data="{ input: '{{ $type }}' }">
        <div class="relative w-full">
            @if ($icon)
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" aria-hidden="true" class="input-icon">
                        @if ($icon == 'envelope')
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        @endif
                        @if ($icon == 'key')
                            <path fill-rule="evenodd"
                                d="M8 7a5 5 0 113.61 4.804l-1.903 1.903A1 1 0 019 14H8v1a1 1 0 01-1 1H6v1a1 1 0 01-1 1H3a1 1 0 01-1-1v-2a1 1 0 01.293-.707L8.196 8.39A5.002 5.002 0 018 7zm5-3a.75.75 0 000 1.5A1.5 1.5 0 0114.5 7 .75.75 0 0016 7a3 3 0 00-3-3z"
                                clip-rule="evenodd" />
                        @endif
                    </svg>
                </div>
            @endif
            <textarea rows="5" id="{{ $id }}" name="{{ $name }}" class="@if ($type == 'color') h-16 @endif bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 @if ($icon) pl-10 @endif @error($nameToDotNotation) is-invalid @enderror"
                placeholder="{{ $placeholder }}" @if ($required) required @endif
                @if ($autofocus) autofocus @endif {{ $attributes }} x-bind:type="input"
                @error($nameToDotNotation) onkeydown="this.classList.remove('is-invalid')" @enderror
                @if ($type == 'range') oninput="document.getElementById('{{ $id }}_output').value = this.value" @endif
            >{{ $value }}</textarea>
        </div>
    </div>
    <div class="flex space-x-2">
        @error($nameToDotNotation)
            <div class="invalid-msg">
                {{ $errors->first($nameToDotNotation) }}
            </div>
        @else
            @if ($text)
                <p class="form-help-text">{!! $text !!}</p>
            @endif
        @enderror

        @if ($rightText && $rightPosition == 'bottom')
            <div class="flex-1 items-center text-right text mt-2">
                @if ($rightLink)
                    <a href="{{ $rightLink }}" class="text-link">
                @endif
                {!! $rightText !!}
                @if ($rightLink)
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
