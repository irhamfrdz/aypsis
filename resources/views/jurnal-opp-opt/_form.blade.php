@php($entry = $jurnalOppOpt ?? null)

@if(($mode ?? 'manual') === 'voyage')
    <input type="hidden" name="mode" value="voyage">
    <div class="mb-6 rounded-xl border border-blue-100 bg-blue-50/50 p-5">
        <div class="mb-4">
            <h2 class="font-bold text-gray-900">Referensi Manifest Voyage</h2>
            <p class="mt-1 text-xs text-gray-600">Pilih pasangan kontainer / BL dari manifest. Biaya dihitung satu kali per kontainer, termasuk LCL dengan beberapa vendor.</p>
        </div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="nama_kapal" class="mb-2 block text-sm font-semibold text-gray-700">Kapal <span class="text-red-500">*</span></label>
                <select id="nama_kapal" name="nama_kapal" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih kapal</option>
                    @foreach($kapals ?? [] as $kapal)
                        <option value="{{ $kapal }}" @selected(old('nama_kapal', $entry?->nama_kapal) === $kapal)>{{ $kapal }}</option>
                    @endforeach
                </select>
                @error('nama_kapal')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="no_voyage" class="mb-2 block text-sm font-semibold text-gray-700">Nomor Voyage <span class="text-red-500">*</span></label>
                <select id="no_voyage" name="no_voyage" required disabled class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih kapal terlebih dahulu</option>
                </select>
                @error('no_voyage')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <div class="mb-2 flex items-center justify-between gap-3">
                    <label class="block text-sm font-semibold text-gray-700">Kontainer / BL</label>
                    <span id="manifest_reference_count" class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">0 kontainer</span>
                </div>
                <div id="manifest_reference_list" class="max-h-56 min-h-14 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="px-4 py-4 text-sm text-gray-400">Pilih kapal dan voyage untuk menampilkan manifest.</div>
                </div>
                <div id="manifest_reference_inputs"></div>
                <p class="mt-2 flex items-start gap-2 text-xs text-gray-500"><i class="fas fa-circle-info mt-0.5 text-blue-500"></i><span>Pilih satu atau lebih pasangan. Saldo dan kredit dibagi rata per kontainer.</span></p>
                @error('manifest_references')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label for="vendor" class="mb-2 block text-sm font-semibold text-gray-700">Vendor</label>
                <input type="text" id="vendor" name="vendor" maxlength="255" value="{{ old('vendor', $entry?->vendor) }}" placeholder="Masukkan nama vendor (opsional)" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                @error('vendor')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
    <script>
        (() => {
            const ship = document.getElementById('nama_kapal');
            const voyage = document.getElementById('no_voyage');
            const referenceList = document.getElementById('manifest_reference_list');
            const referenceInputs = document.getElementById('manifest_reference_inputs');
            const referenceCount = document.getElementById('manifest_reference_count');
            const endpoint = @json(route('jurnal-opp-opt.manifest-options'));
            const selectedVoyage = @json(old('no_voyage', $entry?->no_voyage));
            const selectedReferencesValue = @json(old('manifest_references', $selectedManifestReferences ?? []));
            const selectedReferences = Array.isArray(selectedReferencesValue) ? selectedReferencesValue : [];
            const reset = (select, placeholder) => {
                select.innerHTML = '';
                if (!select.multiple) select.add(new Option(placeholder, ''));
                select.disabled = true;
            };
            const clearReferences = (message) => {
                referenceList.innerHTML = `<div class="px-4 py-4 text-sm text-gray-400">${message}</div>`;
                referenceInputs.innerHTML = '';
                referenceCount.textContent = '0 kontainer';
            };
            const syncReferenceInputs = () => {
                const selected = [...referenceList.querySelectorAll('input[type="checkbox"]:checked')];
                referenceInputs.innerHTML = '';
                const allocationGroups = new Set();
                selected.forEach((checkbox) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'manifest_references[]';
                    input.value = checkbox.value;
                    referenceInputs.appendChild(input);
                    const reference = JSON.parse(checkbox.value);
                    allocationGroups.add(reference.nomor_kontainer
                        ? `container:${reference.nomor_kontainer}`
                        : `bl:${reference.nomor_bl || ''}`);
                });
                referenceCount.textContent = `${allocationGroups.size} kontainer`;
            };
            const fill = (select, values, placeholder, selected = '') => {
                reset(select, placeholder);
                values.forEach((value) => select.add(new Option(value, value)));
                select.disabled = false;
                if (selected && values.includes(selected)) select.value = selected;
            };
            const loadVoyages = async (restore = false) => {
                reset(voyage, ship.value ? 'Memuat voyage...' : 'Pilih kapal terlebih dahulu');
                clearReferences('Pilih kapal dan voyage untuk menampilkan manifest.');
                if (!ship.value) return;
                const url = new URL(endpoint, window.location.origin);
                url.searchParams.set('nama_kapal', ship.value);
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                const data = await response.json();
                fill(voyage, data.voyages || [], 'Pilih nomor voyage', restore ? selectedVoyage : '');
                if (restore && selectedVoyage && data.voyages?.includes(selectedVoyage)) await loadManifestChoices(true);
            };
            const loadManifestChoices = async (restore = false) => {
                clearReferences('Memuat daftar kontainer dan BL...');
                if (!ship.value || !voyage.value) return;
                const url = new URL(endpoint, window.location.origin);
                url.searchParams.set('nama_kapal', ship.value);
                url.searchParams.set('no_voyage', voyage.value);
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                const data = await response.json();
                const references = data.references || [];
                referenceList.innerHTML = '';
                if (references.length === 0) {
                    clearReferences('Tidak ada kontainer atau BL pada voyage ini.');
                    return;
                }
                references.forEach((item) => {
                    const containerLabel = item.nomor_kontainer || 'Tanpa nomor kontainer';
                    const blLabel = item.nomor_bl || 'Tanpa nomor BL';
                    const optionValue = JSON.stringify(item);
                    const row = document.createElement('label');
                    row.className = 'flex cursor-pointer items-center gap-3 border-b border-gray-100 px-4 py-3 transition last:border-b-0 hover:bg-blue-50';
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.value = optionValue;
                    checkbox.className = 'h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500';
                    checkbox.checked = restore && selectedReferences.some((encoded) => {
                        try {
                            const selected = typeof encoded === 'string' ? JSON.parse(encoded) : encoded;
                            return (selected.nomor_kontainer || '') === (item.nomor_kontainer || '')
                                && (selected.nomor_bl || '') === (item.nomor_bl || '');
                        } catch (error) {
                            return false;
                        }
                    });
                    checkbox.addEventListener('change', syncReferenceInputs);
                    const labels = document.createElement('span');
                    labels.className = 'min-w-0 flex-1';
                    labels.innerHTML = `<span class="block truncate text-sm font-semibold text-gray-800"></span><span class="mt-0.5 block text-xs text-gray-500"></span>`;
                    labels.children[0].textContent = containerLabel;
                    labels.children[1].textContent = `BL: ${blLabel}`;
                    row.append(checkbox, labels);
                    referenceList.appendChild(row);
                });
                syncReferenceInputs();
            };
            ship.addEventListener('change', () => loadVoyages());
            voyage.addEventListener('change', () => loadManifestChoices());
            if (ship.value) loadVoyages(true);
        })();
    </script>
