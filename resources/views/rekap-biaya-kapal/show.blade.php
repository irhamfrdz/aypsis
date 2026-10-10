@extends('layouts.app')

@section('title', 'Rekap Biaya Kapal: ' . $kapal . ' (Voy. ' . $voyage . ')')

@section('content')
<div class="container mx-auto px-4 py-8 printable-area">
    <!-- Action Header (Hidden on Print) -->
    <div class="flex items-center justify-between mb-8 no-print">
        <a href="{{ route('rekap-biaya-kapal.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl border border-gray-200 shadow-sm transition duration-200 text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Pemilihan
        </a>
        <div class="flex gap-2">
            <button onclick="window.print()" 
                    class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition duration-200 text-sm">
                <i class="fas fa-print mr-2"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Main Header -->
    <div class="bg-gradient-to-r from-slate-800 to-indigo-900 rounded-2xl shadow-xl border-none p-8 mb-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-pattern opacity-10 pointer-events-none"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <span class="bg-blue-500/30 text-blue-200 text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider">Rekapitulasi Biaya</span>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight">Kapal: {{ $kapal }}</h1>
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 mt-4 text-slate-200 text-sm">
                <span class="flex items-center"><i class="fas fa-route mr-2 text-blue-400"></i> Voyage: <strong>{{ $voyage }}</strong></span>
                @if(!empty($bl))
                    <span class="flex items-center"><i class="fas fa-file-alt mr-2 text-blue-400"></i> BL: <strong>{{ $bl }}</strong></span>
                @endif
                @if(!empty($lokasi))
                    <span class="flex items-center"><i class="fas fa-map-marker-alt mr-2 text-blue-400"></i> Lokasi: <strong>{{ ucfirst($lokasi) }}</strong></span>
                @endif
                <span class="flex items-center"><i class="fas fa-calendar-alt mr-2 text-blue-400"></i> Tanggal Cetak: <strong>{{ \Carbon\Carbon::now()->format('d F Y H:i') }}</strong></span>
                <span class="flex items-center"><i class="fas fa-file-invoice mr-2 text-blue-400"></i> Total Records: <strong>{{ $biayaKapals->count() }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Summary Metrics -->
    @if($biayaKapals->contains(fn ($item) => isset($item->is_pranota_uang_rit)))
        <p class="mb-4 text-sm text-gray-600">Pranota uang rit gabungan dialokasikan rata per surat jalan sesuai rincian pranota. Biaya mencakup uang rit dan adjustment sebelum potongan hutang, tabungan, dan BPJS.</p>
    @endif
    @if(!empty($bl))
        <p class="mb-4 text-sm text-gray-600">Seluruh sumber biaya disaring untuk BL {{ $bl }}. Biaya tanpa hubungan BL ditampilkan sebagai biaya umum kapal dan tidak masuk total BL. Biaya gabungan beberapa BL dihitung proporsional berdasarkan nominal per kontainer bila tersedia, atau jumlah referensi kontainer / BL.</p>
    @endif
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Nominal -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total Nominal</span>
                <span class="text-2xl font-extrabold text-gray-800 block mt-1">Rp {{ number_format($summary['total_nominal'], 0, ',', '.') }}</span>
            </div>
            <div class="bg-blue-50 p-4 rounded-xl text-blue-600">
                <i class="fas fa-money-bill-wave text-xl"></i>
            </div>
        </div>

        <!-- Total PPN -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total PPN</span>
                <span class="text-2xl font-extrabold text-emerald-600 block mt-1">Rp {{ number_format($summary['total_ppn'], 0, ',', '.') }}</span>
            </div>
            <div class="bg-emerald-50 p-4 rounded-xl text-emerald-600">
                <i class="fas fa-calculator text-xl"></i>
            </div>
        </div>

        <!-- Total PPH -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total PPh</span>
                <span class="text-2xl font-extrabold text-rose-600 block mt-1">Rp {{ number_format($summary['total_pph'], 0, ',', '.') }}</span>
            </div>
            <div class="bg-rose-50 p-4 rounded-xl text-rose-600">
                <i class="fas fa-percentage text-xl"></i>
            </div>
        </div>

        <!-- Grand Total -->
        <div class="bg-indigo-600 rounded-2xl shadow-md p-6 flex items-center justify-between hover:shadow-lg transition-all text-white">
            <div>
                <span class="text-xs font-bold text-indigo-200 uppercase tracking-wider block">Grand Total Biaya</span>
                <span class="text-2xl font-black block mt-1">Rp {{ number_format($summary['grand_total'], 0, ',', '.') }}</span>
            </div>
            <div class="bg-white/10 p-4 rounded-xl text-white">
                <i class="fas fa-coins text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Proporsi & Rincian Biaya (Combined Accordion) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
        <h2 class="text-xl font-extrabold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-chart-pie text-indigo-600 mr-2"></i> Proporsi & Rincian Biaya Berdasarkan Klasifikasi
        </h2>
        <div class="space-y-4">
            @php
                $maxGroupTotal = $grouped->map(fn($items) => $items->sum(fn($i) => $i->apportioned['total_biaya']))->max() ?: 1;
            @endphp
            @foreach($grouped as $category => $items)
                @php
                    $groupTotal = $items->sum(fn($i) => $i->apportioned['total_biaya']);
                    $percentageOfMax = ($groupTotal / $maxGroupTotal) * 100;
                    $percentageOfGrand = $summary['grand_total'] > 0 ? ($groupTotal / $summary['grand_total']) * 100 : 0;
                    $accordionId = Str::slug($category) . '-' . $loop->index;
                @endphp
                <div class="border border-gray-100 rounded-xl overflow-hidden group bg-white shadow-sm hover:shadow-md transition-shadow page-break-inside-avoid">
                    <button type="button" class="w-full text-left p-5 hover:bg-gray-50 transition-colors focus:outline-none toggle-accordion" data-target="#accordion-{{ $accordionId }}">
                        <div class="flex justify-between text-sm font-bold text-gray-800 mb-2 items-center">
                            <span class="flex items-center uppercase tracking-wide">
                                <i class="fas fa-chevron-right text-xs text-blue-500 mr-3 transition-transform duration-300 icon-chevron"></i>
                                {{ $category }}
                                <span class="ml-3 px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs font-medium">{{ $items->count() }} Trx</span>
                            </span>
                            <span class="text-base text-gray-900 tracking-tight">Rp {{ number_format($groupTotal, 0, ',', '.') }} <span class="text-xs font-medium text-gray-400 ml-1">({{ number_format($percentageOfGrand, 1) }}%)</span></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 ml-6" style="width: calc(100% - 1.5rem)">
                            <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentageOfMax }}%"></div>
                        </div>
                    </button>
                    
                    <div id="accordion-{{ $accordionId }}" class="accordion-body bg-gray-50/50 border-t border-gray-100" style="display: none;">
                        <div class="p-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm bg-white rounded-lg shadow-sm border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">Tanggal</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-48">No. Bukti / Invoice</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                                        @if(strtoupper($category) === 'BIAYA DOKUMEN')
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vendor</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Nominal</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">PPH</th>
                                        @endif
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-36">@if(strtoupper($category) === 'BIAYA DOKUMEN') Total Biaya @else Total @endif</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-16 no-print">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @php $no = 1; @endphp
                                    @foreach($items as $item)
                                        <tr class="hover:bg-blue-50/30 transition-colors">
                                            <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500 font-medium">
                                                {{ $no++ }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">
                                                {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/M/Y') : '-' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                @if(isset($item->is_uang_jalan) || isset($item->is_tagihan_vendor) || isset($item->is_rit_detail))
                                                    @php
                                                        $sj = $item->rekapSuratJalan ?? $item->suratJalan ?? $item->suratJalanBongkaran ?? $item->suratJalanBongkaranBatam ?? null;
                                                        $noSuratJalan = $sj->no_surat_jalan ?? $sj->nomor_surat_jalan ?? '-';
                                                    @endphp
                                                    <div class="text-xs font-bold text-indigo-600">{{ $noSuratJalan }}</div>
                                                    <div class="text-[10px] text-gray-400">Surat Jalan</div>
                                                    @if(isset($item->is_rit_detail))
                                                        <div class="text-[10px] text-gray-400">Pranota: {{ $item->nomor_invoice }}</div>
                                                    @endif
                                                @elseif(isset($item->is_pranota_uang_rit))
                                                    <div class="text-xs font-bold text-indigo-600">{{ $item->nomor_invoice }}</div>
                                                    <div class="text-[10px] text-gray-400">Pranota Uang Rit</div>
                                                @elseif(isset($item->is_amprahan) && $item->is_amprahan)
                                                    <div class="text-xs font-bold text-amber-600">{{ $item->stockAmprahan->nomor_bukti ?? '-' }}</div>
                                                    <div class="text-[10px] text-gray-400">Bukti Amprahan</div>
                                                @else
                                                    <div class="text-xs font-bold text-gray-800">{{ $item->nomor_invoice ?? '-' }}</div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray-700">
                                                @if(isset($item->is_uang_jalan) || isset($item->is_tagihan_vendor) || isset($item->is_rit_detail))
                                                    @php
                                                        $sj = $item->rekapSuratJalan ?? $item->suratJalan ?? $item->suratJalanBongkaran ?? $item->suratJalanBongkaranBatam ?? null;
                                                        $pengirim = '-';
                                                        if ($sj) {
                                                            if (method_exists($sj, 'pengirimRelation') && $sj->pengirimRelation) {
                                                                $pengirim = $sj->pengirimRelation->nama_pengirim;
                                                            } elseif (isset($sj->pengirim)) {
                                                                $pengirim = is_object($sj->pengirim) ? ($sj->pengirim->nama_pengirim ?? (string)$sj->pengirim) : $sj->pengirim;
                                                            }
                                                        }
                                                        $noKontainer = $sj->no_kontainer ?? '-';
                                                    @endphp
                                                    <span class="block"><strong>Pengirim:</strong> {{ $pengirim }}</span>
                                                    <span class="block text-gray-500"><strong>Kontainer:</strong> {{ $noKontainer }}</span>
                                                    @if(isset($item->is_rit_detail))
                                                        <span class="block text-gray-500"><strong>{{ $item->is_pranota_uang_rit_kenek ? 'Kenek' : 'Supir' }}:</strong> {{ ($item->is_pranota_uang_rit_kenek ? $item->kenek_nama : $item->supir_nama) ?: '-' }}</span>
                                                    @endif
                                                @elseif(isset($item->is_amprahan) && $item->is_amprahan)
                                                    <strong>Barang:</strong> {{ $item->nama_barang_amprahan ?? '-' }}
                                                @elseif(isset($item->is_pranota_uang_rit))
                                                    <span class="block"><strong>{{ $item->is_pranota_uang_rit_kenek ? 'Kenek' : 'Supir' }}:</strong> {{ $item->is_pranota_uang_rit_kenek ? $item->kenek_nama : $item->supir_nama }}</span>
                                                    <span class="block text-gray-500"><strong>Surat Jalan:</strong> {{ collect($item->rekap_rit_items)->pluck('nomor')->implode(', ') }}</span>
                                                @else
                                                    {{ isset($item->buruhBatamDetails) && $item->buruhBatamDetails->isNotEmpty() ? 'BURUH BONGKAR BATAM' : ($item->klasifikasiBiaya->nama ?? $item->jenis_biaya ?? '-') }}
                                                @endif
                                            </td>
                                            @if(strtoupper($category) === 'BIAYA DOKUMEN')
                                                @php
                                                    $kapalLower = strtolower(trim($kapal));
                                                    $voyageLower = strtolower(trim($voyage));
                                                    $filteredDokumens = isset($item->dokumens) ? $item->dokumens->filter(function($d) use ($kapalLower, $voyageLower) {
                                                        return isset($d->kapal) && strtolower(trim($d->kapal)) === $kapalLower && isset($d->voyage) && strtolower(trim($d->voyage)) === $voyageLower;
                                                    }) : collect();
                                                @endphp
                                                <td class="px-4 py-3 text-xs text-gray-700">
                                                    @if($filteredDokumens->count() > 0)
                                                        @foreach($filteredDokumens as $dok)
                                                            @php
                                                                $vendorName = '-';
                                                                if ($dok->vendor_id) {
                                                                    $vendor = \App\Models\PricelistBiayaDokumen::find($dok->vendor_id);
                                                                    if ($vendor) $vendorName = $vendor->nama_vendor;
                                                                }
                                                            @endphp
                                                            <div class="mb-1 pb-1 border-b border-gray-100 last:border-0 last:mb-0 last:pb-0">{{ $vendorName }}</div>
                                                        @endforeach
                                                    @else
                                                        {{ $item->vendor->nama_vendor ?? '-' }}
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-right text-xs text-gray-900 whitespace-nowrap">
                                                    @if($filteredDokumens->count() > 0)
                                                        @foreach($filteredDokumens as $dok)
                                                            <div class="mb-1 pb-1 border-b border-gray-100 last:border-0 last:mb-0 last:pb-0">Rp {{ number_format($dok->nominal, 0, ',', '.') }}</div>
                                                        @endforeach
                                                    @else
                                                        Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-right text-xs text-gray-900 whitespace-nowrap">
                                                    @if($filteredDokumens->count() > 0)
                                                        @foreach($filteredDokumens as $dok)
                                                            <div class="mb-1 pb-1 border-b border-gray-100 last:border-0 last:mb-0 last:pb-0">Rp {{ number_format($dok->pph, 0, ',', '.') }}</div>
                                                        @endforeach
                                                    @else
                                                        Rp {{ number_format($item->pph, 0, ',', '.') }}
                                                    @endif
                                                </td>
                                            @endif
                                            <td class="px-4 py-3 text-right text-xs text-gray-900 font-bold whitespace-nowrap">
                                                Rp {{ number_format($item->apportioned['total_biaya'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-center whitespace-nowrap no-print">
                                                @if(isset($item->is_pranota_uang_rit))
                                                    <a href="{{ route($item->is_pranota_uang_rit_kenek ? 'pranota-uang-rit-kenek.show' : 'pranota-uang-rit.show', $item->id) }}" target="_blank" class="inline-flex items-center justify-center w-7 h-7 rounded bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors tooltip" title="Lihat Detail Pranota Uang Rit">
                                                        <i class="fas fa-eye text-xs"></i>
                                                    </a>
                                                @elseif(isset($item->is_pranota_ob_muat_temas) && $item->is_pranota_ob_muat_temas)
                                                    <a href="{{ route('pranota-ob.muat-temas.show', $item->id) }}" target="_blank" class="inline-flex items-center justify-center w-7 h-7 rounded bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors tooltip" title="Lihat Detail Pranota OB Muat Temas">
                                                        <i class="fas fa-eye text-xs"></i>
                                                    </a>
                                                @elseif(isset($item->is_pranota_ob) && $item->is_pranota_ob)
                                                    <a href="{{ route('pranota-ob.show', $item->id) }}" target="_blank" class="inline-flex items-center justify-center w-7 h-7 rounded bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors tooltip" title="Lihat Detail Pranota OB">
                                                        <i class="fas fa-eye text-xs"></i>
                                                    </a>
                                                @elseif(isset($item->is_amprahan) && $item->is_amprahan)
                                                    <a href="{{ route('stock-amprahan.show', $item->stock_amprahan_id ?? $item->id) }}" target="_blank" class="inline-flex items-center justify-center w-7 h-7 rounded bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors tooltip" title="Lihat Detail Stock Amprahan">
                                                        <i class="fas fa-eye text-xs"></i>
                                                    </a>
                                                @elseif(isset($item->is_ob_muat) || isset($item->is_ob_bongkar))
                                                    <a href="{{ route('ob.index', ['nama_kapal' => $kapal, 'no_voyage' => $voyage, 'kegiatan' => isset($item->is_ob_muat) ? 'muat' : 'bongkar']) }}" target="_blank" class="inline-flex items-center justify-center w-7 h-7 rounded bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors tooltip" title="Lihat Detail di Menu OB">
                                                        <i class="fas fa-external-link-alt text-xs"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('biaya-kapal.show', ['biayaKapal' => $item->id, 'kapal' => $kapal, 'voyage' => $voyage]) }}" target="_blank" class="inline-flex items-center justify-center w-7 h-7 rounded bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors tooltip" title="Lihat Detail Transaksi">
                                                        <i class="fas fa-eye text-xs"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @if(isset($item->is_pranota_ob_muat_temas) && $item->is_pranota_ob_muat_temas && !empty($item->rekap_ob_temas_items))
                                            <tr class="bg-indigo-50/40">
                                                <td colspan="{{ strtoupper($category) === 'BIAYA DOKUMEN' ? 9 : 6 }}" class="px-6 pb-4 pt-2">
                                                    <div class="rounded-lg border border-indigo-100 bg-white overflow-hidden">
                                                        <div class="px-3 py-2 bg-indigo-50 text-[11px] font-semibold uppercase tracking-wide text-indigo-700">
                                                            Rincian biaya per kontainer
                                                        </div>
                                                        <table class="w-full text-xs">
                                                            <thead class="text-[10px] uppercase text-gray-500 bg-gray-50">
                                                                <tr>
                                                                    <th class="px-3 py-2 text-left">No. Kontainer</th>
                                                                    <th class="px-3 py-2 text-left">Nama Barang</th>
                                                                    <th class="px-3 py-2 text-left">Supir</th>
                                                                    <th class="px-3 py-2 text-left">Size</th>
                                                                    <th class="px-3 py-2 text-left">Tujuan</th>
                                                                    <th class="px-3 py-2 text-right">Biaya</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-100">
                                                                @foreach($item->rekap_ob_temas_items as $temaEntry)
                                                                    <tr>
                                                                        <td class="px-3 py-2 font-mono font-semibold text-gray-800">{{ $temaEntry['nomor_kontainer'] ?? '-' }}</td>
                                                                        <td class="px-3 py-2 text-gray-700">{{ $temaEntry['nama_barang'] ?? '-' }}</td>
                                                                        <td class="px-3 py-2 text-gray-700">{{ $temaEntry['supir'] ?? '-' }}</td>
                                                                        <td class="px-3 py-2 text-gray-700">{{ $temaEntry['size'] ?? '-' }}</td>
                                                                        <td class="px-3 py-2 text-gray-700">{{ $temaEntry['tujuan_gudang'] ?? '-' }}</td>
                                                                        <td class="px-3 py-2 text-right font-semibold text-gray-900 whitespace-nowrap">Rp {{ number_format((float) ($temaEntry['biaya'] ?? 0), 0, ',', '.') }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        @php
                                            $temasDetails = $item instanceof \App\Models\BiayaKapal && $item->relationLoaded('temasDetails')
                                                ? $item->temasDetails->filter(fn ($detail) => strtolower(trim($detail->kapal ?? '')) === strtolower(trim($kapal)) && strtolower(trim($detail->voyage ?? '')) === strtolower(trim($voyage)))
                                                : collect();
                                        @endphp
                                        @if($temasDetails->isNotEmpty())
                                            <tr class="bg-blue-50/20">
                                                <td colspan="{{ strtoupper($category) === 'BIAYA DOKUMEN' ? 9 : 6 }}" class="px-4 pb-4 pt-2">
                                                    @include('rekap-biaya-kapal._temas-details', ['temasDetails' => $temasDetails])
                                                </td>
                                            </tr>
                                        @endif
                                        @php
                                            $buruhBatamDetails = isset($item->buruhBatamDetails)
                                                ? $item->buruhBatamDetails->filter(fn ($detail) => strtolower(trim($detail->kapal ?? '')) === strtolower(trim($kapal)) && strtolower(trim($detail->voyage ?? '')) === strtolower(trim($voyage)))
                                                : collect();
                                        @endphp
                                        @if(strtoupper($category) === 'BURUH BONGKAR BATAM' && $buruhBatamDetails->isNotEmpty())
                                            @php
                                                $buruhBatamRows = collect();
                                                foreach ($buruhBatamDetails as $detail) {
                                                    $containers = is_array($detail->kontainer_ids)
                                                        ? $detail->kontainer_ids
                                                        : (json_decode($detail->kontainer_ids ?? '[]', true) ?: []);
                                                    $containers = collect($containers)->values();
                                                    if ($containers->isEmpty()) {
                                                        $containers = collect([['nomor_kontainer' => 'Tanpa rincian kontainer', 'size' => '-', 'nominal' => (float) $detail->nominal]]);
                                                    }

                                                    $weights = $containers->map(fn ($container) => max(0, (float) ($container['nominal'] ?? 0)));
                                                    $weightTotal = $weights->sum();
                                                    $grossTotal = (float) $detail->nominal + (float) ($detail->adjustment ?? 0);
                                                    $allocatedAdjustment = 0;
                                                    $allocatedPph = 0;

                                                    foreach ($containers as $index => $container) {
                                                        $weight = $weightTotal > 0 ? $weights[$index] : 1;
                                                        $denominator = $weightTotal > 0 ? $weightTotal : $containers->count();
                                                        $isLast = $index === $containers->count() - 1;
                                                        $nominal = (float) ($container['nominal'] ?? 0);
                                                        $adjustment = $isLast
                                                            ? (float) ($detail->adjustment ?? 0) - $allocatedAdjustment
                                                            : round((float) ($detail->adjustment ?? 0) * $weight / $denominator, 2);
                                                        $allocatedAdjustment += $adjustment;
                                                        $containerGross = $nominal + $adjustment;
                                                        $pph = $isLast
                                                            ? (float) ($detail->pph_amount ?? 0) - $allocatedPph
                                                            : round((float) ($detail->pph_amount ?? 0) * ($grossTotal != 0 ? $containerGross / $grossTotal : 1 / $containers->count()), 2);
                                                        $allocatedPph += $pph;
                                                        $buruhBatamRows->push([
                                                            'nomor_kontainer' => $container['nomor_kontainer'] ?? 'Unknown',
                                                            'size' => $container['size'] ?? '-',
                                                            'nominal' => $nominal,
                                                            'adjustment' => $adjustment,
                                                            'pph' => $pph,
                                                            'total' => $containerGross - $pph,
                                                        ]);
                                                    }
                                                }
                                            @endphp
                                            <tr class="bg-amber-50/30">
                                                <td colspan="{{ strtoupper($category) === 'BIAYA DOKUMEN' ? 9 : 6 }}" class="px-6 pb-4 pt-2">
                                                    <div class="rounded-lg border border-amber-100 bg-white overflow-hidden">
                                                        <div class="px-3 py-2 bg-amber-50 text-[11px] font-semibold uppercase tracking-wide text-amber-800">Rincian biaya buruh bongkar Batam per kontainer</div>
                                                        <table class="w-full text-xs">
                                                            <thead class="text-[10px] uppercase text-gray-500 bg-gray-50">
                                                                <tr>
                                                                    <th class="px-3 py-2 text-left">No. Kontainer</th>
                                                                    <th class="px-3 py-2 text-left">Size</th>
                                                                    <th class="px-3 py-2 text-right">Nominal</th>
                                                                    <th class="px-3 py-2 text-right">Adjustment</th>
                                                                    <th class="px-3 py-2 text-right">PPh</th>
                                                                    <th class="px-3 py-2 text-right">Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-100">
                                                                @foreach($buruhBatamRows as $containerRow)
                                                                    <tr>
                                                                        <td class="px-3 py-2 font-mono font-semibold text-gray-800">{{ $containerRow['nomor_kontainer'] }}</td>
                                                                        <td class="px-3 py-2 text-gray-700">{{ $containerRow['size'] }}</td>
                                                                        <td class="px-3 py-2 text-right text-gray-700 whitespace-nowrap">Rp {{ number_format($containerRow['nominal'], 0, ',', '.') }}</td>
                                                                        <td class="px-3 py-2 text-right text-gray-700 whitespace-nowrap">Rp {{ number_format($containerRow['adjustment'], 0, ',', '.') }}</td>
                                                                        <td class="px-3 py-2 text-right text-gray-700 whitespace-nowrap">Rp {{ number_format($containerRow['pph'], 0, ',', '.') }}</td>
                                                                        <td class="px-3 py-2 text-right font-semibold text-gray-900 whitespace-nowrap">Rp {{ number_format($containerRow['total'], 0, ',', '.') }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-100/50 font-bold border-t border-gray-200">
                                    <tr>
                                        <td colspan="{{ strtoupper($category) === 'BIAYA DOKUMEN' ? 7 : 4 }}" class="px-4 py-3 text-right text-xs text-gray-500 uppercase tracking-wider">Subtotal {{ $category }}</td>
                                        <td class="px-4 py-3 text-right text-sm text-gray-900">
                                            Rp {{ number_format($groupTotal, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 no-print"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if(!empty($bl) && $biayaUmum->isNotEmpty())
            <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 page-break-inside-avoid">
                <h3 class="font-bold text-gray-800">Biaya Umum Kapal</h3>
                <p class="mt-1 text-xs text-gray-500">Tidak memiliki hubungan BL dan tidak termasuk grand total BL {{ $bl }}.</p>
                <div class="mt-3 overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 text-gray-500"><tr>
                            <th class="p-3 text-left">No. Bukti / Invoice</th>
                            <th class="p-3 text-left">Jenis Biaya</th>
                            <th class="p-3 text-right">Total</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($biayaUmum as $item)
                                <tr>
                                    <td class="p-3 font-semibold">{{ $item->nomor_invoice ?? '-' }}</td>
                                    <td class="p-3">{{ $item->klasifikasiBiaya->nama ?? $item->jenis_biaya ?? '-' }}</td>
                                    <td class="p-3 text-right whitespace-nowrap">Rp {{ number_format($item->apportioned['total_biaya'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 font-bold"><tr>
                            <td colspan="2" class="p-3 text-right">Total Biaya Umum Kapal</td>
                            <td class="p-3 text-right whitespace-nowrap">Rp {{ number_format($biayaUmum->sum(fn ($item) => $item->apportioned['total_biaya']), 0, ',', '.') }}</td>
                        </tr></tfoot>
                    </table>
                </div>
            </div>
        @endif

        @if($biayaKapals->count() === 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden page-break-inside-avoid p-12 text-center text-gray-500 mt-4">
                <i class="fas fa-ship text-gray-300 text-5xl mb-4 block"></i>
                <p class="font-semibold text-lg">Tidak ada data biaya yang ditemukan</p>
                <p class="text-gray-400 text-sm mt-1">Silakan periksa kembali kapal dan nomor voyage yang Anda pilih.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        let temasContainerListsForPrint = [];
        window.addEventListener('beforeprint', function() {
            temasContainerListsForPrint = [...document.querySelectorAll('.temas-container-list')].map(element => ({element, open: element.open}));
            temasContainerListsForPrint.forEach(({element}) => element.open = true);
        });
        window.addEventListener('afterprint', function() {
            temasContainerListsForPrint.forEach(({element, open}) => element.open = open);
        });
        $('.toggle-accordion').on('click', function() {
            const target = $(this).data('target');
            const $body = $(target);
            const $icon = $(this).find('.icon-chevron');
            
            // Toggle slide
            $body.slideToggle(300);
            
            // Toggle styles for opened state
            $icon.toggleClass('rotate-90');
            $(this).toggleClass('bg-blue-50/30');
        });
        
        // Open the first one by default
        setTimeout(() => {
            $('.toggle-accordion').first().trigger('click');
        }, 100);
    });
</script>
@endpush

@push('styles')
<style>
    /* Styling khusus cetak (print) agar output PDF/Print rapi dan indah */
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            background: #fff !important;
            color: #000 !important;
            font-size: 12px !important;
        }
        .container {
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        /* Keep background colors and colors during printing */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .page-break-inside-avoid {
            page-break-inside: avoid !important;
        }
        header, #sidebar, #mobile-menu-button {
            display: none !important;
        }
        .lg\:ml-64 {
            margin-left: 0 !important;
        }
        .p-6 {
            padding: 0 !important;
        }
    }
</style>
@endpush
@endsection
