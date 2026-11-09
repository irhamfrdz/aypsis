@extends('layouts.app')

@section('title', 'Pengisian Tanggal Dokumen')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pengisian Tanggal Dokumen</h1>
            <p class="text-sm text-gray-600 mt-1">Pantau tanggal proses dokumen berdasarkan kapal, voyage, dan nomor BL atau tanda terima.</p>
        </div>
        <a href="{{ route('pengisian-tanggal-dokumen.create') }}" class="inline-flex justify-center items-center px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium"><i class="fas fa-plus mr-2"></i>Input Tanggal Dokumen</a>
    </div>

    @if(session('success'))<div class="mb-5 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700">{{ session('success') }}</div>@endif
    <form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 mb-5 grid md:grid-cols-4 gap-3">
        <select id="filter_kapal" name="kapal_id" class="rounded-lg border-gray-300"><option value="">Semua Kapal</option>@foreach($kapals as $kapal)<option value="{{ $kapal->id }}" @selected(request('kapal_id') == $kapal->id)>{{ $kapal->nama_kapal }}</option>@endforeach</select>
        <select id="filter_voyage" name="no_voyage" data-selected="{{ request('no_voyage') }}" class="rounded-lg border-gray-300"><option value="">Semua Voyage</option></select>
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nomor BL / tanda terima" class="rounded-lg border-gray-300">
        <button class="rounded-lg bg-gray-800 hover:bg-gray-900 text-white px-4 py-2">Filter</button>
    </form>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500"><tr><th class="px-4 py-3">Kapal / Voyage</th><th class="px-4 py-3">Jenis</th><th class="px-4 py-3">Nomor Dokumen</th><th class="px-4 py-3">Tahapan Terisi</th><th class="px-4 py-3">Diperbarui</th><th class="px-4 py-3">Aksi</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($records as $record)
                        @php($completedDates = collect(['tanggal_terima_dokumen_jkt_customer','tanggal_terima_dokumen_packinglist','tanggal_dokumen_invoice_faktur_pajak','tanggal_kirim_dokumen_ppjk','tanggal_terbit_sppb_ppftz','tanggal_terima_dokumen_dari_ppjk','tanggal_kirim_dokumen_jakarta','tanggal_terima_sj_cabang','tanggal_terima_ppftz_cabang'])->filter(fn ($field) => $record->$field !== null)->count())
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3"><span class="font-medium text-gray-800">{{ $record->nama_kapal }}</span><span class="block text-xs text-gray-500">Voyage {{ $record->no_voyage }}</span></td>
                            <td class="px-4 py-3">{{ $record->jenis_dokumen === 'BL' ? 'BL' : 'Tanda Terima' }}</td>
                            <td class="px-4 py-3 font-medium">{{ $record->nomor_dokumen }}</td>
                            <td class="px-4 py-3">{{ $completedDates }} / 9 tanggal</td>
                            <td class="px-4 py-3 text-gray-500">{{ $record->updated_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3"><a href="{{ route('pengisian-tanggal-dokumen.edit', $record) }}" class="text-blue-600 hover:text-blue-800 font-medium">Ubah</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">Belum ada tanggal dokumen yang diisi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $records->links() }}</div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', async () => {
    const kapal = document.getElementById('filter_kapal'), voyage = document.getElementById('filter_voyage');
    const selected = voyage.dataset.selected;
    const load = async () => {
        voyage.innerHTML = '<option value="">Semua Voyage</option>';
        if (!kapal.value) return;
        const response = await fetch(`{{ url('/pengisian-tanggal-dokumen/get-voyages') }}?kapal_id=${encodeURIComponent(kapal.value)}`, {headers: {'Accept':'application/json'}});
        const data = await response.json();
        data.voyages.forEach(item => { const option = document.createElement('option'); option.value = item; option.textContent = item; option.selected = item === selected; voyage.appendChild(option); });
    };
    kapal.addEventListener('change', load);
    if (kapal.value) await load();
});
</script>
@endsection
