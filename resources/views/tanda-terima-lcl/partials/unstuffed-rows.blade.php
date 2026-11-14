@forelse($rows as $lcl)
    <tr class="hover:bg-gray-50">
        <td class="px-4 py-3"><input type="checkbox" class="unstuffed-checkbox rounded border-gray-300 text-teal-600" value="{{ $lcl->id }}"></td>
        <td class="px-4 py-3 text-sm">{{ $lcl->nomor_tanda_terima ?? 'TT-LCL-'.$lcl->id }}</td>
        <td class="px-4 py-3 text-sm">
            @forelse($lcl->items as $item)
                <div class="text-xs">{{ $item->nama_barang }} @if($item->jumlah)({{ $item->jumlah }} {{ $item->satuan ?? 'pcs' }})@endif</div>
            @empty
                -
            @endforelse
        </td>
        <td class="px-4 py-3 text-sm">{{ $lcl->nama_penerima ?? '-' }}</td>
        <td class="px-4 py-3 text-sm">{{ $lcl->nama_pengirim ?? '-' }}</td>
        <td class="px-4 py-3 text-sm">{{ number_format($lcl->items->sum('meter_kubik'), 3) }} m³</td>
        <td class="px-4 py-3 text-sm">{{ number_format($lcl->items->sum('tonase'), 3) }} ton</td>
    </tr>
@empty
    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500 text-sm">Tidak ada LCL yang belum di-stuffing.</td></tr>
@endforelse
