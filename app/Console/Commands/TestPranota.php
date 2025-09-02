<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestPranota extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:pranota';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Pranota model loading';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Test table existence
            $this->info('📊 Checking database tables...');

            $tables = DB::select('SHOW TABLES');
            $tableNames = array_map(function($table) {
                return array_values((array)$table)[0];
            }, $tables);

            $this->info('Found tables: ' . implode(', ', $tableNames));

            if (in_array('pranotalist', $tableNames)) {
                $this->info('✅ Table pranotalist exists!');
            } else {
                $this->error('❌ Table pranotalist not found!');
                return Command::FAILURE;
            }

            // Test model loading
            $pranota = new \App\Models\Pranota();
            $this->info('✅ Pranota model loaded successfully!');

            // Check table name setting
            $this->info('📋 Model table setting: ' . $pranota->getTable());

            // Test raw query first
            $raw_count = DB::select('SELECT COUNT(*) as count FROM pranotalist')[0]->count;
            $this->info("📊 Raw query count from pranotalist: {$raw_count}");

            // Test database connection with explicit table
            $count = \App\Models\Pranota::count();
            $this->info("📊 Pranota count: {$count}");

            $tagihan_count = \App\Models\DaftarTagihanKontainerSewa::count();
            $this->info("📊 Tagihan count: {$tagihan_count}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('❌ File: ' . $e->getFile() . ':' . $e->getLine());
            return Command::FAILURE;
        }
    }
}
