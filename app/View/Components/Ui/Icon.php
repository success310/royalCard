<?php

namespace App\View\Components\Ui;

use Illuminate\View\Component;

class Icon extends Component
{
    public string $icon, $class;

    /**
     * Create a new Icon component instance.
     *
     * @param string $icon The icon name. Default is 'coins'.
     * @param string|null $class The class for the SVG element. Default is null.
     */
    public function __construct(string $icon = 'coins', ?string $class = null)
    {
        $this->icon = $icon;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('components.ui.icon', [
            'icon' => $this->icon,
            'class' => $this->class,
        ]);
    }
}
