@php
    $manifest = $detail->manifest_goods ?? [];
    $perincian = $detail->goods ?? [];
    $hsCodes = data_get($manifest, 'hs_codes', []);
    $goodsDialogId = 'approval-goods-fcl-additional-'.$detail->id;
@endphp
<div class="rounded-lg border border-indigo-200 bg-indigo-50 p-3 text-sm">
    <div class="font-semibold text-indigo-800">
        {{ $detail->shipperJb?->shipper ?: 'Shipper tidak ditemukan' }} → {{ $detail->shipperJb?->consignee ?: '-' }}
    </div>
    <p class="mt-1 text-xs text-gray-600">{{ data_get($manifest, 'nama_barang') ?: '(barang belum diisi)' }}</p>
    @can('approval-tanda-terima-2-approve')
        <div class="mt-3 flex flex-wrap gap-2">
            <button type="button" class="approval-shipper-open rounded-lg border border-indigo-200 bg-white px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100"
                data-update-url="{{ route('approval-tanda-terima-2.fcl-shippers.update', ['id' => $item->id, 'detailId' => $detail->id]) }}"
                data-number="{{ $item->no_surat_jalan }}" data-sender="{{ $item->pengirim }}"
                data-shipper-id="{{ $detail->shipper_jb_id }}" data-shipper="{{ $detail->shipperJb?->shipper }}"
                data-mode="edit-additional">
                <i class="fas fa-pen-to-square mr-1" aria-hidden="true"></i>Ubah Shipper Tambahan
            </button>
            <button type="button" class="approval-goods-open rounded-lg border border-indigo-200 bg-white px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100"
                data-dialog-id="{{ $goodsDialogId }}">
                <i class="fas fa-box-open mr-1" aria-hidden="true"></i>Detail Barang
            </button>
            <form method="POST" action="{{ route('approval-tanda-terima-2.fcl-shippers.destroy', ['id' => $item->id, 'detailId' => $detail->id]) }}" onsubmit="return confirm('Hapus shipper tambahan dan rincian barangnya?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus Shipper Tambahan</button>
            </form>
        </div>
        <dialog id="{{ $goodsDialogId }}" class="approval-goods-dialog whitespace-normal text-left shadow-2xl" aria-labelledby="{{ $goodsDialogId }}-title">
            <div class="flex items-start justify-between gap-3 border-b border-gray-200">
                <div>
                    <h2 id="{{ $goodsDialogId }}-title" class="text-lg font-semibold text-gray-900">Barang Shipper Tambahan</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $detail->shipperJb?->shipper ?: '-' }} → {{ $detail->shipperJb?->consignee ?: '-' }}</p>
                </div>
                <button type="button" class="approval-goods-close rounded-lg px-2 py-1 text-xl text-gray-500 hover:bg-gray-100" aria-label="Tutup">&times;</button>
            </div>
            <form method="POST" action="{{ route('approval-tanda-terima-2.fcl-shippers.goods', ['id' => $item->id, 'detailId' => $detail->id]) }}">
                @csrf
                @method('PUT')
                <div class="grid gap-4 p-5 sm:grid-cols-2 sm:p-6">
                    <fieldset class="space-y-3 rounded-xl border border-gray-200 bg-white p-4">
                        <legend class="px-1 font-semibold text-gray-800">Barang Manifest</legend>
                        <label class="block text-xs font-medium text-gray-600">Nama barang *<textarea name="manifest_goods[nama_barang]" required rows="2" class="mt-1 w-full rounded-lg border-gray-300 text-sm">{{ data_get($manifest, 'nama_barang') }}</textarea></label>
                        <label class="block text-xs font-medium text-gray-600">HS Code, satu per baris<textarea name="manifest_goods[hs_codes]" rows="2" class="mt-1 w-full rounded-lg border-gray-300 text-sm">{{ implode("\n", is_array($hsCodes) ? $hsCodes : []) }}</textarea></label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="block text-xs font-medium text-gray-600">Jumlah<input name="manifest_goods[jumlah]" type="number" min="0" value="{{ data_get($manifest, 'jumlah') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                            <label class="block text-xs font-medium text-gray-600">Satuan<input name="manifest_goods[satuan]" value="{{ data_get($manifest, 'satuan') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                            <label class="block text-xs font-medium text-gray-600">Tonase<input name="manifest_goods[tonase]" inputmode="decimal" value="{{ data_get($manifest, 'tonase') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                            <label class="block text-xs font-medium text-gray-600">Volume (m³)<input name="manifest_goods[meter_kubik]" inputmode="decimal" value="{{ data_get($manifest, 'meter_kubik') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                            <label class="block text-xs font-medium text-gray-600">Ukuran<input name="manifest_goods[ukuran]" value="{{ data_get($manifest, 'ukuran') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                        </div>
                        <label class="block text-xs font-medium text-gray-600">Keterangan<input name="manifest_goods[keterangan_barang]" value="{{ data_get($manifest, 'keterangan_barang') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                    </fieldset>
                    <fieldset class="space-y-3 rounded-xl border border-gray-200 bg-white p-4">
                        <legend class="px-1 font-semibold text-gray-800">Barang Perincian</legend>
                        <label class="block text-xs font-medium text-gray-600">Nama barang *<textarea name="goods[nama_barang]" required rows="2" class="mt-1 w-full rounded-lg border-gray-300 text-sm">{{ data_get($perincian, 'nama_barang') }}</textarea></label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="block text-xs font-medium text-gray-600">Jumlah<input name="goods[jumlah]" type="number" min="0" value="{{ data_get($perincian, 'jumlah') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                            <label class="block text-xs font-medium text-gray-600">Satuan<input name="goods[satuan]" value="{{ data_get($perincian, 'satuan') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                            <label class="block text-xs font-medium text-gray-600">Ukuran<input name="goods[ukuran]" value="{{ data_get($perincian, 'ukuran') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                        </div>
                        <label class="block text-xs font-medium text-gray-600">Keterangan<input name="goods[keterangan_barang]" value="{{ data_get($perincian, 'keterangan_barang') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                    </fieldset>
                </div>
                <div class="flex justify-end gap-2 border-t border-gray-200 bg-white px-5 py-4">
                    <button type="button" class="approval-goods-close rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Batal</button>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Simpan Detail Barang</button>
                </div>
            </form>
        </dialog>
    @else
        <p class="mt-2 text-xs text-gray-600">Manifest: {{ data_get($manifest, 'nama_barang') ?: '-' }} · Perincian: {{ data_get($perincian, 'nama_barang') ?: '-' }}</p>
    @endcan
</div>
