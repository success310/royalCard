<?php

namespace App\View\Components\Member;

use Illuminate\View\Component;
use App\Models\Card;
use App\Models\Reward;

/**
 * Rewards Component
 *
 * This component is used to represent the member's reward information.
 * It holds references to the member's card and reward models.
 */
class Rewards extends Component
{
    /**
     * @var Card|null $card This variable holds the instance of a Card model.
     * @var Reward|null $currentReward This variable holds the instance of the Reward model that is currently visible on the page.
     * @var bool $showClaimable Hint if a member has enough points to claim a reward.
     */
    public $card, $currentReward, $showClaimable;

    /**
     * Create a new component instance.
     *
     * This constructor initializes the card and reward models.
     *
     * @param Card|null $card The card model.
     * @param Reward|null $currentReward The currently active reward model.
     * @param bool $showClaimable Hint if a member has enough points to claim a reward.
     */
    public function __construct(
        Card $card = null,
        Reward $currentReward = null,
        bool $showClaimable = false
    ) {
        $this->card = $card;
        $this->currentReward = $currentReward;
        $this->showClaimable = $showClaimable;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * This method renders the view for this component.
     * The view is located at 'components.member.rewards'.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.member.rewards');
    }
}
