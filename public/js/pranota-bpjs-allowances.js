(function (root) {
    const isBpuExcluded = values => String(values.tipe_bp_jamsostek || '').toUpperCase()
        .replace(/[\s_-]+/g, '') === 'NONKARYUMKMNONPP';
    const isEmployeeJhtExcluded = values => isBpuExcluded(values)
        || String(values.tipe_bp_jamsostek || '').toUpperCase().replace(/[\s_-]+/g, '') === 'NONKARY';
    const isJknExcluded = values => String(values.tipe_jkn || '').toUpperCase()
        .replace(/[\s_-]+/g, '') === 'NONKARY';
    const usesNonKaryPpuTotal = values => String(values.group_bp_jamsostek || '').toUpperCase()
        .replace(/[\s_-]+/g, '') === 'PPU'
        && String(values.tipe_bp_jamsostek || '').toUpperCase().replace(/[\s_-]+/g, '') === 'NONKARY';
    const isPpuExcluded = values => String(values.tipe_bp_jamsostek || '').toUpperCase()
        .replace(/[\s_-]+/g, '') === 'NONKARY';

    function calculate(values) {
        const isPpu = String(values.group_bp_jamsostek || '').trim().toUpperCase().includes('PPU');
        const n = key => Number(values[key]) || 0;
        const nonEmployee = type => String(type || '').toUpperCase().replace(/[\s_-]+/g, '') === 'NONKARY';
        const nonJkn = nonEmployee(values.tipe_jkn);
        const nonJamsostek = nonEmployee(values.tipe_bp_jamsostek);
        const employeeDebt = n('jht_hutang') + n('jkk_hutang') + n('noncrew_jht_hutang') + n('jp_hutang');
        const employerCost = n('jht_biaya') + n('noncrew_jht_biaya') + n('jp_biaya');
        const isUmkmNonPp = isBpuExcluded(values);
        const umkmCost = values.aggregate_jamsostek
            ? n('jht_biaya') + n('jkk_tunjangan') + n('jkm_tunjangan')
            : n('noncrew_jht_biaya') + n('noncrew_jkk_tunjangan') + n('noncrew_jkm_tunjangan');
        const umkmDebt = values.aggregate_jamsostek ? n('jht_hutang') : n('noncrew_jht_hutang');
        const useTotalPpu = usesNonKaryPpuTotal(values);
        const ppuTotal = n('jht_biaya') + n('jht_hutang') + n('jkk_tunjangan')
            + n('jkm_tunjangan') + n('jp_biaya') + n('jp_hutang');
        return {
            ppu: isPpu && !isPpuExcluded(values) ? (Number(values.jkk_tunjangan) || 0) + (Number(values.jkm_tunjangan) || 0) : 0,
            bpu: isUmkmNonPp ? 0 : (Number(values.bpu_jkk_tunjangan) || 0) + (Number(values.bpu_jkm) || 0)
                + (Number(values.noncrew_jkk_tunjangan) || 0) + (Number(values.noncrew_jkm_tunjangan) || 0),
            jkn: isJknExcluded(values) ? 0 : (Number(values.kis_tunjangan) || 0),
            kary_jht: isEmployeeJhtExcluded(values) ? 0 : employeeDebt,
            kary_jkn: nonJkn ? 0 : n('bpjs_ketenagakerjaan'),
            pt: useTotalPpu ? ppuTotal : (isUmkmNonPp ? umkmCost : employerCost),
            non_kary: (nonJkn ? n('kis_tunjangan') + n('kis_biaya') + n('bpjs_ketenagakerjaan') : 0)
                + (isUmkmNonPp ? umkmDebt : (nonJamsostek ? employeeDebt + employerCost
                + n('jkk_tunjangan') + n('jkm_tunjangan') + n('bpu_jkk_tunjangan') + n('bpu_jkm')
                + n('noncrew_jkk_tunjangan') + n('noncrew_jkm_tunjangan') : 0)),
        };
    }

    function effective(amounts, overrides = {}, excludedKeys = []) {
        return Object.fromEntries(Object.entries(amounts).map(([key, value]) =>
            [key, excludedKeys.includes(key) ? 0 : (Object.hasOwn(overrides, key) ? Number(overrides[key]) : value)]));
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
        let aggregateJamsostek = !select('.input-bpu-jkk');
        if (aggregateJamsostek && select('.select-tipe-jkn')?.value === 'manual') {
            const snapshot = select('input[name$="[rincian_bpjs]"]');
            if (snapshot) {
                try {
                    const saved = JSON.parse(snapshot.value);
                    const groups = {
                        jht_biaya: ['jht_biaya', 'noncrew_jht_biaya'],
                        jht_hutang: ['jht_hutang', 'jkk_hutang', 'noncrew_jht_hutang'],
                        jkk_tunjangan: ['jkk_tunjangan', 'bpu_jkk_tunjangan', 'noncrew_jkk_tunjangan'],
                        jkm_tunjangan: ['jkm_tunjangan', 'bpu_jkm', 'noncrew_jkm_tunjangan'],
                        jp_biaya: ['jp_biaya'], jp_hutang: ['jp_hutang'],
                    };
                    const matches = saved && Object.entries(groups).every(([key, fields]) =>
                        Math.abs(fields.reduce((sum, field) => sum + (Number(saved[field]) || 0), 0) - values[key]) <= 0.005);
                    if (matches) { values = saved; aggregateJamsostek = false; }
                } catch (_) { /* Keep displayed values. */ }
            }
        }
        const split = root.BpjsJknColumns.split(read('.input-kes'),
            select('.tipe-karyawan-hidden-input')?.value, row.dataset.isSpecialCase);
        row.allowanceOverrides ??= {};
        const automaticAmounts = calculate({ ...values, group_bp_jamsostek: row.dataset.groupJamsostek,
            group_jkn: row.dataset.groupJkn,
            kis_tunjangan: split.tunjangan, kis_biaya: split.biaya,
            is_special_case: row.dataset.isSpecialCase,
            tipe_jkn: row.dataset.tipeJkn, tipe_bp_jamsostek: row.dataset.tipeJamsostek,
            aggregate_jamsostek: aggregateJamsostek,
            bpjs_ketenagakerjaan: read('.input-ket') });
        const index = select('.karyawan-hidden-input')?.name.match(/^details\[([^\]]+)\]/)?.[1];
        const excludedBpu = isBpuExcluded({ tipe_bp_jamsostek: row.dataset.tipeJamsostek });
        const excludedKeys = [];
        if (isPpuExcluded({ tipe_bp_jamsostek: row.dataset.tipeJamsostek })) excludedKeys.push('ppu');
        if (excludedBpu) excludedKeys.push('bpu');
        if (isEmployeeJhtExcluded({ tipe_bp_jamsostek: row.dataset.tipeJamsostek })) excludedKeys.push('kary_jht');
        if (isJknExcluded({ tipe_jkn: row.dataset.tipeJkn })) excludedKeys.push('jkn');
        const signature = JSON.stringify([automaticAmounts, row.allowanceOverrides, index, excludedKeys]);
        const cached = rowStates.get(row);
        if (cached?.signature === signature) return cached.amounts;
        const amounts = effective(automaticAmounts, row.allowanceOverrides, excludedKeys);
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
                        pt: 'Beban PT. PPU JHT 3.7%, PPU JP 2% & JHT 2% (BIAYA)', non_kary: 'Beban NON KARY. JKN & BPJS' }[key]);
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
                const excluded = excludedKeys.includes(key);
                const manual = !excluded && Object.hasOwn(row.allowanceOverrides, key);
                input.disabled = excluded;
                if (manual && index) input.name = `details[${index}][allowance_${key}]`;
                else input.removeAttribute('name');
                if (document.activeElement !== input) input.value = formatNumber(amount);
                input.title = excluded ? (key === 'jkn' ? 'Tidak dihitung untuk tipe JKN ' + row.dataset.tipeJkn
                    : 'Tidak dihitung untuk tipe BP Jamsostek ' + row.dataset.tipeJamsostek)
                    : (manual ? 'Nominal manual' : 'Nominal otomatis sesuai rumus');
                cell.querySelector('.bpjs-allowance-editor').dataset.manual = String(manual);
                cell.querySelector('.bpjs-allowance-status').textContent = excluded ? 'Tidak dihitung' : (manual ? 'Diubah manual' : 'Sesuai rumus');
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
