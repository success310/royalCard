<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

/**
 * Class FormOpen
 *
 * Represents an open form tag in a view.
 */
class FormOpen extends Component
{
    public ?string $action;
    public ?string $method;
    public ?string $class;

    /**
     * FormOpen constructor.
     *
     * @param string|null $action The form action. Default is null.
     * @param string|null $method The form method. Default is null.
     * @param string|null $class The form class. Default is null.
     */
    public function __construct(
        ?string $action = null,
        ?string $method = null,
        ?string $class = null
    ) {
        $this->action = $action;
        $this->method = $method;
        $this->class = $class;
    }

    /**
     * Get the view or contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(): \Illuminate\Contracts\View\View|\Closure|string
    {
        return view('components.forms.form-open');
    }
}
