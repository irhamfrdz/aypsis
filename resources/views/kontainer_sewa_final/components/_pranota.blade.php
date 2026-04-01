<div id="tab-pranota" style="display:none">
    <div class="card" id="edit-pranota-zone" style="display:none; background: #fffcf0; border-left: 4px solid var(--warning); margin-bottom: 30px;">
        <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h4>✏️ Edit Pranota: <span id="edp-nomor" style="color:var(--primary)"></span></h4>
            <button class="btn btn-red" onclick="batalEditPranota()">TUTUP</button>
        </div>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:15px; background:white; padding:20px; border:1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
            <input type="hidden" id="edp-id">
            <div><label style="font-weight:600; font-size: 11px;">VENDOR:</label><input type="text" id="edp-v" style="width:100%; background:#f1f5f9;" disabled></div>
            <div><label style="font-weight:600; font-size: 11px;">NO. INVOICE:</label><input type="text" id="edp-no-inv" style="width:100%" placeholder="Inv/2026/..."></div>
            <div><label style="font-weight:600; font-size: 11px;">TGL. INVOICE:</label><input type="text" id="edp-tgl-inv" placeholder="dd/mmm/yyyy" style="width:100%" onblur="this.value = smartDate(this.value)"></div>
            <div>
                <label style="font-weight:600; font-size: 11px;">STATUS:</label>
                <select id="edp-status" style="width:100%">
                    <option value="PENDING">PENDING</option>
                    <option value="APPROVED">APPROVED</option>
                    <option value="PAID">PAID</option>
                    <option value="CANCELLED">CANCELLED</option>
                </select>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table id="tbl-edp-items">
                <thead><tr style="background: var(--warning); color:white;"><th>No</th><th>Unit</th><th>Masa Sewa</th><th>AYPSIS</th><th>Vendor Bill</th><th>Selisih</th><th>Ket.</th><th>Aksi</th></tr></thead>
                <tbody id="body-edp-items"></tbody>
            </table>
        </div>
        <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
            <button class="btn btn-blue" style="padding: 12px 30px;" onclick="simpanEditPranota()"><i class="fas fa-save"></i> UPDATE PRANOTA</button>
        </div>
    </div>
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
