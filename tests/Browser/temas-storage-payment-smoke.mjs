// Real browser DOM + production JS, mocked manifest/DP data; no application writes.
import {spawn} from 'node:child_process';
import {readFile, mkdtemp, rm} from 'node:fs/promises';
import {tmpdir} from 'node:os';
import {join, resolve, sep} from 'node:path';
import assert from 'node:assert/strict';

const tempRoot = resolve(tmpdir());
const profile = await mkdtemp(join(tempRoot, 'aypsis-temas-browser-'));
const browser = spawn(process.argv[2] || 'C:/Program Files/Google/Chrome/Application/chrome.exe', [
    '--headless=new', '--disable-gpu', '--no-first-run', '--no-default-browser-check',
    '--remote-debugging-port=0', `--user-data-dir=${profile}`, 'about:blank',
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
            if (match) { clearTimeout(timeout); resolve(match[1]); }
        });
    });
    socket = new WebSocket(url);
    await new Promise((resolve, reject) => { socket.onopen = resolve; socket.onerror = reject; });
    let sequence = 0;
    const pending = new Map();
    const errors = [];
    socket.onmessage = ({data}) => {
        const response = JSON.parse(data);
        if (response.method === 'Runtime.exceptionThrown') errors.push(response.params.exceptionDetails.text);
        if (pending.has(response.id)) {
            const callback = pending.get(response.id); pending.delete(response.id);
            response.error ? callback.reject(new Error(response.error.message)) : callback.resolve(response.result);
        }
    };
    const call = (method, params = {}, sessionId) => new Promise((resolve, reject) => {
        const id = ++sequence; pending.set(id, {resolve, reject});
        socket.send(JSON.stringify({id, method, params, ...(sessionId ? {sessionId} : {})}));
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
    let source = await readFile('resources/views/biaya-kapal/create/_js-temas.blade.php', 'utf8');
    const helpers = await readFile('resources/views/biaya-kapal/create/_js-temas-payments.blade.php', 'utf8');
    source = source.replace("@include('biaya-kapal.create._js-temas-payments')", () => helpers)
        .replace(/\{\{\s*url\(['"]([^'"]+)['"]\)\s*\}\}/g, '/$1')
        .replace(/\{\{\s*csrf_token\(\)\s*\}\}/g, 'test-token');
    await evaluate(`
        document.body.innerHTML = '<form id="form"><input id="nominal"><button type="button" id="add_temas_section_btn">Tambah</button><div id="temas_sections_container"></div></form>';
        window.confirm = () => true;
        window.fetch = async url => ({ok: true, json: async () => url.includes('temas-dp-candidates')
            ? {data: [{id: 7, kapal: 'TEMAS 1', voyage: 'V001', nominal_dibayar: '300000', label: 'DP TEST'}]}
            : url.includes('get-voyages') ? {success: true, voyages: ['V001']}
            : {success: true, containers_data: [
                {id: 9, nomor_bl: '01', nomor_bl_asli: '01-1', nomor_kontainer: 'TEMU001', size: '20'},
                {id: 10, nomor_bl: '01', nomor_bl_asli: '01-2', nomor_kontainer: 'TEMU002', size: '20'}
            ]}});
        const temasSectionsContainer = document.getElementById('temas_sections_container');
        const addTemasSectionBtn = document.getElementById('add_temas_section_btn');
        const nominalInput = document.getElementById('nominal');
        const allKapalsData = [{nama_kapal: 'TEMAS 1'}];
        const pricelistTemasData = [
            {id: 1, jenis_biaya: 'Handling Jakarta', harga: 1000000, size: '20ft', lokasi: 'Jakarta'},
            {id: 2, jenis_biaya: 'Handling Batam', harga: 900000, size: '20ft', lokasi: 'Batam'}
        ];
        ${source}
        initializeTemasSections();
        window.section = temasSectionsContainer.firstElementChild;
        window.set = (selector, value) => {const input = section.querySelector(selector); input.value = value; input.dispatchEvent(new Event('change', {bubbles: true}));};
    `);
    assert.equal(await evaluate("section.querySelectorAll('.temas-activity').length"), 2);
    assert.equal(await evaluate("section.textContent.includes('${')"), false);
    await evaluate("set('.lokasi-select-temas', 'Jakarta')");
    assert.equal(await evaluate("[...section.querySelector('.type-select-temas').options].some(option => option.value === '1')"), true);
    assert.equal(await evaluate("[...section.querySelector('.type-select-temas').options].some(option => option.value === '2')"), false);
    await evaluate("set('.lokasi-select-temas', 'Batam')");
    assert.equal(await evaluate("[...section.querySelector('.type-select-temas').options].some(option => option.value === '1')"), false);
    assert.equal(await evaluate("[...section.querySelector('.type-select-temas').options].some(option => option.value === '2')"), true);
    await evaluate("set('.lokasi-select-temas', 'Jakarta')");
    await evaluate("set('.kapal-select-temas', 'TEMAS 1')");
    await evaluate("set('.voyage-select-temas', 'V001'); set('.temas-payment-mode', 'dp'); set('.temas-dp-amount', '300000'); section.querySelector('.temas-dp-amount').dispatchEvent(new Event('input'))");
    assert.equal(await evaluate("document.getElementById('form').checkValidity()"), true);
    assert.equal(await evaluate("section.querySelector('.temas-cash-value').value"), '300000');
    assert.equal(await evaluate("new FormData(document.getElementById('form')).has('temas[1][types][]')"), false);
    await evaluate("set('.temas-payment-mode', 'pelunasan_dp')");
    await evaluate("set('.temas-dp-reference', '7')");
    await evaluate("set('.temas-bl-select', '01'); set('.type-select-temas', '1')");
    assert.equal(await evaluate("section.querySelector('.temas-cash-value').value"), '700000');
    assert.equal(await evaluate("section.querySelector('.grand-total-value-temas').value"), '1000000');
    assert.equal(await evaluate("document.getElementById('form').checkValidity()"), true);
    assert.equal(await evaluate("new FormData(document.getElementById('form')).get('temas[1][nomor_kontainers][]')"), 'TEMU001, TEMU002');
    assert.equal(await evaluate("new FormData(document.getElementById('form')).get('temas[1][nomor_bls][]')"), '01');
    assert.equal(await evaluate("new FormData(document.getElementById('form')).has('temas[1][pph_active]')"), false);
    await evaluate("set('.price-input-temas', '200000')");
    assert.equal(await evaluate("document.getElementById('form').checkValidity()"), false);
    await evaluate("set('.price-input-temas', '1000000'); set('.temas-payment-mode', 'lunas')");
    assert.equal(await evaluate("section.querySelector('.temas-cash-value').value"), '980000');
    await evaluate(`
        clearAllTemasSections();
        window.section = addTemasSection({kapal: 'TEMAS 1', voyage: 'V001', payment_mode: 'pelunasan_dp', dp_stage_id: 7, dp_diperhitungkan: 300000,
            types: [{type_id: 1, nomor_bl: '01', nomor_kontainer: 'TEMU001, TEMU002', bl_id: 9, size: '20ft', harga: 1000000, kuantitas: 1, lokasi: 'Jakarta', is_muat: true}]});
    `);
    assert.equal(await evaluate("section.querySelector('.temas-cash-value').value"), '700000');
    assert.equal(await evaluate("section.querySelector('.temas-activity').checked"), true);
    assert.equal(await evaluate("document.getElementById('form').checkValidity()"), true);
    assert.deepEqual(errors, []);
    console.log('PASS: rendered controls, DP without costs, final invoice minus DP once, FormData, validation, direct payment, edit hydration.');
    await call('Browser.close');
} finally {
    socket?.close(); browser.kill();
    const target = resolve(profile);
    if (!target.startsWith(tempRoot + sep) || !target.includes('aypsis-temas-browser-')) throw new Error('Unexpected temporary profile path');
    await rm(target, {recursive: true, force: true, maxRetries: 10, retryDelay: 200});
}
