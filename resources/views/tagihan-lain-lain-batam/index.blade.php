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
                        @can('pranota-tagihan-lain-lain-batam-create')<td class="px-4 py-3">@if (!$pranotaItem)<input type="checkbox" class="tagihan-checkbox h-4 w-4 rounded border-gray-300 text-blue-600" value="{{ $item->id }}" aria-label="Pilih tagihan {{ $item->nomor_tagihan }}">@endif</td>@endcan
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
                            @can('tagihan-service-kendaraan-batam-update')
                                <a href="{{ route('tagihan-lain-lain-batam.edit', $item) }}" class="font-medium text-blue-700 hover:underline">Edit</a>
                            @endcan
                            @can('tagihan-service-kendaraan-batam-delete')
                                <form action="{{ route('tagihan-lain-lain-batam.destroy', $item) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Hapus tagihan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="font-medium text-red-700 hover:underline">Hapus</button>
                                </form>
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

<div id="pranota-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4" role="dialog" aria-modal="true" aria-labelledby="pranota-modal-title">
    <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
        <div class="mb-5 flex items-start justify-between gap-4">
            <div>
                <h2 id="pranota-modal-title" class="text-xl font-bold text-gray-900">Masukkan Tagihan ke Pranota</h2>
                <p class="mt-1 text-sm text-gray-600"><span id="modal-selected-count">0</span> tagihan dipilih. Tagihan yang dimasukkan tidak dapat dipilih kembali.</p>
            </div>
            <button type="button" id="close-pranota-modal" class="text-2xl leading-none text-gray-500 hover:text-gray-800" aria-label="Tutup">&times;</button>
        </div>
        <form id="pranota-form" action="{{ route('tagihan-lain-lain-batam.pranota.store') }}" method="POST" class="space-y-4">
            @csrf
            <div id="selected-tagihan-inputs"></div>
            <div>
                <label for="tanggal_pranota" class="mb-1 block text-sm font-medium text-gray-700">Tanggal Pranota <span class="text-red-600">*</span></label>
                <input type="date" id="tanggal_pranota" name="tanggal_pranota" value="{{ now()->format('Y-m-d') }}" required class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label for="keterangan_pranota" class="mb-1 block text-sm font-medium text-gray-700">Keterangan</label>
                <textarea id="keterangan_pranota" name="keterangan_pranota" rows="3" maxlength="2000" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Catatan pranota (opsional)"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" id="cancel-pranota-modal" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-700">Batal</button>
                <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Buat Pranota</button>
            </div>
        </form>
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
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });
    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };
    document.getElementById('close-pranota-modal')?.addEventListener('click', closeModal);
    document.getElementById('cancel-pranota-modal')?.addEventListener('click', closeModal);
    modal.addEventListener('click', (event) => { if (event.target === modal) closeModal(); });
});
</script>
@endsection
