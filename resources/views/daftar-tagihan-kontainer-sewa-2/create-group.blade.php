@extends('layouts.app')

@section('content')
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

/* Loading spinner animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid #ffffff33;
    border-radius: 50%;
    border-top-color: #ffffff;
    animation: spin 0.8s ease-in-out infinite;
    margin-right: 8px;
}

/* Notification styles */
.notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    max-width: 400px;
}

.notification {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 16px;
    margin-bottom: 12px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    transform: translateX(100%);
    opacity: 0;
    transition: all 0.3s ease-in-out;
    border-left: 4px solid;
}

.notification.show {
    transform: translateX(0);
    opacity: 1;
}

.notification.success {
    border-left-color: #10b981;
}

.notification.error {
    border-left-color: #ef4444;
}

.notification.warning {
    border-left-color: #f59e0b;
}

.notification-icon {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 12px;
}

.notification.success .notification-icon {
    background-color: #10b981;
}

.notification.error .notification-icon {
    background-color: #ef4444;
}

.notification.warning .notification-icon {
    background-color: #f59e0b;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 4px;
    color: #1f2937;
}

.notification-message {
    font-size: 13px;
    color: #6b7280;
    line-height: 1.4;
}

.notification-close {
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 2px;
    border-radius: 4px;
    transition: all 0.2s;
    flex-shrink: 0;
}

.notification-close:hover {
    color: #6b7280;
    background-color: #f3f4f6;
}

/* Button hover effects */
.btn-animated {
    transition: all 0.2s ease-in-out;
}

.btn-animated:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-animated:active {
    transform: translateY(0);
}

/* Sticky Table Header Styles */
.sticky-table-header {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: rgb(249 250 251);
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
}

/* Enhanced table container for better scrolling */
.table-container {
    max-height: calc(100vh - 400px);
    overflow-y: auto;
    border: 1px solid rgb(229 231 235);
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
    border-bottom: 2px solid rgb(147 51 234);
}

/* Ensure dropdown menus appear above sticky header */
.relative.group .absolute {
    z-index: 20;
}
</style>

