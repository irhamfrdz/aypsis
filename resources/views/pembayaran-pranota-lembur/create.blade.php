@extends('layouts.app')

@section('title', 'Form Pembayaran Pranota Lembur')
@section('page_title', 'Form Pembayaran Pranota Lembur')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-4 max-w-6xl mx-auto">
        @php
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

        <!-- Header dengan Filter Tanggal (Compact) -->
        <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Filter Pranota Lembur</h3>
                </div>
                <form action="{{ route('pembayaran-pranota-lembur.create') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
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
                        <a href="{{ route('pembayaran-pranota-lembur.create') }}" class="inline-flex justify-center py-1.5 px-3 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <form id="pembayaranForm" action="{{ route('pembayaran-pranota-lembur.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf

            <!-- Data Pembayaran & Bank -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Data Pembayaran -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Data Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label for="nomor_pembayaran" class="{{ $labelClasses }}">Nomor Pembayaran <span class="text-red-500">*</span></label>
                                <div class="flex gap-1">
                                    <input type="text" name="nomor_pembayaran" id="nomor_pembayaran"
                                        value="{{ $nomorPembayaran ?? old('nomor_pembayaran') }}"
                                        class="{{ $readonlyInputClasses }}" readonly required>
                                    <button type="button" id="generateNomorBtn" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded transition-colors" title="Generate nomor pembayaran">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label for="nomor_accurate" class="{{ $labelClasses }}">Nomor Accurate</label>
                                <input type="text" name="nomor_accurate" id="nomor_accurate"
                                    value="{{ old('nomor_accurate') }}"
                                    class="{{ $inputClasses }}" placeholder="Masukkan nomor accurate">
                            </div>
                            <div>
                                <label for="tanggal_pembayaran" class="{{ $labelClasses }}">Tanggal Pembayaran</label>
                                <input type="date" name="tanggal_pembayaran" id="tanggal_pembayaran"
                                    value="{{ old('tanggal_pembayaran', now()->toDateString()) }}"
                                    class="{{ $inputClasses }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank & Transaksi -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Bank & Transaksi (Double Book)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div class="relative">
                                <label for="bank" class="{{ $labelClasses }}">Pilih Bank</label>
                                <select name="bank" id="bank" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Bank --</option>
                                    @foreach($akunCoa as $akun)
                                        <option value="{{ $akun->nama_akun }}" {{ old('bank') == $akun->nama_akun ? 'selected' : '' }}>
                                            {{ $akun->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="jenis_transaksi" class="{{ $labelClasses }}">Jenis Transaksi</label>
                                <select name="jenis_transaksi" id="jenis_transaksi" class="{{ $inputClasses }}" required>
                                    <option value="Debit" {{ old('jenis_transaksi') == 'Debit' ? 'selected' : '' }}>Debit</option>
                                    <option value="Kredit" {{ old('jenis_transaksi', 'Kredit') == 'Kredit' ? 'selected' : '' }}>Kredit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pilih Pranota Lembur -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200 flex justify-between items-center">
                    <h4 class="text-sm font-semibold text-gray-800">Pilih Pranota Lembur</h4>
                    <div class="flex items-center gap-2">
                        <input type="text" id="searchPranota" placeholder="Cari nomor pranota..." class="px-3 py-1.5 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-64">
                    </div>
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
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody id="pranotaTableBody" class="bg-white divide-y divide-gray-200">
                            @forelse ($pranotaLemburs as $pranota)
                                <tr class="pranota-row hover:bg-gray-50 transition-colors">
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <input type="checkbox" name="pranota_lembur_ids[]" value="{{ $pranota->id }}" class="pranota-checkbox h-3 w-3 text-indigo-600 border-gray-300 rounded" data-total="{{ $pranota->total_setelah_adjustment }}">
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">{{ $pranota->nomor_pranota }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold">Rp {{ number_format($pranota->total_setelah_adjustment, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-2 py-4 text-center text-xs text-gray-500">Tidak ada pranota lembur yang tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Total Pembayaran -->
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                <h4 class="text-sm font-semibold text-gray-800 mb-2">Total Pembayaran</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                    <div>
                        <label for="total_pembayaran" class="{{ $labelClasses }}">Total Tagihan</label>
                        <input type="number" name="total_pembayaran" id="total_pembayaran" value="0" class="{{ $readonlyInputClasses }}" readonly>
                    </div>
                    <div>
                        <label for="total_tagihan_penyesuaian" class="{{ $labelClasses }}">Penyesuaian</label>
                        <input type="number" name="total_tagihan_penyesuaian" id="total_tagihan_penyesuaian" class="{{ $inputClasses }}" value="0">
                    </div>
                    <div>
                        <label for="total_tagihan_setelah_penyesuaian" class="{{ $labelClasses }}">Total Akhir</label>
                        <input type="number" name="total_tagihan_setelah_penyesuaian" id="total_tagihan_setelah_penyesuaian" class="{{ $readonlyInputClasses }} font-bold text-gray-800 bg-gray-100" readonly value="0">
                    </div>
                </div>
                <div class="mt-2">
                    <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="2" class="{{ $inputClasses }}" placeholder="Tambahkan keterangan...">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-2">
                <a href="{{ route('pembayaran-pranota-lembur.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('select-all');
        const pranotaCheckboxes = document.querySelectorAll('.pranota-checkbox');
        const totalPembayaranInput = document.getElementById('total_pembayaran');
        const totalPenyesuaianInput = document.getElementById('total_tagihan_penyesuaian');
        const totalSetelahInput = document.getElementById('total_tagihan_setelah_penyesuaian');
        const searchInput = document.getElementById('searchPranota');

        function updateTotal() {
            let total = 0;
            pranotaCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    total += parseFloat(checkbox.dataset.total) || 0;
                }
            });
            totalPembayaranInput.value = total;
            updateFinalTotal();
        }

        function updateFinalTotal() {
            const total = parseFloat(totalPembayaranInput.value) || 0;
            const adjustment = parseFloat(totalPenyesuaianInput.value) || 0;
            totalSetelahInput.value = total + adjustment;
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () {
                pranotaCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateTotal();
            });
        }

        pranotaCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateTotal);
        });

        totalPenyesuaianInput.addEventListener('input', updateFinalTotal);

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                document.querySelectorAll('.pranota-row').forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        const generateBtn = document.getElementById('generateNomorBtn');
        const nomorInput = document.getElementById('nomor_pembayaran');

        if (generateBtn) {
            generateBtn.addEventListener('click', function() {
                generateBtn.disabled = true;
                fetch('{{ route("pembayaran-pranota-lembur.generate-nomor") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            nomorInput.value = data.nomor_pembayaran;
                        }
                    })
                    .finally(() => {
                        generateBtn.disabled = false;
                    });
            });
        }
    });
</script>
@endsection
