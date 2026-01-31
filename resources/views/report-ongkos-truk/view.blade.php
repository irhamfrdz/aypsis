@extends('layouts.app')

@section('title', 'Hasil Laporan Ongkos Truk')
@section('page_title', 'Hasil Laporan Ongkos Truk')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center">
                <a href="{{ route('report.ongkos-truk.index') }}" class="mr-4 text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Laporan Ongkos Truk</h1>
                    <p class="text-gray-600">
                        Periode: <span class="font-semibold text-blue-600">{{ $startDate->format('d/M/Y') }}</span> s/d <span class="font-semibold text-blue-600">{{ $endDate->format('d/M/Y') }}</span>
                        @if($noPlat)
                            | Unit: <span class="font-semibold text-blue-600">{{ is_array($noPlat) ? implode(', ', $noPlat) : $noPlat }}</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="window.print()" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-50 transition duration-200 flex items-center shadow-sm font-medium">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
                <a href="{{ route('report.ongkos-truk.export', request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-200 flex items-center shadow-sm font-medium">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </a>
            </div>
        </div>
    </div>

    {{-- Filtered Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-700 uppercase text-xs font-bold tracking-wider">
                        <th class="px-6 py-4 border-b">No</th>
                        <th class="px-6 py-4 border-b text-center">Tanggal</th>
                        <th class="px-6 py-4 border-b">No. Surat Jalan</th>
                        <th class="px-6 py-4 border-b">Plat Mobil</th>
                        <th class="px-6 py-4 border-b">Supir</th>
                        <th class="px-6 py-4 border-b">Keterangan</th>
                        <th class="px-6 py-4 border-b text-center">Rit</th>
                        <th class="px-6 py-4 border-b text-right">Ongkos Truk</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                    @forelse($data as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 text-center">{{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $item['no_surat_jalan'] }}</td>
                            <td class="px-6 py-4">{{ $item['no_plat'] }}</td>
                            <td class="px-6 py-4">{{ $item['supir'] }}</td>
                            <td class="px-6 py-4">{{ $item['keterangan'] }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($item['rit'] == 'menggunakan_rit')
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase">Ya</span>
                                @else
                                    <span class="text-gray-400 font-medium">Tidak</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-800">
                                Rp {{ number_format($item['ongkos_truck'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-400 italic">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-folder-open text-4xl mb-2"></i>
                                    <span>Tidak ada data untuk periode dan filter yang dipilih.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($data->count() > 0)
                <tfoot class="bg-gray-50 font-bold text-gray-800 uppercase text-xs">
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-right border-t">Grand Total</td>
                        <td class="px-6 py-4 text-right border-t text-sm">
                            Rp {{ number_format($data->sum('ongkos_truck'), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<style>
    @media print {
        header, .lg\:top-16, #sidebar, .bg-gray-100 {
            display: none !important;
        }
        .container {
            width: 100% !important;
            max-width: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .bg-white {
            box-shadow: none !important;
            border: none !important;
        }
        .bg-gray-50 {
            background-color: transparent !important;
        }
        .px-4, .px-6, .py-6, .p-6 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .mb-6 {
            margin-bottom: 20px !important;
        }
        table {
            border: 1px solid #000 !important;
        }
        th, td {
            border: 1px solid #000 !important;
            color: #000 !important;
        }
        .text-blue-600 {
            color: #000 !important;
        }
        .bg-blue-100 {
            background-color: transparent !important;
            border: none !important;
            padding: 0 !important;
        }
    }
</style>
@endsection
