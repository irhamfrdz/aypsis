@extends('layouts.app')

@if(isset($bls) && $bls->count() > 0)
@section('title', 'OB - Data Bongkaran')
@section('page_title', 'OB - Data Bongkaran')
@else
@section('title', 'OB - Data Naik Kapal')
@section('page_title', 'OB - Data Naik Kapal')
@endif

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-ship mr-3 text-orange-600 text-2xl"></i>
                <div>
                    @if(isset($bls) && $bls->count() > 0)
                    <h1 class="text-2xl font-bold text-gray-800">OB - Data Bongkaran</h1>
                    @else
                    <h1 class="text-2xl font-bold text-gray-800">OB - Data Naik Kapal</h1>
                    @endif
                    <p class="text-gray-600">Kapal: <strong>{{ $namaKapal }}</strong> | Voyage: <strong>{{ $noVoyage }}</strong></p>
                    <p class="text-xs text-gray-500 mt-1">Last updated: {{ now()->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="window.location.reload()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh Data
                </button>
                <a href="{{ route('ob.print', array_merge(['nama_kapal' => $namaKapal, 'no_voyage' => $noVoyage], request()->only(['status_ob', 'tipe_kontainer', 'kegiatan']))) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-print mr-2"></i>Print
                </a>
                <a href="{{ route('ob.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-arrow-left mr-2"></i>Pilih Kapal Lain
                </a>
                <a href="{{ route('tagihan-ob.index', array_merge(['nama_kapal' => $namaKapal, 'no_voyage' => $noVoyage], request()->has('kegiatan') ? ['kegiatan' => request('kegiatan')] : [])) }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-file-invoice mr-2"></i>Tagihan OB
                </a>
            </div>
        </div>
    </div>
    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                    <i class="fas fa-boxes text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Kontainer</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalKontainer }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                    <i class="fas fa-check text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Sudah OB</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sudahOB }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-clock text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Belum OB</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $belumOB }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('ob.index') }}">
            <input type="hidden" name="nama_kapal" value="{{ $namaKapal }}">
            <input type="hidden" name="no_voyage" value="{{ $noVoyage }}">
            @if(request()->has('kegiatan'))
                <input type="hidden" name="kegiatan" value="{{ request('kegiatan') }}">
            @endif
            @if(request()->has('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                {{-- Search --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="No kontainer, seal, barang..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Tipe Kontainer Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Kontainer</label>
                    <select name="tipe_kontainer"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tipe</option>
                        <option value="FCL" {{ request('tipe_kontainer') == 'FCL' ? 'selected' : '' }}>FCL</option>
                        <option value="LCL" {{ request('tipe_kontainer') == 'LCL' ? 'selected' : '' }}>LCL</option>
                        <option value="CARGO" {{ request('tipe_kontainer') == 'CARGO' ? 'selected' : '' }}>CARGO</option>
                    </select>
                </div>

                {{-- Asal Kontainer Input --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Asal Kontainer (Semua)</label>
                    @if(request('kegiatan') === 'muat')
                        <select id="bulk_asal_kontainer"
                                class="select2-gudang w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih gudang...</option>
                            @foreach($gudangs as $gudang)
                                <option value="{{ $gudang->nama_gudang }}">
                                    {{ $gudang->nama_gudang }} - {{ $gudang->lokasi }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="text"
                               id="bulk_asal_kontainer"
                               placeholder="Isi untuk mengubah semua"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @endif
                    <p class="text-xs text-gray-500 mt-1">Isi untuk mengubah semua asal kontainer</p>
                </div>

                {{-- Ke Input --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ke (Semua)</label>
                    <input type="text"
                           id="bulk_ke"
                           placeholder="Isi untuk mengubah semua"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Isi untuk mengubah tujuan semua kontainer</p>
                </div>
            </div>

            <div class="flex justify-between items-center mt-4">
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('ob.index', array_merge(['nama_kapal' => $namaKapal, 'no_voyage' => $noVoyage], request()->has('kegiatan') ? ['kegiatan' => request('kegiatan')] : [])) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Reset
                    </a>
                </div>
                <div>
                    <button type="button" id="btnSaveAsalKe" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Asal & Ke
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Table Section --}}
    {{-- Bulk Actions --}}
    <div id="bulk-actions" class="hidden bg-gray-50 px-4 py-3 border-b border-gray-200 mb-4">
        <button type="button" id="btnMasukPranota" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
            <i class="fas fa-plus mr-2"></i>Masukan ke Pranota
        </button>
    </div>
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            @if(isset($bls))
            <table class="min-w-full table-auto text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">
                            <input type="checkbox" id="select-all" class="mr-1">
                            <span class="text-[10px]">✓</span>
                        </th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">No</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">No. BL</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">No. Kontainer</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">No. Seal</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Nama Barang</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Asal</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Ke</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Tipe</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Size</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Created</th>
                        {{-- Volume and Tonase columns removed per request --}}
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Status OB</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($bls as $key => $bl)
                    <tr class="hover:bg-gray-50 transition duration-150 {{ $bl->tipe_kontainer == 'CARGO' ? 'bg-gray-100' : '' }}">
                        <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900">
                            <input type="checkbox" class="row-checkbox" value="{{ $bl->id }}" data-type="bl" data-nomor-kontainer="{{ $bl->nomor_kontainer }}" data-nama-barang="{{ $bl->nama_barang }}" data-tipe="{{ $bl->tipe_kontainer }}" data-size="{{ $bl->size_kontainer }}" data-biaya="{{ $bl->biaya ?? '' }}" data-status="{{ $bl->detected_status ?? 'full' }}" data-supir="{{ $bl->supir ? ($bl->supir->nama_panggilan ?? $bl->supir->nama_lengkap ?? '') : '' }}" data-sudah-tl="{{ $bl->sudah_tl ? '1' : '0' }}" data-sudah-ob="{{ $bl->sudah_ob ? '1' : '0' }}" {{ $bl->tipe_kontainer == 'CARGO' || $bl->sudah_tl || !$bl->sudah_ob ? 'disabled title="' . ($bl->tipe_kontainer == 'CARGO' ? 'Kontainer CARGO' : ($bl->sudah_tl ? 'Kontainer TL' : 'Kontainer belum OB')) . ' tidak bisa dimasukkan ke pranota"' : '' }}>
                            @if($bl->tipe_kontainer == 'CARGO')
                                <span class="text-[10px] text-red-600" title="Kontainer CARGO tidak bisa dimasukkan ke pranota">⚠️</span>
                            @elseif($bl->sudah_tl)
                                <span class="text-[10px] text-blue-600" title="Kontainer TL tidak bisa dimasukkan ke pranota">⚠️</span>
                            @elseif(!$bl->sudah_ob)
                                <span class="text-[10px] text-orange-600" title="Kontainer belum OB tidak bisa dimasukkan ke pranota">⚠️</span>
                            @endif
                        </td>
                        <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900">{{ $bls->firstItem() + $key }}</td>
                        <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900 font-mono">{{ $bl->nomor_bl ?: '-' }}</td>
                        <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900 font-mono">{{ $bl->nomor_kontainer ?: '-' }}</td>
                        <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900 font-mono">{{ $bl->no_seal ?: '-' }}</td>
                        <td class="px-1 py-1 text-xs text-gray-900 max-w-xs truncate" title="{{ $bl->nama_barang }}">{{ $bl->nama_barang ?: '-' }}</td>
                        <td class="px-1 py-1 text-xs text-gray-900">
                            <div class="flex items-center gap-1">
                                @if(request('kegiatan') === 'muat')
                                    <select class="editable-asal-kontainer select2-gudang w-full px-1 py-0.5 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                            data-id="{{ $bl->id }}" 
                                            data-type="bl">
                                        <option value="">Pilih gudang...</option>
                                        @foreach($gudangs as $gudang)
                                            <option value="{{ $gudang->nama_gudang }}" {{ $bl->asal_kontainer == $gudang->nama_gudang ? 'selected' : '' }}>
                                                {{ $gudang->nama_gudang }} - {{ $gudang->lokasi }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" 
                                           class="editable-asal-kontainer w-full px-1 py-0.5 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                           data-id="{{ $bl->id }}" 
                                           data-type="bl"
                                           value="{{ $bl->asal_kontainer ?: (request('kegiatan') === 'bongkar' ? $namaKapal : '') }}"
                                           placeholder="Asal kontainer...">
                                @endif
                                <button onclick="saveAsalKe('bl', {{ $bl->id }}, this.closest('td'))" 
                                        class="text-green-600 hover:text-green-900 transition duration-150"
                                        title="Simpan">
                                    <i class="fas fa-save"></i>
                                </button>
                            </div>
                        </td>
                        <td class="px-1 py-1 text-xs text-gray-900">
                            <div class="flex items-center gap-1">
                                <input type="text" 
                                       class="editable-ke w-full px-1 py-0.5 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                       data-id="{{ $bl->id }}" 
                                       data-type="bl"
                                       value="{{ $bl->ke ?: (request('kegiatan') === 'muat' ? $namaKapal : '') }}"
                                       placeholder="Tujuan...">
                                <button onclick="saveAsalKe('bl', {{ $bl->id }}, this.closest('td'))" 
                                        class="text-green-600 hover:text-green-900 transition duration-150"
                                        title="Simpan">
                                    <i class="fas fa-save"></i>
                                </button>
                            </div>
                        </td>
                        <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900">{{ $bl->tipe_kontainer ?: '-' }}</td>
                        <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900">{{ $bl->size_kontainer ? $bl->size_kontainer : '-' }}</td>
                        <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900">{{ $bl->created_at ? $bl->created_at->format('d/m/y') : '-' }}</td>
                        {{-- Volume and Tonase cells removed per request --}}
                        <td class="px-1 py-1 text-xs text-gray-900">
                            @if($bl->sudah_ob)
                                <div class="flex flex-col space-y-0.5">
                                    <span class="inline-flex items-center px-1.5 py-0 rounded text-[10px] font-medium bg-green-100 text-green-800 border border-green-200 w-fit">
                                        <i class="fas fa-check-circle mr-0.5 text-[9px]"></i>
                                        OB
                                    </span>
                                    @if($bl->supir)
                                        <div class="text-[10px] text-gray-600">
                                            <span class="font-medium">{{ $bl->supir->nama_panggilan }}</span>
                                            @if($bl->supir->plat)
                                                <span class="text-gray-500">({{ $bl->supir->plat }})</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if($bl->tanggal_ob)
                                        <div class="text-[10px] text-gray-500">
                                            {{ $bl->tanggal_ob->format('d/m H:i') }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0 rounded text-[10px] font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                    <i class="fas fa-clock mr-0.5 text-[9px]"></i>
                                    Belum
                                </span>
                            @endif
                        </td>
                        <td class="px-1 py-1 text-xs text-gray-900">
                            @if($bl->sudah_tl)
                                <span class="inline-flex items-center px-1.5 py-0 rounded text-[10px] font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                    <i class="fas fa-exchange-alt mr-0.5 text-[9px]"></i>
                                    TL
                                </span>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0 rounded text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                    -
                                </span>
                            @endif
                        </td>
                        <td class="px-1 py-1 whitespace-nowrap text-xs font-medium">
                            <div class="flex items-center space-x-1">
                                @if(!$bl->sudah_ob)
                                    <button type="button" onclick="openSupirModal('bl', {{ $bl->id }})"
                                           class="text-green-600 hover:text-green-900 transition duration-150"
                                           title="Tandai sudah OB">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @if(!$bl->sudah_tl)
                                    <button type="button" onclick="prosesTLBongkar({{ $bl->id }})"
                                           class="text-purple-600 hover:text-purple-900 transition duration-150"
                                           title="Transfer Loading (TL) - Langsung Dibongkar">
                                        <i class="fas fa-exchange-alt"></i> TL
                                    </button>
                                    @endif
                                @else
                                    <button type="button" onclick="unmarkOB('bl', {{ $bl->id }})"
                                           class="text-yellow-600 hover:text-yellow-900 transition duration-150"
                                           title="Batalkan OB">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                @endif
                                <button type="button" onclick="openSupirModal('bl', {{ $bl->id }})"
                                       class="text-blue-600 hover:text-blue-900 transition duration-150"
                                       title="Input Supir OB">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                <a href="#" class="text-gray-600 hover:text-gray-900 transition duration-150"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14" class="px-4 py-8 text-center text-gray-500">Tidak ada data BL untuk kapal {{ $namaKapal }} voyage {{ $noVoyage }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @else
            <table class="min-w-full table-auto text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">
                            <input type="checkbox" id="select-all" class="mr-1">
                            <span class="text-[10px]">✓</span>
                        </th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">No</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">No. Kontainer</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">No. Seal</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Jenis Barang</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Asal</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Ke</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Tipe</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Size</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Tgl Muat</th>
                        {{-- Volume and Tonase columns removed per request --}}
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Status OB</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($naikKapals as $key => $naikKapal)
                        <tr class="hover:bg-gray-50 transition duration-150 {{ $naikKapal->tipe_kontainer == 'CARGO' ? 'bg-gray-100' : '' }}">
                            <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900">
                                <input type="checkbox" class="row-checkbox" value="{{ $naikKapal->id }}" data-type="naik_kapal" data-nomor-kontainer="{{ $naikKapal->nomor_kontainer }}" data-nama-barang="{{ $naikKapal->jenis_barang }}" data-tipe="{{ $naikKapal->tipe_kontainer }}" data-size="{{ $naikKapal->size_kontainer }}" data-biaya="{{ $naikKapal->biaya ?? '' }}" data-status="{{ $naikKapal->detected_status ?? 'full' }}" data-supir="{{ $naikKapal->supir ? ($naikKapal->supir->nama_panggilan ?? $naikKapal->supir->nama_lengkap ?? '') : '' }}" data-sudah-tl="{{ $naikKapal->sudah_tl ? '1' : '0' }}" data-sudah-ob="{{ $naikKapal->sudah_ob ? '1' : '0' }}" {{ $naikKapal->tipe_kontainer == 'CARGO' || $naikKapal->sudah_tl || !$naikKapal->sudah_ob ? 'disabled title="' . ($naikKapal->tipe_kontainer == 'CARGO' ? 'Kontainer CARGO' : ($naikKapal->sudah_tl ? 'Kontainer TL' : 'Kontainer belum OB')) . ' tidak bisa dimasukkan ke pranota"' : '' }}>
                                @if($naikKapal->tipe_kontainer == 'CARGO')
                                    <span class="text-[10px] text-red-600" title="Kontainer CARGO tidak bisa dimasukkan ke pranota">⚠️</span>
                                @elseif($naikKapal->sudah_tl)
                                    <span class="text-[10px] text-blue-600" title="Kontainer TL tidak bisa dimasukkan ke pranota">⚠️</span>
                                @elseif(!$naikKapal->sudah_ob)
                                    <span class="text-[10px] text-orange-600" title="Kontainer belum OB tidak bisa dimasukkan ke pranota">⚠️</span>
                                @endif
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900">
                                {{ $naikKapals->firstItem() + $key }}
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900 font-mono">
                                {{ $naikKapal->nomor_kontainer ?: '-' }}
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900 font-mono">
                                {{ $naikKapal->no_seal ?: ($naikKapal->prospek->no_seal ?? '-') }}
                            </td>
                            <td class="px-1 py-1 text-xs text-gray-900 max-w-xs truncate" title="{{ $naikKapal->jenis_barang }}">
                                {{ $naikKapal->jenis_barang ?: '-' }}
                            </td>
                            <td class="px-1 py-1 text-xs text-gray-900">
                                <div class="flex items-center gap-1">
                                    @if(request('kegiatan') === 'muat')
                                        <select class="editable-asal-kontainer select2-gudang w-full px-1 py-0.5 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                                data-id="{{ $naikKapal->id }}" 
                                                data-type="naik_kapal">
                                            <option value="">Pilih gudang...</option>
                                            @foreach($gudangs as $gudang)
                                                <option value="{{ $gudang->nama_gudang }}" {{ $naikKapal->asal_kontainer == $gudang->nama_gudang ? 'selected' : '' }}>
                                                    {{ $gudang->nama_gudang }} - {{ $gudang->lokasi }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" 
                                               class="editable-asal-kontainer w-full px-1 py-0.5 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                               data-id="{{ $naikKapal->id }}" 
                                               data-type="naik_kapal"
                                               value="{{ $naikKapal->asal_kontainer ?: (request('kegiatan') === 'bongkar' ? $namaKapal : '') }}"
                                               placeholder="Asal kontainer...">
                                    @endif
                                    <button onclick="saveAsalKe('naik_kapal', {{ $naikKapal->id }}, this.closest('td'))" 
                                            class="text-green-600 hover:text-green-900 transition duration-150"
                                            title="Simpan">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="px-1 py-1 text-xs text-gray-900">
                                <div class="flex items-center gap-1">
                                    <input type="text" 
                                           class="editable-ke w-full px-1 py-0.5 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                           data-id="{{ $naikKapal->id }}" 
                                           data-type="naik_kapal"
                                           value="{{ $naikKapal->ke ?: (request('kegiatan') === 'muat' ? $namaKapal : '') }}"
                                           placeholder="Tujuan...">
                                    <button onclick="saveAsalKe('naik_kapal', {{ $naikKapal->id }}, this.closest('td'))" 
                                            class="text-green-600 hover:text-green-900 transition duration-150"
                                            title="Simpan">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900">
                                @if($naikKapal->tipe_kontainer)
                                    @php
                                        $tipeUpper = strtoupper($naikKapal->tipe_kontainer);
                                        $tipeConfig = [
                                            'FCL' => ['color' => 'bg-purple-100 text-purple-800', 'icon' => 'fa-shipping-fast'],
                                            'LCL' => ['color' => 'bg-orange-100 text-orange-800', 'icon' => 'fa-box'],
                                            'CARGO' => ['color' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-truck']
                                        ];
                                        $config = $tipeConfig[$tipeUpper] ?? ['color' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-shipping-fast'];
                                    @endphp
                                    <span class="inline-flex items-center px-1 py-0 rounded text-[10px] font-medium {{ $config['color'] }}">
                                        {{ $tipeUpper }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900">
                                @if($naikKapal->size_kontainer)
                                    <span class="inline-flex items-center px-1 py-0 rounded text-[10px] font-medium
                                        {{ $naikKapal->size_kontainer == '20' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $naikKapal->size_kontainer }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900">
                                {{ $naikKapal->tanggal_muat ? $naikKapal->tanggal_muat->format('d/m/y') : '-' }}
                            </td>
                            {{-- Volume and Tonase cells removed per request --}}
                            <td class="px-1 py-1 text-xs text-gray-900">
                                @if($naikKapal->sudah_ob)
                                    <div class="flex flex-col space-y-1">
                                        <div class="flex items-center space-x-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200 w-fit">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Sudah OB
                                            </span>
                                            @if($naikKapal->is_tl)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200 w-fit">
                                                    <i class="fas fa-exchange-alt mr-1"></i>
                                                    TL
                                                </span>
                                            @endif
                                        </div>
                                        @if($naikKapal->supir)
                                            <div class="text-xs text-gray-600">
                                                <i class="fas fa-user mr-1"></i>
                                                <span class="font-medium">{{ $naikKapal->supir->nama_panggilan }}</span>
                                                @if($naikKapal->supir->plat)
                                                    <span class="text-gray-500">({{ $naikKapal->supir->plat }})</span>
                                                @endif
                                            </div>
                                        @endif
                                        @if($naikKapal->tanggal_ob)
                                            <div class="text-xs text-gray-500">
                                                <i class="fas fa-calendar mr-1"></i>
                                                {{ $naikKapal->tanggal_ob->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0 rounded text-[10px] font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <i class="fas fa-clock mr-0.5 text-[9px]"></i>
                                        Belum
                                    </span>
                                @endif
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap text-xs font-medium">
                                <div class="flex items-center space-x-2">
                                    @if(!$naikKapal->sudah_ob)
                                        <button type="button" onclick="openSupirModal('naik_kapal', {{ $naikKapal->id }})"
                                               class="text-green-600 hover:text-green-900 transition duration-150"
                                               title="Tandai sudah OB">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" onclick="prosesTL({{ $naikKapal->id }})"
                                               class="text-purple-600 hover:text-purple-900 transition duration-150"
                                               title="Transfer Loading (TL)">
                                            <i class="fas fa-exchange-alt"></i> TL
                                        </button>
                                    @else
                                        <button type="button" onclick="unmarkOB('naik_kapal', {{ $naikKapal->id }})"
                                               class="text-yellow-600 hover:text-yellow-900 transition duration-150"
                                               title="Batalkan OB">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @endif
                                    <button type="button" onclick="openSupirModal('naik_kapal', {{ $naikKapal->id }})"
                                           class="text-blue-600 hover:text-blue-900 transition duration-150"
                                           title="Input Supir OB">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                    <a href="#" class="text-gray-600 hover:text-gray-900 transition duration-150"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-4xl mb-3 text-gray-400"></i>
                                    <p class="text-lg font-medium">Tidak ada data kontainer yang ditemukan</p>
                                    <p class="text-sm text-gray-400 mt-1">Untuk kapal {{ $namaKapal }} voyage {{ $noVoyage }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @endif

        {{-- Modern Pagination --}}
        @if(isset($bls))
            @include('components.modern-pagination', ['paginator' => $bls])
        @else
            @include('components.modern-pagination', ['paginator' => $naikKapals])
        @endif
    </div>
</div>

<!-- Modal Pilih Supir -->
<div id="supirModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Pilih Supir</h3>
                <button type="button" onclick="closeSupirModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="formMarkOB" class="mt-4">
                <input type="hidden" id="record_type" name="record_type">
                <input type="hidden" id="record_id" name="record_id">
                
                <div class="mb-4">
                    <label for="supir_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Supir <span class="text-red-500">*</span>
                    </label>
                    <select id="supir_id" name="supir_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Supir</option>
                        @foreach($supirs as $supir)
                            <option value="{{ $supir->id }}">
                                {{ $supir->nama_panggilan }} - {{ $supir->nama_lengkap }}
                                @if($supir->plat)
                                    ({{ $supir->plat }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan (Opsional)
                    </label>
                    <textarea id="catatan" name="catatan" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 pt-3 border-t">
                    <button type="button" onclick="closeSupirModal()"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-md transition duration-200">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitOB"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition duration-200">
                        <i class="fas fa-check mr-2"></i>
                        Tandai Sudah OB
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Masuk Pranota -->
<div id="pranotaModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Masuk Pranota</h3>
                <button type="button" onclick="closePranotaModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-4">
                <p class="text-sm text-gray-700 mb-4">Berikut adalah detail kontainer yang akan dimasukkan ke pranota. Semua kontainer yang telah Anda pilih di semua halaman akan diproses.</p>
                
                <div class="mb-4">
                    <label for="nomor_pranota" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Pranota <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="text" id="nomor_pranota" name="nomor_pranota" required readonly
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Loading nomor pranota...">
                        <button type="button" onclick="generateNomorPranota()" 
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors"
                                title="Generate nomor baru">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Format: POB-MM-YY-000001 (auto-generate)</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table id="pranota-table" class="min-w-full table-auto border border-gray-300">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">No</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">No. Kontainer</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Nama Barang</th>
                                <!-- Tipe column removed intentionally -->
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Supir</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Size</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Biaya</th>
                        <tbody id="pranota-items" class="bg-white divide-y divide-gray-200">
                            <!-- Items will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
                <p id="total-count" class="text-sm font-semibold text-gray-900 mt-4"></p>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end gap-3 pt-3 border-t">
                <button type="button" onclick="closePranotaModal()"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-md transition duration-200">
                    Batal
                </button>
                <button type="button" id="btnConfirmPranota"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Konfirmasi Masuk Pranota
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Handle per page change
function changePerPage(perPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // Reset to page 1 when changing per page
    window.location.href = url.toString();
}

function openSupirModal(type, id) {
    document.getElementById('record_type').value = type;
    document.getElementById('record_id').value = id;
    document.getElementById('supir_id').value = '';
    document.getElementById('catatan').value = '';
    document.getElementById('supirModal').classList.remove('hidden');
}

function closeSupirModal() {
    document.getElementById('supirModal').classList.add('hidden');
    document.getElementById('formMarkOB').reset();
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('supirModal');
    if (event.target === modal) {
        closeSupirModal();
    }
});

// Pranota Modal functions
function openPranotaModal() {
    let selectedItems = getSelectedItems();
    
    // Filter out CARGO containers (by type or container number)
    const cargoItems = selectedItems.filter(item => {
        if (item.tipe === 'CARGO') return true;
        if (item.nomor_kontainer && item.nomor_kontainer.toUpperCase().includes('CARGO')) return true;
        return false;
    });
    
    // Filter out TL containers
    const tlItems = selectedItems.filter(item => item.sudah_tl === '1' || item.sudah_tl === true);
    
    // Filter out non-OB containers
    const nonObItems = selectedItems.filter(item => item.sudah_ob !== '1' && item.sudah_ob !== true);
    
    selectedItems = selectedItems.filter(item => {
        if (item.tipe === 'CARGO') return false;
        if (item.nomor_kontainer && item.nomor_kontainer.toUpperCase().includes('CARGO')) return false;
        if (item.sudah_tl === '1' || item.sudah_tl === true) return false;
        if (item.sudah_ob !== '1' && item.sudah_ob !== true) return false;
        return true;
    });
    
    // Show warning if CARGO, TL, or non-OB items were filtered out
    let warningMsg = '';
    if (cargoItems.length > 0) {
        warningMsg += `${cargoItems.length} kontainer CARGO tidak akan dimasukkan ke pranota.\nKontainer CARGO: ${cargoItems.map(item => item.nomor_kontainer).join(', ')}`;
    }
    if (tlItems.length > 0) {
        if (warningMsg) warningMsg += '\n\n';
        warningMsg += `${tlItems.length} kontainer TL tidak akan dimasukkan ke pranota.\nKontainer TL: ${tlItems.map(item => item.nomor_kontainer).join(', ')}`;
    }
    if (nonObItems.length > 0) {
        if (warningMsg) warningMsg += '\n\n';
        warningMsg += `${nonObItems.length} kontainer belum OB tidak akan dimasukkan ke pranota.\nKontainer belum OB: ${nonObItems.map(item => item.nomor_kontainer).join(', ')}`;
    }
    if (warningMsg) {
        alert(warningMsg);
    }
    
    // Ensure ship/voyage info available before opening modal
    if (!__PRANOTA_nama_kapal || !__PRANOTA_no_voyage) {
        alert('Informasi kapal dan voyage tidak ditemukan');
        return;
    }
    if (selectedItems.length === 0) {
        alert('Silakan pilih kontainer terlebih dahulu (non-CARGO)');
        return;
    }
    
    // Generate nomor pranota otomatis
    generateNomorPranota();
    
    const tbody = document.getElementById('pranota-items');
    tbody.innerHTML = '';
    
    // Set total count
    const totalP = document.getElementById('total-count');
    totalP.textContent = `Total kontainer yang dipilih: ${selectedItems.length}`;
    
    // Add all items to table
            selectedItems.forEach((item, index) => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
                let biayaDisplay = '';
                if (item.biaya === null || item.biaya === undefined || item.biaya === '') {
                    biayaDisplay = `<span class="text-red-600">Biaya belum diatur</span>`;
                } else {
                    const v = Number(item.biaya);
                    biayaDisplay = `Rp ${v.toLocaleString('id-ID')}`;
                }

            row.innerHTML = `
            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">${index + 1}</td>
            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 font-mono">${item.nomor_kontainer || '-'}</td>
            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">${item.nama_barang || '-'}</td>
            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">${item.supir || '-'}</td>
            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">${item.size ? item.size + ' Feet' : '-'}</td>
            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">${biayaDisplay}</td>
        `;
        
        tbody.appendChild(row);
    });
    
    document.getElementById('pranotaModal').classList.remove('hidden');
}

function closePranotaModal() {
    document.getElementById('pranotaModal').classList.add('hidden');
    document.getElementById('nomor_pranota').value = '';
}

// Generate nomor pranota otomatis
function generateNomorPranota() {
    const nomorInput = document.getElementById('nomor_pranota');
    nomorInput.value = 'Loading...';
    
    fetch('/ob/generate-nomor-pranota', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            nomorInput.value = data.nomor_pranota;
        } else {
            alert(data.message || 'Gagal generate nomor pranota');
            nomorInput.value = '';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat generate nomor pranota');
        nomorInput.value = '';
    });
}

// Close pranota modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('pranotaModal');
    if (event.target === modal) {
        closePranotaModal();
    }
});

// Normalize nomor_kontainer input while typing
document.addEventListener('DOMContentLoaded', function() {
    const containerInput = document.getElementById('nomor_kontainer');
    if (containerInput) {
        containerInput.addEventListener('input', function(e) {
            // Uppercase and remove non-alphanumeric characters (spaces, dashes, dots, etc.)
            let v = e.target.value.toUpperCase();
            v = v.replace(/[^A-Z0-9]/g, '');
            e.target.value = v;
        });
        // Normalize initial value if present
        if (containerInput.value && containerInput.value.length > 0) {
            containerInput.value = containerInput.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        }
    }

    // Initialize Select2 for gudang dropdown
    if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
        jQuery('.select2-gudang').select2({
            placeholder: 'Pilih gudang asal...',
            allowClear: true,
            width: '100%'
        });
    }

    // Handle bulk Asal Kontainer and Ke update
    const bulkAsalInput = document.getElementById('bulk_asal_kontainer');
    const bulkKeInput = document.getElementById('bulk_ke');

    if (bulkAsalInput) {
        // Handle both SELECT and INPUT elements
        const eventType = bulkAsalInput.tagName === 'SELECT' ? 'change' : 'input';
        bulkAsalInput.addEventListener(eventType, function(e) {
            const value = bulkAsalInput.tagName === 'SELECT' 
                ? (bulkAsalInput.options[bulkAsalInput.selectedIndex]?.value || '')
                : e.target.value;
            
            // Determine which table is present and target the correct column
            const tables = document.querySelectorAll('tbody tr');
            tables.forEach(row => {
                if (row.querySelector('.fa-inbox')) return; // Skip empty state row
                
                // Check if this is BL table (has No. BL column) or Naik Kapal table
                const cells = row.querySelectorAll('td');
                if (cells.length === 13) {
                    // BL table: Asal Kontainer is column 7 (index 6)
                    if (cells[6]) cells[6].textContent = value || '-';
                } else if (cells.length === 12) {
                    // Naik Kapal table: Asal Kontainer is column 6 (index 5)
                    if (cells[5]) cells[5].textContent = value || '-';
                }
            });
        });
    }

    if (bulkKeInput) {
        bulkKeInput.addEventListener('input', function(e) {
            const value = e.target.value;
            // Determine which table is present and target the correct column
            const tables = document.querySelectorAll('tbody tr');
            tables.forEach(row => {
                if (row.querySelector('.fa-inbox')) return; // Skip empty state row
                
                // Check if this is BL table (has No. BL column) or Naik Kapal table
                const cells = row.querySelectorAll('td');
                if (cells.length === 13) {
                    // BL table: Ke is column 8 (index 7)
                    if (cells[7]) cells[7].textContent = value || '-';
                } else if (cells.length === 12) {
                    // Naik Kapal table: Ke is column 7 (index 6)
                    if (cells[6]) cells[6].textContent = value || '-';
                }
            });
        });
    }
});

// Handle form submission
document.getElementById('formMarkOB').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const recordType = document.getElementById('record_type').value;
    const recordId = document.getElementById('record_id').value;
    const supirId = document.getElementById('supir_id').value;
    const catatan = document.getElementById('catatan').value;
    
    if (!supirId) {
        alert('Silakan pilih supir terlebih dahulu');
        return;
    }
    
    const btnSubmit = document.getElementById('btnSubmitOB');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
    
    // Determine endpoint based on record type
    let endpoint = '/ob/mark-as-ob';
    let requestData = {
        naik_kapal_id: recordId,
        supir_id: supirId,
        catatan: catatan
    };
    
    if (recordType === 'bl') {
        endpoint = '/ob/mark-as-ob-bl';
        requestData = {
            bl_id: recordId,
            supir_id: supirId,
            catatan: catatan
        };
    }
    
    // Send AJAX request
    fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Server error response:', text);
                throw new Error(`HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Reload page to show updated status
            window.location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan');
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fas fa-check mr-2"></i>Tandai Sudah OB';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data: ' + error.message);
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="fas fa-check mr-2"></i>Tandai Sudah OB';
    });
});

function unmarkOB(type, id) {
    if (confirm('Apakah Anda yakin ingin membatalkan status OB kontainer ini?')) {
        // Determine endpoint based on type
        let endpoint = '/ob/unmark-ob';
        let requestData = { naik_kapal_id: id };
        
        if (type === 'bl') {
            endpoint = '/ob/unmark-ob-bl';
            requestData = { bl_id: id };
        }
        
        // Send AJAX request to unmark
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(requestData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload page to show updated status
                window.location.reload();
            } else {
                alert(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat membatalkan OB');
        });
    }
}

// Function to process TL (Tanda Langsung) - Langsung dimuat tanpa supir
function prosesTL(naikKapalId) {
    if (!confirm('Proses TL (Tanda Langsung)?\n\nKontainer akan langsung dimuat dan ditandai sebagai OB tanpa supir.\n\nProses ini akan:\n- Membuat record BL baru\n- Menandai sebagai sudah OB\n- Ditandai TL untuk audit trail')) {
        return;
    }
    
    // Send AJAX request langsung tanpa modal
    fetch('/ob/process-tl', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            naik_kapal_id: naikKapalId
        })
    })
    .then(response => {
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('Non-JSON response:', text);
                throw new Error('Server mengembalikan response yang tidak valid. Cek console untuk detail.');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Proses TL berhasil! Kontainer sudah masuk ke BLS dan ditandai sebagai OB');
            window.location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan saat memproses TL');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses TL: ' + error.message);
    });
}

// Function to process TL Bongkar (Tanda Langsung) - Langsung dibongkar tanpa supir
function prosesTLBongkar(blId) {
    if (!confirm('Proses TL Bongkar (Tanda Langsung)?\n\nKontainer akan langsung dibongkar dan ditandai sebagai OB tanpa supir.\n\nProses ini akan:\n- Menandai BL sebagai sudah OB\n- Ditandai TL untuk audit trail\n- TIDAK membuat record BL baru')) {
        return;
    }
    
    // Send AJAX request langsung tanpa modal
    fetch('/ob/process-tl-bongkar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            bl_id: blId
        })
    })
    .then(response => {
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('Non-JSON response:', text);
                throw new Error('Server mengembalikan response yang tidak valid. Cek console untuk detail.');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Proses TL Bongkar berhasil! Kontainer sudah ditandai sebagai OB');
            window.location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan saat memproses TL Bongkar');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses TL Bongkar: ' + error.message);
    });
}

// Handle bulk actions
const checkboxes = document.querySelectorAll('.row-checkbox');
const bulkActions = document.getElementById('bulk-actions');
const selectAll = document.getElementById('select-all');

// Storage key based on current page
const storageKey = `selected_ob_{{ $namaKapal }}_{{ $noVoyage }}`;

function getSelectedItems() {
    const stored = localStorage.getItem(storageKey);
    if (!stored) return [];
    
    // Parse and filter out CARGO containers (by type or container number), TL containers, and non-OB containers
    const items = JSON.parse(stored);
    return items.filter(item => {
        // Filter by type
        if (item.tipe === 'CARGO') return false;
        // Filter by container number containing 'CARGO'
        if (item.nomor_kontainer && item.nomor_kontainer.toUpperCase().includes('CARGO')) return false;
        // Filter by TL status
        if (item.sudah_tl === '1' || item.sudah_tl === true) return false;
        // Filter by OB status
        if (item.sudah_ob !== '1' && item.sudah_ob !== true) return false;
        return true;
    });
}

function saveSelectedItems(items) {
    localStorage.setItem(storageKey, JSON.stringify(items));
}

function loadSelectedCheckboxes() {
    const selectedItems = getSelectedItems();
    checkboxes.forEach(cb => {
        // Skip disabled checkboxes (CARGO or TL)
        if (cb.disabled) {
            cb.checked = false; // Ensure CARGO and TL are never checked
            return;
        }
        
        // Skip if container number contains CARGO
        const nomorKontainer = cb.getAttribute('data-nomor-kontainer');
        if (nomorKontainer && nomorKontainer.toUpperCase().includes('CARGO')) {
            cb.checked = false;
            return;
        }
        
        // Skip if TL
        const sudahTl = cb.getAttribute('data-sudah-tl');
        if (sudahTl === '1' || sudahTl === 'true') {
            cb.checked = false;
            return;
        }
        
        // Skip if not OB
        const sudahOb = cb.getAttribute('data-sudah-ob');
        if (sudahOb !== '1' && sudahOb !== 'true') {
            cb.checked = false;
            return;
        }
        
        const item = selectedItems.find(item => item.id === cb.value);
        if (item && item.tipe !== 'CARGO' && item.sudah_tl !== '1' && item.sudah_tl !== true && (item.sudah_ob === '1' || item.sudah_ob === true)) {
            cb.checked = true;
        }
    });
    checkSelected();
}

function checkSelected() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const selected = document.querySelectorAll('.row-checkbox:checked');
    bulkActions.classList.toggle('hidden', selected.length === 0);
    
    // Update select all for current page (exclude disabled checkboxes from count)
    const currentPageCheckboxes = Array.from(document.querySelectorAll('.row-checkbox')).filter(cb => !cb.disabled);
    const currentPageSelected = Array.from(document.querySelectorAll('.row-checkbox:checked')).filter(cb => !cb.disabled);
    selectAll.checked = currentPageSelected.length === currentPageCheckboxes.length && currentPageCheckboxes.length > 0;
    selectAll.indeterminate = currentPageSelected.length > 0 && currentPageSelected.length < currentPageCheckboxes.length;
    
    // Save to storage - collect all selected items (exclude CARGO, TL, and non-OB)
    const allSelected = Array.from(document.querySelectorAll('.row-checkbox:checked'))
        .filter(cb => {
            // Filter by type
            if (cb.getAttribute('data-tipe') === 'CARGO') return false;
            // Filter by container number containing 'CARGO'
            const nomorKontainer = cb.getAttribute('data-nomor-kontainer');
            if (nomorKontainer && nomorKontainer.toUpperCase().includes('CARGO')) return false;
            // Filter by TL status
            const sudahTl = cb.getAttribute('data-sudah-tl');
            if (sudahTl === '1' || sudahTl === 'true') return false;
            // Filter by OB status
            const sudahOb = cb.getAttribute('data-sudah-ob');
            if (sudahOb !== '1' && sudahOb !== 'true') return false;
            return true;
        })
        .map(cb => ({
            id: cb.value,
            type: cb.getAttribute('data-type'),
            nomor_kontainer: cb.getAttribute('data-nomor-kontainer'),
            nama_barang: cb.getAttribute('data-nama-barang'),
            tipe: cb.getAttribute('data-tipe'),
            size: cb.getAttribute('data-size'),
            biaya: cb.getAttribute('data-biaya'),
            status: cb.getAttribute('data-status'),
            supir: cb.getAttribute('data-supir'),
            sudah_tl: cb.getAttribute('data-sudah-tl'),
            sudah_ob: cb.getAttribute('data-sudah-ob')
        }));
    saveSelectedItems(allSelected);
}

checkboxes.forEach(cb => cb.addEventListener('change', checkSelected));

selectAll.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => {
        // Only check/uncheck if checkbox is not disabled (skip CARGO)
        if (!cb.disabled) {
            cb.checked = this.checked;
        }
    });
    checkSelected();
});

// Clean CARGO, TL, and non-OB from localStorage on page load
function cleanCargoFromStorage() {
    const stored = localStorage.getItem(storageKey);
    if (stored) {
        const items = JSON.parse(stored);
        const cleanedItems = items.filter(item => {
            // Filter by type
            if (item.tipe === 'CARGO') return false;
            // Filter by container number containing 'CARGO'
            if (item.nomor_kontainer && item.nomor_kontainer.toUpperCase().includes('CARGO')) return false;
            // Filter by TL status
            if (item.sudah_tl === '1' || item.sudah_tl === true) return false;
            // Filter by OB status
            if (item.sudah_ob !== '1' && item.sudah_ob !== true) return false;
            return true;
        });
        if (cleanedItems.length !== items.length) {
            // CARGO, TL, or non-OB found and removed, update storage
            localStorage.setItem(storageKey, JSON.stringify(cleanedItems));
            console.log(`Removed ${items.length - cleanedItems.length} CARGO/TL/non-OB containers from selection`);
        }
    }
}

// Load selected on page load
document.addEventListener('DOMContentLoaded', function() {
    cleanCargoFromStorage(); // Clean first
    loadSelectedCheckboxes(); // Then load
});

document.getElementById('btnMasukPranota').addEventListener('click', function() {
    openPranotaModal();
});

// Server expects nama_kapal and no_voyage; provide them and validate client-side
const __PRANOTA_nama_kapal = @json($namaKapal ?? null);
const __PRANOTA_no_voyage = @json($noVoyage ?? null);

document.getElementById('btnConfirmPranota').addEventListener('click', function() {
    const selectedItems = getSelectedItems();
    if (selectedItems.length === 0) {
        alert('Silakan pilih kontainer terlebih dahulu');
        return;
    }
    
    const nomorPranota = document.getElementById('nomor_pranota').value.trim();
    if (!nomorPranota) {
        alert('Silakan masukkan nomor pranota');
        return;
    }
    
    const items = selectedItems.map(item => ({ id: item.id, type: item.type, nomor_kontainer: item.nomor_kontainer, nama_barang: item.nama_barang, size: item.size, biaya: item.biaya, status: item.status, supir: item.supir }));
    
    // client-side validation for ship/voyage information
    if (!__PRANOTA_nama_kapal || !__PRANOTA_no_voyage) {
        alert('Informasi kapal dan voyage tidak ditemukan');
        return;
    }

    const btnConfirm = document.getElementById('btnConfirmPranota');
    btnConfirm.disabled = true;
    btnConfirm.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
    
    // Send to pranota endpoint
    fetch('/ob/masuk-pranota', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ items: items, nomor_pranota: nomorPranota, nama_kapal: __PRANOTA_nama_kapal, no_voyage: __PRANOTA_no_voyage })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Berhasil memasukkan ke pranota');
            closePranotaModal();
            // Clear storage after success
            localStorage.removeItem(storageKey);
            window.location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan');
            btnConfirm.disabled = false;
            btnConfirm.innerHTML = '<i class="fas fa-plus mr-2"></i>Konfirmasi Masuk Pranota';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses');
        btnConfirm.disabled = false;
        btnConfirm.innerHTML = '<i class="fas fa-plus mr-2"></i>Konfirmasi Masuk Pranota';
    });
});

// Function to save individual Asal Kontainer and Ke
function saveAsalKe(type, id, tdElement) {
    const row = tdElement.closest('tr');
    const asalInput = row.querySelector('.editable-asal-kontainer');
    const keInput = row.querySelector('.editable-ke');
    
    // Get value from input or select element
    // For Select2 elements, use jQuery to get the value
    let asalValue = '';
    if (asalInput) {
        if (asalInput.tagName === 'SELECT' && typeof jQuery !== 'undefined') {
            // Use jQuery for Select2 elements
            asalValue = jQuery(asalInput).val() || '';
        } else if (asalInput.tagName === 'SELECT') {
            // Fallback for native select
            asalValue = asalInput.options[asalInput.selectedIndex]?.value || '';
        } else {
            // For input elements
            asalValue = asalInput.value || '';
        }
        asalValue = asalValue.trim();
    }
    
    let keValue = '';
    if (keInput) {
        if (keInput.tagName === 'SELECT' && typeof jQuery !== 'undefined') {
            keValue = jQuery(keInput).val() || '';
        } else if (keInput.tagName === 'SELECT') {
            keValue = keInput.options[keInput.selectedIndex]?.value || '';
        } else {
            keValue = keInput.value || '';
        }
        keValue = keValue.trim();
    }
    
    // Show loading state
    const saveBtn = tdElement.querySelector('button');
    const originalBtnHtml = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    // Send AJAX request
    fetch('/ob/save-asal-ke', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            type: type,
            id: id,
            asal_kontainer: asalValue || null,
            ke: keValue || null
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Show success feedback
            saveBtn.innerHTML = '<i class="fas fa-check text-green-600"></i>';
            setTimeout(() => {
                saveBtn.innerHTML = originalBtnHtml;
                saveBtn.disabled = false;
            }, 1500);
        } else {
            alert(result.message || 'Terjadi kesalahan saat menyimpan');
            saveBtn.innerHTML = originalBtnHtml;
            saveBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data');
        saveBtn.innerHTML = originalBtnHtml;
        saveBtn.disabled = false;
    });
}

// Handle Save Asal Kontainer and Ke (Bulk)
document.getElementById('btnSaveAsalKe').addEventListener('click', function() {
    const bulkAsalElement = document.getElementById('bulk_asal_kontainer');
    const bulkKeElement = document.getElementById('bulk_ke');
    
    // Get value from SELECT or INPUT
    const bulkAsalValue = bulkAsalElement.tagName === 'SELECT'
        ? (bulkAsalElement.options[bulkAsalElement.selectedIndex]?.value || '').trim()
        : bulkAsalElement.value.trim();
    const bulkKeValue = bulkKeElement.value.trim();
    
    // Check if bulk values are provided
    if (!bulkAsalValue && !bulkKeValue) {
        alert('Silakan isi Asal Kontainer atau Ke terlebih dahulu');
        return;
    }
    
    const confirmMsg = `Apakah Anda yakin ingin mengubah data dengan:\n` +
                      `Asal Kontainer: ${bulkAsalValue || '(tidak berubah)'}\n` +
                      `Ke: ${bulkKeValue || '(tidak berubah)'}\n\n` +
                      `Kapal: {{ $namaKapal }}\n` +
                      `Voyage: {{ $noVoyage }}\n` +
                      `Kegiatan: {{ request("kegiatan") ?: "Semua" }}\n\n` +
                      `Ini akan mengubah SEMUA data yang sesuai dengan filter di atas (termasuk yang tidak terlihat karena pagination).`;
    
    if (!confirm(confirmMsg)) {
        return;
    }
    
    const btnSave = document.getElementById('btnSaveAsalKe');
    btnSave.disabled = true;
    btnSave.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
    
    // Send bulk values along with current filters
    const requestData = {
        bulk_asal_kontainer: bulkAsalValue || null,
        bulk_ke: bulkKeValue || null,
        nama_kapal: '{{ $namaKapal }}',
        no_voyage: '{{ $noVoyage }}',
        kegiatan: '{{ request("kegiatan") }}',
        status_ob: '{{ request("status_ob") }}',
        tipe_kontainer: '{{ request("tipe_kontainer") }}',
        size_kontainer: '{{ request("size_kontainer") }}',
        search: '{{ request("search") }}',
        nomor_kontainer: '{{ request("nomor_kontainer") }}'
    };
    
    fetch('/ob/save-asal-ke-bulk', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(`Berhasil menyimpan ${result.updated_count} data Asal Kontainer dan Ke`);
            window.location.reload();
        } else {
            alert(result.message || 'Terjadi kesalahan saat menyimpan');
            btnSave.disabled = false;
            btnSave.innerHTML = '<i class="fas fa-save mr-2"></i>Simpan Asal & Ke';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data');
        btnSave.disabled = false;
        btnSave.innerHTML = '<i class="fas fa-save mr-2"></i>Simpan Asal & Ke';
    });
});
</script>

@endsection
