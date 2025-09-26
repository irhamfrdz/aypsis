@extends('layouts.app')

@section('title', 'Pembayaran Pranota Perbaikan Kontainer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-wrap -mx-4">
        <div class="w-full px-4">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">Pembayaran Pranota Perbaikan Kontainer</h3>
                    <div class="flex space-x-2">
                        @can('pembayaran-pranota-perbaikan-kontainer.create')
                        <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm inline-flex items-center">
                            <i class="fas fa-plus mr-2"></i> Tambah Pembayaran
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full border-collapse border border-gray-300" id="pembayaranTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pranota</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pembayaran</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode Pembayaran</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pembayaranPranotaPerbaikanKontainers as $pembayaran)
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                        <strong>{{ $pembayaran->pranotaPerbaikanKontainer->perbaikanKontainers->first()->kontainer->nomor_kontainer ?? 'N/A' }}</strong><br>
                                        <small class="text-gray-500">{{ Str::limit($pembayaran->pranotaPerbaikanKontainer->deskripsi_pekerjaan, 50) }}</small>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $pembayaran->tanggal_pembayaran->format('d/m/Y') }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($pembayaran->nominal_pembayaran, 0, ',', '.') }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                            {{ ucfirst($pembayaran->metode_pembayaran) }}
                                        </span>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $pembayaran->status_pembayaran == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $pembayaran->status_pembayaran == 'paid' ? 'Lunas' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-1">
                                            @can('pembayaran-pranota-perbaikan-kontainer.show')
                                            <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.show', $pembayaran) }}" class="bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded text-xs inline-flex items-center">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('pembayaran-pranota-perbaikan-kontainer.print')
                                            <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.print', $pembayaran) }}" class="bg-green-500 hover:bg-green-700 text-white py-1 px-2 rounded text-xs inline-flex items-center" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @endcan
                                            @can('pembayaran-pranota-perbaikan-kontainer.edit')
                                            <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.edit', $pembayaran) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-2 rounded text-xs inline-flex items-center">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('pembayaran-pranota-perbaikan-kontainer.delete')
                                            <form action="{{ route('pembayaran-pranota-perbaikan-kontainer.destroy', $pembayaran) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded text-xs inline-flex items-center" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection
