@extends('layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'Kelola Mesin')
@section('page_title', 'Kelola Mesin')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Mesin</h1>
                    <p class="mt-1 text-sm text-gray-600">Daftar dan kelola mesin fingerprint untuk absensi</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    @can('mesin-create')
                    <a href="{{ route('master.mesin.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Mesin
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

        <!-- Notifikasi Error -->
        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9a1 1 0 012 0v4a1 1 0 01-2 0V9zm0 6a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form action="{{ route('master.mesin.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        Cari Mesin
                    </label>
                    <div class="relative">
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Cari berdasarkan nama, kode, tipe, atau IP address..."
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 lg:items-end">
                    <div class="flex-1 sm:flex-none">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status"
                                class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                            <option value="">Semua Status</option>
                            <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Rusak" {{ request('status') === 'Rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="Perbaikan" {{ request('status') === 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                            <option value="Nonaktif" {{ request('status') === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        Cari
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('master.mesin.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                            Hapus Filter
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Daftar Mesin</h3>
                <p class="mt-1 text-sm text-gray-600">Kelola koneksi dan sinkronisasi data dari mesin fingerprint</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Mesin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mesin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Mesin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP & Port</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-xs">
                        @forelse ($mesins as $index => $mesin)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                    {{ $mesins->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-indigo-600">
                                    {{ $mesin->kode_mesin }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                    {{ $mesin->nama_mesin }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    {{ $mesin->tipe_mesin }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600 font-mono">
                                    {{ $mesin->ip_address ?: '-' }}{{ $mesin->ip_address ? ':' . $mesin->port : '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $badgeColor = 'bg-gray-100 text-gray-800';
                                        if ($mesin->status === 'Aktif') {
                                            $badgeColor = 'bg-green-100 text-green-800';
                                        } elseif ($mesin->status === 'Rusak') {
                                            $badgeColor = 'bg-red-100 text-red-800';
                                        } elseif ($mesin->status === 'Perbaikan') {
                                            $badgeColor = 'bg-yellow-100 text-yellow-800';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeColor }}">
                                        {{ $mesin->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 max-w-xs truncate">
                                    {{ $mesin->keterangan ?: '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-medium space-y-1 md:space-y-0 md:space-x-2">
                                    @if($mesin->ip_address)
                                        <button type="button" 
                                                onclick="testKoneksi('{{ $mesin->id }}', this)" 
                                                class="px-2 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors">
                                            Test Koneksi
                                        </button>
                                        
                                        <form action="{{ route('master.mesin.sync-logs', $mesin->id) }}" method="POST" class="inline" onsubmit="showSyncLoader(this)">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200 transition-colors">
                                                Sync Absensi
                                            </button>
                                        </form>
                                    @endif

                                    @can('mesin-update')
                                    <a href="{{ route('master.mesin.edit', $mesin->id) }}" class="text-indigo-600 hover:text-indigo-900 inline-block px-1">Edit</a>
                                    @endcan
                                    
                                    <button type="button"
                                            onclick="showAuditLog('{{ get_class($mesin) }}', '{{ $mesin->id }}', '{{ $mesin->kode_mesin }}')"
                                            class="text-purple-600 hover:text-purple-900 font-medium cursor-pointer inline-block px-1"
                                            title="Lihat Riwayat Perubahan">
                                        Riwayat
                                    </button>

                                    @can('mesin-delete')
                                    <a href="#" onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin menghapus mesin ini?')) { document.getElementById('delete-form-{{ $mesin->id }}').submit(); }" class="text-red-600 hover:text-red-900 inline-block px-1">Hapus</a>
                                    <form id="delete-form-{{ $mesin->id }}" action="{{ route('master.mesin.destroy', $mesin->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada data mesin</h3>
                                        <p class="text-sm text-gray-500">Belum ada mesin yang terdaftar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($mesins->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $mesins->links() }}
            </div>
            @endif
        </div>

    </div>
</div>

<!-- Modal Loading Sync -->
<div id="sync-loader-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-sm w-full text-center shadow-xl">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600 mx-auto mb-4"></div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Menghubungkan & Sinkronisasi</h3>
        <p class="text-sm text-gray-500">Mohon tunggu, sedang mendownload log absensi dari mesin fingerprint...</p>
    </div>
</div>

<!-- Audit Log Modal -->
@include('components.audit-log-modal')

<script>
    function testKoneksi(id, buttonElement) {
        const originalText = $(buttonElement).text();
        $(buttonElement).prop('disabled', true).text('Menghubungkan...');
        
        $.ajax({
            url: `/master/mesin/${id}/test-connection`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $(buttonElement).prop('disabled', false).text(originalText);
                if (response.success) {
                    alert(response.message);
                } else {
                    alert('Koneksi Gagal: ' + response.message);
                }
            },
            error: function(xhr) {
                $(buttonElement).prop('disabled', false).text(originalText);
                alert('Terjadi kesalahan sistem saat mencoba terhubung.');
            }
        });
    }

    function showSyncLoader(formElement) {
        $('#sync-loader-modal').removeClass('hidden');
    }
</script>
@endsection
