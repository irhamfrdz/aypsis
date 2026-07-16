
    // ============= BIAYA UMUM LOGIC (MULTI-SECTION) =============
    const umumSectionsContainer = document.getElementById('umum_sections_container');
    const addUmumSectionBtn = document.getElementById('add_umum_section_btn');
    const addUmumSectionBottomBtn = document.getElementById('add_umum_section_bottom_btn');
    let umumSectionCounter = 0;

    function initializeUmumSections() {
        if (umumSectionsContainer) umumSectionsContainer.innerHTML = '';
        umumSectionCounter = 0;
        addUmumSection();
    }

    function clearAllUmumSections() {
        if (umumSectionsContainer) umumSectionsContainer.innerHTML = '';
        umumSectionCounter = 0;
    }

    function addUmumSection(initialData = null) {
        umumSectionCounter++;
        const idx = umumSectionCounter;

        let selectedKapal = initialData ? initialData.kapal : '';
        let selectedVoyage = initialData ? initialData.voyage : '';
        let namaVendor = initialData ? initialData.nama_vendor : '';
        let penerima = initialData ? initialData.penerima : '';
        let keterangan = initialData ? initialData.keterangan : '';
        let nominal = initialData ? initialData.nominal : 0;
        let pph = initialData ? initialData.pph : 0;

        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        if (typeof allKapalsData !== 'undefined') {
            allKapalsData.forEach(kapal => {
                let selected = (kapal.nama_kapal == selectedKapal) ? 'selected' : '';
                kapalOptions += `<option value="${kapal.nama_kapal}" ${selected}>${kapal.nama_kapal}</option>`;
            });
        }

        const section = document.createElement('div');
        section.className = 'umum-section mb-5 p-4 border-2 border-indigo-200 rounded-lg bg-indigo-50';
        section.setAttribute('data-section-index', idx);
        section.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <i class="fas fa-file-invoice-dollar text-indigo-600"></i>
                    <h4 class="text-sm font-semibold text-indigo-800">Item ${idx}</h4>
                </div>
                ${idx > 1 ? `<button type="button" onclick="removeUmumSection(this)" class="text-red-500 hover:text-red-700 text-sm flex items-center gap-1">
                    <i class="fas fa-trash-alt"></i> Hapus
                </button>` : ''}
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kapal (Opsional)</label>
                    <select name="umum_sections[${idx}][kapal]"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            onchange="loadVoyagesForUmumSection(${idx}, this.value)">
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Voyage (Opsional)</label>
                    <select name="umum_sections[${idx}][voyage]"
                            id="umum_voyage_${idx}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        ${selectedVoyage ? `<option value="${selectedVoyage}">${selectedVoyage}</option>` : '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>'}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Vendor</label>
                    <input type="text"
                           name="umum_sections[${idx}][nama_vendor]"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                           placeholder="Masukkan nama vendor..." value="${namaVendor}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penerima</label>
                    <input type="text"
                           name="umum_sections[${idx}][penerima]"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                           placeholder="Masukkan nama penerima..." value="${penerima}">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="umum_sections[${idx}][keterangan]"
                              rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                              placeholder="Masukkan keterangan biaya...">${keterangan}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                        <input type="text"
                               name="umum_sections[${idx}][nominal]"
                               id="umum_jumlah_${idx}"
                               value="${nominal}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 umum-jumlah-input"
                               placeholder="0"
                               oninput="formatUmumBiaya(this)" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PPh</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                        <input type="text"
                               name="umum_sections[${idx}][pph]"
                               id="umum_pph_${idx}"
                               value="${pph}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 umum-pph-input"
                               placeholder="0"
                               oninput="formatUmumBiaya(this)">
                    </div>
                </div>
                <div class="flex items-end md:col-span-2">
                    <div class="w-full bg-indigo-100 border border-indigo-200 rounded-lg px-4 py-3">
                        <p class="text-xs text-indigo-700 font-medium mb-1">Subtotal (Nominal - PPh)</p>
                        <p class="text-lg font-bold text-indigo-800" id="umum_subtotal_${idx}">Rp 0</p>
                    </div>
                </div>
            </div>
        `;

        umumSectionsContainer.appendChild(section);
        
        // Trigger formatting to show initial subtotal correctly
        setTimeout(() => {
            const nominalInput = document.getElementById(`umum_jumlah_${idx}`);
            if (nominalInput) {
                formatUmumBiaya(nominalInput);
            }
        }, 100);
    }

    window.removeUmumSection = function(btn) {
        const section = btn.closest('.umum-section');
        if (section) section.remove();
    };

    window.formatUmumBiaya = function(input) {
        let value = input.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        } else {
            value = '0';
        }
        input.value = value;

        const section = input.closest('.umum-section');
        if (section) {
            const idx = section.getAttribute('data-section-index');
            const nominalInput = document.getElementById(`umum_jumlah_${idx}`);
            const pphInput = document.getElementById(`umum_pph_${idx}`);
            const subtotalEl = document.getElementById(`umum_subtotal_${idx}`);
            
            const nominalVal = parseInt((nominalInput.value || '0').replace(/\./g, ''));
            const pphVal = parseInt((pphInput.value || '0').replace(/\./g, ''));
            const subtotal = nominalVal - pphVal;
            
            if (subtotalEl) subtotalEl.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            
            // Re-calculate global total if needed
            if (typeof calculateTotalFromAllUmumSections === 'function') {
                calculateTotalFromAllUmumSections();
            }
        }
    };

    window.loadVoyagesForUmumSection = function(sectionIndex, kapalNama) {
        const voyageSelect = document.getElementById(`umum_voyage_${sectionIndex}`);
        if (!voyageSelect) return;

        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        voyageSelect.disabled = true;

        if (!kapalNama) {
            voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
            return;
        }

        fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(kapalNama)}`)
            .then(r => r.json())
            .then(data => {
                if (data.success && data.voyages_detailed && data.voyages_detailed.length > 0) {
                    let html = '<option value="">-- Pilih Voyage --</option>';
                    data.voyages_detailed.forEach(v => {
                        html += `<option value="${v.no_voyage}">${v.no_voyage} (${v.tanggal})</option>`;
                    });
                    voyageSelect.innerHTML = html;
                } else if (data.success && data.voyages && data.voyages.length > 0) {
                    let html = '<option value="">-- Pilih Voyage --</option>';
                    data.voyages.forEach(v => {
                        html += `<option value="${v}">${v}</option>`;
                    });
                    voyageSelect.innerHTML = html;
                } else {
                    voyageSelect.innerHTML = '<option value="">Tidak ada voyage tersedia</option>';
                }
                voyageSelect.disabled = false;
            })
            .catch(() => {
                voyageSelect.innerHTML = '<option value="">Gagal memuat voyage</option>';
                voyageSelect.disabled = false;
            });
    };

    if (addUmumSectionBtn) {
        addUmumSectionBtn.addEventListener('click', () => addUmumSection());
    }
    if (addUmumSectionBottomBtn) {
        addUmumSectionBottomBtn.addEventListener('click', () => addUmumSection());
    }

