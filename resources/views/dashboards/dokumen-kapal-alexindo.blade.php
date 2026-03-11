@extends('layouts.app')

@section('title', 'Dashboard Jatuh Tempo Dokumen Kapal')
@section('page_title', 'Dashboard Jatuh Tempo Dokumen Kapal Alexindo')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total Documents Card -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500 cursor-pointer hover:shadow-lg transition-all duration-200 select-none card-toggle"
             data-target="section-total">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Dokumen (Berjangka)</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        <span>{{ $stats['total_dokumen'] }}</span>
                    </p>
                </div>
                <div class="p-2 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-blue-500 mt-3 font-medium flex items-center gap-1 toggle-hint">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                Klik untuk lihat data
            </p>
        </div>

        <!-- Expiring Soon Card -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500 cursor-pointer hover:shadow-lg transition-all duration-200 select-none card-toggle"
             data-target="section-expiring">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Jatuh Tempo 30 Hari</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">
                        <span>{{ $stats['expiring_soon'] }}</span>
                    </p>
                </div>
                <div class="p-2 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-yellow-500 mt-3 font-medium flex items-center gap-1 toggle-hint">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                Klik untuk lihat data
            </p>
        </div>

        <!-- Expired Card -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500 cursor-pointer hover:shadow-lg transition-all duration-200 select-none card-toggle"
             data-target="section-expired">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Sudah Lewat Tempo</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">
                        <span>{{ $stats['expired'] }}</span>
                    </p>
                </div>
                <div class="p-2 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-red-500 mt-3 font-medium flex items-center gap-1 toggle-hint">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                Klik untuk lihat data
            </p>
        </div>

        <!-- No Date Card -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-gray-500 cursor-pointer hover:shadow-lg transition-all duration-200 select-none card-toggle"
             data-target="section-nodate">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Dokumen Tanpa Tanggal Berakhir</p>
                    <p class="text-3xl font-bold text-gray-600 mt-2">
                        <span>{{ $stats['no_date'] }}</span>
                    </p>
                </div>
                <div class="p-2 bg-gray-100 rounded-full">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3 font-medium flex items-center gap-1 toggle-hint">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                Klik untuk lihat data
            </p>
        </div>
    </div>

    <!-- ==================== SECTION: TOTAL DOKUMEN ==================== -->
    <div id="section-total" class="collapsible-section hidden bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-blue-50 border-b border-blue-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h2 class="text-xl font-bold text-blue-800">Semua Dokumen Berjangka</h2>
                    <span class="ml-3 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">{{ $totalDokumens->count() }} Dokumen</span>
                </div>
                <div class="flex gap-2">
                    <button onclick="downloadExcel('total-table', 'Total_Dokumen_Berjangka')" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 no-print">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Excel
                    </button>
                    <button onclick="printTable('total-table')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 no-print">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto" id="total-table">
            <table class="min-w-full bg-white divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapal</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dokumen</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Dokumen</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Berakhir</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                    @forelse($totalDokumens as $index => $dokumen)
                    @php
                        $tanggal = \Carbon\Carbon::parse($dokumen->tanggal_berakhir);
                        $today = \Carbon\Carbon::today();
                        $diffDays = $today->diffInDays($tanggal, false);
                        if ($diffDays < 0) {
                            $statusClass = 'bg-red-100 text-red-800';
                            $statusLabel = abs($diffDays) . ' hari lewat';
                        } elseif ($diffDays <= 30) {
                            $statusClass = 'bg-yellow-100 text-yellow-800';
                            $statusLabel = $diffDays . ' hari lagi';
                        } else {
                            $statusClass = 'bg-green-100 text-green-800';
                            $statusLabel = $diffDays . ' hari lagi';
                        }
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3 text-sm">{{ $index + 1 }}</td>
                        <td class="py-2 px-3 text-sm font-semibold">{{ $dokumen->kapal->nama_kapal ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $dokumen->sertifikatKapal->nama_sertifikat ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $dokumen->nomor_dokumen ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $tanggal->format('d M Y') }}</td>
                        <td class="py-2 px-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="py-2 px-3 text-center">
                            <a href="{{ route('master-dokumen-kapal-alexindo.show', $dokumen->kapal_id) }}" class="bg-blue-500 text-white py-1 px-2 rounded text-xs hover:bg-blue-600 transition-colors duration-200">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-500">Tidak ada data dokumen</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==================== SECTION: EXPIRING SOON ==================== -->
    <div id="section-expiring" class="collapsible-section hidden bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-yellow-50 border-b border-yellow-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="text-xl font-bold text-yellow-800">Dokumen Jatuh Tempo 30 Hari Ke Depan</h2>
                    <span class="ml-3 bg-yellow-600 text-white px-3 py-1 rounded-full text-sm font-semibold">{{ $expiringDokumens->count() }} Dokumen</span>
                </div>
                <div class="flex gap-2">
                    <button onclick="downloadExcel('expiring-table', 'Dokumen_Expiring_30_Hari')" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 no-print">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Excel
                    </button>
                    <button onclick="printTable('expiring-table')" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 no-print">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto" id="expiring-table">
            <table class="min-w-full bg-white divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapal</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dokumen</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Dokumen</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Berakhir</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Hari</th>
                        <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                    @forelse($expiringDokumens as $index => $dokumen)
                    @php
                        $tanggal = \Carbon\Carbon::parse($dokumen->tanggal_berakhir);
                        $today = \Carbon\Carbon::today();
                        $diffDays = $today->diffInDays($tanggal, false);
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3 text-sm">{{ $index + 1 }}</td>
                        <td class="py-2 px-3 text-sm font-semibold">{{ $dokumen->kapal->nama_kapal ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $dokumen->sertifikatKapal->nama_sertifikat ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $dokumen->nomor_dokumen ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $tanggal->format('d M Y') }}</td>
                        <td class="py-2 px-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $diffDays <= 7 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $diffDays }} hari
                            </span>
                        </td>
                        <td class="py-2 px-3 text-center">
                            <a href="{{ route('master-dokumen-kapal-alexindo.show', $dokumen->kapal_id) }}" class="bg-blue-500 text-white py-1 px-2 rounded text-xs hover:bg-blue-600 transition-colors duration-200">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-500">Tidak ada dokumen yang akan jatuh tempo dalam 30 hari</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==================== SECTION: EXPIRED ==================== -->
    <div id="section-expired" class="collapsible-section hidden bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-red-50 border-b border-red-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <h2 class="text-xl font-bold text-red-800">Dokumen Sudah Lewat Jatuh Tempo</h2>
                    <span class="ml-3 bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold">{{ $expiredDokumens->count() }} Dokumen</span>
                </div>
                <div class="flex gap-2">
                    <button onclick="downloadExcel('expired-table', 'Dokumen_Expired')" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 no-print">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Excel
                    </button>
                    <button onclick="printTable('expired-table')" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 no-print">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto" id="expired-table">
            <table class="min-w-full bg-white divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapal</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dokumen</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Dokumen</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Berakhir</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lewat</th>
                        <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                    @forelse($expiredDokumens as $index => $dokumen)
                    @php
                        $tanggal = \Carbon\Carbon::parse($dokumen->tanggal_berakhir);
                        $today = \Carbon\Carbon::today();
                        $diffDays = abs($today->diffInDays($tanggal, false));
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3 text-sm">{{ $index + 1 }}</td>
                        <td class="py-2 px-3 text-sm font-semibold">{{ $dokumen->kapal->nama_kapal ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $dokumen->sertifikatKapal->nama_sertifikat ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $dokumen->nomor_dokumen ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $tanggal->format('d M Y') }}</td>
                        <td class="py-2 px-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                {{ $diffDays }} hari
                            </span>
                        </td>
                        <td class="py-2 px-3 text-center">
                            <a href="{{ route('master-dokumen-kapal-alexindo.show', $dokumen->kapal_id) }}" class="bg-blue-500 text-white py-1 px-2 rounded text-xs hover:bg-blue-600 transition-colors duration-200">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-500">Tidak ada dokumen yang sudah expired</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==================== SECTION: NO DATE ==================== -->
    <div id="section-nodate" class="collapsible-section hidden bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h2 class="text-xl font-bold text-gray-800">Dokumen Tanpa Tanggal Berakhir</h2>
                    <span class="ml-3 bg-gray-600 text-white px-3 py-1 rounded-full text-sm font-semibold">{{ $noDateDokumens->count() }} Dokumen</span>
                </div>
                <div class="flex gap-2">
                    <button onclick="downloadExcel('nodate-table', 'Dokumen_Tanpa_Tanggal')" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 no-print">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Excel
                    </button>
                    <button onclick="printTable('nodate-table')" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 no-print">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto" id="nodate-table">
            <table class="min-w-full bg-white divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapal</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dokumen</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Dokumen</th>
                        <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                    @forelse($noDateDokumens as $index => $dokumen)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3 text-sm">{{ $index + 1 }}</td>
                        <td class="py-2 px-3 text-sm font-semibold">{{ $dokumen->kapal->nama_kapal ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $dokumen->sertifikatKapal->nama_sertifikat ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $dokumen->nomor_dokumen ?? '-' }}</td>
                        <td class="py-2 px-3 text-center">
                            <a href="{{ route('master-dokumen-kapal-alexindo.show', $dokumen->kapal_id) }}" class="bg-blue-500 text-white py-1 px-2 rounded text-xs hover:bg-blue-600 transition-colors duration-200">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-500">Tidak ada dokumen tanpa tanggal berakhir</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SheetJS Library for Excel Export -->
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>

