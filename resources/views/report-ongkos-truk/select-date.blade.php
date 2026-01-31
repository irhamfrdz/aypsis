@extends('layouts.app')

@section('title', 'Report Ongkos Truk - Pilih Periode')
@section('page_title', 'Report Ongkos Truk - Pilih Periode')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-truck mr-3 text-blue-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Report Ongkos Truk</h1>
                    <p class="text-gray-600">Laporan ongkos truk berdasarkan periode dan plat mobil</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Pilih Periode & Plat --}}
    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <i class="fas fa-calendar-alt text-blue-600 text-6xl mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Pilih Periode & Kendaraan</h2>
                <p class="text-gray-600">Silakan pilih rentang tanggal dan plat mobil untuk menampilkan laporan.</p>
            </div>

            <form method="GET" action="{{ route('report.ongkos-truk.view') }}" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Start Date --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Dari Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="start_date" 
                               value="{{ old('start_date', now()->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                               required>
                    </div>

                    {{-- End Date --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Sampai Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="end_date" 
                               value="{{ old('end_date', now()->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                               required>
                    </div>
                </div>

                {{-- Plat Mobil (Searchable Multi-Select) --}}
                <div>
                    <label for="plat_search" class="block text-sm font-medium text-gray-700 mb-2">
                        Plat Mobil (Bisa pilih lebih dari satu)
                    </label>
                    
                    {{-- Hidden inputs for selected values --}}
                    <div id="hidden_inputs"></div>
                    
                    {{-- Search input with dropdown --}}
                    <div class="relative">
                        <div class="w-full min-h-[50px] px-3 py-2 border border-gray-300 rounded-md focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 bg-white cursor-text flex flex-wrap items-center gap-2" 
                             id="plat_container"
                             onclick="document.getElementById('plat_search').focus()">
                             
                            {{-- Selected items (chips) --}}
                            <div id="selected_chips" class="flex flex-wrap gap-1"></div>
                            
                            {{-- Search input --}}
                            <input type="text" 
                                   id="plat_search"
                                   placeholder="--Cari Plat Mobil--"
                                   class="border-0 outline-none bg-transparent flex-1 min-w-[150px] py-1"
                                   autocomplete="off">
                        </div>
                        
                        {{-- Dropdown list --}}
                        <div id="plat_dropdown" 
                             class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto hidden">
                            {{-- Known Mobils --}}
                            @foreach($mobils as $mobil)
                                <div class="plat-option px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0"
                                     data-id="{{ $mobil->nomor_polisi }}"
                                     data-text="{{ $mobil->nomor_polisi }}"
                                     data-merek="{{ $mobil->merek }}"
                                     data-jenis="{{ $mobil->jenis }}"
                                     data-warna="{{ $mobil->warna_plat }}">
                                    <div class="font-bold text-gray-900">
                                        {{ $mobil->nomor_polisi }}
                                    </div>
                                    <div class="text-xs text-gray-500 flex justify-between items-center">
                                        <span>{{ $mobil->merek }} {{ $mobil->jenis }}</span>
                                        @if($mobil->warna_plat)
                                            <span class="bg-gray-100 px-1 rounded text-[10px] uppercase">{{ $mobil->warna_plat }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            
                            {{-- Unknown Plates (legacy or external) --}}
                            @foreach($unknownPlats as $plat)
                                <div class="plat-option px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0"
                                     data-id="{{ $plat }}"
                                     data-text="{{ $plat }}"
                                     data-merek="Unknown"
                                     data-jenis="Vehicle"
                                     data-warna="">
                                    <div class="font-bold text-gray-900">
                                        {{ $plat }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        Data kendaraan tidak ditemukan di Master Mobil
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mt-2 flex justify-between items-center">
                        <span id="selectedCount" class="text-sm text-blue-600">
                            Terpilih: 0 kendaraan
                        </span>
                        <div class="flex gap-2">
                            <button type="button" 
                                    id="selectAllBtn"
                                    class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition duration-200">
                                Select All
                            </button>
                            <button type="button" 
                                    id="clearAllBtn"
                                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded transition duration-200">
                                Clear Semua
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="flex justify-center gap-4 pt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-md transition duration-200 inline-flex items-center text-lg font-medium">
                        <i class="fas fa-search mr-2"></i>
                        Tampilkan Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Searchable Multi-Select Styling */
    #plat_container {
        transition: all 0.15s ease;
    }
    
    .selected-chip {
        display: inline-flex;
        align-items: center;
        background-color: #3b82f6;
        color: white;
        font-size: 0.875rem;
        padding: 4px 10px;
        border-radius: 6px;
        margin: 2px;
        gap: 8px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    
    .selected-chip .remove-chip {
        cursor: pointer;
        font-weight: bold;
        font-size: 1rem;
        line-height: 1;
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    
    .selected-chip .remove-chip:hover {
        opacity: 1;
    }
    
    .plat-option {
        transition: background-color 0.15s ease;
    }
    
    .plat-option.selected {
        background-color: #eff6ff;
        color: #3b82f6;
    }

    #plat_dropdown::-webkit-scrollbar {
        width: 6px;
    }
    #plat_dropdown::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
        border-radius: 10px;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const platSearch = document.getElementById('plat_search');
    const platDropdown = document.getElementById('plat_dropdown');
    const selectedChips = document.getElementById('selected_chips');
    const hiddenInputs = document.getElementById('hidden_inputs');
    const clearAllBtn = document.getElementById('clearAllBtn');
    const platOptions = document.querySelectorAll('.plat-option');
    const selectedCount = document.getElementById('selectedCount');
    
    let selectedPlats = [];
    
    // Show dropdown on focus
    platSearch.addEventListener('focus', function() {
        platDropdown.classList.remove('hidden');
        filterOptions();
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#plat_container') && !e.target.closest('#plat_dropdown')) {
            platDropdown.classList.add('hidden');
        }
    });
    
    // Search/filter options
    platSearch.addEventListener('input', function() {
        filterOptions();
        platDropdown.classList.remove('hidden');
    });
    
    function filterOptions() {
        const searchTerm = platSearch.value.toLowerCase();
        platOptions.forEach(option => {
            const id = option.getAttribute('data-id').toLowerCase();
            const merek = option.getAttribute('data-merek').toLowerCase();
            const jenis = option.getAttribute('data-jenis').toLowerCase();
            const shouldShow = id.includes(searchTerm) || merek.includes(searchTerm) || jenis.includes(searchTerm);
            option.style.display = shouldShow ? 'block' : 'none';
        });
    }
    
    // Handle option selection
    platOptions.forEach(option => {
        option.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const text = this.getAttribute('data-text');
            
            if (!selectedPlats.includes(id)) {
                selectedPlats.push(id);
                addChip(id, text);
                updateSelectedCount();
                updateHiddenInputs();
                this.classList.add('selected');
            }
            
            platSearch.value = '';
            platDropdown.classList.add('hidden');
            platSearch.focus();
        });
    });
    
    function addChip(id, text) {
        const chip = document.createElement('span');
        chip.className = 'selected-chip animate-fade-in';
        chip.setAttribute('data-id', id);
        chip.innerHTML = `
            <span>${text}</span>
            <span class="remove-chip" data-id="${id}">&times;</span>
        `;
        
        // Add click listener to the remove button specifically
        chip.querySelector('.remove-chip').addEventListener('click', function(e) {
            e.stopPropagation();
            removePlate(id);
        });
        
        selectedChips.appendChild(chip);
    }
    
    function removePlate(id) {
        selectedPlats = selectedPlats.filter(p => p !== id);
        const chip = selectedChips.querySelector(`[data-id="${id}"]`);
        if (chip) chip.remove();
        
        const option = document.querySelector(`.plat-option[data-id="${id}"]`);
        if (option) option.classList.remove('selected');
        
        updateSelectedCount();
        updateHiddenInputs();
    }
    
    // Select All button
    const selectAllBtn = document.getElementById('selectAllBtn');
    selectAllBtn.addEventListener('click', function() {
        platOptions.forEach(option => {
            const id = option.getAttribute('data-id');
            const text = option.getAttribute('data-text');
            
            if (!selectedPlats.includes(id)) {
                selectedPlats.push(id);
                addChip(id, text);
                option.classList.add('selected');
            }
        });
        
        updateSelectedCount();
        updateHiddenInputs();
    });
    
    // Clear All button  
    clearAllBtn.addEventListener('click', function() {
        selectedPlats = [];
        selectedChips.innerHTML = '';
        hiddenInputs.innerHTML = '';
        platOptions.forEach(option => {
            option.classList.remove('selected');
        });
        updateSelectedCount();
    });
    
    function updateHiddenInputs() {
        hiddenInputs.innerHTML = '';
        selectedPlats.forEach(plat => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'no_plat[]';
            input.value = plat;
            hiddenInputs.appendChild(input);
        });
    }
    
    // Update selected count display
    function updateSelectedCount() {
        selectedCount.textContent = `Terpilih: ${selectedPlats.length} kendaraan`;
    }
});
</script>
@endpush
@endsection
