@php
    $manifest = $detail->manifest_goods ?? [];
    $perincian = $detail->goods ?? [];
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
        <dialog id="{{ $goodsDialogId }}" data-source-type="fcl" class="approval-goods-dialog whitespace-normal text-left shadow-2xl" aria-labelledby="{{ $goodsDialogId }}-title">
            <div class="flex items-start justify-between gap-3 border-b border-gray-200 px-5 py-4">
                <div>
                    <h2 id="{{ $goodsDialogId }}-title" class="text-lg font-semibold text-gray-900">Barang Shipper Tambahan</h2>
                    <p class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500">
                        <span>Tanda terima: {{ $item->no_surat_jalan ?: 'Tanpa nomor' }}</span>
                        <span>Shipper: {{ $detail->shipperJb?->shipper ?: '-' }}</span>
                    </p>
                </div>
                <button type="button" class="approval-goods-close rounded-lg px-2 py-1 text-xl text-gray-500 hover:bg-gray-100" aria-label="Tutup">&times;</button>
            </div>
            <p class="mx-5 mt-4 rounded-lg bg-indigo-50 px-3 py-2 text-xs text-indigo-800">
                Lengkapi detail barang manifest dan perincian untuk shipper ini. Kolom bertanda * wajib diisi. Perubahan hanya berlaku pada versi approval.
            </p>
            <form method="POST" action="{{ route('approval-tanda-terima-2.fcl-shippers.goods', ['id' => $item->id, 'detailId' => $detail->id]) }}">
                @csrf
                @method('PUT')
                <div class="overflow-x-auto p-5">
                    @include('approval-tanda-terima-2._manifest-goods-section', ['manifestGoods' => collect([$manifest]), 'isAdditionalShipper' => true])
                    <section class="approval-goods-section mt-8 border-t border-gray-200 pt-6" data-field-prefix="goods" data-required="true" data-saved-override="{{ filled(data_get($perincian, 'nama_barang')) ? 'true' : 'false' }}">
                        <h3 class="mb-4 text-base font-semibold text-gray-900">Detail Barang Perincian</h3>
                        <p class="mb-4 text-xs text-gray-500">Isi satu barang perincian. Nilai awal mengikuti Manifest dan dapat diubah terpisah.</p>
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                                <tr>
                                    <th class="px-3 py-2">Jumlah</th><th class="px-3 py-2">Satuan</th><th class="px-3 py-2">Nama Barang</th><th class="px-3 py-2">Ukuran</th><th class="px-3 py-2">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="approval-goods-rows divide-y divide-gray-100">
                                <tr>
                                    @foreach(['jumlah' => 'number', 'satuan' => 'text', 'nama_barang' => 'text', 'ukuran' => 'text'] as $field => $inputType)
                                        <td class="px-2 py-2" data-field="{{ $field }}" @if($field === 'ukuran') hidden @endif>
                                            @if($field === 'nama_barang')
                                                <textarea name="goods[{{ $field }}]" rows="2" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ data_get($perincian, $field) }}</textarea>
                                            @else
                                                <input type="{{ $inputType }}" name="goods[{{ $field }}]" value="{{ data_get($perincian, $field) }}" @if($inputType === 'number') min="0" step="1" @endif class="w-28 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="px-2 py-2" data-field="keterangan_barang"><input type="text" name="goods[keterangan_barang]" value="{{ data_get($perincian, 'keterangan_barang') }}" class="w-40 rounded-lg border-gray-300 text-sm"></td>
                                </tr>
                            </tbody>
                        </table>
                    </section>
                </div>
                <div class="flex justify-end gap-2 border-t border-gray-200 bg-gray-50 px-5 py-4">
                    <button type="button" class="approval-goods-close rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Tutup</button>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Simpan Detail Barang</button>
                </div>
            </form>
        </dialog>
    @else
        <p class="mt-2 text-xs text-gray-600">Manifest: {{ data_get($manifest, 'nama_barang') ?: '-' }} · Perincian: {{ data_get($perincian, 'nama_barang') ?: '-' }}</p>
    @endcan
</div>
