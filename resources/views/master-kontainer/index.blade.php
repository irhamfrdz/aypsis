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

{{-- Rows Per Page Selection --}}
@include('components.rows-per-page', [
    'routeName' => 'master.kontainer.index',
    'paginator' => $kontainers,
    'entityName' => 'kontainer',
    'entityNamePlural' => 'kontainer'
])

<div class="overflow-x-auto shadow-md sm:rounded-lg table-container">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="sticky-table-header bg-gray-50 sticky top-0 z-10 shadow-sm">
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

{{-- Modern Pagination Design --}}
@include('components.modern-pagination', ['paginator' => $kontainers, 'routeName' => 'master.kontainer.index'])

@endsection

<style>
/* Sticky Table Header Styles */
.sticky-table-header {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: rgb(249 250 251); /* bg-gray-50 */
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
}

/* Enhanced table container for better scrolling */
.table-container {
    max-height: calc(100vh - 300px); /* Adjust based on your layout */
    overflow-y: auto;
    border: 1px solid rgb(229 231 235); /* border-gray-200 */
    border-radius: 0.5rem;
}

/* Smooth scrolling for better UX */
.table-container {
    scroll-behavior: smooth;
}

/* Table header cells need specific background to avoid transparency issues */
.sticky-table-header th {
    background-color: rgb(249 250 251) !important;
    border-bottom: 1px solid rgb(229 231 235);
}

/* Optional: Add a subtle border when scrolling */
.table-container.scrolled .sticky-table-header {
    border-bottom: 2px solid rgb(59 130 246); /* blue-500 */
}
</style>
