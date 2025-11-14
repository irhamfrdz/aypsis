@extends('layouts.app')

@section('title', 'Daftar Tagihan OB')

@push('styles')
<style>
/* Modal Animation Styles */
.modal-overlay {
    transition: opacity 0.3s ease-out;
}

.modal-overlay.modal-show {
    opacity: 1;
}

.modal-overlay.modal-hide {
    opacity: 0;
}

.modal-content {
    transition: transform 0.3s ease-out, opacity 0.3s ease-out;
    transform: translateY(-20px) scale(0.95);
    opacity: 0;
}

.modal-content.modal-show {
    transform: translateY(0) scale(1);
    opacity: 1;
}

.modal-content.modal-hide {
    transform: translateY(-20px) scale(0.95);
    opacity: 0;
}

/* Backdrop blur animation */
.modal-backdrop {
    backdrop-filter: blur(0px);
    transition: backdrop-filter 0.3s ease-out;
}

.modal-backdrop.modal-show {
    backdrop-filter: blur(4px);
}

.editable-field .field-display {
    transition: all 0.2s ease;
    border-radius: 0.375rem;
}

.editable-field .field-display:hover {
    background-color: #dbeafe;
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

.editable-field code:hover {
    background-color: #dbeafe !important;
}

/* Loading state */
.loading-field {
    opacity: 0.7;
    pointer-events: none;
}

/* Success highlight */
.field-success {
    background-color: #dcfce7 !important;
    transition: background-color 0.5s ease;
}

/* Tooltip for editable fields */
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
    background-color: #374151;
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
    border-color: transparent transparent #374151 transparent;
}
</style>
@endpush

