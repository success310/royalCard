<?php

namespace App\View\Components\Member;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Member;

class MemberCard extends Component
{
    public ?Member $member;

    /**
     * Create a new component instance.
     *
     * @param Member|null $member The member model. Default is null.
     */
    public function __construct(?Member $member = null)
    {
        $this->member = $member;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.member.member-card');
    }
}
