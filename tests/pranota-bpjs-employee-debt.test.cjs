const test = require('node:test');
const assert = require('node:assert/strict');
require('../public/js/pranota-bpjs-allowances.js');

test('clearing employee JHT burden clears its components and Auto restores them', () => {
    const fields = [20500.25, 10250.5, 0, 4250.25].map(value => ({ value: String(value) }));
    const selectors = ['.input-jht-hutang', '.input-jkk-hutang', '.input-noncrew-jht-hutang', '.input-jp-hutang'];
    let updates = 0;
    const row = { dataset: { groupJamsostek: 'PPU' },
        querySelector: selector => fields[selectors.indexOf(selector)],
        dispatchEvent: () => updates++ };
    const sync = (target, restore = false) => BpjsAllowances.syncEmployeeDebt(row, target, Number, String, restore);
    sync(0);
    assert.deepEqual(fields.map(field => Number(field.value)), [0, 0, 0, 0]);
    sync(0, true);
    assert.deepEqual(fields.map(field => Number(field.value)), [20500.25, 10250.5, 0, 4250.25]);
    sync(123.45);
    assert.equal(Math.round(fields.reduce((sum, field) => sum + Number(field.value), 0) * 100), 12345);
    assert.equal(updates, 3);
});
