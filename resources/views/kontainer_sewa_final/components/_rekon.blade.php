<div id="tab-rekon">
    <div class="card">
        <span class="judul-audit">📌 Audit Tagihan Vendor</span>
        <div class="tab-audit-container">
            <button id="btn-outstanding" class="tab-audit-btn active" onclick="switchAuditTab('outstanding')">Tab A: Outstanding</button>
            <button id="btn-keranjang" class="tab-audit-btn" onclick="switchAuditTab('keranjang')">Tab B: Keranjang / Permohonan</button>
            <button id="btn-bulk-lunas" class="tab-audit-btn" onclick="switchAuditTab('bulk-lunas')">Tab C: Bulk Import Lunas</button>
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

        <!-- TAB C: BULK IMPORT LUNAS -->
        <div id="area-bulk-lunas" style="display:none">
            <div style="background: linear-gradient(135deg, #f0fdf4, #ecfdf5); border: 1px solid #86efac; border-radius: 12px; padding: 16px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                    <span style="font-size: 20px;">⚡</span>
                    <strong style="color: #166534; font-size: 14px;">Bulk Import Kontainer Lunas</strong>
                </div>
                <p style="color: #15803d; font-size: 12px; margin: 0;">
                    Fitur ini untuk memasukkan data kontainer yang <b>sudah SELESAI dan sudah dibayar (lunas)</b> ke dalam pranota secara massal, sehingga tidak akan muncul lagi di Outstanding dan tidak ditagih 2x.
                </p>
            </div>

            <!-- Step 1: Filter Vendor -->
            <div style="margin-bottom:20px; display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:15px; background:#f8fafc; padding:20px; border:1px solid var(--border-color); border-radius: 12px;">
                <div>
                    <label style="font-weight:600; font-size: 11px;">FILTER VENDOR:</label>
                    <select id="bulk-vendor" style="width:100%" onchange="renderBulkLunas()">
                        <option value="">-- Semua Vendor --</option>
                    </select>
                </div>
                <div>
                    <label style="font-weight:600; font-size: 11px;">NO. INVOICE (opsional):</label>
                    <input type="text" id="bulk-no-inv" style="width:100%" placeholder="Inv/2026/...">
                </div>
                <div>
                    <label style="font-weight:600; font-size: 11px;">TGL. INVOICE (opsional):</label>
                    <input type="text" id="bulk-tgl-inv" placeholder="dd/mmm/yyyy" style="width:100%" onblur="this.value = smartDate(this.value)">
                </div>
            </div>

            <!-- Counter -->
            <div style="display: flex; gap: 15px; margin-bottom: 15px; flex-wrap: wrap;">
                <div id="bulk-counter" style="background: #f1f5f9; padding: 10px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; color: var(--text-muted);">
                    Terpilih: <span id="bulk-selected-count" style="color: var(--primary);">0</span> periode
                </div>
                <div id="bulk-total-display" style="background: #f1f5f9; padding: 10px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; color: var(--text-muted);">
                    Total: <span id="bulk-selected-total" style="color: var(--success);">Rp 0</span>
                </div>
                <button class="btn btn-green" style="padding: 6px 14px; font-size: 11px;" onclick="toggleBulkSelectAll()">☑️ Pilih / Batal Semua</button>
            </div>

            <!-- Data Table -->
            <div style="overflow-x: auto; max-height: 500px; overflow-y: auto;">
                <table id="tbl-bulk-lunas">
                    <thead><tr style="background: #059669; color:white; position: sticky; top: 0; z-index: 10;">
                        <th style="width: 30px;"><input type="checkbox" id="bulk-check-all" onchange="toggleBulkSelectAll(this.checked)"></th>
                        <th>Unit</th>
                        <th>Vendor</th>
                        <th>Ambil</th>
                        <th>Kembali</th>
                        <th>Masa Sewa</th>
                        <th>Biaya (AYPSIS)</th>
                    </tr></thead>
                    <tbody id="body-bulk-lunas"></tbody>
                </table>
            </div>

            <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <button class="btn btn-green" style="padding: 12px 30px; font-size: 13px;" onclick="submitBulkLunas()">⚡ SIMPAN SEBAGAI PRANOTA LUNAS</button>
            </div>
        </div>
    </div>
</div>
