// Run with Node and local Chrome. Uses real Blade markup and mocked requests; no application data is changed.
import {spawn, spawnSync} from 'node:child_process';
import {mkdtemp, rm, writeFile} from 'node:fs/promises';
import {tmpdir} from 'node:os';
import {join, resolve, sep} from 'node:path';
import {pathToFileURL} from 'node:url';
import assert from 'node:assert/strict';

const root = resolve(import.meta.dirname, '../..');
const fixture = spawnSync('php', ['tests/Browser/manifest-shipper-fixture.php'], {
    cwd: root, encoding: 'utf8', windowsHide: true,
    env: {...process.env, APP_ENV: 'testing', DB_CONNECTION: 'sqlite', DB_DATABASE: ':memory:', CACHE_STORE: 'array', SESSION_DRIVER: 'array'},
});
assert.equal(fixture.status, 0, fixture.stderr);
const tempRoot = resolve(tmpdir());
const profile = await mkdtemp(join(tempRoot, 'aypsis-shipper-browser-'));
const browser = spawn(process.argv[2] || 'C:/Program Files/Google/Chrome/Application/chrome.exe', [
    '--headless=new', '--disable-gpu', '--no-first-run', '--no-default-browser-check', '--remote-debugging-port=0', `--user-data-dir=${profile}`, 'about:blank',
], {windowsHide: true, stdio: ['ignore', 'ignore', 'pipe']});
let socket;
try {
    const url = await new Promise((resolve, reject) => {
        const timeout = setTimeout(() => reject(new Error('Chrome startup timeout')), 20000);
        let stderr = '';
        browser.on('error', reject);
        browser.stderr.on('data', chunk => {
            stderr += chunk;
            const match = stderr.match(/DevTools listening on (ws:\/\/[^\s]+)/);
            if (match) {clearTimeout(timeout); resolve(match[1]);}
        });
    });
    socket = new WebSocket(url);
    await new Promise((resolve, reject) => {socket.onopen = resolve; socket.onerror = reject;});
    const pending = new Map();
    const errors = [];
    let sequence = 0;
    socket.onmessage = ({data}) => {
        const response = JSON.parse(data);
        if (response.method === 'Runtime.exceptionThrown') errors.push(response.params.exceptionDetails.text);
        if (pending.has(response.id)) {
            const callback = pending.get(response.id); pending.delete(response.id);
            response.error ? callback.reject(new Error(response.error.message)) : callback.resolve(response.result);
        }
    };
    const call = (method, params = {}, sessionId) => new Promise((resolve, reject) => {
        const id = ++sequence; pending.set(id, {resolve, reject}); socket.send(JSON.stringify({id, method, params, ...(sessionId ? {sessionId} : {})}));
    });
    const {targetId} = await call('Target.createTarget', {url: 'about:blank'});
    const {sessionId} = await call('Target.attachToTarget', {targetId, flatten: true});
    const cdp = (method, params) => call(method, params, sessionId);
    await cdp('Runtime.enable');
    const evaluate = async expression => {
        const response = await cdp('Runtime.evaluate', {expression, returnByValue: true, awaitPromise: true});
        if (response.exceptionDetails) throw new Error(JSON.stringify(response.exceptionDetails));
        return response.result.value;
    };
    const waitFor = async expression => {
        for (let i = 0; i < 80; i++) {
            if (await evaluate(expression)) return;
            await new Promise(resolve => setTimeout(resolve, 50));
        }
        throw new Error(`Timed out: ${expression}`);
    };
    const path = join(profile, 'manifest.html');
    await writeFile(path, fixture.stdout);
    await cdp('Page.navigate', {url: pathToFileURL(path).href});
    await waitFor("document.getElementById('manifest-shipper-form') !== null");
    await evaluate(`
        window.calls = [];
        window.mockStatus = 422;
        window.fetch = async (url, options) => {
            if (options.method === 'POST') {
                calls.push({url, body: JSON.parse(options.body)});
                return {ok: mockStatus === 200, json: async () => mockStatus === 200 ? {success: true} : {errors: {shipper_id: ['Coba kembali']}}};
            }
            return {ok: true, json: async () => [
                {real_id: 10, text: 'PT Pengirim', display_text: 'PT Pengirim - Consignee A', alamat: 'Alamat A', consignee: 'Consignee A', notify_party: 'Notify A', alamat_notify_party: 'Alamat notify A'},
                {real_id: 11, text: 'PT Pengirim', display_text: 'PT Pengirim - Consignee B', alamat: 'Alamat B', consignee: 'Consignee B', notify_party: 'Notify B', alamat_notify_party: 'Alamat notify B'}
            ]};
        };
        document.querySelector('.manifest-shipper-open').click();
    `);
    await waitFor("document.querySelectorAll('.manifest-shipper-option').length === 2");
    await evaluate("document.querySelectorAll('.manifest-shipper-option')[1].click()");
    assert.deepEqual(await evaluate("['alamat_pengirim','penerima','notify_party','alamat_notify_party'].map(n => document.getElementById('manifest-shipper-form').elements.namedItem(n).value)"), ['Alamat B', 'Consignee B', 'Notify B', 'Alamat notify B']);
    await evaluate("document.getElementById('manifest-shipper-search').value = 'Changed'; document.getElementById('manifest-shipper-search').dispatchEvent(new Event('input'))");
    assert.equal(await evaluate("document.getElementById('manifest-shipper-save').disabled"), true);
    await waitFor("document.querySelectorAll('.manifest-shipper-option').length === 2 && !document.getElementById('manifest-shipper-options').hidden");
    await evaluate("document.querySelectorAll('.manifest-shipper-option')[1].click(); document.getElementById('manifest-shipper-consignee').value = 'Manual'; document.getElementById('manifest-shipper-form').requestSubmit()");
    await waitFor("calls.length === 1 && !document.getElementById('manifest-shipper-fields').disabled");
    assert.equal(await evaluate('calls[0].body.shipper_id'), 11);
    assert.equal(await evaluate('calls[0].body.penerima'), 'Manual');
    assert.equal(await evaluate("document.getElementById('manifest-shipper-message').textContent"), 'Coba kembali');
    await evaluate("document.getElementById('manifest-shipper-dialog').close(); document.querySelector('[data-mode=add]').click()");
    await waitFor("document.querySelectorAll('.manifest-shipper-option').length === 2 && !document.getElementById('manifest-shipper-options').hidden");
    assert.equal(await evaluate("document.getElementById('manifest-shipper-cargo').hidden"), false);
    assert.equal(await evaluate("document.getElementById('manifest-shipper-form').elements.namedItem('tonnage').max"), '10');
    assert.equal(await evaluate("document.getElementById('manifest-shipper-consignee').value"), '');
    await evaluate(`
        document.querySelectorAll('.manifest-shipper-option')[0].click();
        const f = document.getElementById('manifest-shipper-form');
        f.elements.namedItem('nomor_bl').value = 'BL-002';
        f.elements.namedItem('tonnage').value = '3.125';
        f.elements.namedItem('volume').value = '4.5';
        f.elements.namedItem('kuantitas').value = '25';
        f.requestSubmit();
    `);
    await waitFor("calls.length === 2 && !document.getElementById('manifest-shipper-fields').disabled");
    assert.equal(await evaluate('calls[1].body.tonnage'), '3.125');
    assert.equal(await evaluate('calls[1].body.kuantitas'), '25');
    assert.equal(await evaluate('calls[1].body.shipper_id'), 10);
    assert.ok((await evaluate('calls[1].url')).endsWith('/add-shipper'));
    await evaluate("mockStatus = 200; document.getElementById('manifest-shipper-form').requestSubmit()");
    await waitFor("Number(sessionStorage.getItem('loads')) === 2");
    assert.equal(await evaluate("document.getElementById('manifest-shipper-dialog').open"), false);
    assert.deepEqual(errors, []);
    console.log('PASS: shipper edit and booking addition, cargo allocation payload, master autofill, validation recovery, successful save reload.');
    await call('Browser.close');
} finally {
    socket?.close(); browser.kill();
    const target = resolve(profile);
    if (!target.startsWith(tempRoot + sep) || !target.includes('aypsis-shipper-browser-')) throw new Error('Unexpected temporary profile path');
    await rm(target, {recursive: true, force: true, maxRetries: 10, retryDelay: 200});
}
