    // ============= BL MULTI-SELECT =============
    const blSearch = document.getElementById('bl_search');
    const blDropdown = document.getElementById('bl_dropdown');
    const selectedBlChips = document.getElementById('selected_bl_chips');
    const hiddenBlInputs = document.getElementById('hidden_bl_inputs');
    const blSelectedCount = document.getElementById('blSelectedCount');
    const btnSelectAllBl = document.getElementById('selectAllBlBtn');
    const btnClearAllBl = document.getElementById('clearAllBlBtn');
    
    let selectedBls = {}; // Changed to object to store {id: {kontainer, seal}}
    let availableBls = {}; // Changed to object to store {id: {kontainer, seal}}
    const oldBlValue = @json(old('no_bl', []));
    
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
        fetch("{{ url('biaya-kapal/get-bls-by-voyages') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                voyages: selectedVoyages
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

    // Generate Invoice Number (for display only)
    async function generateInvoiceNumber() {
        const invoiceInput = document.getElementById('nomor_invoice_display');
        const loader = document.getElementById('invoice_loader');
        
        try {
            const response = await fetch("{{ route('biaya-kapal.get-next-invoice-number', [], false) }}", {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            const data = await response.json();
            
            if (data.success) {
                invoiceInput.value = data.invoice_number + ' (Preview)';
            } else {
                // Fallback if server generation fails
                const now = new Date();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const year = String(now.getFullYear()).slice(-2);
                invoiceInput.value = `BKP-${month}-${year}-XXXXXX (Preview)`;
                console.warn('Failed to generate invoice number from server, using fallback');
            }
        } catch (error) {
            // Fallback if fetch fails
            const now = new Date();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = String(now.getFullYear()).slice(-2);
            invoiceInput.value = `BKP-${month}-${year}-XXXXXX (Preview)`;
            console.error('Error generating invoice number:', error);
        } finally {
            if (loader) {
                loader.style.display = 'none';
            }
        }
    }

    // Form submit handler removed - sanitization now handled more robustly in backend
    document.addEventListener('DOMContentLoaded', function() {
        generateInvoiceNumber();
    });
