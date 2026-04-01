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
