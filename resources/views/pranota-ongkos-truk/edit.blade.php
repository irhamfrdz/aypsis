@extends('layouts.app')

@section('title', 'Edit Pranota Ongkos Truk')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Pranota Ongkos Truk</h1>
                <p class="text-gray-600 mt-1">Ubah detail pranota {{ $pranota->no_pranota }}</p>
            </div>
            <a href="{{ route('pranota-ongkos-truk.show', $pranota->id) }}" class="flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail
            </a>
        </div>

        @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <p class="text-red-700">{{ session('error') }}</p>
        </div>
        @endif

        <form action="{{ route('pranota-ongkos-truk.update', $pranota->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Items Table -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                            <h2 class="font-semibold text-gray-800">Rincian Item Surat Jalan</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                    <tr>
                                        <th class="px-6 py-3 font-medium">No. Surat Jalan</th>
                                        <th class="px-6 py-3 font-medium">Tanggal</th>
                                        <th class="px-6 py-3 font-medium">Tujuan</th>
                                        <th class="px-6 py-3 font-medium">Supir</th>
                                        <th class="px-6 py-3 font-medium text-right">Nominal (Net)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($pranota->items as $index => $item)
                                    @php
                                        $tujuan = '-';
                                        $supir = '-';
                                        if ($item->type === 'SuratJalan' && $item->suratJalan) {
                                            $tujuan = $item->suratJalan->tujuanPengambilanRelation->ke ?? $item->suratJalan->tujuan_pengambilan ?? '-';
                                            $supir = $item->suratJalan->supirKaryawan ? ($item->suratJalan->supirKaryawan->nama_panggilan ?? $item->suratJalan->supirKaryawan->nama_lengkap) : ($item->suratJalan->supir ?: '-');
                                        } elseif ($item->type === 'SuratJalanBongkaran' && $item->suratJalanBongkaran) {
                                            $tujuan = $item->suratJalanBongkaran->tujuanPengambilanRelation->ke ?? $item->suratJalanBongkaran->tujuan_pengambilan ?? '-';
                                            $supir = $item->suratJalanBongkaran->supirKaryawan ? ($item->suratJalanBongkaran->nama_panggilan ?? $item->suratJalanBongkaran->nama_lengkap) : ($item->suratJalanBongkaran->supir ?: '-');
                                        }
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-medium text-gray-900">{{ $item->no_surat_jalan }}</span>
                                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                            <input type="hidden" name="items[{{ $index }}][no_surat_jalan]" value="{{ $item->no_surat_jalan }}">
                                            <input type="hidden" name="items[{{ $index }}][type]" value="{{ $item->type }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $tujuan }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $supir }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end">
                                                <span class="text-gray-400 mr-2 text-xs">Rp</span>
                                                <input type="number" name="items[{{ $index }}][nominal]" value="{{ $item->nominal }}" 
                                                    class="item-nominal w-32 border-none focus:ring-0 p-0 text-right font-semibold text-gray-900 bg-transparent" step="0.01">
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 font-bold">
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-right text-gray-600">Total Nominal Item</td>
                                        <td class="px-6 py-4 text-right text-blue-600 text-lg">
                                            Rp <span id="items-total">{{ number_format($pranota->items->sum('nominal'), 0, ',', '.') }}</span>
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
                            <input type="date" name="tanggal_pranota" value="{{ $pranota->tanggal_pranota->format('Y-m-d') }}" required
                                class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Dynamic Adjustments Section -->
                        <div class="border-t pt-4">
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-bold text-gray-700">Adjustments (Opsional)</label>
                                <button type="button" onclick="addAdjustmentRow()" class="px-3 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-bold transition-all duration-200">
                                    <i class="fas fa-plus mr-1"></i> Tambah
                                </button>
                            </div>
                            
                            <div id="adjustments_container" class="space-y-3">
                                <!-- Existing adjustments rendered here -->
                                @if($pranota->adjustments && is_array($pranota->adjustments))
                                    @foreach($pranota->adjustments as $adjIndex => $adj)
                                    <div id="adj_row_{{ $adjIndex }}" class="flex items-center gap-2 bg-gray-50/50 p-2.5 rounded-xl border border-gray-100">
                                        <div class="w-1/3">
                                            <div class="relative">
                                                <span class="absolute left-2.5 top-1.5 text-gray-400 text-xs">Rp</span>
                                                <input type="number" name="adjustments[{{ $adjIndex }}][nominal]" class="adj-nominal w-full pl-7 pr-2 py-1 border border-gray-200 rounded-lg text-xs font-semibold focus:ring-2 focus:ring-blue-500" 
                                                    placeholder="0" value="{{ $adj['nominal'] ?? '' }}" oninput="updateTotals()">
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <input type="text" name="adjustments[{{ $adjIndex }}][keterangan]" class="adj-keterangan w-full px-2.5 py-1 border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500" 
                                                placeholder="Keterangan..." value="{{ $adj['keterangan'] ?? '' }}">
                                        </div>
                                        <button type="button" onclick="removeAdjustmentRow('adj_row_{{ $adjIndex }}')" class="text-red-500 hover:text-red-700 p-1 rounded-lg hover:bg-red-50">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            <p class="text-[9px] text-gray-400 mt-2">Gunakan minus (-) untuk pengurangan nominal</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                            <textarea name="keterangan" rows="3" placeholder="Tambahkan catatan..."
                                class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $pranota->keterangan }}</textarea>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex justify-between items-center font-bold">
                            <span class="text-sm text-gray-600">Grand Total</span>
                            <span class="text-xl text-indigo-700">Rp <span id="grand-total">{{ number_format($pranota->total_nominal, 0, ',', '.') }}</span></span>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i> Update Pranota
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let adjCount = {{ count($pranota->adjustments ?? []) }};

    window.addAdjustmentRow = function() {
        adjCount++;
        const container = document.getElementById('adjustments_container');
        const rowId = `adj_row_new_${adjCount}`;
        const rowHtml = `
            <div id="${rowId}" class="flex items-center gap-2 bg-gray-50/50 p-2.5 rounded-xl border border-gray-100">
                <div class="w-1/3">
                    <div class="relative">
                        <span class="absolute left-2.5 top-1.5 text-gray-400 text-xs">Rp</span>
                        <input type="number" name="adjustments[new_${adjCount}][nominal]" class="adj-nominal w-full pl-7 pr-2 py-1 border border-gray-200 rounded-lg text-xs font-semibold focus:ring-2 focus:ring-blue-500" 
                            placeholder="0" oninput="updateTotals()">
                    </div>
                </div>
                <div class="flex-1">
                    <input type="text" name="adjustments[new_${adjCount}][keterangan]" class="adj-keterangan w-full px-2.5 py-1 border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500" 
                        placeholder="Keterangan...">
                </div>
                <button type="button" onclick="removeAdjustmentRow('${rowId}')" class="text-red-500 hover:text-red-700 p-1 rounded-lg hover:bg-red-50">
                    <i class="fas fa-trash-alt text-xs"></i>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', rowHtml);
        updateTotals();
    };

    window.removeAdjustmentRow = function(rowId) {
        const row = document.getElementById(rowId);
        if (row) {
            row.remove();
            updateTotals();
        }
    };

    window.updateTotals = function() {
        const nominalInputs = document.querySelectorAll('.item-nominal');
        const itemsTotalDisplay = document.getElementById('items-total');
        const adjNominalInputs = document.querySelectorAll('.adj-nominal');
        const grandTotalDisplay = document.getElementById('grand-total');

        let itemsTotal = 0;
        nominalInputs.forEach(input => {
            itemsTotal += parseFloat(input.value) || 0;
        });

        let adjustmentsTotal = 0;
        adjNominalInputs.forEach(input => {
            adjustmentsTotal += parseFloat(input.value) || 0;
        });

        const grandTotal = itemsTotal + adjustmentsTotal;

        itemsTotalDisplay.textContent = new Intl.NumberFormat('id-ID').format(itemsTotal);
        grandTotalDisplay.textContent = new Intl.NumberFormat('id-ID').format(grandTotal);
    };

    document.addEventListener('DOMContentLoaded', function() {
        const nominalInputs = document.querySelectorAll('.item-nominal');
        nominalInputs.forEach(input => {
            input.addEventListener('input', updateTotals);
        });
        updateTotals();
    });
</script>
@endsection
