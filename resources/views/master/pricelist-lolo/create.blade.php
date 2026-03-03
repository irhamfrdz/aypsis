@extends('layouts.app')

@section('title', 'Tambah Pricelist LOLO')

@section('content')
    <h2 class="text-xl font-bold text-gray-800 mb-4">Form Tambah Pricelist LOLO</h2>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada isian form:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul role="list" class="list-disc pl-5 space-y-1">
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
        <form action="{{ route('master.pricelist-lolo.store') }}" method="POST">
            @csrf

            @php
                $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="lokasi" class="block text-sm font-medium text-gray-700">Lokasi</label>
                    <select name="lokasi" id="lokasi" class="{{ $inputClasses }}" required>
                        <option value="" disabled selected>-- Pilih Lokasi --</option>
                        <option value="Jakarta" {{ old('lokasi') == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                        <option value="Batam" {{ old('lokasi') == 'Batam' ? 'selected' : '' }}>Batam</option>
                        <option value="Pinang" {{ old('lokasi') == 'Pinang' ? 'selected' : '' }}>Pinang</option>
                    </select>
                </div>

                <div>
                    <label for="vendor" class="block text-sm font-medium text-gray-700">Vendor</label>
                    <input type="text" name="vendor" id="vendor" value="{{ old('vendor') }}" class="{{ $inputClasses }}" placeholder="Contoh: Nama PT/Vendor" required>
                </div>

                <div>
                    <label for="size" class="block text-sm font-medium text-gray-700">Ukuran Kontainer</label>
                    <select name="size" id="size" class="{{ $inputClasses }}" required>
                        <option value="" disabled selected>-- Pilih Ukuran --</option>
                        <option value="20" {{ old('size') == '20' ? 'selected' : '' }}>20'</option>
                        <option value="40" {{ old('size') == '40' ? 'selected' : '' }}>40'</option>
                        <option value="45" {{ old('size') == '45' ? 'selected' : '' }}>45'</option>
                    </select>
                </div>

                <div>
                    <label for="tarif" class="block text-sm font-medium text-gray-700">Biaya (IDR)</label>
                    <input type="number" name="tarif" id="tarif" value="{{ old('tarif') }}" class="{{ $inputClasses }}" placeholder="0" required>
                </div>

                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori</label>
                    <select name="kategori" id="kategori" class="{{ $inputClasses }}" required>
                        <option value="Full" {{ old('kategori') == 'Full' ? 'selected' : '' }}>Full</option>
                        <option value="Empty" {{ old('kategori') == 'Empty' ? 'selected' : '' }}>Empty</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="{{ $inputClasses }}" required>
                        <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>AKTIF</option>
                        <option value="non-aktif" {{ old('status') == 'non-aktif' ? 'selected' : '' }}>TIDAK AKTIF</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('master.pricelist-lolo.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-indigo-500 focus:ring-offset-2">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Simpan
                </button>
            </div>
        </form>
    </div>
@endsection
