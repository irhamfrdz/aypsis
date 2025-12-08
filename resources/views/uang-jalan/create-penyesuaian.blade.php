@extends('layouts.app')

@section('content')
<div class="container mx-auto px-3 py-2">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 p-3 border-b border-gray-200">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Buat Penyesuaian Uang Jalan</h1>
                <p class="text-xs text-gray-600 mt-0.5">Buat penyesuaian pembayaran uang jalan berdasarkan surat jalan yang dipilih</p>
            </div>
            <a href="{{ route('uang-jalan.select-surat-jalan-penyesuaian') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-2.5 py-1.5 rounded text-xs whitespace-nowrap flex items-center">
                <svg class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Pilih Surat Jalan
            </a>
        </div>

        <!-- Surat Jalan Info -->
        <div class="bg-green-50 border border-green-200 rounded mx-3 mt-2 p-3">
            <div class="flex items-start">
                <svg class="h-4 w-4 text-green-400 mt-0.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="text-xs font-medium text-green-800 mb-2">Surat Jalan Terpilih</h4>
                    <div class="grid grid-cols-3 md:grid-cols-5 gap-3 text-xs text-green-700">
                        <div>
                            <span class="font-medium">No. SJ:</span>
                            <div>{{ $suratJalan->no_surat_jalan }}</div>
                        </div>
                        <div>
                            <span class="font-medium">Tanggal:</span>
                            <div>{{ $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div>
                            <span class="font-medium">Kegiatan:</span>
                            <div>{{ $suratJalan->kegiatan }}</div>
                        </div>
                        @if($suratJalan->order)
                        <div>
                            <span class="font-medium">Pengirim:</span>
                            <div class="truncate">{{ $suratJalan->order->pengirim->nama_pengirim ?? '-' }}</div>
                        </div>
                        <div>
                            <span class="font-medium">No. Order:</span>
                            <div>{{ $suratJalan->order->nomor_order }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Uang Jalan Info -->
        <div class="bg-yellow-50 border border-yellow-200 rounded mx-3 mt-2 p-3">
            <div class="flex items-start">
                <svg class="h-4 w-4 text-yellow-400 mt-0.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="text-xs font-medium text-yellow-800 mb-2">Uang Jalan Saat Ini</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs text-yellow-700">
                        <div>
                            <span class="font-medium">No. UJ:</span>
                            <div>{{ $suratJalan->uangJalan->nomor_uang_jalan ?? '-' }}</div>
                        </div>
                        <div>
                            <span class="font-medium">Tanggal UJ:</span>
                            <div>{{ $suratJalan->uangJalan->tanggal_uang_jalan ? $suratJalan->uangJalan->tanggal_uang_jalan->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div>
                            <span class="font-medium">Total UJ:</span>
                            <div class="font-semibold">Rp {{ number_format($suratJalan->uangJalan->jumlah_total ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <span class="font-medium">Status:</span>
                            <div>
                                <span class="inline-flex px-1.5 py-0.5 text-xs font-medium rounded
                                    @if($suratJalan->uangJalan->status === 'lunas') bg-green-100 text-green-800
                                    @elseif($suratJalan->uangJalan->status === 'sudah_masuk_pranota') bg-blue-100 text-blue-800
                                    @elseif($suratJalan->uangJalan->status === 'belum_masuk_pranota') bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $suratJalan->uangJalan->status ?? 'unknown')) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('uang-jalan.store-penyesuaian') }}" method="POST" class="p-3">
            @csrf
            <input type="hidden" name="surat_jalan_id" value="{{ $suratJalan->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Tanggal Penyesuaian -->
                <div>
                    <label for="tanggal_penyesuaian" class="block text-xs font-medium text-gray-700 mb-1">
                        Tanggal Penyesuaian <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           id="tanggal_penyesuaian"
                           name="tanggal_penyesuaian"
                           value="{{ old('tanggal_penyesuaian', date('Y-m-d')) }}"
                           class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('tanggal_penyesuaian') border-red-500 @enderror"
                           required>
                    @error('tanggal_penyesuaian')
                        <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Penyesuaian -->
                <div>
                    <label for="jenis_penyesuaian" class="block text-xs font-medium text-gray-700 mb-1">
                        Jenis Penyesuaian <span class="text-red-500">*</span>
                    </label>
                    <select id="jenis_penyesuaian"
                            name="jenis_penyesuaian"
                            class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('jenis_penyesuaian') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Jenis Penyesuaian</option>
                        <option value="penambahan" {{ old('jenis_penyesuaian') === 'penambahan' ? 'selected' : '' }}>Penambahan</option>
                        <option value="pengurangan" {{ old('jenis_penyesuaian') === 'pengurangan' ? 'selected' : '' }}>Pengurangan</option>
                        <option value="penggantian" {{ old('jenis_penyesuaian') === 'penggantian' ? 'selected' : '' }}>Penggantian Total</option>
                    </select>
                    @error('jenis_penyesuaian')
                        <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nominal Penyesuaian -->
                <div>
                    <label for="nominal_penyesuaian" class="block text-xs font-medium text-gray-700 mb-1">
                        Nominal Penyesuaian <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-2 top-1.5 text-gray-500 text-sm">Rp</span>
                        <input type="text"
                               id="nominal_penyesuaian"
                               name="nominal_penyesuaian"
                               value="{{ old('nominal_penyesuaian') }}"
                               class="w-full pl-8 pr-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('nominal_penyesuaian') border-red-500 @enderror"
                               placeholder="0"
                               oninput="formatNumber(this)"
                               required>
                    </div>
                    @error('nominal_penyesuaian')
                        <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Setelah Penyesuaian -->
                <div>
                    <label for="total_setelah_penyesuaian" class="block text-xs font-medium text-gray-700 mb-1">
                        Total Setelah Penyesuaian
                    </label>
                    <div class="relative">
                        <span class="absolute left-2 top-1.5 text-gray-500 text-sm">Rp</span>
                        <input type="text"
                               id="total_setelah_penyesuaian"
                               name="total_setelah_penyesuaian"
                               value="{{ old('total_setelah_penyesuaian') }}"
                               class="w-full pl-8 pr-2 py-1.5 text-sm border border-gray-300 rounded bg-gray-50"
                               readonly>
                    </div>
                </div>
            </div>

            <!-- Alasan Penyesuaian -->
            <div class="mb-4">
                <label for="alasan_penyesuaian" class="block text-xs font-medium text-gray-700 mb-1">
                    Alasan Penyesuaian <span class="text-red-500">*</span>
                </label>
                <textarea id="alasan_penyesuaian"
                          name="alasan_penyesuaian"
                          rows="3"
                          class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('alasan_penyesuaian') border-red-500 @enderror"
                          placeholder="Jelaskan alasan penyesuaian uang jalan...">{{ old('alasan_penyesuaian') }}</textarea>
                @error('alasan_penyesuaian')
                    <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                <a href="{{ route('uang-jalan.select-surat-jalan-penyesuaian') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded text-xs font-medium transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs font-medium transition-colors">
                    Simpan Penyesuaian
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function formatNumber(input) {
    let value = input.value.replace(/[^\d]/g, '');
    input.value = new Intl.NumberFormat('id-ID').format(value);
    calculateTotal();
}

function calculateTotal() {
    const jenisPenyesuaian = document.getElementById('jenis_penyesuaian').value;
    const nominalPenyesuaian = parseFloat(document.getElementById('nominal_penyesuaian').value.replace(/[^\d]/g, '')) || 0;
    const uangJalanSaatIni = {{ $suratJalan->uangJalan->jumlah_total ?? 0 }};

    let totalBaru = uangJalanSaatIni;

    if (jenisPenyesuaian === 'penambahan') {
        totalBaru = uangJalanSaatIni + nominalPenyesuaian;
    } else if (jenisPenyesuaian === 'pengurangan') {
        totalBaru = uangJalanSaatIni - nominalPenyesuaian;
    } else if (jenisPenyesuaian === 'penggantian') {
        totalBaru = nominalPenyesuaian;
    }

    document.getElementById('total_setelah_penyesuaian').value = new Intl.NumberFormat('id-ID').format(totalBaru);
}

// Initialize calculation on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();

    // Recalculate when jenis penyesuaian changes
    document.getElementById('jenis_penyesuaian').addEventListener('change', calculateTotal);
});
</script>
@endpush