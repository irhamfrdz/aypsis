(() => {
    'use strict';

    document.querySelectorAll('.approval-goods-open').forEach(button => {
        const dialog = document.getElementById(button.dataset.dialogId);
        if (!dialog) return;

        button.addEventListener('click', () => dialog.showModal());
        dialog.querySelectorAll('.approval-goods-close').forEach(closeButton => {
            closeButton.addEventListener('click', () => dialog.close());
        });
        dialog.addEventListener('close', () => button.focus());
    });
})();

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
    const previewFields = dialog.querySelectorAll('[data-shipper-field]');
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
        previewFields.forEach(field => {
            const name = field.dataset.shipperField;
            const value = data?.[name];
            field.textContent = name === 'status' && value !== null && value !== undefined
                ? (String(value) === '1' || value === true ? 'Aktif' : 'Tidak aktif')
                : (value === null || value === undefined || value === '' ? '-' : String(value));
        });
    }

    function choose(option) {
        stopSearch();
        selectedId.value = option.real_id;
        search.value = option.text;
        selection.textContent = 'Dipilih: ' + (option.display_text || option.text);
        selection.className = 'mt-2 text-xs font-medium text-indigo-700';
        showPreview(option);
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
            showPreview(JSON.parse(button.dataset.shipperDetails || 'null'));
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
