    const pelindoManifestVoyages = @json($pelindoManifestVoyages);
    const pelindoKapalSelect = document.getElementById('pelindo_kapal');
    const pelindoVoyageSelect = document.getElementById('pelindo_voyage');

    function updatePelindoVoyages() {
        const oldValue = pelindoVoyageSelect.dataset.oldValue || pelindoVoyageSelect.value;
        pelindoVoyageSelect.replaceChildren(new Option(
            pelindoKapalSelect.value ? '-- Pilih Voyage --' : '-- Pilih Kapal Terlebih Dahulu --', ''
        ));
        const voyages = [...new Set(pelindoManifestVoyages
            .filter(item => item.nama_kapal === pelindoKapalSelect.value)
            .map(item => item.no_voyage))];
        voyages.forEach(voyage => pelindoVoyageSelect.add(new Option(voyage, voyage)));
        if (voyages.includes(oldValue)) pelindoVoyageSelect.value = oldValue;
        delete pelindoVoyageSelect.dataset.oldValue;
    }

    pelindoKapalSelect.addEventListener('change', function () {
        pelindoVoyageSelect.value = '';
        delete pelindoVoyageSelect.dataset.oldValue;
        updatePelindoVoyages();
    });
    updatePelindoVoyages();

    // Restore section visibility after a validation error, once all scripts are loaded.
    window.addEventListener('DOMContentLoaded', function () {
        if (jenisBiayaSelect.value && selectedJenisBiaya.nama.toLowerCase().includes('tagihan pelindo')) {
            jenisBiayaSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
