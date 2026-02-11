@extends('layouts.app')

@section('title', 'Detail Tanda Terima Bongkaran')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Detail Tanda Terima Bongkaran</h1>
                        <p class="text-gray-600 mt-1">Detail informasi tanda terima bongkaran</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('tanda-terima-bongkaran.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                        <a href="{{ route('tanda-terima-bongkaran.edit', $tandaTerimaBongkaran->id) }}" 
                           class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition duration-200">
                            <i class="fas fa-edit mr-2"></i>
                            Edit
                        </a>
                        <a href="{{ route('tanda-terima-bongkaran.print', $tandaTerimaBongkaran->id) }}" 
                           target="_blank"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200">
                            <i class="fas fa-print mr-2"></i>
                            Print
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Content -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="space-y-6">
                <!-- Informasi Dasar -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nomor Tanda Terima</p>
                            <p class="text-base text-gray-900 font-medium">{{ $tandaTerimaBongkaran->nomor_tanda_terima }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tanggal Tanda Terima</p>
                            <p class="text-base text-gray-900">{{ \Carbon\Carbon::parse($tandaTerimaBongkaran->tanggal_tanda_terima)->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Gudang</p>
                            <p class="text-base text-gray-900">{{ $tandaTerimaBongkaran->gudang->nama_gudang ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Surat Jalan Bongkaran</p>
                            <p class="text-base text-gray-900">
                                {{ $tandaTerimaBongkaran->suratJalanBongkaran->nomor_surat_jalan ?? '-' }}
                                @if($tandaTerimaBongkaran->suratJalanBongkaran && $tandaTerimaBongkaran->suratJalanBongkaran->bl)
                                    <span class="text-gray-500 text-sm">(BL: {{ $tandaTerimaBongkaran->suratJalanBongkaran->bl->nomor_bl }})</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Informasi Kontainer -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Informasi Kontainer</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">No Kontainer</p>
                            <p class="text-base text-gray-900">{{ $tandaTerimaBongkaran->no_kontainer ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">No Seal</p>
                            <p class="text-base text-gray-900">{{ $tandaTerimaBongkaran->no_seal ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Kegiatan</p>
                            <p class="text-base text-gray-900 uppercase">{{ $tandaTerimaBongkaran->kegiatan ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $tandaTerimaBongkaran->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($tandaTerimaBongkaran->status === 'approved' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($tandaTerimaBongkaran->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Keterangan</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $tandaTerimaBongkaran->keterangan ?: 'Tidak ada keterangan' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
