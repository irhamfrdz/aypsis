<section class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
    <div><h2 class="font-semibold text-gray-900">Armada Tidak Tersedia</h2><p class="mt-1 text-sm text-gray-500">Catat rentang tanggal penuh saat kendaraan tidak dapat digunakan. Tanggal tagihan servis tidak otomatis dianggap sebagai lama kendaraan tidak tersedia.</p></div>
    @can('master-mobil-update')
        <form method="POST" action="{{ route('dashboard-armada.availability.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            @csrf
            <div><label for="availability-mobil" class="block text-xs font-semibold text-gray-600 mb-1">Armada</label><select id="availability-mobil" name="mobil_id" required class="w-full rounded-lg border-gray-300 text-sm"><option value="">Pilih Armada</option>@foreach($fleetOptions as $mobil)<option value="{{ $mobil->id }}" @selected((string) old('mobil_id') === (string) $mobil->id)>{{ $mobil->nomor_polisi }}</option>@endforeach</select></div>
            <div><label for="availability-start" class="block text-xs font-semibold text-gray-600 mb-1">Mulai</label><input id="availability-start" name="start_date" type="date" value="{{ old('start_date') }}" required class="w-full rounded-lg border-gray-300 text-sm"></div>
            <div><label for="availability-end" class="block text-xs font-semibold text-gray-600 mb-1">Selesai (opsional)</label><input id="availability-end" name="end_date" type="date" value="{{ old('end_date') }}" class="w-full rounded-lg border-gray-300 text-sm"><p class="mt-1 text-xs text-gray-500">Kosong = masih tidak tersedia.</p></div>
            <div><label for="availability-reason" class="block text-xs font-semibold text-gray-600 mb-1">Alasan</label><input id="availability-reason" name="reason" maxlength="255" value="{{ old('reason') }}" placeholder="Servis / libur / lainnya" required class="w-full rounded-lg border-gray-300 text-sm"></div>
            <div class="flex items-start pt-5"><button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm" type="submit">Simpan Periode</button></div>
        </form>
    @endcan
    <div class="overflow-x-auto"><table class="min-w-full text-sm divide-y divide-gray-200"><thead class="text-xs uppercase text-gray-500"><tr>@foreach(['Armada', 'Mulai', 'Selesai', 'Alasan', 'Aksi'] as $heading)<th scope="col" class="py-3 px-3 text-left">{{ $heading }}</th>@endforeach</tr></thead><tbody class="divide-y divide-gray-100">
        @forelse($availabilityPeriods as $period)
            <tr>
                <td class="py-3 px-3">{{ $period->mobil?->nomor_polisi ?: '-' }}</td>
                <td class="py-3 px-3">{{ $period->start_date->format('d M Y') }}</td>
                <td class="py-3 px-3">{{ $period->end_date?->format('d M Y') ?? 'Masih berlangsung' }}</td>
                <td class="py-3 px-3">{{ $period->reason }}</td>
                <td class="py-3 px-3">
                    @can('master-mobil-update')
                        <details class="mb-2">
                            <summary class="cursor-pointer text-indigo-600">Edit / Selesaikan</summary>
                            <form method="POST" action="{{ route('dashboard-armada.availability.update', $period) }}" class="mt-2 space-y-2 min-w-[220px]">
                                @csrf @method('PATCH')
                                <label class="block text-xs">Mulai<input aria-label="Tanggal mulai {{ $period->mobil?->nomor_polisi }}" name="start_date" type="date" value="{{ $period->start_date->toDateString() }}" required class="block w-full rounded border-gray-300 text-xs"></label>
                                <label class="block text-xs">Selesai<input aria-label="Tanggal selesai {{ $period->mobil?->nomor_polisi }}" name="end_date" type="date" value="{{ $period->end_date?->toDateString() }}" class="block w-full rounded border-gray-300 text-xs"></label>
                                <label class="block text-xs">Alasan<input name="reason" value="{{ $period->reason }}" maxlength="255" required class="block w-full rounded border-gray-300 text-xs"></label>
                                <button class="px-3 py-1.5 bg-indigo-600 text-white rounded text-xs" type="submit">Simpan Perubahan</button>
                            </form>
                        </details>
                        <form method="POST" action="{{ route('dashboard-armada.availability.destroy', $period) }}" onsubmit="return confirm('Hapus catatan tidak tersedia ini? Perhitungan utilisasi akan diperbarui.');">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-800">Hapus</button></form>
                    @else<span class="text-gray-400">-</span>@endcan
                </td>
            </tr>
        @empty<tr><td colspan="5" class="py-4 text-center text-gray-500">Belum ada catatan tidak tersedia yang beririsan dengan periode ini.</td></tr>@endforelse
    </tbody></table></div>
    @if($availabilityPeriods->hasPages()){{ $availabilityPeriods->links() }}@endif
</section>
