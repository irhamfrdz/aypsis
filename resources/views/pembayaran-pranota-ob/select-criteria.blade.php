@extends('layouts.app')

@section('title', 'Pilih Kriteria Pembayaran Pranota OB')
@section('page_title', 'Pilih Kriteria Pembayaran Pranota OB')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 max-w-4xl mx-auto">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Filter Pranota OB</h3>
            <p class="text-sm text-gray-600">Pilih kriteria untuk memfilter pranota OB yang akan dibayar</p>
        </div>

        <form action="{{ route('pembayaran-pranota-ob.create') }}" method="GET" class="space-y-6">
            {{-- Pilih Kapal --}}
            <div>
                <label for="kapal" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih Kapal <span class="text-red-500">*</span>
                </label>
                <select name="kapal" id="kapal" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                    <option value="">-- Pilih Kapal --</option>
                    @foreach($kapalList as $kapal)
                        <option value="{{ $kapal }}" {{ request('kapal') == $kapal ? 'selected' : '' }}>
                            {{ $kapal }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Pilih kapal dari pranota OB yang tersedia</p>
            </div>

            {{-- Pilih Voyage --}}
            <div>
                <label for="voyage" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih Voyage <span class="text-red-500">*</span>
                </label>
                <select name="voyage" id="voyage" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                    <option value="">-- Pilih Voyage --</option>
                    @foreach($voyageList as $voyage)
                        <option value="{{ $voyage }}" {{ request('voyage') == $voyage ? 'selected' : '' }}>
                            {{ $voyage }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Pilih voyage dari pranota OB yang tersedia</p>
            </div>

            {{-- Pilih DP --}}
            <div>
                <label for="dp" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih DP <span class="text-red-500">*</span>
                </label>
                <select name="dp" id="dp" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                    <option value="">-- Pilih DP --</option>
                    @foreach($dpList as $dp)
                        <option value="{{ $dp->id }}" {{ request('dp') == $dp->id ? 'selected' : '' }}>
                            {{ $dp->nomor_pembayaran }} - {{ \Carbon\Carbon::parse($dp->tanggal_pembayaran)->format('d/m/Y') }} - Rp {{ number_format($dp->dp_amount, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Pilih DP yang sudah dibuat dari menu Pembayaran DP OB</p>
            </div>

            {{-- Info Box --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Informasi</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Setelah memilih kriteria, Anda akan diarahkan ke halaman form pembayaran dengan pranota OB yang telah difilter sesuai pilihan Anda.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                <a href="{{ route('pembayaran-pranota-ob.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Kembali
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Lanjutkan
                    <svg class="ml-2 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

    {{-- Script for dynamic filtering --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kapalSelect = document.getElementById('kapal');
            const voyageSelect = document.getElementById('voyage');

            // You can add dynamic filtering logic here if needed
            // For example, updating voyage options based on selected kapal
        });
    </script>
@endsection
