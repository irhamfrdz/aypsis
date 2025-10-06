@extends('layouts.app')@extends('layouts.app')@extends('layouts.app')@extends('layouts.app')



@section('title', 'Detail Pembayaran Aktivitas Lain-lain')



@section('content')@section('title', 'Detail Pembayaran Aktivitas Lain-lain')

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="w-full">

        <div class="bg-white shadow rounded-lg">

            <div class="bg-white border-b border-gray-200 px-4 py-3 flex justify-between items-center rounded-t-lg">@section('content')@section('title', 'Detail Pembayaran Aktivitas Lain-lain')@section('title', 'Detail Pembayaran Aktivitas Lain-lain')

                <h3 class="text-lg font-semibold text-gray-800 flex items-center">

                    <i class="fas fa-file-invoice-dollar mr-2 text-blue-600"></i><div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

                    Detail Pembayaran Aktivitas Lain-lain

                </h3>    <div class="w-full">

                <div class="flex gap-2">

                    @can('pembayaran-aktivitas-lainnya-update')        <div class="bg-white shadow rounded-lg">

                        <a href="{{ route('pembayaran-aktivitas-lainnya.edit', $pembayaran->id) }}" class="inline-flex items-center px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium text-xs rounded-md transition duration-150 ease-in-out">

                            <i class="fas fa-edit mr-1"></i> Edit            <div class="bg-white border-b border-gray-200 px-4 py-3 flex justify-between items-center rounded-t-lg">@section('content')@section('content')

                        </a>

                    @endcan                <h3 class="text-lg font-semibold text-gray-800 flex items-center">

                    @can('pembayaran-aktivitas-lainnya-print')

                        <a href="{{ route('pembayaran-aktivitas-lainnya.print', $pembayaran->id) }}" target="_blank" class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium text-xs rounded-md transition duration-150 ease-in-out">                    <i class="fas fa-file-invoice-dollar mr-2 text-blue-600"></i><div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8"><div class="container-fluid">

                            <i class="fas fa-print mr-1"></i> Print

                        </a>                    Detail Pembayaran Aktivitas Lain-lain

                    @endcan

                    <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium text-xs rounded-md transition duration-150 ease-in-out">                </h3>    <div class="w-full">    <div class="row">

                        <i class="fas fa-arrow-left mr-1"></i> Kembali

                    </a>                <div class="flex gap-2">

                </div>

            </div>                    @can('pembayaran-aktivitas-lainnya-update')        <div class="bg-white shadow rounded-lg">        <div class="col-12">



            <div class="p-6">                        <a href="{{ route('pembayaran-aktivitas-lainnya.edit', $pembayaran->id) }}" class="inline-flex items-center px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium text-xs rounded-md transition duration-150 ease-in-out">

                <!-- Header Info -->

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">                            <i class="fas fa-edit mr-1"></i> Edit            <div class="bg-white border-b border-gray-200 px-4 py-3 flex justify-between items-center rounded-t-lg">            <div class="card">

                    <div class="flex justify-between items-center">

                        <div>                        </a>

                            <h4 class="text-lg font-bold text-blue-800">{{ $pembayaran->nomor_pembayaran }}</h4>

                            <p class="text-sm text-blue-600">Nomor Pembayaran</p>                    @endcan                <h3 class="text-lg font-semibold text-gray-800 flex items-center">                <div class="card-header">

                        </div>

                        <div class="text-right">                    @can('pembayaran-aktivitas-lainnya-print')

                            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($pembayaran->total_nominal, 0, ',', '.') }}</p>

                            <p class="text-sm text-gray-600">Total Pembayaran</p>                        <a href="{{ route('pembayaran-aktivitas-lainnya.print', $pembayaran->id) }}" target="_blank" class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium text-xs rounded-md transition duration-150 ease-in-out">                    <i class="fas fa-file-invoice-dollar mr-2 text-blue-600"></i>                    <h3 class="card-title">

                        </div>

                    </div>                            <i class="fas fa-print mr-1"></i> Print

                </div>

                        </a>                    Detail Pembayaran Aktivitas Lain-lain                        <i class="fas fa-file-invoice-dollar mr-2"></i>

                <!-- Informasi Pembayaran -->

                <div class="mb-6">                    @endcan

                    <h5 class="text-md font-semibold text-gray-800 mb-3 flex items-center">

                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>                    <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium text-xs rounded-md transition duration-150 ease-in-out">                </h3>                        Detail Pembayaran Aktivitas Lain-lain

                        Informasi Pembayaran

                    </h5>                        <i class="fas fa-arrow-left mr-1"></i> Kembali



                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">                    </a>                <div class="flex gap-2">                        @if($pembayaran->nomor_pembayaran)

                        <div class="space-y-3">

                            <div class="flex">                </div>

                                <span class="w-40 text-sm font-medium text-gray-700">Nomor Pembayaran:</span>

                                <span class="text-sm text-gray-900 font-semibold">{{ $pembayaran->nomor_pembayaran }}</span>            </div>                    @can('pembayaran-aktivitas-lainnya-update')                            <span class="badge badge-primary ml-2">{{ $pembayaran->nomor_pembayaran }}</span>

                            </div>

                            <div class="flex">

                                <span class="w-40 text-sm font-medium text-gray-700">Tanggal Pembayaran:</span>

                                <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d F Y') }}</span>            <div class="p-6">                        <a href="{{ route('pembayaran-aktivitas-lainnya.edit', $pembayaran->id) }}" class="inline-flex items-center px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium text-xs rounded-md transition duration-150 ease-in-out">                        @endif

                            </div>

                            <div class="flex">                <!-- Header Info -->

                                <span class="w-40 text-sm font-medium text-gray-700">Referensi:</span>

                                <span class="text-sm text-gray-900">{{ $pembayaran->referensi_pembayaran ?? '-' }}</span>                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">                            <i class="fas fa-edit mr-1"></i> Edit                    </h3>

                            </div>

                        </div>                    <div class="flex justify-between items-center">



                        <div class="space-y-3">                        <div>                        </a>                    <div class="card-tools">

                            <div class="flex">

                                <span class="w-40 text-sm font-medium text-gray-700">Total Nominal:</span>                            <h4 class="text-lg font-bold text-blue-800">{{ $pembayaran->nomor_pembayaran }}</h4>

                                <span class="text-sm text-green-600 font-bold">Rp {{ number_format($pembayaran->total_nominal, 0, ',', '.') }}</span>

                            </div>                            <p class="text-sm text-blue-600">Nomor Pembayaran</p>                    @endcan                        @can('pembayaran-aktivitas-lainnya-update')

                            <div class="flex">

                                <span class="w-40 text-sm font-medium text-gray-700">Dibuat Oleh:</span>                        </div>

                                <span class="text-sm text-gray-900">{{ $pembayaran->creator->username ?? '-' }}</span>

                            </div>                        <div class="text-right">                    @can('pembayaran-aktivitas-lainnya-print')                            @if($pembayaran->status !== 'paid')

                            <div class="flex">

                                <span class="w-40 text-sm font-medium text-gray-700">Dibuat Pada:</span>                            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($pembayaran->total_nominal, 0, ',', '.') }}</p>

                                <span class="text-sm text-gray-900">{{ $pembayaran->created_at->format('d/m/Y H:i') }}</span>

                            </div>                            <p class="text-sm text-gray-600">Total Pembayaran</p>                        <a href="{{ route('pembayaran-aktivitas-lainnya.print', $pembayaran->id) }}" target="_blank" class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium text-xs rounded-md transition duration-150 ease-in-out">                                <a href="{{ route('pembayaran-aktivitas-lainnya.edit', $pembayaran->id) }}" class="btn btn-warning btn-sm">

                        </div>

                    </div>                        </div>

                </div>

                    </div>                            <i class="fas fa-print mr-1"></i> Print                                    <i class="fas fa-edit"></i> Edit

                <!-- Keterangan -->

                @if($pembayaran->keterangan)                </div>

                <div class="mb-6">

                    <h5 class="text-md font-semibold text-gray-800 mb-3 flex items-center">                        </a>                                </a>

                        <i class="fas fa-comment-alt mr-2 text-blue-600"></i>

                        Keterangan / Aktivitas                <!-- Informasi Pembayaran -->

                    </h5>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">                <div class="mb-6">                    @endcan                            @endif

                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $pembayaran->keterangan }}</p>

                    </div>                    <h5 class="text-md font-semibold text-gray-800 mb-3 flex items-center">

                </div>

                @endif                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>                    <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium text-xs rounded-md transition duration-150 ease-in-out">                        @endcan



                <!-- Informasi Tambahan -->                        Informasi Pembayaran

                <div class="bg-gray-50 rounded-lg p-4">

                    <h5 class="text-sm font-semibold text-gray-700 mb-2">Informasi Tambahan</h5>                    </h5>                        <i class="fas fa-arrow-left mr-1"></i> Kembali                        @can('pembayaran-aktivitas-lainnya-delete')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-gray-600">

                        <div>

                            <span class="font-medium">Metode Pembayaran:</span>

                            <span class="ml-1">{{ ucfirst(str_replace('_', ' ', $pembayaran->metode_pembayaran)) }}</span>                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">                    </a>                            @if($pembayaran->status === 'draft')

                        </div>

                        <div>                        <div class="space-y-3">

                            <span class="font-medium">Dibuat:</span>

                            <span class="ml-1">{{ $pembayaran->created_at->format('d/m/Y H:i') }}</span>                            <div class="flex">                </div>                                <button class="btn btn-danger btn-sm" onclick="confirmDelete()">

                        </div>

                        <div>                                <span class="w-40 text-sm font-medium text-gray-700">Nomor Pembayaran:</span>

                            <span class="font-medium">Terakhir Diupdate:</span>

                            <span class="ml-1">{{ $pembayaran->updated_at->format('d/m/Y H:i') }}</span>                                <span class="text-sm text-gray-900 font-semibold">{{ $pembayaran->nomor_pembayaran }}</span>            </div>                                    <i class="fas fa-trash"></i> Hapus

                        </div>

                    </div>                            </div>

                </div>

            </div>                            <div class="flex">                                </button>



            <!-- Footer Actions -->                                <span class="w-40 text-sm font-medium text-gray-700">Tanggal Pembayaran:</span>

            <div class="bg-gray-50 border-t border-gray-200 px-4 py-3 rounded-b-lg">

                <div class="flex flex-col sm:flex-row justify-between items-center gap-2">                                <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d F Y') }}</span>            <div class="p-6">                            @endif

                    <div class="flex gap-2">

                        @can('pembayaran-aktivitas-lainnya-update')                            </div>

                            <a href="{{ route('pembayaran-aktivitas-lainnya.edit', $pembayaran->id) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white font-medium text-xs rounded transition duration-150 ease-in-out">

                                <i class="fas fa-edit mr-1"></i> Edit                            <div class="flex">                <!-- Header Info -->                        @endcan

                            </a>

                        @endcan                                <span class="w-40 text-sm font-medium text-gray-700">Referensi:</span>

                        @can('pembayaran-aktivitas-lainnya-delete')

                            <button type="button" onclick="confirmDelete()" class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-medium text-xs rounded transition duration-150 ease-in-out">                                <span class="text-sm text-gray-900">{{ $pembayaran->referensi_pembayaran ?? '-' }}</span>                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">                        <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="btn btn-secondary btn-sm">

                                <i class="fas fa-trash mr-1"></i> Hapus

                            </button>                            </div>

                        @endcan

                    </div>                        </div>                    <div class="flex justify-between items-center">                            <i class="fas fa-arrow-left"></i> Kembali

                    <div>

                        <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white font-medium text-xs rounded transition duration-150 ease-in-out">

                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar

                        </a>                        <div class="space-y-3">                        <div>                        </a>

                    </div>

                </div>                            <div class="flex">

            </div>

        </div>                                <span class="w-40 text-sm font-medium text-gray-700">Total Nominal:</span>                            <h4 class="text-lg font-bold text-blue-800">{{ $pembayaran->nomor_pembayaran }}</h4>                    </div>

    </div>

