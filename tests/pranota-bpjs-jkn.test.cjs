const test = require('node:test');
const assert = require('node:assert/strict');
require('../public/js/pranota-bpjs-jkn.js');
const { split, syncRow } = globalThis.BpjsJknColumns;

test('mixed employee types split the 4% share without changing JKN debt or total', () => {
    const permanent = split(214319, 'App\\Models\\Karyawan');
    const temporary = split(229195, 'App\\Models\\KaryawanTidakTetap', true);
    assert.deepEqual(permanent, { tunjangan: 214319, biaya: 0 });
    assert.deepEqual(temporary, { tunjangan: 0, biaya: 229195 });
    assert.equal(permanent.tunjangan + temporary.biaya + 53580 + 57299, 554393);
});

test('changing employee type moves the same editable input and preserves its submitted name and value', () => {
    const input = { name: 'details[1][bpjs_kesehatan]', value: '229.195', setAttribute(key, value) { this[key] = value; } };
    const zero = {};
    const type = { value: 'App\\Models\\KaryawanTidakTetap' };
    const tunjangan = { changes: 0, replaceChildren(child) { this.child = child; child.parentElement = this; this.changes++; } };
    const biaya = { changes: 0, replaceChildren(child) { this.child = child; child.parentElement = this; this.changes++; } };
    const selectors = { '.input-kes': input, '.jkn-zero': zero, '.tipe-karyawan-hidden-input': type, '[data-jkn-tunjangan]': tunjangan, '[data-jkn-biaya]': biaya };
    const row = { dataset: { isSpecialCase: '1' }, querySelector(selector) { return selectors[selector]; } };
    syncRow(row);
    assert.equal(biaya.child, input);
    assert.equal(tunjangan.child, zero);
    assert.equal(input['aria-label'], 'KIS 4% (BIAY)');
    syncRow(row);
    assert.equal(biaya.changes, 1, 'recalculating must preserve the focused input');
    row.dataset.isSpecialCase = '0';
    syncRow(row);
    assert.equal(tunjangan.child, input);
    assert.equal(biaya.child, zero);
    assert.equal(input['aria-label'], 'KIS 4% (TUNJ)');
    row.dataset.isSpecialCase = '1';
    syncRow(row);
    assert.equal(biaya.child, input);
    type.value = 'App\\Models\\Karyawan';
    syncRow(row);
    assert.equal(tunjangan.child, input);
    assert.equal(biaya.child, zero);
    assert.equal(input.value, '229.195');
    assert.equal(input.name, 'details[1][bpjs_kesehatan]');
});

test('only enabled special case routes temporary JKN to biaya', () => {
    const type = 'App\\Models\\KaryawanTidakTetap';
    for (const status of [true, 1, '1']) {
        assert.deepEqual(split(214319, type, status), { tunjangan: 0, biaya: 214319 });
    }
    for (const status of [false, 0, '0', null, undefined]) {
        assert.deepEqual(split(214319, type, status), { tunjangan: 214319, biaya: 0 });
    }
});
