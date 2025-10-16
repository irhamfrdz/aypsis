@extends('layouts.app')

@section('title', 'Detail Pranota Surat Jalan')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg">
        <!-- Header -->
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h1 class="text-xl font-semibold text-gray-900">Detail Pranota Surat Jalan</h1>
                </div>
                <div class="flex space-x-3">
                    @can('pranota-surat-jalan-view')
                        <a href="{{ route('pranota-surat-jalan.print', $pranotaSuratJalan) }}"
                           class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                           target="_blank">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print
                        </a>
                    @endcan

                    @can('pranota-surat-jalan-update')
                        @if($pranotaSuratJalan->status == 'draft')
                            <a href="{{ route('pranota-surat-jalan.edit', $pranotaSuratJalan) }}"
                               class="inline-flex items-center px-3 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </a>
                        @endif
                    @endcan

                    <a href="{{ route('pranota-surat-jalan.index') }}"
                       class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="px-6 py-6">
            <!-- Pranota Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="space-y-4">
                    <div class="flex justify-between py-2">
                        <span class="font-medium text-gray-700">Nomor Pranota:</span>
                        <span class="text-gray-900">{{ $pranotaSuratJalan->nomor_pranota }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-t border-gray-100">
                        <span class="font-medium text-gray-700">Tanggal:</span>
                        <span class="text-gray-900">{{ $pranotaSuratJalan->tanggal_formatted }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-t border-gray-100">
                        <span class="font-medium text-gray-700">Status:</span>
                        <span>
                            @if($pranotaSuratJalan->status == 'draft')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Draft</span>
                            @elseif($pranotaSuratJalan->status == 'terkirim')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Terkirim</span>
                            @elseif($pranotaSuratJalan->status == 'dibayar')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Dibayar</span>
                            @endif
                        </span>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between py-2">
                        <span class="font-medium text-gray-700">Total Tarif:</span>
                        <span class="text-lg font-bold text-green-600">
                            {{ $pranotaSuratJalan->total_tarif_formatted }}
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-t border-gray-100">
                        <span class="font-medium text-gray-700">Jumlah Surat Jalan:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $pranotaSuratJalan->suratJalans->count() }} Surat Jalan
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-t border-gray-100">
                        <span class="font-medium text-gray-700">Dibuat oleh:</span>
                        <span class="text-gray-900">{{ $pranotaSuratJalan->user->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            @if($pranotaSuratJalan->keterangan)
                <div class="mb-8">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-2 mb-3">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="font-medium text-gray-900">Keterangan</h3>
                        </div>
                        <p class="text-gray-700">{{ $pranotaSuratJalan->keterangan }}</p>
                    </div>
                </div>
            @endif

            <!-- Surat Jalan List -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200 rounded-t-lg">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="font-medium text-gray-900">Daftar Surat Jalan</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nomor Surat Jalan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Pengirim</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Tujuan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Jenis Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Tarif</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status Approval</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pranotaSuratJalan->suratJalans as $index => $suratJalan)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-medium text-gray-900">{{ $suratJalan->nomor_surat_jalan }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $suratJalan->tanggal_surat_jalan_formatted }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $suratJalan->pengirim_name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $suratJalan->tujuan_kirim_name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $suratJalan->jenis_barang_name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-medium text-green-600">
                                                {{ $suratJalan->tarif ? 'Rp ' . number_format($suratJalan->tarif, 0, ',', '.') : 'Rp 0' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($suratJalan->isFullyApproved())
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Fully Approved</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending Approval</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <th colspan="6" class="px-6 py-3 text-right text-sm font-medium text-gray-900">Total Tarif:</th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-green-600">{{ $pranotaSuratJalan->total_tarif_formatted }}</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Status Actions -->
            @if($pranotaSuratJalan->status == 'draft')
                <div class="mt-8 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="bg-gray-50 px-6 py-3 border-b border-gray-200 rounded-t-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <h3 class="font-medium text-gray-900">Aksi Status</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-blue-700">
                                        Pranota masih dalam status <span class="font-medium">Draft</span>. Anda dapat mengubah status atau mengedit pranota.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex space-x-3">
                            @can('pranota-surat-jalan-update')
                                <form action="{{ route('pranota-surat-jalan.update', $pranotaSuratJalan) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Ubah status menjadi Terkirim?')">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="send">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        Kirim Pranota
                                    </button>
                                </form>
                            @endcan

                            @can('pranota-surat-jalan-delete')
                                <form action="{{ route('pranota-surat-jalan.destroy', $pranotaSuratJalan) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus pranota ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Hapus Pranota
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            @elseif($pranotaSuratJalan->status == 'terkirim')
                <div class="mt-8 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="bg-gray-50 px-6 py-3 border-b border-gray-200 rounded-t-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <h3 class="font-medium text-gray-900">Aksi Status</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-yellow-700">
                                        Pranota sudah <span class="font-medium">Terkirim</span> dan menunggu pembayaran.
                                    </p>
                                </div>
                            </div>
                        </div>

                        @can('pranota-surat-jalan-update')
                            <form action="{{ route('pranota-surat-jalan.update', $pranotaSuratJalan) }}"
                                  method="POST"
                                  class="inline-block"
                                  onsubmit="return confirm('Ubah status menjadi Dibayar?')">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="pay">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Tandai Dibayar
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            @elseif($pranotaSuratJalan->status == 'dibayar')
                <div class="mt-8 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="bg-gray-50 px-6 py-3 border-b border-gray-200 rounded-t-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="font-medium text-gray-900">Status Pranota</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-green-700">
                                        Pranota sudah <span class="font-medium">Dibayar</span> dan selesai.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
