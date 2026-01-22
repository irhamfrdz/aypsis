@extends('layouts.app')

@section('title', 'Tambah Stock Ban')
@section('page_title', 'Tambah Stock Ban')
 
@push('styles')
<style>
    .custom-select-container {
        position: relative;
        z-index: 50;
    }
    .custom-select-button {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        padding: 0.5rem 1rem;
        background-color: white;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        cursor: pointer;
        text-align: left;
    }
    .custom-select-button:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
    }
    .custom-select-dropdown {
        position: absolute;
        z-index: 9999;
        width: 100%;
        margin-top: 0.25rem;
        background-color: white;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        max-height: 15rem;
        overflow-y: auto;
        display: none;
    }
    .custom-select-search {
        position: sticky;
        top: 0;
        padding: 0.5rem;
        background-color: #f9fafb;
        border-bottom: 1px solid #d1d5db;
    }
    .custom-select-option {
        padding: 0.5rem 1rem;
        cursor: pointer;
    }
    .custom-select-option:hover {
        background-color: #eff6ff;
    }
    .custom-select-option.selected {
        background-color: #dbeafe;
        font-weight: 500;
    }
    .hidden {
        display: none !important;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Stock Ban Baru</h1>
            
            <form action="{{ route('stock-ban.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Nomor Seri (Wajib, Unik) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Seri / Kode Ban <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_seri" value="{{ old('nomor_seri') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nomor_seri') border-red-500 @enderror" required>
                        @error('nomor_seri')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor Bukti (Opsional) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Bukti</label>
                        <input type="text" name="nomor_bukti" value="{{ old('nomor_bukti') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nomor_bukti') border-red-500 @enderror" placeholder="Contoh: INV-2026-001">
                        @error('nomor_bukti')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Merk -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Merk <span class="text-red-500">*</span></label>
                        <input type="text" name="merk" value="{{ old('merk') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('merk') border-red-500 @enderror" required placeholder="Contoh: Bridgestone, Michelin">
                        @error('merk')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ukuran -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran <span class="text-red-500">*</span></label>
                        <input type="text" name="ukuran" value="{{ old('ukuran') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ukuran') border-red-500 @enderror" required placeholder="Contoh: 1000-20, 11R22.5">
                        @error('ukuran')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kondisi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kondisi <span class="text-red-500">*</span></label>
                        <select name="kondisi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kondisi') border-red-500 @enderror" required>
                            <option value="Baru" {{ old('kondisi') == 'Baru' ? 'selected' : '' }}>Baru</option>
                            <option value="Vulkanisir" {{ old('kondisi') == 'Vulkanisir' ? 'selected' : '' }}>Vulkanisir</option>
                            <option value="Bekas" {{ old('kondisi') == 'Bekas' ? 'selected' : '' }}>Bekas</option>
                            <option value="Afkir" {{ old('kondisi') == 'Afkir' ? 'selected' : '' }}>Afkir</option>
                        </select>
                        @error('kondisi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror" required>
                            <option value="Stok" {{ old('status', 'Stok') == 'Stok' ? 'selected' : '' }}>Stok (Tersedia)</option>
                            <option value="Terpakai" {{ old('status') == 'Terpakai' ? 'selected' : '' }}>Terpakai (Dipasang)</option>
                            <option value="Rusak" {{ old('status') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="Hilang" {{ old('status') == 'Hilang' ? 'selected' : '' }}>Hilang</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mobil (Assign to Car) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pasang pada Mobil (Opsional)</label>
                        <div class="custom-select-container" id="mobil-select-container">
                            <input type="hidden" name="mobil_id" id="mobil_id" value="{{ old('mobil_id') }}">
                            
                            <button type="button" id="mobil-select-button" class="custom-select-button">
                                <span id="mobil-selected-text">
                                    @if(old('mobil_id'))
                                        @php $selectedMobil = $mobils->firstWhere('id', old('mobil_id')); @endphp
                                        {{ $selectedMobil ? $selectedMobil->nomor_polisi . ' (' . $selectedMobil->merek . ' - ' . $selectedMobil->jenis . ')' : '-- Tidak Dipasang --' }}
                                    @else
                                        -- Tidak Dipasang --
                                    @endif
                                </span>
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </button>

                            <div id="mobil-select-dropdown" class="custom-select-dropdown">
                                <div class="custom-select-search">
                                    <input type="text" id="mobil-search-input" placeholder="Cari mobil..." class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div class="custom-select-options" id="mobil-options-list">
                                    <div class="custom-select-option" data-value="" data-text="-- Tidak Dipasang --">-- Tidak Dipasang --</div>
                                    @foreach($mobils as $mobil)
                                        <div class="custom-select-option" 
                                             data-value="{{ $mobil->id }}" 
                                             data-search="{{ strtolower($mobil->nomor_polisi . ' ' . $mobil->merek . ' ' . $mobil->jenis) }}"
                                             data-text="{{ $mobil->nomor_polisi }} ({{ $mobil->merek }} - {{ $mobil->jenis }})">
                                            {{ $mobil->nomor_polisi }} ({{ $mobil->merek }} - {{ $mobil->jenis }})
                                        </div>
                                    @endforeach
                                </div>
                                <div id="no-mobil-results" class="hidden p-4 text-center text-sm text-gray-500">
                                    Mobil tidak ditemukan
                                </div>
                            </div>
                        </div>
                        @error('mobil_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Lokasi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" name="lokasi" value="{{ old('lokasi', 'Gudang Utama') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('lokasi') border-red-500 @enderror" required>
                        @error('lokasi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga Beli -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Beli <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" name="harga_beli" value="{{ old('harga_beli', 0) }}" class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('harga_beli') border-red-500 @enderror" required min="0">
                        </div>
                        @error('harga_beli')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Masuk -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Masuk <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_masuk') border-red-500 @enderror" required>
                        @error('tanggal_masuk')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('keterangan') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('stock-ban.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition duration-200">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        function initMobilSelect() {
            const selectContainer = document.getElementById('mobil-select-container');
            const selectButton = document.getElementById('mobil-select-button');
            const selectDropdown = document.getElementById('mobil-select-dropdown');
            const searchInput = document.getElementById('mobil-search-input');
            const optionsList = document.getElementById('mobil-options-list');
            const noResults = document.getElementById('no-mobil-results');
            const hiddenInput = document.getElementById('mobil_id');
            const selectedText = document.getElementById('mobil-selected-text');

            if (!selectContainer || !selectButton || !selectDropdown || !searchInput || !optionsList || !hiddenInput || !selectedText) {
                console.warn('Mobil select: missing required DOM elements, aborting initialization.');
                return;
            }

            // Prevent double-initialization (can cause open->close immediate toggle)
            if (selectButton.dataset.mobilInit === '1') {
                console.log('Mobil select: already initialized, skipping');
                return;
            }
            selectButton.dataset.mobilInit = '1';

            console.log('Mobil select: initializing');

            function updateSelectedState(value) {
                const options = optionsList.querySelectorAll('.custom-select-option');
                options.forEach(opt => {
                    if (opt.getAttribute('data-value') === (value || '').toString()) {
                        opt.classList.add('selected');
                    } else {
                        opt.classList.remove('selected');
                    }
                });
            }

            function selectMobil(id, text) {
                hiddenInput.value = id;
                selectedText.textContent = text;
                closeDropdown();
                updateSelectedState(id);
            }

            // Initialize selected state
            updateSelectedState(hiddenInput.value);

            // Toggle dropdown (robust: append dropdown to body to avoid clipping/z-index issues)
            let dropdownAppended = false;
            const originalParent = selectDropdown.parentNode;
            const placeholder = document.createComment('mobil-select-dropdown-placeholder');

            function openDropdown() {
                console.log('Mobil select: openDropdown');
                // reset search
                searchInput.value = '';
                const options = optionsList.querySelectorAll('.custom-select-option');
                if (!options || options.length === 0) console.warn('Mobil select: no options found');
                options.forEach(opt => opt.classList.remove('hidden'));
                noResults.classList.add('hidden');

                // compute position and move to body to avoid overflow/clipping
                const rect = selectButton.getBoundingClientRect();
                selectDropdown.style.position = 'absolute';
                selectDropdown.style.left = rect.left + window.scrollX + 'px';
                selectDropdown.style.top = rect.bottom + window.scrollY + 'px';
                selectDropdown.style.width = rect.width + 'px';
                selectDropdown.style.display = 'block';
                selectDropdown.style.zIndex = 9999;

                if (!dropdownAppended) {
                    originalParent.replaceChild(placeholder, selectDropdown);
                    document.body.appendChild(selectDropdown);
                    dropdownAppended = true;
                }

                setTimeout(() => searchInput.focus(), 10);
                window.addEventListener('scroll', repositionDropdown);
                window.addEventListener('resize', repositionDropdown);
            }

            function closeDropdown() {
                console.log('Mobil select: closeDropdown');
                selectDropdown.style.display = 'none';
                if (dropdownAppended) {
                    document.body.removeChild(selectDropdown);
                    originalParent.replaceChild(selectDropdown, placeholder);
                    selectDropdown.style.position = '';
                    selectDropdown.style.left = '';
                    selectDropdown.style.top = '';
                    selectDropdown.style.width = '';
                    selectDropdown.style.zIndex = '';
                    dropdownAppended = false;
                }
                window.removeEventListener('scroll', repositionDropdown);
                window.removeEventListener('resize', repositionDropdown);
            }

            function repositionDropdown() {
                if (!dropdownAppended) return;
                const rect = selectButton.getBoundingClientRect();
                selectDropdown.style.left = rect.left + window.scrollX + 'px';
                selectDropdown.style.top = rect.bottom + window.scrollY + 'px';
                selectDropdown.style.width = rect.width + 'px';
            }

            selectButton.addEventListener('click', function(e) {
                console.log('Mobil select: button clicked');
                e.preventDefault();
                e.stopPropagation();
                const isHidden = window.getComputedStyle(selectDropdown).display === 'none';
                if (isHidden) {
                    // close any other open custom dropdowns on page
                    document.querySelectorAll('.custom-select-dropdown').forEach(dd => {
                        if (dd !== selectDropdown) dd.style.display = 'none';
                    });
                    openDropdown();
                } else {
                    closeDropdown();
                }
            });

            // Search functionality
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase().trim();
                const options = optionsList.querySelectorAll('.custom-select-option');
                let count = 0;
                
                options.forEach(opt => {
                    const searchData = opt.getAttribute('data-search') || '';
                    const textData = opt.textContent.toLowerCase();
                    if (searchData.includes(term) || textData.includes(term)) {
                        opt.classList.remove('hidden');
                        count++;
                    } else {
                        opt.classList.add('hidden');
                    }
                });

                noResults.classList.toggle('hidden', count > 0);
            });

            // Option selection
            optionsList.addEventListener('click', function(e) {
                const option = e.target.closest('.custom-select-option');
                if (option) {
                    const val = option.getAttribute('data-value');
                    const txt = option.getAttribute('data-text');
                    selectMobil(val, txt);
                }
            });

            // Close when clicking outside
            document.addEventListener('click', function(e) {
                if (!selectContainer.contains(e.target)) {
                    closeDropdown();
                }
            });

            // Prevent dropdown close when clicking search input
            searchInput.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMobilSelect);
        } else {
            initMobilSelect();
        }
    })();
</script>
@endpush
