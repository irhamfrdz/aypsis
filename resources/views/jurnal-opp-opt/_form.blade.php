@php($entry = $jurnalOppOpt ?? null)

@if(($mode ?? 'manual') === 'voyage')
    <input type="hidden" name="mode" value="voyage">
    <div class="rounded-2xl border border-blue-200 bg-blue-50/40 p-5 shadow-sm sm:p-6">
        <div class="mb-6 flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700"><i class="fas fa-ship"></i></div>
            <div>
                <h2 class="text-base font-bold text-slate-900">Referensi Manifest Voyage</h2>
                <p class="mt-1 text-sm leading-5 text-slate-600">Pilih kapal dan voyage, lalu tandai kontainer / BL yang terkait.</p>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="nama_kapal" class="mb-2 block text-sm font-semibold text-slate-700">Kapal <span class="text-rose-600">*</span></label>
                <select id="nama_kapal" name="nama_kapal" required class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">Pilih kapal</option>
                    @foreach($kapals ?? [] as $kapal)
                        <option value="{{ $kapal }}" @selected(old('nama_kapal', $entry?->nama_kapal) === $kapal)>{{ $kapal }}</option>
                    @endforeach
                </select>
                @error('nama_kapal')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="no_voyage" class="mb-2 block text-sm font-semibold text-slate-700">Nomor Voyage <span class="text-rose-600">*</span></label>
                <select id="no_voyage" name="no_voyage" required disabled class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500">
                    <option value="">Pilih kapal terlebih dahulu</option>
                </select>
                @error('no_voyage')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <div class="mb-2 flex items-center justify-between gap-3">
                    <label class="block text-sm font-semibold text-slate-700">Kontainer / BL</label>
                    <span id="manifest_reference_count" class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-200">0 kontainer</span>
                </div>
                <div class="relative mb-2">
                    <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    <input type="search" id="manifest_reference_search" disabled placeholder="Cari nomor kontainer atau BL" class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:cursor-not-allowed disabled:bg-slate-100">
                </div>
                <div class="mb-2 flex justify-end">
                    <button type="button" id="manifest_reference_select_all" disabled class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-white px-3 py-2 text-xs font-semibold text-blue-700 shadow-sm transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 disabled:cursor-not-allowed disabled:opacity-50">
                        <i class="fas fa-check-double"></i>Pilih semua
                    </button>
                </div>
                <div id="manifest_reference_list" class="max-h-56 min-h-14 overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="px-4 py-4 text-sm text-slate-500">Pilih kapal dan voyage untuk menampilkan manifest.</div>
                </div>
                <div id="manifest_reference_inputs"></div>
                <p class="mt-2 flex items-start gap-2 text-xs leading-5 text-slate-600"><i class="fas fa-circle-info mt-0.5 text-blue-500"></i><span>Anda bisa memilih beberapa pasangan. Nilai transaksi dibagi rata per kontainer, termasuk LCL dengan beberapa vendor.</span></p>
                @error('manifest_references')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
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
            const referenceSearch = document.getElementById('manifest_reference_search');
            const selectAllReferences = document.getElementById('manifest_reference_select_all');
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
                referenceList.innerHTML = `<div class="px-4 py-4 text-sm text-slate-500">${message}</div>`;
                referenceInputs.innerHTML = '';
                referenceCount.textContent = '0 kontainer';
                referenceSearch.value = '';
                referenceSearch.disabled = true;
                selectAllReferences.disabled = true;
                document.dispatchEvent(new Event('jurnal-opp-opt:references-changed'));
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
                document.dispatchEvent(new Event('jurnal-opp-opt:references-changed'));
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
                referenceSearch.disabled = false;
                selectAllReferences.disabled = false;
                references.forEach((item) => {
                    const containerLabel = item.nomor_kontainer || 'Tanpa nomor kontainer';
                    const blLabel = item.nomor_bl || 'Tanpa nomor BL';
                    const optionValue = JSON.stringify({
                        nomor_kontainer: item.nomor_kontainer,
                        nomor_bl: item.nomor_bl,
                    });
                    const row = document.createElement('label');
                    row.className = 'flex cursor-pointer items-center gap-3 border-b border-slate-100 px-4 py-3 transition last:border-b-0 hover:bg-blue-50 focus-within:bg-blue-50';
                    row.dataset.search = `${containerLabel} ${blLabel}`.toLocaleLowerCase();
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.value = optionValue;
                    checkbox.dataset.size = item.size_kontainer || '';
                    checkbox.dataset.status = item.status_kontainer || '';
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
                    row.classList.toggle('bg-blue-50', checkbox.checked);
                    checkbox.addEventListener('change', () => {
                        row.classList.toggle('bg-blue-50', checkbox.checked);
                        syncReferenceInputs();
                    });
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
            referenceSearch.addEventListener('input', () => {
                const search = referenceSearch.value.trim().toLocaleLowerCase();
                referenceList.querySelectorAll('label[data-search]').forEach((row) => {
                    row.classList.toggle('hidden', !row.dataset.search.includes(search));
                });
            });
            selectAllReferences.addEventListener('click', () => {
                referenceList.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
                    checkbox.checked = true;
                    checkbox.closest('label')?.classList.add('bg-blue-50');
                });
                syncReferenceInputs();
            });
            if (ship.value) loadVoyages(true);
        })();
    </script>
