@extends('layouts.app')

@section('title', 'Surat Jalan Bongkaran Tanjung Pinang')

@section('content')
<div class="min-h-screen bg-slate-50 px-4 py-5 md:px-6">
    <div class="mx-auto max-w-screen-2xl space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="text-xs font-semibold uppercase tracking-[.18em] text-teal-700">Operasional · Tanjung Pinang</div>
                <h1 class="mt-1 text-2xl font-bold text-slate-900">Surat Jalan Bongkaran</h1>
                <p class="mt-1 text-sm text-slate-500">Kelola dan cetak surat jalan berdasarkan kapal dan voyage.</p>
            </div>
            @can('surat-jalan-bongkaran-tanjung-pinang-create')
                <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-teal-800">
                    <i class="fas fa-plus"></i> Tambah Surat Jalan
                </a>
            @endcan
        </div>

        @if(session('success'))<div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>@endif

        <div class="grid gap-3 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><div class="text-sm text-slate-500">Surat jalan ditemukan</div><div class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($stats['total']) }}</div></div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><div class="text-sm text-slate-500">Dengan nomor kontainer</div><div class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($stats['kontainer']) }}</div></div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><div class="text-sm text-slate-500">Voyage ditemukan</div><div class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($stats['voyage']) }}</div></div>
        </div>

        <form method="GET" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 md:grid-cols-[minmax(220px,1fr)_220px_200px_auto_auto] md:items-end">
                <div><label for="search" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Pencarian</label><input id="search" type="search" name="search" value="{{ $search }}" placeholder="No. SJ, BL, kontainer, atau supir" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-600 focus:ring-teal-600"></div>
                <div><label for="nama_kapal" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Kapal</label><select id="nama_kapal" name="nama_kapal" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-600 focus:ring-teal-600"><option value="">Semua kapal</option>@foreach($kapals as $kapal)<option value="{{ $kapal }}" @selected(request('nama_kapal') === $kapal)>{{ $kapal }}</option>@endforeach</select></div>
                <div><label for="no_voyage" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Voyage</label><select id="no_voyage" name="no_voyage" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-600 focus:ring-teal-600"><option value="">Semua voyage</option>@foreach($voyages as $voyage)<option value="{{ $voyage }}" @selected(request('no_voyage') === $voyage)>{{ $voyage }}</option>@endforeach</select></div>
                <button class="rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-900"><i class="fas fa-search mr-1"></i> Cari</button>
                @if($search || request('nama_kapal') || request('no_voyage'))<a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>@endif
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-4 py-3">
                <h2 class="font-semibold text-slate-800">Daftar surat jalan</h2><span class="text-xs text-slate-500">{{ $suratJalans->total() }} data · halaman {{ $suratJalans->currentPage() }} dari {{ max(1, $suratJalans->lastPage()) }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-[1150px] w-full text-left text-sm">
                    <thead class="bg-slate-800 text-xs uppercase tracking-wide text-white"><tr>
                        <th class="px-4 py-3">Tanggal / No. SJ</th><th class="px-4 py-3">Kapal / Voyage</th><th class="px-4 py-3">BL / Kontainer</th><th class="px-4 py-3">Pengirim / Penerima</th><th class="px-4 py-3">Supir / Plat</th><th class="px-4 py-3">Tujuan</th><th class="px-4 py-3 text-right">Aksi</th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse($suratJalans as $suratJalan)
                        <tr class="hover:bg-teal-50/40">
                            <td class="px-4 py-3"><div class="font-medium text-slate-800">{{ $suratJalan->tanggal_surat_jalan?->format('d/m/Y') ?? '—' }}</div><div class="mt-0.5 text-xs text-slate-500">{{ $suratJalan->nomor_surat_jalan }}</div></td>
                            <td class="px-4 py-3"><div class="font-medium text-slate-800">{{ $suratJalan->nama_kapal ?: '—' }}</div><div class="text-xs text-slate-500">{{ $suratJalan->no_voyage ?: 'Voyage —' }}</div></td>
                            <td class="px-4 py-3"><div>{{ $suratJalan->no_bl ?: 'BL —' }}</div><div class="text-xs text-slate-500">{{ $suratJalan->no_kontainer ?: 'Kontainer —' }}{{ $suratJalan->size ? ' · '.$suratJalan->size : '' }}</div></td>
                            <td class="px-4 py-3"><div class="max-w-[230px] truncate text-slate-700" title="{{ $suratJalan->pengirim }}">{{ $suratJalan->pengirim ?: '—' }}</div><div class="max-w-[230px] truncate text-xs text-slate-500" title="{{ $suratJalan->penerima }}">{{ $suratJalan->penerima ?: '—' }}</div></td>
                            <td class="px-4 py-3"><div>{{ $suratJalan->supir ?: '—' }}</div><div class="text-xs text-slate-500">{{ $suratJalan->no_plat ?: 'Plat —' }}</div></td>
                            <td class="px-4 py-3">{{ $suratJalan->tujuan_pengiriman ?: '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.print', $suratJalan->id) }}" target="_blank" class="rounded-md px-2 py-1 text-teal-700 hover:bg-teal-50" title="Cetak"><i class="fas fa-print"></i></a>
                                @can('surat-jalan-bongkaran-tanjung-pinang-update')<a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.edit', $suratJalan->id) }}" class="rounded-md px-2 py-1 text-blue-700 hover:bg-blue-50" title="Ubah"><i class="fas fa-pen"></i></a>@endcan
                                @can('surat-jalan-bongkaran-tanjung-pinang-delete')<form method="POST" action="{{ route('surat-jalan-bongkaran-tanjung-pinang.destroy', $suratJalan->id) }}" class="inline" onsubmit="return confirm('Hapus surat jalan ini?')">@csrf @method('DELETE')<button class="rounded-md px-2 py-1 text-red-700 hover:bg-red-50" title="Hapus"><i class="fas fa-trash"></i></button></form>@endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center"><div class="text-slate-400"><i class="fas fa-file-circle-xmark text-3xl"></i></div><div class="mt-2 font-medium text-slate-700">Belum ada surat jalan</div><div class="mt-1 text-sm text-slate-500">Ubah filter atau tambahkan surat jalan bongkaran Tanjung Pinang.</div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($suratJalans->hasPages())<div class="border-t border-slate-200 px-4 py-3">{{ $suratJalans->links() }}</div>@endif
        </div>
    </div>
</div>
@endsection
