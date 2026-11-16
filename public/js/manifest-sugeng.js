const initializeManifestSugengSheet = table => {
    const sizeControls = table.closest('.sugeng-sheet-scroll')?.previousElementSibling;
    if (sizeControls?.classList.contains('sugeng-size-controls')) {
        const columns = [...table.querySelectorAll('colgroup col')].slice(1);
        const defaults = columns.map(col => parseInt(col.style.width, 10));
        const columnNames = [...'BCDEFGHIJKLMNOPQRSTUVWXYZ', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG'];
        const columnSelect = sizeControls.querySelector('.sugeng-size-column');
        const widthInput = sizeControls.querySelector('.sugeng-size-width');
        const heightInput = sizeControls.querySelector('.sugeng-size-height');
        const tableHeightInput = sizeControls.querySelector('.sugeng-size-table-height');
        const scrollArea = table.closest('.sugeng-sheet-scroll');
        const storageKey = 'manifest-sugeng-table-size';
        let settings = {};
        try { settings = JSON.parse(localStorage.getItem(storageKey) || '{}') || {}; } catch (_) { settings = {}; }
        const applySizes = () => {
            columns.forEach((col, index) => {
                const width = Number(settings.widths?.[columnNames[index]]) || defaults[index];
                col.style.width = `${width}px`;
            });
            table.style.width = `${44 + columns.reduce((sum, col) => sum + parseInt(col.style.width, 10), 0)}px`;
            const height = Number(settings.height) || 46;
            table.querySelectorAll('tbody tr').forEach(row => {
                const rowHeight = Number(settings.rowHeights?.[`${row.dataset.itemId}-${row.dataset.kind}-${row.dataset.line}`]) || height;
                row.style.height = `${rowHeight}px`;
                [...row.cells].forEach(cell => { cell.style.height = `${rowHeight}px`; });
                row.querySelectorAll('.sheet-cell-content').forEach(content => { content.style.maxHeight = `${Math.max(12, rowHeight - 12)}px`; });
            });
            widthInput.value = parseInt(columns[columnNames.indexOf(columnSelect.value)].style.width, 10);
            heightInput.value = height;
            if (settings.tableHeight) scrollArea.style.height = `${settings.tableHeight}px`;
            else scrollArea.style.removeProperty('height');
            tableHeightInput.value = Math.round(scrollArea.getBoundingClientRect().height);
        };
        const saveSizes = () => { try { localStorage.setItem(storageKey, JSON.stringify(settings)); } catch (_) {} };
        columnSelect.addEventListener('change', () => { widthInput.value = parseInt(columns[columnNames.indexOf(columnSelect.value)].style.width, 10); });
        widthInput.addEventListener('change', () => {
            const width = Number(widthInput.value);
            if (!Number.isInteger(width) || width < 40 || width > 1600) { applySizes(); return; }
            settings.widths ??= {};
            settings.widths[columnSelect.value] = width;
            applySizes(); saveSizes();
        });
        heightInput.addEventListener('change', () => {
            const height = Number(heightInput.value);
            if (!Number.isInteger(height) || height < 24 || height > 500) { applySizes(); return; }
            settings.height = height;
            applySizes(); saveSizes();
        });
        tableHeightInput.addEventListener('change', () => {
            const height = Number(tableHeightInput.value);
            if (!Number.isInteger(height) || height < 200 || height > 2000) { applySizes(); return; }
            settings.tableHeight = height;
            applySizes(); saveSizes();
        });
        scrollArea.nextElementSibling?.classList.contains('sugeng-sheet-resize') && scrollArea.nextElementSibling.addEventListener('pointerdown', event => {
            if (event.button !== 0) return;
            event.preventDefault();
            const startY = event.clientY;
            const initialHeight = scrollArea.getBoundingClientRect().height;
            const move = moveEvent => {
                settings.tableHeight = Math.min(2000, Math.max(200, Math.round(initialHeight + moveEvent.clientY - startY)));
                applySizes();
            };
            const stop = () => { window.removeEventListener('pointermove', move); saveSizes(); };
            window.addEventListener('pointermove', move);
            window.addEventListener('pointerup', stop, { once: true });
        });
        sizeControls.querySelector('.sugeng-size-reset').addEventListener('click', () => {
            settings = {};
            try { localStorage.removeItem(storageKey); } catch (_) {}
            applySizes();
        });
        applySizes();
        table.querySelectorAll('.sheet-column-resize').forEach(handle => {
            handle.addEventListener('pointerdown', event => {
                if (event.button !== 0) return;
                event.preventDefault(); event.stopPropagation();
                const column = handle.dataset.column;
                const index = columnNames.indexOf(column);
                const startX = event.clientX;
                const initialWidth = parseInt(columns[index].style.width, 10);
                const move = moveEvent => {
                    settings.widths ??= {};
                    settings.widths[column] = Math.min(1600, Math.max(40, initialWidth + moveEvent.clientX - startX));
                    applySizes();
                };
                const stop = () => { window.removeEventListener('pointermove', move); saveSizes(); };
                window.addEventListener('pointermove', move);
                window.addEventListener('pointerup', stop, { once: true });
            });
        });
        table.querySelectorAll('tbody .sheet-row-resize').forEach(handle => {
            handle.addEventListener('click', event => event.stopPropagation());
            handle.addEventListener('pointerdown', event => {
                if (event.button !== 0) return;
                event.preventDefault(); event.stopPropagation();
                const row = handle.closest('tr');
                const key = `${row.dataset.itemId}-${row.dataset.kind}-${row.dataset.line}`;
                const startY = event.clientY;
                const initialHeight = row.getBoundingClientRect().height;
                const move = moveEvent => {
                    settings.rowHeights ??= {};
                    settings.rowHeights[key] = Math.min(500, Math.max(24, Math.round(initialHeight + moveEvent.clientY - startY)));
                    applySizes();
                };
                const stop = () => { window.removeEventListener('pointermove', move); saveSizes(); };
                window.addEventListener('pointermove', move);
                window.addEventListener('pointerup', stop, { once: true });
            });
        });
    }
    const rows = Array.from(table.tBodies[0]?.rows || []);
    const saved = new WeakMap();
    const busy = new WeakSet();
    const directions = { ArrowLeft: [0, -1], ArrowRight: [0, 1], ArrowUp: [-1, 0], ArrowDown: [1, 0] };
    let anchor = -1;
    let selectedCell = null;
    const formulaBar = table.closest('.sugeng-sheet-scroll')?.parentElement?.querySelector('.sugeng-formula-bar');
    const address = formulaBar?.querySelector('.sugeng-cell-address');
    const formulaInput = formulaBar?.querySelector('.sugeng-formula-input');
    const formulaSave = formulaBar?.querySelector('.sugeng-formula-save');

    const valueOf = cell => (cell.querySelector('.sheet-cell-content')?.textContent ?? cell.innerText).replace(/\r\n/g, '\n').replace(/\n$/, '');
    const updateDocumentColor = cell => {
        if (cell.dataset.column === 'AA') cell.classList.toggle('sheet-ayp', valueOf(cell).trim().toUpperCase() === 'AYP');
    };
    const showCell = cell => {
        selectedCell = cell;
        if (!cell || !formulaInput) return;
        address.textContent = `${cell.dataset.column || ''}${rows.indexOf(cell.parentElement) + 9}`;
        formulaInput.value = valueOf(cell);
        if (formulaSave) formulaSave.disabled = !cell.isContentEditable;
    };
    const commitFormula = () => {
        if (selectedCell?.isContentEditable && formulaInput && formulaInput.value !== valueOf(selectedCell)) {
            setCell(selectedCell, formulaInput.value);
        }
    };
    const selectedRows = () => rows.filter(row => row.dataset.selected === 'true');
    let draggingId = null;
    let movingRow = false;
    const clearDragStyles = () => table.querySelectorAll('.sheet-dragging, .sheet-drop-before, .sheet-drop-after')
        .forEach(row => row.classList.remove('sheet-dragging', 'sheet-drop-before', 'sheet-drop-after'));
    table.addEventListener('dragstart', event => {
        const header = event.target.closest('tbody .sheet-row-number[draggable="true"]');
        if (!header || event.target.closest('.sheet-row-resize') || movingRow) { event.preventDefault(); return; }
        draggingId = header.parentElement.dataset.itemId;
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', draggingId);
        rows.filter(row => row.dataset.itemId === draggingId).forEach(row => row.classList.add('sheet-dragging'));
    });
    table.addEventListener('dragover', event => {
        const target = event.target.closest('tbody tr[data-item-id]');
        if (!draggingId || !target || target.dataset.itemId === draggingId) return;
        event.preventDefault();
        event.dataTransfer.dropEffect = 'move';
        table.querySelectorAll('.sheet-drop-before, .sheet-drop-after')
            .forEach(row => row.classList.remove('sheet-drop-before', 'sheet-drop-after'));
        target.classList.add(event.clientY < target.getBoundingClientRect().top + target.offsetHeight / 2 ? 'sheet-drop-before' : 'sheet-drop-after');
    });
    table.addEventListener('dragleave', event => {
        const target = event.target.closest('tbody tr[data-item-id]');
        if (target && !target.contains(event.relatedTarget)) target.classList.remove('sheet-drop-before', 'sheet-drop-after');
    });
    table.addEventListener('drop', async event => {
        const target = event.target.closest('tbody tr[data-item-id]');
        if (!draggingId || !target || target.dataset.itemId === draggingId) return;
        event.preventDefault();
        const sourceId = draggingId;
        const position = target.classList.contains('sheet-drop-before') ? 'before' : 'after';
        clearDragStyles();
        draggingId = null;
        movingRow = true;
        try {
            const response = await fetch(table.dataset.moveUrl, {
                method: 'PATCH', credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': table.dataset.csrf, Accept: 'application/json' },
                body: JSON.stringify({ source_id: Number(sourceId), target_id: Number(target.dataset.itemId), position }),
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Posisi data gagal disimpan.');
            const sheetRoot = table.closest('.sugeng-sheet-root');
            const scrollArea = table.closest('.sugeng-sheet-scroll');
            const scrollTop = scrollArea.scrollTop;
            const scrollLeft = scrollArea.scrollLeft;
            const pageX = window.scrollX;
            const pageY = window.scrollY;
            const pageResponse = await fetch(window.location.href, {
                credentials: 'same-origin',
                cache: 'no-store',
                headers: { Accept: 'text/html' },
            });
            if (!pageResponse.ok) throw new Error('Posisi tersimpan, tetapi tampilan tabel gagal diperbarui.');
            const page = new DOMParser().parseFromString(await pageResponse.text(), 'text/html');
            const freshRoot = page.querySelector('.sugeng-sheet-root');
            if (!freshRoot) throw new Error('Posisi tersimpan, tetapi tampilan tabel gagal diperbarui.');
            sheetRoot.replaceWith(freshRoot);
            initializeManifestSugengSheet(freshRoot.querySelector('.sugeng-sheet'));
            const freshScrollArea = freshRoot.querySelector('.sugeng-sheet-scroll');
            freshScrollArea.scrollTop = scrollTop;
            freshScrollArea.scrollLeft = scrollLeft;
            freshRoot.querySelector(`tbody tr[data-item-id="${sourceId}"]`)?.setAttribute('data-selected', 'true');
            window.scrollTo(pageX, pageY);
        } catch (error) {
            alert(error.message || 'Posisi data gagal disimpan.');
            movingRow = false;
        }
    });
    table.addEventListener('dragend', () => { draggingId = null; clearDragStyles(); });
    const markControls = table.closest('.sugeng-sheet-scroll')?.parentElement?.querySelector('.sugeng-mark-controls');
    markControls?.addEventListener('click', async event => {
        const button = event.target.closest('button[data-mark]');
        if (!button || !table.dataset.markUrl || button.disabled) return;
        const selection = selectedRows().filter(row => row.dataset.kind === 'detail');
        if (!selection.length) {
            alert('Pilih nomor baris barang yang ingin ditandai.');
            return;
        }
        const buttons = markControls.querySelectorAll('button[data-mark]');
        buttons.forEach(control => { control.disabled = true; });
        try {
            const response = await fetch(table.dataset.markUrl, {
                method: 'PATCH', credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': table.dataset.csrf, Accept: 'application/json' },
                body: JSON.stringify({
                    rows: selection.map(row => ({ id: Number(row.dataset.itemId), line: Number(row.dataset.line) })),
                    mark: button.dataset.mark,
                }),
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Tanda warna gagal disimpan.');
            selection.forEach(row => {
                if (result.mark) row.dataset.mark = result.mark;
                else delete row.dataset.mark;
            });
        } catch (error) {
            alert(error.message || 'Tanda warna gagal disimpan.');
        } finally {
            buttons.forEach(control => { control.disabled = false; });
        }
    });
    const selectRows = (indices, keep = false) => {
        if (!keep) rows.forEach(row => delete row.dataset.selected);
        indices.forEach(index => { if (rows[index]) rows[index].dataset.selected = 'true'; });
    };
    const setCell = (cell, value) => {
        if (!cell?.isContentEditable) return;
        if (!saved.has(cell)) saved.set(cell, valueOf(cell));
        const content = cell.querySelector('.sheet-cell-content');
        if (content) content.textContent = value;
        else cell.textContent = value;
        updateDocumentColor(cell);
        if (cell === selectedCell && formulaInput) formulaInput.value = value;
        save(cell);
    };

    async function save(cell) {
        if (!cell.dataset.saveUrl || busy.has(cell)) return;
        const value = valueOf(cell);
        if (value === saved.get(cell)) return;
        busy.add(cell);
        cell.dataset.state = 'saving';
        try {
            const response = await fetch(cell.dataset.saveUrl, {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': table.dataset.csrf,
                    Accept: 'application/json',
                },
                body: JSON.stringify({ kind: cell.dataset.kind, line: Number(cell.dataset.line), column: cell.dataset.column, value }),
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Sel gagal disimpan.');
            saved.set(cell, result.value ?? '');
            cell.dataset.state = 'saved';
            cell.title = 'Tersimpan';
            setTimeout(() => { if (cell.dataset.state === 'saved') delete cell.dataset.state; }, 1400);
        } catch (error) {
            cell.dataset.state = 'error';
            cell.title = error.message || 'Sel gagal disimpan.';
            alert(cell.title);
        } finally {
            busy.delete(cell);
            if (cell.dataset.state !== 'error' && valueOf(cell) !== saved.get(cell)) save(cell);
        }
    }

    table.addEventListener('focusin', event => {
        const cell = event.target.closest('td[contenteditable="true"]');
        if (cell) {
            showCell(cell);
            if (!saved.has(cell)) saved.set(cell, valueOf(cell));
        }
    });
    table.addEventListener('focusout', event => {
        const cell = event.target.closest('td[contenteditable="true"]');
        if (cell) save(cell);
    });
    table.addEventListener('click', event => {
        const cell = event.target.closest('tbody td[data-column]');
        if (cell) showCell(cell);
    });
    table.addEventListener('input', event => {
        const cell = event.target.closest('tbody td[data-column]');
        if (cell) updateDocumentColor(cell);
        if (cell === selectedCell && formulaInput) formulaInput.value = valueOf(cell);
    });
    formulaSave?.addEventListener('click', commitFormula);
    formulaInput?.addEventListener('blur', commitFormula);
    formulaInput?.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            formulaInput.value = selectedCell ? valueOf(selectedCell) : '';
            formulaInput.blur();
        } else if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
            event.preventDefault();
            commitFormula();
        }
    });
    table.addEventListener('keydown', event => {
        const rowHeader = event.target.closest('tbody .sheet-row-number');
        if (rowHeader && (event.key === ' ' || event.key === 'Enter')) {
            event.preventDefault();
            rowHeader.click();
            return;
        }
        if (event.altKey || event.ctrlKey || event.metaKey) return;
        const cell = event.target.closest('td[contenteditable="true"]');
        if (!cell) return;
        const move = event.key === 'Enter' ? [event.shiftKey ? -1 : 1, 0] : directions[event.key];
        if (!move) return;
        const nextCell = rows[rows.indexOf(cell.parentElement) + move[0]]?.cells[cell.cellIndex + move[1]];
        if (!nextCell?.isContentEditable) return;
        event.preventDefault();
        nextCell.focus();
        nextCell.scrollIntoView({ block: 'nearest', inline: 'nearest' });
    });
    table.addEventListener('paste', event => {
        const cell = event.target.closest('td[contenteditable="true"]');
        if (!cell) return;
        event.preventDefault();
        const text = event.clipboardData.getData('text/plain').replace(/\r\n/g, '\n').replace(/\n$/, '');
        const values = text.split('\n').map(line => line.split('\t'));
        if (values.length === 1 && values[0].length === 1) {
            document.execCommand('insertText', false, text);
            return;
        }
        const startRow = rows.indexOf(cell.parentElement);
        const startColumn = cell.cellIndex;
        values.forEach((line, rowOffset) => line.forEach((value, columnOffset) => {
            setCell(rows[startRow + rowOffset]?.cells[startColumn + columnOffset], value);
        }));
    });
    table.addEventListener('click', event => {
        const header = event.target.closest('tbody .sheet-row-number');
        if (header) {
            header.focus();
            const index = rows.indexOf(header.parentElement);
            if (event.shiftKey && anchor >= 0) {
                selectRows(Array.from({ length: Math.abs(index - anchor) + 1 }, (_, offset) => Math.min(index, anchor) + offset));
            } else if (event.ctrlKey || event.metaKey) {
                header.parentElement.dataset.selected === 'true'
                    ? delete header.parentElement.dataset.selected
                    : selectRows([index], true);
                anchor = index;
            } else {
                selectRows([index]);
                anchor = index;
            }
        } else if (event.target.closest('tbody td[contenteditable="true"]')) {
            rows.forEach(row => delete row.dataset.selected);
        }
    });
    table.addEventListener('copy', event => {
        const selection = selectedRows();
        if (!selection.length) return;
        event.preventDefault();
        event.clipboardData.setData('text/plain', selection.map(row =>
            Array.from(row.cells).slice(1).map(cell => valueOf(cell).replace(/\t/g, ' ').replace(/\n/g, ' ')).join('\t')
        ).join('\n'));
    });
    table.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            rows.forEach(row => delete row.dataset.selected);
            return;
        }
        if ((event.key === 'Delete' || event.key === 'Backspace') && !event.ctrlKey && !event.metaKey) {
            const selection = selectedRows();
            if (selection.length) {
                event.preventDefault();
                if (table.dataset.clearing === 'true') return;
                const payload = selection.map(row => ({
                    id: Number(row.dataset.itemId), kind: row.dataset.kind, line: Number(row.dataset.line),
                }));
                table.dataset.clearing = 'true';
                fetch(table.dataset.clearUrl, {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': table.dataset.csrf, Accept: 'application/json' },
                    body: JSON.stringify({ rows: payload }),
                }).then(async response => {
                    const result = await response.json();
                    if (!response.ok) throw new Error(result.message || 'Baris gagal dikosongkan.');
                    selection.forEach(row => Array.from(row.cells).slice(1).forEach(cell => {
                        const content = cell.querySelector('.sheet-cell-content');
                        if (content) content.textContent = '';
                        else cell.textContent = '';
                        updateDocumentColor(cell);
                        saved.set(cell, '');
                    }));
                }).catch(error => alert(error.message || 'Baris gagal dikosongkan.'))
                    .finally(() => { delete table.dataset.clearing; });
                return;
            }
        }
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'a' && table.contains(document.activeElement)) {
            event.preventDefault();
            selectRows(rows.map((_, index) => index));
        }
    });
};

