@extends('layouts.app')

@section('title', 'Form Pembayaran Pranota Uang Jalan Bongkaran')
@section('page_title', 'Form Pembayaran Pranota Uang Jalan Bongkaran')

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

        <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Filter Pranota Uang Jalan Bongkaran</h3>
                </div>
                <form action="{{ route('pembayaran-pranota-uang-jalan-bongkaran.create') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
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
                        <a href="{{ route('pembayaran-pranota-uang-jalan-bongkaran.create') }}" class="inline-flex justify-center py-1.5 px-3 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <form id="pembayaranForm" action="{{ route('pembayaran-pranota-uang-jalan-bongkaran.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Data Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label for="nomor_pembayaran" class="{{ $labelClasses }}">Nomor Pembayaran <span class="text-red-500">*</span></label>
                                <div class="flex gap-1">
                                    <input type="text" name="nomor_pembayaran" id="nomor_pembayaran"
                                        value="{{ $nomorPembayaran ?? old('nomor_pembayaran') }}"
                                        class="{{ $readonlyInputClasses }}" readonly required placeholder="Auto generate">
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
                                <label for="tanggal_kas" class="{{ $labelClasses }}">Tanggal Kas</label>
                                <input type="date" name="tanggal_kas" id="tanggal_kas"
                                    value="{{ old('tanggal_kas', now()->toDateString()) }}"
                                    class="{{ $inputClasses }}" required>
                                <input type="hidden" name="tanggal_pembayaran" id="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', now()->toDateString()) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Bank & Transaksi (Double Book Accounting)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div class="relative">
                                <label for="bank" class="{{ $labelClasses }}">Pilih Bank</label>
                                <div class="relative">
                                    <input type="text" id="bankSearch" placeholder="Cari bank..." class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors pr-8" autocomplete="off">
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
                                    </div>
                                    <div id="noBankResults" class="hidden px-3 py-2 text-xs text-gray-500 text-center">Tidak ada bank yang sesuai</div>
                                </div>
                            </div>

                            <div>
                                <label for="jenis_transaksi" class="{{ $labelClasses }}">Jenis Transaksi</label>
                                <select name="jenis_transaksi" id="jenis_transaksi" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Debit" {{ old('jenis_transaksi') == 'Debit' ? 'selected' : '' }}>Debit (Bank +, Biaya -)</option>
                                    <option value="Kredit" {{ old('jenis_transaksi', 'Kredit') == 'Kredit' ? 'selected' : '' }}>Kredit (Biaya +, Bank -)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pilih Pranota Uang Jalan Bongkaran -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-semibold text-gray-800">Pilih Pranota Uang Jalan Bongkaran</h4>
                        <div class="flex items-center gap-2">
                            <input type="text" id="searchPranota" placeholder="Cari nomor pranota, supir, tujuan..." class="px-3 py-1.5 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-64">
                            <span id="searchCounter" class="text-xs text-gray-600"></span>
                        </div>
                    </div>
                    <p class="text-xs text-blue-600">
                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                        <strong>Info:</strong> Pembayaran akan otomatis dicatat ke akun terkait dan Bank yang dipilih menggunakan sistem double book accounting. Uang jalan bongkaran <strong>tidak</strong> akan otomatis membuat data Prospek.
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
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan Ambil</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Uang Jalan</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody id="pranotaTableBody" class="bg-white divide-y divide-gray-200">
                            @forelse ($pranotaUangJalans as $pranota)
                                <tr class="pranota-row hover:bg-gray-50 transition-colors" data-search="{{ strtolower($pranota->nomor_pranota ?? '') }}">
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <input type="checkbox" name="pranota_uang_jalan_bongkaran_ids[]" value="{{ $pranota->id }}" class="pranota-checkbox h-3 w-3 text-indigo-600 border-gray-300 rounded" data-total="{{ $pranota->total_for_payment }}">
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">
                                        <a href="{{ route('pranota-uang-jalan-bongkaran.show', $pranota->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 hover:underline">
                                            {{ $pranota->nomor_pranota ?? '-' }}
                                        </a>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @if ($pranota->tanggal_pranota)
                                            {{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/M/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @if($pranota->uangJalanBongkarans && $pranota->uangJalanBongkarans->count() > 0)
                                            @php
                                                $supirs = collect();
                                                foreach($pranota->uangJalanBongkarans as $uangJalan) {
                                                    if($uangJalan->suratJalanBongkaran) {
                                                        if($uangJalan->suratJalanBongkaran->supir) {
                                                            $supirs->push($uangJalan->suratJalanBongkaran->supir);
                                                        }
                                                    }
                                                }
                                                $supirs = $supirs->filter()->unique();
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
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @if($pranota->uangJalanBongkarans && $pranota->uangJalanBongkarans->count() > 0)
                                            @php
                                                $tujuans = collect();
                                                foreach($pranota->uangJalanBongkarans as $uangJalan) {
                                                    if($uangJalan->suratJalanBongkaran) {
                                                        if($uangJalan->suratJalanBongkaran->tujuan_pengambilan) {
                                                            $tujuans->push($uangJalan->suratJalanBongkaran->tujuan_pengambilan);
                                                        }
                                                    }
                                                }
                                                $tujuans = $tujuans->filter()->unique();
                                            @endphp
                                            @if($tujuans->count() > 0)
                                                {{ $tujuans->take(2)->implode(', ') }}
                                                @if($tujuans->count() > 2)
                                                    <span class="text-gray-500 text-xs">(+{{ $tujuans->count() - 2 }} tujuan)</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        {{ $pranota->uangJalanBongkarans ? $pranota->uangJalanBongkarans->count() : 0 }} item
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold">Rp {{ number_format($pranota->total_for_payment, 0, ',', '.') }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <span class="px-1.5 py-0.5 inline-flex text-xs font-medium rounded bg-yellow-100 text-yellow-800">Belum Bayar</span>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyRow">
                                    <td colspan="9" class="px-2 py-4 text-center text-xs text-gray-500">Tidak ada pranota uang jalan bongkaran yang tersedia.</td>
                                </tr>
                            @endforelse
                            <tr id="noResultsRow" class="hidden">
                                <td colspan="9" class="px-2 py-4 text-center text-xs text-gray-500">Tidak ada hasil yang sesuai dengan pencarian.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                    <div class="flex items-start gap-2">
                        <p class="text-xs text-gray-600 flex-1">* Pilih satu atau lebih pranota uang jalan untuk dibayar.</p>
                        <div class="flex flex-col gap-1">
                            <p class="text-xs text-blue-600"><span class="px-1 py-0.5 bg-blue-100 text-blue-600 rounded">📊 Double Book</span> = Otomatis jurnal ke COA</p>
                            <p class="text-xs text-blue-600"><span class="px-1 py-0.5 bg-gray-100 text-gray-600 rounded">⚠ No Prospek</span> = Pembayaran Bongkaran tidak membuat Prospek</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <div class="lg:col-span-2">
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
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Informasi Tambahan</h4>
                        <div class="space-y-2">
                            <div>
                                <label for="alasan_penyesuaian" class="{{ $labelClasses }}">Alasan Penyesuaian</label>
                                <textarea name="alasan_penyesuaian" id="alasan_penyesuaian" rows="2" class="{{ $inputClasses }}" placeholder="Jelaskan alasan penyesuaian...">{{ old('alasan_penyesuaian') }}</textarea>
                            </div>
                            <div>
                                <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" rows="2" class="{{ $inputClasses }}" placeholder="Tambahkan keterangan...">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('pembayaran-pranota-uang-jalan-bongkaran.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">Batal</a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">Simpan Pembayaran</button>
            </div>
        </form>
    </div>

<script>
    // Auto-generate nomor pembayaran via AJAX (SIS)
    document.addEventListener('DOMContentLoaded', function () {
        const nomorPembayaranInput = document.getElementById('nomor_pembayaran');
        const generateBtn = document.getElementById('generateNomorBtn');

        function generateNomorPembayaran() {
            if (generateBtn) { generateBtn.disabled = true; }
            fetch('{{ route("pembayaran-pranota-uang-jalan-bongkaran.generate-nomor") }}', {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => { if (data.nomor_pembayaran) nomorPembayaranInput.value = data.nomor_pembayaran; else alert('Gagal generate nomor pembayaran'); })
            .catch(error => { console.error(error); alert('Terjadi kesalahan saat generate nomor pembayaran'); })
            .finally(() => { if (generateBtn) generateBtn.disabled = false; });
        }
        if (generateBtn) { generateBtn.addEventListener('click', generateNomorPembayaran); }
    });

    // Reuse scripts from pembayaran-pranota-uang-jalan create (bank dropdown, search filter, checkbox totals, validation)
    document.addEventListener('DOMContentLoaded', function () {
        // Bank search functionality
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
                if (!searchTerm) { bankSelect.value = ''; }
            });
            document.addEventListener('click', function(e) { if (!bankSearch.contains(e.target) && !bankDropdown.contains(e.target)) bankDropdown.classList.add('hidden'); });
            if (bankSelect.value) { const selected = bankSelect.options[bankSelect.selectedIndex]; if (selected) bankSearch.value = selected.text; }
            else { const bcaOption = Array.from(bankSelect.options).find(option => option.text.toLowerCase().includes('bca') && option.text.toLowerCase().includes('trucking')); if (bcaOption) { bankSelect.value = bcaOption.value; bankSearch.value = bcaOption.text; } }
            const form = document.getElementById('pembayaranForm'); if (form) { form.addEventListener('reset', function() { bankSearch.value = ''; bankSelect.value = ''; }); }
        }
    });

    // Pranota search/filter
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
                    const nomorPranota = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                    const supir = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
                    const tujuan = row.querySelector('td:nth-child(5)')?.textContent.toLowerCase() || '';
                    const tipe = row.querySelector('td:nth-child(6)')?.textContent.toLowerCase() || '';
                    const searchableText = nomorPranota + ' ' + supir + ' ' + tujuan + ' ' + tipe;
                    if (searchableText.includes(searchTerm)) { row.style.display = ''; visibleCount++; } else { row.style.display = 'none'; }
                });
                if (searchTerm) { searchCounter.textContent = `${visibleCount} dari ${totalCount} pranota`; } else { searchCounter.textContent = ''; }
                if (visibleCount === 0 && totalCount > 0) { if (noResultsRow) noResultsRow.classList.remove('hidden'); if (emptyRow) emptyRow.classList.add('hidden'); } else { if (noResultsRow) noResultsRow.classList.add('hidden'); if (emptyRow && totalCount === 0) emptyRow.classList.remove('hidden'); }
            });
        }
    });

    // Select/total validation + helper function
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('select-all');
        const pranotaCheckboxes = document.querySelectorAll('.pranota-checkbox');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () { pranotaCheckboxes.forEach(checkbox => { checkbox.checked = this.checked; }); updateTotalPembayaran(); });
        }
        const pembayaranForm = document.getElementById('pembayaranForm');
        if (pembayaranForm) {
            pembayaranForm.addEventListener('submit', function(e) {
                const checkedCheckboxes = document.querySelectorAll('.pranota-checkbox:checked');
                const bankSelect = document.getElementById('bank');
                const jenisTransaksiSelect = document.getElementById('jenis_transaksi');
                const nomorPembayaranInput = document.getElementById('nomor_pembayaran');
                if (!nomorPembayaranInput.value) { e.preventDefault(); showWarning('Nomor pembayaran belum digenerate. Silakan klik tombol refresh untuk generate nomor.', 'warning'); return false; }
                if (checkedCheckboxes.length === 0) { e.preventDefault(); showWarning('Silakan pilih minimal satu pranota uang jalan bongkaran.', 'warning'); return false; }
                if (!bankSelect.value) { e.preventDefault(); showWarning('Silakan pilih bank terlebih dahulu.', 'warning'); bankSelect.focus(); return false; }
                if (!jenisTransaksiSelect.value) { e.preventDefault(); showWarning('Silakan pilih jenis transaksi terlebih dahulu.', 'warning'); jenisTransaksiSelect.focus(); return false; }
                const totalPembayaran = parseFloat(document.getElementById('total_tagihan_setelah_penyesuaian')?.value) || 0;
                const bankName = bankSelect.options[bankSelect.selectedIndex].text;
                const jenisTransaksi = jenisTransaksiSelect.value;
                const confirmMessage = `Konfirmasi pembayaran:\n\nJumlah: Rp ${totalPembayaran.toLocaleString('id-ID')}\nBank: ${bankName}\nJenis: ${jenisTransaksi}\nPranota terpilih: ${checkedCheckboxes.length} item\n\nLanjutkan pembayaran?`;
                if (!confirm(confirmMessage)) { e.preventDefault(); return false; }
                showLoadingIndicator();
            });
        }

        function showWarning(message, type = 'error') {
            const warningDiv = document.createElement('div');
            warningDiv.className = `mb-3 p-3 rounded-lg border text-sm ${ type === 'warning' ? 'bg-yellow-50 border-yellow-200 text-yellow-800' : 'bg-red-50 border-red-200 text-red-800' }`;
            warningDiv.innerHTML = `<div class="flex items-center"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg><span>${message}</span><button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-lg leading-none">&times;</button></div>`;
            const form = document.getElementById('pembayaranForm'); form.parentNode.insertBefore(warningDiv, form);
            setTimeout(() => { if (warningDiv.parentNode) warningDiv.remove(); }, 5000);
        }
        function showLoadingIndicator() { const submitButton = document.querySelector('button[type="submit"]'); if (submitButton) { submitButton.disabled = true; submitButton.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan...`; } }

        const totalPembayaranInput = document.getElementById('total_pembayaran');
        const totalPenyesuaianInput = document.getElementById('total_tagihan_penyesuaian');
        const totalSetelahInput = document.getElementById('total_tagihan_setelah_penyesuaian');
        function updateTotalPembayaran() { let total = 0; pranotaCheckboxes.forEach(function(checkbox) { if (checkbox.checked) { const amount = parseFloat(checkbox.dataset.total) || 0; total += amount; } }); if (totalPembayaranInput) totalPembayaranInput.value = total; updateTotalSetelahPenyesuaian(); }
        function updateTotalSetelahPenyesuaian() { const totalPembayaran = parseFloat(totalPembayaranInput?.value) || 0; const totalPenyesuaian = parseFloat(totalPenyesuaianInput?.value) || 0; if (totalSetelahInput) totalSetelahInput.value = totalPembayaran + totalPenyesuaian; }
        pranotaCheckboxes.forEach(function(checkbox) { checkbox.addEventListener('change', updateTotalPembayaran); });
        if (totalPembayaranInput) totalPembayaranInput.addEventListener('input', updateTotalSetelahPenyesuaian); if (totalPenyesuaianInput) totalPenyesuaianInput.addEventListener('input', updateTotalSetelahPenyesuaian); updateTotalPembayaran();
    });

    document.addEventListener('DOMContentLoaded', function () {
        const tanggalPembayaran = document.getElementById('tanggal_pembayaran');
        if (tanggalPembayaran) {
            const tanggalKasInput = document.getElementById('tanggal_kas');
            if (tanggalKasInput) {
                tanggalPembayaran.value = tanggalKasInput.value || new Date().toISOString().split('T')[0];
                tanggalKasInput.addEventListener('change', function () { tanggalPembayaran.value = this.value || new Date().toISOString().split('T')[0]; });
            } else { tanggalPembayaran.value = new Date().toISOString().split('T')[0]; }
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const nomorPembayaranInput = document.getElementById('nomor_pembayaran');
        if (nomorPembayaranInput) {
            nomorPembayaranInput.addEventListener('blur', function() {
                const value = this.value.trim();
                if (value && value.length > 0) {
                    const formatRegex = /^[A-Z0-9]{1,3}-\d{2}-\d{2}-\d{6}$/;
                    if (!formatRegex.test(value)) { showWarning('Format nomor pembayaran tidak valid. Format yang benar: XXX-MM-YY-NNNNNN', 'warning'); }
                }
            });
        }
    });
</script>
@endsection