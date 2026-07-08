@extends('layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'Edit Mesin')
@section('page_title', 'Edit Mesin')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center">
                <a href="{{ route('master.mesin.index') }}" class="mr-4 p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Mesin</h1>
                    <p class="mt-1 text-sm text-gray-600">Ubah data detail mesin/engine</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informasi Mesin</h3>
                <p class="mt-1 text-sm text-gray-600">Perbarui data detail mesin</p>
            </div>

            <form action="{{ route('master.mesin.update', $mesin->id) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Kode Mesin -->
                <div>
                    <label for="kode_mesin" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Kode Mesin
                    </label>
                    <input type="text"
                           name="kode_mesin"
                           id="kode_mesin"
                           value="{{ old('kode_mesin', $mesin->kode_mesin) }}"
                           required
                           maxlength="50"
                           class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                           placeholder="Masukkan kode mesin"
                           style="text-transform: uppercase;">
                    @error('kode_mesin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Mesin -->
                <div>
                    <label for="nama_mesin" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Nama Mesin
                    </label>
                    <input type="text"
                           name="nama_mesin"
                           id="nama_mesin"
                           value="{{ old('nama_mesin', $mesin->nama_mesin) }}"
                           required
                           class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                           placeholder="Masukkan nama mesin">
                    @error('nama_mesin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipe Mesin -->
                <div>
                    <label for="tipe_mesin" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Tipe Mesin
                    </label>
                    <input type="text"
                           name="tipe_mesin"
                           id="tipe_mesin"
                           value="{{ old('tipe_mesin', $mesin->tipe_mesin) }}"
                           required
                           class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                           placeholder="Masukkan tipe mesin">
                    @error('tipe_mesin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jaringan / Koneksi Mesin -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="md:col-span-3 flex justify-between items-center mb-1">
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Koneksi Fingerprint (Optional)</h4>
                            <p class="text-xs text-gray-500">Konfigurasi jaringan untuk koneksi langsung ke mesin Solution X609 (gunakan 127.0.0.1 untuk dummy/testing)</p>
                        </div>
                        <button type="button" id="btn-test-connection" class="px-3 py-1.5 bg-blue-100 text-blue-700 hover:bg-blue-200 font-semibold rounded text-xs transition-colors">
                            Test Koneksi
                        </button>
                    </div>

                    <div>
                        <label for="ip_address" class="block text-xs font-semibold text-gray-700 mb-1">IP Address</label>
                        <input type="text" name="ip_address" id="ip_address" value="{{ old('ip_address', $mesin->ip_address) }}"
                               placeholder="e.g. 192.168.1.201"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                        @error('ip_address')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="port" class="block text-xs font-semibold text-gray-700 mb-1">Port</label>
                        <input type="number" name="port" id="port" value="{{ old('port', $mesin->port ?? 4370) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                        @error('port')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="comm_key" class="block text-xs font-semibold text-gray-700 mb-1">Comm Key (Password)</label>
                        <input type="number" name="comm_key" id="comm_key" value="{{ old('comm_key', $mesin->comm_key ?? 0) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                        @error('comm_key')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Status
                    </label>
                    <select name="status"
                            id="status"
                            required
                            class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                        <option value="Aktif" {{ old('status', $mesin->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Rusak" {{ old('status', $mesin->status) == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                        <option value="Perbaikan" {{ old('status', $mesin->status) == 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                        <option value="Nonaktif" {{ old('status', $mesin->status) == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan
                    </label>
                    <textarea name="keterangan"
                              id="keterangan"
                              rows="4"
                              class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                              placeholder="Masukkan catatan atau keterangan tambahan (opsional)">{{ old('keterangan', $mesin->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('master.mesin.index') }}"
                       class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const kodeInput = document.getElementById('kode_mesin');
    if (kodeInput) {
        kodeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }

    $('#btn-test-connection').on('click', function() {
        const ip = $('#ip_address').val();
        const port = $('#port').val();
        
        if (!ip) {
            alert('IP Address harus diisi terlebih dahulu untuk melakukan tes koneksi!');
            return;
        }

        const btn = $(this);
        const originalText = btn.text();
        btn.prop('disabled', true).text('Menghubungkan...');

        $.ajax({
            url: '{{ route("master.mesin.test-connection-raw") }}',
            type: 'POST',
            data: {
                ip_address: ip,
                port: port,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                btn.prop('disabled', false).text(originalText);
                if (response.success) {
                    alert(response.message);
                } else {
                    alert('Koneksi Gagal: ' + response.message);
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).text(originalText);
                alert('Terjadi kesalahan sistem saat mencoba terhubung. Pastikan IP Address dan Port valid.');
            }
        });
    });
});
</script>
@endpush
@endsection