</div>                                <span class="text-sm text-green-600 font-bold">Rp {{ number_format($pembayaran->total_nominal, 0, ',', '.') }}</span>



<!-- Delete Form -->                            </div>                            <p class="text-sm text-blue-600">Nomor Pembayaran</p>                </div>

@can('pembayaran-aktivitas-lainnya-delete')

    <form id="deleteForm" action="{{ route('pembayaran-aktivitas-lainnya.destroy', $pembayaran->id) }}" method="POST" style="display: none;">                            <div class="flex">

        @csrf

        @method('DELETE')                                <span class="w-40 text-sm font-medium text-gray-700">Dibuat Oleh:</span>                        </div>

    </form>

@endcan                                <span class="text-sm text-gray-900">{{ $pembayaran->creator->username ?? '-' }}</span>

@endsection

                            </div>                        <div class="text-right">                <div class="card-body">

@push('styles')

<style>                            <div class="flex">

    .hover-scale:hover {

        transform: scale(1.02);                                <span class="w-40 text-sm font-medium text-gray-700">Dibuat Pada:</span>                            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($pembayaran->total_nominal, 0, ',', '.') }}</p>                    <!-- Status Badge -->

        transition: transform 0.2s ease-in-out;

    }                                <span class="text-sm text-gray-900">{{ $pembayaran->created_at->format('d/m/Y H:i') }}</span>

