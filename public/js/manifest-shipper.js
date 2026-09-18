(() => {
    'use strict';
    const dialog = document.getElementById('manifest-shipper-dialog');
    if (!dialog) return;
    const form = document.getElementById('manifest-shipper-form');
    const search = document.getElementById('manifest-shipper-search');
    const options = document.getElementById('manifest-shipper-options');
    const save = document.getElementById('manifest-shipper-save');
    const fields = document.getElementById('manifest-shipper-fields');
    const selection = document.getElementById('manifest-shipper-selection');
    const message = document.getElementById('manifest-shipper-message');
    const cargoFields = document.getElementById('manifest-shipper-cargo');
    const allocationFields = ['tonnage', 'volume', 'kuantitas', 'tonnage_perincian', 'volume_perincian'];
    let adding = false;
    const autofill = {alamat: 'alamat_pengirim', consignee: 'penerima', notify_party: 'notify_party', alamat_notify_party: 'alamat_notify_party'};
    let shipperId = null;
    let updateUrl = '';
    let timer;
    let pendingSearch;
    let generation = 0;
    let saving = false;
    let opener;

    function stopSearch() {
        clearTimeout(timer);
        pendingSearch?.abort();
        generation++;
    }

    function hideOptions() {
        options.hidden = true;
        search.setAttribute('aria-expanded', 'false');
    }

    function showMessage(text) {
        message.textContent = text;
        message.hidden = !text;
    }

    function selectShipper(option) {
        stopSearch();
        shipperId = option.real_id;
        search.value = option.text;
        // The edit form only overwrites fields supplied by the selected master record.
        Object.entries(autofill).forEach(([source, target]) => {
            if (option[source]) form.elements.namedItem(target).value = option[source];
        });
        selection.textContent = `Dipilih: ${option.display_text || option.text}`;
        hideOptions();
        showMessage('');
        save.disabled = !shipperId;
    }

    async function fetchShippers() {
        stopSearch();
        const requestGeneration = generation;
        pendingSearch = new AbortController();
        options.replaceChildren();
        options.hidden = false;
        options.textContent = 'Mencari shipper...';
        search.setAttribute('aria-expanded', 'true');
        try {
            const url = new URL(dialog.dataset.searchUrl, window.location.href);
            url.searchParams.set('q', search.value.trim());
            const response = await fetch(url, {headers: {Accept: 'application/json'}, signal: pendingSearch.signal});
            if (!response.ok) throw new Error('Daftar shipper gagal dimuat. Ketik kembali untuk mencoba lagi.');
            const data = await response.json();
            if (requestGeneration !== generation || !dialog.open) return;
            if (!Array.isArray(data)) throw new Error('Daftar shipper tidak dapat dibaca. Muat ulang halaman.');
            options.replaceChildren();
            if (!data.length) options.textContent = 'Shipper tidak ditemukan. Coba nama lain.';
            data.forEach(option => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'manifest-shipper-option';
                button.textContent = option.display_text || option.text;
                button.addEventListener('click', () => selectShipper(option));
                options.append(button);
            });
        } catch (error) {
            if (error.name === 'AbortError' || requestGeneration !== generation) return;
            hideOptions();
            showMessage(error.message);
        }
    }

    document.querySelectorAll('.manifest-shipper-open').forEach(button => {
        button.addEventListener('click', () => {
            stopSearch();
            const manifest = JSON.parse(button.dataset.manifest);
            opener = button;
            updateUrl = button.dataset.updateUrl;
            adding = button.dataset.mode === 'add';
            // Require a selection so a legacy name or nickname is never mistaken for a master ID.
            shipperId = null;
            form.reset();
            cargoFields.hidden = !adding;
            cargoFields.disabled = !adding;
            document.getElementById('manifest-shipper-title').textContent = adding ? 'Tambah Shipper FCL Booking' : 'Pilih Shipper Manifest';
            save.textContent = adding ? 'Tambah Shipper' : 'Simpan Shipper';
            if (adding) {
                allocationFields.forEach(name => {
                    const available = Number(manifest[name] || 0);
                    form.elements.namedItem(name).max = available;
                    document.getElementById(`manifest-available-${name}`).textContent = `(tersedia: ${available})`;
                });
            }
            fields.disabled = false;
            save.disabled = true;
            search.value = manifest.pengirim || '';
            Object.values(autofill).forEach(name => {form.elements.namedItem(name).value = manifest[name] || '';});
            document.getElementById('manifest-shipper-context').textContent = `BL: ${manifest.nomor_bl || '-'} · Kontainer: ${manifest.nomor_kontainer || '-'}`;
            selection.textContent = 'Pilih data dari hasil pencarian shipper.';
            showMessage('');
            hideOptions();
            dialog.showModal();
            search.focus();
            search.select();
            fetchShippers();
        });
    });

    search.addEventListener('input', () => {
        stopSearch();
        shipperId = null;
        save.disabled = true;
        selection.textContent = 'Pilih data dari hasil pencarian shipper.';
        hideOptions();
        timer = setTimeout(fetchShippers, 250);
    });
    search.addEventListener('focus', () => {if (dialog.open && !saving) fetchShippers();});
    search.addEventListener('keydown', event => {
        if (event.key === 'Enter') {event.preventDefault(); return;}
        if (event.key === 'ArrowDown' && !options.hidden) {
            event.preventDefault();
            options.querySelector('button')?.focus();
        }
    });
    dialog.addEventListener('click', event => {
        if (!options.contains(event.target) && event.target !== search) hideOptions();
    });
    dialog.addEventListener('cancel', event => {if (saving) event.preventDefault();});
    dialog.addEventListener('close', () => {stopSearch(); opener?.focus();});
    dialog.querySelectorAll('.manifest-shipper-close').forEach(button => {
        button.addEventListener('click', () => {if (!saving) dialog.close();});
    });
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (!shipperId || saving) return;
        const payload = {shipper_id: shipperId};
        if (adding) {
            [...allocationFields, 'nomor_bl', 'nama_barang', 'nomor_tanda_terima'].forEach(name => {
                payload[name] = form.elements.namedItem(name).value;
            });
        }
        Object.values(autofill).forEach(name => {payload[name] = form.elements.namedItem(name).value;});
        saving = true;
        stopSearch();
        hideOptions();
        fields.disabled = true;
        save.textContent = 'Menyimpan...';
        showMessage('');
        try {
            const response = await fetch(updateUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
                body: JSON.stringify(payload),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) {
                throw new Error(data.errors ? Object.values(data.errors).flat().join(' ') : data.message || 'Shipper gagal disimpan. Periksa sesi login atau coba kembali.');
            }
            // Keep the current filters and pagination, and refresh all related columns and document links.
            window.location.reload();
        } catch (error) {
            showMessage(error.message || 'Tidak dapat terhubung ke server. Silakan coba kembali.');
            saving = false;
            fields.disabled = false;
            save.disabled = false;
            save.textContent = adding ? 'Tambah Shipper' : 'Simpan Shipper';
        }
    });
})();
