@extends('layouts.app')

@section('title', 'Detail Asuransi Tanda Terima')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center">
                    <a href="{{ route('asuransi-tanda-terima.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Detail Asuransi</h1>
                        <p class="text-gray-600 mt-1">Polis: {{ $asuransiTandaTerima->nomor_polis }}</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    @can('asuransi-tanda-terima-update')
                    <a href="{{ route('asuransi-tanda-terima.edit', $asuransiTandaTerima->id) }}"
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                        Edit Data
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Content -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Data Polis -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Informasi Polis</h3>
                    <div class="space-y-4">
                        <div>
                            <span class="block text-sm text-gray-500 uppercase font-medium">Vendor Asuransi</span>
                            <span class="text-gray-900 font-medium">{{ $asuransiTandaTerima->vendorAsuransi->nama_asuransi }}</span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-500 uppercase font-medium">Nomor Polis</span>
                            <span class="text-gray-900 font-medium">{{ $asuransiTandaTerima->nomor_polis }}</span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-500 uppercase font-medium">Tanggal Polis</span>
                            <span class="text-gray-900 font-medium">{{ $asuransiTandaTerima->tanggal_polis ? $asuransiTandaTerima->tanggal_polis->format('d F Y') : '-' }}</span>
                        </div>
                        <div class="pt-2 mt-2 border-t border-gray-100">
                            <span class="block text-xs text-gray-400 uppercase font-bold mb-1">Informasi Kapal</span>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="block text-sm text-gray-500">Nomor Urut:</span>
                                    <span class="text-gray-900 font-medium">{{ $asuransiTandaTerima->nomor_urut ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="block text-sm text-gray-500">Voyage:</span>
                                    <span class="text-gray-900 font-medium">{{ $asuransiTandaTerima->nomor_voyage ?? '-' }}</span>
                                </div>
                                <div class="col-span-2">
                                    <span class="block text-sm text-gray-500">Nama Kapal:</span>
                                    <span class="text-gray-900 font-medium">{{ $asuransiTandaTerima->nama_kapal ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keuangan -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Nilai & Biaya</h3>
                    <div class="space-y-4 font-mono text-gray-900">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-700 font-sans font-medium">NILAI BARANG</span>
                            <span class="text-lg font-bold">Rp {{ number_format($asuransiTandaTerima->nilai_pertanggungan > 0 ? $asuransiTandaTerima->nilai_pertanggungan : $asuransiTandaTerima->premi, 0, ',', '.') }}</span>
                        </div>
                        @if($asuransiTandaTerima->nilai_pertanggungan > 0)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-700 font-sans font-medium">PREMI ASURANSI</span>
                            <span class="text-md font-semibold font-sans text-gray-800">Rp {{ number_format($asuransiTandaTerima->premi, 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Tanda Terima -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Informasi Sumber Data</h3>
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <label class="block text-xs text-gray-500 uppercase font-bold mb-2">Terhubung ke {{ $asuransiTandaTerima->source_type_name }}</label>
                        <div class="text-lg text-gray-900">
                            {{ $asuransiTandaTerima->source_number }}
                        </div>
                        @if($asuransiTandaTerima->source)
                            <div class="mt-4 grid grid-cols-2 gap-4 text-sm mt-4 pt-4 border-t border-gray-200">
                                <div>
                                    <span class="block text-gray-500">Penerima:</span>
                                    <span class="font-medium">
                                        {{ $asuransiTandaTerima->tanda_terima_lcl_id ? $asuransiTandaTerima->source->nama_penerima : $asuransiTandaTerima->source->penerima }}
                                    </span>
                                </div>
                                <div>
                                    <span class="block text-gray-500">Supir / No Plat:</span>
                                    <span class="font-medium">{{ $asuransiTandaTerima->source->supir }} - {{ $asuransiTandaTerima->source->no_plat }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Dokumen & Lampiran -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Dokumen Polis</h3>
                    @if($asuransiTandaTerima->asuransi_path)
                        <div class="flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <svg class="w-10 h-10 text-red-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A1 1 0 0111 2.293l4.707 4.707a1 1 0 01.293.707V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Dokumen Polis Asuransi</p>
                                <p class="text-xs text-gray-500">Tersertifikasi</p>
                            </div>
                            <a href="{{ asset('storage/' . $asuransiTandaTerima->asuransi_path) }}" target="_blank"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition duration-200">
                                Lihat Dokumen
                            </a>
                        </div>
                    @else
                        <div class="text-center py-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <p class="text-gray-500 italic">Tidak ada lampiran dokumen</p>
                        </div>
                    @endif
                </div>

                <!-- Audit info -->
                <div class="md:col-span-2 pt-6 border-t mt-4 text-xs text-gray-500 flex justify-between">
                    <span>Dibuat oleh: {{ $asuransiTandaTerima->creator->name ?? 'System' }} ({{ $asuransiTandaTerima->created_at->format('d/m/Y H:i') }})</span>
                    <span>Terakhir diperbarui: {{ $asuransiTandaTerima->updated_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
