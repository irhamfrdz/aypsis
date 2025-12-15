@extends('layouts.app')

@section('title', 'Stuffing Kontainer LCL')
@section('page_title', 'Stuffing Kontainer LCL')

@push('styles')
<link href="https://unpkg.com/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px;
        color: #111827;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 8px;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Stuffing Kontainer LCL</h1>
                    <p class="text-xs text-gray-600 mt-1">Proses memasukkan tanda terima LCL ke dalam kontainer</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-center">
                        <div class="text-lg font-semibold text-blue-600">{{ $stats['total_containers'] ?? 0 }}</div>
                        <div class="text-gray-500 text-xs">Total Kontainer</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-semibold text-green-600">{{ $stats['total_lcl_stuffed'] ?? 0 }}</div>
                        <div class="text-gray-500 text-xs">LCL Sudah Stuffing</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-semibold text-orange-600">{{ $stats['total_lcl_unstuffed'] ?? 0 }}</div>
                        <div class="text-gray-500 text-xs">LCL Belum Stuffing</div>
                    </div>
                    <a href="{{ route('tanda-terima-tanpa-surat-jalan.index', ['tipe' => 'lcl']) }}" 
                       style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #4b5563; color: #ffffff; border-radius: 0.5rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);"
                       onmouseover="this.style.backgroundColor='#374151'" 
                       onmouseout="this.style.backgroundColor='#4b5563'"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>

            <!-- Alerts -->
            <div class="p-4">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium text-sm">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 rounded-md" role="alert">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="font-medium text-sm mb-1">Peringatan Proses Stuffing</p>
                                <p class="text-sm whitespace-pre-line">{{ session('warning') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="font-medium text-sm mb-1">Gagal Melakukan Stuffing</p>
                                <p class="text-sm whitespace-pre-line">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="font-medium text-sm mb-2">Validasi Error:</p>
                                <ul class="list-disc list-inside text-sm space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Filter Form -->
                <form method="GET" action="{{ route('tanda-terima-lcl.stuffing') }}" class="mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nomor kontainer, tanda terima, penerima..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Filter Kontainer</label>
                            <select name="kontainer" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Kontainer</option>
                                @foreach($uniqueContainers as $kontainer)
                                    <option value="{{ $kontainer }}" {{ request('kontainer') == $kontainer ? 'selected' : '' }}>
                                        {{ $kontainer }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" 
                                    style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #2563eb; color: #ffffff; border-radius: 0.375rem; font-size: 0.875rem; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);"
                                    onmouseover="this.style.backgroundColor='#1d4ed8'" 
                                    onmouseout="this.style.backgroundColor='#2563eb'"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Cari
                            </button>
                            <a href="{{ route('tanda-terima-lcl.stuffing') }}" 
                               style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #4b5563; color: #ffffff; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);"
                               onmouseover="this.style.backgroundColor='#374151'" 
                               onmouseout="this.style.backgroundColor='#4b5563'"
                               class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                                Reset
                            </a>
                            <button type="button" onclick="showUnstuffedModal()" 
                                    style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #0d9488; color: #ffffff; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; margin-left: auto; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: all 0.2s;"
                                    onmouseover="this.style.backgroundColor='#0f766e'" 
                                    onmouseout="this.style.backgroundColor='#0d9488'"
                                    class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 text-sm ml-auto">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Stuffing
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table - Display Pivot Data Grouped by Container -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="overflow-x-auto">
                @forelse($groupedByContainer as $container)
                    <div class="border-b border-gray-200 last:border-b-0">
                        <!-- Container Header -->
                        <div class="bg-gray-50 px-6 py-4 flex items-center justify-between cursor-pointer hover:bg-gray-100 transition" onclick="toggleContainer('container-{{ $loop->index }}')">
                            <div class="flex items-center gap-6">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $container['nomor_kontainer'] }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $container['size_kontainer'] ?? '-' }}
                                        @if($container['tipe_kontainer'])
                                            | {{ $container['tipe_kontainer'] }}
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $container['total_lcl'] }} LCL
                                        </span>
                                    </div>
                                    <div class="text-gray-600">
                                        <span class="font-medium">Volume:</span> {{ number_format($container['total_volume'], 2) }} m³
                                    </div>
                                    <div class="text-gray-600">
                                        <span class="font-medium">Berat:</span> {{ number_format($container['total_berat'], 2) }} ton
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                @php
                                    $firstPivot = $container['items']->first();
                                    $hasSealed = $firstPivot && $firstPivot->nomor_seal;
                                @endphp
                                @if($hasSealed)
                                    <div class="text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Seal: {{ $firstPivot->nomor_seal }}
                                        </span>
                                    </div>
                                @else
                                    <button type="button" onclick="showSealModal('{{ $container['nomor_kontainer'] }}', event)" 
                                            style="display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; background-color: #d97706; color: #ffffff; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; border: none; cursor: pointer; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: all 0.2s;"
                                            onmouseover="this.style.backgroundColor='#b45309'" 
                                            onmouseout="this.style.backgroundColor='#d97706'"
                                            class="inline-flex items-center px-3 py-1.5 bg-amber-600 text-white rounded-md hover:bg-amber-700 text-xs font-medium transition">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        Seal Kontainer
                                    </button>
                                @endif
                                <svg class="w-5 h-5 text-gray-400 transition-transform" id="arrow-container-{{ $loop->index }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Container Details (Collapsible) -->
                        <div id="container-{{ $loop->index }}" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Tanda Terima LCL</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volume (m³)</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berat (ton)</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Stuffing</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oleh</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($container['items'] as $pivot)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-3">
                                                @if($pivot->tandaTerima)
                                                    <div class="text-sm font-medium text-gray-900">{{ $pivot->tandaTerima->nomor_tanda_terima ?? 'TT-LCL-' . $pivot->tandaTerima->id }}</div>
                                                @else
                                                    <span class="text-xs text-gray-400">Data tidak tersedia</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-3">
                                                @if($pivot->tandaTerima)
                                                    <div class="text-sm text-gray-900">{{ $pivot->tandaTerima->nama_penerima ?? '-' }}</div>
                                                @else
                                                    <span class="text-xs text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-3">
                                                @if($pivot->tandaTerima)
                                                    <div class="text-sm text-gray-900">{{ $pivot->tandaTerima->nama_pengirim ?? '-' }}</div>
                                                @else
                                                    <span class="text-xs text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-3">
                                                @if($pivot->tandaTerima && $pivot->tandaTerima->items)
                                                    <div class="text-sm text-gray-900">{{ number_format($pivot->tandaTerima->items->sum('meter_kubik'), 3) }}</div>
                                                @else
                                                    <span class="text-xs text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-3">
                                                @if($pivot->tandaTerima && $pivot->tandaTerima->items)
                                                    <div class="text-sm text-gray-900">{{ number_format($pivot->tandaTerima->items->sum('tonase'), 3) }}</div>
                                                @else
                                                    <span class="text-xs text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-3">
                                                <div class="text-sm text-gray-900">{{ $pivot->assigned_at ? $pivot->assigned_at->format('d/m/Y H:i') : '-' }}</div>
                                            </td>
                                            <td class="px-6 py-3">
                                                <div class="text-sm text-gray-900">{{ $pivot->assignedByUser->name ?? '-' }}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <p class="text-sm">Belum ada data stuffing</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Stuffing Modal - Add New Stuffing -->
