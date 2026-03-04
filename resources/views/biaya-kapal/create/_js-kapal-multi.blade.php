    // ============= KAPAL MULTI-SELECT =============
    const kapalSearch = document.getElementById('kapal_search');
    const kapalDropdown = document.getElementById('kapal_dropdown');
    const selectedKapalChips = document.getElementById('selected_kapal_chips');
    const hiddenKapalInputs = document.getElementById('hidden_kapal_inputs');
    const kapalOptions = document.querySelectorAll('.kapal-option');
    const kapalSelectedCount = document.getElementById('kapalSelectedCount');
    const btnSelectAllKapal = document.getElementById('selectAllKapalBtn');
    const btnClearAllKapal = document.getElementById('clearAllKapalBtn');
    
    let selectedKapals = [];
    const oldKapalValue = @json(old('nama_kapal', []));
    
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
