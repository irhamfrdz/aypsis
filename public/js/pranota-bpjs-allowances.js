(function (root) {
    function calculate(values) {
        const isPpu = String(values.group_bp_jamsostek || '').trim().toUpperCase().includes('PPU');
        return {
            ppu: isPpu ? (Number(values.jkk_tunjangan) || 0) + (Number(values.jkm_tunjangan) || 0) : 0,
            bpu: (Number(values.bpu_jkk_tunjangan) || 0) + (Number(values.bpu_jkm) || 0)
                + (Number(values.noncrew_jkk_tunjangan) || 0) + (Number(values.noncrew_jkm_tunjangan) || 0),
            jkn: Number(values.kis_tunjangan) || 0,
        };
    }

    function update(container, parseAmount, formatNumber) {
        const totals = { ppu: 0, bpu: 0, jkn: 0 };
        container.querySelectorAll('tr.detail-row').forEach(row => {
            const read = selector => parseAmount(row.querySelector(selector)?.value);
            let values = {
                jkk_tunjangan: read('.input-jkk'), jkm_tunjangan: read('.input-jkm'),
                bpu_jkk_tunjangan: read('.input-bpu-jkk'), bpu_jkm: read('.input-bpu-jkm'),
                noncrew_jkk_tunjangan: read('.input-noncrew-jkk'), noncrew_jkm_tunjangan: read('.input-noncrew-jkm'),
            };
            // The edit form displays aggregate amounts; use its saved breakdown for manual rows.
            if (!row.querySelector('.input-bpu-jkk') && row.querySelector('.select-tipe-jkn')?.value === 'manual') {
                const snapshot = row.querySelector('input[name$="[rincian_bpjs]"]');
                if (snapshot) {
                    try { values = JSON.parse(snapshot.value); } catch (_) { /* Keep displayed values. */ }
                }
            }
            const split = root.BpjsJknColumns.split(read('.input-kes'),
                row.querySelector('.tipe-karyawan-hidden-input')?.value, row.dataset.isSpecialCase);
            const amounts = calculate({ ...values, group_bp_jamsostek: row.dataset.groupJamsostek, kis_tunjangan: split.tunjangan });
            Object.entries(amounts).forEach(([key, amount]) => {
                const cell = row.querySelector(`[data-allowance="${key}"]`);
                if (cell) cell.textContent = 'Rp ' + formatNumber(amount);
                if (!row.classList.contains('hidden')) totals[key] += amount;
            });
        });
        Object.entries(totals).forEach(([key, amount]) => {
            const cell = document.getElementById('total-allowance-' + key);
            if (cell) cell.textContent = 'Rp ' + formatNumber(amount);
            document.querySelectorAll(`[data-allowance-total="${key}"]`).forEach(summary => {
                summary.textContent = 'Rp ' + formatNumber(amount);
            });
        });
    }

    root.BpjsAllowances = { calculate, update };
})(globalThis);
