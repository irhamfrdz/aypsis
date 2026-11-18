@extends('layouts.app')

@section('title', 'Surat Jalan Bongkaran Tanjung Pinang')

@push('styles')
<style>
    body:has(.bongkaran-page) { font-family: Arial, sans-serif; font-size: 12px; }
    body:has(.bongkaran-page) #page-content { background: #F5F7FA; }
    .bongkaran-page { --bongkaran-navy:#1E3A5F;--bongkaran-line:#e1e6ed;font:12px/1.5 Arial,sans-serif;color:#344256;padding:0;min-width:0; }
    .bongkaran-page :is(p,span,a,button,input,select,textarea,label,table,th,td,div) { font-family:Arial,sans-serif;font-size:12px; }
    .bongkaran-page h1 { font:600 20px/1.4 Arial,sans-serif;color:var(--bongkaran-navy); }
    .bongkaran-page :is(h2,h3) { font:600 16px/1.4 Arial,sans-serif;color:var(--bongkaran-navy); }
    .bongkaran-page .bongkaran-heading { margin-bottom:18px; }
    .bongkaran-page .bongkaran-heading nav { color:#8490a1; }
    .bongkaran-page .bongkaran-heading a { color:#5b718d; }
    .bongkaran-page .bongkaran-panel { border-color:var(--bongkaran-line);border-radius:8px;background:#fff;box-shadow:0 2px 6px rgb(30 58 95 / 4%);min-width:0; }
    .bongkaran-page .bongkaran-panel-header { padding:16px;border-color:var(--bongkaran-line); }
    .bongkaran-page .bongkaran-panel-header>.flex { flex-wrap:wrap;gap:12px 20px; }
    .bongkaran-page .bongkaran-panel-header>.flex>div:first-child>.flex { margin-top:5px;flex-wrap:wrap;gap:6px 20px;color:#748091; }
    .bongkaran-page .bongkaran-actions { display:flex;flex-wrap:wrap;gap:8px; }
    .bongkaran-page .bongkaran-actions :is(a,button) { min-height:34px;padding:7px 12px;border:1px solid #cdd7e4;border-radius:5px;background:#fff;color:var(--bongkaran-navy);box-shadow:none;font-weight:500;transition:none;white-space:nowrap; }
    .bongkaran-page .bongkaran-actions .tp-primary { background:var(--bongkaran-navy);color:#fff;border-color:var(--bongkaran-navy); }
    .bongkaran-page .bongkaran-actions :is(a,button):hover { background:#edf2f8; }
    .bongkaran-page .bongkaran-actions .tp-primary:hover { background:#2b4c75; }
    .bongkaran-page .bongkaran-actions :is(svg,i) { width:14px;height:14px;font-size:12px;margin-right:6px; }
    .bongkaran-page .bongkaran-panel-body { padding:16px; }
    .bongkaran-page .bongkaran-filters { margin-bottom:16px;padding:12px;background:#fafbfd;border:1px solid #e7ebf1;border-radius:6px; }
    .bongkaran-page .bongkaran-filters>div { gap:12px; }
    .bongkaran-page .bongkaran-filters label { color:#617087;font-weight:400; }
    .bongkaran-page input:not([type="hidden"]),.bongkaran-page select { min-height:34px;padding:7px 10px;border:1px solid #d5dde7;border-radius:5px;background:#fff;box-shadow:none;font-size:12px; }
    .bongkaran-page .bongkaran-filters button[type="submit"] { min-height:34px;padding:7px 12px;border:1px solid var(--bongkaran-navy);border-radius:5px;background:var(--bongkaran-navy);color:#fff; }
    .bongkaran-page .bongkaran-filters .tp-reset { min-height:34px;padding:7px 12px;border:1px solid #cdd7e4;border-radius:5px;background:#fff;color:var(--bongkaran-navy); }
    .bongkaran-page .bongkaran-summary { display:flex;flex-wrap:wrap;gap:4px 20px;padding:10px 12px;background:#f5f7fa;border:1px solid #e6ebf1;border-radius:5px;color:#748091; }
    .bongkaran-page .bongkaran-summary strong { color:#536379;font-weight:600; }
    .bongkaran-page .tp-table { width:100%;min-width:1400px;border-collapse:collapse; }
    .bongkaran-page thead th { padding:10px 12px;border-bottom:1px solid var(--bongkaran-line);background:#F5F7FA;color:#647184;font-size:12px;font-weight:600;letter-spacing:.01em; }
    .bongkaran-page .tp-table td { padding:10px 12px;border-bottom:1px solid var(--bongkaran-line);vertical-align:middle;color:#344256;line-height:1.5; }
    .bongkaran-page tbody tr:hover { background:#f8fafc; }
    .bongkaran-page .tp-muted { color:#748091; }
    .bongkaran-page .tp-status { display:inline-flex;padding:3px 7px;border-radius:4px;background:#edf6f1;color:#34704f;font-weight:500; }
    .bongkaran-page .tp-pending { display:inline-flex;padding:3px 7px;border-radius:4px;background:#fcf3e5;color:#946b2c;font-weight:500; }
    .bongkaran-page .tp-modal-panel { border:1px solid #d1d5db;border-radius:8px;background:#fff;box-shadow:0 8px 30px rgb(30 58 95 / 12%); }
    .bongkaran-page .tp-modal-panel input,.bongkaran-page .tp-modal-panel textarea { min-height:36px;padding:8px 10px;border:1px solid #d5dde7;border-radius:5px;background:#fff;font-size:12px; }
    .bongkaran-page .tp-modal-panel textarea { min-height:75px; }
    .bongkaran-page .tp-modal-panel label { display:block;margin-bottom:5px;color:#536379;font-weight:500; }
    body.tp-modal-open { overflow:hidden; }
    .bongkaran-page .overflow-x-auto::-webkit-scrollbar { height:5px; }
    .bongkaran-page .overflow-x-auto::-webkit-scrollbar-thumb { background:#d5dde7;border-radius:4px; }
    .bongkaran-page .tp-pagination { margin-top:16px;padding-top:12px;border-top:1px solid var(--bongkaran-line); }
    @media(max-width:640px) { .bongkaran-page .bongkaran-panel-header,.bongkaran-page .bongkaran-panel-body { padding:12px; } }
</style>
@endpush

@section('content')
<div class="bongkaran-page flex-1 p-6">
    <div class="bongkaran-heading mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Surat Jalan Bongkaran Tanjung Pinang</h1>
            <nav class="mt-1 flex text-sm"><a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a><span class="mx-2">/</span><a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.index') }}" class="hover:text-blue-600">Surat Jalan Bongkaran</a><span class="mx-2">/</span><span>Data Kapal & Voyage</span></nav>
        </div>
    </div>

    @if(session('success'))<div class="mb-5 flex items-center rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-5 flex items-center rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>@endif

    <div class="bongkaran-panel rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="bongkaran-panel-header border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Surat Jalan Bongkaran Tanjung Pinang</h2>
                    <div class="mt-2 flex items-center gap-4">
                        <span><span class="font-medium">Kapal:</span> {{ $selectedKapal }}</span>
                        <span><span class="font-medium">Voyage:</span> {{ $selectedVoyage }}</span>
                    </div>
                </div>
                <div class="bongkaran-actions flex gap-2">
                    @can('surat-jalan-bongkaran-tanjung-pinang-create')
                    <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.create', ['nama_kapal' => $selectedKapal, 'no_voyage' => $selectedVoyage]) }}" class="tp-primary inline-flex items-center"><i class="fas fa-plus"></i>Tambah Surat Jalan</a>
                    @if($mode === 'manifest')
                    <button type="button" onclick="openTpBulkModal()" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-white hover:bg-indigo-700"><i class="fas fa-layer-group"></i>Tambah Massal</button>
                    @endif
                    @endcan
                    <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.index') }}" class="inline-flex items-center"><i class="fas fa-sync-alt"></i>Ganti Kapal/Voyage</a>
                </div>
            </div>
        </div>

        <div class="bongkaran-panel-body p-6">
            <form method="GET" action="{{ route('surat-jalan-bongkaran-tanjung-pinang.list') }}" class="bongkaran-filters mb-6">
                <input type="hidden" name="nama_kapal" value="{{ $selectedKapal }}">
                <input type="hidden" name="no_voyage" value="{{ $selectedVoyage }}">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-wrap items-center gap-6">
                        <div class="flex items-center gap-3">
                            <label for="mode" class="whitespace-nowrap text-sm font-medium">Tampilan:</label>
                            <select name="mode" id="mode" onchange="this.form.submit()">
                                <option value="manifest" @selected($mode === 'manifest')>Manifest</option>
                                <option value="surat_jalan" @selected($mode === 'surat_jalan')>Surat Jalan Bongkaran</option>
                            </select>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-5 gap-y-1">
                            <span><strong>Total Manifest:</strong> {{ number_format($stats['manifest']) }}</span>
                            <span><strong>Sudah dibuat SJ:</strong> {{ number_format($stats['sudah_sj']) }}</span>
                            <span><strong>Belum dibuat SJ:</strong> {{ number_format($stats['belum_sj']) }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4 md:flex-row">
                        <div class="flex-1">
                            <label for="search" class="mb-1 block text-sm font-medium">Pencarian</label>
                            <input type="text" id="search" name="search" value="{{ $search }}" class="w-full" placeholder="{{ $mode === 'surat_jalan' ? 'Cari nomor surat jalan, kontainer, atau BL...' : 'Cari nomor BL, kontainer, seal, barang, atau penerima...' }}">
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="inline-flex items-center"><i class="fas fa-search mr-2"></i>Cari</button>
                            <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.list', ['nama_kapal' => $selectedKapal, 'no_voyage' => $selectedVoyage, 'mode' => $mode]) }}" class="tp-reset inline-flex items-center"><i class="fas fa-sync-alt mr-2"></i>Reset</a>
                        </div>
                    </div>
                </div>
            </form>

            @if($mode === 'manifest')
            <div class="overflow-x-auto">
                <table class="tp-table" id="manifestTable">
                    <thead><tr>
                        <th>No. Urut</th><th>Nomor BL</th><th>Nomor Kontainer</th><th>Seal</th><th>Ukuran / Tipe</th><th>Pengirim</th><th>Penerima</th><th>Nama Barang</th><th>Term</th><th>Status Surat Jalan</th>
                    </tr></thead>
                    <tbody class="bg-white">
                    @forelse($manifests as $manifest)
                        <tr>
                            <td>{{ $manifest->nomor_urut ?: '—' }}</td>
                            <td class="font-medium">{{ $manifest->nomor_bl ?: '—' }}</td>
                            <td>{{ $manifest->nomor_kontainer ?: '—' }}</td>
                            <td>{{ $manifest->no_seal ?: '—' }}</td>
                            <td>{{ $manifest->size_kontainer ?: '—' }} / {{ $manifest->tipe_kontainer ?: '—' }}</td>
                            <td>{{ $manifest->pengirim ?: '—' }}</td>
                            <td>{{ $manifest->penerima ?: '—' }}</td>
                            <td class="max-w-xs truncate" title="{{ $manifest->nama_barang }}">{{ $manifest->nama_barang ?: '—' }}</td>
                            <td>{{ $manifest->term ?: '—' }}</td>
                            <td class="whitespace-nowrap">
                                @if($manifest->suratJalanBongkaranTanjungPinang)
                                    <span class="tp-status">SJ {{ $manifest->suratJalanBongkaranTanjungPinang->nomor_surat_jalan }}</span>
                                @else
                                    <span class="tp-pending">Belum dibuat</span>
                                    @can('surat-jalan-bongkaran-tanjung-pinang-create')
                                        <button type="button" onclick="openTpCreateModal(this)" data-manifest-id="{{ $manifest->id }}" data-kapal="{{ $selectedKapal }}" data-voyage="{{ $selectedVoyage }}" data-bl="{{ $manifest->nomor_bl }}" data-pengirim="{{ $manifest->pengirim }}" data-penerima="{{ $manifest->penerima }}" data-barang="{{ $manifest->nama_barang }}" data-kontainer="{{ $manifest->nomor_kontainer }}" data-seal="{{ $manifest->no_seal }}" data-size="{{ $manifest->size_kontainer }}" data-tipe="{{ $manifest->tipe_kontainer }}" data-term="{{ $manifest->term }}" class="ml-2 inline-flex items-center rounded bg-blue-600 px-3 py-1.5 text-white hover:bg-blue-700"><i class="fas fa-plus mr-1"></i>Buat SJ</button>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="py-12 text-center text-gray-500">Tidak ada data manifest untuk {{ $selectedKapal }} / {{ $selectedVoyage }}.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="tp-table" id="suratJalanTable">
                    <thead><tr><th>Nomor Surat Jalan</th><th>Tanggal</th><th>Kapal / Voyage</th><th>Nomor BL</th><th>Nomor Kontainer / Seal</th><th>Pengirim / Penerima</th><th>Tujuan</th><th>Aksi</th></tr></thead>
                    <tbody class="bg-white">
                    @forelse($suratJalans as $suratJalan)
                        <tr>
                            <td class="font-medium">{{ $suratJalan->nomor_surat_jalan }}</td>
                            <td>{{ $suratJalan->tanggal_surat_jalan?->format('d/m/Y') ?? '—' }}</td>
                            <td>{{ $suratJalan->nama_kapal }}<div class="tp-muted">{{ $suratJalan->no_voyage }}</div></td>
                            <td>{{ $suratJalan->no_bl ?: '—' }}</td>
                            <td>{{ $suratJalan->no_kontainer ?: '—' }}<div class="tp-muted">{{ $suratJalan->no_seal ?: '—' }}</div></td>
                            <td>{{ $suratJalan->pengirim ?: '—' }}<div class="tp-muted">{{ $suratJalan->penerima ?: '—' }}</div></td>
                            <td>{{ $suratJalan->tujuan_pengiriman ?: '—' }}</td>
                            <td class="whitespace-nowrap">
                                <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.print', $suratJalan->id) }}" target="_blank" class="px-2 py-1 text-blue-700" title="Cetak"><i class="fas fa-print"></i></a>
                                @can('surat-jalan-bongkaran-tanjung-pinang-update')<a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.edit', $suratJalan->id) }}" class="px-2 py-1 text-blue-700" title="Ubah"><i class="fas fa-pen"></i></a>@endcan
                                @can('surat-jalan-bongkaran-tanjung-pinang-delete')<form method="POST" action="{{ route('surat-jalan-bongkaran-tanjung-pinang.destroy', $suratJalan->id) }}" class="inline" onsubmit="return confirm('Hapus surat jalan ini?')">@csrf @method('DELETE')<input type="hidden" name="nama_kapal" value="{{ $selectedKapal }}"><input type="hidden" name="no_voyage" value="{{ $selectedVoyage }}"><button class="px-2 py-1 text-red-700" title="Hapus"><i class="fas fa-trash"></i></button></form>@endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-12 text-center text-gray-500">Belum ada surat jalan untuk pilihan ini.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @endif

            @php($pageData = $mode === 'manifest' ? $manifests : $suratJalans)
            @if($pageData->hasPages())
                <div class="bongkaran-panel-body tp-pagination flex flex-wrap items-center justify-between gap-3">
                    <span class="tp-muted">Menampilkan {{ $pageData->firstItem() }}–{{ $pageData->lastItem() }} dari {{ $pageData->total() }} data</span>
                    {{ $pageData->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="tpCreateModal" class="fixed inset-0 z-50 hidden h-full w-full overflow-y-auto bg-gray-600 bg-opacity-50" style="background-color:rgba(75,85,99,.55);" role="dialog" aria-modal="true" aria-labelledby="tpCreateModalTitle" onclick="if(event.target===this) closeTpCreateModal()">
        <div class="tp-modal-panel relative top-10 mx-auto mb-10 w-11/12 max-w-4xl rounded-lg border bg-white p-5 shadow-lg">
            <div class="flex items-center justify-between border-b pb-3">
                <div><h3 id="tpCreateModalTitle" class="text-xl font-semibold text-gray-900">Buat Surat Jalan Bongkaran</h3><p class="mt-1 text-xs text-gray-500">Data manifest, kapal, dan voyage sudah diisikan.</p></div>
                <button type="button" onclick="closeTpCreateModal()" class="text-2xl text-gray-400 hover:text-gray-600" aria-label="Tutup"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form method="POST" action="{{ route('surat-jalan-bongkaran-tanjung-pinang.store') }}" class="mt-4">
                @csrf
                <input type="hidden" name="manifest_id" id="tp_manifest_id" value="{{ old('manifest_id') }}">
                <input type="hidden" name="nama_kapal" id="tp_nama_kapal" value="{{ old('nama_kapal', $selectedKapal) }}">
                <input type="hidden" name="no_voyage" id="tp_no_voyage" value="{{ old('no_voyage', $selectedVoyage) }}">
                <div class="max-h-[70vh] overflow-y-auto px-2">
                <div class="mb-4 rounded-md border border-blue-100 bg-blue-50 px-3 py-2 text-xs text-blue-900"><strong id="tp_selected_container">Pilih manifest</strong><span class="ml-2" id="tp_selected_ship"></span></div>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="md:col-span-2"><h4 class="mb-2 text-md font-semibold text-gray-800">Informasi Dasar</h4></div>
                    <div><label for="tp_nomor_surat_jalan">Nomor Surat Jalan</label><input id="tp_nomor_surat_jalan" type="text" readonly value="{{ $nomorSuratJalanPreview }}" data-next-id="{{ (int) \App\Models\SuratJalanBongkaranTanjungPinang::max('id') + 1 }}" class="w-full bg-gray-100 text-gray-600"></div>
                    <div><label for="tp_tanggal_surat_jalan">Tanggal Surat Jalan <span class="text-red-600">*</span></label><input id="tp_tanggal_surat_jalan" type="date" name="tanggal_surat_jalan" required value="{{ old('tanggal_surat_jalan', now()->format('Y-m-d')) }}" class="w-full"></div>
                    <div><label for="tp_no_bl">Nomor BL</label><input id="tp_no_bl" name="no_bl" value="{{ old('no_bl') }}" class="w-full"></div>
                    <div><label for="tp_no_kontainer">Nomor Kontainer</label><input id="tp_no_kontainer" name="no_kontainer" value="{{ old('no_kontainer') }}" class="w-full"></div>
                    <div><label for="tp_no_seal">Nomor Seal</label><input id="tp_no_seal" name="no_seal" value="{{ old('no_seal') }}" class="w-full"></div>
                    <div><label for="tp_size">Ukuran Kontainer</label><input id="tp_size" name="size" value="{{ old('size') }}" class="w-full"></div>
                    <div><label for="tp_tipe_kontainer">Tipe Kontainer</label><input id="tp_tipe_kontainer" name="tipe_kontainer" value="{{ old('tipe_kontainer') }}" class="w-full"></div>
                    <div><label for="tp_term">Term</label><input id="tp_term" name="term" value="{{ old('term') }}" class="w-full"></div>
                    <div><label for="tp_pengirim">Pengirim</label><input id="tp_pengirim" name="pengirim" value="{{ old('pengirim') }}" class="w-full"></div>
                    <div><label for="tp_penerima">Penerima</label><input id="tp_penerima" name="penerima" value="{{ old('penerima') }}" class="w-full"></div>
                    <div class="md:col-span-2 mt-2"><h4 class="mb-2 text-md font-semibold text-gray-800">Informasi Pengiriman</h4></div>
                    <div class="md:col-span-2"><label for="tp_jenis_barang">Jenis Barang</label><input id="tp_jenis_barang" name="jenis_barang" value="{{ old('jenis_barang') }}" class="w-full"></div>
                    <div><label for="tp_tujuan_pengambilan">Tujuan Pengambilan</label><input id="tp_tujuan_pengambilan" name="tujuan_pengambilan" value="{{ old('tujuan_pengambilan') }}" class="w-full"></div>
                    <div><label for="tp_tujuan_pengiriman">Tujuan Pengiriman</label><input id="tp_tujuan_pengiriman" name="tujuan_pengiriman" value="{{ old('tujuan_pengiriman') }}" class="w-full"></div>
                    <div class="md:col-span-2"><label for="tp_aktifitas">Aktivitas / Catatan</label><textarea id="tp_aktifitas" name="aktifitas" rows="3" class="w-full">{{ old('aktifitas') }}</textarea></div>
                </div>
                </div>
                <div class="mt-4 flex justify-end gap-2 border-t pt-4">
                    <button type="button" onclick="closeTpCreateModal()" class="rounded bg-gray-500 px-4 py-2 text-sm font-medium text-white hover:bg-gray-600">Batal</button>
                    <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"><i class="fas fa-save mr-1"></i>Simpan Surat Jalan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="tpBulkModal" class="fixed inset-0 z-[60] hidden h-full w-full overflow-y-auto bg-gray-600 bg-opacity-50" style="background-color:rgba(75,85,99,.55);" role="dialog" aria-modal="true" aria-labelledby="tpBulkModalTitle" onclick="if(event.target===this) closeTpBulkModal()">
    <div class="tp-modal-panel relative top-20 mx-auto mb-10 w-11/12 max-w-xl rounded-lg border bg-white p-5 shadow-lg">
        <div class="flex items-center justify-between border-b pb-3">
            <div>
                <h3 id="tpBulkModalTitle" class="text-xl font-semibold text-gray-900">Buat Surat Jalan Massal</h3>
                <p class="mt-1 text-xs text-gray-500">{{ $selectedKapal }} · Voyage {{ $selectedVoyage }}</p>
            </div>
            <button type="button" onclick="closeTpBulkModal()" class="text-2xl text-gray-400 hover:text-gray-600" aria-label="Tutup">&times;</button>
        </div>
        <div class="mt-4">
            <div id="tpBulkAlert" class="mb-4 hidden rounded-md border px-3 py-2 text-sm" role="alert"></div>
            <p class="text-sm text-gray-600">Masukkan satu Nomor BL atau Nomor Kontainer per baris. Data manifest akan disalin otomatis ke setiap surat jalan.</p>
            <div class="mt-4 rounded-md border border-blue-100 bg-blue-50 px-3 py-2 text-sm text-blue-900">
                <span id="tpBulkSelectedCount">0 manifest dimasukkan</span>
            </div>
            <div class="mt-4">
                <label for="tpBulkManifestText" class="mb-1 block text-sm font-medium text-gray-700">Nomor BL / Nomor Kontainer <span class="text-red-600">*</span></label>
                <textarea id="tpBulkManifestText" rows="7" class="w-full rounded-md border border-gray-300 px-3 py-2 font-mono text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Masukkan satu nomor per baris&#10;BL-001&#10;AYPU1234567"></textarea>
            </div>
            <div class="mt-4">
                <label for="tpBulkTanggal" class="mb-1 block text-sm font-medium text-gray-700">Tanggal Surat Jalan <span class="text-red-600">*</span></label>
                <input id="tpBulkTanggal" type="date" required value="{{ now()->format('Y-m-d') }}" class="w-full">
            </div>
        </div>
        <div class="mt-5 flex justify-end gap-2 border-t pt-4">
            <button type="button" onclick="closeTpBulkModal()" class="rounded border border-gray-300 px-4 py-2 text-sm text-gray-700">Batal</button>
            <button type="button" id="tpBulkSubmit" onclick="submitTpBulkCreate()" class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                <span id="tpBulkSubmitLabel">Buat Surat Jalan</span>
            </button>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('tpBulkManifestText')?.addEventListener('input', updateTpBulkSelection);

function updateTpBulkSelection() {
    const lines = (document.getElementById('tpBulkManifestText')?.value || '').split(/\r?\n/).map(line => line.trim()).filter(Boolean);
    const selectedCount = new Set(lines.map(line => line.toLocaleLowerCase())).size;
    const countLabel = document.getElementById('tpBulkSelectedCount');
    const submitButton = document.getElementById('tpBulkSubmit');
    if (countLabel) countLabel.textContent = `${selectedCount} manifest dimasukkan`;
    if (submitButton) submitButton.disabled = selectedCount === 0;
}

function openTpBulkModal() {
    document.getElementById('tpBulkManifestText').value = '';
    updateTpBulkSelection();
    document.getElementById('tpBulkAlert').classList.add('hidden');
    document.getElementById('tpBulkModal').classList.remove('hidden');
    document.body.classList.add('tp-modal-open');
}

function closeTpBulkModal() {
    document.getElementById('tpBulkModal').classList.add('hidden');
    document.body.classList.remove('tp-modal-open');
}

function showTpBulkAlert(message, success = false) {
    const alert = document.getElementById('tpBulkAlert');
    alert.textContent = message;
    alert.className = `mb-4 rounded-md border px-3 py-2 text-sm ${success ? 'border-green-200 bg-green-50 text-green-800' : 'border-red-200 bg-red-50 text-red-800'}`;
}

async function submitTpBulkCreate() {
    const manifestText = document.getElementById('tpBulkManifestText').value;
    const manifestNumbers = manifestText.split(/\r?\n/).map(line => line.trim()).filter(Boolean);
    const tanggal = document.getElementById('tpBulkTanggal').value;
    if (manifestNumbers.length === 0) {
        showTpBulkAlert('Masukkan minimal satu Nomor BL atau Nomor Kontainer.');
        return;
    }
    if (!tanggal) {
        showTpBulkAlert('Tanggal surat jalan wajib diisi.');
        return;
    }

    const button = document.getElementById('tpBulkSubmit');
    const label = document.getElementById('tpBulkSubmitLabel');
    button.disabled = true;
    label.textContent = 'Menyimpan...';

    try {
        const response = await fetch(@json(route('surat-jalan-bongkaran-tanjung-pinang.bulk-store')), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token()),
            },
            body: JSON.stringify({
                nama_kapal: @json($selectedKapal),
                no_voyage: @json($selectedVoyage),
                tanggal_surat_jalan: tanggal,
                manifest_numbers: manifestNumbers,
            }),
        });
        const data = await response.json();
        if (!response.ok || !data.success) {
            const messages = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Gagal membuat surat jalan massal.');
            throw new Error(messages);
        }

        showTpBulkAlert(data.message || 'Surat jalan berhasil dibuat.', true);
        setTimeout(() => { window.location.assign(data.redirect); }, 700);
    } catch (error) {
        showTpBulkAlert(error.message || 'Terjadi kesalahan saat menyimpan surat jalan.');
        button.disabled = false;
        label.textContent = 'Buat Surat Jalan';
    }
}

function openTpCreateModal(button) {
    const data = button.dataset;
    const fields = {
        tp_manifest_id: 'manifestId', tp_nama_kapal: 'kapal', tp_no_voyage: 'voyage', tp_no_bl: 'bl',
        tp_pengirim: 'pengirim', tp_penerima: 'penerima', tp_jenis_barang: 'barang', tp_no_kontainer: 'kontainer',
        tp_no_seal: 'seal', tp_size: 'size', tp_tipe_kontainer: 'tipe', tp_term: 'term'
    };
    Object.entries(fields).forEach(([id, key]) => { document.getElementById(id).value = data[key] || ''; });
    updateTpNomorSuratJalan();
    document.getElementById('tp_selected_container').textContent = data.kontainer || data.bl || 'Manifest';
    document.getElementById('tp_selected_ship').textContent = `${data.kapal || ''} · ${data.voyage || ''}`;
    const modal = document.getElementById('tpCreateModal');
    modal.classList.remove('hidden');
    document.body.classList.add('tp-modal-open');
    document.getElementById('tp_nomor_surat_jalan').focus();
}
function updateTpNomorSuratJalan() {
    const input = document.getElementById('tp_nomor_surat_jalan');
    const tanggal = document.getElementById('tp_tanggal_surat_jalan').value;
    if (!tanggal) return;
    input.value = `SJB/TP/${tanggal.replaceAll('-', '')}/${String(input.dataset.nextId).padStart(6, '0')}`;
}
function closeTpCreateModal() {
    const modal = document.getElementById('tpCreateModal');
    modal.classList.add('hidden');
    document.body.classList.remove('tp-modal-open');
}
document.addEventListener('keydown', event => { if (event.key === 'Escape') { closeTpCreateModal(); closeTpBulkModal(); } });
document.getElementById('tp_tanggal_surat_jalan').addEventListener('change', updateTpNomorSuratJalan);
@if($errors->any())
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('tpCreateModal');
    modal.classList.remove('hidden'); document.body.classList.add('tp-modal-open'); updateTpNomorSuratJalan();
});
@endif
</script>
@endpush
@endsection
