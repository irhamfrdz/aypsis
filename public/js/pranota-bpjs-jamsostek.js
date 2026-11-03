(function (root) {
    const normalize = value => String(value || '').trim().replace(/\s+/g, ' ').toUpperCase();
    const compact = value => normalize(value).replace(/[\s_-]+/g, '');
    const parseAmount = value => {
        if (typeof value === 'number') return Number.isFinite(value) ? value : 0;
        const text = String(value || '').trim();
        if (/^-?\d+\.\d{1,2}$/.test(text)) return Number(text);
        return Number(text.replace(/\./g, '').replace(',', '.')) || 0;
    };

    const isDiscountExcluded = (record, employee) => {
        if (!employee) return false;
        const key = employee.unique_id || (employee.id ? (String(employee.tipe_karyawan || '').endsWith('KaryawanTidakTetap')
            ? 'KaryawanTidakTetap_' : 'Karyawan_') + employee.id : '');
        let excluded = record.diskon_pengecualian || [];
        if (typeof excluded === 'string') {
            try { excluded = JSON.parse(excluded); } catch (_) { excluded = []; }
        }
        return Array.isArray(excluded) && excluded.includes(key);
    };
    const applyDiscount = (record, amount, employee) => {
        const value = parseFloat(record.diskon_nilai || 0);
        if (record.diskon_status !== 'ada' || value <= 0 || isDiscountExcluded(record, employee)) return amount;
        return amount * ((record.diskon_tipe || 'persen').toLowerCase() === 'persen' ? value / 100 : value);
    };
    root.BpjsJamsostek = {
        isDiscountExcluded,
        applyDiscount,
        parseAmount,
        findDppTier(tiers, dppValue) {
            if (typeof tiers === 'string') {
                try { tiers = JSON.parse(tiers); } catch (_) { return null; }
            }
            if (!Array.isArray(tiers)) return null;
            const dpp = parseAmount(dppValue);
            // Non-crew DPP entries are lower bounds; use the greatest bound not exceeding the employee DPP.
            return tiers.filter(tier => parseAmount(tier.dpp) <= dpp)
                .sort((a, b) => parseAmount(b.dpp) - parseAmount(a.dpp))[0] || null;
        },
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
        calculateNonCrew(record, dppValue, employee = null) {
            const dpp = parseAmount(dppValue);
            const tier = this.findDppTier(record.hutang_tiers, dpp);
            const jhtHutang = tier ? parseAmount(tier.potongan) : parseFloat(record.jht_hutang || record.hutang_persen || 0) / 100 * dpp;
            return {
                jhtHutang,
                jhtBiaya: Math.max(0, parseFloat(record.jht_biaya || 0) / 100 * dpp - jhtHutang),
                jkk: applyDiscount(record, parseFloat(record.jkk_tunjangan || record.tunjangan_persen || 0) / 100 * dpp, employee),
                jkm: applyDiscount(record, parseFloat(record.jkm_tunjangan || record.biaya_persen || 0), employee),
            };
        },
        calculateCrew(record, dppValue, employee = null) {
            const dpp = parseAmount(dppValue);
            let tiers = record.hutang_tiers;
            if (typeof tiers === 'string') {
                try { tiers = JSON.parse(tiers); } catch (_) { tiers = []; }
            }
            const tier = Array.isArray(tiers) ? tiers.find(item => parseAmount(item.dpp) === dpp) : null;
            const jht = tier ? parseAmount(tier.potongan) : 0;
            const discount = amount => applyDiscount(record, amount, employee);
            return {
                jht,
                jkk: Math.max(0, discount(parseFloat(record.tunjangan_persen || 0) / 100 * dpp) - jht),
                jkm: discount(parseFloat(record.biaya_persen || 0))
            };
        }
    };
})(globalThis);
