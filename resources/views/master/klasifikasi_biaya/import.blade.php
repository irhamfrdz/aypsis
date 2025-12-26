@extends('layouts.app')

@section('title','Import Master Klasifikasi Biaya')
@section('page_title','Import Master Klasifikasi Biaya')

@section('content')
<div class="p-6 bg-white rounded shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-semibold">Import dari CSV</h3>
        <div class="space-x-2">
            <a href="{{ route('klasifikasi-biaya.download-template') }}" class="px-3 py-2 bg-gray-200 rounded text-sm">Download Template</a>
            <a href="{{ route('klasifikasi-biaya.index') }}" class="px-3 py-2 bg-white border rounded text-sm">Kembali</a>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('klasifikasi-biaya.import') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Pilih file Excel (.xlsx, .xls) atau CSV (delimiter: ; )</label>
            <input type="file" name="csv_file" accept=".xlsx,.xls,.csv" class="mt-1" required>
            <p class="text-xs text-gray-500 mt-2">Format header/kolom (urutan yang disarankan): kode, nama, deskripsi, is_active (active/inactive). Hanya kolom <strong>nama</strong> yang wajib. Jika kolom <strong>kode</strong> dikosongkan akan dibuat otomatis; jika <strong>is_active</strong> dikosongkan maka default <strong>active</strong>.</p>
        </div>
        <div>
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Upload & Import</button>
        </div>
    </form>

    @if(session('import_errors'))
        <div class="mt-4 bg-red-50 border border-red-200 p-4 rounded">
            <h4 class="font-semibold text-red-700">Beberapa baris gagal diimport:</h4>
            <ul class="list-disc pl-6 text-sm text-red-700 mt-2">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('import_duplicates'))
        <div class="mt-4 bg-yellow-50 border border-yellow-200 p-4 rounded">
            <h4 class="font-semibold text-yellow-700">Beberapa baris dilewati karena duplikat:</h4>
            <ul class="list-disc pl-6 text-sm text-yellow-700 mt-2">
                @foreach(session('import_duplicates') as $dup)
                    <li>{{ $dup }}</li>
                @endforeach
            </ul>
        </div>
    @endif

</div>
@endsection
