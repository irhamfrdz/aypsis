<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use App\Models\Kontainer;
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

        // Ambil semua kontainer dari tabel kontainers
        $kontainerList = Kontainer::all();

        // Ambil semua stock kontainer dari master stock kontainer
        $stockKontainers = \App\Models\StockKontainer::all();

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
        ];

        // Tambahkan validasi untuk nomor_kontainer hanya jika kontainer belum diinput sebelumnya.
        if ($permohonan->kontainers->isEmpty()) {
            $rules['nomor_kontainer'] = ['required', 'array', 'size:' . $permohonan->jumlah_kontainer];

            // Check if this is a container repair activity or antar sewa activity
            $kegiatanLower = strtolower($permohonan->kegiatan ?? '');
            $isPerbaikanKontainer = (stripos($kegiatanLower, 'perbaikan') !== false && stripos($kegiatanLower, 'kontainer') !== false)
                || (stripos($kegiatanLower, 'repair') !== false && stripos($kegiatanLower, 'container') !== false);
            $isAntarSewa = stripos($kegiatanLower, 'antar') !== false && stripos($kegiatanLower, 'sewa') !== false;

            // Untuk kegiatan perbaikan kontainer, antar sewa, atau vendor tertentu, supir menginput nomor kontainer sebagai string (nomor lengkap)
            if ($isPerbaikanKontainer || $isAntarSewa || in_array($permohonan->vendor_perusahaan, ['ZONA', 'DPE', 'SOC'])) {
                // Accept free-text nomor kontainer (string) and ensure distinct
                $rules['nomor_kontainer.*'] = ['required', 'string', 'distinct'];
            } else {
                // Setiap nomor kontainer harus unik dan berupa id kontainer
                $rules['nomor_kontainer.*'] = ['required', 'integer', 'distinct', 'exists:kontainers,id'];
            }
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

                // Jika kegiatan perbaikan kontainer, antar sewa, atau vendor menerima input nomor kontainer bebas, cari atau buat kontainer berdasarkan nomor_seri_gabungan
                if ($isPerbaikanKontainer || $isAntarSewa || in_array($permohonan->vendor_perusahaan, ['ZONA', 'DPE', 'SOC'])) {
                    foreach ($validated['nomor_kontainer'] as $nomor) {
                        // Preserve the raw input (trimmed) so what supir types is what we store/display.
                        $nomorRaw = trim($nomor);
                        // Also compute a normalized lookup key (uppercase, no spaces) to find existing records
                        $nomorLookup = strtoupper(str_replace(' ', '', $nomorRaw));

                        // Try to find by exact raw stored serial first, then by normalized form
                        $kontainer = Kontainer::firstWhere('nomor_seri_gabungan', $nomorRaw)
                            ?? Kontainer::firstWhere('nomor_seri_gabungan', $nomorLookup);

                        if (!$kontainer) {
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
                } else {
                    foreach ($validated['nomor_kontainer'] as $kontainerId) {
                        $kontainer = Kontainer::find($kontainerId);
                        if (!$kontainer) {
                            throw new \Exception("Kontainer tidak ditemukan.");
                        }
                        if ($isReturnSewa) {
                            // mark returned: set finish date and make available
                            $kontainer->tanggal_selesai_sewa = $request->input('tanggal_checkpoint') ?? now()->format('Y-m-d');
                            $kontainer->status = 'Tersedia';
                            $kontainer->save();
                            // include id to link to this permohonan
                            $kontainerIds[] = $kontainer->id;
                        } else {
                            if ($kontainer->status !== 'Tersedia') {
                                throw new \Exception("Kontainer tidak tersedia atau sedang digunakan.");
                            }
                            // collect ids to mark as Digunakan after validation
                            $kontainerIds[] = $kontainer->id;
                        }
                    }
                    if (!$isReturnSewa && !empty($kontainerIds)) {
                        Kontainer::whereIn('id', $kontainerIds)->update(['status' => 'Digunakan']);
                    }
                }

                // Hubungkan kontainer ke permohonan
                $permohonan->kontainers()->sync($kontainerIds);

                // Update status stock kontainer berdasarkan jenis kegiatan
                if (!empty($validated['nomor_kontainer'])) {
                    foreach ($validated['nomor_kontainer'] as $nomor) {
                        $nomorRaw = trim($nomor);

                        // Cari stock kontainer berdasarkan nomor kontainer
                        $stockKontainer = \App\Models\StockKontainer::where('nomor_kontainer', $nomorRaw)->first();

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

            // Simpan data checkpoint
            $permohonan->checkpoints()->create([
                'lokasi' => $permohonan->tujuan, // Mengisi lokasi dengan tujuan permohonan
                'catatan' => $validated['catatan'] ?? 'Checkpoint dibuat oleh supir.',
                'surat_jalan_vendor' => $validated['surat_jalan_vendor'] ?? null,
                'tanggal_checkpoint' => $request->input('tanggal_checkpoint') ?? now()->format('Y-m-d'),
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
}
