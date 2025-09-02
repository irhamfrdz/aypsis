@extends('layouts.app')

@section('title','Master Kontainer')
@section('page_title','Master Kontainer')

@section('content')

<h2 class="text-xl font-bold text-gray-800 mb-4">Daftar Kontainer</h2>

<div class="mb-4 flex justify-end">
    <a href="{{ route('master.kontainer.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        Tambah Kontainer Baru
    </a>
</div>

@if (session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-4" role="alert">
    {{session('success')}}
</div>
@endif

<div class="overflow-x-auto shadow-md sm:rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Nomor Kontainer
                </th>

                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Ukuran
                </th>

                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tipe
                </th>

                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                </th>

                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Aksi
                </th>
            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($kontainers as $kontainer )
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{$kontainer->nomor_seri_gabungan}}</div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500">{{$kontainer->ukuran}}</div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500">{{$kontainer->tipe_kontainer}}</div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    {{-- Contoh styling kondisional untuk status. Anda bisa sesuaikan dengan nilai status yang ada. --}}
                    @php
                        $statusClass = 'bg-gray-100 text-gray-800'; // Default
                        if (in_array($kontainer->status, ['Tersedia', 'Baik'])) $statusClass = 'bg-green-100 text-green-800';
                        if (in_array($kontainer->status, ['Disewa', 'Digunakan'])) $statusClass = 'bg-yellow-100 text-yellow-800';
                        if (in_array($kontainer->status, ['Rusak', 'Perbaikan'])) $statusClass = 'bg-red-100 text-red-800';
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                        {{ $kontainer->status ?? 'N/A' }}
                    </span>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{route('master.kontainer.edit',$kontainer->id)}}" class="text-indigo-600 hover:text-indigo-900 mr-4">edit</a>
                    <form action="{{route('master.kontainer.destroy',$kontainer->id)}}" method="POST" class="inline-block" onsubmit="return confirm('Apakah anda yakin ingin menghapus kontainer ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data kontainer.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $kontainers->links() }}
</div>

@endsection
