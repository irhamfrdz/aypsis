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
        };
    }

    function updateGroupTotals(row, parseAmount, formatNumber) {
        const values = {};
        for (const [key, selector] of Object.entries({ bpjs_kesehatan: '.input-kes',
            bpjs_ketenagakerjaan: '.input-ket', jht_biaya: '.input-jht-biaya',
            jht_hutang: '.input-jht-hutang', jkk_tunjangan: '.input-jkk',
            jkm_tunjangan: '.input-jkm', jp_biaya: '.input-jp-biaya', jp_hutang: '.input-jp-hutang' })) {
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

    root.BpjsPerformance = { field, readNumber, scheduler, groupTotals, updateGroupTotals };
})(globalThis);
