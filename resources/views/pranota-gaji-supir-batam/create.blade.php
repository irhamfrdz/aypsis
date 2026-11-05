@extends('layouts.app')

@section('title', 'Buat Pranota Gaji Supir Batam')
@section('page_title', 'Buat Pranota Gaji Supir Batam')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-6">
    <div class="mb-6 flex items-center justify-between gap-3">
        <div><h1 class="text-2xl font-bold text-gray-900">Buat Pranota Gaji Supir Batam</h1><p class="mt-1 text-sm text-gray-600">Pilih gaji berstatus PENDING yang akan digabungkan.</p></div>
        <a href="{{ route('pranota-gaji-supir-batam.index') }}" class="text-sm font-medium text-indigo-700 hover:underline">Kembali</a>
    </div>
    @if($errors->any())<div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    <form id="pranota-gaji-form" method="POST" action="{{ route('pranota-gaji-supir-batam.store') }}" class="space-y-5">
        @csrf
        <div class="grid gap-4 rounded-xl border bg-white p-5 shadow-sm sm:grid-cols-2">
            <div><label for="tanggal_pranota" class="mb-1 block text-sm font-medium">Tanggal Pranota <span class="text-red-600">*</span></label><input id="tanggal_pranota" name="tanggal_pranota" type="date" value="{{ old('tanggal_pranota', now()->toDateString()) }}" required class="w-full rounded-lg border-gray-300 text-sm"></div>
            <div><label for="keterangan" class="mb-1 block text-sm font-medium">Keterangan</label><input id="keterangan" name="keterangan" value="{{ old('keterangan') }}" maxlength="2000" class="w-full rounded-lg border-gray-300 text-sm"></div>
        </div>
        <div class="overflow-x-auto rounded-xl border bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-600"><tr>
                    <th class="px-4 py-3"><input type="checkbox" id="select-all-gaji" aria-label="Pilih semua gaji"></th><th class="px-4 py-3">Supir</th><th class="px-4 py-3">Periode</th><th class="px-4 py-3 text-right">Gaji Pokok</th><th class="px-4 py-3 text-right">Gaji Bersih</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($gajiGroups as $group)
                        @php
                            $first = $group->first();
                            $oldIds = array_map('intval', old('gaji_ids', []));
                            $groupIds = $group->pluck('id')->map(fn ($id) => (int) $id)->all();
                            $selectedCount = count(array_intersect($oldIds, $groupIds));
                            $groupKey = 'periode-'.$loop->index;
                        @endphp
                        <tr class="bg-gray-50">
                            <td class="px-4 py-3"><input type="checkbox" data-gaji-group-checkbox data-total="{{ $group->sum(fn ($gaji) => (float) $gaji->total_gaji) }}" data-group-id="{{ $loop->index }}" @checked($selectedCount === $group->count()) class="rounded border-gray-300 text-indigo-600"></td>
                            <td class="px-4 py-3 font-semibold">
                                <button type="button" data-period-toggle aria-expanded="false" aria-controls="{{ $groupKey }}" class="inline-flex items-center gap-2 text-left hover:text-indigo-700">
                                    <span data-period-icon aria-hidden="true">＋</span>
                                    <span>Periode: {{ $first->periode_text }} <span class="font-normal text-gray-500">({{ $group->count() }} supir)</span></span>
                                </button>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">Klik untuk tampilkan/sembunyikan</td>
                            <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($group->sum(fn ($gaji) => (float) $gaji->gaji_pokok), 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($group->sum(fn ($gaji) => (float) $gaji->total_gaji), 0, ',', '.') }}</td>
                        </tr>
                        @foreach($group as $gaji)
                            <tr data-period-row="{{ $groupKey }}" class="hidden">
                                <td class="px-4 py-2 pl-8"><input type="checkbox" name="gaji_ids[]" value="{{ $gaji->id }}" data-gaji-checkbox data-employee-id="{{ $gaji->karyawan_id }}" data-employee-name="{{ $gaji->karyawan?->nama_lengkap ?: 'Supir tidak tersedia' }}" data-group-id="{{ $loop->parent->index }}" data-total="{{ $gaji->total_gaji }}" @checked(in_array((int) $gaji->id, $oldIds, true)) class="rounded border-gray-300 text-indigo-600"></td>
                                <td class="px-4 py-2"><span class="font-medium">{{ $gaji->karyawan?->nama_lengkap ?: 'Supir tidak tersedia' }}</span><span class="block text-xs text-gray-500">{{ $gaji->karyawan?->plat ?: '-' }}</span></td>
                                <td class="whitespace-nowrap px-4 py-2">{{ $gaji->periode_text }}</td>
                                <td class="whitespace-nowrap px-4 py-2 text-right">Rp {{ number_format((float) $gaji->gaji_pokok, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-4 py-2 text-right font-semibold">Rp {{ number_format((float) $gaji->total_gaji, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">Tidak ada gaji PENDING yang tersedia. <a href="{{ route('gaji-supir-batam.index') }}" class="text-indigo-700 hover:underline">Lihat daftar gaji</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border bg-white p-4 shadow-sm">
            <p class="text-sm font-semibold">Dipilih: <span id="selected-count">0</span> gaji · Total: <span id="selected-total">Rp 0</span></p>
            <button type="submit" @disabled($gajiList->isEmpty()) class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">Simpan Pranota</button>
        </div>
        <dialog id="pph-dialog" aria-labelledby="pph-title" class="w-full max-w-2xl rounded-xl p-0 shadow-xl" style="max-height:85vh">
            <div class="p-5">
                <h2 id="pph-title" class="text-lg font-bold">Isi PPh Setiap Supir</h2>
                <p class="mt-1 mb-4 text-sm text-gray-600">PPh mengurangi gaji bersih. Isi 0 jika tidak ada potongan. Supir dengan beberapa periode cukup diisi sekali.</p>
                <div id="pph-fields" class="space-y-3"></div>
                <p id="pph-net-total" class="mt-4 font-semibold" aria-live="polite"></p>
                <div class="mt-5 flex justify-end gap-3">
                    <button id="pph-cancel" type="button" class="rounded-lg border px-4 py-2">Kembali</button>
                    <button id="pph-confirm" type="button" class="rounded-lg bg-indigo-600 px-4 py-2 text-white">Konfirmasi &amp; Simpan Pranota</button>
                </div>
            </div>
        </dialog>
        <p id="gaji-selection-error" class="hidden text-sm text-red-600" role="alert">Pilih minimal satu gaji terlebih dahulu.</p>
    </form>
</div>
@endsection

@push('scripts')
<script>window.pranotaGajiOldPph = {{ Illuminate\Support\Js::from(old('pph', [])) }};</script>
<script src="{{ asset('js/pranota-gaji-supir-batam-pph.js') }}?v={{ filemtime(public_path('js/pranota-gaji-supir-batam-pph.js')) }}"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const boxes = [...document.querySelectorAll('[data-gaji-checkbox]')];
    const groups = [...document.querySelectorAll('[data-gaji-group-checkbox]')];
    const toggles = [...document.querySelectorAll('[data-period-toggle]')];
    const all = document.getElementById('select-all-gaji');
    const update = () => {
        const selected = boxes.filter(box => box.checked);
        document.getElementById('selected-count').textContent = selected.length;
        document.getElementById('selected-total').textContent = new Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', maximumFractionDigits: 0}).format(selected.reduce((sum, box) => sum + Number(box.dataset.total), 0));
        all.checked = boxes.length > 0 && selected.length === boxes.length;
        all.indeterminate = selected.length > 0 && selected.length < boxes.length;
        groups.forEach(group => {
            const members = boxes.filter(box => box.dataset.groupId === group.dataset.groupId);
            const checked = members.filter(box => box.checked).length;
            group.checked = members.length > 0 && checked === members.length;
            group.indeterminate = checked > 0 && checked < members.length;
        });
    };
    boxes.forEach(box => box.addEventListener('change', update));
    groups.forEach(group => group.addEventListener('change', () => {
        boxes.filter(box => box.dataset.groupId === group.dataset.groupId).forEach(box => box.checked = group.checked);
        update();
    }));
    toggles.forEach(toggle => toggle.addEventListener('click', () => {
        const expanded = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', String(!expanded));
        document.querySelectorAll('[data-period-row="' + toggle.getAttribute('aria-controls') + '"]')
            .forEach(row => row.classList.toggle('hidden', expanded));
        toggle.querySelector('[data-period-icon]').textContent = expanded ? '＋' : '−';
    }));
    all.addEventListener('change', () => { boxes.forEach(box => box.checked = all.checked); update(); });
    update();
});
</script>
@endpush
