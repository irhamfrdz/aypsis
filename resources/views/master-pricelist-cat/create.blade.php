@extends('layouts.app')

@section('title', 'Tambah Pricelist CAT')
@section('page_title', 'Tambah Pricelist CAT')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Tambah Pricelist CAT</h2>
        <a href="{{ route('master.pricelist-cat.index') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
            ← Kembali
        </a>
    </div>

    <form action="{{ route('master.pricelist-cat.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Vendor -->
            <div>
                <label for="vendor" class="block text-sm font-medium text-gray-700 mb-2">
                    Vendor/Bengkel <span class="text-red-500">*</span>
                </label>
                <input type="text" id="vendor" name="vendor"
                       value="{{ old('vendor') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('vendor') border-red-500 @enderror"
                       placeholder="Masukkan nama vendor/bengkel"
                       required>
                @error('vendor')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jenis CAT -->
            <div>
                <label for="jenis_cat" class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis CAT <span class="text-red-500">*</span>
                </label>
                <select id="jenis_cat" name="jenis_cat"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jenis_cat') border-red-500 @enderror"
                        required>
                    <option value="">-- Pilih Jenis CAT --</option>
                    <option value="cat_sebagian" {{ old('jenis_cat') == 'cat_sebagian' ? 'selected' : '' }}>Cat Sebagian</option>
                    <option value="cat_full" {{ old('jenis_cat') == 'cat_full' ? 'selected' : '' }}>Cat Full</option>
                </select>
                @error('jenis_cat')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ukuran Kontainer -->
            <div>
                <label for="ukuran_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                    Ukuran Kontainer <span class="text-red-500">*</span>
                </label>
                <select id="ukuran_kontainer" name="ukuran_kontainer"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ukuran_kontainer') border-red-500 @enderror"
                        required>
                    <option value="">-- Pilih Ukuran --</option>
                    <option value="20ft" {{ old('ukuran_kontainer') == '20ft' ? 'selected' : '' }}>20ft</option>
                    <option value="40ft" {{ old('ukuran_kontainer') == '40ft' ? 'selected' : '' }}>40ft</option>
                    <option value="40ft HC" {{ old('ukuran_kontainer') == '40ft HC' ? 'selected' : '' }}>40ft HC</option>
                </select>
                @error('ukuran_kontainer')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tarif -->
            <div>
                <label for="tarif" class="block text-sm font-medium text-gray-700 mb-2">
                    Tarif (Rp)
                </label>
                <input type="text" id="tarif" name="tarif"
                       value="{{ old('tarif') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent rupiah-input @error('tarif') border-red-500 @enderror"
                       placeholder="0">
                <input type="hidden" id="tarif_raw" name="tarif_raw" value="{{ old('tarif_raw') }}">
                @error('tarif')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4 pt-6 border-t">
            <a href="{{ route('master.pricelist-cat.index') }}"
               class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 focus:ring-2 focus:ring-gray-500 transition duration-200">
                Batal
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition duration-200">
                Simpan Pricelist CAT
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
                this.value = 'Rp 0';
                const hiddenField = this.parentNode.querySelector('input[type="hidden"]');
                if (hiddenField) {
                    hiddenField.value = '';
                }
            } else {
                const number = rupiahToNumber(this.value);
                const hiddenField = this.parentNode.querySelector('input[type="hidden"]');
                if (hiddenField) {
                    hiddenField.value = number;
                }
            }
        });
    });
});
</script>
@endsection
