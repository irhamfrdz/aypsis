@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-truck mr-3 text-purple-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Ongkos Truck</h1>
                    <p class="text-gray-600">Filter dan Kelola Data Ongkos Truck</p>
                </div>
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

    {{-- Filter Form --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="border-b border-gray-200 mb-4">
            <h3 class="text-lg font-semibold text-gray-700 pb-2 flex items-center">
                <i class="fas fa-filter mr-2 text-purple-600"></i>
                Filter Data Ongkos Truck
            </h3>
        </div>

        <form action="{{ route('master.ongkos-truck.show-data') }}" method="GET" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Tanggal Dari --}}
                <div>
                    <label for="tanggal_dari" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Tanda Terima Dari <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="tanggal_dari" 
                           name="tanggal_dari"
                           value="{{ request('tanggal_dari', date('Y-m-01')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                           required>
                </div>

                {{-- Tanggal Sampai --}}
                <div>
                    <label for="tanggal_sampai" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Tanda Terima Sampai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="tanggal_sampai" 
                           name="tanggal_sampai"
                           value="{{ request('tanggal_sampai', date('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                           required>
                </div>

                {{-- Nomor Mobil/Polisi --}}
                <div>
                    <label for="mobil_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Plat Nomor Mobil <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-500 font-normal">(bisa pilih lebih dari 1)</span>
                    </label>
                    <select id="mobil_id" 
                            name="mobil_id[]"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                            multiple
                            required>
                        @foreach($mobils as $mobil)
                            @php
                                $selected = is_array(request('mobil_id')) && in_array($mobil->id, request('mobil_id'));
                            @endphp
                            <option value="{{ $mobil->id }}" {{ $selected ? 'selected' : '' }}>
                                {{ $mobil->nomor_polisi }}{{ $mobil->warna_plat ? ' ('.strtoupper($mobil->warna_plat).')' : '' }} | {{ $mobil->merek }} {{ $mobil->jenis }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" 
                        class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-search mr-2"></i>
                    Tampilkan Data
                </button>
                <a href="{{ route('master.ongkos-truck.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-redo mr-2"></i>
                    Reset Filter
                </a>
            </div>
        </form>
    </div>

    {{-- Results Table (only show when filtered) --}}
    @if(isset($suratJalans) || isset($suratJalanBongkarans))
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="px-6 py-4 bg-purple-500 text-white rounded-t-lg flex justify-between items-center">
            <h3 class="text-lg font-semibold">Data Ongkos Truck</h3>
            <div class="flex gap-2">
                <button onclick="window.print()" class="bg-white text-purple-600 px-4 py-1 rounded text-sm hover:bg-purple-50 transition">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
                <a href="{{ route('master.ongkos-truck.export-excel', request()->query()) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded text-sm transition">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
            </div>
        </div>
        
        <div class="p-6">
            {{-- Summary Info --}}
            <div class="mb-4 p-4 bg-purple-50 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Periode Tanda Terima:</span>
                        <span class="font-semibold ml-2">{{ date('d/m/Y', strtotime(request('tanggal_dari'))) }} - {{ date('d/m/Y', strtotime(request('tanggal_sampai'))) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Nomor Plat:</span>
                        <span class="font-semibold ml-2">{{ implode(', ', $nomorPlatList ?? []) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Total Data:</span>
                        <span class="font-semibold ml-2">
                            {{ (isset($suratJalans) ? $suratJalans->count() : 0) + (isset($suratJalanBongkarans) ? $suratJalanBongkarans->count() : 0) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Detail:</span>
                        <span class="font-semibold ml-2">
                            SJ: {{ isset($suratJalans) ? $suratJalans->count() : 0 }} | 
                            SJB: {{ isset($suratJalanBongkarans) ? $suratJalanBongkarans->count() : 0 }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Combined Surat Jalan Table --}}
            @php
                // Gabungkan data dari kedua collection
                $allSuratJalans = collect();
                
                // Add Surat Jalan data
                if(isset($suratJalans)) {
                    foreach($suratJalans as $sj) {
                        // Tentukan ongkos truk berdasarkan size dari tujuanPengambilanRelation
                        $ongkosTruk = 0;
                        if ($sj->tujuanPengambilanRelation) {
                            $size = strtolower($sj->size ?? '');
                            if (str_contains($size, '40')) {
                                $ongkosTruk = $sj->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0;
                            } else {
                                $ongkosTruk = $sj->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0;
                            }
                        }
                        
                        $allSuratJalans->push([
                            'jenis' => 'Surat Jalan',
                            'tanggal_tanda_terima' => $sj->tandaTerima ? $sj->tandaTerima->tanggal : null,
                            'no_surat_jalan' => $sj->no_surat_jalan,
                            'tanggal_surat_jalan' => $sj->tanggal_surat_jalan,
                            'no_plat' => $sj->no_plat,
                            'supir' => $sj->supir,
                            'kegiatan' => $sj->kegiatan,
                            'tujuan' => $sj->tujuanPengambilanRelation->ke ?? $sj->tujuan_pengambilan,
                            'nama_kapal' => null,
                            'size' => $sj->size,
                            'ongkos_truk' => $ongkosTruk,
                        ]);
                    }
                }
                
                // Add Surat Jalan Bongkaran data
                if(isset($suratJalanBongkarans)) {
                    foreach($suratJalanBongkarans as $sjb) {
                        // Tentukan ongkos truk berdasarkan size dari tujuanPengambilanRelation
                        $ongkosTruk = 0;
                        if ($sjb->tujuanPengambilanRelation) {
                            $size = strtolower($sjb->size ?? '');
                            if (str_contains($size, '40')) {
                                $ongkosTruk = $sjb->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0;
                            } else {
                                $ongkosTruk = $sjb->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0;
                            }
                        }
                        
                        $allSuratJalans->push([
                            'jenis' => 'SJ Bongkaran',
                            'tanggal_tanda_terima' => $sjb->tandaTerima ? $sjb->tandaTerima->tanggal_tanda_terima : null,
                            'no_surat_jalan' => $sjb->nomor_surat_jalan,
                            'tanggal_surat_jalan' => $sjb->tanggal_surat_jalan,
                            'no_plat' => $sjb->no_plat,
                            'supir' => $sjb->supir,
                            'kegiatan' => $sjb->kegiatan,
                            'tujuan' => $sjb->tujuanPengambilanRelation->ke ?? $sjb->tujuan_pengambilan,
                            'nama_kapal' => null,
                            'size' => $sjb->size,
                            'ongkos_truk' => $ongkosTruk,
                        ]);
                    }
                }
                
                // Sort by tanggal_tanda_terima descending
                $allSuratJalans = $allSuratJalans->sortByDesc('tanggal_tanda_terima')->values();
                
                $grandTotalOngkosTruk = $allSuratJalans->sum('ongkos_truk');
            @endphp

            @if($allSuratJalans->count() > 0)
            <div class="mb-6">
                <h4 class="text-md font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-file-alt mr-2 text-blue-600"></i>
                    Data Surat Jalan ({{ $allSuratJalans->count() }} data)
                </h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Jenis</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Tgl Tanda Terima</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">No Surat Jalan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Tgl Surat Jalan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">No Plat</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Supir</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Kegiatan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Tujuan</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Size</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Ongkos Truk</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($allSuratJalans as $index => $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($item['jenis'] == 'Surat Jalan')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-file-alt mr-1"></i> SJ
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                            <i class="fas fa-boxes mr-1"></i> SJB
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 font-medium text-purple-600">
                                    {{ $item['tanggal_tanda_terima'] ? date('d/m/Y', strtotime($item['tanggal_tanda_terima'])) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $item['no_surat_jalan'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $item['tanggal_surat_jalan'] ? date('d/m/Y', strtotime($item['tanggal_surat_jalan'])) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 font-semibold">{{ $item['no_plat'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item['supir'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item['kegiatan'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $item['tujuan'] ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ str_contains(strtolower($item['size'] ?? ''), '40') ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $item['size'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right font-semibold">
                                    Rp {{ number_format($item['ongkos_truk'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-purple-50 font-bold">
                            <tr>
                                <td colspan="10" class="px-4 py-3 text-sm text-gray-900 text-right">TOTAL ONGKOS TRUK:</td>
                                <td class="px-4 py-3 text-sm text-purple-600 text-right">Rp {{ number_format($grandTotalOngkosTruk, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endif

            @if($allSuratJalans->count() == 0)
            <div class="text-center py-8">
                <i class="fas fa-inbox text-6xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">Tidak ada data surat jalan untuk filter yang dipilih</p>
                <p class="text-gray-500 text-sm mt-2">Pastikan tanda terima sudah dibuat untuk surat jalan dengan plat nomor dan periode yang dipilih</p>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    .custom-select-wrapper {
        position: relative;
        width: 100%;
    }
    
    .custom-select-search {
        width: 100%;
        padding: 0.5rem 0.75rem;
        padding-top: 0.25rem;
        min-height: 42px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    
    .custom-select-search:focus {
        outline: none;
        border-color: #a855f7;
        box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
    }
    
    .selected-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.375rem;
        margin-bottom: 0.25rem;
    }
    
    .tag {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        background: #7c3aed;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .tag-remove {
        cursor: pointer;
        font-weight: bold;
        font-size: 0.875rem;
        margin-left: 0.125rem;
        opacity: 0.8;
        transition: opacity 0.2s;
    }
    
    .tag-remove:hover {
        opacity: 1;
    }
    
    .custom-select-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        margin-top: 0.25rem;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        display: none;
    }
    
    .custom-select-dropdown.active {
        display: block;
    }
    
    .custom-select-option {
        padding: 0.75rem;
        cursor: pointer;
        transition: background-color 0.15s;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .custom-select-option:last-child {
        border-bottom: none;
    }
    
    .custom-select-option:hover {
        background-color: #f9fafb;
    }
    
    .custom-select-option.selected {
        background-color: #ede9fe;
        color: #7c3aed;
        font-weight: 500;
    }
    
    .custom-select-option .plat-number {
        color: #7c3aed;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .custom-select-option .vehicle-info {
        color: #6b7280;
        font-size: 0.85rem;
        margin-left: 0.5rem;
    }
    
    .custom-select-no-results {
        padding: 1rem;
        text-align: center;
        color: #9ca3af;
        font-size: 0.875rem;
    }
    
    .custom-select-clear {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 12px;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s;
    }
    
    .custom-select-clear:hover {
        background: #dc2626;
    }
    
    .custom-select-clear.active {
        display: flex;
    }
    
    /* Hide original select */
    #mobil_id {
        display: none;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Create custom searchable select
        const selectElement = document.getElementById('mobil_id');
        const wrapper = document.createElement('div');
        wrapper.className = 'custom-select-wrapper';
        
        // Create container for tags
        const tagsContainer = document.createElement('div');
        tagsContainer.className = 'selected-tags';
        
        // Create search input wrapper
        const inputWrapper = document.createElement('div');
        inputWrapper.className = 'custom-select-search';
        
        // Create search input
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.style.cssText = 'border: none; outline: none; width: 100%; padding: 0; font-size: 0.875rem;';
        searchInput.placeholder = 'Ketik untuk mencari plat nomor mobil...';
        searchInput.autocomplete = 'off';
        
        // Create clear button
        const clearBtn = document.createElement('button');
        clearBtn.type = 'button';
        clearBtn.className = 'custom-select-clear';
        clearBtn.innerHTML = '×';
        
        // Create dropdown
        const dropdown = document.createElement('div');
        dropdown.className = 'custom-select-dropdown';
        
        // Insert wrapper before select
        selectElement.parentNode.insertBefore(wrapper, selectElement);
        inputWrapper.appendChild(tagsContainer);
        inputWrapper.appendChild(searchInput);
        wrapper.appendChild(inputWrapper);
        wrapper.appendChild(clearBtn);
        wrapper.appendChild(dropdown);
        
        // Get all options
        const options = Array.from(selectElement.options).filter(opt => opt.value);
        let selectedValues = [];
        
        // Function to update tags display
        function updateTags() {
            tagsContainer.innerHTML = '';
            selectedValues.forEach(value => {
                const opt = options.find(o => o.value === value);
                if (opt) {
                    const tag = document.createElement('div');
                    tag.className = 'tag';
                    const parts = opt.textContent.split('|');
                    tag.innerHTML = `
                        <span>${parts[0].trim()}</span>
                        <span class="tag-remove" data-value="${value}">×</span>
                    `;
                    tagsContainer.appendChild(tag);
                }
            });
            
            // Update placeholder
            if (selectedValues.length > 0) {
                searchInput.placeholder = '';
                clearBtn.classList.add('active');
            } else {
                searchInput.placeholder = 'Ketik untuk mencari plat nomor mobil...';
                clearBtn.classList.remove('active');
            }
            
            // Update select element
            Array.from(selectElement.options).forEach(opt => {
                opt.selected = selectedValues.includes(opt.value);
            });
        }
        
        // Function to render options
        function renderOptions(searchTerm = '') {
            dropdown.innerHTML = '';
            const term = searchTerm.toLowerCase();
            
            const filteredOptions = options.filter(opt => {
                const text = opt.textContent.toLowerCase();
                return text.includes(term);
            });
            
            if (filteredOptions.length === 0) {
                dropdown.innerHTML = '<div class="custom-select-no-results">Plat nomor tidak ditemukan</div>';
                return;
            }
            
            filteredOptions.forEach(opt => {
                const optionDiv = document.createElement('div');
                optionDiv.className = 'custom-select-option';
                if (selectedValues.includes(opt.value)) {
                    optionDiv.classList.add('selected');
                }
                
                // Split text by | to separate plat and vehicle info
                const parts = opt.textContent.split('|');
                if (parts.length > 1) {
                    optionDiv.innerHTML = `
                        <span class="plat-number">${parts[0].trim()}</span>
                        <span class="vehicle-info">| ${parts[1].trim()}</span>
                    `;
                } else {
                    optionDiv.textContent = opt.textContent;
                }
                
                optionDiv.addEventListener('click', function() {
                    const value = opt.value;
                    
                    if (selectedValues.includes(value)) {
                        // Remove from selection
                        selectedValues = selectedValues.filter(v => v !== value);
                    } else {
                        // Add to selection
                        selectedValues.push(value);
                    }
                    
                    updateTags();
                    searchInput.value = '';
                    searchInput.focus();
                    renderOptions();
                });
                
                dropdown.appendChild(optionDiv);
            });
        }
        
        // Set initial values from selected options
        Array.from(selectElement.options).forEach(opt => {
            if (opt.selected && opt.value) {
                selectedValues.push(opt.value);
            }
        });
        updateTags();
        
        // Search input events
        searchInput.addEventListener('focus', function() {
            renderOptions(this.value);
            dropdown.classList.add('active');
        });
        
        searchInput.addEventListener('input', function() {
            renderOptions(this.value);
            dropdown.classList.add('active');
        });
        
        // Clear button event
        clearBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            selectedValues = [];
            searchInput.value = '';
            updateTags();
            renderOptions();
            dropdown.classList.add('active');
            searchInput.focus();
        });
        
        // Handle tag remove clicks
        tagsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('tag-remove')) {
                const value = e.target.getAttribute('data-value');
                selectedValues = selectedValues.filter(v => v !== value);
                updateTags();
                renderOptions();
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });
        
        // Validate date range
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            const tanggalDari = new Date(document.getElementById('tanggal_dari').value);
            const tanggalSampai = new Date(document.getElementById('tanggal_sampai').value);
            
            if (tanggalDari > tanggalSampai) {
                e.preventDefault();
                alert('Tanggal Dari tidak boleh lebih besar dari Tanggal Sampai');
                return false;
            }
        });
    });
</script>
@endpush
@endsection
