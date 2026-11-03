(function (root) {
    root.prepareBpjsSubmission = function (form, parseAmount, formulas = []) {
        const fields = Array.from(form.querySelectorAll('[name^="details["]')).filter(field => !field.disabled);
        const details = {};
        fields.forEach(field => {
            const match = field.name.match(/^details\[([^\]]+)\]\[([^\]]+)\]$/);
            if (!match) return;
            const [, index, key] = match;
            details[index] ??= {};
            details[index][key] = key !== 'no_voucher' && field.matches('input[type="text"]') ? parseAmount(field.value) : field.value;
        });
        Object.entries(details).forEach(([index, detail]) => {
            const employeeField = fields.find(field => field.name === `details[${index}][karyawan_id]`);
            const row = employeeField?.closest?.('tr.detail-row');
            if (!row) return;
            detail.snapshot_karyawan = row.bpjsEmployeeSnapshot;
            detail.ringkasan_bpjs = {};
            row.querySelectorAll('[data-allowance]').forEach(cell => {
                const input = cell.querySelector('input');
                detail.ringkasan_bpjs[cell.dataset.allowance] = parseAmount(input?.value || '0');
            });
        });
        let formulaPayload = form.querySelector('[name="snapshot_rumus_json"]');
        if (!formulaPayload) {
            formulaPayload = document.createElement('input');
            formulaPayload.type = 'hidden';
            formulaPayload.name = 'snapshot_rumus_json';
            form.appendChild(formulaPayload);
        }
        formulaPayload.value = JSON.stringify(formulas);
        let payload = form.querySelector('[name="details_json"]');
        if (!payload) {
            payload = document.createElement('input');
            payload.type = 'hidden';
            payload.name = 'details_json';
            form.appendChild(payload);
        }
        payload.value = JSON.stringify(Object.values(details));
        // Submit one field instead of thousands of PHP input variables.
        fields.forEach(field => field.removeAttribute('name'));
    };
})(globalThis);
