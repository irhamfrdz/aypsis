@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-clipboard-check mr-3 text-green-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Proses Prospek</h1>
                    <p class="text-gray-600">Tujuan: <span class="font-semibold text-green-600">{{ $tujuan->nama }}</span></p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('prospek.pilih-tujuan') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Pilih Tujuan Lain
                </a>
                <a href="{{ route('prospek.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-home mr-2"></i>
                    Kembali ke Prospek
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    @if($prospeksAktif->count() > 0)
        {{-- Form Naik Kapal --}}
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 bg-blue-500 text-white rounded-t-lg">
                <h3 class="text-lg font-semibold">Tambah Data</h3>
            </div>
            
            <div class="p-6">
                <form action="{{ route('prospek.execute-naik-kapal') }}" method="POST" id="naikKapalForm">
                    @csrf
                    <input type="hidden" name="tujuan_id" value="{{ $tujuanId }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Tanggal --}}
                        <div>
                            <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="tanggal" 
                                   name="tanggal"
                                   value="{{ old('tanggal', date('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tanggal') border-red-500 @enderror"
                                   required>
                            @error('tanggal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kapal --}}
                        <div>
                            <label for="kapal_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Kapal <span class="text-red-500">*</span>
                            </label>
                            <select id="kapal_id" 
                                    name="kapal_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('kapal_id') border-red-500 @enderror"
                                    required>
                                <option value="">--Pilih Kapal--</option>
                                @foreach($masterKapals as $kapal)
                                    <option value="{{ $kapal->id }}" {{ old('kapal_id') == $kapal->id ? 'selected' : '' }}>
                                        {{ $kapal->nama_kapal }} ({{ $kapal->nickname ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('kapal_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- No Voyage --}}
                        <div>
                            <label for="no_voyage" class="block text-sm font-medium text-gray-700 mb-2">
                                No Voyage <span class="text-red-500">*</span>
                            </label>
                            <select id="no_voyage" 
                                    name="no_voyage"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('no_voyage') border-red-500 @enderror"
                                    required>
                                <option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>
                            </select>
                            @error('no_voyage')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- No Kontainer dan Seal (Searchable Multi-Select) --}}
                        <div>
                            <label for="prospek_search" class="block text-sm font-medium text-gray-700 mb-2">
                                No Kontainer dan Seal <span class="text-red-500">*</span>
                            </label>
                            
                            {{-- Hidden inputs for selected values --}}
                            <div id="hidden_inputs"></div>
                            
                            {{-- Search input with dropdown --}}
                            <div class="relative">
                                <div class="w-full min-h-[42px] px-3 py-2 border border-gray-300 rounded-md focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 bg-white cursor-text" 
                                     id="prospek_container"
                                     onclick="document.getElementById('prospek_search').focus()">
                                     
                                    {{-- Selected items (chips) --}}
                                    <div id="selected_chips" class="flex flex-wrap gap-1 mb-1"></div>
                                    
                                    {{-- Search input --}}
                                    <input type="text" 
                                           id="prospek_search"
                                           placeholder="--Pilih Kontainer - Seal--"
                                           class="border-0 outline-none bg-transparent flex-1 min-w-[200px]"
                                           autocomplete="off">
                                </div>
                                
                                {{-- Dropdown list --}}
                                <div id="prospek_dropdown" 
                                     class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto hidden">
                                    @foreach($prospeksAktif as $prospek)
                                        @php
                                            // Allow selection even without container number
                                            if ($prospek->nomor_kontainer) {
                                                $displayText = $prospek->no_seal 
                                                    ? $prospek->nomor_kontainer . ' - ' . $prospek->no_seal 
                                                    : $prospek->nomor_kontainer;
                                            } else {
                                                // Show alternative info when container number is empty
                                                $displayText = 'ID #' . $prospek->id . ' - ' . strtoupper($prospek->tipe ?? 'N/A');
                                                if ($prospek->no_seal) {
                                                    $displayText .= ' - Seal: ' . $prospek->no_seal;
                                                }
                                            }
                                        @endphp
                                        <div class="prospek-option px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0"
                                             data-id="{{ $prospek->id }}"
                                             data-text="{{ $displayText }}"
                                             data-tipe="{{ $prospek->tipe }}"
                                             data-supir="{{ $prospek->nama_supir }}"
                                             data-tanggal="{{ $prospek->created_at ? $prospek->created_at->format('d/m/Y') : '-' }}"
                                             data-pengirim="{{ $prospek->pt_pengirim ?? '-' }}"
                                             data-barang="{{ $prospek->barang ?? '-' }}">
                                            <div class="font-medium text-gray-900 {{ !$prospek->nomor_kontainer ? 'text-orange-600' : '' }}">
                                                {{ $displayText }}
                                            </div>
                                            <div class="text-sm text-gray-500 flex justify-between items-center">
                                                <span>{{ strtoupper($prospek->tipe ?? 'N/A') }} - {{ $prospek->nama_supir }}</span>
                                                <span class="text-blue-600 font-medium">{{ $prospek->created_at ? $prospek->created_at->format('d/m/Y') : '-' }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="mt-2 flex justify-between items-center">
                                <span id="selectedCount" class="text-sm text-blue-600">
                                    Terpilih: 0 dari {{ $prospeksAktif->where('tipe', '!=', 'CARGO')->count() }} prospek
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
                            
                            @error('prospek_ids')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tujuan Kirim Asal --}}
                        <div>
                            <label for="pelabuhan_asal" class="block text-sm font-medium text-gray-700 mb-2">
                                Asal <span class="text-red-500">*</span>
                            </label>
                            <select id="pelabuhan_asal" 
                                    name="pelabuhan_asal"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('pelabuhan_asal') border-red-500 @enderror"
                                    required>
                                <option value="">--Pilih ASAL--</option>
                                @foreach($masterTujuanKirims as $tujuanKirim)
                                    <option value="{{ $tujuanKirim->nama_tujuan }}" {{ old('pelabuhan_asal') == $tujuanKirim->nama_tujuan ? 'selected' : '' }}>
                                        {{ $tujuanKirim->nama_tujuan }} - {{ $tujuanKirim->kota }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pelabuhan_asal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tujuan (Read-only) --}}
                        <div>
                            <label for="tujuan" class="block text-sm font-medium text-gray-700 mb-2">
                                Tujuan
                            </label>
                            <input type="text" 
                                   id="tujuan" 
                                   name="tujuan"
                                   value="{{ $tujuan->nama }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed"
                                   readonly>
                        </div>
                    </div>

                    {{-- Selected Prospeks Table --}}
                    <div id="selectedProspeksTable" class="mt-6 hidden">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-list mr-2"></i>
                                Kontainer Terpilih
                            </h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontainer - Seal</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PT. Pengirim</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selectedProspeksTableBody" class="divide-y divide-gray-200">
                                        <!-- Dynamic content will be inserted here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="mt-6 flex gap-3">
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                            Submit
                        </button>
                        <a href="{{ route('prospek.pilih-tujuan') }}" 
                           class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    @else
        {{-- Tidak Ada Prospek --}}
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="flex flex-col items-center justify-center">
                <i class="fas fa-inbox text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Tidak Ada Prospek untuk {{ $tujuan->nama }}</h3>
                <p class="text-gray-600 mb-6">Belum ada prospek aktif yang memiliki tujuan pengiriman ke {{ $tujuan->nama }}</p>
                <div class="flex gap-3">
                    <a href="{{ route('prospek.pilih-tujuan') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Pilih Tujuan Lain
                    </a>
                    <a href="{{ route('prospek.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Prospek
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endpush

            <style>
                /* Searchable Multi-Select Styling */
                #prospek_container {
                    transition: all 0.15s ease;
                }
                
                #prospek_container:focus-within {
                    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
                }
                
                .selected-chip {
                    display: inline-flex;
                    align-items: center;
                    background-color: #3b82f6;
                    color: white;
                    font-size: 0.75rem;
                    padding: 4px 8px;
                    border-radius: 4px;
                    margin: 1px;
                    gap: 6px;
                }
                
                .selected-chip .remove-chip {
                    margin-left: 4px;
                    cursor: pointer;
                    font-weight: bold;
                    font-size: 0.875rem;
                    opacity: 0.8;
                }
                
                .selected-chip .remove-chip:hover {
                    opacity: 1;
                }
                
                .selected-chip .flex {
                    line-height: 1.2;
                }
                
                .selected-chip .text-xs {
                    font-size: 0.65rem;
                }
                
                .prospek-option {
                    transition: background-color 0.15s ease;
                }
                
                .prospek-option:hover {
                    background-color: #eff6ff !important;
                }
                
                .prospek-option.selected {
                    background-color: #dbeafe;
                    opacity: 0.6;
                }
                
                #prospek_search::placeholder {
                    color: #9ca3af;
                }
                
                #prospek_dropdown {
                    border-top: none;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                }
                
                /* Table Styling */
                .table-row-prospek {
                    transition: background-color 0.15s ease;
                }
                
                .table-row-prospek:hover {
                    background-color: #f9fafb;
                }
                
                .table-row-prospek input[type="text"],
                .table-row-prospek select {
                    min-width: 150px;
                }
                
                .table-row-prospek input[type="text"]:focus,
                .table-row-prospek select:focus {
                    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
                }
            </style><script>
            @push('scripts')
                <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const kapalEl = document.getElementById('kapal_id');
                        if (kapalEl && typeof Choices !== 'undefined') {
                            // Initialize Choices for searchable dropdown while keeping native select behavior
                            const choices = new Choices(kapalEl, {
                                searchEnabled: true,
                                shouldSort: false,
                                searchPlaceholderValue: 'Cari nama kapal...'
                            });
                            // Keep the reference to underlying select for existing code
                            kapalEl.choicesInstance = choices;
                        }
                    });
                </script>
            @endpush
