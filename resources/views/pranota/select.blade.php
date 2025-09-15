@extends('layouts.app')

@                            <div class="flex justify-between items-start mb-4">
                                <h4 class="font-semibold text-gray-900">{{ $pranota->no_invoice }}</h4>
                                <span class="px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 rounded-full">
                                    {{ $pranota->status }}
                                </span>
                            </div>n('title', 'Pilih Pranota untuk Pembayaran')
@section('page_title', 'Pilih Pranota untuk Pembayaran')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Pilih Pranota untuk Pembayaran</h2>

    @if($pranotaList->count() > 0)
    <form action="{{ route('pranota.payment.form') }}" method="GET" id="payment-form">
        <div class="mb-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-blue-800 font-medium">Pilih pranota yang belum lunas untuk diproses pembayaran</span>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($pranotaList as $pranota)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" name="pranota_ids[]" value="{{ $pranota->id }}"
                               class="mt-1 mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900">{{ $pranota->nomor_pranota }}</h4>
                                <span class="px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 rounded-full">
                                    {{ $pranota->status }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600">
                                <div>
                                    <span class="font-medium">No Invoice:</span>
                                    <br>{{ $pranota->no_invoice }}
                                </div>
                                <div>
                                    <span class="font-medium">Tanggal:</span>
                                    <br>{{ $pranota->created_at->format('d/m/Y') }}
                                </div>
                                <div>
                                    <span class="font-medium">Total:</span>
                                    <br><span class="font-semibold text-green-600">Rp {{ number_format((float)$pranota->total_amount, 0, ',', '.') }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Items:</span>
                                    <br>{{ $pranota->jumlah_tagihan }} item(s)
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            <div class="text-sm text-gray-600">
                <span id="selected-count">0</span> pranota dipilih |
                Total: <span id="selected-total" class="font-semibold text-green-600">Rp 0</span>
            </div>
            <div class="space-x-3">
                <a href="{{ route('pranota.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                    Kembali
                </a>
                <button type="submit" id="proceed-button" disabled
                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-6 py-2 rounded-lg transition-colors">
                    Lanjut ke Pembayaran
                </button>
            </div>
        </div>
    </form>
    @else
    <div class="text-center py-12">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-xl font-medium text-gray-900 mb-2">Tidak Ada Pranota yang Belum Lunas</h3>
        <p class="text-gray-600 mb-6">Semua pranota sudah lunas atau tidak ada pranota yang tersedia.</p>
        <a href="{{ route('pranota.index') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
            Kembali ke Daftar Pranota Kontainer
        </a>
    </div>
    @endif
</div>

@if($pranotaList->count() > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="pranota_ids[]"]');
    const selectedCount = document.getElementById('selected-count');
    const selectedTotal = document.getElementById('selected-total');
    const proceedButton = document.getElementById('proceed-button');

    // Pranota amounts for calculation
    const pranotaAmounts = {
        @foreach($pranotaList as $pranota)
        {{ $pranota->id }}: {{ $pranota->total_amount }},
        @endforeach
    };

    function updateSelection() {
        const selected = Array.from(checkboxes).filter(cb => cb.checked);
        const count = selected.length;
        const total = selected.reduce((sum, cb) => {
            return sum + (pranotaAmounts[cb.value] || 0);
        }, 0);

        selectedCount.textContent = count;
        selectedTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
        proceedButton.disabled = count === 0;
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelection);
    });

    // Initial update
    updateSelection();
});
</script>
@endif
@endsection
