const test = require('node:test');
const assert = require('node:assert/strict');
require('../public/js/pranota-bpjs-jkn.js');
require('../public/js/pranota-bpjs-jamsostek.js');
require('../public/js/pranota-bpjs-export.js');

const parse = BpjsJamsostek.parseAmount;
const componentInputs = {
    '.input-kes': '229.195,20', '.input-ket': '57.298,80',
    '.input-jht-biaya': '100.000,80', '.input-jht-hutang': '20.000,00',
    '.input-jkk': '1.000,00', '.input-jkm': '2.000,00', '.input-jp-biaya': '10.000,00', '.input-jp-hutang': '11.000,00',
    '.input-jkk-hutang': '3.000,00', '.input-bpu-jkk': '4.000,00', '.input-bpu-jkm': '5.000,00',
    '.input-noncrew-jht-biaya': '6.000,00', '.input-noncrew-jht-hutang': '7.000,00',
    '.input-noncrew-jkk': '8.000,00', '.input-noncrew-jkm': '9.000,00',
};
function fixture({ temporary = false, hidden = false, selected = true } = {}) {
    const model = temporary ? 'App\\Models\\KaryawanTidakTetap' : 'App\\Models\\Karyawan';
    const values = { ...componentInputs, '.karyawan-hidden-input': selected ? '1' : '',
        '.tipe-karyawan-hidden-input': model, '.select-tipe-jkn': 'tunjangan_hutang',
        '.select-tipe-bp-jamsostek': 'total', '.bpjs-voucher-input': '0001/VC-BPJS' };
    for (const key of ['ppu', 'bpu', 'jkn', 'kary_jht', 'kary_jkn', 'pt', 'non_kary']) {
        values[`[data-allowance="${key}"] input`] = key === 'pt' ? '12.500,75' : '0,00';
    }
    return {
        bpjsEmployeeSnapshot: { id: 1, unique_id: (temporary ? 'KaryawanTidakTetap' : 'Karyawan') + '_1',
            tipe_karyawan: model, tipe_label: temporary ? 'Tidak Tetap' : 'Tetap', nama_lengkap: '=Nama Uji',
            nik: '0000123', nik_ktp: '0000123456789012', jkn: '000998877', no_ketenagakerjaan: '000112233',
            group_jkn: 'JKN-KIS', tipe_jkn: 'KANTOR', dpp_jkn: '5.729.880,50',
            dpp_bp_jamsostek: '4450000.50', tanggal_lahir: '1980-01-01', is_special_case: temporary,
            grup: ['Crew', 'Kapal A'], cabang_bpjs: 'JAKARTA' },
        allowanceOverrides: { pt: 12500.75, jkn: 0 },
        dataset: { isSpecialCase: temporary ? '1' : '0' },
        classList: { contains: () => hidden },
        querySelector: selector => Object.hasOwn(values, selector) ? { value: values[selector] } : null,
    };
}

test('exports every iuran separately with decimals, all group totals and manual zero', () => {
    const [record] = BpjsExcelExport.collectRows([fixture()], parse);
    assert.equal(record.amounts.bpjs_kesehatan, 229195.2);
    assert.equal(record.amounts.kis_tunjangan, 229195.2);
    assert.equal(record.amounts.kis_biaya, 0);
    assert.equal(record.amounts.total_jkn, 286494);
    assert.equal(record.amounts.total_ppu, 144000.8);
    assert.equal(record.amounts.total_bpu_crew, 12000);
    assert.equal(record.amounts.total_bpu_noncrew, 30000);
    assert.equal(record.amounts.total_bpu, 42000);
    assert.equal(record.amounts.grand_total, 472494.8);
    assert.equal(record.summary.pt, 12500.75);
    assert.equal(record.summary.jkn, 0);
    assert.equal(record.overrides.jkn, 0);
    assert.equal(record.voucher, '0001/VC-BPJS');
});

test('includes filtered employees, preserves same-ID employee types and omits unselected placeholders', () => {
    const records = BpjsExcelExport.collectRows([fixture(), fixture({ temporary: true, hidden: true }),
        fixture({ selected: false })], parse);
    assert.equal(records.length, 2);
    assert.equal(records[0].employee.unique_id, 'Karyawan_1');
    assert.equal(records[1].employee.unique_id, 'KaryawanTidakTetap_1');
    assert.equal(records[1].amounts.kis_biaya, 229195.2);
    assert.equal(records[1].amounts.kis_tunjangan, 0);
    assert.equal(records[1].amounts.grand_total, records[0].amounts.grand_total);
});

