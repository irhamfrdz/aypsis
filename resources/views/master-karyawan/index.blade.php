@extends('layouts.app')

@section('title', 'Master Karyawan')
@section('page_title', 'Master Karyawan')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Karyawan</h2>
        <div class="flex items-center space-x-3">
            <a href="{{ route('master.karyawan.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                + Tambah Karyawan
            </a>
            <a href="{{ route('master.karyawan.print') }}" target="_blank" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300">
                Cetak Semua
            </a>
            <a href="{{ route('master.karyawan.export') }}?sep=%3B" class="bg-green-100 hover:bg-green-200 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300">
                Download CSV
            </a>
            <a href="{{ route('master.karyawan.import') }}" class="bg-yellow-100 hover:bg-yellow-200 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300">
                Import CSV
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full bg-white text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">NIK</th>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Lengkap</th>
                    <th class="py-2 px-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Panggilan</th>
                    <th class="py-2 px-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Divisi</th>
                    <th class="py-2 px-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pekerjaan</th>
                    <th class="py-2 px-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">JKN</th>
                    <th class="py-2 px-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">BP Jamsostek</th>
                    <th class="py-2 px-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No Hp</th>
                    <th class="py-2 px-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                    <th class="py-2 px-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status Pajak</th>
                    <th class="py-2 px-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Masuk</th>
                    <th class="py-2 px-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>

            <tbody class="text-gray-700 text-sm">
                @forelse ($karyawans as $karyawan)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="py-2 px-2 whitespace-nowrap">{{ $karyawan->nik }}</td>
                        <td class="py-2 px-3 whitespace-nowrap">{{ $karyawan->nama_lengkap }}</td>
                        <td class="py-2 px-2 whitespace-nowrap">{{ $karyawan->nama_panggilan }}</td>
                        <td class="py-2 px-2 whitespace-nowrap">{{ $karyawan->divisi }}</td>
                        <td class="py-2 px-2 whitespace-nowrap">{{ $karyawan->pekerjaan }}</td>
                        <td class="py-2 px-2 whitespace-nowrap">{{ $karyawan->jkn ?? '-' }}</td>
                        <td class="py-2 px-2 whitespace-nowrap">{{ $karyawan->bpjs_jamsostek ?? '-' }}</td>
                        <td class="py-2 px-2 whitespace-nowrap">{{ $karyawan->no_hp }}</td>
                        <td class="py-2 px-2 whitespace-nowrap">{{ $karyawan->email ?? '-' }}</td>
                        <td class="py-2 px-2 whitespace-nowrap">{{ $karyawan->status_pajak ?? '-' }}</td>
                        <td class="py-2 px-2 whitespace-nowrap">{{ $karyawan->tanggal_masuk ? \Carbon\Carbon::parse($karyawan->tanggal_masuk)->format('d/m/Y') : '-' }}</td>
                        <td class="py-2 px-2 whitespace-nowrap text-center">
                            <div class="flex item-center justify-center space-x-1">
                                {{-- Show crew checklist button only for ABK division --}}
                                @if(strtolower($karyawan->divisi) === 'abk')
                                    <a href="{{ route('master.karyawan.crew-checklist', $karyawan->id) }}" class="text-blue-600 hover:text-blue-900" title="Checklist Kelengkapan Crew">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zm10-12a1 1 0 100-2 1 1 0 000 2zm0 4a1 1 0 100-2 1 1 0 000 2zm0 4a1 1 0 100-2 1 1 0 000 2zm0 4a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                @endif

                                <a href="{{ route('master.karyawan.show', $karyawan->id) }}" class="text-gray-600 hover:text-gray-900" title="Lihat">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M2.458 12C3.732 8.943 7.523 6 12 6s8.268 2.943 9.542 6c-1.274 3.057-5.065 6-9.542 6S3.732 15.057 2.458 12z" />
                                        <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                                    </svg>
                                </a>
                                <a href="{{ route('master.karyawan.print.single', $karyawan->id) }}" target="_blank" class="text-gray-600 hover:text-gray-900" title="Cetak">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M6 9V3h8v6H6z" />
                                        <path d="M4 7h12v6h2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v6h2V7z" />
                                        <path d="M6 13h8v4H6v-4z" />
                                    </svg>
                                </a>
                                <a href="{{ route('master.karyawan.edit', $karyawan->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                        <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <form action="{{ route('master.karyawan.destroy', $karyawan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center py-4 text-gray-500">
                            Belum ada data Karyawan yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $karyawans->links() }}
    </div>
</div>
@endsection
