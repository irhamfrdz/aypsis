(function (root) {
    const normalize = value => String(value || '').trim().replace(/\s+/g, ' ').toUpperCase();
    const compact = value => normalize(value).replace(/[\s_-]+/g, '');
    const parseAmount = value => {
        if (typeof value === 'number') return Number.isFinite(value) ? value : 0;
        const text = String(value || '').trim();
        if (/^-?\d+\.\d{1,2}$/.test(text)) return Number(text);
        return Number(text.replace(/\./g, '').replace(',', '.')) || 0;
    };

    root.BpjsJamsostek = {
        parseAmount,
        findFormula(records, employee) {
            const group = compact(employee.group_bp_jamsostek);
            const type = normalize(employee.tipe_bp_jamsostek);
            const branch = normalize(employee.cabang_bpjs);
            if (!group) return null;
            let candidates = records.filter(record => record.jenis === 'jamsostek' && compact(record.group_name) === group);
            if (type) {
                const typed = candidates.filter(record => normalize(record.tipe) === type);
                candidates = typed.length ? typed : candidates.filter(record => !normalize(record.tipe)
                    && root.BpjsMasterFilters.category(record) === (['CREW', 'BPU-CREW'].includes(type) ? 'bpu_crew'
                        : (group.includes('PPU') ? 'ppu' : 'bpu_non_crew')));
            } else if (new Set(candidates.map(record => normalize(record.tipe))).size > 1) {
                return null;
            }
            if (branch) {
                const exactBranch = candidates.filter(record => normalize(record.cabang_bpjs) === branch);
                if (exactBranch.length) return exactBranch.length === 1 ? exactBranch[0] : null;
            }
            const general = candidates.filter(record => !normalize(record.cabang_bpjs));
            if (general.length) return general.length === 1 ? general[0] : null;
            return !branch && candidates.length === 1 ? candidates[0] : null;
        },
        calculateCrew(record, dppValue) {
            const dpp = parseAmount(dppValue);
            let tiers = record.hutang_tiers;
            if (typeof tiers === 'string') {
                try { tiers = JSON.parse(tiers); } catch (_) { tiers = []; }
            }
            const tier = Array.isArray(tiers) ? tiers.find(item => parseAmount(item.dpp) === dpp) : null;
            const jht = tier ? parseAmount(tier.potongan) : 0;
            const discount = amount => {
                const value = parseFloat(record.diskon_nilai || 0);
                if (record.diskon_status !== 'ada' || value <= 0) return amount;
                return amount * ((record.diskon_tipe || 'persen').toLowerCase() === 'persen' ? value / 100 : value);
            };
            return {
                jht,
                jkk: Math.max(0, discount(parseFloat(record.tunjangan_persen || 0) / 100 * dpp) - jht),
                jkm: discount(parseFloat(record.biaya_persen || 0))
            };
        }
    };
})(globalThis);
