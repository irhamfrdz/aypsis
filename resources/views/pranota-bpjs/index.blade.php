@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Daftar Pranota BPJS</h1>
        </div>
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            @can('pranota-bpjs-create')
            <a href="{{ route('pranota-bpjs.create') }}" class="btn bg-teal-600 hover:bg-teal-700 text-white">
                <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                    <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                </svg>
                <span class="hidden xs:block ml-2">Buat Pranota BPJS</span>
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-t border-b border-gray-200">
                    <tr>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Nomor Pranota</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Tanggal</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-center">Periode</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-center">Jml Karyawan</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-right">Total Nominal</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-center">Status</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-center">Aksi</div>
                        </th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($pranotas as $pranota)
                        <tr>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="text-left text-teal-600 font-medium">{{ $pranota->nomor_pranota }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="text-left">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="text-center font-medium">{{ str_pad($pranota->periode_bulan, 2, '0', STR_PAD_LEFT) }} / {{ $pranota->periode_tahun }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="text-center">{{ $pranota->total_karyawan }} Org</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="text-right text-slate-800 font-semibold">Rp {{ number_format($pranota->grand_total, 2, ',', '.') }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $pranota->status == 'draft' ? 'bg-amber-100 text-amber-600' : 'bg-green-100 text-green-600' }}">
                                        {{ ucfirst($pranota->status) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-2">
                                    @can('pranota-bpjs-view')
                                        <a href="{{ route('pranota-bpjs.show', $pranota->id) }}" class="btn btn-sm bg-blue-500 hover:bg-blue-600 text-white rounded px-2 py-1 text-xs">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    @endcan
                                    
                                    @can('pranota-bpjs-update')
                                        @if($pranota->status == 'draft')
                                            <a href="{{ route('pranota-bpjs.edit', $pranota->id) }}" class="btn btn-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded px-2 py-1 text-xs">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        @endif
                                    @endcan
                                    
                                    @can('pranota-bpjs-delete')
                                        @if($pranota->status == 'draft')
                                            <form action="{{ route('pranota-bpjs.destroy', $pranota->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pranota ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm bg-red-500 hover:bg-red-600 text-white rounded px-2 py-1 text-xs">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-2 first:pl-5 last:pr-5 py-3 text-center text-gray-500">
                                Belum ada data Pranota BPJS.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4">
            {{ $pranotas->links() }}
        </div>
    </div>
</div>
@endsection
