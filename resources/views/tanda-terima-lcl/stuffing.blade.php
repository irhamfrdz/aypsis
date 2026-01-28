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
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Seal</label>
                            <select name="seal_status" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" {{ request('seal_status') === null ? 'selected' : '' }}>Semua Status</option>
                                <option value="sealed" {{ request('seal_status') == 'sealed' ? 'selected' : '' }}>Sudah Di-Seal</option>
                                <option value="unsealed" {{ request('seal_status') == 'unsealed' ? 'selected' : '' }}>Belum Di-Seal</option>
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

                <!-- Action Buttons for Selected Containers -->
                <div id="selectedContainerActions" class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-teal-50 border-2 border-blue-300 rounded-lg shadow-sm" style="display: none;">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">
                                <span id="selectedContainerCount" class="font-bold text-blue-600">0</span> kontainer dipilih
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="bulkContainerAction('split')" 
                                    style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #8b5cf6; color: #ffffff; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: all 0.2s;"
                                    onmouseover="this.style.backgroundColor='#7c3aed'" 
                                    onmouseout="this.style.backgroundColor='#8b5cf6'"
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm font-medium transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                Pecah Kontainer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table - Display Containers -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left">
                                <input type="checkbox" id="selectAllContainers" class="rounded border-gray-300 text-blue-600">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Kontainer</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size / Tipe</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total LCL</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volume Total</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berat Total</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Seal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Seal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($groupedByContainer as $container)
                            @php
                                $firstPivot = $container['items']->first();
                                $hasSealed = $firstPivot && $firstPivot->nomor_seal;
                                $prospek = $container['prospek'] ?? null;
                                $isShipped = $prospek && $prospek->status == 'sudah_muat';
                                $isActive = $prospek && $prospek->status == 'aktif';
                                $isSealedNoProspek = $hasSealed && !$prospek;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $isShipped ? 'bg-green-50 border-l-4 border-l-green-500' : ($isActive ? 'border-l-4 border-l-blue-500' : ($isSealedNoProspek ? 'border-l-4 border-l-yellow-400' : '')) }}">
                                <td class="px-4 py-4">
                                    <input type="checkbox" class="container-checkbox rounded border-gray-300 text-blue-600" value="{{ $container['nomor_kontainer'] }}">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $container['nomor_kontainer'] }}</div>
                                    @if($isShipped)
                                        <div class="text-xs text-green-600 font-bold mt-1 inline-flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Sudah Naik Kapal
                                        </div>
                                        @if($prospek->nama_kapal)
                                            <div class="text-xs text-green-500 mt-0.5">{{ $prospek->nama_kapal }}</div>
                                        @endif
                                    @elseif($isActive)
                                        <div class="text-xs text-blue-600 font-medium mt-1">Status: Aktif</div>
                                    @elseif($isSealedNoProspek)
                                        <div class="text-xs text-yellow-600 font-medium mt-1 inline-flex items-center" title="Container sudah diseal tapi belum masuk data Prospek">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                            Belum Masuk Prospek
                                        </div>
                                        <form action="{{ route('tanda-terima-lcl.sync-prospek') }}" method="POST" class="inline-block ml-2">
                                            @csrf
                                            <input type="hidden" name="nomor_kontainer" value="{{ $container['nomor_kontainer'] }}">
                                            <input type="hidden" name="nomor_seal" value="{{ $firstPivot->nomor_seal }}">
                                            <button type="submit" class="text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-2 py-0.5 rounded border border-yellow-300 transition-colors" title="Klik untuk mengirim data ini ke Gudang/Prospek">
                                                Kirim ke Gudang
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ $container['size_kontainer'] ?? '-' }}
                                        @if($container['tipe_kontainer'])
                                            <span class="text-gray-500">/ {{ $container['tipe_kontainer'] }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $container['total_lcl'] }} LCL
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ number_format($container['total_volume'], 2) }} m³</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ number_format($container['total_berat'], 2) }} ton</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $firstPivot = $container['items']->first();
                                        $hasSealed = $firstPivot && $firstPivot->nomor_seal;
                                    @endphp
                                    @if($hasSealed)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $firstPivot->nomor_seal }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Belum Seal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($hasSealed && $firstPivot->tanggal_seal)
                                        <div class="text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($firstPivot->tanggal_seal)->format('d/m/Y') }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('tanda-terima-lcl.show-container', ['nomor_kontainer' => $container['nomor_kontainer'], 'seal' => $hasSealed ? $firstPivot->nomor_seal : 'unsealed']) }}"
                                           style="display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; background-color: #2563eb; color: #ffffff; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; text-decoration: none; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: all 0.2s;"
                                           onmouseover="this.style.backgroundColor='#1d4ed8'" 
                                           onmouseout="this.style.backgroundColor='#2563eb'"
                                           class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-xs font-medium transition">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Detail
                                        </a>
                                        @if($hasSealed)
                                            <button type="button" onclick="showUnsealModal('{{ $container['nomor_kontainer'] }}', event)" 
                                                    style="display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; background-color: #dc2626; color: #ffffff; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; border: none; cursor: pointer; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: all 0.2s;"
                                                    onmouseover="this.style.backgroundColor='#b91c1c'" 
                                                    onmouseout="this.style.backgroundColor='#dc2626'"
                                                    class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 text-xs font-medium transition">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                                                </svg>
                                                Lepas Seal
                                            </button>
                                        @else
                                            <button type="button" onclick="showSealModal('{{ $container['nomor_kontainer'] }}', event)" 
                                                    style="display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; background-color: #d97706; color: #ffffff; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; border: none; cursor: pointer; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: all 0.2s;"
                                                    onmouseover="this.style.backgroundColor='#b45309'" 
                                                    onmouseout="this.style.backgroundColor='#d97706'"
                                                    class="inline-flex items-center px-3 py-1.5 bg-amber-600 text-white rounded-md hover:bg-amber-700 text-xs font-medium transition">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                                Seal
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <p class="text-sm">Belum ada data stuffing</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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

                <div class="mb-4">
                    <label for="tujuan" class="block text-sm font-medium text-gray-700 mb-1">
                        Tujuan <span class="text-red-500">*</span>
                    </label>
                    <select name="tujuan" id="tujuan" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">Pilih Tujuan Pengiriman</option>
                        @foreach($masterTujuanKirim as $tujuan)
                            <option value="{{ $tujuan->nama_tujuan }}">
                                {{ $tujuan->nama_tujuan }}
                                @if($tujuan->kode)
                                    ({{ $tujuan->kode }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Pilih tujuan pengiriman dari daftar yang tersedia</p>
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

<!-- Unseal Modal -->
<div id="unsealModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4 border-b pb-3">
                <h3 class="text-lg font-medium text-gray-900">Lepas Seal Kontainer</h3>
                <button type="button" onclick="closeUnsealModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="unsealForm" method="POST" action="{{ route('tanda-terima-lcl.unseal') }}">
                @csrf
                <input type="hidden" name="nomor_kontainer" id="unseal_nomor_kontainer">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nomor Kontainer
                    </label>
                    <div id="unseal_container_display" class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-sm text-gray-700 font-medium">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="alasan_unseal" class="block text-sm font-medium text-gray-700 mb-1">
                        Alasan Lepas Seal <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alasan_unseal" id="alasan_unseal" required rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                              placeholder="Jelaskan alasan melepas seal kontainer..."></textarea>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-md p-3 mb-4">
                    <p class="text-xs text-red-800">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Peringatan: Melepas seal akan menghapus data nomor seal dan tanggal seal. Pastikan ada alasan yang jelas untuk melepas seal kontainer.
                    </p>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeUnsealModal()" 
                            style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #d1d5db; color: #374151; border-radius: 0.375rem; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);"
                            onmouseover="this.style.backgroundColor='#9ca3af'" 
                            onmouseout="this.style.backgroundColor='#d1d5db'"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit"
                            style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #dc2626; color: #ffffff; border-radius: 0.375rem; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);"
                            onmouseover="this.style.backgroundColor='#b91c1c'" 
                            onmouseout="this.style.backgroundColor='#dc2626'"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                        </svg>
                        Lepas Seal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal untuk pecah kontainer -->
<div id="splitModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-3/4 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Pecah Kontainer</h3>
                <button type="button" onclick="closeSplitModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Informasi Pecah Kontainer</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Fitur ini akan membuat tanda terima baru dengan data barang yang dipisahkan dari kontainer yang dipilih</li>
                                <li>Data asli akan tetap ada, dan data baru akan dibuat dengan dimensi yang Anda tentukan</li>
                                <li>Pastikan volume dan berat yang dimasukkan sudah benar</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <form id="splitForm" method="POST" action="{{ route('tanda-terima-lcl.bulk-split') }}">
                @csrf
                
                <!-- Hidden input untuk nomor kontainer yang dipilih -->
                <input type="hidden" id="splitSelectedContainersInput" name="ids" value="">
                
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Detail Kontainer Baru</h4>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div id="containerFieldsGrid" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <!-- Tipe Kontainer -->
                            <div>
                                <label for="split_tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tipe Kontainer <span class="text-red-500">*</span>
                                </label>
                                <select name="tipe_kontainer" id="split_tipe_kontainer" required onchange="toggleSplitContainerFields()"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">Pilih Tipe</option>
                                    <option value="lcl">LCL</option>
                                    <option value="cargo">Cargo</option>
                                </select>
                            </div>

                            <!-- Nomor Kontainer -->
                            <div id="splitNomorKontainerField">
                                <label for="split_nomor_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nomor Kontainer
                                </label>
                                <input type="text" name="nomor_kontainer" id="split_nomor_kontainer"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                                       placeholder="Masukkan nomor kontainer">
                            </div>

                            <!-- Size Kontainer -->
                            <div id="splitSizeKontainerField">
                                <label for="split_size_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                    Size Kontainer
                                </label>
                                <select name="size_kontainer" id="split_size_kontainer"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">Pilih Size</option>
                                    <option value="20ft">20 Feet</option>
                                    <option value="40ft">40 Feet</option>
                                    <option value="40hc">40 Feet High Cube</option>
                                    <option value="45ft">45 Feet</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Nama Barang -->
                            <div>
                                <label for="split_nama_barang" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Barang <span class="text-red-500">*</span>
                                </label>
                                <select id="split_nama_barang" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">Memuat data barang...</option>
                                </select>
                                <input type="hidden" name="item_id" id="split_item_id">
                                <input type="hidden" name="nama_barang" id="split_nama_barang_value">
                            </div>

                            <!-- Jumlah -->
                            <div>
                                <label for="split_jumlah" class="block text-sm font-medium text-gray-700 mb-1">
                                    Jumlah <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="jumlah" id="split_jumlah" required min="1" step="1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 split-dimensi-input"
                                       placeholder="0" onchange="calculateSplitVolume()">
                            </div>

                            <!-- Satuan -->
                            <div>
                                <label for="split_satuan" class="block text-sm font-medium text-gray-700 mb-1">
                                    Satuan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="satuan" id="split_satuan" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                                       placeholder="pcs, unit, dll">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <!-- Panjang -->
                            <div>
                                <label for="split_panjang" class="block text-sm font-medium text-gray-700 mb-1">
                                    Panjang (m) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="panjang" id="split_panjang" required min="0" step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 split-dimensi-input"
                                       placeholder="0.00" onchange="calculateSplitVolume()">
                            </div>

                            <!-- Lebar -->
                            <div>
                                <label for="split_lebar" class="block text-sm font-medium text-gray-700 mb-1">
                                    Lebar (m) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="lebar" id="split_lebar" required min="0" step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 split-dimensi-input"
                                       placeholder="0.00" onchange="calculateSplitVolume()">
                            </div>

                            <!-- Tinggi -->
                            <div>
                                <label for="split_tinggi" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tinggi (m) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="tinggi" id="split_tinggi" required min="0" step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 split-dimensi-input"
                                       placeholder="0.00" onchange="calculateSplitVolume()">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <!-- Volume (Auto-calculated) -->
                            <div>
                                <label for="split_volume" class="block text-sm font-medium text-gray-700 mb-1">
                                    Volume (m³) <span class="text-red-500">*</span>
                                    <span class="text-xs text-gray-500">(otomatis dihitung)</span>
                                </label>
                                <input type="number" name="volume" id="split_volume" required min="0" step="0.001"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                       placeholder="0.000">
                            </div>

                            <!-- Tonase -->
                            <div>
                                <label for="split_tonase" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tonase (ton) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="berat" id="split_tonase" required min="0" step="0.001"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                                       placeholder="0.001">
                            </div>
                        </div>
                        
                        <!-- Keterangan -->
                        <div class="mt-4">
                            <label for="split_keterangan" class="block text-sm font-medium text-gray-700 mb-1">
                                Keterangan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="keterangan" id="split_keterangan" required rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                                      placeholder="Masukkan keterangan untuk kontainer yang dipecah...">Pecahan dari kontainer gabungan</textarea>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeSplitModal()" 
                            style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #d1d5db; color: #374151; border-radius: 0.375rem; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);"
                            onmouseover="this.style.backgroundColor='#9ca3af'" 
                            onmouseout="this.style.backgroundColor='#d1d5db'"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit"
                            style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #8b5cf6; color: #ffffff; border-radius: 0.375rem; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);"
                            onmouseover="this.style.backgroundColor='#7c3aed'" 
                            onmouseout="this.style.backgroundColor='#8b5cf6'"
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        Proses Pecah
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
    // Initialize container checkboxes
    initializeContainerCheckboxes();
    
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

function showUnsealModal(nomorKontainer, event) {
    event.stopPropagation(); // Prevent toggle container
    document.getElementById('unseal_nomor_kontainer').value = nomorKontainer;
    document.getElementById('unseal_container_display').textContent = nomorKontainer;
    document.getElementById('unsealModal').classList.remove('hidden');
}

function closeUnsealModal() {
    document.getElementById('unsealModal').classList.add('hidden');
    document.getElementById('unsealForm').reset();
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
    
    const unsealModal = document.getElementById('unsealModal');
    if (event.target === unsealModal) {
        closeUnsealModal();
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

// Validate unseal form
document.getElementById('unsealForm').addEventListener('submit', function(e) {
    const alasanUnseal = document.getElementById('alasan_unseal').value.trim();
    if (!alasanUnseal) {
        e.preventDefault();
        alert('Alasan lepas seal wajib diisi');
        return false;
    }
    
    // Konfirmasi sebelum submit
    if (!confirm('Apakah Anda yakin ingin melepas seal kontainer ini? Data seal akan dihapus.')) {
        e.preventDefault();
        return false;
    }
});

function initializeContainerCheckboxes() {
    const selectAllCheckbox = $('#selectAllContainers');
    const containerCheckboxes = $('.container-checkbox');
    const selectedActions = $('#selectedContainerActions');
    const selectedCount = $('#selectedContainerCount');

    if (!selectAllCheckbox.length || !selectedActions.length) {
        return;
    }

    // Select All functionality
    selectAllCheckbox.on('change', function() {
        containerCheckboxes.prop('checked', this.checked);
        updateContainerSelectedActions();
    });

    // Individual checkbox functionality
    containerCheckboxes.on('change', function() {
        updateSelectAllContainerState();
        updateContainerSelectedActions();
    });

    function updateSelectAllContainerState() {
        const checkedBoxes = $('.container-checkbox:checked');
        const totalBoxes = containerCheckboxes.length;
        
        if (checkedBoxes.length === 0) {
            selectAllCheckbox.prop('indeterminate', false);
            selectAllCheckbox.prop('checked', false);
        } else if (checkedBoxes.length === totalBoxes) {
            selectAllCheckbox.prop('indeterminate', false);
            selectAllCheckbox.prop('checked', true);
        } else {
            selectAllCheckbox.prop('indeterminate', true);
        }
    }

    function updateContainerSelectedActions() {
        const checkedBoxes = $('.container-checkbox:checked');
        const count = checkedBoxes.length;
        
        selectedCount.text(count);
        
        if (count > 0) {
            selectedActions.show();
        } else {
            selectedActions.hide();
        }
    }
}

function bulkContainerAction(action) {
    const checkedBoxes = document.querySelectorAll('.container-checkbox:checked');
    const selectedContainers = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (selectedContainers.length === 0) {
        alert('Pilih minimal satu kontainer untuk melakukan aksi ini.');
        return;
    }

    switch(action) {
        case 'split':
            openSplitModal(selectedContainers);
            break;
    }
}

function openSplitModal(selectedContainers) {
    document.getElementById('splitSelectedContainersInput').value = JSON.stringify(selectedContainers);
    document.getElementById('splitModal').classList.remove('hidden');
    
    // Initialize container fields visibility
    toggleSplitContainerFields();
    
    // Load barang data from selected containers - get all IDs from containers
    loadBarangForSplit(selectedContainers);
    
    // Focus on first input
    const firstInput = document.querySelector('#splitModal select[name="tipe_kontainer"]');
    if (firstInput) firstInput.focus();
}

function loadBarangForSplit(selectedContainers) {
    const namaBarangSelect = document.getElementById('split_nama_barang');
    
    if (!namaBarangSelect) {
        console.error('Dropdown nama barang tidak ditemukan');
        return;
    }
    
    console.log('🔍 Loading barang for containers:', selectedContainers);
    
    // Reset dropdown
    namaBarangSelect.innerHTML = '<option value="">Memuat data barang...</option>';
    namaBarangSelect.disabled = true;
    
    // Get all tanda terima IDs from selected containers
    // We need to make an AJAX call to get barang from these containers
    fetch('{{ route("tanda-terima-lcl.get-barang-from-containers-by-nomor") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            containers: selectedContainers
        })
    })
    .then(response => {
        console.log('📡 Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('📦 Response data:', data);
        
        if (data.success && data.barang && data.barang.length > 0) {
            namaBarangSelect.innerHTML = '<option value="">-- Pilih Barang --</option>';
            
            data.barang.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id; // Use item ID as unique identifier
                option.textContent = item.display_label || item.nama_barang;
                option.dataset.namaBarang = item.nama_barang;
                option.dataset.satuan = item.satuan || '';
                option.dataset.panjang = item.panjang || '';
                option.dataset.lebar = item.lebar || '';
                option.dataset.tinggi = item.tinggi || '';
                option.dataset.jumlah = item.jumlah || '';
                option.dataset.volume = item.meter_kubik || '';
                option.dataset.tonase = item.tonase || '';
                namaBarangSelect.appendChild(option);
            });
            
            namaBarangSelect.disabled = false;
            
            // Add event listener to auto-fill dimensi
            namaBarangSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    // Set the item ID (specific item to split)
                    const itemIdInput = document.getElementById('split_item_id');
                    if (itemIdInput) {
                        itemIdInput.value = selectedOption.value; // This is the item ID
                    }
                    
                    // Set the nama_barang for display/logging
                    const namaBarangInput = document.getElementById('split_nama_barang_value');
                    if (namaBarangInput) {
                        namaBarangInput.value = selectedOption.dataset.namaBarang || selectedOption.textContent;
                    }
                    
                    document.getElementById('split_satuan').value = selectedOption.dataset.satuan || '';
                    document.getElementById('split_panjang').value = selectedOption.dataset.panjang || '';
                    document.getElementById('split_lebar').value = selectedOption.dataset.lebar || '';
                    document.getElementById('split_tinggi').value = selectedOption.dataset.tinggi || '';
                    document.getElementById('split_jumlah').value = selectedOption.dataset.jumlah || '';
                    calculateSplitVolume();
                }
            });
            
            console.log('✓ Loaded', data.barang.length, 'barang items');
        } else {
            console.warn('⚠️ No barang found. Data:', data);
            namaBarangSelect.innerHTML = '<option value="">Tidak ada data barang</option>';
            namaBarangSelect.disabled = false;
            console.warn('Tidak ada data barang ditemukan');
        }
    })
    .catch(error => {
        console.error('❌ Error loading barang:', error);
        namaBarangSelect.innerHTML = '<option value="">Error memuat data</option>';
        namaBarangSelect.disabled = false;
        alert('Terjadi error saat memuat data barang. Silakan coba lagi.');
    });
}

