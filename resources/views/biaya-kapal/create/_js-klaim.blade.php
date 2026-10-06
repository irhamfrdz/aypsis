// ============= KLAIM SECTIONS MANAGEMENT =============
    let klaimSectionCounter = 0;
    const klaimWrapper = document.getElementById('klaim_wrapper');
    const klaimSectionsContainer = document.getElementById('klaim_sections_container');
    const addKlaimSectionBtn = document.getElementById('add_klaim_section_btn');
    const addKlaimSectionBottomBtn = document.getElementById('add_klaim_section_bottom_btn');

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

    function addKlaimSection() {
        if (!klaimSectionsContainer) return;
        klaimSectionCounter++;
        const sectionIndex = klaimSectionCounter;

        const section = document.createElement('div');
        section.className = 'klaim-section mb-6 p-4 border-2 border-rose-200 rounded-lg bg-rose-50/50 shadow-sm transition-all';
        section.setAttribute('data-klaim-section-index', sectionIndex);

        let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
        if (typeof allKapalsData !== 'undefined' && Array.isArray(allKapalsData)) {
            allKapalsData.forEach(kapal => {
                kapalOptions += `<option value="${kapal.nama_kapal}">${kapal.nama_kapal}</option>`;
            });
        }

        section.innerHTML = `
            <div class="flex items-center justify-between mb-4 border-b border-rose-200 pb-2">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 bg-rose-600 text-white rounded-full flex items-center justify-center text-xs font-bold">${sectionIndex}</span>
                    <h3 class="text-md font-semibold text-gray-800">Kapal &amp; Voyage (Klaim)</h3>
                </div>
                ${sectionIndex > 1 ? `<button type="button" onclick="removeKlaimSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-lg transition flex items-center gap-1 shadow-sm"><i class="fas fa-trash"></i> Hapus</button>` : ''}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                    <select name="klaim_sections[${sectionIndex}][kapal]" class="klaim-kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500 text-sm" required>
                        ${kapalOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="klaim_sections[${sectionIndex}][voyage]" class="klaim-voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500 text-sm" required disabled>
                            <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                        <input type="text" name="klaim_sections[${sectionIndex}][voyage]" class="klaim-voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500 text-sm hidden" disabled placeholder="Ketik No. Voyage">
                        <button type="button" class="klaim-voyage-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                            <i class="fas fa-keyboard"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-4 p-4 bg-white rounded-lg border-2 border-dashed border-rose-300">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Pilih Kontainer &amp; Biaya Klaim <span class="text-red-500">*</span></label>
                        <p class="text-xs text-gray-400"><i class="fas fa-info-circle mr-1"></i>Kontainer akan muncul setelah memilih No. Voyage</p>
                    </div>
                    <div class="klaim-bulk-toolbar hidden flex flex-wrap items-center gap-2">
                        <button type="button" class="klaim-select-all-btn text-xs px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded font-medium transition">
                            <i class="fas fa-check-double mr-1"></i>Pilih Semua
                        </button>
                        <button type="button" class="klaim-unselect-all-btn text-xs px-2.5 py-1 bg-gray-50 hover:bg-gray-100 text-gray-600 border border-gray-200 rounded font-medium transition">
                            <i class="fas fa-times mr-1"></i>Batal Pilih
                        </button>
                        <div class="flex items-center gap-1 ml-auto">
                            <div class="relative w-32">
                                <span class="absolute left-2 top-1.5 text-xs text-gray-400">Rp</span>
                                <input type="text" class="klaim-bulk-nominal-input w-full pl-7 pr-2 py-1 text-xs border border-gray-300 rounded" placeholder="Tarif Sama">
                            </div>
                            <button type="button" class="klaim-apply-bulk-btn text-xs px-2 py-1 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium transition" title="Terapkan ke kontainer terpilih">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search box -->
                <div class="klaim-kontainer-search-wrap hidden mb-3 relative">
                    <span class="absolute left-3 top-2 text-gray-400 text-sm"><i class="fas fa-search"></i></span>
                    <input type="text"
                           class="klaim-kontainer-search w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                           placeholder="Cari nomor kontainer, BL, atau barang...">
                </div>

                <!-- Loading indicator -->
                <div class="klaim-kontainer-loading hidden py-4 text-center text-gray-500 text-sm">
                    <i class="fas fa-spinner fa-spin mr-2 text-rose-500"></i>Memuat data kontainer...
                </div>

                <!-- Empty state -->
                <div class="klaim-kontainer-empty hidden py-4 text-center text-gray-400 text-sm">
                    <i class="fas fa-inbox text-3xl mb-2 block text-gray-300"></i>Tidak ada kontainer untuk voyage ini
                </div>

                <!-- Kontainer list -->
                <div class="klaim-kontainer-list space-y-2 max-h-72 overflow-y-auto pr-1"></div>

                <!-- Hidden inputs container -->
                <div class="klaim-kontainer-hidden-inputs"></div>
            </div>

            <div class="border-t border-rose-200 pt-3 mt-2 flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="text-xs text-gray-600">
                    <span>Terpilih: </span><strong class="klaim-count-selected text-rose-600">0</strong> kontainer
                </div>
                <div class="flex items-center gap-3">
                    <label class="text-xs font-semibold text-gray-700 whitespace-nowrap">Subtotal Klaim Kapal Ini:</label>
                    <div class="relative w-48">
                        <span class="absolute left-3 top-2 text-xs text-gray-400 font-bold">Rp</span>
                        <input type="text" name="klaim_sections[${sectionIndex}][subtotal]"
                               class="klaim-section-subtotal-input w-full pl-9 pr-3 py-1.5 border border-rose-300 rounded-lg bg-rose-100 text-rose-800 font-bold text-sm text-right focus:ring-0 cursor-not-allowed"
                               value="0" readonly>
                        <input type="hidden" name="klaim_sections[${sectionIndex}][total_biaya]"
                               class="klaim-section-total-input" value="0">
                    </div>
                </div>
            </div>
        `;

        klaimSectionsContainer.appendChild(section);

        // Bind events inside this section
        bindKlaimSectionEvents(section, sectionIndex);
    }

    window.removeKlaimSection = function(sectionIndex) {
        const section = document.querySelector(`.klaim-section[data-klaim-section-index="${sectionIndex}"]`);
        if (section) {
            section.remove();
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
        kapalSelect.addEventListener('change', function() {
            const kapalNama = this.value;
            voyageSelect.innerHTML = '<option value="">-- Memuat Voyage... --</option>';
            voyageSelect.disabled = true;
            clearContainersView();

            if (!kapalNama) {
                voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
                return;
            }

            fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(kapalNama)}`)
                .then(res => res.json())
                .then(voyages => {
                    voyageSelect.innerHTML = '<option value="">-- Pilih Nomor Voyage --</option>';
                    if (Array.isArray(voyages) && voyages.length > 0) {
                        voyages.forEach(v => {
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
        });

        // Voyage select change -> load containers
        voyageSelect.addEventListener('change', function() {
            if (this.value) {
                loadContainersForKlaim(this.value);
            } else {
                clearContainersView();
            }
        });

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

            fetch(`{{ url('biaya-kapal/get-containers-by-voyage') }}?voyage=${encodeURIComponent(voyageVal)}`)
                .then(res => res.json())
                .then(data => {
                    kontainerLoading.classList.add('hidden');

                    if (!data.success || !data.containers || data.containers.length === 0) {
                        kontainerEmpty.classList.remove('hidden');
                        return;
                    }

                    kontainerSearchWrap.classList.remove('hidden');
                    bulkToolbar.classList.remove('hidden');

                    data.containers.forEach((kontainer, idx) => {
                        const itemCard = document.createElement('div');
                        itemCard.className = 'klaim-kontainer-card flex flex-col md:flex-row md:items-center justify-between gap-3 p-3 bg-gray-50 hover:bg-rose-50/60 rounded-lg border border-gray-200 transition-all';
                        itemCard.setAttribute('data-search-text', `${kontainer.nomor_kontainer} ${kontainer.no_bl || ''} ${kontainer.nama_barang || ''}`.toLowerCase());

                        itemCard.innerHTML = `
                            <div class="flex items-start gap-3 flex-1">
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
                                    <div class="font-semibold text-sm text-gray-800">
                                        <i class="fas fa-cube text-rose-500 mr-1"></i>
                                        ${kontainer.nomor_kontainer}
                                        <span class="ml-1.5 text-xs bg-rose-100 text-rose-700 px-2 py-0.5 rounded-full font-normal">${kontainer.size_kontainer || '-'}'</span>
                                        <span class="ml-1 text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-normal">${kontainer.tipe_kontainer || '-'}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1 flex flex-wrap gap-x-3 gap-y-0.5">
                                        <span><i class="fas fa-file-invoice text-teal-500 mr-1"></i>BL: ${kontainer.no_bl || '-'}</span>
                                        <span><i class="fas fa-lock text-gray-400 mr-1"></i>Seal: ${kontainer.no_seal || '-'}</span>
                                        ${kontainer.nama_barang && kontainer.nama_barang !== '-' ? `<span><i class="fas fa-box text-orange-400 mr-1"></i>${kontainer.nama_barang}</span>` : ''}
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                <div class="relative w-full sm:w-40">
                                    <span class="absolute left-2.5 top-2 text-xs text-gray-400 font-medium">Rp</span>
                                    <input type="text"
                                           class="klaim-nominal-input w-full pl-8 pr-2 py-1.5 border border-gray-300 rounded-lg text-xs font-semibold text-gray-800 focus:ring-2 focus:ring-rose-500 bg-gray-100 transition"
                                           placeholder="Biaya Klaim" disabled>
                                </div>
                                <div class="w-full sm:w-44">
                                    <input type="text"
                                           class="klaim-ket-input w-full px-2.5 py-1.5 border border-gray-300 rounded-lg text-xs text-gray-700 focus:ring-2 focus:ring-rose-500 bg-gray-100 transition"
                                           placeholder="Ket: Kerusakan..." disabled>
                                </div>
                            </div>
                        `;

                        kontainerList.appendChild(itemCard);

                        const chk = itemCard.querySelector('.klaim-kontainer-checkbox');
                        const cardInfo = itemCard.querySelector('.klaim-card-info');
                        const nomInput = itemCard.querySelector('.klaim-nominal-input');
                        const ketInput = itemCard.querySelector('.klaim-ket-input');

                        // Click on card info toggles checkbox
                        cardInfo.addEventListener('click', function() {
                            chk.checked = !chk.checked;
                            chk.dispatchEvent(new Event('change'));
                        });

                        // Checkbox toggle
                        chk.addEventListener('change', function() {
                            if (this.checked) {
                                itemCard.classList.add('bg-rose-50/80', 'border-rose-300');
                                itemCard.classList.remove('bg-gray-50');
                                nomInput.disabled = false;
                                nomInput.classList.remove('bg-gray-100');
                                nomInput.classList.add('bg-white');
                                ketInput.disabled = false;
                                ketInput.classList.remove('bg-gray-100');
                                ketInput.classList.add('bg-white');
                                updateHiddenContainerInput(sectionIndex, idx, this, nomInput.value, ketInput.value);
                                nomInput.focus();
                            } else {
                                itemCard.classList.remove('bg-rose-50/80', 'border-rose-300');
                                itemCard.classList.add('bg-gray-50');
                                nomInput.disabled = true;
                                nomInput.classList.add('bg-gray-100');
                                nomInput.classList.remove('bg-white');
                                ketInput.disabled = true;
                                ketInput.classList.add('bg-gray-100');
                                ketInput.classList.remove('bg-white');
                                removeHiddenContainerInput(idx);
                            }
                            recalcKlaimSectionTotal();
                        });

                        // Format currency on nominal input
                        nomInput.addEventListener('input', function() {
                            let raw = this.value.replace(/\D/g, '');
                            if (raw) {
                                this.value = parseInt(raw).toLocaleString('id-ID');
                            } else {
                                this.value = '';
                            }
                            if (chk.checked) {
                                updateHiddenContainerInput(sectionIndex, idx, chk, this.value, ketInput.value);
                            }
                            recalcKlaimSectionTotal();
                        });

                        // Keterangan input
                        ketInput.addEventListener('input', function() {
                            if (chk.checked) {
                                updateHiddenContainerInput(sectionIndex, idx, chk, nomInput.value, this.value);
                            }
                        });
                    });
                })
                .catch(err => {
                    kontainerLoading.classList.add('hidden');
                    console.error('Error fetching containers for Klaim:', err);
                });
        }

        function updateHiddenContainerInput(secIdx, itemIdx, chkElem, nomVal, ketVal) {
            let hiddenGroup = hiddenInputsContainer.querySelector(`[data-klaim-item-idx="${itemIdx}"]`);
            if (!hiddenGroup) {
                hiddenGroup = document.createElement('div');
                hiddenGroup.setAttribute('data-klaim-item-idx', itemIdx);
                hiddenInputsContainer.appendChild(hiddenGroup);
            }
            const cleanNom = nomVal ? nomVal.replace(/\./g, '') : '0';
            hiddenGroup.innerHTML = `
                <input type="hidden" name="klaim_sections[${secIdx}][kontainer][${itemIdx}][bl_id]" value="${chkElem.dataset.blId}">
                <input type="hidden" name="klaim_sections[${secIdx}][kontainer][${itemIdx}][nomor_kontainer]" value="${chkElem.dataset.nomor}">
                <input type="hidden" name="klaim_sections[${secIdx}][kontainer][${itemIdx}][size]" value="${chkElem.dataset.size}">
                <input type="hidden" name="klaim_sections[${secIdx}][kontainer][${itemIdx}][tipe_kontainer]" value="${chkElem.dataset.tipe}">
                <input type="hidden" name="klaim_sections[${secIdx}][kontainer][${itemIdx}][no_seal]" value="${chkElem.dataset.seal}">
                <input type="hidden" name="klaim_sections[${secIdx}][kontainer][${itemIdx}][no_bl]" value="${chkElem.dataset.bl}">
                <input type="hidden" name="klaim_sections[${secIdx}][kontainer][${itemIdx}][biaya_klaim]" value="${cleanNom}" class="klaim-item-nominal-hidden">
                <input type="hidden" name="klaim_sections[${secIdx}][kontainer][${itemIdx}][keterangan]" value="${ketVal || ''}">
            `;
        }

        function removeHiddenContainerInput(itemIdx) {
            const hiddenGroup = hiddenInputsContainer.querySelector(`[data-klaim-item-idx="${itemIdx}"]`);
            if (hiddenGroup) hiddenGroup.remove();
        }

        function recalcKlaimSectionTotal() {
            let sectionSum = 0;
            let checkedCount = 0;

            const cards = kontainerList.querySelectorAll('.klaim-kontainer-card');
            cards.forEach(card => {
                const chk = card.querySelector('.klaim-kontainer-checkbox');
                const nom = card.querySelector('.klaim-nominal-input');
                if (chk && chk.checked) {
                    checkedCount++;
                    const val = parseInt(nom.value.replace(/\D/g, '') || 0);
                    sectionSum += val;
                }
            });

            countSelectedBadge.textContent = checkedCount;
            subtotalInput.value = sectionSum > 0 ? sectionSum.toLocaleString('id-ID') : '0';
            totalHiddenInput.value = sectionSum;

            syncKlaimTotalNominal();
        }

        // Search container filter
        kontainerSearch.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            const cards = kontainerList.querySelectorAll('.klaim-kontainer-card');
            cards.forEach(card => {
                const text = card.getAttribute('data-search-text') || '';
                card.style.display = text.includes(q) ? '' : 'none';
            });
        });

        // Select All / Unselect All
        selectAllBtn.addEventListener('click', function() {
            const cards = kontainerList.querySelectorAll('.klaim-kontainer-card');
            cards.forEach(card => {
                if (card.style.display !== 'none') {
                    const chk = card.querySelector('.klaim-kontainer-checkbox');
                    if (chk && !chk.checked) {
                        chk.checked = true;
                        chk.dispatchEvent(new Event('change'));
                    }
                }
            });
        });

        unselectAllBtn.addEventListener('click', function() {
            const cards = kontainerList.querySelectorAll('.klaim-kontainer-card');
            cards.forEach(card => {
                const chk = card.querySelector('.klaim-kontainer-checkbox');
                if (chk && chk.checked) {
                    chk.checked = false;
                    chk.dispatchEvent(new Event('change'));
                }
            });
        });

        // Bulk nominal format & apply
        bulkNominalInput.addEventListener('input', function() {
            let raw = this.value.replace(/\D/g, '');
            this.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
        });

        applyBulkBtn.addEventListener('click', function() {
            const bulkVal = bulkNominalInput.value.trim();
            if (!bulkVal) return;

            const cards = kontainerList.querySelectorAll('.klaim-kontainer-card');
            cards.forEach(card => {
                const chk = card.querySelector('.klaim-kontainer-checkbox');
                if (chk && chk.checked) {
                    const nomInput = card.querySelector('.klaim-nominal-input');
                    nomInput.value = bulkVal;
                    nomInput.dispatchEvent(new Event('input'));
                }
            });
        });
    }

    function syncKlaimTotalNominal() {
        const currentJenis = selectedJenisBiaya.nama || '';
        if (!currentJenis.toLowerCase().includes('klaim')) {
            return;
        }

        let overallTotal = 0;
        document.querySelectorAll('.klaim-section').forEach(sec => {
            const cards = sec.querySelectorAll('.klaim-kontainer-card');
            cards.forEach(card => {
                const chk = card.querySelector('.klaim-kontainer-checkbox');
                const nom = card.querySelector('.klaim-nominal-input');
                if (chk && chk.checked) {
                    const val = parseInt(nom.value.replace(/\D/g, '') || 0);
                    overallTotal += val;
                }
            });
        });

        if (nominalInput) {
            nominalInput.value = overallTotal > 0 ? overallTotal.toLocaleString('id-ID') : '';
        }
    }
