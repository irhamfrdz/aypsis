@extends('layouts.app')

@section('title', 'Edit Stock Ban Luar Batam')
@section('page_title', 'Edit Stock Ban Luar Batam')

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
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Stock Ban Luar Batam</h1>
            
            <form action="{{ route('stock-ban-luar-batam.update', $stockBan->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Nomor Seri -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Seri / Kode Ban</label>
                        <input type="text" 
                               name="nomor_seri" 
                               value="{{ old('nomor_seri', $stockBan->nomor_seri) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nomor_seri') border-red-500 @enderror"
                               placeholder="Masukkan nomor seri ban">
                        @error('nomor_seri')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor Bukti -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Bukti</label>
                        <input type="text" name="nomor_bukti" value="{{ old('nomor_bukti', $stockBan->nomor_bukti) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nomor_bukti') border-red-500 @enderror">
                        @error('nomor_bukti')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor Faktur -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Faktur</label>
                        <input type="text" name="nomor_faktur" value="{{ old('nomor_faktur', $stockBan->nomor_faktur) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nomor_faktur') border-red-500 @enderror">
                        @error('nomor_faktur')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Barang -->
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
                        <div class="custom-select-container" id="merk-select-container-edit">
                            @php
                                $selectedMerkId = old('merk_id');
                                if (!$selectedMerkId) {
                                    $currentMerk = $merkBans->firstWhere('nama', $stockBan->merk);
                                    $selectedMerkId = $currentMerk ? $currentMerk->id : '';
                                }
                                $selectedMerk = $merkBans->firstWhere('id', $selectedMerkId);
                            @endphp
                            <input type="hidden" name="merk_id" id="merk_id_edit" value="{{ $selectedMerkId }}">
                            <button type="button" id="merk-select-button-edit" class="custom-select-button">
                                <span id="merk-selected-text-edit">
                                    {{ $selectedMerk ? $selectedMerk->nama : ($stockBan->merk ?: '-- Pilih Merk --') }}
                                </span>
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </button>
                            <div id="merk-select-dropdown-edit" class="custom-select-dropdown">
                                <div class="custom-select-search">
                                    <input type="text" id="merk-search-input-edit" placeholder="Cari merk..." class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div class="custom-select-options" id="merk-options-list-edit">
                                    <div class="custom-select-option" data-value="" data-text="-- Pilih Merk --">-- Pilih Merk --</div>
                                    @foreach($merkBans as $merk)
                                        <div class="custom-select-option {{ $selectedMerkId == $merk->id ? 'selected' : '' }}" 
                                             data-value="{{ $merk->id }}" 
                                             data-search="{{ strtolower($merk->nama) }}"
                                             data-text="{{ $merk->nama }}">
                                            {{ $merk->nama }}
                                        </div>
                                    @endforeach
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
                        <input type="text" name="ukuran" value="{{ old('ukuran', $stockBan->ukuran) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ukuran') border-red-500 @enderror">
                        @error('ukuran')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kondisi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kondisi <span class="text-red-500">*</span></label>
                        <select name="kondisi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kondisi') border-red-500 @enderror" required>
                            <option value="">-- Pilih Kondisi --</option>
                            <option value="asli" {{ old('kondisi', $stockBan->kondisi) == 'asli' ? 'selected' : '' }}>Asli</option>
                            <option value="kanisir" {{ old('kondisi', $stockBan->kondisi) == 'kanisir' ? 'selected' : '' }}>Kanisir</option>
                            <option value="afkir" {{ old('kondisi', $stockBan->kondisi) == 'afkir' ? 'selected' : '' }}>Afkir</option>
                            <option value="rusak" {{ old('kondisi', $stockBan->kondisi) == 'rusak' ? 'selected' : '' }}>Rusak</option>
                        </select>
                        @error('kondisi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Lokasi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                        <div class="custom-select-container" id="lokasi-select-container-edit">
                            <input type="hidden" name="lokasi" id="lokasi_edit" value="{{ old('lokasi', $stockBan->lokasi) }}">
                            <button type="button" id="lokasi-select-button-edit" class="custom-select-button">
                                <span id="lokasi-selected-text-edit">
                                    {{ old('lokasi', $stockBan->lokasi ?: '-- Pilih Lokasi --') }}
                                </span>
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </button>
                            <div id="lokasi-select-dropdown-edit" class="custom-select-dropdown">
                                <div class="custom-select-search">
                                    <input type="text" id="lokasi-search-input-edit" placeholder="Cari lokasi..." class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div class="custom-select-options" id="lokasi-options-list-edit">
                                    <div class="custom-select-option" data-value="" data-text="-- Pilih Lokasi --">-- Pilih Lokasi --</div>
                                    @foreach($masterGudangBans as $g)
                                        <div class="custom-select-option {{ old('lokasi', $stockBan->lokasi) == $g->nama_gudang ? 'selected' : '' }}" 
                                             data-value="{{ $g->nama_gudang }}" 
                                             data-search="{{ strtolower($g->nama_gudang) }}"
                                             data-text="{{ $g->nama_gudang }}">
                                            {{ $g->nama_gudang }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @error('lokasi')
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
                        <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', $stockBan->tanggal_masuk ? $stockBan->tanggal_masuk->format('Y-m-d') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_masuk') border-red-500 @enderror" required>
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
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function initMerkSelectEdit() {
        const selectContainer = document.getElementById('merk-select-container-edit');
        const selectButton = document.getElementById('merk-select-button-edit');
        const selectDropdown = document.getElementById('merk-select-dropdown-edit');
        const searchInput = document.getElementById('merk-search-input-edit');
        const optionsList = document.getElementById('merk-options-list-edit');
        const hiddenInput = document.getElementById('merk_id_edit');
        const selectedText = document.getElementById('merk-selected-text-edit');

        if (!selectContainer || !selectButton) return;

        selectButton.addEventListener('click', () => {
            selectDropdown.style.display = selectDropdown.style.display === 'block' ? 'none' : 'block';
            if (selectDropdown.style.display === 'block') searchInput.focus();
        });

        optionsList.addEventListener('click', (e) => {
            const option = e.target.closest('.custom-select-option');
            if (option) {
                hiddenInput.value = option.dataset.value;
                selectedText.textContent = option.dataset.text;
                
                // Remove selected class from others
                optionsList.querySelectorAll('.custom-select-option').forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                
                selectDropdown.style.display = 'none';
            }
        });

        searchInput.addEventListener('input', () => {
            const term = searchInput.value.toLowerCase();
            const options = optionsList.querySelectorAll('.custom-select-option');
            options.forEach(opt => {
                const text = opt.dataset.search || '';
                opt.style.display = text.includes(term) ? 'block' : 'none';
            });
        });

        document.addEventListener('click', (e) => {
            if (!selectContainer.contains(e.target)) selectDropdown.style.display = 'none';
        });
    }

    function initLokasiSelectEdit() {
        const selectContainer = document.getElementById('lokasi-select-container-edit');
        const selectButton = document.getElementById('lokasi-select-button-edit');
        const selectDropdown = document.getElementById('lokasi-select-dropdown-edit');
        const searchInput = document.getElementById('lokasi-search-input-edit');
        const optionsList = document.getElementById('lokasi-options-list-edit');
        const hiddenInput = document.getElementById('lokasi_edit');
        const selectedText = document.getElementById('lokasi-selected-text-edit');

        if (!selectContainer || !selectButton) return;

        selectButton.addEventListener('click', () => {
            selectDropdown.style.display = selectDropdown.style.display === 'block' ? 'none' : 'block';
            if (selectDropdown.style.display === 'block') searchInput.focus();
        });

        optionsList.addEventListener('click', (e) => {
            const option = e.target.closest('.custom-select-option');
            if (option) {
                hiddenInput.value = option.dataset.value;
                selectedText.textContent = option.dataset.text;
                
                // Remove selected class from others
                optionsList.querySelectorAll('.custom-select-option').forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                
                selectDropdown.style.display = 'none';
            }
        });

        searchInput.addEventListener('input', () => {
            const term = searchInput.value.toLowerCase();
            const options = optionsList.querySelectorAll('.custom-select-option');
            options.forEach(opt => {
                const text = opt.dataset.search || '';
                opt.style.display = text.includes(term) ? 'block' : 'none';
            });
        });

        document.addEventListener('click', (e) => {
            if (!selectContainer.contains(e.target)) selectDropdown.style.display = 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initMerkSelectEdit();
        initLokasiSelectEdit();
    });
</script>
@endpush
