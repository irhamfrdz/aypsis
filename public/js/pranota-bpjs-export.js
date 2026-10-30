(function (root) {
    const components = {
        bpjs_kesehatan: ['JKN 4% (Total)', '.input-kes'],
        bpjs_ketenagakerjaan: ['JKN 1% (Hutang)', '.input-ket'],
        jht_biaya: ['PPU JHT 3,7% (Biaya)', '.input-jht-biaya'],
        jht_hutang: ['PPU JHT 2% (Hutang)', '.input-jht-hutang'],
        jkk_tunjangan: ['PPU JKK (Tunjangan)', '.input-jkk'],
        jkm_tunjangan: ['PPU JKM (Tunjangan)', '.input-jkm'],
        jp_biaya: ['PPU JP 2% (Biaya)', '.input-jp-biaya'],
        jp_hutang: ['PPU JP 1% (Hutang)', '.input-jp-hutang'],
        jkk_hutang: ['BPU Crew JHT (Karyawan)', '.input-jkk-hutang'],
        bpu_jkk_tunjangan: ['BPU Crew JKK (Tunjangan)', '.input-bpu-jkk'],
        bpu_jkm: ['BPU Crew JKM', '.input-bpu-jkm'],
        noncrew_jht_biaya: ['BPU Non Crew JHT (Biaya)', '.input-noncrew-jht-biaya'],
        noncrew_jht_hutang: ['BPU Non Crew JHT (Hutang)', '.input-noncrew-jht-hutang'],
        noncrew_jkk_tunjangan: ['BPU Non Crew JKK (Tunjangan)', '.input-noncrew-jkk'],
        noncrew_jkm_tunjangan: ['BPU Non Crew JKM', '.input-noncrew-jkm'],
    };
    const allowances = {
        ppu: 'Tunjangan PPU JKK + JKM', bpu: 'Tunjangan BPU JKK + JKM', jkn: 'Tunjangan JKN',
        kary_jht: 'Beban Karyawan JHT / JP / BPU JHT', kary_jkn: 'Beban Karyawan JKN',
        pt: 'Beban PT', non_kary: 'Beban Non Karyawan',
    };
    const groups = {
        total_jkn: ['bpjs_kesehatan', 'bpjs_ketenagakerjaan'],
        total_ppu: ['jht_biaya', 'jht_hutang', 'jkk_tunjangan', 'jkm_tunjangan', 'jp_biaya', 'jp_hutang'],
        total_bpu_crew: ['jkk_hutang', 'bpu_jkk_tunjangan', 'bpu_jkm'],
        total_bpu_noncrew: ['noncrew_jht_biaya', 'noncrew_jht_hutang', 'noncrew_jkk_tunjangan', 'noncrew_jkm_tunjangan'],
        total_bpu: ['total_bpu_crew', 'total_bpu_noncrew'],
        grand_total: ['total_jkn', 'total_ppu', 'total_bpu'],
    };
    const groupLabels = { total_jkn: 'Total JKN', total_ppu: 'Total PPU', total_bpu_crew: 'Total BPU Crew',
        total_bpu_noncrew: 'Total BPU Non Crew', total_bpu: 'Total BPU', grand_total: 'Grand Total' };
    const employeeColumns = {
        unique_id: 'ID Unik Karyawan', id: 'ID Karyawan', tipe_label: 'Status Karyawan',
        nik: 'NIK Karyawan', nama_lengkap: 'Nama Karyawan', nik_ktp: 'NIK KTP',
        jkn: 'No. JKN', no_ketenagakerjaan: 'No. Ketenagakerjaan', no_bpjs: 'No. BPJS',
        tanggal_lahir: 'Tanggal Lahir', tanggal_berhenti: 'Tanggal Berhenti', is_special_case: 'Special Case',
        group_jkn: 'Group JKN', tipe_jkn: 'Tipe JKN', dpp_jkn: 'DPP JKN',
        group_bp_jamsostek: 'Group BP Jamsostek', tipe_bp_jamsostek: 'Tipe BP Jamsostek',
        dpp_bp_jamsostek: 'DPP BP Jamsostek', cabang_bpjs: 'Cabang BPJS',
        cabang: 'Cabang', penempatan: 'Penempatan', lokasi: 'Lokasi', divisi: 'Divisi',
        posisi: 'Posisi', pekerjaan: 'Pekerjaan', group_posisi: 'Group Posisi',
    };
    const moneyKeys = new Set([...Object.keys(components), ...Object.keys(groups),
        'kis_tunjangan', 'kis_biaya', 'dpp_jkn', 'dpp_bp_jamsostek',
        ...Object.keys(allowances).map(key => 'allowance_' + key)]);

    function collectRows(rows, parseAmount) {
        return Array.from(rows).filter(row => row.querySelector('.karyawan-hidden-input')?.value).map(row => {
            const employee = { ...(row.bpjsEmployeeSnapshot || {}) };
            const amounts = Object.fromEntries(Object.entries(components).map(([key, [, selector]]) =>
                [key, parseAmount(row.querySelector(selector)?.value || '0')]));
            const employeeType = row.querySelector('.tipe-karyawan-hidden-input')?.value || employee.tipe_karyawan;
            const split = root.BpjsJknColumns.split(amounts.bpjs_kesehatan, employeeType, row.dataset.isSpecialCase);
            amounts.kis_tunjangan = split.tunjangan;
            amounts.kis_biaya = split.biaya;
            Object.entries(groups).forEach(([key, fields]) => {
                amounts[key] = fields.reduce((sum, field) => sum + amounts[field], 0);
            });
            const summary = {};
            const allowanceModes = {};
            Object.keys(allowances).forEach(key => {
                const input = row.querySelector(`[data-allowance="${key}"] input`);
                summary[key] = parseAmount(input?.value || '0');
                allowanceModes[key] = input?.disabled ? 'Tidak dihitung'
                    : (Object.hasOwn(row.allowanceOverrides || {}, key) ? 'Manual' : 'Auto');
                amounts['allowance_' + key] = summary[key];
            });
            return {
                employee, amounts, summary, allowanceModes, overrides: { ...(row.allowanceOverrides || {}) },
                modes: {
                    tipe_jkn: row.querySelector('.select-tipe-jkn')?.value || '',
                    tipe_bp_jamsostek: row.querySelector('.select-tipe-bp-jamsostek')?.value || '',
                },
                employeeType,
                voucher: row.querySelector('.bpjs-voucher-input')?.value || '',
            };
        });
    }

    // Long-form sheets preserve every field, including nested formula tiers and empty values.
    function flatten(value, prefix = '') {
        if (value !== null && typeof value === 'object') {
            const entries = Object.entries(value);
            if (entries.length) return entries.flatMap(([key, child]) => flatten(child, prefix ? prefix + '.' + key : key));
            return [[prefix, Array.isArray(value) ? '[]' : '{}', 'kosong']];
        }
        return [[prefix, value ?? '', value === null ? 'null' : typeof value]];
    }

    function text(value) {
        if (value === null || value === undefined) return '';
        return typeof value === 'object' ? JSON.stringify(value) : String(value);
    }

    function styleSheet(sheet, columns) {
        sheet.columns = columns.map(([key, header, width = 22]) => ({ key, header, width }));
        sheet.views = [{ state: 'frozen', ySplit: 1, xSplit: columns.length > 10 ? 6 : 0 }];
        sheet.autoFilter = { from: { row: 1, column: 1 }, to: { row: 1, column: columns.length } };
        const header = sheet.getRow(1);
        header.height = 48;
        header.eachCell(cell => {
            cell.font = { name: 'Calibri', size: 11, bold: true, color: { argb: 'FFFFFFFF' } };
            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF115E59' } };
            cell.alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
        });
        sheet.pageSetup = { orientation: 'landscape', paperSize: 8 };
    }

    function addRow(sheet, data, columns) {
        const row = sheet.addRow(data);
        const lines = Math.max(1, ...columns.map(([key, , width = 22]) => {
            const value = data[key];
            return typeof value === 'string' ? value.split('\n').reduce((sum, line) =>
                sum + Math.max(1, Math.ceil(line.length / (width - 2))), 0) : 1;
        }));
        row.height = Math.min(409, Math.max(30, lines * 15));
        columns.forEach(([key], index) => {
            const cell = row.getCell(index + 1);
            cell.font = { name: 'Calibri', size: 11 };
            cell.alignment = { vertical: 'middle', horizontal: moneyKeys.has(key) ? 'right' : 'left', wrapText: true };
            cell.numFmt = moneyKeys.has(key) ? '#,##0.00;[Red](#,##0.00);0.00' : (typeof data[key] === 'number' ? '0.########' : '@');
            if (row.number % 2 === 0) cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFF0FDFA' } };
        });
        return row;
    }

    function buildWorkbook(ExcelJS, records, metadata, formulas) {
        const workbook = new ExcelJS.Workbook();
        workbook.creator = 'AYPSIS';
        workbook.created = new Date();
        workbook.calcProperties.fullCalcOnLoad = true;
        const detail = workbook.addWorksheet('Rincian BPJS');
        const columns = [
            ['no', 'No.', 7],
            ...Object.entries(employeeColumns).map(([key, header]) => [key, header, key === 'nama_lengkap' ? 34 : 23]),
            ['tipe_karyawan', 'Model Karyawan', 32], ['mode_jkn', 'Mode Perhitungan JKN', 25],
            ['mode_jamsostek', 'Mode Perhitungan Jamsostek', 25],
            ...Object.entries(components).map(([key, [header]]) => [key, header, 23]),
            ['kis_tunjangan', 'JKN 4% (Tunjangan)', 23], ['kis_biaya', 'JKN 4% (Biaya)', 23],
            ...Object.entries(groupLabels).map(([key, header]) => [key, header, 23]),
            ...Object.entries(allowances).flatMap(([key, header]) =>
                [['allowance_' + key, header, 28], ['mode_allowance_' + key, 'Mode ' + header, 20]]),
            ['no_voucher', 'No. Voucher', 25],
        ];
        styleSheet(detail, columns);
        const col = key => detail.getColumn(key).letter;
        records.forEach((record, index) => {
            const data = { no: index + 1, ...record.amounts, tipe_karyawan: text(record.employeeType),
                mode_jkn: record.modes.tipe_jkn, mode_jamsostek: record.modes.tipe_bp_jamsostek, no_voucher: record.voucher };
            Object.keys(employeeColumns).forEach(key => {
                const value = record.employee[key];
                data[key] = moneyKeys.has(key) && value !== null && value !== undefined && value !== ''
                    ? root.BpjsJamsostek.parseAmount(value) : text(value);
            });
            Object.keys(allowances).forEach(key => {
                data['mode_allowance_' + key] = record.allowanceModes[key];
            });
            const row = addRow(detail, data, columns);
            Object.entries(groups).forEach(([key, fields]) => {
                row.getCell(key).value = { formula: fields.map(field => col(field) + row.number).join('+'), result: record.amounts[key] };
            });
        });
        const totalRow = addRow(detail, { nama_lengkap: 'TOTAL (' + records.length + ' KARYAWAN)' }, columns);
        moneyKeys.forEach(key => {
            if (key.startsWith('dpp_')) return;
            const result = records.reduce((sum, record) => sum + (record.amounts[key] || 0), 0);
            totalRow.getCell(key).value = { formula: `SUM(${col(key)}2:${col(key)}${totalRow.number - 1})`, result };
        });
        totalRow.eachCell(cell => {
            cell.font = { name: 'Calibri', size: 11, bold: true };
            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFCCFBF1' } };
        });

        const summary = workbook.addWorksheet('Ringkasan Pranota');
        const summaryColumns = [['variabel', 'Variabel', 44], ['nilai', 'Nilai', 56]];
        styleSheet(summary, summaryColumns);
        Object.entries(metadata).forEach(([key, value]) => addRow(summary, { variabel: key, nilai: text(value) }, summaryColumns));
        addRow(summary, { variabel: 'Cakupan Export', nilai: 'Seluruh baris dan kolom, termasuk yang tersembunyi oleh filter.' }, summaryColumns);
        addRow(summary, { variabel: 'Jumlah Karyawan', nilai: records.length }, summaryColumns);
        [...Object.entries(groupLabels), ...Object.entries(allowances).map(([key, label]) => ['allowance_' + key, label])].forEach(([key, label]) => {
            const row = addRow(summary, { variabel: label }, summaryColumns);
            row.getCell(2).value = { formula: `'Rincian BPJS'!${col(key)}${totalRow.number}`,
                result: records.reduce((sum, record) => sum + record.amounts[key], 0) };
            row.getCell(2).numFmt = '#,##0.00;[Red](#,##0.00);0.00';
        });

        const variables = workbook.addWorksheet('Variabel Karyawan');
        const variableColumns = [['id', 'ID Unik Karyawan', 28], ['nama', 'Nama Karyawan', 34],
            ['variabel', 'Variabel', 48], ['nilai', 'Nilai', 40], ['jenis', 'Jenis Nilai', 16]];
        styleSheet(variables, variableColumns);
        records.forEach(record => {
            const values = { karyawan: record.employee, tipe_karyawan: record.employeeType, mode: record.modes,
                komponen: record.amounts, ringkasan: record.summary, mode_ringkasan: record.allowanceModes,
                tunjangan_manual: record.overrides, no_voucher: record.voucher };
            flatten(values).forEach(([key, value, type]) => addRow(variables, {
                id: text(record.employee.unique_id), nama: text(record.employee.nama_lengkap),
                variabel: key, nilai: typeof value === 'boolean' ? String(value) : value, jenis: type,
            }, variableColumns));
        });

        const formulaSheet = workbook.addWorksheet('Rumus BPJS');
        const formulaColumns = [['no', 'No. Rumus', 12], ['jenis', 'Jenis BPJS', 16], ['group', 'Group', 30],
            ['tipe', 'Tipe', 28], ['cabang', 'Cabang BPJS', 24], ['variabel', 'Variabel Rumus', 40],
            ['nilai', 'Nilai', 40], ['jenis_nilai', 'Jenis Nilai', 16]];
        styleSheet(formulaSheet, formulaColumns);
        formulas.forEach((formula, index) => flatten(formula).forEach(([key, value, type]) => addRow(formulaSheet, {
            no: index + 1, jenis: text(formula.jenis), group: text(formula.group_name), tipe: text(formula.tipe),
            cabang: text(formula.cabang_bpjs), variabel: key, nilai: typeof value === 'boolean' ? String(value) : value, jenis_nilai: type,
        }, formulaColumns)));
        [detail, summary, variables, formulaSheet].forEach(sheet => {
            sheet.autoFilter = { from: { row: 1, column: 1 },
                to: { row: sheet === detail ? Math.max(1, totalRow.number - 1) : sheet.rowCount, column: sheet.columnCount } };
        });
        return workbook;
    }

    root.BpjsExcelExport = { collectRows, buildWorkbook };
})(globalThis);
