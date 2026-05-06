<div id="tab-pay-transfer" style="display:none">
    <div class="card">
        <div style="display:flex; justify-content:between; align-items:center; margin-bottom:20px;">
            <h4 style="margin:0;">8. Pembayaran Permohonan Transfer</h4>
        </div>

        <div style="max-height: 600px; overflow-y: auto;">
            <table id="tbl-pay-transfer">
                <thead>
                    <tr>
                        <th width="40">No.</th>
                        <th>Nomor Permohonan</th>
                        <th>Vendor</th>
                        <th>Bank / Kas</th>
                        <th>Tanggal</th>
                        <th align="right">Total</th>
                        <th>Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody id="body-pay-transfer"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
function renderPayTransfer() {
    const body = document.getElementById('body-pay-transfer');
    if(!body) return;

    // pending_payments is passed from controller (as part of initialData)
    // or updated via updateDB
    const pending = db.pending_payments || [];
    
    body.innerHTML = pending.map((x, i) => `
        <tr>
            <td align="center">${i+1}</td>
            <td><b>${x.no}</b></td>
            <td>${x.vendor}</td>
            <td>${x.bank}</td>
            <td>${x.tgl}</td>
            <td align="right">Rp ${fmtRibuan(x.total)}</td>
            <td><span class="st-sewa">${x.status}</span></td>
            <td>
                <button class="btn btn-green" style="width:100%;" onclick="prosesBayarTransfer(${x.id}, '${x.no}', ${x.total})">🚀 BAYAR</button>
            </td>
        </tr>`).join('');
    
    if(pending.length === 0) body.innerHTML = '<tr><td colspan="7" align="center" style="padding:40px; color:#64748b;">☕ Tidak ada permohonan transfer yang perlu dibayar.</td></tr>';
}

function prosesBayarTransfer(id, nomor, total) {
    const msg = `Konfirmasi pembayaran untuk permohonan ${nomor} senilai Rp ${fmtRibuan(total)}?\n\n` +
                `Tindakan ini akan:\n` +
                `1. Mengubah status permohonan menjadi PAID\n` +
                `2. Mengubah status Pranota terkait menjadi PAID\n` +
                `3. Mencatat Jurnal Akuntansi otomatis`;

    if(!confirm(msg)) return;

    fetch('{{ url("/kontainer-sewa-final/pay-transfer") }}/' + id, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(r => r.json())
    .then(d => {
        if(d.success) {
            alert("✅ Pembayaran berhasil diproses!");
            location.reload();
        } else {
            alert("❌ Gagal: " + d.message);
        }
    })
    .catch(e => { 
        console.error(e); 
        alert("🚨 Terjadi kesalahan sistem!"); 
    });
}
</script>
