<?php

namespace App\View\Components\Member;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Card;
use App\Models\Member;
use App\Services\Card\TransactionService;

class History extends Component
{
    /**
     * @var Card|null $card - The card model.
     * @var Member|null $member - The member model. If not provided, defaults to the currently authenticated member.
     * @var Transaction[] $transactions - The list of transactions associated with the member and card.
     * @var bool|null $showNotes - Show transaction notes.
     * @var bool|null $showAttachments - Show transaction attachments.
     * @var bool|null $showStaff - Show staff member.
     * @var bool|null $showExpiredAndUsedTransactions - Show transactions with points that have expired or have been fully used.
     */
    public 
        $card,
        $member,
        $transactions,
        $showNotes,
        $showAttachments,
        $showStaff,
        $showExpiredAndUsedTransactions;

    /**
     * Create a new component instance.
     *
     * @param TransactionService $transactionService
     * @param Card|null $card The card model.
     * @param Member|null $member The member model.
     * @param bool|null $showNotes - Show transaction notes.
     * @param bool|null $showAttachments - Show transaction attachments.
     * @param bool|null $showStaff - Show staff member.
     * @param bool|null $showExpiredAndUsedTransactions - Show transactions with points that have expired or have been fully used.
     */
    public function __construct(
        TransactionService $transactionService,
        Card $card = null,
        Member $member = null,
        bool $showNotes = null,
        bool $showAttachments = null,
        bool $showStaff = null,
        bool $showExpiredAndUsedTransactions = true
    ) {
        $this->card = $card;
        $this->member = $member ?? auth('member')->user();
        $this->showNotes = $showNotes ?? false;
        $this->showAttachments = $showAttachments ?? false;
        $this->showStaff = $showStaff ?? false;
        $this->showExpiredAndUsedTransactions = $showExpiredAndUsedTransactions ?? true;
        if ($this->member) $this->transactions = $transactionService->findTransactionsOfMemberForCard($this->member, $this->card, $this->showExpiredAndUsedTransactions);

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.member.history');
    }
}
