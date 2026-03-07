    // ============= OPERASIONAL SECTION LOGIC =============
    var operasionalSectionCounter = 0;
    var operasionalSectionsContainer = document.getElementById('operasional_sections_container');
    var addOperasionalSectionBtn = document.getElementById('add_operasional_section_btn');
    var addOperasionalSectionBottomBtn = document.getElementById('add_operasional_section_bottom_btn');
    
    // Initialize cache for voyages
    var cachedVoyages = {};
    
    // Helper function to format currency inputs
    function formatCurrency(input) {
        // Remove non-numeric chars
        let value = input.value.replace(/\D/g, '');
        
        // Format with thousand separator
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        
        input.value = value;
    }

    if (addOperasionalSectionBtn) {
        addOperasionalSectionBtn.addEventListener('click', function() {
            addOperasionalSection();
        });
    }

    if (addOperasionalSectionBottomBtn) {
        addOperasionalSectionBottomBtn.addEventListener('click', function() {
            addOperasionalSection();
        });
    }

    function addOperasionalSection() {
        operasionalSectionCounter++;
        const sectionIndex = operasionalSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'operasional-section mb-6 p-4 border-2 border-indigo-200 rounded-lg bg-indigo-50';
        section.setAttribute('data-section-index', sectionIndex);
        
        // Kapal Options
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        if (typeof allKapalsData !== 'undefined') {
            allKapalsData.forEach(kapal => {
                kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
            });
        }
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-md font-semibold text-indigo-800">
                    <i class="fas fa-ship mr-2"></i>Kapal ${sectionIndex} (Operasional)
                </h4>
                ${sectionIndex > 0 ? `<button type="button" onclick="removeOperasionalSection(${sectionIndex})" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs rounded transition"><i class="fas fa-times mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kapal</label>
                    <select name="operasional_sections[${sectionIndex}][kapal]" class="kapal-select-operasional w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Voyage</label>
                    <select name="operasional_sections[${sectionIndex}][voyage]" id="voyage_operasional_${sectionIndex}" class="voyage-select-operasional w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" disabled required>
                        <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                    </select>
                </div>
                <div>
                     <label class="block text-sm font-medium text-gray-700 mb-1">Nominal</label>
                     <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-xs">Rp</span>
                        <input type="text" name="operasional_sections[${sectionIndex}][nominal]" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="0" oninput="formatCurrency(this); calculateTotalFromAllOperasionalSections()" required>
                     </div>
                </div>
            </div>
        `;
        
        operasionalSectionsContainer.appendChild(section);
        
        // Add event listener for kapal change with Select2
        const kapalSelect = section.querySelector('.kapal-select-operasional');
        const voyageSelect = section.querySelector('.voyage-select-operasional');

        $(kapalSelect).select2({
            placeholder: "-- Pilih Kapal --",
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 0
        }).on('change', function() {
            loadVoyageForOperasional(this, voyageSelect);
        });
    }

    function removeOperasionalSection(index) {
        const section = document.querySelector(`.operasional-section[data-section-index="${index}"]`);
        if (section) {
            $(section).find('.kapal-select-operasional').select2('destroy');
            section.remove();
        }
        calculateTotalFromAllOperasionalSections();
    }
    
    function loadVoyageForOperasional(selectElement, voyageSelectOrIndex) {
        const namaKapal = selectElement.value;
        
        let voyageSelect;
        if (typeof voyageSelectOrIndex === 'object') {
            voyageSelect = voyageSelectOrIndex;
        } else {
            voyageSelect = document.getElementById(`voyage_operasional_${voyageSelectOrIndex}`);
        }

        if (!voyageSelect) {
            console.error('Voyage select element not found');
            return;
        }
        
        voyageSelect.innerHTML = '<option value="">Memuat...</option>';
        voyageSelect.disabled = true;
        
        if (!namaKapal) {
             voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
             return;
        }

        // Use cached voyages if available
        if (cachedVoyages[namaKapal]) {
            populateVoyageSelect(voyageSelect, cachedVoyages[namaKapal]);
        } else {
            // Fetch voyages
            fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(namaKapal)}`)
                .then(response => response.json())
                .then(data => {
                    // Adapt to the response format (data.voyages which is array of strings)
                    const voyages = data.voyages ? data.voyages.map(v => ({ nomor_voyage: v })) : [];
                    cachedVoyages[namaKapal] = voyages; // Cache it
                    populateVoyageSelect(voyageSelect, voyages);
                })
                .catch(error => {
                    console.error('Error fetching voyages:', error);
                    voyageSelect.innerHTML = '<option value="">Gagal memuat voyage</option>';
                });
        }
    }
    
    function populateVoyageSelect(selectElement, voyages) {
        if (voyages.length === 0) {
            selectElement.innerHTML = '<option value="">Tidak ada voyage aktif</option>';
            selectElement.disabled = false; 
        } else {
            let options = '<option value="">-- Pilih Voyage --</option>';
            voyages.forEach(v => {
                options += `<option value="${v.nomor_voyage}">${v.nomor_voyage}</option>`;
            });
            selectElement.innerHTML = options;
            selectElement.disabled = false;
        }
    }
    
    function calculateTotalFromAllOperasionalSections() {
        let grandTotal = 0;
        document.querySelectorAll('input[name^="operasional_sections"][name$="[nominal]"]').forEach(input => {
            grandTotal += parseInt(input.value.replace(/\D/g, '') || 0);
        });
        
        if (nominalInput) {
            nominalInput.value = grandTotal > 0 ? grandTotal.toLocaleString('id-ID') : '';
            if (typeof calculateSisaPembayaran === 'function') {
                calculateSisaPembayaran(); 
            }
        }
    }
    
    function initializeOperasionalSections() {
        // Only initialize if we have data to show, otherwise default logic (adding 1 empty) applies or is handled by caller
        if(existingOperasionalSections.length > 0) {
            clearAllOperasionalSections();
            existingOperasionalSections.forEach(data => {
                 addOperasionalSection();
                 const sec = operasionalSectionsContainer.lastElementChild;
                 const sectionIndex = sec.getAttribute('data-section-index');
                 
                 $(sec.querySelector('.kapal-select-operasional')).val(data.kapal).trigger('change');
                 
                 const voySel = sec.querySelector('.voyage-select-operasional');
                 voySel.innerHTML = `<option value="${data.voyage}">${data.voyage}</option>`;
                 voySel.value = data.voyage;
                 voySel.disabled = false;
                 
                 const nomInput = sec.querySelector('input[name="operasional_sections['+sectionIndex+'][nominal]"]');
                 if(nomInput) nomInput.value = parseInt(data.nominal).toLocaleString('id-ID');
            });
            calculateTotalFromAllOperasionalSections();
        }
    }

    function clearAllOperasionalSections() {
        operasionalSectionsContainer.innerHTML = '';
        operasionalSectionCounter = 0;
        if (nominalInput) nominalInput.value = '';
    }
