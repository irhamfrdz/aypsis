<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportRitController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        // Tampilkan halaman select date
        return view('report-rit.select-date');
    }

    public function view(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        // Validasi required tanggal
        if (!$request->has('start_date') || !$request->has('end_date')) {
            return redirect()->route('report.rit.index')
                ->with('error', 'Tanggal mulai dan tanggal akhir harus diisi');
        }

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Query untuk Surat Jalan biasa - filter hanya yang menggunakan rit
        $querySuratJalan = SuratJalan::where('rit', 'menggunakan_rit')
            // Harus punya checkpoint ATAU tanda terima ATAU status approved
            ->where(function($q) {
                $q->whereNotNull('tanggal_checkpoint')
                  ->orWhereHas('tandaTerima')
                  ->orWhere(function($subQ) {
                      $subQ->where('kegiatan', 'bongkaran')
                           ->whereNotNull('tanggal_tanda_terima');
                  })
                  ->orWhere('status', 'approved');
            })
            ->where(function($q) use ($startDate, $endDate) {
                // Filter berdasarkan tanggal dari berbagai sumber (OR conditions)
                $q->where(function($subQ) use ($startDate, $endDate) {
                    // 1. Tanggal dari relasi tandaTerima
                    $subQ->whereHas('tandaTerima', function($ttQuery) use ($startDate, $endDate) {
                        $ttQuery->where(\DB::raw('DATE(tanggal)'), '>=', $startDate->toDateString())
                                ->where(\DB::raw('DATE(tanggal)'), '<=', $endDate->toDateString());
                    });
                })
                ->orWhere(function($subQ) use ($startDate, $endDate) {
                    // 2. Tanggal tanda terima untuk kegiatan bongkaran
                    $subQ->where('kegiatan', 'bongkaran')
                         ->whereNotNull('tanggal_tanda_terima')
                         ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '>=', $startDate->toDateString())
                         ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '<=', $endDate->toDateString());
                })
                ->orWhere(function($subQ) use ($startDate, $endDate) {
                    // 3. Filter berdasarkan tanggal checkpoint
                    $subQ->whereNotNull('tanggal_checkpoint')
                         ->where(\DB::raw('DATE(tanggal_checkpoint)'), '>=', $startDate->toDateString())
                         ->where(\DB::raw('DATE(tanggal_checkpoint)'), '<=', $endDate->toDateString());
                });
            });

        // Query untuk Surat Jalan Bongkaran - filter hanya yang menggunakan rit atau rit null
        $querySuratJalanBongkaran = SuratJalanBongkaran::where(function($q) {
                $q->where('rit', 'menggunakan_rit')
                  ->orWhereNull('rit');
            })
            ->where(function($q) use ($startDate, $endDate) {
                // Filter berdasarkan tanggal dari berbagai sumber (OR conditions)
                $q->where(function($subQ) use ($startDate, $endDate) {
                    // 1. Tanggal dari relasi tandaTerima
                    $subQ->whereHas('tandaTerima', function($ttQuery) use ($startDate, $endDate) {
                        $ttQuery->where(\DB::raw('DATE(tanggal_tanda_terima)'), '>=', $startDate->toDateString())
                                ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '<=', $endDate->toDateString());
                    });
                })
                ->orWhere(function($subQ) use ($startDate, $endDate) {
                    // 2. Filter berdasarkan tanggal checkpoint
                    $subQ->whereNotNull('tanggal_checkpoint')
                         ->where(\DB::raw('DATE(tanggal_checkpoint)'), '>=', $startDate->toDateString())
                         ->where(\DB::raw('DATE(tanggal_checkpoint)'), '<=', $endDate->toDateString());
                });
            });

        // Filter tambahan jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $querySuratJalan->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('supir2', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('tujuan_pengiriman', 'like', "%{$search}%");
            });
            
            $querySuratJalanBongkaran->where(function($q) use ($search) {
                $q->where('nomor_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('supir2', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('tujuan_pengiriman', 'like', "%{$search}%");
            });
        }

        if ($request->filled('supir')) {
            $querySuratJalan->where(function($q) use ($request) {
                $q->where('supir', 'like', "%{$request->supir}%")
                  ->orWhere('supir2', 'like', "%{$request->supir}%");
            });
            
            $querySuratJalanBongkaran->where(function($q) use ($request) {
                $q->where('supir', 'like', "%{$request->supir}%")
                  ->orWhere('supir2', 'like', "%{$request->supir}%");
            });
        }

        if ($request->filled('kegiatan')) {
            $querySuratJalan->where('kegiatan', $request->kegiatan);
            $querySuratJalanBongkaran->where('kegiatan', $request->kegiatan);
        }

        // Filter berdasarkan status pembayaran uang rit
        if ($request->filled('status_pembayaran_rit')) {
            $statusFilter = $request->status_pembayaran_rit;
            
            if ($statusFilter === 'belum_dibayar') {
                // Belum dibayar: status null atau belum_dibayar
                $querySuratJalan->where(function($q) {
                    $q->whereNull('status_pembayaran_uang_rit')
                      ->orWhere('status_pembayaran_uang_rit', 'belum_dibayar');
                });
                
                // Filter Bongkaran: belum_bayar atau null
                $querySuratJalanBongkaran->where(function($q) {
                    $q->whereNull('status_pembayaran_uang_rit')
                      ->orWhere('status_pembayaran_uang_rit', 'belum_bayar');
                });
                
            } elseif ($statusFilter === 'dibayar') {
                // Sudah dibayar
                $querySuratJalan->where('status_pembayaran_uang_rit', 'dibayar');
                
                // Filter Bongkaran: lunas
                $querySuratJalanBongkaran->where('status_pembayaran_uang_rit', 'lunas');
                
            } elseif ($statusFilter === 'proses') {
                // Dalam proses: proses_pranota, sudah_masuk_pranota, pranota_submitted, pranota_approved
                $querySuratJalan->whereIn('status_pembayaran_uang_rit', [
                    'proses_pranota',
                    'sudah_masuk_pranota', 
                    'pranota_submitted',
                    'pranota_approved'
                ]);
                
                // Filter Bongkaran: Tidak punya status proses, jadi exclude semua (karena bongkaran langsung lunas)
                // Atau tampilkan kosong
                $querySuratJalanBongkaran->where('id', 0); // Force empty
            }
        }

        // Get data dari kedua tabel
        $suratJalansBiasa = $querySuratJalan
            ->with(['order', 'pengirimRelation', 'jenisBarangRelation', 'tujuanPengirimanRelation', 'tandaTerima'])
            ->get();
            
        $suratJalansBongkaran = $querySuratJalanBongkaran
            ->with(['tandaTerima'])
            ->get();

        // Gabungkan dan transform data agar konsisten
        $allSuratJalans = collect();
        
        // Add surat jalan biasa
        foreach ($suratJalansBiasa as $sj) {
            $allSuratJalans->push([
                'type' => 'regular',
                'id' => $sj->id,
                'tanggal_surat_jalan' => $sj->tanggal_surat_jalan,
                'tanggal_checkpoint' => $sj->tanggal_checkpoint,
                'tanggal_tanda_terima' => $sj->tandaTerima ? $sj->tandaTerima->tanggal : null,
                'no_surat_jalan' => $sj->no_surat_jalan,
                'kegiatan' => $sj->kegiatan,
                'supir' => $sj->supir ?: $sj->supir2,
                'no_plat' => $sj->no_plat,
                'pengirim' => $sj->pengirimRelation ? $sj->pengirimRelation->nama_pengirim : $sj->pengirim,
                'penerima' => $sj->tujuanPengirimanRelation ? $sj->tujuanPengirimanRelation->nama_tujuan : $sj->tujuan_pengiriman,
                'jenis_barang' => $sj->jenisBarangRelation ? $sj->jenisBarangRelation->nama_barang : $sj->jenis_barang,
                'tipe_kontainer' => $sj->tipe_kontainer ?: ($sj->size ?: ($sj->order ? $sj->order->tipe_kontainer : null)),
                'rit' => $sj->rit,
                'order' => $sj->order,
                'created_at' => $sj->created_at,
            ]);
        }
        
        // Add surat jalan bongkaran
        foreach ($suratJalansBongkaran as $sjb) {
            $allSuratJalans->push([
                'type' => 'bongkaran',
                'id' => $sjb->id,
                'tanggal_surat_jalan' => $sjb->tanggal_surat_jalan,
                'tanggal_checkpoint' => $sjb->tanggal_checkpoint,
                'tanggal_tanda_terima' => $sjb->tandaTerima ? $sjb->tandaTerima->tanggal_tanda_terima : null,
                'no_surat_jalan' => $sjb->nomor_surat_jalan,
                'kegiatan' => $sjb->kegiatan,
                'supir' => $sjb->supir ?: $sjb->supir2,
                'no_plat' => $sjb->no_plat,
                'pengirim' => $sjb->pengirim,
                'penerima' => $sjb->tujuan_pengiriman,
                'jenis_barang' => $sjb->jenis_barang,
                'tipe_kontainer' => $sjb->tipe_kontainer ?: $sjb->size,
                'rit' => $sjb->rit,
                'order' => null,
                'created_at' => $sjb->created_at,
            ]);
        }
        
        // Sort by tanggal descending, then created_at descending
        $allSuratJalans = $allSuratJalans->sortByDesc(function($item) {
            return $item['tanggal_surat_jalan'] . ' ' . $item['created_at'];
        });

        // Manual pagination
        $perPage = $request->get('per_page', 50);
        $currentPage = $request->get('page', 1);
        $total = $allSuratJalans->count();
        
        $suratJalans = new \Illuminate\Pagination\LengthAwarePaginator(
            $allSuratJalans->forPage($currentPage, $perPage)->values(),
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('report-rit.view', compact('suratJalans', 'startDate', 'endDate'));
    }

    public function print(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Query untuk Surat Jalan biasa - filter hanya yang menggunakan rit
        $querySuratJalan = SuratJalan::where('rit', 'menggunakan_rit')
            // Harus punya checkpoint ATAU tanda terima ATAU status approved
            ->where(function($q) {
                $q->whereNotNull('tanggal_checkpoint')
                  ->orWhereHas('tandaTerima')
                  ->orWhere(function($subQ) {
                      $subQ->where('kegiatan', 'bongkaran')
                           ->whereNotNull('tanggal_tanda_terima');
                  })
                  ->orWhere('status', 'approved');
            })
            ->where(function($q) use ($startDate, $endDate) {
                // Filter berdasarkan tanggal dari berbagai sumber (OR conditions)
                $q->where(function($subQ) use ($startDate, $endDate) {
                    // 1. Tanggal dari relasi tandaTerima
                    $subQ->whereHas('tandaTerima', function($ttQuery) use ($startDate, $endDate) {
                        $ttQuery->where(\DB::raw('DATE(tanggal)'), '>=', $startDate->toDateString())
                                ->where(\DB::raw('DATE(tanggal)'), '<=', $endDate->toDateString());
                    });
                })
                ->orWhere(function($subQ) use ($startDate, $endDate) {
                    // 2. Tanggal tanda terima untuk kegiatan bongkaran
                    $subQ->where('kegiatan', 'bongkaran')
                         ->whereNotNull('tanggal_tanda_terima')
                         ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '>=', $startDate->toDateString())
                         ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '<=', $endDate->toDateString());
                })
                ->orWhere(function($subQ) use ($startDate, $endDate) {
                    // 3. Filter berdasarkan tanggal checkpoint
                    $subQ->whereNotNull('tanggal_checkpoint')
                         ->where(\DB::raw('DATE(tanggal_checkpoint)'), '>=', $startDate->toDateString())
                         ->where(\DB::raw('DATE(tanggal_checkpoint)'), '<=', $endDate->toDateString());
                });
            });

        // Query untuk Surat Jalan Bongkaran - filter hanya yang menggunakan rit atau rit null
        $querySuratJalanBongkaran = SuratJalanBongkaran::where(function($q) {
                $q->where('rit', 'menggunakan_rit')
                  ->orWhereNull('rit');
            })
            ->where(function($q) use ($startDate, $endDate) {
                // Filter berdasarkan tanggal dari berbagai sumber (OR conditions)
                $q->where(function($subQ) use ($startDate, $endDate) {
                    // 1. Tanggal dari relasi tandaTerima
                    $subQ->whereHas('tandaTerima', function($ttQuery) use ($startDate, $endDate) {
                        $ttQuery->where(\DB::raw('DATE(tanggal_tanda_terima)'), '>=', $startDate->toDateString())
                                ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '<=', $endDate->toDateString());
                    });
                })
                ->orWhere(function($subQ) use ($startDate, $endDate) {
                    // 2. Filter berdasarkan tanggal checkpoint
                    $subQ->whereNotNull('tanggal_checkpoint')
                         ->where(\DB::raw('DATE(tanggal_checkpoint)'), '>=', $startDate->toDateString())
                         ->where(\DB::raw('DATE(tanggal_checkpoint)'), '<=', $endDate->toDateString());
                });
            });

        // Filter tambahan jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $querySuratJalan->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('supir2', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('tujuan_pengiriman', 'like', "%{$search}%");
            });
            
            $querySuratJalanBongkaran->where(function($q) use ($search) {
                $q->where('nomor_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('supir2', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('tujuan_pengiriman', 'like', "%{$search}%");
            });
        }

        if ($request->filled('supir')) {
            $querySuratJalan->where(function($q) use ($request) {
                $q->where('supir', 'like', "%{$request->supir}%")
                  ->orWhere('supir2', 'like', "%{$request->supir}%");
            });
            
            $querySuratJalanBongkaran->where(function($q) use ($request) {
                $q->where('supir', 'like', "%{$request->supir}%")
                  ->orWhere('supir2', 'like', "%{$request->supir}%");
            });
        }

        if ($request->filled('kegiatan')) {
            $querySuratJalan->where('kegiatan', $request->kegiatan);
            $querySuratJalanBongkaran->where('kegiatan', $request->kegiatan);
        }

        // Get data dari kedua tabel
        $suratJalansBiasa = $querySuratJalan->with(['tandaTerima'])->get();
        $suratJalansBongkaran = $querySuratJalanBongkaran->with(['tandaTerima'])->get();

        // Gabungkan dan transform data agar konsisten
        $allSuratJalans = collect();
        
        foreach ($suratJalansBiasa as $sj) {
            $allSuratJalans->push([
                'type' => 'regular',
                'tanggal_surat_jalan' => $sj->tanggal_surat_jalan,
                'tanggal_checkpoint' => $sj->tanggal_checkpoint,
                'tanggal_tanda_terima' => $sj->tandaTerima ? $sj->tandaTerima->tanggal : null,
                'no_surat_jalan' => $sj->no_surat_jalan,
                'kegiatan' => $sj->kegiatan,
                'supir' => $sj->supir ?: $sj->supir2,
                'no_plat' => $sj->no_plat,
                'pengirim' => $sj->pengirim,
                'penerima' => $sj->tujuan_pengiriman,
                'jenis_barang' => $sj->jenis_barang,
                'tipe_kontainer' => $sj->tipe_kontainer ?: $sj->size,
                'rit' => $sj->rit,
                'order' => $sj->order,
                'created_at' => $sj->created_at,
            ]);
        }
        
        foreach ($suratJalansBongkaran as $sjb) {
            $allSuratJalans->push([
                'type' => 'bongkaran',
                'tanggal_surat_jalan' => $sjb->tanggal_surat_jalan,
                'tanggal_checkpoint' => $sjb->tanggal_checkpoint,
                'tanggal_tanda_terima' => $sjb->tandaTerima ? $sjb->tandaTerima->tanggal_tanda_terima : null,
                'no_surat_jalan' => $sjb->nomor_surat_jalan,
                'kegiatan' => $sjb->kegiatan,
                'supir' => $sjb->supir ?: $sjb->supir2,
                'no_plat' => $sjb->no_plat,
                'pengirim' => $sjb->pengirim,
                'penerima' => $sjb->tujuan_pengiriman,
                'jenis_barang' => $sjb->jenis_barang,
                'tipe_kontainer' => $sjb->tipe_kontainer ?: $sjb->size,
                'rit' => $sjb->rit,
                'order' => null,
                'created_at' => $sjb->created_at,
            ]);
        }
        
        $suratJalans = $allSuratJalans->sortByDesc(function($item) {
            return $item['tanggal_surat_jalan'] . ' ' . $item['created_at'];
        });

        return view('report-rit.print', compact('suratJalans', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Query untuk Surat Jalan biasa - filter hanya yang menggunakan rit
        $querySuratJalan = SuratJalan::where('rit', 'menggunakan_rit')
            // Harus punya checkpoint ATAU tanda terima ATAU status approved
            ->where(function($q) {
                $q->whereNotNull('tanggal_checkpoint')
                  ->orWhereHas('tandaTerima')
                  ->orWhere(function($subQ) {
                      $subQ->where('kegiatan', 'bongkaran')
                           ->whereNotNull('tanggal_tanda_terima');
                  })
                  ->orWhere('status', 'approved');
            })
            ->where(function($q) use ($startDate, $endDate) {
                // Filter berdasarkan tanggal dari berbagai sumber (OR conditions)
                $q->where(function($subQ) use ($startDate, $endDate) {
                    // 1. Tanggal dari relasi tandaTerima
                    $subQ->whereHas('tandaTerima', function($ttQuery) use ($startDate, $endDate) {
                        $ttQuery->where(\DB::raw('DATE(tanggal)'), '>=', $startDate->toDateString())
                                ->where(\DB::raw('DATE(tanggal)'), '<=', $endDate->toDateString());
                    });
                })
                ->orWhere(function($subQ) use ($startDate, $endDate) {
                    // 2. Tanggal tanda terima untuk kegiatan bongkaran
                    $subQ->where('kegiatan', 'bongkaran')
                         ->whereNotNull('tanggal_tanda_terima')
                         ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '>=', $startDate->toDateString())
                         ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '<=', $endDate->toDateString());
                })
                ->orWhere(function($subQ) use ($startDate, $endDate) {
                    // 3. Filter berdasarkan tanggal checkpoint
                    $subQ->whereNotNull('tanggal_checkpoint')
                         ->where(\DB::raw('DATE(tanggal_checkpoint)'), '>=', $startDate->toDateString())
                         ->where(\DB::raw('DATE(tanggal_checkpoint)'), '<=', $endDate->toDateString());
                });
            });

        // Query untuk Surat Jalan Bongkaran - filter hanya yang menggunakan rit atau rit null
        $querySuratJalanBongkaran = SuratJalanBongkaran::where(function($q) {
                $q->where('rit', 'menggunakan_rit')
                  ->orWhereNull('rit');
            })
            ->where(function($q) use ($startDate, $endDate) {
                // Filter berdasarkan tanggal dari berbagai sumber (OR conditions)
                $q->where(function($subQ) use ($startDate, $endDate) {
                    // 1. Tanggal dari relasi tandaTerima
                    $subQ->whereHas('tandaTerima', function($ttQuery) use ($startDate, $endDate) {
                        $ttQuery->where(\DB::raw('DATE(tanggal_tanda_terima)'), '>=', $startDate->toDateString())
                                ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '<=', $endDate->toDateString());
                    });
                })
                ->orWhere(function($subQ) use ($startDate, $endDate) {
                    // 2. Filter berdasarkan tanggal checkpoint
                    $subQ->whereNotNull('tanggal_checkpoint')
                         ->where(\DB::raw('DATE(tanggal_checkpoint)'), '>=', $startDate->toDateString())
                         ->where(\DB::raw('DATE(tanggal_checkpoint)'), '<=', $endDate->toDateString());
                });
            });

        // Filter tambahan jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $querySuratJalan->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('supir2', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('tujuan_pengiriman', 'like', "%{$search}%");
            });
            
            $querySuratJalanBongkaran->where(function($q) use ($search) {
                $q->where('nomor_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('supir2', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('tujuan_pengiriman', 'like', "%{$search}%");
            });
        }

        if ($request->filled('supir')) {
            $querySuratJalan->where(function($q) use ($request) {
                $q->where('supir', 'like', "%{$request->supir}%")
                  ->orWhere('supir2', 'like', "%{$request->supir}%");
            });
            
            $querySuratJalanBongkaran->where(function($q) use ($request) {
                $q->where('supir', 'like', "%{$request->supir}%")
                  ->orWhere('supir2', 'like', "%{$request->supir}%");
            });
        }

        if ($request->filled('kegiatan')) {
            $querySuratJalan->where('kegiatan', $request->kegiatan);
            $querySuratJalanBongkaran->where('kegiatan', $request->kegiatan);
        }

        // Get data dari kedua tabel
        $suratJalansBiasa = $querySuratJalan->with(['tandaTerima'])->get();
        $suratJalansBongkaran = $querySuratJalanBongkaran->with(['tandaTerima'])->get();

        // Gabungkan dan transform data agar konsisten
        $allSuratJalans = collect();
        
        foreach ($suratJalansBiasa as $sj) {
            $allSuratJalans->push([
                'type' => 'regular',
                'tanggal_surat_jalan' => $sj->tanggal_surat_jalan,
                'tanggal_checkpoint' => $sj->tanggal_checkpoint,
                'tanggal_tanda_terima' => $sj->tandaTerima ? $sj->tandaTerima->tanggal : null,
                'no_surat_jalan' => $sj->no_surat_jalan,
                'kegiatan' => $sj->kegiatan,
                'supir' => $sj->supir ?: $sj->supir2,
                'no_plat' => $sj->no_plat,
                'pengirim' => $sj->pengirim,
                'penerima' => $sj->tujuan_pengiriman,
                'jenis_barang' => $sj->jenis_barang,
                'tipe_kontainer' => $sj->tipe_kontainer ?: $sj->size,
                'rit' => $sj->rit,
                'order' => $sj->order,
                'created_at' => $sj->created_at,
            ]);
        }
        
        foreach ($suratJalansBongkaran as $sjb) {
            $allSuratJalans->push([
                'type' => 'bongkaran',
                'tanggal_surat_jalan' => $sjb->tanggal_surat_jalan,
                'tanggal_checkpoint' => $sjb->tanggal_checkpoint,
                'tanggal_tanda_terima' => $sjb->tandaTerima ? $sjb->tandaTerima->tanggal_tanda_terima : null,
                'no_surat_jalan' => $sjb->nomor_surat_jalan,
                'kegiatan' => $sjb->kegiatan,
                'supir' => $sjb->supir ?: $sjb->supir2,
                'no_plat' => $sjb->no_plat,
                'pengirim' => $sjb->pengirim,
                'penerima' => $sjb->tujuan_pengiriman,
                'jenis_barang' => $sjb->jenis_barang,
                'tipe_kontainer' => $sjb->tipe_kontainer ?: $sjb->size,
                'rit' => $sjb->rit,
                'order' => null,
                'created_at' => $sjb->created_at,
            ]);
        }
        
        $suratJalans = $allSuratJalans->sortByDesc(function($item) {
            return $item['tanggal_surat_jalan'] . ' ' . $item['created_at'];
        });

        $filename = 'Report_Rit_' . $startDate->format('d-m-Y') . '_to_' . $endDate->format('d-m-Y') . '.xlsx';

        return \Excel::download(new \App\Exports\ReportRitExport($suratJalans, $startDate, $endDate), $filename);
    }
}
