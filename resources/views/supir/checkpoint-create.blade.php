<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Update Checkpoint - AYPSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 sm:px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Update Checkpoint</h1>
            <div class="flex items-center space-x-4">
                <span class="hidden sm:block text-gray-600">Halo, {{ Auth::user()->name }}!</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="container mx-auto mt-8 px-4 sm:px-6 flex-grow">
        <div class="space-y-6">
            {{-- Notifikasi Sukses --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            {{-- Notifikasi Error --}}
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            {{-- Notifikasi Validasi --}}
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Detail Permohonan atau Surat Jalan --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                @if(isset($permohonan))
                    {{-- Detail Permohonan --}}
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4">Memo: {{ $permohonan->nomor_memo }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="font-medium text-gray-500">Tujuan</p>
                            <p class="text-gray-800">{{ $permohonan->tujuan }}</p>
                        </div>
                        <div>
                            @php
                                $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $permohonan->kegiatan)
                                                ->value('nama_kegiatan') ?? ucfirst($permohonan->kegiatan);
                            @endphp
                            <p class="font-medium text-gray-500">Kegiatan</p>
                            <p class="text-gray-800">{{ $kegiatanName }}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">Ukuran</p>
                            <p class="text-gray-800">{{ $permohonan->ukuran }} ft</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">Jumlah Kontainer</p>
                            <p class="text-gray-800">{{ $permohonan->jumlah_kontainer }}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">Vendor</p>
                            <p class="text-gray-800">{{ $permohonan->vendor_perusahaan }}</p>
                        </div>
                    </div>
                @elseif(isset($suratJalan))
                    {{-- Detail Surat Jalan --}}
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4">Surat Jalan: {{ $suratJalan->no_surat_jalan }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="font-medium text-gray-500">Tanggal</p>
                            <p class="text-gray-800">{{ $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}</p>
                        </div>
                        <div>
                            @php
                                $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $suratJalan->kegiatan)
                                                ->value('nama_kegiatan') ?? ucfirst($suratJalan->kegiatan);
                            @endphp
                            <p class="font-medium text-gray-500">Kegiatan</p>
                            <p class="text-gray-800">{{ $kegiatanName }}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">Ukuran</p>
                            <p class="text-gray-800">{{ $suratJalan->size }} ft</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">Jumlah Kontainer</p>
                            <p class="text-gray-800">{{ $suratJalan->jumlah_kontainer }}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">Tujuan Pengiriman</p>
                            <p class="text-gray-800">{{ $suratJalan->tujuan_pengiriman ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">Pengirim</p>
                            <p class="text-gray-800">{{ $suratJalan->pengirim ?? '-' }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Form Input Checkpoint --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Checkpoint Supir</h3>

                @if(isset($permohonan))
                    <form action="{{ route('supir.checkpoint.store', $permohonan) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            {{-- Logika untuk menampilkan input atau daftar kontainer --}}
                            @if($permohonan->kontainers->isNotEmpty())
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nomor Kontainer (Ditugaskan)</label>
                                    <div class="mt-1 space-y-2">
                                        @foreach($permohonan->kontainers as $kontainer)
                                            <div class="block w-full rounded-md border-gray-300 bg-gray-200 shadow-sm p-2.5">
                                                {{ $kontainer->nomor_seri_gabungan }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Kontainer sudah diinput.</p>
                                </div>
                            @else
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Pilih Nomor Kontainer</label>
                                    <div>
                                        <label for="tanggal_checkpoint" class="block text-sm font-medium text-gray-700">Tanggal Checkpoint</label>
                                        <input type="date" id="tanggal_checkpoint" name="tanggal_checkpoint" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" value="{{ date('Y-m-d') }}" required>
                                    </div>

                                    {{-- Existing complex form logic for permohonan --}}
                                    @include('supir.checkpoint-permohonan-form')
                                </div>
                            @endif

                            {{-- Common fields --}}
                            <div>
                                <label for="no_seal_permohonan" class="block text-sm font-medium text-gray-700">No. Seal</label>
                                <input type="text" id="no_seal_permohonan" name="no_seal" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" placeholder="Masukkan nomor seal kontainer">
                            </div>
                            <div>
                                <label for="surat_jalan_vendor" class="block text-sm font-medium text-gray-700">Surat Jalan Vendor</label>
                                <input type="text" id="surat_jalan_vendor" name="surat_jalan_vendor" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" placeholder="Masukkan nomor surat jalan vendor">
                            </div>
                            <div>
                                <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan / Keterangan</label>
                                <textarea id="catatan" name="catatan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5"></textarea>
                            </div>

                            {{-- Upload Gambar untuk Permohonan --}}
                            <div>
                                <label for="gambar_permohonan" class="block text-sm font-medium text-gray-700">Upload Gambar / Dokumen</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-400 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="gambar_permohonan" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Upload file</span>
                                                <input id="gambar_permohonan" name="gambar" type="file" class="sr-only" accept="image/*,application/pdf" onchange="previewFilePermohonan(this)">
                                            </label>
                                            <p class="pl-1">atau drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF, PDF hingga 5MB</p>
                                    </div>
                                </div>

                                {{-- Preview Area untuk Permohonan --}}
                                <div id="file-preview-permohonan" class="mt-3 hidden">
                                    <div class="flex items-center p-3 bg-gray-50 rounded-md">
                                        <svg class="h-8 w-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900" id="file-name-permohonan"></p>
                                            <p class="text-xs text-gray-500" id="file-size-permohonan"></p>
                                        </div>
                                        <button type="button" onclick="removeFilePermohonan()" class="ml-3 text-red-400 hover:text-red-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                @error('gambar')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Simpan Checkpoint
                                </button>
                            </div>
                        </div>
                    </form>

                @elseif(isset($suratJalan))
                    <form action="{{ route('supir.checkpoint.store-surat-jalan', $suratJalan->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            {{-- Simple form for surat jalan --}}
                            <div>
                                <label for="tanggal_checkpoint" class="block text-sm font-medium text-gray-700">Tanggal Checkpoint</label>
                                <input type="date" id="tanggal_checkpoint" name="tanggal_checkpoint" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" value="{{ date('Y-m-d') }}" required>
                            </div>

                            {{-- Container inputs for surat jalan --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Pilih Nomor Kontainer ({{ $suratJalan->size }}ft)</label>
                                @for ($i = 0; $i < $suratJalan->jumlah_kontainer; $i++)
                                    <div class="relative mt-1">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Kontainer #{{ $i + 1 }}</label>
                                        <select name="nomor_kontainer[]" class="select-kontainer block w-full rounded-lg border border-indigo-300 bg-white shadow focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition p-2.5 pr-10" required>
                                            <option value="">-- Pilih Kontainer #{{ $i + 1 }} --</option>
                                            @if(isset($stockKontainers) && $stockKontainers->isNotEmpty())
                                                @foreach($stockKontainers as $stock)
                                                    <option value="{{ $stock->nomor_seri_gabungan }}">{{ $stock->nomor_seri_gabungan }} - {{ $stock->ukuran }}ft ({{ ucfirst($stock->status) }})</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                @endfor
                                <p class="text-xs text-gray-500 mt-1">Pilih nomor kontainer ukuran {{ $suratJalan->size }}ft sesuai jumlah di surat jalan.</p>
                            </div>

                            {{-- Common fields --}}
                            <div>
                                <label for="no_seal_surat_jalan" class="block text-sm font-medium text-gray-700">No. Seal</label>
                                <input type="text" id="no_seal_surat_jalan" name="no_seal" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" placeholder="Masukkan nomor seal kontainer">
                            </div>
                            <div>
                                <label for="surat_jalan_vendor" class="block text-sm font-medium text-gray-700">Surat Jalan Vendor</label>
                                <input type="text" id="surat_jalan_vendor" name="surat_jalan_vendor" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" placeholder="Masukkan nomor surat jalan vendor">
                            </div>
                            <div>
                                <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan / Keterangan</label>
                                <textarea id="catatan" name="catatan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5"></textarea>
                            </div>

                            {{-- Upload Gambar untuk Surat Jalan --}}
                            <div>
                                <label for="gambar_surat_jalan" class="block text-sm font-medium text-gray-700">Upload Gambar / Dokumen</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-400 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="gambar_surat_jalan" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Upload file</span>
                                                <input id="gambar_surat_jalan" name="gambar" type="file" class="sr-only" accept="image/*,application/pdf" onchange="previewFileSuratJalan(this)">
                                            </label>
                                            <p class="pl-1">atau drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF, PDF hingga 5MB</p>
                                    </div>
                                </div>

                                {{-- Preview Area untuk Surat Jalan --}}
                                <div id="file-preview-surat-jalan" class="mt-3 hidden">
                                    <div class="flex items-center p-3 bg-gray-50 rounded-md">
                                        <svg class="h-8 w-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900" id="file-name-surat-jalan"></p>
                                            <p class="text-xs text-gray-500" id="file-size-surat-jalan"></p>
                                        </div>
                                        <button type="button" onclick="removeFileSuratJalan()" class="ml-3 text-red-400 hover:text-red-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                @error('gambar')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Simpan Checkpoint
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>

            {{-- Riwayat Checkpoint --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Checkpoint</h3>
                <div class="space-y-4">
                    @if(isset($permohonan))
                        @forelse($permohonan->checkpoints as $checkpoint)
                            <div class="border-l-4 pl-4 {{ $loop->first ? 'border-indigo-500' : 'border-gray-300' }}">
                                <p class="font-semibold text-gray-800">{{ $checkpoint->catatan }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $checkpoint->created_at->format('d M Y, H:i') }} - {{ $checkpoint->lokasi ?? 'Lokasi tidak diketahui' }}</p>
                                @if($checkpoint->gambar)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $checkpoint->gambar) }}" target="_blank" class="inline-flex items-center text-xs text-indigo-600 hover:text-indigo-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Lihat Gambar
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada riwayat checkpoint.</p>
                        @endforelse
                    @elseif(isset($suratJalan))
                        @if(in_array($suratJalan->status, ['checkpoint_completed', 'sudah_checkpoint']) && $suratJalan->no_kontainer)
                            <div class="border-l-4 pl-4 border-indigo-500">
                                <p class="font-semibold text-gray-800">Checkpoint Selesai - Sedang Menunggu Approval</p>
                                <p class="text-sm text-gray-600 mt-1">Nomor Kontainer: {{ $suratJalan->no_kontainer }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $suratJalan->updated_at->format('d M Y, H:i') }}</p>
                                @if($suratJalan->gambar_checkpoint)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $suratJalan->gambar_checkpoint) }}" target="_blank" class="inline-flex items-center text-xs text-indigo-600 hover:text-indigo-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Lihat Gambar
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Belum ada riwayat checkpoint.</p>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Tombol Kembali --}}
            <div class="pt-4">
                <a href="{{ route('supir.dashboard') }}" class="w-full sm:w-auto inline-flex justify-center py-2 px-6 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Kembali ke Daftar Tugas
                </a>
            </div>
        </div>
    </main>
