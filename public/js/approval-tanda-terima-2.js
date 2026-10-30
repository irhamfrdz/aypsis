(() => {
    'use strict';

    document.querySelectorAll('.approval-goods-open').forEach(button => {
        const dialog = document.getElementById(button.dataset.dialogId);
        if (!dialog) return;
        const form = dialog.querySelector('form');
        const labels = { nama_barang: 'Nama barang *', hs_codes: 'HS Code', jumlah: 'Jumlah', satuan: 'Satuan',
            ukuran: 'Ukuran keramik', meter_kubik: 'Volume (m³)',
            tonase: 'Tonase', keterangan_barang: 'Keterangan barang' };
        const placeholders = { nama_barang: 'Contoh: Mesin industri', hs_codes: 'Contoh: 8479.89 (satu kode per baris)', jumlah: '0',
            satuan: 'Contoh: Unit, koli, karton', ukuran: 'Contoh: 40x40', tonase: 'Contoh: 17.665,78', meter_kubik: 'Contoh: 3.500,25', keterangan_barang: 'Catatan tambahan (opsional)' };
        function formatLocalizedNumber(value) {
            const clean = value.replace(/[^\d.,]/g, '');
            const comma = clean.indexOf(',');
            const decimalDot = comma === -1 && /^\d+\.\d{1,2}$/.test(clean);
            const separator = comma >= 0 ? comma : (decimalDot ? clean.indexOf('.') : -1);
            const whole = (separator >= 0 ? clean.slice(0, separator) : clean).replace(/\D/g, '');
            const fraction = separator >= 0 ? clean.slice(separator + 1).replace(/\D/g, '') : null;
            if (!whole && fraction === null) return '';
            const grouped = (whole || '0').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return grouped + (fraction !== null ? ',' + fraction : '');
        }
        function formatNumberInput(input) {
            const cursor = input.selectionStart;
            const previousLength = input.value.length;
            const formatted = formatLocalizedNumber(input.value);
            if (formatted === input.value) return;
            input.value = formatted;
            if (cursor !== null && document.activeElement === input) {
                const nextCursor = Math.max(0, Math.min(formatted.length, cursor + formatted.length - previousLength));
                input.setSelectionRange(nextCursor, nextCursor);
            }
        }
        function labelRow(row, section) {
            const headings = Array.from(section.querySelectorAll('thead th'));
            Array.from(row.children).forEach((cell, index) => {
                const input = cell.querySelector('input, textarea');
                const field = input?.name.match(/\[([^\]]+)\]$/)?.[1] || cell.dataset.field;
                cell.dataset.label = field ? labels[field] : (cell.querySelector('button') ? '' : headings[index]?.textContent.trim() || '');
                if (!input) return;
                cell.dataset.field = field;
                input.setAttribute('aria-label', labels[field]);
                input.placeholder = placeholders[field] || (input.type === 'number' ? '0' : 'Opsional');
            });
        }
        function toggleUkuranField(row) {
            const ukuranCell = row.querySelector('[data-field="ukuran"]');
            const namaCell = row.querySelector('[data-field="nama_barang"]');
            if (!ukuranCell || !namaCell) return;
            const namaBarang = namaCell.querySelector('input, textarea')?.value ?? namaCell.textContent;
            ukuranCell.hidden = !namaBarang.toLowerCase().includes('keramik');
        }
        function copyManifestToPerincian(manifestRow, perincianRow, changedField = null) {
            const selector = changedField
                ? `td[data-field="${changedField}"] input, td[data-field="${changedField}"] textarea`
                : 'td[data-field] input, td[data-field] textarea';
            manifestRow.querySelectorAll(selector).forEach(sourceInput => {
                const field = sourceInput.name.match(/\[([^\]]+)\]$/)?.[1];
                const targetInput = perincianRow.querySelector(`td[data-field="${field}"] input, td[data-field="${field}"] textarea`);
                if (targetInput && targetInput.dataset.manuallyEdited !== 'true') {
                    targetInput.value = sourceInput.value;
                }
            });
            toggleUkuranField(perincianRow);
        }
        button.addEventListener('click', () => dialog.showModal());
        dialog.querySelectorAll('.approval-goods-close').forEach(closeButton => {
            closeButton.addEventListener('click', () => dialog.close());
        });
        dialog.addEventListener('close', () => button.focus());

        dialog.querySelectorAll('.approval-goods-section').forEach(section => {
            const rows = section.querySelector('.approval-goods-rows');
            const fieldPrefix = section.dataset.fieldPrefix;
            rows.querySelectorAll('tr').forEach(row => {
                labelRow(row, section);
                toggleUkuranField(row);
                if (fieldPrefix === 'manifest_goods') {
                    row.querySelectorAll('[data-field="tonase"] input, [data-field="meter_kubik"] input')
                        .forEach(formatNumberInput);
                }
            });
            if (fieldPrefix === 'goods' && section.dataset.savedOverride === 'true') {
                const manifestRows = dialog.querySelector('[data-field-prefix="manifest_goods"] .approval-goods-rows').children;
                Array.from(rows.children).forEach((row, index) => {
                    const manifestRow = manifestRows[index];
                    const manifestName = manifestRow?.querySelector('[data-field="nama_barang"] textarea')?.value.trim();
                    if (!manifestName) return;
                    row.querySelectorAll('td[data-field] input, td[data-field] textarea').forEach(input => {
                        const field = input.name.match(/\[([^\]]+)\]$/)?.[1];
                        const sourceInput = manifestRow.querySelector(`td[data-field="${field}"] input, td[data-field="${field}"] textarea`);
                        if (sourceInput && input.value !== sourceInput.value) input.dataset.manuallyEdited = 'true';
                    });
                });
            }

            rows.addEventListener('input', event => {
                if (fieldPrefix === 'manifest_goods' && /\[(tonase|meter_kubik)\]$/.test(event.target.name || '')) {
                    formatNumberInput(event.target);
                }
                if (event.target.name?.endsWith('[nama_barang]')) toggleUkuranField(event.target.closest('tr'));
                if (fieldPrefix === 'goods') {
                    event.target.dataset.manuallyEdited = 'true';
                } else if (fieldPrefix === 'manifest_goods') {
                    const manifestRow = event.target.closest('tr');
                    const rowIndex = Array.from(rows.children).indexOf(manifestRow);
                    const perincianRows = dialog.querySelector('[data-field-prefix="goods"] .approval-goods-rows');
                    const changedField = event.target.name.match(/\[([^\]]+)\]$/)?.[1];
                    if (perincianRows.children[rowIndex]) {
                        copyManifestToPerincian(manifestRow, perincianRows.children[rowIndex], changedField);
                    }
                }
            });
            if (section.dataset.required === 'true') form?.addEventListener('submit', event => {
                if (!rows.querySelector('tr')) {
                    event.preventDefault();
                    alert('Isi barang perincian sebelum menyimpan.');
                }
            });
        });
    });
})();

