@extends('layouts.app')

@section('title', 'Detail Invoice Vendor')
@section('page_title', 'Detail Invoice Vendor')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <a href="{{ route('vendor-invoice.index') }}" class="mr-4 text-white hover:text-teal-100 transition duration-150">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Detail Invoice</h1>
                            <p class="text-teal-100 text-sm">Informasi lengkap invoice {{ $vendorInvoice->no_invoice }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('vendor-invoice.edit', $vendorInvoice) }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-md transition duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Basic Info -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Status Dokumen</h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800">
                                RECORDED
                            </span>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Nomor Invoice</h3>
                            <p class="text-xl font-black text-gray-900 uppercase">{{ $vendorInvoice->no_invoice }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Vendor</h3>
                            <p class="text-lg font-bold text-teal-700">{{ $vendorInvoice->vendor->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Tanggal Invoice</h3>
                            <p class="text-lg font-medium text-gray-800">{{ $vendorInvoice->tgl_invoice->format('d F Y') }}</p>
                        </div>
                    </div>

                    <!-- Financial Summary -->
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 font-medium">Total DPP</span>
                            <span class="text-gray-900 font-bold">Rp {{ number_format($vendorInvoice->total_dpp, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 font-medium">Total PPN (+)</span>
                            <span class="text-gray-900 font-bold">Rp {{ number_format($vendorInvoice->total_ppn, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm border-b border-gray-200 pb-2">
                            <span class="text-gray-500 font-medium">Total PPh 23 (-)</span>
                            <span class="text-red-600 font-bold">(Rp {{ number_format($vendorInvoice->total_pph23, 2, ',', '.') }})</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 font-medium">Biaya Materai (+)</span>
                            <span class="text-gray-900 font-bold">Rp {{ number_format($vendorInvoice->total_materai, 2, ',', '.') }}</span>
                        </div>
                        <div class="pt-4 mt-4 border-t-2 border-dashed border-gray-300">
                            <span class="block text-xs font-black text-teal-800 uppercase tracking-wider mb-1 text-center">Grand Total Netto</span>
                            <p class="text-2xl font-black text-teal-600 text-center">Rp {{ number_format($vendorInvoice->total_netto, 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-gray-100 grid grid-cols-2 gap-4 text-center">
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Dibuat Pada</p>
                        <p class="text-xs text-gray-600">{{ $vendorInvoice->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Terakhir Diperbarui</p>
                        <p class="text-xs text-gray-600">{{ $vendorInvoice->updated_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
            </div>

            <div class="px-8 py-5 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                <form action="{{ route('vendor-invoice.destroy', $vendorInvoice) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus invoice ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-bold flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Record
                    </button>
                </form>
                <a href="{{ route('vendor-invoice.index') }}" class="text-teal-600 hover:text-teal-800 text-sm font-bold">
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
