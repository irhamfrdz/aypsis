document.querySelectorAll('.sugeng-sheet').forEach(table => {
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
});
