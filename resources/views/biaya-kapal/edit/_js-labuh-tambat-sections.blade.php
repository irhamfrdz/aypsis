    // ============= LABUH TAMBAT SECTIONS MANAGEMENT =============
    let labuhTambatSectionCounter = 0;
    const labuhTambatSectionsContainer = document.getElementById('labuh_tambat_sections_container');
    const addLabuhTambatSectionBtn = document.getElementById('add_labuh_tambat_section_btn');
    const addLabuhTambatSectionBottomBtn = document.getElementById('add_labuh_tambat_section_bottom_btn');

    function clearAllLabuhTambatSections() {
        if (!labuhTambatSectionsContainer) return;
        labuhTambatSectionsContainer.innerHTML = '';
        labuhTambatSectionCounter = 0;
    }

    if (addLabuhTambatSectionBtn) addLabuhTambatSectionBtn.addEventListener('click', () => addLabuhTambatSection());
    if (addLabuhTambatSectionBottomBtn) addLabuhTambatSectionBottomBtn.addEventListener('click', () => addLabuhTambatSection());

    function addLabuhTambatSection() {
        if (!labuhTambatSectionsContainer) return;
        labuhTambatSectionCounter++;
        const sectionIndex = labuhTambatSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'labuh-tambat-section mb-6 p-4 border-2 border-cyan-200 rounded-lg bg-cyan-50';
        section.setAttribute('data-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`);
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-cyan-800">Kapal/Voyage ${sectionIndex}</h3>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeLabuhTambatSection(${sectionIndex})" class="px-3 py-1 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition"><i class="fas fa-trash"></i></button>` : ''}
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="labuh_tambat[${sectionIndex}][kapal]" class="kapal-select-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                    <select name="labuh_tambat[${sectionIndex}][voyage]" class="voyage-select-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500" required disabled>
                        <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                    </select>
                </div>
                <div>
                     <label class="block text-xs font-medium text-gray-700 mb-1">No. Referensi</label>
                     <input type="text" name="labuh_tambat[${sectionIndex}][nomor_referensi]" class="no-referensi-input-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Lokasi</label>
                    <select name="labuh_tambat[${sectionIndex}][lokasi]" class="lokasi-select-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500">
                        <option value="">-- Pilih Lokasi (Opsional) --</option>
                        <option value="Jakarta">Jakarta</option>
                        <option value="Batam">Batam</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Vendor <span class="text-red-500">*</span></label>
                    <select name="labuh_tambat[${sectionIndex}][vendor]" class="vendor-select-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500" required>
                        <option value="">-- Pilih Vendor Labuh Tambat --</option>
                    </select>
                </div>
            </div>
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-medium text-gray-700">Tipe Biaya <span class="text-red-500">*</span></label>
                    <button type="button" class="add-type-btn-labuh-tambat text-cyan-600 hover:text-cyan-800 text-xs font-semibold" onclick="addTypeToLabuhTambatSection(${sectionIndex})"><i class="fas fa-plus-circle mr-1"></i>Tambah Tipe</button>
                </div>
                <div class="types-list-labuh-tambat flex flex-col gap-2"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 border-t pt-4 bg-gray-50 -mx-4 px-4 rounded-b-lg">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Penerima</label>
                    <input type="text" name="labuh_tambat[${sectionIndex}][penerima]" class="penerima-input-labuh-tambat w-full px-3 py-1.5 border border-gray-300 rounded focus:ring-2 focus:ring-cyan-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">No. Rekening</label>
                    <input type="text" name="labuh_tambat[${sectionIndex}][nomor_rekening]" class="nomor-rekening-input-labuh-tambat w-full px-3 py-1.5 border border-gray-300 rounded focus:ring-2 focus:ring-cyan-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tgl Invoice Vendor</label>
                    <input type="date" name="labuh_tambat[${sectionIndex}][tanggal_invoice_vendor]" class="tanggal-invoice-vendor-input-labuh-tambat w-full px-3 py-1.5 border border-gray-300 rounded focus:ring-2 focus:ring-cyan-500 text-sm">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Subtotal (Rp)</label>
                    <input type="text" class="sub-total-display w-full px-3 py-1.5 border border-transparent bg-transparent font-bold text-gray-800" readonly value="Rp 0">
                    <input type="hidden" name="labuh_tambat[${sectionIndex}][sub_total]" class="sub-total-value" value="0">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1">PPN 11% (Rp)</label>
                    <input type="text" class="ppn-display w-full px-3 py-1.5 border border-transparent bg-transparent font-bold text-red-600" readonly value="Rp 0">
                    <input type="hidden" name="labuh_tambat[${sectionIndex}][ppn]" class="ppn-value" value="0">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Biaya Materai (Rp)</label>
                    <input type="text" name="labuh_tambat[${sectionIndex}][biaya_materai]" class="biaya-materai-input-labuh-tambat w-full px-3 py-1.5 border border-gray-300 rounded focus:ring-2 focus:ring-cyan-500 text-sm" placeholder="0" oninput="this.value = this.value.replace(/\\D/g, '').replace(/\\B(?=(\\d{3})+(?!\\d))/g, '.'); calculateLabuhTambatSectionTotal(\${sectionIndex})">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Grand Total (Rp)</label>
                    <input type="text" class="grand-total-display w-full px-3 py-1.5 border border-transparent bg-transparent font-bold text-blue-700" readonly value="Rp 0">
                    <input type="hidden" name="labuh_tambat[${sectionIndex}][grand_total]" class="grand-total-value" value="0">
                </div>
            </div>
        `;
        labuhTambatSectionsContainer.appendChild(section);
        
        section.querySelector('.kapal-select-labuh-tambat').addEventListener('change', function() { loadVoyagesForLabuhTambatSection(sectionIndex, this.value); });
        section.querySelector('.lokasi-select-labuh-tambat').addEventListener('change', function() { updateLabuhTambatVendorsForLokasi(sectionIndex, this.value); });
        section.querySelector('.vendor-select-labuh-tambat').addEventListener('change', function() { loadTypesForLabuhTambatVendor(sectionIndex, this.value); });
        
        updateLabuhTambatVendorsForLokasi(sectionIndex, '');
        addTypeToLabuhTambatSection(sectionIndex);
    }

    function toggleLabuhTambatTypeInput(btn, sectionIndex) {
        const div = btn.closest('.flex.gap-2');
        const select = div.querySelector('.type-select-labuh-tambat');
        const manualInput = div.querySelector('.type-manual-input-labuh-tambat');
        const hiddenManual = div.querySelector('.hidden-type-manual');
        const priceInput = div.closest('.flex-col').querySelector('.price-input-labuh-tambat');

        if (select.classList.contains('hidden')) {
            select.classList.remove('hidden');
            select.disabled = false;
            manualInput.classList.add('hidden');
            manualInput.disabled = true;
            manualInput.required = false;
            hiddenManual.disabled = true;
            priceInput.readOnly = true;
            priceInput.classList.add('bg-gray-100');
            btn.innerHTML = '<i class="fas fa-keyboard"></i>';
            updateLabuhTambatPriceFromSelect(select);
        } else {
            select.classList.add('hidden');
            select.disabled = true;
            manualInput.classList.remove('hidden');
            manualInput.disabled = false;
            manualInput.required = true;
            hiddenManual.disabled = false;
            priceInput.readOnly = false;
            priceInput.classList.remove('bg-gray-100');
            btn.innerHTML = '<i class="fas fa-list"></i>';
        }
        calculateLabuhTambatSectionTotal(sectionIndex);
    }

    function updateLabuhTambatPriceFromSelect(select) {
        const option = select.options[select.selectedIndex];
        const priceInput = select.closest('.flex-col').querySelector('.price-input-labuh-tambat');
        if (option && option.dataset.harga) {
            priceInput.value = option.dataset.harga;
        } else {
            priceInput.value = 0;
        }
    }

    function loadVoyagesForLabuhTambatSection(sectionIndex, kapalNama) {
        const section = document.querySelector(`.labuh-tambat-section[data-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.voyage-select-labuh-tambat');
        if (!kapalNama) {
            voyageSelect.disabled = true;
            voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
            return;
        }
        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(kapalNama)}`)
            .then(res => res.json())
            .then(data => {
                voyageSelect.disabled = false;
                let opt = '<option value="">-- Pilih Voyage --</option><option value="DOCK">DOCK</option>';
                if (data.success && data.voyages) data.voyages.forEach(v => opt += `<option value="${v}">${v}</option>`);
                voyageSelect.innerHTML = opt;
            });
    }

    function updateLabuhTambatVendorsForLokasi(sectionIndex, lokasi) {
        const section = document.querySelector(`.labuh-tambat-section[data-section-index="${sectionIndex}"]`);
        const vendorSelect = section.querySelector('.vendor-select-labuh-tambat');
        let vendors = lokasi ? [...new Set(pricelistLabuhTambatData.filter(i => i.lokasi === lokasi).map(i => i.nama_agen))] : [...new Set(pricelistLabuhTambatData.map(i => i.nama_agen))];
        if (vendors.length > 0) {
            vendorSelect.disabled = false;
            let opt = '<option value="">-- Pilih Vendor Labuh Tambat --</option>';
            vendors.forEach(v => opt += `<option value="${v}">${v}</option>`);
            vendorSelect.innerHTML = opt;
        } else {
            vendorSelect.disabled = true;
            vendorSelect.innerHTML = '<option value="">-- Tidak ada vendor --</option>';
        }
    }

    function loadTypesForLabuhTambatVendor(sectionIndex, vendorName) {
        const section = document.querySelector(`.labuh-tambat-section[data-section-index="${sectionIndex}"]`);
        const typesList = section.querySelector('.types-list-labuh-tambat');
        const lokasiSelect = section.querySelector('.lokasi-select-labuh-tambat');
        const loc = lokasiSelect.value || '';
        const types = pricelistLabuhTambatData.filter(i => i.nama_agen === vendorName && (!loc || i.lokasi === loc));
        let opt = '<option value="">-- Pilih Type --</option>';
        types.forEach(t => opt += `<option value="${t.id}" data-harga="${t.harga}">${t.keterangan}</option>`);
        typesList.dataset.options = opt;
        typesList.querySelectorAll('.type-select-labuh-tambat').forEach(sel => {
            sel.disabled = false;
            sel.innerHTML = opt;
            updateLabuhTambatPriceFromSelect(sel);
        });
        calculateLabuhTambatSectionTotal(sectionIndex);
    }

    function addTypeToLabuhTambatSection(sectionIndex) {
        const section = document.querySelector(`.labuh-tambat-section[data-section-index="${sectionIndex}"]`);
        const typesList = section.querySelector('.types-list-labuh-tambat');
        const options = typesList.dataset.options || '<option value="">-- Pilih Type --</option>';
        const div = document.createElement('div');
        div.className = 'flex flex-col gap-1 border p-2 rounded bg-gray-50 relative';
        div.innerHTML = `
            <div class="flex gap-2 w-full">
                <select name="labuh_tambat[${sectionIndex}][types][]" class="type-select-labuh-tambat w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500" onchange="updateLabuhTambatPriceFromSelect(this); calculateLabuhTambatSectionTotal(${sectionIndex})">${options}</select>
                <input type="hidden" name="labuh_tambat[${sectionIndex}][types][]" class="hidden-type-manual" value="MANUAL" disabled>
                <input type="text" name="labuh_tambat[${sectionIndex}][manual_names][]" class="type-manual-input-labuh-tambat hidden w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Nama Type Manual">
                <button type="button" class="type-toggle-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" onclick="toggleLabuhTambatTypeInput(this, ${sectionIndex})"><i class="fas fa-keyboard"></i></button>
                <button type="button" class="text-red-500 hover:text-red-700 ml-1" onclick="this.closest('.flex-col').remove(); calculateLabuhTambatSectionTotal(${sectionIndex})"><i class="fas fa-trash"></i></button>
            </div>
            <div class="flex items-center gap-2 mt-1">
                <div class="flex-grow"><input type="number" name="labuh_tambat[${sectionIndex}][custom_prices][]" class="price-input-labuh-tambat w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-slate-500" oninput="calculateLabuhTambatSectionTotal(${sectionIndex})"></div>
                <div class="w-1/4"><input type="number" step="0.01" name="labuh_tambat[${sectionIndex}][type_tonase][]" class="tonase-input-labuh-tambat w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-slate-500" oninput="calculateLabuhTambatSectionTotal(${sectionIndex})"></div>
                <div class="flex items-center gap-2"><input type="hidden" name="labuh_tambat[${sectionIndex}][type_is_lumpsum][]" value="0" class="lumpsum-hidden"><input type="checkbox" class="lumpsum-checkbox h-5 w-5" onchange="this.previousElementSibling.value = this.checked ? 1 : 0; calculateLabuhTambatSectionTotal(${sectionIndex})"><label class="text-xs">Lumpsum</label></div>
            </div>
        `;
        typesList.appendChild(div);
    }

    function calculateLabuhTambatSectionTotal(sectionIndex) {
        const section = document.querySelector(`.labuh-tambat-section[data-section-index="${sectionIndex}"]`);
        let sub = 0;
        let taxable = 0;
        section.querySelectorAll('.types-list-labuh-tambat > div').forEach(c => {
            const h = parseFloat(c.querySelector('.price-input-labuh-tambat').value) || 0;
            const q = parseFloat(c.querySelector('.tonase-input-labuh-tambat').value) || 0;
            const isLumpsum = c.querySelector('.lumpsum-checkbox').checked;
            const cost = isLumpsum ? h : (h * q);
            sub += cost;
            
            // Identify if this is a taxable Fuel Surcharge item
            const sel = c.querySelector('.type-select-labuh-tambat');
            const man = c.querySelector('.type-manual-input-labuh-tambat');
            let name = "";
            if (sel && !sel.classList.contains('hidden')) {
                name = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].text : "";
            } else if (man) {
                name = man.value;
            }
            
            if (name.toLowerCase().includes('fuel surcharge')) {
                taxable += cost;
            }
        });
        sub = Math.round(sub);
        const ppn = Math.round(taxable * 0.11);
        const materaiField = section.querySelector('.biaya-materai-input-labuh-tambat');
        const materai = materaiField ? (parseFloat(materaiField.value.replace(/\./g, '')) || 0) : 0;
        const g = sub + ppn + materai;
        section.querySelector('.sub-total-display').value = `Rp ${sub.toLocaleString('id-ID')}`;
        section.querySelector('.sub-total-value').value = sub;
        
        const ppnDisplay = section.querySelector('.ppn-display');
        const ppnValue = section.querySelector('.ppn-value');
        if (ppnDisplay) ppnDisplay.value = `Rp ${ppn.toLocaleString('id-ID')}`;
        if (ppnValue) ppnValue.value = ppn;
        
        section.querySelector('.grand-total-display').value = `Rp ${g.toLocaleString('id-ID')}`;
        section.querySelector('.grand-total-value').value = g;
        calculateTotalFromAllLabuhTambatSections();
    }

    function calculateTotalFromAllLabuhTambatSections() {
        let t = 0;
        document.querySelectorAll('.labuh-tambat-section .grand-total-value').forEach(v => t += parseFloat(v.value) || 0);
        if (selectedJenisBiaya.nama.toLowerCase().includes('labuh tambat')) {
            nominalInput.value = t > 0 ? t.toLocaleString('id-ID') : '';
        }
    }

    window.removeLabuhTambatSection = (idx) => {
        const s = document.querySelector(`.labuh-tambat-section[data-section-index="${idx}"]`);
        if (s) { s.remove(); calculateTotalFromAllLabuhTambatSections(); }
    };

    window.addTypeToLabuhTambatSectionWithValue = function(sectionIndex, typeId, label, lumpsum, q, h) {
        const section = document.querySelector(`.labuh-tambat-section[data-section-index="${sectionIndex}"]`);
        const typesList = section.querySelector('.types-list-labuh-tambat');
        addTypeToLabuhTambatSection(sectionIndex);
        const last = typesList.lastElementChild;
        const isMan = typeId === 'MANUAL' || !typeId;
        if (isMan) {
            const btn = last.querySelector('.type-toggle-btn');
            toggleLabuhTambatTypeInput(btn, sectionIndex);
            last.querySelector('.type-manual-input-labuh-tambat').value = label || '';
            last.querySelector('.price-input-labuh-tambat').value = h;
        } else {
            const sel = last.querySelector('.type-select-labuh-tambat');
            sel.value = typeId;
            // Do NOT call updateLabuhTambatPriceFromSelect(sel) to preserve the historically saved DB price
        }
        last.querySelector('.tonase-input-labuh-tambat').value = q;
        const cb = last.querySelector('.lumpsum-checkbox');
        cb.checked = lumpsum == 1;
        cb.previousElementSibling.value = lumpsum;
        calculateLabuhTambatSectionTotal(sectionIndex);
    };

