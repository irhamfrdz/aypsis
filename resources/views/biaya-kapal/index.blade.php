@extends('layouts.app')

@section('title', 'Biaya Kapal')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Biaya Kapal</h1>
                <p class="text-gray-600 mt-1">Kelola data biaya operasional kapal</p>
            </div>
            <div>
                @can('biaya-kapal-create')
                <a href="{{ route('biaya-kapal.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg shadow-sm transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Biaya Kapal
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Daftar Biaya Kapal</h2>
        </div>

        <div class="p-6">
            <!-- Filter & Search -->
            <form method="GET" action="{{ route('biaya-kapal.index') }}" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-4">
                        <input type="text"
                               name="search"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Cari nama kapal, jenis biaya..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="md:col-span-3">
                        <select name="jenis_biaya" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua Jenis Biaya</option>
                            <option value="bahan_bakar" {{ request('jenis_biaya') == 'bahan_bakar' ? 'selected' : '' }}>Bahan Bakar</option>
                            <option value="pelabuhan" {{ request('jenis_biaya') == 'pelabuhan' ? 'selected' : '' }}>Pelabuhan</option>
                            <option value="perbaikan" {{ request('jenis_biaya') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                            <option value="awak_kapal" {{ request('jenis_biaya') == 'awak_kapal' ? 'selected' : '' }}>Awak Kapal</option>
                            <option value="asuransi" {{ request('jenis_biaya') == 'asuransi' ? 'selected' : '' }}>Asuransi</option>
                            <option value="lainnya" {{ request('jenis_biaya') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-search mr-2"></i> Cari
                        </button>
                    </div>
                    <div class="md:col-span-3">
                        <a href="{{ route('biaya-kapal.index') }}" class="block text-center w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-redo mr-2"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Invoice</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kapal</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Voyage</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Biaya</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($biayaKapals as $biaya)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 text-center">
                                {{ ($biayaKapals->currentPage() - 1) * $biayaKapals->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                                {{ $biaya->tanggal ? \Carbon\Carbon::parse($biaya->tanggal)->format('d/M/Y') : '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs font-medium text-gray-900">
                                {{ $biaya->nomor_invoice }}
                            </td>
                            <td class="px-3 py-2">
                                @php
                                    // Untuk biaya buruh (KB024), ambil kapal dari barangDetails
                                    if ($biaya->jenis_biaya === 'KB024' && $biaya->barangDetails && $biaya->barangDetails->count() > 0) {
                                        $namaKapals = $biaya->barangDetails->pluck('kapal')->unique()->filter()->values()->toArray();
                                    } 
                                    // Untuk biaya air, ambil kapal dari airDetails
                                    elseif ($biaya->airDetails && $biaya->airDetails->count() > 0) {
                                        $namaKapals = $biaya->airDetails->pluck('kapal')->unique()->filter()->values()->toArray();
                                    }
                                    else {
                                        $namaKapals = is_array($biaya->nama_kapal) ? $biaya->nama_kapal : ($biaya->nama_kapal ? [$biaya->nama_kapal] : []);
                                    }
                                @endphp
                                @if(count($namaKapals) > 0)
                                    <span class="text-xs font-semibold text-gray-900">{{ $namaKapals[0] }}</span>
                                    @if(count($namaKapals) > 1)
                                        <span class="text-xs text-blue-600">+{{ count($namaKapals) - 1 }}</span>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @php
                                    // Untuk biaya buruh (KB024), ambil voyage dari barangDetails
                                    if ($biaya->jenis_biaya === 'KB024' && $biaya->barangDetails && $biaya->barangDetails->count() > 0) {
                                        $noVoyages = $biaya->barangDetails->pluck('voyage')->unique()->filter()->values()->toArray();
                                    } 
                                    // Untuk biaya air, ambil voyage dari airDetails
                                    elseif ($biaya->airDetails && $biaya->airDetails->count() > 0) {
                                        $noVoyages = $biaya->airDetails->pluck('voyage')->unique()->filter()->values()->toArray();
                                    }
                                    else {
                                        $noVoyages = is_array($biaya->no_voyage) ? $biaya->no_voyage : ($biaya->no_voyage ? [$biaya->no_voyage] : []);
                                    }
                                @endphp
                                @if(count($noVoyages) > 0)
                                    <span class="text-xs text-gray-900">{{ $noVoyages[0] }}</span>
                                    @if(count($noVoyages) > 1)
                                        <span class="text-xs text-blue-600">+{{ count($noVoyages) - 1 }}</span>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $biaya->jenis_biaya_label }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                <div class="max-w-xs truncate" title="{{ $biaya->keterangan }}">
                                    {{ Str::limit($biaya->keterangan ?: '-', 50) }}
                                </div>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 text-right font-medium">
                                Rp {{ number_format($biaya->nominal ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center text-xs font-medium">
                                <div class="flex items-center justify-center gap-1">
                                    @can('biaya-kapal-view')
                                    <a href="{{ route('biaya-kapal.show', $biaya->id) }}"
                                       class="inline-flex items-center px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded transition duration-150"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('biaya-kapal-view')
                                    <a href="{{ route('biaya-kapal.print', $biaya->id) }}"
                                       class="inline-flex items-center px-2 py-1 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded transition duration-150"
                                       title="Print"
                                       target="_blank">
                                        <i class="fas fa-print text-xs"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('biaya-kapal-update')
                                    <a href="{{ route('biaya-kapal.edit', $biaya->id) }}"
                                       class="inline-flex items-center px-2 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded transition duration-150"
                                       title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('biaya-kapal-delete')
                                    <form action="{{ route('biaya-kapal.destroy', $biaya->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded transition duration-150"
                                                title="Hapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-3 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-ship text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-gray-500 text-base font-medium">Tidak ada data biaya kapal</p>
                                    <p class="text-gray-400 text-xs mt-1">Silakan tambahkan data biaya kapal terlebih dahulu</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($biayaKapals->hasPages())
            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-4">
                <div class="flex flex-1 justify-between sm:hidden">
                    @if($biayaKapals->onFirstPage())
                        <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-400">
                            Previous
                        </span>
                    @else
                        <a href="{{ $biayaKapals->previousPageUrl() }}" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Previous
                        </a>
                    @endif

                    @if($biayaKapals->hasMorePages())
                        <a href="{{ $biayaKapals->nextPageUrl() }}" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Next
                        </a>
                    @else
                        <span class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-400">
                            Next
                        </span>
                    @endif
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Menampilkan
                            <span class="font-medium">{{ $biayaKapals->firstItem() ?? 0 }}</span>
                            sampai
                            <span class="font-medium">{{ $biayaKapals->lastItem() ?? 0 }}</span>
                            dari
                            <span class="font-medium">{{ $biayaKapals->total() }}</span>
                            data
                        </p>
                    </div>
                    <div>
                        {{ $biayaKapals->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endpush
@endsection
