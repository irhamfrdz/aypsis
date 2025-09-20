@extends('layouts.app')

@section('title', 'Daftar Tagihan CAT')

@section('content')
<style>
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
}

.table-responsive table {
    width: 100%;
    min-width: 1200px;
    table-layout: fixed;
    border-collapse: collapse;
}

.table-responsive th,
.table-responsive td {
    padding: 0.75rem;
    vertical-align: middle;
    word-wrap: break-word;
    border: 1px solid #e5e7eb;
}

.table-responsive th {
    background-color: #f9fafb;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    color: #6b7280;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }

    .table-responsive th,
    .table-responsive td {
        padding: 0.5rem;
        white-space: nowrap;
    }
}
</style>
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Daftar Tagihan CAT</h1>
                <p class="text-gray-600 mt-1">Kelola data tagihan Container Annual Test</p>
            </div>
            @can('tagihan-cat-create')
            <a href="{{ route('tagihan-cat.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Tagihan CAT
            </a>
            @endcan
        </div>

        <!-- Filters -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('tagihan-cat.index') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal CAT</label>
                    <input type="date" name="tanggal_cat" value="{{ request('tanggal_cat') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-[10px]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nomor tagihan CAT, nomor kontainer, vendor..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-[10px]">
                </div>
                <div class="flex items-end space-x-2 md:col-span-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 text-xs">
                        Filter
                    </button>
                    <a href="{{ route('tagihan-cat.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200 text-xs">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Container with Horizontal Scroll -->
        <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg table-responsive" style="width: 100%;">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg" style="width: 100%; table-layout: fixed;">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 5%;">No</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 12%;">Nomor Tagihan CAT</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 12%;">Nomor Kontainer</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 12%;">Vendo/Bengkel</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 10%;">Tanggal CAT</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 12%;">Estimasi Biaya</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 12%;">Realisasi Biaya</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 10%;">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-[10px]">
                    @forelse($tagihanCats as $index => $tagihanCat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" style="width: 5%;">
                            {{ $loop->iteration + ($tagihanCats->currentPage() - 1) * $tagihanCats->perPage() }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap" style="width: 12%;">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $tagihanCat->nomor_tagihan_cat ?? $tagihanCat->id }}
                            </div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap" style="width: 12%;">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $tagihanCat->nomor_kontainer }}
                            </div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap" style="width: 12%;">
                            <div class="text-sm text-gray-900">
                                {{ $tagihanCat->vendor ?? '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" style="width: 10%;">
                            {{ $tagihanCat->tanggal_cat ? \Carbon\Carbon::parse($tagihanCat->tanggal_cat)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" style="width: 12%;">
                            {{ $tagihanCat->estimasi_biaya ? 'Rp ' . number_format($tagihanCat->estimasi_biaya, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" style="width: 12%;">
                            {{ $tagihanCat->realisasi_biaya ? 'Rp ' . number_format($tagihanCat->realisasi_biaya, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap" style="width: 10%;">
                            @if($tagihanCat->status == 'pending')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @elseif($tagihanCat->status == 'paid')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Sudah Dibayar
                                </span>
                            @elseif($tagihanCat->status == 'cancelled')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Dibatalkan
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium" style="width: 15%;">
                            <div class="flex space-x-2">
                                @can('tagihan-cat-view')
                                <a href="{{ route('tagihan-cat.show', $tagihanCat) }}"
                                   class="text-blue-600 hover:text-blue-900">Lihat</a>
                                @endcan
                                @can('tagihan-cat-update')
                                <a href="{{ route('tagihan-cat.edit', $tagihanCat) }}"
                                   class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @endcan
                                @can('tagihan-cat-delete')
                                <form method="POST" action="{{ route('tagihan-cat.destroy', $tagihanCat) }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus tagihan CAT ini?')"
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
                        <td colspan="9" class="px-4 py-4 text-center text-gray-500">
                            Tidak ada data tagihan CAT ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tagihanCats->hasPages())
        <div class="mt-6">
            {{ $tagihanCats->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
