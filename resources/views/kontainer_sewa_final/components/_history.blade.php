<div id="tab-history" style="display:none">
    <div class="card">
        <div style="display:flex; justify-content:between; align-items:center; margin-bottom:20px;">
            <h4 style="margin:0;">8. Riwayat Pembayaran</h4>
            <div style="display:flex; gap:10px; margin-left:auto;">
                <input type="text" id="src-history" placeholder="Cari Nomor Pembayaran..." oninput="renderHistory()" style="width:250px;">
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nomor Pembayaran</th>
                    <th>Tanggal</th>
                    <th>Bank / Kas</th>
                    <th align="right">Total Pembayaran</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="body-history"></tbody>
        </table>
    </div>
</div>

<!-- Modal Detail Pembayaran -->
<div id="modal-detail-pay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div class="card" style="width:700px; max-height:90vh; overflow-y:auto; position:relative;">
        <button onclick="closeModalDetailPay()" style="position:absolute; top:20px; right:20px; background:none; border:none; font-size:24px; cursor:pointer; color:#64748b;">&times;</button>
        <h4 id="det-pay-title">Detail Pembayaran</h4>
        <hr style="border:0; border-top:1px solid var(--border-color); margin:15px 0;">
        
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:20px;">
            <div>
                <p style="margin:4px 0; color:var(--text-muted);">Tanggal:</p>
                <p id="det-pay-tgl" style="margin:0; font-weight:600;"></p>
                <p style="margin:12px 0 4px 0; color:var(--text-muted);">Bank / Kas:</p>
                <p id="det-pay-bank" style="margin:0; font-weight:600;"></p>
            </div>
            <div align="right">
                <p style="margin:4px 0; color:var(--text-muted);">Total Bayar:</p>
                <p id="det-pay-total" style="margin:0; font-size:1.5rem; font-weight:800; color:var(--primary);"></p>
            </div>
        </div>

        <h5 style="margin-bottom:10px; font-weight:600;">Daftar Pranota Terlampir:</h5>
        <table style="margin-top:0;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th>Nomor Pranota</th>
                    <th>Vendor</th>
                    <th align="right">Subtotal</th>
                </tr>
            </thead>
            <tbody id="body-det-pay-items"></tbody>
        </table>

        <div style="margin-top:30px; display:flex; justify-content:flex-end;">
            <button class="btn btn-blue" onclick="closeModalDetailPay()">Tutup</button>
        </div>
    </div>
</div>

<script>
function renderHistory() {
    const s = (document.getElementById('src-history')?.value || "").toUpperCase();
    const body = document.getElementById('body-history');
    if(!body) return;

    if(!db.history) return body.innerHTML = '<tr><td colspan="6" align="center" style="padding:20px;">Belum ada data riwayat.</td></tr>';

    body.innerHTML = db.history.filter(x => x.no.includes(s)).map(x => `
        <tr>
            <td><b>${x.no}</b></td>
            <td>${smartDate(x.tgl)}</td>
            <td>${x.bank}</td>
            <td align="right">Rp ${fmtRibuan(x.total)}</td>
            <td><span class="st-selesai">${x.status}</span></td>
            <td>
                <button class="btn btn-blue" style="padding:4px 10px;" onclick="viewDetailPay(${x.id})">Detail</button>
                <button class="btn btn-green" style="padding:4px 10px;" onclick="printPayment(${x.id})">Cetak</button>
            </td>
        </tr>`).join('');
    
    if(body.innerHTML === '') body.innerHTML = '<tr><td colspan="6" align="center" style="padding:20px;">Tidak ada riwayat ditemukan.</td></tr>';
}

function printPayment(id) {
    window.open('{{ url("/kontainer-sewa-final/print-payment") }}/' + id, '_blank');
}

function viewDetailPay(id) {
    const p = db.history.find(x => x.id === id);
    if(!p) return;

    document.getElementById('det-pay-title').innerText = 'Pembayaran: ' + p.no;
    document.getElementById('det-pay-tgl').innerText = smartDate(p.tgl);
    document.getElementById('det-pay-bank').innerText = p.bank;
    document.getElementById('det-pay-total').innerText = 'Rp ' + fmtRibuan(p.total);

    const body = document.getElementById('body-det-pay-items');
    body.innerHTML = p.items.map(item => `
        <tr>
            <td><b>${item.nomor}</b></td>
            <td>${item.vendor}</td>
            <td align="right">Rp ${fmtRibuan(item.total)}</td>
        </tr>`).join('');

    document.getElementById('modal-detail-pay').style.display = 'flex';
}

function closeModalDetailPay() {
    document.getElementById('modal-detail-pay').style.display = 'none';
}
</script>
