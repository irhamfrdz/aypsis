<div class="mt-6 border-t border-indigo-100 pt-5">
    <p class="mb-3 text-sm font-bold text-indigo-950">Detail Pergantian Ban Supir <span class="ml-1 rounded-full bg-white px-2 py-1 text-xs font-semibold text-indigo-700">{{ number_format($tireDetails->count(), 0, ',', '.') }} ban</span></p>
    @if($tireDetails->isEmpty())
        <p class="text-xs text-gray-600">Tidak ada pergantian ban supir pada periode yang dipilih.</p>
    @else
        <div class="overflow-x-auto rounded-xl border border-indigo-100 bg-white shadow-sm">
            <table class="min-w-[1100px] w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-indigo-50/70 text-left font-semibold text-slate-600">
                    <tr>
                        <th class="px-3 py-2">Tanggal Pakai</th>
                        <th class="px-3 py-2">No. Bukti Pakai</th>
                        <th class="px-3 py-2">No. Seri Ban</th>
                        <th class="px-3 py-2">Merek / Ukuran</th>
                        <th class="px-3 py-2">Kondisi</th>
                        <th class="px-3 py-2 text-right">Harga Ban</th>
                        <th class="px-3 py-2">Kendaraan Plat/KIR</th>
                        <th class="px-3 py-2">Penerima</th>
                        <th class="px-3 py-2">Sumber</th>
                        <th class="px-3 py-2">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($tireDetails as $tire)
                        <tr>
                            <td class="whitespace-nowrap px-3 py-2">{{ \Carbon\Carbon::parse($tire['tanggal'])->format('d/m/Y') }}</td>
                            <td class="px-3 py-2 font-medium">{{ $tire['nomor_bukti'] ?: '-' }}</td>
                            <td class="px-3 py-2">{{ $tire['nomor_seri'] ?: '-' }}</td>
                            <td class="px-3 py-2">{{ $tire['merk'] ?: '-' }} / {{ $tire['ukuran'] ?: '-' }}</td>
                            <td class="px-3 py-2">{{ $tire['kondisi'] ?: '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-2 text-right font-semibold">{{ $tire['harga_beli'] !== null ? 'Rp '.number_format((float) $tire['harga_beli'], 0, ',', '.') : '-' }}</td>
                            <td class="px-3 py-2">{{ $tire['kendaraan'] }}</td>
                            <td class="px-3 py-2">{{ $tire['penerima'] }}</td>
                            <td class="px-3 py-2">{{ $tire['sumber'] }}</td>
                            <td class="px-3 py-2">{{ $tire['keterangan'] ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
