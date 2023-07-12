<?php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    /**
     * Crumbs
     *
     * @var array
     */
    public array $crumbs;

    /**
     * Create a new component instance.
     *
     * @param array|null $crumbs The breadcrumb crumbs. Default is null.
     */
    public function __construct(?array $crumbs = null)
    {
        $this->crumbs = $this->filterCrumbs($crumbs);
    }

    /**
     * Filter the crumbs array to remove null text entries and replace them with icon components.
     *
     * @param array|null $crumbs The breadcrumb crumbs.
     * @return array The filtered crumbs array.
     */
    private function filterCrumbs(?array $crumbs): array
    {
        $filteredCrumbs = [];
        foreach ($crumbs as $crumb) {
            if (isset($crumb['text']) && $crumb['text'] !== null) {
                $filteredCrumbs[] = $crumb;
            } elseif (isset($crumb['icon']) && $crumb['icon'] !== null) {
                $iconComponent = new Icon($crumb['icon'], 'w-5 h-5');
                $crumb['text'] = $iconComponent->render()->render();
                $filteredCrumbs[] = $crumb;
            }
        }
        return $filteredCrumbs;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.breadcrumb');
    }
}
