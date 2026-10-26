const test = require('node:test');
const assert = require('node:assert/strict');
require('../public/js/pranota-bpjs-refresh.js');

test('refresh recalculates existing rows with fresh data while preserving row identity and skipped rows', async () => {
    let employees = [];
    const row = { value: 7991, refreshBpjs() { this.value = employees[0].amount; return true; } };
    const empty = { value: 123, refreshBpjs() { return false; } };
    const rows = [row, empty];
    const result = await refreshBpjsData({
        url: '/pranota-bpjs/create', rows,
        applyData(data) { employees = data.karyawans; },
        async fetchData(url, options) {
            assert.equal(options.cache, 'no-store');
            assert.equal(options.headers.Accept, 'application/json');
            return { ok: true, async json() { return { karyawans: [{ amount: 8000 }], rumusBpjs: [] }; } };
        }
    });
    assert.deepEqual(result, { updated: 1, skipped: 1 });
    assert.equal(rows[0], row);
    assert.equal(row.value, 8000);
    assert.equal(empty.value, 123);
    assert.equal(rows.length, 2);
});

test('failed or invalid responses leave current data and calculations intact', async () => {
    for (const response of [{ ok: false }, { ok: true, async json() { return {}; } }]) {
        let changed = false;
        await assert.rejects(refreshBpjsData({
            url: '/', rows: [{ refreshBpjs() { changed = true; } }],
            applyData() { changed = true; }, fetchData: async () => response
        }));
        assert.equal(changed, false);
    }
});
