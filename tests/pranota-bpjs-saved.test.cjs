const test = require('node:test');
const assert = require('node:assert/strict');
require('../public/js/pranota-bpjs-saved.js');

test('saved PPU and BPU components, manual zero and decimals survive without recalculation', () => {
    const detail = {bpjs_kesehatan: '100.25', bpjs_ketenagakerjaan: '0.00',
        jht_biaya: 999, jkk_tunjangan: 999,
        rincian_bpjs: {jht_biaya: 20.75, noncrew_jht_biaya: 30.5, bpu_jkk_tunjangan: 0, noncrew_jkk_tunjangan: 12.25}};
    const values = BpjsSavedDetail.amounts(detail);
    assert.equal(values.bpjs_kesehatan, 100.25);
    assert.equal(values.bpjs_ketenagakerjaan, 0);
    assert.equal(values.jht_biaya, 20.75);
    assert.equal(values.noncrew_jht_biaya, 30.5);
    assert.equal(values.bpu_jkk_tunjangan, 0);
    assert.equal(values.noncrew_jkk_tunjangan, 12.25);
    assert.equal(values.jkk_tunjangan, 0);
});

test('legacy BPU aggregates retain totals and are restored into BPU columns', () => {
    for (const group of ['BPU-CREW', 'BPU-NON KARY-UMKM', 'CILANDAK-HL']) {
        const values = BpjsSavedDetail.amounts({snapshot_bpjs: {karyawan: {group_bp_jamsostek: group}},
            jht_biaya: 25, jht_hutang: 50, jkk_tunjangan: 10, jkm_tunjangan: 15});
        assert.equal(Object.values(values).reduce((a, b) => a + b), 100);
        assert.equal(values.jkk_tunjangan, 0);
        assert.equal(group === 'BPU-CREW' ? values.bpu_jkk_tunjangan : values.noncrew_jkk_tunjangan, 10);
    }
});