document.addEventListener('DOMContentLoaded', function() {
    const kapalSelect = document.getElementById('kapal_id');
    const voyageSelect = document.getElementById('no_voyage');
    const selectedCount = document.getElementById('selectedCount');
    
    // Handle kapal selection change
    kapalSelect.addEventListener('change', function() {
        const kapalId = this.value;
        
        // Reset voyage dropdown
        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        voyageSelect.disabled = true;
        
        if (!kapalId) {
            voyageSelect.innerHTML = '<option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>';
            return;
        }
        
        // Fetch voyage data for selected kapal
        fetch(`{{ route('prospek.get-voyage-by-kapal') }}?kapal_id=${kapalId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            voyageSelect.innerHTML = '';
            
            if (data.success && data.voyages && data.voyages.length > 0) {
                // Add default option
                voyageSelect.innerHTML += '<option value="">-PILIH VOYAGE-</option>';
                
                // Add voyage options
                data.voyages.forEach(voyage => {
                    voyageSelect.innerHTML += `<option value="${voyage}">${voyage}</option>`;
                });
            } else {
                voyageSelect.innerHTML = '<option value="">Belum ada voyage untuk kapal ini</option>';
            }
            
            voyageSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error fetching voyage data:', error);
            voyageSelect.innerHTML = '<option value="">Error loading voyage data</option>';
            voyageSelect.disabled = false;
        });
    });
    
    // Handle searchable multi-select
    const prospekSearch = document.getElementById('prospek_search');
    const prospekDropdown = document.getElementById('prospek_dropdown');
    const selectedChips = document.getElementById('selected_chips');
    const hiddenInputs = document.getElementById('hidden_inputs');
    const clearAllBtn = document.getElementById('clearAllBtn');
    const prospekOptions = document.querySelectorAll('.prospek-option');
    const selectedProspeksTable = document.getElementById('selectedProspeksTable');
    const selectedProspeksTableBody = document.getElementById('selectedProspeksTableBody');
    
    let selectedProspeks = [];
    
    // Show dropdown on focus
    prospekSearch.addEventListener('focus', function() {
        prospekDropdown.classList.remove('hidden');
        filterOptions();
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#prospek_container') && !e.target.closest('#prospek_dropdown')) {
            prospekDropdown.classList.add('hidden');
        }
    });
    
    // Search/filter options
    prospekSearch.addEventListener('input', function() {
        filterOptions();
    });
    
    function filterOptions() {
        const searchTerm = prospekSearch.value.toLowerCase();
        prospekOptions.forEach(option => {
            const text = option.getAttribute('data-text').toLowerCase();
            const supir = option.getAttribute('data-supir').toLowerCase();
            const tanggal = option.getAttribute('data-tanggal').toLowerCase();
            const shouldShow = text.includes(searchTerm) || supir.includes(searchTerm) || tanggal.includes(searchTerm);
            option.style.display = shouldShow ? 'block' : 'none';
        });
    }
    
    // Handle option selection
    prospekOptions.forEach(option => {
        option.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const text = this.getAttribute('data-text');
            const tipe = this.getAttribute('data-tipe');
            const supir = this.getAttribute('data-supir');
            const tanggal = this.getAttribute('data-tanggal');
            const pengirim = this.getAttribute('data-pengirim');
            const barang = this.getAttribute('data-barang');
            
            if (!selectedProspeks.find(p => p.id === id)) {
                selectedProspeks.push({ id, text, tipe, pengirim, barang, supir, tanggal });
                addChip(id, text, tanggal);
                addTableRow(id, text, tipe, pengirim, barang, supir, tanggal);
                updateSelectedCount();
                updateHiddenInputs();
                updateTableVisibility();
                this.classList.add('selected');
            }
            
            prospekSearch.value = '';
            prospekDropdown.classList.add('hidden');
        });
    });
    
    function addChip(id, text, tanggal) {
        const chip = document.createElement('span');
        chip.className = 'selected-chip';
        chip.setAttribute('data-id', id);
        chip.innerHTML = `
            <div class="flex flex-col">
                <span class="font-medium">${text}</span>
                <span class="text-xs opacity-75">${tanggal}</span>
            </div>
            <span class="remove-chip" onclick="removeChip('${id}')">&times;</span>
        `;
        selectedChips.appendChild(chip);
    }
    
    function addTableRow(id, text, tipe, pengirim, barang, supir, tanggal) {
        const rowIndex = selectedProspeks.length;
        const row = document.createElement('tr');
        row.setAttribute('data-id', id);
        row.className = 'table-row-prospek';
        
        row.innerHTML = `
            <td class="px-4 py-3 text-sm text-gray-900">${rowIndex}</td>
            <td class="px-4 py-3 text-sm text-gray-900 font-medium">${text}</td>
            <td class="px-4 py-3 text-sm text-gray-600">
                <span class="px-2 py-1 text-xs font-medium rounded-full ${tipe === 'CARGO' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'}">
                    ${tipe}
                </span>
            </td>
            <td class="px-4 py-3 text-sm text-gray-900">${pengirim}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${barang}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${supir}</td>
            <td class="px-4 py-3 text-sm text-gray-600">${tanggal}</td>
            <td class="px-4 py-3 text-sm">
                <button type="button" 
                        onclick="removeChip('${id}')"
                        class="text-red-600 hover:text-red-800 transition duration-200">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        selectedProspeksTableBody.appendChild(row);
    }
    
    // Remove chip function (global scope for onclick)
    window.removeChip = function(id) {
        selectedProspeks = selectedProspeks.filter(p => p.id !== id);
        document.querySelector(`[data-id="${id}"].selected-chip`).remove();
        document.querySelector(`[data-id="${id}"].table-row-prospek`).remove();
        document.querySelector(`[data-id="${id}"].prospek-option`).classList.remove('selected');
        updateSelectedCount();
        updateHiddenInputs();
        updateTableVisibility();
        reorderTableRows();
    };
    
    // Select All button
    const selectAllBtn = document.getElementById('selectAllBtn');
    selectAllBtn.addEventListener('click', function() {
        prospekOptions.forEach(option => {
            const id = option.getAttribute('data-id');
            const text = option.getAttribute('data-text');
            const tipe = option.getAttribute('data-tipe');
            const supir = option.getAttribute('data-supir');
            const tanggal = option.getAttribute('data-tanggal');
            const pengirim = option.getAttribute('data-pengirim');
            const barang = option.getAttribute('data-barang');
            
            // Only add if not already selected
            if (!selectedProspeks.find(p => p.id === id)) {
                selectedProspeks.push({ id, text, tipe, pengirim, barang, supir, tanggal });
                addChip(id, text, tanggal);
                addTableRow(id, text, tipe, pengirim, barang, supir, tanggal);
                option.classList.add('selected');
            }
        });
        
        updateSelectedCount();
        updateHiddenInputs();
        updateTableVisibility();
    });
    
    // Clear All button  
    clearAllBtn.addEventListener('click', function() {
        selectedProspeks = [];
        selectedChips.innerHTML = '';
        selectedProspeksTableBody.innerHTML = '';
        hiddenInputs.innerHTML = '';
        prospekOptions.forEach(option => {
            option.classList.remove('selected');
        });
        updateSelectedCount();
        updateTableVisibility();
    });
    
    function updateHiddenInputs() {
        hiddenInputs.innerHTML = '';
        selectedProspeks.forEach(prospek => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'prospek_ids[]';
            input.value = prospek.id;
            hiddenInputs.appendChild(input);
        });
    }
    
    function updateTableVisibility() {
        if (selectedProspeks.length > 0) {
            selectedProspeksTable.classList.remove('hidden');
        } else {
            selectedProspeksTable.classList.add('hidden');
        }
    }
    
    function reorderTableRows() {
        const rows = selectedProspeksTableBody.querySelectorAll('.table-row-prospek');
        rows.forEach((row, index) => {
            const numberCell = row.querySelector('td:first-child');
            numberCell.textContent = index + 1;
        });
    }
    
    // Update selected count display
    function updateSelectedCount() {
        selectedCount.textContent = `Terpilih: ${selectedProspeks.length} dari {{ $prospeksAktif->where('tipe', '!=', 'CARGO')->count() }} prospek`;
    }
    
    // Form validation
    const form = document.getElementById('naikKapalForm');
    form.addEventListener('submit', function(e) {
        const kapalId = kapalSelect.value;
        const voyage = voyageSelect.value;
        const pelabuhan = document.getElementById('pelabuhan_asal').value;
        
        if (selectedProspeks.length === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal 1 prospek untuk dimuat ke kapal');
            return;
        }
        
        if (!kapalId) {
            e.preventDefault();
            alert('Silakan pilih kapal');
            return;
        }
        
        if (!voyage) {
            e.preventDefault();
            alert('Silakan pilih nomor voyage');
            return;
        }
        
        if (!pelabuhan) {
            e.preventDefault();
            alert('Silakan pilih tujuan kirim asal');
            return;
        }
        
        // Confirmation
        if (!confirm(`Apakah Anda yakin ingin memproses ${selectedProspeks.length} prospek?`)) {
            e.preventDefault();
        }
    });
});
</script>
@endsection