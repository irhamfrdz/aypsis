@extends('layouts.app')

@section('title', 'Form Pembayaran Pranota Invoice Vendor')
@section('page_title', 'Form Pembayaran Pranota Invoice Vendor')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-4 max-w-6xl mx-auto">
        @php
            // Definisikan kelas Tailwind untuk input yang lebih compact
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors";
            $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-sm p-2";
            $labelClasses = "block text-xs font-medium text-gray-700 mb-1";
        @endphp

        @if(session('success'))
            <div class="mb-3 p-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <strong>Berhasil!</strong> {{ session('success') }}
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-3 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <div class="font-medium mb-1">Gagal Menyimpan Pembayaran!</div>
                        <div>{{ session('error') }}</div>
                    </div>
                </div>
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

        <!-- Header dengan Filter Vendor (Compact) -->
        <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Filter Vendor Supir</h3>
                    <p class="text-xs text-gray-500">Pilih vendor untuk menampilkan daftar pranota unpaid</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-1/2 justify-end">
                    <div class="min-w-0 flex-1">
                        <label for="vendor_id_filter" class="{{ $labelClasses }}">Pilih Vendor</label>
                        <select id="vendor_id_filter" onchange="window.location.href='{{ route('pembayaran-pranota-invoice-vendor-supir.create') }}?vendor_id=' + this.value" class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors">
                            <option value="">-- Semua Vendor --</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ $selectedVendorId == $vendor->id ? 'selected' : '' }}>{{ $vendor->nama_vendor }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <form id="pembayaranForm" action="{{ route('pembayaran-pranota-invoice-vendor-supir.store') }}" method="POST" class="space-y-3">
            @csrf
            
            <input type="hidden" name="vendor_id" value="{{ $selectedVendorId }}">

            <!-- Data Pembayaran & Bank -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Data Pembayaran -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Data Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label for="nomor_pembayaran" class="{{ $labelClasses }}">Nomor Pembayaran <span class="text-red-500">*</span></label>
                                <input type="text" name="nomor_pembayaran" id="nomor_pembayaran"
                                    value="{{ $nomorPembayaran ?? old('nomor_pembayaran') }}"
                                    class="{{ $readonlyInputClasses }}" readonly required>
                            </div>
                            <div>
                                <label for="tanggal_pembayaran" class="{{ $labelClasses }}">Tanggal Kas</label>
                                <input type="date" name="tanggal_pembayaran" id="tanggal_pembayaran"
                                    value="{{ old('tanggal_pembayaran', now()->toDateString()) }}"
                                    class="{{ $inputClasses }}" required>
                            </div>
                            <div>
                                <label for="nomor_accurate" class="{{ $labelClasses }}">No. Accurate</label>
                                <input type="text" name="nomor_accurate" id="nomor_accurate"
                                    value="{{ old('nomor_accurate') }}"
                                    class="{{ $inputClasses }}" placeholder="Contoh: ACC001">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank & Transaksi -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Bank & Metode</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div class="relative">
                                <label for="bank" class="{{ $labelClasses }}">Pilih Bank</label>
                                <div class="relative">
                                    <input type="text" id="bankSearch" placeholder="Cari bank..." 
                                        class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors pr-8"
                                        autocomplete="off">
                                    <svg class="absolute right-2 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <select name="bank" id="bank" class="hidden" required>
                                    <option value="">-- Pilih Bank --</option>
                                    @if(isset($akunCoa))
                                        @foreach($akunCoa as $akun)
                                            <option value="{{ $akun->nama_akun }}" data-kode="{{ $akun->kode_nomor ?? '000' }}" {{ old('bank') == $akun->nama_akun ? 'selected' : '' }}>
                                                {{ $akun->nama_akun }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div id="bankDropdown" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                                    <div id="bankOptions" class="py-1">
                                        <!-- Options will be populated by JavaScript -->
                                    </div>
                                    <div id="noBankResults" class="hidden px-3 py-2 text-xs text-gray-500 text-center">
                                        Tidak ada bank yang sesuai
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="metode_pembayaran" class="{{ $labelClasses }}">Metode Pembayaran</label>
                                <select name="metode_pembayaran" id="metode_pembayaran" class="{{ $inputClasses }}" required>
                                    <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="cash" {{ old('metode_pembayaran') == 'cash' ? 'selected' : '' }}>Tunai / Kas</option>
                                    <option value="cheque" {{ old('metode_pembayaran') == 'cheque' ? 'selected' : '' }}>Cek / Giro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pilih Pranota Invoice Vendor -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-semibold text-gray-800">Daftar Pranota Unpaid</h4>
                        <div class="flex items-center gap-2">
                            <input type="text" id="searchPranota" placeholder="Cari nomor pranota..." class="px-3 py-1.5 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-64">
                            <span id="searchCounter" class="text-xs text-gray-600"></span>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto max-h-80">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-20">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">
                                    <input type="checkbox" id="selectAll" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pranota</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tagihan</th>
                                <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Nominal Bayar</th>
                            </tr>
                        </thead>
                        <tbody id="pranotaTableBody" class="bg-white divide-y divide-gray-200">
                            @if(isset($pranotas) && count($pranotas) > 0)
                                @foreach($pranotas as $pranota)
                                <tr class="pranota-row hover:bg-gray-50 transition-colors">
                                    <td class="px-3 py-3 whitespace-nowrap text-xs">
                                        <input type="checkbox" name="pranota_ids[]" value="{{ $pranota->id }}" class="pranota-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded" data-total="{{ $pranota->total_nominal }}">
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-xs font-medium text-indigo-600">
                                        {{ $pranota->no_pranota }}
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-xs">
                                        {{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-right text-xs font-semibold">
                                        Rp {{ number_format($pranota->total_nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-right">
                                        <input type="number" name="nominal_bayar[{{ $pranota->id }}]" value="{{ (int)$pranota->total_nominal }}" class="nominal-input block w-full text-right border-gray-300 rounded-md text-sm py-1.5 focus:border-indigo-500 focus:ring-indigo-500" step="0.01">
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr id="emptyRow">
                                    <td colspan="5" class="px-3 py-8 text-center text-sm text-gray-500 italic">
                                        @if($selectedVendorId)
                                            Tidak ada pranota yang belum lunas untuk vendor ini.
                                        @else
                                            Pilih vendor terlebih dahulu.
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            <tr id="noResultsRow" class="hidden">
                                <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-500">
                                    Tidak ada hasil yang sesuai dengan pencarian.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                    <p class="text-xs text-gray-600">
                        * Centang pranota yang akan dibayar. Anda dapat mengubah nominal bayar sebagian jika diperlukan.
                    </p>
                </div>
            </div>

            <!-- Total Pembayaran & Keterangan -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 h-full">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Total Pembayaran</h4>
                        <div class="flex items-center justify-between mt-4">
                            <span class="text-sm font-medium text-gray-700">Total Nominal:</span>
                            <div class="text-3xl font-bold text-indigo-700" id="displayTotal">Rp 0</div>
                            <input type="hidden" name="total_pembayaran" id="total_pembayaran" value="0">
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 h-full">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Informasi Tambahan</h4>
                        <div>
                            <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="2"
                                class="{{ $inputClasses }}" placeholder="Tambahkan keterangan (opsional)...">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-2 mt-4">
                <a href="{{ route('pembayaran-pranota-invoice-vendor-supir.index') }}" class="inline-flex justify-center py-2.5 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Batal
                </a>
                <button type="submit" id="submitBtn" disabled class="inline-flex justify-center py-2.5 px-6 border border-transparent shadow-sm text-sm font-bold rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>

{{-- Script --}}
<script>
    // Bank search functionality
    document.addEventListener('DOMContentLoaded', function () {
        const bankSearch = document.getElementById('bankSearch');
        const bankSelect = document.getElementById('bank');
        const bankDropdown = document.getElementById('bankDropdown');
        const bankOptions = document.getElementById('bankOptions');
        const noBankResults = document.getElementById('noBankResults');

        if (bankSearch && bankSelect && bankDropdown && bankOptions) {
            const banks = Array.from(bankSelect.options).slice(1); 
            
            function renderBankOptions(filteredBanks) {
                bankOptions.innerHTML = '';
                
                if (filteredBanks.length === 0) {
                    bankOptions.classList.add('hidden');
                    noBankResults.classList.remove('hidden');
                } else {
                    bankOptions.classList.remove('hidden');
                    noBankResults.classList.add('hidden');
                    
                    filteredBanks.forEach(option => {
                        const div = document.createElement('div');
                        div.className = 'px-3 py-2 text-sm hover:bg-indigo-50 cursor-pointer transition-colors';
                        div.textContent = option.text;
                        div.dataset.value = option.value;
                        
                        div.addEventListener('click', function() {
                            bankSelect.value = this.dataset.value;
                            bankSearch.value = this.textContent;
                            bankDropdown.classList.add('hidden');
                            bankSelect.dispatchEvent(new Event('change'));
                        });
                        
                        bankOptions.appendChild(div);
                    });
                }
            }

            bankSearch.addEventListener('focus', function() {
                const searchTerm = this.value.toLowerCase();
                const filtered = banks.filter(option => option.text.toLowerCase().includes(searchTerm));
                renderBankOptions(filtered);
                bankDropdown.classList.remove('hidden');
            });

            bankSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const filtered = banks.filter(option => option.text.toLowerCase().includes(searchTerm));
                renderBankOptions(filtered);
                bankDropdown.classList.remove('hidden');
                
                if (!searchTerm) {
                    bankSelect.value = '';
                }
            });

            document.addEventListener('click', function(e) {
                if (!bankSearch.contains(e.target) && !bankDropdown.contains(e.target)) {
                    bankDropdown.classList.add('hidden');
                }
            });

            if (bankSelect.value) {
                const selectedOption = bankSelect.options[bankSelect.selectedIndex];
                if (selectedOption) {
                    bankSearch.value = selectedOption.text;
                }
            }
        }
    });

    // Checkbox and calculation
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
                const nominalInput = document.querySelector(`input[name="nominal_bayar[${cb.value}]"]`);
                if (cb.checked) {
                    const nominal = parseFloat(nominalInput.value) || 0;
                    total += nominal;
                    checkedCount++;
                }
            });
            
            displayTotal.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            inputTotal.value = total;
            
            if (submitBtn) {
                submitBtn.disabled = checkedCount === 0 || total <= 0;
            }
        }

        checkboxes.forEach((cb) => {
            cb.addEventListener('change', calculateTotal);
        });
        
        nominalInputs.forEach((input) => {
            input.addEventListener('input', calculateTotal);
        });

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    const row = cb.closest('tr');
                    if(row.style.display !== 'none') {
                        cb.checked = this.checked;
                    }
                });
                calculateTotal();
            });
        }
    });

    // Pranota Search
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchPranota');
        const tableBody = document.getElementById('pranotaTableBody');
        const searchCounter = document.getElementById('searchCounter');
        const noResultsRow = document.getElementById('noResultsRow');
        const emptyRow = document.getElementById('emptyRow');
        
        if (searchInput && tableBody) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const rows = tableBody.querySelectorAll('.pranota-row');
                let visibleCount = 0;
                let totalCount = rows.length;
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                if (searchTerm) {
                    searchCounter.textContent = `${visibleCount} dari ${totalCount} pranota`;
                } else {
                    searchCounter.textContent = '';
                }
                
                if (visibleCount === 0 && totalCount > 0) {
                    if (noResultsRow) noResultsRow.classList.remove('hidden');
                    if (emptyRow) emptyRow.classList.add('hidden');
                } else {
                    if (noResultsRow) noResultsRow.classList.add('hidden');
                    if (emptyRow && totalCount === 0) emptyRow.classList.remove('hidden');
                }
            });
        }
    });

    // Validation on Submit
    const pembayaranForm = document.getElementById('pembayaranForm');
    if (pembayaranForm) {
        pembayaranForm.addEventListener('submit', function(e) {
            const vendorSelect = document.getElementById('vendor_id_filter');
            const bankSelect = document.getElementById('bank');
            const checkedCheckboxes = document.querySelectorAll('.pranota-checkbox:checked');
            const totalPembayaran = document.getElementById('total_pembayaran').value;

            if (!document.querySelector('input[name="vendor_id"]').value) {
                e.preventDefault();
                alert('Pilih vendor terlebih dahulu!');
                vendorSelect.focus();
                return false;
            }

            if (checkedCheckboxes.length === 0) {
                e.preventDefault();
                alert('Pilih minimal satu pranota yang akan dibayar!');
                return false;
            }

            if (!bankSelect.value) {
                e.preventDefault();
                alert('Silakan pilih Bank!');
                document.getElementById('bankSearch').focus();
                return false;
            }

            // Show loading
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
            `;
        });
    }
</script>
@endsection
