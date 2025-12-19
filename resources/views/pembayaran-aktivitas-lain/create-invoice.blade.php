@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">Pembayaran Aktivitas Lain - Dari Invoice</h1>
                <p class="text-sm text-gray-600 mt-1">Pilih satu atau lebih invoice untuk dibayar</p>
            </div>
            <a href="{{ route('pembayaran-aktivitas-lain.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium text-xs rounded-md transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-red-800 mb-2">Terdapat Error pada Form</h3>
                        <ul class="text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('pembayaran-aktivitas-lain.store-invoice') }}" method="POST" class="p-6 space-y-6" id="invoice_payment_form">
            @csrf

            <!-- Filter Section -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h4 class="text-sm font-semibold text-gray-800 mb-3">Filter Invoice</h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Dari</label>
                        <input type="date" id="filter_tanggal_dari" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Sampai</label>
                        <input type="date" id="filter_tanggal_sampai" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jenis Aktivitas</label>
                        <select id="filter_jenis" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="">Semua</option>
                            <option value="service">Service</option>
                            <option value="sewa">Sewa</option>
                            <option value="pembelian">Pembelian</option>
                            <option value="lain-lain">Lain-lain</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select id="filter_status" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="">Semua</option>
                            <option value="draft">Draft</option>
                            <option value="approved">Approved</option>
                            <option value="unpaid">Belum Dibayar</option>
                            <option value="partial">Dibayar Sebagian</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3 flex gap-2">
                    <button type="button" onclick="applyFilter()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition">
                        Terapkan Filter
                    </button>
                    <button type="button" onclick="resetFilter()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-md transition">
                        Reset
                    </button>
                </div>
            </div>

            <!-- Invoice Selection Table -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h4 class="text-sm font-semibold text-gray-800">Daftar Invoice</h4>
                    <div class="flex items-center gap-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="select-all-invoices" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Pilih Semua</span>
                        </label>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Invoice</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Aktivitas</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sub Jenis</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Invoice</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="invoice-table-body">
                            @forelse($invoices as $invoice)
                                <tr class="hover:bg-gray-50 invoice-row" 
                                    data-tanggal="{{ $invoice->tanggal_invoice->format('Y-m-d') }}"
                                    data-jenis="{{ $invoice->jenis_aktivitas }}"
                                    data-status="{{ $invoice->status }}">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <input type="checkbox" 
                                            name="selected_invoices[]" 
                                            value="{{ $invoice->id }}" 
                                            class="invoice-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            data-amount="{{ $invoice->total }}"
                                            data-penerima="{{ $invoice->penerima }}">
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $invoice->nomor_invoice }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $invoice->tanggal_invoice->format('d/M/Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $invoice->jenis_aktivitas }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $invoice->sub_jenis_kendaraan ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $invoice->penerima }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-semibold text-gray-900">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                        @if($invoice->status == 'paid')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                                        @elseif($invoice->status == 'partial')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Sebagian</span>
                                        @elseif($invoice->status == 'approved')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Approved</span>
                                        @elseif($invoice->status == 'draft')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Belum Bayar</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-sm">Tidak ada invoice yang tersedia</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                    <p class="text-xs text-gray-600">
                        * Pilih satu atau lebih invoice untuk dibayar
                    </p>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <!-- Data Pembayaran -->
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">Data Pembayaran</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Pembayaran</label>
                                <input type="text" name="nomor" id="nomor_pembayaran" readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-100" 
                                    placeholder="Akan diisi otomatis">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Accurate</label>
                                <input type="text" name="nomor_accurate" id="nomor_accurate"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50" 
                                    placeholder="Masukkan nomor dari Accurate (opsional)">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Pembayaran <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                    </div>

                    <!-- Akun & Bank -->
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">Akun & Bank</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Akun COA <span class="text-red-500">*</span></label>
                                <select name="akun_coa_id" id="akun_coa_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- Pilih Akun COA --</option>
                                    @foreach($akunCoas as $akun)
                                        <option value="{{ $akun->id }}">{{ $akun->kode_nomor }} - {{ $akun->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Akun Bank <span class="text-red-500">*</span></label>
                                <select name="akun_bank_id" id="akun_bank_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- Pilih Akun Bank --</option>
                                    @foreach($akunBanks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->kode_nomor }} - {{ $bank->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Debit/Kredit <span class="text-red-500">*</span></label>
                                <select name="debit_kredit" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- Pilih --</option>
                                    <option value="debit">Debit</option>
                                    <option value="kredit">Kredit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <!-- Ringkasan Pembayaran -->
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <h4 class="text-sm font-semibold text-blue-800 mb-3">Ringkasan Pembayaran</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">Jumlah Invoice Dipilih:</span>
                                <span class="font-semibold text-gray-900" id="selected_count">0</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">Total Invoice:</span>
                                <span class="font-semibold text-gray-900" id="total_invoices">Rp 0</span>
                            </div>
                            <div class="border-t border-blue-300 pt-2 mt-2">
                                <div class="flex justify-between text-base">
                                    <span class="font-semibold text-blue-900">Total Pembayaran:</span>
                                    <span class="font-bold text-blue-900 text-lg" id="grand_total">Rp 0</span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="jumlah" id="jumlah_hidden" value="0">
                        <input type="hidden" name="invoice_ids" id="invoice_ids_hidden" value="">
                    </div>

                    <!-- Informasi Tambahan -->
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">Informasi Tambahan</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Jenis Aktivitas</label>
                                <input type="text" name="jenis_aktivitas" id="jenis_aktivitas" readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-100"
                                    placeholder="Akan terisi otomatis dari invoice">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Penerima</label>
                                <input type="text" name="penerima" id="penerima" readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-100"
                                    placeholder="Akan terisi otomatis dari invoice">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                                <textarea name="keterangan" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
                                    placeholder="Tambahkan keterangan pembayaran..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('pembayaran-aktivitas-lain.index') }}" 
                    class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium text-sm rounded-md transition">
                    Batal
                </a>
                <button type="submit" id="submit_button"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-md transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                    Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const invoiceCheckboxes = document.querySelectorAll('.invoice-checkbox');
    const submitButton = document.getElementById('submit_button');

    // Select All Functionality
    selectAllCheckbox.addEventListener('change', function() {
        const visibleCheckboxes = Array.from(invoiceCheckboxes).filter(cb => {
            return cb.closest('tr').style.display !== 'none';
        });
        
        visibleCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateTotals();
    });

    // Individual Checkbox Change
    invoiceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateTotals();
            updateSelectAllState();
        });
    });

    // Update Select All State
    function updateSelectAllState() {
        const visibleCheckboxes = Array.from(invoiceCheckboxes).filter(cb => {
            return cb.closest('tr').style.display !== 'none';
        });
        const checkedCount = visibleCheckboxes.filter(cb => cb.checked).length;
        
        selectAllCheckbox.checked = visibleCheckboxes.length > 0 && checkedCount === visibleCheckboxes.length;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < visibleCheckboxes.length;
    }

    // Update Totals
    function updateTotals() {
        const checkedCheckboxes = Array.from(invoiceCheckboxes).filter(cb => cb.checked);
        const count = checkedCheckboxes.length;
        let total = 0;
        let jenisAktivitasSet = new Set();
        let penerimaSet = new Set();
        let invoiceIds = [];

        checkedCheckboxes.forEach(checkbox => {
            total += parseFloat(checkbox.dataset.amount || 0);
            invoiceIds.push(checkbox.value);
            
            const row = checkbox.closest('tr');
            const jenis = row.querySelector('td:nth-child(4)').textContent.trim();
            const penerima = row.querySelector('td:nth-child(6)').textContent.trim();
            
            if (jenis) jenisAktivitasSet.add(jenis);
            if (penerima) penerimaSet.add(penerima);
        });

        // Update UI
        document.getElementById('selected_count').textContent = count;
        document.getElementById('total_invoices').textContent = 'Rp ' + total.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        document.getElementById('grand_total').textContent = 'Rp ' + total.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        document.getElementById('jumlah_hidden').value = total;
        document.getElementById('invoice_ids_hidden').value = invoiceIds.join(',');

        // Update jenis aktivitas dan penerima
        if (jenisAktivitasSet.size > 0) {
            document.getElementById('jenis_aktivitas').value = Array.from(jenisAktivitasSet).join(', ');
        } else {
            document.getElementById('jenis_aktivitas').value = '';
        }

        if (penerimaSet.size > 0) {
            document.getElementById('penerima').value = Array.from(penerimaSet).join(', ');
        } else {
            document.getElementById('penerima').value = '';
        }

        // Enable/disable submit button
        submitButton.disabled = count === 0;
    }

    // Filter Functions
    window.applyFilter = function() {
        const tanggalDari = document.getElementById('filter_tanggal_dari').value;
        const tanggalSampai = document.getElementById('filter_tanggal_sampai').value;
        const jenis = document.getElementById('filter_jenis').value;
        const status = document.getElementById('filter_status').value;

        const rows = document.querySelectorAll('.invoice-row');
        
        rows.forEach(row => {
            let show = true;
            
            const rowTanggal = row.dataset.tanggal;
            const rowJenis = row.dataset.jenis;
            const rowStatus = row.dataset.status;

            if (tanggalDari && rowTanggal < tanggalDari) show = false;
            if (tanggalSampai && rowTanggal > tanggalSampai) show = false;
            if (jenis && rowJenis !== jenis) show = false;
            if (status && rowStatus !== status) show = false;

            row.style.display = show ? '' : 'none';
        });

        updateSelectAllState();
        updateTotals();
    };

    window.resetFilter = function() {
        document.getElementById('filter_tanggal_dari').value = '';
        document.getElementById('filter_tanggal_sampai').value = '';
        document.getElementById('filter_jenis').value = '';
        document.getElementById('filter_status').value = '';

        const rows = document.querySelectorAll('.invoice-row');
        rows.forEach(row => {
            row.style.display = '';
        });

        updateSelectAllState();
        updateTotals();
    };

    // Form Validation
    document.getElementById('invoice_payment_form').addEventListener('submit', function(e) {
        const checkedCount = Array.from(invoiceCheckboxes).filter(cb => cb.checked).length;
        
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 invoice untuk dibayar!');
            return false;
        }

        const akunCoa = document.getElementById('akun_coa_id').value;
        const akunBank = document.getElementById('akun_bank_id').value;

        if (!akunCoa || !akunBank) {
            e.preventDefault();
            alert('Harap lengkapi Akun COA dan Akun Bank!');
            return false;
        }

        return true;
    });

    // Initial state
    submitButton.disabled = true;
    
    // Show all invoices on load
    resetFilter();
});
</script>
@endpush
@endsection
