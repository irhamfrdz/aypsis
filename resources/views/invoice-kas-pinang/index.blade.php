@extends('layouts.app')

@section('title', 'Invoice Kas Pinang')

@section('content')
<div class="p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Invoice Kas Pinang</h1>
            <p class="text-sm text-gray-500">Dokumen kas internal cabang Pinang yang terhubung dengan akun COA.</p>
        </div>
        @can('invoice-kas-pinang-create')
            <a href="{{ route('invoice-kas-pinang.create') }}" class="rounded bg-pink-600 px-4 py-2 text-sm font-medium text-white hover:bg-pink-700">Tambah Invoice</a>
        @endcan
    </div>

    @if(session('success'))
        <div class="mb-4 rounded border border-green-200 bg-green-50 p-3 text-green-800">{{ session('success') }}</div>
    @endif

    <form method="GET" class="mb-4 flex gap-2">
        <input type="search" name="search" value="{{ $search }}" placeholder="Cari nomor invoice, pihak terkait, atau referensi" class="w-full max-w-md rounded border-gray-300 text-sm">
        <button class="rounded bg-gray-800 px-4 py-2 text-sm text-white">Cari</button>
        @if($search !== '')
            <a href="{{ route('invoice-kas-pinang.index') }}" class="rounded border px-4 py-2 text-sm">Reset</a>
        @endif
    </form>

    <div class="overflow-x-auto rounded border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-gray-700">
                <tr>
                    <th class="px-4 py-3">Nomor Invoice</th><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Pihak Terkait</th>
                    <th class="px-4 py-3">Arus Kas</th><th class="px-4 py-3 text-right">Total</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($invoices as $invoice)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $invoice->nomor_invoice }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $invoice->tanggal_transaksi?->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $invoice->pihak_terkait ?: '-' }}</td>
                        <td class="px-4 py-3">Kas {{ ucfirst($invoice->arus_kas) }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">Rp {{ number_format((float) $invoice->total, 2, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ ucfirst($invoice->status) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('invoice-kas-pinang.print', $invoice) }}" target="_blank" class="mr-2 text-pink-700 hover:underline">Cetak</a>
                            @can('invoice-kas-pinang-update')
                                <a href="{{ route('invoice-kas-pinang.edit', $invoice) }}" class="mr-2 text-blue-700 hover:underline">Ubah</a>
                            @endcan
                            @can('invoice-kas-pinang-delete')
                                <form method="POST" action="{{ route('invoice-kas-pinang.destroy', $invoice) }}" class="inline" onsubmit="return confirm('Hapus invoice ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-700 hover:underline">Hapus</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada Invoice Kas Pinang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $invoices->links() }}</div>
</div>
@endsection
