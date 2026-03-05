    // ============= KAPAL MULTI-SELECT =============
    var kapalSearch = document.getElementById('kapal_search');
    var kapalDropdown = document.getElementById('kapal_dropdown');
    var selectedKapalChips = document.getElementById('selected_kapal_chips');
    var hiddenKapalInputs = document.getElementById('hidden_kapal_inputs');
    var kapalOptions = document.querySelectorAll('.kapal-option');
    var kapalSelectedCount = document.getElementById('kapalSelectedCount');
    var btnSelectAllKapal = document.getElementById('selectAllKapalBtn');
    var btnClearAllKapal = document.getElementById('clearAllKapalBtn');
    
    var selectedKapals = [];
    var oldKapalValue = @json(old('nama_kapal', $biayaKapal->nama_kapal ?? []));
    
    // Show kapal dropdown on focus
    kapalSearch.addEventListener('focus', function() {
        kapalDropdown.classList.remove('hidden');
        filterKapalOptions();
        
        // Show hint on first focus
        if (!localStorage.getItem('kapal_multiselect_hint_shown')) {
            const hint = document.createElement('div');
            hint.className = 'px-3 py-2 bg-green-50 border-b border-green-200 text-xs text-green-700 font-medium';
            hint.innerHTML = '<i class="fas fa-lightbulb mr-1"></i> Tip: Klik beberapa item untuk memilih lebih dari 1 kapal';
            kapalDropdown.insertBefore(hint, kapalDropdown.firstChild);
            localStorage.setItem('kapal_multiselect_hint_shown', 'true');
            
            setTimeout(() => {
                hint.style.transition = 'opacity 0.5s';
                hint.style.opacity = '0';
                setTimeout(() => hint.remove(), 500);
            }, 5000);
        }
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#kapal_container') && !e.target.closest('#kapal_dropdown')) {
            kapalDropdown.classList.add('hidden');
        }
        if (!e.target.closest('#voyage_container_input') && !e.target.closest('#voyage_dropdown')) {
            voyageDropdown.classList.add('hidden');
        }
    });
    
    // Search/filter kapal options
    kapalSearch.addEventListener('input', function() {
        filterKapalOptions();
    });
    
    function filterKapalOptions() {
        const searchTerm = kapalSearch.value.toLowerCase();
        kapalOptions.forEach(option => {
            const nama = option.getAttribute('data-nama').toLowerCase();
            const shouldShow = nama.includes(searchTerm);
            option.style.display = shouldShow ? 'block' : 'none';
        });
    }
    
    // Handle kapal option selection
    kapalOptions.forEach(option => {
        option.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            
            if (!selectedKapals.find(k => k.nama === nama)) {
                selectedKapals.push({ id, nama });
                addKapalChip(id, nama);
                updateKapalHiddenInputs();
                updateKapalSelectedCount();
                updateVoyages();
                this.classList.add('selected');
            } else {
                // If already selected, show visual feedback
                this.style.backgroundColor = '#fee2e2';
                setTimeout(() => {
                    this.style.backgroundColor = '';
                }, 300);
            }
            
            kapalSearch.value = '';
            // Don't hide dropdown to allow multiple selections
            // kapalDropdown.classList.add('hidden');
        });
    });
    
    function addKapalChip(id, nama) {
        const chip = document.createElement('span');
        chip.className = 'selected-chip';
        chip.setAttribute('data-nama', nama);
        chip.innerHTML = `
            <span class="font-medium">${nama}</span>
            <span class="remove-chip" onclick="removeKapalChip('${nama}')">&times;</span>
        `;
        selectedKapalChips.appendChild(chip);
    }
    
    window.removeKapalChip = function(nama) {
        selectedKapals = selectedKapals.filter(k => k.nama !== nama);
        const chip = document.querySelector(`[data-nama="${nama}"].selected-chip`);
        if (chip) chip.remove();
        
        const option = Array.from(kapalOptions).find(opt => opt.getAttribute('data-nama') === nama);
        if (option) option.classList.remove('selected');
        
        updateKapalHiddenInputs();
        updateKapalSelectedCount();
        updateVoyages();
    };
    
    if (btnSelectAllKapal) {
        btnSelectAllKapal.addEventListener('click', function() {
            kapalOptions.forEach(option => {
                const id = option.getAttribute('data-id');
                const nama = option.getAttribute('data-nama');
                
                if (!selectedKapals.find(k => k.nama === nama)) {
                    selectedKapals.push({ id, nama });
                    addKapalChip(id, nama);
                    option.classList.add('selected');
                }
            });
            
            updateKapalHiddenInputs();
            updateKapalSelectedCount();
            updateVoyages();
        });
    }
    
    if (btnClearAllKapal) {
        btnClearAllKapal.addEventListener('click', function() {
            selectedKapals = [];
            selectedKapalChips.innerHTML = '';
            hiddenKapalInputs.innerHTML = '';
            kapalOptions.forEach(option => option.classList.remove('selected'));
            updateKapalSelectedCount();
            updateVoyages();
        });
    }
    
    function updateKapalHiddenInputs() {
        hiddenKapalInputs.innerHTML = '';
        selectedKapals.forEach(kapal => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'nama_kapal[]';
            input.value = kapal.nama;
            hiddenKapalInputs.appendChild(input);
        });
    }
    
    function updateKapalSelectedCount() {
        kapalSelectedCount.textContent = `Terpilih: ${selectedKapals.length} dari ${kapalOptions.length} kapal`;
    }

    // ============= VOYAGE MULTI-SELECT =============
    var voyageSearch = document.getElementById('voyage_search');
    var voyageDropdown = document.getElementById('voyage_dropdown');
    var selectedVoyageChips = document.getElementById('selected_voyage_chips');
    var hiddenVoyageInputs = document.getElementById('hidden_voyage_inputs');
    var voyageSelectedCount = document.getElementById('voyageSelectedCount');
    var btnSelectAllVoyage = document.getElementById('selectAllVoyageBtn');
    var btnClearAllVoyage = document.getElementById('clearAllVoyageBtn');
    
    var selectedVoyages = [];
    var availableVoyages = [];
    var oldVoyageValue = @json(old('no_voyage', []));
    
    // Show voyage dropdown on focus
    voyageSearch.addEventListener('focus', function() {
        if (selectedKapals.length > 0) {
            voyageDropdown.classList.remove('hidden');
            filterVoyageOptions();
            
            // Show hint on first focus
            if (!localStorage.getItem('voyage_multiselect_hint_shown')) {
                setTimeout(() => {
                    const hint = document.createElement('div');
                    hint.className = 'px-3 py-2 bg-green-50 border-b border-green-200 text-xs text-green-700 font-medium';
                    hint.innerHTML = '<i class="fas fa-lightbulb mr-1"></i> Tip: Klik beberapa voyage untuk memilih lebih dari 1';
                    if (voyageDropdown.firstChild && !voyageDropdown.firstChild.textContent.includes('Memuat')) {
                        voyageDropdown.insertBefore(hint, voyageDropdown.firstChild);
                        localStorage.setItem('voyage_multiselect_hint_shown', 'true');
                        
                        setTimeout(() => {
                            hint.style.transition = 'opacity 0.5s';
                            hint.style.opacity = '0';
                            setTimeout(() => hint.remove(), 500);
                        }, 5000);
                    }
                }, 500);
            }
        }
    });
    
    // Search/filter voyage options
    voyageSearch.addEventListener('input', function() {
        filterVoyageOptions();
    });
    
    function filterVoyageOptions() {
        const searchTerm = voyageSearch.value.toLowerCase();
        const voyageOptions = voyageDropdown.querySelectorAll('.voyage-option');
        voyageOptions.forEach(option => {
            const voyage = option.getAttribute('data-voyage').toLowerCase();
            const shouldShow = voyage.includes(searchTerm);
            option.style.display = shouldShow ? 'block' : 'none';
        });
    }
    
    function addVoyageChip(voyage) {
        const chip = document.createElement('span');
        chip.className = 'selected-chip';
        chip.setAttribute('data-voyage', voyage);
        chip.innerHTML = `
            <span class="font-medium">${voyage}</span>
            <span class="remove-chip" onclick="removeVoyageChip('${voyage}')">&times;</span>
        `;
        selectedVoyageChips.appendChild(chip);
    }
    
    window.removeVoyageChip = function(voyage) {
        selectedVoyages = selectedVoyages.filter(v => v !== voyage);
        const chip = document.querySelector(`[data-voyage="${voyage}"].selected-chip`);
        if (chip) chip.remove();
        
        const option = voyageDropdown.querySelector(`[data-voyage="${voyage}"].voyage-option`);
        if (option) option.classList.remove('selected');
        
        updateVoyageHiddenInputs();
        updateVoyageSelectedCount();
        updateBls();
    };
    
    if (btnSelectAllVoyage) {
        btnSelectAllVoyage.addEventListener('click', function() {
            selectedVoyages = [...availableVoyages];
            selectedVoyageChips.innerHTML = '';
            availableVoyages.forEach(voyage => {
                addVoyageChip(voyage);
            });
            
            const voyageOptions = voyageDropdown.querySelectorAll('.voyage-option');
            voyageOptions.forEach(option => option.classList.add('selected'));
            
            updateVoyageHiddenInputs();
            updateVoyageSelectedCount();
            updateBls();
        });
    }
    
    if (btnClearAllVoyage) {
        btnClearAllVoyage.addEventListener('click', function() {
            selectedVoyages = [];
            selectedVoyageChips.innerHTML = '';
            hiddenVoyageInputs.innerHTML = '';
            
            const voyageOptions = voyageDropdown.querySelectorAll('.voyage-option');
            voyageOptions.forEach(option => option.classList.remove('selected'));
            
            updateBls();
            updateVoyageSelectedCount();
        });
    }
    
    function updateVoyageHiddenInputs() {
        hiddenVoyageInputs.innerHTML = '';
        selectedVoyages.forEach(voyage => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'no_voyage[]';
            input.value = voyage;
            hiddenVoyageInputs.appendChild(input);
        });
    }
    
    function updateVoyageSelectedCount() {
        voyageSelectedCount.textContent = `Terpilih: ${selectedVoyages.length} voyage`;
    }
    
    // Function to fetch and display voyages for selected ships
    function updateVoyages() {
        if (selectedKapals.length === 0) {
            voyageSearch.disabled = true;
            voyageSearch.placeholder = '--Pilih Kapal Terlebih Dahulu--';
            voyageDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Pilih kapal terlebih dahulu</p>';
            selectedVoyages = [];
            selectedVoyageChips.innerHTML = '';
            hiddenVoyageInputs.innerHTML = '';
            updateVoyageSelectedCount();
            return;
        }
        
        voyageSearch.disabled = false;
        voyageSearch.placeholder = '--Pilih Voyage--';
        voyageDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Memuat voyages...</p>';
        
        // Fetch voyages for all selected ships
        const fetchPromises = selectedKapals.map(kapal => 
            fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(kapal.nama)}`)
                .then(response => response.json())
        );
        
        Promise.all(fetchPromises)
            .then(results => {
                // Collect all voyages from all ships
                const allVoyages = new Set();
                results.forEach(data => {
                    if (data.success && data.voyages) {
                        data.voyages.forEach(voyage => allVoyages.add(voyage));
                    }
                });
                
                availableVoyages = Array.from(allVoyages).sort();
                
                if (availableVoyages.length > 0) {
                    // Create option list
                    let html = '';
                    availableVoyages.forEach(voyage => {
                        const isSelected = selectedVoyages.includes(voyage) ? 'selected' : '';
                        html += `
                            <div class="voyage-option px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 ${isSelected}"
                                 data-voyage="${voyage}">
                                <div class="font-medium text-gray-900">${voyage}</div>
                            </div>
                        `;
                    });
                    voyageDropdown.innerHTML = html;
                    
                    // Add click handlers to new options
                    const voyageOptions = voyageDropdown.querySelectorAll('.voyage-option');
                    voyageOptions.forEach(option => {
                        option.addEventListener('click', function() {
                            const voyage = this.getAttribute('data-voyage');
                            
                            if (!selectedVoyages.includes(voyage)) {
                                selectedVoyages.push(voyage);
                                addVoyageChip(voyage);
                                updateVoyageHiddenInputs();
                                updateVoyageSelectedCount();
                                updateBls();
                                this.classList.add('selected');
                            } else {
                                // If already selected, show visual feedback
                                this.style.backgroundColor = '#fee2e2';
                                setTimeout(() => {
                                    this.style.backgroundColor = '';
                                }, 300);
                            }
                            
                            voyageSearch.value = '';
                            // Don't hide dropdown to allow multiple selections
                            // voyageDropdown.classList.add('hidden');
                        });
                    });
                } else {
                    voyageDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Tidak ada voyage untuk kapal yang dipilih</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching voyages:', error);
                voyageDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-red-600">Gagal memuat voyages. Silakan coba lagi.</p>';
            });
    }
    
    // ============= BL MULTI-SELECT =============
    var blSearch = document.getElementById('bl_search');
    var blDropdown = document.getElementById('bl_dropdown');
    var selectedBlChips = document.getElementById('selected_bl_chips');
    var hiddenBlInputs = document.getElementById('hidden_bl_inputs');
    var blSelectedCount = document.getElementById('blSelectedCount');
    var btnSelectAllBl = document.getElementById('selectAllBlBtn');
    var btnClearAllBl = document.getElementById('clearAllBlBtn');
    
    var selectedBls = {}; // Changed to object to store {id: {kontainer, seal}}
    var availableBls = {}; // Changed to object to store {id: {kontainer, seal}}
    var oldBlValue = @json(old('no_bl', []));
    
    // Show BL dropdown on focus
    blSearch.addEventListener('focus', function() {
        if (selectedVoyages.length > 0) {
            blDropdown.classList.remove('hidden');
            filterBlOptions();
            
            // Show hint on first focus
            if (!localStorage.getItem('bl_multiselect_hint_shown')) {
                setTimeout(() => {
                    const hint = document.createElement('div');
                    hint.className = 'px-3 py-2 bg-green-50 border-b border-green-200 text-xs text-green-700 font-medium';
                    hint.innerHTML = '<i class="fas fa-lightbulb mr-1"></i> Tip: Klik beberapa kontainer untuk memilih lebih dari 1';
                    if (blDropdown.firstChild && !blDropdown.firstChild.textContent.includes('Memuat')) {
                        blDropdown.insertBefore(hint, blDropdown.firstChild);
                        localStorage.setItem('bl_multiselect_hint_shown', 'true');
                        
                        setTimeout(() => {
                            hint.style.transition = 'opacity 0.5s';
                            hint.style.opacity = '0';
                            setTimeout(() => hint.remove(), 500);
                        }, 5000);
                    }
                }, 500);
            }
        }
    });
    
    // Hide BL dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#bl_container_input') && !e.target.closest('#bl_dropdown')) {
            blDropdown.classList.add('hidden');
        }
    });
    
    // Search/filter BL options
    blSearch.addEventListener('input', function() {
        filterBlOptions();
    });
    
    function filterBlOptions() {
        const searchTerm = blSearch.value.toLowerCase();
        const blOptions = blDropdown.querySelectorAll('.bl-option');
        blOptions.forEach(option => {
            const kontainer = option.getAttribute('data-kontainer').toLowerCase();
            const seal = option.getAttribute('data-seal').toLowerCase();
            const shouldShow = kontainer.includes(searchTerm) || seal.includes(searchTerm);
            option.style.display = shouldShow ? 'block' : 'none';
        });
    }
    
    function addBlChip(blId, kontainer, seal) {
        const chip = document.createElement('span');
        chip.className = 'selected-chip';
        chip.setAttribute('data-bl', blId);
        chip.innerHTML = `
            <div class="flex flex-col">
                <span class="font-medium">${kontainer}</span>
                <span class="text-xs opacity-75">Seal: ${seal}</span>
            </div>
            <span class="remove-chip" onclick="removeBlChip('${blId}')">&times;</span>
        `;
        selectedBlChips.appendChild(chip);
    }
    
    window.removeBlChip = function(blId) {
        delete selectedBls[blId];
        const chip = document.querySelector(`[data-bl="${blId}"].selected-chip`);
        if (chip) chip.remove();
        
        const option = blDropdown.querySelector(`[data-bl="${blId}"].bl-option`);
        if (option) option.classList.remove('selected');
        
        updateBlHiddenInputs();
        updateBlSelectedCount();
        calculateDokumenNominal();
    };
    
    if (btnSelectAllBl) {
        btnSelectAllBl.addEventListener('click', function() {
            selectedBls = {...availableBls};
            selectedBlChips.innerHTML = '';
            Object.keys(availableBls).forEach(blId => {
                const blData = availableBls[blId];
                addBlChip(blId, blData.kontainer, blData.seal);
            });
            
            const blOptions = blDropdown.querySelectorAll('.bl-option');
            blOptions.forEach(option => option.classList.add('selected'));
            
            updateBlHiddenInputs();
            updateBlSelectedCount();
            calculateDokumenNominal();
        });
    }
    
    if (btnClearAllBl) {
        btnClearAllBl.addEventListener('click', function() {
            selectedBls = {};
            selectedBlChips.innerHTML = '';
            hiddenBlInputs.innerHTML = '';
            
            const blOptions = blDropdown.querySelectorAll('.bl-option');
            blOptions.forEach(option => option.classList.remove('selected'));
            
            updateBlSelectedCount();
            calculateDokumenNominal();
        });
    }
    
    function updateBlHiddenInputs() {
        hiddenBlInputs.innerHTML = '';
        Object.keys(selectedBls).forEach(blId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'no_bl[]';
            input.value = blId;
            hiddenBlInputs.appendChild(input);
        });
    }
    
    function updateBlSelectedCount() {
        blSelectedCount.textContent = `Terpilih: ${Object.keys(selectedBls).length} kontainer`;
    }
    
    // Function to fetch and display BLs for selected voyages
    function updateBls() {
        if (selectedVoyages.length === 0) {
            blSearch.disabled = true;
            blSearch.placeholder = '--Pilih Voyage Terlebih Dahulu--';
            blDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Pilih voyage terlebih dahulu</p>';
            selectedBls = {};
            selectedBlChips.innerHTML = '';
            hiddenBlInputs.innerHTML = '';
            updateBlSelectedCount();
            return;
        }
        
        blSearch.disabled = false;
        blSearch.placeholder = '--Cari Kontainer--';
        blDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Memuat kontainer...</p>';
        
        // Fetch BLs for all selected voyages
        const isTrucking = selectedJenisBiaya && selectedJenisBiaya.nama && selectedJenisBiaya.nama.toLowerCase().includes('trucking');
        
        fetch("{{ url('biaya-kapal/get-bls-by-voyages') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                voyages: selectedVoyages,
                source: isTrucking ? 'trucking' : null
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.bls) {
                availableBls = data.bls; // Now an object with {id: {kontainer, seal}}
                
                if (Object.keys(availableBls).length > 0) {
                    // Create option list
                    let html = '';
                    Object.keys(availableBls).sort((a, b) => {
                        const kontainerA = availableBls[a]?.kontainer || '';
                        const kontainerB = availableBls[b]?.kontainer || '';
                        return kontainerA.localeCompare(kontainerB);
                    }).forEach(blId => {
                        const blData = availableBls[blId];
                        if (!blData || !blData.kontainer || !blData.seal) return; // Skip invalid data
                        
                        const isSelected = selectedBls.hasOwnProperty(blId) ? 'selected' : '';
                        html += `
                            <div class="bl-option px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 ${isSelected}"
                                 data-bl="${blId}" 
                                 data-kontainer="${blData.kontainer}" 
                                 data-seal="${blData.seal}">
                                <div class="font-medium text-gray-900">${blData.kontainer}</div>
                                <div class="text-xs text-gray-500">Seal: ${blData.seal}</div>
                            </div>
                        `;
                    });
                    blDropdown.innerHTML = html;
                    
                    // Add click handlers to new options
                    const blOptions = blDropdown.querySelectorAll('.bl-option');
                    blOptions.forEach(option => {
                        option.addEventListener('click', function() {
                            const blId = this.getAttribute('data-bl');
                            const kontainer = this.getAttribute('data-kontainer');
                            const seal = this.getAttribute('data-seal');
                            
                            if (!selectedBls.hasOwnProperty(blId)) {
                                selectedBls[blId] = { kontainer, seal };
                                addBlChip(blId, kontainer, seal);
                                updateBlHiddenInputs();
                                updateBlSelectedCount();
                                calculateDokumenNominal();
                                this.classList.add('selected');
                            } else {
                                // If already selected, show visual feedback
                                this.style.backgroundColor = '#fee2e2';
                                setTimeout(() => {
                                    this.style.backgroundColor = '';
                                }, 300);
                            }
                            
                            blSearch.value = '';
                            // Don't hide dropdown to allow multiple selections
                            // blDropdown.classList.add('hidden');
                        });
                    });
                } else {
                    blDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Tidak ada kontainer untuk voyage yang dipilih</p>';
                }
            } else {
                blDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500 italic">Tidak ada kontainer tersedia</p>';
            }
        })
        .catch(error => {
            console.error('Error fetching BLs:', error);
            blDropdown.innerHTML = '<p class="px-3 py-2 text-sm text-red-600">Gagal memuat kontainer. Silakan coba lagi.</p>';
        });
    }
    
    // Restore old values on page load (for validation errors)
    if (oldKapalValue.length > 0) {
        oldKapalValue.forEach(namaKapal => {
            const option = Array.from(kapalOptions).find(opt => opt.getAttribute('data-nama') === namaKapal);
            if (option) {
                const id = option.getAttribute('data-id');
                selectedKapals.push({ id, nama: namaKapal });
                addKapalChip(id, namaKapal);
                option.classList.add('selected');
            }
        });
        updateKapalHiddenInputs();
        updateKapalSelectedCount();
        updateVoyages();
        
        // Restore voyage selections after voyages are loaded
        setTimeout(() => {
            if (oldVoyageValue.length > 0) {
                oldVoyageValue.forEach(voyage => {
                    if (availableVoyages.includes(voyage)) {
                        selectedVoyages.push(voyage);
                        addVoyageChip(voyage);
                        const option = voyageDropdown.querySelector(`[data-voyage="${voyage}"]`);
                        if (option) option.classList.add('selected');
                    }
                });
                updateVoyageHiddenInputs();
                updateVoyageSelectedCount();
                updateBls();
                
                // Restore BL selections after BLs are loaded
                setTimeout(() => {
                    if (oldBlValue.length > 0) {
                        oldBlValue.forEach(bl => {
                            if (availableBls.includes(bl)) {
                                selectedBls.push(bl);
                                addBlChip(bl);
                                const option = blDropdown.querySelector(`[data-bl="${bl}"]`);
                                if (option) option.classList.add('selected');
                            }
                        });
                        updateBlHiddenInputs();
                        updateBlSelectedCount();
                    }
                }, 1000);
            }
        }, 1000);
    }
