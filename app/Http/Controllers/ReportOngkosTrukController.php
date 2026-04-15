<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use App\Models\Mobil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportOngkosTrukExport;
use App\Models\InvoiceAktivitasLain;
use App\Models\PembayaranAktivitasLain;

class ReportOngkosTrukController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        // Get unique plat numbers from both tables
        $platsSj = SuratJalan::select('no_plat')->distinct()->whereNotNull('no_plat')->pluck('no_plat');
        $platsSjb = SuratJalanBongkaran::select('no_plat')->distinct()->whereNotNull('no_plat')->pluck('no_plat');
        
        $allPlatStrings = $platsSj->merge($platsSjb)->unique()->toArray();
        
        // Get Mobil data for the plates
        $mobils = Mobil::whereIn('nomor_polisi', $allPlatStrings)->get();
        
        // Any plates not in Mobil table (if any)
        $knownPlats = $mobils->pluck('nomor_polisi')->toArray();
        $unknownPlats = array_diff($allPlatStrings, $knownPlats);

        return view('report-ongkos-truk.select-date', compact('mobils', 'unknownPlats'));
    }

    public function view(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'no_plat' => 'nullable|array'
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $noPlat = $request->no_plat;

        $querySj = SuratJalan::where(function($q) use ($startDate, $endDate) {
            $q->whereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
                $tt->whereBetween('tanggal', [$startDate, $endDate]);
            })->orWhere(function($q2) use ($startDate, $endDate) {
                $q2->doesntHave('tandaTerima')
                    ->whereBetween('tanggal_surat_jalan', [$startDate, $endDate]);
            });
        });

        $querySjb = SuratJalanBongkaran::where(function($q) use ($startDate, $endDate) {
            $q->whereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
                $tt->whereBetween('tanggal_tanda_terima', [$startDate, $endDate]);
            })->orWhere(function($q2) use ($startDate, $endDate) {
                $q2->doesntHave('tandaTerima')
                    ->whereBetween('tanggal_surat_jalan', [$startDate, $endDate]);
            });
        });

        if ($noPlat && count($noPlat) > 0) {
            $querySj->whereIn('no_plat', $noPlat);
            $querySjb->whereIn('no_plat', $noPlat);
        }

        $suratJalans = $querySj->with(['tandaTerima', 'order', 'tujuanPengambilanRelation', 'uangJalan.pranotaUangJalan.pembayaranPranotaUangJalans'])->get();
        $suratJalanBongkarans = $querySjb->with(['tandaTerima', 'tujuanPengambilanRelation', 'uangJalan.pranotaUangJalan.pembayaranPranotaUangJalans'])->get();

        // Fetch all adjustments in bulk to avoid N+1
        $sjIds = $suratJalans->pluck('id');
        $sjbIds = $suratJalanBongkarans->pluck('id');
        $allNoSjs = $suratJalans->pluck('no_surat_jalan')->merge($suratJalanBongkarans->pluck('nomor_surat_jalan'))->unique();

        $adjInvoices = InvoiceAktivitasLain::with('pembayarans')->whereIn('surat_jalan_id', $sjIds->merge($sjbIds))
            ->where(function($q) {
                $q->where('jenis_aktivitas', 'like', '%Adjusment%')
                  ->orWhere('jenis_aktivitas', 'like', '%Adjustment%');
            })->where('tipe_penyesuaian', 'not like', '%krani%')->get()->groupBy('surat_jalan_id');

        $adjPembayarans = PembayaranAktivitasLain::whereIn('no_surat_jalan', $allNoSjs)
            ->where('tipe_penyesuaian', 'not like', '%krani%')
            ->get();
            
        // Fetch direct payments that link to invoices via invoice_ids (globally, because payment might lack no_surat_jalan)
        $directPayments = PembayaranAktivitasLain::whereNotNull('invoice_ids')->get();
        // Pre-build invoice to direct payment mapping
        $dpByInvoiceId = [];
        foreach ($directPayments as $dp) {
            // Handle comma-separated string (e.g. "1,2,3")
            $ids = explode(',', $dp->invoice_ids);
            foreach ($ids as $id) {
                $trimmedId = trim($id);
                if ($trimmedId) {
                    $dpByInvoiceId[$trimmedId][] = $dp;
                }
            }
            
            // Also handle JSON format just in case
            try {
                $jsonIds = json_decode($dp->invoice_ids, true);
                if (is_array($jsonIds)) {
                    foreach ($jsonIds as $id) {
                        $dpByInvoiceId[$id][] = $dp;
                    }
                }
            } catch (\Exception $e) {}
        }
        
        $adjPembayaransGrouped = $adjPembayarans->filter(function($dp) {
            $type = strtolower($dp->jenis_aktivitas ?? '');
            return str_contains($type, 'adjusment') || str_contains($type, 'adjustment');
        })->groupBy('no_surat_jalan');

        // Fetch direct PAL payments that have no_surat_jalan + nomor_accurate (for main row bukti)
        $palNomorAccurateBySj = PembayaranAktivitasLain::whereIn('no_surat_jalan', $allNoSjs)
            ->whereNotNull('nomor_accurate')
            ->where('nomor_accurate', '!=', '')
            ->get()
            ->groupBy('no_surat_jalan');

        $data = collect();

        foreach ($suratJalans as $sj) {
            $ongkosTruk = $this->calculateOngkosTruk($sj);
            $totalUangJalan = $sj->uangJalan ? $sj->uangJalan->jumlah_total : 0;
            
            // Collect adjustments for this SJ
            $sjAdjs = collect();
            if (isset($adjInvoices[$sj->id])) $sjAdjs = $sjAdjs->merge($adjInvoices[$sj->id]);
            if (isset($adjPembayaransGrouped[$sj->no_surat_jalan])) $sjAdjs = $sjAdjs->merge($adjPembayaransGrouped[$sj->no_surat_jalan]);

            // Calculate total addition applied to Uang Jalan in DB
            // Only 'penambahan' invoices of specific type increment the DB
            $appliedAdjNominal = 0;
            foreach ($sjAdjs as $adj) {
                $isInvoice = ($adj instanceof \App\Models\InvoiceAktivitasLain);
                $isAddition = (strtolower($adj->jenis_penyesuaian ?? '') === 'penambahan');
                $isUjType = ($adj->jenis_aktivitas === 'Pembayaran Adjustment Uang Jalan');
                
                if ($isInvoice && $isAddition && $isUjType) {
                    $nominal = (float)($adj->grand_total ?: ($adj->total ?: 0));
                    $appliedAdjNominal += $nominal;
                }
            }

            $tanggal = ($sj->tandaTerima && $sj->tandaTerima->tanggal) ? $sj->tandaTerima->tanggal : $sj->tanggal_surat_jalan;

            $mainNomorBukti = '-';
            if ($sj->uangJalan && count($sj->uangJalan->pranotaUangJalan) > 0) {
                $buktis = collect();
                foreach ($sj->uangJalan->pranotaUangJalan as $pranota) {
                    if ($pranota->pembayaranPranotaUangJalans) {
                        $buktis = $buktis->merge($pranota->pembayaranPranotaUangJalans->pluck('nomor_accurate'));
                    }
                }
                $mainNomorBukti = $buktis->filter()->unique()->implode(', ') ?: '-';
            }
            // Fallback: cek langsung dari PembayaranAktivitasLain yang punya no_surat_jalan + nomor_accurate
            if ($mainNomorBukti === '-' && isset($palNomorAccurateBySj[$sj->no_surat_jalan])) {
                $accNums = $palNomorAccurateBySj[$sj->no_surat_jalan]->pluck('nomor_accurate')->filter()->unique();
                if ($accNums->isNotEmpty()) {
                    $mainNomorBukti = $accNums->implode(', ');
                }
            }

            // Main Row (Base Amount - Before Adjustment)
            $data->push([
                'tanggal' => $tanggal,
                'no_surat_jalan' => $sj->no_surat_jalan,
                'no_plat' => $sj->no_plat,
                'supir' => $sj->supir ?: ($sj->supir2 ?: '-'),
                'keterangan' => ($sj->pengirim ?? '-') . ' ke ' . ($sj->tujuan_pengiriman ?? '-'),
                'tujuan' => $sj->tujuan_pengambilan ?? '-',
                'rit' => $sj->rit,
                'ongkos_truck' => $ongkosTruk,
                'uang_jalan' => $totalUangJalan - $appliedAdjNominal,
                'nomor_bukti' => $mainNomorBukti,
                'type' => 'regular',
                'has_tanda_terima' => $sj->tandaTerima ? true : false,
                'id' => $sj->id,
                'model_type' => 'SuratJalan',
            ]);

            // Adjustment Rows
            foreach ($sjAdjs as $adj) {
                $nominal = (float)($adj->grand_total ?: ($adj->total ?: (isset($adj->jumlah) ? $adj->jumlah : 0)));
                
                $adjDate = $adj->tanggal_invoice ?? ($adj->tanggal ?? $tanggal);
                $adjNomorAccurate = '';
                if ($adj->nomor_accurate) {
                    $adjNomorAccurate = $adj->nomor_accurate;
                }
                
                if ($adj instanceof \App\Models\InvoiceAktivitasLain) {
                    $accNums = collect();
                    if ($adj->nomor_accurate) $accNums->push($adj->nomor_accurate);
                    
                    // From many-to-many relationship
                    $accNums = $accNums->merge($adj->pembayarans->pluck('nomor_accurate'));
                    
                    // From direct payments (invoice_ids link)
                    if (isset($dpByInvoiceId[$adj->id])) {
                        foreach ($dpByInvoiceId[$adj->id] as $dp) {
                            if ($dp->nomor_accurate) $accNums->push($dp->nomor_accurate);
                        }
                    }
                    $adjNomorAccurate = $accNums->filter()->unique()->implode(', ');
                }
                
                // Priority: Specific Payment > Parent Payment > Internal ID
                if ($adjNomorAccurate) {
                    $nomorBukti = $adjNomorAccurate;
                } elseif ($mainNomorBukti !== '-') {
                    $nomorBukti = $mainNomorBukti;
                } else {
                    $nomorBukti = $adj->nomor_invoice ?: ($adj->nomor ?: '-');
                }

                $data->push([
                    'tanggal' => $adjDate,
                    'no_surat_jalan' => $sj->no_surat_jalan,
                    'no_plat' => $sj->no_plat,
                    'supir' => $sj->supir ?: ($sj->supir2 ?: '-'),
                    'keterangan' => $adj->jenis_aktivitas,
                    'tujuan' => '-',
                    'rit' => '-',
                    'ongkos_truck' => 0,
                    'uang_jalan' => $nominal,
                    'nomor_bukti' => $nomorBukti,
                    'type' => 'regular_adj',
                    'has_tanda_terima' => $sj->tandaTerima ? true : false,
                    'id' => $adj->id,
                    'model_type' => ($adj instanceof \App\Models\InvoiceAktivitasLain) ? 'InvoiceAktivitasLain' : 'PembayaranAktivitasLain',
                ]);
            }
        }

        foreach ($suratJalanBongkarans as $sjb) {
            $ongkosTruk = $this->calculateOngkosTruk($sjb);
            $totalUangJalan = $sjb->uangJalan ? $sjb->uangJalan->jumlah_total : 0;

            // Collect adjustments for this SJB
            $sjbAdjs = collect();
            if (isset($adjInvoices[$sjb->id])) $sjbAdjs = $sjbAdjs->merge($adjInvoices[$sjb->id]);
            if (isset($adjPembayaransGrouped[$sjb->nomor_surat_jalan])) $sjbAdjs = $sjbAdjs->merge($adjPembayaransGrouped[$sjb->nomor_surat_jalan]);

            $appliedAdjNominal = 0;
            foreach ($sjbAdjs as $adj) {
                $isInvoice = ($adj instanceof \App\Models\InvoiceAktivitasLain);
                $isAddition = (strtolower($adj->jenis_penyesuaian ?? '') === 'penambahan');
                $isUjType = ($adj->jenis_aktivitas === 'Pembayaran Adjustment Uang Jalan');
                
                if ($isInvoice && $isAddition && $isUjType) {
                    $nominal = (float)($adj->grand_total ?: ($adj->total ?: 0));
                    $appliedAdjNominal += $nominal;
                }
            }

            $tanggal = ($sjb->tandaTerima && $sjb->tandaTerima->tanggal_tanda_terima) ? $sjb->tandaTerima->tanggal_tanda_terima : $sjb->tanggal_surat_jalan;

            $mainNomorBukti = '-';
            if ($sjb->uangJalan && count($sjb->uangJalan->pranotaUangJalan) > 0) {
                $buktis = collect();
                foreach ($sjb->uangJalan->pranotaUangJalan as $pranota) {
                    if ($pranota->pembayaranPranotaUangJalans) {
                        $buktis = $buktis->merge($pranota->pembayaranPranotaUangJalans->pluck('nomor_accurate'));
                    }
                }
                $mainNomorBukti = $buktis->filter()->unique()->implode(', ') ?: '-';
            }
            // Fallback: cek langsung dari PembayaranAktivitasLain yang punya no_surat_jalan + nomor_accurate
            if ($mainNomorBukti === '-' && isset($palNomorAccurateBySj[$sjb->nomor_surat_jalan])) {
                $accNums = $palNomorAccurateBySj[$sjb->nomor_surat_jalan]->pluck('nomor_accurate')->filter()->unique();
                if ($accNums->isNotEmpty()) {
                    $mainNomorBukti = $accNums->implode(', ');
                }
            }

            // Main Row
            $data->push([
                'tanggal' => $tanggal,
                'no_surat_jalan' => $sjb->nomor_surat_jalan,
                'no_plat' => $sjb->no_plat,
                'supir' => $sjb->supir ?: ($sjb->supir2 ?: '-'),
                'keterangan' => ($sjb->pengirim ?? '-') . ' ke ' . ($sjb->tujuan_pengiriman ?? '-'),
                'tujuan' => $sjb->tujuan_pengambilan ?? '-',
                'rit' => $sjb->rit,
                'ongkos_truck' => $ongkosTruk,
                'uang_jalan' => $totalUangJalan - $appliedAdjNominal,
                'nomor_bukti' => $mainNomorBukti,
                'type' => 'bongkaran',
                'has_tanda_terima' => $sjb->tandaTerima ? true : false,
                'id' => $sjb->id,
                'model_type' => 'SuratJalanBongkaran',
            ]);

            // Adjustment Rows
            foreach ($sjbAdjs as $adj) {
                $nominal = (float)($adj->grand_total ?: ($adj->total ?: (isset($adj->jumlah) ? $adj->jumlah : 0)));
                
                $adjDate = $adj->tanggal_invoice ?? ($adj->tanggal ?? $tanggal);
                $adjNomorAccurate = '';
                if ($adj->nomor_accurate) {
                    $adjNomorAccurate = $adj->nomor_accurate;
                }
                
                if ($adj instanceof \App\Models\InvoiceAktivitasLain) {
                    $accNums = collect();
                    if ($adj->nomor_accurate) $accNums->push($adj->nomor_accurate);
                    
                    // From many-to-many relationship
                    $accNums = $accNums->merge($adj->pembayarans->pluck('nomor_accurate'));
                    
                    // From direct payments (invoice_ids link)
                    if (isset($dpByInvoiceId[$adj->id])) {
                        foreach ($dpByInvoiceId[$adj->id] as $dp) {
                            if ($dp->nomor_accurate) $accNums->push($dp->nomor_accurate);
                        }
                    }
                    $adjNomorAccurate = $accNums->filter()->unique()->implode(', ');
                }
                
                // Priority: Specific Payment > Parent Payment > Internal ID
                if ($adjNomorAccurate) {
                    $nomorBukti = $adjNomorAccurate;
                } elseif ($mainNomorBukti !== '-') {
                    $nomorBukti = $mainNomorBukti;
                } else {
                    $nomorBukti = $adj->nomor_invoice ?: ($adj->nomor ?: '-');
                }

                $data->push([
                    'tanggal' => $adjDate,
                    'no_surat_jalan' => $sjb->nomor_surat_jalan,
                    'no_plat' => $sjb->no_plat,
                    'supir' => $sjb->supir ?: ($sjb->supir2 ?: '-'),
                    'keterangan' => $adj->jenis_aktivitas,
                    'tujuan' => '-',
                    'rit' => '-',
                    'ongkos_truck' => 0,
                    'uang_jalan' => $nominal,
                    'nomor_bukti' => $nomorBukti,
                    'type' => 'bongkaran_adj',
                    'has_tanda_terima' => $sjb->tandaTerima ? true : false,
                    'id' => $adj->id,
                    'model_type' => ($adj instanceof \App\Models\InvoiceAktivitasLain) ? 'InvoiceAktivitasLain' : 'PembayaranAktivitasLain',
                ]);
            }
        }

        $data = $data->sortBy('tanggal');

        return view('report-ongkos-truk.view', compact('data', 'startDate', 'endDate', 'noPlat'));
    }

    public function print(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'no_plat' => 'nullable|array'
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $noPlat = $request->no_plat;

        $querySj = SuratJalan::whereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
            $tt->whereBetween('tanggal', [$startDate, $endDate]);
        });

        $querySjb = SuratJalanBongkaran::whereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
            $tt->whereBetween('tanggal_tanda_terima', [$startDate, $endDate]);
        });

        if ($noPlat && count($noPlat) > 0) {
            $querySj->whereIn('no_plat', $noPlat);
            $querySjb->whereIn('no_plat', $noPlat);
        }

        $suratJalans = $querySj->with(['tandaTerima', 'order', 'tujuanPengambilanRelation', 'uangJalan.pranotaUangJalan.pembayaranPranotaUangJalans'])->get();
        $suratJalanBongkarans = $querySjb->with(['tandaTerima', 'tujuanPengambilanRelation', 'uangJalan.pranotaUangJalan.pembayaranPranotaUangJalans'])->get();

        // Fetch all adjustments in bulk
        $sjIds = $suratJalans->pluck('id');
        $sjbIds = $suratJalanBongkarans->pluck('id');
        $allNoSjs = $suratJalans->pluck('no_surat_jalan')->merge($suratJalanBongkarans->pluck('nomor_surat_jalan'))->unique();

        $adjInvoices = InvoiceAktivitasLain::with('pembayarans')->whereIn('surat_jalan_id', $sjIds->merge($sjbIds))
            ->where(function($q) {
                $q->where('jenis_aktivitas', 'like', '%Adjusment%')
                  ->orWhere('jenis_aktivitas', 'like', '%Adjustment%');
            })->where('tipe_penyesuaian', 'not like', '%krani%')->get()->groupBy('surat_jalan_id');

        $adjPembayarans = PembayaranAktivitasLain::whereIn('no_surat_jalan', $allNoSjs)
            ->where('tipe_penyesuaian', 'not like', '%krani%')
            ->get();
            
        // Fetch direct payments that link to invoices via invoice_ids (globally, because payment might lack no_surat_jalan)
        $directPayments = PembayaranAktivitasLain::whereNotNull('invoice_ids')->get();
        // Pre-build invoice to direct payment mapping
        $dpByInvoiceId = [];
        foreach ($directPayments as $dp) {
            // Handle comma-separated string (e.g. "1,2,3")
            $ids = explode(',', $dp->invoice_ids);
            foreach ($ids as $id) {
                $trimmedId = trim($id);
                if ($trimmedId) {
                    $dpByInvoiceId[$trimmedId][] = $dp;
                }
            }
            
            // Also handle JSON format just in case
            try {
                $jsonIds = json_decode($dp->invoice_ids, true);
                if (is_array($jsonIds)) {
                    foreach ($jsonIds as $id) {
                        $dpByInvoiceId[$id][] = $dp;
                    }
                }
            } catch (\Exception $e) {}
        }
        
        $adjPembayaransGrouped = $adjPembayarans->filter(function($dp) {
            $type = strtolower($dp->jenis_aktivitas ?? '');
            return str_contains($type, 'adjusment') || str_contains($type, 'adjustment');
        })->groupBy('no_surat_jalan');

        $data = collect();

        foreach ($suratJalans as $sj) {
            $ongkosTruk = $this->calculateOngkosTruk($sj);
            $totalUangJalan = $sj->uangJalan ? $sj->uangJalan->jumlah_total : 0;
            
            // Collect adjustments for this SJ
            $sjAdjs = collect();
            if (isset($adjInvoices[$sj->id])) $sjAdjs = $sjAdjs->merge($adjInvoices[$sj->id]);
            if (isset($adjPembayaransGrouped[$sj->no_surat_jalan])) $sjAdjs = $sjAdjs->merge($adjPembayaransGrouped[$sj->no_surat_jalan]);

            $appliedAdjNominal = 0;
            foreach ($sjAdjs as $adj) {
                $isInvoice = ($adj instanceof \App\Models\InvoiceAktivitasLain);
                $isAddition = (strtolower($adj->jenis_penyesuaian ?? '') === 'penambahan');
                $isUjType = ($adj->jenis_aktivitas === 'Pembayaran Adjustment Uang Jalan');
                
                if ($isInvoice && $isAddition && $isUjType) {
                    $nominal = (float)($adj->grand_total ?: ($adj->total ?: 0));
                    $appliedAdjNominal += $nominal;
                }
            }

            $tanggal = ($sj->tandaTerima && $sj->tandaTerima->tanggal) ? $sj->tandaTerima->tanggal : $sj->tanggal_surat_jalan;

            $mainNomorBukti = '-';
            if ($sj->uangJalan && count($sj->uangJalan->pranotaUangJalan) > 0) {
                $buktis = collect();
                foreach ($sj->uangJalan->pranotaUangJalan as $pranota) {
                    if ($pranota->pembayaranPranotaUangJalans) {
                        $buktis = $buktis->merge($pranota->pembayaranPranotaUangJalans->pluck('nomor_accurate'));
                    }
                }
                $mainNomorBukti = $buktis->filter()->unique()->implode(', ') ?: '-';
            }

            // Main Row
            $data->push([
                'tanggal' => $tanggal,
                'no_surat_jalan' => $sj->no_surat_jalan,
                'no_plat' => $sj->no_plat,
                'supir' => $sj->supir ?: ($sj->supir2 ?: '-'),
                'keterangan' => ($sj->pengirim ?? '-') . ' ke ' . ($sj->tujuan_pengiriman ?? '-'),
                'tujuan' => $sj->tujuan_pengambilan ?? '-',
                'rit' => $sj->rit,
                'ongkos_truck' => $ongkosTruk,
                'uang_jalan' => $totalUangJalan - $appliedAdjNominal,
                'nomor_bukti' => $mainNomorBukti,
                'type' => 'regular'
            ]);

            // Adjustment Rows
            foreach ($sjAdjs as $adj) {
                $nominal = (float)($adj->grand_total ?: ($adj->total ?: (isset($adj->jumlah) ? $adj->jumlah : 0)));
                
                $adjDate = $adj->tanggal_invoice ?? ($adj->tanggal ?? $tanggal);
                $adjNomorAccurate = '';
                if ($adj->nomor_accurate) {
                    $adjNomorAccurate = $adj->nomor_accurate;
                }
                
                if ($adj instanceof \App\Models\InvoiceAktivitasLain) {
                    $accNums = collect();
                    if ($adj->nomor_accurate) $accNums->push($adj->nomor_accurate);
                    
                    // From many-to-many relationship
                    $accNums = $accNums->merge($adj->pembayarans->pluck('nomor_accurate'));
                    
                    // From direct payments (invoice_ids link)
                    if (isset($dpByInvoiceId[$adj->id])) {
                        foreach ($dpByInvoiceId[$adj->id] as $dp) {
                            if ($dp->nomor_accurate) $accNums->push($dp->nomor_accurate);
                        }
                    }
                    $adjNomorAccurate = $accNums->filter()->unique()->implode(', ');
                }
                
                // Priority: Specific Payment > Parent Payment > Internal ID
                if ($adjNomorAccurate) {
                    $nomorBukti = $adjNomorAccurate;
                } elseif ($mainNomorBukti !== '-') {
                    $nomorBukti = $mainNomorBukti;
                } else {
                    $nomorBukti = $adj->nomor_invoice ?: ($adj->nomor ?: '-');
                }

                $data->push([
                    'tanggal' => $adjDate,
                    'no_surat_jalan' => $sj->no_surat_jalan,
                    'no_plat' => $sj->no_plat,
                    'supir' => $sj->supir ?: ($sj->supir2 ?: '-'),
                    'keterangan' => $adj->jenis_aktivitas,
                    'tujuan' => '-',
                    'rit' => '-',
                    'ongkos_truck' => 0,
                    'uang_jalan' => $nominal,
                    'nomor_bukti' => $nomorBukti,
                    'type' => 'regular_adj'
                ]);
            }
        }

        foreach ($suratJalanBongkarans as $sjb) {
            $ongkosTruk = $this->calculateOngkosTruk($sjb);
            $totalUangJalan = $sjb->uangJalan ? $sjb->uangJalan->jumlah_total : 0;

            // Collect adjustments for this SJB
            $sjbAdjs = collect();
            if (isset($adjInvoices[$sjb->id])) $sjbAdjs = $sjbAdjs->merge($adjInvoices[$sjb->id]);
            if (isset($adjPembayaransGrouped[$sjb->nomor_surat_jalan])) $sjbAdjs = $sjbAdjs->merge($adjPembayaransGrouped[$sjb->nomor_surat_jalan]);

            $appliedAdjNominal = 0;
            foreach ($sjbAdjs as $adj) {
                $isInvoice = ($adj instanceof \App\Models\InvoiceAktivitasLain);
                $isAddition = (strtolower($adj->jenis_penyesuaian ?? '') === 'penambahan');
                $isUjType = ($adj->jenis_aktivitas === 'Pembayaran Adjustment Uang Jalan');
                
                if ($isInvoice && $isAddition && $isUjType) {
                    $nominal = (float)($adj->grand_total ?: ($adj->total ?: 0));
                    $appliedAdjNominal += $nominal;
                }
            }

            $tanggal = ($sjb->tandaTerima && $sjb->tandaTerima->tanggal_tanda_terima) ? $sjb->tandaTerima->tanggal_tanda_terima : $sjb->tanggal_surat_jalan;

            // Main Row
            $data->push([
                'tanggal' => $tanggal,
                'no_surat_jalan' => $sjb->nomor_surat_jalan,
                'no_plat' => $sjb->no_plat,
                'supir' => $sjb->supir ?: ($sjb->supir2 ?: '-'),
                'keterangan' => ($sjb->pengirim ?? '-') . ' ke ' . ($sjb->tujuan_pengiriman ?? '-'),
                'tujuan' => $sjb->tujuan_pengambilan ?? '-',
                'rit' => $sjb->rit,
                'ongkos_truck' => $ongkosTruk,
                'uang_jalan' => $totalUangJalan - $appliedAdjNominal,
                'type' => 'bongkaran'
            ]);

            // Adjustment Rows
            foreach ($sjbAdjs as $adj) {
                $nominal = (float)($adj->grand_total ?: ($adj->total ?: (isset($adj->jumlah) ? $adj->jumlah : 0)));
                
                $adjDate = $adj->tanggal_invoice ?? ($adj->tanggal ?? $tanggal);
                $adjNomorAccurate = '';
                if ($adj->nomor_accurate) {
                    $adjNomorAccurate = $adj->nomor_accurate;
                }
                
                if ($adj instanceof \App\Models\InvoiceAktivitasLain) {
                    $accNums = collect();
                    if ($adj->nomor_accurate) $accNums->push($adj->nomor_accurate);
                    
                    // From many-to-many relationship
                    $accNums = $accNums->merge($adj->pembayarans->pluck('nomor_accurate'));
                    
                    // From direct payments (invoice_ids link)
                    if (isset($dpByInvoiceId[$adj->id])) {
                        foreach ($dpByInvoiceId[$adj->id] as $dp) {
                            if ($dp->nomor_accurate) $accNums->push($dp->nomor_accurate);
                        }
                    }
                    $adjNomorAccurate = $accNums->filter()->unique()->implode(', ');
                }
                
                // Priority: Specific Payment > Parent Payment > Internal ID
                if ($adjNomorAccurate) {
                    $nomorBukti = $adjNomorAccurate;
                } elseif ($mainNomorBukti !== '-') {
                    $nomorBukti = $mainNomorBukti;
                } else {
                    $nomorBukti = $adj->nomor_invoice ?: ($adj->nomor ?: '-');
                }

                $data->push([
                    'tanggal' => $adjDate,
                    'no_surat_jalan' => $sjb->nomor_surat_jalan,
                    'no_plat' => $sjb->no_plat,
                    'supir' => $sjb->supir ?: ($sjb->supir2 ?: '-'),
                    'keterangan' => $adj->jenis_aktivitas,
                    'tujuan' => '-',
                    'rit' => '-',
                    'ongkos_truck' => 0,
                    'uang_jalan' => $nominal,
                    'nomor_bukti' => $nomorBukti,
                    'type' => 'bongkaran_adj'
                ]);
            }
        }

        $data = $data->sortBy('tanggal');

        return view('report-ongkos-truk.view', compact('data', 'startDate', 'endDate', 'noPlat'))->with('isPrint', true);
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'no_plat' => 'nullable|array'
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $noPlat = $request->no_plat;

        $querySj = SuratJalan::whereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
            $tt->whereBetween('tanggal', [$startDate, $endDate]);
        });

        $querySjb = SuratJalanBongkaran::whereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
            $tt->whereBetween('tanggal_tanda_terima', [$startDate, $endDate]);
        });

        if ($noPlat && count($noPlat) > 0) {
            $querySj->whereIn('no_plat', $noPlat);
            $querySjb->whereIn('no_plat', $noPlat);
        }


        $suratJalans = $querySj->with(['tandaTerima', 'order', 'tujuanPengambilanRelation', 'uangJalan.pranotaUangJalan.pembayaranPranotaUangJalans', 'supirKaryawan', 'supir2Karyawan', 'kenekKaryawan'])->get();
        $suratJalanBongkarans = $querySjb->with(['tandaTerima', 'tujuanPengambilanRelation', 'uangJalan.pranotaUangJalan.pembayaranPranotaUangJalans', 'supirKaryawan', 'supir2Karyawan', 'kenekKaryawan'])->get();

        // Fetch all adjustments in bulk to avoid N+1
        $sjIds = $suratJalans->pluck('id');
        $sjbIds = $suratJalanBongkarans->pluck('id');
        $allNoSjs = $suratJalans->pluck('no_surat_jalan')->merge($suratJalanBongkarans->pluck('nomor_surat_jalan'))->unique();

        $adjInvoices = InvoiceAktivitasLain::with('pembayarans')->whereIn('surat_jalan_id', $sjIds->merge($sjbIds))
            ->where(function($q) {
                $q->where('jenis_aktivitas', 'like', '%Adjusment%')
                  ->orWhere('jenis_aktivitas', 'like', '%Adjustment%');
            })->where('tipe_penyesuaian', 'not like', '%krani%')->get()->groupBy('surat_jalan_id');

        $adjPembayarans = PembayaranAktivitasLain::whereIn('no_surat_jalan', $allNoSjs)
            ->where('tipe_penyesuaian', 'not like', '%krani%')
            ->get();
            
        // Fetch direct payments that link to invoices via invoice_ids (globally, because payment might lack no_surat_jalan)
        $directPayments = PembayaranAktivitasLain::whereNotNull('invoice_ids')->get();
        // Pre-build invoice to direct payment mapping
        $dpByInvoiceId = [];
        foreach ($directPayments as $dp) {
            // Handle comma-separated string (e.g. "1,2,3")
            $ids = explode(',', $dp->invoice_ids);
            foreach ($ids as $id) {
                $trimmedId = trim($id);
                if ($trimmedId) {
                    $dpByInvoiceId[$trimmedId][] = $dp;
                }
            }
            
            // Also handle JSON format just in case
            try {
                $jsonIds = json_decode($dp->invoice_ids, true);
                if (is_array($jsonIds)) {
                    foreach ($jsonIds as $id) {
                        $dpByInvoiceId[$id][] = $dp;
                    }
                }
            } catch (\Exception $e) {}
        }
        
        $adjPembayaransGrouped = $adjPembayarans->filter(function($dp) {
            $type = strtolower($dp->jenis_aktivitas ?? '');
            return str_contains($type, 'adjusment') || str_contains($type, 'adjustment');
        })->groupBy('no_surat_jalan');

        $data = collect();

        foreach ($suratJalans as $sj) {
            $ongkosTruk = $this->calculateOngkosTruk($sj);
            $totalUangJalanRaw = $sj->uangJalan ? $sj->uangJalan->jumlah_total : 0;

            // Collect adjustments for this SJ
            $sjAdjs = collect();
            if (isset($adjInvoices[$sj->id])) $sjAdjs = $sjAdjs->merge($adjInvoices[$sj->id]);
            if (isset($adjPembayaransGrouped[$sj->no_surat_jalan])) $sjAdjs = $sjAdjs->merge($adjPembayaransGrouped[$sj->no_surat_jalan]);

            $appliedAdjNominal = 0;
            foreach ($sjAdjs as $adj) {
                $isInvoice = ($adj instanceof \App\Models\InvoiceAktivitasLain);
                $isAddition = (strtolower($adj->jenis_penyesuaian ?? '') === 'penambahan');
                $isUjType = ($adj->jenis_aktivitas === 'Pembayaran Adjustment Uang Jalan');
                
                if ($isInvoice && $isAddition && $isUjType) {
                    $nominal = (float)($adj->grand_total ?: ($adj->total ?: 0));
                    $appliedAdjNominal += $nominal;
                }
            }

            $mainNomorBukti = '-';
            if ($sj->uangJalan && count($sj->uangJalan->pranotaUangJalan) > 0) {
                $buktis = collect();
                foreach ($sj->uangJalan->pranotaUangJalan as $pranota) {
                    if ($pranota->pembayaranPranotaUangJalans) {
                        $buktis = $buktis->merge($pranota->pembayaranPranotaUangJalans->pluck('nomor_accurate'));
                    }
                }
                $mainNomorBukti = $buktis->filter()->unique()->implode(', ') ?: '-';
            }

            $driverData = $this->resolveDriverData($sj);
            $tanggal = ($sj->tandaTerima && $sj->tandaTerima->tanggal) ? $sj->tandaTerima->tanggal : $sj->tanggal_surat_jalan;

            // Main Row
            $data->push([
                'tanggal' => $tanggal,
                'no_surat_jalan' => $sj->no_surat_jalan,
                'no_plat' => $sj->no_plat,
                'nama_lengkap_supir' => $driverData['nama'],
                'nik_supir' => $driverData['nik'],
                'nama_lengkap_kenek' => $sj->kenekKaryawan ? $sj->kenekKaryawan->nama_lengkap : ($sj->kenek ?: '-'),
                'nik_kenek' => $sj->kenek_nik,
                'rit_supir' => ($sj->supir || $sj->supir2 || $sj->supirKaryawan) ? 1 : 0,
                'rit_kenek' => ($sj->kenek || $sj->kenekKaryawan) ? 1 : 0,
                'supir' => $sj->supir ?: ($sj->supir2 ?: '-'),
                'keterangan' => ($sj->pengirim ?? '-') . ' ke ' . ($sj->tujuan_pengiriman ?? '-'),
                'tujuan' => $sj->tujuan_pengambilan ?? '-',
                'rit' => $sj->rit,
                'ongkos_truck' => $ongkosTruk,
                'uang_jalan' => $totalUangJalanRaw - $appliedAdjNominal,
                'nomor_bukti' => $mainNomorBukti
            ]);

            // Adjustment Rows
            foreach ($sjAdjs as $adj) {
                $nominal = (float)($adj->grand_total ?: ($adj->total ?: (isset($adj->jumlah) ? $adj->jumlah : 0)));
                
                $adjDate = $adj->tanggal_invoice ?? ($adj->tanggal ?? $tanggal);
                $adjNomorAccurate = '';
                if ($adj->nomor_accurate) {
                    $adjNomorAccurate = $adj->nomor_accurate;
                }
                
                if ($adj instanceof \App\Models\InvoiceAktivitasLain) {
                    $accNums = collect();
                    if ($adj->nomor_accurate) $accNums->push($adj->nomor_accurate);
                    
                    // From many-to-many relationship
                    $accNums = $accNums->merge($adj->pembayarans->pluck('nomor_accurate'));
                    
                    // From direct payments (invoice_ids link)
                    if (isset($dpByInvoiceId[$adj->id])) {
                        foreach ($dpByInvoiceId[$adj->id] as $dp) {
                            if ($dp->nomor_accurate) $accNums->push($dp->nomor_accurate);
                        }
                    }
                    $adjNomorAccurate = $accNums->filter()->unique()->implode(', ');
                }
                
                // Priority: Specific Payment > Parent Payment > Internal ID
                if ($adjNomorAccurate) {
                    $nomorBukti = $adjNomorAccurate;
                } elseif ($mainNomorBukti !== '-') {
                    $nomorBukti = $mainNomorBukti;
                } else {
                    $nomorBukti = $adj->nomor_invoice ?: ($adj->nomor ?: '-');
                }

                $data->push([
                    'tanggal' => $adjDate,
                    'no_surat_jalan' => $sj->no_surat_jalan,
                    'no_plat' => $sj->no_plat,
                    'nama_lengkap_supir' => $driverData['nama'],
                    'nik_supir' => $driverData['nik'],
                    'nama_lengkap_kenek' => $sj->kenekKaryawan ? $sj->kenekKaryawan->nama_lengkap : ($sj->kenek ?: '-'),
                    'nik_kenek' => $sj->kenek_nik,
                    'rit_supir' => 0,
                    'rit_kenek' => 0,
                    'supir' => $sj->supir ?: ($sj->supir2 ?: '-'),
                    'keterangan' => $adj->jenis_aktivitas,
                    'tujuan' => '-',
                    'rit' => '-',
                    'ongkos_truck' => 0,
                    'uang_jalan' => $nominal,
                    'nomor_bukti' => $nomorBukti
                ]);
            }
        }

        foreach ($suratJalanBongkarans as $sjb) {
            $ongkosTruk = $this->calculateOngkosTruk($sjb);
            $totalUangJalanRaw = $sjb->uangJalan ? $sjb->uangJalan->jumlah_total : 0;

            // Collect adjustments for this SJB
            $sjbAdjs = collect();
            if (isset($adjInvoices[$sjb->id])) $sjbAdjs = $sjbAdjs->merge($adjInvoices[$sjb->id]);
            if (isset($adjPembayaransGrouped[$sjb->nomor_surat_jalan])) $sjbAdjs = $sjbAdjs->merge($adjPembayaransGrouped[$sjb->nomor_surat_jalan]);

            $appliedAdjNominal = 0;
            foreach ($sjbAdjs as $adj) {
                $isInvoice = ($adj instanceof \App\Models\InvoiceAktivitasLain);
                $isAddition = (strtolower($adj->jenis_penyesuaian ?? '') === 'penambahan');
                $isUjType = ($adj->jenis_aktivitas === 'Pembayaran Adjustment Uang Jalan');
                
                if ($isInvoice && $isAddition && $isUjType) {
                    $nominal = (float)($adj->grand_total ?: ($adj->total ?: 0));
                    $appliedAdjNominal += $nominal;
                }
            }

            $mainNomorBukti = '-';
            if ($sjb->uangJalan && count($sjb->uangJalan->pranotaUangJalan) > 0) {
                $buktis = collect();
                foreach ($sjb->uangJalan->pranotaUangJalan as $pranota) {
                    if ($pranota->pembayaranPranotaUangJalans) {
                        $buktis = $buktis->merge($pranota->pembayaranPranotaUangJalans->pluck('nomor_accurate'));
                    }
                }
                $mainNomorBukti = $buktis->filter()->unique()->implode(', ') ?: '-';
            }


            $driverData = $this->resolveDriverData($sjb);
            $tanggal = ($sjb->tandaTerima && $sjb->tandaTerima->tanggal_tanda_terima) ? $sjb->tandaTerima->tanggal_tanda_terima : $sjb->tanggal_surat_jalan;

            // Main Row
            $data->push([
                'tanggal' => $tanggal,
                'no_surat_jalan' => $sjb->nomor_surat_jalan,
                'no_plat' => $sjb->no_plat,
                'nama_lengkap_supir' => $driverData['nama'],
                'nik_supir' => $driverData['nik'],
                'nama_lengkap_kenek' => $sjb->kenekKaryawan ? $sjb->kenekKaryawan->nama_lengkap : ($sjb->kenek ?: '-'),
                'nik_kenek' => $sjb->kenek_nik,
                'rit_supir' => ($sjb->supir || $sjb->supir2 || $sjb->supirKaryawan) ? 1 : 0,
                'rit_kenek' => ($sjb->kenek || $sjb->kenekKaryawan) ? 1 : 0,
                'supir' => $sjb->supir ?: ($sjb->supir2 ?: '-'),
                'keterangan' => ($sjb->pengirim ?? '-') . ' ke ' . ($sjb->tujuan_pengiriman ?? '-'),
                'tujuan' => $sjb->tujuan_pengambilan ?? '-',
                'rit' => $sjb->rit,
                'ongkos_truck' => $ongkosTruk,
                'uang_jalan' => $totalUangJalanRaw - $appliedAdjNominal,
                'nomor_bukti' => $mainNomorBukti
            ]);

            // Adjustment Rows
            foreach ($sjbAdjs as $adj) {
                $nominal = (float)($adj->grand_total ?: ($adj->total ?: (isset($adj->jumlah) ? $adj->jumlah : 0)));
                
                $adjDate = $adj->tanggal_invoice ?? ($adj->tanggal ?? $tanggal);
                $adjNomorAccurate = '';
                if ($adj->nomor_accurate) {
                    $adjNomorAccurate = $adj->nomor_accurate;
                }
                
                if ($adj instanceof \App\Models\InvoiceAktivitasLain) {
                    $accNums = collect();
                    if ($adj->nomor_accurate) $accNums->push($adj->nomor_accurate);
                    
                    // From many-to-many relationship
                    $accNums = $accNums->merge($adj->pembayarans->pluck('nomor_accurate'));
                    
                    // From direct payments (invoice_ids link)
                    if (isset($dpByInvoiceId[$adj->id])) {
                        foreach ($dpByInvoiceId[$adj->id] as $dp) {
                            if ($dp->nomor_accurate) $accNums->push($dp->nomor_accurate);
                        }
                    }
                    $adjNomorAccurate = $accNums->filter()->unique()->implode(', ');
                }
                
                // Priority: Specific Payment > Parent Payment > Internal ID
                if ($adjNomorAccurate) {
                    $nomorBukti = $adjNomorAccurate;
                } elseif ($mainNomorBukti !== '-') {
                    $nomorBukti = $mainNomorBukti;
                } else {
                    $nomorBukti = $adj->nomor_invoice ?: ($adj->nomor ?: '-');
                }

                $data->push([
                    'tanggal' => $adjDate,
                    'no_surat_jalan' => $sjb->nomor_surat_jalan,
                    'no_plat' => $sjb->no_plat,
                    'nama_lengkap_supir' => $driverData['nama'],
                    'nik_supir' => $driverData['nik'],
                    'nama_lengkap_kenek' => $sjb->kenekKaryawan ? $sjb->kenekKaryawan->nama_lengkap : ($sjb->kenek ?: '-'),
                    'nik_kenek' => $sjb->kenek_nik,
                    'rit_supir' => 0,
                    'rit_kenek' => 0,
                    'supir' => $sjb->supir ?: ($sjb->supir2 ?: '-'),
                    'keterangan' => $adj->jenis_aktivitas,
                    'tujuan' => '-',
                    'rit' => '-',
                    'ongkos_truck' => 0,
                    'uang_jalan' => $nominal,
                    'nomor_bukti' => $nomorBukti
                ]);
            }
        }

        $data = $data->sortBy('tanggal');

        // Format tanggal setelah di-sort
        $data = $data->map(function($item) {
            if ($item['tanggal'] instanceof \Carbon\Carbon) {
                $item['tanggal'] = $item['tanggal']->format('d/m/Y');
            }
            return $item;
        });

        $filename = 'report_ongkos_truk_' . date('Ymd_His') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ReportOngkosTrukExport($data, $startDate, $endDate), 
            $filename
        );
    }

    private function calculateOngkosTruk($item)
    {
        $ongkosTruk = 0;
        if ($item->tujuanPengambilanRelation) {
            $size = strtolower($item->size ?? '');
            if (str_contains($size, '40')) {
                $ongkosTruk = $item->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0;
            } else {
                $ongkosTruk = $item->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0;
            }
        }

        // Hardcoded override for specific destination
        if ($item->tujuan_pengambilan == "PULO GADUNG ( BESI SCRAP )") {
            $ongkosTruk = 1050000;
        }

        return $ongkosTruk;
    }

    private function resolveDriverData($item)
    {
        // Helper function to check if a karyawan is a supir
        $isSupir = function($karyawan) {
            if (!$karyawan) return false;
            $divisi = strtolower($karyawan->divisi ?? '');
            $pekerjaan = strtolower($karyawan->pekerjaan ?? '');
            return str_contains($divisi, 'supir') || str_contains($pekerjaan, 'supir');
        };

        // Collect all possible karyawan matches for supir and supir2
        $candidates = collect();

        // Search for supir field matches
        if ($item->supir) {
            $supirMatches = \App\Models\Karyawan::where(function($q) use ($item) {
                $q->where('nama_panggilan', $item->supir)
                  ->orWhere('nama_lengkap', $item->supir);
            })->get();
            $candidates = $candidates->merge($supirMatches);
        }

        // Search for supir2 field matches
        if ($item->supir2) {
            $supir2Matches = \App\Models\Karyawan::where(function($q) use ($item) {
                $q->where('nama_panggilan', $item->supir2)
                  ->orWhere('nama_lengkap', $item->supir2);
            })->get();
            $candidates = $candidates->merge($supir2Matches);
        }

        // First priority: Find a karyawan with divisi/pekerjaan supir
        $supirCandidate = $candidates->first(function($karyawan) use ($isSupir) {
            return $isSupir($karyawan);
        });

        if ($supirCandidate) {
            return [
                'nama' => $supirCandidate->nama_lengkap,
                'nik' => $supirCandidate->nik
            ];
        }

        // Second priority: Use the first candidate found
        if ($candidates->isNotEmpty()) {
            $firstCandidate = $candidates->first();
            return [
                'nama' => $firstCandidate->nama_lengkap,
                'nik' => $firstCandidate->nik
            ];
        }

        // Fallback to manual text
        return [
            'nama' => $item->supir ?: ($item->supir2 ?: '-'),
            'nik' => $item->supir_nik ?? '-'
        ];
    }
}
