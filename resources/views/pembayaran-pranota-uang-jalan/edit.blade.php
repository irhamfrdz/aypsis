@extends('layouts.app')

@section('title', 'Edit Pembayaran Pranota Uang Jalan')
@section('page_title', 'Edit Pembayaran Pranota Uang Jalan')

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
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <div class="font-medium mb-1">Kesalahan Validasi!</div>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('pembayaran-pranota-uang-jalan.update', $pembayaranPranotaUangJalan->id) }}" method="POST" enctype="multipart/form-data" id="pembayaranForm">
            @csrf
            @method('PUT')

            @if($pembayaranPranotaUangJalan->isPaid())
                <!-- Alert for paid payment - only nomor accurate can be edited -->
                <div class="mb-3 p-3 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 text-sm">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <strong>Pembayaran Sudah Dibayar</strong><br>
                            Hanya nomor accurate yang dapat diubah untuk pembayaran yang sudah lunas.
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 mb-3">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Edit Nomor Accurate</h4>
                    <div class="grid grid-cols-1 gap-2">
                        <div>
                            <label for="nomor_accurate" class="{{ $labelClasses }}">Nomor Accurate</label>
                            <input type="text" name="nomor_accurate" id="nomor_accurate"
                                value="{{ old('nomor_accurate', $pembayaranPranotaUangJalan->nomor_accurate) }}"
                                class="{{ $inputClasses }}" placeholder="Masukkan nomor accurate">
                        </div>
                        <div class="mt-2">
                            <label for="tanggal_pembayaran" class="{{ $labelClasses }}">Tanggal Pembayaran</label>
                            <input type="date" name="tanggal_pembayaran" id="tanggal_pembayaran"
                                value="{{ old('tanggal_pembayaran', optional($pembayaranPranotaUangJalan->tanggal_pembayaran)->format('Y-m-d')) }}"
                                class="{{ $inputClasses }}">
                        </div>
                    </div>
                </div>

                <!-- Pranota Info (Read Only) -->
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 mb-3">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Informasi Pembayaran (Read Only)</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Nomor Pembayaran:</span>
                            <p class="font-medium">{{ $pembayaranPranotaUangJalan->nomor_pembayaran }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Tanggal Pembayaran:</span>
                            <p class="font-medium">{{ optional($pembayaranPranotaUangJalan->tanggal_pembayaran)->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Bank:</span>
                            <p class="font-medium">{{ $pembayaranPranotaUangJalan->bank }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Total Pembayaran:</span>
                            <p class="font-medium">Rp {{ number_format($pembayaranPranotaUangJalan->total_pembayaran, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Full Edit Form for Unpaid Payments -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-3">
                    <!-- Info Pembayaran -->
                    <div class="lg:col-span-1">
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-800 mb-2">Info Pembayaran</h4>
                            <div class="grid grid-cols-1 gap-2">
                                <div>
                                    <label for="nomor_pembayaran" class="{{ $labelClasses }}">Nomor Pembayaran</label>
                                    <input type="text" name="nomor_pembayaran" id="nomor_pembayaran"
                                        value="{{ $pembayaranPranotaUangJalan->nomor_pembayaran }}"
                                        class="{{ $readonlyInputClasses }}" readonly>
                                </div>
                                <div>
                                    <label for="nomor_accurate" class="{{ $labelClasses }}">Nomor Accurate</label>
                                    <input type="text" name="nomor_accurate" id="nomor_accurate"
                                        value="{{ old('nomor_accurate', $pembayaranPranotaUangJalan->nomor_accurate) }}"
                                        class="{{ $inputClasses }}" placeholder="Masukkan nomor accurate">
                                </div>
                                 <div>
                                    <label for="tanggal_pembayaran" class="{{ $labelClasses }}">Tanggal Pembayaran <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_pembayaran" id="tanggal_pembayaran"
                                        value="{{ old('tanggal_pembayaran', optional($pembayaranPranotaUangJalan->tanggal_pembayaran)->format('Y-m-d')) }}"
                                        class="{{ $inputClasses }}" required>
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
                                <label for="bank" class="{{ $labelClasses }}">Pilih Bank <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" id="bankSearch" placeholder="Cari bank..." 
                                        class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors pr-8"
                                        autocomplete="off" value="{{ old('bank', $pembayaranPranotaUangJalan->bank) }}">
                                    <svg class="absolute right-2 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <select name="bank" id="bank" class="hidden" required>
                                    <option value="">-- Pilih Bank --</option>
                                    @if(isset($akunCoa))
                                        @foreach($akunCoa as $akun)
                                            <option value="{{ $akun->nama_akun }}" data-kode="{{ $akun->kode_nomor ?? '000' }}" {{ old('bank', $pembayaranPranotaUangJalan->bank) == $akun->nama_akun ? 'selected' : '' }}>
                                                {{ $akun->nama_akun }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div id="bankDropdown" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                                    <div id="bankOptions" class="py-1"></div>
                                    <div id="noBankResults" class="hidden px-3 py-2 text-xs text-gray-500 text-center">Tidak ada bank yang sesuai</div>
                                </div>
                            </div>
                            <div>
                                <label for="jenis_transaksi" class="{{ $labelClasses }}">Jenis Transaksi <span class="text-red-500">*</span></label>
                                <select name="jenis_transaksi" id="jenis_transaksi" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Debit" {{ old('jenis_transaksi', $pembayaranPranotaUangJalan->jenis_transaksi) == 'Debit' ? 'selected' : '' }}>Debit (Bank +, Biaya -)</option>
                                    <option value="Kredit" {{ old('jenis_transaksi', $pembayaranPranotaUangJalan->jenis_transaksi) == 'Kredit' ? 'selected' : '' }}>Kredit (Biaya +, Bank -)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded text-xs text-blue-800">
                            <div class="flex items-start">
                                <svg class="w-3 h-3 mr-1 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <strong>Jurnal Akuntansi:</strong><br>
                                    • <strong>Debit:</strong> Bank bertambah (Dr), Biaya berkurang (Cr)<br>
                                    • <strong>Kredit:</strong> Biaya bertambah (Dr), Bank berkurang (Cr)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pranota Info -->
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 mb-3">
                <h4 class="text-sm font-semibold text-gray-800 mb-2">Informasi Pranota</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Nomor Pranota:</span>
                        <p class="font-medium">{{ $pembayaranPranotaUangJalan->pranotaUangJalan->nomor_pranota }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Tanggal Pranota:</span>
                        <p class="font-medium">{{ optional($pembayaranPranotaUangJalan->pranotaUangJalan->tanggal_pranota)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Total Pranota:</span>
                        <p class="font-medium">Rp {{ number_format($pembayaranPranotaUangJalan->pranotaUangJalan->total_amount, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Jumlah Item:</span>
                        <p class="font-medium">{{ $pembayaranPranotaUangJalan->pranotaUangJalan->uangJalans->count() }} item</p>
                    </div>
                </div>
            </div>

            <!-- Pembayaran & Penyesuaian -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Pembayaran</h4>
                    <div class="grid grid-cols-1 gap-2">
                        <div>
                            <label for="total_pembayaran" class="{{ $labelClasses }}">Total Pembayaran <span class="text-red-500">*</span></label>
                            <input type="number" name="total_pembayaran" id="total_pembayaran" step="0.01"
                                value="{{ old('total_pembayaran', $pembayaranPranotaUangJalan->total_pembayaran) }}"
                                class="{{ $inputClasses }}" required>
                        </div>
                        <div>
                            <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="2"
                                class="{{ $inputClasses }}" placeholder="Keterangan tambahan">{{ old('keterangan', $pembayaranPranotaUangJalan->keterangan) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Penyesuaian (Opsional)</h4>
                    <div class="grid grid-cols-1 gap-2">
                        <div>
                            <label for="total_tagihan_penyesuaian" class="{{ $labelClasses }}">Total Penyesuaian</label>
                            <input type="number" name="total_tagihan_penyesuaian" id="total_tagihan_penyesuaian" step="0.01"
                                value="{{ old('total_tagihan_penyesuaian', $pembayaranPranotaUangJalan->total_tagihan_penyesuaian ?? 0) }}"
                                class="{{ $inputClasses }}" placeholder="0">
                        </div>
                        <div>
                            <label for="total_tagihan_setelah_penyesuaian" class="{{ $labelClasses }}">Total Setelah Penyesuaian</label>
                            <input type="number" name="total_tagihan_setelah_penyesuaian" id="total_tagihan_setelah_penyesuaian" step="0.01"
                                value="{{ old('total_tagihan_setelah_penyesuaian', $pembayaranPranotaUangJalan->total_tagihan_setelah_penyesuaian) }}"
                                class="{{ $readonlyInputClasses }}" readonly>
                        </div>
                        <div>
                            <label for="alasan_penyesuaian" class="{{ $labelClasses }}">Alasan Penyesuaian</label>
                            <textarea name="alasan_penyesuaian" id="alasan_penyesuaian" rows="2"
                                class="{{ $inputClasses }}" placeholder="Jelaskan alasan penyesuaian">{{ old('alasan_penyesuaian', $pembayaranPranotaUangJalan->alasan_penyesuaian) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bukti Pembayaran -->
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 mb-3">
                <h4 class="text-sm font-semibold text-gray-800 mb-2">Bukti Pembayaran</h4>
                <div>
                    <label for="bukti_pembayaran" class="{{ $labelClasses }}">Upload Bukti Pembayaran (Opsional)</label>
                    <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" accept="image/*,application/pdf"
                        class="{{ $inputClasses }}">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG, PDF. Maksimal 2MB.</p>
                    @if($pembayaranPranotaUangJalan->bukti_pembayaran)
                        <div class="mt-2">
                            <span class="text-xs text-gray-600">Bukti saat ini:</span>
                            <a href="{{ Storage::url($pembayaranPranotaUangJalan->bukti_pembayaran) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat Bukti</a>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                <a href="{{ route('pembayaran-pranota-uang-jalan.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Pembayaran
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isPaid = {{ $pembayaranPranotaUangJalan->isPaid() ? 'true' : 'false' }};
            
            // Only initialize bank search for unpaid payments
            if (!isPaid) {
                // Bank search functionality
                const bankSearch = document.getElementById('bankSearch');
                const bankSelect = document.getElementById('bank');
                const bankDropdown = document.getElementById('bankDropdown');
                const bankOptions = document.getElementById('bankOptions');
                const noBankResults = document.getElementById('noBankResults');

                function populateBankOptions(searchTerm = '') {
                    const options = Array.from(bankSelect.options).filter(opt => opt.value !== '');
                    const filtered = options.filter(opt => 
                        opt.text.toLowerCase().includes(searchTerm.toLowerCase())
                    );

                    bankOptions.innerHTML = '';
                    
                    if (filtered.length === 0) {
                        noBankResults.classList.remove('hidden');
                        bankOptions.classList.add('hidden');
                    } else {
                        noBankResults.classList.add('hidden');
                        bankOptions.classList.remove('hidden');
                    
                        filtered.forEach(opt => {
                            const div = document.createElement('div');
                            div.className = 'px-3 py-2 hover:bg-indigo-50 cursor-pointer text-sm';
                            div.textContent = opt.text;
                            div.dataset.value = opt.value;
                            
                            div.addEventListener('click', function() {
                                bankSelect.value = this.dataset.value;
                                bankSearch.value = this.textContent;
                                bankDropdown.classList.add('hidden');
                            });
                            
                            bankOptions.appendChild(div);
                        });
                    }
                    
                    bankDropdown.classList.remove('hidden');
                }

                bankSearch.addEventListener('focus', function() {
                    populateBankOptions(this.value);
                });

                bankSearch.addEventListener('input', function() {
                    populateBankOptions(this.value);
                });

                document.addEventListener('click', function(e) {
                    if (!bankSearch.contains(e.target) && !bankDropdown.contains(e.target)) {
                        bankDropdown.classList.add('hidden');
                    }
                });

                // Set initial bank search value
                if (bankSelect.value) {
                    const selectedOption = bankSelect.options[bankSelect.selectedIndex];
                    if (selectedOption) {
                        bankSearch.value = selectedOption.text;
                    }
                }


                // Calculate total after adjustment
                const totalPembayaran = document.getElementById('total_pembayaran');
                const totalPenyesuaian = document.getElementById('total_tagihan_penyesuaian');
                const totalSetelah = document.getElementById('total_tagihan_setelah_penyesuaian');

                function updateTotalSetelah() {
                    const pembayaran = parseFloat(totalPembayaran.value) || 0;
                    const penyesuaian = parseFloat(totalPenyesuaian.value) || 0;
                    totalSetelah.value = pembayaran + penyesuaian;
                }

                totalPembayaran.addEventListener('input', updateTotalSetelah);
                totalPenyesuaian.addEventListener('input', updateTotalSetelah);
                
                // Initial calculation
                updateTotalSetelah();
            }
        });
    </script>
@endsection
