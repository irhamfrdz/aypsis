@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Tambah Pembayaran Pranota</h2>
            <p class="text-sm text-gray-500">Input realisasi pembayaran untuk vendor supir</p>
        </div>
        <a href="{{ route('pembayaran-pranota-invoice-vendor-supir.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <form action="{{ route('pembayaran-pranota-invoice-vendor-supir.store') }}" method="POST" class="space-y-6" id="paymentForm">
        @csrf
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-semibold text-gray-800">Informasi Pembayaran</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Vendor Selection -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Vendor</label>
                    <select name="vendor_id" id="vendor_id" onchange="window.location.href='{{ route('pembayaran-pranota-invoice-vendor-supir.create') }}?vendor_id=' + this.value" class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">-- Pilih Vendor --</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ $selectedVendorId == $vendor->id ? 'selected' : '' }}>{{ $vendor->nama_vendor }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Pembayaran</label>
                    <input type="text" name="nomor_pembayaran" value="{{ $nomorPembayaran }}" readonly class="block w-full bg-gray-50 border border-gray-200 rounded-lg text-sm px-3 py-2 text-gray-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                    <input type="date" name="tanggal_pembayaran" value="{{ date('Y-m-d') }}" required class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                    <select name="metode_pembayaran" required class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2">
                        <option value="transfer">Transfer Bank</option>
                        <option value="cash">Tunai / Kas</option>
                        <option value="cheque">Cek / Giro</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank (Jika Transfer)</label>
                    <input type="text" name="bank" placeholder="Contoh: BCA, Mandiri" class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Referensi</label>
                    <input type="text" name="no_referensi" placeholder="E-banking ref, No Cek, dll" class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="2" class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2" placeholder="Catatan opsional..."></textarea>
                </div>
            </div>
        </div>

        @if($selectedVendorId)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Daftar Pranota Unpaid</h3>
                <span class="text-xs text-gray-500">Pilih pranota yang akan dibayar</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 w-10">
                                <input type="checkbox" id="selectAll" class="rounded text-emerald-600 focus:ring-emerald-500">
                            </th>
                            <th class="px-6 py-3">No. Pranota</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3 text-right">Total Tagihan</th>
                            <th class="px-6 py-3 text-right">Nominal Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pranotas as $pranota)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="pranota_ids[]" value="{{ $pranota->id }}" class="pranota-checkbox rounded text-emerald-600 focus:ring-emerald-500" data-total="{{ $pranota->total_nominal }}">
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $pranota->no_pranota }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($pranota->total_nominal, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right">
                                <input type="number" name="nominal_bayar[{{ $pranota->id }}]" value="{{ (int)$pranota->total_nominal }}" class="nominal-input block w-full text-right border-gray-200 rounded-md text-xs py-1" step="0.01">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">Tidak ada pranota yang belum lunas untuk vendor ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary & Action -->
        <div class="bg-emerald-50 rounded-xl p-6 border border-emerald-100 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0 text-emerald-900">
            <div>
                <div class="text-sm font-medium opacity-75">Tentukan Total yang Dibayar</div>
                <div class="text-3xl font-bold" id="displayTotal">Rp 0</div>
                <input type="hidden" name="total_pembayaran" id="total_pembayaran" value="0">
            </div>
            <button type="submit" class="w-full md:w-auto px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition-all transform active:scale-95 disabled:opacity-50" id="submitBtn" disabled>
                SIMPAN PEMBAYARAN
            </button>
        </div>
        @endif
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.pranota-checkbox');
        const nominalInputs = document.querySelectorAll('.nominal-input');
        const selectAll = document.getElementById('selectAll');
        const displayTotal = document.getElementById('displayTotal');
        const inputTotal = document.getElementById('total_pembayaran');
        const submitBtn = document.getElementById('submitBtn');

        function calculateTotal() {
            let total = 0;
            let checkedCount = 0;
            checkboxes.forEach((cb, index) => {
                if (cb.checked) {
                    const nominal = parseFloat(nominalInputs[index].value) || 0;
                    total += nominal;
                    checkedCount++;
                }
            });
            
            displayTotal.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            inputTotal.value = total;
            submitBtn.disabled = checkedCount === 0 || total <= 0;
        }

        checkboxes.forEach((cb, index) => {
            cb.addEventListener('change', calculateTotal);
            nominalInputs[index].addEventListener('input', calculateTotal);
        });

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
                calculateTotal();
            });
        }
    });
</script>
@endpush
@endsection
