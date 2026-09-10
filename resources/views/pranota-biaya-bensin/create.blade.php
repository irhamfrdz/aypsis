@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Buat Pranota Biaya Bensin</h1>
            <p class="text-gray-600 mt-1">Review data sebelum menyimpan Pranota</p>
        </div>
        <a href="{{ route('biaya-bensin.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 shadow-sm">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('pranota-biaya-bensin.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-800">Daftar Biaya Bensin Terpilih</h2>
                        <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-1 rounded-full">{{ $biayaBensins->count() }} Data</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kendaraan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supir</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Biaya</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($biayaBensins as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->tanggal->format('d/m/Y') }}
                                            <input type="hidden" name="biaya_bensin_ids[]" value="{{ $item->id }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($item->mobil_id)
                                                {{ $item->mobil->nomor_polisi ?? '-' }}
                                            @elseif($item->alat_berat_id)
                                                {{ $item->alatBerat->nama ?? '-' }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->supir ? ($item->supir->nama_panggilan ?: $item->supir->nama_lengkap) : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium text-right">
                                            Rp {{ number_format($item->biaya, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-gray-900 uppercase">Total Keseluruhan</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-amber-700 text-lg">
                                        Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pranota</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="tanggal_pranota" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pranota <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_pranota" id="tanggal_pranota" value="{{ old('tanggal_pranota', date('Y-m-d')) }}" required class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm px-3 py-2">
                        </div>

                        <div>
                            <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan (Opsional)</label>
                            <textarea name="catatan" id="catatan" rows="3" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm px-3 py-2" placeholder="Tulis catatan...">{{ old('catatan') }}</textarea>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-100">
                            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                <i class="fas fa-save mr-2 mt-0.5"></i> Simpan Pranota
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
