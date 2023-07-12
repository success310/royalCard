<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\Card\CardService;
use App\Services\Member\MemberService;
use App\Services\I18nService;
use Illuminate\Http\Request;

/**
 * Class TransactionController
 * @package App\Http\Controllers\Staff
 *
 * Handles transactions for members.
 */
class TransactionController extends Controller
{
    /**
     * Display the form to earn points.
     *
     * @param string $locale
     * @param string $member_identifier
     * @param string $card_identifier
     * @param Request $request
     * @param MemberService $memberService
     * @param CardService $cardService
     * @param I18nService $i18nService
     * @return \Illuminate\View\View
     */
    public function showTransactions(
        string $locale,
        string $member_identifier,
        string $card_identifier,
        Request $request,
        MemberService $memberService,
        CardService $cardService,
        I18nService $i18nService
    ): \Illuminate\View\View {
        $member = $memberService->findActiveByIdentifier($member_identifier);
        $card = $cardService->findActiveCardByIdentifier($card_identifier);
        if (!$card) abort(404);

        $currency = $i18nService->getCurrencyDetails($card->currency);

        return view('staff.transactions.transactions', compact('card', 'member', 'currency'));
    }
}
