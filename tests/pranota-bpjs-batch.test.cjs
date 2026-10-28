const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
require('../public/js/pranota-bpjs-batch.js');

test('264 employees are processed in order while yielding between chunks', async () => {
    const results = [], progress = [];
    let pauses = 0;
    const employees = Array.from({ length: 264 }, (_, index) => index);
    await processBpjsBatch(employees, value => results.push(value),
        (done, total) => progress.push([done, total]), async () => { pauses++; });
    assert.deepEqual(results, employees);
    assert.equal(pauses, 22);
    assert.deepEqual(progress.at(-1), [264, 264]);
});

for (const page of ['create', 'edit']) {
    test(`${page} inline script remains valid after batching changes`, () => {
        const source = fs.readFileSync(`resources/views/pranota-bpjs/${page}.blade.php`, 'utf8');
        const script = source.match(/<script>([\s\S]*?)<\/script>/)[1].replace(/@json\([^\n]*\)/g, '[]');
        assert.doesNotThrow(() => new Function(script));
    });
    test(`${page} builds detached rows and updates global totals once`, async () => {
        const source = fs.readFileSync(`resources/views/pranota-bpjs/${page}.blade.php`, 'utf8');
        const start = source.indexOf('    async function generateRows(');
        const end = source.indexOf('    btnAdd.addEventListener(', start);
        const wrapper = source.slice(start, end);
        const execute = new Function('processBpjsBatch', `
            let isGenerating = false, isRefreshing = false, rowCount = 0, totals = 0, swaps = 0;
            const control = { disabled: false, innerHTML: 'Hitung Semua' };
            const container = { inert: false, replaceChildren(fragment) { swaps++; this.rows = fragment.rows; } };
            let rowTarget = container;
            const document = {
                createDocumentFragment: () => ({ rows: [] }),
                getElementById: id => id === 'btn-generate-all'
                    ? Object.assign(control, { setAttribute() {}, removeAttribute() {} })
                    : { querySelectorAll: () => [control] },
            };
            function addRow() { rowCount++; rowTarget.rows.push(rowCount); }
            function applyFilter() { if (!isGenerating) totals++; }
            function calculateTotals() { if (!isGenerating) totals++; }
            function updateRowNumbers() {}
            ${wrapper}
            return generateRows(Array.from({ length: 264 }), () => { addRow();
                ${page === 'create' ? 'if (!isGenerating) applyFilter();' : 'if (!isGenerating) calculateTotals();'}
            }).then(() => ({ totals, swaps, count: container.rows.length, busy: isGenerating,
                disabled: control.disabled, inert: container.inert }));
        `);
        const result = await execute((items, process, progress) =>
            processBpjsBatch(items, process, progress, async () => {}));
        assert.deepEqual(result, { totals: 1, swaps: 1, count: 264, busy: false, disabled: false, inert: false });
    });
}
