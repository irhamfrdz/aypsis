@extends('layouts.app')

@section('title', 'Tambah Surat Jalan Tarik Kosong Batam')

@push('styles')
<style>
    .kontainer-option:hover, .location-option:hover, .warehouse-option:hover {
        background-color: #f3f4f6;
    }
    .kontainer-option.selected, .location-option.selected, .warehouse-option.selected {
        background-color: #eef2ff;
        border-left: 4px solid #4f46e5;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-semibold text-gray-900">Tambah Surat Jalan</h1>
                    <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded">Tarik Kosong Batam</span>
                </div>
                <p class="text-xs text-gray-600 mt-1">Buat surat jalan tarik kosong baru (Batam)</p>
            </div>
            <a href="{{ route('surat-jalan-tarik-kosong-batam.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('surat-jalan-tarik-kosong-batam.store') }}" method="POST" class="p-4" id="sjtk-form">
            @csrf

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                    <div class="font-medium">Terdapat kesalahan pada form:</div>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Dasar</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat Jalan <span class="text-red-600">*</span></label>
                    <input type="date"
                           name="tanggal_surat_jalan"
                           id="tanggal_surat_jalan"
                           value="{{ old('tanggal_surat_jalan', date('Y-m-d')) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_surat_jalan') border-red-500 @enderror">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Surat Jalan <span class="text-red-600">*</span></label>
                    <div class="flex">
                        <input type="text"
                               name="no_surat_jalan"
                               id="no_surat_jalan"
                               value="{{ old('no_surat_jalan') }}"
                               required
                               readonly
                               placeholder="SJTK/YYYY/MM/XXXX"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg bg-gray-50 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('no_surat_jalan') border-red-500 @enderror">
                        <button type="button"
                                id="btn-generate-number"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-r-lg text-sm">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>




                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengambilan</label>
                    <input type="text"
                           name="tujuan_pengambilan"
                           id="tujuan_pengambilan_search"
                           placeholder="Cari lokasi pengambilan..."
                           autocomplete="off"
                           value="{{ old('tujuan_pengambilan') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                    <div id="tujuan_pengambilan_dropdown" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto mt-1">
                        @foreach($locations as $loc)
                            <div class="location-option px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-50 text-sm"
                                 data-value="{{ $loc }}">
                                {{ $loc }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengiriman</label>
                    <input type="text"
                           name="tujuan_pengiriman"
                           id="tujuan_pengiriman_search"
                           placeholder="Cari lokasi pengiriman..."
                           autocomplete="off"
                           value="{{ old('tujuan_pengiriman') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                    <div id="tujuan_pengiriman_dropdown" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto mt-1">
                        @foreach($warehouses as $wh)
                            <div class="warehouse-option px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-50 text-sm"
                                 data-value="{{ $wh }}">
                                {{ $wh }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Armada Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Armada</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir Utama</label>
                    <select name="supir"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Supir --</option>
                        @foreach($supirs as $supir)
                            <option value="{{ $supir->nama_lengkap }}" 
                                    data-plat="{{ $supir->plat }}"
                                    {{ old('supir') == $supir->nama_lengkap ? 'selected' : '' }}>
                                {{ $supir->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Plat / Armada</label>
                    <select name="no_plat"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Armada --</option>
                        @foreach($mobils as $mobil)
                            <option value="{{ $mobil->nomor_polisi }}" {{ old('no_plat') == $mobil->nomor_polisi ? 'selected' : '' }}>
                                {{ $mobil->nomor_polisi }} ({{ $mobil->merek }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir Cadangan</label>
                    <select name="supir2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Supir --</option>
                        @foreach($supirs as $supir)
                            <option value="{{ $supir->nama_lengkap }}" {{ old('supir2') == $supir->nama_lengkap ? 'selected' : '' }}>
                                {{ $supir->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                    <select name="kenek"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Kenek --</option>
                        @foreach($keneks as $kenek)
                            <option value="{{ $kenek->nama_lengkap }}" {{ old('kenek') == $kenek->nama_lengkap ? 'selected' : '' }}>
                                {{ $kenek->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kontainer Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Kontainer</h3>
                </div>

                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Kontainer</label>
                    <input type="text"
                           id="no_kontainer_search"
                           placeholder="Cari nomor kontainer..."
                           autocomplete="off"
                           value="{{ old('no_kontainer') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                    <input type="hidden" name="no_kontainer" id="no_kontainer_hidden" value="{{ old('no_kontainer') }}">
                    
                    <div id="no_kontainer_dropdown" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto mt-1">
                        @foreach($kontainers as $kontainer)
                            <div class="kontainer-option px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-50 text-sm"
                                 data-value="{{ $kontainer->nomor_seri_gabungan }}"
                                 data-ukuran="{{ $kontainer->ukuran }}">
                                <div class="font-medium">{{ $kontainer->nomor_seri_gabungan }}</div>
                                <div class="text-xs text-gray-500">{{ $kontainer->ukuran }}'</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran</label>
                    <select name="size"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Ukuran --</option>
                        <option value="20" {{ old('size') == '20' ? 'selected' : '' }}>20 FT</option>
                        <option value="40" {{ old('size') == '40' ? 'selected' : '' }}>40 FT</option>
                        <option value="45" {{ old('size') == '45' ? 'selected' : '' }}>45 FT</option>
                    </select>
                </div>



                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">F / E</label>
                    <select name="f_e"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="E" {{ old('f_e', 'E') == 'E' ? 'selected' : '' }}>Empty (E)</option>
                        <option value="F" {{ old('f_e') == 'F' ? 'selected' : '' }}>Full (F)</option>
                    </select>
                </div>

                <!-- Keuangan Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Lain-lain</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Uang Jalan</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                            Rp
                        </span>
                        <input type="text"
                               name="uang_jalan"
                               id="uang_jalan"
                               value="{{ old('uang_jalan', '0') }}"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-r-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 currency">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-600">*</span></label>
                    <select name="status"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="catatan"
                              rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">{{ old('catatan') }}</textarea>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('surat-jalan-tarik-kosong-batam.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-150">
                    Batal
                </a>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <i class="fas fa-save mr-2"></i> Simpan Surat Jalan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Currency formatting
        const currencyInputs = document.querySelectorAll('.currency');
        currencyInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                this.value = new Intl.NumberFormat('id-ID').format(this.value.replace(/[^0-9]/g, ''));
            });
        });

        // Auto generate number
        const btnGenerate = document.getElementById('btn-generate-number');
        const tanggalInput = document.getElementById('tanggal_surat_jalan');
        const noSJInput = document.getElementById('no_surat_jalan');

        function generateNumber() {
            const date = tanggalInput.value;
            if(!date) return;
            
            fetch("{{ route('surat-jalan-tarik-kosong-batam.generate-number') }}?date=" + date)
                .then(response => response.json())
                .then(data => {
                    noSJInput.value = data.number;
                })
                .catch(err => console.error('Gagal generate nomor:', err));
        }

        btnGenerate.addEventListener('click', generateNumber);
        tanggalInput.addEventListener('change', generateNumber);
        
        if (!noSJInput.value) {
            generateNumber();
        }

        // --- Custom Searchable Dropdown for Kontainer ---
        const searchInput = document.getElementById('no_kontainer_search');
        const hiddenInput = document.getElementById('no_kontainer_hidden');
        const dropdown = document.getElementById('no_kontainer_dropdown');
        const options = dropdown.querySelectorAll('.kontainer-option');
        const sizeSelect = document.querySelector('select[name="size"]');

        // Show/Hide dropdown
        searchInput.addEventListener('focus', () => {
            dropdown.classList.remove('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Filter functionality
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            let hasVisible = false;
            
            options.forEach(opt => {
                const text = opt.querySelector('.font-medium').innerText.toLowerCase();
                if (text.includes(filter)) {
                    opt.classList.remove('hidden');
                    hasVisible = true;
                } else {
                    opt.classList.add('hidden');
                }
            });

            dropdown.classList.toggle('hidden', !hasVisible);
            
            // Clear hidden if search is empty
            if (!this.value) {
                hiddenInput.value = '';
            }
        });

        // Selection functionality
        options.forEach(opt => {
            opt.addEventListener('click', function() {
                const val = this.dataset.value;
                const ukuran = this.dataset.ukuran;

                searchInput.value = val;
                hiddenInput.value = val;
                
                if (ukuran) {
                    sizeSelect.value = ukuran;
                    updateUangJalan(); // Trigger uang jalan update
                }

                dropdown.classList.add('hidden');
                
                // Highlight selected
                options.forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
            });
        });

        // Pre-select if value exists (old input)
        if (hiddenInput.value) {
            const selected = Array.from(options).find(o => o.dataset.value === hiddenInput.value);
            if (selected) {
                selected.classList.add('selected');
                searchInput.value = selected.dataset.value;
            }
        }

        // --- Custom Searchable Dropdown for Tujuan Pengambilan ---
        const pickupSearch = document.getElementById('tujuan_pengambilan_search');
        const pickupDropdown = document.getElementById('tujuan_pengambilan_dropdown');
        const pickupOptions = pickupDropdown.querySelectorAll('.location-option');

        pickupSearch.addEventListener('focus', () => {
            pickupDropdown.classList.remove('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!pickupSearch.contains(e.target) && !pickupDropdown.contains(e.target)) {
                pickupDropdown.classList.add('hidden');
            }
        });

        pickupSearch.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            let hasVisible = false;
            
            pickupOptions.forEach(opt => {
                const text = opt.innerText.toLowerCase();
                if (text.includes(filter)) {
                    opt.classList.remove('hidden');
                    hasVisible = true;
                } else {
                    opt.classList.add('hidden');
                }
            });

            pickupDropdown.classList.toggle('hidden', !hasVisible);
        });

        pickupOptions.forEach(opt => {
            opt.addEventListener('click', function() {
                pickupSearch.value = this.dataset.value;
                pickupDropdown.classList.add('hidden');
                pickupOptions.forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
            });
        });

        // --- Custom Searchable Dropdown for Tujuan Pengiriman ---
        const deliverySearch = document.getElementById('tujuan_pengiriman_search');
        const deliveryDropdown = document.getElementById('tujuan_pengiriman_dropdown');
        const deliveryOptions = deliveryDropdown.querySelectorAll('.warehouse-option');

        deliverySearch.addEventListener('focus', () => {
            deliveryDropdown.classList.remove('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!deliverySearch.contains(e.target) && !deliveryDropdown.contains(e.target)) {
                deliveryDropdown.classList.add('hidden');
            }
        });

        deliverySearch.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            let hasVisible = false;
            
            deliveryOptions.forEach(opt => {
                const text = opt.innerText.toLowerCase();
                if (text.includes(filter)) {
                    opt.classList.remove('hidden');
                    hasVisible = true;
                } else {
                    opt.classList.add('hidden');
                }
            });

            deliveryDropdown.classList.toggle('hidden', !hasVisible);
        });

        deliveryOptions.forEach(opt => {
            opt.addEventListener('click', function() {
                deliverySearch.value = this.dataset.value;
                deliveryDropdown.classList.add('hidden');
                deliveryOptions.forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
            });
        });

        // --- Auto-fill Uang Jalan ---
        const pricelistRings = @json($pricelistRings);
        const uangJalanInput = document.getElementById('uang_jalan');
        const fESelect = document.querySelector('select[name="f_e"]');

        function updateUangJalan() {
            const selectedLocation = pickupSearch.value;
            const selectedSize = sizeSelect.value;
            const selectedFE = fESelect.value; // F or E

            if (!selectedLocation || !selectedSize || !selectedFE) return;

            const ringData = pricelistRings.find(r => r.name === selectedLocation);
            if (ringData) {
                const key = `${selectedSize}_${selectedFE}`;
                const rate = ringData.rates[key];
                
                if (rate) {
                    uangJalanInput.value = new Intl.NumberFormat('id-ID').format(rate);
                }
            }
        }

        // Add listeners for Uang Jalan updates
        fESelect.addEventListener('change', updateUangJalan);
        sizeSelect.addEventListener('change', updateUangJalan);
        
        // Update updateUangJalan in pickupOptions listener
        pickupOptions.forEach(opt => {
            opt.addEventListener('click', function() {
                pickupSearch.value = this.dataset.value;
                pickupDropdown.classList.add('hidden');
                pickupOptions.forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
                updateUangJalan(); // Add this
            });
        });

        // --- Auto-fill No. Plat based on Supir ---
        const supirSelect = document.querySelector('select[name="supir"]');
        const platSelect = document.querySelector('select[name="no_plat"]');

        supirSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const plat = selectedOption.dataset.plat;
            
            if (plat && plat !== 'undefined') {
                platSelect.value = plat;
            }
        });
    });
</script>
@endpush
@endsection
