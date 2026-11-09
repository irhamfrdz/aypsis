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

    const pelindoKapalRows = document.getElementById('pelindo_kapal_rows');
    let pelindoNextRowIndex = Date.now();

    function updatePelindoVoyages(row) {
        const kapalSelect = row.querySelector('.pelindo-kapal-select');
        const voyageSelect = row.querySelector('.pelindo-voyage-select');
        const oldValue = voyageSelect.dataset.oldValue || voyageSelect.value;
        voyageSelect.replaceChildren(new Option(
            kapalSelect.value ? '-- Pilih Voyage --' : '-- Pilih Kapal Terlebih Dahulu --', ''
        ));
        const voyages = [...new Set(pelindoManifestVoyages
            .filter(item => item.nama_kapal === kapalSelect.value)
            .map(item => item.no_voyage))];
        voyages.forEach(voyage => voyageSelect.add(new Option(voyage, voyage)));
        if (voyages.includes(oldValue)) voyageSelect.value = oldValue;
        delete voyageSelect.dataset.oldValue;
    }

    pelindoKapalRows.addEventListener('change', function (event) {
        if (!event.target.matches('.pelindo-kapal-select')) return;
        const row = event.target.closest('.pelindo-kapal-row');
        const voyageSelect = row.querySelector('.pelindo-voyage-select');
        voyageSelect.value = '';
        delete voyageSelect.dataset.oldValue;
        updatePelindoVoyages(row);
    });

    pelindoKapalRows.addEventListener('click', function (event) {
        if (event.target.matches('.pelindo-remove-kapal')) {
            event.target.closest('.pelindo-kapal-row').remove();
        }
    });

    document.getElementById('pelindo_tambah_kapal').addEventListener('click', function () {
        const index = pelindoNextRowIndex++;
        const row = document.createElement('div');
        row.className = 'pelindo-kapal-row mt-4 grid grid-cols-1 gap-4 border-t border-blue-200 pt-4 md:grid-cols-2';
        row.innerHTML = `
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Kapal <span class="text-red-500">*</span></label>
                <select name="pelindo_tambahan[${index}][kapal]" class="pelindo-kapal-select w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Pilih Kapal --</option>
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Voyage <span class="text-red-500">*</span></label>
                <select name="pelindo_tambahan[${index}][voyage]" class="pelindo-voyage-select w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                </select>
            </div>
            <button type="button" class="pelindo-remove-kapal justify-self-start text-sm font-medium text-red-600 hover:text-red-800 md:col-span-2">Hapus kapal</button>`;
        const kapalSelect = row.querySelector('.pelindo-kapal-select');
        [...new Set(pelindoManifestVoyages.map(item => item.nama_kapal))]
            .forEach(kapal => kapalSelect.add(new Option(kapal, kapal)));
        pelindoKapalRows.append(row);
    });

    pelindoKapalRows.querySelectorAll('.pelindo-kapal-row').forEach(updatePelindoVoyages);

    // Restore section visibility after a validation error, once all scripts are loaded.
    window.addEventListener('DOMContentLoaded', function () {
        if (jenisBiayaSelect.value && selectedJenisBiaya.nama.toLowerCase().includes('tagihan pelindo')) {
            jenisBiayaSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
