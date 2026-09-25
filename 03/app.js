(()=>{"use strict";
const APP_VERSION="V7.1.38 FINAL • DETAIL FINANCIAL EVIDENCE • MANUAL ADJUSTMENT RESOLUTION • STATE INTEGRITY";
window.CBC_RUNTIME_VERSION=APP_VERSION;
const DB="ContainerBillingControlFinal",VER=5,S=["masters","rates","rentals","expected","invoices","detachedInvoices","payments","pranotas","oplog","settings","rentalOverrides"];
let db;
let expectedPage=1, reviewPage=1, invoicePage=1, rentalPage=1;
let expectedCache=null, cyclesCache=null, invoiceHeadersCache=null, mastersCache=null,masterPage=1,identitySimulation=null;
let currentReviewMode="invoice";
let searchTimer=null;
function invalidateCaches(){expectedCache=null;cyclesCache=null;invoiceHeadersCache=null;mastersCache=null}
function visibleView(){return document.querySelector(".view.active")?.id||"dashboard"}
function pageSlice(rows,page,size){let pages=Math.max(1,Math.ceil(rows.length/size));page=Math.min(Math.max(1,page),pages);return {page,pages,total:rows.length,rows:rows.slice((page-1)*size,page*size)}}
function debounce(fn,ms=220){clearTimeout(searchTimer);searchTimer=setTimeout(fn,ms)}

const $=i=>document.getElementById(i);
const now=()=>new Date().toISOString();
const today=()=>new Date().toISOString().slice(0,10);
const norm=s=>String(s||"").toUpperCase().replace(/\s+/g,"").trim();
const uid=p=>p+"-"+Date.now()+"-"+Math.random().toString(36).slice(2,8);
const money=n=>new Intl.NumberFormat("id-ID",{style:"currency",currency:"IDR",maximumFractionDigits:0}).format(Number(n||0));
const esc=s=>String(s??"").replace(/[&<>"']/g,m=>({"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#39;"}[m]));
const MONTHS=["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
const MONTHMAP={JAN:1,FEB:2,MAR:3,APR:4,MEI:5,MAY:5,JUN:6,JUL:7,AGU:8,AUG:8,SEP:9,OKT:10,OCT:10,NOV:11,DES:12,DEC:12};

function badge(t){
  let c=t==="LAYAK DIBAYAR"||t==="READY TO PAY"||t==="APPROVED"||t==="DITERIMA"||t==="AKTIF"?"ok":
        t==="SUDAH DIBAYAR"||t==="SELESAI"||t==="SUDAH DITAGIH"?"info":
        t==="TERCATAT"?"info":t==="PENDING"||t==="OUTSTANDING"||t==="BELUM DITAGIH"||t==="BELUM DITEMUKAN"||t==="PERLU KONFIRMASI"?"pending":"bad";
  return `<span class="badge ${c}">${esc(t)}</span>`;
}
function openDB(){return new Promise((r,j)=>{let q=indexedDB.open(DB,VER);q.onupgradeneeded=e=>{let d=e.target.result;for(let s of S)if(!d.objectStoreNames.contains(s))d.createObjectStore(s,{keyPath:"id"})};q.onsuccess=()=>{db=q.result;r()};q.onerror=()=>j(q.error)})}
function st(s,m="readonly"){return db.transaction(s,m).objectStore(s)}
function all(s){return new Promise((r,j)=>{let q=st(s).getAll();q.onsuccess=()=>r(q.result);q.onerror=()=>j(q.error)})}
function put(s,o){invalidateCaches();return new Promise((r,j)=>{let q=st(s,"readwrite").put(o);q.onsuccess=()=>r(o);q.onerror=()=>j(q.error)})}
function putMany(s,rows=[]){if(!rows.length)return Promise.resolve(rows);invalidateCaches();return new Promise((r,j)=>{let tx=db.transaction(s,"readwrite"),os=tx.objectStore(s);for(let o of rows)os.put(o);tx.oncomplete=()=>r(rows);tx.onerror=()=>j(tx.error);tx.onabort=()=>j(tx.error||new Error("Transaksi batch dibatalkan"))})}
function clear(s){invalidateCaches();return new Promise((r,j)=>{let q=st(s,"readwrite").clear();q.onsuccess=()=>r();q.onerror=()=>j(q.error)})}
function del(s,id){invalidateCaches();return new Promise((r,j)=>{let q=st(s,"readwrite").delete(id);q.onsuccess=()=>r();q.onerror=()=>j(q.error)})}
async function replace(d){for(let s of S){await clear(s);for(let o of(d[s]||[]))await put(s,o)}}
async function seed(){if((await getSetting("cleanRebuildMode"))===true)return;if((await all("invoices")).length||(await all("masters")).length||(await all("rentals")).length)return;let b=window.BASELINE_DATA||{};for(let s of S)for(let o of(b[s]||[]))await put(s,o)}

function split(line){
  if(line.includes("|")) return line.split("|");
  if(line.includes("\t")) return line.split("\t"); // backward compatibility
  return [line];
}
function pipeLine(parts){return parts.map(v=>String(v??"").trim()).join("|")}
function parseDate(v){
  let s=String(v||"").trim().replace(/\s+/g," ");
  if(!s)return "";
  if(/^\d{4}-\d{2}-\d{2}$/.test(s))return s;
  let n=s.match(/^(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{2,4})$/);
  if(n){
    let y=+n[3]; if(y<100)y+=2000;
    return validISO(y,+n[2],+n[1]);
  }
  let m=s.match(/^(\d{1,2})\s+([A-Za-zÀ-ÿ]+)\s+(\d{2,4})$/);
  if(m){
    let mo=MONTHMAP[m[2].toUpperCase()];
    let y=+m[3]; if(y<100)y+=2000;
    if(mo)return validISO(y,mo,+m[1]);
  }
  if(/^\d+(\.\d+)?$/.test(s)){
    let x=Number(s); if(x>20000&&x<80000)return new Date(Date.UTC(1899,11,30)+Math.round(x)*86400000).toISOString().slice(0,10);
  }
  return "";
}
function validISO(y,m,d){
  let iso=`${y}-${String(m).padStart(2,"0")}-${String(d).padStart(2,"0")}`,x=new Date(iso+"T00:00:00Z");
  return x.getUTCFullYear()===y&&x.getUTCMonth()+1===m&&x.getUTCDate()===d?iso:"";
}
function fmtDate(iso){
  if(!iso)return "";
  let m=String(iso).match(/^(\d{4})-(\d{2})-(\d{2})$/); if(!m)return String(iso);
  return `${m[3]} ${MONTHS[+m[2]-1]} ${m[1].slice(-2)}`;
}
function amt(v){
  let s=String(v??"").trim();if(!s)return null;
  s=s.replace(/Rp/ig,"").replace(/\s/g,"");
  if(s.includes(".")&&s.includes(","))s=s.replace(/\./g,"").replace(",",".");
  else if(s.includes(","))s=s.replace(",",".");
  else s=s.replace(/[^\d.-]/g,"");
  let n=Number(s);return Number.isFinite(n)?n:null;
}
function serial(i){let[y,m,d]=i.split("-").map(Number);return Math.round((Date.UTC(y,m-1,d)-Date.UTC(1899,11,30))/86400000)}
function addDays(i,n){let d=new Date(i+"T00:00:00Z");d.setUTCDate(d.getUTCDate()+n);return d.toISOString().slice(0,10)}
function addMonths(i,n){let[y,m,d]=i.split("-").map(Number),x=new Date(Date.UTC(y,m-1+n,1)),last=new Date(Date.UTC(x.getUTCFullYear(),x.getUTCMonth()+1,0)).getUTCDate();return `${x.getUTCFullYear()}-${String(x.getUTCMonth()+1).padStart(2,"0")}-${String(Math.min(d,last)).padStart(2,"0")}`}
function monthLastDay(y,m){return new Date(Date.UTC(y,m,0)).getUTCDate()}
function nextBillingPeriodStart(start){
  let [y,m,d]=start.split("-").map(Number),ny=y,nm=m+1;
  if(nm===13){nm=1;ny++}
  let last=monthLastDay(ny,nm);

  // Aturan rollover Februari:
  // jika tanggal anniversary tidak tersedia di bulan berikutnya (contoh 29 Jan -> Feb non-kabisat),
  // periode berjalan ditutup pada akhir Februari dan periode berikutnya mulai 1 Maret.
  if(nm===2 && d>last){
    return `${ny}-03-01`;
  }

  // Setelah rollover ke tanggal 1, siklus mengikuti bulan kalender.
  if(d===1){
    return `${ny}-${String(nm).padStart(2,"0")}-01`;
  }

  return `${ny}-${String(nm).padStart(2,"0")}-${String(Math.min(d,last)).padStart(2,"0")}`;
}
function buildBillingPeriods(start,until,maxPeriods=240){
  let result=[],ps=start,p=1;
  while(ps && p<=maxPeriods && (!until || ps<=until)){
    let next=nextBillingPeriodStart(ps),pe=addDays(next,-1);
    if(until && pe>until)pe=until;
    result.push({period:p,startDate:ps,endDate:pe});
    if(until && pe===until)break;
    ps=next;p++;
  }
  return result;
}
function supervisorPeriodSelfTest(){
  let p=buildBillingPeriods("2024-08-29","2025-06-27",20);
  let expected=[[1,"2024-08-29","2024-09-28"],[2,"2024-09-29","2024-10-28"],[3,"2024-10-29","2024-11-28"],[4,"2024-11-29","2024-12-28"],[5,"2024-12-29","2025-01-28"],[6,"2025-01-29","2025-02-28"],[7,"2025-03-01","2025-03-31"],[8,"2025-04-01","2025-04-30"],[9,"2025-05-01","2025-05-31"],[10,"2025-06-01","2025-06-27"]];
  let ok=expected.every((e,i)=>p[i]&&p[i].period===e[0]&&p[i].startDate===e[1]&&p[i].endDate===e[2]);
  if(!ok)console.error("SUPERVISOR PERIOD TEST FAILED",p);
  return ok;
}

function days(a,b){return Math.floor((Date.parse(b+"T00:00:00Z")-Date.parse(a+"T00:00:00Z"))/86400000)+1}
function div(i){let y=+i.slice(0,4),m=+i.slice(5,7);return m!==2?30:((y%4===0&&y%100!==0)||y%400===0?29:28)}

function show(id){document.querySelectorAll(".view").forEach(v=>v.classList.toggle("active",v.id===id));document.querySelectorAll("#nav button").forEach(b=>b.classList.toggle("active",b.dataset.view===id))}
document.querySelectorAll("#nav button").forEach(b=>b.addEventListener("click",async()=>{
  show(b.dataset.view);
  if(b.dataset.view==="dashboard")await renderDash();
  else if(b.dataset.view==="reports")await renderReports();
  else if(b.dataset.view==="expected")await renderExp();
  else if(b.dataset.view==="invoices")await renderInv();
  else if(b.dataset.view==="reviewcenter")await openReviewCenterHome();
  else if(b.dataset.view==="rentals")await renderRent();
  else if(b.dataset.view==="audit")await renderAudit();
  else if(b.dataset.view==="payments")await renderPay();
  else if(b.dataset.view==="pranotas")await renderPranota();
  else if(b.dataset.view==="master"){await renderMaster();await renderRates();}
  else if(b.dataset.view==="invoiceentry")await renderEntryExisting();
  else if(b.dataset.view==="backup")await renderInfo();
}));
document.querySelectorAll("[data-op]").forEach(b=>b.addEventListener("click",()=>{document.querySelectorAll("[data-op]").forEach(x=>x.classList.toggle("active",x===b));document.querySelectorAll(".tabpane").forEach(p=>p.classList.toggle("active",p.id==="op-"+b.dataset.op))}));

function masterVersionId(container,vendor,validFrom){
  return `M|${norm(container)}|${norm(vendor)||"NOVENDOR"}|${validFrom||"LEGACY"}`;
}
function masterEffective(m,date=""){
  if(!m)return false;
  if(date){
    if(m.validFrom&&date<m.validFrom)return false;
    if(m.validTo&&date>m.validTo)return false;
    return true; // historical lookup may use an inactive version inside its validity period
  }
  return m.isActive!==false&&!m.validTo;
}
function selectMasterVersion(list,container,date="",vendor=""){
  let c=norm(container),v=String(vendor||"").trim().toUpperCase(),
      rows=list.filter(x=>x.container===c);
  if(v)rows=rows.filter(x=>String(x.vendor||"").trim().toUpperCase()===v);
  if(!rows.length)return null;
  let valid=rows.filter(x=>masterEffective(x,date));
  if(!valid.length&&date)valid=rows.filter(x=>!x.validFrom||x.validFrom<=date);
  if(!valid.length)valid=rows.filter(x=>x.isActive!==false);
  if(!valid.length)valid=rows;
  valid.sort((a,b)=>(b.validFrom||"").localeCompare(a.validFrom||"")||(b.updatedAt||b.createdAt||"").localeCompare(a.updatedAt||a.createdAt||""));
  return valid[0]||null;
}
async function master(c,date="",vendor=""){return selectMasterVersion(await all("masters"),norm(c),date,vendor)}
async function ensureMaster(c,date=""){
  let allm=await all("masters"),m=selectMasterVersion(allm,c,date);
  if(m&&masterEffective(m,date))return m;
  let existing=allm.filter(x=>x.container===norm(c));
  if(existing.length){
    // Existing container but no valid version: keep transaction traceable without borrowing wrong vendor.
    return {id:"",container:norm(c),size:"",type:"",vendor:"",note:"Tidak ada versi master yang berlaku pada tanggal transaksi.",isActive:false,masterMissingVersion:true};
  }
  m={id:norm(c),container:norm(c),size:"",type:"",vendor:"",validFrom:"",validTo:"",isActive:true,note:"Dibuat otomatis saat import.",createdAt:now()};
  await put("masters",m);return m;
}
async function masterPaidImpact(m){
  let rentals=await all("rentals"),invoices=await all("invoices"),
      cycles=rentals.filter(r=>{
        if(r.masterId&&r.masterId===m.id)return true;
        if(r.container!==m.container)return false;
        if(m.vendor&&r.vendor&&String(r.vendor).toUpperCase()!==String(m.vendor).toUpperCase())return false;
        return (!m.validFrom||r.startDate>=m.validFrom)&&(!m.validTo||r.startDate<=m.validTo);
      }),
      ids=new Set(cycles.map(r=>r.id));
  return invoices.filter(x=>x.paymentStatus==="SUDAH DIBAYAR"&&ids.has(invoiceCycleId(x)));
}
async function syncMasterToRentals(c,m){
  // Master versioning: only cycles already linked to THIS master may inherit non-identity metadata.
  // Historical cycles from another vendor/version are never rewritten.
  let rs=(await all("rentals")).filter(r=>r.masterId===m.id&&r.container===c),
      inv=await all("invoices");
  for(let r of rs){
    let paid=inv.some(x=>x.paymentStatus==="SUDAH DIBAYAR"&&invoiceCycleId(x)===r.id);
    if(paid)continue;
    r.size=m.size||r.size||"";r.type=m.type||r.type||"";r.masterUpdatedAt=now();await put("rentals",r);
  }
}
async function migrateMasterVersions(){
  let ver=await getSetting("masterVersionModel");
  if(ver==="V1")return;
  let ms=await all("masters");
  for(let m of ms){
    let changed=false;
    if(m.isActive===undefined){m.isActive=true;changed=true}
    if(m.validFrom===undefined){m.validFrom="";changed=true}
    if(m.validTo===undefined){m.validTo="";changed=true}
    if(changed)await put("masters",m);
  }
  // Link existing rental history to the most plausible master version without changing vendor/history.
  ms=await all("masters");
  for(let r of await all("rentals")){
    if(r.masterId)continue;
    let m=selectMasterVersion(ms,r.container,r.startDate,r.vendor||"")||selectMasterVersion(ms,r.container,r.startDate);
    if(m){r.masterId=m.id;r.masterLinkedAt=now();await put("rentals",r)}
  }
  await setSetting("masterVersionModel","V1");
}
function auditLog(type,ref,date,status,reason,raw){
  return {id:uid("LOG"),type,container:ref||"",date:date||"",status,reason:reason||"",raw:raw||"",importedAt:now()};
}

async function getSetting(id){return (await all("settings")).find(x=>x.id===id)?.value||""}
async function setSetting(id,value){await put("settings",{id,value,updatedAt:now()})}
async function vendorCredits(){let v=await getSetting("vendorCreditsV1");return Array.isArray(v)?v:[]}
async function creditApplications(){let v=await getSetting("invoiceCreditApplicationsV1");return v&&typeof v==="object"&&!Array.isArray(v)?v:{}}
function invoiceCreditKey(vendor,invoiceNo){return `${String(vendor||"").toUpperCase()}|${String(invoiceNo||"").toUpperCase()}`}
function isSupersededDetail(x){return x?.superseded?.status==="DIGANTI"}
function effectiveDetailProblems(x){
  if(isSupersededDetail(x))return [];
  // Workflow downstream adalah bukti bahwa detail sudah melewati gate. Perubahan material
  // wajib membatalkan Approval/Pranota terlebih dahulu pada fungsi koreksi, bukan lewat refresh.
  if(x.paymentStatus==="SUDAH DIBAYAR"||x.pranotaNo||x.approvalStatus==="APPROVED")return [];
  let p=(x.problems||[]).filter(z=>z!=="PENDING SATU TAGIHAN: ADA DETAIL LAIN BERMASALAH"&&z!=="USER MENETAPKAN: TIDAK BERHAK DITAGIH");
  if(x.creditResolution?.status==="RESOLVED")p=p.filter(z=>!/NOMINAL BERBEDA|SELISIH NOMINAL|NOMINAL.*EXPECTED/i.test(String(z)));
  // Koreksi Nilai adalah keputusan user untuk menyelesaikan PR finansial. Raw problem tetap
  // disimpan sebagai audit evidence, tetapi mismatch nominal tidak lagi menjadi blocker setelah
  // adjustment MANUAL tersimpan dengan keterangan. Masalah struktural tetap aktif.
  if(x.adjustmentSource==="MANUAL"&&String(x.adjustmentNote||"").trim())p=p.filter(z=>!/NOMINAL BERBEDA|SELISIH NOMINAL|NOMINAL.*EXPECTED/i.test(String(z)));
  // Keputusan user persisten: kalkulasi ulang boleh memperbarui evidence, tetapi tidak boleh
  // membuka kembali jenis masalah yang sudah diputuskan user tanpa perubahan material.
  if(x.claimDecision==="TIDAK_BERHAK_DITAGIH")p=p.filter(z=>!/TIDAK BERHAK|HAK TAGIH|TIDAK MEMILIKI HAK TAGIH|EXPECTED.*TIDAK ADA|PERIODE VENDOR/i.test(String(z)));
  if(x.claimDecision==="DITOLAK_USER"||x.duplicateDecision==="BUKAN_DUPLIKAT"||x.reviewDecision==="NOT_DUPLICATE")p=p.filter(z=>!duplicateProblemsOnly(z));
  return p;
}
function hasPersistentUserWork(x){return !!(isSupersededDetail(x)||x.reviewDecision||x.claimDecision||x.duplicateDecision||x.creditResolution?.status==="RESOLVED"||x.adjustmentSource==="MANUAL"||x.manualCycleConfirmed===true||x.approvalStatus==="APPROVED"||x.pranotaNo||x.paymentStatus==="SUDAH DIBAYAR")}
function isWorkflowReviewRow(x){return !x?.legacy||hasPersistentUserWork(x)}
function detailProblems(x){return effectiveDetailProblems(x)}
function detailProblemLabel(x){if(isSupersededDetail(x))return "DIGANTI / TIDAK DIHITUNG";let p=detailProblems(x);if(!invoiceCycleId(x))return "CYCLE BELUM TERHUBUNG";if(String(x.claimStatus||"").startsWith("NO_ENTITLEMENT")&&x.claimDecision!=="TIDAK_BERHAK_DITAGIH")return "TIDAK BERHAK / EXPECTED TIDAK ADA";if(p.length&&!x.resolved)return p.join(" • ");return ""}
let pendingVendorCredit=null;
async function recordVendorCredit(invoiceId){
  let rows=await all("invoices"),x=rows.find(z=>String(z.id)===String(invoiceId));if(!x)return alert("Detail tagihan tidak ditemukan.");
  let masters=await all("masters"),vendor=effectiveVendorCleanup(x,masters)||x.vendor||"",cycles=Object.fromEntries((await legacyCycles()).map(c=>[c.id,c])),cy=cycles[invoiceCycleId(x)];
  let sys=Number(x.systemExpectedAmount??x.amount??0)+Number(x.adjustment||0),suggest=Math.max(0,Number(x.amount||0)-sys);
  let rentRange=cy?`${fmtDate(cy.startDate)} - ${fmtDate(cy.returnDate)||"AKTIF"}`:"-", vendorRange=`${x.migrationStartDate?fmtDate(x.migrationStartDate):"-"} - ${x.migrationEndDate?fmtDate(x.migrationEndDate):"-"}`;
  let def=`Kelebihan bayar No. Tagihan ${x.invoiceNo||"-"} tanggal ${fmtDate(x.invoiceDate)||"-"} | Container ${x.container||"-"} | Sewa ${rentRange} | Periode Tagihan ${vendorRange}`;
  pendingVendorCredit={x,vendor,amount:Math.round(suggest),note:def,cycleId:invoiceCycleId(x)};
  $("creditVendorName").value=vendor;$("creditInvoiceNo").value=x.invoiceNo||"";$("creditAmount").value=Math.round(suggest);$("creditNote").value=def;$("creditDialog").showModal();
}
async function saveVendorCredit(){
  if(!pendingVendorCredit)return;let {x,vendor,cycleId}=pendingVendorCredit,amount=Number(String($("creditAmount").value).replace(/[^0-9.-]/g,"")),note=$("creditNote").value.trim();
  if(!(amount>0))return alert("Nilai kredit harus lebih dari 0.");if(!note)return alert("Keterangan kredit wajib diisi.");
  if(!invoiceCycleId(x))return alert("Kredit belum dapat menyelesaikan detail karena Cycle belum terhubung. Pilih/Ganti Cycle terlebih dahulu.");
  let cs=await vendorCredits(),creditId=uid("CRD");cs.push({id:creditId,vendor,sourceInvoiceNo:x.invoiceNo,sourceInvoiceDate:x.invoiceDate||"",container:x.container,cycleId,amount,balance:amount,note,createdAt:now(),status:"OPEN"});await setSetting("vendorCreditsV1",cs);
  x.creditResolution={status:"RESOLVED",creditId,amount,note,resolvedAt:now(),type:"KELEBIHAN_BAYAR_VENDOR"};x.resolved=effectiveDetailProblems({...x,creditResolution:{status:"RESOLVED"}}).length===0;x.approvalStatus="BELUM APPROVAL";x.approvedAt="";await put("invoices",x);
  await put("oplog",auditLog("KREDIT VENDOR",x.invoiceNo,today(),"DITERIMA",`${vendor} • ${x.container} • ${money(amount)} • ${note} • DETAIL ${x.resolved?"SELESAI - KREDIT VENDOR":"MASIH ADA PR STRUKTURAL"}`,"KELEBIHAN BAYAR HISTORIS"));
  await applyInvoiceGroupStatus();invalidateCaches();
  $("creditDialog").close();pendingVendorCredit=null;let h=(await invoiceHeaders()).find(z=>z.invoiceNo===x.invoiceNo&&String(z.vendor||"").toUpperCase()===String(vendor||"").toUpperCase());alert(`Kredit vendor ${vendor} tersimpan ${money(amount)}.\nStatus detail: ${x.resolved?"SELESAI - KREDIT VENDOR":"masih ada masalah struktural"}.\n${h&&h.status==="READY TO PAY"?"No. Tagihan siap untuk Approval.":"No. Tagihan masih memiliki masalah lain."}`);await renderInv();if(visibleView()==="reviewcenter")await renderScopedReview(x.invoiceNo,x.container);
}
async function applyVendorCredit(invoiceNo,vendor){
  let hs=await invoiceHeaders(),h=hs.find(z=>z.invoiceNo===invoiceNo&&z.vendor===vendor);if(!h)return;
  if(h.approvalStatus==="APPROVED"||h.pranotaNos?.length||h.status==="SUDAH DIBAYAR")return alert("Kredit tidak dapat diubah setelah Approval/Pranota/Payment. Batalkan proses downstream terlebih dahulu.");
  let cs=await vendorCredits(),avail=cs.filter(c=>String(c.vendor||"").toUpperCase()===String(vendor||"").toUpperCase()&&Number(c.balance||0)>0),total=avail.reduce((a,c)=>a+Number(c.balance||0),0);if(total<=0)return alert(`Tidak ada saldo kredit terbuka untuk ${vendor}.`);
  let max=Math.max(0,Number((h.dppBeforeCredit??h.dpp)||0)),raw=prompt(`Saldo kredit ${vendor}: ${money(total)}\nDPP sebelum kredit: ${money(max)}\n\nNilai kredit DPP yang dipakai:`,String(Math.round(Math.min(total,max))));if(raw===null)return;
  let amount=Number(String(raw).replace(/[^0-9.-]/g,""));if(!(amount>=0)||amount>total+0.01||amount>max+0.01)return alert("Nilai kredit melebihi saldo kredit atau DPP tagihan.");
  let apps=await creditApplications(),key=invoiceCreditKey(vendor,invoiceNo),old=apps[key];if(old){for(let u of (old.uses||[])){let c=cs.find(z=>z.id===u.id);if(c){c.balance=Number(c.balance||0)+Number(u.amount||0);c.status="OPEN"}}}
  let rem=amount,uses=[];for(let c of cs.filter(c=>String(c.vendor||"").toUpperCase()===String(vendor||"").toUpperCase()&&Number(c.balance||0)>0)){if(rem<=0)break;let take=Math.min(rem,Number(c.balance||0));if(take>0){c.balance-=take;uses.push({id:c.id,amount:take});rem-=take;c.status=c.balance>0?"OPEN":"USED"}}
  apps[key]={amount,uses,updatedAt:now(),basis:"DPP"};await setSetting("vendorCreditsV1",cs);await setSetting("invoiceCreditApplicationsV1",apps);invalidateCaches();await put("oplog",auditLog("PAKAI KREDIT VENDOR",invoiceNo,today(),"DITERIMA",`${vendor} • DPP dikurangi ${money(amount)}`,JSON.stringify(uses)));await renderInv();
}
async function getOverrides(){return Object.fromEntries((await all("rentalOverrides")).map(x=>[x.id,x]))}
function effectiveRental(r,overrides){
  let o=overrides[r.id];
  return o?{...r,startDate:o.startDate||r.startDate,returnDate:o.returnDate!==undefined?o.returnDate:r.returnDate,correctionReason:o.reason||"",corrected:true}:r;
}

function canonicalCycleId(container,startDate){return norm(container)+serial(startDate)}
function invoiceEffectiveStart(x){return x.correctedStartDate||x.systemStartDate||x.startDate||""}
function invoiceEffectiveEnd(x){return x.correctedEndDate||x.systemEndDate||x.endDate||""}
function dayDistance(a,b){if(!a||!b)return 999999;return Math.abs(Math.round((Date.parse(a+"T00:00:00Z")-Date.parse(b+"T00:00:00Z"))/86400000))}
function cycleStatus(r){
  if(r.returnDate)return "SELESAI";
  if(r.inferredEnd)return "CLOSED (HISTORI)";
  if(r.source==="LEGACY_MIGRATION"||r.source==="SIKLUS_REFERENCE")return "PERLU KONFIRMASI";
  return "AKTIF";
}


// Siklus.xlsx adalah referensi operasional histori:
// Container + TGL SEWA = identitas cycle, Tgl Kembali = closing reference.
// Data finansial dari kontainer.xlsx tetap berada di invoices/payments dan tidak ditimpa.
async function reconcileSiklusReference(force=false){
  let pack=window.SIKLUS_REFERENCE||{},refs=Array.isArray(pack)?pack:(pack.rows||[]);
  if(!refs.length)return{created:0,updated:0,mapped:0,conflicts:0,unmatched:0,skipped:true};

  let refVersion=String(pack.version||1),
      already=await getSetting("siklusReferenceVersion");
  if(!force&&already===refVersion)return{created:0,updated:0,mapped:0,conflicts:0,unmatched:0,skipped:true};

  invalidateCaches();
  let rentals=await all("rentals"),invoices=await all("invoices"),
      masterList=await all("masters"),
      created=0,updated=0,mapped=0,conflicts=0,unmatched=0,
      refByContainer={};

  for(let ref of refs){
    let container=norm(ref.container),start=ref.startDate||"",ret=ref.returnDate||"";
    if(!container||!start)continue;
    (refByContainer[container]??=[]).push({...ref,container,startDate:start,returnDate:ret});
    let id=canonicalCycleId(container,start),
        r=rentals.find(x=>x.id===id)||rentals.find(x=>x.container===container&&x.startDate===start),
        m=selectMasterVersion(masterList,container,start,ref.vendor||"")||selectMasterVersion(masterList,container,start)||{};

    if(!r){
      r={
        id,container,startDate:start,returnDate:"",
        basis:"HISTORI",rate:0,monthlyRate:0,dailyRate:0,
        size:m.size||"",type:m.type||"",vendor:ref.vendor||m.vendor||"",
        source:"SIKLUS_REFERENCE",cycleFlag:"TAKE",legacy:true,
        referenceNo:ref.no||"",referenceReturnDate:ret,
        referenceSource:"Siklus.xlsx",createdAt:now()
      };
      await put("rentals",r);rentals.push(r);created++;
    }else{
      let changed=false;
      if(!r.vendor&&ref.vendor){r.vendor=ref.vendor;changed=true}
      if(r.source==="LEGACY_MIGRATION"){r.source="SIKLUS_REFERENCE";changed=true}
      r.referenceNo=ref.no||r.referenceNo||"";
      r.referenceReturnDate=ret;
      r.referenceSource="Siklus.xlsx";
      r.cycleFlag="TAKE";
      r.referenceMatched=true;
      if(changed){await put("rentals",r);updated++}
      else await put("rentals",r);
    }

    // Vendor referensi hanya mengisi Master yang kosong, tidak menimpa Master yang sudah ada.
    let masterRow=selectMasterVersion(masterList,container,start,ref.vendor||"")||selectMasterVersion(masterList,container,start);
    if(masterRow&&!masterRow.vendor&&ref.vendor){
      masterRow.vendor=ref.vendor;
      masterRow.note=(masterRow.note?masterRow.note+" • ":"")+"Vendor dilengkapi dari Siklus.xlsx";
      await put("masters",masterRow);
    }
  }
  for(let arr of Object.values(refByContainer))arr.sort((a,b)=>a.startDate.localeCompare(b.startDate));

  // Mapping detail transaksi finansial ke cycle referensi.
  // Hanya match yang masuk akal (score <= 60); data lama yang tidak ada di Siklus.xlsx tetap memakai fallback lama.
  for(let x of invoices){
    let arr=refByContainer[x.container]||[];
    if(!arr.length){unmatched++;continue}
    let p=Number(x.period||0);if(!p){unmatched++;continue}
    let rowStart=invoiceEffectiveStart(x),rowEnd=invoiceEffectiveEnd(x),candidates=[];

    for(let ref of arr){
      let schedule=buildBillingPeriods(ref.startDate,ref.returnDate||"",240),
          slot=schedule[p-1];
      if(!slot)continue;
      let score=(rowStart?dayDistance(rowStart,slot.startDate)*4:dayDistance(x.rentalStartEstimated,ref.startDate))+
                (rowEnd?dayDistance(rowEnd,slot.endDate):0);
      candidates.push({ref,slot,score});
    }
    candidates.sort((a,b)=>a.score-b.score||a.ref.startDate.localeCompare(b.ref.startDate));
    let best=candidates[0];
    if(!best||best.score>60){unmatched++;continue}

    let cid=canonicalCycleId(x.container,best.ref.startDate),
        changed=x.canonicalCycleId!==cid||x.cycleModel!=="SIKLUS_REFERENCE_V1";
    x.canonicalCycleId=cid;
    x.rentalId=cid;
    x.cycleStartDate=best.ref.startDate;
    x.cycleModel="SIKLUS_REFERENCE_V1";
    x.matchMethod="SIKLUS_REFERENCE_PERIOD";
    x.referenceCycleNo=best.ref.no||"";
    x.referenceReturnDate=best.ref.returnDate||"";

    // Data PAID immutable: jangan mengubah tanggal efektif transaksi yang sudah dibayar.
    // Untuk unpaid, tanggal sistem mengikuti cycle referensi supaya error Excel lama tidak menggeser periode.
    if(x.paymentStatus!=="SUDAH DIBAYAR"){
      x.systemStartDate=best.slot.startDate;
      x.systemEndDate=best.slot.endDate;
    }
    if(changed){await put("invoices",x);mapped++}
    else await put("invoices",x);
  }

  // Isi Tgl Kembali authoritative setelah invoice sudah terpetakan.
  // Bila return reference akan memotong transaksi PAID, jangan ubah cycle secara diam-diam.
  rentals=await all("rentals");invoices=await all("invoices");
  for(let ref of refs){
    let container=norm(ref.container),start=ref.startDate||"",ret=ref.returnDate||"";
    if(!container||!start||!ret)continue;
    let id=canonicalCycleId(container,start),r=rentals.find(x=>x.id===id);
    if(!r)continue;

    // Import operasional/koreksi user lebih tinggi prioritasnya daripada file referensi.
    if(r.returnDate&&r.returnDate!==ret&&r.source!=="SIKLUS_REFERENCE"&&r.source!=="LEGACY_MIGRATION"){
      r.referenceConflict=`Referensi ${fmtDate(ret)} berbeda dari return operasional ${fmtDate(r.returnDate)}`;
      await put("rentals",r);conflicts++;continue;
    }

    let paid=invoices.filter(x=>invoiceCycleId(x)===id&&x.paymentStatus==="SUDAH DIBAYAR"),
        paidAffected=paid.filter(x=>{
          let s=x.startDate||"",e=x.endDate||"";
          return (s&&s>ret)||(e&&e>ret);
        });

    if(paidAffected.length){
      // Simpan bukti referensi, tetapi jangan ubah data cycle/payment yang sudah PAID.
      r.referenceReturnDate=ret;
      r.referenceConflict=`Tgl Kembali referensi ${fmtDate(ret)} berpotensi memengaruhi ${paidAffected.length} detail PAID`;
      await put("rentals",r);conflicts++;continue;
    }

    if(!r.returnDate){
      r.returnDate=ret;
      r.referenceReturnDate=ret;
      r.referenceConflict="";
      r.returnSource="SIKLUS.xlsx";
      r.updatedAt=now();
      await put("rentals",r);updated++;
    }
  }

  await setSetting("siklusReferenceVersion",refVersion);
  await setSetting("siklusReferenceSummary",JSON.stringify({created,updated,mapped,conflicts,unmatched,rows:refs.length}));
  invalidateCaches();
  return{created,updated,mapped,conflicts,unmatched,skipped:false};
}

// Migrasi histori: P1 = event pengambilan. Perubahan tanggal akibat Februari tidak pernah membuat siklus baru.
// ID estimasi Excel tetap dipertahankan di rentalIdEstimated sebagai bukti, sedangkan rentalId menjadi cycle canonical.
async function normalizeTakeBasedCycles(){
  let invoices=await all("invoices"),legacy=invoices.filter(x=>x.legacy),rentals=await all("rentals"),masterList=await all("masters");
  let byContainer={};for(let x of legacy)(byContainer[x.container]??=[]).push(x);
  let created=0,mapped=0;

  for(let [container,rows] of Object.entries(byContainer)){
    let existing=rentals.filter(r=>r.container===container),anchors=new Set(existing.map(r=>r.startDate).filter(Boolean));

    // Sumber utama siklus histori adalah P1, karena P1 merepresentasikan awal pengambilan.
    for(let x of rows.filter(z=>Number(z.period||0)===1)){
      let start=invoiceEffectiveStart(x)||x.rentalStartEstimated||"";
      if(!start)continue;
      let hasReference=existing.some(r=>r.source==="SIKLUS_REFERENCE"&&dayDistance(r.startDate,start)<=7);
      if(!hasReference)anchors.add(start);
    }
    // Histori bisa mulai dari P2/P3 karena P1 tidak ada di file. RentalStartEstimated boleh menjadi
    // take provisional hanya bila jauh dari cycle yang sudah ada dan konsisten dengan tanggal periodenya.
    let estCandidates=[...new Set(rows.map(x=>x.rentalStartEstimated).filter(Boolean))].sort();
    for(let est of estCandidates){
      if([...anchors].some(a=>dayDistance(a,est)<=45))continue; // contoh 29 Agu -> 01 Sep akibat rollover Februari = cycle yang sama
      let consistent=rows.filter(x=>x.rentalStartEstimated===est&&Number(x.period||0)>0).some(x=>{
        let p=Number(x.period),slot=buildBillingPeriods(est,null,p)[p-1],rs=invoiceEffectiveStart(x);
        return slot&&rs&&dayDistance(rs,slot.startDate)<=7;
      });
      if(consistent)anchors.add(est);
    }
    // Fallback terakhir jika seluruh histori benar-benar tidak memiliki P1 / rental estimate yang konsisten.
    if(!anchors.size){
      let candidates=rows.map(x=>x.rentalStartEstimated||invoiceEffectiveStart(x)).filter(Boolean).sort();
      if(candidates.length)anchors.add(candidates[0]);
    }

    for(let start of [...anchors].sort()){
      let id=canonicalCycleId(container,start);
      if(!rentals.some(r=>r.id===id)){
        let m=masters[container]||{};
        let rr={id,container,startDate:start,returnDate:"",basis:"HISTORI",rate:0,monthlyRate:0,dailyRate:0,size:m.size||"",type:m.type||"",vendor:m.vendor||"",masterId:m.id||"",source:"LEGACY_MIGRATION",cycleFlag:"TAKE",legacy:true,createdAt:now(),migrationNote:"Siklus histori dinormalisasi dari P1 / awal pengambilan"};
        await put("rentals",rr);rentals.push(rr);created++;
      }
    }

    let cycles=rentals.filter(r=>r.container===container).sort((a,b)=>a.startDate.localeCompare(b.startDate));
    for(let i=0;i<cycles.length;i++)cycles[i]._nextStart=cycles[i+1]?.startDate||"";

    for(let x of rows){
      let p=Number(x.period||0);if(!p)continue;
      // Mapping cycle yang sudah canonical/locked tetap dipakai.
      let locked=cycles.find(c=>c.id===(x.canonicalCycleId||x.rentalId)&&
        (x.cycleModel==="TAKE_BASED_V1"||x.cycleModel==="SIKLUS_REFERENCE_V1"));
      let chosen=locked||null;
      if(!chosen){
        let rowStart=invoiceEffectiveStart(x),rowEnd=invoiceEffectiveEnd(x),scores=[];
        for(let c of cycles){
          let limit=c.returnDate||(c._nextStart?addDays(c._nextStart,-1):today()),schedule=buildBillingPeriods(c.startDate,limit,240),slot=schedule[p-1];
          if(!slot)continue;
          let score=0;
          if(rowStart)score+=dayDistance(rowStart,slot.startDate)*4;
          else if(x.rentalStartEstimated)score+=dayDistance(x.rentalStartEstimated,c.startDate);
          if(rowEnd)score+=dayDistance(rowEnd,slot.endDate);
          // P1 harus selalu mengarah ke take date yang sama.
          if(p===1&&rowStart&&rowStart!==c.startDate)score+=10000;
          scores.push({c,slot,score});
        }
        scores.sort((a,b)=>a.score-b.score||a.c.startDate.localeCompare(b.c.startDate));
        if(scores.length)chosen=scores[0].c;
      }
      if(chosen){
        let changed=x.rentalId!==chosen.id||x.cycleModel!=="TAKE_BASED_V1";
        let referenceCycle=chosen.source==="SIKLUS_REFERENCE";
        x.rentalId=chosen.id;x.canonicalCycleId=chosen.id;x.cycleStartDate=chosen.startDate;
        x.cycleModel=referenceCycle?"SIKLUS_REFERENCE_V1":"TAKE_BASED_V1";
        x.matchMethod=referenceCycle?"SIKLUS_REFERENCE_PERIOD":"LEGACY_PERIOD_TO_TAKE_CYCLE";
        if(changed){await put("invoices",x);mapped++}
      }
    }
  }
  await setSetting("cycleModelVersion","SIKLUS_REFERENCE_V1+TAKE_BASED_V1");invalidateCaches();
  return {created,mapped};
}

function resetMasterForm(){
  $("masterForm").reset();$("mMasterId").value="";$("mContainer").readOnly=false;
  $("masterSaveBtn").textContent="Simpan Versi Master";
}
$("masterNewBtn").onclick=resetMasterForm;

$("masterForm").addEventListener("submit",async e=>{
  e.preventDefault();
  let c=norm($("mContainer").value),vendor=$("mVendor").value.trim(),
      from=parseDate($("mValidFrom").value),to=parseDate($("mValidTo").value),
      editId=$("mMasterId").value;
  if(!c)return;
  if(!vendor)return alert("Vendor wajib diisi.");
  if(!(await activeVendorMaster(vendor)))return alert("Vendor belum ada/aktif di Master Vendor. Tambahkan Vendor terlebih dahulu.");
  if($("mValidFrom").value.trim()&&!from)return alert("Berlaku Mulai tidak valid.");
  if($("mValidTo").value.trim()&&!to)return alert("Berlaku Sampai tidak valid.");
  if(from&&to&&to<from)return alert("Berlaku Sampai tidak boleh sebelum Berlaku Mulai.");

  let allm=await all("masters"),existing=editId?allm.find(x=>x.id===editId):null;
  if(existing){
    let paid=await masterPaidImpact(existing);
    let identityChanged=existing.container!==c||String(existing.vendor||"")!==vendor||existing.validFrom!==from||existing.validTo!==to;
    if(paid.length&&identityChanged){
      return alert(`Koreksi master diblokir: ada ${paid.length} detail SUDAH DIBAYAR.\nBatalkan pembayaran terlebih dahulu jika ingin mengubah identitas/vendor/masa berlaku master ini.`);
    }
  }else{
    let sameContainer=allm.filter(x=>x.container===c);
    if(sameContainer.length){
      if(!confirm(`SUSPECT MASTER: No. Container ${c} sudah ada (${sameContainer.length} versi).\n\nValidasi hanya berdasarkan No. Container; Vendor/Ukuran/Jenis diabaikan.\nTetap lanjut membuat versi master baru?`))return;
      if(!from)return alert("Untuk No. Container yang sudah ada, versi baru wajib mempunyai Berlaku Mulai agar histori tidak tertimpa.");
    }
    let overlap=allm.find(x=>x.container===c&&
      (!to||!x.validFrom||x.validFrom<=to)&&(!x.validTo||!from||x.validTo>=from));
    if(overlap)return alert(`Masa berlaku master overlap dengan versi yang sudah ada (${fmtDate(overlap.validFrom)||"awal"} – ${fmtDate(overlap.validTo)||"sekarang"}). Vendor tidak digunakan untuk melewati validasi ini.`);
  }

  let id=editId||masterVersionId(c,vendor,from);
  if(!editId&&(await all("masters")).some(x=>x.id===id))id=id+"|"+Date.now();
  let m={
    id,container:c,size:$("mSize").value.trim(),type:$("mType").value.trim(),vendor,
    validFrom:from,validTo:to,isActive:to?false:true,note:$("mNote").value.trim(),
    createdAt:existing?.createdAt||now(),updatedAt:now()
  };
  await put("masters",m);await syncMasterToRentals(c,m);
  await put("oplog",auditLog(editId?"MASTER":"VERSI MASTER",c,from||today(),"DITERIMA",editId?"Versi master diperbarui":"Versi master baru dibuat",pipeLine([c,m.size,m.type,m.vendor,m.validFrom,m.validTo,m.note])));
  resetMasterForm();await refresh();
});

function containerSerialKey(c){
  let m=norm(c).match(/(\d{7})$/);return m?m[1]:"";
}
function masterImportReasonHtml(rows){
  if(!rows.length)return "";
  let shown=rows.slice(0,30).map(r=>{
    let olds=[...(r.same||[]),...(r.sameSerial||[])];
    let compare=olds.length?`<div class="small" style="margin-top:6px"><b>SUDAH ADA:</b> ${olds.slice(0,3).map(x=>`${esc(x.container)} | ${esc(x.size||"-")} | ${esc(x.type||"-")} | ${esc(x.vendor||"-")} | ${fmtDate(x.validFrom)||"awal"} – ${fmtDate(x.validTo)||"seterusnya"} | ${x.isActive===false?"NONAKTIF":"AKTIF"}`).join("<br>")}<br><b>AKAN DIIMPOR:</b> ${esc(r.container||"")} | ${esc(r.size||"-")} | ${esc(r.type||"-")} | ${esc(r.vendor||"-")} | ${fmtDate(r.from)||"awal"} – ${fmtDate(r.to)||"seterusnya"}</div>`:"";
    return `<div class="rejectitem"><b>Baris ${r.line}: ${esc(r.kind||"REJECT")}</b><div>${esc(r.reason||"")}</div>${compare}<div class="small muted" style="margin-top:4px">RAW: ${esc(r.raw||"")}</div></div>`;
  }).join("");
  return `<div style="margin-top:8px"><b>Alasan baris bermasalah + perbandingan:</b>${shown}${rows.length>30?`<div class="small muted">+ ${rows.length-30} baris lain tetap berada di textarea.</div>`:""}</div>`;
}
$("processMaster").onclick=async()=>{
  let lines=$("masterText").value.split(/\r?\n/).filter(x=>x.trim());
  if(!lines.length)return alert("Tidak ada data Master Container.");
  let allm=await all("masters"),fresh=[],suspect=[],invalid=[];

  for(let i=0;i<lines.length;i++){
    let c=split(lines[i]).map(x=>x.trim()),container=norm(c[0]);
    if(!container){invalid.push({line:i+1,raw:lines[i],reason:"NO. CONTAINER KOSONG"});continue}
    let size=c[1]||"",type=c[2]||"",vendor=c[3]||"",from="",to="",note="";
    if(!vendor){invalid.push({line:i+1,raw:lines[i],reason:"VENDOR KOSONG"});continue}
    if(!(await activeVendorMaster(vendor))){invalid.push({line:i+1,raw:lines[i],reason:"VENDOR TIDAK ADA / NONAKTIF DI MASTER VENDOR"});continue}
    if(c.length>=7){from=parseDate(c[4]);to=parseDate(c[5]);note=c[6]||""}
    else if(c.length===6){from=parseDate(c[4]);to=parseDate(c[5])}
    else {note=c[4]||""}
    if(c.length>=6&&c[4]&&!from){invalid.push({line:i+1,raw:lines[i],reason:"BERLAKU MULAI TIDAK VALID"});continue}
    if(c.length>=6&&c[5]&&!to){invalid.push({line:i+1,raw:lines[i],reason:"BERLAKU SAMPAI TIDAK VALID"});continue}
    if(from&&to&&to<from){invalid.push({line:i+1,raw:lines[i],reason:"BERLAKU SAMPAI < BERLAKU MULAI"});continue}

    let same=allm.filter(x=>x.container===container), serialKey=containerSerialKey(container),
        sameSerial=serialKey?allm.filter(x=>x.container!==container&&containerSerialKey(x.container)===serialKey):[];
    let row={line:i+1,raw:lines[i],container,size,type,vendor,from,to,note,same,sameSerial};
    if(same.length){
      let vendorChanged=same.every(x=>norm(x.vendor)!==norm(vendor));
      if(vendorChanged){
        row.kind="SUSPECT VERSION";
        row.versionRequired=true;
        row.reason=`NO. CONTAINER SAMA tetapi Vendor berubah (${[...new Set(same.map(x=>x.vendor||"-"))].join(", ")} → ${vendor}). Isi BERLAKU SAMPAI versi lama dan BERLAKU MULAI versi baru melalui Input/Edit Satu Kontainer.`;
      }else{row.kind="SUSPECT DUPLIKAT";row.reason="NO. CONTAINER sudah ada di Master; review sebelum update."}
      suspect.push(row);
    }else if(sameSerial.length){
      row.kind="SUSPECT PREFIX";row.identitySuspect=true;
      row.reason=`Serial 7 digit sama ditemukan pada prefix lain (${[...new Set(sameSerial.map(x=>x.container))].join(", ")}). Jangan auto-merge. Tentukan apakah perubahan prefix/identitas atau container berbeda; bila versi identitas, isi BERLAKU SAMPAI lama dan BERLAKU MULAI baru.`;
      suspect.push(row);
    }else fresh.push(row);
  }

  let signature=suspect.map(x=>x.raw).join("\n");
  let secondPass=fresh.length===0&&invalid.length===0&&suspect.length>0&&
      $("masterText").dataset.suspectSignature===signature;

  if(secondPass){
    let manual=suspect.filter(x=>x.versionRequired||x.identitySuspect||x.same.length!==1);
    if(manual.length){
      $("masterImportMsg").style.display="block";
      $("masterImportMsg").innerHTML=`<b>${manual.length} SUSPECT wajib keputusan/versioning manual.</b> Tidak diupdate massal agar histori tidak tertimpa. Gunakan Input / Edit Satu Kontainer untuk mengisi BERLAKU SAMPAI versi lama dan BERLAKU MULAI versi baru bila memang container yang sama.`+masterImportReasonHtml(manual);
      return;
    }
    if(!confirm(`${suspect.length} No. Container sudah ada di Master.\\n\\nValidasi duplikat hanya berdasarkan No. Container; Vendor/Ukuran/Jenis tidak digunakan.\\n\\nKonfirmasi update Master Container yang sudah ada?`))return;

    let updated=0,blocked=[];
    for(let r of suspect){
      let old=r.same[0],paid=await masterPaidImpact(old);
      if(paid.length&&(String(old.vendor||"")!==r.vendor)){
        blocked.push({...r,reason:"VENDOR BERUBAH DAN MASTER TERKAIT TRANSAKSI SUDAH DIBAYAR"});continue;
      }
      old.size=r.size;old.type=r.type;old.vendor=r.vendor;old.note=r.note;
      if(r.from)old.validFrom=r.from;if(r.to)old.validTo=r.to;
      old.isActive=!old.validTo;old.updatedAt=now();
      await put("masters",old);await syncMasterToRentals(r.container,old);
      await put("oplog",auditLog("UPDATE MASTER CONTAINER",r.container,today(),"DITERIMA",
        "Konfirmasi user atas SUSPECT No. Container yang sudah ada",pipeLine([r.container,r.size,r.type,r.vendor,r.from,r.to,r.note])));
      updated++;
    }
    $("masterText").value=blocked.map(x=>x.raw).join("\n");
    delete $("masterText").dataset.suspectSignature;
    $("masterImportMsg").style.display="block";
    $("masterImportMsg").innerHTML=`Update terkonfirmasi <b>${updated}</b>. ${blocked.length?`Blokir <b>${blocked.length}</b> karena berdampak pada transaksi PAID; baris tetap di textarea.`:"Textarea sudah bersih."}`;
    await refresh();return;
  }

  // Klik pertama: Master baru langsung masuk. No. Container yang sudah ada tetap di textarea sebagai SUSPECT.
  let added=0;
  for(let r of fresh){
    let id=masterVersionId(r.container,r.vendor,r.from);
    if(allm.some(x=>x.id===id))id=id+"|"+Date.now();
    let m={id,container:r.container,size:r.size,type:r.type,vendor:r.vendor,validFrom:r.from,validTo:r.to,isActive:!r.to,note:r.note,createdAt:now(),updatedAt:now()};
    await put("masters",m);allm.push(m);added++;
    await put("oplog",auditLog("MASTER CONTAINER BARU",r.container,r.from||today(),"DITERIMA",
      "Impor Master Container baru",pipeLine([r.container,r.size,r.type,r.vendor,r.from,r.to,r.note])));
  }

  let remain=[...suspect,...invalid].sort((a,b)=>a.line-b.line);
  $("masterText").value=remain.map(x=>x.raw).join("\n");
  $("masterText").dataset.suspectSignature=invalid.length?"":suspect.map(x=>x.raw).join("\n");
  $("masterImportMsg").style.display="block";
  $("masterImportMsg").innerHTML=
    `<b>${added} Master Container baru otomatis disimpan.</b> `+
    (suspect.length?`<b>${suspect.length} SUSPECT No. Container sudah ada</b> dan tetap di textarea. Review lalu klik Proses Master lagi untuk konfirmasi update. `:"")+
    (invalid.length?`<b>${invalid.length} INVALID wajib diperbaiki.</b>`:"")+
    masterImportReasonHtml([...suspect,...invalid]);
  await refresh();
};
$("clearMasterText").onclick=()=>{$("masterText").value=""};


async function vendorMasterRows(){let v=await getSetting("vendorMasterV1");return Array.isArray(v)?v:[]}
function vendorCode(v){return String(v||"").trim().toUpperCase()}
async function activeVendorMaster(code){let c=vendorCode(code);return (await vendorMasterRows()).find(v=>vendorCode(v.code)===c&&v.isActive!==false)||null}
async function saveVendorMasterRows(rows){await setSetting("vendorMasterV1",rows);await segarkanDaftarMaster();}
async function renderVendorMaster(){
  let el=$("vendorMasterRows");if(!el)return;let rows=(await vendorMasterRows()).slice().sort((a,b)=>String(a.code).localeCompare(String(b.code)));
  el.innerHTML=rows.length?rows.map(v=>`<div class="rejectitem"><b>${esc(v.code)}</b> • ${esc(v.name||v.code)} ${badge(v.isActive!==false?"AKTIF":"NONAKTIF")} ${v.isActive!==false?`<button type="button" class="mini danger" data-vendor-disable="${esc(v.code)}">Nonaktifkan</button>`:""}</div>`).join(""):'<div class="rejectitem muted">Belum ada Master Vendor. Klik <b>Bangun dari Data Existing</b> agar vendor dari Master Container/Tarif menjadi kandidat.</div>';
  document.querySelectorAll('[data-vendor-disable]').forEach(b=>b.onclick=async()=>{let rows=await vendorMasterRows(),v=rows.find(x=>vendorCode(x.code)===vendorCode(b.dataset.vendorDisable));if(!v)return;if(!confirm(`Nonaktifkan Vendor ${v.code}? Histori lama tidak berubah.`))return;v.isActive=false;v.updatedAt=now();await saveVendorMasterRows(rows);await put("oplog",auditLog("MASTER VENDOR NONAKTIF",v.code,today(),"DITERIMA",v.name||v.code,""));await renderVendorMaster()});
}
async function upsertVendorMaster(code,name){code=vendorCode(code);name=String(name||code).trim();if(!code)return false;let rows=await vendorMasterRows(),v=rows.find(x=>vendorCode(x.code)===code);if(v){v.name=name||v.name||code;v.isActive=true;v.updatedAt=now()}else rows.push({id:`VENDOR|${code}`,code,name:name||code,isActive:true,createdAt:now()});await saveVendorMasterRows(rows);return true}
async function buildVendorMasterExisting(){let ms=await all("masters"),rs=await all("rates"),names=[...new Set([...ms.map(x=>x.vendor),...rs.map(x=>x.vendor)].map(v=>String(v||"").trim()).filter(Boolean))].sort(),rows=await vendorMasterRows(),added=0;for(let n of names){let c=vendorCode(n);if(!rows.some(x=>vendorCode(x.code)===c)){rows.push({id:`VENDOR|${c}`,code:c,name:n,isActive:true,source:"EXISTING",createdAt:now()});added++}}await saveVendorMasterRows(rows);await put("oplog",auditLog("MASTER VENDOR", "BUILD EXISTING",today(),"DITERIMA",`${added} vendor baru dari Master Container/Tarif`,""));$("vendorMasterMsg").innerHTML=`Ditemukan <b>${names.length}</b> vendor existing • ditambahkan <b>${added}</b>.`;await renderVendorMaster()}
if($("buildVendorMasterExisting"))$("buildVendorMasterExisting").onclick=buildVendorMasterExisting;
if($("saveVendorMaster"))$("saveVendorMaster").onclick=async()=>{let c=vendorCode($("vmCode").value),n=$("vmName").value.trim();if(!c)return alert("Kode Vendor wajib.");await upsertVendorMaster(c,n||c);await put("oplog",auditLog("MASTER VENDOR",c,today(),"DITERIMA",n||c,"MANUAL"));$("vmCode").value="";$("vmName").value="";await renderVendorMaster()};
if($("processVendorMaster"))$("processVendorMaster").onclick=async()=>{let lines=$("vendorMasterText").value.split(/\r?\n/).filter(x=>x.trim()),bad=[],ok=0;for(let raw of lines){let a=split(raw),c=vendorCode(a[0]),n=String(a[1]||a[0]||"").trim();if(!c){bad.push(raw);continue}await upsertVendorMaster(c,n);ok++;await put("oplog",auditLog("MASTER VENDOR",c,today(),"DITERIMA",n,raw))}$("vendorMasterText").value=bad.join("\n");$("vendorMasterMsg").innerHTML=`Diterima: <b>${ok}</b> • Reject: <b>${bad.length}</b>`;await renderVendorMaster()};

function rateId(vendor,size,type,from){return `RATE|${norm(vendor)}|${norm(size)}|${norm(type)}|${from||"LEGACY"}|${Date.now()}|${Math.random().toString(36).slice(2,7)}`}
function rateEffective(r,date){return !!r&&(!r.validFrom||date>=r.validFrom)&&(!r.validTo||date<=r.validTo)&&r.isActive!==false}
async function findRateForMaster(m,date){
  let rows=(await all("rates")).filter(r=>norm(r.vendor)===norm(m.vendor)&&norm(r.size)===norm(m.size)&&norm(r.type)===norm(m.type)&&rateEffective(r,date));
  if(rows.length!==1)return {rate:null,reason:rows.length?"MASTER TARIF OVERLAP / LEBIH DARI SATU VERSI BERLAKU":"MASTER TARIF TIDAK DITEMUKAN"};
  return {rate:rows[0],reason:""};
}
function parseRates(t){return t.split(/\r?\n/).map((raw,i)=>({raw,line:i+1,c:split(raw).map(x=>x.trim())})).filter(x=>x.raw.trim()).map(x=>({raw:x.raw,line:x.line,vendor:x.c[0]||"",size:x.c[1]||"",type:x.c[2]||"",monthly:amt(x.c[3]),daily:amt(x.c[4]),from:parseDate(x.c[5]),to:parseDate(x.c[6])}))}
function rateLine(r){return pipeLine([r.vendor,r.size,r.type,Number(r.monthlyRate)>0?r.monthlyRate:"",Number(r.dailyRate)>0?r.dailyRate:"",r.validFrom,r.validTo])}
async function renderRates(){
  let box=$("rateRows");if(!box)return;
  let rows=(await all("rates")).sort((a,b)=>(a.vendor+a.size+a.type+(a.validFrom||"")).localeCompare(b.vendor+b.size+b.type+(b.validFrom||"")));
  let activeCount=rows.filter(r=>r.isActive!==false).length, inactiveCount=rows.length-activeCount;
  if($("rateSummary")&&!String($("rateSummary").textContent||"").startsWith("Diterima:")) $("rateSummary").innerHTML=`Tersimpan: <b>${rows.length}</b> versi • Aktif: <b>${activeCount}</b> • Nonaktif: <b>${inactiveCount}</b>`;
  box.innerHTML=rows.length?rows.map(r=>{let active=r.isActive!==false;return `<div class="rejectitem"><b>${esc(r.vendor)} • ${esc(r.size)} • ${esc(r.type)}</b> ${badge(active?"AKTIF":"NONAKTIF")}<div>Bulanan ${Number(r.monthlyRate)>0?money(r.monthlyRate):"-"} • Harian ${Number(r.dailyRate)>0?money(r.dailyRate):"-"} • ${fmtDate(r.validFrom)} – ${r.validTo?fmtDate(r.validTo):"seterusnya"}</div><div class="actions" style="margin-top:6px"><button class="mini" data-rate-copy="${esc(r.id)}">Versi Baru / Koreksi</button>${active?` <button class="mini danger" data-rate-disable="${esc(r.id)}">Nonaktifkan</button>`:""}</div></div>`}).join(""):'<div class="rejectitem muted">Belum ada Master Tarif Vendor.</div>';
  document.querySelectorAll("[data-rate-copy]").forEach(b=>b.onclick=async()=>{let r=(await all("rates")).find(x=>x.id===b.dataset.rateCopy);if(!r)return;$("rateText").value=rateLine(r);$("rateText").focus();$("rateSummary").innerHTML='<b>Draft versi/koreksi dimuat ke textarea.</b> Ubah tarif/tanggal. Jika mengganti versi pada periode yang sama, nonaktifkan versi salah terlebih dahulu.';});
  document.querySelectorAll("[data-rate-disable]").forEach(b=>b.onclick=()=>disableRate(b.dataset.rateDisable));
}
async function disableRate(id){
  let r=(await all("rates")).find(x=>x.id===id);if(!r)return;
  let cycles=(await all("rentals")).filter(x=>x.rateMasterId===id),paidInv=await all("invoices"),paidCycles=new Set(paidInv.filter(x=>x.paymentStatus==="SUDAH DIBAYAR").map(invoiceCycleId));
  let paid=cycles.filter(x=>paidCycles.has(x.id));
  let msg=`Nonaktifkan Master Tarif ${r.vendor} • ${r.size} • ${r.type}?\nBulanan: ${Number(r.monthlyRate)>0?money(r.monthlyRate):"-"}\nHarian: ${Number(r.dailyRate)>0?money(r.dailyRate):"-"}\nBerlaku: ${fmtDate(r.validFrom)} – ${r.validTo?fmtDate(r.validTo):"seterusnya"}\n\nDipakai ${cycles.length} cycle; ${paid.length} cycle sudah PAID. Nonaktifkan TIDAK mengubah snapshot cycle lama.`;
  if(!confirm(msg))return;
  r.isActive=false;r.deactivatedAt=now();await put("rates",r);await put("oplog",auditLog("MASTER TARIF NONAKTIF",r.vendor,today(),"DITERIMA",`${r.size} ${r.type}; snapshot ${cycles.length} cycle tidak diubah`,r.id));await renderRates();await renderInfo();await renderHistoricalImportMode();
}
async function processRates(){
  let rows=parseRates($("rateText").value),existing=await all("rates"),rej=[],ok=0;
  for(let r of rows){
    let reason="",compare=null;
    if(!r.vendor||!r.size||!r.type)reason="VENDOR / UKURAN / JENIS WAJIB";
    else if(!(await activeVendorMaster(r.vendor)))reason="VENDOR TIDAK ADA / NONAKTIF DI MASTER VENDOR";
    else if(!r.from)reason="BERLAKU DARI WAJIB / TIDAK VALID";
    else if(r.to&&r.to<r.from)reason="BERLAKU SAMPAI < BERLAKU DARI";
    else if(!(r.monthly>0)&&!(r.daily>0))reason="ISI MINIMAL SALAH SATU: TARIF BULANAN ATAU TARIF HARIAN";
    else if(r.monthly!=null&&!(r.monthly>0))reason="TARIF BULANAN HARUS > 0 ATAU DIKOSONGKAN";
    else if(r.daily!=null&&!(r.daily>0))reason="TARIF HARIAN HARUS > 0 ATAU DIKOSONGKAN";
    let overlap=!reason&&existing.find(x=>x.isActive!==false&&norm(x.vendor)===norm(r.vendor)&&norm(x.size)===norm(r.size)&&norm(x.type)===norm(r.type)&&(!x.validTo||x.validTo>=r.from)&&(!r.to||!x.validFrom||x.validFrom<=r.to));
    if(overlap){reason="MASA BERLAKU MASTER TARIF OVERLAP DENGAN VERSI AKTIF. Nonaktifkan/akhiri versi lama dahulu.";compare=overlap}
    if(reason){rej.push({...r,reason,compare});continue}
    let o={id:rateId(r.vendor,r.size,r.type,r.from),vendor:r.vendor,size:r.size,type:r.type,monthlyRate:r.monthly==null?0:Number(r.monthly),dailyRate:r.daily==null?0:Number(r.daily),validFrom:r.from,validTo:r.to,isActive:true,createdAt:now()};
    await put("rates",o);existing.push(o);ok++;await put("oplog",auditLog("MASTER TARIF",r.vendor,r.from,"DITERIMA",`${r.size} ${r.type} • Bulanan ${Number(r.monthly)>0?money(r.monthly):"-"} • Harian ${Number(r.daily)>0?money(r.daily):"-"}`,r.raw));
  }
  $("rateText").value=rej.map(x=>x.raw).join("\n");
  $("rateRejects").innerHTML=rej.length?rej.map(x=>`<div class="rejectitem"><b>Baris ${x.line}: REJECT</b><div>${esc(x.reason)}</div>${x.compare?`<div class="small"><b>SUDAH ADA:</b> ${esc(x.compare.vendor)} | ${esc(x.compare.size)} | ${esc(x.compare.type)} | Bulanan ${Number(x.compare.monthlyRate)>0?money(x.compare.monthlyRate):"-"} | Harian ${Number(x.compare.dailyRate)>0?money(x.compare.dailyRate):"-"} | ${fmtDate(x.compare.validFrom)} – ${x.compare.validTo?fmtDate(x.compare.validTo):"seterusnya"}</div>`:""}<div class="small muted"><b>AKAN DIIMPOR:</b> ${esc(x.raw)}</div></div>`).join(""):'<div class="rejectitem muted">Tidak ada reject.</div>';
  $("rateSummary").innerHTML=`Diterima: <b>${ok}</b> • Reject: <b>${rej.length}</b>`;await renderRates();await renderInfo();await renderHistoricalImportMode();
}
function takes(t){return t.split(/\r?\n/).map((raw,i)=>({raw,line:i+1,c:split(raw).map(x=>x.trim())})).filter(x=>x.raw.trim()).map(x=>({raw:x.raw,line:x.line,container:norm(x.c[0]),date:parseDate(x.c[1]),method:norm(x.c[2])}))}
function returns(t){return t.split(/\r?\n/).map((raw,i)=>({raw,line:i+1,c:split(raw).map(x=>x.trim())})).filter(x=>x.raw.trim()).map(x=>({raw:x.raw,line:x.line,container:norm(x.c[0]),date:parseDate(x.c[1])}))}
function rejectBox(id,a){$(id).innerHTML=a.length?a.map(x=>`<div class="rejectitem"><b>Baris ${x.line}: REJECT</b><div>${esc(x.reason)}</div><div class="small muted">${esc(x.raw)}</div></div>`).join(""):'<div class="rejectitem muted">Tidak ada reject.</div>'}

async function processTake(){
  let rows=takes($("takeText").value),rent=await legacyCycles(),rej=[],ok=0,pending=0;
  let historyMode=(await getSetting("cleanRebuildMode"))===true;
  rows.sort((a,b)=>(a.container+a.date).localeCompare(b.container+b.date));
  for(let r of rows){
    let reason="",historyWarning="";
    if(!r.container)reason="KONTAINER KOSONG";
    else if(!r.date)reason="TANGGAL AMBIL TIDAK VALID";
    if(!reason){
      let same=rent.filter(x=>x.container===r.container).sort((a,b)=>a.startDate.localeCompare(b.startDate));
      if(same.find(x=>x.startDate===r.date))reason="DUPLIKAT: TANGGAL AMBIL SUDAH ADA";
      let prev=same.filter(x=>x.startDate<r.date).sort((a,b)=>b.startDate.localeCompare(a.startDate))[0]||null;
      let future=same.find(x=>x.startDate>r.date)||null;
      // Overlap yang SUDAH TERBUKTI oleh RETURN tetap ditolak pada semua mode.
      if(!reason&&prev&&prev.returnDate&&prev.returnDate>=r.date)reason=`DOUBLE RENTAL / OVERLAP: SIKLUS ${fmtDate(prev.startDate)} BARU KEMBALI ${fmtDate(prev.returnDate)}`;
      // Clean rebuild histori boleh diimpor tidak berurutan / beda waktu. Open cycle lama
      // ditahan sebagai PR histori sampai RETURN aktual datang; tidak dianggap hak tagih final.
      if(!reason&&historyMode){
        if(prev&&!prev.returnDate){
          historyWarning=`HISTORI MENUNGGU RETURN: cycle sebelumnya ${fmtDate(prev.startDate)} belum memiliki Tgl Kembali. TAKE ${fmtDate(r.date)} tetap diterima sementara; RETURN wajib membuktikan tidak overlap.`;
          let actual=(await all("rentals")).find(x=>x.id===prev.id);
          if(actual){actual.historyPending=true;actual.historyPendingReason=`Ada TAKE berikutnya ${r.date}; menunggu RETURN aktual`;actual.updatedAt=now();await put("rentals",actual)}
        }
        if(future)historyWarning=`HISTORI BACK-DATE: ada TAKE berikutnya ${fmtDate(future.startDate)}. Cycle ini diterima sementara dan wajib mendapat RETURN sebelum ${fmtDate(future.startDate)}.`;
      }else if(!reason){
        let ov=same.find(x=>x.startDate<r.date&&((x.returnDate||x.inferredEnd)?(x.returnDate||x.inferredEnd)>=r.date:true));
        if(ov)reason=`MASIH DISEWA SEJAK ${fmtDate(ov.startDate)}${ov.returnDate?" S/D "+fmtDate(ov.returnDate):" DAN BELUM DIKEMBALIKAN"}`;
        if(!reason&&future)reason=`ADA PENGAMBILAN BERIKUTNYA ${fmtDate(future.startDate)}; OPERASIONAL SUDAH DIKUNCI, KOREKSI HISTORI MELALUI REVIEW`;
      }
    }
    if(reason){rej.push({...r,reason});await put("oplog",auditLog("AMBIL",r.container,r.date,"REJECT",reason,r.raw));continue}
    let m=await master(r.container,r.date);if(!m){reason="MASTER CONTAINER / VERSI BERLAKU TIDAK DITEMUKAN";rej.push({...r,reason});await put("oplog",auditLog("AMBIL",r.container,r.date,"REJECT",reason,r.raw));continue}
    let rr0=await findRateForMaster(m,r.date);if(!rr0.rate){reason=rr0.reason;rej.push({...r,reason});await put("oplog",auditLog("AMBIL",r.container,r.date,"REJECT",reason,r.raw));continue}
    let rt=rr0.rate,hasMonthly=Number(rt.monthlyRate)>0,hasDaily=Number(rt.dailyRate)>0,
        basis=({B:"BULANAN",BULANAN:"BULANAN",H:"HARIAN",HARIAN:"HARIAN"})[r.method]||"";
    if(!basis){
      if(hasMonthly&&!hasDaily)basis="BULANAN";
      else if(hasDaily&&!hasMonthly)basis="HARIAN";
      else {reason="MASTER TARIF MEMILIKI BULANAN DAN HARIAN; METODE TAKE WAJIB DIPILIH B/BULANAN ATAU H/HARIAN";rej.push({...r,reason});await put("oplog",auditLog("AMBIL",r.container,r.date,"REJECT",reason,r.raw));continue}
    }
    if(basis==="BULANAN"&&!hasMonthly){reason="TARIF BULANAN TIDAK TERSEDIA PADA MASTER TARIF";rej.push({...r,reason});await put("oplog",auditLog("AMBIL",r.container,r.date,"REJECT",reason,r.raw));continue}
    if(basis==="HARIAN"&&!hasDaily){reason="TARIF HARIAN TIDAK TERSEDIA PADA MASTER TARIF";rej.push({...r,reason});await put("oplog",auditLog("AMBIL",r.container,r.date,"REJECT",reason,r.raw));continue}
    let rate=basis==="BULANAN"?Number(rt.monthlyRate):Number(rt.dailyRate);
    let rr={id:r.container+serial(r.date),container:r.container,startDate:r.date,returnDate:"",basis,rate,monthlyRate:basis==="BULANAN"?rate:0,dailyRate:basis==="HARIAN"?rate:0,size:m.size||"",type:m.type||"",vendor:m.vendor||"",masterId:m.id||"",rateMasterId:rt.id,rateValidFrom:rt.validFrom,rateValidTo:rt.validTo,rateSnapshotAt:now(),warning:historyWarning,historyPending:!!historyWarning,historyPendingReason:historyWarning,createdAt:now(),source:"OPERASIONAL",cycleFlag:"TAKE",cycleModel:"TAKE_BASED_V2"};
    await put("rentals",rr);rent.push(rr);ok++;if(historyWarning)pending++;
    await put("oplog",auditLog("AMBIL",r.container,r.date,"DITERIMA",historyWarning||"Cycle TAKE diterima",r.raw));
    invalidateCaches();rent=await legacyCycles();
  }
  $("takeText").value=rej.map(x=>pipeLine(split(x.raw))).join("\n");
  rejectBox("takeRejects",rej);$("takeSummary").innerHTML=`Diterima: <b>${ok}</b> • Pending histori: <b>${pending}</b> • Reject: <b>${rej.length}</b>${historyMode?' • <span class="badge info">MODE IMPOR HISTORI</span>':''}`;
  await rebuild(false);await setSetting("lastExpectedCalculation",today());await refresh();
}
async function processReturn(){
  let rows=returns($("returnText").value),rent=await legacyCycles(),rej=[],ok=0;
  rows.sort((a,b)=>(a.container+a.date).localeCompare(b.container+b.date));
  for(let r of rows){
    let reason="",same=rent.filter(x=>x.container===r.container).sort((a,b)=>a.startDate.localeCompare(b.startDate)),open=same.filter(x=>!x.returnDate&&x.startDate<=r.date&&(!x.inferredEnd||r.date<=x.inferredEnd)).sort((a,b)=>b.startDate.localeCompare(a.startDate))[0];
    if(!r.container)reason="KONTAINER KOSONG"; else if(!r.date)reason="TANGGAL KEMBALI TIDAK VALID";
    if(!reason&&!open){
      let f=same.filter(x=>!x.returnDate&&x.startDate>r.date).sort((a,b)=>a.startDate.localeCompare(b.startDate))[0],dup=same.find(x=>x.returnDate===r.date);
      if(dup)reason=`DUPLIKAT: SUDAH DIKEMBALIKAN ${fmtDate(r.date)}`;
      else if(f)reason=`TGL KEMBALI ${fmtDate(r.date)} LEBIH KECIL DARI TGL AMBIL ${fmtDate(f.startDate)}`;
      else reason="TIDAK ADA RENTAL AKTIF YANG SESUAI";
    }
    if(!reason&&open){let next=same.find(x=>x.startDate>open.startDate);if(next&&r.date>=next.startDate)reason=`TGL KEMBALI ${fmtDate(r.date)} MELEWATI PENGAMBILAN BERIKUTNYA ${fmtDate(next.startDate)}`}
    if(reason){rej.push({...r,reason});await put("oplog",auditLog("KEMBALI",r.container,r.date,"REJECT",reason,r.raw));continue}
    let actual=(await all("rentals")).find(x=>x.id===open.id);
    if(actual){actual.returnDate=r.date;actual.updatedAt=now();await put("rentals",actual)}
    else{
      let ov=(await all("rentalOverrides")).find(x=>x.id===open.id)||{id:open.id,startDate:open.startDate,reason:"Pengembalian aktual diimport"};
      ov.returnDate=r.date;ov.updatedAt=now();await put("rentalOverrides",ov);
    }
    open.returnDate=r.date;open.historyPending=false;open.historyPendingReason="";
    let saved=(await all("rentals")).find(x=>x.id===open.id);if(saved){saved.historyPending=false;saved.historyPendingReason="";saved.updatedAt=now();await put("rentals",saved)}
    rent=rent.map(x=>x.id===open.id?open:x);ok++;
    await put("oplog",auditLog("KEMBALI",r.container,r.date,"DITERIMA","Menutup siklus rental; PR histori cycle diselesaikan",r.raw));
  }
  $("returnText").value=rej.map(x=>pipeLine(split(x.raw))).join("\n");
  rejectBox("returnRejects",rej);$("returnSummary").innerHTML=`Diterima: <b>${ok}</b> • Reject: <b>${rej.length}</b>`;
  await rebuild(false);await setSetting("lastExpectedCalculation",today());await refresh();
}
$("processTake").onclick=processTake;$("processReturn").onclick=processReturn;$("lockHistoricalImport").onclick=lockHistoricalImport;
$("clearTake").onclick=()=>{$("takeText").value="";rejectBox("takeRejects",[])};
$("clearReturn").onclick=()=>{$("returnText").value="";rejectBox("returnRejects",[])};

async function rebuild(doRefresh=true){
  invalidateCaches();
  // Simpan snapshot Expected lama agar periode yang sudah PAID tidak berubah oleh perubahan formula.
  // Expected unpaid boleh dihitung ulang memakai business rule terbaru.
  let oldExpected=await all("expected"),oldExpectedById=new Map(oldExpected.map(e=>[e.id,e]));
  let rent=await legacyCycles(),inv=await all("invoices");await clear("expected");
  for(let r of rent){
    // Cycle histori yang masih menunggu RETURN aktual belum boleh membentuk Expected palsu.
    if(r.historyPending&&!r.returnDate&&r.inferredEnd)continue;
    let linked=inv.filter(x=>(x.rentalId||"")===r.id),maxDocumented=Math.max(0,...linked.map(x=>Number(x.period||0)).filter(Boolean));
    let end;
    if(r.returnDate)end=r.returnDate;
    else if(r.inferredEnd&&r.source==="LEGACY_MIGRATION"){
      // Jangan menebak tanggal kembali histori. Siklus berikutnya hanya membuktikan cycle lama sudah CLOSED.
      // Expected histori dibangun sampai periode terakhir yang memang terdokumentasi.
      let tmp=buildBillingPeriods(r.startDate,null,Math.max(maxDocumented,1));
      end=tmp[Math.max(maxDocumented,1)-1]?.endDate||r.inferredEnd;
      if(end>r.inferredEnd)end=r.inferredEnd;
    }else end=today();

    let periods=buildBillingPeriods(r.startDate,end,240);
    for(let pr of periods){
      let p=pr.period,start=pr.startDate,pe=pr.endDate,nextNatural=nextBillingPeriodStart(start),naturalEnd=addDays(nextNatural,-1),full=pe===naturalEnd,amount=0;
      if(r.basis==="BULANAN"){
        amount=Number(r.monthlyRate||r.rate||0);if(!full)amount*=days(start,pe)/div(start);
      }else if(r.basis==="HARIAN")amount=Number(r.dailyRate||r.rate||0)*days(start,pe);
      let id=r.id+String(p).padStart(2,"0"),matched=linked.filter(i=>Number(i.period||0)===p),paid=matched.some(i=>i.paymentStatus==="SUDAH DIBAYAR");
      let status=paid?"SUDAH DIBAYAR":matched.length?"SUDAH DITAGIH":"BELUM DITAGIH";
      // PAID adalah financial lock: pertahankan nominal Expected yang sudah menjadi histori pembayaran.
      // Unpaid dihitung ulang sehingga perubahan divisor/formula langsung memperbaiki Review.
      let old=oldExpectedById.get(id),finalAmount=(paid&&old&&Number.isFinite(Number(old.amount)))?Number(old.amount):Math.round(amount*100)/100;
      await put("expected",{id,rentalId:r.id,container:r.container,period:p,startDate:start,endDate:pe,amount:finalAmount,status,invoiceNo:matched.map(i=>i.invoiceNo).filter(Boolean).join(", ")});
    }
  }
  invalidateCaches();if(doRefresh)await refresh();
}
$("rebuildExpected").onclick=async()=>{
  let btn=$("rebuildExpected"),oldText=btn.textContent;btn.disabled=true;btn.textContent="Memproses...";
  try{
    await normalizeTakeBasedCycles();
    let r=await reReviewUnpaidInvoices();
    await setSetting("lastExpectedCalculation",today());
    await refresh();
    let pr=(await cycleCleanupRows()).length;alert(`Kalkulasi + Review Ulang selesai.\n${r.changed} detail diperbarui.\n${r.cleared} detail menjadi bersih.\n${r.remaining} detail masih perlu direview.\n${pr} PR siklus perlu dikoreksi.`);
  }finally{btn.disabled=false;btn.textContent=oldText}
};

function invoices(t){return t.split(/\r?\n/).map((raw,i)=>({raw,line:i+1,c:split(raw).map(x=>x.trim())})).filter(x=>x.raw.trim()).map(x=>{let migrationMode=x.c.length>=8;return {raw:x.raw,line:x.line,vendor:x.c[0]||"",invoiceNo:x.c[1]||"",invoiceDate:parseDate(x.c[2]),amount:amt(x.c[3]),container:norm(x.c[4]),period:parseInt(x.c[5],10)||null,cycleChoice:migrationMode?null:(parseInt(x.c[6],10)||null),migrationStartDate:migrationMode?parseDate(x.c[6]):"",migrationEndDate:migrationMode?(x.c[7]?parseDate(x.c[7]):""):"",migrationStartRaw:migrationMode?(x.c[6]||""):"",migrationEndRaw:migrationMode?(x.c[7]||""):"",migrationMode}})}
function matchMigrationCycle(x,rent,expected=[]){
  if(!x?.migrationMode||!x.container||!x.migrationStartDate)return {matches:[],cycle:null,method:"NONE",evidence:{}};
  const same=rent.filter(r=>r.container===x.container);
  const vp=Number(x.vendorPeriod||x.period||0);

  // V7.1.5: jangan memenangkan TAKE_EXACT terlalu dini.
  // Tanggal referensi historis dapat berarti tanggal cycle ATAU tanggal periode vendor.
  // Kumpulkan seluruh cycle yang masuk akal dari kedua jalur, lalu auto-link HANYA
  // bila union kandidat menghasilkan tepat satu cycle.
  const takeHits=same.filter(r=>r.startDate===x.migrationStartDate);
  const periodEvidence=[];
  if(vp){
    for(const r of same){
      const e=expected.find(e=>e.rentalId===r.id&&Number(e.period||0)===vp);
      if(!e||e.startDate!==x.migrationStartDate)continue;
      periodEvidence.push({r,e,endExact:!!x.migrationEndDate&&e.endDate===x.migrationEndDate});
    }
  }
  const periodHits=periodEvidence.map(z=>z.r);
  let union=new Map();
  for(const r of [...takeHits,...periodHits])union.set(r.id,r);
  let matches=[...union.values()];

  // TGL SEWA SAMPAI adalah bukti tambahan. Ia boleh berupa RETURN cycle atau akhir
  // periode vendor. Hanya gunakan untuk menyaring jika hasilnya tetap tidak ambigu.
  if(matches.length>1&&x.migrationEndDate){
    const endSupported=matches.filter(r=>{
      if(r.returnDate===x.migrationEndDate)return true;
      const z=periodEvidence.find(q=>q.r.id===r.id);
      return !!z?.endExact;
    });
    if(endSupported.length===1)matches=endSupported;
  }

  const evidence={
    takeCycleIds:takeHits.map(r=>r.id),
    periodCycleIds:periodHits.map(r=>r.id),
    referenceStart:x.migrationStartDate,
    referenceEnd:x.migrationEndDate||"",
    vendorPeriod:vp||null
  };
  if(matches.length===1){
    const r=matches[0],fromTake=takeHits.some(z=>z.id===r.id),fromPeriod=periodHits.some(z=>z.id===r.id);
    let method=fromTake&&fromPeriod?"TAKE_AND_VENDOR_PERIOD_SAME_CYCLE":fromTake?"TAKE_REFERENCE_UNIQUE":"VENDOR_PERIOD_TRACE_EXPECTED";
    return {matches,cycle:r,method,evidence};
  }
  if(matches.length>1)return {matches,cycle:null,method:"REFERENCE_AMBIGU_TAKE_OR_PERIOD",evidence};
  return {matches:[],cycle:null,method:"NO_MATCH",evidence};
}

function siklusTerkonfirmasi(r){
  if(!r||!r.startDate)return false;
  // Pengambilan nyata dari impor operasional adalah sumber utama.
  if(r.source==="OPERASIONAL")return true;
  // Siklus.xlsx adalah referensi operasional histori yang telah disepakati sebagai authority.
  if(r.source==="SIKLUS_REFERENCE"&&(r.referenceMatched||r.referenceNo||r.referenceSource==="Siklus.xlsx"))return true;
  // Koreksi manual pada rental yang memang ditandai terkonfirmasi juga diperbolehkan.
  if(r.confirmed===true||r.cycleConfirmed===true)return true;
  // LEGACY_MIGRATION / hasil inferensi finansial tidak boleh mengesahkan pembayaran.
  return false;
}


function kandidatSiklusTagihan(x,expected,rent,existing=[]){
  let vendorPeriod=Number(x.vendorPeriod||x.period||0);
  if(!x.container||!vendorPeriod)return [];
  let cand=rent.filter(r=>r.container===x.container)
    .map(r=>{
      let e=expected.find(e=>e.rentalId===r.id&&Number(e.period||0)===vendorPeriod)||null,
          entitlements=expected.filter(e=>e.rentalId===r.id),
          maxPeriod=Math.max(0,...entitlements.map(e=>Number(e.period||0))),
          amountDiff=e&&Number(e.amount||0)>0&&x.amount!=null?Math.abs(Number(e.amount)-Number(x.amount)):null,
          closed=!!r.returnDate,
          noEntitlement=!e;
      let score=0;if(e)score+=50;if(amountDiff!=null&&amountDiff<=1)score+=30;
      // Tgl Tagihan hanya bukti dokumen; tidak boleh menentukan atau memberi skor Cycle.
      return {r,e,entitlements,maxPeriod,amountDiff,closed,noEntitlement,score};
    });
  // Nomor kandidat sengaja stabil: tanggal ambil lama -> baru. Score hanya menjadi saran, bukan auto-decision.
  cand.sort((a,b)=>String(a.r.startDate||'').localeCompare(String(b.r.startDate||''))||String(a.r.id||'').localeCompare(String(b.r.id||'')));
  let best=[...cand].sort((a,b)=>b.score-a.score||String(b.r.startDate||'').localeCompare(String(a.r.startDate||'')))[0];
  for(let z of cand){z.suggested=!!best&&z.r.id===best.r.id&&best.score>0;z.used=existing.some(i=>i.id!==x.id&&!isSupersededDetail(i)&&(i.rentalId||i.canonicalCycleId)===z.r.id&&Number(i.vendorPeriod||i.period||0)===vendorPeriod&&!i.detached)}
  return cand;
}
function htmlKandidatSiklus(cand){
  return cand.map((z,i)=>`<div class="small" style="padding:5px 0"><b>[${i+1}] Ambil ${fmtDate(z.r.startDate)}</b> • Kembali ${z.r.returnDate?fmtDate(z.r.returnDate):"Aktif"} • ${esc(z.r.source==="SIKLUS_REFERENCE"?"Siklus.xlsx":z.r.source||z.r.basis||"HISTORI")}<br>${z.e?`<span class="goodtext">Hak P${z.e.period} ADA</span> • ${fmtDate(z.e.startDate)}–${fmtDate(z.e.endDate)} • ${money(Number(z.e.amount||0))}`:`<span class="badtext">Hak P${esc(z.maxPeriod?Number(z.maxPeriod)+1:"?")} / P vendor TIDAK ADA pada siklus ini</span> • Expected tersedia s/d P${z.maxPeriod||0}`}${z.suggested?` • <b>USULAN SISTEM</b>`:""}</div>`).join("");
}
// V7.1.36: keputusan kandidat kanonik. Pn hanya mengurangi kandidat melalui Expected aktual.
// Jika tepat satu Cycle memiliki Expected Pn, auto-match. Jika >1, tetap ambigu.
function resolveCanonicalCycleCandidate(cand){
  const entitled=(cand||[]).filter(z=>!!z.e);
  if(entitled.length===1)return {chosen:entitled[0],ambiguous:false,method:"UNIQUE_EXPECTED_PERIOD"};
  if(entitled.length>1)return {chosen:null,ambiguous:true,method:"MULTI_EXPECTED_PERIOD"};
  if((cand||[]).length===1)return {chosen:cand[0],ambiguous:false,method:"SINGLE_CYCLE_NO_ENTITLEMENT"};
  return {chosen:null,ambiguous:(cand||[]).length>1,method:(cand||[]).length?"MULTI_CYCLE_NO_ENTITLEMENT":"NO_CYCLE"};
}
function cycleRelationProtected(x){
  return !!(x?.manualCycleConfirmed===true||x?.userCycleDecision||x?.cycleDecision||x?.userSelectedCycle||x?.manualCycleId||x?.approvalStatus==="APPROVED"||x?.pranotaNo||x?.paymentStatus==="SUDAH DIBAYAR");
}

async function assess(x,existing,expected,rent){
  let p=[],evidence={expected:null,duplicateInvoices:[],paidInvoices:[],cycleId:"",cycleCandidates:[]},cycle=null,exp=null,matchMethod="UNMATCHED";
  let vendorPeriod=Number(x.vendorPeriod||x.period||0);x.vendorPeriod=vendorPeriod||x.vendorPeriod||x.period||null;
  if(!x.vendor&&!x.legacy)p.push("VENDOR KOSONG");if(!x.invoiceNo)p.push("NO TAGIHAN KOSONG");if(!x.invoiceDate)p.push("TGL TAGIHAN TIDAK VALID");if(x.amount==null)p.push("NOMINAL TIDAK VALID");if(!x.container)p.push("KONTAINER KOSONG");if(!vendorPeriod)p.push("PERIODE VENDOR TIDAK VALID");

  // V7.1.36 — bukti vendor P1/Pn harus dinilai sebelum mempercayai link otomatis lama.
  // Link pilihan user / workflow downstream tetap dikunci dan tidak disentuh.
  const protectedRelation=cycleRelationProtected(x);
  if(protectedRelation&&(x.rentalId||x.canonicalCycleId)){
    let lockedId=x.rentalId||x.canonicalCycleId;
    cycle=rent.find(r=>r.id===lockedId&&r.container===x.container)||null;
    if(cycle){exp=expected.find(e=>e.rentalId===cycle.id&&Number(e.period||0)===vendorPeriod)||null;matchMethod="PROTECTED_CYCLE_LOCK"}
  }

  // Historical/manual vendor evidence: tanggal referensi dapat berarti TAKE (P1) ATAU awal Pn.
  // Gunakan union TAKE + Expected aktual. Kasus akhir Feb/awal Mar yang menunjuk >1 Cycle tetap AMBIGU.
  if(!cycle&&x.container&&vendorPeriod&&x.migrationMode){
    let mig=matchMigrationCycle(x,rent,expected);
    evidence.migrationMatch=mig.evidence||{};
    if(mig.cycle){
      cycle=mig.cycle;
      exp=expected.find(e=>e.rentalId===cycle.id&&Number(e.period||0)===vendorPeriod)||null;
      matchMethod=mig.method;
    }else if(mig.matches.length>1){
      evidence.cycleCandidates=mig.matches.map((r,i)=>({no:i+1,id:r.id,startDate:r.startDate||"",returnDate:r.returnDate||""}));
      p.push("SIKLUS AMBIGU: REFERENSI TANGGAL COCOK KE LEBIH DARI SATU CYCLE");
      matchMethod=mig.method;
    }else{
      p.push("SIKLUS RENTAL/PERIODE TIDAK DITEMUKAN DARI REFERENSI TANGGAL");
      matchMethod=mig.method;
    }
  }

  // Data tanpa tanggal referensi: kembali ke prinsip lama Container + P -> Expected pada semua Cycle.
  // Tepat satu Cycle yang mempunyai Expected P tersebut = otomatis. Lebih dari satu = ambigu.
  if(!cycle&&x.container&&vendorPeriod&&!x.migrationMode){
    let cand=kandidatSiklusTagihan(x,expected,rent,existing),auto=resolveCanonicalCycleCandidate(cand);
    evidence.cycleCandidates=cand.map((z,i)=>({no:i+1,id:z.r.id,startDate:z.r.startDate||"",returnDate:z.r.returnDate||"",hasEntitlement:!!z.e,maxPeriod:z.maxPeriod,amount:z.e?Number(z.e.amount||0):null,suggested:z.suggested}));
    if(x.cycleChoice){let chosen=cand[Number(x.cycleChoice)-1];if(chosen){cycle=chosen.r;exp=chosen.e;matchMethod="PILIHAN_USER_KANDIDAT_"+Number(x.cycleChoice)}else p.push(`NO KANDIDAT SIKLUS ${x.cycleChoice} TIDAK VALID`)}
    else if(auto.chosen){cycle=auto.chosen.r;exp=auto.chosen.e;matchMethod=auto.method}
    else if(auto.ambiguous)p.push("SIKLUS AMBIGU: PILIH NO KANDIDAT PADA KOLOM KE-7");
    else p.push("SIKLUS RENTAL TIDAK DITEMUKAN");
  }
  if(cycle&&!siklusTerkonfirmasi(cycle)&&x.manualCycleConfirmed!==true){p.push("SIKLUS RENTAL BELUM TERKONFIRMASI");matchMethod+="|SIKLUS_TIDAK_TERKONFIRMASI"}
  let claimStatus="UNMATCHED",claimFlags=[];

  if(cycle){
    evidence.cycleId=cycle.id;
    if(exp){claimStatus="MATCH_ENTITLEMENT"}
    else{
      claimStatus="NO_ENTITLEMENT";claimFlags.push("TIDAK ADA EXPECTED / HAK TAGIH UNTUK PERIODE VENDOR");
      p.push(`TAGIHAN P${vendorPeriod} TIDAK MEMILIKI HAK TAGIH PADA SIKLUS TERPILIH`);
      if(cycle.returnDate){claimFlags.push(`SIKLUS SUDAH KEMBALI ${fmtDate(cycle.returnDate)}`);p.push(`TAGIHAN DI LUAR HAK SIKLUS; CONTAINER SUDAH KEMBALI ${fmtDate(cycle.returnDate)}`)}
    }
  }
  let systemExpected=0,sys={amount:0,start:"",end:"",isProrata:false,expectedId:""};
  if(exp){sys=await systemExpectedForInvoice({...x,expectedId:exp.id,rentalId:cycle?.id||""},expected,cycle);systemExpected=Number(sys.amount||0);if(!x.legacy&&systemExpected>0&&x.amount!=null&&Math.abs(Number(x.amount)-(systemExpected+Number(x.adjustment||0)))>1)p.push("NOMINAL BERBEDA DARI EXPECTED + KOREKSI MANUAL");evidence.expected={id:exp.id,amount:systemExpected,container:exp.container||x.container,period:Number(exp.period||vendorPeriod),startDate:sys.start||exp.startDate||"",endDate:sys.end||exp.endDate||""}}
  let duplicateRows=cycle?existing.filter(i=>i.id!==x.id&&!isSupersededDetail(i)&&(i.canonicalCycleId||i.rentalId)===cycle.id&&Number(i.vendorPeriod||i.period||0)===vendorPeriod&&i.invoiceNo!==x.invoiceNo):[];
  if(duplicateRows.length){p.push("INDIKASI DUPLIKAT: SIKLUS + PERIODE VENDOR SUDAH DITAGIH PADA NO TAGIHAN LAIN");evidence.duplicateInvoices=duplicateRows.map(i=>({invoiceNo:i.invoiceNo||"",amount:Number(i.amount||0),status:i.decision||"",paymentStatus:i.paymentStatus||"",paymentNo:i.paymentNo||"",paymentDate:i.paymentDate||""}))}
  let paidRows=cycle?existing.filter(i=>i.id!==x.id&&!isSupersededDetail(i)&&(i.canonicalCycleId||i.rentalId)===cycle.id&&Number(i.vendorPeriod||i.period||0)===vendorPeriod&&i.paymentStatus==="SUDAH DIBAYAR"):[];
  if(paidRows.length){p.push("SIKLUS + PERIODE VENDOR SUDAH PERNAH DIBAYAR");evidence.paidInvoices=paidRows.map(i=>({invoiceNo:i.invoiceNo||"",amount:Number(i.paidAmount||i.amount||0),paymentNo:i.paymentNo||"",paymentDate:i.paymentDate||"",pranotaNo:i.pranotaNo||i.batchId||""}))}
  if(x.claimDecision==="TIDAK_BERHAK_DITAGIH"){claimStatus="NO_ENTITLEMENT_CONFIRMED";claimFlags=[...new Set([...claimFlags,"DIKONFIRMASI USER: TIDAK BERHAK DITAGIH"])];if(!p.some(v=>String(v).includes("TIDAK MEMILIKI HAK TAGIH")))p.push("USER MENETAPKAN: TIDAK BERHAK DITAGIH")}
  return{problems:[...new Set(p)],decision:p.length?"PENDING":"READY TO PAY",expectedId:exp?.id||"",rentalId:cycle?.id||"",canonicalCycleId:cycle?.id||"",vendorPeriod,systemPeriod:exp?Number(exp.period):null,startDate:sys.start||exp?.startDate||"",endDate:sys.end||exp?.endDate||"",systemExpectedAmount:systemExpected,isProrata:sys.isProrata,matchMethod,evidence,claimStatus,claimFlags};
}

async function syncClosingCorrectionsToCycles(){
  invalidateCaches();
  let rows=(await all("invoices")).filter(x=>x.paymentStatus!=="SUDAH DIBAYAR"&&x.correctedEndDate),
      cycles=await legacyCycles(),synced=0,blocked=0,financialFixed=0;

  for(let x of rows){
    let cycleId=invoiceCycleId(x);
    if(!cycleId)continue;

    let cycle=cycles.find(c=>c.id===cycleId);
    if(!cycle)continue;

    let start=effectiveDetailStart(x),end=x.correctedEndDate;
    if(!start||!end)continue;

    let naturalEnd=addDays(nextBillingPeriodStart(start),-1);
    if(end>=naturalEnd)continue; // bukan closing/prorata

    let paidImpact=await paidImpactForReturn(cycleId,end);
    if(paidImpact.length){blocked++;continue}

    // Jangan menimpa return date yang sudah berbeda secara otomatis.
    if(cycle.returnDate&&cycle.returnDate!==end){blocked++;continue}

    if(!cycle.returnDate){
      await syncCycleReturn(cycleId,end,x.adjustmentNote||"Sinkronisasi closing dari koreksi detail tagihan");
      synced++;
      invalidateCaches();
      cycles=await legacyCycles();
      cycle=cycles.find(c=>c.id===cycleId)||cycle;
    }

    // Prorata adalah nilai Expected Sistem, BUKAN Koreksi Manual.
    // Recalculate tidak pernah lagi membuat/menimpa adjustment.
    x.adjustmentAuto=false;
  }

  if(synced||financialFixed||blocked){
    await put("oplog",auditLog(
      "SYNC CLOSING",
      "RENTAL",
      today(),
      blocked?"TERCATAT":"DITERIMA",
      `${synced} rental disinkronkan • ${blocked} koreksi diblokir karena konflik/PAID • prorata dihitung sebagai Expected Sistem`,
      "Review Detail -> Rental Cycle"
    ));
  }

  invalidateCaches();
  return {synced,blocked,financialFixed};
}


function cycleOnlyProblem(p){let t=norm(p);return /SIKLUS|CYCLE|RENTAL|PERIODE/.test(t)&&!/NOMINAL|DOUBLE|DUPLIKAT|PAID|BAYAR|TARIF|HAK TAGIH|ENTITLEMENT/.test(t)}
function periodNoOfInvoice(x){let n=Number(x.periodNo||x.vendorPeriodNo||x.period||0);if(Number.isFinite(n)&&n>0)return Math.trunc(n);let m=String(x.vendorPeriod||x.periodLabel||"").match(/\bP(?:ERIODE)?\s*0*(\d+)\b/i);return m?Number(m[1]):0}
function periodNoOfExpected(e,a){let n=Number(e.periodNo||e.period||0);if(Number.isFinite(n)&&n>0)return Math.trunc(n);let z=[...a].sort((p,q)=>String(p.start||p.periodStart||"").localeCompare(String(q.start||q.periodStart||"")));let i=z.findIndex(q=>String(q.id)===String(e.id));return i<0?0:i+1}
function sameDay(a,b){return !!a&&!!b&&String(a).slice(0,10)===String(b).slice(0,10)}
function protectedCycleDecision(x){return !!(x.cycleDecision||x.userSelectedCycle||x.manualCycleId||x.supersededBy||x.supersedesDetailId||x.adjustmentSource==="MANUAL"||x.creditResolution?.status==="RESOLVED"||x.duplicateDecision||x.claimDecision)}
async function autoResolveCycleTraceBack(scope=null){
 let rows=scope||await activeInvoiceRows(),work=rows.filter(x=>isActiveWorkflowDetail(x)&&!isSupersededDetail(x)&&!x.paymentId&&!x.pranotaNo&&x.approvalStatus!=="APPROVED"),rent=await all("rentals"),exp=await all("expected"),rb=new Map(),eb=new Map();
 for(let r of rent){let k=norm(r.container);if(!rb.has(k))rb.set(k,[]);rb.get(k).push(r)}
 for(let e of exp){let k=String(e.cycleId||"");if(!k)continue;if(!eb.has(k))eb.set(k,[]);eb.get(k).push(e)}
 let changed=[];
 for(let x of work){if(protectedCycleDecision(x))continue;let ps=effectiveDetailProblems(x);if(!ps.length||ps.some(p=>!cycleOnlyProblem(p)))continue;let pn=periodNoOfInvoice(x),hint=x.rentalStartRef||x.cycleStartRef||x.takeDateRef||x.startDateRef||"",m=[];
  for(let r of rb.get(norm(x.container))||[]){let cid=String(r.cycleId||r.id||""),es=eb.get(cid)||[];if(!cid||!es.length)continue;if(hint&&!sameDay(hint,r.startDate||r.takeDate||r.start))continue;let pe=pn?es.filter(e=>periodNoOfExpected(e,es)===pn):[];if(pn&&pe.length!==1)continue;let target=pe[0]||null;if(target&&x.vendorPeriodStart&&(target.start||target.periodStart)&&!sameDay(x.vendorPeriodStart,target.start||target.periodStart))continue;if(target&&x.vendorPeriodEnd&&(target.end||target.periodEnd)&&!sameDay(x.vendorPeriodEnd,target.end||target.periodEnd))continue;m.push({cid,target})}
  if(m.length!==1)continue;x.cycleTraceSuggestion={cycleId:m[0].cid,expectedId:m[0].target?.id||"",periodNo:pn,source:"AUTO_TRACEBACK_PN",at:new Date().toISOString()};changed.push(x)}
 if(changed.length)await putMany("invoices",changed);return {changed:changed.length}
}
async function reReviewUnpaidInvoices(){let beforeRows=await activeInvoiceRows(),beforeOpen=beforeRows.filter(x=>effectiveDetailProblems(x).length&&!x.resolved).length,beforeState=new Map(beforeRows.map(x=>[String(x.id),{resolved:x.resolved,decision:x.decision,problems:Array.isArray(x.problems)?[...x.problems]:[]}]));

  await syncClosingCorrectionsToCycles();
  invalidateCaches();
  await rebuild(false);
  invalidateCaches();
  let expected=await combinedExpected(),rent=await legacyCycles(),rows=await all("invoices");
  let changed=0,cleared=0,remaining=0;

  let activeEvidenceRows=rows.filter(x=>!isSupersededDetail(x));
  for(let x of rows){
    if(x.paymentStatus==="SUDAH DIBAYAR"||isSupersededDetail(x)||!isWorkflowReviewRow(x))continue;
    let before=JSON.stringify({problems:x.problems||[],decision:x.decision,expectedId:x.expectedId,rentalId:x.rentalId,systemStartDate:x.systemStartDate||"",systemEndDate:x.systemEndDate||""});

    // Simpan tanggal sumber pertama kali agar bukti data Excel/vendor tidak hilang.
    if(!x.rawStartDate&&x.startDate)x.rawStartDate=x.startDate;
    if(!x.rawEndDate&&x.endDate)x.rawEndDate=x.endDate;

    let a=await assess(x,activeEvidenceRows,expected,rent);
    x.expectedId=a.expectedId;
    x.rentalId=a.rentalId||"";
    x.matchMethod=a.matchMethod;
    x.issueEvidence=a.evidence||{};x.vendorPeriod=a.vendorPeriod||x.vendorPeriod||x.period;x.systemPeriod=a.systemPeriod??null;x.claimStatus=a.claimStatus||"UNMATCHED";x.claimFlags=a.claimFlags||[];x.canonicalCycleId=a.canonicalCycleId||"";

    // Tanggal sistem selalu mengikuti rental + nomor periode. Koreksi manual tetap prioritas tampilan.
    x.systemStartDate=a.startDate||"";
    x.systemEndDate=a.endDate||"";

    let staleLegacyProblems=new Set([
      "PERIODE SEBELUMNYA TIDAK DITEMUKAN",
      "AKHIR < AWAL",
      "PERIODE TUMPANG TINDIH",
      "PERIODE TIDAK BERURUTAN",
      ...(x.legacy?["VENDOR KOSONG"]:[])
    ]);
    let probs=[...a.problems].filter(p=>!staleLegacyProblems.has(String(p||"").trim().toUpperCase()));
    if(x.identityWarning){
      let mastersNow=await all("masters"),masterNow=selectMasterVersion(mastersNow,x.container,x.invoiceDate),candNow=kandidatSiklusTagihan(x,expected,rent,rows),unresolved=[];
      if(String(x.identityWarningReason||"").includes("CONTAINER TIDAK ADA DI MASTER")&&!masterNow)unresolved.push("CONTAINER TIDAK ADA DI MASTER");
      if(String(x.identityWarningReason||"").includes("SIKLUS AMBIGU")&&!x.cycleChoice&&a.problems.some(p=>String(p).includes("SIKLUS AMBIGU")))unresolved.push("SIKLUS AMBIGU");
      if(String(x.identityWarningReason||"").includes("SIKLUS RENTAL/PERIODE TIDAK DITEMUKAN")&&candNow.length===0)unresolved.push("SIKLUS RENTAL/PERIODE TIDAK DITEMUKAN");
      if(String(x.identityWarningReason||"").includes("NO KANDIDAT")&&(!x.cycleChoice||!candNow[Number(x.cycleChoice)-1]))unresolved.push("NO KANDIDAT SIKLUS TIDAK VALID");
      x.identityWarning=unresolved.length>0;x.identityWarningReason=unresolved.join(" • ");for(let w of unresolved)if(!probs.includes(w))probs.push(w);
    }
    let effectiveStart=x.correctedStartDate||x.systemStartDate||x.startDate||"";
    let effectiveEnd=x.correctedEndDate||x.systemEndDate||x.endDate||"";
    if(effectiveStart&&effectiveEnd&&effectiveEnd<effectiveStart)probs.push("AKHIR < AWAL");

    // Bila user sudah mengoreksi tanggal, pastikan koreksinya cocok dengan expected terbaru.
    if(x.correctedStartDate&&a.startDate&&x.correctedStartDate!==a.startDate)probs.push("TGL AWAL KOREKSI BERBEDA DARI EXPECTED");
    if(x.correctedEndDate&&a.endDate&&x.correctedEndDate!==a.endDate)probs.push("TGL AKHIR KOREKSI BERBEDA DARI EXPECTED");

    x.problems=[...new Set(probs)];
    // Raw problems tetap menjadi bukti. Keputusan user yang persisten dinilai melalui effectiveDetailProblems.
    // Kredit Vendor hanya menutup masalah nominal; masalah struktural tetap memblokir.
    x.resolved=effectiveDetailProblems(x).length===0;
    x.systemExpectedAmount=a.claimStatus&&String(a.claimStatus).startsWith("NO_ENTITLEMENT")?0:Number(a.systemExpectedAmount??x.systemExpectedAmount??x.amount??0);
    x.expectedKind=a.isProrata?"PRORATA":"EXPECTED";
    x.adjustment=Number(x.adjustment||0); // hanya input manual; nilai 0 sah.
    x.approvedAmount=approvedFromSystem(x);
    x.decision=effectiveDetailProblems(x).length&&!x.resolved?"PENDING":"READY TO PAY";
    x.reviewedAt=now();
    x.reviewEngine="EXPECTED_PLUS_MANUAL_V1";

    let after=JSON.stringify({problems:x.problems,decision:x.decision,expectedId:x.expectedId,rentalId:x.rentalId,systemStartDate:x.systemStartDate||"",systemEndDate:x.systemEndDate||""});
    if(before!==after){changed++;if(effectiveDetailProblems(x).length===0)cleared++}
    if(effectiveDetailProblems(x).length&&!x.resolved)remaining++;
    await put("invoices",x);
  }
  await applyInvoiceGroupStatus();
  let afterRows=await activeInvoiceRows(),afterOpen=afterRows.filter(x=>effectiveDetailProblems(x).length&&!x.resolved).length;
  if(beforeOpen>=20&&afterOpen===0){
    let restore=afterRows.map(x=>{let b=beforeState.get(String(x.id));if(!b)return x;x.resolved=b.resolved;x.decision=b.decision;x.problems=b.problems;return x});
    await putMany("invoices",restore);
    await put("oplog",auditLog("SAFETY GUARD REVIEW","INVOICE",today(),"DITERIMA",`Revalidasi dibatalkan: ${beforeOpen} PR aktif menjadi 0`,"State sebelumnya dipertahankan"));
    alert("Safety Guard: revalidasi massal menghasilkan 0 PR dan dibatalkan. Data/keputusan sebelumnya dipertahankan.");
    return;
  }
  await put("oplog",auditLog("REVIEW ULANG","EXPECTED",today(),"DITERIMA",`${changed} detail berubah • ${cleared} masalah bersih • ${remaining} detail masih bermasalah`,"Kalkulasi + Review Ulang"));
  invalidateCaches();
  return {changed,cleared,remaining};
}

async function applyInvoiceGroupStatus(scopeInvoiceNos=null){
  let rows=(await activeInvoiceRows()).filter(isWorkflowReviewRow),groups={},scope=scopeInvoiceNos?new Set([...scopeInvoiceNos].map(norm)):null;if(scope)rows=rows.filter(x=>scope.has(norm(x.invoiceNo)));
  for(let x of rows){
    let no=String(x.invoiceNo||"").trim(),k=`${String(x.vendor||"").trim().toUpperCase()}|${no.toUpperCase()}`;
    if(!no)continue;
    (groups[k]??=[]).push(x);
  }
  for(let items of Object.values(groups)){
    let unpaid=items.filter(x=>x.paymentStatus!=="SUDAH DIBAYAR");
    if(!unpaid.length)continue;
    let hasUnresolved=unpaid.some(x=>{
      let raw=effectiveDetailProblems(x);
      // Keputusan final user atas NO_ENTITLEMENT menutup masalah hak-tagih saja.
      // Masalah independen (mis. cycle belum dipilih/ambigu) tetap memblokir Approval.
      if(x.claimDecision==="TIDAK_BERHAK_DITAGIH"){
        raw=raw.filter(p=>!/TIDAK BERHAK|HAK TAGIH|TIDAK MEMILIKI HAK TAGIH|EXPECTED.*TIDAK ADA|PERIODE VENDOR/i.test(String(p)));
        return raw.length>0; // masalah independen tetap harus diselesaikan sebelum Approval
      }
      if(x.claimDecision==="DITOLAK_USER"){
        raw=raw.filter(p=>!duplicateProblemsOnly(p));
        return raw.length>0;
      }
      if(x.duplicateDecision==="BUKAN_DUPLIKAT")raw=raw.filter(p=>!duplicateProblemsOnly(p));
      return raw.length>0&&!x.resolved;
    });
    for(let x of unpaid){
      x.problems=(x.problems||[]).filter(p=>p!=="PENDING SATU TAGIHAN: ADA DETAIL LAIN BERMASALAH");
      if(hasUnresolved){
        x.decision="PENDING";
        let raw=(x.problems||[]).filter(Boolean);
        if((raw.length===0||x.resolved)&&!x.problems.includes("PENDING SATU TAGIHAN: ADA DETAIL LAIN BERMASALAH")){
          x.problems.push("PENDING SATU TAGIHAN: ADA DETAIL LAIN BERMASALAH");
        }
      }else x.decision="READY TO PAY";
      x.approvedAmount=approvedFromSystem(x);
    }
    await putMany("invoices",unpaid);
  }
}
$("previewInvoices").onclick=async()=>{
  let rows=invoices($("invoiceText").value),existing=await all("invoices"),expected=await combinedExpected(),rent=await legacyCycles(),masters=await all("masters"),out=[];
  for(let x of rows.slice(0,200)){
    let hard=[];
    if(!x.vendor)hard.push("VENDOR WAJIB");if(!x.invoiceNo)hard.push("NO TAGIHAN WAJIB");if(!x.invoiceDate)hard.push("TANGGAL TAGIHAN TIDAK VALID");if(x.amount==null)hard.push("NOMINAL TIDAK VALID");if(!x.container)hard.push("CONTAINER WAJIB");if(!x.period)hard.push("PERIODE TIDAK VALID");if(x.migrationMode&&!x.migrationStartDate)hard.push("TGL SEWA DARI WAJIB/INVALID");if(x.migrationMode&&x.migrationEndRaw&&!x.migrationEndDate)hard.push("TGL SEWA SAMPAI INVALID");
    let mig=hard.length?{matches:[],cycle:null}:matchMigrationCycle(x,rent,expected),cand=x.migrationMode?(mig.cycle?[{r:mig.cycle,e:expected.find(e=>e.rentalId===mig.cycle.id&&Number(e.period||0)===Number(x.period||0))||null,maxPeriod:Math.max(0,...expected.filter(e=>e.rentalId===mig.cycle.id).map(e=>Number(e.period||0))),noEntitlement:!expected.find(e=>e.rentalId===mig.cycle.id&&Number(e.period||0)===Number(x.period||0))}]:[]):kandidatSiklusTagihan(x,expected,rent,existing),selected=x.cycleChoice?cand[Number(x.cycleChoice)-1]:null,master=hard.length?null:selectMasterVersion(masters,x.container,x.invoiceDate),warn=[],state="",extra="";
    if(hard.length)state=`<span class="badtext">INVALID — ${esc(hard.join(" • "))}</span>`;
    else{
      if(!master)warn.push("CONTAINER TIDAK ADA DI MASTER");
      if(x.migrationMode){
        if(mig.matches.length===0)warn.push("SIKLUS HISTORI TIDAK COCOK DENGAN TGL SEWA DARI/SAMPAI");
        else if(mig.matches.length>1)warn.push("SIKLUS HISTORI DUPLIKAT UNTUK TGL SEWA DARI/SAMPAI");
        else if(cand[0]?.noEntitlement)warn.push(`PERIODE VENDOR P${x.period} TIDAK MEMILIKI HAK TAGIH PADA SIKLUS INI`);
      }else if(x.cycleChoice&&!selected)warn.push(`NO KANDIDAT ${x.cycleChoice} TIDAK VALID`);else if(!x.cycleChoice&&cand.length>1)warn.push("SIKLUS AMBIGU");else if(!cand.length)warn.push("SIKLUS RENTAL TIDAK DITEMUKAN");else if((selected||cand[0])?.noEntitlement)warn.push(`PERIODE VENDOR P${x.period} TIDAK MEMILIKI HAK TAGIH PADA SIKLUS INI`);
      state=warn.length?`<span class="pending">WARNING — ${esc(warn.join(" • "))}</span>`:`<span class="goodtext">VALID${selected?` • Kandidat ${x.cycleChoice}: Ambil ${fmtDate(selected.r.startDate)}`:cand.length===1?` • Ambil ${fmtDate(cand[0].r.startDate)}`:""}</span>`;
      if(cand.length>1&&!selected)extra=`<div style="margin-top:5px"><b>Kandidat relevan:</b>${htmlKandidatSiklus(cand)}<div class="small">Opsional: tambahkan |NO KANDIDAT untuk menyelesaikan ambigu sebelum upload.</div></div>`;
    }
    out.push(`<div class="rejectitem"><b>Baris ${x.line}</b> • ${esc(x.invoiceNo||"(tanpa tagihan)")} • <b>${x.invoiceDate?fmtDate(x.invoiceDate):"TANGGAL TIDAK VALID"}</b> • ${esc(x.container||"(tanpa kontainer)")} • P${esc(x.period||"?")}${x.migrationMode?` • Sewa ${x.migrationStartDate?fmtDate(x.migrationStartDate):"?"}–${x.migrationEndDate?fmtDate(x.migrationEndDate):"AKTIF"}`:""} • ${x.amount==null?"Nominal tidak valid":money(x.amount)}<div>${state}</div>${extra}</div>`);
  }
  $("invoicePreview").innerHTML=out.join("")||'<div class="rejectitem muted">Tidak ada data.</div>';
};
$("processInvoices").onclick=async()=>{
  let rawInvoiceText=$("invoiceText").value||"";
  let firstNonEmpty=rawInvoiceText.split(/\r?\n/).map(v=>v.trim()).find(Boolean)||"";
  if(/^INV-[^|]+\|/i.test(firstNonEmpty)&&firstNonEmpty.split("|").length>=9){
    return alert("Format ini terlihat seperti Keputusan Siklus Ambigu, bukan format Impor Tagihan Vendor.\n\nPaste data tersebut ke Textarea Keputusan Ambigu, bukan ke Impor Tagihan.");
  }
  let rows=invoices(rawInvoiceText);if(!rows.length)return alert("Tidak ada baris.");
  let existing=await all("invoices"),expected=await combinedExpected(),rent=await legacyCycles(),masters=await all("masters"),valid=[],warnings=[],invalid=[],batchId="BATCH-"+new Date().toISOString().replace(/[-:TZ.]/g,"").slice(0,14);
  for(let r of rows){
    let hard=[];if(!r.vendor)hard.push("VENDOR WAJIB");if(!r.invoiceNo)hard.push("NO TAGIHAN WAJIB");if(!r.invoiceDate)hard.push("TANGGAL TAGIHAN TIDAK VALID");if(r.amount==null)hard.push("NOMINAL TIDAK VALID");if(!r.container)hard.push("CONTAINER WAJIB");if(!r.period)hard.push("PERIODE TIDAK VALID");if(r.migrationMode&&!r.migrationStartDate)hard.push("TGL SEWA DARI WAJIB/INVALID");if(r.migrationMode&&r.migrationEndRaw&&!r.migrationEndDate)hard.push("TGL SEWA SAMPAI INVALID");
    if(hard.length){invalid.push({...r,rejectReason:hard.join(" • ")});continue}
    let master=selectMasterVersion(masters,r.container,r.invoiceDate),mig=matchMigrationCycle(r,rent,expected),cand=r.migrationMode?(mig.cycle?[{r:mig.cycle,e:expected.find(e=>e.rentalId===mig.cycle.id&&Number(e.period||0)===Number(r.period||0))||null}]:[]):kandidatSiklusTagihan(r,expected,rent,existing),warning=[];
    if(!master)warning.push("CONTAINER TIDAK ADA DI MASTER");
    if(r.migrationMode){
      if(mig.matches.length===0)warning.push("SIKLUS HISTORI TIDAK COCOK DENGAN TGL SEWA DARI/SAMPAI");
      else if(mig.matches.length>1)warning.push("SIKLUS HISTORI DUPLIKAT UNTUK TGL SEWA DARI/SAMPAI");
      else {r.rentalId=mig.cycle.id;r.canonicalCycleId=mig.cycle.id;r.migrationMatchedCycleId=mig.cycle.id;r.migrationMatchedAt=now();}
    }else if(r.cycleChoice&&!cand[Number(r.cycleChoice)-1])warning.push(`NO KANDIDAT SIKLUS ${r.cycleChoice} TIDAK VALID`);else if(!r.cycleChoice&&cand.length>1)warning.push("SIKLUS AMBIGU");else if(cand.length===0)warning.push("SIKLUS RENTAL TIDAK DITEMUKAN");
    if(!warning.length&&cand.length===1&&!cand[0].e)warning.push(`PERIODE VENDOR P${r.period} TIDAK MEMILIKI HAK TAGIH PADA SIKLUS INI`);
    if(warning.length)warnings.push({...r,warningReason:warning.join(" • "),candidates:cand});else valid.push(r);
  }
  let accepted=0,pending=0;
  for(let r of valid){let o={...r,entryOrder:Number(r.line||0),id:uid("INV"),source:r.migrationMode?"IMPORT_REFERENSI_SIKLUS":"TEXTAREA_VENDOR",legacy:false,batchId,pranotaNo:"",pranotaNote:"",approvalStatus:"BELUM APPROVAL",importedAt:now(),paymentStatus:"BELUM DIBAYAR",paidAmount:0,paymentNo:"",paymentDate:""},a=await assess(o,[...existing,o],expected,rent);Object.assign(o,a);await put("invoices",o);existing.push(o);accepted++}
  // V7.0.6: WARNING adalah bukti vendor yang valid secara struktur. Simpan langsung sebagai history PENDING,
  // jangan biarkan 1000+ baris menggantung di textarea dan jangan hilangkan P Vendor asli.
  for(let r of warnings){let o={...r,entryOrder:Number(r.line||0),id:uid("INV"),source:r.migrationMode?"IMPORT_REFERENSI_SIKLUS":"TEXTAREA_VENDOR",legacy:false,batchId,pranotaNo:"",pranotaNote:"",approvalStatus:"BELUM APPROVAL",importedAt:now(),paymentStatus:"BELUM DIBAYAR",paidAmount:0,paymentNo:"",paymentDate:"",identityWarning:true,identityWarningReason:r.warningReason,forcedPendingByUser:true,warningQueueOpen:true,originalVendorPeriod:r.period,originalRaw:r.raw},a=await assess(o,[...existing,o],expected,rent);Object.assign(o,a);o.identityWarning=true;o.identityWarningReason=r.warningReason;o.forcedPendingByUser=true;o.warningQueueOpen=true;o.decision="PENDING";o.problems=[...new Set([...(o.problems||[]),...r.warningReason.split(" • ")])];await put("invoices",o);existing.push(o);pending++}
  // Hanya HARD INVALID yang tetap di textarea agar dapat diperbaiki tanpa kehilangan baris.
  $("invoiceText").value=invalid.map(x=>x.raw).join("\n");delete $("invoiceText").dataset.warningSignature;
  await put("oplog",auditLog("IMPOR TAGIHAN",batchId,today(),invalid.length?"TERCATAT":"DITERIMA",`${accepted} valid masuk • ${pending} warning disimpan sebagai history PENDING • ${invalid.length} invalid tetap di textarea • format dikenali otomatis per baris`,batchId));
  $("invoiceImportMsg").style.display="block";$("invoiceImportMsg").innerHTML=`<b>${accepted} detail VALID masuk.</b> ${pending?`<b>${pending} WARNING tersimpan sebagai HISTORY PENDING</b> sebagai PENDING/PR dan langsung tersedia di Tinjau Tagihan. `:""}${invalid.length?`<b>${invalid.length} INVALID tetap di textarea dan wajib diperbaiki.</b>`:""}`;
  await applyInvoiceGroupStatus();invalidateCaches();await refresh();show("invoiceimport");await renderWarningQueue();if(invalid.length)$("previewInvoices").click();
};

function warningType(x){return String(x.identityWarningReason||x.problems?.join(" • ")||"").toUpperCase()}
function isImportWarningProblem(p){let t=String(p||"").toUpperCase();return t.includes("SIKLUS AMBIGU")||t.includes("SIKLUS RENTAL TIDAK DITEMUKAN")||t.includes("SIKLUS RENTAL/PERIODE TIDAK DITEMUKAN")||t.includes("TIDAK MEMILIKI HAK TAGIH")||t.includes("CONTAINER TIDAK ADA DI MASTER")||t.includes("NO KANDIDAT")}
async function rebuildWarningQueueFromHistory(showAlert=false){
  let rows=await all("invoices"),opened=0,already=0,skipped=0;
  for(let x of rows){
    if(x.paymentStatus==="SUDAH DIBAYAR"){skipped++;continue}
    let ps=[...(x.problems||[])];if(x.identityWarningReason)ps.push(...String(x.identityWarningReason).split(" • "));
    let roots=[...new Set(ps.filter(isImportWarningProblem))];
    if(!roots.length){if(x.warningQueueOpen===true){x.warningQueueOpen=false;await put("invoices",x)}continue}
    if(x.warningQueueOpen===true){already++;continue}
    x.warningQueueOpen=true;x.identityWarning=true;x.identityWarningReason=roots.join(" • ");x.forcedPendingByUser=true;x.decision="PENDING";
    if(x.originalVendorPeriod==null)x.originalVendorPeriod=x.vendorPeriod||x.period||"";
    await put("invoices",x);opened++;
  }
  if(opened)await put("oplog",auditLog("BANGUN ULANG ANTREAN WARNING","HISTORY PENDING",today(),"DITERIMA",`${opened} warning direkonstruksi dari history tagihan; ${already} sudah aktif`,"V7.0.8"));
  invalidateCaches();
  if(showAlert)alert(`Antrean Warning dibangun ulang. ${opened} dibuka kembali • ${already} sudah aktif • ${skipped} PAID dilewati.`);
  return opened+already;
}
async function renderWarningQueue(){
  if(!$("warningQueue"))return;
  let allRows=await all("invoices"),rows=allRows.filter(x=>x.warningQueueOpen===true&&x.paymentStatus!=="SUDAH DIBAYAR");
  if(!rows.length){await rebuildWarningQueueFromHistory(false);allRows=await all("invoices");rows=allRows.filter(x=>x.warningQueueOpen===true&&x.paymentStatus!=="SUDAH DIBAYAR")}
  let q=norm($("warningQueueSearch")?.value||""),f=String($("warningQueueFilter")?.value||"").toUpperCase();
  let counts={ambigu:0,missing:0,noent:0,master:0};for(let x of rows){let t=warningType(x);if(t.includes("AMBIGU"))counts.ambigu++;if(t.includes("TIDAK DITEMUKAN"))counts.missing++;if(t.includes("TIDAK MEMILIKI HAK"))counts.noent++;if(t.includes("MASTER"))counts.master++}
  if($("warningQueueCount"))$("warningQueueCount").textContent=rows.length;
  if($("warningQueueSummary"))$("warningQueueSummary").innerHTML=`<b>Total ${rows.length}</b> • Ambigu ${counts.ambigu} • Cycle tidak ditemukan ${counts.missing} • Tidak berhak ${counts.noent} • Master ${counts.master}`;
  rows=rows.filter(x=>(!f||warningType(x).includes(f))&&(!q||norm(`${x.invoiceNo} ${x.container} ${x.vendor}`).includes(q))).sort((a,b)=>String(a.invoiceNo).localeCompare(String(b.invoiceNo))||String(a.container).localeCompare(String(b.container)));
  let expected=await combinedExpected(),rent=await legacyCycles(),existing=await all("invoices"),html="",last="";
  for(let x of rows.slice(0,300)){
    if(x.invoiceNo!==last){last=x.invoiceNo;html+=`<div class="timeline-head" style="margin-top:10px">No Tagihan ${esc(x.invoiceNo)} • ${esc(x.vendor||"")}</div>`}
    let cand=kandidatSiklusTagihan(x,expected,rent,existing),buttons=cand.length?cand.map((z,i)=>`<button class="mini" data-warning-cycle="${esc(x.id)}" data-cycle-no="${i+1}">Pilih ${i+1} • ${fmtDate(z.r.startDate)}</button>`).join(" "):"";
    buttons+=` <button class="mini primary" data-warning-review="${esc(x.invoiceNo)}">Tinjau No. Tagihan</button>`;
    html+=`<div class="rejectitem"><b>${esc(x.container)}</b> • P Vendor ${esc(x.originalVendorPeriod||x.vendorPeriod||x.period||"?")} • ${money(x.amount||0)}<div class="pending">${esc(x.identityWarningReason||"PENDING")}</div>${cand.length?`<div style="margin-top:5px">${htmlKandidatSiklus(cand)}<div class="actions">${buttons}</div></div>`:`<div class="small muted">Belum ada kandidat cycle. History vendor tetap tersimpan PENDING dan dapat direvalidasi setelah Rental diperbaiki.</div>`}</div>`;
  }
  if(rows.length>300)html+=`<div class="note">Menampilkan 300 dari ${rows.length} hasil filter. Gunakan pencarian / filter agar ringan.</div>`;
  $("warningQueue").innerHTML=html||'<div class="muted">Tidak ada warning pada filter ini.</div>';
  document.querySelectorAll("[data-warning-cycle]").forEach(b=>b.onclick=()=>resolveWarningCycle(b.dataset.warningCycle,Number(b.dataset.cycleNo)));
  document.querySelectorAll("[data-warning-review]").forEach(b=>b.onclick=async()=>{show("invoices");$("invoiceSearch").value=b.dataset.warningReview;invoicePage=1;reviewPage=1;await renderInv();});
}
async function resolveWarningCycle(id,no){
  let x=(await all("invoices")).find(v=>v.id===id);if(!x)return;let expected=await combinedExpected(),rent=await legacyCycles(),existing=await all("invoices"),cand=kandidatSiklusTagihan(x,expected,rent,existing),z=cand[no-1];if(!z)return alert("Kandidat cycle tidak lagi tersedia. Refresh dan periksa kembali.");
  x.cycleChoice=no;x.rentalId=z.r.id;x.canonicalCycleId=z.r.id;x.manualCycleConfirmed=true;x.userCycleDecisionAt=now();x.userCycleDecision=`PILIH KANDIDAT ${no}`;
  let a=await assess(x,existing,expected,rent);Object.assign(x,a);x.forcedPendingByUser=true;
  // Bila masalah relasi ambigu sudah selesai, queue ditutup hanya jika tidak ada root cause bisnis lain.
  let critical=(x.problems||[]).filter(p=>!String(p).includes("SIKLUS AMBIGU")&&!String(p).includes("PILIH NO KANDIDAT"));x.warningQueueOpen=critical.length>0;x.identityWarning=x.warningQueueOpen;x.identityWarningReason=critical.join(" • ");x.decision=critical.length?"PENDING":"READY TO PAY";
  await put("invoices",x);await put("oplog",auditLog("KEPUTUSAN WARNING TAGIHAN",x.invoiceNo,today(),critical.length?"TERCATAT":"DITERIMA",`${x.container} P${x.vendorPeriod}: user memilih cycle ambil ${fmtDate(z.r.startDate)}`,x.id));await applyInvoiceGroupStatus();invalidateCaches();await renderWarningQueue();
}
if($("warningQueueFilter"))$("warningQueueFilter").onchange=renderWarningQueue;
if($("rebuildWarningQueue"))$("rebuildWarningQueue").onclick=async()=>{await rebuildWarningQueueFromHistory(true);await renderWarningQueue()};
if($("warningQueueSearch"))$("warningQueueSearch").oninput=()=>debounce(renderWarningQueue);


async function buildAmbiguDecisionText(){
  let rows=(await all("invoices")).filter(x=>x.warningQueueOpen===true&&x.paymentStatus!=="SUDAH DIBAYAR"&&warningType(x).includes("AMBIGU"));
  let expected=await combinedExpected(),rent=await legacyCycles(),existing=await all("invoices");
  rows.sort((a,b)=>String(a.invoiceNo).localeCompare(String(b.invoiceNo))||String(a.container).localeCompare(String(b.container)));
  let out=["ID|NO TAGIHAN|CONTAINER|P VENDOR|NOMINAL|KANDIDAT 1|KANDIDAT 2|KANDIDAT 3|PILIH SISTEM|PILIH USER"];
  for(let x of rows){
    let cand=kandidatSiklusTagihan(x,expected,rent,existing),cs=[];
    for(let i=0;i<3;i++){
      let z=cand[i];
      cs.push(z?`${i+1}=${fmtDate(z.r.startDate)}~${z.r.returnDate?fmtDate(z.r.returnDate):"AKTIF"}~${z.e?`P${z.e.period} ADA ${fmtDate(z.e.startDate)}-${fmtDate(z.e.endDate)} ${money(Number(z.e.amount||0))}`:`P VENDOR TIDAK ADA (Expected s/d P${z.maxPeriod||0})`}`:"");
    }
    let sug=cand.findIndex(z=>z.suggested)+1;if(sug<1)sug="";
    out.push([x.id,x.invoiceNo,x.container,x.originalVendorPeriod||x.vendorPeriod||x.period||"",Number(x.amount||0),...cs,sug,""] .map(v=>String(v??"").replace(/[|\r\n]/g," ")).join("|"));
  }
  return out.join("\n");
}
async function loadAmbiguDecisionText(copyToo=false){
  let t=await buildAmbiguDecisionText();if($("ambiguDecisionText"))$("ambiguDecisionText").value=t;
  if(copyToo){try{await navigator.clipboard.writeText(t);alert("Data Siklus Ambigu sudah disalin. Paste ke Excel, isi kolom PILIH USER, lalu paste kembali ke Textarea Keputusan.");}catch(e){alert("Clipboard browser tidak tersedia. Data sudah dimuat ke Textarea Keputusan; Ctrl+A lalu Ctrl+C.");}}
}
async function processAmbiguBulk(){
  let raw=String($("ambiguDecisionText")?.value||"").trim();if(!raw)return alert("Textarea Keputusan masih kosong.");
  let lines=raw.split(/\r?\n/).filter(Boolean),start=/^ID\|/i.test(lines[0])?1:0,existing=await all("invoices"),expected=await combinedExpected(),rent=await legacyCycles(),byId=new Map(existing.map(x=>[String(x.id),x]));
  let ok=0,skip=0,errors=[];
  for(let n=start;n<lines.length;n++){
    let a=lines[n].split("|"),id=String(a[0]||"").trim(),choice=Number(String(a[9]||"").trim());
    if(!choice){skip++;continue}let x=byId.get(id);if(!x){errors.push(`Baris ${n+1}: ID tidak ditemukan`);continue}
    if(!x.warningQueueOpen||!warningType(x).includes("AMBIGU")){errors.push(`Baris ${n+1}: warning ambigu sudah tidak aktif`);continue}
    let cand=kandidatSiklusTagihan(x,expected,rent,existing),z=cand[choice-1];if(!z){errors.push(`Baris ${n+1}: pilihan ${choice} tidak valid`);continue}
    x.cycleChoice=choice;x.rentalId=z.r.id;x.canonicalCycleId=z.r.id;x.manualCycleConfirmed=true;x.userCycleDecisionAt=now();x.userCycleDecision=`BULK EXCEL PILIH KANDIDAT ${choice}`;
    let aa=await assess(x,existing,expected,rent);Object.assign(x,aa);x.forcedPendingByUser=true;
    let critical=(x.problems||[]).filter(p=>!String(p).includes("SIKLUS AMBIGU")&&!String(p).includes("PILIH NO KANDIDAT"));x.warningQueueOpen=critical.length>0;x.identityWarning=x.warningQueueOpen;x.identityWarningReason=critical.join(" • ");x.decision=critical.length?"PENDING":"READY TO PAY";
    await put("invoices",x);await put("oplog",auditLog("KEPUTUSAN WARNING MASSAL",x.invoiceNo,today(),critical.length?"TERCATAT":"DITERIMA",`${x.container} P${x.vendorPeriod}: Excel memilih cycle ${choice} ambil ${fmtDate(z.r.startDate)}`,x.id));ok++;
  }
  await applyInvoiceGroupStatus();invalidateCaches();await renderWarningQueue();
  let msg=`${ok} keputusan diproses • ${skip} baris tanpa PILIH USER dilewati`+(errors.length?` • ${errors.length} gagal.\n\n${errors.slice(0,20).join("\n")}`:"");alert(msg);
}
if($("loadAmbiguDecision"))$("loadAmbiguDecision").onclick=()=>loadAmbiguDecisionText(false);
if($("copyAmbiguDecision"))$("copyAmbiguDecision").onclick=()=>loadAmbiguDecisionText(true);
if($("processAmbiguDecision"))$("processAmbiguDecision").onclick=processAmbiguBulk;


// Entry / Perbaikan Tagihan.
// Perbaikan identitas kontainer dilakukan dengan melepas detail invoice, memperbaiki Master/Rental,
// lalu memasukkan kembali Expected Billing yang benar ke No. Tagihan yang sama.
let entryDraft=[],entryEdit=null;


function invoiceBusinessKey(x,masterList=[]){
  return [
    String(effectiveInvoiceVendor(x,masterList)||"").trim().toUpperCase(),
    String(x.invoiceNo||"").trim().toUpperCase(),
    String(x.container||"").trim().toUpperCase(),
    String(Number(x.period||0))
  ].join("|");
}
async function activeInvoiceRows(){
  let rows=await all("invoices"),masterList=await all("masters"),nonLegacy=new Set();
  for(let x of rows)if(!x.legacy)nonLegacy.add(invoiceBusinessKey(x,masterList));
  return rows.filter(x=>!x.legacy||!nonLegacy.has(invoiceBusinessKey(x,masterList)));
}

function effectiveInvoiceVendor(x,masterList=[]){
  if(x.vendor)return x.vendor;
  return selectMasterVersion(masterList,x.container,x.cycleStartDate||x.rentalStartEstimated||x.startDate||x.invoiceDate)?.vendor||"";
}
function setEntryHeaderLock(lock){
  $("eVendor").readOnly=lock;$("eInvoiceNo").readOnly=lock;$("eInvoiceDate").readOnly=lock;
}
function resetEntryInvoice(){
  entryDraft=[];entryEdit=null;
  ["eVendor","eInvoiceNo","eInvoiceDate","eContainer","ePeriod","eVendorStart","eVendorEnd","eAmount","eCycleChoice","entryExpectedSearch"].forEach(id=>$(id).value="");
  $("ePpn").value="1";$("ePph").value="1";$("ePpn").disabled=false;$("ePph").disabled=false;
  setEntryHeaderLock(false);
  $("entryModeLabel").textContent="Mode: TAGIHAN BARU";
  $("existingInvoiceDetails").innerHTML='<div class="rejectitem muted">Belum memuat tagihan lama.</div>';
  $("entryExpectedResults").innerHTML='<div class="rejectitem muted">Cari kontainer setelah Master dan Rental sudah diperbaiki.</div>';
  if($("entrySystemPreview"))$("entrySystemPreview").innerHTML='<div class="rejectitem muted">Pilih Container / Expected untuk melihat pembanding Sistem.</div>';
  $("entryInvoiceMsg").style.display="none";
  renderEntryDraft();
}
function renderEntryDraft(){
  let el=$("entryDraft");if(!el)return;
  el.innerHTML=entryDraft.length?entryDraft.map((x,i)=>`<div class="rejectitem"><b>${esc(x.container)}</b> • P Vendor ${x.period} • ${fmtDate(x.migrationStartDate)}–${fmtDate(x.migrationEndDate)} • ${money(x.amount)}${x.expectedId?`<div class="small muted"><b>Sistem:</b> Expected ${esc(x.expectedId)} • P${esc(x.systemPeriod||x.period)} • ${fmtDate(x.expectedStartDate)}–${fmtDate(x.expectedEndDate)} • ${money(Number(x.systemExpectedAmount||0))}</div>`:`<div class="small badtext"><b>Sistem:</b> P${esc(x.period)} tidak memiliki Expected/hak tagih pada Cycle terpilih — detail tetap dicatat sebagai PENDING untuk Review.</div>`} <button class="mini" data-entry-del="${i}" style="float:right">Hapus</button></div>`).join(""):'<div class="rejectitem muted">Belum ada detail yang akan ditambahkan.</div>';
  el.querySelectorAll("[data-entry-del]").forEach(b=>b.onclick=()=>{entryDraft.splice(Number(b.dataset.entryDel),1);renderEntryDraft()});
}

async function refreshEntryMasterChoices(){
  let ms=await all("masters"),vendor=String($("eVendor")?.value||"").trim().toUpperCase(),dk=$("daftarKontainer");
  if(!dk)return;
  let active=ms.filter(m=>m.isActive!==false),filtered=vendor?active.filter(m=>String(m.vendor||"").trim().toUpperCase()===vendor):active;
  let cs=[...new Set(filtered.map(x=>norm(x.container)).filter(Boolean))].sort();
  dk.innerHTML=cs.map(c=>{let m=filtered.find(x=>norm(x.container)===c)||{};return `<option value="${esc(c)}">${esc([m.size,m.type,m.vendor].filter(Boolean).join(" • "))}</option>`}).join("");
}
async function entryContainerChanged(){
  let c=norm($("eContainer").value);if(!c)return;
  let ms=await all("masters"),vendor=String($("eVendor").value||"").trim().toUpperCase(),known=ms.filter(m=>m.isActive!==false&&norm(m.container)===c);
  if(!known.length){if($("entrySystemPreview"))$("entrySystemPreview").innerHTML='<div class="rejectitem pending"><b>Container tidak ditemukan di Master aktif.</b></div>';return}
  if(vendor&&!known.some(m=>String(m.vendor||"").trim().toUpperCase()===vendor)){
    if($("entrySystemPreview"))$("entrySystemPreview").innerHTML=`<div class="rejectitem pending"><b>Periksa Vendor.</b> Container ${esc(c)} tidak tercatat pada Vendor ${esc($("eVendor").value)} di Master aktif. History cycle tetap menjadi bukti final.</div>`;
  }
  $("entryExpectedSearch").value=c;await searchEntryExpected();
}

async function entryInvoiceRows(vendor,invoiceNo){
  let masterList=await all("masters"),v=String(vendor||"").trim().toUpperCase(),n=String(invoiceNo||"").trim().toUpperCase();
  let active=(await all("invoices")).filter(x=>String(x.invoiceNo||"").trim().toUpperCase()===n&&String(effectiveInvoiceVendor(x,masterList)).trim().toUpperCase()===v);
  let detached=(await all("detachedInvoices")).filter(x=>String(x.invoiceNo||"").trim().toUpperCase()===n&&String(x.effectiveVendor||x.vendor||"").trim().toUpperCase()===v);
  return {active,detached,masterList};
}
async function renderEntryExisting(){
  if(!entryEdit)return;
  let {active,detached}=await entryInvoiceRows(entryEdit.vendor,entryEdit.invoiceNo);
  let rows=active.slice().sort((a,b)=>(a.container||"").localeCompare(b.container||"")||Number(a.period||0)-Number(b.period||0));
  let archived=detached.slice().sort((a,b)=>(b.detachedAt||"").localeCompare(a.detachedAt||""));
  let html="";
  if(rows.length){
    html+=`<div class="rejectitem"><b>Detail aktif (${rows.length})</b></div>`;
    html+=rows.map(x=>`<div class="rejectitem"><b>${esc(x.container)}</b> • P${esc(x.period||"?")} • ${money(x.amount||0)} ${badge(x.decision||"PENDING")}<div class="small muted">${fmtDate(x.startDate||x.systemStartDate)}–${fmtDate(x.endDate||x.systemEndDate)}${x.pranotaNo?` • Pranota ${esc(x.pranotaNo)}`:""}</div><button class="mini danger" data-detach-invoice="${esc(x.id)}">Lepas dari Tagihan</button></div>`).join("");
  }else html+='<div class="rejectitem pending"><b>Tidak ada detail aktif.</b> Tagihan tetap dapat diperbaiki dari arsip detail yang dilepas.</div>';
  if(archived.length){
    html+=`<div class="rejectitem"><b>Arsip detail dilepas (${archived.length})</b><div class="small muted">Tidak ikut Review/Pranota/Payment, tetapi tetap disimpan untuk audit.</div></div>`;
    html+=archived.slice(0,20).map(x=>`<div class="rejectitem"><span class="muted"><s>${esc(x.container)}</s> • P${esc(x.period||"?")} • ${money(x.amount||0)}</span><div class="small muted">Dilepas ${String(x.detachedAt||"").replace("T"," ").slice(0,16)}${x.detachReason?` • ${esc(x.detachReason)}`:""}</div></div>`).join("");
  }
  $("existingInvoiceDetails").innerHTML=html;
  document.querySelectorAll("[data-detach-invoice]").forEach(b=>b.onclick=()=>detachInvoiceDetail(b.dataset.detachInvoice));
}
async function loadExistingEntryInvoice(){
  let vendor=$("eVendor").value.trim(),invoiceNo=$("eInvoiceNo").value.trim();
  if(!vendor||!invoiceNo)return alert("Isi Vendor dan No. Tagihan yang akan diperbaiki.");
  let {active,detached}=await entryInvoiceRows(vendor,invoiceNo),source=active[0]||detached[0];
  if(!source)return alert("Tagihan tidak ditemukan. Pastikan Vendor dan No. Tagihan benar.");
  if(active.some(x=>x.paymentStatus==="SUDAH DIBAYAR"))return alert("Tagihan SUDAH DIBAYAR. Batalkan pembayaran terlebih dahulu.");
  entryEdit={vendor,invoiceNo,invoiceDate:source.invoiceDate||"",ppnEnabled:source.ppnEnabled!==false,pphEnabled:source.pphEnabled!==false};
  $("eVendor").value=vendor;$("eInvoiceNo").value=invoiceNo;$("eInvoiceDate").value=fmtDate(source.invoiceDate||"");
  $("ePpn").value=entryEdit.ppnEnabled?"1":"0";$("ePph").value=entryEdit.pphEnabled?"1":"0";
  $("ePpn").disabled=true;$("ePph").disabled=true;setEntryHeaderLock(true);
  $("entryModeLabel").textContent="Mode: PERBAIKAN TAGIHAN LAMA";
  entryDraft=[];renderEntryDraft();await renderEntryExisting();
  $("entryInvoiceMsg").style.display="block";$("entryInvoiceMsg").innerHTML=`Tagihan <b>${esc(invoiceNo)}</b> dimuat. Lepas hanya detail yang salah.`;
}
async function detachInvoiceDetail(id){
  let rows=await all("invoices"),x=rows.find(r=>r.id===id);if(!x)return alert("Detail tidak ditemukan.");
  if(x.paymentStatus==="SUDAH DIBAYAR")return alert("Detail SUDAH DIBAYAR. Batalkan pembayaran terlebih dahulu.");
  let same=rows.filter(r=>r.invoiceNo===x.invoiceNo&&String(r.vendor||"").toUpperCase()===String(x.vendor||"").toUpperCase());
  if(same.some(r=>r.paymentStatus==="SUDAH DIBAYAR"))return alert("Tagihan mempunyai detail SUDAH DIBAYAR. Batalkan pembayaran terlebih dahulu.");
  if(same.some(r=>r.pranotaNo))return alert("Tagihan sudah masuk Pranota. Batalkan Pranota terlebih dahulu.");
  let reason=prompt(`Alasan melepas ${x.container} P${x.period} dari tagihan ${x.invoiceNo}:`,"Salah No. Kontainer");
  if(reason===null)return;if(!reason.trim())return alert("Alasan wajib diisi.");
  if(!confirm(`Lepas ${x.container} P${x.period} dari ${x.invoiceNo}?\n\nDetail tidak dihapus; dipindahkan ke arsip audit.`))return;

  let archived={...x,id:uid("DETINV"),originalInvoiceId:x.id,effectiveVendor:x.vendor||entryEdit?.vendor||"",detachedAt:now(),detachReason:reason.trim(),detachedByWorkflow:"ENTRY_TAGIHAN_REPAIR"};
  await put("detachedInvoices",archived);
  await del("invoices",x.id);

  // Structural change invalidates approval for every remaining detail of the invoice.
  let remaining=(await all("invoices")).filter(r=>r.invoiceNo===x.invoiceNo&&String(r.vendor||"").toUpperCase()===String(x.vendor||"").toUpperCase());
  for(let r of remaining){r.approvalStatus="BELUM APPROVAL";r.approvedAt="";await put("invoices",r)}
  await put("oplog",auditLog("LEPAS DETAIL TAGIHAN",x.invoiceNo,today(),"DITERIMA",`${x.container} • P${x.period} • ${money(x.amount||0)} • ${reason.trim()}`,`Arsip ${archived.id}`));
  // V6.28.1: detach hanya mengubah relasi/evidence invoice. Jangan rebuild seluruh Expected.
  await applyInvoiceGroupStatus();invalidateCaches();
  await renderEntryExisting();
  $("entryInvoiceMsg").style.display="block";$("entryInvoiceMsg").innerHTML=`<b>${esc(x.container)} P${esc(x.period)}</b> sudah dilepas dan diarsipkan. Selanjutnya perbaiki Master/Rental, lalu tambahkan Expected yang benar.`;
}
async function searchEntryExpected(){
  let q=norm($("entryExpectedSearch").value||$("eContainer").value);
  if(!q)return alert("Isi No. Kontainer yang benar.");
  let exp=await combinedExpected(),cycles=Object.fromEntries((await legacyCycles()).map(r=>[r.id,r])),
      inv=await all("invoices"),used=new Set(inv.map(x=>x.expectedId).filter(Boolean));
  let rows=exp.filter(e=>e.container===q&&!used.has(e.id));
  rows.sort((a,b)=>Number(a.period||0)-Number(b.period||0));
  if(!rows.length){
    $("entryExpectedResults").innerHTML=`<div class="rejectitem pending">Expected Billing untuk <b>${esc(q)}</b> yang belum terpakai tidak ditemukan. Pastikan Master dan Pengambilan/Rental sudah dibuat dan Expected sudah terbentuk.</div>`;
    return;
  }
  $("entryExpectedResults").innerHTML=rows.map(e=>{
    let cycle=cycles[e.rentalId]||{},vendor=cycle.vendor||"",amount=Number(e.amount||0);
    return `<div class="rejectitem"><b>${esc(e.container)} • P${e.period}</b> • ${fmtDate(e.startDate)}–${fmtDate(e.endDate)} • ${money(amount)} ${badge(e.status||"BELUM DITAGIH")}<div class="small muted">Vendor Rental: ${esc(vendor||"-")} • Cycle ${esc(e.rentalId||"-")}</div><button class="mini primary" data-add-expected="${esc(e.id)}">Pakai sebagai Draft</button></div>`;
  }).join("");
  document.querySelectorAll("[data-add-expected]").forEach(b=>b.onclick=()=>addExpectedToEntry(b.dataset.addExpected));
}
async function addExpectedToEntry(expectedId){
  let e=(await combinedExpected()).find(x=>x.id===expectedId);if(!e)return alert("Expected tidak ditemukan.");
  if(entryDraft.some(x=>x.expectedId===expectedId))return alert("Expected ini sudah ada di draft.");
  $("eContainer").value=e.container||"";$("ePeriod").value=Number(e.period||0)||"";
  $("eVendorStart").value=fmtDate(e.startDate);$("eVendorEnd").value=fmtDate(e.endDate);$("eAmount").value=Number(e.amount||0);
  $("eCycleChoice").value="";
  if($("entrySystemPreview"))$("entrySystemPreview").innerHTML=`<div class="rejectitem"><b>SISTEM — ${esc(e.container)} • P${esc(e.period)}</b> • ${fmtDate(e.startDate)}–${fmtDate(e.endDate)} • Expected ${money(Number(e.amount||0))}<div class="small muted">Cycle ${esc(e.rentalId||"-")} • Nilai di atas hanya prefill. Edit field Vendor bila hard copy berbeda.</div></div>`;
  await tampilkanKandidatEntry();
  $("eVendorStart").focus();
}

async function tampilkanKandidatEntry(){
  let container=norm($("eContainer").value),period=Number($("ePeriod").value),amount=amt($("eAmount").value),
      invoiceDate=parseDate($("eInvoiceDate").value),el=$("entryCycleCandidates");
  if(!container||!period){el.innerHTML='<div class="rejectitem muted">Isi Container dan Periode untuk memeriksa siklus.</div>';return []}
  let vendorStart=parseDate($("eVendorStart").value),vendorEnd=parseDate($("eVendorEnd").value),
      expected=await combinedExpected(),rent=await legacyCycles(),existing=await all("invoices"),
      probe={container,period,vendorPeriod:period,amount,invoiceDate,migrationMode:!!vendorStart,migrationStartDate:vendorStart,migrationEndDate:vendorEnd},
      mig=vendorStart?matchMigrationCycle(probe,rent,expected):{matches:[],cycle:null},cand;
  if(vendorStart){
    const rr=mig.cycle?[mig.cycle]:mig.matches;
    cand=rr.map(r=>{let e=expected.find(e=>e.rentalId===r.id&&Number(e.period||0)===period)||null,entitlements=expected.filter(e=>e.rentalId===r.id);return {r,e,entitlements,maxPeriod:Math.max(0,...entitlements.map(e=>Number(e.period||0))),noEntitlement:!e,score:e?50:0}});
  }else cand=kandidatSiklusTagihan(probe,expected,rent,existing);
  if(!cand.length)el.innerHTML='<div class="rejectitem pending"><b>SIKLUS RENTAL/PERIODE TIDAK DITEMUKAN.</b> Tagihan tidak dapat disimpan sebelum siklus yang diperlukan tersedia.</div>';
  else if(cand.length===1)el.innerHTML=`<div class="rejectitem"><span class="goodtext">1 siklus cocok dari bukti P1/Pn.</span>${htmlKandidatSiklus(cand)}</div>`;
  else el.innerHTML=`<div class="rejectitem pending"><b>SIKLUS AMBIGU.</b> Referensi tanggal cocok ke lebih dari satu Cycle (termasuk benturan akhir Feb/awal Mar). Pilih No Kandidat.${htmlKandidatSiklus(cand)}</div>`;
  return cand;
}

$("addEntryDetail").onclick=async()=>{
  let container=norm($("eContainer").value),period=Number($("ePeriod").value),vendorStart=parseDate($("eVendorStart").value),vendorEnd=parseDate($("eVendorEnd").value),amount=amt($("eAmount").value),cycleChoice=Number($("eCycleChoice").value||0)||null;
  if(!container)return alert("Container wajib dipilih dari Master.");if(!period||period<1)return alert("P Vendor wajib P1 atau lebih.");if(!vendorStart||!vendorEnd)return alert("Periode Vendor Dari dan Sampai wajib diisi.");if(vendorEnd<vendorStart)return alert("Periode Vendor Sampai tidak boleh sebelum Dari.");if(amount==null||amount<0)return alert("Nominal Vendor tidak valid.");
  let masters=await all("masters"),known=masters.some(m=>norm(m.container)===container);if(!known)return alert("Container tidak ditemukan di Master. Tambahkan/perbaiki Master terlebih dahulu.");
  let cand=await tampilkanKandidatEntry();
  if(!cand.length)return alert("Siklus rental/periode tidak ditemukan. Tagihan belum dapat ditambahkan.");
  if(cand.length>1&&!cycleChoice)return alert("Siklus ambigu. Pilih No Kandidat yang tampil di bawah.");
  if(cycleChoice&&!cand[cycleChoice-1])return alert("No Kandidat tidak valid.");
  let chosen=cand[(cycleChoice||1)-1], ce=chosen.e||null;
  // Evidence vendor tetap boleh dicatat bila Cycle jelas tetapi Expected/P Vendor tidak berhak.
  // expectedId kosong disengaja: assess() akan memberi NO_ENTITLEMENT/PENDING untuk Review.
  entryDraft.push({container,period,vendorPeriod:period,originalVendorPeriod:period,migrationStartDate:vendorStart,migrationEndDate:vendorEnd,migrationMode:true,amount,cycleChoice:cycleChoice||1,rentalId:chosen.r.id,expectedId:ce?ce.id:"",expectedStartDate:ce?ce.startDate:"",expectedEndDate:ce?ce.endDate:"",systemPeriod:ce?Number(ce.period||period):null,systemExpectedAmount:ce?Number(ce.amount||0):0,noEntitlementEntry:!ce});
  $("eContainer").value="";$("ePeriod").value="";$("eVendorStart").value="";$("eVendorEnd").value="";$("eAmount").value="";$("eCycleChoice").value="";if($("entrySystemPreview"))$("entrySystemPreview").innerHTML='<div class="rejectitem muted">Pilih Container / Expected untuk melihat pembanding Sistem.</div>';renderEntryDraft();$("entryCycleCandidates").innerHTML='<div class="rejectitem muted">Jika siklus ambigu, daftar kandidat akan tampil di sini.</div>';$("eContainer").focus();
};
$("newEntryInvoice").onclick=resetEntryInvoice;
async function openEntryInvoicePicker(){
  $("entryPickInvoiceNo").value=$("eInvoiceNo").value.trim();
  $("entryPickQuery").value=$("eVendor").value.trim();
  await searchEntryInvoicePicker();
  $("entryInvoicePickerDlg").showModal();
}
async function searchEntryInvoicePicker(){
  let no=$("entryPickInvoiceNo").value.trim().toUpperCase(),
      q=$("entryPickQuery").value.trim().toUpperCase(),
      masterList=await all("masters"),rows=await all("invoices"),det=await all("detachedInvoices"),
      groups=new Map();
  for(let x of rows){
    let vendor=effectiveInvoiceVendor(x,masterList),key=`${vendor}|||${x.invoiceNo}`;
    if(!groups.has(key))groups.set(key,{vendor,invoiceNo:x.invoiceNo,invoiceDate:x.invoiceDate,active:0,detached:0,paid:false,containers:new Set()});
    let g=groups.get(key);g.active++;g.paid ||= x.paymentStatus==="SUDAH DIBAYAR";g.containers.add(x.container);
  }
  for(let x of det){
    let vendor=x.effectiveVendor||x.vendor||"",key=`${vendor}|||${x.invoiceNo}`;
    if(!groups.has(key))groups.set(key,{vendor,invoiceNo:x.invoiceNo,invoiceDate:x.invoiceDate,active:0,detached:0,paid:false,containers:new Set()});
    let g=groups.get(key);g.detached++;g.containers.add(x.container);
  }
  let list=[...groups.values()].filter(g=>{
    let hay=`${g.vendor} ${g.invoiceNo} ${[...g.containers].join(" ")}`.toUpperCase();
    return (!no||String(g.invoiceNo||"").toUpperCase().includes(no))&&(!q||hay.includes(q));
  }).sort((a,b)=>(b.invoiceDate||"").localeCompare(a.invoiceDate||"")||String(a.invoiceNo).localeCompare(String(b.invoiceNo))).slice(0,100);
  $("entryPickResults").innerHTML=list.length?list.map((g,i)=>`<div class="rejectitem"><b>${esc(g.invoiceNo)}</b> • ${esc(g.vendor||"-")} • ${fmtDate(g.invoiceDate)} ${g.paid?badge("SUDAH DIBAYAR"):badge("BELUM DIBAYAR")}<div class="small muted">${g.active} detail aktif${g.detached?` • ${g.detached} detail dilepas`:""} • ${esc([...g.containers].slice(0,5).join(", "))}${g.containers.size>5?" ...":""}</div><button type="button" class="mini primary" data-pick-old="${i}">Pilih Tagihan</button></div>`).join(""):'<div class="rejectitem muted">Tagihan tidak ditemukan. Coba cari No. Tagihan, vendor, atau No. Kontainer.</div>';
  document.querySelectorAll("[data-pick-old]").forEach(b=>b.onclick=async()=>{
    let g=list[Number(b.dataset.pickOld)];if(!g)return;
    $("eVendor").value=g.vendor;$("eInvoiceNo").value=g.invoiceNo;
    $("entryInvoicePickerDlg").close();
    await loadExistingEntryInvoice();
  });
}
$("loadExistingInvoice").onclick=openEntryInvoicePicker;
$("entryPickSearch").onclick=searchEntryInvoicePicker;
$("entryPickInvoiceNo").addEventListener("keydown",e=>{if(e.key==="Enter"){e.preventDefault();searchEntryInvoicePicker()}});
$("entryPickQuery").addEventListener("keydown",e=>{if(e.key==="Enter"){e.preventDefault();searchEntryInvoicePicker()}});
$("clearEntryInvoice").onclick=resetEntryInvoice;
$("searchEntryExpected").onclick=searchEntryExpected;
$("entryExpectedSearch").addEventListener("keydown",e=>{if(e.key==="Enter"){e.preventDefault();searchEntryExpected()}});
$("eVendor").addEventListener("change",refreshEntryMasterChoices);bindVendorAutocomplete("eVendor","eVendorSuggest",async()=>{await refreshEntryMasterChoices();$("eContainer").value="";});
$("eContainer").addEventListener("change",entryContainerChanged);
["ePeriod","eAmount","eInvoiceDate","eVendorStart","eVendorEnd"].forEach(id=>$(id).addEventListener("change",tampilkanKandidatEntry));
$("goMasterRepair").onclick=async()=>{show("master");await renderMaster();await renderRates()};
$("goTakeRepair").onclick=()=>{show("operasional");let b=document.querySelector('[data-op="take"]');if(b)b.click()};

$("saveEntryInvoice").onclick=async()=>{
  let vendor=$("eVendor").value.trim(),invoiceNo=$("eInvoiceNo").value.trim(),invoiceDate=parseDate($("eInvoiceDate").value);
  if(!vendor||!invoiceNo||!invoiceDate)return alert("Vendor, No. Tagihan, dan Tgl Tagihan wajib diisi.");
  let masterNow=await all("masters"),vendorKnown=!!(await activeVendorMaster(vendor));
  if(!entryEdit&&!vendorKnown)return alert("Vendor tidak ditemukan di Master Vendor aktif. Tambahkan/aktifkan Vendor di menu Master terlebih dahulu.");
  if(!entryDraft.length)return alert("Tambahkan minimal satu detail.");
  if(entryDraft.some(d=>!d.rentalId))return alert("Ada detail yang belum memiliki siklus pasti. Periksa daftar kandidat terlebih dahulu.");
  let existing=await all("invoices"),masterList=await all("masters");
  if(!entryEdit&&existing.some(x=>String(x.invoiceNo||"").toUpperCase()===invoiceNo.toUpperCase()&&String(effectiveInvoiceVendor(x,masterList)).toUpperCase()===vendor.toUpperCase()))return alert("No. Tagihan vendor ini sudah ada. Gunakan Muat Tagihan Lama untuk memperbaikinya.");
  if(entryEdit){
    let activeSame=existing.filter(x=>String(x.invoiceNo||"").toUpperCase()===invoiceNo.toUpperCase()&&String(effectiveInvoiceVendor(x,masterList)).toUpperCase()===vendor.toUpperCase());
    if(activeSame.some(x=>x.paymentStatus==="SUDAH DIBAYAR"))return alert("Tagihan sudah dibayar. Batalkan pembayaran terlebih dahulu.");
    if(activeSame.some(x=>x.pranotaNo))return alert("Batalkan Pranota terlebih dahulu sebelum menambah/mengubah detail.");
  }
  let expected=await combinedExpected(),rent=await legacyCycles(),batchId=(entryEdit?"REPAIR-":"ENTRY-")+new Date().toISOString().replace(/[-:TZ.]/g,"").slice(0,14),pending=0,added=0;
  let sameExisting=existing.filter(x=>String(x.invoiceNo||"").toUpperCase()===invoiceNo.toUpperCase()&&String(effectiveInvoiceVendor(x,masterList)).toUpperCase()===vendor.toUpperCase());
  let entryBase=sameExisting.reduce((m,x)=>Math.max(m,Number(x.entryOrder||0)),0);
  for(let draftIndex=0;draftIndex<entryDraft.length;draftIndex++){let d=entryDraft[draftIndex];
    if(d.expectedId&&existing.some(x=>x.expectedId===d.expectedId))return alert(`Expected ${d.expectedId} sudah dipakai tagihan lain.`);
    let o={...d,entryOrder:entryBase+draftIndex+1,id:uid("INV"),vendor,invoiceNo,invoiceDate,source:entryEdit?"ENTRY_REPAIR":"ENTRY_APP",legacy:false,batchId,pranotaNo:"",approvalStatus:"BELUM APPROVAL",ppnEnabled:$("ePpn").value==="1",ppnRate:11,pphEnabled:$("ePph").value==="1",pphRate:2,importedAt:now(),paymentStatus:"BELUM DIBAYAR",paidAmount:0,paymentNo:"",paymentDate:""};
    o.vendorPeriod=Number(d.vendorPeriod||d.period||0);o.originalVendorPeriod=d.originalVendorPeriod||o.vendorPeriod;o.migrationStartDate=d.migrationStartDate||"";o.migrationEndDate=d.migrationEndDate||"";o.migrationMode=!!(o.migrationStartDate&&o.migrationEndDate);
    let a=await assess(o,[...existing,o],expected,rent);Object.assign(o,a);
    // Preserve exact Expected identity selected by operator when assess can validate it.
    if(d.expectedId)o.expectedId=d.expectedId;
    if(d.rentalId)o.canonicalCycleId=d.rentalId;
    if(o.decision==="PENDING")pending++;
    await put("invoices",o);existing.push(o);added++;
  }
  // Any repair invalidates prior approval on all active details of this No. Tagihan.
  if(entryEdit){
    for(let x of existing.filter(x=>String(x.invoiceNo||"").toUpperCase()===invoiceNo.toUpperCase()&&String(effectiveInvoiceVendor(x,masterList)).toUpperCase()===vendor.toUpperCase())){
      x.approvalStatus="BELUM APPROVAL";x.approvedAt="";await put("invoices",x);
    }
  }
  await put("oplog",auditLog(entryEdit?"TAMBAH DETAIL TAGIHAN":"ENTRI TAGIHAN",invoiceNo,invoiceDate,"DITERIMA",`${added} detail ditambahkan • ${pending} detail bermasalah • belum approval`,vendor));
  entryDraft=[];renderEntryDraft();
  await applyInvoiceGroupStatus();await rebuild(false);
  $("entryInvoiceMsg").style.display="block";
  $("entryInvoiceMsg").innerHTML=entryEdit?`Perbaikan tagihan <b>${esc(invoiceNo)}</b> tersimpan. Lanjutkan ke Review Tagihan untuk review ulang dan approval.`:`Tagihan <b>${esc(invoiceNo)}</b> tersimpan dan masuk Tinjau Tagihan.`;
  if(entryEdit){await renderEntryExisting();await refresh()}else{resetEntryInvoice();await refresh();show("invoices")}
};
resetEntryInvoice();


function parsePranotaSelection(v){return [...new Set(String(v||"").split(/[|,;\n\r]+/).map(x=>x.trim()).filter(Boolean))]}
async function getPranotaSummary(nos){
  let prs=(await all("pranotas")).filter(p=>nos.includes(String(p.pranotaNo||""))),found=prs.map(p=>p.pranotaNo),missing=nos.filter(n=>!found.includes(n)),rows=await all("invoices"),selected=rows.filter(x=>found.includes(String(x.pranotaNo||""))),invoiceNos=[...new Set(prs.flatMap(p=>p.invoiceNos||[]))],hs=await invoiceHeaders();
  let related=hs.filter(h=>invoiceNos.includes(h.invoiceNo)),pending=related.filter(h=>h.status==="PENDING"),notApproved=related.filter(h=>h.approvalStatus!=="APPROVED"),alreadyPaid=related.filter(h=>h.status==="SUDAH DIBAYAR");
  let nett=prs.reduce((sum,p)=>sum+Number(p.nett||0),0);return{nos,prs,selected,found,missing,invoiceNos,hs:related,pending,notApproved,alreadyPaid,nett};
}
let paymentSelectedPranotas=new Set();
async function syncPaymentSelection(){
  let nos=[...paymentSelectedPranotas];$("pPranota").value=nos.join("|");
  if(!nos.length){loadedPayment=null;$("pPranotaCount").value="";$("pAmount").value="";$("pActualAmount").value="";$("pDifference").value="";$("paymentLoadInfo").textContent="Belum ada pranota dipilih.";return}
  let z=await getPranotaSummary(nos);loadedPayment=z;$("pPranotaCount").value=z.found.length;$("pAmount").value=Math.round(z.nett*100)/100;$("pActualAmount").value=Math.round(z.nett*100)/100;updatePaymentDifference();
  $("paymentLoadInfo").innerHTML=`<b>${z.found.length} pranota dipilih</b> • ${z.invoiceNos.length} invoice vendor • ${z.selected.length} detail • <b>Total NETT ${money(z.nett)}</b>`;
}

function updatePaymentDifference(){
  let nett=Number($("pAmount")?.value||0),actual=Number($("pActualAmount")?.value||0);
  if($("pDifference"))$("pDifference").value=Math.round((actual-nett)*100)/100;
}
function updatePaymentEditDifference(){
  let nett=Number($("peNett")?.value||0),actual=Number($("peActualAmount")?.value||0);
  if($("peDifference"))$("peDifference").value=Math.round((actual-nett)*100)/100;
}
if($("pActualAmount"))$("pActualAmount").oninput=updatePaymentDifference;
if($("peActualAmount"))$("peActualAmount").oninput=updatePaymentEditDifference;

async function eligiblePaymentPranotas(){
  let prs=(await all("pranotas")).filter(p=>!p.paymentId&&p.status!=="DIBAYAR"&&p.status!=="DIBATALKAN"),out=[];
  for(let p of prs){let z=await getPranotaSummary([p.pranotaNo]);if(!z.missing.length&&!z.pending.length&&!z.notApproved.length&&!z.alreadyPaid.length&&z.hs.length)out.push({...p,_invoiceCount:z.invoiceNos.length,_nett:z.nett})}
  return out.sort((a,b)=>(b.date||"").localeCompare(a.date||"")||String(a.pranotaNo).localeCompare(String(b.pranotaNo)));
}
async function renderPaymentPranotaCandidates(){
  if(!$("paymentPranotaCandidates"))return;let q=norm($("paymentPranotaSearch")?.value||""),rows=await eligiblePaymentPranotas();
  if(q)rows=rows.filter(p=>norm([p.pranotaNo,p.vendor,...(p.invoiceNos||[])].join(" ")).includes(q));
  $("paymentPranotaCandidates").innerHTML=rows.length?rows.map(p=>`<label class="rejectitem" style="display:grid;grid-template-columns:28px 1fr auto;gap:8px;align-items:center;cursor:pointer"><input type="checkbox" data-pay-pranota="${esc(p.pranotaNo)}" ${paymentSelectedPranotas.has(String(p.pranotaNo))?"checked":""}><div><b>${esc(p.pranotaNo)}</b> • ${esc(p.vendor||"")} • ${fmtDate(p.date)}<div class="small muted">${p._invoiceCount} invoice • ${esc((p.invoiceNos||[]).join(", "))}</div></div><b>${money(p._nett)}</b></label>`).join(""):'<div class="rejectitem muted">Tidak ada Pranota aktif yang eligible untuk dibayar.</div>';
  document.querySelectorAll("[data-pay-pranota]").forEach(c=>c.onchange=async()=>{c.checked?paymentSelectedPranotas.add(c.dataset.payPranota):paymentSelectedPranotas.delete(c.dataset.payPranota);await syncPaymentSelection()});
  return rows;
}
if($("paymentPranotaSearch"))$("paymentPranotaSearch").oninput=()=>debounce(renderPaymentPranotaCandidates);
if($("paymentSelectAll"))$("paymentSelectAll").onclick=async()=>{let rows=await renderPaymentPranotaCandidates();for(let p of rows)paymentSelectedPranotas.add(String(p.pranotaNo));await renderPaymentPranotaCandidates();await syncPaymentSelection()};
if($("paymentClearSelection"))$("paymentClearSelection").onclick=async()=>{paymentSelectedPranotas.clear();await renderPaymentPranotaCandidates();await syncPaymentSelection()};
$("paymentForm").addEventListener("submit",async e=>{
  e.preventDefault();let date=parseDate($("pDate").value),ref=$("pRef").value.trim(),nos=parsePranotaSelection($("pPranota").value);if(!date)return alert("Tanggal bayar tidak valid. Contoh: 09 Mei 23");if(!ref)return alert("No. Bukti Bayar wajib diisi.");if(!nos.length)return alert("Muat No. Pranota terlebih dahulu.");let s=await getPranotaSummary(nos);if(s.missing.length)return alert("Ada pranota tidak ditemukan: "+s.missing.join(", "));if(s.pending.length)return alert("Pembayaran diblokir. Invoice masih PENDING: "+s.pending.map(h=>h.invoiceNo).join(", "));if(s.notApproved.length)return alert("Pembayaran diblokir. Invoice belum APPROVED: "+s.notApproved.map(h=>h.invoiceNo).join(", "));if(s.alreadyPaid.length)return alert("Ada invoice yang sudah dibayar: "+s.alreadyPaid.map(h=>h.invoiceNo).join(", "));if(!s.hs.length)return alert("Tidak ada invoice READY TO PAY.");
  let total=Number(s.nett||0),actualAmount=Number($("pActualAmount").value);if(!Number.isFinite(actualAmount)||actualAmount<0)return alert("Total Bayar Aktual tidak valid.");let difference=Math.round((actualAmount-total)*100)/100,paymentId=uid("PAY"),p={id:paymentId,pranotaNos:nos,invoiceNos:s.invoiceNos,date,amount:total,actualAmount,difference,reference:ref,note:$("pNote").value.trim(),source:"INPUT_APP",status:"AKTIF",createdAt:now()};await put("payments",p);
  for(let x of s.selected){x.paymentStatus="SUDAH DIBAYAR";x.paymentNo=ref;x.paymentDate=date;x.paymentId=paymentId;x.decision="SUDAH DIBAYAR";await put("invoices",x)}for(let pr of (await all("pranotas")).filter(x=>nos.includes(x.pranotaNo))){pr.status="DIBAYAR";pr.paymentId=paymentId;pr.paymentNo=ref;pr.paymentDate=date;await put("pranotas",pr)}await put("oplog",auditLog("PAYMENT",ref,date,"DITERIMA",`${nos.length} pranota • ${s.invoiceNos.length} invoice • NETT ${money(total)} • BAYAR ${money(actualAmount)} • SELISIH ${money(difference)}`,nos.join("|")));loadedPayment=null;paymentSelectedPranotas.clear();e.target.reset();$("pPranota").value="";$("pPranotaCount").value="";$("pAmount").value="";$("pActualAmount").value="";$("pDifference").value="";$("paymentLoadInfo").textContent="Belum ada pranota dipilih.";$("pDate").value=fmtDate(today());await rebuild(false);await refresh();
});

function dl(n,t,type="text/plain"){let b=new Blob([t],{type}),u=URL.createObjectURL(b),a=document.createElement("a");a.href=u;a.download=n;a.click();setTimeout(()=>URL.revokeObjectURL(u),1000)}
function pv(v){let s=String(v??"");return /[|\r\n"]/.test(s)?`"${s.replace(/"/g,'""')}"`:s}
function pipeCsv(n,h,r){dl(n,"\ufeff"+[h.map(pv).join("|"),...r.map(x=>x.map(pv).join("|"))].join("\n"),"text/csv;charset=utf-8")}

async function legacyCycles(){
  if(cyclesCache)return cyclesCache;
  let overrides=await getOverrides(),r=(await all("rentals")).map(x=>effectiveRental({...x},overrides));
  let by={};for(let x of r)(by[x.container]??=[]).push(x);
  for(let arr of Object.values(by)){
    arr.sort((a,b)=>a.startDate.localeCompare(b.startDate));
    for(let i=0;i<arr.length;i++){
      let next=arr[i+1];arr[i].inferredEnd=!arr[i].returnDate&&next?addDays(next.startDate,-1):"";
      arr[i].status=cycleStatus(arr[i]);arr[i].cycleFlag=arr[i].cycleFlag||"TAKE";arr[i].cycleModel="TAKE_BASED_V1";
    }
  }
  cyclesCache=Object.values(by).flat().sort((a,b)=>(b.startDate||"").localeCompare(a.startDate||""));return cyclesCache;
}async function combinedExpected(){
  if(expectedCache)return expectedCache;
  let exp=await all("expected"),inv=await all("invoices"),result=[];
  for(let e of exp){
    let rs=inv.filter(x=>(x.rentalId||x.canonicalCycleId)===e.rentalId&&Number(x.period||0)===Number(e.period||0));
    let status=e.status,invoiceNo="",reason="",displayAmount=Number(e.amount||0)>0?Number(e.amount):null,outstandingAmount=0;
    if(rs.length){
      invoiceNo=[...new Set(rs.map(x=>x.invoiceNo).filter(Boolean))].join(", ");
      if(displayAmount==null)displayAmount=rs.reduce((s,x)=>s+Number(x.systemExpectedAmount??x.amount??0),0);
      outstandingAmount=rs.filter(x=>x.paymentStatus!=="SUDAH DIBAYAR").reduce((s,x)=>s+Number(x.approvedAmount??approvedFromSystem(x)),0);
      if(rs.every(x=>x.paymentStatus==="SUDAH DIBAYAR"))status="SUDAH DIBAYAR";
      else {status="OUTSTANDING";reason=rs.some(x=>x.decision==="PENDING")?"Sudah ditagih tetapi invoice masih PENDING.":"Sudah ditagih dan belum tercatat dibayar."}
    }else if(status==="BELUM DITAGIH")reason="Expected belum memiliki tagihan vendor.";
    result.push({...e,amount:displayAmount,outstandingAmount,status,invoiceNo,reason});
  }
  expectedCache=result;return expectedCache;
}
$("exportRentals").onclick=async()=>{
  let r=await legacyCycles();
  pipeCsv("rentals.csv",["ID","KONTAINER","FLAG","AMBIL","KEMBALI","TARIF","BASIS","UKURAN","JENIS","VENDOR","STATUS"],r.map(x=>[x.id,x.container,x.cycleFlag||"TAKE",fmtDate(x.startDate),fmtDate(x.returnDate),x.rate,x.basis,x.size,x.type,x.vendor,x.status]));
};
$("exportExpected").onclick=async()=>{
  let r=await filteredExpected();
  pipeCsv("expected_billing.csv",["ID_DETAIL","KONTAINER","PERIODE","MULAI","AKHIR","NOMINAL","STATUS","INVOICE","ALASAN"],r.map(x=>[x.id,x.container,x.period,fmtDate(x.startDate),fmtDate(x.endDate),x.amount??"",x.status,x.invoiceNo||"",x.reason||""]));
};
$("exportInvoices").onclick=async()=>{
  let {rows:r,masters}=await getFilteredInvoices();
  pipeCsv("review_tagihan_filter.csv",["PRANOTA","INVOICE","VENDOR","KONTAINER","UKURAN","JENIS","PERIODE","TGL_TAGIHAN","NOMINAL_SUMBER_AUDIT","EXPECTED_SISTEM","KOREKSI_MANUAL","DISETUJUI","STATUS_INVOICE","MASALAH","BUKTI_TRACE","KETERANGAN_KOREKSI","NO_BUKTI_BAYAR","TGL_BAYAR"],r.map(x=>[x.pranotaNo||x.batchId||"",x.invoiceNo,x.vendor||selectMasterVersion(masterList,x.container,x.cycleStartDate||x.startDate||x.invoiceDate)?.vendor||"",x.container,selectMasterVersion(masterList,x.container,x.cycleStartDate||x.startDate||x.invoiceDate)?.size||"",selectMasterVersion(masterList,x.container,x.cycleStartDate||x.startDate||x.invoiceDate)?.type||"",x.period,fmtDate(x.invoiceDate),x.amount,x.systemExpectedAmount??x.amount,x.adjustment||0,x.approvedAmount??approvedFromSystem(x),x.decision,(x.problems||[]).join(" / "),[
    x.issueEvidence?.expected?.id?`Expected ${x.issueEvidence.expected.id} ${money(x.issueEvidence.expected.amount||0)}`:"",
    ...(x.issueEvidence?.duplicateInvoices||[]).map(d=>`Dup ${d.invoiceNo}`),
    ...(x.issueEvidence?.paidInvoices||[]).map(d=>`Paid ${d.invoiceNo} Bukti ${d.paymentNo||"-"} ${fmtDate(d.paymentDate)}`)
  ].filter(Boolean).join(" | "),x.adjustmentNote||"",x.paymentNo,fmtDate(x.paymentDate)]));
};
$("exportPayments").onclick=async()=>{let r=await all("payments");pipeCsv("pembayaran.csv",["TGL_BAYAR","NO_BUKTI_BAYAR","PRANOTA","INVOICE_VENDOR","TOTAL_NETT","TOTAL_BAYAR","SELISIH","KETERANGAN","SUMBER"],r.map(x=>{let actual=Number(x.actualAmount??x.amount??0),diff=Number(x.difference??(actual-Number(x.amount||0)));return[fmtDate(x.date),x.reference,(x.pranotaNos||[]).join(", ")||x.pranotaNo||"",(x.invoiceNos||[]).join(", ")||x.invoiceNo||"",x.amount,actual,diff,x.note||"",x.source||""]}))};
$("exportAudit").onclick=async()=>{
  let r=await combinedAudit();pipeCsv("log_audit.csv",["WAKTU_SUMBER","JENIS","REFERENSI","TANGGAL","STATUS","KETERANGAN","DATA_ASLI"],r.map(x=>[x.displayTime,x.type,x.ref,x.dateDisplay,x.status,x.reason,x.raw]));
};

async function backup(){let o={version:700,workflow:"CLEAN_REBUILD_V1",cycleModel:"TAKE_BASED_V2",app:"Container Billing Control",appVersion:APP_VERSION,exportedAt:now()};for(let s of S)o[s]=await all(s);return o}
function backupFilename(){
  let n=$("backupName").value.trim()||`backup_container_${today()}.json`;
  if(!n.toLowerCase().endsWith(".json"))n+=".json";return n;
}
$("backupBtn").onclick=async()=>dl(backupFilename(),JSON.stringify(await backup(),null,2),"application/json");
$("backupLocationBtn").onclick=async()=>{
  let data=JSON.stringify(await backup(),null,2),name=backupFilename();
  if("showSaveFilePicker" in window){
    try{
      let h=await window.showSaveFilePicker({suggestedName:name,types:[{description:"Backup JSON",accept:{"application/json":[".json"]}}]});
      let w=await h.createWritable();await w.write(data);await w.close();alert("Backup berhasil disimpan ke lokasi yang dipilih.");
    }catch(e){if(e.name!=="AbortError")alert("Gagal menyimpan: "+e.message)}
  }else{dl(name,data,"application/json");alert("Browser tidak mendukung pemilih lokasi. File dikirim melalui Download biasa.")}
};
async function restoreData(d){if(!confirm("Restore akan MENGGANTI database sekarang. Lanjut?"))return;await replace(d);await reconcileSiklusReference(true);await normalizeTakeBasedCycles();await rebuild(false);await reReviewUnpaidInvoices();await refresh();alert("Restore selesai.")}
$("restoreBtn").onclick=async()=>{let f=$("restoreFile").files[0];if(!f)return alert("Pilih file JSON.");let d;try{d=JSON.parse(await f.text())}catch(e){return alert("JSON tidak valid.")}await restoreData(d)};
$("restorePickerBtn").onclick=async()=>{
  if("showOpenFilePicker" in window){
    try{let [h]=await window.showOpenFilePicker({multiple:false,types:[{description:"Backup JSON",accept:{"application/json":[".json"]}}]}),f=await h.getFile(),d=JSON.parse(await f.text());await restoreData(d)}
    catch(e){if(e.name!=="AbortError")alert("Gagal membuka backup: "+e.message)}
  }else $("restoreFile").click();
};
$("restoreBaseline").onclick=async()=>{if(!confirm("Kembalikan database ke DATA AWAL?"))return;await replace(window.BASELINE_DATA||{});await reconcileSiklusReference(true);await normalizeTakeBasedCycles();await rebuild(false);await reReviewUnpaidInvoices();await refresh();alert("Data awal berhasil direstore.")};


function intervalOverlap(a1,a2,b1,b2){
  let loA=a1||"0000-01-01",hiA=a2||"9999-12-31",loB=b1||"0000-01-01",hiB=b2||"9999-12-31";
  return loA<=hiB&&loB<=hiA;
}
function identityEvidenceLink(view,label,container){
  return `<button class="mini" data-identity-jump="${view}" data-identity-container="${esc(container)}">${label}</button>`;
}
async function simulateIdentityCorrection(source,target){
  source=norm(source);target=norm(target);
  if(!source||!target)return {status:"BLOKIR",conflicts:[{type:"INPUT",text:"Identitas sumber dan tujuan wajib diisi."}]};
  if(source===target)return {status:"BLOKIR",conflicts:[{type:"INPUT",text:"Identitas sumber dan tujuan sama."}]};

  let [masters,rentals,expected,invoices,detached,payments,pranotas]=await Promise.all([
    all("masters"),all("rentals"),all("expected"),all("invoices"),all("detachedInvoices"),all("payments"),all("pranotas")
  ]);
  let sm=masters.filter(x=>x.container===source),tm=masters.filter(x=>x.container===target),
      sr=rentals.filter(x=>x.container===source),tr=rentals.filter(x=>x.container===target),
      se=expected.filter(x=>x.container===source),te=expected.filter(x=>x.container===target),
      si=invoices.filter(x=>x.container===source),ti=invoices.filter(x=>x.container===target),
      sd=detached.filter(x=>x.container===source),td=detached.filter(x=>x.container===target),
      conflicts=[],warnings=[];

  if(!sm.length&&!sr.length&&!si.length&&!se.length)
    conflicts.push({type:"SUMBER",text:`Tidak ada data terkait ${source}.`});

  // Master versions that would overlap after rename.
  for(let a of sm)for(let b of tm){
    if(intervalOverlap(a.validFrom,a.validTo,b.validFrom,b.validTo))
      conflicts.push({type:"MASTER DUPLIKAT",text:`Versi Master akan overlap: ${source} ${fmtDate(a.validFrom)||"awal"}–${fmtDate(a.validTo)||"sekarang"} dengan ${target} ${fmtDate(b.validFrom)||"awal"}–${fmtDate(b.validTo)||"sekarang"}.`,a,b});
  }

  // Rental cycles that were safe only because the identity differed.
  for(let a of sr)for(let b of tr){
    let ae=a.returnDate||a.inferredEnd||"9999-12-31",be=b.returnDate||b.inferredEnd||"9999-12-31";
    if(intervalOverlap(a.startDate,ae,b.startDate,be))
      conflicts.push({type:"DOUBLE RENTAL / OVERLAP",text:`${a.id}: ${fmtDate(a.startDate)}–${fmtDate(a.returnDate||a.inferredEnd)||"aktif"} bertabrakan dengan ${b.id}: ${fmtDate(b.startDate)}–${fmtDate(b.returnDate||b.inferredEnd)||"aktif"}.`,a,b});
  }

  // Expected that becomes the same physical period after identity correction.
  for(let a of se)for(let b of te){
    if(a.startDate&&b.startDate&&a.endDate&&b.endDate&&a.startDate===b.startDate&&a.endDate===b.endDate&&a.id!==b.id)
      conflicts.push({type:"DUPLIKAT EXPECTED",text:`Periode ${fmtDate(a.startDate)}–${fmtDate(a.endDate)} sudah ada pada kedua identitas (${a.id} ↔ ${b.id}).`,a,b});
  }

  // Invoice details that become indistinguishable in the same vendor invoice + period.
  let ml=masters;
  for(let a of si)for(let b of ti){
    let va=String(effectiveInvoiceVendor(a,ml)||a.vendor||"").toUpperCase(),
        vb=String(effectiveInvoiceVendor(b,ml)||b.vendor||"").toUpperCase();
    if(String(a.invoiceNo||"").toUpperCase()===String(b.invoiceNo||"").toUpperCase()&&
       va===vb&&Number(a.period||0)===Number(b.period||0)&&a.id!==b.id)
      conflicts.push({type:"DUPLIKAT DETAIL TAGIHAN",text:`${a.invoiceNo} P${a.period}: detail ${a.id} dan ${b.id} akan menjadi container yang sama.`,a,b});
  }

  let paid=si.filter(x=>String(x.paymentStatus||"").includes("DIBAYAR")||x.paymentNo);
  let approved=si.filter(x=>x.approvalStatus==="APPROVED");
  let pranota=si.filter(x=>x.pranotaNo);
  if(paid.length)warnings.push(`${paid.length} detail PAID ikut koreksi identitas; nominal, pajak, bukti bayar dan status pembayaran tidak akan diubah.`);
  if(approved.length)warnings.push(`${approved.length} detail APPROVED ikut koreksi identitas; approval dipertahankan karena Cycle/Expected tidak dipindahkan.`);
  if(pranota.length)warnings.push(`${pranota.length} detail sudah terkait Pranota; nomor Pranota tidak diubah.`);

  return {
    source,target,status:conflicts.length?"BLOKIR":"AMAN",conflicts,warnings,
    counts:{master:sm.length,rental:sr.length,expected:se.length,invoice:si.length,detached:sd.length,
      paid:paid.length,approved:approved.length,pranota:pranota.length,targetMaster:tm.length,targetRental:tr.length,targetExpected:te.length,targetInvoice:ti.length}
  };
}
function renderIdentityImpact(s){
  identitySimulation=s;
  $("identityApply").disabled=s.status!=="AMAN";
  let c=s.counts||{},head=`<div class="rejectitem"><b>HASIL SIMULASI: ${badge(s.status)}</b><div class="small">Sumber ${esc(s.source||"")} → Tujuan ${esc(s.target||"")}</div></div>`;
  let counts=c.master!==undefined?`<div class="rejectitem"><b>Bukti terdampak</b><div>Master ${c.master} • Rental/Siklus ${c.rental} • Expected ${c.expected} • Tagihan ${c.invoice} • Detail dilepas ${c.detached} • APPROVED ${c.approved} • Pranota ${c.pranota} • PAID ${c.paid}</div><div class="actions">${identityEvidenceLink("master","Buka Master",s.target)} ${identityEvidenceLink("rentals","Buka Rental",s.source)} ${identityEvidenceLink("expected","Buka Expected",s.source)} ${identityEvidenceLink("invoices","Buka Tagihan",s.source)} ${identityEvidenceLink("payments","Buka Pembayaran",s.source)}</div></div>`:"";
  let warns=(s.warnings||[]).map(x=>`<div class="rejectitem"><b>INFORMASI</b><div>${esc(x)}</div></div>`).join("");
  let conflicts=(s.conflicts||[]).map(x=>`<div class="rejectitem"><b class="badtext">${esc(x.type)}</b><div>${esc(x.text)}</div></div>`).join("");
  $("identityImpact").innerHTML=head+counts+warns+(conflicts||`<div class="rejectitem"><b class="goodtext">Tidak ditemukan konflik baru.</b><div>Koreksi hanya mengganti identitas container pada referensi terkait; hubungan Cycle/Expected dan data finansial tetap.</div></div>`);
  document.querySelectorAll("[data-identity-jump]").forEach(b=>b.onclick=()=>{
    let v=b.dataset.identityJump,q=b.dataset.identityContainer;show(v);
    if(v==="master"){$("masterSearch").value=q;masterPage=1;renderMaster()}
    if(v==="rentals"){$("rentalSearch").value=q;renderRent()}
    if(v==="expected"){$("expectedSearch").value=q;renderExpected()}
    if(v==="invoices"){$("invoiceSearch").value=q;renderInv()}
  });
}
async function applyIdentityCorrection(){
  let s=identitySimulation;if(!s||s.status!=="AMAN")return alert("Jalankan simulasi sampai status AMAN.");
  let latest=await simulateIdentityCorrection(s.source,s.target);
  if(latest.status!=="AMAN"){renderIdentityImpact(latest);return alert("Kondisi data berubah. Koreksi diblokir; cek bukti terbaru.");}
  if(!confirm(`Koreksi identitas ${s.source} → ${s.target}?\\n\\nSeluruh referensi container terkait akan diperbarui. Cycle ID, Expected ID, nominal, pajak, approval, pranota dan pembayaran tidak diubah.`))return;

  let stores=["masters","rentals","expected","invoices","detachedInvoices","rentalOverrides"],changed={};
  for(let store of stores){
    let rows=await all(store),n=0;
    for(let x of rows){
      let hit=x.container===s.source;
      if(!hit&&store==="rentalOverrides"&&x.oldContainer===s.source){x.oldContainer=s.target;hit=true}
      if(hit){x.container=s.target;x.identityCorrectedFrom=s.source;x.identityCorrectedAt=now();await put(store,x);n++}
    }
    changed[store]=n;
  }
  await put("oplog",auditLog("KOREKSI IDENTITAS CONTAINER",`${s.source}→${s.target}`,today(),"DITERIMA",
    `Simulasi AMAN. Master ${changed.masters||0}, Rental ${changed.rentals||0}, Expected ${changed.expected||0}, Tagihan ${changed.invoices||0}. Data finansial tidak diubah.`,
    pipeLine([s.source,s.target,JSON.stringify(changed)])));
  invalidateCaches();
  await rebuild(false);await reReviewUnpaidInvoices();
  let post=await simulateIdentityCorrection(s.source,s.target);
  // Sumber seharusnya sudah kosong. Konflik baru pada tujuan dicegah oleh pre-check; tampilkan audit hasil akhir.
  await put("oplog",auditLog("VALIDASI PASCA KOREKSI",s.target,today(),post.conflicts?.length?"PERLU REVIEW":"DITERIMA",
    post.conflicts?.length?post.conflicts.map(x=>x.type+": "+x.text).join(" • "):"Tidak ditemukan sisa referensi sumber / konflik baru dari koreksi identitas.",""));
  await refresh();$("identityCorrectionDlg").close();
  alert(`Koreksi identitas selesai: ${s.source} → ${s.target}.\\nMaster ${changed.masters||0} • Rental ${changed.rentals||0} • Expected ${changed.expected||0} • Tagihan ${changed.invoices||0}.`);
}
async function renderMaster(){
  await renderVendorMaster();
  let allm=(await all("masters")).sort((a,b)=>a.container.localeCompare(b.container)||(b.validFrom||"").localeCompare(a.validFrom||"")),
      q=norm($("masterSearch")?.value||""),filter=$("masterFilter")?.value||"",size=Number($("masterPageSize")?.value||50),
      counts={};
  for(let x of allm)counts[x.container]=(counts[x.container]||0)+1;
  let r=allm;
  if(q)r=r.filter(x=>[x.container,x.vendor,x.size,x.type].some(v=>String(v||"").toUpperCase().includes(q)));
  if(filter==="DUPLIKAT")r=r.filter(x=>counts[x.container]>1);
  if(filter==="AKTIF")r=r.filter(x=>x.isActive!==false&&!x.validTo);
  if(filter==="NONAKTIF")r=r.filter(x=>x.isActive===false||!!x.validTo);
  let pg=pageSlice(r,masterPage,size);masterPage=pg.page;
  let containers=new Set(allm.map(x=>x.container));
  $("masterCount").textContent=`${containers.size} nomor kontainer • ${allm.length} versi master.`;
  $("masterPageInfo").textContent=`Halaman ${pg.page} dari ${pg.pages} • ${pg.total} versi`;
  $("masterPrev").disabled=pg.page<=1;$("masterNext").disabled=pg.page>=pg.pages;
  $("masterRows").innerHTML=pg.rows.length?pg.rows.map(x=>{
    let active=x.isActive!==false&&!x.validTo,dup=counts[x.container]>1?` <span class="badge warn">DUPLIKAT ×${counts[x.container]}</span>`:"";
    return `<tr><td><b>${esc(x.container)}</b>${dup}</td><td>${esc(x.size)}</td><td>${esc(x.type)}</td><td>${esc(x.vendor)}</td><td>${fmtDate(x.validFrom)||"Histori/awal"}</td><td>${fmtDate(x.validTo)||"-"}</td><td>${badge(active?"AKTIF":"NONAKTIF")}</td><td class="wrap">${esc(x.note||"")}</td><td><button class="mini" data-edit-master="${esc(x.id)}">Edit</button> <button class="mini" data-correct-identity="${esc(x.container)}">Koreksi Identitas</button> ${active?`<button class="mini danger" data-disable-master="${esc(x.id)}">Nonaktifkan</button>`:""}</td></tr>`;
  }).join(""):`<tr><td colspan="9" class="muted">Tidak ada Master sesuai pencarian/filter.</td></tr>`;
  document.querySelectorAll("[data-edit-master]").forEach(b=>b.onclick=()=>openMasterEdit(b.dataset.editMaster));
  document.querySelectorAll("[data-disable-master]").forEach(b=>b.onclick=()=>disableMaster(b.dataset.disableMaster));
  document.querySelectorAll("[data-correct-identity]").forEach(b=>b.onclick=()=>{
    $("identitySource").value=b.dataset.correctIdentity;$("identityTarget").value="";
    identitySimulation=null;$("identityApply").disabled=true;
    $("identityImpact").innerHTML='<div class="rejectitem muted">Masukkan identitas tujuan lalu jalankan simulasi.</div>';
    $("identityCorrectionDlg").showModal();
  });
}
$("masterSearch").oninput=()=>{masterPage=1;renderMaster()};
$("masterFilter").onchange=()=>{masterPage=1;renderMaster()};
$("masterPageSize").onchange=()=>{masterPage=1;renderMaster()};
$("masterPrev").onclick=()=>{masterPage--;renderMaster()};
$("masterNext").onclick=()=>{masterPage++;renderMaster()};
$("identitySimulate").onclick=async()=>renderIdentityImpact(await simulateIdentityCorrection($("identitySource").value,$("identityTarget").value));
$("identityRecheck").onclick=$("identitySimulate").onclick;
$("identityApply").onclick=applyIdentityCorrection;
async function openMasterEdit(id){
  let m=(await all("masters")).find(x=>x.id===id);if(!m)return;
  $("mMasterId").value=m.id;$("mContainer").value=m.container;$("mContainer").readOnly=true;
  $("mSize").value=m.size||"";$("mType").value=m.type||"";$("mVendor").value=m.vendor||"";
  $("mValidFrom").value=fmtDate(m.validFrom);$("mValidTo").value=fmtDate(m.validTo);$("mNote").value=m.note||"";
  $("masterSaveBtn").textContent="Update Versi Master";
  window.scrollTo({top:0,behavior:"smooth"});
}
async function disableMaster(id){
  let m=(await all("masters")).find(x=>x.id===id);if(!m)return;
  let raw=prompt(`Tanggal akhir berlaku ${m.container} • ${m.vendor}\nContoh: 31 Agu 26`,fmtDate(m.validTo||today()));
  if(raw===null)return;
  let end=parseDate(raw);if(!end)return alert("Tanggal akhir tidak valid.");
  if(m.validFrom&&end<m.validFrom)return alert("Tanggal akhir sebelum tanggal mulai.");
  let paid=await masterPaidImpact(m),latePaid=[];
  if(paid.length){
    let rentals=await all("rentals"),ids=new Set(paid.map(x=>invoiceCycleId(x)));
    latePaid=rentals.filter(r=>ids.has(r.id)&&r.startDate>end);
  }
  if(latePaid.length)return alert(`Tidak bisa dinonaktifkan pada ${fmtDate(end)} karena ada transaksi SUDAH DIBAYAR yang mulai setelah tanggal tersebut.`);
  m.validTo=end;m.isActive=false;m.updatedAt=now();await put("masters",m);
  await put("oplog",auditLog("MASTER NONAKTIF",m.container,end,"DITERIMA",`${m.vendor} dinonaktifkan efektif setelah ${fmtDate(end)}`,m.id));
  await refresh();
}
async function renderRent(){
  let r=await legacyCycles(),q=norm($("rentalSearch").value),s=$("rentalStatus").value,size=Number($("rentalPageSize")?.value||50);
  if(q)r=r.filter(x=>x.container.includes(q));if(s)r=r.filter(x=>x.status===s);
  let pg=pageSlice(r,rentalPage,size);rentalPage=pg.page;
  $("rentalPageInfo").textContent=`Halaman ${pg.page} dari ${pg.pages} • ${pg.total} siklus`;
  $("rentalPrev").disabled=pg.page<=1;$("rentalNext").disabled=pg.page>=pg.pages;
  $("rentalRows").innerHTML=pg.rows.length?pg.rows.map(x=>`<tr><td>${esc(x.id)}</td><td>${esc(x.container)}</td><td><span class="badge info">${esc(x.cycleFlag||"TAKE")}</span></td><td>${fmtDate(x.startDate)}${x.corrected?'<div class="small badtext">DIKOREKSI</div>':''}</td><td>${fmtDate(x.returnDate)}${x.inferredEnd&&!x.returnDate?`<div class="small muted">closed sebelum ${fmtDate(addDays(x.inferredEnd,1))}</div>`:""}</td><td>${x.rate?money(x.rate):""}</td><td>${esc(x.basis)}</td><td>${esc(x.size)}</td><td>${esc(x.type)}</td><td>${esc(x.vendor)}</td><td>${badge(x.status)}</td><td><button class="mini" data-rental-detail="${esc(x.id)}">▣ Detail</button> <button class="mini" data-edit-rental="${esc(x.id)}">Koreksi</button></td></tr>`).join(""):`<tr><td colspan="12" class="muted">Tidak ada data sesuai filter.</td></tr>`;
  document.querySelectorAll("[data-rental-detail]").forEach(b=>b.onclick=()=>openRentalDetail(b.dataset.rentalDetail));
  document.querySelectorAll("[data-edit-rental]").forEach(b=>b.onclick=()=>openRentalCorrection(b.dataset.editRental));
}

function effectiveVendorCleanup(x,masters){return x.vendor||selectMasterVersion(masters,x.container,x.cycleStartDate||x.startDate||x.invoiceDate)?.vendor||""}
function masalahSiklusInvoice(x,cand,rent){
  let current=rent.find(r=>r.id===(x.rentalId||x.canonicalCycleId));
  if(x.manualCycleConfirmed===true&&current)return "";
  if(!cand.length)return "SIKLUS TIDAK DITEMUKAN";
  if(cand.length>1)return "SIKLUS AMBIGU";
  if(!current)return "BELUM TERHUBUNG KE SIKLUS";
  if(!siklusTerkonfirmasi(current))return "SIKLUS BELUM TERKONFIRMASI";
  if(current.id!==cand[0].r.id)return "SUSPECT SALAH SIKLUS";
  return "";
}
async function cycleCleanupRows(){
  let rows=await activeInvoiceRows(),expected=await combinedExpected(),rent=await legacyCycles(),allInv=await all("invoices"),out=[];
  for(let x of rows){if(!x.container||!x.period)continue;let cand=kandidatSiklusTagihan(x,expected,rent,allInv),problem=masalahSiklusInvoice(x,cand,rent);if(problem)out.push({x,cand,problem})}
  return out.sort((a,b)=>(a.x.container||"").localeCompare(b.x.container||"")||Number(a.x.period||0)-Number(b.x.period||0));
}
async function renderCycleCleanup(){
  let el=$("cyclePrList");if(!el)return;let rows=await cycleCleanupRows();$("cyclePrCount").textContent=`${rows.length} PR`;
  if(!rows.length){el.innerHTML='<div class="rejectitem"><span class="goodtext"><b>PR SIKLUS = 0.</b> Tidak ditemukan masalah identitas siklus pada tagihan aktif.</span></div>';return}
  el.innerHTML=rows.map(o=>{let x=o.x,paid=x.paymentStatus==="SUDAH DIBAYAR",approved=x.approvalStatus==="APPROVED",hasPr=!!x.pranotaNo,
    state=paid?'<span class="badtext">TERKUNCI — SUDAH DIBAYAR</span>':hasPr?'<span class="badtext">TERKUNCI — BATALKAN PRANOTA DULU</span>':approved?'<span class="pending">APPROVED — approval akan dibatalkan saat koreksi</span>':'<span class="muted">BELUM APPROVAL</span>',
    candidates=o.cand.length?`<div class="small" style="margin-top:5px"><b>Kandidat relevan:</b>${htmlKandidatSiklus(o.cand)}</div>`:'<div class="badtext small" style="margin-top:5px">Tidak ada kandidat siklus untuk Container + Periode ini.</div>',
    actions="";
    if(!paid&&!hasPr&&o.cand.length)actions=`<div style="margin-top:7px"><b>Pilih:</b> ${o.cand.map((z,i)=>`<button class="mini primary" data-cycle-fix="${esc(x.id)}" data-cycle-no="${i+1}">[${i+1}] ${fmtDate(z.r.startDate)}</button>`).join(" ")}</div>`;
    else if(!paid&&!hasPr&&!o.cand.length)actions=`<div style="margin-top:7px"><button class="mini primary" data-cycle-add="${esc(x.container)}">+ Tambah Siklus</button></div>`;
    return `<div class="rejectitem"><div><b>${esc(x.container)} • ${esc(x.invoiceNo||"-")} • P${esc(x.period||"?")}</b> ${badge(o.problem)}</div><div class="small muted">Tgl Tagihan ${fmtDate(x.invoiceDate)} • ${state}</div>${candidates}${actions}</div>`}).join("");
  document.querySelectorAll("[data-cycle-fix]").forEach(b=>b.onclick=()=>applyCycleCleanup(b.dataset.cycleFix,Number(b.dataset.cycleNo)));
  document.querySelectorAll("[data-cycle-add]").forEach(b=>b.onclick=()=>prepareCycleTake(b.dataset.cycleAdd));
}
async function applyCycleCleanup(invoiceId,no){
  let allInv=await all("invoices"),x=allInv.find(v=>v.id===invoiceId);if(!x)return alert("Detail tagihan tidak ditemukan.");
  if(x.paymentStatus==="SUDAH DIBAYAR")return alert("Tagihan sudah dibayar. Batalkan Pembayaran terlebih dahulu.");
  if(x.pranotaNo)return alert(`Tagihan sudah masuk Pranota ${x.pranotaNo}. Batalkan Pranota terlebih dahulu.`);
  let expected=await combinedExpected(),rent=await legacyCycles(),cand=kandidatSiklusTagihan(x,expected,rent,allInv),chosen=cand[no-1];if(!chosen)return alert("Kandidat sudah berubah. Klik Kalkulasi + Review Ulang.");
  let oldCycle=x.rentalId||x.canonicalCycleId||"",wasApproved=x.approvalStatus==="APPROVED";
  if(wasApproved&&!confirm(`Tagihan ${x.invoiceNo} sudah APPROVED.\nKoreksi siklus akan membatalkan Approval dan wajib Approval ulang.\n\nLanjutkan?`))return;
  x.rentalId=chosen.r.id;x.canonicalCycleId=chosen.r.id;x.expectedId=chosen.e?.id||"";x.cycleChoice=no;x.manualCycleConfirmed=true;x.manualCycleAssignedAt=now();x.matchMethod="SUPERVISOR_CYCLE_CLEANUP";
  if(wasApproved){x.approvalStatus="BELUM APPROVAL";x.approvedAt=""}await put("invoices",x);
  if(wasApproved){let masters=await all("masters"),vendor=effectiveVendorCleanup(x,masters),same=allInv.filter(v=>v.invoiceNo===x.invoiceNo&&effectiveVendorCleanup(v,masters)===vendor&&v.paymentStatus!=="SUDAH DIBAYAR");for(let v of same){v.approvalStatus="BELUM APPROVAL";v.approvedAt="";await put("invoices",v)}}
  await put("oplog",auditLog("KOREKSI SIKLUS TAGIHAN",x.invoiceNo||x.id,today(),"DITERIMA",`${x.container} • P${x.period} • ${oldCycle||"-"} → ${chosen.r.id} • Ambil ${fmtDate(chosen.r.startDate)}${wasApproved?" • APPROVAL DIBATALKAN":""}`,"SUPERVISOR TAGIHAN SEHARUSNYA"));
  invalidateCaches();await reReviewUnpaidInvoices();await rebuild(false);await refresh();show("expected");
}
function prepareCycleTake(container){
  show("operasional");document.querySelectorAll("[data-op]").forEach(b=>b.classList.toggle("active",b.dataset.op==="ambil"));document.querySelectorAll(".tabpane").forEach(p=>p.classList.toggle("active",p.id==="op-ambil"));
  $("takeText").value=`${container}|||`;$("takeText").focus();alert(`Container ${container} sudah disiapkan.\nLengkapi Tgl Ambil dan salah satu tarif lalu Proses Impor.\nSetelah itu kembali ke Tagihan Seharusnya dan Kalkulasi + Review Ulang.`);
}

async function filteredExpected(){
  let r=await combinedExpected(),f=$("expectedFilter").value,q=String($("expectedSearch").value||"").trim().toUpperCase();
  if(f)r=r.filter(x=>x.status===f);
  if(q)r=r.filter(x=>[x.container,x.id,x.invoiceNo,x.rentalId].some(v=>String(v||"").toUpperCase().includes(q)));
  return r.sort((a,b)=>(b.startDate||"").localeCompare(a.startDate||""));
}
async function renderExp(){
  $("expectedRows").innerHTML='<tr><td colspan="9" class="muted">Memuat...</td></tr>';
  $("cyclePrList").innerHTML='<div class="rejectitem muted">Memeriksa PR siklus...</div>';
  let allr=await combinedExpected(),
      out=allr.filter(x=>x.status==="OUTSTANDING"||x.status==="BELUM DITAGIH"),
      missing=allr.filter(x=>x.status==="BELUM DITEMUKAN"),
      paid=allr.filter(x=>x.status==="SUDAH DIBAYAR"),
      r=await filteredExpected(),
      size=Number($("expectedPageSize")?.value||50),
      pg=pageSlice(r,expectedPage,size);
  expectedPage=pg.page;
  $("eTotal").textContent=allr.length;
  $("eOutstanding").textContent=money(out.reduce((s,x)=>s+Number(x.outstandingAmount??x.amount??0),0));
  $("eMissing").textContent=missing.length;
  $("ePaid").textContent=paid.length;
  $("expectedPageInfo").textContent=`Halaman ${pg.page} dari ${pg.pages} • ${pg.total} data`;
  $("expectedPrev").disabled=pg.page<=1;$("expectedNext").disabled=pg.page>=pg.pages;
  $("expectedRows").innerHTML=pg.rows.length?pg.rows.map(x=>`<tr><td>${esc(x.id)}</td><td>${esc(x.container)}</td><td>${x.period}</td><td>${fmtDate(x.startDate)}</td><td>${fmtDate(x.endDate)}</td><td>${x.amount==null?"—":money(x.amount)}</td><td>${badge(x.status)}</td><td>${esc(x.invoiceNo||"")}</td><td class="wrap">${esc(x.reason||"")}</td></tr>`).join(""):`<tr><td colspan="9" class="muted">Tidak ada data sesuai filter.</td></tr>`;  await renderCycleCleanup();
}async function getFilteredInvoices(){
  let r=await activeInvoiceRows(),f=$("invoiceFilter").value,q=$("invoiceSearch").value.trim().toUpperCase(),masters=mastersCache||(mastersCache=Object.fromEntries((await all("masters")).map(m=>[m.container,m])));
  r.sort((a,b)=>(a.container||"").localeCompare(b.container||"")||(a.rentalStartEstimated||a.startDate||"").localeCompare(b.rentalStartEstimated||b.startDate||"")||Number(a.period||0)-Number(b.period||0));
  if(f)r=r.filter(x=>x.decision===f);
  if(q)r=r.filter(x=>[x.invoiceNo,x.container,x.vendor,masters[x.container]?.vendor].some(v=>String(v||"").toUpperCase().includes(q)));
  return {rows:r,masters};
}async function invoiceHeaders(){
  if(invoiceHeadersCache)return invoiceHeadersCache;
  let rows=await activeInvoiceRows(),masters=Object.fromEntries((await all("masters")).map(m=>[m.container,m])),creditApps=await creditApplications(),g={};
  for(let x of rows){
    let vendor=x.vendor||masters[x.container]?.vendor||"",no=x.invoiceNo||"(TANPA NOMOR)",k=`${String(vendor).toUpperCase()}|${String(no).toUpperCase()}`;
    if(!g[k])g[k]={invoiceNo:no,vendor,date:x.invoiceDate||"",items:[],status:"READY TO PAY"};
    g[k].items.push(x);
  }
  let expRows=await combinedExpected(),expMap=Object.fromEntries(expRows.map(e=>[e.id,e]));
  for(let h of Object.values(g)){
    h.billableItems=h.items.filter(x=>!isSupersededDetail(x));
    let bi=h.billableItems;
    if(bi.length&&bi.every(x=>x.paymentStatus==="SUDAH DIBAYAR"))h.status="SUDAH DIBAYAR";
    else if(!bi.length)h.status="PENDING";
    else if(bi.some(x=>effectiveDetailProblems(x).length&&!x.resolved))h.status="PENDING";
    else h.status="READY TO PAY";
    h.sourceTotal=bi.reduce((s,x)=>s+Number(x.amount||0),0);
    h.estimateTotal=bi.reduce((s,x)=>s+Number(x.systemExpectedAmount??expMap[x.expectedId]?.amount??x.amount??0),0);
    h.adjustment=bi.reduce((s,x)=>s+Number(x.adjustment||0),0);
    h.dppBeforeCredit=h.estimateTotal+h.adjustment;
    h.creditApplied=Number(creditApps[invoiceCreditKey(h.vendor,h.invoiceNo)]?.amount||0);
    h.dpp=Math.max(0,h.dppBeforeCredit-h.creditApplied);
    let taxBase=bi[0]||h.items[0];h.ppnEnabled=taxBase?.ppnEnabled!==false;h.ppnRate=Number(taxBase?.ppnRate??11);
    h.pphEnabled=taxBase?.pphEnabled!==false;h.pphRate=Number(taxBase?.pphRate??2);
    h.ppn=h.ppnEnabled?h.dpp*h.ppnRate/100:0;h.pph=h.pphEnabled?h.dpp*h.pphRate/100:0;h.nett=h.dpp+h.ppn-h.pph;h.nettPayable=h.nett;
    h.approvedTotal=h.dpp;
    h.pranotaNos=[...new Set(h.items.map(x=>x.pranotaNo).filter(Boolean))];
    h.approvalStatus=bi.length&&bi.every(x=>x.approvalStatus==="APPROVED"||((x.pranotaNo||"")&&x.legacy))?"APPROVED":"BELUM APPROVAL";
    h.approvedAt=h.items.find(x=>x.approvedAt)?.approvedAt||"";
    h.problemCount=bi.filter(x=>effectiveDetailProblems(x).length&&!x.resolved).length;h.supersededCount=h.items.length-bi.length;
  }
  invoiceHeadersCache=Object.values(g).sort((a,b)=>(b.date||"").localeCompare(a.date||"")||a.invoiceNo.localeCompare(b.invoiceNo));return invoiceHeadersCache;
}


function headerKey(h){return `${String(h.vendor||"").toUpperCase()}|${String(h.invoiceNo||"").toUpperCase()}`}
async function setInvoiceApproval(invoiceNo,vendor,approve){
  let masters=Object.fromEntries((await all("masters")).map(m=>[m.container,m])),
      targetVendor=String(vendor||"").toUpperCase(),
      rows=(await activeInvoiceRows()).filter(x=>x.invoiceNo===invoiceNo&&!isSupersededDetail(x)&&String(x.vendor||masters[x.container]?.vendor||"").toUpperCase()===targetVendor);
  if(!rows.length)return alert("Tagihan tidak ditemukan.");
  if(rows.some(x=>x.paymentStatus==="SUDAH DIBAYAR"))return alert("Approval tidak dapat diubah karena tagihan sudah dibayar.");
  if(!approve&&rows.some(x=>x.pranotaNo))return alert("Batalkan pranota terlebih dahulu sebelum membatalkan approval.");
  let headers=await invoiceHeaders(),headerNow=headers.find(x=>x.invoiceNo===invoiceNo&&x.vendor===vendor);
  if(approve){
    let h=headerNow,blockers=rows.map(x=>({x,reason:detailProblemLabel(x)})).filter(z=>z.reason);
    if(!h||h.status!=="READY TO PAY"||h.problemCount||blockers.length){let msg=blockers.length?blockers.map(z=>`${z.x.container} • P${z.x.vendorPeriod||z.x.period||"?"} • ${z.reason} • Vendor ${money(z.x.amount||0)} • Expected ${money(Number(z.x.systemExpectedAmount??z.x.amount??0))} • Koreksi ${money(Number(z.x.adjustment||0))}`).join("\n"):"Masih ada masalah invoice yang belum selesai.";return alert(`Approval DIBLOKIR — ${invoiceNo}\n\n${msg}`);}
  }
  let at=now();for(let x of rows){x.approvalStatus=approve?"APPROVED":"BELUM APPROVAL";x.approvedAt=approve?at:"";x.approvalSnapshot=approve&&headerNow?{dppBeforeCredit:headerNow.dppBeforeCredit,creditApplied:headerNow.creditApplied,dpp:headerNow.dpp,ppn:headerNow.ppn,pph:headerNow.pph,nett:headerNow.nett,ppnEnabled:headerNow.ppnEnabled,pphEnabled:headerNow.pphEnabled,ppnRate:headerNow.ppnRate,pphRate:headerNow.pphRate,approvedAt:at}:null}
  await putMany("invoices",rows)
  await put("oplog",auditLog(approve?"APPROVAL TAGIHAN":"BATAL APPROVAL",invoiceNo,today(),"DITERIMA",`${vendor} • ${rows.length} detail`,"REVIEW BILLING • vendor efektif dari detail/master"));
  await renderInv();
}
function invoiceItemsInEntryOrder(items=[]){
  // Preserve physical entry/import sequence. Legacy rows without an explicit sequence keep their stored order.
  if(!items.some(x=>Number(x.entryOrder||0)>0))return [...items];
  return items.map((x,i)=>({x,i})).sort((a,b)=>{let ao=Number(a.x.entryOrder||0),bo=Number(b.x.entryOrder||0);if(ao&&bo&&ao!==bo)return ao-bo;if(ao&&!bo)return -1;if(!ao&&bo)return 1;return a.i-b.i}).map(z=>z.x);
}

async function activeVendorChoices(){
  return (await vendorMasterRows()).filter(v=>v.isActive!==false).sort((a,b)=>String(a.code||"").localeCompare(String(b.code||"")));
}
async function showVendorAutocomplete(inputId,menuId,onPick){
  let input=$(inputId),menu=$(menuId);if(!input||!menu)return;
  let q=String(input.value||"").trim().toUpperCase(),rows=await activeVendorChoices();
  if(q)rows=rows.filter(v=>String(v.code||"").toUpperCase().includes(q)||String(v.name||"").toUpperCase().includes(q));
  rows=rows.slice(0,30);
  if(!rows.length){menu.innerHTML='<div class="autocomplete-item muted">Vendor aktif tidak ditemukan di Master Vendor.</div>';menu.style.display="block";return}
  menu.innerHTML=rows.map(v=>`<div class="autocomplete-item" data-vendor-pick="${esc(v.code)}"><b>${esc(v.code)}</b>${v.name&&v.name!==v.code?` • ${esc(v.name)}`:""}</div>`).join("");menu.style.display="block";
  menu.querySelectorAll("[data-vendor-pick]").forEach(el=>el.onmousedown=async e=>{e.preventDefault();input.value=el.dataset.vendorPick;menu.style.display="none";if(onPick)await onPick(el.dataset.vendorPick)});
}
function hideVendorAutocomplete(menuId){let m=$(menuId);if(m)setTimeout(()=>m.style.display="none",140)}
function bindVendorAutocomplete(inputId,menuId,onPick){let input=$(inputId);if(!input||input.dataset.vendorAc)return;input.dataset.vendorAc="1";input.addEventListener("input",()=>showVendorAutocomplete(inputId,menuId,onPick));input.addEventListener("focus",()=>showVendorAutocomplete(inputId,menuId,onPick));input.addEventListener("blur",()=>hideVendorAutocomplete(menuId));}

async function renderPranota(){
  let hs=await invoiceHeaders(),q=String($("pranotaSearch")?.value||"").trim().toUpperCase(),vendor=String($("pranotaVendor")?.value||"").trim().toUpperCase(),pr=await all("pranotas");
  let cand=hs.filter(h=>h.approvalStatus==="APPROVED"&&h.status==="READY TO PAY"&&!h.pranotaNos.length);
  if(vendor)cand=cand.filter(h=>String(h.vendor||"").toUpperCase()===vendor);
  if(q)cand=cand.filter(h=>String(h.invoiceNo||"").toUpperCase().includes(q));
  $("pranotaCandidates").innerHTML=cand.length?`<div class="rejectitem" style="position:sticky;top:0;background:#fff;z-index:2"><label style="display:flex;gap:8px;align-items:center"><input type="checkbox" id="pranotaSelectAll" style="width:auto"><b>Pilih Semua Hasil Filter</b> <span class="small muted">• ${cand.length} kandidat</span></label></div>`+cand.map(h=>`<div class="rejectitem"><label style="display:flex;gap:8px;align-items:center"><input type="checkbox" data-pranota-candidate="${esc(headerKey(h))}" data-pranota-vendor="${esc(h.vendor)}" style="width:auto"><span><b>${esc(h.invoiceNo)}</b> • ${esc(h.vendor)}<br><span class="small muted">${fmtDate(h.date)} • ${h.items.length} detail • NETT BAYAR ${money(h.nettPayable??h.nett)}</span></span></label></div>`).join(""):'<div class="rejectitem muted">Tidak ada tagihan APPROVED yang belum masuk pranota.</div>';
  if($("pranotaSelectAll"))$("pranotaSelectAll").onchange=e=>{let boxes=[...document.querySelectorAll("[data-pranota-candidate]")],vendors=[...new Set(boxes.map(b=>b.dataset.pranotaVendor))];if(e.target.checked&&vendors.length>1){e.target.checked=false;return alert("Hasil filter berisi lebih dari satu vendor. Cari/filter satu vendor dahulu sebelum Pilih Semua.")}boxes.forEach(b=>b.checked=e.target.checked)};
  pr.sort((a,b)=>(b.date||"").localeCompare(a.date||""));
  $("pranotaList").innerHTML=pr.length?pr.map(x=>{let det=x.paymentDetail||[],tot=det.reduce((a,d)=>({dpp:a.dpp+Number(d.dpp||0),ppn:a.ppn+Number(d.ppn||0),pph:a.pph+Number(d.pph||0),total:a.total+Number(d.total||0)}),{dpp:0,ppn:0,pph:0,total:0});return `<div class="rejectitem"><b>${esc(x.pranotaNo)}</b> • ${esc(x.vendor)} • ${fmtDate(x.date)} ${badge(x.status||"DRAFT")}<div class="small muted">${(x.invoiceNos||[]).map(esc).join(", ")} • NETT ${money(x.nett)}${x.note?` • ${esc(x.note)}`:""}</div>${det.length?`<div class="tablewrap" style="max-height:none;margin:8px 0"><table style="min-width:760px"><thead><tr><th>Tgl Tagihan</th><th>No Tagihan</th><th>DPP</th><th>PPN</th><th>PPh</th><th>Total</th><th>Kontainer</th></tr></thead><tbody>${det.map(d=>`<tr><td>${fmtDate(d.date)}</td><td>${esc(d.invoiceNo)}</td><td>${money(d.dpp)}</td><td>${money(d.ppn)}</td><td>-${money(d.pph)}</td><td>${money(d.total)}</td><td class="wrap">${esc((d.containers||[]).join(", "))}</td></tr>`).join("")}<tr><th colspan="2">TOTAL PRANOTA</th><th>${money(tot.dpp)}</th><th>${money(tot.ppn)}</th><th>-${money(tot.pph)}</th><th>${money(tot.total)}</th><th></th></tr></tbody></table></div>`:""}${x.status!=="DIBAYAR"?`<button class="mini" data-cancel-pranota="${esc(x.id)}">Batalkan Pranota</button>`:""}</div>`}).join(""):'<div class="rejectitem muted">Belum ada pranota manual.</div>';
  document.querySelectorAll("[data-cancel-pranota]").forEach(b=>b.onclick=()=>cancelPranota(b.dataset.cancelPranota));
}
$("createPranota").onclick=async()=>{
  let keys=[...document.querySelectorAll("[data-pranota-candidate]:checked")].map(x=>x.dataset.pranotaCandidate),no=$("newPranotaNo").value.trim(),date=parseDate($("newPranotaDate").value),note=$("newPranotaNote").value.trim();
  if(!keys.length)return alert("Pilih minimal satu No. Tagihan APPROVED.");if(!no||!date)return alert("No. Pranota dan Tanggal wajib diisi.");
  let pr=await all("pranotas"),inv=await activeInvoiceRows(),hs=await invoiceHeaders();if(pr.some(x=>String(x.pranotaNo).toUpperCase()===no.toUpperCase())||inv.some(x=>String(x.pranotaNo||"").toUpperCase()===no.toUpperCase()))return alert("No. Pranota sudah digunakan.");
  let selected=hs.filter(h=>keys.includes(headerKey(h))),vendors=[...new Set(selected.map(h=>h.vendor))],pickedVendor=String($("pranotaVendor")?.value||"").trim().toUpperCase();if(vendors.length!==1)return alert("Satu pranota hanya boleh berisi satu vendor.");if(pickedVendor&&String(vendors[0]||"").toUpperCase()!==pickedVendor)return alert("Kandidat tidak sesuai Vendor yang dipilih.");
  if(selected.some(h=>h.approvalStatus!=="APPROVED"||h.status!=="READY TO PAY"||h.pranotaNos.length))return alert("Ada tagihan yang sudah berubah status. Muat ulang kandidat.");
  let snap=h=>h.items?.find(i=>i.approvalSnapshot)?.approvalSnapshot||{dpp:h.dpp,ppn:h.ppn,pph:h.pph,nett:h.nett};
  let invoiceNos=selected.map(h=>h.invoiceNo),nett=selected.reduce((a,h)=>a+Number(snap(h).nett||0),0),id=uid("PRN");
  let paymentDetail=selected.map(h=>{let z=snap(h);return {date:h.date,invoiceNo:h.invoiceNo,dpp:Number(z.dpp||0),ppn:Number(z.ppn||0),pph:Number(z.pph||0),total:Number(z.nett||0),containers:[...new Set(invoiceItemsInEntryOrder((h.items||[]).filter(i=>!isSupersededDetail(i))).map(i=>i.container).filter(Boolean))]}});
  await put("pranotas",{id,pranotaNo:no,date,vendor:vendors[0],invoiceNos,nett,note,paymentDetail,status:"DRAFT",createdAt:now()});
  let masterMap=Object.fromEntries((await all("masters")).map(m=>[m.container,m])),targetVendor=String(vendors[0]||"").toUpperCase();
  for(let x of inv.filter(x=>invoiceNos.includes(x.invoiceNo)&&!isSupersededDetail(x)&&String(x.vendor||masterMap[x.container]?.vendor||"").toUpperCase()===targetVendor)){x.pranotaNo=no;x.pranotaId=id;x.pranotaNote=note;await put("invoices",x)}
  await put("oplog",auditLog("BUAT PRANOTA",no,date,"DITERIMA",`${vendors[0]} • ${invoiceNos.length} tagihan • ${money(nett)}`,invoiceNos.join("|")));
  $("newPranotaNo").value="";$("newPranotaDate").value="";$("newPranotaNote").value="";await renderPranota();
};
async function cancelPranota(id){
  let p=(await all("pranotas")).find(x=>x.id===id);if(!p)return;if(p.status==="DIBAYAR")return alert("Pranota sudah dibayar dan tidak dapat dibatalkan.");if(!confirm(`Batalkan pranota ${p.pranotaNo}?`))return;
  let inv=await all("invoices");for(let x of inv.filter(x=>x.pranotaId===id||x.pranotaNo===p.pranotaNo)){if(x.paymentStatus==="SUDAH DIBAYAR")return alert("Pembatalan diblokir karena ada tagihan sudah dibayar.")}
  for(let x of inv.filter(x=>x.pranotaId===id||x.pranotaNo===p.pranotaNo)){x.pranotaNo="";x.pranotaId="";x.pranotaNote="";await put("invoices",x)}
  p.status="DIBATALKAN";p.cancelledAt=now();await put("pranotas",p);await put("oplog",auditLog("BATAL PRANOTA",p.pranotaNo,today(),"DITERIMA",`${p.vendor} • ${(p.invoiceNos||[]).length} tagihan`,"PRANOTA"));await renderPranota();
}

async function buildTraceContext(){
  let inv=await all("invoices"),exp=await combinedExpected(),pay=await all("payments"),
      expById={},expByKey={},invByKey={},payByInvoice={};

  for(let e of exp){
    if(e.id)expById[e.id]=e;
    let k=`${e.rentalId||""}|${Number(e.period||0)}`;
    if(e.rentalId)expByKey[k]=e;
  }
  for(let i of inv){
    let cid=invoiceCycleId(i),k=`${cid}|${Number(i.period||0)}`;
    if(cid)(invByKey[k]??=[]).push(i);
  }
  for(let p of pay){
    let nos=[...(p.invoiceNos||[]),p.invoiceNo].filter(Boolean);
    for(let n of nos)(payByInvoice[n]??=[]).push(p);
  }
  return{inv,exp,pay,expById,expByKey,invByKey,payByInvoice};
}
async function setVendorClaimDecision(id,decision){
  let x=(await all("invoices")).find(z=>z.id===id);if(!x)return;
  if(x.paymentStatus==="SUDAH DIBAYAR")return alert("Tagihan sudah PAID. Batalkan pembayaran dari transaksi terakhir terlebih dahulu.");
  if(decision==="TIDAK_BERHAK_DITAGIH"){
    let reason=prompt("Alasan keputusan",(x.claimFlags||[]).join(" • ")||"Tidak ada hak tagih/Expected pada siklus yang dikonfirmasi.");if(reason===null)return;
    x.claimDecision=decision;x.claimDecisionReason=reason.trim()||"Tidak berhak ditagih";x.claimDecisionAt=now();x.claimDecisionBy="USER";x.resolved=true;x.decision="PENDING";x.approvedAmount=0;
    x.problems=[...new Set([...(x.problems||[]),"USER MENETAPKAN: TIDAK BERHAK DITAGIH"])];
    await put("invoices",x);await put("oplog",auditLog("KEPUTUSAN TAGIHAN",x.invoiceNo,today(),"DITERIMA",`${x.container} • P vendor ${x.vendorPeriod||x.period} • TIDAK BERHAK DITAGIH • ${x.claimDecisionReason}`,JSON.stringify({invoiceId:x.id,cycleId:invoiceCycleId(x),vendorPeriod:x.vendorPeriod||x.period,returnFlag:x.claimFlags||[]})));
    invalidateCaches();await applyInvoiceGroupStatus();await renderInv();if(visibleView()==="reviewcenter")await renderReviewCenter(x.container);
  }
}

function traceButton(label,kind,value,title=""){
  if(!value)return "";
  return `<button class="mini" style="margin:3px 4px 0 0" data-trace-kind="${esc(kind)}" data-trace-value="${esc(value)}" title="${esc(title)}">↗ ${esc(label)}</button>`;
}
function uniqueBy(arr,keyFn){
  let seen=new Set(),out=[];
  for(let x of arr){let k=keyFn(x);if(seen.has(k))continue;seen.add(k);out.push(x)}
  return out;
}
function traceEvidenceHtml(x,ctx){
  let problems=(x.problems||[]).filter(p=>p!=="PENDING SATU TAGIHAN: ADA DETAIL LAIN BERMASALAH");
  if(!problems.length)return "";
  let cid=invoiceCycleId(x),period=Number(x.period||0),key=`${cid}|${period}`,
      same=(ctx.invByKey[key]||[]).filter(i=>i.id!==x.id),
      exp=(x.expectedId&&ctx.expById[x.expectedId])||ctx.expByKey[key]||null,
      html=`<div class="small" style="margin-top:5px;padding-top:5px;border-top:1px dashed #d7dde5"><b>Bukti / Trace:</b> `;
  let parts=[];

  if(problems.includes("NOMINAL BERBEDA DARI EXPECTED")){
    if(exp){
      parts.push(`<div>Expected <b>${esc(exp.id||"")}</b> = <b>${money(exp.amount||0)}</b> • ${fmtDate(exp.startDate)}–${fmtDate(exp.endDate)} ${traceButton("Expected","expected",exp.id||x.container)}</div>`);
    }else{
      parts.push(`<div class="badtext">Expected pendukung tidak ditemukan pada data aktif.</div>`);
    }
  }

  if(problems.includes("INDIKASI DUPLIKAT: SIKLUS + PERIODE SUDAH DITAGIH PADA NO TAGIHAN LAIN")){
    let dup=uniqueBy(same.filter(i=>i.invoiceNo&&i.invoiceNo!==x.invoiceNo),i=>i.invoiceNo);
    if(dup.length){
      parts.push(`<div><b>Sudah ditagih pada:</b> ${dup.map(i=>`<b>${esc(i.invoiceNo)}</b> • Cycle <b>${esc(invoiceCycleId(i)||"-")}</b> • P Vendor ${esc(i.vendorPeriod||i.period||"?")} • Periode Sistem ${esc(systemPeriodLabel(i))} • ${money(i.amount||0)} • <b>${esc(comparisonStatus(i))}</b> ${traceButton("Cari hard copy / Review","invoice",i.invoiceNo)}`).join("<br>")}</div>`);
    }else{
      parts.push(`<div class="badtext">Referensi invoice duplikat tidak ditemukan. Flag ini perlu Review Ulang.</div>`);
    }
  }

  if(problems.includes("SIKLUS + PERIODE SUDAH PERNAH DIBAYAR")){
    let paid=uniqueBy(same.filter(i=>i.paymentStatus==="SUDAH DIBAYAR"),i=>`${i.invoiceNo}|${i.paymentNo}|${i.paymentDate}`);
    if(paid.length){
      parts.push(`<div>Pembayaran sebelumnya: ${paid.map(i=>{
        let p=(ctx.payByInvoice[i.invoiceNo]||[])[0],
            ref=i.paymentNo||p?.reference||"(bukti kosong)",
            dt=i.paymentDate||p?.date||"",
            q=ref!=="(bukti kosong)"?ref:i.invoiceNo;
        return `<b>${esc(i.invoiceNo||"")}</b> • Bukti <b>${esc(ref)}</b>${dt?` • ${fmtDate(dt)}`:""} ${traceButton("Pembayaran","payment",q)}`;
      }).join(" • ")}</div>`);
    }else{
      parts.push(`<div class="badtext">Bukti pembayaran pendukung tidak ditemukan. Flag ini perlu Review Ulang.</div>`);
    }
  }

  if(cid){
    parts.push(`<div>Siklus <b>${esc(cid)}</b> • P${period} ${traceButton("Rental","rental",x.container)}</div>`);
  }

  return html+parts.join("")+`</div>`;
}
async function followTrace(kind,value){
  if(kind==="expected"){
    show("expected");$("expectedSearch").value=value;expectedPage=1;await renderExp();$("expectedSearch").focus();
  }else if(kind==="invoice"){
    show("invoices");$("invoiceSearch").value=value;reviewPage=1;invoicePage=1;await renderInv();$("invoiceSearch").focus();
  }else if(kind==="payment"){
    show("payments");if($("paymentSearch"))$("paymentSearch").value=value;await renderPay();$("paymentSearch")?.focus();
  }else if(kind==="rental"){
    show("rentals");$("rentalSearch").value=value;rentalPage=1;await renderRent();$("rentalSearch").focus();
  }
}
function bindTraceButtons(){
  document.querySelectorAll("[data-trace-kind]").forEach(b=>b.onclick=()=>followTrace(b.dataset.traceKind,b.dataset.traceValue));
}



function systemPeriodLabel(x){
  let p=x.systemPeriod||x.period||"?",a=x.correctedStartDate||x.systemStartDate||x.startDate||"",b=x.correctedEndDate||x.systemEndDate||x.endDate||"";
  return `P${p}${a&&b?` • ${fmtDate(a)} – ${fmtDate(b)}`:" • tanggal belum tersedia"}`;
}
function comparisonStatus(i){
  if(i.paymentStatus==="SUDAH DIBAYAR")return `PAID${i.paymentDate?` • ${fmtDate(i.paymentDate)}`:""}${i.paymentNo?` • Bukti ${i.paymentNo}`:""}`;
  if(i.approvalStatus==="APPROVED")return "APPROVED";
  return i.decision||"PENDING";
}
function duplicateProblemsOnly(p){return /INDIKASI DUPLIKAT|SUDAH PERNAH DIBAYAR/i.test(String(p||""))}
async function markInvoiceDetailSuperseded(id){
  let rows=await all("invoices"),x=rows.find(z=>String(z.id)===String(id));if(!x)return alert("Detail tagihan tidak ditemukan.");
  if(x.paymentStatus==="SUDAH DIBAYAR")return alert("Detail sudah PAID. Batalkan pembayaran terlebih dahulu.");
  if(x.pranotaNo)return alert(`Detail sudah masuk Pranota ${x.pranotaNo}. Batalkan Pranota terlebih dahulu.`);
  let masters=await all("masters"),vendor=effectiveVendorCleanup(x,masters),candidates=rows.filter(z=>String(z.id)!==String(x.id)&&norm(z.invoiceNo)===norm(x.invoiceNo)&&norm(z.container)===norm(x.container)&&effectiveVendorCleanup(z,masters)===vendor&&!isSupersededDetail(z));
  if(!candidates.length)return alert("Tidak ada detail pengganti kandidat pada No. Tagihan + Container yang sama. Data lama tidak diubah.");
  let lines=candidates.map((z,i)=>`${i+1}. P Vendor ${z.vendorPeriod||z.period||"?"} • ${money(z.amount||0)} • Cycle ${invoiceCycleId(z)||"BELUM TERHUBUNG"}`).join("\n");
  let pick=prompt(`Tandai detail ini sebagai DIGANTI / TIDAK BERLAKU.\n\n${x.container} • P Vendor ${x.vendorPeriod||x.period||"?"} • ${money(x.amount||0)}\n\nPilih nomor detail PENGGANTI:\n${lines}\n\nMasukkan nomor kandidat:`);if(pick===null)return;
  let idx=Number(pick)-1,repl=candidates[idx];if(!repl)return alert("Nomor kandidat tidak valid. Tidak ada perubahan data.");
  let note=prompt("Keterangan (opsional)","Dokumen/detail vendor lama diganti; No. dan Tgl Tagihan dapat tetap sama.");if(note===null)return;
  if(!confirm(`Konfirmasi keputusan user:\n\nDIGANTI / TIDAK DIHITUNG:\n${x.container} • P${x.vendorPeriod||x.period||"?"} • ${money(x.amount||0)}\n\nPENGGANTI AKTIF:\n${repl.container} • P${repl.vendorPeriod||repl.period||"?"} • ${money(repl.amount||0)}\n\nWaktu penerimaan invoice TIDAK dipakai sebagai patokan. Lanjutkan?`))return;
  let wasApproved=x.approvalStatus==="APPROVED";
  x.superseded={status:"DIGANTI",replacementId:repl.id,note:note.trim(),decidedAt:now(),decidedBy:"USER"};x.reviewDecision="DIGANTI";x.reviewDecisionNote=note.trim();x.resolved=true;x.decision="READY TO PAY";x.approvedAmount=0;x.approvalStatus="BELUM APPROVAL";x.approvedAt="";x.approvalSnapshot=null;
  repl.replacesIds=[...new Set([...(repl.replacesIds||[]),x.id])];
  let changedRows=[x,repl];
  if(wasApproved){let same=rows.filter(v=>norm(v.invoiceNo)===norm(x.invoiceNo)&&effectiveVendorCleanup(v,masters)===vendor&&!isSupersededDetail(v));for(let v of same){v.approvalStatus="BELUM APPROVAL";v.approvedAt="";v.approvalSnapshot=null}changedRows.push(...same)}
  await putMany("invoices",[...new Map(changedRows.map(v=>[v.id,v])).values()])
  await put("oplog",auditLog("DOKUMEN TAGIHAN DIGANTI",x.invoiceNo,today(),"DITERIMA",`${x.container} • detail ${x.id} DIGANTI oleh ${repl.id} • ${note.trim()||"tanpa keterangan"}${wasApproved?" • APPROVAL DIBATALKAN":""}`,JSON.stringify({supersededId:x.id,replacementId:repl.id,invoiceNo:x.invoiceNo,container:x.container})));
  invalidateCaches();
  // Keputusan DIGANTI mengubah himpunan bukti aktif. Review ulang wajib dari nol agar
  // detail pengganti tidak mewarisi status double/masalah dari detail yang sudah tidak berlaku.
  await targetedReReview(x.container);
  invalidateCaches();
  let inv=sessionStorage.getItem("reviewScopeInvoice")||x.invoiceNo,c=sessionStorage.getItem("reviewScopeContainer")||x.container;await renderScopedReview(inv,c);
}
async function setComparisonDecision(id,decision){
  let rows=await all("invoices"),x=rows.find(z=>String(z.id)===String(id));if(!x)return;
  if(x.paymentStatus==="SUDAH DIBAYAR")return alert("Detail yang sedang direview sudah PAID. Pembayaran harus dibuka dari transaksi terakhir terlebih dahulu.");
  let note=prompt("Catatan keputusan supervisor",decision==="CURRENT_INVALID"?"Tagihan sekarang bermasalah berdasarkan hard copy vendor.":decision==="OLD_INVALID"?"Tagihan pembanding/lama perlu dikoreksi berdasarkan hard copy vendor.":decision==="NOT_DUPLICATE"?"Keduanya benar / bukan duplikat setelah pemeriksaan hard copy.":"Belum dapat diputuskan; perlu cek hard copy/dokumen pendukung.");
  if(note===null)return;
  x.reviewDecision=decision;x.reviewDecisionAt=now();x.reviewDecisionBy="USER";x.reviewDecisionNote=note.trim();
  if(decision==="CURRENT_INVALID"){
    x.claimDecision="DITOLAK_USER";x.approvedAmount=0;x.resolved=true;
    x.problems=(x.problems||[]).filter(p=>!duplicateProblemsOnly(p));
  }else if(decision==="NOT_DUPLICATE"){
    x.duplicateDecision="BUKAN_DUPLIKAT";x.problems=(x.problems||[]).filter(p=>!duplicateProblemsOnly(p));
    x.resolved=(x.problems||[]).filter(p=>p!=="PENDING SATU TAGIHAN: ADA DETAIL LAIN BERMASALAH").length===0;
  }else if(decision==="OLD_INVALID"){
    x.resolved=false;if(!(x.problems||[]).some(p=>String(p).includes("PR DOWNSTREAM")))x.problems=[...(x.problems||[]),"PR DOWNSTREAM: TAGIHAN LAMA/PAID DIPUTUS USER PERLU KOREKSI"];
  }else{x.resolved=false;}
  if(window.__linkAudit&&["CURRENT_INVALID","NOT_DUPLICATE"].includes(decision)){for(let f of (window.__linkAudit.findings||[])){if(String(f.id)===String(x.id)){f.userResolved=true;f.userDecision=decision;f.userDecisionAt=now();}}try{localStorage.setItem("cbc_link_audit_snapshot_v1",JSON.stringify(window.__linkAudit))}catch(e){}}
  await put("invoices",x);
  await put("oplog",auditLog("KEPUTUSAN REVIEW DUPLIKAT",x.invoiceNo,today(),decision==="UNDECIDED"?"TERCATAT":"DITERIMA",`${x.container} • ${decision} • ${x.reviewDecisionNote||"-"}`,JSON.stringify({invoiceId:x.id,cycleId:invoiceCycleId(x),vendorPeriod:x.vendorPeriod||x.period})));
  invalidateCaches();await applyInvoiceGroupStatus();
  let inv=sessionStorage.getItem("reviewScopeInvoice")||x.invoiceNo,c=sessionStorage.getItem("reviewScopeContainer")||x.container;await renderScopedReview(inv,c);
}
async function openComparisonReview(id){
  let active=await activeInvoiceRows(),x=active.find(z=>String(z.id)===String(id));if(!x)return;
  let rent=(await legacyCycles()).filter(r=>r.container===x.container).sort((a,b)=>String(a.startDate||"").localeCompare(String(b.startDate||""))),expected=await combinedExpected(),cid=invoiceCycleId(x),vp=Number(x.vendorPeriod||x.period||0);
  let refStart=x.migrationStartDate||"",refEnd=x.migrationEndDate||"";
  let candidateRows=rent.map(r=>{
    let e=expected.find(z=>z.rentalId===r.id&&Number(z.period||0)===vp)||null;
    let startOk=!!e&&(!refStart||e.startDate===refStart),endOk=!!e&&(!refEnd||e.endDate===refEnd),exact=!!e&&startOk&&endOk;
    let takeExact=!!refStart&&r.startDate===refStart;
    return {r,e,startOk,endOk,exact,takeExact,current:r.id===cid};
  });
  let d=document.getElementById("comparisonDialog");if(!d){d=document.createElement("dialog");d.id="comparisonDialog";d.style.maxWidth="1200px";d.style.width="96%";document.body.appendChild(d)}
  let evidence=candidateRows.map(z=>`<tr><td>${z.current?'<b>SAAT INI</b>':'KANDIDAT'}</td><td>${esc(z.r.id)}</td><td>${fmtDate(z.r.startDate)}–${z.r.returnDate?fmtDate(z.r.returnDate):"AKTIF"}</td><td>${z.e?`P${vp} • ${fmtDate(z.e.startDate)}–${fmtDate(z.e.endDate)}`:`P${vp} TIDAK ADA`}</td><td>${z.takeExact?'<span class="goodtext">YA</span>':'-'}</td><td>${z.exact?'<span class="goodtext"><b>COCOK EXACT</b></span>':z.e?'<span class="badtext">TIDAK COCOK</span>':'<span class="badtext">TIDAK ADA HAK</span>'}</td><td>${z.e?money(z.e.amount||0):'-'}</td></tr>`).join("");
  let exactCount=candidateRows.filter(z=>z.exact).length,conclusion=exactCount===1?`1 Cycle cocok exact dengan P Vendor + referensi Dari/Sampai.`:exactCount>1?`${exactCount} Cycle sama-sama cocok: AMBIGU, keputusan user diperlukan.`:`Tidak ada Cycle yang cocok exact dengan P Vendor + referensi Dari/Sampai.`;
  d.innerHTML=`<form method="dialog"><div style="display:flex;justify-content:space-between;gap:10px"><div><h3 style="margin:0">Bukti Matching Cycle • ${esc(x.container)}</h3><div class="small muted">Tgl Tagihan ${fmtDate(x.invoiceDate)} hanya bukti dokumen dan <b>tidak digunakan untuk memilih Cycle</b>.</div></div><button>Tutup</button></div></form><div class="note" style="margin-top:10px"><b>No. Tagihan:</b> ${esc(x.invoiceNo)} • <b>P Vendor ${esc(vp)}</b> • Nominal ${money(x.amount||0)}<br><b>Referensi vendor:</b> ${refStart?fmtDate(refStart):"-"} – ${refEnd?fmtDate(refEnd):"-"}<br><b>Hasil:</b> ${esc(conclusion)}</div><div style="overflow:auto"><table><thead><tr><th>Status</th><th>Cycle</th><th>Ambil–Kembali</th><th>Expected P Vendor</th><th>TAKE = Dari</th><th>P + Dari/Sampai</th><th>Expected</th></tr></thead><tbody>${evidence||'<tr><td colspan="7">Tidak ada cycle.</td></tr>'}</tbody></table></div><div class="note" style="margin-top:10px"><b>Metode tersimpan:</b> ${esc(x.matchMethod||"-")} • <b>Cycle terpasang:</b> ${esc(cid||"BELUM TERHUBUNG")}</div><div style="display:flex;gap:7px;flex-wrap:wrap;margin-top:12px"><button class="danger" data-cmp-dec="CURRENT_INVALID">Tagihan Sekarang Bermasalah</button><button class="primary" data-cmp-dec="SUPERSEDED">Diganti / Tidak Berlaku</button><button data-cmp-dec="NOT_DUPLICATE">Data Benar / Bukan Duplikat</button><button data-cmp-dec="WRONG_CYCLE">Cycle Salah</button><button data-cmp-dec="UNDECIDED">Belum Diputuskan</button></div>${x.reviewDecision?`<div class="note" style="margin-top:10px"><b>Keputusan user terakhir:</b> ${esc(x.reviewDecision)} • ${esc(x.reviewDecisionNote||"")}</div>`:""}`;
  d.querySelectorAll("[data-cmp-dec]").forEach(b=>b.onclick=async()=>{let dec=b.dataset.cmpDec;if(dec==="WRONG_CYCLE"){d.close();await openScopedCycleCorrection(x.id);return}if(dec==="SUPERSEDED"){d.close();await markInvoiceDetailSuperseded(x.id);return}d.close();await setComparisonDecision(x.id,dec)});d.showModal();
}
function auditFindingsForInvoice(invoiceNo,container=""){
  let a=window.__linkAudit;if(!a)return [];
  let no=norm(invoiceNo),c=norm(container);
  return (a.findings||[]).filter(f=>!f.userResolved&&norm(f.invoiceNo)===no&&(!c||norm(f.container)===c));
}
function directChronologyProblem(x,cycles){
  // V7.1.8: Tgl Tagihan dapat dibuat kapan saja dan hanya bukti dokumen.
  // Validitas Cycle ditentukan oleh referensi sewa / Expected, bukan tanggal invoice.
  return false;
}
function auditBadgeHtml(invoiceNo,items=[],cycles={}){
  let fs=auditFindingsForInvoice(invoiceNo).filter(f=>f.severity!=="ARSIP");
  let direct=(items||[]).filter(x=>directChronologyProblem(x,cycles)).length;if(direct)return `<span class="badge bad">${direct} KRITIS KRONOLOGI</span>`;
  if(!window.__linkAudit)return '<span class="badge info">BELUM AUDIT</span>';
  if(!fs.length)return '<span class="badge good">AUDIT BERSIH</span>';
  let critical=fs.filter(f=>f.severity==="KRITIS").length;
  return `<span class="badge ${critical?"bad":"warn"}">${critical?critical+" KRITIS":fs.length+" PR AUDIT"}</span>`;
}
async function openScopedReview(invoiceNo,container=""){
  invoiceNo=norm(invoiceNo);container=norm(container);
  sessionStorage.setItem("reviewScopeInvoice",invoiceNo);sessionStorage.setItem("reviewScopeContainer",container);
  show("reviewcenter");
  if($("reportType"))$("reportType").onchange=renderReports;if($("reportSearch"))$("reportSearch").oninput=()=>debounce(renderReports);if($("exportReport"))$("exportReport").onclick=exportCurrentReport;
if($("reviewCenterSearch"))$("reviewCenterSearch").value=container||invoiceNo;
  await renderScopedReview(invoiceNo,container);
}

async function openCycleFromInvoiceDetail(invoiceId){
  let rows=await all("invoices"),x=rows.find(v=>String(v.id)===String(invoiceId));
  if(!x)return alert("Detail tagihan tidak ditemukan.");
  sessionStorage.setItem("reviewScopeInvoice",norm(x.invoiceNo||""));
  sessionStorage.setItem("reviewScopeContainer",norm(x.container||""));
  show("reviewcenter");
  if($("reportType"))$("reportType").onchange=renderReports;if($("reportSearch"))$("reportSearch").oninput=()=>debounce(renderReports);if($("exportReport"))$("exportReport").onclick=exportCurrentReport;
if($("reviewCenterSearch"))$("reviewCenterSearch").value=x.container||x.invoiceNo||"";
  await openScopedCycleCorrection(invoiceId);
}

async function openScopedCycleCorrection(invoiceId){
  let rows=await all("invoices"),x=rows.find(v=>String(v.id)===String(invoiceId));
  if(!x)return alert("Detail tagihan tidak ditemukan.");
  let invoiceNo=norm(x.invoiceNo||""),container=norm(x.container||"");
  let rent=(await legacyCycles()).filter(r=>r.container===container).sort((a,b)=>String(a.startDate||"").localeCompare(String(b.startDate||"")));
  let expected=await combinedExpected(),currentId=invoiceCycleId(x),box=$("reviewCenterDetail");
  let cards=rent.map(r=>{
    let ex=expected.filter(e=>e.rentalId===r.id).sort((a,b)=>Number(a.period||0)-Number(b.period||0)),vp=Number(x.vendorPeriod||x.period||0),match=ex.find(e=>Number(e.period||0)===vp),current=r.id===currentId;
    let periodInfo=match?`Hak P${vp}: ${fmtDate(match.startDate)}–${fmtDate(match.endDate)} • ${money(match.amount||0)}`:`Hak P${vp}: TIDAK ADA • Expected tersedia s/d P${Math.max(0,...ex.map(e=>Number(e.period||0)))}`;
    let refStart=x.migrationStartDate||"",refEnd=x.migrationEndDate||"",refMatch=!!match&&(!refStart||match.startDate===refStart)&&(!refEnd||match.endDate===refEnd);
    let refInfo=x.migrationMode?`<div class="small"><b>Referensi vendor:</b> ${refStart?fmtDate(refStart):"-"}–${refEnd?fmtDate(refEnd):"-"} • ${refMatch?'<span class="goodtext">COCOK EXACT</span>':'<span class="badtext">TIDAK COCOK</span>'}</div>`:"";
    return `<div class="rejectitem"><div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap"><div><b>${current?"SAAT INI • ":""}${fmtDate(r.startDate)} – ${r.returnDate?fmtDate(r.returnDate):"AKTIF"}</b><div class="small">Cycle ${esc(r.id)} • ${esc(r.vendor||"")} • ${esc(r.status||"")}</div><div class="small"><b>${esc(periodInfo)}</b></div>${refInfo}</div><div>${current?'<span class="badge pending">CYCLE SAAT INI</span>':`<button class="mini primary" data-choose-cycle="${esc(r.id)}">Gunakan Cycle Ini</button>`}</div></div></div>`;
  }).join("");
  box.innerHTML=`<div class="card"><div class="actions" style="justify-content:space-between;align-items:center"><div><h3 style="margin:0">Koreksi Cycle • ${esc(invoiceNo)} • ${esc(container)}</h3><div class="small muted">P Vendor ${esc(x.vendorPeriod||x.period||"?")} • ${money(x.amount||0)} • Tgl Tagihan ${fmtDate(x.invoiceDate)}</div></div><button class="mini" id="cancelCycleCorrection">← Kembali ke Review Tagihan</button></div><div class="note warn"><b>Yang diubah hanya relasi detail tagihan → cycle.</b> P Vendor, nominal vendor, Tgl Tagihan, Rental dan Expected tidak diubah. <b>Tgl Tagihan hanya referensi dokumen dan tidak dipakai menentukan Cycle.</b></div>${cards||'<div class="badtext">Tidak ada history cycle untuk container ini.</div>'}</div>`;
  $("cancelCycleCorrection").onclick=()=>renderScopedReview(invoiceNo,sessionStorage.getItem("reviewScopeContainer")||"");
  document.querySelectorAll("[data-choose-cycle]").forEach(b=>b.onclick=()=>applyScopedCycleCorrection(invoiceId,b.dataset.chooseCycle));
}
async function applyScopedCycleCorrection(invoiceId,cycleId){
  let rows=await all("invoices"),x=rows.find(v=>String(v.id)===String(invoiceId));if(!x)return alert("Detail tagihan tidak ditemukan.");
  if(x.paymentStatus==="SUDAH DIBAYAR")return alert("Detail ini sudah dibayar. Batalkan Pembayaran terlebih dahulu sebelum mengubah relasi cycle.");
  if(x.pranotaNo)return alert(`Detail sudah masuk Pranota ${x.pranotaNo}. Batalkan Pranota terlebih dahulu.`);
  let rent=await legacyCycles(),chosen=rent.find(r=>r.id===cycleId&&r.container===x.container);if(!chosen)return alert("Cycle pilihan tidak ditemukan.");
  let oldCycle=invoiceCycleId(x),wasApproved=x.approvalStatus==="APPROVED";
  if(!confirm(`Koreksi relasi tagihan ${x.invoiceNo} / ${x.container}\n\nDari: ${oldCycle||"BELUM TERHUBUNG"}\nKe: ${chosen.id} • ${fmtDate(chosen.startDate)} – ${chosen.returnDate?fmtDate(chosen.returnDate):"AKTIF"}\n\nP Vendor ${x.vendorPeriod||x.period} dan nominal ${money(x.amount||0)} tetap sesuai dokumen vendor.\nLanjutkan?`))return;
  x.rentalId=chosen.id;x.canonicalCycleId=chosen.id;x.expectedId="";x.manualCycleConfirmed=true;x.manualCycleAssignedAt=now();x.matchMethod="SUPERVISOR_SCOPED_CYCLE_CORRECTION";x.cycleChoice=null;
  // Keputusan lama yang bergantung pada cycle harus direview ulang setelah relasi berubah.
  x.claimDecision="";x.claimDecisionReason="";x.reviewDecision="";x.reviewDecisionNote="";x.duplicateDecision="";x.resolved=false;
  if(wasApproved){x.approvalStatus="BELUM APPROVAL";x.approvedAt=""}
  await put("invoices",x);
  if(wasApproved){let masters=await all("masters"),vendor=effectiveVendorCleanup(x,masters),same=rows.filter(v=>v.invoiceNo===x.invoiceNo&&effectiveVendorCleanup(v,masters)===vendor&&v.paymentStatus!=="SUDAH DIBAYAR");for(let v of same){v.approvalStatus="BELUM APPROVAL";v.approvedAt="";await put("invoices",v)}}
  await put("oplog",auditLog("KOREKSI SIKLUS TAGIHAN",x.invoiceNo||x.id,today(),"DITERIMA",`${x.container} • P Vendor ${x.vendorPeriod||x.period} • ${oldCycle||"-"} → ${chosen.id} • Ambil ${fmtDate(chosen.startDate)} • Kembali ${chosen.returnDate?fmtDate(chosen.returnDate):"AKTIF"}${wasApproved?" • APPROVAL DIBATALKAN":""}`,"REVIEW & KOREKSI SCOPED"));
  await targetedReReview(x.container);
  // V7.0.10: warning import hanya tetap OPEN bila masalah warning memang masih ada setelah cycle diganti.
  let afterRows=await all("invoices"),after=afterRows.find(v=>String(v.id)===String(x.id));
  if(after){
    let warningProblems=(after.problems||[]).filter(isImportWarningProblem);
    after.warningQueueOpen=warningProblems.length>0;
    after.identityWarning=warningProblems.length>0;
    after.identityWarningReason=warningProblems.join(" • ");
    if(!warningProblems.length)after.forcedPendingByUser=false;
    await put("invoices",after);invalidateCaches();
  }
  // Tandai snapshot lama sebagai usang untuk detail ini; audit baru dapat dijalankan kemudian tanpa menghapus jejak log.
  if(window.__linkAudit?.findings){for(let f of window.__linkAudit.findings){if(String(f.id)===String(x.id)){f.userResolved=true;f.resolvedReason="CYCLE_DIKOREKSI_USER";f.resolvedAt=now()}}try{localStorage.setItem("cbc_link_audit_snapshot_v1",JSON.stringify(window.__linkAudit))}catch(e){}}
  await renderScopedReview(norm(x.invoiceNo||""),sessionStorage.getItem("reviewScopeContainer")||"");
  let fresh=(await all("invoices")).find(v=>String(v.id)===String(x.id));
  if(fresh&&String(fresh.claimStatus||"").startsWith("NO_ENTITLEMENT")&&!fresh.claimDecision){
    alert(`Cycle berhasil dikoreksi ke ${fmtDate(chosen.startDate)} – ${chosen.returnDate?fmtDate(chosen.returnDate):"AKTIF"}.\n\nP Vendor ${fresh.vendorPeriod||fresh.period} tetap disimpan. Sistem menemukan periode vendor tersebut TIDAK mempunyai hak/Expected pada cycle pilihan. Review hasilnya lalu tetapkan keputusan user bila sesuai hard copy.`);
  }
}

async function renderScopedReview(invoiceNo,container=""){
  invoiceNo=norm(invoiceNo);container=norm(container);
  let banner=$("reviewScopeBanner");
  if(banner){banner.style.display="block";banner.innerHTML=`<b>Scope aktif:</b> No. Tagihan <b>${esc(invoiceNo)}</b>${container?` • Container <b>${esc(container)}</b>`:""} <button class="mini" id="backScopedInvoice">← Kembali ke Tinjau Tagihan</button>`;}
  let rows=(await activeInvoiceRows()).filter(x=>norm(x.invoiceNo)===invoiceNo&&(!container||norm(x.container)===container));
  let cycles=Object.fromEntries((await legacyCycles()).map(c=>[c.id,c]));
  let fs=auditFindingsForInvoice(invoiceNo,container),byId=new Map(fs.map(f=>[String(f.id),f]));
  let box=$("reviewCenterDetail");
  if(box){box.innerHTML=`<div class="card"><h3>Review ${esc(invoiceNo)}${container?" • "+esc(container):""}</h3><div class="note"><b>${rows.length} detail dalam scope</b> • ${fs.length} temuan snapshot audit. ${window.__linkAudit?"Snapshot audit tersedia.":"Belum ada snapshot audit; bukti referensi Cycle tetap diperiksa pada detail di bawah."}</div>${rows.map(x=>{let f=byId.get(String(x.id)),chrono=directChronologyProblem(x,cycles),cid=invoiceCycleId(x),cy=cycles[cid],ep=effectiveDetailProblems(x),creditDone=x.creditResolution?.status==="RESOLVED",bad=f||chrono||(ep.length&&!x.resolved)||(!creditDone&&String(x.claimStatus||"").startsWith("NO_ENTITLEMENT")),downstream=x.paymentStatus==="SUDAH DIBAYAR"?"SUDAH DIBAYAR":x.pranotaNo?`PRANOTA ${x.pranotaNo}`:x.approvalStatus==="APPROVED"?"APPROVED":"";return `<div class="rejectitem"><div style="display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap"><div><b>${esc(x.container)} • P Vendor ${esc(x.vendorPeriod||x.period||"?")}</b> • ${money(x.amount)}${downstream?` • <span class="badge info">${esc(downstream)}</span>`:""}<div class="small">Tgl Tagihan ${fmtDate(x.invoiceDate)} • Cycle ${esc(cid||"BELUM TERHUBUNG")} ${cy?`• Ambil ${fmtDate(cy.startDate)} • Kembali ${cy.returnDate?fmtDate(cy.returnDate):"AKTIF"}`:""}</div><div class="small"><b>Range Sewa:</b> ${cy?`${fmtDate(cy.startDate)}–${cy.returnDate?fmtDate(cy.returnDate):"AKTIF"}`:"-"}</div><div class="small"><b>Periode Sistem:</b> ${esc(systemPeriodLabel(x))}</div><div class="small"><b>Periode Vendor:</b> P${esc(x.vendorPeriod||x.period||"?")} • ${x.migrationStartDate?fmtDate(x.migrationStartDate):"-"}–${x.migrationEndDate?fmtDate(x.migrationEndDate):"-"}</div><div class="small"><b>Tarif Snapshot:</b> ${cy?money(Number(cy.monthlyRate||cy.rate||cy.dailyRate||0)):"-"} • <b>Vendor:</b> ${money(x.amount||0)} • <b>Expected:</b> ${money(Number(x.systemExpectedAmount??x.amount??0))} • <b>Selisih:</b> ${money(Number(x.amount||0)-Number(x.systemExpectedAmount??x.amount??0)-Number(x.adjustment||0))}</div>${f?`<div class="badtext">${esc((f.flags||[]).join(" • "))}</div>`:""}${ep.length&&!x.resolved?`<div class="badtext">${esc(ep.join(" • "))}</div>`:""}${creditDone?`<div class="goodtext"><b>SELESAI - KREDIT VENDOR</b> • Kredit ${money(Number(x.creditResolution.amount||0))}${x.creditResolution.note?` • ${esc(x.creditResolution.note)}`:""}</div>`:""}${isSupersededDetail(x)?`<div class="goodtext"><b>DIGANTI — TIDAK DIHITUNG</b> • Pengganti ${esc(x.superseded.replacementId||"-")}${x.superseded.note?` • ${esc(x.superseded.note)}`:""}</div>`:x.reviewDecision?`<div class="goodtext"><b>Keputusan user:</b> ${esc(x.reviewDecision)}${x.reviewDecisionNote?` • ${esc(x.reviewDecisionNote)}`:""}</div>`:""}${!bad?'<div class="goodtext">Tidak ada PR aktif pada detail ini.</div>':""}</div><div><button class="mini primary" data-scope-compare="${esc(x.id)}">Bandingkan / Keputusan</button> <button class="mini" data-scope-adjust="${esc(x.id)}" title="Isi Koreksi Manual (+/-) dan keterangan pada detail tagihan aktif">Koreksi Nilai</button> <button class="mini" data-scope-guide="${esc(x.id)}">Guide / Koreksi Cycle</button> <button class="mini" data-scope-supersede="${esc(x.id)}" title="Tandai detail vendor lama sebagai DIGANTI / TIDAK DIHITUNG dan pilih detail pengganti">Dokumen Diganti</button> ${creditDone?`<button class="mini" data-view-credit="${esc(x.id)}">Lihat Kredit Vendor</button>`:`<button class="mini" data-record-credit="${esc(x.id)}">Catat Kredit Vendor</button>`}</div></div></div>`}).join("")||'<div class="muted">Tidak ada detail aktif dalam scope ini.</div>'}</div>`;}
  document.querySelectorAll("[data-scope-guide]").forEach(b=>b.onclick=()=>openScopedCycleCorrection(b.dataset.scopeGuide));document.querySelectorAll("[data-scope-compare]").forEach(b=>b.onclick=()=>openComparisonReview(b.dataset.scopeCompare));document.querySelectorAll("[data-scope-adjust]").forEach(b=>b.onclick=()=>openAdjustment(b.dataset.scopeAdjust));document.querySelectorAll("[data-scope-supersede]").forEach(b=>b.onclick=()=>markInvoiceDetailSuperseded(b.dataset.scopeSupersede));document.querySelectorAll("[data-record-credit]").forEach(b=>b.onclick=()=>recordVendorCredit(b.dataset.recordCredit));document.querySelectorAll("[data-view-credit]").forEach(b=>b.onclick=async()=>{let x=(await all("invoices")).find(z=>String(z.id)===String(b.dataset.viewCredit));if(!x?.creditResolution)return alert("Data Kredit Vendor tidak ditemukan.");alert(`Kredit Vendor tersimpan\n\nNilai: ${money(Number(x.creditResolution.amount||0))}\nKeterangan: ${x.creditResolution.note||"-"}\nStatus: SELESAI - KREDIT VENDOR${x.pranotaNo?`\nPranota: ${x.pranotaNo}`:""}${x.paymentStatus==="SUDAH DIBAYAR"?"\nPembayaran: SUDAH DIBAYAR":""}`)});
  if($("backScopedInvoice"))$("backScopedInvoice").onclick=async()=>{show("invoices");$("invoiceSearch").value=invoiceNo;invoicePage=1;reviewPage=1;await renderInv()};
  if(window.__linkAudit){let el=$("globalLinkAuditResult");if(el){el.style.display="block";if($("auditInvoiceSearch"))$("auditInvoiceSearch").value=container||invoiceNo;renderAuditSnapshot();}}
}
async function openReviewCenterHome(){
  let inv=sessionStorage.getItem("reviewScopeInvoice")||"",c=sessionStorage.getItem("reviewScopeContainer")||"";
  if(inv)return renderScopedReview(inv,c);
  let banner=$("reviewScopeBanner");if(banner)banner.style.display="none";
  if(window.__linkAudit){$("globalLinkAuditResult").style.display="block";renderAuditSnapshot();}
  await renderReviewCenter();
}
async function renderInv(){
  let size=Number($("invoicePageSize")?.value||50),f=$("invoiceFilter").value,q=$("invoiceSearch").value.trim().toUpperCase();
  let filtered=await getFilteredInvoices(),r=filtered.rows,masters=filtered.masters,
      cycles=Object.fromEntries((await legacyCycles()).map(c=>[c.id,c])),traceCtx=await buildTraceContext(),
      allHeaders=await invoiceHeaders(),headerMap=Object.fromEntries(allHeaders.map(h=>[headerKey(h),h]));

  const actionButtons=h=>{
    if(!h)return "";
    let parts=[];
    // Pajak dan approval selalu merupakan aksi level No. Tagihan. Audit OPEN memblokir Approval.
    let auditOpen=(h.items||[]).some(x=>effectiveDetailProblems(x).length||(!x.resolved&&auditFindingsForInvoice(h.invoiceNo,x.container).some(f=>f.severity!=="ARSIP"))||directChronologyProblem(x,cycles));
    parts.push(`<button class="mini" data-tax="${esc(h.invoiceNo)}" data-tax-vendor="${esc(h.vendor)}">Pajak</button>`);
    parts.push(`<button class="mini" data-open-review-invoice="${esc(h.invoiceNo)}">Review & Koreksi</button>`);
    if(h.approvalStatus!=="APPROVED"&&!h.pranotaNos.length){
      if(h.status==="READY TO PAY"&&!auditOpen)parts.push(`<button class="mini primary" data-approve="${esc(h.invoiceNo)}" data-vendor="${esc(h.vendor)}">Approve</button>`);
      else parts.push(`<button class="mini" disabled title="Selesaikan seluruh masalah detail sebelum Approval">Approval Diblokir</button>`);
    }else if(h.approvalStatus==="APPROVED"&&h.status!=="SUDAH DIBAYAR"&&!h.pranotaNos.length){
      parts.push(`<button class="mini" data-unapprove="${esc(h.invoiceNo)}" data-vendor="${esc(h.vendor)}">Batalkan Approval</button>`);
    }
    return parts.join(" ");
  };
  const approvalBadge=h=>h?.approvalStatus?badge(h.approvalStatus):badge("BELUM APPROVAL");

  // PER KONTAINER: halaman tetap berdasarkan detail, tetapi header aksi dikelompokkan per No. Tagihan.
  // Jadi Approve/Pajak tidak diulang pada setiap kontainer.
  let pg=pageSlice(r,reviewPage,size);reviewPage=pg.page;
  $("reviewPageInfo").textContent=`Halaman ${pg.page} dari ${pg.pages} • ${pg.total} detail`;
  $("reviewPrev").disabled=pg.page<=1;$("reviewNext").disabled=pg.page>=pg.pages;

  let invoiceGroups={};
  for(let x of pg.rows){
    let vendor=x.vendor||masters[x.container]?.vendor||"",k=`${String(vendor).toUpperCase()}|${String(x.invoiceNo||"").toUpperCase()}`;
    if(!invoiceGroups[k])invoiceGroups[k]={header:headerMap[k],items:[]};
    invoiceGroups[k].items.push(x);
  }
  let html="";
  for(let [k,ig] of Object.entries(invoiceGroups)){
    let h=ig.header;
    if(!h){
      let first=ig.items[0]||{},vendor=first.vendor||masters[first.container]?.vendor||"";
      h={invoiceNo:first.invoiceNo||"(TANPA NOMOR)",vendor,date:first.invoiceDate||"",status:first.decision||"PENDING",approvalStatus:first.approvalStatus||"BELUM APPROVAL",pranotaNos:[...new Set(ig.items.map(x=>x.pranotaNo).filter(Boolean))]};
    }
    html+=`<div class="invoice-summary" style="display:block;border:1px solid #e1e6ec;border-radius:10px;margin:0 0 12px;padding:0;overflow:hidden">
      <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;padding:11px 12px;background:#f7f9fb;border-bottom:1px solid #e7ebef">
        <div><b>${esc(h.invoiceNo)}</b><div class="small muted">${esc(h.vendor)} • ${fmtDate(h.date)} • ${ig.items.length} detail pada halaman ini • Pranota: ${esc((h.pranotaNos||[]).join(", ")||"-")}</div></div>
        <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">${badge(h.status)} ${approvalBadge(h)} ${auditBadgeHtml(h.invoiceNo,h.items||ig.items,cycles)} ${actionButtons(h)}</div>
      </div>`;

    let byCycle={};
    for(let x of ig.items){
      let cycle=invoiceCycleId(x)||x.rentalIdEstimated||`${x.container}|${x.rentalStartEstimated||x.startDate||""}`,
          ck=`${x.container}||${cycle}`;
      if(!byCycle[ck])byCycle[ck]={container:x.container,start:cycles[cycle]?.startDate||x.rentalStartEstimated||x.startDate||"",items:[]};
      byCycle[ck].items.push(x);
    }
    for(let g of Object.values(byCycle).sort((a,b)=>a.container.localeCompare(b.container)||(a.start||"").localeCompare(b.start||""))){
      g.items.sort((a,b)=>Number(a.period||0)-Number(b.period||0)||(a.invoiceDate||"").localeCompare(b.invoiceDate||""));
      html+=`<div class="timeline-group" style="margin:10px 12px 14px"><div class="timeline-head">${esc(g.container)} • Rental ${fmtDate(g.start)||"?"}</div>`;
      for(let x of g.items){
        let problems=(x.problems||[]).filter(p=>p!=="PENDING SATU TAGIHAN: ADA DETAIL LAIN BERMASALAH"),
            autoExpected=Number(x.systemExpectedAmount??x.amount??0),
            manual=Number(x.adjustment||0),
            approved=Number(x.approvedAmount??(autoExpected+manual)),
            autoLabel=x.expectedKind==="PRORATA"?"Prorata Otomatis":"Expected Sistem";
        html+=`<div class="timeline-row"><div><b>P${esc(x.period||"?")}</b><div class="small muted">${fmtDate(x.correctedStartDate||x.systemStartDate||x.startDate)}–${fmtDate(x.correctedEndDate||x.systemEndDate||x.endDate)}</div></div><div><b>No. Tagihan:</b> ${esc(x.invoiceNo)}<div class="small muted"><b>Tgl Tagihan:</b> ${fmtDate(x.invoiceDate)}</div></div><div><div><b>${autoLabel}</b> ${money(autoExpected)}</div><div class="small">Koreksi Manual ${money(manual)}</div><div class="small"><b>Disetujui ${money(approved)}</b></div>${problems.length?`<div class="badtext">${esc(problems.join(" • "))}</div>`:(x.decision==="PENDING"?`<div class="badtext">PENDING KARENA DETAIL LAIN DALAM NO. TAGIHAN BERMASALAH</div>`:`<div class="goodtext">${x.resolved?"Koreksi selesai":"OK"}</div>`)}${x.adjustmentNote?`<div class="small muted">Catatan koreksi: ${esc(x.adjustmentNote)}</div>`:""}${x.claimStatus&&String(x.claimStatus).startsWith("NO_ENTITLEMENT")?`<div class="badtext">Vendor P${esc(x.vendorPeriod||x.period)} • HAK TAGIH SISTEM: TIDAK ADA${x.claimDecision==="TIDAK_BERHAK_DITAGIH"?" • DIKONFIRMASI USER":""}</div>`:""}${traceEvidenceHtml(x,traceCtx)}</div><div>${x.claimDecision==="TIDAK_BERHAK_DITAGIH"?badge("TIDAK BERHAK DITAGIH"):badge(x.decision)}</div><div>${x.paymentStatus==="SUDAH DIBAYAR"?`<span class="small"><b>Bukti Bayar:</b> ${esc(x.paymentNo||"-")}<br><b>Tgl Bayar:</b> ${fmtDate(x.paymentDate)||"-"}</span>`:`<button class="mini" data-adjust="${esc(x.id)}">Koreksi</button> <button class="mini" data-open-review-detail="${esc(x.invoiceNo)}" data-review-container-detail="${esc(x.container)}">Review PR</button>${x.claimStatus==="NO_ENTITLEMENT"?` <button class="mini danger" data-no-entitlement="${esc(x.id)}">Tidak Berhak</button>`:""}`}</div></div>`;
      }
      html+="</div>";
    }
    html+="</div>";
  }
  $("containerTimeline").innerHTML=html||'<div class="muted">Tidak ada data sesuai filter.</div>';

  // Footer seluruh hasil filter. Pajak tetap dihitung per No. Tagihan lalu dijumlahkan.
  let filteredRows=r,
      invoiceKeys=[...new Set(filteredRows.map(x=>`${String(x.vendor||masters[x.container]?.vendor||"").toUpperCase()}|${String(x.invoiceNo||"").toUpperCase()}`).filter(Boolean))],
      headerSel=invoiceKeys.map(k=>headerMap[k]).filter(Boolean),
      invoiceNos=[...new Set(headerSel.map(h=>h.invoiceNo))];
  let detailExpected=filteredRows.reduce((s,x)=>s+Number(x.systemExpectedAmount??x.amount??0),0),detailAdj=filteredRows.reduce((s,x)=>s+Number(x.adjustment||0),0),detailApproved=filteredRows.reduce((s,x)=>s+Number(x.approvedAmount??approvedFromSystem(x)),0);
  let taxDpp=headerSel.reduce((s,h)=>s+Number(h.dpp||0),0),taxPpn=headerSel.reduce((s,h)=>s+Number(h.ppn||0),0),taxPph=headerSel.reduce((s,h)=>s+Number(h.pph||0),0),taxNett=headerSel.reduce((s,h)=>s+Number(h.nett||0),0);
  $("reviewFilterFooter").innerHTML=filteredRows.length?`<div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap"><div><b>Ringkasan Hasil Filter</b><div class="small muted">${filteredRows.length} detail • ${invoiceNos.length} No. Tagihan</div></div></div><div class="finance-grid"><div class="finance-cell"><span class="small muted">Expected Sistem</span><b>${money(detailExpected)}</b></div><div class="finance-cell"><span class="small muted">Koreksi Manual</span><b>${money(detailAdj)}</b></div><div class="finance-cell"><span class="small muted">Disetujui</span><b>${money(detailApproved)}</b></div><div class="finance-cell"><span class="small muted">DPP Invoice</span><b>${money(taxDpp)}</b></div><div class="finance-cell"><span class="small muted">PPN (per invoice)</span><b>${money(taxPpn)}</b></div><div class="finance-cell"><span class="small muted">PPh (per invoice)</span><b>-${money(taxPph)}</b></div><div class="finance-cell"><span class="small muted">NETT Invoice</span><b>${money(taxNett)}</b></div></div><div class="small muted" style="margin-top:8px">PPN/PPh tetap disimpan per No. Tagihan. Tombol Pajak/Approval berada di header No. Tagihan, bukan per kontainer.</div>`:'<div class="muted">Tidak ada hasil untuk dihitung.</div>';

  // PER NO. TAGIHAN: ringkasan invoice dengan tombol yang sama.
  let hs=allHeaders.slice();
  if(f)hs=hs.filter(h=>h.status===f);
  if(q)hs=hs.filter(h=>[h.invoiceNo,h.vendor,...h.items.map(i=>i.container)].some(v=>String(v||"").toUpperCase().includes(q)));
  let hp=pageSlice(hs,invoicePage,size);invoicePage=hp.page;
  $("invoicePageInfo").textContent=`Halaman ${hp.page} dari ${hp.pages} • ${hp.total} No. Tagihan`;
  $("invoicePrev").disabled=hp.page<=1;$("invoiceNext").disabled=hp.page>=hp.pages;
  $("invoiceHeaderList").innerHTML=hp.rows.length?hp.rows.map(h=>`<div class="invoice-summary" style="display:block"><div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap"><div><b>${esc(h.invoiceNo)}</b><div class="small muted">${esc(h.vendor)} • ${fmtDate(h.date)} • ${h.items.length} detail historis${h.supersededCount?` • ${h.items.length-h.supersededCount} berlaku • ${h.supersededCount} diganti`:""} • Pranota: ${esc(h.pranotaNos.join(", ")||"-")}</div>${h.problemCount?`<div class="badtext">${h.problemCount} masalah belum selesai</div>`:""}</div><div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">${badge(h.status)} ${approvalBadge(h)} ${auditBadgeHtml(h.invoiceNo,h.items||[],cycles)} ${actionButtons(h)}</div></div><div style="margin-top:8px">${h.items.map(x=>{let fs=auditFindingsForInvoice(h.invoiceNo,x.container).filter(f=>f.severity!=="ARSIP"),cy=cycles[invoiceCycleId(x)],chrono=directChronologyProblem(x,cycles);return `<div class="rejectitem" style="margin:5px 0"><b>${esc(x.container)} • P Vendor ${esc(x.vendorPeriod||x.period||"?")}</b> • ${money(x.amount)} • Cycle ${esc(invoiceCycleId(x)||"BELUM TERHUBUNG")}${cy?` • Ambil ${fmtDate(cy.startDate)} • Kembali ${fmtDate(cy.endDate)||"AKTIF"}`:""}<br><span class="small"><b>Periode Sistem:</b> ${esc(systemPeriodLabel(x))}</span> ${detailProblemLabel(x)?`<span class="badge bad">${esc(detailProblemLabel(x))}</span>`:x.creditResolution?.status==="RESOLVED"?`<span class="badge good" style="font-size:12px;font-weight:800">SELESAI - KREDIT VENDOR</span>`:`<span class="badge good" style="font-size:13px;font-weight:900;padding:4px 8px">OK</span>`}<br><span class="small"><b>Nilai Sistem:</b> ${money(Number(x.systemExpectedAmount??x.amount??0))} • <b>Koreksi Manual:</b> ${money(Number(x.adjustment||0))} • <b>Nilai Vendor:</b> ${money(Number(x.amount||0))} • <b>DPP Disetujui:</b> ${money(Number(x.systemExpectedAmount??x.amount??0)+Number(x.adjustment||0))}</span>${x.adjustmentNote?`<br><span class="small muted"><b>Ket. Koreksi:</b> ${esc(x.adjustmentNote)}</span>`:""}${x.creditResolution?.status==="RESOLVED"?`<br><span class="small"><b>Kredit Vendor:</b> ${money(Number(x.creditResolution.amount||0))}</span>`:""} ${isSupersededDetail(x)?` <span class="badge good">DIGANTI — TIDAK DIHITUNG</span>`:(chrono?badge("KRITIS"):fs.length?badge(fs.some(f=>f.severity==="KRITIS")?"KRITIS":"SUSPECT"):"")} ${isSupersededDetail(x)?`<span class="small muted">Pengganti: ${esc(x.superseded?.replacementId||"-")}</span>`:`<button class="mini primary" data-direct-cycle="${esc(x.id)}">Pilih / Ganti Cycle</button> <button class="mini" data-direct-adjust="${esc(x.id)}">Koreksi Nilai</button> <button class="mini" data-direct-supersede="${esc(x.id)}">Dokumen Diganti</button>`} <button class="mini" data-open-review-detail="${esc(h.invoiceNo)}" data-review-container-detail="${esc(x.container)}">Review & Koreksi</button></div>`}).join("")}</div><div class="finance-grid"><div class="finance-cell"><span class="small muted">Expected Sistem</span><b>${money(h.estimateTotal)}</b></div><div class="finance-cell"><span class="small muted">Koreksi Manual</span><b>${money(h.adjustment)}</b></div><div class="finance-cell"><span class="small muted">DPP Disetujui</span><b>${money(h.dpp)}</b></div><div class="finance-cell"><span class="small muted">PPN ${h.ppnEnabled?h.ppnRate+"%":"Tidak"}</span><b>${money(h.ppn)}</b></div><div class="finance-cell"><span class="small muted">PPh ${h.pphEnabled?h.pphRate+"%":"Tidak"}</span><b>-${money(h.pph)}</b></div><div class="finance-cell"><span class="small muted">NETT</span><b>${money(h.nett)}</b></div><div class="finance-cell"><span class="small muted">Kredit Vendor</span><b>-${money(h.creditApplied||0)}</b></div><div class="finance-cell"><span class="small muted">NETT BAYAR</span><b>${money(h.nettPayable??h.nett)}</b></div></div><div style="margin-top:6px"><button class="mini" data-apply-credit="${esc(h.invoiceNo)}" data-credit-vendor="${esc(h.vendor)}">Pakai / Ubah Kredit Vendor</button></div></div>`).join(""):'<div class="muted">Tidak ada invoice sesuai filter.</div>';

  document.querySelectorAll("[data-adjust]").forEach(b=>b.onclick=()=>openAdjustment(b.dataset.adjust));document.querySelectorAll("[data-no-entitlement]").forEach(b=>b.onclick=()=>setVendorClaimDecision(b.dataset.noEntitlement,"TIDAK_BERHAK_DITAGIH"));
  document.querySelectorAll("[data-tax]").forEach(b=>b.onclick=()=>openTax(b.dataset.tax,b.dataset.taxVendor));
  document.querySelectorAll("[data-approve]").forEach(b=>b.onclick=()=>setInvoiceApproval(b.dataset.approve,b.dataset.vendor,true));
  document.querySelectorAll("[data-unapprove]").forEach(b=>b.onclick=()=>setInvoiceApproval(b.dataset.unapprove,b.dataset.vendor,false));
  document.querySelectorAll("[data-open-review-invoice]").forEach(b=>b.onclick=()=>openScopedReview(b.dataset.openReviewInvoice));
  document.querySelectorAll("[data-open-review-detail]").forEach(b=>b.onclick=()=>openScopedReview(b.dataset.openReviewDetail,b.dataset.reviewContainerDetail));
  document.querySelectorAll("[data-direct-cycle]").forEach(b=>b.onclick=()=>openCycleFromInvoiceDetail(b.dataset.directCycle));document.querySelectorAll("[data-direct-adjust]").forEach(b=>b.onclick=()=>openAdjustment(b.dataset.directAdjust));document.querySelectorAll("[data-direct-supersede]").forEach(b=>b.onclick=()=>markInvoiceDetailSuperseded(b.dataset.directSupersede));document.querySelectorAll("[data-apply-credit]").forEach(b=>b.onclick=()=>applyVendorCredit(b.dataset.applyCredit,b.dataset.creditVendor));
  bindTraceButtons();
}let currentAdjustId="",currentRentalId="",currentTaxInvoice="",currentTaxVendor="",loadedPayment=null;
document.querySelectorAll("[data-review]").forEach(b=>b.onclick=async()=>{
  currentReviewMode=b.dataset.review;
  document.querySelectorAll("[data-review]").forEach(x=>x.classList.toggle("active",x===b));
  $("review-container").style.display=currentReviewMode==="container"?"block":"none";
  $("review-invoice").style.display=currentReviewMode==="invoice"?"block":"none";
  if($("review-cycle"))$("review-cycle").style.display=currentReviewMode==="cycle"?"block":"none";
  if(currentReviewMode==="cycle")await renderCyclePeriodReview(); else await renderInv();
});
async function renderCyclePeriodReview(container=""){
  let q=norm(container||$("cycleReviewSearch")?.value||"");
  let box=$("cycleReviewBody");if(!box)return;
  if(!q){box.innerHTML='<div class="muted">Pilih container untuk melihat seluruh siklus dan periode otomatis.</div>';return}
  if($("cycleReviewSearch"))$("cycleReviewSearch").value=q;
  let cycles=(await legacyCycles()).filter(r=>r.container===q).sort((a,b)=>String(a.startDate||"").localeCompare(String(b.startDate||""))),exp=await combinedExpected(),inv=await all("invoices");
  if(!cycles.length){box.innerHTML=`<div class="badtext">Tidak ada Cycle untuk ${esc(q)}. Periksa Master/Pengambilan.</div>`;return}
  box.innerHTML=cycles.map((r,i)=>{let ps=exp.filter(e=>e.rentalId===r.id).sort((a,b)=>Number(a.period||0)-Number(b.period||0)),linked=inv.filter(x=>invoiceCycleId(x)===r.id);return `<div class="timeline-group"><div class="timeline-head">Cycle ${i+1} • ${esc(r.id)} • Ambil ${fmtDate(r.startDate)} • Kembali ${fmtDate(r.returnDate)||"AKTIF"} ${badge(r.status||"")}</div><div class="small muted">${esc(r.vendor||"")} • ${esc(r.basis||"")} • Tarif ${money(Number(r.rate||r.monthlyRate||r.dailyRate||0))} • ${linked.length} detail tagihan terhubung</div><div style="margin:7px 0"><button class="mini" data-cycle-detail="${esc(r.id)}">Detail Cycle</button> <button class="mini" data-cycle-edit="${esc(r.id)}">Koreksi Siklus</button></div>${ps.length?ps.map(e=>{let li=linked.filter(x=>Number(x.period||0)===Number(e.period||0));let vend=li.length?li.map(x=>`<div class="small" style="margin-top:4px"><b>Vendor P${esc(x.vendorPeriod||x.period||"?")}</b> • Referensi ${x.migrationStartDate?fmtDate(x.migrationStartDate):"-"}–${x.migrationEndDate?fmtDate(x.migrationEndDate):"-"} • ${money(x.amount||0)} • ${esc(x.invoiceNo||"-")} • ${esc(x.paymentStatus||"BELUM DIBAYAR")}${detailProblemLabel(x)?` • <span class="badtext">${esc(detailProblemLabel(x))}</span>`:""}</div>`).join(""):'<div class="small muted">Vendor: BELUM DITAGIH</div>';return `<div class="rejectitem"><b>Range Sewa Cycle: ${fmtDate(r.startDate)}–${fmtDate(r.returnDate)||"AKTIF"}</b><br><b>Sistem P${e.period}</b> • ${fmtDate(e.startDate)}–${fmtDate(e.endDate)} • Expected ${money(Number(e.amount||0))}${vend}</div>`}).join(""):'<div class="rejectitem muted">Belum ada Expected.</div>'}</div>`}).join("");
  box.querySelectorAll("[data-cycle-detail]").forEach(b=>b.onclick=()=>openRentalDetail(b.dataset.cycleDetail));
  box.querySelectorAll("[data-cycle-edit]").forEach(b=>b.onclick=()=>openRentalCorrection(b.dataset.cycleEdit));
}
if($("cycleReviewRun"))$("cycleReviewRun").onclick=()=>renderCyclePeriodReview();
if($("cycleReviewSearch"))$("cycleReviewSearch").onkeydown=e=>{if(e.key==="Enter")renderCyclePeriodReview()};

function invoiceCycleId(x){return x.canonicalCycleId||x.rentalId||""}
async function cycleInvoices(cycleId){return (await all("invoices")).filter(x=>invoiceCycleId(x)===cycleId)}
async function cycleHasPaid(cycleId){return (await cycleInvoices(cycleId)).some(x=>x.paymentStatus==="SUDAH DIBAYAR")}
async function findCycle(cycleId){return (await legacyCycles()).find(r=>r.id===cycleId)||null}
function effectiveDetailStart(x){return x.correctedStartDate||x.systemStartDate||x.startDate||""}
function effectiveDetailEnd(x){return x.correctedEndDate||x.systemEndDate||x.endDate||""}
async function paidImpactForReturn(cycleId,newReturn){
  let paid=(await cycleInvoices(cycleId)).filter(x=>x.paymentStatus==="SUDAH DIBAYAR"),impacted=[];
  for(let x of paid){
    let s=effectiveDetailStart(x),e=effectiveDetailEnd(x);
    if((s&&s>newReturn)||(e&&e>newReturn))impacted.push(x);
  }
  return impacted;
}
async function referenceMonthlyAmount(x,cycle){
  if(cycle&&cycle.basis==="BULANAN"&&Number(cycle.monthlyRate||cycle.rate||0)>0)return Number(cycle.monthlyRate||cycle.rate||0);
  let rows=(await cycleInvoices(invoiceCycleId(x))).filter(i=>Number(i.amount||0)>0);
  // Gunakan nominal full-month yang paling sering muncul sebagai referensi histori.
  let freq={};for(let r of rows){let n=Math.round(Number(r.amount||0));freq[n]=(freq[n]||0)+1}
  let vals=Object.entries(freq).sort((a,b)=>b[1]-a[1]||Number(b[0])-Number(a[0]));
  return vals.length?Number(vals[0][0]):Number(x.amount||0);
}

async function systemExpectedForInvoice(x,expectedRows=null,cycle=null){
  let rows=expectedRows||await combinedExpected(),
      exp=(x.expectedId?rows.find(e=>e.id===x.expectedId):null);
  if(!exp&&x.rentalId&&x.period)exp=rows.find(e=>e.rentalId===x.rentalId&&Number(e.period||0)===Number(x.period||0));
  cycle=cycle||await findCycle(invoiceCycleId(x));

  let start=exp?.startDate||x.systemStartDate||x.startDate||"",
      end=exp?.endDate||x.systemEndDate||x.endDate||"",
      naturalEnd=start?addDays(nextBillingPeriodStart(start),-1):"",
      isProrata=!!(start&&end&&naturalEnd&&end<naturalEnd),
      amount=Number(exp?.amount||0);

  if(amount<=0&&isProrata){
    let monthly=await referenceMonthlyAmount(x,cycle);
    if(monthly>0)amount=Math.round(monthly*(days(start,end)/div(start))*100)/100;
  }
  if(amount<=0)amount=Number(x.systemExpectedAmount??x.amount??0);

  return {amount,start,end,naturalEnd,isProrata,expectedId:exp?.id||x.expectedId||""};
}
function approvedFromSystem(x){
  // Vendor claim yang sudah diputus user TIDAK BERHAK tetap menjadi bukti, tetapi tidak masuk DPP/NETT/Pranota.
  if(x.claimDecision==="TIDAK_BERHAK_DITAGIH"||x.claimDecision==="DITOLAK_USER")return 0;
  return Number(x.systemExpectedAmount??x.amount??0)+Number(x.adjustment||0);
}

async function correctionPreview(x,newEnd){
  let cycleId=invoiceCycleId(x),cycle=await findCycle(cycleId),start=effectiveDetailStart(x),systemEnd=x.systemEndDate||x.endDate||"",naturalEnd=start?addDays(nextBillingPeriodStart(start),-1):systemEnd;
  let monthly=await referenceMonthlyAmount(x,cycle),newExpected=Number((x.systemExpectedAmount??x.amount) || 0),isClosing=false,daysUsed=0,den=0;
  // Closing ditentukan terhadap akhir periode normal, bukan systemEnd yang mungkin sudah terpotong oleh returnDate.
  if(start&&newEnd&&naturalEnd&&newEnd<naturalEnd){
    isClosing=true;daysUsed=days(start,newEnd);den=div(start);newExpected=Math.round(monthly*(daysUsed/den)*100)/100;
  }else{
    let sys=await systemExpectedForInvoice(x,null,cycle);newExpected=Number(sys.amount||newExpected);
  }
  let paidImpact=isClosing?await paidImpactForReturn(cycleId,newEnd):[];
  return {cycle,cycleId,start,systemEnd,naturalEnd,monthly,newExpected,isClosing,daysUsed,den,paidImpact};
}
async function syncCycleReturn(cycleId,newReturn,reason){
  let actual=(await all("rentals")).find(r=>r.id===cycleId);
  if(actual){actual.returnDate=newReturn;actual.updatedAt=now();await put("rentals",actual);return}
  let cycle=await findCycle(cycleId),ov=(await all("rentalOverrides")).find(x=>x.id===cycleId)||{id:cycleId,startDate:cycle?.startDate||"",reason:reason||"Koreksi tanggal kembali dari detail tagihan"};
  ov.returnDate=newReturn;ov.reason=reason||ov.reason;ov.updatedAt=now();await put("rentalOverrides",ov);
}
async function updateAdjustmentPreview(){
  let x=(await all("invoices")).find(z=>z.id===currentAdjustId);if(!x)return;
  let ce=parseDate($("adjustEnd").value),save=$("adjustSave");
  if(!ce){$("adjustPreview").innerHTML="Tanggal akhir belum valid.";save.disabled=true;return}
  let start=effectiveDetailStart(x);if(ce<start){$("adjustPreview").innerHTML="<b>Tanggal akhir lebih kecil dari tanggal awal.</b>";save.disabled=true;return}
  let p=await correctionPreview(x,ce),manual=Number($("adjustAmount").value||0),label=p.isClosing?"Prorata Otomatis":"Expected Sistem",approved=p.newExpected+manual;
  $("adjustPreview").innerHTML=`${label} <b>${money(p.newExpected)}</b>${p.isClosing?` <span class="small muted">(${p.daysUsed}/${p.den} hari)</span>`:""}<br>Koreksi Manual <b>${money(manual)}</b><br>Disetujui <b>${money(approved)}</b>`;
  if(p.paidImpact.length){
    let periods=p.paidImpact.map(i=>`P${i.period} ${i.invoiceNo||""}`).join(", ");
    $("adjustImpact").style.display="block";$("adjustImpact").innerHTML=`<b>KOREKSI DIBLOKIR.</b><br>Perubahan tanggal akhir akan memengaruhi periode yang sudah dibayar: ${esc(periods)}. Data PAID tidak boleh berubah.`;save.disabled=true;
  }else{
    $("adjustImpact").style.display=p.isClosing?"block":"none";
    if(p.isClosing)$("adjustImpact").innerHTML=`Tanggal akhir lebih cepat dari akhir periode. Saat disimpan, sistem akan <b>menutup Rental Cycle pada ${fmtDate(ce)}</b>, menghitung ulang prorata, membatalkan expected setelah tanggal kembali, dan review ulang invoice yang belum dibayar.`;
    save.disabled=false;
  }
}
async function openAdjustment(id){
  let x=(await all("invoices")).find(z=>z.id===id);if(!x)return;
  if(isSupersededDetail(x))return alert("Detail berstatus DIGANTI / TIDAK DIHITUNG. Koreksi Nilai hanya boleh pada detail yang masih aktif.");
  if(x.paymentStatus==="SUDAH DIBAYAR")return alert("Detail sudah dibayar dan tidak boleh dikoreksi.");
  if(x.pranotaNo)return alert(`Koreksi Nilai diblokir karena tagihan sudah masuk Pranota ${x.pranotaNo}. Batalkan/buka Pranota terlebih dahulu.`);
  currentAdjustId=id;
  let autoExpected=Number(x.systemExpectedAmount??x.amount??0),autoLabel=x.expectedKind==="PRORATA"?"Prorata Otomatis":"Expected Sistem";
  $("adjustInfo").innerHTML=`${esc(x.invoiceNo)} • ${esc(x.container)} • Periode ${esc(x.period||"?")}<br>${autoLabel}: <b>${money(autoExpected)}</b><br>${esc((x.problems||[]).filter(p=>p!=="PENDING SATU TAGIHAN: ADA DETAIL LAIN BERMASALAH").join(" • ")||"Tidak ada masalah detail.")}`;
  $("adjustStart").value=fmtDate(effectiveDetailStart(x));
  $("adjustEnd").value=fmtDate(effectiveDetailEnd(x));
  $("adjustAmount").value=Number(x.adjustment||0);
  $("adjustNote").value=x.adjustmentNote||(x.problems||[]).filter(p=>p!=="PENDING SATU TAGIHAN: ADA DETAIL LAIN BERMASALAH").join(" / ");
  $("adjustImpact").style.display="none";$("adjustSave").disabled=false;
  $("adjustDialog").showModal();await updateAdjustmentPreview();
}
$("adjustEnd").addEventListener("input",()=>debounce(updateAdjustmentPreview,120));$("adjustAmount").addEventListener("input",()=>debounce(updateAdjustmentPreview,80));
$("adjustCancel").onclick=()=>$("adjustDialog").close();
$("adjustSave").onclick=async()=>{
  let x=(await all("invoices")).find(z=>z.id===currentAdjustId);if(!x)return;
  if(isSupersededDetail(x))return alert("Koreksi diblokir karena detail sudah DIGANTI / TIDAK DIHITUNG.");
  if(x.paymentStatus==="SUDAH DIBAYAR")return alert("Koreksi diblokir karena detail sudah dibayar.");
  if(x.pranotaNo)return alert(`Koreksi diblokir karena tagihan sudah masuk Pranota ${x.pranotaNo}.`);
  let cs=effectiveDetailStart(x),ce=parseDate($("adjustEnd").value);if(!ce)return alert("Tanggal akhir koreksi tidak valid.");if(ce<cs)return alert("Tanggal akhir masih lebih kecil dari tanggal awal.");
  let preview=await correctionPreview(x,ce);
  if(preview.paidImpact.length)return alert(`Koreksi diblokir. Perubahan memengaruhi ${preview.paidImpact.length} periode yang sudah dibayar.`);
  if(preview.naturalEnd&&ce>preview.naturalEnd)return alert(`Tanggal akhir tidak boleh melewati akhir periode normal ${fmtDate(preview.naturalEnd)}. Gunakan Pengembalian/Koreksi Rental bila tanggal kembali berada di periode berikutnya.`);
  let old={startDate:x.startDate,endDate:x.endDate,correctedEndDate:x.correctedEndDate||"",adjustment:x.adjustment||0,note:x.adjustmentNote||"",resolved:!!x.resolved};
  // Tanggal awal tidak pernah diubah dari Koreksi Detail.
  x.correctedEndDate=ce;
  x.correctedStartDate="";
  x.adjustment=Number($("adjustAmount").value||0); // 0 adalah nilai valid dan harus tersimpan.
  x.adjustmentAuto=false;
  x.adjustmentSource="MANUAL";
  x.adjustmentNote=$("adjustNote").value.trim();
  if(x.adjustment===0&&/^prorata otomatis/i.test(x.adjustmentNote))x.adjustmentNote="";
  x.systemExpectedAmount=Number(preview.newExpected||x.systemExpectedAmount||x.amount||0);
  x.expectedKind=preview.isClosing?"PRORATA":"EXPECTED";
  x.resolved=true;x.resolvedAt=now();x.approvedAmount=approvedFromSystem(x);
  if(x.approvalStatus==="APPROVED"){x.approvalStatus="";x.approvedAt="";x.approvedBy="";}
  if(preview.isClosing){await syncCycleReturn(preview.cycleId,ce,x.adjustmentNote||"Closing dari koreksi detail tagihan");invalidateCaches();}
  await put("invoices",x);
  await put("oplog",auditLog("ADJUSTMENT",x.invoiceNo,today(),"DITERIMA",`Detail ${x.container} P${x.period}: akhir ${fmtDate(ce)} • adjustment ${money(x.adjustment)} • ${x.adjustmentNote||"koreksi detail"}`,JSON.stringify({old,new:{correctedEndDate:x.correctedEndDate,adjustment:x.adjustment,note:x.adjustmentNote,resolved:true,cycleClosed:preview.isClosing}})));
  $("adjustDialog").close();
  if(preview.isClosing){await reReviewUnpaidInvoices();await setSetting("lastExpectedCalculation",today())}
  else await targetedReReview(x.container);
  invalidateCaches();
  let scopeInv=sessionStorage.getItem("reviewScopeInvoice"),scopeContainer=sessionStorage.getItem("reviewScopeContainer");
  if(scopeInv){await renderScopedReview(scopeInv,scopeContainer||x.container)}else if(visibleView()==="invoices")await renderInv();
};
async function openRentalDetail(id){
  invalidateCaches();
  let cycles=await legacyCycles(),cycle=cycles.find(x=>x.id===id);if(!cycle)return;
  let same=cycles.filter(x=>x.container===cycle.container).sort((a,b)=>(a.startDate||"").localeCompare(b.startDate||""));
  let exp=await combinedExpected(),periods=exp.filter(x=>x.rentalId===cycle.id).sort((a,b)=>Number(a.period||0)-Number(b.period||0));
  let inv=(await all("invoices")).filter(x=>invoiceCycleId(x)===cycle.id),byP={};
  for(let x of inv)(byP[Number(x.period||0)]??=[]).push(x);
  $("rentalDetailHead").innerHTML=`<b>${esc(cycle.container)}</b> • Cycle ${esc(cycle.id)}<br>Ambil: <b>${fmtDate(cycle.startDate)}</b> • Kembali: <b>${fmtDate(cycle.returnDate)||"Belum dikembalikan"}</b> • Status: ${badge(cycle.status)}<br>Sumber cycle: <b>${esc(cycle.source==="SIKLUS_REFERENCE"?"Siklus.xlsx":cycle.source||"-")}</b>${cycle.returnSource?` • Sumber kembali: <b>${esc(cycle.returnSource)}</b>`:""}${cycle.referenceConflict?`<div class="badtext">${esc(cycle.referenceConflict)}</div>`:""}<br>Tarif: <b>${cycle.rate?money(cycle.rate):"Belum terkonfirmasi"}</b> • ${esc(cycle.basis||"")} • Vendor: ${esc(cycle.vendor||"-")}`;
  $("rentalDetailPeriods").innerHTML=periods.length?periods.map(p=>{let rows=byP[Number(p.period)]||[],invoice=[...new Set(rows.map(x=>x.invoiceNo).filter(Boolean))].join(", "),st=rows.length?(rows.every(x=>x.paymentStatus==="SUDAH DIBAYAR")?"SUDAH DIBAYAR":rows.some(x=>x.decision==="PENDING")?"PENDING":"READY TO PAY"):p.status,natural=addDays(nextBillingPeriodStart(p.startDate),-1),label=p.endDate<natural?"Prorata Otomatis":"Expected Sistem";return `<div class="rejectitem"><b>P${p.period} • ${fmtDate(p.startDate)}–${fmtDate(p.endDate)}</b><div>${badge(st)} ${invoice?`• Invoice ${esc(invoice)}`:"• Belum ada invoice"}</div><div class="small muted">${label}: ${p.amount==null?"Belum terkonfirmasi":money(p.amount)}</div></div>`}).join(""):'<div class="rejectitem muted">Belum ada periode.</div>';
  $("rentalDetailHistory").innerHTML=same.map((c,i)=>`<div class="rejectitem" style="${c.id===cycle.id?'background:#eff5ff':''}"><b>Siklus ${i+1}${c.id===cycle.id?' • DIPILIH':''}</b><div>Ambil ${fmtDate(c.startDate)} • Kembali ${fmtDate(c.returnDate)||"Belum kembali"}</div><div class="small">${badge(c.status)} • ${esc(c.id)}</div></div>`).join("");
  $("rentalDetailDialog").showModal();
}
$("rentalDetailClose").onclick=()=>$("rentalDetailDialog").close();
async function openRentalCorrection(id){
  let cycles=await legacyCycles(),r=cycles.find(x=>x.id===id);if(!r)return;currentRentalId=id;
  let hasPaid=await cycleHasPaid(r.id);
  $("rentalEditInfo").innerHTML=`${esc(r.container)} • ID ${esc(r.id)}<br>Tanggal saat ini: <b>${fmtDate(r.startDate)}</b>${r.returnDate?` s/d <b>${fmtDate(r.returnDate)}</b>`:""}`;
  $("editStart").value=fmtDate(r.startDate);$("editReturn").value=fmtDate(r.returnDate);$("editReason").value=r.correctionReason||"";
  $("editStart").readOnly=hasPaid;$("editStart").classList.toggle("readonly",hasPaid);
  $("rentalImpact").style.display=hasPaid?"block":"none";
  if(hasPaid)$("rentalImpact").innerHTML="<b>PERINGATAN:</b> cycle ini memiliki tagihan yang sudah dibayar. Tanggal Ambil dikunci. Perubahan Tanggal Kembali juga akan diperiksa dan diblokir bila memengaruhi periode PAID.";
  $("rentalDialog").showModal();
}
$("rentalCancel").onclick=()=>$("rentalDialog").close();
$("rentalSave").onclick=async()=>{
  let cycles=await legacyCycles(),r=cycles.find(x=>x.id===currentRentalId);if(!r)return;
  let start=parseDate($("editStart").value),ret=$("editReturn").value.trim()?parseDate($("editReturn").value):"",reason=$("editReason").value.trim();
  if(!start)return alert("Tanggal ambil tidak valid.");if(ret&&ret<start)return alert("Tanggal kembali tidak boleh lebih kecil dari tanggal ambil.");if(!reason)return alert("Alasan koreksi wajib diisi.");
  let paidRows=(await cycleInvoices(r.id)).filter(x=>x.paymentStatus==="SUDAH DIBAYAR");
  if(paidRows.length&&start!==r.startDate)return alert("Tanggal Ambil tidak boleh diubah karena cycle ini sudah memiliki tagihan yang dibayar.");
  if(ret){let impacted=await paidImpactForReturn(r.id,ret);if(impacted.length)return alert(`Tanggal Kembali tidak boleh diubah ke ${fmtDate(ret)} karena memengaruhi periode yang sudah dibayar: ${impacted.map(x=>`P${x.period}`).join(", ")}.`)}
  let same=cycles.filter(x=>x.container===r.container&&x.id!==r.id).sort((a,b)=>a.startDate.localeCompare(b.startDate));
  if(same.some(x=>x.startDate===start))return alert("Tanggal ambil tersebut sudah dipakai siklus lain.");
  let prev=same.filter(x=>x.startDate<start).sort((a,b)=>b.startDate.localeCompare(a.startDate))[0],next=same.find(x=>x.startDate>start);
  if(prev&&(!prev.returnDate||prev.returnDate>=start))return alert(`Koreksi menyebabkan overlap dengan siklus sebelumnya ${fmtDate(prev.startDate)}.`);
  if(ret&&next&&ret>=next.startDate)return alert(`Tanggal kembali melewati siklus berikutnya ${fmtDate(next.startDate)}.`);
  let old={startDate:r.startDate,returnDate:r.returnDate||""};
  await put("rentalOverrides",{id:r.id,startDate:start,returnDate:ret,reason,updatedAt:now()});
  await put("oplog",auditLog("KOREKSI RENTAL",r.container,start,"DITERIMA",reason,JSON.stringify({rentalId:r.id,old,new:{startDate:start,returnDate:ret}})));
  $("rentalDialog").close();await reReviewUnpaidInvoices();await setSetting("lastExpectedCalculation",today());await refresh();
};
async function openTax(invoiceNo,vendor){
  let h=(await invoiceHeaders()).find(x=>x.invoiceNo===invoiceNo&&x.vendor===vendor);if(!h)return;currentTaxInvoice=invoiceNo;currentTaxVendor=vendor;
  $("taxInfo").innerHTML=`${esc(invoiceNo)}<br>DPP saat ini: <b>${money(h.dpp)}</b>`;$("taxPpnEnabled").value=h.ppnEnabled?"YA":"TIDAK";$("taxPpnRate").value=h.ppnRate;$("taxPphEnabled").value=h.pphEnabled?"YA":"TIDAK";$("taxPphRate").value=h.pphRate;$("taxDialog").showModal();
}
$("taxCancel").onclick=()=>$("taxDialog").close();
$("taxSave").onclick=async()=>{let masters=Object.fromEntries((await all("masters")).map(m=>[m.container,m])),targetVendor=String(currentTaxVendor||"").toUpperCase(),items=(await activeInvoiceRows()).filter(x=>x.invoiceNo===currentTaxInvoice&&String(x.vendor||masters[x.container]?.vendor||"").toUpperCase()===targetVendor),ppnEnabled=$("taxPpnEnabled").value==="YA",pphEnabled=$("taxPphEnabled").value==="YA",ppnRate=Number($("taxPpnRate").value||0),pphRate=Number($("taxPphRate").value||0);for(let x of items){x.ppnEnabled=ppnEnabled;x.ppnRate=ppnRate;x.pphEnabled=pphEnabled;x.pphRate=pphRate;await put("invoices",x)}await put("oplog",auditLog("ADJUSTMENT",currentTaxInvoice,today(),"DITERIMA",`Pajak invoice: PPN ${ppnEnabled?ppnRate+"%":"Tidak"}; PPh ${pphEnabled?pphRate+"%":"Tidak"}`,currentTaxInvoice));$("taxDialog").close();await refresh();};


let currentPaymentEditId="";

function paymentInvoiceRows(inv,p){
  let ids=new Set((p.invoiceNos||[]).map(String));
  return inv.filter(x=>
    (p.id&&x.paymentId===p.id) ||
    (p.reference&&x.paymentNo===p.reference&&(!ids.size||ids.has(String(x.invoiceNo||""))))
  );
}
async function openPaymentEdit(id){
  let p=(await all("payments")).find(x=>x.id===id);if(!p)return alert("Pembayaran tidak ditemukan.");
  if(p.status==="DIBATALKAN")return alert("Pembayaran sudah dibatalkan.");
  currentPaymentEditId=id;
  let actual=Number(p.actualAmount??p.amount??0);
  $("paymentEditInfo").innerHTML=`Bukti <b>${esc(p.reference||"-")}</b> • ${fmtDate(p.date)}<br><span class="small muted">${esc((p.pranotaNos||[]).join(", ")||p.pranotaNo||"-")} • ${esc((p.invoiceNos||[]).join(", ")||p.invoiceNo||"-")}</span>`;
  $("peDate").value=fmtDate(p.date);$("peRef").value=p.reference||"";$("peNett").value=Number(p.amount||0);$("peActualAmount").value=actual;$("peNote").value=p.note||"";updatePaymentEditDifference();
  $("paymentEditDialog").showModal();
}
async function savePaymentEdit(){
  let p=(await all("payments")).find(x=>x.id===currentPaymentEditId);if(!p)return alert("Pembayaran tidak ditemukan.");
  if(p.status==="DIBATALKAN")return alert("Pembayaran sudah dibatalkan.");
  let date=parseDate($("peDate").value),ref=$("peRef").value.trim(),note=$("peNote").value.trim(),actualAmount=Number($("peActualAmount").value);
  if(!date)return alert("Tanggal bayar tidak valid.");if(!ref)return alert("No. Bukti Bayar wajib diisi.");if(!Number.isFinite(actualAmount)||actualAmount<0)return alert("Total Bayar Aktual tidak valid.");
  let difference=Math.round((actualAmount-Number(p.amount||0))*100)/100,old={date:p.date,reference:p.reference||"",actualAmount:Number(p.actualAmount??p.amount??0),difference:Number(p.difference??0),note:p.note||""},inv=await all("invoices"),rows=paymentInvoiceRows(inv,p),pr=await all("pranotas");
  p.date=date;p.reference=ref;p.actualAmount=actualAmount;p.difference=difference;p.note=note;p.updatedAt=now();await put("payments",p);
  for(let x of rows){x.paymentDate=date;x.paymentNo=ref;await put("invoices",x)}
  for(let x of pr.filter(x=>x.paymentId===p.id||(p.pranotaNos||[]).includes(x.pranotaNo))){
    if(x.status==="DIBAYAR"){x.paymentDate=date;x.paymentNo=ref;await put("pranotas",x)}
  }
  await put("oplog",auditLog("UBAH PEMBAYARAN",ref,date,"DITERIMA",`${rows.length} detail invoice • ${money(p.amount||0)}`,JSON.stringify({paymentId:p.id,old,new:{date,reference:ref,actualAmount,difference,note}})));
  $("paymentEditDialog").close();currentPaymentEditId="";invalidateCaches();await refresh();
}
async function cancelPayment(id){
  let p=(await all("payments")).find(x=>x.id===id);if(!p)return alert("Pembayaran tidak ditemukan.");
  if(p.status==="DIBATALKAN")return alert("Pembayaran sudah dibatalkan.");
  if(!confirm(`Batalkan pembayaran ${p.reference||p.id}?\n\nTagihan terkait akan kembali ke status sebelum dibayar. Pranota tidak dihapus dan dapat dibayar ulang.`))return;
  let inv=await all("invoices"),rows=paymentInvoiceRows(inv,p),pr=await all("pranotas");
  // Jangan membatalkan jika detail sudah terikat pembayaran lain.
  let conflicts=rows.filter(x=>x.paymentId&&x.paymentId!==p.id);
  if(conflicts.length)return alert("Pembatalan diblokir karena ada detail yang sudah terikat pembayaran lain.");
  for(let x of rows){
    x.paymentStatus="BELUM DIBAYAR";x.paymentNo="";x.paymentDate="";x.paymentId="";
    x.resolved=effectiveDetailProblems(x).length===0;x.decision=effectiveDetailProblems(x).length&&!x.resolved?"PENDING":"READY TO PAY";
    await put("invoices",x);
  }
  for(let x of pr.filter(x=>x.paymentId===p.id||(p.pranotaNos||[]).includes(x.pranotaNo))){
    x.status="DRAFT";x.paymentId="";x.paymentNo="";x.paymentDate="";await put("pranotas",x);
  }
  p.status="DIBATALKAN";p.cancelledAt=now();p.cancelledReference=p.reference||"";await put("payments",p);
  await put("oplog",auditLog("BATAL PEMBAYARAN",p.reference||p.id,today(),"DITERIMA",`${rows.length} detail invoice dikembalikan ke status review • pranota tetap tersedia`,(p.pranotaNos||[]).join("|")));
  invalidateCaches();await reReviewUnpaidInvoices();if((await getSetting("supervisorCycleGuardVersion"))!=="CONFIRMED_CYCLE_V1"){
    await reReviewUnpaidInvoices();
    await setSetting("supervisorCycleGuardVersion","CONFIRMED_CYCLE_V1");
  }
  if((await getSetting("reviewSingleSourceVersion"))!=="ACTIVE_SOURCE_V1"){
    await reReviewUnpaidInvoices();
    await setSetting("reviewSingleSourceVersion","ACTIVE_SOURCE_V1");
  }
  await refresh();
}

async function renderPay(){
  await renderPaymentPranotaCandidates();await syncPaymentSelection();
  let r=(await all("payments")).sort((a,b)=>(b.date||"").localeCompare(a.date||"")),
      q=String($("paymentSearch")?.value||"").trim().toUpperCase();
  if(q)r=r.filter(x=>[
    x.reference,
    ...(x.pranotaNos||[]),x.pranotaNo,
    ...(x.invoiceNos||[]),x.invoiceNo,
    x.note
  ].some(v=>String(v||"").toUpperCase().includes(q)));
  if($("paymentSearchInfo"))$("paymentSearchInfo").textContent=q?`${r.length} pembayaran ditemukan`:`${r.length} pembayaran`;
  $("paymentRows").innerHTML=r.length?r.map(x=>{let actual=Number(x.actualAmount??x.amount??0),diff=Number(x.difference??(actual-Number(x.amount||0)));return `<tr${x.status==="DIBATALKAN"?' style="opacity:.6"':""}><td>${fmtDate(x.date)}</td><td><b>${esc(x.reference||"")}</b>${x.status==="DIBATALKAN"?`<div class="badtext">DIBATALKAN</div>`:""}</td><td>${esc((x.pranotaNos||[]).join(", ")||x.pranotaNo||"")}</td><td class="wrap">${esc((x.invoiceNos||[]).join(", ")||x.invoiceNo||"")}</td><td>${money(x.amount)}</td><td>${money(actual)}</td><td>${money(diff)}</td><td>${esc(x.note||"")}</td><td>${esc(x.source||"")}</td><td>${x.status==="DIBATALKAN"?"":`<button class="mini" data-edit-payment="${esc(x.id)}">Edit</button> <button class="mini danger" data-cancel-payment="${esc(x.id)}">Batalkan</button>`}</td></tr>`}).join(""):`<tr><td colspan="10" class="muted">Tidak ada pembayaran sesuai pencarian.</td></tr>`;
  document.querySelectorAll("[data-edit-payment]").forEach(b=>b.onclick=()=>openPaymentEdit(b.dataset.editPayment));
  document.querySelectorAll("[data-cancel-payment]").forEach(b=>b.onclick=()=>cancelPayment(b.dataset.cancelPayment));
}
async function combinedAudit(){
  let op=(await all("oplog")).map(x=>({displayTime:new Date(x.importedAt).toLocaleString("id-ID"),type:x.type,ref:x.container||"",dateDisplay:fmtDate(x.date),status:x.status,reason:x.reason||"",raw:x.raw||"",sort:x.importedAt||""}));
  let inv=await all("invoices"),groups={};
  for(let x of inv.filter(i=>i.legacy)){
    let k=x.invoiceNo||"(tanpa nomor)";
    if(!groups[k])groups[k]={invoiceNo:k,date:x.invoiceDate||"",decision:x.decision,rows:0,source:"kontainer.xlsx"};
    groups[k].rows++;
    if(x.decision==="PENDING")groups[k].decision="PENDING";
  }
  let initial=Object.values(groups).map(x=>({displayTime:"DATA AWAL EXCEL",type:"DATA AWAL",ref:x.invoiceNo,dateDisplay:fmtDate(x.date),status:"TERCATAT",reason:`${x.rows} detail • keputusan awal ${x.decision}`,raw:x.source,sort:"2026-08-23T00:00:00.000Z"}));
  return [...op,...initial].sort((a,b)=>b.sort.localeCompare(a.sort));
}
async function renderAudit(){
  let r=await combinedAudit(),t=$("auditType").value,s=$("auditStatus").value;if(t)r=r.filter(x=>x.type===t);if(s)r=r.filter(x=>x.status===s);
  $("auditRows").innerHTML=r.length?r.map(x=>`<tr><td>${esc(x.displayTime)}</td><td>${esc(x.type)}</td><td>${esc(x.ref)}</td><td>${esc(x.dateDisplay)}</td><td>${badge(x.status)}</td><td class="wrap">${esc(x.reason)}</td><td class="wrap">${esc(x.raw)}</td></tr>`).join(""):`<tr><td colspan="7" class="muted">Tidak ada log sesuai filter.</td></tr>`;
}
async function targetedReReview(container){
  container=norm(container);invalidateCaches();let expected=await combinedExpected(),rent=await legacyCycles(),rows=await all("invoices"),targets=rows.filter(x=>x.container===container&&x.paymentStatus!=="SUDAH DIBAYAR"&&!isSupersededDetail(x)&&isWorkflowReviewRow(x)),changed=0,invoiceNos=new Set();
  for(let x of targets){invoiceNos.add(norm(x.invoiceNo));let before=JSON.stringify([x.problems,x.rentalId,x.expectedId,x.claimStatus,x.claimDecision,x.adjustment,x.reviewDecision]);let a=await assess(x,rows.filter(z=>!isSupersededDetail(z)),expected,rent);x.expectedId=a.expectedId;x.rentalId=a.rentalId||"";x.canonicalCycleId=a.canonicalCycleId||"";x.vendorPeriod=a.vendorPeriod||x.vendorPeriod||x.period;x.systemPeriod=a.systemPeriod??null;x.systemStartDate=a.startDate||"";x.systemEndDate=a.endDate||"";x.systemExpectedAmount=String(a.claimStatus).startsWith("NO_ENTITLEMENT")?0:Number(a.systemExpectedAmount||0);x.claimStatus=a.claimStatus;x.claimFlags=a.claimFlags||[];x.issueEvidence=a.evidence||{};x.problems=[...new Set(a.problems||[])];if(x.claimDecision==="TIDAK_BERHAK_DITAGIH"&&!x.problems.includes("USER MENETAPKAN: TIDAK BERHAK DITAGIH"))x.problems.push("USER MENETAPKAN: TIDAK BERHAK DITAGIH");if(x.duplicateDecision==="BUKAN_DUPLIKAT"||x.claimDecision==="DITOLAK_USER")x.problems=x.problems.filter(p=>!duplicateProblemsOnly(p));if(x.creditResolution?.status==="RESOLVED")x.resolved=effectiveDetailProblems(x).length===0;else x.resolved=(x.duplicateDecision==="BUKAN_DUPLIKAT"||x.claimDecision==="DITOLAK_USER")&&effectiveDetailProblems(x).length===0;x.decision=effectiveDetailProblems(x).length&&!x.resolved?"PENDING":"READY TO PAY";x.approvedAmount=approvedFromSystem(x);x.reviewedAt=now();if(before!==JSON.stringify([x.problems,x.rentalId,x.expectedId,x.claimStatus,x.claimDecision,x.adjustment,x.reviewDecision]))changed++}
  await putMany("invoices",targets);invalidateCaches();await applyInvoiceGroupStatus(invoiceNos);invalidateCaches();return {changed,count:targets.length};
}
function simulateCyclePeriods(cycle){let end=cycle.returnDate||cycle.inferredEnd||today(),periods=buildBillingPeriods(cycle.startDate,end,240);return periods.map(pr=>{let naturalEnd=addDays(nextBillingPeriodStart(pr.startDate),-1),full=pr.endDate===naturalEnd,amount=0;if(cycle.basis==="BULANAN"){amount=Number(cycle.monthlyRate||cycle.rate||0);if(!full)amount*=days(pr.startDate,pr.endDate)/div(pr.startDate)}else if(cycle.basis==="HARIAN")amount=Number(cycle.dailyRate||cycle.rate||0)*days(pr.startDate,pr.endDate);return {...pr,amount:Math.round(amount*100)/100,rentalId:cycle.id}})}
async function reviewContainer(container){
  container=norm(container);let cycles=(await legacyCycles()).filter(x=>x.container===container),exp=(await combinedExpected()).filter(x=>x.container===container),inv=(await activeInvoiceRows()).filter(x=>x.container===container),pr=await all("pranotas"),pay=await all("payments"),masters=(await all("masters")).filter(x=>x.container===container);
  let pending=inv.filter(x=>x.decision==="PENDING"),noEnt=inv.filter(x=>String(x.claimStatus||"").startsWith("NO_ENTITLEMENT")),confirmedNoEnt=inv.filter(x=>x.claimDecision==="TIDAK_BERHAK_DITAGIH"),paid=inv.filter(x=>x.paymentStatus==="SUDAH DIBAYAR"),pranota=inv.filter(x=>x.pranotaNo),approved=inv.filter(x=>x.approvalStatus==="APPROVED");
  let root=paid.some(x=>pending.includes(x))?"PAYMENT":pranota.some(x=>pending.includes(x))?"PRANOTA":approved.some(x=>pending.includes(x))?"APPROVAL":pending.length?"TAGIHAN VENDOR":cycles.some(x=>x.status==="PERLU KONFIRMASI")?"RENTAL / SIKLUS":!masters.length?"MASTER":"VALIDASI AKHIR";
  let simulated=cycles.flatMap(simulateCyclePeriods),bm=new Map(exp.map(x=>[`${x.rentalId}|${x.period}`,x])),am=new Map(simulated.map(x=>[`${x.rentalId}|${x.period}`,x])),changes=[];for(let k of new Set([...bm.keys(),...am.keys()])){let b=bm.get(k),a=am.get(k),status=!b?"AKAN DIBUAT":!a?"TIDAK BERLAKU":b.startDate!==a.startDate||b.endDate!==a.endDate||Math.abs(Number(b.amount||0)-Number(a.amount||0))>1?"BERUBAH":"TETAP";if(status!=="TETAP")changes.push({k,b,a,status})}
  let conclusion=noEnt.length?`${noEnt.length} detail vendor claim tidak mempunyai hak tagih/Expected pada siklus yang terhubung. Bukti vendor tetap disimpan; keputusan akhir user.`:pending.length?`${pending.length} detail masih perlu review sebelum Approval.`:"Tidak ditemukan PR tagihan aktif pada container ini.";
  let suggestion=paid.length&&pending.length?"Mulai dari transaksi terakhir: batalkan Payment yang mengunci koreksi, lalu kembali ke guide.":noEnt.some(x=>!x.claimDecision)?"Review siklus yang benar. Jika vendor memang menagih setelah hak sewa berakhir, konfirmasi TIDAK BERHAK DITAGIH; jangan membuat Expected palsu.":pending.length?"Selesaikan PR Tagihan Vendor. Setelah edit, kembali dan Review Ulang Step.":"Lakukan validasi akhir Before vs Simulasi After.";
  return {container,cycles,exp,inv,pending,noEnt,confirmedNoEnt,paid,pranota,approved,root,changes,conclusion,suggestion};
}

async function globalLinkageAudit(){
  // Audit read-only V7.1.8: Tgl Tagihan hanya bukti dokumen, bukan guard Cycle.
  let [rows,detached,expected,rent]=await Promise.all([activeInvoiceRows(),all("detachedInvoices"),combinedExpected(),legacyCycles()]);
  let byContainer=new Map();for(let r of rent){if(!byContainer.has(r.container))byContainer.set(r.container,[]);byContainer.get(r.container).push(r)}
  let findings=[];
  for(let x of rows){
    if(!x.container)continue;
    let cycles=byContainer.get(x.container)||[],linkedId=x.rentalId||x.canonicalCycleId||"",linked=cycles.find(r=>r.id===linkedId)||null,flags=[],severity="",vp=Number(x.vendorPeriod||x.period||0),refStart=x.migrationStartDate||"",refEnd=x.migrationEndDate||"";
    let exact=cycles.filter(r=>{let e=expected.find(z=>z.rentalId===r.id&&Number(z.period||0)===vp);return !!e&&(!refStart||e.startDate===refStart)&&(!refEnd||e.endDate===refEnd)});
    if(x.migrationMode&&exact.length>1){severity="SUSPECT";flags.push(`${exact.length} cycle cocok exact dengan P${vp} + referensi tanggal: AMBIGU`)}
    if(x.migrationMode&&exact.length===1&&linked&&linked.id!==exact[0].id){severity="KRITIS";flags.push(`Cycle terpasang berbeda dari satu-satunya Cycle yang cocok exact referensi P${vp}`)}
    if(x.migrationMode&&exact.length===0){severity=severity||"SUSPECT";flags.push(`Tidak ada Cycle yang cocok exact dengan P${vp} + referensi tanggal`)}
    if(!linked){severity=severity||"SUSPECT";flags.push("Detail belum terhubung ke Cycle")}
    if(linked){let has=expected.some(e=>e.rentalId===linked.id&&Number(e.period||0)===vp);if(!has){severity=severity||"SUSPECT";flags.push(`P vendor ${vp} tidak mempunyai Expected pada cycle terpasang`)}}
    if(flags.length)findings.push({kind:"ACTIVE",id:x.id,container:x.container,invoiceNo:x.invoiceNo||"",invoiceDate:x.invoiceDate,period:vp,amount:Number(x.amount||0),paymentStatus:x.paymentStatus||"",linkedId,linkedStart:linked?.startDate||"",severity,flags});
  }
  for(let x of detached){if(!x.container)continue;let flags=[];if(/salah|siklus|periode|tagih|expected/i.test(String(x.detachReason||"")))flags.push(`Alasan detach perlu review: ${x.detachReason}`);if(flags.length)findings.push({kind:"DETACHED",id:x.id,container:x.container,invoiceNo:x.invoiceNo||"",invoiceDate:x.invoiceDate,period:Number(x.vendorPeriod||x.period||0),amount:Number(x.amount||0),paymentStatus:x.paymentStatus||"",linkedId:x.rentalId||x.canonicalCycleId||"",linkedStart:"",severity:"ARSIP",flags})}
  findings.sort((a,b)=>({KRITIS:0,SUSPECT:1,ARSIP:2}[a.severity]??3)-({KRITIS:0,SUSPECT:1,ARSIP:2}[b.severity]??3)||a.container.localeCompare(b.container)||(a.invoiceNo||"").localeCompare(b.invoiceNo||""));
  return {findings,active:rows.length,detached:detached.length,critical:findings.filter(x=>x.severity==="KRITIS").length,suspect:findings.filter(x=>x.severity==="SUSPECT").length,archive:findings.filter(x=>x.severity==="ARSIP").length};
}
function buildAuditCases(a){
  let byInvoice=new Map();
  for(let f of (a?.findings||[])){
    if(f.userResolved)continue;
    let key=f.invoiceNo||"(TANPA NO TAGIHAN)";
    if(!byInvoice.has(key))byInvoice.set(key,{invoiceNo:key,findings:[],containers:new Set(),critical:0,suspect:0,archive:0,paid:0});
    let g=byInvoice.get(key);g.findings.push(f);g.containers.add(f.container);if(f.severity==="KRITIS")g.critical++;else if(f.severity==="SUSPECT")g.suspect++;else g.archive++;if(f.paymentStatus==="SUDAH DIBAYAR")g.paid++;
  }
  let cases=[...byInvoice.values()];
  for(let g of cases){
    g.priority=g.critical?0:g.suspect?1:2;
    let roots=new Set();for(let f of g.findings){for(let z of f.flags||[]){if(/lebih awal|kronologi/i.test(z))roots.add("KRONOLOGI MUSTAHIL");else if(/tidak mempunyai Expected/i.test(z))roots.add("TANPA HAK/EXPECTED");else if(/beberapa cycle|multi-cycle|matching lama/i.test(z))roots.add("MULTI-CYCLE");else if(f.kind==="DETACHED")roots.add("ARSIP DETACH");else roots.add("RELASI SIKLUS");}}
    g.roots=[...roots];
  }
  cases.sort((x,y)=>x.priority-y.priority||y.paid-x.paid||y.findings.length-x.findings.length||x.invoiceNo.localeCompare(y.invoiceNo));
  return cases;
}
function renderAuditSnapshot(){
  let el=$("globalLinkAuditResult"),a=window.__linkAudit;if(!el||!a)return;
  let cases=buildAuditCases(a),q=norm($("auditInvoiceSearch")?.value||$("reviewCenterSearch")?.value||""),status=$("auditCaseFilter")?.value||$("reviewCenterFilter")?.value||"";
  if(q)cases=cases.filter(g=>norm(g.invoiceNo).includes(q)||[...g.containers].some(c=>norm(c).includes(q)));
  if(status==="KRITIS")cases=cases.filter(g=>g.critical);else if(status==="SUSPECT")cases=cases.filter(g=>!g.critical&&g.suspect);else if(status==="PAID")cases=cases.filter(g=>g.paid);else if(status==="ARSIP")cases=cases.filter(g=>!g.critical&&!g.suspect&&g.archive);
  let openFindings=(a.findings||[]).filter(x=>!x.userResolved),resolvedCount=(a.findings||[]).filter(x=>x.userResolved).length,allCases=buildAuditCases(a),allActive=allCases.filter(x=>x.critical||x.suspect),affectedContainers=new Set(openFindings.map(x=>x.container)),activeCases=cases.filter(x=>x.critical||x.suspect),archiveCases=cases.filter(x=>!x.critical&&!x.suspect),openCritical=openFindings.filter(x=>x.severity==="KRITIS").length,openSuspect=openFindings.filter(x=>x.severity==="SUSPECT").length;
  el.innerHTML=`<div class="note"><b>Snapshot Audit — READ ONLY.</b> ${a.active} detail aktif + ${a.detached} arsip detach diperiksa. <b>${openCritical} KRITIS OPEN</b> • <b>${openSuspect} SUSPECT OPEN</b> • <b>${resolvedCount} sudah diputus user</b> • ${a.archive} arsip perlu review.<br><b>Unit kerja supervisor:</b> ${allActive.length} No. Tagihan • ${affectedContainers.size} container masih terdampak. <b>Jumlah OPEN berkurang hanya setelah keputusan user/koreksi; pencarian tidak menjalankan audit ulang.</b></div>`+
    `<div class="toolbar" style="margin-top:10px"><div><label>Cari hasil audit: No. Tagihan / Container</label><input id="auditInvoiceSearch" value="${esc(q)}" placeholder="mis. ZONA260131526 / DFSU2296934"></div><div><label>Prioritas</label><select id="auditCaseFilter"><option value="">Semua temuan</option><option value="KRITIS" ${status==="KRITIS"?"selected":""}>KRITIS</option><option value="SUSPECT" ${status==="SUSPECT"?"selected":""}>SUSPECT</option><option value="PAID" ${status==="PAID"?"selected":""}>PAID terkunci</option><option value="ARSIP" ${status==="ARSIP"?"selected":""}>Arsip detach</option></select></div><div><button id="auditClearSearch">Bersihkan Filter</button></div></div>`+
    `<div class="note warn"><b>Hasil filter:</b> ${activeCases.length} No. Tagihan aktif${q?` untuk pencarian <b>${esc(q)}</b>`:""}. Klik <b>Tinjau Tagihan</b> untuk invoice utuh. Tidak ada keputusan otomatis.</div>`+
    (activeCases.length?activeCases.slice(0,100).map(g=>{let fs=g.findings.filter(f=>f.severity!=="ARSIP"),sample=fs.slice(0,6);return `<div class="rejectitem"><div style="display:flex;justify-content:space-between;gap:10px;align-items:flex-start;flex-wrap:wrap"><div><b>${esc(g.invoiceNo)}</b> • ${fs.length} temuan • ${g.containers.size} container ${g.paid?`• <b>${g.paid} PAID terkunci</b>`:""}<div class="small muted">Akar: ${esc(g.roots.join(" • ")||"RELASI SIKLUS")}</div></div><div><button class="mini primary" data-audit-invoice="${esc(g.invoiceNo)}">Tinjau Tagihan</button></div></div><div class="small">${sample.map(f=>`${badge(f.severity)} <b>${esc(f.container)} • P${esc(f.period||"?")}</b> • ${fmtDate(f.invoiceDate)}${f.paymentStatus==="SUDAH DIBAYAR"?" • PAID":""} <button class="mini" data-audit-guide="${esc(f.container)}">Guide</button><br><span class="badtext">${esc(f.flags.join(" • "))}</span>`).join("<br>")}${fs.length>6?`<br><b>+ ${fs.length-6} temuan lain dalam No. Tagihan ini</b>`:""}</div></div>`}).join(""):'<div class="rejectitem goodtext"><b>Tidak ada No. Tagihan aktif sesuai pencarian/filter.</b></div>')+
    (activeCases.length>100?`<div class="note">Menampilkan 100 No. Tagihan pertama dari ${activeCases.length} hasil filter. Persempit pencarian No. Tagihan/container.</div>`:"")+
    (archiveCases.length?`<details class="card" style="margin-top:10px"><summary><b>${archiveCases.length} No. Tagihan hanya berisi temuan arsip detach</b></summary>${archiveCases.slice(0,30).map(g=>`<div class="rejectitem"><b>${esc(g.invoiceNo)}</b> • ${g.findings.length} arsip • ${g.containers.size} container</div>`).join("")}</details>`:"");
  $("auditInvoiceSearch").oninput=()=>debounce(renderAuditSnapshot);$("auditCaseFilter").onchange=renderAuditSnapshot;$("auditClearSearch").onclick=()=>{$("auditInvoiceSearch").value="";$("auditCaseFilter").value="";renderAuditSnapshot()};
  document.querySelectorAll("[data-audit-guide]").forEach(b=>b.onclick=async()=>{let c=b.dataset.auditGuide;$("reviewCenterSearch").value=c;sessionStorage.setItem("reviewReturnContainer",c);await renderReviewCenter(c)});
  document.querySelectorAll("[data-audit-invoice]").forEach(b=>b.onclick=async()=>{let no=b.dataset.auditInvoice;sessionStorage.setItem("reviewReturnInvoice",no);$("invoiceSearch").value=no;$("reviewCenterSearch").value=no;invoicePage=1;reviewPage=1;await openScopedReview(no)});
}
async function renderGlobalLinkageAudit(){
  let el=$("globalLinkAuditResult");if(!el)return;el.style.display="block";el.innerHTML='<div class="muted">Audit relasi berjalan… tidak ada kalkulasi ulang Expected.</div>';
  window.__linkAudit=await globalLinkageAudit();try{localStorage.setItem("cbc_link_audit_snapshot_v1",JSON.stringify(window.__linkAudit))}catch(e){}renderAuditSnapshot();
}
async function openUnifiedInvoiceReview(query){
  let q=norm(query||$("reviewCenterSearch")?.value||"");
  if(!q)return alert("Isi No. Tagihan, No. Container, atau Vendor.");
  if($("reportType"))$("reportType").onchange=renderReports;if($("reportSearch"))$("reportSearch").oninput=()=>debounce(renderReports);if($("exportReport"))$("exportReport").onclick=exportCurrentReport;
if($("reviewCenterSearch"))$("reviewCenterSearch").value=q;
  if($("invoiceSearch"))$("invoiceSearch").value=q;
  reviewPage=1;invoicePage=1;
  await renderInv();
  let rows=await activeInvoiceRows();
  let exact=rows.filter(x=>norm(x.invoiceNo)===q);
  let matched=exact.length?exact:rows.filter(x=>norm(x.invoiceNo).includes(q)||norm(x.container).includes(q)||norm(effectiveInvoiceVendor(x)).includes(q));
  let box=$("reviewCenterDetail");
  if(!matched.length){if(box)box.innerHTML='<div class="card"><div class="badtext"><b>Data tidak ditemukan.</b> Periksa No. Tagihan / Container / Vendor.</div></div>';return}
  let invoiceNos=[...new Set(matched.map(x=>x.invoiceNo).filter(Boolean))];
  if(exact.length){
    let problems=exact.filter(x=>x.decision==="PENDING"||String(x.claimStatus||"").startsWith("NO_ENTITLEMENT")||(x.claimFlags||[]).length);
    let containers=[...new Set(problems.map(x=>x.container).filter(Boolean))];
    if(box)box.innerHTML=`<div class="card"><h3>Investigasi ${esc(q)}</h3><div class="note"><b>${exact.length} detail</b> • <b>${problems.length} perlu review</b> • ${containers.length} container bermasalah. Review dilakukan pada invoice ini saja; Expected global tidak dihitung ulang.</div>${containers.length?`<div class="actions">${containers.slice(0,20).map(c=>`<button class="mini primary" data-unified-guide="${esc(c)}">Guide ${esc(c)}</button>`).join("")}</div>`:'<div class="goodtext"><b>Tidak ada PR supervisor pada invoice ini.</b> Jika header sudah SIAP APPROVAL, gunakan tombol Approve pada No. Tagihan di bawah.</div>'}</div>`;
    document.querySelectorAll('[data-unified-guide]').forEach(b=>b.onclick=()=>renderReviewCenter(b.dataset.unifiedGuide));
    if(containers.length===1)await renderReviewCenter(containers[0]);
  }else if(invoiceNos.length>1){
    if(box)box.innerHTML=`<div class="card"><h3>Hasil pencarian</h3><div class="note">Ditemukan ${invoiceNos.length} No. Tagihan. Pilih invoice pada daftar di bawah agar review tidak tercampur.</div></div>`;
  }else if(invoiceNos.length===1){
    let no=invoiceNos[0];$("reviewCenterSearch").value=no;$("invoiceSearch").value=no;await openUnifiedInvoiceReview(no);
  }
  if(window.__linkAudit)renderAuditSnapshot();
}

async function renderReviewCenter(selected=""){
  let q=norm($("reviewCenterSearch")?.value||""),filter=$("reviewCenterFilter")?.value||"",allRows=await activeInvoiceRows(),rows=allRows.filter(isWorkflowReviewRow),groups={},reviewRows=[];for(let x of rows){let open=effectiveDetailProblems(x).length&&!x.resolved;if(!open&&x.claimDecision!=="TIDAK_BERHAK_DITAGIH")continue;reviewRows.push(x);(groups[x.container]??=[]).push(x)}
  let legacyAuditRows=allRows.filter(x=>x.legacy&&!hasPersistentUserWork(x)&&effectiveDetailProblems(x).length),legacyAuditInvoiceCount=new Set(legacyAuditRows.map(x=>norm(x.invoiceNo)).filter(Boolean)).size;
  let reviewInvoiceCount=new Set(reviewRows.map(x=>norm(x.invoiceNo)).filter(Boolean)).size,reviewDetailCount=reviewRows.length,reviewContainerCount=new Set(reviewRows.map(x=>norm(x.container)).filter(Boolean)).size;
  if($("reviewCenterCount"))$("reviewCenterCount").innerHTML=`<b>${reviewInvoiceCount} No Tagihan bermasalah aktif</b> • ${reviewDetailCount} detail perlu review • ${reviewContainerCount} container • <span class="muted">Audit data lama read-only: ${legacyAuditInvoiceCount} No Tagihan (tidak masuk PR aktif)</span>`;
  let list=Object.entries(groups).map(([container,a])=>({container,count:a.length,noEnt:a.filter(x=>String(x.claimStatus||"").startsWith("NO_ENTITLEMENT")).length,confirmed:a.filter(x=>x.claimDecision==="TIDAK_BERHAK_DITAGIH").length,paid:a.filter(x=>x.paymentStatus==="SUDAH DIBAYAR").length}));if(q)list=list.filter(x=>x.container.includes(q)||groups[x.container].some(i=>String(i.invoiceNo||"").toUpperCase().includes(q)));if(filter==="TIDAK BERHAK DITAGIH")list=list.filter(x=>x.confirmed);if(filter==="PERLU TINDAKAN")list=list.filter(x=>x.count);list.sort((a,b)=>b.paid-a.paid||b.noEnt-a.noEnt||a.container.localeCompare(b.container));
  if($("reviewCenterList")){
    $("reviewCenterList").innerHTML=list.length?list.slice(0,25).map(x=>`<div class="invoice-summary"><div><b>${esc(x.container)}</b><div class="small muted">${x.count} PR • ${x.noEnt} tanpa hak tagih • ${x.confirmed} dikonfirmasi user</div></div><div>${x.paid?badge("PAID TERKAIT"):badge("PERLU TINDAKAN")}</div><div><button class="primary mini" data-review-container="${esc(x.container)}">Buka Guide</button></div></div>`).join(""):'<div class="muted">Tidak ada PR sesuai filter.</div>';
    document.querySelectorAll("[data-review-container]").forEach(b=>b.onclick=()=>renderReviewCenter(b.dataset.reviewContainer));
  }
  if(!selected){if($("reviewCenterDetail"))$("reviewCenterDetail").innerHTML="";return}let a=await reviewContainer(selected),view=a.root==="PAYMENT"?"payments":a.root==="PRANOTA"?"pranotas":a.root==="APPROVAL"||a.root==="TAGIHAN VENDOR"?"invoices":a.root.startsWith("RENTAL")?"rentals":a.root==="MASTER"?"master":"expected";
  $("reviewCenterDetail").innerHTML=`<div class="card"><h3>${esc(a.container)} — Guide Supervisor</h3><div class="grid4"><div class="card kpi"><div class="label">Cycle</div><div class="value">${a.cycles.length}</div></div><div class="card kpi"><div class="label">Expected</div><div class="value">${a.exp.length}</div></div><div class="card kpi"><div class="label">PR Tagihan</div><div class="value">${a.pending.length}</div></div><div class="card kpi"><div class="label">Tanpa Hak</div><div class="value">${a.noEnt.length}</div></div></div><div class="note"><b>Kesimpulan Sistem:</b> ${esc(a.conclusion)}<br><br><b>Saran:</b> ${esc(a.suggestion)}</div><div class="note warn"><b>STEP AKTIF: ${esc(a.root)}</b><div class="actions"><button class="mini" id="reviewGuideOpen">Buka ${esc(a.root)}</button><button class="primary" id="reviewGuideRecheck">Review Ulang Step</button></div></div></div><div class="card" style="margin-top:12px"><h3>Vendor Claim & Keputusan User</h3>${a.noEnt.map(x=>`<div class="rejectitem"><b>${esc(x.invoiceNo)} • ${esc(x.container)} • P Vendor ${esc(x.vendorPeriod||x.period)}</b><div>${money(x.amount)} • Cycle ${esc(invoiceCycleId(x)||"BELUM DIPILIH")}</div><div class="badtext">${esc((x.claimFlags||[]).join(" • ")||"Tidak ada Expected/hak tagih")}</div><div>${x.claimDecision==="TIDAK_BERHAK_DITAGIH"?badge("TIDAK BERHAK DITAGIH"):`<button class="mini danger" data-center-noent="${esc(x.id)}">Konfirmasi Tidak Berhak</button>`}</div></div>`).join("")||'<div class="muted">Tidak ada vendor claim tanpa hak tagih.</div>'}</div><div class="card" style="margin-top:12px"><h3>Before → Simulasi After Periode</h3><div class="small muted">Simulasi virtual; tidak membuat Expected palsu.</div>${a.changes.slice(0,100).map(x=>`<div class="rejectitem"><b>${esc(x.k)} • ${esc(x.status)}</b><div>Sebelum: ${x.b?fmtDate(x.b.startDate)+"–"+fmtDate(x.b.endDate)+" • "+money(x.b.amount):"—"}</div><div>Sesudah: ${x.a?fmtDate(x.a.startDate)+"–"+fmtDate(x.a.endDate)+" • "+money(x.a.amount):"—"}</div></div>`).join("")||'<div class="muted">Tidak ada perubahan periode.</div>'}</div>`;
  $("reviewGuideOpen").onclick=()=>{sessionStorage.setItem("reviewReturnContainer",a.container);show(view);if(view==="invoices"){$("invoiceSearch").value=a.container;renderInv()}if(view==="rentals"){$("rentalSearch").value=a.container;renderRent()}};$("reviewGuideRecheck").onclick=async()=>{let z=await targetedReReview(a.container);await renderReviewCenter(a.container);alert(`Review ulang container selesai. ${z.changed} dari ${z.count} detail berubah.`)};document.querySelectorAll("[data-center-noent]").forEach(b=>b.onclick=async()=>{await setVendorClaimDecision(b.dataset.centerNoent,"TIDAK_BERHAK_DITAGIH");await renderReviewCenter(a.container)});
}

async function renderDash(){
  let [inv,pay,prs,hs,cycles,exp,credits]=await Promise.all([all("invoices"),all("payments"),all("pranotas"),invoiceHeaders(),legacyCycles(),combinedExpected(),vendorCredits()]);
  let workflowHs=hs.filter(h=>(h.billableItems||h.items||[]).some(isWorkflowReviewRow)),legacyAuditHs=hs.filter(h=>h.status==="PENDING"&&(h.billableItems||h.items||[]).some(x=>x.legacy&&!hasPersistentUserWork(x))&&!(h.billableItems||h.items||[]).some(isWorkflowReviewRow));
  let active=cycles.filter(x=>x.status==="AKTIF"||x.status==="PERLU KONFIRMASI").length,pending=workflowHs.filter(h=>h.status==="PENDING"),ready=workflowHs.filter(h=>h.status==="READY TO PAY"&&h.approvalStatus!=="APPROVED"),approved=workflowHs.filter(h=>h.status==="READY TO PAY"&&h.approvalStatus==="APPROVED"&&!h.pranotaNos.length),paid=hs.filter(h=>h.status==="SUDAH DIBAYAR"),prUnpaid=prs.filter(p=>p.status!=="DIBAYAR"),creditBal=credits.reduce((a,c)=>a+Number(c.balance||0),0);
  let linked=new Set(inv.map(x=>x.expectedId).filter(Boolean)),outExp=exp.filter(e=>!linked.has(e.id));
  $("kActive").textContent=active;$("kPayable").textContent=ready.length;$("kPending").textContent=pending.length;$("kPaid").textContent=paid.length;
  if($("dbStatus"))$("dbStatus").textContent=`${APP_VERSION} • Database terbuka • ${hs.length} No Tagihan • ${inv.filter(x=>!isSupersededDetail(x)).length} detail aktif`;
  $("dashSummary").innerHTML=`Expected belum ditagih: <b>${outExp.length}</b> (${money(outExp.reduce((a,e)=>a+Number(e.amount||0),0))}) • Siap Approval: <b>${ready.length}</b> • Approved belum Pranota: <b>${approved.length}</b> • Pranota belum dibayar: <b>${prUnpaid.length}</b> (${money(prUnpaid.reduce((a,p)=>a+Number(p.nett||0),0))}) • Saldo Kredit Vendor: <b>${money(creditBal)}</b> • Payment aktif: <b>${pay.filter(p=>p.status!=="DIBATALKAN").length}</b> • Audit data lama read-only: <b>${legacyAuditHs.length}</b> No Tagihan (tidak masuk PR aktif).`;
}
async function reportData(){
  let type=$("reportType")?.value||"OUT_EXPECTED",q=norm($("reportSearch")?.value||""),rows=[],head=[];
  if(type==="OUT_EXPECTED"){let [inv,exp]=await Promise.all([all("invoices"),combinedExpected()]),linked=new Set(inv.filter(x=>!isSupersededDetail(x)).map(x=>x.expectedId).filter(Boolean));head=["CONTAINER","CYCLE","PERIODE","AWAL","AKHIR","EXPECTED"];rows=exp.filter(e=>!linked.has(e.id)).map(e=>[e.container||"",e.rentalId||"",e.period||"",fmtDate(e.startDate),fmtDate(e.endDate),Number(e.amount||0)])}
  else if(type==="PENDING"){let hs=await invoiceHeaders();head=["VENDOR","NO TAGIHAN","CONTAINER","P VENDOR","NOMINAL VENDOR","EXPECTED","KOREKSI","MASALAH"];for(let h of hs.filter(h=>h.status==="PENDING"&&(h.billableItems||h.items||[]).some(isWorkflowReviewRow)))for(let x of (h.billableItems||h.items).filter(isWorkflowReviewRow))if(effectiveDetailProblems(x).length&&!x.resolved)rows.push([h.vendor,h.invoiceNo,x.container,x.vendorPeriod||x.period||"",Number(x.amount||0),Number(x.systemExpectedAmount||0),Number(x.adjustment||0),effectiveDetailProblems(x).join(" / ")])}
  else if(type==="ACTIVE"){let cycles=await legacyCycles();head=["CONTAINER","AMBIL","KEMBALI","VENDOR","TARIF","BASIS","STATUS"];rows=cycles.filter(c=>c.status==="AKTIF"||c.status==="PERLU KONFIRMASI").map(c=>[c.container,fmtDate(c.startDate),fmtDate(c.returnDate)||"AKTIF",c.vendor||"",Number(c.rate||c.monthlyRate||c.dailyRate||0),c.rateType||c.method||"",c.status])}
  else if(type==="CREDIT"){let credits=await vendorCredits();head=["VENDOR","SUMBER TAGIHAN","CONTAINER","KREDIT AWAL","SALDO","STATUS","KETERANGAN"];rows=credits.map(c=>[c.vendor,c.sourceInvoiceNo,c.container,Number(c.amount||0),Number(c.balance||0),c.status,c.note||""])}
  else if(type==="APPROVAL"){let hs=await invoiceHeaders();head=["VENDOR","NO TAGIHAN","STATUS","APPROVAL","PRANOTA","DPP","PPN","PPH","NETT"];rows=hs.filter(h=>h.status!=="SUDAH DIBAYAR").map(h=>[h.vendor,h.invoiceNo,h.status,h.approvalStatus,h.pranotaNos.join(", "),Number(h.dpp||0),Number(h.ppn||0),Number(h.pph||0),Number(h.nett||0)])}
  else {let pays=await all("payments");head=["TGL BAYAR","BUKTI","PRANOTA","NO TAGIHAN","NETT SISTEM","TOTAL BAYAR","SELISIH","STATUS","KETERANGAN"];rows=pays.map(p=>[fmtDate(p.date),p.reference||"",(p.pranotaNos||[]).join(", "),(p.invoiceNos||[]).join(", "),Number(p.amount||0),Number((p.actualAmount??p.amount)||0),Number(p.difference??(Number((p.actualAmount??p.amount)||0)-Number(p.amount||0))),p.status||"AKTIF",p.note||""])}
  if(q)rows=rows.filter(r=>r.some(v=>norm(v).includes(q)));return{type,head,rows};
}
async function renderReports(){let d=await reportData();$("reportHead").innerHTML=`<tr>${d.head.map(x=>`<th>${esc(x)}</th>`).join("")}</tr>`;$("reportRows").innerHTML=d.rows.length?d.rows.slice(0,1000).map(r=>`<tr>${r.map(v=>`<td class="wrap">${typeof v==="number"?money(v):esc(v)}</td>`).join("")}</tr>`).join(""):'<tr><td class="muted">Tidak ada data.</td></tr>';$("reportSummary").innerHTML=`${d.rows.length} baris • tampilan maksimal 1.000 baris; export memuat seluruh hasil filter.`;}
async function exportCurrentReport(){let d=await reportData();pipeCsv(`laporan_${d.type.toLowerCase()}.csv`,d.head,d.rows)}
async function renderInfo(){
  let c={};for(let s of S)c[s]=(await all(s)).length;
  let exp=await combinedExpected(),legacyCyclesCount=(await legacyCycles()).length;
  $("backupInfo").innerHTML=Object.entries(c).map(([k,v])=>`${k}: <b>${v}</b>`).join(" • ")+` • siklus rental terbaca: <b>${legacyCyclesCount}</b> • expected: <b>${exp.length}</b>`;
  $("dbStatus").textContent=`${APP_VERSION} • ${c.invoices} detail tagihan`;
}

async function segarkanDaftarMaster(){
  let ms=await all("masters"),dk=$("daftarKontainer"),dv=$("daftarVendor");
  if(dk){
    let cs=[...new Set(ms.map(x=>x.container).filter(Boolean))].sort();
    dk.innerHTML=cs.map(c=>{let vs=ms.filter(x=>x.container===c),m=vs.find(x=>x.isActive!==false&&!x.validTo)||vs[0]||{};
      return `<option value="${esc(c)}">${esc([m.size,m.type,m.vendor].filter(Boolean).join(" • "))}</option>`}).join("");
  }
  if(dv){
    let vm=(await vendorMasterRows()).filter(v=>v.isActive!==false).sort((a,b)=>String(a.code).localeCompare(String(b.code)));
    dv.innerHTML=vm.map(v=>`<option value="${esc(v.code)}">${esc(v.name||v.code)}</option>`).join("");
  }
}
function pasangTanggalFleksibel(){
  for(let id of ["mValidFrom","mValidTo","eInvoiceDate","newPranotaDate","pDate","peDate"]){
    let el=$(id);if(!el||el.dataset.tanggalFleksibel)return;
    el.dataset.tanggalFleksibel="1";
    el.addEventListener("blur",()=>{if(!el.value.trim())return;let d=parseDate(el.value);if(d)el.value=fmtDate(d)});
  }
}

async function invoiceOnlyResetPreview(){
  let keepIds=["masters","rates","rentals","expected","rentalOverrides"],dropIds=["invoices","detachedInvoices","pranotas","payments"],keep={},drop={};
  for(let x of keepIds)keep[x]=(await all(x)).length;
  for(let x of dropIds)drop[x]=(await all(x)).length;
  let inv=await all("invoices");
  let noTagihan=new Set(inv.map(x=>String(x.invoiceNo||"").trim()).filter(Boolean)).size;
  let warnings=inv.filter(x=>x.warningQueueOpen===true||x.identityWarning===true||x.decision==="PENDING").length;
  if($("invoiceOnlyResetPreview"))$("invoiceOnlyResetPreview").innerHTML=`<span class="badge ok">SIMULASI SELESAI</span> HAPUS: detail tagihan <b>${drop.invoices}</b> • No. Tagihan <b>${noTagihan}</b> • warning/PR tagihan <b>${warnings}</b> • detached <b>${drop.detachedInvoices}</b> • pranota <b>${drop.pranotas}</b> • payment <b>${drop.payments}</b><br><span class="goodtext">TETAP: Master <b>${keep.masters}</b> • Tarif <b>${keep.rates}</b> • Rental/Siklus <b>${keep.rentals}</b> • Expected tersimpan <b>${keep.expected}</b> • Koreksi Rental <b>${keep.rentalOverrides}</b></span>`;
  return {keep,drop,noTagihan,warnings};
}
async function executeInvoiceOnlyReset(){
  let c=await invoiceOnlyResetPreview();
  if(c.drop.payments>0||c.drop.pranotas>0){
    if(!confirm(`PERINGATAN: database berisi ${c.drop.pranotas} Pranota dan ${c.drop.payments} Payment. Reset khusus Tagihan akan ikut membersihkan turunannya agar tidak menjadi data yatim.\n\nLanjut ke konfirmasi berikutnya?`))return;
  }
  if(!confirm(`RESET KHUSUS TAGIHAN akan menghapus ${c.drop.invoices} detail dari ${c.noTagihan} No. Tagihan beserta warning/PR dan relasi hasil matching Tagihan.\n\nMASTER, TARIF, TAKE/RETURN, RENTAL/SIKLUS dan EXPECTED TIDAK DIHAPUS.\n\nBuat Backup JSON terlebih dahulu. Lanjut?`))return;
  if(!confirm("KONFIRMASI TERAKHIR: seluruh Tagihan Vendor akan kosong dan siap diimpor ulang memakai format 8 kolom TGL SEWA DARI/SAMPAI yang dikenali otomatis. Data operasional tetap dipertahankan."))return;
  for(let x of ["invoices","detachedInvoices","pranotas","payments"])await clear(x);
  // Hapus hanya audit yang berasal dari lapisan tagihan; audit operasional/master tetap dipertahankan.
  let logs=await all("oplog"),tagihanKinds=/TAGIHAN|INVOICE|WARNING|AMBIGU|APPROVAL|PRANOTA|PEMBAYARAN|PAYMENT/i;
  for(let l of logs){if(tagihanKinds.test(String(l.type||l.kind||l.action||"")))await del("oplog",l.id)}
  try{localStorage.removeItem("cbc_link_audit_snapshot_v1")}catch(e){} window.__linkAudit=null;
  await put("oplog",auditLog("RESET KHUSUS TAGIHAN","SALDO AWAL",today(),"DITERIMA",`${c.drop.invoices} detail tagihan dibersihkan. Master/Tarif/TAKE/RETURN/Rental/Siklus/Expected dipertahankan. Siap impor ulang Tagihan; format 8 kolom dengan referensi tanggal sewa dikenali otomatis.`,"V7.1.0"));
  invalidateCaches();invoicePage=1;reviewPage=1;await refresh();await invoiceOnlyResetPreview();
  alert("Reset khusus Tagihan selesai. Master, Tarif, TAKE/RETURN, Rental/Siklus dan Expected tetap ada. Sekarang impor ulang seluruh Tagihan; format 8 kolom referensi siklus dikenali otomatis.");
}
async function cleanResetPreview(){
  let ids=["masters","rates","rentals","expected","invoices","detachedInvoices","payments","pranotas","oplog","rentalOverrides"],c={};
  for(let x of ids)c[x]=(await all(x)).length;
  $("cleanResetPreview").innerHTML=`<span class="badge ok">SIMULASI SELESAI</span> `+Object.entries(c).map(([k,v])=>`${esc(k)}: <b>${v}</b>`).join(" • ");
  let b=$("cleanResetPreviewBtn");if(b){let old=b.textContent;b.textContent="✓ Simulasi Selesai";setTimeout(()=>b.textContent=old,1800)}
  return c
}
async function renderHistoricalImportMode(){
  let el=$("historyImportStatus");if(!el)return;
  let mode=(await getSetting("cleanRebuildMode"))===true,r=await legacyCycles();
  let pending=r.filter(x=>x.historyPending&&!x.returnDate&&x.inferredEnd);
  el.innerHTML=mode?`<span class="badge warn">MODE IMPOR HISTORI AKTIF</span> TAKE/RETURN boleh masuk tidak berurutan atau beda waktu. ${pending.length?`<b>${pending.length}</b> cycle lama masih menunggu RETURN aktual.`:"Tidak ada cycle lama yang menunggu RETURN."}`:`<span class="badge good">OPERASIONAL TERKUNCI</span> Aturan normal aktif: tidak boleh TAKE baru saat cycle sebelumnya belum selesai.`;
  let b=$("lockHistoricalImport");if(b)b.style.display=mode?"inline-block":"none";
}
async function lockHistoricalImport(){
  let r=await legacyCycles(),pending=r.filter(x=>x.historyPending&&!x.returnDate&&x.inferredEnd);
  let conflicts=[];
  let by={};for(let x of r)(by[x.container]??=[]).push(x);
  for(let arr of Object.values(by)){arr.sort((a,b)=>a.startDate.localeCompare(b.startDate));for(let i=0;i<arr.length-1;i++){let a=arr[i],b=arr[i+1];if(a.returnDate&&a.returnDate>=b.startDate)conflicts.push(`${a.container}: ${fmtDate(a.startDate)}–${fmtDate(a.returnDate)} overlap TAKE ${fmtDate(b.startDate)}`)}}
  if(pending.length||conflicts.length){alert(`BELUM BISA DIKUNCI.\n\n${pending.length} cycle histori masih menunggu RETURN aktual.\n${conflicts.length} overlap aktual ditemukan.\n\nImpor/perbaiki RETURN sampai cycle lama selesai terlebih dahulu.`);return}
  if(!confirm("Selesaikan fase impor histori dan KUNCI operasional? Setelah dikunci, TAKE baru kembali memakai validasi ketat dan back-date yang bertentangan harus melalui Review & Koreksi."))return;
  await setSetting("cleanRebuildMode",false);await put("oplog",auditLog("MODE HISTORI","KUNCI OPERASIONAL",today(),"DITERIMA","Impor histori selesai; validasi TAKE normal dikunci kembali","V7.0.8"));await renderHistoricalImportMode();alert("Operasional sudah dikunci kembali.");
}
async function executeCleanReset(){let c=await cleanResetPreview();if(!confirm("RESET DATA CLEAN REBUILD akan menghapus Master, Tarif, Operasional/Siklus, Expected, Tagihan, Pranota, Payment, dan Audit. ATURAN/CODING TIDAK DIHAPUS.\n\nPastikan Backup JSON sudah dibuat. Lanjut?"))return;if(!confirm("KONFIRMASI TERAKHIR: data transaksi akan kosong dan harus diimpor ulang dari Master → Tarif → TAKE → RETURN → Tagihan → Approval/Pranota → Paid."))return;for(let x of ["masters","rates","rentals","expected","invoices","detachedInvoices","payments","pranotas","oplog","rentalOverrides"])await clear(x);await setSetting("cleanRebuildMode",true);await setSetting("lastExpectedCalculation","");try{localStorage.removeItem("cbc_link_audit_snapshot_v1")}catch(e){}window.__linkAudit=null;await put("oplog",auditLog("CLEAN REBUILD","RESET",today(),"DITERIMA","Data dibersihkan; aturan aplikasi tetap. Siap impor ulang dari sumber.","V7.0"));invalidateCaches();await refresh();await cleanResetPreview();alert("Clean Reset selesai. Mulai dari Master Container dan Master Tarif Vendor.")}
async function importHistoricalPranota(){let rows=$("pranotaImportText").value.split(/\r?\n/).map((raw,i)=>({raw,line:i+1,c:split(raw).map(x=>x.trim())})).filter(x=>x.raw.trim()),rej=[],ok=0,inv=await all("invoices");for(let z of rows){let [no,dateRaw,vendor,invoiceNo,note]=z.c,date=parseDate(dateRaw),reason="",items=inv.filter(x=>norm(x.invoiceNo)===norm(invoiceNo)&&norm(x.vendor)===norm(vendor));if(!no||!date||!vendor||!invoiceNo)reason="NO PRANOTA / TGL / VENDOR / NO TAGIHAN WAJIB";else if(!items.length)reason="TAGIHAN VENDOR TIDAK DITEMUKAN";else if(items.some(x=>x.decision==="PENDING"))reason="TAGIHAN MASIH PENDING";else if(items.some(x=>x.pranotaNo&&x.pranotaNo!==no))reason="TAGIHAN SUDAH TERIKAT PRANOTA LAIN";if(reason){rej.push({line:z.line,raw:z.raw,reason});continue}for(let x of items){x.approvalStatus="APPROVED";x.approvalSource="HISTORICAL_IMPORT";x.pranotaNo=no;await put("invoices",x)}let pr=(await all("pranotas")).find(x=>x.pranotaNo===no)||{id:uid("PRN"),pranotaNo:no,date,vendor,invoiceNos:[],status:"AKTIF",source:"HISTORICAL_IMPORT",createdAt:now()};pr.invoiceNos=[...new Set([...(pr.invoiceNos||[]),invoiceNo])];pr.note=note||pr.note||"";await put("pranotas",pr);ok++}$("pranotaImportText").value=rej.map(x=>x.raw).join("\n");rejectBox("pranotaImportRejects",rej);$("pranotaImportSummary").innerHTML=`Diterima: <b>${ok}</b> • Reject: <b>${rej.length}</b>`;await refresh()}
async function importHistoricalPayments(){let rows=$("paymentImportText").value.split(/\r?\n/).map((raw,i)=>({raw,line:i+1,c:split(raw).map(x=>x.trim())})).filter(x=>x.raw.trim()),rej=[],ok=0;for(let z of rows){let [dateRaw,ref,pranotaRaw,amountRaw,note]=z.c,date=parseDate(dateRaw),amount=amt(amountRaw),nos=parsePranotaSelection(pranotaRaw),reason="",summary=nos.length?await getPranotaSummary(nos):null;if(!date||!ref||!nos.length||amount==null)reason="TGL BAYAR / BUKTI / PRANOTA / NOMINAL WAJIB";else if(summary.missing.length)reason="PRANOTA TIDAK DITEMUKAN: "+summary.missing.join(", ");else if(summary.alreadyPaid.length)reason="ADA TAGIHAN SUDAH DIBAYAR";else if(Math.abs(Number(amount)-Number(summary.nett))>1)reason=`NOMINAL PAID ${money(amount)} != NETT SISTEM ${money(summary.nett)}`;if(reason){rej.push({line:z.line,raw:z.raw,reason});continue}let paymentId=uid("PAY"),p={id:paymentId,pranotaNos:nos,invoiceNos:summary.invoiceNos,date,amount:Number(amount),actualAmount:Number(amount),difference:0,reference:ref,note:note||"",source:"HISTORICAL_IMPORT",status:"AKTIF",createdAt:now()};await put("payments",p);for(let x of summary.selected){x.paymentStatus="SUDAH DIBAYAR";x.paymentNo=ref;x.paymentDate=date;x.paymentId=paymentId;x.decision="SUDAH DIBAYAR";await put("invoices",x)}for(let pr of (await all("pranotas")).filter(x=>nos.includes(x.pranotaNo))){pr.status="DIBAYAR";pr.paymentId=paymentId;pr.paymentNo=ref;pr.paymentDate=date;await put("pranotas",pr)}ok++}$("paymentImportText").value=rej.map(x=>x.raw).join("\n");rejectBox("paymentImportRejects",rej);$("paymentImportSummary").innerHTML=`Diterima: <b>${ok}</b> • Reject: <b>${rej.length}</b>`;await rebuild(false);await refresh()}
// V7.0.1: wiring eksplisit + delegated fallback untuk tombol baru V7.
// Delegated handler dipakai agar tombol tetap bekerja walaupun section dirender/berpindah DOM.
if($("processRates"))$("processRates").onclick=processRates;
if($("clearRates"))$("clearRates").onclick=()=>{$("rateText").value=""};
if($("invoiceOnlyResetPreviewBtn"))$("invoiceOnlyResetPreviewBtn").onclick=invoiceOnlyResetPreview;
if($("executeInvoiceOnlyReset"))$("executeInvoiceOnlyReset").onclick=executeInvoiceOnlyReset;
if($("cleanResetPreviewBtn"))$("cleanResetPreviewBtn").onclick=cleanResetPreview;
if($("executeCleanReset"))$("executeCleanReset").onclick=executeCleanReset;
if($("processPranotaImport"))$("processPranotaImport").onclick=importHistoricalPranota;
if($("processPaymentImport"))$("processPaymentImport").onclick=importHistoricalPayments;
const V7_DELEGATED={
  processRates:()=>processRates(),
  clearRates:()=>{$("rateText").value="";$('rateText').focus()},
  cleanResetPreviewBtn:()=>cleanResetPreview(),
  executeCleanReset:()=>executeCleanReset(),
  processPranotaImport:()=>importHistoricalPranota(),
  processPaymentImport:()=>importHistoricalPayments()
};
document.addEventListener("click",e=>{
  let b=e.target.closest("button");if(!b||!V7_DELEGATED[b.id])return;
  e.preventDefault();e.stopImmediatePropagation();
  Promise.resolve(V7_DELEGATED[b.id]()).catch(err=>{console.error("V7 button error",b.id,err);alert(`Tombol ${b.textContent.trim()} gagal: ${err.message}`)});
},true);
async function refresh(){
  await segarkanDaftarMaster();pasangTanggalFleksibel();
  let v=visibleView();
  if(v==="dashboard")await renderDash();
  else if(v==="master"){await renderMaster();await renderRates();}
  else if(v==="rentals")await renderRent();
  else if(v==="expected")await renderExp();
  else if(v==="invoices")await renderInv();
  else if(v==="payments")await renderPay();
  else if(v==="pranotas")await renderPranota();
  else if(v==="invoiceentry")await renderEntryExisting();
  else if(v==="invoiceimport")await renderWarningQueue();
  else if(v==="reviewcenter")await openReviewCenterHome();
  else if(v==="audit")await renderAudit();
  else if(v==="reports")await renderReports();
  else if(v==="backup")await renderInfo();
}

if($("reportType"))$("reportType").onchange=renderReports;if($("reportSearch"))$("reportSearch").oninput=()=>debounce(renderReports);if($("exportReport"))$("exportReport").onclick=exportCurrentReport;
if($("reviewCenterSearch"))$("reviewCenterSearch").oninput=()=>debounce(async()=>{if(window.__linkAudit)renderAuditSnapshot()});
if($("reviewCenterFilter"))$("reviewCenterFilter").onchange=async()=>{if(window.__linkAudit)renderAuditSnapshot();};
if($("reviewCenterRun"))$("reviewCenterRun").onclick=async()=>{let q=norm($("reviewCenterSearch").value);let rows=await activeInvoiceRows(),exact=rows.filter(x=>norm(x.invoiceNo)===q);if(exact.length)await openScopedReview(q);else{sessionStorage.removeItem("reviewScopeInvoice");sessionStorage.removeItem("reviewScopeContainer");await renderReviewCenter(q);if(window.__linkAudit)renderAuditSnapshot()}};
if($("reviewClearScope"))$("reviewClearScope").onclick=async()=>{sessionStorage.removeItem("reviewScopeInvoice");sessionStorage.removeItem("reviewScopeContainer");$("reviewCenterSearch").value="";let b=$("reviewScopeBanner");if(b)b.style.display="none";if(window.__linkAudit){$("globalLinkAuditResult").style.display="block";renderAuditSnapshot()}await renderReviewCenter()};
if($("globalLinkAuditBtn"))$("globalLinkAuditBtn").onclick=renderGlobalLinkageAudit;
$("rentalSearch").oninput=()=>debounce(()=>{rentalPage=1;renderRent()});$("rentalStatus").onchange=()=>{rentalPage=1;renderRent()};
$("rentalPageSize").onchange=()=>{rentalPage=1;renderRent()};$("rentalPrev").onclick=()=>{if(rentalPage>1){rentalPage--;renderRent()}};$("rentalNext").onclick=()=>{rentalPage++;renderRent()};
$("expectedFilter").onchange=()=>{expectedPage=1;renderExp()};
$("expectedSearch").oninput=()=>debounce(()=>{expectedPage=1;renderExp()});
$("expectedPageSize").onchange=()=>{expectedPage=1;renderExp()};
$("expectedPrev").onclick=()=>{if(expectedPage>1){expectedPage--;renderExp()}};
$("expectedNext").onclick=()=>{expectedPage++;renderExp()};
$("invoiceFilter").onchange=()=>{reviewPage=1;invoicePage=1;renderInv()};
$("invoiceSearch").oninput=()=>debounce(()=>{reviewPage=1;invoicePage=1;renderInv()});
$("invoicePageSize").onchange=()=>{reviewPage=1;invoicePage=1;renderInv()};
$("reviewPrev").onclick=()=>{if(reviewPage>1){reviewPage--;renderInv()}};
$("reviewNext").onclick=()=>{reviewPage++;renderInv()};
$("invoicePrev").onclick=()=>{if(invoicePage>1){invoicePage--;renderInv()}};
$("invoiceNext").onclick=()=>{invoicePage++;renderInv()};if($("paymentSearch"))$("paymentSearch").oninput=()=>debounce(renderPay);if($("paymentEditSave"))$("paymentEditSave").onclick=savePaymentEdit;if($("paymentEditCancel"))$("paymentEditCancel").onclick=()=>$("paymentEditDialog").close();if($("pranotaVendor"))bindVendorAutocomplete("pranotaVendor","pranotaVendorSuggest",async()=>{await renderPranota()});if($("pranotaSearch"))$("pranotaSearch").oninput=()=>debounce(renderPranota);if($("pranotaVendor"))$("pranotaVendor").onchange=()=>debounce(renderPranota);$("auditType").onchange=renderAudit;$("auditStatus").onchange=renderAudit;

try{let z=localStorage.getItem("cbc_link_audit_snapshot_v1");if(z)window.__linkAudit=JSON.parse(z)}catch(e){}

if($("creditSave"))$("creditSave").onclick=saveVendorCredit;if($("creditCancel"))$("creditCancel").onclick=()=>{$("creditDialog").close();pendingVendorCredit=null};
openDB().then(async()=>{
  if(!supervisorPeriodSelfTest())throw new Error("Period engine self-test gagal. Database tidak diubah.");
  await seed();
  let cleanMode=(await getSetting("cleanRebuildMode"))===true;
  if(!cleanMode)await migrateMasterVersions();
  let refMigration=cleanMode?{skipped:true,created:0,updated:0,mapped:0,conflicts:0,unmatched:0}:await reconcileSiklusReference();
  if(!refMigration.skipped)await put("oplog",auditLog("REFERENSI SIKLUS","Siklus.xlsx",today(),refMigration.conflicts?"TERCATAT":"DITERIMA",`${refMigration.created} cycle dibuat • ${refMigration.updated} cycle diperbarui • ${refMigration.mapped} detail finansial dipetakan • ${refMigration.conflicts} konflik dilindungi • ${refMigration.unmatched} detail tetap fallback`,"Container + TGL SEWA = kunci cycle; Tgl Kembali = reference closing"));
  let cycleMigration=cleanMode?{created:0,mapped:0}:await normalizeTakeBasedCycles();
  if(cycleMigration.created||cycleMigration.mapped)await put("oplog",auditLog("MIGRASI SIKLUS","TAKE_BASED_FALLBACK",today(),"DITERIMA",`${cycleMigration.created} siklus fallback dibuat • ${cycleMigration.mapped} detail fallback dipetakan`,"Dipakai hanya bila Siklus.xlsx tidak memiliki referensi yang cocok"));

  // One-time migration formula prorata V7.1.23. V7.1.21 mengubah divisor tetapi Expected lama
  // masih tersimpan di IndexedDB. Migrasi ini menghitung ulang hanya jalur UNPAID; PAID dikunci di rebuild().
  let prorataFormulaVersion=await getSetting("prorataFormulaVersion");
  if(prorataFormulaVersion!=="PRORATA_30_FEB_ACTUAL_V1"){
    if((await all("rentals")).length)await rebuild(false);
    let mig=await reReviewUnpaidInvoices();
    await setSetting("prorataFormulaVersion","PRORATA_30_FEB_ACTUAL_V1");
    await put("oplog",auditLog("MIGRASI PRORATA","PRORATA_30_FEB_ACTUAL_V1",today(),"DITERIMA",`${mig.changed} detail unpaid direview ulang • ${mig.cleared} menjadi bersih • ${mig.remaining} masih perlu review`,`Mar-Jan /30 • Feb 28/29 aktual • PAID tidak diubah`));
    invalidateCaches();
  }

  let financeModel=await getSetting("financialModelVersion");
  if(financeModel!=="EXPECTED_PLUS_MANUAL_V1"){
    if(!cleanMode)await rebuild(false);
    await reReviewUnpaidInvoices();
    await setSetting("financialModelVersion","EXPECTED_PLUS_MANUAL_V1");
    await put("oplog",auditLog("MIGRASI FINANSIAL","EXPECTED_PLUS_MANUAL_V1",today(),"DITERIMA","Prorata dipisahkan dari Koreksi Manual; Disetujui = Expected Sistem + Koreksi Manual","Tidak mengubah data PAID"));
    invalidateCaches();
  }
  let closingSync=await syncClosingCorrectionsToCycles();
  if(closingSync.synced||closingSync.financialFixed){await rebuild(false)}
  await applyInvoiceGroupStatus();
  if((await getSetting("stateHistoryGuardVersion"))!=="STATE_HISTORY_GUARD_V1"){
    let ar=await activeInvoiceRows(),legacyBacklog=ar.filter(x=>x.legacy&&!hasPersistentUserWork(x)&&effectiveDetailProblems(x).length),activePr=ar.filter(x=>isWorkflowReviewRow(x)&&effectiveDetailProblems(x).length&&!x.resolved);
    await setSetting("stateHistoryGuardVersion","STATE_HISTORY_GUARD_V1");
    await put("oplog",auditLog("MIGRASI STATE HISTORY","STATE_HISTORY_GUARD_V1",today(),"DITERIMA",`${new Set(activePr.map(x=>norm(x.invoiceNo))).size} No Tagihan PR aktif • ${new Set(legacyBacklog.map(x=>norm(x.invoiceNo))).size} No Tagihan audit lama dipisahkan read-only`,`Tidak menghapus evidence, keputusan user, adjustment, approval, pranota, payment atau data legacy`));
    invalidateCaches();
  }
  let last=await getSetting("lastExpectedCalculation");
  if(!refMigration.skipped||cycleMigration.created||cycleMigration.mapped||last!==today()){if((await all("rentals")).length)await rebuild(false);await setSetting("lastExpectedCalculation",today())}
  const ex=window.APP_EXAMPLES||{};
  $("pDate").value=fmtDate(today());
  if($("takeText")&&!$("takeText").value.trim())$("takeText").value=ex.take||"DFSU2296934|12 Feb 26|B";
  if($("returnText")&&!$("returnText").value.trim())$("returnText").value=ex.return||"DFSU2296934|19 Jun 26";
  if($("invoiceText")&&!$("invoiceText").value.trim())$("invoiceText").value=ex.invoice||"ZONA|ZONA260131527|26 Jan 26|675676|DFSU2296934|3";
  if($("rateText")&&!$("rateText").value.trim())$("rateText").value="ZONA|20 FT|DRY|675676|30000|01 Jan 26|\nZONA|40 FT|DRY|1261261|50000|01 Jan 26|";
  if($("pranotaImportText")&&!$("pranotaImportText").value.trim())$("pranotaImportText").value="PRN-0001|31 Jan 26|ZONA|ZONA260131527|Pranota histori";
  if($("paymentImportText")&&!$("paymentImportText").value.trim())$("paymentImportText").value="05 Feb 26|BB-0001|PRN-0001|750000|Lunas";
  $("backupName").value=`backup_container_${today()}.json`;
  await renderDash();
  if(visibleView()==="backup")await renderInfo();
  if(visibleView()==="master"){await renderMaster();await renderRates();}
}).catch(e=>{console.error(e);$("dbStatus").textContent="Database gagal";alert("Database gagal: "+e.message)});
})();
