@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Import Pranota Kontainer Sewa</h1>
                <p class="text-xs text-gray-600 mt-1">Import pranota dari file CSV berdasarkan group dan periode</p>
            </div>
            <a href="{{ route('pranota.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Content -->
        <div class="p-4">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
                    {{ session('success') }}

                    @if(session('import_result'))
                        @php $result = session('import_result'); @endphp
                        <div class="mt-3 text-sm">
                            <div class="flex items-center gap-2 mb-2">
                                <strong>Detail Import:</strong>
                                @if(isset($result['grouping_mode_text']))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Mode: {{ $result['grouping_mode_text'] }}
                                    </span>
                                @endif
                            </div>
                            <ul class="list-disc list-inside mt-2">
                                <li>Berhasil: {{ $result['imported'] }} pranota dibuat</li>
                                <li>Total: {{ $result['total_kontainers'] ?? 0 }} kontainer masuk pranota</li>
                                @if(isset($result['use_vendor_invoice_grouping']) && $result['use_vendor_invoice_grouping'])
                                    @php
                                        $totalRows = count(session('csv_rows', []));
                                        $efficiency = $totalRows > 0 ? round((($totalRows - $result['imported']) / $totalRows) * 100, 1) : 0;
                                    @endphp
                                    <li class="text-green-700">Efisiensi Grouping: {{ $efficiency }}% ({{ $totalRows }} kontainer → {{ $result['imported'] }} pranota)</li>
                                @endif
                                @if(!empty($result['not_found']))
                                    <li class="text-yellow-700">Tidak ditemukan: {{ count($result['not_found']) }} kontainer</li>
                                @endif
                                @if(!empty($result['errors']))
                                    <li class="text-red-700">Error: {{ count($result['errors']) }} baris</li>
                                @endif
                            </ul>

                            @if(!empty($result['pranota_details']))
                                <div class="mt-3 p-3 bg-white border border-gray-300 rounded">
                                    <strong>Pranota yang Dibuat:</strong>
                                    <div class="mt-2 space-y-2">
                                        @foreach($result['pranota_details'] as $detail)
                                            <div class="text-xs p-2 bg-gray-50 border border-gray-200 rounded">
                                                <div class="font-semibold text-indigo-700">{{ $detail['no_invoice'] }}</div>
                                                <div class="mt-1">
                                                    @if(isset($detail['grouping_mode']) && $detail['grouping_mode'] === 'vendor_invoice')
                                                        <span class="font-medium">Invoice: {{ $detail['invoice_vendor'] }}</span> -
                                                        <span class="font-medium">Bank: {{ $detail['bank_number'] }}</span>
                                                    @else
                                                        <span class="font-medium">Group {{ $detail['group'] ?? 'N/A' }}</span> -
                                                        <span class="font-medium">Periode {{ $detail['periode'] ?? 'N/A' }}</span>
                                                    @endif
                                                </div>
                                                <div class="mt-1">{{ $detail['jumlah_kontainer'] }} kontainer | Rp {{ number_format($detail['total_amount'], 0, ',', '.') }}</div>
                                                <div class="mt-1 text-gray-600">{{ $detail['kontainers'] }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($result['not_found']) || !empty($result['errors']))
                                <button type="button" onclick="toggleDetails()" class="mt-2 text-blue-600 hover:text-blue-800 underline">
                                    Lihat Detail
                                </button>
                                <div id="importDetails" class="hidden mt-3 p-3 bg-white border border-gray-300 rounded max-h-60 overflow-y-auto">
                                    @if(!empty($result['not_found']))
                                        <div class="mb-3">
                                            <strong class="text-yellow-700">Kontainer Tidak Ditemukan:</strong>
                                            <ul class="list-disc list-inside mt-1 text-xs">
                                                @foreach($result['not_found'] as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if(!empty($result['errors']))
                                        <div>
                                            <strong class="text-red-700">Error:</strong>
                                            <ul class="list-disc list-inside mt-1 text-xs">
                                                @foreach($result['errors'] as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-red-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <div class="font-medium mb-1">Format CSV Error</div>
                            <div class="text-sm whitespace-pre-line">{{ session('error') }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Instructions -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-3 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-blue-900 flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Petunjuk Import
                    </h3>
                    <button type="button" onclick="toggleFullInstructions()" class="text-blue-700 hover:text-blue-900 text-xs flex items-center">
                        <span id="instructionToggleText">Detail</span>
                        <svg id="instructionToggleIcon" class="h-3 w-3 ml-1 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                    <div class="bg-white bg-opacity-60 rounded p-2.5 border border-blue-100">
                        <h4 class="font-semibold text-blue-900 mb-1.5 flex items-center">
                            <span class="bg-blue-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] mr-1.5">1</span>
                            Format Standar
                        </h4>
                        <ul class="text-blue-800 space-y-0.5 ml-5">
                            <li>• <strong>Group</strong> & <strong>Periode</strong></li>
                            <li>• <strong>Nomor Kontainer</strong></li>
                            <li>• keterangan, due_date (opsional)</li>
                        </ul>
                    </div>
                    <div class="bg-white bg-opacity-60 rounded p-2.5 border border-green-100">
                        <h4 class="font-semibold text-green-900 mb-1.5 flex items-center">
                            <span class="bg-green-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] mr-1.5">⚡</span>
                            Auto Smart Grouping
                        </h4>
                        <ul class="text-green-800 space-y-0.5 ml-5">
                            <li>• <strong>No.InvoiceVendor</strong> & <strong>No.Bank</strong></li>
                            <li>• <strong>Nomor Kontainer</strong></li>
                            <li>• Efisiensi tinggi!</li>
                        </ul>
                    </div>
                    <div class="bg-white bg-opacity-60 rounded p-2.5 border border-indigo-100">
                        <h4 class="font-semibold text-indigo-900 mb-1.5 flex items-center">
                            <span class="bg-indigo-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] mr-1.5">✓</span>
                            Hasil Import
                        </h4>
                        <ul class="text-indigo-800 space-y-0.5 ml-5">
                            <li>• 1 pranota = beberapa kontainer</li>
                            <li>• Auto-detect format</li>
                            <li>• Status → "masuk pranota"</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-2.5 space-y-2">
                    <div class="p-2 bg-blue-100 bg-opacity-70 border border-blue-300 rounded text-xs flex items-start">
                        <svg class="h-4 w-4 text-blue-600 mr-1.5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <strong>✨ Smart Import:</strong> File dengan kolom "No.InvoiceVendor" & "No.Bank" akan otomatis menggunakan grouping efisien untuk mengurangi jumlah pranota
                        </div>
                    </div>

                    <div class="p-2 bg-green-50 border border-green-200 rounded text-xs">
                        <strong class="text-green-800">📋 Column Detection:</strong>
                        <div class="mt-1 text-green-700 text-[11px]">
                            Sistem akan otomatis mendeteksi variasi nama kolom seperti:
                            <span class="font-mono bg-green-100 px-1 rounded">"Group"</span>,
                            <span class="font-mono bg-green-100 px-1 rounded">"group"</span>,
                            <span class="font-mono bg-green-100 px-1 rounded">"Nomor Kontainer"</span>,
                            <span class="font-mono bg-green-100 px-1 rounded">"kontainer"</span>, dll.
                        </div>
                    </div>
                </div>

                <div id="fullInstructions" class="hidden mt-3 pt-3 border-t border-blue-200">
                    <ol class="list-decimal list-inside space-y-1 text-xs text-blue-900">
                        <li>Download template CSV atau gunakan file export</li>
                        <li>Pastikan kolom Group, Periode, dan Nomor Kontainer sesuai</li>
                        <li>Save dengan encoding UTF-8, delimiter semicolon (;)</li>
                        <li>Upload file CSV menggunakan form di bawah</li>
                        <li>Sistem akan mengelompokkan otomatis per Group & Periode</li>
                    </ol>
                </div>
            </div>

            <!-- Download Template & Upload Form -->
            <div class="bg-gray-50 rounded-lg p-3 mb-4 border border-gray-200">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                    <!-- Template Download -->
                    <div class="lg:col-span-1">
                        <a href="{{ route('pranota-kontainer-sewa.template.csv') }}"
                           class="group block w-full text-center px-3 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all duration-150 shadow-sm hover:shadow">
                            <svg class="h-4 w-4 mx-auto mb-1 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <div class="text-xs font-medium">Download Template</div>
                        </a>
                    </div>

                    <!-- Upload Form -->
                    <div class="lg:col-span-3">
                        <form action="{{ route('pranota-kontainer-sewa.import.process') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="space-y-2">
                                <label for="file" class="block text-xs font-medium text-gray-700">
                                    Pilih File CSV <span class="text-red-600">*</span>
                                </label>
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <div class="flex-1">
                                        <input type="file"
                                               id="file"
                                               name="file"
                                               accept=".csv,.txt"
                                               required
                                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                        @error('file')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('pranota.index') }}"
                                           class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-white transition-colors duration-150 text-xs font-medium whitespace-nowrap">
                                            Batal
                                        </a>
                                        <button type="submit"
                                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors duration-150 flex items-center text-xs font-medium whitespace-nowrap shadow-sm hover:shadow">
                                            <svg class="h-3.5 w-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                            </svg>
                                            Import
                                        </button>
                                    </div>
                                </div>

                                <!-- File Info -->
                                <div id="fileInfo" class="hidden bg-white border border-gray-200 rounded p-2 text-xs">
                                    <span class="text-gray-600">📄</span> <span id="fileName" class="font-medium text-gray-900"></span>
                                    <span class="text-gray-400">•</span> <span id="fileSize" class="text-gray-600"></span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Important Notes & Example -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <!-- Important Notes -->
                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg p-3">
                    <h4 class="font-semibold text-yellow-900 mb-2 flex items-center text-xs">
                        <svg class="h-4 w-4 mr-1.5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Catatan Penting
                    </h4>
                    <ul class="text-[11px] text-yellow-900 space-y-1 leading-relaxed">
                        <li class="flex items-start">
                            <span class="text-yellow-600 mr-1.5 flex-shrink-0">▸</span>
                            <span>Hanya kontainer <strong>belum masuk pranota</strong></span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-600 mr-1.5 flex-shrink-0">▸</span>
                            <span><strong>1 Pranota = beberapa kontainer</strong> (per Group & Periode)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-600 mr-1.5 flex-shrink-0">▸</span>
                            <span>Delimiter: <strong>semicolon (;)</strong></span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-600 mr-1.5 flex-shrink-0">▸</span>
                            <span>Nomor pranota: format <strong>PMS</strong> otomatis</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-600 mr-1.5 flex-shrink-0">▸</span>
                            <span>Status akan berubah → <strong>"masuk pranota"</strong></span>
                        </li>
                    </ul>
                </div>

                <!-- Example -->
                <div class="bg-gradient-to-br from-gray-50 to-slate-50 border border-gray-200 rounded-lg p-3">
                    <h4 class="font-semibold text-gray-900 mb-2 text-xs flex items-center">
                        <svg class="h-4 w-4 mr-1.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Contoh Hasil Import
                    </h4>
                    <div class="text-[11px] text-gray-700 space-y-1.5">
                        <div class="p-2 bg-white border border-gray-300 rounded shadow-sm">
                            <div class="font-medium text-gray-900 mb-0.5">📥 Input CSV:</div>
                            <div class="text-gray-600">Group 1, Periode 1 → <strong>3 kontainer</strong></div>
                        </div>
                        <div class="flex items-center justify-center text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                        </div>
                        <div class="p-2 bg-green-50 border border-green-300 rounded shadow-sm">
                            <div class="font-medium text-green-800 mb-0.5">✓ Output:</div>
                            <div class="text-green-700"><strong>1 Pranota</strong> (Group 1, Periode 1)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Show file info when file is selected
document.getElementById('file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        document.getElementById('fileInfo').classList.remove('hidden');
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = formatFileSize(file.size);
    } else {
        document.getElementById('fileInfo').classList.add('hidden');
    }
});

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

function toggleDetails() {
    const details = document.getElementById('importDetails');
    details.classList.toggle('hidden');
}

function toggleFullInstructions() {
    const instructions = document.getElementById('fullInstructions');
    const toggleText = document.getElementById('instructionToggleText');
    const toggleIcon = document.getElementById('instructionToggleIcon');

    if (instructions.classList.contains('hidden')) {
        instructions.classList.remove('hidden');
        toggleText.textContent = 'Sembunyikan';
        toggleIcon.classList.add('rotate-180');
    } else {
        instructions.classList.add('hidden');
        toggleText.textContent = 'Detail';
        toggleIcon.classList.remove('rotate-180');
    }
}
</script>
@endsection
