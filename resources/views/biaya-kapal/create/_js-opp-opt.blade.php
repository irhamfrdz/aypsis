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
                <div class="flex items-center gap-4">
                    <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex} (OPP/OPT)</h3>
                    <label class="flex items-center gap-2 px-3 py-1 bg-white border border-purple-300 rounded-lg cursor-pointer hover:bg-purple-100 transition shadow-sm">
                        <input type="checkbox" name="opp_opt_sections[${sectionIndex}][is_bongkaran]" class="opp-opt-is-bongkaran-checkbox rounded border-purple-400 text-purple-600 focus:ring-purple-500" value="1">
                        <span class="text-xs font-bold text-purple-700">BONGKARAN</span>
                    </label>
                </div>
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
                <label class="block text-xs font-medium text-gray-700 mb-2">Detail Kontainer/BL (OPP/OPT)</label>
                <div class="opp-opt-barang-container" data-opp-opt-section="${sectionIndex}"></div>
                <button type="button" onclick="addBarangToOppOptSection(${sectionIndex})" class="mt-2 px-3 py-1.5 bg-purple-500 hover:bg-purple-600 text-white text-xs rounded-lg transition">
                    <i class="fas fa-plus mr-1"></i> Tambah Kontainer/BL
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
        
        // Setup voyage change listener for Bongkaran toggle
        const bongkaranCheckbox = section.querySelector('.opp-opt-is-bongkaran-checkbox');
        bongkaranCheckbox.addEventListener('change', function() {
            if (kapalSelect.value && (voyageSelect.value || voyageInput.value)) {
                autoFillOppOptBarangForSection(sectionIndex, kapalSelect.value, voyageSelect.value || voyageInput.value);
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

    // Load Manifests based on Kapal & Voyage
    function autoFillOppOptBarangForSection(sectionIndex, kapalNama, voyage) {
        const section = document.querySelector(`[data-opp-opt-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.opp-opt-barang-container');
        
        // Show loading
        container.innerHTML = '<div class="text-sm text-gray-500 italic py-2"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data kontainer/BL...</div>';
        
        fetch('{{ url("biaya-kapal/get-manifests-by-kapal-voyage") }}', {
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
            if (data.success && data.data) {
                container.innerHTML = '';
                section.setAttribute('data-manifests', JSON.stringify(data.data));
                
                // Group data by nomor_kontainer
                const groupedManifests = {};
                const cargoManifests = [];
                
                data.data.forEach(manifest => {
                    if (manifest.nomor_kontainer) {
                        if (!groupedManifests[manifest.nomor_kontainer]) {
                            groupedManifests[manifest.nomor_kontainer] = [];
                        }
                        groupedManifests[manifest.nomor_kontainer].push(manifest.id);
                    } else {
                        cargoManifests.push([manifest.id]);
                    }
                });
                
                // Add rows for grouped containers
                Object.values(groupedManifests).forEach(manifestIds => {
                    addBarangToOppOptSection(sectionIndex, manifestIds);
                });
                
                // Add rows for cargo (no container number)
                cargoManifests.forEach(manifestIds => {
                    addBarangToOppOptSection(sectionIndex, manifestIds);
                });
                
                // If no data, add empty row
                if (Object.keys(groupedManifests).length === 0 && cargoManifests.length === 0) {
                    addBarangToOppOptSection(sectionIndex);
                }
                
                calculateTotalFromAllOppOptSections();
            } else {
                container.innerHTML = '';
                section.removeAttribute('data-manifests');
                addBarangToOppOptSection(sectionIndex);
            }
        })
        .catch(error => {
            console.error('Error fetching manifests for OPP/OPT:', error);
            container.innerHTML = '';
            section.removeAttribute('data-manifests');
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
    
    window.addBarangToOppOptSection = function(sectionIndex, selectedManifestIds = []) {
        const section = document.querySelector(`[data-opp-opt-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.opp-opt-barang-container');
        const barangIndex = container.children.length;
        
        let manifestsData = [];
        try {
            manifestsData = JSON.parse(section.getAttribute('data-manifests') || '[]');
        } catch (e) {}

        let barangOptions = '<option value="">Pilih Kontainer / BL</option>';
        manifestsData.forEach(manifest => {
            const isSelected = selectedManifestIds.includes(manifest.id) ? 'selected' : '';
            barangOptions += `<option value="${manifest.id}" ${isSelected}>${manifest.label}</option>`;
        });

        let biayaOptions = '<option value="">Pilih Biaya</option>';
        @if(isset($klasifikasiBiayas))
            @foreach($klasifikasiBiayas as $kb)
                biayaOptions += `<option value="{{ $kb->id }}">{{ $kb->nama }}</option>`;
            @endforeach
        @endif
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2';
        inputGroup.innerHTML = `
            <div class="w-[12%]">
                <label class="block text-[10px] font-medium text-gray-700 mb-1">Klasifikasi</label>
                <select name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][klasifikasi]" class="opp-opt-klasifikasi-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500">
                    <option value="">-- Pilih --</option>
                    <option value="opslag">Opslag</option>
                </select>
            </div>
            <div class="w-[15%]">
                <label class="block text-[10px] font-medium text-gray-700 mb-1">Biaya</label>
                <select name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][klasifikasi_biaya_id]" class="opp-opt-biaya-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" required>
                    ${biayaOptions}
                </select>
            </div>
            <div class="w-[20%]">
                <label class="block text-[10px] font-medium text-gray-700 mb-1">Kontainer / BL</label>
                <select name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][manifest_id][]" multiple="multiple" class="opp-opt-barang-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" required>
                    ${barangOptions}
                </select>
            </div>
            <div class="w-[15%]">
                <label class="block text-[10px] font-medium text-gray-700 mb-1">Tarif (Rp)</label>
                <input type="number" step="any" name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][tarif]" class="opp-opt-tarif-input-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" placeholder="0" required>
            </div>
            <div class="w-[15%]">
                <label class="block text-[10px] font-medium text-gray-700 mb-1">Vendor</label>
                <input type="text" name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][vendor]" class="opp-opt-vendor-input-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" placeholder="Vendor">
            </div>
            <div class="flex-1">
                <label class="block text-[10px] font-medium text-gray-700 mb-1">Catatan</label>
                <input type="text" name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][catatan]" class="opp-opt-catatan-input-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" placeholder="Catatan">
            </div>
            <button type="button" onclick="removeBarangFromOppOptSection(this)" class="px-2 py-1.5 mb-0.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition h-[34px]">
                <i class="fas fa-trash text-xs"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        // Initialize select2 if available
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $(inputGroup).find('.opp-opt-barang-select-item').select2({
                width: '100%',
                placeholder: 'Pilih Kontainer / BL (Bisa Lebih Dari 1)',
                allowClear: true
            });
        }
        
        // Add event listeners
        const tarifInput = inputGroup.querySelector('.opp-opt-tarif-input-item');
        
        tarifInput.addEventListener('input', function() {
            calculateTotalFromAllOppOptSections();
        });
    };

    window.addBarangToOppOptSectionWithValue = function(sectionIndex, manifestId, tarif, vendor, catatan, klasifikasiBiayaId, klasifikasi) {
        const section = document.querySelector(`[data-opp-opt-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.opp-opt-barang-container');
        const barangIndex = container.children.length;
        
        let manifestsData = [];
        try {
            manifestsData = JSON.parse(section.getAttribute('data-manifests') || '[]');
        } catch (e) {}

        let barangOptions = '<option value="">Pilih Kontainer / BL</option>';
        let found = false;
        manifestsData.forEach(manifest => {
            const selected = manifest.id == manifestId ? 'selected' : '';
            if (manifest.id == manifestId) found = true;
            barangOptions += `<option value="${manifest.id}" ${selected}>${manifest.label}</option>`;
        });
        
        // If manifestId exists but not in current options, just add it as an option
        if (manifestId && !found) {
            barangOptions += `<option value="${manifestId}" selected>Manifest ID: ${manifestId}</option>`;
        }
        
        let biayaOptions = '<option value="">Pilih Biaya</option>';
        @if(isset($klasifikasiBiayas))
            @foreach($klasifikasiBiayas as $kb)
                biayaOptions += `<option value="{{ $kb->id }}" ${klasifikasiBiayaId == '{{ $kb->id }}' ? 'selected' : ''}>{{ $kb->nama }}</option>`;
            @endforeach
        @endif
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2';
        inputGroup.innerHTML = `
            <div class="w-[12%]">
                <label class="block text-[10px] font-medium text-gray-700 mb-1">Klasifikasi</label>
                <select name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][klasifikasi]" class="opp-opt-klasifikasi-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500">
                    <option value="">-- Pilih --</option>
                    <option value="opslag" ${klasifikasi === 'opslag' ? 'selected' : ''}>Opslag</option>
                </select>
            </div>
            <div class="w-[15%]">
                <label class="block text-[10px] font-medium text-gray-700 mb-1">Biaya</label>
                <select name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][klasifikasi_biaya_id]" class="opp-opt-biaya-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" required>
                    ${biayaOptions}
                </select>
            </div>
            <div class="w-[20%]">
                <label class="block text-[10px] font-medium text-gray-700 mb-1">Kontainer / BL</label>
                <select name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][manifest_id]" class="opp-opt-barang-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" required>
                    ${barangOptions}
                </select>
            </div>
            <div class="w-[15%]">
                <label class="block text-[10px] font-medium text-gray-700 mb-1">Tarif (Rp)</label>
                <input type="number" step="any" name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][tarif]" class="opp-opt-tarif-input-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" placeholder="0" value="${tarif || 0}" required>
            </div>
            <div class="w-[15%]">
                <label class="block text-[10px] font-medium text-gray-700 mb-1">Vendor</label>
                <input type="text" name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][vendor]" value="${vendor || ''}" class="opp-opt-vendor-input-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" placeholder="Vendor">
            </div>
            <div class="flex-1">
                <label class="block text-[10px] font-medium text-gray-700 mb-1">Catatan</label>
                <input type="text" name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][catatan]" value="${catatan || ''}" class="opp-opt-catatan-input-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" placeholder="Catatan">
            </div>
            <button type="button" onclick="removeBarangFromOppOptSection(this)" class="px-2 py-1.5 mb-0.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition h-[34px]">
                <i class="fas fa-trash text-xs"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        // Initialize select2 if available
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $(inputGroup).find('.opp-opt-barang-select-item').select2({
                width: '100%'
            });
        }
        
        // Add event listeners
        const tarifInput = inputGroup.querySelector('.opp-opt-tarif-input-item');
        
        tarifInput.addEventListener('input', function() {
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
        const inputGroups = container.querySelectorAll('.flex.items-end');
        
        inputGroups.forEach((group, newIndex) => {
            const barangSelect = group.querySelector('.opp-opt-barang-select-item');
            if (barangSelect) {
                barangSelect.name = `opp_opt_sections[${sectionIndex}][barang][${newIndex}][manifest_id][]`;
            }
            const tarifInput = group.querySelector('.opp-opt-tarif-input-item');
            if (tarifInput) {
                tarifInput.name = `opp_opt_sections[${sectionIndex}][barang][${newIndex}][tarif]`;
            }
            const vendorInput = group.querySelector('.opp-opt-vendor-input-item');
            if (vendorInput) {
                vendorInput.name = `opp_opt_sections[${sectionIndex}][barang][${newIndex}][vendor]`;
            }
            const klasifikasiInput = group.querySelector('.opp-opt-klasifikasi-select-item');
            if (klasifikasiInput) {
                klasifikasiInput.name = `opp_opt_sections[${sectionIndex}][barang][${newIndex}][klasifikasi]`;
            }
            const catatanInput = group.querySelector('.opp-opt-catatan-input-item');
            if (catatanInput) {
                catatanInput.name = `opp_opt_sections[${sectionIndex}][barang][${newIndex}][catatan]`;
            }
        });
    }
    
    function calculateTotalFromAllOppOptSections() {
        let grandTotal = 0;
        
        document.querySelectorAll('.opp-opt-section').forEach(section => {
            let sectionTotal = 0;
            const tarifInputs = section.querySelectorAll('.opp-opt-tarif-input-item');
            const nominalDisplay = section.querySelector('.opp-opt-section-nominal-display');
            
            tarifInputs.forEach((tarifInput) => {
                const tarif = parseFloat(tarifInput.value.replace(',', '.')) || 0;
                sectionTotal += tarif;
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
