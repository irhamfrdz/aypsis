// ============= KLAIM SECTIONS MANAGEMENT =============
    let klaimSectionCounter = 0;
    const klaimWrapper = document.getElementById('klaim_wrapper');
    const klaimSectionsContainer = document.getElementById('klaim_sections_container');
    const addKlaimSectionBtn = document.getElementById('add_klaim_section_btn');
    const addKlaimSectionBottomBtn = document.getElementById('add_klaim_section_bottom_btn');
    const globalDefaultNominalInput = document.getElementById('klaim_global_default_nominal');
    const applyGlobalNominalBtn = document.getElementById('klaim_apply_global_nominal_btn');

    function initializeKlaimSections() {
        if (!klaimSectionsContainer) return;
        klaimSectionsContainer.innerHTML = '';
        klaimSectionCounter = 0;
        addKlaimSection();
    }

    function clearAllKlaimSections() {
        if (!klaimSectionsContainer) return;
        klaimSectionsContainer.innerHTML = '';
        klaimSectionCounter = 0;
        syncKlaimTotalNominal();
    }

    if (addKlaimSectionBtn) {
        addKlaimSectionBtn.addEventListener('click', function() {
            addKlaimSection();
        });
    }

    if (addKlaimSectionBottomBtn) {
        addKlaimSectionBottomBtn.addEventListener('click', function() {
            addKlaimSection();
        });
    }

    // Format angka ribuan
    function formatKlaimRupiah(num) {
        if (!num && num !== 0) return '';
        return parseInt(num).toLocaleString('id-ID');
    }

    function parseKlaimRupiah(str) {
        if (!str) return 0;
        return parseInt(String(str).replace(/\D/g, '') || 0);
    }

    // Auto format rupiah pada input global default
    if (globalDefaultNominalInput) {
        globalDefaultNominalInput.addEventListener('input', function() {
            const val = parseKlaimRupiah(this.value);
            this.value = val > 0 ? formatKlaimRupiah(val) : '';
        });
    }

    // Terapkan tarif global ke semua kontainer terpilih di semua kapal
    if (applyGlobalNominalBtn && globalDefaultNominalInput) {
        applyGlobalNominalBtn.addEventListener('click', function() {
            const val = parseKlaimRupiah(globalDefaultNominalInput.value);
            if (val <= 0) {
                alert('Silakan masukkan tarif per kontainer terlebih dahulu.');
                return;
            }

            const formatted = formatKlaimRupiah(val);
            document.querySelectorAll('.klaim-section').forEach(section => {
                const checkboxes = section.querySelectorAll('.klaim-kontainer-checkbox:checked');
                checkboxes.forEach(cb => {
                    const card = cb.closest('.klaim-kontainer-card');
                    if (card) {
                        const feeInput = card.querySelector('.klaim-fee-input');
                        if (feeInput) feeInput.value = formatted;
                    }
                });
                calculateKlaimSectionSubtotal(section);
            });
            syncKlaimTotalNominal();
        });
    }

    function addKlaimSection() {
        if (!klaimSectionsContainer) return;
        klaimSectionCounter++;
        const sectionIndex = klaimSectionCounter;

        const section = document.createElement('div');
        section.className = 'klaim-section p-4 border-2 border-rose-200 rounded-xl bg-white shadow-sm transition-all hover:border-rose-300';
        section.setAttribute('data-klaim-section-index', sectionIndex);

        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        if (typeof allKapalsData !== 'undefined' && Array.isArray(allKapalsData)) {
            allKapalsData.forEach(kapal => {
                kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
            });
        }

        section.innerHTML = `
            <div class="flex items-center justify-between mb-3 border-b border-rose-100 pb-2.5">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-rose-600 text-white rounded-full flex items-center justify-center text-xs font-bold">${sectionIndex}</span>
                    <h4 class="text-sm font-bold text-gray-800">Kapal #${sectionIndex}</h4>
                </div>
                <button type="button" onclick="removeKlaimSection(${sectionIndex})" class="klaim-remove-btn px-2.5 py-1 text-red-600 hover:text-red-700 hover:bg-red-50 text-xs rounded-lg transition flex items-center gap-1 font-medium">
                    <i class="fas fa-trash-alt"></i> Hapus Kapal
                </button>
            </div>

            <!-- Grid Info Kapal & Voyage -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-3">
                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="klaim_sections[${sectionIndex}][kapal]" class="klaim-kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500 text-sm" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-1.5">
                        <select name="klaim_sections[${sectionIndex}][voyage]" class="klaim-voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500 text-sm" required disabled>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="klaim_sections[${sectionIndex}][voyage]" class="klaim-voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500 text-sm hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="klaim-voyage-manual-btn px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg border border-gray-300 transition text-xs" title="Mode Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan / No. Surat</label>
                    <input type="text" name="klaim_sections[${sectionIndex}][keterangan]" class="klaim-keterangan-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500 text-sm" placeholder="Contoh: 139/09/26/jts">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="klaim_sections[${sectionIndex}][tanggal]" class="klaim-tanggal-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500 text-sm">
                </div>
            </div>

            <!-- Panel Daftar Kontainer -->
            <div class="p-3.5 bg-gray-50/70 rounded-xl border border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2.5">
                    <div>
                        <label class="block text-xs font-bold text-gray-800">Daftar Kontainer Kapal Ini <span class="text-red-500">*</span></label>
                        <p class="text-[11px] text-gray-500"><i class="fas fa-info-circle mr-1"></i>Pilih No. Voyage di atas untuk memuat kontainer dari manifest</p>
                    </div>
                    <div class="klaim-bulk-toolbar hidden flex flex-wrap items-center gap-1.5">
                        <button type="button" class="klaim-select-all-btn text-xs px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded font-medium transition">
                            <i class="fas fa-check-double mr-1"></i>Pilih Semua
                        </button>
                        <button type="button" class="klaim-unselect-all-btn text-xs px-2.5 py-1 bg-white hover:bg-gray-100 text-gray-600 border border-gray-200 rounded font-medium transition">
                            <i class="fas fa-times mr-1"></i>Batal Pilih
                        </button>
                        <div class="flex items-center gap-1 ml-auto">
                            <div class="relative w-28">
                                <span class="absolute left-2 top-1 text-xs text-gray-400">Rp</span>
                                <input type="text" class="klaim-bulk-nominal-input w-full pl-6 pr-2 py-1 text-xs border border-gray-300 rounded" placeholder="Tarif Sama">
                            </div>
                            <button type="button" class="klaim-apply-bulk-btn text-xs px-2 py-1 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium transition" title="Terapkan ke kontainer terpilih di kapal ini">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search box -->
                <div class="klaim-kontainer-search-wrap hidden mb-2.5 relative">
                    <span class="absolute left-3 top-2 text-gray-400 text-xs"><i class="fas fa-search"></i></span>
                    <input type="text"
                           class="klaim-kontainer-search w-full pl-8 pr-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-rose-500 focus:border-rose-500 bg-white"
                           placeholder="Cari nomor kontainer, BL, atau barang...">
                </div>

                <!-- Loading indicator -->
                <div class="klaim-kontainer-loading hidden py-4 text-center text-gray-500 text-xs">
                    <i class="fas fa-spinner fa-spin mr-2 text-rose-500"></i>Memuat data kontainer dari manifest...
                </div>

                <!-- Empty state -->
                <div class="klaim-kontainer-empty hidden py-4 text-center text-gray-400 text-xs">
                    <i class="fas fa-inbox text-2xl mb-1.5 block text-gray-300"></i>Tidak ada kontainer ditemukan untuk voyage ini
                </div>

                <!-- Kontainer list -->
                <div class="klaim-kontainer-list space-y-2 max-h-72 overflow-y-auto pr-1"></div>

                <!-- Hidden inputs container -->
                <div class="klaim-kontainer-hidden-inputs"></div>
            </div>

            <!-- Subtotal Footer per Kapal -->
            <div class="border-t border-gray-200 pt-2.5 mt-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="text-xs text-gray-600">
                    <span>Terpilih pada kapal ini: </span><strong class="klaim-count-selected text-rose-600 font-bold">0</strong> kontainer
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs font-semibold text-gray-700 whitespace-nowrap">Subtotal Kapal Ini:</label>
                    <div class="relative w-44">
                        <span class="absolute left-2.5 top-1.5 text-xs text-rose-700 font-bold">Rp</span>
                        <input type="text" name="klaim_sections[${sectionIndex}][subtotal]"
                               class="klaim-section-subtotal-input w-full pl-8 pr-3 py-1 border border-rose-300 rounded-lg bg-rose-50 text-rose-800 font-bold text-sm text-right focus:ring-0 cursor-not-allowed"
                               value="0" readonly>
                        <input type="hidden" name="klaim_sections[${sectionIndex}][total_biaya]"
                               class="klaim-section-total-input" value="0">
                    </div>
                </div>
            </div>
        `;

        klaimSectionsContainer.appendChild(section);

        // Update numbering / remove button visibility
        updateKlaimSectionHeaders();

        // Bind events inside this section
        bindKlaimSectionEvents(section, sectionIndex);
    }

    function updateKlaimSectionHeaders() {
        const sections = document.querySelectorAll('.klaim-section');
        sections.forEach((sec, idx) => {
            const num = idx + 1;
            const badge = sec.querySelector('.w-6.h-6');
            if (badge) badge.textContent = num;
            const title = sec.querySelector('h4');
            if (title) title.textContent = `Kapal #${num}`;
            const removeBtn = sec.querySelector('.klaim-remove-btn');
            if (removeBtn) {
                removeBtn.style.display = sections.length > 1 ? 'flex' : 'none';
            }
        });
    }

    window.removeKlaimSection = function(sectionIndex) {
        const section = document.querySelector(`.klaim-section[data-klaim-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
            updateKlaimSectionHeaders();
            syncKlaimTotalNominal();
        }
    };

    function bindKlaimSectionEvents(section, sectionIndex) {
        const kapalSelect = section.querySelector('.klaim-kapal-select');
        const voyageSelect = section.querySelector('.klaim-voyage-select');
        const voyageInput = section.querySelector('.klaim-voyage-input');
        const voyageManualBtn = section.querySelector('.klaim-voyage-manual-btn');

        const kontainerList = section.querySelector('.klaim-kontainer-list');
        const hiddenInputsContainer = section.querySelector('.klaim-kontainer-hidden-inputs');
        const kontainerSearchWrap = section.querySelector('.klaim-kontainer-search-wrap');
        const kontainerSearch = section.querySelector('.klaim-kontainer-search');
        const kontainerLoading = section.querySelector('.klaim-kontainer-loading');
        const kontainerEmpty = section.querySelector('.klaim-kontainer-empty');
        const bulkToolbar = section.querySelector('.klaim-bulk-toolbar');
        const selectAllBtn = section.querySelector('.klaim-select-all-btn');
        const unselectAllBtn = section.querySelector('.klaim-unselect-all-btn');
        const bulkNominalInput = section.querySelector('.klaim-bulk-nominal-input');
        const applyBulkBtn = section.querySelector('.klaim-apply-bulk-btn');
        const countSelectedBadge = section.querySelector('.klaim-count-selected');
        const subtotalInput = section.querySelector('.klaim-section-subtotal-input');
        const totalHiddenInput = section.querySelector('.klaim-section-total-input');

        let isManualVoyage = false;

        // Toggle Manual Voyage
        voyageManualBtn.addEventListener('click', function() {
            isManualVoyage = !isManualVoyage;
            if (isManualVoyage) {
                voyageSelect.classList.add('hidden');
                voyageSelect.disabled = true;
                voyageInput.classList.remove('hidden');
                voyageInput.disabled = false;
                voyageInput.focus();
                this.classList.add('bg-rose-200', 'text-rose-700');
            } else {
                voyageInput.classList.add('hidden');
                voyageInput.disabled = true;
                voyageSelect.classList.remove('hidden');
                voyageSelect.disabled = false;
                this.classList.remove('bg-rose-200', 'text-rose-700');
                if (voyageSelect.value) {
                    loadContainersForKlaim(voyageSelect.value);
                }
            }
        });

        // Kapal change -> load voyages
        const handleKapalChange = function() {
            const kapalNama = kapalSelect.value;
            voyageSelect.innerHTML = '<option value="">-- Memuat Voyage... --</option>';
            voyageSelect.disabled = true;
            clearContainersView();

            if (!kapalNama) {
                voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
                return;
            }

            fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(kapalNama)}`)
                .then(res => res.json())
                .then(data => {
                    voyageSelect.innerHTML = '<option value="">-- Pilih Nomor Voyage --</option>';

                    let voyageList = [];
                    if (data && Array.isArray(data.voyages)) {
                        voyageList = data.voyages;
                    } else if (data && Array.isArray(data.voyages_detailed)) {
                        voyageList = data.voyages_detailed;
                    } else if (Array.isArray(data)) {
                        voyageList = data;
                    }

                    if (voyageList.length > 0) {
                        voyageList.forEach(v => {
                            const val = typeof v === 'object' ? (v.no_voyage || v.voyage) : v;
                            if (val) {
                                voyageSelect.innerHTML += `<option value="${val}">${val}</option>`;
                            }
                        });
                        voyageSelect.disabled = false;
                    } else {
                        voyageSelect.innerHTML = '<option value="">-- Tidak ada voyage ditemukan --</option>';
                    }
                })
                .catch(err => {
                    console.error('Error fetching voyages for Klaim:', err);
                    voyageSelect.innerHTML = '<option value="">-- Gagal memuat voyage --</option>';
                });
        };

        kapalSelect.addEventListener('change', handleKapalChange);
        if (typeof jQuery !== 'undefined') {
            jQuery(kapalSelect).on('change select2:select', handleKapalChange);
        }

        // Voyage select change -> load containers
        const handleVoyageChange = function() {
            if (this.value) {
                loadContainersForKlaim(this.value);
            } else {
                clearContainersView();
            }
        };

        voyageSelect.addEventListener('change', handleVoyageChange);
        if (typeof jQuery !== 'undefined') {
            jQuery(voyageSelect).on('change select2:select', handleVoyageChange);
        }

        // Voyage manual input (debounced)
        let manualTimeout;
        voyageInput.addEventListener('input', function() {
            clearTimeout(manualTimeout);
            const val = this.value.trim();
            manualTimeout = setTimeout(() => {
                if (val) {
                    loadContainersForKlaim(val);
                } else {
                    clearContainersView();
                }
            }, 600);
        });

        function clearContainersView() {
            kontainerList.innerHTML = '';
            hiddenInputsContainer.innerHTML = '';
            kontainerLoading.classList.add('hidden');
            kontainerEmpty.classList.add('hidden');
            kontainerSearchWrap.classList.add('hidden');
            bulkToolbar.classList.add('hidden');
            kontainerSearch.value = '';
            countSelectedBadge.textContent = '0';
            subtotalInput.value = '0';
            totalHiddenInput.value = '0';
            syncKlaimTotalNominal();
        }

        function loadContainersForKlaim(voyageVal) {
            clearContainersView();
            kontainerLoading.classList.remove('hidden');

            const kapalVal = kapalSelect ? kapalSelect.value : '';
            const url = `{{ url('biaya-kapal/get-manifest-containers-by-voyage') }}?voyage=${encodeURIComponent(voyageVal)}&kapal=${encodeURIComponent(kapalVal)}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    kontainerLoading.classList.add('hidden');

                    if (!data.success || !data.containers || data.containers.length === 0) {
                        kontainerEmpty.classList.remove('hidden');
                        return;
                    }

                    kontainerSearchWrap.classList.remove('hidden');
                    bulkToolbar.classList.remove('hidden');

                    // Default default nominal dari input global
                    const defaultFee = globalDefaultNominalInput ? globalDefaultNominalInput.value : '50.000';

                    data.containers.forEach((kontainer, idx) => {
                        const itemCard = document.createElement('div');
                        itemCard.className = 'klaim-kontainer-card flex flex-col md:flex-row md:items-center justify-between gap-2.5 p-3 bg-white hover:bg-rose-50/50 rounded-lg border border-gray-200 transition-all';
                        itemCard.setAttribute('data-search-text', `${kontainer.nomor_kontainer} ${kontainer.no_bl || ''} ${kontainer.nama_barang || ''}`.toLowerCase());

                        itemCard.innerHTML = `
                            <div class="flex items-start gap-2.5 flex-1">
                                <input type="checkbox"
                                       class="klaim-kontainer-checkbox mt-1 w-4 h-4 rounded text-rose-600 focus:ring-rose-500 cursor-pointer flex-shrink-0"
                                       data-idx="${idx}"
                                       data-bl-id="${kontainer.id || ''}"
                                       data-nomor="${kontainer.nomor_kontainer}"
                                       data-seal="${kontainer.no_seal || ''}"
                                       data-tipe="${kontainer.tipe_kontainer || ''}"
                                       data-size="${kontainer.size_kontainer || ''}"
                                       data-bl="${kontainer.no_bl || ''}">
                                <div class="flex-1 cursor-pointer klaim-card-info">
                                    <div class="font-semibold text-xs text-gray-800 flex flex-wrap items-center gap-1.5">
                                        <i class="fas fa-cube text-rose-500"></i>
                                        <span>${kontainer.nomor_kontainer}</span>
                                        <span class="text-[11px] bg-rose-100 text-rose-700 px-1.5 py-0.5 rounded-full font-medium">${kontainer.size_kontainer || '-'}'</span>
                                        <span class="text-[11px] bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded-full font-medium">${kontainer.tipe_kontainer || '-'}</span>
                                        ${kontainer.is_lcl ? `<span class="text-[11px] bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded-full font-semibold"><i class="fas fa-layer-group mr-1"></i>LCL (${kontainer.total_bl || 1} BL)</span>` : ''}
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-1 flex flex-wrap gap-x-3 gap-y-0.5">
                                        <span><i class="fas fa-file-invoice text-teal-600 mr-1"></i>BL: <strong class="text-gray-700">${kontainer.no_bl || '-'}</strong></span>
                                        <span><i class="fas fa-lock text-gray-400 mr-1"></i>Seal: ${kontainer.no_seal || '-'}</span>
                                        ${kontainer.nama_barang && kontainer.nama_barang !== '-' ? `<span><i class="fas fa-box text-orange-400 mr-1"></i>${kontainer.nama_barang}</span>` : ''}
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                <div class="relative w-36">
                                    <span class="absolute left-2.5 top-1.5 text-xs text-gray-400 font-semibold">Rp</span>
                                    <input type="text"
                                           class="klaim-fee-input w-full pl-8 pr-2.5 py-1 text-xs border border-gray-300 rounded-lg text-right font-bold text-gray-800 focus:ring-2 focus:ring-rose-500"
                                           placeholder="Biaya Klaim"
                                           value="${defaultFee}">
                                </div>
                                <div class="w-48">
                                    <input type="text"
                                           class="klaim-ket-input w-full px-2.5 py-1 text-xs border border-gray-300 rounded-lg text-gray-700 focus:ring-2 focus:ring-rose-500"
                                           placeholder="Ket: Kerusakan...">
                                </div>
                            </div>
                        `;

                        kontainerList.appendChild(itemCard);

                        // Card click -> toggle checkbox
                        const infoBlock = itemCard.querySelector('.klaim-card-info');
                        const cb = itemCard.querySelector('.klaim-kontainer-checkbox');
                        const feeInput = itemCard.querySelector('.klaim-fee-input');
                        const ketInput = itemCard.querySelector('.klaim-ket-input');

                        infoBlock.addEventListener('click', () => {
                            cb.checked = !cb.checked;
                            cb.dispatchEvent(new Event('change'));
                        });

                        cb.addEventListener('change', () => {
                            if (cb.checked) {
                                itemCard.classList.add('bg-rose-50/70', 'border-rose-300');
                                itemCard.classList.remove('bg-white');
                                if (!feeInput.value || feeInput.value === '0') {
                                    feeInput.value = globalDefaultNominalInput ? globalDefaultNominalInput.value : '50.000';
                                }
                            } else {
                                itemCard.classList.remove('bg-rose-50/70', 'border-rose-300');
                                itemCard.classList.add('bg-white');
                            }
                            rebuildHiddenInputs();
                            calculateKlaimSectionSubtotal(section);
                        });

                        // Fee input formatting
                        feeInput.addEventListener('input', function() {
                            const val = parseKlaimRupiah(this.value);
                            this.value = val > 0 ? formatKlaimRupiah(val) : '';
                            if (cb.checked) {
                                rebuildHiddenInputs();
                                calculateKlaimSectionSubtotal(section);
                            }
                        });

                        ketInput.addEventListener('input', function() {
                            if (cb.checked) {
                                rebuildHiddenInputs();
                            }
                        });
                    });
                })
                .catch(err => {
                    console.error('Error fetching containers for Klaim:', err);
                    kontainerLoading.classList.add('hidden');
                    kontainerEmpty.classList.remove('hidden');
                });
        }

        // Search container
        kontainerSearch.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            const cards = kontainerList.querySelectorAll('.klaim-kontainer-card');
            cards.forEach(card => {
                const text = card.getAttribute('data-search-text') || '';
                card.style.display = text.includes(term) ? 'flex' : 'none';
            });
        });

        // Select All visible
        selectAllBtn.addEventListener('click', function() {
            const cards = kontainerList.querySelectorAll('.klaim-kontainer-card');
            cards.forEach(card => {
                if (card.style.display !== 'none') {
                    const cb = card.querySelector('.klaim-kontainer-checkbox');
                    if (cb && !cb.checked) {
                        cb.checked = true;
                        cb.dispatchEvent(new Event('change'));
                    }
                }
            });
        });

        // Unselect All visible
        unselectAllBtn.addEventListener('click', function() {
            const cards = kontainerList.querySelectorAll('.klaim-kontainer-card');
            cards.forEach(card => {
                if (card.style.display !== 'none') {
                    const cb = card.querySelector('.klaim-kontainer-checkbox');
                    if (cb && cb.checked) {
                        cb.checked = false;
                        cb.dispatchEvent(new Event('change'));
                    }
                }
            });
        });

        // Bulk apply per kapal
        bulkNominalInput.addEventListener('input', function() {
            const val = parseKlaimRupiah(this.value);
            this.value = val > 0 ? formatKlaimRupiah(val) : '';
        });

        applyBulkBtn.addEventListener('click', function() {
            const val = parseKlaimRupiah(bulkNominalInput.value);
            if (val <= 0) return;
            const formatted = formatKlaimRupiah(val);

            const cards = kontainerList.querySelectorAll('.klaim-kontainer-card');
            cards.forEach(card => {
                const cb = card.querySelector('.klaim-kontainer-checkbox');
                if (cb && cb.checked) {
                    const feeInp = card.querySelector('.klaim-fee-input');
                    if (feeInp) feeInp.value = formatted;
                }
            });
            rebuildHiddenInputs();
            calculateKlaimSectionSubtotal(section);
        });

        function rebuildHiddenInputs() {
            hiddenInputsContainer.innerHTML = '';
            const checkboxes = kontainerList.querySelectorAll('.klaim-kontainer-checkbox:checked');
            countSelectedBadge.textContent = checkboxes.length;

            checkboxes.forEach((cb, kIdx) => {
                const card = cb.closest('.klaim-kontainer-card');
                const feeInput = card ? card.querySelector('.klaim-fee-input') : null;
                const ketInput = card ? card.querySelector('.klaim-ket-input') : null;

                const feeVal = feeInput ? parseKlaimRupiah(feeInput.value) : 0;
                const ketVal = ketInput ? ketInput.value : '';

                const prefix = `klaim_sections[${sectionIndex}][kontainer][${kIdx}]`;

                hiddenInputsContainer.innerHTML += `
                    <input type="hidden" name="${prefix}[bl_id]" value="${cb.getAttribute('data-bl-id')}">
                    <input type="hidden" name="${prefix}[nomor_kontainer]" value="${cb.getAttribute('data-nomor')}">
                    <input type="hidden" name="${prefix}[size]" value="${cb.getAttribute('data-size')}">
                    <input type="hidden" name="${prefix}[biaya_klaim]" value="${feeVal}">
                    <input type="hidden" name="${prefix}[keterangan]" value="${ketVal}">
                `;
            });
        }
    }

    function calculateKlaimSectionSubtotal(section) {
        let subtotal = 0;
        const checkboxes = section.querySelectorAll('.klaim-kontainer-checkbox:checked');
        checkboxes.forEach(cb => {
            const card = cb.closest('.klaim-kontainer-card');
            if (card) {
                const feeInput = card.querySelector('.klaim-fee-input');
                if (feeInput) subtotal += parseKlaimRupiah(feeInput.value);
            }
        });

        const subtotalInput = section.querySelector('.klaim-section-subtotal-input');
        const totalHiddenInput = section.querySelector('.klaim-section-total-input');

        if (subtotalInput) subtotalInput.value = formatKlaimRupiah(subtotal);
        if (totalHiddenInput) totalHiddenInput.value = subtotal;

        syncKlaimTotalNominal();
    }

    function syncKlaimTotalNominal() {
        let grandTotal = 0;
        let totalCont = 0;

        document.querySelectorAll('.klaim-section').forEach(section => {
            const hiddenTotal = section.querySelector('.klaim-section-total-input');
            if (hiddenTotal) {
                grandTotal += parseInt(hiddenTotal.value) || 0;
            }
            const checkedBoxes = section.querySelectorAll('.klaim-kontainer-checkbox:checked');
            totalCont += checkedBoxes.length;
        });

        // Update form utama nominal
        if (typeof nominalInput !== 'undefined' && nominalInput) {
            nominalInput.value = grandTotal > 0 ? formatKlaimRupiah(grandTotal) : '';
            if (typeof calculateTotalBiaya === 'function') {
                calculateTotalBiaya();
            }
        }

        // Update banner ringkasan klaim
        const contCountEl = document.getElementById('klaim_summary_cont_count');
        const calcTextEl = document.getElementById('klaim_summary_calc_text');
        const totalDisplayEl = document.getElementById('klaim_summary_total_display');

        if (contCountEl) contCountEl.textContent = totalCont;
        if (totalDisplayEl) totalDisplayEl.textContent = 'Rp ' + formatKlaimRupiah(grandTotal);

        if (calcTextEl) {
            if (totalCont > 0 && grandTotal > 0) {
                const avg = Math.round(grandTotal / totalCont);
                calcTextEl.textContent = `(Rata-rata: Rp ${formatKlaimRupiah(avg)} / cont)`;
            } else {
                calcTextEl.textContent = '';
            }
        }
    }

    // Sync input Penerima, Nomor Rekening, dan Bank tunggal klaim ke main form
    const klaimPenerimaInput = document.getElementById('klaim_penerima');
    if (klaimPenerimaInput) {
        klaimPenerimaInput.addEventListener('input', function() {
            const mainPenerima = document.getElementById('penerima');
            if (mainPenerima) mainPenerima.value = this.value;
        });
    }

    const klaimRekeningInput = document.getElementById('klaim_nomor_rekening');
    if (klaimRekeningInput) {
        klaimRekeningInput.addEventListener('input', function() {
            const mainRek = document.getElementById('nomor_rekening');
            if (mainRek) mainRek.value = this.value;
        });
    }

    const klaimBankInput = document.getElementById('klaim_bank_id');
    if (klaimBankInput) {
        klaimBankInput.addEventListener('change', function() {
            const mainBank = document.getElementById('bank_id');
            if (mainBank) mainBank.value = this.value;
        });
    }

