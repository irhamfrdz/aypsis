@extends('layouts.app')

@section('title', 'Tambah Pranota Uang Kenek')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-lg rounded-lg p-4">
        {{-- Notifikasi --}}
        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
                <p class="font-bold">Peringatan</p>
                <ul class="list-disc list-inside mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
                <p class="font-bold">Peringatan</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
                <p class="font-bold">Sukses</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @php
            // Definisikan kelas Tailwind untuk input yang lebih compact
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors";
            $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-sm p-2";
            $labelClasses = "block text-xs font-medium text-gray-700 mb-1";
        @endphp

        <form action="{{ route('pranota-uang-kenek.store') }}" method="POST" id="pranotaForm" class="space-y-3">
            @csrf

            <!-- Hidden inputs untuk data hutang dan tabungan per kenek -->
            <div id="kenekDetailsInputs"></div>

            <!-- Data Pranota & Total Uang dalam satu baris -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Data Pranota -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">💰 Data Pranota Uang Kenek</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div>
                                <label for="tanggal" class="{{ $labelClasses }}">
                                    Tanggal Pranota <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       class="{{ $inputClasses }} {{ $errors->has('tanggal') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : '' }}"
                                       id="tanggal" 
                                       name="tanggal" 
                                       value="{{ old('tanggal', date('Y-m-d')) }}" 
                                       required>
                                @error('tanggal')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="jumlah_surat_jalan_display" class="{{ $labelClasses }}">Jumlah Surat Jalan</label>
                                <input type="text" 
                                       class="{{ $readonlyInputClasses }}"
                                       id="jumlah_surat_jalan_display"
                                       value="0 surat jalan"
                                       readonly>
                            </div>
                        </div>
                        <div class="mt-2">
                            <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                            <textarea class="{{ $inputClasses }} {{ $errors->has('keterangan') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : '' }}"
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="3" 
                                      placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Total Uang -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">📊 Total Keseluruhan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label for="jumlah_kenek_display" class="{{ $labelClasses }}">Jumlah Kenek</label>
                                <input type="text" class="{{ $readonlyInputClasses }}" id="jumlah_kenek_display" value="0 kenek" readonly>
                            </div>
                            <div>
                                <label for="total_uang_kenek_display" class="{{ $labelClasses }}">Total Uang Kenek</label>
                                <input type="text" class="{{ $readonlyInputClasses }}" id="total_uang_kenek_display" value="Rp 0" readonly>
                            </div>
                            <div>
                                <label for="total_utang_display" class="{{ $labelClasses }}">Total Hutang</label>
                                <input type="text" class="{{ $readonlyInputClasses }}" id="total_utang_display" value="Rp 0" readonly>
                            </div>
                            <div>
                                <label for="total_tabungan_display" class="{{ $labelClasses }}">Total Tabungan</label>
                                <input type="text" class="{{ $readonlyInputClasses }}" id="total_tabungan_display" value="Rp 0" readonly>
                            </div>
                            <div class="md:col-span-2">
                                <label for="grand_total_display" class="{{ $labelClasses }}">Grand Total</label>
                                <input type="text" class="{{ $readonlyInputClasses }} font-bold text-purple-700" id="grand_total_display" value="Rp 0" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pilih Surat Jalan -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <h4 class="text-sm font-semibold text-gray-800">🚛 Pilih Surat Jalan untuk Uang Kenek</h4>
                            <span id="searchResults" class="text-xs text-gray-500 hidden"></span>
                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            <div class="relative flex-1 sm:flex-initial">
                                <input type="text" 
                                       id="searchSuratJalan" 
                                       placeholder="Cari nomor, kenek, atau plat..." 
                                       class="block w-48 px-3 py-1.5 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <button type="button" id="clearSearch" class="inline-flex items-center justify-center px-2 py-1.5 border border-gray-300 rounded-md text-xs text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" title="Clear search">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <button type="button" id="selectAllBtn" class="inline-flex items-center px-2 py-1.5 border border-blue-300 rounded-md text-xs text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Pilih Semua
                            </button>
                            <button type="button" id="deselectAllBtn" class="inline-flex items-center px-2 py-1.5 border border-gray-300 rounded-md text-xs text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Batal Pilih
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Selected Summary -->
                <div class="bg-blue-50 border-b border-blue-200 px-3 py-2 hidden" id="selectedSummary">
                    <div class="flex items-center flex-wrap gap-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-xs text-blue-700">
                                <span id="selectedCount">0</span> surat jalan dipilih
                            </span>
                        </div>
                        <div class="text-xs text-blue-700">
                            <span id="selectedKenekCount">0</span> kenek terlibat
                        </div>
                        <div class="text-xs text-blue-700">
                            Total Uang Kenek: <span class="font-semibold text-indigo-600" id="totalUangKenek">Rp 0</span>
                        </div>
                        <div class="text-xs text-blue-700">
                            Total Utang: <span class="font-semibold text-red-600" id="totalUtangSummary">Rp 0</span>
                        </div>
                        <div class="text-xs text-blue-700">
                            Total Tabungan: <span class="font-semibold text-green-600" id="totalTabunganSummary">Rp 0</span>
                        </div>
                        <div class="text-xs text-blue-700">
                            Grand Total: <span class="font-semibold text-purple-600" id="grandTotalSummary">Rp 0</span>
                        </div>
                    </div>
                </div>

                @if($suratJalans->isEmpty())
                <div class="p-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Tidak ada surat jalan tersedia</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Surat jalan harus memiliki kenek, status approved, dan belum dibuat pranotanya.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="overflow-x-auto max-h-60">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-20">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                                           id="selectAllCheckbox">
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kenek</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Plat</th>
                                <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Uang Rit Kenek (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($suratJalans as $suratJalan)
                            <tr class="hover:bg-gray-50 transition-colors duration-200 surat-jalan-row"
                                data-nomor="{{ strtolower($suratJalan->no_surat_jalan) }}"
                                data-kenek="{{ strtolower($suratJalan->kenek) }}"
                                data-plat="{{ strtolower($suratJalan->no_plat) }}">
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <input type="checkbox" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded surat-jalan-checkbox" 
                                           name="surat_jalan_data[{{ $suratJalan->id }}][selected]" 
                                           value="1" 
                                           id="sj_{{ $suratJalan->id }}"
                                           data-id="{{ $suratJalan->id }}"
                                           data-kenek_nama="{{ $suratJalan->kenek }}">
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-xs font-medium text-gray-900">{{ $suratJalan->no_surat_jalan }}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">
                                    {{ $suratJalan->tanggal_surat_jalan ? \Carbon\Carbon::parse($suratJalan->tanggal_surat_jalan)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">{{ $suratJalan->kenek }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">{{ $suratJalan->no_plat }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-right">
                                    <input type="number" 
                                           class="w-24 px-2 py-1 text-right text-xs border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors uang-rit-kenek-input" 
                                           name="surat_jalan_data[{{ $suratJalan->id }}][uang_rit_kenek]" 
                                           value="{{ old('surat_jalan_data.'.$suratJalan->id.'.uang_rit_kenek', $suratJalan->uang_rit_kenek ?? 50000) }}" 
                                           min="0" 
                                           step="1000">
                                </td>
                            </tr>
                            
                            <!-- Hidden fields for selected data -->
                            <input type="hidden" 
                                   name="surat_jalan_data[{{ $suratJalan->id }}][no_surat_jalan]" 
                                   value="{{ $suratJalan->no_surat_jalan }}">
                            <input type="hidden" 
                                   name="surat_jalan_data[{{ $suratJalan->id }}][supir_nama]" 
                                   value="{{ $suratJalan->supir ?? '' }}">
                            <input type="hidden" 
                                   name="surat_jalan_data[{{ $suratJalan->id }}][kenek_nama]" 
                                   value="{{ $suratJalan->kenek }}">
                            <input type="hidden" 
                                   name="surat_jalan_data[{{ $suratJalan->id }}][no_plat]" 
                                   value="{{ $suratJalan->no_plat }}">
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100 border-t-2 border-gray-300">
                            <!-- Grand Total Per Person (will be populated by JavaScript) -->
                            <tbody id="grandTotalPerPerson" class="bg-yellow-50 border-t-2 border-yellow-300 hidden">
                                <!-- Dynamic content will be inserted here -->
                            </tbody>
                            <!-- Overall Grand Total -->
                            <tr class="font-semibold text-gray-800 bg-gray-200">
                                <td class="px-2 py-2 text-xs" colspan="5">🚛 Total Surat Jalan Dipilih</td>
                                <td class="px-2 py-2 text-right text-xs" id="grandTotalSuratJalan">0</td>
                            </tr>
                            <tr class="font-semibold text-gray-800 bg-gray-200">
                                <td class="px-2 py-2 text-xs" colspan="5">👥 Total Kenek Terlibat</td>
                                <td class="px-2 py-2 text-right text-xs" id="grandTotalKenek">0</td>
                            </tr>
                            <tr class="font-semibold text-gray-800 bg-gray-200">
                                <td class="px-2 py-2 text-xs" colspan="5">💰 Grand Total Uang Kenek</td>
                                <td class="px-2 py-2 text-right text-xs" id="grandTotalUangKenek">Rp 0</td>
                            </tr>
                            <tr class="font-semibold text-gray-800 bg-gray-200">
                                <td class="px-2 py-2 text-xs" colspan="5">💳 Grand Total Hutang</td>
                                <td class="px-2 py-2 text-right text-xs" id="grandTotalUtang">Rp 0</td>
                            </tr>
                            <tr class="font-semibold text-gray-800 bg-gray-200">
                                <td class="px-2 py-2 text-xs" colspan="5">💰 Grand Total Tabungan</td>
                                <td class="px-2 py-2 text-right text-xs" id="grandTotalTabungan">Rp 0</td>
                            </tr>
                            <tr class="font-semibold text-gray-800 bg-purple-200">
                                <td class="px-2 py-2 text-xs" colspan="5">💎 Grand Total Keseluruhan</td>
                                <td class="px-2 py-2 text-right text-xs" id="grandTotalKeseluruhan">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                    <p class="text-xs text-gray-600">
                        * Pilih surat jalan dan masukkan nominal uang kenek, hutang, dan tabungan untuk setiap surat jalan yang dipilih.
                        <br>* <strong>Grand Total = Uang Kenek - Hutang - Tabungan</strong> (Hutang dan Tabungan mengurangi total yang diterima kenek)
                    </p>
                </div>

                @error('surat_jalan_data')
                    <div class="bg-red-50 px-3 py-2 border-t border-red-200">
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    </div>
                @enderror
                @endif
            </div>

            <!-- Submit Button -->
            @if($suratJalans->count() > 0)
                <div class="flex flex-col sm:flex-row justify-end gap-2">
                    <a href="{{ route('pranota-uang-kenek.index') }}"
                       class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            id="submitBtn"
                            disabled>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Buat Pranota Uang Kenek
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const suratJalanCheckboxes = document.querySelectorAll('.surat-jalan-checkbox');
    const uangRitKenekInputs = document.querySelectorAll('.uang-rit-kenek-input');
    const jumlahSuratJalanDisplay = document.getElementById('jumlah_surat_jalan_display');
    const jumlahKenekDisplay = document.getElementById('jumlah_kenek_display');
    const totalUangKenekDisplay = document.getElementById('total_uang_kenek_display');
    const totalUtangDisplay = document.getElementById('total_utang_display');
    const totalTabunganDisplay = document.getElementById('total_tabungan_display');
    const grandTotalDisplay = document.getElementById('grand_total_display');
    const submitBtn = document.getElementById('submitBtn');
    const selectedSummary = document.getElementById('selectedSummary');
    const selectedCount = document.getElementById('selectedCount');
    const totalUangKenek = document.getElementById('totalUangKenek');

    function updateTotals() {
        let totalRit = 0;
        let count = 0;
        let personTotals = {}; // Object to store totals per person

        // Update individual grand totals for all rows (checked and unchecked)
        suratJalanCheckboxes.forEach((checkbox, index) => {
            if (checkbox.checked) {
                count++;
                
                const uangRitKenekInput = uangRitKenekInputs[index];
                
                // Get person name from data attribute
                const personName = checkbox.dataset.kenek_nama || 'Tanpa Nama';
                
                // Initialize person totals if not exists
                if (!personTotals[personName]) {
                    personTotals[personName] = {
                        count: 0,
                        uangKenek: 0
                    };
                }
                
                personTotals[personName].count++;
                
                if (uangRitKenekInput) {
                    const rowUangKenek = parseFloat(uangRitKenekInput.value) || 0;
                    totalRit += rowUangKenek;
                    personTotals[personName].uangKenek += rowUangKenek;
                }
            }
        });

        // Update displays
        if (jumlahSuratJalanDisplay) {
            jumlahSuratJalanDisplay.value = count + ' surat jalan';
        }
        
        if (jumlahKenekDisplay) {
            jumlahKenekDisplay.value = Object.keys(personTotals).length + ' kenek';
        }
        
        if (totalUangKenekDisplay) {
            totalUangKenekDisplay.value = 'Rp ' + totalRit.toLocaleString('id-ID');
        }

        // Update summary
        if (selectedCount && totalUangKenek) {
            selectedCount.textContent = count;
            totalUangKenek.textContent = 'Rp ' + totalRit.toLocaleString('id-ID');
        }

        // Update kenek count in summary
        const selectedKenekCount = document.getElementById('selectedKenekCount');
        if (selectedKenekCount) {
            selectedKenekCount.textContent = Object.keys(personTotals).length;
        }

        // Update per person totals
        updatePersonTotals(personTotals);

        // Show/hide summary and enable/disable submit button
        if (count > 0) {
            if (selectedSummary) selectedSummary.classList.remove('hidden');
            if (submitBtn) submitBtn.disabled = false;
        } else {
            if (selectedSummary) selectedSummary.classList.add('hidden');
            if (submitBtn) submitBtn.disabled = true;
        }

        return { totalRit, count, personTotals };
    }

    function updatePersonTotals(personTotals) {
        const grandTotalPerPersonContainer = document.getElementById('grandTotalPerPerson');
        
        if (!grandTotalPerPersonContainer) return;

        // Clear existing content
        grandTotalPerPersonContainer.innerHTML = '';

        // Check if there are any person totals to display
        const hasPersonTotals = Object.keys(personTotals).length > 0;
        
        if (hasPersonTotals) {
            grandTotalPerPersonContainer.classList.remove('hidden');
            
            // Add header row for per person totals
            const headerRow = document.createElement('tr');
            headerRow.className = 'bg-yellow-100 font-semibold text-gray-700';
            headerRow.innerHTML = `
                <td class="px-2 py-2 text-xs font-bold" colspan="6">
                    📊 TOTAL PER KENEK
                </td>
            `;
            grandTotalPerPersonContainer.appendChild(headerRow);

            // Add sub-header row for input columns
            const subHeaderRow = document.createElement('tr');
            subHeaderRow.className = 'bg-yellow-50 font-medium text-gray-600';
            subHeaderRow.innerHTML = `
                <td class="px-2 py-2 text-xs" colspan="2">Nama Kenek</td>
                <td class="px-2 py-2 text-xs text-center">Jumlah SJ</td>
                <td class="px-2 py-2 text-xs text-center">-</td>
                <td class="px-2 py-2 text-xs text-center">Total Uang</td>
                <td class="px-2 py-2 text-xs">
                    <div class="flex gap-1">
                        <div class="w-16 text-center text-red-600 font-medium text-xs">Hutang</div>
                        <div class="w-16 text-center text-green-600 font-medium text-xs">Tabungan</div>
                        <div class="w-20 text-center text-purple-600 font-medium text-xs">Total</div>
                    </div>
                </td>
            `;
            grandTotalPerPersonContainer.appendChild(subHeaderRow);

            // Sort persons alphabetically
            const sortedPersons = Object.keys(personTotals).sort();

            // Create rows for each person
            sortedPersons.forEach(personName => {
                const totals = personTotals[personName];
                
                const personRow = document.createElement('tr');
                personRow.className = 'bg-yellow-50 text-gray-700 border-t border-yellow-200';
                
                personRow.innerHTML = `
                    <td class="px-2 py-2 text-xs font-medium" colspan="2">
                        👤 ${personName}
                    </td>
                    <td class="px-2 py-2 text-xs text-center">
                        ${totals.count} surat jalan
                    </td>
                    <td class="px-2 py-2 text-xs text-center">
                        -
                    </td>
                    <td class="px-2 py-2 text-right text-xs font-semibold text-indigo-600">
                        Rp ${totals.uangKenek.toLocaleString('id-ID')}
                    </td>
                    <td class="px-2 py-2 text-right text-xs">
                        <div class="flex gap-1">
                            <input type="number" 
                                   class="person-utang-input w-16 px-1 py-1 text-xs border border-red-300 rounded focus:ring-1 focus:ring-red-500 focus:border-red-500 text-right"
                                   placeholder="Hutang" 
                                   value="0"
                                   min="0" 
                                   step="1000"
                                   data-person="${personName}">
                            <input type="number" 
                                   class="person-tabungan-input w-16 px-1 py-1 text-xs border border-green-300 rounded focus:ring-1 focus:ring-green-500 focus:border-green-500 text-right"
                                   placeholder="Tabungan" 
                                   value="0"
                                   min="0" 
                                   step="1000"
                                   data-person="${personName}">
                            <div class="person-grand-total w-20 px-1 py-1 text-xs bg-purple-50 border border-purple-200 rounded text-right font-semibold text-purple-700"
                                 data-person="${personName}">
                                Rp ${totals.uangKenek.toLocaleString('id-ID')}
                            </div>
                        </div>
                    </td>
                `;
                
                grandTotalPerPersonContainer.appendChild(personRow);
            });
            
            // Add event listeners for person-level inputs
            addPersonInputListeners();
        } else {
            grandTotalPerPersonContainer.classList.add('hidden');
        }
    }

    function addPersonInputListeners() {
        const personUtangInputs = document.querySelectorAll('.person-utang-input');
        const personTabunganInputs = document.querySelectorAll('.person-tabungan-input');
        
        personUtangInputs.forEach(input => {
            input.addEventListener('input', updatePersonGrandTotals);
        });
        
        personTabunganInputs.forEach(input => {
            input.addEventListener('input', updatePersonGrandTotals);
        });
    }
    
    function updatePersonGrandTotals() {
        const personUtangInputs = document.querySelectorAll('.person-utang-input');
        const personTabunganInputs = document.querySelectorAll('.person-tabungan-input');
        const personGrandTotals = document.querySelectorAll('.person-grand-total');
        
        // Calculate grand totals for each person
        personGrandTotals.forEach(grandTotalDiv => {
            const personName = grandTotalDiv.dataset.person;
            
            // Find corresponding inputs
            const utangInput = document.querySelector(`.person-utang-input[data-person="${personName}"]`);
            const tabunganInput = document.querySelector(`.person-tabungan-input[data-person="${personName}"]`);
            
            // Get person's total uang kenek from checked checkboxes
            let personUangKenek = 0;
            suratJalanCheckboxes.forEach((checkbox, index) => {
                if (checkbox.checked && checkbox.dataset.kenek_nama === personName) {
                    const uangRitKenekInput = uangRitKenekInputs[index];
                    if (uangRitKenekInput) {
                        personUangKenek += parseFloat(uangRitKenekInput.value) || 0;
                    }
                }
            });
            
            const utang = utangInput ? (parseFloat(utangInput.value) || 0) : 0;
            const tabungan = tabunganInput ? (parseFloat(tabunganInput.value) || 0) : 0;
            
            // Calculate grand total: Uang Kenek - Hutang - Tabungan
            const grandTotal = personUangKenek - utang - tabungan;
            grandTotalDiv.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
        });
        
        // Update overall totals
        updateOverallTotals();
    }
    
    function updateOverallTotals() {
        const personUtangInputs = document.querySelectorAll('.person-utang-input');
        const personTabunganInputs = document.querySelectorAll('.person-tabungan-input');
        
        let totalUtang = 0;
        let totalTabungan = 0;
        let totalUangKenek = 0;
        
        // Calculate total uang kenek from checked checkboxes
        suratJalanCheckboxes.forEach((checkbox, index) => {
            if (checkbox.checked) {
                const uangRitKenekInput = uangRitKenekInputs[index];
                if (uangRitKenekInput) {
                    totalUangKenek += parseFloat(uangRitKenekInput.value) || 0;
                }
            }
        });
        
        // Calculate total hutang and tabungan from person inputs
        personUtangInputs.forEach(input => {
            totalUtang += parseFloat(input.value) || 0;
        });
        
        personTabunganInputs.forEach(input => {
            totalTabungan += parseFloat(input.value) || 0;
        });
        
        const overallGrandTotal = totalUangKenek - totalUtang - totalTabungan;
        
        // Update display elements
        if (totalUtangDisplay) {
            totalUtangDisplay.value = 'Rp ' + totalUtang.toLocaleString('id-ID');
        }
        
        if (totalTabunganDisplay) {
            totalTabunganDisplay.value = 'Rp ' + totalTabungan.toLocaleString('id-ID');
        }
        
        if (grandTotalDisplay) {
            grandTotalDisplay.value = 'Rp ' + overallGrandTotal.toLocaleString('id-ID');
        }
        
        // Update summary
        const totalUtangSummary = document.getElementById('totalUtangSummary');
        const totalTabunganSummary = document.getElementById('totalTabunganSummary');
        const grandTotalSummary = document.getElementById('grandTotalSummary');

        if (totalUtangSummary) {
            totalUtangSummary.textContent = 'Rp ' + totalUtang.toLocaleString('id-ID');
        }

        if (totalTabunganSummary) {
            totalTabunganSummary.textContent = 'Rp ' + totalTabungan.toLocaleString('id-ID');
        }
        
        if (grandTotalSummary) {
            grandTotalSummary.textContent = 'Rp ' + overallGrandTotal.toLocaleString('id-ID');
        }

        // Update footer totals
        const grandTotalUangKenek = document.getElementById('grandTotalUangKenek');
        const grandTotalUtang = document.getElementById('grandTotalUtang');
        const grandTotalTabungan = document.getElementById('grandTotalTabungan');
        const grandTotalKeseluruhan = document.getElementById('grandTotalKeseluruhan');

        if (grandTotalUangKenek) {
            grandTotalUangKenek.textContent = 'Rp ' + totalUangKenek.toLocaleString('id-ID');
        }

        if (grandTotalUtang) {
            grandTotalUtang.textContent = 'Rp ' + totalUtang.toLocaleString('id-ID');
        }

        if (grandTotalTabungan) {
            grandTotalTabungan.textContent = 'Rp ' + totalTabungan.toLocaleString('id-ID');
        }
        
        if (grandTotalKeseluruhan) {
            grandTotalKeseluruhan.textContent = 'Rp ' + overallGrandTotal.toLocaleString('id-ID');
        }
    }

    // Checkbox change events
    suratJalanCheckboxes.forEach((checkbox, index) => {
        checkbox.addEventListener('change', function() {
            updateTotals();
            updateSelectAllState();
        });
    });

    // Uang rit input change events
    uangRitKenekInputs.forEach(input => {
        input.addEventListener('input', updateTotals);
    });

    function updateSelectAllState() {
        if (!selectAllCheckbox) return;

        const visibleCheckboxes = document.querySelectorAll('.surat-jalan-checkbox:not([style*="display: none"])');
        const checkedVisibleCheckboxes = document.querySelectorAll('.surat-jalan-checkbox:not([style*="display: none"]):checked');

        if (visibleCheckboxes.length === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedVisibleCheckboxes.length === visibleCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedVisibleCheckboxes.length > 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            const visibleCheckboxes = document.querySelectorAll('.surat-jalan-checkbox:not([style*="display: none"])');
            visibleCheckboxes.forEach((checkbox, globalIndex) => {
                checkbox.checked = this.checked;
            });
            updateTotals();
        });
    }

    // Select all button
    const selectAllBtn = document.getElementById('selectAllBtn');
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            const visibleCheckboxes = document.querySelectorAll('.surat-jalan-checkbox:not([style*="display: none"])');
            visibleCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            updateTotals();
            updateSelectAllState();
        });
    }

    // Deselect all button
    const deselectAllBtn = document.getElementById('deselectAllBtn');
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function() {
            const visibleCheckboxes = document.querySelectorAll('.surat-jalan-checkbox:not([style*="display: none"])');
            visibleCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateTotals();
            updateSelectAllState();
        });
    }

    // Search functionality
    const searchInput = document.getElementById('searchSuratJalan');
    const clearSearchBtn = document.getElementById('clearSearch');
    const tableRows = document.querySelectorAll('tr.surat-jalan-row');

    let searchTimeout;
    function debounceSearch(searchTerm) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterTable(searchTerm);
        }, 300);
    }

    function filterTable(searchTerm) {
        const term = searchTerm.toLowerCase().trim();
        let visibleCount = 0;
        const searchResultsEl = document.getElementById('searchResults');

        tableRows.forEach(row => {
            const nomor = row.dataset.nomor || '';
            const kenek = row.dataset.kenek || '';
            const plat = row.dataset.plat || '';

            const isVisible = nomor.includes(term) ||
                            kenek.includes(term) ||
                            plat.includes(term);

            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        // Update search results count
        if (searchResultsEl) {
            if (term === '') {
                searchResultsEl.classList.add('hidden');
            } else {
                searchResultsEl.textContent = `${visibleCount} surat jalan ditemukan`;
                searchResultsEl.classList.remove('hidden');
            }
        }

        updateSelectAllState();
        updateTotals();
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            debounceSearch(this.value);
        });
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            if (searchInput) {
                searchInput.value = '';
                filterTable('');
                searchInput.focus();
            }
        });
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+F or Cmd+F to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        // Escape to clear search
        if (e.key === 'Escape' && document.activeElement === searchInput) {
            searchInput.value = '';
            filterTable('');
        }
    });

    // Form validation
    const pranotaForm = document.getElementById('pranotaForm');
    if (pranotaForm) {
        pranotaForm.addEventListener('submit', function(e) {
            const checkedCheckboxes = document.querySelectorAll('.surat-jalan-checkbox:checked');
            if (checkedCheckboxes.length === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal satu surat jalan untuk membuat pranota uang kenek.');
                return false;
            }

            // Check if all selected items have valid values
            let hasEmptyValues = false;
            checkedCheckboxes.forEach(checkbox => {
                const uangRitKenekInput = document.querySelector(`input[name*="[${checkbox.dataset.id}][uang_rit_kenek]"]`);
                
                if (uangRitKenekInput && (!uangRitKenekInput.value || parseFloat(uangRitKenekInput.value) < 0)) {
                    hasEmptyValues = true;
                }
            });

            if (hasEmptyValues) {
                e.preventDefault();
                alert('Silakan masukkan nilai yang valid untuk uang kenek pada semua surat jalan yang dipilih.');
                return false;
            }

            // Collect hutang and tabungan data per kenek
            collectKenekDetailsData();
        });
    }

    function collectKenekDetailsData() {
        const kenekDetailsContainer = document.getElementById('kenekDetailsInputs');
        const personUtangInputs = document.querySelectorAll('.person-utang-input');
        const personTabunganInputs = document.querySelectorAll('.person-tabungan-input');
        
        // Clear existing inputs
        kenekDetailsContainer.innerHTML = '';
        
        // Create hidden inputs for each kenek's hutang and tabungan
        personUtangInputs.forEach(input => {
            const kenekNama = input.dataset.person;
            const hutangValue = input.value || 0;
            
            const hutangInput = document.createElement('input');
            hutangInput.type = 'hidden';
            hutangInput.name = `kenek_details[${kenekNama}][hutang]`;
            hutangInput.value = hutangValue;
            kenekDetailsContainer.appendChild(hutangInput);
        });
        
        personTabunganInputs.forEach(input => {
            const kenekNama = input.dataset.person;
            const tabunganValue = input.value || 0;
            
            const tabunganInput = document.createElement('input');
            tabunganInput.type = 'hidden';
            tabunganInput.name = `kenek_details[${kenekNama}][tabungan]`;
            tabunganInput.value = tabunganValue;
            kenekDetailsContainer.appendChild(tabunganInput);
        });
    }

    // Initialize
    updateTotals();
    updateSelectAllState();
});
</script>
@endpush