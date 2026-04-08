    // ============= MERATUS SECTIONS MANAGEMENT =============
    // Note: meratusSectionsContainer & addMeratusSectionBtn are declared in _js-jenis-biaya.blade.php
    let meratusSectionCounter = 0;
    
    function initializeMeratusSections() {
        if (meratusSectionsContainer) meratusSectionsContainer.innerHTML = '';
        meratusSectionCounter = 0;
        addMeratusSection();
    }
    
    function clearAllMeratusSections() {
        if (meratusSectionsContainer) meratusSectionsContainer.innerHTML = '';
        meratusSectionCounter = 0;
        if (nominalInput) nominalInput.value = '';
    }
    
    if (addMeratusSectionBtn) {
        addMeratusSectionBtn.addEventListener('click', function() {
            addMeratusSection();
        });
    }
    
    window.updateMeratusPriceFromSelect = function(select, sectionIndex) {
        const container = select.closest('.meratus-type-item');
        const priceInput = container.querySelector('.price-input-meratus');
        const lokasiSelect = container.querySelector('.lokasi-select-meratus');
        const sizeSelect = container.querySelector('.size-select-meratus');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            const harga = selectedOption.getAttribute('data-harga');
            const lokasi = selectedOption.getAttribute('data-lokasi');
            const size = selectedOption.getAttribute('data-size');
            
            priceInput.value = harga || 0;
            if (lokasi) lokasiSelect.value = lokasi;
            if (size) sizeSelect.value = size;
        } else {
            priceInput.value = 0;
        }
        calculateMeratusSectionTotal(sectionIndex);
    };
    
    window.toggleMeratusTypeInput = function(btn, sectionIndex) {
        const container = btn.closest('.meratus-type-item');
        const select = container.querySelector('.type-select-meratus');
        const manualInput = container.querySelector('.type-manual-input-meratus');
        const hiddenManual = container.querySelector('.hidden-type-manual-meratus');
        const priceInput = container.querySelector('.price-input-meratus');
        
        if (manualInput.classList.contains('hidden')) {
            // Switch to Manual
            select.classList.add('hidden');
            select.disabled = true;
            select.required = false;
            
            manualInput.classList.remove('hidden');
            manualInput.required = true;
            
            hiddenManual.disabled = false;
            
            btn.classList.add('bg-blue-200', 'text-blue-700');
            btn.classList.remove('bg-gray-200', 'text-gray-600');
            btn.innerHTML = '<i class="fas fa-list"></i>';
            btn.title = "Switch to List Selection";
            
            priceInput.readOnly = false;
            priceInput.classList.remove('bg-gray-100');
            priceInput.classList.add('bg-white');
            priceInput.value = '';
            priceInput.focus();
        } else {
            // Switch to Select
            manualInput.classList.add('hidden');
            manualInput.required = false;
            
            select.classList.remove('hidden');
            select.disabled = false;
            select.required = true;
            
            hiddenManual.disabled = true;
            
            btn.classList.remove('bg-blue-200', 'text-blue-700');
            btn.classList.add('bg-gray-200', 'text-gray-600');
            btn.innerHTML = '<i class="fas fa-keyboard"></i>';
            btn.title = "Switch to Manual Input";
            
            priceInput.readOnly = true;
            priceInput.classList.add('bg-gray-100');
            priceInput.classList.remove('bg-white');
            
            updateMeratusPriceFromSelect(select, sectionIndex);
        }
        
        calculateMeratusSectionTotal(sectionIndex);
    };
 
    function addMeratusSection() {
        meratusSectionCounter++;
        const sectionIndex = meratusSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'meratus-section mb-6 p-4 border-2 border-blue-200 rounded-lg bg-blue-50';
        section.setAttribute('data-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });
 
        // Meratus options from pricelistMeratusData
        let meratusOptions = '<option value="">-- Pilih Jenis Biaya Meratus --</option>';
        pricelistMeratusData.forEach(item => {
            const locStr = item.lokasi ? ` (${item.lokasi})` : '';
            const sizeStr = item.size ? ` - ${item.size}` : '';
            meratusOptions += `<option value="${item.id}" data-harga="${item.harga}" data-lokasi="${item.lokasi || ''}" data-size="${item.size || ''}">${item.jenis_biaya}${sizeStr}${locStr} - Rp ${parseInt(item.harga).toLocaleString('id-ID')}</option>`;
        });
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-md font-semibold text-blue-800">
                    <i class="fas fa-ship mr-2"></i>Kapal ${sectionIndex} (Meratus)
                </h4>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeMeratusSection(${sectionIndex})" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs rounded transition"><i class="fas fa-times mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kapal</label>
                    <select name="meratus[${sectionIndex}][kapal]" class="kapal-select-meratus w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Voyage</label>
                    <div class="flex gap-2">
                        <select name="meratus[${sectionIndex}][voyage]" class="voyage-select-meratus w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" disabled required>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="meratus[${sectionIndex}][voyage]" class="voyage-input-meratus w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="voyage-manual-btn-meratus px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
                <div class="meratus-types-container md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Detail Biaya Tagihan Meratus</label>
                    <div class="meratus-types-list space-y-2 mb-2">
                        <div class="meratus-type-item flex flex-col gap-1 border p-3 rounded bg-white relative">
                            <div class="flex gap-2 w-full">
                                <select name="meratus[${sectionIndex}][types][]" class="type-select-meratus w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" required onchange="updateMeratusPriceFromSelect(this, ${sectionIndex})">
                                    ${meratusOptions}
                                </select>
                                
                                <input type="hidden" name="meratus[${sectionIndex}][types][]" class="hidden-type-manual-meratus" value="MANUAL" disabled>
                                
                                <input type="text" name="meratus[${sectionIndex}][manual_names][]" class="type-manual-input-meratus hidden w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Nama Biaya Manual">
                                
                                <button type="button" class="type-toggle-btn-meratus px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Switch Input (Master/Manual)" onclick="toggleMeratusTypeInput(this, ${sectionIndex})">
                                    <i class="fas fa-keyboard"></i>
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 mt-1">
                                <div>
                                    <label class="text-xs text-gray-500 block mb-1">Lokasi</label>
                                    <select name="meratus[${sectionIndex}][lokasi_items][]" class="lokasi-select-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Lokasi --</option>
                                        <option value="Jakarta">Jakarta</option>
                                        <option value="Batam">Batam</option>
                                        <option value="Pinang">Pinang</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 block mb-1">Size</label>
                                    <select name="meratus[${sectionIndex}][size_items][]" class="size-select-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Size --</option>
                                        <option value="20ft">20ft</option>
                                        <option value="40ft">40ft</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 block mb-1">Harga Satuan (Rp)</label>
                                    <input type="number" name="meratus[${sectionIndex}][custom_prices][]" class="price-input-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 bg-gray-100" placeholder="0" readonly oninput="calculateMeratusSectionTotal(${sectionIndex})">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 block mb-1">Kuantitas</label>
                                    <input type="number" step="0.01" min="0" name="meratus[${sectionIndex}][quantities][]" class="quantity-input-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" placeholder="0" oninput="calculateMeratusSectionTotal(${sectionIndex})">
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="add-type-btn-meratus text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded transition duration-200 flex items-center gap-1" onclick="addTypeToMeratusSection(${sectionIndex})">
                        <i class="fas fa-plus"></i> Tambah Biaya
                    </button>
                </div>
 
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sub Total</label>
                    <input type="text" class="sub-total-display-meratus w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" value="Rp 0" readonly>
                    <input type="hidden" name="meratus[${sectionIndex}][sub_total]" class="sub-total-value-meratus" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PPH (2%)</label>
                    <input type="text" class="pph-display-meratus w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" value="Rp 0" readonly>
                    <input type="hidden" name="meratus[${sectionIndex}][pph]" class="pph-value-meratus" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grand Total</label>
                    <input type="text" class="grand-total-display-meratus w-full px-3 py-2 border border-gray-300 rounded-lg bg-emerald-50 font-semibold cursor-not-allowed" value="Rp 0" readonly>
                    <input type="hidden" name="meratus[${sectionIndex}][grand_total]" class="grand-total-value-meratus" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Referensi</label>
                    <input type="text" name="meratus[${sectionIndex}][nomor_referensi]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Masukkan No. Referensi">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penerima</label>
                    <input type="text" name="meratus[${sectionIndex}][penerima]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nama penerima">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                    <input type="text" name="meratus[${sectionIndex}][nomor_rekening]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nomor rekening">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Invoice Vendor</label>
                    <input type="date" name="meratus[${sectionIndex}][tanggal_invoice_vendor]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="meratus[${sectionIndex}][keterangan]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" rows="2" placeholder="Catatan opsional..."></textarea>
                </div>
            </div>
        `;
        
        meratusSectionsContainer.appendChild(section);
        
        // Setup kapal change listener
        const kapalSelect = section.querySelector('.kapal-select-meratus');
        kapalSelect.addEventListener('change', function() {
            loadVoyagesForMeratusSection(sectionIndex, this.value);
        });
        
        // Manual voyage toggle
        const voyageSelect = section.querySelector('.voyage-select-meratus');
        const voyageInput = section.querySelector('.voyage-input-meratus');
        const voyageManualBtn = section.querySelector('.voyage-manual-btn-meratus');
 
        voyageManualBtn.addEventListener('click', function() {
            if (voyageInput.classList.contains('hidden')) {
                voyageSelect.classList.add('hidden');
                voyageSelect.disabled = true;
                voyageInput.classList.remove('hidden');
                voyageInput.disabled = false;
                voyageInput.focus();
                this.classList.add('bg-blue-200', 'text-blue-700');
                this.innerHTML = '<i class="fas fa-list"></i>';
            } else {
                voyageInput.classList.add('hidden');
                voyageInput.disabled = true;
                voyageSelect.classList.remove('hidden');
                if (kapalSelect.value) voyageSelect.disabled = false;
                this.classList.remove('bg-blue-200', 'text-blue-700');
                this.innerHTML = '<i class="fas fa-keyboard"></i>';
            }
        });
        
        calculateMeratusSectionTotal(sectionIndex);
    }
    
    function addTypeToMeratusSection(sectionIndex) {
        const section = document.querySelector(`.meratus-section[data-section-index="${sectionIndex}"]`);
        const typesList = section.querySelector('.meratus-types-list');
        
        // Get meratus options
        let meratusOptions = '<option value="">-- Pilih Jenis Biaya Meratus --</option>';
        pricelistMeratusData.forEach(item => {
            const locStr = item.lokasi ? ` (${item.lokasi})` : '';
            const sizeStr = item.size ? ` - ${item.size}` : '';
            meratusOptions += `<option value="${item.id}" data-harga="${item.harga}" data-lokasi="${item.lokasi || ''}" data-size="${item.size || ''}">${item.jenis_biaya}${sizeStr}${locStr} - Rp ${parseInt(item.harga).toLocaleString('id-ID')}</option>`;
        });
        
        const div = document.createElement('div');
        div.className = 'meratus-type-item flex flex-col gap-1 border p-3 rounded bg-white relative';
        div.innerHTML = `
            <div class="flex gap-2 w-full">
                <select name="meratus[${sectionIndex}][types][]" class="type-select-meratus w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" required onchange="updateMeratusPriceFromSelect(this, ${sectionIndex})">
                    ${meratusOptions}
                </select>
                
                <input type="hidden" name="meratus[${sectionIndex}][types][]" class="hidden-type-manual-meratus" value="MANUAL" disabled>
                
                <input type="text" name="meratus[${sectionIndex}][manual_names][]" class="type-manual-input-meratus hidden w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Nama Biaya Manual">
                
                <button type="button" class="type-toggle-btn-meratus px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Switch Input (Master/Manual)" onclick="toggleMeratusTypeInput(this, ${sectionIndex})">
                    <i class="fas fa-keyboard"></i>
                </button>
                    
                <button type="button" class="text-red-500 hover:text-red-700 ml-1" onclick="this.closest('.meratus-type-item').remove(); calculateMeratusSectionTotal(${sectionIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 mt-1">
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Lokasi</label>
                    <select name="meratus[${sectionIndex}][lokasi_items][]" class="lokasi-select-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Lokasi --</option>
                        <option value="Jakarta">Jakarta</option>
                        <option value="Batam">Batam</option>
                        <option value="Pinang">Pinang</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Size</label>
                    <select name="meratus[${sectionIndex}][size_items][]" class="size-select-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Size --</option>
                        <option value="20ft">20ft</option>
                        <option value="40ft">40ft</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Harga Satuan (Rp)</label>
                    <input type="number" name="meratus[${sectionIndex}][custom_prices][]" class="price-input-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 bg-gray-100" placeholder="0" readonly oninput="calculateMeratusSectionTotal(${sectionIndex})">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Kuantitas</label>
                    <input type="number" step="0.01" min="0" name="meratus[${sectionIndex}][quantities][]" class="quantity-input-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" placeholder="0" oninput="calculateMeratusSectionTotal(${sectionIndex})">
                </div>
            </div>
        `;
        
        typesList.appendChild(div);
    }
    
    window.removeMeratusSection = function(sectionIndex) {
        const section = document.querySelector(`.meratus-section[data-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllMeratusSections();
        }
    };
    
    function loadVoyagesForMeratusSection(sectionIndex, kapalNama) {
        const section = document.querySelector(`.meratus-section[data-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.voyage-select-meratus');
        
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
                voyageSelect.disabled = false;
                let options = '<option value="">-- Pilih Voyage --</option><option value="DOCK">DOCK</option>';
                if (data && data.success && data.voyages) {
                    data.voyages.forEach(v => options += `<option value="${v}">${v}</option>`);
                }
                voyageSelect.innerHTML = options;
            })
            .catch(() => {
                voyageSelect.disabled = false;
                voyageSelect.innerHTML = '<option value="">-- Pilih Voyage --</option><option value="DOCK">DOCK</option>';
            });
    }
    
    function calculateMeratusSectionTotal(sectionIndex) {
        const section = document.querySelector(`.meratus-section[data-section-index="${sectionIndex}"]`);
        if (!section) return;
        
        const typeItems = section.querySelectorAll('.meratus-type-item');
        
        let subTotal = 0;
        typeItems.forEach(item => {
            const price = parseFloat(item.querySelector('.price-input-meratus').value) || 0;
            const qty = parseFloat(item.querySelector('.quantity-input-meratus').value) || 0;
            subTotal += (price * qty);
        });
        
        const pph = Math.round(subTotal * 0.02);
        const grandTotal = subTotal - pph;
        
        section.querySelector('.sub-total-display-meratus').value = subTotal > 0 ? `Rp ${subTotal.toLocaleString('id-ID')}` : 'Rp 0';
        section.querySelector('.sub-total-value-meratus').value = subTotal;
        
        section.querySelector('.pph-display-meratus').value = pph > 0 ? `Rp ${pph.toLocaleString('id-ID')}` : 'Rp 0';
        section.querySelector('.pph-value-meratus').value = pph;
        
        section.querySelector('.grand-total-display-meratus').value = grandTotal > 0 ? `Rp ${grandTotal.toLocaleString('id-ID')}` : 'Rp 0';
        section.querySelector('.grand-total-value-meratus').value = grandTotal;
        
        calculateTotalFromAllMeratusSections();
    }
    
    function calculateTotalFromAllMeratusSections() {
        let grandTotalAll = 0;
        document.querySelectorAll('.meratus-section').forEach(section => {
            grandTotalAll += parseFloat(section.querySelector('.grand-total-value-meratus').value) || 0;
        });
        
        if (nominalInput) {
            nominalInput.value = grandTotalAll > 0 ? grandTotalAll.toLocaleString('id-ID') : '';
        }
    }
