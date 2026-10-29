<section class="approval-goods-section" data-field-prefix="manifest_goods">
    @php
        $displayManifestGoods = $manifestGoods->isEmpty() && auth()->user()?->can('approval-tanda-terima-2-approve')
            ? collect([[]])
            : $manifestGoods->take(1);
    @endphp
    <h3 class="mb-2 text-base font-semibold text-gray-900">Detail Barang Manifest</h3>
    <p class="mb-4 text-xs text-gray-500">Isi satu barang untuk manifest approval. Baris yang dibiarkan kosong tidak akan disimpan.</p>
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
            <tr>
                <th class="px-3 py-2">HS Code</th><th class="px-3 py-2">Jumlah</th><th class="px-3 py-2">Satuan</th><th class="px-3 py-2">Nama Barang</th>
                <th class="px-3 py-2">Tonase</th><th class="px-3 py-2">Ukuran</th><th class="px-3 py-2">Volume (m³)</th>
                @if($type !== 'ttsj')<th class="px-3 py-2">Keterangan</th>@endif
            </tr>
        </thead>
        <tbody class="approval-goods-rows divide-y divide-gray-100">
            @foreach($displayManifestGoods as $good)
                <tr>
                    @can('approval-tanda-terima-2-approve')
                        @foreach(['hs_code' => 'text', 'jumlah' => 'number', 'satuan' => 'text', 'nama_barang' => 'text', 'tonase' => 'text', 'ukuran' => 'text', 'meter_kubik' => 'text'] as $field => $inputType)
                            <td class="px-2 py-2" data-field="{{ $field }}" @if($field === 'ukuran') hidden @endif>
                                @if($field === 'nama_barang')
                                    <textarea name="manifest_goods[{{ $loop->parent->index }}][{{ $field }}]" rows="2" @if($manifestGoods->isNotEmpty()) required @endif class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ data_get($good, $field) }}</textarea>
                                @else
                                    <input type="{{ $inputType }}" name="manifest_goods[{{ $loop->parent->index }}][{{ $field }}]"
                                        value="{{ data_get($good, $field) }}"
                                        @if($inputType === 'number') min="0" step="{{ $field === 'jumlah' ? '1' : 'any' }}" @endif
                                        @if(in_array($field, ['tonase', 'meter_kubik'])) inputmode="decimal" placeholder="Contoh: 17.665,78" @endif
                                        class="w-28 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @endif
                            </td>
                        @endforeach
                        @if($type !== 'ttsj')
                            <td class="px-2 py-2"><input type="text" name="manifest_goods[{{ $loop->index }}][keterangan_barang]" value="{{ data_get($good, 'keterangan_barang') }}" class="w-40 rounded-lg border-gray-300 text-sm"></td>
                        @endif
                    @else
                        @foreach(['hs_code', 'jumlah', 'satuan', 'nama_barang', 'tonase', 'ukuran', 'meter_kubik'] as $field)
                            @php
                                $value = data_get($good, $field);
                                if (in_array($field, ['tonase', 'meter_kubik']) && preg_match('/^\d{4,}$/', (string) $value)) {
                                    $value = preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $value);
                                }
                            @endphp
                            <td class="px-3 py-3" data-field="{{ $field }}" @if($field === 'ukuran') hidden @endif>{{ $value ?? '-' }}</td>
                        @endforeach
                        @if($type !== 'ttsj')<td class="px-3 py-3">{{ data_get($good, 'keterangan_barang') ?: '-' }}</td>@endif
                    @endcan
                </tr>
            @endforeach
        </tbody>
    </table>
    @if($displayManifestGoods->isEmpty())<p class="approval-goods-empty mt-3 text-sm text-gray-500">Detail barang manifest belum tersedia.</p>@endif
</section>
