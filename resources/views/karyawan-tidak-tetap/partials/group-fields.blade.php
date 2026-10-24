@php
    $selectedGroups = array_values(array_filter((array) old('group', $karyawanTidakTetap->group ?? []), 'is_string'));
    $selectedSubGroups = array_values(array_filter((array) old('sub_group', $karyawanTidakTetap->sub_group ?? []), 'is_string'));
    $groupOptions = $groups->merge($selectedGroups)->unique()->values();
    $subGroupOptions = collect($selectedGroups)->flatMap(fn ($group) => $subGroupsByGroup[strtoupper(trim($group))] ?? [])
        ->merge($selectedSubGroups)->unique()->values();
@endphp
<div>
    <label for="group" class="{{ $labelClasses }}">Group</label>
    <select name="group[]" id="group" class="{{ $selectClasses }} select2-multiple" multiple data-placeholder="-- Pilih Group --">
        @foreach($groupOptions as $group)
            <option value="{{ $group }}" @selected(in_array($group, $selectedGroups, true))>{{ $group }}</option>
        @endforeach
    </select>
</div>
<div>
    <label for="sub_group" class="{{ $labelClasses }}">Sub Group</label>
    <select name="sub_group[]" id="sub_group" class="{{ $selectClasses }} select2-multiple" multiple data-placeholder="-- Pilih Sub Group --">
        @foreach($subGroupOptions as $subGroup)
            <option value="{{ $subGroup }}" @selected(in_array($subGroup, $selectedSubGroups, true))>{{ $subGroup }}</option>
        @endforeach
    </select>
    <p class="mt-1 text-xs text-gray-500">Sub Group GAJI mengikuti data aktif pada Master Type Gaji Karyawan.</p>
</div>
@push('scripts')
<script>
    $(document).ready(function() {
        const optionsByGroup = @json($subGroupsByGroup);
        const groupInput = $('#group');
        const subGroupInput = $('#sub_group');
        groupInput.on('change.typeGaji', function() {
            const selected = subGroupInput.val() || [];
            const available = [...new Set((groupInput.val() || []).flatMap(group => optionsByGroup[group.trim().toUpperCase()] || []))];
            subGroupInput.empty();
            available.forEach(value => subGroupInput.append(new Option(value, value, false, selected.includes(value))));
            subGroupInput.trigger('change.select2');
        });
    });
</script>
@endpush
