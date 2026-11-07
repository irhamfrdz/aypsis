@php
    $selectedJenisBiayas = (array) old('jenis_biaya', []);
@endphp

<fieldset class="min-w-0 rounded-xl border border-gray-200 bg-gray-50/70 p-4 sm:p-5">
    <legend class="sr-only">Jenis biaya</legend>
    <div class="flex items-start gap-3">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
            <i class="fas fa-list-check" aria-hidden="true"></i>
        </span>
        <div class="min-w-0">
            <p class="text-sm font-semibold text-gray-900">Jenis biaya</p>
            <p class="mt-0.5 text-xs leading-5 text-gray-600">Pilih jenis yang ingin ditampilkan. Kosongkan pilihan untuk semua jenis.</p>
        </div>
    </div>
    <div class="mt-4 grid max-h-52 grid-cols-1 gap-2 overflow-y-auto rounded-xl border border-gray-200 bg-white p-2 sm:grid-cols-2" role="group" aria-label="Pilihan jenis biaya">
        @forelse($jenisBiayas as $jenisBiaya)
            <label class="flex min-w-0 cursor-pointer items-start gap-3 rounded-lg border border-gray-200 px-3 py-2.5 text-sm text-gray-700 transition hover:border-indigo-300 hover:bg-indigo-50 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100">
                <input type="checkbox" name="jenis_biaya[]" value="{{ $jenisBiaya }}" @checked(in_array($jenisBiaya, $selectedJenisBiayas, true)) class="peer mt-0.5 shrink-0 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="min-w-0 break-words leading-5 peer-checked:font-semibold peer-checked:text-indigo-700">{{ $jenisBiaya }}</span>
            </label>
        @empty
            <p class="px-3 py-3 text-sm text-gray-500 sm:col-span-2">Belum ada jenis biaya tersedia.</p>
        @endforelse
    </div>
</fieldset>
