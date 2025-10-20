@extends('layouts.app')

@section('title','Master Stock Kontainer')
@section('page_title','Master Stock Kontainer')

@section('content')

<h2 class="text-xl font-bold text-gray-800 mb-4">Daftar Stock Kontainer</h2>

<div class="mb-4 flex justify-between items-center">
    <div class="flex space-x-4">
        <!-- Filter Status -->
        <select id="status-filter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
            <option value="">Semua Status</option>
            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
            <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Disewa</option>
            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Perbaikan</option>
            <option value="damaged" {{ request('status') == 'damaged' ? 'selected' : '' }}>Rusak</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
        </select>

        <!-- Search -->
        <div class="relative">
            <input type="text" id="search-input" placeholder="Cari nomor kontainer..." value="{{ request('search') }}"
                   class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm pl-4 pr-10">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="flex space-x-2">
        <!-- Import/Export Section -->
        <div class="flex space-x-2">
            <!-- Download Template Button -->
            <a href="{{ route('master.stock-kontainer.template') }}"
               class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Template CSV
            </a>

            <!-- Import Button -->
            <button type="button" onclick="document.getElementById('import-modal').style.display = 'block'"
                    class="inline-flex items-center px-3 py-2 border border-green-600 text-sm font-medium rounded-md shadow-sm text-green-600 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
                Import CSV
            </button>
        </div>

        <!-- Add New Button -->
        <a href="{{ route('master.stock-kontainer.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Tambah Stock Kontainer
        </a>
    </div>
</div>

@if (session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-4" role="alert">
    {{session('success')}}
</div>
@endif

@if (session('warning'))
<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-md mb-4" role="alert">
    <strong class="font-bold">Peringatan!</strong>
    <span class="block sm:inline">{{ session('warning') }}</span>
</div>
@endif

@if (session('error'))
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-4" role="alert">
    <strong class="font-bold">Error!</strong>
    <span class="block sm:inline">{{ session('error') }}</span>
</div>
@endif

{{-- Rows Per Page Selection --}}
@include('components.rows-per-page', [
    'routeName' => 'master.stock-kontainer.index',
    'paginator' => $stockKontainers,
    'entityName' => 'stock kontainer',
    'entityNamePlural' => 'stock kontainer'
])

<div class="overflow-x-auto shadow-md sm:rounded-lg table-container">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="sticky-table-header bg-gray-50 sticky top-0 z-10 shadow-sm">
            <tr>
                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Nomor Kontainer
                </th>
                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Ukuran
                </th>
                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tipe
                </th>
                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                </th>
                <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Aksi
                </th>
            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($stockKontainers as $stockKontainer)
            <tr>
                <td class="px-4 py-2 whitespace-nowrap text-center">
                    <div class="text-sm font-medium text-gray-900">{{ $stockKontainer->nomor_kontainer }}</div>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-center">
                    <div class="text-sm text-gray-500">{{ $stockKontainer->ukuran ?? '-' }}</div>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-center">
                    <div class="text-sm text-gray-500">{{ $stockKontainer->tipe_kontainer ?? '-' }}</div>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-center">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $stockKontainer->status_badge }}">
                        @switch($stockKontainer->status)
                            @case('available')
                                Tersedia
                                @break
                            @case('rented')
                                Disewa
                                @break
                            @case('maintenance')
                                Perbaikan
                                @break
                            @case('damaged')
                                Rusak
                                @break
                            @case('inactive')
                                Non-Aktif
                                @break
                            @default
                                {{ ucfirst(str_replace('_', ' ', $stockKontainer->status)) }}
                        @endswitch
                    </span>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end space-x-3 text-[10px]">
                        <a href="{{ route('master.stock-kontainer.show', $stockKontainer) }}" class="text-green-600 hover:text-green-800 hover:underline font-medium" title="Lihat Detail">Detail</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('master.stock-kontainer.edit', $stockKontainer) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium" title="Edit Data">Edit</a>
                        <span class="text-gray-300">|</span>
                        @can('audit-log-view')
                            <button type="button" class="audit-log-btn text-purple-600 hover:text-purple-800 hover:underline font-medium"
                                    data-model="{{ get_class($stockKontainer) }}"
                                    data-id="{{ $stockKontainer->id }}"
                                    title="Lihat Riwayat">
                                Riwayat
                            </button>
                        @endcan
                        <span class="text-gray-300">|</span>
                        <form action="{{ route('master.stock-kontainer.destroy', $stockKontainer) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah anda yakin ingin menghapus stock kontainer ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 hover:underline font-medium cursor-pointer border-none bg-transparent p-0" title="Hapus Data">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-2 text-center text-sm text-gray-500">Tidak ada data stock kontainer.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if ($stockKontainers->hasPages())
<div class="mt-4">
    {{ $stockKontainers->appends(request()->query())->links() }}
</div>
@endif

{{-- Import Modal --}}
<div id="import-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Import Stock Kontainer</h3>
                <button type="button" onclick="document.getElementById('import-modal').style.display = 'none'"
                        class="text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('master.stock-kontainer.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-2">
                        File CSV <span class="text-red-500">*</span>
                    </label>
                    <input type="file"
                           id="excel_file"
                           name="excel_file"
                           accept=".csv"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">
                        Pilih file CSV dengan format yang sesuai template. Maksimal 5MB.
                    </p>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mb-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-yellow-700">
                            <p class="font-medium">Panduan Import:</p>
                            <ul class="mt-1 list-disc list-inside text-xs">
                                <li>Download template CSV terlebih dahulu</li>
                                <li>Nomor kontainer: 11 karakter (format: ABCD123456X)</li>
                                <li>Status: available, rented, maintenance, damaged, inactive</li>
                                <li>Tahun pembuatan: opsional, angka antara 1900 - {{ date('Y') }} (boleh kosong)</li>
                                <li>Keterangan: opsional (boleh kosong)</li>
                                <li>Hapus baris contoh data sebelum import</li>
                                <li>Data yang sudah ada akan diperbarui</li>
                                <li>Duplikasi dengan master kontainer akan diset inactive</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <button type="button"
                            onclick="document.getElementById('import-modal').style.display = 'none'"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('status-filter');
    const searchInput = document.getElementById('search-input');

    function updateFilters() {
        const params = new URLSearchParams(window.location.search);

        if (statusFilter.value) {
            params.set('status', statusFilter.value);
        } else {
            params.delete('status');
        }

        if (searchInput.value) {
            params.set('search', searchInput.value);
        } else {
            params.delete('search');
        }

        window.location.href = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    }

    statusFilter.addEventListener('change', updateFilters);

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            updateFilters();
        }
    });
});
</script>

<!-- Include Audit Log Modal -->
@include('components.audit-log-modal')

@endsection
