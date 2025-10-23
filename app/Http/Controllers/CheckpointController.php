<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use App\Models\Kontainer;
use App\Models\Prospek;
use Illuminate\Http\Request; // Menggunakan Request standar
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckpointController extends Controller
{
    /**
     * Menampilkan form untuk supir memperbarui checkpoint.
     */
    public function create(Permohonan $permohonan)
    {
        // Pastikan user yang login adalah karyawan dengan divisi supir
        $user = Auth::user();
        if (!$user->isSupir()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk supir.');
        }

        // Otorisasi: Pastikan supir yang login adalah yang ditugaskan
        if ($user->karyawan->id !== $permohonan->supir_id) {
            abort(403, 'Anda tidak memiliki akses ke permohonan ini.');
        }

        // Get kegiatan name from master kegiatan if available
        $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $permohonan->kegiatan)
                        ->value('nama_kegiatan') ?? $permohonan->kegiatan;

        $kegiatanLower = strtolower($kegiatanName);
        $isAntarKontainerSewa = (stripos($kegiatanLower, 'antar') !== false &&
                                stripos($kegiatanLower, 'kontainer') !== false &&
                                stripos($kegiatanLower, 'sewa') !== false);

        // Filter kontainer berdasarkan kegiatan dan ukuran
        if ($isAntarKontainerSewa) {
            // Untuk antar kontainer sewa, filter berdasarkan ukuran dan status tersedia
            $kontainerList = Kontainer::where('ukuran', $permohonan->ukuran)
                                    ->where('status', 'Tersedia')
                                    ->orderBy('nomor_seri_gabungan')
                                    ->get();
        } else {
            // Untuk kegiatan lain, ambil kontainer sesuai vendor dan kondisi lainnya
            if (in_array($permohonan->vendor_perusahaan, ['ZONA','DPE','SOC'])) {
                // Untuk vendor ini, filter kontainer approved/tagihan group jika tarik sewa
                $isTarikSewa = (stripos($kegiatanLower, 'tarik') !== false && stripos($kegiatanLower, 'sewa') !== false)
                    || (stripos($kegiatanLower, 'pengambilan') !== false);

                if ($isTarikSewa) {
                    // Untuk tarik kontainer sewa, ambil kontainer dari daftar tagihan yang sedang ongoing
                    $sewaKontainerNumbers = DB::table('daftar_tagihan_kontainer_sewa')
                                              ->where('status', 'ongoing')
                                              ->where('size', $permohonan->ukuran)
                                              ->pluck('nomor_kontainer')
                                              ->toArray();

                    if (!empty($sewaKontainerNumbers)) {
                        $kontainerList = Kontainer::whereIn('nomor_seri_gabungan', $sewaKontainerNumbers)
                                                ->where('ukuran', $permohonan->ukuran)
                                                ->orderBy('nomor_seri_gabungan')
                                                ->get();
                    } else {
                        // Jika tidak ada kontainer dalam tagihan sewa, ambil kontainer yang sedang disewa
                        $kontainerList = Kontainer::where('ukuran', $permohonan->ukuran)
                                                ->whereNotNull('tanggal_masuk_sewa')
                                                ->where(function($query) {
                                                    $query->whereNull('tanggal_selesai_sewa')
                                                          ->orWhere('tanggal_selesai_sewa', '>=', now());
                                                })
                                                ->orderBy('nomor_seri_gabungan')
                                                ->get();
                    }
                } else {
                    $kontainerList = Kontainer::where('ukuran', $permohonan->ukuran)
                                            ->orderBy('nomor_seri_gabungan')
                                            ->get();
                }
            } else {
                // Untuk vendor lain, filter berdasarkan ukuran
                $kontainerList = Kontainer::where('ukuran', $permohonan->ukuran)
                                        ->orderBy('nomor_seri_gabungan')
                                        ->get();
            }
        }

        // Ambil stock kontainer berdasarkan ukuran permohonan (20ft atau 40ft)
        $stockKontainers = \App\Models\StockKontainer::where('ukuran', $permohonan->ukuran)
                                                    ->where('status', '!=', 'inactive')
                                                    ->orderBy('nomor_seri_gabungan')
                                                    ->get();

        return view('supir.checkpoint-create', compact('permohonan', 'kontainerList', 'stockKontainers'));
    }

    /**
     * Menyimpan checkpoint baru.
     */
    public function store(Request $request, Permohonan $permohonan)
    {
        // Pastikan user yang login adalah karyawan dengan divisi supir
        $user = Auth::user();
        if (!$user->isSupir()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk supir.');
        }

        // Otorisasi: Pastikan supir yang login adalah yang ditugaskan
        if ($user->karyawan->id !== $permohonan->supir_id) {
            abort(403, 'Anda tidak memiliki akses ke permohonan ini.');
        }

        // Validasi dasar
        $rules = [
            'surat_jalan_vendor' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'gambar' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:5120', // 5MB
        ];

        // Tambahkan validasi untuk nomor_kontainer hanya jika kontainer belum diinput sebelumnya.
        if ($permohonan->kontainers->isEmpty()) {
            $rules['nomor_kontainer'] = ['required', 'array', 'size:' . $permohonan->jumlah_kontainer];

            // Check if this is a container repair activity or antar sewa activity
            $kegiatanLower = strtolower($permohonan->kegiatan ?? '');
            $isPerbaikanKontainer = (stripos($kegiatanLower, 'perbaikan') !== false && stripos($kegiatanLower, 'kontainer') !== false)
                || (stripos($kegiatanLower, 'repair') !== false && stripos($kegiatanLower, 'container') !== false);
            $isAntarSewa = stripos($kegiatanLower, 'antar') !== false && stripos($kegiatanLower, 'sewa') !== false;

            // Untuk semua kegiatan, supir menginput nomor kontainer sebagai string (nomor_seri_gabungan)
            // Karena form selalu mengirim nomor_seri_gabungan, bukan ID
            $rules['nomor_kontainer.*'] = ['required', 'string', 'distinct'];
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            // Jika nomor kontainer di-submit, proses dan hubungkan ke permohonan
            if ($permohonan->kontainers->isEmpty() && !empty($validated['nomor_kontainer'])) {
                $kontainerIds = [];

                // Resolve kegiatan name untuk deteksi yang akurat
                $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $permohonan->kegiatan)
                               ->value('nama_kegiatan') ?? $permohonan->kegiatan;

                // Determine if this permohonan is a return of sewa containers
                $kegiatanLower = strtolower($kegiatanName ?? ($permohonan->kegiatan ?? ''));
                $isReturnSewa = (stripos($kegiatanLower, 'tarik') !== false && stripos($kegiatanLower, 'sewa') !== false) || ($kegiatanLower === 'pengambilan');
                $isPerbaikanKontainer = (stripos($kegiatanLower, 'perbaikan') !== false && stripos($kegiatanLower, 'kontainer') !== false)
                    || (stripos($kegiatanLower, 'repair') !== false && stripos($kegiatanLower, 'container') !== false);
                $isAntarSewa = stripos($kegiatanLower, 'antar') !== false && stripos($kegiatanLower, 'sewa') !== false;
                $isAntarKontainerPerbaikan = (stripos($kegiatanLower, 'antar') !== false && stripos($kegiatanLower, 'kontainer') !== false && stripos($kegiatanLower, 'perbaikan') !== false);

                // Untuk semua kegiatan, sekarang kita selalu menerima nomor_seri_gabungan (string)
                // Cari atau buat kontainer berdasarkan nomor_seri_gabungan
                foreach ($validated['nomor_kontainer'] as $nomor) {
                    // Preserve the raw input (trimmed) so what supir types is what we store/display.
                    $nomorRaw = trim($nomor);
                    // Also compute a normalized lookup key (uppercase, no spaces) to find existing records
                    $nomorLookup = strtoupper(str_replace(' ', '', $nomorRaw));

                    // Try to find by exact raw stored serial first, then by normalized form
                    $kontainer = Kontainer::firstWhere('nomor_seri_gabungan', $nomorRaw)
                        ?? Kontainer::firstWhere('nomor_seri_gabungan', $nomorLookup);

                    if (!$kontainer) {
                        // For non-special activities, check if this should be allowed
                        if (!$isPerbaikanKontainer && !$isAntarSewa && !in_array($permohonan->vendor_perusahaan, ['ZONA', 'DPE', 'SOC'])) {
                            throw new \Exception("Kontainer {$nomorRaw} tidak ditemukan dalam sistem. Pastikan nomor kontainer sudah terdaftar.");
                        }

                        // Create minimal kontainer record but store the raw input as nomor_seri_gabungan
                        $kontainer = Kontainer::create([
                            'awalan_kontainer' => substr($nomorLookup, 0, 4) ?: 'UNK',
                            'nomor_seri_kontainer' => substr($nomorLookup, 4, 6) ?: '000000',
                            'akhiran_kontainer' => substr($nomorLookup, -1) ?: '0',
                            'nomor_seri_gabungan' => $nomorRaw,
                            'ukuran' => $permohonan->ukuran ?? '20',
                            'tipe_kontainer' => 'DRY',
                            'status' => $isReturnSewa ? 'Tersedia' : 'Digunakan',
                        ]);
                    } else {
                        // Ensure stored serial matches what supir entered
                        if ($kontainer->nomor_seri_gabungan !== $nomorRaw) {
                            $kontainer->nomor_seri_gabungan = $nomorRaw;
                        }
                        if ($isReturnSewa) {
                            // mark returned: set finish date and make available
                            $kontainer->tanggal_selesai_sewa = $request->input('tanggal_checkpoint') ?? now()->format('Y-m-d');
                            $kontainer->status = 'Tersedia';
                        } else {
                            if ($kontainer->status !== 'Tersedia') {
                                throw new \Exception("Kontainer {$nomorRaw} tidak tersedia atau sedang digunakan.");
                            }
                            // tandai akan digunakan
                            $kontainer->status = 'Digunakan';
                        }
                        $kontainer->save();
                    }
                    $kontainerIds[] = $kontainer->id;
                }

                // Hubungkan kontainer ke permohonan
                $permohonan->kontainers()->sync($kontainerIds);

                // Update status stock kontainer berdasarkan jenis kegiatan
                if (!empty($validated['nomor_kontainer'])) {
                    foreach ($validated['nomor_kontainer'] as $nomor) {
                        $nomorRaw = trim($nomor);

                        // Cari stock kontainer berdasarkan nomor seri gabungan
                        $stockKontainer = \App\Models\StockKontainer::where('nomor_seri_gabungan', $nomorRaw)->first();

                        if ($stockKontainer) {
                            if ($isAntarKontainerPerbaikan) {
                                // Untuk antar kontainer perbaikan, status menjadi maintenance
                                $stockKontainer->update(['status' => 'maintenance']);
                                Log::info("Stock kontainer {$nomorRaw} status updated to maintenance for antar kontainer perbaikan");

                            } elseif (stripos($kegiatanLower, 'kembali') !== false && stripos($kegiatanLower, 'perbaikan') !== false) {
                                // Untuk kembali dari perbaikan, status menjadi available
                                $stockKontainer->update(['status' => 'available']);
                                Log::info("Stock kontainer {$nomorRaw} status updated to available from perbaikan");

                            } elseif (stripos($kegiatanLower, 'selesai') !== false && stripos($kegiatanLower, 'perbaikan') !== false) {
                                // Untuk selesai perbaikan, status menjadi available
                                $stockKontainer->update(['status' => 'available']);
                                Log::info("Stock kontainer {$nomorRaw} status updated to available - selesai perbaikan");

                            } elseif ($isReturnSewa) {
                                // Untuk tarik/pengambilan sewa, status menjadi available
                                $stockKontainer->update(['status' => 'available']);
                                Log::info("Stock kontainer {$nomorRaw} status updated to available - return sewa");

                            } elseif (!$isPerbaikanKontainer && !$isAntarSewa) {
                                // Untuk kegiatan lain (selain perbaikan), status menjadi rented
                                $stockKontainer->update(['status' => 'rented']);
                                Log::info("Stock kontainer {$nomorRaw} status updated to rented");
                            }
                        }
                    }
                }
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('gambar')) {
                $image = $request->file('gambar');
                $filename = time() . '_checkpoint_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('file_surat_jalan', $filename, 'public');
            }

            // Simpan data checkpoint
            $permohonan->checkpoints()->create([
                'lokasi' => $permohonan->tujuan, // Mengisi lokasi dengan tujuan permohonan
                'catatan' => $validated['catatan'] ?? 'Checkpoint dibuat oleh supir.',
                'surat_jalan_vendor' => $validated['surat_jalan_vendor'] ?? null,
                'tanggal_checkpoint' => $request->input('tanggal_checkpoint') ?? now()->format('Y-m-d'),
                'gambar' => $imagePath,
            ]);

            // Update permohonan quick-access fields for driver checkpoint and supir (if not already set)
            try {
                $checkpointDate = $request->input('tanggal_checkpoint') ?? now()->format('Y-m-d');
                // store as date string on permohonan for fast access by Tagihan aggregates
                $permohonan->tanggal_checkpoint_supir = $checkpointDate;
                // Ensure supir_id is set to the currently authenticated karyawan if missing
                if (empty($permohonan->supir_id) && Auth::user()->karyawan?->id) {
                    $permohonan->supir_id = Auth::user()->karyawan->id;
                }
                $permohonan->save();
            } catch (\Exception $e) {
                // Non-fatal: don't block on failing to save quick-access fields
                Log::warning('Failed to update permohonan quick-access checkpoint fields: ' . $e->getMessage());
            }

            // Update status permohonan menjadi 'Proses'
            $permohonan->status = 'Proses';
            $permohonan->save();

            DB::commit();
            return redirect()->route('supir.checkpoint.create', $permohonan)->with('success', 'Checkpoint berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan form checkpoint untuk surat jalan.
     */
    public function createSuratJalan(\App\Models\SuratJalan $suratJalan)
    {
        // Pastikan user yang login adalah karyawan dengan divisi supir
        $user = Auth::user();
        if (!$user->isSupir()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk supir.');
        }

        // Otorisasi: Pastikan supir yang login adalah yang ditugaskan untuk surat jalan ini
        // Check multiple possible name formats to ensure access
        $userNamaLengkap = $user->karyawan->nama_lengkap ?? $user->username;
        $userNama = $user->karyawan->nama ?? $user->username;
        $userName = $user->name; // This uses the accessor we created

        if ($userNamaLengkap !== $suratJalan->supir &&
            $userNama !== $suratJalan->supir &&
            $userName !== $suratJalan->supir &&
            $user->username !== $suratJalan->supir) {
            abort(403, 'Anda tidak memiliki akses ke surat jalan ini. User: ' . $userName . ', Surat Jalan Supir: ' . $suratJalan->supir);
        }

        // Get kegiatan name from master kegiatan if available
        $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $suratJalan->kegiatan)
                        ->value('nama_kegiatan') ?? $suratJalan->kegiatan;

        $kegiatanLower = strtolower($kegiatanName);
        $isAntarKontainerSewa = (stripos($kegiatanLower, 'antar') !== false &&
                                stripos($kegiatanLower, 'kontainer') !== false &&
                                stripos($kegiatanLower, 'sewa') !== false);

        // Filter kontainer berdasarkan kegiatan dan ukuran
        if ($isAntarKontainerSewa) {
            // Untuk antar kontainer sewa, filter berdasarkan ukuran dan status tersedia
            $kontainerList = Kontainer::where('ukuran', $suratJalan->size)
                                    ->where('status', 'Tersedia')
                                    ->orderBy('nomor_seri_gabungan')
                                    ->get();
        } else {
            // Untuk kegiatan lain, ambil kontainer sesuai vendor dan kondisi lainnya
            $kontainerList = Kontainer::where('ukuran', $suratJalan->size)
                                    ->orderBy('nomor_seri_gabungan')
                                    ->get();
        }

        // Ambil stock kontainer berdasarkan ukuran surat jalan (20ft atau 40ft)
        $stockKontainers = \App\Models\StockKontainer::where('ukuran', $suratJalan->size)
                                                    ->where('status', '!=', 'inactive')
                                                    ->orderBy('nomor_seri_gabungan')
                                                    ->get();

        return view('supir.checkpoint-create', compact('suratJalan', 'kontainerList', 'stockKontainers'));
    }

    /**
     * Menyimpan checkpoint untuk surat jalan.
     */
    public function storeSuratJalan(Request $request, \App\Models\SuratJalan $suratJalan)
    {
        // Pastikan user yang login adalah karyawan dengan divisi supir
        $user = Auth::user();
        if (!$user->isSupir()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk supir.');
        }

        // Otorisasi: Pastikan supir yang login adalah yang ditugaskan untuk surat jalan ini
        // Check multiple possible name formats to ensure access
        $userNamaLengkap = $user->karyawan->nama_lengkap ?? $user->username;
        $userNama = $user->karyawan->nama ?? $user->username;
        $userName = $user->name; // This uses the accessor we created

        if ($userNamaLengkap !== $suratJalan->supir &&
            $userNama !== $suratJalan->supir &&
            $userName !== $suratJalan->supir &&
            $user->username !== $suratJalan->supir) {
            abort(403, 'Anda tidak memiliki akses ke surat jalan ini. User: ' . $userName . ', Surat Jalan Supir: ' . $suratJalan->supir);
        }

        // Validasi input
        $rules = [
            'nomor_kontainer' => 'required|array',
            'nomor_kontainer.*' => 'required|string',
            'surat_jalan_vendor' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'tanggal_checkpoint' => 'required|date',
            'gambar' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:5120', // 5MB
        ];

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('gambar')) {
                $image = $request->file('gambar');
                $filename = time() . '_surat_jalan_checkpoint_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('file_surat_jalan', $filename, 'public');
            }

            // Update surat jalan dengan nomor kontainer dan status
            $nomorKontainers = implode(', ', $request->nomor_kontainer);

            $suratJalan->update([
                'no_kontainer' => $nomorKontainers,
                'no_seal' => $request->no_seal,
                'status' => 'sudah_checkpoint', // Status berubah menjadi "sudah checkpoint"
                'gambar_checkpoint' => $imagePath,
            ]);

            // Buat approval record untuk surat jalan (hanya 1 approval)
            \App\Models\SuratJalanApproval::create([
                'surat_jalan_id' => $suratJalan->id,
                'approval_level' => 'approval',
                'status' => 'pending',
            ]);

            // Log checkpoint untuk tracking
            Log::info('Surat jalan checkpoint completed by supir - sent to approval:', [
                'surat_jalan_id' => $suratJalan->id,
                'supir' => $user->karyawan->nama ?? $user->name,
                'nomor_kontainer' => $nomorKontainers,
                'no_seal' => $request->no_seal,
                'catatan' => $request->catatan,
                'surat_jalan_vendor' => $request->surat_jalan_vendor,
                'approval_level_created' => 'approval'
            ]);

            DB::commit();

            // Update prospek jika tipe kontainer adalah FCL
            $this->updateProspekFromCheckpoint($suratJalan, $nomorKontainers, $request->no_seal);

            return redirect()->route('supir.dashboard')->with('success', 'Checkpoint surat jalan berhasil disimpan dan telah dikirim ke approval tugas 1 dan 2!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing surat jalan checkpoint: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan checkpoint: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update prospek dengan nomor kontainer dan seal dari checkpoint FCL
     */
    private function updateProspekFromCheckpoint(\App\Models\SuratJalan $suratJalan, $nomorKontainer, $noSeal)
    {
        try {
            // Cek apakah tipe kontainer adalah FCL
            if (strtoupper($suratJalan->tipe_kontainer ?? '') !== 'FCL') {
                Log::info('Surat jalan bukan FCL, skip update prospek', [
                    'surat_jalan_id' => $suratJalan->id,
                    'tipe_kontainer' => $suratJalan->tipe_kontainer
                ]);
                return;
            }

            // Cari prospek yang sesuai dengan surat jalan ini
            // Kriteria: supir, pengirim, dan tanggal yang berdekatan
            $prospeks = Prospek::where('nama_supir', $suratJalan->supir)
                ->where('pt_pengirim', $suratJalan->pengirim)
                ->where('status', Prospek::STATUS_AKTIF)
                ->whereNull('nomor_kontainer') // Hanya update yang belum ada nomor kontainer
                ->whereBetween('tanggal', [
                    now()->subDays(7)->format('Y-m-d'),
                    now()->addDays(1)->format('Y-m-d')
                ])
                ->get();

            if ($prospeks->isEmpty()) {
                Log::info('Tidak ada prospek yang cocok untuk diupdate', [
                    'surat_jalan_id' => $suratJalan->id,
                    'supir' => $suratJalan->supir,
                    'pengirim' => $suratJalan->pengirim
                ]);
                return;
            }

            // Update prospek pertama yang ditemukan
            $prospek = $prospeks->first();

            $prospek->update([
                'nomor_kontainer' => $nomorKontainer,
                'no_seal' => $noSeal,
                'updated_by' => Auth::id()
            ]);

            Log::info('Prospek berhasil diupdate dari checkpoint FCL', [
                'prospek_id' => $prospek->id,
                'surat_jalan_id' => $suratJalan->id,
                'nomor_kontainer' => $nomorKontainer,
                'no_seal' => $noSeal,
                'supir' => $suratJalan->supir,
                'pengirim' => $suratJalan->pengirim
            ]);

        } catch (\Exception $e) {
            // Log error tapi jangan fail proses checkpoint
            Log::error('Error updating prospek from checkpoint', [
                'surat_jalan_id' => $suratJalan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
