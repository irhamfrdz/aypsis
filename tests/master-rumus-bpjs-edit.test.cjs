const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync('resources/views/master/rumus-bpjs/index.blade.php', 'utf8');
const script = source.match(/<script>([\s\S]*?)<\/script>/)[1];

function load() {
    const elements = new Map();
    for (const [, id] of source.matchAll(/id="([^"]+)"/g)) {
        const classes = new Set();
        elements.set(id, {
            value: '', fields: [], style: {},
            classList: {
                add: value => classes.add(value), remove: value => classes.delete(value),
                contains: value => classes.has(value),
                toggle(value, enabled) { if (enabled) classes.add(value); else classes.delete(value); },
            },
            querySelectorAll() { return this.fields; },
        });
    }
    const errors = [];
    const context = vm.createContext({ document: { getElementById: id => elements.get(id) || null },
        console: { error: (...args) => errors.push(args) }, alert: message => errors.push(message) });
    vm.runInContext(script, context);
    context.editAddTierRow = () => {};
    context.editAddNonCrewTierRow = () => {};
    return { context, elements, errors };
}

test('edit opens each category with saved data and disables conflicting hidden formula inputs', () => {
    for (const [jenis, group, tipe, activeSection] of [
        ['jkn', 'JKN-KIS', 'NON KARY', 'edit_jkn_fields'],
        ['jamsostek', 'BPU', 'CREW', 'edit_bpu_crew_fields'],
        ['jamsostek', 'BPU', 'NON KARY', 'edit_jamsostek_rate_fields'],
        ['jamsostek', 'PPU', 'KANTOR', 'edit_jamsostek_rate_fields'],
    ]) {
        const { context, elements, errors } = load();
        for (const id of ['edit_jkn_fields', 'edit_bpu_crew_fields', 'edit_jamsostek_rate_fields']) {
            elements.get(id).fields = [{ disabled: false }];
        }
        elements.get('editModal').classList.add('hidden');
        const data = { id: 7, jenis, group_name: group, tipe, tunjangan_persen: '4.00' };
        context.handleEditClick({ getAttribute: key => ({
            'data-rumus': JSON.stringify(data), 'data-groups': JSON.stringify([group]), 'data-ids': '7',
        })[key] });
        assert.deepEqual(errors, []);
        assert.equal(elements.get('editModal').classList.contains('hidden'), false);
        assert.equal(elements.get('editForm').action, '/master-rumus-bpjs/7');
        assert.equal(elements.get('edit_tipe').value, tipe);
        for (const id of ['edit_jkn_fields', 'edit_bpu_crew_fields', 'edit_jamsostek_rate_fields']) {
            assert.equal(elements.get(id).fields[0].disabled, id !== activeSection);
        }
        assert.equal(elements.get('edit_hutang_tiers').disabled, jenis !== 'jamsostek');
    }
});
