<?php

namespace Database\Seeders;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Member::create([
            'name' => 'Member Name',
            'email' => 'member@example.com',
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
        ]);

        for ($i = 0; $i < 6; $i++) {
            $created_at = fake()->dateTimeBetween('-78 week', '-6 week');
            Member::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'password' => bcrypt('welcome123'),
                'role' => 1, // 1 = user
                'email_verified_at' => $created_at,
                'is_active' => true,
                'is_undeletable' => true,
                'is_uneditable' => true,
                'created_at' => $created_at,
                'locale' => config('app.locale'),
                'currency' => config('default.currency'),
                'time_zone' => config('default.time_zone'),
            ]);
        }
    }
}
