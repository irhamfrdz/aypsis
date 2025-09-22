@extends('layouts.app')

@section('title', 'Form Pranota Supir')
@section('page_title', 'Form Pranota Supir')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-4 max-w-6xl mx-auto">
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
                    <h3 class="text-sm font-semibold text-gray-800">Filter Memo Permohonan</h3>
                </div>
                <form action="{{ route('pranota-supir.create') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
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
                        <a href="{{ route('pranota-supir.create') }}" class="inline-flex justify-center py-1.5 px-3 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <form id="pranotaForm" action="{{ route('pranota-supir.store') }}" method="POST" class="space-y-3">
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
                                    <input type="text" id="nomor_pranota_display" class="{{ $readonlyInputClasses }}" value="{{ $nomor_pranota_display }}" readonly>
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
                                <label for="total_biaya_memo_display" class="{{ $labelClasses }}">Total Memo</label>
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

            <!-- Pilih Memo Permohonan -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <h4 class="text-sm font-semibold text-gray-800">Pilih Memo Permohonan</h4>
                            <span id="searchResults" class="text-xs text-gray-500 hidden"></span>
                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            <div class="relative flex-1 sm:flex-initial">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" id="searchInput" placeholder="Cari nomor memo, kegiatan, atau supir... (Ctrl+F)" class="pl-8 pr-3 py-1.5 border border-gray-300 rounded-md text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-64 transition-colors" title="Tekan Ctrl+F untuk fokus search, ESC untuk clear">
                            </div>
                            <button type="button" id="clearSearch" class="inline-flex items-center justify-center px-2 py-1.5 border border-gray-300 rounded-md text-xs text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" title="Clear search">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto max-h-60">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-20">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all" class="h-3 w-3 text-indigo-600 border-gray-300 rounded">
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Memo</th>
                                <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Memo</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Plat</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($permohonans as $permohonan)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <input type="checkbox" name="permohonan_ids[]" value="{{ $permohonan->id }}" data-biaya="{{ $permohonan->total_harga_setelah_adj }}" class="permohonan-checkbox h-3 w-3 text-indigo-600 border-gray-300 rounded">
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">{{ $permohonan->nomor_memo }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs text-center">{{ $permohonan->tanggal_memo ? \Carbon\Carbon::parse($permohonan->tanggal_memo)->format('d/m/Y') : '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $kegiatanMap[$permohonan->kegiatan] ?? ucfirst($permohonan->kegiatan) }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $permohonan->supir->nama_lengkap ?? $permohonan->supir->nama_panggilan ?? '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $permohonan->plat_nomor ?? '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold">Rp {{ number_format($permohonan->total_harga_setelah_adj, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-2 py-4 text-center text-xs text-gray-500">
                                        Tidak ada memo yang tersedia. Pastikan memo sudah di-approve.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                    <p class="text-xs text-gray-600">
                        * Hanya memo yang sudah di-approve (status Selesai/Bermasalah) dan belum memiliki pranota.
                    </p>
                </div>
            </div>

            <!-- Informasi Tambahan & Submit -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                <div class="lg:col-span-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label for="alasan_adjustment" class="{{ $labelClasses }}">Alasan Adjustment</label>
                            <textarea name="alasan_adjustment" id="alasan_adjustment" rows="2" class="{{ $inputClasses }}" placeholder="Jelaskan alasan adjustment..."></textarea>
                        </div>
                        <div>
                            <label for="catatan" class="{{ $labelClasses }}">Catatan</label>
                            <textarea name="catatan" id="catatan" rows="2" class="{{ $inputClasses }}" placeholder="Tambahkan catatan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col justify-end">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Buat Pranota
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Nomor Cetakan & Nomor Pranota Otomatis
            const nomorCetakanInput = document.getElementById('nomor_cetakan');
            const nomorPranotaDisplay = document.getElementById('nomor_pranota_display');
            nomorCetakanInput.addEventListener('input', updateNomorPranota);

            function updateNomorPranota() {
                // Ambil format dari value awal
                let format = nomorPranotaDisplay.value;
                // Ganti nomor cetakan di format
                let parts = format.split('-');
                if(parts.length === 5) {
                    parts[1] = nomorCetakanInput.value;
                    nomorPranotaDisplay.value = parts.join('-');
                }
            }

            const selectAllCheckbox = document.getElementById('select-all');
            const permohonanCheckboxes = document.querySelectorAll('.permohonan-checkbox');
            const totalBiayaMemoDisplay = document.getElementById('total_biaya_memo_display');
            const adjustmentInput = document.getElementById('adjustment');
            const totalBiayaPranotaDisplay = document.getElementById('total_biaya_pranota_display');

            function updateTotalBiayaMemo() {
                let total = 0;
                permohonanCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        total += parseFloat(checkbox.dataset.biaya) || 0;
                    }
                });
                totalBiayaMemoDisplay.value = 'Rp ' + total.toLocaleString('id-ID');
                return total;
            }

            function updateTotalBiayaPranota() {
                const totalMemo = updateTotalBiayaMemo();
                const adjustment = parseFloat(adjustmentInput.value) || 0;
                const total = totalMemo + adjustment;
                totalBiayaPranotaDisplay.value = 'Rp ' + total.toLocaleString('id-ID');
            }

            selectAllCheckbox.addEventListener('change', function () {
                const visibleCheckboxes = document.querySelectorAll('.permohonan-checkbox:not([style*="display: none"])');
                visibleCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateTotalBiayaPranota();
            });

            permohonanCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateTotalBiayaPranota);
            });

            adjustmentInput.addEventListener('input', updateTotalBiayaPranota);

            // Tambahkan validasi untuk memastikan minimal satu memo dipilih
            const pranotaForm = document.getElementById('pranotaForm');
            pranotaForm.addEventListener('submit', function(e) {
                const checkedCheckboxes = document.querySelectorAll('.permohonan-checkbox:checked');
                if (checkedCheckboxes.length === 0) {
                    e.preventDefault();
                    alert('Silakan pilih minimal satu memo permohonan.');
                    return false;
                }
            });

            updateTotalBiayaPranota();
        });

        // Search functionality with debouncing
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        const tableBody = document.querySelector('tbody');
        const tableRows = tableBody.querySelectorAll('tr');

        let searchTimeout;
        function debounceSearch(searchTerm) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterTable(searchTerm);
            }, 300); // 300ms delay
        }

        function filterTable(searchTerm) {
            const term = searchTerm.toLowerCase().trim();
            let visibleCount = 0;
            const searchResultsEl = document.getElementById('searchResults');

            tableRows.forEach(row => {
                // Skip the "no data" row
                if (row.cells.length === 1 && row.textContent.includes('Tidak ada memo')) {
                    return;
                }

                const cells = row.querySelectorAll('td');
                let isVisible = false;

                // Search in relevant columns (No. Memo, Tanggal Memo, Kegiatan, Supir, No. Plat)
                for (let i = 1; i < cells.length - 1; i++) { // Skip checkbox and total biaya columns
                    const cellText = cells[i].textContent.toLowerCase();
                    if (cellText.includes(term)) {
                        isVisible = true;
                        break;
                    }
                }

                row.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount++;
            });

            // Update search results count
            if (term === '') {
                searchResultsEl.classList.add('hidden');
            } else {
                searchResultsEl.textContent = `${visibleCount} memo ditemukan`;
                searchResultsEl.classList.remove('hidden');
            }

            // Show/hide "no results" message
            let noResultsRow = tableBody.querySelector('.no-results-row');
            if (visibleCount === 0 && term !== '') {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.className = 'no-results-row';
                    noResultsRow.innerHTML = `
                        <td colspan="7" class="px-2 py-8 text-center text-sm text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <p>Tidak ada memo yang cocok dengan "<span class="font-medium">${searchTerm}</span>"</p>
                                <p class="text-xs mt-1">Coba kata kunci yang berbeda</p>
                            </div>
                        </td>
                    `;
                    tableBody.appendChild(noResultsRow);
                }
            } else {
                if (noResultsRow) {
                    noResultsRow.remove();
                }
            }

            // Update select all checkbox state
            updateSelectAllState();
        }

        function updateSelectAllState() {
            const visibleCheckboxes = document.querySelectorAll('.permohonan-checkbox:not([style*="display: none"])');
            const checkedVisibleCheckboxes = document.querySelectorAll('.permohonan-checkbox:not([style*="display: none"]):checked');
            const selectAllCheckbox = document.getElementById('select-all');

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

        // Search input event listeners with debouncing
        searchInput.addEventListener('input', function() {
            debounceSearch(this.value);
        });

        // Clear search button
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterTable('');
            searchInput.focus();
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+F or Cmd+F to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
            // Escape to clear search
            if (e.key === 'Escape' && document.activeElement === searchInput) {
                searchInput.value = '';
                filterTable('');
            }
        });

        // Update select all when individual checkboxes change (with search filter consideration)
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('permohonan-checkbox')) {
                updateSelectAllState();
            }
        });
    </script>
@endsection