@else
    <input type="hidden" name="mode" value="manual">
@endif

<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="mb-6 flex items-start gap-3">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"><i class="fas fa-file-invoice-dollar"></i></div>
        <div>
            <h2 class="text-base font-bold text-slate-900">Detail Transaksi</h2>
            <p class="mt-1 text-sm text-slate-500">Lengkapi kategori, vendor, dan tanggal pencatatan.</p>
        </div>
    </div>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <label for="klasifikasi_biaya_id" class="mb-2 block text-sm font-semibold text-slate-700">Klasifikasi Biaya <span class="font-normal text-slate-500">(opsional)</span></label>
            <select id="klasifikasi_biaya_id" name="klasifikasi_biaya_id" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                <option value="">Pilih klasifikasi biaya</option>
                @foreach($klasifikasiBiayas ?? [] as $klasifikasi)
                    <option value="{{ $klasifikasi->id }}" data-penumpukan="{{ stripos($klasifikasi->nama, 'biaya penumpukan') !== false ? '1' : '0' }}" @selected((string) old('klasifikasi_biaya_id', $entry?->klasifikasi_biaya_id) === (string) $klasifikasi->id)>{{ $klasifikasi->kode }} — {{ $klasifikasi->nama }}</option>
                @endforeach
            </select>
            @error('klasifikasi_biaya_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="vendor" class="mb-2 block text-sm font-semibold text-slate-700">Vendor <span class="font-normal text-slate-500">(opsional)</span></label>
            <input type="text" id="vendor" name="vendor" maxlength="255" value="{{ old('vendor', $entry?->vendor) }}" placeholder="Masukkan nama vendor" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
            @error('vendor')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div id="penumpukan_date_range" class="hidden rounded-xl border border-amber-200 bg-amber-50/60 p-4 sm:col-span-2">
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-amber-950">Periode Penumpukan</h3>
                <p class="mt-1 text-xs text-amber-800">Hari mulai dan selesai dihitung. Satu massa berlaku untuk setiap 3 hari.</p>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label for="tanggal_mulai_penumpukan" class="mb-2 block text-sm font-semibold text-slate-700">Dari Tanggal <span class="text-rose-600">*</span></label>
                    <input type="date" id="tanggal_mulai_penumpukan" name="tanggal_mulai_penumpukan" value="{{ old('tanggal_mulai_penumpukan', $entry?->tanggal_mulai_penumpukan?->format('Y-m-d')) }}" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                    @error('tanggal_mulai_penumpukan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="tanggal_selesai_penumpukan" class="mb-2 block text-sm font-semibold text-slate-700">Sampai Tanggal <span class="text-rose-600">*</span></label>
                    <input type="date" id="tanggal_selesai_penumpukan" name="tanggal_selesai_penumpukan" value="{{ old('tanggal_selesai_penumpukan', $entry?->tanggal_selesai_penumpukan?->format('Y-m-d')) }}" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                    @error('tanggal_selesai_penumpukan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="massa_penumpukan" class="mb-2 block text-sm font-semibold text-slate-700">Massa</label>
                    <input type="number" id="massa_penumpukan" value="{{ old('massa', $entry?->massa) }}" min="1" readonly placeholder="Otomatis dari tanggal" class="block w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-700">
                </div>
                <div>
                    <label for="status_kontainer" class="mb-2 block text-sm font-semibold text-slate-700">Status Kontainer</label>
                    <select id="status_kontainer" name="status_kontainer" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                        <option value="">Otomatis dari manifest</option>
                        <option value="full" @selected(old('status_kontainer', $entry?->status_kontainer) === 'full')>Full</option>
                        <option value="empty" @selected(old('status_kontainer', $entry?->status_kontainer) === 'empty')>Empty</option>
                    </select>
                    @error('status_kontainer')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <p id="penumpukan_tarif_hint" class="mt-3 text-xs leading-5 text-amber-800" aria-live="polite"></p>
        </div>
        <div>
            <label for="tanggal" class="mb-2 block text-sm font-semibold text-slate-700">Tanggal <span class="text-rose-600">*</span></label>
            <input type="date" id="tanggal" name="tanggal" required value="{{ old('tanggal', $entry?->tanggal?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
            @error('tanggal')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="nomor_referensi" class="mb-2 block text-sm font-semibold text-slate-700">Nomor Referensi <span class="font-normal text-slate-500">(opsional)</span></label>
            <input type="text" id="nomor_referensi" name="nomor_referensi" maxlength="100" value="{{ old('nomor_referensi', $entry?->nomor_referensi) }}" placeholder="Opsional" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
            @error('nomor_referensi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const classification = document.getElementById('klasifikasi_biaya_id');
        const dateRange = document.getElementById('penumpukan_date_range');
        const start = document.getElementById('tanggal_mulai_penumpukan');
        const end = document.getElementById('tanggal_selesai_penumpukan');
        const mass = document.getElementById('massa_penumpukan');
        const statusOverride = document.getElementById('status_kontainer');
        const referenceList = document.getElementById('manifest_reference_list');
        const credit = document.getElementById('kredit');
        const hint = document.getElementById('penumpukan_tarif_hint');
        const storageTarifs = @json($storageTarifs ?? []);
        let autoCreditCents = null;
        let creditEdited = false;

        const clearAutoCredit = () => {
            if (autoCreditCents !== null && Math.round(Number(credit.value || 0) * 100) === autoCreditCents) {
                credit.value = '0';
            }
            autoCreditCents = null;
        };

        const updateAutoCredit = () => {
            if (classification.selectedOptions[0]?.dataset.penumpukan !== '1') {
                clearAutoCredit();
                hint.textContent = '';
                return;
            }
            if (!mass.value) {
                clearAutoCredit();
                hint.textContent = 'Lengkapi periode penumpukan untuk menghitung tarif.';
                return;
            }
            if (!referenceList) {
                clearAutoCredit();
                hint.textContent = 'Pada transaksi tanpa voyage, isi kredit secara manual.';
                return;
            }

            const selected = [...referenceList.querySelectorAll('input[type="checkbox"]:checked')];
            if (!selected.length) {
                clearAutoCredit();
                hint.textContent = 'Pilih kontainer / BL agar kredit dapat dihitung otomatis.';
                return;
            }

            const containers = new Map();
            selected.forEach((checkbox) => {
                const reference = JSON.parse(checkbox.value);
                const key = reference.nomor_kontainer
                    ? `container:${reference.nomor_kontainer}`
                    : `bl:${reference.nomor_bl || ''}`;
                const size = checkbox.dataset.size.match(/20|40|45/)?.[0] || null;
                const status = statusOverride.value || checkbox.dataset.status || null;
                if (containers.has(key)) {
                    const container = containers.get(key);
                    if (size && container.size && container.size !== size) container.sizeConflict = true;
                    if (status && container.status && container.status !== status) container.statusConflict = true;
                    if (size && !container.sizeConflict) container.size = size;
                    if (status && !container.statusConflict) container.status = status;
                } else {
                    containers.set(key, { size, status, sizeConflict: false, statusConflict: false });
                }
            });

            if ([...containers.values()].some((container) =>
                !container.size || !container.status || container.sizeConflict || container.statusConflict
            )) {
                clearAutoCredit();
                hint.textContent = 'Ukuran atau status salah satu kontainer tidak tersedia. Pilih status Full/Empty jika perlu, lalu isi kredit manual bila tarif tetap tidak ditemukan.';
                return;
            }

            let totalCents = 0;
            for (const container of containers.values()) {
                const pricelist = storageTarifs.find((item) =>
                    String(item.size).includes(container.size)
                    && String(item.status).toLowerCase() === container.status
                );
                const rate = pricelist?.tarif_massa.find((item) => Number(item.massa) === Number(mass.value))?.tarif;
                if (rate === undefined || rate === null) {
                    clearAutoCredit();
                    hint.textContent = 'Tarif massa ini belum tersedia untuk salah satu kontainer. Isi kredit secara manual.';
                    return;
                }
                totalCents += Math.round(Number(rate) * 100);
            }

            const currentCents = Math.round(Number(credit.value || 0) * 100);
            if (autoCreditCents === null && currentCents > 0 && currentCents !== totalCents) {
                creditEdited = true;
            }
            if (!creditEdited) {
                credit.value = (totalCents / 100).toFixed(2);
                autoCreditCents = totalCents;
            }
            const formatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' })
                .format(totalCents / 100);
            hint.textContent = creditEdited
                ? `Tarif otomatis ${formatted} untuk ${containers.size} kontainer. Kredit saat ini diisi manual.`
                : `Kredit otomatis ${formatted} untuk ${containers.size} kontainer, dihitung sekali per kontainer.`;
        };

        const updateMass = () => {
            end.min = start.value;
            const days = start.value && end.value
                ? Math.round((Date.parse(`${end.value}T00:00:00Z`) - Date.parse(`${start.value}T00:00:00Z`)) / 86400000) + 1
                : 0;
            mass.value = days > 0 ? Math.ceil(days / 3) : '';
            updateAutoCredit();
        };
        const updateVisibility = () => {
            const visible = classification.selectedOptions[0]?.dataset.penumpukan === '1';
            dateRange.classList.toggle('hidden', !visible);
            [start, end].forEach((input) => {
                input.disabled = !visible;
                input.required = visible;
            });
            statusOverride.disabled = !visible;
            if (visible) updateMass();
            else updateAutoCredit();
        };

        classification.addEventListener('change', updateVisibility);
        start.addEventListener('change', updateMass);
        end.addEventListener('change', updateMass);
        statusOverride.addEventListener('change', updateAutoCredit);
        document.addEventListener('jurnal-opp-opt:references-changed', updateAutoCredit);
        credit.addEventListener('input', () => {
            autoCreditCents = null;
            creditEdited = Number(credit.value || 0) > 0;
            updateAutoCredit();
        });
        updateVisibility();
    });
</script>

<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="mb-6 flex items-start gap-3">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700"><i class="fas fa-coins"></i></div>
        <div>
            <h2 class="text-base font-bold text-slate-900">Nominal dan Keterangan</h2>
            <p class="mt-1 text-sm text-slate-500">Isi nilai penambahan atau pengurangan, lalu jelaskan transaksi.</p>
        </div>
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4">
            <label for="saldo" class="mb-2 block text-sm font-semibold text-emerald-900">Saldo (Penambahan)</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm font-semibold text-slate-500">Rp</span>
                <input type="number" id="saldo" name="saldo" min="0" step="0.01" value="{{ old('saldo', $entry?->saldo ?? 0) }}" class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
            </div>
            <p class="mt-2 text-xs text-slate-600">Menambah saldo berjalan.</p>
            @error('saldo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="rounded-xl border border-rose-200 bg-rose-50/50 p-4">
            <label for="kredit" class="mb-2 block text-sm font-semibold text-rose-900">Kredit (Pengurangan)</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm font-semibold text-slate-500">Rp</span>
                <input type="number" id="kredit" name="kredit" min="0" step="0.01" value="{{ old('kredit', $entry?->kredit ?? 0) }}" class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-900 shadow-sm focus:border-rose-500 focus:ring-2 focus:ring-rose-100">
            </div>
            <p class="mt-2 text-xs text-slate-600">Mengurangi saldo berjalan. Untuk penumpukan, tarif terisi dari massa dan kontainer terpilih.</p>
            @error('kredit')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
    <div class="mt-5">
        <label for="keterangan" class="mb-2 block text-sm font-semibold text-slate-700">Keterangan <span class="text-rose-600">*</span></label>
        <textarea id="keterangan" name="keterangan" rows="4" required maxlength="2000" placeholder="Tuliskan keterangan transaksi" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">{{ old('keterangan', $entry?->keterangan) }}</textarea>
        @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</section>

<div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
    <a href="{{ route('jurnal-opp-opt.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">Batal</a>
    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
        <i class="fas fa-save"></i>{{ $submitLabel }}
    </button>
</div>
