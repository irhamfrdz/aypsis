// ============= THC SECTIONS MANAGEMENT =============
    let thcSectionCounter = 0;
    
    function initializeTHCSections() {
        if (!thcSectionsContainer) return;
        thcSectionsContainer.innerHTML = '';
        thcSectionCounter = 0;
        addTHCSection();
    }
    
    function clearAllTHCSections() {
        if (!thcSectionsContainer) return;
        thcSectionsContainer.innerHTML = '';
        thcSectionCounter = 0;
    }
    
    if (addThcSectionBtn) {
        addThcSectionBtn.addEventListener('click', function() {
            addTHCSection();
        });
    }

    if (addThcSectionBottomBtn) {
        addThcSectionBottomBtn.addEventListener('click', function() {
            addTHCSection();
        });
    }
    
    function addTHCSection() {
        if (!thcSectionsContainer) return;
        thcSectionCounter++;
        const sectionIndex = thcSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'thc-section mb-6 p-4 border-2 border-teal-200 rounded-lg bg-teal-50';
        section.setAttribute('data-thc-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });

        let vendorThcOptions = '<option value="">-- Pilih Vendor --</option>';
        pricelistThcVendorsData.forEach(vendor => {
            vendorThcOptions += `<option value="${vendor}">${vendor}</option>`;
        });
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex} (THC)</h3>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeTHCSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="thc_sections[${sectionIndex}][kapal]" class="thc-kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-teal-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="thc_sections[${sectionIndex}][voyage]" class="thc-voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-teal-500" required disabled>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="thc_sections[${sectionIndex}][voyage]" class="thc-voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-teal-500 hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="thc-voyage-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-700 mb-1">Vendor <span class="text-red-500">*</span></label>
                <select name="thc_sections[${sectionIndex}][vendor]" class="thc-vendor-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-teal-500" required>
                    ${vendorThcOptions}
                </select>
            </div>
            
            <div class="mb-4 p-4 bg-white rounded-lg border-2 border-dashed border-teal-300">
                <label class="block text-sm font-semibold text-gray-800 mb-2">Pilih Kontainer <span class="text-red-500">*</span></label>
                <p class="text-xs text-gray-400 mb-3"><i class="fas fa-info-circle mr-1"></i>Kontainer akan muncul setelah memilih No. Voyage</p>

                <!-- Search box -->
                <div class="thc-kontainer-search-wrap hidden mb-3 relative">
                    <span class="absolute left-3 top-2.5 text-gray-400 text-sm"><i class="fas fa-search"></i></span>
                    <input type="text"
                           class="thc-kontainer-search w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                           placeholder="Cari nomor kontainer...">
                </div>

                <!-- Loading indicator -->
                <div class="thc-kontainer-loading hidden py-4 text-center text-gray-500 text-sm">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Memuat data kontainer...
                </div>

                <!-- Empty state -->
                <div class="thc-kontainer-empty hidden py-4 text-center text-gray-400 text-sm">
                    <i class="fas fa-inbox text-3xl mb-2 block"></i>Tidak ada kontainer untuk voyage ini
                </div>

                <!-- Kontainer checklist -->
                <div class="thc-kontainer-list space-y-2 max-h-60 overflow-y-auto pr-1"></div>

                <!-- Hidden inputs container -->
                <div class="thc-kontainer-hidden-inputs"></div>
            </div>
            
            <div class="border-t pt-4 mt-2 space-y-3">
                <!-- Row 1: Subtotal + Dokumen Muat + Dokumen Bongkar -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal THC <span class="text-xs text-teal-500 font-normal">(otomatis)</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="thc_sections[${sectionIndex}][subtotal]"
                                   class="thc-subtotal-input w-full pl-10 pr-3 py-2 border border-teal-200 rounded-lg bg-teal-50 text-teal-800 focus:ring-0 cursor-not-allowed"
                                   value="0" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Dokumen Muat</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="thc_sections[${sectionIndex}][biaya_dokumen_muat]"
                                   class="thc-dok-muat-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                                   value="200.000">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Dokumen Bongkar</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="thc_sections[${sectionIndex}][biaya_dokumen_bongkar]"
                                   class="thc-dok-bongkar-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                                   value="200.000">
                        </div>
                    </div>
                </div>

                <!-- Row 2: Materai (kondisional) + Total -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div class="thc-materai-wrap hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Biaya Materai
                            <span class="text-xs text-amber-500 font-normal">(total &gt; Rp 5 jt)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="thc_sections[${sectionIndex}][biaya_materai]"
                                   class="thc-materai-input w-full pl-10 pr-3 py-2 border border-amber-200 rounded-lg bg-amber-50 text-amber-800 focus:ring-0 cursor-not-allowed"
                                   value="10.000" readonly>
                        </div>
                    </div>
                    <div class="hidden">
                        <input type="hidden" name="thc_sections[${sectionIndex}][pph]" class="thc-pph-input" value="0">
                    </div>
                    <div class="md:col-start-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Biaya</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="thc_sections[${sectionIndex}][total_biaya]"
                                   class="thc-total-input w-full pl-10 pr-3 py-2 border border-blue-300 rounded-lg bg-blue-50 text-blue-800 font-bold focus:ring-0 cursor-not-allowed"
                                   value="0" readonly>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        thcSectionsContainer.appendChild(section);
        
        // Setup kapal change listener
        const kapalSelect = section.querySelector('.thc-kapal-select');
        kapalSelect.addEventListener('change', function() {
            loadVoyagesForTHCSection(sectionIndex, this.value);
        });

        // Setup manual voyage toggle
        const voyageSelect = section.querySelector('.thc-voyage-select');
        const voyageInput = section.querySelector('.thc-voyage-input');
        const voyageManualBtn = section.querySelector('.thc-voyage-manual-btn');

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

        // --- KONTAINER MULTI-SELECT LOGIC (loaded by voyage) ---
        const kontainerList         = section.querySelector('.thc-kontainer-list');
        const kontainerLoading      = section.querySelector('.thc-kontainer-loading');
        const kontainerEmpty        = section.querySelector('.thc-kontainer-empty');
        const hiddenInputsContainer = section.querySelector('.thc-kontainer-hidden-inputs');
        const kontainerSearchWrap   = section.querySelector('.thc-kontainer-search-wrap');
        const kontainerSearch       = section.querySelector('.thc-kontainer-search');

        // Filter kontainer list saat mengetik
        kontainerSearch.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            kontainerList.querySelectorAll('label').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = (!q || text.includes(q)) ? '' : 'none';
            });
        });

        function loadContainersForTHCSection(voyageValue) {
            // Clear previous state
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

                    // Tampilkan search box karena ada data
                    kontainerSearchWrap.classList.remove('hidden');
                    kontainerSearch.focus();

                    data.containers.forEach((kontainer, idx) => {
                        const row = document.createElement('label');
                        row.className = 'flex items-center gap-3 p-3 bg-gray-50 hover:bg-teal-50 rounded-lg cursor-pointer border border-gray-200 hover:border-teal-300 transition-all';
                        row.innerHTML = `
                            <input type="checkbox"
                                   class="thc-kontainer-checkbox w-4 h-4 rounded text-teal-600 focus:ring-teal-500 cursor-pointer"
                                   data-bl-id="${kontainer.id}"
                                   data-nomor="${kontainer.nomor_kontainer}"
                                   data-seal="${kontainer.no_seal}"
                                   data-tipe="${kontainer.tipe_kontainer}"
                                   data-size="${kontainer.size_kontainer}">
                            <div class="flex-1">
                                <div class="font-semibold text-sm text-gray-800">
                                    <i class="fas fa-cube text-teal-500 mr-1"></i>
                                    ${kontainer.nomor_kontainer}
                                    <span class="ml-2 text-xs bg-teal-100 text-teal-700 px-2 py-0.5 rounded-full">${kontainer.size_kontainer || '-'}'</span>
                                    <span class="ml-1 text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">${kontainer.tipe_kontainer || '-'}</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    <span class="mr-3"><i class="fas fa-lock text-gray-400 mr-1"></i>Seal: ${kontainer.no_seal || '-'}</span>
                                    <span><i class="fas fa-box text-orange-400 mr-1"></i>${kontainer.nama_barang || '-'}</span>
                                </div>
                            </div>
                        `;

                        const checkbox = row.querySelector('.thc-kontainer-checkbox');
                        checkbox.addEventListener('change', function() {
                            const blId = this.dataset.blId;
                            const existingInput = hiddenInputsContainer.querySelector(`[data-bl-id="${blId}"]`);

                            if (this.checked) {
                                if (!existingInput) {
                                    const hiddenGroup = document.createElement('div');
                                    hiddenGroup.setAttribute('data-bl-id', blId);
                                     hiddenGroup.innerHTML = `<input type="hidden" name="thc_sections[${sectionIndex}][kontainer][${blId}][bl_id]" value="${blId}">
                                    <input type="hidden" name="thc_sections[${sectionIndex}][kontainer][${blId}][nomor_kontainer]" value="${this.dataset.nomor}">
                                    <input type="hidden" name="thc_sections[${sectionIndex}][kontainer][${blId}][size]" value="${this.dataset.size}">`;
                                    hiddenInputsContainer.appendChild(hiddenGroup);
                                }
                            } else {
                                if (existingInput) existingInput.remove();
                            }

                            // Auto recalculate subtotal
                            recalcThcSubtotal(section, sectionIndex);
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

        // --- SUBTOTAL AUTO CALCULATION ---
        // Find tarif from pricelist_thcs matching vendor + container size
        function getThcTarif(vendorName, containerSize) {
            if (!vendorName || !containerSize) return 0;

            // Normalize size: could be "20", "40", "20ft", "40ft", "20FT", etc.
            const sizeNum = String(containerSize).replace(/[^0-9]/g, '');

            // Find matching pricelist entry: vendor matches AND nama_barang contains the size number
            const match = pricelistThcsData.find(p => {
                const vendorMatch = p.vendor && p.vendor.toLowerCase() === vendorName.toLowerCase();
                const namaBarang  = (p.nama_barang || '').toLowerCase();
                const sizeMatch   = namaBarang.includes(sizeNum + 'ft') || namaBarang.startsWith(sizeNum + 'ft') || namaBarang.includes(sizeNum + ' ft');
                return vendorMatch && sizeMatch;
            });

            return match ? parseFloat(match.tarif) : 0;
        }

        function recalcThcSubtotal(sec, secIdx) {
            const vendorSelect  = sec.querySelector('.thc-vendor-select');
            const vendorName    = vendorSelect ? vendorSelect.value : '';
            const checkboxes    = sec.querySelectorAll('.thc-kontainer-checkbox:checked');

            // 1. Subtotal THC (dari tarif kontainer)
            let subtotalThc = 0;
            checkboxes.forEach(cb => {
                const size  = cb.dataset.size || '';
                const tarif = getThcTarif(vendorName, size);
                subtotalThc += tarif;
            });

            // 2. Biaya dokumen muat & bongkar (bisa diubah user)
            const parseFmt = (el) => parseFloat((el ? el.value : '0').replace(/\./g, '').replace(',', '.')) || 0;
            const dokMuatInput    = sec.querySelector('.thc-dok-muat-input');
            const dokBongkarInput = sec.querySelector('.thc-dok-bongkar-input');
            const biayaDokMuat    = parseFmt(dokMuatInput);
            const biayaDokBongkar = parseFmt(dokBongkarInput);

            // 3. Total sebelum materai
            const totalSebelumMaterai = subtotalThc + biayaDokMuat + biayaDokBongkar;

            // 4. Materai kondisional: muncul jika total > 5.000.000
            const MATERAI          = 10000;
            const BATAS_MATERAI    = 5000000;
            const materaiWrap      = sec.querySelector('.thc-materai-wrap');
            const materaiInput     = sec.querySelector('.thc-materai-input');
            const kenaMaterai      = totalSebelumMaterai > BATAS_MATERAI;

            if (kenaMaterai) {
                materaiWrap.classList.remove('hidden');
            } else {
                materaiWrap.classList.add('hidden');
            }
            const biayaMaterai = kenaMaterai ? MATERAI : 0;

            // 5. Total akhir
            const totalAkhir = totalSebelumMaterai + biayaMaterai;

            const fmt = (val) => new Intl.NumberFormat('id-ID').format(Math.round(val));

            sec.querySelector('.thc-subtotal-input').value = fmt(subtotalThc);
            sec.querySelector('.thc-pph-input').value      = fmt(0);
            sec.querySelector('.thc-total-input').value    = fmt(totalAkhir);
            if (materaiInput) materaiInput.value           = fmt(MATERAI);

            calculateTotalFromAllTHCSections();
        }

        // Re-calculate when vendor changes
        const vendorSelectEl = section.querySelector('.thc-vendor-select');
        if (vendorSelectEl) {
            vendorSelectEl.addEventListener('change', function() {
                recalcThcSubtotal(section, sectionIndex);
            });
        }

        // Format rupiah + recalc saat biaya dokumen muat/bongkar diubah
        function attachDocInputListener(inputEl) {
            if (!inputEl) return;
            inputEl.addEventListener('input', function() {
                let raw = this.value.replace(/[^0-9]/g, '');
                const num = parseFloat(raw) || 0;
                this.value = num > 0 ? new Intl.NumberFormat('id-ID').format(num) : '';
                recalcThcSubtotal(section, sectionIndex);
            });
        }
        attachDocInputListener(section.querySelector('.thc-dok-muat-input'));
        attachDocInputListener(section.querySelector('.thc-dok-bongkar-input'));

        // Jalankan recalc awal agar Total Biaya ter-update saat section pertama dibuat
        recalcThcSubtotal(section, sectionIndex);

        // Expose function so voyage change can call it
        section._loadContainers = loadContainersForTHCSection;


    }
    
    window.removeTHCSection = function(sectionIndex) {
        const section = document.querySelector(`[data-thc-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllTHCSections();
        }
    };
    
    function loadVoyagesForTHCSection(sectionIndex, kapalNama) {
        const section = document.querySelector(`[data-thc-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.thc-voyage-select');
        const voyageInput  = section.querySelector('.thc-voyage-input');
        
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

                    // Add/replace voyage change listener to load containers
                    voyageSelect.onchange = function() {
                        if (section._loadContainers) {
                            section._loadContainers(this.value);
                        }
                    };
                } else {
                    voyageSelect.innerHTML = '<option value="">Tidak ada voyage tersedia</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching voyages for THC:', error);
                voyageSelect.innerHTML = '<option value="">Gagal memuat voyages</option>';
            });

        // Also listen to manual voyage input
        voyageInput.oninput = function() {
            if (section._loadContainers) {
                clearTimeout(voyageInput._thcDebounce);
                voyageInput._thcDebounce = setTimeout(() => {
                    section._loadContainers(this.value.trim());
                }, 500);
            }
        };
    }

    function calculateTHCTotals(sectionIndex) {
        const section = document.querySelector(`.thc-section[data-thc-section-index="${sectionIndex}"]`);
        if (!section) return;

        const ttContainer = section.querySelector('.thc-tt-container');
        const subtotalInput = section.querySelector('.thc-subtotal-input');
        const pphInput = section.querySelector('.thc-pph-input');
        const totalInput = section.querySelector('.thc-total-input');

        console.log(`[THC Calculation Section ${sectionIndex}]`);

        // Read manual subtotal instead of auto-calculating
        let subtotal = parseFloat(subtotalInput.value.replace(/[^0-9]/g, '')) || 0;
        
        // Remove automatic calculation logic
        /*
        const ttItems = ttContainer.querySelectorAll('.tt-search-wrapper');
        ttItems.forEach((item, index) => {
            // For now, we'll use a fixed price per tanda terima
            // In the future, this could be based on pricelist or other factors
            const pricePerTt = 500000; // Example: 500,000 per tanda terima
            subtotal += pricePerTt;
            console.log(`  - Tanda Terima ${index + 1}: ${pricePerTt}`);
        });
        */

        const pph = 0; // No PPH for Biaya THC
        const total = subtotal - pph;
        
        console.log('- Subtotal:', subtotal);
        console.log('- PPh:', pph);
        console.log('- Total:', total);

        const formatRupiah = (val) => {
            return new Intl.NumberFormat('id-ID').format(Math.round(val));
        };

        // Don't overwrite subtotalInput value as it is user input now
        // subtotalInput.value = formatRupiah(subtotal);
        
        pphInput.value = formatRupiah(pph);
        totalInput.value = formatRupiah(total);

        // Update global summary
        calculateTotalFromAllTHCSections();
    }

    function calculateTotalFromAllTHCSections() {
        let totalSubtotal = 0;

        document.querySelectorAll('.thc-section').forEach(section => {
            const sub = parseFloat(section.querySelector('.thc-subtotal-input').value.replace(/\./g, '')) || 0;
            totalSubtotal += sub;
        });

        // Add to nominal input if thc is the selected jenis biaya
        const jenisBiaya = jenisBiayaSelect ? jenisBiayaSelect.value : '';
        if (jenisBiaya === 'THC') {
            if (nominalInput) {
                nominalInput.value = totalSubtotal > 0 ? Math.round(totalSubtotal).toLocaleString('id-ID') : '';
            }
        }
    }