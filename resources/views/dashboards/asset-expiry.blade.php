@extends('layouts.app')

@section('title', 'Dashboard Jatuh Tempo Asset')
@section('page_title', 'Dashboard Jatuh Tempo Asset')

@section('content')
<div class="space-y-6">
    <!-- Tab Navigation -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="switchTab('asuransi')" id="tab-asuransi" class="tab-button active group relative min-w-0 flex-1 overflow-hidden bg-white py-4 px-4 text-sm font-medium text-center hover:bg-gray-50 focus:z-10 transition-colors">
                    <span>Asuransi</span>
                    <span aria-hidden="true" class="tab-indicator bg-blue-500 absolute inset-x-0 bottom-0 h-0.5"></span>
                </button>
                <button onclick="switchTab('plat')" id="tab-plat" class="tab-button group relative min-w-0 flex-1 overflow-hidden bg-white py-4 px-4 text-sm font-medium text-center hover:bg-gray-50 focus:z-10 transition-colors">
                    <span>Plat/STNK</span>
                    <span aria-hidden="true" class="tab-indicator bg-blue-500 absolute inset-x-0 bottom-0 h-0.5 opacity-0"></span>
                </button>
                <button onclick="switchTab('kir')" id="tab-kir" class="tab-button group relative min-w-0 flex-1 overflow-hidden bg-white py-4 px-4 text-sm font-medium text-center hover:bg-gray-50 focus:z-10 transition-colors">
                    <span>KIR</span>
                    <span aria-hidden="true" class="tab-indicator bg-blue-500 absolute inset-x-0 bottom-0 h-0.5 opacity-0"></span>
                </button>
                <button onclick="switchTab('pajak')" id="tab-pajak" class="tab-button group relative min-w-0 flex-1 overflow-hidden bg-white py-4 px-4 text-sm font-medium text-center hover:bg-gray-50 focus:z-10 transition-colors">
                    <span>Pajak</span>
                    <span aria-hidden="true" class="tab-indicator bg-blue-500 absolute inset-x-0 bottom-0 h-0.5 opacity-0"></span>
                </button>
            </nav>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total Assets Card -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p id="total-assets-label" class="text-sm text-gray-600 font-medium">Total Asset dengan Asuransi</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        <span id="total-assets-count">{{ $stats['asuransi']['total_assets'] }}</span>
                    </p>
                </div>
                <div class="p-2 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Expiring Soon Card -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p id="expiring-label" class="text-sm text-gray-600 font-medium">Asuransi Jatuh Tempo 30 Hari</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">
                        <span id="expiring-count">{{ $stats['asuransi']['expiring_soon'] }}</span>
                    </p>
                </div>
                <div class="p-2 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Expired Card -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p id="expired-label" class="text-sm text-gray-600 font-medium">Asuransi Sudah Lewat Tempo</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">
                        <span id="expired-count">{{ $stats['asuransi']['expired'] }}</span>
                    </p>
                </div>
                <div class="p-2 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- No Date Card -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-gray-500">
            <div class="flex items-center justify-between">
                <div>
                    <p id="nodate-label" class="text-sm text-gray-600 font-medium">Asset Tanpa Tanggal Asuransi</p>
                    <p class="text-3xl font-bold text-gray-600 mt-2">
                        <span id="nodate-count">{{ $stats['asuransi']['no_date'] }}</span>
                    </p>
                </div>
                <div class="p-2 bg-gray-100 rounded-full">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Contents -->
    @foreach(['asuransi', 'plat', 'kir', 'pajak'] as $type)
    <div id="content-{{ $type }}" class="tab-content {{ $type !== 'asuransi' ? 'hidden' : '' }}">
        @php
            $typeLabels = [
                'asuransi' => 'Asuransi',
                'plat' => 'Plat/STNK',
                'kir' => 'KIR',
                'pajak' => 'Pajak'
            ];
            $typeLabel = $typeLabels[$type];
            $dateField = $type === 'asuransi' ? 'tanggal_jatuh_tempo_asuransi' : 'pajak_' . ($type === 'plat' ? 'stnk' : $type);
        @endphp

        <!-- Expiring Soon Section -->
        @if($expiringAssets[$type]->count() > 0)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-yellow-50 border-b border-yellow-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h2 class="text-xl font-bold text-yellow-800">Asset {{ $typeLabel }} Jatuh Tempo 30 Hari Ke Depan</h2>
                        <span class="ml-3 bg-yellow-600 text-white px-3 py-1 rounded-full text-sm font-semibold">{{ $expiringAssets[$type]->count() }} Asset</span>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="downloadExcel('expiring-table-{{ $type }}', 'Asset_{{ $typeLabel }}_Jatuh_Tempo_30_Hari')" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 no-print">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Excel
                        </button>
                        <button onclick="printTable('expiring-table-{{ $type }}')" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 no-print">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto" id="expiring-table-{{ $type }}">
                <table class="min-w-full bg-white divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode No</th>
                            <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plat/KIR</th>
                            <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merek</th>
                            <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            @if($type === 'asuransi')
                            <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perusahaan Asuransi</th>
                            @endif
                            <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Jatuh Tempo</th>
                            <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Hari</th>
                            <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                        @foreach($expiringAssets[$type] as $index => $asset)
                        @php
                            $tanggal = \Carbon\Carbon::parse($asset->$dateField);
                            $today = \Carbon\Carbon::today();
                            $diffDays = $today->diffInDays($tanggal, false);
                            
                            $bulanIndonesia = [
                                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                                5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
                                9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                            ];
                            $formattedDate = $tanggal->format('d') . ' ' . $bulanIndonesia[$tanggal->month] . ' ' . $tanggal->format('Y');
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-3 text-sm">{{ $index + 1 }}</td>
                            <td class="py-2 px-3 font-mono text-xs">{{ $asset->kode_no }}</td>
                            <td class="py-2 px-3">
                                <div class="space-y-1">
                                    @if($asset->nomor_polisi)
                                        <div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $asset->nomor_polisi }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($asset->no_kir)
                                        <div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                {{ $asset->no_kir }}
                                            </span>
                                        </div>
                                    @endif
                                    @if(!$asset->nomor_polisi && !$asset->no_kir)
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2 px-3 text-sm">{{ $asset->merek ?? '-' }}</td>
                            <td class="py-2 px-3 text-sm">{{ $asset->jenis ?? '-' }}</td>
                            @if($type === 'asuransi')
                            <td class="py-2 px-3 text-sm">{{ $asset->asuransi ?? '-' }}</td>
                            @endif
                            <td class="py-2 px-3 text-sm">{{ $formattedDate }}</td>
                            <td class="py-2 px-3">
                                @if($diffDays <= 7)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                        {{ $diffDays }} hari
                                    </span>
                                @elseif($diffDays <= 14)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800">
                                        {{ $diffDays }} hari
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                        {{ $diffDays }} hari
                                    </span>
                                @endif
                            </td>
                            <td class="py-2 px-3 text-center">
                                <a href="{{ route('master.mobil.show', $asset->id) }}" class="bg-blue-500 text-white py-1 px-2 rounded text-xs hover:bg-blue-600 transition-colors duration-200">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Tidak Ada Asset yang Akan Jatuh Tempo</h3>
                <p class="mt-1 text-sm text-gray-500">Semua asset {{ $typeLabel }} aman untuk 30 hari ke depan</p>
            </div>
        </div>
        @endif

        <!-- Expired Section -->
        @if($expiredAssets[$type]->count() > 0)
        <div class="bg-white rounded-lg shadow-md overflow-hidden mt-6">
            <div class="bg-red-50 border-b border-red-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <h2 class="text-xl font-bold text-red-800">Asset {{ $typeLabel }} Sudah Lewat Jatuh Tempo</h2>
                        <span class="ml-3 bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold">{{ $expiredAssets[$type]->count() }} Asset</span>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="downloadExcel('expired-table-{{ $type }}', 'Asset_{{ $typeLabel }}_Sudah_Lewat_Jatuh_Tempo')" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 no-print">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Excel
                        </button>
                        <button onclick="printTable('expired-table-{{ $type }}')" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 no-print">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto" id="expired-table-{{ $type }}">
                <table class="min-w-full bg-white divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode No</th>
                            <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plat/KIR</th>
                            <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merek</th>
                            <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            @if($type === 'asuransi')
                            <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perusahaan Asuransi</th>
                            @endif
                            <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Jatuh Tempo</th>
                            <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lewat</th>
                            <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                        @foreach($expiredAssets[$type] as $index => $asset)
                        @php
                            $tanggal = \Carbon\Carbon::parse($asset->$dateField);
                            $today = \Carbon\Carbon::today();
                            $diffDays = abs($today->diffInDays($tanggal, false));
                            
                            $bulanIndonesia = [
                                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                                5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
                                9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                            ];
                            $formattedDate = $tanggal->format('d') . ' ' . $bulanIndonesia[$tanggal->month] . ' ' . $tanggal->format('Y');
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-3 text-sm">{{ $index + 1 }}</td>
                            <td class="py-2 px-3 font-mono text-xs">{{ $asset->kode_no }}</td>
                            <td class="py-2 px-3">
                                <div class="space-y-1">
                                    @if($asset->nomor_polisi)
                                        <div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $asset->nomor_polisi }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($asset->no_kir)
                                        <div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                {{ $asset->no_kir }}
                                            </span>
                                        </div>
                                    @endif
                                    @if(!$asset->nomor_polisi && !$asset->no_kir)
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2 px-3 text-sm">{{ $asset->merek ?? '-' }}</td>
                            <td class="py-2 px-3 text-sm">{{ $asset->jenis ?? '-' }}</td>
                            @if($type === 'asuransi')
                            <td class="py-2 px-3 text-sm">{{ $asset->asuransi ?? '-' }}</td>
                            @endif
                            <td class="py-2 px-3 text-sm">{{ $formattedDate }}</td>
                            <td class="py-2 px-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                    {{ $diffDays }} hari
                                </span>
                            </td>
                            <td class="py-2 px-3 text-center">
                                <a href="{{ route('master.mobil.show', $asset->id) }}" class="bg-blue-500 text-white py-1 px-2 rounded text-xs hover:bg-blue-600 transition-colors duration-200">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg shadow-md p-8 mt-6">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Tidak Ada Asset yang Lewat Jatuh Tempo</h3>
                <p class="mt-1 text-sm text-gray-500">Semua asset {{ $typeLabel }} masih dalam periode yang valid</p>
            </div>
        </div>
        @endif
    </div>
    @endforeach
