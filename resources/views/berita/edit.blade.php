@extends('layouts.app')

@section('title', 'Edit Berita / Pamflet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/cropper.min.css') }}">
<style>
    .ratio-btn.active {
        border-color: #4f46e5;
        background-color: #eef2ff;
        color: #4338ca;
        font-weight: 700;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
    }
    .cropper-view-box,
    .cropper-face {
        border-radius: 8px;
    }
</style>
@endpush

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

    <form id="beritaForm" method="POST" action="{{ route('berita.update', $berita->id) }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-6">
        @csrf
        @method('PUT')

        {{-- Hidden Fields untuk Cropper & Rasio --}}
        <input type="hidden" id="aspect_ratio" name="aspect_ratio" value="{{ old('aspect_ratio', $berita->aspect_ratio ?? '2.2:1') }}">
        <input type="hidden" id="gambar_cropped" name="gambar_cropped" value="">

        {{-- Judul --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="judul">Judul <span class="text-red-500">*</span></label>
            <input type="text" id="judul" name="judul" value="{{ old('judul', $berita->judul) }}" required
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('judul') border-red-400 @enderror">
            @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Tipe Konten --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tipe Konten <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-gray-200 hover:border-indigo-300 transition-all cursor-pointer has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/50">
                    <input type="radio" name="tipe" value="berita" {{ old('tipe', $berita->tipe) === 'berita' ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <div>
                        <span class="text-sm font-bold text-gray-800 block">📰 Berita</span>
                        <span class="text-xs text-gray-500">Artikel pengumuman/kabar</span>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-gray-200 hover:border-indigo-300 transition-all cursor-pointer has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/50">
                    <input type="radio" name="tipe" value="pamflet" {{ old('tipe', $berita->tipe) === 'pamflet' ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <div>
                        <span class="text-sm font-bold text-gray-800 block">🖼️ Pamflet</span>
                        <span class="text-xs text-gray-500">Banner visual / poster promo</span>
                    </div>
                </label>
            </div>
        </div>

        {{-- Upload & Pengaturan Ukuran Pamflet/Banner --}}
        <div class="border-t border-gray-100 pt-5 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <label class="block text-sm font-semibold text-gray-800">
                        Gambar / Pamflet Banner
                    </label>
                    <p class="text-xs text-gray-500 mt-0.5">Atur ukuran tampilan atau ganti gambar pamflet banner</p>
                </div>
                <span class="text-xs text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full font-medium">Maks 5MB</span>
            </div>

            {{-- Kartu Gambar Saat Ini --}}
            @if($berita->gambar)
            <div id="current-image-card" class="p-4 bg-gray-50 rounded-2xl border border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <img id="current-image-preview" src="{{ asset($berita->gambar) }}" alt="Gambar saat ini"
                         class="w-24 h-16 object-cover rounded-xl border border-gray-300 shadow-sm flex-shrink-0">
                    <div>
                        <p class="text-xs font-bold text-gray-700">Gambar Banner Aktif</p>
                        <p class="text-[11px] text-gray-400 font-mono break-all mt-0.5">{{ $berita->gambar }}</p>
                        <span class="inline-block mt-1 text-[11px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">
                            Rasio tersimpan: {{ $berita->aspect_ratio ?? '2.2:1' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="button" id="btn-crop-existing"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold transition-colors">
                        ✂️ Atur Ulang Ukuran
                    </button>
                    <button type="button" onclick="document.getElementById('gambar').click()"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-xs font-bold transition-colors">
                        Ganti File
                    </button>
                </div>
            </div>
            @endif

            {{-- Dropzone / Upload Box --}}
            <div id="drop-zone" class="relative border-2 border-dashed border-gray-300 rounded-2xl p-5 text-center hover:border-indigo-400 transition-colors cursor-pointer bg-gray-50/50"
                 onclick="document.getElementById('gambar').click()">
                <div id="upload-icon">
                    <div class="w-10 h-10 mx-auto mb-1.5 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Klik untuk upload gambar baru atau tarik file ke sini</p>
                    <p class="text-xs text-gray-400 mt-0.5">Biarkan kosong jika tidak ingin mengganti file gambar</p>
                </div>
                <input type="file" id="gambar" name="gambar" accept="image/*" class="hidden">
            </div>
            @error('gambar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

            {{-- Panel Pengaturan Ukuran & Pemotong (Cropper Workspace) --}}
            <div id="cropper-container" class="hidden bg-slate-900 rounded-2xl p-4 text-white shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 rounded-lg bg-indigo-600/30 text-indigo-400 text-xs">✂️ Atur Ukuran</span>
                        <h3 class="text-sm font-semibold text-slate-200">Sesuaikan Posisi & Ukuran Pamflet</h3>
                    </div>
                    <span id="dimension-badge" class="text-[11px] font-mono bg-slate-800 text-slate-300 px-2.5 py-1 rounded-full border border-slate-700">0 × 0 px</span>
                </div>

                {{-- Pilihan Aspek Rasio --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Pilih Rasio Banner</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                        <button type="button" class="ratio-btn p-2 rounded-xl border border-slate-700 bg-slate-800 hover:border-indigo-400 text-slate-200 transition-all text-left" data-ratio="2.2" data-key="2.2:1">
                            <span class="block font-bold">2.2 : 1 ⭐</span>
                            <span class="text-[10px] text-slate-400">Banner PWA</span>
                        </button>
                        <button type="button" class="ratio-btn p-2 rounded-xl border border-slate-700 bg-slate-800 hover:border-indigo-400 text-slate-200 transition-all text-left" data-ratio="1.7778" data-key="16:9">
                            <span class="block font-bold">16 : 9</span>
                            <span class="text-[10px] text-slate-400">Widescreen</span>
                        </button>
                        <button type="button" class="ratio-btn p-2 rounded-xl border border-slate-700 bg-slate-800 hover:border-indigo-400 text-slate-200 transition-all text-left" data-ratio="1.3333" data-key="4:3">
                            <span class="block font-bold">4 : 3</span>
                            <span class="text-[10px] text-slate-400">Standar Lega</span>
                        </button>
                        <button type="button" class="ratio-btn p-2 rounded-xl border border-slate-700 bg-slate-800 hover:border-indigo-400 text-slate-200 transition-all text-left" data-ratio="1" data-key="1:1">
                            <span class="block font-bold">1 : 1</span>
                            <span class="text-[10px] text-slate-400">Persegi/Kotak</span>
                        </button>
                        <button type="button" class="ratio-btn p-2 rounded-xl border border-slate-700 bg-slate-800 hover:border-indigo-400 text-slate-200 transition-all text-left" data-ratio="0.8" data-key="4:5">
                            <span class="block font-bold">4 : 5</span>
                            <span class="text-[10px] text-slate-400">Poster Vertikal</span>
                        </button>
                        <button type="button" class="ratio-btn p-2 rounded-xl border border-slate-700 bg-slate-800 hover:border-indigo-400 text-slate-200 transition-all text-left" data-ratio="free" data-key="free">
                            <span class="block font-bold">Bebas</span>
                            <span class="text-[10px] text-slate-400">Custom Crop</span>
                        </button>
                        <button type="button" class="ratio-btn p-2 rounded-xl border border-slate-700 bg-slate-800 hover:border-indigo-400 text-slate-200 transition-all text-left sm:col-span-2" data-ratio="original" data-key="original">
                            <span class="block font-bold">Asli (Tanpa Potong)</span>
                            <span class="text-[10px] text-slate-400">Pertahankan ukuran asli</span>
                        </button>
                    </div>
                </div>

                {{-- Area Canvas Cropper --}}
                <div class="relative bg-slate-950 rounded-xl overflow-hidden border border-slate-800" style="max-height: 380px;">
                    <img id="cropper-image" src="" alt="Cropper" class="max-w-full block" style="max-height: 360px; margin: auto;">
                </div>

                {{-- Toolbar Navigasi Cropper --}}
                <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-800">
                    <div class="flex items-center gap-1.5">
                        <button type="button" id="btn-zoom-in" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200" title="Perbesar">🔍+ Perbesar</button>
                        <button type="button" id="btn-zoom-out" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200" title="Perkecil">🔍- Perkecil</button>
                        <button type="button" id="btn-rotate-left" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200" title="Putar Kiri">↺ 90°</button>
                        <button type="button" id="btn-rotate-right" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200" title="Putar Kanan">↻ 90°</button>
                        <button type="button" id="btn-reset" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200" title="Reset">⟲ Reset</button>
                    </div>
                    <button type="button" id="btn-apply-crop" class="px-4 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white transition-all shadow-md">
                        ✓ Terapkan Ukuran
                    </button>
                </div>
            </div>

            {{-- Live Mockup Hasil Tampilan Banner di PWA --}}
            <div id="preview-mockup-wrap" class="hidden bg-gray-50 border border-gray-200 rounded-2xl p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-bold text-gray-700">Simulasi Tampilan Banner di Dashboard PWA</span>
                    </div>
                    <button type="button" id="btn-edit-crop" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Ubah Ukuran/Potongan
                    </button>
                </div>

                {{-- Banner Mockup Box --}}
                <div class="relative w-full rounded-2xl overflow-hidden border border-gray-300 shadow-md bg-slate-900" id="banner-mockup-frame" style="aspect-ratio: 2.2 / 1;">
                    <img id="preview-final-img" src="#" alt="Hasil Crop" class="w-full h-full object-cover block">
                    <div class="absolute bottom-2.5 right-3 bg-black/60 backdrop-blur-md text-white/90 text-[10px] font-semibold px-2.5 py-0.5 rounded-full pointer-events-none">
                        Rasio: <span id="preview-ratio-label">2.2 : 1</span>
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 text-center">Gambar di atas adalah tampilan persis pamflet yang akan dilihat karyawan di dashboard aplikasi.</p>
            </div>
        </div>

        {{-- Konten --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="konten">Konten / Deskripsi</label>
            <textarea id="konten" name="konten" rows="5"
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
            <button type="submit" id="btn-submit"
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
<script src="{{ asset('js/cropper.min.js') }}"></script>
<script>
let cropper = null;
let currentRatioKey = "{{ old('aspect_ratio', $berita->aspect_ratio ?? '2.2:1') }}";
let currentRatio = 2.2;
let isOriginalMode = currentRatioKey === 'original';
let rawOriginalSrc = null;

const inputGambar       = document.getElementById('gambar');
const cropperContainer  = document.getElementById('cropper-container');
const cropperImage      = document.getElementById('cropper-image');
const mockupWrap        = document.getElementById('preview-mockup-wrap');
const previewFinalImg   = document.getElementById('preview-final-img');
const bannerMockupFrame = document.getElementById('banner-mockup-frame');
const previewRatioLabel = document.getElementById('preview-ratio-label');
const dimensionBadge    = document.getElementById('dimension-badge');
const inputAspectRatio  = document.getElementById('aspect_ratio');
const inputCropped      = document.getElementById('gambar_cropped');
const dropZone          = document.getElementById('drop-zone');
const btnCropExisting   = document.getElementById('btn-crop-existing');

// Mapping rasio
const RATIO_MAP = {
    '2.2:1':    { key: '2.2:1', label: '2.2 : 1 (Banner PWA)', val: 2.2,    cssRatio: '2.2 / 1', dataRatio: '2.2' },
    '16:9':     { key: '16:9',  label: '16 : 9 (Widescreen)',  val: 16 / 9, cssRatio: '16 / 9',  dataRatio: '1.7778' },
    '4:3':      { key: '4:3',   label: '4 : 3 (Standar)',      val: 4 / 3,  cssRatio: '4 / 3',   dataRatio: '1.3333' },
    '1:1':      { key: '1:1',   label: '1 : 1 (Persegi)',      val: 1,      cssRatio: '1 / 1',   dataRatio: '1' },
    '4:5':      { key: '4:5',   label: '4 : 5 (Poster)',       val: 0.8,    cssRatio: '4 / 5',   dataRatio: '0.8' },
    'free':     { key: 'free',  label: 'Bebas (Custom)',       val: NaN,    cssRatio: '2.2 / 1', dataRatio: 'free' },
    'original': { key: 'original', label: 'Asli (Tanpa Potong)', val: null, cssRatio: 'auto',   dataRatio: 'original' },
};

// Set nilai rasio awal sesuai data tersimpan
if (RATIO_MAP[currentRatioKey]) {
    currentRatio = RATIO_MAP[currentRatioKey].val;
    highlightRatioButton(currentRatioKey);
}

function highlightRatioButton(key) {
    document.querySelectorAll('.ratio-btn').forEach(b => {
        if (b.dataset.key === key) {
            b.classList.add('active');
        } else {
            b.classList.remove('active');
        }
    });
}

function initCropper(imageSrc) {
    rawOriginalSrc = imageSrc;
    cropperImage.src = imageSrc;
    cropperContainer.classList.remove('hidden');

    if (cropper) {
        cropper.destroy();
    }

    cropper = new Cropper(cropperImage, {
        aspectRatio: isNaN(currentRatio) ? NaN : (currentRatio || 2.2),
        viewMode: 1,
        autoCropArea: 0.95,
        responsive: true,
        background: false,
        zoomable: true,
        crop(e) {
            const data = e.detail;
            dimensionBadge.textContent = `${Math.round(data.width)} × ${Math.round(data.height)} px`;
        },
        ready() {
            updateCropPreview();
        }
    });
}

function updateCropPreview() {
    if (!cropper && !isOriginalMode) return;

    if (isOriginalMode) {
        previewFinalImg.src = rawOriginalSrc;
        bannerMockupFrame.style.aspectRatio = 'auto';
        bannerMockupFrame.style.maxHeight = '300px';
        previewRatioLabel.textContent = 'Ukuran Asli';
        inputAspectRatio.value = 'original';
        inputCropped.value = '';
        mockupWrap.classList.remove('hidden');
        return;
    }

    const canvas = cropper.getCroppedCanvas({
        maxWidth: 1600,
        maxHeight: 1600,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
    });

    if (canvas) {
        const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.92);
        previewFinalImg.src = croppedDataUrl;
        inputCropped.value = croppedDataUrl;

        // Atur frame mockup
        const ratioConfig = RATIO_MAP[currentRatioKey] || RATIO_MAP['2.2:1'];
        bannerMockupFrame.style.aspectRatio = ratioConfig.cssRatio;
        bannerMockupFrame.style.maxHeight = '320px';
        previewRatioLabel.textContent = ratioConfig.label;
        inputAspectRatio.value = ratioConfig.key;

        mockupWrap.classList.remove('hidden');
    }
}

// Tombol atur ulang gambar saat ini
if (btnCropExisting) {
    btnCropExisting.addEventListener('click', function() {
        const existingUrl = "{{ asset($berita->gambar) }}";
        initCropper(existingUrl);
        cropperContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
}

// Handler pemilihan file baru
inputGambar.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = ev => {
            initCropper(ev.target.result);
        };
        reader.readAsDataURL(file);
    }
});

// Drag & drop dropzone
['dragenter', 'dragover'].forEach(name => {
    dropZone.addEventListener(name, (e) => { e.preventDefault(); dropZone.classList.add('border-indigo-500', 'bg-indigo-50/50'); });
});
['dragleave', 'drop'].forEach(name => {
    dropZone.addEventListener(name, (e) => { e.preventDefault(); dropZone.classList.remove('border-indigo-500', 'bg-indigo-50/50'); });
});
dropZone.addEventListener('drop', (e) => {
    const dt = e.dataTransfer;
    const file = dt?.files[0];
    if (file && file.type.startsWith('image/')) {
        inputGambar.files = dt.files;
        const reader = new FileReader();
        reader.onload = ev => initCropper(ev.target.result);
        reader.readAsDataURL(file);
    }
});

// Tombol pemilihan rasio
document.querySelectorAll('.ratio-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.ratio-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const ratioKey = this.dataset.key;
        currentRatioKey = ratioKey;

        if (ratioKey === 'original') {
            isOriginalMode = true;
            if (cropper) {
                cropper.clear();
            }
            updateCropPreview();
            return;
        }

        isOriginalMode = false;
        if (!cropper) return;

        cropper.crop();
        if (ratioKey === 'free') {
            currentRatio = NaN;
            cropper.setAspectRatio(NaN);
        } else {
            const config = RATIO_MAP[ratioKey];
            currentRatio = config ? config.val : 2.2;
            cropper.setAspectRatio(currentRatio);
        }
        updateCropPreview();
    });
});

// Toolbar cropper
document.getElementById('btn-zoom-in')?.addEventListener('click', () => cropper?.zoom(0.1));
document.getElementById('btn-zoom-out')?.addEventListener('click', () => cropper?.zoom(-0.1));
document.getElementById('btn-rotate-left')?.addEventListener('click', () => cropper?.rotate(-90));
document.getElementById('btn-rotate-right')?.addEventListener('click', () => cropper?.rotate(90));
document.getElementById('btn-reset')?.addEventListener('click', () => cropper?.reset());

// Terapkan crop
document.getElementById('btn-apply-crop')?.addEventListener('click', () => {
    updateCropPreview();
    cropperContainer.classList.add('hidden');
    mockupWrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
});

// Buka kembali cropper dari mockup
document.getElementById('btn-edit-crop')?.addEventListener('click', () => {
    cropperContainer.classList.remove('hidden');
    cropperContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
});

// Sebelum submit, pastikan data crop mutakhir jika cropper aktif
document.getElementById('beritaForm').addEventListener('submit', function() {
    if (cropper && !isOriginalMode) {
        updateCropPreview();
    }
});
</script>
@endpush
