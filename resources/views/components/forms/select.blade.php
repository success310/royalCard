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
<style type="text/css">
[data-te-input-notch-ref] div {
    border: 0 !important;
}
[dataf-te-select-wrapper-ref] > div > span {
    color: #fff !important;
}
</style>
    <select data-te-select-init data-te-select-filter="true" data-te-class-select-input="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 py-[9px] dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
    data-te-class-select-arrow="absolute right-3 top-3 text-[0.65rem] cursor-pointer text-gray-700 dark:text-gray-100 group-data-[te-was-validated]/validation:peer-valid:text-green-600 group-data-[te-was-validated]/validation:peer-invalid:text-[rgb(220,76,100)]"
    data-te-class-select-option="flex flex-row items-center justify-between w-full px-4 truncate text-gray-700 bg-transparent select-none cursor-pointer data-[te-input-multiple-active]:bg-gray/5 hover:[&:not([data-te-select-option-disabled])]:bg-black/5 data-[te-input-state-active]:bg-black/5 data-[te-select-option-selected]:data-[te-input-state-active]:bg-black/5 data-[te-select-selected]:data-[te-select-option-disabled]:cursor-default data-[te-select-selected]:data-[te-select-option-disabled]:text-gray-400 data-[te-select-selected]:data-[te-select-option-disabled]:bg-transparent data-[te-select-option-selected]:bg-black/[0.02] data-[te-select-option-disabled]:text-gray-400 data-[te-select-option-disabled]:cursor-default group-data-[te-select-option-group-ref]/opt:pl-7 dark:text-gray-200 dark:hover:[&:not([data-te-select-option-disabled])]:bg-white/30 dark:data-[te-input-state-active]:bg-white/30 dark:data-[te-select-option-selected]:data-[te-input-state-active]:bg-white/30 dark:data-[te-select-option-disabled]:text-gray-400 dark:data-[te-input-multiple-active]:bg-white/30"
        class="input-select @error($name) is-invalid @enderror"
        id="{{ $id }}"
        @if ($multiselect)
            name="{{ $name }}[]"
        @else
            name="{{ $name }}" 
        @endif
        placeholder="{{ $placeholder }}"
        @if ($required) required @endif 
        @if ($multiselect) multiple @endif 
        @if ($autofocus) autofocus @endif
        {{ $attributes }}>
        <?php
        foreach ($options as $option_value => $option_text) {
            if (is_array($option_text)) {
                echo '<optgroup label="' . $option_value . '">';
        
                foreach ($option_text as $optgroup_value => $optgroup_text) {
                $selected = ($value === $option_value || (is_array($value) && in_array($option_value, $value))) ? ' selected' : '';
                    echo '<option value="' . $optgroup_value . '"' . $selected . '>' . $optgroup_text . '</option>';
                }
        
                echo '</optgroup>';
            } else {
                $selected = ($value === $option_value || (is_array($value) && in_array($option_value, $value))) ? ' selected' : '';
                echo '<option value="' . $option_value . '"' . $selected . '>' . $option_text . '</option>';
            }
        }
        ?>
    </select>

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
