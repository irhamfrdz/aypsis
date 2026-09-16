// Run: node tests/Browser/gudang-plan-smoke.mjs [path-to-chrome]
// Uses local Chrome headlessly, the real Blade partial, and a mocked save endpoint.
// Server-side persistence and validation are covered by GudangPlanTest.
import {spawn, spawnSync} from 'node:child_process';
import {mkdtemp, rm, writeFile} from 'node:fs/promises';
import {tmpdir} from 'node:os';
import {join, resolve, sep} from 'node:path';
import {pathToFileURL} from 'node:url';
import assert from 'node:assert/strict';

const root = resolve(import.meta.dirname, '../..');
const output = spawnSync('php', ['tests/Browser/gudang-plan-fixture.php'], {
    cwd: root, encoding: 'utf8', windowsHide: true,
    env: {...process.env, APP_ENV: 'testing', DB_CONNECTION: 'sqlite', DB_DATABASE: ':memory:', CACHE_STORE: 'array', SESSION_DRIVER: 'array'},
});
assert.equal(output.status, 0, output.stderr);
const fixtures = JSON.parse(output.stdout);
const tempRoot = resolve(tmpdir());
const profile = await mkdtemp(join(tempRoot, 'aypsis-gudang-browser-'));
const chrome = process.argv[2] || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const browser = spawn(chrome, ['--headless=new', '--disable-gpu', '--no-first-run', '--no-default-browser-check', '--remote-debugging-port=0', `--user-data-dir=${profile}`, 'about:blank'], {windowsHide: true, stdio: ['ignore', 'ignore', 'pipe']});
let socket;
try {
    const browserUrl = await new Promise((resolve, reject) => {
        const timeout = setTimeout(() => reject(new Error('Chrome did not start within 20 seconds')), 20000);
        let stderr = '';
        browser.on('error', reject);
        browser.stderr.on('data', chunk => {
            stderr += chunk;
            const match = stderr.match(/DevTools listening on (ws:\/\/[^\s]+)/);
            if (match) {clearTimeout(timeout); resolve(match[1]);}
        });
    });
    socket = new WebSocket(browserUrl);
    await new Promise((resolve, reject) => {socket.onopen = resolve; socket.onerror = reject;});
    let sequence = 0;
    const pending = new Map();
    const errors = [];
    socket.onmessage = ({data}) => {
        const response = JSON.parse(data);
        if (response.method === 'Runtime.exceptionThrown') errors.push(response.params.exceptionDetails.text);
        if (pending.has(response.id)) {
            const {resolve, reject} = pending.get(response.id); pending.delete(response.id);
            response.error ? reject(new Error(response.error.message)) : resolve(response.result);
        }
    };
    const call = (method, params = {}, sessionId) => new Promise((resolve, reject) => {
        const id = ++sequence; pending.set(id, {resolve, reject}); socket.send(JSON.stringify({id, method, params, ...(sessionId ? {sessionId} : {})}));
    });
    const {targetId} = await call('Target.createTarget', {url: 'about:blank'});
    const {sessionId} = await call('Target.attachToTarget', {targetId, flatten: true});
    const cdp = (method, params) => call(method, params, sessionId);
    await cdp('Runtime.enable');
    await cdp('Page.enable');
    await cdp('Emulation.setDeviceMetricsOverride', {width: 1440, height: 1000, deviceScaleFactor: 1, mobile: false});
    const evaluate = async expression => {
        const result = await cdp('Runtime.evaluate', {expression, returnByValue: true, awaitPromise: true});
        if (result.exceptionDetails) throw new Error(JSON.stringify(result.exceptionDetails));
        return result.result.value;
    };
    const waitFor = async expression => {
        for (let i = 0; i < 60; i++) {
            if (await evaluate(expression)) return;
            await new Promise(resolve => setTimeout(resolve, 50));
        }
        throw new Error(`Timed out: ${expression}`);
    };
    const load = async mode => {
        const path = join(profile, `${mode}.html`);
        await writeFile(path, fixtures[mode]);
        await cdp('Page.navigate', {url: pathToFileURL(path).href});
        await waitFor("document.querySelectorAll('.gp-stat').length === 4");
        await evaluate(`
            window.confirm = () => true;
            window.savedCalls = [];
            window.serverState = structuredClone(window.gudangPlanConfig.state);
            window.fetch = async (url, options) => {
                const body = JSON.parse(options.body);
                savedCalls.push({url, method: options.method, body});
                if (body.layout) serverState.layout = body.layout;
                else if (options.method === 'DELETE') serverState.positions = serverState.positions.filter(p => !url.endsWith('/' + p.id));
                else {
                    const container = serverState.containers.find(c => c.source === body.source && c.container_id === body.container_id);
                    const existing = serverState.positions.find(p => p.key === container.key);
                    serverState.positions = serverState.positions.filter(p => p.key !== container.key);
                    serverState.positions.push({...body, id: existing?.id || savedCalls.length, key: container.key, container_number: container.number, span: container.span, stale: false});
                }
                serverState.version++;
                return {ok: true, json: async () => structuredClone(serverState)};
            };
        `);
    };

    await load('layout');
    await evaluate("document.getElementById('gp-add-block').click()");
    assert.equal(await evaluate("document.querySelectorAll('.gp-cell').length"), 40);
    await evaluate(`
        const code = document.querySelector('[data-field="code"]'); code.value = 'B'; code.dispatchEvent(new Event('input'));
        const bays = document.querySelector('[data-field="bays"]'); bays.value = '6'; bays.dispatchEvent(new Event('input'));
        document.getElementById('gp-save-layout').click();
    `);
    await waitFor("savedCalls.length === 1 && !document.getElementById('gp-controls').disabled");
    assert.equal(await evaluate('savedCalls[0].body.layout.blocks[0].code'), 'B');
    assert.equal(await evaluate('savedCalls[0].body.layout.blocks[0].bays'), 6);
    await evaluate("document.querySelector('.gp-cell').click(); document.getElementById('gp-save-layout').click()");
    await waitFor("savedCalls.length === 2 && !document.getElementById('gp-controls').disabled");
    assert.equal(await evaluate('serverState.layout.blocks[0].disabled.length'), 1);

    await load('positions');
    assert.equal(await evaluate("/\\b(Bay|Tier)\\b/.test(document.body.innerText)"), false);
    assert.equal(await evaluate("document.getElementById('gp-list-filter').value"), 'all');
    await evaluate("document.querySelector('.gp-card').click(); document.querySelector('.gp-cell').click()");
    await waitFor("document.querySelectorAll('.gp-filled').length === 1");
    assert.equal(await evaluate('serverState.positions[0].container_number'), 'AYPU1234567');
    assert.equal(await evaluate("document.querySelector('.gp-location-code').textContent"), 'A-S01-B01-T01');
    assert.equal(await evaluate("document.getElementById('gp-position-rows').textContent.includes('A-S01-B01-T01')"), true);
    await evaluate("const search = document.getElementById('gp-search'); search.value = 'a-s01-b01-t01'; search.dispatchEvent(new Event('input'))");
    assert.equal(await evaluate("document.querySelectorAll('.gp-card').length"), 1);
    assert.equal(await evaluate("document.querySelector('.gp-card').textContent.includes('AYPU1234567')"), true);
    await evaluate("document.getElementById('gp-search').value = ''; document.getElementById('gp-search').dispatchEvent(new Event('input'))");
    await evaluate("document.getElementById('gp-remove-position').click()");
    await waitFor("document.querySelectorAll('.gp-filled').length === 0");
    await evaluate(`
        const card = [...document.querySelectorAll('.gp-card')].find(c => c.textContent.includes('SEWU'));
        const transfer = new DataTransfer();
        card.dispatchEvent(new DragEvent('dragstart', {dataTransfer: transfer, bubbles: true}));
        document.querySelector('.gp-cell').dispatchEvent(new DragEvent('drop', {dataTransfer: transfer, bubbles: true, cancelable: true}));
        card.dispatchEvent(new DragEvent('dragend', {dataTransfer: transfer}));
    `);
    await waitFor("document.querySelectorAll('.gp-filled').length === 1");
    assert.equal(await evaluate("document.querySelector('.gp-filled').style.gridRow"), '2 / span 2');
    await evaluate("document.querySelector('.gp-filled').click(); document.getElementById('gp-input-row').value = '2'; document.getElementById('gp-input-row').dispatchEvent(new Event('change'))");
    assert.equal(await evaluate("document.querySelector('.gp-location-code').textContent"), 'A-S01-B02-T01');
    assert.equal(await evaluate("document.getElementById('gp-location-summary').textContent.includes('slot 01–02')"), true);
    await evaluate("document.getElementById('gp-position-form').requestSubmit()");
    await waitFor('serverState.positions[0].row === 2');
    await cdp('Emulation.setDeviceMetricsOverride', {width: 390, height: 844, deviceScaleFactor: 1, mobile: true});
    assert.equal(await evaluate('document.documentElement.scrollWidth <= window.innerWidth'), true);
    await evaluate("window.fetch = async () => ({ok: false, status: 409, json: async () => ({message: 'Denah sudah diubah pengguna lain.'})}); document.getElementById('gp-position-form').requestSubmit()");
    await waitFor("document.getElementById('gp-message').textContent.includes('pengguna lain')");
    assert.equal(await evaluate("document.getElementById('gp-controls').disabled"), true);

    await load('readonly');
    assert.equal(await evaluate("document.getElementById('gp-save-position') === null"), true);
    assert.equal(await evaluate("document.querySelectorAll('[draggable=true]').length"), 0);
    assert.deepEqual(errors, []);
    console.log('PASS: layout, blocked cells, placement, drag-and-drop 40ft, move, delete, location codes and search, yard labels, mobile width, conflicts, read-only UI.');
    await call('Browser.close');
} finally {
    socket?.close();
    browser.kill();
    // Only remove the test's uniquely-created directory within the system temporary directory.
    const target = resolve(profile);
    if (!target.startsWith(tempRoot + sep) || !target.includes('aypsis-gudang-browser-')) throw new Error('Unexpected browser profile path');
    await rm(target, {recursive: true, force: true, maxRetries: 10, retryDelay: 200});
}
