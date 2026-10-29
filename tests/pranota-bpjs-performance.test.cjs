const test = require('node:test');
const assert = require('node:assert/strict');
require('../public/js/pranota-bpjs-performance.js');
require('../public/js/pranota-bpjs-jkn.js');
require('../public/js/pranota-bpjs-allowances.js');

test('group totals sum each membership separately and exclude allowance summaries', () => {
    const amounts = { bpjs_kesehatan: 229195.20, bpjs_ketenagakerjaan: 57298.80,
        jht_biaya: 100000.80, jht_hutang: 20000, jkk_tunjangan: 1000,
        jkm_tunjangan: 2000, jp_biaya: 10000, jp_hutang: 11000,
        noncrew_jht_biaya: 50000, bpu_jkk_tunjangan: 15000, allowance_pt: 99999 };
    assert.deepEqual(BpjsPerformance.groupTotals(amounts), { jkn: 286494, ppu: 144000.8, bpu_crew: 15000, bpu_noncrew: 50000 });
    assert.deepEqual(BpjsPerformance.groupTotals({}), { jkn: 0, ppu: 0, bpu_crew: 0, bpu_noncrew: 0 });
});

test('displayed group totals refresh on edits and never become submitted input fields', () => {
    const fields = new Map([
        ['.input-kes', { value: '229195.20' }], ['.input-ket', { value: '57298.80' }],
        ['.input-jht-biaya', { value: '100000.80' }], ['.input-jp-biaya', { value: '11000' }],
        ['.row-total-jkn', {}], ['.row-total-ppu', {}],
        ['.input-jkk-hutang', { value: '19000.25' }], ['.input-bpu-jkk', { value: '9000' }],
        ['.input-bpu-jkm', { value: '6800' }], ['.row-total-bpu_crew', {}],
        ['.input-noncrew-jht-biaya', { value: '19000.25' }], ['.input-noncrew-jht-hutang', { value: '10000' }],
        ['.input-noncrew-jkk', { value: '9500.5' }], ['.input-noncrew-jkm', { value: '6800' }],
        ['.row-total-bpu_noncrew', {}],
    ]);
    const row = { querySelector: selector => fields.get(selector) || null };
    const parse = value => Number(value) || 0;
    BpjsPerformance.updateGroupTotals(row, parse, value => value.toFixed(2));
    assert.equal(fields.get('.row-total-jkn').textContent, 'Rp 286494.00');
    assert.equal(fields.get('.row-total-ppu').textContent, 'Rp 111000.80');
    assert.equal(fields.get('.row-total-bpu_crew').textContent, 'Rp 34800.25');
    assert.equal(fields.get('.row-total-bpu_noncrew').textContent, 'Rp 45300.75');
    fields.get('.input-jp-biaya').value = '15000.25';
    fields.get('.input-noncrew-jkm').value = '7000';
    BpjsPerformance.updateGroupTotals(row, parse, value => value.toFixed(2));
    assert.equal(fields.get('.row-total-ppu').textContent, 'Rp 115001.05');
    assert.equal(fields.get('.row-total-bpu_noncrew').textContent, 'Rp 45500.75');
    assert.equal(fields.get('.row-total-bpu_crew').name, undefined);
    assert.equal(fields.get('.row-total-bpu_noncrew').name, undefined);
    assert.equal(fields.get('.row-total-ppu').name, undefined);
});

test('UMKM Non PP edit rows use saved noncrew components and recalculate changed aggregates', () => {
    const fields = new Map([
        ['.input-jht-biaya', { value: '19000' }], ['.input-jkk', { value: '9500.25' }],
        ['.input-jkm', { value: '6800' }], ['.select-tipe-jkn', { value: 'manual' }],
        ['input[name$="[rincian_bpjs]"]', { value: JSON.stringify({ noncrew_jht_biaya: 19000,
            noncrew_jkk_tunjangan: 9500.25, noncrew_jkm_tunjangan: 6800 }) }],
    ]);
    const row = { dataset: { tipeJamsostek: 'NON KARY-UMKM Non PP' },
        querySelector: selector => fields.get(selector) || null };
    const parse = value => Number(value) || 0;
    const calculate = () => BpjsAllowances.updateRow(row, {}, parse, String).pt;
    assert.equal(calculate(), 35300.25);
    fields.get('.input-jkk').value = '10000';
    assert.equal(calculate(), 35800);
    fields.delete('input[name$="[rincian_bpjs]"]');
    row.dataset.tipeJamsostek = 'PPU';
    assert.equal(calculate(), 19000);
});

test('editing saved aggregate JHT and JP recalculates employer burden using the displayed amounts', () => {
    const fields = new Map([
        ['.input-jht-biaya', { value: '247320.25' }], ['.input-jp-biaya', { value: '107200' }],
        ['.input-kes', { value: '214319.20' }], ['.select-tipe-jkn', { value: 'manual' }],
        ['input[name$="[rincian_bpjs]"]', { value: JSON.stringify({ jht_biaya: 198320,
            noncrew_jht_biaya: 49000.25, jp_biaya: 107200 }) }],
    ]);
    const row = { dataset: {}, querySelector: selector => fields.get(selector) || null };
    const parse = value => Number(value) || 0;
    const calculate = () => BpjsAllowances.updateRow(row, {}, parse, String).pt;
    assert.equal(calculate(), 354520.25);
    fields.get('.input-jht-biaya').value = '250000.50';
    fields.get('.input-jp-biaya').value = '110000.25';
    assert.equal(calculate(), 360000.75);
});

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
