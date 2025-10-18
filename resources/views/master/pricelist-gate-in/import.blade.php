@extends('layouts.app')

@section('title', 'Import Pricelist Gate In CSV')
@section('page_title', 'Import Pricelist Gate In CSV')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Import Pricelist Gate In CSV</h1>
                    <p class="mt-1 text-sm text-gray-600">Upload file CSV untuk import data pricelist gate in secara massal</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('master.pricelist-gate-in.download-template') }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Template
                    </a>
                    <a href="{{ route('master.pricelist-gate-in.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan:</h3>
                        <ul class="mt-2 text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Import Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-500 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-medium text-blue-800 mb-2">Petunjuk Import CSV</h3>
                    <div class="text-sm text-blue-700 space-y-2">
                        <p><strong>1. Download Template:</strong> Gunakan tombol "Download Template" untuk mendapatkan format CSV yang benar.</p>
                        <p><strong>2. Format File:</strong> File CSV harus menggunakan delimiter titik koma (;) dan encoding UTF-8.</p>
                        <p><strong>3. Header Kolom:</strong> kode, keterangan, catatan, tarif, status</p>
                        <p><strong>4. Ketentuan Data:</strong></p>
                        <ul class="ml-4 space-y-1">
                            <li>• <strong>kode:</strong> Wajib diisi, maksimal 20 karakter, harus unik</li>
                            <li>• <strong>keterangan:</strong> Wajib diisi, maksimal 255 karakter</li>
                            <li>• <strong>catatan:</strong> Opsional, maksimal 500 karakter</li>
                            <li>• <strong>tarif:</strong> Wajib diisi, format angka (contoh: 150000 atau 150000.50)</li>
                            <li>• <strong>status:</strong> Opsional, nilai: 'aktif' atau 'tidak_aktif' (default: aktif jika kosong)</li>
                        </ul>
                        <p><strong>5. Ukuran File:</strong> Maksimal 2MB</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Upload File CSV</h3>
                <p class="mt-1 text-sm text-gray-600">Pilih file CSV yang akan diimport</p>
            </div>

            <form action="{{ route('master.pricelist-gate-in.import.process') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf

                <!-- File Upload -->
                <div class="mb-6">
                    <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        File CSV <span class="text-red-500">*</span>
                    </label>

                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition-colors duration-200">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="csv_file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Upload file</span>
                                    <input id="csv_file" name="csv_file" type="file" accept=".csv,.txt" required class="sr-only">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">CSV, TXT hingga 2MB</p>
                        </div>
                    </div>

                    <!-- Selected file display -->
                    <div id="selected-file" class="mt-3 hidden">
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span id="file-name" class="text-sm text-gray-900 font-medium"></span>
                            <span id="file-size" class="text-sm text-gray-500 ml-2"></span>
                        </div>
                    </div>

                    @error('csv_file')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Sample Data Preview -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Contoh Format Data CSV:</h4>
                    <div class="bg-white p-3 rounded border font-mono text-xs overflow-x-auto">
                        <div class="text-gray-600">kode;keterangan;catatan;tarif;status</div>
                        <div class="text-gray-800">GATE20;Gate In 20 Feet;Tarif gate in kontainer 20 feet;150000;aktif</div>
                        <div class="text-gray-800">GATE40;Gate In 40 Feet;Tarif gate in kontainer 40 feet;250000;aktif</div>
                        <div class="text-gray-800">GATEOV;Gate In Over Size;;500000;</div>
                        <div class="text-gray-500 text-xs mt-1">* Status akan default ke 'aktif' jika kosong</div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('master.pricelist-gate-in.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('csv_file');
    const selectedFileDiv = document.getElementById('selected-file');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Show selected file info
            fileName.textContent = file.name;
            fileSize.textContent = `(${formatFileSize(file.size)})`;
            selectedFileDiv.classList.remove('hidden');

            // Validate file type
            const allowedTypes = ['.csv', '.txt'];
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();

            if (!allowedTypes.includes(fileExtension)) {
                alert('Format file tidak didukung. Hanya file CSV dan TXT yang diperbolehkan.');
                fileInput.value = '';
                selectedFileDiv.classList.add('hidden');
                return;
            }

            // Validate file size (2MB = 2048KB = 2097152 bytes)
            if (file.size > 2097152) {
                alert('Ukuran file terlalu besar. Maksimal 2MB diperbolehkan.');
                fileInput.value = '';
                selectedFileDiv.classList.add('hidden');
                return;
            }
        } else {
            selectedFileDiv.classList.add('hidden');
        }
    });

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Form submission loading state
    document.querySelector('form').addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Memproses Import...
        `;
    });

    // Drag and drop functionality
    const dropZone = document.querySelector('.border-dashed');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        dropZone.classList.add('border-indigo-400', 'bg-indigo-50');
    }

    function unhighlight() {
        dropZone.classList.remove('border-indigo-400', 'bg-indigo-50');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            fileInput.files = files;
            fileInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }
});
</script>
@endpush