</style>

@endpush                            </div>                            <p class="text-sm text-gray-600">Total Pembayaran</p>                    <div class="row mb-3">



@push('scripts')                        </div>

<script>

function confirmDelete() {                    </div>                        </div>                        <div class="col-12">

    if (confirm('Apakah Anda yakin ingin menghapus pembayaran ini? Tindakan ini tidak dapat dibatalkan.')) {

        document.getElementById('deleteForm').submit();                </div>

    }

}                    </div>                            <div class="d-flex justify-content-between align-items-center">

</script>

@endpush                <!-- Keterangan -->

                @if($pembayaran->keterangan)                </div>                                <span class="badge badge-{{ $pembayaran->status === 'paid' ? 'success' : ($pembayaran->status === 'approved' ? 'info' : 'warning') }} badge-lg px-3 py-2 h5 mb-0">

                <div class="mb-6">

                    <h5 class="text-md font-semibold text-gray-800 mb-3 flex items-center">                                    <i class="fas fa-{{ $pembayaran->status === 'paid' ? 'check-circle' : ($pembayaran->status === 'approved' ? 'clock' : 'edit') }} mr-2"></i>

                        <i class="fas fa-comment-alt mr-2 text-blue-600"></i>

                        Keterangan / Aktivitas                <!-- Informasi Pembayaran -->                                    {{ ucfirst($pembayaran->status) }}

                    </h5>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">                <div class="mb-6">                                </span>

                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $pembayaran->keterangan }}</p>

                    </div>                    <h5 class="text-md font-semibold text-gray-800 mb-3 flex items-center">                                <div class="text-right">

                </div>

                @endif                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>                                    <small class="text-muted d-block">Dibuat: {{ $pembayaran->created_at->format('d/m/Y H:i') }}</small>



                <!-- Informasi Tambahan -->                        Informasi Pembayaran                                    <small class="text-muted d-block">Diupdate: {{ $pembayaran->updated_at->format('d/m/Y H:i') }}</small>

                <div class="bg-gray-50 rounded-lg p-4">

                    <h5 class="text-sm font-semibold text-gray-700 mb-2">Informasi Tambahan</h5>                    </h5>                                </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-gray-600">

                        <div>                            </div>

                            <span class="font-medium">Metode Pembayaran:</span>

                            <span class="ml-1">{{ ucfirst(str_replace('_', ' ', $pembayaran->metode_pembayaran)) }}</span>                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">                        </div>

                        </div>

                        <div>                        <div class="space-y-3">                    </div>

                            <span class="font-medium">Dibuat:</span>

                            <span class="ml-1">{{ $pembayaran->created_at->format('d/m/Y H:i') }}</span>                            <div class="flex">

                        </div>

                        <div>                                <span class="w-40 text-sm font-medium text-gray-700">Nomor Pembayaran:</span>                    <hr>

                            <span class="font-medium">Terakhir Diupdate:</span>

                            <span class="ml-1">{{ $pembayaran->updated_at->format('d/m/Y H:i') }}</span>                                <span class="text-sm text-gray-900 font-semibold">{{ $pembayaran->nomor_pembayaran }}</span>

                        </div>

                    </div>                            </div>                    <!-- Informasi Pembayaran -->

                </div>

            </div>                            <div class="flex">                    <h5 class="mb-3">



            <!-- Footer Actions -->                                <span class="w-40 text-sm font-medium text-gray-700">Tanggal Pembayaran:</span>                        <i class="fas fa-info-circle mr-2"></i>

            <div class="bg-gray-50 border-t border-gray-200 px-4 py-3 rounded-b-lg">

                <div class="flex flex-col sm:flex-row justify-between items-center gap-2">                                <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d F Y') }}</span>                        Informasi Pembayaran

                    <div class="flex gap-2">

                        @can('pembayaran-aktivitas-lainnya-update')                            </div>                    </h5>

                            <a href="{{ route('pembayaran-aktivitas-lainnya.edit', $pembayaran->id) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white font-medium text-xs rounded transition duration-150 ease-in-out">

                                <i class="fas fa-edit mr-1"></i> Edit                            <div class="flex">

                            </a>

                        @endcan                                <span class="w-40 text-sm font-medium text-gray-700">Referensi:</span>                    <div class="row">

                        @can('pembayaran-aktivitas-lainnya-delete')

                            <button type="button" onclick="confirmDelete()" class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-medium text-xs rounded transition duration-150 ease-in-out">                                <span class="text-sm text-gray-900">{{ $pembayaran->referensi_pembayaran ?? '-' }}</span>                        <div class="col-md-6">

                                <i class="fas fa-trash mr-1"></i> Hapus

                            </button>                            </div>                            <table class="table table-borderless">

                        @endcan

                    </div>                        </div>                                <tr>

                    <div>

                        <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white font-medium text-xs rounded transition duration-150 ease-in-out">                                    <td width="40%" class="font-weight-bold">Nomor Pembayaran:</td>

                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar

                        </a>                        <div class="space-y-3">                                    <td>{{ $pembayaran->nomor_pembayaran ?? '-' }}</td>

                    </div>

                </div>                            <div class="flex">                                </tr>

            </div>

        </div>                                <span class="w-40 text-sm font-medium text-gray-700">Total Nominal:</span>                                <tr>

    </div>

