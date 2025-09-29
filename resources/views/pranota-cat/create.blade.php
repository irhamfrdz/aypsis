@extends('layouts.app')

@section('title', 'Form Pranota Tagihan CAT')
@section('page_title', 'Form Pranota Tagihan CAT')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-4 max-w-6xl mx-auto">
        {{-- Notifikasi --}}
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
                <p class="font-bold">Sukses</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
                <p class="font-bold">Peringatan</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif
        @php
            // Definisikan kelas Tailwind untuk input yang lebih compact
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors";
            $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-sm p-2";
            $labelClasses = "block text-xs font-medium text-gray-700 mb-1";
        @endphp

        <!-- Header dengan Filter Tanggal (Compact) -->
        <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Filter Item Tagihan CAT</h3>
                </div>
                <form action="{{ route('pranota-cat.create') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                    <div class="flex gap-2">
                        <div class="min-w-0">
                            <label for="start_date" class="{{ $labelClasses }}">Dari</label>
                            <input type="date" name="start_date" id="start_date" class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors" value="{{ $start_date ?? '' }}">
                        </div>
                        <div class="min-w-0">
                            <label for="end_date" class="{{ $labelClasses }}">Sampai</label>
                            <input type="date" name="end_date" id="end_date" class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors" value="{{ $end_date ?? '' }}">
                        </div>
                    </div>
                    <div class="flex gap-1 sm:self-end">
                        <button type="submit" class="inline-flex justify-center py-1.5 px-3 border border-transparent shadow-sm text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Cari
                        </button>
                        <a href="{{ route('pranota-cat.create') }}" class="inline-flex justify-center py-1.5 px-3 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <form id="pranotaForm" action="{{ route('pranota-cat.store') }}" method="POST" class="space-y-3">
            @csrf

            <!-- Data Pranota & Total Biaya dalam satu baris -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Data Pranota -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Data Pranota</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div class="flex items-end gap-1">
                                <div class="flex-1">
                                    <label for="nomor_pranota_display" class="{{ $labelClasses }}">Nomor Pranota</label>
                                    <input type="text" id="nomor_pranota_display" class="{{ $readonlyInputClasses }}" value="{{ $nomor_pranota_display ?? 'PTK-' . date('Ymd') . '-001' }}" readonly>
                                </div>
                                <div class="w-16">
                                    <label for="nomor_cetakan" class="{{ $labelClasses }}">Cetak</label>
                                    <input type="number" min="1" id="nomor_cetakan" name="nomor_cetakan" value="{{ $nomor_cetakan ?? 1 }}" class="{{ $inputClasses }}">
                                </div>
                            </div>
                            <div>
                                <label for="tanggal_pranota" class="{{ $labelClasses }}">Tanggal</label>
                                <input type="text" name="tanggal_pranota" id="tanggal_pranota" class="{{ $readonlyInputClasses }}" value="{{ now()->format('d/M/Y') }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Biaya -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Total Biaya</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label for="total_biaya_memo_display" class="{{ $labelClasses }}">Total Item</label>
                                <input type="text" id="total_biaya_memo_display" class="{{ $readonlyInputClasses }}" readonly>
                            </div>
                            <div>
                                <label for="adjustment" class="{{ $labelClasses }}">Adjustment</label>
                                <input type="number" name="adjustment" id="adjustment" class="{{ $inputClasses }}" value="0" step="0.01">
                            </div>
                            <div>
                                <label for="total_biaya_pranota_display" class="{{ $labelClasses }}">Total Pranota</label>
                                <input type="text" id="total_biaya_pranota_display" class="{{ $readonlyInputClasses }} font-bold text-gray-800 bg-gray-100" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Item Tagihan CAT -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-gray-800">Item Tagihan CAT</h4>
                        <div class="flex items-center gap-2">
                            <button type="button" id="selectAllBtn" class="inline-flex justify-center py-1.5 px-3 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Pilih Semua
                            </button>
                            <button type="button" id="clearSelectionBtn" class="inline-flex justify-center py-1.5 px-3 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Hapus Pilihan
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="selectAll" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontainer</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tagihanCats as $tagihan)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="tagihan_ids[]" value="{{ $tagihan->id }}" class="item-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" data-biaya="{{ $tagihan->realisasi_biaya ?? $tagihan->estimasi_biaya ?? 0 }}">
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $tagihan->nomor_kontainer ?? 'N/A' }}
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $tagihan->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs truncate" title="{{ $tagihan->deskripsi_pekerjaan ?? 'N/A' }}">
                                            {{ $tagihan->deskripsi_pekerjaan ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Rp {{ number_format($tagihan->realisasi_biaya ?? $tagihan->estimasi_biaya ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $tagihan->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500">
                                        Tidak ada tagihan CAT yang belum masuk pranota
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form Tambahan -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Informasi Tambahan</h4>
                    <div class="space-y-2">
                        <div>
                            <label for="supplier" class="{{ $labelClasses }}">Supplier</label>
                            <input type="text" name="supplier" id="supplier" class="{{ $inputClasses }}" placeholder="Masukkan nama supplier">
                        </div>
                        <div>
                            <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="3" class="{{ $inputClasses }}" placeholder="Masukkan keterangan tambahan"></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Ringkasan</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Jumlah Item Terpilih:</span>
                            <span id="selectedCount">0</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Total Biaya Item:</span>
                            <span id="totalBiayaItem">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Adjustment:</span>
                            <span id="adjustmentDisplay">Rp 0</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between text-sm font-semibold">
                            <span>Total Pranota:</span>
                            <span id="totalPranota">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('pranota-cat.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Batal
                </a>
                <button type="submit" id="submitBtn" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Buat Pranota
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show warning/alert for success or error messages
            @if(session('success'))
                alert('SUKSES: {{ session('success') }}');
            @endif

            @if(session('error'))
                alert('PERINGATAN: {{ session('error') }}');
            @endif

            const selectAllCheckbox = document.getElementById('selectAll');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            const selectAllBtn = document.getElementById('selectAllBtn');
            const clearSelectionBtn = document.getElementById('clearSelectionBtn');
            const selectedCountEl = document.getElementById('selectedCount');
            const totalBiayaItemEl = document.getElementById('totalBiayaItem');
            const adjustmentInput = document.getElementById('adjustment');
            const adjustmentDisplayEl = document.getElementById('adjustmentDisplay');
            const totalPranotaEl = document.getElementById('totalPranota');
            const totalBiayaMemoDisplay = document.getElementById('total_biaya_memo_display');
            const totalBiayaPranotaDisplay = document.getElementById('total_biaya_pranota_display');
            const submitBtn = document.getElementById('submitBtn');

            function updateCalculations() {
                const selectedItems = document.querySelectorAll('.item-checkbox:checked');
                let totalBiaya = 0;
                let selectedCount = selectedItems.length;

                selectedItems.forEach(checkbox => {
                    totalBiaya += parseFloat(checkbox.dataset.biaya) || 0;
                });

                const adjustment = parseFloat(adjustmentInput.value) || 0;
                const totalPranota = totalBiaya + adjustment;

                selectedCountEl.textContent = selectedCount;
                totalBiayaItemEl.textContent = 'Rp ' + totalBiaya.toLocaleString('id-ID');
                adjustmentDisplayEl.textContent = 'Rp ' + adjustment.toLocaleString('id-ID');
                totalPranotaEl.textContent = 'Rp ' + totalPranota.toLocaleString('id-ID');
                totalBiayaMemoDisplay.value = 'Rp ' + totalBiaya.toLocaleString('id-ID');
                totalBiayaPranotaDisplay.value = 'Rp ' + totalPranota.toLocaleString('id-ID');

                submitBtn.disabled = selectedCount === 0;
            }

            // Select All functionality
            selectAllCheckbox.addEventListener('change', function() {
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateCalculations();
            });

            selectAllBtn.addEventListener('click', function() {
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                selectAllCheckbox.checked = true;
                updateCalculations();
            });

            clearSelectionBtn.addEventListener('click', function() {
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                selectAllCheckbox.checked = false;
                updateCalculations();
            });

            // Individual checkbox changes
            itemCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(itemCheckboxes).some(cb => cb.checked);

                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;

                    updateCalculations();
                });
            });

            // Adjustment input changes
            adjustmentInput.addEventListener('input', updateCalculations);

            // Initial calculation
            updateCalculations();

            // Update nomor pranota when nomor_cetakan changes
            document.getElementById('nomor_cetakan').addEventListener('input', function() {
                const cetakan = parseInt(this.value) || 1;
                const tahun = new Date().getFullYear().toString().slice(-2);
                const bulan = (new Date().getMonth() + 1).toString().padStart(2, '0');

                // This would need AJAX call to get running number, for now just update display
                const baseNomor = `PTK${cetakan}${tahun}${bulan}`;
                document.getElementById('nomor_pranota_display').value = baseNomor + '000001'; // Placeholder
            });
        });
    </script>
@endsection
