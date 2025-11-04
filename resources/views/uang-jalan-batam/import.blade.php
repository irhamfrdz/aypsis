@extends('layouts.app')

@section('title', 'Import Uang Jalan Batam')
@section('page_title', 'Import Uang Jalan Batam')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Import Data Uang Jalan Batam</h2>
            <p class="mt-1 text-sm text-gray-600">Upload file Excel/CSV untuk mengimport data secara massal</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('uang-jalan-batam.download-template') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <i class="fas fa-download mr-2"></i>
                Download Template
            </a>
            <a href="{{ route('uang-jalan-batam.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="mb-6 rounded-md bg-green-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">
                    {{ session('error') }}
                </p>
            </div>
        </div>
    </div>
@endif

@if (session('import_errors'))
    <div class="mb-6 rounded-md bg-yellow-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    Terdapat Error pada Beberapa Baris:
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Instructions -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-400"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-lg font-medium text-blue-900 mb-3">Petunjuk Import</h3>
            <div class="text-sm text-blue-800 space-y-2">
                <p><strong>1. Download Template:</strong> Klik tombol "Download Template" untuk mendapatkan format yang benar.</p>
                <p><strong>2. Isi Data:</strong> Lengkapi template dengan data yang ingin diimport.</p>
                <p><strong>3. Format yang Didukung:</strong> Excel (.xlsx, .xls) dan CSV (.csv)</p>
                <p><strong>4. Maksimal Ukuran File:</strong> 2MB</p>
            </div>
            
            <div class="mt-4">
                <h4 class="font-medium text-blue-900 mb-2">Kolom yang Wajib Diisi:</h4>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-2 text-sm text-blue-800">
                    <span class="bg-blue-100 px-2 py-1 rounded">Wilayah</span>
                    <span class="bg-blue-100 px-2 py-1 rounded">Rute</span>
                    <span class="bg-blue-100 px-2 py-1 rounded">Expedisi</span>
                    <span class="bg-blue-100 px-2 py-1 rounded">Ring</span>
                    <span class="bg-blue-100 px-2 py-1 rounded">FT</span>
                    <span class="bg-blue-100 px-2 py-1 rounded">F/E</span>
                    <span class="bg-blue-100 px-2 py-1 rounded">Tarif</span>
                    <span class="bg-blue-100 px-2 py-1 rounded">Tanggal Awal</span>
                    <span class="bg-blue-100 px-2 py-1 rounded">Tanggal Akhir</span>
                </div>
            </div>
            
            <div class="mt-4">
                <h4 class="font-medium text-blue-900 mb-2">Format Tanggal yang Diterima:</h4>
                <div class="text-sm text-blue-800">
                    <code class="bg-blue-100 px-2 py-1 rounded mr-2">YYYY-MM-DD</code>
                    <code class="bg-blue-100 px-2 py-1 rounded mr-2">DD/MM/YYYY</code>
                    <code class="bg-blue-100 px-2 py-1 rounded">DD-MM-YYYY</code>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Form -->
<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('uang-jalan-batam.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div>
            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                Pilih File untuk Import <span class="text-red-500">*</span>
            </label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors duration-200">
                <div class="space-y-1 text-center">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                    <div class="flex text-sm text-gray-600">
                        <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                            <span>Upload file</span>
                            <input id="file" name="file" type="file" class="sr-only" accept=".xlsx,.xls,.csv" required>
                        </label>
                        <p class="pl-1">atau drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">
                        Excel atau CSV sampai 2MB
                    </p>
                </div>
            </div>
            @error('file')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="bg-gray-50 rounded-md p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-lightbulb text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-gray-900">Tips untuk Import yang Sukses:</h4>
                    <ul class="mt-2 text-sm text-gray-600 list-disc list-inside space-y-1">
                        <li>Pastikan nama kolom sesuai dengan template</li>
                        <li>Tanggal akhir berlaku harus >= tanggal awal berlaku</li>
                        <li>Tarif harus berupa angka positif</li>
                        <li>Status hanya boleh: "aqua" atau "chasis PB" (atau kosong)</li>
                        <li>FT hanya boleh: "20FT", "40FT", atau "45FT"</li>
                        <li>F/E hanya boleh: "Full" atau "Empty"</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('uang-jalan-batam.index') }}" 
               class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Batal
            </a>
            <button type="submit" 
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-upload mr-2"></i>
                Import Data
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const dropZone = fileInput.closest('.border-dashed');
    
    // File name display
    fileInput.addEventListener('change', function() {
        const fileName = this.files[0]?.name;
        if (fileName) {
            const label = dropZone.querySelector('label span');
            label.textContent = fileName;
            dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
        }
    });
    
    // Drag and drop functionality
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-indigo-500', 'bg-indigo-50');
    });
    
    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-indigo-500', 'bg-indigo-50');
    });
    
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-indigo-500', 'bg-indigo-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });
});
</script>
@endsection