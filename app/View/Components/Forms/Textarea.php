<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class Textarea extends Component
{
    public ?string $type;
    public ?string $label;
    public ?string $name;
    public string $nameToDotNotation;
    public ?string $text;
    public ?string $help;
    public ?string $id;
    public ?string $placeholder;
    public ?string $icon;
    public $value;
    public bool $required;
    public bool $autofocus;
    public ?string $class;
    public ?string $classLabel;
    public ?string $rightText;
    public ?string $rightLink;
    public ?string $rightPosition;

    /**
     * Create a new component instance.
     *
     * @param string|null $type The input type. Default is 'textarea'.
     * @param string|null $label The label text. Default is null.
     * @param string|null $name The input name. Default is null.
     * @param string|null $nameToDotNotation The input name with dot notation. Default is null.
     * @param string|null $text The text below the input. Default is null.
     * @param string|null $help The tooltip balloon. Default is null.
     * @param string|null $id The input id. If not provided, it will be generated based on the name.
     * @param string|null $placeholder The input placeholder. Default is null.
     * @param string|null $icon The input icon. Default is null.
     * @param string|null $value The input value. Default is null.
     * @param bool $required Whether the input is required. Default is false.
     * @param bool $autofocus Whether the input should be autofocused. Default is false.
     * @param string|null $class The input class. Default is null.
     * @param string|null $classLabel The label class. Default is null.
     * @param string|null $rightText The optional text/link right-aligned in the label. Default is null.
     * @param string|null $rightLink The optional text/link right-aligned in the label. Default is null.
     * @param string|null $rightPosition The position of the right text, 'top' or 'bottom'. Default is 'top'.
     */
    public function __construct(
        ?string $type = 'textarea',
        ?string $label = null,
        ?string $name = null,
        ?string $nameToDotNotation = null,
        ?string $text = null,
        ?string $help = null,
        ?string $id = null,
        ?string $placeholder = null,
        ?string $icon = null,
        $value = null,
        bool $required = false,
        bool $autofocus = false,
        ?string $class = null,
        ?string $classLabel = null,
        ?string $rightText = null,
        ?string $rightLink = null,
        ?string $rightPosition = 'top'
    ) {
        $this->type = $type;
        $this->label = $label;
        $this->name = $name;
        $this->nameToDotNotation = str_replace(['[', ']'], ['.', ''], $this->name);
        $this->text = $text;
        $this->help = $help;
        $this->id = $id ?? $this->nameToDotNotation;
        $this->placeholder = $placeholder;
        $this->icon = $icon;
        $this->value = old($this->nameToDotNotation, $value);
        $this->required = $required;
        $this->autofocus = $autofocus;
        $this->class = $class;
        $this->classLabel = $classLabel;
        $this->rightText = $rightText;
        $this->rightLink = $rightLink;
        $this->rightPosition = $rightPosition;

        if ($this->required) {
            $this->label .= '&nbsp;*';
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.forms.textarea');
    }
}
