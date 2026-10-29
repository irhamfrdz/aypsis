const test = require('node:test');
const assert = require('node:assert/strict');
require('../public/js/pranota-bpjs-allowances.js');

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
    assert.equal(special.kary_jht + special.kary_jkn, 0);
    assert.equal(special.pt, 285000.25);
    assert.equal(special.non_kary, 742000.25);
});
