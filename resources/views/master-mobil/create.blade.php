@extends('layouts.app')

@section('title', 'Tambah Mobil')
@section('page_title', 'Tambah Mobil')

@section('content')
<h2 class="text-xl font-bold text-gray-800 mb-4">Formulir Mobil Baru</h2>

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
    <form action="{{ route('master.mobil.store') }}" method="POST">
        @csrf

        @php
            // Definisikan kelas Tailwind untuk input yang lebih besar dan jelas, sama seperti form permohonan
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
        @endphp

        <fieldset class="mb-6">
            <legend class="text-lg font-semibold text-gray-800 mb-4">Informasi Mobil</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Aktiva -->
                <div>
                    <label for="aktiva" class="block text-sm font-medium text-gray-700">Aktiva <span class="text-red-500">*</span></label>
                    <input type="text" name="aktiva" id="aktiva" value="{{ old('aktiva') }}" class="{{ $inputClasses }}" required maxlength="50">
                    @error('aktiva')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Plat -->
                <div>
                    <label for="plat" class="block text-sm font-medium text-gray-700">Plat <span class="text-red-500">*</span></label>
                    <input type="text" name="plat" id="plat" value="{{ old('plat') }}" class="{{ $inputClasses }}" required maxlength="20">
                    @error('plat')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Rangka -->
                <div>
                    <label for="nomor_rangka" class="block text-sm font-medium text-gray-700">Nomor Rangka <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_rangka" id="nomor_rangka" value="{{ old('nomor_rangka') }}" class="{{ $inputClasses }}" required maxlength="50">
                    @error('nomor_rangka')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ukuran -->
                <div>
                    <label for="ukuran" class="block text-sm font-medium text-gray-700">Ukuran <span class="text-red-500">*</span></label>
                    <input type="text" name="ukuran" id="ukuran" value="{{ old('ukuran') }}" class="{{ $inputClasses }}" required maxlength="50">
                    @error('ukuran')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('master.mobil.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
