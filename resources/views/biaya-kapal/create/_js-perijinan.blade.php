    // ============= BIAYA PERIJINAN LOGIC (MULTI-SECTION) =============
    let perijinanSectionCounter = 0;
    // Variables already declared in _js-jenis-biaya.blade.php:
    // perijinanWrapper, perijinanSectionsContainer, addPerijinanSectionBtn, addPerijinanSectionBottomBtn




    function initializePerijinanSections() {
        if (perijinanSectionsContainer) perijinanSectionsContainer.innerHTML = '';
        perijinanSectionCounter = 0;
        addPerijinanSection();
    }

    function clearAllPerijinanSections() {
        if (perijinanSectionsContainer) perijinanSectionsContainer.innerHTML = '';
        perijinanSectionCounter = 0;
    }

    function addPerijinanSection(initialData = null) {
        perijinanSectionCounter++;
        const idx = perijinanSectionCounter;


            const section = document.createElement('div');
        section.className = 'perijinan-section mb-6 p-4 border-2 border-blue-200 rounded-lg bg-blue-50 shadow-sm';
        section.setAttribute('data-section-index', idx);
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4 border-b border-blue-100 pb-2">
                <h4 class="text-md font-semibold text-gray-800 uppercase tracking-wider">Perijinan ${idx}</h4>
                ${idx > 1 ? `<button type="button" onclick="removePerijinanSection(this)" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-lg transition flex items-center gap-1">
                    <i class="fas fa-trash-alt"></i> <span>Hapus</span>
                </button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Row 1: Kapal & Voyage -->
                <div class="space-y-1 searchable-kapal-container" data-section-index="${idx}">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <div class="relative perijinan-kapal-wrapper">
                        <input type="hidden" name="perijinan_sections[${idx}][nama_kapal]" class="perijinan-kapal-hidden" required>
                        <div class="relative">
                            <input type="text" 
                                   class="perijinan-kapal-search w-full px-3 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white" 
                                   placeholder="Cari Kapal..."
                                   autocomplete="off">
                            <div class="selected-kapal-display hidden absolute inset-0 flex items-center px-3 py-2 bg-white border border-blue-200 rounded-lg cursor-pointer">
                                <span class="selected-kapal-text text-sm text-gray-800 font-medium truncate mr-6"></span>
                                <i class="fas fa-times absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 clear-kapal-btn p-1"></i>
                            </div>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-blue-300 search-icon-perijinan">
                                <i class="fas fa-search text-xs"></i>
                            </div>
                        </div>
                        <div class="perijinan-kapal-dropdown hidden absolute z-[60] w-full mt-1 bg-white border border-blue-100 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                            <!-- Options injected by JS -->
                        </div>
                    </div>
                </div>
                
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <div class="flex-grow relative perijinan-select-container">
                            <select name="perijinan_sections[${idx}][no_voyage]" 
                                    id="perijinan_voyage_${idx}" 
                                    class="perijinan-voyage-select w-full px-3 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white appearance-none disabled:bg-gray-50"
                                    disabled required>
                                <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-blue-300">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                        <input type="text" 
                               name="perijinan_sections[${idx}][no_voyage]" 
                               class="perijinan-voyage-manual-input hidden w-full px-3 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white" 
                               placeholder="Ketik No. Voyage..." disabled>
                        <button type="button" class="perijinan-voyage-toggle-btn px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Row 2: Vendor & Lokasi -->
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Vendor</label>
                    <input type="text" 
                           name="perijinan_sections[${idx}][vendor]" 
                           class="w-full px-3 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white" 
                           placeholder="Nama Vendor...">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Lokasi</label>
                    <div class="relative">
                        <select name="perijinan_sections[${idx}][lokasi]" 
                                class="w-full px-3 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white appearance-none">
                            <option value="">-- Pilih Lokasi --</option>
                            ${[...new Set(pricelistPerijinansData.filter(item => item.lokasi).map(item => item.lokasi))].sort().map(loc => `<option value="${loc}">${loc}</option>`).join('')}
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-blue-300">
                            <i class="fas fa-map-marker-alt text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Row: Detail Perijinan -->
                <div class="md:col-span-2 space-y-2 mt-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Detail Perijinan</label>
                    <div id="perijinan_items_container_${idx}" class="space-y-2 perijinan-items-container">
                        <!-- Items will be added here -->
                    </div>
                    <button type="button" 
                            onclick="addPerijinanItemToSection(${idx})" 
                            class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded-lg transition shadow-sm">
                        <i class="fas fa-plus mr-1.5"></i> Tambah Item
                    </button>
                </div>

                <!-- Row 6: Penerima & Nomor Rekening -->
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Penerima</label>
                    <input type="text" 
                           name="perijinan_sections[${idx}][penerima]" 
                           class="w-full px-3 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white" 
                           placeholder="Nama penerima pembayaran...">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Rekening</label>
                    <input type="text" 
                           name="perijinan_sections[${idx}][nomor_rekening]" 
                           class="w-full px-3 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white" 
                           placeholder="Nomor rekening bank...">
                </div>

                <!-- Row 7: Nomor Referensi & Tanggal Invoice -->
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Referensi</label>
                    <input type="text" 
                           name="perijinan_sections[${idx}][nomor_referensi]" 
                           class="w-full px-3 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white" 
                           placeholder="No. Ref / No. Invoice Vendor...">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Invoice Vendor</label>
                    <input type="date" 
                           name="perijinan_sections[${idx}][tanggal_invoice_vendor]" 
                           class="w-full px-3 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white shadow-sm">
                </div>

                <!-- Row 8: Keterangan -->
                <div class="md:col-span-2 space-y-1 mt-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan Tambahan</label>
                    <textarea name="perijinan_sections[${idx}][keterangan]" 
                               rows="2" 
                               class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white" 
                               placeholder="Masukkan detail tambahan jika ada..."></textarea>
                </div>
            </div>

            <!-- Nominal Per Kapal Display (Matched with Buruh style) -->
            <div class="mt-4 p-3 bg-white border border-blue-300 rounded-lg shadow-inner">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-700 uppercase tracking-widest">Nominal Perijinan ${idx}:</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-blue-400">Rp</span>
                        <span id="perijinan_jumlah_display_${idx}" class="text-lg font-bold text-blue-600">0</span>
                        <input type="hidden" name="perijinan_sections[${idx}][jumlah_biaya]" id="perijinan_jumlah_${idx}" class="perijinan-base-input" value="0">
                        <input type="hidden" name="perijinan_sections[${idx}][sub_total]" class="perijinan-subtotal-value sub-total-value" value="0">
                        <input type="hidden" name="perijinan_sections[${idx}][grand_total]" class="perijinan-grandtotal-value grand-total-value" value="0">
                    </div>
                </div>
            </div>
        `;

        perijinanSectionsContainer.appendChild(section);
        
        // Initialize searchable kapal for this section
        initSearchableKapalForPerijinan(section, idx);

        if (initialData) {
            // Populate basic fields
            if (initialData.nama_kapal) {
                const hiddenInput = section.querySelector('.perijinan-kapal-hidden');
                const display = section.querySelector('.selected-kapal-display');
                const displayText = section.querySelector('.selected-kapal-text');
                const searchInput = section.querySelector('.perijinan-kapal-search');
                
                hiddenInput.value = initialData.nama_kapal;
                displayText.textContent = initialData.nama_kapal;
                display.classList.remove('hidden');
                searchInput.classList.add('hidden');
                
                // Load voyages
                window.loadVoyagesForPerijinanSection(idx, initialData.nama_kapal);
            }
            
            if (initialData.no_voyage) {
                // We need to wait for voyage options to load, or just set it
                const voySel = section.querySelector('.perijinan-voyage-select');
                voySel.innerHTML = `<option value="${initialData.no_voyage}">${initialData.no_voyage}</option>`;
                voySel.value = initialData.no_voyage;
                voySel.disabled = false;
            }
            
            section.querySelector(`[name="perijinan_sections[${idx}][nomor_referensi]"]`).value = initialData.nomor_referensi || '';
            section.querySelector(`[name="perijinan_sections[${idx}][vendor]"]`).value = initialData.vendor || '';
            section.querySelector(`[name="perijinan_sections[${idx}][lokasi]"]`).value = initialData.lokasi || '';
            section.querySelector(`[name="perijinan_sections[${idx}][penerima]"]`).value = initialData.penerima || '';
            section.querySelector(`[name="perijinan_sections[${idx}][nomor_rekening]"]`).value = initialData.nomor_rekening || '';
            section.querySelector(`[name="perijinan_sections[${idx}][tanggal_invoice_vendor]"]`).value = initialData.tanggal_invoice_vendor || '';
            section.querySelector(`[name="perijinan_sections[${idx}][keterangan]"]`).value = initialData.keterangan || '';
            
            // Populate items
            if (initialData.items && initialData.items.length > 0) {
                initialData.items.forEach(item => {
                    addPerijinanItemToSection(idx, item);
                });
            } else {
                addPerijinanItemToSection(idx);
            }
        } else {
            // Add first perijinan item automatically for new section
            addPerijinanItemToSection(idx);
        }

        const voyageSelect = section.querySelector('.perijinan-voyage-select');
        const voyageManualInput = section.querySelector('.perijinan-voyage-manual-input');
        const voyageToggleBtn = section.querySelector('.perijinan-voyage-toggle-btn');

        // Toggle Manual Voyage logic (similar to air)
        voyageToggleBtn.addEventListener('click', function() {
            if (voyageManualInput.classList.contains('hidden')) {
                // Switch to manual input
                voyageSelect.closest('.perijinan-select-container').classList.add('hidden');
                voyageSelect.disabled = true;
                
                voyageManualInput.classList.remove('hidden');
                voyageManualInput.disabled = false;
                voyageManualInput.focus();
                
                this.classList.remove('bg-indigo-100', 'text-indigo-600');
                this.classList.add('bg-indigo-600', 'text-white');
                this.innerHTML = '<i class="fas fa-list text-xs"></i>';
            } else {
                // Switch to select list
                voyageManualInput.classList.add('hidden');
                voyageManualInput.disabled = true;
                
                voyageSelect.closest('.perijinan-select-container').classList.remove('hidden');
                
                // Only enable select if there are options and not just 'Loading...'
                if (voyageSelect.innerHTML.indexOf('option') > -1 && voyageSelect.innerHTML.indexOf('Loading') === -1) {
                    voyageSelect.disabled = false;
                }
                
                this.classList.add('bg-indigo-100', 'text-indigo-600');
                this.classList.remove('bg-indigo-600', 'text-white');
                this.innerHTML = '<i class="fas fa-keyboard text-xs"></i>';
            }
        });
    }

    function initSearchableKapalForPerijinan(section, idx) {
        const searchInput = section.querySelector('.perijinan-kapal-search');
        const dropdown = section.querySelector('.perijinan-kapal-dropdown');
        const hiddenInput = section.querySelector('.perijinan-kapal-hidden');
        const display = section.querySelector('.selected-kapal-display');
        const displayText = section.querySelector('.selected-kapal-text');
        const clearBtn = section.querySelector('.clear-kapal-btn');
        const searchIcon = section.querySelector('.search-icon-perijinan');

        function renderOptions(filter = '') {
            let html = '';
            const filtered = allKapalsData.filter(k => 
                k.nama_kapal.toLowerCase().includes(filter.toLowerCase())
            );

            if (filtered.length === 0) {
                html = '<div class="px-4 py-3 text-sm text-gray-500 italic text-center">Kapal tidak ditemukan</div>';
            } else {
                filtered.forEach(kapal => {
                    html += `
                        <div class="kapal-option px-4 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-indigo-900 border-b border-indigo-50 last:border-0 transition-colors" 
                             data-value="${kapal.nama_kapal}">
                            <div class="font-medium">${kapal.nama_kapal}</div>
                        </div>
                    `;
                });
            }
            dropdown.innerHTML = html;

            // Add click listeners to options
            dropdown.querySelectorAll('.kapal-option').forEach(opt => {
                opt.addEventListener('click', function() {
                    const val = this.getAttribute('data-value');
                    selectKapal(val);
                });
            });
        }

        function selectKapal(val) {
            hiddenInput.value = val;
            displayText.textContent = val;
            display.classList.remove('hidden');
            searchInput.classList.add('hidden');
            searchIcon.classList.add('hidden');
            dropdown.classList.add('hidden');
            
            // Trigger voyage loading
            loadVoyagesForPerijinanSection(idx, val);
            
            // Trigger change event for validation
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function clearSelection() {
            hiddenInput.value = '';
            searchInput.value = '';
            display.classList.add('hidden');
            searchInput.classList.remove('hidden');
            searchIcon.classList.remove('hidden');
            renderOptions();
            
            // Reset voyage list
            loadVoyagesForPerijinanSection(idx, '');
            
            // Trigger change event
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        searchInput.addEventListener('focus', () => {
            renderOptions(searchInput.value);
            dropdown.classList.remove('hidden');
        });

        searchInput.addEventListener('input', () => {
            renderOptions(searchInput.value);
        });

        clearBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            clearSelection();
        });

        display.addEventListener('click', () => {
            searchInput.classList.remove('hidden');
            searchIcon.classList.remove('hidden');
            display.classList.add('hidden');
            searchInput.focus();
        });

        // Hide dropdown on click outside
        document.addEventListener('click', (e) => {
            if (!section.querySelector('.perijinan-kapal-wrapper').contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Initialize options
        renderOptions();
    }

    window.removePerijinanSection = function(btn) {
        const section = btn.closest('.perijinan-section');
        if (section) {
            // Cleanup section
            section.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                section.remove();
                calculateGrandTotalPerijinan();
            }, 300);
        }
    };

    window.formatPerijinanBiaya = function(input) {
        let value = input.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        input.value = value;

        const section = input.closest('.perijinan-section');
        if (section) {
            const idx = section.getAttribute('data-section-index');
            
            // Update display span
            const displaySpan = document.getElementById(`perijinan_jumlah_display_${idx}`);
            if (displaySpan) {
                displaySpan.textContent = value || '0';
            }

            // Get base cost value in this section
            const baseVal = parseInt(section.querySelector('.perijinan-base-input').value.replace(/\./g, '') || 0);
            
            const subtotal = baseVal;
            const pph = 0;
            const grandTotal = baseVal;

            // Update hidden inputs
            const subtotalValue = section.querySelector('.perijinan-subtotal-value');
            const pphValue = section.querySelector('.perijinan-pph-value');
            const grandtotalValue = section.querySelector('.perijinan-grandtotal-value');

            if (subtotalValue) subtotalValue.value = subtotal;
            if (pphValue) pphValue.value = pph;
            if (grandtotalValue) grandtotalValue.value = grandTotal;
        }
        
        calculateGrandTotalPerijinan();
    };

    function calculateGrandTotalPerijinan() {
        let totalSum = 0;
        document.querySelectorAll('.perijinan-section').forEach(section => {
            const grandtotalValue = section.querySelector('.perijinan-grandtotal-value');
            if (grandtotalValue) {
                totalSum += parseInt(grandtotalValue.value || 0);
            }
        });
        
        if (typeof nominalInput !== 'undefined' && nominalInput) {
            nominalInput.value = totalSum.toLocaleString('id-ID');
            nominalInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    window.addPerijinanItemToSection = function(sectionIndex, initialItem = null) {
        const container = document.getElementById(`perijinan_items_container_${sectionIndex}`);
        if (!container) return;
        
        const itemIdx = container.children.length;
        
        let perijinanOptions = '<option value="">-- Pilih Jenis Perijinan --</option>';
        pricelistPerijinansData.forEach(p => {
            const selected = (initialItem && initialItem.pricelist_perijinan_id == p.id) ? 'selected' : '';
            perijinanOptions += `<option value="${p.id}" data-tarif="${p.tarif}" ${selected}>${p.nama}</option>`;
        });
        
        const isManual = initialItem && !initialItem.pricelist_perijinan_id && initialItem.nama_perijinan;
        const tarifVal = initialItem ? parseInt(initialItem.tarif).toLocaleString('id-ID') : '0';
        
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2 mb-2 p-2 bg-white border border-blue-100 rounded shadow-sm';
        div.innerHTML = `
            <div class="flex-grow">
                <select name="perijinan_sections[${sectionIndex}][items][${itemIdx}][pricelist_perijinan_id]" 
                        class="perijinan-item-select w-full px-2 py-1.5 border border-blue-200 rounded text-sm focus:ring-2 focus:ring-blue-500 bg-white"
                        onchange="handlePerijinanItemChange(this, ${sectionIndex})">
                    ${perijinanOptions}
                    <option value="MANUAL" ${isManual ? 'selected' : ''}>-- Input Manual --</option>
                </select>
            </div>
            <div class="perijinan-manual-name-wrapper ${isManual ? '' : 'hidden'} flex-grow">
                <input type="text" name="perijinan_sections[${sectionIndex}][items][${itemIdx}][nama_perijinan]" 
                       class="perijinan-item-manual-name w-full px-2 py-1.5 border border-blue-200 rounded text-sm focus:ring-2 focus:ring-blue-500 bg-white"
                       placeholder="Nama Manual..."
                       value="${initialItem ? (initialItem.nama_perijinan || '') : ''}">
            </div>
            <div class="w-32 relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
                    <span class="text-blue-400 font-bold text-[10px]">Rp</span>
                </div>
                <input type="text" name="perijinan_sections[${sectionIndex}][items][${itemIdx}][tarif]" 
                       class="perijinan-item-tarif w-full pl-7 pr-2 py-1.5 border border-blue-200 rounded text-sm focus:ring-2 focus:ring-blue-500 bg-white font-medium"
                       placeholder="0"
                       value="${tarifVal}"
                       ${(!isManual && initialItem) ? 'readonly' : ''}
                       oninput="handlePerijinanTarifInput(this, ${sectionIndex})">
            </div>
            <button type="button" onclick="removePerijinanItem(this, ${sectionIndex})" 
                    class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded transition-colors shadow-sm">
                <i class="fas fa-trash-alt text-xs"></i>
            </button>
        `;
        
        container.appendChild(div);
        if (initialItem) {
            calculatePerijinanSectionTotalFromItems(sectionIndex);
        }
    };

    window.handlePerijinanItemChange = function(select, sectionIndex) {
        const wrapper = select.closest('.flex');
        const manualWrapper = wrapper.querySelector('.perijinan-manual-name-wrapper');
        const manualInput = wrapper.querySelector('.perijinan-item-manual-name');
        const tarifInput = wrapper.querySelector('.perijinan-item-tarif');
        
        if (select.value === 'MANUAL') {
            manualWrapper.classList.remove('hidden');
            manualInput.required = true;
            tarifInput.value = '';
            tarifInput.readOnly = false;
        } else {
            manualWrapper.classList.add('hidden');
            manualInput.required = false;
            manualInput.value = '';
            
            const option = select.options[select.selectedIndex];
            const tarif = option.dataset.tarif || 0;
            tarifInput.value = parseInt(tarif).toLocaleString('id-ID');
            tarifInput.readOnly = true;
        }
        
        calculatePerijinanSectionTotalFromItems(sectionIndex);
    };

    window.handlePerijinanTarifInput = function(input, sectionIndex) {
        // Format as Indonesian currency
        let val = input.value.replace(/\D/g, "");
        if (val) {
            input.value = parseInt(val).toLocaleString("id-ID");
        } else {
            input.value = "";
        }
        calculatePerijinanSectionTotalFromItems(sectionIndex);
    };

    window.removePerijinanItem = function(btn, sectionIndex) {
        const container = document.getElementById(`perijinan_items_container_${sectionIndex}`);
        if (container && container.children.length > 1) {
            btn.closest('.flex').remove();
            reindexPerijinanItems(sectionIndex);
            calculatePerijinanSectionTotalFromItems(sectionIndex);
        }
    };

    function reindexPerijinanItems(sectionIndex) {
        const container = document.getElementById(`perijinan_items_container_${sectionIndex}`);
        if (!container) return;
        
        Array.from(container.children).forEach((div, itemIdx) => {
            const select = div.querySelector('.perijinan-item-select');
            const manualName = div.querySelector('.perijinan-item-manual-name');
            const tarif = div.querySelector('.perijinan-item-tarif');
            
            if (select) select.name = `perijinan_sections[${sectionIndex}][items][${itemIdx}][pricelist_perijinan_id]`;
            if (manualName) manualName.name = `perijinan_sections[${sectionIndex}][items][${itemIdx}][nama_perijinan]`;
            if (tarif) tarif.name = `perijinan_sections[${sectionIndex}][items][${itemIdx}][tarif]`;
        });
    }

    function calculatePerijinanSectionTotalFromItems(sectionIndex) {
        const container = document.getElementById(`perijinan_items_container_${sectionIndex}`);
        if (!container) return;
        
        let sectionTotal = 0;
        container.querySelectorAll('.perijinan-item-tarif').forEach(input => {
            const val = parseInt(input.value.replace(/\D/g, "") || 0);
            sectionTotal += val;
        });
        
        const jumlahInput = document.getElementById(`perijinan_jumlah_${sectionIndex}`);
        if (jumlahInput) {
            jumlahInput.value = sectionTotal.toLocaleString('id-ID');
            // Trigger format and calculation logic
            formatPerijinanBiaya(jumlahInput);
        }
    }

    window.loadVoyagesForPerijinanSection = function(sectionIndex, kapalNama) {
        const voyageSelect = document.getElementById(`perijinan_voyage_${sectionIndex}`);
        if (!voyageSelect) return;

        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        voyageSelect.disabled = true;

        if (!kapalNama) {
            voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
            return;
        }

        fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(kapalNama)}`)
            .then(r => r.json())
            .then(data => {
                if (data.success && data.voyages && data.voyages.length > 0) {
                    let html = '<option value="">-- Pilih Voyage --</option>';
                    data.voyages.forEach(v => {
                        html += `<option value="${v}">${v}</option>`;
                    });
                    voyageSelect.innerHTML = html;
                } else {
                    voyageSelect.innerHTML = '<option value="">Tidak ada voyage tersedia</option>';
                }
                voyageSelect.disabled = false;
            })
            .catch(() => {
                voyageSelect.innerHTML = '<option value="">Gagal memuat voyage</option>';
                voyageSelect.disabled = false;
            });
    };

    if (addPerijinanSectionBtn) {
        addPerijinanSectionBtn.addEventListener('click', () => addPerijinanSection());
    }
    if (addPerijinanSectionBottomBtn) {
        addPerijinanSectionBottomBtn.addEventListener('click', () => addPerijinanSection());
    }
