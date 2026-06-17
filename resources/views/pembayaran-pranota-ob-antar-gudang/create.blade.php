@extends('layouts.app')

@section('title', 'Buat Pembayaran Pranota OB Antar Gudang')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container { width: 100% !important; }
    .select2-container .select2-selection--single {
        height: 38px !important;
        padding: 5px 10px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
        background-color: #ffffff !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 26px !important;
        font-size: 0.875rem !important;
    }
    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-teal-100">
        <!-- Header -->
        <div class="bg-teal-700 text-white px-6 py-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h5 class="text-lg font-bold flex items-center">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                        Buat Pembayaran Pranota OB Antar Gudang
                    </h5>
                    <p class="text-teal-100 text-xs mt-1">Buat record pembayaran and jurnal double-entry otomatis</p>
                </div>
                <div>
                    <a href="{{ route('pembayaran-pranota-ob-antar-gudang.index') }}"
                       class="bg-teal-600 hover:bg-teal-500 text-white border border-teal-500 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4" role="alert">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4" role="alert">
                    <ul class="list-disc list-inside text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pembayaran-pranota-ob-antar-gudang.store') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin data pembayaran sudah benar?')">
                @csrf

                <!-- Grid Form Info Utama -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="nomor_pembayaran" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nomor Pembayaran</label>
                        <input type="text" name="nomor_pembayaran" id="nomor_pembayaran" value="{{ old('nomor_pembayaran', $nomorPembayaran) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500 text-sm font-mono focus:outline-none" readonly>
                    </div>

                    <div>
                        <label for="nomor_accurate" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nomor Accurate</label>
                        <input type="text" name="nomor_accurate" id="nomor_accurate" value="{{ old('nomor_accurate') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500" placeholder="Masukkan nomor accurate (opsional)">
                    </div>

                    <div>
                        <label for="tanggal_kas" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tanggal Kas</label>
                        <input type="date" name="tanggal_kas" id="tanggal_kas" value="{{ old('tanggal_kas', now()->toDateString()) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500" required>
                    </div>

                    <div>
                        <label for="debit_kredit" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jenis Transaksi</label>
                        <select name="debit_kredit" id="debit_kredit" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-white focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500" required>
                            <option value="credit" {{ old('debit_kredit', 'credit') === 'credit' ? 'selected' : '' }}>KREDIT (Bank berkurang, Biaya bertambah)</option>
                            <option value="debit" {{ old('debit_kredit') === 'debit' ? 'selected' : '' }}>DEBIT (Bank bertambah, Biaya berkurang)</option>
                        </select>
                    </div>

                    <div>
                        <label for="akun_bank_id" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Pilih Bank/Kas (COA)</label>
                        <select name="akun_bank_id" id="akun_bank_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-white focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 coa-select" required>
                            <option value="">-- Pilih Bank --</option>
                            @foreach ($akunBank as $akun)
                                <option value="{{ $akun->id }}" {{ old('akun_bank_id') == $akun->id ? 'selected' : '' }}>
                                    {{ $akun->nomor_akun }} - {{ $akun->nama_akun }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="akun_coa_id" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Pilih Akun Biaya (COA)</label>
                        <select name="akun_coa_id" id="akun_coa_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-white focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 coa-select" required>
                            <option value="">-- Pilih Akun Biaya --</option>
                            @foreach ($akunBiaya as $akun)
                                <option value="{{ $akun->id }}" {{ old('akun_coa_id') == $akun->id ? 'selected' : '' }}>
                                    {{ $akun->nomor_akun }} - {{ $akun->nama_akun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Section Pilih Pranota -->
                <div class="border border-gray-200 rounded-lg overflow-hidden mb-6">
                    <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800">Pilih Pranota OB Antar Gudang untuk Dibayar</h4>
                    </div>
                    <div class="overflow-x-auto max-h-80">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 text-left">
                                        <input type="checkbox" id="select-all" class="h-4 w-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No. Pranota</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Item</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Grand Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pranotaList as $pranota)
                                    <tr class="hover:bg-teal-50/20">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" name="pranota_ids[]" value="{{ $pranota->id }}" class="pranota-checkbox h-4 w-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500" data-amount="{{ $pranota->grand_total }}">
                                        </td>
                                        <td class="px-4 py-3 text-xs font-semibold font-mono text-gray-950">{{ $pranota->nomor_pranota }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-900">{{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3 text-xs text-center font-bold text-gray-900">{{ $pranota->items->count() }} kontainer</td>
                                        <td class="px-4 py-3 text-xs text-right font-bold text-gray-900">Rp {{ number_format($pranota->grand_total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 text-xs italic">Tidak ada pranota OB Antar Gudang yang belum lunas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Total Pembayaran -->
                <div class="bg-teal-50 border border-teal-200 rounded-lg p-5 mb-6">
                    <h4 class="text-sm font-semibold text-teal-900 mb-4">Rincian Pembayaran</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="total_tagihan_display" class="block text-xs font-semibold text-teal-800 uppercase mb-1">Total Tagihan Selected</label>
                            <input type="text" id="total_tagihan_display" class="block w-full px-3 py-2 border border-teal-200 rounded-md bg-white text-teal-950 font-bold text-sm" value="Rp 0" readonly>
                        </div>
                        <div>
                            <label for="penyesuaian" class="block text-xs font-semibold text-teal-800 uppercase mb-1">Penyesuaian (Adjustment)</label>
                            <input type="number" name="penyesuaian" id="penyesuaian" class="block w-full px-3 py-2 border border-teal-200 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-500" value="0" step="any">
                        </div>
                        <div>
                            <label for="total_setelah_penyesuaian_display" class="block text-xs font-semibold text-teal-800 uppercase mb-1">Grand Total Bayar</label>
                            <input type="text" id="total_setelah_penyesuaian_display" class="block w-full px-3 py-2 border border-teal-200 rounded-md bg-teal-100 text-teal-950 font-bold text-sm" value="Rp 0" readonly>
                        </div>
                    </div>
                </div>

                <!-- Alasan & Keterangan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="alasan_penyesuaian" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Alasan Penyesuaian</label>
                        <textarea name="alasan_penyesuaian" id="alasan_penyesuaian" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500" placeholder="Jelaskan alasan jika ada penyesuaian/selisih nominal..."></textarea>
                    </div>
                    <div>
                        <label for="keterangan" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500" placeholder="Catatan tambahan pembayaran..."></textarea>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex justify-end gap-2">
                    <button type="submit" class="bg-teal-600 hover:bg-teal-500 text-white border border-teal-500 px-6 py-2.5 rounded-md text-sm font-semibold transition duration-150 ease-in-out shadow-sm">
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.pranota-checkbox');
        const totalTagihanDisplay = document.getElementById('total_tagihan_display');
        const penyesuaianInput = document.getElementById('penyesuaian');
        const totalSetelahPenyesuaianDisplay = document.getElementById('total_setelah_penyesuaian_display');

        function formatRupiah(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        function recalculate() {
            let total = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    total += parseFloat(cb.getAttribute('data-amount')) || 0;
                }
            });

            const penyesuaian = parseFloat(penyesuaianInput.value) || 0;
            const grandTotal = total + penyesuaian;

            totalTagihanDisplay.value = formatRupiah(total);
            totalSetelahPenyesuaianDisplay.value = formatRupiah(grandTotal);
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
                recalculate();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                if (!cb.checked && selectAll) {
                    selectAll.checked = false;
                }
                recalculate();
            });
        });

        penyesuaianInput.addEventListener('input', recalculate);

        // Initialize Select2 directly since it is loaded in layouts/app.blade.php
        if (typeof $.fn.select2 !== 'undefined') {
            $('.coa-select').select2({
                width: '100%',
                placeholder: '-- Pilih --',
                allowClear: true
            });
        }

        // Run initially
        recalculate();
    });
</script>
@endpush
@endsection
