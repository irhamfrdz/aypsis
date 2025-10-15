<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Karyawan;

class CreateSupirUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-supir-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user for supir JONI for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $karyawan = Karyawan::where('nama_lengkap', 'JONI')->first();

        if (!$karyawan) {
            $this->error('Karyawan JONI not found');
            return 1;
        }

        $this->info("Karyawan JONI found with ID: {$karyawan->id}");

        $user = User::where('karyawan_id', $karyawan->id)->first();

        if (!$user) {
            $this->info('Creating user for karyawan JONI...');
            $user = User::create([
                'username' => 'joni',
                'password' => bcrypt('password'),
                'karyawan_id' => $karyawan->id,
                'status' => 'approved'
            ]);
            $this->info("User created with username: {$user->username}");
        } else {
            $this->info("User already exists with username: {$user->username}");
        }

        $this->info("User name accessor: {$user->name}");
        return 0;
    }
}
