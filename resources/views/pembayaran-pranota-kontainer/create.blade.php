@extends('layouts.app')

@section('title', 'Form Pembayaran Pranota Kontainer Sewa')
@section('page_title', 'Form Pembayaran Pranota Kontainer Sewa')

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
            <div class="mb-3 p-4 rounded-lg bg-red-50 border-2 border-red-300 text-red-800 text-sm shadow-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-bold text-red-800">Gagal Menyimpan Data Pembayaran!</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <strong>Peringatan:</strong> {{ session('error') }}
                        </div>
                    </div>
                </div>
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

        <form id="pembayaranForm" action="{{ route('pembayaran-pranota-kontainer.store') }}" method="POST" class="space-y-3">
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
                                        value=""
                                        class="{{ $readonlyInputClasses }}" readonly>
                                </div>
                                <div class="w-16">
                                    <label for="nomor_cetakan" class="{{ $labelClasses }}">Cetak</label>
                                    <input type="number" name="nomor_cetakan" id="nomor_cetakan" min="1" max="9" value="1"
                                        class="{{ $inputClasses }}">
                                </div>
                            </div>
                            <div>
                                <label for="nomor_accurate" class="{{ $labelClasses }}">Nomor Accurate</label>
                                <input type="text" name="nomor_accurate" id="nomor_accurate"
                                    value="{{ old('nomor_accurate') }}"
                                    class="{{ $inputClasses }}" placeholder="Masukkan nomor accurate...">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                            <div>
                                <label for="tanggal_kas" class="{{ $labelClasses }}">Tanggal Kas</label>
                                <input type="date" name="tanggal_kas" id="tanggal_kas"
                                    value="{{ now()->toDateString() }}"
                                    class="{{ $inputClasses }}" required>
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
                                    @foreach($akunCoa as $akun)
                                        <option value="{{ $akun->nama_akun }}" data-kode="{{ $akun->kode_nomor ?? '000' }}" {{ old('bank') == $akun->nama_akun ? 'selected' : '' }}>
                                            {{ $akun->nomor_akun }} - {{ $akun->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="jenis_transaksi" class="{{ $labelClasses }}">Jenis Transaksi</label>
                                <select name="jenis_transaksi" id="jenis_transaksi" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Debit">Debit</option>
                                    <option value="Kredit">Kredit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pilih Pranota Kontainer --}}
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800">Pilih Pranota Kontainer</h4>
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
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jml Tagihan</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pranotaList as $pranota)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <input type="checkbox" name="pranota_ids[]" value="{{ $pranota->id }}" class="pranota-checkbox h-3 w-3 text-indigo-600 border-gray-300 rounded" checked>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">{{ $pranota->no_invoice }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @if ($pranota->tanggal_pranota)
                                            {{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/M/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <span class="px-1.5 py-0.5 inline-flex text-xs font-medium rounded bg-blue-100 text-blue-800">
                                            {{ $pranota->jumlah_tagihan ?? 0 }} item
                                        </span>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold">Rp {{ number_format($pranota->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @if ($pranota->status == 'completed' || $pranota->status == 'paid')
                                            <span class="px-1.5 py-0.5 inline-flex text-xs font-medium rounded bg-green-100 text-green-800">Lunas</span>
                                        @else
                                            <span class="px-1.5 py-0.5 inline-flex text-xs font-medium rounded bg-yellow-100 text-yellow-800">Belum</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-2 py-4 text-center text-xs text-gray-500">
                                        Tidak ada pranota kontainer yang tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                    <p class="text-xs text-gray-600">
                        * Pilih satu atau lebih pranota kontainer untuk dibayar.
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
                                <label for="total_tagihan_setelah_penyesuaian" class="{{ $labelClasses }}">Total Akhir <span id="dpLabel" class="text-green-600 hidden">(- DP)</span></label>
                                <input type="text" name="total_tagihan_setelah_penyesuaian" id="total_tagihan_setelah_penyesuaian"
                                    class="{{ $readonlyInputClasses }} font-bold text-gray-800 bg-gray-100" readonly value="0">
                            </div>
                        </div>

                        <!-- Detail Perhitungan DP -->
                        <div id="dpCalculationDetail" class="hidden mt-3 p-2 bg-green-50 border border-green-200 rounded">
                            <div class="text-xs text-gray-700">
                                <div class="font-medium mb-1">Detail Perhitungan:</div>
                                <div class="flex justify-between">
                                    <span>Total Tagihan:</span>
                                    <span id="detailTagihan">Rp 0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Penyesuaian:</span>
                                    <span id="detailPenyesuaian">Rp 0</span>
                                </div>
                                <div class="flex justify-between text-red-600">
                                    <span>DP Terpotong:</span>
                                    <span id="detailDPAmount">- Rp 0</span>
                                </div>
                                <hr class="my-1">
                                <div class="flex justify-between font-semibold">
                                    <span>Total yang Harus Dibayar:</span>
                                    <span id="detailTotalAkhir">Rp 0</span>
                                </div>
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

            {{-- DP & Submit Buttons --}}
            <div class="flex justify-between items-center">
                <div>
                    <!-- Tombol Pilih DP -->
                    <button type="button" id="btnPilihDP" class="inline-flex items-center py-2 px-4 border border-yellow-300 shadow-sm text-sm font-medium rounded-md text-yellow-700 bg-yellow-50 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                        <i class="fas fa-money-bill-wave mr-2"></i>
                        Pilih DP
                    </button>

                    <!-- Info DP yang dipilih -->
                    <div id="selectedDPInfo" class="hidden mt-2 p-2 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-green-800">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span id="selectedDPText">DP terpilih: </span>
                            </div>
                            <button type="button" id="clearDPSelection" class="text-red-600 hover:text-red-800 text-xs">
                                <i class="fas fa-times"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Simpan Pembayaran
                    </button>
                </div>
            </div>

            <!-- Hidden input untuk menyimpan DP yang dipilih -->
            <input type="hidden" id="selectedDPId" name="selected_dp_id" value="">
            <input type="hidden" id="selectedDPAmount" name="selected_dp_amount" value="">
        </form>
    </div>

    <!-- Modal Pilih DP -->
    <div id="dpModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header Modal -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Pilih Pembayaran DP</h3>
                    <button type="button" id="closeDPModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Loading -->
                <div id="dpLoading" class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                    <p class="text-gray-600">Memuat data DP...</p>
                </div>

                <!-- Content -->
                <div id="dpContent" class="hidden">
                    <!-- Search -->
                    <div class="mb-4">
                        <input type="text" id="dpSearch" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Cari berdasarkan nomor pembayaran atau aktivitas...">
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto max-h-96">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pilih</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktivitas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                                </tr>
                            </thead>
                            <tbody id="dpTableBody" class="bg-white divide-y divide-gray-200">
                                <!-- Data akan diisi oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- No Data -->
                    <div id="dpNoData" class="hidden text-center py-8">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-600">Tidak ada pembayaran DP yang tersedia</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-4 flex justify-end">
                    <button type="button" id="cancelDPSelection" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2">
                        Batal
                    </button>
                    <button type="button" id="confirmDPSelection" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:bg-gray-300 disabled:cursor-not-allowed" disabled>
                        Pilih DP
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Error -->
    <div id="errorModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative mx-auto p-0 border w-11/12 max-w-md shadow-2xl rounded-lg bg-white animate-bounce-in">
            <!-- Header -->
            <div class="bg-red-600 text-white px-6 py-4 rounded-t-lg">
                <div class="flex items-center">
                    <svg class="h-8 w-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <h3 class="text-xl font-bold">Gagal Menyimpan Data!</h3>
                </div>
            </div>
            
            <!-- Body -->
            <div class="px-6 py-6">
                <div class="mb-4">
                    <p class="text-gray-700 text-sm font-medium mb-2">Terjadi kesalahan saat menyimpan data pembayaran:</p>
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                        <p id="errorMessage" class="text-red-800 text-sm"></p>
                    </div>
                </div>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                    <p class="text-yellow-800 text-xs">
                        <strong>Saran:</strong> Periksa kembali data yang Anda masukkan dan pastikan semua field yang wajib telah diisi dengan benar.
                    </p>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end">
                <button type="button" id="closeErrorModal" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <style>
        @keyframes bounce-in {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                transform: scale(1);
            }
        }
        .animate-bounce-in {
            animation: bounce-in 0.5s ease-out;
        }
    </style>

{{-- Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('select-all');
        const pranotaCheckboxes = document.querySelectorAll('.pranota-checkbox');

        selectAllCheckbox.addEventListener('change', function () {
            pranotaCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateTotalPembayaran();
        });

        // Validasi minimal satu pranota
        const pembayaranForm = document.getElementById('pembayaranForm');
        pembayaranForm.addEventListener('submit', function(e) {
            const checkedCheckboxes = document.querySelectorAll('.pranota-checkbox:checked');
            if (checkedCheckboxes.length === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal satu pranota kontainer.');
                return false;
            }

            // Convert formatted numbers back to plain numbers for submission
            const penyesuaianValue = totalPenyesuaianInput.value.replace(/\./g, '').replace(',', '.');
            totalPenyesuaianInput.value = penyesuaianValue;

            const totalValue = totalPembayaranInput.value.replace(/\./g, '').replace(',', '.');
            totalPembayaranInput.value = totalValue;

            const totalAkhirValue = totalSetelahInput.value.replace(/\./g, '').replace(',', '.');
            totalSetelahInput.value = totalAkhirValue;
        });

        // Perhitungan otomatis total pembayaran berdasarkan pranota yang dipilih
        const totalPembayaranInput = document.getElementById('total_pembayaran');
        const totalPenyesuaianInput = document.getElementById('total_tagihan_penyesuaian');
        const totalSetelahInput = document.getElementById('total_tagihan_setelah_penyesuaian');

        // Simpan nilai total_amount di data attribute
        const pranotaBiayaMap = {};
        @if(isset($pranotaList))
            @foreach ($pranotaList as $pranota)
                pranotaBiayaMap['{{ $pranota->id }}'] = parseFloat({{ $pranota->total_amount }});
            @endforeach
        @endif

        function updateTotalPembayaran() {
            let total = 0;
            pranotaCheckboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    const id = checkbox.value;
                    const biaya = parseFloat(pranotaBiayaMap[id]) || 0;
                    total += biaya;
                }
            });
            // Format total dengan pemisah ribuan Indonesia
            totalPembayaranInput.value = total.toLocaleString('id-ID');
            updateTotalSetelahPenyesuaian();
        }

        function updateTotalSetelahPenyesuaian() {
            const totalPembayaran = parseFloat(totalPembayaranInput.value.replace(/\./g, '').replace(',', '.')) || 0;
            const totalPenyesuaian = parseFloat(totalPenyesuaianInput.value) || 0;
            const dpAmount = parseFloat(document.getElementById('selectedDPAmount').value) || 0;

            // Total = (Total Pembayaran + Penyesuaian) - DP Amount
            const totalAkhir = (totalPembayaran + totalPenyesuaian) - dpAmount;
            totalSetelahInput.value = totalAkhir.toLocaleString('id-ID');

            // Update detail perhitungan jika ada DP
            if (dpAmount > 0) {
                document.getElementById('detailTagihan').textContent = 'Rp ' + totalPembayaran.toLocaleString('id-ID');
                document.getElementById('detailPenyesuaian').textContent = 'Rp ' + totalPenyesuaian.toLocaleString('id-ID');
                document.getElementById('detailDPAmount').textContent = '- Rp ' + dpAmount.toLocaleString('id-ID');
                document.getElementById('detailTotalAkhir').textContent = 'Rp ' + totalAkhir.toLocaleString('id-ID');
                document.getElementById('dpCalculationDetail').classList.remove('hidden');
            } else {
                document.getElementById('dpCalculationDetail').classList.add('hidden');
            }
        }

        pranotaCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', updateTotalPembayaran);
        });
        totalPembayaranInput.addEventListener('input', updateTotalSetelahPenyesuaian);
        totalPenyesuaianInput.addEventListener('input', updateTotalSetelahPenyesuaian);
        totalPenyesuaianInput.addEventListener('blur', function() {
            // Format penyesuaian input saat kehilangan focus
            const value = parseFloat(this.value) || 0;
            this.value = value.toLocaleString('id-ID');
            updateTotalSetelahPenyesuaian();
        });
        updateTotalPembayaran();
    });

    // Sync tanggal_kas with tanggal_pembayaran when user changes it
    document.addEventListener('DOMContentLoaded', function () {
        const tanggalKas = document.getElementById('tanggal_kas');
        const tanggalPembayaran = document.getElementById('tanggal_pembayaran');
        
        if (tanggalKas && tanggalPembayaran) {
            // Sync tanggal_pembayaran when tanggal_kas changes
            tanggalKas.addEventListener('change', function() {
                tanggalPembayaran.value = this.value;
            });
            
            // Initialize tanggal_pembayaran with tanggal_kas value
            tanggalPembayaran.value = tanggalKas.value;
        }
    });
