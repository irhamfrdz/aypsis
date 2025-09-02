@extends('layouts.app')

@section('title','Import Karyawan')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 max-w-xl mx-auto">
    <h2 class="text-xl font-semibold mb-4">Import Karyawan dari CSV</h2>
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-4">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-md mb-4">{{ session('warning') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-4">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('master.karyawan.import.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">File CSV</label>
            <input type="file" name="csv_file" accept=".csv,text/csv,.txt" class="mt-1 block w-full" required>
            <p class="text-xs text-gray-500 mt-1">Header CSV harus mengandung kolom seperti: nik,nama_lengkap,ktp,kk,jkn,no_ketenagakerjaan,... (urutan tidak wajib). Gunakan template di <code>resources/csv_templates/</code>.</p>
        </div>
        <div class="flex justify-end">
            <a href="{{ route('master.karyawan.index') }}" class="mr-2 px-4 py-2 bg-gray-200 rounded">Batal</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Upload</button>
        </div>
    </form>
</div>
@endsection
