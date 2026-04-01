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
                <div><label style="font-weight:600; font-size: 11px;">TGL. INVOICE:</label><input type="text" id="aud-tgl-inv" placeholder="dd/mmm/yyyy" style="width:100%" onblur="this.value = smartDate(this.value)"></div>
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
