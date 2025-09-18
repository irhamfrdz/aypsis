@extends('layouts.app')

@section('title', 'Tambah Perbaikan Kontainer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Tambah Perbaikan Kontainer</h1>
                    <p class="text-gray-600 mt-1">Masukkan data perbaikan kontainer baru</p>
                </div>
                <a href="{{ route('perbaikan-kontainer.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>

            <!-- Form -->
            <form action="{{ route('perbaikan-kontainer.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Nomor Tagihan -->
                <div>
                    <label for="nomor_tagihan" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Tagihan
                    </label>
                    <input type="text" id="nomor_tagihan" name="nomor_tagihan"
                           value="{{ old('nomor_tagihan') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Masukkan nomor tagihan...">
                    @error('nomor_tagihan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kontainer Selection -->
                <div>
                    <label for="nomor_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Kontainer <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nomor_kontainer" name="nomor_kontainer"
                           value="{{ old('nomor_kontainer') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Masukkan nomor kontainer..."
                           required>
                    @error('nomor_kontainer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Vendor/Bengkel -->
                <div>
                    <label for="vendor_bengkel_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Vendor/Bengkel
                    </label>
                    <select id="vendor_bengkel_id" name="vendor_bengkel_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Vendor/Bengkel...</option>
                        @foreach($vendorBengkels as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_bengkel_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->nama_bengkel }}
                            </option>
                        @endforeach
                    </select>
                    @error('vendor_bengkel_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status
                    </label>
                    <select id="status" name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="belum_masuk_pranota" {{ old('status') == 'belum_masuk_pranota' ? 'selected' : '' }}>Belum Masuk Pranota</option>
                        <option value="sudah_masuk_pranota" {{ old('status') == 'sudah_masuk_pranota' ? 'selected' : '' }}>Sudah Masuk Pranota</option>
                        <option value="sudah_dibayar" {{ old('status') == 'sudah_dibayar' ? 'selected' : '' }}>Sudah Dibayar</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estimasi Kerusakan Kontainer -->
                <div>
                    <label for="estimasi_kerusakan_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                        Estimasi Kerusakan Kontainer <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="estimasi_kerusakan_kontainer" name="estimasi_kerusakan_kontainer"
                           value="{{ old('estimasi_kerusakan_kontainer') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Estimasi kerusakan kontainer..."
                           required>
                    @error('estimasi_kerusakan_kontainer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Biaya Perbaikan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="estimasi_biaya_perbaikan" class="block text-sm font-medium text-gray-700 mb-2">
                            Estimasi Biaya Perbaikan
                        </label>
                        <input type="text" id="estimasi_biaya_perbaikan" name="estimasi_biaya_perbaikan"
                               value="{{ old('estimasi_biaya_perbaikan') ? number_format(old('estimasi_biaya_perbaikan'), 0, ',', '.') : '' }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="0">
                        <input type="hidden" id="estimasi_biaya_perbaikan_raw" name="estimasi_biaya_perbaikan_raw" value="{{ old('estimasi_biaya_perbaikan') }}">
                        <p class="mt-1 text-sm text-gray-500">Estimasi biaya yang diperlukan untuk perbaikan</p>
                        @error('estimasi_biaya_perbaikan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="realisasi_biaya_perbaikan" class="block text-sm font-medium text-gray-700 mb-2">
                            Realisasi Biaya Perbaikan
                        </label>
                        <input type="text" id="realisasi_biaya_perbaikan" name="realisasi_biaya_perbaikan"
                               value="{{ old('realisasi_biaya_perbaikan') ? number_format(old('realisasi_biaya_perbaikan'), 0, ',', '.') : '' }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="0">
                        <input type="hidden" id="realisasi_biaya_perbaikan_raw" name="realisasi_biaya_perbaikan_raw" value="{{ old('realisasi_biaya_perbaikan') }}">
                        <p class="mt-1 text-sm text-gray-500">Biaya aktual yang dikeluarkan untuk perbaikan</p>
                        @error('realisasi_biaya_perbaikan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Tanggal -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tanggal_perbaikan" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Perbaikan <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="tanggal_perbaikan" name="tanggal_perbaikan" value="{{ old('tanggal_perbaikan') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        @error('tanggal_perbaikan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Selesai
                        </label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('tanggal_selesai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('perbaikan-kontainer.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Batal
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Function to format number with dots as thousand separator
function formatNumber(num) {
    if (!num) return '';
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Function to remove dots and get raw number
function unformatNumber(str) {
    if (!str) return '';
    return str.toString().replace(/\./g, '');
}

// Function to handle input formatting
function handleNumberInput(inputId, hiddenId) {
    const input = document.getElementById(inputId);
    const hidden = document.getElementById(hiddenId);

    input.addEventListener('input', function(e) {
        let value = e.target.value;

        // Remove any non-numeric characters except dots
        value = value.replace(/[^\d.]/g, '');

        // Remove existing dots to get clean number
        let cleanValue = unformatNumber(value);

        // Format the number with dots
        let formattedValue = formatNumber(cleanValue);

        // Update the display input
        e.target.value = formattedValue;

        // Update the hidden input with raw value
        hidden.value = cleanValue;
    });

    input.addEventListener('blur', function(e) {
        let value = e.target.value;
        let cleanValue = unformatNumber(value);
        let formattedValue = formatNumber(cleanValue);

        e.target.value = formattedValue;
        hidden.value = cleanValue;
    });

    // Initialize on page load
    if (input.value) {
        let cleanValue = unformatNumber(input.value);
        input.value = formatNumber(cleanValue);
        hidden.value = cleanValue;
    }
}

// Initialize formatting for both inputs
document.addEventListener('DOMContentLoaded', function() {
    handleNumberInput('estimasi_biaya_perbaikan', 'estimasi_biaya_perbaikan_raw');
    handleNumberInput('realisasi_biaya_perbaikan', 'realisasi_biaya_perbaikan_raw');
});
</script>
@endsection
