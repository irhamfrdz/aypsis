// ============= STORAGE SECTIONS MANAGEMENT =============
    let storageSectionCounter = 0;
    
    function initializeStorageSections() {
        if (!storageSectionsContainer) return;
        storageSectionsContainer.innerHTML = '';
        storageSectionCounter = 0;
        addStorageSection();
    }
    
    function clearAllStorageSections() {
        if (!storageSectionsContainer) return;
        storageSectionsContainer.innerHTML = '';
        storageSectionCounter = 0;
    }
    
    if (addStorageSectionBtn) {
        addStorageSectionBtn.addEventListener('click', function() {
            addStorageSection();
        });
    }

    if (addStorageSectionBottomBtn) {
        addStorageSectionBottomBtn.addEventListener('click', function() {
            addStorageSection();
        });
    }
    
    function addStorageSection() {
        if (!storageSectionsContainer) return;
        storageSectionCounter++;
        const sectionIndex = storageSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'storage-section mb-6 p-4 border-2 border-sky-200 rounded-lg bg-sky-50';
        section.setAttribute('data-storage-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });

        const uniqueVendors = [...new Set(pricelistStoragesData.map(item => item.vendor))];
        let vendorOptions = '<option value="">-- Pilih Vendor --</option>';
        uniqueVendors.forEach(vendor => {
            vendorOptions += `<option value="${vendor}">${vendor}</option>`;
        });

        const uniqueLocations = [...new Set(pricelistStoragesData.map(item => item.lokasi))];
        let lokasiOptions = '<option value="">-- Pilih Lokasi --</option>';
        uniqueLocations.forEach(lokasi => {
            lokasiOptions += `<option value="${lokasi}">${lokasi}</option>`;
        });
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex} (Storage)</h3>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeStorageSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="storage_sections[${sectionIndex}][kapal]" class="storage-kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-sky-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="storage_sections[${sectionIndex}][voyage]" class="storage-voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-sky-500" required disabled>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="storage_sections[${sectionIndex}][voyage]" class="storage-voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-sky-500 hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="storage-voyage-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
                <div class="storage-vendor-wrap">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Vendor <span class="text-red-500">*</span></label>
                    <select name="storage_sections[${sectionIndex}][vendor]" class="storage-vendor-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-sky-500" required>
                        ${vendorOptions}
                    </select>
                </div>
                <div class="storage-lokasi-wrap">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Lokasi <span class="text-red-500">*</span></label>
                    <select name="storage_sections[${sectionIndex}][lokasi]" class="storage-lokasi-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-sky-500" required>
                        ${lokasiOptions}
                    </select>
                </div>
            </div>
            
            <div class="storage-container-wrap mb-4 p-4 bg-white rounded-lg border-2 border-dashed border-sky-300">
                <label class="block text-sm font-semibold text-gray-800 mb-2">Pilih Kontainer <span class="text-red-500">*</span></label>
                <p class="text-xs text-gray-400 mb-3"><i class="fas fa-info-circle mr-1"></i>Kontainer akan muncul setelah memilih No. Voyage</p>

                <div class="storage-kontainer-search-wrap hidden mb-3 relative">
                    <span class="absolute left-3 top-2.5 text-gray-400 text-sm"><i class="fas fa-search"></i></span>
                    <input type="text"
                           class="storage-kontainer-search w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                           placeholder="Cari nomor kontainer...">
                </div>

                <div class="storage-kontainer-loading hidden py-4 text-center text-gray-500 text-sm">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Memuat data kontainer...
                </div>

                <div class="storage-kontainer-empty hidden py-4 text-center text-gray-400 text-sm">
                    <i class="fas fa-inbox text-3xl mb-2 block"></i>Tidak ada kontainer untuk voyage ini
                </div>

                <div class="storage-kontainer-list space-y-2 max-h-60 overflow-y-auto pr-1"></div>
                <div class="storage-kontainer-hidden-inputs"></div>
            </div>
            
            <div class="border-t pt-4 mt-2 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-3 bg-white rounded-lg border border-sky-200">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mode Pembayaran Storage <span class="text-red-500">*</span></label>
                        <select name="storage_sections[${sectionIndex}][payment_mode]" class="storage-payment-mode w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-sky-500">
                            <option value="lunas">Lunas</option>
                            <option value="dp">DP / Uang Muka</option>
                            <option value="pelunasan_dp">Pelunasan DP</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 storage-payment-help">Pembayaran dicatat lunas sebesar nilai tagihan.</p>
                    </div>
                    <div class="storage-dp-reference-wrap hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Referensi DP <span class="text-red-500">*</span></label>
                        <select name="storage_sections[${sectionIndex}][dp_storage_id]" class="storage-dp-reference w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-sky-500" disabled>
                            <option value="">-- Pilih DP yang akan dilunasi --</option>
                        </select>
                    </div>
                    <div class="storage-dp-paid-wrap hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Sudah Dibayar</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" class="storage-dp-paid-input w-full pl-10 pr-3 py-2 border border-emerald-200 rounded-lg bg-emerald-50 text-emerald-800 font-bold" value="0" readonly>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="storage-subtotal-wrap">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal (DPP) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="storage_sections[${sectionIndex}][subtotal]"
                                   class="storage-subtotal-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500"
                                   placeholder="0" required>
                        </div>
                    </div>
                    <div class="storage-materai-wrap">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Materai</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="storage_sections[${sectionIndex}][biaya_materai]"
                                   class="storage-materai-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-0"
                                   value="0" readonly>
                        </div>
                    </div>
                    <div class="storage-pph-wrap">
                        <label class="block text-sm font-medium text-gray-700 mb-1">PPh 2%</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="storage_sections[${sectionIndex}][pph]"
                                   class="storage-pph-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500"
                                   value="0">
                        </div>
                    </div>
                    <div class="storage-adjustment-wrap">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adjustment</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="storage_sections[${sectionIndex}][adjustment]"
                                   class="storage-adjustment-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500"
                                   placeholder="0">
                        </div>
                    </div>
                    <div class="storage-notes-adjustment-wrap md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes Adjustment</label>
                        <input type="text" name="storage_sections[${sectionIndex}][notes_adjustment]"
                               class="storage-notes-adjustment-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500"
                               placeholder="Keterangan adjustment (contoh: Diskon khusus, Koreksi tarif, dll)">
                    </div>
                    <div class="storage-total-wrap">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Biaya</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                             <input type="text" name="storage_sections[${sectionIndex}][total_biaya]"
                                   class="storage-total-input w-full pl-10 pr-3 py-2 border border-sky-300 rounded-lg bg-sky-50 text-sky-800 font-bold focus:ring-0 cursor-not-allowed"
                                   value="0" readonly>
                        </div>
                    </div>
                    <div class="storage-paid-amount-wrap hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nominal DP Dibayar <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="storage_sections[${sectionIndex}][nominal_dibayar]"
                                   class="storage-paid-amount w-full pl-10 pr-3 py-2 border border-amber-300 rounded-lg focus:ring-2 focus:ring-amber-500"
                                   placeholder="0" disabled>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        storageSectionsContainer.appendChild(section);
        
        const vendorSelect = section.querySelector('.storage-vendor-select');
        const lokasiSelect = section.querySelector('.storage-lokasi-select');

        // Kapal change listener
        const kapalSelect = section.querySelector('.storage-kapal-select');
        kapalSelect.addEventListener('change', function() {
            loadVoyagesForStorageSection(sectionIndex, this.value);
        });

        // Vendor & Lokasi change listeners to trigger recalculation
        vendorSelect.addEventListener('change', () => calculateStorageSectionSubtotal(section));
        lokasiSelect.addEventListener('change', () => calculateStorageSectionSubtotal(section));

        // Setup manual voyage toggle
        const voyageSelect = section.querySelector('.storage-voyage-select');
        const voyageInput  = section.querySelector('.storage-voyage-input');
        const voyageManualBtn = section.querySelector('.storage-voyage-manual-btn');

        voyageManualBtn.addEventListener('click', function() {
            if (voyageInput.classList.contains('hidden')) {
                voyageSelect.classList.add('hidden');
                voyageSelect.disabled = true;
                voyageInput.classList.remove('hidden');
                voyageInput.disabled = false;
                voyageInput.focus();
                this.innerHTML = '<i class="fas fa-list"></i>';
            } else {
                voyageInput.classList.add('hidden');
                voyageInput.disabled = true;
                voyageSelect.classList.remove('hidden');
                voyageSelect.disabled = false;
                this.innerHTML = '<i class="fas fa-keyboard"></i>';
            }
        });

        // --- KONTAINER MULTI-SELECT LOGIC ---
        const kontainerList         = section.querySelector('.storage-kontainer-list');
        const kontainerLoading      = section.querySelector('.storage-kontainer-loading');
        const kontainerEmpty        = section.querySelector('.storage-kontainer-empty');
        const hiddenInputsContainer = section.querySelector('.storage-kontainer-hidden-inputs');
        const kontainerSearchWrap   = section.querySelector('.storage-kontainer-search-wrap');
        const kontainerSearch       = section.querySelector('.storage-kontainer-search');

        kontainerSearch.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            kontainerList.querySelectorAll('label').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = (!q || text.includes(q)) ? '' : 'none';
            });
        });

        function loadContainersForStorageSection(voyageValue) {
            kontainerList.innerHTML = '';
            hiddenInputsContainer.innerHTML = '';
            kontainerLoading.classList.remove('hidden');
            kontainerEmpty.classList.add('hidden');
            kontainerSearchWrap.classList.add('hidden');
            kontainerSearch.value = '';

            if (!voyageValue) {
                kontainerLoading.classList.add('hidden');
                return;
            }

            fetch(`{{ url('biaya-kapal/get-containers-by-voyage') }}?voyage=${encodeURIComponent(voyageValue)}`)
                .then(res => res.json())
                .then(data => {
                    kontainerLoading.classList.add('hidden');
                    if (!data.success || !data.containers || data.containers.length === 0) {
                        kontainerEmpty.classList.remove('hidden');
                        return;
                    }
                    kontainerSearchWrap.classList.remove('hidden');
                    data.containers.forEach((kontainer, idx) => {
                        const row = document.createElement('label');
                        row.className = 'flex items-center gap-3 p-3 bg-gray-50 hover:bg-sky-50 rounded-lg cursor-pointer border border-gray-200 hover:border-sky-300 transition-all';
                        row.innerHTML = `
                            <input type="checkbox"
                                   class="storage-kontainer-checkbox w-4 h-4 rounded text-sky-600 focus:ring-sky-500 cursor-pointer"
                                   data-bl-id="${kontainer.id}"
                                   data-nomor="${kontainer.nomor_kontainer}"
                                   data-size="${kontainer.size_kontainer}">
                            <div class="flex-1">
                                <div class="font-semibold text-sm text-gray-800">
                                    <i class="fas fa-cube text-sky-500 mr-1"></i>
                                    ${kontainer.nomor_kontainer}
                                    <span class="ml-2 text-xs bg-sky-100 text-sky-700 px-2 py-0.5 rounded-full">${kontainer.size_kontainer || '-'}'</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-1">
                                    <label class="text-[10px] text-gray-400 uppercase font-bold">Nominal DP</label>
                                    <input type="text"
                                           class="storage-kontainer-nominal-dp w-24 px-2 py-1 border border-emerald-300 rounded text-sm focus:ring-1 focus:ring-emerald-500"
                                           value="0" placeholder="0">
                                </div>
                                <div class="flex items-center gap-1">
                                    <label class="text-[10px] text-gray-400 uppercase font-bold">Massa 1</label>
                                    <input type="number" 
                                           class="storage-kontainer-hari-massa-1 w-14 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-sky-500"
                                           value="0" min="0"
                                           data-bl-id="${kontainer.id}">
                                </div>
                                <div class="flex items-center gap-1">
                                    <label class="text-[10px] text-gray-400 uppercase font-bold">Massa 2</label>
                                    <input type="number" 
                                           class="storage-kontainer-hari-massa-2 w-14 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-sky-500"
                                           value="0" min="0"
                                           data-bl-id="${kontainer.id}">
                                </div>
                                <div class="flex items-center gap-1">
                                    <label class="text-[10px] text-gray-400 uppercase font-bold">DPP</label>
                                    <input type="text"
                                           class="storage-kontainer-dpp w-24 px-2 py-1 border border-sky-300 rounded text-sm focus:ring-1 focus:ring-sky-500"
                                           value="0" placeholder="0">
                                </div>
                                <div class="flex items-center gap-1">
                                    <label class="text-[10px] text-gray-400 uppercase font-bold">Sisa</label>
                                    <input type="text"
                                           class="storage-kontainer-sisa w-24 px-2 py-1 border border-amber-200 rounded text-sm bg-amber-50 text-amber-800 font-semibold"
                                           value="0" readonly>
                                </div>
                            </div>
                        `;

                        const checkbox = row.querySelector('.storage-kontainer-checkbox');
                        const nominalDpInput = row.querySelector('.storage-kontainer-nominal-dp');
                        const dppInput = row.querySelector('.storage-kontainer-dpp');
                        const sisaInput = row.querySelector('.storage-kontainer-sisa');
                        const hariMassa1Input = row.querySelector('.storage-kontainer-hari-massa-1');
                        const hariMassa2Input = row.querySelector('.storage-kontainer-hari-massa-2');

                        checkbox.addEventListener('change', function() {
                            const blId = this.dataset.blId;
                            const existingInput = hiddenInputsContainer.querySelector(`[data-bl-id="${blId}"]`);
                            if (this.checked) {
                                if (!existingInput) {
                                    const hiddenGroup = document.createElement('div');
                                    hiddenGroup.setAttribute('data-bl-id', blId);
                                    hiddenGroup.innerHTML = `
                                    <input type="hidden" name="storage_sections[${sectionIndex}][kontainer][${blId}][bl_id]" value="${blId}">
                                    <input type="hidden" name="storage_sections[${sectionIndex}][kontainer][${blId}][nomor_kontainer]" value="${this.dataset.nomor}">
                                     <input type="hidden" name="storage_sections[${sectionIndex}][kontainer][${blId}][size]" value="${this.dataset.size}">
                                     <input type="hidden" name="storage_sections[${sectionIndex}][kontainer][${blId}][nominal_dp]" class="nominal-dp-hidden" value="${nominalDpInput.value}">
                                     <input type="hidden" name="storage_sections[${sectionIndex}][kontainer][${blId}][dpp]" class="dpp-hidden" value="${dppInput.value}">
                                     <input type="hidden" name="storage_sections[${sectionIndex}][kontainer][${blId}][sisa_pembayaran]" class="sisa-hidden" value="${sisaInput.value}">
                                     <input type="hidden" name="storage_sections[${sectionIndex}][kontainer][${blId}][hari_massa_1]" class="hari-massa-1-hidden" value="${hariMassa1Input.value}">
                                    <input type="hidden" name="storage_sections[${sectionIndex}][kontainer][${blId}][hari_massa_2]" class="hari-massa-2-hidden" value="${hariMassa2Input.value}">`;
                                    hiddenInputsContainer.appendChild(hiddenGroup);
                                }
                            } else {
                                if (existingInput) existingInput.remove();
                            }
                            calculateStorageSectionSubtotal(section);
                        });

                        nominalDpInput.addEventListener('input', function() {
                            const raw = this.value.replace(/\D/g, '');
                            this.value = raw ? new Intl.NumberFormat('id-ID').format(parseInt(raw, 10)) : '0';
                            const hidden = hiddenInputsContainer.querySelector(`[data-bl-id="${checkbox.dataset.blId}"] .nominal-dp-hidden`);
                            if (hidden) hidden.value = raw || '0';
                            updateContainerBalance(row);
                        });

                        dppInput.addEventListener('input', function() {
                            const raw = this.value.replace(/\D/g, '');
                            this.value = raw ? new Intl.NumberFormat('id-ID').format(parseInt(raw, 10)) : '0';
                            const hidden = hiddenInputsContainer.querySelector(`[data-bl-id="${checkbox.dataset.blId}"] .dpp-hidden`);
                            if (hidden) hidden.value = raw || '0';
                            updateContainerBalance(row);
                        });

                        hariMassa1Input.addEventListener('input', function() {
                            const blId = this.dataset.blId;
                            const existingInput = hiddenInputsContainer.querySelector(`[data-bl-id="${blId}"] .hari-massa-1-hidden`);
                            if (existingInput) {
                                existingInput.value = this.value;
                            }
                            calculateStorageSectionSubtotal(section);
                        });

                        hariMassa2Input.addEventListener('input', function() {
                            const blId = this.dataset.blId;
                            const existingInput = hiddenInputsContainer.querySelector(`[data-bl-id="${blId}"] .hari-massa-2-hidden`);
                            if (existingInput) {
                                existingInput.value = this.value;
                            }
                            calculateStorageSectionSubtotal(section);
                        });
                        kontainerList.appendChild(row);
                        if (paymentModeInput.value === 'pelunasan_dp') {
                            checkbox.checked = true;
                            checkbox.dispatchEvent(new Event('change'));
                        }
                    });
                    if (paymentModeInput.value === 'pelunasan_dp') allocateDpAcrossContainers();
                })
                .catch(e => {
                    kontainerLoading.classList.add('hidden');
                    kontainerList.innerHTML = '<div class="p-3 text-center text-red-500 text-sm"><i class="fas fa-exclamation-triangle mr-1"></i>Gagal memuat kontainer</div>';
                    console.error(e);
                });
        }

        const subtotalInput = section.querySelector('.storage-subtotal-input');
        const materaiInput  = section.querySelector('.storage-materai-input');
        const pphInput      = section.querySelector('.storage-pph-input');
        const adjustmentInput = section.querySelector('.storage-adjustment-input');
        const notesAdjustmentInput = section.querySelector('.storage-notes-adjustment-input');
        const totalInput    = section.querySelector('.storage-total-input');
        const paymentModeInput = section.querySelector('.storage-payment-mode');
        const paymentHelp = section.querySelector('.storage-payment-help');
        const dpReferenceWrap = section.querySelector('.storage-dp-reference-wrap');
        const dpReferenceInput = section.querySelector('.storage-dp-reference');
        const paidAmountWrap = section.querySelector('.storage-paid-amount-wrap');
        const paidAmountInput = section.querySelector('.storage-paid-amount');
        const dpPaidWrap = section.querySelector('.storage-dp-paid-wrap');
        const dpPaidInput = section.querySelector('.storage-dp-paid-input');
        const detailControls = [
            section.querySelector('.storage-vendor-select'),
            section.querySelector('.storage-lokasi-select'),
            section.querySelector('.storage-container-wrap'),
            section.querySelector('.storage-materai-input'),
            section.querySelector('.storage-pph-input'),
            subtotalInput,
            adjustmentInput,
            notesAdjustmentInput,
            totalInput
        ];
        const detailWrappers = [
            section.querySelector('.storage-vendor-wrap'),
            section.querySelector('.storage-lokasi-wrap'),
            section.querySelector('.storage-container-wrap'),
            section.querySelector('.storage-materai-wrap'),
            section.querySelector('.storage-pph-wrap'),
            section.querySelector('.storage-subtotal-wrap'),
            section.querySelector('.storage-adjustment-wrap'),
            section.querySelector('.storage-notes-adjustment-wrap'),
            section.querySelector('.storage-total-wrap')
        ];

        const toNumber = (value) => parseFloat(String(value || '').replace(/\./g, '').replace(',', '.')) || 0;
        const formatCurrency = (value) => new Intl.NumberFormat('id-ID').format(Math.max(0, Math.round(value || 0)));

        function updateStoragePaymentFields() {
            const mode = paymentModeInput.value;
            const nilaiTagihan = toNumber(totalInput.value);
            const isDp = mode === 'dp';
            const isPelunasan = mode === 'pelunasan_dp';
            const compactPaymentMode = isDp || isPelunasan;
            const showPaymentContext = isDp || isPelunasan;
            const paymentModeChanged = section._storagePaymentMode !== mode;
            section._storagePaymentMode = mode;

            detailWrappers.forEach(wrapper => wrapper && wrapper.classList.toggle('hidden', compactPaymentMode));
            const vendorWrap = section.querySelector('.storage-vendor-wrap');
            const lokasiWrap = section.querySelector('.storage-lokasi-wrap');
            if (vendorWrap) vendorWrap.classList.toggle('hidden', !showPaymentContext);
            if (lokasiWrap) lokasiWrap.classList.toggle('hidden', !showPaymentContext);
            vendorSelect.disabled = !showPaymentContext;
            lokasiSelect.disabled = !showPaymentContext;
            const containerWrap = section.querySelector('.storage-container-wrap');
            if (containerWrap) containerWrap.classList.toggle('hidden', !isPelunasan);
            // The final invoice amount is entered only when settling a DP.
            const totalWrap = section.querySelector('.storage-total-wrap');
            if (totalWrap) totalWrap.classList.toggle('hidden', !isPelunasan);
            totalInput.readOnly = true;
            totalInput.disabled = false;
            totalInput.classList.toggle('cursor-not-allowed', !isPelunasan);
            totalInput.classList.toggle('bg-sky-50', !isPelunasan);
            totalInput.classList.toggle('bg-white', isPelunasan);
            detailControls.forEach(control => {
                if (!control) return;
                if (control.matches('select, input')) control.disabled = compactPaymentMode;
            });
            if (showPaymentContext) {
                vendorSelect.disabled = false;
                lokasiSelect.disabled = false;
            }
            if (isDp) {
                materaiInput.value = '0';
                pphInput.value = '0';
                const hiddenContainerInputs = section.querySelector('.storage-kontainer-hidden-inputs');
                if (hiddenContainerInputs) hiddenContainerInputs.innerHTML = '';
                section.querySelectorAll('.storage-kontainer-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
            }
            if (isPelunasan && section._loadContainers && (paymentModeChanged || !kontainerList.children.length)) {
                const voyageValue = voyageSelect.value || voyageInput.value.trim();
                if (voyageValue) section._loadContainers(voyageValue);
            }

            paidAmountWrap.classList.toggle('hidden', !isDp);
            dpReferenceWrap.classList.toggle('hidden', !isPelunasan);
            dpPaidWrap.classList.toggle('hidden', !isPelunasan);
            if (!isPelunasan) dpPaidInput.value = '0';
            paidAmountInput.disabled = !isDp;
            dpReferenceInput.disabled = !isPelunasan;

            if (isDp) {
                paymentHelp.textContent = 'Masukkan nominal DP yang sudah dibayar. Nilai tagihan ditentukan saat pelunasan.';
            } else if (isPelunasan) {
                paymentHelp.textContent = 'Pilih DP, lalu masukkan nilai tagihan akhir untuk menghitung pelunasan.';
            } else {
                paymentHelp.textContent = 'Pembayaran dicatat lunas sebesar nilai tagihan.';
            }
        }

        function allocateDpAcrossContainers() {
            if (paymentModeInput.value !== 'pelunasan_dp') return;
            const rows = Array.from(kontainerList.querySelectorAll('.storage-kontainer-checkbox'));
            if (!rows.length) return;
            const totalDp = toNumber(dpPaidInput.value);
            const base = Math.floor(totalDp / rows.length);
            let remainder = Math.round(totalDp - (base * rows.length));
            rows.forEach(checkbox => {
                const row = checkbox.closest('label');
                const input = row.querySelector('.storage-kontainer-nominal-dp');
                const hidden = hiddenInputsContainer.querySelector(`[data-bl-id="${checkbox.dataset.blId}"] .nominal-dp-hidden`);
                const amount = base + (remainder-- > 0 ? 1 : 0);
                input.value = formatCurrency(amount);
                if (hidden) hidden.value = amount;
                updateContainerBalance(row);
            });
        }

        function updateContainerBalance(row) {
            const dpp = toNumber(row.querySelector('.storage-kontainer-dpp')?.value);
            const dp = toNumber(row.querySelector('.storage-kontainer-nominal-dp')?.value);
            const sisa = Math.max(0, dpp - dp);
            const sisaInput = row.querySelector('.storage-kontainer-sisa');
            if (sisaInput) sisaInput.value = formatCurrency(sisa);
            const checkbox = row.querySelector('.storage-kontainer-checkbox');
            const hidden = hiddenInputsContainer.querySelector(`[data-bl-id="${checkbox?.dataset.blId}"] .sisa-hidden`);
            if (hidden) hidden.value = sisa;
            updateSettlementTotalsFromContainers();
        }

        function updateSettlementTotalsFromContainers() {
            if (paymentModeInput.value !== 'pelunasan_dp') return;
            let totalSisa = 0;
            kontainerList.querySelectorAll('label').forEach(row => {
                totalSisa += toNumber(row.querySelector('.storage-kontainer-sisa')?.value);
            });
            totalInput.value = formatCurrency(totalSisa);
            calculateTotalFromAllStorageSections();
        }

        function loadOutstandingStorageDps() {
            dpReferenceInput.innerHTML = '<option value="">Memuat DP...</option>';
            fetch(`{{ url('biaya-kapal/storage-dp-candidates') }}`)
                .then(res => res.json())
                .then(data => {
                    dpReferenceInput.innerHTML = '<option value="">-- Pilih DP yang akan dilunasi --</option>';
                    (data.data || []).forEach(dp => {
                        dpReferenceInput.innerHTML += `<option value="${dp.id}" data-sisa="${dp.sisa_pembayaran}" data-dp="${dp.nominal_dibayar}" data-kapal="${dp.kapal || ''}" data-voyage="${dp.voyage || ''}" data-vendor="${dp.vendor || ''}">${dp.label}</option>`;
                    });
                })
                .catch(() => {
                    dpReferenceInput.innerHTML = '<option value="">Gagal memuat daftar DP</option>';
                });
        }

        paymentModeInput.addEventListener('change', function() {
            updateStoragePaymentFields();
            if (this.value === 'pelunasan_dp') loadOutstandingStorageDps();
            calculateTotalFromAllStorageSections();
        });
        dpReferenceInput.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const total = toNumber(totalInput.value);
            const dpPaid = option ? toNumber(option.dataset.dp) : 0;
            dpPaidInput.value = formatCurrency(dpPaid);
            if (!option || !option.value) return;

            const dpKapal = option.dataset.kapal || '';
            const dpVoyage = option.dataset.voyage || '';
            const dpVendor = option.dataset.vendor || '';
            if (dpVendor) vendorSelect.value = dpVendor;
            if (dpKapal) {
                kapalSelect.value = dpKapal;
                loadVoyagesForStorageSection(sectionIndex, dpKapal, dpVoyage);
            }
        });
        paidAmountInput.addEventListener('input', function() {
            this.value = formatCurrency(toNumber(this.value));
            updateStoragePaymentFields();
            calculateTotalFromAllStorageSections();
        });
        totalInput.addEventListener('input', function() {
            if (paymentModeInput.value !== 'pelunasan_dp') return;
            this.value = formatCurrency(toNumber(this.value));
            const option = dpReferenceInput.options[dpReferenceInput.selectedIndex];
            const dpPaid = option ? toNumber(option.dataset.dp) : 0;
            calculateTotalFromAllStorageSections();
        });

        function calculateStorageSectionSubtotal(sec) {
            const vendor = sec.querySelector('.storage-vendor-select').value;
            const lokasi = sec.querySelector('.storage-lokasi-select').value;
            const subsInput = sec.querySelector('.storage-subtotal-input');
            const checkedCbs = sec.querySelectorAll('.storage-kontainer-checkbox:checked');
            
            let calculatedSubtotal = 0;
            
            if (vendor && lokasi) {
                checkedCbs.forEach(cb => {
                    const blId = cb.dataset.blId;
                    const size = cb.dataset.size;
                    const hariMassa1 = parseInt(sec.querySelector(`.storage-kontainer-hari-massa-1[data-bl-id="${blId}"]`).value) || 0;
                    const hariMassa2 = parseInt(sec.querySelector(`.storage-kontainer-hari-massa-2[data-bl-id="${blId}"]`).value) || 0;
                    
                    // Normalize size (20, 40, 45)
                    let normSize = size || '20';
                    normSize = normSize.toString().replace(/[^0-9]/g, '');
                    if (normSize === '2') normSize = '20';
                    if (normSize === '4') normSize = '40';

                    const pricelist = pricelistStoragesData.find(p => 
                        p.lokasi === lokasi && 
                        p.vendor === vendor && 
                        (p.size_kontainer ? p.size_kontainer.toString() : '') === normSize
                    );
                    
                    const tarif1 = pricelist ? parseFloat(pricelist.tarif_massa_1) : 0;
                    const tarif2 = pricelist ? parseFloat(pricelist.tarif_massa_2 || 0) : 0;
                    const containerDpp = (tarif1 * hariMassa1) + (tarif2 * hariMassa2);
                    calculatedSubtotal += containerDpp;

                    const row = cb.closest('label');
                    const dppInput = row?.querySelector('.storage-kontainer-dpp');
                    const dppHidden = hiddenInputsContainer.querySelector(`[data-bl-id="${blId}"] .dpp-hidden`);
                    if (dppInput) dppInput.value = new Intl.NumberFormat('id-ID').format(Math.round(containerDpp));
                    if (dppHidden) dppHidden.value = Math.round(containerDpp);
                    if (row) updateContainerBalance(row);
                });
            }
            
            subsInput.value = calculatedSubtotal > 0 ? new Intl.NumberFormat('id-ID').format(calculatedSubtotal) : '0';
            if (sec.querySelector('.storage-payment-mode')?.value === 'pelunasan_dp') {
                updateStoragePaymentFields();
                calculateTotalFromAllStorageSections();
            } else {
                recalcStorageTotal(true);
            }
        }

        function recalcStorageTotal(updatePph = false) {
            const subtotal = parseFloat(subtotalInput.value.replace(/\./g, '')) || 0;
            const adjustment = parseFloat(adjustmentInput.value.replace(/\./g, '')) || 0;
            
            // Logic: if subtotal >= 5,000,000 then materai = 10,000
            const materai = subtotal >= 5000000 ? 10000 : 0;
            
            const fmt = (val) => new Intl.NumberFormat('id-ID').format(Math.round(val));
            
            if (updatePph) {
                const pph = Math.round(subtotal * 0.02);
                if (pphInput) pphInput.value = fmt(pph);
            }
            
            const pph = parseFloat(pphInput.value.replace(/\./g, '')) || 0;
            const total = paymentModeInput && ['dp', 'pelunasan_dp'].includes(paymentModeInput.value)
                ? subtotal + adjustment
                : subtotal + materai - pph + adjustment;

            if (materaiInput) materaiInput.value = fmt(materai);
            if (totalInput) totalInput.value = fmt(total);

            updateStoragePaymentFields();
            calculateTotalFromAllStorageSections();
        }

        [subtotalInput, pphInput, adjustmentInput].forEach(el => {
            if (el) {
                el.addEventListener('input', function() {
                    let isNegative = this.value.startsWith('-');
                    let raw = this.value.replace(/[^0-9]/g, '');
                    const num = parseFloat(raw) || 0;
                    let formatted = num > 0 ? new Intl.NumberFormat('id-ID').format(num) : (num === 0 && this.value !== '' ? '0' : '');
                    this.value = (isNegative && num > 0) ? '-' + formatted : formatted;

                    // If subtotal is changed manually, update PPh automatically
                    const autoUpdatePph = (this === subtotalInput);
                    recalcStorageTotal(autoUpdatePph);
                });
            }
        });

        section._loadContainers = loadContainersForStorageSection;
        updateStoragePaymentFields();
    }

    window.removeStorageSection = function(sectionIndex) {
        const section = document.querySelector(`[data-storage-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllStorageSections();
        }
    };

    function loadVoyagesForStorageSection(sectionIndex, kapalNama, preferredVoyage = '') {
        const section = document.querySelector(`[data-storage-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.storage-voyage-select');
        const voyageInput  = section.querySelector('.storage-voyage-input');
        
        if (!kapalNama) {
            voyageSelect.disabled = true;
            voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
            return;
        }
        
        voyageSelect.disabled = true;
        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        
        fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(kapalNama)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.voyages) {
                    let html = '<option value="">-- Pilih Voyage --</option>';
                    data.voyages.forEach(voy => html += `<option value="${voy}">${voy}</option>`);
                    voyageSelect.innerHTML = html;
                    voyageSelect.disabled = false;
                    voyageSelect.onchange = function() {
                        if (section._loadContainers) section._loadContainers(this.value);
                    };
                    if (preferredVoyage && data.voyages.includes(preferredVoyage)) {
                        voyageSelect.value = preferredVoyage;
                        if (section._loadContainers) section._loadContainers(preferredVoyage);
                    }
                } else {
                    voyageSelect.innerHTML = '<option value="">Tidak ada voyage tersedia</option>';
                }
            })
            .catch(err => {
                console.error('Error fetching voyages:', err);
                voyageSelect.innerHTML = '<option value="">Gagal memuat voyages</option>';
            });

        voyageInput.oninput = function() {
            if (section._loadContainers) {
                clearTimeout(voyageInput._storageDebounce);
                voyageInput._storageDebounce = setTimeout(() => {
                    section._loadContainers(this.value.trim());
                }, 500);
            }
        };
    }

    function calculateTotalFromAllStorageSections() {
        let totalSubtotal = 0;
        document.querySelectorAll('.storage-section').forEach(sec => {
            const mode = sec.querySelector('.storage-payment-mode')?.value;
            let sub = 0;
            if (mode === 'dp') {
                sub = parseFloat((sec.querySelector('.storage-paid-amount')?.value || '').replace(/\./g, '')) || 0;
            } else if (mode === 'pelunasan_dp') {
                const total = parseFloat((sec.querySelector('.storage-total-input')?.value || '').replace(/\./g, '')) || 0;
                const dp = parseFloat((sec.querySelector('.storage-dp-paid-input')?.value || '').replace(/\./g, '')) || 0;
                sub = Math.max(0, total - dp);
            } else {
                sub = parseFloat((sec.querySelector('.storage-total-input')?.value || '').replace(/\./g, '')) || 0;
            }
            totalSubtotal += sub;
        });

        const selectedText = selectedJenisBiaya.nama || '';
        if (selectedText.toLowerCase().includes('storage')) {
            if (nominalInput) {
                nominalInput.value = totalSubtotal > 0 ? Math.round(totalSubtotal).toLocaleString('id-ID') : '';
            }
        }
    }
