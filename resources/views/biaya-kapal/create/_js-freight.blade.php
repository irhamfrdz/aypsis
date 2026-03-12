// ============= FREIGHT SECTIONS MANAGEMENT =============
    let freightSectionCounter = 0;
    
    function initializeFreightSections() {
        if (!freightSectionsContainer) return;
        freightSectionsContainer.innerHTML = '';
        freightSectionCounter = 0;
        addFreightSection();
    }
    
    function clearAllFreightSections() {
        if (!freightSectionsContainer) return;
        freightSectionsContainer.innerHTML = '';
        freightSectionCounter = 0;
    }
    
    if (addFreightSectionBtn) {
        addFreightSectionBtn.addEventListener('click', function() {
            addFreightSection();
        });
    }

    if (addFreightSectionBottomBtn) {
        addFreightSectionBottomBtn.addEventListener('click', function() {
            addFreightSection();
        });
    }
    
    function addFreightSection() {
        if (!freightSectionsContainer) return;
        freightSectionCounter++;
        const sectionIndex = freightSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'freight-section mb-6 p-4 border-2 border-indigo-200 rounded-lg bg-indigo-50';
        section.setAttribute('data-freight-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });

        let vendorFreightOptions = '<option value="">-- Pilih Vendor --</option>';
        pricelistFreightVendorsData.forEach(vendor => {
            vendorFreightOptions += `<option value="${vendor}">${vendor}</option>`;
        });
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex} (Freight)</h3>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeFreightSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="freight_sections[${sectionIndex}][kapal]" class="freight-kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="freight_sections[${sectionIndex}][voyage]" class="freight-voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" required disabled>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="freight_sections[${sectionIndex}][voyage]" class="freight-voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="freight-voyage-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Vendor <span class="text-red-500">*</span></label>
                    <select name="freight_sections[${sectionIndex}][vendor]" class="freight-vendor-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" required>
                        ${vendorFreightOptions}
                    </select>
                </div>
            </div>
            
            <div class="mb-4 p-4 bg-white rounded-lg border-2 border-dashed border-indigo-300">
                <label class="block text-sm font-semibold text-gray-800 mb-2">Pilih Kontainer <span class="text-red-500">*</span></label>
                <p class="text-xs text-gray-400 mb-3"><i class="fas fa-info-circle mr-1"></i>Kontainer akan muncul setelah memilih No. Voyage</p>

                <!-- Search box -->
                <div class="freight-kontainer-search-wrap hidden mb-3 relative">
                    <span class="absolute left-3 top-2.5 text-gray-400 text-sm"><i class="fas fa-search"></i></span>
                    <input type="text"
                           class="freight-kontainer-search w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Cari nomor kontainer...">
                </div>

                <!-- Loading indicator -->
                <div class="freight-kontainer-loading hidden py-4 text-center text-gray-500 text-sm">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Memuat data kontainer...
                </div>

                <!-- Empty state -->
                <div class="freight-kontainer-empty hidden py-4 text-center text-gray-400 text-sm">
                    <i class="fas fa-inbox text-3xl mb-2 block"></i>Tidak ada kontainer untuk voyage ini
                </div>

                <!-- Kontainer checklist -->
                <div class="freight-kontainer-list space-y-2 max-h-60 overflow-y-auto pr-1"></div>

                <!-- Hidden inputs container -->
                <div class="freight-kontainer-hidden-inputs"></div>
            </div>
            
            <div class="border-t pt-4 mt-2 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal (Freight) <span class="text-xs text-indigo-500 font-normal">(otomatis)</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="freight_sections[${sectionIndex}][subtotal]"
                                   class="freight-subtotal-input w-full pl-10 pr-3 py-2 border border-indigo-200 rounded-lg bg-indigo-50 text-indigo-800 focus:ring-0 cursor-not-allowed"
                                   value="0" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Dokumen</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="freight_sections[${sectionIndex}][biaya_dokumen]"
                                   class="freight-dokumen-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                   value="0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Materai</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="freight_sections[${sectionIndex}][biaya_materai]"
                                   class="freight-materai-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                   value="0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PPh 2%</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="freight_sections[${sectionIndex}][pph]"
                                   class="freight-pph-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                   value="0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Biaya</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="freight_sections[${sectionIndex}][total_biaya]"
                                   class="freight-total-input w-full pl-10 pr-3 py-2 border border-indigo-300 rounded-lg bg-indigo-50 text-indigo-800 font-bold focus:ring-0 cursor-not-allowed"
                                   value="0" readonly>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        freightSectionsContainer.appendChild(section);
        
        // Setup kapal change listener
        const kapalSelect = section.querySelector('.freight-kapal-select');
        kapalSelect.addEventListener('change', function() {
            loadVoyagesForFreightSection(sectionIndex, this.value);
        });

        // Setup manual voyage toggle
        const voyageSelect = section.querySelector('.freight-voyage-select');
        const voyageInput = section.querySelector('.freight-voyage-input');
        const voyageManualBtn = section.querySelector('.freight-voyage-manual-btn');

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

        // --- KONTAINER MULTI-SELECT LOGIC ---
        const kontainerList         = section.querySelector('.freight-kontainer-list');
        const kontainerLoading      = section.querySelector('.freight-kontainer-loading');
        const kontainerEmpty        = section.querySelector('.freight-kontainer-empty');
        const hiddenInputsContainer = section.querySelector('.freight-kontainer-hidden-inputs');
        const kontainerSearchWrap   = section.querySelector('.freight-kontainer-search-wrap');
        const kontainerSearch       = section.querySelector('.freight-kontainer-search');

        // Filter kontainer list
        kontainerSearch.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            kontainerList.querySelectorAll('label').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = (!q || text.includes(q)) ? '' : 'none';
            });
        });

        function loadContainersForFreightSection(voyageValue) {
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
                                   class="freight-kontainer-checkbox w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                   data-bl-id="${kontainer.id}"
                                   data-nomor="${kontainer.nomor_kontainer}"
                                   data-tipe="${kontainer.tipe_kontainer}"
                                   data-size="${kontainer.size_kontainer}">
                            <div class="flex-1">
                                <div class="font-semibold text-sm text-gray-800">
                                    <i class="fas fa-cube text-indigo-500 mr-1"></i>
                                    ${kontainer.nomor_kontainer}
                                    <span class="ml-2 text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">${kontainer.size_kontainer || '-'}'</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    <span><i class="fas fa-box text-orange-400 mr-1"></i>${kontainer.nama_barang || '-'}</span>
                                </div>
                            </div>
                        `;

                        const checkbox = row.querySelector('.freight-kontainer-checkbox');
                        checkbox.addEventListener('change', function() {
                            const blId = this.dataset.blId;
                            const existingInput = hiddenInputsContainer.querySelector(`[data-bl-id="${blId}"]`);

                            if (this.checked) {
                                if (!existingInput) {
                                    const hiddenGroup = document.createElement('div');
                                    hiddenGroup.setAttribute('data-bl-id', blId);
                                    hiddenGroup.innerHTML = `
                                        <input type="hidden" name="freight_sections[${sectionIndex}][kontainer][${blId}][bl_id]" value="${blId}">
                                        <input type="hidden" name="freight_sections[${sectionIndex}][kontainer][${blId}][nomor_kontainer]" value="${this.dataset.nomor}">
                                        <input type="hidden" name="freight_sections[${sectionIndex}][kontainer][${blId}][size]" value="${this.dataset.size}">`;
                                    hiddenInputsContainer.appendChild(hiddenGroup);
                                }
                            } else {
                                if (existingInput) existingInput.remove();
                            }
                            recalcFreightSubtotal(section, sectionIndex);
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

        // --- CALCULATION LOGIC ---
        function getFreightTarif(vendorName, containerSize) {
            if (!vendorName || !containerSize) return 0;
            const sizeNum = String(containerSize).replace(/[^0-9]/g, '');
            const match = pricelistFreightsData.find(p => {
                const vendorMatch = p.vendor && p.vendor.toLowerCase() === vendorName.toLowerCase();
                const namaBarang  = (p.nama_barang || '').toLowerCase();
                const sizeMatch   = namaBarang.includes(sizeNum + 'ft') || namaBarang.startsWith(sizeNum + 'ft') || namaBarang.includes(sizeNum + ' ft');
                return vendorMatch && sizeMatch;
            });
            return match ? parseFloat(match.tarif) : 0;
        }

        function recalcFreightSubtotal(sec, secIdx) {
            const vendorSelect  = sec.querySelector('.freight-vendor-select');
            const vendorName    = vendorSelect ? vendorSelect.value : '';
            const checkboxes    = sec.querySelectorAll('.freight-kontainer-checkbox:checked');

            let subtotalFreight = 0;
            checkboxes.forEach(cb => {
                const size  = cb.dataset.size || '';
                const tarif = getFreightTarif(vendorName, size);
                subtotalFreight += tarif;
            });

            const fmt = (val) => new Intl.NumberFormat('id-ID').format(Math.round(val));
            const parseFmt = (el) => parseFloat((el ? el.value : '0').replace(/\./g, '').replace(',', '.')) || 0;

            const subtotalInput = sec.querySelector('.freight-subtotal-input');
            const dokumenInput  = sec.querySelector('.freight-dokumen-input');
            const pphInput      = sec.querySelector('.freight-pph-input');
            const materaiInput  = sec.querySelector('.freight-materai-input');
            const totalInput    = sec.querySelector('.freight-total-input');

            const biayaDokumen = parseFmt(dokumenInput);
            
            // Auto-calc Materai: if total (subtotal + dokumen) > 5.000.000 then 10.000
            if (subtotalFreight + biayaDokumen > 5000000) {
                materaiInput.value = fmt(10000);
            }

            // Auto-calc PPh 2% from subtotal
            const pphValue = Math.round(subtotalFreight * 0.02);
            pphInput.value = fmt(pphValue);
            
            subtotalInput.value = fmt(subtotalFreight);
            
            const materaiValue = parseFmt(materaiInput);
            const totalValue = subtotalFreight + biayaDokumen + materaiValue - pphValue;
            totalInput.value = fmt(totalValue);

            calculateTotalFromAllFreightSections();
        }

        const vendorSelectEl = section.querySelector('.freight-vendor-select');
        if (vendorSelectEl) {
            vendorSelectEl.addEventListener('change', function() {
                recalcFreightSubtotal(section, sectionIndex);
            });
        }

        function attachInputListener(inputEl) {
            if (!inputEl) return;
            inputEl.addEventListener('input', function() {
                let raw = this.value.replace(/[^0-9]/g, '');
                const num = parseFloat(raw) || 0;
                this.value = num > 0 ? new Intl.NumberFormat('id-ID').format(num) : (num === 0 && this.value !== '' ? '0' : '');
                recalcFreightSubtotal(section, sectionIndex);
            });
        }
        attachInputListener(section.querySelector('.freight-dokumen-input'));
        attachInputListener(section.querySelector('.freight-materai-input'));
        attachInputListener(section.querySelector('.freight-pph-input'));

        recalcFreightSubtotal(section, sectionIndex);
        section._loadContainers = loadContainersForFreightSection;
    }
    
    window.removeFreightSection = function(sectionIndex) {
        const section = document.querySelector(`[data-freight-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllFreightSections();
        }
    };
    
    function loadVoyagesForFreightSection(sectionIndex, kapalNama) {
        const section = document.querySelector(`[data-freight-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.freight-voyage-select');
        const voyageInput  = section.querySelector('.freight-voyage-input');
        
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
                    data.voyages.forEach(v => html += `<option value="${v}">${v}</option>`);
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
                clearTimeout(voyageInput._freightDebounce);
                voyageInput._freightDebounce = setTimeout(() => {
                    section._loadContainers(this.value.trim());
                }, 500);
            }
        };
    }

    function calculateTotalFromAllFreightSections() {
        let totalFinal = 0;
        document.querySelectorAll('.freight-section').forEach(sec => {
            const total = parseFloat(sec.querySelector('.freight-total-input').value.replace(/\./g, '')) || 0;
            totalFinal += total;
        });

        const selectedText = selectedJenisBiaya.nama || '';
        if (selectedText.toLowerCase().includes('freight')) {
            if (nominalInput) {
                nominalInput.value = totalFinal > 0 ? Math.round(totalFinal).toLocaleString('id-ID') : '';
            }
        }
    }
