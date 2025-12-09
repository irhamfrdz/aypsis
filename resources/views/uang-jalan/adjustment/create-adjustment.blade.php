@extends('layouts.app')

@section('content')
<div class="container mx-auto px-3 py-2">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 p-3 border-b border-gray-200">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Tambah Pengurangan/Penambahan Uang Jalan</h1>
                <p class="text-xs text-gray-600 mt-0.5">Tambahkan pengurangan atau penambahan pada uang jalan yang sudah ada</p>
            </div>
            <a href="{{ route('uang-jalan.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-2.5 py-1.5 rounded text-xs whitespace-nowrap flex items-center">
                <svg class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Uang Jalan
            </a>
        </div>

        <!-- Uang Jalan Info -->
        <div class="bg-blue-50 border border-blue-200 rounded mx-3 mt-2 p-3">
            <div class="flex items-start">
                <svg class="h-4 w-4 text-blue-400 mt-0.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="text-xs font-medium text-blue-800 mb-2">Uang Jalan Terpilih</h4>
                    <div class="grid grid-cols-3 md:grid-cols-5 gap-3 text-xs text-blue-700">
                        <div>
                            <span class="font-medium">No. UJ:</span>
                            <div>{{ $uangJalan->nomor_uang_jalan ?? '-' }}</div>
                        </div>
                        <div>
                            <span class="font-medium">Tanggal:</span>
                            <div>{{ $uangJalan->tanggal_uang_jalan ? $uangJalan->tanggal_uang_jalan->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div>
                            <span class="font-medium">Supir:</span>
                            <div>{{ $uangJalan->supir ?? '-' }}</div>
                        </div>
                        <div>
                            <span class="font-medium">Kenek:</span>
                            <div>{{ $uangJalan->kenek ?? '-' }}</div>
                        </div>
                        <div>
                            <span class="font-medium">No. Plat:</span>
                            <div>{{ $uangJalan->no_plat ?? '-' }}</div>
                        </div>
                        <div>
                            <span class="font-medium">Total Saat Ini:</span>
                            <div class="text-sm font-semibold">Rp {{ number_format($uangJalan->jumlah_total ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('uang-jalan.adjustment.store') }}" method="POST" class="p-3">
            @csrf
            <input type="hidden" name="uang_jalan_id" value="{{ $uangJalan->id }}">

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-3 py-2 rounded text-xs mb-3">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-3 py-2 rounded text-xs mb-3">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-3 py-2 rounded text-xs mb-3">
                    <div class="font-medium">Terdapat kesalahan pada form:</div>
                    <ul class="mt-1 list-disc list-inside text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2">Informasi Penyesuaian</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Tanggal Penyesuaian -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Penyesuaian <span class="text-red-600">*</span></label>
                        <input type="date"
                               name="tanggal_penyesuaian"
                               value="{{ old('tanggal_penyesuaian', date('Y-m-d')) }}"
                               required
                               class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_penyesuaian') border-red-500 @enderror">
                        @error('tanggal_penyesuaian')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Penyesuaian -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Jenis Penyesuaian <span class="text-red-600">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" 
                                       name="jenis_penyesuaian" 
                                       value="penambahan" 
                                       {{ old('jenis_penyesuaian', 'penambahan') == 'penambahan' ? 'checked' : '' }}
                                       required
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 focus:ring-2 border-gray-300 border-2">
                                <span class="ml-2 text-sm text-gray-700 font-medium">Penambahan</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" 
                                       name="jenis_penyesuaian" 
                                       value="pengurangan" 
                                       {{ old('jenis_penyesuaian') == 'pengurangan' ? 'checked' : '' }}
                                       required
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 focus:ring-2 border-gray-300 border-2">
                                <span class="ml-2 text-sm text-gray-700 font-medium">Pengurangan</span>
                            </label>
                        </div>
                        @error('jenis_penyesuaian')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Penyesuaian -->
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2">Detail Penyesuaian</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Alasan Penyesuaian -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Alasan Penyesuaian <span class="text-red-600">*</span></label>
                        <input type="text" 
                               name="alasan_penyesuaian"
                               value="{{ old('alasan_penyesuaian') }}"
                               placeholder="Masukkan alasan penyesuaian"
                               required
                               class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('alasan_penyesuaian') border-red-500 @enderror">
                        @error('alasan_penyesuaian')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah Penyesuaian -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Penyesuaian <span class="text-red-600">*</span></label>
                        <div class="relative">
                            <span class="absolute left-2 top-2 text-xs text-gray-500">Rp</span>
                            <input type="number" 
                                   name="jumlah_penyesuaian"
                                   id="jumlah_penyesuaian"
                                   value="{{ old('jumlah_penyesuaian', 0) }}"
                                   step="1"
                                   required
                                   oninput="calculateTotal()"
                                   class="w-full pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jumlah_penyesuaian') border-red-500 @enderror">
                            @error('jumlah_penyesuaian')
                                <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <p class="mt-0.5 text-xs text-gray-500">Masukkan nilai positif untuk penambahan, negatif untuk pengurangan</p>
                    </div>
                </div>
            </div>

            <!-- Total & Memo -->
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2">Total & Memo</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Total Baru -->
                    <div>
                        <div class="bg-gray-50 p-3 rounded">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Jumlah Total Baru</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-600 font-medium">Rp</span>
                                <input type="number" 
                                       name="jumlah_total_baru"
                                       id="jumlah_total_baru"
                                       readonly
                                       value="{{ old('jumlah_total_baru', $uangJalan->jumlah_total ?? 0) }}"
                                       class="w-full pl-8 pr-3 py-2.5 border-2 border-gray-300 rounded bg-white text-base font-semibold text-gray-800 focus:outline-none">
                            </div>
                            <p class="mt-1 text-xs text-gray-600">Total saat ini: Rp {{ number_format($uangJalan->jumlah_total ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Memo -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Memo</label>
                        <textarea name="memo" 
                                  rows="5" 
                                  placeholder="Masukkan catatan atau memo tambahan"
                                  class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('memo') border-red-500 @enderror">{{ old('memo') }}</textarea>
                        @error('memo')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Summary Box -->
            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded p-3">
                <div class="flex items-start">
                    <svg class="h-4 w-4 text-yellow-400 mt-0.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-xs font-medium text-yellow-800 mb-1">Ringkasan Penyesuaian</h4>
                        <div class="text-xs text-yellow-700">
                            <div class="grid grid-cols-2 gap-3 mb-2">
                                <div>
                                    <span class="font-medium">Jenis:</span> <span id="jenis-summary">{{ old('jenis_penyesuaian', 'penambahan') == 'penambahan' ? 'Penambahan' : 'Pengurangan' }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Jumlah:</span> Rp <span id="jumlah-summary">{{ number_format(abs(old('jumlah_penyesuaian', 0)), 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-yellow-300">
                                <p class="text-xs"><strong>Pastikan data sudah benar sebelum menyimpan.</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-2 mt-4 pt-3 border-t border-gray-200">
                <a href="{{ route('uang-jalan.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-1.5 rounded text-sm">
                    Batal
                </a>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded text-sm">
                    Simpan Penyesuaian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function calculateTotal() {
    const currentTotal = {{ $uangJalan->jumlah_total ?? 0 }};
    const adjustment = parseFloat(document.getElementById('jumlah_penyesuaian').value) || 0;
    const newTotal = currentTotal + adjustment;
    
    document.getElementById('jumlah_total_baru').value = newTotal;
    
    // Update summary
    const jenis = document.querySelector('input[name="jenis_penyesuaian"]:checked').value;
    document.getElementById('jenis-summary').textContent = jenis === 'penambahan' ? 'Penambahan' : 'Pengurangan';
    document.getElementById('jumlah-summary').textContent = Math.abs(adjustment).toLocaleString('id-ID');
}

// Calculate total on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
    
    // Update on radio change
    document.querySelectorAll('input[name="jenis_penyesuaian"]').forEach(radio => {
        radio.addEventListener('change', calculateTotal);
    });
});
</script>
@endsection