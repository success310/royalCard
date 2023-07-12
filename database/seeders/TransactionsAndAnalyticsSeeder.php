<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Admin;
use App\Models\Partner;
use App\Models\Staff;
use App\Models\Analytic;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Money\Currency;
use Money\Currencies\ISOCurrencies;
use Money\Parser\DecimalMoneyParser;

class TransactionsAndAnalyticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$analyticsService = app(\App\Services\AnalyticsService::class);
        $transactionService = app(\App\Services\Card\TransactionService::class);

        $cards = Card::all();

        foreach ($cards as $card) {
            // Card partner
            $partner = $card->partner;

            // All members are going to interact
            $members = Member::all();

            foreach ($members as $member) {
                // First a member visits a card and rewards
                $startDate = fake()->dateTimeBetween('-120 days', '-120 days')->format('Y-m-d H:i:s');
                $endDate = Carbon::now();

                // Card visits
                $visits = mt_rand(45, 60);
                for($i=0; $i < $visits; $i++) {
                    $interactionDate = fake()->dateTimeBetween($startDate, $endDate)->format('Y-m-d H:i:s');
                    Analytic::create([
                        'partner_id' => $card->partner->id,
                        'member_id' => $member->id,
                        'staff_id' => null,
                        'card_id' => $card->id,
                        'reward_id' => null,
                        'event' => 'card_view',
                        'locale' => $member->locale,
                        'created_at' => $interactionDate,
                    ]);
                    $card->increment('views');
                    $card->where('id', $card->id)->update(['last_view' => Carbon::now('UTC')]);
                }

                // Reward visits
                $visits = mt_rand(65, 85);
                for($i=0; $i < $visits; $i++) {
                    $rewards = $card->rewards()->inRandomOrder()->limit(4)->get();
                    foreach($rewards as $reward) {
                        $interactionDate = fake()->dateTimeBetween($startDate, $endDate)->format('Y-m-d H:i:s');
                        Analytic::create([
                            'partner_id' => $card->partner->id,
                            'member_id' => $member->id,
                            'staff_id' => null,
                            'card_id' => $card->id,
                            'reward_id' => $reward->id,
                            'event' => 'reward_view',
                            'locale' => $member->locale,
                            'created_at' => $interactionDate,
                        ]);
                        $reward->increment('views');
                        $reward->where('id', $reward->id)->update(['last_view' => Carbon::now('UTC')]);
                    }
                }

                // Then a member earns points
                $startDate = fake()->dateTimeBetween('-120 days', '-120 days')->format('Y-m-d H:i:s');
                $endDate = \Carbon\Carbon::now();

                $interactions = mt_rand(15, 30);
                for($i=0; $i < $interactions; $i++) {
                    // Define the range and step
                    $min = 1.5;
                    $max = 200;
                    $step = 1.5;

                    // Calculate the number of steps in the range
                    $minSteps = ceil($min / $step);
                    $maxSteps = floor($max / $step);

                    // Generate a random number of steps within the range
                    $steps = rand($minSteps, $maxSteps);

                    // Calculate the actual random number
                    $purchase_amount = $steps * $step;

                    $interactionDate = ($i == 0) ? $startDate : fake()->dateTimeBetween($startDate, $endDate)->format('Y-m-d H:i:s');

                    $staff = $partner->staff()->inRandomOrder()->first();

                    $transactionService->addPurchase($member->unique_identifier, $card->unique_identifier, $staff, $purchase_amount, null, null, $interactionDate);
                }

                // Finally a member claims some rewards
                $startDate = fake()->dateTimeBetween('-150 days', '-130 days')->format('Y-m-d H:i:s');
                $endDate = \Carbon\Carbon::now();

                $interactions = mt_rand(3, 8);
                for($i=0; $i < $interactions; $i++) {
                    
                    $interactionDate = fake()->dateTimeBetween($startDate, $endDate)->format('Y-m-d H:i:s');

                    $staff = $partner->staff()->inRandomOrder()->first();
                    $reward = $card->rewards()->inRandomOrder()->first();

                    $transactionService->claimReward($card->id, $reward->id, $member->unique_identifier, $staff, null, null, $interactionDate);
                }
            }
        }
    }
}
