@if (in_array($column['type'], ['string', 'textarea']))
    @if ($column['translatable'])
        @if (count($languages['all']) > 1)
            <fieldset class="p-4 pt-0 border rounded-lg border-gray-300 dark:border-gray-600">
                <legend class="input-label">{{ $column['text'] }}</legend>
        @endif
@php
$label = (count($languages['all']) > 1) ? $label = '<div class="inline-flex items-center"><div class="w-4 h-4 mr-2 rounded-full fis fi-' . strtolower($languages['current']['countryCode']) . '"></div> ' . $languages['current']['languageName'] . ' (' . $languages['current']['countryCode'] . ')' . '</div>' : $column['text']; //  . ' (' . $languages['current']['languageName'] . ')';

$value = $form['data']->getTranslation($column['name'], $languages['current']['locale'], false) ?? $column['default'];
@endphp
        @if ($column['type'] == 'string')
            <x-forms.input
                :value="$value"
                :type="$column['format']"
                :prefix="$column['prefix']"
                :suffix="$column['suffix']"
                :min="$column['min']"
                :max="$column['max']"
                :step="$column['step']"
                :name="$column['name'] . '[' . $languages['current']['locale'] . ']'"
                :icon="$column['format'] == 'email' ? 'envelope' : null"
                :label="$label"
                :help="$column['help']"
                :placeholder="$column['placeholder']"
                :required="in_array('required', $column['validate'])"
            />
        @elseif ($column['type'] == 'textarea')
            <x-forms.textarea
                :value="$value"
                :type="$column['format']"
                :prefix="$column['prefix']"
                :suffix="$column['suffix']"
                :min="$column['min']"
                :max="$column['max']"
                :step="$column['step']"
                :name="$column['name'] . '[' . $languages['current']['locale'] . ']'"
                :icon="$column['format'] == 'email' ? 'envelope' : null"
                :label="$label"
                :help="$column['help']"
                :placeholder="$column['placeholder']"
                :required="in_array('required', $column['validate'])"
            />
        @endif

        @foreach ($languages['all'] as $language)
            @if ($languages['current']['countryCode'] != $language['countryCode'])
@php
$label = '<div class="inline-flex items-center"><div class="w-4 h-4 mr-2 rounded-full fis fi-' . strtolower($language['countryCode']) . '"></div> ' . $language['languageName'] . ' (' . $language['countryCode'] . ')' . '</div>';

$value = $form['data']->getTranslation($column['name'], $language['locale'], false) ?? $column['default'];
@endphp
                @if ($column['type'] == 'string')
                    <x-forms.input
                        class="mt-6"
                        :value="$value"
                        :type="$column['format']"
                        :prefix="$column['prefix']"
                        :suffix="$column['suffix']"
                        :min="$column['min']"
                        :max="$column['max']"
                        :step="$column['step']"
                        :name="$column['name'] . '[' . $language['locale'] . ']'"
                        :icon="$column['format'] == 'email' ? 'envelope' : null"
                        :label="$label"
                        :help="$column['help']"
                        :placeholder="$column['placeholder']"
                        :required="in_array('required', $column['validate'])"
                    />
                @elseif ($column['type'] == 'textarea')
                    <x-forms.textarea
                        class="mt-6"
                        :value="$value"
                        :type="$column['format']"
                        :prefix="$column['prefix']"
                        :suffix="$column['suffix']"
                        :min="$column['min']"
                        :max="$column['max']"
                        :step="$column['step']"
                        :name="$column['name'] . '[' . $language['locale'] . ']'"
                        :icon="$column['format'] == 'email' ? 'envelope' : null"
                        :label="$label"
                        :help="$column['help']"
                        :placeholder="$column['placeholder']"
                        :required="in_array('required', $column['validate'])"
                    />
                @endif
            @endif
        @endforeach
        @if (count($languages['all']) > 1)
            </fieldset>
        @endif
    @else
        @if ($column['type'] == 'string')
            <x-forms.input
                :value="$form['data']->{$column['name']} ?? $column['default']"
                :type="$column['format']"
                :prefix="$column['prefix']"
                :suffix="$column['suffix']"
                :min="$column['min']"
                :max="$column['max']"
                :step="$column['step']"
                :name="$column['name']"
                :icon="$column['format'] == 'email' ? 'envelope' : null"
                :label="$column['text']"
                :help="$column['help']"
                :placeholder="$column['placeholder']"
                :required="in_array('required', $column['validate'])"
            />
        @elseif ($column['type'] == 'textarea')
            <x-forms.textarea
                :value="$form['data']->{$column['name']} ?? $column['default']"
                :type="$column['format']"
                :prefix="$column['prefix']"
                :suffix="$column['suffix']"
                :min="$column['min']"
                :max="$column['max']"
                :step="$column['step']"
                :name="$column['name']"
                :icon="$column['format'] == 'email' ? 'envelope' : null"
                :label="$column['text']"
                :help="$column['help']"
                :placeholder="$column['placeholder']"
                :required="in_array('required', $column['validate'])"
            />
        @endif
    @endif
@elseif($column['type'] == 'password')
    <x-forms.input 
        value=""
        type="password"
        :prefix="$column['prefix']"
        :suffix="$column['suffix']"
        :name="$column['name']"
        icon="key"
        :generate-password="$column['generatePasswordButton']"
        :mail-password="$column['mailUserPassword']"
        :mail-password-checked="$column['mailUserPasswordChecked']"
        :label="$column['text']"
        :label="$column['text']"
        :help="$column['help']"
        :placeholder="$column['placeholder']"
        :required="in_array('required', $column['validate'])"
    />
@elseif($column['type'] == 'image' || $column['type'] == 'avatar')
    <x-forms.image
        :value="$form['data']->{$column['name']} !== null && $column['conversion'] !== null ? $form['data']->{$column['name'] . '-' . $column['conversion']} : $form['data']->{$column['name']}"
        :type="$column['type'] == 'avatar' ? 'avatar' : 'image'" 
        :name="$column['name']"
        :label="$column['text']"
        :help="$column['help']"
        :placeholder="$column['placeholder']"
        :accept="$column['accept']"
        :validate="$column['validate']"
        :required="in_array('required', $column['validate'])"
    />
@elseif($column['type'] == 'boolean')
    <x-forms.checkbox
        :name="$column['name']"
        :checked="$form['data']->{$column['name']} ?? $column['default'] == 1 ? 1 : 0"
        :label="$column['text']"
        :help="$column['help']"
        :required="in_array('required', $column['validate'])"
    />
@elseif(in_array($column['type'], ['time_zone', 'currency', 'locale', 'select', 'belongsToMany', 'hasMany', 'belongsTo']))
    <x-forms.select
        :type="$column['type']"
        :multiselect="(in_array($column['type'], ['belongsToMany', 'hasMany'])) ? true : false"
        :name="$column['name']"
        :value="$form['data']->{$column['name']} ?? $column['default']"
        :options="$column['options']"
        :label="$column['text']"
        :help="$column['help']"
        :required="in_array('required', $column['validate'])"
    />
@endif