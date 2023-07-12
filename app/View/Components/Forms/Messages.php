<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class Messages extends Component
{
    public ?string $msg;

    /**
     * Create a new component instance.
     *
     * @param string|null $msg The general error message. Default is null.
     * @return void
     */
    public function __construct(?string $msg = null)
    {
        $this->msg = $msg ?? trans('common.form_contains_errors');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.forms.messages');
    }
}
