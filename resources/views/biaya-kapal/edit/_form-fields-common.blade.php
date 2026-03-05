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

                <!-- Trucking (for Biaya Trucking) -->
                <div id="trucking_wrapper" class="md:col-span-2 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Detail Trucking <span class="text-red-500">*</span>
                        </label>
                        <button type="button" id="add_trucking_section_btn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Trucking</span>
                        </button>
                    </div>
                    <div id="trucking_sections_container"></div>
                </div>

                <!-- Labuh Tambat (for Biaya Labuh Tambat) -->
                <div id="labuh_tambat_wrapper" class="md:col-span-2 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Detail Labuh Tambat <span class="text-red-500">*</span>
                        </label>
                        <button type="button" id="add_labuh_tambat_section_btn" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Kapal/Voyage</span>
                        </button>
                    </div>
                    <div id="labuh_tambat_sections_container"></div>
                    
                    <button type="button" id="add_labuh_tambat_section_bottom_btn" class="mt-2 w-full py-2 border-2 border-dashed border-cyan-300 rounded-lg text-cyan-600 hover:bg-cyan-50 hover:border-cyan-400 transition flex items-center justify-center gap-2 font-medium">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Kapal/Voyage Lainnya</span>
                    </button>
                </div>

                <!-- THC (for Biaya THC) -->
                <div id="thc_wrapper" class="md:col-span-2 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Detail Kapal & THC (Tanda Terima) <span class="text-red-500">*</span>
                        </label>
                        <button type="button" id="add_thc_section_btn" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Kapal</span>
                        </button>
                    </div>
                    <div id="thc_sections_container"></div>
                    
                    <button type="button" id="add_thc_section_bottom_btn" class="mt-2 w-full py-2 border-2 border-dashed border-teal-300 rounded-lg text-teal-600 hover:bg-teal-50 hover:border-teal-400 transition flex items-center justify-center gap-2 font-medium">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Kapal Lainnya</span>
                    </button>
                </div>

                <!-- LOLO (for Biaya Lolo) -->
                <div id="lolo_wrapper" class="md:col-span-2 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Detail Kapal & LOLO <span class="text-red-500">*</span>
                        </label>
                        <button type="button" id="add_lolo_section_btn" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Kapal</span>
                        </button>
                    </div>
                    <div id="lolo_sections_container"></div>
                    
                    <button type="button" id="add_lolo_section_bottom_btn" class="mt-2 w-full py-2 border-2 border-dashed border-indigo-300 rounded-lg text-indigo-600 hover:bg-indigo-50 hover:border-indigo-400 transition flex items-center justify-center gap-2 font-medium">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Kapal Lainnya</span>
                    </button>
                </div>

                <!-- STORAGE Section -->
                <div id="storage_wrapper" class="md:col-span-2 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Detail Kapal & Biaya Storage <span class="text-red-500">*</span>
                        </label>
                        <button type="button" id="add_storage_section_btn" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Kapal</span>
                        </button>
                    </div>
                    <div id="storage_sections_container"></div>
                    
                    <button type="button" id="add_storage_section_bottom_btn" class="mt-2 w-full py-2 border-2 border-dashed border-sky-300 rounded-lg text-sky-600 hover:bg-sky-50 hover:border-sky-400 transition flex items-center justify-center gap-2 font-medium">
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