<?php

namespace Database\Seeders;

use App\Models\Partner;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Partner::create([
            'name' => 'Partner Name',
            'email' => 'partner@example.com',
            'password' => bcrypt('welcome123'),
            'role' => 1,
            'email_verified_at' => Carbon::now('UTC'),
            'is_active' => true,
            'is_undeletable' => true,
            'is_uneditable' => true,
            'created_at' => Carbon::now('UTC'),
            'locale' => config('app.locale'),
            'currency' => config('default.currency'),
            'time_zone' => config('default.time_zone'),
        ]);

        // No additional demo partners for now, is confusing
        /*
        for ($i = 0; $i < 14; $i++) {
            $gender = (fake()->boolean) ? 'male' : 'female';
            $created_at = fake()->dateTimeBetween('-78 week', '-6 week');
            $partner = Partner::create([
                'name' => fake()->name($gender),
                'email' => fake()->unique()->safeEmail(),
                'password' => bcrypt('welcome123'),
                'role' => 1,
                'email_verified_at' => $created_at,
                'is_active' => fake()->boolean(90),
                'number_of_times_logged_in' => mt_rand(1, 44),
                'last_login_at' => fake()->dateTimeBetween('-12 week', '-1 day'),
                'is_undeletable' => true,
                'is_uneditable' => true,
                'created_at' => $created_at,
                'locale' => config('app.locale'),
                'currency' => config('default.currency'),
                'time_zone' => config('default.time_zone'),
            ]);

            // Randomly generate an avatar for some of the admins
            if (fake()->boolean(74)) {
                $genderDir = ($gender == 'male') ? 'men' : 'women';
                $avatar = storage_path("demo-images/avatars/$genderDir/".fake()->numberBetween(2, 99).'.jpg');

                $partner
                    ->addMedia($avatar)
                    ->preservingOriginal()
                    ->sanitizingFileName(function ($fileName) {
                        return strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName));
                    })
                    ->toMediaCollection('avatar', 'files');
            }
        }
        */
    }
}
