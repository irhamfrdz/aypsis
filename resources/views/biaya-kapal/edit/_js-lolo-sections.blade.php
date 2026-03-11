    // ============= LOLO SECTIONS MANAGEMENT (EDIT MODE) =============
    function clearAllLoloSections() {
        if (!loloSectionsContainer) return;
        loloSectionsContainer.innerHTML = '';
        loloSectionCounter = 0;
    }
    
    if (addLoloSectionBtn) {
        addLoloSectionBtn.addEventListener('click', function() {
            addLoloSection();
        });
    }

    if (addLoloSectionBottomBtn) {
        addLoloSectionBottomBtn.addEventListener('click', function() {
            addLoloSection();
        });
    }
    
    function addLoloSection() {
        if (!loloSectionsContainer) return;
        loloSectionCounter++;
        const sectionIndex = loloSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'lolo-section mb-6 p-4 border-2 border-indigo-200 rounded-lg bg-indigo-50';
        section.setAttribute('data-lolo-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });

        const lokasiOptions = `
            <option value="">-- Pilih Lokasi --</option>
            <option value="Jakarta">Jakarta</option>
            <option value="Batam">Batam</option>
            <option value="Pinang">Pinang</option>
        `;
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex} (LOLO)</h3>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeLoloSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="lolo_sections[${sectionIndex}][kapal]" class="lolo-kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="lolo_sections[${sectionIndex}][voyage]" class="lolo-voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" required disabled>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="lolo_sections[${sectionIndex}][voyage]" class="lolo-voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="lolo-voyage-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Lokasi <span class="text-red-500">*</span></label>
                    <select name="lolo_sections[${sectionIndex}][lokasi]" class="lolo-lokasi-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" required>
                        ${lokasiOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Vendor <span class="text-red-500">*</span></label>
                    <select name="lolo_sections[${sectionIndex}][vendor]" class="lolo-vendor-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" required disabled>
                        <option value="">-- Pilih Lokasi Terlebih Dahulu --</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-4 p-4 bg-white rounded-lg border-2 border-dashed border-indigo-300">
                <label class="block text-sm font-semibold text-gray-800 mb-2">Pilih Kontainer <span class="text-red-500">*</span></label>
                <div class="lolo-kontainer-search-wrap mb-3 relative hidden">
                    <span class="absolute left-3 top-2.5 text-gray-400 text-sm"><i class="fas fa-search"></i></span>
                    <input type="text" class="lolo-kontainer-search w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" placeholder="Cari nomor kontainer...">
                </div>
                <div class="lolo-kontainer-loading hidden py-4 text-center text-gray-500 text-sm">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Memuat data kontainer...
                </div>
                <div class="lolo-kontainer-empty hidden py-4 text-center text-gray-400 text-sm">
                    <i class="fas fa-inbox text-3xl mb-2 block"></i>Tidak ada kontainer untuk voyage ini
                </div>
                <div class="lolo-kontainer-list space-y-2 max-h-60 overflow-y-auto pr-1"></div>
                <div class="lolo-kontainer-hidden-inputs"></div>
            </div>
            
            <div class="border-t pt-4 mt-2 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="lolo_sections[${sectionIndex}][subtotal]"
                                   class="lolo-subtotal-input w-full pl-10 pr-3 py-2 border border-indigo-200 rounded-lg bg-indigo-50 text-indigo-800 focus:ring-0 cursor-not-allowed"
                                   value="0" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PPH (2%)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="lolo_sections[${sectionIndex}][pph]"
                                   class="lolo-pph-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                   value="0">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PPN (11%)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="lolo_sections[${sectionIndex}][ppn]"
                                   class="lolo-ppn-input w-full pl-10 pr-3 py-2 border border-blue-200 rounded-lg bg-blue-50 text-blue-800 focus:ring-0 cursor-not-allowed"
                                   value="0" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Materai <span class="text-xs text-amber-500 font-normal">(total > Rp 5 jt)</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="lolo_sections[${sectionIndex}][biaya_materai]"
                                   class="lolo-materai-input w-full pl-10 pr-3 py-2 border border-amber-200 rounded-lg bg-amber-50 text-amber-800 focus:ring-0 cursor-not-allowed"
                                   value="0" readonly>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adjustment</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="lolo_sections[${sectionIndex}][adjustment]"
                                   class="lolo-adjustment-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                   value="0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Adjustment</label>
                        <input type="text" name="lolo_sections[${sectionIndex}][notes_adjustment]"
                               class="lolo-notes-adjustment-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                               placeholder="Contoh: Koreksi tarif, Biaya tambahan, dll">
                    </div>
                </div>
                    <div>
                        <label class="block text-lg font-bold text-gray-900 mb-1">Total Biaya (Nett)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-600 font-bold">Rp</span>
                            <input type="text" name="lolo_sections[${sectionIndex}][total_biaya]"
                                   class="lolo-total-biaya-input w-full pl-12 pr-3 py-3 border border-indigo-500 rounded-lg bg-indigo-100 text-indigo-900 font-bold text-lg focus:ring-0 cursor-not-allowed"
                                   value="0" readonly>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        loloSectionsContainer.appendChild(section);
        setupLoloSectionListeners(section, sectionIndex);
        return section;
    }
    
    window.removeLoloSection = function(index) {
        const section = document.querySelector(`.lolo-section[data-lolo-section-index="${index}"]`);
        if (section) {
             // No destroy needed for vanilla select
            section.remove();
            calculateTotalFromAllLoloSections();
        }
    };
    
    function setupLoloSectionListeners(section, index) {
        const kapalSelect = section.querySelector('.lolo-kapal-select');
        const voyageSelect = section.querySelector('.lolo-voyage-select');
        const voyageInput = section.querySelector('.lolo-voyage-input');
        const voyageManualBtn = section.querySelector('.lolo-voyage-manual-btn');
        const lokasiSelect = section.querySelector('.lolo-lokasi-select');
        const vendorSelect = section.querySelector('.lolo-vendor-select');
        const kontainerList = section.querySelector('.lolo-kontainer-list');
        const kontainerLoading = section.querySelector('.lolo-kontainer-loading');
        const kontainerEmpty = section.querySelector('.lolo-kontainer-empty');
        const searchInput = section.querySelector('.lolo-kontainer-search');
        const adjInput = section.querySelector('.lolo-adjustment-input');
        
        if (kapalSelect) {
            kapalSelect.addEventListener('change', async function() {
                const kapalName = this.value;
                voyageSelect.innerHTML = '<option value="">-- Memuat Voyage... --</option>';
                voyageSelect.disabled = true;
                kontainerList.innerHTML = '';
                
                if (!kapalName) {
                    voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
                    return;
                }
                
                try {
                    const res = await fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(kapalName)}`);
                    const data = await res.json();
                    voyageSelect.innerHTML = '<option value="">-- Pilih No. Voyage --</option>';
                    if (data.voyages && data.voyages.length > 0) {
                        data.voyages.forEach(v => {
                            voyageSelect.innerHTML += `<option value="${v}">${v}</option>`;
                        });
                        voyageSelect.disabled = false;
                    } else {
                        voyageSelect.innerHTML = '<option value="">-- Tidak ada voyage ditemukan --</option>';
                        voyageSelect.classList.add('hidden');
                        voyageInput.classList.remove('hidden');
                        voyageInput.disabled = false;
                    }
                } catch (err) {
                    console.error('Error fetching voyages:', err);
                }
            });
        }
        
        voyageManualBtn.addEventListener('click', function() {
            if (voyageSelect.classList.contains('hidden')) {
                voyageSelect.classList.remove('hidden');
                voyageInput.classList.add('hidden');
                voyageInput.disabled = true;
                if (voyageSelect.options.length > 1) voyageSelect.disabled = false;
            } else {
                voyageSelect.classList.add('hidden');
                voyageInput.classList.remove('hidden');
                voyageInput.disabled = false;
                voyageSelect.disabled = true;
            }
        });
        
        const handleVoyageChange = async function() {
            const voyage = this.value;
            const kapal = kapalSelect.value;
            kontainerList.innerHTML = '';
            if (!voyage || !kapal) return;
            
            kontainerLoading.classList.remove('hidden');
            kontainerEmpty.classList.add('hidden');
            
            try {
                const res = await fetch(`{{ url('biaya-kapal/get-containers') }}?kapal=${encodeURIComponent(kapal)}&voyage=${encodeURIComponent(voyage)}`);
                const data = await res.json();
                kontainerLoading.classList.add('hidden');
                if (data.containers && data.containers.length > 0) {
                    section.querySelector('.lolo-kontainer-search-wrap').classList.remove('hidden');
                    data.containers.forEach(c => {
                        const item = document.createElement('div');
                        item.className = 'flex items-center gap-3 p-2 hover:bg-gray-100 rounded border border-gray-100 transition';
                        const id = `lolo_c_${index}_${c.bl_id}_${c.nomor_kontainer.replace(/\s+/g, '_')}`;
                        item.innerHTML = `
                            <input type="checkbox" id="${id}" 
                                   class="lolo-kontainer-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                   data-bl-id="${c.bl_id}" data-nomor="${c.nomor_kontainer}" data-size="${c.size}">
                            <label for="${id}" class="flex-1 text-sm text-gray-700 cursor-pointer">
                                <span class="font-bold">${c.nomor_kontainer}</span> 
                                <span class="ml-2 px-2 py-0.5 bg-gray-200 rounded-full text-[10px] font-bold text-gray-600">${c.size}'</span>
                            </label>
                        `;
                        kontainerList.appendChild(item);
                        item.querySelector('input').addEventListener('change', () => calculateLoloSectionTotal(section));
                    });
                } else {
                    kontainerEmpty.classList.remove('hidden');
                }
            } catch (err) {
                console.error('Error fetching containers:', err);
            }
        };
        
        voyageSelect.addEventListener('change', handleVoyageChange);
        voyageInput.addEventListener('change', handleVoyageChange);
        
        lokasiSelect.addEventListener('change', function() {
            const lokasi = this.value;
            vendorSelect.innerHTML = '<option value="">-- Pilih Vendor --</option>';
            if (!lokasi) { vendorSelect.disabled = true; return; }
            const vendors = [...new Set(pricelistLolosData.filter(p => p.lokasi === lokasi).map(p => p.vendor))];
            if (vendors.length > 0) {
                vendors.forEach(v => { vendorSelect.innerHTML += `<option value="${v}">${v}</option>`; });
                vendorSelect.disabled = false;
            } else {
                vendorSelect.innerHTML = '<option value="">-- Tidak ada vendor --</option>';
                vendorSelect.disabled = true;
            }
            calculateLoloSectionTotal(section);
        });

        vendorSelect.addEventListener('change', () => calculateLoloSectionTotal(section));
        pphInput.addEventListener('input', function() {
            let val = this.value.replace(/\./g, '');
            this.value = parseInt(val || 0).toLocaleString('id-ID');
            calculateLoloSectionTotal(section, true);
        });

        adjInput.addEventListener('input', function() {
            let val = this.value.replace(/\./g, '');
            this.value = parseInt(val || 0).toLocaleString('id-ID');
            calculateLoloSectionTotal(section, true);
        });

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                kontainerList.querySelectorAll('label').forEach(lbl => {
                    lbl.style.display = lbl.textContent.toLowerCase().includes(term) ? 'flex' : 'none';
                });
            });
        }
    }

    function calculateLoloSectionTotal(section, skipPphAuto = false) {
        const index = section.getAttribute('data-lolo-section-index');
        const lokasi = section.querySelector('.lolo-lokasi-select').value;
        const vendor = section.querySelector('.lolo-vendor-select').value;
        const subInput = section.querySelector('.lolo-subtotal-input');
        const pphInput = section.querySelector('.lolo-pph-input');
        const ppnInput = section.querySelector('.lolo-ppn-input');
        const matInput = section.querySelector('.lolo-materai-input');
        const totInput = section.querySelector('.lolo-total-biaya-input');
        const hiddenCont = section.querySelector('.lolo-kontainer-hidden-inputs');
        
        const checked = section.querySelectorAll('.lolo-kontainer-checkbox:checked');
        let subtotal = 0;
        hiddenCont.innerHTML = '';
        
        if (lokasi && vendor) {
            checked.forEach((cb, i) => {
                const blId = cb.dataset.blId;
                const nomor = cb.dataset.nomor;
                const size = cb.dataset.size;
                let normSize = size.toString().replace(/[^0-9]/g, '');
                
                const pl = pricelistLolosData.find(p => p.lokasi === lokasi && p.vendor === vendor && p.size.toString() === normSize);
                const tarif = pl ? parseFloat(pl.tarif) : 0;
                subtotal += tarif;
                
                hiddenCont.innerHTML += `
                    <input type="hidden" name="lolo_sections[${index}][kontainer][${i}][bl_id]" value="${blId}">
                    <input type="hidden" name="lolo_sections[${index}][kontainer][${i}][nomor_kontainer]" value="${nomor}">
                    <input type="hidden" name="lolo_sections[${index}][kontainer][${i}][size]" value="${size}">
                `;
            });
        }
        
        const adjInput = section.querySelector('.lolo-adjustment-input');
        
        let ppn = Math.round(subtotal * 0.11);
        
        let pph = 0;
        const rawPphInput = pphInput.value.replace(/\./g, '');
        if (skipPphAuto && rawPphInput !== '') {
            pph = parseInt(rawPphInput) || 0;
        } else {
            pph = Math.round(subtotal * 0.02);
            pphInput.value = pph.toLocaleString('id-ID');
        }
        let adj = parseInt(adjInput.value.replace(/\./g, '') || 0);
        let mat = subtotal > 5000000 ? 10000 : 0;
        let total = subtotal + ppn + mat - pph + adj;
        
        subInput.value = subtotal.toLocaleString('id-ID');
        ppnInput.value = ppn.toLocaleString('id-ID');
        matInput.value = mat.toLocaleString('id-ID');
        totInput.value = total.toLocaleString('id-ID');
        
        calculateTotalFromAllLoloSections();
    }
    
    function calculateTotalFromAllLoloSections() {
        let grand = 0;
        document.querySelectorAll('.lolo-total-biaya-input').forEach(input => {
            grand += parseInt(input.value.replace(/\D/g, '') || 0);
        });
        
        if (selectedJenisBiaya.nama.toLowerCase().includes('lolo')) {
            nominalInput.value = grand > 0 ? grand.toLocaleString('id-ID') : '';
        }
    }

    async function initializeLoloSections() {
        const currentJenis = selectedJenisBiaya.nama || '';
        if (currentJenis.toLowerCase().includes('lolo')) {
            clearAllLoloSections();
            @if(isset($biayaKapal->loloDetails) && count($biayaKapal->loloDetails) > 0)
                @foreach($biayaKapal->loloDetails as $detail)
                    (async function() {
                        const section = addLoloSection();
                        const sIdx = section.getAttribute('data-lolo-section-index');
                        
                        const kapalSel = section.querySelector('.lolo-kapal-select');
                        if (kapalSel) {
                            kapalSel.value = "{{ $detail->kapal }}";
                            kapalSel.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                        section.querySelector('.lolo-lokasi-select').value = "{{ $detail->lokasi }}";
                        
                        // Populate vendors
                        const vSelect = section.querySelector('.lolo-vendor-select');
                        const vendors = [...new Set(pricelistLolosData.filter(p => p.lokasi === "{{ $detail->lokasi }}").map(p => p.vendor))];
                        vSelect.innerHTML = '<option value="">-- Pilih Vendor --</option>';
                        vendors.forEach(v => { vSelect.innerHTML += `<option value="${v}">${v}</option>`; });
                        vSelect.value = "{{ $detail->vendor }}";
                        vSelect.disabled = false;
                        
                        // Load voyages
                        const voySelect = section.querySelector('.lolo-voyage-select');
                        const resv = await fetch(`{{ url('biaya-kapal/get-voyages') }}/{{ urlencode($detail->kapal) }}`);
                        const datav = await resv.json();
                        voySelect.innerHTML = '<option value="">-- Pilih No. Voyage --</option>';
                        (datav.voyages || []).forEach(v => { voySelect.innerHTML += `<option value="${v}">${v}</option>`; });
                        voySelect.value = "{{ $detail->voyage }}";
                        voySelect.disabled = false;
                        
                        // Load kontainers
                        const resk = await fetch(`{{ url('biaya-kapal/get-containers') }}?kapal={{ urlencode($detail->kapal) }}&voyage={{ urlencode($detail->voyage) }}`);
                        const datak = await resk.json();
                        const list = section.querySelector('.lolo-kontainer-list');
                        const saved = {!! json_encode($detail->kontainer_ids) !!} || [];
                        
                        if (datak.containers && datak.containers.length > 0) {
                            section.querySelector('.lolo-kontainer-search-wrap').classList.remove('hidden');
                            datak.containers.forEach(c => {
                                const isChecked = saved.some(s => s.bl_id == c.bl_id);
                                const id = `lolo_edit_${sIdx}_${c.bl_id}`;
                                const row = document.createElement('div');
                                row.className = 'flex items-center gap-3 p-2 hover:bg-gray-100 rounded border border-gray-100 transition';
                                row.innerHTML = `<input type="checkbox" id="${id}" class="lolo-kontainer-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded" ${isChecked ? 'checked' : ''} data-bl-id="${c.bl_id}" data-nomor="${c.nomor_kontainer}" data-size="${c.size}">
                                    <label for="${id}" class="flex-1 text-sm text-gray-700 cursor-pointer font-bold">${c.nomor_kontainer} <span class="ml-2 px-2 py-0.5 bg-gray-200 rounded-full text-[10px] text-gray-600">${c.size}'</span></label>`;
                                list.appendChild(row);
                                row.querySelector('input').addEventListener('change', () => calculateLoloSectionTotal(section));
                            });
                        }
                        
                        section.querySelector('.lolo-pph-input').value = "{{ number_format($detail->pph, 0, ',', '.') }}";
                        calculateLoloSectionTotal(section);
                    })();
                @endforeach
            @else
                addLoloSection();
            @endif
        }
    }

