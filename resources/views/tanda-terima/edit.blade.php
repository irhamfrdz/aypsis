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
                    @method('PUT')>

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

                        <!-- Tanggal Section Table -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Informasi Tanggal
                            </label>

                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tanggal Ambil Kontainer
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tanggal Terima Pelabuhan
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tanggal Garasi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input type="date"
                                                       name="tanggal_ambil_kontainer"
                                                       id="tanggal_ambil_kontainer"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_ambil_kontainer') border-red-500 @enderror"
                                                       value="{{ old('tanggal_ambil_kontainer', $tandaTerima->tanggal_ambil_kontainer?->format('Y-m-d')) }}">
                                                @error('tanggal_ambil_kontainer')
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input type="date"
                                                       name="tanggal_terima_pelabuhan"
                                                       id="tanggal_terima_pelabuhan"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_terima_pelabuhan') border-red-500 @enderror"
                                                       value="{{ old('tanggal_terima_pelabuhan', $tandaTerima->tanggal_terima_pelabuhan?->format('Y-m-d')) }}">
                                                @error('tanggal_terima_pelabuhan')
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input type="date"
                                                       name="tanggal_garasi"
                                                       id="tanggal_garasi"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_garasi') border-red-500 @enderror"
                                                       value="{{ old('tanggal_garasi', $tandaTerima->tanggal_garasi?->format('Y-m-d')) }}">
                                                @error('tanggal_garasi')
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Jumlah & Satuan Table -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Informasi Kuantitas
                            </label>

                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Jumlah
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Satuan
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input type="number"
                                                       name="jumlah"
                                                       id="jumlah"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('jumlah') border-red-500 @enderror"
                                                       placeholder="Masukkan jumlah"
                                                       value="{{ old('jumlah', $tandaTerima->jumlah) }}"
                                                       min="0"
                                                       step="1">
                                                @error('jumlah')
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input type="text"
                                                       name="satuan"
                                                       id="satuan"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('satuan') border-red-500 @enderror"
                                                       placeholder="Contoh: Pcs, Dus, Karton"
                                                       value="{{ old('satuan', $tandaTerima->satuan) }}">
                                                @error('satuan')
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Dimensi & Volume Table -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <label class="block text-sm font-medium text-gray-700">
                                    Dimensi & Volume Items
                                </label>
                                <button type="button" id="addDimensiItem" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition duration-200">
                                    <i class="fas fa-plus mr-2"></i> Tambah Item
                                </button>
                            </div>

                            <!-- Table Container -->
                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                                No.
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Panjang (cm)
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Lebar (cm)
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tinggi (cm)
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Volume (m³)
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tonase (Ton)
                                            </th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="dimensiTableBody" class="bg-white divide-y divide-gray-200">
                                        <!-- Template for existing data -->
                                        @php
                                            $existingDimensi = [];
                                            if($tandaTerima->panjang || $tandaTerima->lebar || $tandaTerima->tinggi) {
                                                $existingDimensi[] = [
                                                    'panjang' => $tandaTerima->panjang,
                                                    'lebar' => $tandaTerima->lebar,
                                                    'tinggi' => $tandaTerima->tinggi,
                                                    'meter_kubik' => $tandaTerima->meter_kubik,
                                                    'tonase' => $tandaTerima->tonase
                                                ];
                                            }
                                            // If no existing data, create one empty item
                                            if(empty($existingDimensi)) {
                                                $existingDimensi[] = [
                                                    'panjang' => old('dimensi_items.0.panjang'),
                                                    'lebar' => old('dimensi_items.0.lebar'),
                                                    'tinggi' => old('dimensi_items.0.tinggi'),
                                                    'meter_kubik' => old('dimensi_items.0.meter_kubik'),
                                                    'tonase' => old('dimensi_items.0.tonase')
                                                ];
                                            }
                                        @endphp

                                        @foreach($existingDimensi as $index => $item)
                                            <tr class="dimensi-item hover:bg-gray-50" data-index="{{ $index }}">
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <span class="item-number text-sm font-medium text-gray-900">{{ $index + 1 }}</span>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="number"
                                                           name="dimensi_items[{{ $index }}][panjang]"
                                                           class="dimensi-panjang w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('dimensi_items.'.$index.'.panjang') border-red-500 @enderror"
                                                           placeholder="0"
                                                           value="{{ old('dimensi_items.'.$index.'.panjang', $item['panjang']) }}"
                                                           min="0"
                                                           step="0.01"
                                                           onchange="calculateItemVolume(this)">
                                                    @error('dimensi_items.'.$index.'.panjang')
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="number"
                                                           name="dimensi_items[{{ $index }}][lebar]"
                                                           class="dimensi-lebar w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('dimensi_items.'.$index.'.lebar') border-red-500 @enderror"
                                                           placeholder="0"
                                                           value="{{ old('dimensi_items.'.$index.'.lebar', $item['lebar']) }}"
                                                           min="0"
                                                           step="0.01"
                                                           onchange="calculateItemVolume(this)">
                                                    @error('dimensi_items.'.$index.'.lebar')
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="number"
                                                           name="dimensi_items[{{ $index }}][tinggi]"
                                                           class="dimensi-tinggi w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('dimensi_items.'.$index.'.tinggi') border-red-500 @enderror"
                                                           placeholder="0"
                                                           value="{{ old('dimensi_items.'.$index.'.tinggi', $item['tinggi']) }}"
                                                           min="0"
                                                           step="0.01"
                                                           onchange="calculateItemVolume(this)">
                                                    @error('dimensi_items.'.$index.'.tinggi')
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="number"
                                                           name="dimensi_items[{{ $index }}][meter_kubik]"
                                                           class="item-meter-kubik w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm @error('dimensi_items.'.$index.'.meter_kubik') border-red-500 @enderror"
                                                           placeholder="0.000000"
                                                           value="{{ old('dimensi_items.'.$index.'.meter_kubik', $item['meter_kubik']) }}"
                                                           readonly
                                                           step="0.000001">
                                                    @error('dimensi_items.'.$index.'.meter_kubik')
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="number"
                                                           name="dimensi_items[{{ $index }}][tonase]"
                                                           class="dimensi-tonase w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('dimensi_items.'.$index.'.tonase') border-red-500 @enderror"
                                                           placeholder="0.00"
                                                           value="{{ old('dimensi_items.'.$index.'.tonase', $item['tonase']) }}"
                                                           min="0"
                                                           step="0.01"
                                                           onchange="calculateTotals()">
                                                    @error('dimensi_items.'.$index.'.tonase')
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    @if($index > 0)
                                                        <button type="button" class="remove-dimensi-item text-red-600 hover:text-red-800 p-1">
                                                            <i class="fas fa-trash text-sm"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <!-- Table Footer with Totals -->
                                    <tfoot class="bg-blue-50">
                                        <tr>
                                            <td colspan="4" class="px-4 py-3 text-right font-medium text-gray-900">
                                                Total:
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span id="totalVolume" class="font-semibold text-blue-900">0.000000 m³</span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span id="totalTonase" class="font-semibold text-blue-900">0.00 Ton</span>
                                            </td>
                                            <td class="px-4 py-3"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Hidden fields for backward compatibility -->
                            <input type="hidden" name="panjang" id="hiddenPanjang">
                            <input type="hidden" name="lebar" id="hiddenLebar">
                            <input type="hidden" name="tinggi" id="hiddenTinggi">
                            <input type="hidden" name="meter_kubik" id="hiddenMeterKubik">
                            <input type="hidden" name="tonase" id="hiddenTonase">
                        </div>

                        <!-- Informasi Tambahan Table -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Informasi Tambahan
                            </label>

                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tujuan Pengiriman
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Catatan
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 align-top">
                                                <input type="text"
                                                       name="tujuan_pengiriman"
                                                       id="tujuan_pengiriman"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tujuan_pengiriman') border-red-500 @enderror"
                                                       placeholder="Masukkan tujuan pengiriman"
                                                       value="{{ old('tujuan_pengiriman', $tandaTerima->tujuan_pengiriman) }}">
                                                @error('tujuan_pengiriman')
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-3 align-top">
                                                <textarea name="catatan"
                                                          id="catatan"
                                                          rows="3"
                                                          class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('catatan') border-red-500 @enderror"
                                                          placeholder="Tambahkan catatan jika diperlukan">{{ old('catatan', $tandaTerima->catatan) }}</textarea>
                                                @error('catatan')
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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

        // Calculate initial volumes and totals
        calculateAllVolumesAndTotals();

        // Add new dimensi item
        $('#addDimensiItem').click(function() {
            addNewDimensiItem();
        });

        // Remove dimensi item
        $(document).on('click', '.remove-dimensi-item', function() {
            $(this).closest('.dimensi-item').remove();
            updateItemNumbers();
            calculateAllVolumesAndTotals();
        });
    });

    let dimensiItemIndex = {{ count($existingDimensi) }};

    function addNewDimensiItem() {
        const newRow = `
            <tr class="dimensi-item hover:bg-gray-50" data-index="${dimensiItemIndex}">
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="item-number text-sm font-medium text-gray-900">${dimensiItemIndex + 1}</span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <input type="number"
                           name="dimensi_items[${dimensiItemIndex}][panjang]"
                           class="dimensi-panjang w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                           placeholder="0"
                           min="0"
                           step="0.01"
                           onchange="calculateItemVolume(this)">
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <input type="number"
                           name="dimensi_items[${dimensiItemIndex}][lebar]"
                           class="dimensi-lebar w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                           placeholder="0"
                           min="0"
                           step="0.01"
                           onchange="calculateItemVolume(this)">
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <input type="number"
                           name="dimensi_items[${dimensiItemIndex}][tinggi]"
                           class="dimensi-tinggi w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                           placeholder="0"
                           min="0"
                           step="0.01"
                           onchange="calculateItemVolume(this)">
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <input type="number"
                           name="dimensi_items[${dimensiItemIndex}][meter_kubik]"
                           class="item-meter-kubik w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm"
                           placeholder="0.000000"
                           readonly
                           step="0.000001">
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <input type="number"
                           name="dimensi_items[${dimensiItemIndex}][tonase]"
                           class="dimensi-tonase w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                           placeholder="0.00"
                           min="0"
                           step="0.01"
                           onchange="calculateTotals()">
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-center">
                    <button type="button" class="remove-dimensi-item text-red-600 hover:text-red-800 p-1">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </td>
            </tr>`;

        $('#dimensiTableBody').append(newRow);
        dimensiItemIndex++;
        updateItemNumbers();
    }

    function updateItemNumbers() {
        $('#dimensiTableBody .dimensi-item').each(function(index) {
            $(this).find('.item-number').text(index + 1);
            $(this).attr('data-index', index);
        });
    }

    function calculateItemVolume(element) {
        const row = $(element).closest('.dimensi-item');
        const panjang = parseFloat(row.find('.dimensi-panjang').val()) || 0;
        const lebar = parseFloat(row.find('.dimensi-lebar').val()) || 0;
        const tinggi = parseFloat(row.find('.dimensi-tinggi').val()) || 0;

        let volume = 0;
        if (panjang > 0 && lebar > 0 && tinggi > 0) {
            volume = (panjang * lebar * tinggi) / 1000000;
        }

        row.find('.item-meter-kubik').val(volume > 0 ? volume.toFixed(6) : '');
        calculateTotals();
    }

    function calculateAllVolumesAndTotals() {
        $('#dimensiTableBody .dimensi-item').each(function() {
            const row = $(this);
            const panjang = parseFloat(row.find('.dimensi-panjang').val()) || 0;
            const lebar = parseFloat(row.find('.dimensi-lebar').val()) || 0;
            const tinggi = parseFloat(row.find('.dimensi-tinggi').val()) || 0;

            let volume = 0;
            if (panjang > 0 && lebar > 0 && tinggi > 0) {
                volume = (panjang * lebar * tinggi) / 1000000;
            }

            row.find('.item-meter-kubik').val(volume > 0 ? volume.toFixed(6) : '');
        });
        calculateTotals();
    }

    function calculateTotals() {
        let totalVolume = 0;
        let totalTonase = 0;

        $('#dimensiTableBody .dimensi-item').each(function() {
            const volume = parseFloat($(this).find('.item-meter-kubik').val()) || 0;
            const tonase = parseFloat($(this).find('.dimensi-tonase').val()) || 0;

            totalVolume += volume;
            totalTonase += tonase;
        });

        // Update summary display
        $('#totalVolume').text(totalVolume.toFixed(6) + ' m³');
        $('#totalTonase').text(totalTonase.toFixed(2) + ' Ton');

        // Update hidden fields for backward compatibility
        // Use first item's values or totals
        const firstRow = $('#dimensiTableBody .dimensi-item').first();
        if (firstRow.length) {
            $('#hiddenPanjang').val(firstRow.find('.dimensi-panjang').val() || '');
            $('#hiddenLebar').val(firstRow.find('.dimensi-lebar').val() || '');
            $('#hiddenTinggi').val(firstRow.find('.dimensi-tinggi').val() || '');
        }
        $('#hiddenMeterKubik').val(totalVolume > 0 ? totalVolume.toFixed(6) : '');
        $('#hiddenTonase').val(totalTonase > 0 ? totalTonase.toFixed(2) : '');
    }

    // Legacy function for backward compatibility
    function calculateMeterKubik() {
        calculateAllVolumesAndTotals();
    }
</script>
@endpush
