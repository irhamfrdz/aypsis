<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AYPSIS - KONTAINER SEWA FINAL (2026)</title>
    <!-- Use Google Fonts for better aesthetics -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #1e293b;
            --main-bg: #f8fafc;
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--main-bg); 
            margin: 0; 
            font-size: 13px; 
            color: var(--text-main);
            display: flex; 
            height: 100vh; 
            overflow: hidden; 
        }

        .sidebar { 
            width: 260px; 
            background: var(--sidebar-bg); 
            color: white; 
            padding: 20px; 
            display: flex; 
            flex-direction: column; 
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
            z-index: 100;
        }

        .sidebar h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-align: center;
            margin-bottom: 30px;
            letter-spacing: -0.025em;
        }

        .main { 
            flex: 1; 
            padding: 30px; 
            overflow-y: auto; 
            display: flex;
            flex-direction: column;
        }

        .card { 
            background: var(--card-bg); 
            padding: 24px; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); 
            margin-bottom: 24px; 
            border: 1px solid var(--border-color);
            transition: transform 0.2s;
        }

        h4 {
            margin-top: 0;
            margin-bottom: 15px;
            font-weight: 600;
            color: var(--text-main);
            font-size: 1.1rem;
        }

        .nav-btn { 
            width: 100%; 
            padding: 12px 16px; 
            border: none; 
            background: transparent; 
            color: #94a3b8; 
            text-align: left; 
            cursor: pointer; 
            font-weight: 500; 
            border-radius: 8px;
            margin-bottom: 4px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-btn:hover { 
            background: rgba(255, 255, 255, 0.05); 
            color: white;
        }

        .nav-btn.active { 
            background: var(--primary); 
            color: white; 
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
            background: white; 
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        th, td { 
            padding: 12px 16px; 
            text-align: left; 
            border-bottom: 1px solid var(--border-color);
        }

        th { 
            background: #f1f5f9; 
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            position: sticky; 
            top: 0; 
            z-index: 10; 
        }

        tr:last-child td {
            border-bottom: none;
        }

        .btn { 
            padding: 8px 16px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-weight: 600; 
            font-size: 12px; 
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn:active {
            transform: scale(0.98);
        }

        .btn-blue { background: var(--primary); color: white; }
        .btn-blue:hover { background: var(--primary-hover); }

        .btn-green { background: var(--success); color: white; }
        .btn-green:hover { opacity: 0.9; }

        .btn-orange { background: var(--warning); color: white; }
        .btn-orange:hover { opacity: 0.9; }

        .btn-red { background: var(--danger); color: white; }
        .btn-red:hover { opacity: 0.9; }

        input, select, textarea { 
            padding: 10px 14px; 
            border: 1px solid var(--border-color); 
            border-radius: 8px; 
            font-size: 13px; 
            outline: none;
            transition: border-color 0.2s;
        }

        input:focus, select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .pagination { 
            margin-top: 20px; 
            display: flex; 
            gap: 6px; 
            justify-content: center; 
        }

        .pg-btn { 
            padding: 8px 14px; 
            border: 1px solid var(--border-color); 
            background: white; 
            cursor: pointer; 
            border-radius: 6px;
            color: var(--text-main);
            font-weight: 500;
            transition: all 0.2s;
        }

        .pg-btn:hover {
            background: #f1f5f9;
        }

        .pg-btn.active { 
            background: var(--sidebar-bg); 
            color: white; 
            border-color: var(--sidebar-bg);
        }

        .non-aktif { background: #fdfdfd; color: #94a3b8; text-decoration: line-through; }
        .st-sewa { color: var(--danger); font-weight: 600; } 
        .st-selesai { color: var(--success); font-weight: 600; }

        .judul-audit { 
            color: var(--sidebar-bg); 
            font-weight: 800; 
            font-size: 1.25rem; 
            margin-bottom: 20px; 
            display: block; 
        }

        .tab-audit-container { 
            display: flex; 
            gap: 12px; 
            margin-bottom: 20px; 
        }

        .tab-audit-btn { 
            padding: 10px 20px; 
            border: 1px solid var(--primary); 
            background: white; 
            cursor: pointer; 
            border-radius: 8px; 
            font-weight: 600; 
            color: var(--primary); 
            transition: all 0.2s;
        }

        .tab-audit-btn.active { 
            background: var(--primary); 
            color: white; 
        }

        /* Responsive Improvements */
        @media (max-width: 768px) {
            body { flex-direction: column; height: auto; overflow: visible; }
            .sidebar { width: 100%; height: auto; position: sticky; top: 0; }
            .main { height: auto; overflow: visible; }
        }

        /* Custom scrollbar for sidebar */
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>MASTER BTM</h2>
    
    <div style="background: rgba(255,255,255,0.05); padding:15px; border-radius:12px; margin-bottom:20px;">
        <button class="btn btn-blue" style="width:100%; margin-bottom:10px; font-size: 11px;" onclick="document.getElementById('fIn').click()">📂 BUKA FILE DATA</button>
        <button class="btn btn-green" style="width:100%; font-size: 11px;" onclick="simpanData()">💾 SIMPAN DATA</button>
        <input type="file" id="fIn" style="display:none" onchange="loadFile(event)">
    </div>

    <nav style="flex: 1; overflow-y: auto;">
        <button class="nav-btn" onclick="showTab(event, 'tab-vtz')"><span>1.</span> Vendor / Tipe / Size</button>
        <button class="nav-btn" onclick="showTab(event, 'tab-tarif')"><span>2.</span> Master Tarif</button>
        <button class="nav-btn" onclick="showTab(event, 'tab-unit')"><span>3.</span> Master Kontainer</button>
        <button class="nav-btn" onclick="showTab(event, 'tab-trx')"><span>4.</span> Transaksi IN (OUT)</button>
        <button class="nav-btn active" onclick="showTab(event, 'tab-rekon')"><span>5.</span> Rekon & Audit</button>
        <button class="nav-btn" onclick="showTab(event, 'tab-pranota')"><span>6.</span> Daftar Pranota</button>
    </nav>
    
    <div style="margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.75rem; color: #64748b; text-align: center;">
        AYPSIS &copy; 2026<br>
        <a href="/" style="color: var(--primary); text-decoration: none;">Kembali ke Dashboard</a>
    </div>
</div>

<div class="main">
    <div id="tab-vtz" style="display:none">
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:20px;">
            <div class="card">
                <h4>Vendor</h4>
                <div style="display:flex; gap:8px;">
                    <input id="in-v" style="flex:1" placeholder="Nama Vendor...">
                    <button class="btn btn-blue" onclick="addM('v','in-v')">Add</button>
                </div>
                <table id="tbl-v">
                    <thead><tr><th>No</th><th>Vendor</th><th>Aksi</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="card">
                <h4>Tipe</h4>
                <div style="display:flex; gap:8px;">
                    <input id="in-t" style="flex:1" placeholder="Nama Tipe...">
                    <button class="btn btn-blue" onclick="addM('t','in-t')">Add</button>
                </div>
                <table id="tbl-t">
                    <thead><tr><th>No</th><th>Tipe</th><th>Aksi</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="card">
                <h4>Size</h4>
                <div style="display:flex; gap:8px;">
                    <input id="in-z" style="flex:1" placeholder="Nama Size...">
                    <button class="btn btn-blue" onclick="addM('z','in-z')">Add</button>
                </div>
                <table id="tbl-z">
                    <thead><tr><th>No</th><th>Size</th><th>Aksi</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="tab-tarif" style="display:none">
        <div class="card">
            <h4>➕ Setup Tarif</h4>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap:12px; margin-bottom:15px;">
                <select id="rt-v"></select>
                <select id="rt-t"></select>
                <select id="rt-z"></select> 
                <input id="rt-bln" type="number" placeholder="Per Bulan (Rp)">
                <input id="rt-hr" type="number" placeholder="Per Hari (Rp)">
            </div>
            <button class="btn btn-blue" style="width:100%;" onclick="addR()">SIMPAN TARIF</button>
        </div>
        <div class="card">
            <h4>📋 Daftar Tarif</h4>
            <table id="tbl-rt">
                <thead><tr><th>No.</th><th>Vendor</th><th>Tipe/Size</th><th>Bln</th><th>Hr</th><th>Aksi</th></tr></thead>
                <tbody id="body-rt"></tbody>
            </table>
        </div>
    </div>

    <div id="tab-unit" style="display:none">
        <div class="card" id="edit-unit-zone" style="display:none; background: #fffcf0; border-left: 4px solid var(--warning);">
            <h4>✏️ Edit Master Unit</h4>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap:10px;">
                <input type="hidden" id="edu-idx">
                <input type="text" id="edu-no" placeholder="No. Unit">
                <select id="edu-v"></select>
                <select id="edu-t"></select>
                <select id="edu-z"></select>
                <button class="btn btn-orange" onclick="simpanEditUnit()">SIMPAN</button>
                <button class="btn btn-red" onclick="batalEditUnit()">BATAL</button>
            </div>
        </div>
        <div class="card" id="entry-unit-zone">
            <h4>➕ Entry Unit Baru</h4>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap:10px;">
                <input type="text" id="mu-no" placeholder="No. Unit">
                <select id="mu-v"></select>
                <select id="mu-t"></select>
                <select id="mu-z"></select>
                <button class="btn btn-green" onclick="tambahUnitManual()">TAMBAH</button>
            </div>
        </div>
        <div class="card">
            <h4>🔍 Cari Unit</h4>
            <input type="text" id="src-u" onkeyup="pgU=1;renderU()" placeholder="Masukan No Unit untuk memfilter..." style="width:100%; margin-bottom:20px; padding:12px; box-sizing: border-box;">
            <table id="tbl-u">
                <thead><tr><th>No.</th><th>Unit</th><th>Vendor</th><th>Tipe/Size</th><th>Aksi</th></tr></thead>
                <tbody id="body-u"></tbody>
            </table>
            <div id="pg-u" class="pagination"></div>
        </div>
    </div>

    <div id="tab-trx" style="display:none">
        <div class="card" id="edit-trx-zone" style="display:none; background: #fffcf0; border-left: 4px solid var(--warning);">
            <h4>✏️ Edit Transaksi</h4>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:10px;">
                <input type="hidden" id="edx-idx">
                <select id="edx-no"></select>
                <input type="text" id="edx-s" placeholder="Ambil (dd/mm/yyyy)">
                <input type="text" id="edx-e" placeholder="Kembali (dd/mm/yyyy)">
                <select id="edx-st-t">
                    <option value="B">Bulanan (B)</option>
                    <option value="H">Harian (H)</option>
                </select>
                <button class="btn btn-orange" onclick="simpanEditTrx()">SIMPAN</button>
                <button class="btn btn-red" onclick="batalEditTrx()">BATAL</button>
            </div>
        </div>
        <div class="card" id="entry-trx-zone">
            <h4>➕ Entry Transaksi</h4>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:10px;">
                <input type="text" id="tx-no" list="list-u" placeholder="No Unit...">
                <datalist id="list-u"></datalist>
                <input type="text" id="tx-s" placeholder="Ambil (dd/mm/yyyy)">
                <input type="text" id="tx-e" placeholder="Kembali (dd/mm/yyyy)">
                <select id="tx-st-t">
                    <option value="B">Bulanan (B)</option>
                    <option value="H">Harian (H)</option>
                </select>
                <button class="btn btn-green" onclick="tambahTrx()">TAMBAH TRX</button>
            </div>
        </div>
        <div class="card">
            <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h4>📊 Riwayat Transaksi</h4>
                <button class="btn btn-orange" onclick="dlTrx()">📂 DOWNLOAD CSV</button>
            </div>
            <input type="text" id="src-x" onkeyup="pgX=1;renderX()" placeholder="Cari ID TRX atau No Unit..." style="width:100%; margin-bottom:20px; padding:12px; box-sizing: border-box;">
            <table id="tbl-x">
                <thead><tr><th>No.</th><th>ID TRX</th><th>Unit</th><th>Ambil</th><th>Kembali</th><th>St Tarif</th><th>St Unit</th><th>Biaya</th><th>Aksi</th></tr></thead>
                <tbody id="body-x"></tbody>
            </table>
            <div id="pg-x" class="pagination"></div>
        </div>
    </div>

    <div id="tab-rekon">
        <div class="card">
            <span class="judul-audit">📌 Audit Tagihan Vendor</span>
            <div class="tab-audit-container">
                <button id="btn-outstanding" class="tab-audit-btn active" onclick="switchAuditTab('outstanding')">Tab A: Outstanding</button>
                <button id="btn-keranjang" class="tab-audit-btn" onclick="switchAuditTab('keranjang')">Tab B: Keranjang / Permohonan</button>
            </div>
            
            <div id="area-outstanding">
                <input type="text" id="src-audit" onkeyup="renderAudit()" placeholder="🔍 Cari Unit untuk di Audit..." style="width:100%; padding:14px; border:2px solid var(--primary); border-radius:10px; margin-bottom:20px; box-sizing: border-box;">
                <div style="overflow-x: auto;">
                    <table id="tbl-audit">
                        <thead><tr style="background: var(--primary); color:white;"><th>ID TRX Induk</th><th>Unit</th><th>Ambil</th><th>Kembali</th><th>St Unit</th><th>Aksi</th></tr></thead>
                        <tbody id="body-audit"></tbody>
                    </table>
                </div>
            </div>

            <div id="area-keranjang" style="display:none">
                <div style="margin-bottom:25px; display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:15px; background:#f8fafc; padding:20px; border:1px solid var(--border-color); border-radius: 12px;">
                    <div><label style="font-weight:600; font-size: 11px;">VENDOR:</label><select id="aud-v-name" style="width:100%"></select></div>
                    <div><label style="font-weight:600; font-size: 11px;">NO. INVOICE:</label><input type="text" id="aud-no-inv" style="width:100%" placeholder="Inv/2026/..."></div>
                    <div><label style="font-weight:600; font-size: 11px;">TGL. INVOICE:</label><input type="text" id="aud-tgl-inv" placeholder="dd/mm/yyyy" style="width:100%"></div>
                </div>
                <div style="overflow-x: auto;">
                    <table id="tbl-cart">
                        <thead><tr style="background: var(--success); color:white;"><th>No</th><th>Unit</th><th>Masa Sewa</th><th>AYPSIS</th><th>Vendor Bill</th><th>Selisih</th><th>Ket.</th><th>Aksi</th></tr></thead>
                        <tbody id="body-cart"></tbody>
                        <tfoot id="foot-cart" style="background:#f8fafc; font-weight:bold;"></tfoot>
                    </table>
                </div>
                <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                    <button class="btn btn-blue" style="padding: 12px 30px;" onclick="simpanPranota()"><i class="fas fa-save"></i> SIMPAN PRANOTA</button>
                </div>
            </div>
        </div>
    </div>

    <div id="tab-pranota" style="display:none">
        <div class="card">
            <h4>📋 Daftar Pranota Sewa Kontainer (Final)</h4>
            <div style="overflow-x: auto;">
                <table id="tbl-p">
                    <thead><tr style="background:var(--sidebar-bg); color:white;"><th>No. Pranota</th><th>Vendor</th><th>No. Invoice</th><th>Tgl. Invoice</th><th align="right">Total</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody id="body-p"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
let db = { v:[], t:[], z:[], u:[], r:[], x:[], cart:[], p:[] };
let pgU = 1, pgX = 1; const rPP = 15;
let expAudit = null;
let currentAuditTab = 'outstanding';

// --- LOGIKA UTAMA AUDIT (MENU 5) ---
function genPeriode(x, idInduk) {
    const dAmbil = parseD(x.s); 
    const dAkhir = x.e ? parseD(x.e) : new Date(); 
    const mu = db.u.find(unit => unit.no === x.no);
    const r = mu ? db.r.find(rt => rt.v === mu.v && rt.t === mu.t && rt.z === mu.z) : null;
    const biayaSnapshot = !r ? 0 : (x.stT === 'H' ? (r.rh || 0) : (r.rb || 0));
    
    let h = `<table style="width:100%; background:white; border-radius: 8px; margin-top: 10px;"><thead><tr style="background:#f1f5f9"><th>Periode</th><th>Masa Sewa</th><th>AYPSIS</th><th>Vendor Bill</th><th>Alasan Selisih</th><th>Aksi</th></tr></thead>`;
    let curr = new Date(dAmbil), p = 1;
    
    while (true) {
        let sP = new Date(curr);
        let eP = new Date(curr.getFullYear(), curr.getMonth() + 1, curr.getDate() - 1);
        
        if (sP > dAkhir) break; 
        if (x.e && eP > dAkhir) eP = dAkhir;

        const diff = Math.ceil((eP - sP) / 86400000) + 1;
        const nilaiAYPSIS = (diff >= 28) ? biayaSnapshot : Math.round((diff/30)*biayaSnapshot);
        const idp = `${idInduk}-${p}`;

        if(!db.cart.some(c => c.idp === idp)) {
            h += `<tr><td>Bulan ke-${p}</td><td>${fmtTglLay(sP)} - ${fmtTglLay(eP)}</td><td>${fmtRibuan(nilaiAYPSIS)}</td><td><input type="text" id="v-${idp}" value="${fmtRibuan(nilaiAYPSIS)}" oninput="inputRibuan(this);onInputBill('${idp}',${nilaiAYPSIS})" style="width:110px; padding: 4px 8px;"></td><td><div id="note-wrapper-${idp}" style="display:none;"><input type="text" id="n-${idp}" placeholder="Wajib diisi..." oninput="onInputBill('${idp}',${nilaiAYPSIS})" style="width:180px; font-size:11px; padding:6px 10px; border:1px solid #ddd; border-radius:6px; outline:none;"></div></td><td><button id="btn-${idp}" class="btn btn-green" onclick="saveToCart('${idp}','${x.no}','${fmtTglDB(sP)} - ${fmtTglDB(eP)}',${nilaiAYPSIS})">+</button></td></tr>`;
        }
        
        if (x.e && eP >= dAkhir) break; 
        curr.setMonth(curr.getMonth() + 1); p++;
    }
    return h + `</table>`;
}

function onInputBill(idp, aypsis) {
    const vEl = document.getElementById('v-'+idp);
    const nWrap = document.getElementById('note-wrapper-'+idp);
    const nEl = document.getElementById('n-'+idp);
    const btn = document.getElementById('btn-'+idp);
    if(!vEl || !nWrap || !nEl || !btn) return;

    const bill = cleanNum(vEl.value);
    const isDiff = bill !== aypsis;
    
    if(isDiff) {
        nWrap.style.display = 'block';
        if(nEl.value.trim() === "") {
            btn.disabled = true;
            btn.style.opacity = '0.5';
            btn.style.cursor = 'not-allowed';
        } else {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
        }
    } else {
        nWrap.style.display = 'none';
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
    }
}

function saveToCart(idp, unit, masa, aypsis) {
    const vEl = document.getElementById('v-'+idp);
    const nEl = document.getElementById('n-'+idp);
    const vendorBill = cleanNum(vEl.value);
    const note = nEl ? nEl.value : "";
    db.cart.push({ idp, unit, masa, aypsis, vendorBill, note });
    updateDB();
}

function renderCart() {
    const body = document.getElementById('body-cart');
    const foot = document.getElementById('foot-cart');
    if(!body) return;

    const vSelect = document.getElementById('aud-v-name');
    if(vSelect) vSelect.innerHTML = db.v.filter(v => v.act !== false).map(v => `<option value="${v.val||v}">${v.val||v}</option>`).join('');

    let tBill = 0;
    body.innerHTML = db.cart.map((c, i) => {
        tBill += c.vendorBill;
        return `<tr><td>${i+1}</td><td>${c.unit}</td><td>${c.masa}</td><td>${fmtRibuan(c.aypsis)}</td><td>${fmtRibuan(c.vendorBill)}</td><td>${fmtRibuan(c.vendorBill - c.aypsis)}</td><td>${c.note||'-'}</td><td><button class="btn btn-red" style="padding: 4px 8px;" onclick="db.cart.splice(${i},1);updateDB()">Hapus</button></td></tr>`;
    }).join('');

    const dpp = tBill;
    const ppn = Math.round(dpp * 0.11);
    const pph = Math.round(dpp * 0.02);
    const grand = dpp + ppn - pph;

    foot.innerHTML = `
        <tr><td colspan="4" align="right" style="padding: 12px;">DPP (Total Bill)</td><td colspan="4">${fmtRibuan(dpp)}</td></tr>
        <tr><td colspan="4" align="right" style="padding: 12px;">PPN 11% (+)</td><td colspan="4">${fmtRibuan(ppn)}</td></tr>
        <tr><td colspan="4" align="right" style="padding: 12px;">PPh 2% (-)</td><td colspan="4" style="color:var(--danger)">${fmtRibuan(pph)}</td></tr>
        <tr style="background:var(--success); color:white;"><td colspan="4" align="right" style="padding: 15px; font-size: 1rem;">GRAND TOTAL</td><td colspan="4" style="font-size: 1rem;">Rp ${fmtRibuan(grand)}</td></tr>`;
}

// --- FUNGSI FORMATTING & TOOLS ---
function fmtTglLay(d) {
    const blnArr = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
    return `${d.getDate()} ${blnArr[d.getMonth()]} ${d.getFullYear().toString().substr(-2)}`;
}
function fmtTglDB(d) {
    return `${d.getDate().toString().padStart(2,'0')}/${(d.getMonth()+1).toString().padStart(2,'0')}/${d.getFullYear()}`;
}
function fmtRibuan(n) { return Math.round(n).toLocaleString('id-ID'); }
function cleanNum(s) { return parseInt(s.toString().replace(/\./g, '')) || 0; }
function inputRibuan(el) {
    let val = el.value.replace(/\./g, '');
    if(!isNaN(val) && val !== "") { el.value = parseInt(val).toLocaleString('id-ID'); }
}

// --- FUNGSI UPDATE & RENDER ---
function updateDB() {
    localStorage.setItem('AYPSIS_2026_DB', JSON.stringify(db));
    renderVTZ(); renderRT(); renderU(); renderX(); renderAudit(); renderCart(); renderP();
    const ops = (k) => db[k].filter(x => x.act !== false).map(x => `<option value="${x.val||x}">${x.val||x}</option>`).join('');
    ['v','t','z'].forEach(k => { 
        if(document.getElementById('rt-'+k)) document.getElementById('rt-'+k).innerHTML = ops(k); 
        if(document.getElementById('mu-'+k)) document.getElementById('mu-'+k).innerHTML = ops(k);
        if(document.getElementById('edu-'+k)) document.getElementById('edu-'+k).innerHTML = ops(k);
    });
    if(document.getElementById('list-u')) document.getElementById('list-u').innerHTML = db.u.filter(u => u.act !== false).map(u => `<option value="${u.no}">${u.v} | ${u.t} | ${u.z}</option>`).join('');
}

function switchAuditTab(tab) {
    currentAuditTab = tab;
    document.getElementById('btn-outstanding').classList.toggle('active', tab === 'outstanding');
    document.getElementById('btn-keranjang').classList.toggle('active', tab === 'keranjang');
    document.getElementById('area-outstanding').style.display = tab === 'outstanding' ? 'block' : 'none';
    document.getElementById('area-keranjang').style.display = tab === 'keranjang' ? 'block' : 'none';
    if(tab === 'keranjang') renderCart();
}

function renderAudit() {
    const s = (document.getElementById('src-audit')?.value || "").toUpperCase();
    const body = document.getElementById('body-audit');
    if(!body) return;
    body.innerHTML = db.x.filter(x => x.no.includes(s)).map(x => {
        const idTrx = x.no + toExcelSerial(x.s);
        const isExp = expAudit === idTrx;
        return `<tr><td><b>${idTrx}</b></td><td>${x.no}</td><td>${x.s}</td><td>${x.e||'-'}</td><td class="${x.e?'st-selesai':'st-sewa'}">${x.e?'SELESAI':'SEWA'}</td><td><button class="btn btn-blue" style="min-width: 100px;" onclick="toggleAudit('${idTrx}')">${isExp?'− Tutup':'+ Pilih'}</button></td></tr>` + 
        (isExp ? `<tr><td colspan="6" style="background:#f1f5f9; padding:20px; border:1px solid var(--border-color);">${genPeriode(x, idTrx)}</td></tr>` : '');
    }).join('');
}

function toggleAudit(id) { expAudit = (expAudit === id) ? null : id; renderAudit(); }
function parseD(s) { if(!s) return new Date(); const [d,m,y] = s.split('/').map(Number); return new Date(y, m-1, d); }
function toExcelSerial(d) { if(!d||!d.includes('/')) return "0"; const [dd,mm,yy]=d.split('/').map(Number); return Math.floor((new Date(yy,mm-1,dd)-new Date(1899,11,30))/86400000); }

// --- MENU 1-4 ---
function renderVTZ() { ['v','t','z'].forEach(k => { const body = document.querySelector(`#tbl-${k} tbody`); if(body) body.innerHTML = db[k].map((x,i) => `<tr class="${x.act===false?'non-aktif':''}"><td>${i+1}</td><td>${x.val||x}</td><td><button class="btn btn-orange" style="padding: 4px 10px;" onclick="edM('${k}',${i})">Edit</button> ${x.act!==false?`<button class="btn btn-red" style="padding: 4px 10px;" onclick="delM('${k}',${i})">Off</button>`:''}</td></tr>`).join(''); }); }
function renderRT() { const body = document.getElementById('body-rt'); if(body) body.innerHTML = db.r.map((x,i) => `<tr class="${x.act===false?'non-aktif':''}"><td>${i+1}</td><td>${x.v}</td><td>${x.t}/${x.z}</td><td>${fmtRibuan(x.rb)}</td><td>${fmtRibuan(x.rh)}</td><td><button class="btn btn-orange" style="padding: 4px 10px;" onclick="edR(${i})">Edit</button> ${x.act!==false?`<button class="btn btn-red" style="padding: 4px 10px;" onclick="delR(${i})">Off</button>`:''}</td></tr>`).join(''); }
function renderU() { const s = document.getElementById('src-u').value.toUpperCase(); const fil = db.u.filter(x => x.no.includes(s)); document.getElementById('body-u').innerHTML = fil.slice((pgU-1)*rPP, pgU*rPP).map((x,i) => { const idx = db.u.indexOf(x); return `<tr class="${x.act===false?'non-aktif':''}"><td>${((pgU-1)*rPP)+i+1}</td><td>${x.no}</td><td>${x.v}</td><td>${x.t}/${x.z}</td><td><button class="btn btn-orange" style="padding: 4px 10px;" onclick="bukaEditUnit(${idx})">Edit</button> ${x.act!==false?`<button class="btn btn-red" style="padding: 4px 10px;" onclick="delU(${idx})">Off</button>`:''}</td></tr>`; }).join(''); renderPg('pg-u', fil.length, 'pgU', 'renderU'); }
function renderX() { const s = document.getElementById('src-x').value.toUpperCase(); const fil = db.x.filter(x => x.no.includes(s)); document.getElementById('body-x').innerHTML = fil.slice((pgX-1)*rPP, pgX*rPP).map((x,i) => { const idx = db.x.indexOf(x); const mu = db.u.find(unit => unit.no === x.no); const r = mu ? db.r.find(rt => rt.v === mu.v && rt.t === mu.t && rt.z === mu.z && rt.act !== false) : null; const biaya = !r ? 0 : (x.stT === 'H' ? (r.rh || 0) : (r.rb || 0)); return `<tr><td>${((pgX-1)*rPP)+i+1}</td><td><b>${x.no + toExcelSerial(x.s)}</b></td><td>${x.no}</td><td>${x.s}</td><td>${x.e||'-'}</td><td>${x.stT||'B'}</td><td class="${x.e?'st-selesai':'st-sewa'}">${x.e?'SELESAI':'SEWA'}</td><td align="right">${fmtRibuan(biaya)}</td><td><button class="btn btn-orange" style="padding: 4px 10px;" onclick="bukaEditTrx(${idx})">Edit</button> <button class="btn btn-red" style="padding: 4px 10px;" onclick="delX(${idx})">Hapus</button></td></tr>`; }).join(''); renderPg('pg-x', fil.length, 'pgX', 'renderX'); }

function renderP() {
    const body = document.getElementById('body-p');
    if(!body) return;
    body.innerHTML = db.p.map((x,i) => `
        <tr>
            <td><b>${x.nomor}</b></td>
            <td>${x.vendor}</td>
            <td>${x.no_inv||'-'}</td>
            <td>${x.tgl_inv||'-'}</td>
            <td align="right">Rp ${fmtRibuan(x.total)}</td>
            <td><span class="${x.status==='PENDING'?'st-sewa':'st-selesai'}">${x.status}</span></td>
            <td>
                <button class="btn btn-blue" style="padding: 4px 10px;" onclick="cetakPranota(${x.id})">🔍 Show</button>
                <button class="btn btn-green" style="padding: 4px 10px;" onclick="cetakPranota(${x.id})">🖨️ Print</button>
            </td>
        </tr>`).join('');
}

function cetakPranota(id) {
    window.open('{{ url("/kontainer-sewa-final/print-pranota") }}/' + id, '_blank');
}

function edM(k, i) { const n = prompt("Ubah:", (db[k][i].val || db[k][i])); if(n) { if(typeof db[k][i] === 'object') db[k][i].val = n.toUpperCase(); else db[k][i] = {val:n.toUpperCase(), act:true}; updateDB(); } }
function edR(i) { const b = prompt("Bln:", db.r[i].rb), h = prompt("Hr:", db.r[i].rh); if(b) db.r[i].rb = parseInt(b); if(h) db.r[i].rh = parseInt(h); updateDB(); }
function bukaEditUnit(i) { document.getElementById('entry-unit-zone').style.display='none'; document.getElementById('edit-unit-zone').style.display='block'; document.getElementById('edu-idx').value=i; document.getElementById('edu-no').value=db.u[i].no; document.getElementById('edu-v').value=db.u[i].v; document.getElementById('edu-t').value=db.u[i].t; document.getElementById('edu-z').value=db.u[i].z; window.scrollTo(0,0); }
function simpanEditUnit() { const i = document.getElementById('edu-idx').value; db.u[i].no = document.getElementById('edu-no').value.toUpperCase(); db.u[i].v = document.getElementById('edu-v').value; db.u[i].t = document.getElementById('edu-t').value; db.u[i].z = document.getElementById('edu-z').value; batalEditUnit(); updateDB(); }
function batalEditUnit() { document.getElementById('entry-unit-zone').style.display='block'; document.getElementById('edit-unit-zone').style.display='none'; }
function bukaEditTrx(i) { document.getElementById('entry-trx-zone').style.display='none'; document.getElementById('edit-trx-zone').style.display='block'; document.getElementById('edx-idx').value=i; document.getElementById('edx-no').value=db.x[i].no; document.getElementById('edx-s').value=db.x[i].s; document.getElementById('edx-e').value=db.x[i].e; document.getElementById('edx-st-t').value=db.x[i].stT; window.scrollTo(0,0); }
function simpanEditTrx() { const i = document.getElementById('edx-idx').value; db.x[i].no = document.getElementById('edx-no').value; db.x[i].s = document.getElementById('edx-s').value; db.x[i].e = document.getElementById('edx-e').value; db.x[i].stT = document.getElementById('edx-st-t').value; batalEditTrx(); updateDB(); }
function batalEditTrx() { document.getElementById('entry-trx-zone').style.display='block'; document.getElementById('edit-trx-zone').style.display='none'; }
function showTab(e, id) { document.querySelectorAll('[id^="tab-"]').forEach(x => x.style.display='none'); document.getElementById(id).style.display='block'; document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active')); e.currentTarget.classList.add('active'); if(id==='tab-rekon'||id==='tab-pranota') updateDB(); }
function addM(k, id) { const v = document.getElementById(id).value.toUpperCase(); if(v) { db[k].push({val:v, act:true}); document.getElementById(id).value=''; updateDB(); } }
function addR() { db.r.push({ v:document.getElementById('rt-v').value, t:document.getElementById('rt-t').value, z:document.getElementById('rt-z').value, rb:parseInt(document.getElementById('rt-bln').value)||0, rh:parseInt(document.getElementById('rt-hr').value)||0, act:true }); updateDB(); }
function tambahUnitManual() { const no = document.getElementById('mu-no').value.toUpperCase(); if(no) { db.u.push({ no, v:document.getElementById('mu-v').value, t:document.getElementById('mu-t').value, z:document.getElementById('mu-z').value, act:true }); document.getElementById('mu-no').value=''; updateDB(); } }
function tambahTrx() { const no = document.getElementById('tx-no').value.toUpperCase(); const u = db.u.find(unit => unit.no === no && unit.act !== false); if(u && document.getElementById('tx-s').value) { db.x.push({ no:u.no, s:document.getElementById('tx-s').value, e:document.getElementById('tx-e').value, stT:document.getElementById('tx-st-t').value }); updateDB(); document.getElementById('tx-no').value=''; document.getElementById('tx-s').value=''; document.getElementById('tx-e').value=''; } else { alert("Unit Off/Data Kurang!"); } }
function renderPg(id, tot, pgVar, func) { const pgs = Math.ceil(tot/rPP); let h = `<button class="pg-btn" onclick="window['${pgVar}']=Math.max(1,window['${pgVar}']-1);${func}()">Prev</button>`; for(let i=1; i<=pgs; i++) { if(i===1||i===pgs||(i>=window[pgVar]-2 && i<=window[pgVar]+2)) h+=`<button class="pg-btn ${i===window[pgVar]?'active':''}" onclick="window['${pgVar}']=${i};${func}()">${i}</button>`; } const el = document.getElementById(id); if(el) el.innerHTML = h + `<button class="pg-btn" onclick="window['${pgVar}']=Math.min(${pgs},window['${pgVar}']+1);${func}()">Next</button>`; }
function delU(i) { if(confirm("Set Off?")) { db.u[i].act = false; updateDB(); } }
function delM(k, i) { if(confirm("Set Off?")) { if(typeof db[k][i] === 'object') db[k][i].act = false; else db[k][i] = {val:db[k][i], act:false}; updateDB(); } }
function delR(i) { if(confirm("Set Off?")) { db.r[i].act = false; updateDB(); } }
function delX(i) { if(confirm("Hapus Trx?")) { db.x.splice(i, 1); updateDB(); } }
function loadFile(e) { const fr = new FileReader(); fr.onload = (x) => { db = JSON.parse(x.target.result); if(!db.cart) db.cart = []; updateDB(); }; fr.readAsText(e.target.files[0]); }
function simpanPranota() {
    const v = document.getElementById('aud-v-name').value;
    const inv = document.getElementById('aud-no-inv').value;
    const tgl = document.getElementById('aud-tgl-inv').value;
    if(!v) return alert('Pilih Vendor Terlebih Dahulu');
    if(!db.cart.length) return alert('Keranjang masih kosong');

    if(!confirm('Simpan Pranota ini? Data dalam keranjang akan dikunci.')) return;

    fetch('{{ route('kontainer-sewa-final.submit-pranota') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            vendor: v,
            no_invoice: inv,
            tgl_invoice: tgl,
            cart: db.cart
        })
    })
    .then(r => r.json())
    .then(d => {
        if(d.success) {
            alert("Pranota Berhasil Disimpan: " + d.nomor);
            db.cart = []; // Kosongkan keranjang lokal
            localStorage.setItem('AYPSIS_2026_DB', JSON.stringify(db));
            location.reload(); // Reload untuk membersihkan status
        } else {
            alert("Error: " + d.message);
        }
    })
    .catch(e => {
        console.error(e);
        alert("Terjadi kesalahan sistem");
    });
}

function simpanData() {
    syncWithDB();
    const a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([JSON.stringify(db)]));
    a.download = "AYPSIS_DATA.json";
    a.click();
}

function syncWithDB() {
    fetch('{{ route('kontainer-sewa-final.sync') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ data: db })
    })
    .then(r => r.json())
    .then(d => {
        if(d.success) {
            console.log("Synced with DB");
            alert("Data berhasil disinkronkan ke Database!");
        } else {
            alert("Sync Error: " + d.message);
        }
    })
    .catch(e => console.error("Sync error", e));
}

window.onload = () => { 
    const s = localStorage.getItem('AYPSIS_2026_DB'); 
    let initial = {!! $initialData !!};
    
    if (typeof initial === 'string') {
        try {
            initial = JSON.parse(initial);
        } catch(e) {
            initial = { v:[], t:[], z:[], u:[], r:[], x:[], cart:[] };
        }
    }

    if(s) { 
        db = JSON.parse(s); 
        // Selalu ambil daftar pranota dan vendor terbaru dari database
        db.p = initial.p || [];
        db.v = initial.v || db.v;
        db.cart = initial.cart || db.cart; 
    } else {
        db = initial;
    }
    
    if(!db.cart) db.cart = []; 
    if(!db.p) db.p = [];
    updateDB(); 
};
</script>
</body>
</html>
