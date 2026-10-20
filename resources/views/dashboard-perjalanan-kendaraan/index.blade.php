@extends('layouts.app')

@section('title', 'Dashboard Perjalanan Kendaraan')
@section('page_title', 'Dashboard Perjalanan Kendaraan')

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    <div class="rounded-2xl bg-gradient-to-r from-indigo-700 to-blue-600 px-6 py-7 text-white shadow-sm">
        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/15"><i class="fas fa-route text-xl"></i></span>
            <div>
                <h1 class="text-2xl font-bold">Dashboard Perjalanan Kendaraan</h1>
                <p class="mt-1 text-sm text-indigo-100">Lihat jarak tempuh armada berdasarkan data GPS dalam periode pilihan Anda.</p>
            </div>
        </div>
    </div>

    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm" aria-labelledby="periode-title">
        <div class="mb-5">
            <h2 id="periode-title" class="text-lg font-semibold text-gray-900">Pilih rentang tanggal</h2>
            <p class="mt-1 text-sm text-gray-500">Tentukan tanggal awal dan akhir untuk menampilkan perjalanan kendaraan.</p>
        </div>

        @if($errors->any())
            <div role="alert" class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="GET" action="{{ route('dashboard-perjalanan-kendaraan.index') }}" class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_auto]">
            <div>
                <label for="tanggal_dari" class="mb-1 block text-sm font-medium text-gray-700">Dari Tanggal <span class="text-red-500">*</span></label>
                <input id="tanggal_dari" name="tanggal_dari" type="date" required value="{{ old('tanggal_dari', $tanggalDari) }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="tanggal_sampai" class="mb-1 block text-sm font-medium text-gray-700">Sampai Tanggal <span class="text-red-500">*</span></label>
                <input id="tanggal_sampai" name="tanggal_sampai" type="date" required min="{{ old('tanggal_dari', $tanggalDari) }}" value="{{ old('tanggal_sampai', $tanggalSampai) }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-semibold text-white hover:bg-indigo-700">
                <i class="fas fa-chart-line"></i> Tampilkan Dashboard
            </button>
        </form>
    </section>

    @if(!$hasReport)
        <div class="rounded-2xl border border-dashed border-indigo-200 bg-indigo-50 px-6 py-12 text-center">
            <i class="fas fa-calendar-alt text-3xl text-indigo-400"></i>
            <h2 class="mt-4 text-lg font-semibold text-gray-900">Mulai dengan memilih periode</h2>
            <p class="mt-2 text-sm text-gray-600">Ringkasan jarak tempuh akan muncul setelah Anda memilih kedua tanggal.</p>
        </div>
    @else
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-lg font-semibold text-gray-900">Hasil {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}</h2>
            <span class="text-xs text-gray-500">Jarak perkiraan dari titik GPS tersimpan; hari tanpa data GPS tidak dihitung.</span>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-5">
                <p class="text-sm text-indigo-700">Total Jarak Tempuh</p>
                <p class="mt-2 text-3xl font-bold text-indigo-950">{{ number_format($totalDistance, 2, ',', '.') }} <span class="text-base font-medium">km</span></p>
            </div>
            <div class="rounded-xl border border-blue-100 bg-blue-50 p-5">
                <p class="text-sm text-blue-700">Kendaraan Tercatat</p>
                <p class="mt-2 text-3xl font-bold text-blue-950">{{ number_format($vehicleStats->count(), 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-5">
                <p class="text-sm text-emerald-700">Hari dengan Data GPS</p>
                <p class="mt-2 text-3xl font-bold text-emerald-950">{{ number_format($dailyTotals->count(), 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-amber-100 bg-amber-50 p-5">
                <p class="text-sm text-amber-700">Rata-rata per Hari Tercatat</p>
                <p class="mt-2 text-3xl font-bold text-amber-950">{{ number_format($dailyTotals->count() ? $totalDistance / $dailyTotals->count() : 0, 2, ',', '.') }} <span class="text-base font-medium">km</span></p>
            </div>
        </div>

        @if($vehicleStats->isEmpty())
            <div class="rounded-xl border border-gray-200 bg-white px-6 py-12 text-center shadow-sm">
                <i class="fas fa-truck text-3xl text-gray-300"></i>
                <p class="mt-4 font-semibold text-gray-800">Belum ada data jarak tempuh pada rentang tanggal ini.</p>
                <p class="mt-1 text-sm text-gray-500">Pilih periode lain untuk melihat data GPS yang tersimpan.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-5">
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm xl:col-span-3" aria-labelledby="kendaraan-title">
                    <h3 id="kendaraan-title" class="text-lg font-semibold text-gray-900">Jarak Tempuh per Kendaraan</h3>
                    <p class="mt-1 text-sm text-gray-500">Diurutkan dari jarak terjauh pada periode ini. Klik nama kendaraan untuk melihat detail rit dari surat jalan sesuai periode Report Rit.</p>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-3 py-3">Kendaraan</th>
                                    <th class="px-3 py-3 text-center">Hari GPS</th>
                                    <th class="px-3 py-3 text-center">Hari Bergerak</th>
                                    <th class="px-3 py-3 text-center">Rit</th>
                                    <th class="px-3 py-3 text-right">Jarak</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($vehicleStats as $row)
                                    <tr>
                                        <td class="px-3 py-3">
                                            <button type="button" class="group flex w-full items-start justify-between gap-2 text-left focus-visible:rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500" data-rit-toggle aria-expanded="false" aria-controls="rit-details-{{ $row->mobil_id }}">
                                                <span>
                                                    <span class="block font-semibold text-gray-900 group-hover:text-indigo-700">{{ $row->mobil?->nomor_polisi ?: 'Kendaraan #'.$row->mobil_id }}</span>
                                                    <span class="block text-xs text-gray-500">{{ $row->mobil?->merek ?: '-' }} · {{ $row->mobil?->karyawan?->nama_panggilan ?: ($row->mobil?->karyawan?->nama_lengkap ?: 'Supir tidak tercatat') }}</span>
                                                </span>
                                                <i class="fas fa-chevron-down mt-1 text-xs text-gray-400 transition-transform" aria-hidden="true"></i>
                                            </button>
                                            <div class="mt-2 h-1.5 w-full rounded-full bg-gray-100"><div class="h-1.5 rounded-full bg-indigo-500" style="width: {{ $maxVehicleDistance > 0 ? round((float) $row->total_km / $maxVehicleDistance * 100, 1) : 0 }}%"></div></div>
                                        </td>
                                        <td class="px-3 py-3 text-center text-gray-600">{{ $row->days_with_data }}</td>
                                        <td class="px-3 py-3 text-center text-gray-600">{{ $row->moving_days }}</td>
                                        <td class="px-3 py-3 text-center font-semibold text-gray-900">{{ number_format($row->rit_count, 0, ',', '.') }}</td>
                                        <td class="px-3 py-3 text-right font-semibold text-indigo-700 whitespace-nowrap">{{ number_format((float) $row->total_km, 2, ',', '.') }} km</td>
                                    </tr>
                                    <tr id="rit-details-{{ $row->mobil_id }}" class="hidden bg-indigo-50/50">
                                        <td colspan="5" class="px-3 py-4">
                                            <p class="mb-3 text-xs font-semibold text-indigo-900">Detail Rit · {{ $row->mobil?->nomor_polisi ?: 'Kendaraan #'.$row->mobil_id }} · {{ number_format($row->rit_count, 0, ',', '.') }} rit</p>
                                            @if($row->rit_details->isEmpty())
                                                <p class="text-xs text-gray-600">Tidak ada surat jalan rit untuk kendaraan ini pada periode yang dipilih.</p>
                                            @else
                                                <div class="overflow-x-auto rounded-lg border border-indigo-100 bg-white">
                                                    <table class="min-w-full divide-y divide-gray-100 text-xs">
                                                        <thead class="bg-indigo-50 text-left text-gray-600">
                                                            <tr>
                                                                <th class="px-3 py-2">Tanggal</th>
                                                                <th class="px-3 py-2">No. Surat Jalan</th>
                                                                <th class="px-3 py-2">Jenis</th>
                                                                <th class="px-3 py-2">Kegiatan</th>
                                                                <th class="px-3 py-2">Supir</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-100">
                                                            @foreach($row->rit_details as $detail)
                                                                <tr>
                                                                    <td class="whitespace-nowrap px-3 py-2">{{ $detail['tanggal'] }}</td>
                                                                    <td class="px-3 py-2 font-medium">{{ $detail['nomor'] ?: '-' }}</td>
                                                                    <td class="px-3 py-2">{{ $detail['jenis'] }}</td>
                                                                    <td class="px-3 py-2">{{ ucfirst($detail['kegiatan'] ?: '-') }}</td>
                                                                    <td class="px-3 py-2">{{ $detail['supir'] ?: '-' }}</td>
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

                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm xl:col-span-2" aria-labelledby="harian-title">
                    <h3 id="harian-title" class="text-lg font-semibold text-gray-900">Jarak Tempuh Harian</h3>
                    <p class="mt-1 text-sm text-gray-500">Total semua kendaraan pada tanggal yang memiliki data GPS.</p>
                    <div class="mt-4 max-h-[520px] space-y-4 overflow-y-auto pr-1">
                        @foreach($dailyTotals as $day)
                            <div>
                                <div class="mb-1 flex items-center justify-between gap-3 text-xs">
                                    <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }} <span class="text-gray-400">· {{ $day->vehicle_count }} kendaraan</span></span>
                                    <span class="whitespace-nowrap font-semibold text-indigo-700">{{ number_format((float) $day->total_km, 2, ',', '.') }} km</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-gray-100"><div class="h-2 rounded-full bg-blue-500" style="width: {{ $maxDailyDistance > 0 ? round((float) $day->total_km / $maxDailyDistance * 100, 1) : 0 }}%"></div></div>
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
