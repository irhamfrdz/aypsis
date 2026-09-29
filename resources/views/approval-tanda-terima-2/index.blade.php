@extends('layouts.app')

@section('title', 'Approval Tanda Terima 2')
@section('page_title', 'Approval Tanda Terima 2')

@push('styles')
<style>
    #approval-shipper-dialog { width: min(680px, calc(100vw - 24px)); max-height: calc(100vh - 32px); border: 0; border-radius: 16px; padding: 0; overflow-y: auto; }
    #approval-shipper-dialog::backdrop { background: rgb(15 23 42 / 60%); }
    #approval-shipper-dialog [hidden] { display: none !important; }
    .approval-shipper-option { display: block; width: 100%; padding: 10px 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
    .approval-shipper-option:hover, .approval-shipper-option:focus { background: #eef2ff; outline: 2px solid #6366f1; outline-offset: -2px; }
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

    <form method="GET" action="{{ route('approval-tanda-terima-2.index') }}" class="mb-5 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center gap-2 text-gray-800"><i class="fas fa-filter text-indigo-600" aria-hidden="true"></i><h2 class="font-semibold">Cari Tanda Terima</h2></div>
        <div class="grid gap-4 md:grid-cols-12 md:items-end">
            <div class="md:col-span-3">
                <label for="type" class="mb-1 block text-sm font-medium text-gray-700">Jenis tanda terima</label>
                <select id="type" name="type" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="fcl" @selected($type === 'fcl')>FCL</option>
                    <option value="lcl" @selected($type === 'lcl')>LCL</option>
                    <option value="ttsj" @selected($type === 'ttsj')>Tanpa Surat Jalan</option>
                </select>
            </div>
            <div class="md:col-span-4">
                <label for="search" class="mb-1 block text-sm font-medium text-gray-700">Nomor tanda terima atau pengirim</label>
                <input id="search" name="search" type="search" value="{{ request('search') }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Cari nomor atau nama pengirim...">
            </div>
            <div class="md:col-span-3">
                <label for="destination" class="mb-1 block text-sm font-medium text-gray-700">Tujuan pengiriman</label>
                <select id="destination" name="destination" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua tujuan</option>
                    <option value="jakarta" @selected(request('destination') === 'jakarta')>Jakarta</option>
                    <option value="batam" @selected(request('destination') === 'batam')>Batam</option>
                    <option value="tanjung-pinang" @selected(request('destination') === 'tanjung-pinang')>Tanjung Pinang</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status shipper</label>
                <select id="status" name="status" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="belum" @selected(request('status') === 'belum')>Belum dipilih</option>
                    <option value="sudah" @selected(request('status') === 'sudah')>Sudah dipilih</option>
                </select>
            </div>
            <div class="flex gap-2 md:col-span-12 md:justify-end">
                <button type="submit" class="flex-1 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 md:flex-none">Cari</button>
                <a href="{{ route('approval-tanda-terima-2.index') }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50" title="Reset filter" aria-label="Reset filter"><i class="fas fa-rotate-left" aria-hidden="true"></i></a>
            </div>
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
                        <th scope="col" class="px-5 py-3">Pengirim pada Tanda Terima</th>
                        <th scope="col" class="px-5 py-3">Shipper Manifest JB</th>
                        @can('approval-tanda-terima-approve')<th scope="col" class="px-5 py-3 text-right">Aksi</th>@endcan
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        @php
                            $number = $type === 'fcl' ? $item->no_surat_jalan : ($type === 'lcl' ? $item->nomor_tanda_terima : ($item->no_tanda_terima ?: $item->nomor_tanda_terima));
                            $sender = $type === 'lcl' ? $item->nama_pengirim : $item->pengirim;
                            $date = $type === 'fcl' ? $item->tanggal : $item->tanggal_tanda_terima;
                            $containerNumber = $type === 'lcl' ? $item->nomor_kontainer : $item->no_kontainer;
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
                            @can('approval-tanda-terima-approve')
                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <button type="button" class="approval-shipper-open inline-flex items-center gap-2 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        data-update-url="{{ route('approval-tanda-terima-2.update', ['sourceType' => $type, 'id' => $item->id]) }}"
                                        data-number="{{ $number }}" data-sender="{{ $sender }}"
                                        data-shipper-id="{{ $item->shipper_jb_id }}"
                                        data-shipper="{{ $item->shipperJb?->shipper }}"
                                        data-alamat="{{ $item->shipperJb?->alamat_shipper }}"
                                        data-consignee="{{ $item->shipperJb?->consignee }}"
                                        data-notify="{{ $item->shipperJb?->notify_party_consignee }}"
                                        data-notify-address="{{ $item->shipperJb?->alamat_notify_party_consignee }}">
                                        <i class="fas fa-pen-to-square" aria-hidden="true"></i>{{ $item->shipper_jb_id ? 'Ubah Shipper' : 'Pilih Shipper' }}
                                    </button>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr><td colspan="{{ auth()->user()->can('approval-tanda-terima-approve') ? 6 : 5 }}" class="px-5 py-14 text-center text-gray-500"><i class="fas fa-inbox mb-3 block text-3xl text-gray-300" aria-hidden="true"></i>Tidak ada tanda terima yang sesuai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">{{ $items->links() }}</div>
</div>

@can('approval-tanda-terima-approve')
<dialog id="approval-shipper-dialog" aria-labelledby="approval-shipper-title" data-search-url="{{ url('/api/manifests/search-shippers') }}">
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
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                <h3 class="mb-3 text-sm font-semibold text-gray-800">Data dari master</h3>
                <dl class="grid gap-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-xs font-medium text-gray-500">Alamat pengirim</dt><dd id="approval-shipper-alamat" class="mt-0.5 whitespace-pre-line text-gray-800">-</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500">Consignee</dt><dd id="approval-shipper-consignee" class="mt-0.5 text-gray-800">-</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500">Notify party</dt><dd id="approval-shipper-notify" class="mt-0.5 text-gray-800">-</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500">Alamat notify party</dt><dd id="approval-shipper-notify-address" class="mt-0.5 whitespace-pre-line text-gray-800">-</dd></div>
                </dl>
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
