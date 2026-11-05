@php
    $selectedJenisBiayas = (array) old('jenis_biaya', []);
@endphp

<fieldset>
    <legend class="text-sm font-semibold text-gray-800">Jenis biaya</legend>
    <p class="mt-1 text-xs text-gray-500">Centang satu atau beberapa jenis. Biarkan kosong untuk semua jenis.</p>
    <div class="mt-2 max-h-44 space-y-1 overflow-y-auto rounded-lg border border-gray-300 bg-white p-2">
        @forelse($jenisBiayas as $jenisBiaya)
            <label class="flex cursor-pointer items-start gap-3 rounded-md px-2 py-2 text-sm text-gray-700 hover:bg-indigo-50">
                <input type="checkbox" name="jenis_biaya[]" value="{{ $jenisBiaya }}" @checked(in_array($jenisBiaya, $selectedJenisBiayas, true)) class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span>{{ $jenisBiaya }}</span>
            </label>
        @empty
            <p class="px-2 py-2 text-sm text-gray-500">Belum ada jenis biaya tersedia.</p>
        @endforelse
    </div>
</fieldset>
