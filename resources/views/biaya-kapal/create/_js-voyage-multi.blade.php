    // ============= VOYAGE MULTI-SELECT =============
    const voyageSearch = document.getElementById('voyage_search');
    const voyageDropdown = document.getElementById('voyage_dropdown');
    const selectedVoyageChips = document.getElementById('selected_voyage_chips');
    const hiddenVoyageInputs = document.getElementById('hidden_voyage_inputs');
    const voyageSelectedCount = document.getElementById('voyageSelectedCount');
    const btnSelectAllVoyage = document.getElementById('selectAllVoyageBtn');
    const btnClearAllVoyage = document.getElementById('clearAllVoyageBtn');
    
    let selectedVoyages = [];
    let availableVoyages = [];
    const oldVoyageValue = @json(old('no_voyage', []));
    
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
    