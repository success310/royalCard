<?php

namespace App\Services\Card;

use Illuminate\Database\Eloquent\Collection;
use App\Services\Member\MemberService;
use App\Services\Card\RewardService;
use App\Models\Staff;
use App\Models\Transaction;
use App\Models\Member;
use App\Models\Card;
use Money\Currency;
use Money\Currencies\ISOCurrencies;
use Money\Parser\DecimalMoneyParser;
use Carbon\Carbon;
use App\Notifications\Member\RewardClaimed;
use App\Notifications\Member\PointsReceived;

/**
 * Class TransactionService
 * 
 * Handles operations related to transactions.
 */
class TransactionService
{
    /**
     * @var MemberService
     */
    protected $memberService;

    /**
     * @var CardService
     */
    protected $cardService;

    /**
     * @var RewardService
     */
    protected $rewardService;

    /**
     * @var AnalyticsService
     */
    protected $analyticsService;

    /**
     * TransactionService constructor.
     *
     * @param MemberService $memberService
     * @param CardService $cardService
     * @param RewardService $rewardService
     * @param AnalyticsService $analyticsService
     */
    public function __construct(MemberService $memberService, CardService $cardService, RewardService $rewardService, AnalyticsService $analyticsService)
    {
        $this->memberService = $memberService;
        $this->cardService = $cardService;
        $this->rewardService = $rewardService;
        $this->analyticsService = $analyticsService;
    }

    /**
     * Retrieves transactions of a given member for a specific card.
     *
     * This function fetches all transactions by default. However, 
     * if $showExpiredAndUsedTransactions is set to false, the returned collection will
     * exclude transactions of events 'initial_bonus_points' and 'staff_credited_points_for_purchase' 
     * where points have either expired or have been fully used.
     *
     * @param Member $member The member associated with the transactions.
     * @param Card $card The card associated with the transactions.
     * @param bool $showExpiredAndUsedTransactions Determines whether to include transactions 
     * where points have expired or been fully used. Default is true.
     *
     * @return Collection The collection of relevant Transaction instances.
     */
    public function findTransactionsOfMemberForCard(
        Member $member,
        Card $card,
        bool $showExpiredAndUsedTransactions = true
    ): Collection {
        // Define the query to retrieve all transactions for the given member and card.
        $query = Transaction::where('member_id', $member->id)
                            ->where('card_id', $card->id)
                            ->orderBy('created_at', 'desc');

        // If expired and fully used transactions should be excluded, adjust the query.
        if ($showExpiredAndUsedTransactions == false) {
            $query->where(function ($query) use($member, $card) {
                // Select transactions where the expiry date is in the future 
                // and not all points have been used.
                $query->where('expires_at', '>=', Carbon::now())
                    ->whereColumn('points', '>', 'points_used')
                    ->where('member_id', $member->id)
                    ->where('card_id', $card->id);
                // Only apply the above conditions to these specific event types.
            })->whereIn('event', ['initial_bonus_points', 'staff_credited_points_for_purchase'])
            ->orWhereNotIn('event', ['initial_bonus_points', 'staff_credited_points_for_purchase'])
            ->where('member_id', $member->id)
            ->where('card_id', $card->id);
        }

        // Execute the query and return the collection of transactions.
        return $query->get();
    }

    /**
     * Adds a purchase to the system, creating a new Transaction record.
     *
     * @param string $member_identifier
     * @param string $card_identifier
     * @param Staff $staff
     * @param float $purchase_amount
     * @param string|null $image
     * @param string|null $note
     * @param string|null $created_at
     * 
     * @return Transaction
     */
    public function addPurchase(
        string $member_identifier, 
        string $card_identifier, 
        Staff $staff, 
        float $purchase_amount, 
        string $image = null, 
        string $note = null, 
        string $created_at = null
    ): Transaction {
        // Fetch member and card details
        $member = $this->memberService->findActiveByIdentifier($member_identifier);
        $card = $this->cardService->findActiveCardByIdentifier($card_identifier);
        $partner = $card->partner;

        // Check if staff has access to card
        if (!$staff->isRelatedToCard($card)) {
            abort(401);
        }

        // Parse $purchase_amount to an integer for database storage
        $currencies = new ISOCurrencies();
        $moneyParser = new DecimalMoneyParser($currencies);
        $purchase_amount_parsed = $moneyParser->parse((string)$purchase_amount, new Currency($card->currency))->getAmount();

        // Calculate points based on $purchase_amount
        $points = $card->calculatePoints($purchase_amount);
        $number_of_points_issued = $points;

        // Set point expiration date
        $expires_at = Carbon::now()->addMonths($card->points_expiration_months)->format('Y-m-d H:i:s');

        // Data for transaction record
        $data = [
            'staff_id' => $staff->id,
            'member_id' => $member->id,
            'card_id' => $card->id,
            'partner_name' => $partner->name,
            'partner_email' => $partner->email,
            'staff_name' => $staff->name,
            'staff_email' => $staff->email,
            'card_title' => $card->getTranslations('head'),
            'currency' => $card->currency,
            'purchase_amount' => $purchase_amount_parsed,
            'points_per_currency' => $card->points_per_currency,
            'min_points_per_purchase' => $card->min_points_per_purchase,
            'max_points_per_purchase' => $card->max_points_per_purchase,
            'expires_at' => $expires_at,
            'created_by' => $partner->id,
            'created_at' => $created_at ?? Carbon::now('UTC'),
            'updated_at' => $created_at ?? Carbon::now('UTC'),
        ];

        // Check if this is first transaction and if bonus points are configured
        if ($card->initial_bonus_points && !Transaction::where('member_id', $member->id)->where('card_id', $card->id)->exists()) {
            $bonusData = array_merge($data, [
                'points' => $card->initial_bonus_points,
                'event' => 'initial_bonus_points'
            ]);
            $transaction = Transaction::create($bonusData);
            $number_of_points_issued += $card->initial_bonus_points;

            sleep(1);
        }

        // Prepare data for new transaction record
        $purchaseData = array_merge($data, [
            'points' => $points,
            'event' => 'staff_credited_points_for_purchase',
            'note' => $note,
        ]);

        // Create a new transaction record
        $transaction = Transaction::create($purchaseData);

        // Attach image if present
        if ($image) {
            $transaction->addMediaFromRequest('image')->toMediaCollection('image');
        }

        // Update stats
        $card->total_amount_purchased += $purchase_amount_parsed;
        $card->number_of_points_issued += $number_of_points_issued;
        $card->last_points_issued_at = Carbon::now('UTC');
        $card->save();

        // Add analytics
        $this->analyticsService->addIssueAnalytic($card, $staff, $member, $points, $card->currency, $purchase_amount_parsed, $created_at);

        // Send mail
        $member->notify(new PointsReceived($member, $points, $card));

        return $transaction;
    }

