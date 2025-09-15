@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Pranota Tagihan Kontainer</h1>
        @if(auth()->check() && auth()->user()->can('tagihan-kontainer.create'))
            @if(Route::has('pembayaran-pranota-tagihan-kontainer.create'))
                <a href="{{ route('pembayaran-pranota-tagihan-kontainer.create') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">Buat Pembayaran</a>
            @endif
        @endif
    </div>

    <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm md:text-base font-sans leading-relaxed">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm md:text-base font-semibold text-gray-600">#</th>
                    <th class="px-4 py-3 text-left text-sm md:text-base font-semibold text-gray-600">Nomor Pranota</th>
                    <th class="px-4 py-3 text-left text-sm md:text-base font-semibold text-gray-600">Vendor</th>
                    <th class="px-4 py-3 text-left text-sm md:text-base font-semibold text-gray-600">Tanggal</th>
                    <th class="px-4 py-3 text-right text-sm md:text-base font-semibold text-gray-600">Jumlah (Rp)</th>
                    <th class="px-4 py-3 text-left text-sm md:text-base font-semibold text-gray-600">Status Pembayaran</th>
                    <th class="px-4 py-3 text-left text-sm md:text-base font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pranotas as $i => $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm md:text-base text-gray-800">{{ is_object($pranotas) && method_exists($pranotas, 'firstItem') ? $pranotas->firstItem() + $i : ($i + 1) }}</td>
                    <td class="px-4 py-3 text-sm md:text-base text-gray-800">{{ $p->nomor_pranota ?? $p->group_code }}</td>
                    <td class="px-4 py-3 text-sm md:text-base text-gray-800">{{ $p->vendor }}</td>
                    <td class="px-4 py-3 text-sm md:text-base text-gray-800">{{ optional(
                        \Carbon\Carbon::parse($p->tanggal_harga_awal) )->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 text-sm md:text-base text-right text-gray-800">{{ number_format($p->harga, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-sm md:text-base text-gray-800">
                        @php $paid = isset($p->pembayaran_count) && $p->pembayaran_count > 0; @endphp
                        @if($paid)
                            <span class="inline-flex items-center px-2 py-1 rounded text-sm font-medium bg-green-100 text-green-800">Dibayar</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-sm font-medium bg-yellow-100 text-yellow-800">Belum Dibayar</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm md:text-base text-gray-800">
                        <div class="flex items-center space-x-3">
                            {{-- Tagihan kontainer sewa view was removed; replace with pranota detail or remove links --}}
                            @if(Route::has('pranota-tagihan-kontainer.destroy'))
                                <form action="{{ route('pranota-tagihan-kontainer.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus pranota ini? Tindakan ini akan mengembalikan status pada tagihan sumber.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600">Hapus</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">Tidak ada pranota ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        @if(is_object($pranotas) && method_exists($pranotas, 'links'))
            {{ $pranotas->links() }}
        @endif
    </div>
</div>
@endsection
