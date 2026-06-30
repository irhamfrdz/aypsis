@extends('layouts.app')

@section('title', 'Detail Pranota Ongkos Truk')
@section('page_title', 'Detail Pranota ' . $pranota->no_pranota)

@section('content')
<div class="p-6">
    <div class="max-w-5xl mx-auto">
        <!-- Action Buttons -->
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('pranota-ongkos-truk.index') }}" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors font-bold text-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
            </a>
            <div class="flex gap-3">
                <a href="{{ route('pranota-ongkos-truk.edit', $pranota->id) }}" class="px-5 py-2.5 bg-amber-500 text-white rounded-xl hover:bg-amber-600 transition-all font-bold text-sm flex items-center gap-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('pranota-ongkos-truk.print', $pranota->id) }}" target="_blank" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-bold text-sm flex items-center gap-2">
                    <i class="fas fa-print"></i> Cetak
                </a>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden shadow-blue-500/5">
            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-10 text-white relative">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <span class="bg-white/20 backdrop-blur-md text-white text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full mb-3 inline-block">
                            Pranota Ongkos Truk
                        </span>
                        <h2 class="text-4xl font-black">{{ $pranota->no_pranota }}</h2>
                        <div class="mt-2 flex items-center text-blue-100 font-medium whitespace-nowrap overflow-hidden">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            {{ $pranota->tanggal_pranota->format('l, d F Y') }}
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end">
                        <div class="bg-white/10 p-4 rounded-2xl backdrop-blur-sm border border-white/10">
                            <span class="text-blue-100 text-xs font-bold uppercase tracking-wider block mb-1">Total Dibayarkan</span>
                            <span class="text-3xl font-black">Rp {{ number_format($pranota->total_nominal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <!-- Abstract Decor -->
                <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
                    <i class="fas fa-file-invoice text-9xl transform rotate-12"></i>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                    <div>
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                            <span class="w-8 h-px bg-gray-200 mr-3"></span> Informasi Umum
                        </h4>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-50">
                                <span class="text-sm text-gray-500 font-medium">Dibuat Oleh</span>
                                <span class="text-sm text-gray-900 font-bold">{{ $pranota->creator->username ?? 'System' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-50">
                                <span class="text-sm text-gray-500 font-medium">Status</span>
                                <span class="px-3 py-0.5 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase">{{ $pranota->status }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-500 font-medium">Tanggal Input</span>
                                <span class="text-sm text-gray-900 font-bold">{{ $pranota->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                            <span class="w-8 h-px bg-gray-200 mr-3"></span> Catatan & Adjustment
                        </h4>
                        <div class="space-y-4">
                             <div class="bg-gray-50 rounded-2xl p-4 min-h-[60px] border border-gray-100 italic text-sm text-gray-600">
                                {{ $pranota->keterangan ?: 'Tidak ada keterangan tambahan.' }}
                            </div>
                            @if($pranota->adjustments && is_array($pranota->adjustments))
                                @foreach($pranota->adjustments as $index => $adj)
                                    @if(isset($adj['nominal']) && $adj['nominal'] != 0)
                                    <div class="flex justify-between items-center py-2 px-4 bg-orange-50 border border-orange-100 rounded-xl">
                                        <span class="text-[11px] text-orange-700 font-bold uppercase tracking-wide">Adjustment {{ $loop->iteration }}: {{ $adj['keterangan'] ?? 'Tanpa Keterangan' }}</span>
                                        <span class="text-sm font-black {{ $adj['nominal'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                                            Rp {{ number_format($adj['nominal'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                    @endif
                                @endforeach
                            @elseif($pranota->adjustment != 0)
                            <div class="flex justify-between items-center py-2 px-4 bg-orange-50 border border-orange-100 rounded-xl">
                                <span class="text-xs text-orange-700 font-bold uppercase tracking-wide">Adjustment Nilai</span>
                                <span class="text-sm font-black {{ $pranota->adjustment < 0 ? 'text-red-600' : 'text-green-600' }}">
                                    Rp {{ number_format($pranota->adjustment, 0, ',', '.') }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div>
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center">
                        <span class="w-8 h-px bg-gray-200 mr-3"></span> Rincian Item Surat Jalan
                    </h4>
                    <div class="overflow-hidden border border-gray-100 rounded-3xl">
                        <table class="w-full">
                            <thead class="bg-gray-50/80">
                                <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">
                                    <th class="px-6 py-4 text-left">Tgl SJ</th>
                                    <th class="px-6 py-4 text-left">Tgl TT</th>
                                    <th class="px-6 py-4 text-left">No. Surat Jalan</th>
                                    <th class="px-6 py-4 text-left">No. Plat</th>
                                    <th class="px-6 py-4 text-left">Size</th>
                                    <th class="px-6 py-4 text-left">No. Bukti</th>
                                    <th class="px-6 py-4 text-left">Tipe</th>
                                    <th class="px-6 py-4 text-left">Tujuan</th>
                                    <th class="px-6 py-4 text-right">Ongkos</th>
                                    <th class="px-6 py-4 text-right">UJ</th>
                                    <th class="px-6 py-4 text-right">Nominal (Net)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($pranota->items as $item)
                                    <tr class="hover:bg-gray-50/30 transition-colors">
                                        @php
                                            $noPlatSj = '-';
                                            $tglSj = $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/M/Y') : '-';
                                            $tglTt = '-';
                                            $size = '-';
                                            $ongkosTruk = 0;
                                            $uangJalan = 0;
                                            $noBukti = '-';
                                            $tujuanSj = '-';
                                            
                                            if($item->type === 'SuratJalan' && $item->suratJalan) {
                                                $noPlatSj = $item->suratJalan->no_plat ?? '-';
                                                $size = $item->suratJalan->size ?? '-';
                                                $tglSj = $item->suratJalan->tanggal_surat_jalan ? $item->suratJalan->tanggal_surat_jalan->format('d/M/Y') : $tglSj;
                                                $tujuanSj = $item->suratJalan->tujuanPengambilanRelation->ke ?? $item->suratJalan->tujuan_pengambilan ?? '-';

                                                // Ongkos Truck Logic
                                                if ($item->suratJalan->tujuanPengambilanRelation) {
                                                    $sz = strtolower($item->suratJalan->size ?? '');
                                                    $ongkosTruk = str_contains($sz, '40') 
                                                        ? ($item->suratJalan->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0)
                                                        : ($item->suratJalan->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0);
                                                }
                                                if ($item->suratJalan->tujuan_pengambilan == "PULO GADUNG ( BESI SCRAP )") {
                                                    $ongkosTruk = 1050000;
                                                }

                                                // Uang Jalan & No Bukti
                                                $uj = $item->suratJalan->uangJalan;
                                                $uangJalan = $uj ? $uj->jumlah_total : 0;
                                                if ($uj && count($uj->pranotaUangJalan) > 0) {
                                                    $buktis = collect();
                                                    foreach ($uj->pranotaUangJalan as $puj) {
                                                        if ($puj->pembayaranPranotaUangJalans) {
                                                            $buktis = $buktis->merge($puj->pembayaranPranotaUangJalans->pluck('nomor_accurate'));
                                                        }
                                                    }
                                                    $noBukti = $buktis->filter()->unique()->implode(', ') ?: '-';
                                                }

                                                // Tgl TT Logic
                                                if ($item->suratJalan->tanggal_tanda_terima) {
                                                    $tglTt = $item->suratJalan->tanggal_tanda_terima->format('d/M/Y');
                                                } elseif ($item->suratJalan->tandaTerima && $item->suratJalan->tandaTerima->tanggal) {
                                                    $tglTt = $item->suratJalan->tandaTerima->tanggal->format('d/M/Y');
                                                }
                                            } elseif($item->type === 'SuratJalanBongkaran' && $item->suratJalanBongkaran) {
                                                $noPlatSj = $item->suratJalanBongkaran->no_plat ?? '-';
                                                $size = $item->suratJalanBongkaran->size ?? '-';
                                                $tglSj = $item->suratJalanBongkaran->tanggal_surat_jalan ? $item->suratJalanBongkaran->tanggal_surat_jalan->format('d/M/Y') : $tglSj;
                                                $tujuanSj = $item->suratJalanBongkaran->tujuanPengambilanRelation->ke ?? $item->suratJalanBongkaran->tujuan_pengambilan ?? '-';

                                                // Ongkos Truck Logic
                                                if ($item->suratJalanBongkaran->tujuanPengambilanRelation) {
                                                    $sz = strtolower($item->suratJalanBongkaran->size ?? '');
                                                    $ongkosTruk = str_contains($sz, '40') 
                                                        ? ($item->suratJalanBongkaran->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0)
                                                        : ($item->suratJalanBongkaran->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0);
                                                }
                                                if ($item->suratJalanBongkaran->tujuan_pengambilan == "PULO GADUNG ( BESI SCRAP )") {
                                                    $ongkosTruk = 1050000;
                                                }

                                                // Uang Jalan & No Bukti
                                                $ujb = $item->suratJalanBongkaran->uangJalan;
                                                $uangJalan = $ujb ? $ujb->jumlah_total : 0;
                                                if ($ujb && count($ujb->pranotaUangJalan) > 0) {
                                                    $buktis = collect();
                                                    foreach ($ujb->pranotaUangJalan as $puj) {
                                                        if ($puj->pembayaranPranotaUangJalans) {
                                                            $buktis = $buktis->merge($puj->pembayaranPranotaUangJalans->pluck('nomor_accurate'));
                                                        }
                                                    }
                                                    $noBukti = $buktis->filter()->unique()->implode(', ') ?: '-';
                                                }

                                                // Tgl TT Logic
                                                if ($item->suratJalanBongkaran->tandaTerima && $item->suratJalanBongkaran->tandaTerima->tanggal_tanda_terima) {
                                                    $tglTt = $item->suratJalanBongkaran->tandaTerima->tanggal_tanda_terima->format('d/M/Y');
                                                }
                                            }
                                        @endphp
                                        <td class="px-6 py-4 text-sm text-gray-600 font-medium text-center whitespace-nowrap">
                                            {{ $tglSj }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 font-medium text-center whitespace-nowrap">
                                            {{ $tglTt }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-black text-gray-900">{{ $item->no_surat_jalan }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                            {{ $noPlatSj }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 font-medium text-center">
                                            {{ $size }}
                                        </td>
                                        <td class="px-6 py-4 text-xs text-gray-500 font-medium italic">
                                            {{ $noBukti }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 {{ $item->type == 'SuratJalan' ? 'bg-blue-50 text-blue-600' : 'bg-teal-50 text-teal-600' }} rounded text-[9px] font-black uppercase whitespace-nowrap">
                                                {{ $item->type == 'SuratJalan' ? 'Reguler' : 'Bongkaran' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 min-w-[150px]">
                                            {{ $tujuanSj }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm text-gray-600 font-medium whitespace-nowrap">
                                            Rp {{ number_format($ongkosTruk, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm text-gray-600 font-medium whitespace-nowrap">
                                            Rp {{ number_format($uangJalan, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-black text-gray-900 text-sm whitespace-nowrap">
                                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50/50">
                                <tr>
                                    <td colspan="10" class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Subtotal Items
                                    </td>
                                    <td class="px-6 py-4 text-right font-black text-gray-900">
                                        Rp {{ number_format($pranota->items->sum('nominal'), 0, ',', '.') }}
                                    </td>
                                </tr>
                                @if($pranota->adjustments && is_array($pranota->adjustments))
                                    @foreach($pranota->adjustments as $index => $adj)
                                        @if(isset($adj['nominal']) && $adj['nominal'] != 0)
                                        <tr class="bg-white">
                                            <td colspan="10" class="px-6 py-2 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">
                                                Adjustment {{ $loop->iteration }}: {{ $adj['keterangan'] ?? 'Tanpa Keterangan' }}
                                            </td>
                                            <td class="px-6 py-2 text-right text-sm font-black {{ $adj['nominal'] < 0 ? 'text-red-500' : 'text-green-500' }}">
                                                {{ $adj['nominal'] < 0 ? '-' : '+' }} Rp {{ number_format(abs($adj['nominal']), 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                @elseif($pranota->adjustment != 0)
                                <tr class="bg-white">
                                    <td colspan="10" class="px-6 py-2 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Adjustment Value
                                    </td>
                                    <td class="px-6 py-2 text-right text-sm font-black {{ $pranota->adjustment < 0 ? 'text-red-500' : 'text-green-500' }}">
                                        {{ $pranota->adjustment < 0 ? '-' : '+' }} Rp {{ number_format(abs($pranota->adjustment), 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endif
                                <tr class="bg-blue-50/30">
                                    <td colspan="10" class="px-6 py-6 text-right text-sm font-black text-blue-600 uppercase tracking-widest">
                                        Grand Total
                                    </td>
                                    <td class="px-6 py-6 text-right text-2xl font-black text-blue-800">
                                        Rp {{ number_format($pranota->total_nominal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        header, #sidebar, .mb-6, .max-w-5xl {
            margin: 0 !important;
            box-shadow: none !important;
        }
        .max-w-5xl {
            max-width: 100% !important;
        }
        button, a { display: none !important; }
        .bg-gradient-to-r {
            background: #eee !important;
            color: black !important;
        }
        .text-white { color: black !important; }
        .bg-white\/20, .bg-white\/10 { border: 1px solid #ccc !important; }
    }
</style>
@endsection