document.querySelectorAll('.sugeng-sheet').forEach(initializeManifestSugengSheet);

(() => {
    const dialog = document.getElementById('manifest-finish-dialog');
    const form = document.getElementById('manifest-finish-form');
    const ship = document.getElementById('manifest-finish-ship');
    const voyage = document.getElementById('manifest-finish-voyage');
    const help = document.getElementById('manifest-finish-help');
    let requestNumber = 0;
    let lastRequestedShip = null;

    if (window.jQuery?.fn?.select2 && ship && voyage) {
        const $dialog = window.jQuery(dialog);
        window.jQuery(ship).select2({
            width: '100%',
            placeholder: ship.dataset.placeholder,
            allowClear: true,
            dropdownParent: $dialog,
            minimumResultsForSearch: 0,
        });
        window.jQuery(voyage).select2({
            width: '100%',
            placeholder: voyage.dataset.placeholder,
            allowClear: true,
            dropdownParent: $dialog,
            minimumResultsForSearch: 0,
        });
    }

    document.getElementById('manifest-finish-open')?.addEventListener('click', () => {
        const visibleIds = new Set(Array.from(document.querySelectorAll('.sugeng-sheet tbody tr[data-item-id]')).map(row => row.dataset.itemId));
        const count = document.getElementById('manifest-finish-count');
        if (count) count.textContent = `${visibleIds.size} data manifest pada halaman ini akan ditugaskan.`;
        dialog?.showModal();
        window.setTimeout(() => window.jQuery?.(ship).select2?.('open'), 80);
    });
    document.getElementById('manifest-finish-cancel')?.addEventListener('click', () => dialog?.close());
    const loadVoyages = async () => {
        const selectedShip = ship.value;
        if (selectedShip === lastRequestedShip) return;
        lastRequestedShip = selectedShip;
        const currentRequest = ++requestNumber;
        voyage.replaceChildren(new Option(selectedShip ? 'Memuat voyage...' : '-- Pilih kapal terlebih dahulu --', ''));
        voyage.disabled = true;
        window.jQuery?.(voyage).trigger('change.select2');
        if (!selectedShip) return;
        help.textContent = 'Memuat daftar voyage...';
        try {
            const response = await fetch(`${dialog.dataset.voyagesUrl}/${encodeURIComponent(selectedShip)}`, {
                credentials: 'same-origin', headers: { Accept: 'application/json' },
            });
            if (!response.ok) throw new Error('Daftar voyage gagal dimuat.');
            const data = await response.json();
            if (currentRequest !== requestNumber) return;
            const voyages = Array.isArray(data.voyages) ? data.voyages : [];
            voyage.replaceChildren(new Option('-- Pilih voyage --', ''));
            voyages.forEach(value => voyage.add(new Option(value, value)));
            voyage.disabled = voyages.length === 0;
            window.jQuery?.(voyage).val(null).trigger('change.select2');
            help.textContent = voyages.length ? `${voyages.length} voyage tersedia.` : 'Tidak ada voyage untuk kapal ini.';
        } catch (error) {
            if (currentRequest !== requestNumber) return;
            voyage.replaceChildren(new Option('Gagal memuat voyage', ''));
            help.textContent = error.message;
        }
    };
    ship?.addEventListener('change', loadVoyages);
    window.jQuery?.(ship).on('select2:select select2:clear', loadVoyages);
    form?.addEventListener('submit', event => {
        form.querySelectorAll('input[name="ids[]"]').forEach(input => input.remove());
        const table = document.querySelector('.sugeng-sheet');
        const ids = [...new Set(Array.from(table?.querySelectorAll('tbody tr[data-item-id]') || []).map(row => row.dataset.itemId))];
        if (!ids.length) {
            event.preventDefault();
            alert('Tidak ada data manifest pada halaman ini.');
            return;
        }
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.append(input);
        });
    });
})();
