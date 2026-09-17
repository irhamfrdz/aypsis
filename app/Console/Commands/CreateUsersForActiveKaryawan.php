<?php

namespace App\Console\Commands;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CreateUsersForActiveKaryawan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-user-karyawan 
                            {--password=Password : Password default untuk akun yang dibuat (default: Password)} 
                            {--dry-run : Menjalankan simulasi tanpa menyimpan perubahan ke database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat akun User untuk karyawan aktif yang belum memiliki akun dengan format username nama panggilan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $defaultPassword = (string) $this->option('password');
        $isDryRun = (bool) $this->option('dry-run');

        $this->info('================================================================');
        $this->info('🚀 PEMBUATAN AKUN USER UNTUK KARYAWAN AKTIF');
        $this->info('================================================================');
        if ($isDryRun) {
            $this->warn('⚠️  MODE SIMULASI (DRY-RUN): Data TIDAK akan disimpan ke database.');
        }

        // Ambil semua karyawan aktif (tanggal_berhenti IS NULL)
        // Dan belum memiliki akun user
        $existingKaryawanIdsWithUser = User::whereNotNull('karyawan_id')->pluck('karyawan_id')->toArray();

        $activeKaryawansWithoutUser = Karyawan::whereNull('tanggal_berhenti')
            ->whereNotIn('id', $existingKaryawanIdsWithUser)
            ->orderBy('id', 'asc')
            ->get();

        $total = $activeKaryawansWithoutUser->count();

        $this->info("📋 Total karyawan aktif yang belum memiliki akun: {$total}");

        if ($total === 0) {
            $this->info('✅ Semua karyawan aktif sudah memiliki akun user.');
            return 0;
        }

        $usedUsernames = [];
        $createdList = [];

        DB::beginTransaction();

        try {
            foreach ($activeKaryawansWithoutUser as $index => $karyawan) {
                // Tentukan nama dasar untuk username
                $sourceName = '';
                $panggilan = trim((string) $karyawan->nama_panggilan);

                // Jika nama panggilan ada dan bukan deretan angka panjang (misal KTP)
                if (!empty($panggilan) && !(is_numeric($panggilan) && strlen($panggilan) > 8)) {
                    $words = preg_split('/\s+/', $panggilan);
                    $sourceName = $words[0] ?? $panggilan;
                }

                // Jika masih kosong / invalid, fallback ke kata pertama nama lengkap
                if (empty($sourceName) && !empty(trim((string) $karyawan->nama_lengkap))) {
                    $words = preg_split('/\s+/', trim($karyawan->nama_lengkap));
                    $sourceName = $words[0] ?? '';
                }

                // Bersihkan karakter non-alphanumeric dan ubah ke lowercase
                $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $sourceName));
                if (empty($baseUsername)) {
                    $baseUsername = 'karyawan' . ($karyawan->nik ?? $karyawan->id);
                }

                // Logika penomoran jika username sudah ada (di database atau di batch saat ini)
                $username = $baseUsername;
                $counter = 1;

                while (
                    in_array($username, $usedUsernames, true) ||
                    User::where('username', $username)->exists()
                ) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }

                $usedUsernames[] = $username;

                if (! $isDryRun) {
                    $userData = [
                        'username' => $username,
                        'password' => Hash::make($defaultPassword),
                        'karyawan_id' => $karyawan->id,
                        'status' => 'approved',
                    ];

                    if (Schema::hasColumn('users', 'role')) {
                        $userData['role'] = 'user';
                    }
                    if (Schema::hasColumn('users', 'is_approved')) {
                        $userData['is_approved'] = true;
                    }
                    if (Schema::hasColumn('users', 'approved_at')) {
                        $userData['approved_at'] = now();
                    }

                    $newUser = User::create($userData);

                    if (Schema::hasColumn('karyawans', 'user_id')) {
                        $karyawan->update(['user_id' => $newUser->id]);
                    }
                }

                $createdList[] = [
                    'No' => $index + 1,
                    'NIK' => $karyawan->nik ?? '-',
                    'Nama Lengkap' => $karyawan->nama_lengkap ?? '-',
                    'Nama Panggilan' => $karyawan->nama_panggilan ?? '-',
                    'Username' => $username,
                    'Password' => $defaultPassword,
                ];
            }

            if (! $isDryRun) {
                DB::commit();
                $this->info("✅ Berhasil membuat {$total} akun user baru!");
            } else {
                DB::rollBack();
                $this->warn("ℹ️ Simulasi selesai. {$total} akun dapat dibuat.");
            }

            $this->table(['No', 'NIK', 'Nama Lengkap', 'Nama Panggilan', 'Username', 'Password'], $createdList);

            return 0;

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("❌ Terjadi kesalahan: {$e->getMessage()}");
            return 1;
        }
    }
}
