// Injected in place of the original IndexedDB helpers; domain/UI code is unchanged.
let serverRevision = 0;
let serverData = {};
let writeQueue = Promise.resolve();
let storageFailure = null;
let booting = true;
const cloneData = value => structuredClone(value);
async function storageRequest(method, payload) {
  const response = await fetch(window.CBC_SERVER.stateUrl, {
    method, credentials: "same-origin", cache: "no-store",
    headers: {"Accept": "application/json", "Content-Type": "application/json", "X-CSRF-TOKEN": window.CBC_SERVER.csrf},
    ...(payload ? {body: JSON.stringify(payload)} : {})
  });
  const result = await response.json().catch(() => ({}));
  if (!response.ok) throw new Error(result.message || `Database server gagal (${response.status}). Muat ulang halaman.`);
  return result;
}
async function openDB() {
  const state = await storageRequest("GET");
  serverRevision = state.revision;
  serverData = state.data;
}
function storageWrite(payload, apply) {
  if (storageFailure) return Promise.reject(storageFailure);
  if (booting) { apply(); invalidateCaches(); return Promise.resolve(); }
  const task = writeQueue.then(async () => {
    if (storageFailure) throw storageFailure;
    try {
      const result = await storageRequest("POST", {...payload, revision: serverRevision});
      serverRevision = result.revision;
      apply();
      invalidateCaches();
    } catch (error) {
      storageFailure = error;
      $("dbStatus").textContent = error.message;
      alert(error.message + " Perubahan yang gagal belum tersimpan. Muat ulang sebelum melanjutkan.");
      throw error;
    }
  });
  writeQueue = task.catch(() => {});
  return task;
}
async function finishStorageBoot() {
  booting = false;
  await storageWrite({operation: "replace", data: cloneData(serverData)}, () => {});
}
async function all(s) {
  await writeQueue;
  if (storageFailure) throw storageFailure;
  // IndexedDB getAll() returns primary-key order and fresh objects.
  return cloneData(serverData[s]).sort((a, b) => {
    if (typeof a.id !== typeof b.id) return typeof a.id === "number" ? -1 : 1;
    return a.id < b.id ? -1 : a.id > b.id ? 1 : 0;
  });
}
async function put(s, o) { await putMany(s, [o]); return o; }
async function putMany(s, rows = []) {
  if (!rows.length) return rows;
  const copy = cloneData(rows);
  await storageWrite({operation: "put", store: s, rows: copy}, () => {
    const records = new Map(serverData[s].map(row => [row.id, row]));
    for (const row of copy) records.set(row.id, row);
    serverData[s] = [...records.values()];
  });
  return rows;
}
async function clear(s) {
  await storageWrite({operation: "clear", store: s}, () => { serverData[s] = []; });
}
async function del(s, id) {
  await storageWrite({operation: "delete", store: s, id}, () => { serverData[s] = serverData[s].filter(row => row.id !== id); });
}
async function replace(d) {
  const data = Object.fromEntries(S.map(s => [s, cloneData(d[s] || [])]));
  await storageWrite({operation: "replace", data}, () => { serverData = data; });
}
async function seed() {
  if ((await getSetting("cleanRebuildMode")) === true) return;
  if ((await all("invoices")).length || (await all("masters")).length || (await all("rentals")).length) return;
  await replace(window.BASELINE_DATA || {});
}
