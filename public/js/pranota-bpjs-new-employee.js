(function (root) {
    root.BpjsNewEmployee = {
        matches(employee, month, year) {
            const date = String(employee?.created_at || '').match(/^(\d{4})-(\d{2})-\d{2}/);
            return !!date && Number(date[1]) === Number(year) && Number(date[2]) === Number(month);
        },
        badge() {
            const badge = document.createElement('span');
            badge.className = 'bpjs-new-badge';
            badge.textContent = 'NEW';
            badge.title = 'Data karyawan baru dibuat pada periode pranota ini';
            return badge;
        }
    };
})(globalThis);
