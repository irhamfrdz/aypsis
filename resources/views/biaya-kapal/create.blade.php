@extends('layouts.app')

@section('title', 'Tambah Biaya Kapal')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Biaya Kapal</h1>
                <p class="text-gray-600 mt-1">Tambah data biaya operasional kapal baru</p>
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

        <form action="{{ route('biaya-kapal.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

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

                <!-- Nomor Invoice (Auto-generated) -->
                <div>
                    <label for="nomor_invoice" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Invoice <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="nomor_invoice" 
                               name="nomor_invoice" 
                               value="{{ old('nomor_invoice') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nomor_invoice') border-red-500 @enderror"
                               placeholder="Loading..."
                               readonly
                               required>
                        <div id="invoice_loader" class="absolute right-3 top-3">
                            <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    @error('nomor_invoice')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Referensi -->
                <div>
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kapal <span class="text-red-500">*</span> <span class="text-xs text-gray-500">(Bisa pilih lebih dari 1)</span>
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Voyage <span class="text-xs text-gray-500">(Bisa pilih lebih dari 1)</span>
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor BL <span class="text-xs text-gray-500">(Bisa pilih lebih dari 1)</span>
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
                    <label for="jenis_biaya" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Biaya <span class="text-red-500">*</span>
                    </label>
                    <select id="jenis_biaya" 
                            name="jenis_biaya" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jenis_biaya') border-red-500 @enderror"
                            required>
                        <option value="">-- Pilih Jenis Biaya --</option>
                        @foreach($klasifikasiBiayas as $k)
                            <option value="{{ $k->kode }}" {{ old('jenis_biaya') == $k->kode ? 'selected' : '' }}>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                    @error('jenis_biaya')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Barang (for Biaya Buruh) -->
                <div id="barang_wrapper" class="md:col-span-2 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Barang <span class="text-red-500">*</span>
                    </label>
                    <div id="barang_container"></div>
                    <button type="button" id="add_barang_btn" class="mt-2 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg transition">
                        <i class="fas fa-plus mr-1"></i> Tambah Barang
                    </button>
                </div>

                <!-- Nominal -->
                <div>
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
    // Store pricelist buruh data
    const pricelistBuruhData = @json($pricelistBuruh);

    // Format nominal input with thousand separator
    const nominalInput = document.getElementById('nominal');
    
    nominalInput.addEventListener('input', function(e) {
        // Remove all non-numeric characters
        let value = this.value.replace(/\D/g, '');
        
        // Format with thousand separator
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        
        this.value = value;
    });

    // Before form submit, remove formatting from nominal
    document.querySelector('form').addEventListener('submit', function(e) {
        const nominalInput = document.getElementById('nominal');
        nominalInput.value = nominalInput.value.replace(/\./g, '');
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

    // ============= JENIS BIAYA TOGGLE =============
    const jenisBiayaSelect = document.getElementById('jenis_biaya');
    const barangWrapper = document.getElementById('barang_wrapper');
    const addBarangBtn = document.getElementById('add_barang_btn');
    
    // Get BL wrapper element
    const blWrapper = document.querySelector('#bl_container_input').closest('div').parentElement;

    // Toggle barang wrapper based on jenis biaya
    jenisBiayaSelect.addEventListener('change', function() {
        const selectedValue = this.value;
        const selectedText = this.options[this.selectedIndex].text;
        
        // Show barang wrapper if "Biaya Buruh" is selected
        if (selectedText.toLowerCase().includes('buruh')) {
            barangWrapper.classList.remove('hidden');
            initializeBarangInputs();
            
            // Hide BL wrapper for Biaya Buruh
            blWrapper.classList.add('hidden');
            clearBlSelections();
        } else {
            barangWrapper.classList.add('hidden');
            clearBarangInputs();
            // Clear calculated total when switching away from Biaya Buruh
            nominalInput.value = '';
            
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

    // Barang management functions
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
                <input type="number" name="barang[${index}][jumlah]" value="${existingJumlah}" min="1" class="jumlah-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0" required>
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
            const jumlah = parseInt(jumlahInputs[index].value) || 0;
            total += tarif * jumlah;
        });
        
        if (total > 0) {
            nominalInput.value = total.toLocaleString('id-ID');
        }
    }

    // Add button for barang
    if (addBarangBtn) {
        addBarangBtn.addEventListener('click', function() {
            addBarangInput();
        });
    }

    // ============= KAPAL MULTI-SELECT =============
    const kapalSearch = document.getElementById('kapal_search');
    const kapalDropdown = document.getElementById('kapal_dropdown');
    const selectedKapalChips = document.getElementById('selected_kapal_chips');
    const hiddenKapalInputs = document.getElementById('hidden_kapal_inputs');
    const kapalOptions = document.querySelectorAll('.kapal-option');
    const kapalSelectedCount = document.getElementById('kapalSelectedCount');
    const selectAllKapalBtn = document.getElementById('selectAllKapalBtn');
    const clearAllKapalBtn = document.getElementById('clearAllKapalBtn');
    
    let selectedKapals = [];
    const oldKapalValue = @json(old('nama_kapal', []));
    
    // Show kapal dropdown on focus
    kapalSearch.addEventListener('focus', function() {
        kapalDropdown.classList.remove('hidden');
        filterKapalOptions();
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
            }
            
            kapalSearch.value = '';
            kapalDropdown.classList.add('hidden');
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
    
    selectAllKapalBtn.addEventListener('click', function() {
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
    
    clearAllKapalBtn.addEventListener('click', function() {
        selectedKapals = [];
        selectedKapalChips.innerHTML = '';
        hiddenKapalInputs.innerHTML = '';
        kapalOptions.forEach(option => option.classList.remove('selected'));
        updateKapalSelectedCount();
        updateVoyages();
    });
    
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
    const voyageSearch = document.getElementById('voyage_search');
    const voyageDropdown = document.getElementById('voyage_dropdown');
    const selectedVoyageChips = document.getElementById('selected_voyage_chips');
    const hiddenVoyageInputs = document.getElementById('hidden_voyage_inputs');
    const voyageSelectedCount = document.getElementById('voyageSelectedCount');
    const selectAllVoyageBtn = document.getElementById('selectAllVoyageBtn');
    const clearAllVoyageBtn = document.getElementById('clearAllVoyageBtn');
    
    let selectedVoyages = [];
    let availableVoyages = [];
    const oldVoyageValue = @json(old('no_voyage', []));
    
    // Show voyage dropdown on focus
    voyageSearch.addEventListener('focus', function() {
        if (selectedKapals.length > 0) {
            voyageDropdown.classList.remove('hidden');
            filterVoyageOptions();
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
    
    selectAllVoyageBtn.addEventListener('click', function() {
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
    
    clearAllVoyageBtn.addEventListener('click', function() {
        selectedVoyages = [];
        selectedVoyageChips.innerHTML = '';
        hiddenVoyageInputs.innerHTML = '';
        
        const voyageOptions = voyageDropdown.querySelectorAll('.voyage-option');
        voyageOptions.forEach(option => option.classList.remove('selected'));
        
        updateBls();
        updateVoyageSelectedCount();
    });
    
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
                            }
                            
                            voyageSearch.value = '';
                            voyageDropdown.classList.add('hidden');
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
    const blSearch = document.getElementById('bl_search');
    const blDropdown = document.getElementById('bl_dropdown');
    const selectedBlChips = document.getElementById('selected_bl_chips');
    const hiddenBlInputs = document.getElementById('hidden_bl_inputs');
    const blSelectedCount = document.getElementById('blSelectedCount');
    const selectAllBlBtn = document.getElementById('selectAllBlBtn');
    const clearAllBlBtn = document.getElementById('clearAllBlBtn');
    
    let selectedBls = {}; // Changed to object to store {id: {kontainer, seal}}
    let availableBls = {}; // Changed to object to store {id: {kontainer, seal}}
    const oldBlValue = @json(old('no_bl', []));
    
    // Show BL dropdown on focus
    blSearch.addEventListener('focus', function() {
        if (selectedVoyages.length > 0) {
            blDropdown.classList.remove('hidden');
            filterBlOptions();
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
    };
    
    selectAllBlBtn.addEventListener('click', function() {
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
    });
    
    clearAllBlBtn.addEventListener('click', function() {
        selectedBls = {};
        selectedBlChips.innerHTML = '';
        hiddenBlInputs.innerHTML = '';
        
        const blOptions = blDropdown.querySelectorAll('.bl-option');
        blOptions.forEach(option => option.classList.remove('selected'));
        
        updateBlSelectedCount();
    });
    
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
        fetch('{{ url('biaya-kapal/get-bls-by-voyages') }}', {
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
                                this.classList.add('selected');
                            }
                            
                            blSearch.value = '';
                            blDropdown.classList.add('hidden');
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

    // Generate Invoice Number
    async function generateInvoiceNumber() {
        const invoiceInput = document.getElementById('nomor_invoice');
        const loader = document.getElementById('invoice_loader');
        
        try {
            const response = await fetch("{{ route('biaya-kapal.get-next-invoice-number') }}", {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            const data = await response.json();
            
            if (data.success) {
                invoiceInput.value = data.invoice_number;
            } else {
                // Fallback if server generation fails
                const now = new Date();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const year = String(now.getFullYear()).slice(-2);
                invoiceInput.value = `BKP-${month}-${year}-000001`;
                console.warn('Failed to generate invoice number from server, using fallback');
            }
        } catch (error) {
            // Fallback if fetch fails
            const now = new Date();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = String(now.getFullYear()).slice(-2);
            invoiceInput.value = `BKP-${month}-${year}-000001`;
            console.error('Error generating invoice number:', error);
        } finally {
            if (loader) {
                loader.style.display = 'none';
            }
        }
    }

    // Generate invoice number on page load
    document.addEventListener('DOMContentLoaded', function() {
        generateInvoiceNumber();
    });
</script>
@endpush
@endsection

@push('styles')
<style>
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
    
    .kapal-option, .voyage-option {
        transition: background-color 0.15s ease;
    }
    
    .kapal-option:hover, .voyage-option:hover {
        background-color: #eff6ff !important;
    }
    
    .kapal-option.selected, .voyage-option.selected {
        background-color: #dbeafe;
        opacity: 0.6;
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
