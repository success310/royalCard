<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            //IsoSeeder::class,
            AdminSeeder::class,
        ]);

        if (config('default.app_demo')) {
            $this->call([
                PartnerSeeder::class,
                ClubSeeder::class,
                RewardSeeder::class,
                CardSeeder::class,
            ]);
        }

        $this->call([
            NetworkSeeder::class,
        ]);

        if (config('default.app_demo')) {
            $this->call([
                StaffSeeder::class,
                MemberSeeder::class,
                TransactionsAndAnalyticsSeeder::class,
            ]);
        }
    }
}
