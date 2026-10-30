const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
require('../public/js/pranota-bpjs-refresh.js');
require('../public/js/pranota-bpjs-jamsostek.js');
require('../public/js/pranota-bpjs-filters.js');

function setup(type, fetchData, failGeneration = false) {
    const source = fs.readFileSync('resources/views/pranota-bpjs/create.blade.php', 'utf8');
    const start = source.indexOf('    async function generateRows(');
    const end = source.indexOf('    btnAdd.addEventListener(', start);
    const handlerStart = source.indexOf("    document.getElementById('btn-generate-all').addEventListener");
    const handlerEnd = source.indexOf("    document.getElementById('btn-refresh-bpjs').addEventListener", handlerStart);
    const handler = source.slice(handlerStart, handlerEnd).replace(/@json\([^\n]*\)/g, "'/pranota-bpjs/create'");
    const people = ['Karyawan_1', 'KaryawanTidakTetap_1'].map(unique_id => ({
        id: 1, unique_id, group_bp_jamsostek: 'BPU TEST', tipe_bp_jamsostek: type, dpp_bp_jamsostek: 2200000,
    }));
    const rule = { jenis: 'jamsostek', group_name: 'BPU TEST', tipe: type, diskon_status: 'ada',
        diskon_tipe: 'persen', diskon_nilai: 50, diskon_pengecualian: ['Karyawan_1'],
        tunjangan_persen: 1, biaya_persen: 6800, jht_biaya: 2, jkk_tunjangan: 1, jkm_tunjangan: 6800,
        hutang_tiers: [{ dpp: 2200000, potongan: type === 'CREW' ? 5720 : 36000 }] };
    const execute = new Function('initialPeople', 'initialRules', 'loadBpjsData', 'failGeneration', `
        let karyawans = initialPeople, rumusBpjs = initialRules;
        let isGenerating = false, isRefreshing = false, rowCount = 1;
        const previousRows = [{ manualAmount: 12345 }];
        const container = { inert: false, rows: previousRows, replaceChildren(fragment) { this.rows = fragment.rows; } };
        let rowTarget = container;
        const alerts = [];
        const control = { disabled: false, innerHTML: 'Hitung Semua', setAttribute() {}, removeAttribute() {},
            addEventListener(name, callback) { this.handler = callback; } };
        const submit = { disabled: false }, status = { hidden: true, textContent: '' };
        const document = { createDocumentFragment() { return { rows: [] }; },
            getElementById: id => id === 'btn-generate-all' ? control : id === 'bpjs-refresh-status' ? status
                : { querySelectorAll: () => [control, submit] } };
        const BpjsAllowances = { updateRow() {} };
        const parseIdNumber = Number, formatNumber = String;
        const console = { error() {} };
        function alert(message) { alerts.push(message); }
        function applyFilter() {}
        function populateGroupFilterOptions() {}
        function populateCabangFilterOptions() {}
        async function processBpjsBatch(items, process, progress) {
            for (const item of items) process(item);
            progress(items.length, items.length);
        }
        function addRow(key) {
            if (failGeneration && key === 'KaryawanTidakTetap_1') throw new Error('row failed');
            const person = karyawans.find(employee => employee.unique_id === key);
            const formula = BpjsJamsostek.findFormula(rumusBpjs, person);
            const amounts = person.tipe_bp_jamsostek === 'CREW'
                ? BpjsJamsostek.calculateCrew(formula, person.dpp_bp_jamsostek, person)
                : BpjsJamsostek.calculateNonCrew(formula, person.dpp_bp_jamsostek, person);
            rowTarget.rows.push({ key, amounts });
            rowCount++;
        }
        ${source.slice(start, end)}
        ${handler}
        return { generate: () => control.handler(),
            snapshot: () => ({ karyawans, rumusBpjs, rows: container.rows, previousRows, alerts, rowCount,
                busy: isGenerating, inert: container.inert, disabled: control.disabled, submitDisabled: submit.disabled, status: status.textContent }) };
    `);
    return { people, rule, create: () => execute(people, [rule], args => loadBpjsData({ ...args, fetchData }), failGeneration) };
}

test('Hitung Semua fetches the latest exclusions before computing both employee models for crew and noncrew', async () => {
    for (const type of ['CREW', 'TUNAI']) {
        let fixture, calls = 0;
        fixture = setup(type, async (url, options) => {
            calls++;
            assert.equal(url, '/pranota-bpjs/create');
            assert.equal(options.cache, 'no-store');
            return { ok: true, json: async () => ({ karyawans: fixture.people,
                rumusBpjs: [{ ...fixture.rule, diskon_pengecualian: ['KaryawanTidakTetap_1'] }] }) };
        });
        const screen = fixture.create();
        await screen.generate();
        const state = screen.snapshot();
        assert.equal(calls, 1);
        assert.equal(state.rows.length, 2);
        assert.equal(state.rows[0].amounts.jkk, type === 'CREW' ? 5280 : 11000);
        assert.equal(state.rows[0].amounts.jkm, 3400);
        assert.equal(state.rows[1].amounts.jkk, type === 'CREW' ? 16280 : 22000);
        assert.equal(state.rows[1].amounts.jkm, 6800);
        assert.equal(state.busy, false);
        assert.equal(state.disabled, false);
        assert.equal(state.inert, false);
        assert.equal(state.alerts.length, 0);
        assert.deepEqual(state.rumusBpjs[0].diskon_pengecualian, ['KaryawanTidakTetap_1']);
    }
});

test('a failed load or calculation preserves previous rows and restores the prior formula snapshot', async () => {
    for (const failGeneration of [false, true]) {
        let fixture;
        fixture = setup('CREW', async () => {
            if (!failGeneration) return { ok: false };
            return { ok: true, json: async () => ({ karyawans: fixture.people.map(employee => ({ ...employee })),
                rumusBpjs: [{ ...fixture.rule, diskon_pengecualian: [] }] }) };
        }, failGeneration);
        const screen = fixture.create();
        await screen.generate();
        const state = screen.snapshot();
        assert.equal(state.rows, state.previousRows);
        assert.equal(state.rows[0].manualAmount, 12345);
        assert.equal(state.karyawans, fixture.people);
        assert.equal(state.rumusBpjs[0], fixture.rule);
        assert.equal(state.alerts.length, 1);
        assert.equal(state.rowCount, 1);
        assert.equal(state.disabled, false);
        assert.equal(state.submitDisabled, false);
        assert.equal(state.busy, false);
        assert.equal(state.inert, false);
    }
});
