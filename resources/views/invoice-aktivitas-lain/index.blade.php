@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Invoice Aktivitas Lain</h1>
                <p class="text-gray-600 mt-1">Kelola invoice untuk pembayaran aktivitas lain</p>
            </div>
            @can('invoice-aktivitas-lain-create')
            <div>
                <a href="{{ route('invoice-aktivitas-lain.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Invoice
                </a>
            </div>
            @endcan
        </div>
    </div>

    <!-- Bulk Actions Section -->
    <div id="bulkActions" class="hidden mb-6 bg-blue-50 border border-blue-200 rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-700">
                    <span id="selected-count">0</span> invoice dipilih
                </span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" 
                        id="btnBulkPranota"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Masukan ke Pranota
                </button>
                <button type="button" 
                        id="btnBulkDelete"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Hapus Terpilih
                </button>
                <button type="button" 
                        id="btnCancelSelection"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-lg transition">
                    Batal
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" action="{{ route('invoice-aktivitas-lain.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Invoice</label>
                <input type="text" name="nomor_invoice" value="{{ request('nomor_invoice') }}" 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Cari nomor invoice...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Pranota</label>
                <input type="text" name="nomor_pranota" value="{{ request('nomor_pranota') }}" 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Cari nomor pranota...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Aktivitas</label>
                <input type="text" name="jenis_aktivitas" value="{{ request('jenis_aktivitas') }}" 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Cari jenis aktivitas...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                    Filter
                </button>
                <a href="{{ route('invoice-aktivitas-lain.index') }}" 
                   class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nomor Invoice
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Invoice
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jenis Aktivitas
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            BL / Klasifikasi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Penerima
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $invoice->id }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $invoice->nomor_invoice }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $invoice->tanggal_invoice->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $invoice->jenis_aktivitas ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $blDetails = $invoice->bl_details_array;
                                @endphp
                                @if(count($blDetails) > 0)
                                    <div class="text-sm font-medium text-gray-900">
                                        BL: {{ $blDetails[0]['nomor_bl'] ?? '-' }}
                                        @if(isset($blDetails[0]['nomor_kontainer']) && $blDetails[0]['nomor_kontainer'])
                                            <div class="text-xs text-gray-500">{{ $blDetails[0]['nomor_kontainer'] }}</div>
                                        @endif
                                    </div>
                                    @if(count($blDetails) > 1)
                                        <div class="text-xs text-blue-600 mt-1">+{{ count($blDetails) - 1 }} BL lainnya</div>
                                    @endif
                                @elseif($invoice->klasifikasiBiaya && isset($invoice->klasifikasiBiaya->nama))
                                    <div class="text-sm text-gray-900">{{ $invoice->klasifikasiBiaya->nama }}</div>
                                    @php
                                        $barangCount = count(json_decode($invoice->barang_detail, true) ?? []);
                                    @endphp
                                    @if($barangCount > 0)
                                        <div class="text-xs text-gray-500">{{ $barangCount }} barang</div>
                                    @endif
                                @elseif($invoice->klasifikasiBiayaUmum && isset($invoice->klasifikasiBiayaUmum->nama))
                                    <div class="text-sm text-gray-900">{{ $invoice->klasifikasiBiayaUmum->nama }}</div>
                                    @php
                                        $barangCount = count(json_decode($invoice->barang_detail, true) ?? []);
                                    @endphp
                                    @if($barangCount > 0)
                                        <div class="text-xs text-gray-500">{{ $barangCount }} barang</div>
                                    @endif
                                @else
                                    <div class="text-sm text-gray-500">-</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $invoice->penerima ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    // Check if this is electricity invoice
                                    $isListrikInvoice = $invoice->klasifikasiBiayaUmum && 
                                                        str_contains(strtolower($invoice->klasifikasiBiayaUmum->nama ?? ''), 'listrik');
                                    
                                    // For electricity invoices, sum grand_total from all listrik entries
                                    if ($isListrikInvoice && $invoice->biayaListrik && $invoice->biayaListrik->isNotEmpty()) {
                                        $displayTotal = $invoice->biayaListrik->sum('grand_total');
                                    } else {
                                        $displayTotal = $invoice->total ?? 0;
                                    }
                                @endphp
                                <div class="text-sm font-medium text-gray-900">Rp {{ number_format($displayTotal, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-gray-100 text-gray-800',
                                        'submitted' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-blue-100 text-blue-800',
                                        'paid' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusLabels = [
                                        'draft' => 'Draft',
                                        'submitted' => 'Submitted',
                                        'approved' => 'Approved',
                                        'paid' => 'Paid',
                                        'cancelled' => 'Cancelled',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$invoice->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    @can('invoice-aktivitas-lain-view')
                                    <a href="{{ route('invoice-aktivitas-lain.show', $invoice->id) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @endcan
                                    @can('invoice-aktivitas-lain-view')
                                    <!-- Print Button - Conditional based on jenis biaya -->
                                    @php
                                        $isListrik = $invoice->klasifikasiBiayaUmum && 
                                                     str_contains(strtolower($invoice->klasifikasiBiayaUmum->nama ?? ''), 'listrik');
                                    @endphp
                                    
                                    @if($isListrik)
                                        <!-- Print Listrik (with PPH) -->
                                        <a href="{{ route('invoice-aktivitas-lain.print-listrik', $invoice->id) }}" 
                                           class="text-blue-600 hover:text-blue-900" title="Print Biaya Listrik (dengan PPH)" target="_blank">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                            </svg>
                                        </a>
                                    @elseif($invoice->klasifikasiBiaya && str_contains(strtolower($invoice->klasifikasiBiaya->nama), 'labuh tambat'))
                                        <!-- Print Labuh Tambat (dengan PPH 2%) -->
                                        <a href="{{ route('invoice-aktivitas-lain.print-labuh-tambat', $invoice->id) }}" 
                                           class="text-blue-600 hover:text-blue-900" title="Print Labuh Tambat (dengan PPH 2%)" target="_blank">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                            </svg>
                                        </a>
                                    @else
                                        <!-- Print Normal -->
                                        <a href="{{ route('invoice-aktivitas-lain.print', $invoice->id) }}" 
                                           class="text-purple-600 hover:text-purple-900" title="Print" target="_blank">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                    @endcan
                                    @can('invoice-aktivitas-lain-update')
                                    <a href="{{ route('invoice-aktivitas-lain.edit', $invoice->id) }}" 
                                       class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @endcan
                                    @can('invoice-aktivitas-lain-delete')
                                    <form action="{{ route('invoice-aktivitas-lain.destroy', $invoice->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus" 
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus invoice ini?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium">Tidak ada invoice ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($invoices->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selected-count');
    const btnCancelSelection = document.getElementById('btnCancelSelection');

    // Select all functionality
    selectAll?.addEventListener('change', function() {
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });

    // Individual checkbox change
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    // Cancel selection
    btnCancelSelection?.addEventListener('click', function() {
        selectAll.checked = false;
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateBulkActions();
    });

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (count > 0) {
            bulkActions?.classList.remove('hidden');
            if (selectedCount) selectedCount.textContent = count;
        } else {
            bulkActions?.classList.add('hidden');
        }
    }

    // Bulk delete
    document.getElementById('btnBulkDelete')?.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        if (checkedBoxes.length === 0) return;

        if (confirm(`Apakah Anda yakin ingin menghapus ${checkedBoxes.length} invoice?`)) {
            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            
            fetch('{{ route("invoice-aktivitas-lain.bulk-delete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Gagal: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus invoice.');
            });
        }
    });

    // Bulk pranota
    document.getElementById('btnBulkPranota')?.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        if (checkedBoxes.length === 0) return;

        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        // Implement bulk pranota functionality here
        console.log('Add to Pranota IDs:', ids);
    });
});
</script>
@endsection
