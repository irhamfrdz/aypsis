@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Report Tanda Terima Jakarta</h1>
            <p class="text-gray-600">Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('report.tanda-terima-jakarta.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Change Filter
            </a>
            <a href="{{ route('report.tanda-terima-jakarta.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        @php
            $counts = $data->groupBy('source')->map->count();
            $sudahNaikKapal = $data->where('naik_kapal', true)->count();
            $belumNaikKapal = $data->where('naik_kapal', false)->count();
        @endphp
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="text-sm font-medium text-gray-500 mb-1">Standard</div>
            <div class="text-2xl font-bold text-purple-600">{{ $counts->get('Standard', 0) }}</div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="text-sm font-medium text-gray-500 mb-1">Tanpa SJ</div>
            <div class="text-2xl font-bold text-blue-600">{{ $counts->get('Tanpa SJ', 0) }}</div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="text-sm font-medium text-gray-500 mb-1">LCL</div>
            <div class="text-2xl font-bold text-orange-600">{{ $counts->get('LCL', 0) }}</div>
        </div>
        <div class="bg-emerald-50 p-6 rounded-xl shadow-sm border border-emerald-100">
            <div class="flex items-center gap-2 mb-1">
                <i class="fas fa-ship text-emerald-500 text-xs"></i>
                <span class="text-sm font-medium text-emerald-700">Sudah Naik Kapal</span>
            </div>
            <div class="text-2xl font-bold text-emerald-600">{{ $sudahNaikKapal }}</div>
        </div>
        <div class="bg-amber-50 p-6 rounded-xl shadow-sm border border-amber-100">
            <div class="flex items-center gap-2 mb-1">
                <i class="fas fa-clock text-amber-500 text-xs"></i>
                <span class="text-sm font-medium text-amber-700">Belum Naik Kapal</span>
            </div>
            <div class="text-2xl font-bold text-amber-600">{{ $belumNaikKapal }}</div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Sumber</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">No. TT / SJ</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Kontainer / Seal</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Size</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Pengirim</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Penerima</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tujuan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($data as $row)
                        <tr class="hover:bg-gray-50 transition-colors {{ $row['naik_kapal'] ? '' : 'bg-amber-50/40' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($row['source'] == 'Standard') bg-purple-100 text-purple-700
                                    @elseif($row['source'] == 'Tanpa SJ') bg-blue-100 text-blue-700
                                    @elseif($row['source'] == 'LCL') bg-orange-100 text-orange-700
                                    @else bg-emerald-100 text-emerald-700
                                    @endif">
                                    {{ $row['source'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($row['naik_kapal'])
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        <i class="fas fa-ship text-[10px]"></i>
                                        Sudah Naik Kapal
                                    </span>
                                    @if($row['nama_kapal'] || $row['no_voyage'])
                                        <div class="text-[10px] text-emerald-600 mt-0.5 font-medium leading-tight">
                                            @if($row['nama_kapal']) {{ $row['nama_kapal'] }} @endif
                                            @if($row['no_voyage']) <span class="text-gray-400">/ Voy.</span> {{ $row['no_voyage'] }} @endif
                                        </div>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        <i class="fas fa-clock text-[10px]"></i>
                                        Belum Naik Kapal
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $row['tanggal'] ? $row['tanggal']->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                {{ $row['no_tt'] }}
                                @if($row['no_sj_pabrik'] && $row['no_sj_pabrik'] != '-')
                                    <div class="text-xs text-gray-400 mt-0.5">SJ Pabrik: {{ $row['no_sj_pabrik'] }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $row['no_kontainer'] ?: '-' }}
                                @if($row['no_seal'] && $row['no_seal'] != '-')
                                    <div class="text-[10px] text-green-600 font-bold mt-0.5"><i class="fas fa-lock text-[9px] mr-1"></i>{{ $row['no_seal'] }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $row['size'] ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ Str::limit($row['pengirim'], 20) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ Str::limit($row['penerima'], 20) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ Str::limit($row['tujuan'], 20) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ Str::limit($row['keterangan'], 30) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center text-gray-500 italic">
                                No data found for the selected period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
