@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Buat Pranota Uang Rit Supir Batam
            </h2>
        </div>

        <form action="{{ route('pranota-uang-rit-batam.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Info Section -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pranota</label>
                        <input type="date" name="tanggal_pranota" value="{{ date('Y-m-d') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Supir</label>
                        <select name="supir_nama" id="supir_nama" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                            <option value="">-- Pilih Supir --</option>
                            @foreach($availableSuratJalans->unique('supir') as $sj)
                                <option value="{{ $sj->supir }}">{{ $sj->supir }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Penyesuaian (Opsional)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-2 text-gray-500">Rp</span>
                            <input type="number" name="penyesuaian" id="penyesuaian" value="0"
                                   class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan</label>
                        <textarea name="catatan" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                                  placeholder="Tambahkan catatan jika ada..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Surat Jalan Selection -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    Pilih Surat Jalan
                </h3>
                
                <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" id="checkAll" class="rounded text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tujuan Ambil</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Uang Rit</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="suratJalanList">
                            @forelse($availableSuratJalans as $sj)
                                <tr class="hover:bg-indigo-50 transition-colors sj-row" data-supir="{{ $sj->supir }}">
                                    <td class="px-4 py-4">
                                        <input type="checkbox" name="surat_jalan_ids[]" value="{{ $sj->id }}" 
                                               class="sj-checkbox rounded text-indigo-600 focus:ring-indigo-500"
                                               data-amount="{{ is_numeric($sj->rit) ? $sj->rit : 0 }}">
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900">{{ $sj->no_surat_jalan }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-600">{{ $sj->tanggal_surat_jalan->format('d/m/Y') }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-600">{{ $sj->tujuan_pengambilan }}</td>
                                    <td class="px-4 py-4 text-sm text-right font-bold text-gray-900">
                                        Rp {{ number_format(is_numeric($sj->rit) ? $sj->rit : 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center text-gray-500 italic">
                                        Tidak ada Surat Jalan tersedia untuk supir ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-gray-600">
                        <span id="selectedCount" class="font-bold text-indigo-600">0</span> Surat Jalan terpilih
                    </div>
                    <div class="text-2xl font-black text-gray-900">
                        Total: <span id="totalDisplay" class="text-indigo-600">Rp 0</span>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('pranota-uang-rit-batam.index') }}" 
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-all font-semibold">
                            Batal
                        </a>
                        <button type="submit" id="submitBtn" disabled
                                class="px-8 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all font-bold shadow-lg shadow-indigo-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            Simpan Pranota
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const supirSelect = document.getElementById('supir_nama');
    const rows = document.querySelectorAll('.sj-row');
    const checkboxes = document.querySelectorAll('.sj-checkbox');
    const checkAll = document.getElementById('checkAll');
    const selectedCount = document.getElementById('selectedCount');
    const totalDisplay = document.getElementById('totalDisplay');
    const penyesuaianInput = document.getElementById('penyesuaian');
    const submitBtn = document.getElementById('submitBtn');

    function filterBySupir() {
        const supir = supirSelect.value;
        rows.forEach(row => {
            if (supir === '' || row.dataset.supir === supir) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
                row.querySelector('.sj-checkbox').checked = false;
            }
        });
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        let count = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) {
                total += parseFloat(cb.dataset.amount);
                count++;
            }
        });

        const penyesuaian = parseFloat(penyesuaianInput.value) || 0;
        const grandTotal = total + penyesuaian;

        selectedCount.textContent = count;
        totalDisplay.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
        submitBtn.disabled = count === 0;
    }

    supirSelect.addEventListener('change', filterBySupir);
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', calculateTotal);
    });

    checkAll.addEventListener('change', function() {
        const supir = supirSelect.value;
        checkboxes.forEach(cb => {
            const row = cb.closest('.sj-row');
            if (row.style.display !== 'none') {
                cb.checked = checkAll.checked;
            }
        });
        calculateTotal();
    });

    penyesuaianInput.addEventListener('input', calculateTotal);
    
    // Initial filter
    filterBySupir();
});
</script>
@endsection
