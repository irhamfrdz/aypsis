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

    .status-badge {
        font-size: 0.75rem !important;
        padding: 0.125rem 0.5rem !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 100px !important;
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

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <ul class="text-sm font-medium text-red-800">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

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

        <!-- Action Buttons -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Data Tagihan CAT</h2>
                <p class="text-sm text-gray-600">Pilih item untuk melakukan aksi bulk</p>
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

        <!-- Bulk Actions -->
        <div id="bulkActions" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-blue-800">
                        <span id="selectedCount">0</span> item dipilih
                    </span>
                    <div id="vendorInfo" class="hidden text-xs text-blue-600">
                        Vendor: <span id="vendorList"></span>
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" id="btnBulkDelete"
                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-200">
                            Hapus Terpilih
                        </button>
                        <button type="button" id="btnBulkStatus"
                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-200">
                            Update Status
                        </button>
                        <button type="button" id="btnBulkPranota"
                                class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-200">
                            Masukan Pranota
                        </button>
                    </div>
                </div>
                <button type="button" id="btnCancelSelection"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Batal
                </button>
            </div>
        </div>
        <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg table-responsive" style="width: 100%;">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg" style="width: 100%; table-layout: fixed;">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 5%;">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 5%;">No</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%;">Nomor Tagihan CAT</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 12%;">Nomor Kontainer</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%;">Vendo/Bengkel</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 10%;">Tanggal CAT</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 10%;">Tanggal Pranota</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 10%;">Estimasi Biaya</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 10%;">Realisasi Biaya</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 10%;">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-[10px]">
                    @forelse($tagihanCats as $index => $tagihanCat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 whitespace-nowrap" style="width: 5%;">
                            @php
                                $hasPranotaForCheckbox = false;
                                if (!empty($tagihanCat->pranota) && $tagihanCat->pranota->isNotEmpty()) {
                                    $hasPranotaForCheckbox = true;
                                } elseif (\App\Models\Pranota::whereJsonContains('tagihan_ids', $tagihanCat->id)->exists()) {
                                    $hasPranotaForCheckbox = true;
                                }
                            @endphp
                            <input type="checkbox" name="selected_items[]" value="{{ $tagihanCat->id }}"
                                   class="item-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                   {{ $hasPranotaForCheckbox ? 'disabled' : '' }}>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" style="width: 5%;">
                            {{ $loop->iteration + ($tagihanCats->currentPage() - 1) * $tagihanCats->perPage() }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap" style="width: 15%;">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $tagihanCat->nomor_tagihan_cat ?? $tagihanCat->id }}
                            </div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap" style="width: 12%;">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $tagihanCat->nomor_kontainer }}
                            </div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap" style="width: 15%;">
                            <div class="text-sm text-gray-900">
                                {{ $tagihanCat->vendor ?? '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" style="width: 10%;">
                            {{ $tagihanCat->tanggal_cat ? \Carbon\Carbon::parse($tagihanCat->tanggal_cat)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" style="width: 10%;">
                            @php
                                $tanggalPranota = null;
                                if (!empty($tagihanCat->pranota) && $tagihanCat->pranota->isNotEmpty()) {
                                    $tanggalPranota = $tagihanCat->pranota->first()->tanggal_pranota;
                                } elseif (\App\Models\Pranota::whereJsonContains('tagihan_ids', $tagihanCat->id)->exists()) {
                                    $pranota = \App\Models\Pranota::whereJsonContains('tagihan_ids', $tagihanCat->id)->first();
                                    $tanggalPranota = $pranota->tanggal_pranota;
                                }
                            @endphp
                            {{ $tanggalPranota ? \Carbon\Carbon::parse($tanggalPranota)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" style="width: 10%;">
                            {{ $tagihanCat->estimasi_biaya ? 'Rp ' . number_format($tagihanCat->estimasi_biaya, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" style="width: 10%;">
                            {{ $tagihanCat->realisasi_biaya ? 'Rp ' . number_format($tagihanCat->realisasi_biaya, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap" style="width: 10%;">
                            @if($tagihanCat->status == 'pending')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 status-badge">
                                    Pending
                                </span>
                            @elseif($tagihanCat->status == 'masuk pranota')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 status-badge">
                                    Pranota
                                </span>
                            @elseif($tagihanCat->status == 'paid')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 status-badge">
                                    Dibayar
                                </span>
                            @elseif($tagihanCat->status == 'cancelled')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 status-badge">
                                    Batal
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium" style="width: 10%;">
                            <div class="flex items-center justify-center space-x-2">
                                @can('tagihan-cat-view')
                                <a href="{{ route('tagihan-cat.show', $tagihanCat) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-md transition-colors duration-200"
                                   title="Lihat detail">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @endcan
                                @can('tagihan-cat-update')
                                <a href="{{ route('tagihan-cat.edit', $tagihanCat) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-md transition-colors duration-200"
                                   title="Edit data">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @endcan
                                @can('pranota-create')
                                @php
                                    $hasPranota = false;
                                    if (!empty($tagihanCat->pranota) && $tagihanCat->pranota->isNotEmpty()) {
                                        $hasPranota = true;
                                    } elseif (\App\Models\Pranota::whereJsonContains('tagihan_ids', $tagihanCat->id)->exists()) {
                                        $hasPranota = true;
                                    }
                                @endphp
                                @if(!$hasPranota)
                                <button type="button"
                                        onclick="showPranotaModal([{{ $tagihanCat->id }}], false)"
                                        class="inline-flex items-center justify-center w-8 h-8 text-green-600 hover:text-green-900 hover:bg-green-50 rounded-md transition-colors duration-200"
                                        title="Masukan Pranota">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </button>
                                @else
                                <div class="inline-flex items-center justify-center w-8 h-8 text-gray-400 cursor-not-allowed"
                                     title="Sudah masuk pranota">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                @endif
                                @endcan
                                @can('tagihan-cat-delete')
                                <form method="POST" action="{{ route('tagihan-cat.destroy', $tagihanCat) }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus tagihan CAT ini?')"
                                      class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-md transition-colors duration-200"
                                            title="Hapus data">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-4 py-4 text-center text-gray-500">
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

<!-- Modal Pranota -->
<div id="pranotaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[60]">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Masukan Pranota CAT</h3>
                <button type="button" id="closePranotaModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-4">
                <form id="pranotaForm" method="POST" action="{{ route('pranota.bulk-create-from-tagihan-cat') }}">
                    @csrf
                    <input type="hidden" id="tagihan_cat_ids" name="tagihan_cat_ids">

                    <!-- Selected Items Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">Item yang Dipilih:</h4>
                        <div id="selectedItemsList" class="text-sm text-blue-700 space-y-1">
                            <!-- Selected items will be populated here -->
                        </div>
                    </div>

                    <!-- Pranota Form Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="nomor_pranota" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Pranota <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nomor_pranota" name="nomor_pranota" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Contoh: PTC12509000001">
                            <div id="formatPreview" class="text-xs text-gray-500 mt-1"></div>
                        </div>
                        <div>
                            <label for="tanggal_pranota" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Pranota <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="tanggal_pranota" name="tanggal_pranota" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="supplier" class="block text-sm font-medium text-gray-700 mb-1">
                                Supplier/Vendor <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="supplier" name="supplier" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Nama supplier/vendor">
                        </div>
                        <div>
                            <label for="realisasi_biaya_total" class="block text-sm font-medium text-gray-700 mb-1">
                                Realisasi Biaya Total <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="realisasi_biaya_total" name="realisasi_biaya_total_display" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Rp 0">
                            <input type="hidden" id="realisasi_biaya_total_numeric" name="realisasi_biaya_total">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">
                            Keterangan
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                  placeholder="Tambahkan keterangan jika diperlukan"></textarea>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end pt-4 border-t">
                <button type="button" id="cancelPranotaBtn"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium transition duration-200 mr-2">
                    Batal
                </button>
                <button type="button" id="submitPranotaBtn"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                    Simpan Pranota
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
console.log('Script tag executed - JavaScript is loading');

// Global error handler to catch JavaScript errors
window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.error);
    console.error('Error message:', e.message);
    console.error('Error file:', e.filename);
    console.error('Error line:', e.lineno);
});

window.addEventListener('unhandledrejection', function(e) {
    console.error('Unhandled Promise Rejection:', e.reason);
});

// Global functions for rupiah input handling
function handleRupiahInput(input) {
    input.addEventListener('input', function(e) {
        let value = this.value;
        let number = rupiahToNumber(value);
        if (number > 0) {
            this.value = formatRupiah(number);
        } else {
            this.value = '';
        }
    });

    input.addEventListener('focus', function(e) {
        if (this.value === 'Rp 0' || this.value === '') {
            this.value = '';
        }
    });

    input.addEventListener('blur', function(e) {
        if (this.value === '' || rupiahToNumber(this.value) === 0) {
            this.value = 'Rp 0';
        }
    });
}

// Global function to format rupiah
function formatRupiah(angka, prefix = 'Rp ') {
    if (!angka) return '';
    let number_string = angka.toString().replace(/[^,\d]/g, ''),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix + rupiah;
}

// Global function to convert rupiah to number
function rupiahToNumber(rupiah) {
    return parseFloat(rupiah.replace(/[^\d]/g, '')) || 0;
}

// Global function to generate pranota CAT number
function generatePranotaCatNumber() {
    const now = new Date();
    const year = now.getFullYear().toString().slice(-2); // 2 digit tahun (25)
    const month = (now.getMonth() + 1).toString().padStart(2, '0'); // 2 digit bulan (09)
    const kode = 'PTC'; // 3 digit kode
    const cetakan = '1'; // 1 digit nomor cetakan

    // Get running number from localStorage or start from 1
    let runningNumber = parseInt(localStorage.getItem('pranota_cat_running_number') || '0') + 1;

    // Reset counter if it's a new month
    const lastGenerated = localStorage.getItem('pranota_cat_last_generated');
    const currentMonth = `${year}${month}`;

    if (lastGenerated !== currentMonth) {
        runningNumber = 1;
        localStorage.setItem('pranota_cat_last_generated', currentMonth);
    }

    // Save new running number
    localStorage.setItem('pranota_cat_running_number', runningNumber.toString());

    // Format running number to 6 digits
    const formattedRunningNumber = runningNumber.toString().padStart(6, '0');

    const nomorPranota = `${kode}${cetakan}${year}${month}${formattedRunningNumber}`;
    return nomorPranota;
}

// Preview format function
function previewPranotaCatFormat() {
    const now = new Date();
    const year = now.getFullYear().toString().slice(-2);
    const month = (now.getMonth() + 1).toString().padStart(2, '0');
    const kode = 'PTC';
    const cetakan = '1';
    const runningNumber = '000001';

    return `${kode}${cetakan}${year}${month}${runningNumber}`;
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded fired - JavaScript is loading');

    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    const btnBulkDelete = document.getElementById('btnBulkDelete');
    const btnBulkStatus = document.getElementById('btnBulkStatus');
    const btnCancelSelection = document.getElementById('btnCancelSelection');
    const btnBulkPranota = document.getElementById('btnBulkPranota');

    // Vendor info elements
    const vendorInfo = document.getElementById('vendorInfo');
    const vendorList = document.getElementById('vendorList');

    // Modal elements
    const pranotaModal = document.getElementById('pranotaModal');
    const closePranotaModal = document.getElementById('closePranotaModal');
    const cancelPranotaBtn = document.getElementById('cancelPranotaBtn');
    const submitPranotaBtn = document.getElementById('submitPranotaBtn');
    const pranotaForm = document.getElementById('pranotaForm');
    const selectedItemsList = document.getElementById('selectedItemsList');
    const tagihanCatIdsInput = document.getElementById('tagihan_cat_ids');

    console.log('Modal elements found:', {
        pranotaModal: !!pranotaModal,
        submitPranotaBtn: !!submitPranotaBtn,
        pranotaForm: !!pranotaForm
    });

    // Global variables for pranota
    let selectedPranotaIds = [];
    let isBulkPranota = false;

    console.log('JavaScript loaded successfully');
    console.log('Elements found:', {
        selectAllCheckbox: !!selectAllCheckbox,
        itemCheckboxes: itemCheckboxes.length,
        bulkActions: !!bulkActions,
        selectedCount: !!selectedCount,
        btnBulkStatus: !!btnBulkStatus,
        btnBulkPranota: !!btnBulkPranota,
        pranotaModal: !!pranotaModal
    });

    // Function to show pranota modal
    window.showPranotaModal = function(ids, isBulk = false) {
        // For individual pranota, redirect to create page
        if (!isBulk) {
            window.location.href = '{{ route("pranota.create") }}?tagihan_cat_id=' + ids[0];
            return;
        }

        // For bulk pranota, show modal
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Pilih minimal satu item untuk dimasukkan ke pranota');
            return;
        }

        // Get selected items data and validate vendors
        const selectedItems = [];
        const ids_array = [];
        const vendors = new Set();
        const itemsWithPranota = [];

        checkedBoxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const nomorTagihan = row.querySelector('td:nth-child(3)').textContent.trim();
            const nomorKontainer = row.querySelector('td:nth-child(4) div:first-child').textContent.trim();
            const vendorName = row.querySelector('td:nth-child(5) div:first-child').textContent.trim();
            const tanggalPranota = row.querySelector('td:nth-child(7)').textContent.trim();
            const id = checkbox.value;

            // Check if item already has pranota
            if (tanggalPranota && tanggalPranota !== '-') {
                itemsWithPranota.push(nomorTagihan);
                return; // Skip this item
            }

            selectedItems.push({
                nomorTagihan: nomorTagihan,
                nomorKontainer: nomorKontainer,
                vendor: vendorName
            });
            ids_array.push(id);
            if (vendorName && vendorName !== '-') {
                vendors.add(vendorName);
            }
        });

        // Check if any selected items already have pranota
        if (itemsWithPranota.length > 0) {
            alert(`Item berikut sudah memiliki pranota dan tidak dapat diproses:\n${itemsWithPranota.join('\n')}\n\nSilakan hapus centang pada item tersebut.`);
            return;
        }

        // Check if we still have valid items after filtering
        if (selectedItems.length === 0) {
            alert('Tidak ada item yang valid untuk dimasukkan ke pranota. Semua item yang dipilih sudah memiliki pranota.');
            return;
        }

        // Validate vendor consistency
        if (vendors.size > 1) {
            const vendorList = Array.from(vendors).join(', ');
            alert(`Tidak dapat memproses pranota bulk. Item yang dipilih memiliki vendor yang berbeda: ${vendorList}. Silakan pilih item dengan vendor yang sama.`);
            return;
        }

        // Get the vendor name (should be only one since we validated)
        const vendorName = vendors.size > 0 ? Array.from(vendors)[0] : '';

        // Populate modal with selected items
        selectedItemsList.innerHTML = selectedItems.map(item =>
            `<div>• Tagihan: ${item.nomorTagihan} - Kontainer: ${item.nomorKontainer} - Vendor: ${item.vendor}</div>`
        ).join('');

        // Set hidden input with selected IDs
        // Clear any existing array inputs first
        const existingArrayInputs = pranotaForm.querySelectorAll('input[name="tagihan_cat_ids[]"]');
        existingArrayInputs.forEach(input => input.remove());

        // Create array inputs for tagihan_cat_ids
        ids_array.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tagihan_cat_ids[]';
            input.value = id;
            pranotaForm.appendChild(input);
        });

        // Keep the JSON version for debugging
        tagihanCatIdsInput.value = JSON.stringify(ids_array);

        // Auto-populate vendor field
        document.getElementById('supplier').value = vendorName;

        // Calculate total realisasi biaya
        let totalRealisasi = 0;
        checkedBoxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const biayaText = row.querySelector('td:nth-child(9)').textContent.trim();
            const biaya = biayaText !== '-' ? parseFloat(biayaText.replace(/[^\d]/g, '')) : 0;
            totalRealisasi += biaya;
        });

        // Set realisasi biaya total
        document.getElementById('realisasi_biaya_total').value = formatRupiah(totalRealisasi);

        // Initialize rupiah formatting for realisasi input
        const realisasiInput = document.getElementById('realisasi_biaya_total');
        handleRupiahInput(realisasiInput);

        // Auto-generate nomor pranota
        const nomorPranota = generatePranotaCatNumber();
        document.getElementById('nomor_pranota').value = nomorPranota;

        // Update format preview
        const previewFormat = previewPranotaCatFormat();
        document.getElementById('formatPreview').textContent = `Contoh: ${previewFormat}`;

        // Set today's date
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('tanggal_pranota').value = today;

        // Show modal
        pranotaModal.classList.remove('hidden');
    };

    // Modal event listeners
    // Close modal
    closePranotaModal.addEventListener('click', function() {
        pranotaModal.classList.add('hidden');
    });

    cancelPranotaBtn.addEventListener('click', function() {
        pranotaModal.classList.add('hidden');
    });

    // Close modal when clicking outside
    pranotaModal.addEventListener('click', function(e) {
        if (e.target === pranotaModal) {
            pranotaModal.classList.add('hidden');
        }
    });

    // Submit pranota form
    if (submitPranotaBtn) {
        console.log('submitPranotaBtn found, attaching event listener');
        submitPranotaBtn.addEventListener('click', function() {
            console.log('Submit button clicked');

            const nomorPranota = document.getElementById('nomor_pranota').value.trim();
            const tanggalPranota = document.getElementById('tanggal_pranota').value;
            const supplier = document.getElementById('supplier').value.trim();
            const realisasiDisplay = document.getElementById('realisasi_biaya_total').value;

            console.log('Form values:', { nomorPranota, tanggalPranota, supplier, realisasiDisplay });

            if (!nomorPranota) {
                alert('Nomor pranota harus diisi');
                return;
            }

            if (!tanggalPranota) {
                alert('Tanggal pranota harus diisi');
                return;
            }

            if (!supplier) {
                alert('Supplier harus diisi');
                return;
            }

            // Convert rupiah format to number for form submission
            const realisasiNumeric = rupiahToNumber(realisasiDisplay);
            console.log('Converted value:', realisasiNumeric);

            if (realisasiNumeric <= 0) {
                alert('Realisasi biaya total harus lebih dari 0');
                return;
            }

            // Set the hidden field
            document.getElementById('realisasi_biaya_total_numeric').value = realisasiNumeric;

            console.log('Submitting form...');
            // Submit form
            pranotaForm.submit();
        });
    } else {
        console.error('submitPranotaBtn not found!');
    }

    // Add form submit event listener for debugging
    if (pranotaForm) {
        pranotaForm.addEventListener('submit', function(e) {
            console.log('Form submit event fired');
            console.log('Form action:', this.action);
            console.log('Form method:', this.method);

            const formData = new FormData(this);
            console.log('Form data:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }

            // Check if we have the required fields
            const requiredFields = ['nomor_pranota', 'tanggal_pranota', 'supplier', 'realisasi_biaya_total'];
            const missingFields = requiredFields.filter(field => !formData.has(field) || !formData.get(field));

            if (missingFields.length > 0) {
                console.error('Missing required fields:', missingFields);
                e.preventDefault();
                alert('Field yang diperlukan belum diisi: ' + missingFields.join(', '));
                return false;
            }

            // Check if we have tagihan_cat_ids
            const tagihanIds = formData.getAll('tagihan_cat_ids[]');
            if (tagihanIds.length === 0) {
                console.error('No tagihan_cat_ids found');
                e.preventDefault();
                alert('Tidak ada tagihan CAT yang dipilih');
                return false;
            }

            console.log('tagihan_cat_ids:', tagihanIds);
        });
    }

    // Close modal when clicking outside
    pranotaModal.addEventListener('click', function(e) {
        if (e.target === pranotaModal) {
            pranotaModal.classList.add('hidden');
        }
    });

    // Initialize bulk actions on page load
    updateBulkActions();

    // Handle select all checkbox
    selectAllCheckbox.addEventListener('change', function() {
        console.log('Select all checkbox changed:', this.checked);
        const isChecked = this.checked;
        // Only check/uncheck enabled checkboxes
        document.querySelectorAll('.item-checkbox:not([disabled])').forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        updateBulkActions();
    });

    // Handle individual checkboxes
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            console.log('Individual checkbox changed:', this.checked, 'ID:', this.value);
            updateSelectAllState();
            updateBulkActions();
        });
    });

    // Update select all checkbox state
    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const enabledBoxes = document.querySelectorAll('.item-checkbox:not([disabled])');
        const totalEnabledBoxes = enabledBoxes.length;

        if (checkedBoxes.length === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedBoxes.length === totalEnabledBoxes) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Update bulk actions visibility and count
    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const count = checkedBoxes.length;

        console.log('updateBulkActions called, checked boxes:', count);
        console.log('bulkActions element:', bulkActions);
        console.log('selectedCount element:', selectedCount);

        if (selectedCount) {
            selectedCount.textContent = count;
        }

        if (bulkActions) {
            if (count > 0) {
                // Check vendor consistency for bulk pranota
                const vendors = new Set();
                let hasDifferentVendors = false;
                let hasItemsWithPranota = false;

                checkedBoxes.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    const vendorName = row.querySelector('td:nth-child(5) div:first-child').textContent.trim();
                    const tanggalPranota = row.querySelector('td:nth-child(7)').textContent.trim();

                    if (vendorName && vendorName !== '-') {
                        vendors.add(vendorName);
                    }

                    // Check if item already has pranota
                    if (tanggalPranota && tanggalPranota !== '-') {
                        hasItemsWithPranota = true;
                    }
                });

                hasDifferentVendors = vendors.size > 1;

                // Show vendor info if multiple vendors selected
                if (vendorInfo && vendorList) {
                    if (vendors.size > 0) {
                        vendorList.textContent = Array.from(vendors).join(', ');
                        vendorInfo.classList.remove('hidden');
                    } else {
                        vendorInfo.classList.add('hidden');
                    }
                }

                // Show bulk actions
                console.log('Showing bulk actions - removing hidden class');
                bulkActions.classList.remove('hidden');
                bulkActions.style.display = 'block'; // Force show

                // Update bulk pranota button state
                if (btnBulkPranota) {
                    if (hasDifferentVendors) {
                        btnBulkPranota.disabled = true;
                        btnBulkPranota.classList.add('opacity-50', 'cursor-not-allowed');
                        btnBulkPranota.title = 'Tidak dapat memproses pranota bulk karena vendor berbeda';
                    } else if (hasItemsWithPranota) {
                        btnBulkPranota.disabled = true;
                        btnBulkPranota.classList.add('opacity-50', 'cursor-not-allowed');
                        btnBulkPranota.title = 'Tidak dapat memproses pranota bulk karena ada item yang sudah memiliki pranota';
                    } else {
                        btnBulkPranota.disabled = false;
                        btnBulkPranota.classList.remove('opacity-50', 'cursor-not-allowed');
                        btnBulkPranota.title = 'Masukan Pranota';
                    }
                }
            } else {
                console.log('Hiding bulk actions - adding hidden class');
                bulkActions.classList.add('hidden');
                bulkActions.style.display = 'none'; // Force hide

                // Reset bulk pranota button
                if (btnBulkPranota) {
                    btnBulkPranota.disabled = false;
                    btnBulkPranota.classList.remove('opacity-50', 'cursor-not-allowed');
                    btnBulkPranota.title = 'Masukan Pranota';
                }

                // Hide vendor info
                if (vendorInfo) {
                    vendorInfo.classList.add('hidden');
                }
            }
        } else {
            console.error('bulkActions element not found!');
        }
    }

    // Cancel selection
    btnCancelSelection.addEventListener('click', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
        updateBulkActions();
    });

    // Bulk delete handler
    btnBulkDelete.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Pilih minimal satu item untuk dihapus');
            return;
        }

        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        const message = `Apakah Anda yakin ingin menghapus ${checkedBoxes.length} item yang dipilih?`;

        if (confirm(message)) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("tagihan-cat.bulk-delete") }}';

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Add method
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);

            // Add selected IDs
            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }
    });

    // Bulk status update handler
    btnBulkStatus.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Pilih minimal satu item untuk update status');
            return;
        }

        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        const newStatus = prompt('Masukkan status baru:\n1. pending\n2. masuk pranota\n3. paid\n4. cancelled');

        let statusValue = '';
        if (newStatus === '1' || newStatus === 'pending') {
            statusValue = 'pending';
        } else if (newStatus === '2' || newStatus === 'masuk pranota') {
            statusValue = 'masuk pranota';
        } else if (newStatus === '3' || newStatus === 'paid') {
            statusValue = 'paid';
        } else if (newStatus === '4' || newStatus === 'cancelled') {
            statusValue = 'cancelled';
        }

        if (statusValue) {
            const message = `Apakah Anda yakin ingin mengubah status ${checkedBoxes.length} item menjadi "${statusValue}"?`;
            if (confirm(message)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("tagihan-cat.bulk-update-status") }}';

                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                // Add status
                const statusField = document.createElement('input');
                statusField.type = 'hidden';
                statusField.name = 'status';
                statusField.value = statusValue;
                form.appendChild(statusField);

                // Add selected IDs
                ids.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
        } else if (newStatus) {
            alert('Status tidak valid. Pilih:\n1. pending\n2. masuk pranota\n3. paid\n4. cancelled');
        }
    });

    // Bulk pranota handler
    btnBulkPranota.addEventListener('click', function() {
        if (this.disabled) {
            alert('Tidak dapat memproses pranota bulk karena vendor berbeda. Pilih item dengan vendor yang sama.');
            return;
        }

        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Pilih minimal satu item untuk masukan pranota');
            return;
        }

        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        showPranotaModal(ids, true);
    });
});
</script>
