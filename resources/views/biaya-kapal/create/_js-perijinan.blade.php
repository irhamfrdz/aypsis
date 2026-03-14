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
        section.className = 'perijinan-section mb-6 p-5 border-2 border-indigo-100 rounded-xl bg-indigo-50/30 shadow-sm hover:shadow-md transition-all duration-300';
        section.setAttribute('data-section-index', idx);
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-indigo-100/50">
                <div class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-7 h-7 bg-indigo-600 text-white text-xs font-bold rounded-full shadow-sm">${idx}</span>
                    <h4 class="text-sm font-bold text-indigo-900 uppercase tracking-wider">Detail Kapal & Perijinan ${idx}</h4>
                </div>
                ${idx > 1 ? `<button type="button" onclick="removePerijinanSection(this)" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs rounded-lg transition-colors flex items-center gap-1.5 font-bold border border-red-100">
                    <i class="fas fa-trash-alt"></i> <span>Hapus</span>
                </button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Row 1: Kapal & Voyage -->
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-indigo-900 uppercase tracking-widest">Nama Kapal <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="perijinan_sections[${idx}][nama_kapal]" 
                                class="w-full perijinan-kapal-select px-4 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white shadow-sm appearance-none"
                                onchange="loadVoyagesForPerijinanSection(${idx}, this.value)" required>
                            ${kapalOptions}
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-indigo-300">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-indigo-900 uppercase tracking-widest">Nomor Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <div class="flex-grow relative perijinan-select-container">
                            <select name="perijinan_sections[${idx}][no_voyage]" 
                                    id="perijinan_voyage_${idx}" 
                                    class="perijinan-voyage-select w-full px-4 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white shadow-sm appearance-none disabled:bg-gray-50"
                                    disabled required>
                                <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-indigo-300">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                        <input type="text" 
                               name="perijinan_sections[${idx}][no_voyage]" 
                               class="perijinan-voyage-manual-input hidden w-full px-4 py-2 border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white" 
                               placeholder="Ketik No. Voyage..." disabled>
                        <button type="button" class="perijinan-voyage-toggle-btn px-3 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-600 rounded-lg transition shadow-sm" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Row 2: Vendor & Lokasi -->
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-indigo-900 uppercase tracking-widest">Vendor</label>
                    <input type="text" 
                           name="perijinan_sections[${idx}][vendor]" 
                           class="w-full px-4 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white shadow-sm" 
                           placeholder="Nama Vendor...">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-indigo-900 uppercase tracking-widest">Lokasi</label>
                    <input type="text" 
                           name="perijinan_sections[${idx}][lokasi]" 
                           class="w-full px-4 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white shadow-sm" 
                           placeholder="Lokasi Perijinan...">
                </div>

                <!-- Row 3: Jumlah Biaya -->
                <div class="md:col-span-2 space-y-1.5">
                    <label class="block text-[10px] font-black text-indigo-900 uppercase tracking-widest">Jumlah Biaya</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <span class="text-indigo-400 font-bold text-xs">Rp</span>
                        </div>
                        <input type="text" 
                               name="perijinan_sections[${idx}][jumlah_biaya]" 
                               id="perijinan_jumlah_${idx}" 
                               class="w-full pl-12 pr-4 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white shadow-sm perijinan-base-input" 
                               placeholder="0"
                               oninput="formatPerijinanBiaya(this)">
                        <input type="hidden" name="perijinan_sections[${idx}][sub_total]" class="perijinan-subtotal-value sub-total-value" value="0">
                        <input type="hidden" name="perijinan_sections[${idx}][grand_total]" class="perijinan-grandtotal-value grand-total-value" value="0">
                    </div>
                </div>

                <!-- Row 6: Penerima & Nomor Rekening -->
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-indigo-900 uppercase tracking-widest">Penerima</label>
                    <input type="text" 
                           name="perijinan_sections[${idx}][penerima]" 
                           class="w-full px-4 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white shadow-sm" 
                           placeholder="Nama penerima pembayaran...">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-indigo-900 uppercase tracking-widest">Nomor Rekening</label>
                    <input type="text" 
                           name="perijinan_sections[${idx}][nomor_rekening]" 
                           class="w-full px-4 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white shadow-sm" 
                           placeholder="Nomor rekening bank...">
                </div>

                <!-- Row 7: Nomor Referensi & Tanggal Invoice -->
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-indigo-900 uppercase tracking-widest">Nomor Referensi</label>
                    <input type="text" 
                           name="perijinan_sections[${idx}][nomor_referensi]" 
                           class="w-full px-4 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white shadow-sm" 
                           placeholder="No. Ref / No. Invoice Vendor...">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-indigo-900 uppercase tracking-widest">Tanggal Invoice Vendor</label>
                    <input type="date" 
                           name="perijinan_sections[${idx}][tanggal_invoice_vendor]" 
                           class="w-full px-4 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white shadow-sm">
                </div>

                <!-- Row 8: Keterangan -->
                <div class="md:col-span-2 space-y-1.5 mt-2">
                    <label class="block text-[10px] font-black text-indigo-900 uppercase tracking-widest">Keterangan Tambahan</label>
                    <textarea name="perijinan_sections[${idx}][keterangan]" 
                              rows="2" 
                              class="w-full px-4 py-2.5 border border-indigo-100 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white shadow-sm" 
                              placeholder="Masukkan detail tambahan jika ada..."></textarea>
                </div>
            </div>
        `;

        perijinanSectionsContainer.appendChild(section);

        const kapalSelect = section.querySelector('.perijinan-kapal-select');
        const voyageSelect = section.querySelector('.perijinan-voyage-select');
        const voyageManualInput = section.querySelector('.perijinan-voyage-manual-input');
        const voyageToggleBtn = section.querySelector('.perijinan-voyage-toggle-btn');

        // Standard event listener
        kapalSelect.addEventListener('change', function() {
            loadVoyagesForPerijinanSection(idx, this.value);
        });

        // Toggle Manual Voyage logic (similar to air)
        voyageToggleBtn.addEventListener('click', function() {
            if (voyageManualInput.classList.contains('hidden')) {
                // Switch to manual input
                voyageSelect.closest('.perijinan-select-container').classList.add('hidden');
                voyageSelect.disabled = true;
                
                voyageManualInput.classList.remove('hidden');
                voyageManualInput.disabled = false;
                voyageManualInput.focus();
                
                this.classList.remove('bg-indigo-100', 'text-indigo-600');
                this.classList.add('bg-indigo-600', 'text-white');
                this.innerHTML = '<i class="fas fa-list text-xs"></i>';
            } else {
                // Switch to select list
                voyageManualInput.classList.add('hidden');
                voyageManualInput.disabled = true;
                
                voyageSelect.closest('.perijinan-select-container').classList.remove('hidden');
                
                // Only enable select if there are options and not just 'Loading...'
                if (voyageSelect.innerHTML.indexOf('option') > -1 && voyageSelect.innerHTML.indexOf('Loading') === -1) {
                    voyageSelect.disabled = false;
                }
                
                this.classList.add('bg-indigo-100', 'text-indigo-600');
                this.classList.remove('bg-indigo-600', 'text-white');
                this.innerHTML = '<i class="fas fa-keyboard text-xs"></i>';
            }
        });
    }

    window.removePerijinanSection = function(btn) {
        const section = btn.closest('.perijinan-section');
        if (section) {
            // Cleanup section
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
            
            // Get base cost value in this section
            const baseVal = parseInt(section.querySelector('.perijinan-base-input').value.replace(/\./g, '') || 0);
            
            const subtotal = baseVal;
            const pph = 0;
            const grandTotal = baseVal;

            // Update hidden inputs
            const subtotalValue = section.querySelector('.perijinan-subtotal-value');
            const pphValue = section.querySelector('.perijinan-pph-value');
            const grandtotalValue = section.querySelector('.perijinan-grandtotal-value');

            if (subtotalValue) subtotalValue.value = subtotal;
            if (pphValue) pphValue.value = pph;
            if (grandtotalValue) grandtotalValue.value = grandTotal;
        }
        
        calculateGrandTotalPerijinan();
    };

    function calculateGrandTotalPerijinan() {
        let totalSum = 0;
        document.querySelectorAll('.perijinan-section').forEach(section => {
            const grandtotalValue = section.querySelector('.perijinan-grandtotal-value');
            if (grandtotalValue) {
                totalSum += parseInt(grandtotalValue.value || 0);
            }
        });
        
        if (typeof nominalInput !== 'undefined' && nominalInput) {
            nominalInput.value = totalSum.toLocaleString('id-ID');
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
