<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\Card\CardService;
use App\Services\Card\TransactionService;
use App\Services\Member\MemberService;
use App\Services\I18nService;
use Illuminate\Http\Request;
use App\Http\Requests\Staff\AddPointsRequest;

/**
 * Class EarnController
 * @package App\Http\Controllers\Staff
 *
 * Handles points earning for members, like via QR code scanning.
 */
class EarnController extends Controller
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
    public function showEarnPoints(
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

        // Check if staff has access to card
        if (!auth('staff')->user()->isRelatedToCard($card)) {
            abort(401);
        }

        $currency = $i18nService->getCurrencyDetails($card->currency);

        return view('staff.earn.points', compact('card', 'member', 'currency'));
    }

    /**
     * Process the request of earning points and redirect to the transactions list.
     *
     * This method handles the processing of the 'earn points' request. It uses
     * the provided member and card identifiers along with the form request data 
     * to add a purchase transaction. Once completed, it redirects to the transactions
     * list for the specified member and card.
     *
     * @param string $locale
     * @param string $member_identifier
     * @param string $card_identifier
     * @param AddPointsRequest $request
     * @param TransactionService $transactionService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEarnPoints(
        string $locale,
        string $member_identifier,
        string $card_identifier,
        AddPointsRequest $request,
        TransactionService $transactionService
    ): \Illuminate\Http\RedirectResponse {

        $staffUser = auth('staff')->user();

        // Process the 'add purchase' transaction
        $transaction = $transactionService->addPurchase(
            $member_identifier, 
            $card_identifier, 
            $staffUser, 
            $request->purchase_amount, 
            $request->image, 
            $request->note
        );

        // Redirect to the transactions list with the newly created transaction
        session()->flash('success', trans('common.transaction_added'));

        return redirect()->route('staff.transactions', [
            'member_identifier' => $member_identifier,
            'card_identifier' => $card_identifier
        ]);
    }
}