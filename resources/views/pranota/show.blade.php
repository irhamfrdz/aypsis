@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-900">Detail Pranota: {{ $pranota->no_invoice }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('pranota-kontainer-sewa.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
                <a href="{{ route('pranota-kontainer-sewa.print', $pranota->id) }}" target="_blank"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </a>
                @if($pranota->status == 'unpaid')
                <button type="button"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center"
                        onclick="openStatusModal()">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Ubah Status
                </button>
                @endif
            </div>
        </div>

        <!-- Alert Messages -->
        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Informasi Pranota -->
                <div class="bg-gray-50 rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Pranota</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">No. Pranota:</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $pranota->no_invoice }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Tanggal Pranota:</dt>
                                <dd class="text-sm text-gray-900">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Due Date:</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($pranota->due_date)
                                        {{ $pranota->due_date->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Status:</dt>
                                <dd class="text-sm">
                                    @if($pranota->status == 'unpaid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Belum Lunas
                                        </span>
                                    @elseif($pranota->status == 'paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Lunas
                                        </span>
                                    @elseif($pranota->status == 'cancelled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Dibatalkan
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Keterangan:</dt>
                                <dd class="text-sm text-gray-900">{{ $pranota->keterangan ?: '-' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">No. Invoice Vendor:</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $pranota->no_invoice_vendor ?: '-' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Tgl Invoice Vendor:</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($pranota->tgl_invoice_vendor)
                                        {{ $pranota->tgl_invoice_vendor->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Summary -->
                <div class="bg-blue-50 rounded-lg border border-blue-200">
                    <div class="px-6 py-4 border-b border-blue-200">
                        <h3 class="text-lg font-medium text-blue-900">Ringkasan</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-blue-700">Jumlah Tagihan:</dt>
                                <dd class="text-sm text-blue-900 font-semibold">{{ $pranota->jumlah_tagihan }} item</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-blue-700">Total Amount:</dt>
                                <dd class="text-lg font-bold text-green-600">Rp {{ number_format($pranota->total_amount, 2, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-blue-700">Dibuat:</dt>
                                <dd class="text-sm text-blue-900">{{ $pranota->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-blue-700">Diupdate:</dt>
                                <dd class="text-sm text-blue-900">{{ $pranota->updated_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Daftar Tagihan -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Tagihan dalam Pranota</h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="selectAllTagihan" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" onchange="toggleAllTagihan()">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Masa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarif</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DPP</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grand Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($tagihanItems as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox"
                                               class="tagihan-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                               value="{{ $item->id }}"
                                               data-amount="{{ $item->grand_total }}"
                                               data-vendor="{{ $item->vendor }}"
                                               onchange="updateTagihanSelection()">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->vendor }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $item->nomor_kontainer }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->size }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->periode }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->masa }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($item->tarif)
                                            @if(strtolower($item->tarif) == 'harian')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Harian
                                                </span>
                                            @elseif(strtolower($item->tarif) == 'bulanan')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Bulanan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $item->tarif }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Rp {{ number_format($item->dpp ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Rp {{ number_format($item->grand_total, 2, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                                        Tidak ada tagihan ditemukan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($tagihanItems->count() > 0)
                            <tfoot class="bg-blue-50">
                                <tr>
                                    <th colspan="9" class="px-6 py-3 text-right text-sm font-medium text-blue-900">Total:</th>
                                    <th class="px-6 py-3 text-left text-sm font-bold text-green-600">
                                        Rp {{ number_format($tagihanItems->sum('grand_total'), 2, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                    <!-- Selected Items Summary & Actions -->
                    <div id="selectedItemsSummary" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg hidden">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                            <div class="flex items-center space-x-4">
                                <div class="text-sm text-blue-800">
                                    <span id="selectedTagihanCount" class="font-semibold">0</span> tagihan dipilih
                                    (<span id="selectedTotalAmount" class="font-bold text-green-600">Rp 0</span>)
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button type="button"
                                        id="lepasKontainerBtn"
                                        onclick="lepasKontainer()"
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm font-medium">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Lepas Kontainer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Modal -->
@if($pranota->status == 'unpaid')
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ubah Status Pranota</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeStatusModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="text-sm text-gray-700 mb-6">
                Pilih status baru untuk pranota <strong>{{ $pranota->no_invoice }}</strong>:
            </p>
            <div class="grid grid-cols-1 gap-3">
                <form action="{{ route('pranota-kontainer-sewa.update.status', $pranota->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="sent">
                    <button type="submit"
                            class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center justify-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Kirim
                    </button>
                </form>
                <form action="{{ route('pranota-kontainer-sewa.update.status', $pranota->id) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pranota ini?')">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit"
                            class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center justify-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batalkan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<script>
function toggleAllTagihan() {
    const selectAll = document.getElementById('selectAllTagihan');
    const checkboxes = document.querySelectorAll('.tagihan-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });

    updateTagihanSelection();
}

function updateTagihanSelection() {
    const checkboxes = document.querySelectorAll('.tagihan-checkbox:checked');
    const selectedCount = checkboxes.length;
    const totalAmount = Array.from(checkboxes).reduce((sum, checkbox) => {
        return sum + parseFloat(checkbox.dataset.amount);
    }, 0);

    // Update select all checkbox state
    const selectAll = document.getElementById('selectAllTagihan');
    const allCheckboxes = document.querySelectorAll('.tagihan-checkbox');

    if (selectedCount === 0) {
        selectAll.indeterminate = false;
        selectAll.checked = false;
    } else if (selectedCount === allCheckboxes.length) {
        selectAll.indeterminate = false;
        selectAll.checked = true;
    } else {
        selectAll.indeterminate = true;
        selectAll.checked = false;
    }

    // Update selected items summary
    const summaryDiv = document.getElementById('selectedItemsSummary');
    const countSpan = document.getElementById('selectedTagihanCount');
    const amountSpan = document.getElementById('selectedTotalAmount');

    if (selectedCount > 0) {
        summaryDiv.classList.remove('hidden');
        countSpan.textContent = selectedCount;
        amountSpan.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalAmount);
    } else {
        summaryDiv.classList.add('hidden');
        countSpan.textContent = '0';
        amountSpan.textContent = 'Rp 0';
    }
}

function lepasKontainer() {
    const checkboxes = document.querySelectorAll('.tagihan-checkbox:checked');
    const selectedItems = Array.from(checkboxes).map(checkbox => ({
        id: checkbox.value,
        vendor: checkbox.dataset.vendor,
        amount: checkbox.dataset.amount
    }));

    if (selectedItems.length === 0) {
        alert('Silakan pilih tagihan yang akan dilepas kontainernya.');
        return;
    }

    const totalAmount = selectedItems.reduce((sum, item) => sum + parseFloat(item.amount), 0);
    const confirmation = confirm(
        `Anda akan melepas kontainer untuk ${selectedItems.length} tagihan:\n\n` +
        selectedItems.map(item => `- ${item.vendor} (Rp ${new Intl.NumberFormat('id-ID').format(item.amount)})`).join('\n') +
        `\n\nTotal Amount: Rp ${new Intl.NumberFormat('id-ID').format(totalAmount)}\n\n` +
        'Apakah Anda yakin ingin melanjutkan?'
    );

    if (confirmation) {
        // Create JSON payload
        const payload = {
            _token: '{{ csrf_token() }}',
            tagihan_ids: selectedItems.map(item => item.id)
        };

        // Send POST request
        fetch(`{{ route('pranota-kontainer-sewa.lepas-kontainer', $pranota->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                alert('Kontainer berhasil dilepas dari pranota.');
                location.reload(); // Reload page to show updated data
            } else {
                alert('Gagal melepas kontainer: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses permintaan.');
        });
    }
}

function openStatusModal() {
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('statusModal');
    if (event.target == modal) {
        closeStatusModal();
    }
}
</script>
@endsection
