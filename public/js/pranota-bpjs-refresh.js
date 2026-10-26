(function (root) {
    root.refreshBpjsData = async function ({ url, rows, applyData, fetchData = root.fetch.bind(root) }) {
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
        applyData(data);
        let updated = 0, skipped = 0;
        rows.forEach(row => {
            if (row.refreshBpjs?.()) updated++;
            else skipped++;
        });
        return { updated, skipped };
    };
})(globalThis);
