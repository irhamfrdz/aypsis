@extends('layouts.app')

@section('title', 'Import Saldo Awal Utang Supir - AYPSIS')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <!-- Back Button & Title -->
    <div class="flex items-center space-x-4 mb-8">
        <a href="{{ route('saldo-utang-supir.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-gray-900 shadow-sm transition-all duration-200">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Import Saldo Awal</h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Unggah file CSV saldo awal armada supir</p>
        </div>
    </div>

    <!-- Alert Error -->
    @if(session('error'))
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl flex items-center">
            <div class="w-8 h-8 bg-rose-100 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <span class="font-bold text-sm">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Information Card -->
    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6 mb-6">
        <div class="flex items-start">
            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3 text-indigo-600 flex-shrink-0">
                <i class="fas fa-info-circle text-xs"></i>
            </div>
            <div>
                <h3 class="text-xs font-black text-indigo-900 uppercase tracking-wider mb-2">Petunjuk Format CSV</h3>
                <p class="text-xs text-indigo-700 leading-relaxed mb-3">
                    File CSV harus dipisahkan menggunakan tanda titik koma (<strong>semicolon ;</strong>) dengan struktur kolom sebagai berikut:
                </p>
                <div class="bg-indigo-900/5 rounded-lg p-3 font-mono text-[10px] text-indigo-900 leading-normal">
                    Nik.;Nama Pelanggan;Saldo (Asing)<br>
                    0001;ACE, BP;650.000,00<br>
                    0012;ABDULLAH /DULOH, BP;2.800.000,00
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Card -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden p-8">
        <form action="{{ route('saldo-utang-supir.import-process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-6">
                <!-- File Input -->
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Pilih File CSV</label>
                    <div class="border-2 border-dashed border-gray-200 hover:border-indigo-400 transition-colors rounded-2xl p-8 flex flex-col items-center justify-center cursor-pointer relative bg-gray-50" id="drop-area">
                        <input type="file" 
                               name="csv_file" 
                               id="csv_file" 
                               accept=".csv,.txt"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               required>
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center border border-gray-100 shadow-sm mb-3 text-gray-400" id="upload-icon">
                            <i class="fas fa-cloud-upload-alt text-lg"></i>
                        </div>
                        <span class="text-xs font-black text-gray-700 uppercase tracking-wider" id="file-label">Pilih file atau seret kemari</span>
                        <span class="text-[9px] text-gray-400 mt-1">Hanya mendukung format .csv / .txt</span>
                    </div>
                    @error('csv_file')
                        <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex justify-end space-x-2">
                <a href="{{ route('saldo-utang-supir.index') }}" class="px-5 py-3 border border-gray-200 text-gray-700 font-bold rounded-xl text-xs uppercase tracking-wider hover:bg-gray-50 transition-colors duration-200">
                    Batal
                </a>
                <button type="submit" class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-100 transition-all duration-200 text-xs uppercase tracking-wider">
                    Mulai Impor
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const fileInput = document.getElementById('csv_file');
    const fileLabel = document.getElementById('file-label');
    const uploadIcon = document.getElementById('upload-icon');
    const dropArea = document.getElementById('drop-area');

    fileInput.addEventListener('change', function(e) {
        if(fileInput.files.length > 0) {
            fileLabel.textContent = fileInput.files[0].name;
            fileLabel.classList.remove('text-gray-700');
            fileLabel.classList.add('text-indigo-600');
            uploadIcon.innerHTML = '<i class="fas fa-file-csv text-indigo-600 text-lg"></i>';
        }
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropArea.classList.add('border-indigo-400');
        dropArea.classList.add('bg-indigo-50/50');
    }

    function unhighlight(e) {
        dropArea.classList.remove('border-indigo-400');
        dropArea.classList.remove('bg-indigo-50/50');
    }
</script>
@endsection
