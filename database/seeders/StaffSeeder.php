<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\Partner;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This function seeds the 'staff' table with test data.
     * For each partner, two staff members are created.
     * 
     * @return void
     */
    public function run()
    {
        // Get a faker instance
        $faker = Faker::create();

        // Get all partners
        $partners = Partner::all();

        // Create two staff members for each partner
        $count = 0;
        foreach ($partners as $partner) {
            for ($i = 0; $i < 2; $i++) {
                if ($count == 0) {
                    Staff::create([
                        'club_id' => $partner->clubs->first()->id,
                        'name' => 'Staff Member Name',
                        'email' => 'staff@example.com',
                        'password' => bcrypt('welcome123'),
                        'role' => 1, // 1 = user
                        'email_verified_at' => Carbon::now('UTC'),
                        'is_active' => true,
                        'is_undeletable' => true,
                        'is_uneditable' => true,
                        'created_at' => Carbon::now('UTC'),
                        'locale' => config('app.locale'),
                        'currency' => config('default.currency'),
                        'time_zone' => config('default.time_zone'),
                        'created_by' => $partner->id,
                    ]);
                } else {
                    Staff::create([
                        'club_id' => $partner->clubs->first()->id,
                        'name' => $faker->name(),
                        'email' => $faker->unique()->safeEmail(),
                        'password' => bcrypt('welcome123'),
                        'role' => 1, // 1 = user
                        'email_verified_at' => Carbon::now(),
                        'is_active' => true,
                        'is_undeletable' => true,
                        'is_uneditable' => true,
                        'locale' => config('app.locale'),
                        'currency' => config('default.currency'),
                        'time_zone' => config('default.time_zone'),
                        'created_by' => $partner->id,
                    ]);
                }
                $count++;
            }
        }
    }
}