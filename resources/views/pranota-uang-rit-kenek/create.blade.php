@extends('layouts.app')

@section('title', 'Tambah Pranota Uang Kenek')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-lg rounded-lg p-4">
        {{-- Notifikasi --}}
        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
                <p class="font-bold">Peringatan</p>
                <ul class="list-disc list-inside mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
                <p class="font-bold">Peringatan</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
                <p class="font-bold">Sukses</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @php
            // Definisikan kelas Tailwind untuk input yang lebih compact
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors";
            $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-sm p-2";
            $labelClasses = "block text-xs font-medium text-gray-700 mb-1";
        @endphp

        <form action="{{ route('pranota-uang-rit-kenek.store') }}" method="POST" id="pranotaForm" class="space-y-3">
            @csrf
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
            <input type="hidden" name="rit_filter_hidden" value="{{ $ritFilter ?? 'semua' }}">

            @if(isset($viewStartDate) && isset($viewEndDate) && $viewStartDate && $viewEndDate)
            <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-md flex flex-wrap items-center justify-between gap-2">
                <div>
                    <p class="text-xs text-yellow-800">Menampilkan Surat Jalan dengan <strong>tanggal tanda terima</strong> dari <strong>{{ \Carbon\Carbon::parse($viewStartDate)->format('d/m/Y') }}</strong> hingga <strong>{{ \Carbon\Carbon::parse($viewEndDate)->format('d/m/Y') }}</strong>.</p>
                    <a href="{{ route('pranota-uang-rit-kenek.select-date') }}" class="ml-2 text-xs text-blue-600 hover:underline">Ubah rentang tanggal</a>
                </div>
                <div class="flex items-center gap-2">
                    <label for="rit_filter" class="text-xs font-medium text-gray-700">Filter Status Rit:</label>
                    <select id="rit_filter" name="rit_filter" class="text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1 px-2" onchange="applyRitFilter()">
                        <option value="semua" {{ ($ritFilter ?? 'semua') == 'semua' ? 'selected' : '' }}>Semua</option>
                        <option value="menggunakan_rit" {{ ($ritFilter ?? 'semua') == 'menggunakan_rit' ? 'selected' : '' }}>Menggunakan Rit</option>
                        <option value="tanpa_rit" {{ ($ritFilter ?? 'semua') == 'tanpa_rit' ? 'selected' : '' }}>Tanpa Rit</option>
                    </select>
                </div>
            </div>
            @endif

            {{-- Local debug info to help diagnose filtering issues --}}
            @if(app()->isLocal())
            <div class="mt-2 text-xs text-gray-600 bg-gray-50 border border-gray-200 rounded p-2">
                <p class="mb-1">Debug: filter range: <strong>{{ $viewStartDate }}</strong> - <strong>{{ $viewEndDate }}</strong></p>
                <p class="mb-1">Eligible count: <strong>{{ $eligibleCount ?? 'N/A' }}</strong></p>
                <p class="mb-1">SuratJalans returned: <strong>{{ $suratJalans->count() ?? 'N/A' }}</strong></p>
                @if($suratJalans->count() > 0)
                <p class="mb-1">Sample tanggal surat jalan: <strong>{{ $suratJalans->first()->tanggal_surat_jalan ?? 'N/A' }}</strong></p>
                <p class="mb-1">Sample ID: <strong>{{ $suratJalans->first()->id ?? 'N/A' }}</strong> - No: <strong>{{ $suratJalans->first()->no_surat_jalan ?? 'N/A' }}</strong></p>
                @endif
                @if($suratJalans->count() > 1)
                <p class="mb-1">Last sample tanggal: <strong>{{ $suratJalans->last()->tanggal_surat_jalan ?? 'N/A' }}</strong> - No: <strong>{{ $suratJalans->last()->no_surat_jalan ?? 'N/A' }}</strong></p>
                @endif
                <p class="mb-0">Check laravel.log for 'Date filtering impact' entry. If filtering still not working, there may be a database issue.</p>
            </div>
            @endif

            <!-- Hidden inputs untuk data hutang dan tabungan per Kenek -->
            <div id="kenekDetailsInputs"></div>

            <!-- Data Pranota & Total Uang dalam satu baris -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Data Pranota -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">💰 Data Pranota Uang Kenek</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div>
                                <label for="nomor_pranota_preview" class="{{ $labelClasses }}">Nomor Pranota</label>
                                <input type="text"
                                       class="{{ $readonlyInputClasses }} font-medium text-indigo-600"
                                       id="nomor_pranota_preview"
                                       value="Auto Generate: PURK-{{ date('m') }}-{{ date('y') }}-XXXXXX"
                                       readonly>
                                <p class="mt-1 text-xs text-gray-500">Satu nomor untuk semua surat jalan yang dipilih</p>
                            </div>
                            <div>
                                <label for="tanggal" class="{{ $labelClasses }}">
                                    Tanggal <span class="text-red-500">*</span>
                                </label>
                                <input type="date"
                                       class="{{ $inputClasses }} @error('tanggal') border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror"
                                       id="tanggal"
                                       name="tanggal"
                                       value="{{ old('tanggal', date('Y-m-d')) }}"
                                       required>
                                @error('tanggal')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-2">
                            <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                            <textarea class="{{ $inputClasses }} @error('keterangan') border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror"
                                      id="keterangan"
                                      name="keterangan"
                                      rows="2"
                                      placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Total Uang -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">📊 Total Keseluruhan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label for="jumlah_surat_jalan_display" class="{{ $labelClasses }}">Jumlah Surat Jalan</label>
                                <input type="text" id="jumlah_surat_jalan_display" class="{{ $readonlyInputClasses }}" value="0" readonly>
                            </div>
                            <div>
                                <label for="jumlah_Kenek_display" class="{{ $labelClasses }}">Jumlah Kenek</label>
                                <input type="text" id="jumlah_Kenek_display" class="{{ $readonlyInputClasses }}" value="0" readonly>
                            </div>
                            <div>
                                <label for="total_uang_rit_display" class="{{ $labelClasses }}">Total Uang Kenek</label>
                                <input type="text" id="total_uang_rit_display" class="{{ $readonlyInputClasses }} font-bold text-indigo-600" value="Rp 0" readonly>
                            </div>
                            <div>
                                <label for="total_utang_display" class="{{ $labelClasses }}">Total Utang</label>
                                <input type="text" id="total_utang_display" class="{{ $readonlyInputClasses }} font-bold text-red-600" value="Rp 0" readonly>
                            </div>
                            <div>
                                <label for="total_tabungan_display" class="{{ $labelClasses }}">Total Tabungan</label>
                                <input type="text" id="total_tabungan_display" class="{{ $readonlyInputClasses }} font-bold text-green-600" value="Rp 0" readonly>
                            </div>
                            <div>
                                <label for="total_bpjs_display" class="{{ $labelClasses }}">Total BPJS</label>
                                <input type="text" id="total_bpjs_display" class="{{ $readonlyInputClasses }} font-bold text-yellow-600" value="Rp 0" readonly>
                            </div>
                            <div>
                                <label for="grand_total_display" class="{{ $labelClasses }}">Grand Total</label>
                                <input type="text" id="grand_total_display" class="{{ $readonlyInputClasses }} font-bold text-purple-600" value="Rp 0" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pilih Surat Jalan -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <h4 class="text-sm font-semibold text-gray-800">🚚 Pilih Surat Jalan untuk Uang Kenek</h4>
                            <span id="searchResults" class="text-xs text-gray-500 hidden"></span>
                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            <div class="relative flex-1 sm:flex-initial">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" id="searchSuratJalan" placeholder="Cari nomor surat jalan, Kenek, no plat... (Ctrl+F)" class="pl-8 pr-3 py-1.5 border border-gray-300 rounded-md text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-64 transition-colors" title="Tekan Ctrl+F untuk fokus search, ESC untuk clear">
                            </div>
                            <button type="button" id="clearSearch" class="inline-flex items-center justify-center px-2 py-1.5 border border-gray-300 rounded-md text-xs text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" title="Clear search">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <button type="button" id="selectAllBtn" class="inline-flex items-center px-2 py-1.5 border border-blue-300 rounded-md text-xs text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Pilih Semua
                            </button>
                            <button type="button" id="deselectAllBtn" class="inline-flex items-center px-2 py-1.5 border border-gray-300 rounded-md text-xs text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Batal Pilih
                            </button>
                            <button type="button" id="downloadExcelBtn" class="inline-flex items-center px-2 py-1.5 border border-green-300 rounded-md text-xs text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors" title="Download data yang dipilih ke Excel">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download Excel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Selected Summary -->
                <div class="bg-blue-50 border-b border-blue-200 px-3 py-2 hidden" id="selectedSummary">
                    <div class="flex items-center flex-wrap gap-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-xs text-blue-700">
                                <span id="selectedCount">0</span> surat jalan dipilih
                            </span>
                        </div>
                        <div class="text-xs text-blue-700">
                            <span id="selectedKenekCount">0</span> Kenek terlibat
                        </div>
                        <div class="text-xs text-blue-700">
                            Total Uang Kenek: <span class="font-semibold text-indigo-600" id="totalUangRit">Rp 0</span>
                        </div>
                        <div class="text-xs text-blue-700">
                            Total Utang: <span class="font-semibold text-red-600" id="totalUtangSummary">Rp 0</span>
                        </div>
                        <div class="text-xs text-blue-700">
                            Total Tabungan: <span class="font-semibold text-green-600" id="totalTabunganSummary">Rp 0</span>
                        </div>
                        <div class="text-xs text-blue-700">
                            Total BPJS: <span class="font-semibold text-yellow-600" id="totalBpjsSummary">Rp 0</span>
                        </div>
                        <div class="text-xs text-blue-700">
                            Grand Total: <span class="font-semibold text-purple-600" id="grandTotalSummary">Rp 0</span>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 text-xs text-gray-600">
                    <p>Note: Hanya surat jalan yang <strong>approved</strong>, sudah melalui <strong>checkpoint</strong>, memiliki <strong>Tanda Terima</strong>, memiliki <strong>Kenek</strong>, atau surat jalan <strong>bongkaran</strong> yang sudah memilih <strong>tanggal tanda terima</strong> dan memiliki <strong>Kenek</strong> yang dapat dipilih untuk Pranota Uang Rit Kenek.</p>
                </div>
                @if(isset($eligibleCount))
                <div class="px-4 py-3 text-xs text-gray-700 bg-yellow-50 rounded-b-md border-t border-yellow-200">
                    <p class="mb-1">Keterangan: <strong>{{ $eligibleCount }}</strong> total surat jalan memenuhi syarat umum. <strong>{{ $pranotaUsedCount }}</strong> sudah diproses dalam pranota. Setelah filter tambahan, <strong>{{ $finalFilteredCount }}</strong> yang tersedia untuk dipilih.</p>
                </div>
                @endif

                @if(isset($eligibleExamples) && $eligibleExamples->count() > 0)
                <div class="px-4 py-3 text-xs text-gray-700 bg-white rounded-md border border-gray-200 my-2">
                    <p class="font-semibold mb-1">Contoh surat jalan yang memenuhi syarat:</p>
                    <ul class="list-disc list-inside text-xs text-gray-600">
                        @foreach($eligibleExamples as $ex)
                            <li>{{ $ex->no_surat_jalan }} - {{ $ex->Kenek }} - status: {{ $ex->status }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(isset($excludedByPranotaExamples) && $excludedByPranotaExamples->count() > 0)
                <div class="px-4 py-3 text-xs text-gray-700 bg-white rounded-md border border-gray-200 my-2">
                    <p class="font-semibold mb-1">Contoh surat jalan yang <strong>sudah diproses (pranota)</strong> (sehingga tidak tampil):</p>
                    <ul class="list-disc list-inside text-xs text-gray-600">
                        @foreach($excludedByPranotaExamples as $ex)
                            <li>{{ $ex->no_surat_jalan }} - {{ $ex->Kenek }} - status: {{ $ex->status }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(isset($excludedByPaymentExamples) && $excludedByPaymentExamples->count() > 0)
                <div class="px-4 py-3 text-xs text-gray-700 bg-white rounded-md border border-gray-200 my-2">
                    <p class="font-semibold mb-1">Contoh surat jalan yang <strong>terhalang karena status pembayaran</strong> (bukan 'belum_dibayar'):</p>
                    <ul class="list-disc list-inside text-xs text-gray-600">
                        @foreach($excludedByPaymentExamples as $ex)
                            <li>{{ $ex->no_surat_jalan }} - {{ $ex->Kenek }} - status pembayaran: {{ $ex->status_pembayaran_uang_rit }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(isset($excludedByTandaTerimaExamples) && $excludedByTandaTerimaExamples->count() > 0)
                <div class="px-4 py-3 text-xs text-gray-700 bg-white rounded-md border border-gray-200 my-2">
                    <p class="font-semibold mb-1">Contoh surat jalan yang <strong>memiliki Tanda Terima</strong> tetapi <strong>tidak tampil</strong> setelah filter:</p>
                    <ul class="list-disc list-inside text-xs text-gray-600">
                        @foreach($excludedByTandaTerimaExamples as $ex)
                            <li>{{ $ex->no_surat_jalan }} - {{ $ex->Kenek }} - rit: {{ $ex->rit ?? '-' }} - status pembayaran: {{ $ex->status_pembayaran_uang_rit ?? '-' }} - status: {{ $ex->status ?? '-' }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="overflow-x-auto max-h-60">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-20">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="selectAllCheckbox" class="h-3 w-3 text-indigo-600 border-gray-300 rounded">
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                                <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Tanda Terima</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kenek</th>
                                <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Eligible</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Uang Kenek</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $allSuratJalans = collect();
                                
                                // Add regular surat jalans
                                foreach($suratJalans as $sj) {
                                    // Skip surat jalan yang tidak memiliki kenek
                                    if (empty($sj->kenek)) {
                                        continue;
                                    }
                                    
                                    $allSuratJalans->push([
                                        'type' => 'regular',
                                        'id' => $sj->id,
                                        'no_surat_jalan' => $sj->no_surat_jalan,
                                        'tanggal_surat_jalan' => $sj->tanggal_surat_jalan,
                                        'kenek' => $sj->kenek,
                                        'tanggal_checkpoint' => $sj->tanggal_checkpoint,
                                        'tandaTerima' => $sj->tandaTerima,
                                        'kegiatan' => $sj->kegiatan,
                                        'tanggal_tanda_terima' => $sj->tandaTerima ? $sj->tandaTerima->tanggal : null,
                                        'approvals' => $sj->approvals,
                                        'data' => $sj
                                    ]);
                                }
                                
                                // Add surat jalan bongkarans if available
                                if(isset($suratJalanBongkarans)) {
                                    foreach($suratJalanBongkarans as $sjb) {
                                        // Skip surat jalan bongkaran yang tidak memiliki kenek
                                        if (empty($sjb->kenek)) {
                                            continue;
                                        }
                                        
                                        $allSuratJalans->push([
                                            'type' => 'bongkaran',
                                            'id' => $sjb->id,
                                            'no_surat_jalan' => $sjb->nomor_surat_jalan,
                                            'tanggal_surat_jalan' => $sjb->tanggal_surat_jalan,
                                            'kenek' => $sjb->kenek,
                                            'tanggal_checkpoint' => null,
                                            'tandaTerima' => $sjb->tandaTerima,
                                            'kegiatan' => $sjb->kegiatan,
                                            'tanggal_tanda_terima' => $sjb->tandaTerima ? $sjb->tandaTerima->tanggal_tanda_terima : null,
                                            'approvals' => null,
                                            'data' => $sjb
                                        ]);
                                    }
                                }
                            @endphp
                            
                            @forelse($allSuratJalans as $item)
                                @php
                                    $inputPrefix = $item['type'] === 'regular' ? 'surat_jalan_data' : 'surat_jalan_bongkaran_data';
                                @endphp
                                <tr class="surat-jalan-row hover:bg-gray-50 transition-colors"
                                    data-nomor="{{ strtolower($item['no_surat_jalan'] ?? '') }}"
                                    data-kenek="{{ strtolower($item['kenek'] ?? '') }}">
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @php
                                            // Get NIK for this kenek from karyawans table
                                            $kenekNik = '-';
                                            if (!empty($item['kenek'])) {
                                                $karyawan = \App\Models\Karyawan::where('nama_lengkap', $item['kenek'])
                                                    ->orWhere('nama_panggilan', $item['kenek'])
                                                    ->first();
                                                if ($karyawan && $karyawan->nik) {
                                                    $kenekNik = $karyawan->nik;
                                                }
                                            }
                                        @endphp
                                        <input type="checkbox"
                                               name="{{ $inputPrefix }}[{{ $item['id'] }}][selected]"
                                               value="1"
                                               class="surat-jalan-checkbox h-3 w-3 text-indigo-600 border-gray-300 rounded"
                                               data-id="{{ $item['id'] }}"
                                               data-type="{{ $item['type'] }}"
                                               data-nomor="{{ $item['no_surat_jalan'] }}"
                                               data-no_surat_jalan="{{ $item['no_surat_jalan'] }}"
                                               data-kenek_nama="{{ $item['kenek'] }}"
                                               data-kenek_nik="{{ $kenekNik }}"
                                               data-tanggal="{{ $item['tanggal_tanda_terima'] ? \Carbon\Carbon::parse($item['tanggal_tanda_terima'])->format('Y-m-d') : '' }}"
                                               data-plat="{{ $item['type'] === 'regular' ? ($item['data']->no_plat ?? '-') : ($item['data']->no_plat ?? '-') }}"
                                               data-tujuan_pengambilan="{{ $item['type'] === 'regular' ? ($item['data']->tujuan_pengambilan ?? $item['data']->tempat_pengambilan ?? '-') : ($item['data']->tujuan_pengambilan ?? $item['data']->tempat_tujuan ?? '-') }}">
                                        <input type="hidden" name="{{ $inputPrefix }}[{{ $item['id'] }}][no_surat_jalan]" value="{{ $item['no_surat_jalan'] }}">
                                        <input type="hidden" name="{{ $inputPrefix }}[{{ $item['id'] }}][kenek_nama]" value="{{ $item['kenek'] }}">
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">
                                        {{ $item['no_surat_jalan'] ?? '-' }}
                                        @if($item['type'] === 'bongkaran')
                                            <span class="ml-1 px-1 py-0.5 text-xs font-semibold rounded bg-purple-100 text-purple-800">Bongkaran</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs text-center">{{ $item['tanggal_tanda_terima'] ? \Carbon\Carbon::parse($item['tanggal_tanda_terima'])->format('d/m/Y') : '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $item['kenek'] ?? '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-center text-xs">
                                        @if($item['tanggal_checkpoint'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-100 text-indigo-800" title="Checkpoint Kenek detected">Checkpoint</span>
                                        @endif
                                        @if($item['tandaTerima'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800 ml-1" title="Tanda Terima exists">Tanda Terima</span>
                                        @endif
                                        @if($item['kegiatan'] == 'bongkaran' && $item['tanggal_tanda_terima'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-teal-100 text-teal-800 ml-1" title="Bongkaran dengan tanggal tanda terima">Bongkaran TT</span>
                                        @endif
                                        @if($item['approvals'] && $item['approvals']->where('status', 'approved')->isNotEmpty())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-800 ml-1" title="Approved via approval flow">Approved</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-right text-xs">
                                        @php
                                            // Inisialisasi dengan 0
                                            $ritValue = 0;
                                            
                                            // Untuk surat jalan regular, cek field rit
                                            if ($item['type'] === 'regular') {
                                                $ritValue = $item['data']->rit ?? 0;
                                            }
                                            // Untuk bongkaran, cek field rit_kenek atau rit
                                            else if ($item['type'] === 'bongkaran') {
                                                $ritValue = $item['data']->rit_kenek ?? $item['data']->rit ?? 0;
                                            }
                                            
                                            // Jika rit masih kosong atau 0, coba ambil dari pricelist_rit
                                            if (!$ritValue || $ritValue == 0) {
                                                try {
                                                    // Query langsung ke tabel pricelist_rit untuk tujuan 'Kenek'
                                                    $pricelistRit = \DB::table('pricelist_rit')
                                                        ->where('tujuan', 'Kenek')
                                                        ->where('status', 'aktif')
                                                        ->first();
                                                    
                                                    if ($pricelistRit) {
                                                        $ritValue = $pricelistRit->tarif ?? 50000;
                                                    }
                                                } catch (\Exception $e) {
                                                    // Jika query gagal, gunakan default
                                                    $ritValue = 50000;
                                                }
                                            }
                                            
                                            // Pastikan nilai minimal 50000
                                            $ritValue = (float) ($ritValue ?: 50000);
                                            
                                            // Ensure minimum value
                                            if ($ritValue < 50000) {
                                                $ritValue = 50000;
                                            }
                                        @endphp
                                        <input type="number" 
                                               name="{{ $inputPrefix }}[{{ $item['id'] }}][uang_rit_kenek]" 
                                               class="uang-rit-kenek-input w-20 px-1 py-1 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-right"
                                               placeholder="50000" 
                                               value="{{ $ritValue }}"
                                               min="0" 
                                               step="1000"
                                               title="Uang Kenek: {{ $item['type'] === 'bongkaran' ? 'Bongkaran' : 'Regular' }} - {{ number_format($ritValue, 0, ',', '.') }}">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-2 py-4 text-center text-xs text-gray-500">
                                        <div class="flex flex-col items-center py-4">
                                            <svg class="h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <p class="font-medium">Tidak ada surat jalan tersedia</p>
                                            <p class="text-xs mt-1">Tidak ada surat jalan yang approved untuk diproses.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-100 border-t-2 border-gray-300">
                            <!-- Grand Total Per Person (will be populated by JavaScript) -->
                            <tbody id="grandTotalPerPerson" class="bg-yellow-50 border-t-2 border-yellow-300 hidden">
                                <!-- Per person totals will be inserted here by JavaScript -->
                            </tbody>
                            <!-- Overall Grand Total -->
                            <tr class="font-semibold text-gray-800 bg-gray-200">
                                <td class="px-2 py-3 text-xs font-bold" colspan="5">
                                    GRAND TOTAL KESELURUHAN
                                </td>
                                <td class="px-2 py-3 text-right text-xs font-bold text-indigo-600" id="grandTotalUangKenek">
                                    Rp 0
                                </td>
                            </tr>
                            <tr class="font-semibold text-gray-800 bg-gray-200">
                                <td class="px-2 py-3 text-xs font-bold" colspan="5">
                                    TOTAL HUTANG
                                </td>
                                <td class="px-2 py-3 text-right text-xs font-bold text-red-600" id="grandTotalUtang">
                                    Rp 0
                                </td>
                            </tr>
                            <tr class="font-semibold text-gray-800 bg-gray-200">
                                <td class="px-2 py-3 text-xs font-bold" colspan="5">
                                    TOTAL TABUNGAN
                                </td>
                                <td class="px-2 py-3 text-right text-xs font-bold text-green-600" id="grandTotalTabungan">
                                    Rp 0
                                </td>
                            </tr>
                            <tr class="font-semibold text-gray-800 bg-gray-200">
                                <td class="px-2 py-3 text-xs font-bold" colspan="5">
                                    TOTAL BPJS
                                </td>
                                <td class="px-2 py-3 text-right text-xs font-bold text-yellow-600" id="grandTotalBpjs">
                                    Rp 0
                                </td>
                            </tr>
                            <tr class="font-semibold text-gray-800 bg-purple-200">
                                <td class="px-2 py-3 text-xs font-bold" colspan="5">
                                    GRAND TOTAL BERSIH
                                </td>
                                <td class="px-2 py-3 text-right text-xs font-bold text-purple-600" id="grandTotalKeseluruhan">
                                    Rp 0
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                    <p class="text-xs text-gray-600">
                        * Pilih surat jalan dan masukkan nominal Uang Kenek, hutang, tabungan, dan BPJS untuk setiap surat jalan yang dipilih.
                        <br>* <strong>Grand Total = Uang Kenek - Hutang - Tabungan - BPJS</strong> (Hutang, Tabungan, dan BPJS mengurangi total yang diterima Kenek)
                    </p>
                </div>

                @error('surat_jalan_data')
                    <div class="bg-red-50 px-3 py-2 border-t border-red-200">
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    </div>
                @enderror
            </div>

            <!-- Submit Button -->
            @if($suratJalans->count() > 0)
                <div class="flex flex-col sm:flex-row justify-end gap-2">
                    <a href="{{ route('pranota-uang-rit-kenek.index') }}"
                       class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            id="submitBtn"
                            disabled>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Buat Pranota Uang Kenek
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Function to apply rit filter - redirect to same page with filter parameter
function applyRitFilter() {
    const ritFilter = document.getElementById('rit_filter').value;
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('rit_filter', ritFilter);
    window.location.href = currentUrl.toString();
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const suratJalanCheckboxes = document.querySelectorAll('.surat-jalan-checkbox');
    const uangRitKenekInputs = document.querySelectorAll('.uang-rit-kenek-input');
    const jumlahSuratJalanDisplay = document.getElementById('jumlah_surat_jalan_display');
    const jumlahKenekDisplay = document.getElementById('jumlah_Kenek_display');
    const totalUangRitDisplay = document.getElementById('total_uang_rit_display');
    const totalUtangDisplay = document.getElementById('total_utang_display');
    const totalTabunganDisplay = document.getElementById('total_tabungan_display');
    const totalBpjsDisplay = document.getElementById('total_bpjs_display');
    const grandTotalDisplay = document.getElementById('grand_total_display');
    const submitBtn = document.getElementById('submitBtn');
    const selectedSummary = document.getElementById('selectedSummary');
    const selectedCount = document.getElementById('selectedCount');
    const totalUangRit = document.getElementById('totalUangRit');

    function updateTotals() {
        let totalRit = 0;
        let count = 0;
        let personTotals = {}; // Object to store totals per person (keyed by NIK)

        // Update individual grand totals for all rows (checked and unchecked)
        suratJalanCheckboxes.forEach((checkbox, index) => {
            if (checkbox.checked) {
                count++;
                
                const uangRitKenekInput = uangRitKenekInputs[index];
                
                // Use NIK as key to group same person with different name variants
                const personNik = checkbox.dataset.kenek_nik || 'unknown';
                const personName = checkbox.dataset.kenek_nama || 'Tanpa Nama';
                
                // Initialize person totals if not exists (using NIK as key)
                if (!personTotals[personNik]) {
                    personTotals[personNik] = {
                        count: 0,
                        uangKenek: 0,
                        nama: personName, // Store name for display
                        nik: personNik
                    };
                }
                
                personTotals[personNik].count++;
                
                if (uangRitKenekInput) {
                    const rowUangKenek = parseFloat(uangRitKenekInput.value) || 0;
                    totalRit += rowUangKenek;
                    personTotals[personNik].uangKenek += rowUangKenek;
                }
            }
        });

        // Update displays
        jumlahSuratJalanDisplay.value = count;
        
        if (jumlahKenekDisplay) {
            jumlahKenekDisplay.value = Object.keys(personTotals).length;
        }
        
        totalUangRitDisplay.value = 'Rp ' + totalRit.toLocaleString('id-ID');

        // Update summary
        if (selectedCount && totalUangRit) {
            selectedCount.textContent = count;
            totalUangRit.textContent = 'Rp ' + totalRit.toLocaleString('id-ID');
        }

        // Update Kenek count in summary
        const selectedKenekCount = document.getElementById('selectedKenekCount');
        if (selectedKenekCount) {
            selectedKenekCount.textContent = Object.keys(personTotals).length;
        }

        // Update per person totals
        updatePersonTotals(personTotals);

        // Show/hide summary and enable/disable submit button
        if (count > 0) {
            if (selectedSummary) selectedSummary.classList.remove('hidden');
            if (submitBtn) submitBtn.disabled = false;
        } else {
            if (selectedSummary) selectedSummary.classList.add('hidden');
            if (submitBtn) submitBtn.disabled = true;
        }

        return { totalRit, count, personTotals };
    }

    function updatePersonTotals(personTotals) {
        const grandTotalPerPersonContainer = document.getElementById('grandTotalPerPerson');
        
        if (!grandTotalPerPersonContainer) return;

        // Clear existing content
        grandTotalPerPersonContainer.innerHTML = '';

        // Check if there are any person totals to display
        const hasPersonTotals = Object.keys(personTotals).length > 0;
        
        if (hasPersonTotals) {
            grandTotalPerPersonContainer.classList.remove('hidden');
            
            // Add header row for per person totals
            const headerRow = document.createElement('tr');
            headerRow.className = 'bg-yellow-100 font-semibold text-gray-700';
            headerRow.innerHTML = `
                <td class="px-2 py-2 text-xs font-bold" colspan="6">
                    📊 TOTAL PER Kenek
                </td>
            `;
            grandTotalPerPersonContainer.appendChild(headerRow);

            // Sort persons alphabetically by name
            const sortedPersonNiks = Object.keys(personTotals).sort((a, b) => {
                return personTotals[a].nama.localeCompare(personTotals[b].nama);
            });

            // Create rows for each person
            sortedPersonNiks.forEach(personNik => {
                const totals = personTotals[personNik];
                
                const personRow = document.createElement('tr');
                personRow.className = 'bg-yellow-50 text-gray-700 border-t border-yellow-200';
                
                personRow.innerHTML = `
                    <td class="px-2 py-2 text-xs font-medium" colspan="2">
                        👤 ${totals.nama} <span class="text-gray-500">(NIK: ${totals.nik})</span>
                    </td>
                    <td class="px-2 py-2 text-xs text-center">
                        ${totals.count} surat jalan
                    </td>
                    <td class="px-2 py-2 text-xs text-center">
                        -
                    </td>
                    <td class="px-2 py-2 text-right text-xs font-semibold text-indigo-600">
                        Rp ${totals.uangKenek.toLocaleString('id-ID')}
                    </td>
                    <td class="px-2 py-2 text-right text-xs">
                        <div class="flex gap-1">
                            <input type="number" 
                                   class="person-utang-input w-16 px-1 py-1 text-xs border border-red-300 rounded focus:ring-1 focus:ring-red-500 focus:border-red-500 text-right"
                                   placeholder="Hutang" 
                                   value="0"
                                   min="0" 
                                   step="1000"
                                   data-person-nik="${personNik}"
                                   data-person-nama="${totals.nama}">
                            <input type="number" 
                                   class="person-tabungan-input w-16 px-1 py-1 text-xs border border-green-300 rounded focus:ring-1 focus:ring-green-500 focus:border-green-500 text-right"
                                   placeholder="Tabungan" 
                                   value="0"
                                   min="0" 
                                   step="1000"
                                   data-person-nik="${personNik}"
                                   data-person-nama="${totals.nama}">
                            <input type="number" 
                                   class="person-bpjs-input w-16 px-1 py-1 text-xs border border-yellow-300 rounded focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 text-right"
                                   placeholder="BPJS" 
                                   value="0"
                                   min="0" 
                                   step="1000"
                                   data-person-nik="${personNik}"
                                   data-person-nama="${totals.nama}">
                            <div class="person-grand-total w-20 px-1 py-1 text-xs bg-purple-50 border border-purple-200 rounded text-right font-semibold text-purple-700"
                                 data-person-nik="${personNik}">
                                Rp ${totals.uangKenek.toLocaleString('id-ID')}
                            </div>
                        </div>
                    </td>
                `;
                
                grandTotalPerPersonContainer.appendChild(personRow);
            });
            
            // Add event listeners for person-level inputs
            addPersonInputListeners();
        } else {
            grandTotalPerPersonContainer.classList.add('hidden');
        }
    }

    function addPersonInputListeners() {
        const personUtangInputs = document.querySelectorAll('.person-utang-input');
        const personTabunganInputs = document.querySelectorAll('.person-tabungan-input');
        const personBpjsInputs = document.querySelectorAll('.person-bpjs-input');
        
        personUtangInputs.forEach(input => {
            input.addEventListener('input', updatePersonGrandTotals);
        });
        
        personTabunganInputs.forEach(input => {
            input.addEventListener('input', updatePersonGrandTotals);
        });
        
        personBpjsInputs.forEach(input => {
            input.addEventListener('input', updatePersonGrandTotals);
        });
    }
    
    function updatePersonGrandTotals() {
        const personUtangInputs = document.querySelectorAll('.person-utang-input');
        const personTabunganInputs = document.querySelectorAll('.person-tabungan-input');
        const personBpjsInputs = document.querySelectorAll('.person-bpjs-input');
        const personGrandTotals = document.querySelectorAll('.person-grand-total');
        
        // Calculate grand totals for each person
        personGrandTotals.forEach(grandTotalDiv => {
            const personNik = grandTotalDiv.dataset.person_nik;
            
            // Find corresponding inputs using NIK
            const utangInput = document.querySelector(`.person-utang-input[data-person-nik="${personNik}"]`);
            const tabunganInput = document.querySelector(`.person-tabungan-input[data-person-nik="${personNik}"]`);
            const bpjsInput = document.querySelector(`.person-bpjs-input[data-person-nik="${personNik}"]`);
            
            // Get person's total Uang Kenek from checked checkboxes (matching by NIK)
            let personUangKenek = 0;
            suratJalanCheckboxes.forEach((checkbox, index) => {
                if (checkbox.checked && checkbox.dataset.kenek_nik === personNik) {
                    const uangRitKenekInput = uangRitKenekInputs[index];
                    if (uangRitKenekInput) {
                        personUangKenek += parseFloat(uangRitKenekInput.value) || 0;
                    }
                }
            });
            
            const utang = utangInput ? (parseFloat(utangInput.value) || 0) : 0;
            const tabungan = tabunganInput ? (parseFloat(tabunganInput.value) || 0) : 0;
            const bpjs = bpjsInput ? (parseFloat(bpjsInput.value) || 0) : 0;
            
            // Calculate grand total: Uang Kenek - Hutang - Tabungan - BPJS
            const grandTotal = personUangKenek - utang - tabungan - bpjs;
            grandTotalDiv.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
        });
        
        // Update overall totals
        updateOverallTotals();
    }
    
    function updateOverallTotals() {
        const personUtangInputs = document.querySelectorAll('.person-utang-input');
        const personTabunganInputs = document.querySelectorAll('.person-tabungan-input');
        const personBpjsInputs = document.querySelectorAll('.person-bpjs-input');
        
        let totalUtang = 0;
        let totalTabungan = 0;
        let totalBpjs = 0;
        let totalUangKenek = 0;
        
        // Calculate total Uang Kenek from checked checkboxes
        suratJalanCheckboxes.forEach((checkbox, index) => {
            if (checkbox.checked) {
                const uangRitKenekInput = uangRitKenekInputs[index];
                if (uangRitKenekInput) {
                    totalUangKenek += parseFloat(uangRitKenekInput.value) || 0;
                }
            }
        });
        
        // Calculate total hutang, tabungan, and bpjs from person inputs
        personUtangInputs.forEach(input => {
            totalUtang += parseFloat(input.value) || 0;
        });
        
        personTabunganInputs.forEach(input => {
            totalTabungan += parseFloat(input.value) || 0;
        });
        
        personBpjsInputs.forEach(input => {
            totalBpjs += parseFloat(input.value) || 0;
        });
        
        const overallGrandTotal = totalUangKenek - totalUtang - totalTabungan - totalBpjs;
        
        // Update display elements
        if (totalUtangDisplay) {
            totalUtangDisplay.value = 'Rp ' + totalUtang.toLocaleString('id-ID');
        }
        
        if (totalTabunganDisplay) {
            totalTabunganDisplay.value = 'Rp ' + totalTabungan.toLocaleString('id-ID');
        }
        
        if (totalBpjsDisplay) {
            totalBpjsDisplay.value = 'Rp ' + totalBpjs.toLocaleString('id-ID');
        }
        
        if (grandTotalDisplay) {
            grandTotalDisplay.value = 'Rp ' + overallGrandTotal.toLocaleString('id-ID');
        }
        
        // Update summary
        const totalUtangSummary = document.getElementById('totalUtangSummary');
        const totalTabunganSummary = document.getElementById('totalTabunganSummary');
        const totalBpjsSummary = document.getElementById('totalBpjsSummary');
        const grandTotalSummary = document.getElementById('grandTotalSummary');

        if (totalUtangSummary) {
            totalUtangSummary.textContent = 'Rp ' + totalUtang.toLocaleString('id-ID');
        }

        if (totalTabunganSummary) {
            totalTabunganSummary.textContent = 'Rp ' + totalTabungan.toLocaleString('id-ID');
        }
        
        if (totalBpjsSummary) {
            totalBpjsSummary.textContent = 'Rp ' + totalBpjs.toLocaleString('id-ID');
        }
        
        if (grandTotalSummary) {
            grandTotalSummary.textContent = 'Rp ' + overallGrandTotal.toLocaleString('id-ID');
        }

        // Update footer totals
        const grandTotalUangKenek = document.getElementById('grandTotalUangKenek');
        const grandTotalUtang = document.getElementById('grandTotalUtang');
        const grandTotalTabungan = document.getElementById('grandTotalTabungan');
        const grandTotalBpjs = document.getElementById('grandTotalBpjs');
        const grandTotalKeseluruhan = document.getElementById('grandTotalKeseluruhan');

        if (grandTotalUangKenek) {
            grandTotalUangKenek.textContent = 'Rp ' + totalUangKenek.toLocaleString('id-ID');
        }

        if (grandTotalUtang) {
            grandTotalUtang.textContent = 'Rp ' + totalUtang.toLocaleString('id-ID');
        }

        if (grandTotalTabungan) {
            grandTotalTabungan.textContent = 'Rp ' + totalTabungan.toLocaleString('id-ID');
        }
        
        if (grandTotalBpjs) {
            grandTotalBpjs.textContent = 'Rp ' + totalBpjs.toLocaleString('id-ID');
        }
        
        if (grandTotalKeseluruhan) {
            grandTotalKeseluruhan.textContent = 'Rp ' + overallGrandTotal.toLocaleString('id-ID');
        }
    }

    // Checkbox change events
    suratJalanCheckboxes.forEach((checkbox, index) => {
        checkbox.addEventListener('change', function() {
            updateTotals();
            updateSelectAllState();
        });
    });

    // Uang rit input change events
    uangRitKenekInputs.forEach(input => {
        input.addEventListener('input', updateTotals);
    });

    function updateSelectAllState() {
        if (!selectAllCheckbox) return;

        const visibleCheckboxes = document.querySelectorAll('.surat-jalan-checkbox:not([style*="display: none"])');
        const checkedVisibleCheckboxes = document.querySelectorAll('.surat-jalan-checkbox:not([style*="display: none"]):checked');

        if (visibleCheckboxes.length === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedVisibleCheckboxes.length === visibleCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedVisibleCheckboxes.length > 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            const visibleCheckboxes = document.querySelectorAll('.surat-jalan-checkbox:not([style*="display: none"])');
            visibleCheckboxes.forEach((checkbox, globalIndex) => {
                checkbox.checked = this.checked;
            });
            updateTotals();
        });
    }

    // Select all button
    const selectAllBtn = document.getElementById('selectAllBtn');
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            const visibleCheckboxes = document.querySelectorAll('.surat-jalan-checkbox:not([style*="display: none"])');
            visibleCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            updateTotals();
            updateSelectAllState();
        });
    }

    // Deselect all button
    const deselectAllBtn = document.getElementById('deselectAllBtn');
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function() {
            const visibleCheckboxes = document.querySelectorAll('.surat-jalan-checkbox:not([style*="display: none"])');
            visibleCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateTotals();
            updateSelectAllState();
        });
    }

    // Search functionality
    const searchInput = document.getElementById('searchSuratJalan');
    const clearSearchBtn = document.getElementById('clearSearch');
    const tableRows = document.querySelectorAll('tr.surat-jalan-row');

    let searchTimeout;
    function debounceSearch(searchTerm) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterTable(searchTerm);
        }, 300);
    }

    function filterTable(searchTerm) {
        const term = searchTerm.toLowerCase().trim();
        let visibleCount = 0;
        const searchResultsEl = document.getElementById('searchResults');

        tableRows.forEach(row => {
            const nomor = row.dataset.nomor || '';
            const kenek = row.dataset.kenek || '';
            const plat = row.dataset.plat || '';

            const isVisible = nomor.includes(term) ||
                            kenek.includes(term) ||
                            plat.includes(term);

            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        // Update search results count
        if (searchResultsEl) {
            if (term === '') {
                searchResultsEl.classList.add('hidden');
            } else {
                searchResultsEl.textContent = `${visibleCount} surat jalan ditemukan`;
                searchResultsEl.classList.remove('hidden');
            }
        }

        updateSelectAllState();
        updateTotals();
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            debounceSearch(this.value);
        });
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            if (searchInput) {
                searchInput.value = '';
                filterTable('');
                searchInput.focus();
            }
        });
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+F or Cmd+F to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        // Escape to clear search
        if (e.key === 'Escape' && document.activeElement === searchInput) {
            searchInput.value = '';
            filterTable('');
        }
    });

    // Form validation
    const pranotaForm = document.getElementById('pranotaForm');
    if (pranotaForm) {
        pranotaForm.addEventListener('submit', function(e) {
            const checkedCheckboxes = document.querySelectorAll('.surat-jalan-checkbox:checked');
            if (checkedCheckboxes.length === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal satu surat jalan untuk membuat pranota Uang Kenek.');
                return false;
            }

            // Check if all selected items have valid values
            let hasEmptyValues = false;
            checkedCheckboxes.forEach(checkbox => {
                const uangRitKenekInput = document.querySelector(`input[name*="[${checkbox.dataset.id}][uang_rit_kenek]"]`);
                
                if (uangRitKenekInput && (!uangRitKenekInput.value || parseFloat(uangRitKenekInput.value) < 0)) {
                    hasEmptyValues = true;
                }
            });

            if (hasEmptyValues) {
                e.preventDefault();
                alert('Silakan masukkan nilai yang valid untuk Uang Kenek pada semua surat jalan yang dipilih.');
                return false;
            }

            // Collect hutang and tabungan data per Kenek
            collectKenekDetailsData();
        });
    }

    function collectKenekDetailsData() {
        const kenekDetailsContainer = document.getElementById('kenekDetailsInputs');
        const personUtangInputs = document.querySelectorAll('.person-utang-input');
        const personTabunganInputs = document.querySelectorAll('.person-tabungan-input');
        const personBpjsInputs = document.querySelectorAll('.person-bpjs-input');
        
        // Clear existing inputs
        kenekDetailsContainer.innerHTML = '';
        
        // Create hidden inputs for each Kenek's hutang, tabungan, and bpjs (using nama as key for backend compatibility)
        personUtangInputs.forEach(input => {
            const kenekNama = input.dataset.person_nama; // Use nama for backend
            const hutangValue = input.value || 0;
            
            const hutangInput = document.createElement('input');
            hutangInput.type = 'hidden';
            hutangInput.name = `kenek_details[${kenekNama}][hutang]`;
            hutangInput.value = hutangValue;
            kenekDetailsContainer.appendChild(hutangInput);
        });
        
        personTabunganInputs.forEach(input => {
            const kenekNama = input.dataset.person_nama;
            const tabunganValue = input.value || 0;
            
            const tabunganInput = document.createElement('input');
            tabunganInput.type = 'hidden';
            tabunganInput.name = `kenek_details[${kenekNama}][tabungan]`;
            tabunganInput.value = tabunganValue;
            kenekDetailsContainer.appendChild(tabunganInput);
        });
        
        personBpjsInputs.forEach(input => {
            const kenekNama = input.dataset.person_nama;
            const bpjsValue = input.value || 0;
            
            const bpjsInput = document.createElement('input');
            bpjsInput.type = 'hidden';
            bpjsInput.name = `kenek_details[${kenekNama}][bpjs]`;
            bpjsInput.value = bpjsValue;
            kenekDetailsContainer.appendChild(bpjsInput);
        });
    }

    // Download Excel functionality
    const downloadExcelBtn = document.getElementById('downloadExcelBtn');
    if (downloadExcelBtn) {
        downloadExcelBtn.addEventListener('click', function() {
            const checkedCheckboxes = document.querySelectorAll('.surat-jalan-checkbox:checked');
            
            if (checkedCheckboxes.length === 0) {
                alert('Silakan pilih minimal satu surat jalan untuk download Excel.');
                return;
            }

            // Collect selected surat jalan IDs and types
            const selectedData = [];
            checkedCheckboxes.forEach(checkbox => {
                const uangKenekInput = checkbox.closest('tr').querySelector('.uang-rit-kenek-input');
                selectedData.push({
                    id: checkbox.dataset.id,
                    type: checkbox.dataset.type,
                    no_surat_jalan: checkbox.dataset.no_surat_jalan || '-',
                    tanggal_surat_jalan: checkbox.dataset.tanggal || '-',
                    kenek_nama: checkbox.dataset.kenek_nama || '-',
                    kenek_nik: checkbox.dataset.kenek_nik || '-',
                    no_plat: checkbox.dataset.plat || '-',
                    uang_rit_kenek: uangKenekInput ? uangKenekInput.value : 0,
                    tujuan_pengambilan: checkbox.dataset.tujuan_pengambilan || '-'
                });
            });
            
            console.log('Selected data for export:', selectedData);

            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("pranota-uang-rit-kenek.export-excel") }}';
            form.target = '_blank';

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add selected data as JSON
            const dataInput = document.createElement('input');
            dataInput.type = 'hidden';
            dataInput.name = 'selected_data';
            dataInput.value = JSON.stringify(selectedData);
            form.appendChild(dataInput);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        });
    }

    // Initialize
    updateTotals();
    updateSelectAllState();
});
</script>
@endpush
