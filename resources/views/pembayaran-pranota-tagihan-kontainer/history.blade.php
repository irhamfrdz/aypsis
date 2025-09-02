@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Riwayat Pembayaran Pranota</h1>
        @if(Route::has('pembayaran-pranota-tagihan-kontainer.index'))
            <a href="{{ route('pembayaran-pranota-tagihan-kontainer.index') }}" class="inline-block bg-gray-200 text-gray-800 px-3 py-2 rounded">Kembali</a>
        @endif
    </div>

    <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm md:text-base font-sans leading-relaxed">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">#</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Nomor Pembayaran</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal Kas</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Bank</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Total (Rp)</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Keterangan</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Tagihan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($payments as $i => $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ is_object($payments) && method_exists($payments, 'firstItem') ? $payments->firstItem() + $i : ($i + 1) }}</td>
                    <td class="px-4 py-3">{{ $p->nomor_pembayaran }}</td>
                    <td class="px-4 py-3">{{ optional(\Carbon\Carbon::parse($p->tanggal_kas))->format('Y-m-d') }}</td>
                    <td class="px-4 py-3">{{ $p->bank }}</td>
                    <td class="px-4 py-3 text-right">{{ number_format($p->total_pembayaran,2,',','.') }}</td>
                    <td class="px-4 py-3">{{ $p->keterangan }}</td>
                    <td class="px-4 py-3">
                        @if($p->tagihans && $p->tagihans->count())
                            <ul class="list-disc pl-4 text-sm">
                                @foreach($p->tagihans as $t)
                                    <li>{{ $t->nomor_pranota ?? $t->group_code ?? ('#' . $t->id) }} — Rp {{ number_format($t->harga,2,',','.') }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-xs text-gray-500">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada pembayaran.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        @if(is_object($payments) && method_exists($payments, 'links'))
            {{ $payments->links() }}
        @endif
    </div>
</div>
@endsection
