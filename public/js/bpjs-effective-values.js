(function (root) {
    root.BpjsEffectiveValues = {
        date(month, year) {
            return `${year}-${String(month).padStart(2, '0')}-${new Date(Number(year), Number(month), 0).getDate()}`;
        },
        resolve(record, date) {
            if (!record) return record;
            const base = record.bpjs_base_values || Object.fromEntries(
                (record.bpjs_effective_values || []).flatMap(entry => Object.keys(entry.values))
                    .map(key => [key, record[key] ?? null]));
            const result = { ...record, ...base, bpjs_base_values: base, bpjs_active_fields: [] };
            (record.bpjs_effective_values || []).forEach(entry => {
                if (entry.valid_from <= date && date <= entry.valid_until) {
                    Object.assign(result, entry.values);
                    result.bpjs_active_fields.push(...Object.keys(entry.values));
                }
            });
            return result;
        }
    };
})(globalThis);
