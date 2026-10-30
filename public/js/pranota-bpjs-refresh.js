(function (root) {
    root.loadBpjsData = async function ({ url, fetchData = root.fetch.bind(root) }) {
        const response = await fetchData(url, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            cache: 'no-store'
        });
        if (!response.ok) throw new Error('Data terbaru gagal diambil. Silakan coba lagi.');
        const data = await response.json();
        if (!Array.isArray(data.karyawans) || !Array.isArray(data.rumusBpjs)) {
            throw new Error('Respons data tidak sesuai. Silakan muat ulang setelah menyimpan pekerjaan Anda.');
        }
        return data;
    };
    root.refreshBpjsData = async function ({ url, rows, applyData, addEmployee, fetchData = root.fetch.bind(root) }) {
        const data = await root.loadBpjsData({ url, fetchData });
        applyData(data);
        let updated = 0, skipped = 0, removed = 0;
        const availableIds = new Set(data.karyawans.map(employee => employee.unique_id));
        await root.processBpjsBatch(rows, row => {
            if (row.dataset?.uniqueId && !availableIds.has(row.dataset.uniqueId)) {
                row.remove();
                removed++;
                return;
            }
            if (row.refreshBpjs?.()) updated++;
            else skipped++;
        });
        if (!addEmployee) return { updated, skipped, removed };
        const existingIds = new Set(rows.map(row => row.dataset?.uniqueId).filter(Boolean));
        let added = 0;
        await root.processBpjsBatch(data.karyawans, employee => {
            if (!employee.unique_id || existingIds.has(employee.unique_id)) return;
            if (!employee.group_jkn && !employee.group_bp_jamsostek && !employee.cabang_bpjs) return;
            addEmployee(employee);
            existingIds.add(employee.unique_id);
            added++;
        });
        return { updated, skipped, added, removed };
    };
})(globalThis);
