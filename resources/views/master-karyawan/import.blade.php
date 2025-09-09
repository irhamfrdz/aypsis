@extends('layouts.app')

@section('title','Import Karyawan')
@section('page_title', 'Import Karyawan')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <!-- Header Section -->
        <div class="px-6 py-4 border-b bg-white">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h2 class="text-xl font-semibold text-gray-900">Import Data Karyawan dari CSV/Excel</h2>
                <div class="flex gap-2">
                    <a href="{{ route('master.karyawan.template') }}" class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded transition duration-150">
                        <i class="fas fa-download mr-2"></i>Template CSV
                    </a>
                    <a href="{{ route('master.karyawan.excel-template') }}" class="inline-flex items-center px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded transition duration-150">
                        <i class="fas fa-file-excel mr-2"></i>Template Excel
                    </a>
                    <a href="{{ route('master.karyawan.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded transition duration-150">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{!! nl2br(e(session('success'))) !!}</span>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="mx-6 mt-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>{!! nl2br(e(session('warning'))) !!}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-times-circle mr-2"></i>
                    <span>{!! nl2br(e(session('error'))) !!}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded" role="alert">
                <div class="flex items-start">
                    <i class="fas fa-times-circle mr-2 mt-1"></i>
                    <div>
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Upload Form -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Upload File CSV/Excel</h3>

                    <form action="{{ route('master.karyawan.import.store') }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf
                        <div class="mb-4">
                            <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">Pilih File CSV/Excel</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors duration-200" id="dropZone">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="csv_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload file</span>
                                            <input id="csv_file" name="csv_file" type="file" accept=".csv,text/csv,.txt,.xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" class="sr-only" required>
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">CSV, TXT, XLSX, XLS hingga 10MB</p>
                                </div>
                            </div>
                            <div id="fileInfo" class="mt-2 hidden">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-file-csv mr-2 text-green-500"></i>
                                    <span id="fileName"></span>
                                    <span id="fileSize" class="ml-2 text-gray-400"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="window.location.href='{{ route('master.karyawan.index') }}'" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded transition duration-150">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition duration-150" id="submitBtn">
                                <i class="fas fa-upload mr-2"></i>Import Data
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Instructions -->
                <div class="space-y-6">
                    <div class="bg-blue-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-blue-900 mb-4">
                            <i class="fas fa-info-circle mr-2"></i>Panduan Import
                        </h3>
                        <div class="text-sm text-blue-800 space-y-3">
                            <div class="flex items-start">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-200 text-blue-800 rounded-full text-xs font-semibold mr-3 mt-0.5">1</span>
                                <div>
                                    <p class="font-medium">Download Template</p>
                                    <p class="text-blue-700">Pilih "Template CSV" untuk format standar atau "Template Excel" untuk Excel (.csv)</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-200 text-blue-800 rounded-full text-xs font-semibold mr-3 mt-0.5">2</span>
                                <div>
                                    <p class="font-medium">Isi Data</p>
                                    <p class="text-blue-700">Isi template dengan data karyawan. NIK harus unik dan tidak boleh kosong</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-200 text-blue-800 rounded-full text-xs font-semibold mr-3 mt-0.5">3</span>
                                <div>
                                    <p class="font-medium">Upload File</p>
                                    <p class="text-blue-700">Upload file CSV atau Excel (.xlsx/.xls) yang sudah diisi untuk import data</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-yellow-900 mb-4">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Catatan Penting
                        </h3>
                        <div class="text-sm text-yellow-800 space-y-2">
                            <p>• File harus berformat CSV dengan delimiter koma (,) atau semicolon (;)</p>
                            <p>• Baris pertama harus berisi nama kolom (header)</p>
                            <p>• NIK harus unik untuk setiap karyawan</p>
                            <p>• Format tanggal: DD/MM/YYYY atau YYYY-MM-DD</p>
                            <p>• Data yang sudah ada akan diupdate berdasarkan NIK</p>
                            <p>• Kolom kosong akan diisi dengan nilai default atau NULL</p>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-green-900 mb-4">
                            <i class="fas fa-list mr-2"></i>Kolom yang Didukung
                        </h3>
                        <div class="text-sm text-green-800">
                            <div class="grid grid-cols-2 gap-2">
                                <div>• nik</div>
                                <div>• nama_lengkap</div>
                                <div>• nama_panggilan</div>
                                <div>• email</div>
                                <div>• no_hp</div>
                                <div>• divisi</div>
                                <div>• pekerjaan</div>
                                <div>• jkn</div>
                                <div>• no_ketenagakerjaan</div>
                                <div>• status_pajak</div>
                                <div>• tanggal_masuk</div>
                                <div>• dan lainnya...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('csv_file');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('importForm');

    // Drag and drop functionality
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('border-blue-400', 'bg-blue-50'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('border-blue-400', 'bg-blue-50'), false);
    });

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            fileInput.files = files;
            displayFileInfo(files[0]);
        }
    }

    // File input change
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            displayFileInfo(this.files[0]);
        } else {
            hideFileInfo();
        }
    });

    function displayFileInfo(file) {
        fileName.textContent = file.name;
        fileSize.textContent = `(${formatFileSize(file.size)})`;
        fileInfo.classList.remove('hidden');
    }

    function hideFileInfo() {
        fileInfo.classList.add('hidden');
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Form submission
    form.addEventListener('submit', function() {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengupload...';
        submitBtn.disabled = true;
    });
});
</script>
@endsection
