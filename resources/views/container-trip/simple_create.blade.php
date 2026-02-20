 @extends('layouts.app')

@section('title', 'Input Kontainer Barddu')
@section('page_title', 'Form Input Data Kontainer')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 flex justify-between items-center">
                <h1 class="text-xl font-bold text-white">Input Kontainer Baru</h1>
                <a href="{{ route('container-trip.report.dashboard') }}" class="text-green-100 hover:text-white">&larr; Kembali</a>
            </div>
            
            <form action="{{ route('container-trip.report.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700">Pilih Vendor</label>
                    <select name="vendor_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" required>
                        <option value="">-- Pilih Vendor --</option>
                        @foreach($vendors as $v)
                            <option value="{{ $v->id }}">{{ $v->nama_vendor }} ({{ ucfirst($v->tipe_hitung) }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nomor Kontainer</label>
                    <input type="text" name="no_kontainer" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="Contoh: CONT-12345" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Ukuran</label>
                    <select name="ukuran" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value="20">20'</option>
                        <option value="40">40'</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Ambil</label>
                    <input type="date" name="tgl_ambil" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Harga Sewa Per Bulan (DPP)</label>
                    <div class="relative rounded-md shadow-sm mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="harga_sewa" class="focus:ring-green-500 focus:border-green-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2" placeholder="Contoh: 2000000" required>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Simpan Kontainer
                    </button>
                    <a href="{{ route('container-trip.report.dashboard') }}" class="block text-center mt-3 text-sm text-gray-500 hover:text-gray-800">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
