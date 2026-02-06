@extends('layouts.app')

@section('title', 'Stock Ban')
@section('page_title', 'Stock Ban')

@push('styles')
<style>
    .tab-btn {
        padding: 0.75rem 1rem;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        color: #6b7280;
    }
    .tab-btn:hover {
        color: #1f2937;
        border-color: #d1d5db;
    }
    .tab-btn.active {
        color: #2563eb;
        border-color: #2563eb;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }

    /* Refined Custom Dropdown & Form Styles */
    .dropdown-menu-custom {
        display: none;
        position: fixed;
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        z-index: 999999;
        max-height: 300px;
        overflow-y: auto;
        min-width: 250px;
        border: 1px solid #3b82f633;
    }
    .dropdown-search-container {
        position: sticky;
        top: 0;
        background-color: #f9fafb;
        padding: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
        z-index: 10;
    }
    .dropdown-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        color: #4b5563;
        font-size: 0.875rem;
        transition: all 0.2s;
        border-bottom: 1px solid #f3f4f6;
    }
    .dropdown-item:last-child {
        border-bottom: none;
    }
    .dropdown-item:hover {
        background-color: #eff6ff;
        color: #2563eb;
        padding-left: 1.25rem;
    }
    .dropdown-item.selected {
        background-color: #dbeafe;
        color: #1d4ed8;
        font-weight: 600;
    }

    /* Premium Input Styling */
    .form-input-premium {
        width: 100%;
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.625rem 1rem;
        color: #111827;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    .form-input-premium:focus {
        background-color: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }
    .form-label-premium {
        display: block;
        margin-bottom: 0.375rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
    }
    .btn-dropdown-premium {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.625rem 1rem;
        color: #111827;
        font-size: 0.875rem;
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-dropdown-premium:hover {
        border-color: #9ca3af;
    }
    .btn-dropdown-premium:focus {
        background-color: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .hidden {
        display: none !important;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
           <div class="flex space-x-1 border-b border-gray-200" id="tabs-container">
               <button class="tab-btn active" data-target="tab-ban-luar">Ban Luar</button>
               <button class="tab-btn" data-target="tab-ban-dalam">Ban Dalam</button>
               <button class="tab-btn" data-target="tab-ban-perut">Ban Perut</button>
               <button class="tab-btn" data-target="tab-lock-kontainer">Lock Kontainer</button>
               <button class="tab-btn" data-target="tab-ring-velg">Ring Velg</button>
               <button class="tab-btn" data-target="tab-velg">Velg</button>
           </div>
        </div>
        
        <div class="flex gap-2 items-center w-full md:w-auto">
            <!-- Search Box -->
            <div class="relative flex-1 md:flex-initial md:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" 
                       id="search-input" 
                       class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition"
                       placeholder="Cari nomor seri, merk, ukuran, lokasi...">
                <button type="button" 
                        id="clear-search" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                    <i class="fas fa-times text-gray-400 hover:text-gray-600 cursor-pointer"></i>
                </button>
            </div>
            
            <a href="{{ route('stock-ban.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-plus"></i> Tambah Stock
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Tab: Ban Luar -->
        <div id="tab-ban-luar" class="tab-content active p-4">
            <!-- Rekap Statistik Ban Luar -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-blue-600"></i>
                    Rekap Ban Luar
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <!-- Total Ban -->
                    <div id="card-total" onclick="setCardFilter('total')" class="cursor-pointer bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4 shadow-sm hover:shadow-md transition card-filter active-filter ring-2 ring-blue-400">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-blue-600 uppercase">Total Ban</span>
                            <i class="fas fa-circle-notch text-blue-400 text-lg"></i>
                        </div>
                        <div class="text-2xl font-bold text-blue-900">{{ $stockBans->count() }}</div>
                        <p class="text-xs text-blue-600 mt-1">Unit</p>
                    </div>
                    
                    <!-- Ban Stok -->
                    @php
                        $banStok = $stockBans->where('status', 'Stok')->count();
                    @endphp
                    <div id="card-stok" onclick="setCardFilter('stok')" class="cursor-pointer bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-4 shadow-sm hover:shadow-md transition card-filter">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-green-600 uppercase">Stok</span>
                            <i class="fas fa-box text-green-400 text-lg"></i>
                        </div>
                        <div class="text-2xl font-bold text-green-900">{{ $banStok }}</div>
                        <p class="text-xs text-green-600 mt-1">Tersedia</p>
                    </div>
                    
                    <!-- Ban Terpakai -->
                    @php
                        $banTerpakai = $stockBans->where('status', 'Terpakai')->count();
                    @endphp
                    <div id="card-terpakai" onclick="setCardFilter('terpakai')" class="cursor-pointer bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-4 shadow-sm hover:shadow-md transition card-filter">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-purple-600 uppercase">Terpakai</span>
                            <i class="fas fa-check-circle text-purple-400 text-lg"></i>
                        </div>
                        <div class="text-2xl font-bold text-purple-900">{{ $banTerpakai }}</div>
                        <p class="text-xs text-purple-600 mt-1">Terpasang</p>
                    </div>
                    
                    <!-- Ban Asli -->
                    @php
                        $banAsli = $stockBans->where('kondisi', 'asli')->count();
                    @endphp
                    <div id="card-asli" onclick="setCardFilter('asli')" class="cursor-pointer bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200 rounded-lg p-4 shadow-sm hover:shadow-md transition card-filter">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-emerald-600 uppercase">Asli</span>
                            <i class="fas fa-star text-emerald-400 text-lg"></i>
                        </div>
                        <div class="text-2xl font-bold text-emerald-900">{{ $banAsli }}</div>
                        <p class="text-xs text-emerald-600 mt-1">Original</p>
                    </div>
                    
                    <!-- Ban Kanisir -->
                    @php
                        $banKanisir = $stockBans->where('kondisi', 'kanisir')->count();
                    @endphp
                    <div id="card-kanisir" onclick="setCardFilter('kanisir')" class="cursor-pointer bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 rounded-lg p-4 shadow-sm hover:shadow-md transition card-filter">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-yellow-600 uppercase">Kanisir</span>
                            <i class="fas fa-recycle text-yellow-400 text-lg"></i>
                        </div>
                        <div class="text-2xl font-bold text-yellow-900">{{ $banKanisir }}</div>
                        <p class="text-xs text-yellow-600 mt-1">Remelted</p>
                    </div>
                    
                    <!-- Ban Afkir -->
                    @php
                        $banAfkir = $stockBans->where('kondisi', 'afkir')->count();
                    @endphp
                    <div id="card-afkir" onclick="setCardFilter('afkir')" class="cursor-pointer bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-lg p-4 shadow-sm hover:shadow-md transition card-filter">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-red-600 uppercase">Afkir</span>
                            <i class="fas fa-times-circle text-red-400 text-lg"></i>
                        </div>
                        <div class="text-2xl font-bold text-red-900">{{ $banAfkir }}</div>
                        <p class="text-xs text-red-600 mt-1">Rusak</p>
                    </div>

                    <!-- Garasi Pluit -->
                    @php
                        $garasiPluit = $stockBans->filter(function($ban) {
                            return stripos($ban->lokasi, 'Garasi Pluit') !== false && $ban->status === 'Stok';
                        })->count();
                    @endphp
                    <div id="card-garasi-pluit" onclick="setCardFilter('garasi-pluit')" class="cursor-pointer bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-lg p-4 shadow-sm hover:shadow-md transition card-filter">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-indigo-600 uppercase">Garasi Pluit</span>
                            <i class="fas fa-warehouse text-indigo-400 text-lg"></i>
                        </div>
                        <div class="text-2xl font-bold text-indigo-900">{{ $garasiPluit }}</div>
                        <p class="text-xs text-indigo-600 mt-1">Lokasi</p>
                    </div>

                    <!-- Ruko 10 -->
                    @php
                        $ruko10 = $stockBans->filter(function($ban) {
                            return stripos($ban->lokasi, 'Ruko 10') !== false && $ban->status === 'Stok';
                        })->count();
                    @endphp
                    <div id="card-ruko-10" onclick="setCardFilter('ruko-10')" class="cursor-pointer bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-lg p-4 shadow-sm hover:shadow-md transition card-filter">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-orange-600 uppercase">Ruko 10</span>
                            <i class="fas fa-building text-orange-400 text-lg"></i>
                        </div>
                        <div class="text-2xl font-bold text-orange-900">{{ $ruko10 }}</div>
                        <p class="text-xs text-orange-600 mt-1">Lokasi</p>
                    </div>
                </div>
            </div>



                <div class="mb-4 flex justify-end hidden" id="bulk-action-container">
                     <button type="button" class="px-4 py-2 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 shadow-md transition flex items-center" onclick="openKanisirModal(event)">
                        <i class="fas fa-fire mr-2"></i> Masak Kanisir (Bulk)
                     </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="check-all-ban-luar" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Seri / Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Faktur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merk & Ukuran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobil</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi / Posisi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Masuk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Masak</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($stockBans as $ban)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ban->status == 'Stok' && $ban->kondisi != 'afkir')
                                    <input type="checkbox" name="ids[]" value="{{ $ban->id }}" 
                                        data-type="{{ ucfirst($ban->kondisi) }}" 
                                        data-ukuran="{{ $ban->ukuran }}"
                                        data-status-luar="{{ $ban->status_ban_luar }}"
                                        data-harga="{{ number_format($ban->harga_beli, 0, ',', '.') }}"
                                        class="check-item rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $ban->nomor_seri ?? '-' }}
                                    @if($ban->namaStockBan)
                                    <div class="text-xs text-gray-500">{{ $ban->namaStockBan->nama }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $ban->nomor_faktur ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="font-medium text-gray-800">{{ $ban->merk ?? $ban->merkBan->nama ?? '-' }}</div>
                                    <div class="text-xs">{{ $ban->ukuran ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $ban->kondisi == 'asli' ? 'bg-green-100 text-green-800' : 
                                           ($ban->kondisi == 'kanisir' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($ban->kondisi == 'afkir' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst($ban->kondisi) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $ban->status == 'Stok' ? 'bg-blue-100 text-blue-800' : 
                                           ($ban->status == 'Terpakai' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ $ban->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($ban->mobil)
                                        <span class="text-blue-600 font-medium">
                                            <i class="fas fa-truck mr-1"></i> {{ $ban->mobil->nomor_polisi }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $ban->penerima->nama_lengkap ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $ban->lokasi ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ date('d-m-Y', strtotime($ban->tanggal_masuk)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex flex-col">
                                        <span class="px-2 inline-flex text-[10px] leading-4 font-semibold rounded-full w-fit
                                            {{ $ban->status_masak == 'sudah' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ ucfirst($ban->status_masak) }}
                                        </span>
                                        @if($ban->jumlah_masak > 0)
                                            <span class="text-[10px] text-gray-400 mt-1">{{ $ban->jumlah_masak }}x masak</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        @if($ban->status == 'Stok')
                                            <button type="button" 
                                                onclick="openUsageModal('{{ $ban->id }}', '{{ $ban->nomor_seri }}')"
                                                class="text-green-600 hover:text-green-900" title="Gunakan / Pasang">
                                                <i class="fas fa-wrench"></i>
                                            </button>
                                            
                                            @if($ban->kondisi != 'kanisir' && $ban->kondisi != 'afkir')
                                            <form action="{{ route('stock-ban.masak', $ban->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin masak ban ini jadi kanisir?')">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="text-orange-600 hover:text-orange-900" title="Masak Kanisir">
                                                    <i class="fas fa-fire"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @elseif($ban->status == 'Terpakai')
                                            <button type="button" 
                                                onclick="openReturnModal('{{ $ban->id }}', '{{ $ban->nomor_seri }}', '{{ $ban->mobil ? $ban->mobil->nomor_polisi : '-' }}')"
                                                class="text-indigo-600 hover:text-indigo-900" title="Kembalikan ke Gudang">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        @endif

                                        <a href="{{ route('stock-ban.show', $ban->id) }}" class="text-purple-600 hover:text-purple-900" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('stock-ban.edit', $ban->id) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('stock-ban.destroy', $ban->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="12" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data stock ban luar</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

        </div>

        <!-- Tab: Ban Dalam -->
        <div id="tab-ban-dalam" class="tab-content p-4">
            @include('stock-ban.partials.table-bulk', ['items' => $stockBanDalams, 'type' => 'Ban Dalam'])
        </div>

        <!-- Tab: Ban Perut -->
        <div id="tab-ban-perut" class="tab-content p-4">
            @include('stock-ban.partials.table-bulk', ['items' => $stockBanPeruts, 'type' => 'Ban Perut'])
        </div>

        <!-- Tab: Lock Kontainer -->
        <div id="tab-lock-kontainer" class="tab-content p-4">
            @include('stock-ban.partials.table-bulk', ['items' => $stockLockKontainers, 'type' => 'Lock Kontainer'])
        </div>

        <!-- Tab: Ring Velg -->
        <div id="tab-ring-velg" class="tab-content p-4">
            @include('stock-ban.partials.table-ring-velg', ['items' => $stockRingVelgs, 'type' => 'Ring Velg'])
        </div>

        <!-- Tab: Velg -->
        <div id="tab-velg" class="tab-content p-4">
            @include('stock-ban.partials.table-ring-velg', ['items' => $stockVelgs, 'type' => 'Velg'])
        </div>
    </div>
</div>

<!-- Hidden Form for Bulk Action -->
<form action="{{ route('stock-ban.bulk-masak') }}" method="POST" id="bulk-masak-form" class="hidden">
    @csrf
</form>

<!-- Modal Masak Kanisir (Bulk) -->
<div id="kanisirModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeKanisirModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-fire text-orange-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Proses Masak Kanisir
                        </h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="kanisir_invoice" class="form-label-premium">Nomor Invoice</label>
                                <input type="text" id="kanisir_invoice" class="form-input-premium bg-gray-100" value="{{ $nextInvoice }}" readonly>


                            <div>
                                <label for="kanisir_faktur_vendor" class="form-label-premium">Nomor Faktur Vendor</label>
                                <input type="text" id="kanisir_faktur_vendor" class="form-input-premium" placeholder="Nomor faktur dari vendor...">
                            </div>
                            
                            <div>
                                <label for="kanisir_tanggal" class="form-label-premium">Tanggal Masuk Kanisir <span class="text-red-500">*</span></label>
                                <input type="date" id="kanisir_tanggal" class="form-input-premium" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div>
                                <label class="form-label-premium">Vendor <span class="text-red-500">*</span></label>
                                <input type="hidden" id="kanisir_vendor" required>
                                <button type="button" id="btn-kanisir_vendor" class="form-input-premium flex justify-between items-center bg-white" onclick="DropdownManager.toggle('kanisir_vendor', this)">
                                    <span class="block truncate" id="text-kanisir_vendor">-- Pilih Vendor --</span>
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </button>
                                
                                <!-- Dropdown Content -->
                                <div id="dropdown-content-kanisir_vendor" class="hidden">
                                    <div class="dropdown-search-container">
                                        <input type="text" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 p-2" placeholder="Cari vendor..." onkeyup="DropdownManager.filter(this)">
                                    </div>
                                    <div class="dropdown-list">
                                        <div class="dropdown-item" onclick="DropdownManager.select('kanisir_vendor', '', '-- Pilih Vendor --')">-- Pilih Vendor --</div>
                                        @foreach($pricelistKanisirBans as $pricelist)
                                            <div class="dropdown-item" 
                                                 onclick="DropdownManager.select('kanisir_vendor', '{{ $pricelist->vendor }}', '{{ $pricelist->vendor }}')"
                                                 data-search="{{ strtolower($pricelist->vendor) }}">
                                                {{ $pricelist->vendor }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="kanisir_harga" class="form-label-premium">Harga (Total/Satuan) <span class="text-red-500">*</span></label>
                                <div class="mt-1 relative rounded-lg shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" id="kanisir_harga" class="form-input-premium pl-10" placeholder="0" required>
                                </div>
                                <p class="mt-2 text-xs text-gray-400 italic">Harga yang dimasukkan akan diupdate ke data ban.</p>
                            </div>

                            {{-- Selected Ban Table --}}
                            <div class="mt-6 border-t border-gray-100 pt-4">
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Ban yang Dipilih:</h4>
                                <div class="overflow-hidden border border-gray-200 rounded-xl shadow-sm bg-gray-50">
                                    <div class="overflow-y-auto max-h-48">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-100 sticky top-0">
                                                <tr>
                                                    <th scope="col" class="px-4 py-2 text-left text-[10px] font-bold text-gray-600 uppercase">No Seri</th>
                                                    <th scope="col" class="px-4 py-2 text-left text-[10px] font-bold text-gray-600 uppercase">Merk / Ukuran</th>
                                                    <th scope="col" class="px-4 py-2 text-left text-[10px] font-bold text-gray-600 uppercase">Type</th>
                                                    <th scope="col" class="px-4 py-2 text-left text-[10px] font-bold text-gray-600 uppercase text-right">Harga</th>
                                                </tr>
                                            </thead>
                                            <tbody id="kanisir_selected_ban_container" class="bg-white divide-y divide-gray-100">
                                                <!-- Dynamic Content -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="bg-white px-4 py-2 border-t border-gray-100 flex justify-between items-center text-xs font-medium text-gray-500">
                                        <span>Total Terpilih:</span>
                                        <span id="kanisir_total_terpilih" class="text-orange-600 font-bold">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                <button type="button" onclick="submitKanisirForm(event)" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-md px-6 py-2 bg-orange-600 text-base font-semibold text-white hover:bg-orange-700 focus:outline-none transition-all transform hover:scale-105 sm:ml-0 sm:w-auto sm:text-sm">
                    Simpan
                </button>
                <button type="button" onclick="closeKanisirModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-6 py-2 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none transition-all sm:mt-0 sm:ml-0 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kembalikan Ban ke Gudang -->
<div id="returnBanModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeReturnModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-undo text-indigo-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Kembalikan Ban ke Gudang
                        </h3>
                        <div class="mt-4 space-y-4">
                            <!-- Info Ban -->
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <span class="text-gray-500">Nomor Seri:</span>
                                        <p id="return_nomor_seri" class="font-semibold text-gray-900">-</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Dari Mobil:</span>
                                        <p id="return_mobil" class="font-semibold text-gray-900">-</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Return -->
                            <form id="returnBanForm" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" id="return_ban_id" name="ban_id">
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="return_lokasi" class="block text-sm font-medium text-gray-700 mb-2">
                                            Lokasi Penyimpanan <span class="text-red-500">*</span>
                                        </label>
                                        <select name="lokasi" id="return_lokasi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                                            <option value="">-- Pilih Lokasi --</option>
                                            <option value="Ruko 10">Ruko 10</option>
                                            <option value="Garasi Pluit">Garasi Pluit</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="return_keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                                            Keterangan (Opsional)
                                        </label>
                                        <textarea name="keterangan" 
                                                  id="return_keterangan" 
                                                  rows="3"
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                  placeholder="Catatan kondisi ban atau alasan pengembalian..."></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                <button type="button" 
                        onclick="submitReturnForm()" 
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-md px-6 py-2 bg-indigo-600 text-base font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-all transform hover:scale-105 sm:ml-0 sm:w-auto sm:text-sm">
                    <i class="fas fa-check mr-2"></i> Kembalikan ke Gudang
                </button>
                <button type="button" 
                        onclick="closeReturnModal()" 
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-6 py-2 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none transition-all sm:mt-0 sm:ml-0 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Gunakan Ban Luar -->
<div id="usageModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeUsageModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="usageForm" method="POST" action="">
                @csrf
                @method('POST')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                        Gunakan Ban: <span id="modal-ban-seri"></span>
                    </h3>
                    
                    <div class="mb-4">
                        <label class="form-label-premium">Mobil</label>
                        <input type="hidden" name="mobil_id" id="mobil" required>
                        <button type="button" id="btn-mobil" class="btn-dropdown-premium" onclick="DropdownManager.toggle('mobil', this)">
                            <span class="block truncate" id="text-mobil">-- Pilih Mobil --</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        
                        <!-- Dropdown Content (Hidden Loop) -->
                        <div id="dropdown-content-mobil" class="hidden">
                            <div class="dropdown-search-container">
                                <input type="text" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 p-2" placeholder="Cari mobil..." onkeyup="DropdownManager.filter(this)">
                            </div>
                            <div class="dropdown-list">
                                <div class="dropdown-item" onclick="DropdownManager.select('mobil', '', '-- Pilih Mobil --')">-- Pilih Mobil --</div>
                                @foreach($mobils as $mobil)
                                    <div class="dropdown-item" 
                                         onclick="DropdownManager.select('mobil', '{{ $mobil->id }}', '{{ $mobil->nomor_polisi }}')"
                                         data-search="{{ strtolower($mobil->nomor_polisi . ' ' . $mobil->merek . ' ' . $mobil->jenis) }}">
                                        {{ $mobil->nomor_polisi }} ({{ $mobil->merek }} - {{ $mobil->jenis }})
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-premium">Penerima (Supir/Kenek)</label>
                        <input type="hidden" name="penerima_id" id="penerima" required>
                        <button type="button" id="btn-penerima" class="btn-dropdown-premium" onclick="DropdownManager.toggle('penerima', this)">
                            <span class="block truncate" id="text-penerima">-- Pilih Penerima --</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        
                        <!-- Dropdown Content (Hidden Loop) -->
                        <div id="dropdown-content-penerima" class="hidden">
                            <div class="dropdown-search-container">
                                <input type="text" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 p-2" placeholder="Cari penerima..." onkeyup="DropdownManager.filter(this)">
                            </div>
                            <div class="dropdown-list">
                                <div class="dropdown-item" onclick="DropdownManager.select('penerima', '', '-- Pilih Penerima --')">-- Pilih Penerima --</div>
                                @foreach($karyawans as $karyawan)
                                    <div class="dropdown-item" 
                                         onclick="DropdownManager.select('penerima', '{{ $karyawan->id }}', '{{ $karyawan->nama_lengkap }}')"
                                         data-search="{{ strtolower($karyawan->nama_lengkap) }}">
                                        {{ $karyawan->nama_lengkap }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-premium">Tanggal Pasang / Keluar</label>
                        <input type="date" name="tanggal_keluar" required value="{{ date('Y-m-d') }}" class="form-input-premium">
                    </div>

                    <div class="mb-4">
                        <label class="form-label-premium">Keterangan</label>
                        <textarea name="keterangan" rows="3" class="form-input-premium" placeholder="Tambahkan catatan pemakaian..."></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-md px-6 py-2 bg-blue-600 text-base font-semibold text-white hover:bg-blue-700 focus:outline-none transition-all transform hover:scale-105 sm:ml-0 sm:w-auto sm:text-sm">
                        Simpan
                    </button>
                    <button type="button" onclick="closeUsageModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-6 py-2 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none transition-all sm:mt-0 sm:ml-0 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab Logic
        const tabs = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));

                tab.classList.add('active');
                document.getElementById(tab.dataset.target).classList.add('active');
            });
        });

        // Bulk Action & Check All Logic (specific for Ban Luar)
        const bulkActionContainer = document.getElementById('bulk-action-container');
        
        function updateBulkButton() {
            if (!bulkActionContainer) return;
            const checkedCount = document.querySelectorAll('#tab-ban-luar .check-item:checked').length;
            
            if (checkedCount > 0) {
                bulkActionContainer.classList.remove('hidden');
            } else {
                bulkActionContainer.classList.add('hidden');
            }
        }

        // Listen for changes on the unique check-all box
        const checkAllBanLuar = document.getElementById('check-all-ban-luar');
        if (checkAllBanLuar) {
            checkAllBanLuar.addEventListener('change', function(e) {
                const isChecked = e.target.checked;
                // Only target check-items within the ban-luar tab to avoid cross-tab pollution
                const container = document.getElementById('tab-ban-luar');
                if(container) {
                    container.querySelectorAll('.check-item').forEach(item => {
                        item.checked = isChecked;
                    });
                }
                updateBulkButton();
            });
        }

        // Delegated listener for individual checkboxes
        document.getElementById('tab-ban-luar').addEventListener('change', function(e) {
            if (e.target.classList.contains('check-item')) {
                updateBulkButton();
            }
        });

        // Initialize state
        updateBulkButton();
    });
    
    // Global data for price lookup
    const pricelistData = @json($pricelistKanisirBans);

    const DropdownManager = {
        activeDropdownId: null,
        activeMenu: null,

        toggle: function(id, button) {
            if (this.activeDropdownId === id) {
                this.close();
                return;
            }
            this.open(id, button);
        },

        open: function(id, button) {
            this.close(); // Close existing

            // Create/Get Menu
            let menu = document.getElementById('dropdown-menu-overlay-' + id);
            if (!menu) {
                // Clone the content template
                const template = document.getElementById('dropdown-content-' + id);
                if (!template) return;

                menu = document.createElement('div');
                menu.id = 'dropdown-menu-overlay-' + id;
                menu.className = 'dropdown-menu-custom';
                menu.innerHTML = template.innerHTML;
                document.body.appendChild(menu);

                // Prevent click bubbling from menu
                menu.addEventListener('click', (e) => e.stopPropagation());
            }

            // Position it
            const rect = button.getBoundingClientRect();
            menu.style.width = rect.width + 'px';
            menu.style.left = rect.left + 'px';
            menu.style.top = (rect.bottom + window.scrollY) + 'px';
            menu.style.display = 'block';

            // Reset search
            const searchInput = menu.querySelector('input');
            if(searchInput) {
                searchInput.value = '';
                searchInput.focus();
                this.filter(searchInput); // Reset filter
            }

            this.activeDropdownId = id;
            this.activeMenu = menu;

            // Add scroll listener to update position
            window.addEventListener('scroll', this.reposition, true);
            window.addEventListener('resize', this.reposition);
        },

        close: function() {
            if (this.activeMenu) {
                this.activeMenu.style.display = 'none';
                this.activeDropdownId = null;
                this.activeMenu = null;
                window.removeEventListener('scroll', this.reposition, true);
                window.removeEventListener('resize', this.reposition);
            }
        },

        reposition: function() {
            if (!DropdownManager.activeDropdownId || !DropdownManager.activeMenu) return;
            // Find the button (re-query in case context changed?) - we assume button ID convention
            const button = document.getElementById('btn-' + DropdownManager.activeDropdownId);
            if(button) {
                const rect = button.getBoundingClientRect();
                DropdownManager.activeMenu.style.left = rect.left + 'px';
                DropdownManager.activeMenu.style.top = (rect.bottom + window.scrollY) + 'px';
                DropdownManager.activeMenu.style.width = rect.width + 'px';
            }
        },

        select: function(id, value, text) {
            const hiddenInput = document.getElementById(id);
            if (hiddenInput) {
                hiddenInput.value = value;
            }
            
            const textEl = document.getElementById('text-' + id);
            if (textEl) {
                textEl.textContent = text;
            }
            
            this.close();

            // Custom trigger for kanisir vendor
            if (id === 'kanisir_vendor') {
                updateKanisirPrices(value);
            }
        },

        filter: function(input) {
            const term = input.value.toLowerCase();
            const items = input.closest('.dropdown-menu-custom').querySelectorAll('.dropdown-item');
            items.forEach(item => {
                const search = item.getAttribute('data-search') || '';
                if (!term || search.includes(term) || item.textContent.toLowerCase().includes(term)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    };

    // Close on click outside
    document.addEventListener('click', function(e) {
        // If click is not on a dropdown button and not inside a menu
        if (!e.target.closest('button[onclick^="DropdownManager.toggle"]') && 
            !e.target.closest('.dropdown-menu-custom')) {
            DropdownManager.close();
        }
    });

    function openUsageModal(id, seri) {
        document.getElementById('modal-ban-seri').textContent = seri;
        document.getElementById('usageForm').action = "{{ url('stock-ban') }}/" + id + "/use";
        
        // Reset selections
        document.getElementById('mobil').value = '';
        document.getElementById('text-mobil').textContent = '-- Pilih Mobil --';
        document.getElementById('penerima').value = '';
        document.getElementById('text-penerima').textContent = '-- Pilih Penerima --';
        
        document.getElementById('usageModal').classList.remove('hidden');
    }

    function closeUsageModal() {
        document.getElementById('usageModal').classList.add('hidden');
        DropdownManager.close();
    }

    // Kanisir Modal Logic
    function openKanisirModal(e) {
        e.preventDefault();
        
        // Reset vendor selection
        document.getElementById('kanisir_vendor').value = '';
        document.getElementById('text-kanisir_vendor').textContent = '-- Pilih Vendor --';
        
        const selectedCheckboxes = document.querySelectorAll('#tab-ban-luar .check-item:checked');
        const container = document.getElementById('kanisir_selected_ban_container');
        const totalSpan = document.getElementById('kanisir_total_terpilih');
        
        container.innerHTML = '';
        totalSpan.textContent = selectedCheckboxes.length;

        selectedCheckboxes.forEach(cb => {
            const row = cb.closest('tr');
            // Clone data from row and checkbox attributes
            const noSeriContent = row.cells[1].innerHTML;
            const merkUkuranContent = row.cells[2].innerHTML;
            const typeValue = cb.getAttribute('data-type') || '-';
            const banId = cb.value;

            const tr = document.createElement('tr');
            tr.className = "hover:bg-orange-50/30 transition-colors";
            tr.setAttribute('data-ban-id', banId);
            tr.setAttribute('data-ukuran', cb.getAttribute('data-ukuran') || '');
            tr.setAttribute('data-status-luar', cb.getAttribute('data-status-luar') || '');
            
            const td1 = document.createElement('td');
            td1.className = "px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-900";
            td1.innerHTML = noSeriContent;

            const td2 = document.createElement('td');
            td2.className = "px-4 py-3 whitespace-nowrap text-xs text-gray-600";
            td2.innerHTML = merkUkuranContent;

            const td3 = document.createElement('td');
            td3.className = "px-4 py-3 whitespace-nowrap text-xs text-gray-600";
            const typeSpan = document.createElement('span');
            typeSpan.className = "px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-medium";
            typeSpan.textContent = typeValue;
            td3.appendChild(typeSpan);

            const td4 = document.createElement('td');
            td4.className = "px-4 py-3 whitespace-nowrap text-xs text-gray-900 font-bold text-right kanisir-price-col";
            td4.innerHTML = `<span class="text-[10px] text-gray-400 font-normal mr-1">Rp</span>-`;

            tr.appendChild(td1);
            tr.appendChild(td2);
            tr.appendChild(td3);
            tr.appendChild(td4);
            container.appendChild(tr);
        });

        // If vendor already selected (unlikely on open, but for safety)
        const currentVendor = document.getElementById('kanisir_vendor').value;
        if (currentVendor) updateKanisirPrices(currentVendor);

        document.getElementById('kanisirModal').classList.remove('hidden');
    }

    function updateKanisirPrices(vendorName) {
        if (!vendorName) return;
        
        const pricelist = pricelistData.find(p => p.vendor === vendorName);
        if (!pricelist) return;

        const rows = document.querySelectorAll('#kanisir_selected_ban_container tr');
        let totalHarga = 0;

        rows.forEach(row => {
            const ukuran = row.getAttribute('data-ukuran');
            const status = row.getAttribute('data-status-luar');
            
            // Map status to column suffix
            const suffix = status === 'kawat' ? 'kawat' : (status === 'benang' ? 'benang' : null);
            let harga = 0;
            
            if (suffix && ukuran) {
                const colName = `harga_${ukuran}_${suffix}`;
                harga = parseFloat(pricelist[colName]) || 0;
            }

            const priceCol = row.querySelector('.kanisir-price-col');
            if (priceCol) {
                priceCol.innerHTML = `<span class="text-[10px] text-gray-400 font-normal mr-1">Rp</span>${new Intl.NumberFormat('id-ID').format(harga)}`;
            }
            totalHarga += harga;
        });

        // Update the main harga input in the modal based on total selected prices
        document.getElementById('kanisir_harga').value = totalHarga;
    }

    function closeKanisirModal() {
        document.getElementById('kanisirModal').classList.add('hidden');
    }

    function submitKanisirForm(e) {
        e.preventDefault();
        
        const form = document.getElementById('bulk-masak-form');
        
        // Get values
        const invoice = document.getElementById('kanisir_invoice').value;
        const fakturVendor = document.getElementById('kanisir_faktur_vendor').value;
        const tanggal = document.getElementById('kanisir_tanggal').value;
        const vendor = document.getElementById('kanisir_vendor').value;
        const harga = document.getElementById('kanisir_harga').value;
        
        if (!tanggal || !vendor || !harga) {
            alert('Mohon lengkapi data Tanggal, Vendor, dan Harga.');
            return;
        }

        // Helper to append hidden input
        const appendHidden = (name, value) => {
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        };

        // Append selected IDs
        const selectedCheckboxes = document.querySelectorAll('#tab-ban-luar .check-item:checked');
        if (selectedCheckboxes.length === 0) {
            alert('Pilih ban terlebih dahulu.');
            return;
        }

        selectedCheckboxes.forEach(cb => {
            appendHidden('ids[]', cb.value);
        });

        appendHidden('nomor_invoice', invoice);
        appendHidden('nomor_faktur_vendor', fakturVendor);
        appendHidden('tanggal_masuk_kanisir', tanggal);
        appendHidden('vendor', vendor);
        appendHidden('harga', harga);

        form.submit();
    }


    // Search functionality
    let currentCardFilter = 'total';

    function setCardFilter(filterType) {
        currentCardFilter = filterType;
        
        // Visual updates
        // Visual updates
        document.querySelectorAll('.card-filter').forEach(card => {
            card.classList.remove('active-filter', 'ring-2');
            
            // Remove specific color rings
            card.classList.remove('ring-blue-400', 'ring-green-400', 'ring-purple-400', 'ring-emerald-400', 'ring-yellow-400', 'ring-red-400', 'ring-indigo-400', 'ring-orange-400');
        });

        const activeCard = document.getElementById('card-' + filterType);
        if(activeCard) {
            activeCard.classList.add('active-filter', 'ring-2');
            
            // Add specific color ring based on type
            const colorMap = {
                'total': 'ring-blue-400',
                'stok': 'ring-green-400',
                'terpakai': 'ring-purple-400',
                'asli': 'ring-emerald-400',
                'kanisir': 'ring-yellow-400',
                'afkir': 'ring-red-400',
                'garasi-pluit': 'ring-indigo-400',
                'ruko-10': 'ring-orange-400'
            };
            activeCard.classList.add(colorMap[filterType]);
        }
        
        // Trigger search to apply filter
        const searchInput = document.getElementById('search-input');
        if(searchInput) {
            searchInput.dispatchEvent(new Event('input'));
        }
    }

    (function() {
        const searchInput = document.getElementById('search-input');
        const clearButton = document.getElementById('clear-search');
        
        if (!searchInput || !clearButton) return;

        let searchTimeout;

        function performSearch() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const activeTab = document.querySelector('.tab-content.active');
            
            if (!activeTab) return;

            const tableRows = activeTab.querySelectorAll('tbody tr');
            let visibleCount = 0;

            tableRows.forEach(row => {
                // Skip if row is "No data" message
                if (row.querySelector('td[colspan]')) {
                    row.style.display = 'none';
                    return;
                }

                // Get row data
                const nomorSeri = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                const merk = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || ''; // Merk & Ukuran
                
                // Get raw status text
                const statusSpan = row.querySelector('td:nth-child(5) span');
                const status = statusSpan ? statusSpan.textContent.trim().toLowerCase() : '';

                // Get raw kondisi text
                const kondisiSpan = row.querySelector('td:nth-child(4) span');
                const kondisi = kondisiSpan ? kondisiSpan.textContent.trim().toLowerCase() : '';
                
                const lokasi = row.querySelector('td:nth-child(8)')?.textContent.toLowerCase() || '';
                const mobil = row.querySelector('td:nth-child(6)')?.textContent.toLowerCase() || '';
                const penerima = row.querySelector('td:nth-child(7)')?.textContent.toLowerCase() || '';


                // Check Text Match
                const textMatch = nomorSeri.includes(searchTerm) || 
                                merk.includes(searchTerm) || 
                                lokasi.includes(searchTerm) ||
                                mobil.includes(searchTerm) ||
                                penerima.includes(searchTerm);

                // Check Card Filter Match
                let filterMatch = true;
                if (currentCardFilter !== 'total') {
                    if (currentCardFilter === 'stok') {
                        filterMatch = status === 'stok';
                    } else if (currentCardFilter === 'terpakai') {
                        filterMatch = status === 'terpakai';
                    } else if (currentCardFilter === 'asli') {
                        filterMatch = kondisi === 'asli';
                    } else if (currentCardFilter === 'kanisir') {
                        filterMatch = kondisi === 'kanisir';
                    } else if (currentCardFilter === 'afkir') {
                        filterMatch = kondisi === 'afkir';
                    } else if (currentCardFilter === 'garasi-pluit') {
                        filterMatch = lokasi.includes('garasi pluit');
                    } else if (currentCardFilter === 'ruko-10') {
                        filterMatch = lokasi.includes('ruko 10');
                    }
                }

                if ((textMatch || searchTerm === '') && filterMatch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show "no results" message if needed
            const tbody = activeTab.querySelector('tbody');
            let noResultsRow = tbody.querySelector('.no-results-row');
            
            // Only show no results if both filters have input/active and yield 0 results
            if (visibleCount === 0) {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.className = 'no-results-row';
                    noResultsRow.innerHTML = `
                        <td colspan="12" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-search text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500 font-medium">Tidak ada data ditemukan</p>
                                <p class="text-gray-400 text-sm mt-1">Coba ubah filter atau kata kunci pencarian</p>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(noResultsRow);
                }
            } else if (noResultsRow) {
                noResultsRow.remove();
            }

            // Toggle clear button visibility
            clearButton.classList.toggle('hidden', searchTerm === '');
        }

        // Input event with debounce
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        });

        // Clear search
        clearButton.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.focus();
            performSearch();
        });

        // Re-run search when tab changes
        const tabButtons = document.querySelectorAll('.tab-btn');
        tabButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Wait for tab content to be visible
                setTimeout(performSearch, 50);
            });
        });
        
        // Expose performSearch to external calls (if needed)
        window.performSearch = performSearch;
    })();

    // Return Ban Modal Functions
    function openReturnModal(banId, nomorSeri, mobilPolisi) {
        document.getElementById('return_ban_id').value = banId;
        document.getElementById('return_nomor_seri').textContent = nomorSeri || '-';
        document.getElementById('return_mobil').textContent = mobilPolisi || '-';
        
        // Reset form
        document.getElementById('return_lokasi').value = '';
        document.getElementById('return_keterangan').value = '';
        
        // Show modal
        document.getElementById('returnBanModal').classList.remove('hidden');
    }

    function closeReturnModal() {
        document.getElementById('returnBanModal').classList.add('hidden');
    }

    function submitReturnForm() {
        const banId = document.getElementById('return_ban_id').value;
        const lokasi = document.getElementById('return_lokasi').value;
        
        if (!lokasi) {
            alert('Mohon pilih lokasi penyimpanan!');
            return;
        }

        const form = document.getElementById('returnBanForm');
        form.action = `{{ url('stock-ban') }}/${banId}/return`;
        form.submit();
    }
</script>
@endpush
