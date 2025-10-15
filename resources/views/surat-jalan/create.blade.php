@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Tambah Surat Jalan</h1>
                <p class="text-xs text-gray-600 mt-1">Buat surat jalan baru untuk pengiriman barang</p>
            </div>
            <a href="{{ route('surat-jalan.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        @if($selectedOrder)
        <!-- Selected Order Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg mx-4 mt-4 p-4">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-blue-400 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-blue-800">Order Terpilih</h4>
                    <div class="mt-1 text-sm text-blue-700">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                            <div><strong>No. Order:</strong> {{ $selectedOrder->nomor_order }}</div>
                            <div><strong>Tanggal:</strong> {{ $selectedOrder->tanggal_order ? $selectedOrder->tanggal_order->format('d/m/Y') : '-' }}</div>
                            <div><strong>Pengirim:</strong> {{ $selectedOrder->pengirim->nama_pengirim ?? '-' }}</div>
                            <div><strong>Jenis Barang:</strong> {{ $selectedOrder->jenisBarang->nama_barang ?? '-' }}</div>
                        </div>
                    </div>
                </div>
                <a href="{{ route('surat-jalan.select-order') }}"
                   class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                    Ganti Order
                </a>
            </div>
        </div>
        @endif

        <!-- Form -->
        <form action="{{ route('surat-jalan.store') }}" method="POST" enctype="multipart/form-data" class="p-4">
            @csrf

            @if($selectedOrder)
                <input type="hidden" name="order_id" value="{{ $selectedOrder->id }}">
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                    <div class="font-medium">Terdapat kesalahan pada form:</div>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Dasar</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat Jalan <span class="text-red-600">*</span></label>
                    <input type="date"
                           name="tanggal_surat_jalan"
                           value="{{ old('tanggal_surat_jalan', date('Y-m-d')) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_surat_jalan') border-red-500 @enderror">
                    @error('tanggal_surat_jalan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Surat Jalan <span class="text-red-600">*</span></label>
                    <div class="flex">
                        <input type="text"
                               name="no_surat_jalan"
                               value="{{ old('no_surat_jalan') }}"
                               required
                               placeholder="Contoh: SJ/2025/10/0001"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('no_surat_jalan') border-red-500 @enderror">
                        <button type="button"
                                onclick="generateNomorSuratJalan()"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-r-lg text-sm">
                            Generate
                        </button>
                    </div>
                    @error('no_surat_jalan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kegiatan <span class="text-red-600">*</span></label>
                    <select name="kegiatan"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('kegiatan') border-red-500 @enderror">
                        <option value="">Pilih Kegiatan</option>
                        @if(isset($kegiatanSuratJalan))
                            @foreach($kegiatanSuratJalan as $kegiatan)
                                <option value="{{ $kegiatan->nama_kegiatan }}" {{ old('kegiatan') == $kegiatan->nama_kegiatan ? 'selected' : '' }}>
                                    {{ $kegiatan->nama_kegiatan }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('kegiatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Data kegiatan diambil dari master kegiatan dengan type "kegiatan surat jalan"</p>
                </div>

                <!-- Pengirim Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Pengirim</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pengirim</label>
                    <input type="text"
                           name="pengirim"
                           value="{{ old('pengirim', $selectedOrder ? $selectedOrder->pengirim->nama_pengirim ?? '' : '') }}"
                           placeholder="Nama pengirim"
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none @error('pengirim') border-red-500 @enderror">
                    @error('pengirim')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($selectedOrder)
                        <p class="text-xs text-gray-500 mt-1">Data pengirim diambil dari order yang dipilih</p>
                    @endif
                </div>

                <!-- Barang Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Barang</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Barang</label>
                    <input type="text"
                           name="jenis_barang"
                           value="{{ old('jenis_barang', $selectedOrder ? $selectedOrder->jenisBarang->nama_barang ?? '' : '') }}"
                           placeholder="Jenis/nama barang"
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none @error('jenis_barang') border-red-500 @enderror">
                    @error('jenis_barang')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($selectedOrder)
                        <p class="text-xs text-gray-500 mt-1">Data jenis barang diambil dari order yang dipilih</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengambilan</label>
                    <input type="text"
                           name="tujuan_pengambilan"
                           value="{{ old('tujuan_pengambilan', $selectedOrder ? $selectedOrder->tujuan_ambil ?? '' : '') }}"
                           placeholder="Lokasi pengambilan"
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none @error('tujuan_pengambilan') border-red-500 @enderror">
                    @error('tujuan_pengambilan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($selectedOrder)
                        <p class="text-xs text-gray-500 mt-1">Data tujuan pengambilan diambil dari order yang dipilih</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengiriman</label>
                    <input type="text"
                           name="tujuan_pengiriman"
                           value="{{ old('tujuan_pengiriman', $selectedOrder ? $selectedOrder->tujuan_kirim ?? '' : '') }}"
                           placeholder="Lokasi tujuan pengiriman"
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none @error('tujuan_pengiriman') border-red-500 @enderror">
                    @error('tujuan_pengiriman')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($selectedOrder)
                        <p class="text-xs text-gray-500 mt-1">Data tujuan pengiriman diambil dari order yang dipilih</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Retur Barang</label>
                    <input type="text"
                           name="retur_barang"
                           value="{{ old('retur_barang') }}"
                           placeholder="Retur barang (jika ada)"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('retur_barang') border-red-500 @enderror">
                    @error('retur_barang')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Retur</label>
                    <input type="number"
                           name="jumlah_retur"
                           value="{{ old('jumlah_retur', 0) }}"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jumlah_retur') border-red-500 @enderror">
                    @error('jumlah_retur')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kontainer Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Kontainer</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Kontainer</label>
                    <input type="text"
                           name="tipe_kontainer"
                           value="{{ old('tipe_kontainer', $selectedOrder ? $selectedOrder->tipe_kontainer ?? '' : '') }}"
                           placeholder="Tipe kontainer"
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none @error('tipe_kontainer') border-red-500 @enderror">
                    @error('tipe_kontainer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($selectedOrder)
                        <p class="text-xs text-gray-500 mt-1">Data tipe kontainer diambil dari order yang dipilih</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Size</label>
                    <select name="size"
                            onchange="updateUangJalan()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('size') border-red-500 @enderror">
                        <option value="">Pilih Size</option>
                        @php $selectedSize = old('size', $selectedOrder ? $selectedOrder->size_kontainer ?? '' : ''); @endphp
                        <option value="20" {{ $selectedSize == '20' ? 'selected' : '' }}>20 ft</option>
                        <option value="40" {{ $selectedSize == '40' ? 'selected' : '' }}>40 ft</option>
                        <option value="45" {{ $selectedSize == '45' ? 'selected' : '' }}>45 ft</option>
                    </select>
                    @error('size')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Uang jalan akan diperbarui berdasarkan size kontainer</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kontainer</label>
                    <input type="number"
                           name="jumlah_kontainer"
                           value="{{ old('jumlah_kontainer', 1) }}"
                           min="1"
                           placeholder="Jumlah kontainer"
                           onchange="updateKontainerRules()"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jumlah_kontainer') border-red-500 @enderror">
                    @error('jumlah_kontainer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Seal</label>
                    <input type="text"
                           name="no_seal"
                           value="{{ old('no_seal') }}"
                           placeholder="Nomor seal kontainer"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('no_seal') border-red-500 @enderror">
                    @error('no_seal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Packaging Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Kemasan</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Karton</label>
                    <select name="karton"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('karton') border-red-500 @enderror">
                        <option value="">Pilih Status Karton</option>
                        <option value="pakai" {{ old('karton') == 'pakai' ? 'selected' : '' }}>Pakai</option>
                        <option value="tidak_pakai" {{ old('karton') == 'tidak_pakai' ? 'selected' : '' }}>Tidak Pakai</option>
                    </select>
                    @error('karton')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plastik</label>
                    <select name="plastik"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('plastik') border-red-500 @enderror">
                        <option value="">Pilih Status Plastik</option>
                        <option value="pakai" {{ old('plastik') == 'pakai' ? 'selected' : '' }}>Pakai</option>
                        <option value="tidak_pakai" {{ old('plastik') == 'tidak_pakai' ? 'selected' : '' }}>Tidak Pakai</option>
                    </select>
                    @error('plastik')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Terpal</label>
                    <select name="terpal"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('terpal') border-red-500 @enderror">
                        <option value="">Pilih Status Terpal</option>
                        <option value="pakai" {{ old('terpal') == 'pakai' ? 'selected' : '' }}>Pakai</option>
                        <option value="tidak_pakai" {{ old('terpal') == 'tidak_pakai' ? 'selected' : '' }}>Tidak Pakai</option>
                    </select>
                    @error('terpal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transport Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Transport</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir</label>
                    <select name="supir"
                            id="supir-select"
                            onchange="updateNoPlat()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('supir') border-red-500 @enderror">
                        <option value="">Pilih Supir</option>
                        @if(isset($supirs))
                            @foreach($supirs as $supir)
                                <option value="{{ $supir->nama_lengkap }}"
                                        data-plat="{{ $supir->plat }}"
                                        {{ old('supir') == $supir->nama_lengkap ? 'selected' : '' }}>
                                    {{ $supir->nama_lengkap }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('supir')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                    <select name="kenek"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('kenek') border-red-500 @enderror">
                        <option value="">Pilih Kenek</option>
                        @if(isset($keneks))
                            @foreach($keneks as $kenek)
                                <option value="{{ $kenek->nama_lengkap }}"
                                        {{ old('kenek') == $kenek->nama_lengkap ? 'selected' : '' }}>
                                    {{ $kenek->nama_lengkap }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('kenek')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Data kenek diambil dari master karyawan divisi krani</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Plat</label>
                    <input type="text"
                           name="no_plat"
                           id="no-plat-input"
                           value="{{ old('no_plat') }}"
                           placeholder="Nomor plat kendaraan"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('no_plat') border-red-500 @enderror">
                    @error('no_plat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">No. plat akan otomatis terisi berdasarkan supir yang dipilih</p>
                </div>

                <!-- Schedule Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Jadwal</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Muat</label>
                    <input type="date"
                           name="tanggal_muat"
                           value="{{ old('tanggal_muat') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_muat') border-red-500 @enderror">
                    @error('tanggal_muat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berangkat</label>
                    <input type="date"
                           name="tanggal_berangkat"
                           value="{{ old('tanggal_berangkat') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_berangkat') border-red-500 @enderror">
                    @error('tanggal_berangkat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Additional Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Tambahan</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                    <input type="text"
                           name="term"
                           value="{{ old('term', $selectedOrder && $selectedOrder->term ? $selectedOrder->term->nama_status ?? '' : '') }}"
                           placeholder="Term pembayaran"
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none @error('term') border-red-500 @enderror">
                    @error('term')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($selectedOrder)
                        <p class="text-xs text-gray-500 mt-1">Data term diambil dari order yang dipilih</p>
                        @if($selectedOrder->term)
                            <p class="text-xs text-green-600 mt-1">Term ditemukan: {{ $selectedOrder->term->nama_status }}</p>
                        @else
                            <p class="text-xs text-red-600 mt-1">Term tidak ditemukan untuk order ini</p>
                        @endif
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rit</label>
                    <select name="rit"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('rit') border-red-500 @enderror">
                        <option value="">Pilih Status Rit</option>
                        <option value="menggunakan_rit" {{ old('rit') == 'menggunakan_rit' ? 'selected' : '' }}>Menggunakan Rit</option>
                        <option value="tidak_menggunakan_rit" {{ old('rit') == 'tidak_menggunakan_rit' ? 'selected' : '' }}>Tidak Menggunakan Rit</option>
                    </select>
                    @error('rit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Uang Jalan</label>
                    <input type="text"
                           name="uang_jalan"
                           id="uang-jalan-input"
                           value="{{ old('uang_jalan', '0') }}"
                           placeholder="0"
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none @error('uang_jalan') border-red-500 @enderror">
                    @error('uang_jalan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Uang jalan otomatis berdasarkan tujuan pengambilan (Format: 200,000)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Pemesanan</label>
                    <input type="text"
                           name="no_pemesanan"
                           value="{{ old('no_pemesanan', $selectedOrder ? $selectedOrder->nomor_order ?? '' : '') }}"
                           placeholder="Nomor pemesanan"
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none @error('no_pemesanan') border-red-500 @enderror">
                    @error('no_pemesanan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($selectedOrder)
                        <p class="text-xs text-gray-500 mt-1">Nomor pemesanan diambil dari nomor order yang dipilih</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar/Dokumen</label>
                    <input type="file"
                           name="gambar"
                           accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('gambar') border-red-500 @enderror">
                    @error('gambar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Max: 2MB</p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('surat-jalan.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-150">
                    Batal
                </a>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition-colors duration-150">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function generateNomorSuratJalan() {
    fetch('{{ route("surat-jalan.generate-nomor") }}')
        .then(response => response.json())
        .then(data => {
            document.querySelector('input[name="no_surat_jalan"]').value = data.no_surat_jalan;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal generate nomor surat jalan');
        });
}

function updateNoPlat() {
    const supirSelect = document.getElementById('supir-select');
    const noPlatInput = document.getElementById('no-plat-input');

    if (supirSelect.selectedIndex > 0) {
        const selectedOption = supirSelect.options[supirSelect.selectedIndex];
        const platNumber = selectedOption.getAttribute('data-plat');

        if (platNumber) {
            noPlatInput.value = platNumber;
        } else {
            noPlatInput.value = '';
        }
    } else {
        noPlatInput.value = '';
    }
}

function updateUangJalan() {
    const tujuanPengambilan = document.querySelector('input[name="tujuan_pengambilan"]').value;
    const sizeSelect = document.querySelector('select[name="size"]');
    const uangJalanInput = document.getElementById('uang-jalan-input');
    const jumlahKontainer = parseInt(document.querySelector('input[name="jumlah_kontainer"]').value) || 1;

    if (tujuanPengambilan) {
        let size = sizeSelect ? sizeSelect.value : '';

        // Jika jumlah kontainer = 2, gunakan perhitungan untuk 40ft meskipun size 20ft
        let calculationSize = size;
        if (jumlahKontainer === 2 && size === '20') {
            calculationSize = '40';
        }

        // Fetch uang jalan based on tujuan pengambilan and container size
        fetch('/api/get-uang-jalan-by-tujuan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                tujuan: tujuanPengambilan,
                size: calculationSize
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                uangJalanInput.value = data.uang_jalan || '0';
            } else {
                uangJalanInput.value = '0';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            uangJalanInput.value = '0';
        });
    } else {
        uangJalanInput.value = '0';
    }
}

function updateKontainerRules() {
    const jumlahKontainer = parseInt(document.querySelector('input[name="jumlah_kontainer"]').value) || 1;
    const sizeSelect = document.querySelector('select[name="size"]');

    if (jumlahKontainer === 2) {
        // Jika jumlah kontainer = 2, set size ke 20ft dan disable
        sizeSelect.value = '20';
        sizeSelect.disabled = true;
        sizeSelect.style.backgroundColor = '#F3F4F6';
        sizeSelect.style.color = '#6B7280';

        // Update uang jalan
        updateUangJalan();

        // Tambahkan keterangan
        let existingNote = document.getElementById('kontainer-rule-note');
        if (!existingNote) {
            const note = document.createElement('p');
            note.id = 'kontainer-rule-note';
            note.className = 'text-xs text-blue-600 mt-1';
            note.innerHTML = '<strong>Catatan:</strong> Untuk 2 kontainer, size otomatis 20ft dengan tarif 40ft';
            sizeSelect.parentNode.appendChild(note);
        }
    } else {
        // Jika bukan 2 kontainer, enable kembali size select
        sizeSelect.disabled = false;
        sizeSelect.style.backgroundColor = '';
        sizeSelect.style.color = '';

        // Hapus keterangan jika ada
        const existingNote = document.getElementById('kontainer-rule-note');
        if (existingNote) {
            existingNote.remove();
        }

        // Update uang jalan
        updateUangJalan();
    }
}

// Auto-populate uang jalan when page loads if order is selected
@if($selectedOrder && $selectedOrder->tujuan_ambil)
document.addEventListener('DOMContentLoaded', function() {
    updateUangJalan();
    updateKontainerRules(); // Check kontainer rules on load
});
@else
document.addEventListener('DOMContentLoaded', function() {
    updateKontainerRules(); // Check kontainer rules on load
});
@endif
</script>
@endsection
