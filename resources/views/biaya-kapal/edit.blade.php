@extends('layouts.app')

@section('title', 'Edit Biaya Kapal')
@section('page_title', 'Edit Biaya Kapal')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Biaya Kapal</h2>
        <a href="{{ route('biaya-kapal.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-md transition duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <strong>Terdapat kesalahan:</strong>
            </div>
            <ul class="list-disc list-inside ml-7">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('biaya-kapal.update', $biayaKapal->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Tanggal -->
            <div>
                <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       name="tanggal" 
                       id="tanggal" 
                       value="{{ old('tanggal', $biayaKapal->tanggal->format('Y-m-d')) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal') border-red-500 @enderror"
                       required>
                @error('tanggal')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Kapal -->
            <div>
                <label for="nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Kapal <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="nama_kapal" 
                       id="nama_kapal" 
                       value="{{ old('nama_kapal', $biayaKapal->nama_kapal) }}"
                       placeholder="Masukkan nama kapal"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_kapal') border-red-500 @enderror"
                       required>
                @error('nama_kapal')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jenis Biaya -->
            <div>
                <label for="jenis_biaya" class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Biaya <span class="text-red-500">*</span>
                </label>
                <select name="jenis_biaya" 
                        id="jenis_biaya" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jenis_biaya') border-red-500 @enderror"
                        required>
                    <option value="">-- Pilih Jenis Biaya --</option>
                    @foreach($klasifikasiBiayas as $k)
                        <option value="{{ $k->kode }}" {{ old('jenis_biaya', $biayaKapal->jenis_biaya) == $k->kode ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
                @error('jenis_biaya')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nominal -->
            <div>
                <label for="nominal" class="block text-sm font-medium text-gray-700 mb-2">
                    Nominal <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500 font-medium">Rp</span>
                    <input type="text" 
                           name="nominal" 
                           id="nominal" 
                           value="{{ old('nominal', number_format($biayaKapal->nominal, 0, ',', '.')) }}"
                           placeholder="0"
                           class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nominal') border-red-500 @enderror"
                           required>
                </div>
                @error('nominal')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Nominal akan diformat otomatis</p>
            </div>

            <!-- Keterangan -->
            <div class="md:col-span-2">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan
                </label>
                <textarea name="keterangan" 
                          id="keterangan" 
                          rows="4"
                          placeholder="Masukkan keterangan tambahan (opsional)"
                          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('keterangan') border-red-500 @enderror">{{ old('keterangan', $biayaKapal->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Upload Bukti -->
            <div class="md:col-span-2">
                <label for="bukti" class="block text-sm font-medium text-gray-700 mb-2">
                    Upload Bukti (PDF/Gambar)
                </label>
                
                @if($biayaKapal->bukti)
                <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-md">
                    <p class="text-sm text-blue-800 mb-2">
                        <strong>File saat ini:</strong> {{ basename($biayaKapal->bukti) }}
                    </p>
                    @if($biayaKapal->bukti_foto)
                        <img src="{{ $biayaKapal->bukti_foto }}" alt="Bukti" class="max-w-xs h-auto rounded">
                    @else
                        <a href="{{ asset('storage/' . $biayaKapal->bukti) }}" target="_blank" class="text-blue-600 hover:underline">Lihat file</a>
                    @endif
                </div>
                @endif

                <input type="file" 
                       name="bukti" 
                       id="bukti" 
                       accept=".pdf,.png,.jpg,.jpeg"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('bukti') border-red-500 @enderror"
                       onchange="updateFileName(this)">
                @error('bukti')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Format: PDF, PNG, JPG, JPEG (Maksimal 2MB). Kosongkan jika tidak ingin mengubah file.</p>
                <div id="file-info" class="mt-2 text-sm text-gray-600"></div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-blue-800">Informasi</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Pastikan semua data yang diisi sudah benar</li>
                            <li>Nominal akan diformat otomatis dengan pemisah ribuan</li>
                            <li>Upload bukti dalam format PDF atau gambar untuk dokumentasi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('biaya-kapal.index') }}" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-md transition duration-150">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition duration-150">
                Perbarui Data
            </button>
        </div>
    </form>
</div>

<script>
    // Auto-format nominal with thousand separators
    document.getElementById('nominal').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value) {
            e.target.value = parseInt(value).toLocaleString('id-ID');
        }
    });

    // Show file info when file is selected
    function updateFileName(input) {
        const fileInfo = document.getElementById('file-info');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const sizeMB = (file.size / 1024 / 1024).toFixed(2);
            fileInfo.innerHTML = `<strong>File dipilih:</strong> ${file.name} (${sizeMB} MB)`;
            
            // Validate file size
            if (file.size > 2 * 1024 * 1024) {
                fileInfo.innerHTML += '<br><span class="text-red-600">⚠️ Ukuran file melebihi 2MB</span>';
            }
        } else {
            fileInfo.innerHTML = '';
        }
    }
</script>
@endsection
