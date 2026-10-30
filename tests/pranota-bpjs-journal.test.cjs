const test = require('node:test');
const assert = require('node:assert/strict');
require('../public/js/pranota-bpjs-journal.js');
require('../public/js/pranota-bpjs-performance.js');
require('../public/js/pranota-bpjs-jkn.js');
require('../public/js/pranota-bpjs-allowances.js');
const calculate = BpjsJournal.calculate;
const row = (journal, key) => journal.rows.find(item => item.key === key);

test('journal contains every requested subgroup in the specified order, with zero totals before calculation', () => {
    const journal = calculate([]);
    assert.deepEqual(journal.rows.map(item => item.key), ['CREW', 'HARIAN', 'TRANSFER', 'TUNAI', 'KANTOR',
        'LAPANGAN', 'NONKARYPBM', 'NONKARYUMKM', 'NONKARYUMKMKIS', 'NONKARY', 'NONKARYUMKMNONPP']);
    assert.deepEqual(journal.totals, { tunjangan: 0, biaya: 0, hutang: 0, debit: 0, grand_total: 0 });
    assert.equal(row(journal, 'CREW').account, '5501.001 - Biaya Gaji ABK');
    assert.equal(row(journal, 'NONKARY').account, '6101.007 - Biaya Pengobatan Jakarta');
});

test('JKN and Jamsostek amounts follow their own types and reconcile all contribution components', () => {
    const journal = calculate([{
        employee: { group_jkn: 'JKN-KIS', tipe_jkn: ' Kantor ', group_bp_jamsostek: 'PPU', tipe_bp_jamsostek: 'Transfer' },
        amounts: { bpjs_kesehatan: 229195.2, bpjs_ketenagakerjaan: 57298.8, kis_biaya: 0,
            jht_biaya: 100000.8, jht_hutang: 20000, jkk_tunjangan: 1000, jkm_tunjangan: 2000,
            jp_biaya: 10000, jp_hutang: 11000, jkk_hutang: 3000, bpu_jkk_tunjangan: 4000, bpu_jkm: 5000,
            noncrew_jht_biaya: 6000, noncrew_jht_hutang: 7000, noncrew_jkk_tunjangan: 8000, noncrew_jkm_tunjangan: 9000 },
    }]);
    assert.equal(row(journal, 'KANTOR').tunjangan, 229195.2);
    assert.equal(row(journal, 'KANTOR').hutang, 57298.8);
    assert.equal(row(journal, 'TRANSFER').tunjangan, 29000);
    assert.equal(row(journal, 'TRANSFER').biaya, 116000.8);
    assert.equal(row(journal, 'TRANSFER').hutang, 41000);
    assert.equal(journal.totals.grand_total, 472494.8);
    assert.equal(journal.totals.debit, 374196);
});

test('NON KARY JKN and PPU costs are shown as biaya and do not duplicate allowance or debt', () => {
    const journal = calculate([{
        employee: { group_jkn: ' jkn - kis ', tipe_jkn: 'NON_KARY', group_bp_jamsostek: 'PPU', tipe_bp_jamsostek: 'NON KARY' },
        amounts: { bpjs_kesehatan: 286494, bpjs_ketenagakerjaan: 0, jht_biaya: 300000,
            jht_hutang: 30000, jkk_tunjangan: 10000, jkm_tunjangan: 10000, jp_biaya: 5000, jp_hutang: 2552 },
    }]);
    assert.equal(row(journal, 'NONKARY').biaya, 644046);
    assert.equal(row(journal, 'NONKARY').tunjangan, 0);
    assert.equal(row(journal, 'NONKARY').hutang, 0);
    assert.equal(journal.totals.grand_total, 644046);
});

