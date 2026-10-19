@extends('layouts.app')

@section('title', 'Tagihan Lain Lain Batam')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tagihan Lain Lain Batam</h1>
            <p class="mt-1 text-sm text-gray-600">Kelola tagihan lain lain Batam.</p>
        </div>
        @can('tagihan-service-kendaraan-batam-create')
            <a href="{{ route('tagihan-lain-lain-batam.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Tambah Tagihan</a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800">{{ $errors->first() }}</div>
    @endif

    <form method="GET" action="{{ route('tagihan-lain-lain-batam.index') }}" class="mb-5 grid grid-cols-1 gap-3 rounded-lg border bg-white p-4 md:grid-cols-5">
        <select name="jenis_tagihan" class="rounded-lg border-gray-300 text-sm" aria-label="Jenis tagihan">
            <option value="">Semua jenis tagihan</option>
            <option value="kendaraan" @selected(request('jenis_tagihan') === 'kendaraan')>Tagihan Kendaraan</option>
            <option value="lain_lain" @selected(request('jenis_tagihan') === 'lain_lain')>Tagihan Lain Lain</option>
        </select>
        <input name="search" value="{{ request('search') }}" placeholder="No. tagihan, polisi, bengkel" class="rounded-lg border-gray-300 text-sm">
        <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="rounded-lg border-gray-300 text-sm" aria-label="Tanggal service dari">
        <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="rounded-lg border-gray-300 text-sm" aria-label="Tanggal service sampai">
        <div class="flex gap-2">
            <button class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white">Filter</button>
            <a href="{{ route('tagihan-lain-lain-batam.index') }}" class="rounded-lg border px-4 py-2 text-sm text-gray-700">Reset</a>
        </div>
    </form>

    @can('pranota-tagihan-lain-lain-batam-create')
        <div id="pranota-actions" class="mb-4 hidden items-center justify-between rounded-lg border border-blue-200 bg-blue-50 p-3">
            <span class="text-sm text-blue-900"><span id="selected-count">0</span> tagihan dipilih</span>
            <button type="button" id="open-pranota-modal" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Masukkan ke Pranota</button>
        </div>
    @endcan

    <div class="overflow-x-auto rounded-lg border bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase text-gray-600">
                <tr>
                    @can('pranota-tagihan-lain-lain-batam-create')<th class="px-4 py-3"><input type="checkbox" id="select-all-tagihan" aria-label="Pilih semua tagihan"></th>@endcan<th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">No. Tagihan</th><th class="px-4 py-3">Jenis Tagihan</th>@if (request('jenis_tagihan') !== 'lain_lain')<th class="px-4 py-3">No. Polisi / KIR</th>@endif<th class="px-4 py-3">Bengkel / Vendor</th><th class="px-4 py-3">Rincian</th><th class="px-4 py-3 text-right">Biaya</th><th class="px-4 py-3">PPh</th><th class="px-4 py-3 text-right">Total Biaya</th><th class="px-4 py-3">Status Pranota</th><th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($tagihan as $item)
                    <tr>
                        @php($pranotaItem = $item->pranotaItems->first())
                        @can('pranota-tagihan-lain-lain-batam-create')<td class="px-4 py-3">@if (!$pranotaItem)<input type="checkbox" class="tagihan-checkbox h-4 w-4 rounded border-gray-300 text-blue-600" value="{{ $item->id }}" data-nomor="{{ $item->nomor_tagihan }}" data-jenis="{{ $item->jenis_tagihan === 'kendaraan' ? 'Kendaraan' : 'Lain Lain' }}" data-vendor="{{ $item->bengkel }}" data-biaya="{{ $item->biaya }}" data-pph="{{ $item->pph_rate }}" data-total="{{ $item->total_biaya }}" aria-label="Pilih tagihan {{ $item->nomor_tagihan }}">@endif</td>@endcan
                        <td class="whitespace-nowrap px-4 py-3">{{ $item->tanggal_service->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $item->nomor_tagihan ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $item->jenis_tagihan === 'kendaraan' ? 'Tagihan Kendaraan' : 'Tagihan Lain Lain' }}</td>
                        @if (request('jenis_tagihan') !== 'lain_lain')<td class="whitespace-nowrap px-4 py-3 font-medium">{{ $item->jenis_tagihan === 'kendaraan' ? ($item->nomor_polisi ?: '-') : '-' }}</td>@endif
                        <td class="px-4 py-3">{{ $item->bengkel }}</td>
                        <td class="max-w-sm px-4 py-3">{{ $item->rincian_service }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">Rp {{ number_format((float) $item->biaya, 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-4 py-3">{{ (float) $item->pph_rate == 0 ? 'Tanpa PPh' : rtrim(rtrim(number_format((float) $item->pph_rate, 1, ',', ''), '0'), ',').'%' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">Rp {{ number_format((float) $item->total_biaya, 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-4 py-3">@if ($pranotaItem?->pranota)<span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">Sudah dibuat pranota</span><div class="mt-1 text-xs text-gray-500">@can('pranota-tagihan-lain-lain-batam-view')<a href="{{ route('pranota-tagihan-lain-lain-batam.show', $pranotaItem->pranota) }}" class="text-blue-700 hover:underline">{{ $pranotaItem->pranota->nomor_pranota }}</a>@else{{ $pranotaItem->pranota->nomor_pranota }}@endcan</div>@else<span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800">Belum dibuat pranota</span>@endif</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            @if ($item->jenis_tagihan === 'kendaraan' && $item->mobil_id)
                                @can('rekap-biaya-asset-view')
                                    <a href="{{ route('rekap-biaya-asset.show', ['asset_type' => 'mobil', 'asset_id' => $item->mobil_id, 'bulan' => $item->tanggal_service->month, 'tahun' => $item->tanggal_service->year]) }}" class="font-medium text-emerald-700 hover:underline">Lihat rekap</a>
                                @endcan
                            @endif
                            @can('tagihan-service-kendaraan-batam-update')
                                <a href="{{ route('tagihan-lain-lain-batam.edit', $item) }}" class="ml-3 font-medium text-blue-700 hover:underline">Edit</a>
                            @endcan
                            @can('tagihan-service-kendaraan-batam-delete')
                                @if ($pranotaItem)
                                    <span class="ml-3 text-xs text-gray-500" title="Hapus pranotanya terlebih dahulu">Hapus pranota dulu</span>
                                @else
                                    <form action="{{ route('tagihan-lain-lain-batam.destroy', $item) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Hapus tagihan ini?')">
                                        @csrf @method('DELETE')
                                        <button class="font-medium text-red-700 hover:underline">Hapus</button>
                                    </form>
                                @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ (request('jenis_tagihan') === 'lain_lain' ? 10 : 11) + (auth()->user()->can('pranota-tagihan-lain-lain-batam-create') ? 1 : 0) }}" class="px-4 py-8 text-center text-gray-500">Belum ada tagihan untuk filter ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $tagihan->links() }}</div>
</div>

<div id="pranota-modal" class="fixed inset-0 z-50 hidden overflow-y-auto p-4" role="dialog" aria-modal="true" aria-labelledby="pranota-modal-title">
    <div id="pranota-backdrop" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" aria-hidden="true"></div>
    <div class="relative z-10 flex min-h-full items-center justify-center py-4">
        <div class="w-full max-w-4xl overflow-hidden rounded-2xl bg-white text-left shadow-2xl">
            <form id="pranota-form" action="{{ route('tagihan-lain-lain-batam.pranota.store') }}" method="POST">
                @csrf
                <div id="selected-tagihan-inputs"></div>
                <div class="flex items-center justify-between gap-4 bg-indigo-600 px-6 py-4 text-white">
                    <div>
                        <h2 id="pranota-modal-title" class="text-lg font-bold">Buat Pranota Tagihan Lain Lain Batam</h2>
                        <p class="mt-1 text-xs text-indigo-100">Penerbitan pranota dari tagihan yang dipilih</p>
                    </div>
                    <button type="button" id="close-pranota-modal" class="rounded-lg px-2 text-2xl leading-none hover:bg-white/20" aria-label="Tutup">&times;</button>
                </div>

                <div class="max-h-[70vh] space-y-5 overflow-y-auto p-6">
                    <div class="grid gap-4 rounded-xl border bg-gray-50 p-4 sm:grid-cols-2">
                        <div>
                            <label for="nomor_pranota_preview" class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-700">Nomor Pranota</label>
                            <input id="nomor_pranota_preview" value="Dibuat otomatis saat disimpan" readonly class="w-full rounded-lg border-gray-300 bg-gray-100 text-sm font-medium text-gray-600">
                        </div>
                        <div>
                            <label for="tanggal_pranota" class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-700">Tanggal Pranota <span class="text-red-600">*</span></label>
                            <input type="date" id="tanggal_pranota" name="tanggal_pranota" value="{{ old('tanggal_pranota', now()->format('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 text-sm">
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-3 text-xs font-bold uppercase tracking-wide text-gray-700">Rincian Tagihan Terpilih (<span id="modal-selected-count">0</span>)</h3>
                        <div class="overflow-x-auto rounded-xl border">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-600">
                                    <tr><th class="px-4 py-3">No. Tagihan</th><th class="px-4 py-3">Jenis</th><th class="px-4 py-3">Bengkel / Vendor</th><th class="px-4 py-3 text-right">Biaya</th><th class="px-4 py-3">PPh</th><th class="px-4 py-3 text-right">Total</th></tr>
                                </thead>
                                <tbody id="modal-tagihan-rows" class="divide-y divide-gray-100"></tbody>
                                <tfoot class="bg-gray-50"><tr><td colspan="5" class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-600">Grand Total</td><td id="modal-grand-total" class="whitespace-nowrap px-4 py-3 text-right text-base font-bold text-indigo-700">Rp 0</td></tr></tfoot>
                            </table>
                        </div>
                    </div>

                    <div>
                        <label for="keterangan_pranota" class="mb-1 block text-sm font-medium text-gray-700">Keterangan</label>
                        <textarea id="keterangan_pranota" name="keterangan_pranota" rows="2" maxlength="2000" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Catatan pranota (opsional)">{{ old('keterangan_pranota') }}</textarea>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t bg-gray-50 px-6 py-4">
                    <p class="text-xs text-gray-500">Periksa rincian tagihan sebelum menerbitkan pranota.</p>
                    <div class="flex gap-2">
                        <button type="button" id="cancel-pranota-modal" class="rounded-lg border bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Batal</button>
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Simpan &amp; Terbitkan Pranota</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const checkboxes = [...document.querySelectorAll('.tagihan-checkbox')];
    const selectAll = document.getElementById('select-all-tagihan');
    const actionBar = document.getElementById('pranota-actions');
    const count = document.getElementById('selected-count');
    const modal = document.getElementById('pranota-modal');
    const modalCount = document.getElementById('modal-selected-count');
    const selectedInputs = document.getElementById('selected-tagihan-inputs');
    const modalRows = document.getElementById('modal-tagihan-rows');
    const modalGrandTotal = document.getElementById('modal-grand-total');
    const formatRupiah = (amount) => `Rp ${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(amount)}`;
    const updateSelection = () => {
        const selected = checkboxes.filter((checkbox) => checkbox.checked);
        count.textContent = selected.length;
        actionBar.classList.toggle('hidden', selected.length === 0);
        actionBar.classList.toggle('flex', selected.length > 0);
        if (selectAll) selectAll.checked = checkboxes.length > 0 && selected.length === checkboxes.length;
    };
    checkboxes.forEach((checkbox) => checkbox.addEventListener('change', updateSelection));
    selectAll?.addEventListener('change', () => {
        checkboxes.forEach((checkbox) => { checkbox.checked = selectAll.checked; });
        updateSelection();
    });
    document.getElementById('open-pranota-modal')?.addEventListener('click', () => {
        const selected = checkboxes.filter((checkbox) => checkbox.checked);
        if (!selected.length) return;
        modalCount.textContent = selected.length;
        selectedInputs.replaceChildren(...selected.map((checkbox) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tagihan_ids[]';
            input.value = checkbox.value;
            return input;
        }));
        let grandTotal = 0;
        modalRows.replaceChildren(...selected.map((checkbox) => {
            const row = document.createElement('tr');
            const total = Number.parseFloat(checkbox.dataset.total) || 0;
            grandTotal += total;
            [
                checkbox.dataset.nomor || '-',
                checkbox.dataset.jenis || '-',
                checkbox.dataset.vendor || '-',
                formatRupiah(Number.parseFloat(checkbox.dataset.biaya) || 0),
                `${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(Number.parseFloat(checkbox.dataset.pph) || 0)}%`,
                formatRupiah(total),
            ].forEach((value, index) => {
                const cell = document.createElement('td');
                cell.className = `px-4 py-3${index === 3 || index === 5 ? ' text-right whitespace-nowrap' : ''}`;
                cell.textContent = value;
                row.appendChild(cell);
            });
            return row;
        }));
        modalGrandTotal.textContent = formatRupiah(grandTotal);
        modal.classList.remove('hidden');
    });
    const closeModal = () => {
        modal.classList.add('hidden');
    };
    document.getElementById('close-pranota-modal')?.addEventListener('click', closeModal);
    document.getElementById('cancel-pranota-modal')?.addEventListener('click', closeModal);
    document.getElementById('pranota-backdrop')?.addEventListener('click', closeModal);
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal(); });
});
</script>
@endsection
