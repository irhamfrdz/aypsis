@extends('layouts.app')

@section('title', 'Form Pembayaran Biaya Kapal')
@section('page_title', 'Form Pembayaran Biaya Kapal')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-4 max-w-6xl mx-auto">
        @php
            // Definisikan kelas Tailwind untuk input yang lebih compact
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors";
            $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-sm p-2";
            $labelClasses = "block text-xs font-medium text-gray-700 mb-1";
        @endphp

        <!-- Session Messages -->
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
        @if($errors->any())
            <div class="mb-3 p-3 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mt-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Filter Section -->
        <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Filter Biaya Kapal</h3>
                </div>
                <form action="{{ route('pembayaran-biaya-kapal.create') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                    <div class="flex gap-2">
                        <div class="min-w-0">
                            <label for="start_date" class="{{ $labelClasses }}">Dari</label>
                            <input type="date" name="start_date" id="start_date" class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors" value="{{ request('start_date') }}">
                        </div>
                        <div class="min-w-0">
                            <label for="end_date" class="{{ $labelClasses }}">Sampai</label>
                            <input type="date" name="end_date" id="end_date" class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors" value="{{ request('end_date') }}">
                        </div>
                    </div>
                    @if(request('biaya_kapal_id'))
                        <input type="hidden" name="biaya_kapal_id" value="{{ request('biaya_kapal_id') }}">
                    @endif
                    <div class="flex gap-1 sm:self-end">
                        <button type="submit" class="inline-flex justify-center py-1.5 px-3 border border-transparent shadow-sm text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                            Cari
                        </button>
                        <a href="{{ route('pembayaran-biaya-kapal.create') }}" class="inline-flex justify-center py-1.5 px-3 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <form id="pembayaranForm" action="{{ route('pembayaran-biaya-kapal.store') }}" method="POST" class="space-y-3">
            @csrf

            <!-- Header Info Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Data Pembayaran -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Data Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label for="nomor_pembayaran" class="{{ $labelClasses }}">No. Pembayaran <span class="text-red-500">*</span></label>
                                <input type="text" name="nomor_pembayaran" id="nomor_pembayaran" value="{{ $nomorPembayaran }}" class="{{ $readonlyInputClasses }}" readonly>
                            </div>
                            <div>
                                <label for="nomor_accurate" class="{{ $labelClasses }}">Nomor Accurate</label>
                                <input type="text" name="nomor_accurate" id="nomor_accurate" value="{{ old('nomor_accurate') }}" class="{{ $inputClasses }}" placeholder="Ex: ACC-001">
                            </div>
                            <div>
                                <label for="tanggal_pembayaran" class="{{ $labelClasses }}">Tanggal Pembayaran</label>
                                <input type="date" name="tanggal_pembayaran" id="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', date('Y-m-d')) }}" class="{{ $inputClasses }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank & Transaksi -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Bank & Transaksi</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div>
                                <label for="bank" class="{{ $labelClasses }}">Pilih Kas/Bank <span class="text-red-500">*</span></label>
                                <select name="bank" id="bank" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Kas/Bank --</option>
                                    @foreach($akunCoa as $coa)
                                        <option value="{{ $coa->nama_akun }}" {{ old('bank') == $coa->nama_akun ? 'selected' : '' }}>{{ $coa->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="jenis_transaksi" class="{{ $labelClasses }}">Jenis Transaksi</label>
                                <select name="jenis_transaksi" id="jenis_transaksi" class="{{ $inputClasses }}" required>
                                    <option value="kredit" {{ old('jenis_transaksi', 'kredit') == 'kredit' ? 'selected' : '' }}>Kredit (Mengurangi Kas/Bank)</option>
                                    <option value="debit" {{ old('jenis_transaksi') == 'debit' ? 'selected' : '' }}>Debit (Menambah Kas/Bank)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pilih Invoice Table -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <div class="flex items-center justify-between gap-4">
                        <h4 class="text-sm font-semibold text-gray-800">Pilih Invoice Biaya Kapal</h4>
                        <div class="flex items-center gap-2 flex-1 justify-end">
                            <div class="relative w-full max-w-xs">
                                <input type="text" id="searchInvoice" placeholder="Cari nomor invoice, kapal..." class="block w-full px-8 py-1.5 text-xs border border-gray-300 rounded-md focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                    <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-[10px] text-blue-600 font-medium bg-blue-50 px-2 py-1.5 rounded whitespace-nowrap">
                                Hanya menampilkan invoice belum lunas
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-3 py-2 text-left">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-3 w-3">
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Invoice</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapal / Klasifikasi</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($biayaKapals as $biaya)
                            <tr class="hover:bg-gray-50 cursor-pointer invoice-row transition-colors">
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <input type="checkbox" name="biaya_kapal_ids[]" value="{{ $biaya->id }}" 
                                        data-amount="{{ $biaya->total_biaya ?? $biaya->nominal }}"
                                        {{ (is_array(old('biaya_kapal_ids')) && in_array($biaya->id, old('biaya_kapal_ids'))) || request('biaya_kapal_id') == $biaya->id ? 'checked' : '' }}
                                        class="invoice-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-3 w-3">
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs font-medium text-gray-900">
                                    {{ $biaya->nomor_invoice }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">
                                    {{ $biaya->tanggal ? \Carbon\Carbon::parse($biaya->tanggal)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-3 py-2 text-xs text-gray-500">
                                    <div class="font-medium text-gray-800">{{ $biaya->display_nama_kapal }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $biaya->klasifikasiBiaya->nama ?? '-' }}</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-right font-semibold text-gray-900">
                                    Rp {{ number_format($biaya->total_biaya ?? $biaya->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-xs text-gray-500">
                                    Tidak ada invoice biaya kapal yang perlu dibayar dalam periode ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Links -->
                @if($biayaKapals->hasPages())
                <div class="px-3 py-2 border-t border-gray-200 bg-gray-50">
                    {{ $biayaKapals->appends(request()->except('page'))->links() }}
                </div>
                @endif
                <div class="bg-gray-50 px-3 py-2 border-t border-gray-200 flex justify-between items-center">
                    <span class="text-xs text-gray-500 italic">* Pilih satu atau lebih invoice</span>
                    <div id="selectionStatus" class="text-xs font-medium text-indigo-600">Terpilih: 0 Invoice</div>
                </div>
            </div>

            <!-- Footer Section: Summary & Additional Info -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Keterangan & Alasan Adjustment -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Informasi Tambahan</h4>
                        <div class="space-y-3">
                            <div>
                                <label for="keterangan" class="{{ $labelClasses }}">Keterangan Umum</label>
                                <textarea name="keterangan" id="keterangan" rows="2" class="{{ $inputClasses }}" placeholder="Tambahkan keterangan pembayaran di sini...">{{ old('keterangan') }}</textarea>
                            </div>
                            <div>
                                <label for="alasan_penyesuaian" class="{{ $labelClasses }}">Alasan Penyesuaian (Jika ada)</label>
                                <textarea name="alasan_penyesuaian" id="alasan_penyesuaian" rows="2" class="{{ $inputClasses }}" placeholder="Jelaskan alasan jika ada penyesuaian nominal...">{{ old('alasan_penyesuaian') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Summary -->
                <div class="lg:col-span-2">
                    <div class="bg-indigo-50 rounded-lg p-3 border border-indigo-200">
                        <h4 class="text-sm font-semibold text-indigo-800 mb-2">Ringkasan Pembayaran</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-indigo-600 font-medium">Subtotal Terpilih:</span>
                                <span class="text-sm font-bold text-gray-800" id="displaySubtotal">Rp 0</span>
                                <input type="hidden" name="total_pembayaran" id="inputSubtotal" value="0">
                            </div>
                            <div class="flex justify-between items-center bg-white p-2 rounded border border-indigo-100">
                                <span class="text-xs text-indigo-600 font-medium">Penyesuaian:</span>
                                <div class="w-1/2">
                                    <input type="number" name="total_tagihan_penyesuaian" id="inputPenyesuaian" 
                                        value="{{ old('total_tagihan_penyesuaian', 0) }}" 
                                        class="w-full text-right py-1 px-2 text-sm border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="0">
                                </div>
                            </div>
                            <div class="border-t border-indigo-200 pt-2 flex justify-between items-end">
                                <span class="text-sm font-bold text-indigo-900 uppercase tracking-tight">Total Akhir:</span>
                                <div class="text-right">
                                    <div class="text-xl font-black text-indigo-700" id="displayTotal">Rp 0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('pembayaran-biaya-kapal.index') }}" class="inline-flex justify-center py-2 px-6 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-colors">
                    Batal
                </a>
                <button type="submit" id="submitBtn" class="inline-flex justify-center py-2 px-8 border border-transparent shadow-lg text-sm font-bold rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                    SIMPAN PEMBAYARAN
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function calculateTotal() {
        let subtotal = 0;
        let count = 0;
        
        const storedData = getStoredData();
        count = storedData.length;
        
        storedData.forEach(d => {
            subtotal += d.amount;
        });

        const penyesuaian = parseFloat($('#inputPenyesuaian').val()) || 0;
        const total = subtotal + penyesuaian;
        
        const formattedSubtotal = new Intl.NumberFormat('id-ID', { 
            style: 'currency', 
            currency: 'IDR', 
            minimumFractionDigits: 0 
        }).format(subtotal).replace('IDR', 'Rp');

        const formattedTotal = new Intl.NumberFormat('id-ID', { 
            style: 'currency', 
            currency: 'IDR', 
            minimumFractionDigits: 0 
        }).format(total).replace('IDR', 'Rp');
        
        $('#displaySubtotal').text(formattedSubtotal);
        $('#displayTotal').text(formattedTotal);
        $('#inputSubtotal').val(subtotal);
        $('#selectionStatus').text('Terpilih: ' + count + ' Invoice');

        // Toggle submit button style
        if (count > 0) {
            $('#submitBtn').removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);
        } else {
            $('#submitBtn').addClass('opacity-50 cursor-not-allowed').prop('disabled', true);
        }
    }

    // Listener for adjustment input
    $('#inputPenyesuaian').on('input', calculateTotal);

    // Search Invoice
    $('#searchInvoice').on('keyup', function() {
        const query = $(this).val().toLowerCase();
        $('.invoice-row').each(function() {
            const rowText = $(this).text().toLowerCase();
            if (rowText.indexOf(query) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Persistence Logic using localStorage
    const storageKey = 'selected_biaya_kapal_data';
    
    function getStoredData() {
        const stored = localStorage.getItem(storageKey);
        return stored ? JSON.parse(stored) : [];
    }

    function setStoredData(data) {
        localStorage.setItem(storageKey, JSON.stringify(data));
    }

    // On page load, sync checkboxes with storage
    function syncCheckboxesWithStorage() {
        const storedData = getStoredData();
        const storedIds = storedData.map(d => d.id);
        
        $('.invoice-checkbox').each(function() {
            const id = $(this).val();
            if (storedIds.includes(id)) {
                $(this).prop('checked', true);
            }
        });
        
        // Handle initial selection from URL if any
        const urlId = "{{ request('biaya_kapal_id') }}";
        if (urlId && !storedIds.includes(urlId)) {
            const row = $('.invoice-checkbox[value="' + urlId + '"]');
            if (row.length) {
                const amount = parseFloat(row.data('amount')) || 0;
                storedData.push({ id: urlId, amount: amount });
                setStoredData(storedData);
                row.prop('checked', true);
            }
        }

        calculateTotal();
    }

    syncCheckboxesWithStorage();

    // Update storage when checkbox changes
    $('.invoice-checkbox').change(function() {
        const id = $(this).val();
        const amount = parseFloat($(this).data('amount')) || 0;
        let storedData = getStoredData();
        
        if (this.checked) {
            if (!storedData.find(d => d.id === id)) {
                storedData.push({ id: id, amount: amount });
            }
        } else {
            storedData = storedData.filter(d => d.id !== id);
        }
        
        setStoredData(storedData);
        calculateTotal();
        
        const allChecked = $('.invoice-checkbox:checked').length === $('.invoice-checkbox').length && $('.invoice-checkbox').length > 0;
        $('#selectAll').prop('checked', allChecked);
    });

    // Clear storage on form submit or cancel
    $('#pembayaranForm').submit(function() {
        // We will append all stored IDs to the form before submitting
        const storedData = getStoredData();
        const form = $(this);
        
        // Disable real checkboxes so they don't get submitted
        form.find('input[type="checkbox"][name="biaya_kapal_ids[]"]').prop('disabled', true);
        
        // Remove any previously injected hidden inputs
        form.find('input[type="hidden"].injected-id').remove();
        
        storedData.forEach(d => {
            form.append('<input type="hidden" name="biaya_kapal_ids[]" value="' + d.id + '" class="injected-id">');
        });

        localStorage.removeItem(storageKey);
    });

    $('a[href="{{ route("pembayaran-biaya-kapal.index") }}"]').click(function() {
        localStorage.removeItem(storageKey);
    });

    // Row click
    $('.invoice-row').click(function(e) {
        if ($(e.target).is('input')) return;
        const checkbox = $(this).find('.invoice-checkbox');
        checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
    });

    // Select All
    $('#selectAll').change(function() {
        $('.invoice-checkbox').prop('checked', $(this).is(':checked')).trigger('change');
    });

    // Form submission confirmation
    $('#pembayaranForm').submit(function(e) {
        const count = $('.invoice-checkbox:checked').length;
        if (count === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal satu invoice untuk dibayar.');
            return;
        }

        const total = $('#inputTotal').val();
        const bank = $('#bank').val();
        if (!bank) {
            e.preventDefault();
            alert('Silakan pilih bank terlebih dahulu.');
            return;
        }

        const formattedTotal = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(total);
        if (!confirm('Anda akan melakukan pembayaran total ' + formattedTotal + ' menggunakan ' + bank + '. Lanjutkan?')) {
            e.preventDefault();
        } else {
            $('#submitBtn').prop('disabled', true).text('MEMPROSES...');
        }
    });
});
</script>
@endpush
