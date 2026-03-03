@extends('layouts.app')

@section('title', 'Edit Dokumen Kapal')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center rounded-t-lg">
                <h2 class="text-lg font-bold text-gray-800">Edit Dokumen Kapal</h2>
                <a href="{{ route('master-dokumen-kapal-alexindo.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium pb-1.5 border-b-2 border-transparent hover:border-gray-500 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>

            <div class="px-6 py-4">
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <ul class="list-disc pl-5 text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('master-dokumen-kapal-alexindo.update', $dokumen->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kapal <span class="text-red-500">*</span></label>
                            <select name="kapal_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                <option value="">-- Pilih Kapal --</option>
                                @foreach($kapals as $kapal)
                                    <option value="{{ $kapal->id }}" {{ (old('kapal_id') ?? $dokumen->kapal_id) == $kapal->id ? 'selected' : '' }}>
                                        {{ $kapal->nama_kapal }} {{ $kapal->nickname ? '('.$kapal->nickname.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Dokumen <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_dokumen" value="{{ old('nama_dokumen') ?? $dokumen->nama_dokumen }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Dokumen</label>
                            <input type="text" name="nomor_dokumen" value="{{ old('nomor_dokumen') ?? $dokumen->nomor_dokumen }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Terbit</label>
                            <input type="date" name="tanggal_terbit" value="{{ old('tanggal_terbit') ?? $dokumen->tanggal_terbit }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kadaluarsa</label>
                            <input type="date" name="tanggal_berakhir" value="{{ old('tanggal_berakhir') ?? $dokumen->tanggal_berakhir }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">File Dokumen</label>
                            @if($dokumen->file_dokumen)
                                <div class="mb-2">
                                    <a href="{{ asset($dokumen->file_dokumen) }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 underline">
                                        <i class="fas fa-file-pdf mr-1"></i> Lihat File Saat Ini
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="file_dokumen" accept=".pdf,.png,.jpg,.jpeg" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah file saat ini. Maksimal 5MB. Format: PDF, JPG, PNG.</p>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                            <textarea name="keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('keterangan') ?? $dokumen->keterangan }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-gray-200 mt-6 pt-4 gap-2">
                        <a href="{{ route('master-dokumen-kapal-alexindo.index') }}" class="bg-white hover:bg-gray-50 text-gray-700 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium transition duration-150">Batal</a>
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium transition duration-150">Update Dokumen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
