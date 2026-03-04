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

                <!-- Nomor Invoice (Auto-generated - Display Only) -->
                <div>
                    <label for="nomor_invoice_display" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Invoice <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-500 font-normal">(Otomatis dibuat oleh sistem)</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="nomor_invoice_display" 
                               value="{{ old('nomor_invoice') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Akan dibuat otomatis saat menyimpan..."
                               readonly
                               disabled>
                        <div id="invoice_loader" class="absolute right-3 top-3">
                            <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Nomor invoice akan dibuat otomatis oleh sistem saat data disimpan
                    </p>
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
