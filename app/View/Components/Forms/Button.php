<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

/**
 * Class Button
 *
 * Represents a button in a form, which may include a back button.
 */
class Button extends Component
{
    public string $type;
    public ?string $label;
    public ?string $class;
    public ?string $buttonClass;
    public bool $back;
    public string $backUrl;
    public ?string $backText;

    /**
     * Button constructor.
     *
     * @param string $type The button type. Default is 'submit'.
     * @param string|null $label The button text. Default is null.
     * @param string|null $class The div container class. Default is null.
     * @param string|null $buttonClass The button class. Default is null.
     * @param bool $back Whether to show the back button. Default is false.
     * @param string $backUrl The back button URL. Default is 'javascript:history.go(-1);'.
     * @param string|null $backText The back button text. Default is null. Uses 'common.back' translation if null.
     */
    public function __construct(
        string $type = 'submit',
        ?string $label = null,
        ?string $class = null,
        ?string $buttonClass = null,
        bool $back = false,
        string $backUrl = 'javascript:history.go(-1);',
        ?string $backText = null
    ) {
        $this->type = $type;
        $this->label = $label;
        $this->class = $class;
        $this->buttonClass = $buttonClass;
        $this->back = $back;
        $this->backUrl = $backUrl;
        $this->backText = $backText ?? trans('common.back');
    }

    /**
     * Get the view or contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(): \Illuminate\Contracts\View\View|\Closure|string
    {
        return view('components.forms.button');
    }
}
