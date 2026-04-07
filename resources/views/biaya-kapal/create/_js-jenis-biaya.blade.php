    // ============= JENIS BIAYA SEARCHABLE DROPDOWN =============
    const jenisBiayaSearch = document.getElementById('jenis_biaya_search');
    const jenisBiayaContainer = document.getElementById('jenis_biaya_container');
    const jenisBiayaDropdown = document.getElementById('jenis_biaya_dropdown');
    const jenisBiayaHiddenInput = document.getElementById('jenis_biaya');
    const selectedJenisBiayaDisplay = document.getElementById('selected_jenis_biaya_display');
    const jenisBiayaOptions = document.querySelectorAll('.jenis-biaya-option');
    const clearJenisBiayaBtn = document.getElementById('clearJenisBiayaBtn');
    const jenisBiayaSelectedCount = document.getElementById('jenisBiayaSelectedCount');
    
    let selectedJenisBiaya = { kode: '', nama: '' };
    const oldJenisBiayaValue = "{{ old('jenis_biaya') }}";
    
    // Show dropdown on focus
    jenisBiayaSearch.addEventListener('focus', function() {
        jenisBiayaDropdown.classList.remove('hidden');
        filterJenisBiayaOptions();
    });
    
    // Container click to focus search
    jenisBiayaContainer.addEventListener('click', function() {
        jenisBiayaSearch.focus();
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#jenis_biaya_container') && !e.target.closest('#jenis_biaya_dropdown')) {
            jenisBiayaDropdown.classList.add('hidden');
        }
    });
    
    // Search/filter options
    jenisBiayaSearch.addEventListener('input', function() {
        filterJenisBiayaOptions();
    });
    
    function filterJenisBiayaOptions() {
        const searchTerm = jenisBiayaSearch.value.toLowerCase();
        jenisBiayaOptions.forEach(option => {
            const nama = option.getAttribute('data-nama').toLowerCase();
            const kode = option.getAttribute('data-kode').toLowerCase();
            const shouldShow = nama.includes(searchTerm) || kode.includes(searchTerm);
            option.style.display = shouldShow ? 'block' : 'none';
        });
    }
    
    // Handle option selection
    jenisBiayaOptions.forEach(option => {
        option.addEventListener('click', function() {
            const kode = this.getAttribute('data-kode');
            const nama = this.getAttribute('data-nama');
            
            selectedJenisBiaya = { kode, nama };
            jenisBiayaHiddenInput.value = kode;
            
            // Update display
            jenisBiayaSearch.value = '';
            jenisBiayaSearch.classList.add('hidden');
            selectedJenisBiayaDisplay.textContent = nama;
            selectedJenisBiayaDisplay.classList.remove('hidden');
            
            // Remove selected class from all options
            jenisBiayaOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            
            jenisBiayaDropdown.classList.add('hidden');
            updateJenisBiayaSelectedCount();
            
            // Trigger change event for existing logic
            const event = new Event('change', { bubbles: true });
            jenisBiayaHiddenInput.dispatchEvent(event);
        });
    });
    
    // Clear selection
    clearJenisBiayaBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        clearJenisBiayaSelection();
    });
    
    function clearJenisBiayaSelection() {
        selectedJenisBiaya = { kode: '', nama: '' };
        jenisBiayaHiddenInput.value = '';
        jenisBiayaSearch.value = '';
        jenisBiayaSearch.classList.remove('hidden');
        selectedJenisBiayaDisplay.classList.add('hidden');
        jenisBiayaOptions.forEach(opt => opt.classList.remove('selected'));
        updateJenisBiayaSelectedCount();
        
        // Trigger change event
        const event = new Event('change', { bubbles: true });
        jenisBiayaHiddenInput.dispatchEvent(event);
    }
    
    function updateJenisBiayaSelectedCount() {
        if (selectedJenisBiaya.kode) {
            jenisBiayaSelectedCount.textContent = 'Terpilih: ' + selectedJenisBiaya.nama;
        } else {
            jenisBiayaSelectedCount.textContent = 'Pilih jenis biaya';
        }
    }
    
    // Restore old value on page load (for validation errors)
    if (oldJenisBiayaValue) {
        const option = Array.from(jenisBiayaOptions).find(opt => opt.getAttribute('data-kode') === oldJenisBiayaValue);
        if (option) {
            option.click();
        }
    }
    
    // Declare all input elements at the top
    const nominalInput = document.getElementById('nominal');
    const jenisBiayaSelect = document.getElementById('jenis_biaya');
    const barangWrapper = document.getElementById('barang_wrapper');
    const oppOptWrapper = document.getElementById('opp_opt_wrapper');
    const addBarangBtn = document.getElementById('add_barang_btn');
    const ppnWrapper = document.getElementById('ppn_wrapper');
    const pphWrapper = document.getElementById('pph_wrapper');
    const totalBiayaWrapper = document.getElementById('total_biaya_wrapper');
    const ppnInput = document.getElementById('ppn');
    const pphInput = document.getElementById('pph');
    const totalBiayaInput = document.getElementById('total_biaya');
    const blWrapper = document.getElementById('bl_wrapper');
    const kapalWrapper = document.getElementById('kapal_wrapper');
    const voyageWrapper = document.getElementById('voyage_wrapper');
    const dpWrapper = document.getElementById('dp_wrapper');
    const sisaPembayaranWrapper = document.getElementById('sisa_pembayaran_wrapper');
    const dpInput = document.getElementById('dp');
    const sisaPembayaranInput = document.getElementById('sisa_pembayaran');
    const vendorWrapper = document.getElementById('vendor_wrapper');
    const vendorSelect = document.getElementById('vendor');
    const biayaMateraiWrapper = document.getElementById('biaya_materai_wrapper');
    const biayaMateraiInput = document.getElementById('biaya_materai');
    
    // Biaya Dokumen specific fields
    const pphDokumenWrapper = document.getElementById('pph_dokumen_wrapper');
    const grandTotalDokumenWrapper = document.getElementById('grand_total_dokumen_wrapper');
    const pphDokumenInput = document.getElementById('pph_dokumen');
    const grandTotalDokumenInput = document.getElementById('grand_total_dokumen');
    
    // Biaya Air specific fields
    const airWrapper = document.getElementById('air_wrapper');
    const vendorAirWrapper = document.getElementById('vendor_air_wrapper');
    const vendorAirSelect = document.getElementById('vendor_air');
    const typeAirWrapper = document.getElementById('type_air_wrapper');
    const typeAirInput = document.getElementById('type_air');
    const kuantitasAirWrapper = document.getElementById('kuantitas_air_wrapper');
    const kuantitasAirInput = document.getElementById('kuantitas_air');
    const operasionalWrapper = document.getElementById('operasional_wrapper');
    
    // Trucking specific fields
    const truckingWrapper = document.getElementById('trucking_wrapper');
    const truckingSectionsContainer = document.getElementById('trucking_sections_container');
    const addTruckingSectionBtn = document.getElementById('add_trucking_section_btn');
    const addTruckingSectionBottomBtn = document.getElementById('add_trucking_section_bottom_btn');

    // Stuffing specific fields
    const stuffingWrapper = document.getElementById('stuffing_wrapper');
    const stuffingSectionsContainer = document.getElementById('stuffing_sections_container');
    const addStuffingSectionBtn = document.getElementById('add_stuffing_section_btn');
    const addStuffingSectionBottomBtn = document.getElementById('add_stuffing_section_bottom_btn');

    // THC specific fields
    const thcWrapper = document.getElementById('thc_wrapper');
    const thcSectionsContainer = document.getElementById('thc_sections_container');
    const addThcSectionBtn = document.getElementById('add_thc_section_btn');
    const addThcSectionBottomBtn = document.getElementById('add_thc_section_bottom_btn');

    // Freight specific fields
    const freightWrapper = document.getElementById('freight_wrapper');
    const freightSectionsContainer = document.getElementById('freight_sections_container');
    const addFreightSectionBtn = document.getElementById('add_freight_section_btn');
    const addFreightSectionBottomBtn = document.getElementById('add_freight_section_bottom_btn');

    // LOLO specific fields
    const loloWrapper = document.getElementById('lolo_wrapper');
    const loloSectionsContainer = document.getElementById('lolo_sections_container');
    const addLoloSectionBtn = document.getElementById('add_lolo_section_btn');
    const addLoloSectionBottomBtn = document.getElementById('add_lolo_section_bottom_btn');

    // Biaya Perlengkapan multi-section
    const perlengkapanWrapper = document.getElementById('perlengkapan_wrapper');
    const perlengkapanSectionsContainer = document.getElementById('perlengkapan_sections_container');
    const addPerlengkapanSectionBtn = document.getElementById('add_perlengkapan_section_btn');
    const addPerlengkapanSectionBottomBtn = document.getElementById('add_perlengkapan_section_bottom_btn');

    // Biaya Perijinan multi-section
    const perijinanWrapper = document.getElementById('perijinan_wrapper');
    const perijinanSectionsContainer = document.getElementById('perijinan_sections_container');
    const addPerijinanSectionBtn = document.getElementById('add_perijinan_section_btn');
    const addPerijinanSectionBottomBtn = document.getElementById('add_perijinan_section_bottom_btn');

    // Tagihan Meratus multi-section
    const meratusWrapper = document.getElementById('meratus_wrapper');
    const meratusSectionsContainer = document.getElementById('meratus_sections_container');
    const addMeratusSectionBtn = document.getElementById('add_meratus_section_btn');
    
    // Standard field wrappers
    const nominalWrapper = document.getElementById('nominal_wrapper');
    const penerimaWrapper = document.getElementById('penerima_wrapper');
    const penerimaInput = document.getElementById('penerima');
    const namaVendorWrapper = document.getElementById('nama_vendor_wrapper');
    const nomorRekeningWrapper = document.getElementById('nomor_rekening_wrapper');
    const nomorReferensiWrapper = document.getElementById('nomor_referensi_wrapper');

    // Pricelist Air Tawar data
    const pricelistAirTawarData = {!! json_encode($pricelistAirTawar) !!};

    // Pricelist Labuh Tambat data
    const pricelistLabuhTambatData = {!! json_encode($pricelistLabuhTambat) !!};

    // Labuh Tambat specific fields
    const labuhTambatWrapper = document.getElementById('labuh_tambat_wrapper');
    const labuhTambatSectionsContainer = document.getElementById('labuh_tambat_sections_container');
    const addLabuhTambatSectionBtn = document.getElementById('add_labuh_tambat_section_btn');
    
    let labuhTambatSectionCounter = 0;

    // Pricelist Lolo data
    const pricelistLolosData = {!! json_encode($pricelistLolosData) !!};
    const pricelistLoloVendorsData = {!! json_encode($pricelistLoloVendors) !!};

    // Pricelist Storage data
    const pricelistStoragesData = {!! json_encode($pricelistStoragesData) !!};

    // Storage DOM elements
    const storageWrapper = document.getElementById('storage_wrapper');
    const storageSectionsContainer = document.getElementById('storage_sections_container');
    const addStorageSectionBtn = document.getElementById('add_storage_section_btn');
    const addStorageSectionBottomBtn = document.getElementById('add_storage_section_bottom_btn');

    // Format nominal input with thousand separator
    
    nominalInput.addEventListener('input', function(e) {
        // Remove all non-numeric characters
        let value = this.value.replace(/\D/g, '');
        
        // Format with thousand separator
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        
        this.value = value;
        
        // Recalculate based on jenis biaya
        const selectedText = selectedJenisBiaya.nama || '';
        if (selectedText.toLowerCase().includes('dokumen') || selectedText.toLowerCase().includes('listrik') || selectedText.toLowerCase().includes('trucking')) {
            calculatePphDokumen();
        } else if (selectedText.toLowerCase().includes('penumpukan')) {
            calculatePpnPenumpukan();
        } else if (selectedText.toLowerCase().includes('buruh')) {
            calculateSisaPembayaran();
        }
    });
    
    // Format DP input
    dpInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        this.value = value;
        calculateSisaPembayaran();
    });
    
    // Format PPN input
    ppnInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        this.value = value;
        calculateTotalBiaya();
    });
    
    // Format PPH input
    pphInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        this.value = value;
        calculateTotalBiaya();
    });
    
    // Format Biaya Materai input
    biayaMateraiInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        this.value = value;
        calculateTotalBiaya();
    });
    
    // Calculate Sisa Pembayaran = Nominal - DP (for Biaya Buruh)
    function calculateSisaPembayaran() {
        const nominal = parseInt(nominalInput.value.replace(/\D/g, '') || 0);
        const dp = parseInt(dpInput.value.replace(/\D/g, '') || 0);
        
        const sisa = nominal - dp;
        sisaPembayaranInput.value = sisa > 0 ? sisa.toLocaleString('id-ID') : '0';
    }
    
    // Calculate PPH Dokumen (2% dari nominal) and Grand Total (for Biaya Dokumen)
    function calculatePphDokumen() {
        const nominal = parseInt(nominalInput.value.replace(/\D/g, '') || 0);
        
        // PPH = 2% dari nominal
        const pph = Math.round(nominal * 0.02);
        pphDokumenInput.value = pph > 0 ? pph.toLocaleString('id-ID') : '0';
        
        // Grand Total = Nominal - PPH
        const grandTotal = nominal - pph;
        grandTotalDokumenInput.value = grandTotal > 0 ? grandTotal.toLocaleString('id-ID') : '0';
    }
    
    // Calculate PPH Penumpukan (2% dari nominal) for Biaya Penumpukan
    function calculatePphPenumpukan() {
        const nominal = parseInt(nominalInput.value.replace(/\D/g, '') || 0);
        
        // PPH = 2% dari nominal
        const pph = Math.round(nominal * 0.02);
        pphInput.value = pph > 0 ? pph.toLocaleString('id-ID') : '0';
        
        // Recalculate total biaya
        calculateTotalBiaya();
    }
    
    // Calculate PPN Penumpukan (11% dari nominal) for Biaya Penumpukan
    function calculatePpnPenumpukan() {
        const nominal = parseInt(nominalInput.value.replace(/\D/g, '') || 0);
        
        // PPN = 11% dari nominal
        const ppn = Math.round(nominal * 0.11);
        ppnInput.value = ppn > 0 ? ppn.toLocaleString('id-ID') : '0';
        
        // Auto-calculate PPH after PPN
        calculatePphPenumpukan();
    }
    
    // Calculate Total Biaya = Nominal + PPN + Materai - PPH
    function calculateTotalBiaya() {
        const nominal = parseInt(nominalInput.value.replace(/\D/g, '') || 0);
        const ppn = parseInt(ppnInput.value.replace(/\D/g, '') || 0);
        const pph = parseInt(pphInput.value.replace(/\D/g, '') || 0);
        const materai = parseInt(biayaMateraiInput.value.replace(/\D/g, '') || 0);
        
        const total = nominal + ppn + materai - pph;
        totalBiayaInput.value = total > 0 ? total.toLocaleString('id-ID') : '';
    }

    // Before form submit, remove formatting from all currency fields
    document.querySelector('form').addEventListener('submit', function(e) {
        // DEBUG: Log all kapal sections data before submit
        const sections = document.querySelectorAll('.kapal-section');
        console.log('=== FORM SUBMIT DEBUG ===');
        console.log('Total sections found:', sections.length);
        
        // Check for duplicate data-section-index
        const indexMap = {};
        sections.forEach((section, idx) => {
            const sectionIdx = section.getAttribute('data-section-index');
            if (indexMap[sectionIdx]) {
                console.error('DUPLICATE data-section-index found!', sectionIdx);
            }
            indexMap[sectionIdx] = true;
            
            const kapalSelect = section.querySelector('.kapal-select');
            const voyageSelect = section.querySelector('.voyage-select');
            const kapalInputName = kapalSelect ? kapalSelect.getAttribute('name') : 'N/A';
            const voyageInputName = voyageSelect ? voyageSelect.getAttribute('name') : 'N/A';
            console.log(`Section DOM[${idx}] data-section-index=${sectionIdx}:`, {
                kapal: kapalSelect ? kapalSelect.value : 'N/A',
                kapalInputName: kapalInputName,
                voyage: voyageSelect ? voyageSelect.value : 'N/A',
                voyageInputName: voyageInputName
            });
        });
        
        // Check all kapal_sections inputs
        console.log('=== ALL KAPAL_SECTIONS INPUTS ===');
        document.querySelectorAll('[name^="kapal_sections"]').forEach(input => {
            console.log(input.name, '=', input.value);
        });
        console.log('=========================');
        
        nominalInput.value = nominalInput.value.replace(/\./g, '');
        ppnInput.value = ppnInput.value.replace(/\./g, '');
        pphInput.value = pphInput.value.replace(/\./g, '');
        if (dpInput.value) {
            dpInput.value = dpInput.value.replace(/\./g, '');
        }
        if (sisaPembayaranInput.value) {
            sisaPembayaranInput.value = sisaPembayaranInput.value.replace(/\./g, '');
        }
        if (totalBiayaInput.value) {
            totalBiayaInput.value = totalBiayaInput.value.replace(/\./g, '');
        }
        // Clean Biaya Dokumen fields
        if (pphDokumenInput && pphDokumenInput.value) {
            pphDokumenInput.value = pphDokumenInput.value.replace(/\./g, '');
        }
        if (grandTotalDokumenInput && grandTotalDokumenInput.value) {
            grandTotalDokumenInput.value = grandTotalDokumenInput.value.replace(/\./g, '');
        }
        // Clean Biaya Materai field
        if (biayaMateraiInput && biayaMateraiInput.value) {
            biayaMateraiInput.value = biayaMateraiInput.value.replace(/\./g, '');
        }
        // Clean Biaya Air fields
        if (jasaAirInput && jasaAirInput.value) {
            jasaAirInput.value = jasaAirInput.value.replace(/\./g, '');
        }
        if (pphAirInput && pphAirInput.value) {
            pphAirInput.value = pphAirInput.value.replace(/\./g, '');
        }
        if (grandTotalAirInput && grandTotalAirInput.value) {
            grandTotalAirInput.value = grandTotalAirInput.value.replace(/\./g, '');
        }
        // Clean Biaya Storage fields
        document.querySelectorAll('[name^="storage_sections"]').forEach(input => {
            if (input.name.includes('[subtotal]') || input.name.includes('[biaya_materai]') || 
                input.name.includes('[ppn]') || input.name.includes('[pph]') || 
                input.name.includes('[total_biaya]')) {
                input.value = input.value.replace(/\./g, '');
            }
        });

        // Sanitize per-section numeric hidden inputs to ensure validation accepts numbers
        document.querySelectorAll('.sub-total-value').forEach(el => {
            el.value = String(el.value).replace(/\./g, '').replace(/[^0-9\-]/g, '');
        });
        document.querySelectorAll('.pph-value').forEach(el => {
            el.value = String(el.value).replace(/\./g, '').replace(/[^0-9\-]/g, '');
        });
        document.querySelectorAll('.grand-total-value').forEach(el => {
            el.value = String(el.value).replace(/\./g, '').replace(/[^0-9\-]/g, '');
        });
    });

    // Update file name display
    function updateFileName(input) {
        const fileNameDisplay = document.getElementById('file-name');
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2); // Convert to MB
            fileNameDisplay.innerHTML = `<i class="fas fa-file-alt mr-2 text-blue-600"></i><span class="font-medium">File terpilih:</span> ${fileName} (${fileSize} MB)`;
        } else {
            fileNameDisplay.innerHTML = '';
        }
    }

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.bg-red-50');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

    // Function to calculate nominal for Biaya Dokumen (vendor tariff × number of containers)
    function calculateDokumenNominal() {
        const currentJenisBiaya = selectedJenisBiaya.nama || '';
        
        // Only calculate if jenis biaya is "Biaya Dokumen"
        if (!currentJenisBiaya.toLowerCase().includes('dokumen')) {
            return;
        }
        
        const selectedOption = vendorSelect.options[vendorSelect.selectedIndex];
        const biaya = selectedOption.getAttribute('data-biaya');
        const jumlahKontainer = Object.keys(selectedBls).length;
        
        console.log('Calculating Dokumen Nominal:');
        console.log('- Tarif vendor:', biaya);
        console.log('- Jumlah kontainer:', jumlahKontainer);
        
        if (biaya && biaya !== '' && biaya !== '0' && jumlahKontainer > 0) {
            const totalNominal = parseInt(biaya) * jumlahKontainer;
            const formattedNominal = totalNominal.toLocaleString('id-ID');
            nominalInput.value = formattedNominal;
            console.log('- Total nominal:', formattedNominal);
            
            // Calculate PPH and Grand Total after nominal is updated
            calculatePphDokumen();
        } else if (biaya && biaya !== '' && biaya !== '0') {
            // If vendor selected but no containers yet, show vendor tariff
            const formattedBiaya = parseInt(biaya).toLocaleString('id-ID');
            nominalInput.value = formattedBiaya;
            console.log('- No containers selected, showing vendor tariff:', formattedBiaya);
            
            // Calculate PPH and Grand Total after nominal is updated
            calculatePphDokumen();
        } else {
            nominalInput.value = '';
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
        }
    }

    // Auto-fill nominal from vendor selection
    vendorSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const biaya = selectedOption.getAttribute('data-biaya');
        
        console.log('Vendor selected:', this.value);
        console.log('Biaya from vendor:', biaya);
        
        const currentJenisBiaya = selectedJenisBiaya.nama || '';
        
        // If Biaya Dokumen, use the calculate function
        if (currentJenisBiaya.toLowerCase().includes('dokumen')) {
            calculateDokumenNominal();
        } else {
            // For other jenis biaya, use original logic
            if (biaya && biaya !== '' && biaya !== '0') {
                // Format biaya with thousand separator
                const formattedBiaya = parseInt(biaya).toLocaleString('id-ID');
                nominalInput.value = formattedBiaya;
                nominalInput.focus();
                
                console.log('Nominal set to:', formattedBiaya);
            } else {
                // Clear nominal if no vendor selected or biaya is 0
                nominalInput.value = '';
            }
        }
    });
