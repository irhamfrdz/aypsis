@extends('layouts.app')

@section('title', 'Edit Biaya Kapal')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Biaya Kapal</h1>
                <p class="text-gray-600 mt-1">Perbarui data biaya operasional kapal</p>
            </div>
            <div>
                <a href="{{ route('biaya-kapal.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg shadow-sm transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Terdapat beberapa kesalahan:</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Informasi Biaya Kapal</h2>
            <p class="text-sm text-gray-600 mt-1">Lengkapi formulir di bawah ini dengan data yang akurat</p>
        </div>

        <form action="{{ route('biaya-kapal.update', $biayaKapal->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tanggal -->
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="tanggal" 
                           name="tanggal" 
                           value="{{ old('tanggal', date('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal') border-red-500 @enderror"
                           required>
                    @error('tanggal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Invoice (Display Only) -->
                <div>
                    <label for="nomor_invoice_display" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Invoice <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="nomor_invoice_display" 
                               name="nomor_invoice"
                               value="{{ old('nomor_invoice', $biayaKapal->nomor_invoice) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed focus:ring-2 focus:ring-blue-500 focus:border-transparent font-medium text-gray-800"
                               placeholder="Nomor Invoice"
                               readonly>
                    </div>
                </div>

                <!-- Nomor Referensi -->
                <div id="nomor_referensi_wrapper">
                    <label for="nomor_referensi" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Referensi
                    </label>
                    <input type="text" 
                           id="nomor_referensi" 
                           name="nomor_referensi" 
                           value="{{ old('nomor_referensi') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nomor_referensi') border-red-500 @enderror"
                           placeholder="Masukkan nomor referensi (opsional)">
                    @error('nomor_referensi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Kapal -->
                <div id="kapal_wrapper">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kapal <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">✓ Multi-Select</span>
                    </label>
                    
                    {{-- Hidden inputs for selected kapal --}}
                    <div id="hidden_kapal_inputs"></div>
                    
                    {{-- Search input with dropdown --}}
                    <div class="relative">
                        <div class="w-full min-h-[42px] px-3 py-2 border border-gray-300 rounded-lg focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 bg-white cursor-text @error('nama_kapal') border-red-500 @enderror" 
                             id="kapal_container"
                             onclick="document.getElementById('kapal_search').focus()">
                             
                            {{-- Selected kapal chips --}}
                            <div id="selected_kapal_chips" class="flex flex-wrap gap-1 mb-1"></div>
                            
                            {{-- Search input --}}
                            <input type="text" 
                                   id="kapal_search"
                                   placeholder="--Pilih Kapal--"
                                   class="border-0 outline-none bg-transparent flex-1 min-w-[200px]"
                                   autocomplete="off">
                        </div>
                        
                        {{-- Dropdown list --}}
                        <div id="kapal_dropdown" 
                             class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto hidden">
                            @foreach($kapals as $kapal)
                                <div class="kapal-option px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0"
                                     data-id="{{ $kapal->id }}"
                                     data-nama="{{ $kapal->nama_kapal }}">
                                    <div class="font-medium text-gray-900">{{ $kapal->nama_kapal }}</div>
                                    @if($kapal->nickname)
                                        <div class="text-sm text-gray-500">{{ $kapal->nickname }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mt-2 flex justify-between items-center">
                        <span id="kapalSelectedCount" class="text-sm text-blue-600">
                            Terpilih: 0 dari {{ $kapals->count() }} kapal
                        </span>
                        <div class="flex gap-2">
                            <button type="button" 
                                    id="selectAllKapalBtn"
                                    class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition duration-200">
                                Select All
                            </button>
                            <button type="button" 
                                    id="clearAllKapalBtn"
                                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded transition duration-200">
                                Clear Semua
                            </button>
                        </div>
                    </div>
                    
                    @error('nama_kapal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Voyage -->
                <div id="voyage_wrapper">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Voyage <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">✓ Multi-Select</span>
                    </label>
                    
                    {{-- Hidden inputs for selected voyage --}}
                    <div id="hidden_voyage_inputs"></div>
                    
                    {{-- Search input with dropdown --}}
                    <div class="relative">
                        <div class="w-full min-h-[42px] px-3 py-2 border border-gray-300 rounded-lg focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 bg-white cursor-text @error('no_voyage') border-red-500 @enderror" 
                             id="voyage_container_input"
                             onclick="document.getElementById('voyage_search').focus()">
                             
                            {{-- Selected voyage chips --}}
                            <div id="selected_voyage_chips" class="flex flex-wrap gap-1 mb-1"></div>
                            
                            {{-- Search input --}}
                            <input type="text" 
                                   id="voyage_search"
                                   placeholder="--Pilih Kapal Terlebih Dahulu--"
                                   class="border-0 outline-none bg-transparent flex-1 min-w-[200px]"
                                   autocomplete="off"
                                   disabled>
                        </div>
                        
                        {{-- Dropdown list --}}
                        <div id="voyage_dropdown" 
                             class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto hidden">
                            <p class="px-3 py-2 text-sm text-gray-500 italic">Pilih kapal terlebih dahulu</p>
                        </div>
                    </div>
                    
                    <div class="mt-2 flex justify-between items-center">
                        <span id="voyageSelectedCount" class="text-sm text-blue-600">
                            Terpilih: 0 voyage
                        </span>
                        <div class="flex gap-2">
                            <button type="button" 
                                    id="selectAllVoyageBtn"
                                    class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition duration-200">
                                Select All
                            </button>
                            <button type="button" 
                                    id="clearAllVoyageBtn"
                                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded transition duration-200">
                                Clear Semua
                            </button>
                        </div>
                    </div>
                    
                    @error('no_voyage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor BL -->
                <div id="bl_wrapper">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor BL <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">✓ Multi-Select</span>
                    </label>
                    
                    {{-- Hidden inputs for selected BL --}}
                    <div id="hidden_bl_inputs"></div>
                    
                    {{-- Search input with dropdown --}}
                    <div class="relative">
                        <div class="w-full min-h-[42px] px-3 py-2 border border-gray-300 rounded-lg focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 bg-white cursor-text @error('no_bl') border-red-500 @enderror" 
                             id="bl_container_input"
                             onclick="document.getElementById('bl_search').focus()">
                             
                            {{-- Selected BL chips --}}
                            <div id="selected_bl_chips" class="flex flex-wrap gap-1 mb-1"></div>
                            
                            {{-- Search input --}}
                            <input type="text" 
                                   id="bl_search"
                                   placeholder="--Pilih Voyage Terlebih Dahulu--"
                                   class="border-0 outline-none bg-transparent flex-1 min-w-[200px]"
                                   autocomplete="off"
                                   disabled>
                        </div>
                        
                        {{-- Dropdown list --}}
                        <div id="bl_dropdown" 
                             class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto hidden">
                            <p class="px-3 py-2 text-sm text-gray-500 italic">Pilih voyage terlebih dahulu</p>
                        </div>
                    </div>
                    
                    <div class="mt-2 flex justify-between items-center">
                        <span id="blSelectedCount" class="text-sm text-blue-600">
                            Terpilih: 0 BL
                        </span>
                        <div class="flex gap-2">
                            <button type="button" 
                                    id="selectAllBlBtn"
                                    class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition duration-200">
                                Select All
                            </button>
                            <button type="button" 
                                    id="clearAllBlBtn"
                                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded transition duration-200">
                                Clear Semua
                            </button>
                        </div>
                    </div>
                    
                    @error('no_bl')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Biaya -->
                <div>
                    <label for="jenis_biaya_search" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Biaya <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div id="jenis_biaya_container" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent @error('jenis_biaya') border-red-500 @enderror min-h-[42px] cursor-pointer">
                            <div class="flex items-center justify-between">
                                <input type="text" 
                                       id="jenis_biaya_search" 
                                       class="flex-1 outline-none bg-transparent text-sm" 
                                       placeholder="-- Pilih atau ketik untuk mencari jenis biaya --"
                                       autocomplete="off">
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                            <div id="selected_jenis_biaya_display" class="text-sm font-medium text-gray-700 hidden"></div>
                        </div>
                        
                        <!-- Hidden input for form submission -->
                        <input type="hidden" id="jenis_biaya" name="jenis_biaya" value="{{ old('jenis_biaya') }}" required>
                        
                        <!-- Dropdown list -->
                        <div id="jenis_biaya_dropdown" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <div class="sticky top-0 bg-gray-50 px-3 py-2 border-b border-gray-200 flex justify-between items-center">
                                <span class="text-xs font-medium text-gray-600" id="jenisBiayaSelectedCount">Pilih jenis biaya</span>
                                <button type="button" id="clearJenisBiayaBtn" class="text-xs text-red-600 hover:text-red-800 font-medium">
                                    <i class="fas fa-times mr-1"></i>Clear
                                </button>
                            </div>
                            @foreach($klasifikasiBiayas as $k)
                                <div class="jenis-biaya-option px-3 py-2 hover:bg-gray-50 cursor-pointer text-sm border-b border-gray-100 last:border-b-0"
                                     data-kode="{{ $k->kode }}"
                                     data-nama="{{ $k->nama }}">
                                    <div class="font-medium text-gray-900">{{ $k->nama }}</div>
                                    <div class="text-xs text-gray-500">{{ $k->kode }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @error('jenis_biaya')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Vendor (for Biaya Dokumen) -->
                <div id="vendor_wrapper" class="hidden">
                    <label for="vendor" class="block text-sm font-medium text-gray-700 mb-2">
                        Vendor <span class="text-red-500">*</span>
                    </label>
                    <select id="vendor" 
                            name="vendor_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Pilih Vendor --</option>
                        @foreach($pricelistBiayaDokumen as $pricelist)
                            <option value="{{ $pricelist->id }}" 
                                    data-biaya="{{ $pricelist->biaya }}"
                                    {{ old('vendor_id') == $pricelist->id ? 'selected' : '' }}>
                                {{ $pricelist->nama_vendor }} - Rp {{ number_format($pricelist->biaya, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Pilih vendor untuk biaya dokumen</p>
                    @error('vendor_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Detail Kapal Air Tawar (for Biaya Air) - MULTI KAPAL SYSTEM -->
                <div id="air_wrapper" class="md:col-span-2 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Detail Kapal & Air Tawar <span class="text-red-500">*</span>
                        </label>
                        <button type="button" id="add_air_section_btn" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Kapal</span>
                        </button>
                    </div>
                    <div id="air_sections_container"></div>
                </div>

                <!-- Barang (for Biaya Buruh) - NEW MULTI KAPAL SYSTEM -->
                <div id="barang_wrapper" class="md:col-span-2 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Detail Kapal & Barang <span class="text-red-500">*</span>
                        </label>
                        <button type="button" id="add_kapal_section_btn" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Kapal</span>
                        </button>
                    </div>
                    <div id="kapal_sections_container"></div>
                </div>

                <!-- TKBM (for Biaya TKBM) - SIMILAR TO BURUH SYSTEM -->
                <div id="tkbm_wrapper" class="md:col-span-2 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Detail Kapal & Barang TKBM <span class="text-red-500">*</span>
                        </label>
                        <button type="button" id="add_tkbm_section_btn" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Kapal</span>
                        </button>
                    </div>
                    <div id="tkbm_sections_container"></div>
                    
                    <button type="button" id="add_tkbm_section_bottom_btn" class="mt-2 w-full py-2 border-2 border-dashed border-amber-300 rounded-lg text-amber-600 hover:bg-amber-50 hover:border-amber-400 transition flex items-center justify-center gap-2 font-medium">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Kapal Lainnya</span>
                    </button>
                </div>

                <!-- Stuffing (for Biaya Stuffing) -->
                <div id="stuffing_wrapper" class="md:col-span-2 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Detail Kapal & Stuffing (Tanda Terima) <span class="text-red-500">*</span>
                        </label>
                        <button type="button" id="add_stuffing_section_btn" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Kapal</span>
                        </button>
                    </div>
                    <div id="stuffing_sections_container"></div>
                    
                    <button type="button" id="add_stuffing_section_bottom_btn" class="mt-2 w-full py-2 border-2 border-dashed border-rose-300 rounded-lg text-rose-600 hover:bg-rose-50 hover:border-rose-400 transition flex items-center justify-center gap-2 font-medium">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Kapal Lainnya</span>
                    </button>
                </div>

                <!-- Operasional (for Biaya Operasional) -->
                <div id="operasional_wrapper" class="md:col-span-2 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Detail Operasional <span class="text-red-500">*</span>
                        </label>
                        <button type="button" id="add_operasional_section_btn" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Kapal</span>
                        </button>
                    </div>
                    <div id="operasional_sections_container"></div>
                    
                    <button type="button" id="add_operasional_section_bottom_btn" class="mt-2 w-full py-2 border-2 border-dashed border-indigo-300 rounded-lg text-indigo-600 hover:bg-indigo-50 hover:border-indigo-400 transition flex items-center justify-center gap-2 font-medium">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Kapal Lainnya</span>
                    </button>
                </div>

                <!-- Nominal -->
                <div id="nominal_wrapper" class="hidden">
                    <label for="nominal" class="block text-sm font-medium text-gray-700 mb-2">
                        Nominal <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="nominal" 
                               name="nominal" 
                               value="{{ old('nominal') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nominal') border-red-500 @enderror"
                               placeholder="0"  
                               required>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Masukkan nominal tanpa titik atau koma</p>
                    @error('nominal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PPH Dokumen (for Biaya Dokumen - 2% dari nominal) -->
                <div id="pph_dokumen_wrapper" class="hidden">
                    <label for="pph_dokumen" class="block text-sm font-medium text-gray-700 mb-2">
                        PPH (2%)
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="pph_dokumen" 
                               name="pph_dokumen" 
                               value="{{ old('pph_dokumen', '0') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pph_dokumen') border-red-500 @enderror"
                               placeholder="0"
                               readonly>
                    </div>
                    <p class="mt-1 text-xs text-blue-600 font-medium">PPH = 2% × Nominal</p>
                    @error('pph_dokumen')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Grand Total Dokumen (for Biaya Dokumen - Nominal - PPH) -->
                <div id="grand_total_dokumen_wrapper" class="hidden">
                    <label for="grand_total_dokumen" class="block text-sm font-medium text-gray-700 mb-2">
                        Grand Total
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="grand_total_dokumen" 
                               name="grand_total_dokumen" 
                               value="{{ old('grand_total_dokumen', '') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg bg-green-50 font-semibold cursor-not-allowed focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('grand_total_dokumen') border-red-500 @enderror"
                               placeholder="0"
                               readonly>
                    </div>
                    <p class="mt-1 text-xs text-green-600 font-medium">Grand Total = Nominal - PPH</p>
                    @error('grand_total_dokumen')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Biaya Materai (for Biaya Penumpukan) -->
                <div id="biaya_materai_wrapper" class="hidden">
                    <label for="biaya_materai" class="block text-sm font-medium text-gray-700 mb-2">
                        Biaya Materai
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="biaya_materai" 
                               name="biaya_materai" 
                               value="{{ old('biaya_materai', '0') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('biaya_materai') border-red-500 @enderror"
                               placeholder="0">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Biaya materai untuk dokumen</p>
                    @error('biaya_materai')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- DP (for Biaya Buruh) -->
                <div id="dp_wrapper" class="hidden">
                    <label for="dp" class="block text-sm font-medium text-gray-700 mb-2">
                        DP / Uang Muka
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="dp" 
                               name="dp" 
                               value="{{ old('dp', '0') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('dp') border-red-500 @enderror"
                               placeholder="0">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Uang muka yang sudah dibayarkan</p>
                    @error('dp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sisa Pembayaran (for Biaya Buruh) -->
                <div id="sisa_pembayaran_wrapper" class="hidden">
                    <label for="sisa_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                        Sisa Pembayaran
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="sisa_pembayaran" 
                               name="sisa_pembayaran" 
                               value="{{ old('sisa_pembayaran', '0') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed @error('sisa_pembayaran') border-red-500 @enderror"
                               placeholder="0" 
                               readonly>
                    </div>
                    <p class="mt-1 text-xs text-blue-600 font-medium">Sisa = Nominal - DP</p>
                    @error('sisa_pembayaran')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Penerima -->
                <div id="penerima_wrapper">
                    <label for="penerima" class="block text-sm font-medium text-gray-700 mb-2">
                        Penerima <span class="text-red-500">*</span>
                    </label>
                    <select id="penerima" 
                            name="penerima" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('penerima') border-red-500 @enderror"
                            required>
                        <option value="">-- Pilih atau ketik nama penerima --</option>
                        @foreach($karyawans as $karyawan)
                            <option value="{{ $karyawan->nama_lengkap }}" {{ old('penerima') == $karyawan->nama_lengkap ? 'selected' : '' }}>
                                {{ $karyawan->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                    @error('penerima')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PPN (for Biaya Penumpukan) -->
                <div id="ppn_wrapper" class="hidden">
                    <label for="ppn" class="block text-sm font-medium text-gray-700 mb-2">
                        PPN
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="ppn" 
                               name="ppn" 
                               value="{{ old('ppn', '0') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ppn') border-red-500 @enderror"
                               placeholder="0">
                    </div>
                    @error('ppn')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PPH (for Biaya Penumpukan) -->
                <div id="pph_wrapper" class="hidden">
                    <label for="pph" class="block text-sm font-medium text-gray-700 mb-2">
                        PPH
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="pph" 
                               name="pph" 
                               value="{{ old('pph', '0') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pph') border-red-500 @enderror"
                               placeholder="0">
                    </div>
                    @error('pph')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Biaya (for Biaya Penumpukan) -->
                <div id="total_biaya_wrapper" class="hidden">
                    <label for="total_biaya" class="block text-sm font-medium text-gray-700 mb-2">
                        Total Biaya <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="total_biaya" 
                               name="total_biaya" 
                               value="{{ old('total_biaya') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('total_biaya') border-red-500 @enderror"
                               placeholder="0"
                               readonly>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Total = Nominal + PPN - PPH</p>
                    @error('total_biaya')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Vendor -->
                <div id="nama_vendor_wrapper">
                    <label for="nama_vendor" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Vendor
                    </label>
                    <input type="text" 
                           id="nama_vendor" 
                           name="nama_vendor" 
                           value="{{ old('nama_vendor') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama_vendor') border-red-500 @enderror"
                           placeholder="Masukkan nama vendor">
                    <p class="mt-1 text-xs text-gray-500">Nama perusahaan atau individu penerima pembayaran</p>
                    @error('nama_vendor')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Rekening -->
                <div id="nomor_rekening_wrapper">
                    <label for="nomor_rekening" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Rekening
                    </label>
                    <input type="text" 
                           id="nomor_rekening" 
                           name="nomor_rekening" 
                           value="{{ old('nomor_rekening') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nomor_rekening') border-red-500 @enderror"
                           placeholder="Contoh: 1234567890">
                    <p class="mt-1 text-xs text-gray-500">Nomor rekening bank penerima</p>
                    @error('nomor_rekening')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan
                    </label>
                    <textarea id="keterangan" 
                              name="keterangan" 
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('keterangan') border-red-500 @enderror"
                              placeholder="Masukkan keterangan atau catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Upload Bukti -->
                <div class="md:col-span-2">
                    <label for="bukti" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Bukti
                    </label>
                    <div class="flex items-center justify-center w-full">
                        <label for="bukti" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition duration-200">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                                <p class="mb-2 text-sm text-gray-500">
                                    <span class="font-semibold">Klik untuk upload</span> atau drag and drop
                                </p>
                                <p class="text-xs text-gray-500">PDF, PNG, JPG atau JPEG (Max. 2MB)</p>
                            </div>
                            <input id="bukti" 
                                   name="bukti" 
                                   type="file" 
                                   class="hidden" 
                                   accept=".pdf,.png,.jpg,.jpeg"
                                   onchange="updateFileName(this)">
                        </label>
                    </div>
                    <p id="file-name" class="mt-2 text-sm text-gray-600"></p>
                    @error('bukti')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-semibold text-blue-800">Informasi:</h4>
                        <ul class="mt-2 text-xs text-blue-700 list-disc list-inside space-y-1">
                            <li>Field yang bertanda <span class="text-red-500">*</span> wajib diisi</li>
                            <li><strong>Kapal, Voyage, dan BL mendukung multi-select</strong> - klik untuk menambahkan lebih dari satu</li>
                            <li>Gunakan tombol "Select All" untuk memilih semua atau "Clear Semua" untuk menghapus pilihan</li>
                            <li>Dropdown tetap terbuka untuk memudahkan memilih banyak item sekaligus</li>
                            <li>Nominal akan otomatis diformat dengan pemisah ribuan</li>
                            <li>Upload bukti bersifat opsional namun direkomendasikan untuk dokumentasi</li>
                            <li>Pastikan data yang diinput sudah benar sebelum menyimpan</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('biaya-kapal.index') }}" 
                   class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // PREPARE DATA FOR EDIT MODE
    @php
        $editKapalSections = [];
        $editAirSections = [];
        $editTkbmSections = [];
        $editOperasionalSections = [];

        // Group Buruh
        if($biayaKapal->barangDetails->count() > 0) {
            $grouped = $biayaKapal->barangDetails->groupBy(function($item) {
                return $item->kapal . '|||' . $item->voyage;
            });
            foreach($grouped as $key => $items) {
                 $parts = explode('|||', $key);
                 if(count($parts) == 2) {
                     $editKapalSections[] = [
                         'kapal' => $parts[0],
                         'voyage' => $parts[1],
                         'barang' => $items->map(function($i){ return ['barang_id' => $i->barang_id, 'jumlah' => $i->jumlah]; })
                     ];
                 }
            }
        }

        // Map Air
        foreach($biayaKapal->airDetails as $air) {
            $editAirSections[] = [
                'kapal' => $air->kapal,
                'voyage' => $air->voyage,
                'vendor' => $air->vendor,
                'type' => $air->type_id,
                'lokasi' => $air->lokasi,
                'kuantitas' => $air->kuantitas,
                'jasa_air' => $air->jasa_air,
                'biaya_agen' => $air->biaya_agen,
                'penerima' => $air->penerima,
                'nomor_rekening' => $air->nomor_rekening,
                'nomor_referensi' => $air->nomor_referensi,
                'tanggal_invoice_vendor' => $air->tanggal_invoice_vendor,
            ];
        }

        // Group TKBM
        if($biayaKapal->tkbmDetails->count() > 0) {
            $grouped = $biayaKapal->tkbmDetails->groupBy(function($item) {
                return $item->kapal . '|||' . $item->voyage . '|||' . ($item->no_referensi ?? '') . '|||' . ($item->tanggal_invoice_vendor ?? '');
            });
            foreach($grouped as $key => $items) {
                 $parts = explode('|||', $key); 
                 if(count($parts) >= 2) {
                     $firstItem = $items->first();
                     $editTkbmSections[] = [
                         'kapal' => $parts[0],
                         'voyage' => $parts[1],
                         'no_referensi' => $parts[2] ?? '',
                         'tanggal_invoice_vendor' => $parts[3] ?? '',
                         'adjustment' => $firstItem->adjustment ?? 0,
                         'barang' => $items->map(function($i){ return ['barang_id' => $i->pricelist_tkbm_id, 'jumlah' => $i->jumlah]; })
                     ];
                 }
            }
        }
        
        // Map Operasional
        if(old('operasional_sections')) {
            foreach(old('operasional_sections') as $oldOp) {
                // Nominal in old input might be formatted (e.g. 1.000.000)
                // We need to clean it for the JS to integers properly
                $rawNominal = isset($oldOp['nominal']) ? str_replace('.', '', $oldOp['nominal']) : 0;
                
                $editOperasionalSections[] = [
                    'kapal' => $oldOp['kapal'] ?? '',
                    'voyage' => $oldOp['voyage'] ?? '',
                    'nominal' => $rawNominal
                ];
            }
        } else {
            foreach($biayaKapal->operasionalDetails as $op) {
                $editOperasionalSections[] = [
                    'kapal' => $op->kapal,
                    'voyage' => $op->voyage,
                    'nominal' => $op->nominal
                ];
            }
        }

        // Map Stuffing
        $editStuffingSections = [];
        foreach($biayaKapal->stuffingDetails as $stuff) {
            $editStuffingSections[] = [
                'kapal' => $stuff->kapal,
                'voyage' => $stuff->voyage,
                'tanda_terima_ids' => $stuff->tanda_terima_ids ?? [],
            ];
        }
    @endphp

    var allKapalsData = @json($kapals);
    var existingKapalSections = @json($editKapalSections);
    var existingAirSections = @json($editAirSections);
    var existingTkbmSections = @json($editTkbmSections);
    var existingOperasionalSections = @json($editOperasionalSections);
    var existingStuffingSections = @json($editStuffingSections);

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initializeEditMode, 500);
    });

    function initializeEditMode() {
        console.log("Initializing Edit Mode Data...");
        
        // 1. Jenis Biaya
        const currentJenis = "{{ $biayaKapal->jenis_biaya }}";
        const jbOption = Array.from(document.querySelectorAll('.jenis-biaya-option')).find(o => o.getAttribute('data-kode') === currentJenis);
        if(jbOption) {
            jbOption.click();
        }

        // 2. BURUH SECTIONS
        if(existingKapalSections.length > 0) {
            clearAllKapalSections();
            existingKapalSections.forEach(myData => {
                addKapalSection();
                const sectionIndex = kapalSectionCounter;
                const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
                
                if(section) {
                    section.querySelector('.kapal-select').value = myData.kapal;
                    
                    const voySel = section.querySelector('.voyage-select');
                    voySel.innerHTML = `<option value="${myData.voyage}">${myData.voyage}</option>`;
                    voySel.value = myData.voyage;
                    voySel.disabled = false;
                    
                    section.querySelector('.barang-container-section').innerHTML = '';
                    myData.barang.forEach(b => {
                        addBarangToSectionWithValue(sectionIndex, b.barang_id, b.jumlah);
                    });
                }
            });
            calculateTotalFromAllSections();
        }

        // 3. AIR SECTIONS
        if(existingAirSections.length > 0) {
            // Need to expose addAirSection or click the button
            const addBtn = document.getElementById('add_air_section_btn');
            // But we can just use the function directly if available (it is inside script tag)
             // However, addAirSection uses 'airSectionCounter' which is global in that script.
             
             // We need to clear first?
             const airContainer = document.getElementById('air_sections_container');
             if(airContainer) airContainer.innerHTML = '';
             // Reset counter? No access to variable directly if scoped... wait, it is in script tag so global to window (or script block scope).
             // Since it's in the same file, variables are shared.
             
             existingAirSections.forEach(data => {
                 // Simulate adding section
                 document.getElementById('add_air_section_btn').click();
                 // The counter is incremented.
                 // We need to find the *last* added section. 
                 const sections = document.querySelectorAll('.air-section');
                 const sec = sections[sections.length - 1]; // Last one
                 const sectionIndex = sec.getAttribute('data-section-index');
                 
                 sec.querySelector('.kapal-select-air').value = data.kapal;
                 
                 const voySel = sec.querySelector('.voyage-select-air');
                 voySel.innerHTML = `<option value="${data.voyage}">${data.voyage}</option>`;
                 voySel.value = data.voyage;
                 voySel.disabled = false;
                 
                 sec.querySelector('.vendor-select-air').value = data.vendor;
                 loadTypesForVendor(sectionIndex, data.vendor); // Synchronous
                 
                 sec.querySelector('.type-select-air').value = data.type;
                 if(data.lokasi) sec.querySelector('.lokasi-select-air').value = data.lokasi;
                 sec.querySelector('.kuantitas-input-air').value = data.kuantitas;
                 sec.querySelector('.jasa-air-input').value = data.jasa_air;
                 sec.querySelector('.biaya-agen-input').value = data.biaya_agen;
                 if(data.penerima) sec.querySelector('.penerima-input-air').value = data.penerima;
                 if(data.nomor_rekening) sec.querySelector('.nomor-rekening-input-air').value = data.nomor_rekening;
                 if(data.nomor_referensi) sec.querySelector('input[name="air['+sectionIndex+'][nomor_referensi]"]').value = data.nomor_referensi;
                 if(data.tanggal_invoice_vendor) sec.querySelector('input[name="air['+sectionIndex+'][tanggal_invoice_vendor]"]').value = data.tanggal_invoice_vendor;
                 
                 calculateAirSectionTotal(sectionIndex);
             });
        }

        // 4. TKBM SECTIONS
        if(existingTkbmSections.length > 0) {
            clearAllTkbmSections();
            existingTkbmSections.forEach(data => {
                addTkbmSection();
                const sectionIndex = tkbmSectionCounter;
                const sec = document.querySelector(`[data-tkbm-section-index="${sectionIndex}"]`);
                
                sec.querySelector('.tkbm-kapal-select').value = data.kapal;
                
                const voySel = sec.querySelector('.tkbm-voyage-select');
                voySel.innerHTML = `<option value="${data.voyage}">${data.voyage}</option>`;
                voySel.value = data.voyage;
                voySel.disabled = false;
                
                sec.querySelector('input[name="tkbm_sections['+sectionIndex+'][no_referensi]"]').value = data.no_referensi;
                sec.querySelector('input[name="tkbm_sections['+sectionIndex+'][tanggal_invoice_vendor]"]').value = data.tanggal_invoice_vendor;
                if(data.adjustment) {
                    sec.querySelector('.tkbm-adjustment-input').value = data.adjustment;
                }
                
                sec.querySelector('.tkbm-barang-container').innerHTML = '';
                data.barang.forEach(b => {
                    addBarangToTkbmSectionWithValue(sectionIndex, b.barang_id, b.jumlah);
                });
            });
            calculateTotalFromAllTkbmSections();
        }

        // 5. OPERASIONAL SECTIONS
        initializeOperasionalSections();

        // 6. STUFFING SECTIONS
        if (existingStuffingSections.length > 0) {
            clearAllStuffingSections();
            existingStuffingSections.forEach(myData => {
                addStuffingSection();
                const sectionIndex = stuffingSectionCounter;
                const section = document.querySelector(`[data-stuffing-section-index="${sectionIndex}"]`);
                
                if (section) {
                    section.querySelector('.stuffing-kapal-select').value = myData.kapal;
                    
                    // Trigger voyage load or set manually
                    const voySel = section.querySelector('.stuffing-voyage-select');
                    voySel.innerHTML = `<option value="${myData.voyage}">${myData.voyage}</option>`;
                    voySel.value = myData.voyage;
                    voySel.disabled = false;
                    
                    // Load Tanda Terimas
                    const ttContainer = section.querySelector('.stuffing-tt-container');
                    ttContainer.innerHTML = '';
                    
                    if (myData.tanda_terima_ids && myData.tanda_terima_ids.length > 0) {
                        myData.tanda_terima_ids.forEach(ttId => {
                            addTandaTerimaToSectionWithId(sectionIndex, ttId);
                        });
                    } else {
                        addTandaTerimaToSection(sectionIndex);
                    }
                }
            });
        }
    }

    // New helper for TKBM
    window.addBarangToTkbmSectionWithValue = function(sectionIndex, barangId, jumlah) {
        const section = document.querySelector(`[data-tkbm-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.tkbm-barang-container');
        const barangIndex = container.children.length;
        
        // Use TKBM pricelist
        let barangOptions = '<option value="">Pilih Nama Barang</option>';
        pricelistTkbmData.forEach(pricelist => {
            const selected = pricelist.id == barangId ? 'selected' : '';
            barangOptions += `<option value="${pricelist.id}" data-tarif="${pricelist.tarif}" ${selected}>${pricelist.nama_barang}</option>`;
        });
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2';
        inputGroup.innerHTML = `
            <div class="flex-1">
                <select name="tkbm_sections[${sectionIndex}][barang][${barangIndex}][barang_id]" class="tkbm-barang-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-amber-500" required>
                    ${barangOptions}
                </select>
            </div>
            <div class="w-24">
                <input type="number" step="any" name="tkbm_sections[${sectionIndex}][barang][${barangIndex}][jumlah]" value="${jumlah}" class="tkbm-jumlah-input-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-amber-500" placeholder="0" required>
            </div>
            <button type="button" onclick="removeBarangFromTkbmSection(this)" class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition">
                <i class="fas fa-trash text-xs"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        // Event listeners
        const barangSelect = inputGroup.querySelector('.tkbm-barang-select-item');
        const jumlahInput = inputGroup.querySelector('.tkbm-jumlah-input-item');
        
        barangSelect.addEventListener('change', function() {
            calculateTotalFromAllTkbmSections();
        });
        
        jumlahInput.addEventListener('input', function() {
            calculateTotalFromAllTkbmSections();
        });
    };

    // Original Script Follows
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Store pricelist buruh data
    var pricelistBuruhData = {!! json_encode($pricelistBuruh) !!};

    // Store pricelist TKBM data for Biaya TKBM
    var pricelistTkbmData = {!! json_encode($pricelistTkbm ?? []) !!};
    
    // Store kapals data (required for dynamic sections)
    var allKapalsData = {!! json_encode($kapals) !!};

    // ============= JENIS BIAYA SEARCHABLE DROPDOWN =============
    var jenisBiayaSearch = document.getElementById('jenis_biaya_search');
    var jenisBiayaContainer = document.getElementById('jenis_biaya_container');
    var jenisBiayaDropdown = document.getElementById('jenis_biaya_dropdown');
    var jenisBiayaHiddenInput = document.getElementById('jenis_biaya');
    var selectedJenisBiayaDisplay = document.getElementById('selected_jenis_biaya_display');
    var jenisBiayaOptions = document.querySelectorAll('.jenis-biaya-option');
    var clearJenisBiayaBtn = document.getElementById('clearJenisBiayaBtn');
    var jenisBiayaSelectedCount = document.getElementById('jenisBiayaSelectedCount');
    
    var selectedJenisBiaya = { kode: '', nama: '' };
    var oldJenisBiayaValue = "{{ old('jenis_biaya') }}";
    
    // Show dropdown on focus
    jenisBiayaSearch.addEventListener('focus', function() {
        jenisBiayaDropdown.classList.remove('hidden');
        filterJenisBiayaOptions();
    });
    
    // Container click to focus search
    jenisBiayaContainer.addEventListener('click', function() {
        jenisBiayaSearch.focus();
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#jenis_biaya_container') && !e.target.closest('#jenis_biaya_dropdown')) {
            jenisBiayaDropdown.classList.add('hidden');
        }
    });
    
    // Search/filter options
    jenisBiayaSearch.addEventListener('input', function() {
        filterJenisBiayaOptions();
    });
    
    function filterJenisBiayaOptions() {
        const searchTerm = jenisBiayaSearch.value.toLowerCase();
        jenisBiayaOptions.forEach(option => {
            const nama = option.getAttribute('data-nama').toLowerCase();
            const kode = option.getAttribute('data-kode').toLowerCase();
            const shouldShow = nama.includes(searchTerm) || kode.includes(searchTerm);
            option.style.display = shouldShow ? 'block' : 'none';
        });
    }
    
    // Handle option selection
    jenisBiayaOptions.forEach(option => {
        option.addEventListener('click', function() {
            const kode = this.getAttribute('data-kode');
            const nama = this.getAttribute('data-nama');
            
            selectedJenisBiaya = { kode, nama };
            jenisBiayaHiddenInput.value = kode;
            
            // Update display
            jenisBiayaSearch.value = '';
            jenisBiayaSearch.classList.add('hidden');
            selectedJenisBiayaDisplay.textContent = nama;
            selectedJenisBiayaDisplay.classList.remove('hidden');
            
            // Remove selected class from all options
            jenisBiayaOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            
            jenisBiayaDropdown.classList.add('hidden');
            updateJenisBiayaSelectedCount();
            
            // Trigger change event for existing logic
            const event = new Event('change', { bubbles: true });
            jenisBiayaHiddenInput.dispatchEvent(event);
        });
    });
    
    // Clear selection
    clearJenisBiayaBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        clearJenisBiayaSelection();
    });
    
    function clearJenisBiayaSelection() {
        selectedJenisBiaya = { kode: '', nama: '' };
        jenisBiayaHiddenInput.value = '';
        jenisBiayaSearch.value = '';
        jenisBiayaSearch.classList.remove('hidden');
        selectedJenisBiayaDisplay.classList.add('hidden');
        jenisBiayaOptions.forEach(opt => opt.classList.remove('selected'));
        updateJenisBiayaSelectedCount();
        
        // Trigger change event
        const event = new Event('change', { bubbles: true });
        jenisBiayaHiddenInput.dispatchEvent(event);
    }
    
    function updateJenisBiayaSelectedCount() {
        if (selectedJenisBiaya.kode) {
            jenisBiayaSelectedCount.textContent = 'Terpilih: ' + selectedJenisBiaya.nama;
        } else {
            jenisBiayaSelectedCount.textContent = 'Pilih jenis biaya';
        }
    }
    
    // Restore old value on page load (for validation errors)
    if (oldJenisBiayaValue) {
        const option = Array.from(jenisBiayaOptions).find(opt => opt.getAttribute('data-kode') === oldJenisBiayaValue);
        if (option) {
            option.click();
        }
    }
    
    // Declare all input elements at the top
    var nominalInput = document.getElementById('nominal');
    var jenisBiayaSelect = document.getElementById('jenis_biaya');
    var barangWrapper = document.getElementById('barang_wrapper');
    var addBarangBtn = document.getElementById('add_barang_btn');
    var ppnWrapper = document.getElementById('ppn_wrapper');
    var pphWrapper = document.getElementById('pph_wrapper');
    var totalBiayaWrapper = document.getElementById('total_biaya_wrapper');
    var ppnInput = document.getElementById('ppn');
    var pphInput = document.getElementById('pph');
    var totalBiayaInput = document.getElementById('total_biaya');
    var blWrapper = document.getElementById('bl_wrapper');
    var kapalWrapper = document.getElementById('kapal_wrapper');
    var voyageWrapper = document.getElementById('voyage_wrapper');
    var dpWrapper = document.getElementById('dp_wrapper');
    var sisaPembayaranWrapper = document.getElementById('sisa_pembayaran_wrapper');
    var dpInput = document.getElementById('dp');
    var sisaPembayaranInput = document.getElementById('sisa_pembayaran');
    var vendorWrapper = document.getElementById('vendor_wrapper');
    var vendorSelect = document.getElementById('vendor');
    var biayaMateraiWrapper = document.getElementById('biaya_materai_wrapper');
    var biayaMateraiInput = document.getElementById('biaya_materai');
    
    // Biaya Dokumen specific fields
    var pphDokumenWrapper = document.getElementById('pph_dokumen_wrapper');
    var grandTotalDokumenWrapper = document.getElementById('grand_total_dokumen_wrapper');
    var pphDokumenInput = document.getElementById('pph_dokumen');
    var grandTotalDokumenInput = document.getElementById('grand_total_dokumen');
    
    // Biaya Air specific fields
    var airWrapper = document.getElementById('air_wrapper');
    var vendorAirWrapper = document.getElementById('vendor_air_wrapper');
    var vendorAirSelect = document.getElementById('vendor_air');
    var typeAirWrapper = document.getElementById('type_air_wrapper');
    var typeAirInput = document.getElementById('type_air');
    var kuantitasAirWrapper = document.getElementById('kuantitas_air_wrapper');
    var kuantitasAirInput = document.getElementById('kuantitas_air');
    var jasaAirWrapper = document.getElementById('jasa_air_wrapper');
    var operasionalWrapper = document.getElementById('operasional_wrapper');
    var jasaAirInput = document.getElementById('jasa_air');
    var pphAirWrapper = document.getElementById('pph_air_wrapper');
    var pphAirInput = document.getElementById('pph_air');
    var grandTotalAirWrapper = document.getElementById('grand_total_air_wrapper');
    var grandTotalAirInput = document.getElementById('grand_total_air');
    
    // Standard field wrappers
    var nominalWrapper = document.getElementById('nominal_wrapper');
    var penerimaWrapper = document.getElementById('penerima_wrapper');
    var penerimaInput = document.getElementById('penerima');
    var namaVendorWrapper = document.getElementById('nama_vendor_wrapper');
    var nomorRekeningWrapper = document.getElementById('nomor_rekening_wrapper');
    var nomorReferensiWrapper = document.getElementById('nomor_referensi_wrapper');
    
    // Stuffing specific fields
    const stuffingWrapper = document.getElementById('stuffing_wrapper');
    const stuffingSectionsContainer = document.getElementById('stuffing_sections_container');
    const addStuffingSectionBtn = document.getElementById('add_stuffing_section_btn');
    const addStuffingSectionBottomBtn = document.getElementById('add_stuffing_section_bottom_btn');

    // Pricelist Air Tawar data
    var pricelistAirTawarData = {!! json_encode($pricelistAirTawar) !!};

    // Format nominal input with thousand separator
    
    nominalInput.addEventListener('input', function(e) {
        // Remove all non-numeric characters
        let value = this.value.replace(/\D/g, '');
        
        // Format with thousand separator
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        
        this.value = value;
        
        // Recalculate based on jenis biaya
        const selectedText = selectedJenisBiaya.nama || '';
        if (selectedText.toLowerCase().includes('dokumen') || selectedText.toLowerCase().includes('listrik') || selectedText.toLowerCase().includes('trucking')) {
            calculatePphDokumen();
        } else if (selectedText.toLowerCase().includes('penumpukan')) {
            calculatePpnPenumpukan();
        } else if (selectedText.toLowerCase().includes('buruh')) {
            calculateSisaPembayaran();
        }
    });
    
    // Format DP input
    dpInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        this.value = value;
        calculateSisaPembayaran();
    });
    
    // Format PPN input
    ppnInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        this.value = value;
        calculateTotalBiaya();
    });
    
    // Format PPH input
    pphInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        this.value = value;
        calculateTotalBiaya();
    });
    
    // Format Biaya Materai input
    biayaMateraiInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        this.value = value;
        calculateTotalBiaya();
    });
    
    // Calculate Sisa Pembayaran = Nominal - DP (for Biaya Buruh)
    function calculateSisaPembayaran() {
        const nominal = parseInt(nominalInput.value.replace(/\D/g, '') || 0);
        const dp = parseInt(dpInput.value.replace(/\D/g, '') || 0);
        
        const sisa = nominal - dp;
        sisaPembayaranInput.value = sisa > 0 ? sisa.toLocaleString('id-ID') : '0';
    }
    
    // Calculate PPH Dokumen (2% dari nominal) and Grand Total (for Biaya Dokumen)
    function calculatePphDokumen() {
        const nominal = parseInt(nominalInput.value.replace(/\D/g, '') || 0);
        
        // PPH = 2% dari nominal
        const pph = Math.round(nominal * 0.02);
        pphDokumenInput.value = pph > 0 ? pph.toLocaleString('id-ID') : '0';
        
        // Grand Total = Nominal - PPH
        const grandTotal = nominal - pph;
        grandTotalDokumenInput.value = grandTotal > 0 ? grandTotal.toLocaleString('id-ID') : '0';
    }
    
    // Calculate PPH Penumpukan (2% dari nominal) for Biaya Penumpukan
    function calculatePphPenumpukan() {
        const nominal = parseInt(nominalInput.value.replace(/\D/g, '') || 0);
        
        // PPH = 2% dari nominal
        const pph = Math.round(nominal * 0.02);
        pphInput.value = pph > 0 ? pph.toLocaleString('id-ID') : '0';
        
        // Recalculate total biaya
        calculateTotalBiaya();
    }
    
    // Calculate PPN Penumpukan (11% dari nominal) for Biaya Penumpukan
    function calculatePpnPenumpukan() {
        const nominal = parseInt(nominalInput.value.replace(/\D/g, '') || 0);
        
        // PPN = 11% dari nominal
        const ppn = Math.round(nominal * 0.11);
        ppnInput.value = ppn > 0 ? ppn.toLocaleString('id-ID') : '0';
        
        // Auto-calculate PPH after PPN
        calculatePphPenumpukan();
    }
    
    // Calculate Total Biaya = Nominal + PPN + Materai - PPH
    function calculateTotalBiaya() {
        const nominal = parseInt(nominalInput.value.replace(/\D/g, '') || 0);
        const ppn = parseInt(ppnInput.value.replace(/\D/g, '') || 0);
        const pph = parseInt(pphInput.value.replace(/\D/g, '') || 0);
        const materai = parseInt(biayaMateraiInput.value.replace(/\D/g, '') || 0);
        
        const total = nominal + ppn + materai - pph;
        totalBiayaInput.value = total > 0 ? total.toLocaleString('id-ID') : '';
    }

    // Before form submit, remove formatting from all currency fields
    document.querySelector('form').addEventListener('submit', function(e) {
        nominalInput.value = nominalInput.value.replace(/\./g, '');
        ppnInput.value = ppnInput.value.replace(/\./g, '');
        pphInput.value = pphInput.value.replace(/\./g, '');
        if (dpInput.value) {
            dpInput.value = dpInput.value.replace(/\./g, '');
        }
        if (sisaPembayaranInput.value) {
            sisaPembayaranInput.value = sisaPembayaranInput.value.replace(/\./g, '');
        }
        if (totalBiayaInput.value) {
            totalBiayaInput.value = totalBiayaInput.value.replace(/\./g, '');
        }
        // Clean Biaya Dokumen fields
        if (pphDokumenInput && pphDokumenInput.value) {
            pphDokumenInput.value = pphDokumenInput.value.replace(/\./g, '');
        }
        if (grandTotalDokumenInput && grandTotalDokumenInput.value) {
            grandTotalDokumenInput.value = grandTotalDokumenInput.value.replace(/\./g, '');
        }
        // Clean Biaya Materai field
        if (biayaMateraiInput && biayaMateraiInput.value) {
            biayaMateraiInput.value = biayaMateraiInput.value.replace(/\./g, '');
        }
        // Clean Biaya Air fields
        if (jasaAirInput && jasaAirInput.value) {
            jasaAirInput.value = jasaAirInput.value.replace(/\./g, '');
        }
        if (pphAirInput && pphAirInput.value) {
            pphAirInput.value = pphAirInput.value.replace(/\./g, '');
        }
        if (grandTotalAirInput && grandTotalAirInput.value) {
            grandTotalAirInput.value = grandTotalAirInput.value.replace(/\./g, '');
        }
        // Sanitize per-section numeric hidden inputs to ensure validation accepts numbers
        document.querySelectorAll('.sub-total-value').forEach(el => {
            el.value = String(el.value).replace(/\./g, '').replace(/[^0-9\-]/g, '');
        });
        document.querySelectorAll('.pph-value').forEach(el => {
            el.value = String(el.value).replace(/\./g, '').replace(/[^0-9\-]/g, '');
        });
        document.querySelectorAll('.grand-total-value').forEach(el => {
            el.value = String(el.value).replace(/\./g, '').replace(/[^0-9\-]/g, '');
        });
    });

    // Update file name display
    function updateFileName(input) {
        const fileNameDisplay = document.getElementById('file-name');
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2); // Convert to MB
            fileNameDisplay.innerHTML = `<i class="fas fa-file-alt mr-2 text-blue-600"></i><span class="font-medium">File terpilih:</span> ${fileName} (${fileSize} MB)`;
        } else {
            fileNameDisplay.innerHTML = '';
        }
    }

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.bg-red-50');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

    // Function to calculate nominal for Biaya Dokumen (vendor tariff × number of containers)
    function calculateDokumenNominal() {
        const selectedJenisBiaya = jenisBiayaSelect.options[jenisBiayaSelect.selectedIndex].text;
        
        // Only calculate if jenis biaya is "Biaya Dokumen"
        if (!selectedJenisBiaya.toLowerCase().includes('dokumen')) {
            return;
        }
        
        const selectedOption = vendorSelect.options[vendorSelect.selectedIndex];
        const biaya = selectedOption.getAttribute('data-biaya');
        const jumlahKontainer = Object.keys(selectedBls).length;
        
        console.log('Calculating Dokumen Nominal:');
        console.log('- Tarif vendor:', biaya);
        console.log('- Jumlah kontainer:', jumlahKontainer);
        
        if (biaya && biaya !== '' && biaya !== '0' && jumlahKontainer > 0) {
            const totalNominal = parseInt(biaya) * jumlahKontainer;
            const formattedNominal = totalNominal.toLocaleString('id-ID');
            nominalInput.value = formattedNominal;
            console.log('- Total nominal:', formattedNominal);
            
            // Calculate PPH and Grand Total after nominal is updated
            calculatePphDokumen();
        } else if (biaya && biaya !== '' && biaya !== '0') {
            // If vendor selected but no containers yet, show vendor tariff
            const formattedBiaya = parseInt(biaya).toLocaleString('id-ID');
            nominalInput.value = formattedBiaya;
            console.log('- No containers selected, showing vendor tariff:', formattedBiaya);
            
            // Calculate PPH and Grand Total after nominal is updated
            calculatePphDokumen();
        } else {
            nominalInput.value = '';
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
        }
    }

    // Auto-fill nominal from vendor selection
    vendorSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const biaya = selectedOption.getAttribute('data-biaya');
        
        console.log('Vendor selected:', this.value);
        console.log('Biaya from vendor:', biaya);
        
        const selectedJenisBiaya = jenisBiayaSelect.options[jenisBiayaSelect.selectedIndex].text;
        
        // If Biaya Dokumen, use the calculate function
        if (selectedJenisBiaya.toLowerCase().includes('dokumen')) {
            calculateDokumenNominal();
        } else {
            // For other jenis biaya, use original logic
            if (biaya && biaya !== '' && biaya !== '0') {
                // Format biaya with thousand separator
                const formattedBiaya = parseInt(biaya).toLocaleString('id-ID');
                nominalInput.value = formattedBiaya;
                nominalInput.focus();
                
                console.log('Nominal set to:', formattedBiaya);
            } else {
                // Clear nominal if no vendor selected or biaya is 0
                nominalInput.value = '';
            }
        }
    });

    // ============= JENIS BIAYA TOGGLE =============
    // Toggle barang wrapper based on jenis biaya
    jenisBiayaSelect.addEventListener('change', function() {
        const selectedValue = this.value;
        const selectedText = selectedJenisBiaya.nama || '';
        
        // Reset visibility of standard fields
        if(nominalWrapper) nominalWrapper.classList.remove('hidden');
        if(penerimaWrapper) penerimaWrapper.classList.remove('hidden');
        if(namaVendorWrapper) namaVendorWrapper.classList.remove('hidden');
        if(nomorRekeningWrapper) nomorRekeningWrapper.classList.remove('hidden');
        if(nomorReferensiWrapper) nomorReferensiWrapper.classList.remove('hidden');
        
        // Reset required attributes
        if(nominalInput) nominalInput.setAttribute('required', 'required');
        if(penerimaInput) penerimaInput.setAttribute('required', 'required');

        // Show vendor wrapper if "Biaya Dokumen" is selected
        if (selectedText.toLowerCase().includes('dokumen')) {
            vendorWrapper.classList.remove('hidden');
            
            // Show PPH Dokumen and Grand Total fields for Biaya Dokumen
            pphDokumenWrapper.classList.remove('hidden');
            grandTotalDokumenWrapper.classList.remove('hidden');
            
            // Hide other type-specific fields
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            airWrapper.classList.add('hidden');
            clearAllAirSections();
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            
            // Hide TKBM wrapper
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            
            // Show standard fields
            kapalWrapper.classList.remove('hidden');
            voyageWrapper.classList.remove('hidden');
            blWrapper.classList.remove('hidden');
            
            // Reset values
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';
            
            // Calculate PPH if nominal already filled
            if (nominalInput.value) {
                calculatePphDokumen();
            }
        }
        // Show PPH fields if "Biaya Listrik" is selected
        else if (selectedText.toLowerCase().includes('listrik')) {
            // Show PPH Dokumen and Grand Total fields for Biaya Listrik
            pphDokumenWrapper.classList.remove('hidden');
            grandTotalDokumenWrapper.classList.remove('hidden');
            
            // Hide other type-specific fields
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            
            // Show standard fields
            kapalWrapper.classList.remove('hidden');
            voyageWrapper.classList.remove('hidden');
            blWrapper.classList.remove('hidden');
            
            // Reset values
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';
            
            // Calculate PPH if nominal already filled
            if (nominalInput.value) {
                calculatePphDokumen();
            }
        }
        // Show PPH fields if "Biaya Trucking" is selected
        else if (selectedText.toLowerCase().includes('trucking')) {
            // Show PPH Dokumen and Grand Total fields for Biaya Trucking
            pphDokumenWrapper.classList.remove('hidden');
            grandTotalDokumenWrapper.classList.remove('hidden');
            
            // Hide other type-specific fields
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            airWrapper.classList.add('hidden');
            clearAllAirSections();
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            
            // Show standard fields
            kapalWrapper.classList.remove('hidden');
            voyageWrapper.classList.remove('hidden');
            blWrapper.classList.remove('hidden');
            
            // Reset values
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';
            
            // Calculate PPH if nominal already filled
            if (nominalInput.value) {
                calculatePphDokumen();
            }
        }
        // Show fields for "Biaya Air"
        else if (selectedText.toLowerCase().includes('air')) {
            // Show Biaya Air multi kapal wrapper
            if (airWrapper) airWrapper.classList.remove('hidden');
            initializeAirSections();
            
            // Show summary fields (with null checks)
            if (jasaAirWrapper) jasaAirWrapper.classList.remove('hidden');
            if (pphAirWrapper) pphAirWrapper.classList.remove('hidden');
            if (grandTotalAirWrapper) grandTotalAirWrapper.classList.remove('hidden');
            
            // Hide standard kapal/voyage/bl fields (already in air sections)
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide Nomor Referensi for Biaya Air
            if (nomorReferensiWrapper) nomorReferensiWrapper.classList.add('hidden');
            
            // Hide standard fields for Biaya Air
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(penerimaWrapper) penerimaWrapper.classList.add('hidden');
            if(namaVendorWrapper) namaVendorWrapper.classList.add('hidden');
            if(nomorRekeningWrapper) nomorRekeningWrapper.classList.add('hidden');
            
            // Remove required attributes for hidden fields
            if(nominalInput) nominalInput.removeAttribute('required');
            if(penerimaInput) penerimaInput.removeAttribute('required');
            
            // Hide other type-specific fields (with null checks)
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            if (vendorAirWrapper) vendorAirWrapper.classList.add('hidden');
            if (typeAirWrapper) typeAirWrapper.classList.add('hidden');
            if (kuantitasAirWrapper) kuantitasAirWrapper.classList.add('hidden');
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            biayaMateraiWrapper.classList.add('hidden');
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            
            // Reset values (with null checks)
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';
            biayaMateraiInput.value = '0';
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
            nominalInput.value = '';
            if (jasaAirInput) jasaAirInput.value = '0';
            if (pphAirInput) pphAirInput.value = '0';
            if (grandTotalAirInput) grandTotalAirInput.value = '0';
            
            // Hide TKBM wrapper
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
        }
        // Show barang wrapper if "Biaya Buruh" is selected
        else if (selectedText.toLowerCase().includes('buruh')) {
            barangWrapper.classList.remove('hidden');
            initializeKapalSections();
            
            // Hide Nama Kapal and Nomor Voyage fields (already in section)
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            
            // Hide BL wrapper for Biaya Buruh
            blWrapper.classList.add('hidden');
            clearBlSelections();
            
            // Hide PPN/PPH fields for Biaya Buruh
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            
            // Hide vendor wrapper for Biaya Buruh
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            
            // Hide PPH Dokumen fields for Biaya Buruh
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
            
            // Hide Biaya Air fields for Biaya Buruh
            if (airWrapper) airWrapper.classList.add('hidden');
            clearAllAirSections();
            if (jasaAirWrapper) jasaAirWrapper.classList.add('hidden');
            if (pphAirWrapper) pphAirWrapper.classList.add('hidden');
            if (grandTotalAirWrapper) grandTotalAirWrapper.classList.add('hidden');
            
            // Show DP fields for Biaya Buruh
            dpWrapper.classList.remove('hidden');
            sisaPembayaranWrapper.classList.remove('hidden');
            calculateSisaPembayaran();
            
            // Hide TKBM wrapper for Biaya Buruh
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper for Biaya Buruh
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
        }
        // Show TKBM wrapper if "Biaya KTKBM" is selected
        else if (selectedText.toLowerCase().includes('ktkbm')) {
            document.getElementById('tkbm_wrapper').classList.remove('hidden');
            initializeTkbmSections();
            
            // Hide Operasional wrapper for Biaya TKBM
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            
            // Hide Nama Kapal and Nomor Voyage fields (already in section)
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            
            // Hide BL wrapper for Biaya TKBM
            blWrapper.classList.add('hidden');
            clearBlSelections();
            
            // Hide PPN/PPH fields for Biaya TKBM
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            
            // Hide vendor wrapper for Biaya TKBM
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            
            // Hide PPH Dokumen fields for Biaya TKBM
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
            
            // Hide Biaya Air fields for Biaya TKBM
            if (airWrapper) airWrapper.classList.add('hidden');
            clearAllAirSections();
            if (jasaAirWrapper) jasaAirWrapper.classList.add('hidden');
            if (pphAirWrapper) pphAirWrapper.classList.add('hidden');
            if (grandTotalAirWrapper) grandTotalAirWrapper.classList.add('hidden');
            
            // Hide Biaya Buruh fields for Biaya TKBM
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            
            // Show DP fields for Biaya TKBM
            dpWrapper.classList.remove('hidden');
            sisaPembayaranWrapper.classList.remove('hidden');
            calculateSisaPembayaran();
        }
        // Show operasional wrapper if "Operasional" is selected
        else if (selectedText.toLowerCase().includes('operasional')) {
            operasionalWrapper.classList.remove('hidden');
            if (operasionalSectionsContainer.children.length === 0) {
                addOperasionalSection();
            }
            
            // Hide other wrappers
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            
            // Hide Nama Kapal and Nomor Voyage fields (already in section)
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            
            // Hide BL wrapper
            blWrapper.classList.add('hidden');
            clearBlSelections();
            
            // Hide PPN/PPH fields
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            
            // Hide vendor wrapper
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            
            // Hide PPH Dokumen fields
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
            
            // Hide Biaya Air fields
            if (airWrapper) airWrapper.classList.add('hidden');
            clearAllAirSections();
            if (jasaAirWrapper) jasaAirWrapper.classList.add('hidden');
            if (pphAirWrapper) pphAirWrapper.classList.add('hidden');
            if (grandTotalAirWrapper) grandTotalAirWrapper.classList.add('hidden');
            
            // Hide TKBM wrapper
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Show DP fields for Biaya Operasional (Like Buruh)
            dpWrapper.classList.remove('hidden');
            sisaPembayaranWrapper.classList.remove('hidden');
            calculateSisaPembayaran();
        }
        // Show stuffing wrapper if "Stuffing" is selected
        else if (selectedText.toLowerCase().includes('stuffing')) {
            stuffingWrapper.classList.remove('hidden');
            if (stuffingSectionsContainer.children.length === 0) {
                addStuffingSection();
            }
            
            // Hide other wrappers
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            
            // Hide Nama Kapal and Nomor Voyage fields (already in section)
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            
            // Hide BL wrapper
            blWrapper.classList.add('hidden');
            clearBlSelections();
            
            // Hide PPN/PPH fields
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            
            // Hide vendor wrapper
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            
            // Hide PPH Dokumen fields
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
            
            // Hide Biaya Air fields
            if (airWrapper) airWrapper.classList.add('hidden');
            clearAllAirSections();
            
            // Show DP fields (Like Buruh)
            dpWrapper.classList.remove('hidden');
            sisaPembayaranWrapper.classList.remove('hidden');
            calculateSisaPembayaran();
        } else if (selectedText.toLowerCase().includes('penumpukan')) {
            // Show PPN/PPH fields for Biaya Penumpukan
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            ppnWrapper.classList.remove('hidden');
            pphWrapper.classList.remove('hidden');
            totalBiayaWrapper.classList.remove('hidden');
            
            // Show Biaya Materai for Biaya Penumpukan
            biayaMateraiWrapper.classList.remove('hidden');
            
            // Hide DP fields for Biaya Penumpukan
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';
            
            // Hide vendor wrapper for Biaya Penumpukan
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            
            // Hide PPH Dokumen fields for Biaya Penumpukan
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
            
            // Hide Biaya Air fields for Biaya Penumpukan
            if (airWrapper) airWrapper.classList.add('hidden');
            clearAllAirSections();
            if (jasaAirWrapper) jasaAirWrapper.classList.add('hidden');
            if (pphAirWrapper) pphAirWrapper.classList.add('hidden');
            if (grandTotalAirWrapper) grandTotalAirWrapper.classList.add('hidden');
            
            // Auto-calculate PPN (11%) and PPH (2% dari nominal) for Biaya Penumpukan
            calculatePpnPenumpukan();
            
            // Show Nama Kapal and Nomor Voyage fields
            kapalWrapper.classList.remove('hidden');
            voyageWrapper.classList.remove('hidden');
            
            // Show BL wrapper for Biaya Penumpukan
            blWrapper.classList.remove('hidden');
            
            // Hide TKBM wrapper for Biaya Penumpukan
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }

            // Hide Operasional wrapper
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            
            // Calculate initial total
            calculateTotalBiaya();
        } else {
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            
            // Hide TKBM wrapper for other types
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper for other types
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            
            // Hide PPN/PPH fields for other types
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            
            // Hide DP fields for other types
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';
            
            // Hide Biaya Materai for other types
            biayaMateraiWrapper.classList.add('hidden');
            biayaMateraiInput.value = '0';
            
            // Hide vendor wrapper for other types
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            
            // Hide PPH Dokumen fields for other types
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
            
            // Hide Biaya Air fields
            if (airWrapper) airWrapper.classList.add('hidden');
            clearAllAirSections();
            if (vendorAirWrapper) vendorAirWrapper.classList.add('hidden');
            if (typeAirWrapper) typeAirWrapper.classList.add('hidden');
            if (kuantitasAirWrapper) kuantitasAirWrapper.classList.add('hidden');
            if (jasaAirWrapper) jasaAirWrapper.classList.add('hidden');
            if (pphAirWrapper) pphAirWrapper.classList.add('hidden');
            if (grandTotalAirWrapper) grandTotalAirWrapper.classList.add('hidden');
            if (jasaAirInput) jasaAirInput.value = '0';
            if (pphAirInput) pphAirInput.value = '0';
            if (grandTotalAirInput) grandTotalAirInput.value = '0';
            
            // Clear calculated total when switching away from Biaya Buruh
            nominalInput.value = '';
            
            // Show Nama Kapal and Nomor Voyage fields for other types
            kapalWrapper.classList.remove('hidden');
            voyageWrapper.classList.remove('hidden');
            
            // Show BL wrapper for other types
            blWrapper.classList.remove('hidden');
        }
    });
    
    // Function to clear BL selections
    function clearBlSelections() {
        selectedBls = {};
        selectedBlChips.innerHTML = '';
        hiddenBlInputs.innerHTML = '';
        const blOptions = document.querySelectorAll('.bl-option');
        blOptions.forEach(option => option.classList.remove('selected'));
        updateBlSelectedCount();
    }
    
    // Function to clear Kapal selections
    function clearKapalSelections() {
        selectedKapals = [];
        selectedKapalChips.innerHTML = '';
        hiddenKapalInputs.innerHTML = '';
        kapalOptions.forEach(option => option.classList.remove('selected'));
        updateKapalSelectedCount();
    }
    
    // Function to clear Voyage selections
    function clearVoyageSelections() {
        selectedVoyages = [];
        selectedVoyageChips.innerHTML = '';
        hiddenVoyageInputs.innerHTML = '';
        const voyageOptions = document.querySelectorAll('.voyage-option');
        voyageOptions.forEach(option => option.classList.remove('selected'));
        updateVoyageSelectedCount();
    }

    // ============= NEW KAPAL SECTIONS MANAGEMENT =============
    var kapalSectionCounter = 0;
    var kapalSectionsContainer = document.getElementById('kapal_sections_container');
    var addKapalSectionBtn = document.getElementById('add_kapal_section_btn');
    var allKapalsData = @json($kapals);
    
    function initializeKapalSections() {
        kapalSectionsContainer.innerHTML = '';
        kapalSectionCounter = 0;
        addKapalSection();
    }
    
    function clearAllKapalSections() {
        kapalSectionsContainer.innerHTML = '';
        kapalSectionCounter = 0;
        nominalInput.value = '';
    }
    
    addKapalSectionBtn.addEventListener('click', function() {
        addKapalSection();
    });
    
    function addKapalSection() {
        kapalSectionCounter++;
        const sectionIndex = kapalSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'kapal-section mb-6 p-4 border-2 border-blue-200 rounded-lg bg-blue-50';
        section.setAttribute('data-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex}</h3>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeKapalSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="kapal_sections[${sectionIndex}][kapal]" class="kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="kapal_sections[${sectionIndex}][voyage]" class="voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500" required disabled>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="kapal_sections[${sectionIndex}][voyage]" class="voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="voyage-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="block text-xs font-medium text-gray-700 mb-2">Detail Barang</label>
                <div class="barang-container-section" data-section="${sectionIndex}"></div>
                <button type="button" onclick="addBarangToSection(${sectionIndex})" class="mt-2 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded-lg transition">
                    <i class="fas fa-plus mr-1"></i> Tambah Barang
                </button>
            </div>
        `;
        
        kapalSectionsContainer.appendChild(section);
        
        // Setup kapal change listener
        const kapalSelect = section.querySelector('.kapal-select');
        const voyageSelectArray = section.querySelectorAll('.voyage-select'); // Use querySelector because it's único per section or querySelectorAll if needed? Just one.
        const voyageSelect = section.querySelector('.voyage-select');
        
        kapalSelect.addEventListener('change', function() {
            loadVoyagesForSection(sectionIndex, this.value);
        });
        
        // Setup voyage change listener for auto-fill barang
        voyageSelect.addEventListener('change', function() {
            const kapalNama = kapalSelect.value;
            const voyageValue = this.value;
            if (kapalNama && voyageValue) {
                autoFillBarangForSection(sectionIndex, kapalNama, voyageValue);
            }
        });

        // Setup manual voyage toggle
        const voyageInput = section.querySelector('.voyage-input');
        const voyageManualBtn = section.querySelector('.voyage-manual-btn');

        voyageManualBtn.addEventListener('click', function() {
            if (voyageInput.classList.contains('hidden')) {
                // Switch to manual input
                voyageSelect.classList.add('hidden');
                voyageSelect.disabled = true;
                
                voyageInput.classList.remove('hidden');
                voyageInput.disabled = false;
                voyageInput.focus();
                
                this.classList.remove('bg-gray-200', 'text-gray-600');
                this.classList.add('bg-blue-200', 'text-blue-700');
                this.innerHTML = '<i class="fas fa-list"></i>';
            } else {
                // Switch to select list
                voyageInput.classList.add('hidden');
                voyageInput.disabled = true;
                
                voyageSelect.classList.remove('hidden');
                voyageSelect.disabled = false;
                
                this.classList.add('bg-gray-200', 'text-gray-600');
                this.classList.remove('bg-blue-200', 'text-blue-700');
                this.innerHTML = '<i class="fas fa-keyboard"></i>';
            }
        });
        
        // Add first barang input
        addBarangToSection(sectionIndex);
    }
    
    // Auto-fill barang based on container counts from BL table
    function autoFillBarangForSection(sectionIndex, kapalNama, voyage) {
        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.barang-container-section');
        
        // Show loading
        container.innerHTML = '<div class="text-sm text-gray-500 italic py-2"><i class="fas fa-spinner fa-spin mr-2"></i>Menghitung kontainer...</div>';
        
        fetch('{{ url("biaya-kapal/get-container-counts") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                kapal: kapalNama,
                voyage: voyage
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.counts) {
                container.innerHTML = '';
                let barangAdded = false;
                
                // Pricelist IDs mapping
                const pricelistIds = {
                    '20_full': null,
                    '20_empty': null,
                    '40_full': null,
                    '40_empty': null
                };
                
                // Find pricelist IDs from pricelistBuruhData
                pricelistBuruhData.forEach(p => {
                    const barangLower = p.barang.toLowerCase();
                    if (barangLower.includes('kontainer') && barangLower.includes('20') && barangLower.includes('full')) {
                        pricelistIds['20_full'] = p.id;
                    } else if (barangLower.includes('kontainer') && barangLower.includes('20') && barangLower.includes('empty')) {
                        pricelistIds['20_empty'] = p.id;
                    } else if (barangLower.includes('kontainer') && barangLower.includes('40') && barangLower.includes('full')) {
                        pricelistIds['40_full'] = p.id;
                    } else if (barangLower.includes('kontainer') && barangLower.includes('40') && barangLower.includes('empty')) {
                        pricelistIds['40_empty'] = p.id;
                    }
                });
                
                // Add 20' FULL if count > 0
                if (data.counts['20'] && data.counts['20'].full > 0 && pricelistIds['20_full']) {
                    addBarangToSectionWithValue(sectionIndex, pricelistIds['20_full'], data.counts['20'].full);
                    barangAdded = true;
                }
                
                // Add 20' EMPTY if count > 0
                if (data.counts['20'] && data.counts['20'].empty > 0 && pricelistIds['20_empty']) {
                    addBarangToSectionWithValue(sectionIndex, pricelistIds['20_empty'], data.counts['20'].empty);
                    barangAdded = true;
                }
                
                // Add 40' FULL if count > 0
                if (data.counts['40'] && data.counts['40'].full > 0 && pricelistIds['40_full']) {
                    addBarangToSectionWithValue(sectionIndex, pricelistIds['40_full'], data.counts['40'].full);
                    barangAdded = true;
                }
                
                // Add 40' EMPTY if count > 0
                if (data.counts['40'] && data.counts['40'].empty > 0 && pricelistIds['40_empty']) {
                    addBarangToSectionWithValue(sectionIndex, pricelistIds['40_empty'], data.counts['40'].empty);
                    barangAdded = true;
                }
                
                // If no containers found, add empty barang input
                if (!barangAdded) {
                    addBarangToSection(sectionIndex);
                }
                
                // Recalculate total
                calculateTotalFromAllSections();
            } else {
                // Fallback to empty input
                container.innerHTML = '';
                addBarangToSection(sectionIndex);
            }
        })
        .catch(error => {
            console.error('Error fetching container counts:', error);
            container.innerHTML = '';
            addBarangToSection(sectionIndex);
        });
    }
    
    // Add barang to section with pre-filled values
    window.addBarangToSectionWithValue = function(sectionIndex, barangId, jumlah) {
        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.barang-container-section');
        const barangIndex = container.children.length;
        
        let barangOptions = '<option value="">Pilih Nama Barang</option>';
        pricelistBuruhData.forEach(pricelist => {
            const selected = pricelist.id == barangId ? 'selected' : '';
            barangOptions += `<option value="${pricelist.id}" data-tarif="${pricelist.tarif}" ${selected}>${pricelist.barang}</option>`;
        });
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2';
        inputGroup.innerHTML = `
            <div class="flex-1">
                <select name="kapal_sections[${sectionIndex}][barang][${barangIndex}][barang_id]" class="barang-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" required>
                    ${barangOptions}
                </select>
            </div>
            <div class="w-24">
                <input type="number" step="any" name="kapal_sections[${sectionIndex}][barang][${barangIndex}][jumlah]" value="${jumlah}" class="jumlah-input-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" placeholder="0" required>
            </div>
            <button type="button" onclick="removeBarangFromSection(this)" class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition">
                <i class="fas fa-trash text-xs"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        // Add event listeners
        const barangSelect = inputGroup.querySelector('.barang-select-item');
        const jumlahInput = inputGroup.querySelector('.jumlah-input-item');
        
        barangSelect.addEventListener('change', function() {
            calculateTotalFromAllSections();
        });
        
        jumlahInput.addEventListener('input', function() {
            calculateTotalFromAllSections();
        });
    };
    
    window.removeKapalSection = function(sectionIndex) {
        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllSections();
        }
    };
    
    function loadVoyagesForSection(sectionIndex, kapalNama) {
        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.voyage-select');
        const voyageInput = section.querySelector('.voyage-input');
        const voyageManualBtn = section.querySelector('.voyage-manual-btn');
        
        if (!kapalNama) {
            voyageSelect.disabled = true;
            voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
            return;
        }
        
        // Only if currently in select mode
        if (!voyageSelect.classList.contains('hidden')) {
            voyageSelect.disabled = true;
            voyageSelect.innerHTML = '<option value="">Loading...</option>';
        }
        
        fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(kapalNama)}`)
            .then(response => response.json())
            .then(data => {
                console.log('Voyages response for', kapalNama, data);
                if (data.success && data.voyages) {
                    let html = '<option value="">-- Pilih Voyage --</option>';
                    data.voyages.forEach(voyage => {
                        html += `<option value="${voyage}">${voyage}</option>`;
                    });
                    voyageSelect.innerHTML = html;
                    
                    // Only enable if not in manual mode
                    if (voyageInput.classList.contains('hidden')) {
                        voyageSelect.disabled = false;
                    }
                } else {
                    voyageSelect.innerHTML = '<option value="">Tidak ada voyage tersedia</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching voyages:', error);
                if (!voyageSelect.classList.contains('hidden')) {
                   voyageSelect.innerHTML = '<option value="">Gagal memuat voyages</option>';
                }
            });
    }
    
    window.addBarangToSection = function(sectionIndex) {
        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.barang-container-section');
        const barangIndex = container.children.length;
        
        let barangOptions = '<option value="">Pilih Nama Barang</option>';
        pricelistBuruhData.forEach(pricelist => {
            barangOptions += `<option value="${pricelist.id}" data-tarif="${pricelist.tarif}">${pricelist.barang}</option>`;
        });
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2';
        inputGroup.innerHTML = `
            <div class="flex-1">
                <select name="kapal_sections[${sectionIndex}][barang][${barangIndex}][barang_id]" class="barang-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" required>
                    ${barangOptions}
                </select>
            </div>
            <div class="w-24">
                <input type="number" step="any" name="kapal_sections[${sectionIndex}][barang][${barangIndex}][jumlah]" class="jumlah-input-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" placeholder="0" required>
            </div>
            <button type="button" onclick="removeBarangFromSection(this)" class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition">
                <i class="fas fa-trash text-xs"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        // Add event listeners
        const barangSelect = inputGroup.querySelector('.barang-select-item');
        const jumlahInput = inputGroup.querySelector('.jumlah-input-item');
        
        barangSelect.addEventListener('change', function() {
            calculateTotalFromAllSections();
        });
        
        jumlahInput.addEventListener('input', function() {
            calculateTotalFromAllSections();
        });
    };
    
    window.removeBarangFromSection = function(button) {
        const container = button.closest('.barang-container-section');
        if (container.children.length > 1) {
            button.closest('.flex').remove();
            calculateTotalFromAllSections();
        }
    };
    
    function calculateTotalFromAllSections() {
        let grandTotal = 0;
        
        document.querySelectorAll('.kapal-section').forEach(section => {
            const barangSelects = section.querySelectorAll('.barang-select-item');
            const jumlahInputs = section.querySelectorAll('.jumlah-input-item');
            
            barangSelects.forEach((select, index) => {
                const selectedOption = select.options[select.selectedIndex];
                const tarif = parseFloat(selectedOption.getAttribute('data-tarif')) || 0;
                // Convert comma to period for proper decimal parsing (Indonesian format)
                const jumlahRaw = jumlahInputs[index].value.replace(',', '.');
                const jumlah = parseFloat(jumlahRaw) || 0;
                grandTotal += tarif * jumlah;
            });
        });
        
        if (grandTotal > 0) {
            nominalInput.value = Math.round(grandTotal).toLocaleString('id-ID');
            // Recalculate sisa pembayaran after nominal changes
            calculateSisaPembayaran();
        } else {
            nominalInput.value = '';
        }
    }

    // ============= TKBM SECTIONS MANAGEMENT =============
    var tkbmSectionCounter = 0;
    var tkbmSectionsContainer = document.getElementById('tkbm_sections_container');
    var addTkbmSectionBtn = document.getElementById('add_tkbm_section_btn');
    var addTkbmSectionBottomBtn = document.getElementById('add_tkbm_section_bottom_btn');
    
    function initializeTkbmSections() {
        if (!tkbmSectionsContainer) return;
        tkbmSectionsContainer.innerHTML = '';
        tkbmSectionCounter = 0;
        addTkbmSection();
    }
    
    function clearAllTkbmSections() {
        if (!tkbmSectionsContainer) return;
        tkbmSectionsContainer.innerHTML = '';
        tkbmSectionCounter = 0;
    }
    
    if (addTkbmSectionBtn) {
        addTkbmSectionBtn.addEventListener('click', function() {
            addTkbmSection();
        });
    }

    if (addTkbmSectionBottomBtn) {
        addTkbmSectionBottomBtn.addEventListener('click', function() {
            addTkbmSection();
        });
    }
    
    function addTkbmSection() {
        if (!tkbmSectionsContainer) return;
        tkbmSectionCounter++;
        const sectionIndex = tkbmSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'tkbm-section mb-6 p-4 border-2 border-amber-200 rounded-lg bg-amber-50';
        section.setAttribute('data-tkbm-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex}</h3>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeTkbmSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="tkbm_sections[${sectionIndex}][kapal]" class="tkbm-kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-amber-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="tkbm_sections[${sectionIndex}][voyage]" class="tkbm-voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-amber-500" required disabled>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="tkbm_sections[${sectionIndex}][voyage]" class="tkbm-voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-amber-500 hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="tkbm-voyage-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Referensi</label>
                    <input type="text" name="tkbm_sections[${sectionIndex}][no_referensi]" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-amber-500" placeholder="Masukkan No. Referensi">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Invoice Vendor</label>
                    <input type="date" name="tkbm_sections[${sectionIndex}][tanggal_invoice_vendor]" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Total Biaya Per Kapal</label>
                    <input type="text" class="tkbm-section-total w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 font-semibold text-gray-700" value="Rp 0" readonly>
                    <input type="hidden" name="tkbm_sections[${sectionIndex}][total_nominal]" class="tkbm-section-total-hidden" value="0">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">PPH (2%)</label>
                    <input type="text" class="tkbm-section-pph w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700" value="Rp 0" readonly>
                    <input type="hidden" name="tkbm_sections[${sectionIndex}][pph]" class="tkbm-section-pph-hidden" value="0">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Adjustment</label>
                    <input type="number" name="tkbm_sections[${sectionIndex}][adjustment]" class="tkbm-adjustment-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500" value="0" step="0.01">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Grand Total</label>
                    <input type="text" class="tkbm-grand-total-display w-full px-3 py-2 border border-gray-300 rounded-lg bg-emerald-50 font-semibold cursor-not-allowed" value="Rp 0" readonly>
                    <input type="hidden" name="tkbm_sections[${sectionIndex}][grand_total]" class="tkbm-grand-total-value" value="0">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="block text-xs font-medium text-gray-700 mb-2">Detail Barang TKBM</label>
                <div class="tkbm-barang-container" data-tkbm-section="${sectionIndex}"></div>
                <button type="button" onclick="addBarangToTkbmSection(${sectionIndex})" class="mt-2 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs rounded-lg transition">
                    <i class="fas fa-plus mr-1"></i> Tambah Barang
                </button>
            </div>
        `;
        
        tkbmSectionsContainer.appendChild(section);
        
        // Setup kapal change listener
        const kapalSelect = section.querySelector('.tkbm-kapal-select');
        kapalSelect.addEventListener('change', function() {
            loadVoyagesForTkbmSection(sectionIndex, this.value);
        });

        // Setup adjustment listener
        const adjustmentInput = section.querySelector('.tkbm-adjustment-input');
        adjustmentInput.addEventListener('input', function() {
            calculateTotalFromAllTkbmSections();
        });

        // Setup manual voyage toggle
        const voyageSelect = section.querySelector('.tkbm-voyage-select');
        const voyageInput = section.querySelector('.tkbm-voyage-input');
        const voyageManualBtn = section.querySelector('.tkbm-voyage-manual-btn');

        voyageManualBtn.addEventListener('click', function() {
            if (voyageInput.classList.contains('hidden')) {
                // Switch to manual input
                voyageSelect.classList.add('hidden');
                voyageSelect.disabled = true;
                
                voyageInput.classList.remove('hidden');
                voyageInput.disabled = false;
                voyageInput.focus();
                
                this.classList.remove('bg-gray-200', 'text-gray-600');
                this.classList.add('bg-blue-200', 'text-blue-700');
                this.innerHTML = '<i class="fas fa-list"></i>';
            } else {
                // Switch to select list
                voyageInput.classList.add('hidden');
                voyageInput.disabled = true;
                
                voyageSelect.classList.remove('hidden');
                voyageSelect.disabled = false;
                
                this.classList.add('bg-gray-200', 'text-gray-600');
                this.classList.remove('bg-blue-200', 'text-blue-700');
                this.innerHTML = '<i class="fas fa-keyboard"></i>';
            }
        });
        
        // Add first barang input
        addBarangToTkbmSection(sectionIndex);
    }
    
    window.removeTkbmSection = function(sectionIndex) {
        const section = document.querySelector(`[data-tkbm-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllTkbmSections();
        }
    };
    
    function loadVoyagesForTkbmSection(sectionIndex, kapalNama) {
        const section = document.querySelector(`[data-tkbm-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.tkbm-voyage-select');
        
        if (!kapalNama) {
            voyageSelect.disabled = true;
            voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
            return;
        }
        
        voyageSelect.disabled = true;
        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        
        fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(kapalNama)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.voyages) {
                    let html = '<option value="">-- Pilih Voyage --</option>';
                    data.voyages.forEach(voyage => {
                        html += `<option value="${voyage}">${voyage}</option>`;
                    });
                    voyageSelect.innerHTML = html;
                    voyageSelect.disabled = false;
                } else {
                    voyageSelect.innerHTML = '<option value="">Tidak ada voyage tersedia</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching voyages for TKBM:', error);
                voyageSelect.innerHTML = '<option value="">Gagal memuat voyages</option>';
            });
    }
    
    window.addBarangToTkbmSection = function(sectionIndex) {
        const section = document.querySelector(`[data-tkbm-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.tkbm-barang-container');
        const barangIndex = container.children.length;
        
        // Use TKBM pricelist data instead of Buruh pricelist data
        let barangOptions = '<option value="">Pilih Nama Barang</option>';
        pricelistTkbmData.forEach(pricelist => {
            barangOptions += `<option value="${pricelist.id}" data-tarif="${pricelist.tarif}">${pricelist.nama_barang}</option>`;
        });
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2';
        inputGroup.innerHTML = `
            <div class="flex-1">
                <select name="tkbm_sections[${sectionIndex}][barang][${barangIndex}][barang_id]" class="tkbm-barang-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-amber-500" required>
                    ${barangOptions}
                </select>
            </div>
            <div class="w-24">
                <input type="number" step="any" name="tkbm_sections[${sectionIndex}][barang][${barangIndex}][jumlah]" class="tkbm-jumlah-input-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-amber-500" placeholder="0" required>
            </div>
            <button type="button" onclick="removeBarangFromTkbmSection(this)" class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition">
                <i class="fas fa-trash text-xs"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        // Add event listeners
        const barangSelect = inputGroup.querySelector('.tkbm-barang-select-item');
        const jumlahInput = inputGroup.querySelector('.tkbm-jumlah-input-item');
        
        barangSelect.addEventListener('change', function() {
            calculateTotalFromAllTkbmSections();
        });
        
        jumlahInput.addEventListener('input', function() {
            calculateTotalFromAllTkbmSections();
        });
    };
    
    window.removeBarangFromTkbmSection = function(button) {
        const container = button.closest('.tkbm-barang-container');
        if (container.children.length > 1) {
            button.closest('.flex').remove();
            calculateTotalFromAllTkbmSections();
        }
    };
    
    function calculateTotalFromAllTkbmSections() {
        let grandTotal = 0;
        
        document.querySelectorAll('.tkbm-section').forEach(section => {
            let sectionTotal = 0;
            const barangSelects = section.querySelectorAll('.tkbm-barang-select-item');
            const jumlahInputs = section.querySelectorAll('.tkbm-jumlah-input-item');
            
            barangSelects.forEach((select, index) => {
                const selectedOption = select.options[select.selectedIndex];
                const tarif = parseFloat(selectedOption.getAttribute('data-tarif')) || 0;
                // Convert comma to period for proper decimal parsing (Indonesian format)
                const jumlahRaw = jumlahInputs[index].value.replace(',', '.');
                const jumlah = parseFloat(jumlahRaw) || 0;
                sectionTotal += tarif * jumlah;
            });
            
            // Update section total display
            const sectionTotalInput = section.querySelector('.tkbm-section-total');
            const sectionTotalHidden = section.querySelector('.tkbm-section-total-hidden');
            const sectionPphInput = section.querySelector('.tkbm-section-pph');
            const sectionPphHidden = section.querySelector('.tkbm-section-pph-hidden');
            const adjustmentInput = section.querySelector('.tkbm-adjustment-input');
            const sectionGrandTotalInput = section.querySelector('.tkbm-grand-total-display');
            const sectionGrandTotalHidden = section.querySelector('.tkbm-grand-total-value');
            
            // Calculate PPH and Grand Total
            const adjustment = parseFloat(adjustmentInput.value) || 0;
            const adjustedTotal = sectionTotal + adjustment;
            const pph = Math.round(adjustedTotal * 0.02);
            const grandTotalSection = adjustedTotal - pph;
            
            if (sectionTotalInput) sectionTotalInput.value = 'Rp ' + Math.round(adjustedTotal).toLocaleString('id-ID');
            if (sectionTotalHidden) sectionTotalHidden.value = Math.round(adjustedTotal);
            
            if (sectionPphInput) sectionPphInput.value = 'Rp ' + Math.round(pph).toLocaleString('id-ID');
            if (sectionPphHidden) sectionPphHidden.value = Math.round(pph);
            
            if (sectionGrandTotalInput) sectionGrandTotalInput.value = 'Rp ' + Math.round(grandTotalSection).toLocaleString('id-ID');
            if (sectionGrandTotalHidden) sectionGrandTotalHidden.value = Math.round(grandTotalSection);
            
            grandTotal += grandTotalSection;
        });
        
        if (grandTotal > 0) {
            nominalInput.value = Math.round(grandTotal).toLocaleString('id-ID');
            // Recalculate sisa pembayaran after nominal changes
            calculateSisaPembayaran();
        } else {
            nominalInput.value = '';
        }
    }

    // Barang management functions (OLD - keep for backward compatibility)
    function initializeBarangInputs() {
        const container = document.getElementById('barang_container');
        container.innerHTML = '';
        addBarangInput();
    }

    function clearBarangInputs() {
        const container = document.getElementById('barang_container');
        if (container) container.innerHTML = '';
    }

    function addBarangInput(existingBarangId = '', existingJumlah = '') {
        const container = document.getElementById('barang_container');
        const index = container.children.length;
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-3 p-3 bg-gray-50 rounded-md mb-2';
        
        // Build options from pricelist buruh data
        let barangOptions = '<option value="">Pilih Nama Barang</option>';
        pricelistBuruhData.forEach(pricelist => {
            const selected = existingBarangId == pricelist.id ? 'selected' : '';
            barangOptions += `<option value="${pricelist.id}" data-tarif="${pricelist.tarif}" ${selected}>${pricelist.barang}</option>`;
        });
        
        inputGroup.innerHTML = `
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Barang</label>
                <select name="barang[${index}][barang_id]" class="barang-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    ${barangOptions}
                </select>
            </div>
            <div class="w-32">
                <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah</label>
                <input type="number" name="barang[${index}][jumlah]" value="${existingJumlah}" min="0" step="0.01" class="jumlah-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0" required>
            </div>
            <button type="button" onclick="removeBarangInput(this)" class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                <i class="fas fa-trash"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        // Add event listeners for auto-calculation
        const barangSelect = inputGroup.querySelector('.barang-select');
        const jumlahInput = inputGroup.querySelector('.jumlah-input');
        
        barangSelect.addEventListener('change', function() {
            calculateTotalFromBarang();
        });
        
        jumlahInput.addEventListener('input', function() {
            calculateTotalFromBarang();
        });
    }

    window.removeBarangInput = function(button) {
        const container = document.getElementById('barang_container');
        if (container.children.length > 1) {
            button.closest('.flex').remove();
            calculateTotalFromBarang();
        }
    };

    function calculateTotalFromBarang() {
        const container = document.getElementById('barang_container');
        const barangSelects = container.querySelectorAll('.barang-select');
        const jumlahInputs = container.querySelectorAll('.jumlah-input');
        let total = 0;
        
        barangSelects.forEach((select, index) => {
            const selectedOption = select.options[select.selectedIndex];
            const tarif = parseFloat(selectedOption.getAttribute('data-tarif')) || 0;
            const jumlah = parseFloat(jumlahInputs[index].value) || 0;
            total += tarif * jumlah;
        });
        
        if (total > 0) {
            nominalInput.value = Math.round(total).toLocaleString('id-ID');
        }
    }

    // Add button for barang
    if (addBarangBtn) {
        addBarangBtn.addEventListener('click', function() {
            addBarangInput();
        });
    }

    // ============= AIR SECTIONS MANAGEMENT =============
    var airSectionCounter = 0;
    var airSectionsContainer = document.getElementById('air_sections_container');
    var addAirSectionBtn = document.getElementById('add_air_section_btn');
    
    function initializeAirSections() {
        airSectionsContainer.innerHTML = '';
        airSectionCounter = 0;
        addAirSection();
    }
    
    function clearAllAirSections() {
        if (airSectionsContainer) airSectionsContainer.innerHTML = '';
        airSectionCounter = 0;
        if (nominalInput) nominalInput.value = '';
        if (jasaAirInput) jasaAirInput.value = '0';
        if (pphAirInput) pphAirInput.value = '0';
        if (grandTotalAirInput) grandTotalAirInput.value = '0';
    }
    
    addAirSectionBtn.addEventListener('click', function() {
        addAirSection();
    });
    
    function addAirSection() {
        airSectionCounter++;
        const sectionIndex = airSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'air-section mb-6 p-4 border-2 border-cyan-200 rounded-lg bg-cyan-50';
        section.setAttribute('data-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });
        
        // Get unique vendor names
        let vendorOptions = '<option value="">-- Pilih Vendor Air Tawar --</option>';
        const uniqueVendors = [...new Set(pricelistAirTawarData.map(item => item.nama_agen))];
        uniqueVendors.forEach(vendorName => {
            vendorOptions += `<option value="${vendorName}">${vendorName}</option>`;
        });

        // Get unique lokasi from pricelist data
        let lokasiOptions = '<option value="">-- Pilih Lokasi --</option>';
        const uniqueLokasis = [...new Set(pricelistAirTawarData.map(item => item.lokasi))];
        uniqueLokasis.forEach(loc => {
            lokasiOptions += `<option value="${loc}">${loc}</option>`;
        });

        // Get Penerima options
        let penerimaOptions = '<option value="">-- Pilih Penerima --</option>';
        @foreach($karyawans as $karyawan)
            penerimaOptions += `<option value="{{ $karyawan->nama_lengkap }}">{{ $karyawan->nama_lengkap }}</option>`;
        @endforeach
        
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-md font-semibold text-cyan-800">
                    <i class="fas fa-ship mr-2"></i>Kapal ${sectionIndex}
                </h4>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeAirSection(${sectionIndex})" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs rounded transition"><i class="fas fa-times mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kapal</label>
                    <select name="air[${sectionIndex}][kapal]" class="kapal-select-air w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Voyage</label>
                    <div class="flex gap-2">
                        <select name="air[${sectionIndex}][voyage]" class="voyage-select-air w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500" disabled required>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="air[${sectionIndex}][voyage]" class="voyage-input-air w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="voyage-manual-btn-air px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vendor Air Tawar</label>
                    <select name="air[${sectionIndex}][vendor]" class="vendor-select-air w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500" required>
                        ${vendorOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="air[${sectionIndex}][type]" class="type-select-air w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500" disabled required>
                        <option value="">-- Pilih Vendor Terlebih Dahulu --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <select name="air[${sectionIndex}][lokasi]" class="lokasi-select-air w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500">
                        ${lokasiOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kuantitas (Ton)</label>
                    <input type="number" name="air[${sectionIndex}][kuantitas]" step="0.01" min="0" class="kuantitas-input-air w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500" placeholder="0.00" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jasa Air (Input)</label>
                    <input type="number" name="air[${sectionIndex}][jasa_air]" class="jasa-air-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500" value="0" placeholder="0">
                </div>
                <!-- Tanggal -->
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" value="{{ old('tanggal', $biayaKapal->tanggal->format('Y-m-d')) }}" required>
                </div>

                <!-- Nomor Invoice -->
                <div>
                    <label for="nomor_invoice" class="block text-sm font-medium text-gray-700 mb-1">Nomor Invoice</label>
                    <div class="relative">
                        <input type="text" name="nomor_invoice" id="nomor_invoice_display" class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed" value="{{ $biayaKapal->nomor_invoice }}" readonly>
                        <input type="hidden" name="nomor_invoice_value" id="nomor_invoice_value" value="{{ $biayaKapal->nomor_invoice }}">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Nomor invoice tidak dapat diubah (Auto-generated)</p>
                </div>

                <!-- Referensi (Optional) -->
                <div class="md:col-span-2">
                    <label for="nomor_referensi" class="block text-sm font-medium text-gray-700 mb-1">Nomor Referensi (Opsional)</label>
                    <input type="text" name="nomor_referensi" id="nomor_referensi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" placeholder="Contoh: REF-001" value="{{ old('nomor_referensi', $biayaKapal->nomor_referensi) }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Agen</label>
                    <input type="number" name="air[${sectionIndex}][biaya_agen]" class="biaya-agen-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500" value="6500000" placeholder="6500000">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sub Total</label>
                    <input type="text" class="sub-total-display w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" value="Rp 0" readonly>
                    <input type="hidden" name="air[${sectionIndex}][sub_total]" class="sub-total-value" value="0">
                    <input type="hidden" name="air[${sectionIndex}][harga]" class="harga-hidden" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PPH (2%)</label>
                    <input type="text" class="pph-display w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" value="Rp 0" readonly>
                    <input type="hidden" name="air[${sectionIndex}][pph]" class="pph-value" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grand Total</label>
                    <input type="text" class="grand-total-display w-full px-3 py-2 border border-gray-300 rounded-lg bg-emerald-50 font-semibold cursor-not-allowed" value="Rp 0" readonly>
                    <input type="hidden" name="air[${sectionIndex}][grand_total]" class="grand-total-value" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penerima</label>
                    <input type="text" name="air[${sectionIndex}][penerima]" class="penerima-input-air w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500" placeholder="Masukkan nama penerima">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                    <input type="text" name="air[${sectionIndex}][nomor_rekening]" class="nomor-rekening-input-air w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500" placeholder="Masukkan nomor rekening">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Referensi</label>
                    <input type="text" name="air[${sectionIndex}][nomor_referensi]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500" placeholder="Masukkan No. Referensi">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Invoice Vendor</label>
                    <input type="date" name="air[${sectionIndex}][tanggal_invoice_vendor]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500">
                </div>
            </div>
        `;
        
        airSectionsContainer.appendChild(section);
        
        // Setup kapal change listener
        const kapalSelect = section.querySelector('.kapal-select-air');
        kapalSelect.addEventListener('change', function() {
            loadVoyagesForAirSection(sectionIndex, this.value);
        });
        
        // Setup vendor change listener to load types
        const vendorSelect = section.querySelector('.vendor-select-air');
        vendorSelect.addEventListener('change', function() {
            loadTypesForVendor(sectionIndex, this.value);
            // Check for Abqori on vendor change
            const jasaAirInput = section.querySelector('.jasa-air-input');
            if (this.value && this.value.toLowerCase().includes('abqori')) {
                jasaAirInput.value = 100000;
            } else {
                jasaAirInput.value = 0;
            }
            calculateAirSectionTotal(sectionIndex);
        });
        
        // Setup type change listener for auto-calculation
        const typeSelect = section.querySelector('.type-select-air');
        typeSelect.addEventListener('change', function() {
            calculateAirSectionTotal(sectionIndex);
        });
        
        // Setup kuantitas change listener
        const kuantitasInput = section.querySelector('.kuantitas-input-air');
        kuantitasInput.addEventListener('input', function() {
            calculateAirSectionTotal(sectionIndex);
        });

        // Set default lokasi if available
        const lokasiSelect = section.querySelector('.lokasi-select-air');
        if (lokasiSelect) {
            if (lokasiSelect.querySelector('option[value="Jakarta"]')) {
                lokasiSelect.value = 'Jakarta';
            } else if (lokasiSelect.options.length > 0) {
                lokasiSelect.selectedIndex = 0;
            }

            // When lokasi changes, update vendor list for this section
            lokasiSelect.addEventListener('change', function() {
                updateVendorsForLokasi(sectionIndex, this.value);
            });

            // Initialize vendor list based on default lokasi
            updateVendorsForLokasi(sectionIndex, lokasiSelect.value);
        }

        // Setup jasa air input change listener
        const jasaAirInput = section.querySelector('.jasa-air-input');
        jasaAirInput.addEventListener('input', function() {
            calculateAirSectionTotal(sectionIndex);
        });
        
        // Setup biaya agen input change listener
        const biayaAgenInput = section.querySelector('.biaya-agen-input');
        biayaAgenInput.addEventListener('input', function() {
            calculateAirSectionTotal(sectionIndex);
        });

        // Setup manual voyage toggle
        const voyageSelect = section.querySelector('.voyage-select-air');
        const voyageInput = section.querySelector('.voyage-input-air');
        const voyageManualBtn = section.querySelector('.voyage-manual-btn-air');

        voyageManualBtn.addEventListener('click', function() {
            if (voyageInput.classList.contains('hidden')) {
                // Switch to manual input
                voyageSelect.classList.add('hidden');
                voyageSelect.disabled = true;
                
                voyageInput.classList.remove('hidden');
                voyageInput.disabled = false;
                voyageInput.focus();
                
                this.classList.remove('bg-gray-200', 'text-gray-600');
                this.classList.add('bg-cyan-200', 'text-cyan-700');
                this.innerHTML = '<i class="fas fa-list"></i>';
            } else {
                // Switch to select list
                voyageInput.classList.add('hidden');
                voyageInput.disabled = true;
                
                voyageSelect.classList.remove('hidden');
                
                // Only enable select if kapal is selected
                const kapalSelect = section.querySelector('.kapal-select-air');
                if (kapalSelect && kapalSelect.value) {
                    voyageSelect.disabled = false;
                } else {
                    voyageSelect.disabled = true;
                }
                
                this.classList.add('bg-gray-200', 'text-gray-600');
                this.classList.remove('bg-cyan-200', 'text-cyan-700');
                this.innerHTML = '<i class="fas fa-keyboard"></i>';
            }
        });
    }
    
    window.removeAirSection = function(sectionIndex) {
        const section = document.querySelector(`.air-section[data-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllAirSections();
        }
    };
    
    function loadVoyagesForAirSection(sectionIndex, kapalNama) {
        const section = document.querySelector(`.air-section[data-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.voyage-select-air');
        
        if (!kapalNama) {
            voyageSelect.disabled = true;
            voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
            return;
        }
        
        voyageSelect.disabled = true;
        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        
        fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(kapalNama)}`)
            .then(response => response.json())
            .then(data => {
                console.log('Voyages response for', kapalNama, data);
                voyageSelect.disabled = false;
                
                let options = '<option value="">-- Pilih Voyage --</option>';
                // Tambahkan DOCK sesuai permintaan user
                options += '<option value="DOCK">DOCK</option>';
                
                if (data && data.success && data.voyages && data.voyages.length > 0) {
                    data.voyages.forEach(voyage => {
                        options += `<option value="${voyage}">${voyage}</option>`;
                    });
                } else if (!data || !data.voyages || data.voyages.length === 0) {
                    // Jika tidak ada voyage, tetap tampilkan DOCK
                }
                
                voyageSelect.innerHTML = options;
            })
            .catch(error => {
                console.error('Error loading voyages:', error);
                voyageSelect.disabled = false;
                // Tetap izinkan pilihan DOCK meskipun fetch gagal
                voyageSelect.innerHTML = '<option value="">-- Pilih Voyage --</option><option value="DOCK">DOCK</option>';
            });
    }
    
    function loadTypesForVendor(sectionIndex, vendorName) {
        const section = document.querySelector(`.air-section[data-section-index="${sectionIndex}"]`);
        const typeSelect = section.querySelector('.type-select-air');
        
        if (!vendorName) {
            typeSelect.disabled = true;
            typeSelect.innerHTML = '<option value="">-- Pilih Vendor Terlebih Dahulu --</option>';
            return;
        }
        
        // Get selected lokasi for this section
        const lokasiSelect = section.querySelector('.lokasi-select-air');
        const selectedLokasi = lokasiSelect ? (lokasiSelect.value || '') : '';

        // Filter pricelist data by vendor name and lokasi (if selected)
        const vendorTypes = pricelistAirTawarData.filter(item => item.nama_agen === vendorName && (selectedLokasi === '' || item.lokasi === selectedLokasi));
        
        if (vendorTypes.length > 0) {
            typeSelect.disabled = false;
            let options = '<option value="">-- Pilih Type --</option>';
            vendorTypes.forEach(type => {
                options += `<option value="${type.id}" data-keterangan="${type.keterangan}" data-harga="${type.harga}">${type.keterangan} - Rp ${parseInt(type.harga).toLocaleString('id-ID')}/ton</option>`;
            });
            typeSelect.innerHTML = options;
        } else {
            typeSelect.disabled = true;
            typeSelect.innerHTML = '<option value="">Tidak ada type tersedia</option>';
        }
    }

    // Update vendor list for a given lokasi in a section
    function updateVendorsForLokasi(sectionIndex, lokasi) {
        const section = document.querySelector(`.air-section[data-section-index="${sectionIndex}"]`);
        if (!section) return;
        const vendorSelect = section.querySelector('.vendor-select-air');
        const typeSelect = section.querySelector('.type-select-air');

        // Build vendor list filtered by lokasi; if lokasi is empty, include all vendors
        let vendors = [];
        if (lokasi && lokasi !== '') {
            vendors = [...new Set(pricelistAirTawarData.filter(item => (item.lokasi || '') === lokasi).map(i => i.nama_agen))];
        } else {
            vendors = [...new Set(pricelistAirTawarData.map(i => i.nama_agen))];
        }

        if (vendors.length > 0) {
            vendorSelect.disabled = false;
            let options = '<option value="">-- Pilih Vendor Air Tawar --</option>';
            vendors.forEach(v => options += `<option value="${v}">${v}</option>`);
            vendorSelect.innerHTML = options;
        } else {
            vendorSelect.disabled = true;
            vendorSelect.innerHTML = '<option value="">-- Tidak ada vendor di lokasi ini --</option>';
        }

        // Clear type options whenever vendor list changes
        if (typeSelect) {
            typeSelect.disabled = true;
            typeSelect.innerHTML = '<option value="">-- Pilih Vendor Terlebih Dahulu --</option>';
        }
    }
    
    function calculateAirSectionTotal(sectionIndex) {
        const section = document.querySelector(`.air-section[data-section-index="${sectionIndex}"]`);
        const typeSelect = section.querySelector('.type-select-air');
        const kuantitasInput = section.querySelector('.kuantitas-input-air');
        
        // Updated selectors
        const subTotalDisplay = section.querySelector('.sub-total-display');
        const subTotalValue = section.querySelector('.sub-total-value');
        const jasaAirInput = section.querySelector('.jasa-air-input');
        const biayaAgenInput = section.querySelector('.biaya-agen-input');
        
        const hargaHidden = section.querySelector('.harga-hidden');
        
        // New fields
        const pphDisplay = section.querySelector('.pph-display');
        const pphValue = section.querySelector('.pph-value');
        const grandTotalDisplay = section.querySelector('.grand-total-display');
        const grandTotalValue = section.querySelector('.grand-total-value');
        
        const selectedOption = typeSelect.options[typeSelect.selectedIndex];
        const hargaPerTon = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
        const kuantitas = parseFloat(kuantitasInput.value) || 0;
        
        let waterCost = Math.round(hargaPerTon * kuantitas);
        let jasaAir = parseFloat(jasaAirInput.value) || 0;
        let biayaAgen = parseFloat(biayaAgenInput.value) || 0;
        
        // Sub Total = (Price * Qty) + Jasa Air + Biaya Agen
        let subTotal = waterCost + jasaAir + biayaAgen;
        
        // PPH = (Jasa Air + Biaya Agen) * 2%
        const pph = Math.round((jasaAir + biayaAgen) * 0.02);
        const grandTotal = subTotal - pph;
        
        subTotalDisplay.value = subTotal > 0 ? `Rp ${subTotal.toLocaleString('id-ID')}` : 'Rp 0';
        subTotalValue.value = subTotal;
        hargaHidden.value = hargaPerTon;
        
        if (pphDisplay) pphDisplay.value = pph > 0 ? `Rp ${pph.toLocaleString('id-ID')}` : 'Rp 0';
        if (pphValue) pphValue.value = pph;
        
        if (grandTotalDisplay) grandTotalDisplay.value = grandTotal > 0 ? `Rp ${grandTotal.toLocaleString('id-ID')}` : 'Rp 0';
        if (grandTotalValue) grandTotalValue.value = grandTotal;
        
        // Recalculate total from all sections
        calculateTotalFromAllAirSections();
    }
    
    function calculateTotalFromAllAirSections() {
        let totalBase = 0;
        let totalPph = 0;
        let totalGrandTotal = 0;
        
        document.querySelectorAll('.air-section').forEach(section => {
            const subTotalValue = section.querySelector('.sub-total-value');
            // Jasa air is already included in subTotal
            const pphValue = section.querySelector('.pph-value');
            const grandTotalValue = section.querySelector('.grand-total-value');
            
            const subTotal = parseFloat(subTotalValue ? subTotalValue.value : 0) || 0;
            
            totalBase += subTotal;
            totalPph += parseFloat(pphValue ? pphValue.value : 0) || 0;
            totalGrandTotal += parseFloat(grandTotalValue ? grandTotalValue.value : 0) || 0;
        });
        
        // Set to Nominal field
        if (totalBase > 0) {
            nominalInput.value = totalBase.toLocaleString('id-ID');
        } else {
            nominalInput.value = '';
        }
        
        // Calculate Jasa Air / Total Base summary
        if (jasaAirInput) jasaAirInput.value = totalBase > 0 ? totalBase.toLocaleString('id-ID') : '0';
        
        // Calculate PPH total
        if (pphAirInput) pphAirInput.value = totalPph > 0 ? totalPph.toLocaleString('id-ID') : '0';
        
        // Calculate Grand Total
        if (grandTotalAirInput) grandTotalAirInput.value = totalGrandTotal > 0 ? totalGrandTotal.toLocaleString('id-ID') : '0';
    }

    // ============= PENERIMA SELECT2 INITIALIZATION =============
    // Load Select2 if jQuery is available
    if (typeof jQuery !== 'undefined') {
        // Check if Select2 is loaded
        function initPenerimaSelect2() {
            if (typeof jQuery.fn.select2 !== 'undefined') {
                jQuery('#penerima').select2({
                    placeholder: '-- Pilih atau ketik nama penerima --',
                    allowClear: true,
                    tags: true,
                    width: '100%'
                });
            } else {
                // Load Select2 CSS
                if (!document.querySelector('link[href*="select2"]')) {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css';
                    document.head.appendChild(link);
                }
                
                // Load Select2 JS
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
                script.onload = function() {
                    jQuery('#penerima').select2({
                        placeholder: '-- Pilih atau ketik nama penerima --',
                        allowClear: true,
                        tags: true,
                        width: '100%'
                    });
                };
                document.head.appendChild(script);
            }
        }
        
        // Initialize on DOM ready
        jQuery(document).ready(function() {
            initPenerimaSelect2();
        });
    }

    // ============= KAPAL MULTI-SELECT =============
    var kapalSearch = document.getElementById('kapal_search');
    var kapalDropdown = document.getElementById('kapal_dropdown');
    var selectedKapalChips = document.getElementById('selected_kapal_chips');
    var hiddenKapalInputs = document.getElementById('hidden_kapal_inputs');
    var kapalOptions = document.querySelectorAll('.kapal-option');
    var kapalSelectedCount = document.getElementById('kapalSelectedCount');
    var btnSelectAllKapal = document.getElementById('selectAllKapalBtn');
    var btnClearAllKapal = document.getElementById('clearAllKapalBtn');
    
    var selectedKapals = [];
    var oldKapalValue = @json(old('nama_kapal', $biayaKapal->nama_kapal ?? []));
    
    // Show kapal dropdown on focus
    kapalSearch.addEventListener('focus', function() {
        kapalDropdown.classList.remove('hidden');
        filterKapalOptions();
        
        // Show hint on first focus
        if (!localStorage.getItem('kapal_multiselect_hint_shown')) {
            const hint = document.createElement('div');
            hint.className = 'px-3 py-2 bg-green-50 border-b border-green-200 text-xs text-green-700 font-medium';
            hint.innerHTML = '<i class="fas fa-lightbulb mr-1"></i> Tip: Klik beberapa item untuk memilih lebih dari 1 kapal';
            kapalDropdown.insertBefore(hint, kapalDropdown.firstChild);
            localStorage.setItem('kapal_multiselect_hint_shown', 'true');
            
            setTimeout(() => {
                hint.style.transition = 'opacity 0.5s';
                hint.style.opacity = '0';
                setTimeout(() => hint.remove(), 500);
            }, 5000);
        }
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#kapal_container') && !e.target.closest('#kapal_dropdown')) {
            kapalDropdown.classList.add('hidden');
        }
        if (!e.target.closest('#voyage_container_input') && !e.target.closest('#voyage_dropdown')) {
            voyageDropdown.classList.add('hidden');
        }
    });
    
    // Search/filter kapal options
    kapalSearch.addEventListener('input', function() {
        filterKapalOptions();
    });
    
    function filterKapalOptions() {
        const searchTerm = kapalSearch.value.toLowerCase();
        kapalOptions.forEach(option => {
            const nama = option.getAttribute('data-nama').toLowerCase();
            const shouldShow = nama.includes(searchTerm);
            option.style.display = shouldShow ? 'block' : 'none';
        });
    }
    
    // Handle kapal option selection
    kapalOptions.forEach(option => {
        option.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            
            if (!selectedKapals.find(k => k.nama === nama)) {
                selectedKapals.push({ id, nama });
                addKapalChip(id, nama);
                updateKapalHiddenInputs();
                updateKapalSelectedCount();
                updateVoyages();
                this.classList.add('selected');
            } else {
                // If already selected, show visual feedback
                this.style.backgroundColor = '#fee2e2';
                setTimeout(() => {
                    this.style.backgroundColor = '';
                }, 300);
            }
            
            kapalSearch.value = '';
            // Don't hide dropdown to allow multiple selections
            // kapalDropdown.classList.add('hidden');
        });
    });
    
    function addKapalChip(id, nama) {
        const chip = document.createElement('span');
        chip.className = 'selected-chip';
        chip.setAttribute('data-nama', nama);
        chip.innerHTML = `
            <span class="font-medium">${nama}</span>
            <span class="remove-chip" onclick="removeKapalChip('${nama}')">&times;</span>
        `;
        selectedKapalChips.appendChild(chip);
    }
    
    window.removeKapalChip = function(nama) {
        selectedKapals = selectedKapals.filter(k => k.nama !== nama);
        const chip = document.querySelector(`[data-nama="${nama}"].selected-chip`);
        if (chip) chip.remove();
        
        const option = Array.from(kapalOptions).find(opt => opt.getAttribute('data-nama') === nama);
        if (option) option.classList.remove('selected');
        
        updateKapalHiddenInputs();
        updateKapalSelectedCount();
        updateVoyages();
    };
    
    if (btnSelectAllKapal) {
        btnSelectAllKapal.addEventListener('click', function() {
            kapalOptions.forEach(option => {
                const id = option.getAttribute('data-id');
                const nama = option.getAttribute('data-nama');
                
                if (!selectedKapals.find(k => k.nama === nama)) {
                    selectedKapals.push({ id, nama });
                    addKapalChip(id, nama);
                    option.classList.add('selected');
                }
            });
            
            updateKapalHiddenInputs();
            updateKapalSelectedCount();
            updateVoyages();
        });
    }
    
    if (btnClearAllKapal) {
        btnClearAllKapal.addEventListener('click', function() {
            selectedKapals = [];
            selectedKapalChips.innerHTML = '';
            hiddenKapalInputs.innerHTML = '';
            kapalOptions.forEach(option => option.classList.remove('selected'));
            updateKapalSelectedCount();
            updateVoyages();
        });
    }
    
    function updateKapalHiddenInputs() {
        hiddenKapalInputs.innerHTML = '';
        selectedKapals.forEach(kapal => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'nama_kapal[]';
            input.value = kapal.nama;
            hiddenKapalInputs.appendChild(input);
        });
    }
    
    function updateKapalSelectedCount() {
        kapalSelectedCount.textContent = `Terpilih: ${selectedKapals.length} dari ${kapalOptions.length} kapal`;
    }

    // ============= VOYAGE MULTI-SELECT =============
    var voyageSearch = document.getElementById('voyage_search');
    var voyageDropdown = document.getElementById('voyage_dropdown');
    var selectedVoyageChips = document.getElementById('selected_voyage_chips');
    var hiddenVoyageInputs = document.getElementById('hidden_voyage_inputs');
    var voyageSelectedCount = document.getElementById('voyageSelectedCount');
    var btnSelectAllVoyage = document.getElementById('selectAllVoyageBtn');
    var btnClearAllVoyage = document.getElementById('clearAllVoyageBtn');
    
    var selectedVoyages = [];
    var availableVoyages = [];
    var oldVoyageValue = @json(old('no_voyage', []));
    
    // Show voyage dropdown on focus
    voyageSearch.addEventListener('focus', function() {
        if (selectedKapals.length > 0) {
            voyageDropdown.classList.remove('hidden');
            filterVoyageOptions();
            
            // Show hint on first focus
            if (!localStorage.getItem('voyage_multiselect_hint_shown')) {
                setTimeout(() => {
                    const hint = document.createElement('div');
                    hint.className = 'px-3 py-2 bg-green-50 border-b border-green-200 text-xs text-green-700 font-medium';
                    hint.innerHTML = '<i class="fas fa-lightbulb mr-1"></i> Tip: Klik beberapa voyage untuk memilih lebih dari 1';
                    if (voyageDropdown.firstChild && !voyageDropdown.firstChild.textContent.includes('Memuat')) {
                        voyageDropdown.insertBefore(hint, voyageDropdown.firstChild);
                        localStorage.setItem('voyage_multiselect_hint_shown', 'true');
                        
                        setTimeout(() => {
                            hint.style.transition = 'opacity 0.5s';
                            hint.style.opacity = '0';
                            setTimeout(() => hint.remove(), 500);
                        }, 5000);
                    }
                }, 500);
            }
        }
    });
    
    // Search/filter voyage options
    voyageSearch.addEventListener('input', function() {
        filterVoyageOptions();
    });
    
    function filterVoyageOptions() {
        const searchTerm = voyageSearch.value.toLowerCase();
        const voyageOptions = voyageDropdown.querySelectorAll('.voyage-option');
        voyageOptions.forEach(option => {
            const voyage = option.getAttribute('data-voyage').toLowerCase();
            const shouldShow = voyage.includes(searchTerm);
            option.style.display = shouldShow ? 'block' : 'none';
        });
    }
    
    function addVoyageChip(voyage) {
        const chip = document.createElement('span');
        chip.className = 'selected-chip';
        chip.setAttribute('data-voyage', voyage);
        chip.innerHTML = `
            <span class="font-medium">${voyage}</span>
            <span class="remove-chip" onclick="removeVoyageChip('${voyage}')">&times;</span>
        `;
        selectedVoyageChips.appendChild(chip);
    }
    
    window.removeVoyageChip = function(voyage) {
        selectedVoyages = selectedVoyages.filter(v => v !== voyage);
        const chip = document.querySelector(`[data-voyage="${voyage}"].selected-chip`);
        if (chip) chip.remove();
        
        const option = voyageDropdown.querySelector(`[data-voyage="${voyage}"].voyage-option`);
        if (option) option.classList.remove('selected');
        
        updateVoyageHiddenInputs();
        updateVoyageSelectedCount();
        updateBls();
    };
    
    if (btnSelectAllVoyage) {
        btnSelectAllVoyage.addEventListener('click', function() {
            selectedVoyages = [...availableVoyages];
            selectedVoyageChips.innerHTML = '';
            availableVoyages.forEach(voyage => {
                addVoyageChip(voyage);
            });
            
            const voyageOptions = voyageDropdown.querySelectorAll('.voyage-option');
            voyageOptions.forEach(option => option.classList.add('selected'));
            
            updateVoyageHiddenInputs();
            updateVoyageSelectedCount();
            updateBls();
        });
    }
    
    if (btnClearAllVoyage) {
        btnClearAllVoyage.addEventListener('click', function() {
            selectedVoyages = [];
            selectedVoyageChips.innerHTML = '';
            hiddenVoyageInputs.innerHTML = '';
            
            const voyageOptions = voyageDropdown.querySelectorAll('.voyage-option');
            voyageOptions.forEach(option => option.classList.remove('selected'));
            
            updateBls();
            updateVoyageSelectedCount();
        });
    }
    
    function updateVoyageHiddenInputs() {
        hiddenVoyageInputs.innerHTML = '';
        selectedVoyages.forEach(voyage => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'no_voyage[]';
            input.value = voyage;
            hiddenVoyageInputs.appendChild(input);
        });
    }
    
    function updateVoyageSelectedCount() {
        voyageSelectedCount.textContent = `Terpilih: ${selectedVoyages.length} voyage`;
    }
    
    // Function to fetch and display voyages for selected ships
    function updateVoyages() {
        if (selectedKapals.length === 0) {
            voyageSearch.disabled = true;
            voyageSearch.placeholder = '--Pilih Kapal Terlebih Dahulu--';
            voyageDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Pilih kapal terlebih dahulu</p>';
            selectedVoyages = [];
            selectedVoyageChips.innerHTML = '';
            hiddenVoyageInputs.innerHTML = '';
            updateVoyageSelectedCount();
            return;
        }
        
        voyageSearch.disabled = false;
        voyageSearch.placeholder = '--Pilih Voyage--';
        voyageDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Memuat voyages...</p>';
        
        // Fetch voyages for all selected ships
        const fetchPromises = selectedKapals.map(kapal => 
            fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(kapal.nama)}`)
                .then(response => response.json())
        );
        
        Promise.all(fetchPromises)
            .then(results => {
                // Collect all voyages from all ships
                const allVoyages = new Set();
                results.forEach(data => {
                    if (data.success && data.voyages) {
                        data.voyages.forEach(voyage => allVoyages.add(voyage));
                    }
                });
                
                availableVoyages = Array.from(allVoyages).sort();
                
                if (availableVoyages.length > 0) {
                    // Create option list
                    let html = '';
                    availableVoyages.forEach(voyage => {
                        const isSelected = selectedVoyages.includes(voyage) ? 'selected' : '';
                        html += `
                            <div class="voyage-option px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 ${isSelected}"
                                 data-voyage="${voyage}">
                                <div class="font-medium text-gray-900">${voyage}</div>
                            </div>
                        `;
                    });
                    voyageDropdown.innerHTML = html;
                    
                    // Add click handlers to new options
                    const voyageOptions = voyageDropdown.querySelectorAll('.voyage-option');
                    voyageOptions.forEach(option => {
                        option.addEventListener('click', function() {
                            const voyage = this.getAttribute('data-voyage');
                            
                            if (!selectedVoyages.includes(voyage)) {
                                selectedVoyages.push(voyage);
                                addVoyageChip(voyage);
                                updateVoyageHiddenInputs();
                                updateVoyageSelectedCount();
                                updateBls();
                                this.classList.add('selected');
                            } else {
                                // If already selected, show visual feedback
                                this.style.backgroundColor = '#fee2e2';
                                setTimeout(() => {
                                    this.style.backgroundColor = '';
                                }, 300);
                            }
                            
                            voyageSearch.value = '';
                            // Don't hide dropdown to allow multiple selections
                            // voyageDropdown.classList.add('hidden');
                        });
                    });
                } else {
                    voyageDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Tidak ada voyage untuk kapal yang dipilih</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching voyages:', error);
                voyageDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-red-600">Gagal memuat voyages. Silakan coba lagi.</p>';
            });
    }
    
    // ============= BL MULTI-SELECT =============
    var blSearch = document.getElementById('bl_search');
    var blDropdown = document.getElementById('bl_dropdown');
    var selectedBlChips = document.getElementById('selected_bl_chips');
    var hiddenBlInputs = document.getElementById('hidden_bl_inputs');
    var blSelectedCount = document.getElementById('blSelectedCount');
    var btnSelectAllBl = document.getElementById('selectAllBlBtn');
    var btnClearAllBl = document.getElementById('clearAllBlBtn');
    
    var selectedBls = {}; // Changed to object to store {id: {kontainer, seal}}
    var availableBls = {}; // Changed to object to store {id: {kontainer, seal}}
    var oldBlValue = @json(old('no_bl', []));
    
    // Show BL dropdown on focus
    blSearch.addEventListener('focus', function() {
        if (selectedVoyages.length > 0) {
            blDropdown.classList.remove('hidden');
            filterBlOptions();
            
            // Show hint on first focus
            if (!localStorage.getItem('bl_multiselect_hint_shown')) {
                setTimeout(() => {
                    const hint = document.createElement('div');
                    hint.className = 'px-3 py-2 bg-green-50 border-b border-green-200 text-xs text-green-700 font-medium';
                    hint.innerHTML = '<i class="fas fa-lightbulb mr-1"></i> Tip: Klik beberapa kontainer untuk memilih lebih dari 1';
                    if (blDropdown.firstChild && !blDropdown.firstChild.textContent.includes('Memuat')) {
                        blDropdown.insertBefore(hint, blDropdown.firstChild);
                        localStorage.setItem('bl_multiselect_hint_shown', 'true');
                        
                        setTimeout(() => {
                            hint.style.transition = 'opacity 0.5s';
                            hint.style.opacity = '0';
                            setTimeout(() => hint.remove(), 500);
                        }, 5000);
                    }
                }, 500);
            }
        }
    });
    
    // Hide BL dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#bl_container_input') && !e.target.closest('#bl_dropdown')) {
            blDropdown.classList.add('hidden');
        }
    });
    
    // Search/filter BL options
    blSearch.addEventListener('input', function() {
        filterBlOptions();
    });
    
    function filterBlOptions() {
        const searchTerm = blSearch.value.toLowerCase();
        const blOptions = blDropdown.querySelectorAll('.bl-option');
        blOptions.forEach(option => {
            const kontainer = option.getAttribute('data-kontainer').toLowerCase();
            const seal = option.getAttribute('data-seal').toLowerCase();
            const shouldShow = kontainer.includes(searchTerm) || seal.includes(searchTerm);
            option.style.display = shouldShow ? 'block' : 'none';
        });
    }
    
    function addBlChip(blId, kontainer, seal) {
        const chip = document.createElement('span');
        chip.className = 'selected-chip';
        chip.setAttribute('data-bl', blId);
        chip.innerHTML = `
            <div class="flex flex-col">
                <span class="font-medium">${kontainer}</span>
                <span class="text-xs opacity-75">Seal: ${seal}</span>
            </div>
            <span class="remove-chip" onclick="removeBlChip('${blId}')">&times;</span>
        `;
        selectedBlChips.appendChild(chip);
    }
    
    window.removeBlChip = function(blId) {
        delete selectedBls[blId];
        const chip = document.querySelector(`[data-bl="${blId}"].selected-chip`);
        if (chip) chip.remove();
        
        const option = blDropdown.querySelector(`[data-bl="${blId}"].bl-option`);
        if (option) option.classList.remove('selected');
        
        updateBlHiddenInputs();
        updateBlSelectedCount();
        calculateDokumenNominal();
    };
    
    if (btnSelectAllBl) {
        btnSelectAllBl.addEventListener('click', function() {
            selectedBls = {...availableBls};
            selectedBlChips.innerHTML = '';
            Object.keys(availableBls).forEach(blId => {
                const blData = availableBls[blId];
                addBlChip(blId, blData.kontainer, blData.seal);
            });
            
            const blOptions = blDropdown.querySelectorAll('.bl-option');
            blOptions.forEach(option => option.classList.add('selected'));
            
            updateBlHiddenInputs();
            updateBlSelectedCount();
            calculateDokumenNominal();
        });
    }
    
    if (btnClearAllBl) {
        btnClearAllBl.addEventListener('click', function() {
            selectedBls = {};
            selectedBlChips.innerHTML = '';
            hiddenBlInputs.innerHTML = '';
            
            const blOptions = blDropdown.querySelectorAll('.bl-option');
            blOptions.forEach(option => option.classList.remove('selected'));
            
            updateBlSelectedCount();
            calculateDokumenNominal();
        });
    }
    
    function updateBlHiddenInputs() {
        hiddenBlInputs.innerHTML = '';
        Object.keys(selectedBls).forEach(blId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'no_bl[]';
            input.value = blId;
            hiddenBlInputs.appendChild(input);
        });
    }
    
    function updateBlSelectedCount() {
        blSelectedCount.textContent = `Terpilih: ${Object.keys(selectedBls).length} kontainer`;
    }
    
    // Function to fetch and display BLs for selected voyages
    function updateBls() {
        if (selectedVoyages.length === 0) {
            blSearch.disabled = true;
            blSearch.placeholder = '--Pilih Voyage Terlebih Dahulu--';
            blDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Pilih voyage terlebih dahulu</p>';
            selectedBls = {};
            selectedBlChips.innerHTML = '';
            hiddenBlInputs.innerHTML = '';
            updateBlSelectedCount();
            return;
        }
        
        blSearch.disabled = false;
        blSearch.placeholder = '--Cari Kontainer--';
        blDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Memuat kontainer...</p>';
        
        // Fetch BLs for all selected voyages
        fetch("{{ url('biaya-kapal/get-bls-by-voyages') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                voyages: selectedVoyages
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.bls) {
                availableBls = data.bls; // Now an object with {id: {kontainer, seal}}
                
                if (Object.keys(availableBls).length > 0) {
                    // Create option list
                    let html = '';
                    Object.keys(availableBls).sort((a, b) => {
                        const kontainerA = availableBls[a]?.kontainer || '';
                        const kontainerB = availableBls[b]?.kontainer || '';
                        return kontainerA.localeCompare(kontainerB);
                    }).forEach(blId => {
                        const blData = availableBls[blId];
                        if (!blData || !blData.kontainer || !blData.seal) return; // Skip invalid data
                        
                        const isSelected = selectedBls.hasOwnProperty(blId) ? 'selected' : '';
                        html += `
                            <div class="bl-option px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 ${isSelected}"
                                 data-bl="${blId}" 
                                 data-kontainer="${blData.kontainer}" 
                                 data-seal="${blData.seal}">
                                <div class="font-medium text-gray-900">${blData.kontainer}</div>
                                <div class="text-xs text-gray-500">Seal: ${blData.seal}</div>
                            </div>
                        `;
                    });
                    blDropdown.innerHTML = html;
                    
                    // Add click handlers to new options
                    const blOptions = blDropdown.querySelectorAll('.bl-option');
                    blOptions.forEach(option => {
                        option.addEventListener('click', function() {
                            const blId = this.getAttribute('data-bl');
                            const kontainer = this.getAttribute('data-kontainer');
                            const seal = this.getAttribute('data-seal');
                            
                            if (!selectedBls.hasOwnProperty(blId)) {
                                selectedBls[blId] = { kontainer, seal };
                                addBlChip(blId, kontainer, seal);
                                updateBlHiddenInputs();
                                updateBlSelectedCount();
                                calculateDokumenNominal();
                                this.classList.add('selected');
                            } else {
                                // If already selected, show visual feedback
                                this.style.backgroundColor = '#fee2e2';
                                setTimeout(() => {
                                    this.style.backgroundColor = '';
                                }, 300);
                            }
                            
                            blSearch.value = '';
                            // Don't hide dropdown to allow multiple selections
                            // blDropdown.classList.add('hidden');
                        });
                    });
                } else {
                    blDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Tidak ada kontainer untuk voyage yang dipilih</p>';
                }
            } else {
                blDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Tidak ada kontainer tersedia</p>';
            }
        })
        .catch(error => {
            console.error('Error fetching BLs:', error);
            blDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-red-600">Gagal memuat kontainer. Silakan coba lagi.</p>';
        });
    }
    
    // Restore old values on page load (for validation errors)
    if (oldKapalValue.length > 0) {
        oldKapalValue.forEach(namaKapal => {
            const option = Array.from(kapalOptions).find(opt => opt.getAttribute('data-nama') === namaKapal);
            if (option) {
                const id = option.getAttribute('data-id');
                selectedKapals.push({ id, nama: namaKapal });
                addKapalChip(id, namaKapal);
                option.classList.add('selected');
            }
        });
        updateKapalHiddenInputs();
        updateKapalSelectedCount();
        updateVoyages();
        
        // Restore voyage selections after voyages are loaded
        setTimeout(() => {
            if (oldVoyageValue.length > 0) {
                oldVoyageValue.forEach(voyage => {
                    if (availableVoyages.includes(voyage)) {
                        selectedVoyages.push(voyage);
                        addVoyageChip(voyage);
                        const option = voyageDropdown.querySelector(`[data-voyage="${voyage}"]`);
                        if (option) option.classList.add('selected');
                    }
                });
                updateVoyageHiddenInputs();
                updateVoyageSelectedCount();
                updateBls();
                
                // Restore BL selections after BLs are loaded
                setTimeout(() => {
                    if (oldBlValue.length > 0) {
                        oldBlValue.forEach(bl => {
                            if (availableBls.includes(bl)) {
                                selectedBls.push(bl);
                                addBlChip(bl);
                                const option = blDropdown.querySelector(`[data-bl="${bl}"]`);
                                if (option) option.classList.add('selected');
                            }
                        });
                        updateBlHiddenInputs();
                        updateBlSelectedCount();
                    }
                }, 1000);
            }
        }, 1000);
    }

    // ============= OPERASIONAL SECTION LOGIC =============
    var operasionalSectionCounter = 0;
    var operasionalSectionsContainer = document.getElementById('operasional_sections_container');
    var addOperasionalSectionBtn = document.getElementById('add_operasional_section_btn');
    var addOperasionalSectionBottomBtn = document.getElementById('add_operasional_section_bottom_btn');
    
    // Initialize cache for voyages
    var cachedVoyages = {};
    
    // Helper function to format currency inputs
    function formatCurrency(input) {
        // Remove non-numeric chars
        let value = input.value.replace(/\D/g, '');
        
        // Format with thousand separator
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        
        input.value = value;
    }

    if (addOperasionalSectionBtn) {
        addOperasionalSectionBtn.addEventListener('click', function() {
            addOperasionalSection();
        });
    }

    if (addOperasionalSectionBottomBtn) {
        addOperasionalSectionBottomBtn.addEventListener('click', function() {
            addOperasionalSection();
        });
    }

    function addOperasionalSection() {
        operasionalSectionCounter++;
        const sectionIndex = operasionalSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'operasional-section mb-6 p-4 border-2 border-indigo-200 rounded-lg bg-indigo-50';
        section.setAttribute('data-section-index', sectionIndex);
        
        // Kapal Options
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        if (typeof allKapalsData !== 'undefined') {
            allKapalsData.forEach(kapal => {
                kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
            });
        }
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-md font-semibold text-indigo-800">
                    <i class="fas fa-ship mr-2"></i>Kapal ${sectionIndex} (Operasional)
                </h4>
                ${sectionIndex > 0 ? `<button type="button" onclick="removeOperasionalSection(${sectionIndex})" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs rounded transition"><i class="fas fa-times mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kapal</label>
                    <select name="operasional_sections[${sectionIndex}][kapal]" class="kapal-select-operasional w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Voyage</label>
                    <select name="operasional_sections[${sectionIndex}][voyage]" id="voyage_operasional_${sectionIndex}" class="voyage-select-operasional w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" disabled required>
                        <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                    </select>
                </div>
                <div>
                     <label class="block text-sm font-medium text-gray-700 mb-1">Nominal</label>
                     <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-xs">Rp</span>
                        <input type="text" name="operasional_sections[${sectionIndex}][nominal]" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="0" oninput="formatCurrency(this); calculateTotalFromAllOperasionalSections()" required>
                     </div>
                </div>
            </div>
        `;
        
        operasionalSectionsContainer.appendChild(section);
        
        // Add event listener for kapal change
        const kapalSelect = section.querySelector('.kapal-select-operasional');
        const voyageSelect = section.querySelector('.voyage-select-operasional');

        kapalSelect.addEventListener('change', function() {
            loadVoyageForOperasional(this, voyageSelect);
        });
    }

    function removeOperasionalSection(index) {
        const section = document.querySelector(`.operasional-section[data-section-index="${index}"]`);
        if (section) section.remove();
        calculateTotalFromAllOperasionalSections();
    }
    
    function loadVoyageForOperasional(selectElement, voyageSelectOrIndex) {
        const namaKapal = selectElement.value;
        
        let voyageSelect;
        if (typeof voyageSelectOrIndex === 'object') {
            voyageSelect = voyageSelectOrIndex;
        } else {
            voyageSelect = document.getElementById(`voyage_operasional_${voyageSelectOrIndex}`);
        }

        if (!voyageSelect) {
            console.error('Voyage select element not found');
            return;
        }
        
        voyageSelect.innerHTML = '<option value="">Memuat...</option>';
        voyageSelect.disabled = true;
        
        if (!namaKapal) {
             voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
             return;
        }

        // Use cached voyages if available
        if (cachedVoyages[namaKapal]) {
            populateVoyageSelect(voyageSelect, cachedVoyages[namaKapal]);
        } else {
            // Fetch voyages
            fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(namaKapal)}`)
                .then(response => response.json())
                .then(data => {
                    // Adapt to the response format (data.voyages which is array of strings)
                    const voyages = data.voyages ? data.voyages.map(v => ({ nomor_voyage: v })) : [];
                    cachedVoyages[namaKapal] = voyages; // Cache it
                    populateVoyageSelect(voyageSelect, voyages);
                })
                .catch(error => {
                    console.error('Error fetching voyages:', error);
                    voyageSelect.innerHTML = '<option value="">Gagal memuat voyage</option>';
                });
        }
    }
    
    function populateVoyageSelect(selectElement, voyages) {
        if (voyages.length === 0) {
            selectElement.innerHTML = '<option value="">Tidak ada voyage aktif</option>';
            selectElement.disabled = false; 
        } else {
            let options = '<option value="">-- Pilih Voyage --</option>';
            voyages.forEach(v => {
                options += `<option value="${v.nomor_voyage}">${v.nomor_voyage}</option>`;
            });
            selectElement.innerHTML = options;
            selectElement.disabled = false;
        }
    }
    
    function calculateTotalFromAllOperasionalSections() {
        let grandTotal = 0;
        document.querySelectorAll('input[name^="operasional_sections"][name$="[nominal]"]').forEach(input => {
            grandTotal += parseInt(input.value.replace(/\D/g, '') || 0);
        });
        
        if (nominalInput) {
            nominalInput.value = grandTotal > 0 ? grandTotal.toLocaleString('id-ID') : '';
            if (typeof calculateSisaPembayaran === 'function') {
                calculateSisaPembayaran(); 
            }
        }
    }
    
    function initializeOperasionalSections() {
        // Only initialize if we have data to show, otherwise default logic (adding 1 empty) applies or is handled by caller
        if(existingOperasionalSections.length > 0) {
            clearAllOperasionalSections();
            existingOperasionalSections.forEach(data => {
                 addOperasionalSection();
                 const sec = operasionalSectionsContainer.lastElementChild;
                 const sectionIndex = sec.getAttribute('data-section-index');
                 
                 sec.querySelector('.kapal-select-operasional').value = data.kapal;
                 
                 const voySel = sec.querySelector('.voyage-select-operasional');
                 voySel.innerHTML = `<option value="${data.voyage}">${data.voyage}</option>`;
                 voySel.value = data.voyage;
                 voySel.disabled = false;
                 
                 const nomInput = sec.querySelector('input[name="operasional_sections['+sectionIndex+'][nominal]"]');
                 if(nomInput) nomInput.value = parseInt(data.nominal).toLocaleString('id-ID');
            });
            calculateTotalFromAllOperasionalSections();
        } else if (operasionalSectionsContainer.children.length === 0) {
            addOperasionalSection();
        }
    }

    function clearAllOperasionalSections() {
        operasionalSectionsContainer.innerHTML = '';
        operasionalSectionCounter = 0;
        if (nominalInput) nominalInput.value = '';
    }

    // ============= STUFFING SECTIONS MANAGEMENT =============
    let stuffingSectionCounter = 0;
    
    function initializeStuffingSections() {
        if (!stuffingSectionsContainer) return;
        stuffingSectionsContainer.innerHTML = '';
        stuffingSectionCounter = 0;
        addStuffingSection();
    }
    
    function clearAllStuffingSections() {
        if (!stuffingSectionsContainer) return;
        stuffingSectionsContainer.innerHTML = '';
        stuffingSectionCounter = 0;
    }
    
    if (addStuffingSectionBtn) {
        addStuffingSectionBtn.addEventListener('click', function() {
            addStuffingSection();
        });
    }

    if (addStuffingSectionBottomBtn) {
        addStuffingSectionBottomBtn.addEventListener('click', function() {
            addStuffingSection();
        });
    }
    
    function addStuffingSection() {
        if (!stuffingSectionsContainer) return;
        stuffingSectionCounter++;
        const sectionIndex = stuffingSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'stuffing-section mb-6 p-4 border-2 border-rose-200 rounded-lg bg-rose-50';
        section.setAttribute('data-stuffing-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex} (Stuffing)</h3>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeStuffingSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="stuffing_sections[${sectionIndex}][kapal]" class="stuffing-kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="stuffing_sections[${sectionIndex}][voyage]" class="stuffing-voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500" required disabled>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="stuffing_sections[${sectionIndex}][voyage]" class="stuffing-voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500 hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="stuffing-voyage-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
            </div>
            
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t pt-4 mt-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal Biaya</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                        <input type="text" name="stuffing_sections[${sectionIndex}][subtotal]" 
                               class="stuffing-subtotal-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-0 text-right" 
                               value="0" readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PPh 2%</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                        <input type="text" name="stuffing_sections[${sectionIndex}][pph]" 
                               class="stuffing-pph-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-0 text-right" 
                               value="0" readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Biaya</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                        <input type="text" name="stuffing_sections[${sectionIndex}][total_biaya]" 
                               class="stuffing-total-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-0 text-right font-bold text-rose-600" 
                               value="0" readonly>
                    </div>
                </div>
            </div>
        `;
        
        stuffingSectionsContainer.appendChild(section);
        
        // Setup kapal change listener
        const kapalSelect = section.querySelector('.stuffing-kapal-select');
        kapalSelect.addEventListener('change', function() {
            loadVoyagesForStuffingSection(sectionIndex, this.value);
        });

        // Setup manual voyage toggle
        const voyageSelect = section.querySelector('.stuffing-voyage-select');
        const voyageInput = section.querySelector('.stuffing-voyage-input');
        const voyageManualBtn = section.querySelector('.stuffing-voyage-manual-btn');

        voyageManualBtn.addEventListener('click', function() {
            if (voyageInput.classList.contains('hidden')) {
                voyageSelect.classList.add('hidden');
                voyageSelect.disabled = true;
                voyageInput.classList.remove('hidden');
                voyageInput.disabled = false;
                voyageInput.focus();
                this.innerHTML = '<i class="fas fa-list"></i>';
            } else {
                voyageInput.classList.add('hidden');
                voyageInput.disabled = true;
                voyageSelect.classList.remove('hidden');
                voyageSelect.disabled = false;
                this.innerHTML = '<i class="fas fa-keyboard"></i>';
            }
        });
    }
    
    window.removeStuffingSection = function(sectionIndex) {
        const section = document.querySelector(`[data-stuffing-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
        }
    };
    
    function loadVoyagesForStuffingSection(sectionIndex, kapalNama) {
        const section = document.querySelector(`[data-stuffing-section-index="${sectionIndex}"]`);
        if (!section) return;
        const voyageSelect = section.querySelector('.stuffing-voyage-select');
        
        if (!kapalNama) {
            voyageSelect.disabled = true;
            voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
            return;
        }
        
        voyageSelect.disabled = true;
        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        
        fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(kapalNama)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.voyages) {
                    let html = '<option value="">-- Pilih Voyage --</option>';
                    data.voyages.forEach(voyage => {
                        html += `<option value="${voyage}">${voyage}</option>`;
                    });
                    voyageSelect.innerHTML = html;
                    voyageSelect.disabled = false;
                } else {
                    voyageSelect.innerHTML = '<option value="">Tidak ada voyage tersedia</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching voyages for Stuffing:', error);
                voyageSelect.innerHTML = '<option value="">Gagal memuat voyages</option>';
            });
    }

    window.addTandaTerimaToSection = function(sectionIndex) {
        const section = document.querySelector(`[data-stuffing-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.stuffing-tt-container');
        const ttIndex = container.children.length;
        
        const wrapper = document.createElement('div');
        wrapper.className = 'tt-search-wrapper mb-3 border p-3 rounded bg-white relative';
        wrapper.innerHTML = `
            <div class="flex items-center gap-2 mb-2">
                <div class="relative flex-1">
                    <input type="text" class="tt-search-input w-full px-3 py-2 border rounded text-sm" placeholder="Cari No. Surat Jalan / No. Kontainer / Pengirim...">
                    <div class="tt-results-dropdown hidden absolute z-10 w-full mt-1 bg-white border rounded shadow-lg max-h-60 overflow-y-auto"></div>
                </div>
                <button type="button" onclick="removeTtFromSection(this)" class="px-2 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="tt-selected-details hidden text-xs text-gray-600 bg-gray-50 p-2 rounded">
                <!-- Selected TT details show here -->
            </div>
            <input type="hidden" name="stuffing_sections[${sectionIndex}][tanda_terima][${ttIndex}][id]" class="selected-tt-id">
        `;
        
        container.appendChild(wrapper);
        setupTtSearch(wrapper);
    }

    window.addTandaTerimaToSectionWithId = function(sectionIndex, ttId) {
        const section = document.querySelector(`[data-stuffing-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.stuffing-tt-container');
        const ttIndex = container.children.length;
        
        const wrapper = document.createElement('div');
        wrapper.className = 'tt-search-wrapper mb-3 border p-3 rounded bg-white relative';
        wrapper.innerHTML = `
            <div class="flex items-center gap-2 mb-2">
                <div class="relative flex-1">
                    <input type="text" class="tt-search-input w-full px-3 py-2 border rounded text-sm" placeholder="Cari No. Surat Jalan / No. Kontainer / Pengirim...">
                    <div class="tt-results-dropdown hidden absolute z-10 w-full mt-1 bg-white border rounded shadow-lg max-h-60 overflow-y-auto"></div>
                </div>
                <button type="button" onclick="removeTtFromSection(this)" class="px-2 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="tt-selected-details hidden text-xs text-gray-600 bg-gray-50 p-2 rounded">
                <!-- Selected TT details show here -->
            </div>
            <input type="hidden" name="stuffing_sections[${sectionIndex}][tanda_terima][${ttIndex}][id]" class="selected-tt-id" value="${ttId}">
        `;
        
        container.appendChild(wrapper);
        setupTtSearch(wrapper);
        
        if (ttId) {
            fetch(`{{ url('biaya-kapal/get-tanda-terima-details') }}/${ttId}`)
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        wrapper.querySelector('.tt-search-input').value = res.data.no_surat_jalan;
                        showTtDetails(ttId, wrapper.querySelector('.tt-selected-details'));
                    }
                });
        }
    }

    function setupTtSearch(wrapper) {
        const searchInput = wrapper.querySelector('.tt-search-input');
        const resultsDropdown = wrapper.querySelector('.tt-results-dropdown');
        const selectedIdInput = wrapper.querySelector('.selected-tt-id');
        const detailsDiv = wrapper.querySelector('.tt-selected-details');
        
        let timeout = null;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            const query = this.value;
            if (query.length < 2) {
                resultsDropdown.classList.add('hidden');
                return;
            }
            
            timeout = setTimeout(() => {
                fetch(`{{ url('biaya-kapal/search-tanda-terima') }}?search=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        resultsDropdown.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(tt => {
                                const item = document.createElement('div');
                                item.className = 'p-2 hover:bg-rose-50 cursor-pointer border-b text-sm';
                                item.innerHTML = `
                                    <div class="font-bold">${tt.no_surat_jalan}</div>
                                    <div class="text-xs text-gray-500">${tt.no_kontainer || 'No Container'} | ${tt.pengirim} -> ${tt.penerima}</div>
                                `;
                                item.addEventListener('click', () => {
                                    searchInput.value = tt.no_surat_jalan;
                                    selectedIdInput.value = tt.id;
                                    resultsDropdown.classList.add('hidden');
                                    showTtDetails(tt.id, detailsDiv);
                                });
                                resultsDropdown.appendChild(item);
                            });
                            resultsDropdown.classList.remove('hidden');
                        } else {
                            resultsDropdown.innerHTML = '<div class="p-2 text-sm text-gray-500">Tidak ditemukan</div>';
                            resultsDropdown.classList.remove('hidden');
                        }
                    });
            }, 300);
        });
        
        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                resultsDropdown.classList.add('hidden');
            }
        });
    }
    
    function showTtDetails(id, container) {
        fetch(`{{ url('biaya-kapal/get-tanda-terima-details') }}/${id}`)
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    const tt = res.data;
                    container.innerHTML = `
                        <div class="grid grid-cols-2 gap-2">
                            <div><strong>Pengirim:</strong> ${tt.pengirim}</div>
                            <div><strong>Penerima:</strong> ${tt.penerima}</div>
                            <div><strong>No. Kontainer:</strong> ${tt.no_kontainer || '-'}</div>
                            <div><strong>Tipe:</strong> ${tt.tipe_kontainer || '-'} (${tt.size || '-'})</div>
                            <div class="col-span-2"><strong>Tujuan:</strong> ${tt.tujuan_pengiriman || '-'}</div>
                        </div>
                    `;
                    container.classList.remove('hidden');
                }
            });
    }
    
    window.removeTtFromSection = function(btn) {
        btn.closest('.tt-search-wrapper').remove();
        // Since we removed a TT, we might need to recalculate. 
        // But we need the sectionIndex. Let's try to find it.
        const section = btn.closest('.stuffing-section');
        if (section) {
            const sectionIndex = section.getAttribute('data-stuffing-section-index');
            calculateStuffingTotals(sectionIndex);
        }
    }

    window.calculateStuffingTotals = function(sectionIndex) {
        const section = document.querySelector(`[data-stuffing-section-index="${sectionIndex}"]`);
        if (!section) return;

        const subtotalInput = section.querySelector('.stuffing-subtotal-input');
        const pphInput = section.querySelector('.stuffing-pph-input');
        const totalInput = section.querySelector('.stuffing-total-input');
        
        // In edit mode, we might not have .tt-price-value yet unless we add it to tt details.
        // For now, let's assume we want to calculate based on number of TTs or something, 
        // or actually, the user might need to input prices.
        // In create.blade.php, it seems it might be fetching prices.
        
        // Let's implement a simple version or wait for user feedback if they want auto-calculation from pricelist.
        // For now, I'll just add the functions so they exist.
        
        let subtotal = 0;
        const ttWrappers = section.querySelectorAll('.tt-search-wrapper');
        // If there's a price field, use it.
        section.querySelectorAll('.tt-price-input').forEach(input => {
            subtotal += parseFloat(input.value.replace(/\./g, '')) || 0;
        });

        const pph = Math.round(subtotal * 0.02);
        const total = subtotal - pph;

        const formatRupiah = (val) => {
            return new Intl.NumberFormat('id-ID').format(Math.round(val));
        };

        if (subtotalInput) subtotalInput.value = formatRupiah(subtotal);
        if (pphInput) pphInput.value = formatRupiah(pph);
        if (totalInput) totalInput.value = formatRupiah(total);

        calculateTotalFromAllStuffingSections();
    }

    function calculateTotalFromAllStuffingSections() {
        let totalSubtotal = 0;
        document.querySelectorAll('.stuffing-section').forEach(section => {
            const input = section.querySelector('.stuffing-subtotal-input');
            if (input) {
                totalSubtotal += parseFloat(input.value.replace(/\./g, '')) || 0;
            }
        });

        const currentJenis = jenisBiayaSelect.options[jenisBiayaSelect.selectedIndex].getAttribute('data-kode');
        if (currentJenis === 'Stuffing') {
            if (nominalInput) {
                nominalInput.value = new Intl.NumberFormat('id-ID').format(totalSubtotal);
            }
        }
    }
</script>
@endpush
@endsection

@push('styles')
<style>
    /* Select2 Styling */
    .select2-container {
        width: 100% !important;
    }
    .select2-container .select2-selection--single {
        height: 42px !important;
        padding: 6px 12px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
        padding-left: 0 !important;
    }
    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
    .select2-dropdown {
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
    }
    .select2-container--open .select2-selection--single {
        border-color: #3b82f6 !important;
    }
    .select2-results__option--highlighted {
        background-color: #3b82f6 !important;
    }

    /* Searchable Multi-Select Styling */
    #kapal_container, #voyage_container_input {
        transition: all 0.15s ease;
    }
    
    #kapal_container:focus-within, #voyage_container_input:focus-within {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .selected-chip {
        display: inline-flex;
        align-items: center;
        background-color: #3b82f6;
        color: white;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 4px;
        margin: 1px;
        gap: 6px;
    }
    
    .selected-chip .remove-chip {
        margin-left: 4px;
        cursor: pointer;
        font-weight: bold;
        font-size: 0.875rem;
        opacity: 0.8;
    }
    
    .selected-chip .remove-chip:hover {
        opacity: 1;
    }
    
    .kapal-option, .voyage-option, .bl-option {
        transition: background-color 0.15s ease;
        position: relative;
    }
    
    .kapal-option:hover, .voyage-option:hover, .bl-option:hover {
        background-color: #eff6ff !important;
    }
    
    .kapal-option.selected, .voyage-option.selected, .bl-option.selected {
        background-color: #dbeafe !important;
        border-left: 3px solid #3b82f6;
        padding-left: 9px;
    }
    
    .kapal-option.selected::after, .voyage-option.selected::after, .bl-option.selected::after, .jenis-biaya-option.selected::after {
        content: '\u2713';
        position: absolute;
        right: 12px;
        color: #3b82f6;
        font-weight: bold;
        font-size: 1rem;
    }
    
    .jenis-biaya-option {
        transition: background-color 0.15s ease;
        position: relative;
    }
    
    .jenis-biaya-option:hover {
        background-color: #eff6ff !important;
    }
    
    .jenis-biaya-option.selected {
        background-color: #dbeafe !important;
        border-left: 3px solid #3b82f6;
        padding-left: 9px;
    }
    
    #jenis_biaya_container {
        transition: all 0.15s ease;
    }
    
    #jenis_biaya_container:focus-within {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    #kapal_search::placeholder, #voyage_search::placeholder {
        color: #9ca3af;
    }
    
    #kapal_dropdown, #voyage_dropdown {
        border-top: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>

@endpush
