const test = require('node:test');
const assert = require('node:assert/strict');
require('../public/js/pranota-bpjs-submit.js');
require('../public/js/pranota-bpjs-jamsostek.js');

test('264 employee rows become one payload while preserving IDs and fractional amounts', () => {
    const fields = [];
    for (let index = 1; index <= 264; index++) {
        for (const [key, value] of Object.entries({ karyawan_id: String(index), tipe_karyawan: 'App\\Models\\Karyawan',
            bpjs_kesehatan: '229.195', bpjs_ketenagakerjaan: '57.299', jht_biaya: '100.000,8', no_voucher: '001/VC-BPJS' })) {
            fields.push({ name: `details[${index}][${key}]`, value, disabled: false,
                matches: () => key.startsWith('bpjs_') || key === 'jht_biaya' || key === 'no_voucher',
                removeAttribute() { this.name = ''; } });
        }
    }
    const form = { payload: null, inputs: {}, querySelectorAll: () => fields,
        querySelector(selector) { return this.inputs[selector]; },
        appendChild(element) { this.inputs[`[name="${element.name}"]`] = element; if (element.name === 'details_json') this.payload = element; } };
    globalThis.document = { createElement: () => ({}) };
    prepareBpjsSubmission(form, BpjsJamsostek.parseAmount);
    const details = JSON.parse(form.payload.value);
    assert.equal(details.length, 264);
    assert.equal(details[263].no_voucher, '001/VC-BPJS');
    assert.equal(details[263].karyawan_id, '264');
    assert.equal(details[263].tipe_karyawan, 'App\\Models\\Karyawan');
    assert.equal(details[263].bpjs_kesehatan, 229195);
    assert.equal(details[263].jht_biaya, 100000.8);
    assert.equal(form.payload.name, 'details_json');
    assert.equal(fields.filter(field => field.name).length, 0);
});

test('submission captures employee metadata, automatic zero and manual summaries plus formula variables', () => {
    const employee = { id: 7, unique_id: 'Karyawan_7', nama_lengkap: 'Nama Lama',
        dpp_jkn: '5500000', group_jkn: 'JKN-KIS', is_special_case: true };
    const cells = ['ppu', 'bpu', 'jkn', 'kary_jht', 'kary_jkn', 'pt', 'non_kary'].map(key => ({
        dataset: { allowance: key }, querySelector: () => ({ value: key === 'pt' ? '12.500,75' : '0,00' }),
    }));
    const row = { bpjsEmployeeSnapshot: employee, querySelectorAll: () => cells };
    const fields = Object.entries({ karyawan_id: '7', tipe_karyawan: 'App\\Models\\Karyawan', no_voucher: 'VC-007' })
        .map(([key, value]) => ({ name: `details[1][${key}]`, value, disabled: false,
            matches: () => false, closest: () => row, removeAttribute() { this.name = ''; } }));
    const inputs = {};
    const form = { querySelectorAll: () => fields, querySelector: selector => inputs[selector],
        appendChild: element => { inputs[`[name="${element.name}"]`] = element; } };
    globalThis.document = { createElement: () => ({}) };
    const formulas = [{ jenis: 'jkn', nilai: 4, jp_max_age: 59, diskon_nilai: 25 }];
    prepareBpjsSubmission(form, BpjsJamsostek.parseAmount, formulas);
    const detail = JSON.parse(inputs['[name="details_json"]'].value)[0];
    assert.deepEqual(detail.snapshot_karyawan, employee);
    assert.equal(detail.ringkasan_bpjs.pt, 12500.75);
    assert.equal(detail.ringkasan_bpjs.jkn, 0);
    assert.equal(detail.no_voucher, 'VC-007');
    assert.deepEqual(JSON.parse(inputs['[name="snapshot_rumus_json"]'].value), formulas);
});
