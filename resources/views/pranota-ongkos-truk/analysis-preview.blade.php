<form class="js-analysis-destination analysis-destination-card" action="{{ route('pranota-ongkos-truk.analysis-preview', $pranota->id) }}" method="GET">
    <div class="analysis-destination-intro">
        <span class="analysis-destination-icon"><i class="fas fa-location-dot" aria-hidden="true"></i></span>
        <div><h3>Analisis berdasarkan tujuan</h3><p>Pilih atau ganti tujuan. Tabel otomatis menampilkan perbandingan uang jalan seluruh plat pada tujuan tersebut.</p></div>
    </div>
    <div class="analysis-destination-controls">
        <div class="analysis-destination-field">
            <label for="analysis-destination">Tujuan <span aria-hidden="true">*</span></label>
            <select id="analysis-destination" name="tujuan" required aria-describedby="analysis-destination-help" @disabled(empty($destinations))>
                <option value="">{{ empty($destinations) ? 'Tidak ada tujuan tersedia' : 'Pilih tujuan pengiriman' }}</option>
                @foreach($destinations as $destination)<option value="{{ $destination }}" @selected($selectedDestination === $destination)>{{ $destination }}</option>@endforeach
            </select>
            <p id="analysis-destination-help">{{ count($destinations) }} tujuan tersedia dalam pranota ini.</p>
        </div>
    </div>
</form>
<div class="analysis-results" data-destination="{{ $selectedDestination }}">
    <div class="analysis-metrics">
        <div class="analysis-metric"><span>TUJUAN</span><strong>{{ $selectedDestination ?? 'Belum dipilih' }}</strong></div>
        <div class="analysis-metric"><span>CAKUPAN</span><strong>{{ $rows->count() }} <small>baris / {{ $rows->pluck('plat')->unique()->count() }} plat</small></strong></div>
        <div class="analysis-metric"><span>TOTAL UJ</span><strong><small>Rp</small> {{ number_format($rows->sum('uj'), 0, ',', '.') }}</strong></div>
    </div>
    <h3 class="text-base font-bold text-gray-900 mb-3">Rincian Analisis{{ $selectedDestination !== null ? ' Tujuan '.$selectedDestination : '' }}</h3>
    <p class="text-sm text-gray-600 mb-3">Warna pelancar yang sama menandakan nominal yang sama; warna berbeda menandakan nominal berbeda. Seluruh plat pada tujuan ini dibandingkan. Data diurutkan berdasarkan nama pengirim; nomor mengacu pada baris asli pranota. Perbedaan size dan biaya tambahan dapat memengaruhi total UJ. Alasan operasional memerlukan bukti pendukung.</p>
    @if($selectedDestination !== null && empty($summaries))<p class="text-sm text-gray-600 mb-3">{{ $rows->count() > 1 ? 'Total UJ sama pada seluruh baris tujuan ini.' : 'Hanya satu baris pada tujuan ini; belum ada pembanding.' }}</p>@endif
    <div class="overflow-x-auto analysis-table-wrap">
        <table class="analysis-table" style="min-width:1900px">
            <thead><tr>@foreach(['NO','NO SURAT JALAN','NO PLAT','NAMA PENGIRIM','SIZE','TUJUAN','ONGKOS','PELANCAR','UJ','NOMINAL','KETERANGAN'] as $heading)<th>{{ $heading }}</th>@endforeach</tr></thead>
            <tbody>
            @forelse($rows->sortBy(fn ($row) => mb_strtoupper(trim($row['pengirim'])))->values() as $row)
                <tr>
                    @foreach(['no','sj','plat','pengirim','size','tujuan'] as $key)<td>{{ $row[$key] }}</td>@endforeach
                    @foreach(['ongkos','pelancar','uj','nominal'] as $key)
<td class="analysis-money">@if($key === 'pelancar')@include('pranota-ongkos-truk.pelancar-badge', ['amount' => $row['pelancar'], 'amounts' => $rows->pluck('pelancar')->unique()->sort()->values()])@else{{ number_format($row[$key], 0, ',', '.') }}@endif</td>
@endforeach
                    <td style="min-width:400px">{{ $row['keterangan'] }}</td>
                </tr>
            @empty
                <tr><td colspan="11" style="text-align:center;padding:48px 16px;color:#64748b">{{ empty($destinations) ? 'Belum ada tujuan pada pranota ini.' : 'Pilih tujuan di atas untuk menampilkan data analisis.' }}</td></tr>
            @endforelse
            </tbody>
            <tfoot><tr><td colspan="6"><strong>Subtotal tujuan terpilih</strong></td>@foreach(['ongkos','pelancar','uj','nominal'] as $key)<td class="analysis-money"><strong>{{ number_format($rows->sum($key), 0, ',', '.') }}</strong></td>@endforeach<td></td></tr></tfoot>
        </table>
    </div>

</div>
