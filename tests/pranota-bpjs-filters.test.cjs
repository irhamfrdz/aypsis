const assert = require('node:assert/strict');
const test = require('node:test');
require('../public/js/pranota-bpjs-filters.js');
const filters = globalThis.BpjsMasterFilters;
const records = [
    { jenis: 'jkn', group_name: 'JKN ALEXINDO', tipe: 'KANTOR' },
    { jenis: 'jamsostek', group_name: 'BPU ALEXINDO PLUIT', tipe: 'CREW' },
    { jenis: 'jamsostek', group_name: 'BPU ALEXINDO PLUIT', tipe: 'NON KARY' },
    { jenis: 'jamsostek', group_name: 'PPU', tipe: 'KANTOR' },
    { jenis: 'jamsostek', group_name: ' bpu alexindo pluit ', tipe: ' crew ' },
];

test('filter options use unique nonempty values from master columns', () => {
    assert.deepEqual(filters.options(records, 'group_name'), ['BPU ALEXINDO PLUIT', 'JKN ALEXINDO', 'PPU']);
    assert.deepEqual(filters.options([...records, { tipe: null }], 'tipe'), ['CREW', 'KANTOR', 'NON KARY']);
});

test('group and type must match the same insurance membership', () => {
    const employee = {
        group_jkn: 'JKN ALEXINDO', tipe_jkn: 'KANTOR',
        group_bp_jamsostek: 'BPU ALEXINDO PLUIT', tipe_bp_jamsostek: 'CREW',
        cabang_bpjs: 'KANTOR',
    };
    assert.equal(filters.matches(employee, records, 'bpu alexindo pluit', ' crew '), true);
    assert.equal(filters.matches(employee, records, 'JKN ALEXINDO', 'KANTOR'), true);
    assert.equal(filters.matches(employee, records, 'BPU ALEXINDO PLUIT', 'KANTOR'), false);
    assert.equal(filters.matches(employee, records, 'JKN ALEXINDO', 'CREW'), false);
    assert.equal(filters.matches(employee, records, 'all', 'NON KARY'), false);
    assert.equal(filters.matches(employee, records, 'all', 'all'), true);
});

test('old employee types are inferred only for an unambiguous master group', () => {
    assert.equal(filters.matches({ group_jkn: 'JKN ALEXINDO' }, records, 'JKN ALEXINDO', 'KANTOR'), true);
    assert.equal(filters.matches({ group_bp_jamsostek: 'BPU ALEXINDO PLUIT' }, records, 'all', 'CREW'), false);
});

test('custom master groups select correct contribution columns and totals', () => {
    assert.deepEqual([...filters.categories(records, 'BPU ALEXINDO PLUIT', 'CREW')], ['bpu_crew']);
    assert.deepEqual([...filters.categories(records, 'BPU ALEXINDO PLUIT', 'NON KARY')], ['bpu_non_crew']);
    const amounts = { jkn: 100, ppu: 200, bpu_crew: 300, bpu_non_crew: 400 };
    assert.equal(filters.total(records, 'BPU ALEXINDO PLUIT', 'CREW', amounts), 300);
    assert.equal(filters.total(records, 'BPU ALEXINDO PLUIT', 'all', amounts), 700);
    assert.equal(filters.total(records, 'all', 'KANTOR', amounts), 300);
    assert.equal(filters.total(records, 'all', 'all', amounts), 1000);
});

test('BPU filters hide JKN summaries while retaining BPU and employee JHT summaries', () => {
    const selected = filters.allowanceKeys(filters.categories(records, 'BPU ALEXINDO PLUIT', 'all'));
    assert.equal(selected.has('jkn'), false);
    assert.equal(selected.has('kary_jkn'), false);
    assert.equal(selected.has('ppu'), false);
    assert.equal(selected.has('bpu'), true);
    assert.equal(selected.has('kary_jht'), true);
    const jkn = filters.allowanceKeys(filters.categories(records, 'JKN ALEXINDO', 'KANTOR'));
    assert.equal(jkn.has('jkn'), true);
    assert.equal(jkn.has('kary_jkn'), true);
    assert.equal(jkn.has('bpu'), false);
    assert.equal(jkn.has('kary_jht'), false);
    const all = filters.allowanceKeys(filters.categories(records));
    assert.equal(all.size, 7);
    assert.equal(all.has('jkn'), true);
});