</div>

<!-- SheetJS Library for Excel Export -->
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>

<style>
/* Tab Styles */
.tab-button {
    color: #6b7280;
}

.tab-button.active {
    color: #2563eb;
    font-weight: 600;
}

.tab-button .tab-indicator {
    transition: opacity 0.2s;
}

.tab-button.active .tab-indicator {
    opacity: 1 !important;
}

/* Print Styles */
@media print {
    body * {
        visibility: hidden;
    }
    
    #print-area, #print-area * {
        visibility: visible;
    }
    
    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    
    .no-print, .no-print * {
        display: none !important;
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
    
    .bg-yellow-50, .bg-red-50 {
        background-color: white !important;
    }
    
    .shadow-md {
        box-shadow: none !important;
    }
    
    .bg-blue-100, .bg-green-100, .bg-yellow-100, .bg-red-100, .bg-orange-100 {
        border: 1px solid #000;
    }
}
</style>

<script>
// Store stats data for tab switching
const statsData = @json($stats);

// Tab Switching Function
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Show selected tab content
    document.getElementById(`content-${tabName}`).classList.remove('hidden');
    
    // Update tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
        button.querySelector('.tab-indicator').style.opacity = '0';
    });
    
    const activeButton = document.getElementById(`tab-${tabName}`);
    activeButton.classList.add('active');
    activeButton.querySelector('.tab-indicator').style.opacity = '1';
    
    // Define labels for each category
    const labels = {
        'asuransi': {
            total: 'Total Asset dengan Asuransi',
            expiring: 'Asuransi Jatuh Tempo 30 Hari',
            expired: 'Asuransi Sudah Lewat Tempo',
            nodate: 'Asset Tanpa Tanggal Asuransi'
        },
        'plat': {
            total: 'Total Asset dengan Pajak STNK',
            expiring: 'Pajak STNK Jatuh Tempo 30 Hari',
            expired: 'Pajak STNK Sudah Lewat Tempo',
            nodate: 'Asset Tanpa Tanggal Pajak STNK'
        },
        'kir': {
            total: 'Total Asset dengan KIR',
            expiring: 'KIR Jatuh Tempo 30 Hari',
            expired: 'KIR Sudah Lewat Tempo',
            nodate: 'Asset Tanpa Tanggal KIR'
        },
        'pajak': {
            total: 'Total Asset dengan Pajak',
            expiring: 'Pajak Jatuh Tempo 30 Hari',
            expired: 'Pajak Sudah Lewat Tempo',
            nodate: 'Asset Tanpa Tanggal Pajak'
        }
    };
    
    // Update statistics cards counts
    document.getElementById('total-assets-count').textContent = statsData[tabName].total_assets;
    document.getElementById('expiring-count').textContent = statsData[tabName].expiring_soon;
    document.getElementById('expired-count').textContent = statsData[tabName].expired;
    document.getElementById('nodate-count').textContent = statsData[tabName].no_date;
    
    // Update statistics cards labels
    document.getElementById('total-assets-label').textContent = labels[tabName].total;
    document.getElementById('expiring-label').textContent = labels[tabName].expiring;
    document.getElementById('expired-label').textContent = labels[tabName].expired;
    document.getElementById('nodate-label').textContent = labels[tabName].nodate;
}

