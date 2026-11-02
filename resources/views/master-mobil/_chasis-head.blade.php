@php
    $jenisKendaraan = strtoupper(trim((string) $mobil->jenis));
    $isHead = $jenisKendaraan === 'TRACTOR HEAD';
    $isBuntut = $jenisKendaraan === 'BUNTUT' || str_starts_with($jenisKendaraan, 'BUNTUT ');
    $pasanganKendaraan = $isHead
        ? collect([$mobil->buntut])->filter()
        : ($isBuntut ? $mobil->tractorHeads : collect());
@endphp
@forelse($pasanganKendaraan as $pasangan)
    <div class="mb-1 last:mb-0">
        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium {{ $isHead ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
            {{ $pasangan->nomor_polisi ?: ($pasangan->no_kir ?: $pasangan->kode_no) }}
        </span>
        <span class="mt-1 block text-xs text-gray-500">{{ $pasangan->jenis }}</span>
    </div>
@empty
    <span class="text-gray-400">-</span>
@endforelse
