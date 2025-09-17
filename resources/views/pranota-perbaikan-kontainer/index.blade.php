@extends('layouts.app')

@section('title', 'Pranota Perbaikan Kontainer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Pranota Perbaikan Kontainer</h1>
                <p class="text-gray-600 mt-1">Kelola data pranota perbaikan kontainer</p>
            </div>
            @can('pranota-perbaikan-kontainer.create')
            <a href="{{ route('pranota-perbaikan-kontainer.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Pranota
            </a>
            @endcan
        </div>

        <!-- Filters -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('pranota-perbaikan-kontainer.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dari</label>
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sampai</label>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nomor kontainer, teknisi..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="flex items-end space-x-2 md:col-span-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        Filter
                    </button>
                    <a href="{{ route('pranota-perbaikan-kontainer.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Pranota</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Disetujui</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['approved'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Selesai</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['completed'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perbaikan Kontainer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pranota</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Teknisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estimasi Biaya</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estimasi Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pranotaPerbaikanKontainers as $pranota)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $loop->iteration + ($pranotaPerbaikanKontainers->currentPage() - 1) * $pranotaPerbaikanKontainers->perPage() }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $pranota->perbaikanKontainer->kontainer->nomor_kontainer ?? 'N/A' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ Str::limit($pranota->perbaikanKontainer->deskripsi_perbaikan ?? $pranota->deskripsi_pekerjaan, 50) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $pranota->tanggal_pranota ? \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $pranota->nama_teknisi ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $pranota->estimasi_biaya ? 'Rp ' . number_format($pranota->estimasi_biaya, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $pranota->estimasi_waktu ? $pranota->estimasi_waktu . ' jam' : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($pranota->status == 'approved')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Disetujui
                                </span>
                            @elseif($pranota->status == 'pending')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @elseif($pranota->status == 'completed')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Selesai
                                </span>
                            @elseif($pranota->status == 'draft')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Draft
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                    {{ ucfirst($pranota->status ?? 'Unknown') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                @can('pranota-perbaikan-kontainer.view')
                                <a href="{{ route('pranota-perbaikan-kontainer.show', $pranota) }}"
                                   class="text-blue-600 hover:text-blue-900">Lihat</a>
                                @endcan
                                @can('pranota-perbaikan-kontainer.edit')
                                <a href="{{ route('pranota-perbaikan-kontainer.edit', $pranota) }}"
                                   class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @endcan
                                @can('pranota-perbaikan-kontainer.delete')
                                <form method="POST" action="{{ route('pranota-perbaikan-kontainer.destroy', $pranota) }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data pranota perbaikan kontainer ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pranotaPerbaikanKontainers->hasPages())
        <div class="mt-6">
            {{ $pranotaPerbaikanKontainers->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
