@extends('layouts.app')

@section('title', 'Buat Pranota Uang Rit Batam')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">💰 Buat Pranota Uang Rit Supir Batam</h1>
                <p class="text-gray-600 mt-1">
                    Pilih surat jalan untuk pranota uang rit supir Batam
                    @if(isset($viewStartDate) && isset($viewEndDate))
                        <span class="text-sm text-blue-600 font-medium">
                            (Periode: {{ \Carbon\Carbon::parse($viewStartDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($viewEndDate)->format('d/m/Y') }})
                        </span>
                        <a href="{{ route('pranota-uang-rit-batam.select-date') }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline ml-2">Ubah Tanggal</a>
                    @endif
                </p>
            </div>
            <a href="{{ route('pranota-uang-rit-batam.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>

        <form action="{{ route('pranota-uang-rit-batam.store') }}" method="POST">
            @csrf

            <!-- Info Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">📝 Informasi Pranota</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label for="tanggal_pranota" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pranota <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_pranota" id="tanggal_pranota" value="{{ date('Y-m-d') }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                            <div>
                                <label for="supir_nama" class="block text-sm font-medium text-gray-700 mb-2">Pilih Supir <span class="text-red-500">*</span></label>
                                <select name="supir_nama" id="supir_nama" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">-- Pilih Supir --</option>
                                    @php
                                        $allSupirs = collect($availableRegular)->pluck('supir')
                                            ->merge(collect($availableBongkaran)->pluck('supir'))
                                            ->merge(collect($availableTarik)->pluck('supir'))
                                            ->unique()
                                            ->sort();
                                    @endphp
                                    @foreach($allSupirs as $supir)
                                        <option value="{{ $supir }}">{{ $supir }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="penyesuaian" class="block text-sm font-medium text-gray-700 mb-2">Penyesuaian (Opsional)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500 text-sm">Rp</span>
                                    <input type="number" name="penyesuaian" id="penyesuaian" value="0"
                                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                            </div>
                            <div>
                                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                                <textarea name="catatan" id="catatan" rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                          placeholder="Tambahkan catatan jika ada..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Surat Jalan Selection Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">🚚 Pilih Surat Jalan</h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input type="checkbox" id="checkAll" class="rounded text-blue-600 focus:ring-blue-500">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan/Barang</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Uang Rit</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="suratJalanList">
                                {{-- Regular SJ --}}
                                @foreach($availableRegular as $sj)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 sj-row" data-supir="{{ $sj->supir }}">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" name="surat_jalan_ids[]" value="{{ $sj->id }}" 
                                                   class="sj-checkbox rounded text-blue-600 focus:ring-blue-500"
                                                   data-amount="{{ is_numeric($sj->rit) ? $sj->rit : 0 }}">
                                        </td>
                                        <td class="px-6 py-4 text-xs font-medium text-gray-500">REGULAR</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $sj->no_surat_jalan }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $sj->tanggal_surat_jalan->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $sj->tujuan_pengambilan }}</td>
                                        <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">
                                            Rp {{ number_format(is_numeric($sj->rit) ? $sj->rit : 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Bongkaran SJ --}}
                                @foreach($availableBongkaran as $sj)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 sj-row" data-supir="{{ $sj->supir }}">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" name="surat_jalan_bongkaran_ids[]" value="{{ $sj->id }}" 
                                                   class="sj-checkbox rounded text-blue-600 focus:ring-blue-500"
                                                   data-amount="{{ is_numeric($sj->rit) ? $sj->rit : 0 }}">
                                        </td>
                                        <td class="px-6 py-4 text-xs font-medium text-indigo-500">BONGKARAN</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $sj->nomor_surat_jalan }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $sj->tanggal_surat_jalan->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $sj->jenis_barang }}</td>
                                        <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">
                                            Rp {{ number_format(is_numeric($sj->rit) ? $sj->rit : 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Tarik Kosong SJ --}}
                                @foreach($availableTarik as $sj)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 sj-row" data-supir="{{ $sj->supir }}">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" name="surat_jalan_tarik_kosong_ids[]" value="{{ $sj->id }}" 
                                                   class="sj-checkbox rounded text-blue-600 focus:ring-blue-500"
                                                   data-amount="{{ is_numeric($sj->rit) ? $sj->rit : 0 }}">
                                        </td>
                                        <td class="px-6 py-4 text-xs font-medium text-green-500">TARIK KOSONG</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $sj->no_surat_jalan }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $sj->tanggal_surat_jalan->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $sj->tujuan_pengambilan }}</td>
                                        <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">
                                            Rp {{ number_format(is_numeric($sj->rit) ? $sj->rit : 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach

                                @if(count($availableRegular) == 0 && count($availableBongkaran) == 0 && count($availableTarik) == 0)
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>
                                            Tidak ada Surat Jalan tersedia untuk supir ini.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary & Submit -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="text-gray-600">
                            <span id="selectedCount" class="font-bold text-blue-600">0</span> Surat Jalan terpilih
                        </div>
                        <div class="text-2xl font-bold text-gray-900">
                            Total: <span id="totalDisplay" class="text-blue-600">Rp 0</span>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('pranota-uang-rit-batam.index') }}" 
                               class="inline-flex items-center px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 font-medium">
                                Batal
                            </a>
                            <button type="submit" id="submitBtn" disabled
                                    class="inline-flex items-center px-8 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-save mr-2"></i> Simpan Pranota
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
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
@endpush
