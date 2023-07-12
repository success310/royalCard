<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

/**
 * Class Image
 *
 * Represents an image input in a form.
 */
class Image extends Component
{
    public string $type;
    public ?string $label;
    public ?string $class;
    public ?string $name;
    public ?string $text;
    public ?string $help;
    public ?string $id;
    public ?string $placeholder;
    public ?string $height;
    public ?string $icon;
    public ?string $value;
    public bool $required;
    public ?string $accept;
    public array $validate;
    public ?string $requirements;

    /**
     * Image constructor.
     *
     * @param string $type The input type. Default is 'image'.
     * @param string|null $label The label text. Default is null.
     * @param string|null $class The input class. Default is null.
     * @param string|null $name The input name. Default is null.
     * @param string|null $text The text below the input. Default is null.
     * @param string|null $help The tooltip balloon. Default is null.
     * @param string|null $id The input id. If not provided, it will be generated based on the name. Default is null.
     * @param string|null $placeholder The input placeholder. Default is null.
     * @param string|null $height The Tailwind height class for the element. Default is null.
     * @param string|null $icon The input icon. Default is null.
     * @param string|null $value The input value. Default is null.
     * @param bool $required Whether the input is required. Default is false.
     * @param string|false $accept The accepted MIME types. Default is false.
     * @param array $validate The validation array. Default is an empty array.
     * @param string $requirements The requirements to display at file upload. Default is an empty string.
     */
    public function __construct(
        string $type = 'image',
        ?string $label = null,
        ?string $class = null,
        ?string $name = null,
        ?string $text = null,
        ?string $help = null,
        ?string $id = null,
        ?string $placeholder = null,
        ?string $height = null,
        ?string $icon = null,
        ?string $value = null,
        bool $required = false,
        $accept = false,
        array $validate = [],
        string $requirements = ''
    ) {
        $this->type = $type;
        $this->label = $label;
        $this->class = $class;
        $this->name = $name;
        $this->text = $text;
        $this->help = $help;
        $this->id = $id ?? Str::slug($name, '_') . '-' . uniqid();
        $this->placeholder = $placeholder;
        $this->height = $height;
        $this->icon = $icon;
        $this->value = old($name, $value);
        $this->required = $required;
        if ($this->required) {
            $this->label .= ' *';
        }
        $this->accept = $accept;
        $this->validate = $validate;
        $this->requirements = ($this->validate) ? $this->processImageValidationRules($this->validate) : null;
    }

    /**
     * Process image validation rules and generate a human-readable requirements string.
     *
     * @param array $validate An array of validation rules.
     * @return string A human-readable requirements string.
     */
    private function processImageValidationRules(array $validate): string
    {
        $mimes = '';
        $maxWidth = '';
        $maxHeight = '';
        $maxSizeInKb = '';
        foreach ($validate as $validation) {
            if (Str::startsWith($validation, 'mimes:')) {
                $mimes = explode(',', Str::replaceFirst('mimes:', '', $validation));
                $mimes = implode(', ', $mimes) . ' ' . strtolower(trans('common.or')) . ' ' . array_pop($mimes);
            }

            if (Str::startsWith($validation, 'dimensions:')) {
                $dimensions = explode(',', Str::replaceFirst('dimensions:', '', $validation));
                foreach ($dimensions as $dimension) {
                    if (Str::startsWith($dimension, 'max_width=')) {
                        $maxWidth = Str::replaceFirst('max_width=', '', $dimension);
                    }
                    if (Str::startsWith($dimension, 'max_height=')) {
                        $maxHeight = Str::replaceFirst('max_height=', '', $dimension);
                    }
                }
            }

            if (Str::startsWith($validation, 'max:')) {
                $maxSizeInKb = Str::replaceFirst('max:', '', $validation);
                $maxSizeInKb = formatBytes($maxSizeInKb * 1024);
            }
        }

        return "$mimes (Max. {$maxWidth}x{$maxHeight}px, $maxSizeInKb)";
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|Closure|string
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.image');
    }
}
