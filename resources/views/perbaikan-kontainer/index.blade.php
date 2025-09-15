@extends('layouts.app')

@section('title', 'Perbaikan Kontainer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Perbaikan Kontainer</h1>
                <p class="text-gray-600 mt-1">Kelola data perbaikan kontainer</p>
            </div>
            @can('perbaikan-kontainer.create')
            <a href="{{ route('perbaikan-kontainer.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Perbaikan
            </a>
            @endcan
        </div>

        <!-- Filters -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('perbaikan-kontainer.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="belum_masuk_pranota" {{ request('status') == 'belum_masuk_pranota' ? 'selected' : '' }}>Belum Masuk Pranota</option>
                        <option value="sudah_masuk_pranota" {{ request('status') == 'sudah_masuk_pranota' ? 'selected' : '' }}>Sudah Masuk Pranota</option>
                        <option value="sudah_dibayar" {{ request('status') == 'sudah_dibayar' ? 'selected' : '' }}>Sudah Dibayar</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Kontainer</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nomor kontainer..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        Filter
                    </button>
                    <a href="{{ route('perbaikan-kontainer.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontainer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Memo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kerusakan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya Perbaikan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($perbaikanKontainers as $index => $perbaikan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $loop->iteration + ($perbaikanKontainers->currentPage() - 1) * $perbaikanKontainers->perPage() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $perbaikan->kontainer->nomor_kontainer ?? 'N/A' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $perbaikan->kontainer->ukuran ?? '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $perbaikan->nomor_memo_perbaikan ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $perbaikan->deskripsi_perbaikan }}">
                                {{ Str::limit($perbaikan->deskripsi_perbaikan, 30) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $perbaikan->biaya_perbaikan ? 'Rp ' . number_format($perbaikan->biaya_perbaikan, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $perbaikan->status_color }}">
                                {{ $perbaikan->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $perbaikan->tanggal_perbaikan ? \Carbon\Carbon::parse($perbaikan->tanggal_perbaikan)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $perbaikan->tanggal_selesai ? \Carbon\Carbon::parse($perbaikan->tanggal_selesai)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                @can('perbaikan-kontainer.view')
                                <a href="{{ route('perbaikan-kontainer.show', $perbaikan) }}"
                                   class="text-blue-600 hover:text-blue-900">Lihat</a>
                                @endcan
                                @can('perbaikan-kontainer.update')
                                <a href="{{ route('perbaikan-kontainer.edit', $perbaikan) }}"
                                   class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @endcan
                                @can('perbaikan-kontainer.delete')
                                <form method="POST" action="{{ route('perbaikan-kontainer.destroy', $perbaikan) }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data perbaikan ini?')"
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
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data perbaikan kontainer ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($perbaikanKontainers->hasPages())
        <div class="mt-6">
            {{ $perbaikanKontainers->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
