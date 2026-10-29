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

    root.BpjsPerformance = { field, readNumber, scheduler };
})(globalThis);
