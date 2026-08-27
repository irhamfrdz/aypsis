    // ============= NEW BURUH BONGKAR SECTIONS MANAGEMENT =============
    let bbSectionCounter = 0;
    const bbSectionsContainer = document.getElementById('buruh_bongkar_sections_container');
    const addBbSectionBtn = document.getElementById('add_buruh_bongkar_section_btn');

    function initializeBuruhBongkarSections() {
        if (!bbSectionsContainer) return;
        bbSectionsContainer.innerHTML = '';
        bbSectionCounter = 0;
        addBuruhBongkarSection();
    }
    
    function clearAllBuruhBongkarSections() {
        if (!bbSectionsContainer) return;
        bbSectionsContainer.innerHTML = '';
        bbSectionCounter = 0;
        if(typeof nominalInput !== 'undefined' && nominalInput) nominalInput.value = '';
    }
    
    if (addBbSectionBtn) {
        addBbSectionBtn.addEventListener('click', function() {
            addBuruhBongkarSection();
        });
    }
    
    function addBuruhBongkarSection() {
        bbSectionCounter++;
        const sectionIndex = bbSectionCounter;
        
        const section = document.createElement('div');
        // Using kapal-section class to be compatible with calculateTotalFromAllSections
        section.className = 'kapal-section mb-6 p-4 border-2 border-blue-200 rounded-lg bg-blue-50';
        section.setAttribute('data-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        if (typeof allKapalsData !== 'undefined') {
            allKapalsData.forEach(kapal => {
                kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
            });
        }
        
        let bankOptions = '<option value="">-- Pilih Bank --</option>';
        @foreach($banks as $bank)
            bankOptions += '<option value="{{ $bank->id }}">{{ $bank->name }}</option>';
        @endforeach

        let karyawanOptions = '<option value="">-- Pilih Penerima --</option>';
        @foreach($karyawans as $karyawan)
            karyawanOptions += '<option value="{{ $karyawan->nama_lengkap }}">{{ $karyawan->nama_lengkap }}</option>';
        @endforeach

        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex} <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded ml-2">Buruh Bongkar</span></h3>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeBuruhBongkarSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
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

            <!-- Kontainer Selection -->
            <div class="mb-3 p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wider">Detail Kontainer <span class="text-xs text-gray-500 font-normal normal-case">(Opsional)</span></label>
                <div class="kontainer-loading hidden py-2 text-center text-gray-500 text-xs italic"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat kontainer...</div>
                <div class="kontainer-empty hidden py-2 text-center text-red-500 text-xs italic">Tidak ada kontainer untuk voyage ini</div>
                <div class="kontainer-container-section" data-section="${sectionIndex}"></div>
                <div class="mt-2 flex items-center gap-2">
                    <button type="button" onclick="addKontainerToBuruhBongkarSection(${sectionIndex})" class="btn-tambah-kontainer px-3 py-1.5 bg-indigo-500 hover:bg-indigo-600 text-white text-xs rounded-lg transition shadow-sm" disabled>
                        <i class="fas fa-plus mr-1"></i> Tambah Kontainer
                    </button>
                    <span class="info-kontainer text-xs text-gray-500"><i class="fas fa-info-circle mr-1"></i>Pilih No. Voyage terlebih dahulu</span>
                </div>
            </div>

            <!-- Nominal Per Kapal Display -->
            <div class="mt-3 p-3 bg-white border border-blue-300 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nominal <span class="text-red-500">*</span></label>
                        <input type="text" name="kapal_sections[${sectionIndex}][nominal_manual]" class="nominal-manual-input w-full px-3 py-2 border border-indigo-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 text-sm font-semibold" placeholder="0" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Adjustment</label>
                        <input type="text" name="kapal_sections[${sectionIndex}][adjustment]" class="adjustment-input w-full px-3 py-2 border border-blue-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 text-sm" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Catatan Adjustment</label>
                        <input type="text" name="kapal_sections[${sectionIndex}][notes_adjustment]" class="w-full px-3 py-2 border border-blue-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 text-sm" placeholder="Keterangan adjustment">
                    </div>
                </div>
                
                <!-- Detail Pembayaran & Dokumen -->
                <div class="mt-4 pt-3 border-t border-gray-200">
                    <h4 class="text-xs font-bold text-gray-700 mb-2 uppercase">Detail Pembayaran & Dokumen</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Bukti</label>
                            <input type="text" name="kapal_sections[${sectionIndex}][nomor_bukti]" class="nomor-bukti-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm" placeholder="Nomor Bukti">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Vendor (Ketik Manual)</label>
                            <input type="text" name="kapal_sections[${sectionIndex}][nama_vendor]" class="nama-vendor-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm" placeholder="Nama Vendor">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Penerima</label>
                            <select name="kapal_sections[${sectionIndex}][penerima]" class="penerima-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm">
                                ${karyawanOptions}
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Bank</label>
                            <select name="kapal_sections[${sectionIndex}][bank_id]" class="bank-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm">
                                ${bankOptions}
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Rekening</label>
                            <input type="text" name="kapal_sections[${sectionIndex}][nomor_rekening]" class="nomor-rekening-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm" placeholder="Nomor Rekening">
                        </div>
                    </div>
                </div>
                </div>

                <!-- PPh Section -->
                <div class="mt-4 pt-3 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">PPh</label>
                            <select name="kapal_sections[${sectionIndex}][pph_percent]" class="pph-percent-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="0">0%</option>
                                <option value="0.5">0.5%</option>
                                <option value="2">2%</option>
                                <option value="2.5">2.5%</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nominal PPh</label>
                            <input type="text" name="kapal_sections[${sectionIndex}][pph_amount]" class="pph-amount-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm bg-gray-100" placeholder="0" readonly>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center border-t border-blue-100 pt-2 mt-2">
                    <span class="text-sm font-semibold text-gray-700">Total Nominal Kapal ${sectionIndex}:</span>
                    <span class="section-nominal-display text-lg font-bold text-blue-600">Rp 0</span>
                </div>
            </div>
        `;
        
        bbSectionsContainer.appendChild(section);
        
        const kapalSelect = section.querySelector('.kapal-select');
        const voyageSelect = section.querySelector('.voyage-select');
        
        kapalSelect.addEventListener('change', function() {
            const currentSection = this.closest('.kapal-section');
            const currentIndex = parseInt(currentSection.getAttribute('data-section-index'));
            if(typeof loadVoyagesForSection === 'function') {
                loadVoyagesForSection(currentIndex, this.value);
            }
        });
        
        voyageSelect.addEventListener('change', function() {
            const currentSection = this.closest('.kapal-section');
            const currentIndex = parseInt(currentSection.getAttribute('data-section-index'));
            const currentKapalSelect = currentSection.querySelector('.kapal-select');
            const kapalNama = currentKapalSelect.value;
            const voyageValue = this.value;
            if (kapalNama && voyageValue) {
                if(typeof loadContainersForBuruhSection === 'function') {
                    loadContainersForBuruhSection(currentSection, currentIndex, voyageValue);
                }
            }
        });

        const voyageInput = section.querySelector('.voyage-input');
        const voyageManualBtn = section.querySelector('.voyage-manual-btn');
        const adjustmentInput = section.querySelector('.adjustment-input');
        const nominalManualInput = section.querySelector('.nominal-manual-input');
        const pphPercentSelect = section.querySelector('.pph-percent-select');

        if (pphPercentSelect) {
            pphPercentSelect.addEventListener('change', function() {
                if(typeof calculateTotalFromAllSections === 'function') calculateTotalFromAllSections();
            });
        }

        if (adjustmentInput) {
            adjustmentInput.addEventListener('input', function() {
                let val = this.value.replace(/\./g, '');
                if (!isNaN(val) && val !== '') {
                    this.value = Math.round(val).toLocaleString('id-ID');
                }
                if(typeof calculateTotalFromAllSections === 'function') calculateTotalFromAllSections();
            });
        }
        
        if (nominalManualInput) {
            nominalManualInput.addEventListener('input', function() {
                let val = this.value.replace(/\./g, '');
                if (!isNaN(val) && val !== '') {
                    this.value = Math.round(val).toLocaleString('id-ID');
                }
                if(typeof calculateTotalFromAllSections === 'function') calculateTotalFromAllSections();
            });
        }

        voyageManualBtn.addEventListener('click', function() {
            if (voyageInput.classList.contains('hidden')) {
                voyageSelect.classList.add('hidden');
                voyageSelect.disabled = true;
                voyageInput.classList.remove('hidden');
                voyageInput.disabled = false;
                voyageInput.focus();
                this.classList.remove('bg-gray-200', 'text-gray-600');
                this.classList.add('bg-blue-500', 'text-white');
            } else {
                voyageInput.classList.add('hidden');
                voyageInput.disabled = true;
                voyageSelect.classList.remove('hidden');
                voyageSelect.disabled = false;
                this.classList.remove('bg-blue-500', 'text-white');
                this.classList.add('bg-gray-200', 'text-gray-600');
            }
        });

        // --- AUTO-COPY PAYMENT DETAILS FROM KAPAL 1 ---
        const namaVendorInputBB = section.querySelector('.nama-vendor-input');
        const penerimaSelectBB = section.querySelector('.penerima-select');
        const bankSelectBB = section.querySelector('.bank-select');
        const nomorRekeningInputBB = section.querySelector('.nomor-rekening-input');

        // Copy values from Kapal 1 if this is a new section (> 1)
        if (sectionIndex > 1) {
            const firstSection = bbSectionsContainer.querySelector('[data-section-index="1"]');
            if (firstSection) {
                const firstVendor = firstSection.querySelector('.nama-vendor-input');
                const firstPenerima = firstSection.querySelector('.penerima-select');
                const firstBank = firstSection.querySelector('.bank-select');
                const firstRekening = firstSection.querySelector('.nomor-rekening-input');

                if (namaVendorInputBB && firstVendor && firstVendor.value) namaVendorInputBB.value = firstVendor.value;
                if (penerimaSelectBB && firstPenerima && firstPenerima.value) penerimaSelectBB.value = firstPenerima.value;
                if (bankSelectBB && firstBank && firstBank.value) bankSelectBB.value = firstBank.value;
                if (nomorRekeningInputBB && firstRekening && firstRekening.value) nomorRekeningInputBB.value = firstRekening.value;
            }
        }

        // Auto-update other sections when Kapal 1 changes
        if (sectionIndex === 1) {
            const updateOtherSectionsBB = (selector, value) => {
                bbSectionsContainer.querySelectorAll('.kapal-section').forEach(sec => {
                    const idx = parseInt(sec.getAttribute('data-section-index'));
                    if (idx > 1) {
                        const input = sec.querySelector(selector);
                        if (input) input.value = value;
                    }
                });
            };

            if (namaVendorInputBB) {
                namaVendorInputBB.addEventListener('input', function() {
                    updateOtherSectionsBB('.nama-vendor-input', this.value);
                });
            }
            if (penerimaSelectBB) {
                penerimaSelectBB.addEventListener('change', function() {
                    updateOtherSectionsBB('.penerima-select', this.value);
                });
            }
            if (bankSelectBB) {
                bankSelectBB.addEventListener('change', function() {
                    updateOtherSectionsBB('.bank-select', this.value);
                });
            }
            if (nomorRekeningInputBB) {
                nomorRekeningInputBB.addEventListener('input', function() {
                    updateOtherSectionsBB('.nomor-rekening-input', this.value);
                });
            }
        }
    }

    function removeBuruhBongkarSection(index) {
        const section = document.querySelector(`.kapal-section[data-section-index="${index}"]`);
        if (section && section.closest('#buruh_bongkar_sections_container')) {
            section.remove();
            if(typeof calculateTotalFromAllSections === 'function') calculateTotalFromAllSections();
        }
    }

    window.addKontainerToBuruhBongkarSection = function(sectionIndex) {
        const bbContainer = document.getElementById('buruh_bongkar_sections_container');
        if (!bbContainer) return;
        const section = bbContainer.querySelector(`[data-section-index="${sectionIndex}"]`);
        if (!section) return;
        const container = section.querySelector('.kontainer-container-section');
        const kontainerIndex = container.children.length;
        
        let kontainerOptions = '<option value="">Pilih Kontainer</option>';
        if (window.sectionContainers && window.sectionContainers[sectionIndex]) {
            window.sectionContainers[sectionIndex].forEach(c => {
                const sizeInfo = c.size_kontainer && c.size_kontainer !== '-' ? ` [Size: ${c.size_kontainer}]` : '';
                kontainerOptions += `<option value="${c.id}" data-nomor="${c.nomor_kontainer}" data-size="${c.size_kontainer || ''}">${c.nomor_kontainer} (BL: ${c.no_bl || '-'})${sizeInfo}</option>`;
            });
        }
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2 animate-fade-in';
        inputGroup.innerHTML = `
            <div class="flex-1">
                <select name="kapal_sections[${sectionIndex}][kontainer][${kontainerIndex}][bl_id]" class="kontainer-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-500" required>
                    ${kontainerOptions}
                </select>
                <input type="hidden" name="kapal_sections[${sectionIndex}][kontainer][${kontainerIndex}][nomor_kontainer]" class="kontainer-nomor-hidden">
                <input type="hidden" name="kapal_sections[${sectionIndex}][kontainer][${kontainerIndex}][size]" class="kontainer-size-hidden">
            </div>
            <div class="w-32">
                <input type="text" name="kapal_sections[${sectionIndex}][kontainer][${kontainerIndex}][nominal]" class="kontainer-nominal-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-500" placeholder="Rp 0" required>
            </div>
            <button type="button" onclick="removeBbKontainerFromSection(this)" class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition">
                <i class="fas fa-trash text-xs"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        const kontainerSelect = inputGroup.querySelector('.kontainer-select-item');
        const nomorHidden = inputGroup.querySelector('.kontainer-nomor-hidden');
        const sizeHidden = inputGroup.querySelector('.kontainer-size-hidden');

        function autoFillNominal(size) {
            if (typeof pricelistBuruhBongkarData !== 'undefined' && size) {
                const cleanCSize = String(size).replace(/[^0-9]/g, '');
                const pricelist = pricelistBuruhBongkarData.find(p => {
                    const cleanPSize = String(p.size).replace(/[^0-9]/g, '');
                    return cleanPSize === cleanCSize;
                });
                if (pricelist) {
                    const nominalInput = inputGroup.querySelector('.kontainer-nominal-item');
                    nominalInput.value = parseInt(pricelist.nominal).toLocaleString('id-ID');
                    if (typeof calculateTotalFromAllSections === 'function') calculateTotalFromAllSections();
                }
            }
        }
        
        if (typeof jQuery !== 'undefined' && $.fn.select2) {
            $(kontainerSelect).select2({
                placeholder: "Pilih Kontainer",
                allowClear: true,
                width: '100%'
            });
            
            $(kontainerSelect).on('select2:select', function (e) {
                const selectedOption = $(this).find(':selected');
                nomorHidden.value = selectedOption.attr('data-nomor') || '';
                const size = selectedOption.attr('data-size') || '';
                sizeHidden.value = size;
                autoFillNominal(size);
            });
            
            $(kontainerSelect).on('select2:unselect', function (e) {
                nomorHidden.value = '';
                sizeHidden.value = '';
            });
        } else {
            kontainerSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                nomorHidden.value = selectedOption.getAttribute('data-nomor') || '';
                const size = selectedOption.getAttribute('data-size') || '';
                sizeHidden.value = size;
                autoFillNominal(size);
            });
        }
        
        const nominalInput = inputGroup.querySelector('.kontainer-nominal-item');
        nominalInput.addEventListener('input', function() {
            let val = this.value.replace(/\D/g, '');
            if (val !== '') {
                this.value = parseInt(val).toLocaleString('id-ID');
            }
            if (typeof calculateTotalFromAllSections === 'function') calculateTotalFromAllSections();
        });
    };

    window.removeBbKontainerFromSection = function(button) {
        const container = button.closest('.kontainer-container-section');
        button.closest('.flex').remove();
        // Reindex
        const section = container.closest('.kapal-section');
        const sectionIndex = section.getAttribute('data-section-index');
        const inputGroups = container.querySelectorAll('.flex');
        inputGroups.forEach((group, newIndex) => {
            const select = group.querySelector('.kontainer-select-item');
            if (select) select.name = `kapal_sections[${sectionIndex}][kontainer][${newIndex}][bl_id]`;
            const nomor = group.querySelector('.kontainer-nomor-hidden');
            if (nomor) nomor.name = `kapal_sections[${sectionIndex}][kontainer][${newIndex}][nomor_kontainer]`;
            const size = group.querySelector('.kontainer-size-hidden');
            if (size) size.name = `kapal_sections[${sectionIndex}][kontainer][${newIndex}][size]`;
            const nominal = group.querySelector('.kontainer-nominal-item');
            if (nominal) nominal.name = `kapal_sections[${sectionIndex}][kontainer][${newIndex}][nominal]`;
        });
        if (typeof calculateTotalFromAllSections === 'function') calculateTotalFromAllSections();
    };

