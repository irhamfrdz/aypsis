@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-900">Form Pembayaran Pranota Supir</h1>
            <a href="{{ route('pranota.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
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

            <form action="{{ route('pranota.process.payment') }}" method="POST">
                @csrf

                <!-- Form Fields Row 1 -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <!-- Nomor Pembayaran -->
                    <div>
                        <label for="nomor_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Pembayaran
                        </label>
                        <input type="text"
                               id="nomor_pembayaran"
                               name="nomor_pembayaran"
                               value="{{ $nomorPembayaran }}"
                               readonly
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Nomor Cetakan -->
                    <div>
                        <label for="nomor_cetakan" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Cetakan
                        </label>
                        <input type="number"
                               id="nomor_cetakan"
                               name="nomor_cetakan"
                               value="1"
                               min="1"
                               max="9"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Pilih Bank -->
                    <div>
                        <label for="bank" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih Bank
                        </label>
                        <select id="bank"
                                name="bank"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Pilih Bank --</option>
                            <option value="BCA">BCA</option>
                            <option value="BNI">BNI</option>
                            <option value="BRI">BRI</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="CIMB">CIMB Niaga</option>
                            <option value="Danamon">Bank Danamon</option>
                            <option value="Permata">Bank Permata</option>
                            <option value="BJB">Bank BJB</option>
                        </select>
                    </div>

                    <!-- Jenis Transaksi -->
                    <div>
                        <label for="jenis_transaksi" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Transaksi
                        </label>
                        <select id="jenis_transaksi"
                                name="jenis_transaksi"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Transfer">Transfer</option>
                            <option value="Tunai">Tunai</option>
                            <option value="Cek">Cek</option>
                            <option value="Giro">Giro</option>
                        </select>
                    </div>
                </div>

                <!-- Form Fields Row 2 -->
                <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mb-6">
                    <!-- Tanggal Kas -->
                    <div class="md:col-span-1">
                        <label for="tanggal_kas" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Kas
                        </label>
                        <input type="date"
                               id="tanggal_kas"
                               name="tanggal_kas"
                               value="{{ date('Y-m-d') }}"
                               class="block w-full md:w-1/4 px-3 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <!-- Alasan Penyesuaian -->
                <div class="mb-6">
                    <label for="alasan_penyesuaian" class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Penyesuaian
                    </label>
                    <textarea id="alasan_penyesuaian"
                              name="alasan_penyesuaian"
                              rows="4"
                              placeholder="Masukkan alasan penyesuaian jika ada..."
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <!-- Keterangan -->
                <div class="mb-6">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan
                    </label>
                    <textarea id="keterangan"
                              name="keterangan"
                              rows="4"
                              placeholder="Masukkan keterangan tambahan..."
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <!-- Pilih Pranota Supir -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pilih Pranota Supir</h3>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="selectAllPranota" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" onchange="toggleAllPranotaSelection()">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Pranota</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if($pranotaList->count() > 0)
                                    @foreach($pranotaList as $pranota)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox"
                                                   name="selected_pranota_ids[]"
                                                   value="{{ $pranota->id }}"
                                                   checked
                                                   class="pranota-selection rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                   onchange="updateTotalCalculation()">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $pranota->no_invoice }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $pranota->tanggal_pranota->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $pranota->keterangan ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            <span class="pranota-amount" data-amount="{{ $pranota->total_amount }}">
                                                Rp {{ number_format($pranota->total_amount, 2, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($pranota->status == 'paid')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Lunas
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Belum Lunas
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            Tidak ada pranota supir yang tersedia.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-sm text-gray-600">
                        * Anda dapat memilih satu atau lebih pranota supir untuk dibayar.
                    </div>
                </div>

                <!-- Summary -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Pembayaran</label>
                            <div class="text-2xl font-bold text-gray-900" id="totalPembayaran">
                                Rp {{ number_format($totalPembayaran, 2, ',', '.') }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Penyesuaian</label>
                            <input type="number"
                                   id="penyesuaian"
                                   name="penyesuaian"
                                   value="0"
                                   step="0.01"
                                   onchange="updateTotalCalculation()"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Pembayaran setelah Penyesuaian</label>
                            <div class="text-2xl font-bold text-indigo-600" id="totalSetelahPenyesuaian">
                                Rp {{ number_format($totalPembayaran, 2, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg transition-colors duration-150 flex items-center text-lg font-medium">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleAllPranotaSelection() {
    const selectAll = document.getElementById('selectAllPranota');
    const checkboxes = document.querySelectorAll('.pranota-selection');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });

    updateTotalCalculation();
}

function updateTotalCalculation() {
    const selectedCheckboxes = document.querySelectorAll('.pranota-selection:checked');
    const penyesuaian = parseFloat(document.getElementById('penyesuaian').value) || 0;

    let totalPembayaran = 0;

    selectedCheckboxes.forEach(checkbox => {
        const row = checkbox.closest('tr');
        const amountElement = row.querySelector('.pranota-amount');
        const amount = parseFloat(amountElement.dataset.amount) || 0;
        totalPembayaran += amount;
    });

    const totalSetelahPenyesuaian = totalPembayaran + penyesuaian;

    document.getElementById('totalPembayaran').textContent =
        'Rp ' + totalPembayaran.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    document.getElementById('totalSetelahPenyesuaian').textContent =
        'Rp ' + totalSetelahPenyesuaian.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateTotalCalculation();
});
</script>
@endsection
