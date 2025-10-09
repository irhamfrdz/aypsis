@extends('layouts.app')

@section('title', 'Tambah Pricelist Uang Jalan')
@section('page_title', 'Tambah Pricelist Uang Jalan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-bold text-gray-900">Tambah Pricelist Uang Jalan</h1>
                    <a href="{{ route('master-pricelist-uang-jalan.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('master-pricelist-uang-jalan.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Basic Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="cabang" class="block text-sm font-medium text-gray-700 mb-1">Cabang *</label>
                            <input type="text" name="cabang" id="cabang" value="{{ old('cabang', 'JKT') }}" required
                                   list="cabangList"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('cabang') border-red-500 @enderror">
                            <datalist id="cabangList">
                                @foreach($cabangList as $cabang)
                                    <option value="{{ $cabang }}">
                                @endforeach
                                <option value="JKT">
                                <option value="SBY">
                                <option value="BDG">
                            </datalist>
                            @error('cabang')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="wilayah" class="block text-sm font-medium text-gray-700 mb-1">Wilayah *</label>
                            <input type="text" name="wilayah" id="wilayah" value="{{ old('wilayah') }}" required
                                   placeholder="e.g., JAKARTA UTARA"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('wilayah') border-red-500 @enderror">
                            @error('wilayah')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Route Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Rute</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="dari" class="block text-sm font-medium text-gray-700 mb-1">Dari *</label>
                            <input type="text" name="dari" id="dari" value="{{ old('dari', 'GARASI PLUIT') }}" required
                                   placeholder="Lokasi asal"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('dari') border-red-500 @enderror">
                            @error('dari')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="ke" class="block text-sm font-medium text-gray-700 mb-1">Ke *</label>
                            <input type="text" name="ke" id="ke" value="{{ old('ke') }}" required
                                   placeholder="Lokasi tujuan"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('ke') border-red-500 @enderror">
                            @error('ke')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="jarak_km" class="block text-sm font-medium text-gray-700 mb-1">Jarak (KM)</label>
                            <input type="number" name="jarak_km" id="jarak_km" value="{{ old('jarak_km') }}" step="0.1" min="0"
                                   placeholder="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('jarak_km') border-red-500 @enderror">
                            @error('jarak_km')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="liter" class="block text-sm font-medium text-gray-700 mb-1">Estimasi Liter BBM</label>
                            <input type="number" name="liter" id="liter" value="{{ old('liter') }}" min="0"
                                   placeholder="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('liter') border-red-500 @enderror">
                            @error('liter')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Pricing Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Tarif</h3>
                    
                    <!-- Uang Jalan -->
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-800 mb-3">Uang Jalan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="uang_jalan_20ft" class="block text-sm font-medium text-gray-700 mb-1">20 Feet *</label>
                                <input type="number" name="uang_jalan_20ft" id="uang_jalan_20ft" value="{{ old('uang_jalan_20ft') }}" required min="0" step="1000"
                                       placeholder="350000"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('uang_jalan_20ft') border-red-500 @enderror">
                                @error('uang_jalan_20ft')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="uang_jalan_40ft" class="block text-sm font-medium text-gray-700 mb-1">40 Feet</label>
                                <input type="number" name="uang_jalan_40ft" id="uang_jalan_40ft" value="{{ old('uang_jalan_40ft') }}" min="0" step="1000"
                                       placeholder="500000"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('uang_jalan_40ft') border-red-500 @enderror">
                                @error('uang_jalan_40ft')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Mel (Handling) -->
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-800 mb-3">Mel (Handling)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="mel_20ft" class="block text-sm font-medium text-gray-700 mb-1">20 Feet</label>
                                <input type="number" name="mel_20ft" id="mel_20ft" value="{{ old('mel_20ft') }}" min="0" step="1000"
                                       placeholder="30000"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('mel_20ft') border-red-500 @enderror">
                                @error('mel_20ft')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="mel_40ft" class="block text-sm font-medium text-gray-700 mb-1">40 Feet</label>
                                <input type="number" name="mel_40ft" id="mel_40ft" value="{{ old('mel_40ft') }}" min="0" step="1000"
                                       placeholder="50000"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('mel_40ft') border-red-500 @enderror">
                                @error('mel_40ft')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Ongkos & Antar Lokasi -->
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-800 mb-3">Ongkos & Antar Lokasi</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="ongkos_truk_20ft" class="block text-sm font-medium text-gray-700 mb-1">Ongkos Truk 20ft</label>
                                <input type="number" name="ongkos_truk_20ft" id="ongkos_truk_20ft" value="{{ old('ongkos_truk_20ft') }}" min="0" step="1000"
                                       placeholder="1050000"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('ongkos_truk_20ft') border-red-500 @enderror">
                                @error('ongkos_truk_20ft')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="antar_lokasi_20ft" class="block text-sm font-medium text-gray-700 mb-1">Antar Lokasi 20ft</label>
                                <input type="number" name="antar_lokasi_20ft" id="antar_lokasi_20ft" value="{{ old('antar_lokasi_20ft') }}" min="0" step="1000"
                                       placeholder="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('antar_lokasi_20ft') border-red-500 @enderror">
                                @error('antar_lokasi_20ft')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="antar_lokasi_40ft" class="block text-sm font-medium text-gray-700 mb-1">Antar Lokasi 40ft</label>
                                <input type="number" name="antar_lokasi_40ft" id="antar_lokasi_40ft" value="{{ old('antar_lokasi_40ft') }}" min="0" step="1000"
                                       placeholder="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('antar_lokasi_40ft') border-red-500 @enderror">
                                @error('antar_lokasi_40ft')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Tambahan</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="3" 
                                      placeholder="Informasi khusus untuk rute ini..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="berlaku_dari" class="block text-sm font-medium text-gray-700 mb-1">Berlaku Dari *</label>
                                <input type="date" name="berlaku_dari" id="berlaku_dari" value="{{ old('berlaku_dari', now()->format('Y-m-d')) }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('berlaku_dari') border-red-500 @enderror">
                                @error('berlaku_dari')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="berlaku_sampai" class="block text-sm font-medium text-gray-700 mb-1">Berlaku Sampai</label>
                                <input type="date" name="berlaku_sampai" id="berlaku_sampai" value="{{ old('berlaku_sampai') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('berlaku_sampai') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ada batas waktu</p>
                                @error('berlaku_sampai')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('master-pricelist-uang-jalan.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Batal
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Pricelist
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto calculate 40ft price based on 20ft (biasanya 1.4x - 1.5x)
    document.getElementById('uang_jalan_20ft').addEventListener('input', function() {
        const price20ft = parseFloat(this.value) || 0;
        const price40ft = Math.round(price20ft * 1.43); // Average multiplier from CSV data
        
        const price40ftField = document.getElementById('uang_jalan_40ft');
        if (!price40ftField.value) {
            price40ftField.value = price40ft;
        }
    });

    // Auto calculate mel 40ft based on mel 20ft (biasanya 1.6x)
    document.getElementById('mel_20ft').addEventListener('input', function() {
        const mel20ft = parseFloat(this.value) || 0;
        const mel40ft = Math.round(mel20ft * 1.67); // Average multiplier
        
        const mel40ftField = document.getElementById('mel_40ft');
        if (!mel40ftField.value) {
            mel40ftField.value = mel40ft;
        }
    });

    // Validation: berlaku_sampai harus >= berlaku_dari
    document.getElementById('berlaku_sampai').addEventListener('change', function() {
        const berlakuDari = document.getElementById('berlaku_dari').value;
        const berlakuSampai = this.value;
        
        if (berlakuSampai && berlakuDari && berlakuSampai < berlakuDari) {
            alert('Tanggal berlaku sampai tidak boleh lebih kecil dari tanggal berlaku dari');
            this.value = '';
        }
    });
</script>
@endsection