<div id="stuffingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4 border-b pb-3">
                <h3 class="text-lg font-medium text-gray-900">Tambah Stuffing Baru - Pilih LCL</h3>
                <button type="button" onclick="closeStuffingModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Unstuffed LCL Table with Checkboxes -->
            <div class="mb-4">
                <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mb-4">
                    <p class="text-sm text-blue-800">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pilih LCL yang akan di-stuffing ke dalam kontainer yang sama
                    </p>
                </div>

                <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" id="selectAllUnstuffed" class="rounded border-gray-300 text-teal-600">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Tanda Terima</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penerima</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengirim</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volume</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Berat</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($unstuffedLcl as $lcl)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <input type="checkbox" class="unstuffed-checkbox rounded border-gray-300 text-teal-600" value="{{ $lcl->id }}">
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $lcl->nomor_tanda_terima ?? 'TT-LCL-' . $lcl->id }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($lcl->items->isNotEmpty())
                                            <div class="max-w-xs">
                                                @foreach($lcl->items as $item)
                                                    <div class="text-xs {{ !$loop->last ? 'mb-1 pb-1 border-b border-gray-200' : '' }}">
                                                        <span class="font-medium text-gray-700">{{ $item->nama_barang }}</span>
                                                        @if($item->jumlah)
                                                            <span class="text-gray-500">({{ $item->jumlah }} {{ $item->satuan ?? 'pcs' }})</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $lcl->nama_penerima ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $lcl->nama_pengirim ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ number_format($lcl->items->sum('meter_kubik'), 3) }} m³</td>
                                    <td class="px-4 py-3 text-sm">{{ number_format($lcl->items->sum('tonase'), 3) }} ton</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 text-sm">
                                        Semua LCL sudah di-stuffing
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 text-sm text-gray-600">
                    <span id="selectedUnstuffedCount">0</span> LCL dipilih
                </div>
            </div>

            <form id="stuffingForm" method="POST" action="{{ route('tanda-terima-lcl.stuffing.process') }}">
                @csrf
                <div id="stuffing_selected_ids_container"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- Nomor Kontainer -->
                    <div>
                        <label for="stuffing_nomor_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                            Nomor Kontainer <span class="text-red-500">*</span>
                        </label>
                        <select name="nomor_kontainer" id="stuffing_nomor_kontainer" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 select2-kontainer">
                            <option value="">Pilih Nomor Kontainer</option>
                            @foreach($availableKontainers as $k)
                                <option value="{{ $k['nomor_kontainer'] }}" data-size="{{ $k['ukuran'] }}">
                                    {{ $k['nomor_kontainer'] }} @if($k['ukuran'])({{ $k['ukuran'] }})@endif
                                </option>
                            @endforeach
                            <option value="__manual__">+ Input Manual</option>
                        </select>
                        <input type="text" id="stuffing_nomor_kontainer_manual" 
                               class="hidden w-full mt-2 px-3 py-2 border border-gray-300 rounded-md"
                               placeholder="Ketik nomor kontainer">
                    </div>

                    <!-- Size Kontainer -->
                    <div>
                        <label for="stuffing_size_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                            Size Kontainer
                        </label>
                        <select name="size_kontainer" id="stuffing_size_kontainer"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">Pilih Size</option>
                            <option value="20ft">20 Feet</option>
                            <option value="40ft">40 Feet</option>
                            <option value="40hc">40 Feet High Cube</option>
                            <option value="45ft">45 Feet</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="stuffing_tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                        Tipe Kontainer
                    </label>
                    <input type="text" name="tipe_kontainer" id="stuffing_tipe_kontainer" value="LCL" readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700 cursor-not-allowed focus:outline-none">
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeStuffingModal()" 
                            style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #d1d5db; color: #374151; border-radius: 0.375rem; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);"
                            onmouseover="this.style.backgroundColor='#9ca3af'" 
                            onmouseout="this.style.backgroundColor='#d1d5db'"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit"
                            style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #0d9488; color: #ffffff; border-radius: 0.375rem; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);"
                            onmouseover="this.style.backgroundColor='#0f766e'" 
                            onmouseout="this.style.backgroundColor='#0d9488'"
                            class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Proses Stuffing
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Seal Modal -->
<div id="sealModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4 border-b pb-3">
                <h3 class="text-lg font-medium text-gray-900">Seal Kontainer</h3>
                <button type="button" onclick="closeSealModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="sealForm" method="POST" action="{{ route('tanda-terima-lcl.seal') }}">
                @csrf
                <input type="hidden" name="nomor_kontainer" id="seal_nomor_kontainer">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nomor Kontainer
                    </label>
                    <div id="seal_container_display" class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-sm text-gray-700 font-medium">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="nomor_seal" class="block text-sm font-medium text-gray-700 mb-1">
                        Nomor Seal <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nomor_seal" id="nomor_seal" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500"
                           placeholder="Masukkan nomor seal kontainer">
                </div>

                <div class="mb-4">
                    <label for="tanggal_seal" class="block text-sm font-medium text-gray-700 mb-1">
                        Tanggal Seal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_seal" id="tanggal_seal" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-md p-3 mb-4">
                    <p class="text-xs text-amber-800">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Pastikan nomor seal sudah benar. Data seal tidak dapat diubah setelah disimpan.
                    </p>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeSealModal()" 
                            style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #d1d5db; color: #374151; border-radius: 0.375rem; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);"
                            onmouseover="this.style.backgroundColor='#9ca3af'" 
                            onmouseout="this.style.backgroundColor='#d1d5db'"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit"
                            style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #d97706; color: #ffffff; border-radius: 0.375rem; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);"
                            onmouseover="this.style.backgroundColor='#b45309'" 
                            onmouseout="this.style.backgroundColor='#d97706'"
                            class="px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Simpan Seal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>
