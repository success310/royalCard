<?php

namespace App\View\Components\Member;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Reward;

class RewardCard extends Component
{
    public ?Reward $reward;

    /**
     * Create a new component instance.
     *
     * @param Reward|null $reward The reward model. Default is null.
     */
    public function __construct(?Reward $reward = null)
    {
        $this->reward = $reward;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.member.reward-card');
    }
}
