@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Detail Pembayaran Pranota Perbaikan Kontainer</h1>
                <div class="flex space-x-3">
                    @can('pembayaran-pranota-perbaikan-kontainer.print')
                    <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.print', $pembayaran) }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-print mr-2"></i>
                        Print
                    </a>
                    @endcan
                    @can('pembayaran-pranota-perbaikan-kontainer.edit')
                    <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.edit', $pembayaran) }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </a>
                    @endcan
                    <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="mx-6 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-6 mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="p-6">
            <!-- Payment Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Basic Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pembayaran</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nomor Pembayaran</dt>
                            <dd class="text-sm text-gray-900 font-semibold">{{ $pembayaran->nomor_pembayaran ?? $pembayaran->nomor_invoice ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Pembayaran</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaran->tanggal_kas ? \Carbon\Carbon::parse($pembayaran->tanggal_kas)->format('d F Y') : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Bayar</dt>
                            <dd class="text-sm text-gray-900 font-semibold">Rp {{ number_format($pembayaran->nominal_pembayaran, 0, ',', '.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jenis Transaksi</dt>
                            <dd class="text-sm text-gray-900">{{ ucfirst($pembayaran->jenis_transaksi ?? 'N/A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status Pembayaran</dt>
                            <dd class="text-sm">
                                @if($pembayaran->status == 'completed')
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">Lunas</span>
                                @elseif($pembayaran->status == 'pending')
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-yellow-100 text-yellow-800">Pending</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800">{{ ucfirst($pembayaran->status ?? 'Unknown') }}</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Additional Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Tambahan</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Bank</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaran->bank ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Keterangan</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaran->keterangan ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dibuat Pada</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaran->created_at ? \Carbon\Carbon::parse($pembayaran->created_at)->format('d F Y H:i') : '-' }}</dd>
                        </div>
                        @if($pembayaran->updated_at && $pembayaran->updated_at != $pembayaran->created_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Diupdate Pada</dt>
                            <dd class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($pembayaran->updated_at)->format('d F Y H:i') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Pranota Details -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Detail Pranota Perbaikan Kontainer</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pranota</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pranota</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teknisi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi Pekerjaan</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pembayaran->pranotaPerbaikanKontainers as $pranota)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    @if($pranota->nomor_pranota)
                                        <a href="{{ route('pranota-perbaikan-kontainer.show', $pranota) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                            {{ $pranota->nomor_pranota }}
                                        </a>
                                    @else
                                        Belum ada
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $pranota->tanggal_pranota ? \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d F Y') : '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $pranota->nama_teknisi ?? '-' }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900 max-w-xs">
                                    <div class="truncate" title="{{ $pranota->deskripsi_pekerjaan ?? '' }}">
                                        {{ Str::limit($pranota->deskripsi_pekerjaan ?? '', 50) }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">
                                    Rp {{ number_format($pranota->pivot->amount ?? $pranota->total_biaya ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    @if($pranota->status == 'approved')
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">Approved</span>
                                    @elseif($pranota->status == 'belum_dibayar')
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800">Belum Dibayar</span>
                                    @elseif($pranota->status == 'pending')
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-yellow-100 text-yellow-800">Pending</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800">{{ ucfirst($pranota->status ?? 'Unknown') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex justify-end space-x-3">
                @can('pembayaran-pranota-perbaikan-kontainer.print')
                <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.print', $pembayaran) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-print mr-2"></i>
                    Print Bukti Pembayaran
                </a>
                @endcan
                @can('pembayaran-pranota-perbaikan-kontainer.edit')
                <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.edit', $pembayaran) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Pembayaran
                </a>
                @endcan
                <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
