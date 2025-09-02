@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-8">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-8 border-b pb-4">
            Form Pembayaran Pranota Kontainer
        </h2>

        @if (session('error'))
            <div id="flash-message" class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800">
                <strong>Peringatan:</strong> {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div id="flash-message" class="mb-6 p-4 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pembayaran-pranota-kontainer.store') }}" method="POST" id="paymentForm" class="space-y-8">
            @csrf

            <!-- Hidden fields untuk pranota IDs -->
            @foreach($pranotaList as $pranota)
                <input type="hidden" name="pranota_ids[]" value="{{ $pranota->id }}">
            @endforeach

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label for="nomor_pembayaran" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Pembayaran</label>
                            <input type="text" name="nomor_pembayaran" id="nomor_pembayaran"
                                value="{{ $nomorPembayaran }}"
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5" readonly>
                        </div>
                        <div>
                            <label for="nomor_cetakan" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Cetakan</label>
                            <input type="number" name="nomor_cetakan" id="nomor_cetakan" min="1" max="9" value="1"
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5" required>
                        </div>
                    </div>
                    <div>
                        <label for="tanggal_kas" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Kas</label>
                        <input type="date" name="tanggal_kas" id="tanggal_kas"
                            value="{{ now()->toDateString() }}"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="bank" class="block text-sm font-semibold text-gray-700 mb-1">Pilih Bank</label>
                        <select name="bank" id="bank"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5" required>
                            <option value="">-- Pilih Bank --</option>
                            <option value="BCA">BCA</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="BRI">BRI</option>
                            <option value="BNI">BNI</option>
                            <option value="CIMB">CIMB</option>
                            <option value="Danamon">Danamon</option>
                            <option value="Permata">Permata</option>
                        </select>
                    </div>
                    <div>
                        <label for="jenis_transaksi" class="block text-sm font-semibold text-gray-700 mb-1">Jenis Transaksi</label>
                        <select name="jenis_transaksi" id="jenis_transaksi"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="transfer">Transfer</option>
                            <option value="tunai">Tunai</option>
                            <option value="cek">Cek</option>
                            <option value="giro">Giro</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Alasan Penyesuaian --}}
            <div>
                <label for="alasan_penyesuaian" class="block text-sm font-semibold text-gray-700 mb-1">Alasan Penyesuaian</label>
                <textarea name="alasan_penyesuaian" id="alasan_penyesuaian" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5"></textarea>
            </div>

            {{-- Keterangan --}}
            <div>
                <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5"></textarea>
            </div>

            {{-- Daftar Pranota Kontainer --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pilih Pranota Kontainer</h3>
                <div class="overflow-x-auto rounded-xl border shadow-sm">
                    <table class="min-w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3">
                                    <input type="checkbox" id="select-all" class="h-4 w-4 text-indigo-600 border-gray-300 rounded" checked disabled>
                                </th>
                                <th class="px-4 py-3">Nomor Pranota</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Customer</th>
                                <th class="px-4 py-3">Periode</th>
                                <th class="px-4 py-3 text-right">Total Biaya</th>
                                <th class="px-4 py-3">Status Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($pranotaList as $pranota)
                                <tr class="hover:bg-indigo-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <input type="checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded" checked disabled>
                                    </td>
                                    <td class="px-4 py-3">{{ $pranota->no_invoice }}</td>
                                    <td class="px-4 py-3">
                                        {{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $pranota->customer ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">{{ $pranota->periode ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right font-medium">
                                        Rp {{ number_format($pranota->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pranota->getSimplePaymentStatusColor() }}">
                                            {{ $pranota->getSimplePaymentStatus() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        Tidak ada pranota yang tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pranotaList->count() > 0)
                    <p class="mt-2 text-sm text-gray-600">
                        * Anda dapat memilih satu atau lebih pranota kontainer untuk dibayar.
                    </p>
                @endif
            </div>

            {{-- Total Pembayaran Section --}}
            <div class="bg-gray-50 p-6 rounded-xl border">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Total Pembayaran</label>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ $pranotaList->count() }}
                        </div>
                        <p class="text-sm text-gray-500">pranota dipilih</p>
                    </div>
                    <div class="text-center">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Penyesuaian</label>
                        <input type="number" step="0.01" name="penyesuaian" id="penyesuaian" value="0"
                            class="w-full text-center text-xl font-bold border-0 bg-transparent focus:ring-0 p-0" onchange="updateTotal()">
                        <p class="text-sm text-gray-500">adjustment</p>
                    </div>
                    <div class="text-center">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Total Pembayaran setelah Penyesuaian</label>
                        <div class="text-2xl font-bold text-blue-600" id="finalTotal">
                            {{ $totalPembayaran }}
                        </div>
                        <p class="text-sm text-gray-500">total final</p>
                    </div>
                </div>
            </div>

            {{-- Submit Section --}}
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('pembayaran-pranota-kontainer.create') }}"
                   class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200">
                    Kembali
                </a>
                <button type="submit"
                        class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">
                    Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateTotal() {
    const subtotal = {{ $totalPembayaran }};
    const penyesuaian = parseFloat(document.getElementById('penyesuaian').value) || 0;
    const total = subtotal + penyesuaian;

    document.getElementById('finalTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// Auto-hide flash messages
document.addEventListener('DOMContentLoaded', function() {
    const flashMessage = document.getElementById('flash-message');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.style.transition = 'opacity 0.5s';
            flashMessage.style.opacity = '0';
            setTimeout(() => flashMessage.remove(), 500);
        }, 5000);
    }
});
</script>
@endsection
