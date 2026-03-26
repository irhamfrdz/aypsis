@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Edit Surat Jalan Batam</h1>
                <p class="text-xs text-gray-600 mt-1">Perbarui surat jalan Batam: {{ $suratJalan->no_surat_jalan }}</p>
            </div>
            <a href="{{ route('surat-jalan-batam.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('surat-jalan-batam.update', $suratJalan->id) }}" method="POST" class="p-4">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- No SJ & Tanggal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Surat Jalan <span class="text-red-600">*</span></label>
                    <input type="text" name="no_surat_jalan" value="{{ old('no_surat_jalan', $suratJalan->no_surat_jalan) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-600 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat Jalan <span class="text-red-600">*</span></label>
                    <input type="date" name="tanggal_surat_jalan" value="{{ old('tanggal_surat_jalan', $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('Y-m-d') : '') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Section: Detail Pengiriman -->
                <div class="md:col-span-2 border-t pt-4 mt-2">
                    <h3 class="text-sm font-bold text-gray-800 mb-3">Detail Pengiriman</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pengirim</label>
                    <input type="text" name="pengirim" value="{{ old('pengirim', $suratJalan->pengirim) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Barang</label>
                    <input type="text" name="jenis_barang" value="{{ old('jenis_barang', $suratJalan->jenis_barang) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengambilan</label>
                    <input type="text" name="tujuan_pengambilan" value="{{ old('tujuan_pengambilan', $suratJalan->tujuan_pengambilan) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengiriman</label>
                    <input type="text" name="tujuan_pengiriman" value="{{ old('tujuan_pengiriman', $suratJalan->tujuan_pengiriman) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>

                <!-- Section: Transport -->
                <div class="md:col-span-2 border-t pt-4 mt-2">
                    <h3 class="text-sm font-bold text-gray-800 mb-3">Informasi Transportasi</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Plat Kendaraan</label>
                    <input type="text" name="no_plat" id="no_plat" value="{{ old('no_plat', $suratJalan->no_plat) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500">
                </div>

                <div class="relative supir-dropdown-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" id="supir_search" placeholder="Cari supir..." autocomplete="off"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        <input type="hidden" name="supir" id="supir_value" value="{{ old('supir', $suratJalan->supir) }}">
                        <div id="supir_list" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
                            <div class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 italic supir-item" data-value="" data-plat="">Pilih Supir</div>
                            @foreach($supirs as $s)
                                <div class="px-4 py-2 hover:bg-indigo-50 hover:text-indigo-700 cursor-pointer text-sm transition-colors border-b border-gray-50 last:border-0 supir-item" 
                                     data-value="{{ $s->nama_panggilan ?: $s->nama_lengkap }}" 
                                     data-plat="{{ $s->plat }}">
                                    <div class="font-medium">{{ $s->nama_panggilan ?: $s->nama_lengkap }}</div>
                                    <div class="text-xs text-gray-400">{{ $s->plat ?: 'Tanpa Plat' }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir 2 (Opsional)</label>
                    <input type="text" name="supir2" value="{{ old('supir2', $suratJalan->supir2) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>

                <div class="relative kenek-dropdown-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                    <div class="relative">
                        <input type="text" id="kenek_search" placeholder="Cari kenek..." autocomplete="off"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        <input type="hidden" name="kenek" id="kenek_value" value="{{ old('kenek', $suratJalan->kenek) }}">
                        <div id="kenek_list" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
                            <div class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 italic kenek-item" data-value="">Pilih Kenek</div>
                            @foreach($keneks as $k)
                                <div class="px-4 py-2 hover:bg-indigo-50 hover:text-indigo-700 cursor-pointer text-sm transition-colors border-b border-gray-50 last:border-0 kenek-item" 
                                     data-value="{{ $k->nama_panggilan ?: $k->nama_lengkap }}">
                                    <div class="font-medium">{{ $k->nama_panggilan ?: $k->nama_lengkap }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Section: Kontainer -->
                <div class="md:col-span-2 border-t pt-4 mt-2">
                    <h3 class="text-sm font-bold text-gray-800 mb-3">Informasi Kontainer</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Size Kontainer</label>
                    <select name="size" id="size_select" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Pilih Size</option>
                        @foreach($ukuranKontainers as $uk)
                            <option value="{{ $uk }}" {{ old('size', $suratJalan->size) == $uk ? 'selected' : '' }}>
                                {{ $uk }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Kontainer</label>
                    <select name="tipe_kontainer" id="tipe_kontainer" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Pilih Tipe</option>
                        @php
                            $selectedTipe = old('tipe_kontainer', $suratJalan->tipe_kontainer);
                        @endphp
                        <option value="FCL" {{ $selectedTipe == 'FCL' ? 'selected' : '' }}>FCL</option>
                        <option value="LCL" {{ $selectedTipe == 'LCL' ? 'selected' : '' }}>LCL</option>
                        <option value="CARGO" {{ $selectedTipe == 'CARGO' ? 'selected' : '' }}>CARGO</option>
                    </select>
                </div>

                <div class="relative no-kontainer-dropdown-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Kontainer</label>
                    <div class="relative">
                        <input type="text" id="no_kontainer_search" placeholder="Cari nomor kontainer..." autocomplete="off"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        <input type="hidden" name="no_kontainer" id="no_kontainer_value" value="{{ old('no_kontainer', $suratJalan->no_kontainer) }}">
                        <div id="no_kontainer_list" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
                            <div class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 italic no-kontainer-item" data-value="">Pilih No. Kontainer</div>
                            @foreach($daftarKontainers as $kontainer)
                                <div class="px-4 py-2 hover:bg-indigo-50 hover:text-indigo-700 cursor-pointer text-sm transition-colors border-b border-gray-50 last:border-0 no-kontainer-item" 
                                     data-value="{{ $kontainer['no'] }}"
                                     data-size="{{ $kontainer['size'] }}"
                                     data-tipe="{{ $kontainer['tipe'] }}">
                                    <div class="font-medium">{{ $kontainer['no'] }}</div>
                                    @if($kontainer['size'] || $kontainer['tipe'])
                                        <div class="text-xs text-gray-400">
                                            {{ $kontainer['size'] ? 'Size: ' . $kontainer['size'] : '' }}
                                            {{ $kontainer['size'] && $kontainer['tipe'] ? ' | ' : '' }}
                                            {{ $kontainer['tipe'] ? 'Tipe: ' . $kontainer['tipe'] : '' }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="draft" {{ old('status', $suratJalan->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', $suratJalan->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ old('status', $suratJalan->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $suratJalan->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <a href="{{ route('surat-jalan-batam.index') }}" class="px-4 py-2 border rounded-lg text-sm text-gray-600">Batal</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium">Perbarui</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Custom Searchable Dropdown Logic
    function setupSearchableDropdown(containerClass, inputId, listId, valueId, itemClass, onSelect = null) {
        const searchInput = document.getElementById(inputId);
        const listContainer = document.getElementById(listId);
        const hiddenValue = document.getElementById(valueId);
        const items = document.querySelectorAll('.' + itemClass);

        // Toggle list visibility
        searchInput.addEventListener('focus', () => {
            listContainer.classList.remove('hidden');
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.' + containerClass)) {
                listContainer.classList.add('hidden');
            }
        });

        // Filter items
        searchInput.addEventListener('input', () => {
            const filter = searchInput.value.toLowerCase();
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(filter) ? 'block' : 'none';
            });
            listContainer.classList.remove('hidden');
        });

        // Select item
        items.forEach(item => {
            item.addEventListener('click', () => {
                const val = item.getAttribute('data-value');
                searchInput.value = val;
                hiddenValue.value = val;
                listContainer.classList.add('hidden');
                if (onSelect) onSelect(item);
            });
        });

        // Handle initial value (for old/edit)
        if (hiddenValue.value) {
            searchInput.value = hiddenValue.value;
        }
    }

    // Initialize Supir Dropdown
    setupSearchableDropdown(
        'supir-dropdown-container',
        'supir_search',
        'supir_list',
        'supir_value',
        'supir-item',
        (item) => {
            const plat = item.getAttribute('data-plat');
            if (plat) {
                document.getElementById('no_plat').value = plat;
            }
        }
    );

    // Initialize Kenek Dropdown
    setupSearchableDropdown(
        'kenek-dropdown-container',
        'kenek_search',
        'kenek_list',
        'kenek_value',
        'kenek-item'
    );

    // Initialize No. Kontainer Dropdown
    setupSearchableDropdown(
        'no-kontainer-dropdown-container',
        'no_kontainer_search',
        'no_kontainer_list',
        'no_kontainer_value',
        'no-kontainer-item',
        (item) => {
            const size = item.getAttribute('data-size');
            const tipe = item.getAttribute('data-tipe');
            
            if (size) {
                const sizeSelect = document.getElementById('size_select');
                if (sizeSelect) sizeSelect.value = size;
            }
            if (tipe) {
                const tipeInput = document.getElementById('tipe_kontainer');
                if (tipeInput) tipeInput.value = tipe;
            }
        }
    );
</script>
@endpush
