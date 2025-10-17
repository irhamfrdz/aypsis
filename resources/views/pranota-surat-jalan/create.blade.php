@extends('layouts.app')

@section('title', 'Buat Pranota Surat Jalan')
@section('page_title', 'Form Pranota Surat Jalan')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-4 max-w-6xl mx-auto">
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

        <form action="{{ route('pranota-surat-jalan.store') }}" method="POST" id="pranotaForm" class="space-y-3">
            @csrf

            <!-- Data Pranota & Total Uang Jalan dalam satu baris -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Data Pranota -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Data Pranota</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label for="nomor_pranota_preview" class="{{ $labelClasses }}">Nomor Pranota</label>
                                <input type="text"
                                       class="{{ $readonlyInputClasses }} font-medium text-indigo-600"
                                       id="nomor_pranota_preview"
                                       value="Auto Generate: PSJ-{{ date('my') }}-XXXXXX"
                                       readonly>
                                <p class="mt-1 text-xs text-gray-500">Nomor otomatis saat disimpan</p>
                            </div>
                            <div>
                                <label for="tanggal_pranota" class="{{ $labelClasses }}">
                                    Tanggal Pranota <span class="text-red-500">*</span>
                                </label>
                                <input type="date"
                                       class="{{ $inputClasses }} @error('tanggal_pranota') border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror"
                                       id="tanggal_pranota"
                                       name="tanggal_pranota"
                                       value="{{ old('tanggal_pranota', date('Y-m-d')) }}"
                                       required>
                                @error('tanggal_pranota')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                                <textarea class="{{ $inputClasses }} @error('keterangan') border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror"
                                          id="keterangan"
                                          name="keterangan"
                                          rows="2"
                                          placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Uang Jalan -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Total Uang Jalan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div>
                                <label for="jumlah_surat_jalan_display" class="{{ $labelClasses }}">Jumlah Surat Jalan</label>
                                <input type="text" id="jumlah_surat_jalan_display" class="{{ $readonlyInputClasses }}" value="0" readonly>
                            </div>
                            <div>
                                <label for="total_uang_jalan_display" class="{{ $labelClasses }}">Total Uang Jalan</label>
                                <input type="text" id="total_uang_jalan_display" class="{{ $readonlyInputClasses }} font-bold text-gray-800 bg-gray-100" value="Rp 0" readonly>
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
                            <h4 class="text-sm font-semibold text-gray-800">🚚 Pilih Surat Jalan yang Sudah Disetujui</h4>
                            <span id="searchResults" class="text-xs text-gray-500 hidden"></span>
                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            <div class="relative flex-1 sm:flex-initial">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" id="searchSuratJalan" placeholder="Cari nomor surat jalan, pengirim, tujuan... (Ctrl+F)" class="pl-8 pr-3 py-1.5 border border-gray-300 rounded-md text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-64 transition-colors" title="Tekan Ctrl+F untuk fokus search, ESC untuk clear">
                            </div>
                            <button type="button" id="clearSearch" class="inline-flex items-center justify-center px-2 py-1.5 border border-gray-300 rounded-md text-xs text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" title="Clear search">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <button type="button" id="selectAllBtn" class="inline-flex items-center px-2 py-1.5 border border-blue-300 rounded-md text-xs text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-xs text-blue-700">
                            <span id="selectedCount">0</span> surat jalan dipilih dengan total uang jalan:
                            <span class="font-semibold" id="totalUangJalan">Rp 0</span>
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto max-h-60">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-20">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="selectAllCheckbox" class="h-3 w-3 text-indigo-600 border-gray-300 rounded">
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                                <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Barang</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Uang Jalan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($approvedSuratJalans as $suratJalan)
                                <tr class="surat-jalan-row hover:bg-gray-50 transition-colors"
                                    data-nomor="{{ strtolower($suratJalan->no_surat_jalan ?? $suratJalan->nomor_surat_jalan ?? '') }}"
                                    data-pengirim="{{ strtolower($suratJalan->pengirim ?? '') }}"
                                    data-tujuan="{{ strtolower($suratJalan->tujuan_pengambilan ?? '') }}"
                                    data-jenis-barang="{{ strtolower($suratJalan->jenis_barang ?? '') }}">
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <input type="checkbox"
                                               name="surat_jalan_ids[]"
                                               value="{{ $suratJalan->id }}"
                                               class="surat-jalan-checkbox h-3 w-3 text-indigo-600 border-gray-300 rounded"
                                               data-uang_jalan="{{ $suratJalan->uang_jalan ?? 0 }}"
                                               {{ in_array($suratJalan->id, old('surat_jalan_ids', [])) ? 'checked' : '' }}>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">{{ $suratJalan->no_surat_jalan ?? $suratJalan->nomor_surat_jalan ?? '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs text-center">{{ $suratJalan->tanggal_surat_jalan ? \Carbon\Carbon::parse($suratJalan->tanggal_surat_jalan)->format('d/m/Y') : '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $suratJalan->pengirim ?? '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $suratJalan->tujuan_pengambilan ?? '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $suratJalan->jenis_barang ?? '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold">
                                        {{ $suratJalan->uang_jalan ? 'Rp ' . number_format($suratJalan->uang_jalan, 0, ',', '.') : 'Rp 0' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-2 py-4 text-center text-xs text-gray-500">
                                        <div class="flex flex-col items-center py-4">
                                            <svg class="h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <p class="font-medium">Tidak ada surat jalan tersedia</p>
                                            <p class="text-xs mt-1">Tidak ada surat jalan yang sudah disetujui dan belum memiliki pranota.</p>
                                            <p class="text-xs">Pastikan surat jalan sudah disetujui di kedua level approval.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                    <p class="text-xs text-gray-600">
                        * Hanya surat jalan yang sudah di-approve oleh <strong>Approval Tugas 1</strong> dan <strong>Approval Tugas 2</strong> dan belum memiliki pranota.
                    </p>
                </div>

                @error('surat_jalan_ids')
                    <div class="bg-red-50 px-3 py-2 border-t border-red-200">
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    </div>
                @enderror
            </div>

            <!-- Submit Button -->
            @if($approvedSuratJalans->count() > 0)
                <div class="flex flex-col sm:flex-row justify-end gap-2">
                    <a href="{{ route('pranota-surat-jalan.index') }}"
                       class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            id="submitBtn">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Buat Pranota
                    </button>
                </div>
            @endif
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const suratJalanCheckboxes = document.querySelectorAll('.surat-jalan-checkbox');
            const jumlahSuratJalanDisplay = document.getElementById('jumlah_surat_jalan_display');
            const totalUangJalanDisplay = document.getElementById('total_uang_jalan_display');
            const submitBtn = document.getElementById('submitBtn');
            const selectedSummary = document.getElementById('selectedSummary');
            const selectedCount = document.getElementById('selectedCount');
            const totalUangJalan = document.getElementById('totalUangJalan');

            function updateTotalUangJalan() {
                let total = 0;
                let count = 0;
                suratJalanCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        total += parseFloat(checkbox.dataset.uang_jalan) || 0;
                        count++;
                    }
                });

                jumlahSuratJalanDisplay.value = count;
                totalUangJalanDisplay.value = 'Rp ' + total.toLocaleString('id-ID');

                if (selectedCount && totalUangJalan) {
                    selectedCount.textContent = count;
                    totalUangJalan.textContent = 'Rp ' + total.toLocaleString('id-ID');
                }

                // Show/hide summary and enable/disable submit button
                if (count > 0) {
                    if (selectedSummary) selectedSummary.classList.remove('hidden');
                    if (submitBtn) submitBtn.disabled = false;
                } else {
                    if (selectedSummary) selectedSummary.classList.add('hidden');
                    if (submitBtn) submitBtn.disabled = true;
                }

                return { total, count };
            }

            // Select all functionality
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    const visibleCheckboxes = document.querySelectorAll('.surat-jalan-checkbox:not([style*="display: none"])');
                    visibleCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateTotalUangJalan();
                });
            }

            // Individual checkbox change
            suratJalanCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateTotalUangJalan();
                    updateSelectAllState();
                });
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

            // Select all button
            const selectAllBtn = document.getElementById('selectAllBtn');
            if (selectAllBtn) {
                selectAllBtn.addEventListener('click', function() {
                    const visibleCheckboxes = document.querySelectorAll('.surat-jalan-checkbox:not([style*="display: none"])');
                    visibleCheckboxes.forEach(checkbox => {
                        checkbox.checked = true;
                    });
                    updateTotalUangJalan();
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
                    updateTotalUangJalan();
                    updateSelectAllState();
                });
            }

            // Search functionality
            const searchInput = document.getElementById('searchSuratJalan');
            const clearSearchBtn = document.getElementById('clearSearch');
            const tableBody = document.querySelector('tbody');
            const tableRows = tableBody.querySelectorAll('tr.surat-jalan-row');

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
                    const pengirim = row.dataset.pengirim || '';
                    const tujuan = row.dataset.tujuan || '';
                    const jenisBarang = row.dataset.jenisBarang || '';

                    const isVisible = nomor.includes(term) ||
                                    pengirim.includes(term) ||
                                    tujuan.includes(term) ||
                                    jenisBarang.includes(term);

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
                updateTotalUangJalan();
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
                        alert('Silakan pilih minimal satu surat jalan untuk membuat pranota.');
                        return false;
                    }
                });
            }

            // Initialize
            updateTotalUangJalan();
            updateSelectAllState();
        });
    </script>
@endsection
