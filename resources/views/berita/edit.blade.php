@extends('layouts.app')

@section('title', 'Edit Berita / Pamflet')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('berita.index') }}" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Berita / Pamflet</h1>
            <p class="text-gray-500 text-sm mt-0.5 truncate max-w-xs" title="{{ $berita->judul }}">{{ $berita->judul }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('berita.update', $berita->id) }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">
        @csrf
        @method('PUT')

        {{-- Judul --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="judul">Judul <span class="text-red-500">*</span></label>
            <input type="text" id="judul" name="judul" value="{{ old('judul', $berita->judul) }}" required
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('judul') border-red-400 @enderror">
            @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Tipe --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tipe Konten <span class="text-red-500">*</span></label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="tipe" value="berita" {{ old('tipe', $berita->tipe) === 'berita' ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700 font-medium">📰 Berita</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="tipe" value="pamflet" {{ old('tipe', $berita->tipe) === 'pamflet' ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700 font-medium">🖼️ Pamflet</span>
                </label>
            </div>
        </div>

        {{-- Gambar --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="gambar">
                Gambar / Banner
                <span class="text-gray-400 font-normal">(Kosongkan jika tidak diganti)</span>
            </label>
            @if($berita->gambar)
            <div class="mb-3 flex items-start gap-4 p-3 bg-gray-50 rounded-xl border border-gray-200">
                <img src="{{ asset($berita->gambar) }}" alt="Gambar saat ini" class="w-28 h-20 object-cover rounded-lg border border-gray-200 flex-shrink-0">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Gambar saat ini</p>
                    <p class="text-xs text-gray-400 font-mono break-all">{{ $berita->gambar }}</p>
                </div>
            </div>
            @endif
            <div id="drop-zone" class="relative border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-indigo-400 transition-colors cursor-pointer"
                 onclick="document.getElementById('gambar').click()">
                <div id="preview-wrap" class="hidden mb-3">
                    <img id="preview-img" src="#" alt="Preview" class="max-h-40 mx-auto rounded-lg object-contain">
                </div>
                <div id="upload-icon">
                    <svg class="w-8 h-8 mx-auto text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <p class="text-sm text-gray-400">Klik untuk ganti gambar</p>
                </div>
                <input type="file" id="gambar" name="gambar" accept="image/*" class="hidden">
            </div>
        </div>

        {{-- Konten --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="konten">Konten / Deskripsi</label>
            <textarea id="konten" name="konten" rows="6"
                      class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-y">{{ old('konten', $berita->konten) }}</textarea>
        </div>

        {{-- Tanggal Publish --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="published_at">Tanggal Publish</label>
            <input type="datetime-local" id="published_at" name="published_at"
                   value="{{ old('published_at', $berita->published_at?->format('Y-m-d\TH:i')) }}"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
        </div>

        {{-- Opsi --}}
        <div class="flex flex-wrap gap-5 pt-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $berita->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <span class="text-sm text-gray-700 font-medium">Aktif (tampil di PWA)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="pinned" value="1" {{ old('pinned', $berita->pinned) ? 'checked' : '' }}
                       class="w-4 h-4 text-yellow-500 border-gray-300 rounded focus:ring-yellow-400">
                <span class="text-sm text-gray-700 font-medium">📌 Pin (tampil di bagian atas)</span>
            </label>
        </div>

        {{-- Tombol --}}
        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl transition-colors shadow-sm">
                Simpan Perubahan
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