@section('content')
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="bg-blue-600 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h5 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-ship mr-2"></i>
                        Daftar Tagihan OB (On Board)
                    </h5>
                    @isset($selectedKapal, $selectedVoyage)
                        <div class="mt-1 text-blue-100 text-sm">
                            <span class="bg-blue-500 px-2 py-1 rounded text-xs mr-2">
                                <i class="fas fa-ship mr-1"></i>{{ $selectedKapal }}
                            </span>
                            <span class="bg-blue-500 px-2 py-1 rounded text-xs">
                                <i class="fas fa-route mr-1"></i>{{ $selectedVoyage }}
                            </span>
                        </div>
                    @endisset
                </div>
                <div class="flex space-x-2">
                    @isset($selectedKapal, $selectedVoyage)
                        <a href="{{ route('tagihan-ob.index') }}" class="bg-white text-blue-600 hover:bg-gray-50 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                            <i class="fas fa-exchange-alt mr-1"></i>
                            Ganti Kapal/Voyage
                        </a>
                    @endisset
                    @can('pranota-ob-create')
                        <button type="button" id="createPranotaBtn" style="display: none;" class="bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                            <i class="fas fa-file-invoice mr-1"></i>
                            Masukan Pranota (<span id="selectedCount">0</span>)
                        </button>
                    @endcan
                    @can('tagihan-ob-create')
                        <a href="{{ route('tagihan-ob.create') }}{{ isset($selectedKapal, $selectedVoyage) ? '?kapal=' . urlencode($selectedKapal) . '&voyage=' . urlencode($selectedVoyage) : '' }}" 
                           class="bg-white text-blue-600 hover:bg-gray-50 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                            <i class="fas fa-plus mr-1"></i>
                            Tambah Tagihan OB
                        </a>
                    @endcan
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

            <!-- Info Banner for Inline Editing -->
            @isset($selectedKapal, $selectedVoyage)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-edit text-blue-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Inline Editing Aktif</h3>
                            <p class="text-sm text-blue-600">
                                Anda dapat mengedit <strong>Nama Supir</strong>, <strong>Nomor Kontainer</strong>, dan <strong>Biaya</strong> langsung dari tabel. 
                                Klik pada field yang ingin diedit, lalu tekan <kbd class="px-1 py-0.5 text-xs bg-blue-100 rounded">Enter</kbd> untuk menyimpan atau <kbd class="px-1 py-0.5 text-xs bg-blue-100 rounded">Esc</kbd> untuk membatalkan.
                            </p>
                        </div>
                    </div>
                </div>
            @endisset

            <!-- Filter & Search -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="md:col-span-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Cari kapal, voyage, kontainer..." id="searchInput">
                    </div>
                </div>
                <div>
                    <select class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" id="statusFilter">
                        <option value="">Semua Status</option>
                        <option value="full">Full</option>
                        <option value="empty">Empty</option>
                    </select>
                </div>
                <div>
                    <select class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" id="pembayaranFilter">
                        <option value="">Status Pembayaran</option>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Kapal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Voyage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">No. Kontainer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Nama Supir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Biaya</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Status Bayar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($tagihanOb as $index => $item)
                            <tr class="hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}" data-item-id="{{ $item->id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if(!$item->pranotaObItem)
                                        <input type="checkbox" class="item-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                               value="{{ $item->id }}" data-biaya="{{ $item->biaya }}">
                                    @else
                                        <span class="text-gray-400" title="Sudah ada di pranota">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tagihanOb->firstItem() + $index }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->kapal }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->voyage }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="editable-field" data-field="nomor_kontainer" data-id="{{ $item->id }}" title="Klik untuk edit nomor kontainer">
                                        <span class="field-display">
                                            <code class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-mono">{{ $item->nomor_kontainer }}</code>
                                        </span>
                                        <input type="text" class="field-input hidden w-full px-2 py-1 border border-blue-500 rounded text-xs font-mono focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ $item->nomor_kontainer }}">
                                        <div class="edit-tooltip">Klik untuk edit</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="editable-field" data-field="nama_supir" data-id="{{ $item->id }}" title="Klik untuk edit nama supir">
                                        <span class="field-display cursor-pointer hover:bg-blue-50 px-1 py-1 rounded">{{ $item->nama_supir }}</span>
                                        <input type="text" class="field-input hidden w-full px-2 py-1 border border-blue-500 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ $item->nama_supir }}">
                                        <div class="edit-tooltip">Klik untuk edit</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($item->barang, 30) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $item->status_kontainer === 'full' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($item->status_kontainer) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <div class="editable-field" data-field="biaya" data-id="{{ $item->id }}" title="Klik untuk edit biaya">
                                        <span class="field-display cursor-pointer hover:bg-blue-50 px-1 py-1 rounded">Rp {{ number_format($item->biaya, 0, ',', '.') }}</span>
                                        <input type="number" class="field-input hidden w-full px-2 py-1 border border-blue-500 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ $item->biaya }}">
                                        <div class="edit-tooltip">Klik untuk edit</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $item->status_pembayaran === 'paid' ? 'bg-green-100 text-green-800' : 
                                           ($item->status_pembayaran === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($item->status_pembayaran) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @can('tagihan-ob-view')
                                            <a href="{{ route('tagihan-ob.show', $item) }}" 
                                               class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-2 py-1 rounded" 
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan
                                        @can('tagihan-ob-update')
                                            <a href="{{ route('tagihan-ob.edit', $item) }}" 
                                               class="text-yellow-600 hover:text-yellow-900 bg-yellow-100 hover:bg-yellow-200 px-2 py-1 rounded" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('tagihan-ob-delete')
                                            <button type="button" 
                                                    class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-2 py-1 rounded" 
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
                                <td colspan="12" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                                        <p class="text-gray-500 text-lg">Belum ada data tagihan OB</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-6">
                <div class="text-sm text-gray-700">
                    Menampilkan {{ $tagihanOb->firstItem() ?? 0 }} - {{ $tagihanOb->lastItem() ?? 0 }} 
                    dari {{ $tagihanOb->total() }} data
                </div>
                <div class="pagination-links">
                    {{ $tagihanOb->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Pranota Modal -->
<div id="pranotaModal" class="modal-overlay modal-backdrop fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20">
        <div class="modal-content bg-white rounded-lg shadow-xl max-w-2xl w-full my-8" onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                        <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 mr-3">
                            <i class="fas fa-file-invoice text-blue-600"></i>
                        </div>
                        Buat Pranota OB
                    </h3>
                    <button type="button" onclick="closePranotaModal()" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="px-6 py-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">Informasi Pranota</h4>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Jumlah tagihan dipilih: <span id="modalSelectedCount" class="font-semibold">0</span></p>
                                <p>Total biaya: <span id="modalTotalAmount" class="font-semibold">Rp 0</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                        
                        <form id="pranotaForm" method="POST" action="{{ route('pranota-ob.store') }}">
                            @csrf
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="nomor_pranota" class="block text-sm font-medium text-gray-700">
                                            Nomor Pranota <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="nomor_pranota" name="nomor_pranota" required
                                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Nomor Pranota">
                                        <p class="mt-1 text-xs text-gray-500">Contoh: PR-OB-2024-001</p>
                                    </div>
                                    
                                    <div>
                                        <label for="tanggal_pranota" class="block text-sm font-medium text-gray-700">
                                            Tanggal Pranota <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" id="tanggal_pranota" name="tanggal_pranota" required
                                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="total_biaya" class="block text-sm font-medium text-gray-700">
                                            Total Biaya <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" id="total_biaya" name="total_biaya" required readonly
                                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="0">
                                        <p class="mt-1 text-xs text-gray-500">Total otomatis dari tagihan terpilih</p>
                                    </div>
                                    
                                    <div>
                                        <label for="penyesuaian" class="block text-sm font-medium text-gray-700">
                                            Penyesuaian (Adjustment)
                                        </label>
                                        <input type="number" id="penyesuaian" name="penyesuaian" value="0"
                                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="0">
                                        <p class="mt-1 text-xs text-gray-500">Masukkan nilai penyesuaian (+ atau -)</p>
                                    </div>
                                </div>
                                
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-blue-900">Grand Total:</span>
                                        <span id="grand_total_display" class="text-lg font-bold text-blue-900">Rp 0</span>
                                    </div>
                                    <p class="text-xs text-blue-700 mt-1">Total Biaya + Penyesuaian</p>
                                </div>
                                
                                <div>
                                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                                    <textarea id="keterangan" name="keterangan" rows="3" 
                                              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                              placeholder="Masukkan keterangan pranota (opsional)"></textarea>
                                </div>
                                
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h5 class="text-sm font-medium text-gray-700 mb-3">Daftar Tagihan Terpilih:</h5>
                                    <div id="selectedItemsList" class="space-y-2 max-h-40 overflow-y-auto">
                                        <!-- Selected items will be populated here -->
                                    </div>
                                </div>
                            </div>
                    
                    <input type="hidden" name="tagihan_ids" id="selectedTagihanIds">
                </form>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="closePranotaModal()" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Batal
                </button>
                <button type="submit" form="pranotaForm" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>
                    Buat Pranota
                </button>
            </div>
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
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Konfirmasi Hapus
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Apakah Anda yakin ingin menghapus tagihan OB ini?
                        </p>
                        <p class="text-sm text-red-600 mt-1">
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Hapus
                    </button>
                </form>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm" onclick="closeDeleteModal()">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedItems = [];

// Fungsi untuk update selected items dan toggle button
function updateSelectedItems() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    selectedItems = [];
    
    checkboxes.forEach(checkbox => {
        const row = checkbox.closest('tr');
        const itemId = checkbox.value;
        const biaya = parseFloat(checkbox.dataset.biaya);
        const kontainer = row.querySelector('td:nth-child(6) code')?.textContent || '';
        const supir = row.querySelector('td:nth-child(7) .field-display')?.textContent.trim() || '';
        
        selectedItems.push({
            id: itemId,
            biaya: biaya,
            kontainer: kontainer,
            supir: supir
        });
    });
    
    // Update counter dan toggle button
    const btn = document.getElementById('createPranotaBtn');
    const counter = document.getElementById('selectedCount');
    
    if (counter) counter.textContent = selectedItems.length;
    
    if (btn) {
        btn.style.display = selectedItems.length > 0 ? 'inline-flex' : 'none';
    }
}

// Fungsi untuk buka modal pranota
function openPranotaModal() {
    // Update modal content
    const totalBiaya = selectedItems.reduce((sum, item) => sum + item.biaya, 0);
    
    document.getElementById('modalSelectedCount').textContent = selectedItems.length;
    document.getElementById('modalTotalAmount').textContent = 'Rp ' + formatNumber(totalBiaya);
    document.getElementById('selectedTagihanIds').value = selectedItems.map(item => item.id).join(',');
    
    // Set total biaya
    const totalBiayaField = document.getElementById('total_biaya');
    if (totalBiayaField) {
        totalBiayaField.value = totalBiaya;
    }
    
    // Reset penyesuaian
    const penyesuaianField = document.getElementById('penyesuaian');
    if (penyesuaianField) {
        penyesuaianField.value = 0;
    }
    
    // Update grand total display
    updateGrandTotal();
    
    // Set default tanggal pranota (today)
    const today = new Date().toISOString().split('T')[0];
    const tanggalPranotaField = document.getElementById('tanggal_pranota');
    if (tanggalPranotaField) {
        tanggalPranotaField.value = today;
    }
    
    // Generate nomor pranota otomatis (format: PR-OB-YYYY-MM-XXX)
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const random = String(Math.floor(Math.random() * 1000)).padStart(3, '0');
    const nomorPranota = `PR-OB-${year}-${month}-${random}`;
    
    const nomorPranotaField = document.getElementById('nomor_pranota');
    if (nomorPranotaField) {
        nomorPranotaField.value = nomorPranota;
    }
    
    // Generate default keterangan
    const containerCount = selectedItems.length;
    const defaultKeterangan = `Pranota OB - ${containerCount} kontainer`;
    const keteranganField = document.getElementById('keterangan');
    if (keteranganField) {
        keteranganField.value = defaultKeterangan;
    }
    
    // Populate list
    const listContainer = document.getElementById('selectedItemsList');
    listContainer.innerHTML = '';
    
    selectedItems.forEach(item => {
        const div = document.createElement('div');
        div.className = 'flex justify-between items-center p-2 bg-white rounded border text-sm';
        div.innerHTML = `
            <div class="flex-1">
                <span class="font-medium">${item.kontainer}</span>
                <span class="text-gray-500 ml-2">${item.supir}</span>
            </div>
            <span class="font-medium text-blue-600">Rp ${formatNumber(item.biaya)}</span>
        `;
        listContainer.appendChild(div);
    });
    
    // Show modal with animation
    const modal = document.getElementById('pranotaModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Trigger animation after a small delay
        setTimeout(() => {
            modal.classList.add('modal-show');
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.classList.add('modal-show');
            }
        }, 10);
        
        console.log('Modal opened with animation');
    } else {
        console.error('Modal not found!');
    }
}

// Fungsi untuk update grand total
function updateGrandTotal() {
    const totalBiaya = parseFloat(document.getElementById('total_biaya')?.value || 0);
    const penyesuaian = parseFloat(document.getElementById('penyesuaian')?.value || 0);
    const grandTotal = totalBiaya + penyesuaian;
    
    const grandTotalDisplay = document.getElementById('grand_total_display');
    if (grandTotalDisplay) {
        grandTotalDisplay.textContent = 'Rp ' + formatNumber(grandTotal);
    }
}

// Fungsi untuk tutup modal
function closePranotaModal() {
    const modal = document.getElementById('pranotaModal');
    if (!modal) return;
    
    // Add closing animation
    modal.classList.add('modal-hide');
    modal.classList.remove('modal-show');
    
    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        modalContent.classList.add('modal-hide');
        modalContent.classList.remove('modal-show');
    }
    
    // Hide modal after animation completes
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('modal-hide');
        if (modalContent) {
            modalContent.classList.remove('modal-hide');
        }
        document.body.style.overflow = 'auto';
        
        // Reset form
        const form = document.getElementById('pranotaForm');
        if (form) {
            form.reset();
        }
    }, 300); // Match the CSS transition duration
}

