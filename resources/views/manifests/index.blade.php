@extends('layouts.app')

@section('title', 'Manifest - ' . $namaKapal . ' - ' . $noVoyage)
@section('page_title', 'Manifest')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section with Ship Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Manifest</h1>
                    <p class="mt-1 text-sm text-gray-600">Kelola data manifest pengiriman kontainer</p>
                </div>
                @can('manifest-create')
                <div class="flex flex-wrap items-center gap-2 justify-start sm:justify-end mt-4 sm:mt-0">
                    <button onclick="autoUpdateNomorUrutGlobal('{{ $namaKapal }}', '{{ $noVoyage }}')"
                       class="inline-flex items-center justify-center px-3 py-2 bg-white border border-gray-300 text-gray-700 text-xs font-medium rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-1.5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Upd. No. Urut
                    </button>
                    <button onclick="autoUpdateSize('{{ $namaKapal }}', '{{ $noVoyage }}')"
                       class="inline-flex items-center justify-center px-3 py-2 bg-white border border-gray-300 text-gray-700 text-xs font-medium rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-1.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Upd. Size
                    </button>
                    <button onclick="autoUpdateTanggalBerangkat('{{ $namaKapal }}', '{{ $noVoyage }}', '{{ $manifests->first()->tanggal_berangkat ?? '' }}')"
                       class="inline-flex items-center justify-center px-3 py-2 bg-white border border-gray-300 text-gray-700 text-xs font-medium rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-1.5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Upd. Tgl Berangkat
                    </button>
                    @can('manifest-edit')
                    <button type="button" onclick="updateManifestData(true)" 
                            class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-gray-700 text-xs font-medium rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-1.5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Prev. Update
                    </button>
                    <button type="button" onclick="updateManifestData(false)" 
                            class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-gray-700 text-xs font-medium rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-1.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Upd. Manifest
                    </button>
                    @endcan
                    <form action="{{ route('report.manifests.sync') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="nama_kapal" value="{{ $namaKapal }}">
                        <input type="hidden" name="no_voyage" value="{{ $noVoyage }}">
                        <button type="submit"
                           onclick="return confirm('Apakah Anda yakin ingin melakukan sinkronisasi data Manifest dari data Naik Kapal untuk voyage ini? Proses ini mungkin akan memakan waktu sejenak dan memperbarui data nama barang yang kosong atau belum sesuai dengan Tanda Terima.')"
                           class="inline-flex items-center justify-center px-3 py-2 bg-white border border-gray-300 text-gray-700 text-xs font-medium rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-1.5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Sync Data
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="hidden sm:block h-6 w-px bg-gray-300 mx-1"></div>

                    <button onclick="openImportModal()"
                       class="inline-flex items-center justify-center px-3 py-2 bg-green-50 text-green-700 border border-green-200 text-xs font-semibold rounded hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Import
                    </button>
                    <a href="{{ route('report.manifests.export', request()->all()) }}"
                       class="inline-flex items-center justify-center px-3 py-2 bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold rounded hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                    </a>
                    
                    <!-- Divider -->
                    <div class="hidden sm:block h-6 w-px bg-gray-300 mx-1"></div>

                    <a href="{{ route('report.manifests.create') }}"
                       class="inline-flex items-center justify-center px-3 py-2 bg-purple-600 text-white text-xs font-semibold rounded hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Data
                    </a>
                    <button onclick="openBroadcastModal()"
                       class="inline-flex items-center justify-center px-3 py-2 bg-green-500 text-white text-xs font-semibold rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Broadcast WA
                    </button>
                </div>
                @endcan
            </div>

            <!-- Ship & Voyage Info Banner -->
            <div class="bg-gradient-to-r from-purple-500 to-indigo-500 rounded-lg p-4 text-white">
                <div class="flex flex-wrap items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                            <div>
                                <div class="text-xs text-purple-100">Nama Kapal</div>
                                <div class="font-bold">{{ $namaKapal }}</div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <div>
                                <div class="text-xs text-purple-100">No. Voyage</div>
                                <div class="font-bold">{{ $noVoyage }}</div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div>
                                <div class="text-xs text-purple-100">Total Manifest</div>
                                <div class="font-bold">{{ $manifests->total() }} dokumen</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('report.manifests.select-ship') }}" 
                           class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Pilih Kapal Lain
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('report.manifests.index') }}">
                <!-- Hidden fields for ship and voyage -->
                <input type="hidden" name="nama_kapal" value="{{ $namaKapal }}">
                <input type="hidden" name="no_voyage" value="{{ $noVoyage }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Pencarian -->
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Pencarian
                        </label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="No. BL, No. Tanda Terima, No. Kontainer, Nama Barang..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <!-- Tipe Kontainer -->
                    <div>
                        <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Tipe Kontainer</label>
                        <select name="tipe_kontainer" id="tipe_kontainer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Semua Tipe</option>
                            <option value="FCL" {{ strtoupper(request('tipe_kontainer')) == 'FCL' ? 'selected' : '' }}>FCL</option>
                            <option value="LCL" {{ strtoupper(request('tipe_kontainer')) == 'LCL' ? 'selected' : '' }}>LCL</option>
                            <option value="Cargo" {{ strtoupper(request('tipe_kontainer')) == 'Cargo' ? 'selected' : '' }}>Cargo</option>
                            <option value="Dry Container" {{ request('tipe_kontainer') == 'Dry Container' ? 'selected' : '' }}>Dry Container</option>
                            <option value="High Cube" {{ request('tipe_kontainer') == 'High Cube' ? 'selected' : '' }}>High Cube</option>
                            <option value="Reefer" {{ request('tipe_kontainer') == 'Reefer' ? 'selected' : '' }}>Reefer</option>
                            <option value="FREE USE" {{ request('tipe_kontainer') == 'FREE USE' ? 'selected' : '' }}>FREE USE</option>
                        </select>
                    </div>

                    <!-- Size Kontainer -->
                    <div>
                        <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Size</label>
                        <select name="size_kontainer" id="size_kontainer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Semua Size</option>
                            <option value="20" {{ request('size_kontainer') == '20' ? 'selected' : '' }}>20'</option>
                            <option value="40" {{ request('size_kontainer') == '40' ? 'selected' : '' }}>40'</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('report.manifests.index') }}"
                       class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                        Reset
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-3 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">No / Urut</th>
                            <th class="px-2 py-3 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">No. BL</th>
                            <th class="px-2 py-3 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">No. Tanda Terima</th>
                            <th class="px-2 py-3 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                            <th class="px-2 py-3 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Tipe & Size</th>
                            <th class="px-2 py-3 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                            <th class="px-2 py-3 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Kuantitas</th>
                            <th class="px-2 py-3 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider w-32">SHIPPER</th>
                            <th class="px-2 py-3 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider w-32">ALAMAT PENGIRIM</th>
                            <th class="px-2 py-3 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider w-32">CONSIGNEE</th>
                            <th class="px-2 py-3 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider w-32">NOTIFY PARTY</th>
                            <th class="px-2 py-3 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($manifests as $index => $manifest)
                        @php
                            if (str_contains(strtoupper($manifest->no_voyage), 'JB') && empty($manifest->shipper_id)) {
                                $manifest->pengirim = null;
                                $manifest->alamat_pengirim = null;
                                $manifest->penerima = null;
                                $manifest->alamat_penerima = null;
                                $manifest->notify_party = null;
                                $manifest->alamat_notify_party = null;
                            }
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-3">
                                <div class="text-[11px] text-gray-500 mb-1">#{{ ($manifests->currentPage() - 1) * $manifests->perPage() + $index + 1 }}</div>
                                @can('manifest-edit')
                                <div class="flex items-center gap-1">
                                    <input type="number" 
                                           class="text-[11px] font-medium text-gray-900 border border-gray-300 rounded px-1 py-0.5 w-12 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 urut-input" 
                                           data-manifest-id="{{ $manifest->id }}"
                                           data-original-value="{{ $manifest->nomor_urut }}"
                                           value="{{ $manifest->nomor_urut }}"
                                           title="Edit nomor urut">
                                    <button type="button" 
                                            class="save-urut-btn p-1 bg-blue-500 hover:bg-blue-600 text-white rounded hidden"
                                            data-manifest-id="{{ $manifest->id }}"
                                            title="Simpan">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                </div>
                                @else
                                <div class="text-[11px] font-bold text-gray-900">{{ $manifest->nomor_urut ?? '-' }}</div>
                                @endcan
                            </td>
                            <td class="px-2 py-3">
                                @can('manifest-edit')
                                <div class="flex flex-col gap-1">
                                    <input type="text" 
                                           class="text-[11px] font-medium text-gray-900 border border-gray-300 rounded px-1 py-0.5 w-24 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 bl-input" 
                                           data-manifest-id="{{ $manifest->id }}"
                                           data-original-value="{{ $manifest->nomor_bl }}"
                                           value="{{ $manifest->nomor_bl }}"
                                           title="Edit nomor BL">
                                    <div class="flex gap-1">
                                        <button type="button" 
                                                class="save-bl-btn px-1.5 py-0.5 bg-green-500 hover:bg-green-600 text-white text-[10px] font-medium rounded transition-colors flex items-center gap-1"
                                                data-manifest-id="{{ $manifest->id }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span>Ok</span>
                                        </button>
                                        <button type="button" 
                                                class="cancel-bl-btn px-1.5 py-0.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-[10px] font-medium rounded transition-colors"
                                                data-manifest-id="{{ $manifest->id }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                @else
                                <div class="text-[11px] font-medium text-gray-900">{{ $manifest->nomor_bl }}</div>
                                @endcan
                            </td>
                            <td class="px-2 py-3 text-[11px] text-gray-900">
                                {{ $manifest->nomor_tanda_terima_display }}
                            </td>
                            <td class="px-2 py-3">
                                <div class="text-[11px] font-medium text-gray-900">{{ $manifest->nomor_kontainer }}</div>
                                <div class="text-[10px] text-gray-500">Seal: {{ $manifest->no_seal }}</div>
                            </td>
                            <td class="px-2 py-3 text-[11px] text-gray-900">
                                {{ $manifest->tipe_kontainer }}<br><span class="text-gray-500">{{ $manifest->size_kontainer }}'</span>
                            </td>
                            <td class="px-2 py-3 text-[11px] text-gray-900 whitespace-normal leading-tight">
                                {{ $manifest->nama_barang }}
                            </td>
                            <td class="px-2 py-3">
                                @can('manifest-edit')
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-1">
                                        <input type="number" step="any"
                                               class="text-[11px] border border-gray-300 rounded px-1 py-0.5 w-12 kuantitas-input" 
                                               data-manifest-id="{{ $manifest->id }}"
                                               data-original-value="{{ $manifest->kuantitas }}"
                                               value="{{ $manifest->kuantitas }}">
                                        <button type="button" class="save-kuantitas-btn p-1 bg-green-500 text-white rounded hidden" data-manifest-id="{{ $manifest->id }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <input type="text" 
                                               class="text-[11px] border border-gray-300 rounded px-1 py-0.5 w-12 satuan-input" 
                                               data-manifest-id="{{ $manifest->id }}"
                                               data-original-value="{{ $manifest->satuan }}"
                                               value="{{ $manifest->satuan }}">
                                        <button type="button" class="save-satuan-btn p-1 bg-green-500 text-white rounded hidden" data-manifest-id="{{ $manifest->id }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    </div>
                                </div>
                                @else
                                <div class="text-[11px] text-gray-900">{{ $manifest->kuantitas ?? '-' }} {{ $manifest->satuan }}</div>
                                @endcan
                            </td>
                            <td class="px-2 py-3 text-[11px] text-gray-900 whitespace-normal leading-tight">
                                <div class="font-bold">{{ $manifest->pengirim }}</div>
                            </td>
                            <td class="px-2 py-3 text-[11px] text-gray-900 whitespace-normal leading-tight">
                                @if($manifest->alamat_pengirim)
                                    <div class="text-[10px] text-gray-500 mt-0.5 line-clamp-2" title="{{ $manifest->alamat_pengirim }}">
                                        {{ $manifest->alamat_pengirim }}
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-2 py-3 text-[11px] text-gray-900 whitespace-normal leading-tight">
                                <div class="font-bold">{{ $manifest->penerima }}</div>
                                @if($manifest->alamat_penerima)
                                    <div class="text-[10px] text-gray-500 mt-0.5 line-clamp-1" title="{{ $manifest->alamat_penerima }}">
                                        {{ $manifest->alamat_penerima }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-2 py-3 text-[11px] text-gray-900 whitespace-normal leading-tight">
                                @if($manifest->notify_party)
                                    <div class="font-bold">{{ $manifest->notify_party }}</div>
                                    @if($manifest->alamat_notify_party)
                                        <div class="text-[10px] text-gray-500 mt-0.5 line-clamp-1" title="{{ $manifest->alamat_notify_party }}">
                                            {{ $manifest->alamat_notify_party }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-2 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @can('manifest-view')
                                    <a href="{{ route('report.manifests.show', $manifest->id) }}"
                                       class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('report.manifests.print-document', $manifest->id) }}" target="_blank"
                                       class="text-emerald-600 hover:text-emerald-900" title="Print Dokumen">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('report.manifests.print-ba', $manifest->id) }}" target="_blank"
                                       class="text-amber-600 hover:text-amber-900" title="Print BA">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>
                                    @endcan
                                    @php
                                        $waText = "*INFO MANIFEST*\n\n";
                                        $waText .= "Kapal: " . $namaKapal . " - " . $noVoyage . "\n";
                                        $waText .= "No. Tanda Terima: " . $manifest->nomor_tanda_terima_display . "\n";
                                        $waText .= "No. BL: " . $manifest->nomor_bl . "\n";
                                        $waText .= "No. Kontainer: " . $manifest->nomor_kontainer . " (" . $manifest->size_kontainer . "' " . $manifest->tipe_kontainer . ")\n";
                                        $waText .= "Barang: " . $manifest->nama_barang . "\n";
                                        $waText .= "Kuantitas: " . $manifest->kuantitas . " " . $manifest->satuan . "\n\n";
                                        $waText .= "Pengirim: " . $manifest->pengirim . "\n";
                                        $waText .= "Penerima: " . $manifest->penerima;
                                        $waUrl = "https://wa.me/?text=" . rawurlencode($waText);
                                    @endphp
                                    <a href="{{ $waUrl }}" target="_blank"
                                       class="text-green-500 hover:text-green-700" title="Bagikan via WhatsApp">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                                        </svg>
                                    </a>
                                    @can('manifest-edit')
                                    <a href="{{ route('report.manifests.edit', $manifest->id) }}"
                                       class="text-purple-600 hover:text-purple-900" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @endcan
                                    @can('manifest-delete')
                                    <form action="{{ route('report.manifests.destroy', $manifest->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus manifest ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                                <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan manifest baru.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($manifests->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $manifests->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <h3 class="text-xl font-bold text-gray-900">Import Manifest</h3>
            </div>
            <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form action="{{ route('report.manifests.import') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <input type="hidden" name="nama_kapal" value="{{ $namaKapal }}">
            <input type="hidden" name="no_voyage" value="{{ $noVoyage }}">

            <!-- Info Banner -->
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Format File:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>File Excel (.xlsx atau .xls)</li>
                            <li>Gunakan template yang disediakan</li>
                            <li>Maksimal 10MB</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Download Template -->
            <div class="mb-4">
                <a href="{{ route('report.manifests.download-template') }}" 
                   class="inline-flex items-center text-sm text-purple-600 hover:text-purple-800 font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Template
                </a>
            </div>

            <!-- File Upload -->
            <div class="mb-6">
                <label for="import_file" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih File Excel
                </label>
                <div class="relative">
                    <input type="file" 
                           name="file" 
                           id="import_file" 
                           accept=".xlsx,.xls"
                           required
                           class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <p class="mt-1 text-xs text-gray-500">File Excel dengan format .xlsx atau .xls</p>
            </div>

            <!-- Ship Info Display -->
            <div class="mb-6 p-3 bg-gray-50 rounded-lg">
                <div class="text-sm text-gray-600 space-y-1">
                    <div class="flex justify-between">
                        <span class="font-medium">Nama Kapal:</span>
                        <span class="text-gray-900">{{ $namaKapal }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">No. Voyage:</span>
                        <span class="text-gray-900">{{ $noVoyage }}</span>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2">
                <button type="button" 
                        onclick="closeImportModal()"
                        class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Upload & Import
                </button>
            </div>
        </form>
    </div>
    </div>
</div>

<!-- Broadcast Modal -->
<div id="broadcastModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black bg-opacity-50 transition-opacity">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden" onclick="event.stopPropagation()">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Broadcast Info Kapal</h3>
            <button onclick="closeBroadcastModal()" class="text-gray-400 hover:text-gray-600 transition duration-150">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('report.manifests.broadcast-preview') }}" method="POST">
            @csrf
            <input type="hidden" name="nama_kapal" value="{{ $namaKapal }}">
            <input type="hidden" name="no_voyage" value="{{ $noVoyage }}">
            
            <div class="p-6 space-y-4">
                <div>
                    <label for="template_id" class="block text-sm font-semibold text-gray-700 mb-1">Pilih Template WA</label>
                    <select name="template_id" id="template_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">-- Pilih Template --</option>
                        @foreach($waTemplates ?? [] as $template)
                            <option value="{{ $template->id }}">{{ $template->nama_template }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="kategori_masalah" class="block text-sm font-semibold text-gray-700 mb-1">Kategori Masalah</label>
                    <select name="kategori_masalah" id="kategori_masalah" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="Kerusakan Mesin">Kerusakan Mesin</option>
                        <option value="Cuaca Buruk">Cuaca Buruk</option>
                        <option value="Keterlambatan Sandar">Keterlambatan Sandar</option>
                        <option value="Perubahan Jadwal">Perubahan Jadwal</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label for="deskripsi_masalah" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi/Detail (Opsional)</label>
                    <textarea name="deskripsi_masalah" id="deskripsi_masalah" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                </div>
                <div>
                    <label for="estimasi_keterlambatan" class="block text-sm font-semibold text-gray-700 mb-1">Estimasi Keterlambatan (Opsional)</label>
                    <input type="text" name="estimasi_keterlambatan" id="estimasi_keterlambatan" placeholder="Misal: 2 Hari / Belum Diketahui" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeBroadcastModal()"
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                    Lanjut Preview
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Import Modal Functions - Defined immediately for inline onclick handlers
window.openImportModal = function() {
    document.getElementById('importModal').classList.remove('hidden');
    document.getElementById('importModal').classList.add('flex');
}

window.closeImportModal = function() {
    document.getElementById('importModal').classList.add('hidden');
    document.getElementById('importModal').classList.remove('flex');
    // Reset form
    const fileInput = document.getElementById('import_file');
    if (fileInput) fileInput.value = '';
}

// Broadcast Modal Functions
window.openBroadcastModal = function() {
    document.getElementById('broadcastModal').classList.remove('hidden');
    document.getElementById('broadcastModal').classList.add('flex');
}

window.closeBroadcastModal = function() {
    document.getElementById('broadcastModal').classList.add('hidden');
    document.getElementById('broadcastModal').classList.remove('flex');
}
</script>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Editable BL Number functionality
document.addEventListener('DOMContentLoaded', function() {
    // Close modal on outside click
    const importModal = document.getElementById('importModal');
    if (importModal) {
        importModal.addEventListener('click', function(e) {
            if (e.target === this) {
                window.closeImportModal();
            }
        });
    }
    
    const blInputs = document.querySelectorAll('.bl-input');
    const saveBtns = document.querySelectorAll('.save-bl-btn');
    const cancelBtns = document.querySelectorAll('.cancel-bl-btn');
    
    blInputs.forEach(input => {
        const manifestId = input.dataset.manifestId;
        const saveBtn = document.querySelector(`.save-bl-btn[data-manifest-id="${manifestId}"]`);
        const cancelBtn = document.querySelector(`.cancel-bl-btn[data-manifest-id="${manifestId}"]`);
        
        // Show buttons when input is focused or changed
        input.addEventListener('focus', function() {
            saveBtn.classList.remove('hidden');
            cancelBtn.classList.remove('hidden');
        });

        input.addEventListener('input', function() {
            const newValue = this.value.trim();
            const originalValue = this.dataset.originalValue;
            
            // Optional: visual feedback if modified
            if (newValue !== originalValue) {
                this.classList.add('border-yellow-400');
            } else {
                this.classList.remove('border-yellow-400');
            }
        });
        
        // Handle Enter key to save
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const newValue = this.value.trim();
                const originalValue = this.dataset.originalValue;
                
                if (newValue !== originalValue && newValue !== '') {
                    updateNomorBl(manifestId, newValue, this);
                }
            }
            
            // Handle Escape key to cancel
            if (e.key === 'Escape') {
                e.preventDefault();
                this.value = this.dataset.originalValue;
                saveBtn.classList.add('hidden');
                cancelBtn.classList.add('hidden');
            }
        });
    });

    // Nomor Urut functionality
    const urutInputs = document.querySelectorAll('.urut-input');
    urutInputs.forEach(input => {
        const manifestId = input.dataset.manifestId;
        const saveBtn = document.querySelector(`.save-urut-btn[data-manifest-id="${manifestId}"]`);

        input.addEventListener('focus', function() {
            saveBtn.classList.remove('hidden');
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                updateNomorUrut(manifestId, this.value, this);
            }
            if (e.key === 'Escape') {
                e.preventDefault();
                this.value = this.dataset.originalValue;
                saveBtn.classList.add('hidden');
            }
        });
    });

    // Kuantitas functionality
    const kuantitasInputs = document.querySelectorAll('.kuantitas-input');
    kuantitasInputs.forEach(input => {
        const manifestId = input.dataset.manifestId;
        const saveBtn = document.querySelector(`.save-kuantitas-btn[data-manifest-id="${manifestId}"]`);

        input.addEventListener('focus', function() {
            saveBtn.classList.remove('hidden');
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                updateKuantitas(manifestId, this.value, this);
            }
            if (e.key === 'Escape') {
                e.preventDefault();
                this.value = this.dataset.originalValue;
                saveBtn.classList.add('hidden');
            }
        });
    });

    // Satuan functionality
    const satuanInputs = document.querySelectorAll('.satuan-input');
    satuanInputs.forEach(input => {
        const manifestId = input.dataset.manifestId;
        const saveBtn = document.querySelector(`.save-satuan-btn[data-manifest-id="${manifestId}"]`);

        input.addEventListener('focus', function() {
            saveBtn.classList.remove('hidden');
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                updateSatuan(manifestId, this.value, this);
            }
            if (e.key === 'Escape') {
                e.preventDefault();
                this.value = this.dataset.originalValue;
                saveBtn.classList.add('hidden');
            }
        });
    });

    document.querySelectorAll('.save-kuantitas-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const manifestId = this.dataset.manifestId;
            const input = document.querySelector(`.kuantitas-input[data-manifest-id="${manifestId}"]`);
            updateKuantitas(manifestId, input.value, input);
        });
    });

    function updateKuantitas(manifestId, newValue, element) {
        element.classList.add('opacity-50');
        const saveBtn = document.querySelector(`.save-kuantitas-btn[data-manifest-id="${manifestId}"]`);
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        fetch(`/report/manifests/${manifestId}/update-kuantitas`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ kuantitas: newValue })
        })
        .then(response => response.json())
        .then(data => {
            element.classList.remove('opacity-50');
            if (data.success) {
                element.dataset.originalValue = data.kuantitas;
                saveBtn.classList.add('hidden');
                showToast('success', data.message);
                element.classList.add('bg-green-100');
                setTimeout(() => element.classList.remove('bg-green-100'), 1000);
            } else {
                showToast('error', data.message);
                element.value = element.dataset.originalValue;
            }
        })
        .catch(error => {
            element.classList.remove('opacity-50');
            element.value = element.dataset.originalValue;
            showToast('error', 'Terjadi kesalahan');
        });
    }

    document.querySelectorAll('.save-satuan-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const manifestId = this.dataset.manifestId;
            const input = document.querySelector(`.satuan-input[data-manifest-id="${manifestId}"]`);
            updateSatuan(manifestId, input.value, input);
        });
    });

    function updateSatuan(manifestId, newValue, element) {
        element.classList.add('opacity-50');
        const saveBtn = document.querySelector(`.save-satuan-btn[data-manifest-id="${manifestId}"]`);
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        fetch(`/report/manifests/${manifestId}/update-satuan`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ satuan: newValue })
        })
        .then(response => response.json())
        .then(data => {
            element.classList.remove('opacity-50');
            if (data.success) {
                element.dataset.originalValue = data.satuan;
                saveBtn.classList.add('hidden');
                showToast('success', data.message);
                element.classList.add('bg-green-100');
                setTimeout(() => element.classList.remove('bg-green-100'), 1000);
            } else {
                showToast('error', data.message);
                element.value = element.dataset.originalValue;
            }
        })
        .catch(error => {
            element.classList.remove('opacity-50');
            element.value = element.dataset.originalValue;
            showToast('error', 'Terjadi kesalahan');
        });
    }

    document.querySelectorAll('.save-urut-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const manifestId = this.dataset.manifestId;
            const input = document.querySelector(`.urut-input[data-manifest-id="${manifestId}"]`);
            updateNomorUrut(manifestId, input.value, input);
        });
    });

    function updateNomorUrut(manifestId, newValue, element) {
        element.classList.add('opacity-50');
        const saveBtn = document.querySelector(`.save-urut-btn[data-manifest-id="${manifestId}"]`);
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        fetch(`/report/manifests/${manifestId}/update-nomor-urut`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ nomor_urut: newValue })
        })
        .then(response => response.json())
        .then(data => {
            element.classList.remove('opacity-50');
            if (data.success) {
                element.dataset.originalValue = data.nomor_urut;
                saveBtn.classList.add('hidden');
                showToast('success', data.message);
                element.classList.add('bg-green-100');
                setTimeout(() => element.classList.remove('bg-green-100'), 1000);
            } else {
                showToast('error', data.message);
                element.value = element.dataset.originalValue;
            }
        })
        .catch(error => {
            element.classList.remove('opacity-50');
            element.value = element.dataset.originalValue;
            showToast('error', 'Terjadi kesalahan');
        });
    }
    
    // Save button click handlers
    saveBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const manifestId = this.dataset.manifestId;
            const input = document.querySelector(`.bl-input[data-manifest-id="${manifestId}"]`);
            const newValue = input.value.trim();
            
            console.log('Save clicked for manifest:', manifestId, 'Value:', newValue);
            
            if (newValue !== '') {
                updateNomorBl(manifestId, newValue, input);
            } else {
                showToast('error', 'Nomor BL tidak boleh kosong');
            }
        });
    });
    
    // Cancel button click handlers
    cancelBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const manifestId = this.dataset.manifestId;
            const input = document.querySelector(`.bl-input[data-manifest-id="${manifestId}"]`);
            const saveBtn = document.querySelector(`.save-bl-btn[data-manifest-id="${manifestId}"]`);
            
            input.value = input.dataset.originalValue;
            this.classList.add('hidden');
            saveBtn.classList.add('hidden');
        });
    });
    
    window.autoUpdateNomorUrutGlobal = function(namaKapal, noVoyage) {
        if (!confirm('Apakah Anda yakin ingin mengupdate nomor urut secara otomatis? (FCL 1,2.. dan LCL 1,2..)')) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        fetch('{{ route("report.manifests.auto-update-nomor-urut", [], false) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                nama_kapal: namaKapal,
                no_voyage: noVoyage
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan network');
        });
    }

    window.autoUpdateSize = function(namaKapal, noVoyage) {
        if (!confirm('Apakah Anda yakin ingin mengupdate size kontainer secara otomatis berdasarkan master data?')) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        fetch('{{ route("report.manifests.auto-update-size", [], false) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                nama_kapal: namaKapal,
                no_voyage: noVoyage
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan network');
        });
    }

        window.autoUpdateTanggalBerangkat = function(namaKapal, noVoyage, currentTgl) {
        Swal.fire({
            title: 'Update Tanggal Berangkat',
            html: '<p class="text-sm text-gray-500 mb-3">Set tanggal berangkat untuk kapal <b>' + namaKapal + '</b> voyage <b>' + noVoyage + '</b></p>' +
                  '<input type="date" id="tgl_berangkat" class="swal2-input" value="' + currentTgl + '" style="max-width: 100%;">',
            showCancelButton: true,
            confirmButtonText: 'Update',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                const date = document.getElementById('tgl_berangkat').value;
                if (!date) {
                    Swal.showValidationMessage('Silakan pilih tanggal');
                }
                return date;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                fetch('{{ route("report.manifests.auto-update-tanggal-berangkat", [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        nama_kapal: namaKapal,
                        no_voyage: noVoyage,
                        tanggal_berangkat: result.value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Terjadi kesalahan network', 'error');
                });
            }
        });
    }
    window.updateManifestData = function(isDryRun) {

        const actionText = isDryRun ? 'Preview Update' : 'Update Data';
        const confirmText = isDryRun ? 
            'Anda akan melihat preview data Manifest yang akan diupdate dari Tanda Terima tanpa mengubah data apapun. Lanjutkan?' : 
            'Anda akan mengupdate data penerima dan alamat pada Manifest berdasarkan data Tanda Terima. Lanjutkan?';
        
        if (!confirm(confirmText)) {
            return;
        }
        
        // Show loading
        const loadingHtml = `
            <div id="loading-overlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
                <div class="bg-white rounded-lg p-6 shadow-xl flex items-center gap-4">
                    <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-lg font-medium">${actionText}...</span>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', loadingHtml);
        
        // Call API through tanda-terima controller
        fetch('{{ route("tanda-terima.update-manifest", [], false) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                dry_run: isDryRun,
                nama_kapal: '{{ $namaKapal }}',
                no_voyage: '{{ $noVoyage }}'
            })
        })
        .then(response => response.json())
        .then(data => {
            // Remove loading
            document.getElementById('loading-overlay').remove();
            
            if (data.success) {
                // Show result modal
                showManifestUpdateResult(data, isDryRun);
            } else {
                alert('Error: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            document.getElementById('loading-overlay').remove();
            alert('Error: ' + error.message);
            console.error('Error:', error);
        });
    }

    function showManifestUpdateResult(data, isDryRun) {
        const modalTitle = isDryRun ? 'Preview Update Manifest' : 'Hasil Update Manifest';
        const modalIcon = isDryRun ? 
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>' :
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
        
        const warningNote = isDryRun && data.total_with_changes > 0 ? 
            `<div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800"><strong>Catatan:</strong> Ini adalah preview. Data belum diupdate. Klik tombol "Update Manifest" untuk melakukan update sebenarnya.</p>
            </div>` : '';
        
        const successNote = !isDryRun && data.total_updated > 0 ? 
            `<div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-800"><strong>Berhasil!</strong> Data Manifest telah diupdate dari Tanda Terima.</p>
            </div>` : '';
        
        const noChangeNote = data.total_with_changes === 0 ? 
            `<div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800"><strong>Info:</strong> Semua data penerima dan alamat pada Manifest sudah sama dengan data di Tanda Terima.</p>
            </div>` : '';
        
        const modalHtml = `
            <div id="manifest-update-result-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                ${modalIcon}
                            </svg>
                            <h3 class="text-2xl font-bold text-gray-900">${modalTitle}</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-sm text-blue-600 font-medium">Total Manifest Diproses</p>
                                <p class="text-3xl font-bold text-blue-900">${data.total_manifests}</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <p class="text-sm text-purple-600 font-medium">Dengan Tanda Terima</p>
                                <p class="text-3xl font-bold text-purple-900">${data.total_manifest_with_tt}</p>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <p class="text-sm text-yellow-600 font-medium">${isDryRun ? 'Akan Diupdate' : 'Dengan Perubahan'}</p>
                                <p class="text-3xl font-bold text-yellow-900">${data.total_with_changes}</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <p class="text-sm text-green-600 font-medium">${isDryRun ? 'Preview' : 'Berhasil Diupdate'}</p>
                                <p class="text-3xl font-bold text-green-900">${isDryRun ? data.total_with_changes : data.total_updated}</p>
                            </div>
                        </div>
                        
                        ${warningNote}
                        ${successNote}
                        ${noChangeNote}
                    </div>
                    <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                        <button onclick="closeManifestUpdateModal()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            Tutup
                        </button>
                        ${isDryRun && data.total_with_changes > 0 ? 
                            '<button onclick="closeManifestUpdateModal(); updateManifestData(false);" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Update Sekarang</button>' : 
                            ''}
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    window.closeManifestUpdateModal = function() {
        const modal = document.getElementById('manifest-update-result-modal');
        if (modal) {
            modal.remove();
        }
    }

    function updateNomorBl(manifestId, newValue, element) {
        // Show loading state
        element.classList.add('opacity-50');
        const saveBtn = document.querySelector(`.save-bl-btn[data-manifest-id="${manifestId}"]`);
        if (saveBtn) saveBtn.disabled = true;
        
        console.log('Sending update request for manifest:', manifestId, 'with value:', newValue);
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token not found!');
            showToast('error', 'CSRF token tidak ditemukan');
            element.classList.remove('opacity-50');
            if (saveBtn) saveBtn.disabled = false;
            return;
        }
        
        fetch(`/report/manifests/${manifestId}/update-nomor-bl`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                nomor_bl: newValue
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Error response:', text);
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            element.classList.remove('opacity-50');
            if (saveBtn) saveBtn.disabled = false;
            
            if (data.success) {
                // Update the value and original value
                element.value = data.nomor_bl;
                element.dataset.originalValue = data.nomor_bl;
                
                // Show success feedback
                element.classList.add('bg-green-100');
                setTimeout(() => {
                    element.classList.remove('bg-green-100');
                }, 1000);
                
                // Show toast notification
                showToast('success', data.message || 'Nomor BL berhasil disimpan');
            } else {
                // Restore original value on error
                element.value = element.dataset.originalValue;
                showToast('error', data.message || 'Gagal memperbarui nomor BL');
            }
        })
        .catch(error => {
            element.classList.remove('opacity-50');
            if (saveBtn) saveBtn.disabled = false;
            element.value = element.dataset.originalValue;
            console.error('Fetch error:', error);
            showToast('error', 'Terjadi kesalahan: ' + error.message);
        });
    }
    
    function showToast(type, message) {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.add('translate-x-0');
        }, 10);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-x-full');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
});
</script>
@endpush
