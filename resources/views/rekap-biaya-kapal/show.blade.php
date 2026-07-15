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
                <span class="flex items-center"><i class="fas fa-calendar-alt mr-2 text-blue-400"></i> Tanggal Cetak: <strong>{{ \Carbon\Carbon::now()->format('d F Y H:i') }}</strong></span>
                <span class="flex items-center"><i class="fas fa-file-invoice mr-2 text-blue-400"></i> Total Records: <strong>{{ $biayaKapals->count() }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Summary Metrics -->
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

    <!-- Visual Breakdown / Progress Bars -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-chart-pie text-indigo-600 mr-2"></i> Proporsi Biaya Berdasarkan Klasifikasi
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
                @endphp
                <div>
                    <div class="flex justify-between text-sm font-semibold text-gray-700 mb-1">
                        <span>{{ $category }}</span>
                        <span>Rp {{ number_format($groupTotal, 0, ',', '.') }} <span class="text-xs font-normal text-gray-400">({{ number_format($percentageOfGrand, 1) }}%)</span></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $percentageOfMax }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Detailed Cost Table -->
    <div class="space-y-6">
        <h2 class="text-xl font-extrabold text-gray-800 flex items-center border-b border-gray-200 pb-4 mb-4">
            <i class="fas fa-list-ol text-blue-600 mr-2"></i> Rincian Biaya Kapal
        </h2>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden page-break-inside-avoid">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-48">No. Invoice</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Klasifikasi</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">Total</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-20 no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @php $no = 1; @endphp
                        @forelse($biayaKapals as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ $no++ }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                    {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/M/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-gray-800">
                                    {{ $item->nomor_invoice }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-700">
                                    {{ $item->klasifikasiBiaya->nama ?? $item->jenis_biaya ?? 'Lain-lain' }}
                                </td>
                                <td class="px-6 py-4 text-right text-xs text-gray-900 font-bold whitespace-nowrap">
                                    Rp {{ number_format($item->apportioned['total_biaya'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap no-print">
                                    @if(isset($item->is_pranota_ob) && $item->is_pranota_ob)
                                        <a href="{{ route('pranota-ob.show', $item->id) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors tooltip" title="Lihat Detail Pranota OB">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                    @elseif(isset($item->is_amprahan) && $item->is_amprahan)
                                        <a href="{{ route('stock-amprahan.show', $item->stock_amprahan_id ?? $item->id) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors tooltip" title="Lihat Detail Stock Amprahan">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('biaya-kapal.show', ['biayaKapal' => $item->id, 'kapal' => $kapal, 'voyage' => $voyage]) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors tooltip" title="Lihat Detail Transaksi">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-ship text-gray-300 text-5xl mb-4 block"></i>
                                    <p class="font-semibold text-lg">Tidak ada data biaya kapal yang ditemukan</p>
                                    <p class="text-gray-400 text-sm mt-1">Silakan periksa kembali kapal dan nomor voyage yang Anda pilih.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($biayaKapals->count() > 0)
                        <tfoot class="bg-gray-50 font-bold">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right text-xs text-gray-500 uppercase tracking-wider">Total Akhir</td>
                                <td class="px-6 py-4 text-right text-sm text-gray-900">
                                    Rp {{ number_format($summary['grand_total'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 no-print"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

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
