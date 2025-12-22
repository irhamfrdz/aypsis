@extends('layouts.app')

@section('title', 'Edit Pranota Uang Kenek')

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

        <form action="{{ route('pranota-uang-rit-kenek.update', $pranotaUangRitKenek) }}" method="POST" id="pranotaForm" class="space-y-3">
            @csrf
            @method('PUT')

            <div class="bg-blue-50 border border-blue-200 p-3 rounded-md">
                <p class="text-xs text-blue-800">
                    Edit Pranota Uang Kenek <strong>{{ $pranotaUangRitKenek->no_pranota }}</strong>. 
                    Anda dapat mengubah tanggal, keterangan, dan detail per Kenek (hutang, tabungan, BPJS).
                </p>
            </div>



            <!-- Hidden inputs untuk data hutang dan tabungan per Kenek -->
            <div id="KenekDetailsInputs"></div>

            <!-- Data Pranota & Total Uang dalam satu baris -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Data Pranota -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">💰 Data Pranota Uang Kenek</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div>
                                <label for="nomor_pranota_preview" class="{{ $labelClasses }}">Nomor Pranota</label>
                                <input type="text"
                                       class="{{ $readonlyInputClasses }} font-medium text-indigo-600"
                                       id="nomor_pranota_preview"
                                       value="{{ $pranotaUangRitKenek->no_pranota }}"
                                       readonly>
                                <p class="mt-1 text-xs text-gray-500">Nomor otomatis, tidak dapat diubah</p>
                            </div>
                            <div>
                                <label for="tanggal" class="{{ $labelClasses }}">
                                    Tanggal <span class="text-red-500">*</span>
                                </label>
                                <input type="date"
                                       class="{{ $inputClasses }} @error('tanggal') border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror"
                                       id="tanggal"
                                       name="tanggal"
                                       value="{{ old('tanggal', $pranotaUangRitKenek->tanggal ? $pranotaUangRitKenek->tanggal->format('Y-m-d') : '') }}"
                                       required>
                                @error('tanggal')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-2">
                            <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                            <textarea class="{{ $inputClasses }} @error('keterangan') border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror"
                                      id="keterangan"
                                      name="keterangan"
                                      rows="2"
                                      placeholder="Keterangan tambahan (opsional)">{{ old('keterangan', $pranotaUangRitKenek->keterangan) }}</textarea>
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
                                <label for="jumlah_surat_jalan_display" class="{{ $labelClasses }}">Jumlah Surat Jalan</label>
                                <input type="text" id="jumlah_surat_jalan_display" class="{{ $readonlyInputClasses }}" value="{{ $suratJalans->count() }}" readonly>
                            </div>
                            <div>
                                <label for="jumlah_Kenek_display" class="{{ $labelClasses }}">Jumlah Kenek</label>
                                <input type="text" id="jumlah_Kenek_display" class="{{ $readonlyInputClasses }}" value="{{ $KenekDetails->count() }}" readonly>
                            </div>
                            <div>
                                <label for="total_uang_rit_display" class="{{ $labelClasses }}">Total Uang Kenek</label>
                                <input type="text" id="total_uang_rit_display" class="{{ $readonlyInputClasses }} font-bold text-indigo-600" value="Rp {{ number_format($pranotaUangRitKenek->total_uang, 0, ',', '.') }}" readonly>
                            </div>
                            <div>
                                <label for="total_utang_display" class="{{ $labelClasses }}">Total Utang</label>
                                <input type="text" id="total_utang_display" class="{{ $readonlyInputClasses }} font-bold text-red-600" value="Rp {{ number_format($pranotaUangRitKenek->total_hutang, 0, ',', '.') }}" readonly>
                            </div>
                            <div>
                                <label for="total_tabungan_display" class="{{ $labelClasses }}">Total Tabungan</label>
                                <input type="text" id="total_tabungan_display" class="{{ $readonlyInputClasses }} font-bold text-green-600" value="Rp {{ number_format($pranotaUangRitKenek->total_tabungan, 0, ',', '.') }}" readonly>
                            </div>
                            <div>
                                <label for="total_bpjs_display" class="{{ $labelClasses }}">Total BPJS</label>
                                <input type="text" id="total_bpjs_display" class="{{ $readonlyInputClasses }} font-bold text-yellow-600" value="Rp {{ number_format($pranotaUangRitKenek->total_bpjs, 0, ',', '.') }}" readonly>
                            </div>
                            <div>
                                <label for="grand_total_display" class="{{ $labelClasses }}">Grand Total</label>
                                <input type="text" id="grand_total_display" class="{{ $readonlyInputClasses }} font-bold text-purple-600" value="Rp {{ number_format($pranotaUangRitKenek->grand_total_bersih, 0, ',', '.') }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Surat Jalan -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800">🚚 Surat Jalan dalam Pranota Ini</h4>
                </div>









                <div class="overflow-x-auto max-h-60">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-20">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                                <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kenek</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Uang Kenek</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($suratJalans as $suratJalan)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">{{ $suratJalan->no_surat_jalan ?? '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs text-center">{{ $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $suratJalan->Kenek ?? '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold text-gray-700">
                                        Rp {{ number_format($suratJalan->uang_rit_Kenek ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-2 py-4 text-center text-xs text-gray-500">
                                        <div class="flex flex-col items-center py-4">
                                            <svg class="h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <p class="font-medium">Tidak ada surat jalan dalam pranota ini</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-100 border-t-2 border-gray-300">
                            <!-- Per Kenek Details (editable) -->
                            <tbody class="bg-yellow-50 border-t-2 border-yellow-300">
                                @foreach($KenekDetails as $detail)
                                <tr class="border-b border-yellow-200" data-Kenek="{{ $detail->Kenek_nama }}">
                                    <td colspan="4" class="px-2 py-2 text-xs">
                                        <div class="font-semibold text-gray-800 mb-2">{{ $detail->Kenek_nama }}</div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="text-xs text-gray-600">Uang Kenek:</label>
                                                <div class="text-xs font-semibold text-indigo-600">Rp {{ number_format($detail->total_uang_Kenek, 0, ',', '.') }}</div>
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-600">Hutang:</label>
                                                <input type="number" 
                                                       name="Kenek_details[{{ $detail->id }}][hutang]"
                                                       class="person-utang-input w-full px-2 py-1 text-xs border border-gray-300 rounded"
                                                       value="{{ $detail->hutang }}"
                                                       data-Kenek="{{ $detail->Kenek_nama }}"
                                                       min="0">
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-600">Tabungan:</label>
                                                <input type="number" 
                                                       name="Kenek_details[{{ $detail->id }}][tabungan]"
                                                       class="person-tabungan-input w-full px-2 py-1 text-xs border border-gray-300 rounded"
                                                       value="{{ $detail->tabungan }}"
                                                       data-Kenek="{{ $detail->Kenek_nama }}"
                                                       min="0">
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-600">BPJS:</label>
                                                <input type="number" 
                                                       name="Kenek_details[{{ $detail->id }}][bpjs]"
                                                       class="person-bpjs-input w-full px-2 py-1 text-xs border border-gray-300 rounded"
                                                       value="{{ $detail->bpjs }}"
                                                       data-Kenek="{{ $detail->Kenek_nama }}"
                                                       min="0">
                                            </div>
                                        </div>
                                        <input type="hidden" name="Kenek_details[{{ $detail->id }}][Kenek_nama]" value="{{ $detail->Kenek_nama }}">
                                        <input type="hidden" name="Kenek_details[{{ $detail->id }}][total_uang_Kenek]" value="{{ $detail->total_uang_Kenek }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <!-- Overall Grand Total -->
                            <tr class="font-semibold text-gray-800 bg-gray-200">
                                <td class="px-2 py-3 text-xs font-bold" colspan="3">
                                    TOTAL Uang Kenek
                                </td>
                                <td class="px-2 py-3 text-right text-xs font-bold text-indigo-600" id="grandTotalUangKenek">
                                    Rp {{ number_format($pranotaUangRitKenek->total_uang, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="font-semibold text-gray-800 bg-gray-200">
                                <td class="px-2 py-3 text-xs font-bold" colspan="3">
                                    TOTAL HUTANG
                                </td>
                                <td class="px-2 py-3 text-right text-xs font-bold text-red-600" id="grandTotalUtang">
                                    Rp {{ number_format($pranotaUangRitKenek->total_hutang, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="font-semibold text-gray-800 bg-gray-200">
                                <td class="px-2 py-3 text-xs font-bold" colspan="3">
                                    TOTAL TABUNGAN
                                </td>
                                <td class="px-2 py-3 text-right text-xs font-bold text-green-600" id="grandTotalTabungan">
                                    Rp {{ number_format($pranotaUangRitKenek->total_tabungan, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="font-semibold text-gray-800 bg-gray-200">
                                <td class="px-2 py-3 text-xs font-bold" colspan="3">
                                    TOTAL BPJS
                                </td>
                                <td class="px-2 py-3 text-right text-xs font-bold text-yellow-600" id="grandTotalBpjs">
                                    Rp {{ number_format($pranotaUangRitKenek->total_bpjs, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="font-semibold text-gray-800 bg-purple-200">
                                <td class="px-2 py-3 text-xs font-bold" colspan="3">
                                    GRAND TOTAL BERSIH
                                </td>
                                <td class="px-2 py-3 text-right text-xs font-bold text-purple-600" id="grandTotalKeseluruhan">
                                    Rp {{ number_format($pranotaUangRitKenek->grand_total_bersih, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                    <p class="text-xs text-gray-600">
                        * Ubah nilai hutang, tabungan, dan BPJS untuk setiap Kenek sesuai kebutuhan.
                        <br>* <strong>Grand Total = Uang Kenek - Hutang - Tabungan - BPJS</strong> (Hutang, Tabungan, dan BPJS mengurangi total yang diterima Kenek)
                    </p>
                </div>

                @error('Kenek_details')
                    <div class="bg-red-50 px-3 py-2 border-t border-red-200">
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    </div>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex flex-col sm:flex-row justify-end gap-2">
                <a href="{{ route('pranota-uang-rit-kenek.index') }}"
                   class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Pranota Uang Kenek
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Add event listeners to all Kenek detail inputs
    function addInputListeners() {
        const personUtangInputs = document.querySelectorAll('.person-utang-input');
        const personTabunganInputs = document.querySelectorAll('.person-tabungan-input');
        const personBpjsInputs = document.querySelectorAll('.person-bpjs-input');
        
        personUtangInputs.forEach(input => {
            input.addEventListener('input', updateOverallTotals);
        });
        
        personTabunganInputs.forEach(input => {
            input.addEventListener('input', updateOverallTotals);
        });
        
        personBpjsInputs.forEach(input => {
            input.addEventListener('input', updateOverallTotals);
        });
    }
    
    function updateOverallTotals() {
        const personUtangInputs = document.querySelectorAll('.person-utang-input');
        const personTabunganInputs = document.querySelectorAll('.person-tabungan-input');
        const personBpjsInputs = document.querySelectorAll('.person-bpjs-input');
        
        let totalUtang = 0;
        let totalTabungan = 0;
        let totalBpjs = 0;
        
        // Get total Uang Kenek from pranota data (fixed value)
        const totalUangKenek = parseFloat('{{ $pranotaUangRitKenek->total_uang }}') || 0;
        
        // Calculate totals from inputs
        personUtangInputs.forEach(input => {
            totalUtang += parseFloat(input.value) || 0;
        });
        
        personTabunganInputs.forEach(input => {
            totalTabungan += parseFloat(input.value) || 0;
        });
        
        personBpjsInputs.forEach(input => {
            totalBpjs += parseFloat(input.value) || 0;
        });
        
        // Calculate grand total bersih
        const grandTotalBersih = totalUangKenek - totalUtang - totalTabungan - totalBpjs;
        
        // Update footer totals
        const grandTotalUtang = document.getElementById('grandTotalUtang');
        const grandTotalTabungan = document.getElementById('grandTotalTabungan');
        const grandTotalBpjs = document.getElementById('grandTotalBpjs');
        const grandTotalKeseluruhan = document.getElementById('grandTotalKeseluruhan');

        if (grandTotalUtang) {
            grandTotalUtang.textContent = 'Rp ' + totalUtang.toLocaleString('id-ID');
        }

        if (grandTotalTabungan) {
            grandTotalTabungan.textContent = 'Rp ' + totalTabungan.toLocaleString('id-ID');
        }
        
        if (grandTotalBpjs) {
            grandTotalBpjs.textContent = 'Rp ' + totalBpjs.toLocaleString('id-ID');
        }
        
        if (grandTotalKeseluruhan) {
            grandTotalKeseluruhan.textContent = 'Rp ' + grandTotalBersih.toLocaleString('id-ID');
        }
        
        // Update top section displays
        const totalUtangDisplay = document.getElementById('total_utang_display');
        const totalTabunganDisplay = document.getElementById('total_tabungan_display');
        const totalBpjsDisplay = document.getElementById('total_bpjs_display');
        const grandTotalDisplay = document.getElementById('grand_total_display');
        
        if (totalUtangDisplay) {
            totalUtangDisplay.value = 'Rp ' + totalUtang.toLocaleString('id-ID');
        }
        
        if (totalTabunganDisplay) {
            totalTabunganDisplay.value = 'Rp ' + totalTabungan.toLocaleString('id-ID');
        }
        
        if (totalBpjsDisplay) {
            totalBpjsDisplay.value = 'Rp ' + totalBpjs.toLocaleString('id-ID');
        }
        
        if (grandTotalDisplay) {
            grandTotalDisplay.value = 'Rp ' + grandTotalBersih.toLocaleString('id-ID');
        }
    }

    // Initialize
    addInputListeners();
    updateOverallTotals(); // Initial calculation
});
</script>
@endpush
