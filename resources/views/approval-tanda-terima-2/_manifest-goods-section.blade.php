<section class="approval-goods-section" data-field-prefix="manifest_goods">
    @php
        $displayManifestGoods = $manifestGoods->isEmpty() && auth()->user()?->can('approval-tanda-terima-2-approve')
            ? collect([[]])
            : $manifestGoods;
    @endphp
    <h3 class="mb-2 text-base font-semibold text-gray-900">Detail Barang Manifest</h3>
    <p class="mb-4 text-xs text-gray-500">Isi rincian untuk manifest approval. Kartu yang dibiarkan kosong tidak akan disimpan.</p>
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
            <tr>
                <th class="px-3 py-2">HS Code</th><th class="px-3 py-2">Jumlah</th><th class="px-3 py-2">Satuan</th><th class="px-3 py-2">Nama Barang</th>
                <th class="px-3 py-2">Tonase</th><th class="px-3 py-2">Ukuran</th><th class="px-3 py-2">Panjang</th>
                <th class="px-3 py-2">Lebar</th><th class="px-3 py-2">Tinggi</th><th class="px-3 py-2">Volume (m³)</th>
                @if($type !== 'ttsj')<th class="px-3 py-2">Keterangan</th>@endif
                @can('approval-tanda-terima-2-approve')<th class="px-3 py-2">Aksi</th>@endcan
            </tr>
        </thead>
        <tbody class="approval-goods-rows divide-y divide-gray-100">
            @foreach($displayManifestGoods as $good)
                <tr>
                    @can('approval-tanda-terima-2-approve')
                        @foreach(['hs_code' => 'text', 'jumlah' => 'number', 'satuan' => 'text', 'nama_barang' => 'text', 'tonase' => 'text', 'ukuran' => 'text', 'panjang' => 'number', 'lebar' => 'number', 'tinggi' => 'number', 'meter_kubik' => 'number'] as $field => $inputType)
                            <td class="px-2 py-2" data-field="{{ $field }}" @if($field === 'ukuran') hidden @endif>
                                @if($field === 'nama_barang')
                                    <textarea name="manifest_goods[{{ $loop->parent->index }}][{{ $field }}]" rows="2" @if($manifestGoods->isNotEmpty()) required @endif class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ data_get($good, $field) }}</textarea>
                                @else
                                    <input type="{{ $inputType }}" name="manifest_goods[{{ $loop->parent->index }}][{{ $field }}]"
                                        value="{{ data_get($good, $field) }}"
                                        @if($inputType === 'number') min="0" step="{{ $field === 'jumlah' ? '1' : 'any' }}" @endif
                                        @if($field === 'tonase') inputmode="decimal" placeholder="Contoh: 17.665,78" @endif
                                        class="w-28 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @endif
                            </td>
                        @endforeach
                        @if($type !== 'ttsj')
                            <td class="px-2 py-2"><input type="text" name="manifest_goods[{{ $loop->index }}][keterangan_barang]" value="{{ data_get($good, 'keterangan_barang') }}" class="w-40 rounded-lg border-gray-300 text-sm"></td>
                        @endif
                        <td class="px-2 py-2"><button type="button" class="approval-goods-remove rounded-lg px-2 py-1 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button></td>
                    @else
                        @foreach(['hs_code', 'jumlah', 'satuan', 'nama_barang', 'tonase', 'ukuran', 'panjang', 'lebar', 'tinggi', 'meter_kubik'] as $field)
                            <td class="px-3 py-3" data-field="{{ $field }}" @if($field === 'ukuran') hidden @endif>{{ data_get($good, $field) ?? '-' }}</td>
                        @endforeach
                        @if($type !== 'ttsj')<td class="px-3 py-3">{{ data_get($good, 'keterangan_barang') ?: '-' }}</td>@endif
                    @endcan
                </tr>
            @endforeach
        </tbody>
    </table>
    @if($displayManifestGoods->isEmpty())<p class="approval-goods-empty mt-3 text-sm text-gray-500">Detail barang manifest belum tersedia.</p>@endif
    @can('approval-tanda-terima-2-approve')
        <button type="button" class="approval-goods-add mt-4 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">+ Tambah Barang Manifest</button>
    @endcan
</section>
