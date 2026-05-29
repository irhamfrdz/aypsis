@extends('layouts.app')

@section('title', 'Detail Tanda Terima SJ Kontainer Sewa')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 text-xs font-semibold rounded bg-cyan-100 text-cyan-800">
                        Kontainer Sewa
                    </span>
                    <span class="text-xs text-gray-500">
                        Dibuat oleh {{ $tandaTerima->createdByUser->name ?? 'System' }} pada {{ $tandaTerima->created_at->format('d/m/Y H:i') }}
                    </span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mt-2 flex items-center gap-2">
                    Tanda Terima: {{ $tandaTerima->nomor_tanda_terima }}
                </h1>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('tanda-terima-surat-jalan-kontainer-sewa.index', ['tipe' => 'tanda_terima']) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition duration-200 text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                <a href="{{ route('tanda-terima-surat-jalan-kontainer-sewa.edit', $tandaTerima->id) }}" 
                   class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg shadow-sm transition duration-200 text-sm">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('tanda-terima-surat-jalan-kontainer-sewa.print', $tandaTerima->id) }}" 
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white font-semibold rounded-lg shadow-sm transition duration-200 text-sm">
                    <i class="fas fa-print mr-2"></i>
                    Cetak Tanda Terima
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Tanda Terima Information -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-cyan-600 px-6 py-4 text-white">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-file-invoice"></i>
                        Informasi Tanda Terima
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <span class="text-gray-500 text-xs font-semibold uppercase tracking-wider block">Nomor Tanda Terima</span>
                            <span class="text-gray-900 font-bold block mt-1 text-base">{{ $tandaTerima->nomor_tanda_terima }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 text-xs font-semibold uppercase tracking-wider block">Tanggal Tanda Terima</span>
                            <span class="text-gray-900 font-medium block mt-1 text-base">
                                {{ $tandaTerima->tanggal_tanda_terima ? $tandaTerima->tanggal_tanda_terima->format('d F Y') : '-' }}
                            </span>
                        </div>
                        <div class="border-t pt-3 md:col-span-2"></div>
                        <div>
                            <span class="text-gray-500 text-xs font-semibold uppercase tracking-wider block">Supir</span>
                            <span class="text-gray-900 font-bold block mt-1">{{ $tandaTerima->supir ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 text-xs font-semibold uppercase tracking-wider block">Nomor Plat</span>
                            <span class="text-gray-900 font-bold block mt-1">{{ $tandaTerima->no_plat ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 text-xs font-semibold uppercase tracking-wider block">Tanggal Mulai Sewa</span>
                            <span class="text-gray-900 font-semibold block mt-1 text-cyan-700">
                                {{ $tandaTerima->tanggal_mulai_sewa ? $tandaTerima->tanggal_mulai_sewa->format('d F Y') : '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500 text-xs font-semibold uppercase tracking-wider block">Tipe Kegiatan</span>
                            <span class="mt-1 inline-block">
                                @if($tandaTerima->kegiatan === 'pengambilan')
                                    <span class="px-3 py-1 text-xs font-bold rounded-md bg-emerald-100 text-emerald-800">
                                        PENGAMBILAN
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs font-bold rounded-md bg-orange-100 text-orange-800">
                                        PENGEMBALIAN
                                    </span>
                                @endif
                            </span>
                        </div>
                        <div class="border-t pt-3 md:col-span-2"></div>
                        <div class="md:col-span-2">
                            <span class="text-gray-500 text-xs font-semibold uppercase tracking-wider block">Opsi Tambahan</span>
                            <div class="flex flex-wrap gap-3 mt-2">
                                @if($tandaTerima->lembur)
                                    <span class="px-3 py-1 text-xs font-bold rounded bg-red-100 text-red-800 border border-red-200">
                                        Lembur
                                    </span>
                                @endif
                                @if($tandaTerima->nginap)
                                    <span class="px-3 py-1 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-200">
                                        Nginap
                                    </span>
                                @endif
                                @if($tandaTerima->tidak_lembur_nginap)
                                    <span class="px-3 py-1 text-xs font-bold rounded bg-gray-100 text-gray-800 border border-gray-200">
                                        Tidak Lembur & Nginap
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="border-t pt-3 md:col-span-2"></div>
                        <div class="md:col-span-2">
                            <span class="text-gray-500 text-xs font-semibold uppercase tracking-wider block">Keterangan</span>
                            <div class="text-gray-800 mt-2 bg-gray-50 p-4 rounded-lg border border-gray-100 whitespace-pre-line text-sm min-h-24">
                                {{ $tandaTerima->keterangan ?: 'Tidak ada keterangan tambahan.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Connected Surat Jalan Information -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-800 px-6 py-4 text-white">
                    <h3 class="text-md font-bold flex items-center gap-2">
                        <i class="fas fa-route"></i>
                        Asal Surat Jalan
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <span class="text-gray-400 text-xs font-semibold block uppercase">Nomor Surat Jalan</span>
                        <a href="{{ route('surat-jalan-kontainer-sewa.show', $tandaTerima->surat_jalan_kontainer_sewa_id) }}" 
                           class="text-cyan-600 hover:text-cyan-700 font-bold mt-1 inline-block text-sm hover:underline">
                            {{ $tandaTerima->nomor_surat_jalan }}
                            <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                        </a>
                    </div>
                    <div class="border-t"></div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-400 text-xs font-semibold block uppercase">Tanggal SJ</span>
                            <span class="text-gray-900 font-medium mt-0.5 block">
                                {{ $tandaTerima->suratJalanKontainerSewa->tanggal ? $tandaTerima->suratJalanKontainerSewa->tanggal->format('d/m/Y') : '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-xs font-semibold block uppercase">Vendor</span>
                            <span class="text-gray-900 font-medium mt-0.5 block">
                                {{ $tandaTerima->suratJalanKontainerSewa->vendor ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-xs font-semibold block uppercase">No Kontainer</span>
                            <span class="text-gray-900 font-bold mt-0.5 block text-cyan-800">
                                {{ $tandaTerima->nomor_kontainer ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-xs font-semibold block uppercase">Tipe / Ukuran</span>
                            <span class="text-gray-900 font-medium mt-0.5 block">
                                {{ $tandaTerima->tipe_kontainer ?? '-' }} / {{ $tandaTerima->ukuran ?? '-' }}
                            </span>
                        </div>
                        <div class="col-span-2 border-t pt-3"></div>
                        <div class="col-span-2">
                            <span class="text-gray-400 text-xs font-semibold block uppercase">Tujuan</span>
                            <span class="text-gray-900 font-medium mt-0.5 block">
                                {{ $tandaTerima->suratJalanKontainerSewa->tujuan ?? '-' }}
                            </span>
                        </div>

                        <div class="col-span-2">
                            <span class="text-gray-400 text-xs font-semibold block uppercase">Lokasi Pengembalian</span>
                            <span class="text-gray-900 font-medium mt-0.5 block">
                                {{ $tandaTerima->suratJalanKontainerSewa->lokasi_pengembalian ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
