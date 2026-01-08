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

                <!-- BL (conditional for Pembayaran Kapal) -->
                <div id="bl_wrapper" class="hidden md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Daftar BL
                    </label>
                    <div id="bl_container" class="space-y-3">
                        <!-- Dynamic BL inputs will be added here -->
                    </div>
                    <button type="button" 
                            id="add_bl_btn" 
                            class="mt-3 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg transition inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah BL
                    </button>
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

                <!-- Total -->
                <div>
                    <label for="total" class="block text-sm font-medium text-gray-700 mb-2">
                        Total <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               name="total" 
                               id="total" 
                               value="{{ old('total') }}"
                               class="w-full pl-10 {{ $errors->has('total') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               style="height: 38px; padding: 6px 12px 6px 40px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;"
                               placeholder="0"
                               required>
                    </div>
                    @error('total')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Detail Pembayaran Multiple -->
            <div class="mt-6 border-t pt-6">
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
</style>

<!-- Ensure jQuery + Select2 are available (dynamic loader with fallbacks) -->
<script>
// Store pricelist buruh data as JavaScript variable (v2)
const pricelistBuruhData = @json($pricelistBuruh);
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
        $('#sub_jenis_kendaraan').select2({ placeholder: 'Pilih Sub Jenis Kendaraan', allowClear: true, width: '100%' });
        $('#nomor_polisi').select2({ placeholder: 'Pilih Nomor Polisi', allowClear: true, width: '100%' });
        $('#nomor_voyage').select2({ placeholder: 'Pilih Nomor Voyage', allowClear: true, width: '100%' });
        $('#klasifikasi_biaya_select').select2({ placeholder: 'Pilih Klasifikasi Biaya', allowClear: true, width: '100%' });
        $('#nama_barang_select').select2({ placeholder: 'Pilih Nama Barang', allowClear: true, width: '100%' });
        $('#surat_jalan_select').select2({ placeholder: 'Pilih Surat Jalan', allowClear: true, width: '100%' });
        $('#jenis_penyesuaian_select').select2({ placeholder: 'Pilih Jenis Penyesuaian', allowClear: true, width: '100%' });
        $('#penerima').select2({ placeholder: 'Pilih Penerima', allowClear: true, width: '100%' });

        // Format currency input
        const totalInput = document.getElementById('total');
        if (totalInput) {
            totalInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                if (value) value = parseInt(value).toLocaleString('id-ID');
                e.target.value = value;
            });
            totalInput.closest('form').addEventListener('submit', function(e) {
                const plainValue = totalInput.value.replace(/\./g, '');
                totalInput.value = plainValue;
                
                // No need to collect tipe penyesuaian data - it's already in the form as tipe_penyesuaian_detail array
            });
        }

        // Toggle conditional fields
        const jenisAktivitasSelect = document.getElementById('jenis_aktivitas');
        const subJenisKendaraanWrapper = document.getElementById('sub_jenis_kendaraan_wrapper');
        const subJenisKendaraanSelect = document.getElementById('sub_jenis_kendaraan');
        const nomorPolisiWrapper = document.getElementById('nomor_polisi_wrapper');
        const nomorPolisiSelect = document.getElementById('nomor_polisi');
        const nomorVoyageWrapper = document.getElementById('nomor_voyage_wrapper');
        const nomorVoyageSelect = document.getElementById('nomor_voyage');
        const blWrapper = document.getElementById('bl_wrapper');
        const klasifikasiBiayaWrapper = document.getElementById('klasifikasi_biaya_wrapper');
        const klasifikasiBiayaSelect = document.getElementById('klasifikasi_biaya_select');
        const barangWrapper = document.getElementById('barang_wrapper');
        const suratJalanWrapper = document.getElementById('surat_jalan_wrapper');
        const suratJalanSelect = document.getElementById('surat_jalan_select');
        const jenisPenyesuaianWrapper = document.getElementById('jenis_penyesuaian_wrapper');
        const jenisPenyesuaianSelect = document.getElementById('jenis_penyesuaian_select');
        const tipePenyesuaianWrapper = document.getElementById('tipe_penyesuaian_wrapper');

        function toggleConditionalFields() {
            const jenisVal = jenisAktivitasSelect.value;
            
            // Hide all conditional fields first
            subJenisKendaraanWrapper.classList.add('hidden');
            subJenisKendaraanSelect.removeAttribute('required');
            $('#sub_jenis_kendaraan').val('').trigger('change');
            
            nomorPolisiWrapper.classList.add('hidden');
            nomorPolisiSelect.removeAttribute('required');
            $('#nomor_polisi').val('').trigger('change');
            
            nomorVoyageWrapper.classList.add('hidden');
            nomorVoyageSelect.removeAttribute('required');
            $('#nomor_voyage').val('').trigger('change');
            
            blWrapper.classList.add('hidden');
            clearBlInputs();
            
            klasifikasiBiayaWrapper.classList.add('hidden');
            klasifikasiBiayaSelect.removeAttribute('required');
            $('#klasifikasi_biaya_select').val('').trigger('change');
            
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
            } else if (jenisVal === 'Pembayaran Adjustment Uang Jalan') {
                suratJalanWrapper.classList.remove('hidden');
                suratJalanSelect.setAttribute('required', 'required');
                jenisPenyesuaianWrapper.classList.remove('hidden');
                jenisPenyesuaianSelect.setAttribute('required', 'required');
                
                setTimeout(() => {
                    $('#surat_jalan_select').select2({ placeholder: 'Pilih Surat Jalan', allowClear: true, width: '100%' });
                    $('#jenis_penyesuaian_select').select2({ placeholder: 'Pilih Jenis Penyesuaian', allowClear: true, width: '100%' });
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
                const namaKlasifikasi = selectedOption.data('nama');
                
                if (namaKlasifikasi && namaKlasifikasi.toLowerCase().includes('buruh')) {
                    barangWrapper.classList.remove('hidden');
                    initializeBarangInputs();
                } else {
                    barangWrapper.classList.add('hidden');
                    clearBarangInputs();
                }
            });
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
        
        function addBlInput(existingBlId = '') {
            const container = document.getElementById('bl_container');
            const index = container.children.length;
            
            const inputGroup = document.createElement('div');
            inputGroup.className = 'flex items-end gap-3 p-3 bg-gray-50 rounded-md';
            
            // Build options from JavaScript data
            let blOptions = '<option value="">Pilih BL</option>';
            blsData.forEach(bl => {
                const selected = existingBlId == bl.id ? 'selected' : '';
                const nomorKontainer = bl.nomor_kontainer || 'N/A';
                const pengirim = bl.pengirim || 'N/A';
                blOptions += `<option value="${bl.id}" ${selected}>${bl.nomor_bl} - ${nomorKontainer} (${pengirim})</option>`;
            });
            
            inputGroup.innerHTML = `
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor BL</label>
                    <select name="bl_details[${index}][bl_id]" 
                            class="bl-select w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            style="height: 38px; padding: 6px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                        ${blOptions}
                    </select>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" 
                            onclick="removeBlInput(this)" 
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
                $(inputGroup).find('.bl-select').select2({
                    placeholder: 'Pilih BL',
                    allowClear: true,
                    width: '100%'
                });
            }, 100);
        }
        
        window.removeBlInput = function(button) {
            const container = document.getElementById('bl_container');
            if (container.children.length > 1) {
                button.closest('.flex.items-end.gap-3').remove();
            }
        };
        
        // Add button for BL
        const addBlBtn = document.getElementById('add_bl_btn');
        if (addBlBtn) {
            addBlBtn.addEventListener('click', function() {
                addBlInput();
            });
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
                           min="0" 
                           step="0.01"
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
                const jumlah = parseFloat(jumlahInputs[index].value) || 0;
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
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jenis Biaya</label>
                    <select name="detail_pembayaran[${index}][jenis_biaya]" 
                            class="detail-jenis-biaya w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">--Pilih Jenis Biaya--</option>
                        <option value="Uang Jalan" ${existingData.jenis_biaya === 'Uang Jalan' ? 'selected' : ''}>Uang Jalan</option>
                        <option value="Biaya Operasional" ${existingData.jenis_biaya === 'Biaya Operasional' ? 'selected' : ''}>Biaya Operasional</option>
                        <option value="Biaya Maintenance" ${existingData.jenis_biaya === 'Biaya Maintenance' ? 'selected' : ''}>Biaya Maintenance</option>
                        <option value="Biaya Lain-lain" ${existingData.jenis_biaya === 'Biaya Lain-lain' ? 'selected' : ''}>Biaya Lain-lain</option>
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
                $(inputGroup).find('.detail-jenis-biaya').select2({
                    placeholder: 'Pilih Jenis Biaya',
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
            
            // Format currency for biaya input
            const biayaInput = inputGroup.querySelector('.detail-biaya');
            biayaInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                if (value) value = parseInt(value).toLocaleString('id-ID');
                e.target.value = value;
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
        };
        
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
