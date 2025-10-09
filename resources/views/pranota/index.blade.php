@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-900">Daftar Pranota Kontainer Sewa</h1>
            <div class="flex items-center space-x-3">
                <a href="{{ route('pranota-kontainer-sewa.import') }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Import Pranota
                </a>
                <a href="{{ route('pranota-kontainer-sewa.create') }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Pranota Baru
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Search Section -->
            <div class="mb-6">
                <form method="GET" action="{{ route('pranota-kontainer-sewa.index') }}" id="searchForm">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex-1 max-w-md">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text"
                                       name="search"
                                       id="searchInput"
                                       value="{{ request('search') }}"
                                       placeholder="Cari berdasarkan No. Pranota, Keterangan, No. Invoice Vendor... (Ctrl+K)"
                                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       title="Tekan Ctrl+K untuk fokus pencarian, ESC untuk menghapus"
                                       autocomplete="off">
                            </div>
                        </div>

                        <!-- Filter by Status -->
                        <div class="flex items-center space-x-2">
                            <label for="statusFilter" class="text-sm font-medium text-gray-700">Status:</label>
                            <select name="status"
                                    id="statusFilter"
                                    class="rounded border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    onchange="document.getElementById('searchForm').submit()">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Dibayar</option>
                                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Terlambat</option>
                            </select>
                        </div>

                        <!-- Clear Filters & Search Button -->
                        <div class="flex items-center space-x-2">
                            @if(request('search') || request('status'))
                                <a href="{{ route('pranota-kontainer-sewa.index') }}"
                                   class="text-sm text-red-600 hover:text-red-800 font-medium">
                                    Clear Filters
                                </a>
                            @endif
                            <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Search Results Info -->
            @if(request('search') || request('status'))
                <div class="mb-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                        <p class="text-sm text-blue-800">
                            <span class="font-medium">
                                Menampilkan {{ $pranotaList->total() }} hasil
                                @if(request('search'))
                                    untuk pencarian "<strong>{{ request('search') }}</strong>"
                                @endif
                                @if(request('status'))
                                    dengan status "<strong>{{ ucfirst(request('status')) }}</strong>"
                                @endif
                            </span>
                            <a href="{{ route('pranota-kontainer-sewa.index') }}"
                               class="ml-2 text-blue-600 hover:text-blue-800 font-medium underline">
                                Tampilkan semua
                            </a>
                        </p>
                    </div>
                </div>
            @endif

            <!-- Bulk Actions -->
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" onchange="toggleAllCheckboxes()">
                        <span class="ml-2 text-sm text-gray-700">Pilih Semua</span>
                    </label>
                    <span id="selectedCount" class="text-sm text-gray-500">0 pranota dipilih</span>
                </div>

                <!-- Bulk Delete Button -->
                <div id="bulkActionsContainer" class="hidden">
                    @can('pranota-kontainer-sewa-delete')
                    <button type="button"
                            onclick="bulkDelete()"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus Terpilih
                    </button>
                    @endcan
                </div>
            </div>

            <!-- Bulk Delete Form (Hidden) -->
            <form id="bulkDeleteForm" action="{{ route('pranota-kontainer-sewa.bulk-delete') }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
                <input type="hidden" name="pranota_ids" id="bulkDeleteIds">
            </form>

            <!-- Table -->
            <div class="table-container overflow-x-auto max-h-screen">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="sticky-table-header bg-gray-50 sticky top-0 z-10 shadow-sm">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAllHeader" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" onchange="toggleAllCheckboxes()">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pranota</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pranota</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Tagihan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Invoice Vendor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Invoice Vendor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pembayaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pembayaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 table-body-text">
                        @forelse($pranotaList as $index => $pranota)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox"
                                       class="pranota-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                       value="{{ $pranota->id }}"
                                       data-amount="{{ $pranota->total_amount }}"
                                       data-no-invoice="{{ $pranota->no_invoice }}"
                                       onchange="updateSelection()">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $pranotaList->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('pranota-kontainer-sewa.show', $pranota->id) }}"
                                   class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    {{ $pranota->no_invoice }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $pranota->tanggal_pranota->format('d/M/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $pranota->jumlah_tagihan }} item
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Rp {{ number_format($pranota->total_amount, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $pranota->no_invoice_vendor ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($pranota->tgl_invoice_vendor)
                                    {{ $pranota->tgl_invoice_vendor->format('d/M/Y') }}
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pranota->getStatusColor() }}">
                                    {{ $pranota->getStatusLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($pranota->status === 'paid' && $pranota->getPaymentDate())
                                    {{ $pranota->getPaymentDate()->format('d/M/Y') }}
                                @elseif($pranota->status === 'paid')
                                    <span class="text-green-600">Dibayar</span>
                                @else
                                    <span class="text-gray-500">Belum dibayar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <!-- View Button -->
                                    <a href="{{ route('pranota-kontainer-sewa.show', $pranota->id) }}"
                                       class="text-indigo-600 hover:text-indigo-900">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    <!-- Print Button -->
                                    <a href="{{ route('pranota-kontainer-sewa.print', $pranota->id) }}" target="_blank"
                                       class="text-blue-600 hover:text-blue-900"
                                       title="Print Pranota">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                    </a>

                                    <!-- Edit Button -->
                                    @can('pranota-kontainer-sewa-edit')
                                    <a href="{{ route('pranota-kontainer-sewa.edit', $pranota->id) }}"
                                       class="text-yellow-600 hover:text-yellow-900"
                                       title="Edit Pranota">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @endcan

                                    <!-- Delete Button -->
                                    @can('pranota-kontainer-sewa-delete')
                                    <form action="{{ route('pranota-kontainer-sewa.destroy', $pranota->id) }}"
                                          method="POST"
                                          class="inline-block"
                                          onsubmit="return confirmDelete(event, '{{ $pranota->no_invoice }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-900"
                                                title="Hapus Pranota">
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
                            <td colspan="11" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    @if(request('search') || request('status'))
                                        <!-- No Search Results -->
                                        <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada hasil ditemukan</h3>
                                        <p class="text-gray-500 mb-4">Coba ubah kata kunci pencarian atau filter yang Anda gunakan.</p>
                                        <a href="{{ route('pranota-kontainer-sewa.index') }}"
                                           class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors duration-150">
                                            Hapus Filter
                                        </a>
                                    @else
                                        <!-- No Data at All -->
                                        <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data pranota</h3>
                                        <p class="text-gray-500 mb-4">Mulai dengan membuat pranota pertama Anda.</p>
                                        <a href="{{ route('pranota-kontainer-sewa.create') }}"
                                           class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors duration-150">
                                            Buat Pranota Sekarang
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            {{ $pranotaList->appends(request()->query())->links('components.modern-pagination', ['routeName' => 'pranota-kontainer-sewa.index']) }}
        </div>
    </div>
</div>

<script>
function toggleAllCheckboxes() {
    const selectAll = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const checkboxes = document.querySelectorAll('.pranota-checkbox');

    // Sync both select all checkboxes
    if (selectAll.checked) {
        selectAllHeader.checked = true;
    } else {
        selectAllHeader.checked = false;
    }

    // If triggered from header checkbox, sync with sidebar checkbox
    if (event.target.id === 'selectAllHeader') {
        selectAll.checked = selectAllHeader.checked;
    }

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });

    updateSelection();
}

function updateSelection() {
    const checkboxes = document.querySelectorAll('.pranota-checkbox:checked');
    const selectedCount = checkboxes.length;
    const totalAmount = Array.from(checkboxes).reduce((sum, checkbox) => {
        return sum + parseFloat(checkbox.dataset.amount);
    }, 0);

    // Update count display
    document.getElementById('selectedCount').textContent =
        selectedCount > 0 ?
        `${selectedCount} pranota dipilih (Total: Rp ${new Intl.NumberFormat('id-ID').format(totalAmount)})` :
        '0 pranota dipilih';

    // Show/hide bulk actions container
    const bulkActionsContainer = document.getElementById('bulkActionsContainer');
    if (bulkActionsContainer) {
        if (selectedCount > 0) {
            bulkActionsContainer.classList.remove('hidden');
        } else {
            bulkActionsContainer.classList.add('hidden');
        }
    }

    // Update select all checkboxes
    const allCheckboxes = document.querySelectorAll('.pranota-checkbox');
    const selectAll = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');

    if (selectedCount === 0) {
        selectAll.indeterminate = false;
        selectAll.checked = false;
        selectAllHeader.indeterminate = false;
        selectAllHeader.checked = false;
    } else if (selectedCount === allCheckboxes.length) {
        selectAll.indeterminate = false;
        selectAll.checked = true;
        selectAllHeader.indeterminate = false;
        selectAllHeader.checked = true;
    } else {
        selectAll.indeterminate = true;
        selectAll.checked = false;
        selectAllHeader.indeterminate = true;
        selectAllHeader.checked = false;
    }
}

// Confirm delete single pranota
function confirmDelete(event, pranotaNo) {
    event.preventDefault();

    if (confirm(`Apakah Anda yakin ingin menghapus pranota "${pranotaNo}"?\n\nTindakan ini tidak dapat dibatalkan!`)) {
        event.target.submit();
    }

    return false;
}

// Bulk delete function
function bulkDelete() {
    const checkboxes = document.querySelectorAll('.pranota-checkbox:checked');
    const selectedCount = checkboxes.length;

    if (selectedCount === 0) {
        alert('Pilih minimal 1 pranota untuk dihapus.');
        return;
    }

    const pranotaNumbers = Array.from(checkboxes).map(cb => cb.dataset.noInvoice).join(', ');
    const confirmMessage = `Apakah Anda yakin ingin menghapus ${selectedCount} pranota?\n\nPranota yang akan dihapus:\n${pranotaNumbers}\n\nTindakan ini tidak dapat dibatalkan!`;

    if (confirm(confirmMessage)) {
        const pranotaIds = Array.from(checkboxes).map(cb => cb.value);
        document.getElementById('bulkDeleteIds').value = JSON.stringify(pranotaIds);
        document.getElementById('bulkDeleteForm').submit();
    }
}



// Server-side Search Functions
function performServerSearch() {
    document.getElementById('searchForm').submit();
}

// Debounced search function
function debounceSearch() {
    clearTimeout(window.searchTimeout);
    window.searchTimeout = setTimeout(performServerSearch, 1000); // 1 second delay
}

// Enhanced updateSelection function for server-side pagination
function updateSelection() {
    const checkboxes = document.querySelectorAll('.pranota-checkbox:checked');
    const selectedCount = checkboxes.length;
    const totalAmount = Array.from(checkboxes).reduce((sum, checkbox) => {
        return sum + parseFloat(checkbox.dataset.amount || 0);
    }, 0);

    // Update count display
    document.getElementById('selectedCount').textContent =
        selectedCount > 0 ?
        `${selectedCount} pranota dipilih (Total: Rp ${new Intl.NumberFormat('id-ID').format(totalAmount)})` :
        '0 pranota dipilih';

    // Show/hide bulk actions container
    const bulkActionsContainer = document.getElementById('bulkActionsContainer');
    if (bulkActionsContainer) {
        if (selectedCount > 0) {
            bulkActionsContainer.classList.remove('hidden');
        } else {
            bulkActionsContainer.classList.add('hidden');
        }
    }

    // Update select all checkboxes
    const allCheckboxes = document.querySelectorAll('.pranota-checkbox');
    const selectAll = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');

    if (selectedCount === 0) {
        selectAll.indeterminate = false;
        selectAll.checked = false;
        selectAllHeader.indeterminate = false;
        selectAllHeader.checked = false;
    } else if (selectedCount === allCheckboxes.length) {
        selectAll.indeterminate = false;
        selectAll.checked = true;
        selectAllHeader.indeterminate = false;
        selectAllHeader.checked = true;
    } else {
        selectAll.indeterminate = true;
        selectAll.checked = false;
        selectAllHeader.indeterminate = true;
        selectAllHeader.checked = false;
    }
}

// Enhanced toggleAllCheckboxes for server-side pagination
function toggleAllCheckboxes() {
    const selectAll = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const allCheckboxes = document.querySelectorAll('.pranota-checkbox');

    // Sync both select all checkboxes
    if (selectAll.checked) {
        selectAllHeader.checked = true;
    } else {
        selectAllHeader.checked = false;
    }

    // If triggered from header checkbox, sync with sidebar checkbox
    if (event.target.id === 'selectAllHeader') {
        selectAll.checked = selectAllHeader.checked;
    }

    // Toggle all checkboxes on current page
    allCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });

    updateSelection();
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Add change listeners to existing checkboxes
    document.querySelectorAll('.pranota-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelection);
    });

    // Initial update
    updateSelection();

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+K or Ctrl+F to focus search
        if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'f')) {
            e.preventDefault();
            document.getElementById('searchInput').focus();
        }

        // Escape to clear search
        if (e.key === 'Escape') {
            const searchInput = document.getElementById('searchInput');
            if (document.activeElement === searchInput) {
                clearFilters();
                searchInput.blur();
            }
        }
    });

    // Add search input event listener for debounced server search
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounceSearch);

        // Submit on Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performServerSearch();
            }
        });
    }

    // Sticky Header Enhancement
    const tableContainer = document.querySelector('.table-container');
    const stickyHeader = document.querySelector('.sticky-table-header');

    if (tableContainer && stickyHeader) {
        // Add scroll event listener for visual feedback
        tableContainer.addEventListener('scroll', function() {
            if (tableContainer.scrollTop > 0) {
                tableContainer.classList.add('scrolled');
            } else {
                tableContainer.classList.remove('scrolled');
            }
        });

        // Optional: Add smooth scroll to top button
        const scrollToTopBtn = document.createElement('button');
        scrollToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
        scrollToTopBtn.className = 'fixed bottom-4 right-4 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-all duration-300 opacity-0 invisible z-50';
        scrollToTopBtn.title = 'Scroll ke atas';
        document.body.appendChild(scrollToTopBtn);

        // Show/hide scroll to top button
        tableContainer.addEventListener('scroll', function() {
            if (tableContainer.scrollTop > 200) {
                scrollToTopBtn.classList.remove('opacity-0', 'invisible');
                scrollToTopBtn.classList.add('opacity-100', 'visible');
            } else {
                scrollToTopBtn.classList.add('opacity-0', 'invisible');
                scrollToTopBtn.classList.remove('opacity-100', 'visible');
            }
        });

        // Scroll to top functionality
        scrollToTopBtn.addEventListener('click', function() {
            tableContainer.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});
</script>

<style>
/* Sticky Table Header Styles */
.sticky-table-header {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: rgb(249 250 251); /* bg-gray-50 */
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
}

/* Enhanced table container for better scrolling */
.table-container {
    max-height: calc(100vh - 300px); /* Adjust based on your layout */
    overflow-y: auto;
    border: 1px solid rgb(229 231 235); /* border-gray-200 */
    border-radius: 0.5rem;
}

/* Smooth scrolling for better UX */
.table-container {
    scroll-behavior: smooth;
}

/* Table header cells need specific background to avoid transparency issues */
.sticky-table-header th {
    background-color: rgb(249 250 251) !important;
    border-bottom: 1px solid rgb(229 231 235);
}

/* Optional: Add a subtle border when scrolling */
.table-container.scrolled .sticky-table-header {
    border-bottom: 2px solid rgb(59 130 246); /* blue-500 */
}

/* Ensure dropdown menus appear above sticky header */
.relative.group .absolute {
    z-index: 20;
}

/* Custom table body font size */
.table-body-text td {
    font-size: 10px !important;
}

/* Search and Filter Styles */
#searchInput:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

#statusFilter:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Search Results Animation */
#searchInfo {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Highlight search results */
.search-highlight {
    background-color: rgba(255, 235, 59, 0.3);
    padding: 2px 4px;
    border-radius: 2px;
}

/* Filter button styles */
.filter-active {
    background-color: rgb(59 130 246) !important;
    color: white !important;
}

/* No results state */
.no-results-message {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Search input focus animation */
#searchInput {
    transition: all 0.3s ease;
}

#searchInput:focus {
    transform: scale(1.02);
}

/* Table row fade animation */
tbody tr {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

tbody tr[style*="display: none"] {
    opacity: 0;
    transform: scale(0.98);
}

/* Status filter animation */
#statusFilter {
    transition: all 0.2s ease;
}

/* Responsive search section */
@media (max-width: 768px) {
    .search-section {
        flex-direction: column;
        gap: 1rem;
    }

    #searchInput {
        font-size: 16px; /* Prevents zoom on iOS */
    }
}

/* Enhanced Pagination Styles */
.pagination-links .page-link {
    @apply inline-flex items-center px-2.5 py-1.5 text-sm font-medium transition-colors duration-200 border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900;
}

.pagination-links .page-link.active {
    @apply bg-blue-600 border-blue-600 text-white hover:bg-blue-700 hover:border-blue-700;
}

.pagination-links .page-link.disabled {
    @apply opacity-50 cursor-not-allowed pointer-events-none;
}

.pagination-links .page-item:first-child .page-link {
    @apply rounded-l-md;
}

.pagination-links .page-item:last-child .page-link {
    @apply rounded-r-md;
}

.pagination-links .page-item:not(:first-child):not(:last-child) .page-link {
    @apply border-l-0;
}
</style>
@endsection
