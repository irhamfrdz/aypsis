@extends('layouts.app')

@section('title', 'Stock Amprahan')
@section('page_title', 'Stock Amprahan')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Stock Amprahan</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola ketersediaan barang operasional dan kantor.</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <a href="{{ route('stock-amprahan.all-history', ['lokasi' => request('lokasi')]) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Riwayat Pemakaian
            </a>
            <a href="{{ route('stock-amprahan.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Stock
            </a>
        </div>
    </div>

    {{-- Alert Section --}}
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-sm flex items-center">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    {{-- Bulk Actions --}}
    <div id="bulkActions" class="hidden mb-6 bg-indigo-50 border border-indigo-200 rounded-lg shadow-sm p-4 animate-in fade-in slide-in-from-top-4 duration-300">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="p-2 bg-indigo-100 rounded-full text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-sm font-bold text-indigo-900"><span id="selected-count">0</span> Item Terpilih</span>
                    <p class="text-xs text-indigo-600">Pilih item untuk dimasukkan ke dalam pranota</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" 
                        id="btnBulkPranota"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Masukan ke Pranota
                </button>
                <button type="button" 
                        id="btnCancelSelection"
                        class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
                    Batal
                </button>
            </div>
        </div>
    </div>

    {{-- Search Section --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('stock-amprahan.index') }}" class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari berdasarkan nama barang atau no. bukti..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Cari
                </button>
                @if((isset($search) && $search) || request('lokasi'))
                <a href="{{ route('stock-amprahan.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Rekap Section Title --}}
    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-chart-pie text-indigo-600"></i>
        Rekap Stock Amprahan
    </h3>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        {{-- Total Jenis --}}
        <a href="{{ route('stock-amprahan.index') }}" class="group bg-gradient-to-br from-white to-blue-50/30 p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 {{ !request('lokasi') ? 'ring-2 ring-blue-500 bg-blue-50/50' : '' }}">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-lg {{ !request('lokasi') ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-blue-100 text-blue-600' }} group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <span class="text-[10px] font-black {{ !request('lokasi') ? 'text-blue-600' : 'text-gray-400' }} uppercase tracking-widest">Semua</span>
            </div>
            <div>
                <p class="text-3xl font-black text-gray-800 tracking-tight">{{ $stats['total_jenis'] }}</p>
                <p class="text-[10px] font-bold text-gray-500 uppercase mt-1 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                    Jenis Barang
                </p>
            </div>
        </a>

        {{-- Total Qty --}}
        <div class="bg-gradient-to-br from-white to-indigo-50/30 p-5 rounded-xl shadow-sm border border-gray-100 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-lg bg-indigo-100 text-indigo-600 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Global</span>
            </div>
        </div>

        {{-- Jakarta --}}
        <a href="{{ route('stock-amprahan.index', ['lokasi' => 'KANTOR AYP JAKARTA']) }}" class="group bg-gradient-to-br from-white to-emerald-50/30 p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 {{ request('lokasi') == 'KANTOR AYP JAKARTA' ? 'ring-2 ring-emerald-500 bg-emerald-50/50' : '' }}">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-lg {{ request('lokasi') == 'KANTOR AYP JAKARTA' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-200' : 'bg-emerald-100 text-emerald-600' }} group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="text-[10px] font-black {{ request('lokasi') == 'KANTOR AYP JAKARTA' ? 'text-emerald-600' : 'text-gray-400' }} uppercase tracking-widest">Jakarta</span>
            </div>
        </a>

        {{-- Batam --}}
        <a href="{{ route('stock-amprahan.index', ['lokasi' => 'KANTOR AYP BATAM']) }}" class="group bg-gradient-to-br from-white to-orange-50/30 p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 {{ request('lokasi') == 'KANTOR AYP BATAM' ? 'ring-2 ring-orange-500 bg-orange-50/50' : '' }}">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-lg {{ request('lokasi') == 'KANTOR AYP BATAM' ? 'bg-orange-600 text-white shadow-lg shadow-orange-200' : 'bg-orange-100 text-orange-600' }} group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="text-[10px] font-black {{ request('lokasi') == 'KANTOR AYP BATAM' ? 'text-orange-600' : 'text-gray-400' }} uppercase tracking-widest">Batam</span>
            </div>
        </a>

        {{-- Lainnya --}}
        <a href="{{ route('stock-amprahan.index', ['lokasi' => 'LAINNYA']) }}" class="group bg-gradient-to-br from-white to-amber-50/30 p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 {{ request('lokasi') == 'LAINNYA' ? 'ring-2 ring-amber-500 bg-amber-50/50' : '' }}">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-lg {{ request('lokasi') == 'LAINNYA' ? 'bg-amber-600 text-white shadow-lg shadow-amber-200' : 'bg-amber-100 text-amber-600' }} group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-[10px] font-black {{ request('lokasi') == 'LAINNYA' ? 'text-amber-600' : 'text-gray-400' }} uppercase tracking-widest">Lainnya</span>
            </div>
        </a>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Bukti</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Jumlah</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="item-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                                value="{{ $item->id }}"
                                data-nama="{{ $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? '-') }}"
                                data-kode="{{ $item->nomor_bukti ?? '-' }}"
                                data-harga="{{ $item->harga_satuan ?? 0 }}"
                                data-jumlah="{{ ($item->jumlah ?? 0) + ($item->usages_sum_jumlah ?? 0) }}"
                                data-satuan="{{ $item->satuan ?? '-' }}"
                            >
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $item->nomor_bukti ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $item->tanggal_beli ? $item->tanggal_beli->format('Y-m-d') : ($item->created_at ? $item->created_at->format('Y-m-d') : '-') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? '-') }}</div>
                            <div class="text-xs text-gray-400">ID: #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $item->masterNamaBarangAmprahan->nama_barang ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold {{ $item->jumlah > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ number_format($item->jumlah, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $item->satuan ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $item->lokasi ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <button type="button" onclick="openHistoryModal('{{ $item->id }}', '{{ $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? '-') }}')" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Riwayat Pengambilan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                                <button type="button" onclick="openUsageModal('{{ $item->id }}', '{{ $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? '-') }}', '{{ $item->jumlah }}', '{{ $item->satuan ?? '-' }}')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Ambil Barang">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <a href="{{ route('stock-amprahan.edit', $item->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit Data">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                     </svg>
                                 </a>
                                <form action="{{ route('stock-amprahan.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Data">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-sm">Tidak ada data stock amprahan.</p>
                                <a href="{{ route('stock-amprahan.create') }}" class="mt-2 text-indigo-600 font-semibold hover:underline">Tambah data pertama Anda</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($items->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $items->links() }}
        </div>
        @endif
    </div>

    {{-- Modal Pengambilan Stock --}}
    <div id="usageModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeUsageModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="usageForm" method="POST" action="">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Pengambilan Barang
                                </h3>
                                <div class="mt-2">
                                    <div id="errorMessage" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-md text-sm text-red-700"></div>
                                    <p class="text-sm text-gray-500 mb-4">
                                        Silakan isi detail pengambilan untuk barang <strong id="modalItemName"></strong>.
                                        Sisa stock saat ini: <strong id="modalCurrentStock"></strong> <span id="modalUnit"></span>
                                    </p>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Penerima</label>
                                            <div class="relative" id="penerima_dropdown">
                                                <input type="hidden" name="penerima_id" id="penerima_id_hidden" required>
                                                
                                                <div class="relative">
                                                    <input type="text" id="penerima_search_input" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out bg-white" placeholder="Pilih Penerima..." autocomplete="off">
                                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 cursor-pointer" onclick="togglePenerimaDropdown()">
                                                        <svg class="h-5 w-5 transition-transform duration-200" id="dropdown_arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </div>
                                                </div>

                                                <div id="penerima_options_list" class="absolute z-50 w-full mt-1 bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm hidden">
                                                    @foreach($karyawans as $karyawan)
                                                        <div class="penerima-option cursor-pointer select-none relative py-2.5 pl-4 pr-9 hover:bg-blue-50 text-gray-900 transition-colors duration-150 border-b border-gray-50 last:border-0" 
                                                             data-value="{{ $karyawan->id }}" 
                                                             data-name="{{ $karyawan->nama_lengkap }}"
                                                             onclick="selectPenerima('{{ $karyawan->id }}', '{{ $karyawan->nama_lengkap }}')">
                                                            <span class="block truncate font-medium">{{ $karyawan->nama_lengkap }}</span>
                                                        </div>
                                                    @endforeach
                                                    <div id="no_results" class="hidden px-4 py-3 text-sm text-gray-500 text-center italic">Tidak ada nama yang cocok</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Mobil (Opsional)</label>
                                            <div class="relative" id="mobil_dropdown">
                                                <input type="hidden" name="mobil_id" id="mobil_id_hidden">
                                                
                                                <div class="relative">
                                                    <input type="text" id="mobil_search_input" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out bg-white" placeholder="Pilih Mobil..." autocomplete="off">
                                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 cursor-pointer" onclick="toggleMobilDropdown()">
                                                        <svg class="h-5 w-5 transition-transform duration-200" id="mobil_dropdown_arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </div>
                                                </div>

                                                <div id="mobil_options_list" class="absolute z-50 w-full mt-1 bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm hidden">
                                                    @if($mobils->count() > 0)
                                                        @foreach($mobils as $mobil)
                                                            <div class="mobil-option cursor-pointer select-none relative py-2.5 pl-4 pr-9 hover:bg-blue-50 text-gray-900 transition-colors duration-150 border-b border-gray-50 last:border-0" 
                                                                 data-value="{{ $mobil->id }}" 
                                                                 data-name="{{ $mobil->nomor_polisi }} {{ $mobil->merek }}"
                                                                 onclick="selectMobil('{{ $mobil->id }}', '{{ $mobil->nomor_polisi }} - {{ $mobil->merek }}')">
                                                                <span class="block truncate font-medium">{{ $mobil->nomor_polisi }} - {{ $mobil->merek }}</span>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                    <div id="mobil_no_results" class="hidden px-4 py-3 text-sm text-gray-500 text-center italic">Tidak ada mobil yang cocok</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Buntut (Opsional)</label>
                                            <div class="relative" id="buntut_dropdown">
                                                <input type="hidden" name="buntut_id" id="buntut_id_hidden">
                                                
                                                <div class="relative">
                                                    <input type="text" id="buntut_search_input" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out bg-white" placeholder="Pilih Buntut..." autocomplete="off">
                                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 cursor-pointer" onclick="toggleBuntutDropdown()">
                                                        <svg class="h-5 w-5 transition-transform duration-200" id="buntut_dropdown_arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </div>
                                                </div>

                                                <div id="buntut_options_list" class="absolute z-50 w-full mt-1 bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm hidden">
                                                    @if($mobils->count() > 0)
                                                        @foreach($mobils as $buntut)
                                                            <div class="buntut-option cursor-pointer select-none relative py-2.5 pl-4 pr-9 hover:bg-blue-50 text-gray-900 transition-colors duration-150 border-b border-gray-50 last:border-0" 
                                                                 data-value="{{ $buntut->id }}" 
                                                                 data-name="{{ $buntut->no_kir }} {{ $buntut->nomor_polisi }}"
                                                                 onclick="selectBuntut('{{ $buntut->id }}', '{{ $buntut->no_kir ?: ($buntut->nomor_polisi ?: '-') }}')">
                                                                <span class="block truncate font-medium">{{ $buntut->no_kir ?: ($buntut->nomor_polisi ?: '-') }}</span>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                    <div id="buntut_no_results" class="hidden px-4 py-3 text-sm text-gray-500 text-center italic">Tidak ada buntut yang cocok</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Alat Berat (Opsional)</label>
                                            <div class="relative" id="alat_berat_dropdown">
                                                <input type="hidden" name="alat_berat_id" id="alat_berat_id_hidden">
                                                
                                                <div class="relative">
                                                    <input type="text" id="alat_berat_search_input" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out bg-white" placeholder="Pilih Alat Berat..." autocomplete="off">
                                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 cursor-pointer" onclick="toggleAlatBeratDropdown()">
                                                        <svg class="h-5 w-5 transition-transform duration-200" id="alat_berat_dropdown_arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </div>
                                                </div>

                                                <div id="alat_berat_options_list" class="absolute z-50 w-full mt-1 bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm hidden">
                                                    @if($alatBerats->count() > 0)
                                                        @foreach($alatBerats as $alat)
                                                            <div class="alat-berat-option cursor-pointer select-none relative py-2.5 pl-4 pr-9 hover:bg-blue-50 text-gray-900 transition-colors duration-150 border-b border-gray-50 last:border-0" 
                                                                 data-value="{{ $alat->id }}" 
                                                                 data-name="{{ $alat->kode_alat }} {{ $alat->nama }} {{ $alat->merk ?? '' }} {{ $alat->lokasi ?? '' }} {{ $alat->warna ?? '' }}"
                                                                 onclick="selectAlatBerat('{{ $alat->id }}', '{{ $alat->kode_alat }} - {{ $alat->nama }}{{ $alat->merk ? ' - ' . $alat->merk : '' }}{{ $alat->lokasi ? ' - ' . $alat->lokasi : '' }}{{ $alat->warna ? ' - ' . $alat->warna : '' }}')">
                                                                <span class="block font-medium">{{ $alat->kode_alat }} - {{ $alat->nama }}{{ $alat->merk ? ' - ' . $alat->merk : '' }}{{ $alat->lokasi ? ' - ' . $alat->lokasi : '' }}{{ $alat->warna ? ' - ' . $alat->warna : '' }}</span>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                    <div id="alat_berat_no_results" class="hidden px-4 py-3 text-sm text-gray-500 text-center italic">Tidak ada alat berat yang cocok</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Kapal (Opsional)</label>
                                            <div class="relative" id="kapal_dropdown">
                                                <input type="hidden" name="kapal_id" id="kapal_id_hidden">
                                                
                                                <div class="relative">
                                                    <input type="text" id="kapal_search_input" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out bg-white" placeholder="Pilih Kapal..." autocomplete="off">
                                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 cursor-pointer" onclick="toggleKapalDropdown()">
                                                        <svg class="h-5 w-5 transition-transform duration-200" id="kapal_dropdown_arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </div>
                                                </div>

                                                <div id="kapal_options_list" class="absolute z-50 w-full mt-1 bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm hidden">
                                                    @foreach($kapals as $kapal)
                                                        <div class="kapal-option cursor-pointer select-none relative py-2.5 pl-4 pr-9 hover:bg-blue-50 text-gray-900 transition-colors duration-150 border-b border-gray-50 last:border-0" 
                                                             data-value="{{ $kapal->id }}" 
                                                             data-name="{{ $kapal->nama_kapal }}"
                                                             onclick="selectKapal('{{ $kapal->id }}', '{{ $kapal->nama_kapal }}')">
                                                            <span class="block truncate font-medium">{{ $kapal->nama_kapal }}</span>
                                                        </div>
                                                    @endforeach
                                                    <div id="kapal_no_results" class="hidden px-4 py-3 text-sm text-gray-500 text-center italic">Tidak ada kapal yang cocok</div>
                                                </div>
                                            </div>
                                         </div>
                                         <div>
                                             <label for="kilometer" class="block text-sm font-medium text-gray-700 mb-1">Kilometer (Opsional)</label>
                                             <input type="number" step="0.01" name="kilometer" id="kilometer" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out" placeholder="Masukkan kilometer...">
                                         </div>
                                         <div>
                                             <label for="jumlah_ambil" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Ambil</label>
                                            <input type="number" name="jumlah" id="jumlah_ambil" required min="1" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out" placeholder="Masukkan jumlah barang...">
                                            <p class="text-xs text-red-500 mt-1 hidden" id="stockError">
                                                <span class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Jumlah melebihi stock yang tersedia!
                                                </span>
                                            </p>
                                        </div>
                                        
                                        <div>
                                            <label for="tanggal_ambil" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengambilan</label>
                                            <input type="date" name="tanggal" id="tanggal_ambil" required class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out" value="{{ date('Y-m-d') }}">
                                        </div>

                                        <div>
                                            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Keperluan</label>
                                            <textarea name="keterangan" id="keterangan" rows="3" required class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out resize-none" placeholder="Contoh: Untuk operasional kantor, kebutuhan project X..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <button type="button" onclick="closeUsageModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Riwayat Pengambilan --}}
    <div id="historyModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeHistoryModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 flex justify-between items-center" id="modal-title">
                                <span>Riwayat Pengambilan: <span id="historyItemName"></span></span>
                                <a id="fullHistoryLink" href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold flex items-center shadow-sm px-2 py-1 bg-indigo-50 rounded border border-indigo-100 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Halaman Penuh
                                </a>
                            </h3>
                            <div class="mt-4">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobil</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buntut</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapal</th>
                                                 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alat Berat</th>
                                                 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KM</th>
                                                 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                                 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oleh</th>
                                            </tr>
                                        </thead>
                                        <tbody id="historyTableBody" class="bg-white divide-y divide-gray-200">
                                            <tr>
                                                <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">Memuat data...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeHistoryModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openUsageModal(id, name, stock, unit) {
        document.getElementById('usageModal').classList.remove('hidden');
        document.getElementById('modalItemName').textContent = name;
        document.getElementById('modalCurrentStock').textContent = stock;
        document.getElementById('modalUnit').textContent = unit;
        document.getElementById('kilometer').value = '';
        
        // Set form action
        const form = document.getElementById('usageForm');
        form.action = `/stock-amprahan/${id}/usage`;
        
        // Max validation
        const input = document.getElementById('jumlah_ambil');
        input.max = stock;
        input.value = '';
        
        input.addEventListener('input', function() {
            const val = parseFloat(this.value);
            const max = parseFloat(stock);
            if(val > max) {
                document.getElementById('stockError').classList.remove('hidden');
                this.classList.add('border-red-500');
            } else {
                document.getElementById('stockError').classList.add('hidden');
                this.classList.remove('border-red-500');
            }
        });
    }

    function closeUsageModal() {
        document.getElementById('usageModal').classList.add('hidden');
    }

    function openHistoryModal(id, name) {
        document.getElementById('historyModal').classList.remove('hidden');
        document.getElementById('historyItemName').textContent = name;
        document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="11" class="px-6 py-4 text-center text-sm text-gray-500">Memuat data...</td></tr>';
        
        // Update full history link
        const fullHistoryLink = document.getElementById('fullHistoryLink');
        fullHistoryLink.href = `/stock-amprahan/${id}/history`;

        fetch(`/stock-amprahan/${id}/history`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('historyTableBody');
                tbody.innerHTML = '';
                
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada riwayat aktivitas</td></tr>';
                    return;
                }

                data.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-l-4 ${item.type === 'Masuk' ? 'border-green-500' : 'border-orange-500'}">${item.tanggal}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold ${item.type === 'Masuk' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'}">
                                ${item.type}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold ${item.type === 'Masuk' ? 'text-green-600' : 'text-orange-600'}">${item.type === 'Masuk' ? '+' : '-'}${item.jumlah}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.penerima}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.mobil || '-'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.buntut || '-'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.kapal || '-'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.alat_berat || '-'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.kilometer || '-'}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">${item.keterangan || '-'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.created_by}</td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="10" class="px-6 py-4 text-center text-sm text-red-500">Gagal memuat data</td></tr>';
            });
    }

    function closeHistoryModal() {
        document.getElementById('historyModal').classList.add('hidden');
    }

    // SEARCHABLE DROPDOWN LOGIC
    const penerimaInput = document.getElementById('penerima_search_input');
    const penerimaList = document.getElementById('penerima_options_list');
    const penerimaHidden = document.getElementById('penerima_id_hidden');
    const dropdownArrow = document.getElementById('dropdown_arrow');
    const options = document.querySelectorAll('.penerima-option');
    const noResults = document.getElementById('no_results');

    function togglePenerimaDropdown() {
        const isHidden = penerimaList.classList.contains('hidden');
        if (isHidden) {
            openDropdown();
        } else {
            closeDropdown();
        }
    }

    function openDropdown() {
        penerimaList.classList.remove('hidden');
        dropdownArrow.style.transform = 'rotate(180deg)';
        penerimaInput.focus();
    }

    function closeDropdown() {
        penerimaList.classList.add('hidden');
        dropdownArrow.style.transform = 'rotate(0deg)';
        // Reset filter if closed without selection (optional, but good UX)
        // filterOptions(''); 
    }

    function selectPenerima(id, name) {
        penerimaHidden.value = id;
        penerimaInput.value = name;
        closeDropdown();
    }

    penerimaInput.addEventListener('focus', function() {
        openDropdown();
    });

    penerimaInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        filterOptions(value);
        openDropdown(); // Ensure it's open when typing
    });

    function filterOptions(value) {
        let hasVisible = false;
        options.forEach(option => {
            const name = option.getAttribute('data-name').toLowerCase();
            if (name.includes(value)) {
                option.classList.remove('hidden');
                hasVisible = true;
            } else {
                option.classList.add('hidden');
            }
        });

        if (!hasVisible) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('penerima_dropdown');
        if (dropdown && !dropdown.contains(e.target)) {
            closeDropdown();
            // If input is empty, clear hidden value too
            if (penerimaInput.value === '') {
                penerimaHidden.value = '';
            } 
            // Optional: reset input to match selected value if partial text entered?
            // For now, let's just leave it. If user types "Andi" but doesn't select, value remains "Andi" visually but hidden ID might be wrong/empty.
            // Better approach: check if current input matches the hidden ID's name.
        }
    });

    // Reset dropdown when modal opens
    const originalOpenUsageModal = openUsageModal;
    openUsageModal = function(id, name, stock, unit) {
        originalOpenUsageModal(id, name, stock, unit);
        // Reset dropdown state
        penerimaHidden.value = '';
        penerimaInput.value = '';
        filterOptions('');
        closeDropdown();

        // Reset mobil dropdown state
        mobilHidden.value = '';
        mobilInput.value = '';
        filterMobilOptions('');
        closeMobilDropdown();

        // Reset buntut dropdown state
        buntutHidden.value = '';
        buntutInput.value = '';
        filterBuntutOptions('');
        closeBuntutDropdown();

        // Reset kapal dropdown state
        kapalHidden.value = '';
        kapalInput.value = '';
        filterKapalOptions('');
        closeKapalDropdown();

        // Reset alat berat dropdown state
        alatBeratHidden.value = '';
        alatBeratInput.value = '';
        filterAlatBeratOptions('');
        closeAlatBeratDropdown();
    };

    // SEARCHABLE DROPDOWN MOBIL LOGIC
    const mobilInput = document.getElementById('mobil_search_input');
    const mobilList = document.getElementById('mobil_options_list');
    const mobilHidden = document.getElementById('mobil_id_hidden');
    const mobilDropdownArrow = document.getElementById('mobil_dropdown_arrow');
    const mobilOptions = document.querySelectorAll('.mobil-option');
    const mobilNoResults = document.getElementById('mobil_no_results');

    function toggleMobilDropdown() {
        const isHidden = mobilList.classList.contains('hidden');
        if (isHidden) {
            openMobilDropdown();
        } else {
            closeMobilDropdown();
        }
    }

    function openMobilDropdown() {
        mobilList.classList.remove('hidden');
        mobilDropdownArrow.style.transform = 'rotate(180deg)';
        mobilInput.focus();
    }

    function closeMobilDropdown() {
        mobilList.classList.add('hidden');
        mobilDropdownArrow.style.transform = 'rotate(0deg)';
    }

    function selectMobil(id, name) {
        mobilHidden.value = id;
        mobilInput.value = name;
        // Clear alat berat selection
        alatBeratHidden.value = '';
        alatBeratInput.value = '';
        closeMobilDropdown();
    }

    mobilInput.addEventListener('focus', function() {
        openMobilDropdown();
    });

    mobilInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        filterMobilOptions(value);
        openMobilDropdown();
    });

    // Add click event for dropdown arrow
    document.getElementById('mobil_dropdown_arrow').addEventListener('click', function() {
        toggleMobilDropdown();
    });

    function filterMobilOptions(value) {
        let hasVisible = false;
        mobilOptions.forEach(option => {
            const name = option.getAttribute('data-name').toLowerCase();
            if (name.includes(value)) {
                option.classList.remove('hidden');
                hasVisible = true;
            } else {
                option.classList.add('hidden');
            }
        });

        if (!hasVisible) {
            mobilNoResults.classList.remove('hidden');
        } else {
            mobilNoResults.classList.add('hidden');
        }
    }

    // Update the existing click outside listener to handle both dropdowns
    document.addEventListener('click', function(e) {
        // Penerima Dropdown
        const dropdown = document.getElementById('penerima_dropdown');
        if (dropdown && !dropdown.contains(e.target)) {
            closeDropdown();
            if (penerimaInput.value === '') {
                penerimaHidden.value = '';
            }
        }

        // Mobil Dropdown
        const mobilDropdown = document.getElementById('mobil_dropdown');
        if (mobilDropdown && !mobilDropdown.contains(e.target)) {
            closeMobilDropdown();
            if (mobilInput.value === '') {
                mobilHidden.value = '';
            }
        }

        // Buntut Dropdown
        const buntutDropdown = document.getElementById('buntut_dropdown');
        if (buntutDropdown && !buntutDropdown.contains(e.target)) {
            closeBuntutDropdown();
            if (buntutInput.value === '') {
                buntutHidden.value = '';
            }
        }

        // Kapal Dropdown
        const kapalDropdown = document.getElementById('kapal_dropdown');
        if (kapalDropdown && !kapalDropdown.contains(e.target)) {
            closeKapalDropdown();
            if (kapalInput.value === '') {
                kapalHidden.value = '';
            }
        }

        // Alat Berat Dropdown
        const alatBeratDropdown = document.getElementById('alat_berat_dropdown');
        if (alatBeratDropdown && !alatBeratDropdown.contains(e.target)) {
            closeAlatBeratDropdown();
            if (alatBeratInput.value === '') {
                alatBeratHidden.value = '';
            }
        }
    });

    // SEARCHABLE DROPDOWN BUNTUT LOGIC
    const buntutInput = document.getElementById('buntut_search_input');
    const buntutList = document.getElementById('buntut_options_list');
    const buntutHidden = document.getElementById('buntut_id_hidden');
    const buntutDropdownArrow = document.getElementById('buntut_dropdown_arrow');
    const buntutOptions = document.querySelectorAll('.buntut-option');
    const buntutNoResults = document.getElementById('buntut_no_results');

    function toggleBuntutDropdown() {
        const isHidden = buntutList.classList.contains('hidden');
        if (isHidden) {
            openBuntutDropdown();
        } else {
            closeBuntutDropdown();
        }
    }

    function openBuntutDropdown() {
        buntutList.classList.remove('hidden');
        buntutDropdownArrow.style.transform = 'rotate(180deg)';
        buntutInput.focus();
    }

    function closeBuntutDropdown() {
        buntutList.classList.add('hidden');
        buntutDropdownArrow.style.transform = 'rotate(0deg)';
    }

    function selectBuntut(id, name) {
        buntutHidden.value = id;
        buntutInput.value = name;
        closeBuntutDropdown();
    }

    buntutInput.addEventListener('focus', function() {
        openBuntutDropdown();
    });

    buntutInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        filterBuntutOptions(value);
        openBuntutDropdown();
    });

    // Add click event for dropdown arrow
    document.getElementById('buntut_dropdown_arrow').addEventListener('click', function() {
        toggleBuntutDropdown();
    });

    function filterBuntutOptions(value) {
        let hasVisible = false;
        buntutOptions.forEach(option => {
            const name = option.getAttribute('data-name').toLowerCase();
            if (name.includes(value)) {
                option.classList.remove('hidden');
                hasVisible = true;
            } else {
                option.classList.add('hidden');
            }
        });

        if (!hasVisible) {
            buntutNoResults.classList.remove('hidden');
        } else {
            buntutNoResults.classList.add('hidden');
        }
    }

    // SEARCHABLE DROPDOWN KAPAL LOGIC
    const kapalInput = document.getElementById('kapal_search_input');
    const kapalList = document.getElementById('kapal_options_list');
    const kapalHidden = document.getElementById('kapal_id_hidden');
    const kapalDropdownArrow = document.getElementById('kapal_dropdown_arrow');
    const kapalOptions = document.querySelectorAll('.kapal-option');
    const kapalNoResults = document.getElementById('kapal_no_results');

    function toggleKapalDropdown() {
        const isHidden = kapalList.classList.contains('hidden');
        if (isHidden) {
            openKapalDropdown();
        } else {
            closeKapalDropdown();
        }
    }

    function openKapalDropdown() {
        kapalList.classList.remove('hidden');
        kapalDropdownArrow.style.transform = 'rotate(180deg)';
        kapalInput.focus();
    }

    function closeKapalDropdown() {
        kapalList.classList.add('hidden');
        kapalDropdownArrow.style.transform = 'rotate(0deg)';
    }

    function selectKapal(id, name) {
        kapalHidden.value = id;
        kapalInput.value = name;
        closeKapalDropdown();
    }

    kapalInput.addEventListener('focus', function() {
        openKapalDropdown();
    });

    kapalInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        filterKapalOptions(value);
        openKapalDropdown();
    });

    // Add click event for dropdown arrow
    document.getElementById('kapal_dropdown_arrow').addEventListener('click', function() {
        toggleKapalDropdown();
    });

    function filterKapalOptions(value) {
        let hasVisible = false;
        kapalOptions.forEach(option => {
            const name = option.getAttribute('data-name').toLowerCase();
            if (name.includes(value)) {
                option.classList.remove('hidden');
                hasVisible = true;
            } else {
                option.classList.add('hidden');
            }
        });

        if (!hasVisible) {
            kapalNoResults.classList.remove('hidden');
        } else {
            kapalNoResults.classList.add('hidden');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!kapalInput.contains(event.target) && !kapalList.contains(event.target)) {
            closeKapalDropdown();
        }
        if (!alatBeratInput.contains(event.target) && !alatBeratList.contains(event.target)) {
            closeAlatBeratDropdown();
        }
    });

    // SEARCHABLE DROPDOWN ALAT BERAT LOGIC
    const alatBeratInput = document.getElementById('alat_berat_search_input');
    const alatBeratList = document.getElementById('alat_berat_options_list');
    const alatBeratHidden = document.getElementById('alat_berat_id_hidden');
    const alatBeratDropdownArrow = document.getElementById('alat_berat_dropdown_arrow');
    const alatBeratOptions = document.querySelectorAll('.alat-berat-option');
    const alatBeratNoResults = document.getElementById('alat_berat_no_results');

    function toggleAlatBeratDropdown() {
        const isHidden = alatBeratList.classList.contains('hidden');
        if (isHidden) {
            openAlatBeratDropdown();
        } else {
            closeAlatBeratDropdown();
        }
    }

    function openAlatBeratDropdown() {
        alatBeratList.classList.remove('hidden');
        alatBeratDropdownArrow.style.transform = 'rotate(180deg)';
        alatBeratInput.focus();
    }

    function closeAlatBeratDropdown() {
        alatBeratList.classList.add('hidden');
        alatBeratDropdownArrow.style.transform = 'rotate(0deg)';
    }

    function selectAlatBerat(id, name) {
        alatBeratHidden.value = id;
        alatBeratInput.value = name;
        // Clear mobil selection
        mobilHidden.value = '';
        mobilInput.value = '';
        closeAlatBeratDropdown();
    }

    alatBeratInput.addEventListener('focus', function() {
        openAlatBeratDropdown();
    });

    alatBeratInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        filterAlatBeratOptions(value);
        openAlatBeratDropdown();
    });

    // Add click event for dropdown arrow
    document.getElementById('alat_berat_dropdown_arrow').addEventListener('click', function() {
        toggleAlatBeratDropdown();
    });

    function filterAlatBeratOptions(value) {
        let hasVisible = false;
        alatBeratOptions.forEach(option => {
            const name = option.getAttribute('data-name').toLowerCase();
            if (name.includes(value)) {
                option.classList.remove('hidden');
                hasVisible = true;
            } else {
                option.classList.add('hidden');
            }
        });

        if (!hasVisible) {
            alatBeratNoResults.classList.remove('hidden');
        } else {
            alatBeratNoResults.classList.add('hidden');
        }
    }

    // Handle form submission with AJAX
    document.getElementById('usageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const errorDiv = document.getElementById('errorMessage');
        
        // Hide previous error
        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';
        
        // Disable submit button
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.redirected) {
                // Success redirect
                window.location.href = response.url;
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.errors) {
                // Validation errors
                let errorMessages = [];
                for (let field in data.errors) {
                    errorMessages.push(data.errors[field].join(', '));
                }
                errorDiv.textContent = 'Pengambilan barang gagal: ' + errorMessages.join('; ');
                errorDiv.classList.remove('hidden');
            } else if (data && data.message) {
                errorDiv.textContent = data.message;
                errorDiv.classList.remove('hidden');
            }
        })
        .catch(error => {
            errorDiv.textContent = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
            errorDiv.classList.remove('hidden');
        })
        .finally(() => {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    });

    // Bulk Selection Logic
    const selectAll = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCountLabel = document.getElementById('selected-count');
    const btnBulkPranota = document.getElementById('btnBulkPranota');
    const btnCancelSelection = document.getElementById('btnCancelSelection');

    let selectedIds = [];

    function updateBulkActions() {
        const checkedBoxes = Array.from(itemCheckboxes).filter(cb => cb.checked);
        selectedIds = checkedBoxes.map(cb => cb.value);

        if (selectedCountLabel) {
            selectedCountLabel.textContent = selectedIds.length;
        }

        if (bulkActions) {
            if (selectedIds.length > 0) {
                bulkActions.classList.remove('hidden');
            } else {
                bulkActions.classList.add('hidden');
            }
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateBulkActions();
        });
    }

    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateBulkActions();
            if (selectAll) {
                const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
                selectAll.checked = allChecked;
                selectAll.indeterminate = !allChecked && selectedIds.length > 0;
            }
        });
    });

    if (btnCancelSelection) {
        btnCancelSelection.addEventListener('click', function() {
            itemCheckboxes.forEach(cb => {
                cb.checked = false;
            });
            if (selectAll) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            }
            updateBulkActions();
        });
    }

    if (btnBulkPranota) {
        btnBulkPranota.addEventListener('click', function() {
            if (selectedIds.length === 0) return;
            openPranotaModal();
        });
    }

    // Modal Functions
    const getPranotaModal = () => document.getElementById('pranotaModal');

    function openPranotaModal() {
        const tbody = document.getElementById('pranota-items');
        tbody.innerHTML = '';
        
        let totalBiaya = 0;
        let count = 0;

        itemCheckboxes.forEach(cb => {
            if (cb.checked) {
                count++;
                const nama = cb.dataset.nama || '-';
                const kode = cb.dataset.kode || '-';
                const harga = parseFloat(cb.dataset.harga || 0);
                const jumlah = parseFloat(cb.dataset.jumlah || 0);
                const biaya = harga * jumlah;
                totalBiaya += biaya;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-4 py-3 whitespace-nowrap text-gray-500">${count}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="font-bold text-gray-900">${nama}</div>
                        <div class="text-[10px] text-gray-400">Bukti: ${kode}</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-center font-bold text-gray-800">${jumlah.toLocaleString('id-ID')}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-right font-bold text-indigo-600">Rp ${biaya.toLocaleString('id-ID')}</td>
                `;
                tbody.appendChild(row);
            }
        });

        document.getElementById('total-count-display').textContent = count;
        const totalDisplay = document.getElementById('total-biaya-display');
        totalDisplay.dataset.original = totalBiaya;
        updateTotalBiayaDisplay();

        const modal = getPranotaModal();
        if (modal) {
            modal.classList.remove('hidden');
        }
        generateNomorPranota();
    }

    function closePranotaModal() {
        const modal = getPranotaModal();
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function updateTotalBiayaDisplay() {
        const display = document.getElementById('total-biaya-display');
        const original = parseFloat(display.dataset.original || 0);
        const adj = parseFloat(document.getElementById('adjustment').value || 0);
        const total = original + adj;
        display.textContent = `Rp ${total.toLocaleString('id-ID')}`;
    }

    const adjInput = document.getElementById('adjustment');
    if (adjInput) {
        adjInput.addEventListener('input', updateTotalBiayaDisplay);
    }

    function generateNomorPranota() {
        const input = document.getElementById('nomor_pranota');
        input.value = 'Generating...';
        
        fetch("{{ route('stock-amprahan.generate-nomor-pranota') }}", {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    input.value = data.nomor_pranota;
                } else {
                    alert(data.message || 'Gagal generate nomor');
                    input.value = '';
                }
            })
            .catch(err => {
                console.error(err);
                input.value = 'Error';
            });
    }

    const btnConfirm = document.getElementById('btnConfirmPranota');
    if (btnConfirm) {
        btnConfirm.addEventListener('click', function() {
            const nomor = document.getElementById('nomor_pranota').value;
            const tanggal = document.getElementById('tanggal_pranota').value;
            const accurate = document.getElementById('nomor_accurate').value;
            const adj = document.getElementById('adjustment').value;
            const ket = document.getElementById('keterangan_pranota').value;
            
            if (!nomor || nomor === 'Generating...' || nomor === 'Error') {
                alert('Nomor pranota belum tersedia');
                return;
            }

            const items = [];
            itemCheckboxes.forEach(cb => {
                if (cb.checked) {
                    items.push({
                        id: cb.value,
                        nama: cb.dataset.nama,
                        kode: cb.dataset.kode,
                        harga: cb.dataset.harga,
                        jumlah: cb.dataset.jumlah,
                        satuan: cb.dataset.satuan
                    });
                }
            });

            const btn = this;
            btn.disabled = true;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

            fetch("{{ route('stock-amprahan.masuk-pranota') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    nomor_pranota: nomor,
                    tanggal_pranota: tanggal,
                    nomor_accurate: accurate,
                    adjustment: adj,
                    keterangan: ket,
                    items: items
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Terjadi kesalahan');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan saat menghubungi server');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        });
    }

    window.onclick = function(event) {
        const modal = getPranotaModal();
        if (event.target == modal) {
            closePranotaModal();
        }
    }
</script>

<!-- Modal Masuk Pranota -->
<div id="pranotaModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 transition-opacity duration-300">
    <div class="relative top-20 mx-auto p-8 border w-11/12 max-w-2xl shadow-2xl rounded-2xl bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-800">Konfirmasi Masuk Pranota</h3>
                <button type="button" onclick="closePranotaModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-6">
                <p class="text-sm text-gray-600 leading-relaxed mb-6">Berikut adalah detail barang yang akan dimasukkan ke pranota. Semua barang yang telah Anda pilih akan diproses.</p>
                
                <div class="mb-6">
                    <label for="nomor_pranota" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nomor Pranota <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-3">
                        <div class="relative flex-1">
                            <input type="text" id="nomor_pranota" name="nomor_pranota" required readonly
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-gray-700 font-medium"
                                   placeholder="Loading nomor pranota...">
                        </div>
                        <button type="button" onclick="generateNomorPranota()" 
                                class="px-5 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl transition-all shadow-sm hover:shadow-md active:scale-95"
                                title="Generate nomor baru">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <p class="text-[11px] text-gray-500 mt-2 font-medium">Format: PSA-MM-YY-000001 (auto-generate)</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="tanggal_pranota" class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="tanggal_pranota" name="tanggal_pranota" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-gray-700"
                               value="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label for="nomor_accurate" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nomor Accurate
                        </label>
                        <input type="text" id="nomor_accurate" name="nomor_accurate"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-gray-700"
                               placeholder="Masukkan nomor accurate...">
                    </div>
                </div>
                
                <div class="overflow-x-auto rounded-xl border border-gray-200 mb-6 font-mono text-xs">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Nama Barang</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider text-center">Jumlah</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider text-right">Biaya</th>
                            </tr>
                        </thead>
                        <tbody id="pranota-items" class="bg-white divide-y divide-gray-100">
                            <!-- Items will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
                
                <div class="flex flex-col gap-2 mb-6 border-t border-gray-100 pt-4">
                    <div class="flex justify-between text-sm font-medium text-gray-600">
                        <span>Total item yang dipilih:</span>
                        <span id="total-count-display" class="font-bold text-gray-900">0</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-gray-900">
                        <span>Total Biaya:</span>
                        <span id="total-biaya-display" class="text-indigo-600">Rp 0</span>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="adjustment" class="block text-sm font-semibold text-gray-700 mb-2">
                        Adjustment (Opsional)
                    </label>
                    <input type="number" id="adjustment" name="adjustment"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                           placeholder="Masukkan nilai adjustment (bisa negatif)...">
                    <p class="text-[11px] text-gray-500 mt-2 font-medium italic">Nilai ini akan ditambahkan ke total biaya. Gunakan nilai negatif for pengurangan.</p>
                </div>

                <div class="mb-2">
                    <label for="keterangan_pranota" class="block text-sm font-semibold text-gray-700 mb-2">
                        Keterangan
                    </label>
                    <textarea id="keterangan_pranota" name="keterangan_pranota" rows="3"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                              placeholder="Masukkan keterangan for pranota..."></textarea>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 mt-8">
                <button type="button" onclick="closePranotaModal()"
                        class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all active:scale-95">
                    Batal
                </button>
                <button type="button" id="btnConfirmPranota"
                        class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg active:scale-95 flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Konfirmasi Masuk Pranota
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

