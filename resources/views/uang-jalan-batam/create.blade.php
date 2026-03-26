@extends('layouts.app')

@section('page_title', 'Buat Uang Jalan Batam')

@section('content')
<div class="container mx-auto px-3 py-2">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 p-3 border-b border-gray-200">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Buat Uang Jalan Batam</h1>
                <p class="text-xs text-gray-600 mt-0.5">Buat pembayaran uang jalan Batam berdasarkan surat jalan yang dipilih</p>
            </div>
            <a href="{{ route('uang-jalan-batam.select-surat-jalan') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-2.5 py-1.5 rounded text-xs whitespace-nowrap flex items-center transition-colors">
                <svg class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Pilih Surat Jalan
            </a>
        </div>

        <!-- Surat Jalan Info -->
        <div class="mx-3 mt-4">
            <div class="bg-blue-50 border border-blue-200 rounded p-4">
                <div class="flex items-start">
                    <div class="p-2 bg-blue-100 rounded-lg mr-4">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-blue-800 mb-3">Informasi Surat Jalan Batam</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                            <div class="space-y-1">
                                <span class="text-blue-600 font-medium uppercase tracking-wider">No. Surat Jalan</span>
                                <div class="text-gray-900 font-bold text-sm">{{ $suratJalan->no_surat_jalan }}</div>
                            </div>
                            <div class="space-y-1">
                                <span class="text-blue-600 font-medium uppercase tracking-wider">Tanggal</span>
                                <div class="text-gray-900 font-bold text-sm">{{ $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}</div>
                            </div>
                            <div class="space-y-1">
                                <span class="text-blue-600 font-medium uppercase tracking-wider">Supir / Kenek</span>
                                <div class="text-gray-900 font-bold text-sm">{{ $suratJalan->supir ?? '-' }} / {{ $suratJalan->kenek ?? '-' }}</div>
                            </div>
                            <div class="space-y-1">
                                <span class="text-blue-600 font-medium uppercase tracking-wider">No. Plat</span>
                                <div class="text-gray-900 font-bold text-sm">{{ $suratJalan->no_plat ?? '-' }}</div>
                            </div>
                            <div class="space-y-1">
                                <span class="text-blue-600 font-medium uppercase tracking-wider">Ukuran</span>
                                <div class="text-gray-900 font-bold text-sm">{{ $suratJalan->size ?? '-' }}</div>
                            </div>
                            <div class="space-y-1">
                                <span class="text-blue-600 font-medium uppercase tracking-wider">No. Kontainer</span>
                                <div class="text-gray-900 font-bold text-sm">{{ $suratJalan->no_kontainer ?? '-' }}</div>
                            </div>
                            <div class="md:col-span-2 space-y-1">
                                <span class="text-blue-600 font-medium uppercase tracking-wider">Rute (Tujuan Ambil)</span>
                                <div class="text-gray-900 font-bold text-sm">{{ $suratJalan->tujuan_pengambilan ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('uang-jalan-batam.store') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="surat_jalan_batam_id" value="{{ $suratJalan->id }}">

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan input:</h3>
                            <ul class="mt-1 list-disc list-inside text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center">
                        <span class="w-2 h-2 bg-blue-600 rounded-full mr-2"></span>
                        Informasi Utama
                    </h3>
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Uang Jalan</label>
                        <input type="text" name="nomor_uang_jalan" value="{{ old('nomor_uang_jalan', $nomorUangJalan) }}" readonly
                               class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm font-bold text-gray-600 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Pembayaran <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_uang_jalan" value="{{ old('tanggal_uang_jalan', date('Y-m-d')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Status Pembayaran <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            @foreach(\App\Models\UangJalanBatam::getStatusOptions() as $key => $label)
                                <option value="{{ $key }}" {{ old('status', 'belum_dibayar') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center">
                        <span class="w-2 h-2 bg-blue-600 rounded-full mr-2"></span>
                        Catatan Tambahan
                    </h3>
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Memo / Keterangan</label>
                        <textarea name="memo" rows="4" placeholder="Tambahkan catatan jika diperlukan..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">{{ old('memo') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center border-b border-gray-200 pb-2">
                    <span class="w-4 h-4 bg-green-100 text-green-600 rounded-full flex items-center justify-center mr-2 text-[10px] font-bold">Rp</span>
                    Rincian Biaya
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Uang Jalan Pokok <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-400 text-sm">Rp</span>
                            <input type="number" name="jumlah_uang_jalan" id="jumlah_uang_jalan" 
                                   value="{{ old('jumlah_uang_jalan', intval($suratJalan->uang_jalan ?? 0)) }}" required
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm font-bold focus:ring-blue-500 focus:border-blue-500"
                                   oninput="calculateTotal()">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">MEL</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-400 text-sm">Rp</span>
                            <input type="number" name="jumlah_mel" id="jumlah_mel" value="{{ old('jumlah_mel', 0) }}"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                                   oninput="calculateTotal()">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Pelancar</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-400 text-sm">Rp</span>
                            <input type="number" name="jumlah_pelancar" id="jumlah_pelancar" value="{{ old('jumlah_pelancar', 0) }}"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                                   oninput="calculateTotal()">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Kawalan</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-400 text-sm">Rp</span>
                            <input type="number" name="jumlah_kawalan" id="jumlah_kawalan" value="{{ old('jumlah_kawalan', 0) }}"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                                   oninput="calculateTotal()">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Parkir</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-400 text-sm">Rp</span>
                            <input type="number" name="jumlah_parkir" id="jumlah_parkir" value="{{ old('jumlah_parkir', 0) }}"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                                   oninput="calculateTotal()">
                        </div>
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Subtotal</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500 font-bold text-sm">Rp</span>
                            <input type="text" id="subtotal_display" value="0" readonly
                                   class="w-full pl-10 pr-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-sm font-bold text-gray-700">
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Penyesuaian (Opsional)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Alasan Penyesuaian</label>
                            <input type="text" name="alasan_penyesuaian" value="{{ old('alasan_penyesuaian') }}" placeholder="Cth: Penambahan rute"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Jumlah Penyesuaian (+/-)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-400 text-sm">Rp</span>
                                <input type="number" name="jumlah_penyesuaian" id="jumlah_penyesuaian" value="{{ old('jumlah_penyesuaian', 0) }}"
                                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                                       oninput="calculateTotal()">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-between items-center bg-blue-600 rounded-lg p-5 text-white shadow-lg">
                    <div class="mb-4 md:mb-0">
                        <div class="text-xs font-medium uppercase tracking-widest opacity-80">Total Bayar Akhir</div>
                        <div class="text-3xl font-black" id="total_bayar_akhir_display">Rp 0</div>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('uang-jalan-batam.index') }}" 
                           class="px-6 py-2.5 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg font-bold text-sm transition-all border border-white border-opacity-30">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-8 py-2.5 bg-white text-blue-600 hover:bg-blue-50 rounded-lg font-bold text-sm transition-all shadow-md">
                            Simpan Uang Jalan Batam
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(number);
}

function calculateTotal() {
    const pokok = parseInt(document.getElementById('jumlah_uang_jalan').value) || 0;
    const mel = parseInt(document.getElementById('jumlah_mel').value) || 0;
    const pelancar = parseInt(document.getElementById('jumlah_pelancar').value) || 0;
    const kawalan = parseInt(document.getElementById('jumlah_kawalan').value) || 0;
    const parkir = parseInt(document.getElementById('jumlah_parkir').value) || 0;
    const penyesuaian = parseInt(document.getElementById('jumlah_penyesuaian').value) || 0;

    const subtotal = pokok + mel + pelancar + kawalan + parkir;
    const total = subtotal + penyesuaian;

    document.getElementById('subtotal_display').value = new Intl.NumberFormat('id-ID').format(subtotal);
    document.getElementById('total_bayar_akhir_display').innerText = formatRupiah(total);
}

// Initial calculation
document.addEventListener('DOMContentLoaded', calculateTotal);
</script>
@endsection
