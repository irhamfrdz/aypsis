    // ============= NEW KAPAL SECTIONS MANAGEMENT =============
    let kapalSectionCounter = 0;
    const kapalSectionsContainer = document.getElementById('kapal_sections_container');
    const addKapalSectionBtn = document.getElementById('add_kapal_section_btn');
    const pricelistThcVendorsData = @json($pricelistThcVendors ?? []);
    const pricelistThcsData = @json($pricelistThcs ?? []);
    const allBuruhsData = @json($allBuruhs ?? []);

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
            
            <!-- Hidden inputs for section totals -->
            <input type="hidden" name="kapal_sections[${sectionIndex}][total_nominal]" class="section-total-hidden" value="0">
            <input type="hidden" name="kapal_sections[${sectionIndex}][dp]" class="section-dp-hidden" value="0">
            <input type="hidden" name="kapal_sections[${sectionIndex}][sisa_pembayaran]" class="section-sisa-hidden" value="0">
            
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
            
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-700 mb-2">Detail Barang</label>
                <div class="barang-container-section" data-section="${sectionIndex}"></div>
                <button type="button" onclick="addBarangToSection(${sectionIndex})" class="mt-2 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded-lg transition">
                    <i class="fas fa-plus mr-1"></i> Tambah Barang
                </button>
            </div>
            
            <div class="mb-3 p-3 bg-gray-100 border border-gray-200 rounded-lg">
                <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wider">Tenaga Kerja / Buruh</label>
                <div class="buruh-container-section" data-section="${sectionIndex}"></div>
                <div class="flex gap-2 mt-2">
                    <button type="button" onclick="addBuruhToSection(${sectionIndex})" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs rounded-lg transition shadow-sm">
                        <i class="fas fa-user-plus mr-1"></i> Tambah Buruh
                    </button>
                    <button type="button" onclick="randomizeBuruhForSection(${sectionIndex})" class="px-3 py-1.5 bg-purple-500 hover:bg-purple-600 text-white text-xs rounded-lg transition shadow-sm" title="Pilih buruh & nominal secara acak">
                        <i class="fas fa-dice mr-1"></i> Randomize Buruh
                    </button>
                </div>
            </div>
            
            <!-- Nominal Per Kapal Display -->
            <div class="mt-3 p-3 bg-white border border-blue-300 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Adjustment</label>
                        <input type="text" name="kapal_sections[${sectionIndex}][adjustment]" class="adjustment-input w-full px-3 py-2 border border-blue-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 text-sm" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Catatan Adjustment</label>
                        <input type="text" name="kapal_sections[${sectionIndex}][notes_adjustment]" class="w-full px-3 py-2 border border-blue-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 text-sm" placeholder="Keterangan adjustment">
                    </div>
                </div>
                <div class="flex justify-between items-center border-t border-blue-100 pt-2">
                    <span class="text-sm font-semibold text-gray-700">Nominal Kapal ${sectionIndex}:</span>
                    <span class="section-nominal-display text-lg font-bold text-blue-600">Rp 0</span>
                </div>
            </div>
        `;
        
        kapalSectionsContainer.appendChild(section);
        
        // Setup kapal change listener - use data attribute to get correct section
        const kapalSelect = section.querySelector('.kapal-select');
        const voyageSelect = section.querySelector('.voyage-select');
        
        kapalSelect.addEventListener('change', function() {
            const currentSection = this.closest('.kapal-section');
            const currentIndex = parseInt(currentSection.getAttribute('data-section-index'));
            console.log('Kapal changed in section:', currentIndex, 'Value:', this.value);
            loadVoyagesForSection(currentIndex, this.value);
        });
        
        voyageSelect.addEventListener('change', function() {
            const currentSection = this.closest('.kapal-section');
            const currentIndex = parseInt(currentSection.getAttribute('data-section-index'));
            const currentKapalSelect = currentSection.querySelector('.kapal-select');
            const kapalNama = currentKapalSelect.value;
            const voyageValue = this.value;
            console.log('Voyage changed in section:', currentIndex, 'Kapal:', kapalNama, 'Voyage:', voyageValue);
            if (kapalNama && voyageValue) {
                autoFillBarangForSection(currentIndex, kapalNama, voyageValue);
            }
        });

        // Setup manual voyage toggle
        const voyageInput = section.querySelector('.voyage-input');
        const voyageManualBtn = section.querySelector('.voyage-manual-btn');
        const adjustmentInput = section.querySelector('.adjustment-input');

        adjustmentInput.addEventListener('input', function() {
            // Apply currency formatting
            let val = this.value.replace(/\./g, '');
            if (!isNaN(val) && val !== '') {
                this.value = Math.round(val).toLocaleString('id-ID');
            }
            calculateTotalFromAllSections();
        });

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
                    '40_empty': null,
                    'cargo': null
                };
                
                // Find pricelist IDs from pricelistBuruhData
                pricelistBuruhData.forEach(p => {
                    const barangLower = (p.barang || '').toLowerCase();
                    if (barangLower.includes('kontainer') && barangLower.includes('20') && barangLower.includes('full')) {
                        pricelistIds['20_full'] = p.id;
                    } else if (barangLower.includes('kontainer') && barangLower.includes('20') && barangLower.includes('empty')) {
                        pricelistIds['20_empty'] = p.id;
                    } else if (barangLower.includes('kontainer') && barangLower.includes('40') && barangLower.includes('full')) {
                        pricelistIds['40_full'] = p.id;
                    } else if (barangLower.includes('kontainer') && barangLower.includes('40') && barangLower.includes('empty')) {
                        pricelistIds['40_empty'] = p.id;
                    } else if (barangLower === 'cargo') {
                        pricelistIds['cargo'] = p.id;
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

                // Add CARGO if count > 0
                if (data.counts['cargo_max_tv_sum'] > 0 && pricelistIds['cargo']) {
                    addBarangToSectionWithValue(sectionIndex, pricelistIds['cargo'], data.counts['cargo_max_tv_sum']);
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
            section.remove();
            calculateTotalFromAllSections();
        }
    };
    
    function loadVoyagesForSection(sectionIndex, kapalNama) {
        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.voyage-select');
        
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
                console.error('Error fetching voyages:', error);
                voyageSelect.innerHTML = '<option value="">Gagal memuat voyages</option>';
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
    
    window.addBuruhToSection = function(sectionIndex) {
        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.buruh-container-section');
        const buruhIndex = container.children.length;
        
        let buruhOptions = '<option value="">Pilih Nama Buruh</option>';
        allBuruhsData.forEach(buruh => {
            buruhOptions += `<option value="${buruh.id}">${buruh.nama} ${buruh.nik ? '('+buruh.nik+')' : ''}</option>`;
        });
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2';
        inputGroup.innerHTML = `
            <div class="flex-1">
                <select name="kapal_sections[${sectionIndex}][tenaga_kerja][${buruhIndex}][buruh_id]" class="buruh-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-emerald-500" required>
                    ${buruhOptions}
                </select>
            </div>
            <div class="w-32">
                <input type="text" name="kapal_sections[${sectionIndex}][tenaga_kerja][${buruhIndex}][nominal]" class="buruh-nominal-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-emerald-500" placeholder="Rp 0" required>
            </div>
            <button type="button" onclick="removeBuruhFromSection(this)" class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition">
                <i class="fas fa-trash text-xs"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        // Add currency formatting to nominal input
        const nominalInput = inputGroup.querySelector('.buruh-nominal-item');
        nominalInput.addEventListener('input', function() {
            let val = this.value.replace(/\D/g, '');
            if (val !== '') {
                this.value = parseInt(val).toLocaleString('id-ID');
            }
        });
    };

    window.randomizeBuruhForSection = function(sectionIndex) {
        if (!allBuruhsData || allBuruhsData.length === 0) {
            alert('Data buruh tidak tersedia.');
            return;
        }

        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        
        // 1. Hitung total biaya kapal pada section ini (Barang + Adjustment)
        let sectionTotal = 0;
        const barangSelects = section.querySelectorAll('.barang-select-item');
        const jumlahInputs = section.querySelectorAll('.jumlah-input-item');
        const adjustmentInput = section.querySelector('.adjustment-input');
        
        barangSelects.forEach((select, index) => {
            const selectedOption = select.options[select.selectedIndex];
            const tarif = parseFloat(selectedOption.getAttribute('data-tarif')) || 0;
            const jumlahRaw = (jumlahInputs[index].value || '0').replace(',', '.');
            const jumlah = parseFloat(jumlahRaw) || 0;
            sectionTotal += tarif * jumlah;
        });

        if (adjustmentInput) {
            const adjustmentRaw = (adjustmentInput.value || '0').replace(/\./g, '').replace(',', '.');
            const adjustment = parseFloat(adjustmentRaw) || 0;
            sectionTotal += adjustment;
        }

        sectionTotal = Math.round(sectionTotal / 1000) * 1000;

        if (sectionTotal <= 0) {
            alert('Nominal biaya kapal masih 0 atau negatif. Harap isi data barang terlebih dahulu.');
            return;
        }

        const container = section.querySelector('.buruh-container-section');
        
        // Clear existing buruh in this section for a clean random set
        container.innerHTML = '';
        
        // 2. Tentukan jumlah buruh acak (misal 3 - 12 orang)
        // Jika total kecil, kurangi jumlah buruh maksimal
        let maxBuruh = 12;
        if (sectionTotal < 500000) maxBuruh = 4;
        else if (sectionTotal < 1000000) maxBuruh = 7;
        
        const count = Math.floor(Math.random() * (maxBuruh - 3 + 1)) + 3; 
        
        // Pick random unique buruhs
        const shuffled = [...allBuruhsData].sort(() => 0.5 - Math.random());
        const selected = shuffled.slice(0, Math.min(count, shuffled.length));
        
        // 3. Distribusikan sectionTotal ke buruh yang terpilih secara merata (Equal Distribution)
        let average = sectionTotal / selected.length;
        let baseNominal = Math.round(average / 1000) * 1000;
        let totalBase = baseNominal * selected.length;
        let diff = sectionTotal - totalBase; 
        let countToAdjust = Math.abs(diff / 1000);
        let adjustment = diff > 0 ? 1000 : -1000;

        selected.forEach((buruh, index) => {
            let nominal = baseNominal;
            // Bagikan sisa (plus atau minus) ke beberapa orang pertama
            if (index < countToAdjust) {
                nominal += adjustment;
            }
            
            addBuruhToSectionWithData(sectionIndex, buruh.id, nominal);
        });
    };

    window.addBuruhToSectionWithData = function(sectionIndex, buruhId, nominal) {
        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.buruh-container-section');
        const buruhIndex = container.children.length;
        
        let buruhOptions = '<option value="">Pilih Nama Buruh</option>';
        allBuruhsData.forEach(buruh => {
            const selected = buruh.id == buruhId ? 'selected' : '';
            buruhOptions += `<option value="${buruh.id}" ${selected}>${buruh.nama} ${buruh.nik ? '('+buruh.nik+')' : ''}</option>`;
        });
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2 animate-fade-in';
        inputGroup.innerHTML = `
            <div class="flex-1">
                <select name="kapal_sections[${sectionIndex}][tenaga_kerja][${buruhIndex}][buruh_id]" class="buruh-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-emerald-500" required>
                    ${buruhOptions}
                </select>
            </div>
            <div class="w-32">
                <input type="text" name="kapal_sections[${sectionIndex}][tenaga_kerja][${buruhIndex}][nominal]" value="${nominal.toLocaleString('id-ID')}" class="buruh-nominal-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-emerald-500" placeholder="Rp 0" required>
            </div>
            <button type="button" onclick="removeBuruhFromSection(this)" class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition">
                <i class="fas fa-trash text-xs"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        const nominalInput = inputGroup.querySelector('.buruh-nominal-item');
        nominalInput.addEventListener('input', function() {
            let val = this.value.replace(/\D/g, '');
            if (val !== '') {
                this.value = parseInt(val).toLocaleString('id-ID');
            }
        });
    };
    
    window.removeBuruhFromSection = function(button) {
        const container = button.closest('.buruh-container-section');
        button.closest('.flex').remove();
        reindexBuruhInputs(container);
    };
    
    function reindexBuruhInputs(container) {
        const section = container.closest('.kapal-section');
        const sectionIndex = section.getAttribute('data-section-index');
        const inputGroups = container.querySelectorAll('.flex');
        
        inputGroups.forEach((group, newIndex) => {
            const buruhSelect = group.querySelector('.buruh-select-item');
            if (buruhSelect) {
                buruhSelect.name = `kapal_sections[${sectionIndex}][tenaga_kerja][${newIndex}][buruh_id]`;
            }
            
            const nominalInput = group.querySelector('.buruh-nominal-item');
            if (nominalInput) {
                nominalInput.name = `kapal_sections[${sectionIndex}][tenaga_kerja][${newIndex}][nominal]`;
            }
        });
    }

    window.removeBarangFromSection = function(button) {
        const container = button.closest('.barang-container-section');
        if (container.children.length > 1) {
            button.closest('.flex').remove();
            
            // CRITICAL FIX: Reindex all barang inputs after removal to prevent gaps in array indices
            reindexBarangInputs(container);
            
            calculateTotalFromAllSections();
        }
    };
    
    // Helper function to reindex barang input names after deletion
    function reindexBarangInputs(container) {
        const section = container.closest('.kapal-section');
        const sectionIndex = section.getAttribute('data-section-index');
        const inputGroups = container.querySelectorAll('.flex');
        
        inputGroups.forEach((group, newIndex) => {
            // Update barang_id input name
            const barangSelect = group.querySelector('.barang-select-item');
            if (barangSelect) {
                barangSelect.name = `kapal_sections[${sectionIndex}][barang][${newIndex}][barang_id]`;
            }
            
            // Update jumlah input name
            const jumlahInput = group.querySelector('.jumlah-input-item');
            if (jumlahInput) {
                jumlahInput.name = `kapal_sections[${sectionIndex}][barang][${newIndex}][jumlah]`;
            }
        });
    }
    
    function calculateTotalFromAllSections() {
        let grandTotal = 0;
        
        document.querySelectorAll('.kapal-section').forEach(section => {
            const barangSelects = section.querySelectorAll('.barang-select-item');
            const jumlahInputs = section.querySelectorAll('.jumlah-input-item');
            const adjustmentInput = section.querySelector('.adjustment-input');
            const nominalDisplay = section.querySelector('.section-nominal-display');
            const sectionTotalHidden = section.querySelector('.section-total-hidden');
            
            let sectionTotal = 0;
            
            barangSelects.forEach((select, index) => {
                const selectedOption = select.options[select.selectedIndex];
                const tarif = parseFloat(selectedOption.getAttribute('data-tarif')) || 0;
                // Convert comma to period for proper decimal parsing (Indonesian format)
                const jumlahRaw = (jumlahInputs[index].value || '0').replace(',', '.');
                const jumlah = parseFloat(jumlahRaw) || 0;
                sectionTotal += tarif * jumlah;
            });

            // Add adjustment
            if (adjustmentInput) {
                const adjustmentRaw = (adjustmentInput.value || '0').replace(/\./g, '').replace(',', '.');
                const adjustment = parseFloat(adjustmentRaw) || 0;
                sectionTotal += adjustment;
            }
            
            // Update section nominal display
            if (nominalDisplay) {
                nominalDisplay.textContent = sectionTotal > 0 ? `Rp ${Math.round(sectionTotal).toLocaleString('id-ID')}` : 'Rp 0';
            }

            // Update hidden input for section total
            if (sectionTotalHidden) {
                sectionTotalHidden.value = Math.round(sectionTotal);
            }
            
            grandTotal += sectionTotal;
        });
        
        if (grandTotal > 0) {
            nominalInput.value = Math.round(grandTotal).toLocaleString('id-ID');
            // Recalculate sisa pembayaran after nominal changes
            calculateSisaPembayaran();
        } else {
            nominalInput.value = '';
        }
    }
