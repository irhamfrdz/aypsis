@extends('layouts.app')

@section('title', 'Master Customer Buruh')
@section('page_title', 'Master Customer Buruh Bongkar Muat')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 font-sans">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">Daftar Customer Buruh</h2>
        @if(auth()->user()->can('master-customer-buruh-create'))
        <a href="{{ route('master-customer-buruh.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-300 flex items-center text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Customer
        </a>
        @endif
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Nama Customer</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">PIC / Telp</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($customers as $index => $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $customer->kode }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $customer->nama_customer }}</td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ $customer->pic ?: '-' }} <br>
                            <span class="text-xs">{{ $customer->no_telp }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ \Illuminate\Support\Str::limit($customer->alamat, 50) }}</td>
                        <td class="px-4 py-3">
                            @if($customer->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                @if(auth()->user()->can('master-customer-buruh-update'))
                                <a href="{{ route('master-customer-buruh.edit', $customer->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                @endif

                                @if(auth()->user()->can('master-customer-buruh-delete'))
                                <form action="{{ route('master-customer-buruh.destroy', $customer->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada data customer buruh.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
