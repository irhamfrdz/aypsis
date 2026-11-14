document.addEventListener('DOMContentLoaded', function () {
    const boxes = Array.from(document.querySelectorAll('input[name="group_keys[]"]'));
    const employees = Array.from(document.querySelectorAll('input[name="employee_keys[]"]'));
    const selectAll = document.getElementById('select-all-employees');
    const selectAllTypes = document.getElementById('select-all-types');
    const button = document.getElementById('save-payment');
    const output = document.getElementById('payment-total');
    const credit = document.getElementById('bank_coa_id');
    const message = document.getElementById('credit-account-message');
    const groupSelect = document.getElementById('payment-group');
    const bankSearch = document.getElementById('bank-search');
    const bankSelect = document.getElementById('akun_coa_id');
    const details = document.getElementById('payment-details');
    const branchFilter = document.getElementById('employee-branch-filter');
    const nameSearch = document.getElementById('employee-name-search');
    const normalize = value => String(value || '').trim().replace(/\s+/g, ' ').toLocaleUpperCase('id-ID');
    if (bankSearch && bankSelect) {
        const bankOptions = Array.from(bankSelect.options).slice(1);
        bankSearch.addEventListener('input', function () {
            const query = normalize(bankSearch.value);
            let visibleCount = 0;
            bankOptions.forEach(option => {
                const matches = normalize(option.dataset.search || option.textContent).includes(query);
                option.hidden = !matches;
                if (matches) visibleCount++;
            });
            document.getElementById('bank-search-empty').classList.toggle('hidden', visibleCount > 0);
            if (bankSelect.selectedOptions[0]?.hidden) bankSelect.value = '';
        });
        bankSelect.addEventListener('change', function () {
            bankSearch.value = '';
            bankOptions.forEach(option => { option.hidden = false; });
            document.getElementById('bank-search-empty').classList.add('hidden');
        });
    }
    function updateTotal() {
        const availableTypes = boxes.filter(box => !box.disabled);
        const checkedTypes = availableTypes.filter(box => box.checked);
        selectAllTypes.disabled = availableTypes.length === 0;
        selectAllTypes.checked = availableTypes.length > 0 && checkedTypes.length === availableTypes.length;
        selectAllTypes.indeterminate = checkedTypes.length > 0 && checkedTypes.length < availableTypes.length;
        const selectedTypes = boxes.filter(box => box.checked);
        const selectedKeys = new Set(selectedTypes.map(box => box.value));
        employees.forEach(box => {
            box.disabled = !selectedKeys.has(box.dataset.groupKey);
            if (box.disabled) box.checked = false;
        });
        const available = employees.filter(box => !box.disabled);
        const previousBranch = branchFilter.value;
        const branches = new Map();
        available.forEach(box => {
            const branch = box.closest('tr').dataset.employeeBranch;
            branches.set(normalize(branch), branch);
        });
        branchFilter.replaceChildren(new Option('Semua Cabang BPJS', ''));
        [...branches.entries()].sort(([a], [b]) => a.localeCompare(b, 'id')).forEach(([key, label]) => {
            branchFilter.add(new Option(label, key));
        });
        branchFilter.value = branches.has(previousBranch) ? previousBranch : '';
        const query = normalize(nameSearch.value);
        employees.forEach(box => {
            const row = box.closest('tr');
            row.hidden = box.disabled || !normalize(row.dataset.employeeName).includes(query)
                || (branchFilter.value !== '' && normalize(row.dataset.employeeBranch) !== branchFilter.value);
        });
        const visible = available.filter(box => !box.closest('tr').hidden);
        const visibleSelected = visible.filter(box => box.checked);
        const selected = available.filter(box => box.checked);
        selectAll.disabled = visible.length === 0;
        selectAll.checked = visible.length > 0 && visibleSelected.length === visible.length;
        selectAll.indeterminate = visibleSelected.length > 0 && visibleSelected.length < visible.length;
        const hiddenSelected = selected.length - visibleSelected.length;
        document.getElementById('employee-filter-summary').textContent = visible.length + ' dari ' + available.length
            + ' karyawan ditampilkan · ' + selected.length + ' dipilih'
            + (hiddenSelected ? ' (' + hiddenSelected + ' di luar filter, tetap masuk total pembayaran)' : '');
        document.getElementById('employee-filter-empty').hidden = available.length === 0 || visible.length > 0;
        const empty = document.getElementById('payment-empty-selection');
        if (empty) empty.hidden = selectedTypes.length > 0;
        const cents = selected.reduce((sum, box) => sum + Math.round(Number(box.dataset.amount) * 100), 0);
        output.textContent = 'Rp ' + (cents / 100).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        let journalGrandTotalCents = 0;
        ['tunjangan', 'biaya', 'hutang'].forEach(field => {
            const totalCents = selected.reduce((sum, box) => sum + Math.round(Number(box.dataset[field] || 0) * 100), 0);
            journalGrandTotalCents += totalCents;
            document.getElementById('selected-' + field).textContent = 'Rp ' + (totalCents / 100).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        });
        document.getElementById('selected-journal-grand-total').textContent = 'Rp ' + (journalGrandTotalCents / 100).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const missing = selected.some(box => !box.dataset.creditId);
        const accounts = new Map();
        selected.forEach(box => {
            if (!box.dataset.creditId) return;
            const account = accounts.get(box.dataset.creditId) || {label: box.dataset.creditLabel, cents: 0};
            account.cents += Math.round(Number(box.dataset.amount) * 100);
            accounts.set(box.dataset.creditId, account);
        });
        credit.replaceChildren();
        accounts.forEach(account => {
            const row = document.createElement('div');
            row.className = 'rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm';
            const name = document.createElement('p');
            name.textContent = account.label;
            const amount = document.createElement('p');
            amount.className = 'font-semibold mt-1';
            amount.textContent = 'Rp ' + (account.cents / 100).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            row.append(name, amount);
            credit.append(row);
        });
        message.textContent = !selected.length ? 'Centang karyawan yang akan dibayar untuk menampilkan akun jurnal.'
            : missing ? 'Akun jurnal untuk tipe yang dipilih belum tersedia atau belum sesuai di COA.'
            : 'Akun otomatis sesuai tipe yang dipilih. Pembayaran dicatat per akun dengan satu bank yang digunakan.';
        message.classList.toggle('text-red-600', missing);
        button.disabled = selected.length === 0 || missing;
    }
    boxes.forEach(box => box.addEventListener('change', updateTotal));
    selectAllTypes.addEventListener('change', () => {
        boxes.filter(box => !box.disabled).forEach(box => { box.checked = selectAllTypes.checked; });
        updateTotal();
    });
    employees.forEach(box => box.addEventListener('change', updateTotal));
    branchFilter.addEventListener('change', updateTotal);
    nameSearch.addEventListener('input', updateTotal);
    selectAll.addEventListener('change', () => {
        employees.filter(box => !box.disabled && !box.closest('tr').hidden).forEach(box => { box.checked = selectAll.checked; });
        updateTotal();
    });
    if (groupSelect) {
        const restored = boxes.find(box => box.checked);
        if (restored) groupSelect.value = restored.dataset.selectionKey;
        const changeGroup = (restore = false) => {
            if (!restore) {
                branchFilter.value = '';
                nameSearch.value = '';
            }
            const key = groupSelect.value;
            details.hidden = !key;
            document.querySelectorAll('[data-payment-group]').forEach(row => {
                row.hidden = row.dataset.paymentGroup !== key;
            });
            boxes.forEach(box => {
                const matches = box.dataset.selectionKey === key;
                box.disabled = !matches;
                box.checked = matches && restore && box.checked;
            });
            updateTotal();
        };
        groupSelect.addEventListener('change', () => changeGroup());
        changeGroup(true);
    }
    updateTotal();
    button.form.addEventListener('submit', function () {
        button.disabled = true;
        button.textContent = 'Menyimpan...';
    });
});
