(function (root) {
    function split(amount, employeeType) {
        const temporary = String(employeeType || '').split('\\').pop() === 'KaryawanTidakTetap';
        return { tunjangan: temporary ? 0 : amount, biaya: temporary ? amount : 0 };
    }

    function syncRow(row) {
        const input = row.querySelector('.input-kes');
        const tunjangan = row.querySelector('[data-jkn-tunjangan]');
        const biaya = row.querySelector('[data-jkn-biaya]');
        if (!input || !tunjangan || !biaya) return;
        const type = row.querySelector('.tipe-karyawan-hidden-input')?.value || row.dataset.tipeKaryawan;
        const temporary = split(1, type).biaya === 1;
        const zero = row.querySelector('.jkn-zero') || document.createElement('span');
        zero.className = 'jkn-zero block text-right text-xs text-gray-400 px-2';
        zero.textContent = '0';
        const active = temporary ? biaya : tunjangan;
        const inactive = temporary ? tunjangan : biaya;
        if (input.parentElement !== active) active.replaceChildren(input);
        if (zero.parentElement !== inactive) inactive.replaceChildren(zero);
        input.setAttribute('aria-label', temporary ? 'KIS 4% (BIAY)' : 'KIS 4% (TUNJ)');
    }

    root.BpjsJknColumns = { split, syncRow };
})(globalThis);
