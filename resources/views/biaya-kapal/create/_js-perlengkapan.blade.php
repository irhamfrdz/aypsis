    // ============= BIAYA PERLENGKAPAN LOGIC (MULTI-SECTION) =============
    let perlengkapanSectionCounter = 0;

    function initializePerlengkapanSections() {
        if (perlengkapanSectionsContainer) perlengkapanSectionsContainer.innerHTML = '';
        perlengkapanSectionCounter = 0;
        addPerlengkapanSection();
    }

    function clearAllPerlengkapanSections() {
        if (perlengkapanSectionsContainer) perlengkapanSectionsContainer.innerHTML = '';
        perlengkapanSectionCounter = 0;
    }

    function addPerlengkapanSection() {
        perlengkapanSectionCounter++;
        const idx = perlengkapanSectionCounter;

        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });

        const section = document.createElement('div');
        section.className = 'perlengkapan-section mb-5 p-4 border-2 border-orange-200 rounded-lg bg-orange-50';
        section.setAttribute('data-section-index', idx);
        section.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <i class="fas fa-toolbox text-orange-600"></i>
                    <h4 class="text-sm font-semibold text-orange-800">Kapal ${idx}</h4>
                </div>
                ${idx > 1 ? `<button type="button" onclick="removePerlengkapanSection(this)" class="text-red-500 hover:text-red-700 text-sm flex items-center gap-1">
                    <i class="fas fa-trash-alt"></i> Hapus
                </button>` : ''}
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="perlengkapan_sections[${idx}][nama_kapal]"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 perlengkapan-kapal-select"
                            onchange="loadVoyagesForPerlengkapanSection(${idx}, this.value)">
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Voyage <span class="text-red-500">*</span></label>
                    <select name="perlengkapan_sections[${idx}][no_voyage]"
                            id="perlengkapan_voyage_${idx}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                            disabled>
                        <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Pilih kapal untuk memuat daftar voyage</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="perlengkapan_sections[${idx}][keterangan]"
                              rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                              placeholder="Masukkan keterangan perlengkapan..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Biaya <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                        <input type="text"
                               name="perlengkapan_sections[${idx}][jumlah_biaya]"
                               id="perlengkapan_jumlah_${idx}"
                               value="0"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 perlengkapan-jumlah-input"
                               placeholder="0"
                               oninput="formatPerlengkapanBiaya(this)">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Masukkan nominal tanpa titik atau koma</p>
                </div>
                <div class="flex items-end">
                    <div class="w-full bg-orange-100 border border-orange-200 rounded-lg px-4 py-3">
                        <p class="text-xs text-orange-700 font-medium mb-1">Subtotal</p>
                        <p class="text-lg font-bold text-orange-800" id="perlengkapan_subtotal_${idx}">Rp 0</p>
                    </div>
                </div>
            </div>
        `;

        perlengkapanSectionsContainer.appendChild(section);
    }

    window.removePerlengkapanSection = function(btn) {
        const section = btn.closest('.perlengkapan-section');
        if (section) section.remove();
    };

    window.formatPerlengkapanBiaya = function(input) {
        let value = input.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        input.value = value;

        // Update subtotal display in same section
        const section = input.closest('.perlengkapan-section');
        if (section) {
            const idx = section.getAttribute('data-section-index');
            const subtotalEl = document.getElementById(`perlengkapan_subtotal_${idx}`);
            const numVal = parseInt(input.value.replace(/\./g, '') || 0);
            if (subtotalEl) subtotalEl.textContent = 'Rp ' + numVal.toLocaleString('id-ID');
        }
    };

    window.loadVoyagesForPerlengkapanSection = function(sectionIndex, kapalNama) {
        const voyageSelect = document.getElementById(`perlengkapan_voyage_${sectionIndex}`);
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
                if (data.success && data.voyages && data.voyages.length > 0) {
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

    if (addPerlengkapanSectionBtn) {
        addPerlengkapanSectionBtn.addEventListener('click', () => addPerlengkapanSection());
    }
    if (addPerlengkapanSectionBottomBtn) {
        addPerlengkapanSectionBottomBtn.addEventListener('click', () => addPerlengkapanSection());
    }
    
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
