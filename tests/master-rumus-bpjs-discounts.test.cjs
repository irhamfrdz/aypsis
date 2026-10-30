const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

test('discount modal preserves selections through search, saves model-specific IDs and clears prior group data', async () => {
    let document;
    const element = () => {
        const classes = new Set(['hidden']);
        return { children: [], listeners: {}, dataset: {}, value: '', disabled: false,
            classList: { add: name => classes.add(name), remove: name => classes.delete(name), contains: name => classes.has(name) },
            append(...items) { this.children.push(...items); },
            appendChild(item) { this.children.push(item); },
            replaceChildren() { this.children = []; },
            addEventListener(name, callback) { this.listeners[name] = callback; },
            focus() { document.activeElement = this; },
        };
    };
    const ids = ['modal', 'list', 'search', 'status', 'count', 'save', 'close', 'cancel', 'groups'];
    const controls = Object.fromEntries(ids.map(id => ['bpjs-discount-' + id, element()]));
    const buttons = [1, 2].map(id => {
        const button = element();
        button.dataset = { ids: String(id), loadUrl: '/formula/' + id + '/employees', saveUrl: '/formula/' + id + '/exclusions' };
        button.counter = element();
        button.querySelector = () => button.counter;
        return button;
    });
    document = { activeElement: null, addEventListener(name, callback) { callback(); },
        getElementById: id => controls[id], createElement: () => element(),
        querySelectorAll: () => buttons, querySelector: () => ({ content: 'csrf-test' }) };
    const data = { employees: [
        { unique_id: 'Karyawan_1', nama_lengkap: 'Tetap', nik: '001', tipe_label: 'Tetap', group: 'A' },
        { unique_id: 'KaryawanTidakTetap_1', nama_lengkap: 'Tidak Tetap', nik: '002', tipe_label: 'Tidak Tetap', group: 'A' },
    ], selected: [], groups: ['A'] };
    const requests = [];
    let finishSecondLoad, rejectSave = false;
    const fetch = async (url, options) => {
        requests.push({ url, options });
        if (options.method === 'PUT') return rejectSave
            ? { ok: false, json: async () => ({ errors: { diskon_pengecualian: ['Group karyawan sudah berubah.'] } }) }
            : { ok: true, json: async () => ({ success: true, count: JSON.parse(options.body).diskon_pengecualian.length }) };
        if (url.startsWith('/formula/2')) return new Promise(resolve => { finishSecondLoad = resolve; });
        return { ok: true, json: async () => data };
    };
    vm.runInNewContext(fs.readFileSync('public/js/master-rumus-bpjs-discounts.js', 'utf8'), { document, fetch });
    const list = controls['bpjs-discount-list'], search = controls['bpjs-discount-search'];
    const save = controls['bpjs-discount-save'], modal = controls['bpjs-discount-modal'];
    await buttons[0].listeners.click();
    assert.equal(list.children.length, 2);
    list.children[0].children[0].checked = true;
    list.children[0].children[0].listeners.change();
    search.value = 'Tidak';
    search.listeners.input();
    assert.equal(list.children.length, 1);
    list.children[0].children[0].checked = true;
    list.children[0].children[0].listeners.change();
    search.value = '';
    search.listeners.input();
    assert.ok(list.children.every(label => label.children[0].checked));
    await save.listeners.click();
    assert.deepEqual(JSON.parse(requests[1].options.body).diskon_pengecualian, ['Karyawan_1', 'KaryawanTidakTetap_1']);
    assert.equal(requests[1].options.headers['X-CSRF-TOKEN'], 'csrf-test');
    assert.equal(buttons[0].counter.textContent, 2);
    assert.equal(modal.classList.contains('hidden'), true);
    const loading = buttons[1].listeners.click();
    assert.equal(search.disabled, true);
    assert.equal(list.children.length, 0);
    finishSecondLoad({ ok: true, json: async () => ({ employees: [], selected: ['Karyawan_1'], groups: ['B'] }) });
    await loading;
    assert.equal(controls['bpjs-discount-groups'].textContent, 'B');
    assert.equal(controls['bpjs-discount-count'].textContent, '0 karyawan tanpa diskon');
    rejectSave = true;
    await save.listeners.click();
    assert.equal(controls['bpjs-discount-status'].textContent, 'Group karyawan sudah berubah.');
    assert.equal(modal.classList.contains('hidden'), false);
    assert.equal(save.disabled, false);
    rejectSave = false;
    await save.listeners.click();
    assert.deepEqual(JSON.parse(requests.at(-1).options.body).diskon_pengecualian, []);
    assert.equal(buttons[1].counter.textContent, 0);
});
