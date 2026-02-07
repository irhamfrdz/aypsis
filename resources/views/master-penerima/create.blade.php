@extends('layouts.app')

@section('title', 'Tambah Penerima')
@section('page_title', 'Tambah Penerima')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h1 class="text-lg font-medium text-gray-900">Tambah Penerima Baru</h1>
                <a href="{{ route('penerima.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    &larr; Kembali
                </a>
            </div>
            
            <form action="{{ route('penerima.store') }}" method="POST" class="p-6">
                @csrf
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="kode" class="block text-sm font-medium text-gray-700">Kode Penerima <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="text" name="kode" id="kode" value="{{ old('kode', \App\Models\Penerima::count() == 0 ? 'MR00001' : '') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                <p class="mt-1 text-xs text-gray-500">Contoh: MR00001. Biarkan kosong jika ingin generate otomatis (logic perlu ditambahkan di controller jika ingin auto-gen saat submit, tapi sebaiknya pre-fill).</p>
                            </div>
                            @error('kode')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-6">
                            <label for="nama_penerima" class="block text-sm font-medium text-gray-700">Nama Penerima <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="text" name="nama_penerima" id="nama_penerima" value="{{ old('nama_penerima') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                            </div>
                            @error('nama_penerima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-6">
                            <label for="contact_person" class="block text-sm font-medium text-gray-700">Contact Person</label>
                            <div class="mt-1">
                                <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('contact_person')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-6">
                            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                            <div class="mt-1">
                                <textarea name="alamat" id="alamat" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('alamat') }}</textarea>
                            </div>
                            @error('alamat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="npwp" class="block text-sm font-medium text-gray-700">NPWP</label>
                            <div class="mt-1">
                                <input type="text" name="npwp" id="npwp" value="{{ old('npwp') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('npwp')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="nitku" class="block text-sm font-medium text-gray-700">NITKU</label>
                            <div class="mt-1">
                                <input type="text" name="nitku" id="nitku" value="{{ old('nitku') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('nitku')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-6">
                            <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan</label>
                            <div class="mt-1">
                                <textarea name="catatan" id="catatan" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('catatan') }}</textarea>
                            </div>
                            @error('catatan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="iu_bp_kawasan" class="block text-sm font-medium text-gray-700">IU BP Kawasan</label>
                            <div class="mt-1">
                                <select name="iu_bp_kawasan" id="iu_bp_kawasan" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="tidak ada" {{ old('iu_bp_kawasan') == 'tidak ada' ? 'selected' : '' }}>Tidak Ada</option>
                                    <option value="ada" {{ old('iu_bp_kawasan') == 'ada' ? 'selected' : '' }}>Ada</option>
                                </select>
                            </div>
                            @error('iu_bp_kawasan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select name="status" id="status" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end">
                    <a href="{{ route('penerima.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
