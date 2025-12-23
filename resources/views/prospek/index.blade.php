@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center">
            <i class="fas fa-shipping-fast mr-3 text-blue-600 text-2xl"></i>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data Prospek</h1>
                <p class="text-gray-600">Daftar prospek pengiriman kontainer</p>
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

    {{-- Filter Section --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('prospek.index') }}">
            {{-- Preserve current per_page selection if present so filter submissions don't reset rows-per-page --}} 
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
                              placeholder="No. Surat Jalan, Nama supir, kontainer, barang, pengirim..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="sudah_muat" {{ request('status') == 'sudah_muat' ? 'selected' : '' }}>Sudah Muat</option>
                        <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>

                {{-- Tipe Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Kontainer</label>
                    <select name="tipe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tipe</option>
                        <option value="FCL" {{ request('tipe') == 'FCL' ? 'selected' : '' }}>FCL</option>
                        <option value="LCL" {{ request('tipe') == 'LCL' ? 'selected' : '' }}>LCL</option>
                        <option value="CARGO" {{ request('tipe') == 'CARGO' ? 'selected' : '' }}>CARGO</option>
                    </select>
                </div>

                {{-- Ukuran Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran</label>
                    <select name="ukuran" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Ukuran</option>
                        <option value="20" {{ request('ukuran') == '20' ? 'selected' : '' }}>20 Feet</option>
                        <option value="40" {{ request('ukuran') == '40' ? 'selected' : '' }}>40 Feet</option>
                    </select>
                </div>

                {{-- Tujuan Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tujuan</label>
                    <input type="text"
                           name="tujuan"
                           value="{{ request('tujuan') }}"
                           placeholder="Tujuan pengiriman..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex justify-between items-center mt-4">
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('prospek.export-excel', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Excel
                    </a>
                    <a href="{{ route('prospek.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Reset
                    </a>
                </div>
                
                {{-- Tombol Naik Kapal untuk prospek aktif --}}
                <div class="flex gap-2">
                    <button type="button" onclick="openScanModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-file-upload mr-2"></i>
                        Scan Surat Jalan
                    </button>
                    <a href="{{ route('prospek.pilih-tujuan') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-ship mr-2"></i>
                        Naik Kapal
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
                <table class="min-w-full table-auto resizable-table" id="prospekTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">No<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">No. Surat Jalan<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Tanggal<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Tanggal Checkpoint<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Nama Supir<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Barang<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">PT/Pengirim<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Tipe<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Ukuran<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">No. Kontainer<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">No. Seal<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">No. Seal<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Tujuan<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Status<div class="resize-handle"></div></th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($prospeks as $key => $prospek)
                        <tr class="transition duration-150 {{ $prospek->status == 'aktif' ? 'bg-blue-50 hover:bg-blue-100' : ($prospek->status == 'sudah_muat' ? 'bg-green-50 hover:bg-green-100' : 'hover:bg-gray-50') }}">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prospeks->firstItem() + $key }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                {{ $prospek->no_surat_jalan ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prospek->tanggal ? (is_string($prospek->tanggal) ? \Carbon\Carbon::parse($prospek->tanggal)->format('d/M/Y') : $prospek->tanggal->format('d/M/Y')) : '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($prospek->suratJalan && $prospek->suratJalan->tanggal_checkpoint)
                                    {{ is_string($prospek->suratJalan->tanggal_checkpoint) ? \Carbon\Carbon::parse($prospek->suratJalan->tanggal_checkpoint)->format('d/M/Y') : $prospek->suratJalan->tanggal_checkpoint->format('d/M/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prospek->nama_supir ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prospek->barang ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prospek->pt_pengirim ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($prospek->tipe)
                                    @php
                                        $tipeUpper = strtoupper($prospek->tipe);
                                        $tipeConfig = [
                                            'FCL' => ['color' => 'bg-purple-100 text-purple-800', 'icon' => 'fa-shipping-fast'],
                                            'LCL' => ['color' => 'bg-orange-100 text-orange-800', 'icon' => 'fa-box'],
                                            'CARGO' => ['color' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-truck']
                                        ];
                                        $config = $tipeConfig[$tipeUpper] ?? ['color' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-shipping-fast'];
                                    @endphp 
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['color'] }}">
                                        <i class="fas {{ $config['icon'] }} mr-1"></i>
                                        {{ $tipeUpper }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($prospek->ukuran)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $prospek->ukuran == '20' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        <i class="fas fa-box mr-1"></i>
                                        {{ $prospek->ukuran }} Feet
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                @if($prospek->suratJalan && $prospek->suratJalan->no_kontainer != $prospek->nomor_kontainer)
                                    <div class="flex items-center gap-2">
                                        <span class="text-red-600" title="Data tidak sinkron dengan surat jalan">
                                            {{ $prospek->nomor_kontainer ?? '-' }}
                                        </span>
                                        <i class="fas fa-exclamation-triangle text-yellow-500 text-xs" 
                                           title="Data berbeda dengan surat jalan: {{ $prospek->suratJalan->no_kontainer }}"></i>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Surat Jalan: {{ $prospek->suratJalan->no_kontainer }}
                                    </div>
                                @else
                                    {{ $prospek->nomor_kontainer ?? '-' }}
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                {{ $prospek->no_seal ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                @can('prospek-edit')
                                    <div class="seal-edit-container" data-prospek-id="{{ $prospek->id }}">
                                        <span class="seal-display cursor-pointer hover:bg-gray-100 px-2 py-1 rounded border-2 border-transparent hover:border-blue-300 transition-all duration-200" 
                                              title="Klik untuk edit seal">
                                            {{ $prospek->no_seal ?? 'Klik untuk edit' }}
                                        </span>
                                        <input type="text" 
                                               class="seal-input hidden w-full px-2 py-1 border border-blue-500 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                               value="{{ $prospek->no_seal ?? '' }}"
                                               placeholder="Masukkan nomor seal">
                                        <div class="seal-buttons mt-1 gap-1" style="display: none;">
                                            <button class="save-seal bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs transition-colors">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="cancel-seal bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded text-xs transition-colors">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <span class="font-mono">{{ $prospek->no_seal ?? '-' }}</span>
                                @endcan
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prospek->tujuan_pengiriman ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                @php
                                    $statusConfig = [
                                        'aktif' => [
                                            'color' => 'bg-green-100 text-green-800 border-green-200',
                                            'icon' => 'fa-check-circle',
                                            'label' => 'Aktif'
                                        ],
                                        'sudah_muat' => [
                                            'color' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'icon' => 'fa-ship',
                                            'label' => 'Sudah Muat'
                                        ],
                                        'batal' => [
                                            'color' => 'bg-red-100 text-red-800 border-red-200',
                                            'icon' => 'fa-times-circle',
                                            'label' => 'Batal'
                                        ]
                                    ];
                                    $config = $statusConfig[$prospek->status] ?? [
                                        'color' => 'bg-gray-100 text-gray-800 border-gray-200',
                                        'icon' => 'fa-question-circle',
                                        'label' => $prospek->status
                                    ];
                                @endphp
                                <div class="space-y-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $config['color'] }}">
                                        <i class="fas {{ $config['icon'] }} mr-1"></i>
                                        {{ $config['label'] }}
                                    </span>
                                    @if($prospek->status == 'sudah_muat' && $prospek->no_voyage)
                                        <div class="mt-1">
                                            @if($prospek->bls && $prospek->bls->count() > 0)
                                                <a href="{{ route('bl.index', ['search' => $prospek->no_voyage]) }}" 
                                                   class="inline-flex items-center px-2 py-0.5 text-xs font-medium text-blue-700 bg-blue-50 rounded hover:bg-blue-100 border border-blue-200 transition-colors duration-150"
                                                   title="Lihat data BL">
                                                    <i class="fas fa-file-alt mr-1"></i>
                                                    Voyage: {{ $prospek->no_voyage }}
                                                    <i class="fas fa-external-link-alt ml-1 text-[10px]"></i>
                                                </a>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium text-gray-600 bg-gray-50 rounded border border-gray-200">
                                                    <i class="fas fa-ship mr-1"></i>
                                                    Voyage: {{ $prospek->no_voyage }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('prospek.show', $prospek->id) }}"
                                       class="text-blue-600 hover:text-blue-900 transition duration-150"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('prospek-edit')
                                        @if($prospek->suratJalan)
                                            <button type="button"
                                                    class="sync-prospek text-purple-600 hover:text-purple-900 transition duration-150"
                                                    data-prospek-id="{{ $prospek->id }}"
                                                    title="Sinkronkan dari Surat Jalan">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        @endif
                                        <div class="relative status-dropdown-container">
                                            <button type="button"
                                                    class="text-green-600 hover:text-green-900 transition duration-150 status-dropdown-btn"
                                                    data-prospek-id="{{ $prospek->id }}"
                                                    data-current-status="{{ $prospek->status }}"
                                                    title="Ubah Status">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                            <div class="status-dropdown hidden absolute right-0 mt-1 w-40 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                                <div class="py-1">
                                                    <button type="button" class="change-status w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 transition duration-150" data-status="aktif">
                                                        <i class="fas fa-check-circle text-green-600 mr-2"></i>Aktif
                                                    </button>
                                                    <button type="button" class="change-status w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 transition duration-150" data-status="sudah_muat">
                                                        <i class="fas fa-ship text-blue-600 mr-2"></i>Sudah Muat
                                                    </button>
                                                    <button type="button" class="change-status w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-50 transition duration-150" data-status="batal">
                                                        <i class="fas fa-times-circle text-red-600 mr-2"></i>Batal
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                    @can('prospek-delete')
                                        <button type="button"
                                                class="delete-prospek text-red-600 hover:text-red-900 transition duration-150"
                                                data-prospek-id="{{ $prospek->id }}"
                                                data-no-surat-jalan="{{ $prospek->no_surat_jalan }}"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-4xl mb-3 text-gray-400"></i>
                                    <p class="text-lg font-medium">Tidak ada data prospek yang ditemukan</p>
                                    <p class="text-sm text-gray-400 mt-1">Silakan tambah data prospek baru atau ubah filter pencarian</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($prospeks->hasPages())
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                @include('components.modern-pagination', ['paginator' => $prospeks])
                @include('components.rows-per-page')
            </div>
        @endif
    </div>

    {{-- Summary Cards --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Total Prospek (Belum Muat) --}}
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-hourglass-half text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Prospek</p>
                    <p class="text-xs text-gray-400">Belum dimuat ke kapal</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalBelumMuat }}</p>
                </div>
            </div>
        </div>

        {{-- Sudah Muat --}}
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                    <i class="fas fa-ship text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Sudah Muat</p>
                    <p class="text-xs text-gray-400">Dimuat ke kapal</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalSudahMuat }}</p>
                </div>
            </div>
        </div>

        {{-- Batal --}}
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                    <i class="fas fa-times-circle text-2xl text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Batal</p>
                    <p class="text-xs text-gray-400">Tidak jadi dimuat</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalBatal }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Scan Surat Jalan --}}
<div id="scanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-file-upload mr-2 text-purple-600"></i>
                    Scan Surat Jalan dari Excel
                </h3>
                <button type="button" onclick="closeScanModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="mb-4 p-4 bg-purple-50 border border-purple-200 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-purple-800">Petunjuk Upload</h3>
                        <div class="mt-2 text-sm text-purple-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Upload file Excel berisi daftar nomor surat jalan</li>
                                <li>Kolom pertama harus berisi nomor surat jalan</li>
                                <li>Sistem akan mencari surat jalan yang sesuai</li>
                                <li>Data BL terkait akan diupdate menjadi status "Sudah Muat"</li>
                                <li>Format file yang didukung: .xlsx, .xls, .csv</li>
                                <li>Maksimal ukuran file: 5 MB</li>
                            </ul>
                            <div class="mt-3">
                                <a href="{{ asset('templates/template_scan_surat_jalan.csv') }}" download class="inline-flex items-center text-purple-600 hover:text-purple-800 font-medium">
                                    <i class="fas fa-download mr-1"></i> Download Template Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form id="scanForm" action="{{ route('prospek.scan-surat-jalan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih File Excel <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-purple-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="excel_file" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                    <span>Upload file</span>
                                    <input id="excel_file" name="excel_file" type="file" class="sr-only" accept=".xlsx,.xls,.csv" onchange="showScanFileName(this)" required>
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                XLSX, XLS, CSV maksimal 5MB
                            </p>
                            <p id="scan-file-name" class="text-sm text-gray-700 font-medium mt-2"></p>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeScanModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" id="btnScanSubmit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <i class="fas fa-search mr-1"></i> Scan & Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Global functions for modal
function openScanModal() {
    document.getElementById('scanModal').classList.remove('hidden');
}

function closeScanModal() {
    document.getElementById('scanModal').classList.add('hidden');
    document.getElementById('excel_file').value = '';
    document.getElementById('scan-file-name').textContent = '';
}

function showScanFileName(input) {
    const fileName = input.files[0]?.name || '';
    document.getElementById('scan-file-name').textContent = fileName ? `File: ${fileName}` : '';
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('scanModal');
    if (e.target === modal) {
        closeScanModal();
    }
});

// Handle form submission with AJAX
document.getElementById('scanForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('btnScanSubmit');
    const fileInput = document.getElementById('excel_file');
    
    // Validasi file dipilih
    if (!fileInput.files || fileInput.files.length === 0) {
        alert('Silakan pilih file Excel terlebih dahulu');
        return;
    }
    
    const formData = new FormData(this);
    
    // Pastikan CSRF token terkirim dengan benar
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                      document.querySelector('input[name="_token"]')?.value;
    
    if (csrfToken) {
        formData.set('_token', csrfToken);
    }
    
    // Debug: log file yang akan diupload
    console.log('File to upload:', fileInput.files[0]);
    console.log('CSRF Token:', csrfToken);
    console.log('FormData entries:', Array.from(formData.entries()));
    
    // Change button state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...';
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
            // Jangan set Content-Type, biar browser yang set otomatis dengan boundary
        }
    })
    .then(response => {
        // Handle both success and error responses
        if (!response.ok) {
            return response.json().then(data => {
                throw { status: response.status, data: data };
            });
        }
        return response.json();
    })
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-search mr-1"></i> Scan & Update';
        
        if (data.success) {
            // Show success message with details
            let message = data.message;
            if (data.data) {
                message += `\n\nDetail:\n`;
                message += `- Total dipindai: ${data.data.total_scanned}\n`;
                message += `- Ditemukan: ${data.data.found}\n`;
                message += `- Tidak ditemukan: ${data.data.not_found}\n`;
                message += `- Prospek diupdate: ${data.data.prospek_updated}\n`;
                message += `- BL diupdate: ${data.data.bl_updated}`;
                
                if (data.data.not_found_numbers && data.data.not_found_numbers.length > 0) {
                    message += `\n\nTidak ditemukan:\n${data.data.not_found_numbers.join('\n')}`;
                }
            }
            
            alert(message);
            closeScanModal();
            
            // Reload page to show updated data
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Terjadi kesalahan'));
        }
    })
    .catch(error => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-search mr-1"></i> Scan & Update';
        
        console.error('Error:', error);
        
        // Handle validation errors (422)
        if (error.status === 422 && error.data && error.data.errors) {
            let errorMessage = 'Validasi gagal:\n\n';
            Object.keys(error.data.errors).forEach(key => {
                errorMessage += `- ${error.data.errors[key].join(', ')}\n`;
            });
            alert(errorMessage);
        } else if (error.data && error.data.message) {
            alert('Error: ' + error.data.message);
        } else if (error.message) {
            alert('Terjadi kesalahan saat memproses file: ' + error.message);
        } else {
            alert('Terjadi kesalahan saat memproses file. Silakan coba lagi.');
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Handle seal inline editing
    const sealContainers = document.querySelectorAll('.seal-edit-container');
    
    sealContainers.forEach(container => {
        const prospekId = container.dataset.prospekId;
        const sealDisplay = container.querySelector('.seal-display');
        const sealInput = container.querySelector('.seal-input');
        const sealButtons = container.querySelector('.seal-buttons');
        const saveBtn = container.querySelector('.save-seal');
        const cancelBtn = container.querySelector('.cancel-seal');
        
        let originalValue = sealInput.value;
        
        // Enter edit mode
        sealDisplay.addEventListener('click', function() {
            sealDisplay.style.display = 'none';
            sealInput.classList.remove('hidden');
            sealButtons.style.display = 'flex';
            sealInput.focus();
            sealInput.select();
            originalValue = sealInput.value;
        });
        
        // Cancel edit
        cancelBtn.addEventListener('click', function() {
            exitEditMode();
            sealInput.value = originalValue;
        });
        
        // Save edit
        saveBtn.addEventListener('click', function() {
            saveSeal(prospekId, sealInput.value, container);
        });
        
        // Save on Enter key
        sealInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                saveSeal(prospekId, sealInput.value, container);
            } else if (e.key === 'Escape') {
                exitEditMode();
                sealInput.value = originalValue;
            }
        });
        
        // Exit edit mode on blur (with delay to allow button clicks)
        sealInput.addEventListener('blur', function() {
            setTimeout(() => {
                if (!container.querySelector('.save-seal:hover') && !container.querySelector('.cancel-seal:hover')) {
                    exitEditMode();
                    sealInput.value = originalValue;
                }
            }, 100);
        });
        
        function exitEditMode() {
            sealDisplay.style.display = 'inline';
            sealInput.classList.add('hidden');
            sealButtons.style.display = 'none';
        }
    });
    
    function saveSeal(prospekId, newValue, container) {
        const saveBtn = container.querySelector('.save-seal');
        const originalText = saveBtn.innerHTML;
        
        // Show loading
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        saveBtn.disabled = true;
        
        // Prepare form data
        const formData = new FormData();
        formData.append('no_seal', newValue);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('_method', 'PATCH');
        
        fetch(`/prospek/${prospekId}/update-seal`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update display
                const sealDisplay = container.querySelector('.seal-display');
                sealDisplay.textContent = data.data.no_seal || 'Klik untuk edit';
                
                // Exit edit mode
                exitEditMode(container);
                
                // Show success message
                showToast('success', 'Nomor seal berhasil diperbarui');
            } else {
                throw new Error(data.error || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', error.message || 'Terjadi kesalahan saat mengupdate seal');
            
            // Reset input value
            const sealInput = container.querySelector('.seal-input');
            sealInput.value = sealInput.dataset.originalValue || '';
        })
        .finally(() => {
            // Reset button
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        });
    }
    
    function exitEditMode(container) {
        const sealDisplay = container.querySelector('.seal-display');
        const sealInput = container.querySelector('.seal-input');
        const sealButtons = container.querySelector('.seal-buttons');
        
        sealDisplay.style.display = 'inline';
        sealInput.classList.add('hidden');
        sealButtons.style.display = 'none';
    }
    
    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transition-all duration-300 ${
            type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'
        }`;
        
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-lg leading-none hover:opacity-75">&times;</button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            }
        }, 3000);
    }
    
    // Handle sync prospek from surat jalan
    document.querySelectorAll('.sync-prospek').forEach(button => {
        button.addEventListener('click', function() {
            const prospekId = this.dataset.prospekId;
            
            if (confirm('Sinkronkan data prospek dari surat jalan?\n\nData nomor kontainer, supir, barang, pengirim, dan tujuan akan diperbarui sesuai dengan data di surat jalan.')) {
                // Show loading
                const originalIcon = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;
                
                // Prepare form data
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                fetch(`/prospek/${prospekId}/sync-from-surat-jalan`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', data.message || 'Data prospek berhasil disinkronkan');
                        
                        // Reload page to show updated data
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        throw new Error(data.error || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', error.message || 'Terjadi kesalahan saat sinkronisasi');
                    
                    // Reset button
                    this.innerHTML = originalIcon;
                    this.disabled = false;
                });
            }
        });
    });
    
    // Handle delete prospek
    document.querySelectorAll('.delete-prospek').forEach(button => {
        button.addEventListener('click', function() {
            const prospekId = this.dataset.prospekId;
            const noSuratJalan = this.dataset.noSuratJalan;
            
            if (confirm(`Apakah Anda yakin ingin menghapus prospek dengan No. Surat Jalan: ${noSuratJalan}?\n\nData yang dihapus tidak dapat dikembalikan.`)) {
                // Show loading
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;
                
                // Prepare form data
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('_method', 'DELETE');
                
                fetch(`/prospek/${prospekId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', data.message || 'Prospek berhasil dihapus');
                        
                        // Remove row from table with animation
                        const row = this.closest('tr');
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-20px)';
                        setTimeout(() => {
                            row.remove();
                            
                            // Reload page if no more data
                            const tbody = document.querySelector('tbody');
                            const dataRows = tbody.querySelectorAll('tr:not(:has(td[colspan]))');
                            if (dataRows.length === 0) {
                                window.location.reload();
                            }
                        }, 300);
                    } else {
                        throw new Error(data.error || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', error.message || 'Terjadi kesalahan saat menghapus prospek');
                    
                    // Reset button
                    this.innerHTML = '<i class="fas fa-trash"></i>';
                    this.disabled = false;
                });
            }
        });
    });
    
    // Handle status dropdown
    document.querySelectorAll('.status-dropdown-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const container = this.closest('.status-dropdown-container');
            const dropdown = container.querySelector('.status-dropdown');
            const currentStatus = this.dataset.currentStatus;
            
            // Close all other dropdowns
            document.querySelectorAll('.status-dropdown').forEach(d => {
                if (d !== dropdown) d.classList.add('hidden');
            });
            
            // Toggle current dropdown
            dropdown.classList.toggle('hidden');
            
            // Disable current status option
            dropdown.querySelectorAll('.change-status').forEach(btn => {
                if (btn.dataset.status === currentStatus) {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            });
        });
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.status-dropdown-container')) {
            document.querySelectorAll('.status-dropdown').forEach(d => d.classList.add('hidden'));
        }
    });
    
    // Handle status change
    document.querySelectorAll('.change-status').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const container = this.closest('.status-dropdown-container');
            const statusBtn = container.querySelector('.status-dropdown-btn');
            const prospekId = statusBtn.dataset.prospekId;
            const newStatus = this.dataset.status;
            const currentStatus = statusBtn.dataset.currentStatus;
            
            const statusLabels = {
                'aktif': 'Aktif',
                'sudah_muat': 'Sudah Muat',
                'batal': 'Batal'
            };
            
            if (confirm(`Apakah Anda yakin ingin mengubah status menjadi "${statusLabels[newStatus]}"?`)) {
                // Show loading on button
                const originalIcon = statusBtn.innerHTML;
                statusBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                statusBtn.disabled = true;
                
                // Hide dropdown
                container.querySelector('.status-dropdown').classList.add('hidden');
                
                // Prepare form data
                const formData = new FormData();
                formData.append('status', newStatus);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('_method', 'PATCH');
                
                fetch(`/prospek/${prospekId}/update-status`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', `Status berhasil diubah menjadi ${statusLabels[newStatus]}`);
                        
                        // Update status display in table
                        const row = statusBtn.closest('tr');
                        const statusCell = row.querySelector('td:nth-last-child(2)');
                        
                        const statusConfig = {
                            'aktif': {
                                color: 'bg-green-100 text-green-800 border-green-200',
                                icon: 'fa-check-circle',
                                label: 'Aktif'
                            },
                            'sudah_muat': {
                                color: 'bg-blue-100 text-blue-800 border-blue-200',
                                icon: 'fa-ship',
                                label: 'Sudah Muat'
                            },
                            'batal': {
                                color: 'bg-red-100 text-red-800 border-red-200',
                                icon: 'fa-times-circle',
                                label: 'Batal'
                            }
                        };
                        
                        const config = statusConfig[newStatus];
                        statusCell.innerHTML = `
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${config.color}">
                                <i class="fas ${config.icon} mr-1"></i>
                                ${config.label}
                            </span>
                        `;
                        
                        // Update button's current status
                        statusBtn.dataset.currentStatus = newStatus;
                        
                        // Reload after delay to update summary cards
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        throw new Error(data.error || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', error.message || 'Terjadi kesalahan saat mengubah status');
                })
                .finally(() => {
                    // Reset button
                    statusBtn.innerHTML = originalIcon;
                    statusBtn.disabled = false;
                });
            }
        });
    });
});
</script>

@endsection

@include('components.resizable-table')

@push('scripts')
<script>
$(document).ready(function() {
    initResizableTable('prospekTable');
});
</script>
@endpush