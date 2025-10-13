@extends('layouts.app')

@section('title', 'Tambah Order')
@section('page_title', 'Tambah Order')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Tambah Order Baru</h1>
                    <p class="mt-1 text-sm text-gray-600">Masukkan informasi order yang akan ditambahkan</p>
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
            <form action="{{ route('orders.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Basic Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nomor Order -->
                        <div>
                            <label for="nomor_order" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Order <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nomor_order" id="nomor_order" value="{{ old('nomor_order') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('nomor_order') border-red-500 @enderror"
                                   placeholder="Masukkan nomor order">
                            @error('nomor_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Order -->
                        <div>
                            <label for="tanggal_order" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Order <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_order" id="tanggal_order" value="{{ old('tanggal_order') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_order') border-red-500 @enderror">
                            @error('tanggal_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- No Tiket/DO -->
                        <div>
                            <label for="no_tiket_do" class="block text-sm font-medium text-gray-700 mb-2">
                                No Tiket/DO
                            </label>
                            <input type="text" name="no_tiket_do" id="no_tiket_do" value="{{ old('no_tiket_do') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('no_tiket_do') border-red-500 @enderror"
                                   placeholder="Masukkan no tiket/DO">
                            @error('no_tiket_do')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror">
                                <option value="">Pilih Status</option>
                                <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="confirmed" {{ old('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="processing" {{ old('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Destination Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Tujuan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tujuan Kirim -->
                        <div>
                            <label for="tujuan_kirim" class="block text-sm font-medium text-gray-700 mb-2">
                                Tujuan Kirim <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tujuan_kirim" id="tujuan_kirim" value="{{ old('tujuan_kirim') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_kirim') border-red-500 @enderror"
                                   placeholder="Masukkan tujuan kirim">
                            @error('tujuan_kirim')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tujuan Ambil -->
                        <div>
                            <label for="tujuan_ambil" class="block text-sm font-medium text-gray-700 mb-2">
                                Tujuan Ambil <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tujuan_ambil" id="tujuan_ambil" value="{{ old('tujuan_ambil') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_ambil') border-red-500 @enderror"
                                   placeholder="Masukkan tujuan ambil">
                            @error('tujuan_ambil')
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
                            <label for="term_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Term
                            </label>
                            <select name="term_id" id="term_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('term_id') border-red-500 @enderror">
                                <option value="">Pilih Term</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                        {{ $term->kode }} - {{ $term->nama_status }}
                                    </option>
                                @endforeach
                            </select>
                            @error('term_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pengirim -->
                        <div>
                            <label for="pengirim_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Pengirim
                            </label>
                            <select name="pengirim_id" id="pengirim_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('pengirim_id') border-red-500 @enderror">
                                <option value="">Pilih Pengirim</option>
                                @foreach($pengirims as $pengirim)
                                    <option value="{{ $pengirim->id }}" {{ old('pengirim_id') == $pengirim->id ? 'selected' : '' }}>
                                        {{ $pengirim->kode }} - {{ $pengirim->nama_pengirim }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pengirim_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jenis Barang -->
                        <div>
                            <label for="jenis_barang_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Barang
                            </label>
                            <select name="jenis_barang_id" id="jenis_barang_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('jenis_barang_id') border-red-500 @enderror">
                                <option value="">Pilih Jenis Barang</option>
                                @foreach($jenisBarangs as $jenisBarang)
                                    <option value="{{ $jenisBarang->id }}" {{ old('jenis_barang_id') == $jenisBarang->id ? 'selected' : '' }}>
                                        {{ $jenisBarang->kode }} - {{ $jenisBarang->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_barang_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Container Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Kontainer</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Size Kontainer -->
                        <div>
                            <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                                Size Kontainer <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="size_kontainer" id="size_kontainer" value="{{ old('size_kontainer') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('size_kontainer') border-red-500 @enderror"
                                   placeholder="20ft, 40ft, dll">
                            @error('size_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Unit Kontainer -->
                        <div>
                            <label for="unit_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                                Unit Kontainer <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="unit_kontainer" id="unit_kontainer" value="{{ old('unit_kontainer') }}" required min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('unit_kontainer') border-red-500 @enderror"
                                   placeholder="Jumlah unit">
                            @error('unit_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipe Kontainer -->
                        <div>
                            <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipe Kontainer <span class="text-red-500">*</span>
                            </label>
                            <select name="tipe_kontainer" id="tipe_kontainer" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('tipe_kontainer') border-red-500 @enderror">
                                <option value="">Pilih Tipe</option>
                                <option value="fcl" {{ old('tipe_kontainer') === 'fcl' ? 'selected' : '' }}>FCL</option>
                                <option value="lcl" {{ old('tipe_kontainer') === 'lcl' ? 'selected' : '' }}>LCL</option>
                                <option value="cargo" {{ old('tipe_kontainer') === 'cargo' ? 'selected' : '' }}>Cargo</option>
                                <option value="fcl_plus" {{ old('tipe_kontainer') === 'fcl_plus' ? 'selected' : '' }}>FCL Plus</option>
                            </select>
                            @error('tipe_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Pickup -->
                        <div>
                            <label for="tanggal_pickup" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Pickup
                            </label>
                            <input type="date" name="tanggal_pickup" id="tanggal_pickup" value="{{ old('tanggal_pickup') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_pickup') border-red-500 @enderror">
                            @error('tanggal_pickup')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                                    <input type="radio" name="ftz03_option" id="exclude_ftz03" value="exclude" {{ old('ftz03_option') === 'exclude' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="exclude_ftz03" class="ml-2 block text-sm text-gray-900">Exclude FTZ03</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="ftz03_option" id="include_ftz03" value="include" {{ old('ftz03_option') === 'include' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="include_ftz03" class="ml-2 block text-sm text-gray-900">Include FTZ03</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="ftz03_option" id="none_ftz03" value="none" {{ old('ftz03_option', 'none') === 'none' ? 'checked' : '' }}
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
                                    <input type="radio" name="sppb_option" id="exclude_sppb" value="exclude" {{ old('sppb_option') === 'exclude' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="exclude_sppb" class="ml-2 block text-sm text-gray-900">Exclude SPPB</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="sppb_option" id="include_sppb" value="include" {{ old('sppb_option') === 'include' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="include_sppb" class="ml-2 block text-sm text-gray-900">Include SPPB</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="sppb_option" id="none_sppb" value="none" {{ old('sppb_option', 'none') === 'none' ? 'checked' : '' }}
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
                                    <input type="radio" name="buruh_bongkar_option" id="exclude_buruh_bongkar" value="exclude" {{ old('buruh_bongkar_option') === 'exclude' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="exclude_buruh_bongkar" class="ml-2 block text-sm text-gray-900">Exclude Buruh Bongkar</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="buruh_bongkar_option" id="include_buruh_bongkar" value="include" {{ old('buruh_bongkar_option') === 'include' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="include_buruh_bongkar" class="ml-2 block text-sm text-gray-900">Include Buruh Bongkar</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="buruh_bongkar_option" id="none_buruh_bongkar" value="none" {{ old('buruh_bongkar_option', 'none') === 'none' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="none_buruh_bongkar" class="ml-2 block text-sm text-gray-900">Tidak ada</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea name="catatan" id="catatan" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('catatan') border-red-500 @enderror"
                              placeholder="Masukkan catatan tambahan (opsional)">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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
                        Simpan Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
