// Run: node tests/temas-container-calculation.cjs
const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');

// Minimal DOM fixtures for the actual Blade calculation function.
const input = (value = '') => ({
    value, checked: false, textContent: '',
    classList: { add() {}, remove() {}, toggle() {} },
    hasAttribute(key) { return Boolean(this[key]); },
    setCustomValidity(message) { this.validationMessage = message; },
});
const group = (fields, lists = {}) => ({
    querySelector: selector => fields[selector],
    querySelectorAll: selector => lists[selector] || [],
});
function cost(price, type) {
    const fields = {};
    for (const key of ['price-input-temas', 'quantity-input-temas', 'temas-row-number', 'temas-row-nomor-bl', 'temas-row-bl', 'temas-row-size', 'type-select-temas', 'type-manual-input-temas', 'temas-cost-calculation', 'temas-row-per-container', 'temas-per-container']) {
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
first.querySelector('.temas-per-container').checked = true;
manual.querySelector('.temas-per-container').checked = false;
second.querySelector('.temas-per-container').checked = true;
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
for (const key of ['temas-payment-mode', 'temas-settlement-breakdown', 'temas-settlement-total', 'temas-settlement-dp', 'temas-settlement-balance', 'temas-dp-reference', 'temas-cash-value', 'temas-cash-display']) fields['.' + key] = input();
fields['.temas-payment-mode'].value = 'lunas';
fields['.pph-active-temas'].checked = true;
const section = group(fields, { '.temas-container-card': cards, '.temas-type-item': [first, manual, second] });
section.dataset = {};
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
assert.equal(first.querySelector('.temas-row-per-container').value, '1');
assert.equal(manual.querySelector('.temas-row-per-container').value, '0');
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

const dpSelect = fields['.temas-dp-reference'];
const option = (value, amount, voyage = 'V001') => ({value, dataset: {amount, kapal: 'TEMAS 1', voyage}});
const dp1 = option('1', '100000.25');
const dp2 = option('2', '50000.50');
const other = option('3', '10000', 'V002');
dpSelect.options = [dp1, dp2, other];
dpSelect.selectedOptions = [dp1, dp2];
context.updateTemasDpSelection(section);
assert.equal(section.dataset.dpAmount, 150000.75);
assert.equal(other.disabled, false);
fields['.temas-payment-mode'].value = 'pelunasan_dp';
context.calculateTemasSectionTotal(1);
assert.equal(fields['.temas-cash-value'].value, 299999.25);
assert.equal(fields['.grand-total-value-temas'].value, 450000);
assert.equal(dpSelect.validationMessage, '');
dp2.dataset.amount = '500000';
context.updateTemasDpSelection(section);
context.calculateTemasSectionTotal(1);
assert.equal(dpSelect.validationMessage, '');
assert.equal(fields['.temas-cash-value'].value, 0);
const money = amount => vm.runInContext('temasMoney(' + Number(amount) + ')', context);
assert.equal(fields['.temas-settlement-balance'].textContent, money(150000.25));
dpSelect.selectedOptions = [];
context.updateTemasDpSelection(section);
assert.equal(section.dataset.dpAmount, 0);
assert.equal(other.disabled, false);
console.log('PASS: multiple DP balances, decimal precision and cross-voyage selection');

const source = option('1', '35000000');
dpSelect.options = [source];
dpSelect.selectedOptions = [source];
fields['.grand-total-value-temas'].value = 33000000;
context.calculateTemasDpUsageForAllSections();
assert.equal(fields['.temas-cash-value'].value, 0);
assert.equal(fields['.temas-settlement-balance'].textContent, money(2000000));
const nextFields = {};
for (const key of ['temas-payment-mode', 'grand-total-value-temas', 'temas-dp-reference', 'temas-settlement-dp', 'temas-settlement-balance', 'temas-cash-value', 'temas-cash-display']) nextFields['.' + key] = input();
nextFields['.temas-payment-mode'].value = 'pelunasan_dp';
nextFields['.grand-total-value-temas'].value = 5000000;
nextFields['.temas-dp-reference'].selectedOptions = [option('1', '35000000')];
const nextSection = Object.assign(group(nextFields), {dataset: {}});
context.document.querySelectorAll = () => [section, nextSection];
context.calculateTemasDpUsageForAllSections();
assert.equal(nextFields['.temas-cash-value'].value, 3000000);
assert.equal(nextFields['.temas-settlement-balance'].textContent, money(0));
assert.equal(nextSection.dataset.dpAmount, 2000000);
fields['.grand-total-value-temas'].value = 32000000;
context.calculateTemasDpUsageForAllSections();
assert.equal(nextFields['.temas-cash-value'].value, 2000000);
console.log('PASS: DP 35m covers invoice 33m and retains 2m for the next invoice; sections share the balance once');

(async () => {
    class OptionFixture {
        constructor(text, value) { this.text = text; this.value = String(value); this.dataset = {}; this.selected = false; }
        cloneNode() { const copy = new OptionFixture(this.text, this.value); copy.dataset = {...this.dataset}; copy.selected = this.selected; return copy; }
    }
    const select = {
        options: [],
        get selectedOptions() { return this.options.filter(o => o.selected); },
        add(option) { this.options.push(option); },
        replaceChildren(...options) { this.options = options; },
    };
    const saved1 = new OptionFixture('DP 1', 1);
    const saved2 = new OptionFixture('DP 2', 2);
    Object.assign(saved1, {selected: true, dataset: {amount: '100000.25', kapal: 'TEMAS 1', voyage: 'V001'}});
    Object.assign(saved2, {selected: true, dataset: {amount: '50000.50', kapal: 'TEMAS 1', voyage: 'V001'}});
    select.options = [saved1, saved2];
    const reloaded = {isConnected: true, dataset: {}, querySelector: selector => selector === '.temas-dp-reference' ? select : {textContent: ''}};
    context.Option = OptionFixture;
    context.fetch = async () => ({ok: true, json: async () => ({data: [{id: 1, label: 'DP 1', nominal_dibayar: '100000.25', kapal: 'TEMAS 1', voyage: 'V001'}]})});
    context.updateTemasPaymentMode = () => {};
    await context.loadTemasDps(reloaded);
    assert.deepEqual(select.selectedOptions.map(o => o.value), ['1', '2']);
    assert.equal(reloaded.dataset.dpAmount, 150000.75);
    console.log('PASS: reloading DP candidates preserves every selected reference');
})().catch(error => { console.error(error); process.exitCode = 1; });