// Excel Download Function
function downloadExcel(tableId, filename) {
    const tableContainer = document.getElementById(tableId);
    
    if (!tableContainer) {
        alert('Tabel tidak ditemukan!');
        return;
    }
    
    const table = tableContainer.querySelector('table');
    
    if (!table) {
        alert('Tabel tidak ditemukan!');
        return;
    }
    
    const tableClone = table.cloneNode(true);
    
    // Remove action column from header
    const headerRow = tableClone.querySelector('thead tr');
    if (headerRow) {
        const headerCells = headerRow.querySelectorAll('th');
        if (headerCells.length > 0) {
            headerCells[headerCells.length - 1].remove();
        }
    }
    
    // Remove action column from all rows
    const bodyRows = tableClone.querySelectorAll('tbody tr');
    bodyRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length > 0) {
            cells[cells.length - 1].remove();
        }
    });
    
    // Extract data
    const data = [];
    
    // Headers
    const headers = [];
    const headerCells = tableClone.querySelectorAll('thead th');
    headerCells.forEach(cell => {
        headers.push(cell.textContent.trim());
    });
    data.push(headers);
    
    // Body data
    const rows = tableClone.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const rowData = [];
        const cells = row.querySelectorAll('td');
        
        cells.forEach((cell, index) => {
            let cellText = cell.textContent.trim();
            
            if (cell.querySelector('.inline-flex')) {
                const badges = cell.querySelectorAll('.inline-flex');
                const badgeTexts = Array.from(badges).map(badge => badge.textContent.trim());
                cellText = badgeTexts.join(', ');
            }
            
            cellText = cellText.replace(/\s+/g, ' ').trim();
            rowData.push(cellText);
        });
        
        data.push(rowData);
    });
    
    // Create workbook
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(data);
    
    const colWidths = [
        { wch: 5 },
        { wch: 15 },
        { wch: 20 },
        { wch: 20 },
        { wch: 20 },
        { wch: 20 },
        { wch: 15 }
    ];
    ws['!cols'] = colWidths;
    
    XLSX.utils.book_append_sheet(wb, ws, 'Data');
    
    const today = new Date();
    const dateStr = today.toISOString().split('T')[0];
    const fullFilename = `${filename}_${dateStr}.xlsx`;
    
    XLSX.writeFile(wb, fullFilename);
}

