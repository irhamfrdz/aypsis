@extends('layouts.app')

@section('content')
<div class="space-y-4 max-w-2xl mx-auto">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Edit Tagihan Supir Vendor</h2>
            <p class="text-sm text-gray-500">Ubah nominal dan status pembayaran atau tambah keterangan</p>
        </div>
        <a href="{{ route('tagihan-supir-vendor.index') }}" class="flex items-center text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg px-4 py-2 hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-sm">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    @if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Terdapat error pada input Anda:</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('tagihan-supir-vendor.update', $tagihanSupirVendor->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-6">
                
                <!-- Info Section (Readonly) -->
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-200 border-dashed">Detail Rekod Tagihan #{{ $tagihanSupirVendor->id }}</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm mt-3">
                        <div>
                            <span class="text-gray-500 block mb-0.5">Nama Supir</span>
                            <span class="font-medium text-gray-900">{{ $tagihanSupirVendor->nama_supir }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block mb-0.5">No Surat Jalan</span>
                            <span class="font-medium text-gray-900">
                                @if($tagihanSupirVendor->surat_jalan_id && $tagihanSupirVendor->suratJalan)
                                    {{ $tagihanSupirVendor->suratJalan->no_surat_jalan }}
                                @else
                                    - Tidak Terhubung -
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500 block mb-0.5">Rute</span>
                            <span class="font-medium text-gray-900">{{ $tagihanSupirVendor->dari }} <span class="mx-1 text-gray-400">→</span> {{ $tagihanSupirVendor->ke }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block mb-0.5">Kontainer</span>
                            <span class="font-medium text-gray-900">{{ Str::upper($tagihanSupirVendor->jenis_kontainer ?? '-') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Input Section -->
                <div class="space-y-5">
                    
                    <div class="form-group">
                        <label for="nominal" class="block text-sm font-medium text-gray-700 mb-1.5">Nominal Tagihan<span class="text-red-500 ml-1">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 sm:text-sm font-medium pointer-events-none">Rp</span>
                            <input type="number" name="nominal" id="nominal" required
                                class="w-full pl-10 pr-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-shadow @error('nominal') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                value="{{ old('nominal', $tagihanSupirVendor->nominal) }}">
                        </div>
                        @error('nominal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran<span class="text-red-500 ml-1">*</span></label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <!-- Belum Dibayar -->
                            <div>
                                <input type="radio" name="status_pembayaran" id="status_belum_dibayar" value="belum_dibayar" class="peer hidden" {{ old('status_pembayaran', $tagihanSupirVendor->status_pembayaran) == 'belum_dibayar' ? 'checked' : '' }} required>
                                <label for="status_belum_dibayar" class="block cursor-pointer select-none rounded-lg border border-gray-200 p-3 text-center transition-all peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:ring-1 peer-checked:ring-red-500">
                                    <div class="text-sm font-medium text-gray-900 peer-checked:text-red-700">Belum Dibayar</div>
                                </label>
                            </div>
                            <!-- Sebagian -->
                            <div>
                                <input type="radio" name="status_pembayaran" id="status_sebagian" value="sebagian" class="peer hidden" {{ old('status_pembayaran', $tagihanSupirVendor->status_pembayaran) == 'sebagian' ? 'checked' : '' }} required>
                                <label for="status_sebagian" class="block cursor-pointer select-none rounded-lg border border-gray-200 p-3 text-center transition-all peer-checked:border-yellow-500 peer-checked:bg-yellow-50 peer-checked:ring-1 peer-checked:ring-yellow-500">
                                    <div class="text-sm font-medium text-gray-900 peer-checked:text-yellow-700">Sebagian</div>
                                </label>
                            </div>
                            <!-- Lunas -->
                            <div>
                                <input type="radio" name="status_pembayaran" id="status_lunas" value="lunas" class="peer hidden" {{ old('status_pembayaran', $tagihanSupirVendor->status_pembayaran) == 'lunas' ? 'checked' : '' }} required>
                                <label for="status_lunas" class="block cursor-pointer select-none rounded-lg border border-gray-200 p-3 text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:ring-1 peer-checked:ring-green-500">
                                    <div class="text-sm font-medium text-gray-900 peer-checked:text-green-700">Lunas</div>
                                </label>
                            </div>
                        </div>
                        @error('status_pembayaran')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan Tambahan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" 
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-shadow @error('keterangan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                            placeholder="Catatan tambahan tagihan... (opsional)">{{ old('keterangan', $tagihanSupirVendor->keterangan) }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end space-x-3">
                <a href="{{ route('tagihan-supir-vendor.index') }}" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm focus:bg-blue-700 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
            
        </form>
    </div>
</div>
@endsection
