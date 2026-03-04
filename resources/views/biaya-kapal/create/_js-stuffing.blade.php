    // ============= STUFFING SECTIONS MANAGEMENT =============
    let stuffingSectionCounter = 0;
    
    function initializeStuffingSections() {
        if (!stuffingSectionsContainer) return;
        stuffingSectionsContainer.innerHTML = '';
        stuffingSectionCounter = 0;
        addStuffingSection();
    }
    
    function clearAllStuffingSections() {
        if (!stuffingSectionsContainer) return;
        stuffingSectionsContainer.innerHTML = '';
        stuffingSectionCounter = 0;
    }
    
    if (addStuffingSectionBtn) {
        addStuffingSectionBtn.addEventListener('click', function() {
            addStuffingSection();
        });
    }

    if (addStuffingSectionBottomBtn) {
        addStuffingSectionBottomBtn.addEventListener('click', function() {
            addStuffingSection();
        });
    }
    
    function addStuffingSection() {
        if (!stuffingSectionsContainer) return;
        stuffingSectionCounter++;
        const sectionIndex = stuffingSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'stuffing-section mb-6 p-4 border-2 border-rose-200 rounded-lg bg-rose-50';
        section.setAttribute('data-stuffing-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });
        
        section.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex} (Stuffing)</h3>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeStuffingSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="stuffing_sections[${sectionIndex}][kapal]" class="stuffing-kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="stuffing_sections[${sectionIndex}][voyage]" class="stuffing-voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500" required disabled>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="stuffing_sections[${sectionIndex}][voyage]" class="stuffing-voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500 hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="stuffing-voyage-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="mb-4 p-4 bg-white rounded-lg border-2 border-dashed border-rose-300">
                <label class="block text-sm font-semibold text-gray-800 mb-2">Pilih Tanda Terima <span class="text-red-500">*</span></label>
                
                <div class="relative stuffing-multiselect-container">
                    <!-- Chips and Input Container -->
                    <div class="w-full min-h-[42px] px-3 py-2 border border-gray-300 rounded-lg bg-white cursor-text flex flex-wrap gap-2 items-center focus-within:ring-2 focus-within:ring-rose-500 focus-within:border-rose-500 transition-all shadow-sm"
                         onclick="this.querySelector('.tt-search-input').focus()">
                         
                        <div class="selected-chips flex flex-wrap gap-2"></div>
                        
                        <input type="text" 
                               class="tt-search-input border-none outline-none bg-transparent flex-1 min-w-[200px] text-sm focus:ring-0 p-1"
                               placeholder="Cari No. Surat Jalan / Kontainer / Pengirim..."
                               autocomplete="off">
                    </div>
                    
                    <!-- Dropdown Results -->
                    <div class="tt-results-dropdown hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                        <div class="p-4 text-center text-gray-400 text-sm">
                            <i class="fas fa-search mb-1"></i>
                            <p>Ketik minimal 2 karakter untuk mencari</p>
                        </div>
                    </div>
                </div>
                
                <!-- Hidden Inputs for Form Submission -->
                <div class="hidden-inputs-container"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t pt-4 mt-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal Biaya</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                        <input type="text" name="stuffing_sections[${sectionIndex}][subtotal]" 
                               class="stuffing-subtotal-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500" 
                               value="0">
                    </div>
                </div>
                <div class="hidden">
                    <input type="hidden" name="stuffing_sections[${sectionIndex}][pph]" 
                           class="stuffing-pph-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-0" 
                           value="0" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Biaya</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                        <input type="text" name="stuffing_sections[${sectionIndex}][total_biaya]" 
                               class="stuffing-total-input w-full pl-10 pr-3 py-2 border border-blue-300 rounded-lg bg-blue-50 text-blue-800 font-bold focus:ring-0" 
                               value="0" readonly>
                    </div>
                </div>
            </div>
        `;
        
        stuffingSectionsContainer.appendChild(section);
        
        // Setup kapal change listener
        const kapalSelect = section.querySelector('.stuffing-kapal-select');
        kapalSelect.addEventListener('change', function() {
            loadVoyagesForStuffingSection(sectionIndex, this.value);
        });

        // Setup manual voyage toggle
        const voyageSelect = section.querySelector('.stuffing-voyage-select');
        const voyageInput = section.querySelector('.stuffing-voyage-input');
        const voyageManualBtn = section.querySelector('.stuffing-voyage-manual-btn');

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

        // Setup manual subtotal calculation
        const subtotalInput = section.querySelector('.stuffing-subtotal-input');
        subtotalInput.addEventListener('input', function() {
            // Remove non-numeric chars
            let rawValue = this.value.replace(/[^0-9]/g, '');
            const numericValue = parseFloat(rawValue) || 0;
            
            // Format back to rupiah
            if (rawValue) {
                this.value = new Intl.NumberFormat('id-ID').format(numericValue);
            } else {
                this.value = '';
            }
            
            // Recalculate PPh (0) & Total
            const pph = 0;
            const total = numericValue - pph;
            
            section.querySelector('.stuffing-pph-input').value = new Intl.NumberFormat('id-ID').format(pph);
            section.querySelector('.stuffing-total-input').value = new Intl.NumberFormat('id-ID').format(total);
            
            // Update Grand Total
            calculateTotalFromAllStuffingSections();
        });

        // --- SEARCHABLE MULTI-SELECT LOGIC ---
        const multiselectContainer = section.querySelector('.stuffing-multiselect-container');
        const searchInput = section.querySelector('.tt-search-input');
        const resultsDropdown = section.querySelector('.tt-results-dropdown');
        const selectedChipsContainer = section.querySelector('.selected-chips');
        const hiddenInputsContainer = section.querySelector('.hidden-inputs-container');
        
        let searchTimeout;

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!multiselectContainer.contains(e.target)) {
                resultsDropdown.classList.add('hidden');
            }
        });

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value;
            
            if (query.length < 2) {
                resultsDropdown.classList.add('hidden');
                return;
            }
            
            searchTimeout = setTimeout(() => {
                resultsDropdown.classList.remove('hidden');
                resultsDropdown.innerHTML = '<div class="p-3 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Mencari...</div>';
                
                fetch(`{{ url('biaya-kapal/search-tanda-terima') }}?search=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                         resultsDropdown.innerHTML = '';
                         if (data.length > 0) {
                             let foundAny = false;
                             data.forEach(tt => {
                                 // Check if already selected
                                 if (hiddenInputsContainer.querySelector(`input[value="${tt.id}"]`)) return;
                                 
                                 foundAny = true;
                                 const item = document.createElement('div');
                                 item.className = 'p-3 hover:bg-rose-50 cursor-pointer border-b last:border-0 border-gray-100 transition-colors';
                                 
                                 let typeBadge = '';
                                 if (tt.type === 'tanda_terima_tanpa_surat_jalan') {
                                     typeBadge = '<span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full ml-2">Tanpa SJ</span>';
                                 } else if (tt.type === 'tanda_terima_lcl') {
                                     typeBadge = '<span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full ml-2">LCL</span>';
                                 }

                                 item.innerHTML = `
                                     <div class="flex items-start gap-2">
                                         <div class="flex-1">
                                             <div class="font-medium text-sm text-gray-800 flex items-center">
                                                 ${tt.display_text || tt.no_surat_jalan}
                                                 ${typeBadge}
                                             </div>
                                             <div class="text-xs text-gray-500 mt-1">
                                                 <span class="mr-2"><i class="fas fa-box text-orange-400 mr-1"></i>${tt.no_kontainer || '-'}</span>
                                                 <span><i class="fas fa-user-check text-green-400 mr-1"></i>${tt.penerima}</span>
                                             </div>
                                         </div>
                                     </div>
                                 `; 

                                 item.addEventListener('click', () => {
                                     addChip(tt);
                                     searchInput.value = '';
                                     resultsDropdown.classList.add('hidden');
                                     searchInput.focus();
                                 });
                                 resultsDropdown.appendChild(item);
                             });
                             
                             if (!foundAny) {
                                  resultsDropdown.innerHTML = '<div class="p-3 text-center text-gray-500">Semua hasil sudah dipilih</div>';
                             }
                         } else {
                             resultsDropdown.innerHTML = '<div class="p-3 text-center text-gray-500">Tidak ditemukan</div>';
                         }
                    })
                    .catch(e => {
                        console.error(e);
                        resultsDropdown.innerHTML = '<div class="p-3 text-center text-red-500">Gagal memuat data</div>';
                    });
            }, 300);
        });

        function addChip(tt) {
            const chip = document.createElement('div');
            // Styling similar to proses-naik-kapal but adjusted
            chip.className = 'inline-flex items-center bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full border border-blue-200 shadow-sm gap-2';
            
            const label = tt.display_text || tt.no_surat_jalan;
            chip.innerHTML = `
                <span class="font-medium text-xs">${label}</span>
                <button type="button" class="ml-1 text-blue-600 hover:text-blue-800 focus:outline-none rounded-full flex items-center justify-center bg-blue-200 hover:bg-blue-300 h-4 w-4 transition-colors">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
            `;
            
            chip.querySelector('button').addEventListener('click', (e) => {
                e.stopPropagation(); // prevent focusing input
                chip.remove();
                const hiddenInput = hiddenInputsContainer.querySelector(`.tt-input-group-${tt.id}`);
                if (hiddenInput) hiddenInput.remove();
            });
            
            selectedChipsContainer.appendChild(chip);
            
            // Add hidden inputs
            const hiddenGroup = document.createElement('div');
            hiddenGroup.className = `tt-input-group-${tt.id}`;
            hiddenGroup.innerHTML = `
                <input type="hidden" name="stuffing_sections[${sectionIndex}][tanda_terima][][id]" value="${tt.id}">
                <input type="hidden" name="stuffing_sections[${sectionIndex}][tanda_terima][][type]" value="${tt.type || 'tanda_terima'}">
            `;
            hiddenInputsContainer.appendChild(hiddenGroup);
        }
    }
    
    window.removeStuffingSection = function(sectionIndex) {
        const section = document.querySelector(`[data-stuffing-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllStuffingSections();
        }
    };
    
    function loadVoyagesForStuffingSection(sectionIndex, kapalNama) {
        const section = document.querySelector(`[data-stuffing-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.stuffing-voyage-select');
        
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
                if (data.success && data.voyages) {
                    let html = '<option value="">-- Pilih Voyage --</option>';
                    data.voyages.forEach(voyage => {
                        html += `<option value="${voyage}">${voyage}</option>`;
                    });
                    voyageSelect.innerHTML = html;
                    voyageSelect.disabled = false;
                } else {
                    voyageSelect.innerHTML = '<option value="">Tidak ada voyage tersedia</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching voyages for Stuffing:', error);
                voyageSelect.innerHTML = '<option value="">Gagal memuat voyages</option>';
            });
    }

    function calculateStuffingTotals(sectionIndex) {
        const section = document.querySelector(`.stuffing-section[data-stuffing-section-index="${sectionIndex}"]`);
        if (!section) return;

        const ttContainer = section.querySelector('.stuffing-tt-container');
        const subtotalInput = section.querySelector('.stuffing-subtotal-input');
        const pphInput = section.querySelector('.stuffing-pph-input');
        const totalInput = section.querySelector('.stuffing-total-input');

        console.log(`[Stuffing Calculation Section ${sectionIndex}]`);

        // Read manual subtotal instead of auto-calculating
        let subtotal = parseFloat(subtotalInput.value.replace(/[^0-9]/g, '')) || 0;
        
        // Remove automatic calculation logic
        /*
        const ttItems = ttContainer.querySelectorAll('.tt-search-wrapper');
        ttItems.forEach((item, index) => {
            // For now, we'll use a fixed price per tanda terima
            // In the future, this could be based on pricelist or other factors
            const pricePerTt = 500000; // Example: 500,000 per tanda terima
            subtotal += pricePerTt;
            console.log(`  - Tanda Terima ${index + 1}: ${pricePerTt}`);
        });
        */

        const pph = 0; // No PPH for Biaya Stuffing
        const total = subtotal - pph;
        
        console.log('- Subtotal:', subtotal);
        console.log('- PPh:', pph);
        console.log('- Total:', total);

        const formatRupiah = (val) => {
            return new Intl.NumberFormat('id-ID').format(Math.round(val));
        };

        // Don't overwrite subtotalInput value as it is user input now
        // subtotalInput.value = formatRupiah(subtotal);
        
        pphInput.value = formatRupiah(pph);
        totalInput.value = formatRupiah(total);

        // Update global summary
        calculateTotalFromAllStuffingSections();
    }

    function calculateTotalFromAllStuffingSections() {
        let totalSubtotal = 0;

        document.querySelectorAll('.stuffing-section').forEach(section => {
            const sub = parseFloat(section.querySelector('.stuffing-subtotal-input').value.replace(/\./g, '')) || 0;
            totalSubtotal += sub;
        });

        // Add to nominal input if stuffing is the selected jenis biaya
        const jenisBiaya = jenisBiayaSelect ? jenisBiayaSelect.value : '';
        if (jenisBiaya === 'Stuffing') {
            if (nominalInput) {
                nominalInput.value = totalSubtotal > 0 ? Math.round(totalSubtotal).toLocaleString('id-ID') : '';
            }
        }
    }

