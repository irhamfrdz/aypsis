@extends('layouts.app')

@section('title', 'Edit Pembayaran Pranota Lembur')
@section('page_title', 'Edit Pembayaran Pranota Lembur')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-6 max-w-4xl mx-auto">
    <form action="{{ route('pembayaran-pranota-lembur.update', $pembayaranPranotaLembur->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nomor Pembayaran</label>
                <input type="text" value="{{ $pembayaranPranotaLembur->nomor_pembayaran }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm" readonly>
            </div>
            <div>
                <label for="nomor_accurate" class="block text-sm font-medium text-gray-700">Nomor Accurate</label>
                <input type="text" name="nomor_accurate" id="nomor_accurate" value="{{ old('nomor_accurate', $pembayaranPranotaLembur->nomor_accurate) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="tanggal_pembayaran" class="block text-sm font-medium text-gray-700">Tanggal Pembayaran</label>
                <input type="date" name="tanggal_pembayaran" id="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', $pembayaranPranotaLembur->tanggal_pembayaran->toDateString()) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Total Pembayaran</label>
                <input type="text" value="Rp {{ number_format($pembayaranPranotaLembur->total_tagihan_setelah_penyesuaian, 0, ',', '.') }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm" readonly>
            </div>
        </div>

        <div>
            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
            <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('keterangan', $pembayaranPranotaLembur->keterangan) }}</textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('pembayaran-pranota-lembur.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
