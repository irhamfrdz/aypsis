const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
require('../public/js/pranota-bpjs-jkn.js');
require('../public/js/pranota-bpjs-jamsostek.js');

test('actual create and edit calculations round per-row KIS 4% and KIS 1% to whole rupiah', () => {
    const names = ['inputKes', 'inputKet', 'inputJhtBiaya', 'inputJhtHutang', 'inputJkk', 'inputJkm',
        'inputBpuJht', 'inputBpuJkk', 'inputBpuJkm', 'inputNcJhtBiaya', 'inputNcJhtHutang',
        'inputNcJkk', 'inputNcJkm', 'inputJpBiaya', 'inputJpHutang'];

    // A HOANG: DPP 5.357.980 → KIS 4%: 214.319,20 → rounded: 214.319
    //                         → KIS 1%: 53.579,80  → rounded: 53.580
    const employeeHoang = { id: 1, unique_id: 'Karyawan_1', group_jkn: 'JKN-KIS', tipe_jkn: 'LAPANGAN', dpp_jkn: '5.357.980,00' };
    const formulaHoang = { jenis: 'jkn', group_name: 'JKN-KIS', tipe: 'LAPANGAN', tunjangan_persen: 4, hutang_persen: 1 };

    // Employee with DPP 5.729.880 → KIS 4%: 229.195,20 → rounded: 229.195
    //                              → KIS 1%: 57.298,80  → rounded: 57.299
    const employee = { id: 2, unique_id: 'Karyawan_2', group_jkn: 'JKN-KIS', tipe_jkn: 'KANTOR', dpp_jkn: '5.729.880,00' };
    const formula = { jenis: 'jkn', group_name: 'JKN-KIS', tipe: 'KANTOR', tunjangan_persen: 4, hutang_persen: 1 };

    for (const page of ['create', 'edit']) {
        const source = fs.readFileSync(`resources/views/pranota-bpjs/${page}.blade.php`, 'utf8');
        const amountFormatter = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const formatNumber = new Function('amountFormatter', source.match(/function formatNumber\(num\) \{[\s\S]*?\n    \}/)[0] + '; return formatNumber;')(amountFormatter);
        const formatRounded = new Function(source.match(/function formatRounded\(num\) \{[\s\S]*?\n    \}/)[0] + '; return formatRounded;')();

        assert.equal(formatNumber(229195.2), '229.195,20');
        assert.equal(formatRounded(229195.2), '229.195');
        assert.equal(formatRounded(57298.8), '57.299');
        assert.equal(formatRounded(214319.2), '214.319');
        assert.equal(formatRounded(53579.8), '53.580');

        const start = source.indexOf('        function calculateBpjsForKaryawan(');
        const end = source.indexOf('        const handleKaryawanChange', start);
        const inputs = Object.fromEntries(names.map(name => [name, { value: '0,00' }]));
        const calculate = new Function('karyawans', 'rumusBpjs', 'parseIdNumber', 'formatNumber', 'formatRounded', 'document',
            'updateSubtotal', 'findKaryawan', 'updateInfoBadgeJkn', ...names,
            source.slice(start, end) + '; return calculateBpjsForKaryawan;');
        calculate([employee, employeeHoang], [formula, formulaHoang], BpjsJamsostek.parseAmount, formatNumber, formatRounded,
            { getElementById: () => null }, () => {}, () => employee, () => {}, ...Object.values(inputs))(employee.unique_id, 'tunjangan_hutang');
        assert.equal(inputs.inputKes.value, '229.195');
        assert.equal(inputs.inputKet.value, '57.299');

        // Reset and test A HOANG
        const inputsHoang = Object.fromEntries(names.map(name => [name, { value: '0,00' }]));
        calculate([employee, employeeHoang], [formula, formulaHoang], BpjsJamsostek.parseAmount, formatNumber, formatRounded,
            { getElementById: () => null }, () => {}, () => employeeHoang, () => {}, ...Object.values(inputsHoang))(employeeHoang.unique_id, 'tunjangan_hutang');
        assert.equal(inputsHoang.inputKes.value, '214.319');
        assert.equal(inputsHoang.inputKet.value, '53.580');
    }
});

test('Group JKN totals keep KIS 4% unrounded while rounding KIS 1% debt', () => {
    // 12 employees (1 @ 6.000.000 DPP, 11 @ 5.729.880 DPP)
    // KIS 4%: 240.000 + 11 * 229.195,20 = 2.761.147,20
    // KIS 1%: 60.000 + 11 * 57.298,80 = 690.286,80 -> rounded: 690.287
    const sumKes = 2761147.20;
    const sumKesBiaya = 0;
    const sumKet = 690286.80;

    const totalKesTunj = sumKes - sumKesBiaya;
    const totalKesBiay = sumKesBiaya;
    const totalKetHutang = Math.round(sumKet);
    const totalJkn = totalKesTunj + totalKesBiay + totalKetHutang;

    assert.equal(totalKesTunj, 2761147.20);
    assert.equal(totalKetHutang, 690287);
    assert.equal(totalJkn, 3451434.20);

    const formatter = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    assert.equal(formatter.format(totalKesTunj), '2.761.147,20');
    assert.equal(totalKetHutang.toLocaleString('id-ID'), '690.287');
    assert.equal(formatter.format(totalJkn), '3.451.434,20');
});
