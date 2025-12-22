@extends('layouts.app')

@section('title', 'Edit Pricelist Rit')
@section('page_title', 'Edit Pricelist Rit')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Pricelist Rit</h2>
        <a href="{{ route('master.pricelist-rit.index') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
            ← Kembali
        </a>
    </div>

    <form action="{{ route('master.pricelist-rit.update', $pricelistRit) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Tujuan -->
            <div>
                <label for="tujuan" class="block text-sm font-medium text-gray-700 mb-2">
                    Tujuan <span class="text-red-500">*</span>
                </label>
                <select id="tujuan" name="tujuan"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tujuan') border-red-500 @enderror"
                        required>
                    <option value="">-- Pilih Tujuan --</option>
                    <option value="Supir" {{ old('tujuan', $pricelistRit->tujuan) == 'Supir' ? 'selected' : '' }}>Supir</option>
                    <option value="Kenek" {{ old('tujuan', $pricelistRit->tujuan) == 'Kenek' ? 'selected' : '' }}>Kenek</option>
                </select>
                @error('tujuan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tarif -->
            <div>
                <label for="tarif" class="block text-sm font-medium text-gray-700 mb-2">
                    Tarif (Rp) <span class="text-red-500">*</span>
                </label>
                <input type="text" id="tarif" name="tarif"
                       value="{{ old('tarif', $pricelistRit->tarif ? 'Rp ' . number_format($pricelistRit->tarif, 0, ',', '.') : '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent rupiah-input @error('tarif') border-red-500 @enderror"
                       placeholder="0"
                       required>
                <input type="hidden" id="tarif_raw" name="tarif_raw" value="{{ old('tarif_raw', $pricelistRit->tarif) }}">
                @error('tarif')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select id="status" name="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror"
                        required>
                    <option value="Aktif" {{ old('status', $pricelistRit->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Tidak Aktif" {{ old('status', $pricelistRit->status) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keterangan -->
            <div class="md:col-span-2">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan
                </label>
                <textarea id="keterangan" name="keterangan" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('keterangan') border-red-500 @enderror"
                          placeholder="Masukkan keterangan (opsional)">{{ old('keterangan', $pricelistRit->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4 pt-6 border-t">
            <a href="{{ route('master.pricelist-rit.index') }}"
               class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 focus:ring-2 focus:ring-gray-500 transition duration-200">
                Batal
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition duration-200">
                Update Pricelist Rit
            </button>
        </div>
    </form>
</div>

<script>
// Rupiah input formatting
function formatRupiah(angka, prefix = 'Rp ') {
    if (!angka) return '';
    let number_string = angka.toString().replace(/[^,\d]/g, ''),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix + rupiah;
}

function rupiahToNumber(rupiah) {
    return parseFloat(rupiah.replace(/[^\d]/g, '')) || 0;
}

// Handle rupiah input
document.addEventListener('DOMContentLoaded', function() {
    const rupiahInputs = document.querySelectorAll('.rupiah-input');

    rupiahInputs.forEach(function(input) {
        input.addEventListener('input', function(e) {
            let value = this.value;
            let number = rupiahToNumber(value);
            if (number > 0) {
                this.value = formatRupiah(number);
            } else {
                this.value = '';
            }
            // Update hidden field with raw number
            const hiddenField = this.parentNode.querySelector('input[type="hidden"]');
            if (hiddenField) {
                hiddenField.value = number > 0 ? number : '';
            }
        });

        input.addEventListener('focus', function(e) {
            if (this.value === 'Rp 0' || this.value === '') {
                this.value = '';
            }
        });

        input.addEventListener('blur', function(e) {
            if (this.value === '' || rupiahToNumber(this.value) === 0) {
                this.value = '';
            }
        });
    });
});
</script>
@endsection
