(() => {
    'use strict';

    document.querySelectorAll('.approval-goods-open').forEach(button => {
        const dialog = document.getElementById(button.dataset.dialogId);
        if (!dialog) return;
        const rows = dialog.querySelector('.approval-goods-rows');
        const form = dialog.querySelector('form');

        button.addEventListener('click', () => dialog.showModal());
        dialog.querySelectorAll('.approval-goods-close').forEach(closeButton => {
            closeButton.addEventListener('click', () => dialog.close());
        });
        dialog.addEventListener('close', () => button.focus());

        dialog.querySelector('.approval-goods-add')?.addEventListener('click', () => {
            const index = 'new_' + Date.now() + '_' + Math.random().toString(36).slice(2);
            const row = document.createElement('tr');
            const fields = ['nama_barang', 'jumlah', 'satuan', 'ukuran', 'panjang', 'lebar', 'tinggi', 'meter_kubik', 'tonase'];
            fields.forEach(field => {
                const cell = document.createElement('td');
                cell.className = 'px-2 py-2';
                const input = document.createElement('input');
                input.type = ['jumlah', 'panjang', 'lebar', 'tinggi', 'meter_kubik', 'tonase'].includes(field) ? 'number' : 'text';
                input.name = `goods[${index}][${field}]`;
                input.className = 'w-28 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500';
                if (field === 'nama_barang') {
                    input.required = true;
                    input.classList.add('min-w-48');
                }
                if (input.type === 'number') {
                    input.min = '0';
                    input.step = field === 'jumlah' ? '1' : 'any';
                }
                cell.append(input);
                row.append(cell);
            });
            if (dialog.dataset.sourceType !== 'ttsj') {
                const cell = document.createElement('td');
                cell.className = 'px-2 py-2';
                const input = document.createElement('input');
                input.type = 'text';
                input.name = `goods[${index}][keterangan_barang]`;
                input.className = 'w-40 rounded-lg border-gray-300 text-sm';
                cell.append(input);
                row.append(cell);
            }
            const action = document.createElement('td');
            action.className = 'px-2 py-2';
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'approval-goods-remove rounded-lg px-2 py-1 text-xs font-semibold text-red-700 hover:bg-red-50';
            remove.textContent = 'Hapus';
            action.append(remove);
            row.append(action);
            rows.append(row);
            dialog.querySelector('.approval-goods-empty')?.remove();
            row.querySelector('input').focus();
        });
        rows.addEventListener('click', event => {
            if (event.target.closest('.approval-goods-remove')) event.target.closest('tr').remove();
        });
        form?.addEventListener('submit', event => {
            if (!rows.querySelector('tr')) {
                event.preventDefault();
                alert('Tambahkan setidaknya satu barang sebelum menyimpan.');
            }
        });
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
    let pendingDetails;
    let detailsGeneration = 0;
    let opener;

    function stopSearch() {
        clearTimeout(timer);
        pendingSearch?.abort();
        generation++;
    }

    function stopDetails() {
        pendingDetails?.abort();
        detailsGeneration++;
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
        stopDetails();
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
        options.classList.add('approval-shipper-message');
        options.textContent = 'Mencari shipper...';
        options.hidden = false;
        search.setAttribute('aria-expanded', 'true');

        try {
            const url = new URL(dialog.dataset.searchUrl, window.location.href);
            url.searchParams.set('q', search.value.trim());
            const response = await fetch(url, {
                headers: {Accept: 'application/json'},
                cache: 'no-store',
                signal: pendingSearch.signal,
            });
            if (!response.ok) throw new Error('Daftar shipper gagal dimuat. Coba cari lagi.');
            const records = await response.json();
            if (currentGeneration !== generation || !dialog.open) return;
            if (!Array.isArray(records)) throw new Error('Daftar shipper tidak dapat dibaca.');

            options.replaceChildren();
            if (!records.length) {
                options.textContent = 'Shipper atau consignee tidak ditemukan. Coba nama lain.';
                return;
            }
            options.classList.remove('approval-shipper-message');
            records.forEach(option => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'approval-shipper-option';
                const name = document.createElement('span');
                name.className = 'approval-shipper-option-name';
                name.textContent = option.text;
                button.append(name);
                if (option.consignee) {
                    const detail = document.createElement('span');
                    detail.className = 'approval-shipper-option-detail';
                    detail.textContent = 'Consignee: ' + option.consignee;
                    button.append(detail);
                }
                button.addEventListener('click', () => choose(option));
                options.append(button);
            });
        } catch (error) {
            if (error.name === 'AbortError' || currentGeneration !== generation) return;
            options.replaceChildren();
            options.textContent = error.message;
        }
    }

    async function refreshCurrentShipper(id) {
        stopDetails();
        const currentGeneration = detailsGeneration;
        pendingDetails = new AbortController();

        try {
            const url = new URL(dialog.dataset.searchUrl, window.location.href);
            url.searchParams.set('id', id);
            const response = await fetch(url, {
                headers: {Accept: 'application/json'},
                cache: 'no-store',
                signal: pendingDetails.signal,
            });
            if (!response.ok) throw new Error('Data shipper terbaru gagal dimuat. Cari ulang shipper.');
            const records = await response.json();
            if (currentGeneration !== detailsGeneration || !dialog.open) return;

            const current = Array.isArray(records) ? records[0] : null;
            if (!current) {
                search.value = '';
                selection.textContent = 'Shipper saat ini tidak ditemukan di master. Cari shipper lain.';
                showPreview(null);
                fetchShippers();
            } else {
                const previousName = search.value;
                search.value = current.text;
                selection.textContent = 'Shipper saat ini: ' + current.text + '. Pilih dari hasil pencarian untuk mengubahnya.';
                showPreview(current);
                if (previousName !== current.text) fetchShippers();
            }
        } catch (error) {
            if (error.name === 'AbortError' || currentGeneration !== detailsGeneration) return;
            selection.textContent = error.message;
            showPreview(null);
        }
    }

    document.querySelectorAll('.approval-shipper-open').forEach(button => {
        button.addEventListener('click', () => {
            stopSearch();
            stopDetails();
            opener = button;
            form.action = button.dataset.updateUrl;
            selectedId.value = '';
            save.disabled = true;
            search.value = button.dataset.shipper || '';
            document.getElementById('approval-shipper-context').textContent =
                'Tanda terima: ' + (button.dataset.number || '-') + ' · Pengirim: ' + (button.dataset.sender || '-');
            selection.textContent = button.dataset.shipperId
                ? 'Memuat data shipper terbaru...'
                : 'Pilih shipper dari hasil pencarian.';
            selection.className = 'mt-2 text-xs text-gray-500';
            showPreview(null);
            hideOptions();
            dialog.showModal();
            search.focus();
            search.select();
            if (button.dataset.shipperId) refreshCurrentShipper(button.dataset.shipperId);
        });
    });

    search.addEventListener('input', () => {
        stopSearch();
        stopDetails();
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
        stopDetails();
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
