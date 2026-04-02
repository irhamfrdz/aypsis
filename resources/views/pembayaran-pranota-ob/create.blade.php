@extends('layouts.app')

@section('title', 'Form Pembayaran Pranota OB')
@section('page_title', 'Form Pembayaran Pranota OB')

@push('styles')
<style>
    .custom-select-container {
        position: relative;
    }
    .custom-select-button {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        padding: 0.5rem 0.75rem; /* p-2 equivalent approx */
        background-color: #f9fafb; /* bg-gray-50 */
        border: 1px solid #d1d5db; /* border-gray-300 */
        border-radius: 0.375rem; /* rounded-md */
        cursor: pointer;
        text-align: left;
        font-size: 0.875rem; /* text-sm */
        line-height: 1.25rem;
    }
    .custom-select-button:focus {
        outline: none;
        border-color: #6366f1; /* indigo-500 */
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.5); /* ring-indigo-500 */
    }
    .custom-select-dropdown {
        position: absolute;
        z-index: 9999;
        width: 100%;
        margin-top: 0.25rem;
        background-color: white;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        display: none;
    }
    .custom-select-search {
        padding: 0.5rem;
        background-color: #f9fafb;
        border-bottom: 1px solid #d1d5db;
    }
    .custom-select-option {
        padding: 0.5rem 0.75rem;
        cursor: pointer;
        font-size: 0.875rem;
    }
    .custom-select-option:hover {
        background-color: #eff6ff; /* blue-50 */
    }
    .custom-select-option.selected {
        background-color: #dbeafe; /* blue-100 */
        color: #1e40af; /* blue-800 */
        font-weight: 500;
    }
    .hidden {
        display: none !important;
    }
</style>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container { width: 100% !important; }
    .select2-container .select2-selection--single {
        height: 38px !important;
        padding: 5px 10px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
        background-color: #f9fafb !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 26px !important;
        font-size: 0.875rem !important;
    }
    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
</style>
@endpush

