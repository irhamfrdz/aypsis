<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CheckUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info("Checking users table structure...");
        
        $columns = DB::select('DESCRIBE users');
        foreach($columns as $col) {
            $this->command->info("- {$col->Field} ({$col->Type})");
        }
        
        $userCount = DB::table('users')->count();
        $this->command->info("Current users count: {$userCount}");
        
        if ($userCount > 0) {
            $sample = DB::table('users')->first();
            $this->command->info("Sample user: " . json_encode($sample));
        }
    }
}
