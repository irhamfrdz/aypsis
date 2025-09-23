@extends('layouts.app')

@section('title', 'Edit Tagihan CAT')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Edit Tagihan CAT</h1>
                    <p class="text-gray-600 mt-1">Perbarui data tagihan Container</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('tagihan-cat.show', $tagihanCat) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Lihat Detail
                    </a>
                    <a href="{{ route('tagihan-cat.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('tagihan-cat.update', $tagihanCat) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Row 1: Nomor Tagihan CAT & Nomor Kontainer -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nomor Tagihan CAT -->
                    <div>
                        <label for="nomor_tagihan_cat" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Tagihan CAT
                        </label>
                        <input type="text" id="nomor_tagihan_cat" name="nomor_tagihan_cat"
                               value="{{ old('nomor_tagihan_cat', $tagihanCat->nomor_tagihan_cat) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Masukkan nomor tagihan CAT...">
                        @error('nomor_tagihan_cat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor Kontainer -->
                    <div>
                        <label for="nomor_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Kontainer <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nomor_kontainer" name="nomor_kontainer"
                               value="{{ old('nomor_kontainer', $tagihanCat->nomor_kontainer) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Masukkan nomor kontainer..."
                               required>
                        @error('nomor_kontainer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Row 2: Vendor & Tanggal CAT -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Vendor -->
                    <div>
                        <label for="vendor" class="block text-sm font-medium text-gray-700 mb-2">
                            Vendor
                        </label>
                        <select id="vendor" name="vendor"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pilih Vendor...</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->vendor }}" {{ old('vendor', $tagihanCat->vendor) == $vendor->vendor ? 'selected' : '' }}>
                                    {{ $vendor->vendor }}
                                </option>
                            @endforeach
                        </select>
                        @error('vendor')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal CAT -->
                    <div>
                        <label for="tanggal_cat" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal CAT <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="tanggal_cat" name="tanggal_cat"
                               value="{{ old('tanggal_cat', $tagihanCat->tanggal_cat ? $tagihanCat->tanggal_cat->format('Y-m-d') : '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        @error('tanggal_cat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Row 3: Estimasi Biaya & Realisasi Biaya -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Estimasi Biaya -->
                    <div>
                        <label for="estimasi_biaya" class="block text-sm font-medium text-gray-700 mb-2">
                            Estimasi Biaya
                        </label>
                        <input type="text" id="estimasi_biaya" name="estimasi_biaya"
                               value="{{ old('estimasi_biaya', $tagihanCat->estimasi_biaya ? number_format($tagihanCat->estimasi_biaya, 0, ',', '.') : '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="0">
                        <input type="hidden" id="estimasi_biaya_raw" name="estimasi_biaya_raw" value="{{ old('estimasi_biaya_raw', $tagihanCat->estimasi_biaya) }}">
                        <p class="mt-1 text-sm text-gray-500">Estimasi biaya dalam Rupiah</p>
                        @error('estimasi_biaya_raw')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Realisasi Biaya -->
                    <div>
                        <label for="realisasi_biaya" class="block text-sm font-medium text-gray-700 mb-2">
                            Realisasi Biaya
                        </label>
                        <input type="text" id="realisasi_biaya" name="realisasi_biaya"
                               value="{{ old('realisasi_biaya', $tagihanCat->realisasi_biaya ? number_format($tagihanCat->realisasi_biaya, 0, ',', '.') : '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="0">
                        <input type="hidden" id="realisasi_biaya_raw" name="realisasi_biaya_raw" value="{{ old('realisasi_biaya_raw', $tagihanCat->realisasi_biaya) }}">
                        <p class="mt-1 text-sm text-gray-500">Realisasi biaya dalam Rupiah</p>
                        @error('realisasi_biaya_raw')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Row 4: Keterangan (Full Width) -->
                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan
                    </label>
                    <textarea id="keterangan" name="keterangan" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Tambahkan keterangan tambahan jika diperlukan...">{{ old('keterangan', $tagihanCat->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Row 5: Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="pending" {{ old('status', $tagihanCat->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ old('status', $tagihanCat->status) == 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                        <option value="cancelled" {{ old('status', $tagihanCat->status) == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('tagihan-cat.show', $tagihanCat) }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Batal
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Simple and robust number formatting
function formatNumber(num) {
    if (!num || num === '0') return '';
    // Convert to string and add dots as thousand separator
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function unformatNumber(str) {
    if (!str) return '';
    // Remove all dots
    return str.toString().replace(/\./g, '');
}

// Initialize number formatting for inputs
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing number formatting...');

    // Get input elements
    const estimasiBiayaInput = document.getElementById('estimasi_biaya');
    const estimasiBiayaHidden = document.getElementById('estimasi_biaya_raw');
    const realisasiBiayaInput = document.getElementById('realisasi_biaya');
    const realisasiBiayaHidden = document.getElementById('realisasi_biaya_raw');

    console.log('Elements found:', {
        estimasiBiayaInput: !!estimasiBiayaInput,
        estimasiBiayaHidden: !!estimasiBiayaHidden,
        realisasiBiayaInput: !!realisasiBiayaInput,
        realisasiBiayaHidden: !!realisasiBiayaHidden
    });

    if (estimasiBiayaInput && estimasiBiayaHidden) {
        setupNumberInput(estimasiBiayaInput, estimasiBiayaHidden, 'estimasi_biaya');
    }
    if (realisasiBiayaInput && realisasiBiayaHidden) {
        setupNumberInput(realisasiBiayaInput, realisasiBiayaHidden, 'realisasi_biaya');
    }
});

function setupNumberInput(input, hidden, name) {
    console.log('Setting up', name, 'input');

    // Handle input event
    input.addEventListener('input', function(e) {
        console.log(name + ' input event:', e.target.value);

        let value = e.target.value;

        // Remove non-numeric characters except dots
        value = value.replace(/[^\d.]/g, '');
        console.log(name + ' cleaned value:', value);

        // Remove existing dots
        let cleanValue = unformatNumber(value);
        console.log(name + ' clean value:', cleanValue);

        // Format with dots
        let formattedValue = formatNumber(cleanValue);
        console.log(name + ' formatted value:', formattedValue);

        // Update display (without triggering input event)
        input.value = formattedValue;

        // Update hidden field
        hidden.value = cleanValue;

        console.log(name + ' final values - display:', formattedValue, 'hidden:', cleanValue);
    });

    // Handle focus/blur for final formatting
    input.addEventListener('blur', function(e) {
        console.log(name + ' blur event');
        let value = e.target.value;
        let cleanValue = unformatNumber(value);
        let formattedValue = formatNumber(cleanValue);

        input.value = formattedValue;
        hidden.value = cleanValue;
    });

    // Initialize if there's a value
    if (input.value) {
        console.log(name + ' has initial value:', input.value);
        let cleanValue = unformatNumber(input.value);
        input.value = formatNumber(cleanValue);
        hidden.value = cleanValue;
    }
}
</script>
@endpush
