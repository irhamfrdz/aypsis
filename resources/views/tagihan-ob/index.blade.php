@extends('layouts.app')

@section('title', 'Daftar Tagihan OB')

@push('styles')
<style>
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
                    <thead class="bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Kapal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Voyage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">No. Kontainer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Nama Supir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Biaya</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status Bayar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($tagihanOb as $index => $item)
                            <tr class="hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
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
                                <td colspan="11" class="px-6 py-12 text-center">
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
function confirmDelete(id) {
    const form = document.getElementById('deleteForm');
    form.action = `/tagihan-ob/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Simple search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const table = document.querySelector('tbody');
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

// Status filters
document.getElementById('statusFilter').addEventListener('change', function() {
    filterTable();
});

document.getElementById('pembayaranFilter').addEventListener('change', function() {
    filterTable();
});

function filterTable() {
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const pembayaranFilter = document.getElementById('pembayaranFilter').value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const statusText = row.querySelector('td:nth-child(8) span')?.textContent.toLowerCase() || '';
        const pembayaranText = row.querySelector('td:nth-child(10) span')?.textContent.toLowerCase() || '';
        
        const statusMatch = !statusFilter || statusText.includes(statusFilter);
        const pembayaranMatch = !pembayaranFilter || pembayaranText.includes(pembayaranFilter);
        
        row.style.display = (statusMatch && pembayaranMatch) ? '' : 'none';
    });
}

// Inline Editing Functionality
document.addEventListener('DOMContentLoaded', function() {
    const editableFields = document.querySelectorAll('.editable-field');
    
    editableFields.forEach(field => {
        const display = field.querySelector('.field-display');
        const input = field.querySelector('.field-input');
        
        // Make field clickable
        display.addEventListener('click', function() {
            enterEditMode(field, display, input);
        });
        
        // Handle input events
        input.addEventListener('blur', function() {
            saveField(field, display, input);
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                input.blur(); // This will trigger the blur event
            } else if (e.key === 'Escape') {
                cancelEdit(field, display, input);
            }
        });
    });
});

function enterEditMode(field, display, input) {
    display.classList.add('hidden');
    input.classList.remove('hidden');
    input.focus();
    input.select();
}

function exitEditMode(field, display, input) {
    display.classList.remove('hidden');
    input.classList.add('hidden');
}

function cancelEdit(field, display, input) {
    // Reset input to original value
    const originalValue = display.textContent.trim();
    if (field.dataset.field === 'biaya') {
        // Extract number from "Rp 1,000,000" format
        const numericValue = originalValue.replace(/[^\d]/g, '');
        input.value = numericValue;
    } else if (field.dataset.field === 'nomor_kontainer') {
        // Extract from code element
        input.value = display.querySelector('code').textContent.trim();
    } else {
        input.value = originalValue;
    }
    exitEditMode(field, display, input);
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
    
    // For biaya field, ensure we send the full numeric value
    if (fieldName === 'biaya') {
        // Convert to number to remove any formatting issues
        const numericValue = parseFloat(newValue);
        if (isNaN(numericValue) || numericValue < 0) {
            alert('Nilai biaya harus berupa angka yang valid');
            input.focus();
            return;
        }
        newValue = numericValue.toString();
    }
    
    // Debug logging
    console.log('Saving field:', {
        field: fieldName,
        rawInputValue: input.value,
        trimmedValue: input.value.trim(),
        processedValue: newValue,
        recordId: recordId
    });
    
    // Show loading state
    const originalDisplayContent = display.innerHTML;
    display.innerHTML = '<i class="fas fa-spinner fa-spin text-blue-500"></i> Menyimpan...';
    exitEditMode(field, display, input);
    
    // Send AJAX request
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
        console.log('Server response:', data);
        if (data.success) {
            // Update display with new value - for biaya use the raw value, not formatted
            if (fieldName === 'biaya') {
                // Get the actual saved value from server response or use the sent value
                const actualValue = data.raw_value || newValue;
                updateDisplayValue(display, fieldName, actualValue);
            } else {
                updateDisplayValue(display, fieldName, data.formatted_value || newValue);
            }
            
            // Show success message briefly
            showNotification('Data berhasil diperbarui', 'success');
        } else {
            throw new Error(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        display.innerHTML = originalDisplayContent;
        showNotification(error.message || 'Gagal menyimpan data', 'error');
        
        // Re-enter edit mode to allow user to fix
        setTimeout(() => {
            enterEditMode(field, display, input);
        }, 100);
    });
}

function updateDisplayValue(display, fieldName, value) {
    console.log('Updating display value:', {
        fieldName: fieldName,
        value: value,
        valueType: typeof value
    });
    
    if (fieldName === 'biaya') {
        // Ensure value is a number before formatting
        const numericValue = parseFloat(value);
        const formattedValue = formatNumber(numericValue);
        console.log('Biaya formatting:', {
            originalValue: value,
            numericValue: numericValue,
            formattedValue: formattedValue
        });
        display.innerHTML = `Rp ${formattedValue}`;
    } else if (fieldName === 'nomor_kontainer') {
        display.innerHTML = `<code class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-mono">${value}</code>`;
    } else {
        display.textContent = value;
    }
    
    // Add success highlight animation
    display.classList.add('field-success');
    setTimeout(() => {
        display.classList.remove('field-success');
    }, 2000);
}

function formatNumber(num) {
    // Ensure we have a valid number
    if (isNaN(num)) {
        console.warn('Invalid number for formatting:', num);
        return '0';
    }
    return new Intl.NumberFormat('id-ID').format(num);
}

function showNotification(message, type = 'info') {
    // Create notification element
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
    
    // Show notification
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Hide notification after 3 seconds
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