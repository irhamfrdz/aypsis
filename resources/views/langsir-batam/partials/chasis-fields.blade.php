@php
    $sumberChasis = old('sumber_chasis', $langsir?->sumber_chasis ?? '');
    $noChasisAyp = old('no_chasis_ayp', $sumberChasis === 'AYP' ? $langsir?->no_chasis : '');
    $noChasisPb = old('no_chasis_pb', $sumberChasis === 'PB' ? $langsir?->no_chasis : '');
@endphp

<div>
    <label for="sumber_chasis" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wider">
        Menggunakan Chasis <span class="text-red-500">*</span>
    </label>
    <select name="sumber_chasis" id="sumber_chasis" @if(!$langsir) required @endif
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
        <option value="">Pilih sumber chasis</option>
        <option value="AYP" {{ $sumberChasis === 'AYP' ? 'selected' : '' }}>Chasis AYP</option>
        <option value="PB" {{ $sumberChasis === 'PB' ? 'selected' : '' }}>Chasis PB</option>
    </select>
</div>

<div id="chasis_ayp_fields" class="hidden">
    <label for="no_chasis_ayp" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wider">
        Nomor Chasis AYP <span class="text-red-500">*</span>
    </label>
    <select name="no_chasis_ayp" id="no_chasis_ayp" disabled
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
        <option value="">Pilih nomor chasis AYP</option>
        @foreach($chasisBatams as $chasisBatam)
            <option value="{{ $chasisBatam->kode }}" {{ (string) $noChasisAyp === (string) $chasisBatam->kode ? 'selected' : '' }}>
                {{ $chasisBatam->kode }}
            </option>
        @endforeach
    </select>
    <p class="mt-1 text-xs text-gray-500">Daftar nomor chasis berasal dari Master Chasis Batam.</p>
</div>

<div id="chasis_pb_fields" class="hidden">
    <label for="no_chasis_pb" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wider">
        Nomor Chasis PB <span class="text-red-500">*</span>
    </label>
    <input type="text" name="no_chasis_pb" id="no_chasis_pb" value="{{ $noChasisPb }}" disabled maxlength="255"
           placeholder="Masukkan nomor chasis PB"
           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all uppercase">
    <p class="mt-1 text-xs text-gray-500">Nomor chasis PB diisi manual.</p>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const source = document.getElementById('sumber_chasis');
    const aypFields = document.getElementById('chasis_ayp_fields');
    const pbFields = document.getElementById('chasis_pb_fields');
    const aypInput = document.getElementById('no_chasis_ayp');
    const pbInput = document.getElementById('no_chasis_pb');

    function toggleChasisFields() {
        const isAyp = source.value === 'AYP';
        const isPb = source.value === 'PB';
        aypFields.classList.toggle('hidden', !isAyp);
        pbFields.classList.toggle('hidden', !isPb);
        aypInput.disabled = !isAyp;
        pbInput.disabled = !isPb;
        aypInput.required = isAyp;
        pbInput.required = isPb;
    }

    source.addEventListener('change', toggleChasisFields);
    toggleChasisFields();
});
</script>
@endpush
