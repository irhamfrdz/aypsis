<?php

namespace App\Http\Controllers;

use App\Models\InvoiceAktivitasLain;
use App\Models\PembatalanSuratJalan;
use App\Models\PembayaranAktivitasLain;
use App\Models\PembayaranPranotaOb;
use App\Models\PembayaranPranotaObAntarGudang;
use App\Models\PranotaObAntarGudang;
use App\Models\PranotaObMuatTemas;
use App\Models\UangJalan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportUangJalanController extends Controller
{
    public function index(Request $request)
    {
        return view('report-uang-jalan.select-date');
    }

    /**
     * Fetch adjustment invoices & pembayarans for a set of UangJalans.
     * Returns a collection keyed by surat_jalan_id (or surat_jalan_bongkaran_id).
     */
    private function fetchAdjustments($uangJalans)
    {
        // Collect all SJ and SJB ids from UangJalans
        $sjIds = $uangJalans->pluck('surat_jalan_id')->filter()->unique();
        $sjbIds = $uangJalans->pluck('surat_jalan_bongkaran_id')->filter()->unique();
        $allSjIds = $sjIds->merge($sjbIds)->unique();

        if ($allSjIds->isEmpty()) {
            return collect();
        }

        // Also collect no_surat_jalan strings for PembayaranAktivitasLain lookup
        $allNoSjs = collect();
        foreach ($uangJalans as $uj) {
            if ($uj->suratJalan) {
                $allNoSjs->push($uj->suratJalan->no_surat_jalan);
            }
            if ($uj->suratJalanBongkaran) {
                $allNoSjs->push($uj->suratJalanBongkaran->nomor_surat_jalan);
            }
        }
        $allNoSjs = $allNoSjs->filter()->unique();

        // Fetch adjustment invoices
        $adjInvoices = InvoiceAktivitasLain::with(['pembayarans', 'suratJalan'])
            ->whereIn('surat_jalan_id', $allSjIds)
            ->where(function ($q) {
                $q->where('jenis_aktivitas', 'like', '%Adjusment%')
                    ->orWhere('jenis_aktivitas', 'like', '%Adjustment%');
            })
            ->get();

        // Fetch direct payments (PembayaranAktivitasLain) linked via no_surat_jalan
        $adjPembayarans = collect();
        if ($allNoSjs->isNotEmpty()) {
            $adjPembayarans = PembayaranAktivitasLain::whereIn('no_surat_jalan', $allNoSjs)
                ->where(function ($q) {
                    $q->where('jenis_aktivitas', 'like', '%Adjusment%')
                        ->orWhere('jenis_aktivitas', 'like', '%Adjustment%');
                })
                ->get();
        }

        // Fetch direct payments that link to invoices via invoice_ids for nomor_accurate
        $directPayments = PembayaranAktivitasLain::whereNotNull('invoice_ids')->get();
        $dpByInvoiceId = [];
        foreach ($directPayments as $dp) {
            $ids = explode(',', $dp->invoice_ids);
            foreach ($ids as $id) {
                $trimmedId = trim($id);
                if ($trimmedId) {
                    $dpByInvoiceId[$trimmedId][] = $dp;
                }
            }
            try {
                $jsonIds = json_decode($dp->invoice_ids, true);
                if (is_array($jsonIds)) {
                    foreach ($jsonIds as $id) {
                        $dpByInvoiceId[$id][] = $dp;
                    }
                }
            } catch (\Exception $e) {
            }
        }

        // Group by surat_jalan_id for invoices
        $adjBySjId = $adjInvoices->groupBy('surat_jalan_id');

        // Group pembayarans by no_surat_jalan
        $adjPembayaransGrouped = $adjPembayarans->filter(function ($dp) {
            $type = strtolower($dp->jenis_aktivitas ?? '');

            return str_contains($type, 'adjusment') || str_contains($type, 'adjustment');
        })->groupBy('no_surat_jalan');

        // Fetch pembatalan surat jalan (cancellation = return of uang jalan)
        $pembatalanBySjId = PembatalanSuratJalan::where(function ($q) use ($sjIds, $sjbIds) {
            $q->whereIn('surat_jalan_id', $sjIds)
                ->orWhereIn('surat_jalan_bongkaran_id', $sjbIds);
        })->get()->groupBy(function ($p) {
            return $p->surat_jalan_id ?? $p->surat_jalan_bongkaran_id;
        });

        // Build final result: map each UangJalan to its adjustments
        $result = collect();
        foreach ($uangJalans as $uj) {
            $ujAdjs = collect();
            $sjId = $uj->surat_jalan_id ?? $uj->surat_jalan_bongkaran_id;
            $noSj = null;

            if ($uj->suratJalan) {
                $noSj = $uj->suratJalan->no_surat_jalan;
            } elseif ($uj->suratJalanBongkaran) {
                $noSj = $uj->suratJalanBongkaran->nomor_surat_jalan;
            }

            // Add invoice adjustments
            if ($sjId && isset($adjBySjId[$sjId])) {
                $ujAdjs = $ujAdjs->merge($adjBySjId[$sjId]);
            }

            // Add pembayaran adjustments
            if ($noSj && isset($adjPembayaransGrouped[$noSj])) {
                $ujAdjs = $ujAdjs->merge($adjPembayaransGrouped[$noSj]);
            }

            // Add pembatalan surat jalan (always treated as pengembalian)
            if ($sjId && isset($pembatalanBySjId[$sjId])) {
                foreach ($pembatalanBySjId[$sjId] as $pembatalan) {
                    // Wrap pembatalan data into a stdClass with consistent properties
                    $adjObj = new \stdClass;
                    $adjObj->_source_type = 'pembatalan';
                    $adjObj->tanggal_invoice = $pembatalan->tanggal_kas;
                    $adjObj->tanggal = $pembatalan->tanggal_kas;
                    $adjObj->nomor_invoice = $pembatalan->nomor_pembayaran ?: $pembatalan->no_surat_jalan;
                    $adjObj->nomor = $pembatalan->nomor_pembayaran;
                    $adjObj->jenis_penyesuaian = 'Pengembalian Uang Jalan (Pembatalan SJ)';
                    $adjObj->jenis_aktivitas = 'Pembatalan Surat Jalan';
                    $refundAmount = (float) ($pembatalan->total_tagihan_setelah_penyesuaian ?? 0);
                    if ($refundAmount <= 0) {
                        $refundAmount = (float) ($pembatalan->total_pembayaran ?? 0);
                    }
                    $adjObj->grand_total = $refundAmount;
                    $adjObj->total = $refundAmount;
                    $adjObj->_resolved_nomor_bukti = $pembatalan->nomor_accurate ?: '-';
                    $adjObj->alasan_batal = $pembatalan->alasan_batal;
                    $ujAdjs->push($adjObj);
                }
            }

            // For each adjustment, resolve nomor_accurate (nomor bukti)
            $ujAdjsWithBukti = $ujAdjs->map(function ($adj) use ($dpByInvoiceId) {
                // Skip if already resolved (pembatalan)
                if (isset($adj->_source_type) && $adj->_source_type === 'pembatalan') {
                    return $adj;
                }

                $nomorBukti = '-';

                if ($adj instanceof InvoiceAktivitasLain) {
                    $accNums = collect();
                    // From many-to-many pembayarans relationship
                    if ($adj->relationLoaded('pembayarans')) {
                        $accNums = $accNums->merge($adj->pembayarans->pluck('nomor_accurate'));
                    }
                    // From direct payments (invoice_ids link)
                    if (isset($dpByInvoiceId[$adj->id])) {
                        foreach ($dpByInvoiceId[$adj->id] as $dp) {
                            if ($dp->nomor_accurate) {
                                $accNums->push($dp->nomor_accurate);
                            }
                        }
                    }
                    $nomorBukti = $accNums->filter()->unique()->implode(', ') ?: '-';
                } elseif ($adj instanceof PembayaranAktivitasLain) {
                    $nomorBukti = $adj->nomor_accurate ?: '-';
                }

                $adj->_resolved_nomor_bukti = $nomorBukti;

                return $adj;
            });

            if ($ujAdjsWithBukti->isNotEmpty()) {
                $result[$uj->id] = $ujAdjsWithBukti;
            }
        }

        return $result;
    }

    private function appendStandalonePembatalans(&$uangJalans, &$adjustmentsByUjId, $startDate, $endDate, $search)
    {
        $pembatalans = PembatalanSuratJalan::where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal_kas', [$startDate, $endDate])
                ->orWhereBetween('tanggal_pembayaran', [$startDate, $endDate]);
        });

        if ($search) {
            $pembatalans->where(function ($q) use ($search) {
                $q->where('nomor_pembayaran', 'like', "%{$search}%")
                    ->orWhere('nomor_accurate', 'like', "%{$search}%")
                    ->orWhereRaw("REPLACE(nomor_accurate, ' ', '') LIKE ?", ['%'.str_replace(' ', '', $search).'%'])
                    ->orWhere('no_surat_jalan', 'like', "%{$search}%");
            });
        }

        $pembatalans = $pembatalans->get();

        foreach ($pembatalans as $pbl) {
            // Deduplicate only by the concrete standalone row ID. Checking
            // nested adjustment collections by attribute can incorrectly skip
            // unrelated cancellation records.
            // UangJalan casts its primary key to integer, so use a unique
            // negative integer instead of a string such as "pbl_21".
            $standaloneId = -((int) $pbl->id);
            if ($uangJalans->contains(fn ($uj) => (int) $uj->id === $standaloneId)) {
                continue;
            }

            // Create fake UangJalan
            $fakeUj = new UangJalan;
            $fakeUj->id = $standaloneId;
            $fakeUj->tanggal_uang_jalan = Carbon::parse($pbl->tanggal_kas);
            $fakeUj->nomor_uang_jalan = '-';
            $fakeUj->jumlah_uang_jalan = 0;
            $fakeUj->jumlah_mel = 0;
            $fakeUj->jumlah_pelancar = 0;
            $fakeUj->jumlah_kawalan = 0;
            $fakeUj->jumlah_parkir = 0;
            $fakeUj->jumlah_total = 0;

            if ($pbl->tipe_sj === 'reguler') {
                $fakeUj->surat_jalan_id = $pbl->surat_jalan_id ?? 1; // dummy truthy value
                $fakeSjModel = new \App\Models\SuratJalan;
                $fakeSjModel->no_surat_jalan = $pbl->no_surat_jalan;
                $fakeSjModel->jenis_barang = '-';
                $fakeSjModel->tujuan_pengambilan = '-';
                $fakeSjModel->supir = '-';
                $fakeSjModel->no_plat = '-';
                $fakeUj->setRelation('suratJalan', $fakeSjModel);
            } else {
                $fakeUj->surat_jalan_bongkaran_id = $pbl->surat_jalan_bongkaran_id ?? 1; // dummy truthy value
                $fakeSjModel = new \App\Models\SuratJalanBongkaran;
                $fakeSjModel->nomor_surat_jalan = $pbl->no_surat_jalan;
                $fakeSjModel->jenis_barang = '-';
                $fakeSjModel->tujuan_pengambilan = '-';
                $fakeSjModel->supir = '-';
                $fakeSjModel->no_plat = '-';
                $fakeUj->setRelation('suratJalanBongkaran', $fakeSjModel);
            }

            // Fake relations
            $fakeUj->setRelation('pranotaUangJalan', collect());

            $fakeUser = new \App\Models\User;
            $fakeUser->username = '-';
            $fakeUj->setRelation('createdBy', $fakeUser);

            $uangJalans->push($fakeUj);

            // Create adjustment obj
            $adjObj = new \stdClass;
            $adjObj->_source_type = 'pembatalan';
            $adjObj->tanggal_invoice = $pbl->tanggal_kas;
            $adjObj->tanggal = $pbl->tanggal_kas;
            $adjObj->nomor_invoice = $pbl->nomor_pembayaran ?: $pbl->no_surat_jalan;
            $adjObj->nomor = $pbl->nomor_pembayaran;
            $adjObj->jenis_penyesuaian = 'Pengembalian Uang Jalan (Pembatalan SJ)';
            $adjObj->jenis_aktivitas = 'Pembatalan Surat Jalan';
            $refundAmount = (float) ($pbl->total_tagihan_setelah_penyesuaian ?? 0);
            if ($refundAmount <= 0) {
                $refundAmount = (float) ($pbl->total_pembayaran ?? 0);
            }
            $adjObj->grand_total = $refundAmount;
            $adjObj->total = $refundAmount;
            $adjObj->_resolved_nomor_bukti = $pbl->nomor_accurate ?: '-';
            $adjObj->alasan_batal = $pbl->alasan_batal;

            $adjustmentsByUjId[$fakeUj->id] = collect([$adjObj]);
        }

        $uangJalans = $uangJalans->sortByDesc('tanggal_uang_jalan')->values();
    }

    private function appendStandalonePembayaranAktivitasLain(&$uangJalans, &$adjustmentsByUjId, $startDate, $endDate, $search)
    {
        $query = PembayaranAktivitasLain::with('creator')
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereRaw("LOWER(COALESCE(keterangan, '')) LIKE ?", ['%uang jalan%']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                    ->orWhere('nomor_accurate', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhere('penerima', 'like', "%{$search}%")
                    ->orWhere('nomor_polisi', 'like', "%{$search}%");
            });
        }

        $payments = $query->get();
        $existingPaymentIds = $adjustmentsByUjId->flatten()->filter(function ($item) {
            return $item instanceof PembayaranAktivitasLain;
        })->pluck('id')->all();

        foreach ($payments as $payment) {
            if (in_array($payment->id, $existingPaymentIds, true)) {
                continue;
            }

            $fakeUj = new UangJalan;
            $fakeUj->id = 'pal_'.$payment->id;
            $fakeUj->tanggal_uang_jalan = Carbon::parse($payment->tanggal);
            $fakeUj->nomor_uang_jalan = $payment->nomor ?: '-';
            $fakeUj->jumlah_uang_jalan = (float) ($payment->jumlah ?? 0);
            $fakeUj->jumlah_mel = 0;
            $fakeUj->jumlah_pelancar = 0;
            $fakeUj->jumlah_kawalan = 0;
            $fakeUj->jumlah_parkir = 0;
            $fakeUj->jumlah_total = (float) ($payment->jumlah ?? 0);
            $fakeUj->_source_type = 'pembayaran_aktivitas_lain';
            $fakeUj->_standalone_payment = $payment;

            $fakeSj = new \App\Models\SuratJalan;
            $fakeSj->no_surat_jalan = '-';
            $fakeSj->jenis_barang = $payment->keterangan ?: 'Pembayaran Aktivitas Lain';
            $fakeSj->tujuan_pengambilan = '-';
            $fakeSj->supir = $payment->penerima ?: '-';
            $fakeSj->no_plat = $payment->nomor_polisi ?: '-';
            $fakeUj->surat_jalan_id = 1;
            $fakeUj->setRelation('suratJalan', $fakeSj);
            $fakeUj->setRelation('pranotaUangJalan', collect());
            $fakeUj->setRelation('createdBy', $payment->creator);

            $uangJalans->push($fakeUj);
            $adjustmentsByUjId[$fakeUj->id] = collect();
        }

        $uangJalans = $uangJalans->sortByDesc('tanggal_uang_jalan')->values();
    }

    private function appendPranotaObRows(&$uangJalans, $startDate, $endDate, $search)
    {
        $sources = [
            [PranotaObAntarGudang::class, PembayaranPranotaObAntarGudang::class, 'pranota_ob_antar_gudang_ids', 'OB Antar Gudang', 1000000000],
            [PranotaObMuatTemas::class, PembayaranPranotaOb::class, 'pranota_ob_muat_temas_ids', 'OB Muat Temas', 2000000000],
        ];

        foreach ($sources as [$model, $paymentModel, $paymentIdsColumn, $type, $idOffset]) {
            $query = $model::with(['creator', 'items.tagihanOb.suratJalan.supirKaryawan'])
                ->whereBetween('tanggal_pranota', [$startDate->toDateString(), $endDate->toDateString()]);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_pranota', 'like', "%{$search}%")
                        ->orWhereHas('items.tagihanOb', function ($itemQuery) use ($search) {
                            $itemQuery->where('nomor_kontainer', 'like', "%{$search}%")
                                ->orWhere('nomor_surat_jalan', 'like', "%{$search}%")
                                ->orWhere('nama_supir', 'like', "%{$search}%");
                        });
                });
            }

            $pranotas = $query->get();
            if ($pranotas->isEmpty()) {
                continue;
            }

            $pranotaIds = $pranotas->pluck('id')->mapWithKeys(fn ($id) => [(int) $id => true]);
            $buktiByPranotaId = [];
            $payments = $paymentModel::query()
                ->select($paymentIdsColumn, 'nomor_accurate')
                ->whereNotNull($paymentIdsColumn)
                ->orderByDesc('tanggal_kas')
                ->orderByDesc('id')
                ->cursor();
            foreach ($payments as $payment) {
                foreach ($payment->$paymentIdsColumn ?? [] as $pranotaId) {
                    $pranotaId = (int) $pranotaId;
                    if (isset($pranotaIds[$pranotaId]) && ! array_key_exists($pranotaId, $buktiByPranotaId)) {
                        $buktiByPranotaId[$pranotaId] = $payment->nomor_accurate ?: '-';
                    }
                }
            }

            foreach ($pranotas as $pranota) {
                $isMuatTemas = $pranota instanceof PranotaObMuatTemas;
                $items = $pranota->items->filter(fn ($item) => ($isMuatTemas && $item->snapshot) || $item->tagihanOb)->values();
                if ($items->isEmpty()) {
                    continue;
                }

                $remaining = round((float) $pranota->grand_total, 2);
                $nominal = $items->sum(fn ($item) => (float) (($isMuatTemas ? $item->snapshot?->biaya : null) ?? $item->tagihanOb?->biaya ?? 0));

                foreach ($items as $index => $item) {
                    $detail = ($isMuatTemas ? $item->snapshot : null) ?? $item->tagihanOb;
                    $baseAmount = (float) ($detail->biaya ?? 0);
                    $amount = $index === $items->count() - 1
                        ? $remaining
                        : round($baseAmount + ($nominal > 0
                            ? (float) $pranota->adjustment * $baseAmount / $nominal
                            : (float) $pranota->adjustment / $items->count()), 2);
                    $remaining = round($remaining - $amount, 2);

                    $suratJalan = $item->tagihanOb?->suratJalan;
                    $fakeSj = new \App\Models\SuratJalan;
                    $fakeSj->no_surat_jalan = $detail->nomor_surat_jalan ?? $suratJalan?->no_surat_jalan ?? '-';
                    $fakeSj->jenis_barang = $detail->barang ?? $item->tagihanOb?->barang ?? '-';
                    $fakeSj->tujuan_pengambilan = $detail->tujuan_gudang ?? $item->tagihanOb?->keterangan ?? '-';
                    $fakeSj->supir = $detail->nama_supir ?? '-';
                    $fakeSj->no_plat = $suratJalan?->no_plat ?? '-';
                    $fakeSj->setRelation('supirKaryawan', $suratJalan?->supirKaryawan);

                    $row = new UangJalan;
                    $row->id = -($idOffset + (int) $item->id);
                    $row->tanggal_uang_jalan = $pranota->tanggal_pranota;
                    $row->nomor_uang_jalan = $pranota->nomor_pranota;
                    $row->jumlah_uang_jalan = $amount;
                    $row->jumlah_total = $amount;
                    $row->jumlah_mel = 0;
                    $row->jumlah_pelancar = 0;
                    $row->jumlah_kawalan = 0;
                    $row->jumlah_parkir = 0;
                    $row->_report_type = $type;
                    $row->_report_nomor_bukti = $buktiByPranotaId[$pranota->id] ?? '-';
                    $row->_report_kontainer = $detail->nomor_kontainer ?? '-';
                    $row->setRelation('suratJalan', $fakeSj);
                    $row->setRelation('pranotaUangJalan', collect());
                    $row->setRelation('createdBy', $pranota->creator);

                    $uangJalans->push($row);
                }
            }
        }

        $uangJalans = $uangJalans->sortByDesc('tanggal_uang_jalan')->values();
    }

    public function view(Request $request)
    {
        if (! $request->has('start_date') || ! $request->has('end_date')) {
            return redirect()->route('report.uang-jalan.index')
                ->with('error', 'Tanggal mulai dan tanggal akhir harus diisi');
        }

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $search = $request->input('search');

        $query = UangJalan::query()
            ->with(['suratJalan.supirKaryawan', 'suratJalanBongkaran.supirKaryawan', 'createdBy', 'pranotaUangJalan.pembayaranPranotaUangJalans'])
            ->whereBetween('tanggal_uang_jalan', [$startDate, $endDate]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_uang_jalan', 'like', "%{$search}%")
                    ->orWhereHas('suratJalan', function ($sq) use ($search) {
                        $sq->where('no_surat_jalan', 'like', "%{$search}%")
                            ->orWhere('supir', 'like', "%{$search}%")
                            ->orWhere('no_plat', 'like', "%{$search}%")
                            ->orWhere('jenis_barang', 'like', "%{$search}%");
                    })
                    ->orWhereHas('suratJalanBongkaran', function ($sq) use ($search) {
                        $sq->where('nomor_surat_jalan', 'like', "%{$search}%")
                            ->orWhere('supir', 'like', "%{$search}%")
                            ->orWhere('no_plat', 'like', "%{$search}%")
                            ->orWhere('jenis_barang', 'like', "%{$search}%");
                    });
            });
        }

        $uangJalans = $query->orderBy('tanggal_uang_jalan', 'desc')->get();

        // Fetch adjustments
        $adjustmentsByUjId = $this->fetchAdjustments($uangJalans);

        // Append standalone Pembatalan records that occurred in this period
        $this->appendStandalonePembatalans($uangJalans, $adjustmentsByUjId, $startDate, $endDate, $search);
        $this->appendStandalonePembayaranAktivitasLain($uangJalans, $adjustmentsByUjId, $startDate, $endDate, $search);
        $this->appendPranotaObRows($uangJalans, $startDate, $endDate, $search);

        return view('report-uang-jalan.view', [
            'uangJalans' => $uangJalans,
            'adjustmentsByUjId' => $adjustmentsByUjId,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'search' => $search,
        ]);
    }

    public function export(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300); // 5 minutes

        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date', now()->endOfMonth()))->endOfDay();
        $search = $request->input('search');

        $query = UangJalan::query()
            ->with(['suratJalan.supirKaryawan', 'suratJalanBongkaran.supirKaryawan', 'createdBy', 'pranotaUangJalan.pembayaranPranotaUangJalans'])
            ->whereBetween('tanggal_uang_jalan', [$startDate, $endDate]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_uang_jalan', 'like', "%{$search}%")
                    ->orWhereHas('suratJalan', function ($sq) use ($search) {
                        $sq->where('no_surat_jalan', 'like', "%{$search}%")
                            ->orWhere('supir', 'like', "%{$search}%")
                            ->orWhere('no_plat', 'like', "%{$search}%")
                            ->orWhere('jenis_barang', 'like', "%{$search}%");
                    })
                    ->orWhereHas('suratJalanBongkaran', function ($sq) use ($search) {
                        $sq->where('nomor_surat_jalan', 'like', "%{$search}%")
                            ->orWhere('supir', 'like', "%{$search}%")
                            ->orWhere('no_plat', 'like', "%{$search}%")
                            ->orWhere('jenis_barang', 'like', "%{$search}%");
                    });
            });
        }

        $uangJalans = $query->orderBy('tanggal_uang_jalan', 'desc')->get();

        // Fetch adjustments
        $adjustmentsByUjId = $this->fetchAdjustments($uangJalans);

        // Append standalone Pembatalan records that occurred in this period
        $this->appendStandalonePembatalans($uangJalans, $adjustmentsByUjId, $startDate, $endDate, $search);
        $this->appendStandalonePembayaranAktivitasLain($uangJalans, $adjustmentsByUjId, $startDate, $endDate, $search);
        $this->appendPranotaObRows($uangJalans, $startDate, $endDate, $search);

        // In export, order needs to be ascending as before
        $uangJalans = $uangJalans->sortBy('tanggal_uang_jalan')->values();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ReportUangJalanExport($uangJalans, $startDate, $endDate, $adjustmentsByUjId),
            'Report_Uang_Jalan_'.$startDate->format('Y-m-d').'_to_'.$endDate->format('Y-m-d').'.xlsx'
        );
    }
}
