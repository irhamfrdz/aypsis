(function (root) {
    function split(amount, employeeType, isSpecialCase = false) {
        const special = isSpecialCase === true || isSpecialCase === 1 || isSpecialCase === '1';
        const biaya = String(employeeType || '').split('\\').pop() === 'KaryawanTidakTetap' && !special;
        return { tunjangan: biaya ? 0 : amount, biaya: biaya ? amount : 0 };
    }

    function syncRow(row) {
        const input = row.querySelector('.input-kes');
        const tunjangan = row.querySelector('[data-jkn-tunjangan]');
        const biaya = row.querySelector('[data-jkn-biaya]');
        if (!input || !tunjangan || !biaya) return;
        const type = row.querySelector('.tipe-karyawan-hidden-input')?.value || row.dataset.tipeKaryawan;
        const useBiaya = split(1, type, row.dataset.isSpecialCase).biaya === 1;
        const zero = row.querySelector('.jkn-zero') || document.createElement('span');
        zero.className = 'jkn-zero block text-right text-xs text-gray-400 px-2';
        zero.textContent = '0';
        const active = useBiaya ? biaya : tunjangan;
        const inactive = useBiaya ? tunjangan : biaya;
        if (input.parentElement !== active) active.replaceChildren(input);
        if (zero.parentElement !== inactive) inactive.replaceChildren(zero);
        input.setAttribute('aria-label', useBiaya ? 'KIS 4% (BIAY)' : 'KIS 4% (TUNJ)');
    }

    root.BpjsJknColumns = { split, syncRow };
})(globalThis);
