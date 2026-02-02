@extends('layouts.app')

@section('title', 'Report Pranota OB')
@section('page_title', 'Report Pranota OB')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-file-invoice mr-3 text-blue-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Report Pranota OB</h1>
                    <p class="text-gray-600">
                        Periode: {{ $dariTanggal->format('d M Y') }} - {{ $sampaiTanggal->format('d M Y') }}
                    </p>
                </div>
            </div>
            <a href="{{ route('report.pranota-ob.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Pilih Periode Lain
            </a>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                <i class="fas fa-print mr-2"></i>
                Print
            </button>
            <a href="{{ route('report.pranota-ob.export', ['dari_tanggal' => request('dari_tanggal'), 'sampai_tanggal' => request('sampai_tanggal')]) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                <i class="fas fa-file-excel mr-2"></i>
                Export EXCEL
            </a>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($groupedByVoyage->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Voyage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $no = 1; @endphp
                    @foreach($groupedByVoyage as $voyage => $items)
                        {{-- Voyage Header --}}
                        <tr class="bg-blue-50">
                            <td colspan="6" class="px-6 py-3 text-sm font-bold text-blue-900">
                                <i class="fas fa-ship mr-2"></i>VOYAGE: {{ $voyage ?? 'Tidak Ada Voyage' }}
                            </td>
                        </tr>
                        {{-- Items in this voyage --}}
                        @foreach($items as $item)
                            @if($item->supir && $item->total_biaya > 0)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $no++ }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($item->tanggal_ob)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->no_voyage ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->nik ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->supir ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                                    Rp {{ number_format($item->total_biaya, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endif
                        @endforeach
                        {{-- Subtotal for this voyage --}}
                        <tr class="bg-gray-100">
                            <td colspan="5" class="px-6 py-3 text-sm font-bold text-gray-900 text-right">
                                Subtotal {{ $voyage ?? 'Tidak Ada Voyage' }}:
                            </td>
                            <td class="px-6 py-3 text-sm font-bold text-gray-900 text-right">
                                Rp {{ number_format($items->sum('total_biaya'), 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right text-sm font-bold text-gray-900">
                            TOTAL KESELURUHAN:
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">
                            Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="px-6 py-3 text-center text-sm text-gray-600">
                            <i class="fas fa-list mr-2"></i>
                            Total: {{ $groupedByVoyage->flatten()->count() }} supir
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-500 text-lg">Tidak ada data pranota OB untuk periode yang dipilih</p>
            <a href="{{ route('report.pranota-ob.index') }}" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Pilih Periode Lain
            </a>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    @media print {
        .bg-gray-500, .bg-white:has(button), button, a[href*="pilih"] {
            display: none !important;
        }
        
        body {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
        
        .container {
            max-width: 100% !important;
        }
        
        table {
            page-break-inside: auto;
        }
        
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        thead {
            display: table-header-group;
        }
        
        tfoot {
            display: table-footer-group;
        }
    }
</style>
@endpush
@endsection
