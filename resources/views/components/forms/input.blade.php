<div {!! $class ? 'class="' . $class . '"' : '' !!}>
    @if ($label || $rightText)
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
    @endif
    <div class="flex space-x-2" x-data="{ input: '{{ $type }}' }">
        <div class="relative w-full">
            @if ($icon)
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <x-ui.icon :icon="$icon" :class="($affixClass) ? $affixClass . 'w-5 h-5' : 'input-icon'" />
                </div>
            @endif
            @if ($prefix)
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="{{ $affixClass ?? 'text-gray-600 dark:text-gray-300 sm:text-sm' }}" id="{{ $id }}_prefix">{{ $prefix }}</span>
                </div>
                <script>
                    // Grab the elements
                    const {{ $id }}_prefix_label = document.getElementById('{{ $id }}_prefix');
                    const {{ $id }}_prefix_input = document.getElementById('{{ $id }}');
                
                    // Create an observer instance linked to the callback function
                    const {{ $id }}_prefix_observer = new IntersectionObserver((entries, {{ $id }}_prefix_observer) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                {{ $id }}_suffix_input.style.paddingLeft = {{ $id }}_prefix_label.offsetWidth + 20 + 'px';
                                {{ $id }}_prefix_observer.disconnect();
                            }
                        });
                    });

                    // Start observing the target element
                    {{ $id }}_prefix_observer.observe({{ $id }}_prefix_label);
                </script>
            @endif
            <input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}"
                value="{{ $value }}"
                class="@if ($type == 'color') h-16 @endif bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 @if ($icon) pl-10 @endif @error($nameToDotNotation) is-invalid @enderror {{ $inputClass }}"
                placeholder="{{ $placeholder }}" @if ($required) required @endif
                @if ($autofocus) autofocus @endif {{ $attributes }} x-bind:type="input"
                @error($nameToDotNotation) onkeydown="this.classList.remove('is-invalid')" @enderror
                @if ($type == 'range') oninput="document.getElementById('{{ $id }}_output').value = this.value" @endif
            >
            @if ($suffix)
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <span class="{{ $affixClass ?? 'text-gray-600 dark:text-gray-300 sm:text-sm' }}" id="{{ $id }}_suffix">{{ $suffix }}</span>
                </div>
                <script>
                    // Grab the elements
                    const {{ $id }}_suffix_label = document.getElementById('{{ $id }}_suffix');
                    const {{ $id }}_suffix_input = document.getElementById('{{ $id }}');
                
                    // Create an observer instance linked to the callback function
                    const {{ $id }}_suffix_observer = new IntersectionObserver((entries, {{ $id }}_suffix_observer) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                {{ $id }}_suffix_input.style.paddingRight = {{ $id }}_suffix_label.offsetWidth + 20 + 'px';
                                {{ $id }}_suffix_observer.disconnect();
                            }
                        });
                    });

                    // Start observing the target element
                    {{ $id }}_suffix_observer.observe({{ $id }}_suffix_label);
                </script>
            @endif
        </div>
        @if ($type == 'range') 
            <div class="flex space-x-2">
                <input type="number" id="{{ $id }}_output" {{ $attributes }} name="{{ $name }}" value="{{ $value }}" oninput="document.getElementById('{{ $id }}').value = this.value"
                class="bg-gray-50 border border-gray-300 text-center text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 @if ($icon) pl-10 @endif @error($nameToDotNotation) is-invalid @enderror"
                >
            </div>
        @endif
        @if ($type == 'password')
            <input id="{{ $id }}_changed" type="hidden" value="" />
            <button type="button" tabindex="-1" class="flex-1 btn px-3 focus:ring-2 focus:ring-primary-600 borderfocus:border-primary-600 dark:focus:ring-primary-500 dark:focus:border-primary-500 border border-gray-300 dark:border-gray-600"
                x-on:click="input = (input === 'password') ? 'text' : 'password'">
                <svg x-show="input === 'password'" aria-hidden="true" class="w-4 h-4" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <svg x-show="input != 'password'" aria-hidden="true" class="w-4 h-4" fill="none"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
            </button>
            @if($generatePassword)
                <button type="button" tabindex="-1" class="flex-1 btn px-3 focus:ring-2 focus:ring-primary-600 borderfocus:border-primary-600 dark:focus:ring-primary-500 dark:focus:border-primary-500 border border-gray-300 dark:border-gray-600"
                    onClick="document.getElementById('{{ $id }}').value = generatePassword(8)">
                    <x-ui.icon icon="refresh" class="w-4 h-4" data-fb="tooltip" title="{!! parse_attr(trans('common.generate_password')) !!}" />
                </button>
            @endif
        @endif
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
    @if($mailPassword)
    <x-forms.checkbox
        class="mt-3"
        name="send_user_password"
        :checked="$mailPasswordChecked"
        :label="trans('common.send_user_password')"
    />
    @endif
</div>
