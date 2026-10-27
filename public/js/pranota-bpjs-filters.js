(function (root) {
    const normalize = value => String(value || '').trim().replace(/\s+/g, ' ').toUpperCase();
    const isAll = value => !value || value === 'all';
    const category = record => {
        if (record.jenis === 'jkn') return 'jkn';
        const group = normalize(record.group_name);
        const tipe = normalize(record.tipe);
        if (group.includes('PPU')) return 'ppu';
        return ['CREW', 'BPU-CREW'].includes(tipe) || (!tipe && group.includes('BPU-CREW'))
            ? 'bpu_crew' : 'bpu_non_crew';
    };
    const selectedRecords = (records, group, tipe) => records.filter(record =>
        (isAll(group) || normalize(record.group_name) === normalize(group))
        && (isAll(tipe) || normalize(record.tipe) === normalize(tipe)));

    root.BpjsMasterFilters = {
        category,
        options(records, column) {
            const values = new Map();
            records.forEach(record => {
                const value = String(record[column] || '').trim();
                if (value && !values.has(normalize(value))) values.set(normalize(value), value);
            });
            return Array.from(values.values()).sort((a, b) => a.localeCompare(b, 'id', { sensitivity: 'base' }));
        },
        categories(records, group = 'all', tipe = 'all') {
            if (isAll(group) && isAll(tipe)) return new Set(['jkn', 'ppu', 'bpu_crew', 'bpu_non_crew']);
            return new Set(selectedRecords(records, group, tipe).map(category));
        },
        matches(employee, records, group = 'all', tipe = 'all') {
            if (isAll(group) && isAll(tipe)) return true;
            return ['jkn', 'jamsostek'].some(jenis => {
                const employeeGroup = normalize(jenis === 'jkn' ? employee.group_jkn : employee.group_bp_jamsostek);
                const employeeType = normalize(jenis === 'jkn' ? employee.tipe_jkn : employee.tipe_bp_jamsostek);
                if (!employeeGroup || (!isAll(group) && employeeGroup !== normalize(group))) return false;
                const master = records.filter(record => record.jenis === jenis && normalize(record.group_name) === employeeGroup);
                if (isAll(tipe)) return master.length > 0;
                // Old employees without a type can only be inferred when the master has one unique type.
                const masterTypes = new Set(master.map(record => normalize(record.tipe)).filter(Boolean));
                const resolvedType = employeeType || (masterTypes.size === 1 ? [...masterTypes][0] : '');
                return resolvedType === normalize(tipe) && master.some(record => normalize(record.tipe) === resolvedType);
            });
        },
        total(records, group, tipe, amounts) {
            return [...this.categories(records, group, tipe)].reduce((sum, key) => sum + (amounts[key] || 0), 0);
        }
    };
})(globalThis);
