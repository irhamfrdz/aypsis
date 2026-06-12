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
            meratusOptions += `<option value="${item.id}" data-harga="${parseInt(item.harga)}" data-lokasi="${item.lokasi || ''}" data-size="${item.size || ''}">${item.jenis_biaya}${sizeStr}${locStr} - Rp ${parseInt(item.harga).toLocaleString('id-ID')}</option>`;
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
                
                <!-- Container Info Panel -->
                <div class="meratus-container-info-wrapper md:col-span-2 hidden">
                    <div class="meratus-container-info-content text-xs font-semibold text-blue-800 bg-blue-100/70 border border-blue-200 p-3 rounded-lg flex flex-col gap-2">
                        <div class="flex flex-wrap gap-x-4 gap-y-1 items-center">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            <span>Info Kontainer (Manifest):</span>
                            <span class="meratus-info-20">20ft: 0</span>
                            <span class="text-blue-300">|</span>
                            <span class="meratus-info-40">40ft: 0</span>
                            <span class="text-blue-300">|</span>
                            <span class="meratus-info-pelabuhan">Rute: -</span>
                        </div>
                        <div class="meratus-info-list-kontainer border-t border-blue-200/40 pt-2 mt-1">
                            <span class="font-bold text-blue-900 block mb-1.5"><i class="fas fa-boxes mr-1"></i>Nomor Kontainer:</span>
                            <div class="flex flex-wrap gap-1 meratus-badges-container">
                                <!-- Container Badges -->
                            </div>
                        </div>
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
                            
                            <div class="grid grid-cols-2 lg:grid-cols-6 gap-2 mt-1">
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
                                    <input type="number" name="meratus[${sectionIndex}][custom_prices][]" class="price-input-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 bg-white" placeholder="0" oninput="calculateMeratusSectionTotal(${sectionIndex})">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 block mb-1">Kuantitas</label>
                                    <input type="number" step="0.01" min="0" name="meratus[${sectionIndex}][quantities][]" class="quantity-input-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" placeholder="0" oninput="calculateMeratusSectionTotal(${sectionIndex})">
                                </div>
                                <div class="flex items-end pb-1">
                                    <label class="flex items-center gap-1 text-xs text-gray-700 cursor-pointer">
                                        <input type="hidden" name="meratus[${sectionIndex}][is_muat][]" value="0">
                                        <input type="checkbox" value="1" class="is-muat-checkbox w-4 h-4 rounded text-blue-600 focus:ring-blue-500" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                                        Muat
                                    </label>
                                </div>
                                <div class="flex items-end pb-1">
                                    <label class="flex items-center gap-1 text-xs text-gray-700 cursor-pointer">
                                        <input type="hidden" name="meratus[${sectionIndex}][is_bongkar][]" value="0">
                                        <input type="checkbox" value="1" class="is-bongkar-checkbox w-4 h-4 rounded text-blue-600 focus:ring-blue-500" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                                        Bongkar
                                    </label>
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
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700">PPH (2%)</label>
                        <div class="flex items-center gap-1">
                            <input type="checkbox" name="meratus[${sectionIndex}][pph_active]" class="pph-active-meratus w-4 h-4 rounded text-blue-600 focus:ring-blue-500 cursor-pointer" checked onchange="calculateMeratusSectionTotal(${sectionIndex})">
                            <span class="text-[10px] text-gray-600 font-medium cursor-pointer" onclick="this.previousElementSibling.click()">Aktifkan</span>
                        </div>
                    </div>
                    <input type="text" class="pph-display-meratus w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-colors duration-200" value="Rp 0">
                    <input type="hidden" name="meratus[${sectionIndex}][pph]" class="pph-value-meratus" value="0">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700">PPN (11%)</label>
                        <div class="flex items-center gap-1">
                            <input type="checkbox" name="meratus[${sectionIndex}][ppn_active]" class="ppn-active-meratus w-4 h-4 rounded text-blue-600 focus:ring-blue-500 cursor-pointer" onchange="calculateMeratusSectionTotal(${sectionIndex})">
                            <span class="text-[10px] text-gray-600 font-medium cursor-pointer" onclick="this.previousElementSibling.click()">Aktifkan</span>
                        </div>
                    </div>
                    <input type="text" class="ppn-display-meratus w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-colors duration-200" value="Rp 0">
                    <input type="hidden" name="meratus[${sectionIndex}][ppn]" class="ppn-value-meratus" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Materai</label>
                    <input type="text" class="materai-display-meratus w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="Rp 0">
                    <input type="hidden" name="meratus[${sectionIndex}][biaya_materai]" class="materai-value-meratus" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adjustment</label>
                    <input type="text" class="adjustment-display-meratus w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="Rp 0">
                    <input type="hidden" name="meratus[${sectionIndex}][adjustment]" class="adjustment-value-meratus" value="0">
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
        
        // Setup voyage change listener
        const voyageSelect = section.querySelector('.voyage-select-meratus');
        voyageSelect.addEventListener('change', function() {
            const kapalNama = kapalSelect.value;
            const voyageValue = this.value;
            if (kapalNama && voyageValue) {
                autoFillMeratusForSection(sectionIndex, kapalNama, voyageValue);
            }
        });
        
        // Manual voyage toggle
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

        // PPH Manual edit listener
        const pphDisplay = section.querySelector('.pph-display-meratus');
        const pphValue = section.querySelector('.pph-value-meratus');
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
            calculateMeratusSectionTotal(sectionIndex);
        });

        // PPN Manual edit listener
        const ppnDisplay = section.querySelector('.ppn-display-meratus');
        const ppnValue = section.querySelector('.ppn-value-meratus');
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
            calculateMeratusSectionTotal(sectionIndex);
        });

        // Materai Manual edit listener
        const materaiDisplay = section.querySelector('.materai-display-meratus');
        const materaiValue = section.querySelector('.materai-value-meratus');
        materaiDisplay.addEventListener('input', function() {
            let val = this.value.replace(/\D/g, '');
            if (val) {
                this.value = 'Rp ' + parseInt(val).toLocaleString('id-ID');
                materaiValue.value = val;
            } else {
                this.value = 'Rp 0';
                materaiValue.value = 0;
            }
            calculateMeratusSectionTotal(sectionIndex);
        });

        // Adjustment Manual edit listener
        const adjustmentDisplay = section.querySelector('.adjustment-display-meratus');
        const adjustmentValue = section.querySelector('.adjustment-value-meratus');
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
            calculateMeratusSectionTotal(sectionIndex);
        });
        
        calculateMeratusSectionTotal(sectionIndex);
    }
    
    //     // Auto-fill Meratus based on container counts from manifest table
    function autoFillMeratusForSection(sectionIndex, kapalNama, voyage) {
        const section = document.querySelector(`.meratus-section[data-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.meratus-types-list');
        
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
                const infoWrapper = section.querySelector('.meratus-container-info-wrapper');
                const info20 = section.querySelector('.meratus-info-20');
                const info40 = section.querySelector('.meratus-info-40');
                const infoPelabuhan = section.querySelector('.meratus-info-pelabuhan');
                const badgesContainer = section.querySelector('.meratus-badges-container');
                
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
                
                // Filter pricelistMeratusData by matching location
                const matchedItems = pricelistMeratusData.filter(item => {
                    const itemLoc = item.lokasi || '';
                    return itemLoc.toLowerCase() === location.toLowerCase();
                });
                
                matchedItems.forEach(item => {
                    if (item.size === '20ft') {
                        const sizeContainers = containersList.filter(c => (c.size || '').includes('20'));
                        sizeContainers.forEach(c => {
                            addTypeToMeratusSectionWithValue(sectionIndex, item.id, 1, item.lokasi, item.size, item.harga, c.nomor_kontainer, c.id);
                            addedAny = true;
                        });
                    } else if (item.size === '40ft') {
                        const sizeContainers = containersList.filter(c => (c.size || '').includes('40'));
                        sizeContainers.forEach(c => {
                            addTypeToMeratusSectionWithValue(sectionIndex, item.id, 1, item.lokasi, item.size, item.harga, c.nomor_kontainer, c.id);
                            addedAny = true;
                        });
                    } else {
                        // flat fee / no size
                        if (containersList.length > 0) {
                            addTypeToMeratusSectionWithValue(sectionIndex, item.id, 1, item.lokasi, item.size, item.harga, null, null);
                            addedAny = true;
                        }
                    }
                });
                
                if (!addedAny) {
                    addTypeToMeratusSection(sectionIndex);
                }
                
                calculateMeratusSectionTotal(sectionIndex);
            } else {
                container.innerHTML = '';
                addTypeToMeratusSection(sectionIndex);
            }
        })
        .catch(error => {
            console.error('Error fetching container counts:', error);
            container.innerHTML = '';
            addTypeToMeratusSection(sectionIndex);
        });
    }

    // Add Meratus type input with values
    window.addTypeToMeratusSectionWithValue = function(sectionIndex, pricelistId, quantity, lokasi, size, harga, selectedContainer = null, selectedBlId = null) {
        const section = document.querySelector(`.meratus-section[data-section-index="${sectionIndex}"]`);
        const typesList = section.querySelector('.meratus-types-list');
        
        let meratusOptions = '<option value="">-- Pilih Jenis Biaya Meratus --</option>';
        pricelistMeratusData.forEach(item => {
            const selected = item.id == pricelistId ? 'selected' : '';
            const locStr = item.lokasi ? ` (${item.lokasi})` : '';
            const sizeStr = item.size ? ` - ${item.size}` : '';
            meratusOptions += `<option value="${item.id}" data-harga="${parseInt(item.harga)}" data-lokasi="${item.lokasi || ''}" data-size="${item.size || ''}" ${selected}>${item.jenis_biaya}${sizeStr}${locStr} - Rp ${parseInt(item.harga).toLocaleString('id-ID')}</option>`;
        });
        
        const containerOptions = getContainerOptionsForSection(sectionIndex, selectedContainer);
        
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
            
            <div class="grid grid-cols-2 lg:grid-cols-7 gap-2 mt-1">
                <div>
                    <label class="text-xs text-gray-500 block mb-1">No. Kontainer</label>
                    <select name="meratus[${sectionIndex}][nomor_kontainers][]" class="container-select-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" onchange="updateMeratusContainerSize(this)">
                        ${containerOptions}
                    </select>
                    <input type="hidden" name="meratus[${sectionIndex}][bl_ids][]" class="bl-id-input-meratus" value="${selectedBlId || ''}">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Lokasi</label>
                    <select name="meratus[${sectionIndex}][lokasi_items][]" class="lokasi-select-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Lokasi --</option>
                        <option value="Jakarta" ${lokasi === 'Jakarta' ? 'selected' : ''}>Jakarta</option>
                        <option value="Batam" ${lokasi === 'Batam' ? 'selected' : ''}>Batam</option>
                        <option value="Pinang" ${lokasi === 'Pinang' ? 'selected' : ''}>Pinang</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Size</label>
                    <select name="meratus[${sectionIndex}][size_items][]" class="size-select-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Size --</option>
                        <option value="20ft" ${size === '20ft' ? 'selected' : ''}>20ft</option>
                        <option value="40ft" ${size === '40ft' ? 'selected' : ''}>40ft</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Harga Satuan (Rp)</label>
                    <input type="number" name="meratus[${sectionIndex}][custom_prices][]" value="${parseInt(harga) || 0}" class="price-input-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 bg-white" placeholder="0" oninput="calculateMeratusSectionTotal(${sectionIndex})">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Kuantitas</label>
                    <input type="number" step="0.01" min="0" name="meratus[${sectionIndex}][quantities][]" value="${quantity}" class="quantity-input-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" placeholder="0" oninput="calculateMeratusSectionTotal(${sectionIndex})">
                </div>
                <div class="flex items-end pb-1 text-center justify-center">
                    <label class="flex items-center gap-1 text-xs text-gray-700 cursor-pointer">
                        <input type="hidden" name="meratus[${sectionIndex}][is_muat][]" value="0">
                        <input type="checkbox" value="1" class="is-muat-checkbox w-4 h-4 rounded text-blue-600 focus:ring-blue-500" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                        Muat
                    </label>
                </div>
                <div class="flex items-end pb-1 text-center justify-center">
                    <label class="flex items-center gap-1 text-xs text-gray-700 cursor-pointer">
                        <input type="hidden" name="meratus[${sectionIndex}][is_bongkar][]" value="0">
                        <input type="checkbox" value="1" class="is-bongkar-checkbox w-4 h-4 rounded text-blue-600 focus:ring-blue-500" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                        Bongkar
                    </label>
                </div>
            </div>
        `;
        
        typesList.appendChild(div);
    };

    function getContainerOptionsForSection(sectionIndex, selectedContainer = null) {
        const section = document.querySelector(`.meratus-section[data-section-index="${sectionIndex}"]`);
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

    window.updateMeratusContainerSize = function(select) {
        const container = select.closest('.meratus-type-item');
        const selectedOption = select.options[select.selectedIndex];
        const blIdInput = container.querySelector('.bl-id-input-meratus');
        const sizeSelect = container.querySelector('.size-select-meratus');
        
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

    window.addTypeToMeratusSection = function(sectionIndex, data = null) {
        const section = document.querySelector(`.meratus-section[data-section-index="${sectionIndex}"]`);
        const typesList = section.querySelector('.meratus-types-list');
        
        // Get meratus options
        let meratusOptions = '<option value="">-- Pilih Jenis Biaya Meratus --</option>';
        pricelistMeratusData.forEach(item => {
            let selected = data && data.type_id == item.id ? 'selected' : '';
            const locStr = item.lokasi ? ` (${item.lokasi})` : '';
            const sizeStr = item.size ? ` - ${item.size}` : '';
            meratusOptions += `<option value="${item.id}" data-harga="${parseInt(item.harga)}" data-lokasi="${item.lokasi || ''}" data-size="${item.size || ''}" ${selected}>${item.jenis_biaya}${sizeStr}${locStr} - Rp ${parseInt(item.harga).toLocaleString('id-ID')}</option>`;
        });
        
        const isManual = data && data.type_id === 'MANUAL';
        const selectedContainer = data ? data.nomor_kontainer : null;
        const selectedBlId = data ? data.bl_id : null;
        const containerOptions = getContainerOptionsForSection(sectionIndex, selectedContainer);
        
        const div = document.createElement('div');
        div.className = 'meratus-type-item flex flex-col gap-1 border p-3 rounded bg-white relative';
        div.innerHTML = `
            <div class="flex gap-2 w-full">
                <select name="meratus[${sectionIndex}][types][]" class="type-select-meratus w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 ${isManual ? 'hidden' : ''}" ${isManual ? 'disabled' : ''} required onchange="updateMeratusPriceFromSelect(this, ${sectionIndex})">
                    ${meratusOptions}
                </select>
                
                <input type="hidden" name="meratus[${sectionIndex}][types][]" class="hidden-type-manual-meratus" value="MANUAL" ${isManual ? '' : 'disabled'}>
                
                <input type="text" name="meratus[${sectionIndex}][manual_names][]" class="type-manual-input-meratus w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 ${isManual ? '' : 'hidden'}" value="${isManual ? (data.manual_name || '') : ''}" placeholder="Nama Biaya Manual">
                
                <button type="button" class="type-toggle-btn-meratus px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Switch Input (Master/Manual)" onclick="toggleMeratusTypeInput(this, ${sectionIndex})">
                    <i class="fas fa-keyboard"></i>
                </button>
                    
                <button type="button" class="text-red-500 hover:text-red-700 ml-1" onclick="this.closest('.meratus-type-item').remove(); calculateMeratusSectionTotal(${sectionIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-2 lg:grid-cols-7 gap-2 mt-1">
                <div>
                    <label class="text-xs text-gray-500 block mb-1">No. Kontainer</label>
                    <select name="meratus[${sectionIndex}][nomor_kontainers][]" class="container-select-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" onchange="updateMeratusContainerSize(this)">
                        ${containerOptions}
                    </select>
                    <input type="hidden" name="meratus[${sectionIndex}][bl_ids][]" class="bl-id-input-meratus" value="${selectedBlId || ''}">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Lokasi</label>
                    <select name="meratus[${sectionIndex}][lokasi_items][]" class="lokasi-select-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Lokasi --</option>
                        <option value="Jakarta" ${data && data.lokasi === 'Jakarta' ? 'selected' : ''}>Jakarta</option>
                        <option value="Batam" ${data && data.lokasi === 'Batam' ? 'selected' : ''}>Batam</option>
                        <option value="Pinang" ${data && data.lokasi === 'Pinang' ? 'selected' : ''}>Pinang</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Size</label>
                    <select name="meratus[${sectionIndex}][size_items][]" class="size-select-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Size --</option>
                        <option value="20ft" ${data && data.size === '20ft' ? 'selected' : ''}>20ft</option>
                        <option value="40ft" ${data && data.size === '40ft' ? 'selected' : ''}>40ft</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Harga Satuan (Rp)</label>
                    <input type="number" name="meratus[${sectionIndex}][custom_prices][]" class="price-input-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 bg-white" value="${data ? (data.harga || 0) : 0}" placeholder="0" oninput="calculateMeratusSectionTotal(${sectionIndex})">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Kuantitas</label>
                    <input type="number" step="0.01" min="0" name="meratus[${sectionIndex}][quantities][]" class="quantity-input-meratus w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" value="${data ? (data.kuantitas || 0) : 0}" placeholder="0" oninput="calculateMeratusSectionTotal(${sectionIndex})">
                </div>
                <div class="flex items-end pb-1 text-center justify-center">
                    <label class="flex items-center gap-1 text-xs text-gray-700 cursor-pointer">
                        <input type="hidden" name="meratus[${sectionIndex}][is_muat][]" value="${data && data.is_muat ? '1' : '0'}">
                        <input type="checkbox" value="1" class="is-muat-checkbox w-4 h-4 rounded text-blue-600 focus:ring-blue-500" ${data && data.is_muat ? 'checked' : ''} onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                        Muat
                    </label>
                </div>
                <div class="flex items-end pb-1 text-center justify-center">
                    <label class="flex items-center gap-1 text-xs text-gray-700 cursor-pointer">
                        <input type="hidden" name="meratus[${sectionIndex}][is_bongkar][]" value="${data && data.is_bongkar ? '1' : '0'}">
                        <input type="checkbox" value="1" class="is-bongkar-checkbox w-4 h-4 rounded text-blue-600 focus:ring-blue-500" ${data && data.is_bongkar ? 'checked' : ''} onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                        Bongkar
                    </label>
                </div>
            </div>
        `;
        
        typesList.appendChild(div);
        
        if (isManual) {
            const toggleBtn = div.querySelector('.type-toggle-btn-meratus');
            toggleBtn.classList.add('bg-blue-200', 'text-blue-700');
            toggleBtn.innerHTML = '<i class="fas fa-list"></i>';
        }
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
        
        section.querySelector('.sub-total-display-meratus').value = subTotal > 0 ? `Rp ${subTotal.toLocaleString('id-ID')}` : 'Rp 0';
        section.querySelector('.sub-total-value-meratus').value = subTotal;
        
        const pphActive = section.querySelector('.pph-active-meratus').checked;
        const pphDisplay = section.querySelector('.pph-display-meratus');
        const pphValue = section.querySelector('.pph-value-meratus');
        
        let pph = 0;
        if (pphDisplay.hasAttribute('data-manual-pph')) {
            pph = parseFloat(pphValue.value) || 0;
        } else {
            pph = Math.round(subTotal * 0.02);
            pphDisplay.value = pph > 0 ? `Rp ${pph.toLocaleString('id-ID')}` : 'Rp 0';
            pphValue.value = pph;
        }

        // PPN Calculation
        const ppnActive = section.querySelector('.ppn-active-meratus').checked;
        const ppnDisplay = section.querySelector('.ppn-display-meratus');
        const ppnValue = section.querySelector('.ppn-value-meratus');
        
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
        const materaiValue = parseFloat(section.querySelector('.materai-value-meratus').value) || 0;
        const adjustmentValue = parseFloat(section.querySelector('.adjustment-value-meratus').value) || 0;
        
        const grandTotal = subTotal + ppnForCalculation - pphForCalculation + materaiValue + adjustmentValue;
        
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
