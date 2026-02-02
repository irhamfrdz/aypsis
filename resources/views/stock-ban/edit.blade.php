@extends('layouts.app')

@section('title', 'Edit Stock Ban')
@section('page_title', 'Edit Stock Ban')
 
@push('styles')
<style>
    .custom-select-container {
        position: relative;
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
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Data Stock Ban</h1>
            
            <form action="{{ route('stock-ban.update', $stockBan->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Nomor Seri (Opsional, Unik jika diisi) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Seri / Kode Ban</label>
                        <input type="text" name="nomor_seri" value="{{ old('nomor_seri', $stockBan->nomor_seri) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nomor_seri') border-red-500 @enderror">
                        @error('nomor_seri')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor Bukti (Opsional) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Bukti</label>
                        <input type="text" name="nomor_bukti" value="{{ old('nomor_bukti', $stockBan->nomor_bukti) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nomor_bukti') border-red-500 @enderror" placeholder="Contoh: INV-2026-001">
                        @error('nomor_bukti')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Barang (Dropdown from nama_stock_bans) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Barang <span class="text-red-500">*</span></label>
                        <select name="nama_stock_ban_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama_stock_ban_id') border-red-500 @enderror" required>
                            <option value="">-- Pilih Nama Barang --</option>
                            @foreach($namaStockBans as $item)
                                <option value="{{ $item->id }}" {{ old('nama_stock_ban_id', $stockBan->nama_stock_ban_id) == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                        @error('nama_stock_ban_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Merk -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Merk <span class="text-red-500">*</span></label>
                        <div class="custom-select-container" id="merk-select-container">
                            <!-- Try to match existing merk string to an ID if possible, otherwise just use empty. 
                                 However, edit mode is tricky if we only have string. 
                                 We will try to find matching ID from name. -->
                            @php 
                                $currentMerkId = old('merk_id');
                                if (!$currentMerkId && $stockBan->merk) {
                                    $match = $merkBans->first(function($m) use ($stockBan) {
                                        return strtolower($m->nama ?? $m->nama_merk ?? $m->merk) === strtolower($stockBan->merk);
                                    });
                                    if ($match) $currentMerkId = $match->id;
                                }
                            @endphp
                            <input type="hidden" name="merk_id" id="merk_id" value="{{ $currentMerkId }}">
                            
                            <button type="button" id="merk-select-button" class="custom-select-button">
                                <span id="merk-selected-text">
                                    @if($currentMerkId)
                                        @php $selectedMerk = $merkBans->firstWhere('id', $currentMerkId); @endphp
                                        {{ $selectedMerk ? ($selectedMerk->nama ?? $selectedMerk->nama_merk ?? $selectedMerk->merk) : '-- Pilih Merk --' }}
                                    @else
                                        -- Pilih Merk --
                                    @endif
                                </span>
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </button>

                            <div id="merk-select-dropdown" class="custom-select-dropdown">
                                <div class="custom-select-search">
                                    <input type="text" id="merk-search-input" placeholder="Cari merk..." class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div class="custom-select-options" id="merk-options-list">
                                    <div class="custom-select-option" data-value="" data-text="-- Pilih Merk --">-- Pilih Merk --</div>
                                    @foreach($merkBans as $merk)
                                        @php $merkName = $merk->nama ?? $merk->nama_merk ?? $merk->merk; @endphp
                                        <div class="custom-select-option {{ $currentMerkId == $merk->id ? 'selected' : '' }}" 
                                             data-value="{{ $merk->id }}" 
                                             data-search="{{ strtolower($merkName) }}"
                                             data-text="{{ $merkName }}">
                                            {{ $merkName }}
                                        </div>
                                    @endforeach
                                </div>
                                <div id="no-merk-results" class="hidden p-4 text-center text-sm text-gray-500">
                                    Merk tidak ditemukan
                                </div>
                            </div>
                        </div>
                        @error('merk_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ukuran -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran</label>
                        <input type="text" name="ukuran" value="{{ old('ukuran', $stockBan->ukuran) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ukuran') border-red-500 @enderror" placeholder="Contoh: 1000, 1100, 750">
                        @error('ukuran')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kondisi (Type) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type <span class="text-red-500">*</span></label>
                        <select name="kondisi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kondisi') border-red-500 @enderror" required>
                            <option value="">-- Pilih Type --</option>
                            <option value="asli" {{ old('kondisi', $stockBan->kondisi) == 'asli' ? 'selected' : '' }}>Asli</option>
                            <option value="kanisir" {{ old('kondisi', $stockBan->kondisi) == 'kanisir' ? 'selected' : '' }}>Kanisir</option>
                            <option value="afkir" {{ old('kondisi', $stockBan->kondisi) == 'afkir' ? 'selected' : '' }}>Afkir</option>
                            <option value="kaleng" {{ old('kondisi', $stockBan->kondisi) == 'kaleng' ? 'selected' : '' }}>Kaleng</option>
                            <option value="karung" {{ old('kondisi', $stockBan->kondisi) == 'karung' ? 'selected' : '' }}>Karung</option>
                            <option value="liter" {{ old('kondisi', $stockBan->kondisi) == 'liter' ? 'selected' : '' }}>Liter</option>
                            <option value="pail" {{ old('kondisi', $stockBan->kondisi) == 'pail' ? 'selected' : '' }}>Pail</option>
                            <option value="pcs" {{ old('kondisi', $stockBan->kondisi) == 'pcs' ? 'selected' : '' }}>Pcs</option>
                        </select>
                        @error('kondisi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status (Conditional for Ban Luar) -->
                    <div id="status-ban-luar-container" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status_ban_luar" id="status_ban_luar" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status_ban_luar') border-red-500 @enderror">
                            <option value="">-- Pilih Status --</option>
                            <option value="kawat" {{ old('status_ban_luar', $stockBan->status_ban_luar) == 'kawat' ? 'selected' : '' }}>Kawat</option>
                            <option value="benang" {{ old('status_ban_luar', $stockBan->status_ban_luar) == 'benang' ? 'selected' : '' }}>Benang</option>
                            <option value="claim" {{ old('status_ban_luar', $stockBan->status_ban_luar) == 'claim' ? 'selected' : '' }}>Claim</option>
                            <option value="no seri hilang" {{ old('status_ban_luar', $stockBan->status_ban_luar) == 'no seri hilang' ? 'selected' : '' }}>No Seri Hilang</option>
                        </select>
                        @error('status_ban_luar')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>



                    <!-- Mobil (Assign to Car) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pasang pada Mobil (Opsional)</label>
                        <div class="custom-select-container" id="mobil-select-container">
                            <input type="hidden" name="mobil_id" id="mobil_id" value="{{ old('mobil_id', $stockBan->mobil_id) }}">
                            
                            <button type="button" id="mobil-select-button" class="custom-select-button">
                                <span id="mobil-selected-text">
                                    @if(old('mobil_id', $stockBan->mobil_id))
                                        @php $selectedMobil = $mobils->firstWhere('id', old('mobil_id', $stockBan->mobil_id)); @endphp
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
                                    <div class="custom-select-option {{ !old('mobil_id', $stockBan->mobil_id) ? 'selected' : '' }}" data-value="" data-text="-- Tidak Dipasang --">-- Tidak Dipasang --</div>
                                    @foreach($mobils as $mobil)
                                        <div class="custom-select-option {{ old('mobil_id', $stockBan->mobil_id) == $mobil->id ? 'selected' : '' }}" 
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                        <div class="custom-select-container" id="lokasi-select-container">
                            <input type="hidden" name="lokasi" id="lokasi" value="{{ old('lokasi', $stockBan->lokasi) }}">
                            
                            <button type="button" id="lokasi-select-button" class="custom-select-button">
                                <span id="lokasi-selected-text">
                                    @if(old('lokasi', $stockBan->lokasi))
                                        {{ old('lokasi', $stockBan->lokasi) }}
                                    @else
                                        -- Pilih Lokasi --
                                    @endif
                                </span>
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </button>

                            <div id="lokasi-select-dropdown" class="custom-select-dropdown">
                                <div class="custom-select-search">
                                    <input type="text" id="lokasi-search-input" placeholder="Cari lokasi..." class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div class="custom-select-options" id="lokasi-options-list">
                                    <div class="custom-select-option" data-value="" data-text="-- Pilih Lokasi --">-- Pilih Lokasi --</div>
                                    @foreach($gudangs as $gudang)
                                        <div class="custom-select-option {{ old('lokasi', $stockBan->lokasi) == $gudang->nama_gudang ? 'selected' : '' }}" 
                                             data-value="{{ $gudang->nama_gudang }}" 
                                             data-search="{{ strtolower($gudang->nama_gudang) }}"
                                             data-text="{{ $gudang->nama_gudang }}">
                                            {{ $gudang->nama_gudang }}
                                        </div>
                                    @endforeach
                                </div>
                                <div id="no-lokasi-results" class="hidden p-4 text-center text-sm text-gray-500">
                                    Lokasi tidak ditemukan
                                </div>
                            </div>
                        </div>
                        @error('lokasi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Penerima (Conditional for Ban Luar) -->
                    <div id="penerima-container" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Penerima</label>
                        <div class="custom-select-container" id="penerima-select-container">
                            <input type="hidden" name="penerima_id" id="penerima_id" value="{{ old('penerima_id', $stockBan->penerima_id) }}">
                            
                            <button type="button" id="penerima-select-button" class="custom-select-button">
                                <span id="penerima-selected-text">
                                    @if(old('penerima_id', $stockBan->penerima_id))
                                        @php $selectedPenerima = $karyawans->firstWhere('id', old('penerima_id', $stockBan->penerima_id)); @endphp
                                        {{ $selectedPenerima ? $selectedPenerima->nama_lengkap : '-- Pilih Penerima --' }}
                                    @else
                                        -- Pilih Penerima --
                                    @endif
                                </span>
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </button>

                            <div id="penerima-select-dropdown" class="custom-select-dropdown">
                                <div class="custom-select-search">
                                    <input type="text" id="penerima-search-input" placeholder="Cari penerima..." class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div class="custom-select-options" id="penerima-options-list">
                                    <div class="custom-select-option {{ !old('penerima_id', $stockBan->penerima_id) ? 'selected' : '' }}" data-value="" data-text="-- Pilih Penerima --">-- Pilih Penerima --</div>
                                    @foreach($karyawans as $karyawan)
                                        <div class="custom-select-option {{ old('penerima_id', $stockBan->penerima_id) == $karyawan->id ? 'selected' : '' }}" 
                                             data-value="{{ $karyawan->id }}" 
                                             data-search="{{ strtolower($karyawan->nama_lengkap) }}"
                                             data-text="{{ $karyawan->nama_lengkap }}">
                                            {{ $karyawan->nama_lengkap }}
                                        </div>
                                    @endforeach
                                </div>
                                <div id="no-penerima-results" class="hidden p-4 text-center text-sm text-gray-500">
                                    Penerima tidak ditemukan
                                </div>
                            </div>
                        </div>
                        @error('penerima_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga Beli -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Beli</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" name="harga_beli" value="{{ old('harga_beli', $stockBan->harga_beli) }}" class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('harga_beli') border-red-500 @enderror" min="0">
                        </div>
                        @error('harga_beli')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Masuk -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Masuk <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', $stockBan->tanggal_masuk->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_masuk') border-red-500 @enderror" required>
                        @error('tanggal_masuk')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('keterangan', $stockBan->keterangan) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('stock-ban.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition duration-200">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200">
                        Simpan Perubahan
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

            if (!selectContainer || !selectButton || !selectDropdown || !searchInput || !optionsList || !hiddenInput || !selectedText) return;
            if (selectButton.dataset.mobilInit === '1') return;
            selectButton.dataset.mobilInit = '1';

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

            updateSelectedState(hiddenInput.value);

            let dropdownAppended = false;
            const originalParent = selectDropdown.parentNode;
            const placeholder = document.createComment('mobil-select-dropdown-placeholder');

            function openDropdown() {
                searchInput.value = '';
                const options = optionsList.querySelectorAll('.custom-select-option');
                options.forEach(opt => opt.classList.remove('hidden'));
                noResults.classList.add('hidden');

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
                selectDropdown.style.display = 'none';
                if (dropdownAppended) {
                    document.body.removeChild(selectDropdown);
                    originalParent.replaceChild(selectDropdown, placeholder);
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
                e.preventDefault();
                e.stopPropagation();
                if (window.getComputedStyle(selectDropdown).display === 'none') {
                    document.querySelectorAll('.custom-select-dropdown').forEach(dd => {
                        if (dd !== selectDropdown) dd.style.display = 'none';
                    });
                    openDropdown();
                } else {
                    closeDropdown();
                }
            });

            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase().trim();
                const options = optionsList.querySelectorAll('.custom-select-option');
                let count = 0;
                options.forEach(opt => {
                    const searchData = opt.getAttribute('data-search') || '';
                    if (searchData.includes(term) || opt.textContent.toLowerCase().includes(term)) {
                        opt.classList.remove('hidden');
                        count++;
                    } else {
                        opt.classList.add('hidden');
                    }
                });
                noResults.classList.toggle('hidden', count > 0);
            });

            optionsList.addEventListener('click', function(e) {
                const option = e.target.closest('.custom-select-option');
                if (option) selectMobil(option.getAttribute('data-value'), option.getAttribute('data-text'));
            });

            document.addEventListener('click', function(e) {
                if (!selectContainer.contains(e.target)) closeDropdown();
            });

            searchInput.addEventListener('click', e => e.stopPropagation());
        }

        function initPenerimaSelect() {
            const selectContainer = document.getElementById('penerima-select-container');
            const selectButton = document.getElementById('penerima-select-button');
            const selectDropdown = document.getElementById('penerima-select-dropdown');
            const searchInput = document.getElementById('penerima-search-input');
            const optionsList = document.getElementById('penerima-options-list');
            const noResults = document.getElementById('no-penerima-results');
            const hiddenInput = document.getElementById('penerima_id');
            const selectedText = document.getElementById('penerima-selected-text');

            if (!selectContainer || !selectButton || !selectDropdown || !searchInput || !optionsList || !hiddenInput || !selectedText) return;
            if (selectButton.dataset.penerimaInit === '1') return;
            selectButton.dataset.penerimaInit = '1';

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

            function selectPenerima(id, text) {
                hiddenInput.value = id;
                selectedText.textContent = text;
                closeDropdown();
                updateSelectedState(id);
            }

            updateSelectedState(hiddenInput.value);

            let dropdownAppended = false;
            const originalParent = selectDropdown.parentNode;
            const placeholder = document.createComment('penerima-select-dropdown-placeholder');

            function openDropdown() {
                searchInput.value = '';
                const options = optionsList.querySelectorAll('.custom-select-option');
                options.forEach(opt => opt.classList.remove('hidden'));
                noResults.classList.add('hidden');

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
                selectDropdown.style.display = 'none';
                if (dropdownAppended) {
                    document.body.removeChild(selectDropdown);
                    originalParent.replaceChild(selectDropdown, placeholder);
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
                e.preventDefault();
                e.stopPropagation();
                if (window.getComputedStyle(selectDropdown).display === 'none') {
                    document.querySelectorAll('.custom-select-dropdown').forEach(dd => {
                        if (dd !== selectDropdown) dd.style.display = 'none';
                    });
                    openDropdown();
                } else {
                    closeDropdown();
                }
            });

            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase().trim();
                const options = optionsList.querySelectorAll('.custom-select-option');
                let count = 0;
                options.forEach(opt => {
                    const searchData = opt.getAttribute('data-search') || '';
                    if (searchData.includes(term) || opt.textContent.toLowerCase().includes(term)) {
                        opt.classList.remove('hidden');
                        count++;
                    } else {
                        opt.classList.add('hidden');
                    }
                });
                noResults.classList.toggle('hidden', count > 0);
            });

            optionsList.addEventListener('click', function(e) {
                const option = e.target.closest('.custom-select-option');
                if (option) selectPenerima(option.getAttribute('data-value'), option.getAttribute('data-text'));
            });

            document.addEventListener('click', function(e) {
                if (!selectContainer.contains(e.target)) closeDropdown();
            });

            searchInput.addEventListener('click', e => e.stopPropagation());
        }

        function initMerkSelect() {
            const selectContainer = document.getElementById('merk-select-container');
            const selectButton = document.getElementById('merk-select-button');
            const selectDropdown = document.getElementById('merk-select-dropdown');
            const searchInput = document.getElementById('merk-search-input');
            const optionsList = document.getElementById('merk-options-list');
            const noResults = document.getElementById('no-merk-results');
            const hiddenInput = document.getElementById('merk_id');
            const selectedText = document.getElementById('merk-selected-text');

            if (!selectContainer || !selectButton || !selectDropdown || !searchInput || !optionsList || !hiddenInput || !selectedText) return;
            if (selectButton.dataset.merkInit === '1') return;
            selectButton.dataset.merkInit = '1';

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

            function selectMerk(id, text) {
                hiddenInput.value = id;
                selectedText.textContent = text;
                closeDropdown();
                updateSelectedState(id);
            }

            updateSelectedState(hiddenInput.value);

            let dropdownAppended = false;
            const originalParent = selectDropdown.parentNode;
            const placeholder = document.createComment('merk-select-dropdown-placeholder');

            function openDropdown() {
                searchInput.value = '';
                const options = optionsList.querySelectorAll('.custom-select-option');
                options.forEach(opt => opt.classList.remove('hidden'));
                noResults.classList.add('hidden');

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
                selectDropdown.style.display = 'none';
                if (dropdownAppended) {
                    document.body.removeChild(selectDropdown);
                    originalParent.replaceChild(selectDropdown, placeholder);
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
                e.preventDefault();
                e.stopPropagation();
                if (window.getComputedStyle(selectDropdown).display === 'none') {
                    document.querySelectorAll('.custom-select-dropdown').forEach(dd => {
                        if (dd !== selectDropdown) dd.style.display = 'none';
                    });
                    openDropdown();
                } else {
                    closeDropdown();
                }
            });

            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase().trim();
                const options = optionsList.querySelectorAll('.custom-select-option');
                let count = 0;
                options.forEach(opt => {
                    const searchData = opt.getAttribute('data-search') || '';
                    if (searchData.includes(term) || opt.textContent.toLowerCase().includes(term)) {
                        opt.classList.remove('hidden');
                        count++;
                    } else {
                        opt.classList.add('hidden');
                    }
                });
                noResults.classList.toggle('hidden', count > 0);
            });

            optionsList.addEventListener('click', function(e) {
                const option = e.target.closest('.custom-select-option');
                if (option) selectMerk(option.getAttribute('data-value'), option.getAttribute('data-text'));
            });

            document.addEventListener('click', function(e) {
                if (!selectContainer.contains(e.target)) closeDropdown();
            });

            searchInput.addEventListener('click', e => e.stopPropagation());
        }

        function initLokasiSelect() {
            const selectContainer = document.getElementById('lokasi-select-container');
            const selectButton = document.getElementById('lokasi-select-button');
            const selectDropdown = document.getElementById('lokasi-select-dropdown');
            const searchInput = document.getElementById('lokasi-search-input');
            const optionsList = document.getElementById('lokasi-options-list');
            const noResults = document.getElementById('no-lokasi-results');
            const hiddenInput = document.getElementById('lokasi');
            const selectedText = document.getElementById('lokasi-selected-text');

            if (!selectContainer || !selectButton || !selectDropdown || !searchInput || !optionsList || !hiddenInput || !selectedText) return;
            if (selectButton.dataset.lokasiInit === '1') return;
            selectButton.dataset.lokasiInit = '1';

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

            function selectLokasi(value, text) {
                hiddenInput.value = value;
                selectedText.textContent = text;
                closeDropdown();
                updateSelectedState(value);
            }

            updateSelectedState(hiddenInput.value);

            let dropdownAppended = false;
            const originalParent = selectDropdown.parentNode;
            const placeholder = document.createComment('lokasi-select-dropdown-placeholder');

            function openDropdown() {
                searchInput.value = '';
                const options = optionsList.querySelectorAll('.custom-select-option');
                options.forEach(opt => opt.classList.remove('hidden'));
                noResults.classList.add('hidden');

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
                selectDropdown.style.display = 'none';
                if (dropdownAppended) {
                    document.body.removeChild(selectDropdown);
                    originalParent.replaceChild(selectDropdown, placeholder);
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
                e.preventDefault();
                e.stopPropagation();
                if (window.getComputedStyle(selectDropdown).display === 'none') {
                    document.querySelectorAll('.custom-select-dropdown').forEach(dd => {
                        if (dd !== selectDropdown) dd.style.display = 'none';
                    });
                    openDropdown();
                } else {
                    closeDropdown();
                }
            });

            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase().trim();
                const options = optionsList.querySelectorAll('.custom-select-option');
                let count = 0;
                options.forEach(opt => {
                    const searchData = opt.getAttribute('data-search') || '';
                    if (searchData.includes(term) || opt.textContent.toLowerCase().includes(term)) {
                        opt.classList.remove('hidden');
                        count++;
                    } else {
                        opt.classList.add('hidden');
                    }
                });
                noResults.classList.toggle('hidden', count > 0);
            });

            optionsList.addEventListener('click', function(e) {
                const option = e.target.closest('.custom-select-option');
                if (option) selectLokasi(option.getAttribute('data-value'), option.getAttribute('data-text'));
            });

            document.addEventListener('click', function(e) {
                if (!selectContainer.contains(e.target)) closeDropdown();
            });

            searchInput.addEventListener('click', e => e.stopPropagation());
        }

        function initAll() {
            initMobilSelect();
            initPenerimaSelect();
            initMerkSelect();
            initLokasiSelect();
            initBanBatchLogic();
        }

        function initBanBatchLogic() {
            const namaBarangSelect = document.querySelector('select[name="nama_stock_ban_id"]');
            const nomorSeriContainer = document.querySelector('input[name="nomor_seri"]').closest('div');
            const merkContainer = document.getElementById('merk-select-container').closest('div');
            const typeSelect = document.querySelector('select[name="kondisi"]');
            const hargaBeliContainer = document.querySelector('input[name="harga_beli"]').closest('div');
            const mobilContainer = document.getElementById('mobil-select-container').closest('div');
            
            // Create Stock Qty field if it doesn't exist
            let qtyWrapper = document.getElementById('qty-container');
            if (!qtyWrapper) {
                qtyWrapper = document.createElement('div');
                qtyWrapper.id = 'qty-container';
                qtyWrapper.className = 'hidden';
                qtyWrapper.innerHTML = `
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stock (Qty) <span class="text-red-500">*</span></label>
                    <input type="number" name="qty" value="{{ old('qty', $stockBan->qty ?? 0) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" min="0">
                `;
                hargaBeliContainer.parentNode.insertBefore(qtyWrapper, hargaBeliContainer);
            }

            // Store original options
            const originalTypeOptions = Array.from(typeSelect.options).map(opt => ({ value: opt.value, text: opt.text }));

            function checkItemType() {
                const selectedOption = namaBarangSelect.options[namaBarangSelect.selectedIndex];
                const selectedText = selectedOption ? selectedOption.text.toLowerCase() : '';
                
                const isBanDalam = selectedText.includes('ban dalam');
                const isBanPerut = selectedText.includes('ban perut');
                const isLockKontainer = selectedText.includes('lock kontainer');
                const isRingVelg = selectedText.includes('ring velg');
                const isVelg = selectedText.includes('velg');
                const isBulk = isBanDalam || isBanPerut || isLockKontainer || isRingVelg || isVelg;
                const isBanLuar = selectedText.includes('ban luar');
                
                const penerimaContainer = document.getElementById('penerima-container');
                const statusBanLuarContainer = document.getElementById('status-ban-luar-container');
                const statusBanLuarSelect = document.getElementById('status_ban_luar');

                // Penerima Visibility
                if (penerimaContainer) {
                    if (isBanLuar) {
                        penerimaContainer.classList.remove('hidden');
                    } else {
                        penerimaContainer.classList.add('hidden');
                    }
                }

                // Status Ban Luar Visibility
                if (statusBanLuarContainer) {
                    if (isBanLuar) {
                        statusBanLuarContainer.classList.remove('hidden');
                        if (statusBanLuarSelect) statusBanLuarSelect.setAttribute('required', 'required');
                    } else {
                        statusBanLuarContainer.classList.add('hidden');
                        if (statusBanLuarSelect) {
                            statusBanLuarSelect.removeAttribute('required');
                            statusBanLuarSelect.value = '';
                        }
                    }
                }

                if (isBulk) {
                    nomorSeriContainer.classList.add('hidden');
                    merkContainer.classList.add('hidden');
                    mobilContainer.classList.add('hidden');
                    qtyWrapper.classList.remove('hidden');
                    
                    document.querySelector('input[name="nomor_seri"]').removeAttribute('required');
                    document.querySelector('input[name="qty"]').setAttribute('required', 'required');
                    typeSelect.setAttribute('name', 'type');

                    // Adjust Ukuran label
                    const ukuranInput = document.querySelector('input[name="ukuran"]');
                    const ukuranContainer = ukuranInput ? ukuranInput.closest('div') : null;
                    if (ukuranContainer) {
                        const label = ukuranContainer.querySelector('label');
                        if (isBanDalam || isBanPerut) {
                            ukuranContainer.classList.add('hidden');
                        } else {
                            ukuranContainer.classList.remove('hidden');
                            if (isRingVelg || isVelg) {
                                label.textContent = 'Lobang';
                            } else {
                                label.textContent = 'Ukuran';
                            }
                        }
                    }

                    // Adjust Type Options
                    typeSelect.innerHTML = '';
                    if (isBanDalam) {
                        const opt = document.createElement('option');
                        opt.value = 'pcs';
                        opt.text = 'Pcs';
                        opt.selected = true;
                        typeSelect.appendChild(opt);
                    } else {
                        originalTypeOptions.forEach(opt => {
                            const option = document.createElement('option');
                            option.value = opt.value;
                            option.text = opt.text;
                            if (opt.value === "{{ old('type', $stockBan->type ?? 'pcs') }}") option.selected = true;
                            typeSelect.appendChild(option);
                        });
                    }
                } else {
                    nomorSeriContainer.classList.remove('hidden');
                    merkContainer.classList.remove('hidden');
                    mobilContainer.classList.remove('hidden');
                    qtyWrapper.classList.add('hidden');
                    
                    document.querySelector('input[name="qty"]').removeAttribute('required');
                    typeSelect.setAttribute('name', 'kondisi');

                    // Restore Type Options
                    typeSelect.innerHTML = '';
                    originalTypeOptions.forEach(opt => {
                        const option = document.createElement('option');
                        option.value = opt.value;
                        option.text = opt.text;
                        if (opt.value === "{{ old('kondisi', $stockBan->kondisi) }}") option.selected = true;
                        typeSelect.appendChild(option);
                    });

                    const ukuranInput = document.querySelector('input[name="ukuran"]');
                    const ukuranContainer = ukuranInput ? ukuranInput.closest('div') : null;
                    if (ukuranContainer) {
                        ukuranContainer.classList.remove('hidden');
                        const label = ukuranContainer.querySelector('label');
                        if (label) label.textContent = 'Ukuran';
                    }
                }
            }

            if (namaBarangSelect) {
                namaBarangSelect.addEventListener('change', checkItemType);
                checkItemType();
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAll);
        } else {
            initAll();
        }
    })();
</script>
@endpush
