@extends('layouts.app')

@section('title', 'Master Armada')
@section('page_title', 'Master Armada')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h1 class="text-2xl font-bold text-gray-900">Master Armada</h1>
        <p class="mt-1 text-sm text-gray-600">Daftar armada yang menggunakan GPS beserta informasi kendaraan dan pengemudi.</p>
        <p class="mt-1 text-xs text-gray-500">Isi Km secara manual pada masing-masing armada, lalu klik Simpan.</p>
        <a href="{{ route('dashboard-armada.index') }}" class="inline-block mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800">Lihat Dashboard Armada</a>
    </div>

    @if(session('success'))
        <div role="status" class="rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div role="alert" class="rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-800">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('master.armada.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <label for="search" class="sr-only">Cari Armada</label>
                <input id="search" name="search" type="search" value="{{ $search }}" placeholder="Cari plat, kode, merek, lokasi, atau pengemudi..." class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Cari</button>
            @if($search !== '')
                <a href="{{ route('master.armada.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-indigo-600">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 text-sm text-gray-600">Total {{ number_format($armadas->total(), 0, ',', '.') }} armada</div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        @foreach(['No', 'Kode', 'Nomor Polisi', 'Nama Armada', 'Merek / Jenis', 'Tahun', 'Km', 'Lokasi', 'Pengemudi / Pemakai', 'Aksi'] as $heading)
                            <th scope="col" class="px-6 py-3 text-left whitespace-nowrap">{{ $heading }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($armadas as $armada)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-500">{{ $armadas->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $armada->kode_no ?: '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">{{ $armada->nomor_polisi ?: '-' }}</td>
                            <td class="px-6 py-4">{{ $armada->nickname ?: '-' }}</td>
                            <td class="px-6 py-4">
                                <div>{{ $armada->merek ?: '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $armada->jenis ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $armada->tahun_pembuatan ?: '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @can('master-mobil-update')
                                    <form method="POST" action="{{ route('master.armada.update-km', $armada) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="armada_id" value="{{ $armada->id }}">
                                        <label for="km-{{ $armada->id }}" class="sr-only">Km {{ $armada->nomor_polisi }}</label>
                                        <input id="km-{{ $armada->id }}" name="km" type="number" min="0" max="999999999.999" step="0.001" value="{{ (string) old('armada_id') === (string) $armada->id ? old('km') : $armada->km }}" placeholder="Isi Km" class="w-36 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <button type="submit" class="px-3 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-xs font-medium text-white">Simpan</button>
                                    </form>
                                @else
                                    {{ $armada->km !== null ? number_format($armada->km, 3, ',', '.') : '-' }}
                                @endcan
                            </td>
                            <td class="px-6 py-4">{{ $armada->lokasi ?: '-' }}</td>
                            <td class="px-6 py-4">
                                <div>{{ $armada->karyawan?->nama_lengkap ?: ($armada->pemakai ?: '-') }}</div>
                                @if($armada->karyawan?->nik)
                                    <div class="text-xs text-gray-500">{{ $armada->karyawan->nik }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4"><a href="{{ route('master.mobil.show', $armada) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-6 py-10 text-center text-gray-500">{{ $search !== '' ? 'Tidak ada armada yang sesuai dengan pencarian.' : 'Belum ada data armada.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($armadas->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $armadas->links() }}</div>
        @endif
    </div>
</div>
@endsection
