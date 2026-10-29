const test = require('node:test');
const assert = require('node:assert/strict');
require('../public/js/pranota-bpjs-allowances.js');

test('terminated CREW with Special Case retains employee JHT and JKN debt like an active employee', () => {
    const values = { group_bp_jamsostek: 'BPU-ALEXINDO PLUIT', tipe_bp_jamsostek: 'CREW',
        tipe_jkn: 'KARYAWAN', jkk_hutang: 5720, bpu_jkk_tunjangan: 16530,
        bpu_jkm: 3400, bpjs_ketenagakerjaan: 50000 };
    const active = BpjsAllowances.calculate(values);
    for (const flag of [true, 1, '1']) {
        const special = BpjsAllowances.calculate({ ...values, is_special_case: flag, tanggal_berhenti: '2026-10-01' });
        assert.equal(special.kary_jht, 5720);
        assert.equal(special.kary_jkn, 50000);
        assert.equal(special.non_kary, 0);
        assert.deepEqual(special, active);
    }
    const nonEmployee = BpjsAllowances.calculate({ ...values, is_special_case: true, tipe_bp_jamsostek: 'NON KARY' });
    assert.equal(nonEmployee.kary_jht, 0);
    assert.equal(nonEmployee.non_kary, 25650);
});

test('NON KARY employer burden follows the matching JKN-KIS and PPU membership totals', () => {
    const values = { kis_tunjangan: 229195.20, bpjs_ketenagakerjaan: 57298.80,
        jht_biaya: 100000.8, jht_hutang: 20000, jkk_tunjangan: 1000,
        jkm_tunjangan: 2000, jp_biaya: 10000, jp_hutang: 11000, noncrew_jht_biaya: 6000 };
    const jkn = { group_jkn: ' jkn - kis ', tipe_jkn: ' non kary ' };
    const ppu = { group_bp_jamsostek: 'ppu', tipe_bp_jamsostek: 'NON_KARY' };
    assert.equal(BpjsAllowances.calculate({ ...values, ...jkn }).pt, 286494);
    assert.equal(BpjsAllowances.calculate({ ...values, ...ppu }).pt, 144000.8);
    assert.equal(BpjsAllowances.calculate({ ...values, ...jkn, ...ppu }).pt, 430494.8);
    assert.equal(BpjsAllowances.calculate({ ...values, ...jkn, kis_tunjangan: 0, kis_biaya: 229195.2 }).pt, 286494);
    assert.equal(BpjsAllowances.calculate({ ...jkn, ...ppu }).pt, 0);
    assert.equal(BpjsAllowances.calculate({ ...values, ...jkn, group_jkn: 'LAIN' }).pt, 116000.8);
    assert.equal(BpjsAllowances.calculate({ ...values, ...ppu, tipe_bp_jamsostek: 'KARYAWAN' }).pt, 116000.8);
    assert.equal(BpjsAllowances.calculate({ ...values, group_jkn: 'JKN-KIS', tipe_bp_jamsostek: 'NON KARY' }).pt, 116000.8);
    assert.equal(BpjsAllowances.effective(BpjsAllowances.calculate({ ...values, ...ppu }), { pt: 0 }).pt, 0);
});

test('NON KARY-UMKM Non PP employer burden uses noncrew JHT biaya plus JKK and JKM only', () => {
    const values = { noncrew_jht_biaya: 19000, noncrew_jkk_tunjangan: 9500.25,
        noncrew_jkm_tunjangan: 6800, jht_biaya: 100000, jp_biaya: 20000,
        kis_tunjangan: 214319.2, noncrew_jht_hutang: 50000 };
    for (const type of ['NON KARY-UMKM Non PP', ' non kary - umkm  non pp ', 'NON_KARY_UMKM_NON_PP']) {
        const result = BpjsAllowances.calculate({ ...values, tipe_bp_jamsostek: type });
        assert.equal(result.pt, 35300.25);
        assert.equal(BpjsAllowances.effective(result, { pt: 0 }).pt, 0);
    }
    for (const type of ['PPU', 'NON KARY-UMKM', '', 'NON KARY-UMKM PP']) {
        assert.equal(BpjsAllowances.calculate({ ...values, tipe_bp_jamsostek: type }).pt, 139000);
    }
    assert.equal(BpjsAllowances.calculate({ ...values, tipe_jkn: 'NON KARY-UMKM Non PP' }).pt, 139000);
});

