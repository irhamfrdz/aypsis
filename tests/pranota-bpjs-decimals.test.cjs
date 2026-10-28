const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
require('../public/js/pranota-bpjs-jkn.js');
require('../public/js/pranota-bpjs-jamsostek.js');

test('actual create and edit calculations preserve fractional JKN contributions and debt', () => {
    const names = ['inputKes', 'inputKet', 'inputJhtBiaya', 'inputJhtHutang', 'inputJkk', 'inputJkm',
        'inputBpuJht', 'inputBpuJkk', 'inputBpuJkm', 'inputNcJhtBiaya', 'inputNcJhtHutang',
        'inputNcJkk', 'inputNcJkm', 'inputJpBiaya', 'inputJpHutang'];
    const employee = { id: 1, unique_id: 'Karyawan_1', group_jkn: 'JKN-KIS', tipe_jkn: 'KANTOR', dpp_jkn: '5.729.880,00' };
    const formula = { jenis: 'jkn', group_name: 'JKN-KIS', tipe: 'KANTOR', tunjangan_persen: 4, hutang_persen: 1 };
    for (const page of ['create', 'edit']) {
        const source = fs.readFileSync(`resources/views/pranota-bpjs/${page}.blade.php`, 'utf8');
        const format = new Function('amountFormatter', source.match(/function formatNumber\(num\) \{[\s\S]*?\n    \}/)[0] + '; return formatNumber;')(new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        assert.equal(format(229195.2), '229.195,20');
        assert.equal(format(57298.8), '57.298,80');
        const start = source.indexOf('        function calculateBpjsForKaryawan(');
        const end = source.indexOf('        const handleKaryawanChange', start);
        const inputs = Object.fromEntries(names.map(name => [name, { value: '0,00' }]));
        const calculate = new Function('karyawans', 'rumusBpjs', 'parseIdNumber', 'formatNumber', 'document',
            'updateSubtotal', 'findKaryawan', 'updateInfoBadgeJkn', ...names,
            source.slice(start, end) + '; return calculateBpjsForKaryawan;');
        calculate([employee], [formula], BpjsJamsostek.parseAmount, format, { getElementById: () => null },
            () => {}, () => employee, () => {}, ...Object.values(inputs))(employee.unique_id, 'tunjangan_hutang');
        assert.equal(inputs.inputKes.value, '229.195,20');
        assert.equal(inputs.inputKet.value, '57.298,80');
    }
});
