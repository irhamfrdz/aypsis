@extends('layouts.app')

@section('title', 'Laporan Surat Jalan')
@section('page_title', 'Laporan Surat Jalan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center">
                <i class="fas fa-file-alt mr-3 text-blue-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Laporan Surat Jalan</h1>
                    <p class="text-gray-600">
                        Periode: 
                        <span class="font-semibold">{{ $startDate->format('d/m/Y') }}</span> 
                        s/d 
                        <span class="font-semibold">{{ $endDate->format('d/m/Y') }}</span>
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('report.surat_jalan.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <a href="{{ route('report.surat_jalan.export', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">No Surat Jalan</th>
                        <th class="px-6 py-3">Plat Mobil</th>
                        <th class="px-6 py-3">Supir</th>
                        <th class="px-6 py-3">Kenek</th>
                        <th class="px-6 py-3">Rute</th>
                        <th class="px-6 py-3">Uang Jalan</th>
                        <th class="px-6 py-3">Nomor Bukti</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $item['tanggal']->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 font-medium text-blue-600">
                                {{ $item['no_surat_jalan'] }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $item['no_plat'] }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $item['supir'] }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $item['kenek'] }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $item['rute'] }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                Rp {{ number_format($item['uang_jalan'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $item['nomor_bukti'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                    <p>Tidak ada data surat jalan pada periode ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t bg-gray-50">
            <p class="text-sm text-gray-600">
                Total Data: <span class="font-bold">{{ $data->count() }}</span>
            </p>
        </div>
    </div>
</div>
@endsection
