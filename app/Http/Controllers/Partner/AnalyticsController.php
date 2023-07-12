<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Services\Card\CardService;
use App\Services\Card\AnalyticsService;
use Illuminate\Http\Request;

/**
 * Class AnalyticsController
 * @package App\Http\Controllers\Partner
 *
 * Handles transactions for members.
 */
class AnalyticsController extends Controller
{
    /**
     * Handles the request to display the analytics page.
     *
     * This method retrieves the analytics data, applies sorting and filtering
     * options from the request or from cookies, and returns the analytics view 
     * with attached cookies for the sorting and active_only filter.
     *
     * @param Request $request
     * @param CardService $cardService
     * @return \Illuminate\Http\Response
     */
    public function showAnalytics(Request $request, CardService $cardService): \Illuminate\Http\Response {
    
        // Define the allowed values for the sort parameter
        $allowedSortValues = [
            'views,desc',
            'views,asc',
            'last_view,desc',
            'last_view,asc',
            'total_amount_purchased,desc',
            'total_amount_purchased,asc',
            'number_of_points_issued,desc',
            'number_of_points_issued,asc',
            'number_of_rewards_redeemed,desc',
            'number_of_rewards_redeemed,asc',
            'last_points_issued_at,desc',
            'last_points_issued_at,asc',
            'last_reward_redeemed_at,desc',
            'last_reward_redeemed_at,asc'
        ];

        // Extract query parameters or get from cookies if they exist
        $sort = $request->query('sort', $request->cookie('sort', 'views,desc'));
        $active_only = $request->query('active_only', $request->cookie('active_only', 'true'));

        // Validate the 'sort' query parameter and reset it to default if it's not in the allowed sort values
        if (!in_array($sort, $allowedSortValues)) {
            $sort = 'views,desc';
        }

        // Validate the 'active_only' query parameter and reset it to default if it's not 'true' or 'false'
        if (!in_array($active_only, ['true', 'false'])) {
            $active_only = 'true';
        }

        // Convert active_only to a boolean
        $active_only = filter_var($active_only, FILTER_VALIDATE_BOOLEAN);

        // Extract the column and direction from the sort value
        [$column, $direction] = explode(',', $sort);

        // Retrieve cards from the authenticated partner
        $partnerId = auth('partner')->user()->id;
        $cards = $cardService->findCardsFromPartner($partnerId, $column, $direction, $active_only ? 'is_active' : null, $active_only);

        // Prepare view
        $view = view('partner.analytics.analytics', compact('cards', 'sort', 'active_only'));

        // Convert boolean values back to strings for the cookie
        $active_only = $active_only ? 'true' : 'false';

        // Create cookies for sort and active_only
        $sortCookie = cookie('sort', $sort, 6 * 24 * 30);
        $activeOnlyCookie = cookie('active_only', $active_only, 6 * 24 * 30);

        // Attach cookies to the response and return it
        return response($view)->withCookie($sortCookie)->withCookie($activeOnlyCookie);
    }

