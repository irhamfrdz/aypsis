@extends('layouts.app')

@section('title', 'Report Lembur/Nginap')
@section('page_title', 'Report Lembur/Nginap')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-bed mr-3 text-blue-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Report Lembur/Nginap</h1>
                    <p class="text-gray-600">Laporan driver lembur/nginap berdasarkan periode</p>
                </div>
            </div>
            <a href="{{ route('report.lembur.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Pilih Periode Lain
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Tanggal Tanda Terima</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No SJ</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($suratJalans as $sj)
                    <tr>
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $sj->report_date ? \Carbon\Carbon::parse($sj->report_date)->format('d/M/Y') : '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sj->type_surat == 'Muat' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                {{ $sj->type_surat }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $sj->no_surat_jalan }}</td>
                        <td class="px-4 py-3">{{ $sj->supir }}</td>
                        <td class="px-4 py-3">{{ $sj->no_plat }}</td>
                        <td class="px-4 py-3">
                            @if($sj->lembur) <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold mr-1">Lembur</span> @endif
                            @if($sj->nginap) <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">Nginap</span> @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            {{-- Pagination Placehoder if needed later --}}
        </div>
    </div>
</div>
@endsection
