@extends('layouts.app')

@section('title', 'Approval Tanda Terima Pelabuhan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Approval Tanda Terima Pelabuhan</h1>
        <p class="mt-1 text-sm text-gray-600">Klik Setujui untuk mengisi tanggal, nama barang, supir pengganti, lembur/nginap, dan gudang tujuan.</p>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('approval-tanda-terima-pelabuhan.index') }}" class="flex flex-col gap-3 border-b border-gray-200 p-4 md:flex-row md:items-end">
            <label class="flex-1 text-sm font-medium text-gray-700">Cari no. surat jalan, kontainer, atau supir
                <input type="search" name="search" value="{{ $search }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Cari tanda terima">
            </label>
            <label class="text-sm font-medium text-gray-700">Status
                <select name="status" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="pending" @selected($status === 'pending')>Menunggu approval</option>
                    <option value="approved" @selected($status === 'approved')>Disetujui</option>
                    <option value="all" @selected($status === 'all')>Semua</option>
                </select>
            </label>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Tampilkan</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Surat jalan</th>
                        <th class="px-4 py-3">Kontainer / Supir</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tanggal tanda terima</th>
                        <th class="px-4 py-3">Tanggal checkpoint supir</th>
                        <th class="px-4 py-3">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tandaTerimas as $tandaTerima)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                @can('tanda-terima-view')
                                    <a href="{{ route('tanda-terima.show', $tandaTerima) }}" class="text-blue-700 hover:underline">{{ $tandaTerima->no_surat_jalan ?: 'TT #' . $tandaTerima->id }}</a>
                                @else
                                    {{ $tandaTerima->no_surat_jalan ?: 'TT #' . $tandaTerima->id }}
                                @endcan
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                <div>{{ $tandaTerima->no_kontainer ?: '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $tandaTerima->supir ?: '-' }}</div>
                                @if($tandaTerima->supir_pengganti)
                                    <div class="text-xs text-gray-500">Pengganti: {{ $tandaTerima->supir_pengganti }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($tandaTerima->approval_pelabuhan_status === 'approved')
                                    <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">Disetujui</span>
                                @else
                                    <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-medium text-amber-700">Menunggu</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                {{ $tandaTerima->tanggal?->format('d/m/Y') ?: '-' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $tandaTerima->tanggal_checkpoint_supir?->format('d/m/Y') ?: '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @can('approval-tanda-terima-pelabuhan-approve')
                                    <button type="button" class="open-approval-modal whitespace-nowrap rounded-lg bg-green-600 px-3 py-2 font-medium text-white hover:bg-green-700"
                                        data-id="{{ $tandaTerima->id }}"
                                        data-action="{{ route('approval-tanda-terima-pelabuhan.approve', $tandaTerima) }}"
                                        data-number="{{ $tandaTerima->no_surat_jalan ?: 'TT #' . $tandaTerima->id }}"
                                        data-tanggal="{{ $tandaTerima->tanggal?->format('Y-m-d') }}"
                                        data-checkpoint="{{ $tandaTerima->tanggal_checkpoint_supir?->format('Y-m-d') }}"
                                        data-supir-pengganti="{{ $tandaTerima->supir_pengganti }}"
                                        data-dimensi="{{ json_encode($tandaTerima->approval_dimensi) }}"
                                        data-lembur="{{ ($tandaTerima->approval_pelabuhan_status === 'pending' ? $tandaTerima->suratJalan?->lembur : $tandaTerima->lembur) ? '1' : '0' }}"
                                        data-nginap="{{ ($tandaTerima->approval_pelabuhan_status === 'pending' ? $tandaTerima->suratJalan?->nginap : $tandaTerima->nginap) ? '1' : '0' }}"
                                        data-tidak-lembur-nginap="{{ ($tandaTerima->approval_pelabuhan_status === 'pending' ? $tandaTerima->suratJalan?->tidak_lembur_nginap : $tandaTerima->tidak_lembur_nginap) ? '1' : '0' }}"
                                        data-gudang-id="{{ $tandaTerima->gudang_tujuan_id }}"
                                        data-requires-gudang="{{ strtolower((string) ($tandaTerima->suratJalan?->tipe_kontainer ?? $tandaTerima->tipe_kontainer)) === 'cargo' ? '0' : '1' }}"
                                        data-approved="{{ $tandaTerima->approval_pelabuhan_status === 'approved' ? '1' : '0' }}">{{ $tandaTerima->approval_pelabuhan_status === 'approved' ? 'Ubah persetujuan' : 'Setujui' }}</button>
                                @else
                                    <span class="text-gray-400">Lihat saja</span>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">Tidak ada tanda terima untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-200 p-4">{{ $tandaTerimas->links() }}</div>
    </div>

    @can('approval-tanda-terima-pelabuhan-approve')
        <dialog id="approval-pelabuhan-modal" class="m-auto max-h-[90vh] w-[95vw] max-w-5xl overflow-y-auto rounded-xl border border-gray-200 p-0 shadow-2xl backdrop:bg-black/50"
            aria-labelledby="approval-pelabuhan-title" data-reopen-id="{{ old('approval_tanda_terima_id') }}"
            data-old-tanggal="{{ old('tanggal') }}" data-old-checkpoint="{{ old('tanggal_checkpoint_supir') }}"
            data-old-supir-pengganti="{{ old('supir_pengganti') }}"
            data-old-dimensi="{{ json_encode(array_values(old('dimensi', []))) }}"
            data-old-lembur="{{ old('lembur') ? '1' : '0' }}" data-old-nginap="{{ old('nginap') ? '1' : '0' }}"
            data-old-tidak-lembur-nginap="{{ old('tidak_lembur_nginap') ? '1' : '0' }}"
            data-old-gudang-id="{{ old('gudang_id') }}">
            <form id="approval-pelabuhan-form" method="POST" class="p-6">
                @csrf
                @method('PATCH')
                <input type="hidden" name="approval_tanda_terima_id" id="approval-tanda-terima-id">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="hidden" name="search" value="{{ $search }}">

                <h2 id="approval-pelabuhan-title" class="text-lg font-semibold text-gray-900">Setujui tanda terima</h2>
                <p id="approval-pelabuhan-number" class="mt-1 text-sm text-gray-600"></p>

                @if($errors->any())
                    <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="mt-5 space-y-4">
                    <label for="approval-tanggal" class="block text-sm font-medium text-gray-700">Tanggal tanda terima
                        <input id="approval-tanggal" type="date" name="tanggal" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </label>
                    <label for="approval-checkpoint" class="block text-sm font-medium text-gray-700">Tanggal checkpoint supir
                        <input id="approval-checkpoint" type="date" name="tanggal_checkpoint_supir" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </label>
                    <label for="approval-supir-pengganti" class="block text-sm font-medium text-gray-700">Supir pengganti <span class="font-normal text-gray-500">(opsional)</span>
                        <input id="approval-supir-pengganti" type="text" name="supir_pengganti" maxlength="255" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Nama supir pengganti">
                    </label>
                    <section class="rounded-lg border border-purple-200 bg-purple-50 p-4" aria-labelledby="approval-dimensi-title">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <h3 id="approval-dimensi-title" class="text-sm font-semibold text-gray-900">Dimensi dan Volume</h3>
                            <button id="approval-add-dimensi" type="button" class="rounded-lg bg-purple-600 px-3 py-2 text-xs font-medium text-white hover:bg-purple-700">+ Tambah Dimensi</button>
                        </div>
                        <div id="approval-dimensi-list" class="space-y-4"></div>
                    </section>
                    <fieldset class="rounded-lg border border-gray-200 p-3">
                        <legend class="px-1 text-sm font-medium text-gray-700">Lembur / Nginap</legend>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-700">
                            <label class="inline-flex items-center gap-2"><input id="approval-lembur" type="checkbox" name="lembur" value="1" class="rounded border-gray-300"> Lembur</label>
                            <label class="inline-flex items-center gap-2"><input id="approval-nginap" type="checkbox" name="nginap" value="1" class="rounded border-gray-300"> Nginap</label>
                            <label class="inline-flex items-center gap-2"><input id="approval-tidak-lembur-nginap" type="checkbox" name="tidak_lembur_nginap" value="1" class="rounded border-gray-300"> Tidak Lembur &amp; Nginap</label>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Pilih minimal satu. Lembur dan Nginap dapat dipilih bersama.</p>
                    </fieldset>
                    <label for="approval-gudang" class="block text-sm font-medium text-gray-700">Gudang Tujuan <span id="approval-gudang-required" class="text-red-600">*</span>
                        <select id="approval-gudang" name="gudang_id" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">-- Pilih Gudang --</option>
                            @foreach($gudangs as $gudang)
                                <option value="{{ $gudang->id }}">{{ $gudang->nama_gudang }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button id="approval-pelabuhan-cancel" type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                    <button id="approval-pelabuhan-submit" type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Setujui</button>
                </div>
            </form>
        </dialog>
    @endcan
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dialog = document.getElementById('approval-pelabuhan-modal');
        if (!dialog) return;

        const form = document.getElementById('approval-pelabuhan-form');
        const buttons = [...document.querySelectorAll('.open-approval-modal')];
        const tanggal = document.getElementById('approval-tanggal');
        const checkpoint = document.getElementById('approval-checkpoint');
        const supirPengganti = document.getElementById('approval-supir-pengganti');
        const dimensiList = document.getElementById('approval-dimensi-list');
        const lembur = document.getElementById('approval-lembur');
        const nginap = document.getElementById('approval-nginap');
        const tidakLemburNginap = document.getElementById('approval-tidak-lembur-nginap');
        const gudang = document.getElementById('approval-gudang');

        let dimensiIndex = 0;

        function updateUkuran(row) {
            const isKeramik = row.querySelector('[data-field="nama_barang"]').value.toLowerCase().includes('keramik');
            row.querySelector('.approval-ukuran').classList.toggle('hidden', !isKeramik);
            row.querySelector('.approval-dimensi-info').classList.toggle('md:grid-cols-4', isKeramik);
            row.querySelector('.approval-dimensi-info').classList.toggle('md:grid-cols-3', !isKeramik);
        }

        function calculateVolume(row) {
            const value = field => Number(row.querySelector(`[data-field="${field}"]`).value);
            const volume = row.querySelector('[data-field="meter_kubik"]');
            volume.value = ['jumlah', 'panjang', 'lebar', 'tinggi'].every(field => value(field) > 0)
                ? (value('jumlah') * value('panjang') * value('lebar') * value('tinggi')).toFixed(3)
                : '';
        }

        function updateRemoveButtons() {
            dimensiList.querySelectorAll('.approval-remove-dimensi').forEach(button => {
                button.classList.toggle('hidden', dimensiList.children.length === 1);
            });
        }

        function addDimensi(values = {}) {
            const index = dimensiIndex++;
            const row = document.createElement('div');
            row.className = 'approval-dimensi-row border-b border-purple-200 pb-4 last:border-b-0 last:pb-0';
            row.innerHTML = `
                <div class="mb-3 flex justify-end"><button type="button" class="approval-remove-dimensi text-xs font-medium text-red-600 hover:underline">Hapus dimensi</button></div>
                <div class="approval-dimensi-info mb-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                    <label class="text-xs font-medium text-gray-600">Nama Barang
                        <input type="text" data-field="nama_barang" name="dimensi[${index}][nama_barang]" maxlength="5000" placeholder="Nama barang" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm font-normal">
                    </label>
                    <label class="approval-ukuran hidden text-xs font-medium text-gray-600">Ukuran
                        <input type="text" data-field="ukuran" name="dimensi[${index}][ukuran]" maxlength="255" placeholder="Contoh: 40x40" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm font-normal">
                    </label>
                    <label class="text-xs font-medium text-gray-600">Jumlah
                        <input type="number" data-field="jumlah" name="dimensi[${index}][jumlah]" min="0" step="1" placeholder="0" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm font-normal">
                    </label>
                    <label class="text-xs font-medium text-gray-600">Satuan
                        <input type="text" data-field="satuan" name="dimensi[${index}][satuan]" maxlength="255" placeholder="Pcs, Kg, Box" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm font-normal">
                    </label>
                </div>
                <div class="grid grid-cols-2 gap-3 md:grid-cols-5">
                    <label class="text-xs font-medium text-gray-600">Panjang (m)
                        <input type="number" data-field="panjang" name="dimensi[${index}][panjang]" min="0" step="0.001" placeholder="0.000" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm font-normal">
                    </label>
                    <label class="text-xs font-medium text-gray-600">Lebar (m)
                        <input type="number" data-field="lebar" name="dimensi[${index}][lebar]" min="0" step="0.001" placeholder="0.000" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm font-normal">
                    </label>
                    <label class="text-xs font-medium text-gray-600">Tinggi (m)
                        <input type="number" data-field="tinggi" name="dimensi[${index}][tinggi]" min="0" step="0.001" placeholder="0.000" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm font-normal">
                    </label>
                    <label class="text-xs font-medium text-gray-600">Volume (m³)
                        <input type="number" data-field="meter_kubik" name="dimensi[${index}][meter_kubik]" min="0" step="0.001" placeholder="0.000" readonly class="mt-1 w-full rounded border border-gray-300 bg-gray-100 px-3 py-2 text-sm font-normal">
                    </label>
                    <label class="text-xs font-medium text-gray-600">Tonase (Ton)
                        <input type="number" data-field="tonase" name="dimensi[${index}][tonase]" min="0" step="0.001" placeholder="0.000" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm font-normal">
                    </label>
                </div>
            `;
            Object.entries(values).forEach(([field, value]) => {
                const input = row.querySelector(`[data-field="${field}"]`);
                if (input && value !== null) input.value = value;
            });
            if (!Object.hasOwn(values, 'tonase') && index === 0) row.querySelector('[data-field="tonase"]').value = '15';
            dimensiList.appendChild(row);
            updateUkuran(row);
            updateRemoveButtons();
        }

        function updateLemburOptions(changed) {
            if (changed === tidakLemburNginap && tidakLemburNginap.checked) {
                lembur.checked = false;
                nginap.checked = false;
            } else if ((changed === lembur || changed === nginap) && (lembur.checked || nginap.checked)) {
                tidakLemburNginap.checked = false;
            }
            lembur.setCustomValidity(lembur.checked || nginap.checked || tidakLemburNginap.checked ? '' : 'Pilih minimal satu opsi lembur/nginap.');
        }

        function openApproval(button, useOldValues = false) {
            form.action = button.dataset.action;
            document.getElementById('approval-tanda-terima-id').value = button.dataset.id;
            document.getElementById('approval-pelabuhan-number').textContent = button.dataset.number;
            document.getElementById('approval-pelabuhan-title').textContent = button.dataset.approved === '1' ? 'Ubah persetujuan tanda terima' : 'Setujui tanda terima';
            document.getElementById('approval-pelabuhan-submit').textContent = button.dataset.approved === '1' ? 'Simpan perubahan' : 'Setujui';
            tanggal.value = useOldValues ? dialog.dataset.oldTanggal : button.dataset.tanggal;
            checkpoint.value = useOldValues ? dialog.dataset.oldCheckpoint : button.dataset.checkpoint;
            supirPengganti.value = useOldValues ? dialog.dataset.oldSupirPengganti : button.dataset.supirPengganti;
            dimensiList.replaceChildren();
            dimensiIndex = 0;
            const dimensi = JSON.parse(useOldValues ? dialog.dataset.oldDimensi : button.dataset.dimensi);
            (Array.isArray(dimensi) && dimensi.length ? dimensi : [{}]).forEach(addDimensi);
            lembur.checked = (useOldValues ? dialog.dataset.oldLembur : button.dataset.lembur) === '1';
            nginap.checked = (useOldValues ? dialog.dataset.oldNginap : button.dataset.nginap) === '1';
            tidakLemburNginap.checked = (useOldValues ? dialog.dataset.oldTidakLemburNginap : button.dataset.tidakLemburNginap) === '1';
            updateLemburOptions(null);
            gudang.value = useOldValues ? dialog.dataset.oldGudangId : button.dataset.gudangId;
            gudang.required = button.dataset.requiresGudang === '1';
            document.getElementById('approval-gudang-required').classList.toggle('hidden', !gudang.required);
            dialog.showModal();
        }

        [lembur, nginap, tidakLemburNginap].forEach(input => input.addEventListener('change', () => updateLemburOptions(input)));
        document.getElementById('approval-add-dimensi').addEventListener('click', () => addDimensi());
        dimensiList.addEventListener('input', event => {
            const row = event.target.closest('.approval-dimensi-row');
            if (!row) return;
            if (event.target.dataset.field === 'nama_barang') updateUkuran(row);
            if (['jumlah', 'panjang', 'lebar', 'tinggi'].includes(event.target.dataset.field)) calculateVolume(row);
        });
        dimensiList.addEventListener('click', event => {
            if (event.target.closest('.approval-remove-dimensi') && dimensiList.children.length > 1) {
                event.target.closest('.approval-dimensi-row').remove();
                updateRemoveButtons();
            }
        });
        buttons.forEach(button => button.addEventListener('click', () => openApproval(button)));
        document.getElementById('approval-pelabuhan-cancel').addEventListener('click', () => dialog.close());
        dialog.addEventListener('click', event => {
            if (event.target === dialog) dialog.close();
        });

        if (dialog.dataset.reopenId) {
            const button = buttons.find(item => item.dataset.id === dialog.dataset.reopenId);
            if (button) openApproval(button, true);
        }
    });
</script>
@endpush
