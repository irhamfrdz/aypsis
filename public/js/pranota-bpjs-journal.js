(function (root) {
    const compact = value => String(value || '').toUpperCase().replace(/[\s_-]+/g, '');
    const categories = [
        ['CREW', 'Crew', '5501.001 - Biaya Gaji ABK'],
        ['HARIAN', 'Harian', '6101.005 - Biaya Makan Jakarta'],
        ['TRANSFER', 'Transfer', '6101.001 - Biaya Gaji Karyawan Tetap Jakarta'],
        ['TUNAI', 'Tunai', '6101.001 - Biaya Gaji Karyawan Tetap Jakarta'],
        ['KANTOR', 'Kantor', '6201.001 - Biaya Gaji Karyawan Tetap Batam'],
        ['LAPANGAN', 'Lapangan', '6201.002 - Biaya Upah Harian Batam'],
        ['NONKARYPBM', 'Non Kary - PBM', '5501.001 - Biaya Insentif Supir Truck Jakarta'],
        ['NONKARYUMKM', 'Non Kary - UMKM', '5501.001 - Biaya Insentif Supir Truck Jakarta'],
        ['NONKARYUMKMKIS', 'Non Kary - UMKM KIS', '5501.001 - Biaya Insentif Supir Truck Jakarta'],
        ['NONKARY', 'Non Kary', '6101.007 - Biaya Pengobatan Jakarta'],
        ['NONKARYUMKMNONPP', 'Non Kary - UMKM non PP', '6101.007 - Biaya Pengobatan Jakarta'],
    ];
    const fields = ['bpjs_kesehatan', 'bpjs_ketenagakerjaan', 'jht_biaya', 'jht_hutang',
        'jkk_tunjangan', 'jkm_tunjangan', 'jp_biaya', 'jp_hutang', 'jkk_hutang',
        'bpu_jkk_tunjangan', 'bpu_jkm', 'noncrew_jht_biaya', 'noncrew_jht_hutang',
        'noncrew_jkk_tunjangan', 'noncrew_jkm_tunjangan'];
    function calculate(records) {
        const groups = new Map(categories.map(([key, label, account]) => [key, { key, label, account, tunjangan: 0, biaya: 0, hutang: 0 }]));
        function group(type, fallback) {
            let key = compact(type);
            if (key === 'BPUCREW') key = 'CREW';
            if (!key) key = compact(fallback) === 'BPUCREW' ? 'CREW' : 'LAINNYA';
            if (!groups.has(key)) key = 'LAINNYA';
            if (!groups.has(key)) groups.set(key, { key, label: 'Lainnya / tipe belum dipetakan', account: '-', tunjangan: 0, biaya: 0, hutang: 0 });
            return groups.get(key);
        }
        records.forEach(record => {
            const employee = record.employee || {}, amounts = record.amounts || {};
            const cents = key => Math.round((Number(amounts[key]) || 0) * 100);
            const sum = keys => keys.reduce((total, key) => total + cents(key), 0);
            const jknTotal = cents('bpjs_kesehatan') + cents('bpjs_ketenagakerjaan');
            if (jknTotal) {
                const target = group(employee.tipe_jkn, employee.group_jkn);
                if (compact(employee.group_jkn) === 'JKNKIS' && compact(employee.tipe_jkn) === 'NONKARY') {
                    target.biaya += jknTotal;
                } else {
                    const biaya = amounts.kis_biaya !== undefined ? cents('kis_biaya')
                        : (employee.is_special_case ? cents('bpjs_kesehatan') : 0);
                    target.biaya += biaya;
                    target.tunjangan += cents('bpjs_kesehatan') - biaya;
                    target.hutang += cents('bpjs_ketenagakerjaan');
                }
            }
            const cost = sum(['jht_biaya', 'noncrew_jht_biaya', 'jp_biaya']);
            const debt = sum(['jht_hutang', 'jkk_hutang', 'noncrew_jht_hutang', 'jp_hutang']);
            const allowance = sum(['jkk_tunjangan', 'jkm_tunjangan', 'bpu_jkk_tunjangan', 'bpu_jkm', 'noncrew_jkk_tunjangan', 'noncrew_jkm_tunjangan']);
            if (cost || debt || allowance) {
                const target = group(employee.tipe_bp_jamsostek, employee.group_bp_jamsostek);
                const type = compact(employee.tipe_bp_jamsostek), jamsostekGroup = compact(employee.group_bp_jamsostek);
                if (type === 'NONKARY' && jamsostekGroup === 'PPU') {
                    target.biaya += cost + debt + allowance;
                } else {
                    const shift = type === 'NONKARYUMKMNONPP' && ['BPUSUPIR', 'BPUSUPIRJKTPLUIT', 'CILANDAKHL'].includes(jamsostekGroup)
                        ? sum(record.aggregate_jamsostek ? ['jkk_tunjangan', 'jkm_tunjangan']
                            : ['noncrew_jkk_tunjangan', 'noncrew_jkm_tunjangan']) : 0;
                    target.biaya += cost + shift;
                    target.tunjangan += allowance - shift;
                    target.hutang += debt;
                }
            }
        });
        const rows = [...groups.values()].map(row => ({ ...row, tunjangan: row.tunjangan / 100, biaya: row.biaya / 100, hutang: row.hutang / 100 }));
        const totalCents = key => [...groups.values()].reduce((total, row) => total + row[key], 0);
        const tunjangan = totalCents('tunjangan'), biaya = totalCents('biaya'), hutang = totalCents('hutang');
        return { rows, totals: { tunjangan: tunjangan / 100, biaya: biaya / 100, hutang: hutang / 100,
            debit: (tunjangan + biaya) / 100, grand_total: (tunjangan + biaya + hutang) / 100 } };
    }
    function render(records) {
        const section = document.querySelector('[data-bpjs-journal]');
        if (!section) return;
        const journal = calculate(records);
        const formatter = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const fragment = document.createDocumentFragment();
        journal.rows.forEach(row => {
            const tr = document.createElement('tr');
            const label = document.createElement('th');
            label.scope = 'row';
            label.textContent = row.label;
            tr.appendChild(label);
            for (const key of ['tunjangan', 'biaya', 'hutang']) {
                const td = document.createElement('td');
                td.className = 'bpjs-journal-' + key;
                td.textContent = row[key] ? formatter.format(row[key]) : '-';
                tr.appendChild(td);
            }
            const account = document.createElement('td');
            account.textContent = row.account;
            tr.appendChild(account);
            fragment.appendChild(tr);
        });
        section.querySelector('[data-journal-rows]').replaceChildren(fragment);
        Object.entries(journal.totals).forEach(([key, amount]) => {
            section.querySelector('[data-journal-total="' + key + '"]').textContent = formatter.format(amount);
        });
    }
    function renderFromRows(container) {
        render(Array.from(container.querySelectorAll('tr.detail-row')).filter(row =>
            row.querySelector('.karyawan-hidden-input')?.value && row.bpjsJournalRecord).map(row => row.bpjsJournalRecord));
    }
    root.BpjsJournal = { fields, calculate, render, renderFromRows };
})(globalThis);
