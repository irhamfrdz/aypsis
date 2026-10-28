const test = require('node:test');
const assert = require('node:assert/strict');
require('../public/js/pranota-bpjs-jkn.js');
const { split, syncRow } = globalThis.BpjsJknColumns;

test('JKN-KIS REUMBERSMENT uses full DPP with zero debt even without a percentage formula', () => {
    const { calculate, isReimbursement } = globalThis.BpjsJknColumns;
    const employee = { group_jkn: ' jkn-kis ', tipe_jkn: ' reumbersment ' };
    assert.equal(isReimbursement(employee), true);
    assert.deepEqual(calculate(null, 5729880.25, employee), { contribution: 5729880.25, debt: 0 });
    assert.deepEqual(calculate({ group_name: 'JKN-KIS', tipe: 'REUMBERSMENT', tunjangan_persen: 4, hutang_persen: 1 }, 5729880.25),
        { contribution: 5729880.25, debt: 0 });
    assert.equal(isReimbursement({ ...employee, group_jkn: 'OTHER' }), false);
    assert.equal(isReimbursement({ ...employee, tipe_jkn: 'KANTOR' }), false);
});

test('termination label follows refreshed employee data and clears for active or empty rows', () => {
    const badge = { classList: { toggle(name, enabled) { this.hidden = enabled; } } };
    const row = { querySelector: () => badge };
    const sync = globalThis.BpjsJknColumns.syncEmploymentStatus;
    sync(row, { tanggal_berhenti: '2026-10-01', is_special_case: true });
    assert.equal(badge.textContent, 'Sudah Berhenti');
    assert.equal(badge.classList.hidden, false);
    assert.match(badge.title, /Special Case/);
    for (const employee of [{ tanggal_berhenti: null }, null]) {
        sync(row, employee);
        assert.equal(badge.classList.hidden, true);
        assert.equal(badge.textContent, '');
        assert.equal(badge.title, '');
    }
});

test('JKN-KIS NON KARY uses its own percentage with no debt from another type', () => {
    const { findFormula, calculate } = globalThis.BpjsJknColumns;
    const records = [
        { jenis: 'jkn', group_name: 'JKN-KIS', tipe: 'KARYAWAN', tunjangan_persen: 4, hutang_persen: 1 },
        { jenis: 'jkn', group_name: 'JKN-KIS', tipe: 'NON KARY', tunjangan_persen: 5, hutang_persen: null },
    ];
    const employee = { group_jkn: 'JKN-KIS', tipe_jkn: ' non kary ' };
    const formula = findFormula(records, employee);
    assert.equal(formula, records[1]);
    assert.deepEqual(calculate(formula, 5729880), { contribution: 286494, debt: 0 });
    assert.deepEqual(calculate(records[0], 5729880), { contribution: 229195.2, debt: 57298.8 });
    assert.equal(findFormula(records, { ...employee, tipe_jkn: 'UNKNOWN' }), null);
    assert.equal(findFormula(records, { group_jkn: 'JKN-KIS' }), null);
    assert.deepEqual(calculate(null, 5729880), { contribution: 0, debt: 0 });
});

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
    row.dataset.isSpecialCase = '0';
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

test('permanent employee special case uses biaya only while enabled', () => {
    assert.deepEqual(split(229195, 'App\\Models\\Karyawan', true), { tunjangan: 0, biaya: 229195 });
    assert.deepEqual(split(229195, 'App\\Models\\Karyawan', false), { tunjangan: 229195, biaya: 0 });
});
