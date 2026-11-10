@extends('layouts.app')

@section('title', 'Dashboard Perjalanan Kendaraan')
@section('page_title', 'Dashboard Perjalanan Kendaraan')

@section('content')
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

        <form method="GET" action="{{ route('dashboard-perjalanan-kendaraan.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-end">
            <div class="min-w-0">
                <label for="tanggal_dari" class="mb-2 block text-sm font-semibold text-slate-700">Dari tanggal</label>
                <input id="tanggal_dari" name="tanggal_dari" type="date" required value="{{ old('tanggal_dari', $tanggalDari) }}" class="block h-11 w-full rounded-xl border-slate-300 bg-slate-50 px-3 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
            </div>
            <div class="min-w-0">
                <label for="tanggal_sampai" class="mb-2 block text-sm font-semibold text-slate-700">Sampai tanggal</label>
                <input id="tanggal_sampai" name="tanggal_sampai" type="date" required min="{{ old('tanggal_dari', $tanggalDari) }}" value="{{ old('tanggal_sampai', $tanggalSampai) }}" class="block h-11 w-full rounded-xl border-slate-300 bg-slate-50 px-3 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
            </div>
            <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-span-2 lg:col-span-1">
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
                <p class="mt-1 text-sm text-slate-500">Coba pilih periode lain yang memiliki data GPS.</p>
            </div>
        @else
            <section class="space-y-4" aria-labelledby="ranking-title">
                <div>
                    <h3 id="ranking-title" class="text-lg font-bold text-slate-900">Ranking kendaraan per variabel</h3>
                    <p class="mt-1 max-w-4xl text-sm leading-relaxed text-slate-600">Setiap daftar mengurutkan kendaraan dari nilai tertinggi ke terendah pada periode terpilih. Ranking menunjukkan banyaknya aktivitas atau pemakaian yang tercatat; nilai tinggi bukan otomatis berarti kendaraan bermasalah. Durasi idle yang tinggi berarti mesin lebih lama menyala saat kendaraan berhenti. Kendaraan tanpa data untuk suatu variabel dapat muncul dengan nilai nol.</p>
                </div>
                <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-3">Kendaraan</th>
                                <th class="whitespace-nowrap px-4 py-3">Nama supir</th>
                                @foreach($rankings as $ranking)<th class="whitespace-nowrap px-4 py-3">{{ $ranking['label'] }}</th>@endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($vehicleStats as $row)
                                @php $vehicleLabel = $row->mobil?->nomor_polisi ?: 'Kendaraan #'.$row->mobil_id; @endphp
                                <tr class="hover:bg-slate-50">
                                    <th scope="row" class="whitespace-nowrap px-4 py-3 text-left font-semibold text-slate-800">{{ $vehicleLabel }}</th>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-700">{{ $row->mobil?->karyawan?->nama_panggilan ?: ($row->mobil?->karyawan?->nama_lengkap ?: 'Supir tidak tercatat') }}</td>
                                    @foreach($rankings as $ranking)
                                        @php $rankEntry = $ranking['rows']->firstWhere('vehicle', $vehicleLabel); @endphp
                                        <td class="whitespace-nowrap px-4 py-3 text-slate-700">
                                            @if($rankEntry)<span class="mr-1.5 inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-indigo-50 px-1.5 text-xs font-bold text-indigo-700">{{ $rankEntry['rank'] }}</span><span class="font-semibold">{{ number_format($rankEntry['value'], $ranking['unit'] === 'km' ? 2 : 0, ',', '.') }}</span><span class="text-slate-500"> {{ $ranking['unit'] }}</span>@else—@endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section aria-labelledby="kendaraan-title" class="space-y-4">
                <div class="flex flex-wrap items-end justify-between gap-2">
                    <div>
                        <h3 id="kendaraan-title" class="text-lg font-bold text-slate-900">Jarak tempuh per kendaraan</h3>
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
                                    <div class="rounded-xl bg-slate-50 px-3 py-3"><dt class="text-xs text-slate-500">Hari GPS</dt><dd class="mt-1 font-bold text-slate-900">{{ $row->days_with_data }} hari</dd></div>
                                    <div class="rounded-xl bg-slate-50 px-3 py-3"><dt class="text-xs text-slate-500">Hari bergerak</dt><dd class="mt-1 font-bold text-slate-900">{{ $row->moving_days }} hari</dd></div>
                                    <div class="rounded-xl bg-amber-50 px-3 py-3"><dt class="text-xs text-amber-800">Durasi idle</dt><dd class="mt-1 font-bold text-amber-900">{{ $row->days_with_idle_data > 0 ? intdiv($idleSeconds, 3600).' jam '.intdiv($idleSeconds % 3600, 60).' menit' : 'Belum tersedia' }}</dd>@if($row->days_with_idle_data > 0 && $row->days_with_idle_data < $row->days_with_data)<p class="mt-1 text-xs text-amber-700">{{ $row->days_with_idle_data }}/{{ $row->days_with_data }} hari</p>@endif</div>
                                    <div class="rounded-xl bg-slate-50 px-3 py-3"><dt class="text-xs text-slate-500">Rit</dt><dd class="mt-1 font-bold text-slate-900">{{ number_format($row->rit_count, 0, ',', '.') }}</dd></div>
                                    <div class="rounded-xl bg-slate-50 px-3 py-3"><dt class="text-xs text-slate-500">Pergantian Ban &amp; Velg</dt><dd class="mt-1 font-bold text-slate-900">{{ number_format($row->tire_replacement_count, 0, ',', '.') }} kali</dd><p class="text-xs text-slate-500">{{ number_format($row->tire_count, 0, ',', '.') }} unit</p></div>
                                    <div class="rounded-xl bg-slate-50 px-3 py-3"><dt class="text-xs text-slate-500">Barang / servis</dt><dd class="mt-1 font-bold text-slate-900">{{ number_format($row->amprahan_usages->count(), 0, ',', '.') }} kali</dd></div>
                                </dl>
                            </div>

                            <details class="group border-t border-slate-100 bg-slate-50/50">
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-5 py-4 text-sm font-semibold text-indigo-700 hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 sm:px-6 [&::-webkit-details-marker]:hidden">
                                    <span>Detail rit, ban &amp; velg, dan barang / servis</span>
                                    <i class="fas fa-chevron-down text-xs transition-transform group-open:rotate-180" aria-hidden="true"></i>
                                </summary>
                                <div class="space-y-7 border-t border-slate-100 px-5 py-5 sm:px-6">
                                    <section aria-label="Detail rit">
                                        <div class="mb-3 flex flex-wrap items-center gap-2"><h5 class="font-bold text-slate-900">Detail rit</h5><span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-semibold text-indigo-700">{{ number_format($row->rit_count, 0, ',', '.') }} rit</span></div>
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
                                        <div class="mb-3 flex flex-wrap items-center gap-2"><h5 class="font-bold text-slate-900">Barang / servis dipakai</h5><span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">{{ number_format($row->amprahan_usages->count(), 0, ',', '.') }} kali</span></div>
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
@endsection
