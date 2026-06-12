    // ============= TANTO SECTIONS MANAGEMENT =============
    // Note: tantoSectionsContainer & addTantoSectionBtn are declared in _js-jenis-biaya.blade.php
    let tantoSectionCounter = 0;
    
    function initializeTantoSections() {
        if (tantoSectionsContainer) tantoSectionsContainer.innerHTML = '';
        tantoSectionCounter = 0;
        addTantoSection();
    }
    
    function clearAllTantoSections() {
        if (tantoSectionsContainer) tantoSectionsContainer.innerHTML = '';
        tantoSectionCounter = 0;
        if (nominalInput) nominalInput.value = '';
    }
    
    if (addTantoSectionBtn) {
        addTantoSectionBtn.addEventListener('click', function() {
            addTantoSection();
        });
    }
    
    window.updateTantoPriceFromSelect = function(select, sectionIndex) {
        const container = select.closest('.tanto-type-item');
        const priceInput = container.querySelector('.price-input-tanto');
        const lokasiSelect = container.querySelector('.lokasi-select-tanto');
        const sizeSelect = container.querySelector('.size-select-tanto');
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
        calculateTantoSectionTotal(sectionIndex);
    };
    
    window.toggleTantoTypeInput = function(btn, sectionIndex) {
        const container = btn.closest('.tanto-type-item');
        const select = container.querySelector('.type-select-tanto');
        const manualInput = container.querySelector('.type-manual-input-tanto');
        const hiddenManual = container.querySelector('.hidden-type-manual-tanto');
        const priceInput = container.querySelector('.price-input-tanto');
        
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
            
            updateTantoPriceFromSelect(select, sectionIndex);
        }
        
        calculateTantoSectionTotal(sectionIndex);
    };
 
    function addTantoSection() {
        tantoSectionCounter++;
        const sectionIndex = tantoSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'tanto-section mb-6 p-4 border-2 border-blue-200 rounded-lg bg-blue-50';
        section.setAttribute('data-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });
 
        // Tanto options from pricelistTantoData
        let tantoOptions = '<option value="">-- Pilih Jenis Biaya Tanto --</option>';
        pricelistTantoData.forEach(item => {
            const locStr = item.lokasi ? ` (${item.lokasi})` : '';
            const sizeStr = item.size ? ` - ${item.size}` : '';
            tantoOptions += `<option value="${item.id}" data-harga="${parseInt(item.harga)}" data-lokasi="${item.lokasi || ''}" data-size="${item.size || ''}">${item.jenis_biaya}${sizeStr}${locStr} - Rp ${parseInt(item.harga).toLocaleString('id-ID')}</option>`;
        });
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-md font-semibold text-blue-800">
                    <i class="fas fa-ship mr-2"></i>Kapal ${sectionIndex} (Tanto)
                </h4>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeTantoSection(${sectionIndex})" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs rounded transition"><i class="fas fa-times mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kapal</label>
                    <select name="tanto[${sectionIndex}][kapal]" class="kapal-select-tanto w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Voyage</label>
                    <div class="flex gap-2">
                        <select name="tanto[${sectionIndex}][voyage]" class="voyage-select-tanto w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" disabled required>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="tanto[${sectionIndex}][voyage]" class="voyage-input-tanto w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="voyage-manual-btn-tanto px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Container Info Panel -->
                <div class="tanto-container-info-wrapper md:col-span-2 hidden">
                    <div class="tanto-container-info-content text-xs font-semibold text-blue-800 bg-blue-100/70 border border-blue-200 p-3 rounded-lg flex flex-col gap-2">
                        <div class="flex flex-wrap gap-x-4 gap-y-1 items-center">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            <span>Info Kontainer (Manifest):</span>
                            <span class="tanto-info-20">20ft: 0</span>
                            <span class="text-blue-300">|</span>
                            <span class="tanto-info-40">40ft: 0</span>
                            <span class="text-blue-300">|</span>
                            <span class="tanto-info-pelabuhan">Rute: -</span>
                        </div>
                        <div class="tanto-info-list-kontainer border-t border-blue-200/40 pt-2 mt-1">
                            <span class="font-bold text-blue-900 block mb-1.5"><i class="fas fa-boxes mr-1"></i>Nomor Kontainer:</span>
                            <div class="flex flex-wrap gap-1 tanto-badges-container">
                                <!-- Container Badges -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="tanto-types-container md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Detail Biaya Tagihan Tanto</label>
                    <div class="tanto-types-list space-y-2 mb-2">
                        <div class="tanto-type-item flex flex-col gap-1 border p-3 rounded bg-white relative">
                            <div class="flex gap-2 w-full">
                                <select name="tanto[${sectionIndex}][types][]" class="type-select-tanto w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" required onchange="updateTantoPriceFromSelect(this, ${sectionIndex})">
                                    ${tantoOptions}
                                </select>
                                
                                <input type="hidden" name="tanto[${sectionIndex}][types][]" class="hidden-type-manual-tanto" value="MANUAL" disabled>
                                
                                <input type="text" name="tanto[${sectionIndex}][manual_names][]" class="type-manual-input-tanto hidden w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Nama Biaya Manual">
                                
                                <button type="button" class="type-toggle-btn-tanto px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Switch Input (Master/Manual)" onclick="toggleTantoTypeInput(this, ${sectionIndex})">
                                    <i class="fas fa-keyboard"></i>
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-2 lg:grid-cols-6 gap-2 mt-1">
                                <div>
                                    <label class="text-xs text-gray-500 block mb-1">Lokasi</label>
                                    <select name="tanto[${sectionIndex}][lokasi_items][]" class="lokasi-select-tanto w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Lokasi --</option>
                                        <option value="Jakarta">Jakarta</option>
                                        <option value="Batam">Batam</option>
                                        <option value="Pinang">Pinang</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 block mb-1">Size</label>
                                    <select name="tanto[${sectionIndex}][size_items][]" class="size-select-tanto w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Size --</option>
                                        <option value="20ft">20ft</option>
                                        <option value="40ft">40ft</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 block mb-1">Harga Satuan (Rp)</label>
                                    <input type="number" name="tanto[${sectionIndex}][custom_prices][]" class="price-input-tanto w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 bg-white" placeholder="0" oninput="calculateTantoSectionTotal(${sectionIndex})">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 block mb-1">Kuantitas</label>
                                    <input type="number" step="0.01" min="0" name="tanto[${sectionIndex}][quantities][]" class="quantity-input-tanto w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" placeholder="0" oninput="calculateTantoSectionTotal(${sectionIndex})">
                                </div>
                                <div class="flex items-end pb-1">
                                    <label class="flex items-center gap-1 text-xs text-gray-700 cursor-pointer">
                                        <input type="hidden" name="tanto[${sectionIndex}][is_muat][]" value="0">
                                        <input type="checkbox" value="1" class="is-muat-checkbox w-4 h-4 rounded text-blue-600 focus:ring-blue-500" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                                        Muat
                                    </label>
                                </div>
                                <div class="flex items-end pb-1">
                                    <label class="flex items-center gap-1 text-xs text-gray-700 cursor-pointer">
                                        <input type="hidden" name="tanto[${sectionIndex}][is_bongkar][]" value="0">
                                        <input type="checkbox" value="1" class="is-bongkar-checkbox w-4 h-4 rounded text-blue-600 focus:ring-blue-500" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                                        Bongkar
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="add-type-btn-tanto text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded transition duration-200 flex items-center gap-1" onclick="addTypeToTantoSection(${sectionIndex})">
                        <i class="fas fa-plus"></i> Tambah Biaya
                    </button>
                </div>
 
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sub Total</label>
                    <input type="text" class="sub-total-display-tanto w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" value="Rp 0" readonly>
                    <input type="hidden" name="tanto[${sectionIndex}][sub_total]" class="sub-total-value-tanto" value="0">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700">PPH (2%)</label>
                        <div class="flex items-center gap-1">
                            <input type="checkbox" name="tanto[${sectionIndex}][pph_active]" class="pph-active-tanto w-4 h-4 rounded text-blue-600 focus:ring-blue-500 cursor-pointer" checked onchange="calculateTantoSectionTotal(${sectionIndex})">
                            <span class="text-[10px] text-gray-600 font-medium cursor-pointer" onclick="this.previousElementSibling.click()">Aktifkan</span>
                        </div>
                    </div>
                    <input type="text" class="pph-display-tanto w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-colors duration-200" value="Rp 0">
                    <input type="hidden" name="tanto[${sectionIndex}][pph]" class="pph-value-tanto" value="0">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700">PPN (11%)</label>
                        <div class="flex items-center gap-1">
                            <input type="checkbox" name="tanto[${sectionIndex}][ppn_active]" class="ppn-active-tanto w-4 h-4 rounded text-blue-600 focus:ring-blue-500 cursor-pointer" onchange="calculateTantoSectionTotal(${sectionIndex})">
                            <span class="text-[10px] text-gray-600 font-medium cursor-pointer" onclick="this.previousElementSibling.click()">Aktifkan</span>
                        </div>
                    </div>
                    <input type="text" class="ppn-display-tanto w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-colors duration-200" value="Rp 0">
                    <input type="hidden" name="tanto[${sectionIndex}][ppn]" class="ppn-value-tanto" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Materai</label>
                    <input type="text" class="materai-display-tanto w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="Rp 0">
                    <input type="hidden" name="tanto[${sectionIndex}][biaya_materai]" class="materai-value-tanto" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adjustment</label>
                    <input type="text" class="adjustment-display-tanto w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="Rp 0">
                    <input type="hidden" name="tanto[${sectionIndex}][adjustment]" class="adjustment-value-tanto" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grand Total</label>
                    <input type="text" class="grand-total-display-tanto w-full px-3 py-2 border border-gray-300 rounded-lg bg-emerald-50 font-semibold cursor-not-allowed" value="Rp 0" readonly>
                    <input type="hidden" name="tanto[${sectionIndex}][grand_total]" class="grand-total-value-tanto" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Referensi</label>
                    <input type="text" name="tanto[${sectionIndex}][nomor_referensi]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Masukkan No. Referensi">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penerima</label>
                    <input type="text" name="tanto[${sectionIndex}][penerima]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nama penerima">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                    <input type="text" name="tanto[${sectionIndex}][nomor_rekening]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nomor rekening">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Invoice Vendor</label>
                    <input type="date" name="tanto[${sectionIndex}][tanggal_invoice_vendor]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="tanto[${sectionIndex}][keterangan]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" rows="2" placeholder="Catatan opsional..."></textarea>
                </div>
            </div>
        `;
        
        tantoSectionsContainer.appendChild(section);
        
        // Setup kapal change listener
        const kapalSelect = section.querySelector('.kapal-select-tanto');
        kapalSelect.addEventListener('change', function() {
            loadVoyagesForTantoSection(sectionIndex, this.value);
        });
        
        // Setup voyage change listener
        const voyageSelect = section.querySelector('.voyage-select-tanto');
        voyageSelect.addEventListener('change', function() {
            const kapalNama = kapalSelect.value;
            const voyageValue = this.value;
            if (kapalNama && voyageValue) {
                autoFillTantoForSection(sectionIndex, kapalNama, voyageValue);
            }
        });
        
        // Manual voyage toggle
        const voyageInput = section.querySelector('.voyage-input-tanto');
        const voyageManualBtn = section.querySelector('.voyage-manual-btn-tanto');
 
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
 
        // PPH Manual edit listener
        const pphDisplay = section.querySelector('.pph-display-tanto');
        const pphValue = section.querySelector('.pph-value-tanto');
        pphDisplay.addEventListener('input', function() {
            this.setAttribute('data-manual-pph', 'true');
            let val = this.value.replace(/\D/g, '');
            if (val) {
                this.value = 'Rp ' + parseInt(val).toLocaleString('id-ID');
                pphValue.value = val;
            } else {
                this.value = 'Rp 0';
                pphValue.value = 0;
            }
            calculateTantoSectionTotal(sectionIndex);
        });
 
        // PPN Manual edit listener
        const ppnDisplay = section.querySelector('.ppn-display-tanto');
        const ppnValue = section.querySelector('.ppn-value-tanto');
        ppnDisplay.addEventListener('input', function() {
            this.setAttribute('data-manual-ppn', 'true');
            let val = this.value.replace(/\D/g, '');
            if (val) {
                this.value = 'Rp ' + parseInt(val).toLocaleString('id-ID');
                ppnValue.value = val;
            } else {
                this.value = 'Rp 0';
                ppnValue.value = 0;
            }
            calculateTantoSectionTotal(sectionIndex);
        });
 
        // Materai Manual edit listener
        const materaiDisplay = section.querySelector('.materai-display-tanto');
        const materaiValue = section.querySelector('.materai-value-tanto');
        materaiDisplay.addEventListener('input', function() {
            let val = this.value.replace(/\D/g, '');
            if (val) {
                this.value = 'Rp ' + parseInt(val).toLocaleString('id-ID');
                materaiValue.value = val;
            } else {
                this.value = 'Rp 0';
                materaiValue.value = 0;
            }
            calculateTantoSectionTotal(sectionIndex);
        });
 
        // Adjustment Manual edit listener
        const adjustmentDisplay = section.querySelector('.adjustment-display-tanto');
        const adjustmentValue = section.querySelector('.adjustment-value-tanto');
        adjustmentDisplay.addEventListener('input', function() {
            // Allow negative prefix for adjustment
            let isNegative = this.value.includes('-');
            let val = this.value.replace(/\D/g, '');
            
            if (val) {
                let numVal = parseInt(val) * (isNegative ? -1 : 1);
                this.value = (isNegative ? '- Rp ' : 'Rp ') + parseInt(val).toLocaleString('id-ID');
                adjustmentValue.value = numVal;
            } else {
                this.value = 'Rp 0';
                adjustmentValue.value = 0;
            }
            calculateTantoSectionTotal(sectionIndex);
        });
        
        calculateTantoSectionTotal(sectionIndex);
    }
    
    // Auto-fill Tanto based on container counts from manifest table
    function autoFillTantoForSection(sectionIndex, kapalNama, voyage) {
        const section = document.querySelector(`.tanto-section[data-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.tanto-types-list');
        
        // Show loading
        container.innerHTML = '<div class="text-sm text-blue-500 italic py-2"><i class="fas fa-spinner fa-spin mr-2"></i>Menghitung kontainer...</div>';
        
        fetch('{{ url("biaya-kapal/get-container-counts") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                kapal: kapalNama,
                voyage: voyage
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.counts) {
                container.innerHTML = '';
                
                // Store containers list on the section element for dynamic addition
                const containersList = data.containers_data || [];
                section.setAttribute('data-containers', JSON.stringify(containersList));
                
                // Determine location from pelabuhan_tujuan / pelabuhan_asal / pelabuhan_muat / pelabuhan_bongkar
                let location = 'Jakarta'; // default
                const pAsal = (data.pelabuhan_asal || '').toLowerCase();
                const pTujuan = (data.pelabuhan_tujuan || '').toLowerCase();
                const pMuat = (data.pelabuhan_muat || '').toLowerCase();
                const pBongkar = (data.pelabuhan_bongkar || '').toLowerCase();
                
                if (pTujuan.includes('batam') || pBongkar.includes('batam') || pAsal.includes('batam') || pMuat.includes('batam')) {
                    location = 'Batam';
                } else if (pTujuan.includes('pinang') || pTujuan.includes('kijang') || pBongkar.includes('pinang') || pAsal.includes('pinang') || pAsal.includes('kijang') || pMuat.includes('pinang')) {
                    location = 'Pinang';
                }
                
                // Calculate quantities
                const qty20 = (data.counts['20'] ? ((data.counts['20'].full || 0) + (data.counts['20'].empty || 0)) : 0);
                const qty40 = (data.counts['40'] ? ((data.counts['40'].full || 0) + (data.counts['40'].empty || 0)) : 0);
                
                // Update container info display
                const infoWrapper = section.querySelector('.tanto-container-info-wrapper');
                const info20 = section.querySelector('.tanto-info-20');
                const info40 = section.querySelector('.tanto-info-40');
                const infoPelabuhan = section.querySelector('.tanto-info-pelabuhan');
                const badgesContainer = section.querySelector('.tanto-badges-container');
                
                if (infoWrapper) {
                    const c20 = data.counts['20'] || { full: 0, empty: 0 };
                    const c40 = data.counts['40'] || { full: 0, empty: 0 };
                    
                    info20.innerHTML = `<span class="font-bold text-blue-900">20ft:</span> ${qty20} (Full: ${c20.full || 0}, Empty: ${c20.empty || 0})`;
                    info40.innerHTML = `<span class="font-bold text-blue-900">40ft:</span> ${qty40} (Full: ${c40.full || 0}, Empty: ${c40.empty || 0})`;
                    
                    const pAsalName = data.pelabuhan_asal || '-';
                    const pTujuanName = data.pelabuhan_tujuan || '-';
                    infoPelabuhan.innerHTML = `<span class="font-bold text-blue-900">Rute:</span> ${pAsalName} &rarr; ${pTujuanName}`;
                    
                    if (badgesContainer) {
                        badgesContainer.innerHTML = '';
                        if (containersList.length > 0) {
                            containersList.forEach(c => {
                                const badge = document.createElement('span');
                                badge.className = 'inline-block bg-blue-200 text-blue-800 px-2 py-0.5 rounded text-[10px] font-mono border border-blue-300';
                                badge.textContent = `${c.nomor_kontainer} (${c.size})`;
                                badgesContainer.appendChild(badge);
                            });
                        } else {
                            badgesContainer.innerHTML = '<span class="text-gray-500 italic text-[11px]">Tidak ada nomor kontainer</span>';
                        }
                    }
                    
                    infoWrapper.classList.remove('hidden');
                }
                
                let addedAny = false;
                
                // Filter pricelistTantoData by matching location
                const matchedItems = pricelistTantoData.filter(item => {
                    const itemLoc = item.lokasi || '';
                    return itemLoc.toLowerCase() === location.toLowerCase();
                });
                
                matchedItems.forEach(item => {
                    if (item.size === '20ft') {
                        const sizeContainers = containersList.filter(c => (c.size || '').includes('20'));
                        sizeContainers.forEach(c => {
                            addTypeToTantoSectionWithValue(sectionIndex, item.id, 1, item.lokasi, item.size, item.harga, c.nomor_kontainer, c.id);
                            addedAny = true;
                        });
                    } else if (item.size === '40ft') {
                        const sizeContainers = containersList.filter(c => (c.size || '').includes('40'));
                        sizeContainers.forEach(c => {
                            addTypeToTantoSectionWithValue(sectionIndex, item.id, 1, item.lokasi, item.size, item.harga, c.nomor_kontainer, c.id);
                            addedAny = true;
                        });
                    } else {
                        // flat fee / no size
                        if (containersList.length > 0) {
                            addTypeToTantoSectionWithValue(sectionIndex, item.id, 1, item.lokasi, item.size, item.harga, null, null);
                            addedAny = true;
                        }
                    }
                });
                
                if (!addedAny) {
                    addTypeToTantoSection(sectionIndex);
                }
                
                calculateTantoSectionTotal(sectionIndex);
            } else {
                container.innerHTML = '';
                addTypeToTantoSection(sectionIndex);
            }
        })
        .catch(error => {
            console.error('Error fetching container counts:', error);
            container.innerHTML = '';
            addTypeToTantoSection(sectionIndex);
        });
    }

    // Add Tanto type input with values
    window.addTypeToTantoSectionWithValue = function(sectionIndex, pricelistId, quantity, lokasi, size, harga, selectedContainer = null, selectedBlId = null) {
        const section = document.querySelector(`.tanto-section[data-section-index="${sectionIndex}"]`);
        const typesList = section.querySelector('.tanto-types-list');
        
        let tantoOptions = '<option value="">-- Pilih Jenis Biaya Tanto --</option>';
        pricelistTantoData.forEach(item => {
            const selected = item.id == pricelistId ? 'selected' : '';
            const locStr = item.lokasi ? ` (${item.lokasi})` : '';
            const sizeStr = item.size ? ` - ${item.size}` : '';
            tantoOptions += `<option value="${item.id}" data-harga="${parseInt(item.harga)}" data-lokasi="${item.lokasi || ''}" data-size="${item.size || ''}" ${selected}>${item.jenis_biaya}${sizeStr}${locStr} - Rp ${parseInt(item.harga).toLocaleString('id-ID')}</option>`;
        });
        
        const containerOptions = getContainerOptionsForTantoSection(sectionIndex, selectedContainer);
        
        const div = document.createElement('div');
        div.className = 'tanto-type-item flex flex-col gap-1 border p-3 rounded bg-white relative';
        div.innerHTML = `
            <div class="flex gap-2 w-full">
                <select name="tanto[${sectionIndex}][types][]" class="type-select-tanto w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" required onchange="updateTantoPriceFromSelect(this, ${sectionIndex})">
                    ${tantoOptions}
                </select>
                
                <input type="hidden" name="tanto[${sectionIndex}][types][]" class="hidden-type-manual-tanto" value="MANUAL" disabled>
                
                <input type="text" name="tanto[${sectionIndex}][manual_names][]" class="type-manual-input-tanto hidden w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Nama Biaya Manual">
                
                <button type="button" class="type-toggle-btn-tanto px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Switch Input (Master/Manual)" onclick="toggleTantoTypeInput(this, ${sectionIndex})">
                    <i class="fas fa-keyboard"></i>
                </button>
                    
                <button type="button" class="text-red-500 hover:text-red-700 ml-1" onclick="this.closest('.tanto-type-item').remove(); calculateTantoSectionTotal(${sectionIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-2 lg:grid-cols-7 gap-2 mt-1">
                <div>
                    <label class="text-xs text-gray-500 block mb-1">No. Kontainer</label>
                    <select name="tanto[${sectionIndex}][nomor_kontainers][]" class="container-select-tanto w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" onchange="updateTantoContainerSize(this)">
                        ${containerOptions}
                    </select>
                    <input type="hidden" name="tanto[${sectionIndex}][bl_ids][]" class="bl-id-input-tanto" value="${selectedBlId || ''}">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Lokasi</label>
                    <select name="tanto[${sectionIndex}][lokasi_items][]" class="lokasi-select-tanto w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Lokasi --</option>
                        <option value="Jakarta" ${lokasi === 'Jakarta' ? 'selected' : ''}>Jakarta</option>
                        <option value="Batam" ${lokasi === 'Batam' ? 'selected' : ''}>Batam</option>
                        <option value="Pinang" ${lokasi === 'Pinang' ? 'selected' : ''}>Pinang</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Size</label>
                    <select name="tanto[${sectionIndex}][size_items][]" class="size-select-tanto w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Size --</option>
                        <option value="20ft" ${size === '20ft' ? 'selected' : ''}>20ft</option>
                        <option value="40ft" ${size === '40ft' ? 'selected' : ''}>40ft</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Harga Satuan (Rp)</label>
                    <input type="number" name="tanto[${sectionIndex}][custom_prices][]" value="${parseInt(harga) || 0}" class="price-input-tanto w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 bg-white" placeholder="0" oninput="calculateTantoSectionTotal(${sectionIndex})">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Kuantitas</label>
                    <input type="number" step="0.01" min="0" name="tanto[${sectionIndex}][quantities][]" value="${quantity}" class="quantity-input-tanto w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" placeholder="0" oninput="calculateTantoSectionTotal(${sectionIndex})">
                </div>
                <div class="flex items-end pb-1 text-center justify-center">
                    <label class="flex items-center gap-1 text-xs text-gray-700 cursor-pointer">
                        <input type="hidden" name="tanto[${sectionIndex}][is_muat][]" value="0">
                        <input type="checkbox" value="1" class="is-muat-checkbox w-4 h-4 rounded text-blue-600 focus:ring-blue-500" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                        Muat
                    </label>
                </div>
                <div class="flex items-end pb-1 text-center justify-center">
                    <label class="flex items-center gap-1 text-xs text-gray-700 cursor-pointer">
                        <input type="hidden" name="tanto[${sectionIndex}][is_bongkar][]" value="0">
                        <input type="checkbox" value="1" class="is-bongkar-checkbox w-4 h-4 rounded text-blue-600 focus:ring-blue-500" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                        Bongkar
                    </label>
                </div>
            </div>
        `;
        
        typesList.appendChild(div);
    };

    function getContainerOptionsForTantoSection(sectionIndex, selectedContainer = null) {
        const section = document.querySelector(`.tanto-section[data-section-index="${sectionIndex}"]`);
        let containers = [];
        if (section && section.hasAttribute('data-containers')) {
            try {
                containers = JSON.parse(section.getAttribute('data-containers') || '[]');
            } catch (e) {
                console.error(e);
            }
        }
        
        let options = '<option value="">-- Pilih Kontainer --</option>';
        containers.forEach(c => {
            let selected = selectedContainer === c.nomor_kontainer ? 'selected' : '';
            options += `<option value="${c.nomor_kontainer}" data-bl-id="${c.id}" data-size="${c.size}" ${selected}>${c.nomor_kontainer} (${c.size})</option>`;
        });
        
        if (selectedContainer && !containers.some(c => c.nomor_kontainer === selectedContainer)) {
            options += `<option value="${selectedContainer}" selected>${selectedContainer}</option>`;
        }
        
        return options;
    }

    window.updateTantoContainerSize = function(select) {
        const container = select.closest('.tanto-type-item');
        const selectedOption = select.options[select.selectedIndex];
        const blIdInput = container.querySelector('.bl-id-input-tanto');
        const sizeSelect = container.querySelector('.size-select-tanto');
        
        if (selectedOption && selectedOption.value) {
            const blId = selectedOption.getAttribute('data-bl-id');
            const size = selectedOption.getAttribute('data-size');
            
            blIdInput.value = blId || '';
            if (size) {
                let sizeVal = size;
                if (size === '20') sizeVal = '20ft';
                if (size === '40') sizeVal = '40ft';
                sizeSelect.value = sizeVal;
            }
        } else {
            blIdInput.value = '';
        }
    };

    window.addTypeToTantoSection = function(sectionIndex, data = null) {
        const section = document.querySelector(`.tanto-section[data-section-index="${sectionIndex}"]`);
        const typesList = section.querySelector('.tanto-types-list');
        
        // Get tanto options
        let tantoOptions = '<option value="">-- Pilih Jenis Biaya Tanto --</option>';
        pricelistTantoData.forEach(item => {
            let selected = data && data.type_id == item.id ? 'selected' : '';
            const locStr = item.lokasi ? ` (${item.lokasi})` : '';
            const sizeStr = item.size ? ` - ${item.size}` : '';
            tantoOptions += `<option value="${item.id}" data-harga="${parseInt(item.harga)}" data-lokasi="${item.lokasi || ''}" data-size="${item.size || ''}" ${selected}>${item.jenis_biaya}${sizeStr}${locStr} - Rp ${parseInt(item.harga).toLocaleString('id-ID')}</option>`;
        });
        
        const isManual = data && data.type_id === 'MANUAL';
        const selectedContainer = data ? data.nomor_kontainer : null;
        const selectedBlId = data ? data.bl_id : null;
        const containerOptions = getContainerOptionsForTantoSection(sectionIndex, selectedContainer);
        
        const div = document.createElement('div');
        div.className = 'tanto-type-item flex flex-col gap-1 border p-3 rounded bg-white relative';
        div.innerHTML = `
            <div class="flex gap-2 w-full">
                <select name="tanto[${sectionIndex}][types][]" class="type-select-tanto w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 ${isManual ? 'hidden' : ''}" ${isManual ? 'disabled' : ''} required onchange="updateTantoPriceFromSelect(this, ${sectionIndex})">
                    ${tantoOptions}
                </select>
                
                <input type="hidden" name="tanto[${sectionIndex}][types][]" class="hidden-type-manual-tanto" value="MANUAL" ${isManual ? '' : 'disabled'}>
                
                <input type="text" name="tanto[${sectionIndex}][manual_names][]" class="type-manual-input-tanto w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 ${isManual ? '' : 'hidden'}" value="${isManual ? (data.manual_name || '') : ''}" placeholder="Nama Biaya Manual">
                
                <button type="button" class="type-toggle-btn-tanto px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Switch Input (Master/Manual)" onclick="toggleTantoTypeInput(this, ${sectionIndex})">
                    <i class="fas fa-keyboard"></i>
                </button>
                    
                <button type="button" class="text-red-500 hover:text-red-700 ml-1" onclick="this.closest('.tanto-type-item').remove(); calculateTantoSectionTotal(${sectionIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-2 lg:grid-cols-7 gap-2 mt-1">
                <div>
                    <label class="text-xs text-gray-500 block mb-1">No. Kontainer</label>
                    <select name="tanto[${sectionIndex}][nomor_kontainers][]" class="container-select-tanto w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" onchange="updateTantoContainerSize(this)">
                        ${containerOptions}
                    </select>
                    <input type="hidden" name="tanto[${sectionIndex}][bl_ids][]" class="bl-id-input-tanto" value="${selectedBlId || ''}">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Lokasi</label>
                    <select name="tanto[${sectionIndex}][lokasi_items][]" class="lokasi-select-tanto w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Lokasi --</option>
                        <option value="Jakarta" ${data && data.lokasi === 'Jakarta' ? 'selected' : ''}>Jakarta</option>
                        <option value="Batam" ${data && data.lokasi === 'Batam' ? 'selected' : ''}>Batam</option>
                        <option value="Pinang" ${data && data.lokasi === 'Pinang' ? 'selected' : ''}>Pinang</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Size</label>
                    <select name="tanto[${sectionIndex}][size_items][]" class="size-select-tanto w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Size --</option>
                        <option value="20ft" ${data && data.size === '20ft' ? 'selected' : ''}>20ft</option>
                        <option value="40ft" ${data && data.size === '40ft' ? 'selected' : ''}>40ft</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Harga Satuan (Rp)</label>
                    <input type="number" name="tanto[${sectionIndex}][custom_prices][]" class="price-input-tanto w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 bg-white" value="${data ? (data.harga || 0) : 0}" placeholder="0" oninput="calculateTantoSectionTotal(${sectionIndex})">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Kuantitas</label>
                    <input type="number" step="0.01" min="0" name="tanto[${sectionIndex}][quantities][]" class="quantity-input-tanto w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" value="${data ? (data.kuantitas || 0) : 0}" placeholder="0" oninput="calculateTantoSectionTotal(${sectionIndex})">
                </div>
                <div class="flex items-end pb-1 text-center justify-center">
                    <label class="flex items-center gap-1 text-xs text-gray-700 cursor-pointer">
                        <input type="hidden" name="tanto[${sectionIndex}][is_muat][]" value="0">
                        <input type="checkbox" value="1" class="is-muat-checkbox w-4 h-4 rounded text-blue-600 focus:ring-blue-500" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                        Muat
                    </label>
                </div>
                <div class="flex items-end pb-1 text-center justify-center">
                    <label class="flex items-center gap-1 text-xs text-gray-700 cursor-pointer">
                        <input type="hidden" name="tanto[${sectionIndex}][is_bongkar][]" value="0">
                        <input type="checkbox" value="1" class="is-bongkar-checkbox w-4 h-4 rounded text-blue-600 focus:ring-blue-500" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                        Bongkar
                    </label>
                </div>
            </div>
        `;
        
        typesList.appendChild(div);
    };

    window.removeTantoSection = function(sectionIndex) {
        const section = document.querySelector(`.tanto-section[data-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllTantoSections();
        }
    };
    
    function loadVoyagesForTantoSection(sectionIndex, kapalNama) {
        const section = document.querySelector(`.tanto-section[data-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.voyage-select-tanto');
        
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
    
    function calculateTantoSectionTotal(sectionIndex) {
        const section = document.querySelector(`.tanto-section[data-section-index="${sectionIndex}"]`);
        if (!section) return;
        
        const typeItems = section.querySelectorAll('.tanto-type-item');
        
        let subTotal = 0;
        typeItems.forEach(item => {
            const price = parseFloat(item.querySelector('.price-input-tanto').value) || 0;
            const qty = parseFloat(item.querySelector('.quantity-input-tanto').value) || 0;
            subTotal += (price * qty);
        });
        
        section.querySelector('.sub-total-display-tanto').value = subTotal > 0 ? `Rp ${subTotal.toLocaleString('id-ID')}` : 'Rp 0';
        section.querySelector('.sub-total-value-tanto').value = subTotal;
        
        const pphActive = section.querySelector('.pph-active-tanto').checked;
        const pphDisplay = section.querySelector('.pph-display-tanto');
        const pphValue = section.querySelector('.pph-value-tanto');
        
        let pph = 0;
        if (pphDisplay.hasAttribute('data-manual-pph')) {
            pph = parseFloat(pphValue.value) || 0;
        } else {
            pph = Math.round(subTotal * 0.02);
            pphDisplay.value = pph > 0 ? `Rp ${pph.toLocaleString('id-ID')}` : 'Rp 0';
            pphValue.value = pph;
        }
 
        // PPN Calculation
        const ppnActive = section.querySelector('.ppn-active-tanto').checked;
        const ppnDisplay = section.querySelector('.ppn-display-tanto');
        const ppnValue = section.querySelector('.ppn-value-tanto');
        
        let ppn = 0;
        if (ppnDisplay.hasAttribute('data-manual-ppn')) {
            ppn = parseFloat(ppnValue.value) || 0;
        } else {
            ppn = Math.round(subTotal * 0.11);
            ppnDisplay.value = ppn > 0 ? `Rp ${ppn.toLocaleString('id-ID')}` : 'Rp 0';
            ppnValue.value = ppn;
        }
 
        // Update Styling based on active state
        if (pphActive) {
            pphDisplay.classList.remove('bg-gray-100', 'text-gray-400');
            pphDisplay.classList.add('bg-white');
        } else {
            pphDisplay.classList.add('bg-gray-100', 'text-gray-400');
            pphDisplay.classList.remove('bg-white');
        }
 
        if (ppnActive) {
            ppnDisplay.classList.remove('bg-gray-100', 'text-gray-400');
            ppnDisplay.classList.add('bg-white');
        } else {
            ppnDisplay.classList.add('bg-gray-100', 'text-gray-400');
            ppnDisplay.classList.remove('bg-white');
        }
        
        const pphForCalculation = pphActive ? pph : 0;
        const ppnForCalculation = ppnActive ? ppn : 0;
        const materaiValue = parseFloat(section.querySelector('.materai-value-tanto').value) || 0;
        const adjustmentValue = parseFloat(section.querySelector('.adjustment-value-tanto').value) || 0;
        
        const grandTotal = subTotal + ppnForCalculation - pphForCalculation + materaiValue + adjustmentValue;
        
        section.querySelector('.grand-total-display-tanto').value = grandTotal > 0 ? `Rp ${grandTotal.toLocaleString('id-ID')}` : 'Rp 0';
        section.querySelector('.grand-total-value-tanto').value = grandTotal;
        
        calculateTotalFromAllTantoSections();
    }
    
    function calculateTotalFromAllTantoSections() {
        let grandTotalAll = 0;
        document.querySelectorAll('.tanto-section').forEach(section => {
            grandTotalAll += parseFloat(section.querySelector('.grand-total-value-tanto').value) || 0;
        });
        
        if (nominalInput) {
            nominalInput.value = grandTotalAll > 0 ? grandTotalAll.toLocaleString('id-ID') : '';
        }
    }
