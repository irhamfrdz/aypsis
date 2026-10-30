@php
    $manifest = $detail->manifest_goods ?? [];
    $perincian = $detail->goods ?? [];
    $hsCodes = data_get($manifest, 'hs_codes', []);
@endphp
<details class="rounded-lg border border-indigo-200 bg-indigo-50 p-3 text-sm">
    <summary class="cursor-pointer font-semibold text-indigo-800">
        {{ $detail->shipperJb?->shipper ?: 'Shipper tidak ditemukan' }} → {{ $detail->shipperJb?->consignee ?: '-' }}
        <span class="ml-1 font-normal text-gray-600">{{ data_get($manifest, 'nama_barang') ?: '(barang belum diisi)' }}</span>
    </summary>
    @can('approval-tanda-terima-2-approve')
        <form method="POST" action="{{ route('approval-tanda-terima-2.fcl-shippers.goods', ['id' => $item->id, 'detailId' => $detail->id]) }}" class="mt-3 space-y-3">
            @csrf
            @method('PUT')
            <div class="grid gap-3 sm:grid-cols-2">
                <fieldset class="space-y-2 rounded-lg bg-white p-3">
                    <legend class="font-semibold">Barang Manifest</legend>
                    <label class="block">Nama barang *<textarea name="manifest_goods[nama_barang]" required rows="2" class="mt-1 w-full rounded-lg border-gray-300 text-sm">{{ data_get($manifest, 'nama_barang') }}</textarea></label>
                    <label class="block">HS Code, satu per baris<textarea name="manifest_goods[hs_codes]" rows="2" class="mt-1 w-full rounded-lg border-gray-300 text-sm">{{ implode("\n", is_array($hsCodes) ? $hsCodes : []) }}</textarea></label>
                    <div class="grid grid-cols-2 gap-2">
                        <label>Jumlah<input name="manifest_goods[jumlah]" type="number" min="0" value="{{ data_get($manifest, 'jumlah') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                        <label>Satuan<input name="manifest_goods[satuan]" value="{{ data_get($manifest, 'satuan') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                        <label>Tonase<input name="manifest_goods[tonase]" inputmode="decimal" value="{{ data_get($manifest, 'tonase') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                        <label>Volume (m³)<input name="manifest_goods[meter_kubik]" inputmode="decimal" value="{{ data_get($manifest, 'meter_kubik') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                        <label>Ukuran<input name="manifest_goods[ukuran]" value="{{ data_get($manifest, 'ukuran') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                    </div>
                    <label class="block">Keterangan<input name="manifest_goods[keterangan_barang]" value="{{ data_get($manifest, 'keterangan_barang') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                </fieldset>
                <fieldset class="space-y-2 rounded-lg bg-white p-3">
                    <legend class="font-semibold">Barang Perincian</legend>
                    <label class="block">Nama barang *<textarea name="goods[nama_barang]" required rows="2" class="mt-1 w-full rounded-lg border-gray-300 text-sm">{{ data_get($perincian, 'nama_barang') }}</textarea></label>
                    <div class="grid grid-cols-2 gap-2">
                        <label>Jumlah<input name="goods[jumlah]" type="number" min="0" value="{{ data_get($perincian, 'jumlah') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                        <label>Satuan<input name="goods[satuan]" value="{{ data_get($perincian, 'satuan') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                        <label>Ukuran<input name="goods[ukuran]" value="{{ data_get($perincian, 'ukuran') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                    </div>
                    <label class="block">Keterangan<input name="goods[keterangan_barang]" value="{{ data_get($perincian, 'keterangan_barang') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                </fieldset>
            </div>
            <button type="submit" class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700">Simpan Barang Shipper</button>
        </form>
        <form method="POST" action="{{ route('approval-tanda-terima-2.fcl-shippers.destroy', ['id' => $item->id, 'detailId' => $detail->id]) }}" onsubmit="return confirm('Hapus shipper tambahan dan rincian barangnya?')" class="mt-2">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs font-semibold text-red-700 hover:underline">Hapus Shipper Tambahan</button>
        </form>
    @else
        <p class="mt-2">Manifest: {{ data_get($manifest, 'nama_barang') ?: '-' }} · Perincian: {{ data_get($perincian, 'nama_barang') ?: '-' }}</p>
    @endcan
</details>
