@extends('layouts.app')

@section('title', 'Data Kontainer - ' . $gudang->nama_gudang)
@section('page_title', 'Data Kontainer - ' . $gudang->nama_gudang)

@section('content')
<div class="min-h-screen bg-gray-50 py-3 sm:py-6">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-start sm:items-center">
                    <a href="{{ route('checkpoint-kontainer-keluar.checkpoint', $cabangSlug) }}" 
                       class="mr-3 sm:mr-4 inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors mt-1 sm:mt-0">
                        <svg class="w-5 h-5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Data Kontainer</h1>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                {{ $cabangNama }}
                            </span>
                            <span class="text-gray-400 hidden sm:inline">•</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $gudang->nama_gudang }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end sm:justify-start">
                    <a href="{{ route('checkpoint-kontainer-keluar.index') }}" 
                       class="inline-flex items-center px-3 sm:px-4 py-2 bg-gray-600 text-white text-xs sm:text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors touch-manipulation">
                        <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span class="hidden sm:inline">Ke Menu Utama</span>
                        <span class="sm:hidden">Menu</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Pengembalian Kontainer Sewa -->
        <div class="bg-gradient-to-br from-purple-100 via-purple-50 to-indigo-100 rounded-xl shadow-lg border-2 border-purple-400 p-4 sm:p-6 mb-4 sm:mb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <!-- Icon & Text -->
                <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
                    <div class="flex items-center justify-center w-11 h-11 sm:w-12 sm:h-12 bg-gradient-to-br from-purple-600 to-indigo-700 rounded-xl shadow-lg flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold text-purple-900 leading-tight">Pengembalian Kontainer Sewa</h3>
                        <p class="text-xs sm:text-sm text-purple-700 mt-0.5">Proses pengembalian kontainer yang disewa dari gudang <span class="font-bold text-purple-900">{{ $gudang->nama_gudang }}</span></p>
                    </div>
                </div>
                <!-- Button -->
                <button onclick="openPengembalianModal()" 
                        class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl touch-manipulation active:scale-95 flex-shrink-0">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="whitespace-nowrap">Pengembalian Kontainer</span>
                </button>
            </div>
        </div>

        <!-- Data Kontainer -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 px-3 sm:px-4 py-3 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Data Kontainer</h3>
                        <p class="text-xs sm:text-sm text-gray-600 mt-1">Total: {{ $kontainers->count() + $stockKontainers->count() }} kontainer</p>
                    </div>
                    <div class="relative w-full sm:w-64">
                        <input type="text" id="searchKontainers" placeholder="Cari kontainer..." 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Data</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Kontainer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Plat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="kontainersTableBody">
                        @php
                            $counter = 0;
                        @endphp
                        
                        @foreach($kontainers as $kontainer)
                            @php $counter++; @endphp
                            <tr class="hover:bg-gray-50 kontainer-row" data-search="{{ strtolower($kontainer->nomor_seri_gabungan . ' ' . $kontainer->ukuran . ' ' . $kontainer->tipe_kontainer . ' ' . $kontainer->status . ' kontainers') }}">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $counter }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">
                                        Kontainers
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $kontainer->nomor_seri_gabungan }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $kontainer->ukuran }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $kontainer->tipe_kontainer }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $kontainer->no_plat ?? '-' }}</td>
                                <td class="px-4 py-4 text-sm text-gray-600">{{ $kontainer->keterangan ?? '-' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <button onclick="openKirimModal('kontainer', {{ $kontainer->id }}, '{{ $kontainer->nomor_seri_gabungan }}', '{{ $kontainer->ukuran }}', '{{ $kontainer->tipe_kontainer }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                        Kirim
                                    </button>
                                </td>
                            </tr>
                        @endforeach

                        @foreach($stockKontainers as $stock)
                            @php $counter++; @endphp
                            <tr class="hover:bg-gray-50 kontainer-row" data-search="{{ strtolower($stock->nomor_seri_gabungan . ' ' . $stock->ukuran . ' ' . $stock->tipe_kontainer . ' ' . $stock->status . ' stock') }}">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $counter }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        Stock
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $stock->nomor_seri_gabungan }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $stock->ukuran ?? '-' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $stock->tipe_kontainer ?? '-' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $stock->no_plat ?? '-' }}</td>
                                <td class="px-4 py-4 text-sm text-gray-600">{{ $stock->keterangan ?? '-' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <button onclick="openKirimModal('stock', {{ $stock->id }}, '{{ $stock->nomor_seri_gabungan }}', '{{ $stock->ukuran }}', '{{ $stock->tipe_kontainer }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                        Kirim
                                    </button>
                                </td>
                            </tr>
                        @endforeach

                        @if($kontainers->isEmpty() && $stockKontainers->isEmpty())
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        <p class="font-medium text-gray-900 mb-1">Tidak ada kontainer ditemukan</p>
                                        <p class="text-xs text-gray-500">Belum ada kontainer di gudang ini</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden" id="mobileKontainersView">
                @php
                    $counter = 0;
                @endphp
                
                @foreach($kontainers as $kontainer)
                    @php $counter++; @endphp
                    <div class="kontainer-row border-b border-gray-200 p-4 hover:bg-gray-50" data-search="{{ strtolower($kontainer->nomor_seri_gabungan . ' ' . $kontainer->ukuran . ' ' . $kontainer->tipe_kontainer . ' ' . $kontainer->status . ' kontainers') }}">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs font-medium text-gray-500">#{{ $counter }}</span>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-orange-100 text-orange-800">
                                        Kontainers
                                    </span>
                                </div>
                                <h4 class="text-sm font-bold text-gray-900 mb-1">{{ $kontainer->nomor_seri_gabungan }}</h4>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-gray-500">Ukuran:</span>
                                <span class="text-gray-900 font-medium ml-1">{{ $kontainer->ukuran }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Tipe:</span>
                                <span class="text-gray-900 font-medium ml-1">{{ $kontainer->tipe_kontainer }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">No. Plat:</span>
                                <span class="text-gray-900 font-medium ml-1">{{ $kontainer->no_plat ?? '-' }}</span>
                            </div>
                        </div>
                        @if($kontainer->keterangan)
                            <div class="mt-2 text-xs text-gray-600">
                                <span class="text-gray-500">Keterangan:</span> {{ $kontainer->keterangan }}
                            </div>
                        @endif
                        <div class="mt-3">
                            <button onclick="openKirimModal('kontainer', {{ $kontainer->id }}, '{{ $kontainer->nomor_seri_gabungan }}', '{{ $kontainer->ukuran }}', '{{ $kontainer->tipe_kontainer }}')"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors touch-manipulation active:scale-95">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                                Kirim Kontainer
                            </button>
                        </div>
                    </div>
                @endforeach

                @foreach($stockKontainers as $stock)
                    @php $counter++; @endphp
                    <div class="kontainer-row border-b border-gray-200 p-4 hover:bg-gray-50" data-search="{{ strtolower($stock->nomor_seri_gabungan . ' ' . $stock->ukuran . ' ' . $stock->tipe_kontainer . ' ' . $stock->status . ' stock') }}">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs font-medium text-gray-500">#{{ $counter }}</span>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        Stock
                                    </span>
                                </div>
                                <h4 class="text-sm font-bold text-gray-900 mb-1">{{ $stock->nomor_seri_gabungan }}</h4>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-gray-500">Ukuran:</span>
                                <span class="text-gray-900 font-medium ml-1">{{ $stock->ukuran ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Tipe:</span>
                                <span class="text-gray-900 font-medium ml-1">{{ $stock->tipe_kontainer ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">No. Plat:</span>
                                <span class="text-gray-900 font-medium ml-1">{{ $stock->no_plat ?? '-' }}</span>
                            </div>
                        </div>
                        @if($stock->keterangan)
                            <div class="mt-2 text-xs text-gray-600">
                                <span class="text-gray-500">Keterangan:</span> {{ $stock->keterangan }}
                            </div>
                        @endif
                        <div class="mt-3">
                            <button onclick="openKirimModal('stock', {{ $stock->id }}, '{{ $stock->nomor_seri_gabungan }}', '{{ $stock->ukuran }}', '{{ $stock->tipe_kontainer }}')"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors touch-manipulation active:scale-95">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                                Kirim Kontainer
                            </button>
                        </div>
                    </div>
                @endforeach

                @if($kontainers->isEmpty() && $stockKontainers->isEmpty())
                    <div class="p-12 text-center text-sm text-gray-500">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <p class="font-medium text-gray-900 mb-1">Tidak ada kontainer ditemukan</p>
                            <p class="text-xs text-gray-500">Belum ada kontainer di gudang ini</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

<!-- Modal Pengembalian Kontainer -->
<div id="pengembalianModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Pengembalian Kontainer Sewa</h3>
            <button onclick="closePengembalianModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="pengembalianKontainerForm" method="POST" action="{{ route('pengembalian-kontainer.store') }}">
            @csrf
            <input type="hidden" name="gudangs_id" value="{{ $gudang->id }}">
            <input type="hidden" name="cabang" value="{{ $cabangSlug }}">
            
            <div class="bg-purple-50 rounded-lg p-4 mb-4 border border-purple-200">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm text-purple-800">
                        <p class="font-medium mb-1">Informasi:</p>
                        <p class="text-xs">Pilih kontainer yang akan dikembalikan ke pemilik sewa</p>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="kontainer_search" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Kontainer <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" id="kontainer_search" placeholder="Cari nomor kontainer..." required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                           autocomplete="off">
                    <input type="hidden" id="kontainer_id" name="kontainer_id" required>
                    <div id="kontainer_dropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                        <div class="py-1" id="kontainer_options">
                            @php
                                $allKontainers = $kontainers->merge($stockKontainers);
                            @endphp
                            @foreach($allKontainers as $k)
                                <div class="px-3 py-2 hover:bg-purple-50 cursor-pointer kontainer-option" 
                                     data-id="{{ $k->id }}"
                                     data-tipe="{{ $k instanceof \App\Models\Kontainer ? 'kontainer' : 'stock' }}"
                                     data-text="{{ $k->nomor_seri_gabungan }} - {{ $k->ukuran }} - {{ $k->tipe_kontainer }}">
                                    <div class="text-sm font-medium text-gray-900">{{ $k->nomor_seri_gabungan }}</div>
                                    <div class="text-xs text-gray-500">{{ $k->ukuran }} • {{ $k->tipe_kontainer }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="tanggal_pengembalian" class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Pengembalian <span class="text-red-500">*</span>
                </label>
                <input type="date" id="tanggal_pengembalian" name="tanggal_pengembalian" required
                       value="{{ date('Y-m-d') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div class="mb-4">
                <label for="keterangan_pengembalian" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan
                </label>
                <textarea id="keterangan_pengembalian" name="keterangan" rows="3" placeholder="Catatan tambahan (opsional)"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <button type="button" onclick="closePengembalianModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    Proses Pengembalian
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kirim Kontainer -->
<div id="kirimModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Kirim Kontainer</h3>
            <button onclick="closeKirimModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="kirimKontainerForm" method="POST" action="{{ route('checkpoint-kontainer-keluar.kirim') }}">
            @csrf
            <input type="hidden" name="tipe_data" id="modal_tipe_data">
            <input type="hidden" name="kontainer_id" id="modal_kontainer_id">
            <input type="hidden" name="gudangs_id" value="{{ $gudang->id }}">
            <input type="hidden" name="cabang" value="{{ $cabangSlug }}">
            
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nomor Kontainer:</span>
                        <span class="font-semibold text-gray-900" id="modal_nomor">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ukuran:</span>
                        <span class="font-medium text-gray-900" id="modal_ukuran">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tipe:</span>
                        <span class="font-medium text-gray-900" id="modal_tipe">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Gudang:</span>
                        <span class="font-medium text-gray-900">{{ $gudang->nama_gudang }}</span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="tujuan" class="block text-sm font-medium text-gray-700 mb-2">
                    Tujuan <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" id="tujuan_search" placeholder="Cari gudang tujuan..." required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                           autocomplete="off">
                    <input type="hidden" id="tujuan" name="tujuan" required>
                    <div id="tujuan_dropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                        <div class="py-1">
                            @foreach($gudangs as $gd)
                                <div class="px-3 py-2 hover:bg-green-50 cursor-pointer tujuan-option" 
                                     data-value="{{ $gd->nama_gudang }}"
                                     data-text="{{ $gd->nama_gudang }} - {{ $gd->lokasi }}">
                                    <div class="text-sm font-medium text-gray-900">{{ $gd->nama_gudang }}</div>
                                    <div class="text-xs text-gray-500">{{ $gd->lokasi }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="tanggal_kirim" class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Kirim <span class="text-red-500">*</span>
                </label>
                <input type="date" id="tanggal_kirim" name="tanggal_kirim" required
                       value="{{ date('Y-m-d') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>

            <div class="mb-4">
                <label for="nomor_surat_jalan" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Surat Jalan
                </label>
                <div class="relative">
                    <input type="text" id="nomor_surat_jalan_search" placeholder="Cari nomor surat jalan..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                           autocomplete="off">
                    <input type="hidden" id="nomor_surat_jalan" name="nomor_surat_jalan">
                    <div id="surat_jalan_dropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                        <div class="py-1">
                            <div class="px-3 py-2 text-xs text-gray-500 hover:bg-gray-50 cursor-pointer sj-option" data-value="" data-text="Pilih Surat Jalan">
                                <span class="text-gray-400">Pilih Surat Jalan</span>
                            </div>
                            @foreach($suratJalans as $sj)
                                <div class="px-3 py-2 hover:bg-green-50 cursor-pointer sj-option" 
                                     data-value="{{ $sj->no_surat_jalan }}"
                                     data-text="{{ $sj->no_surat_jalan }} - {{ $sj->tanggal_surat_jalan ? $sj->tanggal_surat_jalan->format('d/m/Y') : '-' }}{{ $sj->no_plat ? ' - ' . $sj->no_plat : '' }}{{ $sj->tujuan_pengiriman ? ' - ' . $sj->tujuan_pengiriman : '' }}">
                                    <div class="text-sm font-medium text-gray-900">{{ $sj->no_surat_jalan }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $sj->tanggal_surat_jalan ? $sj->tanggal_surat_jalan->format('d/m/Y') : '-' }}
                                        @if($sj->no_plat)
                                            • {{ $sj->no_plat }}
                                        @endif
                                        @if($sj->tujuan_pengiriman)
                                            • {{ $sj->tujuan_pengiriman }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="keterangan_kirim" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan
                </label>
                <textarea id="keterangan_kirim" name="keterangan" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <button type="button" onclick="closeKirimModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Kirim Kontainer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Searchable Tujuan Dropdown
const tujuanSearchInput = document.getElementById('tujuan_search');
const tujuanHiddenInput = document.getElementById('tujuan');
const tujuanDropdown = document.getElementById('tujuan_dropdown');
const tujuanOptions = document.querySelectorAll('.tujuan-option');

// Show dropdown when input is focused
tujuanSearchInput.addEventListener('focus', function() {
    tujuanDropdown.classList.remove('hidden');
    filterTujuan();
});

// Hide dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!tujuanSearchInput.contains(e.target) && !tujuanDropdown.contains(e.target)) {
        tujuanDropdown.classList.add('hidden');
    }
});

// Filter options based on search input
tujuanSearchInput.addEventListener('input', filterTujuan);

function filterTujuan() {
    const searchTerm = tujuanSearchInput.value.toLowerCase();
    let hasVisibleOption = false;
    
    tujuanOptions.forEach(option => {
        const text = option.getAttribute('data-text').toLowerCase();
        if (text.includes(searchTerm)) {
            option.style.display = '';
            hasVisibleOption = true;
        } else {
            option.style.display = 'none';
        }
    });
    
    if (!hasVisibleOption) {
        tujuanDropdown.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500">Tidak ada hasil</div>';
    }
}

// Handle option selection
tujuanOptions.forEach(option => {
    option.addEventListener('click', function() {
        const value = this.getAttribute('data-value');
        const text = this.getAttribute('data-text');
        
        tujuanHiddenInput.value = value;
        tujuanSearchInput.value = text;
        tujuanDropdown.classList.add('hidden');
    });
});

// Searchable Surat Jalan Dropdown
const sjSearchInput = document.getElementById('nomor_surat_jalan_search');
const sjHiddenInput = document.getElementById('nomor_surat_jalan');
const sjDropdown = document.getElementById('surat_jalan_dropdown');
const sjOptions = document.querySelectorAll('.sj-option');

// Show dropdown when input is focused
sjSearchInput.addEventListener('focus', function() {
    sjDropdown.classList.remove('hidden');
    filterSuratJalan();
});

// Hide dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!sjSearchInput.contains(e.target) && !sjDropdown.contains(e.target)) {
        sjDropdown.classList.add('hidden');
    }
});

// Filter options based on search input
sjSearchInput.addEventListener('input', filterSuratJalan);

function filterSuratJalan() {
    const searchTerm = sjSearchInput.value.toLowerCase();
    let hasVisibleOption = false;
    
    sjOptions.forEach(option => {
        const text = option.getAttribute('data-text').toLowerCase();
        if (text.includes(searchTerm)) {
            option.style.display = '';
            hasVisibleOption = true;
        } else {
            option.style.display = 'none';
        }
    });
    
    if (!hasVisibleOption) {
        sjDropdown.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500">Tidak ada hasil</div>';
    }
}

// Handle option selection
sjOptions.forEach(option => {
    option.addEventListener('click', function() {
        const value = this.getAttribute('data-value');
        const text = this.getAttribute('data-text');
        
        sjHiddenInput.value = value;
        sjSearchInput.value = value ? text : '';
        sjDropdown.classList.add('hidden');
    });
});

// Modal Functions
function openKirimModal(tipe, id, nomor, ukuran, tipeKontainer) {
    document.getElementById('modal_tipe_data').value = tipe;
    document.getElementById('modal_kontainer_id').value = id;
    document.getElementById('modal_nomor').textContent = nomor;
    document.getElementById('modal_ukuran').textContent = ukuran || '-';
    document.getElementById('modal_tipe').textContent = tipeKontainer || '-';
    document.getElementById('kirimModal').classList.remove('hidden');
}

function closeKirimModal() {
    document.getElementById('kirimModal').classList.add('hidden');
    document.getElementById('kirimKontainerForm').reset();
    // Reset searchable dropdowns
    tujuanSearchInput.value = '';
    tujuanHiddenInput.value = '';
    sjSearchInput.value = '';
    sjHiddenInput.value = '';
}

// Close modal when clicking outside
document.getElementById('kirimModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeKirimModal();
    }
});

// Search functionality for Kontainers
const searchKontainers = document.getElementById('searchKontainers');
if (searchKontainers) {
    searchKontainers.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.kontainer-row').forEach(row => {
            const searchData = row.dataset.search || '';
            row.style.display = searchData.includes(searchTerm) ? '' : 'none';
        });
    });
}

