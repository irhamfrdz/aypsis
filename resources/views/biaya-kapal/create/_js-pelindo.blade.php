    const pelindoManifestVoyages = @json($pelindoManifestVoyages);
    const pelindoBarangOptions = @json($pelindoBarangOptions);
    const pelindoKebersihanOptions = @json($pelindoKebersihanOptions);
    const pelindoJumlahUrl = @json(route('biaya-kapal.pelindo-jumlah'));
    const pelindoWrapper = document.getElementById('pelindo_wrapper');
    const pelindoSectionsContainer = document.getElementById('pelindo_sections_container');
    const pelindoCountCache = new Map();
    let pelindoNextSectionIndex = Date.now();
    let pelindoNextTagihanIndex = Date.now();
    let pelindoNextBarangIndex = Date.now();

    function normalizePelindoKapal(namaKapal) {
        return namaKapal.replace(/\./g, '').replace(/\s+/g, ' ').trim().toUpperCase();
    }

    function updatePelindoVoyages(section) {
        const kapalSelect = section.querySelector('.pelindo-kapal-select');
        const voyageSelect = section.querySelector('.pelindo-voyage-select');
        const previous = voyageSelect.dataset.oldValue || voyageSelect.value;
        voyageSelect.replaceChildren(new Option(
            kapalSelect.value ? '-- Pilih Voyage --' : '-- Pilih Kapal Terlebih Dahulu --', ''
        ));
        const voyages = [...new Set(pelindoManifestVoyages
            .filter(item => normalizePelindoKapal(item.nama_kapal) === normalizePelindoKapal(kapalSelect.value))
            .map(item => item.no_voyage))];
        voyages.forEach(voyage => voyageSelect.add(new Option(voyage, voyage)));
        if (voyages.includes(previous)) voyageSelect.value = previous;
        delete voyageSelect.dataset.oldValue;
    }

    function updatePelindoLokasi(tagihan) {
        const kegiatan = tagihan.querySelector('.pelindo-jenis-tagihan-select').value;
        const lokasiSelect = tagihan.querySelector('.pelindo-lokasi-select');
        const previous = lokasiSelect.value;
        lokasiSelect.replaceChildren(new Option('-- Pilih Lokasi --', ''));
        [...new Set(pelindoBarangOptions
            .filter(option => option.kegiatan === kegiatan)
            .map(option => option.lokasi))]
            .forEach(lokasi => lokasiSelect.add(new Option(lokasi, lokasi)));
        if ([...lokasiSelect.options].some(option => option.value === previous)) lokasiSelect.value = previous;
    }

    function updatePelindoBarang(tagihan) {
        const kegiatan = tagihan.querySelector('.pelindo-jenis-tagihan-select').value;
        const lokasi = tagihan.querySelector('.pelindo-lokasi-select').value;
        tagihan.querySelectorAll('.pelindo-barang-select').forEach(barangSelect => {
            const previous = barangSelect.dataset.oldValue || barangSelect.value;
            barangSelect.replaceChildren(new Option(
                kegiatan && lokasi ? '-- Pilih Barang --' : '-- Pilih Jenis Tagihan dan Lokasi Terlebih Dahulu --', ''
            ));
            pelindoBarangOptions
                .filter(option => option.kegiatan === kegiatan && option.lokasi === lokasi)
                .forEach(option => {
                    const selectOption = new Option(option.label, option.value);
                    selectOption.dataset.countKey = option.count_key;
                    selectOption.dataset.tarif = option.tarif;
                    barangSelect.add(selectOption);
                });
            if ([...barangSelect.options].some(option => option.value === previous)) {
                barangSelect.value = previous;
            }
            delete barangSelect.dataset.oldValue;
            updatePelindoBiaya(barangSelect.closest('.pelindo-barang-row'));
        });
    }

    function updatePelindoBiaya(row) {
        const option = row.querySelector('.pelindo-barang-select').selectedOptions[0];
        const jumlahInput = row.querySelector('.pelindo-jumlah-input');
        const biayaInput = row.querySelector('.pelindo-biaya-input');
        const hint = row.querySelector('.pelindo-biaya-hint');
        const isCargo = option?.dataset.countKey === '|cargo';
        jumlahInput.step = isCargo ? '0.001' : '1';
        jumlahInput.min = isCargo ? '0.001' : '1';
        const tarif = Number(option?.dataset.tarif);
        const jumlah = Number(jumlahInput.value);
        if (!option?.value || jumlahInput.value === '' || !Number.isFinite(tarif) || !Number.isFinite(jumlah)) {
            biayaInput.value = '';
            biayaInput.dataset.cents = '';
            hint.textContent = 'Tarif Pricelist Pelindo × Jumlah.';
        } else {
            const biayaCents = Math.round(Math.round(tarif * 100) * jumlah);
            biayaInput.dataset.cents = String(biayaCents);
            biayaInput.value = (biayaCents / 100).toLocaleString('id-ID', { maximumFractionDigits: 2 });
            hint.textContent = `Tarif Rp ${tarif.toLocaleString('id-ID', { maximumFractionDigits: 2 })} × ${jumlah} ${isCargo ? 'ton' : 'barang'}.`;
        }
        updatePelindoNominal();
    }

    function getPelindoCounts(kapal, voyage) {
        const key = `${kapal}|${voyage}`;
        if (!pelindoCountCache.has(key)) {
            const url = new URL(pelindoJumlahUrl, window.location.origin);
            url.searchParams.set('kapal', kapal);
            url.searchParams.set('voyage', voyage);
            const request = fetch(url, { headers: { Accept: 'application/json' } })
                .then(response => {
                    if (!response.ok) throw new Error('Gagal mengambil jumlah dari manifest.');
                    return response.json();
                })
                .then(data => data.counts || {})
                .catch(error => {
                    pelindoCountCache.delete(key);
                    throw error;
                });
            pelindoCountCache.set(key, request);
        }
        return pelindoCountCache.get(key);
    }

    async function updatePelindoJumlah(row) {
        const section = row.closest('.pelindo-section');
        const kapal = section.querySelector('.pelindo-kapal-select').value;
        const voyage = section.querySelector('.pelindo-voyage-select').value;
        const barangSelect = row.querySelector('.pelindo-barang-select');
        const barang = barangSelect.value;
        const countKey = barangSelect.selectedOptions[0]?.dataset.countKey;
        const jumlahInput = row.querySelector('.pelindo-jumlah-input');
        const hint = row.querySelector('.pelindo-jumlah-hint');
        const selection = `${kapal}|${voyage}|${barang}`;
        row.dataset.countSelection = selection;
        if (!kapal || !voyage || !barang || row.dataset.manualJumlah === '1') return;

        hint.textContent = 'Menghitung jumlah dari manifest...';
        try {
            const counts = await getPelindoCounts(kapal, voyage);
            if (row.dataset.countSelection !== selection || row.dataset.manualJumlah === '1') return;
            const count = Number(counts[countKey] || 0);
            jumlahInput.value = count > 0 ? count : '';
            hint.textContent = count > 0
                ? (countKey === '|cargo'
                    ? 'Total tonnage Cargo dari manifest; dapat disesuaikan.'
                    : 'Jumlah dari manifest; dapat disesuaikan.')
                : 'Barang ini tidak ditemukan pada manifest. Isi jumlah secara manual bila diperlukan.';
            updatePelindoBiaya(row);
        } catch (error) {
            if (row.dataset.countSelection === selection) {
                hint.textContent = 'Jumlah tidak dapat dimuat. Isi secara manual.';
            }
        }
    }

    function resetPelindoJumlah(container) {
        container.querySelectorAll('.pelindo-barang-row').forEach(row => {
            row.dataset.manualJumlah = '';
            row.dataset.countSelection = '';
            row.querySelector('.pelindo-jumlah-input').value = '';
            row.querySelector('.pelindo-jumlah-hint').textContent = 'Terisi otomatis dari manifest kapal dan voyage; dapat disesuaikan.';
            updatePelindoBiaya(row);
        });
    }

    function updatePelindoNominal() {
        const rows = [...pelindoSectionsContainer.querySelectorAll('.pelindo-barang-row')];
        const barangCents = rows.reduce((sum, row) =>
            sum + (Number(row.querySelector('.pelindo-biaya-input').dataset.cents) || 0), 0);
        const adminInput = document.getElementById('pelindo_biaya_admin_nota');
        const kebersihanInput = document.getElementById('pelindo_biaya_kebersihan');
        const kebersihanHint = document.getElementById('pelindo_kebersihan_hint');
        const locations = [...new Set([...pelindoSectionsContainer.querySelectorAll('.pelindo-lokasi-select')]
            .map(input => input.value).filter(Boolean))];
        const matchingPricelists = locations.length === 1
            ? pelindoKebersihanOptions.filter(item => item.lokasi === locations[0])
            : [];
        const kebersihanCents = matchingPricelists.length === 1
            ? Math.round(Number(matchingPricelists[0].tarif) * 100)
            : 0;
        adminInput.value = '10.000';
        kebersihanInput.value = matchingPricelists.length === 1
            ? (kebersihanCents / 100).toLocaleString('id-ID', { maximumFractionDigits: 2 })
            : '';
        kebersihanHint.textContent = locations.length > 1
            ? 'Pilih satu lokasi yang sama untuk semua jenis tagihan.'
            : locations.length === 0
                ? 'Pilih lokasi tagihan untuk melihat tarif Kebersihan.'
                : matchingPricelists.length === 0
                    ? 'Tarif Kebersihan aktif untuk lokasi ini belum tersedia.'
                    : matchingPricelists.length > 1
                        ? 'Terdapat lebih dari satu tarif Kebersihan aktif untuk lokasi ini.'
                        : `Tarif Kebersihan dari Pricelist Pelindo ${locations[0]}.`;
        const extraCents = 1000000 + kebersihanCents;
        const subtotalCents = barangCents + extraCents;
        const adjustmentInput = document.getElementById('pelindo_adjustment');
        const adjustmentCents = (Number(adjustmentInput.value.replace(/\./g, '')) || 0) * 100;
        const grandTotalCents = subtotalCents + adjustmentCents;
        const hasCompleteRow = rows.some(row =>
            row.querySelector('.pelindo-jumlah-input').value !== '' &&
            row.querySelector('.pelindo-biaya-input').value !== ''
        );
        const showTotal = hasCompleteRow || locations.length > 0;
        document.getElementById('pelindo_subtotal').value = showTotal
            ? (subtotalCents / 100).toLocaleString('id-ID', { maximumFractionDigits: 2 }) : '';
        document.getElementById('pelindo_grand_total').value = showTotal
            ? (grandTotalCents / 100).toLocaleString('id-ID', { maximumFractionDigits: 2 }) : '';
        nominalInput.value = showTotal
            ? (Math.max(grandTotalCents, 0) / 100).toLocaleString('id-ID', { maximumFractionDigits: 2 }) : '';
    }

    const pelindoAdjustmentInput = document.getElementById('pelindo_adjustment');
    function formatPelindoAdjustment() {
        const value = pelindoAdjustmentInput.value.trim();
        const negative = value.startsWith('-');
        const digits = value.replace(/\D/g, '');
        pelindoAdjustmentInput.value = digits
            ? `${negative ? '-' : ''}${Number(digits).toLocaleString('id-ID')}`
            : negative ? '-' : '';
    }
    formatPelindoAdjustment();
    pelindoAdjustmentInput.addEventListener('input', function () {
        formatPelindoAdjustment();
        updatePelindoNominal();
    });
    pelindoAdjustmentInput.addEventListener('blur', function () {
        if (!this.value || this.value === '-') this.value = '0';
        updatePelindoNominal();
    });

    function refreshPelindoSections() {
        const sections = [...pelindoSectionsContainer.querySelectorAll('.pelindo-section')];
        sections.forEach((section, index) => {
            section.querySelector('.pelindo-section-title').textContent = `Kapal ${index + 1} (Tagihan Pelindo)`;
            section.querySelector('.pelindo-remove-kapal').classList.toggle('hidden', sections.length === 1);
            refreshPelindoTagihans(section);
        });
    }

    function refreshPelindoTagihans(section) {
        const tagihans = [...section.querySelectorAll('.pelindo-tagihan')];
        tagihans.forEach((tagihan, index) => {
            tagihan.querySelector('.pelindo-tagihan-title').textContent = `Jenis Tagihan ${index + 1}`;
            tagihan.querySelector('.pelindo-remove-tagihan').classList.toggle('hidden', tagihans.length === 1);
            refreshPelindoBarangRows(tagihan);
        });
    }

    function refreshPelindoBarangRows(tagihan) {
        const rows = [...tagihan.querySelectorAll('.pelindo-barang-row')];
        rows.forEach((row, index) => {
            row.querySelector('.pelindo-barang-title').textContent = `Barang ${index + 1}`;
            row.querySelector('.pelindo-remove-barang').classList.toggle('hidden', rows.length === 1);
        });
    }

    pelindoWrapper.querySelectorAll('.pelindo-add-kapal').forEach(button => {
        button.addEventListener('click', function () {
            const template = document.getElementById('pelindo_card_template').innerHTML;
            const holder = document.createElement('div');
            holder.innerHTML = template.replaceAll('__INDEX__', String(pelindoNextSectionIndex++));
            const section = holder.firstElementChild;
            pelindoSectionsContainer.append(section);
            section.querySelectorAll('select, input').forEach(input => {
                input.disabled = false;
                input.required = true;
            });
            refreshPelindoSections();
            section.querySelectorAll('.pelindo-tagihan').forEach(tagihan => updatePelindoLokasi(tagihan));
            section.querySelector('.pelindo-kapal-select').focus();
        });
    });

    pelindoSectionsContainer.addEventListener('click', function (event) {
        const addTagihanButton = event.target.closest('.pelindo-add-tagihan');
        if (addTagihanButton) {
            const section = addTagihanButton.closest('.pelindo-section');
            const holder = document.createElement('div');
            holder.innerHTML = document.getElementById('pelindo_tagihan_template').innerHTML
                .replaceAll('__SECTION__', section.dataset.sectionIndex)
                .replaceAll('__TAGIHAN__', String(pelindoNextTagihanIndex++));
            const tagihan = holder.firstElementChild;
            section.querySelector('.pelindo-tagihans').append(tagihan);
            tagihan.querySelectorAll('select, input').forEach(input => {
                input.disabled = false;
                input.required = true;
            });
            refreshPelindoTagihans(section);
            updatePelindoLokasi(tagihan);
            tagihan.querySelector('.pelindo-jenis-tagihan-select').focus();
            return;
        }

        if (event.target.closest('.pelindo-remove-tagihan')) {
            const section = event.target.closest('.pelindo-section');
            event.target.closest('.pelindo-tagihan').remove();
            refreshPelindoTagihans(section);
            updatePelindoNominal();
            return;
        }

        const addBarangButton = event.target.closest('.pelindo-add-barang');
        if (addBarangButton) {
            const section = addBarangButton.closest('.pelindo-section');
            const tagihan = addBarangButton.closest('.pelindo-tagihan');
            const holder = document.createElement('div');
            holder.innerHTML = document.getElementById('pelindo_barang_template').innerHTML
                .replaceAll('__SECTION__', section.dataset.sectionIndex)
                .replaceAll('__TAGIHAN__', tagihan.dataset.tagihanIndex)
                .replaceAll('__BARANG__', String(pelindoNextBarangIndex++));
            const row = holder.firstElementChild;
            tagihan.querySelector('.pelindo-barangs').append(row);
            row.querySelectorAll('select, input').forEach(input => {
                input.disabled = false;
                input.required = true;
            });
            refreshPelindoBarangRows(tagihan);
            updatePelindoBarang(tagihan);
            row.querySelector('.pelindo-barang-select').focus();
            return;
        }

        if (event.target.closest('.pelindo-remove-barang')) {
            const tagihan = event.target.closest('.pelindo-tagihan');
            event.target.closest('.pelindo-barang-row').remove();
            refreshPelindoBarangRows(tagihan);
            updatePelindoNominal();
            return;
        }

        if (event.target.closest('.pelindo-remove-kapal')) {
            event.target.closest('.pelindo-section').remove();
            refreshPelindoSections();
            updatePelindoNominal();
        }
    });

    pelindoSectionsContainer.addEventListener('change', function (event) {
        const section = event.target.closest('.pelindo-section');
        if (event.target.matches('.pelindo-kapal-select')) {
            section.querySelector('.pelindo-voyage-select').value = '';
            updatePelindoVoyages(section);
            resetPelindoJumlah(section);
        } else if (event.target.matches('.pelindo-voyage-select')) {
            resetPelindoJumlah(section);
            section.querySelectorAll('.pelindo-barang-row').forEach(updatePelindoJumlah);
        } else if (event.target.matches('.pelindo-jenis-tagihan-select')) {
            const tagihan = event.target.closest('.pelindo-tagihan');
            tagihan.querySelectorAll('.pelindo-barang-select').forEach(select => {
                select.value = '';
                delete select.dataset.oldValue;
            });
            updatePelindoLokasi(tagihan);
            updatePelindoBarang(tagihan);
            resetPelindoJumlah(tagihan);
        } else if (event.target.matches('.pelindo-lokasi-select')) {
            const tagihan = event.target.closest('.pelindo-tagihan');
            tagihan.querySelectorAll('.pelindo-barang-select').forEach(select => {
                select.value = '';
                delete select.dataset.oldValue;
            });
            updatePelindoBarang(tagihan);
            resetPelindoJumlah(tagihan);
        } else if (event.target.matches('.pelindo-barang-select')) {
            const row = event.target.closest('.pelindo-barang-row');
            row.dataset.manualJumlah = '';
            row.dataset.countSelection = '';
            row.querySelector('.pelindo-jumlah-input').value = '';
            updatePelindoBiaya(row);
            updatePelindoJumlah(row);
        }
    });

    pelindoSectionsContainer.addEventListener('input', function (event) {
        if (event.target.matches('.pelindo-jumlah-input')) {
            const row = event.target.closest('.pelindo-barang-row');
            row.dataset.manualJumlah = '1';
            updatePelindoBiaya(row);
        }
    });

    pelindoSectionsContainer.querySelectorAll('.pelindo-section').forEach(section => {
        updatePelindoVoyages(section);
        section.querySelectorAll('.pelindo-tagihan').forEach(tagihan => {
            updatePelindoLokasi(tagihan);
            updatePelindoBarang(tagihan);
        });
        section.querySelectorAll('.pelindo-barang-row').forEach(row => {
            row.dataset.manualJumlah = row.querySelector('.pelindo-jumlah-input').value ? '1' : '';
            updatePelindoJumlah(row);
        });
    });
    refreshPelindoSections();

    jenisBiayaSelect.addEventListener('change', function () {
        if ((selectedJenisBiaya.nama || '').toLowerCase().includes('tagihan pelindo')) {
            updatePelindoNominal();
        }
    });

    window.addEventListener('DOMContentLoaded', function () {
        if (jenisBiayaSelect.value && selectedJenisBiaya.nama.toLowerCase().includes('tagihan pelindo')) {
            jenisBiayaSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
