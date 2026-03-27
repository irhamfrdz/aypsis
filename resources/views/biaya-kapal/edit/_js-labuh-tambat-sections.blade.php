    // ============= LABUH TAMBAT SECTIONS MANAGEMENT =============
    let labuhTambatSectionCounter = 0;
    const labuhTambatSectionsContainer = document.getElementById('labuh_tambat_sections_container');
    const addLabuhTambatSectionBtn = document.getElementById('add_labuh_tambat_section_btn');
    const addLabuhTambatSectionBottomBtn = document.getElementById('add_labuh_tambat_section_bottom_btn');

    function clearAllLabuhTambatSections() {
        if (!labuhTambatSectionsContainer) return;
        labuhTambatSectionsContainer.innerHTML = '';
        labuhTambatSectionCounter = 0;
        if (nominalInput) nominalInput.value = '';
    }

    if (addLabuhTambatSectionBtn) addLabuhTambatSectionBtn.addEventListener('click', () => addLabuhTambatSection());
    if (addLabuhTambatSectionBottomBtn) addLabuhTambatSectionBottomBtn.addEventListener('click', () => addLabuhTambatSection());

    window.updateLabuhTambatPriceFromSelect = function(select) {
        const container = select.closest('.flex.flex-col');
        const priceInput = container.querySelector('.price-input-labuh-tambat');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            const harga = selectedOption.getAttribute('data-harga');
            priceInput.value = harga || 0;
        } else {
            priceInput.value = 0;
        }
    };

    window.toggleLabuhTambatTypeInput = function(btn, sectionIndex) {
        const container = btn.closest('.flex.flex-col');
        const select = container.querySelector('.type-select-labuh-tambat');
        const manualInput = container.querySelector('.type-manual-input-labuh-tambat');
        const hiddenManual = container.querySelector('.hidden-type-manual');
        const priceInput = container.querySelector('.price-input-labuh-tambat');
        
        if (manualInput.classList.contains('hidden')) {
            // Switch to Manual
            select.classList.add('hidden');
            select.disabled = true;
            select.required = false;
            
            manualInput.classList.remove('hidden');
            manualInput.required = true;
            
            hiddenManual.disabled = false;
            
            btn.classList.add('bg-slate-200', 'text-slate-700');
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
            
            btn.classList.remove('bg-slate-200', 'text-slate-700');
            btn.classList.add('bg-gray-200', 'text-gray-600');
            btn.innerHTML = '<i class="fas fa-keyboard"></i>';
            btn.title = "Switch to Manual Input";
            
            priceInput.readOnly = true;
            priceInput.classList.add('bg-gray-100');
            priceInput.classList.remove('bg-white');
            
            // Restore price
            updateLabuhTambatPriceFromSelect(select);
        }
        
        calculateLabuhTambatSectionTotal(sectionIndex);
    };

    function addLabuhTambatSection() {
        if (!labuhTambatSectionsContainer) return;
        labuhTambatSectionCounter++;
        const sectionIndex = labuhTambatSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'labuh-tambat-section mb-6 p-4 border-2 border-slate-200 rounded-lg bg-slate-50';
        section.setAttribute('data-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });
        
        let vendorOptions = '<option value="">-- Pilih Vendor Labuh Tambat --</option>';
        const uniqueVendors = [...new Set(pricelistLabuhTambatData.map(item => item.nama_agen))];
        uniqueVendors.forEach(vendorName => {
            vendorOptions += `<option value="${vendorName}">${vendorName}</option>`;
        });

        let lokasiOptions = '<option value="">-- Pilih Lokasi --</option>';
        const uniqueLokasis = [...new Set(pricelistLabuhTambatData.map(item => item.lokasi))];
        uniqueLokasis.forEach(loc => {
            lokasiOptions += `<option value="${loc}">${loc}</option>`;
        });
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-md font-semibold text-slate-800">
                    <i class="fas fa-ship mr-2"></i>Kapal ${sectionIndex}
                </h4>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeLabuhTambatSection(${sectionIndex})" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs rounded transition"><i class="fas fa-times mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kapal</label>
                    <select name="labuh_tambat[${sectionIndex}][kapal]" class="kapal-select-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Voyage</label>
                    <div class="flex gap-2">
                        <select name="labuh_tambat[${sectionIndex}][voyage]" class="voyage-select-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500" disabled required>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="labuh_tambat[${sectionIndex}][voyage]" class="voyage-input-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="voyage-manual-btn-labuh-tambat px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vendor Labuh Tambat</label>
                    <select name="labuh_tambat[${sectionIndex}][vendor]" class="vendor-select-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500" required>
                        ${vendorOptions}
                    </select>
                </div>
                <div class="types-wrapper-labuh-tambat-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <div class="types-list-labuh-tambat space-y-2 mb-2">
                          <div class="flex flex-col gap-1 border p-2 rounded bg-gray-50 relative">
                                <div class="flex gap-2 w-full">
                                    <select name="labuh_tambat[${sectionIndex}][types][]" class="type-select-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500" disabled required onchange="updateLabuhTambatPriceFromSelect(this); calculateLabuhTambatSectionTotal(${sectionIndex})">
                                        <option value="">-- Pilih Vendor Terlebih Dahulu --</option>
                                    </select>
                                    
                                    <input type="hidden" name="labuh_tambat[${sectionIndex}][types][]" class="hidden-type-manual" value="MANUAL" disabled>
                                    
                                    <input type="text" name="labuh_tambat[${sectionIndex}][manual_names][]" class="type-manual-input-labuh-tambat hidden w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Nama Type Manual">
                                    
                                    <button type="button" class="type-toggle-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Switch Input (Master/Manual)" onclick="toggleLabuhTambatTypeInput(this, ${sectionIndex})">
                                        <i class="fas fa-keyboard"></i>
                                    </button>
                                </div>
                                
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="flex-grow">
                                        <label class="text-xs text-gray-500 block mb-1">Harga Satuan (Rp)</label>
                                        <input type="number" name="labuh_tambat[${sectionIndex}][custom_prices][]" class="price-input-labuh-tambat w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-slate-500" oninput="calculateLabuhTambatSectionTotal(${sectionIndex})">
                                    </div>
                                    <div class="w-1/4">
                                        <label class="text-xs text-gray-500 block mb-1">Kuantitas</label>
                                        <input type="number" step="0.01" min="0" name="labuh_tambat[${sectionIndex}][type_tonase][]" class="tonase-input-labuh-tambat w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-slate-500" placeholder="0" oninput="calculateLabuhTambatSectionTotal(${sectionIndex})">
                                    </div>
                                    <div class="flex items-end pb-1">
                                        <div class="flex items-center gap-2">
                                            <input type="hidden" name="labuh_tambat[${sectionIndex}][type_is_lumpsum][]" value="0" class="lumpsum-hidden">
                                            <input type="checkbox" class="lumpsum-checkbox rounded text-slate-600 focus:ring-slate-500 h-5 w-5" onchange="this.previousElementSibling.value = this.checked ? 1 : 0; calculateLabuhTambatSectionTotal(${sectionIndex})">
                                            <label class="text-xs text-gray-600">Lumpsum (Fix)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <button type="button" class="add-type-btn-labuh-tambat text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-1 rounded transition duration-200 flex items-center gap-1" disabled onclick="addTypeToLabuhTambatSection(${sectionIndex})">
                        <i class="fas fa-plus"></i> Tambah Type
                    </button>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <select name="labuh_tambat[${sectionIndex}][lokasi]" class="lokasi-select-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500">
                        ${lokasiOptions}
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sub Total</label>
                    <input type="text" class="sub-total-display w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" value="Rp 0" readonly>
                    <input type="hidden" name="labuh_tambat[${sectionIndex}][sub_total]" class="sub-total-value" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PPN (11%)</label>
                    <input type="text" class="ppn-display w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" value="Rp 0" readonly>
                    <input type="hidden" name="labuh_tambat[${sectionIndex}][ppn]" class="ppn-value" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Materai</label>
                    <input type="text" name="labuh_tambat[${sectionIndex}][biaya_materai]" class="biaya-materai-input-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500" placeholder="0" oninput="this.value = this.value.replace(/\\D/g, '').replace(/\\B(?=(\\d{3})+(?!\\d))/g, '.'); calculateLabuhTambatSectionTotal(${sectionIndex})">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grand Total</label>
                    <input type="text" class="grand-total-display w-full px-3 py-2 border border-gray-300 rounded-lg bg-emerald-50 font-semibold cursor-not-allowed" value="Rp 0" readonly>
                    <input type="hidden" name="labuh_tambat[${sectionIndex}][grand_total]" class="grand-total-value" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penerima</label>
                    <input type="text" name="labuh_tambat[${sectionIndex}][penerima]" class="penerima-input-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500" placeholder="Masukkan nama penerima">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                    <input type="text" name="labuh_tambat[${sectionIndex}][nomor_rekening]" class="nomor-rekening-input-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500" placeholder="Masukkan nomor rekening">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Referensi</label>
                    <input type="text" name="labuh_tambat[${sectionIndex}][nomor_referensi]" class="no-referensi-input-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500" placeholder="Masukkan No. Referensi">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Invoice Vendor</label>
                    <input type="date" name="labuh_tambat[${sectionIndex}][tanggal_invoice_vendor]" class="tanggal-invoice-vendor-input-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500">
                </div>
            </div>
        `;
        
        labuhTambatSectionsContainer.appendChild(section);
        
        // Setup listeners
        const kapalSelect = section.querySelector('.kapal-select-labuh-tambat');
        kapalSelect.addEventListener('change', function() {
            loadVoyagesForLabuhTambatSection(sectionIndex, this.value);
            
            // Refresh types when kapal changes
            const vendorSelect = section.querySelector('.vendor-select-labuh-tambat');
            if(vendorSelect && vendorSelect.value) {
                loadTypesForLabuhTambatVendor(sectionIndex, vendorSelect.value);
            }
        });
        
        const vendorSelect = section.querySelector('.vendor-select-labuh-tambat');
        vendorSelect.addEventListener('change', function() {
            loadTypesForLabuhTambatVendor(sectionIndex, this.value);
            calculateLabuhTambatSectionTotal(sectionIndex);
        });
        
        const lokasiSelect = section.querySelector('.lokasi-select-labuh-tambat');
        if (lokasiSelect) {
            lokasiSelect.addEventListener('change', function() {
                updateLabuhTambatVendorsForLokasi(sectionIndex, this.value);
            });
            updateLabuhTambatVendorsForLokasi(sectionIndex, lokasiSelect.value);
        }

        const voyageSelect = section.querySelector('.voyage-select-labuh-tambat');
        const voyageInput = section.querySelector('.voyage-input-labuh-tambat');
        const voyageManualBtn = section.querySelector('.voyage-manual-btn-labuh-tambat');

        voyageManualBtn.addEventListener('click', function() {
            if (voyageInput.classList.contains('hidden')) {
                voyageSelect.classList.add('hidden');
                voyageSelect.disabled = true;
                voyageInput.classList.remove('hidden');
                voyageInput.disabled = false;
                voyageInput.focus();
                this.classList.remove('bg-gray-200', 'text-gray-600');
                this.classList.add('bg-slate-200', 'text-slate-700');
                this.innerHTML = '<i class="fas fa-list"></i>';
            } else {
                voyageInput.classList.add('hidden');
                voyageInput.disabled = true;
                voyageSelect.classList.remove('hidden');
                const kapalSel = section.querySelector('.kapal-select-labuh-tambat');
                if (kapalSel && kapalSel.value) {
                    voyageSelect.disabled = false;
                } else {
                    voyageSelect.disabled = true;
                }
                this.classList.add('bg-gray-200', 'text-gray-600');
                this.classList.remove('bg-slate-200', 'text-slate-700');
                this.innerHTML = '<i class="fas fa-keyboard"></i>';
            }
        });
        
        calculateLabuhTambatSectionTotal(sectionIndex);
        
        return section;
    }
    
    window.removeLabuhTambatSection = function(sectionIndex) {
        const section = document.querySelector(`.labuh-tambat-section[data-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllLabuhTambatSections();
        }
    };
    
    function loadVoyagesForLabuhTambatSection(sectionIndex, kapalNama) {
        const section = document.querySelector(`.labuh-tambat-section[data-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.voyage-select-labuh-tambat');
        
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
                voyageSelect.disabled = false;
                let options = '<option value="">-- Pilih Voyage --</option>';
                options += '<option value="DOCK">DOCK</option>';
                if (data && data.success && data.voyages && data.voyages.length > 0) {
                    data.voyages.forEach(voyage => {
                        options += `<option value="${voyage}">${voyage}</option>`;
                    });
                }
                voyageSelect.innerHTML = options;
            })
            .catch(error => {
                console.error('Error loading voyages:', error);
                voyageSelect.disabled = false;
                voyageSelect.innerHTML = '<option value="">-- Pilih Voyage --</option><option value="DOCK">DOCK</option>';
            });
    }
    
    function loadTypesForLabuhTambatVendor(sectionIndex, vendorName) {
        const section = document.querySelector(`.labuh-tambat-section[data-section-index="${sectionIndex}"]`);
        const typesList = section.querySelector('.types-list-labuh-tambat');
        const typeContainers = typesList.querySelectorAll('.flex.flex-col');
        const addTypeBtn = section.querySelector('.add-type-btn-labuh-tambat');
        
        if (!vendorName) {
            typeContainers.forEach(container => {
                const select = container.querySelector('.type-select-labuh-tambat');
                select.disabled = true;
                select.innerHTML = '<option value="">-- Pilih Vendor Terlebih Dahulu --</option>';
            });
            addTypeBtn.disabled = true;
            typesList.dataset.options = '<option value="">-- Pilih Vendor Terlebih Dahulu --</option>';
            return;
        }
        
        const lokasiSelect = section.querySelector('.lokasi-select-labuh-tambat');
        const selectedLokasi = lokasiSelect ? (lokasiSelect.value || '') : '';

        // Get selected kapal in this section
        const kapalSelect = section.querySelector('.kapal-select-labuh-tambat');
        const selectedKapal = kapalSelect ? (kapalSelect.value || '') : '';

        const vendorTypes = pricelistLabuhTambatData.filter(item => 
            item.nama_agen === vendorName && 
            (selectedLokasi === '' || item.lokasi === selectedLokasi) &&
            (selectedKapal === '' || item.nama_kapal === selectedKapal || !item.nama_kapal)
        );
        
        let options = '<option value="">-- Pilih Type --</option>';
        if (vendorTypes.length > 0) {
            vendorTypes.forEach(type => {
                options += `<option value="${type.id}" data-keterangan="${type.keterangan}" data-harga="${type.harga}">${type.keterangan} - Rp ${parseInt(type.harga).toLocaleString('id-ID')}</option>`;
            });
            
            typeContainers.forEach(container => {
                const select = container.querySelector('.type-select-labuh-tambat');
                const currentValue = select.value;
                select.disabled = false;
                select.innerHTML = options;
                if (currentValue) select.value = currentValue;
                if (!container.querySelector('.type-manual-input-labuh-tambat').required) {
                    updateLabuhTambatPriceFromSelect(select);
                }
            });
            addTypeBtn.disabled = false;
        } else {
            options = '<option value="">Tidak ada type tersedia</option>';
            typeContainers.forEach(container => {
                const select = container.querySelector('.type-select-labuh-tambat');
                select.disabled = true;
                select.innerHTML = options;
            });
            addTypeBtn.disabled = true;
        }
        typesList.dataset.options = options;
    }

    function addTypeToLabuhTambatSection(sectionIndex) {
        const section = document.querySelector(`.labuh-tambat-section[data-section-index="${sectionIndex}"]`);
        const typesList = section.querySelector('.types-list-labuh-tambat');
        const options = typesList.dataset.options || '<option value="">-- Pilih Type --</option>';
        
        const div = document.createElement('div');
        div.className = 'flex flex-col gap-1 border p-2 rounded bg-gray-50 relative';
        div.innerHTML = `
            <div class="flex gap-2 w-full">
                <select name="labuh_tambat[${sectionIndex}][types][]" class="type-select-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500" onchange="updateLabuhTambatPriceFromSelect(this); calculateLabuhTambatSectionTotal(${sectionIndex})">
                    ${options}
                </select>
                <input type="hidden" name="labuh_tambat[${sectionIndex}][types][]" class="hidden-type-manual" value="MANUAL" disabled>
                <input type="text" name="labuh_tambat[${sectionIndex}][manual_names][]" class="type-manual-input-labuh-tambat hidden w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Nama Type Manual">
                <button type="button" class="type-toggle-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Switch Input (Master/Manual)" onclick="toggleLabuhTambatTypeInput(this, ${sectionIndex})">
                    <i class="fas fa-keyboard"></i>
                </button>
                <button type="button" class="text-red-500 hover:text-red-700 ml-1" onclick="this.closest('.flex-col').remove(); calculateLabuhTambatSectionTotal(${sectionIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="flex items-center gap-2 mt-1">
                <div class="flex-grow">
                    <label class="text-xs text-gray-500 block mb-1">Harga Satuan (Rp)</label>
                    <input type="number" name="labuh_tambat[${sectionIndex}][custom_prices][]" class="price-input-labuh-tambat w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-slate-500" placeholder="0" oninput="calculateLabuhTambatSectionTotal(${sectionIndex})">
                </div>
                <div class="w-1/4">
                    <label class="text-xs text-gray-500 block mb-1">Kuantitas</label>
                    <input type="number" step="0.01" min="0" name="labuh_tambat[${sectionIndex}][type_tonase][]" class="tonase-input-labuh-tambat w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-slate-500" placeholder="0" oninput="calculateLabuhTambatSectionTotal(${sectionIndex})">
                </div>
                <div class="flex items-end pb-1">
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="labuh_tambat[${sectionIndex}][type_is_lumpsum][]" value="0" class="lumpsum-hidden">
                        <input type="checkbox" class="lumpsum-checkbox rounded text-slate-600 focus:ring-slate-500 h-5 w-5" onchange="this.previousElementSibling.value = this.checked ? 1 : 0; calculateLabuhTambatSectionTotal(${sectionIndex})">
                        <label class="text-xs text-gray-600">Lumpsum (Fix)</label>
                    </div>
                </div>
            </div>
        `;
        typesList.appendChild(div);
    }

    function updateLabuhTambatVendorsForLokasi(sectionIndex, lokasi) {
        const section = document.querySelector(`.labuh-tambat-section[data-section-index="${sectionIndex}"]`);
        if (!section) return;
        const vendorSelect = section.querySelector('.vendor-select-labuh-tambat');
        const typesList = section.querySelector('.types-list-labuh-tambat');
        const typeSelects = typesList.querySelectorAll('.type-select-labuh-tambat');
        const addTypeBtn = section.querySelector('.add-type-btn-labuh-tambat');

        let vendors = [];
        if (lokasi && lokasi !== '') {
            vendors = [...new Set(pricelistLabuhTambatData.filter(item => (item.lokasi || '') === lokasi).map(i => i.nama_agen))];
        } else {
            vendors = [...new Set(pricelistLabuhTambatData.map(i => i.nama_agen))];
        }

        if (vendors.length > 0) {
            vendorSelect.disabled = false;
            let options = '<option value="">-- Pilih Vendor Labuh Tambat --</option>';
            vendors.forEach(v => options += `<option value="${v}">${v}</option>`);
            vendorSelect.innerHTML = options;
        } else {
            vendorSelect.disabled = true;
            vendorSelect.innerHTML = '<option value="">-- Tidak ada vendor di lokasi ini --</option>';
        }

        if (typeSelects.length > 0) {
            typeSelects.forEach(select => {
                select.disabled = true;
                select.innerHTML = '<option value="">-- Pilih Vendor Terlebih Dahulu --</option>';
            });
            addTypeBtn.disabled = true;
            typesList.dataset.options = '<option value="">-- Pilih Vendor Terlebih Dahulu --</option>';
        }
    }
    
    function calculateLabuhTambatSectionTotal(sectionIndex) {
        const section = document.querySelector(`.labuh-tambat-section[data-section-index="${sectionIndex}"]`);
        const typeContainers = section.querySelectorAll('.types-list-labuh-tambat > div');
        const subTotalDisplay = section.querySelector('.sub-total-display');
        const subTotalValue = section.querySelector('.sub-total-value');
        const ppnDisplay = section.querySelector('.ppn-display');
        const ppnValue = section.querySelector('.ppn-value');
        const materaiInput = section.querySelector('.biaya-materai-input-labuh-tambat');
        const grandTotalDisplay = section.querySelector('.grand-total-display');
        const grandTotalValue = section.querySelector('.grand-total-value');
        
        let totalCost = 0;
        let taxableCost = 0;
        typeContainers.forEach(container => {
            const select = container.querySelector('.type-select-labuh-tambat');
            const manualInput = container.querySelector('.type-manual-input-labuh-tambat');
            const checkbox = container.querySelector('.lumpsum-checkbox');
            if (select) {
                const priceInput = container.querySelector('.price-input-labuh-tambat');
                const tonaseInput = container.querySelector('.tonase-input-labuh-tambat');
                const harga = parseFloat(priceInput.value) || 0;
                let selectedKuantitas = 0;
                if (tonaseInput && tonaseInput.value !== "") {
                    selectedKuantitas = parseFloat(tonaseInput.value) || 0;
                }
                const isLumpsum = checkbox ? checkbox.checked : false;
                const itemCost = isLumpsum ? harga : (harga * selectedKuantitas);
                totalCost += itemCost;

                // Identify if this is a taxable Fuel Surcharge item
                let typeName = "";
                if (!select.classList.contains('hidden')) {
                    typeName = select.options[select.selectedIndex] ? select.options[select.selectedIndex].text : "";
                } else if (manualInput) {
                    typeName = manualInput.value;
                }

                if (typeName.toLowerCase().includes('fuel surcharge')) {
                    taxableCost += itemCost;
                }
            }
        });

        let subTotal = Math.round(totalCost);
        const ppn = Math.round(taxableCost * 0.11);
        const materai = materaiInput ? (parseFloat(materaiInput.value.replace(/\./g, '')) || 0) : 0;
        const grandTotal = subTotal + ppn + materai;
        
        subTotalDisplay.value = subTotal > 0 ? `Rp ${subTotal.toLocaleString('id-ID')}` : 'Rp 0';
        subTotalValue.value = subTotal;
        if (ppnDisplay) ppnDisplay.value = ppn > 0 ? `Rp ${ppn.toLocaleString('id-ID')}` : 'Rp 0';
        if (ppnValue) ppnValue.value = ppn;
        if (grandTotalDisplay) grandTotalDisplay.value = grandTotal > 0 ? `Rp ${grandTotal.toLocaleString('id-ID')}` : 'Rp 0';
        if (grandTotalValue) grandTotalValue.value = grandTotal;
        
        calculateTotalFromAllLabuhTambatSections();
    }
    
    function calculateTotalFromAllLabuhTambatSections() {
        let totalGrandTotal = 0;
        document.querySelectorAll('.labuh-tambat-section').forEach(section => {
            const grandTotalVal = section.querySelector('.grand-total-value');
            totalGrandTotal += parseFloat(grandTotalVal ? grandTotalVal.value : 0) || 0;
        });

        // Add adjustment if exists
        const adjustmentInput = document.getElementById('labuh_tambat_adjustment');
        if (adjustmentInput && adjustmentInput.value) {
            const adjustment = parseFloat(adjustmentInput.value.replace(/\./g, '')) || 0;
            totalGrandTotal += adjustment;
        }

        // Update the summary display
        const allSectionsTotalDisplay = document.getElementById('labuh_tambat_all_sections_total');
        if (allSectionsTotalDisplay) {
            allSectionsTotalDisplay.textContent = totalGrandTotal !== 0 ? `Rp ${totalGrandTotal.toLocaleString('id-ID')}` : 'Rp 0';
        }

        if (totalGrandTotal !== 0) {
            if (nominalInput) nominalInput.value = totalGrandTotal.toLocaleString('id-ID');
        } else {
            if (nominalInput) nominalInput.value = '';
        }
    }

    window.addTypeToLabuhTambatSectionWithValue = function(sectionIndex, typeId, label, lumpsum, q, h) {
        const section = document.querySelector(`.labuh-tambat-section[data-section-index="${sectionIndex}"]`);
        const typesList = section.querySelector('.types-list-labuh-tambat');
        addTypeToLabuhTambatSection(sectionIndex);
        const last = typesList.lastElementChild;
        const isMan = typeId === 'MANUAL' || !typeId;
        if (isMan) {
            const btn = last.querySelector('.type-toggle-btn');
            toggleLabuhTambatTypeInput(btn, sectionIndex);
            last.querySelector('.type-manual-input-labuh-tambat').value = label || '';
            last.querySelector('.price-input-labuh-tambat').value = h;
        } else {
            const sel = last.querySelector('.type-select-labuh-tambat');
            sel.value = typeId;
            // Set the saved price from DB instead of re-reading from pricelist
            last.querySelector('.price-input-labuh-tambat').value = h;
        }
        last.querySelector('.tonase-input-labuh-tambat').value = q;
        const cb = last.querySelector('.lumpsum-checkbox');
        const lumpsumVal = (lumpsum == 1 || lumpsum === true || lumpsum === 'true') ? 1 : 0;
        cb.checked = lumpsumVal == 1;
        cb.previousElementSibling.value = lumpsumVal;
        calculateLabuhTambatSectionTotal(sectionIndex);
    };
