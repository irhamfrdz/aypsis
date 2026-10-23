@php($entry = $jurnalOppOpt ?? null)

@if(($mode ?? 'manual') === 'voyage')
    <input type="hidden" name="mode" value="voyage">
    <div class="mb-6 rounded-xl border border-blue-100 bg-blue-50/50 p-5">
        <div class="mb-4">
            <h2 class="font-bold text-gray-900">Referensi Manifest Voyage</h2>
            <p class="mt-1 text-xs text-gray-600">Kontainer LCL yang tercatat pada beberapa vendor akan ditampilkan satu kali.</p>
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
            <div>
                <label for="nomor_kontainer" class="mb-2 block text-sm font-semibold text-gray-700">Nomor Kontainer</label>
                <select id="nomor_kontainer" name="nomor_kontainer" disabled class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Opsional: pilih kontainer</option>
                </select>
                @error('nomor_kontainer')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="nomor_bl" class="mb-2 block text-sm font-semibold text-gray-700">Nomor BL</label>
                <select id="nomor_bl" name="nomor_bl" disabled class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Opsional: pilih BL</option>
                </select>
                @error('nomor_bl')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
    <script>
        (() => {
            const ship = document.getElementById('nama_kapal');
            const voyage = document.getElementById('no_voyage');
            const container = document.getElementById('nomor_kontainer');
            const bl = document.getElementById('nomor_bl');
            const endpoint = @json(route('jurnal-opp-opt.manifest-options'));
            const selectedVoyage = @json(old('no_voyage', $entry?->no_voyage));
            const selectedContainer = @json(old('nomor_kontainer', $entry?->nomor_kontainer));
            const selectedBl = @json(old('nomor_bl', $entry?->nomor_bl));
            const reset = (select, placeholder) => {
                select.innerHTML = '';
                select.add(new Option(placeholder, ''));
                select.disabled = true;
            };
            const fill = (select, values, placeholder, selected = '') => {
                reset(select, placeholder);
                values.forEach((value) => select.add(new Option(value, value)));
                select.disabled = false;
                if (selected && values.includes(selected)) select.value = selected;
            };
            const loadVoyages = async (restore = false) => {
                reset(voyage, ship.value ? 'Memuat voyage...' : 'Pilih kapal terlebih dahulu');
                reset(container, 'Pilih voyage terlebih dahulu');
                reset(bl, 'Pilih voyage terlebih dahulu');
                if (!ship.value) return;
                const url = new URL(endpoint, window.location.origin);
                url.searchParams.set('nama_kapal', ship.value);
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                const data = await response.json();
                fill(voyage, data.voyages || [], 'Pilih nomor voyage', restore ? selectedVoyage : '');
                if (restore && selectedVoyage && data.voyages?.includes(selectedVoyage)) await loadManifestChoices(true);
            };
            const loadManifestChoices = async (restore = false) => {
                reset(container, 'Memuat kontainer...');
                reset(bl, 'Memuat BL...');
                if (!ship.value || !voyage.value) return;
                const url = new URL(endpoint, window.location.origin);
                url.searchParams.set('nama_kapal', ship.value);
                url.searchParams.set('no_voyage', voyage.value);
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                const data = await response.json();
                fill(container, data.containers || [], 'Opsional: pilih kontainer', restore ? selectedContainer : '');
                fill(bl, data.bls || [], 'Opsional: pilih BL', restore ? selectedBl : '');
            };
            ship.addEventListener('change', () => loadVoyages());
            voyage.addEventListener('change', () => loadManifestChoices());
            if (ship.value) loadVoyages(true);
        })();
    </script>
@else
    <input type="hidden" name="mode" value="manual">
@endif

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
