@extends('layouts.app')

@section('title', 'Edit Order')
@section('page_title', 'Edit Order')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Order</h1>
                    <p class="mt-1 text-sm text-gray-600">Ubah informasi order</p>
                </div>
                <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form action="{{ route('orders.update', $order) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nomor Order -->
                        <div>
                            <label for="nomor_order" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Order <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nomor_order" id="nomor_order" value="{{ old('nomor_order', $order->nomor_order) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @error('nomor_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Order -->
                        <div>
                            <label for="tanggal_order" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Order <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_order" id="tanggal_order" value="{{ old('tanggal_order', $order->tanggal_order->format('Y-m-d')) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- No Tiket/DO -->
                        <div>
                            <label for="no_tiket_do" class="block text-sm font-medium text-gray-700 mb-2">
                                No Tiket/DO
                            </label>
                            <input type="text" name="no_tiket_do" id="no_tiket_do" value="{{ old('no_tiket_do', $order->no_tiket_do) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Pilih Status</option>
                                <option value="draft" {{ old('status', $order->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="confirmed" {{ old('status', $order->status) === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="processing" {{ old('status', $order->status) === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ old('status', $order->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $order->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Destination Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Tujuan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tujuan Kirim -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="tujuan_kirim_id" class="text-sm font-medium text-gray-700">
                                    Tujuan Kirim <span class="text-red-500">*</span>
                                </label>
                                <a href="{{ route('tujuan-kirim.create') }}" id="add_tujuan_kirim_link" target="_blank"
                                   class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                   title="Tambah">
                                    Tambah
                                </a>
                            </div>
                            <div class="relative">
                                <div class="dropdown-container">
                                    <input type="text" id="search_tujuan_kirim" placeholder="Search..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                                        <select name="tujuan_kirim_id" id="tujuan_kirim_id" required
                                            class="hidden w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500 {{ $errors->has('tujuan_kirim_id') ? 'border-red-500' : 'border-gray-300' }}">
                                        <option value="">Select an option</option>
                                        @foreach($tujuanKirims as $tujuanKirim)
                                            @php
                                                $isSelected = false;
                                                if (old('tujuan_kirim_id')) {
                                                    $isSelected = old('tujuan_kirim_id') == $tujuanKirim->id;
                                                } else {
                                                    // Check if this tujuan kirim matches the current order's tujuan_kirim_id or tujuan_kirim string
                                                    $isSelected = ($order->tujuan_kirim_id == $tujuanKirim->id) ||
                                                                  ($order->tujuan_kirim == $tujuanKirim->nama_tujuan);
                                                }
                                            @endphp
                                            <option value="{{ $tujuanKirim->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                {{ $tujuanKirim->nama_tujuan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="dropdown_options" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                        <!-- Options will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                            @error('tujuan_kirim_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="tujuan_ambil_id" class="text-sm font-medium text-gray-700">
                                    Tujuan Ambil <span class="text-red-500">*</span>
                                </label>
                                <a href="{{ route('master.tujuan-kegiatan-utama.create') }}" id="add_tujuan_ambil_link" target="_blank"
                                   class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                   title="Tambah">
                                    Tambah
                                </a>
                            </div>
                            <div class="relative">
                                <div class="dropdown-container-ambil">
                                    <input type="text" id="search_tujuan_ambil" placeholder="Search..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                                    <select name="tujuan_ambil_id" id="tujuan_ambil_id" required
                                            class="hidden w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                        <option value="">Select an option</option>
                                        @foreach($tujuanKegiatanUtamas as $tujuanKegiatanUtama)
                                            @php
                                                $isSelected = false;
                                                if (old('tujuan_ambil_id')) {
                                                    $isSelected = old('tujuan_ambil_id') == $tujuanKegiatanUtama->id;
                                                } else {
                                                    // Check if this tujuan ambil matches the current order's tujuan_ambil_id or tujuan_ambil string
                                                    $isSelected = ($order->tujuan_ambil_id == $tujuanKegiatanUtama->id) ||
                                                                  ($order->tujuan_ambil == $tujuanKegiatanUtama->ke);
                                                }
                                            @endphp
                                            <option value="{{ $tujuanKegiatanUtama->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                {{ $tujuanKegiatanUtama->ke }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="dropdown_options_ambil" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                        <!-- Options will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                            @error('tujuan_ambil_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Master Data Relations -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Data Master</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Term -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="term_id" class="text-sm font-medium text-gray-700">
                                    Term
                                </label>
                                <a href="{{ route('term.create') }}" id="add_term_link" target="_blank"
                                   class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                   title="Tambah">
                                    Tambah
                                </a>
                            </div>
                            <div class="relative">
                                <div class="dropdown-container-term">
                                    <input type="text" id="search_term" placeholder="Search..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                                    <select name="term_id" id="term_id"
                                            class="hidden w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                        <option value="">Select an option</option>
                                        @foreach($terms as $term)
                                            @php
                                                $isSelected = false;
                                                if (old('term_id')) {
                                                    $isSelected = old('term_id') == $term->id;
                                                } else {
                                                    $isSelected = $order->term_id == $term->id;
                                                }
                                            @endphp
                                            <option value="{{ $term->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                {{ $term->nama_status }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="dropdown_options_term" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                        <!-- Options will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pengirim -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="pengirim_id" class="text-sm font-medium text-gray-700">Pengirim</label>
                                <a href="{{ route('order.pengirim.create') }}" id="add_pengirim_link" target="_blank"
                                   class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700" title="Tambah">Tambah</a>
                            </div>
                            <div class="relative">
                                <div class="dropdown-container-pengirim">
                                    <input type="text" id="search_pengirim" placeholder="Search..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                                    <select name="pengirim_id" id="pengirim_id" class="hidden w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                        <option value="">Pilih Pengirim</option>
                                        @foreach($pengirims as $pengirim)
                                            <option value="{{ $pengirim->id }}" {{ old('pengirim_id', $order->pengirim_id) == $pengirim->id ? 'selected' : '' }}>
                                                {{ $pengirim->nama_pengirim }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="dropdown_options_pengirim" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Jenis Barang -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="jenis_barang_id" class="text-sm font-medium text-gray-700 mb-2">Jenis Barang</label>
                                <a href="{{ route('order.jenis-barang.create') }}" id="add_jenis_barang_link" target="_blank" class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700" title="Tambah">Tambah</a>
                            </div>
                            <div class="relative">
                                <div class="dropdown-container-jenis-barang">
                                    <input type="text" id="search_jenis_barang" placeholder="Search..." class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                                    <select name="jenis_barang_id" id="jenis_barang_id" class="hidden w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                        <option value="">Pilih Jenis Barang</option>
                                        @foreach($jenisBarangs as $jenisBarang)
                                            <option value="{{ $jenisBarang->id }}" {{ old('jenis_barang_id', $order->jenis_barang_id) == $jenisBarang->id ? 'selected' : '' }}>
                                                {{ $jenisBarang->nama_barang }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="dropdown_options_jenis_barang" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Penerima -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="penerima_id" class="text-sm font-medium text-gray-700">Penerima</label>
                                <a href="{{ route('order.penerima.create') }}" id="add_penerima_link" target="_blank"
                                   class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700" title="Tambah">Tambah</a>
                            </div>
                            <div class="relative">
                                <div class="dropdown-container-penerima">
                                    <input type="text" id="search_penerima" placeholder="Search..." class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                                    <select name="penerima_id" id="penerima_id" class="hidden w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                        <option value="">Pilih Penerima</option>
                                        @foreach($penerimas as $penerima)
                                            <option value="{{ $penerima->id }}" {{ old('penerima_id', $order->penerima_id) == $penerima->id ? 'selected' : '' }}>
                                                {{ $penerima->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="dropdown_options_penerima" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Container Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Kontainer</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Size Kontainer <span class="text-red-500">*</span></label>
                                <select name="size_kontainer" id="size_kontainer" required
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('size_kontainer') ? 'border-red-500' : 'border-gray-300' }}">
                                <option value="">Pilih Size Kontainer</option>
                                @foreach($ukuranKontainers as $ukuran)
                                    <option value="{{ $ukuran }}" {{ old('size_kontainer', $order->size_kontainer) === $ukuran ? 'selected' : '' }}>
                                        {{ $ukuran }}
                                    </option>
                                @endforeach
                            </select>
                            @error('size_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="unit_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Unit Kontainer <span class="text-red-500">*</span></label>
                            <input type="number" name="unit_kontainer" id="unit_kontainer" value="{{ old('unit_kontainer', $order->unit_kontainer) }}" required min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <small class="text-gray-500">Units untuk outstanding: {{ $order->units ?? 0 }} | Sisa: {{ $order->sisa ?? 0 }}</small>
                        </div>

                        <div>
                            <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Tipe Kontainer <span class="text-red-500">*</span></label>
                            <select name="tipe_kontainer" id="tipe_kontainer" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Pilih Tipe</option>
                                <option value="fcl" {{ old('tipe_kontainer', $order->tipe_kontainer) === 'fcl' ? 'selected' : '' }}>FCL</option>
                                <option value="lcl" {{ old('tipe_kontainer', $order->tipe_kontainer) === 'lcl' ? 'selected' : '' }}>LCL</option>
                                <option value="cargo" {{ old('tipe_kontainer', $order->tipe_kontainer) === 'cargo' ? 'selected' : '' }}>Cargo</option>
                                <option value="fcl_plus" {{ old('tipe_kontainer', $order->tipe_kontainer) === 'fcl_plus' ? 'selected' : '' }}>FCL Plus</option>
                            </select>
                        </div>

                        <div>
                            <label for="tanggal_pickup" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pickup</label>
                            <input type="date" name="tanggal_pickup" id="tanggal_pickup" value="{{ old('tanggal_pickup', $order->tanggal_pickup?->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Document Types -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tipe Dokumen</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- FTZ03 Options -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">FTZ03</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" name="ftz03_option" id="exclude_ftz03" value="exclude" {{ old('ftz03_option', ($order->exclude_ftz03 ? 'exclude' : ($order->include_ftz03 ? 'include' : 'none'))) === 'exclude' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="exclude_ftz03" class="ml-2 block text-sm text-gray-900">Exclude FTZ03</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="ftz03_option" id="include_ftz03" value="include" {{ old('ftz03_option', ($order->exclude_ftz03 ? 'exclude' : ($order->include_ftz03 ? 'include' : 'none'))) === 'include' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="include_ftz03" class="ml-2 block text-sm text-gray-900">Include FTZ03</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="ftz03_option" id="none_ftz03" value="none" {{ old('ftz03_option', ($order->exclude_ftz03 ? 'exclude' : ($order->include_ftz03 ? 'include' : 'none'))) === 'none' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="none_ftz03" class="ml-2 block text-sm text-gray-900">Tidak ada</label>
                                </div>
                            </div>
                        </div>

                        <!-- SPPB Options -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">SPPB</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" name="sppb_option" id="exclude_sppb" value="exclude" {{ old('sppb_option', ($order->exclude_sppb ? 'exclude' : ($order->include_sppb ? 'include' : 'none'))) === 'exclude' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="exclude_sppb" class="ml-2 block text-sm text-gray-900">Exclude SPPB</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="sppb_option" id="include_sppb" value="include" {{ old('sppb_option', ($order->exclude_sppb ? 'exclude' : ($order->include_sppb ? 'include' : 'none'))) === 'include' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="include_sppb" class="ml-2 block text-sm text-gray-900">Include SPPB</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="sppb_option" id="none_sppb" value="none" {{ old('sppb_option', ($order->exclude_sppb ? 'exclude' : ($order->include_sppb ? 'include' : 'none'))) === 'none' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="none_sppb" class="ml-2 block text-sm text-gray-900">Tidak ada</label>
                                </div>
                            </div>
                        </div>

                        <!-- Buruh Bongkar Options -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Buruh Bongkar</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" name="buruh_bongkar_option" id="exclude_buruh_bongkar" value="exclude" {{ old('buruh_bongkar_option', ($order->exclude_buruh_bongkar ? 'exclude' : ($order->include_buruh_bongkar ? 'include' : 'none'))) === 'exclude' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="exclude_buruh_bongkar" class="ml-2 block text-sm text-gray-900">Exclude Buruh Bongkar</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="buruh_bongkar_option" id="include_buruh_bongkar" value="include" {{ old('buruh_bongkar_option', ($order->exclude_buruh_bongkar ? 'exclude' : ($order->include_buruh_bongkar ? 'include' : 'none'))) === 'include' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="include_buruh_bongkar" class="ml-2 block text-sm text-gray-900">Include Buruh Bongkar</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="buruh_bongkar_option" id="none_buruh_bongkar" value="none" {{ old('buruh_bongkar_option', ($order->exclude_buruh_bongkar ? 'exclude' : ($order->include_buruh_bongkar ? 'include' : 'none'))) === 'none' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="none_buruh_bongkar" class="ml-2 block text-sm text-gray-900">Tidak ada</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Masukkan catatan tambahan (opsional)">{{ old('catatan', $order->catatan) }}</textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to create searchable dropdown
    function createSearchableDropdown(config) {
        const selectElement = document.getElementById(config.selectId);
        const searchInput = document.getElementById(config.searchId);
        const dropdownOptions = document.getElementById(config.dropdownId);
        let originalOptions = Array.from(selectElement.options);

        // Allow dropdown options to be refreshed (used when a new item was added in a popup)
        function refreshOriginalOptions() {
            originalOptions = Array.from(selectElement.options);
        }
        // Expose a refresh function for this select so popup adds can refresh the in-memory option list
        try {
            window['refresh_' + config.selectId + '_options'] = refreshOriginalOptions;
        } catch (err) {
            // ignore errors if window is not defined
        }

        // Get current selected value for editing
        const currentValue = selectElement.value;
        const currentText = selectElement.options[selectElement.selectedIndex]?.text || '';

        // Set initial search input value
        if (currentValue && currentText !== 'Select an option') {
            searchInput.value = currentText;
        }

        // Initially populate dropdown options
        populateDropdown(originalOptions);

        // Show dropdown when search input is focused or clicked
        searchInput.addEventListener('focus', function() {
            dropdownOptions.classList.remove('hidden');
        });

        searchInput.addEventListener('click', function() {
            dropdownOptions.classList.remove('hidden');
        });

        // Filter options based on search
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const filteredOptions = originalOptions.filter(option => {
                if (option.value === '') return true;
                return option.text.toLowerCase().includes(searchTerm);
            });
            populateDropdown(filteredOptions);
            dropdownOptions.classList.remove('hidden');
        });

        // Populate dropdown with options
        function populateDropdown(options) {
            dropdownOptions.innerHTML = '';
            options.forEach(option => {
                const div = document.createElement('div');
                div.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                div.textContent = option.text;
                div.setAttribute('data-value', option.value);

                div.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    const text = this.textContent;

                    // Set the select value
                    selectElement.value = value;

                    // Update search input
                    if (value === '') {
                        searchInput.value = '';
                        searchInput.placeholder = 'Search...';
                    } else {
                        searchInput.value = text;
                    }

                    // Hide dropdown
                    dropdownOptions.classList.add('hidden');

                    // Trigger change event
                    selectElement.dispatchEvent(new Event('change'));
                });

                dropdownOptions.appendChild(div);
            });
        }

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.' + config.containerClass)) {
                dropdownOptions.classList.add('hidden');
            }
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdownOptions.classList.add('hidden');
            }
        });
    }

    // Initialize Tujuan Kirim dropdown
    createSearchableDropdown({
        selectId: 'tujuan_kirim_id',
        searchId: 'search_tujuan_kirim',
        dropdownId: 'dropdown_options',
        containerClass: 'dropdown-container'
    });

    // Initialize Tujuan Ambil dropdown
    createSearchableDropdown({
        selectId: 'tujuan_ambil_id',
        searchId: 'search_tujuan_ambil',
        dropdownId: 'dropdown_options_ambil',
        containerClass: 'dropdown-container-ambil'
    });

    // Initialize Term dropdown
    createSearchableDropdown({
        selectId: 'term_id',
        searchId: 'search_term',
        dropdownId: 'dropdown_options_term',
        containerClass: 'dropdown-container-term'
    });

    // Initialize Pengirim dropdown (was missing originally)
    createSearchableDropdown({
        selectId: 'pengirim_id',
        searchId: 'search_pengirim',
        dropdownId: 'dropdown_options_pengirim',
        containerClass: 'dropdown-container-pengirim'
    });

    // Initialize Jenis Barang dropdown (was missing originally)
    createSearchableDropdown({
        selectId: 'jenis_barang_id',
        searchId: 'search_jenis_barang',
        dropdownId: 'dropdown_options_jenis_barang',
        containerClass: 'dropdown-container-jenis-barang'
    });

    // Initialize Penerima dropdown
    createSearchableDropdown({
        selectId: 'penerima_id',
        searchId: 'search_penerima',
        dropdownId: 'dropdown_options_penerima',
        containerClass: 'dropdown-container-penerima'
    });

    // Handle popup add events and update selects in-place (don't reload page)
    window.addEventListener('message', function(event) {
        try {
            if (!event.data || !event.data.type) return;

            if (event.data.type === 'tujuan-kirim-added') {
                const tujuanKirimSelect = document.getElementById('tujuan_kirim_id');
                const searchTujuanKirimInput = document.getElementById('search_tujuan_kirim');
                const dropdownOptionsTujuanKirim = document.getElementById('dropdown_options');

                if (tujuanKirimSelect && event.data.data) {
                    const newOption = document.createElement('option');
                    newOption.value = event.data.data.id;
                    newOption.textContent = event.data.data.nama_tujuan;
                    tujuanKirimSelect.appendChild(newOption);
                    tujuanKirimSelect.value = event.data.data.id;
                    if (searchTujuanKirimInput) searchTujuanKirimInput.value = event.data.data.nama_tujuan;
                    if (dropdownOptionsTujuanKirim) {
                        const newOptionDiv = document.createElement('div');
                        newOptionDiv.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                        newOptionDiv.textContent = event.data.data.nama_tujuan;
                        newOptionDiv.setAttribute('data-value', event.data.data.id);
                        newOptionDiv.addEventListener('click', function() {
                            tujuanKirimSelect.value = this.getAttribute('data-value');
                            searchTujuanKirimInput.value = this.textContent;
                            dropdownOptionsTujuanKirim.classList.add('hidden');
                            tujuanKirimSelect.dispatchEvent(new Event('change'));
                        });
                        if (dropdownOptionsTujuanKirim.children.length > 1) {
                            dropdownOptionsTujuanKirim.insertBefore(newOptionDiv, dropdownOptionsTujuanKirim.children[1]);
                        } else {
                            dropdownOptionsTujuanKirim.appendChild(newOptionDiv);
                        }
                        dropdownOptionsTujuanKirim.classList.add('hidden');
                    }
                    // Refresh original options for the searchable dropdown
                    if (window['refresh_tujuan_kirim_id_options']) {
                        window['refresh_tujuan_kirim_id_options']();
                    }
                    tujuanKirimSelect.dispatchEvent(new Event('change'));
                    showNotification('Tujuan Kirim "' + event.data.data.nama_tujuan + '" berhasil ditambahkan dan dipilih!', 'success');
                }
            } else if (event.data.type === 'pengirim-added') {
                const pengirimSelect = document.getElementById('pengirim_id');
                const searchPengirimInput = document.getElementById('search_pengirim');
                const dropdownOptionsPengirim = document.getElementById('dropdown_options_pengirim');
                if (pengirimSelect && event.data.data) {
                    const newOption = document.createElement('option');
                    newOption.value = event.data.data.id;
                    newOption.textContent = event.data.data.nama_pengirim;
                    pengirimSelect.appendChild(newOption);
                    pengirimSelect.value = event.data.data.id;
                    if (searchPengirimInput) searchPengirimInput.value = event.data.data.nama_pengirim;
                    if (dropdownOptionsPengirim) {
                        const newOptionDiv = document.createElement('div');
                        newOptionDiv.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                        newOptionDiv.textContent = event.data.data.nama_pengirim;
                        newOptionDiv.setAttribute('data-value', event.data.data.id);
                        newOptionDiv.addEventListener('click', function() {
                            pengirimSelect.value = this.getAttribute('data-value');
                            searchPengirimInput.value = this.textContent;
                            dropdownOptionsPengirim.classList.add('hidden');
                            pengirimSelect.dispatchEvent(new Event('change'));
                        });
                        if (dropdownOptionsPengirim.children.length > 1) {
                            dropdownOptionsPengirim.insertBefore(newOptionDiv, dropdownOptionsPengirim.children[1]);
                        } else {
                            dropdownOptionsPengirim.appendChild(newOptionDiv);
                        }
                    }
                    if (dropdownOptionsPengirim) dropdownOptionsPengirim.classList.add('hidden');
                    if (window['refresh_pengirim_id_options']) {
                        window['refresh_pengirim_id_options']();
                    }
                    pengirimSelect.dispatchEvent(new Event('change'));
                    showNotification('Pengirim "' + event.data.data.nama_pengirim + '" berhasil ditambahkan dan dipilih!', 'success');
                }
            } else if (event.data.type === 'penerima-added') {
                const penerimaSelect = document.getElementById('penerima_id');
                const searchPenerimaInput = document.getElementById('search_penerima');
                const dropdownOptionsPenerima = document.getElementById('dropdown_options_penerima');
                if (penerimaSelect && event.data.data) {
                    const newOption = document.createElement('option');
                    newOption.value = event.data.data.id;
                    newOption.textContent = event.data.data.nama;
                    penerimaSelect.appendChild(newOption);
                    penerimaSelect.value = event.data.data.id;
                    if (searchPenerimaInput) searchPenerimaInput.value = event.data.data.nama;
                    if (dropdownOptionsPenerima) {
                        const newOptionDiv = document.createElement('div');
                        newOptionDiv.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                        newOptionDiv.textContent = event.data.data.nama;
                        newOptionDiv.setAttribute('data-value', event.data.data.id);
                        newOptionDiv.addEventListener('click', function() {
                            penerimaSelect.value = this.getAttribute('data-value');
                            searchPenerimaInput.value = this.textContent;
                            dropdownOptionsPenerima.classList.add('hidden');
                            penerimaSelect.dispatchEvent(new Event('change'));
                        });
                        if (dropdownOptionsPenerima.children.length > 1) {
                            dropdownOptionsPenerima.insertBefore(newOptionDiv, dropdownOptionsPenerima.children[1]);
                        } else {
                            dropdownOptionsPenerima.appendChild(newOptionDiv);
                        }
                    }
                    if (dropdownOptionsPenerima) dropdownOptionsPenerima.classList.add('hidden');
                    if (window['refresh_penerima_id_options']) {
                        window['refresh_penerima_id_options']();
                    }
                    penerimaSelect.dispatchEvent(new Event('change'));
                    showNotification('Penerima "' + event.data.data.nama + '" berhasil ditambahkan dan dipilih!', 'success');
                }
            } else if (event.data.type === 'jenis-barang-added') {
                const jenisBarangSelect = document.getElementById('jenis_barang_id');
                const searchJenisBarangInput = document.getElementById('search_jenis_barang');
                const dropdownOptionsJenisBarang = document.getElementById('dropdown_options_jenis_barang');
                if (jenisBarangSelect && event.data.data) {
                    const newOption = document.createElement('option');
                    newOption.value = event.data.data.id;
                    newOption.textContent = event.data.data.nama_barang;
                    jenisBarangSelect.appendChild(newOption);
                    jenisBarangSelect.value = event.data.data.id;
                    if (searchJenisBarangInput) searchJenisBarangInput.value = event.data.data.nama_barang;
                    if (dropdownOptionsJenisBarang) {
                        const newOptionDiv = document.createElement('div');
                        newOptionDiv.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                        newOptionDiv.textContent = event.data.data.nama_barang;
                        newOptionDiv.setAttribute('data-value', event.data.data.id);
                        newOptionDiv.addEventListener('click', function() {
                            jenisBarangSelect.value = this.getAttribute('data-value');
                            searchJenisBarangInput.value = this.textContent;
                            dropdownOptionsJenisBarang.classList.add('hidden');
                            jenisBarangSelect.dispatchEvent(new Event('change'));
                        });
                        if (dropdownOptionsJenisBarang.children.length > 1) {
                            dropdownOptionsJenisBarang.insertBefore(newOptionDiv, dropdownOptionsJenisBarang.children[1]);
                        } else {
                            dropdownOptionsJenisBarang.appendChild(newOptionDiv);
                        }
                    }
                    if (dropdownOptionsJenisBarang) dropdownOptionsJenisBarang.classList.add('hidden');
                    if (window['refresh_jenis_barang_id_options']) {
                        window['refresh_jenis_barang_id_options']();
                    }
                    jenisBarangSelect.dispatchEvent(new Event('change'));
                    showNotification('Jenis Barang "' + event.data.data.nama_barang + '" berhasil ditambahkan dan dipilih!', 'success');
                }
            } else if (event.data.type === 'tujuan-ambil-added') {
                const tujuanAmbilSelect = document.getElementById('tujuan_ambil_id');
                const searchTujuanAmbilInput = document.getElementById('search_tujuan_ambil');
                const dropdownOptionsTujuanAmbil = document.getElementById('dropdown_options_ambil');
                if (tujuanAmbilSelect && event.data.data) {
                    const newOption = document.createElement('option');
                    newOption.value = event.data.data.id;
                    newOption.textContent = event.data.data.nama_tujuan;
                    tujuanAmbilSelect.appendChild(newOption);
                    tujuanAmbilSelect.value = event.data.data.id;
                    if (searchTujuanAmbilInput) searchTujuanAmbilInput.value = event.data.data.nama_tujuan;
                    if (dropdownOptionsTujuanAmbil) {
                        const newOptionDiv = document.createElement('div');
                        newOptionDiv.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                        newOptionDiv.textContent = event.data.data.nama_tujuan;
                        newOptionDiv.setAttribute('data-value', event.data.data.id);
                        newOptionDiv.addEventListener('click', function() {
                            tujuanAmbilSelect.value = this.getAttribute('data-value');
                            searchTujuanAmbilInput.value = this.textContent;
                            dropdownOptionsTujuanAmbil.classList.add('hidden');
                            tujuanAmbilSelect.dispatchEvent(new Event('change'));
                        });
                        if (dropdownOptionsTujuanAmbil.children.length > 1) {
                            dropdownOptionsTujuanAmbil.insertBefore(newOptionDiv, dropdownOptionsTujuanAmbil.children[1]);
                        } else {
                            dropdownOptionsTujuanAmbil.appendChild(newOptionDiv);
                        }
                    }
                    if (dropdownOptionsTujuanAmbil) dropdownOptionsTujuanAmbil.classList.add('hidden');
                    if (window['refresh_tujuan_ambil_id_options']) {
                        window['refresh_tujuan_ambil_id_options']();
                    }
                    tujuanAmbilSelect.dispatchEvent(new Event('change'));
                    showNotification('Tujuan Ambil "' + event.data.data.nama_tujuan + '" berhasil ditambahkan dan dipilih!', 'success');
                }
            } else if (event.data.type === 'term-added') {
                const termSelect = document.getElementById('term_id');
                const searchTermInput = document.getElementById('search_term');
                const dropdownOptionsElement = document.getElementById('dropdown_options_term');
                if (termSelect && event.data.data) {
                    const newOption = document.createElement('option');
                    newOption.value = event.data.data.id;
                    newOption.textContent = event.data.data.nama_status;
                    termSelect.appendChild(newOption);
                    termSelect.value = event.data.data.id;
                    if (searchTermInput) searchTermInput.value = event.data.data.nama_status;
                    if (dropdownOptionsElement) {
                        const newOptionDiv = document.createElement('div');
                        newOptionDiv.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                        newOptionDiv.textContent = event.data.data.nama_status;
                        newOptionDiv.setAttribute('data-value', event.data.data.id);
                        newOptionDiv.addEventListener('click', function() {
                            termSelect.value = this.getAttribute('data-value');
                            searchTermInput.value = this.textContent;
                            dropdownOptionsElement.classList.add('hidden');
                            termSelect.dispatchEvent(new Event('change'));
                        });
                        if (dropdownOptionsElement.children.length > 1) {
                            dropdownOptionsElement.insertBefore(newOptionDiv, dropdownOptionsElement.children[1]);
                        } else {
                            dropdownOptionsElement.appendChild(newOptionDiv);
                        }
                    }
                    // Refresh searchable dropdown original options for term
                    if (window.refreshTermOptions) {
                        window.refreshTermOptions();
                    }
                    if (window['refresh_term_id_options']) {
                        window['refresh_term_id_options']();
                    }
                    if (dropdownOptionsElement) dropdownOptionsElement.classList.add('hidden');
                    termSelect.dispatchEvent(new Event('change'));
                    showNotification('Term "' + event.data.data.nama_status + '" berhasil ditambahkan dan dipilih!', 'success');
                }
            }
        } catch (err) {
            console.error('Error handling popup message', err);
        }
    });

    // Add link popup handlers (open popup and pass search term)
    const addTermLink = document.getElementById('add_term_link');
    const searchTermInput = document.getElementById('search_term');
    if (addTermLink && searchTermInput) {
        addTermLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = searchTermInput.value.trim();
            let url = "{{ route('term.create') }}";
            const params = new URLSearchParams();
            params.append('popup', '1');
            if (searchValue) params.append('search', searchValue);
            url += '?' + params.toString();
            const popup = window.open(url, 'addTerm', 'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no');
            if (popup) popup.focus();
        });
    }

    const addTujuanKirimLink = document.getElementById('add_tujuan_kirim_link');
    const searchTujuanKirimInput = document.getElementById('search_tujuan_kirim');
    if (addTujuanKirimLink && searchTujuanKirimInput) {
        addTujuanKirimLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = searchTujuanKirimInput.value.trim();
            let url = "{{ route('tujuan-kirim.create') }}";
            const params = new URLSearchParams();
            params.append('popup', '1');
            if (searchValue) params.append('search', searchValue);
            url += '?' + params.toString();
            const popup = window.open(url, 'addTujuanKirim', 'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no');
            if (popup) popup.focus();
        });
    }

    const addTujuanAmbilLink = document.getElementById('add_tujuan_ambil_link');
    const searchTujuanAmbilInput = document.getElementById('search_tujuan_ambil');
    if (addTujuanAmbilLink && searchTujuanAmbilInput) {
        addTujuanAmbilLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = searchTujuanAmbilInput.value.trim();
            let url = "{{ route('master.tujuan-kegiatan-utama.create') }}";
            const params = new URLSearchParams();
            params.append('popup', '1');
            if (searchValue) params.append('search', searchValue);
            url += '?' + params.toString();
            const popup = window.open(url, 'addTujuanAmbil', 'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no');
            if (popup) popup.focus();
        });
    }

    const addPengirimLink = document.getElementById('add_pengirim_link');
    const searchPengirimInput = document.getElementById('search_pengirim');
    if (addPengirimLink && searchPengirimInput) {
        addPengirimLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = searchPengirimInput.value.trim();
            let url = "{{ route('order.pengirim.create') }}";
            const params = new URLSearchParams();
            params.append('popup', '1');
            if (searchValue) params.append('search', searchValue);
            url += '?' + params.toString();
            const popup = window.open(url, 'addPengirim', 'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no');
            if (popup) popup.focus();
        });
    }

    // Add handler for Penerima popup
    const addPenerimaLink = document.getElementById('add_penerima_link');
    const searchPenerimaInput = document.getElementById('search_penerima');
    if (addPenerimaLink && searchPenerimaInput) {
        addPenerimaLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = searchPenerimaInput.value.trim();
            let url = "{{ route('order.penerima.create') }}";
            const params = new URLSearchParams();
            params.append('popup', '1');
            if (searchValue) params.append('search', searchValue);
            url += '?' + params.toString();
            const popup = window.open(url, 'addPenerima', 'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no');
            if (popup) popup.focus();
        });
    }

    const addJenisBarangLink = document.getElementById('add_jenis_barang_link');
    const searchJenisBarangInput = document.getElementById('search_jenis_barang');
    if (addJenisBarangLink && searchJenisBarangInput) {
        addJenisBarangLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = searchJenisBarangInput.value.trim();
            let url = "{{ route('order.jenis-barang.create') }}";
            const params = new URLSearchParams();
            params.append('popup', '1');
            if (searchValue) params.append('search', searchValue);
            url += '?' + params.toString();
            const popup = window.open(url, 'addJenisBarang', 'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no');
            if (popup) popup.focus();
        });
    }

    // Helper notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
            type === 'success' ? 'bg-green-500 text-white' : type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => { notification.remove(); }, 3000);
    }
});
</script>
@endpush
