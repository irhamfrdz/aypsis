@extends('layouts.app')

@section('title', 'Import Jenis Barang CSV')
@section('page_title', 'Import Jenis Barang CSV')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Import Jenis Barang CSV</h1>
                    <p class="mt-1 text-sm text-gray-600">Upload file CSV untuk import data jenis barang secara massal</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('jenis-barang.download-template') }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Template
                    </a>
                    <a href="{{ route('jenis-barang.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200 shadow-sm">
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

        <!-- Instructions Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Instruksi Import</h3>
            <div class="space-y-3 text-sm text-gray-600">
                <p><strong>1. Download Template:</strong> Gunakan tombol "Download Template" untuk mendapatkan format CSV yang benar.</p>
                <p><strong>2. Format File:</strong> File CSV harus menggunakan delimiter titik koma (;) dan encoding UTF-8.</p>
                <p><strong>3. Field Wajib:</strong> Nama barang wajib diisi. Kode akan auto-generate dengan format JB00001 jika kosong.</p>
                <p><strong>4. Status Default:</strong> Jika kolom status kosong, akan otomatis diset ke 'active'.</p>
                <p><strong>5. Auto-Generate Kode:</strong> Kosongkan kolom kode untuk auto-generate format JB00001, JB00002, dst.</p>
                <p><strong>6. Duplikasi:</strong> Data dengan kode yang sama akan dilewati.</p>
                <p><strong>7. Ukuran File:</strong> Maksimal 2MB per file.</p>
            </div>
        </div>

        <!-- Upload Form Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Upload File CSV</h3>

            <form action="{{ route('jenis-barang.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf

                <div class="space-y-4">
                    <!-- File Upload -->
                    <div>
                        <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih File CSV <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center justify-center w-full">
                            <label for="csv_file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6" id="uploadArea">
                                    <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500">
                                        <span class="font-semibold">Klik untuk upload</span> atau drag dan drop
                                    </p>
                                    <p class="text-xs text-gray-500">CSV atau TXT (Maks. 2MB)</p>
                                </div>
                                <div class="hidden" id="fileInfo">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700" id="fileName"></span>
                                        <span class="text-xs text-gray-500" id="fileSize"></span>
                                    </div>
                                </div>
                                <input id="csv_file" name="csv_file" type="file" class="hidden" accept=".csv,.txt" required>
                            </label>
                        </div>
                        @error('csv_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" id="submitBtn" disabled class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Import Data
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Example Format Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Contoh Format Data CSV</h3>

            <h4 class="text-sm font-medium text-gray-900 mb-2">Contoh Format Data CSV:</h4>
            <div class="bg-gray-100 rounded-lg p-4 font-mono text-sm overflow-x-auto">
                <div class="text-gray-700">
                    kode;nama_barang;catatan;status<br>
                    JB00001;Elektronik;Barang elektronik;active<br>
                    ;Furniture;Barang furniture;inactive<br>
                    ;Tekstil;;
                </div>
            </div>

            <div class="mt-4 space-y-2 text-sm text-gray-600">
                <p><strong>Keterangan:</strong></p>
                <ul class="list-disc list-inside space-y-1 ml-4">
                    <li><strong>kode:</strong> Opsional, akan auto-generate dengan format JB00001 jika kosong</li>
                    <li><strong>nama_barang:</strong> Wajib diisi</li>
                    <li><strong>catatan:</strong> Opsional</li>
                    <li><strong>status:</strong> Opsional (default: active), nilai: active/inactive</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('csv_file');
    const uploadArea = document.getElementById('uploadArea');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitBtn = document.getElementById('submitBtn');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file type
            const allowedTypes = ['text/csv', 'application/csv', 'text/plain'];
            const fileExtension = file.name.split('.').pop().toLowerCase();

            if (!allowedTypes.includes(file.type) && !['csv', 'txt'].includes(fileExtension)) {
                alert('Format file tidak didukung. Hanya file CSV dan TXT yang diperbolehkan.');
                fileInput.value = '';
                return;
            }

            // Validate file size (2MB = 2 * 1024 * 1024 bytes)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                fileInput.value = '';
                return;
            }

            // Show file info
            uploadArea.classList.add('hidden');
            fileInfo.classList.remove('hidden');
            fileName.textContent = file.name;
            fileSize.textContent = `(${formatFileSize(file.size)})`;
            submitBtn.disabled = false;
        } else {
            // Hide file info
            uploadArea.classList.remove('hidden');
            fileInfo.classList.add('hidden');
            submitBtn.disabled = true;
        }
    });

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
@endsection
