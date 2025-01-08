<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Faker\Factory as Faker;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            Product::create([
                'name' => $faker->word,
                'description' => $faker->sentence,
                'quantity' => $faker->numberBetween(1, 100),
                'price' => $faker->numberBetween(10, 100) * 100,
            ]);
        }
    }
}
