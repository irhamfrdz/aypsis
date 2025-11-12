@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-500 mb-2">
            <a href="{{ route('kelola-bbm.index') }}" class="hover:text-indigo-600">
                <i class="fas fa-gas-pump mr-1"></i>
                Kelola BBM
            </a>
            <i class="fas fa-chevron-right mx-2 text-xs"></i>
            <span class="text-gray-900">Edit Data BBM</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-edit mr-2 text-amber-600"></i>
            Edit Data BBM
        </h1>
        <p class="text-gray-600 mt-1">Perbarui informasi data BBM</p>
    </div>

    <!-- Form -->
    <form action="{{ route('kelola-bbm.update', $kelolaBbm) }}" method="POST" class="bg-white rounded-lg shadow-sm p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Bulan -->
            <div>
                <label for="bulan" class="block text-sm font-medium text-gray-700 mb-2">
                    Bulan <span class="text-red-500">*</span>
                </label>
                <select name="bulan" 
                        id="bulan" 
                        required
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('bulan') border-red-300 @enderror">
                    <option value="">Pilih Bulan</option>
                    <option value="1" {{ old('bulan', $kelolaBbm->bulan) == 1 ? 'selected' : '' }}>Januari</option>
                    <option value="2" {{ old('bulan', $kelolaBbm->bulan) == 2 ? 'selected' : '' }}>Februari</option>
                    <option value="3" {{ old('bulan', $kelolaBbm->bulan) == 3 ? 'selected' : '' }}>Maret</option>
                    <option value="4" {{ old('bulan', $kelolaBbm->bulan) == 4 ? 'selected' : '' }}>April</option>
                    <option value="5" {{ old('bulan', $kelolaBbm->bulan) == 5 ? 'selected' : '' }}>Mei</option>
                    <option value="6" {{ old('bulan', $kelolaBbm->bulan) == 6 ? 'selected' : '' }}>Juni</option>
                    <option value="7" {{ old('bulan', $kelolaBbm->bulan) == 7 ? 'selected' : '' }}>Juli</option>
                    <option value="8" {{ old('bulan', $kelolaBbm->bulan) == 8 ? 'selected' : '' }}>Agustus</option>
                    <option value="9" {{ old('bulan', $kelolaBbm->bulan) == 9 ? 'selected' : '' }}>September</option>
                    <option value="10" {{ old('bulan', $kelolaBbm->bulan) == 10 ? 'selected' : '' }}>Oktober</option>
                    <option value="11" {{ old('bulan', $kelolaBbm->bulan) == 11 ? 'selected' : '' }}>November</option>
                    <option value="12" {{ old('bulan', $kelolaBbm->bulan) == 12 ? 'selected' : '' }}>Desember</option>
                </select>
                @error('bulan')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tahun -->
            <div>
                <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">
                    Tahun <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="tahun" 
                       id="tahun" 
                       value="{{ old('tahun', $kelolaBbm->tahun) }}"
                       min="2000"
                       max="2100"
                       required
                       placeholder="2025"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('tahun') border-red-300 @enderror">
                @error('tahun')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- BBM Per Liter -->
            <div>
                <label for="bbm_per_liter" class="block text-sm font-medium text-gray-700 mb-2">
                    BBM Per Liter (Rp) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">Rp</span>
                    </div>
                    <input type="number" 
                           name="bbm_per_liter" 
                           id="bbm_per_liter" 
                           value="{{ old('bbm_per_liter', $kelolaBbm->bbm_per_liter) }}"
                           step="0.01"
                           min="0"
                           required
                           placeholder="10000"
                           class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('bbm_per_liter') border-red-300 @enderror">
                </div>
                @error('bbm_per_liter')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Masukkan harga BBM per liter dalam Rupiah</p>
            </div>

            <!-- Persentase -->
            <div>
                <label for="persentase" class="block text-sm font-medium text-gray-700 mb-2">
                    Persentase (%) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" 
                           name="persentase" 
                           id="persentase" 
                           value="{{ old('persentase', $kelolaBbm->persentase) }}"
                           step="0.01"
                           min="0"
                           max="100"
                           required
                           placeholder="5.50"
                           class="block w-full pr-12 pl-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('persentase') border-red-300 @enderror">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">%</span>
                    </div>
                </div>
                @error('persentase')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Masukkan persentase (0-100)</p>
            </div>

            <!-- Keterangan -->
            <div class="md:col-span-2">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan <span class="text-gray-500">(Opsional)</span>
                </label>
                <textarea name="keterangan" 
                          id="keterangan" 
                          rows="4"
                          placeholder="Tambahkan catatan atau keterangan mengenai data BBM ini..."
                          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('keterangan') border-red-300 @enderror">{{ old('keterangan', $kelolaBbm->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-blue-800">Informasi:</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Bulan, tahun, BBM per liter, dan persentase wajib diisi</li>
                            <li>Bulan dan tahun digunakan untuk periode data BBM</li>
                            <li>BBM per liter harus berupa angka positif dalam Rupiah</li>
                            <li>Persentase harus antara 0 hingga 100</li>
                            <li>Keterangan bersifat opsional</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('kelola-bbm.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors duration-200">
                <i class="fas fa-save mr-2"></i>
                Update Data BBM
            </button>
        </div>
    </form>
</div>
@endsection