jQuery(document).ready(function($) {
    // Initialize Select2
    $('.select2-kontainer').select2({
        placeholder: 'Pilih atau cari nomor kontainer',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#stuffingModal')
    });
    
    // Handle manual input toggle
    $('#stuffing_nomor_kontainer').on('change', function() {
        const manualInput = $('#stuffing_nomor_kontainer_manual');
        const sizeSelect = $('#stuffing_size_kontainer');
        
        if (this.value === '__manual__') {
            manualInput.removeClass('hidden');
            manualInput.attr('name', 'nomor_kontainer');
            manualInput.prop('required', true);
            $(this).removeAttr('name');
            $(this).prop('required', false);
            sizeSelect.val('');
        } else if (this.value !== '') {
            manualInput.addClass('hidden');
            manualInput.removeAttr('name');
            manualInput.prop('required', false);
            manualInput.val('');
            $(this).attr('name', 'nomor_kontainer');
            $(this).prop('required', true);
            
            // Auto-fill size
            const selectedOption = this.options[this.selectedIndex];
            const size = $(selectedOption).data('size');
            if (size) {
                const sizeMapping = {
                    '20': '20ft',
                    '40': '40ft',
                    '40hc': '40hc',
                    '45': '45ft'
                };
                sizeSelect.val(sizeMapping[size] || size);
            }
        }
    });
    
    // Unstuffed LCL checkboxes
    const selectAllUnstuffed = $('#selectAllUnstuffed');
    const unstuffedCheckboxes = $('.unstuffed-checkbox');
    const selectedUnstuffedCount = $('#selectedUnstuffedCount');
    
    selectAllUnstuffed.on('change', function() {
        unstuffedCheckboxes.prop('checked', this.checked);
        updateUnstuffedCount();
    });
    
    unstuffedCheckboxes.on('change', function() {
        updateSelectAllState();
        updateUnstuffedCount();
    });
    
    function updateSelectAllState() {
        const checkedBoxes = $('.unstuffed-checkbox:checked');
        const totalBoxes = unstuffedCheckboxes.length;
        
        if (checkedBoxes.length === 0) {
            selectAllUnstuffed.prop('indeterminate', false);
            selectAllUnstuffed.prop('checked', false);
        } else if (checkedBoxes.length === totalBoxes) {
            selectAllUnstuffed.prop('indeterminate', false);
            selectAllUnstuffed.prop('checked', true);
        } else {
            selectAllUnstuffed.prop('indeterminate', true);
        }
    }
    
    function updateUnstuffedCount() {
        const count = $('.unstuffed-checkbox:checked').length;
        selectedUnstuffedCount.text(count);
    }
});

