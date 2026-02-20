@extends('layouts.app')

@section('title', 'Daftar Invoice Vendor')
@section('page_title', 'Daftar Invoice Vendor')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Invoice Vendor</h1>
                            <p class="text-teal-100 text-sm">Pencatatan invoice masuk untuk mencegah double payment</p>
                        </div>
                    </div>
                    <a href="{{ route('vendor-invoice.create') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Catat Invoice Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Filter & Search -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="p-6">
                <form action="{{ route('vendor-invoice.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-grow">
                        <label for="search" class="sr-only">Cari</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text"
                                   name="search"
                                   id="search"
                                   value="{{ request('search') }}"
                                   placeholder="Cari No. Invoice atau Vendor..."
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm">
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg transition duration-150">
                            Cari
                        </button>
                        <a href="{{ route('vendor-invoice.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition duration-150">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">List Invoice Vendor</h2>
                <span class="text-sm text-gray-500">Total: {{ $invoices->total() }} invoice</span>
            </div>

            @if($invoices->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 resizable-table" id="invoiceTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No. Invoice
                                    <div class="resize-handle"></div>
                                </th>
                                <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Vendor
                                    <div class="resize-handle"></div>
                                </th>
                                <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tgl Invoice
                                    <div class="resize-handle"></div>
                                </th>
                                <th class="resizable-th px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Netto
                                    <div class="resize-handle"></div>
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($invoices as $invoice)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-teal-600 uppercase">{{ $invoice->no_invoice }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium">{{ $invoice->vendor->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600">{{ $invoice->tgl_invoice->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-bold text-gray-900">Rp {{ number_format($invoice->total_netto, 2, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('vendor-invoice.show', $invoice) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1 rounded">Lihat</a>
                                            <a href="{{ route('vendor-invoice.edit', $invoice) }}" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 px-3 py-1 rounded">Edit</a>
                                            <form action="{{ route('vendor-invoice.destroy', $invoice) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus invoice ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination & Rows Per Page --}}
                <div class="px-6 py-4 border-t border-gray-200">
                    @include('components.modern-pagination', ['paginator' => $invoices, 'routeName' => 'vendor-invoice.index'])
                </div>
            @else
                <div class="text-center py-20">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada invoice</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai dengan mencatat invoice masuk dari vendor.</p>
                    <div class="mt-6">
                        <a href="{{ route('vendor-invoice.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700">
                            Tambah Invoice
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@include('components.resizable-table')

@push('scripts')
<script>
$(document).ready(function() {
    initResizableTable('invoiceTable');
});
</script>
@endpush
