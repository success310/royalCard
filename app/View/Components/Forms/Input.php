<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class Input extends Component
{
    public $type, $label, $name, $nameToDotNotation, $prefix, $suffix, $affixClass, $text, $help, $id, $placeholder, $icon, $value, $required, $autofocus, $generatePassword, $class, $mailPassword, $mailPasswordChecked, $inputClass, $classLabel, $rightText, $rightLink, $rightPosition;

    /**
     * Create a new component instance.
     *
     * @param string $type Input type
     * @param string $label Label text
     * @param string $name Input name
     * @param string $nameToDotNotation Input name with dot notation
     * @param string $prefix Prefix
     * @param string $suffix Suffix
     * @param string $affixClass Class for prefix and suffix
     * @param string $text Text below input
     * @param string $help Tooltip balloon
     * @param string $id Input id
     * @param string $placeholder Input placeholder
     * @param string $icon Input icon
     * @param string $value Input value
     * @param bool $required Required
     * @param bool $autofocus Autofocus
     * @param bool $generatePassword Generate password button
     * @param bool $mailPassword Add a checkbox with the option to send the user their password
     * @param bool $mailPasswordChecked Mail password checkbox checked by default
     * @param string $class Class
     * @param string $inputClass Input element class
     * @param string $classLabel Label class
     * @param string $rightText Optional text/link right aligned in label
     * @param string $rightLink Optional text/link right aligned in label
     * @param string $rightPosition Position right text, 'top' or 'bottom'
     * @return void
     */
    public function __construct(
        $type = 'text',
        $label = null,
        $name = null,
        $nameToDotNotation = null,
        $prefix = null,
        $suffix = null,
        $affixClass = null,
        $text = null,
        $help = null,
        $id = null,
        $placeholder = null,
        $icon = null,
        $value = null,
        $required = false,
        $autofocus = false,
        $generatePassword = false,
        $mailPassword = false,
        $mailPasswordChecked = false,
        $class = null,
        $inputClass = null,
        $classLabel = null,
        $rightText = null,
        $rightLink = null,
        $rightPosition = 'top'
    ) {
        $this->type = $type;
        $this->label = $label;
        $this->name = $name;
        $this->nameToDotNotation = str_replace(']', '', str_replace('[', '.', $this->name));
        $this->prefix = $prefix;
        $this->suffix = $suffix;
        $this->affixClass = $affixClass;
        $this->text = $text;
        $this->help = $help;
        $this->id = $id ?? $this->nameToDotNotation;
        $this->placeholder = $placeholder;
        $this->icon = $icon;
        $this->value = ($type == 'password') ? $value : old($this->nameToDotNotation, $value);
        $this->required = $required;
        $this->autofocus = $autofocus;
        $this->generatePassword = $generatePassword;
        $this->mailPassword = $mailPassword;
        $this->mailPasswordChecked = $mailPasswordChecked;
        $this->class = $class;
        $this->inputClass = $inputClass;
        $this->classLabel = $classLabel;
        $this->rightText = $rightText;
        $this->rightLink = $rightLink;
        $this->rightPosition = $rightPosition;
    
        if ($this->required && $this->label) {
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
        return view('components.forms.input');
    }
}