</div>                                <span class="text-sm text-green-600 font-bold">Rp {{ number_format($pembayaran->total_nominal, 0, ',', '.') }}</span>                                    <td class="font-weight-bold">Tanggal Pembayaran:</td>



<!-- Delete Form -->                            </div>                                    <td>{{ $pembayaran->tanggal_pembayaran ? $pembayaran->tanggal_pembayaran->format('d/m/Y') : '-' }}</td>

@can('pembayaran-aktivitas-lainnya-delete')

    <form id="deleteForm" action="{{ route('pembayaran-aktivitas-lainnya.destroy', $pembayaran->id) }}" method="POST" style="display: none;">                            <div class="flex">                                </tr>

        @csrf

        @method('DELETE')                                <span class="w-40 text-sm font-medium text-gray-700">Dibuat Oleh:</span>                                <tr>

    </form>

@endcan                                <span class="text-sm text-gray-900">{{ $pembayaran->creator->username ?? '-' }}</span>                                    <td class="font-weight-bold">Metode Pembayaran:</td>

@endsection

                            </div>                                    <td>

@push('styles')

<style>                            <div class="flex">                                        @switch($pembayaran->metode_pembayaran)

    .hover-scale:hover {

        transform: scale(1.02);                                <span class="w-40 text-sm font-medium text-gray-700">Dibuat Pada:</span>                                            @case('cash')

        transition: transform 0.2s ease-in-out;

    }                                <span class="text-sm text-gray-900">{{ $pembayaran->created_at->format('d/m/Y H:i') }}</span>                                                <span class="badge badge-success">Cash</span>

