@extends('layouts.app')

@section('title', 'Tambah Tagihan OB')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-blue-600 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <h5 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Tagihan OB (On Board)
                </h5>
                <a href="{{ route('tagihan-ob.index') }}{{ isset($prefilledKapal, $prefilledVoyage) ? '?kapal=' . urlencode($prefilledKapal) . '&voyage=' . urlencode($prefilledVoyage) : '' }}" 
                   class="bg-white text-blue-600 hover:bg-gray-50 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Kembali
                </a>
            </div>
            @if(isset($prefilledKapal, $prefilledVoyage))
                <div class="mt-2 text-blue-100 text-sm">
                    <span class="bg-blue-500 px-2 py-1 rounded text-xs mr-2">
                        <i class="fas fa-ship mr-1"></i>{{ $prefilledKapal }}
                    </span>
                    <span class="bg-blue-500 px-2 py-1 rounded text-xs">
                        <i class="fas fa-route mr-1"></i>{{ $prefilledVoyage }}
                    </span>
                </div>
            @endif
        </div>

        <div class="p-6">
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4" role="alert">
                    <div class="flex justify-between items-center">
                        <span>{{ session('error') }}</span>
                        <button type="button" class="text-red-500 hover:text-red-700" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('tagihan-ob.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <label for="kapal" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Kapal <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="block w-full px-3 py-2 border @error('kapal') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   id="kapal" 
                                   name="kapal" 
                                   value="{{ old('kapal', $prefilledKapal ?? '') }}" 
                                   {{ isset($prefilledKapal) ? 'readonly' : '' }}
                                   required>
                            @error('kapal')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="voyage" class="block text-sm font-medium text-gray-700 mb-1">
                                Voyage <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="block w-full px-3 py-2 border @error('voyage') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   id="voyage" 
                                   name="voyage" 
                                   value="{{ old('voyage', $prefilledVoyage ?? '') }}" 
                                   {{ isset($prefilledVoyage) ? 'readonly' : '' }}
                                   required>
                            @error('voyage')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nomor_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Kontainer <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="block w-full px-3 py-2 border @error('nomor_kontainer') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   id="nomor_kontainer" 
                                   name="nomor_kontainer" 
                                   value="{{ old('nomor_kontainer') }}" 
                                   placeholder="Contoh: GESU1234567"
                                   required>
                            @error('nomor_kontainer')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nama_supir" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Supir <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="block w-full px-3 py-2 border @error('nama_supir') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   id="nama_supir" 
                                   name="nama_supir" 
                                   value="{{ old('nama_supir') }}" 
                                   required>
                            @error('nama_supir')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <label for="barang" class="block text-sm font-medium text-gray-700 mb-1">
                                Jenis Barang <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="block w-full px-3 py-2 border @error('barang') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   id="barang" 
                                   name="barang" 
                                   value="{{ old('barang') }}" 
                                   required>
                            @error('barang')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                Status Kontainer <span class="text-red-500">*</span>
                            </label>
                            <select class="block w-full px-3 py-2 border @error('status_kontainer') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    id="status_kontainer" 
                                    name="status_kontainer" 
                                    required>
                                <option value="">Pilih Status</option>
                                <option value="full" {{ old('status_kontainer') === 'full' ? 'selected' : '' }}>
                                    Full (Tarik Isi)
                                </option>
                                <option value="empty" {{ old('status_kontainer') === 'empty' ? 'selected' : '' }}>
                                    Empty (Tarik Kosong)
                                </option>
                            </select>
                            @error('status_kontainer')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-600 text-sm mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Full = Tarik Isi, Empty = Tarik Kosong
                            </p>
                        </div>

                        <div>
                            <label for="bl_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Bill of Lading (BL)
                            </label>
                            <select class="block w-full px-3 py-2 border @error('bl_id') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    id="bl_id" 
                                    name="bl_id">
                                <option value="">Pilih BL (Opsional)</option>
                                @foreach($bls as $bl)
                                    <option value="{{ $bl->id }}" {{ old('bl_id') == $bl->id ? 'selected' : '' }}>
                                        {{ $bl->nomor_bl }} - {{ $bl->kapal }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bl_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">
                                Keterangan
                            </label>
                            <textarea class="block w-full px-3 py-2 border @error('keterangan') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="3" 
                                      placeholder="Keterangan tambahan...">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Info Biaya -->
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex items-center">
                        <i class="fas fa-calculator text-blue-600 text-2xl mr-3"></i>
                        <div>
                            <h6 class="text-blue-900 font-medium mb-1">Informasi Biaya</h6>
                            <p class="text-blue-700 text-sm">Biaya akan dihitung otomatis berdasarkan Master Pricelist OB sesuai dengan status kontainer yang dipilih.</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('tagihan-ob.index') }}{{ isset($prefilledKapal, $prefilledVoyage) ? '?kapal=' . urlencode($prefilledKapal) . '&voyage=' . urlencode($prefilledVoyage) : '' }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md font-medium transition duration-150 ease-in-out">
                        <i class="fas fa-times mr-1"></i>
                        Batal
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition duration-150 ease-in-out">
                        <i class="fas fa-save mr-1"></i>
                        Simpan Tagihan OB
                    </button>
                </div>
            </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-fill data when BL is selected
document.getElementById('bl_id').addEventListener('change', function() {
    const blId = this.value;
    if (blId) {
        // You can add AJAX call here to fetch BL data and auto-fill form fields
        // For now, this is just a placeholder for future enhancement
        console.log('Selected BL ID:', blId);
    }
});

// Form validation enhancement
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = ['kapal', 'voyage', 'nomor_kontainer', 'nama_supir', 'barang', 'status_kontainer'];
    let isValid = true;
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Mohon lengkapi semua field yang wajib diisi.');
    }
});
</script>
@endpush