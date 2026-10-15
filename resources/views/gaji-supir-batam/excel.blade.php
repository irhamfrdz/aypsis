<table>
    <thead>
        <tr><th colspan="8" style="font-weight: bold; font-size: 16px; text-align: center;">SLIP GAJI SUPIR BATAM</th></tr>
        <tr><th colspan="8" style="text-align: center;">Periode: {{ $gaji->periode_text }}</th></tr>
        <tr><th>Nama Supir</th><td colspan="3">{{ $gaji->karyawan->nama_lengkap }}</td><th>No. Plat</th><td colspan="3">{{ $gaji->karyawan->plat ?? '-' }}</td></tr>
        <tr><th>NIK</th><td colspan="3">{{ $gaji->karyawan->nik ?? '-' }}</td><th>Status Bayar</th><td colspan="3">{{ $gaji->status_pembayaran }}</td></tr>
        <tr><th colspan="8"></th></tr>
        <tr><th colspan="8" style="font-weight: bold;">Rincian Surat Jalan ({{ count($waybills) }})</th></tr>
        <tr>
            @foreach(['No.', 'Tipe', 'No. Surat Jalan', 'No. Kontainer', 'Tujuan Pengiriman', 'Ring', 'Tanggal', 'Uang Jalan'] as $heading)
                <th style="font-weight: bold; background-color: #e5e7eb; border: 1px solid #000;">{{ $heading }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse($waybills as $index => $wb)
            <tr>
                <td style="border: 1px solid #000;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000;">{{ $wb['type'] }}</td>
                <td style="border: 1px solid #000;">{{ $wb['no_surat_jalan'] }}</td>
                <td style="border: 1px solid #000;">{{ $wb['no_kontainer'] }}</td>
                <td style="border: 1px solid #000;">{{ $wb['tujuan'] }}</td>
                <td style="border: 1px solid #000;">{{ $wb['ring'] }}</td>
                <td style="border: 1px solid #000;">{{ $wb['tanggal'] }}</td>
                <td style="border: 1px solid #000;" data-format="#,##0">{{ $wb['rit'] }}</td>
            </tr>
        @empty
            <tr><td colspan="8">Tidak ada surat jalan pada periode ini.</td></tr>
        @endforelse
        <tr><td colspan="7" style="font-weight: bold;">Total Uang Jalan</td><td data-format="#,##0" style="font-weight: bold;">{{ collect($waybills)->sum('rit') }}</td></tr>
        <tr><td colspan="8"></td></tr>
        @if($bensinList->isNotEmpty())
            <tr><th colspan="8" style="font-weight: bold;">Rincian Pembelian Bensin</th></tr>
            <tr><th colspan="2">Total Liter</th><th colspan="3">Total Biaya Supir</th><th colspan="3">Total Biaya</th></tr>
            <tr>
                <td colspan="2" data-format="#,##0.00">{{ $bensinList->sum('liter') }}</td>
                <td colspan="3" data-format="#,##0">{{ $bensinList->sum('liter') * 13800 }}</td>
                <td colspan="3" data-format="#,##0">{{ $bensinList->sum('biaya') }}</td>
            </tr>
            <tr><td colspan="8"></td></tr>
        @endif
        <tr><th colspan="8" style="font-weight: bold;">Rincian Gaji</th></tr>
        <tr><td colspan="7">Total Gaji Pokok (Berdasarkan Surat Jalan)</td><td data-format="#,##0">{{ $gaji->gaji_pokok }}</td></tr>
        <tr><td colspan="7">Uang Berangkat Malam/Libur</td><td data-format="#,##0">{{ $gaji->uang_malam_libur }}</td></tr>
        <tr><td colspan="7">Potongan Biaya Bensin</td><td data-format="#,##0">{{ $gaji->biaya_bensin }}</td></tr>
        @if($gaji->is_potongan_5_persen)
            <tr><td colspan="7">Potongan 5%</td><td data-format="#,##0">{{ $gaji->nominal_potongan_5_persen }}</td></tr>
        @endif
        <tr><td colspan="7" style="font-weight: bold;">Total Gaji Bersih</td><td data-format="#,##0" style="font-weight: bold;">{{ $gaji->total_gaji }}</td></tr>
        @if($gaji->keterangan)
            <tr><td>Catatan</td><td colspan="7">{{ $gaji->keterangan }}</td></tr>
        @endif
    </tbody>
</table>
