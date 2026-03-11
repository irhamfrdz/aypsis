@extends('layouts.app')

@section('title', 'Dashboard Jatuh Tempo Dokumen Kapal')
@section('page_title', 'Dashboard Jatuh Tempo Dokumen Kapal Alexindo')

@push('styles')
<style>
    /* ===== MOBILE CARD LIST (mengganti tabel di mobile) ===== */
    .mobile-card-list { display: none; }

    @media (max-width: 767px) {
        /* Sembunyikan tabel biasa di mobile */
        .desktop-table { display: none !important; }
        /* Tampilkan card list di mobile */
        .mobile-card-list { display: block; }

        /* Stat cards: 2 kolom di mobile */
        .stats-grid {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 0.75rem !important;
        }

        /* Kurangi padding card di mobile */
        .stat-card { padding: 0.875rem !important; }
        .stat-card .stat-number { font-size: 1.75rem !important; }
        .stat-card .stat-label { font-size: 0.7rem !important; }
        .stat-card .toggle-hint { font-size: 0.65rem !important; margin-top: 0.5rem !important; }

        /* Section header di mobile: tumpuk vertikal */
        .section-header-inner {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 0.5rem !important;
        }
        .section-header-title {
            flex-wrap: wrap !important;
            gap: 0.5rem !important;
        }
        .section-header-title h2 { font-size: 1rem !important; }
        .section-header-actions { width: 100%; justify-content: flex-end; }

        /* Mobile individual dokumen card */
        .doc-mobile-card {
            background: white;
            border-radius: 0.75rem;
            padding: 0.875rem;
            margin-bottom: 0.625rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .doc-mobile-card:last-child { margin-bottom: 0; }
        .doc-mobile-card .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.625rem;
        }
        .doc-mobile-card .doc-kapal {
            font-weight: 700;
            font-size: 0.875rem;
            color: #1f2937;
        }
        .doc-mobile-card .doc-no {
            font-size: 0.7rem;
            color: #9ca3af;
            background: #f3f4f6;
            border-radius: 9999px;
            padding: 0.1rem 0.5rem;
        }
        .doc-mobile-card .doc-rows {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }
        .doc-mobile-card .doc-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.775rem;
        }
        .doc-mobile-card .doc-row .label {
            color: #6b7280;
            font-size: 0.7rem;
            min-width: 90px;
        }
        .doc-mobile-card .doc-row .value {
            font-weight: 500;
            color: #374151;
            text-align: right;
            flex: 1;
        }
        .doc-mobile-card .doc-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 0.625rem;
            padding-top: 0.5rem;
            border-top: 1px solid #f3f4f6;
        }
        .mobile-empty {
            text-align: center;
            color: #9ca3af;
            padding: 2rem 1rem;
            font-size: 0.875rem;
        }
        .mobile-card-wrapper {
            padding: 0.75rem;
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    {{-- ===== STATS CARDS ===== --}}
    <div class="stats-grid grid grid-cols-1 md:grid-cols-4 gap-4">

        {{-- Total --}}
        <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 select-none card-toggle"
             data-target="section-total">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-label text-sm text-gray-600 font-medium">Total Dokumen (Berjangka)</p>
                    <p class="stat-number text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_dokumen'] }}</p>
                </div>
                <div class="p-2 bg-blue-100 rounded-full shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <p class="toggle-hint text-xs text-blue-500 mt-3 font-medium flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                Tap untuk lihat data
            </p>
        </div>

        {{-- Expiring Soon --}}
        <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 select-none card-toggle"
             data-target="section-expiring">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-label text-sm text-gray-600 font-medium">Jatuh Tempo 30 Hari</p>
                    <p class="stat-number text-3xl font-bold text-yellow-600 mt-2">{{ $stats['expiring_soon'] }}</p>
                </div>
                <div class="p-2 bg-yellow-100 rounded-full shrink-0">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="toggle-hint text-xs text-yellow-500 mt-3 font-medium flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                Tap untuk lihat data
            </p>
        </div>

        {{-- Expired --}}
        <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 select-none card-toggle"
             data-target="section-expired">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-label text-sm text-gray-600 font-medium">Sudah Lewat Tempo</p>
                    <p class="stat-number text-3xl font-bold text-red-600 mt-2">{{ $stats['expired'] }}</p>
                </div>
                <div class="p-2 bg-red-100 rounded-full shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <p class="toggle-hint text-xs text-red-500 mt-3 font-medium flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                Tap untuk lihat data
            </p>
        </div>

        {{-- No Date --}}
        <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-gray-500 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 select-none card-toggle"
             data-target="section-nodate">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-label text-sm text-gray-600 font-medium">Tanpa Tanggal Berakhir</p>
                    <p class="stat-number text-3xl font-bold text-gray-600 mt-2">{{ $stats['no_date'] }}</p>
                </div>
                <div class="p-2 bg-gray-100 rounded-full shrink-0">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="toggle-hint text-xs text-gray-500 mt-3 font-medium flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                Tap untuk lihat data
            </p>
        </div>
    </div>

    {{-- ===================== SECTION: TOTAL DOKUMEN ===================== --}}
    <div id="section-total" class="collapsible-section hidden bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-blue-50 border-b border-blue-200 px-4 md:px-6 py-4">
            <div class="section-header-inner flex items-center justify-between">
                <div class="section-header-title flex items-center flex-wrap gap-2">
                    <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <h2 class="text-base md:text-xl font-bold text-blue-800">Semua Dokumen Berjangka</h2>
                    <span class="bg-blue-600 text-white px-3 py-0.5 rounded-full text-xs md:text-sm font-semibold">{{ $totalDokumens->count() }} Dokumen</span>
                </div>
                <div class="section-header-actions flex gap-2 shrink-0">
                    <button onclick="downloadExcel('total-table', 'Total_Dokumen_Berjangka')" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors no-print">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="hidden sm:inline">Excel</span>
                    </button>
                    <button onclick="printTable('total-table')" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors no-print">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span class="hidden sm:inline">Print</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="desktop-table overflow-x-auto" id="total-table">
            <table class="min-w-full bg-white divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Kapal</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Dokumen</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Dokumen</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Berakhir</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                    @forelse($totalDokumens as $index => $dokumen)
                    @php
                        $tanggal = \Carbon\Carbon::parse($dokumen->tanggal_berakhir);
                        $today2 = \Carbon\Carbon::today();
                        $diffDays = $today2->diffInDays($tanggal, false);
                        if ($diffDays < 0) { $stClass = 'bg-red-100 text-red-800'; $stLabel = abs($diffDays) . ' hari lewat'; }
                        elseif ($diffDays <= 30) { $stClass = 'bg-yellow-100 text-yellow-800'; $stLabel = $diffDays . ' hari lagi'; }
                        else { $stClass = 'bg-green-100 text-green-800'; $stLabel = $diffDays . ' hari lagi'; }
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3 text-sm">{{ $index + 1 }}</td>
                        <td class="py-2 px-3 text-sm font-semibold">{{ $dokumen->kapal->nama_kapal ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $dokumen->sertifikatKapal->nama_sertifikat ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $dokumen->nomor_dokumen ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $tanggal->format('d M Y') }}</td>
                        <td class="py-2 px-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $stClass }}">{{ $stLabel }}</span></td>
                        <td class="py-2 px-3 text-center">
                            <a href="{{ route('master-dokumen-kapal-alexindo.show', $dokumen->kapal_id) }}" class="bg-blue-500 text-white py-1 px-2 rounded text-xs hover:bg-blue-600">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-8 text-center text-gray-500">Tidak ada data dokumen</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card List --}}
        <div class="mobile-card-list mobile-card-wrapper">
            @forelse($totalDokumens as $index => $dokumen)
            @php
                $tanggal = \Carbon\Carbon::parse($dokumen->tanggal_berakhir);
                $today2 = \Carbon\Carbon::today();
                $diffDays = $today2->diffInDays($tanggal, false);
                if ($diffDays < 0) { $stClass = 'bg-red-100 text-red-800'; $stLabel = abs($diffDays) . ' hari lewat'; }
                elseif ($diffDays <= 30) { $stClass = 'bg-yellow-100 text-yellow-800'; $stLabel = $diffDays . ' hari lagi'; }
                else { $stClass = 'bg-green-100 text-green-800'; $stLabel = $diffDays . ' hari lagi'; }
            @endphp
            <div class="doc-mobile-card">
                <div class="doc-header">
                    <span class="doc-kapal">{{ $dokumen->kapal->nama_kapal ?? '-' }}</span>
                    <span class="doc-no">#{{ $index + 1 }}</span>
                </div>
                <div class="doc-rows">
                    <div class="doc-row">
                        <span class="label">Dokumen</span>
                        <span class="value">{{ $dokumen->sertifikatKapal->nama_sertifikat ?? '-' }}</span>
                    </div>
                    <div class="doc-row">
                        <span class="label">Nomor</span>
                        <span class="value">{{ $dokumen->nomor_dokumen ?? '-' }}</span>
                    </div>
                    <div class="doc-row">
                        <span class="label">Tgl Berakhir</span>
                        <span class="value">{{ $tanggal->format('d M Y') }}</span>
                    </div>
                    <div class="doc-row">
                        <span class="label">Status</span>
                        <span class="value"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold {{ $stClass }}">{{ $stLabel }}</span></span>
                    </div>
                </div>
                <div class="doc-footer">
                    <a href="{{ route('master-dokumen-kapal-alexindo.show', $dokumen->kapal_id) }}" class="bg-blue-500 text-white py-1.5 px-4 rounded-lg text-xs font-medium hover:bg-blue-600 active:bg-blue-700 transition-colors">Detail</a>
                </div>
            </div>
            @empty
            <div class="mobile-empty">Tidak ada data dokumen</div>
            @endforelse
        </div>
    </div>

    {{-- ===================== SECTION: EXPIRING SOON ===================== --}}
    <div id="section-expiring" class="collapsible-section hidden bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-yellow-50 border-b border-yellow-200 px-4 md:px-6 py-4">
            <div class="section-header-inner flex items-center justify-between">
                <div class="section-header-title flex items-center flex-wrap gap-2">
                    <svg class="w-5 h-5 text-yellow-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h2 class="text-base md:text-xl font-bold text-yellow-800">Jatuh Tempo 30 Hari</h2>
                    <span class="bg-yellow-600 text-white px-3 py-0.5 rounded-full text-xs md:text-sm font-semibold">{{ $expiringDokumens->count() }} Dokumen</span>
                </div>
                <div class="section-header-actions flex gap-2 shrink-0">
                    <button onclick="downloadExcel('expiring-table', 'Dokumen_Expiring_30_Hari')" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors no-print">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="hidden sm:inline">Excel</span>
                    </button>
                    <button onclick="printTable('expiring-table')" class="inline-flex items-center px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-medium rounded-lg transition-colors no-print">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span class="hidden sm:inline">Print</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="desktop-table overflow-x-auto" id="expiring-table">
            <table class="min-w-full bg-white divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Kapal</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Dokumen</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Dokumen</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Berakhir</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Sisa Hari</th>
                        <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                    @forelse($expiringDokumens as $index => $dokumen)
                    @php
                        $tanggal = \Carbon\Carbon::parse($dokumen->tanggal_berakhir);
                        $today2 = \Carbon\Carbon::today();
                        $diffDays = $today2->diffInDays($tanggal, false);
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3 text-sm">{{ $index + 1 }}</td>
                        <td class="py-2 px-3 text-sm font-semibold">{{ $dokumen->kapal->nama_kapal ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $dokumen->sertifikatKapal->nama_sertifikat ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $dokumen->nomor_dokumen ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $tanggal->format('d M Y') }}</td>
                        <td class="py-2 px-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $diffDays <= 7 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $diffDays }} hari</span>
                        </td>
                        <td class="py-2 px-3 text-center">
                            <a href="{{ route('master-dokumen-kapal-alexindo.show', $dokumen->kapal_id) }}" class="bg-blue-500 text-white py-1 px-2 rounded text-xs hover:bg-blue-600">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-8 text-center text-gray-500">Tidak ada dokumen yang akan jatuh tempo dalam 30 hari</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card List --}}
        <div class="mobile-card-list mobile-card-wrapper">
            @forelse($expiringDokumens as $index => $dokumen)
            @php
                $tanggal = \Carbon\Carbon::parse($dokumen->tanggal_berakhir);
                $today2 = \Carbon\Carbon::today();
                $diffDays = $today2->diffInDays($tanggal, false);
                $sisaClass = $diffDays <= 7 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800';
            @endphp
            <div class="doc-mobile-card border-l-4 border-yellow-400">
                <div class="doc-header">
                    <span class="doc-kapal">{{ $dokumen->kapal->nama_kapal ?? '-' }}</span>
                    <span class="doc-no">#{{ $index + 1 }}</span>
                </div>
                <div class="doc-rows">
                    <div class="doc-row">
                        <span class="label">Dokumen</span>
                        <span class="value">{{ $dokumen->sertifikatKapal->nama_sertifikat ?? '-' }}</span>
                    </div>
                    <div class="doc-row">
                        <span class="label">Nomor</span>
                        <span class="value">{{ $dokumen->nomor_dokumen ?? '-' }}</span>
                    </div>
                    <div class="doc-row">
                        <span class="label">Tgl Berakhir</span>
                        <span class="value">{{ $tanggal->format('d M Y') }}</span>
                    </div>
                    <div class="doc-row">
                        <span class="label">Sisa Hari</span>
                        <span class="value"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold {{ $sisaClass }}">{{ $diffDays }} hari</span></span>
                    </div>
                </div>
                <div class="doc-footer">
                    <a href="{{ route('master-dokumen-kapal-alexindo.show', $dokumen->kapal_id) }}" class="bg-blue-500 text-white py-1.5 px-4 rounded-lg text-xs font-medium hover:bg-blue-600 active:bg-blue-700 transition-colors">Detail</a>
                </div>
            </div>
            @empty
            <div class="mobile-empty">Tidak ada dokumen yang akan jatuh tempo dalam 30 hari</div>
            @endforelse
        </div>
    </div>

    {{-- ===================== SECTION: EXPIRED ===================== --}}
    <div id="section-expired" class="collapsible-section hidden bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-red-50 border-b border-red-200 px-4 md:px-6 py-4">
            <div class="section-header-inner flex items-center justify-between">
                <div class="section-header-title flex items-center flex-wrap gap-2">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <h2 class="text-base md:text-xl font-bold text-red-800">Sudah Lewat Jatuh Tempo</h2>
                    <span class="bg-red-600 text-white px-3 py-0.5 rounded-full text-xs md:text-sm font-semibold">{{ $expiredDokumens->count() }} Dokumen</span>
                </div>
                <div class="section-header-actions flex gap-2 shrink-0">
                    <button onclick="downloadExcel('expired-table', 'Dokumen_Expired')" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors no-print">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="hidden sm:inline">Excel</span>
                    </button>
                    <button onclick="printTable('expired-table')" class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors no-print">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span class="hidden sm:inline">Print</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="desktop-table overflow-x-auto" id="expired-table">
            <table class="min-w-full bg-white divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Kapal</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Dokumen</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Dokumen</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Berakhir</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Lewat</th>
                        <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                    @forelse($expiredDokumens as $index => $dokumen)
                    @php
                        $tanggal = \Carbon\Carbon::parse($dokumen->tanggal_berakhir);
                        $today2 = \Carbon\Carbon::today();
                        $diffDays = abs($today2->diffInDays($tanggal, false));
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3 text-sm">{{ $index + 1 }}</td>
                        <td class="py-2 px-3 text-sm font-semibold">{{ $dokumen->kapal->nama_kapal ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $dokumen->sertifikatKapal->nama_sertifikat ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $dokumen->nomor_dokumen ?? '-' }}</td>
                        <td class="py-2 px-3 text-sm">{{ $tanggal->format('d M Y') }}</td>
                        <td class="py-2 px-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">{{ $diffDays }} hari</span>
                        </td>
                        <td class="py-2 px-3 text-center">
                            <a href="{{ route('master-dokumen-kapal-alexindo.show', $dokumen->kapal_id) }}" class="bg-blue-500 text-white py-1 px-2 rounded text-xs hover:bg-blue-600">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-8 text-center text-gray-500">Tidak ada dokumen yang sudah expired</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card List --}}
        <div class="mobile-card-list mobile-card-wrapper">
            @forelse($expiredDokumens as $index => $dokumen)
            @php
                $tanggal = \Carbon\Carbon::parse($dokumen->tanggal_berakhir);
                $today2 = \Carbon\Carbon::today();
                $diffDays = abs($today2->diffInDays($tanggal, false));
            @endphp
            <div class="doc-mobile-card border-l-4 border-red-400">
                <div class="doc-header">
                    <span class="doc-kapal">{{ $dokumen->kapal->nama_kapal ?? '-' }}</span>
                    <span class="doc-no">#{{ $index + 1 }}</span>
                </div>
                <div class="doc-rows">
                    <div class="doc-row">
                        <span class="label">Dokumen</span>
                        <span class="value">{{ $dokumen->sertifikatKapal->nama_sertifikat ?? '-' }}</span>
                    </div>
                    <div class="doc-row">
                        <span class="label">Nomor</span>
                        <span class="value">{{ $dokumen->nomor_dokumen ?? '-' }}</span>
                    </div>
                    <div class="doc-row">
                        <span class="label">Tgl Berakhir</span>
                        <span class="value">{{ $tanggal->format('d M Y') }}</span>
                    </div>
                    <div class="doc-row">
                        <span class="label">Lewat</span>
                        <span class="value"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">{{ $diffDays }} hari</span></span>
                    </div>
                </div>
                <div class="doc-footer">
                    <a href="{{ route('master-dokumen-kapal-alexindo.show', $dokumen->kapal_id) }}" class="bg-blue-500 text-white py-1.5 px-4 rounded-lg text-xs font-medium hover:bg-blue-600 active:bg-blue-700 transition-colors">Detail</a>
                </div>
            </div>
            @empty
            <div class="mobile-empty">Tidak ada dokumen yang sudah expired</div>
            @endforelse
        </div>
    </div>

    {{-- ===================== SECTION: NO DATE ===================== --}}
    <div id="section-nodate" class="collapsible-section hidden bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 px-4 md:px-6 py-4">
            <div class="section-header-inner flex items-center justify-between">
                <div class="section-header-title flex items-center flex-wrap gap-2">
                    <svg class="w-5 h-5 text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h2 class="text-base md:text-xl font-bold text-gray-800">Tanpa Tanggal Berakhir</h2>
                    <span class="bg-gray-600 text-white px-3 py-0.5 rounded-full text-xs md:text-sm font-semibold">{{ $noDateDokumens->count() }} Dokumen</span>
                </div>
                <div class="section-header-actions flex gap-2 shrink-0">
                    <button onclick="downloadExcel('nodate-table', 'Dokumen_Tanpa_Tanggal')" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors no-print">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="hidden sm:inline">Excel</span>
                    </button>
                    <button onclick="printTable('nodate-table')" class="inline-flex items-center px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors no-print">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span class="hidden sm:inline">Print</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="desktop-table overflow-x-auto" id="nodate-table">
            <table class="min-w-full bg-white divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Kapal</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Dokumen</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Dokumen</th>
                        <th class="py-2 px-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
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
                            <a href="{{ route('master-dokumen-kapal-alexindo.show', $dokumen->kapal_id) }}" class="bg-blue-500 text-white py-1 px-2 rounded text-xs hover:bg-blue-600">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-8 text-center text-gray-500">Tidak ada dokumen tanpa tanggal berakhir</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card List --}}
        <div class="mobile-card-list mobile-card-wrapper">
            @forelse($noDateDokumens as $index => $dokumen)
            <div class="doc-mobile-card border-l-4 border-gray-400">
                <div class="doc-header">
                    <span class="doc-kapal">{{ $dokumen->kapal->nama_kapal ?? '-' }}</span>
                    <span class="doc-no">#{{ $index + 1 }}</span>
                </div>
                <div class="doc-rows">
                    <div class="doc-row">
                        <span class="label">Dokumen</span>
                        <span class="value">{{ $dokumen->sertifikatKapal->nama_sertifikat ?? '-' }}</span>
                    </div>
                    <div class="doc-row">
                        <span class="label">Nomor</span>
                        <span class="value">{{ $dokumen->nomor_dokumen ?? '-' }}</span>
                    </div>
                </div>
                <div class="doc-footer">
                    <a href="{{ route('master-dokumen-kapal-alexindo.show', $dokumen->kapal_id) }}" class="bg-blue-500 text-white py-1.5 px-4 rounded-lg text-xs font-medium hover:bg-blue-600 active:bg-blue-700 transition-colors">Detail</a>
                </div>
            </div>
            @empty
            <div class="mobile-empty">Tidak ada dokumen tanpa tanggal berakhir</div>
            @endforelse
        </div>
    </div>

</div>

<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<script>
// ======== CARD TOGGLE ========
document.querySelectorAll('.card-toggle').forEach(function(card) {
    card.addEventListener('click', function() {
        var targetId = this.getAttribute('data-target');
        var targetSection = document.getElementById(targetId);

        document.querySelectorAll('.collapsible-section').forEach(function(section) {
            if (section.id !== targetId) section.classList.add('hidden');
        });

        document.querySelectorAll('.card-toggle').forEach(function(c) {
            var hint = c.querySelector('.toggle-hint');
            if (hint) hint.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg> Tap untuk lihat data';
        });

        var isHidden = targetSection.classList.contains('hidden');
        if (isHidden) {
            targetSection.classList.remove('hidden');
            setTimeout(function() {
                targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 50);
            var hint = this.querySelector('.toggle-hint');
            if (hint) hint.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg> Tap untuk sembunyikan';
        } else {
            targetSection.classList.add('hidden');
        }
    });
});

// ======== EXCEL DOWNLOAD ========
function downloadExcel(tableId, filename) {
    // Cari tabel di desktop-table atau mobile-card-list (gunakan desktop table)
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
    printWindow.document.write('<html><head><title>Print</title>');
    printWindow.document.write('<style>table{width:100%;border-collapse:collapse}th,td{border:1px solid #ddd;padding:8px;text-align:left}th{background:#f3f4f6}</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h2>' + (titles[tableId] || 'Dashboard Dokumen') + '</h2>');
    printWindow.document.write(tableContainer.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => { printWindow.print(); printWindow.close(); }, 250);
}
</script>
@endsection
