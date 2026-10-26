<div class="mb-4 flex items-center justify-between gap-4 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
    <div>
        <label for="is_special_case" class="text-sm font-semibold text-gray-800 cursor-pointer">Special Case</label>
        <p id="special-case-help" class="mt-1 text-xs text-gray-500">Tandai karyawan ini sebagai kategori Special Case.</p>
    </div>
    <label class="relative inline-flex shrink-0 cursor-pointer items-center">
        <input type="hidden" name="is_special_case" value="0">
        <input type="checkbox" name="is_special_case" id="is_special_case" value="1" role="switch" aria-label="Special Case" aria-describedby="special-case-help" class="sr-only peer" @checked(old('is_special_case', $karyawanTidakTetap->is_special_case ?? false))>
        <span aria-hidden="true" class="h-6 w-11 rounded-full bg-gray-300 peer-checked:bg-indigo-600 peer-focus:ring-2 peer-focus:ring-indigo-300 peer-focus:ring-offset-2 transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow-sm after:transition-transform peer-checked:after:translate-x-5"></span>
    </label>
</div>
@error('is_special_case')
    <p class="mb-3 text-xs text-red-600" role="alert">{{ $message }}</p>
@enderror
