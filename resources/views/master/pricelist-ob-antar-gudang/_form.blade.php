<form action="{{ $action }}" method="POST" class="space-y-7">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    @if(session('error'))
        <div class="flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <i class="fas fa-circle-exclamation mt-0.5"></i><span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <i class="fas fa-circle-exclamation mt-0.5"></i><span>Periksa kembali data yang diisi.</span>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
            <label for="size_kontainer" class="mb-2 block text-sm font-semibold text-gray-700">Size Kontainer <span class="text-red-500">*</span></label>
            <select id="size_kontainer" name="size_kontainer" required class="block w-full rounded-lg border-gray-300 px-3 py-2.5 text-sm shadow-sm transition focus:border-teal-500 focus:ring-2 focus:ring-teal-200 @error('size_kontainer') border-red-400 @enderror">
                @foreach($sizeOptions as $value => $label)<option value="{{ $value }}" @selected(old('size_kontainer', $pricelist?->size_kontainer) === $value)>{{ $label }}</option>@endforeach
            </select>
            @error('size_kontainer')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="status_kontainer" class="mb-2 block text-sm font-semibold text-gray-700">Status Kontainer <span id="status_kontainer_required" class="text-red-500">*</span></label>
            <select id="status_kontainer" name="status_kontainer" required class="block w-full rounded-lg border-gray-300 px-3 py-2.5 text-sm shadow-sm transition focus:border-teal-500 focus:ring-2 focus:ring-teal-200 @error('status_kontainer') border-red-400 @enderror">
                <option value="">Tidak berlaku untuk service</option>
                @foreach($statusOptions as $value => $label)<option value="{{ $value }}" @selected(old('status_kontainer', $pricelist?->status_kontainer) === $value)>{{ $label }}</option>@endforeach
            </select>
            <p id="status_kontainer_hint" class="mt-1.5 hidden text-xs text-gray-500">Status kontainer tidak diperlukan untuk service atau tujuan Temas JKT.</p>
            @error('status_kontainer')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="status_service" class="mb-2 block text-sm font-semibold text-gray-700">Status Service <span class="text-red-500">*</span></label>
            <select id="status_service" name="status_service" required class="block w-full rounded-lg border-gray-300 px-3 py-2.5 text-sm shadow-sm transition focus:border-teal-500 focus:ring-2 focus:ring-teal-200 @error('status_service') border-red-400 @enderror">
                @foreach($statusServiceOptions as $value => $label)<option value="{{ $value }}" @selected(old('status_service', $pricelist?->status_service ?? 'non_service') === $value)>{{ $label }}</option>@endforeach
            </select>
            @error('status_service')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="gudang_tujuan_id" class="mb-2 block text-sm font-semibold text-gray-700">Gudang Tujuan</label>
            <select id="gudang_tujuan_id" name="gudang_tujuan_id" class="block w-full rounded-lg border-gray-300 px-3 py-2.5 text-sm shadow-sm transition focus:border-teal-500 focus:ring-2 focus:ring-teal-200 @error('gudang_tujuan_id') border-red-400 @enderror">
                <option value="">Semua Gudang (Tarif Umum)</option>
                @foreach($gudangs as $gudang)
                    <option value="{{ $gudang->id }}" data-is-temas-jkt="{{ str_contains(mb_strtolower($gudang->nama_gudang), 'temas jkt') ? '1' : '0' }}" @selected((string) old('gudang_tujuan_id', $pricelist?->gudang_tujuan_id) === (string) $gudang->id)>{{ $gudang->nama_gudang }}{{ $gudang->lokasi ? ' - '.$gudang->lokasi : '' }}</option>
                @endforeach
            </select>
            <p class="mt-1.5 text-xs text-gray-500">Pilih tujuan khusus atau gunakan Semua Gudang sebagai tarif cadangan.</p>
            @error('gudang_tujuan_id')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="biaya" class="mb-2 block text-sm font-semibold text-gray-700">Biaya <span class="text-red-500">*</span></label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm font-medium text-gray-500">Rp</span>
                <input id="biaya" type="number" name="biaya" min="0" step="0.01" required value="{{ old('biaya', $pricelist?->biaya) }}" placeholder="0" class="block w-full rounded-lg border-gray-300 py-2.5 pl-11 pr-3 text-sm shadow-sm transition focus:border-teal-500 focus:ring-2 focus:ring-teal-200 @error('biaya') border-red-400 @enderror">
            </div>
            <p class="mt-1.5 text-xs text-gray-500">Masukkan nominal tarif dalam Rupiah.</p>
            @error('biaya')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label for="keterangan" class="mb-2 block text-sm font-semibold text-gray-700">Keterangan <span class="font-normal text-gray-400">(opsional)</span></label>
        <textarea id="keterangan" name="keterangan" rows="4" placeholder="Tambahkan catatan tarif jika diperlukan..." class="block w-full rounded-lg border-gray-300 px-3 py-2.5 text-sm shadow-sm transition focus:border-teal-500 focus:ring-2 focus:ring-teal-200 @error('keterangan') border-red-400 @enderror">{{ old('keterangan', $pricelist?->keterangan) }}</textarea>
        <div class="mt-1.5 flex justify-between"><p class="text-xs text-gray-500">Maksimal 1.000 karakter.</p>@error('keterangan')<p class="text-xs text-red-600">{{ $message }}</p>@enderror</div>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end">
        <a href="{{ route('master.pricelist-ob-antar-gudang.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50"><i class="fas fa-arrow-left mr-2"></i>Batal</a>
        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-300"><i class="fas fa-save mr-2"></i>Simpan Pricelist</button>
    </div>
</form>

<script>
    (() => {
        const serviceSelect = document.getElementById('status_service');
        const containerSelect = document.getElementById('status_kontainer');
        const destinationSelect = document.getElementById('gudang_tujuan_id');
        const requiredMark = document.getElementById('status_kontainer_required');
        const hint = document.getElementById('status_kontainer_hint');

        function updateContainerStatus() {
            const isService = serviceSelect.value === 'service';
            const isTemasJkt = destinationSelect.selectedOptions[0]?.dataset.isTemasJkt === '1';
            const statusNotRequired = isService || isTemasJkt;
            containerSelect.disabled = statusNotRequired;
            containerSelect.required = !statusNotRequired;
            containerSelect.classList.toggle('bg-gray-100', statusNotRequired);
            containerSelect.classList.toggle('text-gray-400', statusNotRequired);
            requiredMark.classList.toggle('hidden', statusNotRequired);
            hint.classList.toggle('hidden', !statusNotRequired);

            if (statusNotRequired) {
                containerSelect.value = '';
            }
        }

        serviceSelect.addEventListener('change', updateContainerStatus);
        destinationSelect.addEventListener('change', updateContainerStatus);
        updateContainerStatus();
    })();
</script>