function closeSplitModal() {
    document.getElementById('splitModal').classList.add('hidden');
    
    // Reset form
    const form = document.getElementById('splitForm');
    if (form) {
        form.reset();
    }
    
    // Reset barang dropdown
    const namaBarangSelect = document.getElementById('split_nama_barang');
    if (namaBarangSelect) {
        namaBarangSelect.innerHTML = '<option value="">Memuat data barang...</option>';
        namaBarangSelect.disabled = true;
    }
    
    // Reset hidden item_id input
    const itemIdInput = document.getElementById('split_item_id');
    if (itemIdInput) {
        itemIdInput.value = '';
    }
    
    // Reset hidden nama_barang input
    const namaBarangInput = document.getElementById('split_nama_barang_value');
    if (namaBarangInput) {
        namaBarangInput.value = '';
    }
    
    // Reset all dimensi input fields
    document.getElementById('split_panjang').value = '';
    document.getElementById('split_lebar').value = '';
    document.getElementById('split_tinggi').value = '';
    document.getElementById('split_jumlah').value = '';
    document.getElementById('split_volume').value = '';
    document.getElementById('split_tonase').value = '';
    
    // Show container fields again
    toggleSplitContainerFields();
}

function toggleSplitContainerFields() {
    const tipeSelect = document.getElementById('split_tipe_kontainer');
    const nomorKontainerField = document.getElementById('splitNomorKontainerField');
    const sizeKontainerField = document.getElementById('splitSizeKontainerField');
    const containerGrid = document.getElementById('containerFieldsGrid');
    
    if (tipeSelect && nomorKontainerField && sizeKontainerField && containerGrid) {
        if (tipeSelect.value === 'cargo') {
            nomorKontainerField.style.display = 'none';
            sizeKontainerField.style.display = 'none';
            containerGrid.className = 'grid grid-cols-1 gap-4 mb-4';
            
            const nomorInput = nomorKontainerField.querySelector('input[name="nomor_kontainer"]');
            const sizeSelect = sizeKontainerField.querySelector('select[name="size_kontainer"]');
            if (nomorInput) nomorInput.value = '';
            if (sizeSelect) sizeSelect.value = '';
        } else {
            nomorKontainerField.style.display = 'block';
            sizeKontainerField.style.display = 'block';
            containerGrid.className = 'grid grid-cols-1 md:grid-cols-3 gap-4 mb-4';
        }
    }
}

