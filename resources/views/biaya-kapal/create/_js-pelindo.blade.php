    const pelindoManifestVoyages = @json($pelindoManifestVoyages);
    const pelindoBarangOptions = @json($pelindoBarangOptions);
    const pelindoWrapper = document.getElementById('pelindo_wrapper');
    const pelindoSectionsContainer = document.getElementById('pelindo_sections_container');
    let pelindoNextSectionIndex = Date.now();
    let pelindoNextItemIndex = Date.now();

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

    function updatePelindoBarang(item) {
        const kegiatan = item.querySelector('.pelindo-jenis-tagihan-select').value;
        const barangSelect = item.querySelector('.pelindo-barang-select');
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
    }

    function updatePelindoNominal() {
        const total = [...pelindoSectionsContainer.querySelectorAll('.pelindo-item')]
            .reduce((sum, item) => {
                const jumlah = Number(item.querySelector('.pelindo-jumlah-input').value) || 0;
                const biaya = Number(item.querySelector('.pelindo-biaya-input').value) || 0;
                return sum + jumlah * biaya;
            }, 0);
        nominalInput.value = total > 0 ? total.toLocaleString('id-ID') : '';
    }

    function refreshPelindoSections() {
        const sections = [...pelindoSectionsContainer.querySelectorAll('.pelindo-section')];
        sections.forEach((section, index) => {
            section.querySelector('.pelindo-section-title').textContent = `Kapal ${index + 1} (Tagihan Pelindo)`;
            section.querySelector('.pelindo-remove-kapal').classList.toggle('hidden', sections.length === 1);
            refreshPelindoItems(section);
        });
    }

    function refreshPelindoItems(section) {
        const items = [...section.querySelectorAll('.pelindo-item')];
        items.forEach((item, index) => {
            item.querySelector('.pelindo-item-title').textContent = `Jenis Tagihan dan Barang ${index + 1}`;
            item.querySelector('.pelindo-remove-item').classList.toggle('hidden', items.length === 1);
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
        const addItemButton = event.target.closest('.pelindo-add-item');
        if (addItemButton) {
            const section = addItemButton.closest('.pelindo-section');
            const holder = document.createElement('div');
            holder.innerHTML = document.getElementById('pelindo_item_template').innerHTML
                .replaceAll('__SECTION__', section.dataset.sectionIndex)
                .replaceAll('__ITEM__', String(pelindoNextItemIndex++));
            const item = holder.firstElementChild;
            section.querySelector('.pelindo-items').append(item);
            item.querySelectorAll('select, input').forEach(input => {
                input.disabled = false;
                input.required = true;
            });
            refreshPelindoItems(section);
            item.querySelector('.pelindo-jenis-tagihan-select').focus();
            return;
        }

        if (event.target.closest('.pelindo-remove-item')) {
            const section = event.target.closest('.pelindo-section');
            event.target.closest('.pelindo-item').remove();
            refreshPelindoItems(section);
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
            const item = event.target.closest('.pelindo-item');
            item.querySelector('.pelindo-barang-select').value = '';
            updatePelindoBarang(item);
        }
    });

    pelindoSectionsContainer.addEventListener('input', function (event) {
        if (event.target.matches('.pelindo-jumlah-input, .pelindo-biaya-input')) updatePelindoNominal();
    });

    pelindoSectionsContainer.querySelectorAll('.pelindo-section').forEach(section => {
        updatePelindoVoyages(section);
        section.querySelectorAll('.pelindo-item').forEach(updatePelindoBarang);
    });
    refreshPelindoSections();

    window.addEventListener('DOMContentLoaded', function () {
        if (jenisBiayaSelect.value && selectedJenisBiaya.nama.toLowerCase().includes('tagihan pelindo')) {
            jenisBiayaSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
