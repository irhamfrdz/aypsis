@extends('layouts.app')

@section('title', 'Preview Broadcast WA')
@section('page_title', 'Preview Broadcast WhatsApp')

@section('content')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">
    <style>
        /* Custom DataTables Styling */
        div.dataTables_wrapper div.dataTables_filter input {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            padding: 0.4rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            margin-left: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        div.dataTables_wrapper div.dataTables_filter input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
        }
        div.dataTables_wrapper div.dataTables_length select {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            padding: 0.4rem 2rem 0.4rem 0.75rem;
            font-size: 0.875rem;
            margin-left: 0.5rem;
            margin-right: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        div.dataTables_wrapper div.dataTables_length select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.25em 0.5em;
        }
        .dataTables_wrapper .grid {
            margin-bottom: 1rem;
            align-items: center;
        }
    </style>
@endpush

<div class="bg-white shadow-md rounded-lg p-6 font-sans">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">Preview Pesan Broadcast</h2>
        <a href="{{ route('master.wa-broadcast.index') }}" class="text-gray-600 hover:text-gray-900 font-medium flex items-center text-sm">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Kapal:</strong> {{ $namaKapal }} | <strong>Voyage:</strong> {{ $noVoyage }} <br>
                    <strong>Masalah:</strong> {{ $kategoriMasalah }} <br>
                    Total ada <strong>{{ count($broadcastData) }}</strong> shipper dan <strong>{{ collect($broadcastData)->sum('jumlah_kontainer') }}</strong> kontainer yang terdampak (sudah digabung per-shipper). 
                    Silakan klik tombol <strong>Kirim WA</strong> untuk membuka WhatsApp Web / Aplikasi WA Anda.
                </p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm bg-white">
        <table id="previewTable" class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-5 py-4 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs w-12">No</th>
                    <th class="px-5 py-4 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs w-1/4">Shipper / Kontak</th>
                    <th class="px-5 py-4 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Preview Pesan</th>
                    <th class="px-5 py-4 text-center font-semibold text-gray-600 uppercase tracking-wider text-xs w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($broadcastData as $index => $data)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 text-gray-500 align-top">{{ $index + 1 }}</td>
                        <td class="px-5 py-4 align-top">
                            <div class="font-bold text-gray-900 text-base">{{ $data['shipper_name'] }}</div>
                            <div class="text-gray-500 text-xs mt-1.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    Sumber: {{ $data['sumber_tabel'] }}
                                </span>
                            </div>
                            <div class="text-gray-600 text-sm flex items-center mt-2.5 bg-gray-50 p-1.5 rounded-lg inline-flex border border-gray-100">
                                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                @if($data['telepon'])
                                    <span class="font-semibold text-gray-800">{{ $data['telepon'] }}</span>
                                @else
                                    <span class="text-red-500 italic font-medium">Belum ada no telepon</span>
                                @endif
                            </div>
                            
                            <div class="mt-3">
                                <div class="text-xs font-semibold text-indigo-700 flex items-center mb-1.5">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                    {{ $data['jumlah_kontainer'] }} Kontainer Terdampak:
                                </div>
                                @if(isset($data['daftar_kontainer']) && count($data['daftar_kontainer']) > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($data['daftar_kontainer'] as $kontainer)
                                        <span class="bg-white border border-gray-300 text-gray-700 px-2 py-0.5 rounded-md text-[10px] font-mono shadow-sm">
                                            {{ $kontainer }}
                                        </span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="relative bg-[#e1f5cd] border border-[#d2e8bd] p-3.5 rounded-2xl rounded-tl-sm text-[13px] text-gray-800 whitespace-pre-wrap font-sans shadow-sm leading-relaxed max-w-xl">
                                {{ $data['pesan'] }}
                                <!-- chat tail pointer -->
                                <div class="absolute top-0 -left-2 w-0 h-0 border-t-[12px] border-t-[#e1f5cd] border-l-[10px] border-l-transparent drop-shadow-sm"></div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center align-top">
                            @if($data['wa_url'])
                                <a href="{{ $data['wa_url'] }}" target="_blank" class="inline-flex items-center justify-center px-3 py-2 bg-green-500 text-white text-xs font-bold rounded hover:bg-green-600 transition-colors shadow-sm">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                    Kirim WA
                                </a>
                            @else
                                <span class="inline-flex items-center justify-center px-3 py-2 bg-gray-300 text-gray-500 text-xs font-bold rounded cursor-not-allowed shadow-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    Tidak Ada No
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada data shipper pada manifest untuk kapal dan voyage ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.tailwindcss.min.js"></script>
<script>
    $(document).ready(function() {
        $('#previewTable').DataTable({
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
