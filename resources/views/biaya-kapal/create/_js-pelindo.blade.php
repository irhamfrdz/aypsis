    const pelindoManifestVoyages = @json($pelindoManifestVoyages);
    const pelindoBarangOptions = @json($pelindoBarangOptions);
    const pelindoWrapper = document.getElementById('pelindo_wrapper');
    const pelindoSectionsContainer = document.getElementById('pelindo_sections_container');
    let pelindoNextSectionIndex = Date.now();
    let pelindoNextTagihanIndex = Date.now();
    let pelindoNextBarangIndex = Date.now();

    function updatePelindoVoyages(section) {
        const kapalSelect = section.querySelector('.pelindo-kapal-select');
        const voyageSelect = section.querySelector('.pelindo-voyage-select');
        const previous = voyageSelect.dataset.oldValue || voyageSelect.value;
        voyageSelect.replaceChildren(new Option(
            kapalSelect.value ? '-- Pilih Voyage --' : '-- Pilih Kapal Terlebih Dahulu --', ''
        ));
        const voyages = [...new Set(pelindoManifestVoyages
            .filter(item => item.nama_kapal === kapalSelect.value)
            .map(item => item.no_voyage))];
        voyages.forEach(voyage => voyageSelect.add(new Option(voyage, voyage)));
        if (voyages.includes(previous)) voyageSelect.value = previous;
        delete voyageSelect.dataset.oldValue;
    }

    function updatePelindoBarang(tagihan) {
        const kegiatan = tagihan.querySelector('.pelindo-jenis-tagihan-select').value;
        tagihan.querySelectorAll('.pelindo-barang-select').forEach(barangSelect => {
            const previous = barangSelect.dataset.oldValue || barangSelect.value;
            barangSelect.replaceChildren(new Option(
                kegiatan ? '-- Pilih Barang --' : '-- Pilih Jenis Tagihan Terlebih Dahulu --', ''
            ));
            pelindoBarangOptions
                .filter(option => option.kegiatan === kegiatan)
                .forEach(option => barangSelect.add(new Option(option.label, option.value)));
            if ([...barangSelect.options].some(option => option.value === previous)) {
                barangSelect.value = previous;
            }
            delete barangSelect.dataset.oldValue;
        });
    }

    function updatePelindoNominal() {
        const rows = [...pelindoSectionsContainer.querySelectorAll('.pelindo-barang-row')];
        const total = rows
            .reduce((sum, row) => {
                const jumlah = Number(row.querySelector('.pelindo-jumlah-input').value) || 0;
                const biaya = Number(row.querySelector('.pelindo-biaya-input').value) || 0;
                return sum + jumlah * biaya;
            }, 0);
        const hasCompleteRow = rows.some(row =>
            row.querySelector('.pelindo-jumlah-input').value !== '' &&
            row.querySelector('.pelindo-biaya-input').value !== ''
        );
        nominalInput.value = hasCompleteRow ? total.toLocaleString('id-ID') : '';
    }

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
        } else if (event.target.matches('.pelindo-jenis-tagihan-select')) {
            const tagihan = event.target.closest('.pelindo-tagihan');
            tagihan.querySelectorAll('.pelindo-barang-select').forEach(select => {
                select.value = '';
                delete select.dataset.oldValue;
            });
            updatePelindoBarang(tagihan);
        }
    });

    pelindoSectionsContainer.addEventListener('input', function (event) {
        if (event.target.matches('.pelindo-jumlah-input, .pelindo-biaya-input')) updatePelindoNominal();
    });

    pelindoSectionsContainer.querySelectorAll('.pelindo-section').forEach(section => {
        updatePelindoVoyages(section);
        section.querySelectorAll('.pelindo-tagihan').forEach(updatePelindoBarang);
    });
    refreshPelindoSections();

    window.addEventListener('DOMContentLoaded', function () {
        if (jenisBiayaSelect.value && selectedJenisBiaya.nama.toLowerCase().includes('tagihan pelindo')) {
            jenisBiayaSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
