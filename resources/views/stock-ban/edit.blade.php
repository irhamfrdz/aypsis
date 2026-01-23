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
                    <!-- Nomor Seri (Wajib, Unik) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Seri / Kode Ban <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_seri" value="{{ old('nomor_seri', $stockBan->nomor_seri) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nomor_seri') border-red-500 @enderror" required>
                        @error('nomor_seri')
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
                        <input type="text" name="merk" value="{{ old('merk', $stockBan->merk) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('merk') border-red-500 @enderror" required placeholder="Contoh: Bridgestone, Michelin">
                        @error('merk')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ukuran -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran <span class="text-red-500">*</span></label>
                        <input type="text" name="ukuran" value="{{ old('ukuran', $stockBan->ukuran) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ukuran') border-red-500 @enderror" required placeholder="Contoh: 1000-20, 11R22.5">
                        @error('ukuran')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kondisi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kondisi <span class="text-red-500">*</span></label>
                        <select name="kondisi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kondisi') border-red-500 @enderror" required>
                            <option value="Baru" {{ old('kondisi', $stockBan->kondisi) == 'Baru' ? 'selected' : '' }}>Baru</option>
                            <option value="Vulkanisir" {{ old('kondisi', $stockBan->kondisi) == 'Vulkanisir' ? 'selected' : '' }}>Vulkanisir</option>
                            <option value="Bekas" {{ old('kondisi', $stockBan->kondisi) == 'Bekas' ? 'selected' : '' }}>Bekas</option>
                            <option value="Afkir" {{ old('kondisi', $stockBan->kondisi) == 'Afkir' ? 'selected' : '' }}>Afkir</option>
                        </select>
                        @error('kondisi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror" required>
                            <option value="Stok" {{ old('status', $stockBan->status) == 'Stok' ? 'selected' : '' }}>Stok (Tersedia)</option>
                            <option value="Terpakai" {{ old('status', $stockBan->status) == 'Terpakai' ? 'selected' : '' }}>Terpakai (Dipasang)</option>
                            <option value="Rusak" {{ old('status', $stockBan->status) == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="Hilang" {{ old('status', $stockBan->status) == 'Hilang' ? 'selected' : '' }}>Hilang</option>
                        </select>
                        @error('status')
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" name="lokasi" value="{{ old('lokasi', $stockBan->lokasi) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('lokasi') border-red-500 @enderror" required>
                        @error('lokasi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga Beli -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Beli <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" name="harga_beli" value="{{ old('harga_beli', $stockBan->harga_beli) }}" class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('harga_beli') border-red-500 @enderror" required min="0">
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
        const selectContainer = document.getElementById('mobil-select-container');
        const selectButton = document.getElementById('mobil-select-button');
        const selectDropdown = document.getElementById('mobil-select-dropdown');
        const searchInput = document.getElementById('mobil-search-input');
        const optionsList = document.getElementById('mobil-options-list');
        const noResults = document.getElementById('no-mobil-results');
        const hiddenInput = document.getElementById('mobil_id');
        const selectedText = document.getElementById('mobil-selected-text');

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
            selectDropdown.style.display = 'none';
            updateSelectedState(id);
        }

        // Toggle dropdown
        selectButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isHidden = window.getComputedStyle(selectDropdown).display === 'none';
            
            if (isHidden) {
                selectDropdown.style.display = 'block';
                searchInput.value = '';
                
                // Reset search results visibility
                const options = optionsList.querySelectorAll('.custom-select-option');
                options.forEach(opt => opt.classList.remove('hidden'));
                noResults.classList.add('hidden');
                
                setTimeout(() => searchInput.focus(), 10);
            } else {
                selectDropdown.style.display = 'none';
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
                selectDropdown.style.display = 'none';
            }
        });

        // Prevent dropdown close when clicking search input
        searchInput.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    })();
</script>
@endpush
