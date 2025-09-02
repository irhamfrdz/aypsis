@extends('layouts.app')

@section('title', 'Edit Kontainer')
@section('page_title', 'Edit Kontainer')

@section('content')
    <h2 class="text-xl font-bold text-gray-800 mb-4">Form Edit Kontainer</h2>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('master.kontainer.update', $kontainer->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Menampilkan error validasi umum --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Oops!</strong>
                    <span class="block sm:inline">Ada beberapa masalah dengan input Anda.</span>
                    <ul class="mt-3 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
