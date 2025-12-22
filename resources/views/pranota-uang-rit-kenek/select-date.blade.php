@extends('layouts.app')

@section('title', 'Pilihan Tanggal - Pranota Uang Rit Kenek')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h1 class="text-xl font-semibold text-gray-900 mb-2">Pilih Rentang Tanggal</h1>
            <p class="text-sm text-gray-600 mb-4">Silakan pilih rentang tanggal untuk menampilkan daftar Pranota Uang Rit Kenek berdasarkan <strong>tanggal tanda terima</strong>.</p>

            <form method="GET" action="{{ route('pranota-uang-rit-kenek.create') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $start_date ?? now()->subDays(30)->format('Y-m-d')) }}" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $end_date ?? now()->format('Y-m-d')) }}" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="sm:col-span-2 flex gap-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Tampilkan</button>
                    <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Batal</a>
                </div>
            </form>
            @if(session('error'))
            <div class="mt-4 bg-red-50 border border-red-200 text-red-700 rounded p-3 text-sm">
                {{ session('error') }}
            </div>
            @endif
            @if(app()->isLocal())
            <div class="mt-4 text-xs text-gray-600">
                <p><strong>Debug:</strong> route('pranota-uang-rit-kenek.create') =&gt; <code>{{ route('pranota-uang-rit-kenek.create') }}</code></p>
                <p>Try opening the above link directly to verify the route exists.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
