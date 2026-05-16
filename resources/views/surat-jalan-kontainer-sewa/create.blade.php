@extends('layouts.app')

@section('title', 'Buat Surat Jalan ' . ($tipe === 'pengambilan' ? 'Pengambilan' : 'Pengembalian') . ' Kontainer Sewa')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('surat-jalan-kontainer-sewa.index') }}" class="hover:text-cyan-600 transition">SJ Kontainer Sewa</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 font-medium">Buat {{ $tipe === 'pengambilan' ? 'Pengambilan' : 'Pengembalian' }}</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-3">
            @if($tipe === 'pengambilan')
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-truck-loading text-emerald-600"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Buat Surat Jalan Pengambilan Kontainer Sewa</h1>
                    <p class="text-sm text-gray-500">Catat pengambilan kontainer dari vendor</p>
                </div>
            @else
                <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-undo-alt text-orange-600"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Buat Surat Jalan Pengembalian Kontainer Sewa</h1>
                    <p class="text-sm text-gray-500">Catat pengembalian kontainer ke vendor</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4 text-sm">
            <ul class="list-disc ml-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('surat-jalan-kontainer-sewa.store') }}" id="form-create">
        @csrf
        <input type="hidden" name="tipe" value="{{ $tipe }}">

        {{-- Info Utama --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b"><i class="fas fa-info-circle text-cyan-600 mr-1"></i> Informasi Utama</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nomor Surat Jalan <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_surat_jalan" value="{{ old('nomor_surat_jalan', $nomorSuratJalan) }}" required class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Surat Jalan <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Vendor</label>
                    <select name="vendor" id="vendor-select" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500">
                        <option value="">-- Pilih Vendor --</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor }}" {{ old('vendor') == $vendor ? 'selected' : '' }}>{{ $vendor }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Supir</label>
                    <select name="supir" id="supir-select" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500">
                        <option value="">-- Pilih Supir --</option>
                        @foreach($supirs as $supir)
                            <option value="{{ $supir->nama_lengkap }}" data-plat="{{ $supir->plat }}" {{ old('supir') == $supir->nama_lengkap ? 'selected' : '' }}>{{ $supir->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">No. Plat</label>
                    <input type="text" name="no_plat" value="{{ old('no_plat') }}" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="B 1234 XYZ">
                </div>
                <div class="flex items-center pt-5">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="antar_lokasi" value="1" {{ old('antar_lokasi') ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-cyan-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Antar Lokasi</span>
                    </label>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nominal Uang Jalan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm">Rp</span>
                        </div>
                        <input type="number" name="nominal_uang_jalan" value="{{ old('nominal_uang_jalan', 0) }}" step="0.01" class="w-full text-sm border border-gray-300 rounded-md pl-10 pr-3 py-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="0.00">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Lokasi {{ $tipe === 'pengambilan' ? 'Pengambilan' : 'Pengembalian' }}</label>
                    @if($tipe === 'pengambilan')
                        <input type="text" name="lokasi_pengambilan" value="{{ old('lokasi_pengambilan') }}" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="Lokasi pengambilan">
                    @else
                        <input type="text" name="lokasi_pengembalian" value="{{ old('lokasi_pengembalian') }}" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="Lokasi pengembalian">
                    @endif
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="2" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="Keterangan tambahan...">{{ old('keterangan') }}</textarea>
            </div>
        </div>

        {{-- Pilih Kontainer --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4 pb-2 border-b">
                <h2 class="text-sm font-semibold text-gray-700"><i class="fas fa-boxes text-cyan-600 mr-1"></i> Pilih Kontainer</h2>
                <button type="button" id="btn-add-row" class="px-3 py-1.5 bg-cyan-600 text-white text-xs rounded-md hover:bg-cyan-700 transition">
                    <i class="fas fa-plus mr-1"></i> Tambah Baris
                </button>
            </div>

            <div id="kontainer-rows" class="space-y-3">
                {{-- Template row will be cloned here via JS --}}
                <div class="kontainer-row flex flex-wrap items-start gap-3 p-3 bg-gray-50 rounded-md border border-gray-200">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nomor Kontainer <span class="text-red-500">*</span></label>
                        <select name="kontainer_ids[]" class="kontainer-select w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500" required>
                            <option value="">-- Pilih --</option>
                            @foreach($kontainers as $k)
                                <option value="{{ $k->nomor_seri_gabungan }}" data-ukuran="{{ $k->ukuran }}" data-tipe="{{ $k->tipe_kontainer }}" data-vendor="{{ $k->vendor }}">
                                    {{ $k->nomor_seri_gabungan }} ({{ $k->vendor }} - {{ $k->ukuran }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-[120px]">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kondisi</label>
                        <select name="kondisi[]" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500">
                            <option value="baik">Baik</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Catatan Kondisi</label>
                        <input type="text" name="catatan_kondisi[]" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="Opsional...">
                    </div>
                    <div class="flex items-end">
                        <button type="button" class="btn-remove-row px-2 py-2 text-red-500 hover:bg-red-50 rounded transition mt-5" title="Hapus baris" style="display:none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('surat-jalan-kontainer-sewa.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300 transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-cyan-600 text-white text-sm rounded-md hover:bg-cyan-700 transition">
                <i class="fas fa-save mr-1"></i> Simpan Surat Jalan
            </button>
        </div>
    </form>
</div>

@push('styles')
<style>
    /* Custom Searchable Dropdown Styling */
    .vanilla-search-wrapper {
        position: relative;
        width: 100%;
    }

    .vanilla-search-input {
        width: 100%;
        padding: 8px 12px;
        font-size: 14px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .vanilla-search-input:focus {
        border-color: #06b6d4;
        box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
    }

    .vanilla-search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 50;
        margin-top: 4px;
        max-height: 250px;
        overflow-y: auto;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        display: none;
    }

    .vanilla-search-results.show {
        display: block;
    }

    .vanilla-search-option {
        padding: 8px 12px;
        font-size: 14px;
        color: #374151;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .vanilla-search-option:hover {
        background-color: #f3f4f6;
    }

    .vanilla-search-option.selected {
        background-color: #ecfeff;
        color: #0891b2;
        font-weight: 600;
    }

    .vanilla-search-no-results {
        padding: 8px 12px;
        font-size: 14px;
        color: #9ca3af;
        font-style: italic;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    /**
     * Reusable Vanilla Searchable Select
     */
    function createSearchableSelect(selectElement, placeholder = '-- Pilih --') {
        if (!selectElement) return;

        // Hide original select
        selectElement.style.display = 'none';

        // Create wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'vanilla-search-wrapper';
        selectElement.parentNode.insertBefore(wrapper, selectElement);
        wrapper.appendChild(selectElement);

        // Create search input
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = placeholder;
        searchInput.className = 'vanilla-search-input';
        searchInput.autocomplete = 'off';
        
        // If select already has a value (old input)
        if (selectElement.value) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            if (selectedOption && selectedOption.value) {
                searchInput.value = selectedOption.text;
            }
        }
        
        wrapper.appendChild(searchInput);

        // Create results container
        const resultsContainer = document.createElement('div');
        resultsContainer.className = 'vanilla-search-results';
        wrapper.appendChild(resultsContainer);

        let selectedIndex = -1;

        function updateResults() {
            const searchTerm = searchInput.value.toLowerCase();
            resultsContainer.innerHTML = '';
            let hasResults = false;

            const options = Array.from(selectElement.options);
            
            options.forEach((option, index) => {
                if (!option.value) return; // Skip placeholder

                if (option.text.toLowerCase().includes(searchTerm)) {
                    const div = document.createElement('div');
                    div.className = 'vanilla-search-option';
                    if (selectElement.value === option.value) div.classList.add('selected');
                    div.textContent = option.text;
                    div.onclick = () => {
                        selectOption(option);
                    };
                    resultsContainer.appendChild(div);
                    hasResults = true;
                }
            });

            if (!hasResults) {
                const noResults = document.createElement('div');
                noResults.className = 'vanilla-search-no-results';
                noResults.textContent = 'Tidak ditemukan';
                resultsContainer.appendChild(noResults);
            }
        }

        function selectOption(option) {
            selectElement.value = option.value;
            searchInput.value = option.text;
            resultsContainer.classList.remove('show');
            
            // Trigger change event on original select
            const event = new Event('change', { bubbles: true });
            selectElement.dispatchEvent(event);
        }

        searchInput.onfocus = () => {
            updateResults();
            resultsContainer.classList.add('show');
        };

        searchInput.oninput = () => {
            updateResults();
            resultsContainer.classList.add('show');
        };

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
                resultsContainer.classList.remove('show');
                
                // If user left partially typed text, reset to selected option
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    searchInput.value = selectedOption.text;
                } else if (!selectElement.value) {
                    searchInput.value = '';
                }
            }
        });

        return { wrapper, searchInput, resultsContainer };
    }

    // Initialize dropdowns
    const supirSelect = document.getElementById('supir-select');
    const vendorSelect = document.getElementById('vendor-select');
    const noPlatInput = document.querySelector('input[name="no_plat"]');

    if (supirSelect) createSearchableSelect(supirSelect, '-- Pilih Supir --');
    if (vendorSelect) createSearchableSelect(vendorSelect, '-- Pilih Vendor --');

    // Handle supir change for auto-fill plat
    if (supirSelect) {
        supirSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const plat = selectedOption ? selectedOption.getAttribute('data-plat') : '';
            if (noPlatInput) noPlatInput.value = plat || '';
        });
    }

    // Container Rows Logic
    const rowsContainer = document.getElementById('kontainer-rows');
    const btnAdd = document.getElementById('btn-add-row');

    function initKontainerSearch(row) {
        const select = row.querySelector('.kontainer-select');
        if (select) createSearchableSelect(select, '-- Pilih Kontainer --');
    }

    // Init existing
    rowsContainer.querySelectorAll('.kontainer-row').forEach(initKontainerSearch);

    function updateRemoveButtons() {
        const rows = rowsContainer.querySelectorAll('.kontainer-row');
        rows.forEach((row) => {
            const btn = row.querySelector('.btn-remove-row');
            if (btn) btn.style.display = rows.length > 1 ? '' : 'none';
        });
    }

    btnAdd.addEventListener('click', function() {
        // We need a clean clone. Since we've modified the DOM with wrappers, 
        // cloning the first row might clone the wrappers too.
        // It's safer to clone the select element and rebuild or find the original row template.
        
        const firstRow = rowsContainer.querySelector('.kontainer-row');
        const newRow = firstRow.cloneNode(true);
        
        // Remove the custom wrappers in the clone and restore original select
        newRow.querySelectorAll('.vanilla-search-wrapper').forEach(wrapper => {
            const select = wrapper.querySelector('select');
            if (select) {
                select.style.display = '';
                wrapper.parentNode.insertBefore(select, wrapper);
            }
            wrapper.remove();
        });

        // Reset values
        newRow.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
        newRow.querySelectorAll('input').forEach(i => i.value = '');
        
        rowsContainer.appendChild(newRow);
        
        // Init the new searchable select
        initKontainerSearch(newRow);

        // Attach remove handler
        newRow.querySelector('.btn-remove-row').addEventListener('click', function() {
            newRow.remove();
            updateRemoveButtons();
        });

        updateRemoveButtons();
    });

    // Attach remove handlers to existing rows
    rowsContainer.querySelectorAll('.btn-remove-row').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.kontainer-row').remove();
            updateRemoveButtons();
        });
    });

    updateRemoveButtons();
});
</script>
@endpush
@endsection


