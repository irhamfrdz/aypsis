// Exercise the complete original startup/migrations with actual baseline assets.
// Minimal DOM stubs test startup behavior, not visual/browser rendering.
import {readFileSync} from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';
const stores = ['masters','rates','rentals','expected','invoices','detachedInvoices','payments','pranotas','oplog','settings','rentalOverrides'];
const elements = new Map();
const element = id => {
  if (!elements.has(id)) elements.set(id, {id, value:'', dataset:{}, style:{}, textContent:'', innerHTML:'', addEventListener(){}, classList:{add(){},remove(){},toggle(){}}, querySelectorAll(){return [];}});
  return elements.get(id);
};
let persisted;
let resolveBoot, rejectBoot;
const completed = new Promise((resolve, reject) => {resolveBoot = resolve; rejectBoot = reject;});
const storage = {getItem(){return null;},setItem(){},removeItem(){}};
const context = vm.createContext({
  console, structuredClone, setTimeout, clearTimeout, Intl, Date,
  localStorage:storage, sessionStorage:storage,
  window:{CBC_SERVER:{stateUrl:'/state',csrf:'test'}},
  document:{getElementById:element,querySelectorAll(){return [];},querySelector(){return {id:'dashboard'};},addEventListener(){}},
  alert(message){rejectBoot(new Error(message));},
  fetch:async (url, options) => {
    if (options.method === 'GET') return {ok:true,json:async()=>({revision:0,data:Object.fromEntries(stores.map(s=>[s,[]]))})};
    persisted = JSON.parse(options.body);
    resolveBoot();
    return {ok:true,json:async()=>({revision:1})};
  }
});
for (const file of ['baseline_data.js','siklus_reference.js','examples.js','app.js']) {
  vm.runInContext(readFileSync(`resources/container-billing/${file}`, 'utf8'), context, {filename:file});
}
const timeout = setTimeout(()=>rejectBoot(new Error('Startup timed out')),120000);
try {
  await completed;
  assert.equal(persisted.operation, 'replace');
  assert(persisted.data.invoices.length > 3000);
  assert(persisted.data.settings.some(row=>row.id === 'stateHistoryGuardVersion'));
  console.log(`PASS: full baseline startup; ${persisted.data.invoices.length} invoices, ${persisted.data.rentals.length} rentals; snapshot ${(Buffer.byteLength(JSON.stringify(persisted))/1024/1024).toFixed(2)} MiB.`);
} finally {clearTimeout(timeout);}
