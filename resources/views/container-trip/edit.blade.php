@extends('layouts.app')

@section('title', 'Edit Perjalanan Kontainer')
@section('page_title', 'Edit Perjalanan Kontainer')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                <div class="flex items-center">
                    <a href="{{ route('container-trip.index') }}" class="mr-4 text-white hover:text-indigo-100 transition duration-150 ease-in-out">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Edit Perjalanan Kontainer</h1>
                        <p class="text-indigo-100 text-sm">Perbarui data perjalanan {{ $containerTrip->no_kontainer }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <form action="{{ route('container-trip.update', $containerTrip) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Vendor -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="vendor_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Vendor <span class="text-red-500">*</span>
                        </label>
                        <select name="vendor_id" id="vendor_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('vendor_id') border-red-500 @enderror" required>
                            <option value="">Pilih Vendor</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ old('vendor_id', $containerTrip->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->nama_vendor }}
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- No Kontainer -->
                    <div>
                        <label for="no_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                            No. Kontainer <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="no_kontainer"
                               id="no_kontainer"
                               value="{{ old('no_kontainer', $containerTrip->no_kontainer) }}"
                               placeholder="Contoh: ABCD1234567"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('no_kontainer') border-red-500 @enderror"
                               required>
                        @error('no_kontainer')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Size -->
                    <div>
                        <label for="ukuran" class="block text-sm font-medium text-gray-700 mb-1">
                            Ukuran <span class="text-red-500">*</span>
                        </label>
                        <select name="ukuran" id="ukuran" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('ukuran') border-red-500 @enderror" required>
                            <option value="20" {{ old('ukuran', $containerTrip->ukuran) == '20' ? 'selected' : '' }}>20'</option>
                            <option value="40" {{ old('ukuran', $containerTrip->ukuran) == '40' ? 'selected' : '' }}>40'</option>
                        </select>
                        @error('ukuran')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Ambil -->
                    <div>
                        <label for="tgl_ambil" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Ambil <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="tgl_ambil"
                               id="tgl_ambil"
                               value="{{ old('tgl_ambil', $containerTrip->tgl_ambil->format('Y-m-d')) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tgl_ambil') border-red-500 @enderror"
                               required>
                        @error('tgl_ambil')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Kembali -->
                    <div>
                        <label for="tgl_kembali" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Kembali
                        </label>
                        <input type="date"
                               name="tgl_kembali"
                               id="tgl_kembali"
                               value="{{ old('tgl_kembali', $containerTrip->tgl_kembali ? $containerTrip->tgl_kembali->format('Y-m-d') : '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tgl_kembali') border-red-500 @enderror">
                        @error('tgl_kembali')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga Sewa -->
                    <div>
                        <label for="harga_sewa" class="block text-sm font-medium text-gray-700 mb-1">
                            Harga Sewa <span class="text-red-500">*</span>
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm text-sm">Rp</span>
                            </div>
                            <input type="number"
                                   step="0.01"
                                   name="harga_sewa"
                                   id="harga_sewa"
                                   value="{{ old('harga_sewa', $containerTrip->harga_sewa) }}"
                                   placeholder="0.00"
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('harga_sewa') border-red-500 @enderror"
                                   required>
                        </div>
                        @error('harga_sewa')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>


                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('container-trip.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Perbarui Perjalanan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
