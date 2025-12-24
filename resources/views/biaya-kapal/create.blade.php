@extends('layouts.app')

@section('title', 'Tambah Biaya Kapal')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Biaya Kapal</h1>
                <p class="text-gray-600 mt-1">Tambah data biaya operasional kapal baru</p>
            </div>
            <div>
                <a href="{{ route('biaya-kapal.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg shadow-sm transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Terdapat beberapa kesalahan:</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Informasi Biaya Kapal</h2>
            <p class="text-sm text-gray-600 mt-1">Lengkapi formulir di bawah ini dengan data yang akurat</p>
        </div>

        <form action="{{ route('biaya-kapal.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tanggal -->
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="tanggal" 
                           name="tanggal" 
                           value="{{ old('tanggal', date('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal') border-red-500 @enderror"
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
                    <select id="nama_kapal" 
                            name="nama_kapal" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama_kapal') border-red-500 @enderror"
                            required>
                        <option value="">-- Pilih Kapal --</option>
                        @foreach($kapals as $kapal)
                            <option value="{{ $kapal->nama_kapal }}" {{ old('nama_kapal') == $kapal->nama_kapal ? 'selected' : '' }}>
                                {{ $kapal->nama_kapal }}
                            </option>
                        @endforeach
                    </select>
                    @error('nama_kapal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Biaya -->
                <div>
                    <label for="jenis_biaya" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Biaya <span class="text-red-500">*</span>
                    </label>
                    <select id="jenis_biaya" 
                            name="jenis_biaya" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jenis_biaya') border-red-500 @enderror"
                            required>
                        <option value="">-- Pilih Jenis Biaya --</option>
                        <option value="bahan_bakar" {{ old('jenis_biaya') == 'bahan_bakar' ? 'selected' : '' }}>Bahan Bakar</option>
                        <option value="pelabuhan" {{ old('jenis_biaya') == 'pelabuhan' ? 'selected' : '' }}>Pelabuhan</option>
                        <option value="perbaikan" {{ old('jenis_biaya') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                        <option value="awak_kapal" {{ old('jenis_biaya') == 'awak_kapal' ? 'selected' : '' }}>Awak Kapal</option>
                        <option value="asuransi" {{ old('jenis_biaya') == 'asuransi' ? 'selected' : '' }}>Asuransi</option>
                        <option value="lainnya" {{ old('jenis_biaya') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="nominal" 
                               name="nominal" 
                               value="{{ old('nominal') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nominal') border-red-500 @enderror"
                               placeholder="0"
                               required>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Masukkan nominal tanpa titik atau koma</p>
                    @error('nominal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan
                    </label>
                    <textarea id="keterangan" 
                              name="keterangan" 
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('keterangan') border-red-500 @enderror"
                              placeholder="Masukkan keterangan atau catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Upload Bukti -->
                <div class="md:col-span-2">
                    <label for="bukti" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Bukti
                    </label>
                    <div class="flex items-center justify-center w-full">
                        <label for="bukti" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition duration-200">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                                <p class="mb-2 text-sm text-gray-500">
                                    <span class="font-semibold">Klik untuk upload</span> atau drag and drop
                                </p>
                                <p class="text-xs text-gray-500">PDF, PNG, JPG atau JPEG (Max. 2MB)</p>
                            </div>
                            <input id="bukti" 
                                   name="bukti" 
                                   type="file" 
                                   class="hidden" 
                                   accept=".pdf,.png,.jpg,.jpeg"
                                   onchange="updateFileName(this)">
                        </label>
                    </div>
                    <p id="file-name" class="mt-2 text-sm text-gray-600"></p>
                    @error('bukti')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-semibold text-blue-800">Informasi:</h4>
                        <ul class="mt-2 text-xs text-blue-700 list-disc list-inside space-y-1">
                            <li>Field yang bertanda <span class="text-red-500">*</span> wajib diisi</li>
                            <li>Nominal akan otomatis diformat dengan pemisah ribuan</li>
                            <li>Upload bukti bersifat opsional namun direkomendasikan untuk dokumentasi</li>
                            <li>Pastikan data yang diinput sudah benar sebelum menyimpan</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('biaya-kapal.index') }}" 
                   class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Format nominal input with thousand separator
    const nominalInput = document.getElementById('nominal');
    
    nominalInput.addEventListener('input', function(e) {
        // Remove all non-numeric characters
        let value = this.value.replace(/\D/g, '');
        
        // Format with thousand separator
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        
        this.value = value;
    });

    // Before form submit, remove formatting from nominal
    document.querySelector('form').addEventListener('submit', function(e) {
        const nominalInput = document.getElementById('nominal');
        nominalInput.value = nominalInput.value.replace(/\./g, '');
    });

    // Update file name display
    function updateFileName(input) {
        const fileNameDisplay = document.getElementById('file-name');
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2); // Convert to MB
            fileNameDisplay.innerHTML = `<i class="fas fa-file-alt mr-2 text-blue-600"></i><span class="font-medium">File terpilih:</span> ${fileName} (${fileSize} MB)`;
        } else {
            fileNameDisplay.innerHTML = '';
        }
    }

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.bg-red-50');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endpush
@endsection
