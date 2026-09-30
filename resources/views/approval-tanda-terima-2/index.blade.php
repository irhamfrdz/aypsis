@extends('layouts.app')

@section('title', 'Approval Tanda Terima 2')
@section('page_title', 'Approval Tanda Terima 2')

@push('styles')
<style>
    #approval-shipper-dialog { width: min(900px, calc(100vw - 24px)); max-height: calc(100vh - 32px); border: 0; border-radius: 16px; padding: 0; overflow-y: auto; }
    #approval-shipper-dialog::backdrop { background: rgb(15 23 42 / 60%); }
    #approval-shipper-dialog [hidden] { display: none !important; }
    .approval-shipper-option { display: block; width: 100%; padding: 10px 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
    .approval-shipper-option:hover, .approval-shipper-option:focus { background: #eef2ff; outline: 2px solid #6366f1; outline-offset: -2px; }
    .approval-goods-dialog { width: min(1000px, calc(100vw - 24px)); max-height: calc(100vh - 32px); border: 0; border-radius: 16px; padding: 0; overflow-y: auto; }
    .approval-goods-dialog::backdrop { background: rgb(15 23 42 / 60%); }
</style>
@endpush

@section('content')
<div class="container mx-auto max-w-7xl px-4 py-6 sm:px-6">
    <div class="mb-6 rounded-2xl bg-gradient-to-r from-indigo-700 to-indigo-600 px-6 py-6 text-white shadow-sm sm:px-8">
        <div class="flex items-start gap-4">
            <div class="hidden rounded-xl bg-white/15 p-3 sm:block"><i class="fas fa-file-signature text-2xl" aria-hidden="true"></i></div>
            <div>
                <h1 class="text-2xl font-bold">Approval Tanda Terima 2</h1>
                <p class="mt-1 max-w-2xl text-sm text-indigo-100">Pilih shipper dari Master Shipper / Consignee untuk tanda terima. Data pilihan digunakan pada manifest voyage JB.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div role="status" class="mb-5 flex items-start gap-2 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <i class="fas fa-circle-check mt-0.5" aria-hidden="true"></i><span>{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form method="GET" action="{{ route('approval-tanda-terima-2.index') }}" class="mb-5 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center gap-3 border-b border-gray-100 bg-gray-50/80 px-5 py-4 sm:px-6">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                <i class="fas fa-filter" aria-hidden="true"></i>
            </span>
            <div>
                <h2 class="font-semibold text-gray-900">Cari Tanda Terima</h2>
                <p class="text-xs text-gray-500">Cari berdasarkan nomor atau pengirim, lalu pilih filter yang dibutuhkan.</p>
            </div>
        </div>
        <div class="space-y-5 px-5 py-5 sm:px-6">
            <div>
                <label for="search" class="mb-2 block text-sm font-semibold text-gray-700">Nomor tanda terima atau pengirim</label>
                <div class="relative">
                    <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" aria-hidden="true"></i>
                    <input id="search" name="search" type="search" value="{{ request('search') }}" class="h-11 w-full rounded-xl border border-gray-300 bg-white pl-11 pr-4 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" placeholder="Ketik nomor tanda terima atau nama pengirim...">
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label for="type" class="mb-2 block text-sm font-semibold text-gray-700">Jenis tanda terima</label>
                    <select id="type" name="type" class="h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                        <option value="fcl" @selected($type === 'fcl')>FCL</option>
                        <option value="lcl" @selected($type === 'lcl')>LCL</option>
                        <option value="ttsj" @selected($type === 'ttsj')>Tanpa Surat Jalan</option>
                    </select>
                </div>
                <div>
                    <label for="destination" class="mb-2 block text-sm font-semibold text-gray-700">Tujuan pengiriman</label>
                    <select id="destination" name="destination" class="h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                        <option value="">Semua tujuan</option>
                        <option value="jakarta" @selected(request('destination') === 'jakarta')>Jakarta</option>
                        <option value="batam" @selected(request('destination') === 'batam')>Batam</option>
                        <option value="tanjung-pinang" @selected(request('destination') === 'tanjung-pinang')>Tanjung Pinang</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="mb-2 block text-sm font-semibold text-gray-700">Status shipper</label>
                    <select id="status" name="status" class="h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                        <option value="">Semua status</option>
                        <option value="belum" @selected(request('status') === 'belum')>Belum dipilih</option>
                        <option value="sudah" @selected(request('status') === 'sudah')>Sudah dipilih</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex flex-col-reverse gap-2 border-t border-gray-100 bg-gray-50/80 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
            <a href="{{ route('approval-tanda-terima-2.index') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 hover:bg-gray-100">
                <i class="fas fa-rotate-left" aria-hidden="true"></i>Reset filter
            </a>
            <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <i class="fas fa-search" aria-hidden="true"></i>Tampilkan hasil
            </button>
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 px-5 py-4">
            <h2 class="font-semibold text-gray-900">Daftar Tanda Terima</h2>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">{{ $items->total() }} data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[1000px] w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th scope="col" class="px-5 py-3">Tanda Terima</th>
                        <th scope="col" class="px-5 py-3">Tanggal</th>
                        <th scope="col" class="px-5 py-3">Nomor Kontainer</th>
                        <th scope="col" class="px-5 py-3">Shipper Perincian</th>
                        <th scope="col" class="px-5 py-3">Shipper Manifest JB</th>
                        <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        @php
                            $number = $type === 'fcl' ? $item->no_surat_jalan : ($type === 'lcl' ? $item->nomor_tanda_terima : ($item->no_tanda_terima ?: $item->nomor_tanda_terima));
                            $sender = $type === 'lcl' ? $item->nama_pengirim : $item->pengirim;
                            $date = $type === 'fcl' ? $item->tanggal : $item->tanggal_tanda_terima;
                            $containerNumber = $type === 'lcl' ? $item->nomor_kontainer : $item->no_kontainer;
                            $goods = match ($type) {
                                'fcl' => collect($item->dimensi_items ?: $item->dimensi_details ?: $item->nama_barang ?: []),
                                'lcl' => $item->items,
                                'ttsj' => $item->dimensiItems,
                            };
                            if ($goods->isEmpty() && $type === 'ttsj' && ($item->nama_barang || $item->jenis_barang)) {
                                $goods = collect([[
                                    'nama_barang' => $item->nama_barang ?: $item->jenis_barang,
                                    'jumlah' => $item->jumlah_barang,
                                    'satuan' => $item->satuan_barang,
                                    'ukuran' => $item->ukuran,
                                    'meter_kubik' => $item->meter_kubik,
                                    'tonase' => $item->tonase,
                                    'keterangan_barang' => $item->keterangan_barang,
                                ]]);
                            }
                        @endphp
                        <tr class="hover:bg-gray-50/70">
                            <td class="px-5 py-4 font-semibold text-gray-900">{{ $number ?: 'Tanpa nomor' }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-gray-600">{{ $date ? \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') : '-' }}</td>
                            <td class="px-5 py-4 font-medium text-gray-700">{{ $containerNumber ?: '-' }}</td>
                            <td class="max-w-xs px-5 py-4 text-gray-700">{{ $sender ?: '-' }}</td>
                            <td class="px-5 py-4">
                                @if($item->shipperJb)
                                    <div class="font-medium text-gray-900">{{ $item->shipperJb->shipper }}</div>
                                    @if($item->shipperJb->consignee)<div class="mt-0.5 text-xs text-gray-500">Consignee: {{ $item->shipperJb->consignee }}</div>@endif
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700"><span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>Belum dipilih</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <button type="button" class="approval-goods-open inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    data-dialog-id="approval-goods-{{ $type }}-{{ $item->id }}">
                                    <i class="fas fa-box-open" aria-hidden="true"></i>Detail Barang
                                </button>
                                @can('approval-tanda-terima-2-approve')
                                    <button type="button" class="approval-shipper-open inline-flex items-center gap-2 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        data-update-url="{{ route('approval-tanda-terima-2.update', ['sourceType' => $type, 'id' => $item->id]) }}"
                                        data-number="{{ $number }}" data-sender="{{ $sender }}"
                                        data-shipper-id="{{ $item->shipper_jb_id }}"
                                        data-shipper="{{ $item->shipperJb?->shipper }}"
                                        data-shipper-details="{{ $item->shipperJb?->toJson() }}">
                                        <i class="fas fa-pen-to-square" aria-hidden="true"></i>{{ $item->shipper_jb_id ? 'Ubah Shipper' : 'Pilih Shipper' }}
                                    </button>
                                    @if($item->shipper_jb_id)
                                        <form method="POST" action="{{ route('approval-tanda-terima-2.destroy', ['sourceType' => $type, 'id' => $item->id]) }}" class="ml-2 inline-block" onsubmit="return confirm('Hapus shipper JB dari tanda terima ini dan manifest terkait?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500"><i class="fas fa-trash" aria-hidden="true"></i>Hapus Shipper</button>
                                        </form>
                                    @endif
                                @endcan
                                <dialog id="approval-goods-{{ $type }}-{{ $item->id }}" data-source-type="{{ $type }}" class="approval-goods-dialog whitespace-normal text-left shadow-2xl" aria-labelledby="approval-goods-title-{{ $type }}-{{ $item->id }}">
                                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 px-5 py-4">
                                        <div>
                                            <h2 id="approval-goods-title-{{ $type }}-{{ $item->id }}" class="text-lg font-semibold text-gray-900">Detail Barang</h2>
                                            <p class="mt-1 text-sm text-gray-500">Tanda terima: {{ $number ?: 'Tanpa nomor' }}</p>
                                        </div>
                                        <button type="button" class="approval-goods-close rounded-lg px-2 py-1 text-xl text-gray-500 hover:bg-gray-100" aria-label="Tutup">&times;</button>
                                    </div>
                                    @can('approval-tanda-terima-2-approve')
                                        <form method="POST" action="{{ route('approval-tanda-terima-2.update-goods', ['sourceType' => $type, 'id' => $item->id]) }}">
                                            @csrf
                                            @method('PUT')
                                    @endcan
                                    <div class="overflow-x-auto p-5">
                                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                                            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                                                <tr>
                                                    <th class="px-3 py-2">Nama Barang</th><th class="px-3 py-2">Jumlah</th><th class="px-3 py-2">Satuan</th>
                                                    <th class="px-3 py-2">Ukuran</th><th class="px-3 py-2">Panjang</th><th class="px-3 py-2">Lebar</th>
                                                    <th class="px-3 py-2">Tinggi</th><th class="px-3 py-2">Volume (m³)</th><th class="px-3 py-2">Tonase</th>
                                                    @if($type !== 'ttsj')<th class="px-3 py-2">Keterangan</th>@endif
                                                    @can('approval-tanda-terima-2-approve')<th class="px-3 py-2">Aksi</th>@endcan
                                                </tr>
                                            </thead>
                                            <tbody class="approval-goods-rows divide-y divide-gray-100">
                                                @foreach($goods as $good)
                                                    <tr>
                                                        @can('approval-tanda-terima-2-approve')
                                                            @foreach(['nama_barang' => 'text', 'jumlah' => 'number', 'satuan' => 'text', 'ukuran' => 'text', 'panjang' => 'number', 'lebar' => 'number', 'tinggi' => 'number', 'meter_kubik' => 'number', 'tonase' => 'number'] as $field => $inputType)
                                                                <td class="px-2 py-2">
                                                                    @if($field === 'nama_barang')
                                                                        @if($type !== 'fcl')
                                                                            <input type="hidden" name="goods[{{ $loop->parent->index }}][id]" value="{{ data_get($good, 'id') }}">
                                                                        @else
                                                                            <input type="hidden" name="goods[{{ $loop->parent->index }}][original_index]" value="{{ $loop->parent->index }}">
                                                                        @endif
                                                                    @endif
                                                                    <input type="{{ $inputType }}" name="goods[{{ $loop->parent->index }}][{{ $field }}]"
                                                                        value="{{ $field === 'nama_barang' && is_scalar($good) ? $good : data_get($good, $field) }}"
                                                                        @if($field === 'nama_barang') required @endif
                                                                        @if($inputType === 'number') min="0" step="{{ $field === 'jumlah' ? '1' : 'any' }}" @endif
                                                                        class="w-28 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $field === 'nama_barang' ? 'min-w-48' : '' }}">
                                                                </td>
                                                            @endforeach
                                                            @if($type !== 'ttsj')
                                                                <td class="px-2 py-2"><input type="text" name="goods[{{ $loop->index }}][keterangan_barang]" value="{{ data_get($good, 'keterangan_barang') }}" class="w-40 rounded-lg border-gray-300 text-sm"></td>
                                                            @endif
                                                            <td class="px-2 py-2"><button type="button" class="approval-goods-remove rounded-lg px-2 py-1 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button></td>
                                                        @else
                                                            @foreach(['nama_barang', 'jumlah', 'satuan', 'ukuran', 'panjang', 'lebar', 'tinggi', 'meter_kubik', 'tonase'] as $field)
                                                                <td class="px-3 py-3">{{ $field === 'nama_barang' && is_scalar($good) ? $good : (data_get($good, $field) ?? '-') }}</td>
                                                            @endforeach
                                                            @if($type !== 'ttsj')<td class="px-3 py-3">{{ data_get($good, 'keterangan_barang') ?: '-' }}</td>@endif
                                                        @endcan
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @if($type === 'ttsj')
                                            <div class="mt-4">
                                                <label class="mb-1 block text-sm font-medium text-gray-700">Keterangan Barang</label>
                                                @can('approval-tanda-terima-2-approve')
                                                    <textarea name="keterangan_barang" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $item->keterangan_barang }}</textarea>
                                                @else
                                                    <p class="text-sm text-gray-700">{{ $item->keterangan_barang ?: '-' }}</p>
                                                @endcan
                                            </div>
                                        @endif
                                        @if($goods->isEmpty())<p class="approval-goods-empty mt-3 text-sm text-gray-500">Detail barang belum tersedia.</p>@endif
                                        @can('approval-tanda-terima-2-approve')
                                            <button type="button" class="approval-goods-add mt-4 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">+ Tambah Barang</button>
                                        @endcan
                                    </div>
                                    <div class="flex justify-end gap-2 border-t border-gray-200 bg-gray-50 px-5 py-4">
                                        <button type="button" class="approval-goods-close rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Tutup</button>
                                        @can('approval-tanda-terima-2-approve')<button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Simpan Detail Barang</button>@endcan
                                    </div>
                                    @can('approval-tanda-terima-2-approve')
                                        </form>
                                    @endcan
                                </dialog>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-14 text-center text-gray-500"><i class="fas fa-inbox mb-3 block text-3xl text-gray-300" aria-hidden="true"></i>Tidak ada tanda terima yang sesuai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">{{ $items->links() }}</div>
</div>

@can('approval-tanda-terima-2-approve')
<dialog id="approval-shipper-dialog" aria-labelledby="approval-shipper-title" data-search-url="{{ route('approval-tanda-terima-2.shippers') }}">
    <div class="border-b border-gray-200 px-5 py-4 sm:px-6">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 id="approval-shipper-title" class="text-lg font-semibold text-gray-900">Pilih Shipper Manifest JB</h2>
                <p id="approval-shipper-context" class="mt-1 text-sm text-gray-500"></p>
            </div>
            <button type="button" class="approval-shipper-close rounded-lg px-2 py-1 text-xl text-gray-500 hover:bg-gray-100" aria-label="Tutup">&times;</button>
        </div>
    </div>
    <form id="approval-shipper-form" method="POST" action="">
        @csrf
        @method('PUT')
        <input type="hidden" id="approval-shipper-id" name="shipper_jb_id">
        <div class="space-y-4 px-5 py-5 sm:px-6">
            <div>
                <div class="mb-1 flex items-center justify-between gap-3">
                    <label for="approval-shipper-search" class="text-sm font-medium text-gray-700">Cari shipper</label>
                    @can('master-shipper-consignee-create')
                        <a href="{{ route('master.shipper-consignee.create') }}" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-indigo-700 hover:underline"><i class="fas fa-plus mr-1" aria-hidden="true"></i>Tambah di master</a>
                    @endcan
                </div>
                <input id="approval-shipper-search" type="search" autocomplete="off" placeholder="Ketik nama shipper..." class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" aria-controls="approval-shipper-options" aria-expanded="false">
                <div id="approval-shipper-options" class="mt-1 max-h-48 overflow-y-auto rounded-lg border border-gray-200 text-sm" aria-label="Hasil pencarian shipper" hidden></div>
                <p id="approval-shipper-selection" class="mt-2 text-xs text-gray-500" aria-live="polite">Pilih shipper dari hasil pencarian.</p>
            </div>
            @php
                $shipperPreviewGroups = [
                    'Informasi Umum' => [
                        'telepon' => 'Telepon', 'alamat_email' => 'Alamat Email',
                        'hs_code' => 'HS Code', 'commodity' => 'Commodity',
                        'document_ppftz_03' => 'Document PPFTZ-03', 'condition' => 'Condition',
                        'ip_bp_kawasan' => 'IP BP Kawasan', 'delivery_address' => 'Delivery Address',
                        'status' => 'Status',
                    ],
                    'Informasi Shipper' => [
                        'shipper' => 'Shipper', 'alamat_shipper' => 'Alamat Shipper',
                        'npwp_shipper' => 'NPWP Shipper', 'nitku_shipper' => 'NITKU Shipper',
                        'contact_person' => 'Contact Person',
                    ],
                    'Informasi Consignee' => [
                        'consignee' => 'Consignee', 'alamat_consignee' => 'Alamat Consignee',
                        'npwp_consignee' => 'NPWP Consignee',
                        'npwp_consignee_16_digit' => 'NPWP Consignee (16 Digit)',
                        'nitku_consignee' => 'NITKU Consignee',
                    ],
                    'Informasi Notify Party' => [
                        'notify_party_consignee' => 'Notify Party',
                        'alamat_notify_party_consignee' => 'Alamat Notify Party',
                        'npwp_notify_party_consignee' => 'NPWP Notify Party',
                    ],
                ];
            @endphp
            <div class="space-y-3" aria-label="Data dari master Shipper / Consignee">
                @foreach($shipperPreviewGroups as $groupTitle => $fields)
                    <section class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <h3 class="mb-3 text-sm font-semibold text-gray-800">{{ $groupTitle }}</h3>
                        <dl class="grid gap-3 text-sm sm:grid-cols-2">
                            @foreach($fields as $field => $label)
                                <div><dt class="text-xs font-medium text-gray-500">{{ $label }}</dt><dd data-shipper-field="{{ $field }}" class="mt-0.5 whitespace-pre-line break-words text-gray-800">-</dd></div>
                            @endforeach
                        </dl>
                    </section>
                @endforeach
            </div>
            <p class="text-xs text-gray-500">Periksa data master sebelum menyimpan. Data ini akan mengisi manifest voyage JB yang terkait.</p>
        </div>
        <div class="flex justify-end gap-2 border-t border-gray-200 bg-gray-50 px-5 py-4 sm:px-6">
            <button type="button" class="approval-shipper-close rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Batal</button>
            <button id="approval-shipper-save" type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50" disabled>Simpan Shipper</button>
        </div>
    </form>
</dialog>
@endcan
@endsection

@push('scripts')
<script src="{{ asset('js/approval-tanda-terima-2.js') }}?v={{ substr(hash_file('sha256', public_path('js/approval-tanda-terima-2.js')), 0, 12) }}" defer></script>
@endpush
