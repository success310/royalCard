<?php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Share extends Component
{
    public $url, $text;

    /**
     * Create a new Share component instance.
     *
     * @param string|null $url The URL to share. Default is the current URL.
     * @param string|null $text The text to share. Default is null.
     * @return void
     */
    public function __construct(?string $url = null, ?string $text = null)
    {
        $this->url = $url ?? url()->full();
        $this->text = $text;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.share');
    }
}
