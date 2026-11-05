document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('pranota-gaji-form');
    const dialog = document.getElementById('pph-dialog');
    const fields = document.getElementById('pph-fields');
    const amounts = new Map(Object.entries(window.pranotaGajiOldPph || {}));
    const money = value => new Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR'}).format(value);
    let confirmed = false;
    let saving = false;
    let totalCents = 0;
    const updateTotal = () => {
        const deduction = [...fields.querySelectorAll('input')].reduce((sum, input) => sum + Math.round(Number(input.value || 0) * 100), 0);
        document.getElementById('pph-net-total').textContent = 'Total setelah PPh: ' + money((totalCents - deduction) / 100);
    };
    form.addEventListener('submit', event => {
        if (saving) { event.preventDefault(); return; }
        if (confirmed) {
            saving = true;
            form.querySelector('button[type="submit"]').disabled = true;
            return;
        }
        event.preventDefault();
        const selected = [...form.querySelectorAll('[data-gaji-checkbox]:checked')];
        document.getElementById('gaji-selection-error').classList.toggle('hidden', selected.length > 0);
        if (!selected.length) return;
        const employees = new Map();
        selected.forEach(box => {
            const id = box.dataset.employeeId;
            const employee = employees.get(id) || {name: box.dataset.employeeName, cents: 0, count: 0};
            employee.cents += Math.round(Number(box.dataset.total) * 100);
            employee.count++;
            employees.set(id, employee);
        });
        fields.replaceChildren();
        totalCents = 0;
        employees.forEach((employee, id) => {
            totalCents += employee.cents;
            const label = document.createElement('label');
            label.className = 'block rounded-lg border p-3';
            const title = document.createElement('span');
            title.className = 'block mb-2 text-sm font-medium';
            title.textContent = employee.name + ' · ' + employee.count + ' gaji · ' + money(employee.cents / 100);
            const input = document.createElement('input');
            input.type = 'number';
            input.name = 'pph[' + id + ']';
            input.min = '0';
            input.max = String(Math.max(0, employee.cents) / 100);
            input.step = '0.01';
            input.required = true;
            input.value = amounts.get(id) ?? '0';
            input.className = 'w-full rounded-lg border-gray-300 text-sm';
            input.setAttribute('aria-label', 'Nominal PPh ' + employee.name);
            input.addEventListener('input', () => { amounts.set(id, input.value); updateTotal(); });
            label.append(title, input);
            fields.append(label);
        });
        updateTotal();
        dialog.showModal();
    });
    // Disabled inputs do not interfere with main form validation when returning to selection.
    dialog.addEventListener('close', () => {
        if (!confirmed) fields.querySelectorAll('input').forEach(input => { input.disabled = true; });
    });
    document.getElementById('pph-cancel').addEventListener('click', () => dialog.close());
    document.getElementById('pph-confirm').addEventListener('click', () => {
        if (!form.reportValidity()) return;
        confirmed = true;
        dialog.close();
        form.requestSubmit();
    });
});
