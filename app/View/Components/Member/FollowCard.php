<?php

namespace App\View\Components\Member;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Card;

class FollowCard extends Component
{
    /**
     * @var Card|null $card Card instance. This variable holds the instance of a Card model.
     * @var bool $follows Does logged in member follow this card.
     * @var bool $canFollow Can a member follow this card.
     */
    public 
        $card,
        $follows,
        $canFollow;

    /**
     * Create a new component instance.
     *
     * @param Card|null $card The card model.
     * @param  bool  $follows
     * 
     */
    public function __construct(
        Card $card = null,
        bool $follows = false
    ) {
        $this->card = $card;
        $this->follows = $follows;
        $this->canFollow = ($card->is_visible_by_default) ? false : true;

        if (auth('member')->check() && $card) {
            if ($card->getMemberBalance(auth('member')->user()) > 0) $this->canFollow = false;
            $this->follows = ($card->members()->where('members.id', auth('member')->user()->id)->exists()) ? true : false;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.member.follow-card');
    }
}
