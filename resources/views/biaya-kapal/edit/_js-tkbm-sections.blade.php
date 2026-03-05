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
