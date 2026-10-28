const test = require('node:test');
const assert = require('node:assert/strict');
require('../public/js/pranota-bpjs-allowances.js');

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
    assert.deepEqual(BpjsAllowances.calculate({}), { ppu: 0, bpu: 0, jkn: 0 });
});

test('PPU allowance adds JKK and JKM only for PPU employees', () => {
    const amounts = { jkk_tunjangan: 13751.712, jkm_tunjangan: 17189.64 };
    assert.equal(BpjsAllowances.calculate({ ...amounts, group_bp_jamsostek: ' ppu-kantor ' }).ppu, 30941.352);
    for (const group of ['BPU-CREW', 'BPU-SUPIR', '', null]) {
        assert.equal(BpjsAllowances.calculate({ ...amounts, group_bp_jamsostek: group }).ppu, 0);
    }
});
