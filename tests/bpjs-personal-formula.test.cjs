const assert = require('node:assert/strict');
const test = require('node:test');
require('../public/js/pranota-bpjs-filters.js');
require('../public/js/pranota-bpjs-jkn.js');
require('../public/js/pranota-bpjs-jamsostek.js');
test('JKN prioritizes the personal formula and preserves the group formula for other employees', () => {
    const general = { jenis: 'jkn', group_name: 'JKN-KIS', tipe: 'KANTOR', tunjangan_persen: 4 };
    const special = { ...general, employee_targets: ['Karyawan_1'], nominal_dpp: 3000000 };
    const records = [general, special];
    const employee = { unique_id: 'Karyawan_1', group_jkn: 'JKN-KIS', tipe_jkn: 'KANTOR' };
    assert.equal(BpjsJknColumns.findFormula(records, employee), special);
    assert.equal(BpjsJknColumns.calculate(special, 2200000).contribution, 120000);
    assert.equal(BpjsJknColumns.findFormula(records, { ...employee, unique_id: 'KaryawanTidakTetap_1' }), general);
    assert.equal(BpjsJknColumns.findFormula([special], { ...employee, unique_id: 'Karyawan_2' }), null);
});
test('Jamsostek selects personal rules only for the correct employee and branch', () => {
    const general = { jenis: 'jamsostek', group_name: 'BPU', tipe: 'CREW', cabang_bpjs: 'PLUIT' };
    const special = { ...general, employee_targets: ['KaryawanTidakTetap_1'] };
    const employee = { unique_id: 'KaryawanTidakTetap_1', group_bp_jamsostek: 'BPU', tipe_bp_jamsostek: 'CREW', cabang_bpjs: 'PLUIT' };
    assert.equal(BpjsJamsostek.findFormula([general, special], employee), special);
    assert.equal(BpjsJamsostek.findFormula([general, special], { ...employee, unique_id: 'Karyawan_1' }), general);
    assert.equal(BpjsJamsostek.findFormula([general, special], { ...employee, cabang_bpjs: 'BATAM' }), null);
});
