@extends('layouts.app')

@section('title', 'Edit Asuransi Tanda Terima')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center mb-6">
                <a href="{{ route('asuransi-tanda-terima.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Edit Asuransi Tanda Terima</h1>
            </div>

            <form action="{{ route('asuransi-tanda-terima.update', $asuransiTandaTerima->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Source Info (Read-only for integrity) -->
                    <div class="md:col-span-2 bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <label class="block text-xs font-bold text-blue-700 uppercase mb-1">Terhubung ke (Kunci)</label>
                        <p class="text-gray-900 font-medium">{{ $asuransiTandaTerima->source_type_name }}: {{ $asuransiTandaTerima->source_number }}</p>
                    </div>

                    <!-- Vendor -->
                    <div class="md:col-span-2">
                        <label for="vendor_asuransi_id" class="block text-sm font-medium text-gray-700 mb-2">Vendor Asuransi <span class="text-red-500">*</span></label>
                        <select id="vendor_asuransi_id" name="vendor_asuransi_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 vanilla-searchable invisible h-0 overflow-hidden">
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" data-tarif="{{ $vendor->tarif }}" {{ old('vendor_asuransi_id', $asuransiTandaTerima->vendor_asuransi_id) == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->nama_asuransi }} (Tarif: {{ $vendor->tarif }}%)
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_asuransi_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Polis Number -->
                    <div>
                        <label for="nomor_polis" class="block text-sm font-medium text-gray-700 mb-2">Nomor Polis <span class="text-red-500">*</span></label>
                        <input type="text" id="nomor_polis" name="nomor_polis" value="{{ old('nomor_polis', $asuransiTandaTerima->nomor_polis) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('nomor_polis') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Polis Date -->
                    <div>
                        <label for="tanggal_polis" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Polis <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_polis" name="tanggal_polis" value="{{ old('tanggal_polis', $asuransiTandaTerima->tanggal_polis ? $asuransiTandaTerima->tanggal_polis->format('Y-m-d') : '') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('tanggal_polis') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    @php
                        $currentRate = $asuransiTandaTerima->asuransi_rate;
                        if ($currentRate <= 0 && $asuransiTandaTerima->nilai_pertanggungan > 0) {
                            $currentRate = ($asuransiTandaTerima->premi / $asuransiTandaTerima->nilai_pertanggungan * 100);
                        }
                        if ($currentRate <= 0) {
                            $currentRate = $asuransiTandaTerima->vendorAsuransi->tarif ?? 0;
                        }
                    @endphp

                    <!-- Nilai Barang -->
                    <div>
                        <label for="nilai_barang" class="block text-sm font-medium text-gray-700 mb-2">Nilai Barang <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                            <input type="number" id="nilai_barang" name="nilai_barang" value="{{ old('nilai_barang', $asuransiTandaTerima->nilai_pertanggungan) }}" required
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        @error('nilai_barang') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Rate Asuransi -->
                    <div>
                        <label for="asuransi_rate" class="block text-sm font-medium text-gray-700 mb-2">Rate Asuransi (%) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.0001" id="asuransi_rate" name="asuransi_rate" value="{{ old('asuransi_rate', number_format($currentRate, 4, '.', '')) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Contoh: 0.2">
                        @error('asuransi_rate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Grand Total -->
                    <div>
                        <label for="grand_total" class="block text-sm font-medium text-gray-700 mb-2">Grand Total (Premi)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                            <input type="text" id="grand_total" name="grand_total" value="0" readonly
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 bg-gray-50 focus:outline-none font-semibold text-gray-800">
                        </div>
                        <p class="mt-1 text-xs text-gray-500" id="tarif_info_text">Tarif default vendor: 0%</p>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label for="asuransi_file" class="block text-sm font-medium text-gray-700 mb-2">Update Dokumen Asuransi</label>
                        <input type="file" id="asuransi_file" name="asuransi_file"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @if($asuransiTandaTerima->asuransi_path)
                            <p class="mt-1 text-xs text-blue-600 italic flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A1 1 0 0111 2.293l4.707 4.707a1 1 0 01.293.707V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                                Dokumen saat ini sudah ada. Unggah baru untuk mengganti.
                            </p>
                        @endif
                        @error('asuransi_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Ship & Voyage Info -->
                    <div>
                        <label for="nomor_urut" class="block text-sm font-medium text-gray-700 mb-2">Nomor Urut</label>
                        <input type="text" id="nomor_urut" name="nomor_urut" value="{{ old('nomor_urut', $asuransiTandaTerima->nomor_urut) }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Contoh: 001">
                        @error('nomor_urut') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">Nama Kapal</label>
                        <select id="nama_kapal" name="nama_kapal" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 vanilla-searchable invisible h-0 overflow-hidden">
                            <option value="">-- Pilih Kapal --</option>
                            @foreach($masterKapals as $kapal)
                                <option value="{{ $kapal->nama_kapal }}" {{ old('nama_kapal', $asuransiTandaTerima->nama_kapal) == $kapal->nama_kapal ? 'selected' : '' }}>
                                    {{ $kapal->nama_kapal }}
                                </option>
                            @endforeach
                        </select>
                        @error('nama_kapal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="nomor_voyage" class="block text-sm font-medium text-gray-700 mb-2">Nomor Voyage</label>
                        <input type="text" id="nomor_voyage" name="nomor_voyage" value="{{ old('nomor_voyage', $asuransiTandaTerima->nomor_voyage) }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Contoh: V.012">
                        @error('nomor_voyage') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="md:col-span-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('keterangan', $asuransiTandaTerima->keterangan) }}</textarea>
                        @error('keterangan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('asuransi-tanda-terima.index') }}"
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium transition duration-200">
                        Batal
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    // Custom Searchable Dropdown Class (Vanilla JS)
    class VanillaSearchableSelect {
        constructor(select) {
            this.select = select;
            this.options = [];
            this.init();
        }
        
        init() {
            this.syncOptions();
            this.container = document.createElement('div');
            this.container.className = 'relative vanilla-select-host w-full mt-1';
            
            this.trigger = document.createElement('div');
            this.trigger.className = 'w-full border border-gray-300 rounded-lg px-3 py-2.5 cursor-pointer bg-white flex justify-between items-center text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200';
            this.trigger.tabIndex = 0;
            this.trigger.innerHTML = `<span class="current-value truncate mr-1">-- Pilih --</span><svg class="w-4 h-4 text-gray-400 transform transition-transform duration-200 dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>`;
            
            this.dropdown = document.createElement('div');
            this.dropdown.className = 'absolute z-[999] w-full mt-1.5 bg-white border border-gray-200 rounded-lg shadow-xl hidden overflow-hidden scale-95 opacity-0 transition-all duration-200 transform origin-top';
            
            const searchWrapper = document.createElement('div');
            searchWrapper.className = 'p-2 border-b border-gray-100 bg-gray-50 sticky top-0';
            this.search = document.createElement('input');
            this.search.type = 'text';
            this.search.placeholder = 'Cari...';
            this.search.className = 'w-full px-3 py-1.5 text-sm border border-gray-200 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none';
            searchWrapper.appendChild(this.search);
            
            this.list = document.createElement('div');
            this.list.className = 'max-h-64 overflow-y-auto custom-scrollbar';
            
            this.dropdown.appendChild(searchWrapper);
            this.dropdown.appendChild(this.list);
            this.container.appendChild(this.trigger);
            this.container.appendChild(this.dropdown);
            this.select.parentNode.insertBefore(this.container, this.select);
            
            this.updateTriggerText();
            this.renderList();
            
            this.trigger.onclick = (e) => { e.stopPropagation(); this.toggle(); };
            this.search.onclick = (e) => e.stopPropagation();
            this.search.oninput = (e) => this.renderList(e.target.value.toLowerCase());
            
            document.addEventListener('click', (e) => {
                if (!this.container.contains(e.target)) this.close();
            });
            this.select.addEventListener('change', () => this.updateTriggerText());
        }
        
        syncOptions() {
            this.options = Array.from(this.select.options).map(o => ({
                text: o.text,
                value: o.value,
                selected: o.selected
            }));
        }
        
        renderList(term = '') {
            this.list.innerHTML = '';
            let hasResults = false;
            this.options.forEach(opt => {
                if (opt.text.toLowerCase().includes(term) || opt.value === "") {
                    const item = document.createElement('div');
                    item.className = 'px-4 py-2.5 hover:bg-blue-50 cursor-pointer text-sm transition-colors duration-150 ' + (opt.selected && opt.value !== "" ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700');
                    item.textContent = opt.text;
                    if (opt.value === "") item.className += ' text-gray-400 italic';
                    item.onclick = (e) => {
                        e.stopPropagation();
                        this.select.value = opt.value;
                        this.select.dispatchEvent(new Event('change'));
                        this.close();
                    };
                    this.list.appendChild(item);
                    hasResults = true;
                }
            });
            if (!hasResults) {
                const empty = document.createElement('div');
                empty.className = 'px-4 py-3 text-sm text-gray-400 italic text-center';
                empty.textContent = 'Tidak ditemukan';
                this.list.appendChild(empty);
            }
        }
        
        updateTriggerText() {
            const selected = this.select.options[this.select.selectedIndex];
            const textEl = this.trigger.querySelector('.current-value');
            if (selected) {
                textEl.textContent = selected.text;
                textEl.classList.remove('text-gray-400');
                if (selected.value === "") textEl.classList.add('text-gray-400');
            }
            this.syncOptions();
        }
        
        toggle() {
            if (this.dropdown.classList.contains('hidden')) this.open();
            else this.close();
        }
        
        open() {
            document.querySelectorAll('.vanilla-select-host div.hidden').forEach(d => {
                if (!this.dropdown.isSameNode(d)) d.classList.add('hidden');
            });
            this.dropdown.classList.remove('hidden');
            this.trigger.classList.add('ring-2', 'ring-blue-500', 'border-blue-500');
            this.trigger.querySelector('.dropdown-arrow').classList.add('rotate-180');
            setTimeout(() => {
                this.dropdown.classList.remove('scale-95', 'opacity-0');
                this.dropdown.classList.add('scale-100', 'opacity-100');
                this.search.focus();
                this.renderList();
            }, 10);
        }
        
        close() {
            this.dropdown.classList.add('scale-95', 'opacity-0');
            this.dropdown.classList.remove('scale-100', 'opacity-100');
            this.trigger.classList.remove('ring-2', 'ring-blue-500', 'border-blue-500');
            this.trigger.querySelector('.dropdown-arrow').classList.remove('rotate-180');
            setTimeout(() => {
                this.dropdown.classList.add('hidden');
                this.search.value = '';
            }, 200);
        }
    }

    function calculateGrandTotal() {
        const select = document.getElementById('vendor_asuransi_id');
        const nilaiInput = document.getElementById('nilai_barang');
        const rateInput = document.getElementById('asuransi_rate');
        const grandTotalInput = document.getElementById('grand_total');
        const tarifInfo = document.getElementById('tarif_info_text');

        if (!nilaiInput || !rateInput || !grandTotalInput) return;

        const rate = parseFloat(rateInput.value) || 0;
        const nilai = parseFloat(nilaiInput.value) || 0;

        const total = nilai * (rate / 100);
        
        const formatted = new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        }).format(total);
        
        grandTotalInput.value = formatted;
        
        if (select && tarifInfo) {
            const selectedOption = select.options[select.selectedIndex];
            const defaultRate = selectedOption ? parseFloat(selectedOption.getAttribute('data-tarif')) || 0 : 0;
            tarifInfo.textContent = 'Tarif default vendor: ' + defaultRate + '%';
        }
    }

    function onVendorChange() {
        const select = document.getElementById('vendor_asuransi_id');
        const rateInput = document.getElementById('asuransi_rate');
        if (!select || !rateInput) return;

        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const tarif = parseFloat(selectedOption.getAttribute('data-tarif')) || 0;
            rateInput.value = tarif;
        }
        calculateGrandTotal();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize custom searchable dropdowns
        document.querySelectorAll('.vanilla-searchable').forEach(el => {
            new VanillaSearchableSelect(el);
        });

        const vendorSelect = document.getElementById('vendor_asuransi_id');
        const nilaiInput = document.getElementById('nilai_barang');
        const rateInput = document.getElementById('asuransi_rate');

        if (vendorSelect) {
            vendorSelect.addEventListener('change', onVendorChange);
        }
        if (nilaiInput) {
            nilaiInput.addEventListener('input', calculateGrandTotal);
        }
        if (rateInput) {
            rateInput.addEventListener('input', calculateGrandTotal);
        }

        calculateGrandTotal();
    });
</script>
@endpush
@endsection