// Pengembalian Modal Functions
function openPengembalianModal() {
    document.getElementById('pengembalianModal').classList.remove('hidden');
}

function closePengembalianModal() {
    document.getElementById('pengembalianModal').classList.add('hidden');
    document.getElementById('pengembalianKontainerForm').reset();
    kontainerSearchInput.value = '';
    kontainerHiddenInput.value = '';
}

// Close modal when clicking outside
document.getElementById('pengembalianModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePengembalianModal();
    }
});

// Searchable Kontainer Dropdown for Pengembalian
const kontainerSearchInput = document.getElementById('kontainer_search');
const kontainerHiddenInput = document.getElementById('kontainer_id');
const kontainerDropdown = document.getElementById('kontainer_dropdown');
const kontainerOptions = document.querySelectorAll('.kontainer-option');

// Show dropdown when input is focused
kontainerSearchInput?.addEventListener('focus', function() {
    kontainerDropdown.classList.remove('hidden');
    filterKontainer();
});

// Hide dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (kontainerSearchInput && !kontainerSearchInput.contains(e.target) && !kontainerDropdown.contains(e.target)) {
        kontainerDropdown.classList.add('hidden');
    }
});

// Filter options based on search input
kontainerSearchInput?.addEventListener('input', filterKontainer);

