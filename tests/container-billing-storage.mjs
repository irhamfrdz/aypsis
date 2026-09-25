import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
import vm from 'node:vm';

const read = path => readFileSync(path, 'utf8').replaceAll('\r\n', '\n');
const original = read('03/app.js');
const integrated = read('resources/container-billing/app.js');
const businessStart = 'function split(line)';
assert.equal(integrated.slice(integrated.indexOf(businessStart)).replace('  await finishStorageBoot();\n', '').replace('let m=selectMasterVersion(masterList,container,start)||{};', 'let m=masters[container]||{};'), original.slice(original.indexOf(businessStart)), 'Domain logic must remain identical except the documented undefined-master fix');
const stores = ['masters','rates','rentals','expected','invoices','detachedInvoices','payments','pranotas','oplog','settings','rentalOverrides'];
let revision = 0;
let state = Object.fromEntries(stores.map(store => [store, []]));
let writes = 0;
const context = vm.createContext({
  structuredClone, console, S: stores, invalidateCaches() {}, $: () => ({}), alert() {},
  window: {CBC_SERVER: {stateUrl: '/container-billing/state', csrf: 'test'}},
  fetch: async (url, options) => {
    if (options.method === 'GET') return {ok: true, json: async () => ({revision, data: structuredClone(state)})};
    const input = JSON.parse(options.body);
    if (input.revision !== revision) return {ok: false, status: 409, json: async () => ({message: 'Conflict'})};
    writes++;
    if (input.operation === 'replace') state = input.data;
    if (input.operation === 'put') {
      const map = new Map(state[input.store].map(row => [row.id, row]));
      for (const row of input.rows) map.set(row.id, row);
      state[input.store] = [...map.values()];
    }
    if (input.operation === 'delete') state[input.store] = state[input.store].filter(row => row.id !== input.id);
    if (input.operation === 'clear') state[input.store] = [];
    return {ok: true, json: async () => ({revision: ++revision})};
  }
});
vm.runInContext(read('resources/container-billing/storage-adapter.js'), context);
const run = source => vm.runInContext(source, context);
await run('openDB()');
await run('put("masters", {id: "B", vendor: "ZONA"})');
assert.equal(writes, 0, 'Startup changes must be committed together');
await run('finishStorageBoot()');
assert.equal(writes, 1);
await run('Promise.all([put("masters", {id:"A"}), put("masters", {id:"C"})])');
assert.equal(revision, 3, 'Concurrent writes must use sequential revisions');
assert.equal(JSON.stringify(await run('all("masters")')), JSON.stringify([{id:'A'},{id:'B',vendor:'ZONA'},{id:'C'}]));
await run('(async()=>{const rows=await all("masters");rows[0].id="MUTATED";})()');
assert.equal((await run('all("masters")'))[0].id, 'A', 'Reads must not mutate stored records');
await run('del("masters", "A")');
assert.equal(state.masters.length, 2);
revision++;
await assert.rejects(run('clear("masters")'), /Conflict/);
assert.equal(state.masters.length, 2, 'Conflict must preserve persisted data');
await assert.rejects(run('put("masters", {id:"D"})'), /Conflict/);
console.log('PASS: unchanged domain logic, atomic startup, serialized writes, read isolation, delete, conflict protection.');
