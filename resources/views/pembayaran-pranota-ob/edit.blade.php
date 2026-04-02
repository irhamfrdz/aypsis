@extends('layouts.app')

@section('title', 'Edit Pembayaran Pranota OB')
@section('page_title', 'Edit Pembayaran Pranota OB')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-4 max-w-6xl mx-auto">
        @php
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors";
            $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-sm p-2";
            $labelClasses = "block text-xs font-medium text-gray-700 mb-1";
        @endphp
        
        <style>
            .hidden { display: none !important; }
            .select2-container { width: 100% !important; }
            .select2-container .select2-selection--single {
                height: 38px !important;
                padding: 1px 1px !important;
                border: 1px solid #d1d5db !important;
                border-radius: 0.375rem !important;
                background-color: #f9fafb !important;
            }
            .select2-container .select2-selection--single .select2-selection__rendered {
                line-height: 36px !important;
                font-size: 0.875rem !important;
                padding-left: 10px !important;
            }
            .select2-container .select2-selection--single .select2-selection__arrow {
                height: 36px !important;
            }
        </style>
        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        {{-- Display current data info --}}
        <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-blue-800">Informasi Pembayaran</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <div class="flex flex-wrap gap-3">
                            <div>
                                <span class="font-semibold">Nomor:</span>
                                <span class="ml-1 px-2 py-0.5 bg-blue-100 rounded">{{ $pembayaran->nomor_pembayaran }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">Kapal:</span>
                                <span class="ml-1 px-2 py-0.5 bg-blue-100 rounded">{{ $pembayaran->kapal ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">Voyage:</span>
                                <span class="ml-1 px-2 py-0.5 bg-blue-100 rounded">{{ $pembayaran->voyage ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

        <form id="pembayaranForm" action="{{ route('pembayaran-pranota-ob.update', $pembayaran->id) }}" method="POST" class="space-y-3">
            @csrf
            @method('PUT')

            {{-- Hidden inputs --}}
            <input type="hidden" name="breakdown_supir" id="breakdown_supir_hidden" value='{{ is_string($pembayaran->breakdown_supir) ? $pembayaran->breakdown_supir : json_encode($pembayaran->breakdown_supir) }}'>

            <!-- Data Pembayaran & Bank -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Data Pembayaran -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Data Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div>
                                <label for="nomor_pembayaran" class="{{ $labelClasses }}">Nomor Pembayaran</label>
                                <input type="text" name="nomor_pembayaran" id="nomor_pembayaran"
                                    value="{{ $pembayaran->nomor_pembayaran }}"
                                    class="{{ $readonlyInputClasses }}" readonly>
                            </div>
                            <div>
                                <label for="nomor_accurate" class="{{ $labelClasses }}">Nomor Accurate</label>
                                <input type="text" name="nomor_accurate" id="nomor_accurate"
                                    value="{{ old('nomor_accurate', $pembayaran->nomor_accurate) }}"
                                    placeholder="Masukkan nomor accurate (opsional)"
                                    class="{{ $inputClasses }}">
                            </div>
                            <div>
                                <label for="tanggal_kas" class="{{ $labelClasses }}">Tanggal Kas</label>
                                <input type="date" name="tanggal_kas" id="tanggal_kas"
                                    value="{{ old('tanggal_kas', \Carbon\Carbon::parse($pembayaran->tanggal_kas)->format('Y-m-d')) }}"
                                    class="{{ $inputClasses }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank & Transaksi (Double Book) -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 h-full">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-semibold text-gray-800">Double Book Accounting</h4>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                📊 Jurnal Preview
                            </span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="md:col-span-2">
                                <label for="debit_kredit" class="{{ $labelClasses }}">Jenis Transaksi <span class="text-red-500">*</span></label>
                                <select name="debit_kredit" id="debit_kredit" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Jenis Transaksi --</option>
                                    <option value="credit" {{ old('debit_kredit', $pembayaran->jenis_transaksi) == 'credit' ? 'selected' : ($pembayaran->jenis_transaksi == 'kredit' ? 'selected' : '') }}>KREDIT (Biaya/Beban bertambah, Bank berkurang)</option>
                                    <option value="debit" {{ old('debit_kredit', $pembayaran->jenis_transaksi) == 'debit' ? 'selected' : '' }}>DEBIT (Bank bertambah, Biaya/Beban berkurang)</option>
                                </select>
                            </div>

                            <div>
                                <label for="akun_bank_id" class="{{ $labelClasses }}">Bank/Kas <span class="text-red-500">*</span></label>
                                <select name="akun_bank_id" id="akun_bank_id" class="{{ $inputClasses }} coa-select" required>
                                    <option value="">-- Pilih Bank --</option>
                                    @foreach($akunBank as $akun)
                                        <option value="{{ $akun->id }}" 
                                                data-nama="{{ $akun->nama_akun }}"
                                                {{ old('akun_bank_id', $pembayaran->akun_bank_id) == $akun->id ? 'selected' : '' }}>
                                            {{ $akun->nomor_akun }} - {{ $akun->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="akun_coa_id" class="{{ $labelClasses }}">Akun Biaya <span class="text-red-500">*</span></label>
                                <select name="akun_coa_id" id="akun_coa_id" class="{{ $inputClasses }} coa-select" required>
                                    <option value="">-- Pilih Akun Biaya --</option>
                                    @foreach($akunBiaya as $akun)
                                        <option value="{{ $akun->id }}" 
                                                data-nama="{{ $akun->nama_akun }}"
                                                {{ old('akun_coa_id', $pembayaran->akun_coa_id) == $akun->id ? 'selected' : '' }}>
                                            {{ $akun->nomor_akun }} - {{ $akun->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Journal Preview -->
                            <div id="journal_preview" class="md:col-span-2 mt-1 p-2 bg-white border border-blue-200 rounded text-[11px] hidden transition-all duration-300">
                                <p class="font-bold text-gray-700 mb-1">Preview Jurnal Akuntansi:</p>
                                <div id="journal_content" class="text-gray-600"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Breakdown Per Supir --}}
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800">Breakdown Per Supir</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="supir-breakdown-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Supir</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">DP</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pot. Utang</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pot. Tabungan</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pot. BPJS</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Grand Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="supir-breakdown-body">
                            @php
                                $breakdownSupir = $pembayaran->breakdown_supir;
                                if (is_string($breakdownSupir)) {
                                    $breakdownSupir = json_decode($breakdownSupir, true) ?? [];
                                }
                                $breakdownSupir = is_array($breakdownSupir) ? $breakdownSupir : [];
                            @endphp
                            @forelse($breakdownSupir as $breakdown)
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap text-xs font-medium text-gray-900">
                                    {{ $breakdown['nama_supir'] ?? ($breakdown['supir'] ?? '-') }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-center text-xs text-gray-900">{{ $breakdown['jumlah_item'] ?? ($breakdown['items'] ?? 0) }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-right text-xs font-semibold text-gray-900">Rp {{ number_format($breakdown['total_biaya'] ?? ($breakdown['biaya'] ?? 0), 0, ',', '.') }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-right text-xs text-green-700">Rp {{ number_format($breakdown['dp'] ?? 0, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-right text-xs text-red-700">Rp {{ number_format($breakdown['sisa'] ?? 0, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-right text-xs">
                                    @php
                                        $supirName = $breakdown['nama_supir'] ?? ($breakdown['supir'] ?? 'Unknown');
                                        $supirSlug = \Illuminate\Support\Str::slug($supirName);
                                    @endphp
                                    <input type="number" 
                                           class="potongan-utang w-24 px-2 py-1 text-xs border border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                           data-supir="{{ $supirSlug }}"
                                           value="{{ $breakdown['potongan_utang'] ?? 0 }}"
                                           min="0"
                                           step="1">
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-right text-xs">
                                    <input type="number" 
                                           class="potongan-tabungan w-24 px-2 py-1 text-xs border border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                           data-supir="{{ $supirSlug }}"
                                           value="{{ $breakdown['potongan_tabungan'] ?? 0 }}"
                                           min="0"
                                           step="1">
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-right text-xs">
                                    <input type="number" 
                                           class="potongan-bpjs w-24 px-2 py-1 text-xs border border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                           data-supir="{{ $supirSlug }}"
                                           value="{{ $breakdown['potongan_bpjs'] ?? 0 }}"
                                           min="0"
                                           step="1">
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-right text-xs font-bold text-blue-800 grand-total-cell" data-supir="{{ $supirSlug }}">
                                    Rp {{ number_format(($breakdown['sisa'] ?? 0) - ($breakdown['potongan_utang'] ?? 0) - ($breakdown['potongan_tabungan'] ?? 0) - ($breakdown['potongan_bpjs'] ?? 0), 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-3 py-4 text-center text-xs text-gray-500 italic">
                                    Tidak ada data breakdown supir
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if(!empty($breakdownSupir))
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td class="px-3 py-2 text-left text-xs font-bold text-gray-800">Total</td>
                                <td class="px-3 py-2 text-center text-xs font-bold text-gray-800" id="total-items">{{ array_sum(array_map(function($b) { return $b['jumlah_item'] ?? ($b['items'] ?? 0); }, $breakdownSupir)) }}</td>
                                <td class="px-3 py-2 text-right text-xs font-bold text-gray-800" id="total-biaya">Rp {{ number_format(array_sum(array_map(function($b) { return $b['total_biaya'] ?? ($b['biaya'] ?? 0); }, $breakdownSupir)), 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right text-xs font-bold text-green-800" id="total-dp">Rp {{ number_format(array_sum(array_column($breakdownSupir, 'dp')), 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right text-xs font-bold text-red-800" id="total-sisa">Rp {{ number_format(array_sum(array_column($breakdownSupir, 'sisa')), 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right text-xs font-bold text-orange-800" id="total-pot-utang">Rp {{ number_format(array_sum(array_column($breakdownSupir, 'potongan_utang')), 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right text-xs font-bold text-orange-800" id="total-pot-tabungan">Rp {{ number_format(array_sum(array_column($breakdownSupir, 'potongan_tabungan')), 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right text-xs font-bold text-orange-800" id="total-pot-bpjs">Rp {{ number_format(array_sum(array_column($breakdownSupir, 'potongan_bpjs')), 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right text-xs font-bold text-blue-800" id="total-grand-total">
                                    Rp {{ number_format(array_sum(array_map(function($b) { return ($b['sisa'] ?? 0) - ($b['potongan_utang'] ?? 0) - ($b['potongan_tabungan'] ?? 0) - ($b['potongan_bpjs'] ?? 0); }, $breakdownSupir)), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
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
                                    value="{{ number_format($pembayaran->total_pembayaran ?? 0, 0, ',', '.') }}"
                                    class="{{ $readonlyInputClasses }}" readonly>
                            </div>
                            <div>
                                <label for="penyesuaian" class="{{ $labelClasses }}">Penyesuaian</label>
                                <input type="text" name="penyesuaian" id="penyesuaian"
                                    value="{{ old('penyesuaian', number_format($pembayaran->penyesuaian ?? 0, 0, ',', '.')) }}"
                                    class="{{ $inputClasses }}">
                            </div>
                            <div>
                                <label for="total_setelah_penyesuaian" class="{{ $labelClasses }}">Total Akhir</label>
                                <input type="text" name="total_setelah_penyesuaian" id="total_setelah_penyesuaian"
                                    value="{{ number_format($pembayaran->total_setelah_penyesuaian ?? $pembayaran->total_pembayaran ?? 0, 0, ',', '.') }}"
                                    class="{{ $readonlyInputClasses }} font-bold text-gray-800 bg-gray-100" readonly>
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
                                    class="{{ $inputClasses }}" placeholder="Jelaskan alasan penyesuaian...">{{ old('alasan_penyesuaian', $pembayaran->alasan_penyesuaian) }}</textarea>
                            </div>
                            <div>
                                <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" rows="2"
                                    class="{{ $inputClasses }}" placeholder="Tambahkan keterangan...">{{ old('keterangan', $pembayaran->keterangan) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit Buttons --}}
            <div class="flex justify-between items-center">
                <a href="{{ route('pembayaran-pranota-ob.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fas fa-save mr-2"></i> Update Pembayaran
                </button>
            </div>
        </form>
    </div>

{{-- Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Load Select2 JS dynamically
        if (typeof jQuery !== 'undefined') {
            const select2Script = document.createElement('script');
            select2Script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
            select2Script.onload = function() {
                initializeSelect2();
            };
            document.head.appendChild(select2Script);
        }

        function initializeSelect2() {
            $('.coa-select').select2({
                width: '100%'
            });
            $('#debit_kredit').select2({
                width: '100%',
                minimumResultsForSearch: Infinity
            });

            // Trigger preview on change
            $('.coa-select, #debit_kredit').on('change', updateJournalPreview);
            updateJournalPreview(); // Initial preview
        }

        function updateJournalPreview() {
            const journalPreview = document.getElementById('journal_preview');
            const journalContent = document.getElementById('journal_content');
            const debitKredit = $('#debit_kredit').val();
            const akunBank = $('#akun_bank_id').select2('data') ? $('#akun_bank_id').select2('data')[0] : null;
            const akunBiaya = $('#akun_coa_id').select2('data') ? $('#akun_coa_id').select2('data')[0] : null;
            const totalText = document.getElementById('total_setelah_penyesuaian').value;

            if (!debitKredit || !akunBank || !akunBank.id || !akunBiaya || !akunBiaya.id) {
                journalPreview.classList.add('hidden');
                return;
            }

            const bankText = akunBank.element.dataset.nama || akunBank.text.split(' - ')[1];
            const biayaText = akunBiaya.element.dataset.nama || akunBiaya.text.split(' - ')[1];
            const amountFormatted = 'Rp ' + totalText;

            let html = '';
            if (debitKredit === 'credit') {
                html = `
                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-green-50 p-2 rounded border border-green-100">
                            <p class="text-green-800 font-bold text-[10px]">DEBIT (+)</p>
                            <p class="text-green-700 font-medium truncate">${biayaText}</p>
                            <p class="text-green-800 font-bold">${amountFormatted}</p>
                        </div>
                        <div class="bg-red-50 p-2 rounded border border-red-100">
                            <p class="text-red-800 font-bold text-[10px]">KREDIT (-)</p>
                            <p class="text-red-700 font-medium truncate">${bankText}</p>
                            <p class="text-red-800 font-bold">${amountFormatted}</p>
                        </div>
                    </div>
                `;
            } else {
                html = `
                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-green-50 p-2 rounded border border-green-100">
                            <p class="text-green-800 font-bold text-[10px]">DEBIT (+)</p>
                            <p class="text-green-700 font-medium truncate">${bankText}</p>
                            <p class="text-green-800 font-bold">${amountFormatted}</p>
                        </div>
                        <div class="bg-red-50 p-2 rounded border border-red-100">
                            <p class="text-red-800 font-bold text-[10px]">KREDIT (-)</p>
                            <p class="text-red-700 font-medium truncate">${biayaText}</p>
                            <p class="text-red-800 font-bold">${amountFormatted}</p>
                        </div>
                    </div>
                `;
            }

            journalContent.innerHTML = html;
            journalPreview.classList.remove('hidden');
        }

        // Get all potongan inputs
        const potonganInputs = document.querySelectorAll('.potongan-utang, .potongan-tabungan, .potongan-bpjs');
        
        // Function to update grand total for a supir
        function updateGrandTotal(supirSlug) {
            const breakdown = getCurrentBreakdown();
            const supirData = breakdown.find(item => {
                const supirName = item.nama_supir || item.supir || 'Unknown';
                const itemSlug = supirName.toLowerCase().replace(/\s+/g, '-');
                const cleanSlug = supirSlug.toLowerCase().replace(/\s+/g, '-');
                return itemSlug === cleanSlug;
            });
            
            if (supirData) {
                const grandTotalCell = document.querySelector(`.grand-total-cell[data-supir="${supirSlug}"]`);
                if (grandTotalCell) {
                    const grandTotal = supirData.grand_total;
                    grandTotalCell.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
                }
            }
            
            updateFooterTotals();
        }
        
        // Function to get current breakdown data
        function getCurrentBreakdown() {
            const breakdown = [];
            const breakdownHidden = document.getElementById('breakdown_supir_hidden');
            
            try {
                const existingBreakdown = JSON.parse(breakdownHidden.value || '[]');
                
                existingBreakdown.forEach(item => {
                    const supirName = item.nama_supir || item.supir || 'Unknown';
                    const supirSlug = supirName.toLowerCase().replace(/\s+/g, '-');
                    const potUtang = parseFloat(document.querySelector(`.potongan-utang[data-supir="${supirSlug}"]`)?.value) || 0;
                    const potTabungan = parseFloat(document.querySelector(`.potongan-tabungan[data-supir="${supirSlug}"]`)?.value) || 0;
                    const potBpjs = parseFloat(document.querySelector(`.potongan-bpjs[data-supir="${supirSlug}"]`)?.value) || 0;
                    
                    item.potongan_utang = potUtang;
                    item.potongan_tabungan = potTabungan;
                    item.potongan_bpjs = potBpjs;
                    item.grand_total = item.sisa - potUtang - potTabungan - potBpjs;
                    
                    breakdown.push(item);
                });
                
                breakdownHidden.value = JSON.stringify(breakdown);
            } catch (e) {
                console.error('Error updating breakdown:', e);
            }
            
            return breakdown;
        }
        
        // Function to update footer totals
        function updateFooterTotals() {
            const breakdown = getCurrentBreakdown();
            
            let totalPotUtang = 0;
            let totalPotTabungan = 0;
            let totalPotBpjs = 0;
            let totalGrandTotal = 0;
            
            breakdown.forEach(item => {
                totalPotUtang += item.potongan_utang || 0;
                totalPotTabungan += item.potongan_tabungan || 0;
                totalPotBpjs += item.potongan_bpjs || 0;
                totalGrandTotal += item.grand_total || 0;
            });
            
            document.getElementById('total-pot-utang').textContent = 'Rp ' + totalPotUtang.toLocaleString('id-ID');
            document.getElementById('total-pot-tabungan').textContent = 'Rp ' + totalPotTabungan.toLocaleString('id-ID');
            document.getElementById('total-pot-bpjs').textContent = 'Rp ' + totalPotBpjs.toLocaleString('id-ID');
            document.getElementById('total-grand-total').textContent = 'Rp ' + totalGrandTotal.toLocaleString('id-ID');
            
            // Update total pembayaran
            document.getElementById('total_pembayaran').value = totalGrandTotal.toLocaleString('id-ID');
            updateTotalAkhir();
        }
        
        // Add event listeners to potongan inputs
        potonganInputs.forEach(input => {
            input.addEventListener('input', function() {
                const supirSlug = this.getAttribute('data-supir');
                updateGrandTotal(supirSlug);
            });
        });
        
        // Penyesuaian change
        document.getElementById('penyesuaian').addEventListener('input', updateTotalAkhir);
        
        // Function to update total akhir
        function updateTotalAkhir() {
            const total = parseFloat(document.getElementById('total_pembayaran').value.replace(/\./g, '').replace(',', '.')) || 0;
            const penyesuaian = parseFloat(document.getElementById('penyesuaian').value.replace(/\./g, '').replace(',', '.')) || 0;
            document.getElementById('total_setelah_penyesuaian').value = (total + penyesuaian).toLocaleString('id-ID');
            updateJournalPreview();
        }
        
        // Format penyesuaian on focus/blur
        const penyesuaianInput = document.getElementById('penyesuaian');
        penyesuaianInput.addEventListener('focus', function() {
            this.value = this.value.replace(/\./g, '').replace(',', '.');
        });
        penyesuaianInput.addEventListener('blur', function() {
            const num = parseFloat(this.value) || 0;
            this.value = num.toLocaleString('id-ID');
            updateTotalAkhir();
        });

        // Initial calls to synchronize totals and preview
        updateFooterTotals();
    });
</script>
@endsection