    /**
     * Redeem points for reward, creating a new Transaction record.
     *
     * @param int $card_id
     * @param int $reward_id
     * @param string $member_identifier
     * @param Staff $staff
     * @param string|null $image
     * @param string|null $note
     * @param string|null $created_at
     * 
     * @return Transaction|bool
     */
    public function claimReward(
        int $card_id, 
        int $reward_id, 
        string $member_identifier, 
        Staff $staff, 
        string $image = null, 
        string $note = null, 
        string $created_at = null
    ): Transaction|bool {
        // Fetch member and card details
        $card = $this->cardService->findActiveCard($card_id);
        $reward = $this->rewardService->findActiveReward($reward_id);
        $member = $this->memberService->findActiveByIdentifier($member_identifier);
        $partner = $card->partner;

        // Check if staff has access to card
        if (!$staff->isRelatedToCard($card)) {
            abort(401);
        }

        if ($card->getMemberBalance($member) < $reward->points) {
            return false;
        }

        /**
         * Updates a member's points balance based on transactions that haven't yet expired. 
         * This method iterates through all valid transactions and credits reward points.
         * Points are used from older transactions first (First-In-First-Out)
         */
        $transactions = Transaction::where('member_id', $member->id)
            ->where('card_id', $card->id)
            ->where('expires_at', '>', Carbon::now())
            ->orderBy('created_at', 'asc')
            ->get();

        $remainingRewardPoints = $reward->points;

        foreach ($transactions as $transaction) {
            $unusedTransactionPoints = $transaction->points - $transaction->points_used;
            
            // Skip the transaction if all points are used or no more reward points left to credit
            if ($unusedTransactionPoints <= 0 || $remainingRewardPoints <= 0) {
                continue;
            }
            
            // Calculate the points to be used from the current transaction
            $pointsToUse = min($remainingRewardPoints, $unusedTransactionPoints);
            
            // Update the transaction's used points and persist the changes
            $transaction->points_used += $pointsToUse;
            $transaction->save();

            // Decrease the remaining reward points
            $remainingRewardPoints -= $pointsToUse;
            
            // Break the loop if all reward points are credited
            if ($remainingRewardPoints <= 0) {
                break;
            }
        }

        // Data for transaction record
        $data = [
            'staff_id' => $staff->id,
            'member_id' => $member->id,
            'card_id' => $card->id,
            'reward_id' => $reward->id,
            'partner_name' => $partner->name,
            'partner_email' => $partner->email,
            'staff_name' => $staff->name,
            'staff_email' => $staff->email,
            'card_title' => $card->getTranslations('head'),
            'reward_title' => $reward->getTranslations('title'),
            'reward_points' => $reward->points,
            'currency' => $card->currency,
            'event' => 'staff_redeemed_points_for_reward',
            'points' => -$reward->points,
            'note' => $note,
            'points_per_currency' => $card->points_per_currency,
            'min_points_per_purchase' => $card->min_points_per_purchase,
            'max_points_per_purchase' => $card->max_points_per_purchase,
            'created_by' => $partner->id,
            'created_at' => $created_at ?? Carbon::now('UTC'),
            'updated_at' => $created_at ?? Carbon::now('UTC'),
        ];

        // Create a new transaction record
        $transaction = Transaction::create($data);

        // Attach image if present
        if ($image) {
            $transaction->addMediaFromRequest('image')->toMediaCollection('image');
        }

        // Update stats
        $card->number_of_points_redeemed += $reward->points;
        $card->number_of_rewards_redeemed += 1;
        $card->last_reward_redeemed_at = Carbon::now('UTC');
        $card->save();

        // Add analytics
        $this->analyticsService->addClaimRewardAnalytic($card, $staff, $member, $reward, $created_at);

        // Send mail
        if (!$created_at) $member->notify(new RewardClaimed($member, $reward->points, $card, $reward));

        return $transaction;
    }
}