@else
    <input type="hidden" name="mode" value="manual">
    <div class="mb-6">
        <label for="vendor" class="mb-2 block text-sm font-semibold text-gray-700">Vendor</label>
        <input type="text" id="vendor" name="vendor" maxlength="255" value="{{ old('vendor', $entry?->vendor) }}" placeholder="Nama vendor (opsional)" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
        @error('vendor')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
@endif

<div class="mb-6">
    <label for="klasifikasi_biaya_id" class="mb-2 block text-sm font-semibold text-gray-700">Klasifikasi Biaya</label>
    <select id="klasifikasi_biaya_id" name="klasifikasi_biaya_id" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
        <option value="">Pilih klasifikasi biaya (opsional)</option>
        @foreach($klasifikasiBiayas ?? [] as $klasifikasi)
            <option value="{{ $klasifikasi->id }}" @selected((string) old('klasifikasi_biaya_id', $entry?->klasifikasi_biaya_id) === (string) $klasifikasi->id)>{{ $klasifikasi->kode }} — {{ $klasifikasi->nama }}</option>
        @endforeach
    </select>
    @error('klasifikasi_biaya_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
        <input type="date" id="tanggal" name="tanggal" required
               value="{{ old('tanggal', $entry?->tanggal?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
               class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
        @error('tanggal')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="nomor_referensi" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Referensi</label>
        <input type="text" id="nomor_referensi" name="nomor_referensi" maxlength="100"
               value="{{ old('nomor_referensi', $entry?->nomor_referensi) }}" placeholder="Opsional"
               class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
        @error('nomor_referensi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="saldo" class="block text-sm font-semibold text-gray-700 mb-2">Saldo (Penambahan)</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
            <input type="number" id="saldo" name="saldo" min="0" step="0.01"
                   value="{{ old('saldo', $entry?->saldo ?? 0) }}"
                   class="w-full pl-10 rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <p class="mt-1 text-xs text-gray-500">Menambah saldo berjalan.</p>
        @error('saldo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="kredit" class="block text-sm font-semibold text-gray-700 mb-2">Kredit (Pengurangan)</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
            <input type="number" id="kredit" name="kredit" min="0" step="0.01"
                   value="{{ old('kredit', $entry?->kredit ?? 0) }}"
                   class="w-full pl-10 rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <p class="mt-1 text-xs text-gray-500">Mengurangi saldo berjalan.</p>
        @error('kredit')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">Keterangan <span class="text-red-500">*</span></label>
        <textarea id="keterangan" name="keterangan" rows="4" required maxlength="2000"
                  class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                  placeholder="Tuliskan keterangan transaksi">{{ old('keterangan', $entry?->keterangan) }}</textarea>
        @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-6">
    <a href="{{ route('jurnal-opp-opt.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
    <button type="submit" class="px-5 py-2.5 rounded-lg bg-emerald-600 text-sm font-semibold text-white hover:bg-emerald-700 shadow-sm">
        {{ $submitLabel }}
    </button>
</div>
