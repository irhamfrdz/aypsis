    // ============= NEW KAPAL SECTIONS MANAGEMENT =============
    let kapalSectionCounter = 0;
    const kapalSectionsContainer = document.getElementById('kapal_sections_container');
    const addKapalSectionBtn = document.getElementById('add_kapal_section_btn');
    const allBuruhsData = @json($allBuruhs ?? []);

    let currentLokasi = 'jakarta';
    const lokasiBuruhSelect = document.getElementById('lokasi_buruh_select');
    const inputLokasiHidden = document.getElementById('input_lokasi_hidden');

    if (lokasiBuruhSelect) {
        lokasiBuruhSelect.addEventListener('change', function() {
            currentLokasi = this.value;
            inputLokasiHidden.value = this.value;
            
            // Re-render semua section dengan mode baru
            initializeKapalSections();
        });
    }

    function initializeKapalSections() {
        if (!kapalSectionsContainer) return;
        kapalSectionsContainer.innerHTML = '';
        kapalSectionCounter = 0;
        addKapalSection();
    }
    
    function clearAllKapalSections() {
        kapalSectionsContainer.innerHTML = '';
        kapalSectionCounter = 0;
        nominalInput.value = '';
    }
    
    addKapalSectionBtn.addEventListener('click', function() {
        addKapalSection();
    });
    
    function addKapalSection() {
        kapalSectionCounter++;
        const sectionIndex = kapalSectionCounter;
        
        const section = document.createElement('div');
        section.className = 'kapal-section mb-6 p-4 border-2 border-blue-200 rounded-lg bg-blue-50';
        section.setAttribute('data-section-index', sectionIndex);
        
        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        allKapalsData.forEach(kapal => {
            kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
        });

        if (currentLokasi === 'batam') {
            // ========================================
            // LAYOUT BATAM (Kontainer/BL + Nominal Manual)
            // ========================================
            section.innerHTML = `
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex} <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded ml-2">Mode Batam</span></h3>
                    ${sectionIndex > 1 ? `<button type="button" onclick="removeKapalSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                        <select name="kapal_sections[${sectionIndex}][kapal]" class="kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500" required>
                            ${kapalOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <select name="kapal_sections[${sectionIndex}][voyage]" class="voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500" required disabled>
                                <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                            </select>
                            <input type="text" name="kapal_sections[${sectionIndex}][voyage]" class="voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 hidden" disabled placeholder="Ketik No. Voyage">
                            <button type="button" class="voyage-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                                <i class="fas fa-keyboard"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Kontainer Selection -->
                <div class="mb-3 p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                    <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wider">Detail Kontainer <span class="text-xs text-gray-500 font-normal normal-case">(Opsional)</span></label>
                    <div class="kontainer-loading hidden py-2 text-center text-gray-500 text-xs italic"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat kontainer...</div>
                    <div class="kontainer-empty hidden py-2 text-center text-red-500 text-xs italic">Tidak ada kontainer untuk voyage ini</div>
                    <div class="kontainer-container-section" data-section="${sectionIndex}"></div>
                    <div class="mt-2 flex items-center gap-2">
                        <button type="button" onclick="addKontainerToSection(${sectionIndex})" class="btn-tambah-kontainer px-3 py-1.5 bg-indigo-500 hover:bg-indigo-600 text-white text-xs rounded-lg transition shadow-sm" disabled>
                            <i class="fas fa-plus mr-1"></i> Tambah Kontainer
                        </button>
                        <span class="info-kontainer text-xs text-gray-500"><i class="fas fa-info-circle mr-1"></i>Pilih No. Voyage terlebih dahulu</span>
                    </div>
                </div>

                <!-- Nominal Per Kapal Display -->
                <div class="mt-3 p-3 bg-white border border-blue-300 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nominal <span class="text-red-500">*</span></label>
                            <input type="text" name="kapal_sections[${sectionIndex}][nominal_manual]" class="nominal-manual-input w-full px-3 py-2 border border-indigo-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 text-sm font-semibold" placeholder="0" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Adjustment</label>
                            <input type="text" name="kapal_sections[${sectionIndex}][adjustment]" class="adjustment-input w-full px-3 py-2 border border-blue-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 text-sm" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Catatan Adjustment</label>
                            <input type="text" name="kapal_sections[${sectionIndex}][notes_adjustment]" class="w-full px-3 py-2 border border-blue-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 text-sm" placeholder="Keterangan adjustment">
                        </div>
                    </div>
                    
                    <!-- Detail Pembayaran Tambahan (Batam) -->
                    <div class="mt-4 pt-3 border-t border-gray-200">
                        <h4 class="text-xs font-bold text-gray-700 mb-2 uppercase">Detail Pembayaran & Dokumen</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Bukti</label>
                                <input type="text" name="kapal_sections[${sectionIndex}][nomor_bukti]" class="nomor-bukti-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm" placeholder="Nomor Bukti">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Vendor (Ketik Manual)</label>
                                <input type="text" name="kapal_sections[${sectionIndex}][nama_vendor]" class="nama-vendor-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm" placeholder="Nama Vendor">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Penerima</label>
                                <select name="kapal_sections[${sectionIndex}][penerima]" class="penerima-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm">
                                    <option value="">-- Pilih Penerima --</option>
                                    @foreach($karyawans as $karyawan)
                                        <option value="{{ $karyawan->nama_lengkap }}">{{ $karyawan->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Bank</label>
                                <select name="kapal_sections[${sectionIndex}][bank_id]" class="bank-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm">
                                    <option value="">-- Pilih Bank --</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Rekening</label>
                                <input type="text" name="kapal_sections[${sectionIndex}][nomor_rekening]" class="nomor-rekening-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm" placeholder="Nomor Rekening">
                            </div>
                        </div>
                        </div>
                    </div>
                    
                    <!-- PPh Section -->
                    <div class="mt-4 pt-3 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">PPh</label>
                                <select name="kapal_sections[${sectionIndex}][pph_percent]" class="pph-percent-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm">
                                    <option value="0">0%</option>
                                    <option value="0.5">0.5%</option>
                                    <option value="2">2%</option>
                                    <option value="2.5">2.5%</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Nominal PPh</label>
                                <input type="text" name="kapal_sections[${sectionIndex}][pph_amount]" class="pph-amount-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm bg-gray-100" placeholder="0" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center border-t border-blue-100 pt-2 mt-2">
                        <span class="text-sm font-semibold text-gray-700">Total Nominal Kapal ${sectionIndex}:</span>
                        <span class="section-nominal-display text-lg font-bold text-blue-600">Rp 0</span>
                    </div>
                </div>
            `;
        } else {
            // ========================================
            // LAYOUT JAKARTA (Default Lama)
            // ========================================
            section.innerHTML = `
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex}</h3>
                    ${sectionIndex > 1 ? `<button type="button" onclick="removeKapalSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
                </div>
                
                <!-- Hidden inputs for section totals -->
                <input type="hidden" name="kapal_sections[${sectionIndex}][total_nominal]" class="section-total-hidden" value="0">
                <input type="hidden" name="kapal_sections[${sectionIndex}][dp]" class="section-dp-hidden" value="0">
                <input type="hidden" name="kapal_sections[${sectionIndex}][sisa_pembayaran]" class="section-sisa-hidden" value="0">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                        <select name="kapal_sections[${sectionIndex}][kapal]" class="kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500" required>
                            ${kapalOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <select name="kapal_sections[${sectionIndex}][voyage]" class="voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500" required disabled>
                                <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                            </select>
                            <input type="text" name="kapal_sections[${sectionIndex}][voyage]" class="voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 hidden" disabled placeholder="Ketik No. Voyage">
                            <button type="button" class="voyage-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                                <i class="fas fa-keyboard"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-700 mb-2">Detail Barang</label>
                    <div class="barang-container-section" data-section="${sectionIndex}"></div>
                    <button type="button" onclick="addBarangToSection(${sectionIndex})" class="mt-2 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded-lg transition">
                        <i class="fas fa-plus mr-1"></i> Tambah Barang
                    </button>
                </div>
                
                <div class="mb-3 p-3 bg-gray-100 border border-gray-200 rounded-lg">
                    <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wider">Tenaga Kerja / Buruh</label>
                    <div class="buruh-container-section" data-section="${sectionIndex}"></div>
                    <div class="flex gap-2 mt-2">
                        <button type="button" onclick="addBuruhToSection(${sectionIndex})" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs rounded-lg transition shadow-sm">
                            <i class="fas fa-user-plus mr-1"></i> Tambah Buruh
                        </button>
                        <button type="button" onclick="randomizeBuruhForSection(${sectionIndex})" class="px-3 py-1.5 bg-purple-500 hover:bg-purple-600 text-white text-xs rounded-lg transition shadow-sm" title="Pilih buruh & nominal secara acak">
                            <i class="fas fa-dice mr-1"></i> Randomize Buruh
                        </button>
                    </div>
                </div>
                
                <!-- Nominal Per Kapal Display -->
                <div class="mt-3 p-3 bg-white border border-blue-300 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Adjustment</label>
                            <input type="text" name="kapal_sections[${sectionIndex}][adjustment]" class="adjustment-input w-full px-3 py-2 border border-blue-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 text-sm" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Catatan Adjustment</label>
                            <input type="text" name="kapal_sections[${sectionIndex}][notes_adjustment]" class="w-full px-3 py-2 border border-blue-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 text-sm" placeholder="Keterangan adjustment">
                        </div>
                    </div>

                    <!-- Detail Pembayaran Tambahan (Jakarta) -->
                    <div class="mt-4 pt-3 border-t border-gray-200">
                        <h4 class="text-xs font-bold text-gray-700 mb-2 uppercase">Detail Pembayaran & Dokumen</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Vendor (Ketik Manual)</label>
                                <input type="text" name="kapal_sections[${sectionIndex}][nama_vendor]" class="nama-vendor-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm" placeholder="Nama Vendor">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Penerima</label>
                                <select name="kapal_sections[${sectionIndex}][penerima]" class="penerima-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm">
                                    <option value="">-- Pilih Penerima --</option>
                                    @foreach($karyawans as $karyawan)
                                        <option value="{{ $karyawan->nama_lengkap }}">{{ $karyawan->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Bank</label>
                                <select name="kapal_sections[${sectionIndex}][bank_id]" class="bank-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm">
                                    <option value="">-- Pilih Bank --</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Rekening</label>
                                <input type="text" name="kapal_sections[${sectionIndex}][nomor_rekening]" class="nomor-rekening-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm" placeholder="Nomor Rekening">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center border-t border-blue-100 pt-2 mt-2">
                        <span class="text-sm font-semibold text-gray-700">Nominal Kapal ${sectionIndex}:</span>
                        <span class="section-nominal-display text-lg font-bold text-blue-600">Rp 0</span>
                    </div>
                </div>
            `;
        }
        
        kapalSectionsContainer.appendChild(section);
        
        // Setup kapal change listener - use data attribute to get correct section
        const kapalSelect = section.querySelector('.kapal-select');
        const voyageSelect = section.querySelector('.voyage-select');
        
        kapalSelect.addEventListener('change', function() {
            const currentSection = this.closest('.kapal-section');
            const currentIndex = parseInt(currentSection.getAttribute('data-section-index'));
            console.log('Kapal changed in section:', currentIndex, 'Value:', this.value);
            loadVoyagesForSection(currentIndex, this.value);
        });
        
        voyageSelect.addEventListener('change', function() {
            const currentSection = this.closest('.kapal-section');
            const currentIndex = parseInt(currentSection.getAttribute('data-section-index'));
            const currentKapalSelect = currentSection.querySelector('.kapal-select');
            const kapalNama = currentKapalSelect.value;
            const voyageValue = this.value;
            console.log('Voyage changed in section:', currentIndex, 'Kapal:', kapalNama, 'Voyage:', voyageValue);
            if (kapalNama && voyageValue) {
                if (currentLokasi === 'batam') {
                    loadContainersForBuruhSection(currentSection, currentIndex, voyageValue);
                } else {
                    autoFillBarangForSection(currentIndex, kapalNama, voyageValue);
                }
            }
        });

        // Setup manual voyage toggle
        const voyageInput = section.querySelector('.voyage-input');
        const voyageManualBtn = section.querySelector('.voyage-manual-btn');
        const adjustmentInput = section.querySelector('.adjustment-input');
        const nominalManualInput = section.querySelector('.nominal-manual-input');
        const pphPercentSelect = section.querySelector('.pph-percent-select');

        if (pphPercentSelect) {
            pphPercentSelect.addEventListener('change', function() {
                calculateTotalFromAllSections();
            });
        }

        if (adjustmentInput) {
            adjustmentInput.addEventListener('input', function() {
                // Apply currency formatting
                let val = this.value.replace(/\./g, '');
                if (!isNaN(val) && val !== '') {
                    this.value = Math.round(val).toLocaleString('id-ID');
                }
                calculateTotalFromAllSections();
            });
        }
        
        if (nominalManualInput) {
            nominalManualInput.addEventListener('input', function() {
                // Apply currency formatting
                let val = this.value.replace(/\./g, '');
                if (!isNaN(val) && val !== '') {
                    this.value = Math.round(val).toLocaleString('id-ID');
                }
                calculateTotalFromAllSections();
            });
        }

        voyageManualBtn.addEventListener('click', function() {
            if (voyageInput.classList.contains('hidden')) {
                // Switch to manual input
                voyageSelect.classList.add('hidden');
                voyageSelect.disabled = true;
                
                voyageInput.classList.remove('hidden');
                voyageInput.disabled = false;
                voyageInput.focus();
                
                this.classList.remove('bg-gray-200', 'text-gray-600');
                this.classList.add('bg-blue-200', 'text-blue-700');
                this.innerHTML = '<i class="fas fa-list"></i>';
            } else {
                // Switch to select list
                voyageInput.classList.add('hidden');
                voyageInput.disabled = true;
                
                voyageSelect.classList.remove('hidden');
                voyageSelect.disabled = false;
                
                this.classList.add('bg-gray-200', 'text-gray-600');
                this.classList.remove('bg-blue-200', 'text-blue-700');
                this.innerHTML = '<i class="fas fa-keyboard"></i>';
            }
        });
        
        // --- AUTO-COPY PAYMENT DETAILS FROM KAPAL 1 ---
        const namaVendorInput = section.querySelector('.nama-vendor-input');
        const penerimaSelect = section.querySelector('.penerima-select');
        const bankSelect = section.querySelector('.bank-select');
        const nomorRekeningInput = section.querySelector('.nomor-rekening-input');

        // Copy values from Kapal 1 if this is a new section (> 1)
        if (sectionIndex > 1) {
            const firstSection = document.querySelector('[data-section-index="1"]');
            if (firstSection) {
                const firstVendor = firstSection.querySelector('.nama-vendor-input');
                const firstPenerima = firstSection.querySelector('.penerima-select');
                const firstBank = firstSection.querySelector('.bank-select');
                const firstRekening = firstSection.querySelector('.nomor-rekening-input');

                if (namaVendorInput && firstVendor && firstVendor.value) namaVendorInput.value = firstVendor.value;
                if (penerimaSelect && firstPenerima && firstPenerima.value) penerimaSelect.value = firstPenerima.value;
                if (bankSelect && firstBank && firstBank.value) bankSelect.value = firstBank.value;
                if (nomorRekeningInput && firstRekening && firstRekening.value) nomorRekeningInput.value = firstRekening.value;
            }
        }

        // Auto-update other sections when Kapal 1 changes
        if (sectionIndex === 1) {
            const updateOtherSections = (selector, value) => {
                document.querySelectorAll('.kapal-section').forEach(sec => {
                    const idx = parseInt(sec.getAttribute('data-section-index'));
                    if (idx > 1) {
                        const input = sec.querySelector(selector);
                        if (input) input.value = value;
                    }
                });
            };

            if (namaVendorInput) {
                namaVendorInput.addEventListener('input', function() {
                    updateOtherSections('.nama-vendor-input', this.value);
                });
            }
            if (penerimaSelect) {
                penerimaSelect.addEventListener('change', function() {
                    updateOtherSections('.penerima-select', this.value);
                });
            }
            if (bankSelect) {
                bankSelect.addEventListener('change', function() {
                    updateOtherSections('.bank-select', this.value);
                });
            }
            if (nomorRekeningInput) {
                nomorRekeningInput.addEventListener('input', function() {
                    updateOtherSections('.nomor-rekening-input', this.value);
                });
            }
        }
        
        // Add first barang input for Jakarta
        if (currentLokasi !== 'batam') {
            addBarangToSection(sectionIndex);
        }
        
        return section;
    }
    
    // Auto-fill barang based on container counts from BL table
    function autoFillBarangForSection(sectionIndex, kapalNama, voyage) {
        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.barang-container-section');
        
        // Show loading
        container.innerHTML = '<div class="text-sm text-gray-500 italic py-2"><i class="fas fa-spinner fa-spin mr-2"></i>Menghitung kontainer...</div>';
        
        fetch('{{ url("biaya-kapal/get-container-counts") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                kapal: kapalNama,
                voyage: voyage
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.counts) {
                container.innerHTML = '';
                let barangAdded = false;
                
                // Pricelist IDs mapping
                const pricelistIds = {
                    '20_full': null,
                    '20_empty': null,
                    '40_full': null,
                    '40_empty': null,
                    'cargo': null
                };
                
                // Find pricelist IDs from pricelistBuruhData
                pricelistBuruhData.forEach(p => {
                    const barangLower = (p.barang || '').toLowerCase();
                    if (barangLower.includes('kontainer') && barangLower.includes('20') && barangLower.includes('full')) {
                        pricelistIds['20_full'] = p.id;
                    } else if (barangLower.includes('kontainer') && barangLower.includes('20') && barangLower.includes('empty')) {
                        pricelistIds['20_empty'] = p.id;
                    } else if (barangLower.includes('kontainer') && barangLower.includes('40') && barangLower.includes('full')) {
                        pricelistIds['40_full'] = p.id;
                    } else if (barangLower.includes('kontainer') && barangLower.includes('40') && barangLower.includes('empty')) {
                        pricelistIds['40_empty'] = p.id;
                    } else if (barangLower === 'cargo') {
                        pricelistIds['cargo'] = p.id;
                    }
                });
                
                // Add 20' FULL if count > 0
                if (data.counts['20'] && data.counts['20'].full > 0 && pricelistIds['20_full']) {
                    addBarangToSectionWithValue(sectionIndex, pricelistIds['20_full'], data.counts['20'].full);
                    barangAdded = true;
                }
                
                // Add 20' EMPTY if count > 0
                if (data.counts['20'] && data.counts['20'].empty > 0 && pricelistIds['20_empty']) {
                    addBarangToSectionWithValue(sectionIndex, pricelistIds['20_empty'], data.counts['20'].empty);
                    barangAdded = true;
                }
                
                // Add 40' FULL if count > 0
                if (data.counts['40'] && data.counts['40'].full > 0 && pricelistIds['40_full']) {
                    addBarangToSectionWithValue(sectionIndex, pricelistIds['40_full'], data.counts['40'].full);
                    barangAdded = true;
                }
                
                // Add 40' EMPTY if count > 0
                if (data.counts['40'] && data.counts['40'].empty > 0 && pricelistIds['40_empty']) {
                    addBarangToSectionWithValue(sectionIndex, pricelistIds['40_empty'], data.counts['40'].empty);
                    barangAdded = true;
                }

                // Add CARGO if count > 0
                if (data.counts['cargo_max_tv_sum'] > 0 && pricelistIds['cargo']) {
                    addBarangToSectionWithValue(sectionIndex, pricelistIds['cargo'], data.counts['cargo_max_tv_sum']);
                    barangAdded = true;
                }
                
                // If no containers found, add empty barang input
                if (!barangAdded) {
                    addBarangToSection(sectionIndex);
                }
                
                // Recalculate total
                calculateTotalFromAllSections();
            } else {
                // Fallback to empty input
                container.innerHTML = '';
                addBarangToSection(sectionIndex);
            }
        })
        .catch(error => {
            console.error('Error fetching container counts:', error);
            container.innerHTML = '';
            addBarangToSection(sectionIndex);
        });
    }
    
    // Add barang to section with pre-filled values
    window.addBarangToSectionWithValue = function(sectionIndex, barangId, jumlah) {
        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.barang-container-section');
        const barangIndex = container.children.length;
        
        let barangOptions = '<option value="">Pilih Nama Barang</option>';
        pricelistBuruhData.forEach(pricelist => {
            const selected = pricelist.id == barangId ? 'selected' : '';
            barangOptions += `<option value="${pricelist.id}" data-tarif="${pricelist.tarif}" ${selected}>${pricelist.barang}</option>`;
        });
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2';
        inputGroup.innerHTML = `
            <div class="flex-1">
                <select name="kapal_sections[${sectionIndex}][barang][${barangIndex}][barang_id]" class="barang-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" required>
                    ${barangOptions}
                </select>
            </div>
            <div class="w-24">
                <input type="number" step="any" name="kapal_sections[${sectionIndex}][barang][${barangIndex}][jumlah]" value="${jumlah}" class="jumlah-input-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" placeholder="0" required>
            </div>
            <button type="button" onclick="removeBarangFromSection(this)" class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition">
                <i class="fas fa-trash text-xs"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        // Add event listeners
        const barangSelect = inputGroup.querySelector('.barang-select-item');
        const jumlahInput = inputGroup.querySelector('.jumlah-input-item');
        
        barangSelect.addEventListener('change', function() {
            calculateTotalFromAllSections();
        });
        
        jumlahInput.addEventListener('input', function() {
            calculateTotalFromAllSections();
        });
    };
    
    window.removeKapalSection = function(sectionIndex) {
        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            calculateTotalFromAllSections();
        }
    };
    
    function loadVoyagesForSection(sectionIndex, kapalNama) {
        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        const voyageSelect = section.querySelector('.voyage-select');
        
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
                console.log('Voyages response for', kapalNama, data);
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
                console.error('Error fetching voyages:', error);
                voyageSelect.innerHTML = '<option value="">Gagal memuat voyages</option>';
            });
    }
    
    window.addBarangToSection = function(sectionIndex) {
        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.barang-container-section');
        const barangIndex = container.children.length;
        
        let barangOptions = '<option value="">Pilih Nama Barang</option>';
        pricelistBuruhData.forEach(pricelist => {
            barangOptions += `<option value="${pricelist.id}" data-tarif="${pricelist.tarif}">${pricelist.barang}</option>`;
        });
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2';
        inputGroup.innerHTML = `
            <div class="flex-1">
                <select name="kapal_sections[${sectionIndex}][barang][${barangIndex}][barang_id]" class="barang-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" required>
                    ${barangOptions}
                </select>
            </div>
            <div class="w-24">
                <input type="number" step="any" name="kapal_sections[${sectionIndex}][barang][${barangIndex}][jumlah]" class="jumlah-input-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" placeholder="0" required>
            </div>
            <button type="button" onclick="removeBarangFromSection(this)" class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition">
                <i class="fas fa-trash text-xs"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        // Add event listeners
        const barangSelect = inputGroup.querySelector('.barang-select-item');
        const jumlahInput = inputGroup.querySelector('.jumlah-input-item');
        
        barangSelect.addEventListener('change', function() {
            calculateTotalFromAllSections();
        });
        
        jumlahInput.addEventListener('input', function() {
            calculateTotalFromAllSections();
        });
    };
    
    window.addBuruhToSection = function(sectionIndex) {
        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.buruh-container-section');
        const buruhIndex = container.children.length;
        
        let buruhOptions = '<option value="">Pilih Nama Buruh</option>';
        allBuruhsData.forEach(buruh => {
            buruhOptions += `<option value="${buruh.id}">${buruh.nama} ${buruh.nik ? '('+buruh.nik+')' : ''}</option>`;
        });
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2';
        inputGroup.innerHTML = `
            <div class="flex-1">
                <select name="kapal_sections[${sectionIndex}][tenaga_kerja][${buruhIndex}][buruh_id]" class="buruh-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-emerald-500" required>
                    ${buruhOptions}
                </select>
            </div>
            <div class="w-32">
                <input type="text" name="kapal_sections[${sectionIndex}][tenaga_kerja][${buruhIndex}][nominal]" class="buruh-nominal-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-emerald-500" placeholder="Rp 0" required>
            </div>
            <button type="button" onclick="removeBuruhFromSection(this)" class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition">
                <i class="fas fa-trash text-xs"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        // Add currency formatting to nominal input
        const nominalInput = inputGroup.querySelector('.buruh-nominal-item');
        nominalInput.addEventListener('input', function() {
            let val = this.value.replace(/\D/g, '');
            if (val !== '') {
                this.value = parseInt(val).toLocaleString('id-ID');
            }
        });
    };

    window.randomizeBuruhForSection = function(sectionIndex) {
        if (!allBuruhsData || allBuruhsData.length === 0) {
            alert('Data buruh tidak tersedia.');
            return;
        }

        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        
        // 1. Hitung total biaya kapal pada section ini (Barang + Adjustment)
        let sectionTotal = 0;
        const barangSelects = section.querySelectorAll('.barang-select-item');
        const jumlahInputs = section.querySelectorAll('.jumlah-input-item');
        const adjustmentInput = section.querySelector('.adjustment-input');
        
        barangSelects.forEach((select, index) => {
            const selectedOption = select.options[select.selectedIndex];
            const tarif = parseFloat(selectedOption.getAttribute('data-tarif')) || 0;
            const jumlahRaw = (jumlahInputs[index].value || '0').replace(',', '.');
            const jumlah = parseFloat(jumlahRaw) || 0;
            sectionTotal += tarif * jumlah;
        });

        if (adjustmentInput) {
            const adjustmentRaw = (adjustmentInput.value || '0').replace(/\./g, '').replace(',', '.');
            const adjustment = parseFloat(adjustmentRaw) || 0;
            sectionTotal += adjustment;
        }

        sectionTotal = Math.round(sectionTotal / 1000) * 1000;

        if (sectionTotal <= 0) {
            alert('Nominal biaya kapal masih 0 atau negatif. Harap isi data barang terlebih dahulu.');
            return;
        }

        const container = section.querySelector('.buruh-container-section');
        
        // Clear existing buruh in this section for a clean random set
        container.innerHTML = '';
        
        // 2. Tentukan jumlah buruh acak (Prioritas > 20 orang)
        // OPTIMASI: Cari jumlah buruh yang bisa membagi rata total nominal tanpa sisa (Perfectly Even)
        const minBuruhForCap = Math.ceil(sectionTotal / 440000);
        const searchMin = Math.max(21, minBuruhForCap);
        const searchMax = Math.max(searchMin + 14, 35);
        
        let perfectCounts = [];
        const limitSearch = Math.min(searchMax, allBuruhsData.length);
        const startSearch = Math.min(searchMin, allBuruhsData.length);
        
        for (let c = startSearch; c <= limitSearch; c++) {
            if (sectionTotal % (c * 1000) === 0) {
                perfectCounts.push(c);
            }
        }
        
        let count;
        if (perfectCounts.length > 0) {
            // Gunakan salah satu jumlah buruh yang menghasilkan angka bulat sempurna
            count = perfectCounts[Math.floor(Math.random() * perfectCounts.length)];
        } else {
            // Fallback: Gunakan random seperti biasa jika tidak ada pembagi sempurna
            count = Math.floor(Math.random() * (limitSearch - startSearch + 1)) + startSearch;
        }
        
        // Pick random unique buruhs
        const shuffled = [...allBuruhsData].sort(() => 0.5 - Math.random());
        const selected = shuffled.slice(0, Math.min(count, shuffled.length));
        
        // 3. Distribusikan sectionTotal ke buruh (Lainnya rata, 1 buruh dapat sisa lebih besar)
        let baseNominal = Math.floor((sectionTotal / selected.length) / 1000) * 1000;
        
        // Joki: Ada kemungkinan 50% untuk sengaja mengurangi baseNominal agar sisa (diff) terkumpul di 1 buruh
        // Ini memastikan ada variasi "1 buruh lebih besar" meskipun angkanya bisa dibagi rata sempurna
        if (Math.random() < 0.5 && baseNominal > 10000) {
            const reduction = 1000; 
            const testLucky = sectionTotal - ((baseNominal - reduction) * (selected.length - 1));
            if (testLucky <= 440000) {
                baseNominal -= reduction;
            }
        }

        let luckyNominal = sectionTotal - (baseNominal * (selected.length - 1));
        
        // Penyesuaian agar tidak melebihi cap 440rb
        while (luckyNominal > 440000 && baseNominal < 440000) {
            baseNominal += 1000;
            luckyNominal = sectionTotal - (baseNominal * (selected.length - 1));
        }

        // Pilih 1 buruh secara acak untuk menjadi si "Lucky"
        const luckyIndex = Math.floor(Math.random() * selected.length);

        selected.forEach((buruh, index) => {
            let nominal = (index === luckyIndex) ? luckyNominal : baseNominal;
            addBuruhToSectionWithData(sectionIndex, buruh.id, nominal);
        });
    };

    window.addBuruhToSectionWithData = function(sectionIndex, buruhId, nominal) {
        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.buruh-container-section');
        const buruhIndex = container.children.length;
        
        let buruhOptions = '<option value="">Pilih Nama Buruh</option>';
        allBuruhsData.forEach(buruh => {
            const selected = buruh.id == buruhId ? 'selected' : '';
            buruhOptions += `<option value="${buruh.id}" ${selected}>${buruh.nama} ${buruh.nik ? '('+buruh.nik+')' : ''}</option>`;
        });
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2 animate-fade-in';
        inputGroup.innerHTML = `
            <div class="flex-1">
                <select name="kapal_sections[${sectionIndex}][tenaga_kerja][${buruhIndex}][buruh_id]" class="buruh-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-emerald-500" required>
                    ${buruhOptions}
                </select>
            </div>
            <div class="w-32">
                <input type="text" name="kapal_sections[${sectionIndex}][tenaga_kerja][${buruhIndex}][nominal]" value="${nominal.toLocaleString('id-ID')}" class="buruh-nominal-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-emerald-500" placeholder="Rp 0" required>
            </div>
            <button type="button" onclick="removeBuruhFromSection(this)" class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition">
                <i class="fas fa-trash text-xs"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        const nominalInput = inputGroup.querySelector('.buruh-nominal-item');
        nominalInput.addEventListener('input', function() {
            let val = this.value.replace(/\D/g, '');
            if (val !== '') {
                this.value = parseInt(val).toLocaleString('id-ID');
            }
        });
    };
    
    window.removeBuruhFromSection = function(button) {
        const container = button.closest('.buruh-container-section');
        button.closest('.flex').remove();
        reindexBuruhInputs(container);
    };
    
    function reindexBuruhInputs(container) {
        const section = container.closest('.kapal-section');
        const sectionIndex = section.getAttribute('data-section-index');
        const inputGroups = container.querySelectorAll('.flex');
        
        inputGroups.forEach((group, newIndex) => {
            const buruhSelect = group.querySelector('.buruh-select-item');
            if (buruhSelect) {
                buruhSelect.name = `kapal_sections[${sectionIndex}][tenaga_kerja][${newIndex}][buruh_id]`;
            }
            
            const nominalInput = group.querySelector('.buruh-nominal-item');
            if (nominalInput) {
                nominalInput.name = `kapal_sections[${sectionIndex}][tenaga_kerja][${newIndex}][nominal]`;
            }
        });
    }

    window.removeBarangFromSection = function(button) {
        const container = button.closest('.barang-container-section');
        if (container.children.length > 1) {
            button.closest('.flex').remove();
            
            // CRITICAL FIX: Reindex all barang inputs after removal to prevent gaps in array indices
            reindexBarangInputs(container);
            
            calculateTotalFromAllSections();
        }
    };
    
    // Helper function to reindex barang input names after deletion
    function reindexBarangInputs(container) {
        const section = container.closest('.kapal-section');
        const sectionIndex = section.getAttribute('data-section-index');
        const inputGroups = container.querySelectorAll('.flex');
        
        inputGroups.forEach((group, newIndex) => {
            // Update barang_id input name
            const barangSelect = group.querySelector('.barang-select-item');
            if (barangSelect) {
                barangSelect.name = `kapal_sections[${sectionIndex}][barang][${newIndex}][barang_id]`;
            }
            
            // Update jumlah input name
            const jumlahInput = group.querySelector('.jumlah-input-item');
            if (jumlahInput) {
                jumlahInput.name = `kapal_sections[${sectionIndex}][barang][${newIndex}][jumlah]`;
            }
        });
    }
    
    function calculateTotalFromAllSections() {
        let grandTotal = 0;
        
        document.querySelectorAll('.kapal-section').forEach(section => {
            const adjustmentInput = section.querySelector('.adjustment-input');
            const nominalDisplay = section.querySelector('.section-nominal-display');
            const sectionTotalHidden = section.querySelector('.section-total-hidden');
            
            let sectionTotal = 0;
            const nominalManualInput = section.querySelector('.nominal-manual-input');
            
            if (nominalManualInput) {
                const kontainerNominals = section.querySelectorAll('.kontainer-nominal-item');
                let sumKontainer = 0;
                
                if (kontainerNominals.length > 0) {
                    kontainerNominals.forEach(input => {
                        const val = parseFloat((input.value || '0').replace(/\./g, '').replace(',', '.')) || 0;
                        sumKontainer += val;
                    });
                    nominalManualInput.value = sumKontainer > 0 ? Math.round(sumKontainer).toLocaleString('id-ID') : '';
                    nominalManualInput.setAttribute('readonly', 'readonly');
                    nominalManualInput.classList.add('bg-gray-100');
                    sectionTotal += sumKontainer;
                } else {
                    nominalManualInput.removeAttribute('readonly');
                    nominalManualInput.classList.remove('bg-gray-100');
                    const nominalRaw = (nominalManualInput.value || '0').replace(/\./g, '').replace(',', '.');
                    sectionTotal += parseFloat(nominalRaw) || 0;
                }
            } else {
                const barangSelects = section.querySelectorAll('.barang-select-item');
                const jumlahInputs = section.querySelectorAll('.jumlah-input-item');
                
                barangSelects.forEach((select, index) => {
                    const selectedOption = select.options[select.selectedIndex];
                    const tarif = parseFloat(selectedOption.getAttribute('data-tarif')) || 0;
                    // Convert comma to period for proper decimal parsing (Indonesian format)
                    const jumlahRaw = (jumlahInputs[index].value || '0').replace(',', '.');
                    const jumlah = parseFloat(jumlahRaw) || 0;
                    sectionTotal += tarif * jumlah;
                });
            }

            // Add adjustment
            if (adjustmentInput) {
                const adjustmentRaw = (adjustmentInput.value || '0').replace(/\./g, '').replace(',', '.');
                const adjustment = parseFloat(adjustmentRaw) || 0;
                sectionTotal += adjustment;
            }
            
            // Calculate PPh
            const pphPercentSelect = section.querySelector('.pph-percent-select');
            const pphAmountInput = section.querySelector('.pph-amount-input');
            if (pphPercentSelect && pphAmountInput) {
                const pphPercent = parseFloat(pphPercentSelect.value) || 0;
                const pphAmount = sectionTotal * (pphPercent / 100);
                pphAmountInput.value = pphAmount > 0 ? Math.round(pphAmount).toLocaleString('id-ID') : '0';
                
                // Subtract PPh from Total
                sectionTotal -= pphAmount;
            }
            
            // Update section nominal display
            if (nominalDisplay) {
                nominalDisplay.textContent = sectionTotal > 0 ? `Rp ${Math.round(sectionTotal).toLocaleString('id-ID')}` : 'Rp 0';
            }

            // Update hidden input for section total
            if (sectionTotalHidden) {
                sectionTotalHidden.value = Math.round(sectionTotal);
            }
            
            grandTotal += sectionTotal;
        });
        
        if (grandTotal > 0) {
            nominalInput.value = Math.round(grandTotal).toLocaleString('id-ID');
            // Recalculate sisa pembayaran after nominal changes
            calculateSisaPembayaran();
        } else {
            nominalInput.value = '';
        }
    }
    
    // --- KONTAINER BATAM LOGIC ---
    // --- KONTAINER BATAM LOGIC ---
    window.sectionContainers = window.sectionContainers || {};

    function loadContainersForBuruhSection(section, sectionIndex, voyageValue) {
        const kontainerContainer = section.querySelector('.kontainer-container-section');
        const kontainerLoading = section.querySelector('.kontainer-loading');
        const kontainerEmpty = section.querySelector('.kontainer-empty');
        const btnTambahKontainer = section.querySelector('.btn-tambah-kontainer');
        const infoKontainer = section.querySelector('.info-kontainer');

        if (!kontainerContainer) return;

        // Clear previous state
        kontainerContainer.innerHTML = '';
        kontainerLoading.classList.remove('hidden');
        kontainerEmpty.classList.add('hidden');
        if(btnTambahKontainer) {
            btnTambahKontainer.disabled = true;
            btnTambahKontainer.classList.remove('bg-indigo-500', 'hover:bg-indigo-600');
            btnTambahKontainer.classList.add('bg-gray-400');
        }
        if(infoKontainer) {
            infoKontainer.classList.remove('hidden');
        }

        if (!voyageValue) {
            kontainerLoading.classList.add('hidden');
            return;
        }

        fetch(`{{ url('biaya-kapal/get-manifest-containers-by-voyage') }}?voyage=${encodeURIComponent(voyageValue)}`)
            .then(res => res.json())
            .then(data => {
                kontainerLoading.classList.add('hidden');

                if (!data.success || !data.containers || data.containers.length === 0) {
                    kontainerEmpty.classList.remove('hidden');
                    return;
                }

                // Simpan data kontainer untuk section ini
                window.sectionContainers[sectionIndex] = data.containers;

                if(btnTambahKontainer) {
                    btnTambahKontainer.disabled = false;
                    btnTambahKontainer.classList.remove('bg-gray-400');
                    btnTambahKontainer.classList.add('bg-indigo-500', 'hover:bg-indigo-600');
                }
                if(infoKontainer) {
                    infoKontainer.classList.add('hidden');
                }
            })
            .catch(e => {
                console.error(e);
                kontainerLoading.classList.add('hidden');
                kontainerContainer.innerHTML = '<div class="p-3 text-center text-red-500 text-sm"><i class="fas fa-exclamation-triangle mr-1"></i>Gagal memuat kontainer</div>';
            });
    }

    window.addKontainerToSection = function(sectionIndex) {
        const section = document.querySelector(`[data-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.kontainer-container-section');
        const kontainerIndex = container.children.length;
        
        let kontainerOptions = '<option value="">Pilih Kontainer</option>';
        if (window.sectionContainers && window.sectionContainers[sectionIndex]) {
            window.sectionContainers[sectionIndex].forEach(c => {
                kontainerOptions += `<option value="${c.id}" data-nomor="${c.nomor_kontainer}" data-size="${c.size_kontainer || ''}">${c.nomor_kontainer} (BL: ${c.no_bl || '-'})</option>`;
            });
        }
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2 animate-fade-in';
        inputGroup.innerHTML = `
            <div class="flex-1">
                <select name="kapal_sections[${sectionIndex}][kontainer][${kontainerIndex}][bl_id]" class="kontainer-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-500" required>
                    ${kontainerOptions}
                </select>
                <input type="hidden" name="kapal_sections[${sectionIndex}][kontainer][${kontainerIndex}][nomor_kontainer]" class="kontainer-nomor-hidden">
                <input type="hidden" name="kapal_sections[${sectionIndex}][kontainer][${kontainerIndex}][size]" class="kontainer-size-hidden">
            </div>
            <div class="w-32">
                <input type="text" name="kapal_sections[${sectionIndex}][kontainer][${kontainerIndex}][nominal]" class="kontainer-nominal-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-500" placeholder="Rp 0" required>
            </div>
            <button type="button" onclick="removeKontainerFromSection(this)" class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition">
                <i class="fas fa-trash text-xs"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        const kontainerSelect = inputGroup.querySelector('.kontainer-select-item');
        const nomorHidden = inputGroup.querySelector('.kontainer-nomor-hidden');
        const sizeHidden = inputGroup.querySelector('.kontainer-size-hidden');
        
        if (typeof jQuery !== 'undefined' && $.fn.select2) {
            $(kontainerSelect).select2({
                placeholder: "Pilih Kontainer",
                allowClear: true,
                width: '100%'
            });
            
            $(kontainerSelect).on('select2:select', function (e) {
                const selectedOption = $(this).find(':selected');
                nomorHidden.value = selectedOption.attr('data-nomor') || '';
                const size = selectedOption.attr('data-size') || '';
                sizeHidden.value = size;

                // Auto-fill nominal for Buruh Bongkar
                if (typeof selectedJenisBiaya !== 'undefined' && selectedJenisBiaya && selectedJenisBiaya.kode === 'KB054') {
                    if (typeof pricelistBuruhBongkarData !== 'undefined' && size) {
                        const cleanCSize = String(size).replace(/[^0-9]/g, '');
                        const pricelist = pricelistBuruhBongkarData.find(p => {
                            const cleanPSize = String(p.size).replace(/[^0-9]/g, '');
                            return cleanPSize === cleanCSize;
                        });
                        if (pricelist) {
                            const nominalInput = inputGroup.querySelector('.kontainer-nominal-item');
                            nominalInput.value = parseInt(pricelist.nominal).toLocaleString('id-ID');
                            calculateTotalFromAllSections();
                        }
                    }
                }
            });
            
            $(kontainerSelect).on('select2:unselect', function (e) {
                nomorHidden.value = '';
                sizeHidden.value = '';
            });
        } else {
            kontainerSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                nomorHidden.value = selectedOption.getAttribute('data-nomor') || '';
                const size = selectedOption.getAttribute('data-size') || '';
                sizeHidden.value = size;

                // Auto-fill nominal for Buruh Bongkar
                if (typeof selectedJenisBiaya !== 'undefined' && selectedJenisBiaya && selectedJenisBiaya.kode === 'KB054') {
                    if (typeof pricelistBuruhBongkarData !== 'undefined' && size) {
                        const cleanCSize = String(size).replace(/[^0-9]/g, '');
                        const pricelist = pricelistBuruhBongkarData.find(p => {
                            const cleanPSize = String(p.size).replace(/[^0-9]/g, '');
                            return cleanPSize === cleanCSize;
                        });
                        if (pricelist) {
                            const nominalInput = inputGroup.querySelector('.kontainer-nominal-item');
                            nominalInput.value = parseInt(pricelist.nominal).toLocaleString('id-ID');
                            calculateTotalFromAllSections();
                        }
                    }
                }
            });
        }
        
        const nominalInput = inputGroup.querySelector('.kontainer-nominal-item');
        nominalInput.addEventListener('input', function() {
            let val = this.value.replace(/\D/g, '');
            if (val !== '') {
                this.value = parseInt(val).toLocaleString('id-ID');
            }
            calculateTotalFromAllSections();
        });
    };
    
    window.removeKontainerFromSection = function(button) {
        const container = button.closest('.kontainer-container-section');
        button.closest('.flex').remove();
        reindexKontainerInputs(container);
        calculateTotalFromAllSections();
    };

    function reindexKontainerInputs(container) {
        const section = container.closest('.kapal-section');
        const sectionIndex = section.getAttribute('data-section-index');
        const inputGroups = container.querySelectorAll('.flex');
        
        inputGroups.forEach((group, newIndex) => {
            const select = group.querySelector('.kontainer-select-item');
            if (select) select.name = `kapal_sections[${sectionIndex}][kontainer][${newIndex}][bl_id]`;
            
            const nomor = group.querySelector('.kontainer-nomor-hidden');
            if (nomor) nomor.name = `kapal_sections[${sectionIndex}][kontainer][${newIndex}][nomor_kontainer]`;
            
            const size = group.querySelector('.kontainer-size-hidden');
            if (size) size.name = `kapal_sections[${sectionIndex}][kontainer][${newIndex}][size]`;
            
            const nominal = group.querySelector('.kontainer-nominal-item');
            if (nominal) nominal.name = `kapal_sections[${sectionIndex}][kontainer][${newIndex}][nominal]`;
        });
    }
