@extends('layouts.app')
@section('title', 'Dashboard Pemakaian Barang')
@section('page_title', 'Dashboard Pemakaian Barang')
@section('content')
<div class="container mx-auto px-4 py-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Dashboard Pemakaian Barang</h1>
        <p class="text-sm text-gray-500 mt-1">Valuasi pemakaian Stock Amprahan berdasarkan kategori pemakai dan tanggal pengambilan.</p>
        <a href="{{ route('stock-amprahan.index') }}" class="text-sm text-indigo-600 hover:underline">Kembali ke Stock Amprahan</a>
    </div>
    <form method="GET" action="{{ route('stock-amprahan.dashboard-pemakaian') }}" class="bg-white border rounded-xl p-5 shadow-sm">
        @if($errors->any())
            <div role="alert" class="mb-4 text-sm text-red-700 bg-red-50 p-3 rounded-lg">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="kategori_pemakai" class="block text-sm font-semibold mb-2">Kategori Pemakai</label>
                <select id="kategori_pemakai" name="kategori_pemakai" required class="w-full border-gray-300 rounded-lg text-sm">
                    <option value="">Pilih kategori pemakai</option>
                    @foreach($categories as $value => $label)
                        <option value="{{ $value }}" @selected(old('kategori_pemakai', request('kategori_pemakai')) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="from_date" class="block text-sm font-semibold mb-2">Tanggal Dari</label>
                <input type="date" id="from_date" name="from_date" required value="{{ old('from_date', request('from_date', now()->startOfMonth()->format('Y-m-d'))) }}" class="w-full border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label for="to_date" class="block text-sm font-semibold mb-2">Tanggal Ke</label>
                <input type="date" id="to_date" name="to_date" required value="{{ old('to_date', request('to_date', now()->format('Y-m-d'))) }}" class="w-full border-gray-300 rounded-lg text-sm">
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-5">
            <a href="{{ route('stock-amprahan.dashboard-pemakaian') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-sm">Reset</a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">Tampilkan Data</button>
        </div>
    </form>
    @if($usages !== null)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border rounded-xl p-5"><p class="text-sm text-gray-500">Kategori Pemakai</p><p class="font-semibold mt-2">{{ $categories[request('kategori_pemakai')] }}</p></div>
            <div class="bg-white border rounded-xl p-5"><p class="text-sm text-gray-500">Jumlah Catatan Pemakaian</p><p class="text-2xl font-bold mt-2">{{ number_format($usages->total(), 0, ',', '.') }}</p></div>
            <div class="bg-indigo-50 border rounded-xl p-5"><p class="text-sm text-indigo-700">Total Nilai Pemakaian (Seluruh Hasil)</p><p class="text-2xl font-bold mt-2">Rp {{ number_format($totalNilai, 2, ',', '.') }}</p></div>
        </div>
        <div class="bg-white border rounded-xl overflow-hidden">
            <div class="p-4 border-b">
                <h2 class="font-semibold">Rincian Pemakaian Barang</h2>
                <p class="text-xs text-gray-500 mt-1">Periode {{ \Carbon\Carbon::parse(request('from_date'))->format('d/m/Y') }} – {{ \Carbon\Carbon::parse(request('to_date'))->format('d/m/Y') }}. Nilai pemakaian = jumlah keluar × harga satuan.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-xs uppercase"><tr>
                        @foreach(['No', 'Tanggal Pakai', 'Pemakai', 'Penerima', 'No. Bukti Stock', 'Nama Barang', 'Toko', 'Tipe Amprahan', 'Jumlah', 'Satuan', 'Harga Satuan', 'Total Nilai', 'Keterangan'] as $heading)
                            <th class="px-4 py-3 whitespace-nowrap">{{ $heading }}</th>
                        @endforeach
                    </tr></thead>
                    <tbody class="divide-y">
                        @forelse($usages as $usage)
                            @php
                                $stock = $usage->stockAmprahan;
                                $harga = $stock->harga_satuan ?? 0;
                                $pemakai = match(request('kategori_pemakai')) {
                                    'penerima' => $usage->penerima->nama_lengkap ?? '-',
                                    'alat_berat' => trim(($usage->alatBerat->kode_alat ?? '').' '.($usage->alatBerat->nama ?? '')) ?: '-',
                                    'kapal' => $usage->kapal->nama_kapal ?? '-',
                                    'kantor' => $usage->kantor ?? '-',
                                    default => collect([$usage->kendaraan, $usage->truck, $usage->buntut])->filter()->unique('id')->map(fn ($mobil) => ($mobil->nomor_polisi && $mobil->nomor_polisi !== '-' ? $mobil->nomor_polisi : ($mobil->no_kir ?: '-')))->implode(' / ') ?: '-',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $usages->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ \Carbon\Carbon::parse($usage->tanggal_pengambilan)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 font-medium">{{ $pemakai }}</td>
                                <td class="px-4 py-3">{{ $usage->penerima->nama_lengkap ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $stock->nomor_bukti ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $stock->nama_barang ?? $stock->masterNamaBarangAmprahan->nama_barang ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $stock->vendorAmprahan->nama_toko ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $stock->type_amprahan ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($usage->jumlah, 2, ',', '.') }}</td>
                                <td class="px-4 py-3">{{ $stock->satuan ?? '-' }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">Rp {{ number_format($harga, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">Rp {{ number_format($usage->jumlah * $harga, 2, ',', '.') }}</td>
                                <td class="px-4 py-3">{{ $usage->keterangan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="13" class="px-4 py-12 text-center text-gray-500">Tidak ada pemakaian barang pada kategori dan periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t">{{ $usages->links() }}</div>
        </div>
    @else
        <div class="bg-white rounded-xl border border-dashed p-12 text-center text-gray-500">Pilih kategori pemakai dan rentang tanggal, kemudian klik Tampilkan Data.</div>
    @endif
</div>
@endsection
