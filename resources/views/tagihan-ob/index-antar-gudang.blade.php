@extends('layouts.app')

@section('title', 'Daftar Tagihan OB Antar Gudang')

@push('styles')
<style>
.editable-field .field-display {
    transition: all 0.2s ease;
    border-radius: 0.375rem;
}

.editable-field .field-display:hover {
    background-color: #f0fdfa;
    cursor: pointer;
    position: relative;
}

.editable-field .field-display:hover::after {
    content: '✏️';
    font-size: 0.75rem;
    position: absolute;
    right: -1.5rem;
    top: 50%;
    transform: translateY(-50%);
}

.editable-field .field-input {
    min-width: 120px;
}

/* Loading state */
.loading-field {
    opacity: 0.7;
    pointer-events: none;
}

/* Success highlight */
.field-success {
    background-color: #ccfbf1 !important;
    transition: background-color 0.5s ease;
}

.editable-field {
    position: relative;
}

.editable-field:hover .edit-tooltip {
    display: block;
}

.edit-tooltip {
    display: none;
    position: absolute;
    bottom: -2rem;
    left: 0;
    background-color: #0f766e;
    color: white;
    text-align: center;
    border-radius: 0.375rem;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    white-space: nowrap;
    z-index: 10;
}

.edit-tooltip::after {
    content: "";
    position: absolute;
    top: -0.25rem;
    left: 50%;
    margin-left: -0.25rem;
    border-width: 0 0.25rem 0.25rem 0.25rem;
    border-style: solid;
    border-color: transparent transparent #0f766e transparent;
}

/* Floating Action Bar */
.floating-bar {
    transform: translate(-50%, 100px) !important;
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
    opacity: 0;
}

.floating-bar.show {
    transform: translate(-50%, 0) !important;
    opacity: 1;
}
</style>
@endpush

