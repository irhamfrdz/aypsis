@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Tambah Tagihan Kontainer Sewa</h1>

    @if($errors->any())
        <div class="mb-4 text-red-700">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('daftar-tagihan-kontainer-sewa.store') }}" method="POST" class="space-y-4 bg-white p-4 rounded shadow">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm">Vendor</label>
                <input type="text" name="vendor" value="{{ old('vendor') }}" class="w-full border rounded px-2 py-1" />
            </div>
            <div>
                <label class="block text-sm">Nomor Kontainer</label>
                <input type="text" name="nomor_kontainer" value="{{ old('nomor_kontainer') }}" class="w-full border rounded px-2 py-1" />
            </div>
            <div>
                <label class="block text-sm">Size</label>
                <input type="text" name="size" value="{{ old('size') }}" class="w-full border rounded px-2 py-1" placeholder="20ft, 40ft, dll" />
            </div>

            <div>
                <label class="block text-sm">Group</label>
                <input type="text" name="group" value="{{ old('group') }}" class="w-full border rounded px-2 py-1" />
            </div>
            <div>
                <label class="block text-sm">Periode</label>
                <input type="text" name="periode" value="{{ old('periode') }}" class="w-full border rounded px-2 py-1" placeholder="YYYY-MM atau teks" />
            </div>

            <div>
                <label class="block text-sm">Tanggal Harga Awal</label>
                <input type="date" name="tanggal_harga_awal" value="{{ old('tanggal_harga_awal') }}" class="w-full border rounded px-2 py-1" />
            </div>
            <div>
                <label class="block text-sm">Tanggal Harga Akhir</label>
                <input type="date" name="tanggal_harga_akhir" value="{{ old('tanggal_harga_akhir') }}" class="w-full border rounded px-2 py-1" />
            </div>

            <div>
                <label class="block text-sm">Masa</label>
                <input type="text" name="masa" value="{{ old('masa') }}" class="w-full border rounded px-2 py-1" />
            </div>
            <div>
                <label class="block text-sm">DPP</label>
                <input type="number" step="0.01" name="dpp" value="{{ old('dpp') }}" class="w-full border rounded px-2 py-1" />
            </div>

            <div>
                <label class="block text-sm">DPP Nilai Lain</label>
                <input type="number" step="0.01" name="dpp_nilai_lain" value="{{ old('dpp_nilai_lain') }}" class="w-full border rounded px-2 py-1" />
            </div>
            <div>
                <label class="block text-sm">PPN</label>
                <input type="number" step="0.01" name="ppn" value="{{ old('ppn') }}" class="w-full border rounded px-2 py-1" />
            </div>

            <div>
                <label class="block text-sm">PPH</label>
                <input type="number" step="0.01" name="pph" value="{{ old('pph') }}" class="w-full border rounded px-2 py-1" />
            </div>
            <div>
                <label class="block text-sm">Grand Total</label>
                <input type="number" step="0.01" name="grand_total" value="{{ old('grand_total') }}" class="w-full border rounded px-2 py-1" />
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
            <a href="{{ route('daftar-tagihan-kontainer-sewa.index') }}" class="ml-2 inline-block px-4 py-2 border rounded">Batal</a>
        </div>
    </form>
</div>
@endsection
