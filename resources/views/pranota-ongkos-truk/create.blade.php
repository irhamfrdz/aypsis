@extends('layouts.app')

@section('title', 'Buat Pranota Ongkos Truk')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Buat Pranota Ongkos Truk</h1>
                <p class="text-gray-600 mt-1">Selesaikan pembuatan pranota dengan memverifikasi data berikut</p>
            </div>
            <a href="{{ route('report.ongkos-truk.index') }}" class="flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Laporan
            </a>
        </div>

        @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <p class="text-red-700">{{ session('error') }}</p>
        </div>
        @endif

        <form action="{{ route('pranota-ongkos-truk.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Items Table -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                            <h2 class="font-semibold text-gray-800">Data Item Terpilih</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                    <tr>
                                        <th class="px-6 py-3 font-medium">No. Surat Jalan</th>
                                        <th class="px-6 py-3 font-medium">Tanggal</th>
                                        <th class="px-6 py-3 font-medium">Supir (Laporan)</th>
                                        <th class="px-6 py-3 font-medium text-right">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($items as $index => $item)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-medium text-gray-900">{{ $item['no_surat_jalan'] }}</span>
                                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item['id'] }}">
                                            <input type="hidden" name="items[{{ $index }}][no_surat_jalan]" value="{{ $item['no_surat_jalan'] }}">
                                            <input type="hidden" name="items[{{ $index }}][type]" value="{{ $item['type'] }}">
                                            <input type="hidden" name="items[{{ $index }}][tanggal]" value="{{ $item['tanggal'] }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item['tanggal'] ? \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item['supir'] ?: '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end">
                                                <span class="text-gray-400 mr-2 text-xs">Rp</span>
                                                <input type="number" name="items[{{ $index }}][nominal]" value="{{ $item['nominal'] }}" 
                                                    class="item-nominal w-32 border-none focus:ring-0 p-0 text-right font-semibold text-gray-900 bg-transparent" step="0.01">
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 font-bold">
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right text-gray-600">Total Nominal</td>
                                        <td class="px-6 py-4 text-right text-blue-600 text-lg">
                                            Rp <span id="grand-total">{{ number_format($items->sum('nominal'), 0, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Form Settings -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pranota</label>
                            <input type="date" name="tanggal_pranota" value="{{ now()->format('Y-m-d') }}" required
                                class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vendor (Opsional)</label>
                            <select name="vendor_id" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Pilih Vendor</option>
                                @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->nama_vendor }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Supir (Opsional)</label>
                            <select name="supir_id" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Pilih Supir</option>
                                @foreach($supirs as $supir)
                                <option value="{{ $supir->id }}">{{ $supir->nama_karyawan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                            <textarea name="keterangan" rows="3" placeholder="Tambahkan catatan..."
                                class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i> Simpan Pranota
                            </button>
                        </div>
                    </div>

                    <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-blue-900 mb-1">Informasi</h4>
                                <p class="text-sm text-blue-700 leading-relaxed">
                                    Nomor pranota akan dibuat secara otomatis menggunakan format <strong>POT[MM][YY][XXXXXX]</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nominalInputs = document.querySelectorAll('.item-nominal');
        const grandTotalDisplay = document.getElementById('grand-total');

        function updateGrandTotal() {
            let total = 0;
            nominalInputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            grandTotalDisplay.textContent = new Intl.NumberFormat('id-ID').format(total);
        }

        nominalInputs.forEach(input => {
            input.addEventListener('input', updateGrandTotal);
        });
    });
</script>
@endsection
