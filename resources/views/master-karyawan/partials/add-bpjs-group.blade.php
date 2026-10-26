<details data-add-bpjs-group="{{ $field }}" class="mb-2 text-xs">
    <summary class="cursor-pointer text-blue-600 hover:text-blue-800">+ Tambah group baru</summary>
    <div class="mt-2 flex items-center gap-2">
        <label for="new_{{ $field }}" class="sr-only">Nama {{ $groupLabel }} baru</label>
        <input type="text" id="new_{{ $field }}" maxlength="255" class="min-w-0 flex-1 rounded-lg border border-gray-300 px-3 py-2 text-xs" placeholder="Nama {{ $groupLabel }} baru">
        <button type="button" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700">Tambahkan</button>
    </div>
    <p class="mt-1 text-gray-500">Group dipilih otomatis dan tersimpan saat data karyawan disimpan.</p>
</details>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-add-bpjs-group]').forEach(function (panel) {
                const input = panel.querySelector('input');
                const select = document.getElementById(panel.dataset.addBpjsGroup);
                function addGroup() {
                    const value = input.value.trim().replace(/\s+/g, ' ').toUpperCase();
                    input.setCustomValidity(value ? '' : 'Isi nama group terlebih dahulu.');
                    if (!input.reportValidity()) return;
                    const existing = Array.from(select.options).find(option => option.value.trim().replace(/\s+/g, ' ').toUpperCase() === value);
                    if (existing) {
                        select.value = existing.value;
                    } else {
                        select.add(new Option(value, value, true, true));
                    }
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    input.value = '';
                    panel.open = false;
                    select.focus();
                }
                input.addEventListener('input', () => input.setCustomValidity(''));
                panel.addEventListener('toggle', function () {
                    if (!panel.open) input.setCustomValidity('');
                });
                input.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        addGroup();
                    }
                });
                panel.querySelector('button').addEventListener('click', addGroup);
            });
});
</script>
@endpush
@endonce