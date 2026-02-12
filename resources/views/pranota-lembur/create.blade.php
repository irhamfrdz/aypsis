@extends('layouts.app')

@section('title', 'Buat Pranota Lembur')
@section('page_title', 'Buat Pranota Lembur')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <i class="fas fa-bed mr-3 text-blue-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Buat Pranota Lembur/Nginap</h1>
                    <p class="text-gray-600">Periode: {{ $startDate->format('d/m/Y') }} s/d {{ $endDate->format('d/m/Y') }}</p>
                </div>
            </div>
            <a href="{{ route('pranota-lembur.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($suratJalans->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <i class="fas fa-inbox text-yellow-400 text-5xl mb-4"></i>
                <h3 class="text-lg font-semibold text-yellow-800 mb-2">Tidak Ada Data</h3>
                <p class="text-yellow-700">Tidak ada surat jalan dengan status lembur/nginap yang belum masuk pranota pada periode ini.</p>
                <a href="{{ route('pranota-lembur.index') }}" class="mt-4 inline-block bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-md transition duration-200">
                    Pilih Periode Lain
                </a>
            </div>
        @else
            <form method="POST" action="{{ route('pranota-lembur.store') }}" id="pranotaForm">
                @csrf

                <!-- Header Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Pranota</label>
                        <input type="text" value="{{ $nomorPranotaDisplay }}" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-gray-800 font-semibold" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pranota <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_pranota" value="{{ old('tanggal_pranota', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Item</label>
                        <input type="text" id="totalItems" value="0" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-gray-800 font-semibold" readonly>
                    </div>
                </div>

                <input type="hidden" name="nomor_cetakan" value="{{ $nomorCetakan }}">

                <!-- Table -->
                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @if(isset($preChecked) && $preChecked) checked @endif>
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal TT</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No SJ</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supir</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plat</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lembur</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nginap</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Biaya Lembur</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Biaya Nginap</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($suratJalans as $index => $sj)
                            <tr class="hover:bg-gray-50 surat-jalan-row">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="selected[]" value="{{ $sj->id }}_{{ $sj->type_surat }}" class="item-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" @if(isset($preChecked) && $preChecked) checked @endif>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-sm">{{ $sj->report_date ? \Carbon\Carbon::parse($sj->report_date)->format('d/M/Y') : '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sj->type_surat == 'Muat' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                        {{ $sj->type_surat }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $sj->no_surat_jalan }}</td>
                                <td class="px-4 py-3 text-sm">{{ $sj->supir }}</td>
                                <td class="px-4 py-3 text-sm">{{ $sj->no_plat }}</td>
                                <td class="px-4 py-3">
                                    @if($sj->lembur)
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold"><i class="fas fa-check"></i></span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($sj->nginap)
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold"><i class="fas fa-check"></i></span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[{{ $index }}][biaya_lembur]" value="0" min="0" step="1000" class="biaya-lembur w-32 px-2 py-1 text-sm border border-gray-300 rounded-md" {{ !$sj->lembur ? 'readonly' : '' }}>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[{{ $index }}][biaya_nginap]" value="0" min="0" step="1000" class="biaya-nginap w-32 px-2 py-1 text-sm border border-gray-300 rounded-md" {{ !$sj->nginap ? 'readonly' : '' }}>
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold total-per-row">Rp 0</td>
                                
                                <!-- Hidden fields -->
                                <input type="hidden" name="items[{{ $index }}][type]" value="{{ strtolower($sj->type_surat) }}">
                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $sj->id }}">
                                <input type="hidden" name="items[{{ $index }}][supir]" value="{{ $sj->supir }}">
                                <input type="hidden" name="items[{{ $index }}][no_plat]" value="{{ $sj->no_plat }}">
                                <input type="hidden" name="items[{{ $index }}][is_lembur]" value="{{ $sj->lembur ? 1 : 0 }}">
                                <input type="hidden" name="items[{{ $index }}][is_nginap]" value="{{ $sj->nginap ? 1 : 0 }}">
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="9" class="px-4 py-3 text-right font-semibold">Total Biaya:</td>
                                <td colspan="3" class="px-4 py-3 font-bold text-lg" id="grandTotal">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Additional Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Adjustment</label>
                        <input type="number" name="adjustment" value="0" step="1000" id="adjustment" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Nilai positif untuk penambahan, negatif untuk pengurangan</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Adjustment</label>
                        <input type="text" name="alasan_adjustment" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea name="catatan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('pranota-lembur.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition duration-200">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Pranota
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const totalItemsEl = document.getElementById('totalItems');
    const grandTotalEl = document.getElementById('grandTotal');
    const adjustmentEl = document.getElementById('adjustment');

    // Select All functionality
    selectAll?.addEventListener('change', function() {
        itemCheckboxes.forEach(cb => cb.checked = this.checked);
        updateTotals();
    });

    // Individual checkbox change
    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateTotals);
    });

    // Calculate row total when biaya changes
    document.querySelectorAll('.biaya-lembur, .biaya-nginap').forEach(input => {
        input.addEventListener('input', function() {
            const row = this.closest('tr');
            calculateRowTotal(row);
            updateTotals();
        });
    });

    // Adjustment change
    adjustmentEl?.addEventListener('input', updateTotals);

    function calculateRowTotal(row) {
        const biayaLembur = parseFloat(row.querySelector('.biaya-lembur').value) || 0;
        const biayaNginap = parseFloat(row.querySelector('.biaya-nginap').value) || 0;
        const total = biayaLembur + biayaNginap;
        row.querySelector('.total-per-row').textContent = formatRupiah(total);
    }

    function updateTotals() {
        let checkedCount = 0;
        let grandTotal = 0;

        itemCheckboxes.forEach(cb => {
            if (cb.checked) {
                checkedCount++;
                const row = cb.closest('tr');
                const biayaLembur = parseFloat(row.querySelector('.biaya-lembur').value) || 0;
                const biayaNginap = parseFloat(row.querySelector('.biaya-nginap').value) || 0;
                grandTotal += biayaLembur + biayaNginap;
            }
        });

        totalItemsEl.value = checkedCount;
        
        const adjustment = parseFloat(adjustmentEl.value) || 0;
        const finalTotal = grandTotal + adjustment;
        
        grandTotalEl.textContent = formatRupiah(finalTotal);
    }

    function formatRupiah(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

    // Form validation
    document.getElementById('pranotaForm')?.addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal 1 surat jalan');
            return false;
        }
    });

    // Initialize calculations
    document.querySelectorAll('.surat-jalan-row').forEach(row => calculateRowTotal(row));
    updateTotals();
});
</script>
@endpush
