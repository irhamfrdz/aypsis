@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-xl font-semibold text-gray-800">Tambah Pembayaran Aktivitas Lain</h1>
            <a href="{{ route('pembayaran-aktivitas-lain.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium text-xs rounded-md transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        <form action="{{ route('pembayaran-aktivitas-lain.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nomor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor</label>
                    <input type="text" value="{{ $nomor }}" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm">
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('tanggal') border-red-500 @enderror">
                    @error('tanggal')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Aktivitas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Aktivitas <span class="text-red-500">*</span></label>
                    <select name="jenis_aktivitas" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('jenis_aktivitas') border-red-500 @enderror">
                        <option value="">Pilih Jenis Aktivitas</option>
                        <option value="Pembayaran KIR" {{ old('jenis_aktivitas') == 'Pembayaran KIR' ? 'selected' : '' }}>Pembayaran KIR</option>
                        <option value="Pembayaran Pajak dan STNK" {{ old('jenis_aktivitas') == 'Pembayaran Pajak dan STNK' ? 'selected' : '' }}>Pembayaran Pajak dan STNK</option>
                        <option value="Pembayaran Asuransi" {{ old('jenis_aktivitas') == 'Pembayaran Asuransi' ? 'selected' : '' }}>Pembayaran Asuransi</option>
                        <option value="Pembayaran Lain Lain" {{ old('jenis_aktivitas') == 'Pembayaran Lain Lain' ? 'selected' : '' }}>Pembayaran Lain Lain</option>
                    </select>
                    @error('jenis_aktivitas')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jumlah -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah" value="{{ old('jumlah') }}" required min="0" step="0.01" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('jumlah') border-red-500 @enderror">
                    @error('jumlah')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Metode Pembayaran -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran <span class="text-red-500">*</span></label>
                    <select name="metode_pembayaran" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('metode_pembayaran') border-red-500 @enderror">
                        <option value="">Pilih Metode</option>
                        <option value="cash" {{ old('metode_pembayaran') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="cek" {{ old('metode_pembayaran') == 'cek' ? 'selected' : '' }}>Cek</option>
                        <option value="giro" {{ old('metode_pembayaran') == 'giro' ? 'selected' : '' }}>Giro</option>
                    </select>
                    @error('metode_pembayaran')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Debit/Kredit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Debit/Kredit <span class="text-red-500">*</span></label>
                    <select name="debit_kredit" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('debit_kredit') border-red-500 @enderror">
                        <option value="">Pilih Debit/Kredit</option>
                        <option value="debit" {{ old('debit_kredit') == 'debit' ? 'selected' : '' }}>Debit</option>
                        <option value="kredit" {{ old('debit_kredit') == 'kredit' ? 'selected' : '' }}>Kredit</option>
                    </select>
                    @error('debit_kredit')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Akun Biaya -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Akun Biaya <span class="text-red-500">*</span></label>
                    <select name="akun_coa_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('akun_coa_id') border-red-500 @enderror">
                        <option value="">Pilih Akun Biaya</option>
                        @foreach($akunBiaya as $akun)
                            <option value="{{ $akun->id }}" {{ old('akun_coa_id') == $akun->id ? 'selected' : '' }}>
                                {{ $akun->kode_nomor }} - {{ $akun->nama_akun }}
                            </option>
                        @endforeach
                    </select>
                    @error('akun_coa_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="4" placeholder="Keterangan tambahan..." class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('pembayaran-aktivitas-lain.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium text-sm rounded-md transition">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-md transition">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
