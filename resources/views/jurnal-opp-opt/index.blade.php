@extends('layouts.app')

@section('title', 'Jurnal OPT/OPP - AYPSIS')
@section('page_title', 'Jurnal OPT/OPP')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Jurnal OPT/OPP</h1>
            <p class="mt-1 text-sm text-gray-500">Buku jurnal mandiri untuk saldo dan kredit OPT/OPP.</p>
        </div>
        @can('jurnal-opp-opt-create')
            <button type="button" id="open-create-options" class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                <i class="fas fa-plus"></i>
                Tambah Transaksi
            </button>
        @endcan
    </div>

    @can('jurnal-opp-opt-create')
        <div id="create-options-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 p-4" role="dialog" aria-modal="true" aria-labelledby="create-options-title">
            <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl sm:p-8">
                <div class="mb-6 flex items-start justify-between gap-4">
                    <div>
                        <h2 id="create-options-title" class="text-xl font-bold text-gray-900">Tambah Transaksi</h2>
                        <p class="mt-1 text-sm text-gray-500">Pilih cara pencatatan transaksi.</p>
                    </div>
                    <button type="button" id="close-create-options" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700" aria-label="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <a href="{{ route('jurnal-opp-opt.create', ['mode' => 'voyage']) }}" class="group rounded-xl border-2 border-blue-100 p-5 transition hover:border-blue-500 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-700 group-hover:bg-blue-200"><i class="fas fa-ship text-lg"></i></span>
                        <span class="block font-bold text-gray-900">Dengan Voyage</span>
                        <span class="mt-1 block text-sm text-gray-500">Transaksi terkait dengan perjalanan kapal.</span>
                    </a>
                    <a href="{{ route('jurnal-opp-opt.create', ['mode' => 'manual']) }}" class="group rounded-xl border-2 border-emerald-100 p-5 transition hover:border-emerald-500 hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <span class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 group-hover:bg-emerald-200"><i class="fas fa-file-invoice-dollar text-lg"></i></span>
                        <span class="block font-bold text-gray-900">Tanpa Voyage</span>
                        <span class="mt-1 block text-sm text-gray-500">Catat transaksi jurnal secara langsung.</span>
                    </a>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="button" id="cancel-create-options" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                </div>
            </div>
        </div>
        <script>
            (() => {
                const modal = document.getElementById('create-options-modal');
                const openButton = document.getElementById('open-create-options');
                const closeButtons = [document.getElementById('close-create-options'), document.getElementById('cancel-create-options')];
                const close = () => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                };

                openButton.addEventListener('click', () => {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });
                closeButtons.forEach((button) => button.addEventListener('click', close));
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) close();
                });
                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && !modal.classList.contains('hidden')) close();
                });
            })();
        </script>
    @endcan

    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-blue-500">Total Saldo pada Filter</p>
            <p class="mt-2 text-xl font-bold text-gray-900">Rp {{ number_format($totalSaldo, 2, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-rose-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-rose-500">Total Kredit pada Filter</p>
            <p class="mt-2 text-xl font-bold text-gray-900">Rp {{ number_format($totalKredit, 2, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-600">Saldo Akhir Keseluruhan</p>
            <p class="mt-2 text-xl font-bold {{ $saldoAkhir < 0 ? 'text-rose-700' : 'text-emerald-800' }}">Rp {{ number_format($saldoAkhir, 2, ',', '.') }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('jurnal-opp-opt.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="md:col-span-2">
                <label for="search" class="mb-1 block text-xs font-bold uppercase tracking-wider text-gray-500">Pencarian</label>
                <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Nomor referensi atau keterangan"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <label for="tanggal_dari" class="mb-1 block text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal Dari</label>
                <input id="tanggal_dari" name="tanggal_dari" type="date" value="{{ request('tanggal_dari') }}"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <label for="tanggal_sampai" class="mb-1 block text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal Sampai</label>
                <input id="tanggal_sampai" name="tanggal_sampai" type="date" value="{{ request('tanggal_sampai') }}"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div class="flex gap-2 md:col-span-4 md:justify-end">
                <a href="{{ route('jurnal-opp-opt.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Reset</a>
                <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">Terapkan Filter</button>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Referensi</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Keterangan</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-500">Saldo</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-500">Kredit</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-500">Saldo Berjalan</th>
                        <th class="px-5 py-3 text-center text-xs font-bold uppercase tracking-wider text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($entries as $entry)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">{{ $entry->tanggal->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-gray-800">{{ $entry->nomor_referensi ?: '-' }}</td>
                            <td class="min-w-64 px-5 py-4 text-sm text-gray-600">{{ $entry->keterangan }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-blue-700">
                                {{ (float) $entry->saldo > 0 ? 'Rp '.number_format((float) $entry->saldo, 2, ',', '.') : '-' }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-rose-700">
                                {{ (float) $entry->kredit > 0 ? 'Rp '.number_format((float) $entry->kredit, 2, ',', '.') : '-' }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-bold {{ (float) $entry->saldo_berjalan < 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                                Rp {{ number_format((float) $entry->saldo_berjalan, 2, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    @can('jurnal-opp-opt-update')
                                        <a href="{{ route('jurnal-opp-opt.edit', $entry) }}" class="rounded-md bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-100">Ubah</a>
                                    @endcan
                                    @can('jurnal-opp-opt-delete')
                                        <form method="POST" action="{{ route('jurnal-opp-opt.destroy', $entry) }}" onsubmit="return confirm('Hapus transaksi ini? Saldo berjalan akan dihitung ulang.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-md bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">Hapus</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-500">Belum ada transaksi Jurnal OPT/OPP.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($entries->hasPages())
            <div class="border-t border-gray-200 px-5 py-4">
                {{ $entries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