// Format number Indonesia
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

// Delete modal functions
function confirmDelete(id) {
    document.getElementById('deleteForm').action = `/tagihan-ob/${id}`;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing pranota modal...');
    
    // Pranota button click
    const pranotaBtn = document.getElementById('createPranotaBtn');
    if (pranotaBtn) {
        console.log('Pranota button found, attaching click handler');
        pranotaBtn.onclick = function(e) {
            e.preventDefault();
            console.log('Pranota button clicked!');
            console.log('Selected items:', selectedItems);
            openPranotaModal();
        };
    } else {
        console.error('Pranota button NOT found!');
    }
    
    // Penyesuaian field change listener
    const penyesuaianField = document.getElementById('penyesuaian');
    if (penyesuaianField) {
        penyesuaianField.addEventListener('input', updateGrandTotal);
        penyesuaianField.addEventListener('change', updateGrandTotal);
    }
    
    // Select all checkbox
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.onchange = function() {
            document.querySelectorAll('.item-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            updateSelectedItems();
        };
    }
    
    // Individual checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('item-checkbox')) {
            updateSelectedItems();
            
            // Update select all state
            const allCheckboxes = document.querySelectorAll('.item-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
            const selectAllCheckbox = document.getElementById('selectAll');
            
            if (selectAllCheckbox) {
                if (checkedCheckboxes.length === 0) {
                    selectAllCheckbox.indeterminate = false;
                    selectAllCheckbox.checked = false;
                } else if (checkedCheckboxes.length === allCheckboxes.length) {
                    selectAllCheckbox.indeterminate = false;
                    selectAllCheckbox.checked = true;
                } else {
                    selectAllCheckbox.indeterminate = true;
                }
            }
        }
    });
    
    // Close modals when clicking outside
    const pranotaModal = document.getElementById('pranotaModal');
    if (pranotaModal) {
        pranotaModal.onclick = function(e) {
            if (e.target === this) closePranotaModal();
        };
    }
    
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.onclick = function(e) {
            if (e.target === this) closeDeleteModal();
        };
    }
    
    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (pranotaModal && !pranotaModal.classList.contains('hidden')) {
                closePranotaModal();
            }
            if (deleteModal && !deleteModal.classList.contains('hidden')) {
                closeDeleteModal();
            }
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
    document.getElementById('pembayaranFilter').onchange = filterTable;
    
    // Initialize
    updateSelectedItems();
    
    // Inline editing
    initInlineEditing();
});

