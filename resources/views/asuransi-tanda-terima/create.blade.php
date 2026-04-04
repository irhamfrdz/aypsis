@extends('layouts.app')

@section('title', 'Tambah Asuransi Tanda Terima')

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
                <h1 class="text-2xl font-bold text-gray-800">Tambah Asuransi Tanda Terima</h1>
            </div>

            <form action="{{ route('asuransi-tanda-terima.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Vendor -->
                    <div class="md:col-span-2">
                        <label for="vendor_asuransi_id" class="block text-sm font-medium text-gray-700 mb-2">Vendor Asuransi <span class="text-red-500">*</span></label>
                        <select id="vendor_asuransi_id" name="vendor_asuransi_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 vanilla-searchable invisible h-0 overflow-hidden">
                            <option value="">-- Pilih Vendor --</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" data-tarif="{{ $vendor->tarif }}" {{ old('vendor_asuransi_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->nama_asuransi }} (Tarif: {{ $vendor->tarif }}%)
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_asuransi_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Source Type -->
                    <div>
                        <label for="receipt_type" class="block text-sm font-medium text-gray-700 mb-2">Jenis Tanda Terima <span class="text-red-500">*</span></label>
                        <select id="receipt_type" name="receipt_type" required onchange="toggleReceiptList()"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                {{ isset($selectedReceipt) ? 'disabled' : '' }}>
                            <option value="tt" {{ (old('receipt_type', $selectedType) == 'tt') ? 'selected' : '' }}>Tanda Terima (Regular)</option>
                            <option value="tttsj" {{ (old('receipt_type', $selectedType) == 'tttsj') ? 'selected' : '' }}>Tanda Terima Tanpa SJ</option>
                            <option value="lcl" {{ (old('receipt_type', $selectedType) == 'lcl') ? 'selected' : '' }}>Tanda Terima LCL</option>
                        </select>
                        @if(isset($selectedReceipt))
                            <input type="hidden" name="receipt_type" value="{{ $selectedType }}">
                        @endif
                        @error('receipt_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Receipt List -->
                    <div>
                        <label for="receipt_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Data <span class="text-red-500">*</span></label>
                        
                        <div id="wrapper_tt" class="receipt-select-wrapper {{ old('receipt_type', $selectedType) == 'tt' ? '' : 'hidden' }}">
                            <select name="receipt_id_tt" id="receipt_id_tt" class="w-full border border-gray-300 rounded-lg px-3 py-2 vanilla-searchable invisible h-0 overflow-hidden" {{ isset($selectedReceipt) && $selectedType != 'tt' ? 'disabled' : '' }}>
                                <option value="">-- Pilih Tanda Terima --</option>
                                @if(isset($selectedReceipt) && $selectedType == 'tt')
                                    <option value="{{ $selectedReceipt->id }}" selected>[{{ $selectedReceipt->no_surat_jalan }}] - {{ $selectedReceipt->penerima }}</option>
                                @endif
                                @foreach($tandaTerimas as $tt)
                                    @if(!(isset($selectedReceipt) && $selectedType == 'tt' && $selectedReceipt->id == $tt->id))
                                        <option value="{{ $tt->id }}" {{ old('receipt_id_tt') == $tt->id ? 'selected' : '' }}>[{{ $tt->no_surat_jalan }}] - {{ $tt->penerima }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div id="wrapper_tttsj" class="receipt-select-wrapper {{ old('receipt_type', $selectedType) == 'tttsj' ? '' : 'hidden' }}">
                            <select name="receipt_id_tttsj" id="receipt_id_tttsj" class="w-full border border-gray-300 rounded-lg px-3 py-2 vanilla-searchable invisible h-0 overflow-hidden" {{ isset($selectedReceipt) && $selectedType != 'tttsj' ? 'disabled' : '' }}>
                                <option value="">-- Pilih Tanda Terima Tanpa SJ --</option>
                                @if(isset($selectedReceipt) && $selectedType == 'tttsj')
                                    <option value="{{ $selectedReceipt->id }}" selected>[{{ $selectedReceipt->no_tanda_terima }}] - {{ $selectedReceipt->penerima }}</option>
                                @endif
                                @foreach($tandaTerimaTanpaSjs as $tt)
                                    @if(!(isset($selectedReceipt) && $selectedType == 'tttsj' && $selectedReceipt->id == $tt->id))
                                        <option value="{{ $tt->id }}" {{ old('receipt_id_tttsj') == $tt->id ? 'selected' : '' }}>[{{ $tt->no_tanda_terima }}] - {{ $tt->penerima }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div id="wrapper_lcl" class="receipt-select-wrapper {{ old('receipt_type', $selectedType) == 'lcl' ? '' : 'hidden' }}">
                            <select name="receipt_id_lcl" id="receipt_id_lcl" class="w-full border border-gray-300 rounded-lg px-3 py-2 vanilla-searchable invisible h-0 overflow-hidden" {{ isset($selectedReceipt) && $selectedType != 'lcl' ? 'disabled' : '' }}>
                                <option value="">-- Pilih Tanda Terima LCL --</option>
                                @if(isset($selectedReceipt) && $selectedType == 'lcl')
                                    <option value="{{ $selectedReceipt->id }}" selected>[{{ $selectedReceipt->nomor_tanda_terima }}] - {{ $selectedReceipt->nama_penerima }}</option>
                                @endif
                                @foreach($tandaTerimaLcls as $tt)
                                    @if(!(isset($selectedReceipt) && $selectedType == 'lcl' && $selectedReceipt->id == $tt->id))
                                        <option value="{{ $tt->id }}" {{ old('receipt_id_lcl') == $tt->id ? 'selected' : '' }}>[{{ $tt->nomor_tanda_terima }}] - {{ $tt->nama_penerima }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <input type="hidden" name="receipt_id" id="final_receipt_id" value="{{ old('receipt_id', $selectedId) }}">
                        @error('receipt_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Additional Info (Read-only display) -->
                    <div class="md:col-span-2 bg-blue-50 border border-blue-200 rounded-lg p-4 grid grid-cols-2 md:grid-cols-5 gap-4" id="receipt_info_section">
                        <div>
                            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wider">No. Kontainer</p>
                            <p class="text-sm font-bold text-gray-800" id="info_no_kontainer">{{ $selectedReceipt ? ($selectedReceipt->no_kontainer ?? $selectedReceipt->nomor_kontainer ?? '-') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wider">No. Surat Jalan</p>
                            <p class="text-sm font-bold text-gray-800" id="info_no_surat_jalan">
                                @if($selectedReceipt)
                                    @if($selectedType == 'tt') {{ $selectedReceipt->no_surat_jalan ?? '-' }}
                                    @elseif($selectedType == 'tttsj') {{ $selectedReceipt->nomor_surat_jalan_customer ?? '-' }}
                                    @elseif($selectedType == 'lcl') {{ $selectedReceipt->no_surat_jalan_customer ?? '-' }}
                                    @endif
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wider">Nama Barang</p>
                            <p class="text-sm font-bold text-gray-800 truncate" id="info_nama_barang" title="{{ $selectedReceipt ? (is_array($selectedReceipt->nama_barang) ? implode(', ', $selectedReceipt->nama_barang) : ($selectedReceipt->nama_barang ?? '-')) : '-' }}">
                                @if($selectedReceipt)
                                    @if($selectedType == 'tt') {{ is_array($selectedReceipt->nama_barang) ? implode(', ', $selectedReceipt->nama_barang) : ($selectedReceipt->nama_barang ?? '-') }}
                                    @elseif($selectedType == 'tttsj') {{ $selectedReceipt->nama_barang ?? '-' }}
                                    @elseif($selectedType == 'lcl') {{ $selectedReceipt->items->pluck('nama_barang')->filter()->unique()->implode(', ') ?: '-' }}
                                    @endif
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wider">Jumlah</p>
                            <p class="text-sm font-bold text-gray-800" id="info_jumlah_barang">
                                @if($selectedReceipt)
                                    @if($selectedType == 'tt') {{ $selectedReceipt->jumlah ?? '-' }}
                                    @elseif($selectedType == 'tttsj') {{ $selectedReceipt->jumlah_barang ?? '-' }}
                                    @elseif($selectedType == 'lcl') {{ $selectedReceipt->items->sum('jumlah') ?: '-' }}
                                    @endif
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wider">Satuan</p>
                            <p class="text-sm font-bold text-gray-800" id="info_satuan">
                                @if($selectedReceipt)
                                    @if($selectedType == 'tt') {{ $selectedReceipt->satuan ?? '-' }}
                                    @elseif($selectedType == 'tttsj') {{ $selectedReceipt->satuan_barang ?? '-' }}
                                    @elseif($selectedType == 'lcl') {{ $selectedReceipt->items->pluck('satuan')->filter()->unique()->implode(', ') ?: '-' }}
                                    @endif
                                @else
                                    -
                                @endif
                            </p>
                        </div>

                        <!-- View Button -->
                        <div class="md:col-span-5 flex justify-end mt-2 pt-2 border-t border-blue-100 {{ $selectedReceipt ? '' : 'hidden' }}" id="view_receipt_wrapper">
                             <a id="btn_view_receipt" href="{{ $selectedReceipt ? (
                                $selectedType == 'tt' ? route('tanda-terima.show', $selectedReceipt->id) : (
                                    $selectedType == 'tttsj' ? route('tanda-terima-tanpa-surat-jalan.show', $selectedReceipt->id) : (
                                        $selectedType == 'lcl' ? route('tanda-terima-lcl.show', $selectedReceipt->id) : '#'
                                    )
                                )
                             ) : '#' }}" target="_blank" class="inline-flex items-center text-blue-700 hover:text-blue-900 border border-blue-300 bg-white px-3 py-1 rounded-md text-xs font-bold shadow-sm transition duration-150">
                                <i class="fas fa-eye mr-1.5"></i> Lihat Detail Tanda Terima
                             </a>
                        </div>
                    </div>

                    <!-- Polis Number -->
                    <div>
                        <label for="nomor_polis" class="block text-sm font-medium text-gray-700 mb-2">Nomor Polis <span class="text-red-500">*</span></label>
                        <input type="text" id="nomor_polis" name="nomor_polis" value="{{ old('nomor_polis') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Contoh: POL12345678">
                        @error('nomor_polis') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Polis Date -->
                    <div>
                        <label for="tanggal_polis" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Polis <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_polis" name="tanggal_polis" value="{{ old('tanggal_polis', date('Y-m-d')) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('tanggal_polis') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Nilai Barang -->
                    <div>
                        <label for="nilai_barang" class="block text-sm font-medium text-gray-700 mb-2">Nilai Barang <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                            <input type="number" id="nilai_barang" name="nilai_barang" value="{{ old('nilai_barang', 0) }}" required
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        @error('nilai_barang') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Rate Asuransi -->
                    <div>
                        <label for="asuransi_rate" class="block text-sm font-medium text-gray-700 mb-2">Rate Asuransi (%) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.0001" id="asuransi_rate" name="asuransi_rate" value="{{ old('asuransi_rate', 0) }}" required
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
                        <label for="asuransi_file" class="block text-sm font-medium text-gray-700 mb-2">Unduh Dokumen Asuransi</label>
                        <input type="file" id="asuransi_file" name="asuransi_file"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Format: PDF, JPG, PNG (Maks 5MB)</p>
                        @error('asuransi_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Ship & Voyage Info -->
                    <div>
                        <label for="nomor_urut" class="block text-sm font-medium text-gray-700 mb-2">Nomor Urut</label>
                        <input type="text" id="nomor_urut" name="nomor_urut" value="{{ old('nomor_urut') }}" 
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
                                <option value="{{ $kapal->nama_kapal }}" {{ old('nama_kapal') == $kapal->nama_kapal ? 'selected' : '' }}>
                                    {{ $kapal->nama_kapal }}
                                </option>
                            @endforeach
                        </select>
                        @error('nama_kapal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="nomor_voyage" class="block text-sm font-medium text-gray-700 mb-2">Nomor Voyage</label>
                        <input type="text" id="nomor_voyage" name="nomor_voyage" value="{{ old('nomor_voyage') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Contoh: V.012">
                        @error('nomor_voyage') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="md:col-span-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('keterangan') }}</textarea>
                        @error('keterangan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('asuransi-tanda-terima.index') }}"
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium transition duration-200">
                        Batal
                    </a>
                    <button type="submit" onclick="syncReceiptId()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Simpan Data
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
            this.active = false;
            this.init();
        }
        
        init() {
            // Parse options
            this.syncOptions();
            
            // Create UI wrapper
            this.container = document.createElement('div');
            this.container.className = 'relative vanilla-select-host w-full mt-1';
            
            // Trigger button (looks like a select)
            this.trigger = document.createElement('div');
            this.trigger.className = 'w-full border border-gray-300 rounded-lg px-3 py-2.5 cursor-pointer bg-white flex justify-between items-center text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200';
            this.trigger.tabIndex = 0;
            this.trigger.innerHTML = `<span class="current-value truncate mr-1">-- Pilih --</span><svg class="w-4 h-4 text-gray-400 transform transition-transform duration-200 dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>`;
            
            // Dropdown menu
            this.dropdown = document.createElement('div');
            this.dropdown.className = 'absolute z-[999] w-full mt-1.5 bg-white border border-gray-200 rounded-lg shadow-xl hidden overflow-hidden scale-95 opacity-0 transition-all duration-200 transform origin-top';
            
            // Search input inside dropdown
            const searchWrapper = document.createElement('div');
            searchWrapper.className = 'p-2 border-b border-gray-100 bg-gray-50 sticky top-0';
            this.search = document.createElement('input');
            this.search.type = 'text';
            this.search.placeholder = 'Cari...';
            this.search.className = 'w-full px-3 py-1.5 text-sm border border-gray-200 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none';
            searchWrapper.appendChild(this.search);
            
            // List of items
            this.list = document.createElement('div');
            this.list.className = 'max-h-64 overflow-y-auto custom-scrollbar';
            
            this.dropdown.appendChild(searchWrapper);
            this.dropdown.appendChild(this.list);
            this.container.appendChild(this.trigger);
            this.container.appendChild(this.dropdown);
            
            this.select.parentNode.insertBefore(this.container, this.select);
            
            // Initial render
            this.updateTriggerText();
            this.renderList();
            
            // Events
            this.trigger.onclick = (e) => { e.stopPropagation(); this.toggle(); };
            this.search.onclick = (e) => e.stopPropagation();
            this.search.oninput = (e) => this.renderList(e.target.value.toLowerCase());
            
            document.addEventListener('click', (e) => {
                if (!this.container.contains(e.target)) this.close();
            });

            // Listen for external changes to underlying select
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
                    
                    if (opt.value === "") {
                        item.className += ' text-gray-400 italic';
                    }

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
            this.syncOptions(); // Keep state updated
        }
        
        toggle() {
            if (this.dropdown.classList.contains('hidden')) this.open();
            else this.close();
        }
        
        open() {
            // Close all other instances first
            document.querySelectorAll('.vanilla-select-host div.hidden').forEach(d => {
                if (!this.dropdown.isSameNode(d)) d.classList.add('hidden');
            });

            this.dropdown.classList.remove('hidden');
            this.trigger.classList.add('ring-2', 'ring-blue-500', 'border-blue-500');
            this.trigger.querySelector('.dropdown-arrow').classList.add('rotate-180');
            
            // Animation
            setTimeout(() => {
                this.dropdown.classList.remove('scale-95', 'opacity-0');
                this.dropdown.classList.add('scale-100', 'opacity-100');
                this.search.focus();
                this.renderList(); // Refresh list on open
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

    function toggleReceiptList() {
        const type = document.getElementById('receipt_type').value;
        const wrappers = document.querySelectorAll('.receipt-select-wrapper');
        
        wrappers.forEach(w => w.classList.add('hidden'));
        const activeWrapper = document.getElementById('wrapper_' + type);
        if (activeWrapper) activeWrapper.classList.remove('hidden');
    }

    function syncReceiptId() {
        const type = document.getElementById('receipt_type').value;
        const select = document.getElementById('receipt_id_' + type);
        if (select) {
            document.getElementById('final_receipt_id').value = select.value;
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

    function updateReceiptInfo() {
        const typeSelect = document.getElementById('receipt_type');
        if (!typeSelect) return;
        const type = typeSelect.value;
        const selectEl = document.getElementById('receipt_id_' + type);
        if (!selectEl) return;
        const id = selectEl.value;
        
        const infoFields = {
            no_kontainer: document.getElementById('info_no_kontainer'),
            no_surat_jalan: document.getElementById('info_no_surat_jalan'),
            nama_barang: document.getElementById('info_nama_barang'),
            jumlah_barang: document.getElementById('info_jumlah_barang'),
            satuan: document.getElementById('info_satuan')
        };

        const inputs = {
            nomor_urut: document.getElementById('nomor_urut'),
            nama_kapal: document.getElementById('nama_kapal'),
            nomor_voyage: document.getElementById('nomor_voyage')
        };

        const viewWrapper = document.getElementById('view_receipt_wrapper');
        const viewBtn = document.getElementById('btn_view_receipt');

        if (!id) {
            Object.values(infoFields).forEach(f => f.textContent = '-');
            if (viewWrapper) viewWrapper.classList.add('hidden');
            return;
        }

        Object.values(infoFields).forEach(f => f.textContent = '...');

        fetch(`/asuransi-tanda-terima/get-receipt-details/${type}/${id}`)
            .then(response => response.json())
            .then(data => {
                infoFields.no_kontainer.textContent = data.no_kontainer || '-';
                infoFields.no_surat_jalan.textContent = data.no_surat_jalan || '-';
                infoFields.nama_barang.textContent = data.nama_barang || '-';
                infoFields.nama_barang.title = data.nama_barang || '-';
                infoFields.jumlah_barang.textContent = data.jumlah_barang || '-';
                infoFields.satuan.textContent = data.satuan || '-';
                
                if (inputs.nomor_urut) inputs.nomor_urut.value = data.nomor_urut !== '-' ? data.nomor_urut : '';
                if (inputs.nama_kapal) {
                    inputs.nama_kapal.value = data.nama_kapal !== '-' ? data.nama_kapal : '';
                    inputs.nama_kapal.dispatchEvent(new Event('change'));
                }
                if (inputs.nomor_voyage) inputs.nomor_voyage.value = data.nomor_voyage !== '-' ? data.nomor_voyage : '';
                
                let baseUrl = '';
                if (type === 'tt') baseUrl = '/tanda-terima/';
                else if (type === 'tttsj') baseUrl = '/tanda-terima-tanpa-surat-jalan/';
                else if (type === 'lcl') baseUrl = '/tanda-terima-lcl/';
                
                if (baseUrl && viewBtn && viewWrapper) {
                    viewBtn.href = baseUrl + id;
                    viewWrapper.classList.remove('hidden');
                } else if (viewWrapper) {
                    viewWrapper.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error fetching receipt details:', error);
                Object.values(infoFields).forEach(f => f.textContent = 'Error');
                if (viewWrapper) viewWrapper.classList.add('hidden');
            });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize custom searchable dropdowns
        document.querySelectorAll('.vanilla-searchable').forEach(el => {
            new VanillaSearchableSelect(el);
        });

        toggleReceiptList();
        syncReceiptId();
        
        const vendorSelect = document.getElementById('vendor_asuransi_id');
        const nilaiInput = document.getElementById('nilai_barang');
        const rateInput = document.getElementById('asuransi_rate');
        const typeSelect = document.getElementById('receipt_type');
        
        if (vendorSelect) {
            vendorSelect.addEventListener('change', onVendorChange);
        }
        if (nilaiInput) {
            nilaiInput.addEventListener('input', calculateGrandTotal);
        }
        if (rateInput) {
            rateInput.addEventListener('input', calculateGrandTotal);
        }
        if (typeSelect) {
            typeSelect.addEventListener('change', () => {
                toggleReceiptList();
                updateReceiptInfo();
            });
        }

        ['tt', 'tttsj', 'lcl'].forEach(key => {
            const select = document.getElementById('receipt_id_' + key);
            if (select) {
                select.addEventListener('change', updateReceiptInfo);
            }
        });

        calculateGrandTotal();
        
        const finalIdInput = document.getElementById('final_receipt_id');
        if (finalIdInput && finalIdInput.value) {
            updateReceiptInfo();
        }
    });
</script>
@endpush
@endsection