@section('content')
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-teal-100">
        {{-- Header dengan tema Teal / Antar Gudang --}}
        <div class="bg-teal-700 text-white px-6 py-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h5 class="text-lg font-bold flex items-center">
                        <i class="fas fa-warehouse mr-2"></i>
                        Daftar Tagihan OB Antar Gudang
                    </h5>
                    <p class="text-teal-100 text-xs mt-1">Mengelola tagihan mutasi / perpindahan kontainer antar gudang</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="btnMasukkanPranota" onclick="openPranotaModal()" class="bg-yellow-500 hover:bg-yellow-400 text-teal-950 font-bold px-4 py-2 rounded-md text-sm transition duration-150 ease-in-out hidden">
                        <i class="fas fa-file-invoice-dollar mr-1"></i>
                        Masukkan ke Pranota (<span id="selectedCountHeader">0</span>)
                    </button>
                    <a href="{{ route('ob-antar-gudang.select') }}" class="bg-teal-600 hover:bg-teal-500 text-white border border-teal-500 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        <i class="fas fa-plus mr-1"></i>
                        Buat OB Baru (Pilih Gudang)
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4" role="alert">
                    <div class="flex justify-between items-center">
                        <span>{{ session('success') }}</span>
                        <button type="button" class="text-green-500 hover:text-green-700" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4" role="alert">
                    <div class="flex justify-between items-center">
                        <span>{{ session('error') }}</span>
                        <button type="button" class="text-red-500 hover:text-red-700" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Info Banner for Inline Editing --}}
            <div class="bg-teal-50 border border-teal-150 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-0.5">
                        <i class="fas fa-info-circle text-teal-600 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-teal-800">Inline Editing & Pembuatan Pranota</h3>
                        <p class="text-xs text-teal-600 mt-0.5">
                            * Anda dapat mengubah <strong>Nomor Kontainer</strong>, <strong>Nama Supir</strong>, dan <strong>Biaya</strong> secara langsung pada tabel. <br>
                            * Beri centang pada beberapa tagihan yang belum masuk pranota untuk memunculkan tombol <strong>"Masukkan ke Pranota"</strong> secara kolektif.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Filter & Search -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="md:col-span-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm" 
                               placeholder="Cari kontainer, supir, keterangan..." id="searchInput">
                    </div>
                </div>
                <div>
                    <select class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm" id="statusFilter">
                        <option value="">Semua Status</option>
                        <option value="full">Full</option>
                        <option value="empty">Empty</option>
                    </select>
                </div>
                <div>
                    <select class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm" id="pranotaFilter">
                        <option value="">Status Pranota</option>
                        <option value="sudah">Sudah Masuk Pranota</option>
                        <option value="belum">Belum Masuk Pranota</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" id="tagihanObTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 w-10">
                                <input type="checkbox" id="selectAll" class="rounded text-teal-600 focus:ring-teal-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Supir</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan Rute</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Biaya</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Pranota</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($tagihanOb as $index => $item)
                            <tr class="hover:bg-teal-50/30 transition duration-150" data-item-id="{{ $item->id }}">
                                <td class="px-4 py-4 whitespace-nowrap text-center text-xs w-10">
                                    @if($item->pranotaObAntarGudangItem)
                                        <input type="checkbox" disabled class="rounded bg-gray-100 text-gray-400 cursor-not-allowed" title="Sudah masuk pranota">
                                    @else
                                        <input type="checkbox" name="selected_tagihan[]" value="{{ $item->id }}" class="row-checkbox rounded text-teal-600 focus:ring-teal-500">
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">{{ $tagihanOb->firstItem() + $index }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    <div class="editable-field" data-field="nomor_kontainer" data-id="{{ $item->id }}" title="Klik untuk edit nomor kontainer">
                                        <span class="field-display font-semibold font-mono text-gray-900 bg-gray-50 px-2 py-1 rounded border border-gray-200">
                                            {{ $item->nomor_kontainer }}
                                        </span>
                                        <input type="text" class="field-input hidden w-full px-2 py-1 border border-teal-500 rounded text-xs font-mono focus:outline-none focus:ring-1 focus:ring-teal-500" value="{{ $item->nomor_kontainer }}">
                                        <div class="edit-tooltip">Klik untuk edit</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">
                                    <div class="editable-field" data-field="nama_supir" data-id="{{ $item->id }}" title="Klik untuk edit nama supir">
                                        <span class="field-display cursor-pointer px-1 py-1 rounded hover:bg-teal-50 font-medium">{{ $item->nama_supir }}</span>
                                        <input type="text" class="field-input hidden w-full px-2 py-1 border border-teal-500 rounded text-xs focus:outline-none focus:ring-1 focus:ring-teal-500" value="{{ $item->nama_supir }}">
                                        <div class="edit-tooltip">Klik untuk edit</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-900 font-medium">
                                    {{ $item->keterangan }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $item->status_kontainer === 'full' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($item->status_kontainer) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-gray-900">
                                    <div class="editable-field" data-field="biaya" data-id="{{ $item->id }}" title="Klik untuk edit biaya">
                                        <span class="field-display cursor-pointer px-1 py-1 rounded hover:bg-teal-50">Rp {{ number_format($item->biaya, 0, ',', '.') }}</span>
                                        <input type="number" class="field-input hidden w-full px-2 py-1 border border-teal-500 rounded text-xs focus:outline-none focus:ring-1 focus:ring-teal-500" value="{{ $item->biaya }}">
                                        <div class="edit-tooltip">Klik untuk edit</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    @if($item->pranotaObAntarGudangItem)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-800">
                                            <i class="fas fa-check-circle mr-1"></i> Sudah Masuk Pranota
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-800">
                                            <i class="fas fa-clock mr-1"></i> Belum Masuk Pranota
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-medium">
                                    <div class="flex justify-center space-x-1">
                                        @can('tagihan-ob-view')
                                            <a href="{{ route('tagihan-ob.show', $item) }}" 
                                               class="text-teal-600 hover:text-teal-900 bg-teal-50 hover:bg-teal-100 p-1.5 rounded transition duration-150" 
                                               title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan
                                        @can('tagihan-ob-update')
                                            <a href="{{ route('tagihan-ob.edit', $item) }}" 
                                               class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 p-1.5 rounded transition duration-150" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('tagihan-ob-delete')
                                            <button type="button" 
                                                    class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded transition duration-150" 
                                                    title="Hapus"
                                                    onclick="confirmDelete({{ $item->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                                        <p class="text-gray-500 text-sm">Belum ada data tagihan OB Antar Gudang</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4">
                <div class="text-xs text-gray-700">
                    Menampilkan {{ $tagihanOb->firstItem() ?? 0 }} - {{ $tagihanOb->lastItem() ?? 0 }} 
                    dari {{ $tagihanOb->total() }} data
                </div>
                <div class="pagination-links text-xs">
                    {{ $tagihanOb->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Bar -->
<div id="floatingBar" class="floating-bar fixed bottom-6 left-1/2 transform -translate-x-1/2 z-[9999] bg-teal-900 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center justify-between gap-6 border border-teal-700/50 max-w-lg w-11/12 md:w-full hidden">
    <div class="flex items-center gap-3">
        <div class="bg-teal-800 text-teal-200 px-3 py-1 rounded-lg text-sm font-bold" id="selectedCount">
            0
        </div>
        <p class="text-xs text-teal-100">Tagihan dipilih untuk Pranota</p>
    </div>
    <button type="button" onclick="openPranotaModal()" class="bg-teal-500 hover:bg-teal-400 text-teal-950 font-bold px-4 py-2 rounded-lg text-xs transition duration-150 shadow-md">
        <i class="fas fa-file-invoice-dollar mr-1"></i> Masukkan ke Pranota
    </button>
</div>

<!-- Modal Masukkan ke Pranota -->
<div id="pranotaModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Overlay --}}
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closePranotaModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal Content --}}
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-teal-150">
            <form action="{{ route('tagihan-ob-antar-gudang.store-pranota') }}" method="POST" id="pranotaForm">
                @csrf
                <div id="selectedIdsContainer"></div>

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-teal-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-file-invoice-dollar text-teal-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-sm font-bold text-gray-900" id="modal-title">
                                Buat Pranota OB Antar Gudang
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">Mengelompokkan tagihan mutasi terpilih ke dalam satu invoice Pranota.</p>

                            {{-- Tampilan Nominal & Grand Total --}}
                            <div class="mt-3 bg-teal-50 border border-teal-200 rounded-lg p-3 space-y-2 shadow-sm">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="font-semibold text-teal-800">Nominal:</span>
                                    <span class="font-bold text-teal-955" id="modalNominal">Rp 0</span>
                                </div>
                                <div class="flex justify-between items-center border-t border-teal-200 pt-2 text-sm">
                                    <span class="font-bold text-teal-800">Grand Total:</span>
                                    <span class="font-extrabold text-teal-955" id="modalGrandTotal">Rp 0</span>
                                </div>
                            </div>

                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="nomor_pranota" class="block text-xs font-semibold text-gray-700 mb-1">Nomor Pranota <span class="text-red-500">*</span></label>
                                    <div class="flex gap-2">
                                        <input type="text" name="nomor_pranota" id="nomor_pranota" required class="flex-1 px-3 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-teal-500 focus:border-teal-500 text-xs font-mono" placeholder="PAG-XXXXXX">
                                        <button type="button" onclick="ajaxGenerateNomor()" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 text-gray-700 px-3 py-1.5 rounded-md text-xs font-medium transition duration-150">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label for="tanggal_pranota" class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Pranota <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_pranota" id="tanggal_pranota" required value="{{ now()->format('Y-m-d') }}" class="w-full px-3 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-teal-500 focus:border-teal-500 text-xs">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="adjustment" class="block text-xs font-semibold text-gray-700 mb-1">Adjustment (Penyesuaian)</label>
                                        <input type="number" name="adjustment" id="adjustment" value="0" oninput="updateGrandTotal()" class="w-full px-3 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-teal-500 focus:border-teal-500 text-xs" placeholder="0">
                                    </div>

                                    <div>
                                        <label for="alasan_adjustment" class="block text-xs font-semibold text-gray-700 mb-1">Alasan Adjustment</label>
                                        <input type="text" name="alasan_adjustment" id="alasan_adjustment" class="w-full px-3 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-teal-500 focus:border-teal-500 text-xs" placeholder="Alasan penyesuaian biaya...">
                                    </div>
                                </div>

                                <div>
                                    <label for="keterangan_pranota" class="block text-xs font-semibold text-gray-700 mb-1">Keterangan (Opsional)</label>
                                    <textarea name="keterangan" id="keterangan_pranota" rows="2" class="w-full px-3 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-teal-500 focus:border-teal-500 text-xs" placeholder="Catatan tambahan pranota..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-600 text-xs font-bold text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-3 sm:w-auto transition-all">
                        Simpan Pranota
                    </button>
                    <button type="button" onclick="closePranotaModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-xs font-bold text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:mt-0 sm:w-auto">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-sm font-bold text-gray-900" id="modal-title">Konfirmasi Hapus</h3>
                    <div class="mt-2">
                        <p class="text-xs text-gray-500">Apakah Anda yakin ingin menghapus tagihan OB Antar Gudang ini?</p>
                        <p class="text-xs text-red-600 mt-1">Data yang sudah dihapus tidak dapat dikembalikan.</p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-xs font-bold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto transition-all">
                        Hapus
                    </button>
                </form>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-xs font-bold text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:mt-0 sm:w-auto" onclick="closeDeleteModal()">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

function confirmDelete(id) {
    document.getElementById('deleteForm').action = `/tagihan-ob/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    // Close modals when clicking outside
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.onclick = function(e) {
            if (e.target === this) closeDeleteModal();
        };
    }
    
    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
            closePranotaModal();
        }
    });
    
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll('tbody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
    
    // Filters
    document.getElementById('statusFilter').onchange = filterTable;
    document.getElementById('pranotaFilter').onchange = filterTable;
    
    // Initialize Checkboxes & Floating Bar
    initCheckboxes();

    // Initialize Inline Editing
    initInlineEditing();
});

// Checkboxes Logic
function initCheckboxes() {
    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const isChecked = this.checked;
            rowCheckboxes.forEach(cb => {
                // check if the checkbox is visible before selecting
                const tr = cb.closest('tr');
                if (tr && tr.style.display !== 'none') {
                    cb.checked = isChecked;
                }
            });
            updateFloatingBar();
        });
    }

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (!this.checked) {
                selectAll.checked = false;
            } else {
                const totalVisible = Array.from(rowCheckboxes).filter(c => c.closest('tr').style.display !== 'none').length;
                const totalChecked = Array.from(rowCheckboxes).filter(c => c.checked && c.closest('tr').style.display !== 'none').length;
                selectAll.checked = (totalVisible === totalChecked);
            }
            updateFloatingBar();
        });
    });
}

function updateFloatingBar() {
    const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
    const selectedCount = document.getElementById('selectedCount');
    const selectedCountHeader = document.getElementById('selectedCountHeader');
    const btnMasukkanPranota = document.getElementById('btnMasukkanPranota');
    const floatingBar = document.getElementById('floatingBar');

    if (selectedCount) selectedCount.textContent = checkedCount;
    if (selectedCountHeader) selectedCountHeader.textContent = checkedCount;

    if (btnMasukkanPranota) {
        if (checkedCount > 0) {
            btnMasukkanPranota.classList.remove('hidden');
        } else {
            btnMasukkanPranota.classList.add('hidden');
        }
    }

    if (floatingBar) {
        if (checkedCount > 0) {
            floatingBar.classList.add('show');
            floatingBar.classList.remove('hidden');
        } else {
            floatingBar.classList.remove('show');
            setTimeout(() => {
                if (document.querySelectorAll('.row-checkbox:checked').length === 0) {
                    floatingBar.classList.add('hidden');
                }
            }, 300);
        }
    }
}

window.currentNominal = 0;

function updateGrandTotal() {
    const nominal = window.currentNominal || 0;
    const adjustmentInput = document.getElementById('adjustment');
    const adjustment = parseFloat(adjustmentInput ? adjustmentInput.value : 0) || 0;
    const grandTotal = nominal + adjustment;

    const modalNominal = document.getElementById('modalNominal');
    if (modalNominal) {
        modalNominal.textContent = 'Rp ' + formatNumber(nominal);
    }

    const modalGrandTotal = document.getElementById('modalGrandTotal');
    if (modalGrandTotal) {
        modalGrandTotal.textContent = 'Rp ' + formatNumber(grandTotal);
    }
}

function openPranotaModal() {
    const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    const container = document.getElementById('selectedIdsContainer');
    container.innerHTML = ''; // Clear previous

    let totalBiaya = 0;
    checkedCheckboxes.forEach(cb => {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'selected_ids[]';
        hiddenInput.value = cb.value;
        container.appendChild(hiddenInput);

        const tr = cb.closest('tr');
        if (tr) {
            const biayaInput = tr.querySelector('.editable-field[data-field="biaya"] .field-input');
            if (biayaInput) {
                totalBiaya += parseFloat(biayaInput.value) || 0;
            }
        }
    });

    window.currentNominal = totalBiaya;

    // Reset fields
    const adjustmentInput = document.getElementById('adjustment');
    if (adjustmentInput) {
        adjustmentInput.value = 0;
    }
    const alasanInput = document.getElementById('alasan_adjustment');
    if (alasanInput) {
        alasanInput.value = '';
    }

    updateGrandTotal();

    // Auto-generate next pranota number
    ajaxGenerateNomor();

    document.getElementById('pranotaModal').classList.remove('hidden');
}

function closePranotaModal() {
    document.getElementById('pranotaModal').classList.add('hidden');
}

function ajaxGenerateNomor() {
    const nomorField = document.getElementById('nomor_pranota');
    nomorField.value = 'Loading...';

    fetch("{{ route('tagihan-ob-antar-gudang.generate-nomor-pranota') }}", {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            nomorField.value = data.nomor_pranota;
        } else {
            nomorField.value = '';
            alert('Gagal mengambil nomor pranota');
        }
    })
    .catch(error => {
        nomorField.value = '';
        console.error('Error fetching pranota number:', error);
    });
}

function filterTable() {
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const pranotaFilter = document.getElementById('pranotaFilter').value.toLowerCase();
    
    document.querySelectorAll('tbody tr').forEach(row => {
        // Status kontainer di col ke-7 (0-indexed 6)
        const statusText = row.querySelector('td:nth-child(7) span')?.textContent.toLowerCase().trim() || '';
        // Pranota di col ke-9 (0-indexed 8)
        const pranotaText = row.querySelector('td:nth-child(9) span')?.textContent.toLowerCase().trim() || '';
        
        const statusMatch = !statusFilter || statusText.includes(statusFilter);
        
        let pranotaMatch = true;
        if (pranotaFilter === 'sudah') {
            pranotaMatch = pranotaText.includes('sudah');
        } else if (pranotaFilter === 'belum') {
            pranotaMatch = pranotaText.includes('belum');
        }
        
        row.style.display = (statusMatch && pranotaMatch) ? '' : 'none';
    });

    // Uncheck selectAll and update floating bar on filter
    const selectAll = document.getElementById('selectAll');
    if (selectAll) selectAll.checked = false;
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
    
    // Trigger update
    updateFloatingBar();
}

// Inline editing functions
function initInlineEditing() {
    document.querySelectorAll('.editable-field').forEach(field => {
        const display = field.querySelector('.field-display');
        const input = field.querySelector('.field-input');
        
        display.onclick = function() {
            display.classList.add('hidden');
            input.classList.remove('hidden');
            input.focus();
            input.select();
        };
        
        input.onblur = function() {
            saveField(field, display, input);
        };
        
        input.onkeydown = function(e) {
            if (e.key === 'Enter') {
                input.blur();
            } else if (e.key === 'Escape') {
                cancelEdit(field, display, input);
            }
        };
    });
}

function cancelEdit(field, display, input) {
    const originalValue = display.textContent.trim();
    if (field.dataset.field === 'biaya') {
        input.value = originalValue.replace(/[^\d]/g, '');
    } else {
        input.value = originalValue;
    }
    display.classList.remove('hidden');
    input.classList.add('hidden');
}

function saveField(field, display, input) {
    const fieldName = field.dataset.field;
    const recordId = field.dataset.id;
    let newValue = input.value.trim();
    
    if (!newValue) {
        alert('Nilai tidak boleh kosong');
        input.focus();
        return;
    }
    
    if (fieldName === 'biaya') {
        const numericValue = parseFloat(newValue);
        if (isNaN(numericValue) || numericValue < 0) {
            alert('Nilai harus berupa angka yang valid');
            input.focus();
            return;
        }
        newValue = numericValue.toString();
    }
    
    const originalDisplayContent = display.innerHTML;
    display.innerHTML = '<i class="fas fa-spinner fa-spin text-teal-500"></i>';
    display.classList.remove('hidden');
    input.classList.add('hidden');
    
    fetch(`/tagihan-ob/${recordId}/update-field`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            field: fieldName,
            value: newValue
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateDisplayValue(display, fieldName, data.raw_value || data.formatted_value || newValue);
            showNotification('Data berhasil diperbarui', 'success');
        } else {
            throw new Error(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        display.innerHTML = originalDisplayContent;
        showNotification(error.message || 'Gagal menyimpan data', 'error');
        setTimeout(() => {
            display.classList.add('hidden');
            input.classList.remove('hidden');
            input.focus();
        }, 100);
    });
}

function updateDisplayValue(display, fieldName, value) {
    if (fieldName === 'biaya') {
        display.innerHTML = `Rp ${formatNumber(parseFloat(value))}`;
    } else {
        display.textContent = value;
    }
    
    display.classList.add('field-success');
    setTimeout(() => display.classList.remove('field-success'), 2000);
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-md shadow-lg transition-all duration-300 transform translate-x-full ${
        type === 'success' ? 'bg-teal-600 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-teal-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center text-xs">
            <i class="fas ${type === 'success' ? 'fa-check' : type === 'error' ? 'fa-times' : 'fa-info'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    setTimeout(() => notification.classList.remove('translate-x-full'), 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.parentElement.removeChild(notification);
            }
        }, 300);
    }, 3000);
}
</script>
@endpush
