// ============= NOTA RETUR SECTIONS MANAGEMENT =============
    let notaReturSectionCounter = 0;
    
    // Define empty pricelist data for Nota Retur
    const notaReturPricelistData = typeof pricelistMeratusData !== 'undefined' 
        ? pricelistMeratusData.filter(item => (item.jenis_biaya || '').toString().toLowerCase().includes('retur')) 
        : [];
    
    function initializeNotaReturSections() {
        const container = document.getElementById('nota_retur_sections_container');
        if (!container) return;
        container.innerHTML = '';
        notaReturSectionCounter = 0;
        addNotaReturSection();
    }
    
    function clearAllNotaReturSections() {
        const container = document.getElementById('nota_retur_sections_container');
        if (!container) return;
        container.innerHTML = '';
        notaReturSectionCounter = 0;
    }
    
    const addNotaReturSectionBtn = document.getElementById('add_nota_retur_section_btn');
    if (addNotaReturSectionBtn) {
        addNotaReturSectionBtn.addEventListener('click', function() {
            addNotaReturSection();
        });
    }
    
    const addNotaReturSectionBottomBtn = document.getElementById('add_nota_retur_section_bottom_btn');
    if (addNotaReturSectionBottomBtn) {
        addNotaReturSectionBottomBtn.addEventListener('click', function() {
            addNotaReturSection();
        });
    }
    
    function addNotaReturSection(data = null) {
        const container = document.getElementById('nota_retur_sections_container');
        if (!container) return;
        notaReturSectionCounter++;
        const sectionIndex = notaReturSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'nota-retur-section mb-6 p-4 border-2 border-indigo-200 rounded-lg bg-indigo-50';
        section.setAttribute('data-nota-retur-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            const selected = (data && data.kapal === kapal.nama_kapal) ? 'selected' : '';
            kapalOptions += `<option value="${kapal.nama_kapal}" ${selected}>${kapal.nama_kapal}</option>`;
        });

        // Use distinct locations from pricelist if available
        const uniqueLocations = [...new Set(notaReturPricelistData.map(item => item.lokasi))];
        let lokasiOptions = '<option value="">-- Pilih Lokasi --</option>';
        uniqueLocations.forEach(lokasi => {
            const selected = (data && data.lokasi === lokasi) ? 'selected' : '';
            lokasiOptions += `<option value="${lokasi}" ${selected}>${lokasi}</option>`;
        });
        
        const showLokasiManual = data ? (data.lokasi && !uniqueLocations.includes(data.lokasi)) : (uniqueLocations.length === 0);
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex} (Nota Retur)</h3>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeNotaReturSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="nota_retur_sections[${sectionIndex}][kapal]" class="nota-retur-kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="nota_retur_sections[${sectionIndex}][voyage]" class="nota-retur-voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" required disabled>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="nota_retur_sections[${sectionIndex}][voyage]" class="nota-retur-voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="nota-retur-voyage-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Vendor <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <input type="text" name="nota_retur_sections[${sectionIndex}][vendor]" class="nota-retur-vendor w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" placeholder="Ketik Vendor" value="${data ? (data.vendor || '') : 'MERATUS'}" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Lokasi <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="nota_retur_sections[${sectionIndex}][lokasi]" class="nota-retur-lokasi-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" ${showLokasiManual ? 'disabled' : ''}>
                            ${lokasiOptions}
                        </select>
                        <input type="text" name="nota_retur_sections[${sectionIndex}][lokasi_manual]" class="nota-retur-lokasi-manual w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 ${showLokasiManual ? '' : 'hidden'}" placeholder="Ketik Lokasi" value="${data ? (data.lokasi || '') : ''}" required ${showLokasiManual ? '' : 'disabled'}>
                        <button type="button" class="nota-retur-lokasi-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Penerima <span class="text-red-500">*</span></label>
                    <input type="text" name="nota_retur_sections[${sectionIndex}][penerima]" class="nota-retur-penerima w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" placeholder="Nama Penerima" value="${data ? (data.penerima || '') : ''}" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Rekening</label>
                    <input type="text" name="nota_retur_sections[${sectionIndex}][rekening]" class="nota-retur-rekening w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" placeholder="Nomor Rekening" value="${data ? (data.rekening || '') : ''}">
                </div>
            </div>
            
            <div class="mb-4 p-4 bg-white rounded-lg border-2 border-dashed border-indigo-300">
                <label class="block text-sm font-semibold text-gray-800 mb-2">Pilih Kontainer <span class="text-red-500">*</span></label>
                <p class="text-xs text-gray-400 mb-3"><i class="fas fa-info-circle mr-1"></i>Kontainer akan muncul setelah memilih No. Voyage</p>

                <div class="nota-retur-kontainer-search-wrap hidden mb-3 relative">
                    <span class="absolute left-3 top-2.5 text-gray-400 text-sm"><i class="fas fa-search"></i></span>
                    <input type="text"
                           class="nota-retur-kontainer-search w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Cari nomor kontainer...">
                </div>

                <div class="nota-retur-kontainer-loading hidden py-4 text-center text-gray-500 text-sm">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Memuat data kontainer...
                </div>

                <div class="nota-retur-kontainer-empty hidden py-4 text-center text-gray-400 text-sm">
                    <i class="fas fa-inbox text-3xl mb-2 block"></i>Tidak ada kontainer untuk voyage ini
                </div>

                <div class="nota-retur-kontainer-list space-y-2 max-h-60 overflow-y-auto pr-1"></div>
                <div class="nota-retur-kontainer-hidden-inputs"></div>
            </div>
            
            <div class="border-t pt-4 mt-2 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal (DPP) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="nota_retur_sections[${sectionIndex}][subtotal]"
                                   class="nota-retur-subtotal-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                   placeholder="0" value="${data ? new Intl.NumberFormat('id-ID').format(Math.round(data.subtotal)) : ''}" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Materai</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="nota_retur_sections[${sectionIndex}][biaya_materai]"
                                   class="nota-retur-materai-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-0"
                                   value="${data ? new Intl.NumberFormat('id-ID').format(Math.round(data.biaya_materai)) : '0'}" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PPh 2%</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="nota_retur_sections[${sectionIndex}][pph]"
                                   class="nota-retur-pph-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                   value="${data ? new Intl.NumberFormat('id-ID').format(Math.round(data.pph)) : '0'}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adjustment</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="nota_retur_sections[${sectionIndex}][adjustment]"
                                   class="nota-retur-adjustment-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                   value="${data ? new Intl.NumberFormat('id-ID').format(Math.round(data.adjustment)) : '0'}">
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes Adjustment</label>
                        <input type="text" name="nota_retur_sections[${sectionIndex}][notes_adjustment]"
                               class="nota-retur-notes-adjustment-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                               placeholder="Keterangan adjustment (contoh: Diskon khusus, Koreksi tarif, dll)"
                               value="${data ? (data.notes_adjustment || '') : ''}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Biaya</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="nota_retur_sections[${sectionIndex}][total_biaya]"
                                   class="nota-retur-total-input w-full pl-10 pr-3 py-2 border border-indigo-300 rounded-lg bg-indigo-50 text-indigo-800 font-bold focus:ring-0 cursor-not-allowed"
                                   value="${data ? new Intl.NumberFormat('id-ID').format(Math.round(data.total_biaya)) : '0'}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(section);
        
        // Kapal change listener
        const kapalSelect = section.querySelector('.nota-retur-kapal-select');
        kapalSelect.addEventListener('change', function() {
            loadVoyagesForNotaReturSection(sectionIndex, this.value);
        });

        // Setup manual voyage toggle
        const voyageSelect = section.querySelector('.nota-retur-voyage-select');
        const voyageInput  = section.querySelector('.nota-retur-voyage-input');
        const voyageManualBtn = section.querySelector('.nota-retur-voyage-manual-btn');

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
        const lokasiSelect     = section.querySelector('.nota-retur-lokasi-select');
        const lokasiManualInput = section.querySelector('.nota-retur-lokasi-manual');
        const lokasiManualBtn   = section.querySelector('.nota-retur-lokasi-manual-btn');

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

        if (showLokasiManual) {
            lokasiSelect.classList.add('hidden');
            lokasiSelect.disabled = true;
            lokasiManualInput.classList.remove('hidden');
            lokasiManualInput.disabled = false;
            lokasiManualBtn.innerHTML = '<i class="fas fa-list"></i>';
        }

        // Event listener for lokasi change to trigger subtotal recalc
        lokasiSelect.addEventListener('change', () => calculateNotaReturSectionSubtotal(section));
        lokasiManualInput.addEventListener('input', () => calculateNotaReturSectionSubtotal(section));

        // --- KONTAINER MULTI-SELECT LOGIC ---
        const kontainerList         = section.querySelector('.nota-retur-kontainer-list');
        const kontainerLoading      = section.querySelector('.nota-retur-kontainer-loading');
        const kontainerEmpty        = section.querySelector('.nota-retur-kontainer-empty');
        const hiddenInputsContainer = section.querySelector('.nota-retur-kontainer-hidden-inputs');
        const kontainerSearchWrap   = section.querySelector('.nota-retur-kontainer-search-wrap');
        const kontainerSearch       = section.querySelector('.nota-retur-kontainer-search');

        kontainerSearch.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            kontainerList.querySelectorAll('label').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = (!q || text.includes(q)) ? '' : 'none';
            });
        });

        function loadContainersForNotaReturSection(voyageValue, selectedContainers = []) {
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
                        const savedMatch = selectedContainers.find(sc => String(sc.bl_id) === String(kontainer.id));
                        const isChecked = savedMatch ? 'checked' : '';
                        const savedHari = savedMatch ? savedMatch.hari : 1;

                        const row = document.createElement('label');
                        row.className = 'flex items-center gap-3 p-3 bg-gray-50 hover:bg-indigo-50 rounded-lg cursor-pointer border border-gray-200 hover:border-indigo-300 transition-all';
                        row.innerHTML = `
                            <input type="checkbox"
                                   class="nota-retur-kontainer-checkbox w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                   data-bl-id="${kontainer.id}"
                                   data-nomor="${kontainer.nomor_kontainer}"
                                   data-size="${kontainer.size_kontainer}"
                                   ${isChecked}>
                            <div class="flex-1">
                                <div class="font-semibold text-sm text-gray-800">
                                    <i class="fas fa-cube text-indigo-500 mr-1"></i>
                                    ${kontainer.nomor_kontainer}
                                    <span class="ml-2 text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">${kontainer.size_kontainer || '-'}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="text-[10px] text-gray-400 uppercase font-bold">Qty</label>
                                <input type="number" 
                                       class="nota-retur-kontainer-hari w-16 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-indigo-500"
                                       value="${savedHari}" min="1"
                                       data-bl-id="${kontainer.id}">
                            </div>
                        `;

                        const checkbox = row.querySelector('.nota-retur-kontainer-checkbox');
                        const hariInput = row.querySelector('.nota-retur-kontainer-hari');

                        const normalizeNotaReturSize = (val) => {
                            const raw = (val || '').toString();
                            if (raw.includes('40')) return '40ft';
                            return '20ft'; 
                        };

                        const handleCheckboxChange = () => {
                            const blId = checkbox.dataset.blId;
                            const existingInput = hiddenInputsContainer.querySelector(`[data-bl-id="${blId}"]`);
                            if (checkbox.checked) {
                                if (!existingInput) {
                                    const normSize = normalizeNotaReturSize(checkbox.dataset.size);
                                    const hiddenGroup = document.createElement('div');
                                    hiddenGroup.setAttribute('data-bl-id', blId);
                                    hiddenGroup.innerHTML = `
                                        <input type="hidden" name="nota_retur_sections[${sectionIndex}][kontainer][${blId}][bl_id]" value="${blId}">
                                        <input type="hidden" name="nota_retur_sections[${sectionIndex}][kontainer][${blId}][nomor_kontainer]" value="${checkbox.dataset.nomor}">
                                        <input type="hidden" name="nota_retur_sections[${sectionIndex}][kontainer][${blId}][size]" value="${normSize}">
                                        <input type="hidden" name="nota_retur_sections[${sectionIndex}][kontainer][${blId}][hari]" class="hari-hidden" value="${hariInput.value}">`;
                                    hiddenInputsContainer.appendChild(hiddenGroup);
                                }
                            } else {
                                if (existingInput) existingInput.remove();
                            }
                        };

                        checkbox.addEventListener('change', function() {
                            handleCheckboxChange();
                            calculateNotaReturSectionSubtotal(section);
                        });

                        hariInput.addEventListener('input', function() {
                            const blId = this.dataset.blId;
                            const existingInput = hiddenInputsContainer.querySelector(`[data-bl-id="${blId}"] .hari-hidden`);
                            if (existingInput) {
                                existingInput.value = this.value;
                            }
                            calculateNotaReturSectionSubtotal(section);
                        });
                        
                        kontainerList.appendChild(row);
                        
                        if (checkbox.checked) {
                            handleCheckboxChange();
                        }
                    });
                })
                .catch(e => {
                    kontainerLoading.classList.add('hidden');
                    kontainerList.innerHTML = '<div class="p-3 text-center text-red-500 text-sm"><i class="fas fa-exclamation-triangle mr-1"></i>Gagal memuat kontainer</div>';
                    console.error(e);
                });
        }

        const subtotalInput = section.querySelector('.nota-retur-subtotal-input');
        const materaiInput  = section.querySelector('.nota-retur-materai-input');
        const pphInput      = section.querySelector('.nota-retur-pph-input');
        const adjustmentInput = section.querySelector('.nota-retur-adjustment-input');
        const totalInput    = section.querySelector('.nota-retur-total-input');

        function calculateNotaReturSectionSubtotal(sec) {
            const lokSelect = sec.querySelector('.nota-retur-lokasi-select');
            const lokManual = sec.querySelector('.nota-retur-lokasi-manual');
            const lokasi = !lokSelect.disabled ? lokSelect.value : lokManual.value;

            let currentSubtotal = 0;
            const checkboxes = sec.querySelectorAll('.nota-retur-kontainer-checkbox:checked');
            
            if (lokasi && notaReturPricelistData.length > 0) {
                checkboxes.forEach(cb => {
                    const blId = cb.dataset.blId;
                    const sizeRaw = cb.dataset.size || ''; 
                    const hari = parseFloat(sec.querySelector(`.nota-retur-kontainer-hari[data-bl-id="${blId}"]`).value) || 0;
                    
                    let baseSize = '20';
                    if (sizeRaw.includes('40')) {
                        baseSize = '40';
                    } else if (sizeRaw.includes('20')) {
                        baseSize = '20';
                    }
                    const normSize = baseSize + 'ft';
                    
                    const pricelist = notaReturPricelistData.find(p => {
                        const pLokasi = (p.lokasi || '').toString().toLowerCase();
                        const pSize   = (p.size || '').toString().toLowerCase();
                        return pLokasi === lokasi.toLowerCase() && pSize === normSize.toLowerCase();
                    });
                    
                    if (pricelist) {
                        currentSubtotal += parseFloat(pricelist.harga) * hari;
                    }
                });
            }

            // Only update subtotal if we have pricelist matches to avoid resetting manual input
            if (currentSubtotal > 0) {
                const subField = sec.querySelector('.nota-retur-subtotal-input');
                subField.value = new Intl.NumberFormat('id-ID').format(currentSubtotal);
            }
            
            recalcNotaReturTotal(true);
        }

        function recalcNotaReturTotal(updatePph = false) {
            const subtotal = parseFloat(subtotalInput.value.replace(/\./g, '')) || 0;
            const adjustment = parseFloat(adjustmentInput.value.replace(/\./g, '')) || 0;
            
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

            calculateTotalFromAllNotaReturSections();
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
                    recalcNotaReturTotal(autoUpdatePph);
                });
            }
        });

        section._loadContainers = loadContainersForNotaReturSection;
        
        if (data && data.kapal) {
            loadVoyagesForNotaReturSection(sectionIndex, data.kapal, data.voyage, data.kontainer);
        }
    }

    window.removeNotaReturSection = function(sectionIndex) {
        const section = document.querySelector(`[data-nota-retur-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllNotaReturSections();
        }
    };

    function loadVoyagesForNotaReturSection(sectionIndex, kapalNama, targetVoyage = null, targetContainers = []) {
        const section = document.querySelector(`[data-nota-retur-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.nota-retur-voyage-select');
        const voyageInput  = section.querySelector('.nota-retur-voyage-input');
        
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
                    data.voyages.forEach(voy => {
                        const selected = (targetVoyage && String(voy) === String(targetVoyage)) ? 'selected' : '';
                        html += `<option value="${voy}" ${selected}>${voy}</option>`;
                    });
                    
                    if (targetVoyage && !data.voyages.includes(targetVoyage)) {
                        html += `<option value="${targetVoyage}" selected>${targetVoyage}</option>`;
                    }
                    
                    voyageSelect.innerHTML = html;
                    voyageSelect.disabled = false;
                    voyageSelect.onchange = function() {
                        if (section._loadContainers) section._loadContainers(this.value);
                    };
                    
                    if (targetVoyage) {
                        if (section._loadContainers) section._loadContainers(targetVoyage, targetContainers);
                    }
                } else {
                    if (targetVoyage) {
                        voyageSelect.innerHTML = `<option value="${targetVoyage}" selected>${targetVoyage}</option>`;
                        voyageSelect.disabled = false;
                        if (section._loadContainers) section._loadContainers(targetVoyage, targetContainers);
                    } else {
                        voyageSelect.innerHTML = '<option value="">Tidak ada voyage tersedia</option>';
                    }
                }
            })
            .catch(err => {
                console.error('Error fetching voyages:', err);
                if (targetVoyage) {
                    voyageSelect.innerHTML = `<option value="${targetVoyage}" selected>${targetVoyage}</option>`;
                    voyageSelect.disabled = false;
                    if (section._loadContainers) section._loadContainers(targetVoyage, targetContainers);
                } else {
                    voyageSelect.innerHTML = '<option value="">Gagal memuat voyages</option>';
                }
            });

        voyageInput.oninput = function() {
            if (section._loadContainers) {
                clearTimeout(voyageInput._notaReturDebounce);
                voyageInput._notaReturDebounce = setTimeout(() => {
                    section._loadContainers(this.value.trim());
                }, 500);
            }
        };
    }

    function calculateTotalFromAllNotaReturSections() {
        let totalSubtotal = 0;
        document.querySelectorAll('.nota-retur-section').forEach(sec => {
            const sub = parseFloat(sec.querySelector('.nota-retur-total-input').value.replace(/\./g, '')) || 0;
            totalSubtotal += sub;
        });

        const selectedText = selectedJenisBiaya.nama || '';
        if (selectedText.toLowerCase().includes('retur')) {
            if (nominalInput) {
                nominalInput.value = totalSubtotal > 0 ? Math.round(totalSubtotal).toLocaleString('id-ID') : '';
            }
        }
    }
