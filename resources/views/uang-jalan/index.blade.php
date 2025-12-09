@extends('layouts.app')

@section('page_title', 'Daftar Uang Jalan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Daftar Uang Jalan</h1>
                        <p class="text-gray-600 mt-1">Kelola data uang jalan yang telah diinput</p>
                    </div>
                          @can('uang-jalan-create')
                          <div class="inline-flex items-center gap-2">
                                <a href="{{ route('uang-jalan.select-surat-jalan') }}" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Uang Jalan
                        </a>

                        @if(Route::has('uang-jalan.adjustment.select-surat-jalan'))
                        @can('uang-jalan-create')
                        <a href="{{ route('uang-jalan.adjustment.select-surat-jalan') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm"
                           title="Tambah Uang Jalan Pengurangan/Penambahan">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                            Tambah Uang Jalan Pengurangan/Penambahan
                        </a>
                        @endcan
                        @endif
                    </div>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Filter dan Search -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4">
                <form method="GET" action="{{ route('uang-jalan.index') }}" id="filterForm">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <!-- Search Input -->
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                            <div class="relative">
                                <input type="text" 
                                       id="search" 
                                       name="search" 
                                       value="{{ $search }}" 
                                       placeholder="Cari nomor uang jalan, memo, supir, no surat jalan..."
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select id="status" name="status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date From -->
                        <div>
                            <label for="tanggal_dari" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Dari</label>
                            <input type="date" 
                                   id="tanggal_dari" 
                                   name="tanggal_dari" 
                                   value="{{ $tanggal_dari }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>

                        <!-- Date To -->
                        <div>
                            <label for="tanggal_sampai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Sampai</label>
                            <input type="date" 
                                   id="tanggal_sampai" 
                                   name="tanggal_sampai" 
                                   value="{{ $tanggal_sampai }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-gray-200">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('uang-jalan.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset
                        </a>
                        @can('uang-jalan-export')
                        <a href="{{ route('uang-jalan.export') }}?{{ http_build_query(request()->only(['search','status','tanggal_dari','tanggal_sampai'])) }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Excel
                        </a>
                        @endcan
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Uang Jalan -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            @if($uangJalans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs table-fixed resizable-table" id="uangJalanTable">
                        <colgroup>
                            <col class="w-12">   <!-- No -->
                            <col class="w-28">   <!-- No Uang Jalan -->
                            <col class="w-32">   <!-- No Surat Jalan -->
                            <col class="w-24">   <!-- Tanggal UJ -->
                            <col class="w-24">   <!-- Supir -->
                            <col class="w-24">   <!-- Total -->
                            <col class="w-24">   <!-- Status -->
                            <col class="w-20">   <!-- Aksi -->
                        </colgroup>
                        <thead class="bg-gray-50">
                            <tr><th class="resizable-th px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight w-8" style="position: relative;">No<div class="resize-handle"></div></th><th class="resizable-th px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight w-24" style="position: relative;">No Uang Jalan<div class="resize-handle"></div></th><th class="resizable-th px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight w-32" style="position: relative;">No Surat Jalan<div class="resize-handle"></div></th><th class="resizable-th px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight w-20" style="position: relative;">Tanggal UJ<div class="resize-handle"></div></th><th class="resizable-th px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight w-24" style="position: relative;">Supir<div class="resize-handle"></div></th><th class="resizable-th px-2 py-1 text-right text-xs font-medium text-gray-500 uppercase tracking-tight w-20" style="position: relative;">Total<div class="resize-handle"></div></th><th class="resizable-th px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-tight w-24" style="position: relative;">Status<div class="resize-handle"></div></th><th class="px-1 py-1 text-center text-xs font-medium text-gray-500 uppercase tracking-tight w-20">Aksi</th></tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($uangJalans as $index => $uangJalan)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900 font-medium w-8">
                                        {{ $uangJalans->firstItem() + $index }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs font-medium text-gray-900 w-24">
                                        {{ $uangJalan->nomor_uang_jalan ?? '-' }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs font-medium text-gray-900 w-32">
                                        {{ $uangJalan->suratJalan->no_surat_jalan ?? '-' }}
                                    </td>
                                    <td class="px-1 py-1 whitespace-nowrap text-xs text-gray-900 w-20">
                                        {{ $uangJalan->tanggal_uang_jalan ? $uangJalan->tanggal_uang_jalan->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900 w-24">
                                        {{ Str::limit($uangJalan->suratJalan->supir ?? '-', 15) }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-right text-xs font-semibold text-blue-600 w-20">
                                        {{ number_format($uangJalan->jumlah_total ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-1 py-1 whitespace-nowrap w-24">
                                        @php
                                            $statusConfig = [
                                                'belum_dibayar' => ['bg-yellow-100', 'text-yellow-800', 'Belum Bayar'],
                                                'belum_masuk_pranota' => ['bg-orange-100', 'text-orange-800', 'Belum Pranota'],
                                                'sudah_masuk_pranota' => ['bg-blue-100', 'text-blue-800', 'Sudah Pranota'],
                                                'lunas' => ['bg-green-100', 'text-green-800', 'Lunas'],
                                                'dibatalkan' => ['bg-red-100', 'text-red-800', 'Batal']
                                            ];
                                            $config = $statusConfig[$uangJalan->status] ?? ['bg-gray-100', 'text-gray-800', ucfirst($uangJalan->status)];
                                        @endphp
                                        <span class="inline-flex px-1.5 py-0.5 text-xs font-medium rounded {{ $config[0] }} {{ $config[1] }}">
                                            {{ $config[2] }}
                                        </span>
                                    </td>
                                    <td class="px-1 py-1 whitespace-nowrap text-center w-20">
                                        <div class="flex justify-center space-x-0.5">
                                            @can('uang-jalan-view')
                                            <a href="{{ route('uang-jalan.show', $uangJalan->id) }}" 
                                               class="inline-flex items-center p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors"
                                               title="Detail">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            @endcan
                                            
                                            @can('uang-jalan-update')
                                            @if(in_array($uangJalan->status, ['belum_dibayar', 'belum_masuk_pranota']))
                                            <a href="{{ route('uang-jalan.edit', $uangJalan->id) }}" 
                                               class="inline-flex items-center p-1.5 text-amber-600 hover:text-amber-800 hover:bg-amber-50 rounded transition-colors"
                                               title="Edit">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            @endif
                                            @endcan
                                            
                                            @can('uang-jalan-delete')
                                            @if(in_array($uangJalan->status, ['belum_dibayar', 'belum_masuk_pranota']))
                                            <button type="button" 
                                                    class="inline-flex items-center p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors"
                                                    title="Hapus"
                                                    onclick="confirmDelete('{{ $uangJalan->id }}', '{{ $uangJalan->nomor_uang_jalan ?? $uangJalan->suratJalan->no_surat_jalan ?? '' }}')">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                            @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($uangJalans->hasPages())
                    <div class="border-t border-gray-200 px-4 py-2">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-2">
                            <div class="text-xs text-gray-500">
                                {{ $uangJalans->firstItem() }}-{{ $uangJalans->lastItem() }} dari {{ $uangJalans->total() }}
                            </div>
                            <div class="flex justify-center">
                                {{ $uangJalans->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data uang jalan</h3>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto">
                        Belum ada uang jalan yang diinput atau tidak ada yang memenuhi kriteria filter yang Anda tentukan.
                    </p>
                          @can('uang-jalan-create')
                          <div class="inline-flex items-center gap-2 justify-center">
                                <a href="{{ route('uang-jalan.select-surat-jalan') }}" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Uang Jalan Pertama
                        </a>

                        @if(Route::has('uang-jalan.adjustment.select-surat-jalan'))
                        @can('uang-jalan-create')
                        <a href="{{ route('uang-jalan.adjustment.select-surat-jalan') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm"
                           title="Tambah Uang Jalan Pengurangan/Penambahan">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                            Tambah Uang Jalan Pengurangan/Penambahan
                        </a>
                        @endcan
                        @endif
                    </div>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Hapus</h3>
            <p class="text-sm text-gray-500 mb-1">
                Apakah Anda yakin ingin menghapus uang jalan
            </p>
            <p class="text-sm font-semibold text-gray-900 mb-4">
                <span id="deleteItemName"></span>?
            </p>
            <div class="flex items-center text-sm text-red-600 bg-red-50 rounded-lg p-3 mb-6">
                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="flex gap-3 justify-center">
                <button type="button" 
                        onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-medium rounded-lg transition-colors duration-200">
                    Batal
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enter key untuk search
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filterForm').submit();
            }
        });
    }
});

function confirmDelete(id, identifier) {
    document.getElementById('deleteItemName').textContent = identifier;
    document.getElementById('deleteForm').action = '{{ route('uang-jalan.destroy', ':id') }}'.replace(':id', id);
    
    // Show modal
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endpush