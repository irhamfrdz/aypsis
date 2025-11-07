@extends('layouts.app')

@section('content')
<div class="container mx-auto px-3 py-2">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Edit Pranota Uang Jalan</h1>
                <p class="text-xs text-gray-600 mt-0.5">{{ $pranotaUangJalan->nomor_pranota }}</p>
            </div>
            <a href="{{ route('pranota-uang-jalan.show', $pranotaUangJalan) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded text-sm whitespace-nowrap flex items-center">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-3 py-2 rounded text-sm mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-3 py-2 rounded text-sm mb-4">
                <div class="font-medium">Terdapat kesalahan pada form:</div>
                <ul class="mt-1 list-disc list-inside text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Alert Info -->
        <div class="bg-yellow-50 border border-yellow-200 rounded p-3 mb-4">
            <div class="flex items-start">
                <svg class="h-4 w-4 text-yellow-400 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="text-xs font-medium text-yellow-800">Informasi</h4>
                    <p class="text-xs text-yellow-700 mt-1">
                        Anda hanya dapat mengubah informasi umum pranota. Untuk mengubah daftar uang jalan, silakan hapus pranota ini dan buat ulang.
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('pranota-uang-jalan.update', $pranotaUangJalan) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Form Information -->
            <div class="bg-white rounded border border-gray-200 p-4 mb-4">
                <h3 class="text-sm font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2">Edit Informasi Pranota</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Pranota</label>
                        <input type="text" 
                               value="{{ $pranotaUangJalan->nomor_pranota }}" 
                               readonly
                               class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded bg-gray-50 text-gray-700 focus:outline-none">
                        <p class="mt-0.5 text-xs text-gray-500">Nomor pranota tidak dapat diubah</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Pranota <span class="text-red-600">*</span></label>
                        <input type="date" 
                               name="tanggal_pranota" 
                               value="{{ old('tanggal_pranota', $pranotaUangJalan->tanggal_pranota->format('Y-m-d')) }}" 
                               required
                               class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_pranota') border-red-500 @enderror">
                        @error('tanggal_pranota')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Periode Tagihan <span class="text-red-600">*</span></label>
                        <input type="text" 
                               name="periode_tagihan" 
                               value="{{ old('periode_tagihan', $pranotaUangJalan->periode_tagihan) }}" 
                               placeholder="YYYY-MM (contoh: 2025-11)"
                               required
                               class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('periode_tagihan') border-red-500 @enderror">
                        @error('periode_tagihan')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-0.5 text-xs text-gray-500">Format: YYYY-MM</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <input type="text" 
                               value="{{ $pranotaUangJalan->status_text }}" 
                               readonly
                               class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded bg-gray-50 text-gray-700 focus:outline-none">
                        <p class="mt-0.5 text-xs text-gray-500">Status tidak dapat diubah</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="catatan" 
                                  rows="3" 
                                  placeholder="Catatan tambahan (opsional)"
                                  class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('catatan') border-red-500 @enderror">{{ old('catatan', $pranotaUangJalan->catatan) }}</textarea>
                        @error('catatan')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Current Uang Jalan List (Read-only) -->
            <div class="bg-white rounded border border-gray-200 p-4 mb-4">
                <h3 class="text-sm font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2">Daftar Uang Jalan (Tidak dapat diubah)</h3>
                
                @if($pranotaUangJalan->uangJalans->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Uang Jalan</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Surat Jalan</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pranotaUangJalan->uangJalans as $index => $uangJalan)
                                    <tr class="bg-gray-50">
                                        <td class="px-3 py-2 text-sm text-gray-900">{{ $index + 1 }}</td>
                                        <td class="px-3 py-2">
                                            <div class="text-sm font-medium text-gray-900">{{ $uangJalan->nomor_uang_jalan }}</div>
                                            <div class="text-xs text-gray-500">{{ $uangJalan->kegiatan_bongkar_muat }}</div>
                                        </td>
                                        <td class="px-3 py-2 text-sm text-gray-900">
                                            {{ $uangJalan->tanggal_pemberian ? $uangJalan->tanggal_pemberian->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-3 py-2">
                                            @if($uangJalan->suratJalan)
                                                <div class="text-sm text-gray-900">{{ $uangJalan->suratJalan->no_surat_jalan }}</div>
                                                <div class="text-xs text-gray-500">{{ $uangJalan->suratJalan->kegiatan }}</div>
                                            @else
                                                <div class="text-sm text-gray-500">-</div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <div class="text-sm font-semibold text-gray-900">
                                                Rp {{ number_format($uangJalan->jumlah_total, 0, ',', '.') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100">
                                <tr>
                                    <td colspan="4" class="px-3 py-3 text-right text-sm font-semibold text-gray-900">
                                        Total:
                                    </td>
                                    <td class="px-3 py-3 text-right text-lg font-bold text-gray-900">
                                        {{ $pranotaUangJalan->formatted_total }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Summary Info -->
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded">
                        <div class="flex items-start">
                            <svg class="h-4 w-4 text-blue-400 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <h4 class="text-xs font-medium text-blue-800">Informasi</h4>
                                <div class="text-xs text-blue-700 mt-1">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <div><strong>Total Item:</strong> {{ $pranotaUangJalan->jumlah_uang_jalan }} uang jalan</div>
                                        <div><strong>Total Amount:</strong> {{ $pranotaUangJalan->formatted_total }}</div>
                                    </div>
                                    <p class="mt-2">Untuk mengubah daftar uang jalan, hapus pranota ini dan buat ulang dengan memilih uang jalan yang diinginkan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-2">
                <a href="{{ route('pranota-uang-jalan.show', $pranotaUangJalan) }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded text-sm">
                    Batal
                </a>
                <button type="submit" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" id="success-alert">
        {{ session('success') }}
    </div>
@endif

<script>
// Auto hide alerts after 3 seconds
setTimeout(function() {
    const successAlert = document.getElementById('success-alert');
    if (successAlert) successAlert.remove();
}, 3000);
</script>
@endsection