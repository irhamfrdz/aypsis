@extends('layouts.app')

@section('title', 'Pengisian Tanggal Dokumen')

@section('content')
<div class="container mx-auto max-w-7xl px-4 py-6 lg:px-6">
    <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-blue-700">Operasional / Administrasi Dokumen</p>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Pengisian Tanggal Dokumen</h1>
            <p class="text-sm text-gray-600 mt-1">Pantau tanggal proses dokumen berdasarkan kapal, voyage, dan nomor BL atau tanda terima.</p>
        </div>
        <a href="{{ route('pengisian-tanggal-dokumen.create') }}" class="inline-flex justify-center items-center px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"><i class="fas fa-plus mr-2"></i>Input Tanggal Dokumen</a>
    </div>

    @if(session('success'))<div class="mb-5 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700">{{ session('success') }}</div>@endif
    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Dokumen ditemukan</p><p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($records->total(), 0, ',', '.') }}</p><p class="mt-1 text-xs text-gray-500">Sesuai filter yang dipilih</p></div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Data ditampilkan</p><p class="mt-2 text-3xl font-bold text-gray-900">{{ $records->count() }}</p><p class="mt-1 text-xs text-gray-500">Halaman {{ $records->currentPage() }} dari {{ $records->lastPage() }}</p></div>
        <div class="rounded-xl border border-blue-100 bg-blue-50 p-5"><div class="flex items-center gap-2 font-semibold text-blue-900"><i class="fas fa-circle-info" aria-hidden="true"></i> Pencatatan per cabang</div><p class="mt-2 text-sm leading-6 text-blue-800">Pilih cabang saat input untuk menampilkan tahapan dokumen yang sesuai.</p></div>
    </div>
    <form method="GET" class="mb-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-4"><h2 class="font-semibold text-gray-900">Filter Dokumen</h2><p class="mt-1 text-xs text-gray-500">Temukan dokumen berdasarkan kapal, voyage, atau nomor dokumen.</p></div>
        <div class="grid items-end gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div><label for="filter_kapal" class="mb-2 block text-xs font-semibold text-gray-600">Kapal</label><select id="filter_kapal" name="kapal_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"><option value="">Semua Kapal</option>@foreach($kapals as $kapal)<option value="{{ $kapal->id }}" @selected(request('kapal_id') == $kapal->id)>{{ $kapal->nama_kapal }}</option>@endforeach</select></div>
            <div><label for="filter_voyage" class="mb-2 block text-xs font-semibold text-gray-600">Voyage</label><select id="filter_voyage" name="no_voyage" data-selected="{{ request('no_voyage') }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"><option value="">Semua Voyage</option></select></div>
            <div><label for="filter_search" class="mb-2 block text-xs font-semibold text-gray-600">Nomor Dokumen</label><input id="filter_search" type="search" name="search" value="{{ request('search') }}" placeholder="Nomor BL / tanda terima" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"></div>
            <div class="flex gap-2"><button class="flex-1 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800"><i class="fas fa-filter mr-2" aria-hidden="true"></i>Terapkan</button><a href="{{ route('pengisian-tanggal-dokumen.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">Reset</a></div>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-5 py-4"><div><h2 class="font-semibold text-gray-900">Daftar Dokumen</h2><p class="mt-1 text-xs text-gray-500">Riwayat pencatatan dan tahapan proses dokumen.</p></div><span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">{{ $records->total() }} dokumen</span></div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm" aria-label="Daftar pengisian tanggal dokumen">
                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500"><tr><th class="px-4 py-4">Kapal / Voyage</th><th class="px-4 py-4">Jenis</th><th class="px-4 py-4">Nomor Dokumen</th><th class="px-4 py-4">Tahapan Terisi</th><th class="px-4 py-4">Diperbarui</th><th class="px-4 py-4">Aksi</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($records as $record)
                        @php($completedDates = collect(['tanggal_keluar_gate_tujuan','tanggal_keluar_gudang_ayp','tanggal_tiba_gudang_customer','tanggal_keluar_gudang_customer','tanggal_masuk_gate_tujuan','tanggal_terima_dokumen_jkt_customer','tanggal_terima_dokumen_packinglist','tanggal_dokumen_invoice_faktur_pajak','tanggal_kirim_dokumen_ppjk','tanggal_terbit_sppb_ppftz','tanggal_terima_dokumen_dari_ppjk','tanggal_kirim_dokumen_jakarta','tanggal_terima_sj_cabang','tanggal_terima_ppftz_cabang'])->filter(fn ($field) => $record->$field !== null)->count())
                        <tr class="transition-colors hover:bg-blue-50/40">
                            <td class="px-4 py-4"><span class="font-medium text-gray-800">{{ $record->nama_kapal }}</span><span class="block text-xs text-gray-500">Voyage {{ $record->no_voyage }}</span><span class="block text-xs text-gray-500">Cabang: {{ $record->cabang?->nama_cabang ?? 'Belum dipilih' }}</span></td>
                            <td class="px-4 py-4"><span class="inline-flex whitespace-nowrap rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">{{ $record->jenis_dokumen === 'BL' ? 'BL' : 'Tanda Terima' }}</span></td>
                            <td class="px-4 py-3 font-medium">{{ $record->nomor_dokumen }}</td>
                            <td class="px-4 py-4"><span class="inline-flex items-center gap-2 whitespace-nowrap text-xs font-medium text-gray-700"><span class="h-2 w-2 rounded-full {{ $completedDates ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>{{ $completedDates }} tanggal terisi</span></td>
                            <td class="px-4 py-3 text-gray-500">{{ $record->updated_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-4"><a href="{{ route('pengisian-tanggal-dokumen.edit', $record) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700"><i class="fas fa-pen-to-square" aria-hidden="true"></i> Ubah</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500"><div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400"><i class="fas fa-folder-open text-xl" aria-hidden="true"></i></div><p class="font-semibold text-gray-700">Tidak ada dokumen ditemukan</p><p class="mt-1 text-xs">Sesuaikan filter atau tambahkan pencatatan dokumen baru.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-200 bg-gray-50 px-5 py-4">{{ $records->links() }}</div>
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
