@extends('layouts.app')

@section('title', 'Edit Pembayaran Pranota Surat Jalan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">Edit Pembayaran Pranota Surat Jalan</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ $pembayaranPranotaSuratJalan->nomor_pembayaran }}</p>
                    </div>
                    <a href="{{ route('pembayaran-pranota-surat-jalan.show', $pembayaranPranotaSuratJalan->id) }}" class="text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('pembayaran-pranota-surat-jalan.update', $pembayaranPranotaSuratJalan->id) }}" enctype="multipart/form-data" class="px-6 py-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pranota Surat Jalan -->
                    <div class="md:col-span-2">
                        <label for="pranota_surat_jalan_id" class="block text-sm font-medium text-gray-700 mb-2">Pranota Surat Jalan *</label>
                        <select name="pranota_surat_jalan_id" id="pranota_surat_jalan_id" required class="{{ 'w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent ' . ($errors->has('pranota_surat_jalan_id') ? 'border-red-500' : 'border-gray-300') }}">
                            <option value="">Pilih Pranota Surat Jalan</option>
                            @foreach($pranotaSuratJalan as $pranota)
                                <option value="{{ $pranota->id }}" {{ old('pranota_surat_jalan_id', $pembayaranPranotaSuratJalan->pranota_surat_jalan_id) == $pranota->id ? 'selected' : '' }}>
                                    {{ $pranota->nomor_pranota }} - {{ $pranota->formatted_total ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('pranota_surat_jalan_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor Pembayaran -->
                    <div>
                        <label for="nomor_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">Nomor Pembayaran *</label>
                        <input type="text" name="nomor_pembayaran" id="nomor_pembayaran" value="{{ old('nomor_pembayaran', $pembayaranPranotaSuratJalan->nomor_pembayaran) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nomor_pembayaran') border-red-500 @enderror" placeholder="Contoh: PAY-SJ-20231023-0001">
                        @error('nomor_pembayaran')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Pembayaran -->
                    <div>
                        <label for="tanggal_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pembayaran *</label>
                        <input type="datetime-local" name="tanggal_pembayaran" id="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', $pembayaranPranotaSuratJalan->tanggal_pembayaran->format('Y-m-d\TH:i')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_pembayaran') border-red-500 @enderror">
                        @error('tanggal_pembayaran')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Metode Pembayaran -->
                    <div>
                        <label for="metode_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran *</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('metode_pembayaran') border-red-500 @enderror">
                            <option value="">Pilih Metode Pembayaran</option>
                            @foreach($methods as $value => $label)
                                <option value="{{ $value }}" {{ old('metode_pembayaran', $pembayaranPranotaSuratJalan->metode_pembayaran) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('metode_pembayaran')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor Referensi -->
                    <div>
                        <label for="nomor_referensi" class="block text-sm font-medium text-gray-700 mb-2">Nomor Referensi</label>
                        <input type="text" name="nomor_referensi" id="nomor_referensi" value="{{ old('nomor_referensi', $pembayaranPranotaSuratJalan->nomor_referensi) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nomor_referensi') border-red-500 @enderror" placeholder="Nomor referensi bank/cek/giro">
                        @error('nomor_referensi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah Pembayaran -->
                    <div>
                        <label for="jumlah_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Pembayaran *</label>
                        <input type="number" name="jumlah_pembayaran" id="jumlah_pembayaran" value="{{ old('jumlah_pembayaran', $pembayaranPranotaSuratJalan->jumlah_pembayaran) }}" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jumlah_pembayaran') border-red-500 @enderror" placeholder="0.00">
                        @error('jumlah_pembayaran')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Pembayaran -->
                    <div>
                        <label for="status_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran *</label>
                        <select name="status_pembayaran" id="status_pembayaran" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status_pembayaran') border-red-500 @enderror">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ old('status_pembayaran', $pembayaranPranotaSuratJalan->status_pembayaran) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status_pembayaran')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Bukti Pembayaran -->
                    @if($pembayaranPranotaSuratJalan->bukti_pembayaran)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Pembayaran Saat Ini</label>
                        <div class="border rounded-lg p-3 bg-gray-50">
                            @php
                                $fileExtension = pathinfo($pembayaranPranotaSuratJalan->bukti_pembayaran, PATHINFO_EXTENSION);
                                $isPdf = strtolower($fileExtension) === 'pdf';
                            @endphp

                            @if($isPdf)
                                <div class="flex items-center space-x-3">
                                    <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm text-gray-700">Dokumen PDF</p>
                                        <a href="{{ Storage::url($pembayaranPranotaSuratJalan->bukti_pembayaran) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">Lihat Dokumen</a>
                                    </div>
                                </div>
                            @else
                                <img src="{{ Storage::url($pembayaranPranotaSuratJalan->bukti_pembayaran) }}" alt="Bukti Pembayaran" class="max-w-xs h-auto rounded">
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Bukti Pembayaran -->
                    <div class="md:col-span-2">
                        <label for="bukti_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $pembayaranPranotaSuratJalan->bukti_pembayaran ? 'Ganti Bukti Pembayaran' : 'Bukti Pembayaran' }}
                        </label>
                        <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" accept=".jpg,.jpeg,.png,.pdf" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bukti_pembayaran') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG, PDF (Max: 2MB) - Biarkan kosong jika tidak ingin mengubah</p>
                        @error('bukti_pembayaran')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="md:col-span-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('keterangan') border-red-500 @enderror" placeholder="Keterangan tambahan...">{{ old('keterangan', $pembayaranPranotaSuratJalan->keterangan) }}</textarea>
                        @error('keterangan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                    <a href="{{ route('pembayaran-pranota-surat-jalan.show', $pembayaranPranotaSuratJalan->id) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->any())
<div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <p class="font-medium">Terdapat kesalahan:</p>
    <ul class="text-sm mt-1">
        @foreach($errors->all() as $error)
            <li>• {{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@endsection
