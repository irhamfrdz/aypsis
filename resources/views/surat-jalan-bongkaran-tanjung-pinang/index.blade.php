@extends('layouts.app')

@section('title', 'Surat Jalan Bongkaran Tanjung Pinang')

@push('styles')
<style>
    body:has(.tp-bongkaran-page) { font-family:Arial,sans-serif;font-size:12px; }
    body:has(.tp-bongkaran-page) #page-content { background:#F5F7FA; }
    .tp-bongkaran-page { --tp-navy:#1E3A5F;--tp-line:#e1e6ed;font:12px/1.5 Arial,sans-serif;color:#344256; }
    .tp-bongkaran-page :is(p,span,a,button,input,select,label,table,th,td,div) { font-family:Arial,sans-serif;font-size:12px; }
    .tp-bongkaran-page h1 { color:var(--tp-navy);font:600 20px/1.4 Arial,sans-serif; }
    .tp-bongkaran-page h2 { color:var(--tp-navy);font:600 16px/1.4 Arial,sans-serif; }
    .tp-bongkaran-page .tp-panel { border:1px solid var(--tp-line);border-radius:8px;background:#fff;box-shadow:0 2px 6px rgb(30 58 95 / 4%); }
    .tp-bongkaran-page input { min-height:34px;padding:7px 10px;border:1px solid #d5dde7;border-radius:5px;font-size:12px; }
    .tp-bongkaran-page .tp-button { min-height:34px;padding:7px 12px;border:1px solid #cdd7e4;border-radius:5px;background:#fff;color:var(--tp-navy);font-weight:500;white-space:nowrap; }
    .tp-bongkaran-page .tp-button-primary { border-color:var(--tp-navy);background:var(--tp-navy);color:white; }
    .tp-bongkaran-page .tp-button:hover { background:#edf2f8; }
    .tp-bongkaran-page .tp-button-primary:hover { background:#2b4c75; }
    .tp-bongkaran-page table { width:100%;min-width:1120px;border-collapse:collapse; }
    .tp-bongkaran-page thead th { padding:10px 12px;border-bottom:1px solid var(--tp-line);background:#F5F7FA;color:#647184;font-weight:600;text-align:left; }
    .tp-bongkaran-page td { padding:10px 12px;border-bottom:1px solid var(--tp-line);vertical-align:middle; }
    .tp-bongkaran-page tbody tr:hover { background:#f8fafc; }
    .tp-bongkaran-page .tp-muted { color:#748091; }
</style>
@endpush

@section('content')
<div class="tp-bongkaran-page flex-1 p-4 md:p-6">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1>Surat Jalan Bongkaran Tanjung Pinang</h1>
            <nav class="mt-1 flex items-center gap-2 text-slate-500"><a href="{{ route('dashboard') }}" class="hover:text-blue-700">Dashboard</a><span>/</span><a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.index') }}" class="hover:text-blue-700">Surat Jalan Bongkaran</a><span>/</span><span>Data Kapal & Voyage</span></nav>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.index') }}" class="tp-button"><i class="fas fa-ship mr-1"></i> Ganti Kapal / Voyage</a>
            @can('surat-jalan-bongkaran-tanjung-pinang-create')
                <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.create', ['nama_kapal' => $selectedKapal, 'no_voyage' => $selectedVoyage]) }}" class="tp-button tp-button-primary"><i class="fas fa-plus mr-1"></i> Tambah Surat Jalan</a>
            @endcan
        </div>
    </div>

    @if(session('success'))<div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-800">{{ session('error') }}</div>@endif

    <div class="tp-panel mb-4 flex flex-wrap items-center gap-x-8 gap-y-2 px-4 py-3">
        <div><span class="tp-muted">Kapal:</span> <strong class="text-slate-800">{{ $selectedKapal }}</strong></div>
        <div><span class="tp-muted">Voyage:</span> <strong class="text-slate-800">{{ $selectedVoyage }}</strong></div>
        <div><span class="tp-muted">Total manifest:</span> <strong class="text-slate-800">{{ number_format($stats['manifest']) }}</strong></div>
        <div><span class="tp-muted">Sudah dibuat SJ:</span> <strong class="text-slate-800">{{ number_format($stats['sudah_sj']) }}</strong></div>
        <div><span class="tp-muted">Belum dibuat SJ:</span> <strong class="text-slate-800">{{ number_format($stats['belum_sj']) }}</strong></div>
    </div>

    <div class="tp-panel">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.list', ['nama_kapal' => $selectedKapal, 'no_voyage' => $selectedVoyage, 'mode' => 'manifest']) }}" class="tp-button {{ $mode === 'manifest' ? 'tp-button-primary' : '' }}">Data Manifest</a>
                <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.list', ['nama_kapal' => $selectedKapal, 'no_voyage' => $selectedVoyage, 'mode' => 'surat_jalan']) }}" class="tp-button {{ $mode === 'surat_jalan' ? 'tp-button-primary' : '' }}">Surat Jalan Tersimpan</a>
            </div>
            <form method="GET" action="{{ route('surat-jalan-bongkaran-tanjung-pinang.list') }}" class="flex flex-wrap gap-2">
                <input type="hidden" name="nama_kapal" value="{{ $selectedKapal }}"><input type="hidden" name="no_voyage" value="{{ $selectedVoyage }}"><input type="hidden" name="mode" value="{{ $mode }}">
                <input type="search" name="search" value="{{ $search }}" placeholder="Cari nomor SJ, BL, kontainer, supir" class="w-64 max-w-full">
                <button class="tp-button tp-button-primary"><i class="fas fa-search mr-1"></i> Cari</button>
                @if($search)<a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.list', ['nama_kapal' => $selectedKapal, 'no_voyage' => $selectedVoyage]) }}" class="tp-button">Reset</a>@endif
            </form>
        </div>
        <div class="overflow-x-auto">
            <table>
                @if($mode === 'manifest')
                <thead><tr><th>No. BL / Urut</th><th>Kontainer / Seal</th><th>Ukuran / Tipe</th><th>Pengirim</th><th>Penerima</th><th>Barang / Term</th><th>Status / Aksi</th></tr></thead>
                <tbody>
                @forelse($manifests as $manifest)
                    <tr>
                        <td><div class="font-medium text-slate-800">{{ $manifest->nomor_bl ?: '—' }}</div><div class="tp-muted mt-0.5">No. urut {{ $manifest->nomor_urut ?: '—' }}</div></td>
                        <td><div class="font-medium">{{ $manifest->nomor_kontainer ?: '—' }}</div><div class="tp-muted">Seal: {{ $manifest->no_seal ?: '—' }}</div></td>
                        <td><div>{{ $manifest->size_kontainer ?: '—' }}</div><div class="tp-muted">{{ $manifest->tipe_kontainer ?: '—' }}</div></td>
                        <td><div class="max-w-[200px] truncate" title="{{ $manifest->pengirim }}">{{ $manifest->pengirim ?: '—' }}</div></td>
                        <td><div class="max-w-[200px] truncate" title="{{ $manifest->penerima }}">{{ $manifest->penerima ?: '—' }}</div></td>
                        <td><div class="max-w-[230px] truncate" title="{{ $manifest->nama_barang }}">{{ $manifest->nama_barang ?: '—' }}</div><div class="tp-muted">Term: {{ $manifest->term ?: '—' }}</div></td>
                        <td class="whitespace-nowrap">
                            @if($manifest->suratJalanBongkaranTanjungPinang)
                                <span class="rounded bg-green-50 px-2 py-1 font-medium text-green-700">SJ {{ $manifest->suratJalanBongkaranTanjungPinang->nomor_surat_jalan }}</span>
                            @else
                                <span class="tp-muted mr-2">Belum dibuat</span>
                                @can('surat-jalan-bongkaran-tanjung-pinang-create')<a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.create', ['manifest_id' => $manifest->id, 'nama_kapal' => $selectedKapal, 'no_voyage' => $selectedVoyage]) }}" class="tp-button tp-button-primary"><i class="fas fa-plus mr-1"></i> Buat SJ</a>@endcan
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-12 text-center"><div class="font-medium text-slate-700">Tidak ada data manifest untuk kapal dan voyage ini.</div><div class="tp-muted mt-1">{{ $selectedKapal }} · {{ $selectedVoyage }}</div></td></tr>
                @endforelse
                </tbody>
                @else
                <thead><tr><th>Tanggal / No. SJ</th><th>BL / Kontainer</th><th>Pengirim / Penerima</th><th>Supir / Plat</th><th>Tujuan</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                @forelse($suratJalans as $suratJalan)
                    <tr>
                        <td><div class="font-medium text-slate-800">{{ $suratJalan->tanggal_surat_jalan?->format('d/m/Y') ?? '—' }}</div><div class="tp-muted mt-0.5">{{ $suratJalan->nomor_surat_jalan }}</div></td>
                        <td><div>{{ $suratJalan->no_bl ?: 'BL —' }}</div><div class="tp-muted">{{ $suratJalan->no_kontainer ?: 'Kontainer —' }}{{ $suratJalan->size ? ' · '.$suratJalan->size : '' }}</div></td>
                        <td><div>{{ $suratJalan->pengirim ?: '—' }}</div><div class="tp-muted">{{ $suratJalan->penerima ?: '—' }}</div></td>
                        <td><div>{{ $suratJalan->supir ?: '—' }}</div><div class="tp-muted">{{ $suratJalan->no_plat ?: 'Plat —' }}</div></td>
                        <td>{{ $suratJalan->tujuan_pengiriman ?: '—' }}</td>
                        <td class="whitespace-nowrap text-right"><a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.print', $suratJalan->id) }}" target="_blank" class="px-2 py-1 text-blue-700" title="Cetak"><i class="fas fa-print"></i></a>@can('surat-jalan-bongkaran-tanjung-pinang-update')<a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.edit', $suratJalan->id) }}" class="px-2 py-1 text-blue-700" title="Ubah"><i class="fas fa-pen"></i></a>@endcan @can('surat-jalan-bongkaran-tanjung-pinang-delete')<form method="POST" action="{{ route('surat-jalan-bongkaran-tanjung-pinang.destroy', $suratJalan->id) }}" class="inline" onsubmit="return confirm('Hapus surat jalan ini?')">@csrf @method('DELETE')<input type="hidden" name="nama_kapal" value="{{ $selectedKapal }}"><input type="hidden" name="no_voyage" value="{{ $selectedVoyage }}"><button class="px-2 py-1 text-red-700" title="Hapus"><i class="fas fa-trash"></i></button></form>@endcan</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-12 text-center"><div class="font-medium text-slate-700">Belum ada surat jalan untuk pilihan ini.</div><div class="tp-muted mt-1">{{ $selectedKapal }} · {{ $selectedVoyage }}</div></td></tr>
                @endforelse
                </tbody>
                @endif
            </table>
        </div>
        @if($mode === 'manifest' && $manifests->hasPages())<div class="flex justify-between border-t border-slate-200 px-4 py-3"><span class="tp-muted">Menampilkan {{ $manifests->firstItem() }}–{{ $manifests->lastItem() }} dari {{ $manifests->total() }}</span>{{ $manifests->links() }}</div>@elseif($mode === 'surat_jalan' && $suratJalans->hasPages())<div class="flex justify-between border-t border-slate-200 px-4 py-3"><span class="tp-muted">Menampilkan {{ $suratJalans->firstItem() }}–{{ $suratJalans->lastItem() }} dari {{ $suratJalans->total() }}</span>{{ $suratJalans->links() }}</div>@endif
    </div>
</div>
@endsection
