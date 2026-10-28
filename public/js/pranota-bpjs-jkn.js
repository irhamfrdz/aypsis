(function (root) {
    const compact = value => String(value || '').trim().toUpperCase().replace(/[\s_-]+/g, '');

    function findFormula(records, employee) {
        const group = compact(employee.group_jkn);
        const type = compact(employee.tipe_jkn);
        if (!group) return null;
        let candidates = records.filter(record => record.jenis === 'jkn' && compact(record.group_name) === group);
        if (type) {
            candidates = candidates.filter(record => compact(record.tipe) === type);
        }
        // Never choose another type just because it appears first in the master.
        return candidates.length === 1 ? candidates[0] : null;
    }

    function calculate(record, dpp) {
        if (!record) return { contribution: 0, debt: 0 };
        const tunjangan = parseFloat(record.tunjangan_persen) || 0;
        const biaya = parseFloat(record.biaya_persen) || 0;
        const hutang = parseFloat(record.hutang_persen) || 0;
        return {
            contribution: dpp * (tunjangan || biaya) / 100,
            debt: dpp * hutang / 100,
        };
    }

    function split(amount, employeeType, isSpecialCase = false) {
        const special = isSpecialCase === true || isSpecialCase === 1 || isSpecialCase === '1';
        const biaya = ['Karyawan', 'KaryawanTidakTetap'].includes(String(employeeType || '').split('\\').pop()) && special;
        return { tunjangan: biaya ? 0 : amount, biaya: biaya ? amount : 0 };
    }

    function syncRow(row) {
        const input = row.querySelector('.input-kes');
        const tunjangan = row.querySelector('[data-jkn-tunjangan]');
        const biaya = row.querySelector('[data-jkn-biaya]');
        if (!input || !tunjangan || !biaya) return;
        const type = row.querySelector('.tipe-karyawan-hidden-input')?.value || row.dataset.tipeKaryawan;
        const useBiaya = split(1, type, row.dataset.isSpecialCase).biaya === 1;
        const zero = row.querySelector('.jkn-zero') || document.createElement('span');
        zero.className = 'jkn-zero block text-right text-xs text-gray-400 px-2';
        zero.textContent = '0';
        const active = useBiaya ? biaya : tunjangan;
        const inactive = useBiaya ? tunjangan : biaya;
        if (input.parentElement !== active) active.replaceChildren(input);
        if (zero.parentElement !== inactive) inactive.replaceChildren(zero);
        input.setAttribute('aria-label', useBiaya ? 'KIS 4% (BIAY)' : 'KIS 4% (TUNJ)');
    }

    function syncEmploymentStatus(row, employee) {
        const badge = row.querySelector('.employee-termination-badge');
        if (!badge) return;
        const terminated = Boolean(employee?.tanggal_berhenti);
        badge.textContent = terminated ? 'Sudah Berhenti' : '';
        badge.classList.toggle('hidden', !terminated);
        badge.title = terminated && employee.is_special_case
            ? 'Tetap ikut BPJS karena Special Case aktif' : '';
    }

    root.BpjsJknColumns = { split, syncRow, findFormula, calculate, syncEmploymentStatus };
})(globalThis);