(() => {
    'use strict';

    const bulkForm = document.getElementById('approval-bulk-export-form');
    const bulkClearForm = document.getElementById('approval-bulk-clear-form');
    const bulkCheckboxes = Array.from(document.querySelectorAll('.approval-bulk-checkbox'));
    const selectAll = document.getElementById('approval-select-all');
    const clearSelection = document.getElementById('approval-clear-selection');
    const receiptType = document.getElementById('type')?.value || 'fcl';
    const selectionKey = `approval-tt2-selection:${window.location.pathname}:${receiptType}`;
    let selected = {};
    try {
        selected = JSON.parse(sessionStorage.getItem(selectionKey) || '{}');
        if (!selected || Array.isArray(selected) || typeof selected !== 'object') selected = {};
    } catch {
        selected = {};
    }
    function saveSelection() {
        try { sessionStorage.setItem(selectionKey, JSON.stringify(selected)); } catch { /* Keep the current page usable without storage. */ }
    }
    bulkCheckboxes.forEach(checkbox => {
        if (Object.hasOwn(selected, checkbox.value)) {
            checkbox.checked = true;
            selected[checkbox.value] = checkbox.dataset.exported === 'true';
        }
    });
    saveSelection();
    function updateBulkSelection() {
        if (!bulkForm) return;
        const values = Object.values(selected);
        const count = values.length;
        const exportedCount = values.filter(Boolean).length;
        bulkForm.querySelector('button').disabled = count === 0;
        bulkForm.querySelector('button span').textContent = `Export Pilihan (${count})`;
        bulkClearForm.querySelector('button').disabled = exportedCount === 0;
        bulkClearForm.querySelector('button span').textContent = `Lepas Tanda Export (${exportedCount})`;
        clearSelection.disabled = count === 0;
        if (selectAll) {
            const currentPageCount = bulkCheckboxes.filter(checkbox => checkbox.checked).length;
            selectAll.checked = bulkCheckboxes.length > 0 && currentPageCount === bulkCheckboxes.length;
            selectAll.indeterminate = currentPageCount > 0 && currentPageCount < bulkCheckboxes.length;
        }
    }
    selectAll?.addEventListener('change', () => {
        const previous = { ...selected };
        bulkCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
            if (checkbox.checked) selected[checkbox.value] = checkbox.dataset.exported === 'true';
            else delete selected[checkbox.value];
        });
        if (Object.keys(selected).length > 300) {
            selected = previous;
            bulkCheckboxes.forEach(checkbox => {
                checkbox.checked = Object.hasOwn(selected, checkbox.value);
            });
            alert('Maksimal 300 tanda terima dapat dipilih sekaligus.');
        }
        saveSelection();
        updateBulkSelection();
    });
    bulkCheckboxes.forEach(checkbox => checkbox.addEventListener('change', () => {
        if (checkbox.checked && Object.keys(selected).length >= 300) {
            checkbox.checked = false;
            alert('Maksimal 300 tanda terima dapat dipilih sekaligus.');
        }
        if (checkbox.checked) selected[checkbox.value] = checkbox.dataset.exported === 'true';
        else delete selected[checkbox.value];
        saveSelection();
        updateBulkSelection();
    }));
    clearSelection?.addEventListener('click', () => {
        selected = {};
        bulkCheckboxes.forEach(checkbox => { checkbox.checked = false; });
        saveSelection();
        updateBulkSelection();
    });
    bulkClearForm?.addEventListener('submit', event => {
        bulkClearForm.querySelectorAll('input[name="ids[]"]').forEach(input => input.remove());
        const exportedIds = Object.keys(selected).filter(id => selected[id]);
        if (exportedIds.length === 0) {
            event.preventDefault();
            return;
        }
        exportedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            bulkClearForm.append(input);
        });
        exportedIds.forEach(id => { selected[id] = false; });
        saveSelection();
    });
    updateBulkSelection();

    document.querySelectorAll('.approval-export-form').forEach(form => {
        form.addEventListener('submit', async event => {
            event.preventDefault();
            const button = form.querySelector('.approval-export-button');
            const label = button.querySelector('span');
            const previousLabel = label.textContent;
            button.disabled = true;
            label.textContent = 'Membuat Excel...';

            try {
                const data = new FormData(form);
                if (form === bulkForm) {
                    data.delete('ids[]');
                    Object.keys(selected).forEach(id => data.append('ids[]', id));
                }
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: data,
                    headers: { Accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' },
                    credentials: 'same-origin',
                });
                if (!response.ok) throw new Error('Export Excel gagal. Coba lagi.');
                const blob = await response.blob();
                const disposition = response.headers.get('Content-Disposition') || '';
                const filename = disposition.match(/filename="?([^";]+)"?/i)?.[1] || 'Manifest.xlsx';
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = filename;
                document.body.append(link);
                link.click();
                link.remove();
                setTimeout(() => URL.revokeObjectURL(url), 1000);

                const rows = form === bulkForm
                    ? bulkCheckboxes.filter(checkbox => checkbox.checked).map(checkbox => checkbox.closest('tr'))
                    : [form.closest('tr')];
                rows.forEach(row => {
                    row.classList.remove('bg-yellow-100', 'hover:bg-yellow-100', 'hover:bg-gray-50/70');
                    row.classList.add('bg-green-100', 'hover:bg-green-100');
                    const actionCell = row.querySelector('td:last-child');
                    actionCell.classList.remove('bg-yellow-200');
                    actionCell.classList.add('bg-green-200');
                    const rowButton = row.querySelector('.approval-export-button');
                    if (rowButton) {
                        rowButton.querySelector('span').textContent = 'Export Lagi';
                        rowButton.classList.remove('border-emerald-300', 'bg-emerald-50', 'text-emerald-800', 'hover:bg-emerald-100');
                        rowButton.classList.add('border-green-500', 'bg-green-300', 'text-green-950', 'hover:bg-green-400');
                    }
                    const resetForm = row.querySelector('.approval-export-reset-form');
                    resetForm?.classList.remove('hidden');
                    resetForm?.classList.add('inline-block');
                    const checkbox = row.querySelector('.approval-bulk-checkbox');
                    if (checkbox) {
                        checkbox.dataset.exported = 'true';
                        if (Object.hasOwn(selected, checkbox.value)) selected[checkbox.value] = true;
                    }
                });
                if (form === bulkForm) Object.keys(selected).forEach(id => { selected[id] = true; });
                saveSelection();
                updateBulkSelection();
            } catch (error) {
                label.textContent = previousLabel;
                alert(error.message || 'Export Excel gagal. Coba lagi.');
            } finally {
                button.disabled = false;
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
            field.classList.toggle('approval-shipper-name-highlight',
                (name === 'shipper' || name === 'consignee') && value !== null && value !== undefined && String(value).trim() !== '');
            field.classList.toggle('approval-shipper-document-highlight',
                name === 'document_ppftz_03' && value !== null && value !== undefined && String(value).trim() !== '');
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
            save.textContent = button.dataset.additional === 'true' ? 'Tambah Shipper' : 'Simpan Shipper';
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
