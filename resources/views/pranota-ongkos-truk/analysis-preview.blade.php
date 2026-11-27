<div class="analysis-metrics">
    <div class="analysis-metric"><span>DATA PRANOTA</span><strong>{{ $rows->count() }} <small>baris transaksi</small></strong></div>
    <div class="analysis-metric"><span>PERBEDAAN UJ</span><strong>{{ count($summaries) }} <small>kelompok plat &amp; tujuan</small></strong></div>
    <div class="analysis-metric"><span>TOTAL UJ</span><strong><small>Rp</small> {{ number_format($rows->sum('uj'), 0, ',', '.') }}</strong></div>
</div>
<div class="analysis-guide"><i class="fas fa-circle-info" aria-hidden="true"></i><span>Klik <strong>No Plat</strong> untuk melihat rincian biaya pada tujuan yang dipilih. UJ mencakup uang jalan dasar, biaya tambahan, dan penyesuaian.</span></div>
<h3 class="text-base font-bold text-gray-900 mb-3">Ringkasan Perbedaan UJ</h3>
<div class="overflow-x-auto mb-5 analysis-table-wrap">
    <table class="analysis-table" style="min-width:850px">
        <thead><tr><th>No Plat</th><th>Tujuan</th><th>No Baris</th><th>Variasi UJ (Rp)</th><th>Komponen Berbeda</th><th>Selisih Min-Maks (Rp)</th></tr></thead>
        <tbody>
        @forelse($summaries as $summaryIndex => $summary)
            <tr><td><button type="button" class="js-analysis-detail analysis-plate" data-detail-id="analysis-detail-{{ $summaryIndex }}" aria-controls="analysis-detail-{{ $summaryIndex }}" aria-expanded="false" aria-label="Lihat rincian {{ $summary['plat'] }} tujuan {{ $summary['tujuan'] }}">{{ $summary['plat'] }}</button></td><td>{{ $summary['tujuan'] }}</td><td>{{ $summary['numbers'] }}</td><td>{{ $summary['totals'] }}</td><td>{{ $summary['changes'] }}</td><td class="analysis-money analysis-difference">{{ number_format($summary['difference'], 0, ',', '.') }}</td></tr>
        @empty
            <tr><td colspan="6">Tidak ditemukan perbedaan UJ pada plat dan tujuan yang sama.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@foreach($summaries as $summaryIndex => $summary)
@php
    $rowNumbers = array_map('intval', explode(',', $summary['numbers']));
    $detailRows = $rows->filter(fn ($row) => in_array($row['no'], $rowNumbers, true))
        ->sortBy(fn ($row) => mb_strtoupper(trim($row['pengirim'])))->values();
@endphp
<section id="analysis-detail-{{ $summaryIndex }}" class="analysis-detail-panel" hidden aria-labelledby="analysis-detail-title-{{ $summaryIndex }}">
    <div class="flex items-center justify-between gap-3 mb-3">
        <h3 id="analysis-detail-title-{{ $summaryIndex }}" class="text-base font-bold text-gray-900" tabindex="-1">Rincian {{ $summary['plat'] }} - {{ $summary['tujuan'] }}</h3>
        <button type="button" class="js-analysis-detail-close text-sm text-blue-700 underline">Tutup rincian</button>
    </div>
    <p class="text-sm text-gray-600 mb-3">{{ $detailRows->count() }} baris untuk plat dan tujuan yang dipilih, diurutkan berdasarkan nama pengirim. Nomor tetap mengacu pada baris asli pranota. Perbedaan angka dijelaskan berdasarkan komponen tersimpan. Alasan operasional biaya tambahan memerlukan memo atau bukti pendukung.</p>
    <div class="overflow-x-auto analysis-table-wrap">
        <table class="analysis-table" style="min-width:1900px">
            <thead><tr>@foreach(['NO','NO SURAT JALAN','NO PLAT','NAMA PENGIRIM','SIZE','TUJUAN','ONGKOS','PELANCAR','UJ','NOMINAL','KETERANGAN'] as $heading)<th>{{ $heading }}</th>@endforeach</tr></thead>
            <tbody>
            @foreach($detailRows as $row)
                <tr>
                    @foreach(['no','sj','plat','pengirim','size','tujuan'] as $key)<td>{{ $row[$key] }}</td>@endforeach
                    @foreach(['ongkos','pelancar','uj','nominal'] as $key)<td class="analysis-money">{{ number_format($row[$key], 0, ',', '.') }}</td>@endforeach
                    <td style="min-width:400px">{{ $row['keterangan'] }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot><tr><td colspan="6"><strong>Subtotal plat dan tujuan terpilih</strong></td>@foreach(['ongkos','pelancar','uj','nominal'] as $key)<td class="analysis-money"><strong>{{ number_format($detailRows->sum($key), 0, ',', '.') }}</strong></td>@endforeach<td></td></tr></tfoot>
        </table>
    </div>
    <p class="text-sm text-gray-500 mt-3">ONGKOS mengikuti master saat analisis; NOMINAL mengikuti nilai item tersimpan. Tanda - berarti data tidak tersedia.</p>
</section>
@endforeach
