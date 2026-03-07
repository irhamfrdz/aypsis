    // ============= AIR SECTIONS MANAGEMENT =============
    let airSectionCounter = 0;
    const airSectionsContainer = document.getElementById('air_sections_container');
    const addAirSectionBtn = document.getElementById('add_air_section_btn');
    
    function initializeAirSections() {
        airSectionsContainer.innerHTML = '';
        airSectionCounter = 0;
        addAirSection();
    }
    
    function clearAllAirSections() {
        if (airSectionsContainer) airSectionsContainer.innerHTML = '';
        airSectionCounter = 0;
        if (nominalInput) nominalInput.value = '';
        // Removed global summary input resets
    }
    
    addAirSectionBtn.addEventListener('click', function() {
        addAirSection();
    });
    
    window.updatePriceFromSelect = function(select) {
        const container = select.closest('.flex.flex-col');
        const priceInput = container.querySelector('.price-input-air');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            const harga = selectedOption.getAttribute('data-harga');
            priceInput.value = harga || 0;
        } else {
            priceInput.value = 0;
        }
    };
    
    window.toggleTypeInput = function(btn, sectionIndex) {
        const container = btn.closest('.flex.flex-col');
        const select = container.querySelector('.type-select-air');
        const manualInput = container.querySelector('.type-manual-input-air');
        const hiddenManual = container.querySelector('.hidden-type-manual');
        const priceInput = container.querySelector('.price-input-air');
        
        if (manualInput.classList.contains('hidden')) {
            // Switch to Manual
            select.classList.add('hidden');
            select.disabled = true;
            select.required = false;
            
            manualInput.classList.remove('hidden');
            manualInput.required = true;
            
            hiddenManual.disabled = false;
            
            btn.classList.add('bg-blue-200', 'text-blue-700');
            btn.classList.remove('bg-gray-200', 'text-gray-600');
            btn.innerHTML = '<i class="fas fa-list"></i>';
            btn.title = "Switch to List Selection";
            
            priceInput.readOnly = false;
            priceInput.classList.remove('bg-gray-100');
            priceInput.classList.add('bg-white');
            priceInput.value = '';
            priceInput.focus();
        } else {
            // Switch to Select
            manualInput.classList.add('hidden');
            manualInput.required = false;
            
            select.classList.remove('hidden');
            select.disabled = false;
            select.required = true;
            
            hiddenManual.disabled = true;
            
            btn.classList.remove('bg-blue-200', 'text-blue-700');
            btn.classList.add('bg-gray-200', 'text-gray-600');
            btn.innerHTML = '<i class="fas fa-keyboard"></i>';
            btn.title = "Switch to Manual Input";
            
            priceInput.readOnly = true;
            priceInput.classList.add('bg-gray-100');
            priceInput.classList.remove('bg-white');
            
            // Restore price
            updatePriceFromSelect(select);
        }
        
        calculateAirSectionTotal(sectionIndex);
    };

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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <select name="air[${sectionIndex}][lokasi]" class="lokasi-select-air w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500">
                        ${lokasiOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vendor Air Tawar</label>
                    <select name="air[${sectionIndex}][vendor]" class="vendor-select-air w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500" required>
                        ${vendorOptions}
                    </select>
                </div>
                <div class="types-wrapper-air-container md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <div class="types-list-air space-y-2 mb-2">
                          <div class="flex flex-col gap-1 border p-2 rounded bg-gray-50 relative">
                                <div class="flex gap-2 w-full">
                                    <select name="air[${sectionIndex}][types][]" class="type-select-air w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500" disabled required onchange="updatePriceFromSelect(this); calculateAirSectionTotal(${sectionIndex})">
                                        <option value="">-- Pilih Vendor Terlebih Dahulu --</option>
                                    </select>
                                    
                                    <input type="hidden" name="air[${sectionIndex}][types][]" class="hidden-type-manual" value="MANUAL" disabled>
                                    
                                    <input type="text" name="air[${sectionIndex}][manual_names][]" class="type-manual-input-air hidden w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Nama Type Manual">
                                    
                                    <button type="button" class="type-toggle-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Switch Input (Master/Manual)" onclick="toggleTypeInput(this, ${sectionIndex})">
                                        <i class="fas fa-keyboard"></i>
                                    </button>
                                </div>
                                
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="flex-grow">
                                        <label class="text-xs text-gray-500 block mb-1">Harga Satuan (Rp)</label>
                                        <input type="number" name="air[${sectionIndex}][custom_prices][]" class="price-input-air w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-cyan-500 bg-gray-100" placeholder="0" readonly oninput="calculateAirSectionTotal(${sectionIndex})">
                                    </div>
                                    <div class="w-1/4">
                                        <label class="text-xs text-gray-500 block mb-1">Berapa Ton</label>
                                        <input type="number" step="0.01" min="0" name="air[${sectionIndex}][type_tonase][]" class="tonase-input-air w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-cyan-500" placeholder="0" oninput="calculateAirSectionTotal(${sectionIndex})">
                                    </div>
                                    <div class="flex items-end pb-1">
                                        <div class="flex items-center gap-2">
                                            <input type="hidden" name="air[${sectionIndex}][type_is_lumpsum][]" value="0" class="lumpsum-hidden">
                                            <input type="checkbox" class="lumpsum-checkbox rounded text-cyan-600 focus:ring-cyan-500 h-5 w-5" onchange="this.previousElementSibling.value = this.checked ? 1 : 0; calculateAirSectionTotal(${sectionIndex})">
                                            <label class="text-xs text-gray-600">Lumpsum (Fix)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <button type="button" class="add-type-btn-air text-xs bg-cyan-100 hover:bg-cyan-200 text-cyan-700 px-2 py-1 rounded transition duration-200 flex items-center gap-1" disabled onclick="addTypeToAirSection(${sectionIndex})">
                        <i class="fas fa-plus"></i> Tambah Type
                    </button>
                </div>
                
                <div class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jasa Air (Input)</label>
                    <input type="number" name="air[${sectionIndex}][jasa_air]" class="jasa-air-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500" value="0" placeholder="0">
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
        
        // Setup kapal change listener with Select2
        const kapalSelect = section.querySelector('.kapal-select-air');
        $(kapalSelect).select2({
            placeholder: "-- Pilih Kapal --",
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 0
        }).on('change', function() {
            loadVoyagesForAirSection(sectionIndex, this.value);
        });
        
        // Setup vendor change listener to load types
        const vendorSelect = section.querySelector('.vendor-select-air');
        vendorSelect.addEventListener('change', function() {
            loadTypesForVendor(sectionIndex, this.value);
            // Jasa air logic removed as per request
            /*
            const jasaAirInput = section.querySelector('.jasa-air-input');
            if (this.value && this.value.toLowerCase().includes('abqori')) {
                jasaAirInput.value = 100000;
            } else {
                jasaAirInput.value = 0;
            }
            */
            calculateAirSectionTotal(sectionIndex);
        });
        
        // Setup add type button listener
        const addTypeBtn = section.querySelector('.add-type-btn-air');
        addTypeBtn.addEventListener('click', function() {
            addTypeToAirSection(sectionIndex);
        });

        // Setup type change listener for auto-calculation (using delegation for dynamic inputs)
        const typesList = section.querySelector('.types-list-air');
        typesList.addEventListener('change', function(e) {
            if (e.target.classList.contains('type-select-air')) {
                calculateAirSectionTotal(sectionIndex);
            }
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

        // Jasa air input listener removed
        


        // Setup manual voyage toggle
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
        
        // Trigger initial calculation for this section
        calculateAirSectionTotal(sectionIndex);
    }
    
    window.removeAirSection = function(sectionIndex) {
        const section = document.querySelector(`.air-section[data-section-index="${sectionIndex}"]`);
        if (section) {
            // Destroy Select2 before removing element
            $(section).find('.kapal-select-air').select2('destroy');
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
        const typesList = section.querySelector('.types-list-air');
        const typeContainers = typesList.querySelectorAll('.flex.flex-col.gap-1.border.p-2.rounded.bg-gray-50'); // Select the new container
        const addTypeBtn = section.querySelector('.add-type-btn-air');
        
        if (!vendorName) {
            typeContainers.forEach(container => {
                const select = container.querySelector('.type-select-air');
                select.disabled = true;
                select.innerHTML = '<option value="">-- Pilih Vendor Terlebih Dahulu --</option>';
            });
            addTypeBtn.disabled = true;
            typesList.dataset.options = '<option value="">-- Pilih Vendor Terlebih Dahulu --</option>';
            return;
        }
        
        // Get selected lokasi for this section
        const lokasiSelect = section.querySelector('.lokasi-select-air');
        const selectedLokasi = lokasiSelect ? (lokasiSelect.value || '') : '';

        // Filter pricelist data by vendor name and lokasi (if selected)
        const vendorTypes = pricelistAirTawarData.filter(item => item.nama_agen === vendorName && (selectedLokasi === '' || item.lokasi === selectedLokasi));
        
        let options = '<option value="">-- Pilih Type --</option>';
        if (vendorTypes.length > 0) {
            vendorTypes.forEach(type => {
                // REMOVE /ton as requested
                options += `<option value="${type.id}" data-keterangan="${type.keterangan}" data-harga="${type.harga}">${type.keterangan} - Rp ${parseInt(type.harga).toLocaleString('id-ID')}</option>`;
            });
            
            typeContainers.forEach(container => {
                const select = container.querySelector('.type-select-air');
                const priceInput = container.querySelector('.price-input-air');
                const currentValue = select.value;
                select.disabled = false;
                select.innerHTML = options;
                if (currentValue) select.value = currentValue;
                
                // Update price if not manual
                if (!container.querySelector('.type-manual-input-air').required) {
                    updatePriceFromSelect(select);
                }
            });
            
            addTypeBtn.disabled = false;
        } else {
            options = '<option value="">Tidak ada type tersedia</option>';
            typeContainers.forEach(container => {
                const select = container.querySelector('.type-select-air');
                select.disabled = true;
                select.innerHTML = options;
            });
            addTypeBtn.disabled = true;
        }
        
        // Store current options in dataset for new inputs
        typesList.dataset.options = options;
    }

    function addTypeToAirSection(sectionIndex) {
        const section = document.querySelector(`.air-section[data-section-index="${sectionIndex}"]`);
        const typesList = section.querySelector('.types-list-air');
        
        // Ensure options exist
        const options = typesList.dataset.options || '<option value="">-- Pilih Type --</option>';
        
        const div = document.createElement('div');
        div.className = 'flex flex-col gap-1 border p-2 rounded bg-gray-50 relative';
        div.innerHTML = `
            <div class="flex gap-2 w-full">
                <select name="air[${sectionIndex}][types][]" class="type-select-air w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500" onchange="updatePriceFromSelect(this); calculateAirSectionTotal(${sectionIndex})">
                    ${options}
                </select>
                
                <input type="hidden" name="air[${sectionIndex}][types][]" class="hidden-type-manual" value="MANUAL" disabled>
                
                <input type="text" name="air[${sectionIndex}][manual_names][]" class="type-manual-input-air hidden w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Nama Type Manual">
                                    
                <button type="button" class="type-toggle-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Switch Input (Master/Manual)" onclick="toggleTypeInput(this, ${sectionIndex})">
                    <i class="fas fa-keyboard"></i>
                </button>
                    
                <button type="button" class="text-red-500 hover:text-red-700 ml-1" onclick="this.closest('.flex-col').remove(); calculateAirSectionTotal(${sectionIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            <div class="flex items-center gap-2 mt-1">
                <div class="flex-grow">
                    <label class="text-xs text-gray-500 block mb-1">Harga Satuan (Rp)</label>
                    <input type="number" name="air[${sectionIndex}][custom_prices][]" class="price-input-air w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-cyan-500 bg-gray-100" placeholder="0" readonly oninput="calculateAirSectionTotal(${sectionIndex})">
                </div>
                <div class="w-1/4">
                    <label class="text-xs text-gray-500 block mb-1">Berapa Ton</label>
                    <input type="number" step="0.01" min="0" name="air[${sectionIndex}][type_tonase][]" class="tonase-input-air w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-cyan-500" placeholder="0" oninput="calculateAirSectionTotal(${sectionIndex})">
                </div>
                <div class="flex items-end pb-1">
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="air[${sectionIndex}][type_is_lumpsum][]" value="0" class="lumpsum-hidden">
                        <input type="checkbox" class="lumpsum-checkbox rounded text-cyan-600 focus:ring-cyan-500 h-5 w-5" onchange="this.previousElementSibling.value = this.checked ? 1 : 0; calculateAirSectionTotal(${sectionIndex})">
                        <label class="text-xs text-gray-600">Lumpsum (Fix)</label>
                    </div>
                </div>
            </div>
        `;
        
        typesList.appendChild(div);
    }

    // Update vendor list for a given lokasi in a section
    function updateVendorsForLokasi(sectionIndex, lokasi) {
        const section = document.querySelector(`.air-section[data-section-index="${sectionIndex}"]`);
        if (!section) return;
        const vendorSelect = section.querySelector('.vendor-select-air');
        const typesList = section.querySelector('.types-list-air');
        const typeSelects = typesList.querySelectorAll('.type-select-air');
        const addTypeBtn = section.querySelector('.add-type-btn-air');

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
        if (typeSelects.length > 0) {
            typeSelects.forEach(select => {
                select.disabled = true;
                select.innerHTML = '<option value="">-- Pilih Vendor Terlebih Dahulu --</option>';
            });
            addTypeBtn.disabled = true;
            typesList.dataset.options = '<option value="">-- Pilih Vendor Terlebih Dahulu --</option>';
        }
    }
    
    function calculateAirSectionTotal(sectionIndex) {
        const section = document.querySelector(`.air-section[data-section-index="${sectionIndex}"]`);
        // Select all type wrapper containers (the flex-cols)
        const typeContainers = section.querySelectorAll('.types-list-air > div');
        
        // Updated selectors
        const subTotalDisplay = section.querySelector('.sub-total-display');
        const subTotalValue = section.querySelector('.sub-total-value');
        const jasaAirInput = section.querySelector('.jasa-air-input');
        
        const hargaHidden = section.querySelector('.harga-hidden');
        
        // New fields
        const pphDisplay = section.querySelector('.pph-display');
        const pphValue = section.querySelector('.pph-value');
        const grandTotalDisplay = section.querySelector('.grand-total-display');
        const grandTotalValue = section.querySelector('.grand-total-value');

        // Vendor check for Abqori PPH logic
        const vendorSelect = section.querySelector('.vendor-select-air');
        const vendorName = vendorSelect ? (vendorSelect.options[vendorSelect.selectedIndex]?.text || '').toLowerCase() : '';
        const isAbqori = vendorName.includes('abqori');
        
        let totalCost = 0;
        let taxableCost = 0;

        // Iterate through each type container to calculate individual costs
        typeContainers.forEach(container => {
            const select = container.querySelector('.type-select-air');
            const manualInput = container.querySelector('.type-manual-input-air');
            const checkbox = container.querySelector('.lumpsum-checkbox');
            
            // Get current type text for PPH logic
            let currentTypeText = '';
            if (manualInput && !manualInput.classList.contains('hidden')) {
                currentTypeText = (manualInput.value || '').toLowerCase();
            } else if (select && select.selectedIndex >= 0) {
                currentTypeText = (select.options[select.selectedIndex]?.text || '').toLowerCase();
            }
            
            // Determine price and quantity
            const priceInput = container.querySelector('.price-input-air');
            const tonaseInput = container.querySelector('.tonase-input-air');
            const harga = parseFloat(priceInput.value) || 0;
            
            let selectedKuantitas = 0;
            if (tonaseInput && tonaseInput.value !== "") {
                selectedKuantitas = parseFloat(tonaseInput.value) || 0;
            }
            
            const isLumpsum = checkbox ? checkbox.checked : false;
            let currentItemCost = isLumpsum ? harga : (harga * selectedKuantitas);

            totalCost += currentItemCost;

            // PPH Logic for Abqori (Only Agency and Jasa Air are taxable)
            if (isAbqori) {
                if (currentTypeText.includes('agency') || currentTypeText.includes('jasa air')) {
                    taxableCost += currentItemCost;
                }
            } else {
                taxableCost += currentItemCost;
            }
        });

        let waterCost = Math.round(totalCost); // This is now the calculated base cost
        
        let jasaAir = parseFloat(jasaAirInput ? jasaAirInput.value : 0) || 0; 
        
        // Sub Total = (Price * Qty) + Jasa Air
        let subTotal = waterCost + jasaAir;
        
        // PPH calculation adjusted for Abqori
        // jasaAir (the hidden input) is always taxable if Abqori is chosen (it's Jasa Air Jakarta)
        let finalTaxableBase = isAbqori ? (taxableCost + jasaAir) : subTotal;
        const pph = Math.round(finalTaxableBase * 0.02);
        const grandTotal = subTotal - pph;
        
        subTotalDisplay.value = subTotal > 0 ? `Rp ${subTotal.toLocaleString('id-ID')}` : 'Rp 0';
        subTotalValue.value = subTotal;
        // hargaHidden is less relevant now as it varies, but we can store the calculated waterCost for reference if needed
        hargaHidden.value = waterCost; 
        
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
        if (totalGrandTotal > 0) {
            nominalInput.value = totalGrandTotal.toLocaleString('id-ID');
        } else {
            nominalInput.value = '';
        }
        
        // Summary calculations for Biaya Air (jasa_air, pph_air, grand_total_air summary fields removed)
    }
    window.addTypeToAirSectionWithValue = function(sectionIndex, typeId, typeKeterangan, isLumpsum, kuantitas, harga) {
        const section = document.querySelector(`.air-section[data-section-index="${sectionIndex}"]`);
        const typesList = section.querySelector('.types-list-air');
        const count = typesList.children.length;
        
        const div = document.createElement('div');
        div.className = 'flex flex-col gap-1 border p-2 rounded bg-gray-50 relative mt-2';
        
        let typeSelectOptions = typesList.dataset.options || '<option value="">-- Pilih Vendor Terlebih Dahulu --</option>';

        const isManual = (typeId == null || typeId == '' || typeId == 'MANUAL' || typeId == 0);
        
        let selectClass = isManual ? 'type-select-air w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 hidden' : 'type-select-air w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500';
        let selectDisabled = isManual ? 'disabled' : '';
        
        let manualInputClass = isManual ? 'type-manual-input-air w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500' : 'type-manual-input-air hidden w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500';
        let manualInputDisabled = isManual ? '' : 'disabled';
        let manualInputValue = isManual ? typeKeterangan : '';

        let hiddenManualDisabled = isManual ? '' : 'disabled';
        
        let btnClass = isManual ? 'type-toggle-btn px-3 py-2 bg-blue-200 hover:bg-blue-300 text-blue-700 rounded-lg transition' : 'type-toggle-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition';
        let btnInner = isManual ? '<i class="fas fa-list"></i>' : '<i class="fas fa-keyboard"></i>';

        let priceReadOnly = isManual ? '' : 'readonly';
        let priceBgClass = isManual ? 'bg-white' : 'bg-gray-100';
        
        let lumpsumChecked = isLumpsum == 1 ? 'checked' : '';

        div.innerHTML = `
            <div class="flex gap-2 w-full">
                <select name="air[${sectionIndex}][types][]" class="${selectClass}" ${selectDisabled} required onchange="updatePriceFromSelect(this); calculateAirSectionTotal(${sectionIndex})">
                    ${typeSelectOptions}
                </select>
                <input type="hidden" name="air[${sectionIndex}][types][]" class="hidden-type-manual" value="MANUAL" ${hiddenManualDisabled}>
                <input type="text" name="air[${sectionIndex}][manual_names][]" class="${manualInputClass}" value="${manualInputValue}" placeholder="Nama Type Manual">
                
                <button type="button" class="${btnClass}" title="Switch Input (Master/Manual)" onclick="toggleTypeInput(this, ${sectionIndex})">
                    ${btnInner}
                </button>
                <button type="button" class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition" onclick="removeTypeFromAirSection(this, ${sectionIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            <div class="flex items-center gap-2 mt-1">
                <div class="flex-grow">
                    <label class="text-xs text-gray-500 block mb-1">Harga Satuan (Rp)</label>
                    <input type="number" name="air[${sectionIndex}][custom_prices][]" class="price-input-air w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-cyan-500 ${priceBgClass}" value="${harga}" placeholder="0" ${priceReadOnly} oninput="calculateAirSectionTotal(${sectionIndex})">
                </div>
                <div class="w-1/4">
                    <label class="text-xs text-gray-500 block mb-1">Berapa Ton</label>
                    <input type="number" step="0.01" min="0" name="air[${sectionIndex}][type_tonase][]" class="tonase-input-air w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-cyan-500" value="${kuantitas}" placeholder="0" oninput="calculateAirSectionTotal(${sectionIndex})">
                </div>
                <div class="flex items-end pb-1">
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="air[${sectionIndex}][type_is_lumpsum][]" value="${isLumpsum}" class="lumpsum-hidden">
                        <input type="checkbox" class="lumpsum-checkbox rounded text-cyan-600 focus:ring-cyan-500 h-5 w-5" ${lumpsumChecked} onchange="this.previousElementSibling.value = this.checked ? 1 : 0; calculateAirSectionTotal(${sectionIndex})">
                        <label class="text-xs text-gray-600">Lumpsum (Fix)</label>
                    </div>
                </div>
            </div>
        `;
        
        typesList.appendChild(div);
        
        if (!isManual) {
            const select = div.querySelector('.type-select-air');
            select.value = typeId;
            // Do NOT call updatePriceFromSelect(select) here to preserve the historically saved DB price
        }
    };
