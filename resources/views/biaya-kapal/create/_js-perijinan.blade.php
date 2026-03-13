    // ============= BIAYA PERIJINAN LOGIC (MULTI-SECTION) =============
    let perijinanSectionCounter = 0;
    // Variables already declared in _js-jenis-biaya.blade.php:
    // perijinanWrapper, perijinanSectionsContainer, addPerijinanSectionBtn, addPerijinanSectionBottomBtn


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
                    <div class="relative">
                        <select name="perijinan_sections[${idx}][nama_kapal]" 
                                class="w-full pl-3 pr-10 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 appearance-none bg-indigo-50/30 perijinan-kapal-select"
                                onchange="loadVoyagesForPerijinanSection(${idx}, this.value)" required>
                            ${kapalOptions}
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-indigo-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-indigo-900 uppercase tracking-tight">Nomor Voyage <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="perijinan_sections[${idx}][no_voyage]" 
                                id="perijinan_voyage_${idx}" 
                                class="w-full pl-3 pr-10 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 appearance-none bg-indigo-50/30"
                                disabled required>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-indigo-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-1.5">
                    <label class="block text-xs font-bold text-indigo-900 uppercase tracking-tight">Nama Perijinan <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="perijinan_sections[${idx}][nama_perijinan]" 
                           class="w-full px-4 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white" 
                           list="dokumen_perijinan_list"
                           placeholder="Contoh: Sertifikat Kelaiklautan, Port Clearance, dll" required>
                    <datalist id="dokumen_perijinan_list">
                        ${dokumenPerijinansData.map(d => `<option value="${d.nama_dokumen}">`).join('')}
                    </datalist>
                </div>

                <div class="md:col-span-2 space-y-1.5">
                    <label class="block text-xs font-bold text-indigo-900 uppercase tracking-tight">Keterangan Tambahan</label>
                    <textarea name="perijinan_sections[${idx}][keterangan]" 
                              rows="2" 
                              class="w-full px-4 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white" 
                              placeholder="Masukkan detail tambahan jika ada..."></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-indigo-900 uppercase tracking-tight">Jumlah Biaya <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none border-r border-indigo-100 pr-3">
                            <span class="text-indigo-600 font-bold text-sm">Rp</span>
                        </div>
                        <input type="text" 
                               name="perijinan_sections[${idx}][jumlah_biaya]" 
                               id="perijinan_jumlah_${idx}" 
                               class="w-full pl-16 pr-4 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white font-semibold text-indigo-900" 
                               placeholder="0"
                               oninput="formatPerijinanBiaya(this)" required>
                    </div>
                </div>

                <div class="flex items-end">
                    <div class="w-full bg-indigo-50/50 border border-indigo-100 rounded-lg px-4 py-2.5 flex justify-between items-center">
                        <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest">Subtotal Section</span>
                        <span class="text-lg font-black text-indigo-700" id="perijinan_subtotal_${idx}">Rp 0</span>
                    </div>
                </div>
            </div>
        `;

        perijinanSectionsContainer.appendChild(section);
    }

    window.removePerijinanSection = function(btn) {
        const section = btn.closest('.perijinan-section');
        if (section) {
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
            const numVal = parseInt(input.value.replace(/\./g, '') || 0);
            if (subtotalEl) subtotalEl.textContent = 'Rp ' + numVal.toLocaleString('id-ID');
        }
        
        calculateGrandTotalPerijinan();
    };

    function calculateGrandTotalPerijinan() {
        let grandTotal = 0;
        document.querySelectorAll('[id^="perijinan_jumlah_"]').forEach(input => {
            const val = parseInt(input.value.replace(/\./g, '') || 0);
            grandTotal += val;
        });
        
        // Update the main nominal input if needed, or just let the form handle it
        if (typeof nominalInput !== 'undefined' && nominalInput) {
            nominalInput.value = grandTotal.toLocaleString('id-ID');
            // Trigger input event to update other depending fields
            nominalInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    window.loadVoyagesForPerijinanSection = function(sectionIndex, kapalNama) {
        const voyageSelect = document.getElementById(`perijinan_voyage_${sectionIndex}`);
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

    if (addPerijinanSectionBtn) {
        addPerijinanSectionBtn.addEventListener('click', () => addPerijinanSection());
    }
    if (addPerijinanSectionBottomBtn) {
        addPerijinanSectionBottomBtn.addEventListener('click', () => addPerijinanSection());
    }