function calculateSplitVolume() {
    const panjangInput = document.getElementById('split_panjang');
    const lebarInput = document.getElementById('split_lebar');
    const tinggiInput = document.getElementById('split_tinggi');
    const jumlahInput = document.getElementById('split_jumlah');
    const volumeInput = document.getElementById('split_volume');

    if (!panjangInput || !lebarInput || !tinggiInput || !volumeInput) {
        return;
    }

    const panjang = parseFloat(panjangInput.value) || 0;
    const lebar = parseFloat(lebarInput.value) || 0;
    const tinggi = parseFloat(tinggiInput.value) || 0;
    const jumlah = parseFloat(jumlahInput.value) || 1;

    // Calculate volume even when dimensions are 0
    const volume = panjang * lebar * tinggi * jumlah;
    volumeInput.value = volume.toFixed(3);
}

// Add submit handler for split form with debugging
document.getElementById('splitForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const formUrl = this.action;
    
    console.log('🚀 Submitting split form to:', formUrl);
    console.log('📋 Form data:', Object.fromEntries(formData));
    
    // Validate item_id value
    const itemIdValue = document.getElementById('split_item_id').value;
    if (!itemIdValue) {
        alert('Silakan pilih barang terlebih dahulu');
        return false;
    }
    
    fetch(formUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('📡 Response status:', response.status);
        console.log('📡 Response URL:', response.url);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.error('❌ Error response:', text);
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Success response:', data);
        if (data.success) {
            alert('Pecah kontainer berhasil!');
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Gagal memproses pecah kontainer'));
        }
    })
    .catch(error => {
        console.error('❌ Fetch error:', error);
        alert('Terjadi kesalahan: ' + error.message + '\n\nSilakan cek Console (F12) untuk detail.');
    });
});

// Close split modal when clicking outside
window.addEventListener('click', function(event) {
    const splitModal = document.getElementById('splitModal');
    if (event.target === splitModal) {
        closeSplitModal();
    }
});
</script>
@endpush
