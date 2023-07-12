<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\Card\CardService;
use App\Services\Card\RewardService;
use App\Services\Card\TransactionService;
use App\Services\Member\MemberService;
use App\Services\I18nService;
use Illuminate\Http\Request;
use App\Http\Requests\Staff\ClaimRewardRequest;

/**
 * Class RewardController
 * @package App\Http\Controllers\Staff
 *
 * Handles rewards for members.
 */
class RewardController extends Controller
{
    /**
     * Display the form to claim reward.
     *
     * @param string $locale
     * @param string $member_identifier
     * @param int $card_id
     * @param int $reward_id
     * @param Request $request
     * @param CardService $cardService
     * @param RewardService $rewardService
     * @param MemberService $memberService
     * @param I18nService $i18nService
     * @return \Illuminate\View\View
     */
    public function showClaimReward(
        string $locale,
        string $member_identifier,
        int $card_id,
        int $reward_id,
        Request $request,
        CardService $cardService,
        RewardService $rewardService,
        MemberService $memberService,
        I18nService $i18nService
    ): \Illuminate\View\View {
        $card = $cardService->findActiveCard($card_id);
        if (!$card) abort(404);

        $reward = $rewardService->findActiveReward($reward_id);
        $member = $memberService->findActiveByIdentifier($member_identifier);
        $currency = $i18nService->getCurrencyDetails($card->currency);

        // Check if staff has access to card
        if (!auth('staff')->user()->isRelatedToCard($card)) {
            abort(401);
        }

        $canRedeem = true;
        if ($card->getMemberBalance($member) < $reward->points) {
            session()->flash('warning', trans('common.member_not_enough_points_for_reward'));
            $canRedeem = false;
        }

        return view('staff.claim.reward', compact('card', 'reward', 'member', 'currency', 'canRedeem'));
    }

    /**
     * Process the request of claiming a reward and redirect to the transactions list.
     *
     * @param string $locale
     * @param string $member_identifier
     * @param int $card_id
     * @param int $reward_id
     * @param ClaimRewardRequest $request
     * @param CardService $cardService
     * @param TransactionService $transactionService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postClaimReward(
        string $locale,
        string $member_identifier,
        int $card_id,
        int $reward_id,
        ClaimRewardRequest $request,
        CardService $cardService,
        TransactionService $transactionService
    ): \Illuminate\Http\RedirectResponse {
        $card = $cardService->findActiveCard($card_id);
        if (!$card) abort(404);

        $staffUser = auth('staff')->user();

        // Process the 'claim reward' transaction
        $transaction = $transactionService->claimReward(
            $card_id, 
            $reward_id, 
            $member_identifier, 
            $staffUser,  
            $request->image, 
            $request->note
        );

        // Redirect to the transactions list with the newly created transaction
        session()->flash('success', trans('common.reward_claimed'));

        return redirect()->route('staff.transactions', [
            'member_identifier' => $member_identifier,
            'card_identifier' => $card->unique_identifier
        ]);
    }
}