@extends('layouts.app')

@section('content')
<div class="container mx-auto px-3 py-2">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Buat Pranota Uang Jalan</h1>
                <p class="text-xs text-gray-600 mt-0.5">Pilih uang jalan yang akan dimasukkan ke pranota</p>
            </div>
            <a href="{{ route('pranota-uang-jalan.index') }}" 
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

        <form action="{{ route('pranota-uang-jalan.store') }}" method="POST" id="pranotaForm">
            @csrf
            
            <!-- Form Information -->
            <div class="bg-white rounded border border-gray-200 p-4 mb-4">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Informasi Pranota</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Pranota <span class="text-red-600">*</span></label>
                        <input type="date" 
                               name="tanggal_pranota" 
                               value="{{ old('tanggal_pranota', date('Y-m-d')) }}" 
                               required
                               class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_pranota') border-red-500 @enderror">
                        @error('tanggal_pranota')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                        <input type="text" 
                               name="keterangan" 
                               value="{{ old('keterangan') }}" 
                               placeholder="Keterangan tambahan (opsional)"
                               class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('keterangan') border-red-500 @enderror">
                        @error('keterangan')
                            <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Status Information -->
            <div class="bg-blue-50 border border-blue-200 p-3 mb-4 rounded">
                <div class="flex items-center">
                    <svg class="h-4 w-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-xs text-blue-800">
                        <strong>Info:</strong> Pranota akan dibuat dengan status 
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Disetujui</span>
                        dan status uang jalan yang dipilih akan berubah dari 
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">Belum Pranota</span>
                        menjadi 
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Sudah Pranota</span>
                    </div>
                </div>
            </div>

            <!-- Available Uang Jalan -->
            <div class="bg-white rounded border border-gray-200 p-4 mb-4">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-sm font-medium text-gray-900">Pilih Uang Jalan</h3>
                    <div class="flex items-center space-x-2">
                        <button type="button" 
                                onclick="selectAll()" 
                                class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-200">
                            Pilih Semua
                        </button>
                        <button type="button" 
                                onclick="deselectAll()" 
                                class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded hover:bg-gray-200">
                            Batal Semua
                        </button>
                    </div>
                </div>

                @if($availableUangJalans->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2">
                                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    </th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Uang Jalan</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Surat Jalan</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Supir/Kenek</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($availableUangJalans as $uangJalan)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2">
                                            <input type="checkbox" 
                                                   name="uang_jalan_ids[]" 
                                                   value="{{ $uangJalan->id }}" 
                                                   data-amount="{{ $uangJalan->jumlah_total }}"
                                                   onchange="updateTotal()"
                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded uang-jalan-checkbox"
                                                   {{ in_array($uangJalan->id, old('uang_jalan_ids', [])) ? 'checked' : '' }}>
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="text-sm font-medium text-gray-900">{{ $uangJalan->nomor_uang_jalan }}</div>
                                            <div class="text-xs text-gray-500">{{ $uangJalan->kegiatan_bongkar_muat }}</div>
                                        </td>
                                        <td class="px-3 py-2 text-sm text-gray-900">
                                            {{ $uangJalan->tanggal_uang_jalan ? $uangJalan->tanggal_uang_jalan->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-3 py-2">
                                            @if($uangJalan->suratJalan)
                                                <div class="text-sm text-gray-900">{{ $uangJalan->suratJalan->no_surat_jalan }}</div>
                                                <div class="text-xs text-gray-500">{{ $uangJalan->suratJalan->kegiatan }}</div>
                                            @elseif($uangJalan->suratJalanBongkaran)
                                                <div class="text-sm text-gray-900">{{ $uangJalan->suratJalanBongkaran->nomor_surat_jalan }}</div>
                                                <div class="text-xs text-gray-500">{{ $uangJalan->suratJalanBongkaran->kegiatan ?? 'Bongkar' }}</div>
                                            @else
                                                <div class="text-sm text-gray-500">-</div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2">
                                            @php
                                                $surat = $uangJalan->suratJalan ?? $uangJalan->suratJalanBongkaran;
                                            @endphp
                                            @if($surat)
                                                <div class="text-xs text-gray-900">
                                                    <div>
                                                        <span class="font-medium">Supir:</span> {{ $surat->supir ?? '-' }}
                                                        @if($surat->supir_nik)
                                                            <span class="text-gray-500">({{ $surat->supir_nik }})</span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">Kenek:</span> {{ $surat->kenek ?? '-' }}
                                                        @if($surat->kenek_nik)
                                                            <span class="text-gray-500">({{ $surat->kenek_nik }})</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-xs text-gray-500">-</div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2">
                                            @php
                                                $statusConfig = [
                                                    'belum_dibayar' => ['bg-yellow-100', 'text-yellow-800', 'Belum Dibayar'],
                                                    'belum_masuk_pranota' => ['bg-orange-100', 'text-orange-800', 'Belum Pranota'],
                                                    'sudah_masuk_pranota' => ['bg-blue-100', 'text-blue-800', 'Sudah Pranota'],
                                                ];
                                                $config = $statusConfig[$uangJalan->status] ?? ['bg-gray-100', 'text-gray-800', ucwords(str_replace('_', ' ', $uangJalan->status))];
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $config[0] }} {{ $config[1] }}">
                                                {{ $config[2] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="text-sm font-semibold text-gray-900">
                                                Rp {{ number_format($uangJalan->jumlah_total, 0, ',', '.') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div class="mt-4 p-3 bg-gray-50 rounded">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-600">
                                <span id="selectedCount">0</span> uang jalan dipilih
                            </div>
                            <div class="text-sm font-semibold text-gray-900">
                                Total: Rp <span id="totalAmount">0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Penyesuaian -->
                    <div class="mt-4 p-4 bg-white border border-gray-200 rounded">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Penyesuaian</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="penyesuaian" class="block text-sm font-medium text-gray-700 mb-1">
                                    Jumlah Penyesuaian
                                </label>
                                <div class="flex">
                                    <select id="penyesuaianType" 
                                            onchange="updateTotalWithPenyesuaian()" 
                                            class="w-16 rounded-l-md border-2 border-gray-300 border-r-0 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:z-10">
                                        <option value="subtract">−</option>
                                        <option value="add">+</option>
                                    </select>
                                    <div class="relative flex-1">
                                        <input type="number" 
                                               name="penyesuaian_amount" 
                                               id="penyesuaian_amount" 
                                               value="{{ old('penyesuaian_amount', 0) }}"
                                               step="0.01"
                                               min="0"
                                               class="block w-full rounded-none rounded-r-md border-2 border-gray-300 pl-3 pr-12 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('penyesuaian') border-red-400 focus:border-red-500 @enderror"
                                               placeholder="0.00"
                                               onchange="updateTotalWithPenyesuaian()">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <span class="text-gray-500 text-sm font-medium">Rp</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="penyesuaian" id="penyesuaian">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Pilih (-) untuk mengurangi atau (+) untuk menambah total</p>
                                @error('penyesuaian')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="keterangan_penyesuaian" class="block text-sm font-medium text-gray-700 mb-1">
                                    Keterangan Penyesuaian
                                </label>
                                <textarea name="keterangan_penyesuaian" 
                                          id="keterangan_penyesuaian" 
                                          rows="3"
                                          class="block w-full rounded-md border-2 border-gray-300 px-3 py-2 text-sm placeholder-gray-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('keterangan_penyesuaian') border-red-400 focus:border-red-500 focus:ring-red-500 @enderror"
                                          placeholder="Jelaskan alasan penyesuaian (opsional)...">{{ old('keterangan_penyesuaian') }}</textarea>
                                @error('keterangan_penyesuaian')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <!-- Total Akhir -->
                        <div class="mt-4 p-4 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-lg border border-indigo-200">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-indigo-800">Total Akhir</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-indigo-900">
                                        Rp <span id="totalWithPenyesuaian">0</span>
                                    </div>
                                    <div class="text-xs text-indigo-600">Subtotal + Penyesuaian</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="h-12 w-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 text-sm">Tidak ada uang jalan yang tersedia</p>
                        <p class="text-xs text-gray-400 mt-1">Semua uang jalan sudah masuk pranota atau belum ada yang dibuat</p>
                        <a href="{{ route('uang-jalan.select-surat-jalan') }}" 
                           class="mt-2 inline-block text-indigo-600 hover:text-indigo-500 text-sm">
                            Buat uang jalan baru
                        </a>
                    </div>
                @endif
            </div>

            <!-- Submit Button -->
            @if($availableUangJalans->count() > 0)
                <div class="flex justify-end gap-2">
                    <a href="{{ route('pranota-uang-jalan.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded text-sm">
                        Batal
                    </a>
                    <button type="submit" 
                            id="submitBtn"
                            disabled
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm disabled:bg-gray-400 disabled:cursor-not-allowed">
                        Buat Pranota
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>

<script>
window.updateTotal = function() {
    const checkboxes = document.querySelectorAll('.uang-jalan-checkbox:checked');
    let total = 0;
    let count = 0;

    checkboxes.forEach(function(checkbox) {
        total += parseFloat(checkbox.getAttribute('data-amount')) || 0;
        count++;
    });

    document.getElementById('selectedCount').textContent = count;
    document.getElementById('totalAmount').textContent = total.toLocaleString('id-ID');
    
    // Update total with penyesuaian
    updateTotalWithPenyesuaian();
    
    // Enable/disable submit button
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.disabled = count === 0;
    }

    // Update select all checkbox
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const allCheckboxes = document.querySelectorAll('.uang-jalan-checkbox');
    if (selectAllCheckbox && allCheckboxes.length > 0) {
        selectAllCheckbox.checked = checkboxes.length === allCheckboxes.length;
        selectAllCheckbox.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
    }
}

window.updateTotalWithPenyesuaian = function() {
    const totalAmountText = document.getElementById('totalAmount').textContent;
    const totalAmount = parseFloat(totalAmountText.replace(/[^\d]/g, '')) || 0;
    
    const penyesuaianType = document.getElementById('penyesuaianType').value;
    const penyesuaianAmount = parseFloat(document.getElementById('penyesuaian_amount').value) || 0;
    
    // Hitung penyesuaian berdasarkan tipe
    let penyesuaian = 0;
    if (penyesuaianType === 'subtract') {
        penyesuaian = -Math.abs(penyesuaianAmount); // Selalu negatif untuk pengurangan
    } else if (penyesuaianType === 'add') {
        penyesuaian = Math.abs(penyesuaianAmount); // Selalu positif untuk penambahan
    }
    
    // Update hidden input dengan nilai final
    document.getElementById('penyesuaian').value = penyesuaian;
    
    const totalWithPenyesuaian = totalAmount + penyesuaian;
    
    const totalWithPenyesuaianElement = document.getElementById('totalWithPenyesuaian');
    if (totalWithPenyesuaianElement) {
        totalWithPenyesuaianElement.textContent = totalWithPenyesuaian.toLocaleString('id-ID');
    }
}

window.toggleSelectAll = function() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.uang-jalan-checkbox');
    
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateTotal();
}

window.selectAll = function() {
    const checkboxes = document.querySelectorAll('.uang-jalan-checkbox');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = true;
    });
    updateTotal();
}

window.deselectAll = function() {
    const checkboxes = document.querySelectorAll('.uang-jalan-checkbox');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = false;
    });
    updateTotal();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateTotal();
    updateTotalWithPenyesuaian();
});
</script>
@endsection