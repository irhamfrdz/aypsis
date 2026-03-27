@extends('layouts.app')

@section('title', 'Form Pembayaran Pranota Uang Jalan Batam')
@section('page_title', 'Form Pembayaran Pranota Uang Jalan Batam')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-4 max-w-6xl mx-auto">
        @php
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors";
            $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-sm p-2";
            $labelClasses = "block text-xs font-medium text-gray-700 mb-1";
        @endphp

        @if(session('success'))
            <div class="mb-3 p-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-3 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                <strong>Gagal!</strong> {{ session('error') }}
            </div>
        @endif

        <!-- Header dengan Filter Tanggal -->
        <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
                <h3 class="text-sm font-semibold text-gray-800">Filter Pranota Uang Jalan Batam</h3>
                <form action="{{ route('pembayaran-pranota-uang-jalan-batam.create') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                    <div class="flex gap-2">
                        <div>
                            <label class="{{ $labelClasses }}">Dari</label>
                            <input type="date" name="start_date" class="{{ $inputClasses }}" style="background-color: white;" value="{{ request('start_date') }}">
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Sampai</label>
                            <input type="date" name="end_date" class="{{ $inputClasses }}" style="background-color: white;" value="{{ request('end_date') }}">
                        </div>
                    </div>
                    <div class="flex gap-1 sm:self-end">
                        <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded text-xs transition-colors">Cari</button>
                        <a href="{{ route('pembayaran-pranota-uang-jalan-batam.create') }}" class="bg-white border text-gray-700 px-3 py-1.5 rounded text-xs transition-colors">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <form id="pembayaranForm" action="{{ route('pembayaran-pranota-uang-jalan-batam.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Data Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label class="{{ $labelClasses }}">Nomor Pembayaran <span class="text-red-500">*</span></label>
                                <input type="text" name="nomor_pembayaran" id="nomor_pembayaran" value="{{ $nomorPembayaran }}" class="{{ $readonlyInputClasses }}" readonly required>
                            </div>
                            <div>
                                <label class="{{ $labelClasses }}">Nomor Accurate</label>
                                <input type="text" name="nomor_accurate" class="{{ $inputClasses }}">
                            </div>
                            <div>
                                <label class="{{ $labelClasses }}">Tanggal Kas</label>
                                <input type="date" name="tanggal_pembayaran" value="{{ date('Y-m-d') }}" class="{{ $inputClasses }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Bank & Transaksi</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div>
                                <label class="{{ $labelClasses }}">Pilih Bank</label>
                                <select name="bank" id="bank" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Bank --</option>
                                    @foreach($akunCoa as $akun)
                                        <option value="{{ $akun->nama_akun }}">{{ $akun->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="{{ $labelClasses }}">Jenis Transaksi</label>
                                <select name="jenis_transaksi" class="{{ $inputClasses }}" required>
                                    <option value="Kredit">Kredit (Biaya +, Bank -)</option>
                                    <option value="Debit">Debit (Bank +, Biaya -)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pilih Pranota -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200 flex justify-between items-center">
                    <h4 class="text-sm font-semibold text-gray-800">Pilih Pranota Uang Jalan Batam</h4>
                    <div class="flex items-center gap-2">
                        <input type="text" id="searchPranota" placeholder="Cari..." class="px-3 py-1 text-xs border rounded w-48 font-normal">
                    </div>
                </div>
                <div class="overflow-x-auto max-h-60">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-20">
                            <tr>
                                <th class="px-2 py-2 text-left"><input type="checkbox" id="select-all"></th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Pranota</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Supir</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody id="pranotaTableBody" class="bg-white divide-y divide-gray-200">
                            @foreach ($pranotaUangJalans as $pranota)
                                <tr class="pranota-row hover:bg-gray-50 text-xs" data-search="{{ strtolower($pranota->nomor_pranota) }}">
                                    <td class="px-2 py-2"><input type="checkbox" name="pranota_uang_jalan_ids[]" value="{{ $pranota->id }}" class="pranota-checkbox" data-total="{{ $pranota->total_for_payment }}"></td>
                                    <td class="px-2 py-2 font-medium">{{ $pranota->nomor_pranota }}</td>
                                    <td class="px-2 py-2">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</td>
                                    <td class="px-2 py-2">
                                        @php
                                            $supir = $pranota->uangJalanBatams->pluck('suratJalanBatam.supir')->filter()->unique()->implode(', ');
                                        @endphp
                                        {{ $supir ?: '-' }}
                                    </td>
                                    <td class="px-2 py-2 text-right font-semibold">Rp {{ number_format($pranota->total_for_payment, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Total -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm">
                            <div>
                                <label class="{{ $labelClasses }}">Total Tagihan</label>
                                <input type="number" id="total_pembayaran" name="total_pembayaran" class="{{ $readonlyInputClasses }}" readonly value="0">
                            </div>
                            <div>
                                <label class="{{ $labelClasses }}">Penyesuaian</label>
                                <input type="number" id="total_tagihan_penyesuaian" name="total_tagihan_penyesuaian" class="{{ $inputClasses }}" value="0">
                            </div>
                            <div>
                                <label class="{{ $labelClasses }}">Total Akhir</label>
                                <input type="number" id="total_tagihan_setelah_penyesuaian" name="total_tagihan_setelah_penyesuaian" class="{{ $readonlyInputClasses }} font-bold" readonly value="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <label class="{{ $labelClasses }}">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="{{ $inputClasses }}"></textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('pembayaran-pranota-uang-jalan-batam.index') }}" class="bg-white border text-gray-700 px-4 py-2 rounded text-sm transition-colors">Batal</a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm transition-colors">Simpan Pembayaran</button>
            </div>
        </form>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.pranota-checkbox');
        const totalTagihan = document.getElementById('total_pembayaran');
        const penyesuaian = document.getElementById('total_tagihan_penyesuaian');
        const totalAkhir = document.getElementById('total_tagihan_setelah_penyesuaian');
        const searchInput = document.getElementById('searchPranota');

        function updateTotals() {
            let total = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) total += parseFloat(cb.dataset.total);
            });
            totalTagihan.value = total;
            totalAkhir.value = total + (parseFloat(penyesuaian.value) || 0);
        }

        if(selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateTotals();
            });
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateTotals));
        if(penyesuaian) penyesuaian.addEventListener('input', updateTotals);

        if(searchInput) {
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                document.querySelectorAll('.pranota-row').forEach(row => {
                    row.style.display = row.dataset.search.includes(term) ? '' : 'none';
                });
            });
        }
    });
</script>
@endsection
