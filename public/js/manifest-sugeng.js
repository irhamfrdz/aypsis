document.querySelectorAll('.sugeng-sheet').forEach(table => {
    const rows = Array.from(table.tBodies[0]?.rows || []);
    const saved = new WeakMap();
    const busy = new WeakSet();
    const directions = { ArrowLeft: [0, -1], ArrowRight: [0, 1], ArrowUp: [-1, 0], ArrowDown: [1, 0] };
    let anchor = -1;
    let selectedCell = null;
    const formulaBar = table.parentElement.previousElementSibling;
    const address = formulaBar?.querySelector('.sugeng-cell-address');
    const formulaInput = formulaBar?.querySelector('.sugeng-formula-input');
    const formulaSave = formulaBar?.querySelector('.sugeng-formula-save');

    const valueOf = cell => (cell.querySelector('.sheet-cell-content')?.textContent ?? cell.innerText).replace(/\r\n/g, '\n').replace(/\n$/, '');
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
