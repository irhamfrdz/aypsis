<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Penerima;

class TruncatePenerimasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncation even if referenced
        Schema::disableForeignKeyConstraints();

        // Truncate the table
        Penerima::truncate();

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        $this->command->info('Penerimas table truncated successfully.');
    }
}
