@extends('layouts.app')

@section('title', 'Edit Pembelian BBM Batam')
@section('page_title', 'Edit Pembelian BBM Batam')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-4">
        <a href="{{ route('pembelian-bbm-batam.index') }}" class="text-sm text-blue-600 hover:text-blue-900 flex items-center gap-2 font-medium">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Edit Pembelian BBM Batam</h3>
            <p class="text-xs text-gray-500 mt-1">Ubah detail data pembelian minyak/BBM untuk cabang Batam.</p>
        </div>

        <form action="{{ route('pembelian-bbm-batam.update', $item->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle mr-2 text-lg"></i>
                    <span class="font-bold">Periksa inputan Anda:</span>
                </div>
                <ul class="list-disc list-inside text-sm pl-4">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- No Bukti (Disabled) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">No Bukti</label>
                    <input type="text" value="{{ $item->nomor_bukti }}" disabled
                           class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-500 font-semibold cursor-not-allowed">
                </div>

                <!-- Tanggal -->
                <div>
                    <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Pembelian <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $item->tanggal ? $item->tanggal->format('Y-m-d') : '') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <!-- Jumlah Liter -->
                <div>
                    <label for="jumlah_liter" class="block text-sm font-semibold text-gray-700 mb-2">Jumlah (Liter) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="jumlah_liter" id="jumlah_liter" value="{{ old('jumlah_liter', $item->jumlah_liter) }}" required placeholder="Contoh: 150.50"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none font-semibold">
                </div>

                <!-- Harga Per Liter -->
                <div>
                    <label for="harga_per_liter" class="block text-sm font-semibold text-gray-700 mb-2">Harga Per Liter <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 text-sm">Rp</span>
                        <input type="number" step="0.01" name="harga_per_liter" id="harga_per_liter" value="{{ old('harga_per_liter', $item->harga_per_liter) }}" required placeholder="Contoh: 14500"
                               class="w-full border border-gray-300 rounded-lg pl-9 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none font-semibold">
                    </div>
                </div>

                <!-- Total Harga (Read Only / Auto Calc) -->
                <div class="md:col-span-2 bg-blue-50 border border-blue-100 rounded-xl p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-sm font-bold text-blue-900 block">Total Pembelian (Estimasi)</span>
                            <span class="text-xs text-blue-600">Terhitung otomatis dari Jumlah Liter × Harga Per Liter</span>
                        </div>
                        <div id="total_harga_display" class="text-xl font-black text-blue-900">
                            Rp {{ number_format($item->total_harga, 2, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- Supplier -->
                <div>
                    <label for="supplier" class="block text-sm font-semibold text-gray-700 mb-2">Supplier / Tempat Beli</label>
                    <input type="text" name="supplier" id="supplier" value="{{ old('supplier', $item->supplier) }}" placeholder="Contoh: SPBU Batam Center"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <!-- Nomor Nota -->
                <div>
                    <label for="nomor_nota" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Nota / Faktur</label>
                    <input type="text" name="nomor_nota" id="nomor_nota" value="{{ old('nomor_nota', $item->nomor_nota) }}" placeholder="Contoh: NOTA-12345"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">Keterangan / Catatan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" placeholder="Masukkan keterangan tambahan..."
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">{{ old('keterangan', $item->keterangan) }}</textarea>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 border-t border-gray-200 pt-6">
                <a href="{{ route('pembelian-bbm-batam.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jumlahInput = document.getElementById('jumlah_liter');
        const hargaInput = document.getElementById('harga_per_liter');
        const totalDisplay = document.getElementById('total_harga_display');

        function calculateTotal() {
            const jumlah = parseFloat(jumlahInput.value) || 0;
            const harga = parseFloat(hargaInput.value) || 0;
            const total = jumlah * harga;

            totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        }

        jumlahInput.addEventListener('input', calculateTotal);
        hargaInput.addEventListener('input', calculateTotal);
        
        // Initial calc
        calculateTotal();
    });
</script>
@endpush
@endsection
