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
