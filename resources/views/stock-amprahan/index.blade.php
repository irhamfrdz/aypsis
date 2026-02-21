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
            <a href="{{ route('stock-amprahan.all-history') }}" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
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
                @if(isset($search) && $search)
                <a href="{{ route('stock-amprahan.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Stats Cards (Optional) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-50 text-blue-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Jenis Barang</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $items->total() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Bukti</th>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $item->nomor_bukti ?? '-' }}
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
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
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
                                                                 data-name="{{ $alat->kode_alat }} {{ $alat->nama }} {{ $alat->lokasi ?? '' }} {{ $alat->warna ?? '' }}"
                                                                 onclick="selectAlatBerat('{{ $alat->id }}', '{{ $alat->kode_alat }} - {{ $alat->nama }}@if(isset($alat->lokasi)) - {{ $alat->lokasi }}@endif@if(isset($alat->warna)) - {{ $alat->warna }}@endif')">
                                                                <span class="block font-medium">{{ $alat->kode_alat }} - {{ $alat->nama }}@if(isset($alat->lokasi)) - {{ $alat->lokasi }}@endif@if(isset($alat->warna)) - {{ $alat->warna }}@endif</span>
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
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobil</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapal</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alat Berat</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oleh</th>
                                            </tr>
                                        </thead>
                                        <tbody id="historyTableBody" class="bg-white divide-y divide-gray-200">
                                            <tr>
                                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">Memuat data...</td>
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
        document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Memuat data...</td></tr>';
        
        // Update full history link
        const fullHistoryLink = document.getElementById('fullHistoryLink');
        fullHistoryLink.href = `/stock-amprahan/${id}/history`;

        fetch(`/stock-amprahan/${id}/history`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('historyTableBody');
                tbody.innerHTML = '';
                
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada riwayat pengambilan</td></tr>';
                    return;
                }

                data.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.tanggal}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.jumlah}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.penerima}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.mobil || '-'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.kapal || '-'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.alat_berat || '-'}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">${item.keterangan || '-'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.created_by}</td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-sm text-red-500">Gagal memuat data</td></tr>';
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
</script>
@endsection
