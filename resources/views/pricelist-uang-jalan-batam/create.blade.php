@extends('layouts.app')

@section('title', 'Tambah Pricelist Uang Jalan Batam')
@section('page_title', 'Tambah Pricelist Uang Jalan Batam')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Tambah Pricelist Baru</h2>
            <p class="mt-1 text-sm text-gray-600">Isi form di bawah untuk menambah pricelist uang jalan Batam</p>
        </div>
        <a href="{{ route('pricelist-uang-jalan-batam.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="mb-6 rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Terdapat beberapa error:</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('pricelist-uang-jalan-batam.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Expedisi -->
            <div>
                <label for="expedisi" class="block text-sm font-medium text-gray-700 mb-2">
                    Expedisi <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="expedisi" 
                       id="expedisi" 
                       value="{{ old('expedisi') }}"
                       placeholder="Contoh: ATB, AYP"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('expedisi') border-red-300 @enderror">
                @error('expedisi')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ring -->
            <div>
                <label for="ring" class="block text-sm font-medium text-gray-700 mb-2">
                    Ring <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="ring" 
                       id="ring" 
                       value="{{ old('ring') }}"
                       placeholder="Contoh: 1, 2, 3"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('ring') border-red-300 @enderror">
                @error('ring')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Rute -->
            <div>
                <label for="rute" class="block text-sm font-medium text-gray-700 mb-2">
                    Rute <span class="text-gray-500">(Opsional)</span>
                </label>
                <input type="text" 
                       name="rute" 
                       id="rute" 
                       value="{{ old('rute') }}"
                       placeholder="Contoh: Batam - Tanjung Uncang"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('rute') border-red-300 @enderror">
                @error('rute')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Size -->
            <div>
                <label for="size" class="block text-sm font-medium text-gray-700 mb-2">
                    Size <span class="text-red-500">*</span>
                </label>
                <select name="size" 
                        id="size"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('size') border-red-300 @enderror">
                    <option value="">-- Pilih Size --</option>
                    <option value="20FT" {{ old('size') == '20FT' ? 'selected' : '' }}>20FT</option>
                    <option value="40FT" {{ old('size') == '40FT' ? 'selected' : '' }}>40FT</option>
                    <option value="45FT" {{ old('size') == '45FT' ? 'selected' : '' }}>45FT</option>
                </select>
                @error('size')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- F/E -->
            <div>
                <label for="f_e" class="block text-sm font-medium text-gray-700 mb-2">
                    F/E (Full/Empty) <span class="text-red-500">*</span>
                </label>
                <select name="f_e" 
                        id="f_e"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('f_e') border-red-300 @enderror">
                    <option value="">-- Pilih F/E --</option>
                    <option value="Full" {{ old('f_e') == 'Full' ? 'selected' : '' }}>Full</option>
                    <option value="Empty" {{ old('f_e') == 'Empty' ? 'selected' : '' }}>Empty</option>
                </select>
                @error('f_e')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tarif -->
            <div>
                <label for="tarif" class="block text-sm font-medium text-gray-700 mb-2">
                    Tarif (Rp) <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="tarif" 
                       id="tarif" 
                       value="{{ old('tarif') }}"
                       placeholder="Contoh: 170500"
                       min="0"
                       step="0.01"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('tarif') border-red-300 @enderror">
                @error('tarif')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-gray-500">(Opsional)</span>
                </label>
                <select name="status" 
                        id="status"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-300 @enderror">
                    <option value="">-- Tidak Ada --</option>
                    <option value="AQUA" {{ old('status') == 'AQUA' ? 'selected' : '' }}>AQUA</option>
                    <option value="CHASIS PB" {{ old('status') == 'CHASIS PB' ? 'selected' : '' }}>CHASIS PB</option>
                </select>
                @error('status')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-900">Catatan:</h3>
                    <div class="mt-2 text-sm text-blue-800">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Field yang bertanda <span class="text-red-500">*</span> wajib diisi</li>
                            <li>Expedisi: Nama perusahaan expedisi (contoh: ATB, AYP)</li>
                            <li>Ring: Area zona pengiriman (1-5)</li>
                            <li>Size: Ukuran kontainer (20FT, 40FT, 45FT)</li>
                            <li>F/E: Status kontainer Full (penuh) atau Empty (kosong)</li>
                            <li>Status: Khusus untuk AQUA atau CHASIS PB (opsional)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('pricelist-uang-jalan-batam.index') }}" 
               class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Batal
            </a>
            <button type="submit" 
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-save mr-2"></i>
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