</style>

@endpush                            </div>                                                @break



@push('scripts')                        </div>                                            @case('transfer')

<script>

function confirmDelete() {                    </div>                                                <span class="badge badge-info">Transfer</span>

    if (confirm('Apakah Anda yakin ingin menghapus pembayaran ini? Tindakan ini tidak dapat dibatalkan.')) {

        document.getElementById('deleteForm').submit();                </div>                                                @break

    }

}                                            @case('check')

</script>

@endpush                <!-- Keterangan -->                                                <span class="badge badge-warning">Check</span>


                @if($pembayaran->keterangan)                                                @break

                <div class="mb-6">                                            @case('credit_card')

                    <h5 class="text-md font-semibold text-gray-800 mb-3 flex items-center">                                                <span class="badge badge-primary">Credit Card</span>

                        <i class="fas fa-comment-alt mr-2 text-blue-600"></i>                                                @break

                        Keterangan / Aktivitas                                            @default

                    </h5>                                                <span class="badge badge-secondary">-</span>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">                                        @endswitch

                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $pembayaran->keterangan }}</p>                                    </td>

                    </div>                                </tr>

                </div>                                <tr>

                @endif                                    <td class="font-weight-bold">Referensi:</td>

                                    <td>{{ $pembayaran->referensi_pembayaran ?? '-' }}</td>

                <!-- Informasi Tambahan -->                                </tr>

                <div class="bg-gray-50 rounded-lg p-4">                            </table>

                    <h5 class="text-sm font-semibold text-gray-700 mb-2">Informasi Tambahan</h5>                        </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-gray-600">

                        <div>                        <div class="col-md-6">

                            <span class="font-medium">Metode Pembayaran:</span>                            <table class="table table-borderless">

                            <span class="ml-1">{{ ucfirst(str_replace('_', ' ', $pembayaran->metode_pembayaran)) }}</span>                                <tr>

                        </div>                                    <td width="40%" class="font-weight-bold">Total Nominal:</td>

                        <div>                                    <td class="text-success font-weight-bold h5">

                            <span class="font-medium">Dibuat:</span>                                        Rp {{ number_format($pembayaran->total_nominal, 0, ',', '.') }}

                            <span class="ml-1">{{ $pembayaran->created_at->format('d/m/Y H:i') }}</span>                                    </td>

                        </div>                                </tr>

                        <div>                                <tr>

                            <span class="font-medium">Terakhir Diupdate:</span>                                    <td class="font-weight-bold">Jumlah Aktivitas:</td>

                            <span class="ml-1">{{ $pembayaran->updated_at->format('d/m/Y H:i') }}</span>                                    <td>

                        </div>                                        <span class="badge badge-info">{{ $pembayaran->detailPembayaran->count() }} aktivitas</span>

                    </div>                                    </td>

                </div>                                </tr>

            </div>                                <tr>

                                    <td class="font-weight-bold">Dibuat Oleh:</td>

            <!-- Footer Actions -->                                    <td>{{ $pembayaran->createdBy->name ?? '-' }}</td>

            <div class="bg-gray-50 border-t border-gray-200 px-4 py-3 rounded-b-lg">                                </tr>

                <div class="flex flex-col sm:flex-row justify-between items-center gap-2">                                @if($pembayaran->approved_by)

                    <div class="flex gap-2">                                    <tr>

                        @can('pembayaran-aktivitas-lainnya-update')                                        <td class="font-weight-bold">Disetujui Oleh:</td>

                            <a href="{{ route('pembayaran-aktivitas-lainnya.edit', $pembayaran->id) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white font-medium text-xs rounded transition duration-150 ease-in-out">                                        <td>{{ $pembayaran->approvedBy->name ?? '-' }}</td>

                                <i class="fas fa-edit mr-1"></i> Edit                                    </tr>

                            </a>                                    <tr>

                        @endcan                                        <td class="font-weight-bold">Tanggal Persetujuan:</td>

                        @can('pembayaran-aktivitas-lainnya-delete')                                        <td>{{ $pembayaran->approved_at ? $pembayaran->approved_at->format('d/m/Y H:i') : '-' }}</td>

                            <button type="button" onclick="confirmDelete()" class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-medium text-xs rounded transition duration-150 ease-in-out">                                    </tr>

                                <i class="fas fa-trash mr-1"></i> Hapus                                @endif

                            </button>                            </table>

                        @endcan                        </div>

                    </div>                    </div>

                    <div>

                        <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white font-medium text-xs rounded transition duration-150 ease-in-out">                    @if($pembayaran->keterangan)

                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar                        <div class="row">

                        </a>                            <div class="col-12">

                    </div>                                <div class="form-group">

                </div>                                    <label class="font-weight-bold">Keterangan:</label>

            </div>                                    <div class="border rounded p-3 bg-light">

        </div>                                        {{ $pembayaran->keterangan }}

    </div>                                    </div>

