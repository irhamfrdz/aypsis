@extends('layouts.app')

@section('title', 'Tambah Penerima')

@section('content')
<div class="container mx-auto px-4 py-6">
    @if($isPopup)
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-blue-500 mr-3"></i>
            <p class="text-blue-700 text-sm">Mode popup: Data akan otomatis dipilih setelah disimpan</p>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h1 class="text-xl font-bold text-gray-900">Tambah Penerima Baru</h1>
        <p class="text-gray-600 mt-1 text-sm">Lengkapi form di bawah ini untuk menambahkan penerima</p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex">
                <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
                <div class="flex-1">
                    <h3 class="text-red-800 font-medium">Terdapat kesalahan pada form:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('order.penerima.store') }}" method="POST">
        @csrf
        @if($isPopup)
            <input type="hidden" name="popup" value="1">
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Penerima</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="kode" class="block text-sm font-medium text-gray-700 mb-1">Kode <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="kode" 
                           id="kode" 
                           value="{{ old('kode', $nextKode) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-gray-50 @error('kode') border-red-500 @enderror"
                           readonly>
                    @error('kode')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama_penerima" class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="nama_penerima" 
                           id="nama_penerima" 
                           value="{{ old('nama_penerima', $search) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('nama_penerima') border-red-500 @enderror"
                           required
                           autofocus>
                    @error('nama_penerima')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                    <input type="text" 
                           name="contact_person" 
                           id="contact_person" 
                           value="{{ old('contact_person') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('contact_person') border-red-500 @enderror">
                    @error('contact_person')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="npwp" class="block text-sm font-medium text-gray-700 mb-1">NPWP</label>
                    <input type="text" 
                           name="npwp" 
                           id="npwp" 
                           value="{{ old('npwp') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('npwp') border-red-500 @enderror">
                    @error('npwp')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nitku" class="block text-sm font-medium text-gray-700 mb-1">NITKU</label>
                    <input type="text" 
                           name="nitku" 
                           id="nitku" 
                           value="{{ old('nitku') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('nitku') border-red-500 @enderror">
                    @error('nitku')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" 
                            id="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('status') border-red-500 @enderror"
                            required>
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="alamat" 
                              id="alamat" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('alamat') border-red-500 @enderror">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            @if($isPopup)
                <button type="button"
                        onclick="window.close()"
                        class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition duration-200">
                    Batal
                </button>
            @else
                <a href="{{ route('penerima.index') }}"
                   class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition duration-200">
                    Batal
                </a>
            @endif
            <button type="submit"
                    class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition duration-200">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
