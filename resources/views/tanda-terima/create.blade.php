@extends('layouts.app')

@section('title', 'Tambah Tanda Terima Manual')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Tanda Terima Manual</h1>
                <p class="text-gray-600 mt-1">Input tanda terima kontainer tanpa surat jalan</p>
            </div>
            <a href="{{ route('tanda-terima.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Form Tanda Terima</h2>
            <p class="text-sm text-gray-500 mt-1">Isi semua field yang diperlukan</p>
        </div>

        <form action="{{ route('tanda-terima.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terdapat beberapa kesalahan:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <!-- Info Alert -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-800">
                            <strong>Catatan:</strong> Tanda terima yang dibuat manual tidak terhubung dengan surat jalan.
                            Gunakan fitur ini hanya untuk tanda terima khusus.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Informasi Dasar Surat Jalan -->
            <div class="mb-8">
                <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                    <i class="fas fa-file-alt text-blue-600 mr-2"></i>
                    Informasi Surat Jalan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informasi Surat Jalan Table -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            Informasi Surat Jalan
                        </label>

                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No. Surat Jalan
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal Surat Jalan
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Supir
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="text"
                                                   name="no_surat_jalan"
                                                   id="no_surat_jalan"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('no_surat_jalan') border-red-500 @enderror"
                                                   value="{{ old('no_surat_jalan') }}"
                                                   placeholder="Nomor surat jalan"
                                                   required>
                                            @error('no_surat_jalan')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="date"
                                                   name="tanggal_surat_jalan"
                                                   id="tanggal_surat_jalan"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_surat_jalan') border-red-500 @enderror"
                                                   value="{{ old('tanggal_surat_jalan') }}"
                                                   required>
                                            @error('tanggal_surat_jalan')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="text"
                                                   name="supir"
                                                   id="supir"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('supir') border-red-500 @enderror"
                                                   value="{{ old('supir') }}"
                                                   placeholder="Nama supir">
                                            @error('supir')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>                    <!-- Informasi Kontainer Table -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            Informasi Kontainer
                        </label>

                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No. Kontainer
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No. Seal
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Size
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jumlah
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 align-top">
                                            <textarea name="no_kontainer"
                                                      id="no_kontainer"
                                                      rows="3"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('no_kontainer') border-red-500 @enderror"
                                                      placeholder="Pisahkan dengan koma jika lebih dari 1">{{ old('no_kontainer') }}</textarea>
                                            <p class="mt-1 text-xs text-gray-500">Contoh: AYPU0033890, AYPU0033891</p>
                                            @error('no_kontainer')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            <input type="text"
                                                   name="no_seal"
                                                   id="no_seal"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('no_seal') border-red-500 @enderror"
                                                   value="{{ old('no_seal') }}"
                                                   placeholder="Nomor seal">
                                            @error('no_seal')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            <select name="size"
                                                    id="size"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('size') border-red-500 @enderror">
                                                <option value="">-- Pilih Size --</option>
                                                <option value="20" {{ old('size') == '20' ? 'selected' : '' }}>20 Feet</option>
                                                <option value="40" {{ old('size') == '40' ? 'selected' : '' }}>40 Feet</option>
                                                <option value="45" {{ old('size') == '45' ? 'selected' : '' }}>45 Feet</option>
                                            </select>
                                            @error('size')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            <input type="number"
                                                   name="jumlah_kontainer"
                                                   id="jumlah_kontainer"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('jumlah_kontainer') border-red-500 @enderror"
                                                   value="{{ old('jumlah_kontainer', 1) }}"
                                                   min="1"
                                                   placeholder="1">
                                            @error('jumlah_kontainer')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Informasi Pengiriman Table -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            Informasi Pengiriman
                        </label>

                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tujuan Pengiriman
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pengirim
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
                                                   value="{{ old('tujuan_pengiriman') }}"
                                                   placeholder="Tujuan pengiriman">
                                            @error('tujuan_pengiriman')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            <input type="text"
                                                   name="pengirim"
                                                   id="pengirim"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('pengirim') border-red-500 @enderror"
                                                   value="{{ old('pengirim') }}"
                                                   placeholder="Nama pengirim">
                                            @error('pengirim')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Informasi Kuantitas Table -->
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
                                                   value="{{ old('jumlah') }}"
                                                   placeholder="Jumlah"
                                                   min="0">
                                            @error('jumlah')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="text"
                                                   name="satuan"
                                                   id="satuan"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('satuan') border-red-500 @enderror"
                                                   value="{{ old('satuan') }}"
                                                   placeholder="Contoh: Kg, Ton, Unit">
                                            @error('satuan')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Kapal & Jadwal -->
            <div class="mb-8">
                <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                    <i class="fas fa-ship text-indigo-600 mr-2"></i>
                    Informasi Kapal & Jadwal
                </h3>
                <div class="space-y-6">
                    <!-- Estimasi Kapal Table -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            Estimasi Kapal
                        </label>

                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Kapal
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <select name="estimasi_nama_kapal"
                                                    id="estimasi_nama_kapal"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm select2-kapal @error('estimasi_nama_kapal') border-red-500 @enderror">
                                                <option value="">-- Pilih Kapal --</option>
                                                @foreach($masterKapals as $kapal)
                                                    <option value="{{ $kapal->nama_kapal }}" {{ old('estimasi_nama_kapal') == $kapal->nama_kapal ? 'selected' : '' }}>
                                                        {{ $kapal->nama_kapal }}
                                                        @if($kapal->nickname) ({{ $kapal->nickname }}) @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('estimasi_nama_kapal')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="date"
                                                   name="estimasi_tanggal_kapal"
                                                   id="estimasi_tanggal_kapal"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('estimasi_tanggal_kapal') border-red-500 @enderror"
                                                   value="{{ old('estimasi_tanggal_kapal') }}">
                                            @error('estimasi_tanggal_kapal')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Jadwal Pengambilan Table -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            Jadwal Pengambilan & Penerimaan
                        </label>

                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tgl Ambil Kontainer
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tgl Terima Pelabuhan
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tgl Garasi
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
                                                   value="{{ old('tanggal_ambil_kontainer') }}">
                                            @error('tanggal_ambil_kontainer')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="date"
                                                   name="tanggal_terima_pelabuhan"
                                                   id="tanggal_terima_pelabuhan"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_terima_pelabuhan') border-red-500 @enderror"
                                                   value="{{ old('tanggal_terima_pelabuhan') }}">
                                            @error('tanggal_terima_pelabuhan')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="date"
                                                   name="tanggal_garasi"
                                                   id="tanggal_garasi"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_garasi') border-red-500 @enderror"
                                                   value="{{ old('tanggal_garasi') }}">
                                            @error('tanggal_garasi')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Muatan -->
            <div class="mb-8">
                <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                    <i class="fas fa-weight text-orange-600 mr-2"></i>
                    Informasi Muatan
                </h3>
                <div class="space-y-6">
                    <!-- Jumlah -->
                    <div>
                        <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-2">
                            Jumlah
                        </label>
                        <input type="number"
                               name="jumlah"
                               id="jumlah"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jumlah') border-red-500 @enderror"
                               value="{{ old('jumlah') }}"
                               min="0"
                               step="0.01">
                        @error('jumlah')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Satuan -->
                    <div>
                        <label for="satuan" class="block text-sm font-medium text-gray-700 mb-2">
                            Satuan
                        </label>
                        <input type="text"
                               name="satuan"
                               id="satuan"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('satuan') border-red-500 @enderror"
                               value="{{ old('satuan') }}"
                               placeholder="Contoh: Kg, Ton, Unit">
                        @error('satuan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                                    <!-- Default first item -->
                                    <tr class="dimensi-item hover:bg-gray-50" data-index="0">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="item-number text-sm font-medium text-gray-900">1</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="number"
                                                   name="dimensi_items[0][panjang]"
                                                   class="dimensi-panjang w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                                   placeholder="0"
                                                   value="{{ old('dimensi_items.0.panjang') }}"
                                                   min="0"
                                                   step="0.01"
                                                   onchange="calculateItemVolume(this)">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="number"
                                                   name="dimensi_items[0][lebar]"
                                                   class="dimensi-lebar w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                                   placeholder="0"
                                                   value="{{ old('dimensi_items.0.lebar') }}"
                                                   min="0"
                                                   step="0.01"
                                                   onchange="calculateItemVolume(this)">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="number"
                                                   name="dimensi_items[0][tinggi]"
                                                   class="dimensi-tinggi w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                                   placeholder="0"
                                                   value="{{ old('dimensi_items.0.tinggi') }}"
                                                   min="0"
                                                   step="0.01"
                                                   onchange="calculateItemVolume(this)">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="number"
                                                   name="dimensi_items[0][meter_kubik]"
                                                   class="item-meter-kubik w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm"
                                                   placeholder="0.000000"
                                                   value="{{ old('dimensi_items.0.meter_kubik') }}"
                                                   readonly
                                                   step="0.000001">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="number"
                                                   name="dimensi_items[0][tonase]"
                                                   class="dimensi-tonase w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                                   placeholder="0.00"
                                                   value="{{ old('dimensi_items.0.tonase') }}"
                                                   min="0"
                                                   step="0.01"
                                                   onchange="calculateTotals()">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <!-- First item can't be removed -->
                                        </td>
                                    </tr>
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
                        <input type="hidden" name="panjang" id="hiddenPanjang" value="{{ old('panjang') }}">
                        <input type="hidden" name="lebar" id="hiddenLebar" value="{{ old('lebar') }}">
                        <input type="hidden" name="tinggi" id="hiddenTinggi" value="{{ old('tinggi') }}">
                        <input type="hidden" name="meter_kubik" id="hiddenMeterKubik" value="{{ old('meter_kubik') }}">
                        <input type="hidden" name="tonase" id="hiddenTonase" value="{{ old('tonase') }}">
                    </div>
                </div>
            </div>

            <!-- Gambar Checkpoint -->
            <div class="mb-8">
                <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                    <i class="fas fa-camera text-red-600 mr-2"></i>
                    Gambar Checkpoint
                </h3>
                <div>
                    <label for="gambar_checkpoint" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Gambar
                    </label>
                    <input type="file"
                           name="gambar_checkpoint"
                           id="gambar_checkpoint"
                           accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('gambar_checkpoint') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, GIF (Max: 2MB)</p>
                    @error('gambar_checkpoint')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('tanda-terima.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200 shadow-sm">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Tanda Terima
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Auto-calculate jumlah_kontainer from no_kontainer
    document.getElementById('no_kontainer').addEventListener('input', function() {
        const value = this.value.trim();
        if (value) {
            const containers = value.split(',').filter(item => item.trim() !== '');
            document.getElementById('jumlah_kontainer').value = containers.length;
        } else {
            document.getElementById('jumlah_kontainer').value = 1;
        }
    });

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

let dimensiItemIndex = 1;

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
}// Legacy function for backward compatibility
function calculateMeterKubik() {
    calculateAllVolumesAndTotals();
}
</script>
@endpush

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

@endsection
