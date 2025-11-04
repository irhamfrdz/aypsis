@extends('layouts.app')

@section('title', 'Import Master Kapal')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Import Master Kapal</h1>
                    <p class="text-gray-600 mt-1">Upload file CSV untuk mengimport data Master Kapal</p>
                </div>
                <a href="{{ route('master-kapal.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>

        <!-- Import Errors Display -->
        @if(session('import_errors') && count(session('import_errors')) > 0)
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-red-800">Error Import ({{ count(session('import_errors')) }} baris)</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p class="mb-2">Beberapa baris gagal diimport karena masalah berikut:</p>
                        <div class="max-h-96 overflow-y-auto">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach(session('import_errors') as $error)
                                <li class="text-xs">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form action="{{ route('master-kapal.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                        File CSV <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition duration-200" id="dropzone">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" id="upload-icon" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <div class="flex text-sm text-gray-600" id="upload-text">
                                <label for="csv_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload file CSV</span>
                                    <input id="csv_file" name="csv_file" type="file" accept=".csv" class="sr-only" required onchange="updateFileName(this)">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500" id="file-size-text">CSV hingga 10MB</p>

                            <!-- File selected info (hidden by default) -->
                            <div id="file-selected" class="hidden mt-3 p-3 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span class="text-sm font-medium text-green-800" id="file-name"></span>
                                </div>
                                <p class="text-xs text-green-600 mt-1" id="file-info"></p>
                                <button type="button" onclick="clearFile()" class="mt-2 text-xs text-red-600 hover:text-red-800 underline">
                                    Hapus file
                                </button>
                            </div>
                        </div>
                    </div>
                    @error('csv_file')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Template CSV</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Download template CSV untuk memastikan format yang benar:</p>
                                <a href="{{ route('master-kapal.download-template') }}" class="inline-flex items-center mt-2 px-3 py-1 border border-blue-300 shadow-sm text-sm leading-4 font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-download mr-2"></i>Download Template
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Petunjuk Import</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p class="mb-3"><strong>Sistem mendukung 2 format CSV:</strong></p>
                                
                                <div class="mb-4">
                                    <h4 class="font-semibold text-yellow-800 mb-2">📋 Format 1: Template Import (Recommended)</h4>
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Gunakan delimiter titik koma (;)</li>
                                        <li>Header: <code class="bg-yellow-100 px-1 rounded text-xs">kode;kode_kapal;nama_kapal;nickname;pelayaran;catatan;status</code></li>
                                        <li>Download template untuk format yang tepat</li>
                                    </ul>
                                </div>

                                <div class="mb-4">
                                    <h4 class="font-semibold text-yellow-800 mb-2">📤 Format 2: File Export CSV</h4>
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Bisa menggunakan file hasil export dari sistem ini</li>
                                        <li>Sistem akan otomatis mendeteksi format export</li>
                                        <li>Delimiter koma (,) dengan header lengkap</li>
                                    </ul>
                                </div>

                                <div class="border-t border-yellow-300 pt-3 mt-3">
                                    <h4 class="font-semibold text-yellow-800 mb-2">📌 Aturan Umum:</h4>
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Kolom <strong>kode</strong> dan <strong>nama_kapal</strong> wajib diisi</li>
                                        <li>Kolom status harus berisi: <strong>aktif/active</strong> atau <strong>nonaktif/inactive</strong></li>
                                        <li>Data yang sudah ada dengan kode sama akan diupdate</li>
                                        <li>Baris dengan kode kosong akan diabaikan</li>
                                        <li>Kolom kode_kapal, nickname, pelayaran, dan catatan bersifat opsional</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-upload mr-2"></i>Import Data
                    </button>
                </div>
            </form>
        </div>

        @if (session('error'))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Error Import</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if (session('success'))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
            <div class="bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Import Berhasil</h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p>{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if (session('import_errors'))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
            <div class="bg-orange-50 border border-orange-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-orange-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-orange-800">Peringatan</h3>
                        <div class="mt-2 text-sm text-orange-700">
                            <p class="mb-2">Beberapa baris tidak dapat diimport:</p>
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function updateFileName(input) {
        const dropzone = document.getElementById('dropzone');
        const uploadIcon = document.getElementById('upload-icon');
        const uploadText = document.getElementById('upload-text');
        const fileSizeText = document.getElementById('file-size-text');
        const fileSelected = document.getElementById('file-selected');
        const fileName = document.getElementById('file-name');
        const fileInfo = document.getElementById('file-info');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const fileSize = (file.size / 1024).toFixed(2); // Convert to KB

            // Update display
            fileName.textContent = file.name;
            fileInfo.textContent = `Ukuran: ${fileSize} KB`;

            // Hide upload UI, show selected file info
            uploadIcon.classList.add('hidden');
            uploadText.classList.add('hidden');
            fileSizeText.classList.add('hidden');
            fileSelected.classList.remove('hidden');

            // Change dropzone border color to green
            dropzone.classList.remove('border-gray-300');
            dropzone.classList.add('border-green-400', 'bg-green-50');
        }
    }

    function clearFile() {
        const fileInput = document.getElementById('csv_file');
        const dropzone = document.getElementById('dropzone');
        const uploadIcon = document.getElementById('upload-icon');
        const uploadText = document.getElementById('upload-text');
        const fileSizeText = document.getElementById('file-size-text');
        const fileSelected = document.getElementById('file-selected');

        // Clear file input
        fileInput.value = '';

        // Reset display
        uploadIcon.classList.remove('hidden');
        uploadText.classList.remove('hidden');
        fileSizeText.classList.remove('hidden');
        fileSelected.classList.add('hidden');

        // Reset dropzone border color
        dropzone.classList.remove('border-green-400', 'bg-green-50');
        dropzone.classList.add('border-gray-300');
    }

    // Drag and drop functionality
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('csv_file');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropzone.classList.add('border-blue-500', 'bg-blue-50');
    }

    function unhighlight(e) {
        dropzone.classList.remove('border-blue-500', 'bg-blue-50');
    }

    dropzone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            fileInput.files = files;
            updateFileName(fileInput);
        }
    }
</script>
@endpush
@endsection
