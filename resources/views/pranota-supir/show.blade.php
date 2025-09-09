@extends('layouts.app')

@section('title', 'Detail Pranota Supir')
@section('page_title', 'Detail Pranota Supir')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <label for="tanggal_kas" class="block text-sm font-medium text-gray-700">Tanggal Kas</label>
            <input type="text" name="tanggal_kas" id="tanggal_kas" value="{{ now()->format('d/M/Y') }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5" readonly>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Nomor Pranota</label>
            <p class="mt-1 text-sm text-gray-900">{{ $pranotaSupir->nomor_pranota }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Tanggal Pranota</label>
            <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($pranotaSupir->tanggal_pranota)->format('d/M/Y') }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Total Biaya Memo</label>
            <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($pranotaSupir->total_biaya_memo, 2, ',', '.') }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Adjustment</label>
            <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($pranotaSupir->adjustment ?? 0, 2, ',', '.') }}</p>
            @if($pranotaSupir->alasan_adjustment)
            <p class="mt-1 text-sm text-gray-500">Alasan: {{ $pranotaSupir->alasan_adjustment }}</p>
            @endif
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Total Biaya Pranota</label>
            <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($pranotaSupir->total_biaya_pranota, 2, ',', '.') }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Status Pembayaran</label>
            <p class="mt-1 text-sm text-gray-900">
                @if ($pranotaSupir->status_pembayaran == 'Lunas')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Lunas
                    </span>
                @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        Belum Lunas
                    </span>
                @endif
            </p>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Supir Terkait</label>
            <div class="mt-1">
                @if ($pranotaSupir->permohonans->isNotEmpty())
                    @php
                        $supirs = $pranotaSupir->permohonans->pluck('supir')->filter()->unique('id');
                    @endphp
                    @if ($supirs->isNotEmpty())
                        <div class="flex flex-wrap gap-2">
                            @foreach ($supirs as $supir)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $supir->nama_karyawan ?? $supir->nama_panggilan }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">-</p>
                    @endif
                @else
                    <p class="text-sm text-gray-500">-</p>
                @endif
            </div>
        </div>
    </div>

    @if($pranotaSupir->catatan)
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700">Catatan</label>
        <p class="mt-1 text-sm text-gray-900">{{ $pranotaSupir->catatan }}</p>
    </div>
    @endif

    <h3 class="text-lg font-semibold text-gray-800 mb-4">Memo Permohonan Terkait</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Memo</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Memo</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Krani</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($pranotaSupir->permohonans as $permohonan)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $permohonan->nomor_memo }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($permohonan->tanggal_memo)->format('d/M/Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $permohonan->kegiatan }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if ($permohonan->supir)
                            <div class="text-sm text-gray-900">{{ $permohonan->supir->nama_karyawan ?? $permohonan->supir->nama_panggilan }}</div>
                        @else
                            <div class="text-sm text-gray-500">-</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if ($permohonan->krani)
                            <div class="text-sm text-gray-900">{{ $permohonan->krani->nama_karyawan ?? $permohonan->krani->nama_panggilan }}</div>
                        @else
                            <div class="text-sm text-gray-500">-</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="text-sm text-gray-900">Rp {{ number_format($permohonan->total_harga_setelah_adj, 2, ',', '.') }}</div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                        Tidak ada memo permohonan terkait.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pranotaSupir->permohonans->isNotEmpty())
    <div class="mt-4">
        <h4 class="text-md font-semibold text-gray-800 mb-2">Kontainer Terkait</h4>
        @foreach($pranotaSupir->permohonans as $permohonan)
            @if($permohonan->kontainers->isNotEmpty())
            <div class="mb-3">
                <p class="text-sm font-medium text-gray-700">Memo: {{ $permohonan->nomor_memo }}</p>
                <div class="ml-4">
                    @foreach($permohonan->kontainers as $kontainer)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                        {{ $kontainer->nomor_kontainer }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    </div>
    @endif

    <div class="mt-6 flex justify-end">
        <a href="{{ route('pranota-supir.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Kembali
        </a>
        @if ($pranotaSupir->status_pembayaran == 'Belum Lunas')
        <a href="{{ route('pembayaran-pranota-supir.create') }}?pranota_ids[]={{ $pranotaSupir->id }}" class="ml-2 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            Buat Pembayaran
        </a>
        @endif
    </div>
</div>
@endsection
