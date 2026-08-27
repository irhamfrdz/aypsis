@extends('layouts.app')

@section('title', 'Edit Template WA')
@section('page_title', 'Edit Template WhatsApp')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 font-sans max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">Edit Template WA</h2>
        <a href="{{ route('master.wa-templates.index') }}" class="text-gray-600 hover:text-gray-900 font-medium flex items-center text-sm">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Ada {{ $errors->count() }} kesalahan:</h3>
                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('master.wa-templates.update', $wa_template) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-6">
            <div>
                <label for="nama_template" class="block text-sm font-semibold text-gray-700 mb-1">Nama Template</label>
                <input type="text" name="nama_template" id="nama_template" value="{{ old('nama_template', $wa_template->nama_template) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>

            <div>
                <label for="isi_template" class="block text-sm font-semibold text-gray-700 mb-1">Isi Template</label>
                <p class="text-xs text-gray-500 mb-2">Gunakan placeholder berikut yang akan diganti otomatis oleh sistem: <br>
                    <code class="bg-gray-100 px-1 rounded">{shipper_name}</code>, <code class="bg-gray-100 px-1 rounded">{nama_kapal}</code>, <code class="bg-gray-100 px-1 rounded">{no_voyage}</code>, <code class="bg-gray-100 px-1 rounded">{kategori_masalah}</code>, <code class="bg-gray-100 px-1 rounded">{deskripsi_masalah}</code>, <code class="bg-gray-100 px-1 rounded">{estimasi_keterlambatan}</code>, <code class="bg-gray-100 px-1 rounded">{daftar_resi}</code>
                </p>
                <textarea name="isi_template" id="isi_template" rows="10" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-mono">{{ old('isi_template', $wa_template->isi_template) }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $wa_template->is_active) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktif</label>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300 shadow-sm text-sm">
                Perbarui
            </button>
        </div>
    </form>
</div>
@endsection
