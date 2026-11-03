(function (root) {
    const rows = new WeakMap();

    function field(row, selector) {
        let fields = rows.get(row);
        if (!fields) { fields = new Map(); rows.set(row, fields); }
        if (!fields.has(selector)) {
            const element = row.querySelector(selector);
            if (element) fields.set(selector, { element });
            else return null;
        }
        return fields.get(selector).element;
    }

    function readNumber(row, selector, parseAmount) {
        const input = field(row, selector);
        if (!input) return 0;
        const cached = rows.get(row).get(selector);
        if (cached.raw !== input.value || cached.parser !== parseAmount) {
            cached.raw = input.value;
            cached.parser = parseAmount;
            cached.amount = parseAmount(input.value);
        }
        return cached.amount;
    }

    // Collapse repeated events into one update; flush before serializing the form.
    function scheduler(callback, delay = 0, clock = root) {
        let pending = null, args = [];
        const cancel = () => {
            if (pending === null) return;
            if (delay) clock.clearTimeout(pending);
            else clock.cancelAnimationFrame(pending);
            pending = null;
        };
        const flush = () => {
            if (pending === null) return;
            cancel();
            callback(...args);
        };
        const schedule = (...values) => {
            args = values;
            if (delay) cancel();
            if (pending !== null) return;
            pending = delay ? clock.setTimeout(flush, delay) : clock.requestAnimationFrame(flush);
        };
        return { schedule, flush, cancel };
    }

    function groupTotals(values) {
        const n = key => Number(values[key]) || 0;
        return {
            jkn: n('bpjs_kesehatan') + n('bpjs_ketenagakerjaan'),
            ppu: n('jht_biaya') + n('jht_hutang') + n('jkk_tunjangan')
                + n('jkm_tunjangan') + n('jp_biaya') + n('jp_hutang'),
            bpu_crew: n('jkk_hutang') + n('bpu_jkk_tunjangan') + n('bpu_jkm'),
            bpu_noncrew: n('noncrew_jht_biaya') + n('noncrew_jht_hutang')
                + n('noncrew_jkk_tunjangan') + n('noncrew_jkm_tunjangan'),
        };
    }

    function updateGroupTotals(row, parseAmount, formatNumber) {
        const values = {};
        for (const [key, selector] of Object.entries({ bpjs_kesehatan: '.input-kes',
            bpjs_ketenagakerjaan: '.input-ket', jht_biaya: '.input-jht-biaya',
            jht_hutang: '.input-jht-hutang', jkk_tunjangan: '.input-jkk',
            jkm_tunjangan: '.input-jkm', jp_biaya: '.input-jp-biaya', jp_hutang: '.input-jp-hutang',
            jkk_hutang: '.input-jkk-hutang', bpu_jkk_tunjangan: '.input-bpu-jkk', bpu_jkm: '.input-bpu-jkm',
            noncrew_jht_biaya: '.input-noncrew-jht-biaya', noncrew_jht_hutang: '.input-noncrew-jht-hutang',
            noncrew_jkk_tunjangan: '.input-noncrew-jkk', noncrew_jkm_tunjangan: '.input-noncrew-jkm' })) {
            values[key] = readNumber(row, selector, parseAmount);
        }
        const totals = groupTotals(values);
        for (const [key, amount] of Object.entries(totals)) {
            const cell = field(row, '.row-total-' + key);
            const text = 'Rp ' + formatNumber(amount);
            if (cell && cell.textContent !== text) cell.textContent = text;
        }
        return totals;
    }

    function updateEmployeeInfo(row, employee, parseAmount, formatNumber) {
        row.bpjsEmployeeSnapshot = employee ? { ...employee } : null;
        const set = (selector, value) => {
            const element = field(row, selector);
            if (element) element.textContent = value;
        };
        const groups = [];
        if (employee?.group_jkn) groups.push('JKN: ' + employee.group_jkn);
        if (employee?.group_bp_jamsostek) groups.push('BP Jamsostek: ' + employee.group_bp_jamsostek);
        set('.group-text', groups.join('\n') || '-');
        set('.employee-tipe-jkn', employee?.tipe_jkn || '-');
        set('.employee-tipe-jamsostek', employee?.tipe_bp_jamsostek || '-');
        for (const [selector, key] of [['.employee-dpp-jkn', 'dpp_jkn'], ['.employee-dpp-jamsostek', 'dpp_bp_jamsostek']]) {
            const value = employee?.[key];
            set(selector, value === null || value === undefined || value === '' ? '-' : 'Rp ' + formatNumber(parseAmount(value)));
        }
    }

    root.BpjsPerformance = { field, readNumber, scheduler, groupTotals, updateGroupTotals, updateEmployeeInfo };
})(globalThis);
