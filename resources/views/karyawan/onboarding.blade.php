@extends('layouts.app')

@section('title', 'Pendaftaran Karyawan Baru')
@section('page_title','Pendaftaran Karyawan Baru')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-4 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-6 lg:text-left">
            <h2 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">
                Formulir Pendaftaran Karyawan
            </h2>
            <p class="text-gray-600 text-sm lg:text-base">Lengkapi formulir di bawah untuk mendaftarkan diri sebagai karyawan.</p>
        </div>

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-lg mb-6 shadow-sm">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Gagal menambahkan data karyawan:</span>
                </div>
                <div class="text-sm">{{ session('error') }}</div>
            </div>
        @endif
        @if (count($errors) > 0)
            <div class="bg-red-50 border-l-4 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 shadow-sm">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Terdapat kesalahan dalam formulir:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach ((array) $errors as $error )
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            <form action="{{ route('karyawan.store') }}" method="POST" class="divide-y divide-gray-100">
            @csrf
            @php
                $inputClasses = "mt-1 block w-full rounded-xl border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 text-base p-3 lg:p-4 transition-all duration-200 min-h-[48px]";
                $readonlyInputClasses = "mt-1 block w-full rounded-xl border-gray-300 bg-gray-100 shadow-sm text-base p-3 lg:p-4 min-h-[48px]";
                $selectClasses = "mt-1 block w-full rounded-xl border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 text-base p-3 lg:p-4 transition-all duration-200 min-h-[48px]";
                $labelClasses = "block text-sm font-semibold text-gray-700 mb-2";
                $fieldsetClasses = "p-6 lg:p-8 space-y-6";
                $legendClasses = "text-lg lg:text-xl font-bold text-gray-800 mb-6 flex items-center";
            @endphp
            @include('master-karyawan._form-fields', compact('inputClasses','readonlyInputClasses','selectClasses','labelClasses','fieldsetClasses','legendClasses'))
            <div class="bg-gray-50 px-6 py-6 lg:px-8 lg:py-8">
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl border-2 border-transparent bg-gradient-to-r from-blue-600 to-indigo-600 py-3 px-6 text-base font-semibold text-white shadow-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 min-h-[48px]">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Data Karyawan
                    </button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection
