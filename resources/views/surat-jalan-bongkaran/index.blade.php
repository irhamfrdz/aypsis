@extends('layouts.app')

@section('title', 'Surat Jalan Bongkaran')

@section('content')
<div class="flex-1 p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Surat Jalan Bongkaran</h1>
            <nav class="flex text-sm text-gray-600 mt-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="mx-2">/</span>
                <span class="text-gray-500">Surat Jalan Bongkaran</span>
            </nav>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('success') }}</span>
            <button type="button" class="ml-auto text-green-600 hover:text-green-800" onclick="this.parentElement.remove()">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('error') }}</span>
            <button type="button" class="ml-auto text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Surat Jalan Bongkaran</h2>
                    <div class="flex items-center gap-4 mt-2">
                        <span class="text-sm text-gray-600">
                            <span class="font-medium">Kapal:</span> {{ $selectedKapal }}
                        </span>
                        <span class="text-sm text-gray-600">
                            <span class="font-medium">Voyage:</span> {{ $selectedVoyage }}
                        </span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="buatSuratJalanManual()" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Surat Jalan
                    </button>
                    <a href="{{ route('surat-jalan-bongkaran.select-ship') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Ganti Kapal/Voyage
                    </a>
                </div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <!-- Search Form -->
            <form method="GET" action="{{ route('surat-jalan-bongkaran.list') }}" class="mb-6">
                <input type="hidden" name="nama_kapal" value="{{ $selectedKapal }}">
                <input type="hidden" name="no_voyage" value="{{ $selectedVoyage }}">
                
                <div class="flex flex-col gap-4">
                    <!-- Mode Filter -->
                    <div class="flex items-center gap-4">
                        <label for="mode" class="text-sm font-medium text-gray-700 whitespace-nowrap">Tampilan:</label>
                        <select name="mode" id="mode" 
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                onchange="this.form.submit()">
                            <option value="manifest" {{ request('mode', 'manifest') == 'manifest' ? 'selected' : '' }}>Manifest</option>
                            <option value="surat_jalan" {{ request('mode') == 'surat_jalan' ? 'selected' : '' }}>Surat Jalan Bongkaran</option>
                        </select>
                    </div>
                    
                    <!-- Search and Filter Row -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   id="search" 
                                   name="search" 
                                   placeholder="{{ request('mode') == 'surat_jalan' ? 'Cari nomor surat jalan, container, seal, term, supir, plat...' : 'Cari nomor BL, container, seal, term, kapal, voyage, barang...' }}" 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Cari
                            </button>
                            <a href="{{ route('surat-jalan-bongkaran.index', ['nama_kapal' => $selectedKapal, 'no_voyage' => $selectedVoyage, 'mode' => request('mode', 'manifest')]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>

                    <!-- Debug Info -->
            @if(config('app.debug'))
                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm">
                    <strong>Debug Info:</strong><br>
                    @if(request('mode') == 'surat_jalan')
                        Total Surat Jalan: {{ $suratJalans->total() }}
                    @else
                        Total Manifest: {{ $manifests->total() }}
                    @endif
                </div>
            @endif

            <!-- Table -->
            <div class="relative">
                @if(request('mode') == 'surat_jalan')
                    <!-- Surat Jalan Bongkaran Table -->
                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="suratJalanTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Surat Jalan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Plat</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Container</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Barang</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($suratJalans as $index => $sj)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-center">
                                        <div class="relative inline-block text-left">
                                            <button type="button" onclick="event.stopPropagation(); toggleDropdown('dropdown-sj-{{ $sj->id }}')"
                                                    class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>

                                            <div id="dropdown-sj-{{ $sj->id }}" class="hidden absolute left-0 z-50 mt-1 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                                                <div class="py-1">
                                                    <a href="#" onclick="editSuratJalan({{ $sj->id }}); return false;" 
                                                       class="group flex items-center px-3 py-2 text-xs text-indigo-700 hover:bg-indigo-50 hover:text-indigo-900">
                                                        <svg class="mr-2 h-4 w-4 text-indigo-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Edit
                                                    </a>
                                                    <a href="#" onclick="printSJBongkaran({{ $sj->id }}); return false;" 
                                                       class="group flex items-center px-3 py-2 text-xs text-blue-700 hover:bg-blue-50 hover:text-blue-900">
                                                        <svg class="mr-2 h-4 w-4 text-blue-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                                        </svg>
                                                        Print
                                                    </a>
                                                    <a href="#" onclick="deleteSuratJalan({{ $sj->id }}); return false;" 
                                                       class="group flex items-center px-3 py-2 text-xs text-red-700 hover:bg-red-50 hover:text-red-900">
                                                        <svg class="mr-2 h-4 w-4 text-red-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                        Hapus
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $suratJalans->firstItem() + $index }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="font-semibold text-gray-900">{{ $sj->nomor_surat_jalan ?: '-' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->tanggal_surat_jalan ? $sj->tanggal_surat_jalan->format('d/m/Y') : '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->term ?: '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->supir ?: '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->no_plat ?: '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->no_kontainer ?: '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($sj->jenis_barang, 30) ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data Surat Jalan</h3>
                                            <p class="text-gray-500">Belum ada surat jalan bongkaran yang tersedia untuk kapal dan voyage ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                @else
                    <!-- Manifest Table -->
                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="manifestTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Surat Jalan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor BL</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Container</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Seal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($manifests as $index => $manifest)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-center">
                                        <div class="relative inline-block text-left">
                                            <button type="button" onclick="event.stopPropagation(); toggleDropdown('dropdown-{{ $manifest->id }}')"
                                                    class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>

                                            <div id="dropdown-{{ $manifest->id }}" class="hidden absolute left-0 z-50 mt-1 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                                                <div class="py-1">
                                                    <!-- Opsi Buat/Tambah Surat Jalan - selalu tampil -->
                                                    <a href="#" onclick="buatSuratJalan({{ $manifest->id }}); return false;" 
                                                       class="group flex items-center px-3 py-2 text-xs text-indigo-700 hover:bg-indigo-50 hover:text-indigo-900">
                                                        <svg class="mr-2 h-4 w-4 text-indigo-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                        </svg>
                                                        @if($manifest->suratJalanBongkaran)
                                                            Tambah Surat Jalan
                                                        @else
                                                            Buat Surat Jalan
                                                        @endif
                                                    </a>
                                                    
                                                    <!-- Opsi Edit - hanya tampil jika sudah ada surat jalan -->
                                                    @if($manifest->suratJalanBongkaran)
                                                        <a href="#" onclick="editSuratJalanFromManifest({{ $manifest->suratJalanBongkaran->id }}); return false;" 
                                                           class="group flex items-center px-3 py-2 text-xs text-purple-700 hover:bg-purple-50 hover:text-purple-900">
                                                            <svg class="mr-2 h-4 w-4 text-purple-400 group-hover:text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                            Edit Surat Jalan
                                                        </a>
                                                    @endif
                                                    <a href="#" onclick="printSJ({{ $manifest->id }}); return false;" 
                                                       class="group flex items-center px-3 py-2 text-xs text-blue-700 hover:bg-blue-50 hover:text-blue-900">
                                                        <svg class="mr-2 h-4 w-4 text-blue-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                                        </svg>
                                                        Print SJ
                                                    </a>
                                                    <a href="#" onclick="printBA({{ $manifest->id }}); return false;" 
                                                       class="group flex items-center px-3 py-2 text-xs text-green-700 hover:bg-green-50 hover:text-green-900">
                                                        <svg class="mr-2 h-4 w-4 text-green-400 group-hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        Print BA
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        @if($manifest->suratJalanBongkaran)
                                            <span class="font-semibold text-blue-600">{{ $manifest->suratJalanBongkaran->nomor_surat_jalan }}</span>
                                        @else
                                            <span class="text-orange-600 font-medium">Perlu surat jalan</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $manifests->firstItem() + $index }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="font-semibold text-gray-900">{{ $manifest->nomor_bl ?: '-' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $manifest->nomor_kontainer ?: '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $manifest->no_seal ?: '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $manifest->size_kontainer ?: '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $manifest->term ? ($manifest->term_nama ? $manifest->term . ' - ' . $manifest->term_nama : $manifest->term) : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($manifest->nama_barang, 30) ?: '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($manifest->penerima, 30) ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data Manifest</h3>
                                            <p class="text-gray-500">Belum ada data Manifest yang tersedia.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if((request('mode') == 'surat_jalan' && isset($suratJalans) && $suratJalans->hasPages()) || (request('mode') != 'surat_jalan' && isset($manifests) && $manifests->hasPages()))
                <div class="flex flex-col sm:flex-row justify-between items-center mt-6 pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                        @if(request('mode') == 'surat_jalan')
                            Menampilkan {{ $suratJalans->firstItem() }} sampai {{ $suratJalans->lastItem() }} 
                            dari {{ $suratJalans->total() }} data
                        @else
                            Menampilkan {{ $manifests->firstItem() }} sampai {{ $manifests->lastItem() }} 
                            dari {{ $manifests->total() }} data
                        @endif
                    </div>
                    <div>
                        @if(request('mode') == 'surat_jalan')
                            {{ $suratJalans->appends(request()->query())->links() }}
                        @else
                            {{ $manifests->appends(request()->query())->links() }}
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Buat Surat Jalan -->
    <div id="modalBuatSuratJalan" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-lg bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 class="text-xl font-semibold text-gray-900">Buat Surat Jalan Bongkaran</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="formBuatSuratJalan" action="{{ route('surat-jalan-bongkaran.store') }}" method="POST" class="mt-4" onsubmit="return handleFormSubmit(event)">
                @csrf
                <input type="hidden" name="manifest_id" id="modal_manifest_id">
                <input type="hidden" name="bl_id" id="modal_bl_id">
                <input type="hidden" name="nama_kapal" id="modal_nama_kapal" value="{{ $selectedKapal }}">
                <input type="hidden" name="no_voyage" id="modal_no_voyage" value="{{ $selectedVoyage }}">
                
                <div class="max-h-[70vh] overflow-y-auto px-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Informasi Dasar -->
                        <div class="md:col-span-2">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Informasi Dasar</h4>
                        </div>

                        <!-- Nomor Surat Jalan -->
                        <div>
                            <label for="modal_nomor_surat_jalan" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Surat Jalan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nomor_surat_jalan" id="modal_nomor_surat_jalan" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan nomor surat jalan">
                        </div>

                        <!-- Tanggal Surat Jalan -->
                        <div>
                            <label for="modal_tanggal_surat_jalan" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Surat Jalan <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_surat_jalan" id="modal_tanggal_surat_jalan" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Lanjut Muat -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lanjut Muat</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="lanjut_muat" value="tidak" checked
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Tidak</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="lanjut_muat" value="ya"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Ya</span>
                                </label>
                            </div>
                        </div>

                        <!-- Nomor Surat Jalan Sebelumnya -->
                        <div id="modal_nomor_sj_sebelumnya_wrapper" style="display: none;">
                            <label for="modal_nomor_sj_sebelumnya" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Surat Jalan Sebelumnya <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nomor_sj_sebelumnya" id="modal_nomor_sj_sebelumnya"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan nomor surat jalan sebelumnya">
                            <p class="mt-1 text-xs text-blue-600">Wajib diisi jika memilih lanjut muat</p>
                        </div>

                        <!-- Term -->
                        <div>
                            <label for="modal_term" class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                            <select name="term" id="modal_term"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih term</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->kode }}">{{ $term->kode }} - {{ $term->nama_status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Aktifitas -->
                        <div>
                            <label for="modal_aktifitas" class="block text-sm font-medium text-gray-700 mb-1">Aktifitas</label>
                            <select name="aktifitas" id="modal_aktifitas"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih aktifitas</option>
                                @foreach($masterKegiatans as $kegiatan)
                                    <option value="{{ $kegiatan->nama_kegiatan }}">{{ $kegiatan->nama_kegiatan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Informasi Penerimaan -->
                        <div class="md:col-span-2 mt-3">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Informasi Pengiriman</h4>
                        </div>

                        <!-- Pengirim -->
                        <div>
                            <label for="modal_pengirim" class="block text-sm font-medium text-gray-700 mb-1">Pengirim</label>
                            <input type="text" name="pengirim" id="modal_pengirim"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan nama pengirim">
                        </div>

                        <!-- Penerima -->
                        <div>
                            <label for="modal_penerima" class="block text-sm font-medium text-gray-700 mb-1">Penerima</label>
                            <input type="text" name="penerima" id="modal_penerima"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan nama penerima">
                        </div>

                        <!-- Jenis Barang -->
                        <div>
                            <label for="modal_jenis_barang" class="block text-sm font-medium text-gray-700 mb-1">Jenis Barang</label>
                            <input type="text" name="jenis_barang" id="modal_jenis_barang"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan jenis barang">
                        </div>

                        <!-- Tujuan Alamat -->
                        <div>
                            <label for="modal_tujuan_alamat" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Alamat</label>
                            <input type="text" name="tujuan_alamat" id="modal_tujuan_alamat"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan tujuan alamat">
                        </div>

                        <!-- Tujuan Pengiriman -->
                        <div>
                            <label for="modal_tujuan_pengambilan" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengiriman</label>
                            <select name="tujuan_pengambilan" id="modal_tujuan_pengambilan"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih tujuan pengiriman</option>
                                @foreach($tujuanKegiatanUtamas as $tujuan)
                                    <option value="{{ $tujuan->ke }}" 
                                            data-uang-jalan-20="{{ $tujuan->uang_jalan_20ft ?? 0 }}" 
                                            data-uang-jalan-40="{{ $tujuan->uang_jalan_40ft ?? 0 }}">
                                        {{ $tujuan->ke }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jenis Pengiriman -->
                        <div>
                            <label for="modal_jenis_pengiriman" class="block text-sm font-medium text-gray-700 mb-1">Jenis Pengiriman</label>
                            <select name="jenis_pengiriman" id="modal_jenis_pengiriman"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih jenis pengiriman</option>
                                <option value="FCL">FCL</option>
                                <option value="LCL">LCL</option>
                                <option value="Cargo">Cargo</option>
                            </select>
                        </div>

                        <!-- Tanggal Ambil Barang -->
                        <div>
                            <label for="modal_tanggal_ambil_barang" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Ambil Barang</label>
                            <input type="date" name="tanggal_ambil_barang" id="modal_tanggal_ambil_barang"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Informasi Personal -->
                        <div class="md:col-span-2 mt-3">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Informasi Personal</h4>
                        </div>

                        <!-- Supir -->
                        <div>
                            <label for="modal_supir" class="block text-sm font-medium text-gray-700 mb-1">Supir</label>
                            <select name="supir" id="modal_supir"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih supir</option>
                                @foreach($karyawanSupirs as $supir)
                                    <option value="{{ $supir->nama_panggilan }}" data-plat="{{ $supir->plat }}">
                                        {{ $supir->nama_panggilan }} ({{ $supir->nama_lengkap }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-blue-600">Nomor plat akan terisi otomatis saat memilih supir</p>
                        </div>

                        <!-- No Plat -->
                        <div>
                            <label for="modal_no_plat" class="block text-sm font-medium text-gray-700 mb-1">No Plat</label>
                            <input type="text" name="no_plat" id="modal_no_plat"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan nomor plat">
                        </div>

                        <!-- Kenek -->
                        <div>
                            <label for="modal_kenek" class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                            <select name="kenek" id="modal_kenek"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih kenek</option>
                                @foreach($karyawanKranis as $krani)
                                    <option value="{{ $krani->nama_panggilan }}">
                                        {{ $krani->nama_panggilan }} ({{ $krani->nama_lengkap }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-blue-600">Pilih kenek dari daftar karyawan krani</p>
                        </div>

                        <!-- Krani -->
                        <div>
                            <label for="modal_krani" class="block text-sm font-medium text-gray-700 mb-1">Krani</label>
                            <select name="krani" id="modal_krani"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih krani</option>
                                @foreach($karyawanKranis as $krani)
                                    <option value="{{ $krani->nama_panggilan }}">
                                        {{ $krani->nama_panggilan }} ({{ $krani->nama_lengkap }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-blue-600">Pilih krani dari daftar karyawan</p>
                        </div>

                        <!-- Informasi Container -->
                        <div class="md:col-span-2 mt-3">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Informasi Container</h4>
                        </div>

                        <!-- No Kontainer -->
                        <div>
                            <label for="modal_no_kontainer" class="block text-sm font-medium text-gray-700 mb-1">No Kontainer</label>
                            <input type="text" name="no_kontainer" id="modal_no_kontainer" readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-700">
                        </div>

                        <!-- No Seal -->
                        <div>
                            <label for="modal_no_seal" class="block text-sm font-medium text-gray-700 mb-1">No Seal</label>
                            <input type="text" name="no_seal" id="modal_no_seal" readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-700">
                        </div>

                        <!-- Nomor BL -->
                        <div>
                            <label for="modal_no_bl" class="block text-sm font-medium text-gray-700 mb-1">Nomor BL</label>
                            <input type="text" name="no_bl" id="modal_no_bl" readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-700">
                        </div>

                        <!-- Size Kontainer -->
                        <div>
                            <label for="modal_size" class="block text-sm font-medium text-gray-700 mb-1">Size Kontainer</label>
                            <select name="size" id="modal_size"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih size kontainer</option>
                                <option value="20">20</option>
                                <option value="40">40</option>
                            </select>
                        </div>

                        <!-- Informasi Packaging -->
                        <div class="md:col-span-2 mt-3">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Informasi Packaging</h4>
                        </div>

                        <!-- Karton -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Karton</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="karton" value="ya" class="form-radio text-blue-600">
                                    <span class="ml-2 text-sm">Ya</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="karton" value="tidak" class="form-radio text-blue-600" checked>
                                    <span class="ml-2 text-sm">Tidak</span>
                                </label>
                            </div>
                        </div>

                        <!-- Plastik -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Plastik</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="plastik" value="ya" class="form-radio text-blue-600">
                                    <span class="ml-2 text-sm">Ya</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="plastik" value="tidak" class="form-radio text-blue-600" checked>
                                    <span class="ml-2 text-sm">Tidak</span>
                                </label>
                            </div>
                        </div>

                        <!-- Terpal -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Terpal</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="terpal" value="ya" class="form-radio text-blue-600">
                                    <span class="ml-2 text-sm">Ya</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="terpal" value="tidak" class="form-radio text-blue-600" checked>
                                    <span class="ml-2 text-sm">Tidak</span>
                                </label>
                            </div>
                        </div>

                        <!-- Empty space for alignment -->
                        <div></div>

                        <!-- Informasi Keuangan -->
                        <div class="md:col-span-2 mt-3">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Informasi Keuangan</h4>
                        </div>

                        <!-- RIT -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">RIT</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="rit" value="menggunakan_rit" class="form-radio text-blue-600" checked>
                                    <span class="ml-2 text-sm">Menggunakan RIT</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="rit" value="tidak_menggunakan_rit" class="form-radio text-blue-600">
                                    <span class="ml-2 text-sm">Tidak Menggunakan RIT</span>
                                </label>
                            </div>
                        </div>

                        <!-- Uang Jalan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Uang Jalan</label>
                            <div class="flex space-x-2">
                                <div class="flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="uang_jalan_type" value="full" class="form-radio text-blue-600" checked>
                                        <span class="ml-2 text-sm">Full</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="uang_jalan_type" value="setengah" class="form-radio text-blue-600">
                                        <span class="ml-2 text-sm">Setengah</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Uang Jalan Nominal -->
                        <div class="md:col-span-2">
                            <label for="modal_uang_jalan_nominal" class="block text-sm font-medium text-gray-700 mb-1">Nominal Uang Jalan</label>
                            <input type="number" name="uang_jalan_nominal" id="modal_uang_jalan_nominal"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan nominal uang jalan" min="0" step="1">
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 mt-4 pt-3 border-t">
                    <button type="button" onclick="closeModal()"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitModal"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <span id="btnSubmitText">Simpan Surat Jalan</span>
                        <span id="btnSubmitLoading" class="hidden">
                            <svg class="animate-spin h-4 w-4 inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Surat Jalan -->
    <div id="modalEditSuratJalan" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-lg bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 class="text-xl font-semibold text-gray-900">Edit Surat Jalan Bongkaran</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="formEditSuratJalan" method="POST" class="mt-4" onsubmit="return handleEditFormSubmit(event)">
                @csrf
                @method('PUT')
                <input type="hidden" name="surat_jalan_id" id="edit_modal_surat_jalan_id">
                <input type="hidden" name="manifest_id" id="edit_modal_manifest_id">
                <input type="hidden" name="bl_id" id="edit_modal_bl_id">
                <input type="hidden" name="nama_kapal" id="edit_modal_nama_kapal" value="{{ $selectedKapal }}">
                <input type="hidden" name="no_voyage" id="edit_modal_no_voyage" value="{{ $selectedVoyage }}">
                
                <div class="max-h-[70vh] overflow-y-auto px-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Informasi Dasar -->
                        <div class="md:col-span-2">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Informasi Dasar</h4>
                        </div>

                        <!-- Nomor Surat Jalan -->
                        <div>
                            <label for="edit_modal_nomor_surat_jalan" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Surat Jalan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nomor_surat_jalan" id="edit_modal_nomor_surat_jalan" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan nomor surat jalan">
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label for="edit_modal_lokasi" class="block text-sm font-medium text-gray-700 mb-1">
                                Lokasi <span class="text-red-500">*</span>
                            </label>
                            <select name="lokasi" id="edit_modal_lokasi" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Lokasi --</option>
                                <option value="Jakarta">Jakarta</option>
                                <option value="Batam">Batam</option>
                                <option value="Tanjung Pinang">Tanjung Pinang</option>
                            </select>
                        </div>

                        <!-- Tanggal Surat Jalan -->
                        <div>
                            <label for="edit_modal_tanggal_surat_jalan" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Surat Jalan <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_surat_jalan" id="edit_modal_tanggal_surat_jalan" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Term -->
                        <div>
                            <label for="edit_modal_term" class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                            <select name="term" id="edit_modal_term"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih term</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->kode }}">{{ $term->kode }} - {{ $term->nama_status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Aktifitas -->
                        <div>
                            <label for="edit_modal_aktifitas" class="block text-sm font-medium text-gray-700 mb-1">Aktifitas</label>
                            <select name="aktifitas" id="edit_modal_aktifitas"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih aktifitas</option>
                                @foreach($masterKegiatans as $kegiatan)
                                    <option value="{{ $kegiatan->nama_kegiatan }}">{{ $kegiatan->nama_kegiatan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Informasi Pengiriman -->
                        <div class="md:col-span-2 mt-3">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Informasi Pengiriman</h4>
                        </div>

                        <!-- Pengirim -->
                        <div>
                            <label for="edit_modal_pengirim" class="block text-sm font-medium text-gray-700 mb-1">Pengirim</label>
                            <input type="text" name="pengirim" id="edit_modal_pengirim"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan nama pengirim">
                        </div>

                        <!-- Penerima -->
                        <div>
                            <label for="edit_modal_penerima" class="block text-sm font-medium text-gray-700 mb-1">Penerima</label>
                            <input type="text" name="penerima" id="edit_modal_penerima"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan nama penerima">
                        </div>

                        <!-- Jenis Barang -->
                        <div>
                            <label for="edit_modal_jenis_barang" class="block text-sm font-medium text-gray-700 mb-1">Jenis Barang</label>
                            <input type="text" name="jenis_barang" id="edit_modal_jenis_barang"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Tujuan Alamat -->
                        <div>
                            <label for="edit_modal_tujuan_alamat" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Alamat</label>
                            <input type="text" name="tujuan_alamat" id="edit_modal_tujuan_alamat"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan tujuan alamat">
                        </div>

                        <!-- Tujuan Pengambilan -->
                        <div>
                            <label for="edit_modal_tujuan_pengambilan" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengiriman</label>
                            <select name="tujuan_pengambilan" id="edit_modal_tujuan_pengambilan"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih tujuan pengiriman</option>
                                @foreach($tujuanKegiatanUtamas as $tujuan)
                                    <option value="{{ $tujuan->ke }}" 
                                            data-uang-jalan-20="{{ $tujuan->uang_jalan_20ft ?? 0 }}" 
                                            data-uang-jalan-40="{{ $tujuan->uang_jalan_40ft ?? 0 }}">
                                        {{ $tujuan->ke }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jenis Pengiriman -->
                        <div>
                            <label for="edit_modal_jenis_pengiriman" class="block text-sm font-medium text-gray-700 mb-1">Jenis Pengiriman</label>
                            <select name="jenis_pengiriman" id="edit_modal_jenis_pengiriman"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih jenis pengiriman</option>
                                <option value="FCL">FCL</option>
                                <option value="LCL">LCL</option>
                                <option value="Cargo">Cargo</option>
                            </select>
                        </div>

                        <!-- Tanggal Ambil Barang -->
                        <div>
                            <label for="edit_modal_tanggal_ambil_barang" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Ambil Barang</label>
                            <input type="date" name="tanggal_ambil_barang" id="edit_modal_tanggal_ambil_barang"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Informasi Personal -->
                        <div class="md:col-span-2 mt-3">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Informasi Personal</h4>
                        </div>

                        <!-- Supir -->
                        <div>
                            <label for="edit_modal_supir" class="block text-sm font-medium text-gray-700 mb-1">Supir</label>
                            <select name="supir" id="edit_modal_supir"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih supir</option>
                                @foreach($karyawanSupirs as $supir)
                                    <option value="{{ $supir->nama_panggilan }}" data-plat="{{ $supir->plat }}">
                                        {{ $supir->nama_panggilan }} ({{ $supir->nama_lengkap }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-blue-600">Nomor plat akan terisi otomatis saat memilih supir</p>
                        </div>

                        <!-- No Plat -->
                        <div>
                            <label for="edit_modal_no_plat" class="block text-sm font-medium text-gray-700 mb-1">No Plat</label>
                            <input type="text" name="no_plat" id="edit_modal_no_plat"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan nomor plat">
                        </div>

                        <!-- Kenek -->
                        <div>
                            <label for="edit_modal_kenek" class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                            <select name="kenek" id="edit_modal_kenek"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih kenek</option>
                                @foreach($karyawanKranis as $krani)
                                    <option value="{{ $krani->nama_panggilan }}">
                                        {{ $krani->nama_panggilan }} ({{ $krani->nama_lengkap }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-blue-600">Pilih kenek dari daftar karyawan krani</p>
                        </div>

                        <!-- Krani -->
                        <div>
                            <label for="edit_modal_krani" class="block text-sm font-medium text-gray-700 mb-1">Krani</label>
                            <select name="krani" id="edit_modal_krani"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih krani</option>
                                @foreach($karyawanKranis as $krani)
                                    <option value="{{ $krani->nama_panggilan }}">
                                        {{ $krani->nama_panggilan }} ({{ $krani->nama_lengkap }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-blue-600">Pilih krani dari daftar karyawan</p>
                        </div>

                        <!-- Informasi Container -->
                        <div class="md:col-span-2 mt-3">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Informasi Container</h4>
                        </div>

                        <!-- No Kontainer -->
                        <div>
                            <label for="edit_modal_no_kontainer" class="block text-sm font-medium text-gray-700 mb-1">No Kontainer</label>
                            <input type="text" name="no_kontainer" id="edit_modal_no_kontainer"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- No Seal -->
                        <div>
                            <label for="edit_modal_no_seal" class="block text-sm font-medium text-gray-700 mb-1">No Seal</label>
                            <input type="text" name="no_seal" id="edit_modal_no_seal"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Nomor BL -->
                        <div>
                            <label for="edit_modal_no_bl" class="block text-sm font-medium text-gray-700 mb-1">Nomor BL</label>
                            <input type="text" name="no_bl" id="edit_modal_no_bl"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Size Kontainer -->
                        <div>
                            <label for="edit_modal_size" class="block text-sm font-medium text-gray-700 mb-1">Size Kontainer</label>
                            <select name="size" id="edit_modal_size"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih size kontainer</option>
                                <option value="20">20</option>
                                <option value="40">40</option>
                            </select>
                        </div>

                        <!-- Informasi Packaging -->
                        <div class="md:col-span-2 mt-3">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Informasi Packaging</h4>
                        </div>

                        <!-- Karton -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Karton</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="karton" value="ya" class="form-radio text-blue-600">
                                    <span class="ml-2 text-sm">Ya</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="karton" value="tidak" class="form-radio text-blue-600" checked>
                                    <span class="ml-2 text-sm">Tidak</span>
                                </label>
                            </div>
                        </div>

                        <!-- Plastik -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Plastik</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="plastik" value="ya" class="form-radio text-blue-600">
                                    <span class="ml-2 text-sm">Ya</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="plastik" value="tidak" class="form-radio text-blue-600" checked>
                                    <span class="ml-2 text-sm">Tidak</span>
                                </label>
                            </div>
                        </div>

                        <!-- Terpal -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Terpal</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="terpal" value="ya" class="form-radio text-blue-600">
                                    <span class="ml-2 text-sm">Ya</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="terpal" value="tidak" class="form-radio text-blue-600" checked>
                                    <span class="ml-2 text-sm">Tidak</span>
                                </label>
                            </div>
                        </div>

                        <!-- Empty space for alignment -->
                        <div></div>

                        <!-- Informasi Keuangan -->
                        <div class="md:col-span-2 mt-3">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Informasi Keuangan</h4>
                        </div>

                        <!-- RIT -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">RIT</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="rit" value="menggunakan_rit" class="form-radio text-blue-600" checked>
                                    <span class="ml-2 text-sm">Menggunakan RIT</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="rit" value="tidak_menggunakan_rit" class="form-radio text-blue-600">
                                    <span class="ml-2 text-sm">Tidak Menggunakan RIT</span>
                                </label>
                            </div>
                        </div>

                        <!-- Uang Jalan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Uang Jalan</label>
                            <div class="flex space-x-2">
                                <div class="flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="uang_jalan_type" value="full" class="form-radio text-blue-600" checked>
                                        <span class="ml-2 text-sm">Full</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="uang_jalan_type" value="setengah" class="form-radio text-blue-600">
                                        <span class="ml-2 text-sm">Setengah</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Uang Jalan Nominal -->
                        <div class="md:col-span-2">
                            <label for="edit_modal_uang_jalan_nominal" class="block text-sm font-medium text-gray-700 mb-1">Nominal Uang Jalan</label>
                            <input type="number" name="uang_jalan_nominal" id="edit_modal_uang_jalan_nominal"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan nominal uang jalan" min="0" step="1">
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 mt-4 pt-3 border-t">
                    <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitEditModal"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <span id="btnSubmitEditText">Update Surat Jalan</span>
                        <span id="btnSubmitEditLoading" class="hidden">
                            <svg class="animate-spin h-4 w-4 inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle dropdown menu for action buttons
function toggleDropdown(dropdownId) {
    // Close all other dropdowns first
    document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
        if (dropdown.id !== dropdownId) {
            dropdown.classList.add('hidden');
        }
    });
    
    // Toggle the clicked dropdown
    const dropdown = document.getElementById(dropdownId);
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="toggleDropdown"]') && !event.target.closest('[id^="dropdown-"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});

// Buat Surat Jalan Manual - Open modal without BL data
function buatSuratJalanManual() {
    // Show modal
    document.getElementById('modalBuatSuratJalan').classList.remove('hidden');
    
    // Clear Manifest and BL IDs
    document.getElementById('modal_manifest_id').value = '';
    document.getElementById('modal_bl_id').value = '';
    
    // Set default values
    document.getElementById('modal_nama_kapal').value = '{{ $selectedKapal }}';
    document.getElementById('modal_no_voyage').value = '{{ $selectedVoyage }}';
    
    // Nomor surat jalan dikosongkan
    document.getElementById('modal_nomor_surat_jalan').value = '';
    
    // Set default tanggal to today
    document.getElementById('modal_pengirim').value = '';
    document.getElementById('modal_tanggal_surat_jalan').value = new Date().toISOString().split('T')[0];
    
    // Clear all other fields
    document.getElementById('modal_no_bl').value = '';
    document.getElementById('modal_no_kontainer').value = '';
    document.getElementById('modal_no_seal').value = '';
    document.getElementById('modal_size').value = '';
    document.getElementById('modal_jenis_barang').value = '';
    document.getElementById('modal_penerima').value = '';
    document.getElementById('modal_tujuan_alamat').value = '';
    
    // Remove readonly from fields
    document.getElementById('modal_no_bl').removeAttribute('readonly');
    document.getElementById('modal_no_kontainer').removeAttribute('readonly');
    document.getElementById('modal_no_seal').removeAttribute('readonly');
    document.getElementById('modal_size').removeAttribute('readonly');
    document.getElementById('modal_jenis_barang').removeAttribute('readonly');
    
    // Setup auto-fill and calculations
    setupModalSupirAutoFill();
    setupModalUangJalanCalculation('');
    setupModalLanjutMuatToggle();
}

// Buat Surat Jalan function - Open modal and populate with Manifest data
function buatSuratJalan(manifestId) {
    // Show modal
    document.getElementById('modalBuatSuratJalan').classList.remove('hidden');
    
    // Fetch Manifest data
    fetch(`/api/manifest/${manifestId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response was not JSON');
            }
            return response.json();
        })
        .then(data => {
            // Populate hidden Manifest ID
            document.getElementById('modal_manifest_id').value = manifestId;
            
            // Populate nama kapal and no voyage from Manifest data
            if (data.nama_kapal) {
                document.getElementById('modal_nama_kapal').value = data.nama_kapal;
            }
            if (data.no_voyage) {
                document.getElementById('modal_no_voyage').value = data.no_voyage;
            }
            
            // Nomor surat jalan dikosongkan
            document.getElementById('modal_nomor_surat_jalan').value = '';
            
            // Set default tanggal to today
            document.getElementById('modal_tanggal_surat_jalan').value = new Date().toISOString().split('T')[0];
            
            // Populate Manifest data fields (readonly)
            document.getElementById('modal_no_bl').value = data.nomor_bl || '';
            document.getElementById('modal_no_kontainer').value = data.nomor_kontainer || '';
            document.getElementById('modal_no_seal').value = data.no_seal || '';
            document.getElementById('modal_size').value = data.size_kontainer || '';
            document.getElementById('modal_pengirim').value = data.pengirim || '';
            document.getElementById('modal_penerima').value = data.penerima || data.pengirim || '';
            
            // Set jenis pengiriman if available
            if (data.jenis_pengiriman) {
                document.getElementById('modal_jenis_pengiriman').value = data.jenis_pengiriman;
            }
            
            // Set alamat pengiriman if available
            if (data.alamat_pengiriman) {
                document.getElementById('modal_tujuan_alamat').value = data.alamat_pengiriman;
            }
            
            // Setup auto-fill plat when supir is selected
            setupModalSupirAutoFill();
            
            // Setup auto-calculate uang jalan
            setupModalUangJalanCalculation(data.size_kontainer);
            
            // Setup toggle lanjut muat
            setupModalLanjutMuatToggle();
        })
        .catch(error => {
            console.error('Error fetching Manifest data:', error);
            closeModal();
            alert('Gagal mengambil data Manifest. ' + (error.message || 'Silakan coba lagi atau hubungi administrator.'));
        });
}

// Setup auto-fill plat nomor when supir is selected in modal
function setupModalSupirAutoFill() {
    const supirSelect = document.getElementById('modal_supir');
    const noPlatInput = document.getElementById('modal_no_plat');
    
    if (supirSelect && noPlatInput) {
        // Remove existing listener if any
        supirSelect.removeEventListener('change', handleModalSupirChange);
        // Add new listener
        supirSelect.addEventListener('change', handleModalSupirChange);
    }
}

function handleModalSupirChange(e) {
    const selectedOption = e.target.options[e.target.selectedIndex];
    const platNumber = selectedOption.getAttribute('data-plat');
    const noPlatInput = document.getElementById('modal_no_plat');
    
    if (platNumber && platNumber.trim() !== '') {
        noPlatInput.value = platNumber;
    }
}

// Setup auto-calculate uang jalan based on tujuan pengambilan in modal
function setupModalUangJalanCalculation(containerSize) {
    const tujuanPengambilanSelect = document.getElementById('modal_tujuan_pengambilan');
    const uangJalanNominalInput = document.getElementById('modal_uang_jalan_nominal');
    const uangJalanTypeRadios = document.querySelectorAll('input[name="uang_jalan_type"]');
    const sizeSelect = document.getElementById('modal_size');
    
    function calculateModalUangJalan() {
        const selectedOption = tujuanPengambilanSelect.options[tujuanPengambilanSelect.selectedIndex];
        const uangJalan20 = parseFloat(selectedOption.getAttribute('data-uang-jalan-20')) || 0;
        const uangJalan40 = parseFloat(selectedOption.getAttribute('data-uang-jalan-40')) || 0;
        const uangJalanType = document.querySelector('input[name="uang_jalan_type"]:checked');
        
        // Get current size from dropdown
        const currentSize = sizeSelect.value;
        
        let uangJalan = 0;
        
        // Determine uang jalan based on container size
        if (currentSize === '20' || currentSize === '20ft') {
            uangJalan = uangJalan20;
        } else if (currentSize === '40' || currentSize === '40ft' || currentSize === '40hc' || currentSize === '40 hc') {
            uangJalan = uangJalan40;
        } else {
            // Default to 20ft if size is not clear
            uangJalan = uangJalan20;
        }
        
        // Apply half calculation if "setengah" is selected
        if (uangJalanType && uangJalanType.value === 'setengah') {
            uangJalan = uangJalan / 2;
        }
        
        if (uangJalan > 0) {
            uangJalanNominalInput.value = Math.round(uangJalan);
        }
    }
    
    if (tujuanPengambilanSelect && uangJalanNominalInput && sizeSelect) {
        // Remove existing listeners
        tujuanPengambilanSelect.removeEventListener('change', calculateModalUangJalan);
        sizeSelect.removeEventListener('change', calculateModalUangJalan);
        
        // Add new listeners
        tujuanPengambilanSelect.addEventListener('change', calculateModalUangJalan);
        sizeSelect.addEventListener('change', calculateModalUangJalan);
        
        uangJalanTypeRadios.forEach(radio => {
            radio.removeEventListener('change', calculateModalUangJalan);
            radio.addEventListener('change', calculateModalUangJalan);
        });
    }
}

// Setup toggle for lanjut muat field in modal
function setupModalLanjutMuatToggle() {
    const lanjutMuatRadios = document.querySelectorAll('input[name="lanjut_muat"]');
    const nomorSjSebelumnyaWrapper = document.getElementById('modal_nomor_sj_sebelumnya_wrapper');
    const nomorSjSebelumnyaInput = document.getElementById('modal_nomor_sj_sebelumnya');
    
    function toggleNomorSjSebelumnya() {
        const lanjutMuatValue = document.querySelector('input[name="lanjut_muat"]:checked')?.value;
        
        if (lanjutMuatValue === 'ya') {
            nomorSjSebelumnyaWrapper.style.display = 'block';
            nomorSjSebelumnyaInput.setAttribute('required', 'required');
        } else {
            nomorSjSebelumnyaWrapper.style.display = 'none';
            nomorSjSebelumnyaInput.removeAttribute('required');
            nomorSjSebelumnyaInput.value = '';
        }
    }
    
    // Add event listeners
    lanjutMuatRadios.forEach(radio => {
        radio.removeEventListener('change', toggleNomorSjSebelumnya);
        radio.addEventListener('change', toggleNomorSjSebelumnya);
    });
    
    // Initialize on load
    toggleNomorSjSebelumnya();
}

// Handle form submit with validation and loading state
function handleFormSubmit(event) {
    event.preventDefault();
    
    const form = document.getElementById('formBuatSuratJalan');
    const submitBtn = document.getElementById('btnSubmitModal');
    const submitText = document.getElementById('btnSubmitText');
    const submitLoading = document.getElementById('btnSubmitLoading');
    
    // Validate required fields
    const nomorSuratJalan = document.getElementById('modal_nomor_surat_jalan').value.trim();
    const tanggalSuratJalan = document.getElementById('modal_tanggal_surat_jalan').value.trim();
    
    if (!nomorSuratJalan) {
        showModalAlert('Field Wajib Diisi!', 'Nomor Surat Jalan harus diisi sebelum menyimpan.', 'error');
        document.getElementById('modal_nomor_surat_jalan').focus();
        return false;
    }
    
    if (!tanggalSuratJalan) {
        showModalAlert('Field Wajib Diisi!', 'Tanggal Surat Jalan harus diisi sebelum menyimpan.', 'error');
        document.getElementById('modal_tanggal_surat_jalan').focus();
        return false;
    }
    
    // Validate nomor surat jalan sebelumnya if lanjut muat is 'ya'
    const lanjutMuat = document.querySelector('input[name="lanjut_muat"]:checked')?.value;
    if (lanjutMuat === 'ya') {
        const nomorSjSebelumnya = document.getElementById('modal_nomor_sj_sebelumnya').value.trim();
        if (!nomorSjSebelumnya) {
            showModalAlert('Field Wajib Diisi!', 'Nomor Surat Jalan Sebelumnya harus diisi jika memilih lanjut muat.', 'error');
            document.getElementById('modal_nomor_sj_sebelumnya').focus();
            return false;
        }
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    submitLoading.classList.remove('hidden');
    
    // Submit form via AJAX
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw data;
            });
        }
        return response.json();
    })
    .then(data => {
        // Success - redirect with success message
        if (data.redirect) {
            // Check if URL already has query parameters
            const separator = data.redirect.includes('?') ? '&' : '?';
            window.location.href = data.redirect + separator + 'success=1';
        } else {
            // Reload page to show success message
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Reset button state
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');
        
        // Show error message
        let errorMessage = '';
        let errorTitle = 'Validasi Gagal!';
        
        if (error.errors && Object.keys(error.errors).length > 0) {
            // Laravel validation errors - format as list
            errorTitle = 'Validasi Gagal! Silakan periksa kembali data yang diinput:';
            const errorItems = [];
            
            for (const [field, messages] of Object.entries(error.errors)) {
                const fieldLabel = getFieldLabel(field);
                messages.forEach(msg => {
                    errorItems.push(`<li class="ml-4"><strong>${fieldLabel}:</strong> ${msg}</li>`);
                });
            }
            
            errorMessage = `<ul class="list-disc mt-2 text-sm">${errorItems.join('')}</ul>`;
        } else if (error.message) {
            errorMessage = error.message;
        } else {
            errorTitle = 'Terjadi Kesalahan!';
            errorMessage = 'Gagal menyimpan surat jalan. Silakan coba lagi atau hubungi administrator.';
        }
        
        showModalAlert(errorTitle, errorMessage, 'error');
    });
    
    return false;
}

// Get field label in Indonesian
function getFieldLabel(fieldName) {
    const labels = {
        'nomor_surat_jalan': 'Nomor Surat Jalan',
        'lokasi': 'Lokasi',
        'tanggal_surat_jalan': 'Tanggal Surat Jalan',
        'term': 'Term',
        'aktifitas': 'Aktifitas',
        'penerima': 'Penerima',
        'pengirim': 'Pengirim',
        'jenis_barang': 'Jenis Barang',
        'tujuan_alamat': 'Tujuan Alamat',
        'tujuan_pengambilan': 'Tujuan Pengambilan',
        'tujuan_pengiriman': 'Tujuan Pengiriman',
        'jenis_pengiriman': 'Jenis Pengiriman',
        'tanggal_ambil_barang': 'Tanggal Ambil Barang',
        'supir': 'Supir',
        'no_plat': 'No Plat',
        'kenek': 'Kenek',
        'krani': 'Krani',
        'no_kontainer': 'No Kontainer',
        'no_seal': 'No Seal',
        'no_bl': 'Nomor BL',
        'size': 'Size Kontainer',
        'karton': 'Karton',
        'plastik': 'Plastik',
        'terpal': 'Terpal',
        'rit': 'RIT',
        'uang_jalan_type': 'Tipe Uang Jalan',
        'uang_jalan_nominal': 'Nominal Uang Jalan',
        'lanjut_muat': 'Lanjut Muat',
        'nomor_sj_sebelumnya': 'Nomor Surat Jalan Sebelumnya',
        'nama_kapal': 'Nama Kapal',
        'no_voyage': 'No Voyage',
        'bl_id': 'BL ID'
    };
    
    return labels[fieldName] || fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}

// Show alert inside modal
function showModalAlert(title, message, type = 'error') {
    // Remove existing alert if any
    const existingAlert = document.querySelector('.modal-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `modal-alert mb-4 px-4 py-3 rounded-lg ${
        type === 'error' 
            ? 'bg-red-50 border border-red-200 text-red-800' 
            : 'bg-green-50 border border-green-200 text-green-800'
    }`;
    
    alertDiv.innerHTML = `
        <div class="flex items-start w-full">
            <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                ${type === 'error' 
                    ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>'
                    : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                }
            </svg>
            <div class="flex-1">
                <div class="font-semibold mb-1">${title}</div>
                <div class="text-sm">${message}</div>
            </div>
            <button type="button" class="ml-3 flex-shrink-0 ${type === 'error' ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800'}" onclick="this.parentElement.parentElement.remove()">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    `;
    
    const modalBody = document.querySelector('#formBuatSuratJalan');
    modalBody.insertBefore(alertDiv, modalBody.firstChild);
    
    // Auto-scroll to top of modal to show alert
    const modalContent = document.querySelector('#modalBuatSuratJalan .max-h-\\[70vh\\]');
    if (modalContent) {
        modalContent.scrollTop = 0;
    }
}

// Close modal function
function closeModal() {
    document.getElementById('modalBuatSuratJalan').classList.add('hidden');
    document.getElementById('formBuatSuratJalan').reset();
    
    // Reset lanjut muat field
    const nomorSjSebelumnyaWrapper = document.getElementById('modal_nomor_sj_sebelumnya_wrapper');
    const nomorSjSebelumnyaInput = document.getElementById('modal_nomor_sj_sebelumnya');
    if (nomorSjSebelumnyaWrapper) {
        nomorSjSebelumnyaWrapper.style.display = 'none';
    }
    if (nomorSjSebelumnyaInput) {
        nomorSjSebelumnyaInput.value = '';
        nomorSjSebelumnyaInput.removeAttribute('required');
    }
    
    // Reset button state
    const submitBtn = document.getElementById('btnSubmitModal');
    const submitText = document.getElementById('btnSubmitText');
    const submitLoading = document.getElementById('btnSubmitLoading');
    
    submitBtn.disabled = false;
    submitText.classList.remove('hidden');
    submitLoading.classList.add('hidden');
    
    // Remove any alerts
    const existingAlert = document.querySelector('.modal-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('modalBuatSuratJalan');
    if (event.target === modal) {
        closeModal();
    }
});

// Print SJ function - Print directly from BL data
function printSJ(blId) {
    // Open print page in new window/tab
    window.open('/surat-jalan-bongkaran/print-from-bl/' + blId, '_blank');
}

// Print BA function - Print Berita Acara directly from BL data
function printBA(blId) {
    // Open print BA page in new window/tab
    window.open('/surat-jalan-bongkaran/print-ba/' + blId, '_blank');
}

// Functions for Surat Jalan Bongkaran mode
function editSuratJalan(suratJalanId) {
    // Open edit modal and populate with Surat Jalan data
    openEditModal(suratJalanId);
}

// Edit Surat Jalan from BL (when BL already has Surat Jalan)
function editSuratJalanFromBL(suratJalanId) {
    // Open edit modal and populate with Surat Jalan data
    openEditModal(suratJalanId);
}

// Open edit modal and fetch surat jalan data
function openEditModal(suratJalanId) {
    // Show modal
    document.getElementById('modalEditSuratJalan').classList.remove('hidden');
    
    // Fetch Surat Jalan data
    fetch(`/api/surat-jalan-bongkaran/${suratJalanId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response was not JSON');
            }
            return response.json();
        })
        .then(data => {
            // Populate hidden ID
            document.getElementById('edit_modal_surat_jalan_id').value = suratJalanId;
            document.getElementById('edit_modal_manifest_id').value = data.manifest_id || '';
            document.getElementById('edit_modal_bl_id').value = data.bl_id || '';
            
            // Populate nama kapal and no voyage from surat jalan data
            if (data.nama_kapal) {
                document.getElementById('edit_modal_nama_kapal').value = data.nama_kapal;
            }
            if (data.no_voyage) {
                document.getElementById('edit_modal_no_voyage').value = data.no_voyage;
            }
            
            // Set form action URL
            document.getElementById('formEditSuratJalan').action = `/surat-jalan-bongkaran/${suratJalanId}`;
            
            // Populate form fields
            document.getElementById('edit_modal_nomor_surat_jalan').value = data.nomor_surat_jalan || '';
            document.getElementById('edit_modal_lokasi').value = data.lokasi || '';
            document.getElementById('edit_modal_tanggal_surat_jalan').value = data.tanggal_surat_jalan || '';
            document.getElementById('edit_modal_term').value = data.term || '';
            document.getElementById('edit_modal_aktifitas').value = data.aktifitas || '';
            document.getElementById('edit_modal_pengirim').value = data.pengirim || '';
            document.getElementById('edit_modal_penerima').value = data.penerima || '';
            document.getElementById('edit_modal_jenis_barang').value = data.jenis_barang || '';
            document.getElementById('edit_modal_tujuan_alamat').value = data.tujuan_alamat || '';
            document.getElementById('edit_modal_tujuan_pengambilan').value = data.tujuan_pengambilan || '';
            document.getElementById('edit_modal_jenis_pengiriman').value = data.jenis_pengiriman || '';
            document.getElementById('edit_modal_tanggal_ambil_barang').value = data.tanggal_ambil_barang || '';
            
            document.getElementById('edit_modal_supir').value = data.supir || '';
            document.getElementById('edit_modal_no_plat').value = data.no_plat || '';
            document.getElementById('edit_modal_kenek').value = data.kenek || '';
            document.getElementById('edit_modal_krani').value = data.krani || '';
            
            document.getElementById('edit_modal_no_kontainer').value = data.no_kontainer || '';
            document.getElementById('edit_modal_no_seal').value = data.no_seal || '';
            document.getElementById('edit_modal_no_bl').value = data.no_bl || '';
            document.getElementById('edit_modal_size').value = data.size || '';
            
            // Set radio buttons
            if (data.karton) {
                document.querySelector(`input[name="karton"][value="${data.karton}"]`).checked = true;
            }
            if (data.plastik) {
                document.querySelector(`input[name="plastik"][value="${data.plastik}"]`).checked = true;
            }
            if (data.terpal) {
                document.querySelector(`input[name="terpal"][value="${data.terpal}"]`).checked = true;
            }
            if (data.rit) {
                document.querySelector(`input[name="rit"][value="${data.rit}"]`).checked = true;
            }
            if (data.uang_jalan_type) {
                document.querySelector(`input[name="uang_jalan_type"][value="${data.uang_jalan_type}"]`).checked = true;
            }
            
            // Convert to integer to remove decimal places
            const nominalValue = data.uang_jalan_nominal ? Math.round(parseFloat(data.uang_jalan_nominal)) : '';
            document.getElementById('edit_modal_uang_jalan_nominal').value = nominalValue;
            
            // Setup auto-fill and auto-calculate functions
            setupEditModalSupirAutoFill();
            setupEditModalUangJalanCalculation(data.size);
        })
        .catch(error => {
            console.error('Error fetching Surat Jalan data:', error);
            closeEditModal();
            
            // Show more detailed error message
            let errorMsg = 'Gagal mengambil data Surat Jalan. ';
            if (error.message) {
                errorMsg += error.message;
            } else if (error.error) {
                errorMsg += error.error;
            } else {
                errorMsg += 'Silakan coba lagi atau hubungi administrator.';
            }
            errorMsg += '\n\nID: ' + suratJalanId;
            
            alert(errorMsg);
        });
}

// Setup auto-fill plat nomor when supir is selected in edit modal
function setupEditModalSupirAutoFill() {
    const supirSelect = document.getElementById('edit_modal_supir');
    const noPlatInput = document.getElementById('edit_modal_no_plat');
    
    if (supirSelect && noPlatInput) {
        supirSelect.removeEventListener('change', handleEditModalSupirChange);
        supirSelect.addEventListener('change', handleEditModalSupirChange);
    }
}

function handleEditModalSupirChange(e) {
    const selectedOption = e.target.options[e.target.selectedIndex];
    const platNumber = selectedOption.getAttribute('data-plat');
    const noPlatInput = document.getElementById('edit_modal_no_plat');
    
    if (platNumber && platNumber.trim() !== '') {
        noPlatInput.value = platNumber;
    }
}

// Setup auto-calculate uang jalan in edit modal
function setupEditModalUangJalanCalculation(containerSize) {
    const tujuanPengambilanSelect = document.getElementById('edit_modal_tujuan_pengambilan');
    const uangJalanNominalInput = document.getElementById('edit_modal_uang_jalan_nominal');
    const uangJalanTypeRadios = document.querySelectorAll('#modalEditSuratJalan input[name="uang_jalan_type"]');
    const sizeSelect = document.getElementById('edit_modal_size');
    
    function calculateEditModalUangJalan() {
        const selectedOption = tujuanPengambilanSelect.options[tujuanPengambilanSelect.selectedIndex];
        const uangJalan20 = parseFloat(selectedOption.getAttribute('data-uang-jalan-20')) || 0;
        const uangJalan40 = parseFloat(selectedOption.getAttribute('data-uang-jalan-40')) || 0;
        const uangJalanType = document.querySelector('#modalEditSuratJalan input[name="uang_jalan_type"]:checked');
        
        const currentSize = sizeSelect.value;
        let uangJalan = 0;
        
        if (currentSize === '20' || currentSize === '20ft') {
            uangJalan = uangJalan20;
        } else if (currentSize === '40' || currentSize === '40ft' || currentSize === '40hc' || currentSize === '40 hc') {
            uangJalan = uangJalan40;
        } else {
            uangJalan = uangJalan20;
        }
        
        if (uangJalanType && uangJalanType.value === 'setengah') {
            uangJalan = uangJalan / 2;
        }
        
        if (uangJalan > 0) {
            uangJalanNominalInput.value = Math.round(uangJalan);
        }
    }
    
    if (tujuanPengambilanSelect && uangJalanNominalInput && sizeSelect) {
        tujuanPengambilanSelect.removeEventListener('change', calculateEditModalUangJalan);
        sizeSelect.removeEventListener('change', calculateEditModalUangJalan);
        
        tujuanPengambilanSelect.addEventListener('change', calculateEditModalUangJalan);
        sizeSelect.addEventListener('change', calculateEditModalUangJalan);
        
        uangJalanTypeRadios.forEach(radio => {
            radio.removeEventListener('change', calculateEditModalUangJalan);
            radio.addEventListener('change', calculateEditModalUangJalan);
        });
    }
}

// Handle edit form submit
function handleEditFormSubmit(event) {
    event.preventDefault();
    
    const form = document.getElementById('formEditSuratJalan');
    const submitBtn = document.getElementById('btnSubmitEditModal');
    const submitText = document.getElementById('btnSubmitEditText');
    const submitLoading = document.getElementById('btnSubmitEditLoading');
    
    // Validate required fields
    const nomorSuratJalan = document.getElementById('edit_modal_nomor_surat_jalan').value.trim();
    const tanggalSuratJalan = document.getElementById('edit_modal_tanggal_surat_jalan').value.trim();
    
    if (!nomorSuratJalan) {
        showEditModalAlert('Field Wajib Diisi!', 'Nomor Surat Jalan harus diisi sebelum menyimpan.', 'error');
        document.getElementById('edit_modal_nomor_surat_jalan').focus();
        return false;
    }
    
    if (!tanggalSuratJalan) {
        showEditModalAlert('Field Wajib Diisi!', 'Tanggal Surat Jalan harus diisi sebelum menyimpan.', 'error');
        document.getElementById('edit_modal_tanggal_surat_jalan').focus();
        return false;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    submitLoading.classList.remove('hidden');
    
    // Submit form via AJAX
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw data;
            });
        }
        return response.json();
    })
    .then(data => {
        // Success - redirect with success message
        if (data.redirect) {
            // Check if URL already has query parameters
            const separator = data.redirect.includes('?') ? '&' : '?';
            window.location.href = data.redirect + separator + 'success=1';
        } else {
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Reset button state
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');
        
        // Show error message
        let errorMessage = '';
        let errorTitle = 'Validasi Gagal!';
        
        if (error.errors && Object.keys(error.errors).length > 0) {
            errorTitle = 'Validasi Gagal! Silakan periksa kembali data yang diinput:';
            const errorItems = [];
            
            for (const [field, messages] of Object.entries(error.errors)) {
                const fieldLabel = getFieldLabel(field);
                messages.forEach(msg => {
                    errorItems.push(`<li class="ml-4"><strong>${fieldLabel}:</strong> ${msg}</li>`);
                });
            }
            
            errorMessage = `<ul class="list-disc mt-2 text-sm">${errorItems.join('')}</ul>`;
        } else if (error.message) {
            errorMessage = error.message;
        } else {
            errorTitle = 'Terjadi Kesalahan!';
            errorMessage = 'Gagal mengupdate surat jalan. Silakan coba lagi atau hubungi administrator.';
        }
        
        showEditModalAlert(errorTitle, errorMessage, 'error');
    });
    
    return false;
}

// Show alert inside edit modal
function showEditModalAlert(title, message, type = 'error') {
    const existingAlert = document.querySelector('#modalEditSuratJalan .modal-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `modal-alert mb-4 px-4 py-3 rounded-lg ${
        type === 'error' 
            ? 'bg-red-50 border border-red-200 text-red-800' 
            : 'bg-green-50 border border-green-200 text-green-800'
    }`;
    
    alertDiv.innerHTML = `
        <div class="flex items-start w-full">
            <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                ${type === 'error' 
                    ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>'
                    : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                }
            </svg>
            <div class="flex-1">
                <div class="font-semibold mb-1">${title}</div>
                <div class="text-sm">${message}</div>
            </div>
            <button type="button" class="ml-3 flex-shrink-0 ${type === 'error' ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800'}" onclick="this.parentElement.parentElement.remove()">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    `;
    
    const modalBody = document.querySelector('#formEditSuratJalan');
    modalBody.insertBefore(alertDiv, modalBody.firstChild);
    
    const modalContent = document.querySelector('#modalEditSuratJalan .max-h-\\[70vh\\]');
    if (modalContent) {
        modalContent.scrollTop = 0;
    }
}

// Close edit modal
function closeEditModal() {
    document.getElementById('modalEditSuratJalan').classList.add('hidden');
    document.getElementById('formEditSuratJalan').reset();
    
    const submitBtn = document.getElementById('btnSubmitEditModal');
    const submitText = document.getElementById('btnSubmitEditText');
    const submitLoading = document.getElementById('btnSubmitEditLoading');
    
    submitBtn.disabled = false;
    submitText.classList.remove('hidden');
    submitLoading.classList.add('hidden');
    
    const existingAlert = document.querySelector('#modalEditSuratJalan .modal-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
}

// Close edit modal when clicking outside
document.addEventListener('click', function(event) {
    const editModal = document.getElementById('modalEditSuratJalan');
    if (event.target === editModal) {
        closeEditModal();
    }
});

function printSJBongkaran(suratJalanId) {
    // Print existing surat jalan bongkaran
    window.open('/surat-jalan-bongkaran/print/' + suratJalanId, '_blank');
}

function deleteSuratJalan(suratJalanId) {
    if (confirm('Apakah Anda yakin ingin menghapus surat jalan ini?')) {
        // Create a form to submit delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/surat-jalan-bongkaran/' + suratJalanId;
        form.style.display = 'none';

        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);

        // Add DELETE method
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush