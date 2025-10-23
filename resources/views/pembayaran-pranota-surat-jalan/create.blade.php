@extends('layouts.app')

@section('title', 'Form Pembayaran Pranota Surat Jalan')
@section('page_title', 'Form Pembayaran Pranota Surat Jalan')

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
                        <div class="mt-2 text-xs bg-red-100 p-2 rounded border border-red-300">
                            <strong>Solusi yang dapat dicoba:</strong>
                            <ul class="list-disc list-inside mt-1 space-y-1">
                                <li>Pastikan semua field yang wajib sudah diisi dengan benar</li>
                                <li>Periksa kembali bank dan jenis transaksi yang dipilih</li>
                                <li>Pastikan pranota surat jalan yang dipilih masih valid</li>
                                <li>Refresh halaman untuk mendapatkan nomor pembayaran baru</li>
                                <li>Jika masalah berlanjut, hubungi administrator sistem</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        {{-- Show validation errors --}}
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

        <!-- Header dengan Filter Tanggal (Compact) -->
        <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Filter Pranota Surat Jalan</h3>
                </div>
                <form action="{{ route('pembayaran-pranota-surat-jalan.create') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
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
                    <div class="flex gap-1 sm:self-end">
                        <button type="submit" class="inline-flex justify-center py-1.5 px-3 border border-transparent shadow-sm text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Cari
                        </button>
                        <a href="{{ route('pembayaran-pranota-surat-jalan.create') }}" class="inline-flex justify-center py-1.5 px-3 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <form id="pembayaranForm" action="{{ route('pembayaran-pranota-surat-jalan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf

            <!-- Data Pembayaran & Bank -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Data Pembayaran -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Data Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div class="flex items-end gap-1">
                                <div class="flex-1">
                                    <label for="nomor_pembayaran" class="{{ $labelClasses }}">Nomor Pembayaran</label>
                                    <input type="text" name="nomor_pembayaran" id="nomor_pembayaran"
                                        value="[AUTO GENERATED]"
                                        class="{{ $readonlyInputClasses }}" readonly placeholder="Nomor otomatis dari sistem">
                                </div>
                                <div class="w-16">
                                    <label for="nomor_cetakan" class="{{ $labelClasses }}">Cetak</label>
                                    <input type="number" name="nomor_cetakan" id="nomor_cetakan" min="1" max="9" value="{{ request('nomor_cetakan', 1) }}"
                                        class="{{ $inputClasses }}">
                                </div>
                            </div>
                            <div>
                                <label for="tanggal_kas" class="{{ $labelClasses }}">Tanggal Kas</label>
                                <input type="text" name="tanggal_kas" id="tanggal_kas"
                                    value="{{ now()->format('d/M/Y') }}"
                                    class="{{ $readonlyInputClasses }}" readonly required>
                                <input type="hidden" name="tanggal_pembayaran" id="tanggal_pembayaran" value="{{ now()->toDateString() }}">
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
                                <label for="bank" class="{{ $labelClasses }}">Pilih Bank</label>
                                <select name="bank" id="bank" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Bank --</option>
                                    @if(isset($akunCoa))
                                        @foreach($akunCoa as $akun)
                                            <option value="{{ $akun->nama_akun }}" data-kode="{{ $akun->kode_nomor ?? '000' }}" {{ old('bank') == $akun->nama_akun ? 'selected' : '' }}>
                                                {{ $akun->nama_akun }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label for="jenis_transaksi" class="{{ $labelClasses }}">Jenis Transaksi</label>
                                <select name="jenis_transaksi" id="jenis_transaksi" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Debit" {{ old('jenis_transaksi') == 'Debit' ? 'selected' : '' }}>Debit</option>
                                    <option value="Kredit" {{ old('jenis_transaksi') == 'Kredit' ? 'selected' : '' }}>Kredit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pilih Pranota Surat Jalan -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800">Pilih Pranota Surat Jalan</h4>
                    <p class="text-xs text-blue-600 mt-1">
                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <strong>Info:</strong> Surat jalan dengan tipe FCL akan otomatis dibuat menjadi data prospek setelah pembayaran.
                    </p>
                </div>
                <div class="overflow-x-auto max-h-60">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-20">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all" class="h-3 w-3 text-indigo-600 border-gray-300 rounded">
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pranota</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Surat Jalan</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pranotaSuratJalan as $pranota)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <input type="checkbox" name="pranota_surat_jalan_ids[]" value="{{ $pranota->id }}" class="pranota-checkbox h-3 w-3 text-indigo-600 border-gray-300 rounded" data-total="{{ $pranota->total_for_payment }}">
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">{{ $pranota->nomor_pranota ?? '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @if ($pranota->tanggal_pranota)
                                            {{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/M/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @if($pranota->suratJalans && $pranota->suratJalans->count() > 0)
                                            @php
                                                $firstSuratJalan = $pranota->suratJalans->first();
                                            @endphp
                                            {{ $firstSuratJalan->no_surat_jalan ?? '-' }}
                                            @if($pranota->suratJalans->count() > 1)
                                                <span class="text-gray-500 text-xs">(+{{ $pranota->suratJalans->count() - 1 }} lainnya)</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @if($pranota->suratJalans && $pranota->suratJalans->count() > 0)
                                            @php
                                                $tipeKontainers = $pranota->suratJalans->pluck('tipe_kontainer')->filter()->unique();
                                                $hasFcl = $tipeKontainers->contains(function($tipe) {
                                                    return strtoupper($tipe) === 'FCL';
                                                });
                                            @endphp
                                            @if($tipeKontainers->count() > 0)
                                                {{ $tipeKontainers->take(2)->implode(', ') }}
                                                @if($hasFcl)
                                                    <span class="ml-1 px-1 py-0.5 text-xs bg-blue-100 text-blue-600 rounded">→ Prospek</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @if($pranota->suratJalans && $pranota->suratJalans->count() > 0)
                                            @php
                                                $supirs = $pranota->suratJalans->pluck('supir')->filter()->unique();
                                            @endphp
                                            @if($supirs->count() > 0)
                                                {{ $supirs->take(2)->implode(', ') }}
                                                @if($supirs->count() > 2)
                                                    <span class="text-gray-500 text-xs">(+{{ $supirs->count() - 2 }} lainnya)</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold">Rp {{ number_format($pranota->total_for_payment, 0, ',', '.') }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <span class="px-1.5 py-0.5 inline-flex text-xs font-medium rounded bg-yellow-100 text-yellow-800">Belum Bayar</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-2 py-4 text-center text-xs text-gray-500">
                                        Tidak ada pranota surat jalan yang tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                    <div class="flex items-start gap-2">
                        <p class="text-xs text-gray-600 flex-1">
                            * Pilih satu atau lebih pranota surat jalan untuk dibayar.
                        </p>
                        <p class="text-xs text-blue-600">
                            <span class="px-1 py-0.5 bg-blue-100 text-blue-600 rounded">→ Prospek</span> = Akan otomatis masuk ke data prospek
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total Pembayaran & Informasi Tambahan -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Total Pembayaran -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Total Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label for="total_pembayaran" class="{{ $labelClasses }}">Total Tagihan</label>
                                <input type="number" name="total_pembayaran" id="total_pembayaran"
                                    value="0"
                                    class="{{ $readonlyInputClasses }}" readonly>
                            </div>
                            <div>
                                <label for="total_tagihan_penyesuaian" class="{{ $labelClasses }}">Penyesuaian</label>
                                <input type="number" name="total_tagihan_penyesuaian" id="total_tagihan_penyesuaian"
                                    class="{{ $inputClasses }}" value="0">
                            </div>
                            <div>
                                <label for="total_tagihan_setelah_penyesuaian" class="{{ $labelClasses }}">Total Akhir</label>
                                <input type="number" name="total_tagihan_setelah_penyesuaian" id="total_tagihan_setelah_penyesuaian"
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
                                    class="{{ $inputClasses }}" placeholder="Jelaskan alasan penyesuaian...">{{ old('alasan_penyesuaian') }}</textarea>
                            </div>
                            <div>
                                <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" rows="2"
                                    class="{{ $inputClasses }}" placeholder="Tambahkan keterangan...">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>

{{-- Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log('Script 1: DOM loaded');
        const selectAllCheckbox = document.getElementById('select-all');
        const pranotaCheckboxes = document.querySelectorAll('.pranota-checkbox');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () {
                pranotaCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateTotalPembayaran();
            });
        }

        // Validasi minimal satu pranota
        const pembayaranForm = document.getElementById('pembayaranForm');
        if (pembayaranForm) {
            pembayaranForm.addEventListener('submit', function(e) {
                const checkedCheckboxes = document.querySelectorAll('.pranota-checkbox:checked');
                const bankSelect = document.getElementById('bank');
                const jenisTransaksiSelect = document.getElementById('jenis_transaksi');

                // Validasi pranota yang dipilih
                if (checkedCheckboxes.length === 0) {
                    e.preventDefault();
                    showWarning('Silakan pilih minimal satu pranota surat jalan.', 'warning');
                    return false;
                }

                // Validasi bank
                if (!bankSelect.value) {
                    e.preventDefault();
                    showWarning('Silakan pilih bank terlebih dahulu.', 'warning');
                    bankSelect.focus();
                    return false;
                }

                // Validasi jenis transaksi
                if (!jenisTransaksiSelect.value) {
                    e.preventDefault();
                    showWarning('Silakan pilih jenis transaksi terlebih dahulu.', 'warning');
                    jenisTransaksiSelect.focus();
                    return false;
                }

                // Konfirmasi sebelum submit
                const totalPembayaran = parseFloat(document.getElementById('total_tagihan_setelah_penyesuaian')?.value) || 0;
                const bankName = bankSelect.options[bankSelect.selectedIndex].text;
                const jenisTransaksi = jenisTransaksiSelect.value;

                const confirmMessage = `Konfirmasi pembayaran:\n\nJumlah: Rp ${totalPembayaran.toLocaleString('id-ID')}\nBank: ${bankName}\nJenis: ${jenisTransaksi}\nPranota terpilih: ${checkedCheckboxes.length} item\n\nLanjutkan pembayaran?`;

                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                    return false;
                }

                // Show loading indicator
                showLoadingIndicator();
            });
        }

        // Function to show warning messages
        function showWarning(message, type = 'error') {
            const warningDiv = document.createElement('div');
            warningDiv.className = `mb-3 p-3 rounded-lg border text-sm ${
                type === 'warning' ? 'bg-yellow-50 border-yellow-200 text-yellow-800' : 'bg-red-50 border-red-200 text-red-800'
            }`;
            warningDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-lg leading-none">&times;</button>
                </div>
            `;

            const form = document.getElementById('pembayaranForm');
            form.parentNode.insertBefore(warningDiv, form);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (warningDiv.parentNode) {
                    warningDiv.remove();
                }
            }, 5000);
        }

        // Function to show loading indicator
        function showLoadingIndicator() {
            const submitButton = document.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menyimpan...
                `;
            }
        }

        // Perhitungan otomatis total pembayaran berdasarkan pranota yang dipilih
        const totalPembayaranInput = document.getElementById('total_pembayaran');
        const totalPenyesuaianInput = document.getElementById('total_tagihan_penyesuaian');
        const totalSetelahInput = document.getElementById('total_tagihan_setelah_penyesuaian');

        function updateTotalPembayaran() {
            let total = 0;
            pranotaCheckboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    const amount = parseFloat(checkbox.dataset.total) || 0;
                    total += amount;
                }
            });
            if (totalPembayaranInput) totalPembayaranInput.value = total;
            updateTotalSetelahPenyesuaian();
        }

        function updateTotalSetelahPenyesuaian() {
            const totalPembayaran = parseFloat(totalPembayaranInput?.value) || 0;
            const totalPenyesuaian = parseFloat(totalPenyesuaianInput?.value) || 0;
            if (totalSetelahInput) totalSetelahInput.value = totalPembayaran + totalPenyesuaian;
        }

        pranotaCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', updateTotalPembayaran);
        });
        if (totalPembayaranInput) totalPembayaranInput.addEventListener('input', updateTotalSetelahPenyesuaian);
        if (totalPenyesuaianInput) totalPenyesuaianInput.addEventListener('input', updateTotalSetelahPenyesuaian);
        updateTotalPembayaran();
    });

    // Keep tanggal_pembayaran hidden field synced with current date
    document.addEventListener('DOMContentLoaded', function () {
        console.log('Script 2: Tanggal pembayaran');
        const tanggalPembayaran = document.getElementById('tanggal_pembayaran');
        if (tanggalPembayaran) {
            // Keep hidden field with today's date for validation
            tanggalPembayaran.value = new Date().toISOString().split('T')[0];
        }
    });
</script>
@endsection
