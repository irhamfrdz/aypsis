@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-900">Daftar Pranota</h1>
            <a href="{{ route('daftar-tagihan-kontainer-sewa.index') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Pranota Baru
            </a>
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

            <!-- Bulk Actions -->
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" onchange="toggleAllCheckboxes()">
                        <span class="ml-2 text-sm text-gray-700">Pilih Semua</span>
                    </label>
                    <span id="selectedCount" class="text-sm text-gray-500">0 pranota dipilih</span>
                </div>
                <div class="flex space-x-2">
                    <button id="processPembayaranBtn"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled
                            onclick="processPembayaranBatch()">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Proses Pembayaran
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAllHeader" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" onchange="toggleAllCheckboxes()">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pranota</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pranota</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Tagihan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pembayaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pembayaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
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
                                <a href="{{ route('pranota.show', $pranota->id) }}"
                                   class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    {{ $pranota->no_invoice }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $pranota->tanggal_pranota->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $pranota->jumlah_tagihan }} item
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Rp {{ number_format($pranota->total_amount, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pranota->getSimplePaymentStatusColor() }}">
                                    {{ $pranota->getSimplePaymentStatus() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($pranota->status === 'paid' && $pranota->getPaymentDate())
                                    {{ $pranota->getPaymentDate()->format('d/m/Y') }}
                                @elseif($pranota->status === 'paid')
                                    <span class="text-green-600">Dibayar</span>
                                @else
                                    <span class="text-gray-500">Belum dibayar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <!-- View Button -->
                                    <a href="{{ route('pranota.show', $pranota->id) }}"
                                       class="text-indigo-600 hover:text-indigo-900">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    <!-- Print Button -->
                                    <a href="{{ route('pranota.print', $pranota->id) }}" target="_blank"
                                       class="text-blue-600 hover:text-blue-900"
                                       title="Print Pranota">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data pranota</h3>
                                    <p class="text-gray-500 mb-4">Mulai dengan membuat pranota pertama Anda.</p>
                                    <a href="{{ route('daftar-tagihan-kontainer-sewa.index') }}"
                                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors duration-150">
                                        Buat Pranota Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($pranotaList->hasPages())
            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-6">
                <div class="flex flex-1 justify-between sm:hidden">
                    @if($pranotaList->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-md">
                            Previous
                        </span>
                    @else
                        <a href="{{ $pranotaList->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Previous
                        </a>
                    @endif

                    @if($pranotaList->hasMorePages())
                        <a href="{{ $pranotaList->nextPageUrl() }}" class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Next
                        </a>
                    @else
                        <span class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-md">
                            Next
                        </span>
                    @endif
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ $pranotaList->firstItem() ?? 0 }}</span>
                            to
                            <span class="font-medium">{{ $pranotaList->lastItem() ?? 0 }}</span>
                            of
                            <span class="font-medium">{{ $pranotaList->total() }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        {{ $pranotaList->links() }}
                    </div>
                </div>
            </div>
            @endif
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

    // Enable/disable process payment button
    const processBtn = document.getElementById('processPembayaranBtn');
    processBtn.disabled = selectedCount === 0;

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

function processPembayaranBatch() {
    const checkboxes = document.querySelectorAll('.pranota-checkbox:checked');

    if (checkboxes.length === 0) {
        alert('Silakan pilih pranota yang akan diproses pembayarannya.');
        return;
    }

    const selectedPranota = Array.from(checkboxes).map(checkbox => ({
        id: checkbox.value,
        no_invoice: checkbox.dataset.noInvoice,
        amount: parseFloat(checkbox.dataset.amount)
    }));

    const totalAmount = selectedPranota.reduce((sum, pranota) => sum + pranota.amount, 0);

    const confirmation = confirm(
        `Anda akan memproses pembayaran untuk ${selectedPranota.length} pranota:\n\n` +
        selectedPranota.map(p => `- ${p.no_invoice} (Rp ${new Intl.NumberFormat('id-ID').format(p.amount)})`).join('\n') +
        `\n\nTotal Amount: Rp ${new Intl.NumberFormat('id-ID').format(totalAmount)}\n\n` +
        'Lanjutkan ke halaman pembayaran?'
    );

    if (confirmation) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("pembayaran-pranota-kontainer.create") }}';

        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        // Add selected pranota IDs
        selectedPranota.forEach(pranota => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'pranota_ids[]';
            input.value = pranota.id;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Add change listeners to existing checkboxes
    document.querySelectorAll('.pranota-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelection);
    });

    // Initial update
    updateSelection();
});
</script>
@endsection
