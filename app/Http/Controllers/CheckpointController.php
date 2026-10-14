<?php

namespace App\Http\Controllers;

use App\Models\Kontainer;
use App\Models\Prospek;
use Illuminate\Http\Request; // Menggunakan Request standar
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckpointController extends Controller
{
    public function createSuratJalan(\App\Models\SuratJalan $suratJalan)
    {
        // Pastikan user yang login adalah karyawan dengan divisi supir
        $user = Auth::user();
        $user->load('karyawan'); // Eager load karyawan relationship

        if (! $user->isSupir()) {
            Log::warning('Checkpoint create (surat jalan) blocked - user is not supir', ['user_id' => $user->id ?? null, 'username' => $user->username ?? null]);
            abort(403, 'Akses ditolak. Fitur ini hanya untuk supir.');
        }

        // Otorisasi: Pastikan supir yang login adalah yang ditugaskan untuk surat jalan ini
        // Check multiple possible name formats to ensure access (case-insensitive)
        $userNamaLengkap = $user->karyawan->nama_lengkap ?? $user->username;
        $userNama = $user->karyawan->nama ?? $user->username;
        $userName = $user->name; // This uses the accessor we created

        if (strcasecmp($userNamaLengkap, $suratJalan->supir) !== 0 &&
            strcasecmp($userNama, $suratJalan->supir) !== 0 &&
            strcasecmp($userName, $suratJalan->supir) !== 0 &&
            strcasecmp($user->username, $suratJalan->supir) !== 0) {
            Log::warning('Checkpoint create (surat jalan) access denied - supir mismatch', ['user_id' => $user->id ?? null, 'username' => $user->username ?? null, 'user_name' => $userName, 'surat_jalan_id' => $suratJalan->id ?? null, 'surat_jalan_supir' => $suratJalan->supir]);
            abort(403, 'Anda tidak memiliki akses ke surat jalan ini. User: '.$userName.', Surat Jalan Supir: '.$suratJalan->supir);
        }

        // Support both "40" and "40ft" formats
        $ukuranValue = str_replace('ft', '', strtolower(trim($suratJalan->size ?? '')));
        $ukuranValues = [$ukuranValue, $ukuranValue.'ft'];

        // Untuk surat jalan, ambil kontainer dengan status Tersedia
        $kontainerList = Kontainer::whereIn('ukuran', $ukuranValues)
            ->where('status', 'Tersedia')
            ->orderBy('nomor_seri_gabungan')
            ->get();

        // Ambil stock kontainer berdasarkan ukuran surat jalan
        $stockKontainers = \App\Models\StockKontainer::whereIn('ukuran', $ukuranValues)
            ->where('status', '!=', 'inactive')
            ->orderBy('nomor_seri_gabungan')
            ->get();

        // Ambil data gudangs untuk dropdown gudang tujuan
        $gudangs = \App\Models\Gudang::orderBy('nama_gudang')->get();

        return view('supir.checkpoint-create', compact('suratJalan', 'kontainerList', 'stockKontainers', 'gudangs'));
    }

    /**
     * Menyimpan checkpoint untuk surat jalan.
     */
    public function storeSuratJalan(Request $request, \App\Models\SuratJalan $suratJalan)
    {
        // Pastikan user yang login adalah karyawan dengan divisi supir
        $user = Auth::user();
        if (! $user->isSupir()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk supir.');
        }

        // Otorisasi: Pastikan supir yang login adalah yang ditugaskan untuk surat jalan ini
        // Check multiple possible name formats to ensure access (case-insensitive)
        $userNamaLengkap = $user->karyawan->nama_lengkap ?? $user->username;
        $userNama = $user->karyawan->nama ?? $user->username;
        $userName = $user->name; // This uses the accessor we created

        if (strcasecmp($userNamaLengkap, $suratJalan->supir) !== 0 &&
            strcasecmp($userNama, $suratJalan->supir) !== 0 &&
            strcasecmp($userName, $suratJalan->supir) !== 0 &&
            strcasecmp($user->username, $suratJalan->supir) !== 0) {
            abort(403, 'Anda tidak memiliki akses ke surat jalan ini. User: '.$userName.', Surat Jalan Supir: '.$suratJalan->supir);
        }

        // Validasi input - berbeda untuk cargo dan non-cargo
        $rules = [
            'surat_jalan_vendor' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'tanggal_checkpoint' => 'required|date',
            'gambar' => 'nullable|array',
            'gambar.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:5120', // 5MB per file
            'bukti_muat' => 'nullable|array',
            'bukti_muat.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:5120', // 5MB per file
            'bukti_timbangan' => 'nullable|array',
            'bukti_timbangan.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:10240', // 10MB per file
            'bukti_timbangan_muat' => 'nullable|array',
            'bukti_timbangan_muat.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:10240', // 10MB per file
            'gudang_tujuan_id' => 'required|exists:gudangs,id',
        ];

        // Add nomor_kontainer and no_seal validation only if tipe_kontainer is not 'cargo'
        if (strtolower($suratJalan->tipe_kontainer ?? '') !== 'cargo') {
            $rules['nomor_kontainer'] = 'required|array';
            $rules['nomor_kontainer.*'] = 'required|string';
            $rules['no_seal'] = 'nullable|array';
            $rules['no_seal.*'] = 'nullable|string|max:255';
        } else {
            // For cargo type, these fields are optional
            $rules['nomor_kontainer'] = 'nullable|array';
            $rules['nomor_kontainer.*'] = 'nullable|string';
            $rules['no_seal'] = 'nullable|array';
            $rules['no_seal.*'] = 'nullable|string|max:255';
        }

        $request->validate($rules, [
            'gambar.*.max' => 'Foto :attribute terlalu besar (maks 5MB). Coba foto ulang dengan resolusi lebih rendah.',
            'gambar.*.mimes' => 'Format file :attribute tidak didukung. Gunakan JPEG, PNG, atau PDF.',
            'gambar.*.uploaded' => 'Foto gagal diupload. Ukuran file terlalu besar untuk server. Coba foto ulang dengan kualitas lebih rendah.',
            'bukti_muat.*.max' => 'Foto bukti muat terlalu besar (maks 5MB).',
            'bukti_muat.*.uploaded' => 'Bukti muat gagal diupload. Ukuran file terlalu besar.',
            'bukti_timbangan.*.max' => 'Foto bukti timbangan terlalu besar (maks 10MB).',
            'bukti_timbangan.*.uploaded' => 'Bukti timbangan gagal diupload. Ukuran file terlalu besar.',
            'bukti_timbangan_muat.*.max' => 'Foto bukti timbangan muat terlalu besar (maks 10MB).',
            'bukti_timbangan_muat.*.uploaded' => 'Bukti timbangan muat gagal diupload. Ukuran file terlalu besar.',
        ]);

        try {
            DB::beginTransaction();

            // Handle multiple image uploads
            $imagePaths = [];
            if ($request->hasFile('gambar')) {
                foreach ($request->file('gambar') as $index => $image) {
                    $filename = time().'_'.$index.'_surat_jalan_checkpoint_'.$image->getClientOriginalName();
                    $imagePath = $image->storeAs('file_surat_jalan', $filename, 'public');
                    $imagePaths[] = $imagePath;
                }
            }
            // Store as JSON array if multiple files, or null if none
            $imagePath = ! empty($imagePaths) ? json_encode($imagePaths) : null;

            // Handle multiple bukti muat uploads
            $buktiMuatPaths = [];
            if ($request->hasFile('bukti_muat')) {
                foreach ($request->file('bukti_muat') as $index => $image) {
                    $filename = time().'_'.$index.'_surat_jalan_bukti_muat_'.$image->getClientOriginalName();
                    $filePath = $image->storeAs('file_surat_jalan', $filename, 'public');
                    $buktiMuatPaths[] = $filePath;
                }
            }
            $buktiMuatPath = ! empty($buktiMuatPaths) ? json_encode($buktiMuatPaths) : null;

            // Handle multiple bukti timbangan uploads
            $buktiTimbanganPaths = [];
            if ($request->hasFile('bukti_timbangan')) {
                foreach ($request->file('bukti_timbangan') as $index => $image) {
                    $filename = time().'_'.$index.'_surat_jalan_bukti_timbangan_'.$image->getClientOriginalName();
                    $filePath = $image->storeAs('file_surat_jalan', $filename, 'public');
                    $buktiTimbanganPaths[] = $filePath;
                }
            }
            $buktiTimbanganPath = ! empty($buktiTimbanganPaths) ? json_encode($buktiTimbanganPaths) : null;

            // Handle multiple bukti timbangan muat uploads
            $buktiTimbanganMuatPaths = [];
            if ($request->hasFile('bukti_timbangan_muat')) {
                foreach ($request->file('bukti_timbangan_muat') as $index => $image) {
                    $filename = time().'_'.$index.'_surat_jalan_bukti_timbangan_muat_'.$image->getClientOriginalName();
                    $filePath = $image->storeAs('file_surat_jalan', $filename, 'public');
                    $buktiTimbanganMuatPaths[] = $filePath;
                }
            }
            $buktiTimbanganMuatPath = ! empty($buktiTimbanganMuatPaths) ? json_encode($buktiTimbanganMuatPaths) : null;

            // Update surat jalan dengan nomor kontainer dan status
            $updateData = [
                'status' => 'sudah_checkpoint', // Status berubah menjadi "sudah checkpoint"
                'gambar_checkpoint' => $imagePath,
                'bukti_muat' => $buktiMuatPath,
                'bukti_timbangan' => $buktiTimbanganPath,
                'bukti_timbangan_muat' => $buktiTimbanganMuatPath,
                'lokasi_gps' => $request->lokasi_gps,
            ];

            // Handle cargo vs non-cargo types
            if (strtolower($suratJalan->tipe_kontainer ?? '') === 'cargo') {
                // For cargo, set default values or leave as null
                $updateData['no_kontainer'] = 'CARGO'; // Default value for cargo
                $updateData['no_seal'] = null; // No seal for cargo
                $nomorKontainers = 'CARGO'; // For logging purposes
            } else {
                // For non-cargo, use provided container numbers
                $nomorKontainers = implode(', ', $request->nomor_kontainer ?? []);
                $updateData['no_kontainer'] = $nomorKontainers;

                // Handle multiple seals - join with comma if multiple provided
                $noSealArray = $request->no_seal ?? [];
                $noSealArray = array_filter($noSealArray, function ($seal) {
                    return ! empty(trim($seal));
                }); // Filter out empty seals
                $updateData['no_seal'] = ! empty($noSealArray) ? implode(', ', $noSealArray) : null;
            }

            $suratJalan->update($updateData);

            // Update gudangs_id to null for kontainers and stock_kontainers
            if (strtolower($suratJalan->tipe_kontainer ?? '') !== 'cargo' && ! empty($request->nomor_kontainer)) {
                // Get nama gudang untuk tujuan_pengiriman
                $gudangTujuan = \App\Models\Gudang::find($request->gudang_tujuan_id);
                $namaGudangTujuan = $gudangTujuan ? $gudangTujuan->nama_gudang : ($suratJalan->tujuan_pengiriman ?? $suratJalan->order->tujuan_kirim ?? null);

                foreach ($request->nomor_kontainer as $nomor) {
                    $nomorRaw = trim($nomor);

                    // Update kontainer
                    $kontainer = Kontainer::where('nomor_seri_gabungan', $nomorRaw)->first();
                    if ($kontainer) {
                        $oldGudangId = $kontainer->gudangs_id;
                        $kontainer->update(['gudangs_id' => null]);
                        Log::info("Kontainer {$nomorRaw} gudangs_id set to null - surat jalan checkpoint");

                        // Log history
                        \App\Models\HistoryKontainer::create([
                            'nomor_kontainer' => $nomorRaw,
                            'tipe_kontainer' => 'kontainer',
                            'jenis_kegiatan' => 'Keluar (Checkpoint SJ)',
                            'tanggal_kegiatan' => $request->tanggal_checkpoint ?? now(),
                            'asal_gudang_id' => $oldGudangId,
                            'gudang_id' => $request->gudang_tujuan_id,
                            'keterangan' => 'Dalam Perjalanan (Checkpoint SJ: '.$suratJalan->no_surat_jalan.')',
                            'created_by' => Auth::id(),
                        ]);
                    }

                    // Update stock kontainer
                    $stockKontainer = \App\Models\StockKontainer::where('nomor_seri_gabungan', $nomorRaw)->first();
                    if ($stockKontainer) {
                        $oldGudangId = $stockKontainer->gudangs_id;
                        $stockKontainer->update(['gudangs_id' => null]);
                        Log::info("Stock kontainer {$nomorRaw} gudangs_id set to null - surat jalan checkpoint");

                        // Log history
                        \App\Models\HistoryKontainer::create([
                            'nomor_kontainer' => $nomorRaw,
                            'tipe_kontainer' => 'stock',
                            'jenis_kegiatan' => 'Keluar (Checkpoint SJ)',
                            'tanggal_kegiatan' => $request->tanggal_checkpoint ?? now(),
                            'asal_gudang_id' => $oldGudangId,
                            'gudang_id' => $request->gudang_tujuan_id,
                            'keterangan' => 'Dalam Perjalanan (Checkpoint SJ: '.$suratJalan->no_surat_jalan.')',
                            'created_by' => Auth::id(),
                        ]);
                    }

                    // Create kontainer_perjalanans record
                    \App\Models\KontainerPerjalanan::create([
                        'surat_jalan_id' => $suratJalan->id,
                        'no_kontainer' => $nomorRaw,
                        'no_surat_jalan' => $suratJalan->no_surat_jalan,
                        'tipe_kontainer' => $suratJalan->tipe_kontainer,
                        'ukuran' => $suratJalan->size,
                        'tujuan_pengiriman' => $namaGudangTujuan,
                        'gudang_tujuan_id' => $request->gudang_tujuan_id,
                        'supir' => $suratJalan->supir,
                        'no_plat' => $suratJalan->no_plat,
                        'waktu_keluar' => $request->tanggal_checkpoint,
                        'status' => 'dalam_perjalanan',
                        'catatan_keluar' => $request->catatan,
                        'created_by' => Auth::id(),
                    ]);

                    Log::info("Created kontainer_perjalanans record for {$nomorRaw}");
                }
            }

            // Buat approval record untuk surat jalan (hanya 1 approval) - cek dulu apakah sudah ada
            $existingApproval = \App\Models\SuratJalanApproval::where('surat_jalan_id', $suratJalan->id)
                ->where('approval_level', 'approval')
                ->first();

            if (! $existingApproval) {
                \App\Models\SuratJalanApproval::create([
                    'surat_jalan_id' => $suratJalan->id,
                    'approval_level' => 'approval',
                    'status' => 'pending',
                ]);

                Log::info('Surat jalan approval record created at checkpoint:', [
                    'surat_jalan_id' => $suratJalan->id,
                    'approval_level' => 'approval',
                ]);
            } else {
                Log::info('Surat jalan approval record already exists, skipping creation:', [
                    'surat_jalan_id' => $suratJalan->id,
                    'existing_approval_id' => $existingApproval->id,
                ]);
            }

            // Log checkpoint untuk tracking
            Log::info('Surat jalan checkpoint completed by supir:', [
                'surat_jalan_id' => $suratJalan->id,
                'supir' => $user->karyawan->nama ?? $user->name,
                'nomor_kontainer' => $nomorKontainers,
                'no_seal' => $request->no_seal,
                'catatan' => $request->catatan,
                'surat_jalan_vendor' => $request->surat_jalan_vendor,
                'approval_already_exists' => $existingApproval ? true : false,
            ]);

            DB::commit();

            // Update prospek jika tipe kontainer adalah FCL
            $this->updateProspekFromCheckpoint($suratJalan, $request->nomor_kontainer ?? [], $request->no_seal);

            return redirect()->route('supir.dashboard')->with('success', 'Checkpoint surat jalan berhasil disimpan dan telah dikirim ke approval tugas 1 dan 2!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing surat jalan checkpoint: '.$e->getMessage());

            return back()->with('error', 'Gagal menyimpan checkpoint: '.$e->getMessage())->withInput();
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
                    'tipe_kontainer' => $suratJalan->tipe_kontainer,
                ]);

                return;
            }

            // Cari prospek dengan pencarian bertingkat berdasarkan prioritas
            $prospeks = collect();

            // Prioritas 1: Cari berdasarkan surat_jalan_id (paling akurat)
            $prospeks = Prospek::where('status', Prospek::STATUS_AKTIF)
                ->where('surat_jalan_id', $suratJalan->id)
                ->where(function ($query) {
                    $query->whereNull('nomor_kontainer')
                        ->orWhere('nomor_kontainer', '')
                        ->orWhere('nomor_kontainer', 'CARGO');
                })
                ->orderBy('created_at', 'asc') // Order by created_at ascending untuk update sesuai urutan dibuat
                ->get();

            // Jika tidak ditemukan, cari berdasarkan no_surat_jalan
            if ($prospeks->isEmpty() && $suratJalan->no_surat_jalan) {
                $prospeks = Prospek::where('status', Prospek::STATUS_AKTIF)
                    ->where('no_surat_jalan', $suratJalan->no_surat_jalan)
                    ->where(function ($query) {
                        $query->whereNull('nomor_kontainer')
                            ->orWhere('nomor_kontainer', '')
                            ->orWhere('nomor_kontainer', 'CARGO');
                    })
                    ->whereBetween('tanggal', [
                        now()->subDays(7)->format('Y-m-d'),
                        now()->addDays(7)->format('Y-m-d'),
                    ])
                    ->orderBy('created_at', 'asc')
                    ->get();
            }

            // Jika masih tidak ditemukan, cari berdasarkan keterangan yang mengandung nomor surat jalan
            if ($prospeks->isEmpty() && $suratJalan->no_surat_jalan) {
                $prospeks = Prospek::where('status', Prospek::STATUS_AKTIF)
                    ->where('keterangan', 'LIKE', '%Surat Jalan: '.$suratJalan->no_surat_jalan.' |%')
                    ->where(function ($query) {
                        $query->whereNull('nomor_kontainer')
                            ->orWhere('nomor_kontainer', '')
                            ->orWhere('nomor_kontainer', 'CARGO');
                    })
                    ->whereBetween('tanggal', [
                        now()->subDays(7)->format('Y-m-d'),
                        now()->addDays(7)->format('Y-m-d'),
                    ])
                    ->orderBy('created_at', 'asc')
                    ->get();
            }

            Log::info('Mencari prospek untuk update dari checkpoint', [
                'surat_jalan_id' => $suratJalan->id,
                'no_surat_jalan' => $suratJalan->no_surat_jalan,
                'supir' => $suratJalan->supir,
                'pengirim' => $suratJalan->pengirim,
                'prospek_found' => $prospeks->count(),
                'nomor_kontainer_baru' => $nomorKontainer,
                'search_surat_jalan_id' => $suratJalan->id,
                'search_no_surat_jalan' => $suratJalan->no_surat_jalan,
            ]);

            if ($prospeks->isEmpty()) {
                // Cari semua prospek dengan status aktif untuk debugging
                $allProspeks = Prospek::where('status', Prospek::STATUS_AKTIF)
                    ->where(function ($query) use ($suratJalan) {
                        $query->where('nama_supir', $suratJalan->supir)
                            ->orWhere('pt_pengirim', $suratJalan->pengirim);
                    })
                    ->whereDate('tanggal', '>=', now()->subDays(3)->format('Y-m-d'))
                    ->select('id', 'nama_supir', 'pt_pengirim', 'nomor_kontainer', 'keterangan', 'tanggal')
                    ->get();

                Log::warning('Tidak ada prospek yang cocok untuk diupdate', [
                    'surat_jalan_id' => $suratJalan->id,
                    'no_surat_jalan' => $suratJalan->no_surat_jalan,
                    'supir' => $suratJalan->supir,
                    'pengirim' => $suratJalan->pengirim,
                    'nomor_kontainer_baru' => $nomorKontainer,
                    'all_prospeks_debug' => $allProspeks->toArray(),
                ]);

                return;
            }

            // Hitung berapa banyak nomor kontainer yang diinput
            $nomorKontainerArray = is_array($nomorKontainer) ? array_filter($nomorKontainer) : [];
            $jumlahKontainerInput = count($nomorKontainerArray);

            // Jika tidak ada nomor kontainer yang diinput, skip update
            if ($jumlahKontainerInput === 0) {
                Log::info('Tidak ada nomor kontainer yang diinput, skip update prospek');

                return;
            }

            Log::info('Jumlah kontainer yang akan diupdate', [
                'jumlah_prospek_ditemukan' => $prospeks->count(),
                'jumlah_kontainer_input' => $jumlahKontainerInput,
                'nomor_kontainer_array' => $nomorKontainerArray,
            ]);

            // Update hanya sejumlah prospek sesuai dengan jumlah kontainer yang diinput
            $updatedCount = 0;
            $prospeksToUpdate = $prospeks->take($jumlahKontainerInput);

            foreach ($prospeksToUpdate as $index => $prospek) {
                // Ambil nomor kontainer dan seal yang sesuai untuk prospek ini
                $nomorKontainerIni = isset($nomorKontainerArray[$index]) ? $nomorKontainerArray[$index] : $nomorKontainerArray[0];
                $noSealArray = is_array($noSeal) ? array_filter($noSeal) : [$noSeal];
                $noSealIni = isset($noSealArray[$index]) ? $noSealArray[$index] : (isset($noSealArray[0]) ? $noSealArray[0] : null);

                $prospek->update([
                    'nomor_kontainer' => $nomorKontainerIni,
                    'no_seal' => $noSealIni,
                    'updated_by' => Auth::id(),
                ]);
                $updatedCount++;

                Log::info('Prospek berhasil diupdate dari checkpoint FCL', [
                    'prospek_id' => $prospek->id,
                    'index' => $index,
                    'surat_jalan_id' => $suratJalan->id,
                    'no_surat_jalan' => $suratJalan->no_surat_jalan,
                    'nomor_kontainer_lama' => $prospek->getOriginal('nomor_kontainer'),
                    'nomor_kontainer_baru' => $nomorKontainerIni,
                    'no_seal' => $noSealIni,
                    'supir' => $suratJalan->supir,
                    'pengirim' => $suratJalan->pengirim,
                ]);
            }

            Log::info('Total prospek yang diupdate: '.$updatedCount);

        } catch (\Exception $e) {
            // Log error tapi jangan fail proses checkpoint
            Log::error('Error updating prospek from checkpoint', [
                'surat_jalan_id' => $suratJalan->id,
                'no_surat_jalan' => $suratJalan->no_surat_jalan,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Menampilkan form checkpoint untuk surat jalan bongkaran.
     */
    public function createSuratJalanBongkaran($id)
    {
        $suratJalanBongkaran = \App\Models\SuratJalanBongkaran::findOrFail($id);

        // Pastikan user yang login adalah karyawan dengan divisi supir
        $user = Auth::user();
        $user->load('karyawan'); // Eager load karyawan relationship

        if (! $user->isSupir()) {
            Log::warning('Checkpoint create (surat jalan bongkaran) blocked - user is not supir', ['user_id' => $user->id ?? null, 'username' => $user->username ?? null]);
            abort(403, 'Akses ditolak. Fitur ini hanya untuk supir.');
        }

        // Otorisasi: Pastikan supir yang login adalah yang ditugaskan untuk surat jalan bongkaran ini
        // Check multiple possible name formats to ensure access (case-insensitive)
        $userNamaLengkap = $user->karyawan->nama_lengkap ?? $user->username;
        $userNama = $user->karyawan->nama ?? $user->username;
        $userName = $user->name;

        if (strcasecmp($userNamaLengkap, $suratJalanBongkaran->supir) !== 0 &&
            strcasecmp($userNama, $suratJalanBongkaran->supir) !== 0 &&
            strcasecmp($userName, $suratJalanBongkaran->supir) !== 0 &&
            strcasecmp($user->username, $suratJalanBongkaran->supir) !== 0) {
            Log::warning('Checkpoint create (surat jalan bongkaran) access denied - supir mismatch', ['user_id' => $user->id ?? null, 'username' => $user->username ?? null, 'user_name' => $userName, 'surat_jalan_bongkaran_id' => $suratJalanBongkaran->id ?? null, 'surat_jalan_bongkaran_supir' => $suratJalanBongkaran->supir]);
            abort(403, 'Anda tidak memiliki akses ke surat jalan bongkaran ini. User: '.$userName.', Surat Jalan Bongkaran Supir: '.$suratJalanBongkaran->supir);
        }

        // Support both "40" and "40ft" formats
        $ukuranValue = str_replace('ft', '', strtolower(trim($suratJalanBongkaran->size ?? '')));
        $ukuranValues = [$ukuranValue, $ukuranValue.'ft'];

        // Untuk surat jalan bongkaran, ambil kontainer dengan status Tersedia
        $kontainerList = Kontainer::whereIn('ukuran', $ukuranValues)
            ->where('status', 'Tersedia')
            ->orderBy('nomor_seri_gabungan')
            ->get();

        // Ambil stock kontainer berdasarkan ukuran surat jalan bongkaran
        $stockKontainers = \App\Models\StockKontainer::whereIn('ukuran', $ukuranValues)
            ->where('status', '!=', 'inactive')
            ->orderBy('nomor_seri_gabungan')
            ->get();

        // Ambil data gudangs untuk dropdown gudang tujuan
        $gudangs = \App\Models\Gudang::orderBy('nama_gudang')->get();

        // Use the same view but pass the bongkaran model as 'suratJalan' for compatibility
        return view('supir.checkpoint-create', [
            'suratJalan' => $suratJalanBongkaran,
            'kontainerList' => $kontainerList,
            'stockKontainers' => $stockKontainers,
            'gudangs' => $gudangs,
            'isBongkaran' => true,
        ]);
    }

    /**
     * Menyimpan checkpoint untuk surat jalan bongkaran.
     */
    public function storeSuratJalanBongkaran(Request $request, $id)
    {
        $suratJalanBongkaran = \App\Models\SuratJalanBongkaran::findOrFail($id);

        // Pastikan user yang login adalah karyawan dengan divisi supir
        $user = Auth::user();
        if (! $user->isSupir()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk supir.');
        }

        // Otorisasi: Pastikan supir yang login adalah yang ditugaskan untuk surat jalan bongkaran ini (case-insensitive)
        $userNamaLengkap = $user->karyawan->nama_lengkap ?? $user->username;
        $userNama = $user->karyawan->nama ?? $user->username;
        $userName = $user->name;

        if (strcasecmp($userNamaLengkap, $suratJalanBongkaran->supir) !== 0 &&
            strcasecmp($userNama, $suratJalanBongkaran->supir) !== 0 &&
            strcasecmp($userName, $suratJalanBongkaran->supir) !== 0 &&
            strcasecmp($user->username, $suratJalanBongkaran->supir) !== 0) {
            abort(403, 'Anda tidak memiliki akses ke surat jalan bongkaran ini. User: '.$userName.', Surat Jalan Bongkaran Supir: '.$suratJalanBongkaran->supir);
        }

        // Validasi input - similar to regular surat jalan
        $rules = [
            'surat_jalan_vendor' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'tanggal_checkpoint' => 'required|date',
            'gambar' => 'nullable|array',
            'gambar.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:5120', // 5MB per file
            'bukti_muat' => 'nullable|array',
            'bukti_muat.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:5120', // 5MB per file
            'bukti_timbangan' => 'nullable|array',
            'bukti_timbangan.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:10240', // 10MB per file
            'bukti_timbangan_muat' => 'nullable|array',
            'bukti_timbangan_muat.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:10240', // 10MB per file
            'gudang_tujuan_id' => 'required|exists:gudangs,id',
        ];

        // Add nomor_kontainer and no_seal validation only if tipe_kontainer is not 'cargo'
        if (strtolower($suratJalanBongkaran->tipe_kontainer ?? '') !== 'cargo') {
            $rules['nomor_kontainer'] = 'required|array';
            $rules['nomor_kontainer.*'] = 'required|string';
            $rules['no_seal'] = 'nullable|array';
            $rules['no_seal.*'] = 'nullable|string|max:255';
        } else {
            // For cargo type, these fields are optional
            $rules['nomor_kontainer'] = 'nullable|array';
            $rules['nomor_kontainer.*'] = 'nullable|string';
            $rules['no_seal'] = 'nullable|array';
            $rules['no_seal.*'] = 'nullable|string|max:255';
        }

        $request->validate($rules, [
            'gambar.*.max' => 'Foto :attribute terlalu besar (maks 5MB). Coba foto ulang dengan resolusi lebih rendah.',
            'gambar.*.mimes' => 'Format file :attribute tidak didukung. Gunakan JPEG, PNG, atau PDF.',
            'gambar.*.uploaded' => 'Foto gagal diupload. Ukuran file terlalu besar untuk server. Coba foto ulang dengan kualitas lebih rendah.',
            'bukti_muat.*.max' => 'Foto bukti muat terlalu besar (maks 5MB).',
            'bukti_muat.*.uploaded' => 'Bukti muat gagal diupload. Ukuran file terlalu besar.',
            'bukti_timbangan.*.max' => 'Foto bukti timbangan terlalu besar (maks 10MB).',
            'bukti_timbangan.*.uploaded' => 'Bukti timbangan gagal diupload. Ukuran file terlalu besar.',
            'bukti_timbangan_muat.*.max' => 'Foto bukti timbangan muat terlalu besar (maks 10MB).',
            'bukti_timbangan_muat.*.uploaded' => 'Bukti timbangan muat gagal diupload. Ukuran file terlalu besar.',
        ]);

        try {
            DB::beginTransaction();

            // Handle multiple image uploads
            $imagePaths = [];
            if ($request->hasFile('gambar')) {
                foreach ($request->file('gambar') as $index => $image) {
                    $filename = time().'_'.$index.'_surat_jalan_bongkaran_checkpoint_'.$image->getClientOriginalName();
                    $imagePath = $image->storeAs('file_surat_jalan', $filename, 'public');
                    $imagePaths[] = $imagePath;
                }
            }
            // Store as JSON array if multiple files, or null if none
            $imagePath = ! empty($imagePaths) ? json_encode($imagePaths) : null;

            // Handle multiple bukti muat uploads
            $buktiMuatPaths = [];
            if ($request->hasFile('bukti_muat')) {
                foreach ($request->file('bukti_muat') as $index => $image) {
                    $filename = time().'_'.$index.'_surat_jalan_bongkaran_bukti_muat_'.$image->getClientOriginalName();
                    $filePath = $image->storeAs('file_surat_jalan', $filename, 'public');
                    $buktiMuatPaths[] = $filePath;
                }
            }
            $buktiMuatPath = ! empty($buktiMuatPaths) ? json_encode($buktiMuatPaths) : null;

            // Handle multiple bukti timbangan uploads
            $buktiTimbanganPaths = [];
            if ($request->hasFile('bukti_timbangan')) {
                foreach ($request->file('bukti_timbangan') as $index => $image) {
                    $filename = time().'_'.$index.'_surat_jalan_bongkaran_bukti_timbangan_'.$image->getClientOriginalName();
                    $filePath = $image->storeAs('file_surat_jalan', $filename, 'public');
                    $buktiTimbanganPaths[] = $filePath;
                }
            }
            $buktiTimbanganPath = ! empty($buktiTimbanganPaths) ? json_encode($buktiTimbanganPaths) : null;

            // Handle multiple bukti timbangan muat uploads
            $buktiTimbanganMuatPaths = [];
            if ($request->hasFile('bukti_timbangan_muat')) {
                foreach ($request->file('bukti_timbangan_muat') as $index => $image) {
                    $filename = time().'_'.$index.'_surat_jalan_bongkaran_bukti_timbangan_muat_'.$image->getClientOriginalName();
                    $filePath = $image->storeAs('file_surat_jalan', $filename, 'public');
                    $buktiTimbanganMuatPaths[] = $filePath;
                }
            }
            $buktiTimbanganMuatPath = ! empty($buktiTimbanganMuatPaths) ? json_encode($buktiTimbanganMuatPaths) : null;

            // Update surat jalan bongkaran dengan nomor kontainer dan tanggal checkpoint
            $updateData = [
                'tanggal_checkpoint' => $request->tanggal_checkpoint,
                'gambar_checkpoint' => $imagePath,
                'bukti_muat' => $buktiMuatPath,
                'bukti_timbangan' => $buktiTimbanganPath,
                'bukti_timbangan_muat' => $buktiTimbanganMuatPath,
                'lokasi_gps' => $request->lokasi_gps,
            ];

            // Handle cargo vs non-cargo types
            if (strtolower($suratJalanBongkaran->tipe_kontainer ?? '') === 'cargo') {
                // For cargo, set default values or leave as null
                $updateData['no_kontainer'] = 'CARGO'; // Default value for cargo
                $updateData['no_seal'] = null; // No seal for cargo
                $nomorKontainers = 'CARGO'; // For logging purposes
            } else {
                // For non-cargo, use provided container numbers
                $nomorKontainers = implode(', ', $request->nomor_kontainer ?? []);
                $updateData['no_kontainer'] = $nomorKontainers;

                // Handle multiple seals - join with comma if multiple provided
                $noSealArray = $request->no_seal ?? [];
                $noSealArray = array_filter($noSealArray, function ($seal) {
                    return ! empty(trim($seal));
                }); // Filter out empty seals
                $updateData['no_seal'] = ! empty($noSealArray) ? implode(', ', $noSealArray) : null;
            }

            $suratJalanBongkaran->update($updateData);

            // Update gudangs_id to null for kontainers and stock_kontainers
            if (strtolower($suratJalanBongkaran->tipe_kontainer ?? '') !== 'cargo' && ! empty($request->nomor_kontainer)) {
                foreach ($request->nomor_kontainer as $nomor) {
                    $nomorRaw = trim($nomor);

                    // Update kontainer
                    $kontainer = Kontainer::where('nomor_seri_gabungan', $nomorRaw)->first();
                    if ($kontainer) {
                        $oldGudangId = $kontainer->gudangs_id;
                        $kontainer->update(['gudangs_id' => null]);
                        Log::info("Kontainer {$nomorRaw} gudangs_id set to null - surat jalan bongkaran checkpoint");

                        // Log history
                        \App\Models\HistoryKontainer::create([
                            'nomor_kontainer' => $nomorRaw,
                            'tipe_kontainer' => 'kontainer',
                            'jenis_kegiatan' => 'Keluar (Checkpoint Bongkaran)',
                            'tanggal_kegiatan' => $request->tanggal_checkpoint ?? now(),
                            'asal_gudang_id' => $oldGudangId,
                            'gudang_id' => $request->gudang_tujuan_id,
                            'keterangan' => 'Dalam Perjalanan (Checkpoint Bongkaran ID: '.$id.')',
                            'created_by' => Auth::id(),
                        ]);
                    }

                    // Update stock kontainer
                    $stockKontainer = \App\Models\StockKontainer::where('nomor_seri_gabungan', $nomorRaw)->first();
                    if ($stockKontainer) {
                        $oldGudangId = $stockKontainer->gudangs_id;
                        $stockKontainer->update(['gudangs_id' => null]);
                        Log::info("Stock kontainer {$nomorRaw} gudangs_id set to null - surat jalan bongkaran checkpoint");

                        // Log history
                        \App\Models\HistoryKontainer::create([
                            'nomor_kontainer' => $nomorRaw,
                            'tipe_kontainer' => 'stock',
                            'jenis_kegiatan' => 'Keluar (Checkpoint Bongkaran)',
                            'tanggal_kegiatan' => $request->tanggal_checkpoint ?? now(),
                            'asal_gudang_id' => $oldGudangId,
                            'gudang_id' => $request->gudang_tujuan_id,
                            'keterangan' => 'Dalam Perjalanan (Checkpoint Bongkaran ID: '.$id.')',
                            'created_by' => Auth::id(),
                        ]);
                    }

                    // Note: For surat_jalan_bongkaran, we don't have a direct surat_jalan_id
                    // So we skip creating kontainer_perjalanans record for bongkaran
                    // Unless we have a relationship or want to extend the model
                }
            }

            // Log checkpoint untuk tracking
            Log::info('Surat jalan bongkaran checkpoint completed by supir:', [
                'surat_jalan_bongkaran_id' => $suratJalanBongkaran->id,
                'supir' => $user->karyawan->nama ?? $user->name,
                'nomor_kontainer' => $nomorKontainers,
                'no_seal' => $request->no_seal,
                'catatan' => $request->catatan,
                'surat_jalan_vendor' => $request->surat_jalan_vendor,
                'tanggal_checkpoint' => $request->tanggal_checkpoint,
            ]);

            DB::commit();

            return redirect()->route('supir.dashboard')->with('success', 'Checkpoint surat jalan bongkaran berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing surat jalan bongkaran checkpoint: '.$e->getMessage());

            return back()->with('error', 'Gagal menyimpan checkpoint: '.$e->getMessage())->withInput();
        }
    }
}
