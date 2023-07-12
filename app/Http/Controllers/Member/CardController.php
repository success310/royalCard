<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Card\CardService;
use App\Services\Card\RewardService;
use App\Services\Card\AnalyticsService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Class CardController
 *
 * Controller for handling member card-related actions.
 */
class CardController extends Controller
{
    /**
     * Display the card index view.
     *
     * @param string $locale The locale code
     * @param int $card_id The card ID
     * @param Request $request The HTTP request instance
     * @param CardService $cardService The card service instance
     *
     * @return \Illuminate\View\View
     */
    public function showCard(string $locale, int $card_id, Request $request, CardService $cardService, AnalyticsService $analyticsService)
    {
        $card = $cardService->findActiveCard($card_id);

        if($card) {
            $cardViewsIncremented = $analyticsService->incrementViews($card);
        }

        return $card ? view('member.card.index', compact('card')) : view('member.card.card-404');
    }

    /**
     * Display the card reward view.
     *
     * @param string $locale The locale code
     * @param int $card_id The card ID
     * @param int $reward_id The reward ID
     * @param Request $request The HTTP request instance
     * @param CardService $cardService The card service instance
     *
     * @return \Illuminate\View\View
     */
    public function showReward(string $locale, int $card_id, int $reward_id, Request $request, CardService $cardService, RewardService $rewardService, AnalyticsService $analyticsService)
    {
        // Card
        $card = $cardService->findActiveCard($card_id);

        if($card) {
            $cardViewsIncremented = $analyticsService->incrementViews($card);
        }

        // Reward
        $reward = $rewardService->findActiveReward($reward_id);

        if($reward) {
            $rewardViewsIncremented = $analyticsService->incrementViews($reward, $card);
        }

        // Balance
        $balance = ($card && $reward && auth('member')->check()) ? $card->getMemberBalance(null) : null;

        return ($card && $reward) ? view('member.card.reward', compact('card', 'reward', 'balance')) : view('member.card.reward-404');
    }

    /**
     * Display the claim reward view.
     *
     * @param string $locale The locale code
     * @param int $card_id The card ID
     * @param int $reward_id The reward ID
     * @param Request $request The HTTP request instance
     * @param CardService $cardService The card service instance
     *
     * @return \Illuminate\View\View
     */
    public function showClaimReward(string $locale, int $card_id, int $reward_id, Request $request, CardService $cardService, RewardService $rewardService, AnalyticsService $analyticsService)
    {
        // Card
        $card = $cardService->findActiveCard($card_id);

        if($card) {
            $cardViewsIncremented = $analyticsService->incrementViews($card);
        }

        // Reward
        $reward = $rewardService->findActiveReward($reward_id);

        if($reward) {
            $rewardViewsIncremented = $analyticsService->incrementViews($reward, $card);
        }

        // Balance
        $balance = ($card && $reward && auth('member')->check()) ? $card->getMemberBalance(null) : null;

        // Claim reward URL
        $claimRewardUrl = ($card && $reward && auth('member')->check()) ? route('staff.claim.reward', ['card_id' => $card->id, 'reward_id' => $reward->id, 'member_identifier' => auth('member')->user()->unique_identifier]) : null;

        return ($card && $reward) ? view('member.card.reward-claim', compact('card', 'reward', 'balance', 'claimRewardUrl')) : view('member.card.reward-404');
    }

    /**
     * Associate the authenticated member with a specified card.
     *
     * @param string $locale The locale code
     * @param int $card_id The ID of the card to be followed
     * @param Request $request The current HTTP request instance
     * @param CardService $cardService The service handling card operations
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function follow(string $locale, int $card_id, Request $request, CardService $cardService)
    {
        // If the member is not authenticated, store the current URL and redirect to the login page
        if (!auth('member')->check()) {
            session()->put('from.member', url()->current());
            return redirect()->route('member.login');
        }

        // Retrieve the active card with the given id
        $card = $cardService->findActiveCard($card_id);

        // If the card does not exist or is not active, show a 404 page
        if ($card === null) {
            return view('member.card.card-404');
        }

        // Follow the card and redirect to the card page with a success message
        $cardService->followCard($card);
        return redirect()->route('member.card', ['card_id' => $card->id])->with('followed', true);
    }

    /**
     * Disassociate the authenticated member from a specified card.
     *
     * @param string $locale The locale code
     * @param int $card_id The ID of the card to be unfollowed
     * @param Request $request The current HTTP request instance
     * @param CardService $cardService The service handling card operations
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function unfollow(string $locale, int $card_id, Request $request, CardService $cardService)
    {
        // If the member is not authenticated, store the current URL and redirect to the login page
        if (!auth('member')->check()) {
            session()->put('from.member', url()->current());
            return redirect()->route('member.login');
        }

        // Retrieve the active card with the given id
        $card = $cardService->findActiveCard($card_id);

        // If the card does not exist or is not active, show a 404 page
        if ($card === null) {
            return view('member.card.card-404');
        }

        // Unfollow the card and redirect to the card page with a success message
        $cardService->unfollowCard($card);
        return redirect()->route('member.card', ['card_id' => $card->id])->with('unfollowed', true);
    }
}