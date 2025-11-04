@extends('layouts.app')

@section('title', 'Tambah Uang Jalan Batam')
@section('page_title', 'Tambah Uang Jalan Batam')

@section('content')
<h2 class="text-xl font-bold text-gray-800 mb-4">Formulir Uang Jalan Batam Baru</h2>

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
    <form action="{{ route('uang-jalan-batam.store') }}" method="POST">
        @csrf

        @php
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
        @endphp

        <fieldset class="mb-6">
            <legend class="text-lg font-semibold text-gray-800 mb-4">Informasi Rute</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Wilayah -->
                <div>
                    <label for="wilayah" class="block text-sm font-medium text-gray-700">
                        Wilayah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="wilayah" id="wilayah" value="{{ old('wilayah') }}" 
                           class="{{ $inputClasses }}" required maxlength="255" 
                           placeholder="Contoh: Jakarta, Batam">
                    @error('wilayah')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rute -->
                <div>
                    <label for="rute" class="block text-sm font-medium text-gray-700">
                        Rute <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="rute" id="rute" value="{{ old('rute') }}" 
                           class="{{ $inputClasses }}" required maxlength="255" 
                           placeholder="Contoh: Jakarta - Batam">
                    @error('rute')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expedisi -->
                <div>
                    <label for="expedisi" class="block text-sm font-medium text-gray-700">
                        Expedisi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="expedisi" id="expedisi" value="{{ old('expedisi') }}" 
                           class="{{ $inputClasses }}" required maxlength="255" 
                           placeholder="Contoh: JNE, TIKI, POS">
                    @error('expedisi')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ring -->
                <div>
                    <label for="ring" class="block text-sm font-medium text-gray-700">
                        Ring <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="ring" id="ring" value="{{ old('ring') }}" 
                           class="{{ $inputClasses }}" required maxlength="255" 
                           placeholder="Contoh: Ring 1, Ring 2">
                    @error('ring')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>

        <fieldset class="mb-6">
            <legend class="text-lg font-semibold text-gray-800 mb-4">Informasi Kontainer</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- FT -->
                <div>
                    <label for="ft" class="block text-sm font-medium text-gray-700">
                        FT <span class="text-red-500">*</span>
                    </label>
                    <select name="ft" id="ft" class="{{ $inputClasses }}" required>
                        <option value="">-- Pilih FT --</option>
                        <option value="20FT" {{ old('ft') == '20FT' ? 'selected' : '' }}>20FT</option>
                        <option value="40FT" {{ old('ft') == '40FT' ? 'selected' : '' }}>40FT</option>
                        <option value="45FT" {{ old('ft') == '45FT' ? 'selected' : '' }}>45FT</option>
                    </select>
                    @error('ft')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- F/E -->
                <div>
                    <label for="f_e" class="block text-sm font-medium text-gray-700">
                        F/E <span class="text-red-500">*</span>
                    </label>
                    <select name="f_e" id="f_e" class="{{ $inputClasses }}" required>
                        <option value="">-- Pilih F/E --</option>
                        <option value="Full" {{ old('f_e') == 'Full' ? 'selected' : '' }}>Full</option>
                        <option value="Empty" {{ old('f_e') == 'Empty' ? 'selected' : '' }}>Empty</option>
                    </select>
                    @error('f_e')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>

        <fieldset class="mb-6">
            <legend class="text-lg font-semibold text-gray-800 mb-4">Informasi Tarif</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tarif -->
                <div>
                    <label for="tarif" class="block text-sm font-medium text-gray-700">
                        Tarif <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="tarif" id="tarif" value="{{ old('tarif') }}" 
                           class="{{ $inputClasses }}" required min="0" step="0.01" 
                           placeholder="0">
                    @error('tarif')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="{{ $inputClasses }}">
                        <option value="">-- Pilih Status --</option>
                        <option value="aqua" {{ old('status') == 'aqua' ? 'selected' : '' }}>Aqua</option>
                        <option value="chasis PB" {{ old('status') == 'chasis PB' ? 'selected' : '' }}>Chasis PB</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Berlaku -->
                <div>
                    <label for="tanggal_berlaku" class="block text-sm font-medium text-gray-700">
                        Tanggal Berlaku <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_berlaku" id="tanggal_berlaku" 
                           value="{{ old('tanggal_berlaku') }}" 
                           class="{{ $inputClasses }}" required>
                    @error('tanggal_berlaku')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('uang-jalan-batam.index') }}" 
               class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Batal
            </a>
            <button type="submit" 
                    class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection