// ============= NOTA RETUR SECTIONS MANAGEMENT =============
    let notaReturSectionCounter = 0;
    
    // Define empty pricelist data for Nota Retur
    const notaReturPricelistData = typeof pricelistMeratusData !== 'undefined' 
        ? pricelistMeratusData.filter(item => (item.jenis_biaya || '').toString().toLowerCase().includes('retur')) 
        : [];
    
    // Pass PHP invoices data to JS
    const allInvoicesData = {!! json_encode($allInvoices ?? []) !!};

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
        
        let invoiceOptions = '<option value="">-- Pilih Nomor Invoice --</option>';
        allInvoicesData.forEach(inv => {
            const selected = (data && (data.no_invoice === inv || data.voyage === inv || data.kapal === inv)) ? 'selected' : '';
            invoiceOptions += `<option value="${inv}" ${selected}>${inv}</option>`;
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
                <h3 class="text-md font-semibold text-gray-800">Nota Retur ${sectionIndex}</h3>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeNotaReturSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Invoice <span class="text-red-500">*</span></label>
                    <select name="nota_retur_sections[${sectionIndex}][no_invoice]" class="nota-retur-invoice-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" required>
                        ${invoiceOptions}
                    </select>
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

        // Event listener for lokasi change
        lokasiSelect.addEventListener('change', () => recalcNotaReturTotal(true));
        lokasiManualInput.addEventListener('input', () => recalcNotaReturTotal(true));

        const subtotalInput = section.querySelector('.nota-retur-subtotal-input');
        const materaiInput  = section.querySelector('.nota-retur-materai-input');
        const pphInput      = section.querySelector('.nota-retur-pph-input');
        const adjustmentInput = section.querySelector('.nota-retur-adjustment-input');
        const totalInput    = section.querySelector('.nota-retur-total-input');

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
    }

    window.removeNotaReturSection = function(sectionIndex) {
        const section = document.querySelector(`[data-nota-retur-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllNotaReturSections();
        }
    };

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
