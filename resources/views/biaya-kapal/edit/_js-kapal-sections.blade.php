    
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
        
        // Setup kapal change listener with Select2
        const kapalSelect = section.querySelector('.kapal-select');
        const voyageSelect = section.querySelector('.voyage-select');
        $(kapalSelect).select2({
            placeholder: "-- Pilih Kapal --",
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 0
        }).on('change', async function() {
        
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
            // Destroy Select2 before removing element to prevent memory leaks
            $(section).find('.kapal-select').select2('destroy');
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

