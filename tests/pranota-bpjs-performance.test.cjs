const test = require('node:test');
const assert = require('node:assert/strict');
require('../public/js/pranota-bpjs-performance.js');
require('../public/js/pranota-bpjs-jkn.js');
require('../public/js/pranota-bpjs-allowances.js');

function fakeClock() {
    let id = 0;
    const jobs = new Map();
    return { jobs,
        requestAnimationFrame(fn) { jobs.set(++id, fn); return id; },
        cancelAnimationFrame(id) { jobs.delete(id); },
        setTimeout(fn) { jobs.set(++id, fn); return id; },
        clearTimeout(id) { jobs.delete(id); },
    };
}

test('rapid edits schedule one update and submitting flushes the latest value exactly once', () => {
    const clock = fakeClock(), values = [];
    const task = BpjsPerformance.scheduler(value => values.push(value), 0, clock);
    for (let i = 0; i < 100; i++) task.schedule(i);
    assert.equal(clock.jobs.size, 1);
    task.flush();
    task.flush();
    assert.deepEqual(values, [99]);
    assert.equal(clock.jobs.size, 0);
});

test('search waits for the latest keystroke and cached amounts are invalidated by value changes', () => {
    const clock = fakeClock(), values = [];
    const task = BpjsPerformance.scheduler(value => values.push(value), 150, clock);
    task.schedule('a'); task.schedule('ac'); task.schedule('aceng');
    assert.equal(clock.jobs.size, 1);
    [...clock.jobs.values()][0]();
    assert.deepEqual(values, ['aceng']);
    let queries = 0, parses = 0;
    const input = { value: '100.000,25' };
    const row = { querySelector() { queries++; return input; } };
    const parse = value => { parses++; return Number(value.replace('.', '').replace(',', '.')); };
    assert.equal(BpjsPerformance.readNumber(row, '.input', parse), 100000.25);
    assert.equal(BpjsPerformance.readNumber(row, '.input', parse), 100000.25);
    input.value = '200,50';
    assert.equal(BpjsPerformance.readNumber(row, '.input', parse), 200.5);
    assert.equal(queries, 1);
    assert.equal(parses, 2);
});

test('264 unchanged rows skip all 1848 editor writes; edits, manual zero, and filtered totals stay correct', () => {
    let writes = 0;
    const keys = Object.keys(BpjsAllowances.calculate({}));
    const rows = Array.from({ length: 264 }, (_, index) => {
        const fields = new Map([
            ['.input-kes', { value: '100.25' }], ['.input-ket', { value: '25.5' }],
            ['.karyawan-hidden-input', { name: `details[${index + 1}][karyawan_id]` }],
            ['.tipe-karyawan-hidden-input', { value: 'App\\Models\\Karyawan' }],
        ]);
        for (const key of keys) {
            const input = { dataset: {}, removeAttribute(name) { delete this[name]; },
                set value(value) { writes++; this.amount = value; }, get value() { return this.amount; } };
            const elements = { input, '.bpjs-allowance-editor': { dataset: {} },
                '.bpjs-allowance-status': {}, '.bpjs-allowance-reset': {} };
            fields.set(`[data-allowance="${key}"]`, { querySelector: selector => elements[selector] });
        }
        return { fields, dataset: {}, hidden: false,
            classList: { contains: () => rows[index].hidden },
            querySelector: selector => fields.get(selector) || null };
    });
    const totals = new Map(keys.map(key => ['total-allowance-' + key, {}]));
    const previousDocument = globalThis.document;
    globalThis.document = { getElementById: id => totals.get(id), querySelectorAll: () => [] };
    try {
        const container = { querySelectorAll: () => rows };
        const parse = value => Number(value) || 0;
        const format = value => value.toFixed(2);
        const update = () => BpjsAllowances.update(container, parse, format);
        update();
        assert.equal(writes, 264 * 7);
        writes = 0;
        update();
        assert.equal(writes, 0);
        rows[0].fields.get('.input-kes').value = '200.25';
        update();
        assert.equal(writes, 7);
        assert.equal(totals.get('total-allowance-jkn').textContent, 'Rp 26566.00');
        rows[0].allowanceOverrides = { jkn: 0 };
        update();
        const manual = rows[0].fields.get('[data-allowance="jkn"]').querySelector('input');
        assert.equal(manual.name, 'details[1][allowance_jkn]');
        assert.equal(manual.value, '0.00');
        rows[1].hidden = true;
        update();
        assert.equal(totals.get('total-allowance-jkn').textContent, 'Rp 26265.50');
        delete rows[0].allowanceOverrides.jkn;
        update();
        assert.equal(manual.name, undefined);
        assert.equal(manual.value, '200.25');
        assert.equal(totals.get('total-allowance-jkn').textContent, 'Rp 26465.75');
    } finally {
        globalThis.document = previousDocument;
    }
});
