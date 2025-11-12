@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-500 mb-2">
            <a href="{{ route('kelola-bbm.index') }}" class="hover:text-indigo-600">
                <i class="fas fa-gas-pump mr-1"></i>
                Kelola BBM
            </a>
            <i class="fas fa-chevron-right mx-2 text-xs"></i>
            <span class="text-gray-900">Tambah Data BBM</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-plus-circle mr-2 text-indigo-600"></i>
            Tambah Data BBM
        </h1>
        <p class="text-gray-600 mt-1">Isi formulir di bawah untuk menambahkan data BBM baru</p>
    </div>

    <!-- Form -->
    <form action="{{ route('kelola-bbm.store') }}" method="POST" class="bg-white rounded-lg shadow-sm p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Bulan -->
            <div>
                <label for="bulan" class="block text-sm font-medium text-gray-700 mb-2">
                    Bulan <span class="text-red-500">*</span>
                </label>
                <select name="bulan" 
                        id="bulan" 
                        required
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('bulan') border-red-300 @enderror">
                    <option value="">Pilih Bulan</option>
                    <option value="1" {{ old('bulan') == 1 ? 'selected' : '' }}>Januari</option>
                    <option value="2" {{ old('bulan') == 2 ? 'selected' : '' }}>Februari</option>
                    <option value="3" {{ old('bulan') == 3 ? 'selected' : '' }}>Maret</option>
                    <option value="4" {{ old('bulan') == 4 ? 'selected' : '' }}>April</option>
                    <option value="5" {{ old('bulan') == 5 ? 'selected' : '' }}>Mei</option>
                    <option value="6" {{ old('bulan') == 6 ? 'selected' : '' }}>Juni</option>
                    <option value="7" {{ old('bulan') == 7 ? 'selected' : '' }}>Juli</option>
                    <option value="8" {{ old('bulan') == 8 ? 'selected' : '' }}>Agustus</option>
                    <option value="9" {{ old('bulan') == 9 ? 'selected' : '' }}>September</option>
                    <option value="10" {{ old('bulan') == 10 ? 'selected' : '' }}>Oktober</option>
                    <option value="11" {{ old('bulan') == 11 ? 'selected' : '' }}>November</option>
                    <option value="12" {{ old('bulan') == 12 ? 'selected' : '' }}>Desember</option>
                </select>
                @error('bulan')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tahun -->
            <div>
                <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">
                    Tahun <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="tahun" 
                       id="tahun" 
                       value="{{ old('tahun', date('Y')) }}"
                       min="2000"
                       max="2100"
                       required
                       placeholder="2025"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('tahun') border-red-300 @enderror">
                @error('tahun')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- BBM Per Liter -->
            <div>
                <label for="bbm_per_liter" class="block text-sm font-medium text-gray-700 mb-2">
                    BBM Per Liter (Rp) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">Rp</span>
                    </div>
                    <input type="number" 
                           name="bbm_per_liter" 
                           id="bbm_per_liter" 
                           value="{{ old('bbm_per_liter') }}"
                           step="0.01"
                           min="0"
                           required
                           placeholder="14000"
                           onchange="calculatePercentage()"
                           oninput="calculatePercentage()"
                           class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('bbm_per_liter') border-red-300 @enderror">
                </div>
                @error('bbm_per_liter')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Harga BBM dasar: Rp 13.800</p>
            </div>

            <!-- Persentase (Auto-calculated) -->
            <div>
                <label for="persentase" class="block text-sm font-medium text-gray-700 mb-2">
                    Persentase (%) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" 
                           name="persentase" 
                           id="persentase" 
                           value="{{ old('persentase') }}"
                           step="0.01"
                           readonly
                           placeholder="0.00"
                           class="block w-full pr-12 pl-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 cursor-not-allowed @error('persentase') border-red-300 @enderror">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">%</span>
                    </div>
                </div>
                @error('persentase')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p id="persentase-info" class="mt-1 text-xs text-gray-500">Dihitung otomatis berdasarkan harga BBM</p>
            </div>

            <!-- Keterangan -->
            <div class="md:col-span-2">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan <span class="text-gray-500">(Opsional)</span>
                </label>
                <textarea name="keterangan" 
                          id="keterangan" 
                          rows="4"
                          placeholder="Tambahkan catatan atau keterangan mengenai data BBM ini..."
                          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('keterangan') border-red-300 @enderror">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-calculator text-blue-400"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-blue-800">Cara Perhitungan Persentase:</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Harga BBM Dasar Perusahaan:</strong> Rp 13.800</li>
                            <li><strong>Persentase</strong> akan dihitung otomatis berdasarkan selisih dari harga dasar</li>
                            <li><strong>Contoh:</strong> Jika input Rp 14.000 → Naik 1.45% (naik Rp 200)</li>
                            <li><strong>Contoh:</strong> Jika input Rp 13.500 → Turun 2.17% (turun Rp 300)</li>
                            <li>Persentase <span class="text-red-600">positif (+)</span> = harga naik, <span class="text-green-600">negatif (-)</span> = harga turun</li>
                            <li>Keterangan bersifat opsional untuk catatan tambahan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warning Box - Dampak ke Pricelist -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-yellow-800">Dampak Terhadap Pricelist Uang Jalan Batam:</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Persentase &lt; 5%:</strong> Tarif pricelist akan <span class="font-semibold text-green-600">DIKEMBALIKAN KE NILAI AWAL</span></li>
                            <li><strong>Persentase = 5%:</strong> Tarif pricelist <span class="font-semibold">TIDAK BERUBAH</span></li>
                            <li><strong>Persentase &gt; 5%:</strong> Tarif pricelist akan <span class="font-semibold text-red-600">OTOMATIS DIUPDATE</span></li>
                            <li><strong>Rumus Kenaikan Tarif:</strong> (Persentase BBM - 5%)</li>
                            <li><strong>Contoh 1:</strong> BBM naik 3% → Tarif kembali ke nilai awal</li>
                            <li><strong>Contoh 2:</strong> BBM naik 7% → Tarif pricelist naik 2% (7% - 5%)</li>
                            <li><strong>Contoh 3:</strong> BBM naik 10% → Tarif pricelist naik 5% (10% - 5%)</li>
                            <li>Semua perubahan tarif akan dicatat dalam history</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('kelola-bbm.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <i class="fas fa-save mr-2"></i>
                Simpan Data BBM
            </button>
        </div>
    </form>
</div>

<script>
// Harga BBM dasar perusahaan
const HARGA_BBM_DASAR = 13800;

function calculatePercentage() {
    const bbmPerLiter = parseFloat(document.getElementById('bbm_per_liter').value) || 0;
    const persentaseInput = document.getElementById('persentase');
    const persentaseInfo = document.getElementById('persentase-info');
    
    if (bbmPerLiter > 0) {
        // Hitung persentase perubahan: ((Harga Baru - Harga Dasar) / Harga Dasar) * 100
        const persentase = ((bbmPerLiter - HARGA_BBM_DASAR) / HARGA_BBM_DASAR) * 100;
        
        // Set nilai persentase (bisa positif atau negatif)
        persentaseInput.value = persentase.toFixed(2);
        
        // Update info text dengan warna sesuai kondisi
        if (persentase > 0) {
            persentaseInfo.innerHTML = `<span class="text-red-600 font-medium"><i class="fas fa-arrow-up"></i> Naik ${persentase.toFixed(2)}% dari harga dasar (Rp ${HARGA_BBM_DASAR.toLocaleString('id-ID')})</span>`;
        } else if (persentase < 0) {
            persentaseInfo.innerHTML = `<span class="text-green-600 font-medium"><i class="fas fa-arrow-down"></i> Turun ${Math.abs(persentase).toFixed(2)}% dari harga dasar (Rp ${HARGA_BBM_DASAR.toLocaleString('id-ID')})</span>`;
        } else {
            persentaseInfo.innerHTML = `<span class="text-blue-600 font-medium"><i class="fas fa-equals"></i> Sama dengan harga dasar (Rp ${HARGA_BBM_DASAR.toLocaleString('id-ID')})</span>`;
        }
        
        // Tampilkan selisih harga
        const selisih = bbmPerLiter - HARGA_BBM_DASAR;
        const selisihFormatted = Math.abs(selisih).toLocaleString('id-ID');
        
        if (selisih !== 0) {
            const selisihText = selisih > 0 
                ? `<span class="text-red-600">+Rp ${selisihFormatted}</span>` 
                : `<span class="text-green-600">-Rp ${selisihFormatted}</span>`;
            persentaseInfo.innerHTML += `<br>${selisihText}`;
        }
    } else {
        persentaseInput.value = '';
        persentaseInfo.innerHTML = 'Dihitung otomatis berdasarkan harga BBM';
    }
}

// Hitung persentase saat halaman dimuat jika ada nilai lama
document.addEventListener('DOMContentLoaded', function() {
    calculatePercentage();
});
</script>
@endsection
