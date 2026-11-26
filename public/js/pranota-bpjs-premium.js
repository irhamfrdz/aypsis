(function (root) {
    root.BpjsPremium = {
        total(employee, records, referenceDate) {
            const parse = root.BpjsJamsostek.parseAmount;
            const jkn = root.BpjsJknColumns.calculate(
                root.BpjsJknColumns.findFormula(records, employee), parse(employee.dpp_jkn), employee);
            let total = jkn.contribution + jkn.debt;
            const formula = root.BpjsJamsostek.findFormula(records, employee);
            if (!formula) return total;

            const dpp = parse(employee.dpp_bp_jamsostek);
            const percent = field => (parseFloat(formula[field]) || 0) / 100;
            const category = root.BpjsMasterFilters.category(formula);
            if (category === 'bpu_crew') {
                const amounts = root.BpjsJamsostek.calculateCrew(formula, employee.dpp_bp_jamsostek, employee);
                total += percent('hutang_persen') * dpp + amounts.jht + amounts.jkk + amounts.jkm;
            } else if (category === 'bpu_non_crew') {
                const amounts = root.BpjsJamsostek.calculateNonCrew(formula, employee.dpp_bp_jamsostek, employee);
                total += amounts.jhtBiaya + amounts.jhtHutang + amounts.jkk + amounts.jkm;
            } else {
                // Match the PPU component calculation used by the contribution table.
                total += (percent('jht_biaya') + percent('jkk_tunjangan') + percent('jkm_tunjangan')) * dpp;
                const birth = employee.tanggal_lahir ? new Date(employee.tanggal_lahir) : null;
                const date = referenceDate ? new Date(referenceDate) : new Date();
                let age = null;
                if (birth && !Number.isNaN(birth.getTime())) {
                    age = date.getFullYear() - birth.getFullYear();
                    if (date.getMonth() < birth.getMonth()
                        || (date.getMonth() === birth.getMonth() && date.getDate() < birth.getDate())) age--;
                }
                const maxAge = parseFloat(formula.jp_max_age) || 0;
                if (!(maxAge > 0 && age !== null && age >= maxAge)) {
                    const maxDpp = parseFloat(formula.jp_max_dpp) || 0;
                    const jpDpp = maxDpp > 0 ? Math.min(dpp, maxDpp) : dpp;
                    const debt = percent('jp_hutang') * jpDpp;
                    total += debt + Math.max(0, percent('jp_biaya') * jpDpp - debt);
                }
            }
            return total;
        }
    };
})(globalThis);
