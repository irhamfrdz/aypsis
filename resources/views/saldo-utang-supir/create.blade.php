@extends('layouts.app')

@section('title', 'Tambah Transaksi Utang Supir - AYPSIS')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <!-- Back Button & Title -->
    <div class="flex items-center space-x-4 mb-8">
        <a href="{{ route('saldo-utang-supir.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-gray-900 shadow-sm transition-all duration-200">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Tambah Transaksi Utang</h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Catat pinjaman baru atau pelunasan manual untuk supir</p>
        </div>
    </div>

    <!-- Alert Error -->
    @if(session('error'))
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl flex items-center">
            <div class="w-8 h-8 bg-rose-100 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <span class="font-bold text-sm">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden p-8">
        <form action="{{ route('saldo-utang-supir.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <!-- Driver Selector -->
                <div>
                    <label for="karyawan_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pilih Supir</label>
                    <select name="karyawan_id" id="karyawan_id" class="block w-full text-xs bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 @error('karyawan_id') border-rose-300 @enderror">
                        <option value="">-- Pilih Supir --</option>
                        @foreach($supirs as $supir)
                            <option value="{{ $supir->id }}" {{ old('karyawan_id') == $supir->id ? 'selected' : '' }}>
                                {{ strtoupper($supir->nama_lengkap) }} ({{ $supir->nik ?? 'TANPA NIK' }})
                            </option>
                        @endforeach
                    </select>
                    @error('karyawan_id')
                        <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Date -->
                <div>
                    <label for="tanggal" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Transaksi</label>
                    <input type="date" 
                           name="tanggal" 
                           id="tanggal" 
                           value="{{ old('tanggal', date('Y-m-d')) }}"
                           class="block w-full text-xs bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal') border-rose-300 @enderror">
                    @error('tanggal')
                        <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="tipe" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tipe Transaksi</label>
                    <select name="tipe" id="tipe" class="block w-full text-xs bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 @error('tipe') border-rose-300 @enderror">
                        <option value="penambahan" {{ old('tipe') == 'penambahan' ? 'selected' : '' }}>Penambahan (Pinjaman Baru / Kasbon)</option>
                        <option value="pengurangan" {{ old('tipe') == 'pengurangan' ? 'selected' : '' }}>Pengurangan (Pembayaran Manual / Pemotongan)</option>
                    </select>
                    @error('tipe')
                        <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Nominal -->
                <div>
                    <label for="nominal" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nominal (Rupiah)</label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-xs font-bold">Rp</span>
                        </div>
                        <input type="number" 
                               name="nominal" 
                               id="nominal" 
                               value="{{ old('nominal') }}"
                               placeholder="Contoh: 150000"
                               min="1"
                               step="any"
                               class="block w-full pl-9 pr-3 py-2.5 text-xs text-gray-900 bg-gray-50 border border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 @error('nominal') border-rose-300 @enderror">
                    </div>
                    @error('nominal')
                        <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Reference -->
                <div>
                    <label for="referensi" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Referensi / Judul</label>
                    <input type="text" 
                           name="referensi" 
                           id="referensi" 
                           value="{{ old('referensi') }}"
                           placeholder="Contoh: Pinjaman Baru, Bayar Cash, dll."
                           class="block w-full text-xs bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 @error('referensi') border-rose-300 @enderror">
                    @error('referensi')
                        <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Note -->
                <div>
                    <label for="keterangan" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Keterangan Tambahan (Opsional)</label>
                    <textarea name="keterangan" 
                              id="keterangan" 
                              rows="3" 
                              placeholder="Catatan pelengkap transaksi..."
                              class="block w-full text-xs bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end space-x-2">
                <a href="{{ route('saldo-utang-supir.index') }}" class="px-5 py-3 border border-gray-200 text-gray-700 font-bold rounded-xl text-xs uppercase tracking-wider hover:bg-gray-50 transition-colors duration-200">
                    Batal
                </a>
                <button type="submit" class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-100 transition-all duration-200 text-xs uppercase tracking-wider">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
