@extends('layouts.app')

@section('title', 'Tambah Kapal Baru')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('master-kapal.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <i class="fas fa-ship mr-2"></i>
                    Master Kapal
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Tambah Kapal</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Kapal Baru</h1>
        <p class="text-gray-600 mt-1">Lengkapi formulir di bawah untuk menambahkan data kapal baru</p>
    </div>

    <!-- Error Alert -->
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada form:</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Form Tambah Kapal</h2>
        </div>

        <form action="{{ route('master-kapal.store') }}" method="POST" class="p-6">
            @csrf

            <!-- Row 1: Kode & Kode Kapal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="kode" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="kode"
                           name="kode"
                           value="{{ old('kode') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kode') border-red-500 @enderror"
                           placeholder="Masukkan kode kapal"
                           required>
                    @error('kode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Kode unik untuk identifikasi kapal (maks. 50 karakter)</p>
                </div>

                <div>
                    <label for="kode_kapal" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Kapal
                    </label>
                    <input type="text"
                           id="kode_kapal"
                           name="kode_kapal"
                           value="{{ old('kode_kapal') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kode_kapal') border-red-500 @enderror"
                           placeholder="Masukkan kode alternatif kapal">
                    @error('kode_kapal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Kode alternatif/tambahan (opsional, maks. 100 karakter)</p>
                </div>
            </div>

            <!-- Nama Kapal -->
            <div class="mb-6">
                <label for="nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Kapal <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="nama_kapal"
                       name="nama_kapal"
                       value="{{ old('nama_kapal') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama_kapal') border-red-500 @enderror"
                       placeholder="Masukkan nama kapal"
                       required>
                @error('nama_kapal')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Row 2: Nickname & Pelayaran -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="nickname" class="block text-sm font-medium text-gray-700 mb-2">
                        Nickname
                    </label>
                    <input type="text"
                           id="nickname"
                           name="nickname"
                           value="{{ old('nickname') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nickname') border-red-500 @enderror"
                           placeholder="Masukkan nickname kapal">
                    @error('nickname')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Nama panggilan/singkatan kapal (opsional)</p>
                </div>

                <div>
                    <label for="pelayaran" class="block text-sm font-medium text-gray-700 mb-2">
                        Pelayaran (Pemilik Kapal)
                    </label>
                    <input type="text"
                           id="pelayaran"
                           name="pelayaran"
                           value="{{ old('pelayaran') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pelayaran') border-red-500 @enderror"
                           placeholder="Masukkan nama pelayaran/pemilik kapal">
                    @error('pelayaran')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Nama perusahaan pelayaran pemilik kapal (opsional)</p>
                </div>
            </div>

            <!-- Row 3: Kapasitas Kontainer & Gross Tonnage -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="kapasitas_kontainer_palka" class="block text-sm font-medium text-gray-700 mb-2">
                        Kapasitas Kontainer Palka
                    </label>
                    <div class="relative">
                        <input type="number"
                               id="kapasitas_kontainer_palka"
                               name="kapasitas_kontainer_palka"
                               value="{{ old('kapasitas_kontainer_palka') }}"
                               min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kapasitas_kontainer_palka') border-red-500 @enderror"
                               placeholder="0">
                    </div>
                    @error('kapasitas_kontainer_palka')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Kapasitas kontainer di bagian palka kapal</p>
                </div>

                <div>
                    <label for="kapasitas_kontainer_deck" class="block text-sm font-medium text-gray-700 mb-2">
                        Kapasitas Kontainer Deck
                    </label>
                    <div class="relative">
                        <input type="number"
                               id="kapasitas_kontainer_deck"
                               name="kapasitas_kontainer_deck"
                               value="{{ old('kapasitas_kontainer_deck') }}"
                               min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kapasitas_kontainer_deck') border-red-500 @enderror"
                               placeholder="0">
                    </div>
                    @error('kapasitas_kontainer_deck')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Kapasitas kontainer di bagian deck kapal</p>
                </div>

                <div>
                    <label for="gross_tonnage" class="block text-sm font-medium text-gray-700 mb-2">
                        Gross Tonnage
                    </label>
                    <div class="relative">
                        <input type="number"
                               id="gross_tonnage"
                               name="gross_tonnage"
                               value="{{ old('gross_tonnage') }}"
                               min="0"
                               step="0.01"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('gross_tonnage') border-red-500 @enderror"
                               placeholder="0.00">
                    </div>
                    @error('gross_tonnage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Gross tonnage kapal dalam ton</p>
                </div>
            </div>

            <!-- Catatan -->
            <div class="mb-6">
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan
                </label>
                <textarea id="catatan"
                          name="catatan"
                          rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('catatan') border-red-500 @enderror"
                          placeholder="Masukkan catatan tambahan tentang kapal">{{ old('catatan') }}</textarea>
                @error('catatan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select id="status"
                        name="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror"
                        required>
                    <option value="">Pilih Status</option>
                    <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 my-6"></div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <a href="{{ route('master-kapal.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200 inline-flex items-center">
                    <i class="fas fa-save mr-2"></i> Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
