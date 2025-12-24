@extends('layouts.app')


@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'Tanda Terima')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tanda Terima</h1>
                <p class="text-gray-600 mt-1">Kelola tanda terima kontainer dari surat jalan yang sudah di-approve</p>
                <!-- bulkActionsContainer moved to table area; header doesn't need duplicate id -->
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('tanda-terima.select-surat-jalan') }}" 
                       class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg shadow-sm transition duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Tanda Terima
                    </a>
                    @can('tanda-terima-export')
                    <!-- Download filtered Excel (works for both tanda terima and missing surat jalan based on mode param) -->
                    <form id="downloadFilteredExcelForm" action="{{ route('tanda-terima.export.post') }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="mode" value="{{ $mode ?? request('mode') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <input type="hidden" name="kegiatan" value="{{ request('kegiatan') }}">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm transition duration-200">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Excel
                        </button>
                    </form>

                    <!-- Additional button specifically for missing surat jalan (still uses the same export route, with mode=missing) -->
                    @if(($mode ?? request('mode')) === 'missing')
                        <form id="downloadMissingSuratJalanExcelForm" action="{{ route('tanda-terima.export.post') }}" method="POST" style="display: inline; margin-left: 6px;">
                            @csrf
                            <input type="hidden" name="mode" value="missing">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="kegiatan" value="{{ request('kegiatan') }}">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg shadow-sm transition duration-200">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download Surat Jalan (Belum Ada TT)
                            </button>
                        </form>
                    <!-- Additional button to export combined Tanda Terima and missing Surat Jalan -->
                    <form id="downloadCombinedExcelForm" action="{{ route('tanda-terima.export.post') }}" method="POST" style="display: inline; margin-left: 6px;">
                        @csrf
                        <input type="hidden" name="mode" value="combined">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <input type="hidden" name="kegiatan" value="{{ request('kegiatan') }}">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition duration-200">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Gabungan (TT + SJ Belum TT)
                        </button>
                    </form>
                    @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(!empty($fallback_missing))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle mr-2 text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-800">Tidak ditemukan pada Data Tanda Terima; menampilkan hasil dari Surat Jalan (Belum Ada Tanda Terima).</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">
                @if(($mode ?? request('mode')) === 'missing')
                    Surat Jalan (Belum Ada Tanda Terima - Hanya yang Sudah Bayar Uang Jalan)
                @elseif(($mode ?? request('mode')) === 'with_tanda_terima')
                    Surat Jalan (Sudah Ada Tanda Terima)
                @else
                    Daftar Tanda Terima
                @endif
            </h2>
            @if(($mode ?? request('mode')) === 'missing')
            <div class="mt-2">
                <p class="text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Menampilkan surat jalan yang belum ada tanda terima dan sudah melakukan pembayaran pranota uang jalan.
                </p>
            </div>
            @endif
        </div>

        <div class="p-6">
            <!-- Filter & Search -->
            <form method="GET" action="{{ route('tanda-terima.index') }}" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-3">
                        <input type="text"
                               name="search"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Cari no. surat jalan, kontainer, kapal..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="md:col-span-3">
                        <select name="mode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="" {{ ($mode ?? request('mode')) == '' ? 'selected' : '' }}>Daftar Tanda Terima</option>
                            <option value="missing" {{ ($mode ?? request('mode')) == 'missing' ? 'selected' : '' }}>Surat Jalan Belum Ada Tanda Terima</option>
                            <option value="with_tanda_terima" {{ ($mode ?? request('mode')) == 'with_tanda_terima' ? 'selected' : '' }}>Surat Jalan Sudah Ada Tanda Terima</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <select name="kegiatan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua Kegiatan</option>
                            @foreach($kegiatanList as $kegiatanItem)
                                <option value="{{ $kegiatanItem }}" {{ request('kegiatan') == $kegiatanItem ? 'selected' : '' }}>
                                    {{ $kegiatanItem }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-1.5">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-search mr-2"></i> Cari
                        </button>
                    </div>
                    <div class="md:col-span-1.5">
                        <a href="{{ route('tanda-terima.index') }}" class="block text-center w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-redo mr-2"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Bulk Actions -->
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" onchange="toggleAllCheckboxes()">
                        <span class="ml-2 text-sm text-gray-700">Pilih Semua</span>
                    </label>
                    <span id="selectedCount" class="text-sm text-gray-500">@if(($mode ?? request('mode')) === 'missing') 0 surat jalan dipilih @else 0 tanda terima dipilih @endif</span>
                </div>

                <!-- Bulk Delete Button -->
                @if(($mode ?? request('mode')) !== 'missing')
                <div id="bulkActionsContainer" class="hidden">
                    <div class="flex items-center gap-2">
                    <button type="button"
                            onclick="bulkExportExcel()"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Excel
                    </button>
                    <button type="button"
                            onclick="bulkDelete()"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus Terpilih
                    </button>
                    </div>
                </div>
                @endif
            </div>

            <!-- Bulk Delete Form (Hidden) -->
            <form id="bulkDeleteForm" action="{{ route('tanda-terima.bulk-delete') }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
                <input type="hidden" name="tanda_terima_ids" id="bulkDeleteIds">
            </form>

            <!-- Bulk Export Excel Form (Hidden) -->
            <form id="bulkExportForm" action="{{ route('tanda-terima.export-excel') }}" method="POST" style="display: none;">
                @csrf
                <input type="hidden" name="tanda_terima_ids" id="bulkExportIds">
            </form>

            <!-- Table -->
            <div class="overflow-x-auto">
                @if(($mode ?? request('mode')) === 'missing')
                    <table class="min-w-full divide-y divide-gray-200 text-sm resizable-table" id="suratJalanTable">
                @elseif(($mode ?? request('mode')) === 'with_tanda_terima')
                <table class="min-w-full divide-y divide-gray-200 text-sm resizable-table" id="suratJalanWithTandaTerimaTable">
                @else
                <table class="min-w-full divide-y divide-gray-200 text-sm resizable-table" id="tandaTerimaTable">
                @endif
                    <thead class="bg-gray-50">
                        @if(($mode ?? request('mode')) === 'missing')
                        <tr>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 48px;">
                                <input type="checkbox" id="selectAllHeader" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" onchange="toggleAllCheckboxes()">
                            </th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Plat</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rit</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Uang Jalan</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Uang Jalan</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                            <th class="resizable-th px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                        @elseif(($mode ?? request('mode')) === 'with_tanda_terima')
                        <tr>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 48px;">
                                <input type="checkbox" id="selectAllHeader" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" onchange="toggleAllCheckboxes()">
                            </th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal SJ</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Plat</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Uang Jalan</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Uang Jalan</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Tanda Terima</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                            <th class="resizable-th px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                        @else
                        <tr>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 48px;">
                                <input type="checkbox" id="selectAllHeader" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" onchange="toggleAllCheckboxes()">
                            </th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Barang</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan Kirim</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Uang Jalan</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="resizable-th px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                        @endif
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if(($mode ?? request('mode')) === 'missing')
                        @forelse($suratJalans as $suratJalan)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-3 py-2 whitespace-nowrap">
                                <input type="checkbox"
                                       class="surat-jalan-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                       value="{{ $suratJalan->id }}"
                                       data-no-surat-jalan="{{ $suratJalan->no_surat_jalan }}"
                                       onchange="updateSelection()">
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 text-center">
                                {{ ($suratJalans->currentPage() - 1) * $suratJalans->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    <span class="text-xs font-semibold text-gray-900">{{ $suratJalan->no_surat_jalan }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                                {{ $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('d/M/Y') : '-' }}
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                <code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded">{{ $suratJalan->no_kontainer ?: '-' }}</code>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">{{ $suratJalan->supir ?: '-' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">{{ $suratJalan->no_plat ?: '-' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">{{ $suratJalan->rit ?: '-' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                                <code class="text-xs bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded">{{ $suratJalan->uangJalan->nomor_uang_jalan ?? '-' }}</code>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                                {{ $suratJalan->uangJalan && $suratJalan->uangJalan->tanggal_uang_jalan ? \Carbon\Carbon::parse($suratJalan->uangJalan->tanggal_uang_jalan)->format('d/M/Y') : '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ Str::limit($suratJalan->kegiatan ?: '-', 12) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <a href="{{ route('tanda-terima.create', ['surat_jalan_id' => $suratJalan->id]) }}"
                                   class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded transition duration-150">
                                    <i class="fas fa-plus mr-1"></i> Buat
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="13" class="px-3 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-truck-loading text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-gray-500 text-base font-medium">Tidak ada surat jalan tanpa tanda terima</p>
                                    <p class="text-gray-400 text-xs mt-1">Semua surat jalan saat ini memiliki tanda terima.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                        @elseif(($mode ?? request('mode')) === 'with_tanda_terima')
                        @forelse($suratJalansWithTandaTerima as $item)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-3 py-2 whitespace-nowrap">
                                <input type="checkbox"
                                       class="surat-jalan-with-tt-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                       value="{{ $item->surat_jalan_id }}"
                                       data-no-surat-jalan="{{ $item->no_surat_jalan }}"
                                       onchange="updateSelection()">
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 text-center">
                                {{ ($suratJalansWithTandaTerima->currentPage() - 1) * $suratJalansWithTandaTerima->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    <span class="text-xs font-semibold text-gray-900">{{ $item->no_surat_jalan }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                                {{ $item->tanggal_surat_jalan ? \Carbon\Carbon::parse($item->tanggal_surat_jalan)->format('d/M/Y') : '-' }}
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                <code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded">{{ $item->no_kontainer ?: '-' }}</code>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">{{ $item->supir ?: '-' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                                <code class="text-xs bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded">{{ $item->nomor_uang_jalan ?? '-' }}</code>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                                {{ $item->tanggal_uang_jalan ? \Carbon\Carbon::parse($item->tanggal_uang_jalan)->format('d/M/Y') : '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <code class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded font-semibold">TT-{{ $item->tanda_terima_id }}</code>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ Str::limit($item->kegiatan ?: '-', 12) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('tanda-terima.show', $item->tanda_terima_id) }}"
                                       class="inline-flex items-center px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded transition duration-150"
                                       title="Lihat Tanda Terima">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('tanda-terima.edit', $item->tanda_terima_id) }}"
                                       class="inline-flex items-center px-2 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded transition duration-150"
                                       title="Edit Tanda Terima">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="13" class="px-3 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-receipt text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-gray-500 text-base font-medium">Tidak ada surat jalan dengan tanda terima</p>
                                    <p class="text-gray-400 text-xs mt-1">Belum ada surat jalan yang memiliki tanda terima.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                        @else
                        @forelse($tandaTerimas as $tandaTerima)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-3 py-2 whitespace-nowrap">
                                <input type="checkbox"
                                       class="tanda-terima-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                       value="{{ $tandaTerima->id }}"
                                       data-no-surat-jalan="{{ $tandaTerima->no_surat_jalan }}"
                                       onchange="updateSelection()">
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 text-center">
                                {{ ($tandaTerimas->currentPage() - 1) * $tandaTerimas->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    <span class="text-xs font-semibold text-gray-900">{{ $tandaTerima->no_surat_jalan }}</span>
                                    @if(!$tandaTerima->surat_jalan_id)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                            Manual
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                                {{ $tandaTerima->tanggal_checkpoint_supir ? $tandaTerima->tanggal_checkpoint_supir->format('d/M/Y') : '-' }}
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                <code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded">{{ $tandaTerima->no_kontainer ?: '-' }}</code>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ Str::limit($tandaTerima->pengirim ?: '-', 20) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ Str::limit($tandaTerima->jenis_barang ?: '-', 12) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                @php
                                    $namaBarang = $tandaTerima->nama_barang;
                                    
                                    // Coba ambil dari dimensi_items atau dimensi_details jika nama_barang kosong
                                    if (!$namaBarang) {
                                        $dimensiItems = [];
                                        
                                        if ($tandaTerima->dimensi_items) {
                                            $dimensiItems = is_string($tandaTerima->dimensi_items) ? json_decode($tandaTerima->dimensi_items, true) : $tandaTerima->dimensi_items;
                                        } elseif ($tandaTerima->dimensi_details) {
                                            $dimensiItems = is_string($tandaTerima->dimensi_details) ? json_decode($tandaTerima->dimensi_details, true) : $tandaTerima->dimensi_details;
                                        }
                                        
                                        if (is_array($dimensiItems) && count($dimensiItems) > 0 && isset($dimensiItems[0]['nama_barang'])) {
                                            $namaBarang = $dimensiItems[0]['nama_barang'];
                                            if (count($dimensiItems) > 1) {
                                                $namaBarang .= ' (+' . (count($dimensiItems) - 1) . ' lainnya)';
                                            }
                                        }
                                    }
                                @endphp
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                                    {{ Str::limit($namaBarang ?: '-', 20) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                <div class="max-w-[150px] truncate" title="{{ $tandaTerima->tujuan_pengiriman ?: 'Tidak ada tujuan kirim' }}">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        {{ Str::limit($tandaTerima->tujuan_pengiriman ?: 'Tidak ada tujuan kirim', 20) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                                {{ $tandaTerima->suratJalan && $tandaTerima->suratJalan->uangJalan && $tandaTerima->suratJalan->uangJalan->tanggal_uang_jalan ? \Carbon\Carbon::parse($tandaTerima->suratJalan->uangJalan->tanggal_uang_jalan)->format('d/M/Y') : '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                                @php
                                    $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $tandaTerima->kegiatan)
                                                    ->value('nama_kegiatan') ?? $tandaTerima->kegiatan;
                                @endphp
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ Str::limit($kegiatanName, 12) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                @if($tandaTerima->status == 'completed')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check w-2 h-2 mr-1"></i>
                                        Done
                                    </span>
                                @elseif($tandaTerima->status == 'submitted')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-paper-plane w-2 h-2 mr-1"></i>
                                        Submit
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-edit w-2 h-2 mr-1"></i>
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center text-xs font-medium">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('tanda-terima.show', $tandaTerima->id) }}"
                                       class="inline-flex items-center px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded transition duration-150"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('tanda-terima.edit', $tandaTerima->id) }}"
                                       class="inline-flex items-center px-2 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded transition duration-150"
                                       title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <button type="button"
                                            onclick="addToProspek('{{ $tandaTerima->id }}', '{{ $tandaTerima->no_surat_jalan }}', '{{ $tandaTerima->no_kontainer }}')"
                                            class="inline-flex items-center px-2 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded transition duration-150"
                                            title="Masukan ke Prospek">
                                        <i class="fas fa-ship text-xs"></i>
                                    </button>
                                    
                                    <!-- Dropdown for additional actions -->
                                    <div class="relative inline-block text-left">
                                        <button type="button" 
                                                class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition duration-150"
                                                onclick="toggleDropdown('dropdown-{{ $tandaTerima->id }}')"
                                                title="Aksi Lainnya">
                                            <i class="fas fa-ellipsis-v text-xs"></i>
                                        </button>
                                        <div id="dropdown-{{ $tandaTerima->id }}" 
                                             class="hidden absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                            <div class="py-1">
                                                <button type="button"
                                                        onclick="addToProspek('{{ $tandaTerima->id }}', '{{ $tandaTerima->no_surat_jalan }}', '{{ $tandaTerima->no_kontainer }}')"
                                                        class="block w-full px-4 py-2 text-left text-xs text-emerald-600 hover:bg-emerald-50">
                                                    <i class="fas fa-ship mr-2"></i>Ke Prospek
                                                </button>
                                                <button type="button"
                                                        onclick="openChangeContainerModal('{{ $tandaTerima->id }}', '{{ $tandaTerima->no_surat_jalan }}', '{{ $tandaTerima->no_kontainer }}', '{{ $tandaTerima->no_seal }}')"
                                                        class="block w-full px-4 py-2 text-left text-xs text-blue-600 hover:bg-blue-50">
                                                    <i class="fas fa-exchange-alt mr-2"></i>Ganti Nomor Kontainer & Seal
                                                </button>
                                                <button type="button"
                                                        onclick="showAuditLog('{{ get_class($tandaTerima) }}', '{{ $tandaTerima->id }}', 'TT-{{ $tandaTerima->id }}')"
                                                        class="block w-full px-4 py-2 text-left text-xs text-purple-600 hover:bg-purple-50">
                                                    <i class="fas fa-history mr-2"></i>Riwayat
                                                </button>
                                                <form action="{{ route('tanda-terima.destroy', $tandaTerima->id) }}"
                                                      method="POST"
                                                      class="block"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanda terima ini?\n\nNo. Surat Jalan: {{ $tandaTerima->no_surat_jalan }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="block w-full px-4 py-2 text-left text-xs text-red-600 hover:bg-red-50">
                                                        <i class="fas fa-trash mr-2"></i>Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="px-3 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-receipt text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-gray-500 text-base font-medium">Tidak ada data tanda terima</p>
                                    <p class="text-gray-400 text-xs mt-1">Tanda terima akan otomatis dibuat setelah surat jalan di-approve</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(($mode ?? request('mode')) === 'missing')
                @if(isset($suratJalans) && $suratJalans->hasPages())
                <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-4">
                    <div class="flex flex-1 justify-between sm:hidden">
                        @if($suratJalans->onFirstPage())
                            <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-400">
                                Previous
                            </span>
                        @else
                            <a href="{{ $suratJalans->previousPageUrl() }}" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Previous
                            </a>
                        @endif

                        @if($suratJalans->hasMorePages())
                            <a href="{{ $suratJalans->nextPageUrl() }}" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Next
                            </a>
                        @else
                            <span class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-400">
                                Next
                            </span>
                        @endif
                    </div>
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Menampilkan
                                <span class="font-medium">{{ $suratJalans->firstItem() ?? 0 }}</span>
                                sampai
                                <span class="font-medium">{{ $suratJalans->lastItem() ?? 0 }}</span>
                                dari
                                <span class="font-medium">{{ $suratJalans->total() }}</span>
                                data
                            </p>
                        </div>
                        <div>
                            @include('components.modern-pagination', ['paginator' => $suratJalans])
                            @include('components.rows-per-page')
                        </div>
                    </div>
                </div>
                @endif
            @elseif(($mode ?? request('mode')) === 'with_tanda_terima')
                @if(isset($suratJalansWithTandaTerima) && $suratJalansWithTandaTerima->hasPages())
                <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-4">
                    <div class="flex flex-1 justify-between sm:hidden">
                        @if($suratJalansWithTandaTerima->onFirstPage())
                            <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-400">
                                Previous
                            </span>
                        @else
                            <a href="{{ $suratJalansWithTandaTerima->previousPageUrl() }}" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Previous
                            </a>
                        @endif

                        @if($suratJalansWithTandaTerima->hasMorePages())
                            <a href="{{ $suratJalansWithTandaTerima->nextPageUrl() }}" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Next
                            </a>
                        @else
                            <span class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-400">
                                Next
                            </span>
                        @endif
                    </div>
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Menampilkan
                                <span class="font-medium">{{ $suratJalansWithTandaTerima->firstItem() ?? 0 }}</span>
                                sampai
                                <span class="font-medium">{{ $suratJalansWithTandaTerima->lastItem() ?? 0 }}</span>
                                dari
                                <span class="font-medium">{{ $suratJalansWithTandaTerima->total() }}</span>
                                data
                            </p>
                        </div>
                        <div>
                            @include('components.modern-pagination', ['paginator' => $suratJalansWithTandaTerima])
                            @include('components.rows-per-page')
                        </div>
                    </div>
                </div>
                @endif
            @else
                @if($tandaTerimas->hasPages())
                <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-4">
                    <div class="flex flex-1 justify-between sm:hidden">
                        @if($tandaTerimas->onFirstPage())
                            <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-400">
                                Previous
                            </span>
                        @else
                            <a href="{{ $tandaTerimas->previousPageUrl() }}" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Previous
                            </a>
                        @endif

                        @if($tandaTerimas->hasMorePages())
                            <a href="{{ $tandaTerimas->nextPageUrl() }}" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Next
                            </a>
                        @else
                            <span class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-400">
                                Next
                            </span>
                        @endif
                    </div>
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Menampilkan
                                <span class="font-medium">{{ $tandaTerimas->firstItem() ?? 0 }}</span>
                                sampai
                                <span class="font-medium">{{ $tandaTerimas->lastItem() ?? 0 }}</span>
                                dari
                                <span class="font-medium">{{ $tandaTerimas->total() }}</span>
                                data
                            </p>
                        </div>
                        <div>
                            @include('components.modern-pagination', ['paginator' => $tandaTerimas])
                            @include('components.rows-per-page')
                        </div>
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

    // Checkbox functionality
    function toggleAllCheckboxes() {
        const selectAll = document.getElementById('selectAll');
        const selectAllHeader = document.getElementById('selectAllHeader');
        const checkboxes = document.querySelectorAll('.tanda-terima-checkbox, .surat-jalan-checkbox, .surat-jalan-with-tt-checkbox');

        // Sync both select all checkboxes
        if (selectAll.checked) {
            selectAllHeader.checked = true;
        } else {
            selectAllHeader.checked = false;
        }

        // If triggered from header checkbox, sync with sidebar checkbox
        if (event.target.id === 'selectAllHeader') {
            selectAll.checked = selectAllHeader.checked;
        }

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });

        updateSelection();
    }

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.tanda-terima-checkbox:checked, .surat-jalan-checkbox:checked, .surat-jalan-with-tt-checkbox:checked');
        const selectedCount = checkboxes.length;

        // Update count display
        const mode = '{{ $mode ?? request('mode') }}';
        let selectionText = `${selectedCount} dipilih`;
        if (selectedCount > 0) {
            if (mode === 'missing') {
                selectionText = `${selectedCount} surat jalan dipilih`;
            } else if (mode === 'with_tanda_terima') {
                selectionText = `${selectedCount} surat jalan dipilih`;
            } else {
                selectionText = `${selectedCount} tanda terima dipilih`;
            }
        } else {
            if (mode === 'missing') {
                selectionText = '0 surat jalan dipilih';
            } else if (mode === 'with_tanda_terima') {
                selectionText = '0 surat jalan dipilih';
            } else {
                selectionText = '0 tanda terima dipilih';
            }
        }
        document.getElementById('selectedCount').textContent = selectionText;

        // Show/hide bulk actions container
        const bulkActionsContainer = document.getElementById('bulkActionsContainer');
        if (bulkActionsContainer) {
            if (selectedCount > 0) {
                bulkActionsContainer.classList.remove('hidden');
            } else {
                bulkActionsContainer.classList.add('hidden');
            }
        }

        // Update select all checkboxes
        const allCheckboxes = document.querySelectorAll('.tanda-terima-checkbox, .surat-jalan-checkbox, .surat-jalan-with-tt-checkbox');
        const selectAll = document.getElementById('selectAll');
        const selectAllHeader = document.getElementById('selectAllHeader');

        if (selectedCount === 0) {
            selectAll.indeterminate = false;
            selectAll.checked = false;
            selectAllHeader.indeterminate = false;
            selectAllHeader.checked = false;
        } else if (selectedCount === allCheckboxes.length) {
            selectAll.indeterminate = false;
            selectAll.checked = true;
            selectAllHeader.indeterminate = false;
            selectAllHeader.checked = true;
        } else {
            selectAll.indeterminate = true;
            selectAll.checked = false;
            selectAllHeader.indeterminate = true;
            selectAllHeader.checked = false;
        }
    }

    // Bulk export to Excel function
    function bulkExportExcel() {
        const mode = '{{ $mode ?? request('mode') }}';
        let checkboxes;
        let message;
        let ids;

        if (mode === 'with_tanda_terima') {
            checkboxes = document.querySelectorAll('.surat-jalan-with-tt-checkbox:checked');
            message = 'Pilih minimal 1 surat jalan untuk di-export.';
            ids = Array.from(checkboxes).map(cb => cb.value); // tanda_terima_id
        } else {
            checkboxes = document.querySelectorAll('.tanda-terima-checkbox:checked');
            message = 'Pilih minimal 1 tanda terima untuk di-export.';
            ids = Array.from(checkboxes).map(cb => cb.value); // tanda_terima_id
        }

        const selectedCount = checkboxes.length;

        if (selectedCount === 0) {
            alert(message);
            return;
        }

        document.getElementById('bulkExportIds').value = JSON.stringify(ids);
        document.getElementById('bulkExportForm').submit();
    }

    // Bulk delete function
    function bulkDelete() {
        const mode = '{{ $mode ?? request('mode') }}';
        let checkboxes;
        let message;
        let ids;
        let noSuratJalans;

        if (mode === 'with_tanda_terima') {
            checkboxes = document.querySelectorAll('.surat-jalan-with-tt-checkbox:checked');
            message = 'Pilih minimal 1 surat jalan untuk dihapus.';
            ids = Array.from(checkboxes).map(cb => cb.value); // tanda_terima_id
            noSuratJalans = Array.from(checkboxes).map(cb => cb.dataset.noSuratJalan).join(', ');
        } else {
            checkboxes = document.querySelectorAll('.tanda-terima-checkbox:checked');
            message = 'Pilih minimal 1 tanda terima untuk dihapus.';
            ids = Array.from(checkboxes).map(cb => cb.value); // tanda_terima_id
            noSuratJalans = Array.from(checkboxes).map(cb => cb.dataset.noSuratJalan).join(', ');
        }

        const selectedCount = checkboxes.length;

        if (selectedCount === 0) {
            alert(message);
            return;
        }

        const confirmMessage = `Apakah Anda yakin ingin menghapus ${selectedCount} tanda terima?\n\nNo. Surat Jalan:\n${noSuratJalans}\n\nTindakan ini tidak dapat dibatalkan!`;

        if (confirm(confirmMessage)) {
            document.getElementById('bulkDeleteIds').value = JSON.stringify(ids);
            document.getElementById('bulkDeleteForm').submit();
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Add change listeners to existing checkboxes (all types)
        document.querySelectorAll('.tanda-terima-checkbox, .surat-jalan-checkbox, .surat-jalan-with-tt-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelection);
        });

        // Initial update
        updateSelection();
    });

    // Function to toggle dropdown
    function toggleDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
        
        // Close all other dropdowns
        allDropdowns.forEach(d => {
            if (d.id !== dropdownId) {
                d.classList.add('hidden');
            }
        });
        
        // Toggle current dropdown
        dropdown.classList.toggle('hidden');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('[onclick*="toggleDropdown"]') && !event.target.closest('[id^="dropdown-"]')) {
            const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
            allDropdowns.forEach(d => d.classList.add('hidden'));
        }
    });

    // Function to add cargo container to prospek
    function addToProspek(tandaTerimaId, noSuratJalan, noKontainer) {
        // Build confirmation message
        let message = `Apakah Anda yakin ingin memasukkan data dari surat jalan ${noSuratJalan}`;
        if (noKontainer && noKontainer !== '-') {
            message += ` (Kontainer: ${noKontainer})`;
        }
        message += ` ke dalam prospek?`;
        
        if (confirm(message)) {
            // Show loading indicator
            const button = event.target.closest('button');
            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i>';
            }
            
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("tanda-terima.add-to-prospek", ":id") }}'.replace(':id', tandaTerimaId);
            
            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfInput);
            
            // Add method
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'POST';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Function to open change container modal
    function openChangeContainerModal(tandaTerimaId, noSuratJalan, noKontainer, noSeal) {
        // Set form action
        const form = document.getElementById('changeContainerForm');
        form.action = '{{ route("tanda-terima.update", ":id") }}'.replace(':id', tandaTerimaId);
        
        // Fill modal fields
        document.getElementById('modalNoSuratJalan').value = noSuratJalan || '-';
        document.getElementById('modalOldKontainer').value = noKontainer || '-';
        document.getElementById('modalOldSeal').value = noSeal || '-';
        document.getElementById('newKontainer').value = '';
        document.getElementById('newSeal').value = '';
        
        // Show modal
        document.getElementById('changeContainerModal').classList.remove('hidden');
        
        // Focus on input
        setTimeout(() => {
            document.getElementById('newKontainer').focus();
        }, 100);
        
        // Close dropdowns
        const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
        allDropdowns.forEach(d => d.classList.add('hidden'));
    }

    // Function to close change container modal
    function closeChangeContainerModal() {
        document.getElementById('changeContainerModal').classList.add('hidden');
        document.getElementById('changeContainerForm').reset();
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('changeContainerModal');
        if (event.target === modal) {
            closeChangeContainerModal();
        }
    });

    // Close modal with ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('changeContainerModal');
            if (!modal.classList.contains('hidden')) {
                closeChangeContainerModal();
            }
        }
    });
</script>
@endpush

<!-- Audit Log Modal -->
@include('components.audit-log-modal')

<!-- Change Container Number Modal -->
<div id="changeContainerModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-semibold text-gray-900">
                <i class="fas fa-exchange-alt mr-2 text-blue-600"></i>
                Ganti Nomor Kontainer & Seal
            </h3>
            <button onclick="closeChangeContainerModal()" class="text-gray-400 hover:text-gray-600 transition duration-150">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <form id="changeContainerForm" method="POST" action="" class="mt-4">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    No. Surat Jalan
                </label>
                <input type="text" id="modalNoSuratJalan" readonly
                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Kontainer Lama
                </label>
                <input type="text" id="modalOldKontainer" readonly
                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700">
            </div>
            
            <div class="mb-4">
                <label for="newKontainer" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Kontainer Baru <span class="text-red-500">*</span>
                </label>
                <input type="text" id="newKontainer" name="nomor_kontainer[]" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Masukkan nomor kontainer baru">
                <p class="mt-1 text-xs text-gray-500">Masukkan nomor kontainer yang baru untuk mengganti nomor kontainer lama</p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Seal Lama
                </label>
                <input type="text" id="modalOldSeal" readonly
                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700">
            </div>
            
            <div class="mb-6">
                <label for="newSeal" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Seal Baru
                </label>
                <input type="text" id="newSeal" name="no_seal[]" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Masukkan nomor seal baru (opsional)">
                <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah nomor seal</p>
            </div>
            
            <!-- Warning Info Box -->
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-semibold text-blue-800 mb-1">Perubahan akan mempengaruhi:</h4>
                        <ul class="text-xs text-blue-700 space-y-1">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-500 mr-2 mt-0.5"></i>
                                <span>Nomor kontainer & seal di <strong>Tanda Terima</strong></span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-500 mr-2 mt-0.5"></i>
                                <span>Nomor kontainer & seal di <strong>Surat Jalan</strong> terkait</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-500 mr-2 mt-0.5"></i>
                                <span>Nomor kontainer & seal di <strong>Prospek</strong> yang terhubung</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-3 border-t">
                <button type="button" onclick="closeChangeContainerModal()"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition duration-150">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-150">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@include('components.resizable-table')

@push('scripts')
<script>
$(document).ready(function() {
    if ('{{ request('mode') }}' === 'missing') {
        initResizableTable('suratJalanTable');
    } else if ('{{ request('mode') }}' === 'with_tanda_terima') {
        initResizableTable('suratJalanWithTandaTerimaTable');
    } else {
        initResizableTable('tandaTerimaTable');
    }
});
</script>
@endpush