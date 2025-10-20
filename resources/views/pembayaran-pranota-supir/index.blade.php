@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-8">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="flex justify-between items-center mb-8 border-b pb-4">
            <h2 class="text-2xl font-bold text-gray-800">
                Daftar Pembayaran Pranota Supir
            </h2>
            @can('pembayaran-pranota-supir-create')
            <a href="{{ route('pembayaran-pranota-supir.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                </svg>
                Tambah Pembayaran
            </a>
            @endcan
        </div>
        <div class="overflow-x-auto rounded-xl border shadow-sm">
            <table class="min-w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-xs">
                        <tr>
                            <th class="px-4 py-3">Nomor Pembayaran</th>
                            <th class="px-4 py-3">Tanggal Pembayaran</th>
                            <th class="px-4 py-3">Nomor Pranota</th>
                            <th class="px-4 py-3">Total Pembayaran</th>
                            <th class="px-4 py-3">Bank</th>
                            <th class="px-4 py-3">Jenis Transaksi</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white text-[10px]">
                    @forelse ($pembayarans as $pembayaran)
                            <tr class="hover:bg-indigo-50 transition-colors">
                                <td class="px-4 py-3">{{ $pembayaran->nomor_pembayaran ?? '-' }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/M/Y') }}</td>
                                <td class="px-4 py-3">
                                    @foreach ($pembayaran->pranotas as $pranota)
                                        <div>{{ $pranota->nomor_pranota }}</div>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3">Rp {{ number_format($pembayaran->total_pembayaran, 2, ',', '.') }}</td>
                                <td class="px-4 py-3">{{ $pembayaran->bank ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $pembayaran->jenis_transaksi ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $pembayaran->keterangan ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('pembayaran-pranota-supir.print', $pembayaran) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded text-sm">Cetak</a>
                                    @can('audit-log-view')
                                        <span class="text-gray-300 mx-1">|</span>
                                        <button type="button"
                                                class="audit-log-btn inline-flex items-center px-3 py-1.5 bg-purple-600 text-white rounded text-sm hover:bg-purple-700"
                                                data-model-type="App\Models\PembayaranPranotaSupir"
                                                data-model-id="{{ $pembayaran->id }}"
                                                data-item-name="{{ $pembayaran->nomor_pembayaran ?? 'Pembayaran Supir' }}"
                                                title="Lihat Riwayat">
                                            Riwayat
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-4 text-center text-gray-500">Belum ada pembayaran yang dilakukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Audit Log Modal -->
@include('components.audit-log-modal')

@endsection
