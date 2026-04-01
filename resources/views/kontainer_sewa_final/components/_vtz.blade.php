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
