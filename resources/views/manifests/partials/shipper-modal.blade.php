@push('styles')
<style>
    #manifest-shipper-dialog { width: min(700px, calc(100vw - 32px)); max-height: calc(100vh - 40px); border: 0; border-radius: 12px; padding: 24px; overflow-y: auto; }
    #manifest-shipper-dialog::backdrop { background: rgb(15 23 42 / 55%); }
    #manifest-shipper-dialog [hidden] { display: none !important; }
    .manifest-shipper-option { display: block; width: 100%; padding: 10px 12px; text-align: left; font-size: 13px; border-bottom: 1px solid #e5e7eb; }
    .manifest-shipper-option:hover, .manifest-shipper-option:focus { background: #f3e8ff; outline: 2px solid #a855f7; outline-offset: -2px; }
</style>
@endpush
<dialog id="manifest-shipper-dialog" aria-labelledby="manifest-shipper-title"
        data-search-url="{{ url('/api/manifests/search-shippers') }}">
    <div class="flex justify-between items-start gap-4 mb-4">
        <div>
            <h2 id="manifest-shipper-title" class="text-lg font-semibold text-gray-900">Pilih Shipper Manifest</h2>
            <p id="manifest-shipper-context" class="text-sm text-gray-500 mt-1"></p>
        </div>
        <button type="button" class="manifest-shipper-close text-gray-500 px-2 py-1" aria-label="Tutup form shipper">&times;</button>
    </div>
    <p class="text-sm text-gray-600 mb-4">Pilih shipper dari master. Alamat pengirim, consignee, notify party, dan alamat notify party akan terisi seperti pada halaman edit. Periksa isian sebelum menyimpan.</p>
    <p id="manifest-shipper-message" class="text-sm text-red-700 mb-3" role="status" aria-live="polite" hidden></p>
    <form id="manifest-shipper-form">
        <fieldset id="manifest-shipper-fields">
            <div class="flex items-center justify-between gap-3 mb-2">
                <label for="manifest-shipper-search" class="block text-sm font-medium text-gray-700">Shipper</label>
                @can('master-shipper-consignee-create')
                    <a href="{{ route('master.shipper-consignee.create') }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-purple-600 text-white text-sm hover:bg-purple-700"
                       title="Tambah master shipper di tab baru">
                        <i class="fas fa-plus" aria-hidden="true"></i> Tambah
                    </a>
                @endcan
            </div>
            <input id="manifest-shipper-search" type="search" autocomplete="off" placeholder="Ketik nama shipper..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" aria-controls="manifest-shipper-options" aria-expanded="false">
            <div id="manifest-shipper-options" class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg mt-1" aria-label="Hasil pencarian shipper" hidden></div>
            @can('master-shipper-consignee-create')
                <p class="text-xs text-gray-500 mt-2">Belum ada di master? Klik Tambah, simpan di tab baru, lalu kembali dan cari nama shipper di sini.</p>
            @endcan
            <p id="manifest-shipper-selection" class="text-xs text-purple-700 mt-2" aria-live="polite"></p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="manifest-shipper-alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat Pengirim</label>
                    <textarea id="manifest-shipper-alamat" name="alamat_pengirim" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                </div>
                <div>
                    <label for="manifest-shipper-consignee" class="block text-sm font-medium text-gray-700 mb-1">Consignee</label>
                    <input id="manifest-shipper-consignee" name="penerima" maxlength="255" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label for="manifest-shipper-notify" class="block text-sm font-medium text-gray-700 mb-1">Notify Party</label>
                    <input id="manifest-shipper-notify" name="notify_party" maxlength="255" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label for="manifest-shipper-notify-address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Notify Party</label>
                    <textarea id="manifest-shipper-notify-address" name="alamat_notify_party" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                </div>
            </div>
            <fieldset id="manifest-shipper-cargo" class="mt-4" hidden disabled>
                <p class="text-sm text-gray-600 mb-3">Data ini tersimpan sebagai detail shipper pada kontainer yang sama. Muatan bersifat opsional dan tidak mengubah total muatan kontainer.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="text-sm">Nomor Tanda Terima
                        <input name="nomor_tanda_terima" maxlength="255" class="block w-full border rounded px-3 py-2">
                    </label>
                    <label class="text-sm sm:col-span-2">Nama Barang
                        <textarea name="nama_barang" class="block w-full border rounded px-3 py-2"></textarea>
                    </label>
                    @foreach(['tonnage' => 'Tonase', 'volume' => 'Volume', 'kuantitas' => 'Jumlah Barang', 'tonnage_perincian' => 'Tonase Perincian', 'volume_perincian' => 'Volume Perincian'] as $field => $label)
                        <label class="text-sm">{{ $label }}
                            <input name="{{ $field }}" type="number" min="0" step="{{ $field === 'kuantitas' ? '1' : '0.001' }}" class="block w-full border rounded px-3 py-2">
                        </label>
                    @endforeach
                </div>
            </fieldset>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" class="manifest-shipper-close px-4 py-2 rounded-lg border border-gray-300 text-sm">Batal</button>
                <button id="manifest-shipper-save" type="submit" class="px-4 py-2 rounded-lg bg-purple-600 text-white text-sm disabled:opacity-50" disabled>Simpan Shipper</button>
            </div>
        </fieldset>
    </form>
</dialog>
@push('scripts')
<script src="{{ asset('js/manifest-shipper.js') }}?v={{ substr(hash_file('sha256', public_path('js/manifest-shipper.js')), 0, 12) }}" defer></script>
@endpush