<div class="container mx-auto p-4">
    <!-- Notification Container -->
    <div id="notification-container" class="notification-container"></div>

    <h1 class="text-2xl font-bold mb-4">Buat Group Tagihan Kontainer Sewa</h1>

    @if($errors->any())
        <div class="mb-4 text-red-700 bg-red-50 border border-red-200 p-4 rounded">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Group Information Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Group</h2>
        <form id="groupForm" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Group</label>
                    <input type="text" name="group_name" id="group_name" value="{{ old('group_name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Masukkan nama group" required />
                    <p class="text-xs text-gray-500 mt-1">Nama unik untuk mengelompokkan tagihan kontainer</p>
                    <button type="button" id="generate-code-btn" class="mt-2 px-3 py-1 text-xs bg-blue-500 hover:bg-blue-600 text-white rounded transition-colors">
                        Generate Kode Otomatis
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Container Selection Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Pilih Kontainer untuk Group</h2>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="select-all-containers" class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 focus:ring-2">
                    <label for="select-all-containers" class="text-sm text-gray-700">Pilih Semua</label>
                </div>
                <span id="selection-count" class="text-sm text-gray-600">0 kontainer dipilih</span>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="mb-4 bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <input type="date" id="filter-tanggal-awal"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div>
                    <select id="filter-vendor" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Semua Vendor</option>
                        @foreach($tagihans->pluck('vendor')->unique()->sort() as $vendor)
                            <option value="{{ $vendor }}">{{ $vendor }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="button" id="reset-filters" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Container Table -->
        <div class="table-container overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" style="min-width: 1400px;">
                <thead class="sticky-table-header bg-gray-50">
                    <tr class="border-b-2 border-gray-200">
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 60px;">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" id="select-all-table" class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 focus:ring-2">
                            </div>
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 160px;">
                            Nomor Kontainer
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 100px;">
                            Periode
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 120px;">
                            Status
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 120px;">
                            Tanggal Awal
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 120px;">
                            Tanggal Keluar
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 120px;">
                            Vendor
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="container-table-body">
                    @forelse($tagihans ?? [] as $tagihan)
                        <tr class="hover:bg-gray-50 container-row" data-vendor="{{ $tagihan->vendor }}" data-container="{{ $tagihan->nomor_kontainer }}" data-tanggal-awal="{{ $tagihan->tanggal_awal ? \Carbon\Carbon::parse($tagihan->tanggal_awal)->format('Y-m-d') : '' }}">
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <input type="checkbox" name="selected_containers[]" value="{{ $tagihan->id }}"
                                       class="container-checkbox w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 focus:ring-2">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-mono">
                                {{ $tagihan->nomor_kontainer }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                {{ $tagihan->periode }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($tagihan->status_pembayaran == 'belum_dibayar') bg-red-100 text-red-800
                                    @elseif($tagihan->status_pembayaran == 'sudah_dibayar') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $tagihan->status_pembayaran ?? 'belum_dibayar')) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                {{ $tagihan->tanggal_awal ? \Carbon\Carbon::parse($tagihan->tanggal_awal)->format('d/M/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                {{ $tagihan->tanggal_akhir ? \Carbon\Carbon::parse($tagihan->tanggal_akhir)->format('d/M/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                    <span>{{ $tagihan->vendor }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-5v2m0 0v2m0-2h2m-2 0h-2"></path>
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada data kontainer</p>
                                    <p class="text-sm">Belum ada tagihan kontainer yang tersedia untuk dibuat group</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Summary and Actions -->
        <div class="mt-6 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <span id="total-containers">{{ $tagihans->count() ?? 0 }}</span> kontainer tersedia
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('daftar-tagihan-kontainer-sewa-2.index') }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="button" id="create-group-btn" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Buat Group
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllContainers = document.getElementById('select-all-containers');
    const selectAllTable = document.getElementById('select-all-table');
    const containerCheckboxes = document.querySelectorAll('.container-checkbox');
    const selectionCount = document.getElementById('selection-count');
    const createGroupBtn = document.getElementById('create-group-btn');
    const resetFilters = document.getElementById('reset-filters');
    const containerRows = document.querySelectorAll('.container-row');
    const filterTanggalAwal = document.getElementById('filter-tanggal-awal');
    const filterVendor = document.getElementById('filter-vendor');

    // Update selection count
    function updateSelectionCount() {
        const checkedBoxes = document.querySelectorAll('.container-checkbox:checked');
        const count = checkedBoxes.length;
        selectionCount.textContent = `${count} kontainer dipilih`;

        // Enable/disable create button
        createGroupBtn.disabled = count === 0;
    }

    // Handle select all checkboxes
    function handleSelectAll(checkbox, checkboxes) {
        checkboxes.forEach(cb => {
            if (cb.closest('tr').style.display !== 'none') {
                cb.checked = checkbox.checked;
            }
        });
        updateSelectionCount();
        updateSelectAllState();
    }

    selectAllContainers.addEventListener('change', function() {
        handleSelectAll(this, containerCheckboxes);
    });

    selectAllTable.addEventListener('change', function() {
        handleSelectAll(this, containerCheckboxes);
    });

    // Handle individual checkboxes
    containerCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectionCount();
            updateSelectAllState();
        });
    });

    // Update select all state based on visible checkboxes
    function updateSelectAllState() {
        const visibleCheckboxes = Array.from(containerCheckboxes).filter(cb =>
            cb.closest('tr').style.display !== 'none'
        );
        const checkedVisibleBoxes = visibleCheckboxes.filter(cb => cb.checked);

        const allChecked = visibleCheckboxes.length > 0 && checkedVisibleBoxes.length === visibleCheckboxes.length;
        const someChecked = checkedVisibleBoxes.length > 0 && checkedVisibleBoxes.length < visibleCheckboxes.length;

        selectAllContainers.checked = allChecked;
        selectAllTable.checked = allChecked;

        selectAllContainers.indeterminate = someChecked;
        selectAllTable.indeterminate = someChecked;
    }

    // Filter functionality
    function filterContainers() {
        const selectedTanggalAwal = filterTanggalAwal.value;
        const selectedVendor = filterVendor.value;

        containerRows.forEach(row => {
            const vendor = row.dataset.vendor;
            const tanggalAwal = row.dataset.tanggalAwal;

            const matchesVendor = !selectedVendor || vendor === selectedVendor;
            const matchesTanggalAwal = !selectedTanggalAwal || tanggalAwal === selectedTanggalAwal;

            if (matchesVendor && matchesTanggalAwal) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
                // Uncheck hidden checkboxes
                const checkbox = row.querySelector('.container-checkbox');
                if (checkbox) checkbox.checked = false;
            }
        });

        updateSelectionCount();
        updateSelectAllState();
    }

    filterTanggalAwal.addEventListener('change', filterContainers);
    filterVendor.addEventListener('change', filterContainers);

    resetFilters.addEventListener('click', function() {
        filterTanggalAwal.value = '';
        filterVendor.value = '';
        filterContainers();
    });

    // Create group functionality
    createGroupBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.container-checkbox:checked');
        if (checkedBoxes.length === 0) {
            showNotification('error', 'Pilih Kontainer', 'Pilih minimal satu kontainer untuk membuat group');
            return;
        }

        // Get form data
        const formData = new FormData(document.getElementById('groupForm'));
        const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);

        // Validate form
        const groupName = formData.get('group_name');

        if (!groupName) {
            showNotification('error', 'Data Tidak Lengkap', 'Mohon isi nama group');
            return;
        }

        // Show loading state
        const originalText = createGroupBtn.innerHTML;
        createGroupBtn.innerHTML = '<span class="loading-spinner"></span>Membuat Group...';
        createGroupBtn.disabled = true;

        // Prepare submission data
        const submitData = new FormData();
        submitData.append('_token', '{{ csrf_token() }}');
        submitData.append('group_name', groupName);

        selectedIds.forEach(id => {
            submitData.append('selected_containers[]', id);
        });

        // Submit to server
        fetch('{{ route("daftar-tagihan-kontainer-sewa-2.store-group") }}', {
            method: 'POST',
            body: submitData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', 'Group Berhasil Dibuat',
                    `Group "${groupName}" berhasil dibuat dengan ${selectedIds.length} kontainer`);
                setTimeout(() => {
                    window.location.href = '{{ route("daftar-tagihan-kontainer-sewa-2.index") }}';
                }, 2000);
            } else {
                showNotification('error', 'Gagal Membuat Group', data.message || 'Terjadi kesalahan saat membuat group');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Error', 'Terjadi kesalahan saat membuat group');
        })
        .finally(() => {
            createGroupBtn.innerHTML = originalText;
            createGroupBtn.disabled = false;
        });
    });

    // Notification system
    window.showNotification = function(type, title, message) {
        const container = document.getElementById('notification-container');
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = `notification ${type}`;

        const icon = type === 'success' ? '✓' : type === 'error' ? '✕' : '⚠';
        notification.innerHTML = `
            <div class="notification-icon">${icon}</div>
            <div class="notification-content">
                <div class="notification-title">${title}</div>
                <div class="notification-message">${message}</div>
            </div>
            <button class="notification-close" onclick="this.parentElement.remove()">×</button>
        `;

        container.appendChild(notification);
        setTimeout(() => notification.classList.add('show'), 10);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    };

    // Initialize
    updateSelectionCount();

    // Auto-generate group code functionality
    const generateCodeBtn = document.getElementById('generate-code-btn');
    const groupNameInput = document.getElementById('group_name');

    generateCodeBtn.addEventListener('click', function() {
        generateGroupCode();
    });

    function generateGroupCode() {
        // Get current date
        const now = new Date();
        const year = now.getFullYear().toString().slice(-2); // Last 2 digits of year
        const month = (now.getMonth() + 1).toString().padStart(2, '0'); // Month with leading zero

        // Get running number from localStorage or start from 1
        const storageKey = `tks_running_number_${year}${month}`;
        let runningNumber = parseInt(localStorage.getItem(storageKey) || '0') + 1;

        // Format running number to 6 digits
        const formattedRunningNumber = runningNumber.toString().padStart(6, '0');

        // Generate code: TKS + year + month + running number
        const generatedCode = `TKS${year}${month}${formattedRunningNumber}`;

        // Set the value
        groupNameInput.value = generatedCode;

        // Save running number to localStorage
        localStorage.setItem(storageKey, runningNumber.toString());

        // Show success notification
        showNotification('success', 'Kode Generated', `Kode group berhasil di-generate: ${generatedCode}`);
    }
});
</script>
@endpush
