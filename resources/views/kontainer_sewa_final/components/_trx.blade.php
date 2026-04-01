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