function toggleContainer(containerId) {
    const element = document.getElementById(containerId);
    const arrow = document.getElementById('arrow-' + containerId);
    
    if (element.style.display === 'none') {
        element.style.display = 'block';
        arrow.style.transform = 'rotate(0deg)';
    } else {
        element.style.display = 'none';
        arrow.style.transform = 'rotate(-90deg)';
    }
}

function showSealModal(nomorKontainer, event) {
    event.stopPropagation(); // Prevent toggle container
    document.getElementById('seal_nomor_kontainer').value = nomorKontainer;
    document.getElementById('seal_container_display').textContent = nomorKontainer;
    
    // Set today as default date
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_seal').value = today;
    
    document.getElementById('sealModal').classList.remove('hidden');
}

function closeSealModal() {
    document.getElementById('sealModal').classList.add('hidden');
    document.getElementById('sealForm').reset();
}

function showUnstuffedModal() {
    document.getElementById('stuffingModal').classList.remove('hidden');
}

function closeStuffingModal() {
    document.getElementById('stuffingModal').classList.add('hidden');
    document.getElementById('stuffingForm').reset();
    jQuery('.select2-kontainer').val('').trigger('change');
    jQuery('.unstuffed-checkbox').prop('checked', false);
    jQuery('#selectAllUnstuffed').prop('checked', false);
    jQuery('#selectedUnstuffedCount').text('0');
}

// Submit form with selected IDs
document.getElementById('stuffingForm').addEventListener('submit', function(e) {
    const checkedBoxes = document.querySelectorAll('.unstuffed-checkbox:checked');
    const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        e.preventDefault();
        alert('Pilih minimal satu LCL untuk di-stuffing');
        return false;
    }
    
    // Clear previous hidden inputs
    const container = document.getElementById('stuffing_selected_ids_container');
    container.innerHTML = '';
    
    // Add hidden input for each selected ID
    selectedIds.forEach(function(id) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'tanda_terima_ids[]';
        input.value = id;
        container.appendChild(input);
    });
});

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const stuffingModal = document.getElementById('stuffingModal');
    if (event.target === stuffingModal) {
        closeStuffingModal();
    }
    
    const sealModal = document.getElementById('sealModal');
    if (event.target === sealModal) {
        closeSealModal();
    }
});

// Prevent form submission if seal number is empty
document.getElementById('sealForm').addEventListener('submit', function(e) {
    const nomorSeal = document.getElementById('nomor_seal').value.trim();
    if (!nomorSeal) {
        e.preventDefault();
        alert('Nomor seal wajib diisi');
        return false;
    }
});
</script>
@endpush
