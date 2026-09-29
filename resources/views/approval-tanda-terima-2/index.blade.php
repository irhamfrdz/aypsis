@extends('layouts.app')

@section('title', 'Approval Tanda Terima 2')
@section('page_title', 'Approval Tanda Terima 2')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-5">
        <h1 class="text-2xl font-semibold text-gray-900">Approval Tanda Terima 2</h1>
        <p class="mt-1 text-sm text-gray-600">Pilih shipper dari Master Shipper / Consignee. ID tersimpan pada tanda terima dan digunakan oleh manifest voyage JB, terpisah dari shipper manifest yang sudah ada.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <form method="GET" action="{{ route('approval-tanda-terima-2.index') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-5 flex flex-wrap items-end gap-3">
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Tanda Terima</label>
            <select id="type" name="type" class="rounded-lg border-gray-300 text-sm">
                <option value="fcl" @selected($type === 'fcl')>FCL</option>
                <option value="lcl" @selected($type === 'lcl')>LCL</option>
                <option value="ttsj" @selected($type === 'ttsj')>Tanpa Surat Jalan</option>
            </select>
        </div>
        <div class="flex-1 min-w-60">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari nomor atau pengirim</label>
            <input id="search" name="search" value="{{ request('search') }}" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Ketik nomor tanda terima...">
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status shipper</label>
            <select id="status" name="status" class="rounded-lg border-gray-300 text-sm">
                <option value="">Semua</option>
                <option value="belum" @selected(request('status') === 'belum')>Belum dipilih</option>
                <option value="sudah" @selected(request('status') === 'sudah')>Sudah dipilih</option>
            </select>
        </div>
        <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Filter</button>
        <a href="{{ route('approval-tanda-terima-2.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700">Reset</a>
    </form>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">No. Tanda Terima</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Pengirim Saat Ini</th>
                    <th class="px-4 py-3">Shipper untuk Manifest JB</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($items as $item)
                    @php
                        $number = $type === 'fcl' ? $item->no_surat_jalan : ($type === 'lcl' ? $item->nomor_tanda_terima : ($item->no_tanda_terima ?: $item->nomor_tanda_terima));
                        $sender = $type === 'lcl' ? $item->nama_pengirim : $item->pengirim;
                        $date = $type === 'fcl' ? $item->tanggal : $item->tanggal_tanda_terima;
                    @endphp
                    <tr>
                        <td class="px-4 py-3">{{ $number ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $date ? \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-3">{{ $sender ?: '-' }}</td>
                        <td class="px-4 py-3 min-w-72">
                            @can('approval-tanda-terima-approve')
                                <form method="POST" action="{{ route('approval-tanda-terima-2.update', ['sourceType' => $type, 'id' => $item->id]) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="shipper_jb_id" required class="min-w-0 flex-1 rounded-lg border-gray-300 text-sm">
                                        <option value="">-- Pilih shipper --</option>
                                        @foreach($shippers as $shipper)
                                            <option value="{{ $shipper->id }}" @selected($item->shipper_jb_id == $shipper->id)>{{ $shipper->shipper }}{{ $shipper->consignee ? ' / '.$shipper->consignee : '' }}</option>
                                        @endforeach
                                    </select>
                                    <button class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-medium text-white hover:bg-indigo-700">Simpan</button>
                                </form>
                            @else
                                {{ $item->shipperJb?->shipper ?: 'Belum dipilih' }}
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-10 text-center text-gray-500">Tidak ada tanda terima yang sesuai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
</div>
@endsection
