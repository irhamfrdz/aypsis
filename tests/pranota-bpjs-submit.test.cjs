const test = require('node:test');
const assert = require('node:assert/strict');
require('../public/js/pranota-bpjs-submit.js');
require('../public/js/pranota-bpjs-jamsostek.js');

test('264 employee rows become one payload while preserving IDs and fractional amounts', () => {
    const fields = [];
    for (let index = 1; index <= 264; index++) {
        for (const [key, value] of Object.entries({ karyawan_id: String(index), tipe_karyawan: 'App\\Models\\Karyawan',
            bpjs_kesehatan: '229.195', bpjs_ketenagakerjaan: '57.299', jht_biaya: '100.000,8' })) {
            fields.push({ name: `details[${index}][${key}]`, value, disabled: false,
                matches: () => key.startsWith('bpjs_') || key === 'jht_biaya',
                removeAttribute() { this.name = ''; } });
        }
    }
    const form = { payload: null, querySelectorAll: () => fields,
        querySelector() { return this.payload; }, appendChild(element) { this.payload = element; } };
    globalThis.document = { createElement: () => ({}) };
    prepareBpjsSubmission(form, BpjsJamsostek.parseAmount);
    const details = JSON.parse(form.payload.value);
    assert.equal(details.length, 264);
    assert.equal(details[263].karyawan_id, '264');
    assert.equal(details[263].tipe_karyawan, 'App\\Models\\Karyawan');
    assert.equal(details[263].bpjs_kesehatan, 229195);
    assert.equal(details[263].jht_biaya, 100000.8);
    assert.equal(form.payload.name, 'details_json');
    assert.equal(fields.filter(field => field.name).length, 0);
});
