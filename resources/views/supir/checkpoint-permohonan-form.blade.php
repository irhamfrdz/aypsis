@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi Select2 pada dropdown kontainer biasa
            $('select.select-kontainer').each(function() {
                $(this).select2({
                    placeholder: 'Cari nomor kontainer',
                    width: '100%'
                });
            });

            // Inisialisasi Select2 dengan tags pada dropdown antar kontainer perbaikan
            $('select.select-kontainer-perbaikan').each(function() {
                $(this).select2({
                    placeholder: 'Pilih atau ketik nomor kontainer',
                    width: '100%',
                    tags: true,
                    tokenSeparators: [',', ' '],
                    createTag: function (params) {
                        var term = $.trim(params.term);
                        if (term === '') {
                            return null;
                        }
                        return {
                            id: term,
                            text: term,
                            newTag: true
                        }
                    }
                });
            });
        });
    </script>
@endpush

@php
    // Use the resolved kegiatan name (if available) so checks work even when
    // $permohonan->kegiatan holds a kode_kegiatan instead of the display name.
    $kegiatanLower = strtolower($kegiatanName ?? ($permohonan->kegiatan ?? ''));
    $isTarikSewa = (stripos($kegiatanLower, 'tarik') !== false && stripos($kegiatanLower, 'sewa') !== false)
        || (stripos($kegiatanLower, 'pengambilan') !== false)
        || ($kegiatanLower === 'pengambilan');
    $isPerbaikanKontainer = (stripos($kegiatanLower, 'perbaikan') !== false && stripos($kegiatanLower, 'kontainer') !== false)
        || (stripos($kegiatanLower, 'repair') !== false && stripos($kegiatanLower, 'container') !== false);
    $isAntarKontainerSewa = (stripos($kegiatanLower, 'antar') !== false && stripos($kegiatanLower, 'kontainer') !== false && stripos($kegiatanLower, 'sewa') !== false);
    $isAntarSewa = stripos($kegiatanLower, 'antar') !== false && stripos($kegiatanLower, 'sewa') !== false;
    $isAntarKontainerPerbaikan = (stripos($kegiatanLower, 'antar') !== false && stripos($kegiatanLower, 'kontainer') !== false && stripos($kegiatanLower, 'perbaikan') !== false);
@endphp

@for ($i = 0; $i < $permohonan->jumlah_kontainer; $i++)
    <div class="relative mt-1">
        <label class="block text-xs font-medium text-gray-500 mb-1">Kontainer #{{ $i + 1 }}</label>
        @if($isAntarKontainerPerbaikan)
            {{-- For antar kontainer perbaikan, show dropdown from master stock kontainer but allow free text --}}
            <select name="nomor_kontainer[]" class="select-kontainer-perbaikan block w-full rounded-lg border border-indigo-300 bg-white shadow focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition p-2.5 pr-10" required>
                <option value="">-- Pilih atau Ketik Nomor Kontainer {{ $permohonan->ukuran }}ft #{{ $i + 1 }} --</option>
                @if(isset($stockKontainers) && $stockKontainers->isNotEmpty())
                    @foreach($stockKontainers as $stock)
                        <option value="{{ $stock->nomor_seri_gabungan }}">{{ $stock->nomor_seri_gabungan }} - {{ $stock->ukuran }}ft ({{ ucfirst($stock->status) }})</option>
                    @endforeach
                @endif
            </select>
        @elseif($isAntarKontainerSewa)
            {{-- For antar kontainer sewa, allow free text input --}}
            <input type="text" name="nomor_kontainer[]" class="block w-full rounded-lg border border-indigo-300 bg-white shadow focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition p-2.5" placeholder="Masukkan nomor kontainer {{ $permohonan->ukuran }}ft #{{ $i + 1 }}" required>
        @elseif($isPerbaikanKontainer)
            {{-- For perbaikan kontainer, allow free text input regardless of vendor --}}
            <input type="text" name="nomor_kontainer[]" class="block w-full rounded-lg border border-indigo-300 bg-white shadow focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition p-2.5" placeholder="Masukkan nomor kontainer #{{ $i + 1 }}" required>
        @elseif($isAntarSewa)
            {{-- For antar kontainer sewa, allow free text input regardless of vendor --}}
            <input type="text" name="nomor_kontainer[]" class="block w-full rounded-lg border border-indigo-300 bg-white shadow focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition p-2.5" placeholder="Masukkan nomor kontainer #{{ $i + 1 }}" required>
        @elseif(in_array($permohonan->vendor_perusahaan, ['ZONA','DPE','SOC']) && $isTarikSewa)
            {{-- For sewa pickup (tarik kontainer sewa), require selecting from approved/tagihan group kontainers --}}
            @if(isset($kontainerList) && $kontainerList->isNotEmpty())
                <select name="nomor_kontainer[]" class="select-kontainer block w-full rounded-lg border border-indigo-300 bg-white shadow focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition p-2.5 pr-10" required>
                    <option value="">-- Pilih Kontainer #{{ $i + 1 }} --</option>
                    @foreach($kontainerList as $kontainer)
                        {{-- Send the displayed serial as the option value so the controller
                             receives exactly what the driver sees/chooses. This avoids the
                             controller creating records with numeric ids as serials. --}}
                        <option value="{{ $kontainer->nomor_seri_gabungan }}">{{ $kontainer->nomor_seri_gabungan }}</option>
                    @endforeach
                </select>
            @else
                <input type="text" name="nomor_kontainer[]" class="block w-full rounded-lg border border-indigo-300 bg-white shadow focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition p-2.5" placeholder="Tidak ada kontainer approved di grup tagihan" required>
            @endif
        @elseif(in_array($permohonan->vendor_perusahaan, ['ZONA','DPE','SOC']))
            {{-- Allow free-text for vendors that supply container numbers (legacy behavior) when not a sewa pickup --}}
            <input type="text" name="nomor_kontainer[]" class="block w-full rounded-lg border border-indigo-300 bg-white shadow focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition p-2.5" placeholder="Masukkan nomor kontainer #{{ $i + 1 }}" required>
        @else
            <select name="nomor_kontainer[]" class="select-kontainer block w-full rounded-lg border border-indigo-300 bg-white shadow focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition p-2.5 pr-10" required>
                <option value="">-- Pilih Kontainer #{{ $i + 1 }} --</option>
                    @if(isset($kontainerList))
                    @foreach($kontainerList as $kontainer)
                        {{-- Keep select values as the serial so what supir selects is what gets submitted. --}}
                        <option value="{{ $kontainer->nomor_seri_gabungan }}">{{ $kontainer->nomor_seri_gabungan }}</option>
                    @endforeach
                @endif
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16l4-4-4-4m8 8V8" /></svg>
            </div>
        @endif
    </div>
@endfor
<p class="text-xs text-gray-500 mt-1">
    @if($isAntarKontainerPerbaikan)
        Pilih dari master stock kontainer {{ $permohonan->ukuran }}ft atau ketik nomor kontainer yang akan diantar untuk perbaikan.
    @elseif($isAntarKontainerSewa)
        Masukkan nomor kontainer {{ $permohonan->ukuran }}ft yang akan diantar ke customer.
    @elseif($isPerbaikanKontainer)
        Masukkan nomor kontainer yang akan diperbaiki.
    @elseif($isAntarSewa)
        Masukkan nomor kontainer yang akan diantar.
    @else
        Pilih nomor kontainer sesuai jumlah di memo.
    @endif
</p>
