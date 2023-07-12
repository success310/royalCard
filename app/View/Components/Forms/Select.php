<?php

namespace App\View\Components\Forms;

use Illuminate\Support\Str;
use Illuminate\View\Component;

/**
 * Class Select
 *
 * Represents a select input in a form.
 */
class Select extends Component
{
    public string $type;
    public bool $multiselect;
    public ?array $options;
    public ?string $label;
    public ?string $name;
    public ?string $text;
    public ?string $help;
    public ?string $id;
    public ?string $placeholder;
    public ?string $icon;
    public $value;
    public bool $required;
    public bool $autofocus;
    public ?string $class;
    public ?string $rightText;
    public ?string $rightLink;
    public ?string $rightPosition;

    /**
     * Select constructor.
     *
     * @param string $type The select type. Default is 'select'.
     * @param bool $multiselect Whether the select is a multiselect. Default is false.
     * @param array|null $options The select options. Default is an empty array.
     * @param string|null $label The label text. Default is null.
     * @param string|null $name The input name. Default is null.
     * @param string|null $text The text below the input. Default is null.
     * @param string|null $help The tooltip balloon. Default is null.
     * @param string|null $id The input id. If not provided, it will be generated based on the name. Default is null.
     * @param string|null $placeholder The input placeholder. Default is null.
     * @param string|null $icon The input icon. Default is null.
     * @param string|array|null $value The input value. Default is null.
     * @param bool $required Whether the input is required. Default is false.
     * @param bool $autofocus Whether the input should be autofocused. Default is false.
     * @param string|null $class The input class. Default is null.
     * @param string|null $rightText The optional text/link right-aligned in the label. Default is null.
     * @param string|null $rightLink The optional text/link right-aligned in the label. Default is null.
     * @param string|null $rightPosition The position of the right text, 'top' or 'bottom'. Default is 'top'.
     */
    public function __construct(
        string $type = 'select',
        bool $multiselect = false,
        ?array $options = [],
        ?string $label = null,
        ?string $name = null,
        ?string $text = null,
        ?string $help = null,
        ?string $id = null,
        ?string $placeholder = null,
        ?string $icon = null,
        $value = null,
        bool $required = false,
        bool $autofocus = false,
        ?string $class = null,
        ?string $rightText = null,
        ?string $rightLink = null,
        ?string $rightPosition = 'top'
    ) {
        $this->type = $type;
        $this->multiselect = $multiselect;
        $this->options = $options ?? [];
        $this->label = $label;
        $this->name = $name;
        $this->text = $text;
        $this->help = $help;
        $this->id = $id ?? Str::slug($name, '_') . '-' . uniqid();
        $this->placeholder = $placeholder;
        $this->icon = $icon;
        $this->value = old($name, $value);
        $this->required = $required;
        $this->autofocus = $autofocus;
        $this->class = $class;
        $this->rightText = $rightText;
        $this->rightLink = $rightLink;
        $this->rightPosition = $rightPosition;

        if ($this->required) {
            $this->label .= ' *';
        } elseif ($type != 'belongsToMany') {
            // Add empty option
            $this->options = ['' => ''] + $this->options;
        }

        if ($this->type === 'time_zone' || $this->type === 'currency') {
            $i18nService = resolve('App\Services\I18nService');
            $data = $this->type === 'time_zone' ? $i18nService->getAllTimezones() : $i18nService->getAllCurrencies();
            $this->options += $data;
        }

        if ($this->type === 'locale') {
            $i18nService = resolve('App\Services\I18nService');
            $allTranslations = $i18nService->getAllTranslations();

            foreach ($allTranslations['all'] as $translation) {
                if (isset($translation['locale'])) {
                    $this->options += [$translation['locale'] => $translation['languageName'] . ' (' . $translation['countryName'] . ')'];
                }
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|Closure|string
     */
    public function render()
    {
        return view('components.forms.select');
    }
}
