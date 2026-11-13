@extends('layouts.app')

@section('title', 'Asuransi Tanda Terima')


@push('styles')
<style>
    body:has(.insurance-receipts) { font-family: Arial, sans-serif; font-size: 12px; }
    body:has(.insurance-receipts) #page-content { background: #F5F7FA; }
    body:has(.insurance-receipts) > header h1 { font-family: Arial, sans-serif; font-size: 20px; }
    body:has(.insurance-receipts) #sidebar :is(a, input, .menu-text),
    body:has(.insurance-receipts) > header :is(button, a, span:not([class*="fa-"])) { font-family: Arial, sans-serif; font-size: 12px; }
    .insurance-receipts, .insurance-receipts-modal { --insurance-navy: #1E3A5F; --insurance-line: #e1e6ed; font: 12px/1.5 Arial, sans-serif; color: #344256; min-width: 0; }
    :is(.insurance-receipts, .insurance-receipts-modal) :is(p, span, a, button, input, select, label, table, th, td, div) { font-family: Arial, sans-serif; font-size: 12px; }
    .insurance-receipts h1 { font: 600 20px/1.4 Arial, sans-serif; color: var(--insurance-navy); }
    .insurance-receipts-modal h3 { font: 600 16px/1.4 Arial, sans-serif; color: var(--insurance-navy); }
    .insurance-receipts .insurance-receipts-panel { padding: 18px; background: #fff; border: 1px solid var(--insurance-line); border-radius: 8px; box-shadow: 0 2px 6px rgb(30 58 95 / 4%); }
    .insurance-receipts .insurance-receipts-heading { flex-wrap: wrap; gap: 12px 20px; margin-bottom: 18px; }
    .insurance-receipts .insurance-receipts-heading p { margin-top: 4px; color: #748091; }
    .insurance-receipts #selectedCount { color: var(--insurance-navy); font-weight: 600; }
    .insurance-receipts #viewSelectedBtn { color: #547293; }
    .insurance-receipts #clearSelectionBtn { color: #ad5b5b; }
    .insurance-receipts .insurance-receipts-actions { flex-wrap: wrap; gap: 8px; }
    .insurance-receipts .insurance-receipts-actions > :not([hidden]) ~ :not([hidden]) { margin-left: 0; }
    .insurance-receipts .insurance-receipts-actions :is(a, button) { min-height: 34px; padding: 7px 12px; border: 1px solid #cdd7e4; border-radius: 5px; background: #fff; color: var(--insurance-navy); font-weight: 500; transition: none; white-space: nowrap; }
    .insurance-receipts .insurance-receipts-actions > a { background: var(--insurance-navy); border-color: var(--insurance-navy); color: #fff; }
    .insurance-receipts .insurance-receipts-actions :is(a, button):hover { background: #edf2f8; }
    .insurance-receipts .insurance-receipts-actions > a:hover { background: #2b4c75; }
    .insurance-receipts .insurance-receipts-actions svg { width: 14px; height: 14px; margin-right: 6px; }
    .insurance-receipts .insurance-receipts-filters { padding: 12px; background: #F5F7FA; border: 1px solid #e7ebf1; border-radius: 6px; margin-bottom: 16px; }
    .insurance-receipts .insurance-receipts-filters form { gap: 12px; }
    .insurance-receipts .insurance-receipts-filters .min-w-\[300px\] { min-width: 220px; }
    .insurance-receipts .insurance-receipts-filters .w-44 { width: 158px; }
    .insurance-receipts .insurance-receipts-filters label { font-weight: 500; color: #647184; text-transform: none; }
    :is(.insurance-receipts, .insurance-receipts-modal) input:not([type="hidden"]):not([type="checkbox"]) { min-height: 34px; padding: 7px 10px; border: 1px solid #d5dde7; border-radius: 5px; background: #fff; box-shadow: none; }
    .insurance-receipts input[type="checkbox"] { width: 14px; height: 14px; border-color: #bac5d3; border-radius: 3px; color: var(--insurance-navy); }
    .insurance-receipts .insurance-receipts-filters button { min-height: 34px; border: 1px solid var(--insurance-navy); border-radius: 5px; padding: 7px 14px; background: var(--insurance-navy); transition: none; }
    .insurance-receipts .insurance-receipts-filters a { display: inline-flex; align-items: center; min-height: 34px; border: 1px solid #cdd7e4; border-radius: 5px; padding: 7px 14px; background: #fff; color: var(--insurance-navy); transition: none; }
    .insurance-receipts .insurance-receipts-filters a:hover { background: #edf2f8; }
    :is(.insurance-receipts, .insurance-receipts-modal) :is(a, button, input):focus-visible { outline: 2px solid #718cac; outline-offset: 2px; box-shadow: none; }
    .insurance-receipts table { width: 100%; min-width: 1440px; border-collapse: collapse; }
    .insurance-receipts thead th { padding: 10px 12px; background: #F5F7FA; color: #647184; font-weight: 600; letter-spacing: .01em; border-bottom: 1px solid var(--insurance-line); }
    .insurance-receipts thead th:first-child { width: 40px; }
    .insurance-receipts td { padding: 10px 12px; border-color: var(--insurance-line); vertical-align: middle; }
    .insurance-receipts tbody tr { transition: none; }
    .insurance-receipts tbody tr:hover { background: #f8fafc; }
    .insurance-receipts tbody tr.bg-blue-50 { background: #f0f4fa; }
    .insurance-receipts tbody tr.bg-blue-50:hover { background: #e9f0f8; }
    .insurance-receipts td:nth-child(3) > span:first-child { color: var(--insurance-navy); font-weight: 600; }
    .insurance-receipts td:nth-child(3) > span:nth-child(2) { color: #8490a1; }
    .insurance-receipts td:nth-child(4) { color: #536379; font-variant-numeric: tabular-nums; }
    .insurance-receipts td:nth-child(5) { min-width: 240px; max-width: 360px; white-space: normal; overflow: visible; text-overflow: clip; overflow-wrap: anywhere; line-height: 1.6; }
    .insurance-receipts td:nth-child(6) { font-variant-numeric: tabular-nums; }
    .insurance-receipts td:nth-child(8) { min-width: 230px; max-width: 320px; white-space: normal; overflow-wrap: anywhere; line-height: 1.6; }
    :is(.insurance-receipts td:nth-child(2), .insurance-receipts-modal #selectedItemsContainer) :is(.bg-blue-100, .bg-purple-100, .bg-teal-100) { display: inline-flex; background: #edf2f8; color: #456386; font-weight: 500; padding: 3px 7px; border-radius: 4px; }
    .insurance-receipts td .bg-green-100 { background: #edf6f1; color: #34704f; border-radius: 4px; }
    .insurance-receipts td .bg-yellow-100 { background: #fcf5e6; color: #8b651f; border-radius: 4px; }
    .insurance-receipts td:last-child .flex { gap: 6px; }
    .insurance-receipts td:last-child .flex > a { display: inline-flex; justify-content: center; align-items: center; min-height: 28px; padding: 5px 8px; border: 1px solid #cdd7e4; border-radius: 5px; background: #fff; color: var(--insurance-navy); transition: none; }
    .insurance-receipts td:last-child .flex > a + a { margin-left: 0; }
    .insurance-receipts td:last-child .flex > a:hover { background: #edf2f8; }
    .insurance-receipts td:last-child svg { width: 14px; height: 14px; }
    .insurance-receipts td[colspan] { padding: 28px 12px; }
    .insurance-receipts td[colspan] svg { width: 30px; height: 30px; margin-bottom: 10px; }
    .insurance-receipts .insurance-receipts-pagination { padding-top: 12px; margin-top: 16px; border-top: 1px solid #edf0f4; }
    .insurance-receipts .insurance-receipts-pagination svg { width: 16px; height: 16px; }
    .insurance-receipts .insurance-receipts-pagination [aria-current="page"] > span { background: #edf2f8; color: var(--insurance-navy); border-color: #b6c5d8; }
    .insurance-receipts .overflow-x-auto::-webkit-scrollbar { height: 5px; }
    .insurance-receipts .overflow-x-auto::-webkit-scrollbar-thumb { background: #d5dde7; border-radius: 4px; }
    .insurance-receipts-modal > div > .inline-block { border: 1px solid var(--insurance-line); border-radius: 8px; box-shadow: 0 8px 30px rgb(30 58 95 / 12%); }
    .insurance-receipts-modal button { border-radius: 5px; font-weight: 500; transition: none; box-shadow: none; }
    .insurance-receipts-modal button.bg-purple-600 { background: var(--insurance-navy); }
    .insurance-receipts-modal #selectedItemsContainer li { padding: 10px 0; gap: 8px; }
    :is(.insurance-receipts, .insurance-receipts-modal) .insurance-date { position: relative; }
    :is(.insurance-receipts, .insurance-receipts-modal) .insurance-date-display { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #344256; pointer-events: none; }
    :is(.insurance-receipts, .insurance-receipts-modal) .insurance-date input { color: transparent; }
    :is(.insurance-receipts, .insurance-receipts-modal) .insurance-date input::-webkit-datetime-edit { opacity: 0; }
    @media (max-width: 640px) {
        .insurance-receipts .insurance-receipts-panel { padding: 14px; }
        .insurance-receipts .insurance-receipts-filters .min-w-\[300px\] { min-width: 100%; }
        .insurance-receipts .insurance-receipts-filters .w-44 { width: calc(50% - 6px); }
        .insurance-receipts .insurance-receipts-filters form > .flex { width: 100%; justify-content: flex-end; }
        .insurance-receipts .insurance-receipts-actions { gap: 6px; }
    }
</style>
@endpush

@section('content')
<div class="insurance-receipts">
    <div class="insurance-receipts-panel bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="insurance-receipts-heading flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Asuransi Tanda Terima</h1>
                <p class="text-gray-600 mt-1">
                    Kelola data asuransi untuk tanda terima 
                    <span id="selectedCount" class="text-blue-600 font-bold ml-1"></span>
                    <button type="button" onclick="toggleSelectedList()" id="viewSelectedBtn" class="text-xs text-blue-500 hover:text-blue-700 underline ml-2 hidden">
                        Lihat Detail
                    </button>
                    <button type="button" onclick="clearAllSelections()" id="clearSelectionBtn" class="text-xs text-red-500 hover:text-red-700 underline ml-2 hidden">
                        Hapus Pilihan
                    </button>
                </p>
            </div>
            <div class="insurance-receipts-actions flex space-x-2">
                <form id="bulkExportForm" action="{{ route('asuransi-tanda-terima.export-request') }}" method="POST" target="_blank">
                    @csrf
                    <input type="hidden" name="selected_ids" id="selectedIdsInput">
                    <button type="button" onclick="showExportModal()"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 text-sm rounded-lg font-medium transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Request Layout
                    </button>
                </form>

                <form id="excelExportForm" action="{{ route('asuransi-tanda-terima.export') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="selected_ids" id="excelSelectedIdsInput">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="dari_tanggal" value="{{ request('dari_tanggal') }}">
                    <input type="hidden" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}">
                    
                    <button type="button" onclick="performExcelExport()"
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 text-sm rounded-lg font-medium transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download Excel
                    </button>
                </form>
                @can('asuransi-tanda-terima-create')
                <a href="{{ route('asuransi-tanda-terima.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm rounded-lg font-medium transition duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Asuransi
                </a>
                @endcan
            </div>
        </div>
        <!-- Notification -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="insurance-receipts-filters bg-gray-50 rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('asuransi-tanda-terima.index') }}" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[300px]">
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nomor polis, kontainer, vendor..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
                <div class="w-44">
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
                <div class="w-44">
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 text-sm">
                        Cari
                    </button>
                    <a href="{{ route('asuransi-tanda-terima.index') }}" 
                       onclick="sessionStorage.removeItem(STORAGE_KEY); sessionStorage.removeItem(STORAGE_KEY + '_data')"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition duration-200 text-sm">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="insurance-receipts-table overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left">
                            <input type="checkbox" aria-label="Pilih semua data" id="selectAllCheckboxes" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Tipe Dokumen</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Nomor & Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">No. Kontainer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Nama Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center whitespace-nowrap">Kuantitas</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center whitespace-nowrap">Satuan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Pengirim / Penerima</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Status Asuransi</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($receipts as $item)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-3 py-4">
                                <input type="checkbox" name="receipt_ids[]" value="{{ $item->type }}_{{ $item->id }}" 
                                       data-number="{{ $item->number }}"
                                       data-type-label="{{ $item->type == 'tt' ? 'Tanda Terima' : ($item->type == 'tttsj' ? 'TT Tanpa SJ' : 'Tanda Terima LCL') }}"
                                       class="receipt-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->type == 'tt')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Tanda Terima</span>
                                @elseif($item->type == 'tttsj')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">TT Tanpa SJ</span>
                                @elseif($item->type == 'lcl')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-teal-100 text-teal-800">Tanda Terima LCL</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="block text-sm font-medium text-gray-900">{{ $item->number }}</span>
                                <span class="block text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->date)->format('d/M/Y') }}</span>
                                @if($item->insurance && ($item->insurance->nama_kapal || $item->insurance->nomor_voyage))
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @if($item->insurance->nama_kapal)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                                🚢 {{ $item->insurance->nama_kapal }}
                                            </span>
                                        @endif
                                        @if($item->insurance->nomor_voyage)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                                V: {{ $item->insurance->nomor_voyage }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900 font-mono">{{ $item->no_kontainer ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap max-w-md truncate" title="{{ is_array(json_decode($item->nama_barang, true)) ? implode(', ', json_decode($item->nama_barang, true)) : ($item->nama_barang ?: '') }}">
                                <span class="text-sm text-gray-900">
                                    @if($item->type == 'tt' && is_string($item->nama_barang) && (str_starts_with($item->nama_barang, '[') || str_starts_with($item->nama_barang, '{')))
                                        @php
                                            $decoded = json_decode($item->nama_barang, true);
                                            echo is_array($decoded) ? implode(', ', $decoded) : ($item->nama_barang ?: '-');
                                        @endphp
                                    @else
                                        {{ $item->nama_barang ?: '-' }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="text-sm text-gray-900 font-medium">{{ $item->kuantitas ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $item->satuan ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <span class="font-medium">Dari:</span> {{ $item->pengirim }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    <span class="font-medium">Ke:</span> {{ $item->penerima }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->insurance)
                                    <div class="flex flex-col">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 w-fit">
                                            Terproteksi
                                        </span>
                                        <span class="text-xs text-gray-600 mt-1 font-medium">{{ $item->insurance->nomor_polis }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $item->insurance->vendorAsuransi->nama_asuransi }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Belum Diasuransikan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex justify-center space-x-2">
                                    @if($item->insurance)
                                        <a href="{{ route('asuransi-tanda-terima.show', $item->insurance->id) }}" title="Lihat Polis" class="text-blue-600 hover:text-blue-900 bg-blue-50 p-1.5 rounded-lg transition duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        @can('asuransi-tanda-terima-update')
                                        <a href="{{ route('asuransi-tanda-terima.edit', $item->insurance->id) }}" title="Edit Polis" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 p-1.5 rounded-lg transition duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        @endcan
                                    @else
                                        @can('asuransi-tanda-terima-create')
                                        <a href="{{ route('asuransi-tanda-terima.create', ['type' => $item->type, 'id' => $item->id]) }}" 
                                           class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition duration-200 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Input Asuransi
                                        </a>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-lg">Tidak ada data tanda terima ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="insurance-receipts-pagination mt-6">
            {{ $receipts->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Selected Items List Modal -->
<div id="selectedListModal" class="insurance-receipts-modal fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="toggleSelectedList()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Daftar Pilihan</h3>
                    <button type="button" onclick="toggleSelectedList()" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div id="selectedItemsContainer" class="max-h-96 overflow-y-auto">
                    <!-- Items will be populated here -->
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="toggleSelectedList()" 
                        class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto sm:text-sm transition duration-200">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Export Configuration Modal -->
<div id="exportModal" class="insurance-receipts-modal fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="hideExportModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Konfigurasi Export Layout Request</h3>
                        <p class="text-xs text-blue-600 mt-1">* Vendor dan Nama Kapal diambil otomatis dari data input asuransi.</p>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Tanggal Request</label>
                            <input type="date" id="export_request_date" value="{{ date('Y-m-d') }}" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="performBulkExport()" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm transition duration-200">
                    Generate Export
                </button>
                <button type="button" onclick="hideExportModal()" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition duration-200">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const STORAGE_KEY = 'selected_asuransi_receipt_ids';

    function getSelectedIds() {
        const stored = sessionStorage.getItem(STORAGE_KEY);
        return stored ? JSON.parse(stored) : [];
    }

    function setSelectedIds(ids) {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
    }

    function updateCheckboxStates() {
        const selectedIds = getSelectedIds();
        const checkboxes = document.querySelectorAll('.receipt-checkbox');
        let allChecked = checkboxes.length > 0;

        checkboxes.forEach(cb => {
            const row = cb.closest('tr');
            if (selectedIds.includes(cb.value)) {
                cb.checked = true;
                if (row) row.classList.add('bg-blue-50');
            } else {
                cb.checked = false;
                if (row) row.classList.remove('bg-blue-50');
                allChecked = false;
            }
        });

        const selectAll = document.getElementById('selectAllCheckboxes');
        if (selectAll) {
            selectAll.checked = allChecked && checkboxes.length > 0;
        }

        updateIndicator();
        updateSelectedListUI();
    }

    function updateIndicator() {
        const count = getSelectedIds().length;
        const indicator = document.getElementById('selectedCount');
        const clearBtn = document.getElementById('clearSelectionBtn');
        
        if (indicator) {
            indicator.textContent = count > 0 ? `(${count} terpilih)` : '';
        }
        
        if (count > 0) {
            if (clearBtn) clearBtn.classList.remove('hidden');
            if (document.getElementById('viewSelectedBtn')) document.getElementById('viewSelectedBtn').classList.remove('hidden');
        } else {
            if (clearBtn) clearBtn.classList.add('hidden');
            if (document.getElementById('viewSelectedBtn')) document.getElementById('viewSelectedBtn').classList.add('hidden');
        }
    }

    function toggleSelectedList() {
        const modal = document.getElementById('selectedListModal');
        modal.classList.toggle('hidden');
        if (!modal.classList.contains('hidden')) {
            updateSelectedListUI();
        }
    }

    function updateSelectedListUI() {
        const container = document.getElementById('selectedItemsContainer');
        if (!container) return;

        const selectedIds = getSelectedIds();
        if (selectedIds.length === 0) {
            container.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">Belum ada data terpilih</p>';
            return;
        }

        // Try to get info from current page checkboxes
        const pageData = {};
        document.querySelectorAll('.receipt-checkbox').forEach(cb => {
            pageData[cb.value] = {
                number: cb.dataset.number,
                type: cb.dataset.typeLabel
            };
        });

        // Store persistent data in sessionStorage for cross-page info
        let persistentData = JSON.parse(sessionStorage.getItem(STORAGE_KEY + '_data') || '{}');
        Object.assign(persistentData, pageData);
        sessionStorage.setItem(STORAGE_KEY + '_data', JSON.stringify(persistentData));

        let html = '<ul class="divide-y divide-gray-200">';
        selectedIds.forEach(id => {
            const data = persistentData[id] || { number: id, type: 'Data' };
            html += `
                <li class="py-3 flex justify-between items-center">
                    <div>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full ${data.type === 'Tanda Terima' ? 'bg-blue-100 text-blue-800' : (data.type === 'TT Tanpa SJ' ? 'bg-purple-100 text-purple-800' : 'bg-teal-100 text-teal-800')}">${data.type}</span>
                        <span class="ml-2 text-sm font-medium text-gray-900">${data.number}</span>
                    </div>
                    <button type="button" onclick="removeFromSelection('${id}')" class="text-red-500 hover:text-red-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </li>
            `;
        });
        html += '</ul>';
        container.innerHTML = html;
    }

    function removeFromSelection(id) {
        let selectedIds = getSelectedIds();
        selectedIds = selectedIds.filter(item => item !== id);
        setSelectedIds(selectedIds);
        updateCheckboxStates();
        updateSelectedListUI();
    }

    function clearAllSelections() {
        if (confirm('Hapus semua pilihan?')) {
            setSelectedIds([]);
            sessionStorage.removeItem(STORAGE_KEY + '_data');
            updateCheckboxStates();
        }
    }

    // Initialize checkboxes on load
    document.addEventListener('DOMContentLoaded', updateCheckboxStates);

    // Individual checkbox change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('receipt-checkbox')) {
            let selectedIds = getSelectedIds();
            const row = e.target.closest('tr');
            
            if (e.target.checked) {
                if (!selectedIds.includes(e.target.value)) {
                    selectedIds.push(e.target.value);
                }
                if (row) row.classList.add('bg-blue-50');
            } else {
                selectedIds = selectedIds.filter(id => id !== e.target.value);
                if (row) row.classList.remove('bg-blue-50');
            }
            setSelectedIds(selectedIds);
            updateIndicator();
            updateSelectedListUI();
            
            // Update select all state
            const checkboxes = document.querySelectorAll('.receipt-checkbox');
            const selectAll = document.getElementById('selectAllCheckboxes');
            if (selectAll) {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                selectAll.checked = allChecked && checkboxes.length > 0;
            }
        }
    });

    // Select All change
    document.getElementById('selectAllCheckboxes').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.receipt-checkbox');
        let selectedIds = getSelectedIds();
        
        checkboxes.forEach(cb => {
            const row = cb.closest('tr');
            cb.checked = this.checked;
            if (this.checked) {
                if (!selectedIds.includes(cb.value)) {
                    selectedIds.push(cb.value);
                }
                if (row) row.classList.add('bg-blue-50');
            } else {
                selectedIds = selectedIds.filter(id => id !== cb.value);
                if (row) row.classList.remove('bg-blue-50');
            }
        });
        
        setSelectedIds(selectedIds);
        updateIndicator();
        updateSelectedListUI();
    });

    function showExportModal() {
        const selected = getSelectedIds();
        if (selected.length === 0) {
            alert('Silakan pilih minimal satu data tanda terima.');
            return;
        }
        document.getElementById('selectedIdsInput').value = JSON.stringify(selected);
        document.getElementById('exportModal').classList.remove('hidden');
    }

    function hideExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
    }

    function performExcelExport() {
        const selected = getSelectedIds();
        const input = document.getElementById('excelSelectedIdsInput');
        const form = document.getElementById('excelExportForm');
        
        if (selected.length > 0) {
            input.value = JSON.stringify(selected);
        } else {
            input.value = '';
        }
        
        form.submit();
    }

    function performBulkExport() {
        const form = document.getElementById('bulkExportForm');
        const reqDate = document.getElementById('export_request_date').value;

        // Cleanup old dynamics if any
        form.querySelectorAll('.dynamic-input').forEach(el => el.remove());

        const dInput = document.createElement('input');
        dInput.type = 'hidden'; 
        dInput.name = 'request_date'; 
        dInput.value = reqDate; 
        dInput.className = 'dynamic-input';
        form.appendChild(dInput);

        form.submit();
        hideExportModal();
    }
</script>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.insurance-receipts input[type="date"], .insurance-receipts-modal input[type="date"]').forEach(function(input) {
        const control = document.createElement('div');
        control.className = 'insurance-date';
        const display = document.createElement('span');
        display.className = 'insurance-date-display';
        display.setAttribute('aria-hidden', 'true');
        input.before(control);
        control.append(input, display);
        function updateDisplay() {
            display.textContent = input.value ? new Intl.DateTimeFormat('en-GB', {
                day: '2-digit', month: 'short', year: 'numeric'
            }).format(new Date(input.value + 'T00:00:00')).replace(/ /g, '/').replace('Sept', 'Sep') : 'dd/mmm/yyyy';
        }
        input.addEventListener('input', updateDisplay);
        input.addEventListener('change', updateDisplay);
        input.addEventListener('focus', updateDisplay);
        updateDisplay();
    });
</script>
@endpush