function filterKontainer() {
    const searchTerm = kontainerSearchInput.value.toLowerCase();
    let hasVisibleOption = false;
    
    kontainerOptions.forEach(option => {
        const text = option.getAttribute('data-text').toLowerCase();
        if (text.includes(searchTerm)) {
            option.style.display = '';
            hasVisibleOption = true;
        } else {
            option.style.display = 'none';
        }
    });
    
    if (!hasVisibleOption) {
        document.getElementById('kontainer_options').innerHTML = '<div class="px-3 py-2 text-sm text-gray-500">Tidak ada hasil</div>';
    }
}

// Handle option selection for Kontainer
kontainerOptions.forEach(option => {
    option.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const tipe = this.getAttribute('data-tipe');
        const text = this.getAttribute('data-text');
        
        kontainerHiddenInput.value = id;
        // Store tipe in a hidden field
        let tipeInput = document.getElementById('kontainer_tipe');
        if (!tipeInput) {
            tipeInput = document.createElement('input');
            tipeInput.type = 'hidden';
            tipeInput.id = 'kontainer_tipe';
            tipeInput.name = 'kontainer_tipe';
            document.getElementById('pengembalianKontainerForm').appendChild(tipeInput);
        }
        tipeInput.value = tipe;
        
        kontainerSearchInput.value = text;
        kontainerDropdown.classList.add('hidden');
    });
});
</script>

@endsection
