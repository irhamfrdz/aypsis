<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Analisis UJ {{ $pranota->no_pranota }}</title>
<style>
@page { margin: 25px 24px 35px; }
body { font-family: DejaVu Sans, sans-serif; font-size: 8px; color: #243746; }
h1 { font-size: 19px; margin: 0 0 6px; }
h2 { font-size: 13px; margin: 16px 0 8px; }
p { line-height: 1.5; }
table { width: 100%; border-collapse: collapse; table-layout: fixed; }
th { background: #153c52; color: white; font-size: 7px; }
th, td { border: 0.5px solid #c6d2d8; padding: 5px 3px; vertical-align: top; overflow-wrap: break-word; }
td { font-size: 7px; line-height: 1.4; }
tr:nth-child(even) td { background: #f1f5f7; }
thead { display: table-header-group; }
tr { page-break-inside: avoid; }
.number { text-align: right; }
.details { page-break-before: always; }
footer { position: fixed; bottom: -22px; left: 0; right: 0; font-size: 7px; color: #657985; }
</style>
</head>
<body>
<footer>AYPSIS | Analisis UJ {{ $pranota->no_pranota }} | {{ now()->format('d/m/Y H:i') }}</footer>
<h1>Analisis Perbedaan Uang Jalan</h1>
<p><strong>{{ $pranota->no_pranota }}</strong> | Tanggal pranota: {{ $pranota->tanggal_pranota?->format('d/m/Y') }}<br>
Cakupan: {{ $rows->count() }} baris; {{ count($summaries) }} kelompok plat dan tujuan dengan UJ berbeda. Nomor mengikuti urutan data pranota.</p>
<p>UJ adalah total uang jalan beserta biaya tambahan dan penyesuaian yang tersimpan pada SJ terkait. Perbandingan dilakukan untuk plat dan tujuan yang sama. Kolom keterangan merinci komponen dan selisih terhadap baris dengan UJ terendah dalam kelompok.</p>
<table>
<thead><tr><th width="10%">NO PLAT</th><th width="13%">TUJUAN</th><th width="17%">NO BARIS ASLI</th><th width="22%">VARIASI UJ (Rp)</th><th width="28%">KOMPONEN YANG BERBEDA</th><th width="10%">SELISIH MIN-MAKS (Rp)</th></tr></thead>
<tbody>
@forelse($summaries as $summary)
<tr><td>{{ $summary['plat'] }}</td><td>{{ $summary['tujuan'] }}</td><td>{{ $summary['numbers'] }}</td><td>{{ $summary['totals'] }}</td><td>{{ $summary['changes'] }}</td><td class="number">{{ number_format($summary['difference'], 0, ',', '.') }}</td></tr>
@empty
<tr><td colspan="6">Tidak ditemukan perbedaan total UJ pada plat dan tujuan yang sama.</td></tr>
@endforelse
</tbody>
</table>
<p><strong>Dasar pembacaan:</strong> ONGKOS mengikuti master tujuan dan size saat laporan dibuat. NOMINAL disalin dari item pranota yang tersimpan, tidak dihitung ulang. TGL TT dan NO BUKTI mengikuti tanda terima dan nomor Accurate pembayaran seperti tampilan cetak pranota. Tanda - berarti data tidak tersedia.</p>
<p><strong>Batas kesimpulan:</strong> laporan menjelaskan sumber selisih angka berdasarkan komponen tersimpan. Alasan operasional biaya tambahan hanya dapat dipastikan dari memo, keterangan atau bukti pendukung. Perbedaan UJ sendiri belum membuktikan kesalahan input. SJ tanpa data UJ ditandai secara khusus.</p>
<div class="details">
<h2>Rincian Seluruh Baris Pranota</h2>
<p>Seluruh nilai uang dalam rupiah. RIT S = rit supir; RIT K = rit kenek.</p>
<table>
<thead><tr>
@foreach(['NO','TGL SJ','TGL TT','NO SURAT JALAN','NO PLAT','SIZE','RIT S','RIT K','NO BUKTI','TUJUAN','ONGKOS','UJ','NOMINAL','KETERANGAN'] as $index => $heading)
<th style="width: {{ [2.5,5,5,7,6,3,3,3,8,8,6,6,6,31.5][$index] }}%">{{ $heading }}</th>
@endforeach
</tr></thead>
<tbody>
@foreach($rows as $row)
<tr>
@foreach(['no','tgl_sj','tgl_tt','sj','plat','size','rit_s','rit_k','bukti','tujuan'] as $key)<td>{{ $row[$key] }}</td>@endforeach
@foreach(['ongkos','uj','nominal'] as $key)<td class="number">{{ number_format($row[$key], 0, ',', '.') }}</td>@endforeach
<td>{{ $row['keterangan'] }}</td>
</tr>
@endforeach
</tbody>
</table>
<p><strong>Subtotal:</strong> ONGKOS Rp{{ number_format($rows->sum('ongkos'), 0, ',', '.') }} | UJ Rp{{ number_format($rows->sum('uj'), 0, ',', '.') }} | NOMINAL item Rp{{ number_format($rows->sum('nominal'), 0, ',', '.') }}.</p>
@if(is_array($pranota->adjustments) && count($pranota->adjustments))
<p><strong>Penyesuaian pranota:</strong><br>
@foreach($pranota->adjustments as $adjustment)
{{ $adjustment['keterangan'] ?? '-' }}: Rp{{ number_format($adjustment['nominal'] ?? 0, 0, ',', '.') }}<br>
@endforeach
</p>
@else
<p>Penyesuaian: Rp{{ number_format($pranota->adjustment, 0, ',', '.') }}.</p>
@endif
<p><strong>Total pranota tersimpan: Rp{{ number_format($pranota->total_nominal, 0, ',', '.') }}.</strong></p>
</div>
</body>
</html>
