    // ============= THC SECTION LOGIC =============
    function addTHCSection() {
        if (!thcSectionsContainer) return;
        thcSectionCounter++;
        const sectionIndex = thcSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'thc-section mb-6 p-4 border-2 border-teal-200 rounded-lg bg-teal-50';
        section.setAttribute('data-thc-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });

        let vendorThcOptions = '<option value="">-- Pilih Vendor --</option>';
        pricelistThcVendorsData.forEach(vendor => {
            vendorThcOptions += `<option value="${vendor}">${vendor}</option>`;
        });
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex} (THC)</h3>
                ${sectionIndex > 0 ? `<button type="button" onclick="removeTHCSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="thc_sections[${sectionIndex}][kapal]" class="thc-kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-teal-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="thc_sections[${sectionIndex}][voyage]" class="thc-voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-teal-500" required disabled>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="thc_sections[${sectionIndex}][voyage]" class="thc-voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-teal-500 hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="thc-voyage-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-700 mb-1">Vendor <span class="text-red-500">*</span></label>
                <select name="thc_sections[${sectionIndex}][vendor]" class="thc-vendor-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-teal-500" required>
                    ${vendorThcOptions}
                </select>
            </div>
            
            <div class="mb-4 p-4 bg-white rounded-lg border-2 border-dashed border-teal-300">
                <label class="block text-sm font-semibold text-gray-800 mb-2">Pilih Kontainer <span class="text-red-500">*</span></label>
                <p class="text-xs text-gray-400 mb-3"><i class="fas fa-info-circle mr-1"></i>Kontainer akan muncul setelah memilih No. Voyage</p>

                <!-- Search box -->
                <div class="thc-kontainer-search-wrap hidden mb-3 relative">
                    <span class="absolute left-3 top-2.5 text-gray-400 text-sm"><i class="fas fa-search"></i></span>
                    <input type="text"
                           class="thc-kontainer-search w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                           placeholder="Cari nomor kontainer...">
                </div>

                <!-- Loading indicator -->
                <div class="thc-kontainer-loading hidden py-4 text-center text-gray-500 text-sm">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Memuat data kontainer...
                </div>

                <!-- Empty state -->
                <div class="thc-kontainer-empty hidden py-4 text-center text-gray-400 text-sm">
                    <i class="fas fa-inbox text-3xl mb-2 block"></i>Tidak ada kontainer untuk voyage ini
                </div>

                <!-- Kontainer checklist -->
                <div class="thc-kontainer-list space-y-2 max-h-60 overflow-y-auto pr-1"></div>

                <!-- Hidden inputs container -->
                <div class="thc-kontainer-hidden-inputs"></div>
            </div>
            
            <div class="border-t pt-4 mt-2 space-y-3">
                <!-- Row 1: Subtotal + Dokumen Muat + Dokumen Bongkar -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal THC <span class="text-xs text-teal-500 font-normal">(otomatis)</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="thc_sections[${sectionIndex}][subtotal]"
                                   class="thc-subtotal-input w-full pl-10 pr-3 py-2 border border-teal-200 rounded-lg bg-teal-50 text-teal-800 focus:ring-0 cursor-not-allowed"
                                   value="0" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Dokumen Muat</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="thc_sections[${sectionIndex}][biaya_dokumen_muat]"
                                   class="thc-dok-muat-input currency-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                                   value="200.000" oninput="calculateTHCSectionTotal(${sectionIndex})">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Dokumen Bongkar</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="thc_sections[${sectionIndex}][biaya_dokumen_bongkar]"
                                   class="thc-dok-bongkar-input currency-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                                   value="200.000" oninput="calculateTHCSectionTotal(${sectionIndex})">
                        </div>
                    </div>
                </div>

                <!-- Row 2: Materai (kondisional) + Total -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div class="thc-materai-wrap hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Biaya Materai
                            <span class="text-xs text-amber-500 font-normal">(total > Rp 5 jt)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="thc_sections[${sectionIndex}][biaya_materai]"
                                   class="thc-materai-input currency-input w-full pl-10 pr-3 py-2 border border-amber-200 rounded-lg bg-amber-50 text-amber-800 focus:ring-0 cursor-not-allowed"
                                   value="10.000" readonly>
                        </div>
                    </div>
                    <div class="hidden">
                        <input type="hidden" name="thc_sections[${sectionIndex}][pph]" class="thc-pph-input" value="0">
                    </div>
                    <div class="md:col-start-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Biaya</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                            <input type="text" name="thc_sections[${sectionIndex}][total_biaya]"
                                   class="thc-total-input w-full pl-10 pr-3 py-2 border border-blue-300 rounded-lg bg-blue-50 text-blue-800 font-bold focus:ring-0 cursor-not-allowed"
                                   value="0" readonly>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        thcSectionsContainer.appendChild(section);
        
        // Setup kapal change listener
        const kapalSelect = section.querySelector('.thc-kapal-select');
        kapalSelect.addEventListener('change', function() {
            loadVoyagesForTHCSection(sectionIndex, this.value);
        });

        // Setup manual voyage toggle
        const voyageSelect = section.querySelector('.thc-voyage-select');
        const voyageInput = section.querySelector('.thc-voyage-input');
        const voyageBtn = section.querySelector('.thc-voyage-manual-btn');
        
        voyageBtn.addEventListener('click', function() {
            if (voyageSelect.classList.contains('hidden')) {
                voyageSelect.classList.remove('hidden');
                voyageSelect.disabled = false;
                voyageInput.classList.add('hidden');
                voyageInput.disabled = true;
            } else {
                voyageSelect.classList.add('hidden');
                voyageSelect.disabled = true;
                voyageInput.classList.remove('hidden');
                voyageInput.disabled = false;
                voyageInput.focus();
                
                // Show kontainer search if manual
                section.querySelector('.thc-kontainer-search-wrap').classList.remove('hidden');
                loadKontainersForTHCSection(sectionIndex, 'MANUAL', kapalSelect.value);
            }
        });

        voyageSelect.addEventListener('change', function() {
            loadKontainersForTHCSection(sectionIndex, this.value, kapalSelect.value);
        });

        // Setup vendor check to recalculate
        section.querySelector('.thc-vendor-select').addEventListener('change', function() {
            recalcThcSubtotal(section, sectionIndex);
        });

        // Setup kontainer search
        const searchInput = section.querySelector('.thc-kontainer-search');
        searchInput.addEventListener('input', function() {
            filterTHCKontainers(section, this.value);
        });

        // Setup currency formatting
        section.querySelectorAll('.currency-input').forEach(input => {
            input.addEventListener('input', function() {
                let val = this.value.replace(/\D/g, '');
                this.value = val ? parseInt(val).toLocaleString('id-ID') : '';
                calculateTHCSectionTotal(sectionIndex);
            });
        });

        return section;
    }

    window.removeTHCSection = function(index) {
        const section = document.querySelector(`.thc-section[data-thc-section-index="${index}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllTHCSections();
        }
    };

    function loadVoyagesForTHCSection(sectionIndex, kapalNama) {
        const section = document.querySelector(`.thc-section[data-thc-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.thc-voyage-select');
        const voyageInput = section.querySelector('.thc-voyage-input');
        
        if (!kapalNama) {
            voyageSelect.disabled = true;
            voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
            return;
        }

        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(kapalNama)}`)
            .then(res => res.json())
            .then(data => {
                populateTHCVoyageSelect(voyageSelect, data.voyages || []);
            });
    }

    function populateTHCVoyageSelect(select, voyages) {
        select.disabled = false;
        let opt = '<option value="">-- Pilih Voyage --</option>';
        voyages.forEach(v => opt += `<option value="${v}">${v}</option>`);
        select.innerHTML = opt;
    }

    function loadKontainersForTHCSection(sectionIndex, voyage, kapal) {
        const section = document.querySelector(`.thc-section[data-thc-section-index="${sectionIndex}"]`);
        const list = section.querySelector('.thc-kontainer-list');
        const loading = section.querySelector('.thc-kontainer-loading');
        const empty = section.querySelector('.thc-kontainer-empty');
        const searchWrap = section.querySelector('.thc-kontainer-search-wrap');
        const hiddenInputsContainer = section.querySelector('.thc-kontainer-hidden-inputs');

        if (!voyage || !kapal) {
            list.innerHTML = '';
            searchWrap.classList.add('hidden');
            return;
        }

        list.innerHTML = '';
        loading.classList.remove('hidden');
        empty.classList.add('hidden');
        searchWrap.classList.add('hidden');

        fetch(`{{ url('biaya-kapal/get-bls-by-voyage-kapal') }}?voyage=${encodeURIComponent(voyage)}&kapal=${encodeURIComponent(kapal)}`)
            .then(res => res.json())
            .then(data => {
                loading.classList.add('hidden');
                if (data.success && data.bls && data.bls.length > 0) {
                    searchWrap.classList.remove('hidden');
                    data.bls.forEach(bl => {
                        const row = document.createElement('label');
                        row.className = 'flex items-center p-2 hover:bg-teal-100 rounded cursor-pointer border border-teal-100 transition';
                        row.innerHTML = `
                            <input type="checkbox" class="thc-kontainer-checkbox h-4 w-4 text-teal-600 rounded border-gray-300 focus:ring-teal-500 mr-3"
                                   data-bl-id="${bl.id}" data-nomor="${bl.nomor_kontainer}" data-size="${bl.size}">
                            <div class="flex-grow">
                                <div class="text-sm font-semibold text-gray-800">${bl.nomor_kontainer}</div>
                                <div class="text-xs text-gray-500">Voy: ${bl.nomor_voyage} | Size: ${bl.size}'</div>
                            </div>
                        `;
                        list.appendChild(row);

                        const checkbox = row.querySelector('.thc-kontainer-checkbox');
                        checkbox.addEventListener('change', function() {
                            const blId = this.dataset.blId;
                            const existingInput = hiddenInputsContainer.querySelector(`[data-bl-id="${blId}"]`);

                            if (this.checked) {
                                if (!existingInput) {
                                    const hiddenGroup = document.createElement('div');
                                    hiddenGroup.setAttribute('data-bl-id', blId);
                                    hiddenGroup.innerHTML = `<input type="hidden" name="thc_sections[${sectionIndex}][kontainer][${blId}][bl_id]" value="${blId}">
                                    <input type="hidden" name="thc_sections[${sectionIndex}][kontainer][${blId}][nomor_kontainer]" value="${this.dataset.nomor}">
                                    <input type="hidden" name="thc_sections[${sectionIndex}][kontainer][${blId}][size]" value="${this.dataset.size}">`;
                                    hiddenInputsContainer.appendChild(hiddenGroup);
                                }
                            } else {
                                if (existingInput) existingInput.remove();
                            }
                            recalcThcSubtotal(section, sectionIndex);
                        });
                    });
                } else {
                    empty.classList.remove('hidden');
                }
            });
    }

    function filterTHCKontainers(section, term) {
        const items = section.querySelectorAll('.thc-kontainer-list label');
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(term.toLowerCase()) ? 'flex' : 'none';
        });
    }

    function recalcThcSubtotal(section, sectionIndex) {
        const vendor = section.querySelector('.thc-vendor-select').value;
        const checked = section.querySelectorAll('.thc-kontainer-checkbox:checked');
        let subtotal = 0;

        checked.forEach(cb => {
            const size = cb.dataset.size;
            const price = pricelistThcData.find(p => p.vendor === vendor && p.size == size);
            if (price) {
                subtotal += parseFloat(price.total_biaya || 0);
            }
        });

        const subInput = section.querySelector('.thc-subtotal-input');
        subInput.value = subtotal.toLocaleString('id-ID');
        calculateTHCSectionTotal(sectionIndex);
    }

    function calculateTHCSectionTotal(sectionIndex) {
        const section = document.querySelector(`.thc-section[data-thc-section-index="${sectionIndex}"]`);
        const sub = parseInt(section.querySelector('.thc-subtotal-input').value.replace(/\D/g, '') || 0);
        const muat = parseInt(section.querySelector('.thc-dok-muat-input').value.replace(/\D/g, '') || 0);
        const bongkar = parseInt(section.querySelector('.thc-dok-bongkar-input').value.replace(/\D/g, '') || 0);
        
        const matWrap = section.querySelector('.thc-materai-wrap');
        const matInput = section.querySelector('.thc-materai-input');
        
        let totalNoMat = sub + muat + bongkar;
        let materai = 0;
        
        if (totalNoMat > 5000000) {
            matWrap.classList.remove('hidden');
            materai = 10000;
            matInput.value = "10.000";
        } else {
            matWrap.classList.add('hidden');
            materai = 0;
            matInput.value = "0";
        }

        const total = totalNoMat + materai;
        section.querySelector('.thc-total-input').value = total.toLocaleString('id-ID');
        calculateTotalFromAllTHCSections();
    }

    function calculateTotalFromAllTHCSections() {
        let grand = 0;
        document.querySelectorAll('.thc-total-input').forEach(input => {
            grand += parseInt(input.value.replace(/\D/g, '') || 0);
        });
        
        if (selectedJenisBiaya.nama.toLowerCase().includes('thc')) {
            nominalInput.value = grand > 0 ? grand.toLocaleString('id-ID') : '';
        }
    }

    function clearAllTHCSections() {
        thcSectionsContainer.innerHTML = '';
        thcSectionCounter = 0;
    }

    async function initializeTHCSections() {
        const currentJenis = selectedJenisBiaya.nama || '';
        if (currentJenis.toLowerCase().includes('thc')) {
            clearAllTHCSections();
            @if(isset($biayaKapal->thcDetails) && count($biayaKapal->thcDetails) > 0)
                @foreach($biayaKapal->thcDetails as $detail)
                    (async function() {
                        const section = addTHCSection();
                        const sIdx = section.getAttribute('data-thc-section-index');
                        
                        section.querySelector('.thc-kapal-select').value = "{{ $detail->kapal }}";
                        section.querySelector('.thc-vendor-select').value = "{{ $detail->vendor }}";
                        
                        // Load voyages
                        const vSelect = section.querySelector('.thc-voyage-select');
                        const resv = await fetch(`{{ url('biaya-kapal/get-voyages') }}/{{ urlencode($detail->kapal) }}`);
                        const datav = await resv.json();
                        populateTHCVoyageSelect(vSelect, datav.voyages || []);
                        vSelect.value = "{{ $detail->voyage }}";
                        
                        // Load kontainers
                        const list = section.querySelector('.thc-kontainer-list');
                        const hiddenInputsContainer = section.querySelector('.thc-kontainer-hidden-inputs');
                        const resk = await fetch(`{{ url('biaya-kapal/get-bls-by-voyage-kapal') }}?voyage={{ urlencode($detail->voyage) }}&kapal={{ urlencode($detail->kapal) }}`);
                        const datak = await resk.json();
                        
                        if (datak.success && datak.bls) {
                            section.querySelector('.thc-kontainer-search-wrap').classList.remove('hidden');
                            const savedKontainers = {!! json_encode($detail->kontainer_ids) !!} || [];
                            
                            datak.bls.forEach(bl => {
                                const isChecked = savedKontainers.some(sk => sk.bl_id == bl.id);
                                const row = document.createElement('label');
                                row.className = 'flex items-center p-2 hover:bg-teal-100 rounded cursor-pointer border border-teal-100 transition';
                                row.innerHTML = `
                                    <input type="checkbox" class="thc-kontainer-checkbox h-4 w-4 text-teal-600 rounded border-gray-300 focus:ring-teal-500 mr-3"
                                           data-bl-id="${bl.id}" data-nomor="${bl.nomor_kontainer}" data-size="${bl.size}" ${isChecked ? 'checked' : ''}>
                                    <div class="flex-grow">
                                        <div class="text-sm font-semibold text-gray-800">${bl.nomor_kontainer}</div>
                                        <div class="text-xs text-gray-500">Voy: ${bl.nomor_voyage} | Size: ${bl.size}'</div>
                                    </div>
                                `;
                                list.appendChild(row);

                                if (isChecked) {
                                    const hiddenGroup = document.createElement('div');
                                    hiddenGroup.setAttribute('data-bl-id', bl.id);
                                    hiddenGroup.innerHTML = `<input type="hidden" name="thc_sections[${sIdx}][kontainer][${bl.id}][bl_id]" value="${bl.id}">
                                    <input type="hidden" name="thc_sections[${sIdx}][kontainer][${bl.id}][nomor_kontainer]" value="${bl.nomor_kontainer}">
                                    <input type="hidden" name="thc_sections[${sIdx}][kontainer][${bl.id}][size]" value="${bl.size}">`;
                                    hiddenInputsContainer.appendChild(hiddenGroup);
                                }

                                row.querySelector('.thc-kontainer-checkbox').addEventListener('change', function() {
                                    const blId = this.dataset.blId;
                                    const existingInput = hiddenInputsContainer.querySelector(`[data-bl-id="${blId}"]`);
                                    if (this.checked) {
                                        if (!existingInput) {
                                            const hg = document.createElement('div');
                                            hg.setAttribute('data-bl-id', blId);
                                            hg.innerHTML = `<input type="hidden" name="thc_sections[${sIdx}][kontainer][${blId}][bl_id]" value="${blId}">
                                            <input type="hidden" name="thc_sections[${sIdx}][kontainer][${blId}][nomor_kontainer]" value="${this.dataset.nomor}">
                                            <input type="hidden" name="thc_sections[${sIdx}][kontainer][${blId}][size]" value="${this.dataset.size}">`;
                                            hiddenInputsContainer.appendChild(hg);
                                        }
                                    } else {
                                        if (existingInput) existingInput.remove();
                                    }
                                    recalcThcSubtotal(section, sIdx);
                                });
                            });
                        }
                        
                        section.querySelector('.thc-dok-muat-input').value = "{{ number_format($detail->biaya_dokumen_muat, 0, ',', '.') }}";
                        section.querySelector('.thc-dok-bongkar-input').value = "{{ number_format($detail->biaya_dokumen_bongkar, 0, ',', '.') }}";
                        section.querySelector('.thc-materai-input').value = "{{ number_format($detail->biaya_materai, 0, ',', '.') }}";
                        
                        recalcThcSubtotal(section, sIdx);
                    })();
                @endforeach
            @else
                addTHCSection();
            @endif
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        initializeTHCSections();
    });

    // Clear and Recalculate everything correctly
    // Clear and Recalculate everything correctly
    function clearAllLabuhTambatSections() {
        if(typeof labuhTambatSectionsContainer !== 'undefined' && labuhTambatSectionsContainer) labuhTambatSectionsContainer.innerHTML = '';
        labuhTambatSectionCounter = 0;
    }

    function clearAllTkbmSections() { document.getElementById('tkbm_sections_container').innerHTML = ''; }
    function clearAllAirSections() { document.getElementById('air_sections_container').innerHTML = ''; }
    function clearAllKapalSections() { document.getElementById('kapal_sections_container').innerHTML = ''; }
    function clearAllOperasionalSections() { document.getElementById('operasional_sections_container').innerHTML = ''; }
    function clearAllTruckingSections() { document.getElementById('trucking_sections_container').innerHTML = ''; }
    function clearAllStuffingSections() { document.getElementById('stuffing_sections_container').innerHTML = ''; }