// Print Function
function printTable(tableId) {
    const tableContainer = document.getElementById(tableId);
    
    if (!tableContainer) {
        alert('Tabel tidak ditemukan!');
        return;
    }
    
    const tableClone = tableContainer.cloneNode(true);
    
    // Remove action headers
    const actionHeaders = tableClone.querySelectorAll('th');
    actionHeaders.forEach((header, index) => {
        if (header.textContent.trim() === 'Aksi') {
            header.remove();
        }
    });
    
    // Remove action cells
    const rows = tableClone.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length > 0) {
            cells[cells.length - 1].remove();
        }
    });
    
    // Determine table title
    let headerTitle = tableId.includes('expiring') ? 'Asset Jatuh Tempo 30 Hari Ke Depan' : 'Asset Sudah Lewat Jatuh Tempo';
    
    const printWindow = window.open('', '', 'height=600,width=800');
    
    printWindow.document.write('<html><head><title>' + headerTitle + '</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; padding: 20px; }');
    printWindow.document.write('h1 { text-align: center; font-size: 18px; margin-bottom: 20px; }');
    printWindow.document.write('table { width: 100%; border-collapse: collapse; margin-top: 20px; }');
    printWindow.document.write('th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 11px; }');
    printWindow.document.write('th { background-color: #f3f4f6; font-weight: bold; }');
    printWindow.document.write('tr:nth-child(even) { background-color: #f9fafb; }');
    printWindow.document.write('.inline-flex { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 10px; margin: 2px 0; }');
    printWindow.document.write('.bg-blue-100 { background-color: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }');
    printWindow.document.write('.bg-green-100 { background-color: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }');
    printWindow.document.write('.bg-yellow-100 { background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }');
    printWindow.document.write('.bg-red-100 { background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }');
    printWindow.document.write('.bg-orange-100 { background-color: #ffedd5; color: #9a3412; border: 1px solid #fdba74; }');
    printWindow.document.write('.print-date { text-align: right; font-size: 10px; color: #666; margin-bottom: 10px; }');
    printWindow.document.write('@media print { body { padding: 10px; } }');
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h1>' + headerTitle + '</h1>');
    
    const today = new Date();
    const dateStr = today.toLocaleDateString('id-ID', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    printWindow.document.write('<div class="print-date">Dicetak pada: ' + dateStr + '</div>');
    
    printWindow.document.write(tableClone.innerHTML);
    printWindow.document.write('</body></html>');
    
    printWindow.document.close();
    printWindow.focus();
    
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
}
</script>
@endsection