</div>                                </div>

                            </div>

<!-- Delete Form -->                        </div>

@can('pembayaran-aktivitas-lainnya-delete')                    @endif

    <form id="deleteForm" action="{{ route('pembayaran-aktivitas-lainnya.destroy', $pembayaran->id) }}" method="POST" style="display: none;">

        @csrf                    <hr>

        @method('DELETE')

    </form>                    <!-- Detail Aktivitas -->

@endcan                    <div class="d-flex justify-content-between align-items-center mb-3">

@endsection                        <h5 class="mb-0">

                            <i class="fas fa-list mr-2"></i>

@push('styles')                            Detail Aktivitas yang Dibayar

<style>                        </h5>

    .hover-scale:hover {                        <div>

        transform: scale(1.02);                            <button class="btn btn-sm btn-outline-primary" id="exportBtn">

        transition: transform 0.2s ease-in-out;                                <i class="fas fa-download"></i> Export

    }                            </button>

</style>                        </div>

@endpush                    </div>



@push('scripts')                    <div class="table-responsive">

<script>                        <table class="table table-bordered table-hover" id="aktivitas_detail_table">

function confirmDelete() {                            <thead class="thead-light">

    if (confirm('Apakah Anda yakin ingin menghapus pembayaran ini? Tindakan ini tidak dapat dibatalkan.')) {                                <tr>

        document.getElementById('deleteForm').submit();                                    <th width="5%">#</th>

    }                                    <th>Nomor Aktivitas</th>

}                                    <th>Tanggal</th>

