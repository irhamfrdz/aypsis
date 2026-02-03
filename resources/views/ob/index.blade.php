@extends('layouts.app')

@if(isset($bls) && $bls->count() > 0)
@section('title', 'OB - Data Bongkaran')
@section('page_title', 'OB - Data Bongkaran')
@else
@section('title', 'OB - Data Naik Kapal')
@section('page_title', 'OB - Data Naik Kapal')
@endif

@push('styles')
<style>
    /* Custom Searchable Dropdown Styles */
    #supir_options_container {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f1f5f9;
    }
    
    #supir_options_container::-webkit-scrollbar {
        width: 8px;
    }
    
    #supir_options_container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    
    #supir_options_container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    
    #supir_options_container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    #supir_selected {
        user-select: none;
    }
    
    #supir_arrow {
        transition: transform 0.2s ease;
    }
    
    .supir-option {
        transition: all 0.15s ease;
    }
    
    .supir-option:active {
        background-color: #3b82f6 !important;
        color: white;
    }
    
    /* Mobile Optimization */
    @media (max-width: 768px) {
        #supir_search {
            font-size: 16px !important; /* Prevents zoom on iOS */
            -webkit-appearance: none;
            appearance: none;
        }
        
        .supir-option {
            min-height: 40px !important; /* Touch-friendly but compact */
            display: flex;
            align-items: center;
        }
        
        #supir_options_container {
            max-height: 180px !important;
        }
        
        /* Make modal more mobile-friendly */
        #supirModal > div {
            width: 95% !important;
            max-width: 95% !important;
            margin: 10px auto !important;
        }
    }
    
    /* General Select2 Styling (for other selects) */
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6 !important;
    }
    
    .select2-container--default .select2-selection--single {
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
    }
    
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #3b82f6 !important;
        outline: 2px solid transparent !important;
        outline-offset: 2px !important;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5) !important;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-3 md:p-6 mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center">
                <i class="fas fa-ship mr-2 md:mr-3 text-orange-600 text-xl md:text-2xl"></i>
                <div>
                    @if(isset($bls) && $bls->count() > 0)
                    <h1 class="text-lg md:text-2xl font-bold text-gray-800">OB - Data Bongkaran</h1>
                    @else
                    <h1 class="text-lg md:text-2xl font-bold text-gray-800">OB - Data Naik Kapal</h1>
                    @endif
                    <p class="text-xs md:text-sm text-gray-600">Kapal: <strong>{{ $namaKapal }}</strong> | Voyage: <strong>{{ $noVoyage }}</strong></p>
                    <p class="text-[10px] md:text-xs text-gray-500 mt-1">Last updated: {{ now()->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="window.location.reload()" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-md text-xs md:text-sm">
                    <i class="fas fa-sync-alt md:mr-2"></i><span class="hidden md:inline">Refresh Data</span>
                </button>
                <a href="{{ route('ob.print', array_merge(['nama_kapal' => $namaKapal, 'no_voyage' => $noVoyage], request()->only(['status_ob', 'tipe_kontainer', 'kegiatan']))) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-xs md:text-sm">
                    <i class="fas fa-print md:mr-2"></i><span class="hidden md:inline">Print</span>
                </a>
                <a href="{{ route('ob.export', array_merge(['nama_kapal' => $namaKapal, 'no_voyage' => $noVoyage], request()->only(['status_ob', 'tipe_kontainer', 'kegiatan', 'search']))) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded-md text-xs md:text-sm">
                    <i class="fas fa-file-excel md:mr-2"></i><span class="hidden md:inline">Export Excel</span>
                </a>
                <a href="{{ route('ob.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-md text-xs md:text-sm">
                    <i class="fas fa-arrow-left md:mr-2"></i><span class="hidden md:inline">Pilih Kapal Lain</span>
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4 mb-4 md:mb-6">
        <div class="bg-white rounded-lg shadow-sm p-3 md:p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-full p-2 md:p-3">
                    <i class="fas fa-boxes text-lg md:text-2xl text-blue-600"></i>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">Total Kontainer</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $totalKontainer }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-3 md:p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-full p-2 md:p-3">
                    <i class="fas fa-check text-lg md:text-2xl text-green-600"></i>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">Sudah OB</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $sudahOB }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-3 md:p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-full p-2 md:p-3">
                    <i class="fas fa-clock text-lg md:text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">Belum OB</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $belumOB }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-lg shadow-sm p-3 md:p-6 mb-4 md:mb-6">
        <form method="GET" action="{{ route('ob.index') }}">
            <input type="hidden" name="nama_kapal" value="{{ $namaKapal }}">
            <input type="hidden" name="no_voyage" value="{{ $noVoyage }}">
            @if(request()->has('kegiatan'))
                <input type="hidden" name="kegiatan" value="{{ request('kegiatan') }}">
            @endif
            @if(request()->has('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
            
            <div class="space-y-3 md:space-y-0 md:grid md:grid-cols-2 lg:grid-cols-6 md:gap-4">
                {{-- Search --}}
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Pencarian</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="No kontainer, seal, barang..."
                           class="w-full px-2 md:px-3 py-1.5 md:py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Tipe Kontainer Filter --}}
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Tipe Kontainer</label>
                    <select name="tipe_kontainer"
                            class="w-full px-2 md:px-3 py-1.5 md:py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tipe</option>
                        <option value="FCL" {{ request('tipe_kontainer') == 'FCL' ? 'selected' : '' }}>FCL</option>
                        <option value="LCL" {{ request('tipe_kontainer') == 'LCL' ? 'selected' : '' }}>LCL</option>
                        <option value="CARGO" {{ request('tipe_kontainer') == 'CARGO' ? 'selected' : '' }}>CARGO</option>
                    </select>
                </div>

                {{-- Status OB Filter --}}
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Status OB</label>
                    <select name="status_ob"
                            class="w-full px-2 md:px-3 py-1.5 md:py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="sudah" {{ request('status_ob') == 'sudah' ? 'selected' : '' }}>Sudah OB</option>
                        <option value="belum" {{ request('status_ob') == 'belum' ? 'selected' : '' }}>Belum OB</option>
                    </select>
                </div>

                {{-- Asal Kontainer Input --}}
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Asal Kontainer (Semua)</label>
                    @if(request('kegiatan') === 'muat')
                        <select id="bulk_asal_kontainer"
                                class="select2-gudang w-full px-2 md:px-3 py-1.5 md:py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                               value="{{ $namaKapal }}"
                               placeholder="Isi untuk mengubah semua"
                               class="w-full px-2 md:px-3 py-1.5 md:py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @endif
                    <p class="text-[10px] md:text-xs text-gray-500 mt-0.5 md:mt-1">Isi untuk mengubah semua asal kontainer</p>
                </div>

                {{-- Ke Input --}}
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Ke (Semua)</label>
                    <select id="bulk_ke"
                            class="select2-gudang w-full px-2 md:px-3 py-1.5 md:py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Isi untuk mengubah semua</option>
                        @foreach($gudangs as $gudang)
                            <option value="{{ $gudang->nama_gudang }}">
                                {{ $gudang->nama_gudang }} - {{ $gudang->lokasi }}
                            </option>
                        @endforeach
                        <!-- Add option for Nama Kapal if needed -->
                        @if($namaKapal)
                             <option value="{{ $namaKapal }}">{{ $namaKapal }}</option>
                        @endif
                    </select>
                    <p class="text-[10px] md:text-xs text-gray-500 mt-0.5 md:mt-1">Isi untuk mengubah tujuan semua kontainer</p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mt-3 md:mt-4">
                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="flex-1 md:flex-none bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-md transition duration-200 inline-flex items-center justify-center text-sm">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('ob.index', array_merge(['nama_kapal' => $namaKapal, 'no_voyage' => $noVoyage], request()->has('kegiatan') ? ['kegiatan' => request('kegiatan')] : [])) }}" class="flex-1 md:flex-none bg-gray-500 hover:bg-gray-600 text-white px-3 md:px-4 py-2 rounded-md transition duration-200 inline-flex items-center justify-center text-sm">
                        <i class="fas fa-times mr-2"></i>
                        Reset
                    </a>
                </div>
                <div class="w-full md:w-auto">
                    <button type="button" id="btnSaveAsalKe" class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white px-3 md:px-4 py-2 rounded-md transition duration-200 inline-flex items-center justify-center text-sm">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Asal & Ke
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Table Section --}}
    {{-- Bulk Actions --}}
    <div id="bulk-actions" class="hidden bg-gray-50 px-4 py-3 border-b border-gray-200 mb-4 rounded-lg">
        <button type="button" id="btnMasukPranota" class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-md text-sm">
            <i class="fas fa-plus mr-2"></i>Masukan ke Pranota
        </button>
    </div>
    
    {{-- Mobile Card View (visible only on mobile) --}}
    <div class="md:hidden space-y-3 mb-4">
        @if(isset($bls))
            @forelse($bls as $key => $bl)
                @php
                    $isTL = ($bl->sudah_tl === true || $bl->sudah_tl === 1 || $bl->sudah_tl === '1');
                    $isOB = ($bl->sudah_ob === true || $bl->sudah_ob === 1 || $bl->sudah_ob === '1');
                    $isCARGO = ($bl->tipe_kontainer == 'CARGO');
                    $shouldDisable = $isCARGO || !$isOB;
                    $barangUpper = strtoupper($bl->nama_barang ?? '');
                    $isEmpty = str_contains($barangUpper, 'EMPTY') || ($bl->tipe_kontainer == 'FCL' && (empty($bl->nomor_kontainer) || str_starts_with($bl->nomor_kontainer, 'CARGO-')));
                @endphp
                <div class="bg-white rounded-lg shadow-sm border {{ $isCARGO ? 'border-red-200 bg-gray-50' : 'border-gray-200' }} p-3">
                    {{-- Card Header with Checkbox and Status --}}
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-start gap-2">
                            <input type="checkbox" class="row-checkbox mt-1" value="{{ $bl->id }}" 
                                   data-type="bl" 
                                   data-nomor-kontainer="{{ $bl->nomor_kontainer }}" 
                                   data-nama-barang="{{ $bl->nama_barang }}" 
                                   data-tipe="{{ $bl->tipe_kontainer }}" 
                                   data-size="{{ $bl->size_kontainer }}" 
                                   data-biaya="{{ $bl->biaya ?? '' }}" 
                                   data-status="{{ $bl->detected_status ?? 'full' }}" 
                                   data-supir="{{ $bl->supir ? ($bl->supir->nama_panggilan ?? $bl->supir->nama_lengkap ?? '') : '' }}" 
                                   data-sudah-tl="{{ $isTL ? '1' : '0' }}" 
                                   data-sudah-ob="{{ $isOB ? '1' : '0' }}" 
                                   {{ $shouldDisable ? 'disabled' : '' }}>
                            <div>
                                <div class="font-mono text-sm font-bold text-gray-900">{{ $bl->nomor_kontainer ?: '-' }}</div>
                                <div class="text-xs text-gray-500">No. BL: {{ $bl->nomor_bl ?: '-' }}</div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            @if($isOB)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1 text-[10px]"></i>OB
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1 text-[10px]"></i>Belum
                                </span>
                            @endif
                            @if($isTL)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-exchange-alt mr-1 text-[10px]"></i>TL
                                </span>
                            @endif
                            <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $isEmpty ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $isEmpty ? 'Empty' : 'Full' }}
                            </span>
                        </div>
                    </div>
                    
                    {{-- Card Content --}}
                    <div class="space-y-1.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Barang:</span>
                            <span class="font-medium text-gray-900 text-right">{{ $bl->nama_barang ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pengirim:</span>
                            <span class="font-medium text-gray-900 text-right">{{ request('kegiatan') === 'muat' ? ($bl->prospek->pt_pengirim ?? $bl->prospek->pengirim ?? '-') : ($bl->pt_pengirim ?? $bl->pengirim ?? '-') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">No. Seal:</span>
                            <span class="font-mono text-gray-900">{{ $bl->no_seal ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tipe/Size:</span>
                            <span class="font-medium text-gray-900">{{ $bl->tipe_kontainer ?: '-' }} / {{ $bl->size_kontainer ?: '-' }}</span>
                        </div>
                        @if($isOB && $bl->supir)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Supir:</span>
                                <span class="font-medium text-gray-900">{{ $bl->supir->nama_panggilan }} {{ $bl->supir->plat ? '(' . $bl->supir->plat . ')' : '' }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">Created:</span>
                            <span class="text-gray-900">{{ $bl->created_at ? $bl->created_at->format('d/m/y') : '-' }}</span>
                        </div>
                    </div>
                    
                    {{-- Card Actions --}}
                    <div class="mt-3 pt-3 border-t border-gray-200 flex flex-wrap items-center gap-2">
                        @if(!$isOB)
                            <button type="button" onclick="openSupirModal('bl', {{ $bl->id }})"
                                   class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs font-medium">
                                <i class="fas fa-check mr-1"></i>Tandai OB
                            </button>
                            @if(!$isTL)
                            <button type="button" onclick="prosesTLBongkar({{ $bl->id }})"
                                   class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded text-xs font-medium">
                                <i class="fas fa-exchange-alt mr-1"></i>TL
                            </button>
                            @endif
                        @else
                            <button type="button" onclick="unmarkOB('bl', {{ $bl->id }})"
                                   class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1.5 rounded text-xs font-medium">
                                <i class="fas fa-undo mr-1"></i>Batalkan OB
                            </button>
                        @endif
                        @if($isTL)
                        <button type="button" onclick="clearTL('bl', {{ $bl->id }})"
                               class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-medium">
                            <i class="fas fa-times-circle mr-1"></i>Clear TL
                        </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2"></i>
                    <p>Tidak ada data BL untuk kapal {{ $namaKapal }} voyage {{ $noVoyage }}</p>
                </div>
            @endforelse
        @else
            @forelse($naikKapals as $key => $naikKapal)
                @php
                    $isTL = ($naikKapal->is_tl === true || $naikKapal->is_tl === 1 || $naikKapal->is_tl === '1');
                    $isOB = ($naikKapal->sudah_ob === true || $naikKapal->sudah_ob === 1 || $naikKapal->sudah_ob === '1');
                    $isCARGO = ($naikKapal->tipe_kontainer == 'CARGO');
                    $shouldDisable = $isCARGO || !$isOB;
                    $barangUpper = strtoupper($naikKapal->jenis_barang ?? '');
                    $isEmpty = str_contains($barangUpper, 'EMPTY');
                @endphp
                <div class="bg-white rounded-lg shadow-sm border {{ $isCARGO ? 'border-red-200 bg-gray-50' : 'border-gray-200' }} p-3">
                    {{-- Card Header with Checkbox and Status --}}
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-start gap-2">
                            <input type="checkbox" class="row-checkbox mt-1" value="{{ $naikKapal->id }}" 
                                   data-type="naik_kapal" 
                                   data-nomor-kontainer="{{ $naikKapal->nomor_kontainer }}" 
                                   data-nama-barang="{{ $naikKapal->jenis_barang }}" 
                                   data-tipe="{{ $naikKapal->tipe_kontainer }}" 
                                   data-size="{{ $naikKapal->size_kontainer }}" 
                                   data-biaya="{{ $naikKapal->biaya ?? '' }}" 
                                   data-status="{{ $naikKapal->detected_status ?? 'full' }}" 
                                   data-supir="{{ $naikKapal->supir ? ($naikKapal->supir->nama_panggilan ?? $naikKapal->supir->nama_lengkap ?? '') : '' }}" 
                                   data-sudah-tl="{{ $isTL ? '1' : '0' }}" 
                                   data-sudah-ob="{{ $isOB ? '1' : '0' }}" 
                                   {{ $shouldDisable ? 'disabled' : '' }}>
                            <div>
                                <div class="font-mono text-sm font-bold text-gray-900">{{ $naikKapal->nomor_kontainer ?: '-' }}</div>
                                <div class="text-xs text-gray-500">#{{ $naikKapals->firstItem() + $key }}</div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            @if($isOB)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1 text-[10px]"></i>OB
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1 text-[10px]"></i>Belum
                                </span>
                            @endif
                            @if($isTL)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-exchange-alt mr-1 text-[10px]"></i>TL
                                </span>
                            @endif
                            <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $isEmpty ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $isEmpty ? 'Empty' : 'Full' }}
                            </span>
                        </div>
                    </div>
                    
                    {{-- Card Content --}}
                    <div class="space-y-1.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Barang:</span>
                            <span class="font-medium text-gray-900 text-right">{{ $naikKapal->jenis_barang ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pengirim:</span>
                            <span class="font-medium text-gray-900 text-right">{{ request('kegiatan') === 'muat' ? ($naikKapal->prospek->pt_pengirim ?? $naikKapal->prospek->pengirim ?? '-') : ($naikKapal->pt_pengirim ?? $naikKapal->pengirim ?? '-') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">No. Seal:</span>
                            <span class="font-mono text-gray-900">{{ $naikKapal->no_seal ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tipe/Size:</span>
                            <span class="font-medium text-gray-900">{{ $naikKapal->tipe_kontainer ?: '-' }} / {{ $naikKapal->size_kontainer ?: '-' }}</span>
                        </div>
                        @if($isOB && $naikKapal->supir)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Supir:</span>
                                <span class="font-medium text-gray-900">{{ $naikKapal->supir->nama_panggilan }} {{ $naikKapal->supir->plat ? '(' . $naikKapal->supir->plat . ')' : '' }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tgl Muat:</span>
                            <span class="text-gray-900">{{ $naikKapal->created_at ? $naikKapal->created_at->format('d/m/y') : '-' }}</span>
                        </div>
                    </div>
                    
                    {{-- Card Actions --}}
                    <div class="mt-3 pt-3 border-t border-gray-200 flex flex-wrap items-center gap-2">
                        @if(!$isOB)
                            <button type="button" onclick="openSupirModal('naik_kapal', {{ $naikKapal->id }})"
                                   class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs font-medium">
                                <i class="fas fa-check mr-1"></i>Tandai OB
                            </button>
                            @if(!$isTL)
                            <button type="button" onclick="prosesTL({{ $naikKapal->id }})"
                                   class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded text-xs font-medium">
                                <i class="fas fa-exchange-alt mr-1"></i>TL
                            </button>
                            @endif
                        @else
                            <button type="button" onclick="unmarkOB('naik_kapal', {{ $naikKapal->id }})"
                                   class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1.5 rounded text-xs font-medium">
                                <i class="fas fa-undo mr-1"></i>Batalkan OB
                            </button>
                        @endif
                        @if($isTL)
                        <button type="button" onclick="clearTL('naik_kapal', {{ $naikKapal->id }})"
                               class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-medium">
                            <i class="fas fa-times-circle mr-1"></i>Clear TL
                        </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2"></i>
                    <p>Tidak ada data Naik Kapal untuk kapal {{ $namaKapal }} voyage {{ $noVoyage }}</p>
                </div>
            @endforelse
        @endif
    </div>
    
    {{-- Desktop Table View (hidden on mobile) --}}
    <div class="hidden md:block bg-white rounded-lg shadow-sm overflow-hidden">
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
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">PT. Pengirim</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Status</th>
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
                    @php
                        // Perkuat pengecekan TL dengan berbagai kemungkinan nilai
                        $isTL = ($bl->sudah_tl === true || $bl->sudah_tl === 1 || $bl->sudah_tl === '1');
                        $isOB = ($bl->sudah_ob === true || $bl->sudah_ob === 1 || $bl->sudah_ob === '1');
                        $isCARGO = ($bl->tipe_kontainer == 'CARGO');
                        $shouldDisable = $isCARGO || !$isOB; // TL containers can now be selected
                    @endphp
                    <tr class="hover:bg-gray-50 transition duration-150 {{ $isCARGO ? 'bg-gray-100' : '' }}">
                        <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900">
                            <input type="checkbox" class="row-checkbox" value="{{ $bl->id }}" data-type="bl" data-nomor-kontainer="{{ $bl->nomor_kontainer }}" data-nama-barang="{{ $bl->nama_barang }}" data-tipe="{{ $bl->tipe_kontainer }}" data-size="{{ $bl->size_kontainer }}" data-biaya="{{ $bl->biaya ?? '' }}" data-status="{{ $bl->detected_status ?? 'full' }}" data-supir="{{ $bl->supir ? ($bl->supir->nama_panggilan ?? $bl->supir->nama_lengkap ?? '') : '' }}" data-sudah-tl="{{ $isTL ? '1' : '0' }}" data-sudah-ob="{{ $isOB ? '1' : '0' }}" {{ $shouldDisable ? 'disabled title="' . ($isCARGO ? 'Kontainer CARGO tidak bisa dimasukkan ke pranota' : 'Kontainer belum OB tidak bisa dimasukkan ke pranota') . '"' : '' }}>
                            @if($isCARGO)
                                <span class="text-[10px] text-red-600" title="Kontainer CARGO tidak bisa dimasukkan ke pranota">⚠️</span>
                            @elseif($isTL)
                                <span class="text-[10px] text-blue-600" title="Kontainer TL - tidak ada biaya">ℹ️</span>
                            @elseif(!$isOB)
                                <span class="text-[10px] text-orange-600" title="Kontainer belum OB tidak bisa dimasukkan ke pranota">⚠️</span>
                            @endif
                        </td>
                        <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900">{{ $bls->firstItem() + $key }}</td>
                        <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900 font-mono">{{ $bl->nomor_bl ?: '-' }}</td>
                        <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900 font-mono">{{ $bl->nomor_kontainer ?: '-' }}</td>
                        <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900 font-mono">{{ $bl->no_seal ?: '-' }}</td>
                        <td class="px-1 py-1 text-xs text-gray-900 max-w-xs truncate" title="{{ $bl->nama_barang }}">{{ $bl->nama_barang ?: '-' }}</td>
                        <td class="px-1 py-1 text-xs text-gray-900 max-w-xs truncate" title="{{ request('kegiatan') === 'muat' ? ($bl->prospek->pt_pengirim ?? $bl->prospek->pengirim ?? '-') : ($bl->pt_pengirim ?? $bl->pengirim ?? '-') }}">
                            {{ request('kegiatan') === 'muat' ? ($bl->prospek->pt_pengirim ?? $bl->prospek->pengirim ?? '-') : ($bl->pt_pengirim ?? $bl->pengirim ?? '-') }}
                        </td>
                        <td class="px-1 py-1 whitespace-nowrap text-xs">
                            @php
                                $barangUpper = strtoupper($bl->nama_barang ?? '');
                                $isEmpty = str_contains($barangUpper, 'EMPTY') || ($bl->tipe_kontainer == 'FCL' && (empty($bl->nomor_kontainer) || str_starts_with($bl->nomor_kontainer, 'CARGO-')));
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $isEmpty ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $isEmpty ? 'E' : 'F' }}
                            </span>
                        </td>
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
                                <!-- CHANGED: Input text 'ke' replaced with select dropdown (editable-ke) -->
                                <select class="editable-ke select2-gudang w-full px-1 py-0.5 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                        data-id="{{ $bl->id }}" 
                                        data-type="bl">
                                    <option value="">Tujuan...</option>
                                    @php
                                        // Determine the selected value:
                                        // 1. Use existing $bl->ke value if present
                                        // 2. Fallback to $namaKapal if activity is 'muat' (similar to logic in original code, though context implies 'ke' might be destination)
                                        //    Original logic: $bl->ke ?: (request('kegiatan') === 'muat' ? $namaKapal : '')
                                        $selectedValue = $bl->ke ?: (request('kegiatan') === 'muat' ? $namaKapal : '');
                                    @endphp
                                    @foreach($gudangs as $gudang)
                                        <option value="{{ $gudang->nama_gudang }}" {{ $selectedValue == $gudang->nama_gudang ? 'selected' : '' }}>
                                            {{ $gudang->nama_gudang }} - {{ $gudang->lokasi }}
                                        </option>
                                    @endforeach
                                    <!-- Add option for Nama Kapal if it's the default but not in gudangs list, or generally available -->
                                    @if($namaKapal && $selectedValue == $namaKapal)
                                         <option value="{{ $namaKapal }}" selected>{{ $namaKapal }}</option>
                                    @elseif($namaKapal)
                                         <option value="{{ $namaKapal }}">{{ $namaKapal }}</option>
                                    @endif
                                </select>
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
                                @if($bl->sudah_tl)
                                <button type="button" onclick="clearTL('bl', {{ $bl->id }})"
                                       class="text-red-600 hover:text-red-900 transition duration-150"
                                       title="Hapus status TL">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                                @endif
                                <a href="#" class="text-gray-600 hover:text-gray-900 transition duration-150"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="15" class="px-4 py-8 text-center text-gray-500">Tidak ada data BL untuk kapal {{ $namaKapal }} voyage {{ $noVoyage }}</td>
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
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">PT. Pengirim</th>
                        <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Status</th>
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
                        @php
                            // Perkuat pengecekan TL dengan berbagai kemungkinan nilai
                            // NaikKapal uses field `is_tl` (boolean) while BL uses `sudah_tl`.
                            $isTL = ($naikKapal->is_tl === true || $naikKapal->is_tl === 1 || $naikKapal->is_tl === '1');
                            $isOB = ($naikKapal->sudah_ob === true || $naikKapal->sudah_ob === 1 || $naikKapal->sudah_ob === '1');
                            $isCARGO = ($naikKapal->tipe_kontainer == 'CARGO');
                            $shouldDisable = $isCARGO || !$isOB; // TL containers can now be selected
                        @endphp
                        <tr class="hover:bg-gray-50 transition duration-150 {{ $isCARGO ? 'bg-gray-100' : '' }}">
                            <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900">
                                <input type="checkbox" class="row-checkbox" value="{{ $naikKapal->id }}" data-type="naik_kapal" data-nomor-kontainer="{{ $naikKapal->nomor_kontainer }}" data-nama-barang="{{ $naikKapal->jenis_barang }}" data-tipe="{{ $naikKapal->tipe_kontainer }}" data-size="{{ $naikKapal->size_kontainer }}" data-biaya="{{ $naikKapal->biaya ?? '' }}" data-status="{{ $naikKapal->detected_status ?? 'full' }}" data-supir="{{ $naikKapal->supir ? ($naikKapal->supir->nama_panggilan ?? $naikKapal->supir->nama_lengkap ?? '') : '' }}" data-sudah-tl="{{ $isTL ? '1' : '0' }}" data-sudah-ob="{{ $isOB ? '1' : '0' }}" {{ $shouldDisable ? 'disabled title="' . ($isCARGO ? 'Kontainer CARGO tidak bisa dimasukkan ke pranota' : 'Kontainer belum OB tidak bisa dimasukkan ke pranota') . '"' : '' }}>
                                @if($isCARGO)
                                    <span class="text-[10px] text-red-600" title="Kontainer CARGO tidak bisa dimasukkan ke pranota">⚠️</span>
                                @elseif($isTL)
                                    <span class="text-[10px] text-blue-600" title="Kontainer TL - tidak ada biaya">ℹ️</span>
                                @elseif(!$isOB)
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
                            <td class="px-1 py-1 text-xs text-gray-900 max-w-xs truncate" title="{{ request('kegiatan') === 'muat' ? ($naikKapal->prospek->pt_pengirim ?? $naikKapal->prospek->pengirim ?? '-') : ($naikKapal->pt_pengirim ?? $naikKapal->pengirim ?? '-') }}">
                                {{ request('kegiatan') === 'muat' ? ($naikKapal->prospek->pt_pengirim ?? $naikKapal->prospek->pengirim ?? '-') : ($naikKapal->pt_pengirim ?? $naikKapal->pengirim ?? '-') }}
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap text-xs">
                                @php
                                    $barangUpper = strtoupper($naikKapal->jenis_barang ?? '');
                                    $isEmpty = str_contains($barangUpper, 'EMPTY') || ($naikKapal->tipe_kontainer == 'FCL' && (empty($naikKapal->nomor_kontainer) || str_starts_with($naikKapal->nomor_kontainer, 'CARGO-')));
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $isEmpty ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $isEmpty ? 'E' : 'F' }}
                                </span>
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
                                        @if($naikKapal->is_tl)
                                        <button type="button" onclick="clearTL('naik_kapal', {{ $naikKapal->id }})"
                                               class="text-red-600 hover:text-red-900 transition duration-150"
                                               title="Hapus status TL">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                        @endif
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
    </div>

    {{-- Pagination (visible on both mobile and desktop) --}}
    <div class="mt-4">
        @if(isset($bls))
            @include('components.modern-pagination', ['paginator' => $bls])
        @else
            @include('components.modern-pagination', ['paginator' => $naikKapals])
        @endif
    </div>
</div>

<!-- Modal Pilih Supir -->
<div id="supirModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-3 md:p-4 border w-[90%] md:w-80 shadow-lg rounded-md bg-white">
        <div class="mt-1">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-2 border-b">
                <h3 class="text-sm font-semibold text-gray-900">Pilih Supir</h3>
                <button type="button" onclick="closeSupirModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="formMarkOB" class="mt-4">
                <input type="hidden" id="record_type" name="record_type">
                <input type="hidden" id="record_id" name="record_id">
                
                <div class="mb-3">
                    <label for="supir_search" class="block text-xs font-medium text-gray-700 mb-1.5">
                        Supir <span class="text-red-500">*</span>
                    </label>
                    
                    <!-- Hidden input to store selected supir ID -->
                    <input type="hidden" id="supir_id" name="supir_id" required>
                    
                    <!-- Search Input -->
                    <input type="text" 
                           id="supir_search" 
                           placeholder="Search..." 
                           autocomplete="off"
                           class="w-full px-2 py-1.5 mb-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    
                    <!-- Dropdown Container -->
                    <div class="relative">
                        <!-- Selected Display (Clickable to toggle) -->
                        <div id="supir_selected" 
                             class="px-2 py-1.5 cursor-pointer hover:bg-gray-50 border border-gray-300 rounded-md text-xs text-gray-500 flex justify-between items-center">
                            <span id="supir_selected_text">Select an option</span>
                            <svg id="supir_arrow" class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        
                        <!-- Options List (Hidden by default) -->
                        <div id="supir_options_container" 
                             class="hidden absolute z-10 w-full mt-1 border border-gray-300 rounded-md bg-white max-h-48 overflow-y-auto shadow-lg">
                            <div id="supir_options" class="divide-y divide-gray-100">
                                @foreach($supirs as $supir)
                                    <div class="supir-option px-2 py-1.5 cursor-pointer hover:bg-blue-50 text-xs transition-colors" 
                                         data-value="{{ $supir->id }}"
                                         data-text="{{ $supir->nama_panggilan }} - {{ $supir->nama_lengkap }}{{ $supir->plat ? ' ('.$supir->plat.')' : '' }}">
                                        {{ $supir->nama_panggilan }} - {{ $supir->nama_lengkap }}
                                        @if($supir->plat)
                                            <span class="text-gray-500">({{ $supir->plat }})</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="ke_gudang_id" class="block text-xs font-medium text-gray-700 mb-1.5">
                        Ke <span class="text-red-500">*</span>
                    </label>
                    <select id="ke_gudang_id" name="ke_gudang_id" required
                            class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Lokasi Tujuan</option>
                        @foreach($gudangs as $gudang)
                            <option value="{{ $gudang->id }}">
                                {{ $gudang->nama_gudang }} {{ $gudang->lokasi ? '('.$gudang->lokasi.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="catatan" class="block text-xs font-medium text-gray-700 mb-1.5">
                        Catatan (Opsional)
                    </label>
                    <textarea id="catatan" name="catatan" rows="2"
                              class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>

                <div class="mb-3">
                    <label for="retur_barang" class="block text-xs font-medium text-gray-700 mb-1.5">
                        Retur Barang (Opsional)
                    </label>
                    <textarea id="retur_barang" name="retur_barang" rows="2"
                              class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Catatan retur barang untuk surat jalan bongkaran..."></textarea>
                    <p class="text-[10px] text-gray-500 mt-0.5">Akan disimpan di surat jalan bongkaran jika ada</p>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" onclick="closeSupirModal()"
                            class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium rounded-md transition duration-200">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitOB"
                            class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md transition duration-200">
                        <i class="fas fa-check mr-1"></i>
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tanggal_ob" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal OB <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="tanggal_ob" name="tanggal_ob" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label for="nomor_accurate" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Accurate
                        </label>
                        <input type="text" id="nomor_accurate" name="nomor_accurate"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Masukkan nomor accurate...">
                    </div>
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
    
    document.getElementById('catatan').value = '';
    document.getElementById('retur_barang').value = '';
    
    // Reset supir selection
    document.getElementById('supir_id').value = '';
    document.getElementById('supir_selected_text').textContent = 'Select an option';
    document.getElementById('supir_selected').classList.add('text-gray-500');
    document.getElementById('supir_selected').classList.remove('text-gray-900');
    document.getElementById('supir_search').value = '';
    
    // Show all options
    const options = document.querySelectorAll('.supir-option');
    options.forEach(opt => opt.style.display = 'block');
    
    // Close dropdown options
    const optionsContainer = document.getElementById('supir_options_container');
    if (optionsContainer) {
        optionsContainer.classList.add('hidden');
    }
    
    // Reset arrow
    const arrow = document.getElementById('supir_arrow');
    if (arrow) {
        arrow.style.transform = 'rotate(0deg)';
    }
    
    // Show modal
    document.getElementById('supirModal').classList.remove('hidden');
    
    // Focus on search input after modal is shown
    setTimeout(() => {
        document.getElementById('supir_search').focus();
    }, 100);
}

// Custom Searchable Dropdown for Supir
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('supir_search');
    const supirOptions = document.querySelectorAll('.supir-option');
    const supirSelected = document.getElementById('supir_selected');
    const supirSelectedText = document.getElementById('supir_selected_text');
    const supirIdInput = document.getElementById('supir_id');
    const supirOptionsContainer = document.getElementById('supir_options_container');
    const supirArrow = document.getElementById('supir_arrow');
    
    // Toggle dropdown visibility
    function toggleDropdown(show) {
        if (show) {
            supirOptionsContainer.classList.remove('hidden');
            supirArrow.style.transform = 'rotate(180deg)';
        } else {
            supirOptionsContainer.classList.add('hidden');
            supirArrow.style.transform = 'rotate(0deg)';
        }
    }
    
    // Click on selected to toggle
    if (supirSelected) {
        supirSelected.addEventListener('click', function(e) {
            e.stopPropagation();
            const isHidden = supirOptionsContainer.classList.contains('hidden');
            toggleDropdown(isHidden);
            if (isHidden) {
                searchInput.focus();
            }
        });
    }
    
    // Focus on search input opens dropdown
    if (searchInput) {
        searchInput.addEventListener('focus', function() {
            toggleDropdown(true);
        });
        
        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            // Open dropdown when typing
            toggleDropdown(true);
            
            supirOptions.forEach(option => {
                const text = option.getAttribute('data-text').toLowerCase();
                if (text.includes(searchTerm)) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        });
    }
    
    // Option selection
    supirOptions.forEach(option => {
        option.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            const text = this.getAttribute('data-text');
            
            // Update hidden input
            supirIdInput.value = value;
            
            // Update selected display
            supirSelectedText.textContent = text;
            supirSelected.classList.remove('text-gray-500');
            supirSelected.classList.add('text-gray-900');
            
            // Clear search
            searchInput.value = '';
            
            // Show all options again
            supirOptions.forEach(opt => opt.style.display = 'block');
            
            // Close dropdown
            toggleDropdown(false);
        });
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const isClickInside = supirSelected.contains(e.target) || 
                            supirOptionsContainer.contains(e.target) || 
                            searchInput.contains(e.target);
        
        if (!isClickInside) {
            toggleDropdown(false);
        }
    });
});

function closeSupirModal() {
    document.getElementById('supirModal').classList.add('hidden');
    document.getElementById('formMarkOB').reset();
    document.getElementById('ke_gudang_id').value = '';
    
    // Reset custom dropdown
    document.getElementById('supir_id').value = '';
    document.getElementById('supir_selected_text').textContent = 'Select an option';
    document.getElementById('supir_selected').classList.add('text-gray-500');
    document.getElementById('supir_selected').classList.remove('text-gray-900');
    document.getElementById('supir_search').value = '';
    
    // Show all options
    const options = document.querySelectorAll('.supir-option');
    options.forEach(opt => opt.style.display = 'block');
    
    // Close dropdown options
    const optionsContainer = document.getElementById('supir_options_container');
    if (optionsContainer) {
        optionsContainer.classList.add('hidden');
    }
    
    // Reset arrow
    const arrow = document.getElementById('supir_arrow');
    if (arrow) {
        arrow.style.transform = 'rotate(0deg)';
    }
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
    
    // Remove duplicates by id (safety check)
    selectedItems = Array.from(new Map(selectedItems.map(item => [item.id, item])).values());
    
    // Filter out CARGO containers (by type or container number)
    const cargoItems = selectedItems.filter(item => {
        if (item.tipe === 'CARGO') return true;
        if (item.nomor_kontainer && item.nomor_kontainer.toUpperCase().includes('CARGO')) return true;
        return false;
    });
    
    // Filter out non-OB containers
    const nonObItems = selectedItems.filter(item => item.sudah_ob !== '1' && item.sudah_ob !== true);
    
    selectedItems = selectedItems.filter(item => {
        if (item.tipe === 'CARGO') return false;
        if (item.nomor_kontainer && item.nomor_kontainer.toUpperCase().includes('CARGO')) return false;
        if (item.sudah_ob !== '1' && item.sudah_ob !== true) return false;
        return true;
    });
    
    // Show warning if CARGO or non-OB items were filtered out
    let warningMsg = '';
    if (cargoItems.length > 0) {
        warningMsg += `${cargoItems.length} kontainer CARGO tidak akan dimasukkan ke pranota.\nKontainer CARGO: ${cargoItems.map(item => item.nomor_kontainer).join(', ')}`;
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
                // Check if TL container first
                if (item.sudah_tl === '1' || item.sudah_tl === true) {
                    biayaDisplay = `<span class="text-blue-600">Tidak ada biaya (TL)</span>`;
                } else if (item.biaya === null || item.biaya === undefined || item.biaya === '') {
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
    document.getElementById('nomor_accurate').value = '';
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
    const keGudangId = document.getElementById('ke_gudang_id').value;
    const catatan = document.getElementById('catatan').value;
    const returBarang = document.getElementById('retur_barang').value;
    
    if (!supirId) {
        alert('Silakan pilih supir terlebih dahulu');
        return;
    }
    
    if (!keGudangId) {
        alert('Silakan pilih lokasi tujuan (Ke) terlebih dahulu');
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
        ke_gudang_id: keGudangId,
        catatan: catatan,
        retur_barang: returBarang
    };
    
    if (recordType === 'bl') {
        endpoint = '/ob/mark-as-ob-bl';
        requestData = {
            bl_id: recordId,
            supir_id: supirId,
            ke_gudang_id: keGudangId,
            catatan: catatan,
            retur_barang: returBarang
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

// Manual clear TL action (for BL or NaikKapal)
function clearTL(type, id) {
    if (!confirm('Hapus status TL untuk kontainer ini?\n\nIni akan menghapus flag TL (Tanda Langsung) sehingga kontainer bisa dimasukkan ke pranota atau diproses kembali.')) {
        return;
    }

    let endpoint = '/ob/clear-tl';
    let requestData = { naik_kapal_id: id };
    if (type === 'bl') {
        endpoint = '/ob/clear-tl-bl';
        requestData = { bl_id: id };
    }

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
            alert(data.message || 'Status TL berhasil dihapus');
            window.location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan saat menghapus status TL');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus status TL');
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
    
    // Parse and filter out CARGO containers (by type or container number) and non-OB containers
    const items = JSON.parse(stored);
    return items.filter(item => {
        // Filter by type
        if (item.tipe === 'CARGO') return false;
        // Filter by container number containing 'CARGO'
        if (item.nomor_kontainer && item.nomor_kontainer.toUpperCase().includes('CARGO')) return false;
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
        // Skip disabled checkboxes (CARGO)
        if (cb.disabled) {
            cb.checked = false; // Ensure CARGO is never checked
            return;
        }
        
        // Skip if container number contains CARGO
        const nomorKontainer = cb.getAttribute('data-nomor-kontainer');
        if (nomorKontainer && nomorKontainer.toUpperCase().includes('CARGO')) {
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
        if (item && item.tipe !== 'CARGO' && (item.sudah_ob === '1' || item.sudah_ob === true)) {
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
    const currentPageSelectedForUI = Array.from(document.querySelectorAll('.row-checkbox:checked')).filter(cb => !cb.disabled);
    selectAll.checked = currentPageSelectedForUI.length === currentPageCheckboxes.length && currentPageCheckboxes.length > 0;
    selectAll.indeterminate = currentPageSelectedForUI.length > 0 && currentPageSelectedForUI.length < currentPageCheckboxes.length;
    
    // Get existing selected items from storage
    let existingSelected = getSelectedItems();
    
    // Get all checkboxes on current page (both checked and unchecked)
    const currentPageIds = Array.from(document.querySelectorAll('.row-checkbox')).map(cb => cb.value);
    
    // Remove items from current page from existing selection (we'll re-add checked ones)
    existingSelected = existingSelected.filter(item => !currentPageIds.includes(item.id));
    
    // Add currently checked items from this page
    const currentPageCheckedItems = Array.from(document.querySelectorAll('.row-checkbox:checked'))
        .filter(cb => {
            // Filter by type
            if (cb.getAttribute('data-tipe') === 'CARGO') return false;
            // Filter by container number containing 'CARGO'
            const nomorKontainer = cb.getAttribute('data-nomor-kontainer');
            if (nomorKontainer && nomorKontainer.toUpperCase().includes('CARGO')) return false;
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
    
    // Merge existing items from other pages with current page selections
    const allSelected = [...existingSelected, ...currentPageCheckedItems];
    
    // Remove duplicates by id (just in case)
    const uniqueSelected = Array.from(new Map(allSelected.map(item => [item.id, item])).values());
    
    saveSelectedItems(uniqueSelected);
}

checkboxes.forEach(cb => {
    cb.addEventListener('change', function(e) {
        checkSelected();
    });
});

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

// Clean CARGO and non-OB from localStorage on page load
function cleanCargoFromStorage() {
    const stored = localStorage.getItem(storageKey);
    if (stored) {
        const items = JSON.parse(stored);
        const cleanedItems = items.filter(item => {
            // Filter by type
            if (item.tipe === 'CARGO') return false;
            // Filter by container number containing 'CARGO'
            if (item.nomor_kontainer && item.nomor_kontainer.toUpperCase().includes('CARGO')) return false;
            // Filter by OB status
            if (item.sudah_ob !== '1' && item.sudah_ob !== true) return false;
            return true;
        });
        if (cleanedItems.length !== items.length) {
            // CARGO or non-OB found and removed, update storage
            localStorage.setItem(storageKey, JSON.stringify(cleanedItems));
            console.log(`Removed ${items.length - cleanedItems.length} CARGO/non-OB containers from selection`);
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
    
    const tanggalOb = document.getElementById('tanggal_ob').value;
    if (!tanggalOb) {
        alert('Silakan pilih tanggal OB');
        return;
    }
    
    const nomorAccurate = document.getElementById('nomor_accurate').value.trim();
    
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
        body: JSON.stringify({ 
            items: items, 
            nomor_pranota: nomorPranota, 
            nama_kapal: __PRANOTA_nama_kapal, 
            no_voyage: __PRANOTA_no_voyage,
            tanggal_ob: tanggalOb,
            nomor_accurate: nomorAccurate
        })
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
