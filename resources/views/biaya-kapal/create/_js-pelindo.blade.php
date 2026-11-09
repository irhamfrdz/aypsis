    const pelindoManifestVoyages = @json($pelindoManifestVoyages);
    const pelindoKapalSelect = document.getElementById('pelindo_kapal');
    const pelindoVoyageSelect = document.getElementById('pelindo_voyage');
    const pelindoJenisTagihanSelect = document.getElementById('pelindo_jenis_tagihan');
    const pelindoBarangSelect = document.getElementById('pelindo_barang');
    const pelindoJumlahInput = document.getElementById('pelindo_jumlah');
    const pelindoBiayaInput = document.getElementById('pelindo_biaya');
    const pelindoBarangOptions = @json($pelindoBarangOptions);
    let oldPelindoBarang = @json(old('pelindo_barang'));

    function updatePelindoBarang() {
        const selectedKegiatan = pelindoJenisTagihanSelect.value;
        const previousBarang = pelindoBarangSelect.value || oldPelindoBarang;
        pelindoBarangSelect.replaceChildren(new Option(
            selectedKegiatan ? '-- Pilih Barang --' : '-- Pilih Jenis Tagihan Terlebih Dahulu --', ''
        ));
        pelindoBarangOptions
            .filter(option => option.kegiatan === selectedKegiatan)
            .forEach(option => pelindoBarangSelect.add(new Option(option.label, option.value)));
        if ([...pelindoBarangSelect.options].some(option => option.value === previousBarang)) {
            pelindoBarangSelect.value = previousBarang;
        }
        oldPelindoBarang = null;
    }

    pelindoJenisTagihanSelect.addEventListener('change', function () {
        pelindoBarangSelect.value = '';
        updatePelindoBarang();
    });
    updatePelindoBarang();

    function updatePelindoNominal() {
        const jumlah = Number(pelindoJumlahInput.value);
        const biaya = Number(pelindoBiayaInput.value);
        nominalInput.value = jumlah > 0 && biaya >= 0 && pelindoBiayaInput.value !== ''
            ? (jumlah * biaya).toLocaleString('id-ID')
            : '';
    }

    pelindoJumlahInput.addEventListener('input', updatePelindoNominal);
    pelindoBiayaInput.addEventListener('input', updatePelindoNominal);

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
