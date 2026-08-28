@extends('layouts.app')


@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'Master Pengirim')
@section('page_title', 'Master Pengirim')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Master Pengirim</h1>
                    <p class="mt-1 text-sm text-gray-600">Kelola data pengirim dalam sistem</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    @can('master-pengirim-create')
                    <!-- Dropdown Import/Export -->
                    <div class="relative group" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                            <i class="fas fa-file-excel mr-2"></i> Excel <i class="fas fa-chevron-down ml-2 text-xs"></i>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 hidden"
                             :class="{'hidden': !open}">
                            <div class="py-1">
                                <!-- Download Data -->
                                <a href="{{ route('pengirim.export-data') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-download mr-2 text-emerald-600"></i> Export Data
                                </a>
                                <!-- Import Update -->
                                <button onclick="openImportUpdateModal(); open = false" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-file-upload mr-2 text-blue-600"></i> Import Update Data
                                </button>
                                <div class="border-t border-gray-100 my-1"></div>
                                <!-- Download Template (Lama) -->
                                <a href="{{ route('pengirim.download-template') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-file-csv mr-2 text-emerald-600"></i> Download Template Baru
                                </a>
                                <!-- Import Data Baru (Lama) -->
                                <a href="{{ route('pengirim.import') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-upload mr-2 text-yellow-600"></i> Import Data Baru
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <!-- Download Excel (Lama) -->
                                <a href="{{ route('pengirim.export-excel', ['search' => request('search')]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-table mr-2 text-emerald-600"></i> Download Excel
                                </a>
                            </div>
                        </div>
                    </div>
                    @endcan
                    @can('master-pengirim-create')
                    <a href="{{ route('pengirim.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Data
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Notifikasi Sukses -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Search Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('pengirim.index') }}" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari Pengirim
                    </label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari berdasarkan kode, nama pengirim, atau catatan..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('pengirim.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Daftar Pengirim</h3>
                <p class="mt-1 text-sm text-gray-600">Total: {{ $pengirims->total() }} pengirim</p>
            </div>

            <!-- Rows per page control -->
            <div class="px-6 py-3 border-b border-gray-200">
                @include('components.rows-per-page')
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 resizable-table" id="masterPengirimTable">
                    <thead class="bg-gray-50">
                        <tr><th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Nama Pengirim<div class="resize-handle"></div></th><th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Nickname 1<div class="resize-handle"></div></th><th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">PIC<div class="resize-handle"></div></th><th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Telepon<div class="resize-handle"></div></th><th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Alamat<div class="resize-handle"></div></th><th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Catatan<div class="resize-handle"></div></th><th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Status<div class="resize-handle"></div></th><th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th></tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-xs">
                        @forelse ($pengirims as $pengirim)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 font-medium">{{ $pengirim->nama_pengirim }}</td>
                                <td class="px-3 py-2 text-xs text-gray-900 max-w-xs truncate" title="{{ $pengirim->nickname1 }}">{{ $pengirim->nickname1 ?: '-' }}</td>
                                <td class="px-3 py-2 text-xs text-gray-900 max-w-xs truncate" title="{{ $pengirim->pic }}">{{ $pengirim->pic ?: '-' }}</td>
                                <td class="px-3 py-2 text-xs text-gray-900 max-w-xs truncate" title="{{ $pengirim->telepon }}">{{ $pengirim->telepon ?: '-' }}</td>
                                <td class="px-3 py-2 text-xs text-gray-900 max-w-xs truncate" title="{{ $pengirim->alamat }}">{{ $pengirim->alamat ?: '-' }}</td>
                                <td class="px-3 py-2 text-xs text-gray-900 max-w-xs truncate" title="{{ $pengirim->catatan }}">{{ $pengirim->catatan ?: '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $pengirim->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $pengirim->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <a href="{{ route('pengirim.edit', $pengirim) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <span class="text-gray-300 text-xs">|</span>
                                        <!-- Audit Log Link -->
                                        <button type="button"
                                                class="audit-log-btn text-purple-600 hover:text-purple-800 hover:underline text-xs font-medium cursor-pointer"
                                                data-model-type="{{ get_class($pengirim) }}"
                                                data-model-id="{{ $pengirim->id }}"
                                                data-item-name="{{ $pengirim->nama_pengirim }}"
                                                title="Lihat Riwayat Perubahan">
                                            Riwayat
                                        </button>
                                        <span class="text-gray-300 text-xs">|</span>
                                        <form action="{{ route('pengirim.destroy', $pengirim) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengirim ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-6 text-center text-xs text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-5v2m0 0v2m0-2h2m-2 0h-2"></path>
                                        </svg>
                                        <p class="text-gray-500 text-xs font-medium">Belum ada data pengirim</p>
                                        <p class="text-gray-400 text-xs mt-1">Tambah pengirim pertama untuk memulai</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @include('components.modern-pagination', ['paginator' => $pengirims, 'routeName' => 'pengirim.index'])
        </div>
    </div>
</div>

<!-- Audit Log Modal -->
@include('components.audit-log-modal')

<!-- Modal Import Update -->
<div id="importUpdateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-[32rem] shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Import Update Data</h3>
                <button onclick="closeImportUpdateModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('pengirim.import-update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="import_update_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih File Excel Hasil Export
                    </label>
                    <input type="file"
                           name="file"
                           id="import_update_file"
                           accept=".xlsx,.xls,.csv"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <h4 class="text-sm font-medium text-blue-800 mb-1">Cara Penggunaan:</h4>
                        <ol class="list-decimal ml-4 text-xs text-blue-700 space-y-1">
                            <li>Klik tombol <strong>Export Data</strong> untuk mengunduh data saat ini.</li>
                            <li>Buka file Excel, lalu isi field yang masih kosong (contoh: PIC, Telepon, Contact Person).</li>
                            <li>Upload kembali file Excel tersebut ke sini.</li>
                            <li>Sistem hanya akan memperbarui data jika field di database masih kosong (data lama yang sudah ada tidak akan ditimpa).</li>
                        </ol>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeImportUpdateModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Upload & Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@include('components.resizable-table')

@push('scripts')
<script>
// Menambahkan AlpineJS jika belum ada di layout
if (typeof Alpine === 'undefined') {
    let script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js';
    script.defer = true;
    document.head.appendChild(script);
}

function openImportUpdateModal() {
    document.getElementById('importUpdateModal').classList.remove('hidden');
}

function closeImportUpdateModal() {
    document.getElementById('importUpdateModal').classList.add('hidden');
    document.getElementById('import_update_file').value = '';
}

// Close modal when clicking outside
window.addEventListener('click', function(e) {
    let importUpdateModal = document.getElementById('importUpdateModal');
    
    if (e.target === importUpdateModal) {
        closeImportUpdateModal();
    }
});

$(document).ready(function() {
    initResizableTable('masterPengirimTable');
});
</script>
@endpush