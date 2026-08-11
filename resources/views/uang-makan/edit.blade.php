@extends('layouts.app')

@section('title', 'Edit Data Uang Makan')
@section('page_title', 'Edit Data Uang Makan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Edit Data Uang Makan</h2>
            </div>
            
            <div class="p-6">
                <form action="{{ route('uang-makan.update', $uangMakan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <div>
                            <label for="karyawan_id" class="block text-sm font-medium text-gray-700">Karyawan</label>
                            <select id="karyawan_id" name="karyawan_id" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Pilih Karyawan</option>
                                <optgroup label="Karyawan">
                                @foreach($karyawans as $karyawan)
                                    <option value="App\Models\Karyawan-{{ $karyawan->id }}" {{ (old('karyawan_id', $uangMakan->tipe_karyawan.'-'.$uangMakan->karyawan_id) == 'App\Models\Karyawan-'.$karyawan->id) ? 'selected' : '' }}>{{ $karyawan->nama_lengkap }} ({{ $karyawan->nik }})</option>
                                @endforeach
                                </optgroup>
                                <optgroup label="Non Karyawan">
                                @foreach($nonKaryawans as $nonKaryawan)
                                    <option value="App\Models\KaryawanTidakTetap-{{ $nonKaryawan->id }}" {{ (old('karyawan_id', $uangMakan->tipe_karyawan.'-'.$uangMakan->karyawan_id) == 'App\Models\KaryawanTidakTetap-'.$nonKaryawan->id) ? 'selected' : '' }}>{{ $nonKaryawan->nama_lengkap }} ({{ $nonKaryawan->nik }})</option>
                                @endforeach
                                </optgroup>
                            </select>
                            @error('karyawan_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                                <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $uangMakan->tanggal->format('Y-m-d')) }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('tanggal')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="nominal" class="block text-sm font-medium text-gray-700">Nominal (Rp)</label>
                                <input type="number" name="nominal" id="nominal" value="{{ old('nominal', intval($uangMakan->nominal)) }}" required min="0" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('nominal')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                            <textarea id="keterangan" name="keterangan" rows="3" class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('keterangan', $uangMakan->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('uang-makan.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
