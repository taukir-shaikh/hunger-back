<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestaurantCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Pizza',
            'Burger',
            'Chinese',
            'North Indian',
            'South Indian',
            'Fast Food',
            'Cafe',
            'Desserts',
            'Beverages',
            'Biryani',
            'Rolls & Wraps',
            'Street Food',
        ];

        foreach ($categories as $name) {
            DB::table('tb_restaurant_categories')->insert([
                'name' => $name,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
