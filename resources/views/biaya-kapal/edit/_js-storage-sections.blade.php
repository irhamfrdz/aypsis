    // ============= STORAGE SECTIONS MANAGEMENT =============
    let storageSectionCounter = 0;
    
    function initializeStorageSections() {
        const currentJenis = selectedJenisBiaya.nama || '';
        if (currentJenis.toLowerCase().includes('storage')) {
            clearAllStorageSections();
            @if(isset($biayaKapal->storageDetails) && count($biayaKapal->storageDetails) > 0)
                @foreach($biayaKapal->storageDetails as $detail)
                    (async function() {
                        const section = addStorageSection();
                        const sIdx = section.getAttribute('data-storage-section-index');
                        
                        section.querySelector('.storage-kapal-select').value = "{{ $detail->kapal }}";
                        section.querySelector('.storage-lokasi-select').value = "{{ $detail->lokasi }}";
                        section.querySelector('.storage-vendor-select').value = "{{ $detail->vendor }}";
                        
                        // Load voyages
                        const voySelect = section.querySelector('.storage-voyage-select');
                        const resv = await fetch(`{{ url('biaya-kapal/get-voyages') }}/{{ urlencode($detail->kapal) }}`);
                        const datav = await resv.json();
                        voySelect.innerHTML = '<option value="">-- Pilih No. Voyage --</option>';
                        (datav.voyages || []).forEach(v => { voySelect.innerHTML += `<option value="${v}">${v}</option>`; });
                        voySelect.value = "{{ $detail->voyage }}";
                        voySelect.disabled = false;
                        
                        // Load kontainers
                        const resk = await fetch(`{{ url('biaya-kapal/get-containers-by-voyage') }}?voyage={{ urlencode($detail->voyage) }}`);
                        const datak = await resk.json();
                        
                        const list = section.querySelector('.storage-kontainer-list');
                        const hiddenInputsContainer = section.querySelector('.storage-kontainer-hidden-inputs');
                        const saved = {!! json_encode($detail->kontainer_ids) !!} || [];
                        
                        if (datak.containers && datak.containers.length > 0) {
                            section.querySelector('.storage-kontainer-search-wrap').classList.remove('hidden');
                            datak.containers.forEach(c => {
                                const savedData = saved.find(s => s.bl_id == c.id);
                                const isChecked = !!savedData;
                                const hariValue = savedData && savedData.hari ? savedData.hari : 1;

                                const row = document.createElement('label');
                                row.className = 'flex items-center gap-3 p-3 bg-gray-50 hover:bg-sky-50 rounded-lg cursor-pointer border border-gray-200 hover:border-sky-300 transition-all';
                                row.innerHTML = `
                                    <input type="checkbox"
                                           class="storage-kontainer-checkbox w-4 h-4 rounded text-sky-600 focus:ring-sky-500 cursor-pointer"
                                           ${isChecked ? 'checked' : ''}
                                           data-bl-id="${c.id}"
                                           data-nomor="${c.nomor_kontainer}"
                                           data-size="${c.size_kontainer}">
                                    <div class="flex-1">
                                        <div class="font-semibold text-sm text-gray-800">
                                            <i class="fas fa-cube text-sky-500 mr-1"></i>
                                            ${c.nomor_kontainer}
                                            <span class="ml-2 text-xs bg-sky-100 text-sky-700 px-2 py-0.5 rounded-full">${c.size_kontainer || '-'}'</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="text-[10px] text-gray-400 uppercase font-bold">Hari</label>
                                        <input type="number" 
                                               class="storage-kontainer-hari w-16 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-sky-500"
                                               value="${hariValue}" min="1"
                                               data-bl-id="${c.id}">
                                    </div>
                                `;

                                const checkbox = row.querySelector('.storage-kontainer-checkbox');
                                const hariInput = row.querySelector('.storage-kontainer-hari');

                                checkbox.addEventListener('change', function() {
                                    const blId = this.dataset.blId;
                                    const existingInput = hiddenInputsContainer.querySelector(`[data-bl-id="${blId}"]`);
                                    if (this.checked) {
                                        if (!existingInput) {
                                            const hiddenGroup = document.createElement('div');
                                            hiddenGroup.setAttribute('data-bl-id', blId);
                                            hiddenGroup.innerHTML = `
                                                <input type="hidden" name="storage_sections[${sIdx}][kontainer][${blId}][bl_id]" value="${blId}">
                                                <input type="hidden" name="storage_sections[${sIdx}][kontainer][${blId}][nomor_kontainer]" value="${this.dataset.nomor}">
                                                <input type="hidden" name="storage_sections[${sIdx}][kontainer][${blId}][size]" value="${this.dataset.size}">
                                                <input type="hidden" name="storage_sections[${sIdx}][kontainer][${blId}][hari]" class="hari-hidden" value="${hariInput.value}">`;
                                            hiddenInputsContainer.appendChild(hiddenGroup);
                                        }
                                    } else {
                                        if (existingInput) existingInput.remove();
                                    }
                                });

                                hariInput.addEventListener('input', function() {
                                    const blId = this.dataset.blId;
                                    const existingInput = hiddenInputsContainer.querySelector(`[data-bl-id="${blId}"] .hari-hidden`);
                                    if (existingInput) {
                                        existingInput.value = this.value;
                                    }
                                });

                                list.appendChild(row);

                                // Trigger change if checked to generate hidden inputs
                                if (isChecked) {
                                    checkbox.dispatchEvent(new Event('change'));
                                }
                            });
                        }
                        
                        section.querySelector('.storage-subtotal-input').value = "{{ number_format($detail->subtotal, 0, ',', '.') }}";
                        section.querySelector('.storage-materai-input').value = "{{ number_format($detail->biaya_materai, 0, ',', '.') }}";
                        section.querySelector('.storage-ppn-input').value = "{{ number_format($detail->ppn, 0, ',', '.') }}";
                        section.querySelector('.storage-pph-input').value = "{{ number_format($detail->pph, 0, ',', '.') }}";
                        section.querySelector('.storage-total-input').value = "{{ number_format($detail->total_biaya, 0, ',', '.') }}";
                        
                    })();
                @endforeach
            @else
                addStorageSection();
            @endif
        }
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
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Vendor <span class="text-red-500">*</span></label>
                    <select name="storage_sections[${sectionIndex}][vendor]" class="storage-vendor-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-sky-500" required>
                        ${vendorOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Lokasi <span class="text-red-500">*</span></label>
                    <select name="storage_sections[${sectionIndex}][lokasi]" class="storage-lokasi-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-sky-500" required>
                        ${lokasiOptions}
                    </select>
                </div>
            </div>
            
            <div class="mb-4 p-4 bg-white rounded-lg border-2 border-dashed border-sky-300">
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal (DPP) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="storage_sections[${sectionIndex}][subtotal]"
                                   class="storage-subtotal-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500"
                                   placeholder="0" required>
                        </div>
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Materai</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="storage_sections[${sectionIndex}][biaya_materai]"
                                   class="storage-materai-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500"
                                   value="0">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t pt-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PPN (11%)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="storage_sections[${sectionIndex}][ppn]"
                                   class="storage-ppn-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-0"
                                   value="0" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PPh 2%</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="storage_sections[${sectionIndex}][pph]"
                                   class="storage-pph-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-0"
                                   value="0" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Biaya</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="storage_sections[${sectionIndex}][total_biaya]"
                                   class="storage-total-input w-full pl-10 pr-3 py-2 border border-sky-300 rounded-lg bg-sky-50 text-sky-800 font-bold focus:ring-0 cursor-not-allowed"
                                   value="0" readonly>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        storageSectionsContainer.appendChild(section);
        
        // Setup kapal change listener
        const kapalSelect = section.querySelector('.storage-kapal-select');
        kapalSelect.addEventListener('change', function() {
            loadVoyagesForStorageSection(sectionIndex, this.value);
        });

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
                            <div class="flex items-center gap-2">
                                <label class="text-[10px] text-gray-400 uppercase font-bold">Hari</label>
                                <input type="number" 
                                       class="storage-kontainer-hari w-16 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-sky-500"
                                       value="1" min="1"
                                       data-bl-id="${kontainer.id}">
                            </div>
                        `;

                        const checkbox = row.querySelector('.storage-kontainer-checkbox');
                        const hariInput = row.querySelector('.storage-kontainer-hari');

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
                                        <input type="hidden" name="storage_sections[${sectionIndex}][kontainer][${blId}][hari]" class="hari-hidden" value="${hariInput.value}">`;
                                    hiddenInputsContainer.appendChild(hiddenGroup);
                                }
                            } else {
                                if (existingInput) existingInput.remove();
                            }
                        });

                        hariInput.addEventListener('input', function() {
                            const blId = this.dataset.blId;
                            const existingInput = hiddenInputsContainer.querySelector(`[data-bl-id="${blId}"] .hari-hidden`);
                            if (existingInput) {
                                existingInput.value = this.value;
                            }
                        });
                        kontainerList.appendChild(row);
                    });
                })
                .catch(e => {
                    kontainerLoading.classList.add('hidden');
                    kontainerList.innerHTML = '<div class="p-3 text-center text-red-500 text-sm"><i class="fas fa-exclamation-triangle mr-1"></i>Gagal memuat kontainer</div>';
                    console.error(e);
                });
        }

        const subtotalInput = section.querySelector('.storage-subtotal-input');
        const materaiInput  = section.querySelector('.storage-materai-input');
        const ppnInput      = section.querySelector('.storage-ppn-input');
        const pphInput      = section.querySelector('.storage-pph-input');
        const totalInput    = section.querySelector('.storage-total-input');

        function recalcStorageTotal() {
            const subtotal = parseFloat(subtotalInput.value.replace(/\./g, '')) || 0;
            const materai  = parseFloat(materaiInput.value.replace(/\./g, '')) || 0;
            
            const ppn = Math.round(subtotal * 0.11);
            const pph = Math.round(subtotal * 0.02);
            const total = subtotal + ppn + materai - pph;

            const fmt = (val) => new Intl.NumberFormat('id-ID').format(Math.round(val));
            ppnInput.value   = fmt(ppn);
            pphInput.value   = fmt(pph);
            totalInput.value = fmt(total);

            calculateTotalFromAllStorageSections();
        }

        [subtotalInput, materaiInput].forEach(el => {
            el.addEventListener('input', function() {
                let raw = this.value.replace(/[^0-9]/g, '');
                const num = parseFloat(raw) || 0;
                this.value = num > 0 ? new Intl.NumberFormat('id-ID').format(num) : '';
                recalcStorageTotal();
            });
        });

        section._loadContainers = loadContainersForStorageSection;
        return section;
    }

    window.removeStorageSection = function(sectionIndex) {
        const section = document.querySelector(`[data-storage-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllStorageSections();
        }
    };

    function loadVoyagesForStorageSection(sectionIndex, kapalNama) {
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
            const sub = parseFloat(sec.querySelector('.storage-total-input').value.replace(/\./g, '')) || 0;
            totalSubtotal += sub;
        });

        const selectedText = selectedJenisBiaya.nama || ''; // Use selectedJenisBiaya instead of jenisBiayaSelect.options
        if (selectedText.toLowerCase().includes('storage')) {
            if (nominalInput) {
                nominalInput.value = totalSubtotal > 0 ? Math.round(totalSubtotal).toLocaleString('id-ID') : '';
            }
        }
    }


    document.addEventListener('DOMContentLoaded', () => {
        initializeTHCSections();
        initializeLoloSections();
        initializeStorageSections();
    });

    function calculateTotalFromAllTruckingSections() {
        let t = 0;
        document.querySelectorAll('.trucking-section .grand-total-value').forEach(v => t += parseFloat(v.value) || 0);
        if (selectedJenisBiaya.nama.toLowerCase().includes('trucking')) {
            nominalInput.value = t > 0 ? t.toLocaleString('id-ID') : '';
        }
    }