test('zero iuran rows remain included and excluded summaries keep their displayed status', () => {
    const row = fixture();
    const find = row.querySelector;
    row.querySelector = selector => {
        if (Object.hasOwn(componentInputs, selector)) return { value: '0,00' };
        if (selector === '[data-allowance="jkn"] input') return { value: '0,00', disabled: true };
        return find(selector);
    };
    const [record] = BpjsExcelExport.collectRows([row], parse);
    assert.equal(record.amounts.grand_total, 0);
    assert.equal(record.summary.pt, 12500.75);
    assert.equal(record.allowanceModes.jkn, 'Tidak dihitung');
    assert.equal(record.overrides.jkn, 0);
    assert.equal(record.voucher, '0001/VC-BPJS');
});

// Set EXCELJS_TEST_PATH to the browser's existing ExcelJS bundle for XLSX round-trip verification.
const excelPath = process.env.EXCELJS_TEST_PATH;
test('XLSX round trip preserves all four sheets, numeric amounts, identifiers, formula variables and totals',
    { skip: !excelPath }, async () => {
        const ExcelJS = require(excelPath);
        const records = BpjsExcelExport.collectRows([fixture(), fixture({ temporary: true, hidden: true })], parse);
        const formulas = [{ id: 1, jenis: 'jamsostek', group_name: 'BPU', tipe: 'CREW', cabang_bpjs: null,
            nilai: 1, diskon_status: 'ada', diskon_nilai: 50, jp_max_dpp: 10000000, jp_max_age: 59,
            hutang_tiers: [{ min: 0, max: 1000000, nominal: 16800.5 }] }];
        const workbook = BpjsExcelExport.buildWorkbook(ExcelJS, records, {
            'Tanggal Pranota': '2026-10-30', 'Periode Bulan': '10', 'Periode Tahun': '2026',
            'Keterangan': 'Pengujian export lengkap', 'Filter Group di Layar': 'PPU',
        }, formulas);
        const buffer = await workbook.xlsx.writeBuffer();
        if (process.env.BPJS_EXPORT_QA_PATH) require('node:fs').writeFileSync(process.env.BPJS_EXPORT_QA_PATH, Buffer.from(buffer));
        const loaded = new ExcelJS.Workbook();
        await loaded.xlsx.load(buffer);
        assert.deepEqual(loaded.worksheets.map(sheet => sheet.name),
            ['Rincian BPJS', 'Ringkasan Pranota', 'Variabel Karyawan', 'Rumus BPJS']);
        const sheet = loaded.getWorksheet('Rincian BPJS');
        const headers = sheet.getRow(1).values;
        const value = (header, row = 2) => sheet.getRow(row).getCell(headers.indexOf(header)).value;
        assert.equal(sheet.rowCount, 4);
        assert.equal(value('NIK Karyawan'), '0000123');
        assert.equal(value('NIK KTP'), '0000123456789012');
        assert.equal(value('No. JKN'), '000998877');
        assert.equal(value('Nama Karyawan'), '=Nama Uji');
        assert.equal(value('No. Voucher'), '0001/VC-BPJS');
        assert.equal(value('DPP JKN'), 5729880.5);
        assert.equal(value('DPP BP Jamsostek'), 4450000.5);
        assert.equal(value('JKN 4% (Tunjangan)'), 229195.2);
        assert.equal(value('JKN 4% (Biaya)', 3), 229195.2);
        assert.equal(value('BPU Non Crew JHT (Biaya)'), 6000);
        assert.equal(value('Beban PT'), 12500.75);
        assert.equal(value('Mode Beban PT'), 'Manual');
        assert.equal(value('Mode Tunjangan JKN'), 'Manual');
        assert.equal(value('Mode Tunjangan PPU JKK + JKM'), 'Auto');
        assert.equal(value('Total BPU').result, 42000);
        assert.equal(value('Grand Total').result, 472494.8);
        assert.equal(value('Grand Total', 4).result, 944989.6);
        assert.match(value('Grand Total', 4).formula, /^SUM\([A-Z]+2:[A-Z]+3\)$/);
        assert.equal(sheet.views[0].xSplit, 6);
        assert.equal(sheet.views[0].ySplit, 1);
        const variables = loaded.getWorksheet('Variabel Karyawan');
        const variableRows = [];
        variables.eachRow((row, index) => { if (index > 1) variableRows.push(row.values); });
        assert.ok(variableRows.some(row => row[3] === 'karyawan.grup.1' && row[4] === 'Kapal A'));
        assert.ok(variableRows.some(row => row[3] === 'tunjangan_manual.jkn' && row[4] === 0));
        const formulaRows = [];
        loaded.getWorksheet('Rumus BPJS').eachRow((row, index) => { if (index > 1) formulaRows.push(row.values); });
        assert.ok(formulaRows.some(row => row[6] === 'hutang_tiers.0.nominal' && row[7] === 16800.5));
        assert.ok(formulaRows.some(row => row[6] === 'diskon_nilai' && row[7] === 50));
        const summaryRows = [];
        loaded.getWorksheet('Ringkasan Pranota').eachRow((row, index) => { if (index > 1) summaryRows.push(row.values); });
        assert.ok(summaryRows.some(row => row[1] === 'Grand Total' && row[2].result === 944989.6));
    });

