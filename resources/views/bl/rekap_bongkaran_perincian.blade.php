@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl no-print">
    <!-- Action Bar -->
    <div class="flex items-center justify-between mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <a href="{{ route('bl.rekap-bongkaran-perincian.select') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition duration-150 ease-in-out shadow-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Pemilihan
        </a>
        <div class="flex space-x-2">
            <a href="{{ route('bl.rekap-bongkaran-perincian.print', ['nama_kapal' => $namaKapal, 'no_voyage' => $noVoyage]) }}" target="_blank" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl shadow-sm hover:bg-indigo-700 focus:outline-none transition duration-150 ease-in-out">
                <i class="fas fa-print mr-2"></i> Cetak / Print
            </a>
        </div>
    </div>
</div>

<!-- Print & Preview Layout -->
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-12 print:shadow-none print:border-none print:p-0 print-half-folio">
        
        <!-- Document Title -->
        <div class="text-center mb-8 border-b-2 border-double border-gray-300 pb-4">
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 uppercase tracking-wider underline">Rekapan Bongkar/Muat Barang Perincian</h1>
        </div>

        <!-- Metadata Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8 text-sm md:text-base border border-gray-200 rounded-xl p-4 md:p-6 bg-gray-50/50 print:bg-white print:border-gray-300 metadata-grid">
            <div class="space-y-2">
                <div class="flex">
                    <span class="font-bold text-gray-700 w-32 uppercase">Nama Kapal</span>
                    <span class="text-gray-900">: {{ $namaKapal }}</span>
                </div>
                <div class="flex">
                    <span class="font-bold text-gray-700 w-32 uppercase">Voyage</span>
                    <span class="text-gray-900">: {{ $noVoyage }}</span>
                </div>
                <div class="flex">
                    <span class="font-bold text-gray-700 w-32 uppercase">Dari</span>
                    <span class="text-gray-900">: 
                        @if(Str::contains($noVoyage, 'BJ')) Batam 
                        @elseif(Str::contains($noVoyage, 'PJ')) Pinang
                        @elseif(Str::contains($noVoyage, ['JB', 'JP'])) Jakarta
                        @else - @endif
                    </span>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex">
                    <span class="font-bold text-gray-700 w-32 uppercase">Estimasi Tiba</span>
                    <span class="text-gray-900">: {{ $estTiba }}</span>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-400 text-[11px] leading-tight">
                <thead>
                    <tr class="bg-gray-100 print:bg-gray-100">
                        <th colspan="2" class="border border-gray-400 px-2 py-0.5 text-center font-bold text-gray-900 uppercase w-[15%]">Jumlah Barang</th>
                        <th class="border border-gray-400 px-2 py-0.5 text-center font-bold text-gray-900 uppercase w-[6%] whitespace-nowrap">No. BL</th>
                        <th class="border border-gray-400 px-2 py-0.5 text-left font-bold text-gray-900 uppercase w-[59%]">Nama Barang</th>
                        <th colspan="2" class="border border-gray-400 px-2 py-0.5 text-center font-bold text-gray-900 uppercase w-[20%]">Ton / M3</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50/50 transition duration-150">
                            <!-- Qty -->
                            <td class="border border-gray-400 px-2 py-0.5 text-right font-medium w-16 text-gray-900">
                                {{ number_format($item['kuantitas'], 0, ',', '.') }}
                            </td>
                            <!-- Qty Unit -->
                            <td class="border border-gray-400 px-2 py-0.5 text-left text-gray-700 w-14 border-l-0">
                                {{ $item['satuan'] }}
                            </td>
                            <!-- Nomor BL -->
                            <td class="border border-gray-400 px-2 py-0.5 text-center text-gray-900">
                                {{ $item['nomor_bl'] ?? '-' }}
                            </td>
                            <!-- Nama Barang -->
                            <td class="border border-gray-400 px-2 py-0.5 text-left font-semibold text-gray-900" title="{{ $item['nama_barang'] }}">
                                {{ Str::limit($item['nama_barang'], 80) }}
                            </td>
                            <!-- Ton/M3 Amount -->
                            <td class="border border-gray-400 px-2 py-0.5 text-right font-medium w-24 text-gray-900">
                                {{ $item['amount'] !== null ? number_format($item['amount'], 3, ',', '.') : '' }}
                            </td>
                            <!-- Ton/M3 Unit -->
                            <td class="border border-gray-400 px-2 py-0.5 text-center text-gray-700 w-10 border-l-0">
                                {{ $item['unit'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border border-gray-400 px-2 py-4 text-center text-gray-500 italic">
                                Tidak ada data bongkar/muat perincian untuk kapal dan voyage ini.
                            </td>
                        </tr>
                    @endforelse
                    
                    <!-- Total Row -->
                    @if($items->count() > 0)
                        <tr class="bg-gray-50/80 font-bold print:bg-white">
                            <td colspan="4" class="border border-gray-400 px-2 py-0.5 text-left text-gray-900 uppercase tracking-wider">
                                Jumlah
                            </td>
                            <td class="border border-gray-400 px-2 py-0.5 text-right text-gray-900">
                                {{ number_format($totalAmount, 3, ',', '.') }}
                            </td>
                            <td class="border border-gray-400 px-2 py-0.5 text-center text-gray-700">
                                Kgs/m3
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

    </div>
</div>

<style>
@media print {
    @page {
        size: 165.1mm 215.9mm; /* Half-Folio */
        margin: 5mm;
    }
    body {
        background-color: white !important;
        color: black !important;
        font-size: 10px !important;
    }
    .no-print {
        display: none !important;
    }
    /* Hide layout header, sidebar, and main page scrollbars when printing */
    header, #sidebar, #mobile-menu-button, footer {
        display: none !important;
    }
    /* Expand content area to full page width */
    .flex-1 {
        overflow: visible !important;
    }
    .h-screen {
        height: auto !important;
    }
    .overflow-hidden {
        overflow: visible !important;
    }
    main, .container {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
    }
    .print-half-folio {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
    }
    .print-half-folio h1 {
        font-size: 16px !important;
        margin-bottom: 8px !important;
    }
    .print-half-folio .metadata-grid {
        font-size: 10px !important;
        padding: 8px !important;
        margin-bottom: 12px !important;
    }
    .print-half-folio table {
        font-size: 9px !important;
    }
    .print-half-folio th, .print-half-folio td {
        padding: 4px 6px !important;
    }
}
</style>
@endsection
