@extends('layouts.app')

@section('title','Tambah Data Transportasi')
@section('page_title', 'Tambah Data Transportasi')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Form Tambah Data Transportasi</h2>
        <p class="text-gray-600 text-sm">Silakan isi form di bawah ini untuk menambahkan data transportasi baru</p>
    </div>

    <form action="{{route('master.tujuan-kegiatan-utama.store')}}" method="POST">
        @csrf

        <!-- Informasi Umum -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">
                <i class="fas fa-info-circle mr-2 text-blue-500"></i>Informasi Umum
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Kode -->
                <div>
                    <label for="kode" class="block text-sm font-medium text-gray-700 mb-2">Kode Rute</label>
                    <input type="text" name="kode" id="kode" value="{{ old('kode') }}" 
                           class="w-full bg-gray-50 border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                           placeholder="Contoh: JKT-SBY-001">
                    @error('kode')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cabang -->
                <div>
                    <label for="cabang" class="block text-sm font-medium text-gray-700 mb-2">Cabang</label>
                    <input type="text" name="cabang" id="cabang" value="{{ old('cabang') }}" 
                           class="w-full bg-gray-50 border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                           placeholder="Contoh: Jakarta Pusat">
                    @error('cabang')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Wilayah -->
                <div>
                    <label for="wilayah" class="block text-sm font-medium text-gray-700 mb-2">Wilayah</label>
                    <input type="text" name="wilayah" id="wilayah" value="{{ old('wilayah') }}" 
                           class="w-full bg-gray-50 border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                           placeholder="Contoh: Jawa Barat">
                    @error('wilayah')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Rute Perjalanan -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">
                <i class="fas fa-route mr-2 text-green-500"></i>Rute Perjalanan
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Dari -->
                <div>
                    <label for="dari" class="block text-sm font-medium text-gray-700 mb-2">Dari</label>
                    <input type="text" name="dari" id="dari" value="{{ old('dari') }}" 
                           class="w-full bg-gray-50 border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                           placeholder="Contoh: Jakarta">
                    @error('dari')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ke -->
                <div>
                    <label for="ke" class="block text-sm font-medium text-gray-700 mb-2">Ke</label>
                    <input type="text" name="ke" id="ke" value="{{ old('ke') }}" 
                           class="w-full bg-gray-50 border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                           placeholder="Contoh: Surabaya">
                    @error('ke')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jarak -->
                <div>
                    <label for="jarak_dari_penjaringan_km" class="block text-sm font-medium text-gray-700 mb-2">Jarak dari Penjaringan (km)</label>
                    <input type="number" step="0.01" name="jarak_dari_penjaringan_km" id="jarak_dari_penjaringan_km" value="{{ old('jarak_dari_penjaringan_km') }}" 
                           class="w-full bg-gray-50 border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                           placeholder="0.00">
                    @error('jarak_dari_penjaringan_km')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Liter -->
                <div>
                    <label for="liter" class="block text-sm font-medium text-gray-700 mb-2">Konsumsi BBM (Liter)</label>
                    <input type="number" step="0.01" name="liter" id="liter" value="{{ old('liter') }}" 
                           class="w-full bg-gray-50 border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                           placeholder="0.00">
                    @error('liter')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Biaya Kontainer 20ft -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">
                <i class="fas fa-shipping-fast mr-2 text-orange-500"></i>Biaya Kontainer 20ft
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Uang Jalan 20ft -->
                <div>
                    <label for="uang_jalan_20ft" class="block text-sm font-medium text-gray-700 mb-2">Uang Jalan</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-500 text-sm">Rp</span>
                        <input type="number" step="0.01" name="uang_jalan_20ft" id="uang_jalan_20ft" value="{{ old('uang_jalan_20ft') }}" 
                               class="w-full bg-gray-50 border border-gray-300 rounded-lg p-3 pl-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               placeholder="0.00">
                    </div>
                    @error('uang_jalan_20ft')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- MEL 20ft -->
                <div>
                    <label for="mel_20ft" class="block text-sm font-medium text-gray-700 mb-2">MEL</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-500 text-sm">Rp</span>
                        <input type="number" step="0.01" name="mel_20ft" id="mel_20ft" value="{{ old('mel_20ft') }}" 
                               class="w-full bg-gray-50 border border-gray-300 rounded-lg p-3 pl-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               placeholder="0.00">
                    </div>
                    @error('mel_20ft')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ongkos Truk 20ft -->
                <div>
                    <label for="ongkos_truk_20ft" class="block text-sm font-medium text-gray-700 mb-2">Ongkos Truk</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-500 text-sm">Rp</span>
                        <input type="number" step="0.01" name="ongkos_truk_20ft" id="ongkos_truk_20ft" value="{{ old('ongkos_truk_20ft') }}" 
                               class="w-full bg-gray-50 border border-gray-300 rounded-lg p-3 pl-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               placeholder="0.00">
                    </div>
                    @error('ongkos_truk_20ft')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Antar Lokasi 20ft -->
                <div>
                    <label for="antar_lokasi_20ft" class="block text-sm font-medium text-gray-700 mb-2">Antar Lokasi</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-500 text-sm">Rp</span>
                        <input type="number" step="0.01" name="antar_lokasi_20ft" id="antar_lokasi_20ft" value="{{ old('antar_lokasi_20ft') }}" 
                               class="w-full bg-gray-50 border border-gray-300 rounded-lg p-3 pl-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               placeholder="0.00">
                    </div>
                    @error('antar_lokasi_20ft')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Biaya Kontainer 40ft -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">
                <i class="fas fa-truck mr-2 text-purple-500"></i>Biaya Kontainer 40ft
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Uang Jalan 40ft -->
                <div>
                    <label for="uang_jalan_40ft" class="block text-sm font-medium text-gray-700 mb-2">Uang Jalan</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-500 text-sm">Rp</span>
                        <input type="number" step="0.01" name="uang_jalan_40ft" id="uang_jalan_40ft" value="{{ old('uang_jalan_40ft') }}" 
                               class="w-full bg-gray-50 border border-gray-300 rounded-lg p-3 pl-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               placeholder="0.00">
                    </div>
                    @error('uang_jalan_40ft')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- MEL 40ft -->
                <div>
                    <label for="mel_40ft" class="block text-sm font-medium text-gray-700 mb-2">MEL</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-500 text-sm">Rp</span>
                        <input type="number" step="0.01" name="mel_40ft" id="mel_40ft" value="{{ old('mel_40ft') }}" 
                               class="w-full bg-gray-50 border border-gray-300 rounded-lg p-3 pl-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               placeholder="0.00">
                    </div>
                    @error('mel_40ft')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ongkos Truk 40ft -->
                <div>
                    <label for="ongkos_truk_40ft" class="block text-sm font-medium text-gray-700 mb-2">Ongkos Truk</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-500 text-sm">Rp</span>
                        <input type="number" step="0.01" name="ongkos_truk_40ft" id="ongkos_truk_40ft" value="{{ old('ongkos_truk_40ft') }}" 
                               class="w-full bg-gray-50 border border-gray-300 rounded-lg p-3 pl-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               placeholder="0.00">
                    </div>
                    @error('ongkos_truk_40ft')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Antar Lokasi 40ft -->
                <div>
                    <label for="antar_lokasi_40ft" class="block text-sm font-medium text-gray-700 mb-2">Antar Lokasi</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-500 text-sm">Rp</span>
                        <input type="number" step="0.01" name="antar_lokasi_40ft" id="antar_lokasi_40ft" value="{{ old('antar_lokasi_40ft') }}" 
                               class="w-full bg-gray-50 border border-gray-300 rounded-lg p-3 pl-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               placeholder="0.00">
                    </div>
                    @error('antar_lokasi_40ft')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Informasi Tambahan -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">
                <i class="fas fa-edit mr-2 text-red-500"></i>Informasi Tambahan
            </h3>
            
            <!-- Keterangan -->
            <div class="mb-4">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="4" 
                          class="w-full bg-gray-50 border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none" 
                          placeholder="Masukkan keterangan tambahan mengenai rute transportasi ini...">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="flex items-center space-x-2">
                <input type="checkbox" name="aktif" id="aktif" value="1" {{ old('aktif', true) ? 'checked' : '' }} 
                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                <label for="aktif" class="text-sm font-medium text-gray-700">Status Aktif</label>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <a href="{{route('master.tujuan-kegiatan-utama.index')}}" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-8 py-3 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                <i class="fas fa-save mr-2"></i>
                Simpan Data
            </button>
        </div>
    </form>
</div>
@endsection