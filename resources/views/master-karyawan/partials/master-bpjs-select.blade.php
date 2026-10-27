@php
    $masterOptions = $rumusOptions->pluck($masterColumn)
        ->map(fn ($value) => trim((string) $value))
        ->filter(fn ($value) => $value !== '')
        ->unique(fn ($value) => mb_strtoupper($value))
        ->sort()->values();
    $selectedValue = trim((string) $currentValue);
    $hasMasterValue = $masterOptions->contains(fn ($value) => mb_strtoupper($value) === mb_strtoupper($selectedValue));
@endphp

<label for="{{ $field }}" class="{{ $labelClasses }}">{{ $fieldLabel }}</label>
<select name="{{ $field }}" id="{{ $field }}" class="{{ $selectClasses }}">
    <option value="">-- Pilih {{ $fieldLabel }} --</option>
    @if($selectedValue !== '' && !$hasMasterValue)
        <option value="{{ $selectedValue }}" selected>{{ $selectedValue }} (data lama)</option>
    @endif
    @foreach($masterOptions as $option)
        <option value="{{ $option }}" @selected(mb_strtoupper($selectedValue) === mb_strtoupper($option))>{{ $option }}</option>
    @endforeach
</select>
@error($field)
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
@enderror
