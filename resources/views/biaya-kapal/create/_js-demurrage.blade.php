// ============= DEMURRAGE SECTIONS MANAGEMENT =============
    let demurrageSectionCounter = 0;
    
    // Define empty pricelist data for Demurrage if not provided by controller
    // This allows the section to work even if no master data is available yet
    // Define pricelist data for Demurrage from Meratus Pricelist
    const demurragePricelistData = typeof pricelistMeratusData !== 'undefined' 
        ? pricelistMeratusData.filter(item => item.jenis_biaya === 'DEMURRAGE') 
        : [];
    
    function initializeDemurrageSections() {
        if (!demurrageSectionsContainer) return;
        demurrageSectionsContainer.innerHTML = '';
        demurrageSectionCounter = 0;
        addDemurrageSection();
    }
    
    function clearAllDemurrageSections() {
        if (!demurrageSectionsContainer) return;
        demurrageSectionsContainer.innerHTML = '';
        demurrageSectionCounter = 0;
    }
    
    if (addDemurrageSectionBtn) {
        addDemurrageSectionBtn.addEventListener('click', function() {
            addDemurrageSection();
        });
    }
    
    if (addDemurrageSectionBottomBtn) {
        addDemurrageSectionBottomBtn.addEventListener('click', function() {
            addDemurrageSection();
        });
    }
    
    function addDemurrageSection() {
        if (!demurrageSectionsContainer) return;
        demurrageSectionCounter++;
        const sectionIndex = demurrageSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'demurrage-section mb-6 p-4 border-2 border-indigo-200 rounded-lg bg-indigo-50';
        section.setAttribute('data-demurrage-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });

        // Use distinct locations from Meratus pricelist
        const uniqueLocations = [...new Set(demurragePricelistData.map(item => item.lokasi))];
        let lokasiOptions = '<option value="">-- Pilih Lokasi --</option>';
        uniqueLocations.forEach(lokasi => {
            lokasiOptions += `<option value="${lokasi}">${lokasi}</option>`;
        });
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex} (Demurrage)</h3>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeDemurrageSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="demurrage_sections[${sectionIndex}][kapal]" class="demurrage-kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="demurrage_sections[${sectionIndex}][voyage]" class="demurrage-voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" required disabled>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="demurrage_sections[${sectionIndex}][voyage]" class="demurrage-voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="demurrage-voyage-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Vendor <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <input type="text" name="demurrage_sections[${sectionIndex}][vendor]" class="demurrage-vendor w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" placeholder="Ketik Vendor" value="MERATUS" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Lokasi <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="demurrage_sections[${sectionIndex}][lokasi]" class="demurrage-lokasi-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" ${uniqueLocations.length > 0 ? 'required' : 'disabled'}>
                            ${lokasiOptions}
                        </select>
                        <input type="text" name="demurrage_sections[${sectionIndex}][lokasi_manual]" class="demurrage-lokasi-manual w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 hidden" placeholder="Ketik Lokasi" required>
                        <button type="button" class="demurrage-lokasi-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="mb-4 p-4 bg-white rounded-lg border-2 border-dashed border-indigo-300">
                <label class="block text-sm font-semibold text-gray-800 mb-2">Pilih Kontainer <span class="text-red-500">*</span></label>
                <p class="text-xs text-gray-400 mb-3"><i class="fas fa-info-circle mr-1"></i>Kontainer akan muncul setelah memilih No. Voyage</p>

                <div class="demurrage-kontainer-search-wrap hidden mb-3 relative">
                    <span class="absolute left-3 top-2.5 text-gray-400 text-sm"><i class="fas fa-search"></i></span>
                    <input type="text"
                           class="demurrage-kontainer-search w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Cari nomor kontainer...">
                </div>

                <div class="demurrage-kontainer-loading hidden py-4 text-center text-gray-500 text-sm">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Memuat data kontainer...
                </div>

                <div class="demurrage-kontainer-empty hidden py-4 text-center text-gray-400 text-sm">
                    <i class="fas fa-inbox text-3xl mb-2 block"></i>Tidak ada kontainer untuk voyage ini
                </div>

                <div class="demurrage-kontainer-list space-y-2 max-h-60 overflow-y-auto pr-1"></div>
                <div class="demurrage-kontainer-hidden-inputs"></div>
            </div>
            
            <div class="border-t pt-4 mt-2 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal (DPP) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="demurrage_sections[${sectionIndex}][subtotal]"
                                   class="demurrage-subtotal-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                   placeholder="0" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Materai</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="demurrage_sections[${sectionIndex}][biaya_materai]"
                                   class="demurrage-materai-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-0"
                                   value="0" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PPh 2%</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="demurrage_sections[${sectionIndex}][pph]"
                                   class="demurrage-pph-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                   value="0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adjustment</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="demurrage_sections[${sectionIndex}][adjustment]"
                                   class="demurrage-adjustment-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                   placeholder="0">
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes Adjustment</label>
                        <input type="text" name="demurrage_sections[${sectionIndex}][notes_adjustment]"
                               class="demurrage-notes-adjustment-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                               placeholder="Keterangan adjustment (contoh: Diskon khusus, Koreksi tarif, dll)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Biaya</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="demurrage_sections[${sectionIndex}][total_biaya]"
                                   class="demurrage-total-input w-full pl-10 pr-3 py-2 border border-indigo-300 rounded-lg bg-indigo-50 text-indigo-800 font-bold focus:ring-0 cursor-not-allowed"
                                   value="0" readonly>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        demurrageSectionsContainer.appendChild(section);
        
        // Kapal change listener
        const kapalSelect = section.querySelector('.demurrage-kapal-select');
        kapalSelect.addEventListener('change', function() {
            loadVoyagesForDemurrageSection(sectionIndex, this.value);
        });

        // Setup manual voyage toggle
        const voyageSelect = section.querySelector('.demurrage-voyage-select');
        const voyageInput  = section.querySelector('.demurrage-voyage-input');
        const voyageManualBtn = section.querySelector('.demurrage-voyage-manual-btn');

        voyageManualBtn.addEventListener('click', function() {
            if (voyageInput.classList.contains('hidden')) {
                voyageSelect.classList.add('hidden');
                voyageSelect.disabled = true;
                voyageInput.classList.remove('hidden');
                voyageInput.disabled = false;
                voyageInput.focus();
                this.innerHTML = '<i class="fas fa-list"></i>';
            } else {
                voyageInput.classList.add('hidden');
                voyageInput.disabled = true;
                voyageSelect.classList.remove('hidden');
                voyageSelect.disabled = false;
                this.innerHTML = '<i class="fas fa-keyboard"></i>';
            }
        });
        
        // Setup manual lokasi toggle
        const lokasiSelect     = section.querySelector('.demurrage-lokasi-select');
        const lokasiManualInput = section.querySelector('.demurrage-lokasi-manual');
        const lokasiManualBtn   = section.querySelector('.demurrage-lokasi-manual-btn');

        lokasiManualBtn.addEventListener('click', function() {
            if (lokasiManualInput.classList.contains('hidden')) {
                lokasiSelect.classList.add('hidden');
                lokasiSelect.disabled = true;
                lokasiManualInput.classList.remove('hidden');
                lokasiManualInput.disabled = false;
                lokasiManualInput.focus();
                this.innerHTML = '<i class="fas fa-list"></i>';
            } else {
                lokasiManualInput.classList.add('hidden');
                lokasiManualInput.disabled = true;
                lokasiSelect.classList.remove('hidden');
                lokasiSelect.disabled = false;
                this.innerHTML = '<i class="fas fa-keyboard"></i>';
            }
        });

        // Event listener for lokasi change to trigger subtotal recalc
        lokasiSelect.addEventListener('change', () => calculateDemurrageSectionSubtotal(section));
        lokasiManualInput.addEventListener('input', () => calculateDemurrageSectionSubtotal(section));

        // --- KONTAINER MULTI-SELECT LOGIC ---
        const kontainerList         = section.querySelector('.demurrage-kontainer-list');
        const kontainerLoading      = section.querySelector('.demurrage-kontainer-loading');
        const kontainerEmpty        = section.querySelector('.demurrage-kontainer-empty');
        const hiddenInputsContainer = section.querySelector('.demurrage-kontainer-hidden-inputs');
        const kontainerSearchWrap   = section.querySelector('.demurrage-kontainer-search-wrap');
        const kontainerSearch       = section.querySelector('.demurrage-kontainer-search');

        kontainerSearch.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            kontainerList.querySelectorAll('label').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = (!q || text.includes(q)) ? '' : 'none';
            });
        });

        function loadContainersForDemurrageSection(voyageValue) {
            kontainerList.innerHTML = '';
            hiddenInputsContainer.innerHTML = '';
            kontainerLoading.classList.remove('hidden');
            kontainerEmpty.classList.add('hidden');
            kontainerSearchWrap.classList.add('hidden');
            kontainerSearch.value = '';

            if (!voyageValue) {
                kontainerLoading.classList.add('hidden');
                return;
            }

            fetch(`{{ url('biaya-kapal/get-containers-by-voyage') }}?voyage=${encodeURIComponent(voyageValue)}`)
                .then(res => res.json())
                .then(data => {
                    kontainerLoading.classList.add('hidden');
                    if (!data.success || !data.containers || data.containers.length === 0) {
                        kontainerEmpty.classList.remove('hidden');
                        return;
                    }
                    kontainerSearchWrap.classList.remove('hidden');
                    data.containers.forEach((kontainer, idx) => {
                        const row = document.createElement('label');
                        row.className = 'flex items-center gap-3 p-3 bg-gray-50 hover:bg-indigo-50 rounded-lg cursor-pointer border border-gray-200 hover:border-indigo-300 transition-all';
                        row.innerHTML = `
                            <input type="checkbox"
                                   class="demurrage-kontainer-checkbox w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                   data-bl-id="${kontainer.id}"
                                   data-nomor="${kontainer.nomor_kontainer}"
                                   data-size="${kontainer.size_kontainer}">
                            <div class="flex-1">
                                <div class="font-semibold text-sm text-gray-800">
                                    <i class="fas fa-cube text-indigo-500 mr-1"></i>
                                    ${kontainer.nomor_kontainer}
                                    <span class="ml-2 text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">${kontainer.size_kontainer || '-'}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="text-[10px] text-gray-400 uppercase font-bold">Hari</label>
                                <input type="number" 
                                       class="demurrage-kontainer-hari w-16 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-indigo-500"
                                       value="1" min="1"
                                       data-bl-id="${kontainer.id}">
                            </div>
                        `;

                        const checkbox = row.querySelector('.demurrage-kontainer-checkbox');
                        const hariInput = row.querySelector('.demurrage-kontainer-hari');

                        checkbox.addEventListener('change', function() {
                            const blId = this.dataset.blId;
                            const existingInput = hiddenInputsContainer.querySelector(`[data-bl-id="${blId}"]`);
                            if (this.checked) {
                                if (!existingInput) {
                                    const hiddenGroup = document.createElement('div');
                                    hiddenGroup.setAttribute('data-bl-id', blId);
                                    hiddenGroup.innerHTML = `
                                        <input type="hidden" name="demurrage_sections[${sectionIndex}][kontainer][${blId}][bl_id]" value="${blId}">
                                        <input type="hidden" name="demurrage_sections[${sectionIndex}][kontainer][${blId}][nomor_kontainer]" value="${this.dataset.nomor}">
                                        <input type="hidden" name="demurrage_sections[${sectionIndex}][kontainer][${blId}][size]" value="${this.dataset.size}">
                                        <input type="hidden" name="demurrage_sections[${sectionIndex}][kontainer][${blId}][hari]" class="hari-hidden" value="${hariInput.value}">`;
                                    hiddenInputsContainer.appendChild(hiddenGroup);
                                }
                            } else {
                                if (existingInput) existingInput.remove();
                            }
                            calculateDemurrageSectionSubtotal(section);
                        });

                        hariInput.addEventListener('input', function() {
                            const blId = this.dataset.blId;
                            const existingInput = hiddenInputsContainer.querySelector(`[data-bl-id="${blId}"] .hari-hidden`);
                            if (existingInput) {
                                existingInput.value = this.value;
                            }
                            calculateDemurrageSectionSubtotal(section);
                        });
                        kontainerList.appendChild(row);
                    });
                })
                .catch(e => {
                    kontainerLoading.classList.add('hidden');
                    kontainerList.innerHTML = '<div class="p-3 text-center text-red-500 text-sm"><i class="fas fa-exclamation-triangle mr-1"></i>Gagal memuat kontainer</div>';
                    console.error(e);
                });
        }

        const subtotalInput = section.querySelector('.demurrage-subtotal-input');
        const materaiInput  = section.querySelector('.demurrage-materai-input');
        const pphInput      = section.querySelector('.demurrage-pph-input');
        const adjustmentInput = section.querySelector('.demurrage-adjustment-input');
        const totalInput    = section.querySelector('.demurrage-total-input');

        function calculateDemurrageSectionSubtotal(sec) {
            const lokSelect = sec.querySelector('.demurrage-lokasi-select');
            const lokManual = sec.querySelector('.demurrage-lokasi-manual');
            const lokasi = !lokSelect.disabled ? lokSelect.value : lokManual.value;

            let currentSubtotal = 0;
            const checkboxes = sec.querySelectorAll('.demurrage-kontainer-checkbox:checked');
            
            if (lokasi) {
                checkboxes.forEach(cb => {
                    const blId = cb.dataset.blId;
                    const sizeRaw = cb.dataset.size || ''; 
                    const hari = parseFloat(sec.querySelector(`.demurrage-kontainer-hari[data-bl-id="${blId}"]`).value) || 0;
                    
                    // Normalize size safely (extract 20 or 40)
                    let baseSize = '20'; // Default fallback assumption
                    if (sizeRaw.includes('40')) {
                        baseSize = '40';
                    } else if (sizeRaw.includes('20')) {
                        baseSize = '20';
                    }
                    const normSize = baseSize + 'ft';
                    
                    const pricelist = demurragePricelistData.find(p => {
                        const pLokasi = (p.lokasi || '').toString().toLowerCase();
                        const pSize   = (p.size || '').toString().toLowerCase();
                        return pLokasi === lokasi.toLowerCase() && pSize === normSize.toLowerCase();
                    });
                    
                    if (pricelist) {
                        currentSubtotal += parseFloat(pricelist.harga) * hari;
                    }
                });
            }

            const subField = sec.querySelector('.demurrage-subtotal-input');
            subField.value = currentSubtotal > 0 ? new Intl.NumberFormat('id-ID').format(currentSubtotal) : '0';
            
            // Always trigger total recalculation even if 0
            recalcDemurrageTotal(true);
        }

        function recalcDemurrageTotal(updatePph = false) {
            const subtotal = parseFloat(subtotalInput.value.replace(/\./g, '')) || 0;
            const adjustment = parseFloat(adjustmentInput.value.replace(/\./g, '')) || 0;
            
            // Logic: if subtotal >= 5,000,000 then materai = 10,000
            const materai = subtotal >= 5000000 ? 10000 : 0;
            
            const fmt = (val) => new Intl.NumberFormat('id-ID').format(Math.round(val));
            
            if (updatePph) {
                const pph = Math.round(subtotal * 0.02);
                if (pphInput) pphInput.value = fmt(pph);
            }
            
            const pph = parseFloat(pphInput.value.replace(/\./g, '')) || 0;
            const total = subtotal + materai - pph + adjustment;

            if (materaiInput) materaiInput.value = fmt(materai);
            if (totalInput) totalInput.value = fmt(total);

            calculateTotalFromAllDemurrageSections();
        }

        [subtotalInput, pphInput, adjustmentInput].forEach(el => {
            if (el) {
                el.addEventListener('input', function() {
                    let isNegative = this.value.startsWith('-');
                    let raw = this.value.replace(/[^0-9]/g, '');
                    const num = parseFloat(raw) || 0;
                    let formatted = num > 0 ? new Intl.NumberFormat('id-ID').format(num) : (num === 0 && this.value !== '' ? '0' : '');
                    this.value = (isNegative && num > 0) ? '-' + formatted : formatted;

                    const autoUpdatePph = (this === subtotalInput);
                    recalcDemurrageTotal(autoUpdatePph);
                });
            }
        });

        section._loadContainers = loadContainersForDemurrageSection;
    }

    window.removeDemurrageSection = function(sectionIndex) {
        const section = document.querySelector(`[data-demurrage-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllDemurrageSections();
        }
    };

    function loadVoyagesForDemurrageSection(sectionIndex, kapalNama) {
        const section = document.querySelector(`[data-demurrage-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.demurrage-voyage-select');
        const voyageInput  = section.querySelector('.demurrage-voyage-input');
        
        if (!kapalNama) {
            voyageSelect.disabled = true;
            voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
            return;
        }
        
        voyageSelect.disabled = true;
        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        
        fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(kapalNama)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.voyages) {
                    let html = '<option value="">-- Pilih Voyage --</option>';
                    data.voyages.forEach(voy => html += `<option value="${voy}">${voy}</option>`);
                    voyageSelect.innerHTML = html;
                    voyageSelect.disabled = false;
                    voyageSelect.onchange = function() {
                        if (section._loadContainers) section._loadContainers(this.value);
                    };
                } else {
                    voyageSelect.innerHTML = '<option value="">Tidak ada voyage tersedia</option>';
                }
            })
            .catch(err => {
                console.error('Error fetching voyages:', err);
                voyageSelect.innerHTML = '<option value="">Gagal memuat voyages</option>';
            });

        voyageInput.oninput = function() {
            if (section._loadContainers) {
                clearTimeout(voyageInput._demurrageDebounce);
                voyageInput._demurrageDebounce = setTimeout(() => {
                    section._loadContainers(this.value.trim());
                }, 500);
            }
        };
    }

    function calculateTotalFromAllDemurrageSections() {
        let totalSubtotal = 0;
        document.querySelectorAll('.demurrage-section').forEach(sec => {
            const sub = parseFloat(sec.querySelector('.demurrage-total-input').value.replace(/\./g, '')) || 0;
            totalSubtotal += sub;
        });

        const selectedText = selectedJenisBiaya.nama || '';
        if (selectedText.toLowerCase().includes('demurrage')) {
            if (nominalInput) {
                nominalInput.value = totalSubtotal > 0 ? Math.round(totalSubtotal).toLocaleString('id-ID') : '';
            }
        }
    }