function filterTable() {
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const pembayaranFilter = document.getElementById('pembayaranFilter').value.toLowerCase();
    
    document.querySelectorAll('tbody tr').forEach(row => {
        const statusText = row.querySelector('td:nth-child(9) span')?.textContent.toLowerCase() || '';
        const pembayaranText = row.querySelector('td:nth-child(11) span')?.textContent.toLowerCase() || '';
        
        const statusMatch = !statusFilter || statusText.includes(statusFilter);
        const pembayaranMatch = !pembayaranFilter || pembayaranText.includes(pembayaranFilter);
        
        row.style.display = (statusMatch && pembayaranMatch) ? '' : 'none';
    });
    
    updateSelectedItems();
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
    } else if (field.dataset.field === 'nomor_kontainer') {
        input.value = display.querySelector('code').textContent.trim();
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
            alert('Nilai biaya harus berupa angka yang valid');
            input.focus();
            return;
        }
        newValue = numericValue.toString();
    }
    
    const originalDisplayContent = display.innerHTML;
    display.innerHTML = '<i class="fas fa-spinner fa-spin text-blue-500"></i> Menyimpan...';
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
    } else if (fieldName === 'nomor_kontainer') {
        display.innerHTML = `<code class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-mono">${value}</code>`;
    } else {
        display.textContent = value;
    }
    
    display.classList.add('field-success');
    setTimeout(() => display.classList.remove('field-success'), 2000);
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-md shadow-lg transition-all duration-300 transform translate-x-full ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
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