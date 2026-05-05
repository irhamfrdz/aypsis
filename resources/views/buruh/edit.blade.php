@extends('layouts.app')

@section('title', 'Edit Buruh')
@section('page_title', 'Edit Data Buruh')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <a href="{{ route('master.buruh.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                <svg class="mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar
            </a>
            <h1 class="mt-2 text-3xl font-extrabold text-gray-900 tracking-tight">Edit Data Buruh</h1>
            <p class="mt-2 text-sm text-gray-600">Perbarui informasi untuk <strong>{{ $buruh->nama }}</strong>.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <form action="{{ route('master.buruh.update', $buruh->id) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <!-- Nama -->
                    <div>
                        <label for="nama" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $buruh->nama) }}" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all duration-200 @error('nama') border-red-500 @enderror"
                               placeholder="Contoh: Budi Santoso">
                        @error('nama') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Telepon -->
                        <div>
                            <label for="telepon" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon/WA</label>
                            <input type="text" name="telepon" id="telepon" value="{{ old('telepon', $buruh->telepon) }}"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all duration-200"
                                   placeholder="0812xxxxxx">
                        </div>

                        <!-- Jabatan -->
                        <div>
                            <label for="jabatan" class="block text-sm font-semibold text-gray-700 mb-1">Jabatan/Keahlian</label>
                            <input type="text" name="jabatan" id="jabatan" value="{{ old('jabatan', $buruh->jabatan) }}"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all duration-200"
                                   placeholder="Contoh: Tukang Angkut">
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label for="alamat" class="block text-sm font-semibold text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamat" rows="3"
                                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all duration-200"
                                  placeholder="Masukkan alamat domisili...">{{ old('alamat', $buruh->alamat) }}</textarea>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Status Aktif</label>
                        <div class="flex gap-4">
                            <label class="relative flex items-center p-3 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors w-1/2">
                                <input type="radio" name="status" value="aktif" {{ old('status', $buruh->status) == 'aktif' ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <span class="ml-3 flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">Aktif</span>
                                    <span class="block text-xs text-gray-500">Dapat ditugaskan segera</span>
                                </span>
                            </label>
                            <label class="relative flex items-center p-3 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors w-1/2">
                                <input type="radio" name="status" value="non-aktif" {{ old('status', $buruh->status) == 'non-aktif' ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <span class="ml-3 flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">Non-Aktif</span>
                                    <span class="block text-xs text-gray-500">Sedang tidak tersedia</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-10 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('master.buruh.index') }}" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all duration-200">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 focus:ring-4 focus:ring-purple-200 shadow-lg shadow-purple-200 transition-all duration-200">
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
