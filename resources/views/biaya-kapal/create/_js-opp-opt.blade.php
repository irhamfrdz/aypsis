    // ============= OPP/OPT SECTIONS MANAGEMENT =============
    let oppOptSectionCounter = 0;
    const oppOptSectionsContainer = document.getElementById('opp_opt_sections_container');
    const addOppOptSectionBtn = document.getElementById('add_opp_opt_section_btn');
    
    function initializeOppOptSections() {
        if (!oppOptSectionsContainer) return;
        oppOptSectionsContainer.innerHTML = '';
        oppOptSectionCounter = 0;
        addOppOptSection();
    }
    
    function clearAllOppOptSections() {
        if (!oppOptSectionsContainer) return;
        oppOptSectionsContainer.innerHTML = '';
        oppOptSectionCounter = 0;
    }
    
    if (addOppOptSectionBtn) {
        addOppOptSectionBtn.addEventListener('click', function() {
            addOppOptSection();
        });
    }
    
    function addOppOptSection() {
        if (!oppOptSectionsContainer) return;
        oppOptSectionCounter++;
        const sectionIndex = oppOptSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'opp-opt-section mb-6 p-4 border-2 border-purple-200 rounded-lg bg-purple-50';
        section.setAttribute('data-opp-opt-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex} (OPP/OPT)</h3>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeOppOptSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <!-- Hidden inputs for section totals -->
            <input type="hidden" name="opp_opt_sections[${sectionIndex}][total_nominal]" class="opp-opt-section-total-hidden" value="0">
            <input type="hidden" name="opp_opt_sections[${sectionIndex}][dp]" class="opp-opt-section-dp-hidden" value="0">
            <input type="hidden" name="opp_opt_sections[${sectionIndex}][sisa_pembayaran]" class="opp-opt-section-sisa-hidden" value="0">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="opp_opt_sections[${sectionIndex}][kapal]" class="opp-opt-kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-purple-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="opp_opt_sections[${sectionIndex}][voyage]" class="opp-opt-voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-purple-500" required disabled>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="opp_opt_sections[${sectionIndex}][voyage]" class="opp-opt-voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-purple-500 hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="opp-opt-voyage-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="block text-xs font-medium text-gray-700 mb-2">Detail Barang (OPP/OPT)</label>
                <div class="opp-opt-barang-container" data-opp-opt-section="${sectionIndex}"></div>
                <button type="button" onclick="addBarangToOppOptSection(${sectionIndex})" class="mt-2 px-3 py-1.5 bg-purple-500 hover:bg-purple-600 text-white text-xs rounded-lg transition">
                    <i class="fas fa-plus mr-1"></i> Tambah Barang
                </button>
            </div>
            
            <!-- Nominal Per Kapal Display -->
            <div class="mt-3 p-3 bg-white border border-purple-300 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-700">Nominal Kapal ${sectionIndex}:</span>
                    <span class="opp-opt-section-nominal-display text-lg font-bold text-purple-600">Rp 0</span>
                </div>
            </div>
        `;
        
        oppOptSectionsContainer.appendChild(section);
        
        // Setup kapal change listener
        const kapalSelect = section.querySelector('.opp-opt-kapal-select');
        kapalSelect.addEventListener('change', function() {
            loadVoyagesForOppOptSection(sectionIndex, this.value);
        });

        // Setup manual voyage toggle
        const voyageSelect = section.querySelector('.opp-opt-voyage-select');
        const voyageInput = section.querySelector('.opp-opt-voyage-input');
        const voyageManualBtn = section.querySelector('.opp-opt-voyage-manual-btn');

        voyageManualBtn.addEventListener('click', function() {
            if (voyageInput.classList.contains('hidden')) {
                // Switch to manual input
                voyageSelect.classList.add('hidden');
                voyageSelect.disabled = true;
                
                voyageInput.classList.remove('hidden');
                voyageInput.disabled = false;
                voyageInput.focus();
                
                this.classList.remove('bg-gray-200', 'text-gray-600');
                this.classList.add('bg-purple-200', 'text-purple-700');
                this.innerHTML = '<i class="fas fa-list"></i>';
            } else {
                // Switch to select list
                voyageInput.classList.add('hidden');
                voyageInput.disabled = true;
                
                voyageSelect.classList.remove('hidden');
                voyageSelect.disabled = false;
                
                this.classList.add('bg-gray-200', 'text-gray-600');
                this.classList.remove('bg-purple-200', 'text-purple-700');
                this.innerHTML = '<i class="fas fa-keyboard"></i>';
            }
        });

        // Trigger auto-fill when voyage changes
        voyageSelect.addEventListener('change', function() {
            if (this.value && kapalSelect.value) {
                autoFillOppOptBarangForSection(sectionIndex, kapalSelect.value, this.value);
            }
        });

        voyageInput.addEventListener('blur', function() {
            if (this.value && kapalSelect.value) {
                autoFillOppOptBarangForSection(sectionIndex, kapalSelect.value, this.value);
            }
        });
        
        // Add first barang input as default
        addBarangToOppOptSection(sectionIndex);
    }
    
    window.removeOppOptSection = function(sectionIndex) {
        const section = document.querySelector(`[data-opp-opt-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllOppOptSections();
        }
    };

    // Auto-fill OPP/OPT barang based on container counts from BL table
    function autoFillOppOptBarangForSection(sectionIndex, kapalNama, voyage) {
        const section = document.querySelector(`[data-opp-opt-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.opp-opt-barang-container');
        
        // Show loading
        container.innerHTML = '<div class="text-sm text-gray-500 italic py-2"><i class="fas fa-spinner fa-spin mr-2"></i>Menghitung item...</div>';
        
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
                let itemsAdded = false;
                
                // Pricelist IDs mapping based on item names
                const pricelistIds = {};
                pricelistOppOptData.forEach(p => {
                    pricelistIds[p.nama_barang] = p.id;
                });
                
                // Add FCL 20FT
                if (data.counts['20'] && data.counts['20'].fcl > 0 && pricelistIds['FCL 20FT']) {
                    addBarangToOppOptSectionWithValue(sectionIndex, pricelistIds['FCL 20FT'], data.counts['20'].fcl);
                    itemsAdded = true;
                }
                
                // Add FCL 40FT
                if (data.counts['40'] && data.counts['40'].fcl > 0 && pricelistIds['FCL 40FT']) {
                    addBarangToOppOptSectionWithValue(sectionIndex, pricelistIds['FCL 40FT'], data.counts['40'].fcl);
                    itemsAdded = true;
                }
                
                // Add LCL 20FT
                if (data.counts['20'] && data.counts['20'].lcl > 0 && pricelistIds['LCL 20FT']) {
                    addBarangToOppOptSectionWithValue(sectionIndex, pricelistIds['LCL 20FT'], data.counts['20'].lcl);
                    itemsAdded = true;
                }
                
                // Add LCL 40FT
                if (data.counts['40'] && data.counts['40'].lcl > 0 && pricelistIds['LCL 40FT']) {
                    addBarangToOppOptSectionWithValue(sectionIndex, pricelistIds['LCL 40FT'], data.counts['40'].lcl);
                    itemsAdded = true;
                }
                
                // Add Extra items (Mobil, Trailer, Truck)
                if (data.counts.extra) {
                    Object.keys(data.counts.extra).forEach(itemName => {
                         const count = data.counts.extra[itemName];
                         if (count > 0 && pricelistIds[itemName]) {
                             addBarangToOppOptSectionWithValue(sectionIndex, pricelistIds[itemName], count);
                             itemsAdded = true;
                         }
                    });
                }
                
                // If no items found, add one empty row
                if (!itemsAdded) {
                    addBarangToOppOptSection(sectionIndex);
                }
                
                // Recalculate total
                calculateTotalFromAllOppOptSections();
            } else {
                // Fallback to empty input
                container.innerHTML = '';
                addBarangToOppOptSection(sectionIndex);
            }
        })
        .catch(error => {
            console.error('Error fetching container counts for OPP/OPT:', error);
            container.innerHTML = '';
            addBarangToOppOptSection(sectionIndex);
        });
    }
    
    function loadVoyagesForOppOptSection(sectionIndex, kapalNama) {
        const section = document.querySelector(`[data-opp-opt-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.opp-opt-voyage-select');
        
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
                console.error('Error fetching voyages for OPP/OPT:', error);
                voyageSelect.innerHTML = '<option value="">Gagal memuat voyages</option>';
            });
    }
    
    window.addBarangToOppOptSection = function(sectionIndex) {
        const section = document.querySelector(`[data-opp-opt-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.opp-opt-barang-container');
        const barangIndex = container.children.length;
        
        let barangOptions = '<option value="">Pilih Nama Barang</option>';
        pricelistOppOptData.forEach(pricelist => {
            barangOptions += `<option value="${pricelist.id}" data-tarif="${pricelist.tarif}">${pricelist.nama_barang}</option>`;
        });
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2';
        inputGroup.innerHTML = `
            <div class="flex-1">
                <select name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][barang_id]" class="opp-opt-barang-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" required>
                    ${barangOptions}
                </select>
            </div>
            <div class="w-24">
                <input type="number" step="any" name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][jumlah]" class="opp-opt-jumlah-input-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" placeholder="0" required>
            </div>
            <button type="button" onclick="removeBarangFromOppOptSection(this)" class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition">
                <i class="fas fa-trash text-xs"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        // Add event listeners
        const barangSelect = inputGroup.querySelector('.opp-opt-barang-select-item');
        const jumlahInput = inputGroup.querySelector('.opp-opt-jumlah-input-item');
        
        barangSelect.addEventListener('change', function() {
            calculateTotalFromAllOppOptSections();
        });
        
        jumlahInput.addEventListener('input', function() {
            calculateTotalFromAllOppOptSections();
        });
    };

    // Add barang to OPP/OPT section with pre-filled values
    window.addBarangToOppOptSectionWithValue = function(sectionIndex, barangId, jumlah) {
        const section = document.querySelector(`[data-opp-opt-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.opp-opt-barang-container');
        const barangIndex = container.children.length;
        
        let barangOptions = '<option value="">Pilih Nama Barang</option>';
        pricelistOppOptData.forEach(pricelist => {
            const selected = pricelist.id == barangId ? 'selected' : '';
            barangOptions += `<option value="${pricelist.id}" data-tarif="${pricelist.tarif}" ${selected}>${pricelist.nama_barang}</option>`;
        });
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2';
        inputGroup.innerHTML = `
            <div class="flex-1">
                <select name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][barang_id]" class="opp-opt-barang-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" required>
                    ${barangOptions}
                </select>
            </div>
            <div class="w-24">
                <input type="number" step="any" name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][jumlah]" value="${jumlah}" class="opp-opt-jumlah-input-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" placeholder="0" required>
            </div>
            <button type="button" onclick="removeBarangFromOppOptSection(this)" class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition">
                <i class="fas fa-trash text-xs"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        // Add event listeners
        const barangSelect = inputGroup.querySelector('.opp-opt-barang-select-item');
        const jumlahInput = inputGroup.querySelector('.opp-opt-jumlah-input-item');
        
        barangSelect.addEventListener('change', function() {
            calculateTotalFromAllOppOptSections();
        });
        
        jumlahInput.addEventListener('input', function() {
            calculateTotalFromAllOppOptSections();
        });
    };
    
    window.removeBarangFromOppOptSection = function(button) {
        const container = button.closest('.opp-opt-barang-container');
        if (container.children.length > 1) {
            button.closest('.flex').remove();
            
            // Reindex all barang inputs after removal
            reindexOppOptBarangInputs(container);
            
            calculateTotalFromAllOppOptSections();
        }
    };
    
    function reindexOppOptBarangInputs(container) {
        const section = container.closest('.opp-opt-section');
        const sectionIndex = section.getAttribute('data-opp-opt-section-index');
        const inputGroups = container.querySelectorAll('.flex');
        
        inputGroups.forEach((group, newIndex) => {
            const barangSelect = group.querySelector('.opp-opt-barang-select-item');
            if (barangSelect) {
                barangSelect.name = `opp_opt_sections[${sectionIndex}][barang][${newIndex}][barang_id]`;
            }
            const jumlahInput = group.querySelector('.opp-opt-jumlah-input-item');
            if (jumlahInput) {
                jumlahInput.name = `opp_opt_sections[${sectionIndex}][barang][${newIndex}][jumlah]`;
            }
        });
    }
    
    function calculateTotalFromAllOppOptSections() {
        let grandTotal = 0;
        
        document.querySelectorAll('.opp-opt-section').forEach(section => {
            let sectionTotal = 0;
            const barangSelects = section.querySelectorAll('.opp-opt-barang-select-item');
            const jumlahInputs = section.querySelectorAll('.opp-opt-jumlah-input-item');
            const nominalDisplay = section.querySelector('.opp-opt-section-nominal-display');
            
            barangSelects.forEach((select, index) => {
                const selectedOption = select.options[select.selectedIndex];
                const tarif = parseFloat(selectedOption.getAttribute('data-tarif')) || 0;
                const jumlahInput = jumlahInputs[index];
                const jumlah = parseFloat(jumlahInput.value.replace(',', '.')) || 0;
                sectionTotal += tarif * jumlah;
            });
            
            // Update section nominal display
            if (nominalDisplay) {
                nominalDisplay.textContent = sectionTotal > 0 ? `Rp ${Math.round(sectionTotal).toLocaleString('id-ID')}` : 'Rp 0';
            }
            
            // Update hidden inputs for DP logic if needed
            const sectionIndex = section.getAttribute('data-opp-opt-section-index');
            const totalHidden = section.querySelector('.opp-opt-section-total-hidden');
            if (totalHidden) totalHidden.value = Math.round(sectionTotal);
            
            grandTotal += sectionTotal;
        });
        
        if (grandTotal > 0) {
            nominalInput.value = Math.round(grandTotal).toLocaleString('id-ID');
            calculateSisaPembayaran();
        } else {
            nominalInput.value = '';
        }
    }
