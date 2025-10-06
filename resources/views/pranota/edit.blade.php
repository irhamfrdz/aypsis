@extends('layouts.app')

@section('title', 'Edit Pranota')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
            <p class="font-bold">Sukses</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
            <p class="font-bold">Peringatan</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Pranota</h1>
            <p class="text-gray-600 mt-1">Form edit pranota kontainer sewa</p>
        </div>

        <form method="POST" action="{{ route('pranota-kontainer-sewa.update', $pranota) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Pranota</label>
                    <input type="text" name="no_invoice" value="{{ old('no_invoice', $pranota->no_invoice) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 cursor-not-allowed"
                           placeholder="Masukkan nomor pranota" readonly>
                    @error('no_invoice')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                    <input type="text" name="supplier" value="{{ old('supplier', $pranota->supplier) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Masukkan supplier">
                    @error('supplier')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">No. Invoice Vendor</label>
                    <input type="text" name="no_invoice_vendor" value="{{ old('no_invoice_vendor', $pranota->no_invoice_vendor) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Masukkan nomor invoice vendor">
                    @error('no_invoice_vendor')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Invoice Vendor</label>
                    <input type="date" name="tgl_invoice_vendor" value="{{ old('tgl_invoice_vendor', $pranota->tgl_invoice_vendor ? \Carbon\Carbon::parse($pranota->tgl_invoice_vendor)->format('Y-m-d') : '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('tgl_invoice_vendor')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Masukkan keterangan">{{ old('keterangan', $pranota->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Informasi Pranota -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pranota</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Pranota</label>
                            <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d M Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                            <p class="text-sm text-gray-900">Rp {{ number_format((float)$pranota->total_amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $pranota->status === 'paid' ? 'bg-green-100 text-green-800' :
                                   ($pranota->status === 'unpaid' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($pranota->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tagihan Items (hanya untuk informasi) -->
            @if($tagihanItems && $tagihanItems->count() > 0)
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tagihan Items ({{ $tagihanItems->count() }} item)</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kontainer</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($tagihanItems as $index => $item)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $item->nomor_kontainer }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $item->vendor ?? '-' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">Rp {{ number_format($item->grand_total, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('pranota-kontainer-sewa.show', $pranota) }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                    Batal
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                    Update Pranota
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
