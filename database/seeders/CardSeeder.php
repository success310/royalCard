<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\File;
use App\Models\Partner;
use App\Models\Card;
use App\Models\Reward;
use Illuminate\Database\Seeder;
use Faker\Factory;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Locales
        $directory = database_path('data/demo/cards/');
        $files = File::files($directory);
        
        $locales = array_map(function ($file) {
            return pathinfo($file, PATHINFO_FILENAME);
        }, $files);

        // Card content
        $usedKeys = [];

        foreach ($locales as $locale) {
            $jsonFilePath = database_path('data/demo/cards/'.$locale.'.json');
            $jsonString = file_get_contents($jsonFilePath);
            $cards[$locale] = json_decode($jsonString, true);
        }

        // Colors
        $colorValues = [
            '#FF5733', // Vivid orange
            '#C70039', // Vivid red
            '#900C3F', // Vivid purple
            '#581845', // Dark purple
            '#2ECC71', // Vivid green
            '#3498DB', // Vivid blue
            '#1B4F72', // Dark blue
            '#17202A', // Very dark blue-gray
            '#FF8C00', // Dark orange
            '#228B22', // Forest green
            '#800000', // Maroon
            '#4B0082', // Indigo
            '#2F4F4F', // Dark slate gray
            '#8B4513', // Saddle brown
            '#7B68EE', // Medium slate blue
            '#6A5ACD', // Slate blue
            '#483D8B', // Dark slate blue
            '#4682B4', // Steel blue
            '#8B0000', // Dark red
            '#DD4132', // Chili Pepper
            '#9E1030', // Cranberry
            '#FE840E', // Tangerine
            '#ff3333', // Coral
            '#20b082', // Mint
            '#FFD700', // Gold
            '#C0D725', // Lime
            '#00B7C2', // Turquoise
            '#6963FF', // Periwinkle
            '#5F4B8B', // Iris
            '#FFA07A', // Light Salmon
            '#7FFF00', // Chartreuse
            '#D2691E', // Chocolate
            '#8A2BE2', // Blue Violet
            '#00a06a', // Aquamarine
            '#DEB887', // Burlywood
            '#5F9EA0', // Cadet Blue
            '#B8860B', // Dark Goldenrod
            '#556B2F', // Dark Olive Green
            '#FF8C00', // Dark Orange
        ];
        //$colorCounter = array_rand($colorValues);
        $colorCounter = 0;

        // Get the partners from the database
        $partners = Partner::all();

        foreach ($partners as $index => $partner) {
            // Generate empty array
            $numbers = [];
            // Randomly shuffle the array
            shuffle($numbers);

            $visibleByDefault = 0;
            foreach ($partner->clubs as $club) {
                for ($i = 0; $i < 3; $i++) {
                    $created_at = fake()->dateTimeBetween('-32 week', '-6 week');
                    $issue_date = fake()->dateTimeBetween('-12 week', '-1 day');
                    $expiration_date = fake()->dateTimeBetween('-2 week', '+64 week');

                    // Color
                    //$bg_color = $values[array_rand($values)];
                    $bg_color = $colorValues[$colorCounter % count($colorValues)];
                    $colorCounter++;

                    // Numbers
                    $values = [10, 20, 50, 100, 100, 100, 100, 100, 150, 200, 250];
                    $initial_bonus_points = $values[array_rand($values)];

                    $values = [6, 12, 24, 32];
                    $points_expiration_months = $values[array_rand($values)];

                    $values = [1, 5, 10];
                    $currency_unit_amount = $values[array_rand($values)];

                    $values = [5, 10, 50];
                    $points_per_currency = $values[array_rand($values)] * $currency_unit_amount;

                    $values = [1, 10, 50, 100];
                    $min_points_per_purchase = $values[array_rand($values)];

                    $values = [10000, 50000, 100000, 1000000, 1000000, 1000000, 1000000];
                    $max_points_per_purchase = $values[array_rand($values)];

                    //$randomKey = array_rand($cards[$locales[0]]);
                    do {
                        $randomKey = array_rand($cards[$locales[0]]);
                    } while (isset($usedKeys[$randomKey]));
                    $usedKeys[$randomKey] = true;

                    foreach ($locales as $locale) {
                        /*
                        $faker = Factory::create($locale);
                        $head[$locale] = $faker->company();
                        $title[$locale] = $faker->sentence(4);
                        $description[$locale] = $faker->sentence(3);
                        */
                        $head[$locale] = $cards[$locale][$randomKey]['head'];
                        $title[$locale] = $cards[$locale][$randomKey]['title'];
                        $description[$locale] = $cards[$locale][$randomKey]['description'];
                    }

                    $is_active = true;
                    //$is_visible_by_default = (rand(0, 100) < 90) ? false : true;
                    $is_visible_by_default = false;

                    if ($club->name == 'Archived') {
                        $is_visible_by_default = false;
                    } else {
                        if ($visibleByDefault < 3 && $index == 0) {
                            $is_visible_by_default = true;
                            $visibleByDefault++;
                        }
                    }

                    $card = Card::create([
                        'club_id' => $club->id,
                        'name' => $cards['en_US'][$randomKey]['head'], //fake()->sentence(rand(1,3)),
                        'head' => $head,
                        'title' => $title,
                        'description' => $description,
                        'issue_date' => $issue_date,
                        'expiration_date' => $expiration_date,
                        'bg_color' => $bg_color, //fake()->hexcolor(),
                        'bg_color_opacity' => rand(79, 88),
                        'text_color' => '#ffffff',
                        'text_label_color' => '#ffffff',
                        'qr_color_light' => '#ffffff',
                        'qr_color_dark' => $bg_color, //'#333333',
                        'currency' => 'USD',
                        'initial_bonus_points' => $initial_bonus_points,
                        'points_expiration_months' => $points_expiration_months,
                        'currency_unit_amount' => $currency_unit_amount,
                        'points_per_currency' => $points_per_currency,
                        'point_value' => 0,
                        'min_points_per_purchase' => $min_points_per_purchase,
                        'max_points_per_purchase' => $max_points_per_purchase,
                        'min_points_per_redemption' => 0,
                        'max_points_per_redemption' => 0,
                        'is_active' => $is_active,
                        'is_undeletable' => true,
                        'is_uneditable' => true,
                        'is_visible_by_default' => $is_visible_by_default,
                        'is_visible_when_logged_in' => false,
                        'created_at' => $created_at,
                        'created_by' => $partner->id,
                    ]);

                    // Then, when you need a random number:
                    $randomNumber = $this->getUniqueRandomNumber($numbers);

                    $background = storage_path('demo-images/visuals-restaurant/cards/' . $randomNumber . '.jpg');

                    $card
                        ->addMedia($background)
                        ->preservingOriginal()
                        ->sanitizingFileName(function ($fileName) {
                            return strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName));
                        })
                        ->toMediaCollection('background', 'files');

                    // Rewards
                    $rewards = $partner->rewards->random(6);
                    $card->rewards()->attach($rewards);
                }
            }
        }
    }

    // Function to get a unique random number
    public function getUniqueRandomNumber(&$numbers) {
        if (empty($numbers)) {
            // If all numbers have been used, regenerate and reshuffle the array
            $numbers = range(1, 20);
            shuffle($numbers);
        }

        // Return a unique random number
        return array_pop($numbers);
    }
}
