@extends('layouts.app')

@section('title', 'Manifest Sugeng')
@section('page_title', 'Manifest Sugeng')

@section('content')
<div class="mx-auto max-w-7xl space-y-5 px-4 py-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Manifest Sugeng</h1>
        <p class="text-sm text-gray-600">Tanda terima yang dikirim dari Approval Tanda Terima 2.</p>
    </div>
    @if(session('success'))<div class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>@endif
    <form method="GET" class="flex flex-wrap gap-3 rounded-xl border border-gray-200 bg-white p-4">
        <input name="search" value="{{ request('search') }}" placeholder="Cari nomor, kontainer, atau pengirim" class="min-w-64 flex-1 rounded-lg border-gray-300 text-sm">
        <select name="type" class="rounded-lg border-gray-300 text-sm">
            <option value="">Semua jenis</option>
            @foreach(['fcl' => 'FCL', 'lcl' => 'LCL', 'ttsj' => 'TTSJ'] as $value => $label)
                <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Cari</button>
    </form>
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">Tanda Terima</th><th class="px-4 py-3">Jenis</th><th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Kontainer</th><th class="px-4 py-3">Pengirim</th><th class="px-4 py-3">Shipper JB</th>
                    <th class="px-4 py-3">Dikirim</th><th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($items as $item)
                    <tr>
                        <td class="px-4 py-3 font-semibold">{{ $item->nomor_tanda_terima ?: '-' }}</td>
                        <td class="px-4 py-3 uppercase">{{ $item->source_type }}</td>
                        <td class="px-4 py-3">{{ $item->tanggal_tanda_terima?->format('d/m/Y') ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $item->nomor_kontainer ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $item->pengirim ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $item->shipperJb?->shipper ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $item->dikirim_pada?->format('d/m/Y H:i') ?: '-' }}</td>
                        <td class="px-4 py-3"><a href="{{ route('manifest-sugeng.show', $item) }}" class="font-semibold text-indigo-600 hover:underline">Lihat</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-10 text-center text-gray-500">Belum ada data Manifest Sugeng.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $items->links() }}
</div>
@endsection
