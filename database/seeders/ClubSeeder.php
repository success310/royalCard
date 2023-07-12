<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Partner;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the partners from the database
        $partners = Partner::all();

        foreach ($partners as $partner) {
            Club::create([
                'name' => 'General',
                'is_active' => true,
                'is_undeletable' => true,
                'is_uneditable' => true,
                'created_at' => Carbon::now('UTC'),
                'created_by' => $partner->id,
            ]);
/*
            Club::create([
                'name' => 'Seasonal',
                'is_active' => true,
                'is_undeletable' => true,
                'is_uneditable' => true,
                'created_at' => Carbon::now('UTC'),
                'created_by' => $partner->id,
            ]);
*/
        }
    }
}
