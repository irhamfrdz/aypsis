<div id="bpjs-discount-modal" class="hidden fixed inset-0 z-50 bg-black/40 p-4 flex items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="bpjs-discount-title">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl flex flex-col" style="max-height:85vh">
        <div class="p-5 border-b border-gray-200">
            <div class="flex items-center justify-between gap-3">
                <h2 id="bpjs-discount-title" class="font-bold text-gray-900">Karyawan Tanpa Diskon</h2>
                <button type="button" id="bpjs-discount-close" class="p-2 text-gray-500" aria-label="Tutup">✕</button>
            </div>
            <p id="bpjs-discount-groups" class="mt-1 text-sm text-indigo-700"></p>
            <p class="mt-2 text-sm text-gray-600">Centang karyawan yang tidak mendapat diskon JKK/JKM. Iuran karyawan lainnya tetap mengikuti diskon group.</p>
            <input id="bpjs-discount-search" type="search" placeholder="Cari nama atau NIK karyawan..." aria-label="Cari karyawan" class="mt-3 w-full rounded-lg border-gray-300 text-sm">
        </div>
        <p id="bpjs-discount-status" class="px-5 py-3 text-sm text-gray-600" role="status" aria-live="polite"></p>
        <div id="bpjs-discount-list" class="overflow-y-auto px-5 flex-1" style="min-height:80px"></div>
        <div class="p-5 border-t border-gray-200 flex items-center justify-between gap-3">
            <span id="bpjs-discount-count" class="text-sm text-gray-600">0 karyawan tanpa diskon</span>
            <div class="flex gap-2">
                <button id="bpjs-discount-cancel" type="button" class="px-4 py-2 rounded-lg border border-gray-300 text-sm">Batal</button>
                <button id="bpjs-discount-save" type="button" disabled class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold disabled:opacity-50">Simpan</button>
            </div>
        </div>
    </div>
</div>
