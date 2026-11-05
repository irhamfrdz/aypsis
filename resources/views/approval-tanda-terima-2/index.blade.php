@extends('layouts.app')

@section('title', 'Approval Tanda Terima 2')
@section('page_title', 'Approval Tanda Terima 2')

@push('styles')
<style>
    #approval-shipper-dialog { width: min(1100px, calc(100vw - 24px)); max-height: calc(100vh - 32px); border: 0; border-radius: 16px; padding: 0; overflow-y: auto; }
    #approval-shipper-dialog::backdrop { background: rgb(15 23 42 / 60%); }
    #approval-ttsj-dialog { width: min(1100px, calc(100vw - 24px)); max-height: calc(100vh - 32px); border: 0; border-radius: 16px; padding: 0; overflow-y: auto; }
    #approval-ttsj-dialog::backdrop { background: rgb(15 23 42 / 60%); }
    #approval-ttsj-dialog { background: #f8fafc; color: #0f172a; }
    #approval-ttsj-dialog > div:first-child { position: sticky; top: 0; z-index: 3; background: #fff; }
    #approval-ttsj-dialog form > div:last-child { position: sticky; bottom: 0; z-index: 3; background: #fff; box-shadow: 0 -4px 16px rgb(15 23 42 / 6%); }
    #approval-ttsj-dialog .ttsj-goods-row { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; box-shadow: 0 2px 4px rgb(15 23 42 / 3%); }
    #approval-ttsj-dialog .ttsj-goods-row > h4 { grid-column: 1 / -1; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 14px; font-weight: 700; }
    #approval-ttsj-dialog .ttsj-goods-field { min-width: 0; }
    #approval-ttsj-dialog .ttsj-goods-field[hidden] { display: none; }
    #approval-ttsj-dialog .ttsj-goods-field[data-field="nama_barang"] { grid-column: span 2; }
    #approval-ttsj-dialog .ttsj-goods-field label, #approval-ttsj-dialog .ttsj-goods-note label { display: block; margin-bottom: 7px; color: #475569; font-size: 12px; font-weight: 600; }
    #approval-ttsj-dialog .ttsj-goods-field input, #approval-ttsj-dialog .ttsj-goods-field textarea, #approval-ttsj-dialog .ttsj-goods-note textarea { box-sizing: border-box; width: 100%; min-width: 0; min-height: 42px; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; color: #0f172a; font-size: 14px; line-height: 20px; }
    #approval-ttsj-dialog .ttsj-goods-field textarea { min-height: 60px; resize: vertical; }
    #approval-ttsj-dialog .ttsj-goods-field[data-field="hs_codes"] textarea { min-height: 96px; }
    #approval-ttsj-dialog .ttsj-goods-field input:focus, #approval-ttsj-dialog .ttsj-goods-field textarea:focus, #approval-ttsj-dialog .ttsj-goods-note textarea:focus { outline: 0; border-color: #6366f1; box-shadow: 0 0 0 3px rgb(99 102 241 / 15%); }
    #approval-ttsj-dialog .ttsj-goods-field input::placeholder, #approval-ttsj-dialog .ttsj-goods-field textarea::placeholder { color: #94a3b8; }
    @media (max-width: 640px) {
        #approval-ttsj-dialog { width: calc(100vw - 16px); max-height: calc(100dvh - 16px); }
        #approval-ttsj-dialog .ttsj-goods-row { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; padding: 16px; }
    }
    #approval-shipper-dialog [hidden] { display: none !important; }
    .approval-shipper-combobox { position: relative; }
    #approval-shipper-search { display: block; width: 100%; min-height: 44px; padding: 10px 42px; border: 1px solid #cbd5e1; border-radius: 10px; background: #fff; color: #0f172a; box-shadow: 0 1px 2px rgb(15 23 42 / 5%); }
    #approval-shipper-search::placeholder { color: #94a3b8; }
    #approval-shipper-search:hover { border-color: #818cf8; }
    #approval-shipper-search:focus { border-color: #6366f1; outline: 0; box-shadow: 0 0 0 3px rgb(99 102 241 / 18%); }
    .approval-shipper-search-icon, .approval-shipper-search-arrow { position: absolute; top: 22px; transform: translateY(-50%); color: #64748b; pointer-events: none; }
    .approval-shipper-search-icon { left: 15px; }
    .approval-shipper-search-arrow { right: 16px; }
    #approval-shipper-options { position: absolute; z-index: 20; top: calc(100% + 6px); left: 0; right: 0; max-height: 220px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 10px; background: #fff; box-shadow: 0 14px 32px rgb(15 23 42 / 16%); padding: 4px; color: #475569; }
    #approval-shipper-options.approval-shipper-message { padding: 12px; }
    .approval-shipper-option { display: block; width: 100%; padding: 10px 12px; text-align: left; border: 0; border-radius: 7px; background: #fff; color: #0f172a; }
    .approval-shipper-option + .approval-shipper-option { border-top: 1px solid #f1f5f9; }
    .approval-shipper-option:hover, .approval-shipper-option:focus { background: #eef2ff; outline: 2px solid #6366f1; outline-offset: -2px; }
    .approval-shipper-option-name { display: block; font-weight: 600; }
    .approval-shipper-option-detail { display: block; margin-top: 2px; font-size: 12px; color: #64748b; white-space: pre-line; overflow-wrap: anywhere; }
    .approval-shipper-field-preview { display: block; width: 100%; margin-top: 2px; padding: 2px 4px; text-align: left; white-space: pre-line; overflow-wrap: anywhere; border: 0; border-radius: 4px; background: transparent; color: #1f2937; cursor: text; }
    .approval-shipper-field-preview:not(:disabled):hover, .approval-shipper-field-preview:not(:disabled):focus-visible { outline: 2px solid #6366f1; outline-offset: 1px; }
    .approval-shipper-field-preview:disabled { cursor: default; }
    .approval-shipper-name-highlight, .approval-shipper-document-highlight { display: inline-block; max-width: 100%; padding: 2px 6px; border-radius: 4px; font-weight: 700; }
    .approval-shipper-name-highlight { background: #fef08a; color: #713f12; }
    .approval-shipper-document-highlight { background: #ff0000; color: #111827; }
    .approval-shipper-field-preview.approval-shipper-name-highlight, .approval-shipper-field-preview.approval-shipper-document-highlight { display: inline-block; width: auto; }
    .approval-goods-dialog { width: min(1000px, calc(100vw - 24px)); max-height: calc(100vh - 32px); border: 0; border-radius: 16px; padding: 0; overflow-y: auto; }
    .approval-goods-dialog::backdrop { background: rgb(15 23 42 / 60%); }
    .approval-goods-dialog { width: min(1040px, calc(100vw - 32px)); background: #f8fafc; color: #0f172a; }
    .approval-goods-dialog > div:first-child { position: sticky; top: 0; z-index: 2; background: #fff; padding: 20px 24px; }
    .approval-goods-dialog form > div:last-child { position: sticky; bottom: 0; z-index: 2; background: #fff; padding: 16px 24px; box-shadow: 0 -4px 16px rgb(15 23 42 / 4%); }
    .approval-goods-dialog .overflow-x-auto { overflow: visible; padding: 20px 24px; }
    .approval-goods-dialog table, .approval-goods-rows { display: block; width: 100%; }
    .approval-goods-dialog thead { display: none; }
    .approval-goods-rows { counter-reset: goods; border: 0; }
    .approval-goods-rows > tr { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; padding: 20px; margin-bottom: 16px; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; box-shadow: 0 2px 4px rgb(15 23 42 / 3%); }
    .approval-goods-rows > tr::before { counter-increment: goods; content: 'Barang ' counter(goods); grid-column: 1 / -1; font-size: 14px; font-weight: 700; color: #334155; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; }
    .approval-goods-rows td { display: block; min-width: 0; padding: 0; white-space: normal; overflow-wrap: anywhere; }
    .approval-goods-rows td[hidden] { display: none; }
    .approval-goods-rows td::before { content: attr(data-label); display: block; margin-bottom: 7px; font-size: 12px; font-weight: 600; color: #475569; }
    .approval-goods-rows td[data-field="nama_barang"], .approval-goods-rows td[data-field="keterangan_barang"] { grid-column: span 2; }
    .approval-goods-dialog input:not([type="hidden"]), .approval-goods-dialog textarea { display: block; box-sizing: border-box; width: 100%; min-width: 0; min-height: 42px; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; color: #0f172a; font-size: 14px; line-height: 20px; }
    .approval-goods-rows td[data-field="nama_barang"] textarea { min-width: 160px; max-width: 100%; min-height: 60px; resize: both; overflow: auto; }
    .approval-goods-dialog input::placeholder, .approval-goods-dialog textarea::placeholder { color: #94a3b8; }
    .approval-goods-dialog input:focus, .approval-goods-dialog textarea:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgb(99 102 241 / 15%); }
    .approval-receipts-table { width: 100%; table-layout: fixed; }
    .approval-receipts-table > thead > tr > th, .approval-receipts-table > tbody > tr > td { padding: 12px 8px; overflow-wrap: anywhere; }
    .approval-goods-preview { display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 3; line-clamp: 3; max-height: 4.2em; line-height: 1.4; overflow: hidden; overflow-wrap: anywhere; }
    .approval-receipts-table .approval-actions-cell { white-space: normal; text-align: left; }
    .approval-receipt-handle { cursor: grab; touch-action: none; }
    .approval-receipt-handle:active { cursor: grabbing; }
    .approval-receipts-table tbody tr.approval-dragging { opacity: .45; }
    .approval-receipts-table tbody tr.approval-drop-target { outline: 2px solid #6366f1; outline-offset: -2px; }
    .approval-actions-cell > form, .approval-actions-cell > button { margin: 0 6px 6px 0; vertical-align: top; }
    @media (max-width: 1279px) {
        .approval-receipts-table, .approval-receipts-table > tbody { display: block; width: 100%; }
        .approval-receipts-table > colgroup, .approval-receipts-table > thead { display: none; }
        .approval-receipts-table > tbody { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 380px), 1fr)); gap: 12px; padding: 12px; }
        .approval-receipts-table > tbody > tr { position: relative; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px 14px; min-width: 0; padding: 16px; border: 1px solid #e2e8f0; border-radius: 12px; }
        .approval-receipts-table > tbody > tr > td { display: block; min-width: 0; padding: 0; border: 0; white-space: normal; }
        .approval-receipts-table > tbody > tr > td[data-label]::before { content: attr(data-label); display: block; margin-bottom: 3px; color: #64748b; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .approval-receipts-table .approval-receipt-select { position: absolute; top: 20px; left: 16px; }
        .approval-receipts-table .approval-receipt-order { position: absolute; top: 16px; right: 16px; border-radius: 9999px; background: #eef2ff; padding: 4px 9px; color: #4338ca; font-size: 12px; font-weight: 700; }
        .approval-receipts-table .approval-receipt-number { grid-column: 1 / -1; padding-left: 28px; padding-right: 48px; }
        .approval-receipts-table .approval-receipt-wide, .approval-receipts-table .approval-actions-cell { grid-column: 1 / -1; }
        .approval-receipts-table .approval-actions-cell { padding-top: 10px; border-top: 1px solid #e2e8f0; background: transparent !important; }
        .approval-receipts-table .approval-empty-row { display: block; grid-column: 1 / -1; }
    }
    @media (max-width: 640px) {
        .approval-goods-dialog { width: calc(100vw - 16px); max-height: calc(100dvh - 16px); }
        .approval-goods-dialog .overflow-x-auto { padding: 16px; }
        .approval-goods-rows > tr { grid-template-columns: repeat(2, minmax(0, 1fr)); padding: 16px; gap: 14px; }
        .approval-goods-dialog form > div:last-child { flex-wrap: wrap; padding: 14px 16px; }
    }
</style>
@endpush

@section('content')
<div class="container mx-auto max-w-7xl px-4 py-6 sm:px-6">
    <div class="mb-6 rounded-2xl bg-gradient-to-r from-indigo-700 to-indigo-600 px-6 py-6 text-white shadow-sm sm:px-8">
        <div class="flex items-start gap-4">
            <div class="hidden rounded-xl bg-white/15 p-3 sm:block"><i class="fas fa-file-signature text-2xl" aria-hidden="true"></i></div>
            <div>
                <h1 class="text-2xl font-bold">Approval Tanda Terima 2</h1>
                <p class="mt-1 max-w-2xl text-sm text-indigo-100">Pilih shipper dari Master Shipper / Consignee untuk tanda terima. Data pilihan digunakan pada manifest voyage JB.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div role="status" class="mb-5 flex items-start gap-2 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <i class="fas fa-circle-check mt-0.5" aria-hidden="true"></i><span>{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form method="GET" action="{{ route('approval-tanda-terima-2.index') }}" class="mb-5 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center gap-3 border-b border-gray-100 bg-gray-50/80 px-5 py-4 sm:px-6">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                <i class="fas fa-filter" aria-hidden="true"></i>
            </span>
            <div>
                <h2 class="font-semibold text-gray-900">Cari Tanda Terima</h2>
                <p class="text-xs text-gray-500">Cari beberapa data sekaligus, pilih hasilnya, lalu export dalam satu Excel.</p>
            </div>
        </div>
        <div class="space-y-5 px-5 py-5 sm:px-6">
            <div>
                <label for="search" class="mb-2 block text-sm font-semibold text-gray-700">Nomor tanda terima, nomor kontainer, pengirim, atau nama barang</label>
                <div class="relative">
                    <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" aria-hidden="true"></i>
                    <input id="search" name="search" type="search" value="{{ request('search') }}" class="h-11 w-full rounded-xl border border-gray-300 bg-white pl-11 pr-4 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" placeholder="Contoh: JB1001, JB1002, JB1003">
                </div>
                <p class="mt-2 text-xs text-gray-500">Pisahkan beberapa kata kunci dengan koma. Pilihan export tetap tersimpan saat Anda mencari lagi atau pindah halaman pada jenis tanda terima yang sama.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label for="type" class="mb-2 block text-sm font-semibold text-gray-700">Jenis tanda terima</label>
                    <select id="type" name="type" class="h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                        <option value="fcl" @selected($type === 'fcl')>FCL</option>
                        <option value="lcl" @selected($type === 'lcl')>LCL</option>
                        <option value="ttsj" @selected($type === 'ttsj')>Tanpa Surat Jalan</option>
                    </select>
                </div>
                <div>
                    <label for="destination" class="mb-2 block text-sm font-semibold text-gray-700">Tujuan pengiriman</label>
                    <select id="destination" name="destination" class="h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                        <option value="" @selected($destination === '')>Semua tujuan</option>
                        <option value="jakarta" @selected($destination === 'jakarta')>Jakarta</option>
                        <option value="batam" @selected($destination === 'batam')>Batam</option>
                        <option value="tanjung-pinang" @selected($destination === 'tanjung-pinang')>Tanjung Pinang</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="mb-2 block text-sm font-semibold text-gray-700">Status shipper</label>
                    <select id="status" name="status" class="h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                        <option value="">Semua status</option>
                        <option value="belum" @selected(request('status') === 'belum')>Belum dipilih</option>
                        <option value="sudah" @selected(request('status') === 'sudah')>Sudah dipilih</option>
                        <option value="belum_lengkap" @selected(request('status') === 'belum_lengkap')>Belum lengkap</option>
                    </select>
                </div>
                <div>
                    <label for="manifest_status" class="mb-2 block text-sm font-semibold text-gray-700">Status Naik ke Kapal</label>
                    <select id="manifest_status" name="manifest_status" class="h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                        <option value="belum" @selected($manifestStatus === 'belum')>Belum Naik ke Kapal</option>
                        <option value="sudah" @selected($manifestStatus === 'sudah')>Sudah Naik ke Kapal</option>
                        <option value="semua" @selected($manifestStatus === 'semua')>Semua</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex flex-col-reverse gap-2 border-t border-gray-100 bg-gray-50/80 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
            <a href="{{ route('approval-tanda-terima-2.index') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 hover:bg-gray-100">
                <i class="fas fa-rotate-left" aria-hidden="true"></i>Reset filter
            </a>
            <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <i class="fas fa-search" aria-hidden="true"></i>Tampilkan hasil
            </button>
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 px-5 py-4">
            <div>
                <h2 class="font-semibold text-gray-900">Daftar Tanda Terima</h2>
                @can('approval-tanda-terima-2-approve')<p class="mt-1 text-xs text-gray-500">Tarik ikon pada kolom No. ke baris lain untuk menukar posisinya.</p>@endcan
                <p id="approval-order-status" class="text-xs text-indigo-700" role="status" aria-live="polite"></p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @can('approval-tanda-terima-2-approve')
                    @if($type === 'ttsj')
                        <button id="approval-ttsj-open" type="button" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700"><i class="fas fa-plus" aria-hidden="true"></i>Tambah Tanda Terima</button>
                    @endif
                    <label class="inline-flex items-center gap-2 text-xs font-medium text-gray-700"><input id="approval-select-all" type="checkbox" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" aria-label="Pilih semua tanda terima di halaman ini">Pilih halaman ini</label>
                    @if($containerQuantityTotal !== null)
                        <span class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-800">Total barang kontainer: {{ number_format($containerQuantityTotal, 0, ',', '.') }}</span>
                    @endif
                    <form id="approval-bulk-export-form" method="POST" action="{{ route('approval-tanda-terima-2.export-bulk', ['sourceType' => $type]) }}" class="approval-export-form">
                        @csrf
                        <button type="submit" class="approval-export-button inline-flex items-center gap-2 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-800 hover:bg-emerald-100 disabled:cursor-not-allowed disabled:opacity-50" disabled>
                            <i class="fas fa-file-excel" aria-hidden="true"></i><span>Export Pilihan (0)</span>
                        </button>
                    </form>
                    <form id="approval-bulk-clear-form" method="POST" action="{{ route('approval-tanda-terima-2.clear-export-bulk', ['sourceType' => $type]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50" disabled>
                            <i class="fas fa-rotate-left" aria-hidden="true"></i><span>Lepas Tanda Export (0)</span>
                        </button>
                    </form>
                    <button id="approval-clear-selection" type="button" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50" disabled>Hapus Pilihan</button>
                @endcan
                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">{{ $items->total() }} data</span>
            </div>
        </div>
        <div>
            <table class="approval-receipts-table divide-y divide-gray-200 text-sm" data-swap-url="{{ route('approval-tanda-terima-2.swap-order', ['sourceType' => $type]) }}" data-start-number="{{ $items->firstItem() ?? 1 }}">
                <colgroup>
                    @can('approval-tanda-terima-2-approve')<col style="width:4%">@endcan
                    <col style="width:4%">
                    <col style="width:10%"><col style="width:8%"><col style="width:10%"><col style="width:10%">
                    <col style="width:15%"><col style="width:10%"><col style="width:12%"><col style="width:17%">
                </colgroup>
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        @can('approval-tanda-terima-2-approve')
                            <th scope="col" aria-label="Pilihan"></th>
                        @endcan
                        <th scope="col" class="px-3 py-3 text-center">No.</th>
                        <th scope="col" class="px-5 py-3">Tanda Terima</th>
                        <th scope="col" class="px-5 py-3">Tanggal</th>
                        <th scope="col" class="px-5 py-3">Nomor Kontainer</th>
                        <th scope="col" class="px-5 py-3">Jumlah / Satuan Manifest</th>
                        <th scope="col" class="px-5 py-3">Nama Barang</th>
                        <th scope="col" class="px-5 py-3">Shipper Perincian</th>
                        <th scope="col" class="px-5 py-3">Shipper Manifest JB</th>
                        <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="approval-receipt-rows" class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        @php
                            $number = $type === 'fcl' ? $item->no_surat_jalan : ($type === 'lcl' ? $item->nomor_tanda_terima : ($item->no_tanda_terima ?: $item->nomor_tanda_terima));
                            $sender = $type === 'lcl' ? $item->nama_pengirim : $item->pengirim;
                            $date = $type === 'fcl' ? $item->tanggal : $item->tanggal_tanda_terima;
                            $containerNumber = $type === 'lcl' ? $item->nomor_kontainer : $item->no_kontainer;
                            $goodsOverride = $goodsOverrides->get($item->id);
                            $shipperApprovalOverride = $shipperOverrides->get($item->id.'|main');
                            $shipperFields = array_replace($item->shipperJb?->getAttributes() ?? [], $shipperApprovalOverride?->fields ?? []);
                            $manifestGood = collect($goodsOverride?->manifest_goods ?? [])->first();
                            $manifestQuantity = data_get($manifestGood, 'jumlah');
                            $manifestUnit = data_get($manifestGood, 'satuan');
                            $isIncomplete = $incompleteMarks->has($item->id);
                            $isExported = $exportedMarks->has($item->id);
                            $goods = $goodsOverride ? collect($goodsOverride->goods) : match ($type) {
                                'fcl' => collect($item->dimensi_items ?: $item->dimensi_details ?: $item->nama_barang ?: []),
                                'lcl' => $item->items,
                                'ttsj' => $item->dimensiItems,
                            };
                            if (!$goodsOverride && $goods->isEmpty() && $type === 'ttsj' && ($item->nama_barang || $item->jenis_barang)) {
                                $goods = collect([[
                                    'nama_barang' => $item->nama_barang ?: $item->jenis_barang,
                                    'jumlah' => $item->jumlah_barang,
                                    'satuan' => $item->satuan_barang,
                                    'ukuran' => $item->ukuran,
                                    'meter_kubik' => $item->meter_kubik,
                                    'tonase' => $item->tonase,
                                    'keterangan_barang' => $item->keterangan_barang,
                                ]]);
                            }
                            $firstGood = $goods->first();
                            $goodsName = is_scalar($firstGood) ? $firstGood : data_get($firstGood, 'nama_barang');
                            $perincianGoods = $goods->isEmpty() && auth()->user()?->can('approval-tanda-terima-2-approve')
                                ? collect([[]])
                                : $goods->take(1)->values();
                            $keteranganBarang = $goodsOverride ? $goodsOverride->keterangan_barang : $item->keterangan_barang;
                        @endphp
                        <tr data-receipt-id="{{ $item->id }}" class="{{ $isExported ? 'bg-green-100 hover:bg-green-100' : ($isIncomplete ? 'bg-yellow-100 hover:bg-yellow-100' : 'hover:bg-gray-50/70') }}">
                            @can('approval-tanda-terima-2-approve')
                                <td class="approval-receipt-select"><input type="checkbox" name="ids[]" value="{{ $item->id }}" form="approval-bulk-export-form" data-exported="{{ $isExported ? 'true' : 'false' }}" class="approval-bulk-checkbox rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" aria-label="Pilih tanda terima {{ $number ?: $item->id }}"></td>
                            @endcan
                            <td class="approval-receipt-order text-center font-semibold text-gray-700">
                                <span class="inline-flex items-center justify-center gap-1">
                                    @can('approval-tanda-terima-2-approve')
                                        <button type="button" draggable="true" class="approval-receipt-handle rounded p-1 text-gray-400 hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500" aria-label="Tarik untuk menukar posisi tanda terima {{ $number ?: $item->id }}" title="Tarik untuk menukar posisi"><i class="fas fa-grip-vertical" aria-hidden="true"></i></button>
                                    @endcan
                                    <span class="approval-order-number">{{ $items->firstItem() + $loop->index }}</span>
                                </span>
                            </td>
                            <td data-label="Tanda Terima" class="approval-receipt-number font-semibold text-gray-900">{{ $number ?: 'Tanpa nomor' }}</td>
                            <td data-label="Tanggal" class="text-gray-600">{{ $date ? \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') : '-' }}</td>
                            <td data-label="Nomor Kontainer" class="font-medium text-gray-700">{{ $containerNumber ?: '-' }}</td>
                            <td data-label="Jumlah / Satuan Manifest" class="text-gray-700">
                                @if(filled($manifestQuantity) || filled($manifestUnit))
                                    <span class="inline-flex flex-wrap items-baseline gap-1"><span>{{ filled($manifestQuantity) ? $manifestQuantity : '-' }}</span><span>{{ $manifestUnit ?: '-' }}</span></span>
                                @else
                                    -
                                @endif
                            </td>
                            <td data-label="Nama Barang" class="approval-receipt-wide text-gray-700">
                                @if(is_scalar($goodsName) && filled($goodsName))
                                    <div class="approval-goods-preview">{{ $goodsName }}</div>
                                    @if(\Illuminate\Support\Str::length((string) $goodsName) > 60 || substr_count((string) $goodsName, "\n") > 1)
                                        <button type="button" class="approval-goods-open mt-1 text-left text-xs font-semibold text-indigo-700 hover:text-indigo-900 hover:underline focus:outline-none focus:underline"
                                            data-dialog-id="approval-goods-{{ $type }}-{{ $item->id }}">Lihat selengkapnya</button>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td data-label="Shipper Perincian" class="text-gray-700">{{ $sender ?: '-' }}</td>
                            <td data-label="Shipper Manifest JB">
                                @if($item->shipperJb)
                                    <div class="font-medium text-gray-900">{{ $type === 'fcl' ? 'Utama: ' : '' }}{{ $shipperFields['shipper'] ?? $item->shipperJb->shipper }}</div>
                                    @if($shipperFields['consignee'] ?? $item->shipperJb->consignee)<div class="mt-0.5 text-xs text-gray-500">Consignee: {{ $shipperFields['consignee'] ?? $item->shipperJb->consignee }}</div>@endif
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700"><span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>Belum dipilih</span>
                                @endif
                                @if($type === 'fcl' && $item->fclShipperDetails->isNotEmpty())
                                    <div class="mt-2 space-y-1 border-t border-gray-200 pt-2 text-xs">
                                        @foreach($item->fclShipperDetails as $detail)
                                            @php
                                                $detailShipperFields = array_replace(
                                                    $detail->shipperJb?->getAttributes() ?? [],
                                                    $shipperOverrides->get($item->id.'|detail:'.$detail->id)?->fields ?? []
                                                );
                                            @endphp
                                            <div class="font-medium text-indigo-800">{{ $loop->iteration + 1 }}. {{ $detailShipperFields['shipper'] ?? '-' }} <span class="font-normal text-gray-500">→ {{ $detailShipperFields['consignee'] ?? '-' }}</span></div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td data-label="Aksi" class="approval-actions-cell {{ $isExported ? 'bg-green-200' : ($isIncomplete ? 'bg-yellow-200' : '') }}">
                                @if($isIncomplete)
                                    <span class="mr-2 inline-flex rounded bg-yellow-300 px-2 py-1 text-xs font-semibold text-yellow-950">Belum lengkap</span>
                                @endif
                                @can('approval-tanda-terima-2-approve')
                                    <form method="POST" action="{{ route('approval-tanda-terima-2.export', ['sourceType' => $type, 'id' => $item->id]) }}" class="approval-export-form mr-2 inline-block">
                                        @csrf
                                        <button type="submit" class="approval-export-button inline-flex items-center gap-2 rounded-lg border px-3 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-green-500 {{ $isExported ? 'border-green-500 bg-green-300 text-green-950 hover:bg-green-400' : 'border-emerald-300 bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                                            <i class="fas fa-file-excel" aria-hidden="true"></i><span>{{ $isExported ? 'Export Lagi' : 'Export Excel' }}</span>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('approval-tanda-terima-2.clear-export', ['sourceType' => $type, 'id' => $item->id]) }}" class="approval-export-reset-form mr-2 {{ $isExported ? 'inline-block' : 'hidden' }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-rotate-left" aria-hidden="true"></i>Lepas Tanda Export
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('approval-tanda-terima-2.set-incomplete', ['sourceType' => $type, 'id' => $item->id]) }}" class="mr-2 inline-block">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="incomplete" value="{{ $isIncomplete ? '0' : '1' }}">
                                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-yellow-400 bg-yellow-100 px-3 py-2 text-xs font-semibold text-yellow-900 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                            <i class="fas fa-highlighter" aria-hidden="true"></i>{{ $isIncomplete ? 'Batalkan Tanda' : 'Tandai Belum Lengkap' }}
                                        </button>
                                    </form>
                                @endcan
                                @cannot('approval-tanda-terima-2-approve')
                                    <button type="button" class="approval-goods-open inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        data-dialog-id="approval-goods-{{ $type }}-{{ $item->id }}">
                                        <i class="fas fa-box-open" aria-hidden="true"></i>{{ $type === 'fcl' ? 'Barang Shipper Utama' : 'Detail Barang' }}
                                    </button>
                                @endcannot
                                @can('approval-tanda-terima-2-approve')
                                    <button type="button" class="approval-shipper-open inline-flex items-center gap-2 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        data-update-url="{{ route('approval-tanda-terima-2.update', ['sourceType' => $type, 'id' => $item->id]) }}"
                                        data-goods-dialog-id="approval-goods-{{ $type }}-{{ $item->id }}"
                                        data-number="{{ $number }}" data-sender="{{ $sender }}"
                                        data-shipper-id="{{ $item->shipper_jb_id }}"
                                        data-shipper="{{ $shipperFields['shipper'] ?? $item->shipperJb?->shipper }}"
                                        data-shipper-overrides='@json($shipperApprovalOverride?->fields ?? [])'>
                                        <i class="fas fa-pen-to-square" aria-hidden="true"></i>Input Approval
                                    </button>
                                    @if($type === 'fcl' && $item->shipper_jb_id)
                                        <button type="button" class="approval-shipper-open ml-2 inline-flex items-center gap-2 rounded-lg border border-indigo-200 bg-white px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-50"
                                            data-update-url="{{ route('approval-tanda-terima-2.fcl-shippers.store', ['id' => $item->id]) }}"
                                            data-number="{{ $number }}" data-sender="{{ $sender }}" data-additional="true">
                                            <i class="fas fa-plus" aria-hidden="true"></i>Tambah Shipper
                                        </button>
                                    @endif
                                    @if($item->shipper_jb_id)
                                        <form method="POST" action="{{ route('approval-tanda-terima-2.destroy', ['sourceType' => $type, 'id' => $item->id]) }}" class="ml-2 inline-block" onsubmit="return confirm('Hapus shipper JB dari tanda terima ini dan manifest terkait?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500"><i class="fas fa-trash" aria-hidden="true"></i>Hapus Shipper</button>
                                        </form>
                                    @endif
                                @endcan
                                @if($type === 'fcl' && $item->fclShipperDetails->isNotEmpty())
                                    <div class="mt-3 space-y-2 border-t border-gray-200 pt-3">
                                        @foreach($item->fclShipperDetails as $detail)
                                            @include('approval-tanda-terima-2._fcl-shipper-detail', ['detail' => $detail, 'item' => $item, 'shipperApprovalOverride' => $shipperOverrides->get($item->id.'|detail:'.$detail->id)])
                                        @endforeach
                                    </div>
                                @endif
                                <dialog id="approval-goods-{{ $type }}-{{ $item->id }}" data-source-type="{{ $type }}" class="approval-goods-dialog whitespace-normal text-left shadow-2xl" aria-labelledby="approval-goods-title-{{ $type }}-{{ $item->id }}">
                                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 px-5 py-4">
                                        <div>
                                            <h2 id="approval-goods-title-{{ $type }}-{{ $item->id }}" class="text-lg font-semibold text-gray-900">{{ $type === 'fcl' ? 'Barang Shipper Utama' : 'Detail Barang' }}</h2>
                                            <p class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500">
                                                <span>Tanda terima: {{ $number ?: 'Tanpa nomor' }}</span>
                                                <span>Nomor kontainer: {{ $containerNumber ?: '-' }}</span>
                                            </p>
                                        </div>
                                        <button type="button" class="approval-goods-close rounded-lg px-2 py-1 text-xl text-gray-500 hover:bg-gray-100" aria-label="Tutup">&times;</button>
                                    </div>
                                    <p class="mx-5 mt-4 rounded-lg bg-indigo-50 px-3 py-2 text-xs text-indigo-800">
                                        {{ $goodsOverride ? 'Periksa atau perbarui detail barang manifest dan perincian.' : 'Lengkapi detail barang manifest dan perincian untuk approval.' }}
                                        Kolom bertanda * wajib diisi. Barang Manifest dan Perincian akan digunakan pada Manifest terkait.
                                    </p>
                                    @can('approval-tanda-terima-2-approve')
                                        <form method="POST" action="{{ route('approval-tanda-terima-2.update-goods', ['sourceType' => $type, 'id' => $item->id]) }}">
                                            @csrf
                                            @method('PUT')
                                    @endcan
                                    <div class="overflow-x-auto p-5">
                                        @include('approval-tanda-terima-2._manifest-goods-section', ['manifestGoods' => collect($goodsOverride?->manifest_goods ?? [])])
                                        <section class="approval-goods-section mt-8 border-t border-gray-200 pt-6" data-field-prefix="goods" data-required="true" data-saved-override="{{ $goodsOverride ? 'true' : 'false' }}">
                                        <h3 class="mb-4 text-base font-semibold text-gray-900">Detail Barang Perincian</h3>
                                        <p class="mb-4 text-xs text-gray-500">Isi satu barang perincian. Nilai awal mengikuti Manifest dan dapat diubah terpisah.</p>
                                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                                            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                                                <tr>
                                                    <th class="px-3 py-2">Jumlah</th><th class="px-3 py-2">Satuan</th><th class="px-3 py-2">Nama Barang</th><th class="px-3 py-2">Ukuran</th>
                                                    @if($type !== 'ttsj')<th class="px-3 py-2">Keterangan</th>@endif
                                                </tr>
                                            </thead>
                                            <tbody class="approval-goods-rows divide-y divide-gray-100">
                                                @foreach($perincianGoods as $good)
                                                    <tr>
                                                        @can('approval-tanda-terima-2-approve')
                                                            @foreach(['jumlah' => 'number', 'satuan' => 'text', 'nama_barang' => 'text', 'ukuran' => 'text'] as $field => $inputType)
                                                                <td class="px-2 py-2" data-field="{{ $field }}" @if($field === 'ukuran') hidden @endif>
                                                                    @if($field === 'nama_barang')
                                                                        <textarea name="goods[{{ $loop->parent->index }}][{{ $field }}]" rows="2" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ is_scalar($good) ? $good : data_get($good, $field) }}</textarea>
                                                                    @else
                                                                        <input type="{{ $inputType }}" name="goods[{{ $loop->parent->index }}][{{ $field }}]"
                                                                            value="{{ data_get($good, $field) }}"
                                                                            @if($inputType === 'number') min="0" step="{{ $field === 'jumlah' ? '1' : 'any' }}" @endif
                                                                            class="w-28 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                            @if($type !== 'ttsj')
                                                                <td class="px-2 py-2"><input type="text" name="goods[{{ $loop->index }}][keterangan_barang]" value="{{ data_get($good, 'keterangan_barang') }}" class="w-40 rounded-lg border-gray-300 text-sm"></td>
                                                            @endif
                                                        @else
                                                            @foreach(['jumlah', 'satuan', 'nama_barang', 'ukuran'] as $field)
                                                                <td class="px-3 py-3" data-field="{{ $field }}" @if($field === 'ukuran') hidden @endif>{{ $field === 'nama_barang' && is_scalar($good) ? $good : (data_get($good, $field) ?? '-') }}</td>
                                                            @endforeach
                                                            @if($type !== 'ttsj')<td class="px-3 py-3">{{ data_get($good, 'keterangan_barang') ?: '-' }}</td>@endif
                                                        @endcan
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @if($type === 'ttsj')
                                            <div class="mt-4">
                                                <label class="mb-1 block text-sm font-medium text-gray-700">Keterangan Barang</label>
                                                @can('approval-tanda-terima-2-approve')
                                                    <textarea name="keterangan_barang" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $keteranganBarang }}</textarea>
                                                @else
                                                    <p class="text-sm text-gray-700">{{ $keteranganBarang ?: '-' }}</p>
                                                @endcan
                                            </div>
                                        @endif
                                        @if($perincianGoods->isEmpty())<p class="approval-goods-empty mt-3 text-sm text-gray-500">Detail barang belum tersedia.</p>@endif
                                        </section>
                                    </div>
                                    <div class="flex justify-end gap-2 border-t border-gray-200 bg-gray-50 px-5 py-4">
                                        <button type="button" class="approval-goods-close rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Tutup</button>
                                        @can('approval-tanda-terima-2-approve')<button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Simpan Detail Barang</button>@endcan
                                    </div>
                                    @can('approval-tanda-terima-2-approve')
                                        </form>
                                    @endcan
                                </dialog>
                            </td>
                        </tr>
                    @empty
                        <tr class="approval-empty-row"><td colspan="{{ auth()->user()?->can('approval-tanda-terima-2-approve') ? 10 : 9 }}" class="px-5 py-14 text-center text-gray-500"><i class="fas fa-inbox mb-3 block text-3xl text-gray-300" aria-hidden="true"></i>Tidak ada tanda terima yang sesuai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">{{ $items->links() }}</div>
</div>

@can('approval-tanda-terima-2-approve')
@if($type === 'ttsj')
<dialog id="approval-ttsj-dialog" aria-labelledby="approval-ttsj-title" @if(old('_approval_ttsj_create')) data-reopen="true" @endif>
    <div class="flex items-start justify-between gap-3 border-b border-gray-200 px-5 py-4 sm:px-6">
        <div>
            <h2 id="approval-ttsj-title" class="text-lg font-semibold text-gray-900">Tambah Tanda Terima Tanpa Surat Jalan</h2>
            <p class="mt-1 text-sm text-gray-500">Isi data tanda terima yang belum ada di daftar approval.</p>
        </div>
        <button type="button" data-ttsj-close class="rounded-lg px-2 py-1 text-xl text-gray-500 hover:bg-gray-100" aria-label="Tutup">&times;</button>
    </div>
    <form method="POST" action="{{ route('approval-tanda-terima-2.ttsj.store') }}" data-shipper-search-url="{{ route('approval-tanda-terima-2.shippers') }}">
        @csrf
        <input type="hidden" name="_approval_ttsj_create" value="1">
        <input id="approval-ttsj-shipper-id" type="hidden" name="shipper_jb_id" value="{{ old('shipper_jb_id') }}">
        <div class="grid gap-4 px-5 py-5 sm:grid-cols-2 sm:px-6">
            <h3 class="sm:col-span-2 text-base font-semibold text-indigo-800">1. Data Tanda Terima dan Shipper / Consignee</h3>
            <div>
                <label for="approval-ttsj-number" class="mb-1 block text-sm font-medium text-gray-700">Nomor tanda terima <span class="text-red-600">*</span></label>
                <input id="approval-ttsj-number" name="no_tanda_terima" value="{{ old('no_tanda_terima', str_contains((string) request('search'), ',') ? '' : request('search')) }}" required maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="approval-ttsj-date" class="mb-1 block text-sm font-medium text-gray-700">Tanggal tanda terima <span class="text-red-600">*</span></label>
                <input id="approval-ttsj-date" name="tanggal_tanda_terima" type="date" value="{{ old('tanggal_tanda_terima', now()->toDateString()) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="sm:col-span-2">
                <label for="approval-ttsj-shipper-search" class="mb-1 block text-sm font-medium text-gray-700">Shipper / consignee dari master <span class="text-red-600">*</span></label>
                <input id="approval-ttsj-shipper-search" type="search" autocomplete="off" placeholder="Cari shipper atau consignee..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" aria-controls="approval-ttsj-shipper-results">
                <div id="approval-ttsj-shipper-results" class="mt-1 max-h-48 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-sm" hidden></div>
                <p id="approval-ttsj-shipper-message" class="mt-1 text-xs text-gray-500" aria-live="polite">Pilih pasangan shipper dan consignee dari hasil pencarian.</p>
                @error('shipper_jb_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            @php
                $ttsjShipperGroups = [
                    'Shipper' => ['shipper' => 'Shipper', 'alamat_shipper' => 'Address Shipper', 'npwp_shipper' => 'NPWP Shipper'],
                    'Consignee' => ['consignee' => 'Consignee', 'alamat_consignee' => 'Address Consignee', 'npwp_consignee' => 'NPWP Consignee'],
                    'Notify Party' => ['notify_party_consignee' => 'Notify Party', 'alamat_notify_party_consignee' => 'Address Notify Party', 'npwp_notify_party_consignee' => 'NPWP Notify Party'],
                    'Informasi Lainnya' => ['delivery_address_contact_person' => 'Delivery Address & Contact Person', 'document_ppftz_03' => 'Dokumen PPFTZ-03', 'condition' => 'Condition', 'status' => 'Status'],
                ];
            @endphp
            <div class="sm:col-span-2 grid gap-3 lg:grid-cols-2">
                @foreach($ttsjShipperGroups as $groupTitle => $fields)
                    <section class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <h4 class="mb-3 font-semibold text-gray-800">{{ $groupTitle }}</h4>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach($fields as $field => $label)
                                <div class="{{ in_array($field, ['alamat_shipper', 'alamat_consignee', 'alamat_notify_party_consignee', 'delivery_address_contact_person']) ? 'sm:col-span-2' : '' }}">
                                    <label for="approval-ttsj-field-{{ $field }}" class="mb-1 block text-xs font-medium text-gray-600">{{ $label }}</label>
                                    @if($field === 'status')
                                        <select id="approval-ttsj-field-{{ $field }}" name="{{ $field }}" data-ttsj-shipper-field="{{ $field }}" disabled class="w-full rounded-lg border-gray-300 bg-white text-sm"><option value="1">Aktif</option><option value="0">Tidak aktif</option></select>
                                    @elseif(in_array($field, ['alamat_shipper', 'alamat_consignee', 'alamat_notify_party_consignee', 'delivery_address_contact_person']))
                                        <textarea id="approval-ttsj-field-{{ $field }}" name="{{ $field }}" data-ttsj-shipper-field="{{ $field }}" rows="2" disabled class="w-full rounded-lg border-gray-300 bg-white text-sm">{{ old($field) }}</textarea>
                                    @else
                                        <input id="approval-ttsj-field-{{ $field }}" name="{{ $field }}" data-ttsj-shipper-field="{{ $field }}" value="{{ old($field) }}" maxlength="255" {{ in_array($field, ['shipper', 'consignee']) ? 'required' : '' }} disabled class="w-full rounded-lg border-gray-300 bg-white text-sm">
                                    @endif
                                    @error($field)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
            <p class="sm:col-span-2 text-xs text-gray-500">Data dapat disesuaikan untuk tanda terima ini. Perubahan membuat salinan; data master asli tetap tersedia.</p>
            <div>
                <label for="approval-ttsj-destination" class="mb-1 block text-sm font-medium text-gray-700">Tujuan pengiriman <span class="text-red-600">*</span></label>
                <input id="approval-ttsj-destination" name="tujuan_pengiriman" value="{{ old('tujuan_pengiriman', $destination === 'tanjung-pinang' ? 'Tanjung Pinang' : ucfirst($destination)) }}" required maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="approval-ttsj-container-type" class="mb-1 block text-sm font-medium text-gray-700">Jenis pengiriman <span class="text-red-600">*</span></label>
                <select id="approval-ttsj-container-type" name="tipe_kontainer" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="fcl" @selected(old('tipe_kontainer', 'fcl') === 'fcl')>FCL (Kontainer)</option>
                    <option value="cargo" @selected(old('tipe_kontainer') === 'cargo')>Cargo</option>
                </select>
            </div>
            <div id="approval-ttsj-container-fields" class="contents">
                <div>
                    <label for="approval-ttsj-container" class="mb-1 block text-sm font-medium text-gray-700">Nomor kontainer <span class="text-red-600">*</span></label>
                    <input id="approval-ttsj-container" name="no_kontainer" value="{{ old('no_kontainer') }}" maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            <div>
                <label for="approval-ttsj-size" class="mb-1 block text-sm font-medium text-gray-700">Ukuran kontainer</label>
                <input id="approval-ttsj-size" name="size_kontainer" value="{{ old('size_kontainer') }}" maxlength="50" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="approval-ttsj-seal" class="mb-1 block text-sm font-medium text-gray-700">Nomor seal</label>
                <input id="approval-ttsj-seal" name="no_seal" value="{{ old('no_seal') }}" maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            </div>
        </div>
        <section class="border-t border-gray-200 px-5 py-5 sm:px-6">
            <h3 class="mb-4 text-base font-semibold text-indigo-800">2. Detail Barang Manifest dan Perincian</h3>
            <div class="space-y-6">
                @foreach(['manifest_goods' => 'Detail Barang Manifest', 'goods' => 'Detail Barang Perincian'] as $prefix => $title)
                    <section class="{{ $prefix === 'goods' ? 'border-t border-gray-200 pt-6' : '' }}">
                        <h4 class="mb-2 text-base font-semibold text-gray-900">{{ $title }}</h4>
                        <p class="mb-4 text-xs text-gray-500">{{ $prefix === 'manifest_goods' ? 'Isi satu barang untuk manifest approval. Satu HS Code per baris.' : 'Isi satu barang perincian. Nilai awal mengikuti Manifest dan dapat diubah terpisah.' }}</p>
                        <div class="ttsj-goods-row">
                            <h4>{{ $prefix === 'manifest_goods' ? 'Rincian Barang Manifest' : 'Rincian Barang Perincian' }}</h4>
                            @if($prefix === 'manifest_goods')
                                <div class="ttsj-goods-field" data-field="hs_codes">
                                    <label for="approval-ttsj-hs-codes">HS Code</label>
                                    <textarea id="approval-ttsj-hs-codes" name="manifest_goods[0][hs_codes]" rows="3" placeholder="Contoh: 8479.89 (satu kode per baris)">{{ old('manifest_goods.0.hs_codes') }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">Satu HS Code per baris.</p>
                                </div>
                            @endif
                            @foreach(['jumlah' => 'Jumlah', 'satuan' => 'Satuan', 'nama_barang' => 'Nama barang', 'ukuran' => 'Ukuran'] as $field => $label)
                                <div class="ttsj-goods-field" data-field="{{ $field }}" @if($field === 'ukuran') data-ttsj-size-field="{{ $prefix }}" @if(!str_contains(strtolower((string) old($prefix.'.0.nama_barang')), 'keramik') && blank(old($prefix.'.0.ukuran'))) hidden @endif @endif>
                                    <label for="approval-ttsj-{{ $prefix }}-{{ $field }}">{{ $label }} {{ $field === 'ukuran' ? '' : '*' }}</label>
                                    @if($field === 'nama_barang')
                                        <textarea id="approval-ttsj-{{ $prefix }}-{{ $field }}" name="{{ $prefix }}[0][{{ $field }}]" rows="2" placeholder="Contoh: Mesin industri" required>{{ old($prefix.'.0.'.$field) }}</textarea>
                                    @else
                                        <input id="approval-ttsj-{{ $prefix }}-{{ $field }}" name="{{ $prefix }}[0][{{ $field }}]" value="{{ old($prefix.'.0.'.$field, $field === 'jumlah' ? 1 : ($field === 'satuan' ? 'unit' : '')) }}" {{ $field === 'ukuran' ? '' : 'required' }} @if($field === 'jumlah') type="number" min="1" @elseif($field === 'satuan') placeholder="Contoh: Unit, koli, karton" @endif>
                                    @endif
                                    @error($prefix.'.0.'.$field)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            @endforeach
                            @if($prefix === 'manifest_goods')
                                @foreach(['tonase' => 'Tonase', 'meter_kubik' => 'Volume (m³)'] as $field => $label)
                                    <div class="ttsj-goods-field" data-field="{{ $field }}">
                                        <label for="approval-ttsj-manifest-{{ $field }}">{{ $label }}</label>
                                        <input id="approval-ttsj-manifest-{{ $field }}" name="manifest_goods[0][{{ $field }}]" value="{{ old('manifest_goods.0.'.$field) }}" inputmode="decimal" placeholder="{{ $field === 'tonase' ? 'Contoh: 17.665,78' : 'Contoh: 3.500,25' }}">
                                        @error('manifest_goods.0.'.$field)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        @if($prefix === 'goods')
                            <div class="ttsj-goods-note mt-4">
                                <label for="approval-ttsj-note">Keterangan Barang</label>
                                <textarea id="approval-ttsj-note" name="keterangan_barang" rows="2">{{ old('keterangan_barang') }}</textarea>
                            </div>
                        @endif
                    </section>
                @endforeach
            </div>
        </section>
        <div class="flex justify-end gap-2 border-t border-gray-200 bg-gray-50 px-5 py-4 sm:px-6">
            <button type="button" data-ttsj-close class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Batal</button>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Simpan Tanda Terima</button>
        </div>
    </form>
</dialog>
@endif
<dialog id="approval-shipper-dialog" aria-labelledby="approval-shipper-title" data-search-url="{{ route('approval-tanda-terima-2.shippers') }}">
    <div class="border-b border-gray-200 px-5 py-4 sm:px-6">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 id="approval-shipper-title" class="text-lg font-semibold text-gray-900">Pilih Shipper Manifest JB</h2>
                <p id="approval-shipper-context" class="mt-1 text-sm text-gray-500"></p>
            </div>
            <button type="button" class="approval-shipper-close rounded-lg px-2 py-1 text-xl text-gray-500 hover:bg-gray-100" aria-label="Tutup">&times;</button>
        </div>
    </div>
    <form id="approval-shipper-form" method="POST" action="">
        @csrf
        @method('PUT')
        <input type="hidden" id="approval-shipper-id" name="shipper_jb_id">
        <input type="hidden" id="approval-save-goods" name="save_goods" value="1" disabled>
        <div class="space-y-4 px-5 py-5 sm:px-6">
            <div>
                <div class="mb-1 flex items-center justify-between gap-3">
                    <label for="approval-shipper-search" class="text-sm font-medium text-gray-700">Cari shipper atau consignee</label>
                    @can('master-shipper-consignee-create')
                        <a href="{{ route('master.shipper-consignee.create') }}" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-indigo-700 hover:underline"><i class="fas fa-plus mr-1" aria-hidden="true"></i>Tambah di master</a>
                    @endcan
                </div>
                <div class="approval-shipper-combobox">
                    <i class="approval-shipper-search-icon fas fa-magnifying-glass" aria-hidden="true"></i>
                    <input id="approval-shipper-search" type="search" autocomplete="off" placeholder="Ketik nama shipper atau consignee..." class="text-sm" aria-controls="approval-shipper-options" aria-expanded="false" aria-autocomplete="list">
                    <i class="approval-shipper-search-arrow fas fa-chevron-down text-xs" aria-hidden="true"></i>
                    <div id="approval-shipper-options" class="text-sm" aria-label="Hasil pencarian shipper" hidden></div>
                </div>
                <p id="approval-shipper-selection" class="mt-2 text-xs text-gray-500" aria-live="polite">Pilih shipper dari hasil pencarian.</p>
            </div>
            @php
                $shipperPreviewGroups = [
                    'Informasi Shipper' => [
                        'shipper' => 'Shipper', 'alamat_shipper' => 'Address (Shipper)',
                        'npwp_shipper' => 'NPWP Shipper',
                    ],
                    'Informasi Consignee' => [
                        'consignee' => 'Consignee', 'alamat_consignee' => 'Address (Consignee)',
                        'npwp_consignee' => 'NPWP Consignee',
                    ],
                    'Informasi Notify Party' => [
                        'notify_party_consignee' => 'Notify Party (Consignee)',
                        'alamat_notify_party_consignee' => 'Address (Notify Party)',
                        'npwp_notify_party_consignee' => 'NPWP Notify Party',
                    ],
                    'Informasi Lainnya' => [
                        'delivery_address_contact_person' => 'Delivery Address & Contact Person',
                        'document_ppftz_03' => 'Doc PPFTZ-03',
                        'condition' => 'Condition', 'status' => 'Status',
                    ],
                ];
            @endphp
            <div class="space-y-2" aria-label="Data Shipper / Consignee yang akan disimpan">
                @foreach($shipperPreviewGroups as $groupTitle => $fields)
                    <section class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                        <h3 class="mb-2 text-sm font-semibold text-gray-800">{{ $groupTitle }}</h3>
                        <div class="grid gap-2 text-sm sm:grid-cols-3">
                            @foreach($fields as $field => $label)
                                <div>
                                    <label for="approval-field-{{ $field }}" class="block text-xs font-medium text-gray-500">{{ $label }}</label>
                                    <button type="button" data-shipper-preview="{{ $field }}" class="approval-shipper-field-preview text-sm" aria-label="Ubah {{ $label }}" disabled>-</button>
                                    @if($field === 'status')
                                        <select id="approval-field-{{ $field }}" name="{{ $field }}" data-shipper-field="{{ $field }}" class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-2 py-2 text-sm text-gray-800 focus:border-indigo-500 focus:ring-indigo-500" disabled hidden>
                                            <option value="1">Aktif</option>
                                            <option value="0">Tidak aktif</option>
                                        </select>
                                    @elseif(in_array($field, ['alamat_shipper', 'alamat_consignee', 'alamat_notify_party_consignee', 'delivery_address_contact_person']))
                                        <textarea id="approval-field-{{ $field }}" name="{{ $field }}" data-shipper-field="{{ $field }}" rows="2" class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-2 py-2 text-sm text-gray-800 focus:border-indigo-500 focus:ring-indigo-500" disabled hidden></textarea>
                                    @else
                                        <input id="approval-field-{{ $field }}" name="{{ $field }}" data-shipper-field="{{ $field }}" type="text" maxlength="255" class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-2 py-2 text-sm text-gray-800 focus:border-indigo-500 focus:ring-indigo-500" {{ $field === 'shipper' ? 'required' : '' }} disabled hidden>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
            <p id="approval-shipper-help" class="text-xs text-gray-500">Klik data yang ingin diubah. Perubahan disimpan sebagai salinan untuk tanda terima dan manifest voyage JB terkait; data master asli tetap tersedia.</p>
        </div>
        <div id="approval-shipper-goods" class="approval-goods-dialog w-full border-t border-gray-200 px-5 py-5 sm:px-6" hidden>
            <h3 class="mb-2 text-base font-semibold text-gray-900">Detail Barang Manifest dan Perincian</h3>
            <p class="mb-4 text-xs text-gray-500">Isi barang manifest dan perincian dalam approval yang sama.</p>
        </div>
        <div class="flex justify-end gap-2 border-t border-gray-200 bg-gray-50 px-5 py-4 sm:px-6">
            <button type="button" class="approval-shipper-close rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Batal</button>
            <button id="approval-shipper-save" type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50" disabled>Simpan Shipper</button>
        </div>
    </form>
</dialog>
@endcan
@endsection

@push('scripts')
<script src="{{ asset('js/approval-tanda-terima-2.js') }}?v={{ substr(hash_file('sha256', public_path('js/approval-tanda-terima-2.js')), 0, 12) }}" defer></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const dialog = document.getElementById('approval-ttsj-dialog');
    if (!dialog) return;
    const openButton = document.getElementById('approval-ttsj-open');
    openButton?.addEventListener('click', () => dialog.showModal());
    dialog.querySelectorAll('[data-ttsj-close]').forEach(button => button.addEventListener('click', () => dialog.close()));
    if (dialog.dataset.reopen === 'true') dialog.showModal();

    const form = dialog.querySelector('form');
    const containerType = document.getElementById('approval-ttsj-container-type');
    const containerFields = document.getElementById('approval-ttsj-container-fields');
    const containerNumber = document.getElementById('approval-ttsj-container');
    function updateContainerFields() {
        const isFcl = containerType.value === 'fcl';
        containerFields.style.display = isFcl ? 'contents' : 'none';
        containerNumber.required = isFcl;
        containerFields.querySelectorAll('input').forEach(input => { input.disabled = !isFcl; });
    }
    containerType.addEventListener('change', updateContainerFields);
    updateContainerFields();
    const search = document.getElementById('approval-ttsj-shipper-search');
    const results = document.getElementById('approval-ttsj-shipper-results');
    const message = document.getElementById('approval-ttsj-shipper-message');
    const selectedId = document.getElementById('approval-ttsj-shipper-id');
    const shipperFields = form.querySelectorAll('[data-ttsj-shipper-field]');
    function updateGoodsSizeVisibility(prefix) {
        const name = form.elements.namedItem(`${prefix}[0][nama_barang]`);
        const size = form.elements.namedItem(`${prefix}[0][ukuran]`);
        const field = form.querySelector(`[data-ttsj-size-field="${prefix}"]`);
        if (field) field.hidden = !name.value.toLowerCase().includes('keramik') && !size.value.trim();
    }
    ['nama_barang', 'jumlah', 'satuan', 'ukuran'].forEach(field => {
        const manifestField = form.elements.namedItem(`manifest_goods[0][${field}]`);
        const perincianField = form.elements.namedItem(`goods[0][${field}]`);
        if (!manifestField || !perincianField) return;
        perincianField.addEventListener('input', () => {
            perincianField.dataset.edited = 'true';
            if (field === 'nama_barang') updateGoodsSizeVisibility('goods');
        });
        manifestField.addEventListener('input', () => {
            if (perincianField.dataset.edited !== 'true') perincianField.value = manifestField.value;
            if (field === 'nama_barang') {
                updateGoodsSizeVisibility('manifest_goods');
                updateGoodsSizeVisibility('goods');
            }
        });
    });
    updateGoodsSizeVisibility('manifest_goods');
    updateGoodsSizeVisibility('goods');
    let timer;
    let controller;

    function selectShipper(item, preserveEdits = false) {
        selectedId.value = item.real_id;
        shipperFields.forEach(field => {
            field.disabled = false;
            if (!preserveEdits) {
                field.value = field.name === 'status'
                    ? (item.status === 0 || item.status === '0' ? '0' : '1')
                    : (item[field.dataset.ttsjShipperField] ?? '');
            }
        });
        search.value = item.display_text;
        message.textContent = 'Data master dipilih. Periksa dan sesuaikan rincian di bawah.';
        results.hidden = true;
    }

    async function findShippers(id = null, preserveEdits = false) {
        controller?.abort();
        controller = new AbortController();
        const url = new URL(form.dataset.shipperSearchUrl, location.href);
        if (id) url.searchParams.set('id', id);
        else {
            url.searchParams.set('q', search.value.trim());
            url.searchParams.set('complete_pair', '1');
        }
        try {
            const response = await fetch(url, {signal: controller.signal});
            if (!response.ok) throw new Error('Pencarian master gagal. Coba lagi.');
            const items = await response.json();
            if (id) {
                const item = items.find(item => String(item.real_id) === String(id));
                if (item && item.shipper && item.consignee) selectShipper(item, preserveEdits);
                else {
                    selectedId.value = '';
                    message.textContent = 'Data master perlu memiliki shipper dan consignee.';
                }
                return;
            }
            results.replaceChildren();
            items.forEach(item => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'block w-full border-b border-gray-100 px-3 py-2 text-left text-sm hover:bg-indigo-50 focus:bg-indigo-50';
                button.textContent = item.display_text;
                button.addEventListener('click', () => selectShipper(item));
                results.append(button);
            });
            results.hidden = !results.childElementCount;
            message.textContent = results.hidden ? 'Pasangan shipper dan consignee tidak ditemukan.' : 'Pilih satu hasil pencarian.';
        } catch (error) {
            if (error.name !== 'AbortError') message.textContent = error.message;
        }
    }

    if (selectedId.value) findShippers(selectedId.value, true);
    search.addEventListener('input', () => {
        clearTimeout(timer);
        controller?.abort();
        selectedId.value = '';
        shipperFields.forEach(field => {
            field.value = '';
            field.disabled = true;
        });
        results.hidden = true;
        timer = setTimeout(() => findShippers(), 250);
    });
    search.addEventListener('focus', () => {
        if (!selectedId.value) findShippers();
    });
    form.addEventListener('submit', event => {
        if (selectedId.value) return;
        event.preventDefault();
        message.textContent = 'Pilih shipper dan consignee dari master terlebih dahulu.';
        search.focus();
    });
});
</script>
@endpush