test('UMKM Non PP costs and debts stay in their subgroup while JKN uses a different subgroup', () => {
    const record = {
        employee: { group_jkn: 'JKN-KIS', tipe_jkn: 'Non Kary - UMKM KIS',
            group_bp_jamsostek: 'BPU-SUPIR JKT PLUIT', tipe_bp_jamsostek: ' non kary - umkm non pp ' },
        amounts: { bpjs_kesehatan: 10000.25, bpjs_ketenagakerjaan: 1000.5,
            noncrew_jht_biaya: 19000, noncrew_jht_hutang: 70000, noncrew_jkk_tunjangan: 22250, noncrew_jkm_tunjangan: 3400 },
    };
    const journal = calculate([record]);
    assert.equal(row(journal, 'NONKARYUMKMNONPP').biaya, 44650);
    assert.equal(row(journal, 'NONKARYUMKMNONPP').hutang, 70000);
    assert.equal(row(journal, 'NONKARYUMKMNONPP').tunjangan, 0);
    assert.equal(row(journal, 'NONKARYUMKMKIS').tunjangan, 10000.25);
    assert.equal(row(journal, 'NONKARYUMKMKIS').hutang, 1000.5);
    assert.equal(journal.totals.grand_total, 125650.75);
    const legacy = calculate([{ ...record, aggregate_jamsostek: true, amounts: { jht_biaya: 19000,
        jht_hutang: 70000, jkk_tunjangan: 22250, jkm_tunjangan: 3400 } }]);
    assert.equal(row(legacy, 'NONKARYUMKMNONPP').biaya, 44650);
    assert.equal(legacy.totals.grand_total, 114650);
});

test('special-case JKN is biaya, unknown types remain visible, and cents do not drift', () => {
    const journal = calculate([{
        employee: { tipe_jkn: 'CREW', tipe_bp_jamsostek: 'Tipe Baru' },
        amounts: { bpjs_kesehatan: 0.1, kis_biaya: 0.1, bpjs_ketenagakerjaan: 0.2, jht_biaya: 0.3 },
    }]);
    assert.equal(row(journal, 'CREW').biaya, 0.1);
    assert.equal(row(journal, 'CREW').hutang, 0.2);
    assert.equal(row(journal, 'LAINNYA').biaya, 0.3);
    assert.equal(journal.totals.grand_total, 0.6);
});

test('live journal includes selected hidden rows, refreshes after edits, and uses contributions independently of manual summaries', () => {
    const previousDocument = globalThis.document;
    const node = () => ({ children: [], appendChild(child) { this.children.push(child); },
        replaceChildren(fragment) { this.children = fragment.children; } });
    const body = node(), totals = {};
    for (const key of ['tunjangan', 'biaya', 'hutang', 'debit', 'grand_total']) totals[key] = {};
    const section = { querySelector(selector) {
        if (selector === '[data-journal-rows]') return body;
        return totals[selector.match(/"([^"]+)"/)[1]];
    } };
    globalThis.document = { querySelector: () => section, querySelectorAll: () => [], getElementById: () => null,
        createElement: node, createDocumentFragment: node };
    const input = { value: '100' };
    function makeRow(hidden) {
        const fields = new Map([
            ['.karyawan-hidden-input', { name: 'details[1][karyawan_id]', value: '1' }],
            ['.tipe-karyawan-hidden-input', { value: 'App\\Models\\Karyawan' }],
            ['.input-kes', input], ['.input-ket', { value: '25' }], ['.input-jht-biaya', { value: '10' }],
            ['.input-jht-hutang', { value: '5' }], ['.input-jkk', { value: '1' }], ['.input-jkm', { value: '1' }],
        ]);
        return { dataset: { groupJkn: 'JKN-KIS', tipeJkn: 'CREW', groupJamsostek: 'PPU', tipeJamsostek: 'Harian' },
            allowanceOverrides: { pt: 99999 }, classList: { contains: () => hidden },
            querySelector: selector => fields.get(selector) || null };
    }
    const rows = [makeRow(false), makeRow(true)];
    const container = { querySelectorAll: () => rows };
    try {
        BpjsAllowances.update(container, Number, String);
        assert.equal(body.children.length, 11);
        assert.equal(totals.grand_total.textContent, '284,00');
        assert.equal(body.children[0].children[1].textContent, '200,00');
        assert.equal(body.children[1].children[2].textContent, '20,00');
        input.value = '200.25';
        BpjsAllowances.update(container, Number, String);
        assert.equal(body.children.length, 11);
        assert.equal(totals.grand_total.textContent, '484,50');
    } finally { globalThis.document = previousDocument; }
});
