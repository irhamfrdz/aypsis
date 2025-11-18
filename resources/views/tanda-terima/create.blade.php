@extends('layouts.app')

@section('title', 'Buat Tanda Terima')

@section('content')
@php
    // Parse container data from surat jalan
    $nomorKontainerArray = [];
    
    if (!empty($suratJalan->no_kontainer)) {
        $nomorKontainerArray = array_map('trim', explode(',', $suratJalan->no_kontainer));
    }
    
    $jumlahKontainer = $suratJalan->jumlah_kontainer ?: 1;
@endphp

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li>
                <a href="{{ route('tanda-terima.index') }}" class="hover:text-blue-600 transition">Tanda Terima</a>
            </li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li>
                <a href="{{ route('tanda-terima.select-surat-jalan') }}" class="hover:text-blue-600 transition">Pilih Surat Jalan</a>
            </li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 font-medium">Buat</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Buat Tanda Terima</h1>
                <p class="text-gray-600 mt-1">No. Surat Jalan: <span class="font-semibold">{{ $suratJalan->no_surat_jalan }}</span></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Section (Left - 2/3) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Data Tanda Terima</h2>
                    <p class="text-sm text-gray-600 mt-1">Lengkapi informasi untuk tanda terima baru</p>
                </div>

                <form action="{{ route('tanda-terima.store') }}" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" name="surat_jalan_id" value="{{ $suratJalan->id }}">

                    <div class="space-y-6">
                        <!-- Data Kontainer Section -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <label class="block text-sm font-medium text-gray-700">
                                    Data Kontainer
                                </label>
                                <span class="text-xs text-gray-500">
                                    {{ $jumlahKontainer }} Kontainer - {{ $suratJalan->size }}ft
                                </span>
                            </div>

                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Kontainer
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No. Kontainer
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No. Seal
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @for($i = 1; $i <= $jumlahKontainer; $i++)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                            <span class="text-sm font-medium text-blue-600">#{{ $i }}</span>
                                                        </div>
                                                        <div class="ml-3">
                                                            <p class="text-sm font-medium text-gray-900">
                                                                Kontainer {{ $i }}
                                                            </p>
                                                            <p class="text-xs text-gray-500">{{ $suratJalan->size }}ft - {{ strtoupper($suratJalan->tipe_kontainer ?: 'FCL') }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="text"
                                                           name="nomor_kontainer[]"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-mono @error('nomor_kontainer.'.$i) border-red-500 @enderror"
                                                           placeholder="Nomor kontainer #{{ $i }}"
                                                           value="{{ old('nomor_kontainer.'.$i, isset($nomorKontainerArray[$i-1]) ? $nomorKontainerArray[$i-1] : '') }}">
                                                    @error('nomor_kontainer.'.$i)
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="text"
                                                           name="no_seal[]"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-mono @error('no_seal.'.$i) border-red-500 @enderror"
                                                           placeholder="Nomor seal #{{ $i }}"
                                                           value="{{ old('no_seal.'.$i, '') }}">
                                                    @error('no_seal.'.$i)
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Estimasi Nama Kapal -->
                        <div>
                            <label for="estimasi_nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">
                                Estimasi Nama Kapal <span class="text-red-500">*</span>
                            </label>
                            <select name="estimasi_nama_kapal"
                                    id="estimasi_nama_kapal"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-kapal @error('estimasi_nama_kapal') border-red-500 @enderror"
                                    required>
                                <option value="">-- Pilih Kapal --</option>
                                @foreach($masterKapals as $kapal)
                                    <option value="{{ $kapal->nama_kapal }}"
                                            {{ old('estimasi_nama_kapal') == $kapal->nama_kapal ? 'selected' : '' }}>
                                        {{ $kapal->nama_kapal }}{{ $kapal->nickname ? ' (' . $kapal->nickname . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estimasi_nama_kapal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-search mr-1"></i>Ketik untuk mencari nama kapal
                            </p>
                        </div>

                        <!-- Tanggal Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Informasi Tanggal
                            </label>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="tanggal_ambil_kontainer" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tanggal Ambil Kontainer
                                    </label>
                                    <input type="date"
                                           name="tanggal_ambil_kontainer"
                                           id="tanggal_ambil_kontainer"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_ambil_kontainer') border-red-500 @enderror"
                                           value="{{ old('tanggal_ambil_kontainer') }}">
                                    @error('tanggal_ambil_kontainer')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="tanggal_terima_pelabuhan" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tanggal Terima Pelabuhan
                                    </label>
                                    <input type="date"
                                           name="tanggal_terima_pelabuhan"
                                           id="tanggal_terima_pelabuhan"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_terima_pelabuhan') border-red-500 @enderror"
                                           value="{{ old('tanggal_terima_pelabuhan') }}">
                                    @error('tanggal_terima_pelabuhan')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="tanggal_garasi" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tanggal Garasi
                                    </label>
                                    <input type="date"
                                           name="tanggal_garasi"
                                           id="tanggal_garasi"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_garasi') border-red-500 @enderror"
                                           value="{{ old('tanggal_garasi') }}">
                                    @error('tanggal_garasi')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Kuantitas -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Informasi Kuantitas
                            </label>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="jumlah" class="block text-xs font-medium text-gray-500 mb-2">
                                        Jumlah
                                    </label>
                                    <input type="number"
                                           name="jumlah"
                                           id="jumlah"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('jumlah') border-red-500 @enderror"
                                           placeholder="0"
                                           value="{{ old('jumlah') }}"
                                           min="0"
                                           step="1">
                                    @error('jumlah')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="satuan" class="block text-xs font-medium text-gray-500 mb-2">
                                        Satuan
                                    </label>
                                    <input type="text"
                                           name="satuan"
                                           id="satuan"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('satuan') border-red-500 @enderror"
                                           placeholder="contoh: Pcs, Kg, Box"
                                           value="{{ old('satuan') }}">
                                    @error('satuan')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Dimensi & Volume -->
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                Dimensi dan Volume
                            </h3>

                            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                <div>
                                    <label for="panjang" class="block text-xs font-medium text-gray-500 mb-2">
                                        Panjang (m)
                                    </label>
                                    <input type="number"
                                           name="panjang"
                                           id="panjang"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm @error('panjang') border-red-500 @enderror"
                                           placeholder="0.000"
                                           value="{{ old('panjang') }}"
                                           min="0"
                                           step="0.001"
                                           onchange="calculateVolume()">
                                    @error('panjang')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="lebar" class="block text-xs font-medium text-gray-500 mb-2">
                                        Lebar (m)
                                    </label>
                                    <input type="number"
                                           name="lebar"
                                           id="lebar"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm @error('lebar') border-red-500 @enderror"
                                           placeholder="0.000"
                                           value="{{ old('lebar') }}"
                                           min="0"
                                           step="0.001"
                                           onchange="calculateVolume()">
                                    @error('lebar')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="tinggi" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tinggi (m)
                                    </label>
                                    <input type="number"
                                           name="tinggi"
                                           id="tinggi"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm @error('tinggi') border-red-500 @enderror"
                                           placeholder="0.000"
                                           value="{{ old('tinggi') }}"
                                           min="0"
                                           step="0.001"
                                           onchange="calculateVolume()">
                                    @error('tinggi')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="meter_kubik" class="block text-xs font-medium text-gray-500 mb-2">
                                        Volume (m³)
                                    </label>
                                    <input type="number"
                                           name="meter_kubik"
                                           id="meter_kubik"
                                           class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm @error('meter_kubik') border-red-500 @enderror"
                                           placeholder="0.000"
                                           value="{{ old('meter_kubik') }}"
                                           min="0"
                                           step="0.001"
                                           readonly>
                                    @error('meter_kubik')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="tonase" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tonase (Ton)
                                    </label>
                                    <input type="number"
                                           name="tonase"
                                           id="tonase"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm @error('tonase') border-red-500 @enderror"
                                           placeholder="0.000"
                                           value="{{ old('tonase') }}"
                                           min="0"
                                           step="0.001">
                                    @error('tonase')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Volume akan dihitung otomatis dari panjang × lebar × tinggi
                            </p>
                        </div>

                        <!-- Informasi Tambahan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Informasi Tambahan
                            </label>

                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label for="tujuan_pengiriman" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tujuan Pengiriman
                                    </label>
                                    <input type="text"
                                           name="tujuan_pengiriman"
                                           id="tujuan_pengiriman"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tujuan_pengiriman') border-red-500 @enderror"
                                           placeholder="Masukkan tujuan pengiriman"
                                           value="{{ old('tujuan_pengiriman', $suratJalan->tujuan_pengiriman) }}">
                                    @error('tujuan_pengiriman')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="catatan" class="block text-xs font-medium text-gray-500 mb-2">
                                        Catatan
                                    </label>
                                    <textarea name="catatan"
                                              id="catatan"
                                              rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('catatan') border-red-500 @enderror"
                                              placeholder="Tambahkan catatan jika diperlukan">{{ old('catatan') }}</textarea>
                                    @error('catatan')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
                        <a href="{{ route('tanda-terima.select-surat-jalan') }}"
                           class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition duration-200">
                            <i class="fas fa-times mr-2"></i> Batal
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition duration-200">
                            <i class="fas fa-save mr-2"></i> Simpan Tanda Terima
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Section (Right - 1/3) -->
        <div class="lg:col-span-1">
            <!-- Surat Jalan Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Surat Jalan</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">No. Surat Jalan</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $suratJalan->no_surat_jalan }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Tanggal</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $suratJalan->tanggal_surat_jalan?->format('d F Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Supir</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $suratJalan->supir ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Jenis Barang</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $suratJalan->jenis_barang ?: '-' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Kegiatan</dt>
                        <dd class="mt-1">
                            @php
                                $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $suratJalan->kegiatan)
                                                ->value('nama_kegiatan') ?? $suratJalan->kegiatan;
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $kegiatanName }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Kontainer Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Kontainer</h3>
                <dl class="space-y-3">
                    @if(!empty($nomorKontainerArray))
                        @foreach($nomorKontainerArray as $index => $kontainer)
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Kontainer #{{ $index + 1 }}</h4>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase">No. Kontainer</dt>
                                    <dd class="mt-1">
                                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">
                                            {{ $kontainer }}
                                        </code>
                                    </dd>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <div class="grid grid-cols-2 gap-3 mt-3 pt-3 border-t border-gray-200">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Size</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $suratJalan->size ?: '-' }} ft</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Jumlah Kontainer</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $jumlahKontainer }} Kontainer
                                </span>
                            </dd>
                        </div>
                    </div>
                </dl>
            </div>

            <!-- Location Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Lokasi</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Tujuan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $suratJalan->tujuan_pengiriman ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Pengirim</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $suratJalan->order && $suratJalan->order->pengirim ? $suratJalan->order->pengirim->nama_pengirim : '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Custom Select2 styling to match Tailwind */
    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px;
        color: #111827;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 8px;
    }

    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.5rem;
    }

    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #dbeafe;
        color: #1e40af;
    }
</style>
@endpush

@push('scripts')
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Use window.onload to ensure everything is loaded
    window.addEventListener('load', function() {
        // Initialize Select2 for kapal dropdown
        if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
            $('.select2-kapal').select2({
                placeholder: '-- Pilih Kapal --',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Kapal tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });
        }
    });

    function calculateVolume() {
        const panjang = parseFloat(document.getElementById('panjang').value) || 0;
        const lebar = parseFloat(document.getElementById('lebar').value) || 0;
        const tinggi = parseFloat(document.getElementById('tinggi').value) || 0;

        if (panjang > 0 && lebar > 0 && tinggi > 0) {
            const volume = panjang * lebar * tinggi;
            document.getElementById('meter_kubik').value = volume.toFixed(3);
        } else {
            document.getElementById('meter_kubik').value = '';
        }
    }
</script>
@endpush
