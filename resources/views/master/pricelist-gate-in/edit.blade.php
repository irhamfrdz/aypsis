@extends('layouts.app')

@section('title', 'Edit Master Pricelist Gate Pelabuhan Sunda Kelapa')
@section('page_title', 'Edit Master Pricelist Gate Pelabuhan Sunda Kelapa')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center">
                    <svg class="w-8 h-8 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Edit Master Pricelist Gate Pelabuhan Sunda Kelapa</h1>
                        <p class="text-blue-100 text-sm">Edit pricelist gate pelabuhan sunda kelapa: {{ $pricelistGateIn->pelabuhan }} - {{ $pricelistGateIn->kegiatan }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <form action="{{ route('master.pricelist-gate-in.update', $pricelistGateIn) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pelabuhan -->
                    <div>
                        <label for="pelabuhan" class="block text-sm font-medium text-gray-700 mb-2">
                            Pelabuhan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="pelabuhan" id="pelabuhan" value="{{ old('pelabuhan', $pricelistGateIn->pelabuhan) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pelabuhan') border-red-500 @enderror"
                               placeholder="Masukkan nama pelabuhan" required>
                        @error('pelabuhan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kegiatan -->
                    <div>
                        <label for="kegiatan" class="block text-sm font-medium text-gray-700 mb-2">
                            Kegiatan <span class="text-red-500">*</span>
                        </label>
                        <select name="kegiatan" id="kegiatan"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kegiatan') border-red-500 @enderror" required>
                            <option value="">Pilih Kegiatan</option>
                            <option value="BATAL MUAT" {{ old('kegiatan', $pricelistGateIn->kegiatan) == 'BATAL MUAT' ? 'selected' : '' }}>BATAL MUAT</option>
                            <option value="CHANGE VASSEL" {{ old('kegiatan', $pricelistGateIn->kegiatan) == 'CHANGE VASSEL' ? 'selected' : '' }}>CHANGE VASSEL</option>
                            <option value="DELIVERY" {{ old('kegiatan', $pricelistGateIn->kegiatan) == 'DELIVERY' ? 'selected' : '' }}>DELIVERY</option>
                            <option value="DISCHARGE" {{ old('kegiatan', $pricelistGateIn->kegiatan) == 'DISCHARGE' ? 'selected' : '' }}>DISCHARGE</option>
                            <option value="DISCHARGE TL" {{ old('kegiatan', $pricelistGateIn->kegiatan) == 'DISCHARGE TL' ? 'selected' : '' }}>DISCHARGE TL</option>
                            <option value="LOADING" {{ old('kegiatan', $pricelistGateIn->kegiatan) == 'LOADING' ? 'selected' : '' }}>LOADING</option>
                            <option value="PENUMPUKAN BPRP" {{ old('kegiatan', $pricelistGateIn->kegiatan) == 'PENUMPUKAN BPRP' ? 'selected' : '' }}>PENUMPUKAN BPRP</option>
                            <option value="PERPANJANGAN DELIVERY" {{ old('kegiatan', $pricelistGateIn->kegiatan) == 'PERPANJANGAN DELIVERY' ? 'selected' : '' }}>PERPANJANGAN DELIVERY</option>
                            <option value="RECEIVING" {{ old('kegiatan', $pricelistGateIn->kegiatan) == 'RECEIVING' ? 'selected' : '' }}>RECEIVING</option>
                            <option value="RECEIVING LOSING" {{ old('kegiatan', $pricelistGateIn->kegiatan) == 'RECEIVING LOSING' ? 'selected' : '' }}>RECEIVING LOSING</option>
                        </select>
                        @error('kegiatan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Biaya -->
                    <div>
                        <label for="biaya" class="block text-sm font-medium text-gray-700 mb-2">
                            Biaya <span class="text-red-500">*</span>
                        </label>
                        <select name="biaya" id="biaya"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('biaya') border-red-500 @enderror" required>
                            <option value="">Pilih Biaya</option>
                            <option value="ADMINISTRASI" {{ old('biaya', $pricelistGateIn->biaya) == 'ADMINISTRASI' ? 'selected' : '' }}>ADMINISTRASI</option>
                            <option value="DERMAGA" {{ old('biaya', $pricelistGateIn->biaya) == 'DERMAGA' ? 'selected' : '' }}>DERMAGA</option>
                            <option value="HAULAGE" {{ old('biaya', $pricelistGateIn->biaya) == 'HAULAGE' ? 'selected' : '' }}>HAULAGE</option>
                            <option value="LOLO" {{ old('biaya', $pricelistGateIn->biaya) == 'LOLO' ? 'selected' : '' }}>LOLO</option>
                            <option value="MASA 1A" {{ old('biaya', $pricelistGateIn->biaya) == 'MASA 1A' ? 'selected' : '' }}>MASA 1A</option>
                            <option value="MASA 1B" {{ old('biaya', $pricelistGateIn->biaya) == 'MASA 1B' ? 'selected' : '' }}>MASA 1B</option>
                            <option value="MASA2" {{ old('biaya', $pricelistGateIn->biaya) == 'MASA2' ? 'selected' : '' }}>MASA2</option>
                            <option value="STEVEDORING" {{ old('biaya', $pricelistGateIn->biaya) == 'STEVEDORING' ? 'selected' : '' }}>STEVEDORING</option>
                            <option value="STRIPPING" {{ old('biaya', $pricelistGateIn->biaya) == 'STRIPPING' ? 'selected' : '' }}>STRIPPING</option>
                            <option value="STUFFING" {{ old('biaya', $pricelistGateIn->biaya) == 'STUFFING' ? 'selected' : '' }}>STUFFING</option>
                        </select>
                        @error('biaya')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gudang -->
                    <div>
                        <label for="gudang" class="block text-sm font-medium text-gray-700 mb-2">
                            Gudang
                        </label>
                        <select name="gudang" id="gudang"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('gudang') border-red-500 @enderror">
                            <option value="">Pilih Gudang</option>
                            <option value="CY" {{ old('gudang', $pricelistGateIn->gudang) == 'CY' ? 'selected' : '' }}>CY</option>
                            <option value="DERMAGA" {{ old('gudang', $pricelistGateIn->gudang) == 'DERMAGA' ? 'selected' : '' }}>DERMAGA</option>
                            <option value="SS" {{ old('gudang', $pricelistGateIn->gudang) == 'SS' ? 'selected' : '' }}>SS</option>
                        </select>
                        @error('gudang')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kontainer -->
                    <div>
                        <label for="kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                            Kontainer
                        </label>
                        <select name="kontainer" id="kontainer"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kontainer') border-red-500 @enderror">
                            <option value="">Pilih Kontainer</option>
                            <option value="20" {{ old('kontainer', $pricelistGateIn->kontainer) == '20' ? 'selected' : '' }}>20 Feet</option>
                            <option value="40" {{ old('kontainer', $pricelistGateIn->kontainer) == '40' ? 'selected' : '' }}>40 Feet</option>
                        </select>
                        @error('kontainer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Muatan -->
                    <div>
                        <label for="muatan" class="block text-sm font-medium text-gray-700 mb-2">
                            Muatan
                        </label>
                        <select name="muatan" id="muatan"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('muatan') border-red-500 @enderror">
                            <option value="">Pilih Muatan</option>
                            <option value="EMPTY" {{ old('muatan', $pricelistGateIn->muatan) == 'EMPTY' ? 'selected' : '' }}>EMPTY</option>
                            <option value="FULL" {{ old('muatan', $pricelistGateIn->muatan) == 'FULL' ? 'selected' : '' }}>FULL</option>
                        </select>
                        @error('muatan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tarif -->
                    <div>
                        <label for="tarif" class="block text-sm font-medium text-gray-700 mb-2">
                            Tarif <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="tarif" id="tarif" value="{{ old('tarif', $pricelistGateIn->tarif) }}" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tarif') border-red-500 @enderror"
                               placeholder="0.00" required>
                        @error('tarif')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror" required>
                            <option value="">Pilih Status</option>
                            <option value="aktif" {{ old('status', $pricelistGateIn->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status', $pricelistGateIn->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('master.pricelist-gate-in.show', $pricelistGateIn) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
