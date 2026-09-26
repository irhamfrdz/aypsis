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
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                Gambar / Banner
                <span class="text-gray-400 font-normal">(Kosongkan jika tidak diganti · Maks 5MB)</span>
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

            <input type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/webp,image/jpg" class="hidden">

            <label for="gambar" id="drop-zone" class="relative block border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-indigo-400 hover:bg-gray-50/50 transition-colors cursor-pointer">
                <div id="preview-wrap" class="hidden mb-3">
                    <img id="preview-img" src="#" alt="Preview" class="max-h-48 mx-auto rounded-lg object-contain shadow-sm border border-gray-100">
                    <div class="mt-2.5 flex items-center justify-center gap-2">
                        <span id="file-info" class="text-xs text-gray-600 font-medium bg-gray-100 px-2.5 py-1 rounded-md"></span>
                        <button type="button" id="btn-remove-gambar" class="text-xs text-red-600 hover:text-red-700 font-semibold px-2.5 py-1 rounded-md bg-red-50 hover:bg-red-100 transition-colors">Batal Ganti</button>
                    </div>
                </div>
                <div id="upload-icon">
                    <svg class="w-8 h-8 mx-auto text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <p class="text-sm text-gray-600 font-medium">Klik untuk ganti gambar atau seret file ke sini</p>
                    <p class="text-xs text-gray-400 mt-0.5">Format: JPG, PNG, WebP (Maks. 5MB)</p>
                </div>
            </label>
            <div id="client-error" class="hidden text-red-500 text-xs mt-1.5 font-medium"></div>
            @error('gambar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
const gambarInput = document.getElementById('gambar');
const dropZone = document.getElementById('drop-zone');
const previewWrap = document.getElementById('preview-wrap');
const previewImg = document.getElementById('preview-img');
const uploadIcon = document.getElementById('upload-icon');
const fileInfo = document.getElementById('file-info');
const btnRemove = document.getElementById('btn-remove-gambar');
const clientError = document.getElementById('client-error');

function showFile(file) {
    clientError.classList.add('hidden');
    clientError.textContent = '';

    if (!file) return;

    if (!file.type.startsWith('image/')) {
        clientError.textContent = 'File yang dipilih bukan gambar valid (JPG, PNG, WebP).';
        clientError.classList.remove('hidden');
        resetInput();
        return;
    }

    if (file.size > 5 * 1024 * 1024) {
        clientError.textContent = 'Ukuran file (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB) melebihi batas maksimal 5 MB.';
        clientError.classList.remove('hidden');
        resetInput();
        return;
    }

    const reader = new FileReader();
    reader.onload = ev => {
        previewImg.src = ev.target.result;
        previewWrap.classList.remove('hidden');
        uploadIcon.classList.add('hidden');
        const sizeFormatted = file.size > 1024 * 1024 
            ? (file.size / (1024 * 1024)).toFixed(2) + ' MB'
            : (file.size / 1024).toFixed(1) + ' KB';
        fileInfo.textContent = file.name + ' (' + sizeFormatted + ')';
    };
    reader.readAsDataURL(file);
}

function resetInput() {
    gambarInput.value = '';
    previewImg.src = '#';
    previewWrap.classList.add('hidden');
    uploadIcon.classList.remove('hidden');
    fileInfo.textContent = '';
}

gambarInput.addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        showFile(e.target.files[0]);
    }
});

btnRemove.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    resetInput();
});

// Drag & drop handlers
['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.add('border-indigo-500', 'bg-indigo-50/40');
    });
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('border-indigo-500', 'bg-indigo-50/40');
    });
});

dropZone.addEventListener('drop', function(e) {
    const dt = e.dataTransfer;
    if (dt && dt.files && dt.files.length > 0) {
        gambarInput.files = dt.files;
        showFile(dt.files[0]);
    }
});
</script>
@endpush
