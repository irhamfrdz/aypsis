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

    function effective(amounts, overrides = {}) {
        return Object.fromEntries(Object.entries(amounts).map(([key, value]) =>
            [key, Object.hasOwn(overrides, key) ? Number(overrides[key]) : value]));
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
            row.allowanceOverrides ??= {};
            const automaticAmounts = calculate({ ...values, group_bp_jamsostek: row.dataset.groupJamsostek, kis_tunjangan: split.tunjangan });
            const amounts = effective(automaticAmounts, row.allowanceOverrides);
            Object.entries(amounts).forEach(([key, amount]) => {
                const cell = row.querySelector(`[data-allowance="${key}"]`);
                if (cell) {
                    let input = cell.querySelector('input');
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'text';
                        input.inputMode = 'decimal';
                        input.className = 'bpjs-allowance-input';
                        input.setAttribute('aria-label', { ppu: 'Tunjangan PPU', bpu: 'Tunjangan BPU', jkn: 'Tunjangan JKN' }[key]);
                        const reset = document.createElement('button');
                        reset.type = 'button';
                        reset.className = 'bpjs-allowance-reset';
                        reset.textContent = '↺ Auto';
                        reset.title = 'Kembalikan nominal sesuai rumus';
                        const editor = document.createElement('div');
                        editor.className = 'bpjs-allowance-editor';
                        const money = document.createElement('div');
                        money.className = 'bpjs-allowance-money';
                        const currency = document.createElement('span');
                        currency.className = 'bpjs-allowance-currency';
                        currency.textContent = 'Rp';
                        currency.setAttribute('aria-hidden', 'true');
                        money.append(currency, input);
                        const meta = document.createElement('div');
                        meta.className = 'bpjs-allowance-meta';
                        const status = document.createElement('span');
                        status.className = 'bpjs-allowance-status';
                        meta.append(status, reset);
                        editor.append(money, meta);
                        cell.replaceChildren(editor);
                        input.addEventListener('input', () => {
                            const value = input.value.trim();
                            if (value === '') delete row.allowanceOverrides[key];
                            else row.allowanceOverrides[key] = parseAmount(value);
                            input.setCustomValidity(parseAmount(value) < 0 ? 'Nominal tidak boleh negatif.' : '');
                            update(container, parseAmount, formatNumber);
                        });
                        input.addEventListener('change', () => {
                            update(container, parseAmount, formatNumber);
                            input.value = formatNumber(row.allowanceOverrides[key] ?? Number(input.dataset.automaticAmount));
                        });
                        reset.addEventListener('click', () => {
                            delete row.allowanceOverrides[key];
                            input.setCustomValidity('');
                            update(container, parseAmount, formatNumber);
                        });
                    }
                    input.dataset.automaticAmount = automaticAmounts[key];
                    const index = row.querySelector('.karyawan-hidden-input')?.name.match(/^details\[([^\]]+)\]/)?.[1];
                    if (Object.hasOwn(row.allowanceOverrides, key) && index) input.name = `details[${index}][allowance_${key}]`;
                    else input.removeAttribute('name');
                    if (document.activeElement !== input) input.value = formatNumber(amount);
                    input.title = Object.hasOwn(row.allowanceOverrides, key) ? 'Nominal manual' : 'Nominal otomatis sesuai rumus';
                    const manual = Object.hasOwn(row.allowanceOverrides, key);
                    cell.querySelector('.bpjs-allowance-editor').dataset.manual = String(manual);
                    cell.querySelector('.bpjs-allowance-status').textContent = manual ? 'Diubah manual' : 'Sesuai rumus';
                    cell.querySelector('.bpjs-allowance-reset').disabled = !manual;
                }
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

    root.BpjsAllowances = { calculate, effective, update };
})(globalThis);
