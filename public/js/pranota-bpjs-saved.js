(function (root) {
    const fields = ['jht_biaya', 'jht_hutang', 'jkk_tunjangan', 'jkm_tunjangan', 'jkk_hutang',
        'bpu_jkk_tunjangan', 'bpu_jkm', 'noncrew_jht_biaya', 'noncrew_jht_hutang',
        'noncrew_jkk_tunjangan', 'noncrew_jkm_tunjangan', 'jp_biaya', 'jp_hutang'];

    root.BpjsSavedDetail = {
        amounts(detail) {
            const values = Object.fromEntries(fields.map(key => [key, Number(detail.rincian_bpjs?.[key]) || 0]));
            if (!detail.rincian_bpjs) {
                // Legacy details contain aggregate values; assign them to their saved BPJS group.
                const group = String(detail.snapshot_bpjs?.karyawan?.group_bp_jamsostek || '').toUpperCase().replace(/[\s_-]+/g, '');
                const bpu = group.startsWith('BPU') || ['CILANDAKHL'].includes(group);
                const crew = group === 'BPUCREW';
                const mapping = bpu ? (crew
                    ? {jht_hutang: 'jkk_hutang', jkk_tunjangan: 'bpu_jkk_tunjangan', jkm_tunjangan: 'bpu_jkm', jht_biaya: 'noncrew_jht_biaya'}
                    : {jht_biaya: 'noncrew_jht_biaya', jht_hutang: 'noncrew_jht_hutang', jkk_tunjangan: 'noncrew_jkk_tunjangan', jkm_tunjangan: 'noncrew_jkm_tunjangan'}) : {};
                ['jht_biaya', 'jht_hutang', 'jkk_tunjangan', 'jkm_tunjangan', 'jp_biaya', 'jp_hutang'].forEach(key => {
                    values[mapping[key] || key] = Number(detail[key]) || 0;
                });
            }
            values.bpjs_kesehatan = Number(detail.bpjs_kesehatan) || 0;
            values.bpjs_ketenagakerjaan = Number(detail.bpjs_ketenagakerjaan) || 0;
            return values;
        }
    };
})(globalThis);
