    const pelindoManifestVoyages = @json($pelindoManifestVoyages);
    const pelindoBarangOptions = @json($pelindoBarangOptions);
    const pelindoWrapper = document.getElementById('pelindo_wrapper');
    const pelindoSectionsContainer = document.getElementById('pelindo_sections_container');
    let pelindoNextSectionIndex = Date.now();

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

    function updatePelindoBarang(section) {
        const kegiatan = section.querySelector('.pelindo-jenis-tagihan-select').value;
        const barangSelect = section.querySelector('.pelindo-barang-select');
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
        const total = [...pelindoSectionsContainer.querySelectorAll('.pelindo-section')]
            .reduce((sum, section) => {
                const jumlah = Number(section.querySelector('.pelindo-jumlah-input').value) || 0;
                const biaya = Number(section.querySelector('.pelindo-biaya-input').value) || 0;
                return sum + jumlah * biaya;
            }, 0);
        nominalInput.value = total > 0 ? total.toLocaleString('id-ID') : '';
    }

    function refreshPelindoSections() {
        const sections = [...pelindoSectionsContainer.querySelectorAll('.pelindo-section')];
        sections.forEach((section, index) => {
            section.querySelector('.pelindo-section-title').textContent = `Kapal ${index + 1} (Tagihan Pelindo)`;
            section.querySelector('.pelindo-remove-kapal').classList.toggle('hidden', sections.length === 1);
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
        if (!event.target.matches('.pelindo-remove-kapal')) return;
        event.target.closest('.pelindo-section').remove();
        refreshPelindoSections();
        updatePelindoNominal();
    });

    pelindoSectionsContainer.addEventListener('change', function (event) {
        const section = event.target.closest('.pelindo-section');
        if (event.target.matches('.pelindo-kapal-select')) {
            section.querySelector('.pelindo-voyage-select').value = '';
            updatePelindoVoyages(section);
        } else if (event.target.matches('.pelindo-jenis-tagihan-select')) {
            section.querySelector('.pelindo-barang-select').value = '';
            updatePelindoBarang(section);
        }
    });

    pelindoSectionsContainer.addEventListener('input', function (event) {
        if (event.target.matches('.pelindo-jumlah-input, .pelindo-biaya-input')) updatePelindoNominal();
    });

    pelindoSectionsContainer.querySelectorAll('.pelindo-section').forEach(section => {
        updatePelindoVoyages(section);
        updatePelindoBarang(section);
    });
    refreshPelindoSections();

    window.addEventListener('DOMContentLoaded', function () {
        if (jenisBiayaSelect.value && selectedJenisBiaya.nama.toLowerCase().includes('tagihan pelindo')) {
            jenisBiayaSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