</script>                                    <th>Deskripsi</th>

@endpush                                    <th>Vendor</th>

                                    <th>Nominal Asli</th>
                                    <th>Nominal Dibayar</th>
                                    <th>Selisih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pembayaran->detailPembayaran as $index => $detail)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <a href="#" class="text-primary" data-toggle="modal" data-target="#aktivitasModal{{ $detail->id }}">
                                                {{ $detail->aktivitasLain->nomor_aktivitas }}
                                            </a>
                                        </td>
                                        <td>{{ $detail->aktivitasLain->tanggal_aktivitas ? $detail->aktivitasLain->tanggal_aktivitas->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 250px;" title="{{ $detail->aktivitasLain->deskripsi_aktivitas }}">
                                                {{ $detail->aktivitasLain->deskripsi_aktivitas }}
                                            </div>
                                        </td>
                                        <td>{{ $detail->aktivitasLain->vendor->nama ?? '-' }}</td>
                                        <td class="text-right">Rp {{ number_format($detail->aktivitasLain->nominal, 0, ',', '.') }}</td>
                                        <td class="text-right text-success font-weight-bold">
                                            Rp {{ number_format($detail->nominal_dibayar, 0, ',', '.') }}
                                        </td>
                                        <td class="text-right">
                                            @php
                                                $selisih = $detail->aktivitasLain->nominal - $detail->nominal_dibayar;
                                            @endphp
                                            @if($selisih > 0)
                                                <span class="text-warning">-Rp {{ number_format($selisih, 0, ',', '.') }}</span>
                                            @elseif($selisih < 0)
                                                <span class="text-info">+Rp {{ number_format(abs($selisih), 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Modal untuk detail aktivitas -->
                                    <div class="modal fade" id="aktivitasModal{{ $detail->id }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Detail Aktivitas {{ $detail->aktivitasLain->nomor_aktivitas }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <table class="table table-borderless">
                                                                <tr>
                                                                    <td class="font-weight-bold">Nomor:</td>
                                                                    <td>{{ $detail->aktivitasLain->nomor_aktivitas }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="font-weight-bold">Tanggal:</td>
                                                                    <td>{{ $detail->aktivitasLain->tanggal_aktivitas ? $detail->aktivitasLain->tanggal_aktivitas->format('d/m/Y') : '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="font-weight-bold">Vendor:</td>
                                                                    <td>{{ $detail->aktivitasLain->vendor->nama ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="font-weight-bold">Nominal:</td>
                                                                    <td>Rp {{ number_format($detail->aktivitasLain->nominal, 0, ',', '.') }}</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <table class="table table-borderless">
                                                                <tr>
                                                                    <td class="font-weight-bold">Dibayar:</td>
                                                                    <td class="text-success font-weight-bold">
                                                                        Rp {{ number_format($detail->nominal_dibayar, 0, ',', '.') }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="font-weight-bold">Status:</td>
                                                                    <td>
                                                                        <span class="badge badge-success">Dibayar</span>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Deskripsi:</label>
                                                                <div class="border rounded p-3 bg-light">
                                                                    {{ $detail->aktivitasLain->deskripsi_aktivitas }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                                            <p class="text-muted">Tidak ada detail aktivitas.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($pembayaran->detailPembayaran->count() > 0)
                                <tfoot class="thead-light">
                                    <tr>
                                        <th colspan="6" class="text-right">Total:</th>
                                        <th class="text-right text-success">
                                            Rp {{ number_format($pembayaran->detailPembayaran->sum('nominal_dibayar'), 0, ',', '.') }}
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    <!-- Action Buttons -->
                    @if($pembayaran->status !== 'paid')
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <div class="bg-light p-3 rounded">
                                    <h6 class="font-weight-bold mb-3">Aksi Tersedia:</h6>
                                    <div class="btn-group" role="group">
                                        @if($pembayaran->status === 'draft')
                                            @can('pembayaran-aktivitas-lainnya-update')
                                                <a href="{{ route('pembayaran-aktivitas-lainnya.edit', $pembayaran->id) }}" class="btn btn-warning">
                                                    <i class="fas fa-edit"></i> Edit Pembayaran
                                                </a>
                                            @endcan
                                            @can('pembayaran-aktivitas-lainnya-approve')
                                                <form action="{{ route('pembayaran-aktivitas-lainnya.approve', $pembayaran->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin menyetujui pembayaran ini?')">
                                                        <i class="fas fa-check"></i> Setujui Pembayaran
                                                    </button>
                                                </form>
                                            @endcan
                                        @elseif($pembayaran->status === 'approved')
                                            @can('pembayaran-aktivitas-lainnya-approve')
                                                <form action="{{ route('pembayaran-aktivitas-lainnya.pay', $pembayaran->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success" onclick="return confirm('Apakah Anda yakin pembayaran ini sudah dilakukan?')">
                                                        <i class="fas fa-credit-card"></i> Tandai sebagai Dibayar
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle mr-2"></i>
                            <strong>Pembayaran Selesai!</strong> Pembayaran ini telah ditandai sebagai selesai dan tidak dapat diubah lagi.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
@can('pembayaran-aktivitas-lainnya-delete')
    @if($pembayaran->status === 'draft')
        <form id="deleteForm" action="{{ route('pembayaran-aktivitas-lainnya.destroy', $pembayaran->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endif
@endcan
@endsection

@push('styles')
<style>
    .badge-lg {
        font-size: 1rem;
    }

    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .table th {
        background-color: #f8f9fa;
    }

    .table-borderless td {
        border: none;
        padding: 0.25rem 0.75rem;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #dee2e6;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Export functionality
    $('#exportBtn').on('click', function() {
        // Simple table to CSV export
        let csv = [];
        let headers = [];

        // Get headers
        $('#aktivitas_detail_table thead tr th').each(function() {
            headers.push($(this).text().trim());
        });
        csv.push(headers.join(','));

        // Get data
        $('#aktivitas_detail_table tbody tr').each(function() {
            let row = [];
            $(this).find('td').each(function() {
                let text = $(this).text().trim().replace(/,/g, ';');
                row.push(text);
            });
            if (row.length > 0) {
                csv.push(row.join(','));
            }
        });

        // Download
        let csvContent = csv.join('\n');
        let blob = new Blob([csvContent], { type: 'text/csv' });
        let url = window.URL.createObjectURL(blob);
        let a = document.createElement('a');
        a.href = url;
        a.download = 'pembayaran_aktivitas_{{ $pembayaran->id }}_{{ date("Y-m-d") }}.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    });
});

function confirmDelete() {
    if (confirm('Apakah Anda yakin ingin menghapus pembayaran ini? Tindakan ini tidak dapat dibatalkan.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush
