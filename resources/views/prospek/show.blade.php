@extends('layouts.app')@extends('layouts.app')



@section('content')@section('content')

<div class="container mx-auto px-4 py-6"><div class="container mx-auto px-4">

    {{-- Header --}}    <div class="flex flex-col">

    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">        <div class="w-full">

        <div class="flex items-center justify-between">            <div class="bg-white shadow-lg rounded-lg">

            <div class="flex items-center">                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-start">

                <i class="fas fa-eye mr-3 text-blue-600 text-2xl"></i>                    <div>

                <div>                        <h4 class="text-xl font-semibold text-gray-800 mb-1">

                    <h1 class="text-2xl font-bold text-gray-800">Detail Prospek</h1>                            <i class="fas fa-info-circle mr-2 text-blue-600"></i> Detail Kontainer Prospek

                    <p class="text-gray-600">Informasi detail data prospek pengiriman kontainer</p>                        </h4>

                </div>                        <p class="text-sm text-gray-600">Informasi lengkap kontainer {{ $kontainer->nomor_kontainer }}</p>

            </div>                    </div>

            <a href="{{ route('prospek.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 flex items-center">                    <a href="{{ route('prospek.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 flex items-center">

                <i class="fas fa-arrow-left mr-2"></i>                        <i class="fas fa-arrow-left mr-2"></i> Kembali

                Kembali                    </a>

            </a>                </div>

        </div>

    </div>                <div class="p-6">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Detail Content --}}                        <!-- Informasi Kontainer -->

    <div class="bg-white rounded-lg shadow-sm p-6">                        <div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">                            <h5 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">

            {{-- Left Column - Main Information --}}                                <i class="fas fa-cube mr-2 text-blue-600"></i> Informasi Kontainer

            <div class="space-y-6">                            </h5>

                <div class="border-b border-gray-200 pb-4">                            <div class="bg-gray-50 rounded-lg p-4">

                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Utama</h3>                                <div class="space-y-3">

                    <div class="grid grid-cols-1 gap-4">                                    <div class="flex justify-between">

                        <div>                                        <span class="font-medium text-gray-600">Nomor Kontainer:</span>

                            <label class="block text-sm font-medium text-gray-500">ID Prospek</label>                                        <span class="text-gray-900">{{ $kontainer->nomor_kontainer ?: '-' }}</span>

                            <p class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 px-2 py-1 rounded">{{ $prospek->id }}</p>                                    </div>

                        </div>                                    <div class="flex justify-between">

                        <div>                                        <span class="font-medium text-gray-600">Ukuran:</span>

                            <label class="block text-sm font-medium text-gray-500">Tanggal</label>                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">

                            <p class="mt-1 text-sm text-gray-900">                                            {{ $kontainer->ukuran ?: '-' }}

                                <i class="fas fa-calendar mr-2 text-blue-600"></i>                                        </span>

                                {{ $prospek->tanggal ? $prospek->tanggal->format('d F Y') : '-' }}                                    </div>

                            </p>                                    <div class="flex justify-between">

                        </div>                                        <span class="font-medium text-gray-600">Tipe Kontainer:</span>

                        <div>                                        <span class="text-gray-900">{{ $kontainer->tipe_kontainer ?: '-' }}</span>

                            <label class="block text-sm font-medium text-gray-500">Status</label>                                    </div>

                            <p class="mt-1">                               </div>

                                @php                            </div>

                                    $statusColors = [                        </div>

                                        'aktif' => 'bg-green-100 text-green-800',

                                        'sudah_muat' => 'bg-blue-100 text-blue-800',                        <!-- Status Siap Muat -->

                                        'batal' => 'bg-red-100 text-red-800'                        <div>

                                    ];                            <h5 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">

                                    $statusLabels = [                                <i class="fas fa-check-circle mr-2 text-green-600"></i> Status Siap Muat

                                        'aktif' => 'Aktif',                            </h5>

                                        'sudah_muat' => 'Sudah Muat',                            <div class="bg-gray-50 rounded-lg p-4">

                                        'batal' => 'Batal'                                <div class="space-y-3">

                                    ];                                    <div class="flex justify-between items-center">

                                @endphp                                        <span class="font-medium text-gray-600">Status:</span>

                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$prospek->status] ?? 'bg-gray-100 text-gray-800' }}">                                        <div>

                                    {{ $statusLabels[$prospek->status] ?? $prospek->status }}                                            @if($kontainer->jenis_tanda_terima == 'Tanda Terima')

                                </span>                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">

                            </p>                                                    <i class="fas fa-check-circle mr-1"></i> Tanda Terima

                        </div>                                                </span>

                    </div>                                            @elseif($kontainer->jenis_tanda_terima == 'Tanda Terima Tanpa SJ')

                </div>                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">

                                                    <i class="fas fa-exclamation-circle mr-1"></i> TT Tanpa SJ

                <div class="border-b border-gray-200 pb-4">                                                </span>

                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pengiriman</h3>                                            @else

                    <div class="grid grid-cols-1 gap-4">                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">

                        <div>                                                    <i class="fas fa-clock mr-1"></i> Belum Siap

                            <label class="block text-sm font-medium text-gray-500">Nama Supir</label>                                                </span>

                            <p class="mt-1 text-sm text-gray-900">                                            @endif

                                <i class="fas fa-user mr-2 text-green-600"></i>                                        </div>

                                {{ $prospek->nama_supir ?? '-' }}                                    </div>

                            </p>                                    <div class="flex justify-between">

                        </div>                                        <span class="font-medium text-gray-600">No. Tanda Terima:</span>

                        <div>                                        <span class="text-gray-900">{{ $kontainer->nomor_tanda_terima ?: '-' }}</span>

                            <label class="block text-sm font-medium text-gray-500">PT/Pengirim</label>                                    </div>

                            <p class="mt-1 text-sm text-gray-900">                                    <div class="flex justify-between">

                                <i class="fas fa-building mr-2 text-purple-600"></i>                                        <span class="font-medium text-gray-600">Tanggal TT:</span>

                                {{ $prospek->pt_pengirim ?? '-' }}                                        <span class="text-gray-900">

                            </p>                                            @if($kontainer->tanggal_tanda_terima)

                        </div>                                                {{ \Carbon\Carbon::parse($kontainer->tanggal_tanda_terima)->format('d/m/Y') }}

                        <div>                                            @else

                            <label class="block text-sm font-medium text-gray-500">Tujuan Pengiriman</label>                                                <span class="text-gray-500">-</span>

                            <p class="mt-1 text-sm text-gray-900">                                            @endif

                                <i class="fas fa-map-marker-alt mr-2 text-red-600"></i>                                        </span>

                                {{ $prospek->tujuan_pengiriman ?? '-' }}                                    </div>

                            </p>                                    <div class="flex justify-between">

                        </div>                                        <span class="font-medium text-gray-600">Keterangan:</span>

                        <div>                                        <span class="text-gray-900">{{ $kontainer->keterangan ?: '-' }}</span>

                            <label class="block text-sm font-medium text-gray-500">Nama Kapal</label>                                    </div>

                            <p class="mt-1 text-sm text-gray-900">                                </div>

                                <i class="fas fa-ship mr-2 text-blue-600"></i>                            </div>

                                {{ $prospek->nama_kapal ?? '-' }}                        </div>

                            </p>                    </div>

                        </div>

                    </div>                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

                </div>                        <!-- Informasi Tujuan dan Kapal -->

            </div>                        <div>

                            <h5 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">

            {{-- Right Column - Container Information --}}                                <i class="fas fa-map-marker-alt mr-2 text-red-500"></i> Tujuan & Kapal

            <div class="space-y-6">                            </h5>

                <div class="border-b border-gray-200 pb-4">                            <div class="bg-gray-50 rounded-lg p-4">

                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kontainer</h3>                                <div class="space-y-3">

                    <div class="grid grid-cols-1 gap-4">                                    <div class="flex justify-between">

                        <div>                                        <span class="font-medium text-gray-600">Tujuan Kirim:</span>

                            <label class="block text-sm font-medium text-gray-500">Barang</label>                                        <span class="text-gray-900">{{ $kontainer->nama_tujuan ?: '-' }}</span>

                            <p class="mt-1 text-sm text-gray-900">                                    </div>

                                <i class="fas fa-box mr-2 text-orange-600"></i>                                    <div class="flex justify-between">

                                {{ $prospek->barang ?? '-' }}                                        <span class="font-medium text-gray-600">Kode Tujuan:</span>

                            </p>                                        <span class="text-gray-900">{{ $kontainer->kode_tujuan ?: '-' }}</span>

                        </div>                                    </div>

                        <div>                                    <div class="flex justify-between">

                            <label class="block text-sm font-medium text-gray-500">Ukuran Kontainer</label>                                        <span class="font-medium text-gray-600">Nama Kapal:</span>

                            <p class="mt-1">                                        <span class="text-gray-900">{{ $kontainer->nama_kapal ?: '-' }}</span>

                                @if($prospek->ukuran)                                    </div>

                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium                                 </div>

                                        {{ $prospek->ukuran == '20' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">                            </div>

                                        <i class="fas fa-container mr-2"></i>                        </div>

                                        {{ $prospek->ukuran }} Feet

                                    </span>                        <!-- Informasi Surat Jalan -->

                                @else                        <div>

                                    <span class="text-gray-500">-</span>                            <h5 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">

                                @endif                                <i class="fas fa-file-text mr-2 text-purple-600"></i> Surat Jalan

                            </p>                            </h5>

                        </div>                            <div class="bg-gray-50 rounded-lg p-4">

                        <div>                                <div class="space-y-3">

                            <label class="block text-sm font-medium text-gray-500">Nomor Kontainer</label>                                    <div class="flex justify-between">

                            <p class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 px-2 py-1 rounded">                                        <span class="font-medium text-gray-600">No. Surat Jalan:</span>

                                {{ $prospek->nomor_kontainer ?? '-' }}                                        <span class="text-gray-900">{{ $kontainer->no_surat_jalan ?: '-' }}</span>

                            </p>                                    </div>

                        </div>                                    <div class="flex justify-between">

                        <div>                                        <span class="font-medium text-gray-600">Tanggal SJ:</span>

                            <label class="block text-sm font-medium text-gray-500">Nomor Seal</label>                                        <span class="text-gray-900">

                            <p class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 px-2 py-1 rounded">                                            @if($kontainer->tanggal_surat_jalan)

                                {{ $prospek->no_seal ?? '-' }}                                                {{ \Carbon\Carbon::parse($kontainer->tanggal_surat_jalan)->format('d/m/Y') }}

                            </p>                                            @else

                        </div>                                                <span class="text-gray-500">-</span>

                    </div>                                            @endif

                </div>                                        </span>

                                    </div>

                <div class="border-b border-gray-200 pb-4">                                </div>

                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Keterangan</h3>                            </div>

                    <div class="bg-gray-50 rounded-lg p-4">                        </div>

                        @if($prospek->keterangan)                    </div>

                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $prospek->keterangan }}</p>

                        @else                    <div class="mt-6">

                            <p class="text-sm text-gray-500 italic">Tidak ada keterangan tambahan</p>                        <!-- Informasi Tanggal -->

                        @endif                        <div>

                    </div>                            <h5 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">

                </div>                                <i class="fas fa-calendar mr-2 text-indigo-600"></i> Riwayat Tanggal

            </div>                            </h5>

        </div>                            <div class="bg-gray-50 rounded-lg p-4">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Audit Information --}}                                    <div class="space-y-3">

        <div class="mt-8 border-t border-gray-200 pt-6">                                        <div class="flex justify-between">

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Audit</h3>                                            <span class="font-medium text-gray-600">Tanggal Masuk:</span>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">                                            <span class="text-gray-900">

                <div class="bg-blue-50 rounded-lg p-4">                                                @if($kontainer->tanggal_masuk)

                    <div class="flex items-center mb-2">                                                    {{ \Carbon\Carbon::parse($kontainer->tanggal_masuk)->format('d/m/Y') }}

                        <i class="fas fa-plus-circle text-blue-600 mr-2"></i>                                                @else

                        <h4 class="text-sm font-medium text-blue-800">Dibuat</h4>                                                    <span class="text-gray-500">-</span>

                    </div>                                                @endif

                    <div class="text-sm text-blue-700">                                            </span>

                        <p><strong>Oleh:</strong> {{ $prospek->createdBy->name ?? 'System' }}</p>                                        </div>

                        <p><strong>Tanggal:</strong> {{ $prospek->created_at ? $prospek->created_at->format('d F Y H:i') : '-' }}</p>                                        <div class="flex justify-between">

                    </div>                                            <span class="font-medium text-gray-600">Created At:</span>

                </div>                                            <span class="text-gray-900">

                                                                @if($kontainer->created_at)

                <div class="bg-green-50 rounded-lg p-4">                                                    {{ \Carbon\Carbon::parse($kontainer->created_at)->format('d/m/Y H:i') }}

                    <div class="flex items-center mb-2">                                                @else

                        <i class="fas fa-edit text-green-600 mr-2"></i>                                                    <span class="text-gray-500">-</span>

                        <h4 class="text-sm font-medium text-green-800">Terakhir Diperbarui</h4>                                                @endif

                    </div>                                            </span>

                    <div class="text-sm text-green-700">                                        </div>

                        <p><strong>Oleh:</strong> {{ $prospek->updatedBy->name ?? 'System' }}</p>                                    </div>

                        <p><strong>Tanggal:</strong> {{ $prospek->updated_at ? $prospek->updated_at->format('d F Y H:i') : '-' }}</p>                                    <div class="space-y-3">

                    </div>                                        <div class="flex justify-between">

                </div>                                            <span class="font-medium text-gray-600">Tanggal Keluar:</span>

            </div>                                            <span class="text-gray-900">

        </div>                                                @if($kontainer->tanggal_keluar)

                                                    {{ \Carbon\Carbon::parse($kontainer->tanggal_keluar)->format('d/m/Y') }}

        {{-- Action Buttons --}}                                                @else

        <div class="mt-8 flex justify-center">                                                    <span class="text-gray-500">-</span>

            <a href="{{ route('prospek.index') }}"                                                 @endif

               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-md transition duration-200 flex items-center">                                            </span>

                <i class="fas fa-arrow-left mr-2"></i>                                        </div>

                Kembali ke Daftar Prospek                                        <div class="flex justify-between">

            </a>                                            <span class="font-medium text-gray-600">Updated At:</span>

        </div>                                            <span class="text-gray-900">

    </div>                                                @if($kontainer->updated_at)

</div>                                                    {{ \Carbon\Carbon::parse($kontainer->updated_at)->format('d/m/Y H:i') }}

@endsection                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($kontainer->keterangan)
                        <div class="mt-6">
                            <h5 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-sticky-note mr-2 text-yellow-600"></i> Keterangan
                            </h5>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-blue-800">{{ $kontainer->keterangan }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('prospek.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
                        </a>
                        <div>
                            @if($kontainer->jenis_tanda_terima == 'Tanda Terima' || $kontainer->jenis_tanda_terima == 'Tanda Terima Tanpa SJ')
                                <span class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-ship mr-2"></i> Siap untuk Dimuat
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-2"></i> Belum Siap Muat
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
