@extends('layouts.app')

@section('title', 'Detail Invoice Kanisir Ban')
@section('page_title', 'Detail Invoice Kanisir Ban')

@section('content')
<div class="flex flex-col h-full bg-gray-100">
    <div class="flex-1 overflow-hidden">
        <div class="h-full flex flex-col">
            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 px-4 py-6 sm:px-6 lg:px-8">
                <!-- Back Button -->
                <div class="mb-6">
                    <a href="{{ route('invoice-kanisir-ban.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke List Invoice
                    </a>
                </div>

                <!-- Invoice Header Info -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $invoice->nomor_invoice }}</h2>
                            <p class="text-sm text-gray-500">Tanggal: {{ date('d F Y', strtotime($invoice->tanggal_invoice)) }}</p>
                        </div>
                        <div class="text-right">
                             <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <span class="text-xs font-medium text-gray-500 uppercase block mb-1">Vendor</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $invoice->vendor }}</span>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <span class="text-xs font-medium text-gray-500 uppercase block mb-1">Jumlah Ban</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $invoice->jumlah_ban }}</span>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <span class="text-xs font-medium text-gray-500 uppercase block mb-1">Total Biaya</span>
                            <span class="text-lg font-semibold text-green-600">Rp {{ number_format($invoice->total_biaya, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    @if($invoice->keterangan)
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-900">Keterangan</h4>
                        <p class="mt-1 text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">{{ $invoice->keterangan }}</p>
                    </div>
                    @endif
                </div>

                <!-- Items Table -->
                <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Daftar Ban Dimasak</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                        No
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nomor Seri / ID Ban
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Detail Ban
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Biaya Masak
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($invoice->items as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        @if($item->stockBan)
                                            <a href="{{ route('stock-ban.show', $item->stockBan->id) }}" class="text-blue-600 hover:underline">
                                                {{ $item->stockBan->nomor_seri ?: 'ID: ' . $item->stockBan->id }}
                                            </a>
                                        @else
                                            <span class="text-red-500 italic">Ban terhapus</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        @if($item->stockBan)
                                            <div class="flex flex-col">
                                                <span class="font-medium text-gray-900">{{ $item->stockBan->namaStockBan->nama ?? '-' }}</span>
                                                <span class="text-xs text-gray-500">Merk: {{ $item->stockBan->merk ?? '-' }} | Ukuran: {{ $item->stockBan->ukuran ?? '-' }}</span>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-900">Total</td>
                                    <td class="px-6 py-4 text-right font-bold text-green-600">
                                        Rp {{ number_format($invoice->total_biaya, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
