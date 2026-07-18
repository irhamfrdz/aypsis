@extends('layouts.app')

@section('title', 'Daftar Pranota Perbaikan Kontainer')
@section('page_title', 'Pranota Perbaikan Kontainer')

@section('content')
<div class="container mx-auto px-4 py-4 overflow-y-auto h-full pb-24">
    <!-- Breadcrumbs / Header -->
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Pranota Perbaikan Kontainer</h1>
            <p class="text-xs text-gray-600">Kelola berkas pranota perbaikan kontainer.</p>
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
    @if(session('error'))
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2 text-red-500 text-lg"></i>
                <span class="font-medium text-sm">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Filters Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mb-6">
        <form action="{{ route('pranota-perbaikan-kontainer.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
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
                               placeholder="No. Pranota / Vendor">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
                    <select name="status" id="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Batal (Cancelled)</option>
                    </select>
                </div>

                <!-- Date Range Start -->
                <div>
                    <label for="tanggal_dari" class="block text-xs font-semibold text-gray-500 mb-1">Tgl Pranota Mulai</label>
                    <input type="date" name="tanggal_dari" id="tanggal_dari" 
                           value="{{ request('tanggal_dari') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                </div>

                <!-- Date Range End -->
                <div>
                    <label for="tanggal_sampai" class="block text-xs font-semibold text-gray-500 mb-1">Tgl Pranota Selesai</label>
                    <input type="date" name="tanggal_sampai" id="tanggal_sampai" 
                           value="{{ request('tanggal_sampai') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                </div>
                
                <!-- Per Page -->
                <div>
                    <label for="per_page" class="block text-xs font-semibold text-gray-500 mb-1">Baris per Halaman</label>
                    <select name="per_page" id="per_page" onchange="this.form.submit()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="flex justify-end gap-2 border-t border-gray-100 pt-3">
                <a href="{{ route('pranota-perbaikan-kontainer.index') }}" 
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
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor Pranota</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Vendor/Bengkel</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Biaya Adjustment</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Biaya</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($pranotaPerbaikanKontainers as $pranota)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $loop->iteration + ($pranotaPerbaikanKontainers->currentPage() - 1) * $pranotaPerbaikanKontainers->perPage() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                                {{ $pranota->nomor_pranota }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $pranota->tanggal_pranota ? $pranota->tanggal_pranota->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $pranota->vendor ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Rp {{ number_format($pranota->adjustment, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                Rp {{ number_format($pranota->total_biaya + $pranota->adjustment, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $badgeColor = match($pranota->status) {
                                        'draft' => 'bg-gray-100 text-gray-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'paid' => 'bg-blue-100 text-blue-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                    $statusLabel = match($pranota->status) {
                                        'draft' => 'Draft',
                                        'approved' => 'Disetujui',
                                        'paid' => 'Lunas',
                                        'cancelled' => 'Batal',
                                        default => ucfirst($pranota->status)
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeColor }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                                <a href="{{ route('pranota-perbaikan-kontainer.show', $pranota->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 inline-flex items-center p-1" 
                                   title="Detail">
                                    <i class="fas fa-eye text-base"></i>
                                </a>
                                @can('pranota-perbaikan-kontainer-print')
                                 <a href="{{ route('pranota-perbaikan-kontainer.print', $pranota->id) }}" 
                                    target="_blank"
                                    class="text-green-600 hover:text-green-900 inline-flex items-center p-1" 
                                    title="Cetak Lengkap">
                                     <i class="fas fa-print text-base"></i>
                                 </a>
                                 <a href="{{ route('pranota-perbaikan-kontainer.print', [$pranota->id, 'type' => 'cat']) }}" 
                                    target="_blank"
                                    class="text-teal-600 hover:text-teal-900 inline-flex items-center p-1" 
                                    title="Cetak Khusus Cat Saja">
                                     <i class="fas fa-paint-roller text-base"></i>
                                 </a>
                                 <a href="{{ route('pranota-perbaikan-kontainer.print', [$pranota->id, 'type' => 'perbaikan']) }}" 
                                    target="_blank"
                                    class="text-indigo-600 hover:text-indigo-900 inline-flex items-center p-1" 
                                    title="Cetak Khusus Perbaikan Kontainer Saja">
                                     <i class="fas fa-tools text-base"></i>
                                 </a>
                                 @endcan
                                @can('pranota-perbaikan-kontainer-delete')
                                <form action="{{ route('pranota-perbaikan-kontainer.destroy', $pranota->id) }}" 
                                      method="POST" 
                                      class="inline-block"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pranota ini?')">
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
                            <td colspan="9" class="px-6 py-10 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-file-invoice text-gray-300 text-4xl mb-3"></i>
                                    <span class="font-medium text-gray-500">Tidak ada data pranota perbaikan kontainer ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pranotaPerbaikanKontainers->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pranotaPerbaikanKontainers->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
