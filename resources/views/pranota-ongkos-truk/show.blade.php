@extends('layouts.app')

@section('title', 'Detail Pranota Ongkos Truk')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Notifications -->
        @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-xl shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm text-gray-500">
                        <li><a href="{{ route('pranota-ongkos-truk.index') }}" class="hover:text-gray-700">Pranota Ongkos Truk</a></li>
                        <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                        <li class="text-gray-900 font-medium">{{ $pranota->no_pranota }}</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-bold text-gray-900">{{ $pranota->no_pranota }}</h1>
            </div>
            <div class="flex space-x-3">
                <button onclick="window.print()" class="bg-white border border-gray-200 text-gray-700 px-5 py-2.5 rounded-xl hover:bg-gray-50 transition-all shadow-sm flex items-center font-medium">
                    <i class="fas fa-print mr-2"></i> Cetak
                </button>
                <form action="{{ route('pranota-ongkos-truk.destroy', $pranota->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pranota ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-50 text-red-600 px-5 py-2.5 rounded-xl hover:bg-red-100 transition-all flex items-center font-medium">
                        <i class="fas fa-trash-alt mr-2"></i> Hapus
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Summary Cards -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-sm font-medium text-gray-500 mb-1">Tanggal</p>
                <p class="text-lg font-bold text-gray-900">{{ $pranota->tanggal_pranota->format('d M Y') }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-sm font-medium text-gray-500 mb-1">Status</p>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $pranota->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                    {{ $pranota->status }}
                </span>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-sm font-medium text-gray-500 mb-1">Total Nominal</p>
                <p class="text-xl font-black text-blue-600">Rp {{ number_format($pranota->total_nominal, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Main Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="grid grid-cols-2 divide-x divide-gray-100 border-b border-gray-100">
                <div class="p-6">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Informasi Supir/Vendor</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-user-tie w-6 text-gray-400"></i>
                            <span class="text-gray-700">{{ $pranota->supir->nama_karyawan ?? 'Tidak Ada Supir' }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-building w-6 text-gray-400"></i>
                            <span class="text-gray-700">{{ $pranota->vendor->nama_vendor ?? 'Tidak Ada Vendor' }}</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Dibuat Oleh</h3>
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $pranota->creator->name ?? 'System' }}</p>
                            <p class="text-xs text-gray-500">{{ $pranota->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($pranota->keterangan)
            <div class="p-6 bg-gray-50/50">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Keterangan</h3>
                <p class="text-gray-700 italic">"{{ $pranota->keterangan }}"</p>
            </div>
            @endif
        </div>

        <!-- Items Table -->
        <h2 class="text-xl font-bold text-gray-900 mb-4">Rincian Item</h2>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 font-medium">No. Surat Jalan</th>
                        <th class="px-6 py-4 font-medium">Tanggal</th>
                        <th class="px-6 py-4 font-medium">Tipe</th>
                        <th class="px-6 py-4 font-medium text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pranota->items as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-gray-900">{{ $item->no_surat_jalan }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs">
                            <span class="px-2 py-1 rounded bg-gray-100 text-gray-600 font-medium capitalize">{{ str_replace('_', ' ', $item->type) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-gray-900">
                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-bold border-t-2 border-gray-100">
                    <tr>
                        <td colspan="3" class="px-6 py-5 text-right text-gray-600">Total Keseluruhan</td>
                        <td class="px-6 py-5 text-right text-blue-600 text-xl">
                            Rp {{ number_format($pranota->total_nominal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<style>
    @media print {
        header, .sidebar, .flex.space-x-3, nav, footer {
            display: none !important;
        }
        body {
            background: white;
            padding: 0;
        }
        .container {
            max-width: 100% !important;
            width: 100% !important;
        }
        .max-w-4xl {
            max-width: 100% !important;
        }
        .bg-white {
            box-shadow: none !important;
            border: none !important;
        }
    }
</style>
@endsection
