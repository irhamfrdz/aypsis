const assert = require('node:assert/strict');
const test = require('node:test');
const fs = require('node:fs');
require('../public/js/pranota-bpjs-filters.js');
require('../public/js/pranota-bpjs-jamsostek.js');
const calculator = globalThis.BpjsJamsostek;

const crew = {
    jenis: 'jamsostek', group_name: 'BPU-ALEXINDO PLUIT', tipe: 'CREW', cabang_bpjs: null,
    tunjangan_persen: '1.00', biaya_persen: '6800.00',
    hutang_tiers: [{ dpp: '2200000.00', potongan: '5720.00' }, { dpp: 4450000, potongan: 5720 }],
    diskon_status: 'ada', diskon_tipe: 'persen', diskon_nilai: '50',
};
const employee = {
    id: 1, unique_id: 'Karyawan_1', group_bp_jamsostek: 'BPU-ALEXINDO PLUIT',
    tipe_bp_jamsostek: 'Crew', cabang_bpjs: 'ALEXINDO PLUIT', dpp_bp_jamsostek: '2.200.000,00',
};

test('formula selection uses group, type, and branch without selecting another group or type', () => {
    const nonCrew = { ...crew, tipe: 'NON KARY' };
    assert.equal(calculator.findFormula([nonCrew, crew], employee), crew);
    assert.equal(calculator.findFormula([nonCrew], employee), null);
    assert.equal(calculator.findFormula([{ ...crew, group_name: 'BPU-JAKSEL' }], employee), null);
    const branch = { ...crew, cabang_bpjs: 'ALEXINDO PLUIT' };
    assert.equal(calculator.findFormula([crew, branch], employee), branch);
    assert.equal(calculator.findFormula([{ ...crew, cabang_bpjs: 'JAKSEL' }], employee), null);
    assert.equal(calculator.findFormula([nonCrew, crew], { ...employee, tipe_bp_jamsostek: '' }), null);
});

test('legacy crew names without a master type still select the crew formula', () => {
    const legacy = { ...crew, group_name: 'BPU-CREW', tipe: null, cabang_bpjs: 'PLUIT' };
    assert.equal(calculator.findFormula([legacy], { ...employee, group_bp_jamsostek: 'BPU-CREW', cabang_bpjs: 'PLUIT' }), legacy);
});

test('DPP parses Indonesian formatting and database decimals consistently', () => {
    for (const value of [2200000, '2200000', '2200000.00', ' 2.200.000,00 ']) {
        assert.equal(calculator.parseAmount(value), 2200000);
    }
    assert.equal(calculator.parseAmount('5.720'), 5720);
    assert.equal(calculator.parseAmount('5.720,50'), 5720.5);
});

test('crew calculation applies matching DPP tiers and the existing discount formula', () => {
    assert.deepEqual(calculator.calculateCrew(crew, '2.200.000,00'), { jht: 5720, jkk: 5280, jkm: 3400 });
    assert.deepEqual(calculator.calculateCrew({ ...crew, hutang_tiers: JSON.stringify(crew.hutang_tiers) }, '4450000.00'), {
        jht: 5720, jkk: 16530, jkm: 3400,
    });
});

test('the actual pranota row calculation fills crew inputs for a custom group with type CREW', () => {
    const view = fs.readFileSync('resources/views/pranota-bpjs/create.blade.php', 'utf8');
    const start = view.indexOf('        function calculateBpjsForKaryawan(');
    const end = view.indexOf('        const handleKaryawanChange', start);
    const inputNames = ['inputKes', 'inputKet', 'inputJhtBiaya', 'inputJhtHutang', 'inputJkk', 'inputJkm',
        'inputBpuJht', 'inputBpuJkk', 'inputBpuJkm', 'inputNcJhtBiaya', 'inputNcJhtHutang',
        'inputNcJkk', 'inputNcJkm', 'inputJpBiaya', 'inputJpHutang'];
    const inputs = Object.fromEntries(inputNames.map(name => [name, { value: '999' }]));
    const load = new Function('karyawans', 'rumusBpjs', 'parseIdNumber', 'formatNumber', 'document',
        'updateSubtotal', ...inputNames, view.slice(start, end) + '; return calculateBpjsForKaryawan;');
    const run = load([employee], [{ ...crew, tipe: 'NON KARY' }, crew], calculator.parseAmount,
        value => String(value), { getElementById: () => null }, () => {}, ...Object.values(inputs));
    run(employee.unique_id, 'tunjangan_hutang');
    assert.equal(inputs.inputBpuJht.value, '5720');
    assert.equal(inputs.inputBpuJkk.value, '5280');
    assert.equal(inputs.inputBpuJkm.value, '3400');
    assert.equal(inputs.inputNcJhtHutang.value, '0');
    assert.equal(inputs.inputNcJkk.value, '0');
    assert.equal(globalThis.BpjsMasterFilters.total([crew], crew.group_name, 'CREW', {
        bpu_crew: Number(inputs.inputBpuJht.value) + Number(inputs.inputBpuJkk.value) + Number(inputs.inputBpuJkm.value),
    }), 14400);
});
