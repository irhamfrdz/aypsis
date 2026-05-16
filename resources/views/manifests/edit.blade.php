@extends('layouts.app')

@section('title', 'Edit Manifest')
@section('page_title', 'Edit Manifest')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('report.manifests.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Daftar Manifest
            </a>
            <h1 class="mt-2 text-3xl font-bold text-gray-900">Edit Manifest</h1>
        </div>

        <form action="{{ route('report.manifests.update', $manifest->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Informasi BL & Kontainer -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi BL & Kontainer</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nomor_urut" class="block text-sm font-medium text-gray-700 mb-2">No. Urut</label>
                        <input type="number" name="nomor_urut" id="nomor_urut" value="{{ old('nomor_urut', $manifest->nomor_urut) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 @error('nomor_urut') border-red-500 @enderror">
                        @error('nomor_urut')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nomor_bl" class="block text-sm font-medium text-gray-700 mb-2">No. BL <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_bl" id="nomor_bl" value="{{ old('nomor_bl', $manifest->nomor_bl) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 @error('nomor_bl') border-red-500 @enderror">
                        @error('nomor_bl')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="prospek_id" class="block text-sm font-medium text-gray-700 mb-2">Prospek</label>
                        <div class="relative">
                            <div class="dropdown-container-prospek">
                                <input type="text" id="search_prospek" placeholder="Cari prospek..." autocomplete="off"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 bg-white text-sm"
                                       value="{{ $manifest->prospek ? $manifest->prospek->pt_pengirim : '' }}">
                                <select name="prospek_id" id="prospek_id" class="hidden">
                                    <option value="">- Pilih Prospek -</option>
                                    @if($manifest->prospek)
                                        <option value="{{ $manifest->prospek_id }}" selected>{{ $manifest->prospek->pt_pengirim }}</option>
                                    @endif
                                </select>
                                <div id="dropdown_options_prospek" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden shadow-lg">
                                    <!-- Options populated by JS -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="nomor_kontainer" class="block text-sm font-medium text-gray-700 mb-2">No. Kontainer <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_kontainer" id="nomor_kontainer" value="{{ old('nomor_kontainer', $manifest->nomor_kontainer) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 @error('nomor_kontainer') border-red-500 @enderror">
                        @error('nomor_kontainer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_seal" class="block text-sm font-medium text-gray-700 mb-2">No. Seal</label>
                        <input type="text" name="no_seal" id="no_seal" value="{{ old('no_seal', $manifest->no_seal) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Tipe Kontainer</label>
                        <select name="tipe_kontainer" id="tipe_kontainer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="">- Pilih Tipe -</option>
                            @php
                                $tipeOptions = ['Dry Container', 'High Cube', 'Reefer', 'Open Top', 'Flat Rack', 'LCL', 'FCL', 'Cargo', 'SOC', '40 FT', '20 FT', 'FREE USE'];
                                $currentTipe = old('tipe_kontainer', $manifest->tipe_kontainer);
                            @endphp
                            @foreach($tipeOptions as $option)
                                <option value="{{ $option }}" {{ strtolower($currentTipe) == strtolower($option) ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                            @if($currentTipe && !collect($tipeOptions)->map(fn($o) => strtolower($o))->contains(strtolower($currentTipe)))
                                <option value="{{ $currentTipe }}" selected>{{ $currentTipe }}</option>
                            @endif
                        </select>
                    </div>

                    <div>
                        <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Size Kontainer</label>
                        <select name="size_kontainer" id="size_kontainer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="">- Pilih Size -</option>
                            @php
                                $sizeOptions = ['20', '40', '20ft', '40ft', '45ft', '10', '20 FT', '40 FT'];
                                $currentSize = old('size_kontainer', $manifest->size_kontainer);
                            @endphp
                            @foreach($sizeOptions as $option)
                                <option value="{{ $option }}" {{ strtolower($currentSize) == strtolower($option) ? 'selected' : '' }}>{{ $option }}{{ in_array($option, ['20', '40']) ? "'" : "" }}</option>
                            @endforeach
                            @if($currentSize && !collect($sizeOptions)->map(fn($o) => strtolower($o))->contains(strtolower($currentSize)))
                                <option value="{{ $currentSize }}" selected>{{ $currentSize }}</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <!-- Informasi Kapal & Pelabuhan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kapal & Pelabuhan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">Nama Kapal</label>
                        <input type="text" name="nama_kapal" id="nama_kapal" value="{{ old('nama_kapal', $manifest->nama_kapal) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="no_voyage" class="block text-sm font-medium text-gray-700 mb-2">No. Voyage</label>
                        <input type="text" name="no_voyage" id="no_voyage" value="{{ old('no_voyage', $manifest->no_voyage) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="pelabuhan_asal" class="block text-sm font-medium text-gray-700 mb-2">Pelabuhan Asal</label>
                        <input type="text" name="pelabuhan_asal" id="pelabuhan_asal" value="{{ old('pelabuhan_asal', $manifest->pelabuhan_asal) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="pelabuhan_tujuan" class="block text-sm font-medium text-gray-700 mb-2">Pelabuhan Tujuan</label>
                        <input type="text" name="pelabuhan_tujuan" id="pelabuhan_tujuan" value="{{ old('pelabuhan_tujuan', $manifest->pelabuhan_tujuan) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="tanggal_berangkat" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berangkat</label>
                        <input type="date" name="tanggal_berangkat" id="tanggal_berangkat" value="{{ old('tanggal_berangkat', $manifest->tanggal_berangkat ? $manifest->tanggal_berangkat->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="penerimaan" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Penerimaan</label>
                        <input type="date" name="penerimaan" id="penerimaan" value="{{ old('penerimaan', $manifest->penerimaan ? $manifest->penerimaan->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>
            </div>

            <!-- Informasi Barang -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Barang</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-2">Nama Barang</label>
                        <textarea name="nama_barang" id="nama_barang" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">{{ old('nama_barang', $manifest->nama_barang) }}</textarea>
                    </div>

                    <div>
                        <label for="tonnage" class="block text-sm font-medium text-gray-700 mb-2">Tonnage</label>
                        <input type="number" step="0.001" name="tonnage" id="tonnage" value="{{ old('tonnage', $manifest->tonnage) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="volume" class="block text-sm font-medium text-gray-700 mb-2">Volume</label>
                        <input type="number" step="0.001" name="volume" id="volume" value="{{ old('volume', $manifest->volume) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="satuan" class="block text-sm font-medium text-gray-700 mb-2">Satuan</label>
                        <input type="text" name="satuan" id="satuan" value="{{ old('satuan', $manifest->satuan) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="kuantitas" class="block text-sm font-medium text-gray-700 mb-2">Kuantitas</label>
                        <input type="number" name="kuantitas" id="kuantitas" value="{{ old('kuantitas', $manifest->kuantitas) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="term" class="block text-sm font-medium text-gray-700 mb-2">Term</label>
                        <input type="text" name="term" id="term" value="{{ old('term', $manifest->term) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>
            </div>

            <!-- Informasi Pengirim & Penerima -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pengirim & Penerima</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="pengirim" class="text-sm font-medium text-gray-700">SHIPPER</label>
                            <a href="#" id="edit_shipper_link"
                               class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 hidden"
                               title="Edit" target="_blank">
                                Edit
                            </a>
                        </div>
                        <div class="relative">
                            <div class="dropdown-container-shipper">
                                <input type="text" id="search_shipper" placeholder="Search shipper..." autocomplete="off"
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-purple-500 bg-white text-sm">
                                <select name="pengirim" id="pengirim_id" class="hidden">
                                    <option value="">- Pilih Shipper -</option>
                                    @if($manifest->pengirim)
                                        <option value="{{ $manifest->pengirim }}" 
                                                data-alamat="{{ $manifest->alamat_pengirim }}"
                                                data-edit-url="{{ route('pengirim.index') /* Fallback URL */ }}"
                                                selected>
                                            {{ $manifest->pengirim }}
                                        </option>
                                    @endif
                                </select>
                                <div id="dropdown_options_shipper" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                    <!-- Options populated by JS -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="alamat_pengirim" class="block text-sm font-medium text-gray-700 mb-2">Alamat Pengirim</label>
                        <textarea name="alamat_pengirim" id="alamat_pengirim" rows="1"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">{{ old('alamat_pengirim', $manifest->alamat_pengirim) }}</textarea>
                    </div>

                    <div>
                        <label for="penerima" class="block text-sm font-medium text-gray-700 mb-2">CONSIGNEE</label>
                        <input type="text" name="penerima" id="penerima" value="{{ old('penerima', $manifest->penerima) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="asal_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Asal Kontainer</label>
                        <input type="text" name="asal_kontainer" id="asal_kontainer" value="{{ old('asal_kontainer', $manifest->asal_kontainer) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="ke" class="block text-sm font-medium text-gray-700 mb-2">Ke</label>
                        <input type="text" name="ke" id="ke" value="{{ old('ke', $manifest->ke) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div class="md:col-span-2">
                        <label for="alamat_pengiriman" class="block text-sm font-medium text-gray-700 mb-2">Alamat Pengiriman</label>
                        <textarea name="alamat_pengiriman" id="alamat_pengiriman" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">{{ old('alamat_pengiriman', $manifest->alamat_pengiriman) }}</textarea>
                    </div>

                    <div>
                        <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">Contact Person</label>
                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $manifest->contact_person) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('report.manifests.index') }}"
                   class="px-6 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    Perbarui Manifest
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function initSearchableDropdown(config) {
        const {
            containerSelector,
            searchInputId,
            selectElementId,
            optionsContainerId,
            apiUrl,
            onSelect,
            onPopulate
        } = config;

        const container = document.querySelector(containerSelector);
        const searchInput = document.getElementById(searchInputId);
        const selectElement = document.getElementById(selectElementId);
        const optionsContainer = document.getElementById(optionsContainerId);
        
        let debounceTimer;

        function fetchOptions(query) {
            optionsContainer.innerHTML = '<div class="px-3 py-2 text-gray-500 text-sm italic">Mencari...</div>';
            optionsContainer.classList.remove('hidden');

            fetch(`${apiUrl}?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    populateDropdown(data);
                })
                .catch(err => {
                    console.error(err);
                    optionsContainer.innerHTML = '<div class="px-3 py-2 text-red-500 text-sm italic">Gagal mengambil data</div>';
                });
        }

        function populateDropdown(options) {
            optionsContainer.innerHTML = '';
            
            if (options.length === 0) {
                optionsContainer.innerHTML = '<div class="px-3 py-4 text-center text-gray-500 text-sm italic">Tidak ada hasil ditemukan</div>';
                return;
            }

            options.forEach(option => {
                const div = document.createElement('div');
                div.className = 'px-3 py-2 hover:bg-purple-50 cursor-pointer border-b border-gray-100 text-sm';
                div.textContent = option.text;
                
                div.addEventListener('click', () => {
                    // Update select element (add option if not exists)
                    let opt = Array.from(selectElement.options).find(o => o.value == option.id);
                    if (!opt) {
                        opt = new Option(option.text, option.id);
                        if (option.alamat) opt.setAttribute('data-alamat', option.alamat);
                        if (option.edit_url) opt.setAttribute('data-edit-url', option.edit_url);
                        selectElement.add(opt);
                    }
                    selectElement.value = option.id;
                    searchInput.value = option.text;
                    optionsContainer.classList.add('hidden');
                    
                    if (onSelect) onSelect(option, opt);
                });
                optionsContainer.appendChild(div);
            });
            if (onPopulate) onPopulate(options);
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value;
            if (query.length < 2) {
                optionsContainer.classList.add('hidden');
                return;
            }
            debounceTimer = setTimeout(() => fetchOptions(query), 300);
        });

        searchInput.addEventListener('focus', () => {
            if (searchInput.value.length >= 2) {
                fetchOptions(searchInput.value);
            }
        });

        searchInput.addEventListener('click', () => {
            if (searchInput.value.length >= 2) {
                optionsContainer.classList.remove('hidden');
            }
        });

        document.addEventListener('click', (e) => {
            if (!container || !container.contains(e.target)) {
                optionsContainer.classList.add('hidden');
            }
        });
    }

    // Initialize Shipper Dropdown
    const editLink = document.getElementById('edit_shipper_link');
    function updateEditLink() {
        const selectElement = document.getElementById('pengirim_id');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const editUrl = selectedOption ? selectedOption.getAttribute('data-edit-url') : '';
        if (editUrl) {
            editLink.href = editUrl;
            editLink.classList.remove('hidden');
        } else {
            editLink.classList.add('hidden');
        }
    }

    initSearchableDropdown({
        containerSelector: '.dropdown-container-shipper',
        searchInputId: 'search_shipper',
        selectElementId: 'pengirim_id',
        optionsContainerId: 'dropdown_options_shipper',
        apiUrl: '/api/manifests/search-shippers',
        onSelect: (option, optElement) => {
            const alamatTextarea = document.getElementById('alamat_pengirim');
            if (alamatTextarea && option.alamat) {
                alamatTextarea.value = option.alamat;
            }
            updateEditLink();
        }
    });

    // Initialize Prospek Dropdown
    initSearchableDropdown({
        containerSelector: '.dropdown-container-prospek',
        searchInputId: 'search_prospek',
        selectElementId: 'prospek_id',
        optionsContainerId: 'dropdown_options_prospek',
        apiUrl: '/api/manifests/search-prospeks'
    });

    // Initial state for edit link
    updateEditLink();
});
</script>
@endpush
