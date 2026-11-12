@extends('layouts.app')
@section('title', 'Pembayaran Pranota BPJS')
@section('page_title', 'Pembayaran Pranota BPJS')

@push('styles')
<style>
    .bpjs-dashboard { color: #374151; font-size: 13px; }
    .bpjs-dashboard .bpjs-panel { border: 1px solid #e5e7eb; border-radius: 12px; background: #fff; box-shadow: 0 1px 3px rgb(15 23 42 / .025); }
    .bpjs-dashboard .bpjs-summary { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
    .bpjs-dashboard .bpjs-stat { padding: 16px; min-width: 0; }
    .bpjs-dashboard .bpjs-amount { font-variant-numeric: tabular-nums; overflow-wrap: anywhere; }
    .bpjs-dashboard .bpjs-stat-value { margin-top: 12px; font-size: clamp(18px, 1.6vw, 24px); font-weight: 700; letter-spacing: -.025em; line-height: 1.3; }
    .bpjs-dashboard .bpjs-table { width: 100%; table-layout: fixed; border-collapse: collapse; }
    .bpjs-dashboard .bpjs-table th { padding: 10px 12px; background: #f9fafb; color: #6b7280; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; text-align: left; }
    .bpjs-dashboard .bpjs-table td { padding: 14px 12px; border-top: 1px solid #f0f1f4; vertical-align: middle; overflow-wrap: anywhere; }
    .bpjs-dashboard .bpjs-table tr { transition: background-color 160ms; }
    .bpjs-dashboard .bpjs-table tbody tr:hover { background: #fafaff; }
    .bpjs-dashboard .bpjs-table .bpjs-money { text-align: right; font-variant-numeric: tabular-nums; font-size: 12px; }
    .bpjs-dashboard .bpjs-link { color: #4338ca; font-weight: 600; }
    .bpjs-dashboard .bpjs-link:hover { text-decoration: underline; }
    .bpjs-dashboard .bpjs-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 11px; font-weight: 600; line-height: 1.35; transition: background-color 160ms, border-color 160ms, color 160ms; }
    .bpjs-dashboard .bpjs-btn:hover { background: #f9fafb; border-color: #a5b4fc; color: #4338ca; }
    .bpjs-dashboard .bpjs-btn-primary { background: #4f46e5; border-color: #4f46e5; color: white; }
    .bpjs-dashboard .bpjs-btn-primary:hover { background: #4338ca; border-color: #4338ca; color: white; }
    .bpjs-dashboard .bpjs-btn-danger { background: #fff1f2; border-color: #fecdd3; color: #be123c; }
    .bpjs-dashboard .bpjs-btn-danger:hover { background: #ffe4e6; border-color: #fda4af; color: #9f1239; }
    .bpjs-dashboard a:focus-visible, .bpjs-dashboard button:focus-visible { outline: 2px solid #6366f1; outline-offset: 3px; }
    .bpjs-dashboard .bpjs-btn:disabled { opacity: .4; cursor: not-allowed; }
    .bpjs-dashboard .bpjs-badge { display: inline-flex; align-items: center; gap: 5px; border-radius: 6px; padding: 4px 6px; font-size: 10px; font-weight: 600; line-height: 1.5; }
    .bpjs-dashboard [hidden] { display: none !important; }
    @media (max-width: 1200px) {
        .bpjs-dashboard .bpjs-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .bpjs-dashboard .bpjs-table th, .bpjs-dashboard .bpjs-table td { padding-left: 8px; padding-right: 8px; }
    }
    @media (max-width: 1023px) {
        .bpjs-dashboard .bpjs-pranotas thead { display: none; }
        .bpjs-dashboard .bpjs-pranotas, .bpjs-dashboard .bpjs-pranotas tbody { display: block; }
        .bpjs-dashboard .bpjs-pranotas tr { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); padding: 8px; border-top: 1px solid #e5e7eb; gap: 4px; }
        .bpjs-dashboard .bpjs-pranotas td { display: block; border: 0; padding: 8px; }
        .bpjs-dashboard .bpjs-pranotas td::before { content: attr(data-label); display: block; color: #6b7280; font-size: 10px; font-weight: 500; margin-bottom: 5px; }
        .bpjs-dashboard .bpjs-pranotas .bpjs-money { text-align: left; }
        .bpjs-dashboard .bpjs-pranotas .bpjs-row-action { grid-column: 1 / -1; }
        .bpjs-dashboard .bpjs-pranotas .bpjs-row-action::before { display: none; }
    }
    @media (max-width: 600px) {
        .bpjs-dashboard .bpjs-stat { padding: 12px; }
        .bpjs-dashboard .bpjs-stat-value { font-size: 18px; }
        .bpjs-dashboard .bpjs-history thead { display: none; }
        .bpjs-dashboard .bpjs-history, .bpjs-dashboard .bpjs-history tbody { display: block; }
        .bpjs-dashboard .bpjs-history tr { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); padding: 8px; border-top: 1px solid #e5e7eb; }
        .bpjs-dashboard .bpjs-history td { display: block; border: 0; padding: 8px; text-align: left; }
        .bpjs-dashboard .bpjs-history td::before { content: attr(data-label); display: block; color: #6b7280; font-size: 10px; margin-bottom: 5px; }
        .bpjs-dashboard .bpjs-history .bpjs-money { text-align: left; }
        .bpjs-dashboard .bpjs-history .bpjs-row-action { grid-column: 1 / -1; }
        .bpjs-dashboard .bpjs-history .bpjs-row-action::before { display: none; }
    }
    @media (max-width: 359px) { .bpjs-dashboard .bpjs-summary { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
@php
    // Ringkasan mengikuti pranota pada halaman aktif, tanpa mengubah perhitungan pembayaran.
    $rows = $pranotas->getCollection()->map(function ($pranota) {
        $paid = round($pranota->pembayaran->sum('pivot.subtotal'), 2);
        return ['pranota' => $pranota, 'paid' => $paid, 'remaining' => max(0, round($pranota->grand_total - $paid, 2))];
    });
    $history = $pranotas->getCollection()->flatMap(fn ($pranota) => $pranota->pembayaran->map(fn ($payment) => ['pranota' => $pranota, 'payment' => $payment]));
    $transactionCount = $history->pluck('payment.id')->unique()->count();
    $periods = $pranotas->getCollection()->map(fn ($pranota) => sprintf('%02d', $pranota->periode_bulan).'/'.$pranota->periode_tahun)->unique();
@endphp
<div class="bpjs-dashboard mx-auto w-full space-y-4 pb-6">
    <header class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold text-indigo-600">Payroll / BPJS</p>
            <h1 class="mt-1 text-xl font-bold tracking-tight text-gray-900">Pembayaran Pranota BPJS</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola tagihan dan riwayat pembayaran BPJS.</p>
        </div>
        @if($periods->isNotEmpty())
        <span class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-600"><i class="fa-regular fa-calendar mr-1.5" aria-hidden="true"></i>{{ $periods->count() === 1 ? 'Periode '.$periods->first() : $periods->count().' periode pada halaman ini' }}</span>
        @endif
    </header>

    @include('pembayaran-pranota-bpjs.partials.messages')

    <section aria-label="Ringkasan pembayaran" class="space-y-2">
        <div class="bpjs-summary">
            @foreach([
                ['label' => 'Total Tagihan', 'value' => $rows->sum(fn ($row) => $row['pranota']->grand_total), 'icon' => 'fa-file-invoice', 'color' => 'text-indigo-700', 'bg' => 'bg-indigo-50'],
                ['label' => 'Total Terbayar', 'value' => $rows->sum('paid'), 'icon' => 'fa-circle-check', 'color' => 'text-emerald-700', 'bg' => 'bg-emerald-50'],
                ['label' => 'Sisa Tagihan', 'value' => $rows->sum('remaining'), 'icon' => 'fa-clock', 'color' => 'text-amber-700', 'bg' => 'bg-amber-50'],
                ['label' => 'Total Transaksi Pembayaran', 'value' => $transactionCount, 'icon' => 'fa-receipt', 'color' => 'text-gray-900', 'bg' => 'bg-gray-100'],
            ] as $stat)
            <div class="bpjs-panel bpjs-stat">
                <div class="flex items-center justify-between gap-2"><p class="text-xs font-medium text-gray-500">{{ $stat['label'] }}</p><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg {{ $stat['bg'] }} {{ $stat['color'] }}"><i class="fa-solid {{ $stat['icon'] }}" aria-hidden="true"></i></span></div>
                <p class="bpjs-stat-value bpjs-amount {{ $stat['color'] }}">{{ $loop->last ? number_format($stat['value'], 0, ',', '.') : 'Rp '.number_format($stat['value'], 2, ',', '.') }}</p>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-gray-500">Ringkasan {{ $pranotas->count() }} pranota pada halaman ini{{ request('search') ? ' sesuai pencarian' : '' }}. Transaksi dihitung berdasarkan nomor pembayaran unik.</p>
    </section>

    <section class="bpjs-panel overflow-hidden" aria-labelledby="pranota-heading">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-4 py-3">
            <div><h2 id="pranota-heading" class="font-semibold text-gray-900">Daftar Pranota <span class="ml-1 rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500">{{ $pranotas->total() }}</span></h2><p class="mt-1 text-xs text-gray-500">Pantau tagihan dan progres pembayaran.</p></div>
            <form method="GET" action="{{ route('pembayaran-pranota-bpjs.index') }}" class="flex w-full flex-wrap gap-2 sm:w-auto">
                <label for="search-pranota" class="sr-only">Cari nomor pranota</label>
                <input id="search-pranota" type="search" name="search" value="{{ request('search') }}" placeholder="Cari nomor pranota…" class="min-w-0 flex-1 rounded-lg border border-gray-300 px-3 py-2 text-xs focus:border-indigo-500 focus:ring-indigo-500 sm:w-52">
                <button class="bpjs-btn bpjs-btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>Cari</button>
                @if(request('search'))<a href="{{ route('pembayaran-pranota-bpjs.index') }}" class="bpjs-btn">Reset</a>@endif
            </form>
        </div>
        @if($rows->isNotEmpty())
        <table class="bpjs-table bpjs-pranotas">
            <thead><tr><th>Nomor Pranota</th><th>Tanggal / Periode</th><th class="bpjs-money">Total Tagihan</th><th class="bpjs-money">Terbayar</th><th class="bpjs-money">Sisa Tagihan</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($rows as $row)
                @php
                    ['pranota' => $pranota, 'paid' => $paid, 'remaining' => $remaining] = $row;
                    $progress = $pranota->grand_total > 0 ? min(100, max(0, round($paid / $pranota->grand_total * 100, 1))) : 0;
                    $statusLabel = $pranota->status === 'cancelled' ? 'Dibatalkan' : ($pranota->status === 'paid' ? 'Lunas' : ($paid > 0 ? 'Dibayar Sebagian' : 'Belum Dibayar'));
                    $statusColor = $pranota->status === 'cancelled' ? 'bg-gray-100 text-gray-600' : ($pranota->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : ($paid > 0 ? 'bg-amber-50 text-amber-700' : 'bg-indigo-50 text-indigo-700'));
                @endphp
                <tr>
                    <td data-label="Nomor Pranota"><a href="{{ route('pranota-bpjs.show', $pranota) }}" class="bpjs-link">{{ $pranota->nomor_pranota }}</a></td>
                    <td data-label="Tanggal / Periode"><span class="text-xs font-medium text-gray-800">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</span><span class="mt-1 block text-xs text-gray-500">{{ sprintf('%02d', $pranota->periode_bulan) }}/{{ $pranota->periode_tahun }}</span></td>
                    <td data-label="Total Tagihan" class="bpjs-money font-medium"><span class="block text-xs text-gray-400">Rp</span>{{ number_format($pranota->grand_total, 2, ',', '.') }}</td>
                    <td data-label="Terbayar" class="bpjs-money font-semibold text-emerald-700"><span class="block text-xs font-normal text-gray-400">Rp</span>{{ number_format($paid, 2, ',', '.') }}</td>
                    <td data-label="Sisa Tagihan" class="bpjs-money font-semibold {{ $remaining > 0 ? 'text-amber-700' : 'text-gray-500' }}"><span class="block text-xs font-normal text-gray-400">Rp</span>{{ number_format($remaining, 2, ',', '.') }}</td>
                    <td data-label="Status"><span class="bpjs-badge {{ $statusColor }}">{{ $statusLabel }}</span><div class="mt-2 h-1 overflow-hidden rounded-full bg-gray-100" role="progressbar" aria-label="Pembayaran {{ $pranota->nomor_pranota }}" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"><div class="h-full rounded-full bg-emerald-500" style="width: {{ $progress }}%"></div></div><span class="mt-1 block text-gray-500" style="font-size: 10px">{{ number_format($progress, 1, ',', '.') }}% terbayar</span></td>
                    <td data-label="Aksi" class="bpjs-row-action">
                        @can('pembayaran-pranota-bpjs-create')
                        @if($remaining > 0 && in_array($pranota->status, ['draft', 'approved', 'unpaid', 'partial']))
                        <a href="{{ route('pembayaran-pranota-bpjs.create', ['pranota_id' => $pranota->id]) }}" class="bpjs-btn bpjs-btn-primary">Buat Pembayaran</a>
                        @else<span class="text-xs text-gray-400">{{ $pranota->status === 'cancelled' ? 'Dibatalkan' : ($remaining <= 0 ? 'Tidak ada sisa' : 'Tidak tersedia') }}</span>@endif
                        @else<span class="text-xs text-gray-400">—</span>@endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <div class="px-4 py-8 text-center"><i class="fa-regular fa-file-lines mb-3 text-2xl text-gray-300" aria-hidden="true"></i><p class="font-semibold text-gray-700">{{ request('search') ? 'Pranota tidak ditemukan' : 'Belum ada pranota BPJS' }}</p><p class="mt-1 text-xs text-gray-500">{{ request('search') ? 'Coba nomor lain atau reset pencarian.' : 'Pranota BPJS akan ditampilkan di sini setelah dibuat.' }}</p></div>
        @endif
        @if($pranotas->hasPages())<div class="border-t border-gray-100 px-4 py-3">{{ $pranotas->links() }}</div>@endif
    </section>

    <section class="bpjs-panel overflow-hidden" aria-labelledby="history-heading">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 px-4 py-3"><div><h2 id="history-heading" class="font-semibold text-gray-900">Riwayat Pembayaran</h2><p class="mt-1 text-xs text-gray-500">Pembayaran untuk pranota pada halaman ini. Nominal sesuai alokasi per pranota.</p></div><span class="text-xs text-gray-500">{{ $history->count() }} alokasi · {{ $transactionCount }} transaksi</span></div>
        @if($history->isNotEmpty())
        <div id="bpjs-history-search-controls" hidden class="flex flex-wrap items-center gap-2 border-b border-gray-100 px-4 py-3">
            <label for="bpjs-history-search" class="sr-only">Cari nomor pembayaran atau nomor pranota</label>
            <div class="relative w-full sm:max-w-sm">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-xs text-gray-400" aria-hidden="true"></i>
                <input id="bpjs-history-search" type="search" placeholder="Cari nomor pembayaran / nomor pranota…" aria-controls="bpjs-history-rows" class="w-full rounded-lg border border-gray-300 py-2 pl-8 pr-3 text-xs focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <button id="bpjs-history-clear" type="button" hidden class="bpjs-btn">Reset pencarian</button>
            <span class="text-xs text-gray-500">10 data per halaman</span>
        </div>
        <table id="bpjs-history-table" class="bpjs-table bpjs-history">
            <thead><tr><th>Nomor Pembayaran</th><th>Nomor Pranota</th><th class="bpjs-money">Nominal Pembayaran</th><th style="width: 26%">Aksi</th></tr></thead>
            <tbody id="bpjs-history-rows">
            @foreach($history as $entry)
                @php ['pranota' => $pranota, 'payment' => $payment] = $entry; @endphp
                <tr data-history-search="{{ $payment->nomor_pembayaran }} {{ $pranota->nomor_pranota }}">
                    <td data-label="Nomor Pembayaran"><a class="bpjs-link" href="{{ route('pembayaran-pranota-bpjs.show', $payment) }}">{{ $payment->nomor_pembayaran }}</a></td>
                    <td data-label="Nomor Pranota"><a class="bpjs-link" href="{{ route('pranota-bpjs.show', $pranota) }}">{{ $pranota->nomor_pranota }}</a></td>
                    <td data-label="Nominal Pembayaran" class="bpjs-money font-semibold text-emerald-700">Rp {{ number_format($payment->pivot->subtotal, 2, ',', '.') }}</td>
                    <td class="bpjs-row-action" data-label="Aksi"><div class="flex flex-wrap items-center gap-2">
                        <a target="_blank" rel="noopener" aria-label="Cetak {{ $payment->nomor_pembayaran }}" class="bpjs-btn" href="{{ route('pembayaran-pranota-bpjs.print', $payment) }}"><i class="fa-solid fa-print" aria-hidden="true"></i>Cetak</a>
                        <a target="_blank" rel="noopener" aria-label="Print Permohonan Transfer {{ $payment->nomor_pembayaran }}" class="bpjs-btn" href="{{ route('pembayaran-pranota-bpjs.print', [$payment, 'dokumen' => 'permohonan-transfer']) }}"><i class="fa-solid fa-file-invoice" aria-hidden="true"></i>Print Permohonan Transfer</a>
                        @can('pembayaran-pranota-bpjs-create')
                        <form method="POST" action="{{ route('pembayaran-pranota-bpjs.destroy', $payment) }}" onsubmit="return confirm('Tarik seluruh pembayaran {{ $payment->nomor_pembayaran }}? Saldo seluruh Akun Bank dan Bank yang Digunakan dikembalikan sesuai transaksi ini, dan tagihan dapat dibayar kembali.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bpjs-btn bpjs-btn-danger" aria-label="Tarik pembayaran {{ $payment->nomor_pembayaran }}">Tarik Pembayaran</button>
                        </form>
                        @endcan
                    </div></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div id="bpjs-history-no-results" hidden class="px-4 py-8 text-center" role="status">
            <p class="font-semibold text-gray-700">Riwayat pembayaran tidak ditemukan</p>
            <p class="mt-1 text-xs text-gray-500">Coba nomor pembayaran atau nomor pranota lain, atau reset pencarian.</p>
        </div>
        <div id="bpjs-history-pagination" hidden class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 px-4 py-3">
            <p id="bpjs-history-range" role="status" aria-live="polite" class="text-xs text-gray-500"></p>
            <nav aria-label="Halaman riwayat pembayaran" class="flex flex-wrap items-center gap-2"><button type="button" id="bpjs-history-prev" class="bpjs-btn" aria-controls="bpjs-history-rows">Sebelumnya</button><span id="bpjs-history-page" class="text-xs text-gray-500"></span><button type="button" id="bpjs-history-next" class="bpjs-btn" aria-controls="bpjs-history-rows">Berikutnya</button></nav>
        </div>
        @else
        <div class="px-4 py-8 text-center"><i class="fa-solid fa-receipt mb-3 text-2xl text-gray-300" aria-hidden="true"></i><p class="font-semibold text-gray-700">Belum ada riwayat pembayaran</p><p class="mt-1 text-xs text-gray-500">Pembayaran yang dicatat untuk pranota di atas akan muncul di sini.</p></div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const rows = Array.from(document.querySelectorAll('#bpjs-history-rows > tr'));
    const pageSize = 10;
    if (!rows.length) return;
    const pagination = document.getElementById('bpjs-history-pagination');
    const previous = document.getElementById('bpjs-history-prev');
    const next = document.getElementById('bpjs-history-next');
    const search = document.getElementById('bpjs-history-search');
    const clear = document.getElementById('bpjs-history-clear');
    const searchRows = rows.map(row => ({ row, text: row.dataset.historySearch.toLocaleLowerCase('id-ID') }));
    let filteredRows = rows;
    let page = 0;
    const render = () => {
        const start = page * pageSize;
        const total = filteredRows.length;
        rows.forEach(row => { row.hidden = true; });
        filteredRows.slice(start, start + pageSize).forEach(row => { row.hidden = false; });
        previous.disabled = page === 0;
        next.disabled = start + pageSize >= total;
        document.getElementById('bpjs-history-table').hidden = total === 0;
        document.getElementById('bpjs-history-no-results').hidden = total !== 0;
        document.getElementById('bpjs-history-range').textContent = `${total ? start + 1 : 0}–${Math.min(start + pageSize, total)} dari ${total} alokasi pembayaran${search.value.trim() ? ` (hasil pencarian dari ${rows.length} data)` : ''}`;
        document.getElementById('bpjs-history-page').textContent = `Halaman ${total ? page + 1 : 0} dari ${Math.ceil(total / pageSize)}`;
        clear.hidden = !search.value;
    };
    const filter = () => {
        const query = search.value.trim().toLocaleLowerCase('id-ID');
        filteredRows = searchRows.filter(entry => entry.text.includes(query)).map(entry => entry.row);
        page = 0;
        render();
    };
    search.addEventListener('input', filter);
    clear.addEventListener('click', () => { search.value = ''; filter(); search.focus(); });
    previous.addEventListener('click', () => { if (page > 0) { page--; render(); } });
    next.addEventListener('click', () => { if ((page + 1) * pageSize < filteredRows.length) { page++; render(); } });
    document.getElementById('bpjs-history-search-controls').hidden = false;
    pagination.hidden = false;
    render();
})();
</script>
@endpush
