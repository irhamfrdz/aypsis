@extends('layouts.app')

@section('title', 'Dashboard Perjalanan Kendaraan')
@section('page_title', 'Dashboard Perjalanan Kendaraan')

@section('content')
<style>
    .vehicle-report-tabs { display: inline-flex; gap: 4px; max-width: 100%; padding: 5px; border: 1px solid #e2e8f0; border-radius: 14px; background: #f1f5f9; }
    .vehicle-report-tab { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 11px 18px; border-radius: 10px; color: #64748b; font-size: 14px; font-weight: 600; transition: background .15s, color .15s, box-shadow .15s; }
    .vehicle-report-tab:hover { background: #e2e8f0; color: #334155; }
    .vehicle-report-tab[aria-selected="true"] { background: #4f46e5; color: #fff; box-shadow: 0 2px 5px rgb(79 70 229 / .2); }
    .vehicle-report-tab:focus-visible { outline: 2px solid #6366f1; outline-offset: 3px; }
    .vehicle-report-panel[hidden] { display: none !important; }
    @media (max-width: 480px) { .vehicle-report-tabs { display: flex; } .vehicle-report-tab { flex: 1; padding: 10px; font-size: 12px; } }
    .ranking-chart-summary::-webkit-details-marker { display: none; }
    #ranking-chart-disclosure .ranking-chart-hide { display: none; }
    #ranking-chart-disclosure[open] .ranking-chart-hide { display: inline; }
    #ranking-chart-disclosure[open] .ranking-chart-show { display: none; }
    #ranking-chart-disclosure[open] .ranking-chart-chevron { transform: rotate(180deg); }
    .ranking-chart-scroll { max-height: 500px; overflow-y: auto; scrollbar-gutter: stable; border: 1px solid #e2e8f0; border-radius: 14px; background: #fff; }
    .ranking-chart-row { display: grid; grid-template-columns: 32px minmax(100px, 150px) minmax(80px, 1fr) 110px; gap: 12px; align-items: center; padding: 15px 16px; border-bottom: 1px solid #f1f5f9; transition: background .15s; }
    .ranking-chart-track { height: 26px; border-left: 1px solid #cbd5e1; background: #fafbff; background-image: linear-gradient(to right, transparent calc(100% - 1px), #e2e8f0 1px); background-size: 25% 100%; }
    .ranking-chart-bar { height: 100%; border-radius: 0 5px 5px 0; background: linear-gradient(90deg, #a5b4fc, #6366f1); box-shadow: inset 0 1px 0 rgb(255 255 255 / .25); transform-origin: left; }
    .ranking-chart-row[data-rank="1"] .ranking-chart-bar { background: linear-gradient(90deg, #34d399, #059669); }
    .ranking-chart-row:hover { background: #f8faff; }
    .ranking-chart-row[data-rank="1"] { background: #f0fdf9; }
    .ranking-chart-rank { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 8px; background: #f1f5f9; color: #64748b; font-size: 12px; font-weight: 700; }
    .ranking-chart-row[data-rank="1"] .ranking-chart-rank { background: #d1fae5; color: #047857; }
    .ranking-chart-row[data-rank="2"] .ranking-chart-rank,
    .ranking-chart-row[data-rank="3"] .ranking-chart-rank { background: #e0e7ff; color: #4338ca; }
    .ranking-chart-axis { position: sticky; top: 0; z-index: 1; margin: 0; background: #f8fafc; color: #94a3b8; font-size: 10px; font-weight: 600; }
    .ranking-chart-ticks { display: flex; justify-content: space-between; font-variant-numeric: tabular-nums; gap: 4px; }
    .ranking-chart-axis-label { font-size: 10px; text-transform: uppercase; letter-spacing: .06em; color: #64748b; }
    .ranking-chart-overview { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-bottom: 20px; }
    .ranking-chart-stat { min-width: 0; padding: 14px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; }
    .ranking-chart-stat:first-child { background: #ecfdf5; border-color: #a7f3d0; }
    .ranking-chart-stat-label { color: #64748b; font-size: 11px; margin-bottom: 6px; }
    .ranking-chart-stat-value { color: #0f172a; font-size: 16px; font-weight: 700; overflow-wrap: anywhere; }
    .ranking-chart-metric:not([hidden]) .ranking-chart-bar { animation: ranking-bar-grow .75s cubic-bezier(.2,.7,.2,1) both; animation-delay: var(--bar-delay, 0ms); }
    .ranking-chart-metric[hidden] { display: none !important; }
    @keyframes ranking-bar-grow { from { transform: scaleX(0); } to { transform: scaleX(1); } }
    @media (prefers-reduced-motion: reduce) { .ranking-chart-metric:not([hidden]) .ranking-chart-bar { animation: none; } }
    @media (max-width: 640px) {
        .ranking-chart-row { grid-template-columns: 26px minmax(0, 1fr) auto; gap: 8px; padding: 12px; }
        .ranking-chart-axis .ranking-chart-ticks { grid-column: 2 / 4; }
        .ranking-chart-axis .ranking-chart-axis-label { display: none; }
        .ranking-chart-overview { gap: 8px; }
        .ranking-chart-stat { padding: 12px 10px; }
        .ranking-chart-stat-value { font-size: 13px; }
        .ranking-chart-track { grid-column: 2 / 4; grid-row: 2; height: 18px; }
        .ranking-chart-value { grid-column: 3; grid-row: 1; }
    }
</style>
<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    <header class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-900 to-indigo-700 px-6 py-8 text-white shadow-lg sm:px-8 sm:py-10">
        <div class="absolute -right-12 -top-20 h-56 w-56 rounded-full border border-white/10 bg-white/5" aria-hidden="true"></div>
        <div class="absolute -bottom-28 right-32 h-52 w-52 rounded-full border border-white/10" aria-hidden="true"></div>
        <div class="relative flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-xl ring-1 ring-white/20"><i class="fas fa-route" aria-hidden="true"></i></span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-200">Pemantauan Armada</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">Perjalanan Kendaraan</h1>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-indigo-100">Jarak GPS, waktu idle, rit, serta pemakaian barang dan ban dalam satu tampilan.</p>
            </div>
        </div>
    </header>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="periode-title">
        <div class="mb-5 flex items-center gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><i class="far fa-calendar-alt" aria-hidden="true"></i></span>
            <div>
                <h2 id="periode-title" class="font-bold text-slate-900">Pilih periode</h2>
                <p class="text-sm text-slate-500">Data ditampilkan sesuai tanggal yang dipilih.</p>
            </div>
        </div>

        @if($errors->any())
            <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="GET" action="{{ route('dashboard-perjalanan-kendaraan.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5 lg:items-end">
            <div class="min-w-0">
                <label for="tanggal_dari" class="mb-2 block text-sm font-semibold text-slate-700"><i class="fas fa-calendar-alt mr-1.5 text-slate-400" aria-hidden="true"></i>Dari tanggal</label>
                <input id="tanggal_dari" name="tanggal_dari" type="date" required value="{{ old('tanggal_dari', $tanggalDari) }}" class="block h-11 w-full rounded-xl border-slate-300 bg-slate-50 px-3 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
            </div>
            <div class="min-w-0">
                <label for="tanggal_sampai" class="mb-2 block text-sm font-semibold text-slate-700"><i class="fas fa-calendar-check mr-1.5 text-slate-400" aria-hidden="true"></i>Sampai tanggal</label>
                <input id="tanggal_sampai" name="tanggal_sampai" type="date" required min="{{ old('tanggal_dari', $tanggalDari) }}" value="{{ old('tanggal_sampai', $tanggalSampai) }}" class="block h-11 w-full rounded-xl border-slate-300 bg-slate-50 px-3 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
            </div>
            <div class="min-w-0">
                <label for="search" class="mb-2 block text-sm font-semibold text-slate-700"><i class="fas fa-search mr-1.5 text-slate-400" aria-hidden="true"></i>Cari kendaraan / supir</label>
                <input id="search" name="search" type="search" maxlength="100" value="{{ $search }}" placeholder="Nomor polisi, KIR, kode, atau supir" class="block h-11 w-full rounded-xl border-slate-300 bg-slate-50 px-3 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
            </div>
            <div class="min-w-0">
                <label for="ukuran" class="mb-2 block text-sm font-semibold text-slate-700">Ukuran kendaraan</label>
                <select id="ukuran" name="ukuran" class="block h-11 w-full rounded-xl border-slate-300 bg-slate-50 px-3 text-sm text-slate-900 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="semua" @selected($ukuran === 'semua')>Semua unit</option>
                    <option value="panjang" @selected($ukuran === 'panjang')>Kendaraan panjang</option>
                    <option value="pendek" @selected($ukuran === 'pendek')>Kendaraan pendek</option>
                    <option value="belum_diisi" @selected($ukuran === 'belum_diisi')>Ukuran belum diisi / lainnya</option>
                </select>
            </div>
            <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <i class="fas fa-chart-line" aria-hidden="true"></i> Tampilkan
            </button>
        </form>
    </section>

    @if(!$hasReport)
        <div class="rounded-2xl border border-dashed border-indigo-200 bg-indigo-50/60 px-6 py-14 text-center">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-xl text-indigo-600 shadow-sm"><i class="far fa-calendar-alt" aria-hidden="true"></i></span>
            <h2 class="mt-4 text-lg font-bold text-slate-900">Pilih periode untuk mulai</h2>
            <p class="mt-1 text-sm text-slate-600">Tentukan tanggal awal dan akhir, lalu tampilkan laporan perjalanan.</p>
        </div>
    @else
        <nav class="flex flex-wrap gap-2" aria-label="Kelompok ukuran kendaraan">
            @foreach(['semua' => 'Semua unit', 'panjang' => 'Kendaraan panjang', 'pendek' => 'Kendaraan pendek', 'belum_diisi' => 'Ukuran belum diisi / lainnya'] as $size => $sizeLabel)
                <a href="{{ route('dashboard-perjalanan-kendaraan.index', array_merge(request()->query(), ['ukuran' => $size])) }}"
                   aria-current="{{ $ukuran === $size ? 'page' : 'false' }}"
                   class="rounded-lg border px-3 py-2 text-xs font-semibold {{ $ukuran === $size ? 'border-slate-700 bg-slate-700 text-white' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">{{ $sizeLabel }}</a>
            @endforeach
        </nav>
        <p class="text-xs text-slate-500">Ringkasan, ranking, grafik, dan rincian mengikuti kelompok ukuran yang dipilih berdasarkan Master Mobil.</p>
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Ringkasan Perjalanan</p>
                <h2 class="mt-1 text-xl font-bold text-slate-900">{{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}</h2>
            </div>
            <p class="rounded-full bg-slate-100 px-3 py-1.5 text-xs text-slate-600">Berdasarkan data GPS yang tersimpan</p>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-indigo-100 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2 text-sm font-medium text-slate-600"><i class="fas fa-route text-indigo-600" aria-hidden="true"></i> Total jarak</div>
                <p class="mt-3 text-2xl font-bold text-slate-900">{{ number_format($totalDistance, 2, ',', '.') }} <span class="text-sm font-medium text-slate-500">km</span></p>
            </div>
            <div class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2 text-sm font-medium text-slate-600"><i class="fas fa-truck text-blue-600" aria-hidden="true"></i> Kendaraan tercatat</div>
                <p class="mt-3 text-2xl font-bold text-slate-900">{{ number_format($vehicleStats->count(), 0, ',', '.') }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2 text-sm font-medium text-slate-600"><i class="fas fa-calendar-check text-emerald-600" aria-hidden="true"></i> Hari dengan GPS</div>
                <p class="mt-3 text-2xl font-bold text-slate-900">{{ number_format($dailyTotals->count(), 0, ',', '.') }}</p>
            </div>
            <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2 text-sm font-medium text-slate-600"><i class="fas fa-chart-line text-amber-600" aria-hidden="true"></i> Rata-rata per hari</div>
                <p class="mt-3 text-2xl font-bold text-slate-900">{{ number_format($dailyTotals->count() ? $totalDistance / $dailyTotals->count() : 0, 2, ',', '.') }} <span class="text-sm font-medium text-slate-500">km</span></p>
            </div>
        </div>

        @if($vehicleStats->isEmpty())
            <div class="rounded-2xl border border-slate-200 bg-white px-6 py-12 text-center shadow-sm">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-truck" aria-hidden="true"></i></span>
                <h3 class="mt-4 font-bold text-slate-800">Belum ada data perjalanan</h3>
                <p class="mt-1 text-sm text-slate-500">{{ $search !== '' ? 'Tidak ada kendaraan yang cocok dengan pencarian pada periode ini.' : 'Coba pilih periode lain yang memiliki data GPS.' }}</p>
            </div>
        @else
            <div class="vehicle-report-tabs" role="tablist" aria-label="Tampilan laporan kendaraan">
                <button type="button" id="vehicle-ranking-tab" class="vehicle-report-tab" role="tab" aria-selected="true" aria-controls="vehicle-ranking-panel" tabindex="0">
                    <i class="fas fa-trophy" aria-hidden="true"></i> Ranking Kendaraan
                </button>
                <button type="button" id="vehicle-detail-tab" class="vehicle-report-tab" role="tab" aria-selected="false" aria-controls="vehicle-detail-panel" tabindex="-1">
                    <i class="fas fa-list-alt" aria-hidden="true"></i> Detail Rincian
                </button>
            </div>

            <div id="vehicle-ranking-panel" class="vehicle-report-panel" role="tabpanel" aria-labelledby="vehicle-ranking-tab" tabindex="0">
            <section class="space-y-4" aria-labelledby="ranking-title">
                <div>
                    <h3 id="ranking-title" class="text-lg font-bold text-slate-900"><i class="fas fa-trophy mr-1.5 text-slate-400" aria-hidden="true"></i>Ranking kendaraan per variabel</h3>
                    <p class="mt-1 max-w-4xl text-sm leading-relaxed text-slate-600">Peringkat 1 adalah posisi terbaik untuk setiap variabel. Jarak tempuh, hari bergerak, dan jumlah rit diurutkan dari nilai tertinggi; durasi idle, pergantian ban/velg, dan pemakaian barang/servis diurutkan dari nilai terendah. Kendaraan tanpa data untuk suatu variabel dapat muncul dengan nilai nol.</p>
                </div>
                <details id="ranking-chart-disclosure" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <summary class="ranking-chart-summary flex cursor-pointer list-none flex-wrap items-center justify-between gap-3 rounded-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-indigo-500 [&::-webkit-details-marker]:hidden">
                        <span class="font-bold text-slate-900"><i class="fas fa-chart-bar mr-2 text-indigo-500" aria-hidden="true"></i>Perbandingan ranking kendaraan</span>
                        <span class="inline-flex items-center gap-2 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700">
                            <span class="ranking-chart-hide">Sembunyikan</span><span class="ranking-chart-show">Tampilkan</span>
                            <i class="fas fa-chevron-down ranking-chart-chevron" aria-hidden="true"></i>
                        </span>
                    </summary>
                    <div class="mt-5 mb-5 flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="mt-1 text-sm text-slate-500">Pilih variabel untuk membandingkan nilai setiap kendaraan.</p>
                        </div>
                        <div class="w-full sm:w-auto">
                            <label for="ranking-chart-variable" class="mb-1.5 block text-xs font-semibold text-slate-600">Variabel ranking</label>
                            <select id="ranking-chart-variable" class="w-full rounded-xl border-slate-300 bg-slate-50 py-2 pl-3 pr-9 text-sm text-slate-800 focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($rankings as $key => $ranking)
                                    <option value="{{ $key }}">{{ $ranking['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @foreach($rankings as $key => $ranking)
                        @php
                            $chartMax = max(0, (float) $ranking['rows']->max('value'));
                            $rawStep = $chartMax > 0 ? $chartMax / 4 : 1;
                            $magnitude = pow(10, floor(log10($rawStep)));
                            $normalizedStep = $rawStep / $magnitude;
                            $chartStep = ($normalizedStep <= 1 ? 1 : ($normalizedStep <= 2 ? 2 : ($normalizedStep <= 5 ? 5 : 10))) * $magnitude;
                            if (!in_array($ranking['unit'], ['km', 'jam'], true)) $chartStep = max(1, ceil($chartStep));
                            $chartCeiling = $chartStep * 4;
                            $chartDecimals = $chartStep < 1 ? min(4, (int) ceil(-log10($chartStep))) : 0;
                            $chartLeader = $ranking['rows']->first();
                        @endphp
                        <div class="ranking-chart-metric" data-ranking-metric="{{ $key }}" @if(!$loop->first) hidden @endif>
                            <div class="mb-4 flex flex-wrap items-center justify-between gap-2 rounded-xl bg-slate-50 px-3 py-2.5 text-xs text-slate-600">
                                <span><i class="fas fa-sort-amount-down mr-1.5 text-indigo-500" aria-hidden="true"></i>{{ $ranking['better'] === 'higher' ? 'Nilai lebih tinggi mendapat ranking lebih baik' : 'Nilai lebih rendah mendapat ranking lebih baik' }}</span>
                                <span>Satuan: <strong>{{ $ranking['unit'] }}</strong> &middot; {{ $ranking['rows']->count() }} kendaraan</span>
                            </div>
                            <div class="ranking-chart-overview">
                                <div class="ranking-chart-stat">
                                    <p class="ranking-chart-stat-label"><i class="fas fa-trophy mr-1 text-emerald-600" aria-hidden="true"></i>Peringkat pertama</p>
                                    <p class="ranking-chart-stat-value">{{ $chartLeader['vehicle'] ?? '-' }}</p>
                                </div>
                                <div class="ranking-chart-stat">
                                    <p class="ranking-chart-stat-label">Nilai peringkat pertama</p>
                                    <p class="ranking-chart-stat-value">{{ number_format($chartLeader['value'] ?? 0, in_array($ranking['unit'], ['km', 'jam'], true) ? 2 : 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">{{ $ranking['unit'] }}</span></p>
                                </div>
                                <div class="ranking-chart-stat">
                                    <p class="ranking-chart-stat-label">Rata-rata kendaraan</p>
                                    <p class="ranking-chart-stat-value">{{ number_format($ranking['rows']->avg('value') ?? 0, 2, ',', '.') }} <span class="text-xs font-normal text-slate-500">{{ $ranking['unit'] }}</span></p>
                                </div>
                            </div>
                            <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                <h5 class="text-sm font-semibold text-slate-800">{{ $ranking['label'] }} <span class="font-normal text-slate-400">({{ $ranking['unit'] }})</span></h5>
                                <span class="text-xs text-slate-500">Urutan peringkat terbaik ke terendah</span>
                            </div>
                            <div class="ranking-chart-scroll" role="list" aria-label="Diagram batang {{ $ranking['label'] }}">
                                <div class="ranking-chart-row ranking-chart-axis" aria-hidden="true">
                                    <span class="ranking-chart-axis-label">No.</span><span class="ranking-chart-axis-label">Kendaraan</span>
                                    <div class="ranking-chart-ticks">@for($tick = 0; $tick <= 4; $tick++)<span>{{ number_format($chartStep * $tick, $chartDecimals, ',', '.') }}</span>@endfor</div>
                                    <span class="ranking-chart-axis-label text-right">Nilai ({{ $ranking['unit'] }})</span>
                                </div>
                                @foreach($ranking['rows'] as $entry)
                                    <div class="ranking-chart-row" role="listitem" data-rank="{{ $entry['rank'] }}">
                                        <span class="ranking-chart-rank" aria-label="Peringkat {{ $entry['rank'] }}">{{ $entry['rank'] }}</span>
                                        <span class="break-words text-xs font-semibold text-slate-700">{{ $entry['vehicle'] }}</span>
                                        <div class="ranking-chart-track" aria-hidden="true"><div class="ranking-chart-bar" style="width: {{ max(0, min(100, (float) $entry['value'] / $chartCeiling * 100)) }}%; --bar-delay: {{ min($loop->index, 12) * 35 }}ms"></div></div>
                                        <span class="ranking-chart-value text-right text-xs font-semibold tabular-nums text-slate-800">{{ number_format($entry['value'], in_array($ranking['unit'], ['km', 'jam'], true) ? 2 : 0, ',', '.') }} <span class="font-normal text-slate-500">{{ $ranking['unit'] }}</span></span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-slate-500">
                                <span><span class="mr-1.5 inline-block h-2.5 w-2.5 rounded-sm bg-emerald-500" aria-hidden="true"></span>Ranking 1</span>
                                <span><span class="mr-1.5 inline-block h-2.5 w-2.5 rounded-sm bg-indigo-500" aria-hidden="true"></span>Kendaraan lainnya</span>
                                <span>Panjang batang menunjukkan nilai aktual, dimulai dari nol.</span>
                            </div>
                        </div>
                    @endforeach
                    <p class="mt-3 text-xs leading-relaxed text-slate-500">Nilai nol dapat mencakup kendaraan tanpa data pada variabel ini. Urutan mengikuti tabel ranking.</p>
                </details>
                <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-3">No.</th>
                                <th class="whitespace-nowrap px-4 py-3"><i class="fas fa-truck mr-1.5 text-slate-400" aria-hidden="true"></i>Kendaraan</th>
                                <th class="whitespace-nowrap px-4 py-3">Ukuran</th>
                                <th class="whitespace-nowrap px-4 py-3"><i class="fas fa-user mr-1.5 text-slate-400" aria-hidden="true"></i>Nama supir</th>
                                @foreach($rankings as $ranking)
                                    <th class="whitespace-nowrap px-4 py-3"><i class="fas {{ match ($ranking['label']) {
                                        'Jarak tempuh', 'Hari bergerak' => 'fa-route',
                                        'Durasi idle' => 'fa-clock',
                                        'Jumlah rit' => 'fa-exchange-alt',
                                        'Pergantian ban & velg' => 'fa-circle',
                                        'Pemakaian barang / servis' => 'fa-tools',
                                        default => 'fa-chart-bar',
                                    } }} mr-1.5 text-slate-400" aria-hidden="true"></i>{{ $ranking['label'] }}</th>
                                @endforeach
                                <th class="whitespace-nowrap px-4 py-3">OB</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($vehicleStats as $index => $row)
                                @php
                                    $vehicleLabel = $row->mobil?->nomor_polisi ?: 'Kendaraan #'.$row->mobil_id;
                                    $vehicleSize = strtolower(trim((string) $row->mobil?->ukuran_tractor_head));
                                    $driverLabel = $row->mobil?->karyawan?->nama_panggilan ?: ($row->mobil?->karyawan?->nama_lengkap ?: 'Supir tidak tercatat');
                                    $aiMetrics = $rankings->map(function ($ranking) use ($vehicleLabel) {
                                        $entry = $ranking['rows']->firstWhere('vehicle', $vehicleLabel);
                                        return $entry ? ['name' => $ranking['label'], 'rank' => $entry['rank'], 'total' => $ranking['rows']->count(), 'value' => number_format($entry['value'], in_array($ranking['unit'], ['km', 'jam'], true) ? 2 : 0, ',', '.'), 'unit' => $ranking['unit'], 'better' => $ranking['better']] : null;
                                    })->filter()->values();
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-500">{{ $index + 1 }}</td>
                                <th scope="row" class="whitespace-nowrap px-4 py-3 text-left font-semibold text-slate-800">{{ $vehicleLabel }}</th>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        @if(in_array($vehicleSize, ['panjang', 'pendek'], true))
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $vehicleSize === 'panjang' ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-700' }}">{{ strtoupper($vehicleSize) }}</span>
                                        @else
                                            <span class="text-slate-400">Belum diisi</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-700">{{ $row->mobil?->karyawan?->nama_panggilan ?: ($row->mobil?->karyawan?->nama_lengkap ?: 'Supir tidak tercatat') }}</td>
                                    @foreach($rankings as $ranking)
                                        @php $rankEntry = $ranking['rows']->firstWhere('vehicle', $vehicleLabel); @endphp
                                        <td class="whitespace-nowrap px-4 py-3 text-slate-700">
                                            @if($rankEntry)<span class="mr-1.5 inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-indigo-50 px-1.5 text-xs font-bold text-indigo-700">{{ $rankEntry['rank'] }}</span><span class="font-semibold">{{ number_format($rankEntry['value'], in_array($ranking['unit'], ['km', 'jam'], true) ? 2 : 0, ',', '.') }}</span><span class="text-slate-500"> {{ $ranking['unit'] }}</span>@else—@endif
                                        </td>
                                    @endforeach
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-400" aria-label="Data OB belum tersedia">—</td>
                                </tr>
                                <tr class="bg-indigo-50/40">
                                    <td colspan="{{ 5 + $rankings->count() }}" class="px-4 pb-3 pt-0">
                                        <button type="button" class="ai-ranking-explain cursor-pointer text-xs font-semibold text-indigo-700 hover:text-indigo-900" data-payload="{{ json_encode(['vehicle' => $vehicleLabel, 'driver' => $driverLabel, 'period' => \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y').' - '.\Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y'), 'metrics' => $aiMetrics], JSON_HEX_APOS | JSON_HEX_QUOT) }}">Buat penjelasan AI untuk {{ $vehicleLabel }}</button>
                                        <p class="ai-ranking-result mt-2 hidden whitespace-pre-line rounded-lg border border-indigo-100 bg-white p-3 text-xs leading-relaxed text-slate-700" role="status"></p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            </div>

            <div id="vehicle-detail-panel" class="vehicle-report-panel" role="tabpanel" aria-labelledby="vehicle-detail-tab" tabindex="0" hidden>
            <section aria-labelledby="kendaraan-title" class="space-y-4">
                <div class="flex flex-wrap items-end justify-between gap-2">
                    <div>
                        <h3 id="kendaraan-title" class="text-lg font-bold text-slate-900"><i class="fas fa-list-alt mr-1.5 text-slate-400" aria-hidden="true"></i>Detail rincian kendaraan</h3>
                        <p class="mt-1 text-sm text-slate-500">Diurutkan dari jarak terjauh. Buka kartu untuk melihat rincian.</p>
                    </div>
                    <span class="text-xs text-slate-500">{{ $vehicleStats->count() }} kendaraan</span>
                </div>

                <div class="grid gap-4">
                    @foreach($vehicleStats as $row)
                        @php
                            $distancePercent = $maxVehicleDistance > 0 ? round((float) $row->total_km / $maxVehicleDistance * 100, 1) : 0;
                            $idleSeconds = (int) $row->idle_seconds;
                        @endphp
                        <article class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="p-5 sm:p-6">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div class="flex min-w-0 items-start gap-3">
                                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><i class="fas fa-truck" aria-hidden="true"></i></span>
                                        <div class="min-w-0">
                                            <h4 class="break-words text-lg font-bold text-slate-900">{{ $row->mobil?->nomor_polisi ?: 'Kendaraan #'.$row->mobil_id }}</h4>
                                            <p class="mt-0.5 break-words text-sm text-slate-500">{{ $row->mobil?->merek ?: 'Merek tidak tercatat' }} · {{ $row->mobil?->karyawan?->nama_panggilan ?: ($row->mobil?->karyawan?->nama_lengkap ?: 'Supir tidak tercatat') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-left sm:text-right">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Jarak tempuh</p>
                                        <p class="mt-1 text-2xl font-bold text-indigo-700">{{ number_format((float) $row->total_km, 2, ',', '.') }} <span class="text-sm font-medium">km</span></p>
                                    </div>
                                </div>

                                <div class="mt-5 h-2 overflow-hidden rounded-full bg-slate-100" role="meter" aria-label="Jarak dibanding kendaraan terjauh" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $distancePercent }}">
                                    <div class="h-full rounded-full bg-indigo-500" style="width: {{ $distancePercent }}%"></div>
                                </div>

                                <dl class="mt-5 grid grid-cols-2 gap-2 sm:grid-cols-3 xl:grid-cols-6">
                                    <div class="rounded-xl bg-slate-50 px-3 py-3"><dt class="text-xs text-slate-500"><i class="fas fa-satellite-dish mr-1.5 text-slate-400" aria-hidden="true"></i>Hari GPS</dt><dd class="mt-1 font-bold text-slate-900">{{ $row->days_with_data }} hari</dd></div>
                                    <div class="rounded-xl bg-slate-50 px-3 py-3"><dt class="text-xs text-slate-500"><i class="fas fa-route mr-1.5 text-slate-400" aria-hidden="true"></i>Hari bergerak</dt><dd class="mt-1 font-bold text-slate-900">{{ $row->moving_days }} hari</dd></div>
                                    <div class="rounded-xl bg-amber-50 px-3 py-3"><dt class="text-xs text-amber-800"><i class="fas fa-clock mr-1.5 text-slate-400" aria-hidden="true"></i>Durasi idle</dt><dd class="mt-1 font-bold text-amber-900">{{ $row->days_with_idle_data > 0 ? intdiv($idleSeconds, 3600).' jam '.intdiv($idleSeconds % 3600, 60).' menit' : 'Belum tersedia' }}</dd>@if($row->days_with_idle_data > 0 && $row->days_with_idle_data < $row->days_with_data)<p class="mt-1 text-xs text-amber-700">{{ $row->days_with_idle_data }}/{{ $row->days_with_data }} hari</p>@endif</div>
                                    <div class="rounded-xl bg-slate-50 px-3 py-3"><dt class="text-xs text-slate-500"><i class="fas fa-exchange-alt mr-1.5 text-slate-400" aria-hidden="true"></i>Rit</dt><dd class="mt-1 font-bold text-slate-900">{{ number_format($row->rit_count, 0, ',', '.') }}</dd></div>
                                    <div class="rounded-xl bg-slate-50 px-3 py-3"><dt class="text-xs text-slate-500"><i class="fas fa-circle mr-1.5 text-slate-400" aria-hidden="true"></i>Pergantian Ban &amp; Velg</dt><dd class="mt-1 font-bold text-slate-900">{{ number_format($row->tire_replacement_count, 0, ',', '.') }} kali</dd><p class="text-xs text-slate-500">{{ number_format($row->tire_count, 0, ',', '.') }} unit</p></div>
                                    <div class="rounded-xl bg-slate-50 px-3 py-3"><dt class="text-xs text-slate-500"><i class="fas fa-tools mr-1.5 text-slate-400" aria-hidden="true"></i>Barang / servis</dt><dd class="mt-1 font-bold text-slate-900">{{ number_format($row->amprahan_usages->count(), 0, ',', '.') }} kali</dd></div>
                                </dl>
                            </div>

                            <details class="group border-t border-slate-100 bg-slate-50/50">
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-5 py-4 text-sm font-semibold text-indigo-700 hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 sm:px-6 [&::-webkit-details-marker]:hidden">
                                    <span><i class="fas fa-clipboard-list mr-1.5 text-slate-400" aria-hidden="true"></i>Detail rit, ban &amp; velg, dan barang / servis</span>
                                    <i class="fas fa-chevron-down text-xs transition-transform group-open:rotate-180" aria-hidden="true"></i>
                                </summary>
                                <div class="space-y-7 border-t border-slate-100 px-5 py-5 sm:px-6">
                                    <section aria-label="Detail rit">
                                        <div class="mb-3 flex flex-wrap items-center gap-2"><h5 class="font-bold text-slate-900"><i class="fas fa-route mr-1.5 text-slate-400" aria-hidden="true"></i>Detail rit</h5><span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-semibold text-indigo-700">{{ number_format($row->rit_count, 0, ',', '.') }} rit</span></div>
                                        @if($row->rit_details->isEmpty())
                                            <p class="rounded-xl bg-white px-4 py-4 text-sm text-slate-500">Tidak ada surat jalan rit pada periode ini.</p>
                                        @else
                                            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                                                @foreach($row->rit_details as $detail)
                                                    <div class="min-w-0 rounded-xl border border-slate-200 bg-white p-4">
                                                        <div class="flex flex-wrap items-start justify-between gap-2"><p class="break-all font-bold text-slate-900">{{ $detail['nomor'] ?: '-' }}</p><span class="rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">{{ $detail['jenis'] }}</span></div>
                                                        <p class="mt-1 text-xs text-slate-500">{{ $detail['tanggal'] }} · {{ ucfirst($detail['kegiatan'] ?: '-') }}</p>
                                                        <dl class="mt-3 space-y-1.5 text-xs"><div class="flex gap-2"><dt class="w-20 shrink-0 text-slate-500">Supir</dt><dd class="min-w-0 break-words font-medium text-slate-700">{{ $detail['supir'] ?: '-' }}</dd></div><div class="flex gap-2"><dt class="w-20 shrink-0 text-slate-500">Pengirim</dt><dd class="min-w-0 break-words text-slate-700">{{ $detail['pengirim'] ?: '-' }}</dd></div><div class="flex gap-2"><dt class="w-20 shrink-0 text-slate-500">Tujuan ambil</dt><dd class="min-w-0 break-words text-slate-700">{{ $detail['tujuan_ambil'] ?: '-' }}</dd></div></dl>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </section>

                                    @include('dashboard-perjalanan-kendaraan.partials.tire-details', ['tireDetails' => $row->tire_details])

                                    <section aria-label="Barang dan servis" class="border-t border-slate-200 pt-6">
                                        <div class="mb-3 flex flex-wrap items-center gap-2"><h5 class="font-bold text-slate-900"><i class="fas fa-tools mr-1.5 text-slate-400" aria-hidden="true"></i>Barang / servis dipakai</h5><span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">{{ number_format($row->amprahan_usages->count(), 0, ',', '.') }} kali</span></div>
                                        @if($row->amprahan_usages->isEmpty())
                                            <p class="rounded-xl bg-white px-4 py-4 text-sm text-slate-500">Tidak ada pemakaian barang atau servis pada periode ini.</p>
                                        @else
                                            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                                                @foreach($row->amprahan_usages as $usage)
                                                    <div class="min-w-0 rounded-xl border border-slate-200 bg-white p-4">
                                                        <div class="flex flex-wrap items-start justify-between gap-2"><p class="min-w-0 break-words font-bold text-slate-900">{{ $usage['nama_barang'] }}</p><span class="text-xs font-semibold text-amber-700">{{ \Carbon\Carbon::parse($usage['tanggal'])->format('d/m/Y') }}</span></div>
                                                        <p class="mt-1 break-words text-xs text-slate-500">{{ $usage['type_barang'] ?: '-' }} · {{ $usage['posisi'] }}</p>
                                                        <div class="mt-3 flex flex-wrap justify-between gap-2 border-t border-slate-100 pt-3 text-xs"><span class="text-slate-600">{{ number_format((float) $usage['jumlah'], 2, ',', '.') }} {{ $usage['satuan'] }}</span><span class="font-bold text-slate-900">Rp {{ number_format((float) $usage['biaya'], 0, ',', '.') }}</span></div>
                                                        <p class="mt-2 break-words text-xs text-slate-500">Bukti: {{ $usage['nomor_bukti'] ?: '-' }}</p>
                                                        @if($usage['keterangan'])<p class="mt-1 break-words text-xs text-slate-600">{{ $usage['keterangan'] }}</p>@endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </section>
                                </div>
                            </details>
                        </article>
                    @endforeach
                </div>
                <p class="text-xs leading-relaxed text-slate-500">Idle berarti mesin menyala saat kendaraan berhenti. Hanya jeda antartitik GPS maksimal 30 menit yang dihitung. Data idle lama mungkin belum tersedia jika riwayat GPS telah dihapus.</p>
                <p class="text-xs leading-relaxed text-slate-500">Pergantian Ban &amp; Velg mencakup Ban, Ban Dalam, Ban Perut, Ring Velg, dan Velg berdasarkan riwayat pemakaian. Jumlah kali menunjukkan banyaknya transaksi; jumlah unit mengikuti kuantitas barang. Pemakaian dihitung berdasarkan kendaraan dan periode tanggal, termasuk jika penerimanya berbeda dari supir saat ini.</p>
            </section>

            </div>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="harian-title">
                <div class="mb-5 flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><i class="fas fa-calendar-day" aria-hidden="true"></i></span><div><h3 id="harian-title" class="font-bold text-slate-900">Jarak tempuh harian</h3><p class="text-sm text-slate-500">Total seluruh kendaraan pada setiap tanggal.</p></div></div>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($dailyTotals as $day)
                        <div class="min-w-0 rounded-xl border border-slate-100 bg-slate-50 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-2"><div><p class="text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}</p><p class="text-xs text-slate-500">{{ $day->vehicle_count }} kendaraan</p></div><p class="text-sm font-bold text-indigo-700">{{ number_format((float) $day->total_km, 2, ',', '.') }} km</p></div>
                            <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-slate-200"><div class="h-full rounded-full bg-blue-500" style="width: {{ $maxDailyDistance > 0 ? round((float) $day->total_km / $maxDailyDistance * 100, 1) : 0 }}%"></div></div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    @endif
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const chartVariable = document.getElementById('ranking-chart-variable');
    chartVariable?.addEventListener('change', () => {
        document.querySelectorAll('[data-ranking-metric]').forEach(panel => {
            panel.hidden = panel.dataset.rankingMetric !== chartVariable.value;
        });
    });
    const reportTabs = Array.from(document.querySelectorAll('.vehicle-report-tab'));
    function activateReportTab(selectedTab) {
        reportTabs.forEach(tab => {
            const active = tab === selectedTab;
            tab.setAttribute('aria-selected', String(active));
            tab.tabIndex = active ? 0 : -1;
            document.getElementById(tab.getAttribute('aria-controls')).hidden = !active;
        });
    }
    reportTabs.forEach((tab, index) => {
        tab.addEventListener('click', () => activateReportTab(tab));
        tab.addEventListener('keydown', event => {
            let nextIndex;
            if (event.key === 'ArrowRight') nextIndex = (index + 1) % reportTabs.length;
            else if (event.key === 'ArrowLeft') nextIndex = (index - 1 + reportTabs.length) % reportTabs.length;
            else if (event.key === 'Home') nextIndex = 0;
            else if (event.key === 'End') nextIndex = reportTabs.length - 1;
            else return;
            event.preventDefault();
            activateReportTab(reportTabs[nextIndex]);
            reportTabs[nextIndex].focus();
        });
    });

    document.querySelectorAll('.ai-ranking-explain').forEach(button => {
        button.addEventListener('click', async () => {
            const result = button.nextElementSibling;
            button.disabled = true;
            button.textContent = 'AI sedang menyusun penjelasan…';
            result.classList.remove('hidden');
            result.textContent = '';
            try {
                const response = await fetch(@json(route('dashboard-perjalanan-kendaraan.explain-ranking')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: button.dataset.payload
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Penjelasan AI gagal dibuat.');
                result.textContent = data.explanation;
                button.textContent = 'Perbarui penjelasan AI';
            } catch (error) {
                result.textContent = error.message;
                button.textContent = 'Coba lagi';
            } finally {
                button.disabled = false;
            }
        });
    });
});
</script>
@endsection
