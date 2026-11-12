document.querySelectorAll('.sugeng-sheet').forEach(table => {
    const rows = Array.from(table.tBodies[0]?.rows || []);
    const saved = new WeakMap();
    const busy = new WeakSet();
    const directions = { ArrowLeft: [0, -1], ArrowRight: [0, 1], ArrowUp: [-1, 0], ArrowDown: [1, 0] };

    const valueOf = cell => cell.innerText.replace(/\r\n/g, '\n').replace(/\n$/, '');

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
        if (cell && !saved.has(cell)) saved.set(cell, valueOf(cell));
    });
    table.addEventListener('focusout', event => {
        const cell = event.target.closest('td[contenteditable="true"]');
        if (cell) save(cell);
    });
    table.addEventListener('keydown', event => {
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
        document.execCommand('insertText', false, event.clipboardData.getData('text/plain'));
    });
});
