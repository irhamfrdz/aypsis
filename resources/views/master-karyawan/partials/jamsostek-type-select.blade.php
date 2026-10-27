@php
    $normalizeJamsostek = fn ($value) => \App\Services\KaryawanBpjsGroups::normalize((string) $value);
    $selectedGroup = $normalizeJamsostek($currentGroup);
    $selectedType = $normalizeJamsostek($currentType);
    $typeOptions = $jamsostekRumusOptions
        ->filter(fn ($rumus) => $normalizeJamsostek($rumus->group_name) === $selectedGroup)
        ->pluck('tipe')->map(fn ($value) => trim((string) $value))
        ->filter(fn ($value) => $value !== '')
        ->unique($normalizeJamsostek)->sort()->values();
    $jamsostekTypeData = $jamsostekRumusOptions->map(fn ($rumus) => [
        'group' => $normalizeJamsostek($rumus->group_name),
        'tipe' => trim((string) $rumus->tipe),
    ])->values();
@endphp

<label for="tipe_bp_jamsostek" class="{{ $labelClasses }}">Tipe BP Jamsostek</label>
<select name="tipe_bp_jamsostek" id="tipe_bp_jamsostek" class="{{ $selectClasses }}">
    <option value="">{{ $selectedGroup === '' ? '-- Pilih Group BP Jamsostek terlebih dahulu --' : '-- Pilih Tipe BP Jamsostek --' }}</option>
    @foreach($typeOptions as $type)
        <option value="{{ $type }}" @selected($normalizeJamsostek($type) === $selectedType)>{{ $type }}</option>
    @endforeach
</select>
@error('tipe_bp_jamsostek')
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
@enderror

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const groupSelect = document.getElementById('group_bp_jamsostek');
    const typeSelect = document.getElementById('tipe_bp_jamsostek');
    const records = @json($jamsostekTypeData);
    const normalize = value => String(value || '').trim().replace(/\s+/g, ' ').toUpperCase();

    function updateTypes() {
        const group = normalize(groupSelect.value);
        const selectedType = normalize(typeSelect.value);
        const types = new Map();
        records.forEach(record => {
            if (record.group === group && normalize(record.tipe)) {
                types.set(normalize(record.tipe), record.tipe);
            }
        });
        typeSelect.replaceChildren(new Option(group
            ? (types.size ? '-- Pilih Tipe BP Jamsostek --' : '-- Belum ada tipe untuk group ini --')
            : '-- Pilih Group BP Jamsostek terlebih dahulu --', ''));
        Array.from(types.values()).sort().forEach(type => {
            typeSelect.add(new Option(type, type, false, normalize(type) === selectedType));
        });
    }

    groupSelect.addEventListener('change', updateTypes);
    updateTypes();
});
</script>
@endpush
