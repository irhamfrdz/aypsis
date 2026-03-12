
    // ============= LOLO SECTIONS MANAGEMENT =============
    let loloSectionCounter = 0;
    
    function initializeLoloSections() {
        if (!loloSectionsContainer) return;
        loloSectionsContainer.innerHTML = '';
        loloSectionCounter = 0;
        addLoloSection();
    }
    
    function clearAllLoloSections() {
        if (!loloSectionsContainer) return;
        loloSectionsContainer.innerHTML = '';
        loloSectionCounter = 0;
    }
    
    if (addLoloSectionBtn) {
        addLoloSectionBtn.addEventListener('click', function() {
            addLoloSection();
        });
    }

    if (addLoloSectionBottomBtn) {
        addLoloSectionBottomBtn.addEventListener('click', function() {
            addLoloSection();
        });
    }
    
    function addLoloSection() {
        if (!loloSectionsContainer) return;
        loloSectionCounter++;
        const sectionIndex = loloSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'lolo-section mb-6 p-4 border-2 border-indigo-200 rounded-lg bg-indigo-50';
        section.setAttribute('data-lolo-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });

        const lokasiOptions = `
            <option value="">-- Pilih Lokasi --</option>
            <option value="Jakarta">Jakarta</option>
            <option value="Batam">Batam</option>
            <option value="Pinang">Pinang</option>
        `;
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex} (LOLO)</h3>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeLoloSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="lolo_sections[${sectionIndex}][kapal]" class="lolo-kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="lolo_sections[${sectionIndex}][voyage]" class="lolo-voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" required disabled>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="lolo_sections[${sectionIndex}][voyage]" class="lolo-voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="lolo-voyage-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Lokasi <span class="text-red-500">*</span></label>
                    <select name="lolo_sections[${sectionIndex}][lokasi]" class="lolo-lokasi-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" required>
                        ${lokasiOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Vendor <span class="text-red-500">*</span></label>
                    <select name="lolo_sections[${sectionIndex}][vendor]" class="lolo-vendor-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" required disabled>
                        <option value="">-- Pilih Lokasi Terlebih Dahulu --</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-4 p-4 bg-white rounded-lg border-2 border-dashed border-indigo-300">
                <label class="block text-sm font-semibold text-gray-800 mb-2">Pilih Kontainer <span class="text-red-500">*</span></label>
                <p class="text-xs text-gray-400 mb-3"><i class="fas fa-info-circle mr-1"></i>Kontainer akan muncul setelah memilih No. Voyage</p>

                <!-- Loading indicator -->
                <div class="lolo-kontainer-loading hidden py-4 text-center text-gray-500 text-sm">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Memuat data kontainer...
                </div>

                <!-- Empty state -->
                <div class="lolo-kontainer-empty hidden py-4 text-center text-gray-400 text-sm">
                    <i class="fas fa-inbox text-3xl mb-2 block"></i>Tidak ada kontainer untuk voyage ini
                </div>

                <!-- Kontainer checklist -->
                <div class="lolo-kontainer-list space-y-2 max-h-60 overflow-y-auto pr-1"></div>

                <!-- Hidden inputs container -->
                <div class="lolo-kontainer-hidden-inputs"></div>
            </div>
            
            <div class="border-t pt-4 mt-2 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal LOLO <span class="text-xs text-indigo-500 font-normal">(otomatis)</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="lolo_sections[${sectionIndex}][subtotal]"
                                   class="lolo-subtotal-input w-full pl-10 pr-3 py-2 border border-indigo-200 rounded-lg bg-indigo-50 text-indigo-800 focus:ring-0 cursor-not-allowed"
                                   value="0" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PPH (2%)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="lolo_sections[${sectionIndex}][pph]"
                                   class="lolo-pph-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                   value="0">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PPN (11%)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="lolo_sections[${sectionIndex}][ppn]"
                                   class="lolo-ppn-input w-full pl-10 pr-3 py-2 border border-blue-200 rounded-lg bg-blue-50 text-blue-800 focus:ring-0 cursor-not-allowed"
                                   value="0" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Materai <span class="text-xs text-amber-500 font-normal">(total > Rp 5 jt)</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="lolo_sections[${sectionIndex}][biaya_materai]"
                                   class="lolo-materai-input w-full pl-10 pr-3 py-2 border border-amber-200 rounded-lg bg-amber-50 text-amber-800 focus:ring-0 cursor-not-allowed"
                                   value="0" readonly>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adjustment</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="lolo_sections[${sectionIndex}][adjustment]"
                                   class="lolo-adjustment-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                   value="0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Adjustment</label>
                        <input type="text" name="lolo_sections[${sectionIndex}][notes_adjustment]"
                               class="lolo-notes-adjustment-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                               placeholder="Contoh: Koreksi tarif, Biaya tambahan, dll">
                    </div>
                </div>
                    <div>
                        <label class="block text-lg font-bold text-gray-900 mb-1">Total Biaya (Nett)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-600 font-bold">Rp</span>
                            <input type="text" name="lolo_sections[${sectionIndex}][total_biaya]"
                                   class="lolo-total-biaya-input w-full pl-12 pr-3 py-3 border border-indigo-500 rounded-lg bg-indigo-100 text-indigo-900 font-bold text-lg focus:ring-0 cursor-not-allowed"
                                   value="0" readonly>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        loloSectionsContainer.appendChild(section);
        
        // Setup listeners for this section
        setupLoloSectionListeners(section, sectionIndex);
    }
    
    window.removeLoloSection = function(index) {
        const section = document.querySelector(`.lolo-section[data-lolo-section-index="${index}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllLoloSections();
        }
    };
    
    function setupLoloSectionListeners(section, index) {
        const kapalSelect = section.querySelector('.lolo-kapal-select');
        const voyageSelect = section.querySelector('.lolo-voyage-select');
        const voyageInput = section.querySelector('.lolo-voyage-input');
        const voyageManualBtn = section.querySelector('.lolo-voyage-manual-btn');
        const lokasiSelect = section.querySelector('.lolo-lokasi-select');
        const vendorSelect = section.querySelector('.lolo-vendor-select');
        const kontainerList = section.querySelector('.lolo-kontainer-list');
        const kontainerLoading = section.querySelector('.lolo-kontainer-loading');
        const kontainerEmpty = section.querySelector('.lolo-kontainer-empty');
        const pphInput = section.querySelector('.lolo-pph-input');
        const adjInput = section.querySelector('.lolo-adjustment-input');
        
        // Kapal change -> Fetch Voyages
        kapalSelect.addEventListener('change', function() {
            const kapalName = this.value;
            voyageSelect.innerHTML = '<option value="">-- Memuat Voyage... --</option>';
            voyageSelect.disabled = true;
            kontainerList.innerHTML = '';
            
            if (!kapalName) {
                voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
                return;
            }
            
            fetch(`/biaya-kapal/get-voyages/${encodeURIComponent(kapalName)}`)
                .then(response => response.json())
                .then(data => {
                    voyageSelect.innerHTML = '<option value="">-- Pilih No. Voyage --</option>';
                    if (data.voyages && data.voyages.length > 0) {
                        data.voyages.forEach(v => {
                            voyageSelect.innerHTML += `<option value="${v}">${v}</option>`;
                        });
                        voyageSelect.disabled = false;
                    } else {
                        voyageSelect.innerHTML = '<option value="">-- Tidak ada voyage ditemukan --</option>';
                        // Auto switch to manual if no voyage found
                        if (!voyageInput.classList.contains('hidden')) return;
                        voyageSelect.classList.add('hidden');
                        voyageInput.classList.remove('hidden');
                        voyageInput.disabled = false;
                    }
                })
                .catch(err => {
                    console.error('Error fetching voyages:', err);
                    voyageSelect.innerHTML = '<option value="">-- Gagal memuat voyage --</option>';
                });
        });
        
        // Voyage Manual Toggle
        voyageManualBtn.addEventListener('click', function() {
            if (voyageSelect.classList.contains('hidden')) {
                voyageSelect.classList.remove('hidden');
                voyageInput.classList.add('hidden');
                voyageInput.disabled = true;
                if (voyageSelect.options.length > 1) voyageSelect.disabled = false;
            } else {
                voyageSelect.classList.add('hidden');
                voyageInput.classList.remove('hidden');
                voyageInput.disabled = false;
                voyageSelect.disabled = true;
            }
        });
        
        // Voyage Change -> Fetch Kontainers
        const handleVoyageChange = function() {
            const voyage = this.value;
            const kapal = kapalSelect.value;
            
            kontainerList.innerHTML = '';
            if (!voyage || !kapal) return;
            
            kontainerLoading.classList.remove('hidden');
            kontainerEmpty.classList.add('hidden');
            
            fetch(`/biaya-kapal/get-containers?kapal=${encodeURIComponent(kapal)}&voyage=${encodeURIComponent(voyage)}`)
                .then(response => response.json())
                .then(data => {
                    kontainerLoading.classList.add('hidden');
                    if (data.containers && data.containers.length > 0) {
                        data.containers.forEach(c => {
                            const item = document.createElement('div');
                            item.className = 'flex items-center gap-3 p-2 hover:bg-gray-100 rounded border border-gray-100 transition';
                            const id = `lolo_c_${index}_${c.bl_id}_${c.nomor_kontainer.replace(/\s+/g, '_')}`;
                            item.innerHTML = `
                                <input type="checkbox" id="${id}" 
                                       class="lolo-kontainer-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                       data-bl-id="${c.bl_id}"
                                       data-nomor="${c.nomor_kontainer}"
                                       data-size="${c.size}">
                                <label for="${id}" class="flex-1 text-sm text-gray-700 cursor-pointer">
                                    <span class="font-bold">${c.nomor_kontainer}</span> 
                                    <span class="ml-2 px-2 py-0.5 bg-gray-200 rounded-full text-[10px] font-bold text-gray-600">${c.size}'</span>
                                    <span class="ml-auto text-[10px] text-gray-400 italic">${c.no_bl}</span>
                                </label>
                            `;
                            kontainerList.appendChild(item);
                            
                            // Listen for checkbox change
                            item.querySelector('input').addEventListener('change', () => calculateLoloSectionTotal(section));
                        });
                    } else {
                        kontainerEmpty.classList.remove('hidden');
                    }
                })
                .catch(err => {
                    console.error('Error fetching containers:', err);
                    kontainerLoading.classList.add('hidden');
                });
        };
        
        voyageSelect.addEventListener('change', handleVoyageChange);
        voyageInput.addEventListener('change', handleVoyageChange);

        // Lokasi Change -> Filter Vendor
        lokasiSelect.addEventListener('change', function() {
            const lokasi = this.value;
            vendorSelect.innerHTML = '<option value="">-- Pilih Vendor --</option>';
            
            if (!lokasi) {
                vendorSelect.disabled = true;
                return;
            }

            const vendors = [...new Set(pricelistLolosData.filter(p => p.lokasi === lokasi).map(p => p.vendor))];
            
            if (vendors.length > 0) {
                vendors.forEach(v => {
                    vendorSelect.innerHTML += `<option value="${v}">${v}</option>`;
                });
                vendorSelect.disabled = false;
            } else {
                vendorSelect.innerHTML = '<option value="">-- Tidak ada vendor di lokasi ini --</option>';
                vendorSelect.disabled = true;
            }
            
            calculateLoloSectionTotal(section);
        });

        // Vendor Change
        vendorSelect.addEventListener('change', function() {
            calculateLoloSectionTotal(section);
        });
        // PPH Input Change
        pphInput.addEventListener('input', function() {
            formatCurrencyInput(this);
            calculateLoloSectionTotal(section, true);
        });

        // Adjustment Input Change
        adjInput.addEventListener('input', function() {
            formatCurrencyInput(this);
            calculateLoloSectionTotal(section, true);
        });
    }

    function calculateLoloSectionTotal(section, skipPphAuto = false) {
        const index = section.getAttribute('data-lolo-section-index');
        const lokasi = section.querySelector('.lolo-lokasi-select').value;
        const vendor = section.querySelector('.lolo-vendor-select').value;
        const subtotalInput = section.querySelector('.lolo-subtotal-input');
        const pphInput = section.querySelector('.lolo-pph-input');
        const ppnInput = section.querySelector('.lolo-ppn-input');
        const materaiInput = section.querySelector('.lolo-materai-input');
        const totalInput = section.querySelector('.lolo-total-biaya-input');
        const adjInput = section.querySelector('.lolo-adjustment-input');
        const hiddenInputsContainer = section.querySelector('.lolo-kontainer-hidden-inputs');
        
        const checkboxes = section.querySelectorAll('.lolo-kontainer-checkbox:checked');
        
        let subtotal = 0;
        hiddenInputsContainer.innerHTML = '';
        
        if (lokasi && vendor) {
            checkboxes.forEach((cb, i) => {
                const blId = cb.getAttribute('data-bl-id');
                const nomor = cb.getAttribute('data-nomor');
                let size = cb.getAttribute('data-size');
                
                // Search for tarif in pricelist data
                // Size in master lolo is usually '20', '40', '45'
                // Normalize size from container data (sometimes '20FT', etc.)
                let normalizedSize = size || '20';
                if (normalizedSize === '2') {
                    normalizedSize = '20';
                }
                normalizedSize = normalizedSize.toString().replace(/[^0-9]/g, '');
                
                const pricelist = pricelistLolosData.find(p => 
                    p.lokasi === lokasi && 
                    p.vendor === vendor && 
                    p.size.toString() === normalizedSize
                );
                
                const tarif = pricelist ? parseFloat(pricelist.tarif) : 0;
                subtotal += tarif;
                
                // Add hidden inputs for form submission
                hiddenInputsContainer.innerHTML += `
                    <input type="hidden" name="lolo_sections[${index}][kontainer][${i}][bl_id]" value="${blId}">
                    <input type="hidden" name="lolo_sections[${index}][kontainer][${i}][nomor_kontainer]" value="${nomor}">
                    <input type="hidden" name="lolo_sections[${index}][kontainer][${i}][size]" value="${size}">
                    <input type="hidden" name="lolo_sections[${index}][kontainer][${i}][tarif]" value="${tarif}">
                `;
            });
        }

        // PPN 11% calculation
        let ppnValue = Math.round(subtotal * 0.11);

        // PPH 2% auto calculation
        let pphValue = 0;
        const rawPphInput = pphInput.value.replace(/\./g, '');
        
        if (skipPphAuto && rawPphInput !== '') {
            pphValue = parseFloat(rawPphInput) || 0;
        } else {
            pphValue = Math.round(subtotal * 0.02);
            pphInput.value = pphValue.toLocaleString('id-ID');
        }
        
        // Adjustment
        let adjValue = parseFloat(adjInput.value.replace(/\./g, '') || 0);

        // Materai if subtotal > 5,000,000
        let materai = 0;
        if (subtotal > 5000000) {
            materai = 10000;
        }
        
        const total = subtotal + ppnValue + materai + adjValue;
        
        subtotalInput.value = subtotal.toLocaleString('id-ID');
        ppnInput.value = ppnValue.toLocaleString('id-ID');
        materaiInput.value = materai.toLocaleString('id-ID');
        totalInput.value = total.toLocaleString('id-ID');
        
        calculateTotalFromAllLoloSections();
    }
    
    function calculateTotalFromAllLoloSections() {
        let totalNominal = 0;
        document.querySelectorAll('.lolo-total-biaya-input').forEach(input => {
            totalNominal += parseFloat(input.value.replace(/\./g, '')) || 0;
        });
        
        const selectedValue = jenisBiayaSelect.value;
        const selectedText = selectedJenisBiaya.nama || '';
        
        if (selectedValue === 'KB043' || selectedText.toLowerCase().includes('lolo')) {
            nominalInput.value = totalNominal.toLocaleString('id-ID');
            // Recalculate main total if needed
            calculateTotalBiaya();
        }
    }

    // Helper to format currency on input
    function formatCurrencyInput(input) {
        let value = input.value.replace(/\./g, '');
        if (value === '') value = '0';
        input.value = parseInt(value).toLocaleString('id-ID');
    }