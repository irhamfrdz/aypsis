@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Riwayat Pranota PUML (Uang Makan & Lembur)</h1>
        </div>
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <a href="{{ route('pranota-puml.create') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-indigo-500">
                <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                    <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                </svg>
                <span class="hidden xs:block ml-2">Gabungkan / Generate PUML Baru</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-t border-b border-gray-200">
                    <tr>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Nomor PUML</div></th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Tanggal</div></th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-right">Total U.Makan</div></th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-right">Total Lembur</div></th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-right">Grand Total</div></th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-center">Status</div></th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-center">Aksi</div></th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($pranotas as $pranota)
                        <tr>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-medium text-sky-500">{{ $pranota->nomor_pranota }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div>{{ $pranota->tanggal_pranota->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-right">
                                <div class="font-medium text-gray-800">Rp {{ number_format($pranota->total_uang_makan, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-right">
                                <div class="font-medium text-gray-800">Rp {{ number_format($pranota->total_lembur, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-right">
                                <div class="font-medium text-green-600">Rp {{ number_format($pranota->grand_total, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-center">
                                <div class="inline-flex font-medium rounded-full text-center px-2.5 py-0.5 bg-yellow-100 text-yellow-600">{{ ucfirst($pranota->status) }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-center w-px">
                                <div class="space-x-1">
                                    <a href="{{ route('pranota-puml.show', $pranota->id) }}" class="inline-flex items-center justify-center p-2 text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors" title="Detail">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-center text-gray-500">
                                Belum ada data PUML.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
