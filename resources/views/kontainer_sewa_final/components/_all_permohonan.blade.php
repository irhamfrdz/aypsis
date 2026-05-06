<div id="tab-all-permohonan" style="display:none">
    <div class="card">
        <div style="display:flex; justify-content:between; align-items:center; margin-bottom:20px;">
            <h4 style="margin:0;">9. Daftar Semua Permohonan Transfer</h4>
            <div style="margin-left:auto;">
                <input type="text" id="src-all-permohonan" placeholder="Cari Permohonan..." oninput="renderAllPermohonan()" style="width:250px;">
            </div>
        </div>

        <div style="max-height: 600px; overflow-y: auto;">
            <table>
                <thead>
                    <tr>
                        <th width="40">No.</th>
                        <th>Nomor</th>
                        <th>Vendor</th>
                        <th>Tanggal</th>
                        <th align="right">Total</th>
                        <th>Status</th>
                        <th>Dibuat Oleh</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody id="body-all-permohonan"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
function renderAllPermohonan() {
    const body = document.getElementById('body-all-permohonan');
    if(!body) return;

    const src = document.getElementById('src-all-permohonan').value.toLowerCase();
    const data = (db.all_permohonans || []).filter(x => 
        x.no.toLowerCase().includes(src) || 
        x.vendor.toLowerCase().includes(src)
    );
    
    body.innerHTML = data.map((x, i) => `
        <tr>
            <td align="center">${i+1}</td>
            <td><b>${x.no}</b></td>
            <td>${x.vendor}</td>
            <td>${x.tgl}</td>
            <td align="right" style="font-weight:600;">Rp ${fmtRibuan(x.total)}</td>
            <td><span class="st-sewa ${x.status === 'PAID' ? 'paid' : ''}">${x.status}</span></td>
            <td>${x.user}</td>
            <td>
                <button class="btn btn-blue" style="padding:4px 10px; font-size:11px;" onclick="printPermohonan(${x.id})">🖨️ PRINT</button>
            </td>
        </tr>`).join('');
    
    if(data.length === 0) body.innerHTML = '<tr><td colspan="8" align="center" style="padding:40px; color:#64748b;">☕ Tidak ada data permohonan transfer.</td></tr>';
}

function printPermohonan(id) {
    window.open('{{ url("/kontainer-sewa-final/print-permohonan") }}/' + id, '_blank');
}
</script>
