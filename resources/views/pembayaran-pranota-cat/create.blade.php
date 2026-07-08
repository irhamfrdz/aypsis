@extends('layouts.app')

@section('title', 'Form Pembayaran Pranota CAT Kontainer')
@section('page_title', 'Form Pembayaran Pranota CAT Kontainer')

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
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-3 p-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                <strong>Peringatan:</strong> {{ session('error') }}
            </div>
        @endif
        {{-- Only show validation errors if this is a POST request (form submission) --}}
        @if(request()->isMethod('post') && !empty($errors) && (is_object($errors) ? $errors->any() : (!empty($errors) && is_array($errors))))
            <div class="mb-3 p-3 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mt-1 list-disc list-inside">
                    @if(is_object($errors) && method_exists($errors, 'all'))
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    @elseif(is_array($errors))
                        @foreach($errors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    @endif
                </ul>
            </div>
        @endif

        <form id="pembayaranForm" action="{{ route('pembayaran-pranota-cat.store') }}" method="POST" class="space-y-3">
            @csrf

            {{-- Hidden inputs for additional data --}}
            <input type="hidden" name="nomor_pembayaran" id="nomor_pembayaran_hidden" value="">

            <!-- Data Pembayaran & Bank -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Data Pembayaran -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Data Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div class="flex items-end gap-1">
                                <div class="flex-1">
                                    <label for="nomor_pembayaran" class="{{ $labelClasses }}">Nomor Pembayaran</label>
                                    <input type="text" name="nomor_pembayaran" id="nomor_pembayaran"
                                        value=""
                                        placeholder="Pilih bank terlebih dahulu"
                                        class="{{ $readonlyInputClasses }}" readonly>
                                </div>
                            </div>
                            <div>
                                <label for="tanggal_kas" class="{{ $labelClasses }}">Tanggal Kas</label>
                                <input type="date" name="tanggal_kas" id="tanggal_kas"
                                    value="{{ old('tanggal_kas', now()->toDateString()) }}"
                                    class="{{ $inputClasses }}" required>
                            </div>
                            <div>
                                <label for="nomor_accurate" class="{{ $labelClasses }}">Nomor Accurate</label>
                                <input type="text" name="nomor_accurate" id="nomor_accurate"
                                    value="{{ old('nomor_accurate') }}"
                                    placeholder="Nomor Accurate"
                                    class="{{ $inputClasses }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank & Transaksi -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Bank & Transaksi</h4>
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
                                    @foreach($akunCoa as $akun)
                                        <option value="{{ $akun->nama_akun }}" data-kode="{{ $akun->kode_nomor ?? '000' }}" {{ old('bank') == $akun->nama_akun ? 'selected' : '' }}>
                                            {{ $akun->nomor_akun }} - {{ $akun->nama_akun }}
                                        </option>
                                    @endforeach
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
                                <label for="jenis_transaksi" class="{{ $labelClasses }}">Jenis Transaksi</label>
                                <select name="jenis_transaksi" id="jenis_transaksi" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="debit">Debit</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pilih Pranota CAT --}}
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800">Pilih Pranota CAT Kontainer</h4>
                </div>
                <div class="overflow-x-auto max-h-60">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-20">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all" class="h-3 w-3 text-indigo-600 border-gray-300 rounded">
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pranota</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor / Bengkel</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pranota</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($pranotaList as $pranota)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <input type="checkbox" name="pranota_ids[]" value="{{ $pranota->id }}" class="pranota-checkbox h-3 w-3 text-indigo-600 border-gray-300 rounded" checked>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">{{ $pranota->nomor_pranota }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        {{ $pranota->vendor ?? '-' }} <span class="text-xs text-gray-400 font-normal">(Perbaikan)</span>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        {{ $pranota->tanggal_pranota ? $pranota->tanggal_pranota->format('d/M/Y') : '-' }}
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold">Rp {{ number_format($pranota->calculateTotalCatAmount(), 0, ',', '.') }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <span class="px-1.5 py-0.5 inline-flex text-xs font-medium rounded bg-yellow-100 text-yellow-800">Belum</span>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach ($pranotaTagihanCatList as $pranota)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <input type="checkbox" name="pranota_tagihan_ids[]" value="{{ $pranota->id }}" class="pranota-checkbox h-3 w-3 text-indigo-600 border-gray-300 rounded" checked>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">{{ $pranota->no_invoice }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        {{ $pranota->supplier ?? '-' }} <span class="text-xs text-indigo-400 font-normal">(Tagihan CAT)</span>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        {{ $pranota->tanggal_pranota ? $pranota->tanggal_pranota->format('d/M/Y') : '-' }}
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold">Rp {{ number_format($pranota->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <span class="px-1.5 py-0.5 inline-flex text-xs font-medium rounded bg-yellow-100 text-yellow-800">Belum</span>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($pranotaList->isEmpty() && $pranotaTagihanCatList->isEmpty())
                                <tr>
                                    <td colspan="6" class="px-2 py-4 text-center text-xs text-gray-500">
                                        Tidak ada pranota perbaikan atau tagihan CAT kontainer yang tersedia.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                    <p class="text-xs text-gray-600">
                        * Pilih satu atau lebih pranota CAT kontainer untuk dibayar.
                    </p>
                </div>
            </div>

            {{-- Total Pembayaran & Informasi Tambahan --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Total Pembayaran -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Total Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label for="total_pembayaran" class="{{ $labelClasses }}">Total Tagihan</label>
                                <input type="text" name="total_pembayaran" id="total_pembayaran"
                                    value="0"
                                    class="{{ $readonlyInputClasses }}" readonly>
                            </div>
                            <div>
                                <label for="total_tagihan_penyesuaian" class="{{ $labelClasses }}">Penyesuaian</label>
                                <input type="text" name="total_tagihan_penyesuaian" id="total_tagihan_penyesuaian"
                                    class="{{ $inputClasses }}" value="0">
                            </div>
                            <div>
                                <label for="total_tagihan_setelah_penyesuaian" class="{{ $labelClasses }}">Total Akhir</label>
                                <input type="text" name="total_tagihan_setelah_penyesuaian" id="total_tagihan_setelah_penyesuaian"
                                    class="{{ $readonlyInputClasses }} font-bold text-gray-800 bg-gray-100" readonly value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Informasi Tambahan</h4>
                        <div class="space-y-2">
                            <div>
                                <label for="alasan_penyesuaian" class="{{ $labelClasses }}">Alasan Penyesuaian</label>
                                <textarea name="alasan_penyesuaian" id="alasan_penyesuaian" rows="2"
                                    class="{{ $inputClasses }}" placeholder="Jelaskan alasan penyesuaian..."></textarea>
                            </div>
                            <div>
                                <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" rows="2"
                                    class="{{ $inputClasses }}" placeholder="Tambahkan keterangan..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>

{{-- Script --}}
{{-- Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('select-all');
        const pranotaCheckboxes = document.querySelectorAll('.pranota-checkbox');

        // Function to calculate total
        function calculateTotal() {
            let total = 0;
            pranotaCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const row = checkbox.closest('tr');
                    const amountText = row.querySelector('td:nth-child(5)').textContent;
                    const amount = parseFloat(amountText.replace(/Rp\s|,|\./g, '')) || 0;
                    total += amount;
                }
            });
            document.getElementById('total_pembayaran').value = total.toLocaleString('id-ID');
            updateTotalAkhir();
        }

        // Function to update total akhir
        function updateTotalAkhir() {
            const total = parseFloat(document.getElementById('total_pembayaran').value.replace(/\./g, '').replace(',', '.')) || 0;
            const penyesuaian = parseFloat(document.getElementById('total_tagihan_penyesuaian').value.replace(/\./g, '').replace(',', '.')) || 0;
            document.getElementById('total_tagihan_setelah_penyesuaian').value = (total + penyesuaian).toLocaleString('id-ID');
        }

        // Select all functionality
        selectAllCheckbox.addEventListener('change', function() {
            pranotaCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            calculateTotal();
        });

        // Individual checkbox change
        pranotaCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(pranotaCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
                calculateTotal();
            });
        });

        // Bank change
        document.getElementById('bank').addEventListener('change', function() {
            updateNomorPembayaran();
        });

        // Function to update nomor pembayaran
        function updateNomorPembayaran() {
            const bankSelect = document.getElementById('bank');
            const selectedOption = bankSelect.options[bankSelect.selectedIndex];
            const kode = selectedOption.getAttribute('data-kode') || '000';
            const counter = {{ \App\Models\PembayaranPranotaCat::count() + 1 }};
            const now = new Date();
            const year = now.getFullYear().toString().slice(-2);
            const month = (now.getMonth() + 1).toString().padStart(2, '0');
            const running = counter.toString().padStart(6, '0');
            const print = '1';
            const nomor = kode + print + year + month + running;
            document.getElementById('nomor_pembayaran').value = nomor;
            document.getElementById('nomor_pembayaran_hidden').value = nomor;
        }

        // Form validation before submission
        document.getElementById('pembayaranForm').addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.pranota-checkbox:checked');
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Pilih minimal satu pranota CAT untuk dibayar.');
                return false;
            }

            const bankSelect = document.getElementById('bank');
            if (!bankSelect.value) {
                e.preventDefault();
                alert('Pilih bank terlebih dahulu.');
                bankSelect.focus();
                return false;
            }

            const jenisTransaksi = document.getElementById('jenis_transaksi');
            if (!jenisTransaksi.value) {
                e.preventDefault();
                alert('Pilih jenis transaksi.');
                jenisTransaksi.focus();
                return false;
            }

            // Mencegah double submit / double book
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
        });

        // Penyesuaian change
        document.getElementById('total_tagihan_penyesuaian').addEventListener('input', updateTotalAkhir);

        // Format penyesuaian on focus/blur
        const penyesuaianInput = document.getElementById('total_tagihan_penyesuaian');
        penyesuaianInput.addEventListener('focus', function() {
            this.value = this.value.replace(/\./g, '').replace(',', '.');
        });
        penyesuaianInput.addEventListener('blur', function() {
            const num = parseFloat(this.value) || 0;
            this.value = num.toLocaleString('id-ID');
            updateTotalAkhir();
        });

        // Initial calculation
        calculateTotal();
        penyesuaianInput.value = '0';

        // Generate initial nomor pembayaran if bank is selected
        const bankSelect = document.getElementById('bank');
        if (bankSelect.value) {
            updateNomorPembayaran();
        }

        // Bank search dropdown functionality
        const bankSearch = document.getElementById('bankSearch');
        const bankDropdown = document.getElementById('bankDropdown');
        const bankOptions = document.getElementById('bankOptions');
        const noBankResults = document.getElementById('noBankResults');

        if (bankSearch && bankSelect && bankDropdown && bankOptions) {
            const banks = Array.from(bankSelect.options).slice(1); // Skip empty option
            
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
                            
                            // Trigger change event
                            bankSelect.dispatchEvent(new Event('change'));
                        });
                        
                        bankOptions.appendChild(div);
                    });
                }
            }

            // Show dropdown on focus
            bankSearch.addEventListener('focus', function() {
                const searchTerm = this.value.toLowerCase();
                const filtered = banks.filter(option => 
                    option.text.toLowerCase().includes(searchTerm)
                );
                renderBankOptions(filtered);
                bankDropdown.classList.remove('hidden');
            });

            // Filter on input
            bankSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const filtered = banks.filter(option => 
                    option.text.toLowerCase().includes(searchTerm)
                );
                renderBankOptions(filtered);
                bankDropdown.classList.remove('hidden');
                
                if (!searchTerm) {
                    bankSelect.value = '';
                    updateNomorPembayaran();
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!bankSearch.contains(e.target) && !bankDropdown.contains(e.target)) {
                    bankDropdown.classList.add('hidden');
                }
            });

            // Set initial value if bank was previously selected
            if (bankSelect.value) {
                const selectedOption = bankSelect.options[bankSelect.selectedIndex];
                if (selectedOption) {
                    bankSearch.value = selectedOption.text;
                }
            }
        }
    });
</script>
@endsection
