<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SuratJalan;
use App\Models\Checkpoint;

class TestUploadGambar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-upload-gambar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test upload gambar functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing upload gambar functionality...');

        // Check if checkpoint-images directory exists
        $path = storage_path('app/public/checkpoint-images');
        if (is_dir($path)) {
            $this->info('✅ Directory checkpoint-images exists: ' . $path);
        } else {
            $this->error('❌ Directory checkpoint-images does not exist');
            return 1;
        }

        // Check if storage is linked
        $publicPath = public_path('storage');
        if (is_link($publicPath) || is_dir($publicPath)) {
            $this->info('✅ Storage link exists');
        } else {
            $this->warn('⚠️  Storage link may not exist. Run: php artisan storage:link');
        }

        // Check database columns
        $this->info('Checking database columns...');

        try {
            // Test checkpoints table
            $checkpoint = new Checkpoint();
            $fillable = $checkpoint->getFillable();
            if (in_array('gambar', $fillable)) {
                $this->info('✅ Checkpoints table has gambar field in fillable');
            } else {
                $this->error('❌ Checkpoints table missing gambar in fillable');
            }

            // Test surat_jalans table
            $suratJalan = new SuratJalan();
            $fillable = $suratJalan->getFillable();
            if (in_array('gambar_checkpoint', $fillable)) {
                $this->info('✅ SuratJalans table has gambar_checkpoint field in fillable');
            } else {
                $this->error('❌ SuratJalans table missing gambar_checkpoint in fillable');
            }

        } catch (\Exception $e) {
            $this->error('Error checking models: ' . $e->getMessage());
            return 1;
        }

        $this->info('✅ Upload gambar functionality ready for testing!');
        $this->info('');
        $this->info('Next steps:');
        $this->info('1. Login as supir user');
        $this->info('2. Go to checkpoint form');
        $this->info('3. Upload a test image');
        $this->info('4. Submit the form');
        $this->info('5. Check if image appears in riwayat');

        return 0;
    }
}