test('employer burden adds only PPU JHT, PPU JP and noncrew JHT biaya', () => {
    const values = { jht_biaya: 198320, jp_biaya: 107200, noncrew_jht_biaya: 49000.25,
        kis_tunjangan: 214319.20, kis_biaya: 70000, jht_hutang: 107200, jp_hutang: 53600 };
    assert.equal(BpjsAllowances.calculate(values).pt, 354520.25);
    assert.equal(BpjsAllowances.calculate({ ...values, is_special_case: '1' }).pt, 354520.25);
    assert.equal(BpjsAllowances.effective(BpjsAllowances.calculate(values), { pt: 0 }).pt, 0);
});

test('manual allowances persist across recalculation, including zero, and reset to automatic when removed', () => {
    const automatic = { ppu: 28080, bpu: 5000, jkn: 229195.2 };
    const manual = { ppu: 30000.75, bpu: 0 };
    assert.deepEqual(BpjsAllowances.effective(automatic, manual), { ppu: 30000.75, bpu: 0, jkn: 229195.2 });
    assert.equal(BpjsAllowances.effective({ ...automatic, ppu: 29000 }, manual).ppu, 30000.75);
    delete manual.ppu;
    assert.equal(BpjsAllowances.effective(automatic, manual).ppu, 28080);
    assert.equal(automatic.ppu, 28080);
});

test('allowances group employer components without debt, JHT, or JP and preserve decimals', () => {
    const values = { group_bp_jamsostek: 'PPU', jkk_tunjangan: 13751.71, jkm_tunjangan: 17189.64,
        bpu_jkk_tunjangan: 22000, bpu_jkm: 3400, noncrew_jkk_tunjangan: 17250,
        noncrew_jkm_tunjangan: 6800, kis_tunjangan: 229195.2,
        jht_biaya: 100000, jht_hutang: 20000, jp_biaya: 30000, bpjs_ketenagakerjaan: 57298.8 };
    const summary = BpjsAllowances.calculate(values);
    assert.equal(summary.ppu.toFixed(2), '30941.35');
    assert.equal(summary.bpu, 49450);
    assert.equal(summary.jkn, 229195.2);
    assert.equal(values.jht_biaya, 100000);
    assert.deepEqual(BpjsAllowances.calculate({}), { ppu: 0, bpu: 0, jkn: 0, kary_jht: 0, kary_jkn: 0, pt: 0, non_kary: 0 });
});

test('PPU allowance adds JKK and JKM only for PPU employees', () => {
    const amounts = { jkk_tunjangan: 13751.712, jkm_tunjangan: 17189.64 };
    assert.equal(BpjsAllowances.calculate({ ...amounts, group_bp_jamsostek: ' ppu-kantor ' }).ppu, 30941.352);
    for (const group of ['BPU-CREW', 'BPU-SUPIR', '', null]) {
        assert.equal(BpjsAllowances.calculate({ ...amounts, group_bp_jamsostek: group }).ppu, 0);
    }
});

test('burdens sum the contribution amounts and route non employee memberships separately', () => {
    const values = { jht_biaya: 185000.25, jht_hutang: 100000, jp_biaya: 100000,
        jp_hutang: 50000, jkk_hutang: 20000, noncrew_jht_hutang: 10000,
        kis_tunjangan: 200000, bpjs_ketenagakerjaan: 50000, jkk_tunjangan: 12000, jkm_tunjangan: 15000 };
    const regular = BpjsAllowances.calculate(values);
    assert.equal(regular.kary_jht, 180000);
    assert.equal(regular.kary_jkn, 50000);
    assert.equal(regular.pt, 285000.25);
    assert.equal(regular.non_kary, 0);
    const nonJkn = BpjsAllowances.calculate({ ...values, tipe_jkn: 'NON KARY' });
    assert.equal(nonJkn.kary_jht, 180000);
    assert.equal(nonJkn.kary_jkn, 0);
    assert.equal(nonJkn.pt, 285000.25);
    assert.equal(nonJkn.non_kary, 250000);
    const special = BpjsAllowances.calculate({ ...values, is_special_case: '1' });
    assert.equal(special.kary_jht, 180000);
    assert.equal(special.kary_jkn, 50000);
    assert.equal(special.pt, 285000.25);
    assert.equal(special.non_kary, 0);
});
