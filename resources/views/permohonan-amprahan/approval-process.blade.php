@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <a href="{{ route('approval-permohonan-amprahan.index') }}" class="text-gray-400 hover:text-blue-600 transition-colors mr-3" title="Kembali">
                    <i class="fas fa-arrow-left"></i>
                </a>
                Proses Persetujuan Amprahan
            </h1>
            <p class="text-gray-600 mt-1 ml-10">Pilih item barang yang akan disetujui atau ditolak</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Info Panel -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">Informasi Permohonan</h3>
                
                <div class="space-y-4">
                    <div>
                        <div class="text-xs text-gray-500 uppercase font-semibold">Kapal</div>
                        <div class="font-medium text-gray-900 mt-1">{{ $permohonan->kapal->nama_kapal ?? '-' }}</div>
                    </div>
                    
                    <div>
                        <div class="text-xs text-gray-500 uppercase font-semibold">Nomor Voyage</div>
                        <div class="font-medium text-gray-900 mt-1">{{ $permohonan->nomor_voyage }}</div>
                    </div>
                    
                    <div>
                        <div class="text-xs text-gray-500 uppercase font-semibold">Tanggal Request</div>
                        <div class="font-medium text-gray-900 mt-1">{{ $permohonan->created_at->format('d F Y, H:i') }} WIB</div>
                    </div>
                    
                    <div>
                        <div class="text-xs text-gray-500 uppercase font-semibold">Pemohon</div>
                        <div class="font-medium text-gray-900 mt-1">{{ $permohonan->user->name ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500 uppercase font-semibold">Status</div>
                        <div class="mt-1">
                            @if($permohonan->status == 'pending')
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @elseif($permohonan->status == 'approved')
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Disetujui
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst($permohonan->status) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($permohonan->keterangan_umum)
                    <div class="pt-2">
                        <div class="text-xs text-gray-500 uppercase font-semibold">Keterangan Umum</div>
                        <div class="font-medium text-gray-900 mt-1 bg-gray-50 p-3 rounded-lg border border-gray-100 italic">
                            {{ $permohonan->keterangan_umum }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Panel -->
        <div class="lg:col-span-2">
            <form action="{{ route('approval-permohonan-amprahan.process', $permohonan->id) }}" method="POST">
                @csrf
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                    <div class="p-6 border-b flex justify-between items-center bg-gray-50">
                        <h3 class="text-lg font-bold text-gray-900">Daftar Barang ({{ $permohonan->items->count() }} Item)</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-white">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">No</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Barang</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Satuan</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Persetujuan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($permohonan->items as $index => $item)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->nama_barang }}</div>
                                            @if($item->keterangan)
                                                <div class="text-xs text-gray-500 mt-1">{{ $item->keterangan }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                            {{ rtrim(rtrim(number_format($item->jumlah, 2, ',', '.'), '0'), ',') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                            {{ $item->satuan }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-4">
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="radio" name="items[{{ $item->id }}]" value="approved" class="form-radio h-4 w-4 text-green-600" {{ $item->status == 'approved' || $item->status == 'pending' ? 'checked' : '' }}>
                                                    <span class="ml-2 text-sm text-green-700 font-medium"><i class="fas fa-check mr-1"></i> Approve</span>
                                                </label>
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="radio" name="items[{{ $item->id }}]" value="rejected" class="form-radio h-4 w-4 text-red-600" {{ $item->status == 'rejected' ? 'checked' : '' }}>
                                                    <span class="ml-2 text-sm text-red-700 font-medium"><i class="fas fa-times mr-1"></i> Reject</span>
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 font-medium text-sm">
                                            Tidak ada item barang dalam permintaan ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('approval-permohonan-amprahan.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm transition-colors flex items-center">
                        <i class="fas fa-save mr-2"></i> Simpan Persetujuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
