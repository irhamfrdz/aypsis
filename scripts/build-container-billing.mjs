// Deterministic mechanical conversion. Run: node scripts/build-container-billing.mjs
import {readFileSync, writeFileSync, mkdirSync, copyFileSync} from 'node:fs';
import assert from 'node:assert/strict';

const read = path => readFileSync(path, 'utf8');
mkdirSync('resources/container-billing', {recursive: true});
mkdirSync('resources/views/container-billing', {recursive: true});
let app = read('03/app.js');
const start = app.indexOf('function openDB()');
const end = app.indexOf('function split(line)', start);
assert(start > 0 && end > start, 'Original storage boundary changed');
app = app.slice(0, start) + read('resources/container-billing/storage-adapter.js') + '\n\n' + app.slice(end);
const bootEnd = '  await renderDash();\n  if(visibleView()==="backup")';
app = app.replaceAll('\r\n', '\n');
assert(app.includes(bootEnd), 'Original initialization boundary changed');
app = app.replace(bootEnd, '  await finishStorageBoot();\n' + bootEnd);
// Source V7.1.38 loads masterList but refers to undeclared `masters` in this migration.
const missingMaster = 'let m=masters[container]||{};';
assert.equal(app.split(missingMaster).length, 2, 'Original master lookup changed');
app = app.replace(missingMaster, 'let m=selectMasterVersion(masterList,container,start)||{};');
writeFileSync('resources/container-billing/app.js', app);
for (const file of ['baseline_data.js', 'siklus_reference.js', 'examples.js']) {
  copyFileSync(`03/${file}`, `resources/container-billing/${file}`);
}
let html = read('03/index.html');
for (const file of ['baseline_data.js', 'siklus_reference.js', 'examples.js', 'app.js?v=7.1.38-final']) {
  html = html.replace(`src="${file}"`, `src="{{ route('container-billing.asset', ['file' => '${file.split('?')[0]}']) }}"`);
}
html = html.replace('<script src=', `<script>window.CBC_SERVER = @json(['stateUrl' => route('container-billing.state'), 'csrf' => csrf_token()]);</script><script src=`);
writeFileSync('resources/views/container-billing/index.blade.php', html);
console.log('Container Billing: original markup/styles and domain logic retained; Laravel storage adapter installed.');