    /**
     * Display the analytics of a card.
     *
     * @param string $locale
     * @param int $card_id
     * @param Request $request
     * @param AnalyticsService $analyticsService
     * @param CardService $cardService
     * @return \Illuminate\Http\Response
     */
    public function showCardAnalytics(
        string $locale,
        int $card_id,
        Request $request,
        AnalyticsService $analyticsService,
        CardService $cardService
    ): \Illuminate\Http\Response {
        // Extract range from query parameters or get from cookies if exists, default to 'week'
        $range = $request->query('range', $request->cookie('range', 'week'));
        $offset = 0;

        // Check if $range contains a comma
        if (strpos($range, ',') !== false) {
            list($rangePeriod, $offset) = explode(',', $range);
        } else {
            $rangePeriod = $range;
        }

        if (! is_numeric($offset) || $offset > 1) $offset = 1;

        // Subtract offset from current date
        switch ($rangePeriod) {
            case 'day': 
                $currentDate = date('Y-m-d');
                break;
            case 'week': 
                $currentDate = date('Y-m-d');
                break;
            case 'month': 
                $currentDate = date('Y-m-01');
                break;
            default: 
                $currentDate = date('Y-01-01');
        }

        $date = new \DateTime($currentDate);
        $date->modify("$offset $rangePeriod");
        $date = $date->format('Y-m-d');

        $datePreviousPeriod = new \DateTime($currentDate);
        $datePreviousPeriod->modify(($offset-1) . " $rangePeriod");
        $datePreviousPeriod = $datePreviousPeriod->format('Y-m-d');

        // Find the card using the card service
        $card = $cardService->findCard($card_id);

        if(!$card) {
            abort(404);
        }

        // Determine which analytics to get based on the range
        switch ($rangePeriod) {
            case 'day': 
                $cardViews = $analyticsService->cardViewsDay($card->id, $date);
                $rewardViews = $analyticsService->rewardViewsDay($card->id, $date);
                $pointsIssued = $analyticsService->pointsIssuedDay($card->id, $date);
                $rewardsClaimed = $analyticsService->rewardsClaimedDay($card->id, $date);

                // Get previous period to compare % increase/decrease
                $cardViewsPreviousPeriod = $analyticsService->cardViewsDay($card->id, $datePreviousPeriod);
                $rewardViewsPreviousPeriod = $analyticsService->rewardViewsDay($card->id, $datePreviousPeriod);
                $pointsIssuedPreviousPeriod = $analyticsService->pointsIssuedDay($card->id, $datePreviousPeriod);
                $rewardsClaimedPreviousPeriod = $analyticsService->rewardsClaimedDay($card->id, $datePreviousPeriod);
                break;
            case 'week': 
                $cardViews = $analyticsService->cardViewsWeek($card->id, $date);
                $rewardViews = $analyticsService->rewardViewsWeek($card->id, $date);
                $pointsIssued = $analyticsService->pointsIssuedWeek($card->id, $date);
                $rewardsClaimed = $analyticsService->rewardsClaimedWeek($card->id, $date);

                // Get previous period to compare % increase/decrease
                $cardViewsPreviousPeriod = $analyticsService->cardViewsWeek($card->id, $datePreviousPeriod);
                $rewardViewsPreviousPeriod = $analyticsService->rewardViewsWeek($card->id, $datePreviousPeriod);
                $pointsIssuedPreviousPeriod = $analyticsService->pointsIssuedWeek($card->id, $datePreviousPeriod);
                $rewardsClaimedPreviousPeriod = $analyticsService->rewardsClaimedWeek($card->id, $datePreviousPeriod);
                break;
            case 'month': 
                $cardViews = $analyticsService->cardViewsMonth($card->id, $date);
                $rewardViews = $analyticsService->rewardViewsMonth($card->id, $date);
                $pointsIssued = $analyticsService->pointsIssuedMonth($card->id, $date);
                $rewardsClaimed = $analyticsService->rewardsClaimedMonth($card->id, $date);

                // Get previous period to compare % increase/decrease
                $cardViewsPreviousPeriod = $analyticsService->cardViewsMonth($card->id, $datePreviousPeriod);
                $rewardViewsPreviousPeriod = $analyticsService->rewardViewsMonth($card->id, $datePreviousPeriod);
                $pointsIssuedPreviousPeriod = $analyticsService->pointsIssuedMonth($card->id, $datePreviousPeriod);
                $rewardsClaimedPreviousPeriod = $analyticsService->rewardsClaimedMonth($card->id, $datePreviousPeriod);
                break;
            default: 
                $cardViews = $analyticsService->cardViewsYear($card->id, $date);
                $rewardViews = $analyticsService->rewardViewsYear($card->id, $date);
                $pointsIssued = $analyticsService->pointsIssuedYear($card->id, $date);
                $rewardsClaimed = $analyticsService->rewardsClaimedYear($card->id, $date);

                // Get previous period to compare % increase/decrease
                $cardViewsPreviousPeriod = $analyticsService->cardViewsYear($card->id, $datePreviousPeriod);
                $rewardViewsPreviousPeriod = $analyticsService->rewardViewsYear($card->id, $datePreviousPeriod);
                $pointsIssuedPreviousPeriod = $analyticsService->pointsIssuedYear($card->id, $datePreviousPeriod);
                $rewardsClaimedPreviousPeriod = $analyticsService->rewardsClaimedYear($card->id, $datePreviousPeriod);
        }

        // Calculate % difference compared to previous period
        $cardViewsTotal = $cardViews['total'];
        $rewardViewsTotal = $rewardViews['total'];
        $pointsIssuedTotal = $pointsIssued['total'];
        $rewardsClaimedTotal = $rewardsClaimed['total'];

        $cardViewsPreviousPeriodTotal = $cardViewsPreviousPeriod['total'];
        $rewardViewsPreviousPeriodTotal = $rewardViewsPreviousPeriod['total'];
        $pointsIssuedPreviousPeriodTotal = $pointsIssuedPreviousPeriod['total'];
        $rewardsClaimedPreviousPeriodTotal = $rewardsClaimedPreviousPeriod['total'];

        $cardViewsDifference = $cardViewsPreviousPeriodTotal != 0 
            ? number_format((($cardViewsTotal - $cardViewsPreviousPeriodTotal) / $cardViewsPreviousPeriodTotal) * 100, 0) 
            : ($cardViewsTotal > 0 ? '100' : '-');

        $rewardViewsDifference = $rewardViewsPreviousPeriodTotal != 0 
            ? number_format((($rewardViewsTotal - $rewardViewsPreviousPeriodTotal) / $rewardViewsPreviousPeriodTotal) * 100, 0) 
            : ($rewardViewsTotal > 0 ? '100' : '-');

        $pointsIssuedDifference = $pointsIssuedPreviousPeriodTotal != 0 
            ? number_format((($pointsIssuedTotal - $pointsIssuedPreviousPeriodTotal) / $pointsIssuedPreviousPeriodTotal) * 100, 0) 
            : ($pointsIssuedTotal > 0 ? '100' : '-');

        $rewardsClaimedDifference = $rewardsClaimedPreviousPeriodTotal != 0 
            ? number_format((($rewardsClaimedTotal - $rewardsClaimedPreviousPeriodTotal) / $rewardsClaimedPreviousPeriodTotal) * 100, 0) 
            : ($rewardsClaimedTotal > 0 ? '100' : '-');

        $resultsFound = ($cardViewsTotal == 0 && $rewardViewsTotal == 0 && $pointsIssuedTotal == 0 && $rewardsClaimedTotal == 0) ? false : true;

        // Prepare view
        $view = view('partner.analytics.card', compact('card', 'range', 'cardViews', 'cardViewsDifference', 'rewardViews', 'rewardViewsDifference', 'pointsIssued', 'pointsIssuedDifference', 'rewardsClaimed', 'rewardsClaimedDifference', 'resultsFound'));

        // Set the 'range' cookie to store the range preference
        $rangeCookie = cookie('range', $range, 6 * 24 * 30);

        // Attach cookies to the response and return it
        return response($view)->withCookie($rangeCookie);
    }
}
