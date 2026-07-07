<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lapor Lokasi & Foto Bukti - AYPSIS</title>
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
            <h1 class="text-2xl font-bold text-gray-800">Lapor Lokasi / Checkpoint</h1>
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
                            <p class="font-medium text-gray-500">Tipe</p>
                            <p class="text-gray-800">{{ strtoupper($permohonan->tipe ?? 'FCL') }}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">Jumlah Kontainer</p>
                            <p class="text-gray-800">{{ $permohonan->jumlah_kontainer }}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">Vendor</p>
                            <p class="text-gray-800">{{ $permohonan->vendor_perusahaan }}</p>
                        </div>
                        @if($permohonan->tujuan_pengambilan ?? false)
                        <div>
                            <p class="font-medium text-gray-500">Tujuan Pengambilan</p>
                            <p class="text-gray-800">{{ $permohonan->tujuan_pengambilan }}</p>
                        </div>
                        @endif
                        @if($permohonan->tujuan_pengiriman ?? false)
                        <div>
                            <p class="font-medium text-gray-500">Tujuan Pengiriman</p>
                            <p class="text-gray-800">{{ $permohonan->tujuan_pengiriman }}</p>
                        </div>
                        @endif
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
                            <p class="font-medium text-gray-500">Tujuan Pengambilan</p>
                            <p class="text-gray-800">{{ $suratJalan->tujuan_pengambilan ?? $suratJalan->order->tujuan_ambil ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">Tujuan Pengiriman</p>
                            <p class="text-gray-800">{{ $suratJalan->tujuan_pengiriman ?? $suratJalan->order->tujuan_kirim ?? '-' }}</p>
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
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Form Laporan Supir</h3>

                @if(isset($permohonan))
                    <form action="{{ route('supir.checkpoint.store', $permohonan) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            {{-- Logika untuk menampilkan input atau daftar kontainer --}}
                            @if($permohonan->kontainers->isNotEmpty())
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nomor Kontainer (Yang Ditugaskan)</label>
                                    <div class="mt-1 space-y-2">
                                        @foreach($permohonan->kontainers as $kontainer)
                                            <div class="block w-full rounded-md border-gray-300 bg-gray-200 shadow-sm p-2.5">
                                                {{ $kontainer->nomor_seri_gabungan }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Nomor kontainer sudah tercatat.</p>
                                </div>
                            @else
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Pilih / Masukkan Nomor Kontainer</label>
                                    <div>
                                        <label for="tanggal_checkpoint" class="block text-sm font-medium text-gray-700">Tanggal Lapor</label>
                                        <input type="date" id="tanggal_checkpoint" name="tanggal_checkpoint" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" value="{{ date('Y-m-d') }}" required>
                                    </div>

                                    <div>
                                        <label for="gudang_tujuan_id" class="block text-sm font-medium text-gray-700">Gudang Tujuan (Tempat Kirim/Bongkar)</label>
                                        <select id="gudang_tujuan_id" name="gudang_tujuan_id" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" required>
                                            <option value="">-- Pilih Gudang Tujuan --</option>
                                            @if(isset($gudangs))
                                                @foreach($gudangs as $gudang)
                                                    <option value="{{ $gudang->id }}">{{ $gudang->nama_gudang }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">Pilih tujuan tempat bongkar/kirim kontainer ini.</p>
                                    </div>

                                    {{-- Existing complex form logic for permohonan --}}
                                    @include('supir.checkpoint-permohonan-form')
                                </div>
                            @endif

                            {{-- Common fields --}}
                            <div id="no_seal_section_permohonan">
                                <label class="block text-sm font-medium text-gray-700">Nomor Seal (Gembok)</label>
                                @for ($i = 0; $i < $permohonan->jumlah_kontainer; $i++)
                                    <div class="relative mt-1">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Nomor Seal Kontainer #{{ $i + 1 }}</label>
                                        <input type="text" 
                                               name="no_seal[]" 
                                               class="block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" 
                                               placeholder="Masukkan nomor gembok/seal kontainer #{{ $i + 1 }}">
                                    </div>
                                @endfor
                                <p class="text-xs text-gray-500 mt-1">Masukkan nomor seal/gembok untuk setiap kontainer (sesuai jumlah {{ $permohonan->jumlah_kontainer }} kontainer).</p>
                            </div>
                            <div>
                                <label for="surat_jalan_vendor" class="block text-sm font-medium text-gray-700">Nomor Surat Jalan Vendor (Jika Ada)</label>
                                <input type="text" id="surat_jalan_vendor" name="surat_jalan_vendor" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" placeholder="Masukkan nomor surat jalan dari vendor">
                            </div>
                            <div>
                                <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan Tambahan (Kondisi Jalan/Barang)</label>
                                <textarea id="catatan" name="catatan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" placeholder="Tulis info penting di sini jika ada..."></textarea>
                            </div>

                            {{-- Upload Gambar untuk Permohonan --}}
                            <div>
                                <h3 class="block text-sm font-bold text-gray-800 mb-2">Foto Surat Jalan / DO (Bisa Banyak)</h3>
                                <label for="gambar_permohonan" class="cursor-pointer block mt-1 flex justify-center px-6 pt-10 pb-10 border-2 border-indigo-300 border-dashed rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-colors bg-white shadow-sm">
                                    <div class="space-y-3 text-center">
                                        <svg class="mx-auto h-16 w-16 text-indigo-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <span class="relative font-bold text-indigo-600 text-lg">
                                                Ketuk untuk Ambil Foto
                                                <input id="gambar_permohonan" name="gambar[]" type="file" class="sr-only" accept="image/*,application/pdf" multiple capture="environment" onchange="previewFilePermohonan(this)">
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 font-medium mt-2">Bisa pilih/foto beberapa gambar sekaligus</p>
                                    </div>
                                </label>

                                {{-- Preview Area untuk Permohonan --}}
                                <div id="file-preview-permohonan" class="mt-3 hidden">
                                    <div class="space-y-2" id="preview-list-permohonan">
                                        <!-- Preview items will be added here -->
                                    </div>
                                </div>

                                @error('gambar')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('gambar.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Upload Bukti Muat untuk Permohonan --}}
                            <div>
                                <h3 class="block text-sm font-bold text-gray-800 mb-2">Foto Bukti Muat (Bisa Banyak)</h3>
                                <label for="bukti_muat_permohonan" class="cursor-pointer block mt-1 flex justify-center px-6 pt-10 pb-10 border-2 border-indigo-300 border-dashed rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-colors bg-white shadow-sm">
                                    <div class="space-y-3 text-center">
                                        <svg class="mx-auto h-16 w-16 text-indigo-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <span class="relative font-bold text-indigo-600 text-lg">
                                                Ketuk untuk Ambil Foto
                                                <input id="bukti_muat_permohonan" name="bukti_muat[]" type="file" class="sr-only" accept="image/*,application/pdf" multiple capture="environment" onchange="previewBuktiMuatPermohonan(this)">
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 font-medium mt-2">Bisa pilih/foto beberapa gambar sekaligus</p>
                                    </div>
                                </label>

                                {{-- Preview Area untuk Bukti Muat Permohonan --}}
                                <div id="file-preview-bukti-muat-permohonan" class="mt-3 hidden">
                                    <div class="space-y-2" id="preview-list-bukti-muat-permohonan">
                                        <!-- Preview items will be added here -->
                                    </div>
                                </div>

                                @error('bukti_muat')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('bukti_muat.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Upload Bukti Timbangan Muat untuk Permohonan --}}
                            <div>
                                <h3 class="block text-sm font-bold text-gray-800 mb-2">Foto Bukti Timbangan Muat (Bisa Banyak)</h3>
                                <label for="bukti_timbangan_muat_permohonan" class="cursor-pointer block mt-1 flex justify-center px-6 pt-10 pb-10 border-2 border-indigo-300 border-dashed rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-colors bg-white shadow-sm">
                                    <div class="space-y-3 text-center">
                                        <svg class="mx-auto h-16 w-16 text-indigo-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <span class="relative font-bold text-indigo-600 text-lg">
                                                Ketuk untuk Ambil Foto
                                                <input id="bukti_timbangan_muat_permohonan" name="bukti_timbangan_muat[]" type="file" class="sr-only" accept="image/*,application/pdf" multiple capture="environment" onchange="previewBuktiTimbanganMuatPermohonan(this)">
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 font-medium mt-2">Bisa pilih/foto beberapa gambar sekaligus</p>
                                    </div>
                                </label>

                                {{-- Preview Area untuk Bukti Timbangan Muat Permohonan --}}
                                <div id="file-preview-bukti-timbangan-muat-permohonan" class="mt-3 hidden">
                                    <div class="space-y-2" id="preview-list-bukti-timbangan-muat-permohonan">
                                        <!-- Preview items will be added here -->
                                    </div>
                                </div>

                                @error('bukti_timbangan_muat')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('bukti_timbangan_muat.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Upload Bukti Timbangan untuk Permohonan --}}
                            <div>
                                <h3 class="block text-sm font-bold text-gray-800 mb-2">Foto Bukti Timbang Kosong / Lainnya (Bisa Banyak)</h3>
                                <label for="bukti_timbangan_permohonan" class="cursor-pointer block mt-1 flex justify-center px-6 pt-10 pb-10 border-2 border-indigo-300 border-dashed rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-colors bg-white shadow-sm">
                                    <div class="space-y-3 text-center">
                                        <svg class="mx-auto h-16 w-16 text-indigo-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <span class="relative font-bold text-indigo-600 text-lg">
                                                Ketuk untuk Ambil Foto
                                                <input id="bukti_timbangan_permohonan" name="bukti_timbangan[]" type="file" class="sr-only" accept="image/*,application/pdf" multiple capture="environment" onchange="previewBuktiTimbanganPermohonan(this)">
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 font-medium mt-2">Bisa pilih/foto beberapa gambar sekaligus</p>
                                    </div>
                                </label>

                                {{-- Preview Area untuk Bukti Timbangan Permohonan --}}
                                <div id="file-preview-bukti-timbangan-permohonan" class="mt-3 hidden">
                                    <div class="space-y-2" id="preview-list-bukti-timbangan-permohonan">
                                        <!-- Preview items will be added here -->
                                    </div>
                                </div>

                                @error('bukti_timbangan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('bukti_timbangan.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end gap-2">
                                <a href="{{ route('supir.dashboard') }}" class="w-full sm:w-auto inline-flex justify-center py-2 px-6 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Batal & Kembali
                                </a>
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Kirim Laporan (Simpan Checkpoint)
                                </button>
                            </div>
                        </div>
                    </form>

                @elseif(isset($suratJalan))
                    <form action="{{ isset($isBongkaran) && $isBongkaran ? route('supir.checkpoint.store-surat-jalan-bongkaran', $suratJalan->id) : route('supir.checkpoint.store-surat-jalan', $suratJalan->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            {{-- Simple form for surat jalan --}}
                            {{-- Simple form for surat jalan --}}
                            <div>
                                <label for="tanggal_checkpoint" class="block text-sm font-medium text-gray-700">Tanggal Lapor</label>
                                <input type="date" id="tanggal_checkpoint" name="tanggal_checkpoint" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div>
                                <label for="gudang_tujuan_id" class="block text-sm font-medium text-gray-700">Gudang Tujuan (Tempat Kirim/Bongkar)</label>
                                <select id="gudang_tujuan_id" name="gudang_tujuan_id" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" required>
                                    <option value="">-- Pilih Gudang Tujuan --</option>
                                    @if(isset($gudangs))
                                        @foreach($gudangs as $gudang)
                                            <option value="{{ $gudang->id }}">{{ $gudang->nama_gudang }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Pilih tujuan tempat bongkar/kirim kontainer ini.</p>
                            </div>

                            {{-- Container inputs for surat jalan --}}
                            <div class="space-y-2" id="container_input_section">
                                <label class="block text-sm font-medium text-gray-700">
                                    Nomor Kontainer ({{ strtoupper($suratJalan->tipe_kontainer ?? 'FCL') }} - {{ $suratJalan->size }}ft)
                                    @if($suratJalan->nomor_kontainer)
                                        <span class="ml-2 text-xs font-normal text-green-600">✓ Sudah diisi dari surat jalan</span>
                                    @endif
                                </label>
                                @for ($i = 0; $i < $suratJalan->jumlah_kontainer; $i++)
                                    <div class="relative mt-1">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">
                                            Kontainer #{{ $i + 1 }}
                                            @if($suratJalan->nomor_kontainer && $i == 0)
                                                <span class="ml-2 text-xs font-normal text-green-600">✓ Terisi otomatis</span>
                                            @endif
                                        </label>
                                        
                                        @if($suratJalan->nomor_kontainer && $i == 0)
                                            {{-- If nomor kontainer already filled, show select with pre-selected value --}}
                                            <select name="nomor_kontainer[]" class="select-kontainer block w-full rounded-lg border-2 border-green-300 bg-green-50 shadow focus:ring-2 focus:ring-green-500 focus:border-green-500 transition p-2.5 pr-10" required>
                                                <option value="{{ $suratJalan->nomor_kontainer }}" selected>{{ $suratJalan->nomor_kontainer }}</option>
                                            </select>
                                            <p class="text-xs text-gray-500 mt-1">Nomor dari surat jalan. Bisa dicari nomor lain jika salah.</p>
                                        @else
                                            {{-- Otherwise show dropdown to select --}}
                                            <select name="nomor_kontainer[]" class="select-kontainer block w-full rounded-lg border border-indigo-300 bg-white shadow focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition p-2.5 pr-10" required>
                                                <option value="">-- Pilih Kontainer #{{ $i + 1 }} --</option>
                                            </select>
                                        @endif
                                    </div>
                                @endfor
                                @if(!$suratJalan->nomor_kontainer)
                                    <p class="text-xs text-gray-500 mt-1">Pilih nomor kontainer tipe {{ strtoupper($suratJalan->tipe_kontainer ?? 'FCL') }} ukuran {{ $suratJalan->size }}ft sesuai jumlah surat jalan.</p>
                                @endif
                            </div>

                            {{-- Cargo type information --}}
                            <div class="space-y-2" id="cargo_info_section" style="display: none;">
                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <svg class="h-5 w-5 text-orange-400 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <h4 class="text-sm font-medium text-orange-800">Tipe Kontainer: Cargo</h4>
                                            <p class="text-sm text-orange-700 mt-1">Untuk tipe cargo, input nomor kontainer dan nomor seal tidak diperlukan.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Common fields --}}
                            <div id="no_seal_section">
                                <label class="block text-sm font-medium text-gray-700">
                                    Nomor Seal (Gembok)
                                    @if($suratJalan->nomor_seal)
                                        <span class="ml-2 text-xs font-normal text-green-600">✓ Sudah diisi dari surat jalan</span>
                                    @endif
                                </label>
                                @for ($i = 0; $i < $suratJalan->jumlah_kontainer; $i++)
                                    <div class="relative mt-1">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Nomor Seal Kontainer #{{ $i + 1 }}</label>
                                        
                                        @if($suratJalan->nomor_seal && $i == 0)
                                            {{-- If nomor seal already filled in surat jalan, pre-fill it --}}
                                            <div class="flex items-center gap-2">
                                                <input type="text" 
                                                       name="no_seal[]" 
                                                       value="{{ $suratJalan->nomor_seal }}"
                                                       class="flex-1 block w-full rounded-md border-2 border-green-300 bg-green-50 shadow-sm p-2.5" 
                                                       placeholder="Masukkan nomor seal kontainer #{{ $i + 1 }}">
                                                <span class="text-xs text-green-600 whitespace-nowrap">Terisi otomatis</span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Nomor dari surat jalan. Anda bisa mengubah jika ada perubahan.</p>
                                        @else
                                            <input type="text" 
                                                   name="no_seal[]" 
                                                   class="block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" 
                                                   placeholder="Masukkan nomor gembok/seal kontainer #{{ $i + 1 }}">
                                        @endif
                                    </div>
                                @endfor
                                @if(!$suratJalan->nomor_seal)
                                    <p class="text-xs text-gray-500 mt-1">Masukkan nomor seal/gembok untuk setiap kontainer (sesuai jumlah {{ $suratJalan->jumlah_kontainer }} kontainer).</p>
                                @endif
                            </div>
                            <div>
                                <label for="surat_jalan_vendor" class="block text-sm font-medium text-gray-700">Nomor Surat Jalan Vendor (Jika Ada)</label>
                                <input type="text" id="surat_jalan_vendor" name="surat_jalan_vendor" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" placeholder="Masukkan nomor surat jalan dari vendor">
                            </div>
                            <div>
                                <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan Tambahan (Kondisi Jalan/Barang)</label>
                                <textarea id="catatan" name="catatan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" placeholder="Tulis info penting di sini jika ada..."></textarea>
                            </div>

                            {{-- Upload Gambar untuk Surat Jalan --}}
                            <div>
                                <h3 class="block text-sm font-bold text-gray-800 mb-2">Foto Surat Jalan / DO (Bisa Banyak)</h3>
                                <label for="gambar_surat_jalan" class="cursor-pointer block mt-1 flex justify-center px-6 pt-10 pb-10 border-2 border-indigo-300 border-dashed rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-colors bg-white shadow-sm">
                                    <div class="space-y-3 text-center">
                                        <svg class="mx-auto h-16 w-16 text-indigo-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <span class="relative font-bold text-indigo-600 text-lg">
                                                Ketuk untuk Ambil Foto
                                                <input id="gambar_surat_jalan" name="gambar[]" type="file" class="sr-only" accept="image/*,application/pdf" multiple capture="environment" onchange="previewFileSuratJalan(this)">
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 font-medium mt-2">Bisa pilih/foto beberapa gambar sekaligus</p>
                                    </div>
                                </label>

                                {{-- Preview Area untuk Surat Jalan --}}
                                <div id="file-preview-surat-jalan" class="mt-3 hidden">
                                    <div class="space-y-2" id="preview-list-surat-jalan">
                                        <!-- Preview items will be added here -->
                                    </div>
                                </div>

                                @error('gambar')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('gambar.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Upload Bukti Muat untuk Surat Jalan --}}
                            <div>
                                <h3 class="block text-sm font-bold text-gray-800 mb-2">Foto Bukti Muat (Bisa Banyak)</h3>
                                <label for="bukti_muat_surat_jalan" class="cursor-pointer block mt-1 flex justify-center px-6 pt-10 pb-10 border-2 border-indigo-300 border-dashed rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-colors bg-white shadow-sm">
                                    <div class="space-y-3 text-center">
                                        <svg class="mx-auto h-16 w-16 text-indigo-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <span class="relative font-bold text-indigo-600 text-lg">
                                                Ketuk untuk Ambil Foto
                                                <input id="bukti_muat_surat_jalan" name="bukti_muat[]" type="file" class="sr-only" accept="image/*,application/pdf" multiple capture="environment" onchange="previewBuktiMuatSuratJalan(this)">
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 font-medium mt-2">Bisa pilih/foto beberapa gambar sekaligus</p>
                                    </div>
                                </label>

                                {{-- Preview Area untuk Bukti Muat Surat Jalan --}}
                                <div id="file-preview-bukti-muat-surat-jalan" class="mt-3 hidden">
                                    <div class="space-y-2" id="preview-list-bukti-muat-surat-jalan">
                                        <!-- Preview items will be added here -->
                                    </div>
                                </div>

                                @error('bukti_muat')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('bukti_muat.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            {{-- Upload Bukti Timbangan Muat untuk Surat Jalan --}}
                            <div>
                                <h3 class="block text-sm font-bold text-gray-800 mb-2">Foto Bukti Timbangan Muat (Bisa Banyak)</h3>
                                <label for="bukti_timbangan_muat_surat_jalan" class="cursor-pointer block mt-1 flex justify-center px-6 pt-10 pb-10 border-2 border-indigo-300 border-dashed rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-colors bg-white shadow-sm">
                                    <div class="space-y-3 text-center">
                                        <svg class="mx-auto h-16 w-16 text-indigo-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <span class="relative font-bold text-indigo-600 text-lg">
                                                Ketuk untuk Ambil Foto
                                                <input id="bukti_timbangan_muat_surat_jalan" name="bukti_timbangan_muat[]" type="file" class="sr-only" accept="image/*,application/pdf" multiple capture="environment" onchange="previewBuktiTimbanganMuatSuratJalan(this)">
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 font-medium mt-2">Bisa pilih/foto beberapa gambar sekaligus</p>
                                    </div>
                                </label>

                                {{-- Preview Area untuk Bukti Timbangan Muat Surat Jalan --}}
                                <div id="file-preview-bukti-timbangan-muat-surat-jalan" class="mt-3 hidden">
                                    <div class="space-y-2" id="preview-list-bukti-timbangan-muat-surat-jalan">
                                        <!-- Preview items will be added here -->
                                    </div>
                                </div>

                                @error('bukti_timbangan_muat')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('bukti_timbangan_muat.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Upload Bukti Timbangan untuk Surat Jalan --}}
                            <div>
                                <h3 class="block text-sm font-bold text-gray-800 mb-2">Foto Bukti Timbang Kosong / Lainnya (Bisa Banyak)</h3>
                                <label for="bukti_timbangan_surat_jalan" class="cursor-pointer block mt-1 flex justify-center px-6 pt-10 pb-10 border-2 border-indigo-300 border-dashed rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-colors bg-white shadow-sm">
                                    <div class="space-y-3 text-center">
                                        <svg class="mx-auto h-16 w-16 text-indigo-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <span class="relative font-bold text-indigo-600 text-lg">
                                                Ketuk untuk Ambil Foto
                                                <input id="bukti_timbangan_surat_jalan" name="bukti_timbangan[]" type="file" class="sr-only" accept="image/*,application/pdf" multiple capture="environment" onchange="previewBuktiTimbanganSuratJalan(this)">
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 font-medium mt-2">Bisa pilih/foto beberapa gambar sekaligus</p>
                                    </div>
                                </label>

                                {{-- Preview Area untuk Bukti Timbangan Surat Jalan --}}
                                <div id="file-preview-bukti-timbangan-surat-jalan" class="mt-3 hidden">
                                    <div class="space-y-2" id="preview-list-bukti-timbangan-surat-jalan">
                                        <!-- Preview items will be added here -->
                                    </div>
                                </div>

                                @error('bukti_timbangan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('bukti_timbangan.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div></div>

                            <div class="flex justify-end gap-2">
                                <a href="{{ route('supir.dashboard') }}" class="w-full sm:w-auto inline-flex justify-center py-2 px-6 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Batal & Kembali
                                </a>
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Kirim Laporan (Simpan Checkpoint)
                                </button>
                            </div></div>
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
                                        @php
                                            $gambarCheckpoint = $checkpoint->gambar;
                                            $isJson = is_string($gambarCheckpoint) && (str_starts_with($gambarCheckpoint, '[') || str_starts_with($gambarCheckpoint, '{'));
                                            $imagePaths = $isJson ? json_decode($gambarCheckpoint, true) : [$gambarCheckpoint];
                                            $imagePaths = is_array($imagePaths) ? array_filter($imagePaths) : [$gambarCheckpoint];
                                        @endphp
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($imagePaths as $index => $imagePath)
                                                <a href="{{ asset('storage/' . $imagePath) }}" target="_blank" class="inline-flex items-center text-xs text-indigo-600 hover:text-indigo-800 animate-pulse">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Gambar {{ $index + 1 }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($checkpoint->bukti_muat)
                                    <div class="mt-2">
                                        @php
                                            $buktiMuat = $checkpoint->bukti_muat;
                                            $isJson = is_string($buktiMuat) && (str_starts_with($buktiMuat, '[') || str_starts_with($buktiMuat, '{'));
                                            $buktiPaths = $isJson ? json_decode($buktiMuat, true) : [$buktiMuat];
                                            $buktiPaths = is_array($buktiPaths) ? array_filter($buktiPaths) : [$buktiMuat];
                                        @endphp
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            <span class="text-xs font-semibold text-gray-500 mr-1">Bukti Muat:</span>
                                            @foreach($buktiPaths as $index => $path)
                                                <a href="{{ asset('storage/' . $path) }}" target="_blank" class="inline-flex items-center text-xs text-indigo-600 hover:text-indigo-800">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Bukti {{ $index + 1 }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($checkpoint->bukti_timbangan_muat)
                                    <div class="mt-2">
                                        @php
                                            $buktiTimbanganMuat = $checkpoint->bukti_timbangan_muat;
                                            $isJson = is_string($buktiTimbanganMuat) && (str_starts_with($buktiTimbanganMuat, '[') || str_starts_with($buktiTimbanganMuat, '{'));
                                            $timbanganMuatPaths = $isJson ? json_decode($buktiTimbanganMuat, true) : [$buktiTimbanganMuat];
                                            $timbanganMuatPaths = is_array($timbanganMuatPaths) ? array_filter($timbanganMuatPaths) : [$buktiTimbanganMuat];
                                        @endphp
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            <span class="text-xs font-semibold text-gray-500 mr-1">Bukti Timbangan Muat:</span>
                                            @foreach($timbanganMuatPaths as $index => $path)
                                                <a href="{{ asset('storage/' . $path) }}" target="_blank" class="inline-flex items-center text-xs text-indigo-600 hover:text-indigo-800">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Timbangan Muat {{ $index + 1 }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($checkpoint->bukti_timbangan)
                                    <div class="mt-2">
                                        @php
                                            $buktiTimbangan = $checkpoint->bukti_timbangan;
                                            $isJson = is_string($buktiTimbangan) && (str_starts_with($buktiTimbangan, '[') || str_starts_with($buktiTimbangan, '{'));
                                            $timbanganPaths = $isJson ? json_decode($buktiTimbangan, true) : [$buktiTimbangan];
                                            $timbanganPaths = is_array($timbanganPaths) ? array_filter($timbanganPaths) : [$buktiTimbangan];
                                        @endphp
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            <span class="text-xs font-semibold text-gray-500 mr-1">Bukti Timbang Kosong / Lainnya:</span>
                                            @foreach($timbanganPaths as $index => $path)
                                                <a href="{{ asset('storage/' . $path) }}" target="_blank" class="inline-flex items-center text-xs text-indigo-600 hover:text-indigo-800">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Timbang {{ $index + 1 }}
                                                </a>
                                            @endforeach
                                        </div>
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
                                        @php
                                            $gambarCheckpoint = $suratJalan->gambar_checkpoint;
                                            $isJson = is_string($gambarCheckpoint) && (str_starts_with($gambarCheckpoint, '[') || str_starts_with($gambarCheckpoint, '{'));
                                            $imagePaths = $isJson ? json_decode($gambarCheckpoint, true) : [$gambarCheckpoint];
                                            $imagePaths = is_array($imagePaths) ? array_filter($imagePaths) : [$gambarCheckpoint];
                                        @endphp
                                        <div class="flex flex-wrap gap-2">
                                        @foreach($imagePaths as $index => $imagePath)
                                            <a href="{{ asset('storage/' . $imagePath) }}" target="_blank" class="inline-flex items-center text-xs text-indigo-600 hover:text-indigo-800">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                Gambar {{ $index + 1 }}
                                            </a>
                                        @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($suratJalan->bukti_muat)
                                    <div class="mt-2">
                                        @php
                                            $buktiMuat = $suratJalan->bukti_muat;
                                            $isJson = is_string($buktiMuat) && (str_starts_with($buktiMuat, '[') || str_starts_with($buktiMuat, '{'));
                                            $buktiPaths = $isJson ? json_decode($buktiMuat, true) : [$buktiMuat];
                                            $buktiPaths = is_array($buktiPaths) ? array_filter($buktiPaths) : [$buktiMuat];
                                        @endphp
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            <span class="text-xs font-semibold text-gray-500 mr-1">Bukti Muat:</span>
                                            @foreach($buktiPaths as $index => $path)
                                                <a href="{{ asset('storage/' . $path) }}" target="_blank" class="inline-flex items-center text-xs text-indigo-600 hover:text-indigo-800">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Bukti {{ $index + 1 }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($suratJalan->bukti_timbangan_muat)
                                    <div class="mt-2">
                                        @php
                                            $buktiTimbanganMuat = $suratJalan->bukti_timbangan_muat;
                                            $isJson = is_string($buktiTimbanganMuat) && (str_starts_with($buktiTimbanganMuat, '[') || str_starts_with($buktiTimbanganMuat, '{'));
                                            $timbanganMuatPaths = $isJson ? json_decode($buktiTimbanganMuat, true) : [$buktiTimbanganMuat];
                                            $timbanganMuatPaths = is_array($timbanganMuatPaths) ? array_filter($timbanganMuatPaths) : [$buktiTimbanganMuat];
                                        @endphp
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            <span class="text-xs font-semibold text-gray-500 mr-1">Bukti Timbangan Muat:</span>
                                            @foreach($timbanganMuatPaths as $index => $path)
                                                <a href="{{ asset('storage/' . $path) }}" target="_blank" class="inline-flex items-center text-xs text-indigo-600 hover:text-indigo-800">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Timbangan Muat {{ $index + 1 }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($suratJalan->bukti_timbangan)
                                    <div class="mt-2">
                                        @php
                                            $buktiTimbangan = $suratJalan->bukti_timbangan;
                                            $isJson = is_string($buktiTimbangan) && (str_starts_with($buktiTimbangan, '[') || str_starts_with($buktiTimbangan, '{'));
                                            $timbanganPaths = $isJson ? json_decode($buktiTimbangan, true) : [$buktiTimbangan];
                                            $timbanganPaths = is_array($timbanganPaths) ? array_filter($timbanganPaths) : [$buktiTimbangan];
                                        @endphp
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            <span class="text-xs font-semibold text-gray-500 mr-1">Bukti Timbang Kosong / Lainnya:</span>
                                            @foreach($timbanganPaths as $index => $path)
                                                <a href="{{ asset('storage/' . $path) }}" target="_blank" class="inline-flex items-center text-xs text-indigo-600 hover:text-indigo-800">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Timbang {{ $index + 1 }}
                                                </a>
                                            @endforeach
                                        </div>
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
            // Get data attributes for filtering
            @if(isset($permohonan))
                const ukuran = '{{ $permohonan->ukuran }}';
                const kegiatan = '{{ $permohonan->kegiatan }}';
            @elseif(isset($suratJalan))
                const ukuran = '{{ $suratJalan->size }}';
                const kegiatan = '{{ $suratJalan->kegiatan }}';
            @else
                const ukuran = '';
                const kegiatan = '';
            @endif

            // Inisialisasi Select2 pada dropdown kontainer biasa dengan AJAX search
            $('select.select-kontainer').each(function() {
                $(this).select2({
                    placeholder: 'Cari nomor kontainer',
                    width: '100%',
                    minimumInputLength: 0,
                    ajax: {
                        url: '{{ route("supir.api.kontainer.search", [], false) }}',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term || '',
                                ukuran: ukuran,
                                kegiatan: kegiatan,
                                page: params.page || 1
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.results
                            };
                        },
                        cache: true
                    }
                });
            });

            // Inisialisasi Select2 dengan tags pada dropdown antar kontainer perbaikan
            $('select.select-kontainer-perbaikan').each(function() {
                $(this).select2({
                    placeholder: 'Pilih atau ketik nomor kontainer',
                    width: '100%',
                    tags: true,
                    minimumInputLength: 0,
                    tokenSeparators: [',', ' '],
                    ajax: {
                        url: '{{ route("supir.api.kontainer.search", [], false) }}',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term || '',
                                ukuran: ukuran,
                                kegiatan: kegiatan,
                                page: params.page || 1
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.results
                            };
                        },
                        cache: true
                    },
                    createTag: function (params) {
                        var term = $.trim(params.term);
                        if (term === '') {
                            return null;
                        }
                        return {
                            id: term,
                            text: term + ' (Nomor Baru)',
                            newTag: true
                        }
                    }
                });
            });

            // Handle cargo type visibility
            handleCargoTypeVisibility();
            
            // Add form submission handler for cargo type
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    @if(isset($suratJalan))
                        const tipeKontainer = '{{ strtolower($suratJalan->tipe_kontainer ?? '') }}';
                        
                        if (tipeKontainer === 'cargo') {
                            // Remove nomor_kontainer fields from form submission
                            const containerSelects = form.querySelectorAll('select[name="nomor_kontainer[]"]');
                            containerSelects.forEach(select => {
                                select.removeAttribute('name');
                                select.removeAttribute('required');
                            });
                            
                            console.log('Cargo type form submission - removed container fields');
                        }
                    @endif
                });
            });
        });

        function handleCargoTypeVisibility() {
            @if(isset($suratJalan))
                const tipeKontainer = '{{ strtolower($suratJalan->tipe_kontainer ?? '') }}';
                
                if (tipeKontainer === 'cargo') {
                    // Hide container input and no seal sections for cargo
                    const containerSection = document.getElementById('container_input_section');
                    const noSealSection = document.getElementById('no_seal_section');
                    const cargoInfoSection = document.getElementById('cargo_info_section');
                    
                    if (containerSection) {
                        containerSection.style.display = 'none';
                        // Remove required attribute from container selects
                        containerSection.querySelectorAll('select[name="nomor_kontainer[]"]').forEach(select => {
                            select.removeAttribute('required');
                            select.removeAttribute('name'); // Don't send this field
                        });
                    }
                    
                    if (noSealSection) {
                        noSealSection.style.display = 'none';
                        // Remove name attribute from all no_seal inputs
                        const noSealInputs = noSealSection.querySelectorAll('input[name="no_seal[]"]');
                        noSealInputs.forEach(input => {
                            input.removeAttribute('name');
                        });
                    }
                    
                    if (cargoInfoSection) {
                        cargoInfoSection.style.display = 'block';
                    }
                    
                    console.log('Cargo type detected - hiding container and seal inputs');
                } else {
                    // Show all sections for non-cargo types
                    const containerSection = document.getElementById('container_input_section');
                    const noSealSection = document.getElementById('no_seal_section');
                    const cargoInfoSection = document.getElementById('cargo_info_section');
                    
                    if (containerSection) {
                        containerSection.style.display = 'block';
                    }
                    
                    if (noSealSection) {
                        noSealSection.style.display = 'block';
                    }
                    
                    if (cargoInfoSection) {
                        cargoInfoSection.style.display = 'none';
                    }
                    
                    console.log('Non-cargo type detected - showing all inputs');
                }
            @elseif(isset($permohonan))
                const tipeKontainer = '{{ strtolower($permohonan->tipe ?? '') }}';
                
                if (tipeKontainer === 'cargo') {
                    // Hide no seal section for cargo in permohonan
                    const noSealSection = document.getElementById('no_seal_section_permohonan');
                    
                    if (noSealSection) {
                        noSealSection.style.display = 'none';
                        // Remove name attribute from all no_seal inputs
                        const noSealInputs = noSealSection.querySelectorAll('input[name="no_seal[]"]');
                        noSealInputs.forEach(input => {
                            input.removeAttribute('name');
                        });
                    }
                    
                    console.log('Cargo type detected in permohonan - hiding seal input');
                }
            @endif
        }

        // Store selected files
        let selectedFilesPermohonan = [];
        let selectedFilesSuratJalan = [];

        // Preview file function untuk Permohonan (Multiple files)
        function previewFilePermohonan(input) {
            const files = Array.from(input.files);
            const previewContainer = document.getElementById('preview-list-permohonan');
            const previewSection = document.getElementById('file-preview-permohonan');
            
            if (files.length > 0) {
                // Add new files to the list
                selectedFilesPermohonan = selectedFilesPermohonan.concat(files);
                
                // Clear and rebuild preview
                previewContainer.innerHTML = '';
                
                selectedFilesPermohonan.forEach((file, index) => {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    const isImage = file.type.startsWith('image/');
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'flex items-center p-3 bg-gray-50 rounded-md border border-gray-200';
                    previewItem.innerHTML = `
                        ${isImage ? 
                            `<img src="${URL.createObjectURL(file)}" class="h-12 w-12 object-cover rounded mr-3" alt="${fileName}">` :
                            `<svg class="h-12 w-12 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`
                        }
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                        <button type="button" onclick="removeFilePermohonanByIndex(${index})" class="ml-3 text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                });
                
                previewSection.classList.remove('hidden');
                updateFileInputPermohonan();
            }
        }

        // Remove specific file untuk Permohonan
        function removeFilePermohonanByIndex(index) {
            selectedFilesPermohonan.splice(index, 1);
            
            if (selectedFilesPermohonan.length === 0) {
                document.getElementById('file-preview-permohonan').classList.add('hidden');
                document.getElementById('gambar_permohonan').value = '';
            } else {
                // Rebuild preview
                const input = document.getElementById('gambar_permohonan');
                const fakeEvent = { target: { files: [] } };
                previewFilePermohonan({ files: [] });
                
                // Re-render existing files
                const previewContainer = document.getElementById('preview-list-permohonan');
                previewContainer.innerHTML = '';
                
                selectedFilesPermohonan.forEach((file, idx) => {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    const isImage = file.type.startsWith('image/');
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'flex items-center p-3 bg-gray-50 rounded-md border border-gray-200';
                    previewItem.innerHTML = `
                        ${isImage ? 
                            `<img src="${URL.createObjectURL(file)}" class="h-12 w-12 object-cover rounded mr-3" alt="${fileName}">` :
                            `<svg class="h-12 w-12 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`
                        }
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                        <button type="button" onclick="removeFilePermohonanByIndex(${idx})" class="ml-3 text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                });
            }
            
            updateFileInputPermohonan();
        }

        // Update file input with selected files
        function updateFileInputPermohonan() {
            const input = document.getElementById('gambar_permohonan');
            const dataTransfer = new DataTransfer();
            
            selectedFilesPermohonan.forEach(file => {
                dataTransfer.items.add(file);
            });
            
            input.files = dataTransfer.files;
        }

        // Preview file function untuk Surat Jalan (Multiple files)
        function previewFileSuratJalan(input) {
            const files = Array.from(input.files);
            const previewContainer = document.getElementById('preview-list-surat-jalan');
            const previewSection = document.getElementById('file-preview-surat-jalan');
            
            if (files.length > 0) {
                // Add new files to the list
                selectedFilesSuratJalan = selectedFilesSuratJalan.concat(files);
                
                // Clear and rebuild preview
                previewContainer.innerHTML = '';
                
                selectedFilesSuratJalan.forEach((file, index) => {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    const isImage = file.type.startsWith('image/');
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'flex items-center p-3 bg-gray-50 rounded-md border border-gray-200';
                    previewItem.innerHTML = `
                        ${isImage ? 
                            `<img src="${URL.createObjectURL(file)}" class="h-12 w-12 object-cover rounded mr-3" alt="${fileName}">` :
                            `<svg class="h-12 w-12 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`
                        }
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                        <button type="button" onclick="removeFileSuratJalanByIndex(${index})" class="ml-3 text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                });
                
                previewSection.classList.remove('hidden');
                updateFileInputSuratJalan();
            }
        }

        // Remove specific file untuk Surat Jalan
        function removeFileSuratJalanByIndex(index) {
            selectedFilesSuratJalan.splice(index, 1);
            
            if (selectedFilesSuratJalan.length === 0) {
                document.getElementById('file-preview-surat-jalan').classList.add('hidden');
                document.getElementById('gambar_surat_jalan').value = '';
            } else {
                // Rebuild preview
                const previewContainer = document.getElementById('preview-list-surat-jalan');
                previewContainer.innerHTML = '';
                
                selectedFilesSuratJalan.forEach((file, idx) => {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    const isImage = file.type.startsWith('image/');
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'flex items-center p-3 bg-gray-50 rounded-md border border-gray-200';
                    previewItem.innerHTML = `
                        ${isImage ? 
                            `<img src="${URL.createObjectURL(file)}" class="h-12 w-12 object-cover rounded mr-3" alt="${fileName}">` :
                            `<svg class="h-12 w-12 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`
                        }
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                        <button type="button" onclick="removeFileSuratJalanByIndex(${idx})" class="ml-3 text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                });
            }
            
            updateFileInputSuratJalan();
        }

        // Update file input with selected files
        function updateFileInputSuratJalan() {
            const input = document.getElementById('gambar_surat_jalan');
            const dataTransfer = new DataTransfer();
            
            selectedFilesSuratJalan.forEach(file => {
                dataTransfer.items.add(file);
            });
            
            input.files = dataTransfer.files;
        }

        // Store selected bukti muat files
        let selectedFilesBuktiMuatPermohonan = [];
        let selectedFilesBuktiMuatSuratJalan = [];

        // Store selected bukti timbangan files
        let selectedFilesBuktiTimbanganPermohonan = [];
        let selectedFilesBuktiTimbanganSuratJalan = [];

        // Store selected bukti timbangan muat files
        let selectedFilesBuktiTimbanganMuatPermohonan = [];
        let selectedFilesBuktiTimbanganMuatSuratJalan = [];

        // Preview Bukti Muat Permohonan
        function previewBuktiMuatPermohonan(input) {
            const files = Array.from(input.files);
            const previewContainer = document.getElementById('preview-list-bukti-muat-permohonan');
            const previewSection = document.getElementById('file-preview-bukti-muat-permohonan');
            
            if (files.length > 0) {
                selectedFilesBuktiMuatPermohonan = selectedFilesBuktiMuatPermohonan.concat(files);
                previewContainer.innerHTML = '';
                
                selectedFilesBuktiMuatPermohonan.forEach((file, index) => {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    const isImage = file.type.startsWith('image/');
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'flex items-center p-3 bg-gray-50 rounded-md border border-gray-200';
                    previewItem.innerHTML = `
                        ${isImage ? 
                            `<img src="${URL.createObjectURL(file)}" class="h-12 w-12 object-cover rounded mr-3" alt="${fileName}">` :
                            `<svg class="h-12 w-12 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`
                        }
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                        <button type="button" onclick="removeBuktiMuatPermohonanByIndex(${index})" class="ml-3 text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                });
                
                previewSection.classList.remove('hidden');
                updateFileInputBuktiMuatPermohonan();
            }
        }

        // Remove Bukti Muat Permohonan
        function removeBuktiMuatPermohonanByIndex(index) {
            selectedFilesBuktiMuatPermohonan.splice(index, 1);
            
            if (selectedFilesBuktiMuatPermohonan.length === 0) {
                document.getElementById('file-preview-bukti-muat-permohonan').classList.add('hidden');
                document.getElementById('bukti_muat_permohonan').value = '';
            } else {
                const previewContainer = document.getElementById('preview-list-bukti-muat-permohonan');
                previewContainer.innerHTML = '';
                
                selectedFilesBuktiMuatPermohonan.forEach((file, idx) => {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    const isImage = file.type.startsWith('image/');
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'flex items-center p-3 bg-gray-50 rounded-md border border-gray-200';
                    previewItem.innerHTML = `
                        ${isImage ? 
                            `<img src="${URL.createObjectURL(file)}" class="h-12 w-12 object-cover rounded mr-3" alt="${fileName}">` :
                            `<svg class="h-12 w-12 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`
                        }
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                        <button type="button" onclick="removeBuktiMuatPermohonanByIndex(${idx})" class="ml-3 text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                });
            }
            updateFileInputBuktiMuatPermohonan();
        }

        // Update file input with selected files
        function updateFileInputBuktiMuatPermohonan() {
            const input = document.getElementById('bukti_muat_permohonan');
            const dataTransfer = new DataTransfer();
            selectedFilesBuktiMuatPermohonan.forEach(file => {
                dataTransfer.items.add(file);
            });
            input.files = dataTransfer.files;
        }

        // Preview Bukti Muat Surat Jalan
        function previewBuktiMuatSuratJalan(input) {
            const files = Array.from(input.files);
            const previewContainer = document.getElementById('preview-list-bukti-muat-surat-jalan');
            const previewSection = document.getElementById('file-preview-bukti-muat-surat-jalan');
            
            if (files.length > 0) {
                selectedFilesBuktiMuatSuratJalan = selectedFilesBuktiMuatSuratJalan.concat(files);
                previewContainer.innerHTML = '';
                
                selectedFilesBuktiMuatSuratJalan.forEach((file, index) => {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    const isImage = file.type.startsWith('image/');
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'flex items-center p-3 bg-gray-50 rounded-md border border-gray-200';
                    previewItem.innerHTML = `
                        ${isImage ? 
                            `<img src="${URL.createObjectURL(file)}" class="h-12 w-12 object-cover rounded mr-3" alt="${fileName}">` :
                            `<svg class="h-12 w-12 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`
                        }
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                        <button type="button" onclick="removeBuktiMuatSuratJalanByIndex(${index})" class="ml-3 text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                });
                
                previewSection.classList.remove('hidden');
                updateFileInputBuktiMuatSuratJalan();
            }
        }

        // Remove Bukti Muat Surat Jalan
        function removeBuktiMuatSuratJalanByIndex(index) {
            selectedFilesBuktiMuatSuratJalan.splice(index, 1);
            
            if (selectedFilesBuktiMuatSuratJalan.length === 0) {
                document.getElementById('file-preview-bukti-muat-surat-jalan').classList.add('hidden');
                document.getElementById('bukti_muat_surat_jalan').value = '';
            } else {
                const previewContainer = document.getElementById('preview-list-bukti-muat-surat-jalan');
                previewContainer.innerHTML = '';
                
                selectedFilesBuktiMuatSuratJalan.forEach((file, idx) => {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    const isImage = file.type.startsWith('image/');
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'flex items-center p-3 bg-gray-50 rounded-md border border-gray-200';
                    previewItem.innerHTML = `
                        ${isImage ? 
                            `<img src="${URL.createObjectURL(file)}" class="h-12 w-12 object-cover rounded mr-3" alt="${fileName}">` :
                            `<svg class="h-12 w-12 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`
                        }
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                        <button type="button" onclick="removeBuktiMuatSuratJalanByIndex(${idx})" class="ml-3 text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                });
            }
            updateFileInputBuktiMuatSuratJalan();
        }

        // Update file input with selected files
        function updateFileInputBuktiMuatSuratJalan() {
            const input = document.getElementById('bukti_muat_surat_jalan');
            const dataTransfer = new DataTransfer();
            selectedFilesBuktiMuatSuratJalan.forEach(file => {
                dataTransfer.items.add(file);
            });
            input.files = dataTransfer.files;
        }

        // Preview Bukti Timbangan Permohonan
        function previewBuktiTimbanganPermohonan(input) {
            const files = Array.from(input.files);
            const previewContainer = document.getElementById('preview-list-bukti-timbangan-permohonan');
            const previewSection = document.getElementById('file-preview-bukti-timbangan-permohonan');
            
            if (files.length > 0) {
                selectedFilesBuktiTimbanganPermohonan = selectedFilesBuktiTimbanganPermohonan.concat(files);
                previewContainer.innerHTML = '';
                
                selectedFilesBuktiTimbanganPermohonan.forEach((file, index) => {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    const isImage = file.type.startsWith('image/');
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'flex items-center p-3 bg-gray-50 rounded-md border border-gray-200';
                    previewItem.innerHTML = `
                        ${isImage ? 
                            `<img src="${URL.createObjectURL(file)}" class="h-12 w-12 object-cover rounded mr-3" alt="${fileName}">` :
                            `<svg class="h-12 w-12 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`
                        }
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                        <button type="button" onclick="removeBuktiTimbanganPermohonanByIndex(${index})" class="ml-3 text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                });
                
                previewSection.classList.remove('hidden');
                updateFileInputBuktiTimbanganPermohonan();
            }
        }

        // Remove Bukti Timbangan Permohonan
        function removeBuktiTimbanganPermohonanByIndex(index) {
            selectedFilesBuktiTimbanganPermohonan.splice(index, 1);
            
            if (selectedFilesBuktiTimbanganPermohonan.length === 0) {
                document.getElementById('file-preview-bukti-timbangan-permohonan').classList.add('hidden');
                document.getElementById('bukti_timbangan_permohonan').value = '';
            } else {
                const previewContainer = document.getElementById('preview-list-bukti-timbangan-permohonan');
                previewContainer.innerHTML = '';
                
                selectedFilesBuktiTimbanganPermohonan.forEach((file, idx) => {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    const isImage = file.type.startsWith('image/');
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'flex items-center p-3 bg-gray-50 rounded-md border border-gray-200';
                    previewItem.innerHTML = `
                        ${isImage ? 
                            `<img src="${URL.createObjectURL(file)}" class="h-12 w-12 object-cover rounded mr-3" alt="${fileName}">` :
                            `<svg class="h-12 w-12 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`
                        }
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                        <button type="button" onclick="removeBuktiTimbanganPermohonanByIndex(${idx})" class="ml-3 text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                });
            }
            updateFileInputBuktiTimbanganPermohonan();
        }

        // Update file input with selected files
        function updateFileInputBuktiTimbanganPermohonan() {
            const input = document.getElementById('bukti_timbangan_permohonan');
            const dataTransfer = new DataTransfer();
            selectedFilesBuktiTimbanganPermohonan.forEach(file => {
                dataTransfer.items.add(file);
            });
            input.files = dataTransfer.files;
        }

        // Preview Bukti Timbangan Surat Jalan
        function previewBuktiTimbanganSuratJalan(input) {
            const files = Array.from(input.files);
            const previewContainer = document.getElementById('preview-list-bukti-timbangan-surat-jalan');
            const previewSection = document.getElementById('file-preview-bukti-timbangan-surat-jalan');
            
            if (files.length > 0) {
                selectedFilesBuktiTimbanganSuratJalan = selectedFilesBuktiTimbanganSuratJalan.concat(files);
                previewContainer.innerHTML = '';
                
                selectedFilesBuktiTimbanganSuratJalan.forEach((file, index) => {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    const isImage = file.type.startsWith('image/');
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'flex items-center p-3 bg-gray-50 rounded-md border border-gray-200';
                    previewItem.innerHTML = `
                        ${isImage ? 
                            `<img src="${URL.createObjectURL(file)}" class="h-12 w-12 object-cover rounded mr-3" alt="${fileName}">` :
                            `<svg class="h-12 w-12 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`
                        }
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                        <button type="button" onclick="removeBuktiTimbanganSuratJalanByIndex(${index})" class="ml-3 text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                });
                
                previewSection.classList.remove('hidden');
                updateFileInputBuktiTimbanganSuratJalan();
            }
        }

        // Remove Bukti Timbangan Surat Jalan
        function removeBuktiTimbanganSuratJalanByIndex(index) {
            selectedFilesBuktiTimbanganSuratJalan.splice(index, 1);
            
            if (selectedFilesBuktiTimbanganSuratJalan.length === 0) {
                document.getElementById('file-preview-bukti-timbangan-surat-jalan').classList.add('hidden');
                document.getElementById('bukti_timbangan_surat_jalan').value = '';
            } else {
                const previewContainer = document.getElementById('preview-list-bukti-timbangan-surat-jalan');
                previewContainer.innerHTML = '';
                
                selectedFilesBuktiTimbanganSuratJalan.forEach((file, idx) => {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    const isImage = file.type.startsWith('image/');
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'flex items-center p-3 bg-gray-50 rounded-md border border-gray-200';
                    previewItem.innerHTML = `
                        ${isImage ? 
                            `<img src="${URL.createObjectURL(file)}" class="h-12 w-12 object-cover rounded mr-3" alt="${fileName}">` :
                            `<svg class="h-12 w-12 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`
                        }
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                        <button type="button" onclick="removeBuktiTimbanganSuratJalanByIndex(${idx})" class="ml-3 text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                });
            }
            updateFileInputBuktiTimbanganSuratJalan();
        }

        // Update file input with selected files
        function updateFileInputBuktiTimbanganSuratJalan() {
            const input = document.getElementById('bukti_timbangan_surat_jalan');
            const dataTransfer = new DataTransfer();
            selectedFilesBuktiTimbanganSuratJalan.forEach(file => {
                dataTransfer.items.add(file);
            });
            input.files = dataTransfer.files;
        }

        // Preview Bukti Timbangan Muat Permohonan
        function previewBuktiTimbanganMuatPermohonan(input) {
            const files = Array.from(input.files);
            const previewContainer = document.getElementById('preview-list-bukti-timbangan-muat-permohonan');
            const previewSection = document.getElementById('file-preview-bukti-timbangan-muat-permohonan');
            
            if (files.length > 0) {
                selectedFilesBuktiTimbanganMuatPermohonan = selectedFilesBuktiTimbanganMuatPermohonan.concat(files);
                previewContainer.innerHTML = '';
                
                selectedFilesBuktiTimbanganMuatPermohonan.forEach((file, index) => {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    const isImage = file.type.startsWith('image/');
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'flex items-center p-3 bg-gray-50 rounded-md border border-gray-200';
                    previewItem.innerHTML = `
                        ${isImage ? 
                            `<img src="${URL.createObjectURL(file)}" class="h-12 w-12 object-cover rounded mr-3" alt="${fileName}">` :
                            `<svg class="h-12 w-12 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`
                        }
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                        <button type="button" onclick="removeBuktiTimbanganMuatPermohonanByIndex(${index})" class="ml-3 text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                });
                
                previewSection.classList.remove('hidden');
                updateFileInputBuktiTimbanganMuatPermohonan();
            }
        }

        // Remove Bukti Timbangan Muat Permohonan
        function removeBuktiTimbanganMuatPermohonanByIndex(index) {
            selectedFilesBuktiTimbanganMuatPermohonan.splice(index, 1);
            
            if (selectedFilesBuktiTimbanganMuatPermohonan.length === 0) {
                document.getElementById('file-preview-bukti-timbangan-muat-permohonan').classList.add('hidden');
                document.getElementById('bukti_timbangan_muat_permohonan').value = '';
            } else {
                const previewContainer = document.getElementById('preview-list-bukti-timbangan-muat-permohonan');
                previewContainer.innerHTML = '';
                
                selectedFilesBuktiTimbanganMuatPermohonan.forEach((file, idx) => {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    const isImage = file.type.startsWith('image/');
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'flex items-center p-3 bg-gray-50 rounded-md border border-gray-200';
                    previewItem.innerHTML = `
                        ${isImage ? 
                            `<img src="${URL.createObjectURL(file)}" class="h-12 w-12 object-cover rounded mr-3" alt="${fileName}">` :
                            `<svg class="h-12 w-12 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`
                        }
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                        <button type="button" onclick="removeBuktiTimbanganMuatPermohonanByIndex(${idx})" class="ml-3 text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                });
            }
            updateFileInputBuktiTimbanganMuatPermohonan();
        }

        // Update file input with selected files
        function updateFileInputBuktiTimbanganMuatPermohonan() {
            const input = document.getElementById('bukti_timbangan_muat_permohonan');
            const dataTransfer = new DataTransfer();
            selectedFilesBuktiTimbanganMuatPermohonan.forEach(file => {
                dataTransfer.items.add(file);
            });
            input.files = dataTransfer.files;
        }

        // Preview Bukti Timbangan Muat Surat Jalan
        function previewBuktiTimbanganMuatSuratJalan(input) {
            const files = Array.from(input.files);
            const previewContainer = document.getElementById('preview-list-bukti-timbangan-muat-surat-jalan');
            const previewSection = document.getElementById('file-preview-bukti-timbangan-muat-surat-jalan');
            
            if (files.length > 0) {
                selectedFilesBuktiTimbanganMuatSuratJalan = selectedFilesBuktiTimbanganMuatSuratJalan.concat(files);
                previewContainer.innerHTML = '';
                
                selectedFilesBuktiTimbanganMuatSuratJalan.forEach((file, index) => {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    const isImage = file.type.startsWith('image/');
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'flex items-center p-3 bg-gray-50 rounded-md border border-gray-200';
                    previewItem.innerHTML = `
                        ${isImage ? 
                            `<img src="${URL.createObjectURL(file)}" class="h-12 w-12 object-cover rounded mr-3" alt="${fileName}">` :
                            `<svg class="h-12 w-12 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`
                        }
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                        <button type="button" onclick="removeBuktiTimbanganMuatSuratJalanByIndex(${index})" class="ml-3 text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                });
                
                previewSection.classList.remove('hidden');
                updateFileInputBuktiTimbanganMuatSuratJalan();
            }
        }

        // Remove Bukti Timbangan Muat Surat Jalan
        function removeBuktiTimbanganMuatSuratJalanByIndex(index) {
            selectedFilesBuktiTimbanganMuatSuratJalan.splice(index, 1);
            
            if (selectedFilesBuktiTimbanganMuatSuratJalan.length === 0) {
                document.getElementById('file-preview-bukti-timbangan-muat-surat-jalan').classList.add('hidden');
                document.getElementById('bukti_timbangan_muat_surat_jalan').value = '';
            } else {
                const previewContainer = document.getElementById('preview-list-bukti-timbangan-muat-surat-jalan');
                previewContainer.innerHTML = '';
                
                selectedFilesBuktiTimbanganMuatSuratJalan.forEach((file, idx) => {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    const isImage = file.type.startsWith('image/');
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'flex items-center p-3 bg-gray-50 rounded-md border border-gray-200';
                    previewItem.innerHTML = `
                        ${isImage ? 
                            `<img src="${URL.createObjectURL(file)}" class="h-12 w-12 object-cover rounded mr-3" alt="${fileName}">` :
                            `<svg class="h-12 w-12 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`
                        }
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                        <button type="button" onclick="removeBuktiTimbanganMuatSuratJalanByIndex(${idx})" class="ml-3 text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                });
            }
            updateFileInputBuktiTimbanganMuatSuratJalan();
        }

        // Update file input with selected files
        function updateFileInputBuktiTimbanganMuatSuratJalan() {
            const input = document.getElementById('bukti_timbangan_muat_surat_jalan');
            const dataTransfer = new DataTransfer();
            selectedFilesBuktiTimbanganMuatSuratJalan.forEach(file => {
                dataTransfer.items.add(file);
            });
            input.files = dataTransfer.files;
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
                        } else if (fileInput.id === 'bukti_muat_permohonan') {
                            previewBuktiMuatPermohonan(fileInput);
                        } else if (fileInput.id === 'bukti_muat_surat_jalan') {
                            previewBuktiMuatSuratJalan(fileInput);
                        } else if (fileInput.id === 'bukti_timbangan_permohonan') {
                            previewBuktiTimbanganPermohonan(fileInput);
                        } else if (fileInput.id === 'bukti_timbangan_surat_jalan') {
                            previewBuktiTimbanganSuratJalan(fileInput);
                        } else if (fileInput.id === 'bukti_timbangan_muat_permohonan') {
                            previewBuktiTimbanganMuatPermohonan(fileInput);
                        } else if (fileInput.id === 'bukti_timbangan_muat_surat_jalan') {
                            previewBuktiTimbanganMuatSuratJalan(fileInput);
                        }
                    }
                });
            });
        });
    </script>
</html>
