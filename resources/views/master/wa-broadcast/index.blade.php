@extends('layouts.app')

@section('title', 'Master Broadcast WA')
@section('page_title', 'Master Broadcast WA')

@section('content')
<div class="bg-white shadow rounded-lg p-4 sm:p-6 mb-6 font-sans">
    <div class="flex justify-between items-center mb-6 pb-4 border-b">
        <h2 class="text-xl font-bold text-gray-800">Riwayat Broadcast WhatsApp</h2>
        @can('master-wa-broadcast-create')
        <a href="{{ route('master.wa-broadcast.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm flex items-center transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Broadcast
        </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table id="dataTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapal & Voyage</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kendala</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template WA</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Shipper</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($broadcasts as $index => $broadcast)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ \Carbon\Carbon::parse($broadcast->created_at)->format('d M Y, H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">{{ $broadcast->nama_kapal }}</div>
                        <div class="text-xs text-gray-500">Voyage: {{ $broadcast->no_voyage }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 font-medium">{{ $broadcast->kategori_masalah }}</div>
                        @if($broadcast->deskripsi_masalah)
                        <div class="text-xs text-gray-500 mt-1 line-clamp-2 max-w-xs">{{ Str::limit($broadcast->deskripsi_masalah, 50) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($broadcast->template)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $broadcast->template->nama_template }}
                            </span>
                        @else
                            <span class="text-gray-400 italic">Terhapus</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $broadcast->total_shipper }} Shipper
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <form action="{{ route('report.manifests.broadcast-preview') }}" method="POST" target="_blank" class="inline-block">
                            @csrf
                            <input type="hidden" name="nama_kapal" value="{{ $broadcast->nama_kapal }}">
                            <input type="hidden" name="no_voyage" value="{{ $broadcast->no_voyage }}">
                            <input type="hidden" name="kategori_masalah" value="{{ $broadcast->kategori_masalah }}">
                            <input type="hidden" name="deskripsi_masalah" value="{{ $broadcast->deskripsi_masalah }}">
                            <input type="hidden" name="template_id" value="{{ $broadcast->wa_template_id }}">
                            <button type="submit" class="inline-flex items-center text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-md transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                Kirim Ulang
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    });
</script>
@endpush
@endsection
