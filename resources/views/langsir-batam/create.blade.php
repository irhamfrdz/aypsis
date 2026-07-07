@extends('layouts.app')

@section('page_title', 'Tambah Langsir Kontainer Batam')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-white flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Tambah Langsir Kontainer Batam</h1>
                    <p class="text-xs text-gray-600 mt-1">Input data pengiriman langsir kontainer baru</p>
                </div>
                <a href="{{ route('langsir-batam.index') }}" 
                   class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>

            <form action="{{ route('langsir-batam.store') }}" method="POST" class="p-6">
                @csrf

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-bold">Terjadi Kesalahan Input</span>
                        </div>
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Section: Utama -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center border-b pb-2">
                            <span class="w-2 h-4 bg-blue-600 rounded-sm mr-2"></span>
                            Informasi Utama
                        </h3>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wider">No. Transaksi (Otomatis)</label>
                            <input type="text" value="{{ $no_transaksi }}" readonly 
                                   class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm font-bold text-gray-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wider">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="relative kontainer-dropdown-container">
                                <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wider">No. Kontainer <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" id="kontainer_search" placeholder="Cari kontainer..." autocomplete="off" value="{{ old('no_kontainer') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all uppercase">
                                    <input type="hidden" name="no_kontainer" id="no_kontainer" value="{{ old('no_kontainer') }}">
                                    <div id="kontainer_list" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
                                        @foreach($all_kontainers as $k)
                                            <div class="px-4 py-2 hover:bg-blue-50 hover:text-blue-700 cursor-pointer text-sm transition-colors border-b border-gray-50 last:border-0 kontainer-item" 
                                                 data-no="{{ $k->no_kontainer }}"
                                                 data-size="{{ $k->size }}">
                                                <div class="font-medium">{{ $k->no_kontainer }}</div>
                                                <div class="text-[10px] text-gray-400 uppercase">{{ $k->size }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wider">Size <span class="text-red-500">*</span></label>
                                <select name="size" id="size_select" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="20FT" {{ old('size') == '20FT' ? 'selected' : '' }}>20 FT</option>
                                    <option value="40FT" {{ old('size') == '40FT' ? 'selected' : '' }}>40 FT</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wider">No. Seal</label>
                                <input type="text" name="no_seal" value="{{ old('no_seal') }}" placeholder="Opsional"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all uppercase">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wider">Status <span class="text-red-500">*</span></label>
                                <select name="status" id="status_select" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="FULL" {{ old('status') == 'FULL' ? 'selected' : '' }}>FULL</option>
                                    <option value="EMPTY" {{ old('status', 'EMPTY') == 'EMPTY' ? 'selected' : '' }}>EMPTY</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Rute & Transport -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center border-b pb-2">
                            <span class="w-2 h-4 bg-emerald-600 rounded-sm mr-2"></span>
                            Rute & Transportasi
                        </h3>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="relative dari-dropdown-container">
                                <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wider">Dari <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" name="dari" id="dari_search" placeholder="Cari Lokasi Asal..." autocomplete="off" value="{{ old('dari') }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all uppercase">
                                    <div id="dari_list" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
                                        @foreach($locations as $loc)
                                            <div class="px-4 py-2 hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer text-sm transition-colors border-b border-gray-50 last:border-0 dari-item" 
                                                 data-name="{{ $loc }}">
                                                <div class="font-medium uppercase">{{ $loc }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="relative ke-dropdown-container">
                                <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wider">Ke <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" name="ke" id="ke_search" placeholder="Cari Lokasi Tujuan..." autocomplete="off" value="{{ old('ke') }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all uppercase">
                                    <div id="ke_list" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
                                        @foreach($locations as $loc)
                                            <div class="px-4 py-2 hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer text-sm transition-colors border-b border-gray-50 last:border-0 ke-item" 
                                                 data-name="{{ $loc }}">
                                                <div class="font-medium uppercase">{{ $loc }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center pt-1">
                            <input type="checkbox" name="ob_dalam_pelabuhan" id="ob_dalam_pelabuhan" value="1" 
                                   {{ old('ob_dalam_pelabuhan') ? 'checked' : '' }}
                                   class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                            <label for="ob_dalam_pelabuhan" class="ml-2 text-xs font-bold text-gray-700 uppercase tracking-wider cursor-pointer">
                                OB Dalam Pelabuhan
                            </label>
                        </div>

                        <div class="relative supir-dropdown-container">
                            <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wider">Supir <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text" id="supir_search" placeholder="Cari supir..." autocomplete="off"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                                <input type="hidden" name="supir_karyawan_id" id="supir_karyawan_id" value="{{ old('supir_karyawan_id') }}">
                                <input type="hidden" name="supir" id="supir_name" value="{{ old('supir') }}">
                                <div id="supir_list" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
                                    @foreach($supirs as $s)
                                        <div class="px-4 py-2 hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer text-sm transition-colors border-b border-gray-50 last:border-0 supir-item" 
                                             data-id="{{ $s->id }}"
                                             data-name="{{ $s->nama_panggilan ?: $s->nama_lengkap }}" 
                                             data-plat="{{ $s->plat }}">
                                            <div class="font-medium">{{ $s->nama_panggilan ?: $s->nama_lengkap }}</div>
                                            <div class="text-[10px] text-gray-400 uppercase">{{ $s->plat ?: 'Tanpa Plat' }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wider">No. Plat</label>
                            <input type="text" name="no_plat" id="no_plat" value="{{ old('no_plat') }}" placeholder="Otomatis terisi saat pilih supir"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all uppercase">
                        </div>
                    </div>

                    <!-- Section: Biaya -->
                    <div class="md:col-span-2 space-y-4 bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <h3 class="text-sm font-bold text-blue-800 uppercase tracking-wider flex items-center border-b border-blue-200 pb-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Biaya & Keterangan
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wider">Biaya Langsir <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-sm">Rp</span>
                                    </div>
                                    <input type="text" name="biaya_display" id="biaya_display" 
                                           value="{{ old('biaya_display') }}" required
                                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg text-lg font-bold text-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all money-format">
                                    <input type="hidden" name="biaya" id="biaya" value="{{ old('biaya') }}">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wider">Keterangan</label>
                                <textarea name="keterangan" rows="2" placeholder="Catatan tambahan jika ada..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t flex flex-col sm:flex-row justify-end gap-3">
                    <a href="{{ route('langsir-batam.index') }}" 
                       class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-all text-center">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-10 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                        Simpan Data Langsir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Money Formatting
    const biayaDisplay = document.getElementById('biaya_display');
    const biayaHidden = document.getElementById('biaya');

    function formatRupiah(angka) {
        let number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }

    if (biayaDisplay) {
        biayaDisplay.addEventListener('input', function(e) {
            let val = e.target.value.replace(/\D/g, '');
            biayaHidden.value = val;
            e.target.value = formatRupiah(val);
        });
        
        // Initial format if any
        if (biayaDisplay.value) {
            let val = biayaDisplay.value.replace(/\D/g, '');
            biayaHidden.value = val;
            biayaDisplay.value = formatRupiah(val);
        }
    }

    // Searchable Dropdown for Kontainer
    const kontainerSearch = document.getElementById('kontainer_search');
    const kontainerList = document.getElementById('kontainer_list');
    const kontainerHidden = document.getElementById('no_kontainer');
    const sizeSelect = document.getElementById('size_select');
    const kontainerItems = document.querySelectorAll('.kontainer-item');

    kontainerSearch.addEventListener('focus', () => {
        kontainerList.classList.remove('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.kontainer-dropdown-container')) {
            kontainerList.classList.add('hidden');
        }
        if (!e.target.closest('.supir-dropdown-container')) {
            supirList.classList.add('hidden');
        }
        if (!e.target.closest('.dari-dropdown-container')) {
            const dariList = document.getElementById('dari_list');
            if (dariList) dariList.classList.add('hidden');
        }
        if (!e.target.closest('.ke-dropdown-container')) {
            const keList = document.getElementById('ke_list');
            if (keList) keList.classList.add('hidden');
        }
    });

    kontainerSearch.addEventListener('input', () => {
        const filter = kontainerSearch.value.toLowerCase();
        kontainerItems.forEach(item => {
            const no = item.getAttribute('data-no').toLowerCase();
            if (no.includes(filter)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
        kontainerList.classList.remove('hidden');
    });

    kontainerItems.forEach(item => {
        item.addEventListener('click', () => {
            const no = item.getAttribute('data-no');
            const size = item.getAttribute('data-size');

            kontainerSearch.value = no;
            kontainerHidden.value = no;
            if (sizeSelect) {
                // Set size select value and handle if size is different from standard
                if (size.includes('20')) sizeSelect.value = '20FT';
                else if (size.includes('40')) sizeSelect.value = '40FT';
                else sizeSelect.value = size; // fallback
            }

            kontainerList.classList.add('hidden');
            autoCalculateBiaya();
        });
    });

    // Searchable Dropdown for Supir
    const supirSearch = document.getElementById('supir_search');
    const supirList = document.getElementById('supir_list');
    const supirIdHidden = document.getElementById('supir_karyawan_id');
    const supirNameHidden = document.getElementById('supir_name');
    const platInput = document.getElementById('no_plat');
    const supirItems = document.querySelectorAll('.supir-item');

    supirSearch.addEventListener('focus', () => {
        supirList.classList.remove('hidden');
    });

    supirSearch.addEventListener('input', () => {
        const filter = supirSearch.value.toLowerCase();
        supirItems.forEach(item => {
            const name = item.getAttribute('data-name').toLowerCase();
            const plat = item.getAttribute('data-plat').toLowerCase();
            if (name.includes(filter) || plat.includes(filter)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
        supirList.classList.remove('hidden');
    });

    supirItems.forEach(item => {
        item.addEventListener('click', () => {
            const id = item.getAttribute('data-id');
            const name = item.getAttribute('data-name');
            const plat = item.getAttribute('data-plat');

            supirSearch.value = name;
            supirIdHidden.value = id;
            supirNameHidden.value = name;
            if (platInput) platInput.value = plat;

            supirList.classList.add('hidden');
        });
    });

    // Handle initial selection if any
    if (supirIdHidden.value) {
        const initialItem = Array.from(supirItems).find(i => i.getAttribute('data-id') == supirIdHidden.value);
        if (initialItem) {
            supirSearch.value = initialItem.getAttribute('data-name');
        }
    }

    // Searchable Dropdown for Dari (Asal)
    const dariSearch = document.getElementById('dari_search');
    const dariList = document.getElementById('dari_list');
    const dariItems = document.querySelectorAll('.dari-item');

    if (dariSearch && dariList) {
        dariSearch.addEventListener('focus', () => {
            dariList.classList.remove('hidden');
        });

        dariSearch.addEventListener('input', () => {
            const filter = dariSearch.value.toLowerCase();
            dariItems.forEach(item => {
                const name = item.getAttribute('data-name').toLowerCase();
                if (name.includes(filter)) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
            dariList.classList.remove('hidden');
            autoCalculateBiaya();
        });

        dariItems.forEach(item => {
            item.addEventListener('click', () => {
                const name = item.getAttribute('data-name');
                dariSearch.value = name;
                dariList.classList.add('hidden');
                autoCalculateBiaya();
            });
        });
    }

    // Searchable Dropdown for Ke (Tujuan)
    const keSearch = document.getElementById('ke_search');
    const keList = document.getElementById('ke_list');
    const keItems = document.querySelectorAll('.ke-item');

    if (keSearch && keList) {
        keSearch.addEventListener('focus', () => {
            keList.classList.remove('hidden');
        });

        keSearch.addEventListener('input', () => {
            const filter = keSearch.value.toLowerCase();
            keItems.forEach(item => {
                const name = item.getAttribute('data-name').toLowerCase();
                if (name.includes(filter)) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
            keList.classList.remove('hidden');
            autoCalculateBiaya();
        });

        keItems.forEach(item => {
            item.addEventListener('click', () => {
                const name = item.getAttribute('data-name');
                keSearch.value = name;
                keList.classList.add('hidden');
                autoCalculateBiaya();
            });
        });
    }

    // Automatic Fee Calculation
    function autoCalculateBiaya() {
        const dariVal = (dariSearch?.value || '').trim().toUpperCase();
        const keVal = (keSearch?.value || '').trim().toUpperCase();
        const sizeVal = sizeSelect?.value || '';
        const statusSelect = document.getElementById('status_select');
        const statusVal = statusSelect?.value || '';

        // Check if route is SRIMAS -> PELABUHAN or PELABUHAN -> SRIMAS
        const isSrimasPelabuhan = (dariVal === 'SRIMAS' && keVal === 'PELABUHAN') || (dariVal === 'PELABUHAN' && keVal === 'SRIMAS');

        if (isSrimasPelabuhan) {
            let price = 0;
            if (sizeVal === '20FT') {
                price = statusVal === 'FULL' ? 40000 : 35000;
            } else if (sizeVal === '40FT') {
                price = statusVal === 'FULL' ? 50000 : 45000;
            }

            if (price > 0) {
                if (biayaDisplay && biayaHidden) {
                    biayaHidden.value = price;
                    biayaDisplay.value = formatRupiah(price.toString());
                }
            }
        }
    }

    if (sizeSelect) {
        sizeSelect.addEventListener('change', autoCalculateBiaya);
    }
    const statusSelect = document.getElementById('status_select');
    if (statusSelect) {
        statusSelect.addEventListener('change', autoCalculateBiaya);
    }
});
</script>
@endpush
