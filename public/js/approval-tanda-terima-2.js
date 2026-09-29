(() => {
    'use strict';

    const dialog = document.getElementById('approval-shipper-dialog');
    if (!dialog) return;

    const form = document.getElementById('approval-shipper-form');
    const search = document.getElementById('approval-shipper-search');
    const options = document.getElementById('approval-shipper-options');
    const selectedId = document.getElementById('approval-shipper-id');
    const selection = document.getElementById('approval-shipper-selection');
    const save = document.getElementById('approval-shipper-save');
    const preview = {
        alamat: document.getElementById('approval-shipper-alamat'),
        consignee: document.getElementById('approval-shipper-consignee'),
        notify: document.getElementById('approval-shipper-notify'),
        notifyAddress: document.getElementById('approval-shipper-notify-address'),
    };
    let timer;
    let pendingSearch;
    let generation = 0;
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

    function showPreview(data) {
        preview.alamat.textContent = data.alamat || '-';
        preview.consignee.textContent = data.consignee || '-';
        preview.notify.textContent = data.notify || '-';
        preview.notifyAddress.textContent = data.notifyAddress || '-';
    }

    function choose(option) {
        stopSearch();
        selectedId.value = option.real_id;
        search.value = option.text;
        selection.textContent = 'Dipilih: ' + (option.display_text || option.text);
        selection.className = 'mt-2 text-xs font-medium text-indigo-700';
        showPreview({
            alamat: option.alamat,
            consignee: option.consignee,
            notify: option.notify_party,
            notifyAddress: option.alamat_notify_party,
        });
        save.disabled = false;
        hideOptions();
        save.focus();
    }

    async function fetchShippers() {
        stopSearch();
        const currentGeneration = generation;
        pendingSearch = new AbortController();
        options.replaceChildren();
        options.textContent = 'Mencari shipper...';
        options.hidden = false;
        search.setAttribute('aria-expanded', 'true');

        try {
            const url = new URL(dialog.dataset.searchUrl, window.location.href);
            url.searchParams.set('q', search.value.trim());
            const response = await fetch(url, {
                headers: {Accept: 'application/json'},
                signal: pendingSearch.signal,
            });
            if (!response.ok) throw new Error('Daftar shipper gagal dimuat. Coba cari lagi.');
            const records = await response.json();
            if (currentGeneration !== generation || !dialog.open) return;
            if (!Array.isArray(records)) throw new Error('Daftar shipper tidak dapat dibaca.');

            options.replaceChildren();
            if (!records.length) {
                options.textContent = 'Shipper tidak ditemukan. Coba nama lain.';
                return;
            }
            records.forEach(option => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'approval-shipper-option';
                button.textContent = option.display_text || option.text;
                button.addEventListener('click', () => choose(option));
                options.append(button);
            });
        } catch (error) {
            if (error.name === 'AbortError' || currentGeneration !== generation) return;
            options.replaceChildren();
            options.textContent = error.message;
        }
    }

    document.querySelectorAll('.approval-shipper-open').forEach(button => {
        button.addEventListener('click', () => {
            stopSearch();
            opener = button;
            form.action = button.dataset.updateUrl;
            selectedId.value = '';
            save.disabled = true;
            search.value = button.dataset.shipper || '';
            document.getElementById('approval-shipper-context').textContent =
                'Tanda terima: ' + (button.dataset.number || '-') + ' · Pengirim: ' + (button.dataset.sender || '-');
            selection.textContent = button.dataset.shipper
                ? 'Shipper saat ini: ' + button.dataset.shipper + '. Pilih dari hasil pencarian untuk mengubahnya.'
                : 'Pilih shipper dari hasil pencarian.';
            selection.className = 'mt-2 text-xs text-gray-500';
            showPreview({
                alamat: button.dataset.alamat,
                consignee: button.dataset.consignee,
                notify: button.dataset.notify,
                notifyAddress: button.dataset.notifyAddress,
            });
            hideOptions();
            dialog.showModal();
            search.focus();
            search.select();
        });
    });

    search.addEventListener('input', () => {
        stopSearch();
        selectedId.value = '';
        save.disabled = true;
        selection.textContent = 'Pilih shipper dari hasil pencarian.';
        selection.className = 'mt-2 text-xs text-gray-500';
        showPreview({});
        hideOptions();
        timer = setTimeout(fetchShippers, 250);
    });
    search.addEventListener('focus', () => {
        if (dialog.open && options.hidden) fetchShippers();
    });
    search.addEventListener('keydown', event => {
        if (event.key === 'Enter') {
            event.preventDefault();
            return;
        }
        if (event.key === 'ArrowDown' && !options.hidden) {
            event.preventDefault();
            options.querySelector('button')?.focus();
        }
    });
    dialog.addEventListener('click', event => {
        if (!options.contains(event.target) && event.target !== search) hideOptions();
    });
    dialog.addEventListener('close', () => {
        stopSearch();
        opener?.focus();
    });
    dialog.querySelectorAll('.approval-shipper-close').forEach(button => {
        button.addEventListener('click', () => dialog.close());
    });
    form.addEventListener('submit', event => {
        if (!selectedId.value) {
            event.preventDefault();
            search.focus();
            return;
        }
        save.disabled = true;
        save.textContent = 'Menyimpan...';
    });
})();
