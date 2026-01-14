@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Buat Invoice Aktivitas Lain</h1>
                <p class="text-gray-600 mt-1">Tambah invoice baru untuk aktivitas lain</p>
            </div>
            <a href="{{ route('invoice-aktivitas-lain.index') }}" 
               class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('invoice-aktivitas-lain.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Informasi Umum -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Informasi Umum</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nomor Invoice -->
                <div>
                    <label for="nomor_invoice" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Invoice <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                                   name="nomor_invoice" 
                                   id="nomor_invoice" 
                                   value="{{ old('nomor_invoice') }}"
                                   class="w-full {{ $errors->has('nomor_invoice') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm bg-gray-50"
                                   style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                                   placeholder="Loading..."
                                   readonly
                                   required>
                        <div id="invoice_loader" class="absolute right-3 top-1/2 -translate-y-1/2">
                            <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    @error('nomor_invoice')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Invoice -->
                <div>
                    <label for="tanggal_invoice" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Invoice <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="tanggal_invoice" 
                           id="tanggal_invoice" 
                           value="{{ old('tanggal_invoice', date('Y-m-d')) }}"
                           class="w-full {{ $errors->has('tanggal_invoice') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                           required>
                    @error('tanggal_invoice')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Aktivitas -->
                <div>
                    <label for="jenis_aktivitas" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Aktivitas <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_aktivitas" 
                            id="jenis_aktivitas" 
                            class="w-full {{ $errors->has('jenis_aktivitas') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                            required>
                        <option value="">Pilih Jenis Aktivitas</option>
                        <option value="Pembayaran Kendaraan" {{ old('jenis_aktivitas') == 'Pembayaran Kendaraan' ? 'selected' : '' }}>Pembayaran Kendaraan</option>
                        <option value="Pembayaran Kapal" {{ old('jenis_aktivitas') == 'Pembayaran Kapal' ? 'selected' : '' }}>Pembayaran Kapal</option>
                        <option value="Pembayaran Adjustment Uang Jalan" {{ old('jenis_aktivitas') == 'Pembayaran Adjustment Uang Jalan' ? 'selected' : '' }}>Pembayaran Adjustment Uang Jalan</option>
                        <option value="Pembayaran Lain-lain" {{ old('jenis_aktivitas') == 'Pembayaran Lain-lain' ? 'selected' : '' }}>Pembayaran Lain-lain</option>
                    </select>
                    @error('jenis_aktivitas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Biaya (conditional for Pembayaran Lain-lain) -->
                <div id="jenis_biaya_wrapper" class="hidden">
                    <label for="jenis_biaya_dropdown" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Biaya
                    </label>
                    <select name="klasifikasi_biaya_umum_id" 
                            id="jenis_biaya_dropdown" 
                            class="w-full {{ $errors->has('klasifikasi_biaya_umum_id') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                        <option value="">Pilih Jenis Biaya</option>
                        @foreach($klasifikasiBiayas as $klasifikasi)
                            <option value="{{ $klasifikasi->id }}" {{ old('klasifikasi_biaya_umum_id') == $klasifikasi->id ? 'selected' : '' }}>
                                {{ $klasifikasi->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('klasifikasi_biaya_umum_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Referensi (conditional for Pembayaran Lain-lain) -->
                <div id="referensi_wrapper" class="hidden">
                    <label for="referensi" class="block text-sm font-medium text-gray-700 mb-2">
                        Referensi
                    </label>
                    <input type="text" 
                           name="referensi" 
                           id="referensi" 
                           class="w-full {{ $errors->has('referensi') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                           value="{{ old('referensi') }}"
                           placeholder="Masukkan referensi (opsional)">
                    @error('referensi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sub Jenis Kendaraan (conditional) -->
                <div id="sub_jenis_kendaraan_wrapper" class="hidden">
                    <label for="sub_jenis_kendaraan" class="block text-sm font-medium text-gray-700 mb-2">
                        Sub Jenis Kendaraan <span class="text-red-500">*</span>
                    </label>
                    <select name="sub_jenis_kendaraan" 
                            id="sub_jenis_kendaraan" 
                            class="w-full {{ $errors->has('sub_jenis_kendaraan') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                        <option value="">Pilih Sub Jenis Kendaraan</option>
                        <option value="STNK" {{ old('sub_jenis_kendaraan') == 'STNK' ? 'selected' : '' }}>STNK</option>
                        <option value="KIR" {{ old('sub_jenis_kendaraan') == 'KIR' ? 'selected' : '' }}>KIR</option>
                        <option value="PLAT" {{ old('sub_jenis_kendaraan') == 'PLAT' ? 'selected' : '' }}>PLAT</option>
                        <option value="Lain-lain" {{ old('sub_jenis_kendaraan') == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                    </select>
                    @error('sub_jenis_kendaraan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Polisi (conditional) -->
                <div id="nomor_polisi_wrapper" class="hidden">
                    <label for="nomor_polisi" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Polisi <span class="text-red-500">*</span>
                    </label>
                    <select name="nomor_polisi" 
                            id="nomor_polisi" 
                            class="w-full {{ $errors->has('nomor_polisi') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                        <option value="">Pilih Nomor Polisi</option>
                        @foreach($mobils as $mobil)
                            <option value="{{ $mobil->nomor_polisi }}" {{ old('nomor_polisi') == $mobil->nomor_polisi ? 'selected' : '' }}>
                                {{ $mobil->nomor_polisi }} - {{ $mobil->merek }} {{ $mobil->jenis }}
                            </option>
                        @endforeach
                    </select>
                    @error('nomor_polisi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Voyage (conditional) -->
                <div id="nomor_voyage_wrapper" class="hidden">
                    <label for="nomor_voyage" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Voyage <span class="text-red-500">*</span>
                    </label>
                    <select name="nomor_voyage" 
                            id="nomor_voyage" 
                            class="w-full {{ $errors->has('nomor_voyage') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                        <option value="">Pilih Nomor Voyage</option>
                        @foreach($voyages as $voyage)
                            <option value="{{ $voyage->voyage }}" {{ old('nomor_voyage') == $voyage->voyage ? 'selected' : '' }}>
                                {{ $voyage->voyage }} - {{ $voyage->nama_kapal }} ({{ $voyage->source }})
                            </option>
                        @endforeach
                    </select>
                    @error('nomor_voyage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Invoice Vendor (conditional for Pembayaran Kapal) -->
                <div id="invoice_vendor_wrapper" class="hidden">
                    <label for="invoice_vendor" class="block text-sm font-medium text-gray-700 mb-2">
                        Invoice Vendor
                    </label>
                    <input type="text" 
                           name="invoice_vendor" 
                           id="invoice_vendor" 
                           value="{{ old('invoice_vendor') }}"
                           class="w-full {{ $errors->has('invoice_vendor') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                           placeholder="Masukkan nomor invoice vendor">
                    @error('invoice_vendor')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- BL (conditional for Pembayaran Kapal) -->
                <div id="bl_wrapper" class="hidden md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Daftar BL
                    </label>
                    
                    <!-- Hidden inputs for selected BL values -->
                    <div id="bl_hidden_inputs"></div>
                    
                    <!-- Search input with dropdown -->
                    <div class="relative">
                        <div class="w-full min-h-[42px] px-3 py-2 border border-gray-300 rounded-md focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 bg-white cursor-text" 
                             id="bl_container"
                             onclick="document.getElementById('bl_search').focus()">
                             
                            <!-- Selected items (chips) -->
                            <div id="bl_selected_chips" class="flex flex-wrap gap-1 mb-1"></div>
                            
                            <!-- Search input -->
                            <input type="text" 
                                   id="bl_search"
                                   class="border-0 focus:ring-0 outline-none p-0 text-sm w-full"
                                   placeholder="Cari kontainer atau seal..." 
                                   autocomplete="off">
                        </div>
                        
                        <!-- Dropdown list -->
                        <div id="bl_dropdown" 
                             class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto hidden">
                            @foreach($bls as $bl)
                                <div class="bl-option px-3 py-2 cursor-pointer hover:bg-blue-50 border-b border-gray-100"
                                     data-id="{{ $bl->id }}"
                                     data-text="{{ $bl->nomor_bl }}"
                                     data-kontainer="{{ $bl->nomor_kontainer ?? 'N/A' }}"
                                     data-seal="{{ $bl->no_seal ?? 'N/A' }}"
                                     data-voyage="{{ $bl->no_voyage ?? '' }}">
                                    <div class="font-medium text-gray-900">{{ $bl->nomor_kontainer ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-600">Seal: {{ $bl->no_seal ?? 'N/A' }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mt-2 flex justify-between items-center">
                        <span id="bl_selectedCount" class="text-sm text-blue-600">
                            Terpilih: 0 dari {{ count($bls) }} BL
                        </span>
                        <div class="flex gap-2">
                            <button type="button" 
                                    id="bl_selectAllBtn"
                                    class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded transition">
                                Pilih Semua
                            </button>
                            <button type="button" 
                                    id="bl_clearAllBtn"
                                    class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded transition">
                                Hapus Semua
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Klasifikasi Biaya (conditional for Pembayaran Kapal) -->
                <div id="klasifikasi_biaya_wrapper" class="hidden">
                    <label for="klasifikasi_biaya_select" class="block text-sm font-medium text-gray-700 mb-2">
                        Klasifikasi Biaya <span class="text-red-500">*</span>
                    </label>
                    <select name="klasifikasi_biaya_id" 
                            id="klasifikasi_biaya_select" 
                            class="w-full {{ $errors->has('klasifikasi_biaya_id') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                        <option value="">Pilih Klasifikasi Biaya</option>
                        @foreach($klasifikasiBiayas as $klasifikasi)
                            <option value="{{ $klasifikasi->id }}" data-nama="{{ $klasifikasi->nama }}" {{ old('klasifikasi_biaya_id') == $klasifikasi->id ? 'selected' : '' }}>
                                {{ $klasifikasi->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('klasifikasi_biaya_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Vendor Dokumen (conditional for Klasifikasi Biaya "biaya dokumen") -->
                <div id="vendor_dokumen_wrapper" class="hidden">
                    <label for="vendor_dokumen_select" class="block text-sm font-medium text-gray-700 mb-2">
                        Vendor Dokumen <span class="text-red-500">*</span>
                    </label>
                    <select name="pricelist_biaya_dokumen_id" 
                            id="vendor_dokumen_select" 
                            class="w-full {{ $errors->has('pricelist_biaya_dokumen_id') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                        <option value="">Pilih Vendor Dokumen</option>
                        @foreach($pricelistBiayaDokumen as $pricelist)
                            <option value="{{ $pricelist->id }}" 
                                    data-biaya="{{ $pricelist->biaya }}" 
                                    {{ old('pricelist_biaya_dokumen_id') == $pricelist->id ? 'selected' : '' }}>
                                {{ $pricelist->nama_vendor }} - Rp {{ number_format($pricelist->biaya, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('pricelist_biaya_dokumen_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Barang dan Jumlah (conditional for Klasifikasi Biaya "buruh") -->
                <div id="barang_wrapper" class="hidden md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Daftar Barang <span class="text-red-500">*</span>
                    </label>
                    <div id="barang_container" class="space-y-3">
                        <!-- Dynamic barang inputs will be added here -->
                    </div>
                    <button type="button" 

                            id="add_barang_btn" 
                            class="mt-3 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg transition inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Barang
                    </button>
                </div>

                <!-- Surat Jalan (conditional for Adjustment) -->
                <div id="surat_jalan_wrapper" class="hidden">
                    <label for="surat_jalan_select" class="block text-sm font-medium text-gray-700 mb-2">
                        Surat Jalan <span class="text-red-500">*</span>
                    </label>
                    <select name="surat_jalan_id" 
                            id="surat_jalan_select" 
                            class="w-full {{ $errors->has('surat_jalan_id') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                        <option value="">Pilih Surat Jalan</option>
                        @foreach($suratJalans as $sj)
                            <option value="{{ $sj->id }}" 
                                    data-uang-jalan="{{ $sj->uang_jalan }}" 
                                    data-source="{{ $sj->source }}"
                                    {{ old('surat_jalan_id') == $sj->id ? 'selected' : '' }}>
                                {{ $sj->no_surat_jalan }} - {{ $sj->tujuan_pengiriman }} (Rp {{ number_format($sj->uang_jalan, 0, ',', '.') }})
                                @if(isset($sj->source))
                                    - [{{ $sj->source == 'regular' ? 'Regular' : 'Bongkar' }}]
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('surat_jalan_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Penyesuaian (conditional for Adjustment) -->
                <div id="jenis_penyesuaian_wrapper" class="hidden">
                    <label for="jenis_penyesuaian_select" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Penyesuaian <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_penyesuaian" 
                            id="jenis_penyesuaian_select" 
                            class="w-full {{ $errors->has('jenis_penyesuaian') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                        <option value="">Pilih Jenis Penyesuaian</option>
                        <option value="pengembalian penuh" {{ old('jenis_penyesuaian') == 'pengembalian penuh' ? 'selected' : '' }}>Pengembalian Penuh</option>
                        <option value="pengembalian sebagian" {{ old('jenis_penyesuaian') == 'pengembalian sebagian' ? 'selected' : '' }}>Pengembalian Sebagian</option>
                        <option value="penambahan" {{ old('jenis_penyesuaian') == 'penambahan' ? 'selected' : '' }}>Penambahan</option>
                    </select>
                    @error('jenis_penyesuaian')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipe Penyesuaian (conditional for Adjustment with 'penambahan') -->
                <div id="tipe_penyesuaian_wrapper" class="hidden md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Penyesuaian <span class="text-red-500">*</span>
                    </label>
                    <div id="tipe_penyesuaian_container" class="space-y-3">
                        <!-- Dynamic tipe penyesuaian inputs will be added here -->
                    </div>
                    <button type="button" 
                            id="add_tipe_penyesuaian_btn" 
                            class="mt-3 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg transition inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Tipe Penyesuaian
                    </button>
                </div>

                <!-- Jumlah Retur Galon (conditional for Adjustment with 'retur galon') -->
                <div id="jumlah_retur_wrapper" class="hidden">
                    <label for="jumlah_retur" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah Retur <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="jumlah_retur" 
                           id="jumlah_retur" 
                           value="{{ old('jumlah_retur') }}"
                           min="1"
                           step="1"
                           class="w-full {{ $errors->has('jumlah_retur') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                           placeholder="Masukkan jumlah galon">
                    @error('jumlah_retur')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Penerima -->
                <div>
                    <label for="penerima" class="block text-sm font-medium text-gray-700 mb-2">
                        Penerima <span class="text-red-500">*</span>
                    </label>
                    <select name="penerima" 
                            id="penerima" 
                            class="w-full {{ $errors->has('penerima') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                            required>
                        <option value="">Pilih Penerima</option>
                        @foreach($karyawans as $karyawan)
                            <option value="{{ $karyawan->nama_lengkap }}" {{ old('penerima') == $karyawan->nama_lengkap ? 'selected' : '' }}>
                                {{ $karyawan->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                    @error('penerima')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Biaya Listrik Multiple Entries -->
                <div id="biaya_listrik_wrapper" class="hidden md:col-span-2">
                    <div class="flex justify-between items-center mb-3">
                        <label class="block text-sm font-medium text-gray-700">
                            Detail Biaya Listrik <span class="text-red-500">*</span>
                        </label>
                        <button type="button" 
                                id="add_biaya_listrik_btn"
                                class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg transition inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Biaya Listrik
                        </button>
                    </div>
                    <div id="biaya_listrik_container" class="space-y-4">
                        <!-- Biaya listrik entries will be added here dynamically -->
                    </div>
                </div>

                <!-- LWBP Baru (for Biaya Listrik) -->
                <div id="lwbp_baru_wrapper" class="hidden">
                    <label for="lwbp_baru" class="block text-sm font-medium text-gray-700 mb-2">
                        LWBP Baru
                    </label>
                    <input type="number" 
                           name="lwbp_baru" 
                           id="lwbp_baru" 
                           value="{{ old('lwbp_baru') }}"
                           class="w-full {{ $errors->has('lwbp_baru') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                           placeholder="Masukkan LWBP baru"
                           step="0.01">
                    @error('lwbp_baru')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- LWBP Lama (for Biaya Listrik) -->
                <div id="lwbp_lama_wrapper" class="hidden">
                    <label for="lwbp_lama" class="block text-sm font-medium text-gray-700 mb-2">
                        LWBP Lama
                    </label>
                    <input type="number" 
                           name="lwbp_lama" 
                           id="lwbp_lama" 
                           value="{{ old('lwbp_lama') }}"
                           class="w-full {{ $errors->has('lwbp_lama') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                           placeholder="Masukkan LWBP lama"
                           step="0.01">
                    @error('lwbp_lama')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- LWBP (for Biaya Listrik) -->
                <div id="lwbp_wrapper" class="hidden">
                    <label for="lwbp" class="block text-sm font-medium text-gray-700 mb-2">
                        LWBP
                    </label>
                    <input type="number" 
                           name="lwbp" 
                           id="lwbp" 
                           value="{{ old('lwbp') }}"
                           class="w-full bg-gray-100 cursor-not-allowed {{ $errors->has('lwbp') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                           placeholder="Auto-calculated"
                           step="0.01"
                           readonly>
                    <p class="mt-1 text-xs text-blue-600 font-medium">LWBP = LWBP Baru - LWBP Lama - WBP</p>
                    @error('lwbp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- WBP (for Biaya Listrik) -->
                <div id="wbp_wrapper" class="hidden">
                    <label for="wbp" class="block text-sm font-medium text-gray-700 mb-2">
                        WBP
                    </label>
                    <input type="number" 
                           name="wbp" 
                           id="wbp" 
                           value="{{ old('wbp') }}"
                           class="w-full bg-gray-100 cursor-not-allowed {{ $errors->has('wbp') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                           placeholder="Auto-calculated"
                           step="0.01"
                           readonly>
                    <p class="mt-1 text-xs text-blue-600 font-medium">WBP = (LWBP Baru - LWBP Lama) × 17%</p>
                    @error('wbp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- LWBP Tarif (for Biaya Listrik) -->
                <div id="lwbp_tarif_wrapper" class="hidden">
                    <label for="lwbp_tarif" class="block text-sm font-medium text-gray-700 mb-2">
                        LWBP Tarif
                    </label>
                    <input type="number" 
                           name="lwbp_tarif" 
                           id="lwbp_tarif" 
                           value="{{ old('lwbp_tarif', '1982') }}"
                           class="w-full {{ $errors->has('lwbp_tarif') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                           placeholder="Masukkan LWBP Tarif"
                           step="0.01">
                    @error('lwbp_tarif')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- WBP Tarif (for Biaya Listrik) -->
                <div id="wbp_tarif_wrapper" class="hidden">
                    <label for="wbp_tarif" class="block text-sm font-medium text-gray-700 mb-2">
                        WBP Tarif
                    </label>
                    <input type="number" 
                           name="wbp_tarif" 
                           id="wbp_tarif" 
                           value="{{ old('wbp_tarif', '2975') }}"
                           class="w-full {{ $errors->has('wbp_tarif') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                           placeholder="Masukkan WBP Tarif"
                           step="0.01">
                    @error('wbp_tarif')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tarif 1 (for Biaya Listrik) -->
                <div id="tarif_1_wrapper" class="hidden">
                    <label for="tarif_1" class="block text-sm font-medium text-gray-700 mb-2">
                        Tarif 1
                    </label>
                    <input type="number" 
                           name="tarif_1" 
                           id="tarif_1" 
                           value="{{ old('tarif_1') }}"
                           class="w-full bg-gray-100 cursor-not-allowed {{ $errors->has('tarif_1') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                           placeholder="Auto-calculated"
                           step="0.01"
                           readonly>
                    <p class="mt-1 text-xs text-blue-600 font-medium">Tarif 1 = LWBP × LWBP Tarif</p>
                    @error('tarif_1')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tarif 2 (for Biaya Listrik) -->
                <div id="tarif_2_wrapper" class="hidden">
                    <label for="tarif_2" class="block text-sm font-medium text-gray-700 mb-2">
                        Tarif 2
                    </label>
                    <input type="number" 
                           name="tarif_2" 
                           id="tarif_2" 
                           value="{{ old('tarif_2') }}"
                           class="w-full bg-gray-100 cursor-not-allowed {{ $errors->has('tarif_2') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                           placeholder="Auto-calculated"
                           step="0.01"
                           readonly>
                    <p class="mt-1 text-xs text-blue-600 font-medium">Tarif 2 = WBP × WBP Tarif</p>
                    @error('tarif_2')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Biaya Beban (for Biaya Listrik) -->
                <div id="biaya_beban_wrapper" class="hidden">
                    <label for="biaya_beban" class="block text-sm font-medium text-gray-700 mb-2">
                        Biaya Beban
                    </label>
                    <input type="number" 
                           name="biaya_beban" 
                           id="biaya_beban" 
                           value="{{ old('biaya_beban') }}"
                           class="w-full {{ $errors->has('biaya_beban') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                           placeholder="Masukkan Biaya Beban"
                           step="0.01">
                    @error('biaya_beban')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PPJU (for Biaya Listrik) -->
                <div id="ppju_wrapper" class="hidden">
                    <label for="ppju" class="block text-sm font-medium text-gray-700 mb-2">
                        PPJU
                    </label>
                    <input type="number" 
                           name="ppju" 
                           id="ppju" 
                           value="{{ old('ppju') }}"
                           class="w-full bg-gray-100 cursor-not-allowed {{ $errors->has('ppju') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                           placeholder="Auto-calculated"
                           step="0.01"
                           readonly>
                    <p class="mt-1 text-xs text-blue-600 font-medium">PPJU = (Tarif 1 + Tarif 2 + Biaya Beban) × 3%</p>
                    @error('ppju')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- DPP (for Biaya Listrik) -->
                <div id="dpp_wrapper" class="hidden">
                    <label for="dpp" class="block text-sm font-medium text-gray-700 mb-2">
                        DPP
                    </label>
                    <input type="number" 
                           name="dpp" 
                           id="dpp" 
                           value="{{ old('dpp') }}"
                           class="w-full {{ $errors->has('dpp') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                           placeholder="Masukkan DPP"
                           step="0.01">
                    @error('dpp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total (for Biaya Listrik) -->
                <div id="total_wrapper" class="hidden">
                    <label for="total" class="block text-sm font-medium text-gray-700 mb-2">
                        Total
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               name="total" 
                               id="total" 
                               value="{{ old('total') }}"
                               class="w-full pl-10 {{ $errors->has('total') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               style="height: 38px; padding: 6px 12px 6px 40px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                               placeholder="0">
                    </div>
                    @error('total')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PPH (for Biaya Listrik - 10% dari total) -->
                <div id="pph_wrapper" class="hidden">
                    <label for="pph" class="block text-sm font-medium text-gray-700 mb-2">
                        PPH (10%)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               name="pph" 
                               id="pph" 
                               value="{{ old('pph', '0') }}"
                               class="w-full pl-10 bg-gray-100 cursor-not-allowed {{ $errors->has('pph') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               style="height: 38px; padding: 6px 12px 6px 40px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                               placeholder="0"
                               readonly>
                    </div>
                    <p class="mt-1 text-xs text-blue-600 font-medium">PPH = 10% × DPP</p>
                    @error('pph')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Grand Total (for Biaya Listrik - Total - PPH) -->
                <div id="grand_total_wrapper" class="hidden">
                    <label for="grand_total" class="block text-sm font-medium text-gray-700 mb-2">
                        Grand Total
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               name="grand_total" 
                               id="grand_total" 
                               value="{{ old('grand_total', '') }}"
                               class="w-full pl-10 bg-green-50 font-semibold cursor-not-allowed {{ $errors->has('grand_total') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               style="height: 38px; padding: 6px 12px 6px 40px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                               placeholder="0"
                               readonly>
                    </div>
                    <p class="mt-1 text-xs text-green-600 font-medium">Grand Total = DPP - PPH</p>
                    @error('grand_total')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Detail Pembayaran Multiple (conditional for Pembayaran Kapal) -->
            <div id="detail_pembayaran_wrapper" class="mt-6 border-t pt-6 hidden">
                <div class="flex justify-between items-center mb-3">
                    <label class="block text-sm font-medium text-gray-700">
                        Detail Pembayaran (Opsional)
                    </label>
                    <button type="button" 
                            id="add_detail_pembayaran_btn"
                            class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah
                    </button>
                </div>
                <div id="detail_pembayaran_container" class="space-y-3">
                    <!-- Dynamic detail pembayaran inputs will be added here -->
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="mt-6">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea name="deskripsi" 
                          id="deskripsi" 
                          rows="4"
                          class="w-full {{ $errors->has('deskripsi') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          style="padding: 8px 12px; font-size: 14px; line-height: 1.5; border: 1px solid #d1d5db; border-radius: 6px;"
                          placeholder="Masukkan deskripsi invoice (opsional)">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Catatan -->
            <div class="mt-6">
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan
                </label>
                <textarea name="catatan" 
                          id="catatan" 
                          rows="3"
                          class="w-full {{ $errors->has('catatan') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          style="padding: 8px 12px; font-size: 14px; line-height: 1.5; border: 1px solid #d1d5db; border-radius: 6px;"
                          placeholder="Masukkan catatan tambahan (opsional)">{{ old('catatan') }}</textarea>
                @error('catatan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 bg-white rounded-lg shadow p-6">
            <a href="{{ route('invoice-aktivitas-lain.index') }}" 
               class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg transition">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Simpan Invoice
            </button>
        </div>
    </form>
</div>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
.select2-container {
    width: 100% !important;
}
.select2-container .select2-selection--single {
    height: 38px !important;
    padding: 6px 12px !important;
    border: 1px solid #d1d5db !important;
    border-radius: 6px !important;
}
.select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 24px !important;
    padding-left: 0 !important;
}
.select2-container .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
}
.select2-dropdown {
    border: 1px solid #d1d5db !important;
    border-radius: 6px !important;
}
.select2-container--open .select2-selection--single {
    border-color: #3b82f6 !important;
}
.select2-results__option--highlighted {
    background-color: #3b82f6 !important;
}

/* BL Multi-Select Styling */
#bl_container {
    transition: all 0.15s ease;
}

#bl_container:focus-within {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.bl-selected-chip {
    display: inline-flex;
    align-items: center;
    background-color: #3b82f6;
    color: white;
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 4px;
    margin: 1px;
    gap: 6px;
}

.bl-selected-chip .remove-chip {
    margin-left: 4px;
    cursor: pointer;
    font-weight: bold;
    font-size: 0.875rem;
    opacity: 0.8;
}

.bl-selected-chip .remove-chip:hover {
    opacity: 1;
}

.bl-option {
    transition: background-color 0.15s ease;
}

.bl-option:hover {
    background-color: #eff6ff !important;
}

.bl-option.selected {
    background-color: #dbeafe;
    opacity: 0.6;
}

#bl_search::placeholder {
    color: #9ca3af;
}

#bl_dropdown {
    border-top: none;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
</style>

<!-- Ensure jQuery + Select2 are available (dynamic loader with fallbacks) -->
<script>
// Store pricelist buruh data as JavaScript variable (v2)
const pricelistBuruhData = @json($pricelistBuruh);
const pricelistBiayaDokumenData = @json($pricelistBiayaDokumen);
const blsData = @json($bls);

// Debug: Check for duplicates
console.log('Total pricelist buruh:', pricelistBuruhData.length);
console.log('Pricelist buruh data:', pricelistBuruhData);

(function() {
    function loadScript(src, onload, onerror) {
        const s = document.createElement('script');
        s.src = src;
        s.async = false;
        s.onload = onload;
        s.onerror = onerror;
        document.head.appendChild(s);
    }

    function ensureJQueryAndSelect2(done) {
        // Load jQuery if missing
        function onJqReady() {
            // Load Select2 if missing
            if (typeof jQuery.fn !== 'undefined' && typeof jQuery.fn.select2 === 'undefined') {
                loadScript('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', function() {
                    console.log('Select2 loaded from jsdelivr');
                    done(null, window.jQuery);
                }, function() {
                    console.warn('Select2 jsdelivr failed, trying unpkg fallback');
                    loadScript('https://unpkg.com/select2@4.0.13/dist/js/select2.min.js', function() {
                        console.log('Select2 loaded from unpkg');
                        done(null, window.jQuery);
                    }, function() {
                        console.error('Failed to load Select2 from CDNs');
                        done(new Error('select2'));
                    });
                });
            } else if (typeof jQuery.fn !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
                done(null, window.jQuery);
            } else {
                done(new Error('jQueryMissing'));
            }
        }

        if (typeof window.jQuery === 'undefined') {
            loadScript('https://code.jquery.com/jquery-3.6.0.min.js', function() {
                console.log('jQuery loaded from CDN');
                onJqReady();
            }, function() {
                console.error('Failed to load jQuery from CDN');
                done(new Error('jquery'));
            });
        } else {
            onJqReady();
        }
    }

    function initializeSelect2AndForm($) {
        if (!$ || typeof $.fn.select2 === 'undefined') {
            console.error('Select2 not available for initialization');
            return;
        }

        // Initialize Select2 for dropdowns
        $('#jenis_aktivitas').select2({ placeholder: 'Pilih Jenis Aktivitas', allowClear: true, width: '100%' });
        $('#jenis_biaya_dropdown').select2({ placeholder: 'Pilih Jenis Biaya', allowClear: true, width: '100%' });
        $('#sub_jenis_kendaraan').select2({ placeholder: 'Pilih Sub Jenis Kendaraan', allowClear: true, width: '100%' });
        $('#nomor_polisi').select2({ placeholder: 'Pilih Nomor Polisi', allowClear: true, width: '100%' });
        $('#nomor_voyage').select2({ placeholder: 'Pilih Nomor Voyage', allowClear: true, width: '100%' });
        $('#klasifikasi_biaya_select').select2({ placeholder: 'Pilih Klasifikasi Biaya', allowClear: true, width: '100%' });
        $('#vendor_dokumen_select').select2({ placeholder: 'Pilih Vendor Dokumen', allowClear: true, width: '100%' });
        $('#nama_barang_select').select2({ placeholder: 'Pilih Nama Barang', allowClear: true, width: '100%' });
        $('#surat_jalan_select').select2({ placeholder: 'Pilih Surat Jalan', allowClear: true, width: '100%' });
        $('#jenis_penyesuaian_select').select2({ placeholder: 'Pilih Jenis Penyesuaian', allowClear: true, width: '100%' });
        $('#penerima').select2({ placeholder: 'Pilih atau ketik nama penerima', allowClear: true, width: '100%', tags: true });

        // Format currency input
        const totalInput = document.getElementById('total');
        const pphInput = document.getElementById('pph');
        const grandTotalInput = document.getElementById('grand_total');
        const pphWrapper = document.getElementById('pph_wrapper');
        const grandTotalWrapper = document.getElementById('grand_total_wrapper');
        const lwbpBaruWrapper = document.getElementById('lwbp_baru_wrapper');
        const lwbpLamaWrapper = document.getElementById('lwbp_lama_wrapper');
        const lwbpBaruInput = document.getElementById('lwbp_baru');
        const lwbpLamaInput = document.getElementById('lwbp_lama');
        const lwbpWrapper = document.getElementById('lwbp_wrapper');
        const lwbpInput = document.getElementById('lwbp');
        const wbpWrapper = document.getElementById('wbp_wrapper');
        const wbpInput = document.getElementById('wbp');
        const lwbpTarifWrapper = document.getElementById('lwbp_tarif_wrapper');
        const lwbpTarifInput = document.getElementById('lwbp_tarif');
        const wbpTarifWrapper = document.getElementById('wbp_tarif_wrapper');
        const wbpTarifInput = document.getElementById('wbp_tarif');
        const tarif1Wrapper = document.getElementById('tarif_1_wrapper');
        const tarif1Input = document.getElementById('tarif_1');
        const tarif2Wrapper = document.getElementById('tarif_2_wrapper');
        const tarif2Input = document.getElementById('tarif_2');
        const biayaBebanWrapper = document.getElementById('biaya_beban_wrapper');
        const biayaBebanInput = document.getElementById('biaya_beban');
        const ppjuWrapper = document.getElementById('ppju_wrapper');
        const ppjuInput = document.getElementById('ppju');
        const dppWrapper = document.getElementById('dpp_wrapper');
        const dppInput = document.getElementById('dpp');
        const totalWrapper = document.getElementById('total_wrapper');
        const jenisBiayaDropdown = document.getElementById('jenis_biaya_dropdown');

        if (totalInput) {
            totalInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                if (value) value = parseInt(value).toLocaleString('id-ID');
                e.target.value = value;
                
                // Calculate PPh if Biaya Listrik is selected
                const selectedJenisBiaya = $('#jenis_biaya_dropdown').find('option:selected');
                const namaJenisBiaya = selectedJenisBiaya.text().toLowerCase();
                if (namaJenisBiaya.includes('listrik')) {
                    calculatePph();
                }
            });
            totalInput.closest('form').addEventListener('submit', function(e) {
                // Strip formatting from total
                const plainValue = totalInput.value.replace(/\./g, '');
                totalInput.value = plainValue;
                
                // Strip formatting from PPh and Grand Total
                if (pphInput && pphInput.value) {
                    pphInput.value = pphInput.value.replace(/\./g, '');
                }
                if (grandTotalInput && grandTotalInput.value) {
                    grandTotalInput.value = grandTotalInput.value.replace(/\./g, '');
                }
                
                // Strip formatting from all detail_biaya inputs
                const detailBiayaInputs = document.querySelectorAll('.detail-biaya');
                detailBiayaInputs.forEach(input => {
                    if (input.value) {
                        input.value = input.value.replace(/\./g, '').replace(/,/g, '');
                    }
                });
            });
        }

        // Calculate PPh (10% dari DPP untuk biaya listrik)
        function calculatePph() {
            const dpp = parseFloat(dppInput.value) || 0;
            
            // PPH = 10% dari DPP
            const pph = Math.round(dpp * 0.10);
            pphInput.value = pph > 0 ? pph.toLocaleString('id-ID') : '0';
            
            // Grand Total = DPP - PPH
            const grandTotal = dpp - pph;
            grandTotalInput.value = grandTotal > 0 ? grandTotal.toLocaleString('id-ID') : '0';
        }

        // Calculate LWBP (LWBP Baru - LWBP Lama - WBP)
        function calculateLwbp() {
            // First calculate WBP
            calculateWbp();
            
            const lwbpBaru = parseFloat(lwbpBaruInput.value) || 0;
            const lwbpLama = parseFloat(lwbpLamaInput.value) || 0;
            const wbp = parseFloat(wbpInput.value) || 0;
            
            // LWBP = LWBP Baru - LWBP Lama - WBP
            const lwbp = lwbpBaru - lwbpLama - wbp;
            lwbpInput.value = lwbp;
            
            // Trigger Tarif 1 calculation when LWBP changes
            calculateTarif1();
        }

        // Calculate WBP ((LWBP Baru - LWBP Lama) × 17%)
        function calculateWbp() {
            const lwbpBaru = parseFloat(lwbpBaruInput.value) || 0;
            const lwbpLama = parseFloat(lwbpLamaInput.value) || 0;
            
            // WBP = (LWBP Baru - LWBP Lama) × 17%
            const wbp = Math.round((lwbpBaru - lwbpLama) * 0.17);
            wbpInput.value = wbp;
            
            // Trigger Tarif 2 calculation when WBP changes
            calculateTarif2();
        }

        // Setup WBP calculation event listeners
        function setupWbpCalculation() {
            if (lwbpBaruInput) {
                lwbpBaruInput.addEventListener('input', calculateWbp);
            }
            if (lwbpLamaInput) {
                lwbpLamaInput.addEventListener('input', calculateWbp);
            }
        }

        // Setup LWBP calculation event listeners
        function setupLwbpCalculation() {
            if (lwbpBaruInput) {
                lwbpBaruInput.addEventListener('input', calculateLwbp);
            }
            if (lwbpLamaInput) {
                lwbpLamaInput.addEventListener('input', calculateLwbp);
            }
        }

        // Calculate Tarif 1 (LWBP × LWBP Tarif)
        function calculateTarif1() {
            const lwbp = parseFloat(lwbpInput.value) || 0;
            const lwbpTarif = parseFloat(lwbpTarifInput.value) || 0;
            
            // Tarif 1 = LWBP × LWBP Tarif
            const tarif1 = lwbp * lwbpTarif;
            tarif1Input.value = tarif1;
            
            // Trigger PPJU calculation when Tarif 1 changes
            calculatePpju();
        }

        // Setup Tarif 1 calculation event listeners
        function setupTarif1Calculation() {
            if (lwbpInput) {
                lwbpInput.addEventListener('change', calculateTarif1);
            }
            if (lwbpTarifInput) {
                lwbpTarifInput.addEventListener('input', calculateTarif1);
            }
        }

        // Calculate Tarif 2 (WBP × WBP Tarif)
        function calculateTarif2() {
            const wbp = parseFloat(wbpInput.value) || 0;
            const wbpTarif = parseFloat(wbpTarifInput.value) || 0;
            
            // Tarif 2 = WBP × WBP Tarif
            const tarif2 = wbp * wbpTarif;
            tarif2Input.value = tarif2;
            
            // Trigger PPJU calculation when Tarif 2 changes
            calculatePpju();
        }

        // Setup Tarif 2 calculation event listeners
        function setupTarif2Calculation() {
            if (wbpInput) {
                wbpInput.addEventListener('change', calculateTarif2);
            }
            if (wbpTarifInput) {
                wbpTarifInput.addEventListener('input', calculateTarif2);
            }
        }

        // Calculate PPJU ((Tarif 1 + Tarif 2 + Biaya Beban) × 3%)
        function calculatePpju() {
            const tarif1 = parseFloat(tarif1Input.value) || 0;
            const tarif2 = parseFloat(tarif2Input.value) || 0;
            const biayaBeban = parseFloat(biayaBebanInput.value) || 0;
            
            // PPJU = (Tarif 1 + Tarif 2 + Biaya Beban) × 3%
            const ppju = Math.round((tarif1 + tarif2 + biayaBeban) * 0.03);
            ppjuInput.value = ppju;
            
            // Trigger DPP calculation when PPJU changes
            calculateDpp();
        }

        // Calculate DPP (Tarif 1 + Tarif 2 + Biaya Beban + PPJU)
        function calculateDpp() {
            const tarif1 = parseFloat(tarif1Input.value) || 0;
            const tarif2 = parseFloat(tarif2Input.value) || 0;
            const biayaBeban = parseFloat(biayaBebanInput.value) || 0;
            const ppju = parseFloat(ppjuInput.value) || 0;
            
            // DPP = Tarif 1 + Tarif 2 + Biaya Beban + PPJU
            const dpp = tarif1 + tarif2 + biayaBeban + ppju;
            dppInput.value = dpp;
            
            // Trigger PPH calculation when DPP changes
            calculatePph();
        }

        // Setup PPJU calculation event listeners
        function setupPpjuCalculation() {
            if (tarif1Input) {
                tarif1Input.addEventListener('change', calculatePpju);
            }
            if (tarif2Input) {
                tarif2Input.addEventListener('change', calculatePpju);
            }
            if (biayaBebanInput) {
                biayaBebanInput.addEventListener('input', calculatePpju);
            }
        }

        // Setup DPP calculation event listeners
        function setupDppCalculation() {
            if (tarif1Input) {
                tarif1Input.addEventListener('change', calculateDpp);
            }
            if (tarif2Input) {
                tarif2Input.addEventListener('change', calculateDpp);
            }
            if (biayaBebanInput) {
                biayaBebanInput.addEventListener('input', calculateDpp);
            }
            if (ppjuInput) {
                ppjuInput.addEventListener('change', calculateDpp);
            }
        }

        // Toggle conditional fields
        const jenisAktivitasSelect = document.getElementById('jenis_aktivitas');
        const jenisBiayaWrapper = document.getElementById('jenis_biaya_wrapper');
        const subJenisKendaraanWrapper = document.getElementById('sub_jenis_kendaraan_wrapper');
        const subJenisKendaraanSelect = document.getElementById('sub_jenis_kendaraan');
        const nomorPolisiWrapper = document.getElementById('nomor_polisi_wrapper');
        const nomorPolisiSelect = document.getElementById('nomor_polisi');
        const nomorVoyageWrapper = document.getElementById('nomor_voyage_wrapper');
        const nomorVoyageSelect = document.getElementById('nomor_voyage');
        const invoiceVendorWrapper = document.getElementById('invoice_vendor_wrapper');
        const invoiceVendorInput = document.getElementById('invoice_vendor');
        const blWrapper = document.getElementById('bl_wrapper');
        const klasifikasiBiayaWrapper = document.getElementById('klasifikasi_biaya_wrapper');
        const klasifikasiBiayaSelect = document.getElementById('klasifikasi_biaya_select');
        const vendorDokumenWrapper = document.getElementById('vendor_dokumen_wrapper');
        const vendorDokumenSelect = document.getElementById('vendor_dokumen_select');
        const barangWrapper = document.getElementById('barang_wrapper');
        const suratJalanWrapper = document.getElementById('surat_jalan_wrapper');
        const suratJalanSelect = document.getElementById('surat_jalan_select');
        const jenisPenyesuaianWrapper = document.getElementById('jenis_penyesuaian_wrapper');
        const jenisPenyesuaianSelect = document.getElementById('jenis_penyesuaian_select');
        const tipePenyesuaianWrapper = document.getElementById('tipe_penyesuaian_wrapper');
        const detailPembayaranWrapper = document.getElementById('detail_pembayaran_wrapper');
        const biayaListrikWrapper = document.getElementById('biaya_listrik_wrapper');

        // Toggle PPh fields based on jenis biaya selection
        if (jenisBiayaDropdown) {
            $('#jenis_biaya_dropdown').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const namaJenisBiaya = selectedOption.text().toLowerCase();
                
                // Show PPh and LWBP fields for Biaya Listrik
                if (namaJenisBiaya.includes('listrik')) {
                    // Show biaya listrik wrapper
                    if (biayaListrikWrapper) {
                        biayaListrikWrapper.classList.remove('hidden');
                        initializeBiayaListrikInputs();
                    }
                    // HIDE total field for biaya listrik (not needed, use DPP instead)
                    if (totalWrapper) {
                        totalWrapper.classList.add('hidden');
                        if (totalInput) {
                            totalInput.value = '';
                            totalInput.removeAttribute('required');
                        }
                    }
                    
                    pphWrapper.classList.remove('hidden');
                    grandTotalWrapper.classList.remove('hidden');
                    if (lwbpBaruWrapper) {
                        lwbpBaruWrapper.classList.remove('hidden');
                        if (lwbpBaruInput) lwbpBaruInput.setAttribute('required', 'required');
                    }
                    if (lwbpLamaWrapper) {
                        lwbpLamaWrapper.classList.remove('hidden');
                        if (lwbpLamaInput) lwbpLamaInput.setAttribute('required', 'required');
                    }
                    if (lwbpWrapper) lwbpWrapper.classList.remove('hidden');
                    if (wbpWrapper) wbpWrapper.classList.remove('hidden');
                    if (lwbpTarifWrapper) {
                        lwbpTarifWrapper.classList.remove('hidden');
                        if (lwbpTarifInput) lwbpTarifInput.setAttribute('required', 'required');
                    }
                    if (wbpTarifWrapper) {
                        wbpTarifWrapper.classList.remove('hidden');
                        if (wbpTarifInput) wbpTarifInput.setAttribute('required', 'required');
                    }
                    if (tarif1Wrapper) tarif1Wrapper.classList.remove('hidden');
                    if (tarif2Wrapper) tarif2Wrapper.classList.remove('hidden');
                    if (biayaBebanWrapper) {
                        biayaBebanWrapper.classList.remove('hidden');
                        if (biayaBebanInput) biayaBebanInput.setAttribute('required', 'required');
                    }
                    if (ppjuWrapper) ppjuWrapper.classList.remove('hidden');
                    if (dppWrapper) dppWrapper.classList.remove('hidden');
                    
                    // Set default values for tarif fields
                    if (lwbpTarifInput && !lwbpTarifInput.value) lwbpTarifInput.value = '1982';
                    if (wbpTarifInput && !wbpTarifInput.value) wbpTarifInput.value = '2975';
                    if (biayaBebanInput && !biayaBebanInput.value) biayaBebanInput.value = '893200';
                    
                    // Setup WBP auto-calculation
                    setupWbpCalculation();
                    
                    // Setup LWBP auto-calculation
                    setupLwbpCalculation();
                    
                    // Setup Tarif 1 auto-calculation
                    setupTarif1Calculation();
                    
                    // Setup Tarif 2 auto-calculation
                    setupTarif2Calculation();
                    
                    // Setup PPJU auto-calculation
                    setupPpjuCalculation();
                    
                    // Setup DPP auto-calculation
                    setupDppCalculation();
                } else {
                    // Hide biaya listrik wrapper for other jenis biaya
                    if (biayaListrikWrapper) {
                        biayaListrikWrapper.classList.add('hidden');
                        clearBiayaListrikInputs();
                    }
                    
                    // Show total field for other jenis biaya
                    if (totalWrapper) {
                        totalWrapper.classList.remove('hidden');
                        if (totalInput) totalInput.setAttribute('required', 'required');
                    }
                    
                    // Hide PPh and LWBP fields for other jenis biaya
                    pphWrapper.classList.add('hidden');
                    grandTotalWrapper.classList.add('hidden');
                    if (lwbpBaruWrapper) {
                        lwbpBaruWrapper.classList.add('hidden');
                        if (lwbpBaruInput) {
                            lwbpBaruInput.value = '';
                            lwbpBaruInput.removeAttribute('required');
                        }
                    }
                    if (lwbpLamaWrapper) {
                        lwbpLamaWrapper.classList.add('hidden');
                        if (lwbpLamaInput) {
                            lwbpLamaInput.value = '';
                            lwbpLamaInput.removeAttribute('required');
                        }
                    }
                    if (lwbpWrapper) {
                        lwbpWrapper.classList.add('hidden');
                        if (lwbpInput) lwbpInput.value = '';
                    }
                    if (wbpWrapper) {
                        wbpWrapper.classList.add('hidden');
                        if (wbpInput) wbpInput.value = '';
                    }
                    if (lwbpTarifWrapper) {
                        lwbpTarifWrapper.classList.add('hidden');
                        if (lwbpTarifInput) {
                            lwbpTarifInput.value = '';
                            lwbpTarifInput.removeAttribute('required');
                        }
                    }
                    if (wbpTarifWrapper) {
                        wbpTarifWrapper.classList.add('hidden');
                        if (wbpTarifInput) {
                            wbpTarifInput.value = '';
                            wbpTarifInput.removeAttribute('required');
                        }
                    }
                    if (tarif1Wrapper) {
                        tarif1Wrapper.classList.add('hidden');
                        if (tarif1Input) tarif1Input.value = '';
                    }
                    if (tarif2Wrapper) {
                        tarif2Wrapper.classList.add('hidden');
                        if (tarif2Input) tarif2Input.value = '';
                    }
                    if (biayaBebanWrapper) {
                        biayaBebanWrapper.classList.add('hidden');
                        if (biayaBebanInput) {
                            biayaBebanInput.value = '';
                            biayaBebanInput.removeAttribute('required');
                        }
                    }
                    if (ppjuWrapper) {
                        ppjuWrapper.classList.add('hidden');
                        if (ppjuInput) ppjuInput.value = '';
                    }
                    if (dppWrapper) {
                        dppWrapper.classList.add('hidden');
                        if (dppInput) dppInput.value = '';
                    }
                    pphInput.value = '0';
                    grandTotalInput.value = '';
                }
            });
        }

        function toggleConditionalFields() {
            const jenisVal = jenisAktivitasSelect.value;
            
            // Hide all conditional fields first
            jenisBiayaWrapper.classList.add('hidden');
            jenisBiayaDropdown.removeAttribute('required');
            $('#jenis_biaya_dropdown').val('').trigger('change');
            
            // Hide LWBP fields
            if (totalWrapper) totalWrapper.classList.add('hidden');
            if (lwbpBaruWrapper) lwbpBaruWrapper.classList.add('hidden');
            if (lwbpLamaWrapper) lwbpLamaWrapper.classList.add('hidden');
            if (lwbpWrapper) lwbpWrapper.classList.add('hidden');
            if (wbpWrapper) wbpWrapper.classList.add('hidden');
            if (lwbpTarifWrapper) lwbpTarifWrapper.classList.add('hidden');
            if (wbpTarifWrapper) wbpTarifWrapper.classList.add('hidden');
            if (tarif1Wrapper) tarif1Wrapper.classList.add('hidden');
            if (tarif2Wrapper) tarif2Wrapper.classList.add('hidden');
            if (biayaBebanWrapper) biayaBebanWrapper.classList.add('hidden');
            if (ppjuWrapper) ppjuWrapper.classList.add('hidden');
            if (dppWrapper) dppWrapper.classList.add('hidden');
            if (totalInput) totalInput.value = '';
            if (lwbpBaruInput) lwbpBaruInput.value = '';
            if (lwbpLamaInput) lwbpLamaInput.value = '';
            if (lwbpInput) lwbpInput.value = '';
            if (wbpInput) wbpInput.value = '';
            if (lwbpTarifInput) lwbpTarifInput.value = '';
            if (wbpTarifInput) wbpTarifInput.value = '';
            if (tarif1Input) tarif1Input.value = '';
            if (tarif2Input) tarif2Input.value = '';
            if (biayaBebanInput) biayaBebanInput.value = '';
            if (ppjuInput) ppjuInput.value = '';
            if (dppInput) dppInput.value = '';
            
            const referensiWrapper = document.getElementById('referensi_wrapper');
            const referensiInput = document.getElementById('referensi');
            if (referensiWrapper) {
                referensiWrapper.classList.add('hidden');
                if (referensiInput) referensiInput.value = '';
            }
            
            subJenisKendaraanWrapper.classList.add('hidden');
            subJenisKendaraanSelect.removeAttribute('required');
            $('#sub_jenis_kendaraan').val('').trigger('change');
            
            nomorPolisiWrapper.classList.add('hidden');
            nomorPolisiSelect.removeAttribute('required');
            $('#nomor_polisi').val('').trigger('change');
            
            nomorVoyageWrapper.classList.add('hidden');
            nomorVoyageSelect.removeAttribute('required');
            $('#nomor_voyage').val('').trigger('change');
            
            if (invoiceVendorWrapper) {
                invoiceVendorWrapper.classList.add('hidden');
                if (invoiceVendorInput) invoiceVendorInput.value = '';
            }
            
            blWrapper.classList.add('hidden');
            clearBlInputs();
            
            klasifikasiBiayaWrapper.classList.add('hidden');
            klasifikasiBiayaSelect.removeAttribute('required');
            $('#klasifikasi_biaya_select').val('').trigger('change');
            
            vendorDokumenWrapper.classList.add('hidden');
            vendorDokumenSelect.removeAttribute('required');
            $('#vendor_dokumen_select').val('').trigger('change');
            
            barangWrapper.classList.add('hidden');
            clearBarangInputs();
            
            suratJalanWrapper.classList.add('hidden');
            suratJalanSelect.removeAttribute('required');
            $('#surat_jalan_select').val('').trigger('change');
            
            jenisPenyesuaianWrapper.classList.add('hidden');
            jenisPenyesuaianSelect.removeAttribute('required');
            $('#jenis_penyesuaian_select').val('').trigger('change');
            
            tipePenyesuaianWrapper.classList.add('hidden');
            clearTipePenyesuaianInputs();
            
            detailPembayaranWrapper.classList.add('hidden');
            clearDetailPembayaranInputs();
            
            // Show relevant fields based on jenis aktivitas
            if (jenisVal === 'Pembayaran Kendaraan') {
                subJenisKendaraanWrapper.classList.remove('hidden');
                subJenisKendaraanSelect.setAttribute('required', 'required');
                nomorPolisiWrapper.classList.remove('hidden');
                nomorPolisiSelect.setAttribute('required', 'required');
                
                setTimeout(() => {
                    $('#nomor_polisi').select2({ placeholder: 'Pilih Nomor Polisi', allowClear: true, width: '100%' });
                }, 100);
            } else if (jenisVal === 'Pembayaran Kapal') {
                nomorVoyageWrapper.classList.remove('hidden');
                nomorVoyageSelect.setAttribute('required', 'required');
                if (invoiceVendorWrapper) {
                    invoiceVendorWrapper.classList.remove('hidden');
                }
                blWrapper.classList.remove('hidden');
                initializeBlInputs();
                klasifikasiBiayaWrapper.classList.remove('hidden');
                klasifikasiBiayaSelect.setAttribute('required', 'required');
                
                setTimeout(() => {
                    $('#nomor_voyage').select2({ placeholder: 'Pilih Nomor Voyage', allowClear: true, width: '100%' });
                    $('#klasifikasi_biaya_select').select2({ placeholder: 'Pilih Klasifikasi Biaya', allowClear: true, width: '100%' });
                }, 100);
                
                // Setup klasifikasi biaya change event
                setupKlasifikasiBiayaToggle();
                
                // Show detail pembayaran section
                detailPembayaranWrapper.classList.remove('hidden');
            } else if (jenisVal === 'Pembayaran Adjustment Uang Jalan') {
                suratJalanWrapper.classList.remove('hidden');
                suratJalanSelect.setAttribute('required', 'required');
                jenisPenyesuaianWrapper.classList.remove('hidden');
                jenisPenyesuaianSelect.setAttribute('required', 'required');
                
                setTimeout(() => {
                    $('#surat_jalan_select').select2({ placeholder: 'Pilih Surat Jalan', allowClear: true, width: '100%' });
                    $('#jenis_penyesuaian_select').select2({ placeholder: 'Pilih Jenis Penyesuaian', allowClear: true, width: '100%' });
                }, 100);
            } else if (jenisVal === 'Pembayaran Lain-lain') {
                jenisBiayaWrapper.classList.remove('hidden');
                
                if (referensiWrapper) {
                    referensiWrapper.classList.remove('hidden');
                }
                
                setTimeout(() => {
                    $('#jenis_biaya_dropdown').select2({ placeholder: 'Pilih Jenis Biaya', allowClear: true, width: '100%' });
                }, 100);
            }
        }

        function toggleTipePenyesuaian() {
            const jenisPenyesuaian = jenisPenyesuaianSelect.value;
            const totalInput = document.getElementById('total');
            const jumlahReturWrapper = document.getElementById('jumlah_retur_wrapper');
            const jumlahReturInput = document.getElementById('jumlah_retur');
            
            // Hide all conditional fields first
            tipePenyesuaianWrapper.classList.add('hidden');
            clearTipePenyesuaianInputs();
            
            if (jumlahReturWrapper) {
                jumlahReturWrapper.classList.add('hidden');
                if (jumlahReturInput) {
                    jumlahReturInput.removeAttribute('required');
                    jumlahReturInput.value = '';
                }
            }
            
            if (jenisPenyesuaian === 'pengembalian penuh') {
                tipePenyesuaianWrapper.classList.add('hidden');
                clearTipePenyesuaianInputs();
                
                // Set total from surat jalan
                const selectedSJ = $('#surat_jalan_select').find('option:selected');
                const uangJalan = selectedSJ.data('uang-jalan');
                if (uangJalan) {
                    totalInput.value = parseInt(uangJalan).toLocaleString('id-ID');
                }
            } else if (jenisPenyesuaian === 'pengembalian sebagian') {
                tipePenyesuaianWrapper.classList.add('hidden');
                clearTipePenyesuaianInputs();
                // Total can be entered manually
            } else if (jenisPenyesuaian === 'penambahan') {
                tipePenyesuaianWrapper.classList.remove('hidden');
                initializeTipePenyesuaianInputs();
            } else {
                tipePenyesuaianWrapper.classList.add('hidden');
                clearTipePenyesuaianInputs();
            }
        }

        function initializeTipePenyesuaianInputs() {
            const container = document.getElementById('tipe_penyesuaian_container');
            container.innerHTML = '';
            addTipePenyesuaianInput();
        }

        function clearTipePenyesuaianInputs() {
            const container = document.getElementById('tipe_penyesuaian_container');
            if (container) container.innerHTML = '';
        }

        function addTipePenyesuaianInput(existingTipe = '', existingNominal = '') {
            const container = document.getElementById('tipe_penyesuaian_container');
            const index = container.children.length;
            
            const inputGroup = document.createElement('div');
            inputGroup.className = 'flex items-end gap-3 p-3 bg-gray-50 rounded-md';
            inputGroup.innerHTML = `
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Penyesuaian</label>
                    <select name="tipe_penyesuaian_detail[${index}][tipe]" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                            required>
                        <option value="">Pilih Tipe</option>
                        <option value="mel" ${existingTipe === 'mel' ? 'selected' : ''}>MEL</option>
                        <option value="krani" ${existingTipe === 'krani' ? 'selected' : ''}>Krani</option>
                        <option value="parkir" ${existingTipe === 'parkir' ? 'selected' : ''}>Parkir</option>
                        <option value="pelancar" ${existingTipe === 'pelancar' ? 'selected' : ''}>Pelancar</option>
                        <option value="kawalan" ${existingTipe === 'kawalan' ? 'selected' : ''}>Kawalan</option>
                        <option value="retur galon" ${existingTipe === 'retur galon' ? 'selected' : ''}>Retur Galon</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp)</label>
                    <input type="number" 
                           name="tipe_penyesuaian_detail[${index}][nominal]" 
                           value="${existingNominal}"
                           min="0" 
                           step="1"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                           placeholder="0"
                           required>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" 
                            onclick="removeTipePenyesuaianInput(this)" 
                            class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            `;
            
            container.appendChild(inputGroup);
            
            // Initialize Select2 for new select
            setTimeout(() => {
                $(inputGroup).find('select').select2({
                    placeholder: 'Pilih Tipe',
                    allowClear: true,
                    width: '100%'
                });
            }, 100);
            
            // Add event listener for auto-calculation
            const nominalInput = inputGroup.querySelector('input');
            nominalInput.addEventListener('input', function(e) {
                calculateTotalFromTipePenyesuaian();
            });
        }

        window.removeTipePenyesuaianInput = function(button) {
            const container = document.getElementById('tipe_penyesuaian_container');
            if (container.children.length > 1) {
                button.closest('.flex.items-end.gap-3').remove();
                calculateTotalFromTipePenyesuaian();
            }
        };

        function calculateTotalFromTipePenyesuaian() {
            const container = document.getElementById('tipe_penyesuaian_container');
            const nominalInputs = container.querySelectorAll('input[name*="[nominal]"]');
            let total = 0;
            
            nominalInputs.forEach(input => {
                const value = parseFloat(input.value) || 0;
                total += value;
            });
            
            const totalInput = document.getElementById('total');
            if (total > 0) {
                totalInput.value = total.toLocaleString('id-ID');
            }
        }

        function setupKlasifikasiBiayaToggle() {
            $('#klasifikasi_biaya_select').off('change').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const namaKlasifikasi = selectedOption.text().toLowerCase();
                
                // Hide all conditional fields first
                barangWrapper.classList.add('hidden');
                clearBarangInputs();
                vendorDokumenWrapper.classList.add('hidden');
                vendorDokumenSelect.removeAttribute('required');
                $('#vendor_dokumen_select').val('').trigger('change');
                
                // Show relevant field based on klasifikasi biaya
                if (namaKlasifikasi.includes('buruh')) {
                    barangWrapper.classList.remove('hidden');
                    initializeBarangInputs();
                } else if (namaKlasifikasi.includes('biaya dokumen') || namaKlasifikasi.includes('dokumen')) {
                    vendorDokumenWrapper.classList.remove('hidden');
                    vendorDokumenSelect.setAttribute('required', 'required');
                    
                    // Re-initialize Select2
                    setTimeout(() => {
                        $('#vendor_dokumen_select').select2({ placeholder: 'Pilih Vendor Dokumen', allowClear: true, width: '100%' });
                    }, 100);
                }
            });
            
            // Setup auto-calculate total when vendor dokumen is selected
            $('#vendor_dokumen_select').off('change').on('change', function() {
                calculateTotalFromVendorDokumen();
            });
        }
        
        // Calculate total from vendor dokumen × number of BLs
        function calculateTotalFromVendorDokumen() {
            const selectedOption = $('#vendor_dokumen_select').find('option:selected');
            const biaya = selectedOption.data('biaya');
            
            if (biaya) {
                const blContainer = document.getElementById('bl_container');
                const blCount = blContainer ? blContainer.children.length : 0;
                const totalBiaya = parseInt(biaya) * blCount;
                
                const totalInput = document.getElementById('total');
                totalInput.value = totalBiaya.toLocaleString('id-ID');
            }
        }
        
        // BL management functions
        function initializeBlInputs() {
            const container = document.getElementById('bl_container');
            container.innerHTML = '';
            addBlInput();
        }
        
        function clearBlInputs() {
            const container = document.getElementById('bl_container');
            if (container) container.innerHTML = '';
        }
        

        // BL Searchable Multi-Select Management
        let selectedBLs = [];
        
        const blSearch = document.getElementById('bl_search');
        const blDropdown = document.getElementById('bl_dropdown');
        const blSelectedChips = document.getElementById('bl_selected_chips');
        const blHiddenInputs = document.getElementById('bl_hidden_inputs');
        const blSelectAllBtn = document.getElementById('bl_selectAllBtn');
        const blClearAllBtn = document.getElementById('bl_clearAllBtn');
        const blOptions = document.querySelectorAll('.bl-option');
        const blSelectedCount = document.getElementById('bl_selectedCount');
        
        // Show dropdown on focus
        if (blSearch) {
            blSearch.addEventListener('focus', function() {
                blDropdown.classList.remove('hidden');
                filterBLOptions();
            });
            
            // Search/filter options
            blSearch.addEventListener('input', function() {
                filterBLOptions();
            });
        }
        
        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#bl_container') && !e.target.closest('#bl_dropdown')) {
                if (blDropdown) blDropdown.classList.add('hidden');
            }
        });
        
        function filterBLOptions() {
            const searchTerm = blSearch.value.toLowerCase();
            const selectedVoyage = $('#nomor_voyage').val();
            
            blOptions.forEach(option => {
                const kontainer = option.getAttribute('data-kontainer').toLowerCase();
                const seal = option.getAttribute('data-seal').toLowerCase();
                const voyage = option.getAttribute('data-voyage');
                
                // Filter by search term
                const matchesSearch = kontainer.includes(searchTerm) || seal.includes(searchTerm);
                
                // Filter by selected voyage (if any)
                const matchesVoyage = !selectedVoyage || voyage === selectedVoyage;
                
                const shouldShow = matchesSearch && matchesVoyage;
                option.style.display = shouldShow ? 'block' : 'none';
            });
        }
        
        // Handle option selection
        blOptions.forEach(option => {
            option.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const kontainer = this.getAttribute('data-kontainer');
                const seal = this.getAttribute('data-seal');
                
                if (!selectedBLs.find(bl => bl.id === id)) {
                    selectedBLs.push({ id, kontainer, seal });
                    addBLChip(id, kontainer, seal);
                    updateBLSelectedCount();
                    updateBLHiddenInputs();
                    this.classList.add('selected');
                    calculateTotalFromVendorDokumen();
                }
                
                blSearch.value = '';
                blDropdown.classList.add('hidden');
            });
        });
        
        function addBLChip(id, kontainer, seal) {
            const chip = document.createElement('span');
            chip.className = 'bl-selected-chip';
            chip.setAttribute('data-id', id);
            chip.innerHTML = `
                <div class="flex flex-col">
                    <span class="font-medium">${kontainer}</span>
                    <span class="text-xs opacity-75">Seal: ${seal}</span>
                </div>
                <span class="remove-chip" onclick="removeBLChip('${id}')">&times;</span>
            `;
            blSelectedChips.appendChild(chip);
        }
        
        // Remove chip function (global scope for onclick)
        window.removeBLChip = function(id) {
            selectedBLs = selectedBLs.filter(bl => bl.id !== id);
            const chipEl = document.querySelector(`[data-id="${id}"].bl-selected-chip`);
            if (chipEl) chipEl.remove();
            const optionEl = document.querySelector(`[data-id="${id}"].bl-option`);
            if (optionEl) optionEl.classList.remove('selected');
            updateBLSelectedCount();
            updateBLHiddenInputs();
            calculateTotalFromVendorDokumen();
        };
        
        // Select All button
        if (blSelectAllBtn) {
            blSelectAllBtn.addEventListener('click', function() {
                const selectedVoyage = $('#nomor_voyage').val();
                
                blOptions.forEach(option => {
                    const id = option.getAttribute('data-id');
                    const kontainer = option.getAttribute('data-kontainer');
                    const seal = option.getAttribute('data-seal');
                    const voyage = option.getAttribute('data-voyage');
                    
                    // Only select BLs matching the selected voyage
                    const matchesVoyage = !selectedVoyage || voyage === selectedVoyage;
                    
                    if (matchesVoyage && !selectedBLs.find(bl => bl.id === id)) {
                        selectedBLs.push({ id, kontainer, seal });
                        addBLChip(id, kontainer, seal);
                        option.classList.add('selected');
                    }
                });
                
                updateBLSelectedCount();
                updateBLHiddenInputs();
                calculateTotalFromVendorDokumen();
            });
        }
        
        // Clear All button  
        if (blClearAllBtn) {
            blClearAllBtn.addEventListener('click', function() {
                clearBlInputs();
            });
        }
        
        function updateBLHiddenInputs() {
            blHiddenInputs.innerHTML = '';
            selectedBLs.forEach(bl => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'bl_details[]';
                input.value = bl.id;
                blHiddenInputs.appendChild(input);
            });
        }
        
        function updateBLSelectedCount() {
            if (blSelectedCount) {
                blSelectedCount.textContent = `Terpilih: ${selectedBLs.length} dari ${blOptions.length} BL`;
            }
        }
        
        function clearBlInputs() {
            selectedBLs = [];
            if (blSelectedChips) blSelectedChips.innerHTML = '';
            if (blHiddenInputs) blHiddenInputs.innerHTML = '';
            blOptions.forEach(option => {
                option.classList.remove('selected');
            });
            updateBLSelectedCount();
            calculateTotalFromVendorDokumen();
        }
        
        function initializeBlInputs() {
            // Clear any existing selections
            clearBlInputs();
        }
        
        // Barang management functions
        function initializeBarangInputs() {
            const container = document.getElementById('barang_container');
            container.innerHTML = '';
            addBarangInput();
        }
        
        function clearBarangInputs() {
            const container = document.getElementById('barang_container');
            if (container) container.innerHTML = '';
        }
        
        function clearDetailPembayaranInputs() {
            const container = document.getElementById('detail_pembayaran_container');
            if (container) container.innerHTML = '';
        }
        
        function addBarangInput(existingBarangId = '', existingJumlah = '') {
            const container = document.getElementById('barang_container');
            const index = container.children.length;
            
            const inputGroup = document.createElement('div');
            inputGroup.className = 'flex items-end gap-3 p-3 bg-gray-50 rounded-md';
            
            // Build options from JavaScript data
            let barangOptions = '<option value="">Pilih Nama Barang</option>';
            pricelistBuruhData.forEach(pricelist => {
                const selected = existingBarangId == pricelist.id ? 'selected' : '';
                barangOptions += `<option value="${pricelist.id}" data-tarif="${pricelist.tarif}" ${selected}>${pricelist.barang}</option>`;
            });
            
            inputGroup.innerHTML = `
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                    <select name="barang_detail[${index}][pricelist_buruh_id]" 
                            class="barang-select w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                            required>
                        ${barangOptions}
                    </select>
                </div>
                <div class="w-32">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                    <input type="number" 
                           name="barang_detail[${index}][jumlah]" 
                           value="${existingJumlah || '1'}"
                           min="1" 
                           class="jumlah-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                           placeholder="0" 
                           required>
                           class="jumlah-input w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                           placeholder="Contoh: 1.5"
                           required>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" 
                            onclick="removeBarangInput(this)" 
                            class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            `;
            
            container.appendChild(inputGroup);
            
            // Initialize Select2 for new select
            setTimeout(() => {
                $(inputGroup).find('.barang-select').select2({
                    placeholder: 'Pilih Nama Barang',
                    allowClear: true,
                    width: '100%'
                });
            }, 100);
            
            // Add event listeners for auto-calculation
            const barangSelect = inputGroup.querySelector('.barang-select');
            const jumlahInput = inputGroup.querySelector('.jumlah-input');
            
            $(barangSelect).on('change', function() {
                calculateTotalFromBarang();
            });
            
            jumlahInput.addEventListener('input', function() {
                calculateTotalFromBarang();
            });
        }
        
        window.removeBarangInput = function(button) {
            const container = document.getElementById('barang_container');
            if (container.children.length > 1) {
                button.closest('.flex.items-end.gap-3').remove();
                calculateTotalFromBarang();
            }
        };
        
        function calculateTotalFromBarang() {
            const container = document.getElementById('barang_container');
            const barangSelects = container.querySelectorAll('.barang-select');
            const jumlahInputs = container.querySelectorAll('.jumlah-input');
            let total = 0;
            
            barangSelects.forEach((select, index) => {
                const selectedOption = $(select).find('option:selected');
                const tarif = parseFloat(selectedOption.data('tarif')) || 0;
                const jumlah = parseInt(jumlahInputs[index].value) || 0;
                total += tarif * jumlah;
            });
            
            if (total > 0) {
                const totalInput = document.getElementById('total');
                totalInput.value = total.toLocaleString('id-ID');
            }
        }
        
        // Add button for barang
        const addBarangBtn = document.getElementById('add_barang_btn');
        if (addBarangBtn) {
            addBarangBtn.addEventListener('click', function() {
                addBarangInput();
            });
        }
        
        if (jenisAktivitasSelect) {
            $('#jenis_aktivitas').on('change', function() {
                jenisAktivitasSelect.value = this.value;
                toggleConditionalFields();
            });
            toggleConditionalFields();
        }
        
        // Add event listener for voyage change to filter BL options
        if (nomorVoyageSelect) {
            $('#nomor_voyage').on('change', function() {
                // Clear BL selections when voyage changes
                clearBlInputs();
                // Refilter BL options based on new voyage
                if (blSearch) filterBLOptions();
            });
        }
        
        if (jenisPenyesuaianSelect) {
            $('#jenis_penyesuaian_select').on('change', function() {
                toggleTipePenyesuaian();
            });
        }
        
        // Add button for tipe penyesuaian
        const addTipeBtn = document.getElementById('add_tipe_penyesuaian_btn');
        if (addTipeBtn) {
            addTipeBtn.addEventListener('click', function() {
                addTipePenyesuaianInput();
            });
        }
        
        // Biaya Listrik Management Functions
        function initializeBiayaListrikInputs() {
            const container = document.getElementById('biaya_listrik_container');
            container.innerHTML = '';
            addBiayaListrikInput();
        }
        
        function clearBiayaListrikInputs() {
            const container = document.getElementById('biaya_listrik_container');
            if (container) container.innerHTML = '';
        }
        
        function addBiayaListrikInput(existingData = {}) {
            const container = document.getElementById('biaya_listrik_container');
            const index = container.children.length;
            
            const inputGroup = document.createElement('div');
            inputGroup.className = 'grid grid-cols-1 md:grid-cols-3 gap-3 p-4 bg-blue-50 rounded-lg border-2 border-blue-200';
            inputGroup.setAttribute('data-bl-index', index);
            
            const removeButton = container.children.length > 0 ? `
                <button type="button" 
                        onclick="removeBiayaListrikInput(this)"
                        class="text-red-600 hover:text-red-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            ` : '';
            
            inputGroup.innerHTML = `
                <div class="md:col-span-3 flex justify-between items-center border-b-2 border-blue-300 pb-2 mb-2">
                    <span class="text-sm font-bold text-blue-700">Biaya Listrik #${index + 1}</span>
                    ${removeButton}
                </div>
                
                <!-- LWBP Baru -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        LWBP Baru <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="biaya_listrik[${index}][lwbp_baru]" 
                           class="bl-lwbp-baru w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="0"
                           step="0.01"
                           value="${existingData.lwbp_baru || ''}"
                           required>
                </div>
                
                <!-- LWBP Lama -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        LWBP Lama <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="biaya_listrik[${index}][lwbp_lama]" 
                           class="bl-lwbp-lama w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="0"
                           step="0.01"
                           value="${existingData.lwbp_lama || ''}"
                           required>
                </div>
                
                <!-- WBP (Auto-calculated) -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        WBP <small class="text-gray-500">(Auto)</small>
                    </label>
                    <input type="number" 
                           name="biaya_listrik[${index}][wbp]" 
                           class="bl-wbp w-full px-3 py-2 text-sm border border-gray-300 rounded-md bg-gray-100"
                           placeholder="0"
                           step="0.01"
                           readonly
                           value="${existingData.wbp || ''}">
                </div>
                
                <!-- LWBP (Auto-calculated) -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        LWBP <small class="text-gray-500">(Auto)</small>
                    </label>
                    <input type="number" 
                           name="biaya_listrik[${index}][lwbp]" 
                           class="bl-lwbp w-full px-3 py-2 text-sm border border-gray-300 rounded-md bg-gray-100"
                           placeholder="0"
                           step="0.01"
                           readonly
                           value="${existingData.lwbp || ''}">
                </div>
                
                <!-- LWBP Tarif -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        LWBP Tarif <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="biaya_listrik[${index}][lwbp_tarif]" 
                           class="bl-lwbp-tarif w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="1982"
                           step="0.01"
                           value="${existingData.lwbp_tarif || '1982'}"
                           required>
                </div>
                
                <!-- WBP Tarif -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        WBP Tarif <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="biaya_listrik[${index}][wbp_tarif]" 
                           class="bl-wbp-tarif w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="2975"
                           step="0.01"
                           value="${existingData.wbp_tarif || '2975'}"
                           required>
                </div>
                
                <!-- Tarif 1 (Auto-calculated) -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Tarif 1 <small class="text-gray-500">(Auto)</small>
                    </label>
                    <input type="number" 
                           name="biaya_listrik[${index}][tarif_1]" 
                           class="bl-tarif-1 w-full px-3 py-2 text-sm border border-gray-300 rounded-md bg-gray-100"
                           placeholder="0"
                           step="0.01"
                           readonly
                           value="${existingData.tarif_1 || ''}">
                </div>
                
                <!-- Tarif 2 (Auto-calculated) -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Tarif 2 <small class="text-gray-500">(Auto)</small>
                    </label>
                    <input type="number" 
                           name="biaya_listrik[${index}][tarif_2]" 
                           class="bl-tarif-2 w-full px-3 py-2 text-sm border border-gray-300 rounded-md bg-gray-100"
                           placeholder="0"
                           step="0.01"
                           readonly
                           value="${existingData.tarif_2 || ''}">
                </div>
                
                <!-- Biaya Beban -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Biaya Beban <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="biaya_listrik[${index}][biaya_beban]" 
                           class="bl-biaya-beban w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="893200"
                           step="0.01"
                           value="${existingData.biaya_beban || '893200'}"
                           required>
                </div>
                
                <!-- PPJU (Auto-calculated) -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        PPJU <small class="text-gray-500">(Auto)</small>
                    </label>
                    <input type="number" 
                           name="biaya_listrik[${index}][ppju]" 
                           class="bl-ppju w-full px-3 py-2 text-sm border border-gray-300 rounded-md bg-gray-100"
                           placeholder="0"
                           step="0.01"
                           readonly
                           value="${existingData.ppju || ''}">
                </div>
                
                <!-- DPP (Auto-calculated) -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        DPP <small class="text-gray-500">(Auto)</small>
                    </label>
                    <input type="number" 
                           name="biaya_listrik[${index}][dpp]" 
                           class="bl-dpp w-full px-3 py-2 text-sm border border-gray-300 rounded-md bg-gray-100"
                           placeholder="0"
                           step="0.01"
                           readonly
                           value="${existingData.dpp || ''}">
                </div>
                
                <!-- PPH 10% (Auto-calculated) -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        PPH 10% <small class="text-gray-500">(Auto)</small>
                    </label>
                    <input type="number" 
                           name="biaya_listrik[${index}][pph]" 
                           class="bl-pph w-full px-3 py-2 text-sm border border-gray-300 rounded-md bg-gray-100"
                           placeholder="0"
                           step="0.01"
                           readonly
                           value="${existingData.pph || ''}">
                </div>
                
                <!-- Grand Total (Auto-calculated) -->
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Grand Total <small class="text-gray-500">(DPP - PPH)</small>
                    </label>
                    <input type="number" 
                           name="biaya_listrik[${index}][grand_total]" 
                           class="bl-grand-total w-full px-3 py-2 text-sm border-2 border-green-400 rounded-md bg-green-50 font-bold"
                           placeholder="0"
                           step="0.01"
                           readonly
                           value="${existingData.grand_total || ''}">
                </div>
            `;
            
            container.appendChild(inputGroup);
            
            // Setup auto-calculations for this entry
            setupBiayaListrikCalculations(inputGroup);
            
            // Update total invoice
            updateTotalFromBiayaListrik();
        }
        
        window.removeBiayaListrikInput = function(button) {
            const container = document.getElementById('biaya_listrik_container');
            if (container.children.length > 1) {
                button.closest('.grid').remove();
                
                // Reindex entries
                const entries = container.querySelectorAll('.grid');
                entries.forEach((entry, index) => {
                    entry.setAttribute('data-bl-index', index);
                    const label = entry.querySelector('span.text-blue-700');
                    if (label) label.textContent = `Biaya Listrik #${index + 1}`;
                    
                    // Update input names
                    entry.querySelectorAll('input').forEach(input => {
                        const name = input.getAttribute('name');
                        if (name && name.startsWith('biaya_listrik[')) {
                            const fieldName = name.substring(name.indexOf('][') + 2, name.lastIndexOf(']'));
                            input.setAttribute('name', `biaya_listrik[${index}][${fieldName}]`);
                        }
                    });
                });
                
                // Update total
                updateTotalFromBiayaListrik();
            }
        };
        
        function setupBiayaListrikCalculations(entry) {
            const lwbpBaruInput = entry.querySelector('.bl-lwbp-baru');
            const lwbpLamaInput = entry.querySelector('.bl-lwbp-lama');
            const wbpInput = entry.querySelector('.bl-wbp');
            const lwbpInput = entry.querySelector('.bl-lwbp');
            const lwbpTarifInput = entry.querySelector('.bl-lwbp-tarif');
            const wbpTarifInput = entry.querySelector('.bl-wbp-tarif');
            const tarif1Input = entry.querySelector('.bl-tarif-1');
            const tarif2Input = entry.querySelector('.bl-tarif-2');
            const biayaBebanInput = entry.querySelector('.bl-biaya-beban');
            const ppjuInput = entry.querySelector('.bl-ppju');
            const dppInput = entry.querySelector('.bl-dpp');
            const pphInput = entry.querySelector('.bl-pph');
            const grandTotalInput = entry.querySelector('.bl-grand-total');
            
            function calculateWBP() {
                const lwbpBaru = parseFloat(lwbpBaruInput.value) || 0;
                const lwbpLama = parseFloat(lwbpLamaInput.value) || 0;
                const wbp = Math.round((lwbpBaru - lwbpLama) * 0.17);
                wbpInput.value = wbp;
                calculateLWBP();
                calculateTarif2();
            }
            
            function calculateLWBP() {
                const lwbpBaru = parseFloat(lwbpBaruInput.value) || 0;
                const lwbpLama = parseFloat(lwbpLamaInput.value) || 0;
                const wbp = parseFloat(wbpInput.value) || 0;
                const lwbp = lwbpBaru - lwbpLama - wbp;
                lwbpInput.value = lwbp;
                calculateTarif1();
            }
            
            function calculateTarif1() {
                const lwbp = parseFloat(lwbpInput.value) || 0;
                const lwbpTarif = parseFloat(lwbpTarifInput.value) || 0;
                const tarif1 = lwbp * lwbpTarif;
                tarif1Input.value = tarif1;
                calculatePPJU();
            }
            
            function calculateTarif2() {
                const wbp = parseFloat(wbpInput.value) || 0;
                const wbpTarif = parseFloat(wbpTarifInput.value) || 0;
                const tarif2 = wbp * wbpTarif;
                tarif2Input.value = tarif2;
                calculatePPJU();
            }
            
            function calculatePPJU() {
                const tarif1 = parseFloat(tarif1Input.value) || 0;
                const tarif2 = parseFloat(tarif2Input.value) || 0;
                const biayaBeban = parseFloat(biayaBebanInput.value) || 0;
                const ppju = Math.round((tarif1 + tarif2 + biayaBeban) * 0.03);
                ppjuInput.value = ppju;
                calculateDPP();
            }
            
            function calculateDPP() {
                const tarif1 = parseFloat(tarif1Input.value) || 0;
                const tarif2 = parseFloat(tarif2Input.value) || 0;
                const biayaBeban = parseFloat(biayaBebanInput.value) || 0;
                const ppju = parseFloat(ppjuInput.value) || 0;
                const dpp = tarif1 + tarif2 + biayaBeban + ppju;
                dppInput.value = dpp;
                calculatePPH();
            }
            
            function calculatePPH() {
                const dpp = parseFloat(dppInput.value) || 0;
                const pph = Math.round(dpp * 0.10);
                pphInput.value = pph;
                calculateGrandTotal();
            }
            
            function calculateGrandTotal() {
                const dpp = parseFloat(dppInput.value) || 0;
                const pph = parseFloat(pphInput.value) || 0;
                const grandTotal = dpp - pph;
                grandTotalInput.value = grandTotal;
                updateTotalFromBiayaListrik();
            }
            
            // Add event listeners
            lwbpBaruInput.addEventListener('input', calculateWBP);
            lwbpLamaInput.addEventListener('input', calculateWBP);
            lwbpTarifInput.addEventListener('input', calculateTarif1);
            wbpTarifInput.addEventListener('input', calculateTarif2);
            biayaBebanInput.addEventListener('input', calculatePPJU);
        }
        
        function updateTotalFromBiayaListrik() {
            const container = document.getElementById('biaya_listrik_container');
            const entries = container.querySelectorAll('.grid');
            let totalGrand = 0;
            
            entries.forEach(entry => {
                const grandTotal = parseFloat(entry.querySelector('.bl-grand-total').value) || 0;
                totalGrand += grandTotal;
            });
            
            // Update main total input if exists
            const totalInput = document.getElementById('total');
            if (totalInput) {
                totalInput.value = totalGrand > 0 ? Math.round(totalGrand).toLocaleString('id-ID') : '';
            }
            
            // Also update pph and grand total main fields
            const pphInput = document.getElementById('pph');
            const grandTotalInputMain = document.getElementById('grand_total');
            
            let totalPPH = 0;
            entries.forEach(entry => {
                const pph = parseFloat(entry.querySelector('.bl-pph').value) || 0;
                totalPPH += pph;
            });
            
            if (pphInput) {
                pphInput.value = totalPPH > 0 ? Math.round(totalPPH).toLocaleString('id-ID') : '0';
            }
            if (grandTotalInputMain) {
                grandTotalInputMain.value = totalGrand > 0 ? Math.round(totalGrand).toLocaleString('id-ID') : '0';
            }
        }
        
        // Add button for biaya listrik
        const addBiayaListrikBtn = document.getElementById('add_biaya_listrik_btn');
        if (addBiayaListrikBtn) {
            addBiayaListrikBtn.addEventListener('click', function() {
                addBiayaListrikInput();
            });
        }
        
        // Detail Pembayaran management functions
        function addDetailPembayaranInput(existingData = {}) {
            const container = document.getElementById('detail_pembayaran_container');
            const index = container.children.length;
            
            const inputGroup = document.createElement('div');
            inputGroup.className = 'grid grid-cols-1 md:grid-cols-3 gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200';
            
            inputGroup.innerHTML = `
                <div class="md:col-span-3 flex justify-between items-center border-b border-gray-300 pb-2 mb-2">
                    <span class="text-sm font-semibold text-gray-700">Detail #${index + 1}</span>
                    <button type="button" 
                            onclick="removeDetailPembayaranInput(this)"
                            class="text-red-600 hover:text-red-800 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Klasifikasi Biaya</label>
                    <select name="detail_pembayaran[${index}][klasifikasi_biaya_id]" 
                            class="detail-klasifikasi-biaya w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">--Pilih Klasifikasi Biaya--</option>
                        @foreach($klasifikasiBiayas as $klasifikasi)
                            <option value="{{ $klasifikasi->id }}" data-selected="${existingData.klasifikasi_biaya_id == {{ $klasifikasi->id }} ? 'selected' : ''}">{{ $klasifikasi->nama }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Biaya</label>
                    <input type="text" 
                           name="detail_pembayaran[${index}][biaya]" 
                           value="${existingData.biaya || ''}"
                           class="detail-biaya w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                           placeholder="0">
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                    <input type="text" 
                           name="detail_pembayaran[${index}][keterangan]" 
                           value="${existingData.keterangan || ''}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                           placeholder="Keterangan">
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Kas</label>
                    <input type="date" 
                           name="detail_pembayaran[${index}][tanggal_kas]" 
                           value="${existingData.tanggal_kas || ''}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No Bukti</label>
                    <input type="text" 
                           name="detail_pembayaran[${index}][no_bukti]" 
                           value="${existingData.no_bukti || ''}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                           placeholder="No Bukti">
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Penerima</label>
                    <select name="detail_pembayaran[${index}][penerima]" 
                            class="detail-penerima w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">--Pilih Penerima--</option>
                        @foreach($penerimaList ?? [] as $penerimaItem)
                            <option value="{{ $penerimaItem }}">{{ $penerimaItem }}</option>
                        @endforeach
                    </select>
                </div>
            `;
            
            container.appendChild(inputGroup);
            
            // Initialize Select2 for new selects
            setTimeout(() => {
                $(inputGroup).find('.detail-klasifikasi-biaya').select2({
                    placeholder: 'Pilih Klasifikasi Biaya',
                    allowClear: true,
                    width: '100%'
                });
                $(inputGroup).find('.detail-penerima').select2({
                    placeholder: 'Pilih Penerima',
                    allowClear: true,
                    width: '100%',
                    tags: true
                });
            }, 100);
            
            // Format currency for biaya input and add auto-calculation
            const biayaInput = inputGroup.querySelector('.detail-biaya');
            biayaInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                if (value) value = parseInt(value).toLocaleString('id-ID');
                e.target.value = value;
                // Auto-calculate total from detail pembayaran
                calculateTotalFromDetailPembayaran();
            });
        }
        
        window.removeDetailPembayaranInput = function(button) {
            const container = document.getElementById('detail_pembayaran_container');
            button.closest('.grid').remove();
            
            // Reindex detail numbers
            const details = container.querySelectorAll('.grid');
            details.forEach((detail, index) => {
                const label = detail.querySelector('span');
                if (label) label.textContent = `Detail #${index + 1}`;
            });
            
            // Recalculate total after removing
            calculateTotalFromDetailPembayaran();
        };
        
        function calculateTotalFromDetailPembayaran() {
            const container = document.getElementById('detail_pembayaran_container');
            const biayaInputs = container.querySelectorAll('.detail-biaya');
            let total = 0;
            
            biayaInputs.forEach(input => {
                const value = input.value.replace(/\./g, '').replace(/,/g, '');
                const numValue = parseFloat(value) || 0;
                total += numValue;
            });
            
            if (total > 0) {
                const totalInput = document.getElementById('total');
                totalInput.value = total.toLocaleString('id-ID');
            }
        }
        
        // Add button for detail pembayaran
        const addDetailPembayaranBtn = document.getElementById('add_detail_pembayaran_btn');
        if (addDetailPembayaranBtn) {
            addDetailPembayaranBtn.addEventListener('click', function() {
                addDetailPembayaranInput();
            });
        }
        
        // Surat jalan change event to auto-fill total for pengembalian penuh
        $('#surat_jalan_select').on('change', function() {
            const jenisPenyesuaian = jenisPenyesuaianSelect.value;
            if (jenisPenyesuaian === 'pengembalian penuh') {
                const selectedSJ = $(this).find('option:selected');
                const uangJalan = selectedSJ.data('uang-jalan');
                if (uangJalan) {
                    const totalInput = document.getElementById('total');
                    totalInput.value = parseInt(uangJalan).toLocaleString('id-ID');
                }
            }
        });

        console.log('Select2 initialized for invoice-aktivitas-lain');
    }

    // Start ensuring libraries and initialize
    ensureJQueryAndSelect2(function(err, jqInstance) {
        if (err) {
            console.error('jQuery or Select2 not loaded properly:', err);
            // If needed, we can show a user-visible message here
            return;
        }
        // Use provided jQuery instance and wait for DOM ready
        const $ = jqInstance || window.jQuery;
        $(document).ready(function() {
            initializeSelect2AndForm($);
            generateInvoiceNumber();
        });
    });

    // Generate Invoice Number automatically
    function generateInvoiceNumber() {
        const invoiceInput = document.getElementById('nomor_invoice');
        const loader = document.getElementById('invoice_loader');
        
        // Fetch next invoice number from server
        fetch('{{ route("invoice-aktivitas-lain.get-next-number") }}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.invoice_number) {
                invoiceInput.value = data.invoice_number;
                if (loader) loader.style.display = 'none';
            } else {
                throw new Error('Invalid response format');
            }
        })
        .catch(error => {
            console.error('Error fetching invoice number:', error);
            // Fallback: generate client-side (without checking database)
            const now = new Date();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = String(now.getFullYear()).slice(-2);
            const runningNumber = '000001'; // Placeholder - should come from server
            
            invoiceInput.value = `IAL-${month}-${year}-${runningNumber}`;
            invoiceInput.placeholder = 'Nomor otomatis (offline mode)';
            if (loader) loader.style.display = 'none';
            
            // Show warning
            const warning = document.createElement('p');
            warning.className = 'mt-1 text-sm text-yellow-600';
            warning.textContent = 'Menggunakan nomor offline - pastikan koneksi server tersedia';
            invoiceInput.parentElement.appendChild(warning);
        });
    }
})();
</script>
@endsection
