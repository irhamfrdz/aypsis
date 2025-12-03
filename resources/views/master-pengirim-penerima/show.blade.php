@extends('layouts.app')

@section('title', 'Detail Pengirim/Penerima')

@section('content')
<div class="container mx-auto px-4 py-8">
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition">Dashboard</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('master-pengirim-penerima.index') }}" class="hover:text-blue-600 transition">Master Pengirim/Penerima</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 font-medium">Detail</li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $masterPengirimPenerima->nama }}</h1>
                <p class="text-gray-600 mt-1">Kode: <span class="font-semibold">{{ $masterPengirimPenerima->kode }}</span></p>
            </div>
            <div class="flex items-center gap-3">
                @if($masterPengirimPenerima->status == 'active')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-2"></i> Active
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        <i class="fas fa-minus-circle mr-2"></i> Inactive
                    </span>
                @endif
                @can('master-pengirim-penerima-update')
                <a href="{{ route('master-pengirim-penerima.edit', $masterPengirimPenerima) }}"
                   class="inline-flex items-center px-4 py-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg transition duration-200">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Informasi</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Kode</dt>
                            <dd class="text-sm text-gray-900">
                                <code class="bg-gray-100 px-2 py-1 rounded">{{ $masterPengirimPenerima->kode }}</code>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Nama</dt>
                            <dd class="text-sm text-gray-900 font-semibold">{{ $masterPengirimPenerima->nama }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Status</dt>
                            <dd class="text-sm text-gray-900">
                                @if($masterPengirimPenerima->status == 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-minus-circle mr-1"></i> Inactive
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">NPWP</dt>
                            <dd class="text-sm text-gray-900">{{ $masterPengirimPenerima->npwp ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if($masterPengirimPenerima->alamat)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Alamat</h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-900">{{ $masterPengirimPenerima->alamat }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    @can('master-pengirim-penerima-update')
                    <a href="{{ route('master-pengirim-penerima.edit', $masterPengirimPenerima) }}"
                       class="block w-full text-center px-4 py-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg transition duration-200">
                        <i class="fas fa-edit mr-2"></i> Edit Data
                    </a>
                    @endcan
                    <a href="{{ route('master-pengirim-penerima.index') }}"
                       class="block w-full text-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Sistem</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $masterPengirimPenerima->creator->name ?? 'System' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Tanggal Dibuat</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $masterPengirimPenerima->created_at?->format('d F Y H:i') ?? '-' }}</dd>
                    </div>
                    @if($masterPengirimPenerima->updated_by)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Diupdate Oleh</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $masterPengirimPenerima->updater->name ?? '-' }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Terakhir Update</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $masterPengirimPenerima->updated_at?->format('d F Y H:i') ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            @can('master-pengirim-penerima-delete')
            <div class="bg-white rounded-lg shadow-sm border border-red-200 p-6">
                <h3 class="text-md font-semibold text-red-900 mb-4">Danger Zone</h3>
                <form action="{{ route('master-pengirim-penerima.destroy', $masterPengirimPenerima) }}"
                      method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?\n\nNama: {{ $masterPengirimPenerima->nama }}\n\nData yang dihapus dapat dipulihkan dari trash.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition duration-200">
                        <i class="fas fa-trash mr-2"></i> Hapus Data
                    </button>
                </form>
            </div>
            @endcan
        </div>
    </div>
</div>
@endsection
