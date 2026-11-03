document.addEventListener('DOMContentLoaded', function () {
    const boxes = Array.from(document.querySelectorAll('input[name="group_keys[]"]'));
    const button = document.getElementById('save-payment');
    const output = document.getElementById('payment-total');
    const credit = document.getElementById('bank_coa_id');
    const message = document.getElementById('credit-account-message');
    function updateTotal() {
        const selected = boxes.filter(box => box.checked);
        const cents = selected.reduce((sum, box) => sum + Math.round(Number(box.dataset.amount) * 100), 0);
        output.textContent = 'Rp ' + (cents / 100).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const ids = new Set(selected.map(box => box.dataset.creditId));
        const missing = selected.some(box => !box.dataset.creditId);
        const mixed = ids.size > 1;
        credit.value = selected.length && !missing && !mixed ? selected[0].dataset.creditLabel : '';
        message.textContent = !selected.length ? 'Pilih tipe JKN atau Jamsostek untuk menampilkan akun jurnal.'
            : missing ? 'Akun jurnal untuk tipe yang dipilih belum tersedia atau belum sesuai di COA.'
            : mixed ? 'Tipe yang dipilih memiliki akun jurnal berbeda. Simpan pembayarannya secara terpisah.'
            : 'Akun otomatis sesuai jurnal pranota BPJS.';
        message.classList.toggle('text-red-600', missing || mixed);
        button.disabled = selected.length === 0 || missing || mixed;
    }
    boxes.forEach(box => box.addEventListener('change', updateTotal));
    updateTotal();
    button.form.addEventListener('submit', function () {
        button.disabled = true;
        button.textContent = 'Menyimpan...';
    });
});
