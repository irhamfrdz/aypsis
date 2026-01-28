<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type / Satuan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Available</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Masuk (Latest)</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($items as $item)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $item->namaStockBan->nama ?? 'Unknown' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $item->ukuran ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $item->type ?? 'pcs' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-bold">
                    {{ $item->qty }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $item->lokasi ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ date('d-m-Y', strtotime($item->tanggal_masuk)) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end gap-2">
                        @if($item->qty > 0 && Str::contains(strtolower($type), 'ban dalam'))
                            <a href="{{ url('stock-ban/ban-dalam/'.$item->id.'/use') }}" class="text-green-600 hover:text-green-900" title="Gunakan">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                        @endif
                        
                        <a href="{{ url('stock-ban/ban-dalam/'.$item->id) }}" class="text-blue-600 hover:text-blue-900" title="Detail / History">
                            <i class="fas fa-eye"></i>
                        </a>

                        <!-- Note: Edit logic for aggregate items might be different, assume generic stock edit unavailable or mapped -->
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data {{ $type }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
