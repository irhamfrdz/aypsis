<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReactSewaKontainerSyncController extends Controller
{
    /**
     * Get the full state of the React application from the database.
     */
    public function getState()
    {
        $state = [
            'customers' => DB::table('sk_customers')->get(),
            'tipes' => DB::table('sk_tipes')->get(),
            'ukurans' => DB::table('sk_ukurans')->get(),
            'kontainers' => DB::table('sk_kontainers')->get(),
            'tarifs' => DB::table('sk_tarifs')->get(),
            'sewas' => DB::table('sk_sewas')->get(),
            'invoices' => DB::table('sk_invoice_grups')->get()->map(function($invoice) {
                $invoice->list_id_tagihan = json_decode($invoice->list_id_tagihan, true) ?? [];
                return $invoice;
            })->toArray(),
        ];

        // Format tagihan_bulans into paymentOverrides and manualTagihans
        $tagihans = DB::table('sk_tagihan_bulans')->get();
        $overrides = [];
        $manuals = [];

        foreach ($tagihans as $tagihan) {
            if ($tagihan->id_sewa === null && $tagihan->bulan_ke === null) {
                // This is an override
                $overrides[$tagihan->id_tagihan] = [
                    'status_bayar' => $tagihan->status_bayar,
                    'tanggal_bayar' => $tagihan->tanggal_bayar,
                    'tanggal_tagihan' => $tagihan->tanggal_tagihan,
                    'nomor_invoice_grup' => $tagihan->nomor_invoice_grup,
                    'nomor_pranota' => $tagihan->nomor_pranota,
                    'tanggal_pranota' => $tagihan->tanggal_pranota,
                    'jumlah_tagihan_override' => $tagihan->jumlah_tagihan_override,
                    'jumlah_bayar' => $tagihan->jumlah_bayar,
                    'selisih_pembayaran' => $tagihan->selisih_pembayaran,
                    'keterangan_selisih' => $tagihan->keterangan_selisih,
                    'ppn' => $tagihan->ppn,
                    'pph' => $tagihan->pph,
                    'nomor_bayar' => $tagihan->nomor_bayar
                ];
            } else {
                // This is a manual tagihan
                $manuals[] = [
                    'id_tagihan' => $tagihan->id_tagihan,
                    'id_sewa' => $tagihan->id_sewa,
                    'bulan_ke' => $tagihan->bulan_ke,
                    'tanggal_awal' => $tagihan->tanggal_awal,
                    'tanggal_akhir' => $tagihan->tanggal_akhir,
                    'jumlah_hari' => $tagihan->jumlah_hari,
                    'tipe_tarif' => $tagihan->tipe_tarif,
                    'jumlah_tagihan' => $tagihan->jumlah_tagihan,
                    'status_bayar' => $tagihan->status_bayar,
                    'tanggal_tagihan' => $tagihan->tanggal_tagihan,
                    'tanggal_bayar' => $tagihan->tanggal_bayar,
                    'nomor_invoice_grup' => $tagihan->nomor_invoice_grup,
                    'nomor_pranota' => $tagihan->nomor_pranota,
                    'tanggal_pranota' => $tagihan->tanggal_pranota,
                    'jumlah_tagihan_override' => $tagihan->jumlah_tagihan_override,
                    'jumlah_bayar' => $tagihan->jumlah_bayar,
                    'selisih_pembayaran' => $tagihan->selisih_pembayaran,
                    'keterangan_selisih' => $tagihan->keterangan_selisih,
                    'ppn' => $tagihan->ppn,
                    'pph' => $tagihan->pph,
                    'nomor_bayar' => $tagihan->nomor_bayar
                ];
            }
        }

        $state['paymentOverrides'] = (object) $overrides;
        $state['manualTagihans'] = $manuals;

        return response()->json($state);
    }

    /**
     * Save the full state of the React application to the database.
     */
    public function saveState(Request $request)
    {
        $state = $request->json()->all();

        DB::transaction(function () use ($state) {
            DB::table('sk_invoice_grups')->delete();
            DB::table('sk_tagihan_bulans')->delete();
            DB::table('sk_sewas')->delete();
            DB::table('sk_tarifs')->delete();
            DB::table('sk_kontainers')->delete();
            DB::table('sk_ukurans')->delete();
            DB::table('sk_tipes')->delete();
            DB::table('sk_customers')->delete();

            if (!empty($state['customers'])) {
                $customers = array_map(function($c) { return array_intersect_key($c, array_flip(['id_customer', 'nama_customer', 'status_aktif'])); }, $state['customers']);
                DB::table('sk_customers')->insert($customers);
            }
            if (!empty($state['tipes'])) {
                $tipes = array_map(function($t) { return array_intersect_key($t, array_flip(['id_tipe', 'nama_tipe', 'status_aktif'])); }, $state['tipes']);
                DB::table('sk_tipes')->insert($tipes);
            }
            if (!empty($state['ukurans'])) {
                $ukurans = array_map(function($u) { return array_intersect_key($u, array_flip(['id_ukuran', 'deskripsi_ukuran', 'status_aktif'])); }, $state['ukurans']);
                DB::table('sk_ukurans')->insert($ukurans);
            }
            if (!empty($state['kontainers'])) {
                $kontainers = array_map(function($k) { return array_intersect_key($k, array_flip(['no_kontainer', 'id_customer', 'id_tipe', 'id_ukuran', 'status_aktif'])); }, $state['kontainers']);
                DB::table('sk_kontainers')->insert($kontainers);
            }
            if (!empty($state['tarifs'])) {
                $tarifs = array_map(function($t) {
                    return [
                        'id_tarif' => $t['id_tarif'],
                        'id_customer' => $t['id_customer'],
                        'id_tipe' => $t['id_tipe'],
                        'id_ukuran' => $t['id_ukuran'],
                        'tarif_bulanan' => $t['tarif_bulanan'] ?? 0,
                        'tarif_harian' => $t['tarif_harian'] ?? 0,
                        'tanggal_mulai_berlaku' => $t['tanggal_mulai_berlaku'],
                        'tanggal_akhir_berlaku' => $t['tanggal_akhir_berlaku'] ?? null,
                        'status_aktif' => $t['status_aktif'] ?? true,
                    ];
                }, $state['tarifs']);
                DB::table('sk_tarifs')->insert($tarifs);
            }
            if (!empty($state['sewas'])) {
                $sewas = array_map(function($s) {
                    return [
                        'id_sewa' => $s['id_sewa'],
                        'no_kontainer' => $s['no_kontainer'],
                        'id_customer' => $s['id_customer'],
                        'tanggal_sewa' => $s['tanggal_sewa'],
                        'tanggal_kembali' => $s['tanggal_kembali'] ?? null,
                        'tarif_bulanan' => $s['tarif_bulanan'] ?? 0,
                        'tarif_harian' => $s['tarif_harian'] ?? 0,
                        'jenis_tarif' => $s['jenis_tarif'],
                        'status_sewa' => $s['status_sewa'],
                        'catatan' => $s['catatan'] ?? null,
                    ];
                }, $state['sewas']);
                DB::table('sk_sewas')->insert($sewas);
            }
            if (!empty($state['invoices'])) {
                $invoices = array_map(function($i) {
                    return [
                        'nomor_invoice' => $i['nomor_invoice'],
                        'id_customer' => $i['id_customer'],
                        'tanggal_invoice' => $i['tanggal_invoice'],
                        'status_pembayaran' => $i['status_pembayaran'],
                        'deskripsi' => $i['deskripsi'] ?? '',
                        'list_id_tagihan' => json_encode($i['list_id_tagihan'] ?? []),
                        'adjustment_biaya' => $i['adjustment_biaya'] ?? null,
                        'adjustment_keterangan' => $i['adjustment_keterangan'] ?? null,
                    ];
                }, $state['invoices']);
                DB::table('sk_invoice_grups')->insert($invoices);
            }

            $tagihanInserts = [];
            
            if (!empty($state['paymentOverrides'])) {
                foreach ($state['paymentOverrides'] as $id => $override) {
                    $tagihanInserts[] = array_merge(['id_tagihan' => $id], $override);
                }
            }

            if (!empty($state['manualTagihans'])) {
                foreach ($state['manualTagihans'] as $manual) {
                    $tagihanInserts[] = $manual;
                }
            }

            if (!empty($tagihanInserts)) {
                $normalized = [];
                $keys = ['id_tagihan','id_sewa','bulan_ke','tanggal_awal','tanggal_akhir','jumlah_hari','tipe_tarif','jumlah_tagihan','status_bayar','tanggal_tagihan','tanggal_bayar','nomor_invoice_grup','nomor_pranota','tanggal_pranota','jumlah_tagihan_override','jumlah_bayar','selisih_pembayaran','keterangan_selisih','ppn','pph','nomor_bayar'];
                foreach ($tagihanInserts as $tag) {
                    $row = [];
                    foreach ($keys as $k) {
                        $row[$k] = $tag[$k] ?? null;
                    }
                    $normalized[] = $row;
                }
                
                foreach (array_chunk($normalized, 500) as $chunk) {
                    DB::table('sk_tagihan_bulans')->insert($chunk);
                }
            }
        });

        return response()->json(['status' => 'success']);
    }
}
