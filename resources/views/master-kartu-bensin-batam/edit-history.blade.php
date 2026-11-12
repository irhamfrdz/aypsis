@extends('layouts.app')

@section('title', 'Edit Riwayat Kartu Bensin Batam')
@section('page_title', 'Edit Riwayat Kartu Bensin')

@section('content')
<div class="container mx-auto max-w-2xl px-4 py-6">
    <a href="{{ route('master-kartu-bensin-batam.history', $card->id) }}" class="mb-4 inline-block text-sm text-blue-600 hover:text-blue-800">&larr; Kembali ke riwayat</a>
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="mb-1 text-xl font-bold text-gray-800">Edit Riwayat {{ $card->nama_kartu }}</h2>
        <p class="mb-6 text-sm text-gray-600">Perubahan nominal akan menyesuaikan saldo transaksi berikutnya dan saldo kartu.</p>
        <form action="{{ route('master-kartu-bensin-batam.history.update', [$card->id, $log->id]) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label for="tanggal" class="mb-1 block text-sm font-medium text-gray-700">Tanggal dan waktu</label>
                <input type="datetime-local" id="tanggal" name="tanggal" value="{{ old('tanggal', $log->tanggal?->format('Y-m-d\TH:i')) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2">
                @error('tanggal')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="nominal" class="mb-1 block text-sm font-medium text-gray-700">Nominal</label>
                <input type="number" id="nominal" name="nominal" min="0.01" step="0.01" value="{{ old('nominal', $log->nominal) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2">
                @error('nominal')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="keterangan" class="mb-1 block text-sm font-medium text-gray-700">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="4" maxlength="1000" class="w-full rounded-lg border border-gray-300 px-3 py-2">{{ old('keterangan', $log->keterangan) }}</textarea>
                @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('master-kartu-bensin-batam.history', $card->id) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Batal</a>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
