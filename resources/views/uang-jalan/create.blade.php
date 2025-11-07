@extends('layouts.app')

@section('content')
<div class="container mx-auto px-3 py-2">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 p-3 border-b border-gray-200">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Buat Uang Jalan</h1>
                <p class="text-xs text-gray-600 mt-0.5">Buat pembayaran uang jalan berdasarkan surat jalan yang dipilih</p>
            </div>
            <a href="{{ route('uang-jalan.select-surat-jalan') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-2.5 py-1.5 rounded text-xs whitespace-nowrap flex items-center">
                <svg class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Pilih Surat Jalan
            </a>
        </div>

        <!-- Surat Jalan Info -->
        <div class="bg-blue-50 border border-blue-200 rounded mx-3 mt-2 p-3">
            <div class="flex items-start">
                <svg class="h-4 w-4 text-blue-400 mt-0.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="text-xs font-medium text-blue-800 mb-2">Surat Jalan Terpilih</h4>
                    <div class="grid grid-cols-3 md:grid-cols-5 gap-3 text-xs text-blue-700">
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
                        <div>
                            <span class="font-medium">Supir:</span>
                            <div>{{ $suratJalan->supir ?? '-' }}</div>
                        </div>
                        <div>
                            <span class="font-medium">Kenek:</span>
                            <div>{{ $suratJalan->kenek ?? '-' }}</div>
                        </div>
                        <div>
                            <span class="font-medium">No. Plat:</span>
                            <div>{{ $suratJalan->no_plat ?? '-' }}</div>
                        </div>
                        <div>
                            <span class="font-medium">Uang Jalan:</span>
                            <div class="text-sm font-semibold">Rp {{ number_format($suratJalan->uang_jalan ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('uang-jalan.store') }}" method="POST" class="p-3">
            @csrf
            <input type="hidden" name="surat_jalan_id" value="{{ $suratJalan->id }}">

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
                <h3 class="text-sm font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2">Informasi Pembayaran</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Nomor Uang Jalan -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Uang Jalan <span class="text-red-600">*</span></label>
                        <input type="text"
                               name="nomor_uang_jalan"
                               value="{{ old('nomor_uang_jalan', $nomorUangJalan) }}"
                               readonly
                               class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded bg-gray-50 text-gray-700 focus:outline-none @error('nomor_uang_jalan') border-red-500 @enderror">
                        @error('nomor_uang_jalan')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-0.5 text-xs text-gray-500">Format: UJ + BulanTahun + Running Number (tidak reset per bulan)</p>
                    </div>

                    <!-- Tanggal Uang Jalan -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Uang Jalan <span class="text-red-600">*</span></label>
                        <input type="date"
                               name="tanggal_uang_jalan"
                               value="{{ old('tanggal_uang_jalan', date('Y-m-d')) }}"
                               required
                               class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_uang_jalan') border-red-500 @enderror">
                        @error('tanggal_uang_jalan')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Pilihan Bongkar/Muat -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kegiatan <span class="text-red-600">*</span></label>
                        <div class="flex gap-3">
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="kegiatan_bongkar_muat" 
                                       value="bongkar" 
                                       {{ old('kegiatan_bongkar_muat') == 'bongkar' ? 'checked' : '' }}
                                       required
                                       class="h-3.5 w-3.5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <span class="ml-1.5 text-xs text-gray-700">Bongkar</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="kegiatan_bongkar_muat" 
                                       value="muat" 
                                       {{ old('kegiatan_bongkar_muat') == 'muat' ? 'checked' : '' }}
                                       required
                                       class="h-3.5 w-3.5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <span class="ml-1.5 text-xs text-gray-700">Muat</span>
                            </label>
                        </div>
                        @error('kegiatan_bongkar_muat')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kategori Uang Jalan -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kategori <span class="text-red-600">*</span></label>
                        <div class="flex gap-3">
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="kategori_uang_jalan" 
                                       value="uang_jalan" 
                                       {{ old('kategori_uang_jalan') == 'uang_jalan' ? 'checked' : '' }}
                                       required
                                       class="h-3.5 w-3.5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <span class="ml-1.5 text-xs text-gray-700">Uang Jalan</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="kategori_uang_jalan" 
                                       value="non_uang_jalan" 
                                       {{ old('kategori_uang_jalan') == 'non_uang_jalan' ? 'checked' : '' }}
                                       required
                                       class="h-3.5 w-3.5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <span class="ml-1.5 text-xs text-gray-700">Non Uang Jalan</span>
                            </label>
                        </div>
                        @error('kategori_uang_jalan')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Komponen Biaya -->
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2">Komponen Biaya</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">

                    <!-- Jumlah Uang Jalan -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Uang Jalan <span class="text-red-600">*</span></label>
                        <div class="relative">
                            <span class="absolute left-2 top-2 text-xs text-gray-500">Rp</span>
                            <input type="number"
                                   name="jumlah_uang_jalan"
                                   id="jumlah_uang_jalan"
                                   value="{{ old('jumlah_uang_jalan', intval($suratJalan->uang_jalan ?? 0)) }}"
                                   min="0"
                                   step="1000"
                                   required
                                   oninput="calculateTotal()"
                                   class="w-full pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jumlah_uang_jalan') border-red-500 @enderror">
                        </div>
                        @error('jumlah_uang_jalan')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah MEL -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah MEL</label>
                        <div class="relative">
                            <span class="absolute left-2 top-2 text-xs text-gray-500">Rp</span>
                            <input type="number"
                                   name="jumlah_mel"
                                   id="jumlah_mel"
                                   value="{{ old('jumlah_mel', 0) }}"
                                   min="0"
                                   step="1000"
                                   oninput="calculateTotal()"
                                   class="w-full pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jumlah_mel') border-red-500 @enderror">
                        </div>
                        @error('jumlah_mel')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah Pelancar -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Pelancar</label>
                        <div class="relative">
                            <span class="absolute left-2 top-2 text-xs text-gray-500">Rp</span>
                            <input type="number"
                                   name="jumlah_pelancar"
                                   id="jumlah_pelancar"
                                   value="{{ old('jumlah_pelancar', 0) }}"
                                   min="0"
                                   step="1000"
                                   oninput="calculateTotal()"
                                   class="w-full pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jumlah_pelancar') border-red-500 @enderror">
                        </div>
                        @error('jumlah_pelancar')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah Kawalan -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Kawalan</label>
                        <div class="relative">
                            <span class="absolute left-2 top-2 text-xs text-gray-500">Rp</span>
                            <input type="number"
                                   name="jumlah_kawalan"
                                   id="jumlah_kawalan"
                                   value="{{ old('jumlah_kawalan', 0) }}"
                                   min="0"
                                   step="1000"
                                   oninput="calculateTotal()"
                                   class="w-full pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jumlah_kawalan') border-red-500 @enderror">
                        </div>
                        @error('jumlah_kawalan')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah Parkir -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Parkir</label>
                        <div class="relative">
                            <span class="absolute left-2 top-2 text-xs text-gray-500">Rp</span>
                            <input type="number"
                                   name="jumlah_parkir"
                                   id="jumlah_parkir"
                                   value="{{ old('jumlah_parkir', 0) }}"
                                   min="0"
                                   step="1000"
                                   oninput="calculateTotal()"
                                   class="w-full pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jumlah_parkir') border-red-500 @enderror">
                        </div>
                        @error('jumlah_parkir')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subtotal -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Subtotal</label>
                        <div class="relative">
                            <span class="absolute left-2 top-2 text-xs text-gray-500">Rp</span>
                            <input type="number"
                                   name="subtotal"
                                   id="subtotal"
                                   readonly
                                   value="{{ old('subtotal', 0) }}"
                                   class="w-full pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded bg-gray-50 text-gray-700 focus:outline-none @error('subtotal') border-red-500 @enderror">
                        </div>
                        @error('subtotal')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Penyesuaian -->
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2">Penyesuaian</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Alasan Penyesuaian -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Alasan Penyesuaian</label>
                        <input type="text" 
                               name="alasan_penyesuaian"
                               value="{{ old('alasan_penyesuaian') }}"
                               placeholder="Masukkan alasan penyesuaian"
                               class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('alasan_penyesuaian') border-red-500 @enderror">
                        @error('alasan_penyesuaian')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah Penyesuaian -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Penyesuaian</label>
                        <div class="relative">
                            <span class="absolute left-2 top-2 text-xs text-gray-500">Rp</span>
                            <input type="number" 
                                   name="jumlah_penyesuaian"
                                   id="jumlah_penyesuaian"
                                   value="{{ old('jumlah_penyesuaian', 0) }}"
                                   step="1000"
                                   oninput="calculateTotal()"
                                   class="w-full pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jumlah_penyesuaian') border-red-500 @enderror">
                            @error('jumlah_penyesuaian')
                                <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total & Memo -->
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2">Total & Memo</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Total -->
                    <div>
                        <div class="bg-gray-50 p-3 rounded">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Jumlah Total <span class="text-red-600">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-600 font-medium">Rp</span>
                                <input type="number" 
                                       name="jumlah_total"
                                       id="jumlah_total"
                                       readonly
                                       value="{{ old('jumlah_total', 0) }}"
                                       class="w-full pl-8 pr-3 py-2.5 border-2 border-gray-300 rounded bg-white text-base font-semibold text-gray-800 focus:outline-none @error('jumlah_total') border-red-500 @enderror">
                                @error('jumlah_total')
                                    <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
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
                        <h4 class="text-xs font-medium text-yellow-800 mb-1">Ringkasan</h4>
                        <div class="text-xs text-yellow-700">
                            <div class="grid grid-cols-3 gap-3 mb-2">
                                <div>
                                    <span class="font-medium">Supir:</span> {{ $suratJalan->supir ?? '-' }}
                                </div>
                                <div>
                                    <span class="font-medium">Kenek:</span> {{ $suratJalan->kenek ?? '-' }}
                                </div>
                                <div>
                                    <span class="font-medium">Plat:</span> {{ $suratJalan->no_plat ?? '-' }}
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
                    Simpan Uang Jalan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function calculateTotal() {
    // Ambil nilai dari setiap komponen biaya
    const jumlahUangJalan = parseFloat(document.getElementById('jumlah_uang_jalan').value) || 0;
    const jumlahMel = parseFloat(document.getElementById('jumlah_mel').value) || 0;
    const jumlahPelancar = parseFloat(document.getElementById('jumlah_pelancar').value) || 0;
    const jumlahKawalan = parseFloat(document.getElementById('jumlah_kawalan').value) || 0;
    const jumlahParkir = parseFloat(document.getElementById('jumlah_parkir').value) || 0;
    const jumlahPenyesuaian = parseFloat(document.getElementById('jumlah_penyesuaian').value) || 0;
    
    // Hitung subtotal (semua komponen kecuali penyesuaian)
    const subtotal = jumlahUangJalan + jumlahMel + jumlahPelancar + jumlahKawalan + jumlahParkir;
    
    // Hitung total (subtotal + penyesuaian)
    const total = subtotal + jumlahPenyesuaian;
    
    // Update nilai di form
    document.getElementById('subtotal').value = subtotal;
    document.getElementById('jumlah_total').value = total;
}

// Format currency input
function formatCurrency(input) {
    let value = input.value.replace(/[^\d]/g, '');
    if (value) {
        value = parseInt(value).toLocaleString('id-ID');
    }
    input.value = value;
}

// Calculate total on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
});
</script>
@endsection