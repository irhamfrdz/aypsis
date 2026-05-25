@extends('layouts.app')

@section('title', 'Daftar Perbaikan Kontainer')
@section('page_title', 'Perbaikan Kontainer')

@section('content')
<div class="container mx-auto px-4 py-4 overflow-y-auto h-full pb-24">
    <!-- Breadcrumbs / Header -->
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Daftar Perbaikan Kontainer</h1>
            <p class="text-xs text-gray-600">Kelola data perbaikan kontainer yang rusak dan dalam perawatan.</p>
        </div>
        <div>
            @can('perbaikan-kontainer-update')
            <a href="{{ route('perbaikan-kontainer.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                <i class="fas fa-plus mr-2"></i>
                Tambah Perbaikan
            </a>
            @endcan
        </div>
    </div>

    <!-- Session Alert Message -->
    @if(session('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 text-green-500 text-lg"></i>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Filters Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mb-6">
        <form action="{{ route('perbaikan-kontainer.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search Input -->
                <div>
                    <label for="search" class="block text-xs font-semibold text-gray-500 mb-1">Cari</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </span>
                        <input type="text" name="search" id="search" 
                               value="{{ request('search') }}"
                               class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none" 
                               placeholder="No. Perbaikan / Kontainer">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
                    <select name="status" id="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Draft)</option>
                        <option value="proses" {{ request('status') === 'proses' ? 'selected' : '' }}>Proses Perbaikan</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="batal" {{ request('status') === 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>

                <!-- Vendor Filter -->
                <div>
                    <label for="vendor_bengkel_id" class="block text-xs font-semibold text-gray-500 mb-1">Bengkel / Vendor</label>
                    <select name="vendor_bengkel_id" id="vendor_bengkel_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                        <option value="">Semua Bengkel</option>
                        @foreach($bengkels as $bengkel)
                            <option value="{{ $bengkel->id }}" {{ request('vendor_bengkel_id') == $bengkel->id ? 'selected' : '' }}>
                                {{ $bengkel->nama_bengkel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range Start -->
                <div>
                    <label for="tanggal_masuk_start" class="block text-xs font-semibold text-gray-500 mb-1">Tgl Masuk Mulai</label>
                    <input type="date" name="tanggal_masuk_start" id="tanggal_masuk_start" 
                           value="{{ request('tanggal_masuk_start') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                </div>

                <!-- Date Range End -->
                <div>
                    <label for="tanggal_masuk_end" class="block text-xs font-semibold text-gray-500 mb-1">Tgl Masuk Selesai</label>
                    <input type="date" name="tanggal_masuk_end" id="tanggal_masuk_end" 
                           value="{{ request('tanggal_masuk_end') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="flex justify-end gap-2 border-t border-gray-100 pt-3">
                <a href="{{ route('perbaikan-kontainer.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i> Reset
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-filter mr-2"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Perbaikan</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ukuran & Tipe</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Bengkel</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl Masuk</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl Selesai</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Estimasi Biaya</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Biaya Riil</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($perbaikanKontainers as $perbaikan)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                                {{ $perbaikan->no_perbaikan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                {{ $perbaikan->no_kontainer }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($perbaikan->ukuran)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $perbaikan->ukuran }}FT
                                    </span>
                                @endif
                                @if($perbaikan->tipe_kontainer)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-800 ml-1">
                                        {{ $perbaikan->tipe_kontainer }}
                                    </span>
                                @endif
                                @if(!$perbaikan->ukuran && !$perbaikan->tipe_kontainer)
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $perbaikan->bengkel->nama_bengkel ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $perbaikan->tanggal_masuk ? $perbaikan->tanggal_masuk->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $perbaikan->tanggal_keluar ? $perbaikan->tanggal_keluar->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Rp {{ number_format($perbaikan->estimasi_biaya, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                @if($perbaikan->status === 'selesai')
                                    Rp {{ number_format($perbaikan->biaya_riil, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $badgeColor = match($perbaikan->status) {
                                        'pending' => 'bg-gray-100 text-gray-800',
                                        'proses' => 'bg-yellow-100 text-yellow-800',
                                        'selesai' => 'bg-green-100 text-green-800',
                                        'batal' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                    $statusLabel = match($perbaikan->status) {
                                        'pending' => 'Pending',
                                        'proses' => 'Proses',
                                        'selesai' => 'Selesai',
                                        'batal' => 'Batal',
                                        default => ucfirst($perbaikan->status)
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeColor }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                                <a href="{{ route('perbaikan-kontainer.show', $perbaikan->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 inline-flex items-center p-1" 
                                   title="Detail">
                                    <i class="fas fa-eye text-base"></i>
                                </a>
                                @can('perbaikan-kontainer-update')
                                <a href="{{ route('perbaikan-kontainer.edit', $perbaikan->id) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 inline-flex items-center p-1" 
                                   title="Edit">
                                    <i class="fas fa-edit text-base"></i>
                                </a>
                                @endcan
                                @can('perbaikan-kontainer-delete')
                                <form action="{{ route('perbaikan-kontainer.destroy', $perbaikan->id) }}" 
                                      method="POST" 
                                      class="inline-block"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data perbaikan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 p-1" title="Hapus">
                                        <i class="fas fa-trash-alt text-base"></i>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-tools text-gray-300 text-4xl mb-3"></i>
                                    <span class="font-medium text-gray-500">Tidak ada data perbaikan kontainer ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($perbaikanKontainers->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $perbaikanKontainers->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
