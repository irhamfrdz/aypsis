const assert = require('node:assert/strict');
const test = require('node:test');
require('../public/js/pranota-bpjs-filters.js');
require('../public/js/pranota-bpjs-jkn.js');
require('../public/js/pranota-bpjs-jamsostek.js');
require('../public/js/bpjs-effective-values.js');
require('../public/js/pranota-bpjs-premium.js');
const total = globalThis.BpjsPremium.total;
const employee = { unique_id: 'Karyawan_1', group_jkn: 'JKN', tipe_jkn: 'KIS',
    dpp_jkn: '2.000.000,00', group_bp_jamsostek: 'BPU-SUPIR',
    tipe_bp_jamsostek: 'NON CREW', dpp_bp_jamsostek: '2.200.000,00' };
const jkn = { jenis: 'jkn', group_name: 'JKN', tipe: 'KIS', tunjangan_persen: 4, hutang_persen: 1 };
const nonCrew = { jenis: 'jamsostek', group_name: 'BPU-SUPIR', tipe: 'NON CREW',
    jht_biaya: 2, jht_hutang: 1, jkk_tunjangan: 1, jkm_tunjangan: 6800,
    diskon_status: 'ada', diskon_tipe: 'persen', diskon_nilai: 50 };

test('picker premium totals JKN and all non-crew components with discount exemptions', () => {
    assert.equal(total(employee, [jkn, nonCrew]), 158400);
    assert.equal(total(employee, [jkn, { ...nonCrew, diskon_pengecualian: ['Karyawan_1'] }]), 172800);
});

test('PPU premium follows JP DPP cap and age limit without double-counting employee debt', () => {
    const ppu = { jenis: 'jamsostek', group_name: 'PPU', tipe: 'PPU', jht_biaya: 3.7,
        jht_hutang: 2, jkk_tunjangan: 0.24, jkm_tunjangan: 0.3,
        jp_biaya: 2, jp_hutang: 1, jp_max_dpp: 1000000, jp_max_age: 59 };
    const person = { ...employee, group_bp_jamsostek: 'PPU', tipe_bp_jamsostek: 'PPU', tanggal_lahir: '1967-11-27' };
    assert.equal(total(person, [jkn, ppu], '2026-11-26'), 213280);
    assert.equal(total(person, [jkn, ppu], '2026-11-27'), 193280);
});

test('crew premium uses tiers, additional debt, and effective period JKN values', () => {
    const crew = { jenis: 'jamsostek', group_name: 'BPU-CREW', tipe: 'CREW',
        hutang_persen: 1, tunjangan_persen: 1, biaya_persen: 6800,
        hutang_tiers: [{ dpp: 2200000, potongan: 5720 }],
        diskon_status: 'ada', diskon_tipe: 'persen', diskon_nilai: 50 };
    const person = { ...employee, group_bp_jamsostek: 'BPU-CREW', tipe_bp_jamsostek: 'CREW' };
    const dated = { ...jkn, bpjs_effective_values: [
        { valid_from: '2026-12-01', valid_until: '2026-12-31', values: { nominal_dpp: 3000000 } }
    ] };
    assert.equal(total(person, [BpjsEffectiveValues.resolve(dated, '2026-12-31'), crew]), 186400);
    assert.equal(total(person, [BpjsEffectiveValues.resolve(dated, '2027-01-31'), crew]), 136400);
});

test('employees without formulas have zero premium and reimbursement uses the DPP', () => {
    assert.equal(total(employee, []), 0);
    assert.equal(total({ ...employee, group_jkn: 'JKN KIS REIMBURSEMENT' }, []), 2000000);
});
