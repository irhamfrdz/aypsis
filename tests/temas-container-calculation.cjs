// Run: node tests/temas-container-calculation.cjs
const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');

// Minimal DOM fixtures for the actual Blade calculation function.
const input = (value = '') => ({
    value, checked: false, textContent: '',
    classList: { add() {}, remove() {} },
    hasAttribute(key) { return Boolean(this[key]); },
    setCustomValidity(message) { this.validationMessage = message; },
});
const group = (fields, lists = {}) => ({
    querySelector: selector => fields[selector],
    querySelectorAll: selector => lists[selector] || [],
});
function cost(price, type) {
    const fields = {};
    for (const key of ['price-input-temas', 'quantity-input-temas', 'temas-row-number', 'temas-row-nomor-bl', 'temas-row-bl', 'temas-row-size', 'type-select-temas', 'type-manual-input-temas', 'temas-cost-calculation']) {
        fields['.' + key] = input();
    }
    fields['.price-input-temas'].value = price;
    fields['.quantity-input-temas'].value = 1;
    fields['.type-select-temas'].value = type;
    return group(fields);
}
const first = cost(100000, '1');
const manual = cost(50000, 'MANUAL');
manual.querySelector('.type-manual-input-temas').value = 'Materai';
const second = cost(200000, '1');
const card = (number, rows, blId) => Object.assign(group({
    '.temas-bl-select': input(number),
    '.temas-container-size': input('20ft'),
    '.temas-container-title': input(),
    '.temas-container-total': input(),
}, { '.temas-type-item': rows }), { dataset: { blId, nomorBl: number, containerNumbers: number === 'bl1' ? 'TEMU1, TEMU2' : 'TEMU3' } });
const cards = [card('bl1', [first, manual], '9'), card('BL2', [second], '10')];
const fields = { '.temas-count': input() };
for (const key of ['kapal-select', 'sub-total-display', 'sub-total-value', 'pph-active', 'pph-display', 'pph-value', 'ppn-active', 'ppn-display', 'ppn-value', 'materai-value', 'admin-value', 'adjustment-value', 'grand-total-display', 'grand-total-value']) {
    fields['.' + key + '-temas'] = input(0);
}
fields['.pph-active-temas'].checked = true;
const section = group(fields, { '.temas-container-card': cards, '.temas-type-item': [first, manual, second] });
const context = {
    window: {}, addTemasSectionBtn: null, nominalInput: input(),
    pricelistTemasData: [{ id: 1, size: '20ft', jenis_biaya: 'THC' }],
    document: { querySelector: () => section, querySelectorAll: () => [section] },
};
vm.createContext(context);
vm.runInContext(fs.readFileSync('resources/views/biaya-kapal/create/_js-temas.blade.php', 'utf8')
    .replace("@include('biaya-kapal.create._js-temas-payments')", fs.readFileSync('resources/views/biaya-kapal/create/_js-temas-payments.blade.php', 'utf8'))
    .replace(/\{\{[\s\S]*?\}\}/g, 'blade'), context);
context.calculateTemasSectionTotal(1);
assert.equal(fields['.sub-total-value-temas'].value, 450000);
assert.equal(fields['.grand-total-value-temas'].value, 441000);
assert.equal(first.querySelector('.quantity-input-temas').value, 2);
assert.equal(manual.querySelector('.quantity-input-temas').value, 1);
assert.equal(first.querySelector('.temas-cost-calculation').textContent, 'Tarif × 2 kontainer');
assert.equal(manual.querySelector('.temas-cost-calculation').textContent, 'Dihitung 1× per BL');
assert.equal(manual.querySelector('.temas-row-number').value, 'TEMU1, TEMU2');
assert.equal(manual.querySelector('.temas-row-nomor-bl').value, 'BL1');
assert.equal(second.querySelector('.temas-row-bl').value, '10');
assert.equal(fields['.ppn-display-temas'].readOnly, true);
fields['.ppn-active-temas'].checked = true;
fields['.adjustment-value-temas'].value = -10000;
context.calculateTemasSectionTotal(1);
assert.equal(fields['.grand-total-value-temas'].value, 480500);
assert.equal(fields['.ppn-display-temas'].readOnly, false);
cards[1].dataset.nomorBl = 'BL1';
context.calculateTemasSectionTotal(1);
assert.ok(cards[1].querySelector('.temas-bl-select').validationMessage);
cards[1].querySelector('.temas-container-size').value = '40ft';
context.calculateTemasSectionTotal(1);
assert.ok(second.querySelector('.type-select-temas').validationMessage);
cards[1].dataset.nomorBl = 'BL2';
cards[1].querySelector('.temas-container-size').value = '20ft';
context.calculateTemasSectionTotal(1);
assert.equal(second.querySelector('.type-select-temas').validationMessage, '');
assert.equal(cards[1].querySelector('.temas-bl-select').validationMessage, '');
console.log('PASS: per-container multiplication, BL-only exceptions, totals, taxes, adjustment and validation');
