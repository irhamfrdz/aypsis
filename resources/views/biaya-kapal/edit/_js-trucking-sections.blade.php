    // ============= TRUCKING SECTIONS MANAGEMENT =============
    let truckingSectionCounter = 0;
    const truckingSectionsContainer = document.getElementById('trucking_sections_container');
    const addTruckingSectionBtn = document.getElementById('add_trucking_section_btn');

    function clearAllTruckingSections() {
        if (!truckingSectionsContainer) return;
        truckingSectionsContainer.innerHTML = '';
        truckingSectionCounter = 0;
    }

    if (addTruckingSectionBtn) {
        addTruckingSectionBtn.addEventListener('click', function() {
            addTruckingSection();
        });
    }

    window.addTruckingSection = function() {
        if (!truckingSectionsContainer) return;
        truckingSectionCounter++;
        const sectionIndex = truckingSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'trucking-section mb-6 p-4 border-2 border-blue-200 rounded-lg bg-blue-50';
        section.setAttribute('data-trucking-section-index', sectionIndex);
        
        let kapalOptionsHtml = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptionsHtml += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });
        
        let vendorOptionsHtml = '<option value="">-- Pilih Vendor Trucking --</option>';
        const uniqueVendors = [...new Set(pricelistBiayaTruckingData.map(item => item.nama_vendor))];
        uniqueVendors.forEach(vendor => {
            vendorOptionsHtml += `<option value="${vendor}">${vendor}</option>`;
        });
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-md font-semibold text-blue-800">
                    <i class="fas fa-truck mr-2"></i>Kapal ${sectionIndex} (Trucking)
                </h4>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeTruckingSection(${sectionIndex})" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs rounded transition"><i class="fas fa-times mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="trucking_sections[${sectionIndex}][kapal]" class="trucking-kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        ${kapalOptionsHtml}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Voyage <span class="text-red-500">*</span></label>
                    <select name="trucking_sections[${sectionIndex}][voyage]" class="trucking-voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" disabled required>
                        <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vendor Trucking <span class="text-red-500">*</span></label>
                    <select name="trucking_sections[${sectionIndex}][nama_vendor]" class="trucking-vendor-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        ${vendorOptionsHtml}
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kontainer <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="trucking-bl-container min-h-[42px] px-3 py-2 border border-gray-300 rounded-lg bg-white cursor-pointer focus-within:ring-2 focus-within:ring-blue-500" 
                         onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <div class="trucking-selected-bl-chips flex flex-wrap gap-1 mb-1"></div>
                        <span class="text-gray-400 text-sm italic placeholder-text">-- Klik untuk memilih kontainer --</span>
                    </div>
                    
                    <div class="trucking-bl-dropdown absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto hidden">
                        <p class="px-3 py-2 text-sm text-gray-500 italic">Pilih voyage terlebih dahulu</p>
                    </div>
                    <div class="trucking-hidden-bl-inputs"></div>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <div class="text-xs text-blue-600 font-medium trucking-bl-count">Terpilih: 0 kontainer</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t pt-4 mt-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal Biaya</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                        <input type="text" name="trucking_sections[${sectionIndex}][subtotal]" 
                               class="trucking-subtotal-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-0" 
                               value="0">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PPh 2%</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                        <input type="text" name="trucking_sections[${sectionIndex}][pph]" 
                               class="trucking-pph-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-0" 
                               value="0" readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Biaya</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                        <input type="text" name="trucking_sections[${sectionIndex}][total_biaya]" 
                               class="trucking-total-input w-full pl-10 pr-3 py-2 border border-blue-300 rounded-lg bg-blue-50 text-blue-800 font-bold focus:ring-0" 
                               value="0" readonly>
                    </div>
                </div>
            </div>
        `;
        
        truckingSectionsContainer.appendChild(section);

        // Events
        const kapalSelect = section.querySelector('.trucking-kapal-select');
        const voyageSelect = section.querySelector('.trucking-voyage-select');
        const blDropdown = section.querySelector('.trucking-bl-dropdown');
        const vendorSelect = section.querySelector('.trucking-vendor-select');
        
        if (kapalSelect) {
            kapalSelect.addEventListener('change', function() {
                loadVoyagesForTruckingSection(sectionIndex, this.value);
            });
        }
        
        voyageSelect.addEventListener('change', function() {
            loadBlsForTruckingSection(sectionIndex, this.value);
        });

        vendorSelect.addEventListener('change', function() {
            calculateTruckingTotals(sectionIndex);
        });

        const subtotalInput = section.querySelector('.trucking-subtotal-input');
        subtotalInput.addEventListener('input', function(e) {
            let rawValue = this.value.replace(/[^0-9]/g, '');
            const numericValue = parseFloat(rawValue) || 0;
            if (rawValue) {
                this.value = new Intl.NumberFormat('id-ID').format(numericValue);
            } else {
                 this.value = '';
            }
            const pph = Math.round(numericValue * 0.02);
            const total = numericValue - pph;
            section.querySelector('.trucking-pph-input').value = new Intl.NumberFormat('id-ID').format(pph);
            section.querySelector('.trucking-total-input').value = new Intl.NumberFormat('id-ID').format(total);
            calculateTotalFromAllTruckingSections();
        });

        document.addEventListener('click', function(e) {
            if (!section.contains(e.target)) {
                blDropdown.classList.add('hidden');
            }
        });

        return section;
    }


    window.removeTruckingSection = function(index) {
        const section = document.querySelector(`.trucking-section[data-trucking-section-index="${index}"]`);
        if (section) {
             // No destroy needed for vanilla select
            section.remove();
            calculateTotalFromAllTruckingSections();
        }
    }

    function loadVoyagesForTruckingSection(sectionIndex, kapalNama) {
        const section = document.querySelector(`.trucking-section[data-trucking-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.trucking-voyage-select');
        const blDropdown = section.querySelector('.trucking-bl-dropdown');
        
        if (!kapalNama) {
            voyageSelect.disabled = true;
            voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
            blDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Pilih voyage terlebih dahulu</p>';
            return;
        }
        
        voyageSelect.disabled = true;
        voyageSelect.innerHTML = '<option value="">Memuat...</option>';
        
        fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(kapalNama)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.voyages) {
                    let html = '<option value="">-- Pilih Voyage --</option>';
                    data.voyages.forEach(voyage => {
                        html += `<option value="${voyage}">${voyage}</option>`;
                    });
                    voyageSelect.innerHTML = html;
                    voyageSelect.disabled = false;
                } else {
                    voyageSelect.innerHTML = '<option value="">Tidak ada voyage</option>';
                }
            });
    }

    function loadBlsForTruckingSection(sectionIndex, voyage) {
        const section = document.querySelector(`.trucking-section[data-trucking-section-index="${sectionIndex}"]`);
        const blDropdown = section.querySelector('.trucking-bl-dropdown');
        const hiddenInputsContainer = section.querySelector('.trucking-hidden-bl-inputs');
        const chipsContainer = section.querySelector('.trucking-selected-bl-chips');
        const countDisplay = section.querySelector('.trucking-bl-count');
        const placeholder = section.querySelector('.placeholder-text');

        hiddenInputsContainer.innerHTML = '';
        chipsContainer.innerHTML = '';
        countDisplay.textContent = 'Terpilih: 0 kontainer';
        placeholder.classList.remove('hidden');

        if (!voyage) {
            blDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Pilih voyage terlebih dahulu</p>';
            return;
        }

        blDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Memuat kontainer...</p>';

        fetch("{{ url('biaya-kapal/get-bls-by-voyages') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                voyages: [voyage],
                source: 'trucking'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.bls && Object.keys(data.bls).length > 0) {
                let html = `
                    <div class="sticky top-0 bg-white border-b border-gray-200 p-2 z-10">
                        <div class="relative">
                            <input type="text" class="trucking-kontainer-search w-full px-3 py-2 pl-9 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="Cari nomor kontainer atau seal..." autocomplete="off">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                        </div>
                    </div>
                    <div class="trucking-bl-options-list">
                `;
                
                Object.keys(data.bls).forEach(id => {
                    const blData = data.bls[id];
                    html += `
                        <div class="trucking-bl-option px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0"
                             data-id="${id}" data-kontainer="${blData.kontainer}" data-seal="${blData.seal}" data-size="${blData.size}">
                            <div class="font-medium text-gray-900">${blData.kontainer} <span class="bg-gray-100 text-gray-600 text-[10px] px-1.5 py-0.5 rounded ml-1">${blData.size}'</span></div>
                            <div class="text-xs text-gray-500">Seal: ${blData.seal}</div>
                        </div>
                    `;
                });
                html += '</div>';
                blDropdown.innerHTML = html;

                const searchInput = blDropdown.querySelector('.trucking-kontainer-search');
                const optionsList = blDropdown.querySelector('.trucking-bl-options-list');
                const allOptions = optionsList.querySelectorAll('.trucking-bl-option');
                
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase().trim();
                    allOptions.forEach(option => {
                        const kontainer = option.getAttribute('data-kontainer').toLowerCase();
                        const seal = option.getAttribute('data-seal').toLowerCase();
                        if (kontainer.includes(searchTerm) || seal.includes(searchTerm)) {
                            option.style.display = 'block';
                        } else {
                            option.style.display = 'none';
                        }
                    });
                });
                
                searchInput.addEventListener('click', function(e) { e.stopPropagation(); });

                blDropdown.querySelectorAll('.trucking-bl-option').forEach(opt => {
                    opt.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const id = this.getAttribute('data-id');
                        const kontainer = this.getAttribute('data-kontainer');

                        if (this.classList.contains('selected')) {
                            this.classList.remove('selected');
                            section.querySelector(`.trucking-chip[data-id="${id}"]`).remove();
                            section.querySelector(`input[value="${id}"]`).remove();
                        } else {
                            this.classList.add('selected');
                            placeholder.classList.add('hidden');
                            
                            const chip = document.createElement('span');
                            chip.className = 'trucking-chip bg-blue-600 text-white text-[10px] px-2 py-0.5 rounded flex items-center gap-1';
                            chip.setAttribute('data-id', id);
                            chip.innerHTML = `${kontainer} <i class="fas fa-times cursor-pointer hover:text-red-200"></i>`;
                            chip.querySelector('i').onclick = (e) => { e.stopPropagation(); opt.click(); };
                            chipsContainer.appendChild(chip);

                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = `trucking_sections[${sectionIndex}][no_bl][]`;
                            input.value = id;
                            hiddenInputsContainer.appendChild(input);
                        }

                        updateTruckingCount(sectionIndex);
                        calculateTruckingTotals(sectionIndex);
                    });
                });
            } else {
                blDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Tidak ada kontainer</p>';
                calculateTruckingTotals(sectionIndex);
            }
        });
    }

    function updateTruckingCount(sectionIndex) {
        const section = document.querySelector(`.trucking-section[data-trucking-section-index="${sectionIndex}"]`);
        const chipsContainer = section.querySelector('.trucking-selected-bl-chips');
        const countDisplay = section.querySelector('.trucking-bl-count');
        const placeholder = section.querySelector('.placeholder-text');
        const count = chipsContainer.children.length;
        countDisplay.textContent = `Terpilih: ${count} kontainer`;
        if (count === 0) placeholder.classList.remove('hidden');
    }

    function calculateTruckingTotals(sectionIndex) {
        const section = document.querySelector(`.trucking-section[data-trucking-section-index="${sectionIndex}"]`);
        if (!section) return;

        const vendor = section.querySelector('.trucking-vendor-select').value;
        const selectedOptions = section.querySelectorAll('.trucking-bl-option.selected');
        const subtotalInput = section.querySelector('.trucking-subtotal-input');
        const pphInput = section.querySelector('.trucking-pph-input');
        const totalInput = section.querySelector('.trucking-total-input');

        let subtotal = 0;
        if (vendor && selectedOptions.length > 0) {
            const vendorPrices = pricelistBiayaTruckingData.filter(item => item.nama_vendor === vendor);
            selectedOptions.forEach(opt => {
                const size = String(opt.getAttribute('data-size')).replace(/\D/g, '');
                const priceItem = vendorPrices.find(item => String(item.size).replace(/\D/g, '') === size);
                if (priceItem) {
                    subtotal += parseFloat(priceItem.biaya) || 0;
                }
            });
        } else if (selectedOptions.length === 0) {
            // If nothing selected, maybe keep the manual value if edited?
            // For now, let's reset only if it's auto-mode
            // subtotal = 0;
        }

        const pph = Math.round(subtotal * 0.02);
        const total = subtotal - pph; 
        
        const formatRupiah = (val) => new Intl.NumberFormat('id-ID').format(Math.round(val));

        if (subtotal > 0) {
            subtotalInput.value = formatRupiah(subtotal);
            pphInput.value = formatRupiah(pph);
            totalInput.value = formatRupiah(total);
        }

        calculateTotalFromAllTruckingSections();
    }

    function calculateTotalFromAllTruckingSections() {
        let totalSubtotal = 0;
        document.querySelectorAll('.trucking-section').forEach(section => {
            const sub = parseFloat(section.querySelector('.trucking-subtotal-input').value.replace(/\./g, '')) || 0;
            totalSubtotal += sub;
        });

        const currentJenis = jenisBiayaSelect.options[jenisBiayaSelect.selectedIndex].getAttribute('data-kode');
        if (selectedJenisBiaya.nama.toLowerCase().includes('trucking')) {
            if (nominalInput) {
                nominalInput.value = totalSubtotal > 0 ? Math.round(totalSubtotal).toLocaleString('id-ID') : '';
            }
        }
    }

    // Helper for Trucking BL Chips in Edit Mode
    window.addBlChipToTruckingSection = function(sectionIndex, blId) {
        const section = document.querySelector(`.trucking-section[data-trucking-section-index="${sectionIndex}"]`);
        const chipsContainer = section.querySelector('.trucking-selected-bl-chips');
        const hiddenInputsContainer = section.querySelector('.trucking-hidden-bl-inputs');
        const placeholder = section.querySelector('.placeholder-text');
        
        placeholder.classList.add('hidden');
        
        const chip = document.createElement('span');
        chip.className = 'trucking-chip bg-blue-600 text-white text-[10px] px-2 py-0.5 rounded flex items-center gap-1';
        chip.setAttribute('data-id', blId);
        chip.innerHTML = `${blId} <i class="fas fa-times cursor-pointer hover:text-red-200"></i>`;
        chip.querySelector('i').onclick = (e) => {
            e.stopPropagation();
            chip.remove();
            const input = hiddenInputsContainer.querySelector(`input[value="${blId}"]`);
            if (input) input.remove();
            updateTruckingCount(sectionIndex);
            calculateTruckingTotals(sectionIndex);
        };
        chipsContainer.appendChild(chip);

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `trucking_sections[${sectionIndex}][no_bl][]`;
        input.value = blId;
        hiddenInputsContainer.appendChild(input);
        
        updateTruckingCount(sectionIndex);
    };