test('both export buttons use the full workbook download with current form values and restored button state',
    { skip: !excelPath }, async () => {
        const fs = require('node:fs');
        const vm = require('node:vm');
        const ExcelJS = require(excelPath);
        const source = fs.readFileSync(require('node:path').join(__dirname, '../resources/views/pranota-bpjs/create.blade.php'), 'utf8');
        const start = source.indexOf('    async function exportToExcel()');
        const end = source.indexOf("    const btnExport = document.getElementById('btn-export-excel');", start);
        assert.equal((source.match(/addEventListener\('click', exportToExcel\)/g) || []).length, 2);
        const makeButton = () => ({ disabled: false, setAttribute(key, value) { this[key] = value; },
            removeAttribute(key) { delete this[key]; } });
        const controls = { 'btn-export-excel': makeButton(), 'btn-export-excel-2': makeButton(),
            tanggal_pranota: { value: '2026-10-30' }, periode_bulan: { value: '10' }, periode_tahun: { value: '2026' },
            keterangan: { value: 'Keterangan dari form' } };
        let saved, updates = 0, flushes = 0;
        const context = {
            ExcelJS, BpjsExcelExport, Blob, console, isGenerating: false, isRefreshing: false,
            container: { querySelectorAll: () => [fixture(), fixture({ temporary: true, hidden: true })] },
            pendingSearch: { flush() { flushes++; } }, pendingUpdate: { flush() { flushes++; } },
            BpjsAllowances: { update() { updates++; } }, parseIdNumber: parse, formatNumber: String,
            document: { getElementById: key => controls[key] },
            filterGroupSelect: { value: 'PPU' }, filterCabangSelect: { value: 'CREW' },
            searchKaryawanInput: { value: 'nama' }, rumusBpjs: [{ jenis: 'jkn', nilai: 4 }],
            saveAs(blob, filename) { saved = { blob, filename }; },
            alert(message) { assert.fail(message); },
        };
        await vm.runInNewContext(source.slice(start, end) + '\nexportToExcel();', context);
        assert.equal(flushes, 2);
        assert.equal(updates, 1);
        assert.equal(saved.filename, 'Pranota_BPJS_Lengkap_2026_10_2026-10-30.xlsx');
        const workbook = new ExcelJS.Workbook();
        await workbook.xlsx.load(await saved.blob.arrayBuffer());
        assert.equal(workbook.getWorksheet('Rincian BPJS').rowCount, 4);
        const summary = [];
        workbook.getWorksheet('Ringkasan Pranota').eachRow(row => summary.push(row.values));
        assert.ok(summary.some(row => row[1] === 'Keterangan' && row[2] === 'Keterangan dari form'));
        assert.ok(summary.some(row => row[1] === 'Pencarian di Layar' && row[2] === 'nama'));
        assert.equal(controls['btn-export-excel'].disabled, false);
        assert.equal(controls['btn-export-excel-2'].disabled, false);
        assert.equal(controls['btn-export-excel']['aria-busy'], undefined);
    });