<script>
// ======== CARD TOGGLE LOGIC ========
document.querySelectorAll('.card-toggle').forEach(function(card) {
    card.addEventListener('click', function() {
        var targetId = this.getAttribute('data-target');
        var targetSection = document.getElementById(targetId);

        // Tutup semua section lain
        document.querySelectorAll('.collapsible-section').forEach(function(section) {
            if (section.id !== targetId) {
                section.classList.add('hidden');
            }
        });

        // Reset semua hint text ke "Klik untuk lihat data"
        document.querySelectorAll('.card-toggle').forEach(function(c) {
            var hint = c.querySelector('.toggle-hint');
            if (hint) {
                hint.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg> Klik untuk lihat data';
            }
        });

        // Toggle section yang diklik
        var isHidden = targetSection.classList.contains('hidden');
        if (isHidden) {
            targetSection.classList.remove('hidden');
            // Scroll ke section
            setTimeout(function() {
                targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 50);
            // Update hint text card yang diklik
            var hint = this.querySelector('.toggle-hint');
            if (hint) {
                hint.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg> Klik untuk sembunyikan';
            }
        } else {
            targetSection.classList.add('hidden');
        }
    });
});

// ======== EXCEL DOWNLOAD ========
function downloadExcel(tableId, filename) {
    const tableContainer = document.getElementById(tableId);
    if (!tableContainer) return;
    const table = tableContainer.querySelector('table');
    if (!table) return;

    const wb = XLSX.utils.table_to_book(table);
    const today = new Date().toISOString().split('T')[0];
    XLSX.writeFile(wb, `${filename}_${today}.xlsx`);
}

// ======== PRINT ========
function printTable(tableId) {
    const tableContainer = document.getElementById(tableId);
    if (!tableContainer) return;

    const titles = {
        'total-table': 'Semua Dokumen Berjangka',
        'expiring-table': 'Dokumen Jatuh Tempo 30 Hari',
        'expired-table': 'Dokumen Expired',
        'nodate-table': 'Dokumen Tanpa Tanggal Berakhir',
    };

    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print Dashboard</title>');
    printWindow.document.write('<style>table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #ddd; padding: 8px; text-align: left; } th { background-color: #f3f4f6; }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h1>' + (titles[tableId] || 'Dashboard Dokumen') + '</h1>');
    printWindow.document.write(tableContainer.innerHTML);
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
