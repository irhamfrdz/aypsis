const assert = require('node:assert/strict');
const test = require('node:test');
require('../public/js/bpjs-effective-values.js');
require('../public/js/pranota-bpjs-jkn.js');
require('../public/js/pranota-bpjs-jamsostek.js');
const effective = globalThis.BpjsEffectiveValues;
const base = {
    nominal_dpp: 2200000, tunjangan_persen: 4, hutang_persen: 1,
    diskon_status: 'ada', diskon_tipe: 'persen', diskon_nilai: 50,
    bpjs_effective_values: [
        { valid_from: '2026-12-01', valid_until: '2027-03-31', values: { nominal_dpp: 3000000 } },
        { valid_from: '2026-12-15', valid_until: '2026-12-31', values: { diskon_nilai: 25 } },
    ]
};
test('DPP and discount use independent inclusive date ranges and restore base on period changes', () => {
    const december = effective.resolve(base, effective.date(12, 2026));
    assert.equal(december.nominal_dpp, 3000000);
    assert.equal(december.diskon_nilai, 25);
    assert.equal(BpjsJknColumns.calculate(december, 2200000).contribution, 120000);
    assert.equal(BpjsJamsostek.applyDiscount(december, 100), 25);
    const january = effective.resolve(december, effective.date(1, 2027));
    assert.equal(january.nominal_dpp, 3000000);
    assert.equal(january.diskon_nilai, 50);
    const april = effective.resolve(january, effective.date(4, 2027));
    assert.equal(april.nominal_dpp, 2200000);
    assert.equal(april.diskon_nilai, 50);
    assert.equal(effective.resolve(base, '2026-12-01').nominal_dpp, 3000000);
    assert.equal(effective.resolve(base, '2027-03-31').nominal_dpp, 3000000);
    assert.equal(base.nominal_dpp, 2200000);
});
test('month end handles leap years and old snapshots without schedules', () => {
    assert.equal(effective.date(2, 2028), '2028-02-29');
    assert.equal(effective.date(2, 2027), '2027-02-28');
    assert.equal(effective.resolve({ nominal_dpp: 42 }, '2026-12-31').nominal_dpp, 42);
});
