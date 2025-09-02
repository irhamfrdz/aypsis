@extends('layouts.app')

@section('title', 'Pricelist Kontainer Sewa')
@section('page_title', 'Pricelist Kontainer Sewa')

@section('content')
<h2 class="text-xl font-bold text-gray-800 mb-4">Pricelist Kontainer Sewa</h2>

@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-4">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('master.pricelist-sewa-kontainer.store') }}" method="POST" id="formPricelist">
        @csrf

        @php
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
        @endphp

        <fieldset class="mb-6">
            <legend class="text-lg font-semibold text-gray-800 mb-4">Pricelist Kontainer Sewa</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Vendor -->
                <div>
                    <label for="vendor" class="block text-sm font-medium text-gray-700">Vendor <span class="text-red-500">*</span></label>
                    <select name="vendor" id="vendor" class="{{ $inputClasses }}" required>
                        <option value="">-- Pilih Vendor --</option>
                        <option value="ZONA" {{ old('vendor') == 'ZONA' ? 'selected' : '' }}>ZONA</option>
                        <option value="DPE" {{ old('vendor') == 'DPE' ? 'selected' : '' }}>DPE</option>
                    </select>
                    @error('vendor')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Tarif -->
                <div>
                    <label for="tarif" class="block text-sm font-medium text-gray-700">Tarif <span class="text-red-500">*</span></label>
                    <select name="tarif" id="tarif" class="{{ $inputClasses }}" required>
                        <option value="">-- Pilih Tarif --</option>
                        <option value="Bulanan" {{ old('tarif') == 'Bulanan' ? 'selected' : '' }}>Bulanan</option>
                        <option value="Harian" {{ old('tarif') == 'Harian' ? 'selected' : '' }}>Harian</option>
                    </select>
                    @error('tarif')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Ukuran Kontainer -->
                <div>
                    <label for="ukuran_kontainer" class="block text-sm font-medium text-gray-700">Ukuran Kontainer <span class="text-red-500">*</span></label>
                    <select name="ukuran_kontainer" id="ukuran_kontainer" class="{{ $inputClasses }}" required>
                        <option value="">-- Pilih Ukuran --</option>
                        <option value="20" {{ old('ukuran_kontainer') == '20' ? 'selected' : '' }}>20 ft</option>
                        <option value="40" {{ old('ukuran_kontainer') == '40' ? 'selected' : '' }}>40 ft</option>
                    </select>
                    @error('ukuran_kontainer')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Harga -->
                <div>
                    <label for="harga" class="block text-sm font-medium text-gray-700">Harga <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" name="harga" id="harga" value="{{ old('harga') }}" class="pl-10 {{ $inputClasses }}" required autocomplete="off">
                    </div>
                    @error('harga')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var hargaInput = document.getElementById('harga');
                            var form = document.getElementById('formPricelist');
                            hargaInput.addEventListener('input', function() {
                                var val = hargaInput.value.replace(/\D/g, '');
                                if (val !== '') {
                                    hargaInput.value = parseInt(val, 10).toLocaleString('id');
                                } else {
                                    hargaInput.value = '';
                                }
                            });
                            hargaInput.addEventListener('blur', function() {
                                var val = hargaInput.value.replace(/\D/g, '');
                                if (val !== '') {
                                    hargaInput.value = parseInt(val, 10).toLocaleString('id');
                                } else {
                                    hargaInput.value = '';
                                }
                            });
                            form.addEventListener('submit', function(e) {
                                hargaInput.value = hargaInput.value.replace(/\./g, '');
                            });
                        });
                    </script>
                </div>
                <!-- Tanggal Harga Awal -->
                <div>
                    <label for="tanggal_harga_awal" class="block text-sm font-medium text-gray-700">Tanggal Harga Awal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_harga_awal" id="tanggal_harga_awal" value="{{ old('tanggal_harga_awal') }}" class="{{ $inputClasses }}" required>
                    @error('tanggal_harga_awal')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Tanggal Harga Akhir -->
                <div>
                    <label for="tanggal_harga_akhir" class="block text-sm font-medium text-gray-700">Tanggal Harga Akhir</label>
                    <input type="date" name="tanggal_harga_akhir" id="tanggal_harga_akhir" value="{{ old('tanggal_harga_akhir') }}" class="{{ $inputClasses }}">
                    @error('tanggal_harga_akhir')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="{{ $inputClasses }}">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>
        <div class="flex justify-end space-x-4">
            <a href="{{ route('master.pricelist-sewa-kontainer.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
