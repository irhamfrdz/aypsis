@extends('layouts.app')

@section('title', 'Riwayat Pemakaian Asset')
@section('page_title', 'Riwayat Pemakaian Asset')

@section('content')
<div class="mx-auto max-w-7xl px-4 pb-8">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <header class="border-b border-gray-200 px-5 py-6 sm:px-7">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">Master Asset / Riwayat</p>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-gray-900">Riwayat Pemakaian Karyawan</h1>
                    <p class="mt-1 text-sm text-gray-500">Pantau serah terima dan pengembalian barang kepada karyawan.</p>
                </div>
                <a href="{{ route('asset.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"><i class="fas fa-arrow-left text-xs" aria-hidden="true"></i> Kembali</a>
            </div>
            <div class="mt-6 flex flex-wrap items-center gap-4 rounded-xl border border-blue-100 bg-blue-50 px-4 py-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-blue-600 shadow-sm"><i class="fas fa-box" aria-hidden="true"></i></span>
                <div class="min-w-0 flex-1"><p class="text-xs font-medium text-blue-700">{{ $asset->kode_asset }}</p><p class="mt-0.5 break-words font-bold text-gray-900">{{ $asset->nama_asset }}</p></div>
                <div class="text-sm"><span class="block text-xs text-gray-500">Kategori</span><span class="mt-1 block font-semibold text-gray-700">{{ $asset->kategori ?: '-' }}</span></div>
                @if($asset->karyawan)<div class="text-sm"><span class="block text-xs text-gray-500">Pemegang di Master Asset</span><span class="mt-1 block font-semibold text-gray-700">{{ $asset->karyawan->nama_lengkap }}{{ $asset->karyawan->nik ? ' ('.$asset->karyawan->nik.')' : '' }}</span></div>@endif
            </div>
        </header>

        <section class="px-5 py-6 sm:px-7" aria-labelledby="history-title">
            @if(session('success'))<div role="status" class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>@endif
            @if($errors->any())<div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>@endif
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div><h2 id="history-title" class="text-base font-bold text-gray-900">Riwayat serah terima APD</h2><p class="mt-1 text-sm text-gray-500">Transaksi yang dicatat pada detail karyawan untuk asset ini.</p></div>
                <span class="rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-semibold text-gray-600">{{ $transactions->total() }} transaksi</span>
            </div>
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr><th class="whitespace-nowrap px-4 py-3">ID Transaksi</th><th class="whitespace-nowrap px-4 py-3">Tanggal</th><th class="whitespace-nowrap px-4 py-3">Nama Barang</th><th class="whitespace-nowrap px-4 py-3">Karyawan</th><th class="px-4 py-3">NIK</th><th class="px-4 py-3">Ukuran</th><th class="px-4 py-3 text-right">Qty</th><th class="px-4 py-3">Keterangan</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($transactions as $item)
                        <tr class="transition hover:bg-gray-50">
                            <td class="whitespace-nowrap px-4 py-4 font-semibold text-blue-700">{{ $item->nomor_transaksi }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-gray-600">{{ $item->tanggal_serah_terima->format('d/m/Y') }}</td>
                            <td class="min-w-[130px] px-4 py-4 font-medium text-gray-900">{{ $item->nama_barang }}</td>
                            <td class="min-w-[140px] px-4 py-4 font-medium text-gray-900">{{ $item->karyawan?->nama_lengkap ?? 'Karyawan tidak tersedia' }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-gray-600">{{ $item->karyawan?->nik ?: '-' }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-gray-600">{{ $item->ukuran ?: '-' }}</td>
                            <td class="px-4 py-4 text-right font-semibold text-gray-700">{{ rtrim(rtrim($item->qty, '0'), '.') }}</td>
                            <td class="min-w-[160px] px-4 py-4 text-gray-600">{{ $item->keterangan ?: '-' }}</td>
                            <td class="px-4 py-4"><span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold {{ $item->status === 'Di Gunakan' ? 'bg-blue-50 text-blue-700' : 'bg-green-50 text-green-700' }}">{{ $item->status }}</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap gap-2">
                                @if($item->status === 'Di Gunakan' && Auth::user() && (Auth::user()->hasRole('admin') || Auth::user()->can('asset-update') || Auth::user()->can('master-asset-update')))
                                <form method="POST" action="{{ route('asset.employee-return', [$asset, $item]) }}" onsubmit="return confirm('Kembalikan barang dari karyawan ini?')">@csrf<button class="whitespace-nowrap rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700" type="submit">Kembalikan</button></form>
                                @endif
                                @if(Auth::user() && (Auth::user()->hasRole('admin') || Auth::user()->can('asset-delete') || Auth::user()->can('master-asset-delete')))
                                <form method="POST" action="{{ route('asset.employee-history.destroy', [$asset, $item]) }}" onsubmit="return confirm('Hapus riwayat transaksi ini? Stok tidak akan berubah. Jika barang dikembalikan, gunakan tombol Kembalikan terlebih dahulu.')">@csrf @method('DELETE')<button type="submit" class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-50">Hapus</button></form>
                                @endif
                            </div></td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="px-4 py-14 text-center"><i class="fas fa-clock-rotate-left text-2xl text-gray-300" aria-hidden="true"></i><p class="mt-3 font-medium text-gray-600">Belum ada transaksi serah terima APD.</p><p class="mt-1 text-xs text-gray-400">Transaksi yang dicatat pada detail karyawan akan muncul di sini.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())<div class="mt-5">{{ $transactions->links() }}</div>@endif
            <div class="mt-8 border-t border-gray-200 pt-6">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div><h2 class="text-base font-bold text-gray-900">Riwayat pengembalian</h2><p class="mt-1 text-sm text-gray-500">Catatan barang yang sudah dikembalikan dan ditambahkan kembali ke stok.</p></div>
                    <span class="rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-semibold text-gray-600">{{ $returns->total() }} pengembalian</span>
                </div>
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500"><tr><th class="px-4 py-3">Waktu Pengembalian</th><th class="px-4 py-3">ID Transaksi</th><th class="px-4 py-3">Barang</th><th class="px-4 py-3">Karyawan</th><th class="px-4 py-3">NIK</th><th class="px-4 py-3">Ukuran</th><th class="px-4 py-3 text-right">Qty</th><th class="px-4 py-3">Diproses Oleh</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($returns as $returned)
                            <tr><td class="whitespace-nowrap px-4 py-3">{{ \Carbon\Carbon::parse($returned->returned_at)->format('d/m/Y H:i') }}</td><td class="whitespace-nowrap px-4 py-3 font-semibold text-blue-700">{{ $returned->nomor_transaksi }}</td><td class="px-4 py-3">{{ $returned->nama_barang }}</td><td class="px-4 py-3">{{ $returned->nama_karyawan ?: '-' }}</td><td class="px-4 py-3">{{ $returned->nik ?: '-' }}</td><td class="px-4 py-3">{{ $returned->ukuran ?: '-' }}</td><td class="px-4 py-3 text-right">{{ rtrim(rtrim(number_format((float) $returned->qty, 2, '.', ''), '0'), '.') }}</td><td class="px-4 py-3">{{ $returned->nama_pengguna ?: '-' }}</td></tr>
                            @empty
                            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Belum ada riwayat pengembalian yang tercatat.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($returns->hasPages())<div class="mt-5">{{ $returns->appends(request()->except('return_page'))->links() }}</div>@endif
            </div>
        </section>
    </div>
</div>
@endsection
