@extends('layouts.app')
@section('title', 'Riwayat Pranota Uang Makan')
@section('page_title', 'Riwayat Pranota Uang Makan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pranota-uang-makan-index.css') }}?v={{ filemtime(public_path('css/pranota-uang-makan-index.css')) }}">
@endpush

@section('content')
@php
    $totalKaryawan = $pranotas->sum(fn ($item) => $item->details->count());
@endphp
<div class="meal-history">
    <header class="mh-header">
        <div class="mh-heading">
            <span class="mh-heading-icon"><i class="fa-solid fa-file-invoice" aria-hidden="true"></i></span>
            <div><h1>Riwayat Pranota Uang Makan</h1><p>Kelola pranota uang makan yang belum terhubung ke PUML.</p></div>
        </div>
        <a href="{{ route('payroll.uang-makan') }}" class="mh-button mh-primary"><i class="fa-solid fa-plus" aria-hidden="true"></i>Buat Pranota Baru</a>
    </header>

    @if(session('success'))
        <div class="mh-alert" role="alert"><i class="fa-solid fa-circle-check" aria-hidden="true"></i>{{ session('success') }}</div>
    @endif

    <section class="mh-summary" aria-label="Ringkasan seluruh pranota belum terhubung ke PUML">
        <article class="mh-stat"><span class="mh-stat-icon"><i class="fa-solid fa-file-lines" aria-hidden="true"></i></span><div><p>Total Pranota</p><strong>{{ number_format($pranotas->count(), 0, ',', '.') }} <small>Pranota</small></strong><span>Belum terhubung ke PUML</span></div></article>
        <article class="mh-stat mh-money"><span class="mh-stat-icon"><i class="fa-solid fa-wallet" aria-hidden="true"></i></span><div><p>Total Nominal</p><strong>Rp {{ number_format($pranotas->sum('total_nominal'), 0, ',', '.') }}</strong><span>Akumulasi seluruh pranota</span></div></article>
        <article class="mh-stat"><span class="mh-stat-icon"><i class="fa-solid fa-users" aria-hidden="true"></i></span><div><p>Total Data Karyawan</p><strong>{{ number_format($totalKaryawan, 0, ',', '.') }} <small>Data</small></strong><span>Akumulasi karyawan per pranota</span></div></article>
    </section>

    <section class="mh-panel" aria-label="Daftar pranota uang makan">
        <div class="mh-toolbar">
            <div><h2>Daftar Pranota</h2><p>Rincian dan dokumen uang makan karyawan.</p></div>
            <label class="mh-search"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><span class="sr-only">Cari nomor pranota, tanggal, atau status</span><input id="mh-search" type="search" placeholder="Cari nomor, tanggal, atau status…" autocomplete="off"></label>
        </div>
        <div class="mh-table-scroll">
            <table class="mh-table">
                <thead><tr><th scope="col">No.</th><th scope="col">Nomor Pranota</th><th scope="col">Tanggal</th><th scope="col" class="mh-center">Karyawan</th><th scope="col" class="mh-right">Total Nominal</th><th scope="col">Status</th><th scope="col" class="mh-center">Aksi</th></tr></thead>
                <tbody>
                    @forelse($pranotas as $pranota)
                        <tr data-mh-row data-search="{{ $pranota->nomor_pranota }} {{ $pranota->tanggal_pranota?->format('d/m/Y') }} {{ $pranota->status }}">
                            <td class="mh-number">{{ $loop->iteration }}</td>
                            <td><a class="mh-document" href="{{ route('pranota-uang-makan.show', $pranota->id) }}">{{ $pranota->nomor_pranota }}</a><span class="mh-caption">Pranota uang makan</span></td>
                            <td><span class="mh-date"><i class="fa-regular fa-calendar" aria-hidden="true"></i>{{ $pranota->tanggal_pranota?->format('d/m/Y') ?? '—' }}</span></td>
                            <td class="mh-center"><span class="mh-employees"><i class="fa-solid fa-users" aria-hidden="true"></i>{{ $pranota->details->count() }} Orang</span></td>
                            <td class="mh-right mh-amount">Rp {{ number_format($pranota->total_nominal, 0, ',', '.') }}</td>
                            <td><span class="mh-status {{ strtolower($pranota->status ?? '') === 'draft' ? 'mh-draft' : '' }}">{{ ucfirst($pranota->status ?? 'Tidak tersedia') }}</span></td>
                            <td>
                                <div class="mh-actions">
                                    <a href="{{ route('pranota-uang-makan.show', $pranota->id) }}" title="Lihat detail" aria-label="Lihat detail {{ $pranota->nomor_pranota }}"><i class="fa-solid fa-eye" aria-hidden="true"></i></a>
                                    <a href="{{ route('pranota-uang-makan.edit', $pranota->id) }}" title="Edit pranota" aria-label="Edit {{ $pranota->nomor_pranota }}"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></a>
                                    <a href="{{ route('pranota-uang-makan.show', $pranota->id) }}?print=true" target="_blank" rel="noopener" title="Cetak pranota" aria-label="Cetak {{ $pranota->nomor_pranota }}"><i class="fa-solid fa-print" aria-hidden="true"></i></a>
                                    <a class="mh-export" href="{{ route('pranota-uang-makan.export-excel', $pranota->id) }}" title="Export Excel" aria-label="Export Excel {{ $pranota->nomor_pranota }}"><i class="fa-solid fa-file-excel" aria-hidden="true"></i></a>
                                    <a href="{{ route('pranota-uang-makan.export-auto-transfer', $pranota->id) }}" title="Export Excel Auto Transfer" aria-label="Export Auto Transfer {{ $pranota->nomor_pranota }}"><i class="fa-solid fa-money-bill-transfer" aria-hidden="true"></i></a>
                                    <form action="{{ route('pranota-uang-makan.destroy', $pranota->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Pranota Uang Makan ini? Data detail yang terkait juga akan ikut terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="mh-delete" type="submit" title="Hapus pranota" aria-label="Hapus {{ $pranota->nomor_pranota }}"><i class="fa-solid fa-trash-can" aria-hidden="true"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="mh-empty"><i class="fa-regular fa-folder-open" aria-hidden="true"></i><h3>Belum ada pranota uang makan</h3><p>Pranota yang belum terhubung ke PUML akan ditampilkan di sini.</p><a href="{{ route('payroll.uang-makan') }}" class="mh-button mh-primary">Buat Pranota Baru</a></div></td></tr>
                    @endforelse
                    <tr id="mh-no-results" hidden><td colspan="7"><div class="mh-empty"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><h3>Pranota tidak ditemukan</h3><p>Coba nomor pranota, tanggal, atau status lainnya.</p><button type="button" class="mh-button" id="mh-reset">Hapus pencarian</button></div></td></tr>
                </tbody>
            </table>
        </div>
        <footer class="mh-footer"><span id="mh-count" role="status" aria-live="polite">Menampilkan {{ $pranotas->count() }} dari {{ $pranotas->count() }} pranota</span><span><i class="fa-solid fa-circle-info" aria-hidden="true"></i>Hanya pranota yang belum terhubung ke PUML</span></footer>
    </section>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const input = document.getElementById('mh-search');
    const rows = Array.from(document.querySelectorAll('[data-mh-row]'));
    const filter = () => {
        const query = input.value.trim().toLocaleLowerCase('id-ID');
        let count = 0;
        rows.forEach(row => {
            const visible = row.dataset.search.toLocaleLowerCase('id-ID').includes(query);
            row.hidden = !visible;
            if (visible) count++;
        });
        document.getElementById('mh-no-results').hidden = !rows.length || count > 0;
        document.getElementById('mh-count').textContent = 'Menampilkan ' + count + ' dari ' + rows.length + ' pranota';
    };
    input.addEventListener('input', filter);
    document.getElementById('mh-reset').addEventListener('click', () => { input.value = ''; filter(); input.focus(); });
})();
</script>
@endpush
