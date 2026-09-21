@extends('layouts.app')

@section('title', 'Tambah Berita / Pamflet')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('berita.index') }}" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tambah Berita / Pamflet</h1>
            <p class="text-gray-500 text-sm mt-0.5">Konten akan tampil di halaman PWA karyawan</p>
        </div>
    </div>

    <form method="POST" action="{{ route('berita.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">
        @csrf

        {{-- Judul --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="judul">Judul <span class="text-red-500">*</span></label>
            <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required
                   placeholder="Tulis judul berita atau pamflet..."
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('judul') border-red-400 @enderror">
            @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Tipe --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tipe Konten <span class="text-red-500">*</span></label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="tipe" value="berita" {{ old('tipe', 'berita') === 'berita' ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700 font-medium">📰 Berita</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="tipe" value="pamflet" {{ old('tipe') === 'pamflet' ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700 font-medium">🖼️ Pamflet</span>
                </label>
            </div>
        </div>

        {{-- Gambar --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="gambar">
                Gambar / Banner
                <span class="text-gray-400 font-normal">(JPG, PNG, WebP · Maks 5MB)</span>
            </label>
            <div id="drop-zone" class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-indigo-400 transition-colors cursor-pointer"
                 onclick="document.getElementById('gambar').click()">
                <div id="preview-wrap" class="hidden mb-3">
                    <img id="preview-img" src="#" alt="Preview" class="max-h-48 mx-auto rounded-lg object-contain">
                </div>
                <div id="upload-icon">
                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-sm text-gray-500">Klik untuk upload atau drag & drop gambar</p>
                </div>
                <input type="file" id="gambar" name="gambar" accept="image/*" class="hidden">
            </div>
            @error('gambar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Konten --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="konten">Konten / Deskripsi</label>
            <textarea id="konten" name="konten" rows="6" placeholder="Tulis isi berita atau keterangan pamflet..."
                      class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-y">{{ old('konten') }}</textarea>
        </div>

        {{-- Tanggal Publish --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="published_at">Tanggal Publish</label>
            <input type="datetime-local" id="published_at" name="published_at"
                   value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            <p class="text-gray-400 text-xs mt-1">Kosongkan untuk langsung publish sekarang</p>
        </div>

        {{-- Opsi --}}
        <div class="flex flex-wrap gap-5 pt-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <span class="text-sm text-gray-700 font-medium">Aktif (tampil di PWA)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="pinned" value="1" {{ old('pinned') ? 'checked' : '' }}
                       class="w-4 h-4 text-yellow-500 border-gray-300 rounded focus:ring-yellow-400">
                <span class="text-sm text-gray-700 font-medium">📌 Pin (tampil di bagian atas)</span>
            </label>
        </div>

        {{-- Tombol --}}
        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl transition-colors shadow-sm">
                Simpan
            </button>
            <a href="{{ route('berita.index') }}"
               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Preview gambar
document.getElementById('gambar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = ev => {
            document.getElementById('preview-img').src = ev.target.result;
            document.getElementById('preview-wrap').classList.remove('hidden');
            document.getElementById('upload-icon').classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
