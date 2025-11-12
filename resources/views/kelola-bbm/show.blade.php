@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-500 mb-2">
            <a href="{{ route('kelola-bbm.index') }}" class="hover:text-indigo-600">
                <i class="fas fa-gas-pump mr-1"></i>
                Kelola BBM
            </a>
            <i class="fas fa-chevron-right mx-2 text-xs"></i>
            <span class="text-gray-900">Detail Data BBM</span>
        </div>
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-file-alt mr-2 text-indigo-600"></i>
                    Detail Data BBM
                </h1>
                <p class="text-gray-600 mt-1">Informasi lengkap data BBM</p>
            </div>
            <div class="flex space-x-2">
                @can('master-kelola-bbm-edit')
                    <a href="{{ route('kelola-bbm.edit', $kelolaBbm) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </a>
                @endcan
                @can('master-kelola-bbm-delete')
                    <form action="{{ route('kelola-bbm.destroy', $kelolaBbm) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data BBM tanggal {{ $kelolaBbm->tanggal->format('d/m/Y') }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">
                <i class="fas fa-info-circle mr-2 text-indigo-600"></i>
                Informasi Data BBM
            </h3>
        </div>
        
        <dl class="px-6 py-5 grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- ID -->
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    ID
                </dt>
                <dd class="mt-1 text-sm text-gray-900 font-medium">
                    #{{ $kelolaBbm->id }}
                </dd>
            </div>

            <!-- Periode (Bulan Tahun) -->
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Periode
                </dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                        <i class="fas fa-calendar mr-2"></i>
                        {{ $kelolaBbm->formatted_bulan_tahun }}
                    </span>
                </dd>
            </div>

            <!-- BBM Per Liter -->
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    BBM Per Liter
                </dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-money-bill-wave mr-2"></i>
                        Rp {{ number_format($kelolaBbm->bbm_per_liter, 0, ',', '.') }}
                    </span>
                </dd>
            </div>

            <!-- Persentase -->
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Persentase
                </dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-percentage mr-2"></i>
                        {{ number_format($kelolaBbm->persentase, 2) }}%
                    </span>
                </dd>
            </div>

            <!-- Keterangan -->
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">
                    Keterangan
                </dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @if($kelolaBbm->keterangan)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            {{ $kelolaBbm->keterangan }}
                        </div>
                    @else
                        <span class="text-sm text-gray-400 italic">Tidak ada keterangan</span>
                    @endif
                </dd>
            </div>

            <!-- Timestamp Info -->
            <div class="sm:col-span-2 pt-4 border-t border-gray-200">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500">
                            Dibuat Pada
                        </dt>
                        <dd class="mt-1 text-xs text-gray-900">
                            <i class="fas fa-clock mr-1 text-gray-400"></i>
                            {{ $kelolaBbm->created_at->format('d/m/Y H:i:s') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">
                            Terakhir Diupdate
                        </dt>
                        <dd class="mt-1 text-xs text-gray-900">
                            <i class="fas fa-sync-alt mr-1 text-gray-400"></i>
                            {{ $kelolaBbm->updated_at->format('d/m/Y H:i:s') }}
                        </dd>
                    </div>
                </div>
            </div>
        </dl>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('kelola-bbm.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar
        </a>
    </div>
</div>
@endsection
