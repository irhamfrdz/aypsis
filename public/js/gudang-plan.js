(() => {
    'use strict';
    const config = window.gudangPlanConfig;
    if (!config) return;
    const $ = id => document.getElementById(id);
    const editingLayout = config.mode === 'layout';
    let state = config.state;
    let layout = structuredClone(state.layout || {blocks: []});
    let currentBlock = layout.blocks[0]?.code || '';
    let currentTier = 1;
    let selected = null;
    let dirty = false;
    let busy = false;
    let conflicted = false;
    let draggedKey = null;
    const pad = value => String(value).padStart(2, '0');
    // Existing stored bay/row/tier coordinates map to slot/baris/tingkat in the yard UI.
    const locationCode = p => `${p.block}-S${pad(p.bay)}-B${pad(p.row)}-T${pad(p.tier)}`;
    const slotRange = p => p.span === 2 ? `${pad(p.bay)}–${pad(p.bay + 1)}` : pad(p.bay);
    const locationDescription = p => `Area ${p.block} / Slot ${slotRange(p)} / Baris ${pad(p.row)} / Tingkat ${pad(p.tier)}${Number(p.tier) === 1 ? ' (dasar)' : ''}`;
    const element = (tag, className, text) => {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    };
    const button = (text, handler, className = 'gp-button') => {
        const node = element('button', className, text);
        node.type = 'button';
        node.addEventListener('click', handler);
        return node;
    };
    const block = () => layout.blocks.find(item => item.code === currentBlock);
    const positionFor = key => state.positions.find(item => item.key === key);
    const containerFor = key => state.containers.find(item => item.key === key);
    const canPlace = () => config.canEdit && config.active && !busy && !conflicted && !!state.layout;
    const at = (code, bay, row, tier) => state.positions.find(p => p.block === code && p.row === row && p.tier === tier && bay >= p.bay && bay < p.bay + p.span);

    function message(text, kind = 'error') {
        $('gp-message').hidden = false;
        $('gp-message').dataset.kind = kind;
        $('gp-message').textContent = text;
        $('gp-message').scrollIntoView({block: 'nearest', behavior: 'smooth'});
    }

    function markDirty() {
        dirty = true;
        $('gp-dirty').textContent = 'Ada perubahan layout yang belum disimpan.';
    }

    function options(select, values, selectedValue) {
        select.replaceChildren();
        values.forEach(value => select.add(new Option(String(value.label ?? value), String(value.value ?? value))));
        if ([...select.options].some(option => option.value === String(selectedValue))) select.value = String(selectedValue);
    }

    async function mutate(url, method, payload) {
        if (busy || conflicted) return false;
        busy = true;
        $('gp-controls').disabled = true;
        message('Menyimpan perubahan...', 'info');
        try {
            const response = await fetch(url, {
                method,
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
                body: JSON.stringify({...payload, version: state.version}),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                if (response.status === 409) conflicted = true;
                const errors = data.errors ? Object.values(data.errors).flat().join('\n') : data.message;
                throw new Error(errors || (response.status === 419 ? 'Sesi berakhir. Muat ulang halaman dan masuk kembali.' : 'Perubahan gagal disimpan. Silakan coba kembali.'));
            }
            if (!Array.isArray(data.positions) || !Array.isArray(data.containers) || !Number.isInteger(data.version)) {
                throw new Error('Respons server tidak sesuai. Muat ulang halaman dan pastikan sesi login masih aktif.');
            }
            state = data;
            layout = structuredClone(state.layout || {blocks: []});
            dirty = false;
            message(editingLayout ? 'Layout gudang berhasil disimpan.' : 'Posisi kontainer berhasil disimpan.', 'success');
            return true;
        } catch (error) {
            message(error.message || 'Tidak dapat terhubung ke server. Periksa koneksi dan coba kembali.');
            return false;
        } finally {
            busy = false;
            $('gp-controls').disabled = conflicted;
            render();
        }
    }

    function stats() {
        const capacity = layout.blocks.reduce((sum, b) => sum + (b.bays * b.rows - b.disabled.length) * b.tiers, 0);
        const used = state.positions.reduce((sum, p) => sum + p.span, 0);
        const unassigned = state.containers.filter(c => !positionFor(c.key)).length;
        $('gp-stats').replaceChildren();
        [[layout.blocks.length, 'Area penumpukan'], [capacity, 'Kapasitas petak 20 kaki'], [state.positions.length, 'Kontainer ditempatkan'], [editingLayout ? Math.max(0, capacity - used) : unassigned, editingLayout ? 'Petak tersisa (semua tingkat)' : 'Belum ditempatkan']].forEach(([value, label]) => {
            const card = element('div', 'gp-stat');
            card.append(element('strong', '', value), element('span', '', label));
            $('gp-stats').append(card);
        });
    }

    function renderEditor() {
        const target = $('gp-block-editor');
        target.replaceChildren();
        layout.blocks.forEach((b, index) => {
            const card = element('div', 'gp-block-config');
            card.dataset.blockIndex = index;
            const codeLabel = element('label', 'gp-label', 'Kode Area');
            const code = element('input', 'gp-input');
            code.value = b.code;
            code.maxLength = 12;
            code.disabled = !config.canEdit;
            code.dataset.field = 'code';
            codeLabel.append(code);
            card.append(codeLabel);
            const dimensions = element('div', 'gp-coordinate-inputs');
            const inputs = {};
            [['bays', 'Jumlah Slot', 40], ['rows', 'Jumlah Baris', 20], ['tiers', 'Maks. Tingkat', 6]].forEach(([key, label, max]) => {
                const input = element('input', 'gp-input');
                input.type = 'number'; input.min = 1; input.max = max; input.value = b[key]; input.disabled = !config.canEdit;
                input.dataset.field = key;
                const wrapper = element('label', '', label);
                wrapper.append(input); dimensions.append(wrapper); inputs[key] = input;
            });
            card.append(dimensions);
            if (config.canEdit) {
                [code, ...Object.values(inputs)].forEach(input => {
                    input.addEventListener('input', markDirty);
                    input.addEventListener('change', () => {if (applyDraft()) {stats(); renderGrid();}});
                });
                const actions = element('div', 'gp-actions');
                actions.append(button('Perbarui Pratinjau', () => {
                    if (!applyDraft()) return;
                    currentBlock = layout.blocks[index].code;
                    render();
                }));
                actions.append(button('Hapus Area', () => {
                    if (!applyDraft()) return;
                    const code = layout.blocks[index].code;
                    if (state.positions.some(p => p.block === code)) {message('Area masih berisi kontainer. Lepaskan atau pindahkan posisinya terlebih dahulu.'); return;}
                    if (!confirm(`Hapus area ${code} dari rancangan layout?`)) return;
                    layout.blocks.splice(index, 1); markDirty(); render();
                }, 'gp-button gp-danger'));
                card.append(actions);
            }
            target.append(card);
        });
        if (!layout.blocks.length) target.append(element('p', 'gp-empty', 'Tambahkan area pertama untuk mulai menyusun layout.'));
        $('gp-dirty').textContent = dirty ? 'Ada perubahan layout yang belum disimpan.' : 'Layout sesuai data tersimpan.';
    }

    // Read every editor before saving so typed values cannot be lost when the preview re-renders.
    function applyDraft() {
        if (!editingLayout || !config.canEdit) return true;
        const next = structuredClone(layout);
        for (const card of $('gp-block-editor').children) {
            if (card.dataset.blockIndex === undefined) continue;
            const index = Number(card.dataset.blockIndex);
            const previous = layout.blocks[index];
            const draft = next.blocks[index];
            draft.code = card.querySelector('[data-field="code"]').value.trim().toUpperCase();
            if (!/^[A-Z0-9_-]{1,12}$/.test(draft.code)) {message('Kode area harus terdiri dari 1–12 huruf A–Z, angka, tanda - atau _.'); return false;}
            for (const [key, max] of [['bays', 40], ['rows', 20], ['tiers', 6]]) {
                const value = Number(card.querySelector(`[data-field="${key}"]`).value);
                if (!Number.isInteger(value) || value < 1 || value > max) {message('Isi jumlah slot 1–40, baris 1–20, dan tingkat maksimum 1–6 dengan bilangan bulat.'); return false;}
                draft[key] = value;
            }
            if (state.positions.some(p => p.block === previous.code && (draft.code !== previous.code || p.bay + p.span - 1 > draft.bays || p.row > draft.rows || p.tier > draft.tiers))) {
                message('Perubahan area mengenai posisi kontainer tersimpan. Pindahkan kontainer terlebih dahulu.'); return false;
            }
            draft.disabled = draft.disabled.filter(c => c.bay <= draft.bays && c.row <= draft.rows);
        }
        if (new Set(next.blocks.map(b => b.code)).size !== next.blocks.length) {message('Kode area harus unik.'); return false;}
        if (next.blocks.reduce((sum, b) => sum + b.bays * b.rows, 0) > 2000) {message('Maksimal 2.000 petak dasar untuk seluruh area gudang.'); return false;}
        const activeIndex = layout.blocks.findIndex(b => b.code === currentBlock);
        layout = next;
        if (activeIndex >= 0) currentBlock = layout.blocks[activeIndex].code;
        return true;
    }

    function renderGrid() {
        if (!block()) currentBlock = layout.blocks[0]?.code || '';
        const active = block();
        $('gp-block-tabs').replaceChildren();
        layout.blocks.forEach(b => {
            const tab = button(`Area ${b.code} · ${b.bays} slot × ${b.rows} baris`, () => {if (!applyDraft()) return; currentBlock = b.code; currentTier = 1; renderGrid();});
            tab.setAttribute('aria-pressed', String(currentBlock === b.code));
            $('gp-block-tabs').append(tab);
        });
        const root = $('gp-grid'); root.replaceChildren();
        if (!active) {
            options($('gp-view-tier'), [], null);
            root.append(element('p', 'gp-empty', 'Layout belum diatur. Tambahkan dan simpan area melalui halaman Atur Layout terlebih dahulu.'));
            return;
        }
        currentTier = Math.min(currentTier, active.tiers);
        options($('gp-view-tier'), Array.from({length: active.tiers}, (_, i) => ({value: i + 1, label: i === 0 ? '01 (dasar)' : pad(i + 1)})), currentTier);
        const grid = element('div', 'gp-grid-inner');
        grid.style.gridTemplateColumns = `42px repeat(${active.rows}, 132px)`;
        grid.style.gridTemplateRows = `28px repeat(${active.bays}, 70px)`;
        const corner = element('div', 'gp-grid-label', 'Slot ↓'); grid.append(corner);
        for (let row = 1; row <= active.rows; row++) grid.append(element('div', 'gp-grid-label', `Baris ${pad(row)}`));
        for (let bay = 1; bay <= active.bays; bay++) {
            const label = element('div', 'gp-grid-label', pad(bay));
            label.style.gridColumn = '1'; label.style.gridRow = String(bay + 1); grid.append(label);
            for (let row = 1; row <= active.rows; row++) {
                const placed = at(active.code, bay, row, currentTier);
                if (placed && placed.bay !== bay) continue;
                const disabled = active.disabled.some(cell => cell.bay === bay && cell.row === row);
                const cell = button('', () => clickCell(active, bay, row, placed, disabled), 'gp-cell');
                cell.style.gridColumn = String(row + 1);
                cell.style.gridRow = `${bay + 1} / span ${placed?.span || 1}`;
                const address = {block: active.code, bay, row, tier: currentTier, span: placed?.span || 1};
                const coordinates = `${locationCode(address)} · ${locationDescription(address)}`;
                if (placed) {
                    cell.classList.add('gp-filled', placed.stale ? 'gp-stale' : `gp-${placed.source}`);
                    if (placed.key === selected) cell.classList.add('gp-selected');
                    cell.append(element('strong', '', placed.container_number), element('span', '', `${placed.span * 20} kaki · Slot ${slotRange(placed)}`));
                    cell.title = `${placed.container_number} · ${coordinates}${placed.stale ? ' · Perlu diperiksa' : ''}`;
                    if (!editingLayout && !placed.stale && canPlace()) setupDrag(cell, placed.key);
                } else {
                    cell.append(element('span', '', disabled ? 'Nonaktif / jalan' : `S${pad(bay)}-B${pad(row)}`));
                    if (disabled) cell.classList.add('gp-blocked');
                    cell.title = coordinates;
                    if (!editingLayout && !disabled && canPlace()) {
                        cell.addEventListener('dragover', event => {event.preventDefault(); event.dataTransfer.dropEffect = 'move'; cell.classList.add('gp-drag-over');});
                        cell.addEventListener('dragleave', () => cell.classList.remove('gp-drag-over'));
                        cell.addEventListener('drop', event => {
                            event.preventDefault(); cell.classList.remove('gp-drag-over');
                            if (!draggedKey) return;
                            selected = draggedKey;
                            savePosition(active.code, bay, row, currentTier);
                        });
                    }
                }
                cell.setAttribute('aria-label', cell.title);
                grid.append(cell);
            }
        }
        root.append(grid);
    }

    function clickCell(active, bay, row, placed, disabled) {
        if (editingLayout) {
            if (!config.canEdit) return;
            if (!applyDraft()) return;
            active = block();
            if (state.positions.some(p => p.block === active.code && p.row === row && bay >= p.bay && bay < p.bay + p.span)) {
                message('Petak ini berisi kontainer pada salah satu tingkat. Pindahkan kontainer terlebih dahulu.'); return;
            }
            if (disabled) active.disabled = active.disabled.filter(cell => !(cell.bay === bay && cell.row === row));
            else active.disabled.push({bay, row});
            markDirty(); stats(); renderGrid();
        } else if (placed) {
            selectContainer(placed.key);
        } else if (!disabled && selected && canPlace()) {
            savePosition(active.code, bay, row, currentTier);
        }
    }

    function setupDrag(node, key) {
        node.draggable = true;
        node.addEventListener('dragstart', event => {draggedKey = key; event.dataTransfer.setData('text/plain', key); event.dataTransfer.effectAllowed = 'move';});
        node.addEventListener('dragend', () => {draggedKey = null; document.querySelectorAll('.gp-drag-over').forEach(cell => cell.classList.remove('gp-drag-over'));});
    }

    function selectContainer(key) {
        selected = key;
        const position = positionFor(key);
        if (position) {currentBlock = position.block; currentTier = position.tier;}
        renderList(); renderForm(); renderGrid();
    }

    function renderList() {
        const search = $('gp-search').value.trim().toLowerCase();
        const filter = $('gp-list-filter').value;
        const root = $('gp-container-list'); root.replaceChildren();
        const containers = state.containers.filter(c => {
            const placed = positionFor(c.key);
            const searchable = `${c.number} ${placed ? locationCode(placed) : ''}`.toLowerCase();
            return searchable.includes(search) && (filter === 'all' || (filter === 'assigned') === !!placed);
        });
        containers.forEach(c => {
            const placed = positionFor(c.key);
            const card = button('', () => selectContainer(c.key), 'gp-card');
            if (selected === c.key) card.classList.add('gp-selected');
            card.append(element('strong', '', c.number || '(Nomor belum diisi)'), element('small', '', `${c.source === 'stock' ? 'Milik sendiri' : 'Sewa'} · ${c.size || '?'} kaki · ${c.type || '-'}`));
            if (placed) card.append(element('small', '', `Lokasi: ${locationCode(placed)}`));
            if (!c.span) card.append(element('small', '', 'Ukuran belum didukung. Gunakan 20 atau 40 kaki.'));
            if (canPlace() && c.span && !placed?.stale) setupDrag(card, c.key);
            root.append(card);
        });
        if (!containers.length) root.append(element('p', 'gp-empty', 'Tidak ada kontainer yang cocok.'));
    }

    function coordinateOptions(code, position) {
        const b = layout.blocks.find(item => item.code === code);
        [['bay', 'bays'], ['row', 'rows'], ['tier', 'tiers']].forEach(([key, dimension]) => {
            options($(`gp-input-${key}`), Array.from({length: b?.[dimension] || 0}, (_, i) => ({value: i + 1, label: key === 'tier' && i === 0 ? '01 (dasar)' : pad(i + 1)})), position?.[key] || 1);
        });
        renderLocationSummary();
    }

    function renderLocationSummary() {
        const summary = $('gp-location-summary');
        summary.replaceChildren();
        const code = $('gp-input-block').value;
        if (!code || !selected) {
            summary.append(element('p', 'gp-help', 'Pilih kontainer dan lokasi tujuan untuk melihat kode lokasinya.'));
            return;
        }
        const destination = {
            block: code,
            bay: Number($('gp-input-bay').value), row: Number($('gp-input-row').value), tier: Number($('gp-input-tier').value),
            span: containerFor(selected)?.span || positionFor(selected)?.span || 1,
        };
        const existing = positionFor(selected);
        if (existing) summary.append(element('p', 'gp-help', `Lokasi tersimpan: ${locationCode(existing)}`));
        summary.append(element('span', 'gp-location-label', 'Lokasi tujuan'), element('strong', 'gp-location-code', locationCode(destination)));
        summary.append(element('p', 'gp-help', locationDescription(destination)));
        if (destination.span === 2) summary.append(element('p', 'gp-help', `Kontainer 40 kaki menempati slot ${slotRange(destination)} pada baris dan tingkat yang sama.`));
    }

    function renderForm() {
        const placed = positionFor(selected);
        const container = containerFor(selected);
        $('gp-selected').textContent = selected ? `${container?.number || placed?.container_number || ''}${placed?.stale ? ' — Perlu diperiksa; lepaskan posisi lama.' : placed ? ' — Ubah posisi' : ' — Penempatan baru'}` : 'Belum ada kontainer dipilih.';
        options($('gp-input-block'), layout.blocks.map(b => b.code), placed?.block || currentBlock);
        coordinateOptions($('gp-input-block').value, placed);
        if ($('gp-save-position')) $('gp-save-position').disabled = !canPlace() || !container?.span || !!placed?.stale;
        if ($('gp-remove-position')) $('gp-remove-position').hidden = !placed;
    }

    function renderTable() {
        const root = $('gp-position-rows'); root.replaceChildren();
        state.positions.forEach(p => {
            const row = element('tr');
            [p.container_number, p.source === 'stock' ? 'Milik sendiri' : 'Sewa', locationCode(p), p.block, slotRange(p), pad(p.row), p.tier === 1 ? '01 (dasar)' : pad(p.tier), p.stale ? 'Perlu diperiksa' : 'Tersimpan'].forEach(value => row.append(element('td', '', value)));
            const action = element('td');
            action.append(button('Lihat', () => selectContainer(p.key)));
            if (config.canEdit) action.append(button('Lepas', () => removePosition(p), 'gp-button gp-danger'));
            row.append(action); root.append(row);
        });
        if (!state.positions.length) {const row = element('tr'); const cell = element('td', 'gp-empty', 'Belum ada posisi tersimpan.'); cell.colSpan = 9; row.append(cell); root.append(row);}
    }

    async function savePosition(code, bay, row, tier) {
        const container = containerFor(selected);
        if (!canPlace() || !container?.span) {message('Pilih kontainer dengan ukuran 20 atau 40 kaki yang tersedia di gudang ini.'); return;}
        if (positionFor(selected)?.stale) {message('Lepaskan posisi lama yang perlu diperiksa sebelum menempatkan kontainer kembali.'); return;}
        if (await mutate(config.positionUrl, 'PUT', {source: container.source, container_id: container.container_id, block: code, bay, row, tier})) {
            currentBlock = code; currentTier = tier; renderGrid();
            message(`Lokasi ${container.number} tersimpan di ${locationCode({block: code, bay, row, tier})}.`, 'success');
        }
    }

    async function removePosition(position) {
        if (!position || !config.canEdit || !confirm(`Lepaskan posisi ${position.container_number} dari denah? Data master kontainer tetap tersimpan.`)) return;
        const url = config.deleteUrl.replace(/\/0$/, `/${position.id}`);
        if (await mutate(url, 'DELETE', {})) {
            selected = null; renderForm(); renderList(); renderGrid();
            message(`Posisi ${position.container_number} di ${locationCode(position)} berhasil dilepas dari denah.`, 'success');
        }
    }

    function render() {
        stats(); renderGrid();
        if (editingLayout) renderEditor();
        else {renderList(); renderForm(); renderTable();}
    }

    $('gp-view-tier').addEventListener('change', event => {currentTier = Number(event.target.value); renderGrid();});
    if (editingLayout) {
        $('gp-add-block')?.addEventListener('click', () => {
            if (!applyDraft()) return;
            if (layout.blocks.length >= 12) {message('Maksimal 12 area per gudang.'); return;}
            let index = 0; let code;
            do {code = String.fromCharCode(65 + index++);} while (layout.blocks.some(b => b.code === code));
            layout.blocks.push({code, bays: 10, rows: 4, tiers: 3, disabled: []});
            currentBlock = code; currentTier = 1; markDirty(); render();
        });
        $('gp-save-layout')?.addEventListener('click', async () => {
            if (!applyDraft()) return;
            if (!layout.blocks.length) {message('Tambahkan minimal satu area gudang.'); return;}
            await mutate(config.layoutUrl, 'PUT', {layout});
        });
    } else {
        $('gp-search').addEventListener('input', renderList);
        $('gp-list-filter').addEventListener('change', renderList);
        $('gp-input-block').addEventListener('change', event => coordinateOptions(event.target.value));
        ['bay', 'row', 'tier'].forEach(key => $(`gp-input-${key}`).addEventListener('change', renderLocationSummary));
        $('gp-position-form').addEventListener('submit', event => {
            event.preventDefault();
            savePosition($('gp-input-block').value, Number($('gp-input-bay').value), Number($('gp-input-row').value), Number($('gp-input-tier').value));
        });
        $('gp-remove-position')?.addEventListener('click', () => removePosition(positionFor(selected)));
    }
    window.addEventListener('beforeunload', event => {if (dirty || busy) {event.preventDefault(); event.returnValue = '';}});
    render();
})();
