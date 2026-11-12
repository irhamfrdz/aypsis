@extends('layouts.app')

@section('title', 'Report Kontainer In/Out - '.$namaGudang)
@section('page_title', 'Report Kontainer In/Out')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 px-3 py-6 sm:px-4 sm:py-10 lg:px-8">
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex flex-col justify-between gap-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:flex-row sm:items-center">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-purple-600">Report Pergerakan Kontainer</p>
                <h1 class="mt-1 text-xl font-bold text-gray-900">{{ $namaGudang }}</h1>
                <p class="mt-1 text-sm text-gray-500">Tanggal {{ $tanggal->format('d/m/Y') }}</p>
            </div>
            <a href="{{ route('master.kontainer.stock-pergudang.show', ['id' => $id, 'type' => $type]) }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali ke Detail Gudang
            </a>
        </div>

        @foreach([
            ['title' => 'Kontainer Masuk', 'rows' => $incoming, 'color' => 'green'],
            ['title' => 'Kontainer Keluar', 'rows' => $outgoing, 'color' => 'rose'],
        ] as $report)
            <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 bg-slate-50 px-5 py-4">
                    <h2 class="font-bold text-gray-900">{{ $report['title'] }}</h2>
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-gray-700">{{ $report['rows']->count() }} kontainer</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">No.</th>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">No. Kontainer</th>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Tipe</th>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Kegiatan</th>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Keterangan</th>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Riwayat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($report['rows'] as $history)
                                @php
                                    $isOutgoing = $report['title'] === 'Kontainer Keluar';
                                    $isIncomingMovement = strtolower(trim((string) $history->jenis_kegiatan)) === 'masuk';
                                    $kegiatan = $isOutgoing && $isIncomingMovement ? 'Keluar' : $history->jenis_kegiatan;
                                    $keterangan = $history->keterangan ?: '-';
                                    if ($isOutgoing) {
                                        $keterangan = preg_replace('/^Masuk via Tanda Terima/i', 'Keluar via Tanda Terima', $keterangan);
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-5 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-sm font-bold text-gray-900">{{ $history->nomor_kontainer }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-sm text-gray-600">{{ ucfirst($history->tipe_kontainer ?? '-') }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-sm text-gray-600">{{ $kegiatan }}</td>
                                    <td class="min-w-64 px-5 py-3 text-sm text-gray-600">{{ $keterangan }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-sm"><a href="{{ route('history-kontainer.index', ['search' => $history->nomor_kontainer]) }}" class="font-semibold text-indigo-600 hover:underline">Lihat</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500">Tidak ada kontainer {{ strtolower($report['title']) }} pada tanggal ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endforeach
    </div>
</div>
@endsection
