@extends('layouts.app')

@section('title', 'Edit Tanda Terima')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li>
                <a href="{{ route('tanda-terima.index') }}" class="hover:text-blue-600 transition">Tanda Terima</a>
            </li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 font-medium">Edit</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Tanda Terima</h1>
                <p class="text-gray-600 mt-1">No. Surat Jalan: <span class="font-semibold">{{ $tandaTerima->no_surat_jalan }}</span></p>
            </div>
            <div>
                @if($tandaTerima->status == 'draft')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        <i class="fas fa-circle text-xs mr-2"></i> Draft
                    </span>
                @elseif($tandaTerima->status == 'submitted')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-paper-plane text-xs mr-2"></i> Submitted
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle text-xs mr-2"></i> Completed
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Section (Left - 2/3) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Data Tambahan</h2>
                    <p class="text-sm text-gray-600 mt-1">Lengkapi informasi tambahan untuk tanda terima</p>
                </div>

                <form action="{{ route('tanda-terima.update', $tandaTerima->id) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- Hidden status field -->
                    <input type="hidden" name="status" value="{{ $tandaTerima->status }}">

                    <div class="space-y-6">
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
                                            {{ old('estimasi_nama_kapal', $tandaTerima->estimasi_nama_kapal) == $kapal->nama_kapal ? 'selected' : '' }}>
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
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="tanggal_ambil_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Ambil Kontainer
                                </label>
                                <input type="date"
                                       name="tanggal_ambil_kontainer"
                                       id="tanggal_ambil_kontainer"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_ambil_kontainer') border-red-500 @enderror"
                                       value="{{ old('tanggal_ambil_kontainer', $tandaTerima->tanggal_ambil_kontainer?->format('Y-m-d')) }}">
                                @error('tanggal_ambil_kontainer')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tanggal_terima_pelabuhan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Terima Pelabuhan
                                </label>
                                <input type="date"
                                       name="tanggal_terima_pelabuhan"
                                       id="tanggal_terima_pelabuhan"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_terima_pelabuhan') border-red-500 @enderror"
                                       value="{{ old('tanggal_terima_pelabuhan', $tandaTerima->tanggal_terima_pelabuhan?->format('Y-m-d')) }}">
                                @error('tanggal_terima_pelabuhan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tanggal_garasi" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Garasi
                                </label>
                                <input type="date"
                                       name="tanggal_garasi"
                                       id="tanggal_garasi"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_garasi') border-red-500 @enderror"
                                       value="{{ old('tanggal_garasi', $tandaTerima->tanggal_garasi?->format('Y-m-d')) }}">
                                @error('tanggal_garasi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Jumlah & Satuan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah
                                </label>
                                <input type="number"
                                       name="jumlah"
                                       id="jumlah"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jumlah') border-red-500 @enderror"
                                       placeholder="Masukkan jumlah"
                                       value="{{ old('jumlah', $tandaTerima->jumlah) }}"
                                       min="0"
                                       step="1">
                                @error('jumlah')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="satuan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Satuan
                                </label>
                                <input type="text"
                                       name="satuan"
                                       id="satuan"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('satuan') border-red-500 @enderror"
                                       placeholder="Contoh: Pcs, Dus, Karton"
                                       value="{{ old('satuan', $tandaTerima->satuan) }}">
                                @error('satuan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Berat Kotor -->
                        <div>
                            <label for="berat_kotor" class="block text-sm font-medium text-gray-700 mb-2">
                                Berat Kotor (Kg)
                            </label>
                            <input type="number"
                                   name="berat_kotor"
                                   id="berat_kotor"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('berat_kotor') border-red-500 @enderror"
                                   placeholder="Masukkan berat kotor"
                                   value="{{ old('berat_kotor', $tandaTerima->berat_kotor) }}"
                                   min="0"
                                   step="0.01">
                            @error('berat_kotor')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dimensi -->
                        <div>
                            <label for="dimensi" class="block text-sm font-medium text-gray-700 mb-2">
                                Dimensi
                            </label>
                            <input type="text"
                                   name="dimensi"
                                   id="dimensi"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('dimensi') border-red-500 @enderror"
                                   placeholder="Contoh: 100cm x 50cm x 40cm"
                                   value="{{ old('dimensi', $tandaTerima->dimensi) }}">
                            @error('dimensi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tujuan Pengiriman -->
                        <div>
                            <label for="tujuan_pengiriman" class="block text-sm font-medium text-gray-700 mb-2">
                                Tujuan Pengiriman
                            </label>
                            <input type="text"
                                   name="tujuan_pengiriman"
                                   id="tujuan_pengiriman"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tujuan_pengiriman') border-red-500 @enderror"
                                   placeholder="Masukkan tujuan pengiriman"
                                   value="{{ old('tujuan_pengiriman', $tandaTerima->tujuan_pengiriman) }}">
                            @error('tujuan_pengiriman')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Catatan -->
                        <div>
                            <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan
                            </label>
                            <textarea name="catatan"
                                      id="catatan"
                                      rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('catatan') border-red-500 @enderror"
                                      placeholder="Tambahkan catatan jika diperlukan">{{ old('catatan', $tandaTerima->catatan) }}</textarea>
                            @error('catatan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
                        <a href="{{ route('tanda-terima.index') }}"
                           class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition duration-200">
                            <i class="fas fa-times mr-2"></i> Batal
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
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
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $tandaTerima->no_surat_jalan }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Tanggal</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tandaTerima->tanggal_surat_jalan?->format('d F Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Supir</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tandaTerima->supir ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Jenis Barang</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $tandaTerima->jenis_barang ?: '-' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Kegiatan</dt>
                        <dd class="mt-1">
                            @php
                                $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $tandaTerima->kegiatan)
                                                ->value('nama_kegiatan') ?? $tandaTerima->kegiatan;
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
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">No. Kontainer</dt>
                        <dd class="mt-1">
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $tandaTerima->no_kontainer ?: '-' }}</code>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">No. Seal</dt>
                        <dd class="mt-1">
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $tandaTerima->no_seal ?: '-' }}</code>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Size</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tandaTerima->size ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Jumlah Kontainer</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tandaTerima->jumlah_kontainer ?: '-' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Location Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Lokasi</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Tujuan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tandaTerima->tujuan_pengiriman ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Pengirim</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tandaTerima->pengirim ?: '-' }}</dd>
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
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for kapal dropdown
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
    });
</script>
@endpush
