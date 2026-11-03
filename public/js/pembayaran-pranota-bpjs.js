document.addEventListener('DOMContentLoaded', function () {
    const boxes = Array.from(document.querySelectorAll('input[name="group_keys[]"]'));
    const button = document.getElementById('save-payment');
    const output = document.getElementById('payment-total');
    function updateTotal() {
        const selected = boxes.filter(box => box.checked);
        const cents = selected.reduce((sum, box) => sum + Math.round(Number(box.dataset.amount) * 100), 0);
        output.textContent = 'Rp ' + (cents / 100).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        button.disabled = selected.length === 0;
    }
    boxes.forEach(box => box.addEventListener('change', updateTotal));
    updateTotal();
    button.form.addEventListener('submit', function () {
        button.disabled = true;
        button.textContent = 'Menyimpan...';
    });
});