</body>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi Select2 pada dropdown kontainer biasa
            $('select.select-kontainer').each(function() {
                $(this).select2({
                    placeholder: 'Cari nomor kontainer',
                    width: '100%'
                });
            });

            // Inisialisasi Select2 dengan tags pada dropdown antar kontainer perbaikan
            $('select.select-kontainer-perbaikan').each(function() {
                $(this).select2({
                    placeholder: 'Pilih atau ketik nomor kontainer',
                    width: '100%',
                    tags: true,
                    tokenSeparators: [',', ' '],
                    createTag: function (params) {
                        var term = $.trim(params.term);
                        if (term === '') {
                            return null;
                        }
                        return {
                            id: term,
                            text: term,
                            newTag: true
                        }
                    }
                });
            });
        });

        // Preview file function untuk Permohonan
        function previewFilePermohonan(input) {
            const file = input.files[0];
            if (file) {
                const fileName = file.name;
                const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';

                document.getElementById('file-name-permohonan').textContent = fileName;
                document.getElementById('file-size-permohonan').textContent = fileSize;
                document.getElementById('file-preview-permohonan').classList.remove('hidden');
            }
        }

        // Remove file function untuk Permohonan
        function removeFilePermohonan() {
            document.getElementById('gambar_permohonan').value = '';
            document.getElementById('file-preview-permohonan').classList.add('hidden');
        }

        // Preview file function untuk Surat Jalan
        function previewFileSuratJalan(input) {
            const file = input.files[0];
            if (file) {
                const fileName = file.name;
                const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';

                document.getElementById('file-name-surat-jalan').textContent = fileName;
                document.getElementById('file-size-surat-jalan').textContent = fileSize;
                document.getElementById('file-preview-surat-jalan').classList.remove('hidden');
            }
        }

        // Remove file function untuk Surat Jalan
        function removeFileSuratJalan() {
            document.getElementById('gambar_surat_jalan').value = '';
            document.getElementById('file-preview-surat-jalan').classList.add('hidden');
        }

        // Drag and drop functionality
        document.addEventListener('DOMContentLoaded', function() {
            const dropZones = document.querySelectorAll('[class*="border-dashed"]');

            dropZones.forEach(dropZone => {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
                    });
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
                    });
                });

                dropZone.addEventListener('drop', (e) => {
                    const files = e.dataTransfer.files;
                    const fileInput = dropZone.querySelector('input[type="file"]');

                    if (files.length > 0 && fileInput) {
                        fileInput.files = files;

                        // Trigger preview based on input ID
                        if (fileInput.id === 'gambar_permohonan') {
                            previewFilePermohonan(fileInput);
                        } else if (fileInput.id === 'gambar_surat_jalan') {
                            previewFileSuratJalan(fileInput);
                        }
                    }
                });
            });
        });
    </script>
</html>