</script>
<script>
    // Script untuk update nomor pembayaran
    document.addEventListener('DOMContentLoaded', function () {
        const nomorCetakanInput = document.getElementById('nomor_cetakan');
        const nomorPembayaranInput = document.getElementById('nomor_pembayaran');
        const bankSelect = document.getElementById('bank');

        // Function to update nomor pembayaran
        function updateNomorPembayaran() {
            const cetakan = nomorCetakanInput.value || 1;

            // Get bank code from selected option's data-kode attribute
            let kodeBank = '000'; // Default
            if (bankSelect.value) {
                const selectedOption = bankSelect.querySelector(`option[value="${bankSelect.value}"]`);
                if (selectedOption && selectedOption.dataset.kode) {
                    kodeBank = selectedOption.dataset.kode;
                }
            }

            // Make AJAX call to get the current nomor pembayaran
            const url = `{{ route('pembayaran-pranota-kontainer.generate-nomor') }}?nomor_cetakan=${cetakan}&kode_bank=${kodeBank}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        nomorPembayaranInput.value = data.nomor_pembayaran;
                    } else {
                        console.error('Error generating nomor pembayaran:', data.message);
                        // Fallback to client-side generation if server fails
                        const now = new Date();
                        const tahun = String(now.getFullYear()).slice(-2);
                        const bulan = String(now.getMonth() + 1).padStart(2, '0');
                        const sequence = '000001';
                        nomorPembayaranInput.value = `${kodeBank}-${cetakan}-${tahun}-${bulan}-${sequence}`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching nomor pembayaran:', error);
                    // Fallback to client-side generation if server fails
                    const now = new Date();
                    const tahun = String(now.getFullYear()).slice(-2);
                    const bulan = String(now.getMonth() + 1).padStart(2, '0');
                    const sequence = '000001';
                    nomorPembayaranInput.value = `${kodeBank}-${cetakan}-${tahun}-${bulan}-${sequence}`;
                });
        }

        // Event listeners
        nomorCetakanInput.addEventListener('input', updateNomorPembayaran);
        bankSelect.addEventListener('change', updateNomorPembayaran);        // Initial update
        updateNomorPembayaran();
    });
</script>
<script>
    // Scroll to flash message if present and focus it for accessibility
    document.addEventListener('DOMContentLoaded', function () {
        const flash = document.getElementById('flash-message');
        if (flash) {
            flash.scrollIntoView({ behavior: 'smooth', block: 'center' });
            flash.setAttribute('tabindex', '-1');
            flash.focus();
        }

        // Show warning alert for success or error messages
        @if(session('success'))
            setTimeout(function() {
                alert('✅ Pembayaran Berhasil!\n\n{{ session('success') }}');
            }, 500);
        @endif

        @if(session('error'))
            // Show detailed error modal
            setTimeout(function() {
                showErrorModal('{{ session('error') }}');
            }, 300);
        @endif

        // Function to show error modal
        function showErrorModal(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorModal').classList.remove('hidden');
        }

        // Close error modal
        document.getElementById('closeErrorModal').addEventListener('click', function() {
            document.getElementById('errorModal').classList.add('hidden');
        });

        // Close error modal when clicking outside
        document.getElementById('errorModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // ==================== DP MODAL FUNCTIONALITY ====================
        let dpData = [];
        let selectedDP = null;

        // Open DP Modal
        document.getElementById('btnPilihDP').addEventListener('click', function() {
            openDPModal();
        });

        // Close DP Modal
        document.getElementById('closeDPModal').addEventListener('click', closeDPModal);
        document.getElementById('cancelDPSelection').addEventListener('click', closeDPModal);

        // Confirm DP Selection
        document.getElementById('confirmDPSelection').addEventListener('click', function() {
            if (selectedDP) {
                selectDP(selectedDP);
                closeDPModal();
            }
        });

        // DP Search
        document.getElementById('dpSearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filterDPTable(searchTerm);
        });

        // Clear DP Selection
        document.getElementById('clearDPSelection').addEventListener('click', function() {
            clearDPSelection();
        });

        // Functions
        function openDPModal() {
            document.getElementById('dpModal').classList.remove('hidden');
            document.getElementById('dpLoading').classList.remove('hidden');
            document.getElementById('dpContent').classList.add('hidden');
            loadDPData();
        }

        function closeDPModal() {
            document.getElementById('dpModal').classList.add('hidden');
            selectedDP = null;
            document.getElementById('confirmDPSelection').disabled = true;
        }

        function loadDPData() {
            fetch('{{ route("pembayaran-pranota-kontainer.get-available-dp") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('dpLoading').classList.add('hidden');

                    if (data.success) {
                        dpData = data.data;
                        if (dpData.length > 0) {
                            renderDPTable(dpData);
                            document.getElementById('dpContent').classList.remove('hidden');
                        } else {
                            document.getElementById('dpNoData').classList.remove('hidden');
                        }
                    } else {
                        alert('Error loading DP data: ' + data.message);
                        closeDPModal();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('dpLoading').classList.add('hidden');
                    alert('Error loading DP data');
                    closeDPModal();
                });
        }

        function renderDPTable(data) {
            const tbody = document.getElementById('dpTableBody');
            tbody.innerHTML = '';

            data.forEach(dp => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50 cursor-pointer';
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="radio" name="selectedDP" value="${dp.id}" class="text-indigo-600 focus:ring-indigo-500">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${dp.nomor_pembayaran}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${dp.tanggal_pembayaran}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">${dp.total_formatted}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${dp.bank_name}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        <div class="max-w-xs truncate" title="${dp.aktivitas_pembayaran}">
                            ${dp.aktivitas_pembayaran.substring(0, 50)}${dp.aktivitas_pembayaran.length > 50 ? '...' : ''}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${dp.creator_name}</td>
                `;

                // Add click event to select radio
                row.addEventListener('click', function() {
                    const radio = row.querySelector('input[type="radio"]');
                    radio.checked = true;
                    selectedDP = dp;
                    document.getElementById('confirmDPSelection').disabled = false;
                });

                // Add change event to radio
                row.querySelector('input[type="radio"]').addEventListener('change', function() {
                    if (this.checked) {
                        selectedDP = dp;
                        document.getElementById('confirmDPSelection').disabled = false;
                    }
                });

                tbody.appendChild(row);
            });
        }

        function filterDPTable(searchTerm) {
            const filteredData = dpData.filter(dp =>
                dp.nomor_pembayaran.toLowerCase().includes(searchTerm) ||
                dp.aktivitas_pembayaran.toLowerCase().includes(searchTerm) ||
                dp.bank_name.toLowerCase().includes(searchTerm)
            );
            renderDPTable(filteredData);
        }

        function selectDP(dp) {
            // Set hidden inputs
            document.getElementById('selectedDPId').value = dp.id;
            document.getElementById('selectedDPAmount').value = dp.total_pembayaran;

            // Show selected DP info
            document.getElementById('selectedDPText').textContent =
                `DP terpilih: ${dp.nomor_pembayaran} - ${dp.total_formatted}`;
            document.getElementById('selectedDPInfo').classList.remove('hidden');

            // Show DP label on total akhir
            document.getElementById('dpLabel').classList.remove('hidden');

            // Change button text
            document.getElementById('btnPilihDP').innerHTML =
                '<i class="fas fa-edit mr-2"></i>Ubah DP';

            // Recalculate total after DP selection
            updateTotalSetelahPenyesuaian();
        }

        function clearDPSelection() {
            // Clear hidden inputs
            document.getElementById('selectedDPId').value = '';
            document.getElementById('selectedDPAmount').value = '';

            // Hide selected DP info
            document.getElementById('selectedDPInfo').classList.add('hidden');

            // Hide DP label on total akhir
            document.getElementById('dpLabel').classList.add('hidden');

            // Reset button text
            document.getElementById('btnPilihDP').innerHTML =
                '<i class="fas fa-money-bill-wave mr-2"></i>Pilih DP';

            // Recalculate total after DP removal
            updateTotalSetelahPenyesuaian();
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectedDPAmountInput = document.getElementById('selectedDPAmount');
        const totalPembayaranInput = document.getElementById('total_pembayaran');
        const totalPenyesuaianInput = document.getElementById('total_tagihan_penyesuaian');
        const totalSetelahInput = document.getElementById('total_tagihan_setelah_penyesuaian');

        function updateTotalSetelahPenyesuaian() {
            const totalPembayaran = parseFloat(totalPembayaranInput.value.replace(/\./g, '').replace(',', '.')) || 0;
            const totalPenyesuaian = parseFloat(totalPenyesuaianInput.value) || 0;
            const dpAmount = parseFloat(selectedDPAmountInput.value) || 0;

            // Total = (Total Pembayaran + Penyesuaian) - DP Amount
            const totalAkhir = (totalPembayaran + totalPenyesuaian) - dpAmount;
            totalSetelahInput.value = totalAkhir.toLocaleString('id-ID');

            // Update detail perhitungan jika ada DP
            if (dpAmount > 0) {
                document.getElementById('detailTagihan').textContent = 'Rp ' + totalPembayaran.toLocaleString('id-ID');
                document.getElementById('detailPenyesuaian').textContent = 'Rp ' + totalPenyesuaian.toLocaleString('id-ID');
                document.getElementById('detailDPAmount').textContent = '- Rp ' + dpAmount.toLocaleString('id-ID');
                document.getElementById('detailTotalAkhir').textContent = 'Rp ' + totalAkhir.toLocaleString('id-ID');
                document.getElementById('dpCalculationDetail').classList.remove('hidden');
            } else {
                document.getElementById('dpCalculationDetail').classList.add('hidden');
            }
        }

        // Listen to input event on hidden DP amount input
        selectedDPAmountInput.addEventListener('input', updateTotalSetelahPenyesuaian);
    });
</script>
@endsection



