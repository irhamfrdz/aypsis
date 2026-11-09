<section aria-label="Detail pergantian ban" class="border-t border-slate-200 pt-6">
    <div class="mb-3 flex flex-wrap items-center gap-2">
        <h5 class="font-bold text-slate-900">Pergantian ban supir</h5>
        <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">{{ number_format($tireDetails->count(), 0, ',', '.') }} ban</span>
    </div>

    @if($tireDetails->isEmpty())
        <p class="rounded-xl bg-white px-4 py-4 text-sm text-slate-500">Tidak ada pergantian ban supir pada periode yang dipilih.</p>
    @else
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            @foreach($tireDetails as $tire)
                <div class="min-w-0 rounded-xl border border-slate-200 bg-white p-4">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="break-words font-bold text-slate-900">{{ $tire['merk'] ?: 'Merek tidak tercatat' }} · {{ $tire['ukuran'] ?: '-' }}</p>
                            <p class="mt-0.5 break-all text-xs text-slate-500">Seri {{ $tire['nomor_seri'] ?: '-' }}</p>
                        </div>
                        <span class="rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700">{{ $tire['kondisi'] ?: '-' }}</span>
                    </div>
                    <dl class="mt-3 space-y-1.5 text-xs">
                        <div class="flex gap-2"><dt class="w-24 shrink-0 text-slate-500">Tanggal pakai</dt><dd class="min-w-0 text-slate-700">{{ \Carbon\Carbon::parse($tire['tanggal'])->format('d/m/Y') }}</dd></div>
                        <div class="flex gap-2"><dt class="w-24 shrink-0 text-slate-500">No. bukti</dt><dd class="min-w-0 break-all text-slate-700">{{ $tire['nomor_bukti'] ?: '-' }}</dd></div>
                        <div class="flex gap-2"><dt class="w-24 shrink-0 text-slate-500">Kendaraan</dt><dd class="min-w-0 break-words text-slate-700">{{ $tire['kendaraan'] }}</dd></div>
                        <div class="flex gap-2"><dt class="w-24 shrink-0 text-slate-500">Penerima</dt><dd class="min-w-0 break-words text-slate-700">{{ $tire['penerima'] }}</dd></div>
                        <div class="flex gap-2"><dt class="w-24 shrink-0 text-slate-500">Sumber</dt><dd class="min-w-0 break-words text-slate-700">{{ $tire['sumber'] }}</dd></div>
                    </dl>
                    <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-3 text-xs">
                        <span class="text-slate-500">Harga ban</span>
                        <span class="font-bold text-slate-900">{{ $tire['harga_beli'] !== null ? 'Rp '.number_format((float) $tire['harga_beli'], 0, ',', '.') : '-' }}</span>
                    </div>
                    @if($tire['keterangan'])
                        <p class="mt-2 break-words text-xs text-slate-600">{{ $tire['keterangan'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</section>
