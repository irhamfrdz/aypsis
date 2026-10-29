(function (root) {
    function calculate(values) {
        const isPpu = String(values.group_bp_jamsostek || '').trim().toUpperCase().includes('PPU');
        const n = key => Number(values[key]) || 0;
        const special = [true, 1, '1'].includes(values.is_special_case);
        const nonEmployee = type => String(type || '').toUpperCase().replace(/[\s_-]+/g, '').startsWith('NONKARY');
        const nonJkn = special || nonEmployee(values.tipe_jkn);
        const nonJamsostek = special || nonEmployee(values.tipe_bp_jamsostek);
        const employeeDebt = n('jht_hutang') + n('jkk_hutang') + n('noncrew_jht_hutang') + n('jp_hutang');
        const employerCost = n('jht_biaya') + n('noncrew_jht_biaya') + n('jp_biaya');
        return {
            ppu: isPpu ? (Number(values.jkk_tunjangan) || 0) + (Number(values.jkm_tunjangan) || 0) : 0,
            bpu: (Number(values.bpu_jkk_tunjangan) || 0) + (Number(values.bpu_jkm) || 0)
                + (Number(values.noncrew_jkk_tunjangan) || 0) + (Number(values.noncrew_jkm_tunjangan) || 0),
            jkn: Number(values.kis_tunjangan) || 0,
            kary_jht: nonJamsostek ? 0 : employeeDebt,
            kary_jkn: nonJkn ? 0 : n('bpjs_ketenagakerjaan'),
            pt: (nonJamsostek ? 0 : employerCost) + (nonJkn ? 0 : n('kis_tunjangan')),
            non_kary: (nonJkn ? n('kis_tunjangan') + n('kis_biaya') + n('bpjs_ketenagakerjaan') : 0)
                + (nonJamsostek ? employeeDebt + employerCost
                + n('jkk_tunjangan') + n('jkm_tunjangan') + n('bpu_jkk_tunjangan') + n('bpu_jkm')
                + n('noncrew_jkk_tunjangan') + n('noncrew_jkm_tunjangan') : 0),
        };
    }

    function effective(amounts, overrides = {}) {
        return Object.fromEntries(Object.entries(amounts).map(([key, value]) =>
            [key, Object.hasOwn(overrides, key) ? Number(overrides[key]) : value]));
    }

    const rowStates = new WeakMap();
    const pendingTotals = new WeakMap();

    function scheduleTotals(container, parseAmount, formatNumber) {
        if (!pendingTotals.has(container)) pendingTotals.set(container,
            root.BpjsPerformance.scheduler(() => update(container, parseAmount, formatNumber)));
        pendingTotals.get(container).schedule();
    }

    function updateRow(row, container, parseAmount, formatNumber) {
        const read = selector => root.BpjsPerformance.readNumber(row, selector, parseAmount);
        const select = selector => root.BpjsPerformance.field(row, selector);
        let values = {
            jkk_tunjangan: read('.input-jkk'), jkm_tunjangan: read('.input-jkm'),
            bpu_jkk_tunjangan: read('.input-bpu-jkk'), bpu_jkm: read('.input-bpu-jkm'),
            noncrew_jkk_tunjangan: read('.input-noncrew-jkk'), noncrew_jkm_tunjangan: read('.input-noncrew-jkm'),
            jht_biaya: read('.input-jht-biaya'), jht_hutang: read('.input-jht-hutang'),
            jkk_hutang: read('.input-jkk-hutang'), noncrew_jht_biaya: read('.input-noncrew-jht-biaya'),
            noncrew_jht_hutang: read('.input-noncrew-jht-hutang'),
            jp_biaya: read('.input-jp-biaya'), jp_hutang: read('.input-jp-hutang'),
        };
        // The edit form displays aggregate amounts; use its saved breakdown for manual rows.
        if (!select('.input-bpu-jkk') && select('.select-tipe-jkn')?.value === 'manual') {
            const snapshot = select('input[name$="[rincian_bpjs]"]');
            if (snapshot) {
                try { values = JSON.parse(snapshot.value); } catch (_) { /* Keep displayed values. */ }
            }
        }
        const split = root.BpjsJknColumns.split(read('.input-kes'),
            select('.tipe-karyawan-hidden-input')?.value, row.dataset.isSpecialCase);
        row.allowanceOverrides ??= {};
        const automaticAmounts = calculate({ ...values, group_bp_jamsostek: row.dataset.groupJamsostek,
            kis_tunjangan: split.tunjangan, kis_biaya: split.biaya,
            is_special_case: row.dataset.isSpecialCase,
            tipe_jkn: row.dataset.tipeJkn, tipe_bp_jamsostek: row.dataset.tipeJamsostek,
            bpjs_ketenagakerjaan: read('.input-ket') });
        const index = select('.karyawan-hidden-input')?.name.match(/^details\[([^\]]+)\]/)?.[1];
        const signature = JSON.stringify([automaticAmounts, row.allowanceOverrides, index]);
        const cached = rowStates.get(row);
        if (cached?.signature === signature) return cached.amounts;
        const amounts = effective(automaticAmounts, row.allowanceOverrides);
        Object.entries(amounts).forEach(([key, amount]) => {
            const cell = select(`[data-allowance="${key}"]`);
            if (cell) {
                let input = cell.querySelector('input');
                if (!input) {
                    input = document.createElement('input');
                    input.type = 'text';
                    input.inputMode = 'decimal';
                    input.className = 'bpjs-allowance-input';
                    input.setAttribute('aria-label', { ppu: 'Tunjangan PPU', bpu: 'Tunjangan BPU', jkn: 'Tunjangan JKN',
                        kary_jht: 'Beban KARY. JHT 2%, JP 1% & BPU JHT', kary_jkn: 'Beban KARY. JKN 1%',
                        pt: 'Beban PT. JHT 3.7%, JP 2%, BPU JHT 1% & JKN', non_kary: 'Beban NON KARY. JKN & BPJS' }[key]);
                    const reset = document.createElement('button');
                    reset.type = 'button';
                    reset.className = 'bpjs-allowance-reset';
                    reset.textContent = '↺ Auto';
                    reset.title = 'Kembalikan nominal sesuai rumus';
                    const editor = document.createElement('div');
                    editor.className = 'bpjs-allowance-editor';
                    const money = document.createElement('div');
                    money.className = 'bpjs-allowance-money';
                    const currency = document.createElement('span');
                    currency.className = 'bpjs-allowance-currency';
                    currency.textContent = 'Rp';
                    currency.setAttribute('aria-hidden', 'true');
                    money.append(currency, input);
                    const meta = document.createElement('div');
                    meta.className = 'bpjs-allowance-meta';
                    const status = document.createElement('span');
                    status.className = 'bpjs-allowance-status';
                    meta.append(status, reset);
                    editor.append(money, meta);
                    cell.replaceChildren(editor);
                    input.addEventListener('input', () => {
                        const value = input.value.trim();
                        if (value === '') delete row.allowanceOverrides[key];
                        else row.allowanceOverrides[key] = parseAmount(value);
                        input.setCustomValidity(parseAmount(value) < 0 ? 'Nominal tidak boleh negatif.' : '');
                        rowStates.delete(row);
                        updateRow(row, container, parseAmount, formatNumber);
                        scheduleTotals(container, parseAmount, formatNumber);
                    });
                    input.addEventListener('change', () => {
                        rowStates.delete(row);
                        updateRow(row, container, parseAmount, formatNumber);
                        scheduleTotals(container, parseAmount, formatNumber);
                        input.value = formatNumber(row.allowanceOverrides[key] ?? Number(input.dataset.automaticAmount));
                    });
                    reset.addEventListener('click', () => {
                        delete row.allowanceOverrides[key];
                        input.setCustomValidity('');
                        rowStates.delete(row);
                        updateRow(row, container, parseAmount, formatNumber);
                        scheduleTotals(container, parseAmount, formatNumber);
                    });
                }
                input.dataset.automaticAmount = automaticAmounts[key];
                if (Object.hasOwn(row.allowanceOverrides, key) && index) input.name = `details[${index}][allowance_${key}]`;
                else input.removeAttribute('name');
                if (document.activeElement !== input) input.value = formatNumber(amount);
                input.title = Object.hasOwn(row.allowanceOverrides, key) ? 'Nominal manual' : 'Nominal otomatis sesuai rumus';
                const manual = Object.hasOwn(row.allowanceOverrides, key);
                cell.querySelector('.bpjs-allowance-editor').dataset.manual = String(manual);
                cell.querySelector('.bpjs-allowance-status').textContent = manual ? 'Diubah manual' : 'Sesuai rumus';
                cell.querySelector('.bpjs-allowance-reset').disabled = !manual;
            }

        });
        rowStates.set(row, { signature, amounts });
        return amounts;
    }

    function update(container, parseAmount, formatNumber) {
        pendingTotals.get(container)?.cancel();
        const totals = Object.fromEntries(Object.keys(calculate({})).map(key => [key, 0]));
        container.querySelectorAll('tr.detail-row').forEach(row => {
            const amounts = updateRow(row, container, parseAmount, formatNumber);
            if (!row.classList.contains('hidden')) {
                Object.entries(amounts).forEach(([key, amount]) => { totals[key] += amount; });
            }
        });
        Object.entries(totals).forEach(([key, amount]) => {
            const cell = document.getElementById('total-allowance-' + key);
            if (cell) cell.textContent = 'Rp ' + formatNumber(amount);
            document.querySelectorAll(`[data-allowance-total="${key}"]`).forEach(summary => {
                summary.textContent = 'Rp ' + formatNumber(amount);
            });
        });
    }

    root.BpjsAllowances = { calculate, effective, update, updateRow,
        flush: container => pendingTotals.get(container)?.flush() };
})(globalThis);
