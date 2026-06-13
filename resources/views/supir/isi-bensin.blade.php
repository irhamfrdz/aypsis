@extends('layouts.supir')

@section('title', 'Isi Bensin - AYPSIS')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl mx-auto">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif
        
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    <span class="font-bold">Terdapat kesalahan:</span>
                </div>
                <ul class="list-disc list-inside ml-6 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Header Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center border border-indigo-100">
                        <i class="fas fa-gas-pump text-indigo-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-900 tracking-tight">Isi Bensin</h2>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-0.5">Catat pengisian bensin unit Anda</p>
                    </div>
                </div>
                <a href="{{ route('supir.dashboard') }}" class="p-2 bg-gray-50 hover:bg-gray-100 rounded-lg text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>

        <!-- Form Container -->
        <div class="bg-white rounded-3xl shadow-xl shadow-gray-100 border border-gray-200 overflow-hidden">
            <div class="p-8">
                <form action="{{ route('supir.isi-bensin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-4">
                        <!-- Tanggal -->
                        <div>
                            <label for="tanggal" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                Tanggal Pengisian <span class="text-red-500 font-bold">*</span>
                            </label>
                            <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                                   class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 text-gray-900 font-bold text-sm transition-all">
                        </div>

                        <!-- Kendaraan / Mobil -->
                        <div>
                            <label for="mobil_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                Kendaraan / Mobil <span class="text-red-500 font-bold">*</span>
                            </label>
                            <div class="relative">
                                <select name="mobil_id" id="mobil_id" required 
                                        class="appearance-none w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 text-gray-900 font-bold text-sm transition-all">
                                    <option value="">--Pilih Mobil--</option>
                                    @foreach($mobils as $m)
                                        <option value="{{ $m->id }}" {{ (old('mobil_id') == $m->id || ($mobil && $mobil->id == $m->id)) ? 'selected' : '' }}>
                                            {{ $m->nomor_polisi ?: '-' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-5 pointer-events-none text-gray-400">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>

                        <!-- KM Awal & Akhir -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="km_awal" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                    KM Awal
                                </label>
                                <input type="number" name="km_awal" id="km_awal" value="{{ old('km_awal') }}" placeholder="0"
                                       class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 text-gray-900 font-bold text-sm transition-all">
                            </div>
                            <div>
                                <label for="km_akhir" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                    KM Akhir
                                </label>
                                <input type="number" name="km_akhir" id="km_akhir" value="{{ old('km_akhir') }}" placeholder="0"
                                       class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 text-gray-900 font-bold text-sm transition-all">
                            </div>
                        </div>

                        <!-- Volume (L) & Biaya -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="liter" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                    Volume (Liter) <span class="text-red-500 font-bold">*</span>
                                </label>
                                <input type="number" step="0.01" name="liter" id="liter" value="{{ old('liter') }}" required placeholder="0.00"
                                       class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 text-gray-900 font-bold text-sm transition-all">
                            </div>
                            <div>
                                <label for="biaya" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                    Total Biaya (Rp) <span class="text-red-500 font-bold">*</span>
                                </label>
                                <input type="number" name="biaya" id="biaya" value="{{ old('biaya') }}" required placeholder="0"
                                       class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 text-gray-900 font-bold text-sm transition-all">
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label for="keterangan" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                Keterangan
                            </label>
                            <textarea name="keterangan" id="keterangan" rows="3" placeholder="Opsional..."
                                      class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 text-gray-900 font-medium text-sm transition-all resize-none">{{ old('keterangan') }}</textarea>
                        </div>

                        <!-- Bukti Pembelian -->
                        <div>
                            <label for="bukti_beli" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                Lampirkan Bukti Pembelian (Foto / PDF)
                            </label>
                            <input type="file" name="bukti_beli" id="bukti_beli" accept="image/*,application/pdf"
                                   class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 text-gray-900 font-medium text-sm transition-all">
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mt-1.5 ml-1">
                                Format: JPG, JPEG, PNG, atau PDF. Maksimal 10 MB.
                            </p>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full py-5 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 hover:shadow-2xl hover:bg-indigo-700 transition-all duration-300 flex items-center justify-center uppercase tracking-widest text-xs">
                            <i class="fas fa-save mr-3"></i> Simpan Catatan Bensin
                        </button>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 p-6 border-t border-gray-100">
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center border border-gray-200 mr-4 shrink-0">
                        <i class="fas fa-info-circle text-indigo-500 text-sm"></i>
                    </div>
                    <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest leading-relaxed">
                        Data pengisian bensin yang Anda masukkan akan langsung diverifikasi oleh admin operasional fleet management.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
