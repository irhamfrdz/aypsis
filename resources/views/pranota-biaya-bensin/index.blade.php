@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-file-invoice-dollar mr-2 text-indigo-600"></i>
                Pranota Biaya Bensin
            </h1>
            <p class="text-gray-600 mt-1">Daftar Pranota (Invoice Internal) Biaya Bensin</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('biaya-bensin.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                <i class="fas fa-gas-pump mr-2"></i>
                Lihat Biaya Bensin
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center shadow-sm">
            <i class="fas fa-check-circle mr-3 text-green-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-center shadow-sm">
            <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pranota</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembuat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pranotas as $pranota)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                {{ $pranota->nomor_pranota }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $pranota->tanggal_pranota->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-amber-700 text-right">
                                Rp {{ number_format($pranota->total_biaya, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $pranota->status_badge }}">
                                    {{ $pranota->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $pranota->creator->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('pranota-biaya-bensin.show', $pranota->id) }}" class="p-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pranota-biaya-bensin.print', $pranota->id) }}" target="_blank" class="p-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors" title="Print Pranota">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    @if($pranota->status !== 'paid')
                                        <form action="{{ route('pranota-biaya-bensin.destroy', $pranota->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pranota ini? (Biaya bensin akan dikembalikan statusnya agar bisa dibuat pranota lagi)');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-file-invoice-dollar text-gray-300 text-5xl mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada Pranota</h3>
                                    <p class="text-gray-500 mb-4 text-sm">Pilih biaya bensin dari halaman Biaya Bensin untuk membuat pranota.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $pranotas->appends(request()->query())->links() }}
    </div>
</div>
@endsection
