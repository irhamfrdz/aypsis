@extends('layouts.app')

@section('title', 'Pranota Tagihan CAT')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Pranota Tagihan CAT</h1>
                <p class="text-gray-600 mt-1">Kelola data pranota tagihan Container Annual Test</p>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div id="bulkActions" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-gray-700">
                        <span id="selectedCount">0</span> item dipilih
                    </span>
                    <button type="button" id="btnBulkStatus"
                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition duration-200">
                        Update Status
                    </button>
                    <button type="button" id="btnBulkPrint"
                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition duration-200">
                        Print Terpilih
                    </button>
                </div>
                <button type="button" id="btnCancelSelection"
                        class="text-gray-500 hover:text-gray-700 text-sm font-medium">
                    Batal Pilih
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('pranota-cat.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
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
                           placeholder="Nomor pranota, vendo/bengkel, keterangan..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="flex items-end space-x-2 md:col-span-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        Filter
                    </button>
                    <a href="{{ route('pranota-cat.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Pranota</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pranota</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendo/Bengkel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pranotaCats as $pranota)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="selected_pranota[]" value="{{ $pranota->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 item-checkbox">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $loop->iteration + ($pranotaCats->currentPage() - 1) * $pranotaCats->perPage() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $pranota->no_invoice ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $pranota->tanggal_pranota ? \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/M/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $pranota->supplier ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $pranota->calculateTotalAmount() ? 'Rp ' . number_format($pranota->calculateTotalAmount(), 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($pranota->status == 'unpaid')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Belum Lunas
                                </span>
                            @elseif($pranota->status == 'paid')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Lunas
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    {{ ucfirst($pranota->status ?? 'Unknown') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('pranota-cat.show', $pranota) }}"
                                   class="text-blue-600 hover:text-blue-900">Lihat</a>
                                @can('pranota-print')
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('pranota-cat.print', $pranota) }}"
                                   class="text-green-600 hover:text-green-900" target="_blank">Print</a>
                                @endcan
                                @can('audit-log-view')
                                <span class="text-gray-300">|</span>
                                <button type="button"
                                        class="audit-log-btn text-purple-600 hover:text-purple-900 bg-transparent border-none cursor-pointer"
                                        data-model-type="App\Models\PranotaCat"
                                        data-model-id="{{ $pranota->id }}"
                                        data-item-name="{{ $pranota->no_invoice ?? 'Pranota CAT' }}"
                                        title="Lihat Riwayat">
                                    Riwayat
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data pranota tagihan CAT ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pranotaCats->hasPages())
        <div class="mt-6">
            @include('components.modern-pagination', ['paginator' => $pranotaCats])
            @include('components.rows-per-page')
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    const btnBulkStatus = document.getElementById('btnBulkStatus');
    const btnBulkPrint = document.getElementById('btnBulkPrint');
    const btnCancelSelection = document.getElementById('btnCancelSelection');

    console.log('JavaScript loaded successfully');
    console.log('Elements found:', {
        selectAllCheckbox: !!selectAllCheckbox,
        itemCheckboxes: itemCheckboxes.length,
        bulkActions: !!bulkActions,
        selectedCount: !!selectedCount,
        btnBulkStatus: !!btnBulkStatus,
        btnBulkPrint: !!btnBulkPrint
    });

    // Initialize bulk actions on page load
    updateBulkActions();

    // Handle select all checkbox
    selectAllCheckbox.addEventListener('change', function() {
        console.log('Select all checkbox changed:', this.checked);
        const isChecked = this.checked;
        itemCheckboxes.forEach(checkbox => {
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
        const totalBoxes = itemCheckboxes.length;

        if (checkedBoxes.length === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedBoxes.length === totalBoxes) {
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

        if (selectedCount) {
            selectedCount.textContent = count;
        }

        if (bulkActions) {
            if (count > 0) {
                console.log('Showing bulk actions - removing hidden class');
                bulkActions.classList.remove('hidden');
                bulkActions.style.display = 'block'; // Force show
            } else {
                console.log('Hiding bulk actions - adding hidden class');
                bulkActions.classList.add('hidden');
                bulkActions.style.display = 'none'; // Force hide
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

    // Bulk status update handler
    btnBulkStatus.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Pilih minimal satu item untuk update status');
            return;
        }

        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        const newStatus = prompt('Masukkan status baru:\n1. unpaid - Belum Lunas\n2. paid - Lunas');

        let statusValue = '';
        if (newStatus === '1' || newStatus === 'unpaid') {
            statusValue = 'unpaid';
        } else if (newStatus === '2' || newStatus === 'paid') {
            statusValue = 'paid';
        }

        if (statusValue) {
            const message = `Apakah Anda yakin ingin mengubah status ${checkedBoxes.length} item menjadi "${statusValue}"?`;
            if (confirm(message)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("pranota-cat.bulk-status-update") }}';

                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                // Add status
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = statusValue;
                form.appendChild(statusInput);

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
            alert('Status tidak valid. Pilih:\n1. unpaid - Belum Lunas\n2. paid - Lunas');
        }
    });

    // Bulk print handler
    btnBulkPrint.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Pilih minimal satu item untuk print');
            return;
        }

        const ids = Array.from(checkedBoxes).map(cb => cb.value);

        // Open print windows for selected items
        ids.forEach(id => {
            window.open('{{ url("pranota-cat") }}/' + id + '/print', '_blank');
        });
    });
});
</script>

<!-- Audit Log Modal -->
@include('components.audit-log-modal')

@endsection
