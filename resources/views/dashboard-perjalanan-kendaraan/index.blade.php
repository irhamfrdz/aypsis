@extends('layouts.app')

@section('title', 'Dashboard Perjalanan Kendaraan')
@section('page_title', 'Dashboard Perjalanan Kendaraan')

@section('content')
<div class="mx-auto max-w-[1440px] space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    <header class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-800 via-indigo-700 to-blue-600 px-6 py-8 text-white shadow-lg shadow-indigo-900/10 sm:px-8 sm:py-9">
        <div class="pointer-events-none absolute -right-12 -top-24 h-64 w-64 rounded-full border border-white/10 bg-white/5"></div>
        <div class="pointer-events-none absolute bottom-[-90px] right-28 h-48 w-48 rounded-full border border-white/10"></div>
        <div class="relative flex items-start gap-4 sm:items-center">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20 sm:h-14 sm:w-14"><i class="fas fa-route text-xl sm:text-2xl"></i></span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-200">Pemantauan Armada</p>
                <h1 class="mt-1 text-xl font-bold tracking-tight sm:text-3xl">Dashboard Perjalanan Kendaraan</h1>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-indigo-100">Pantau jarak tempuh GPS dan pekerjaan rit setiap kendaraan dalam periode yang Anda pilih.</p>
            </div>
        </div>
    </header>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7" aria-labelledby="periode-title">
        <div class="mb-6 flex items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><i class="far fa-calendar-alt"></i></span>
            <div>
                <h2 id="periode-title" class="text-base font-bold text-slate-900 sm:text-lg">Pilih rentang tanggal</h2>
                <p class="mt-1 text-sm text-slate-500">Tentukan tanggal awal dan akhir untuk menampilkan perjalanan kendaraan.</p>
            </div>
        </div>

        @if($errors->any())
            <div role="alert" class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="GET" action="{{ route('dashboard-perjalanan-kendaraan.index') }}" class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto]">
            <div>
                <label for="tanggal_dari" class="mb-2 block text-sm font-semibold text-slate-700">Dari Tanggal <span class="text-rose-500">*</span></label>
                <input id="tanggal_dari" name="tanggal_dari" type="date" required value="{{ old('tanggal_dari', $tanggalDari) }}" class="block h-12 w-full rounded-xl border-slate-300 bg-slate-50 px-4 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
            </div>
            <div>
                <label for="tanggal_sampai" class="mb-2 block text-sm font-semibold text-slate-700">Sampai Tanggal <span class="text-rose-500">*</span></label>
                <input id="tanggal_sampai" name="tanggal_sampai" type="date" required min="{{ old('tanggal_dari', $tanggalDari) }}" value="{{ old('tanggal_sampai', $tanggalSampai) }}" class="block h-12 w-full rounded-xl border-slate-300 bg-slate-50 px-4 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
            </div>
            <button type="submit" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 text-sm font-semibold text-white shadow-sm shadow-indigo-600/20 transition hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-span-2 xl:col-span-1 xl:w-auto">
                <i class="fas fa-chart-line" aria-hidden="true"></i> Tampilkan Dashboard
            </button>
        </form>
    </section>

    @if(!$hasReport)
        <div class="rounded-2xl border border-dashed border-indigo-200 bg-gradient-to-b from-indigo-50 to-white px-6 py-14 text-center">
            <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-indigo-500 shadow-sm ring-1 ring-indigo-100"><i class="far fa-calendar-alt text-2xl" aria-hidden="true"></i></span>
            <h2 class="mt-5 text-lg font-bold text-slate-900">Mulai dengan memilih periode</h2>
            <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-slate-600">Pilih tanggal awal dan akhir, lalu tampilkan dashboard untuk melihat jarak tempuh serta rit kendaraan.</p>
        </div>
    @else
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Ringkasan Perjalanan</p>
                <h2 class="mt-1 text-xl font-bold text-slate-900">{{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}</h2>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-2 text-xs text-slate-600">Jarak berdasarkan titik GPS yang tersimpan</span>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-indigo-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-2"><p class="text-sm font-medium text-slate-600">Total Jarak Tempuh</p><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><i class="fas fa-route" aria-hidden="true"></i></span></div>
                <p class="mt-4 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ number_format($totalDistance, 2, ',', '.') }} <span class="text-base font-medium text-slate-500">km</span></p>
            </div>
            <div class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-2"><p class="text-sm font-medium text-slate-600">Kendaraan Tercatat</p><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><i class="fas fa-truck" aria-hidden="true"></i></span></div>
                <p class="mt-4 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ number_format($vehicleStats->count(), 0, ',', '.') }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-2"><p class="text-sm font-medium text-slate-600">Hari dengan Data GPS</p><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fas fa-calendar-check" aria-hidden="true"></i></span></div>
                <p class="mt-4 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ number_format($dailyTotals->count(), 0, ',', '.') }}</p>
            </div>
            <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-2"><p class="text-sm font-medium text-slate-600">Rata-rata per Hari</p><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><i class="fas fa-chart-line" aria-hidden="true"></i></span></div>
                <p class="mt-4 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ number_format($dailyTotals->count() ? $totalDistance / $dailyTotals->count() : 0, 2, ',', '.') }} <span class="text-base font-medium text-slate-500">km</span></p>
            </div>
        </div>

        @if($vehicleStats->isEmpty())
            <div class="rounded-2xl border border-slate-200 bg-white px-6 py-12 text-center shadow-sm">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fas fa-truck text-xl" aria-hidden="true"></i></span>
                <p class="mt-4 font-semibold text-slate-800">Belum ada data jarak tempuh pada rentang tanggal ini.</p>
                <p class="mt-1 text-sm text-slate-500">Pilih periode lain untuk melihat data GPS yang tersimpan.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 2xl:grid-cols-5">
                <section class="min-w-0 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 2xl:col-span-3" aria-labelledby="kendaraan-title">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><i class="fas fa-truck" aria-hidden="true"></i></span>
                        <div>
                            <h3 id="kendaraan-title" class="text-lg font-bold text-slate-900">Jarak Tempuh per Kendaraan</h3>
                            <p class="mt-1 text-sm leading-relaxed text-slate-500">Diurutkan dari jarak terjauh. Klik nama kendaraan untuk melihat detail rit.</p>
                        </div>
                    </div>
                    <div class="mt-5 overflow-x-auto rounded-xl border border-slate-100">
                        <table class="min-w-[620px] w-full divide-y divide-slate-100 text-sm">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Kendaraan</th>
                                    <th class="px-4 py-3 text-center">Hari GPS</th>
                                    <th class="px-4 py-3 text-center">Hari Bergerak</th>
                                    <th class="px-4 py-3 text-center">Rit</th>
                                    <th class="px-4 py-3 text-right">Jarak</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($vehicleStats as $row)
                                    <tr class="transition-colors hover:bg-slate-50/70">
                                        <td class="px-4 py-3">
                                            <button type="button" class="group flex w-full items-start justify-between gap-3 text-left focus-visible:rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500" data-rit-toggle aria-expanded="false" aria-controls="rit-details-{{ $row->mobil_id }}">
                                                <span>
                                                    <span class="block font-bold text-slate-900 group-hover:text-indigo-700">{{ $row->mobil?->nomor_polisi ?: 'Kendaraan #'.$row->mobil_id }}</span>
                                                    <span class="mt-0.5 block text-xs text-slate-500">{{ $row->mobil?->merek ?: '-' }} · {{ $row->mobil?->karyawan?->nama_panggilan ?: ($row->mobil?->karyawan?->nama_lengkap ?: 'Supir tidak tercatat') }}</span>
                                                </span>
                                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-slate-100 text-slate-500 group-hover:bg-indigo-50 group-hover:text-indigo-600"><i class="fas fa-chevron-down text-[10px] transition-transform" aria-hidden="true"></i></span>
                                            </button>
                                            @php
                                                $distancePercent = $maxVehicleDistance > 0 ? round((float) $row->total_km / $maxVehicleDistance * 100, 1) : 0;
                                                $distancePercentLabel = number_format($distancePercent, 1, ',', '.').'%';
                                            @endphp
                                            <div class="relative mt-3 h-5 w-full rounded-full bg-slate-100" role="meter" aria-label="Jarak dibanding kendaraan dengan jarak terjauh" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $distancePercent }}" title="{{ $distancePercentLabel }} dari jarak kendaraan terjauh">
                                                <div class="flex h-5 items-center justify-center rounded-full bg-indigo-500" style="width: {{ $distancePercent }}%">
                                                    @if($distancePercent >= 25)
                                                        <span class="text-[10px] font-bold leading-none text-white">{{ $distancePercentLabel }}</span>
                                                    @endif
                                                </div>
                                                @if($distancePercent < 25)
                                                    <span class="absolute inset-y-0 flex items-center pl-2 text-[10px] font-bold leading-none text-indigo-700" style="left: {{ $distancePercent }}%">{{ $distancePercentLabel }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center text-slate-600">{{ $row->days_with_data }}</td>
                                        <td class="px-4 py-3 text-center text-slate-600">{{ $row->moving_days }}</td>
                                        <td class="px-4 py-3 text-center"><span class="inline-flex min-w-8 justify-center rounded-full bg-indigo-50 px-2 py-1 text-xs font-bold text-indigo-700">{{ number_format($row->rit_count, 0, ',', '.') }}</span></td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right font-bold text-indigo-700">{{ number_format((float) $row->total_km, 2, ',', '.') }} km</td>
                                    </tr>
                                    <tr id="rit-details-{{ $row->mobil_id }}" class="hidden bg-indigo-50/60">
                                        <td colspan="5" class="px-4 py-5">
                                            <p class="mb-3 text-sm font-bold text-indigo-950">Detail Rit · {{ $row->mobil?->nomor_polisi ?: 'Kendaraan #'.$row->mobil_id }} <span class="ml-1 rounded-full bg-white px-2 py-1 text-xs font-semibold text-indigo-700">{{ number_format($row->rit_count, 0, ',', '.') }} rit</span></p>
                                            @if($row->rit_details->isEmpty())
                                                <p class="text-xs text-gray-600">Tidak ada surat jalan rit untuk kendaraan ini pada periode yang dipilih.</p>
                                            @else
                                                <div class="overflow-x-auto rounded-xl border border-indigo-100 bg-white shadow-sm">
                                                    <table class="min-w-[760px] w-full divide-y divide-slate-100 text-xs">
                                                        <thead class="bg-indigo-50/70 text-left font-semibold text-slate-600">
                                                            <tr>
                                                                <th class="px-3 py-2">Tanggal</th>
                                                                <th class="px-3 py-2">No. Surat Jalan</th>
                                                                <th class="px-3 py-2">Jenis</th>
                                                                <th class="px-3 py-2">Kegiatan</th>
                                                                <th class="px-3 py-2">Supir</th>
                                                                <th class="px-3 py-2">Pengirim</th>
                                                                <th class="px-3 py-2">Tujuan Ambil</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-slate-100 text-slate-700">
                                                            @foreach($row->rit_details as $detail)
                                                                <tr>
                                                                    <td class="whitespace-nowrap px-3 py-2">{{ $detail['tanggal'] }}</td>
                                                                    <td class="px-3 py-2 font-medium">{{ $detail['nomor'] ?: '-' }}</td>
                                                                    <td class="px-3 py-2">{{ $detail['jenis'] }}</td>
                                                                    <td class="px-3 py-2">{{ ucfirst($detail['kegiatan'] ?: '-') }}</td>
                                                                    <td class="px-3 py-2">{{ $detail['supir'] ?: '-' }}</td>
                                                                    <td class="px-3 py-2">{{ $detail['pengirim'] ?: '-' }}</td>
                                                                    <td class="px-3 py-2">{{ $detail['tujuan_ambil'] ?: '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="min-w-0 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 2xl:col-span-2" aria-labelledby="harian-title">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><i class="fas fa-calendar-day" aria-hidden="true"></i></span>
                        <div>
                            <h3 id="harian-title" class="text-lg font-bold text-slate-900">Jarak Tempuh Harian</h3>
                            <p class="mt-1 text-sm text-slate-500">Total semua kendaraan pada tanggal yang memiliki data GPS.</p>
                        </div>
                    </div>
                    <div class="mt-5 max-h-[520px] space-y-3 overflow-y-auto pr-1">
                        @foreach($dailyTotals as $day)
                            <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3">
                                <div class="mb-1 flex items-center justify-between gap-3 text-xs">
                                    <span class="font-semibold text-slate-700">{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }} <span class="font-normal text-slate-400">· {{ $day->vehicle_count }} kendaraan</span></span>
                                    <span class="whitespace-nowrap font-bold text-indigo-700">{{ number_format((float) $day->total_km, 2, ',', '.') }} km</span>
                                </div>
                                <div class="mt-3 h-2 w-full rounded-full bg-slate-200"><div class="h-2 rounded-full bg-blue-500" style="width: {{ $maxDailyDistance > 0 ? round((float) $day->total_km / $maxDailyDistance * 100, 1) : 0 }}%"></div></div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-rit-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const details = document.getElementById(button.getAttribute('aria-controls'));
        const isOpen = button.getAttribute('aria-expanded') === 'true';
        details.classList.toggle('hidden', isOpen);
        button.setAttribute('aria-expanded', String(!isOpen));
        button.querySelector('i').classList.toggle('rotate-180', !isOpen);
    });
});
</script>
@endpush
