@php
    $oppOptTarifsRaw = \App\Models\PricelistOppOpt::all()->map(function($item) {
        return [
            'nama_barang' => strtolower(trim($item->nama_barang)),
            'status' => strtolower(trim($item->status_bongkar_muat)),
            'tarif' => $item->tarif
        ];
    })->toArray();
@endphp
    const oppOptTarifsFull = @json($oppOptTarifsRaw);

    function getOppOptTarif(namaBarang, isBongkaran) {
        if (!namaBarang) return null;
        namaBarang = namaBarang.toLowerCase();
        const requiredStatus = isBongkaran ? 'bongkar' : 'muat';
        
        let matched = oppOptTarifsFull.find(t => 
            t.nama_barang === namaBarang && 
            t.status === requiredStatus
        );
        
        if (!matched) {
            matched = oppOptTarifsFull.find(t => 
                t.nama_barang === namaBarang && 
                t.status === 'bongkar/muat'
            );
        }
        
        if (!matched) {
            matched = oppOptTarifsFull.find(t => t.nama_barang === namaBarang);
        }
        
        return matched ? matched.tarif : null;
    }

    // ============= OPP/OPT SECTIONS MANAGEMENT =============
    let oppOptSectionCounter = 0;
    const oppOptSectionsContainer = document.getElementById('opp_opt_sections_container');
    const addOppOptSectionBtn = document.getElementById('add_opp_opt_section_btn');
    
    function initializeOppOptSections() {
        if (!oppOptSectionsContainer) return;
        oppOptSectionsContainer.innerHTML = '';
        oppOptSectionCounter = 0;
        
        if (typeof existingOppOptSections !== 'undefined' && existingOppOptSections.length > 0) {
            existingOppOptSections.forEach(data => {
                const section = addOppOptSection(true); // true means isEdit to avoid default row
                const sectionIndex = section.getAttribute('data-opp-opt-section-index');
                
                const voySel = section.querySelector('.opp-opt-voyage-select');
                voySel.innerHTML = `<option value="${data.voyage}">${data.voyage}</option>`;
                voySel.value = data.voyage;
                voySel.setAttribute('data-saved-voyage', data.voyage);
                voySel.disabled = false;
                
                // Kapal & Voyage
                const kapalSel = section.querySelector('.opp-opt-kapal-select');
                if (kapalSel) {
                    kapalSel.value = data.kapal;
                    kapalSel.dispatchEvent(new Event('change', { bubbles: true }));
                }

                // Klasifikasi
                const klasifikasiSel = section.querySelector('.opp-opt-klasifikasi-select');
                if (klasifikasiSel && data.barang && data.barang.length > 0) {
                    klasifikasiSel.value = data.barang[0].klasifikasi || '';
                }

                // Hidden values
                const totalHidden = section.querySelector('.opp-opt-section-total-hidden');
                if (totalHidden) totalHidden.value = data.total_nominal || 0;
                
                const dpHidden = section.querySelector('.opp-opt-section-dp-hidden');
                if (dpHidden) dpHidden.value = data.dp || 0;
                
                const sisaHidden = section.querySelector('.opp-opt-section-sisa-hidden');
                if (sisaHidden) sisaHidden.value = data.sisa_pembayaran || 0;

                // Barang
                if (data.barang && data.barang.length > 0) {
                    data.barang.forEach(b => {
                        addBarangToOppOptSectionWithValue(sectionIndex, b.manifest_id, b.manifest_label, b.tarif, b.vendor, b.catatan, b.klasifikasi_biaya_id, b.jenis_ukuran, b.jumlah);
                    });
                } else {
                    addBarangToOppOptSection(sectionIndex);
                }
            });
            calculateTotalFromAllOppOptSections();
        } else {
            addOppOptSection();
        }
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
    
    function addOppOptSection(isEdit = false) {
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
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Klasifikasi</label>
                    <select name="opp_opt_sections[${sectionIndex}][klasifikasi]" class="opp-opt-klasifikasi-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-purple-500">
                        <option value="">-- Pilih --</option>
                        <option value="opslag">Opslag</option>
                    </select>
                </div>
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
        
        // Setup voyage change listener for Bongkaran toggle
        const bongkaranCheckbox = section.querySelector('.opp-opt-is-bongkaran-checkbox');
        
        // Add event listener for bongkaran checkbox to recalculate tariffs
        if (bongkaranCheckbox) {
            bongkaranCheckbox.addEventListener('change', function() {
                const isBongkaran = this.checked;
                const tarifInputs = section.querySelectorAll('.opp-opt-tarif-input-item');
                const jenisSelects = section.querySelectorAll('.opp-opt-jenis-ukuran-select-item');
                
                // Only for opslag rows
                jenisSelects.forEach((select, idx) => {
                    const tarifInput = tarifInputs[idx];
                    if (tarifInput && select.value) {
                        const selectedVal = select.value.toLowerCase();
                        const tarif = getOppOptTarif(selectedVal, isBongkaran);
                        if (tarif) {
                            tarifInput.value = tarif;
                        } else if (selectedVal === '20ft full') {
                            tarifInput.value = getOppOptTarif('fcl 20ft', isBongkaran) || '';
                        } else if (selectedVal === '40ft full') {
                            tarifInput.value = getOppOptTarif('fcl 40ft', isBongkaran) || '';
                        }
                    }
                });
                calculateTotalFromAllOppOptSections();
            });
        }

        // Setup kapal change listener
        const kapalSelect = section.querySelector('.opp-opt-kapal-select');


        kapalSelect.addEventListener('change', function() {
            // We read a data attribute to see if there's a saved voyage to select
            const savedVoyage = voyageSelect.getAttribute('data-saved-voyage');
            loadVoyagesForOppOptSection(sectionIndex, this.value, savedVoyage);
            // Clear it after using it once
            if (savedVoyage) voyageSelect.removeAttribute('data-saved-voyage');
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
            if (kapalSelect.value && (voyageSelect.value || voyageInput.value)) {
                autoFillOppOptBarangForSection(sectionIndex, kapalSelect.value, voyageSelect.value || voyageInput.value);
            }
        });

        // Setup klasifikasi change listener for Opslag mode
        const klasifikasiSelect = section.querySelector('.opp-opt-klasifikasi-select');
        if (klasifikasiSelect) {
            klasifikasiSelect.addEventListener('change', function() {
                toggleOpslagMode(sectionIndex, this.value);
            });
        }
        
        // Add bongkaran logic if needed (it uses voyage input blur now)
        const bongkaranCheckbox = section.querySelector('.opp-opt-is-bongkaran-checkbox');
        
        // Add first barang input as default if not edit
        if (!isEdit) {
            addBarangToOppOptSection(sectionIndex);
        }

        return section;
    }
    
    window.removeOppOptSection = function(sectionIndex) {
        const section = document.querySelector(`[data-opp-opt-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllOppOptSections();
        }
    };

    window.toggleOpslagMode = function(sectionIndex, klasifikasi) {
        const section = document.querySelector(`[data-opp-opt-section-index="${sectionIndex}"]`);
        if (!section) return;
        
        const container = section.querySelector('.opp-opt-barang-container');
        container.innerHTML = ''; // Clear rows
        
        // If Opslag, don't try to fetch manifests, just add one static row
        if (klasifikasi === 'opslag') {
            addBarangToOppOptSection(sectionIndex);
        } else {
            // Re-fetch manifests if Kapal and Voyage are set
            const kapalSelect = section.querySelector('.opp-opt-kapal-select');
            const voyageSelect = section.querySelector('.opp-opt-voyage-select');
            const voyageInput = section.querySelector('.opp-opt-voyage-input');
            
            if (kapalSelect.value && (voyageSelect.value || voyageInput.value)) {
                autoFillOppOptBarangForSection(sectionIndex, kapalSelect.value, voyageSelect.value || voyageInput.value);
            } else {
                addBarangToOppOptSection(sectionIndex);
            }
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
                
                // Add one default row
                addBarangToOppOptSection(sectionIndex);
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
    
    function loadVoyagesForOppOptSection(sectionIndex, kapalNama, savedVoyage = null) {
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
                    // Make sure saved voyage is always in the list even if not active
                    if (savedVoyage && !data.voyages.includes(savedVoyage)) {
                        html += `<option value="${savedVoyage}">${savedVoyage}</option>`;
                    }
                    voyageSelect.innerHTML = html;
                    if (savedVoyage) {
                        voyageSelect.value = savedVoyage;
                    }
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
        
        const klasifikasiSel = section.querySelector('.opp-opt-klasifikasi-select');
        const isOpslag = klasifikasiSel && klasifikasiSel.value === 'opslag';
        
        let manifestsData = [];
        try {
            manifestsData = JSON.parse(section.getAttribute('data-manifests') || '[]');
        } catch (e) {}

        let barangOptions = '<option value="">Pilih Kontainer / BL</option>';
        manifestsData.forEach(manifest => {
            barangOptions += `<option value="${manifest.id}">${manifest.label}</option>`;
        });

        let biayaOptions = '<option value="">Pilih Biaya</option>';
        @if(isset($klasifikasiBiayas))
            @foreach($klasifikasiBiayas as $kb)
                biayaOptions += `<option value="{{ $kb->id }}">{{ $kb->nama }}</option>`;
            @endforeach
        @endif
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2';
        
        if (isOpslag) {
            inputGroup.innerHTML = `
                <div class="w-[20%]">
                    <label class="block text-[10px] font-medium text-gray-700 mb-1">Biaya</label>
                    <select name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][klasifikasi_biaya_id]" class="opp-opt-biaya-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" required>
                        ${biayaOptions}
                    </select>
                </div>
                <div class="w-[20%]">
                    <label class="block text-[10px] font-medium text-gray-700 mb-1">Jenis Ukuran</label>
                    <select name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][jenis_ukuran]" class="opp-opt-jenis-ukuran-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" required>
                        <option value="">Pilih Jenis</option>
                        <option value="20ft Full">20ft Full</option>
                        <option value="20ft Empty">20ft Empty</option>
                        <option value="40ft Full">40ft Full</option>
                        <option value="40ft Empty">40ft Empty</option>
                        <option value="LCL 20ft">LCL 20ft</option>
                        <option value="LCL 40ft">LCL 40ft</option>
                        <option value="Motor">Motor</option>
                        <option value="Mobil">Mobil</option>
                        <option value="Truck">Truck</option>
                        <option value="Trailer">Trailer</option>
                    </select>
                </div>
                <div class="w-[10%]">
                    <label class="block text-[10px] font-medium text-gray-700 mb-1">Jumlah</label>
                    <input type="number" step="any" name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][jumlah]" class="opp-opt-jumlah-input-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" placeholder="0" required>
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
        } else {
            inputGroup.innerHTML = `
                <div class="w-[20%]">
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
        }
        
        container.appendChild(inputGroup);
        
        // Initialize select2 if available
        if (!isOpslag && typeof $ !== 'undefined' && $.fn.select2) {
            $(inputGroup).find('.opp-opt-barang-select-item').select2({
                width: '100%',
                placeholder: 'Pilih Kontainer / BL (Bisa Lebih Dari 1)',
                allowClear: true
            });
        }
        
        // Add event listeners
        const tarifInput = inputGroup.querySelector('.opp-opt-tarif-input-item');
        const jumlahInput = inputGroup.querySelector('.opp-opt-jumlah-input-item');
        const jenisUkuranSelect = inputGroup.querySelector('.opp-opt-jenis-ukuran-select-item');
        
        if (jenisUkuranSelect && tarifInput) {
            jenisUkuranSelect.addEventListener('change', function() {
                const selectedVal = this.value.toLowerCase();
                const isBongkaran = document.querySelector(`[name="opp_opt_sections[${sectionIndex}][is_bongkaran]"]`)?.checked;
                const tarif = getOppOptTarif(selectedVal, isBongkaran);
                
                if (tarif) {
                    tarifInput.value = tarif;
                } else if (selectedVal === '20ft full') {
                    tarifInput.value = getOppOptTarif('fcl 20ft', isBongkaran) || '';
                } else if (selectedVal === '40ft full') {
                    tarifInput.value = getOppOptTarif('fcl 40ft', isBongkaran) || '';
                }
                calculateTotalFromAllOppOptSections();
            });
        }
        
        tarifInput.addEventListener('input', function() {
            calculateTotalFromAllOppOptSections();
        });
        
        if (jumlahInput) {
            jumlahInput.addEventListener('input', function() {
                calculateTotalFromAllOppOptSections();
            });
        }
    };

    window.addBarangToOppOptSectionWithValue = function(sectionIndex, manifestId, manifestLabel, tarif, vendor, catatan, klasifikasiBiayaId, jenisUkuran = null, jumlah = 0) {
        const section = document.querySelector(`[data-opp-opt-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.opp-opt-barang-container');
        const barangIndex = container.children.length;
        
        const klasifikasiSel = section.querySelector('.opp-opt-klasifikasi-select');
        const isOpslag = klasifikasiSel && klasifikasiSel.value === 'opslag';
        
        let manifestsData = [];
        try {
            manifestsData = JSON.parse(section.getAttribute('data-manifests') || '[]');
        } catch (e) {}

        let barangOptions = '<option value="">Pilih Kontainer / BL</option>';
        
        // Ensure manifestId is an array
        const selectedIds = Array.isArray(manifestId) ? manifestId : (manifestId ? [manifestId] : []);
        
        manifestsData.forEach(manifest => {
            const selected = selectedIds.includes(manifest.id) ? 'selected' : '';
            barangOptions += `<option value="${manifest.id}" ${selected}>${manifest.label}</option>`;
        });
        
        // For multiple, if there are IDs not in manifestsData, we should add them
        selectedIds.forEach(id => {
            if (!manifestsData.find(m => m.id == id)) {
                barangOptions += `<option value="${id}" selected>${manifestLabel || 'Manifest ID: ' + id}</option>`;
            }
        });
        
        let biayaOptions = '<option value="">Pilih Biaya</option>';
        @if(isset($klasifikasiBiayas))
            @foreach($klasifikasiBiayas as $kb)
                biayaOptions += `<option value="{{ $kb->id }}" ${klasifikasiBiayaId == '{{ $kb->id }}' ? 'selected' : ''}>{{ $kb->nama }}</option>`;
            @endforeach
        @endif
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2';
        
        if (isOpslag) {
            inputGroup.innerHTML = `
                <div class="w-[20%]">
                    <label class="block text-[10px] font-medium text-gray-700 mb-1">Biaya</label>
                    <select name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][klasifikasi_biaya_id]" class="opp-opt-biaya-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" required>
                        ${biayaOptions}
                    </select>
                </div>
                <div class="w-[20%]">
                    <label class="block text-[10px] font-medium text-gray-700 mb-1">Jenis Ukuran</label>
                    <select name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][jenis_ukuran]" class="opp-opt-jenis-ukuran-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" required>
                        <option value="">Pilih Jenis</option>
                        <option value="20ft Full" ${jenisUkuran === '20ft Full' ? 'selected' : ''}>20ft Full</option>
                        <option value="20ft Empty" ${jenisUkuran === '20ft Empty' ? 'selected' : ''}>20ft Empty</option>
                        <option value="40ft Full" ${jenisUkuran === '40ft Full' ? 'selected' : ''}>40ft Full</option>
                        <option value="40ft Empty" ${jenisUkuran === '40ft Empty' ? 'selected' : ''}>40ft Empty</option>
                        <option value="LCL 20ft" ${jenisUkuran === 'LCL 20ft' ? 'selected' : ''}>LCL 20ft</option>
                        <option value="LCL 40ft" ${jenisUkuran === 'LCL 40ft' ? 'selected' : ''}>LCL 40ft</option>
                        <option value="Motor" ${jenisUkuran === 'Motor' ? 'selected' : ''}>Motor</option>
                        <option value="Mobil" ${jenisUkuran === 'Mobil' ? 'selected' : ''}>Mobil</option>
                        <option value="Truck" ${jenisUkuran === 'Truck' ? 'selected' : ''}>Truck</option>
                        <option value="Trailer" ${jenisUkuran === 'Trailer' ? 'selected' : ''}>Trailer</option>
                    </select>
                </div>
                <div class="w-[10%]">
                    <label class="block text-[10px] font-medium text-gray-700 mb-1">Jumlah</label>
                    <input type="number" step="any" name="opp_opt_sections[${sectionIndex}][barang][${barangIndex}][jumlah]" class="opp-opt-jumlah-input-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" placeholder="0" value="${jumlah || 0}" required>
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
        } else {
            inputGroup.innerHTML = `
                <div class="w-[20%]">
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
        }
        
        container.appendChild(inputGroup);
        
        if (!isOpslag && typeof $ !== 'undefined' && $.fn.select2) {
            $(inputGroup).find('.opp-opt-barang-select-item').select2({
                width: '100%',
                placeholder: 'Pilih Kontainer / BL (Bisa Lebih Dari 1)',
                allowClear: true
            });
        }
        
        // Add event listeners
        const tarifInput = inputGroup.querySelector('.opp-opt-tarif-input-item');
        const jumlahInput = inputGroup.querySelector('.opp-opt-jumlah-input-item');
        const jenisUkuranSelect = inputGroup.querySelector('.opp-opt-jenis-ukuran-select-item');
        
        if (jenisUkuranSelect && tarifInput) {
            jenisUkuranSelect.addEventListener('change', function() {
                const selectedVal = this.value.toLowerCase();
                const isBongkaran = document.querySelector(`[name="opp_opt_sections[${sectionIndex}][is_bongkaran]"]`)?.checked;
                const tarif = getOppOptTarif(selectedVal, isBongkaran);
                
                if (tarif) {
                    tarifInput.value = tarif;
                } else if (selectedVal === '20ft full') {
                    tarifInput.value = getOppOptTarif('fcl 20ft', isBongkaran) || '';
                } else if (selectedVal === '40ft full') {
                    tarifInput.value = getOppOptTarif('fcl 40ft', isBongkaran) || '';
                }
                calculateTotalFromAllOppOptSections();
            });
        }
        
        tarifInput.addEventListener('input', function() {
            calculateTotalFromAllOppOptSections();
        });
        
        if (jumlahInput) {
            jumlahInput.addEventListener('input', function() {
                calculateTotalFromAllOppOptSections();
            });
        }
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
            const jenisUkuranSelect = group.querySelector('.opp-opt-jenis-ukuran-select-item');
            if (jenisUkuranSelect) {
                jenisUkuranSelect.name = `opp_opt_sections[${sectionIndex}][barang][${newIndex}][jenis_ukuran]`;
            }
            const jumlahInput = group.querySelector('.opp-opt-jumlah-input-item');
            if (jumlahInput) {
                jumlahInput.name = `opp_opt_sections[${sectionIndex}][barang][${newIndex}][jumlah]`;
            }
            const tarifInput = group.querySelector('.opp-opt-tarif-input-item');
            if (tarifInput) {
                tarifInput.name = `opp_opt_sections[${sectionIndex}][barang][${newIndex}][tarif]`;
            }
            const vendorInput = group.querySelector('.opp-opt-vendor-input-item');
            if (vendorInput) {
                vendorInput.name = `opp_opt_sections[${sectionIndex}][barang][${newIndex}][vendor]`;
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
                
                const group = tarifInput.closest('.flex');
                const jumlahInput = group.querySelector('.opp-opt-jumlah-input-item');
                const manifestSelect = group.querySelector('.opp-opt-barang-select-item');
                
                let qty = 0;
                if (jumlahInput) {
                    qty = parseFloat(jumlahInput.value) || 0;
                } else if (manifestSelect) {
                    const selectedOptions = Array.from(manifestSelect.options).filter(opt => opt.selected && opt.value !== "");
                    qty = selectedOptions.length;
                }
                
                sectionTotal += (tarif * qty);
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
