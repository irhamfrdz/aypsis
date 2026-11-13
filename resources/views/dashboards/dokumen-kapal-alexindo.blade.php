@extends('layouts.app')

@section('title', 'Dashboard Jatuh Tempo Dokumen Kapal')
@section('page_title', 'Dashboard Jatuh Tempo Dokumen Kapal Alexindo')

@push('styles')
<style>
    body:has(.ship-document-dashboard) { font-family: Arial, sans-serif; font-size: 12px; }
    body:has(.ship-document-dashboard) #page-content { background: #F5F7FA; }
    body:has(.ship-document-dashboard) > header h1 { font-family: Arial, sans-serif; font-size: 20px; }
    body:has(.ship-document-dashboard) #sidebar :is(a, input, .menu-text),
    body:has(.ship-document-dashboard) > header :is(button, a, span:not([class*="fa-"])) {
        font-family: Arial, sans-serif; font-size: 12px;
    }
    .ship-document-dashboard {
        --doc-navy: #1E3A5F;
        --doc-bg: #F5F7FA;
        --doc-line: #e1e6ed;
        font: 12px/1.5 Arial, sans-serif;
        color: #344256;
        min-width: 0;
        background: var(--doc-bg);
    }
    .ship-document-dashboard :is(p, span, a, button, input, select, label, table, th, td) {
        font-family: Arial, sans-serif; font-size: 12px;
    }
    .ship-document-dashboard h2 { font: 600 16px/1.4 Arial, sans-serif; color: var(--doc-navy); }
    .ship-document-dashboard > * + * { margin-top: 20px; }
    .ship-document-dashboard .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
    .ship-document-dashboard :is(.stat-card, .collapsible-section, .doc-mobile-card) {
        background: #fff; border: 1px solid var(--doc-line); border-radius: 8px;
        box-shadow: 0 2px 6px rgb(30 58 95 / 5%);
    }
    .ship-document-dashboard .stat-card {
        padding: 16px; border-top: 3px solid #8799b0; min-width: 0;
        display: flex; flex-direction: column; transition: none; transform: none;
    }
    .ship-document-dashboard .stat-card[data-target="section-total"] { border-top-color: var(--doc-navy); }
    .ship-document-dashboard .stat-card[data-target="section-expiring"] { border-top-color: #bc9345; }
    .ship-document-dashboard .stat-card[data-target="section-expired"] { border-top-color: #bc6767; }
    .ship-document-dashboard .stat-card[data-target="section-nodate"] { border-top-color: #97a1af; }
    .ship-document-dashboard .stat-card:hover { background: #fafbfd; border-color: #aab9cc; box-shadow: 0 2px 6px rgb(30 58 95 / 5%); transform: none; }
    .ship-document-dashboard .stat-card[aria-expanded="true"] {
        background: #f3f6fa; outline: 1px solid var(--doc-navy); outline-offset: -1px;
    }
    .ship-document-dashboard :is(.stat-card, a, button, input, select):focus-visible {
        outline: 2px solid #6b86a8; outline-offset: 2px;
    }
    .ship-document-dashboard .stat-card > .flex { gap: 10px; align-items: flex-start; }
    .ship-document-dashboard .stat-card > .flex > div:first-child { flex: 1; min-width: 0; }
    .ship-document-dashboard .stat-label { color: #647184; min-height: 36px; line-height: 18px; }
    .ship-document-dashboard .stat-number { color: var(--doc-navy); font-size: 20px; font-weight: 700; line-height: 1.3; margin: 6px 0 10px; font-variant-numeric: tabular-nums; }
    .ship-document-dashboard [data-target="section-expiring"] .stat-number { color: #966712; }
    .ship-document-dashboard [data-target="section-expired"] .stat-number { color: #b34242; }
    .ship-document-dashboard [data-target="section-nodate"] .stat-number { color: #657184; }
    .ship-document-dashboard .stat-card .p-2 { padding: 7px; background: #f2f5f8; border-radius: 6px; }
    .ship-document-dashboard .stat-card .p-2 svg { width: 18px; height: 18px; color: #71839b; }
    .ship-document-dashboard .toggle-hint { color: #7a8797; font-weight: 400; margin-top: auto; padding-top: 8px; border-top: 1px solid #edf0f4; }
    .ship-document-dashboard .stat-card[aria-expanded="true"] .toggle-hint { color: var(--doc-navy); }
    .ship-document-dashboard .collapsible-section { scroll-margin-top: 16px; }
    .ship-document-dashboard .collapsible-section > :first-child { background: #fff; border-color: var(--doc-line); padding: 16px; }
    .ship-document-dashboard .section-header-inner { flex-wrap: wrap; gap: 12px 20px; }
    .ship-document-dashboard .section-header-title { min-width: 0; flex: 1 1 340px; gap: 8px; }
    .ship-document-dashboard .section-header-title > svg { width: 18px; height: 18px; color: #71839b; }
    .ship-document-dashboard .section-header-title > span { background: #f1f4f8; color: var(--doc-navy); border: 1px solid #e2e7ee; border-radius: 5px; padding: 2px 8px; white-space: nowrap; }
    .ship-document-dashboard .section-header-actions { gap: 8px; }
    .ship-document-dashboard .section-header-actions button {
        background: var(--doc-navy); color: #fff; min-height: 32px; padding: 6px 12px;
        border: 1px solid var(--doc-navy); border-radius: 5px; transition: none;
    }
    .ship-document-dashboard .section-header-actions button + button { background: #fff; color: var(--doc-navy); border-color: #cdd7e4; }
    .ship-document-dashboard .section-header-actions button:hover { background: #2b4c75; color: #fff; }
    .ship-document-dashboard .section-header-actions button + button:hover { background: #edf2f8; color: var(--doc-navy); }
    .ship-document-dashboard .section-header-actions svg { width: 14px; height: 14px; margin-right: 6px; }
    .ship-document-dashboard table { width: 100%; min-width: 900px; border-collapse: collapse; table-layout: fixed; }
    .ship-document-dashboard th { background: var(--doc-bg); color: #647184; font-weight: 600; padding: 10px 12px; border-color: var(--doc-line); letter-spacing: .01em; }
    .ship-document-dashboard td { padding: 9px 12px; border-color: var(--doc-line); vertical-align: middle; overflow-wrap: anywhere; }
    .ship-document-dashboard th:first-child { width: 44px; }
    .ship-document-dashboard th:nth-child(2) { width: 14%; }
    .ship-document-dashboard th:nth-child(3) { width: 34%; }
    .ship-document-dashboard th:nth-child(4) { width: 14%; }
    .ship-document-dashboard th:nth-child(5) { width: 130px; }
    .ship-document-dashboard th:nth-child(6) { width: 130px; }
    .ship-document-dashboard th:last-child { width: 78px; }
    .ship-document-dashboard td:first-child { color: #7a8594; font-variant-numeric: tabular-nums; }
    .ship-document-dashboard td:nth-child(2) { font-weight: 700; color: var(--doc-navy); }
    .ship-document-dashboard td:nth-child(3) { line-height: 1.6; }
    .ship-document-dashboard td:nth-child(5) { white-space: nowrap; font-variant-numeric: tabular-nums; }
    .ship-document-dashboard tbody tr:hover { background: #f8fafc; }
    .ship-document-dashboard :is(td, .doc-row) .inline-flex { padding: 3px 7px; border-radius: 4px; font-weight: 600; }
    .ship-document-dashboard :is(td, .doc-row) .bg-red-100 { background: #fbeded; color: #ad3d3d; }
    .ship-document-dashboard :is(td, .doc-row) .bg-yellow-100 { background: #fcf5e6; color: #8b651f; }
    .ship-document-dashboard :is(td, .doc-row) .bg-green-100 { background: #edf6f1; color: #34704f; }
    .ship-document-dashboard .document-status-empty { background: #f0f2f5; color: #697586; }
    .ship-document-dashboard :is(td, .doc-footer) a {
        display: inline-flex; justify-content: center; align-items: center; min-height: 28px; padding: 4px 10px;
        border: 1px solid #cdd7e4; border-radius: 5px; background: #fff; color: var(--doc-navy); transition: none;
    }
    .ship-document-dashboard :is(td, .doc-footer) a:hover { background: #edf2f8; border-color: #91a5bd; }
    .ship-document-dashboard .mobile-card-list { display: none; }
    @media (max-width: 1100px) {
        .ship-document-dashboard .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 767px) {
        .ship-document-dashboard .desktop-table { display: none; }
        .ship-document-dashboard .mobile-card-list { display: block; }
        .ship-document-dashboard .section-header-title { flex-basis: 100%; }
        .ship-document-dashboard .section-header-actions { margin-left: auto; }
        .ship-document-dashboard .mobile-card-wrapper { padding: 12px; background: var(--doc-bg); }
        .ship-document-dashboard .doc-mobile-card { padding: 14px; margin-bottom: 10px; border-left-width: 1px; }
        .ship-document-dashboard .doc-mobile-card:last-child { margin-bottom: 0; }
        .ship-document-dashboard .doc-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; padding-bottom: 10px; margin-bottom: 10px; border-bottom: 1px solid #edf0f4; }
        .ship-document-dashboard .doc-kapal { color: var(--doc-navy); font-weight: 700; overflow-wrap: anywhere; }
        .ship-document-dashboard .doc-no { background: #f1f4f8; color: #788596; border-radius: 4px; padding: 1px 6px; flex-shrink: 0; }
        .ship-document-dashboard .doc-rows { display: flex; flex-direction: column; gap: 8px; }
        .ship-document-dashboard .doc-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
        .ship-document-dashboard .doc-row .label { color: #748091; flex: 0 0 82px; }
        .ship-document-dashboard .doc-row .value { flex: 1; min-width: 0; text-align: right; overflow-wrap: anywhere; }
        .ship-document-dashboard .document-empty-date { margin-top: 8px; margin-bottom: 8px; }
        .ship-document-dashboard .doc-footer { display: flex; justify-content: flex-end; border-top: 1px solid #edf0f4; margin-top: 12px; padding-top: 10px; }
        .ship-document-dashboard .mobile-empty { text-align: center; color: #748091; padding: 24px 12px; }
    }
    @media (max-width: 480px) {
        .ship-document-dashboard > * + * { margin-top: 16px; }
        .ship-document-dashboard .stats-grid { gap: 10px; }
        .ship-document-dashboard .stat-card { padding: 12px; }
        .ship-document-dashboard .stat-label { min-height: 54px; }
        .ship-document-dashboard .stat-card > .flex { gap: 6px; }
        .ship-document-dashboard .stat-card .p-2 { padding: 4px; }
        .ship-document-dashboard .stat-card .p-2 svg { width: 14px; height: 14px; }
        .ship-document-dashboard .toggle-hint { gap: 4px; align-items: flex-start; }
        .ship-document-dashboard .toggle-hint svg { flex-shrink: 0; margin-top: 3px; }
    }
    @media (max-width: 350px) {
        .ship-document-dashboard .stats-grid { grid-template-columns: 1fr; }
        .ship-document-dashboard .stat-label { min-height: 0; }
    }
    @media (prefers-reduced-motion: reduce) {
        .ship-document-dashboard { scroll-behavior: auto; }
    }
</style>
@endpush

@section('content')
<div class="ship-document-dashboard space-y-6">

    {{-- ===== STATS CARDS ===== --}}
    <div class="stats-grid grid grid-cols-1 md:grid-cols-4 gap-4">

        {{-- Total --}}
        <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 select-none card-toggle"
             data-target="section-total" role="button" tabindex="0" aria-controls="section-total" aria-expanded="false">
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
             data-target="section-expiring" role="button" tabindex="0" aria-controls="section-expiring" aria-expanded="false">
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
             data-target="section-expired" role="button" tabindex="0" aria-controls="section-expired" aria-expanded="false">
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
             data-target="section-nodate" role="button" tabindex="0" aria-controls="section-nodate" aria-expanded="false">
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
                        <span>Excel</span>
                    </button>
                    <button onclick="printTable('total-table')" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors no-print">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Print</span>
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
                        <td class="py-2 px-3 text-sm">{{ $tanggal->format('d/M/Y') }}</td>
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
                        <span class="value">{{ $tanggal->format('d/M/Y') }}</span>
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
                        <span>Excel</span>
                    </button>
                    <button onclick="printTable('expiring-table')" class="inline-flex items-center px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-medium rounded-lg transition-colors no-print">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Print</span>
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
                        <td class="py-2 px-3 text-sm">{{ $tanggal->format('d/M/Y') }}</td>
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
                        <span class="value">{{ $tanggal->format('d/M/Y') }}</span>
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
                        <span>Excel</span>
                    </button>
                    <button onclick="printTable('expired-table')" class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors no-print">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Print</span>
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
                        <td class="py-2 px-3 text-sm">{{ $tanggal->format('d/M/Y') }}</td>
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
                        <span class="value">{{ $tanggal->format('d/M/Y') }}</span>
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
                        <span>Excel</span>
                    </button>
                    <button onclick="printTable('nodate-table')" class="inline-flex items-center px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors no-print">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Print</span>
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
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Berakhir</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
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
                        <td class="py-2 px-3 text-sm">-</td>
                        <td class="py-2 px-3"><span class="document-status-empty inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold">Tanpa tanggal</span></td>
                        <td class="py-2 px-3 text-center">
                            <a href="{{ route('master-dokumen-kapal-alexindo.show', $dokumen->kapal_id) }}" class="bg-blue-500 text-white py-1 px-2 rounded text-xs hover:bg-blue-600">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-8 text-center text-gray-500">Tidak ada dokumen tanpa tanggal berakhir</td></tr>
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
                <div class="doc-row document-empty-date">
                    <span class="label">Tgl Berakhir</span>
                    <span class="value">-</span>
                </div>
                <div class="doc-row">
                    <span class="label">Status</span>
                    <span class="value"><span class="document-status-empty inline-flex px-2 py-0.5 rounded-full text-xs font-bold">Tanpa tanggal</span></span>
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
document.querySelectorAll('.ship-document-dashboard .card-toggle').forEach(function(card) {
    card.addEventListener('keydown', function(event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            this.click();
        }
    });
    card.addEventListener('click', function() {
        var targetId = this.getAttribute('data-target');
        var targetSection = document.getElementById(targetId);

        document.querySelectorAll('.ship-document-dashboard .collapsible-section').forEach(function(section) {
            if (section.id !== targetId) section.classList.add('hidden');
        });

        document.querySelectorAll('.ship-document-dashboard .card-toggle').forEach(function(c) {
            c.setAttribute('aria-expanded', 'false');
            var hint = c.querySelector('.toggle-hint');
            if (hint) hint.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg> Tap untuk lihat data';
        });

        var isHidden = targetSection.classList.contains('hidden');
        if (isHidden) {
            targetSection.classList.remove('hidden');
            this.setAttribute('aria-expanded', 'true');
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
    printWindow.document.write('<style>body{font:12px/1.5 Arial,sans-serif;color:#344256;padding:16px}h2{font:600 18px/1.4 Arial,sans-serif;color:#1E3A5F}table{width:100%;border-collapse:collapse;font-size:12px}th,td{border-bottom:1px solid #e1e6ed;padding:8px;text-align:left;font-size:12px}th{background:#F5F7FA;color:#647184}td:nth-child(2){font-weight:700}a{color:#1E3A5F;text-decoration:none}.inline-flex{display:inline-block;border-radius:4px;padding:2px 6px;font-size:12px}.bg-red-100{background:#fbeded;color:#ad3d3d}.bg-yellow-100{background:#fcf5e6;color:#8b651f}.bg-green-100{background:#edf6f1;color:#34704f}.document-status-empty{background:#f0f2f5;color:#697586}thead{display:table-header-group}tr{break-inside:avoid}</style>');
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
