
    // ============= JENIS BIAYA SEARCHABLE DROPDOWN =============
    var jenisBiayaSearch = document.getElementById('jenis_biaya_search');
    var jenisBiayaContainer = document.getElementById('jenis_biaya_container');
    var jenisBiayaDropdown = document.getElementById('jenis_biaya_dropdown');
    var jenisBiayaHiddenInput = document.getElementById('jenis_biaya');
    var selectedJenisBiayaDisplay = document.getElementById('selected_jenis_biaya_display');
    var jenisBiayaOptions = document.querySelectorAll('.jenis-biaya-option');
    var clearJenisBiayaBtn = document.getElementById('clearJenisBiayaBtn');
    var jenisBiayaSelectedCount = document.getElementById('jenisBiayaSelectedCount');
    
    var selectedJenisBiaya = { kode: '', nama: '' };
    var oldJenisBiayaValue = "{{ old('jenis_biaya') }}";
    
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
    var nominalInput = document.getElementById('nominal');
    var jenisBiayaSelect = document.getElementById('jenis_biaya');
    var barangWrapper = document.getElementById('barang_wrapper');
    var addBarangBtn = document.getElementById('add_barang_btn');
    var ppnWrapper = document.getElementById('ppn_wrapper');
    var pphWrapper = document.getElementById('pph_wrapper');
    var totalBiayaWrapper = document.getElementById('total_biaya_wrapper');
    var ppnInput = document.getElementById('ppn');
    var pphInput = document.getElementById('pph');
    var totalBiayaInput = document.getElementById('total_biaya');
    var blWrapper = document.getElementById('bl_wrapper');
    var kapalWrapper = document.getElementById('kapal_wrapper');
    var voyageWrapper = document.getElementById('voyage_wrapper');
    var dpWrapper = document.getElementById('dp_wrapper');
    var sisaPembayaranWrapper = document.getElementById('sisa_pembayaran_wrapper');
    var dpInput = document.getElementById('dp');
    var sisaPembayaranInput = document.getElementById('sisa_pembayaran');
    var vendorWrapper = document.getElementById('vendor_wrapper');
    var vendorSelect = document.getElementById('vendor');
    var biayaMateraiWrapper = document.getElementById('biaya_materai_wrapper');
    var biayaMateraiInput = document.getElementById('biaya_materai');
    
    // Biaya Dokumen specific fields
    var pphDokumenWrapper = document.getElementById('pph_dokumen_wrapper');
    var grandTotalDokumenWrapper = document.getElementById('grand_total_dokumen_wrapper');
    var pphDokumenInput = document.getElementById('pph_dokumen');
    var grandTotalDokumenInput = document.getElementById('grand_total_dokumen');
    
    // Biaya Air specific fields
    var airWrapper = document.getElementById('air_wrapper');
    var vendorAirWrapper = document.getElementById('vendor_air_wrapper');
    var vendorAirSelect = document.getElementById('vendor_air');
    var typeAirWrapper = document.getElementById('type_air_wrapper');
    var typeAirInput = document.getElementById('type_air');
    var kuantitasAirWrapper = document.getElementById('kuantitas_air_wrapper');
    var kuantitasAirInput = document.getElementById('kuantitas_air');
    var operasionalWrapper = document.getElementById('operasional_wrapper');
    var jasaAirInput = document.getElementById('jasa_air');
    var pphAirInput = document.getElementById('pph_air');
    var grandTotalAirInput = document.getElementById('grand_total_air');

    
    // Standard field wrappers
    var nominalWrapper = document.getElementById('nominal_wrapper');
    var penerimaWrapper = document.getElementById('penerima_wrapper');
    var penerimaInput = document.getElementById('penerima');
    var namaVendorWrapper = document.getElementById('nama_vendor_wrapper');
    var nomorRekeningWrapper = document.getElementById('nomor_rekening_wrapper');
    var nomorReferensiWrapper = document.getElementById('nomor_referensi_wrapper');
    
    // Stuffing specific fields
    const stuffingWrapper = document.getElementById('stuffing_wrapper');
    const stuffingSectionsContainer = document.getElementById('stuffing_sections_container');
    const addStuffingSectionBtn = document.getElementById('add_stuffing_section_btn');
    const addStuffingSectionBottomBtn = document.getElementById('add_stuffing_section_bottom_btn');

    // Store all data for dynamic sections
    var pricelistBuruhData = {!! json_encode($pricelistBuruh) !!};
    var allKapalsData = {!! json_encode($kapals) !!};


    // Pricelist Air Tawar data
    var pricelistAirTawarData = {!! json_encode($pricelistAirTawar) !!};

    // Pricelist TKBM data
    var pricelistTkbmData = {!! json_encode($pricelistTkbm) !!};

    // Pricelist Trucking data
    var pricelistBiayaTruckingData = {!! json_encode($pricelistBiayaTrucking) !!};

    // Pricelist Labuh Tambat data
    var pricelistLabuhTambatData = {!! json_encode($pricelistLabuhTambat) !!};

    // Pricelist OPP/OPT data
    var pricelistOppOptData = {!! json_encode($pricelistOppOpt) !!};

    // THC Data
    const pricelistThcVendorsData = {!! json_encode($pricelistThcVendors) !!};
    const pricelistThcData = {!! json_encode($pricelistThcs) !!};

    // LOLO Data
    const pricelistLolosData = {!! json_encode($pricelistLolosData) !!};
    const pricelistStoragesData = {!! json_encode($pricelistStoragesData) !!};
    
    // Select containers and elements for Storage
    const storageWrapper = document.getElementById('storage_wrapper');
    const storageSectionsContainer = document.getElementById('storage_sections_container');
    const addStorageSectionBtn = document.getElementById('add_storage_section_btn');
    const addStorageSectionBottomBtn = document.getElementById('add_storage_section_bottom_btn');
    const pricelistLoloVendorsData = {!! json_encode($pricelistLoloVendors) !!};

    // THC Section Logic
    const addThcSectionBtn = document.getElementById('add_thc_section_btn');
    const addThcSectionBottomBtn = document.getElementById('add_thc_section_bottom_btn');
    let thcSectionCounter = 0;

    // LOLO specific fields
    const loloWrapper = document.getElementById('lolo_wrapper');
    const loloSectionsContainer = document.getElementById('lolo_sections_container');
    const addLoloSectionBtn = document.getElementById('add_lolo_section_btn');
    const addLoloSectionBottomBtn = document.getElementById('add_lolo_section_bottom_btn');
    let loloSectionCounter = 0;

    // Format nominal input with thousand separator
