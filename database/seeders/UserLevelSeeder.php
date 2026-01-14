<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tb_user_levels')->truncate();
        DB::table('tb_user_levels')->insert([
            [
                'level_code' => 'USER',
                'level_name' => 'Normal User',
                'description' => 'Standard user with access to food ordering',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level_code' => 'DELIVERY',
                'level_name' => 'Delivery Partner',
                'description' => 'User responsible for delivering orders',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level_code' => 'ADMIN',
                'level_name' => 'Administrator',
                'description' => 'Full access to admin panel and management',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        $this->command->info('Default user levels inserted successfully.');
    }
}