@section('content')
    @php
        use App\Models\PembayaranPranotaOb;
        // Hitung counter untuk pembayaran pranota OB
        $obPaymentCounter = PembayaranPranotaOb::count() + 1;
    @endphp
    <div class="bg-white shadow-lg rounded-lg p-4 max-w-6xl mx-auto">
        @php
            // Definisikan kelas Tailwind untuk input yang lebih compact
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors";
            $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-sm p-2";
            $labelClasses = "block text-xs font-medium text-gray-700 mb-1";
        @endphp

        {{-- Display selected criteria --}}
        <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-blue-800">Kriteria yang Dipilih</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <div class="flex flex-wrap gap-3">
                            <div>
                                <span class="font-semibold">Kapal:</span>
                                <span class="ml-1 px-2 py-0.5 bg-blue-100 rounded">{{ request('kapal', '-') }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">Voyage:</span>
                                <span class="ml-1 px-2 py-0.5 bg-blue-100 rounded">{{ request('voyage', '-') }}</span>
                            </div>
                            @php
                                $selectedDp = request('dp') ? \App\Models\PembayaranOb::find(request('dp')) : null;
                            @endphp
                            <div>
                                <span class="font-semibold">DP:</span>
                                <span class="ml-1 px-2 py-0.5 bg-blue-100 rounded">
                                    @if($selectedDp)
                                        {{ $selectedDp->nomor_pembayaran }} - Rp {{ number_format($selectedDp->dp_amount, 0, ',', '.') }}
                                        @php
                                            // Get supir data from DP
                                            $dpSupirIds = $selectedDp->supir_ids ?? [];
                                            $dpJumlahPerSupir = $selectedDp->jumlah_per_supir ?? [];
                                            $dpSupirData = [];
                                            if (!empty($dpSupirIds)) {
                                                foreach ($dpSupirIds as $supirId) {
                                                    $supir = \App\Models\Karyawan::find($supirId);
                                                    if ($supir) {
                                                        // Use nama_panggilan (nickname) if available, else nama_lengkap
                                                        // Then uppercase to match enrichedItems normalization
                                                        $namaKey = $supir->nama_panggilan ?: $supir->nama_lengkap;
                                                        $dpSupirData[strtoupper(trim($namaKey))] = $dpJumlahPerSupir[$supirId] ?? 0;
                                                    }
                                                }
                                            }
                                        @endphp
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('pembayaran-pranota-ob.select-criteria') }}" class="text-blue-600 hover:text-blue-800 text-xs underline">
                                Ubah Kriteria
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Double Book Accounting Info -->
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-blue-800 mb-1">📊 Sistem Double Book Accounting</h3>
                    <div class="text-xs text-blue-700 space-y-1">
                        <p><strong>Otomatis Jurnal Akuntansi:</strong></p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-1">
                            <div class="bg-white p-2 rounded border border-blue-200">
                                <p class="font-medium text-green-700">✅ Jika pilih CREDIT:</p>
                                <p class="mt-1">• <strong>Dr.</strong> Akun yang dipilih (Biaya/Beban) <span class="text-green-600">+</span></p>
                                <p>• <strong>Cr.</strong> Akun Bank yang dipilih <span class="text-red-600">-</span></p>
                            </div>
                            <div class="bg-white p-2 rounded border border-blue-200">
                                <p class="font-medium text-blue-700">✅ Jika pilih DEBIT:</p>
                                <p class="mt-1">• <strong>Dr.</strong> Akun Bank yang dipilih <span class="text-green-600">+</span></p>
                                <p>• <strong>Cr.</strong> Akun yang dipilih (Biaya/Beban) <span class="text-red-600">-</span></p>
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

        <form id="pembayaranForm" action="{{ route('pembayaran-pranota-ob.store') }}" method="POST" class="space-y-3">
            @csrf

            {{-- Hidden inputs for additional data --}}
            <input type="hidden" name="nomor_pembayaran" id="nomor_pembayaran_hidden" value="{{ old('nomor_pembayaran') }}">
            <input type="hidden" name="kapal" value="{{ request('kapal') }}">
            <input type="hidden" name="voyage" value="{{ request('voyage') }}">
            <input type="hidden" name="dp_id" value="{{ request('dp') }}">
            <input type="hidden" name="breakdown_supir" id="breakdown_supir_hidden" value="">

            <!-- Data Pembayaran & Bank -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Data Pembayaran -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Data Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div class="flex items-end gap-1">
                                <div class="flex-1">
                                    <label for="nomor_pembayaran_display" class="{{ $labelClasses }}">Nomor Pembayaran</label>
                                    <input type="text" id="nomor_pembayaran_display"
                                        value="{{ old('nomor_pembayaran') }}"
                                        placeholder="Pilih bank terlebih dahulu"
                                        class="{{ $readonlyInputClasses }}" readonly>
                                </div>
                            </div>
                            <div>
                                <label for="nomor_accurate" class="{{ $labelClasses }}">Nomor Accurate</label>
                                <input type="text" name="nomor_accurate" id="nomor_accurate"
                                    value="{{ old('nomor_accurate') }}"
                                    placeholder="Masukkan nomor accurate (opsional)"
                                    class="{{ $inputClasses }}">
                            </div>
                            <div>
                                <label for="tanggal_kas" class="{{ $labelClasses }}">Tanggal Kas</label>
                                <input type="date" name="tanggal_kas" id="tanggal_kas"
                                    value="{{ old('tanggal_kas', now()->toDateString()) }}"
                                    class="{{ $inputClasses }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank & Transaksi (Double Book) -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-semibold text-gray-800">Double Book Accounting</h4>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                📊 Auto Jurnal
                            </span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="md:col-span-2">
                                <label for="debit_kredit" class="{{ $labelClasses }}">Jenis Transaksi <span class="text-red-500">*</span></label>
                                <select name="debit_kredit" id="debit_kredit" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Jenis Transaksi --</option>
                                    <option value="credit" {{ old('debit_kredit', 'credit') == 'credit' ? 'selected' : '' }}>KREDIT (Biaya/Beban bertambah, Bank berkurang)</option>
                                    <option value="debit" {{ old('debit_kredit') == 'debit' ? 'selected' : '' }}>DEBIT (Bank bertambah, Biaya/Beban berkurang)</option>
                                </select>
                            </div>

                            <div>
                                <label for="akun_bank_id" class="{{ $labelClasses }}">Pilih Bank/Kas <span class="text-red-500">*</span></label>
                                <select name="akun_bank_id" id="akun_bank_id" class="{{ $inputClasses }} coa-select" required>
                                    <option value="">-- Pilih Bank --</option>
                                    @foreach($akunBank as $akun)
                                        <option value="{{ $akun->id }}" 
                                                data-kode="{{ $akun->kode_nomor ?? '000' }}"
                                                data-nama="{{ $akun->nama_akun }}"
                                                {{ old('akun_bank_id') == $akun->id ? 'selected' : '' }}>
                                            {{ $akun->nomor_akun }} - {{ $akun->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="akun_coa_id" class="{{ $labelClasses }}">Pilih Akun Biaya <span class="text-red-500">*</span></label>
                                <select name="akun_coa_id" id="akun_coa_id" class="{{ $inputClasses }} coa-select" required>
                                    <option value="">-- Pilih Akun Biaya --</option>
                                    @foreach($akunBiaya as $akun)
                                        <option value="{{ $akun->id }}" 
                                                data-nama="{{ $akun->nama_akun }}"
                                                {{ old('akun_coa_id') == $akun->id ? 'selected' : (str_contains(strtolower($akun->nama_akun), 'biaya ob') ? 'selected' : '') }}>
                                            {{ $akun->nomor_akun }} - {{ $akun->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Dynamic Journal Preview -->
                            <div id="journal_preview" class="md:col-span-2 mt-1 p-2 bg-white border border-blue-200 rounded text-[11px] hidden transition-all duration-300">
                                <p class="font-bold text-gray-700 mb-1 flex items-center">
                                    <i class="fas fa-file-invoice mr-1 text-blue-500"></i>
                                    Preview Jurnal Akuntansi:
                                </p>
                                <div id="journal_content" class="text-gray-600">
                                    <!-- Content will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pilih Pranota OB --}}
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800">Pilih Pranota OB</h4>
                </div>
                <div class="overflow-x-auto max-h-60">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-20">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all" class="h-3 w-3 text-indigo-600 border-gray-300 rounded">
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pranota</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapal / Voyage</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pranotaList as $pranota)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <input type="checkbox" name="pranota_ids[]" value="{{ $pranota->id }}" class="pranota-checkbox h-3 w-3 text-indigo-600 border-gray-300 rounded" checked data-pranota-id="{{ $pranota->id }}">
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">{{ $pranota->nomor_pranota }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $pranota->nama_kapal }} / {{ $pranota->no_voyage }}</td>
                                    <td class="px-2 py-2 text-xs">
                                        @php
                                            $enrichedItems = $pranota->getEnrichedItems();
                                            // Ensure uniform uppercase matching
                                            foreach ($enrichedItems as &$item) {
                                                if (isset($item['supir'])) {
                                                    $item['supir'] = strtoupper(trim($item['supir']));
                                                }
                                            }
                                            
                                            $supirList = array_values(array_unique(array_filter(array_column($enrichedItems, 'supir'), function($supir) {
                                                return $supir && $supir !== '-' && $supir !== null && trim($supir) !== '';
                                            })));
                                            
                                            // Build supir breakdown data with actual biaya per supir
                                            $supirBreakdown = [];
                                            foreach ($enrichedItems as $item) {
                                                $supirName = $item['supir'] ?? null;
                                                if ($supirName && $supirName !== '-' && $supirName !== null && trim($supirName) !== '') {
                                                    if (!isset($supirBreakdown[$supirName])) {
                                                        $supirBreakdown[$supirName] = [
                                                            'items' => 0,
                                                            'biaya' => 0
                                                        ];
                                                    }
                                                    $supirBreakdown[$supirName]['items'] += 1;
                                                    $supirBreakdown[$supirName]['biaya'] += floatval($item['biaya'] ?? 0);
                                                }
                                            }
                                        @endphp
                                        <div class="flex flex-wrap gap-1" 
                                             data-supir-data='@json($supirList)'
                                             data-supir-breakdown='@json($supirBreakdown)'>
                                            @if(count($supirList) > 0)
                                                @foreach(array_slice($supirList, 0, 2) as $supir)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">{{ $supir }}</span>
                                                @endforeach
                                                @if(count($supirList) > 2)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">+{{ count($supirList) - 2 }}</span>
                                                @endif
                                            @else
                                                <span class="text-gray-400 italic">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                                        @php
                                            $itemsCount = ($pranota->itemsPivot && $pranota->itemsPivot->count()) ? $pranota->itemsPivot->count() : (is_array($pranota->items) ? count($pranota->items) : 0);
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $itemsCount }}</span>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">{{ \Carbon\Carbon::parse($pranota->created_at)->format('d/M/Y') }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold">Rp {{ number_format($pranota->calculateTotalAmount(), 0, ',', '.') }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @if ($pranota->status == 'paid')
                                            <span class="px-1.5 py-0.5 inline-flex text-xs font-medium rounded bg-green-100 text-green-800">Lunas</span>
                                        @else
                                            <span class="px-1.5 py-0.5 inline-flex text-xs font-medium rounded bg-yellow-100 text-yellow-800">Belum</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-2 py-4 text-center text-xs text-gray-500">
                                        Tidak ada pranota OB yang tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                    <p class="text-xs text-gray-600">
                        * Pilih satu atau lebih pranota OB untuk dibayar.
                    </p>
                </div>
            </div>

            {{-- Breakdown Per Supir dari Pranota yang Dipilih --}}
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800">Breakdown Per Supir dari Pranota yang Dipilih</h4>
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
                            <tr>
                                <td colspan="9" class="px-3 py-4 text-center text-xs text-gray-500 italic">
                                    Pilih pranota untuk melihat breakdown per supir
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50" id="supir-breakdown-footer" style="display: none;">
                            <tr>
                                <td class="px-3 py-2 text-left text-xs font-bold text-gray-800">Total</td>
                                <td class="px-3 py-2 text-center text-xs font-bold text-gray-800" id="total-items">0</td>
                                <td class="px-3 py-2 text-right text-xs font-bold text-gray-800" id="total-biaya">Rp 0</td>
                                <td class="px-3 py-2 text-right text-xs font-bold text-green-800" id="total-dp">Rp 0</td>
                                <td class="px-3 py-2 text-right text-xs font-bold text-red-800" id="total-sisa">Rp 0</td>
                                <td class="px-3 py-2 text-right text-xs font-bold text-orange-800" id="total-pot-utang">Rp 0</td>
                                <td class="px-3 py-2 text-right text-xs font-bold text-orange-800" id="total-pot-tabungan">Rp 0</td>
                                <td class="px-3 py-2 text-right text-xs font-bold text-orange-800" id="total-pot-bpjs">Rp 0</td>
                                <td class="px-3 py-2 text-right text-xs font-bold text-blue-800" id="total-grand-total">Rp 0</td>
                            </tr>
                        </tfoot>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('select-all');
        const pranotaCheckboxes = document.querySelectorAll('.pranota-checkbox');

        // Function to calculate total (should be SISA, not total biaya)
        function calculateTotal() {
            let totalBiaya = 0;
            let totalDp = 0;
            
            // Get DP supir data
            const dpSupirData = @json($selectedDp && isset($dpSupirData) ? $dpSupirData : []);
            
            pranotaCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const row = checkbox.closest('tr');
                    const amountText = row.querySelector('td:nth-child(7)').textContent;
                    const amount = parseFloat(amountText.replace(/Rp\s|,|\./g, '')) || 0;
                    totalBiaya += amount;
                }
            });
            
            // Calculate total DP from DP data
            Object.values(dpSupirData).forEach(dpAmount => {
                totalDp += parseFloat(dpAmount) || 0;
            });
            
            // Total tagihan = total biaya - total DP (SISA yang harus dibayar)
            const totalSisa = totalBiaya - totalDp;
            
            document.getElementById('total_pembayaran').value = totalSisa.toLocaleString('id-ID');
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
                updateSupirList();
            });
        });

        // Function to update supir list
        function updateSupirList() {
            const supirBreakdownBody = document.getElementById('supir-breakdown-body');
            const supirBreakdownFooter = document.getElementById('supir-breakdown-footer');
            const supirData = {};
            let totalItems = 0;
            let totalBiaya = 0;
            
            // Get DP supir data from PHP
            const dpSupirData = @json($selectedDp && isset($dpSupirData) ? $dpSupirData : []);
            
            // Seed supirData with drivers from DP to ensure they appear even if they have 0 items
            Object.keys(dpSupirData).forEach(supirName => {
                supirData[supirName] = {
                    items: 0,
                    biaya: 0
                };
            });
            
            const dpAmount = {{ $selectedDp ? $selectedDp->dp_amount : 0 }};
            let totalDp = 0;

            pranotaCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const row = checkbox.closest('tr');
                    const supirCell = row.querySelector('[data-supir-data]');
                    const pranotaId = checkbox.getAttribute('data-pranota-id');
                    
                    if (supirCell && pranotaId) {
                        try {
                            const supirDataAttr = supirCell.getAttribute('data-supir-data');
                            const supirBreakdownAttr = supirCell.getAttribute('data-supir-breakdown');
                            console.log('Pranota ID:', pranotaId, 'Supir Data Attr:', supirDataAttr);
                            console.log('Supir Breakdown:', supirBreakdownAttr);
                            
                            const supirList = JSON.parse(supirDataAttr);
                            const supirBreakdown = JSON.parse(supirBreakdownAttr);
                            console.log('Parsed supirList:', supirList, 'Length:', supirList.length);
                            console.log('Parsed supirBreakdown:', supirBreakdown);
                            
                            // If there are supir in this pranota
                            if (supirList.length > 0 && Object.keys(supirBreakdown).length > 0) {
                                console.log('Processing', supirList.length, 'supir(s) with breakdown data');
                                
                                // Use actual breakdown data instead of dividing equally
                                Object.entries(supirBreakdown).forEach(([supir, data]) => {
                                    console.log('Adding supir:', supir, 'with', data.items, 'items and biaya:', data.biaya);
                                    if (!supirData[supir]) {
                                        supirData[supir] = {
                                            items: 0,
                                            biaya: 0
                                        };
                                    }
                                    supirData[supir].items += data.items;
                                    supirData[supir].biaya += data.biaya;
                                });
                            } else {
                                console.log('No supir found or no breakdown data, using "Belum Ditentukan"');
                                // If no supir, count as "Belum Ditentukan"
                                const biayaText = row.querySelector('td:nth-child(7)').textContent;
                                const biaya = parseFloat(biayaText.replace(/Rp\s|,|\./g, '')) || 0;
                                const itemCountText = row.querySelector('td:nth-child(5) span').textContent;
                                const itemCount = parseInt(itemCountText) || 0;
                                
                                if (!supirData['Belum Ditentukan']) {
                                    supirData['Belum Ditentukan'] = {
                                        items: 0,
                                        biaya: 0
                                    };
                                }
                                supirData['Belum Ditentukan'].items += itemCount;
                                supirData['Belum Ditentukan'].biaya += biaya;
                            }
                        } catch (e) {
                            console.error('Error parsing supir data:', e);
                        }
                    }
                }
            });

            supirBreakdownBody.innerHTML = '';

            if (Object.keys(supirData).length === 0) {
                supirBreakdownBody.innerHTML = `
                    <tr>
                        <td colspan="9" class="px-3 py-4 text-center text-xs text-gray-500 italic">
                            Tidak ada supir dalam pranota yang dipilih
                        </td>
                    </tr>
                `;
                supirBreakdownFooter.style.display = 'none';
                // Clear breakdown hidden input
                document.getElementById('breakdown_supir_hidden').value = '';
            } else {
                const sortedSupir = Object.entries(supirData).sort((a, b) => a[0].localeCompare(b[0]));
                const breakdownArray = [];
                
                sortedSupir.forEach(([supir, data]) => {
                    // Get DP for this specific supir from DP data
                    const dpPerSupir = dpSupirData[supir] || 0;
                    const sisaPerSupir = data.biaya - dpPerSupir;
                    totalDp += dpPerSupir;
                    
                    const supirSlug = supir.replace(/[^a-z0-9]/gi, '_').toLowerCase();
                    
                    // Store breakdown data for backend
                    breakdownArray.push({
                        nama_supir: supir,
                        jumlah_item: Math.round(data.items * 10) / 10,
                        total_biaya: data.biaya,
                        dp: dpPerSupir,
                        sisa: sisaPerSupir,
                        potongan_utang: 0,
                        potongan_tabungan: 0,
                        potongan_bpjs: 0,
                        grand_total: sisaPerSupir
                    });
                    
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50';
                    row.innerHTML = `
                        <td class="px-3 py-3 text-xs">
                            <div class="flex items-center">
                                <i class="fas fa-user text-purple-500 mr-2"></i>
                                <span class="font-medium text-gray-900">${supir}</span>
                                ${dpPerSupir > 0 ? '<span class="ml-2 text-xs text-green-600">(Ada DP)</span>' : '<span class="ml-2 text-xs text-gray-400">(Tanpa DP)</span>'}
                            </div>
                        </td>
                        <td class="px-3 py-3 text-center text-xs">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                ${Math.round(data.items * 10) / 10} item
                            </span>
                        </td>
                        <td class="px-3 py-3 text-right text-xs font-semibold text-gray-900">
                            Rp ${data.biaya.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0})}
                        </td>
                        <td class="px-3 py-3 text-right text-xs font-semibold ${dpPerSupir > 0 ? 'text-green-700' : 'text-gray-400'}">
                            Rp ${dpPerSupir.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0})}
                        </td>
                        <td class="px-3 py-3 text-right text-xs font-semibold ${sisaPerSupir > 0 ? 'text-red-700' : 'text-gray-500'}">
                            Rp ${sisaPerSupir.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0})}
                        </td>
                        <td class="px-2 py-2">
                            <input type="number" 
                                class="potongan-utang w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                                data-supir="${supirSlug}"
                                placeholder="0" 
                                min="0" 
                                value="0">
                        </td>
                        <td class="px-2 py-2">
                            <input type="number" 
                                class="potongan-tabungan w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                                data-supir="${supirSlug}"
                                placeholder="0" 
                                min="0" 
                                value="0">
                        </td>
                        <td class="px-2 py-2">
                            <input type="number" 
                                class="potongan-bpjs w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                                data-supir="${supirSlug}"
                                placeholder="0" 
                                min="0" 
                                value="0">
                        </td>
                        <td class="px-3 py-3 text-right text-xs font-bold text-blue-700">
                            <span class="grand-total-${supirSlug}">Rp ${sisaPerSupir.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span>
                        </td>
                    `;
                    supirBreakdownBody.appendChild(row);
                    
                    totalItems += data.items;
                    totalBiaya += data.biaya;
                });

                // Update footer
                const totalSisa = totalBiaya - totalDp;
                document.getElementById('total-items').textContent = `${Math.round(totalItems * 10) / 10} item`;
                document.getElementById('total-biaya').textContent = `Rp ${totalBiaya.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0})}`;
                document.getElementById('total-dp').textContent = `Rp ${totalDp.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0})}`;
                document.getElementById('total-sisa').textContent = `Rp ${totalSisa.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0})}`;
                document.getElementById('total-pot-utang').textContent = 'Rp 0';
                document.getElementById('total-pot-tabungan').textContent = 'Rp 0';
                document.getElementById('total-pot-bpjs').textContent = 'Rp 0';
                document.getElementById('total-grand-total').textContent = `Rp ${totalSisa.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0})}`;
                supirBreakdownFooter.style.display = 'table-footer-group';
                
                // Save breakdown to hidden input
                document.getElementById('breakdown_supir_hidden').value = JSON.stringify(breakdownArray);
                
                // Add event listeners to potongan inputs
                addPotonganEventListeners();
            }
        }
        
        // Function to add event listeners to potongan inputs
        function addPotonganEventListeners() {
            const potonganInputs = document.querySelectorAll('.potongan-utang, .potongan-tabungan, .potongan-bpjs');
            
            potonganInputs.forEach(input => {
                input.addEventListener('input', function() {
                    calculateGrandTotal(this.dataset.supir);
                    updateTotalFooter();
                    updateBreakdownData();
                });
            });
        }
        
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
                width: '100%',
                placeholder: '-- Pilih --',
                allowClear: true
            });

            $('#debit_kredit').select2({
                width: '100%',
                minimumResultsForSearch: Infinity
            });

            // Trigger journal preview and nomor pembayaran updates
            $('#akun_bank_id').on('change', function() {
                updateNomorPembayaran();
                updateJournalPreview();
            });
            $('#akun_coa_id').on('change', updateJournalPreview);
            $('#debit_kredit').on('change', updateJournalPreview);

            // Initial call after initialization
            updateNomorPembayaran();
            updateJournalPreview();
        }

        // Function to calculate grand total for a specific supir
        function calculateGrandTotal(supirSlug) {
            const row = document.querySelector(`.potongan-utang[data-supir="${supirSlug}"]`).closest('tr');
            const sisaText = row.querySelector('td:nth-child(5)').textContent.replace(/Rp\s|,|\./g, '');
            const sisa = parseFloat(sisaText) || 0;
            
            const potUtang = parseFloat(row.querySelector('.potongan-utang').value) || 0;
            const potTabungan = parseFloat(row.querySelector('.potongan-tabungan').value) || 0;
            const potBpjs = parseFloat(row.querySelector('.potongan-bpjs').value) || 0;
            
            const grandTotal = sisa - potUtang - potTabungan - potBpjs;
            
            const grandTotalElement = document.querySelector(`.grand-total-${supirSlug}`);
            if (grandTotalElement) {
                grandTotalElement.textContent = `Rp ${grandTotal.toLocaleString('id-ID')}`;
            }
        }
        
        // Function to update total footer
        function updateTotalFooter() {
            let totalPotUtang = 0;
            let totalPotTabungan = 0;
            let totalPotBpjs = 0;
            let totalGrandTotal = 0;
            
            document.querySelectorAll('.potongan-utang').forEach(input => {
                totalPotUtang += parseFloat(input.value) || 0;
            });
            
            document.querySelectorAll('.potongan-tabungan').forEach(input => {
                totalPotTabungan += parseFloat(input.value) || 0;
            });
            
            document.querySelectorAll('.potongan-bpjs').forEach(input => {
                totalPotBpjs += parseFloat(input.value) || 0;
            });
            
            document.querySelectorAll('[class^="grand-total-"]').forEach(cell => {
                const value = cell.textContent.replace(/Rp\s|,|\./g, '');
                totalGrandTotal += parseFloat(value) || 0;
            });
            
            document.getElementById('total-pot-utang').textContent = `Rp ${totalPotUtang.toLocaleString('id-ID')}`;
            document.getElementById('total-pot-tabungan').textContent = `Rp ${totalPotTabungan.toLocaleString('id-ID')}`;
            document.getElementById('total-pot-bpjs').textContent = `Rp ${totalPotBpjs.toLocaleString('id-ID')}`;
            document.getElementById('total-grand-total').textContent = `Rp ${totalGrandTotal.toLocaleString('id-ID')}`;
            
            document.getElementById('total_pembayaran').value = totalGrandTotal.toLocaleString('id-ID');
            updateTotalAkhir();
        }
        
        function updateBreakdownData() {
            try {
                const currentBreakdown = JSON.parse(document.getElementById('breakdown_supir_hidden').value || '[]');
                
                currentBreakdown.forEach(item => {
                    const supirSlug = item.nama_supir.replace(/[^a-z0-9]/gi, '_').toLowerCase();
                    const inputRow = document.querySelector(`.potongan-utang[data-supir="${supirSlug}"]`);
                    if (inputRow) {
                        const row = inputRow.closest('tr');
                        item.potongan_utang = parseFloat(row.querySelector('.potongan-utang').value) || 0;
                        item.potongan_tabungan = parseFloat(row.querySelector('.potongan-tabungan').value) || 0;
                        item.potongan_bpjs = parseFloat(row.querySelector('.potongan-bpjs').value) || 0;
                        item.grand_total = item.sisa - item.potongan_utang - item.potongan_tabungan - item.potongan_bpjs;
                    }
                });
                
                document.getElementById('breakdown_supir_hidden').value = JSON.stringify(currentBreakdown);
            } catch (e) {
                console.error('Error updating breakdown data:', e);
            }
        }

        function updateJournalPreview() {
            const journalPreview = document.getElementById('journal_preview');
            const journalContent = document.getElementById('journal_content');
            
            if (!journalPreview || !journalContent) return;

            const debitKredit = $('#debit_kredit').val();
            const akunBank = $('#akun_bank_id').select2('data') ? $('#akun_bank_id').select2('data')[0] : null;
            const akunBiaya = $('#akun_coa_id').select2('data') ? $('#akun_coa_id').select2('data')[0] : null;
            const totalSetelahPenyesuaian = document.getElementById('total_tagihan_setelah_penyesuaian').value || '0';
            
            if (!debitKredit || !akunBank || !akunBank.id || !akunBiaya || !akunBiaya.id || totalSetelahPenyesuaian === '0' || totalSetelahPenyesuaian === '0,00') {
                journalPreview.classList.add('hidden');
                return;
            }

            const bankText = akunBank.element.dataset.nama || akunBank.text.split(' - ')[1];
            const biayaText = akunBiaya.element.dataset.nama || akunBiaya.text.split(' - ')[1];
            const amountFormatted = 'Rp ' + totalSetelahPenyesuaian;

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
                    <p class="mt-1 text-[10px] text-gray-500 italic">💡 Efek: Beban bertambah, Saldo Bank berkurang</p>
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
                    <p class="mt-1 text-[10px] text-gray-500 italic">💡 Efek: Saldo Bank bertambah, Beban berkurang</p>
                `;
            }

            journalContent.innerHTML = html;
            journalPreview.classList.remove('hidden');
        }

        // Function to update nomor pembayaran
        function updateNomorPembayaran() {
            const bankSelect = $('#akun_bank_id');
            const selectedOption = bankSelect.find('option:selected');
            
            if (!bankSelect.val()) {
                if (document.getElementById('nomor_pembayaran_display')) document.getElementById('nomor_pembayaran_display').value = '';
                if (document.getElementById('nomor_pembayaran_hidden')) document.getElementById('nomor_pembayaran_hidden').value = '';
                return;
            }

            const kode = selectedOption.data('kode') || '000';
            const counter = {{ $obPaymentCounter }};
            const now = new Date();
            const year = now.getFullYear().toString().slice(-2);
            const month = (now.getMonth() + 1).toString().padStart(2, '0');
            const running = counter.toString().padStart(6, '0');
            const nomor = kode + '1' + year + month + running;

            if (document.getElementById('nomor_pembayaran_display')) document.getElementById('nomor_pembayaran_display').value = nomor;
            if (document.getElementById('nomor_pembayaran_hidden')) document.getElementById('nomor_pembayaran_hidden').value = nomor;
        }

        // Form validation before submission
        document.getElementById('pembayaranForm').addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.pranota-checkbox:checked');
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Pilih minimal satu pranota OB untuk dibayar.');
                return false;
            }

            if (!$('#akun_bank_id').val()) {
                e.preventDefault(); alert('Pilih bank terlebih dahulu.'); return false;
            }

            if (!$('#akun_coa_id').val()) {
                e.preventDefault(); alert('Pilih akun biaya.'); return false;
            }

            if (!$('#debit_kredit').val()) {
                e.preventDefault(); alert('Pilih jenis transaksi.'); return false;
            }
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

        calculateTotal();
        updateSupirList();
    });
</script>



@endsection
