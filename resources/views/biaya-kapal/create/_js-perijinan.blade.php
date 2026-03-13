    // ============= BIAYA PERIJINAN LOGIC (MULTI-SECTION) =============
    let perijinanSectionCounter = 0;
    // Variables already declared in _js-jenis-biaya.blade.php:
    // perijinanWrapper, perijinanSectionsContainer, addPerijinanSectionBtn, addPerijinanSectionBottomBtn


    // Helper for Select2 initialization
    const initPerijinanSelect2 = (el, placeholder) => {
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
            jQuery(el).select2({
                placeholder: placeholder,
                allowClear: true,
                width: '100%',
                dropdownAutoWidth: true
            });
        }
    };

    function initializePerijinanSections() {
        if (perijinanSectionsContainer) perijinanSectionsContainer.innerHTML = '';
        perijinanSectionCounter = 0;
        addPerijinanSection();
    }

    function clearAllPerijinanSections() {
        if (perijinanSectionsContainer) perijinanSectionsContainer.innerHTML = '';
        perijinanSectionCounter = 0;
    }

    function addPerijinanSection() {
        perijinanSectionCounter++;
        const idx = perijinanSectionCounter;

        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });

        const section = document.createElement('div');
        section.className = 'perijinan-section mb-5 p-5 border-2 border-indigo-100 rounded-xl bg-white shadow-sm hover:shadow-md transition-all duration-300';
        section.setAttribute('data-section-index', idx);
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-indigo-50">
                <div class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 bg-indigo-600 text-white text-xs font-bold rounded-full">${idx}</span>
                    <h4 class="text-sm font-semibold text-indigo-900 uppercase tracking-wider">Detail Kapal ${idx}</h4>
                </div>
                ${idx > 1 ? `<button type="button" onclick="removePerijinanSection(this)" class="text-red-400 hover:text-red-600 transition-colors text-sm flex items-center gap-1 font-medium">
                    <i class="fas fa-trash-alt"></i> <span>Hapus</span>
                </button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-indigo-900 uppercase tracking-tight">Nama Kapal <span class="text-red-500">*</span></label>
                    <div class="relative perijinan-select2-container">
                        <select name="perijinan_sections[${idx}][nama_kapal]" 
                                class="w-full perijinan-kapal-select"
                                onchange="loadVoyagesForPerijinanSection(${idx}, this.value)" required>
                            ${kapalOptions}
                        </select>
                    </div>
                </div>
                
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-indigo-900 uppercase tracking-tight">Nomor Voyage <span class="text-red-500">*</span></label>
                    <div class="relative perijinan-select2-container">
                        <select name="perijinan_sections[${idx}][no_voyage]" 
                                id="perijinan_voyage_${idx}" 
                                class="w-full perijinan-voyage-select"
                                disabled required>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-indigo-900 uppercase tracking-tight">Nomor Referensi</label>
                    <input type="text" 
                           name="perijinan_sections[${idx}][nomor_referensi]" 
                           class="w-full px-4 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white" 
                           placeholder="Masukkan nomor referensi...">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-indigo-900 uppercase tracking-tight">Vendor</label>
                    <input type="text" 
                           name="perijinan_sections[${idx}][vendor]" 
                           class="w-full px-4 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white" 
                           placeholder="Nama Vendor...">
                </div>

                <div class="md:col-span-2 space-y-1.5">
                    <label class="block text-xs font-bold text-indigo-900 uppercase tracking-tight">Keterangan Tambahan</label>
                    <textarea name="perijinan_sections[${idx}][keterangan]" 
                              rows="2" 
                              class="w-full px-4 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white" 
                              placeholder="Masukkan detail tambahan jika ada..."></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-indigo-900 uppercase tracking-tight">Biaya INSA</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <span class="text-indigo-400 font-bold text-xs">Rp</span>
                        </div>
                        <input type="text" 
                               name="perijinan_sections[${idx}][biaya_insa]" 
                               class="w-full pl-12 pr-4 py-2 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-indigo-50/10 perijinan-insa-input" 
                               placeholder="0"
                               oninput="formatPerijinanBiaya(this)">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-indigo-900 uppercase tracking-tight">Biaya PBNI</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <span class="text-indigo-400 font-bold text-xs">Rp</span>
                        </div>
                        <input type="text" 
                               name="perijinan_sections[${idx}][biaya_pbni]" 
                               class="w-full pl-12 pr-4 py-2 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-indigo-50/10 perijinan-pbni-input" 
                               placeholder="0"
                               oninput="formatPerijinanBiaya(this)">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-indigo-900 uppercase tracking-tight">Jumlah Biaya Lainnya</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none border-r border-indigo-100 pr-3">
                            <span class="text-indigo-600 font-bold text-sm">Rp</span>
                        </div>
                        <input type="text" 
                               name="perijinan_sections[${idx}][jumlah_biaya]" 
                               id="perijinan_jumlah_${idx}" 
                               class="w-full pl-16 pr-4 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white font-semibold text-indigo-900 perijinan-base-input" 
                               placeholder="0"
                               oninput="formatPerijinanBiaya(this)">
                    </div>
                </div>

                <div class="flex items-end">
                    <div class="w-full bg-indigo-50/50 border border-indigo-100 rounded-lg px-4 py-2.5 flex justify-between items-center">
                        <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest">Total Biaya Section</span>
                        <span class="text-lg font-black text-indigo-700" id="perijinan_subtotal_${idx}">Rp 0</span>
                    </div>
                </div>
            </div>
        `;

        perijinanSectionsContainer.appendChild(section);

        // Initialize Select2 for this new section
        const kapalSelect = section.querySelector('.perijinan-kapal-select');
        const voyageSelect = section.querySelector('.perijinan-voyage-select');
        
        initPerijinanSelect2(kapalSelect, "-- Pilih Kapal --");
        initPerijinanSelect2(voyageSelect, "-- Pilih Kapal Terlebih Dahulu --");

        // Use jQuery event for Select2 so onchange continues to work or trigger manually
        jQuery(kapalSelect).on('change', function() {
            loadVoyagesForPerijinanSection(idx, this.value);
        });
    }

    window.removePerijinanSection = function(btn) {
        const section = btn.closest('.perijinan-section');
        if (section) {
            // Destroy select2 before removing to avoid memory leaks
            jQuery(section).find('.select2-hidden-accessible').select2('destroy');
            
            section.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                section.remove();
                calculateGrandTotalPerijinan();
            }, 300);
        }
    };

    window.formatPerijinanBiaya = function(input) {
        let value = input.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        input.value = value;

        const section = input.closest('.perijinan-section');
        if (section) {
            const idx = section.getAttribute('data-section-index');
            const subtotalEl = document.getElementById(`perijinan_subtotal_${idx}`);
            
            // Get all cost values in this section
            const insaVal = parseInt(section.querySelector('.perijinan-insa-input').value.replace(/\./g, '') || 0);
            const pbniVal = parseInt(section.querySelector('.perijinan-pbni-input').value.replace(/\./g, '') || 0);
            const baseVal = parseInt(section.querySelector('.perijinan-base-input').value.replace(/\./g, '') || 0);
            
            const totalSection = insaVal + pbniVal + baseVal;
            if (subtotalEl) subtotalEl.textContent = 'Rp ' + totalSection.toLocaleString('id-ID');
        }
        
        calculateGrandTotalPerijinan();
    };

    function calculateGrandTotalPerijinan() {
        let grandTotal = 0;
        document.querySelectorAll('.perijinan-section').forEach(section => {
            const insaVal = parseInt(section.querySelector('.perijinan-insa-input').value.replace(/\./g, '') || 0);
            const pbniVal = parseInt(section.querySelector('.perijinan-pbni-input').value.replace(/\./g, '') || 0);
            const baseVal = parseInt(section.querySelector('.perijinan-base-input').value.replace(/\./g, '') || 0);
            grandTotal += (insaVal + pbniVal + baseVal);
        });
        
        if (typeof nominalInput !== 'undefined' && nominalInput) {
            nominalInput.value = grandTotal.toLocaleString('id-ID');
            nominalInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    window.loadVoyagesForPerijinanSection = function(sectionIndex, kapalNama) {
        const voyageSelect = document.getElementById(`perijinan_voyage_${sectionIndex}`);
        if (!voyageSelect) return;

        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        if (jQuery(voyageSelect).data('select2')) jQuery(voyageSelect).trigger('change');
        voyageSelect.disabled = true;

        if (!kapalNama) {
            voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
            if (jQuery(voyageSelect).data('select2')) jQuery(voyageSelect).trigger('change');
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
                
                // Refresh Select2
                if (jQuery(voyageSelect).data('select2')) {
                    jQuery(voyageSelect).trigger('change');
                } else {
                    initPerijinanSelect2(voyageSelect, "-- Pilih Voyage --");
                }
            })
            .catch(() => {
                voyageSelect.innerHTML = '<option value="">Gagal memuat voyage</option>';
                voyageSelect.disabled = false;
                if (jQuery(voyageSelect).data('select2')) jQuery(voyageSelect).trigger('change');
            });
    };

    if (addPerijinanSectionBtn) {
        addPerijinanSectionBtn.addEventListener('click', () => addPerijinanSection());
    }
    if (addPerijinanSectionBottomBtn) {
        addPerijinanSectionBottomBtn.addEventListener('click', () => addPerijinanSection());
    }

<style>
    /* Select2 Indigo Theme Compatibility */
    .perijinan-select2-container .select2-container--default .select2-selection--single {
        background-color: rgb(238 242 255 / 0.3); /* indigo-50/30 */
        border: 1px solid rgb(224 231 255); /* indigo-100 */
        border-radius: 0.5rem; /* rounded-lg */
        height: 42px;
        transition: all 0.2s;
    }
    .perijinan-select2-container .select2-container--default.select2-container--focus .select2-selection--single,
    .perijinan-select2-container .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #6366f1; /* indigo-500 */
        ring: 2px;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
    }
    .perijinan-select2-container .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px;
        padding-left: 12px;
        color: #312e81; /* indigo-900 */
        font-size: 0.875rem;
    }
    .perijinan-select2-container .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 8px;
    }
    .perijinan-select2-container .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #818cf8 transparent transparent transparent; /* indigo-400 */
    }
    .select2-dropdown {
        border: 1px solid rgb(224 231 255);
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #4f46e5 !important; /* indigo-600 */
    }
</style>

