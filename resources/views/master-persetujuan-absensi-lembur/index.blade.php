@extends('layouts.app')

@section('title', 'Persetujuan Absen Lembur')
@section('page_title', 'Persetujuan Absen Lembur')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        /* Custom DataTables Styling */
        div.dataTables_wrapper div.dataTables_filter input {
            border-radius: 0.375rem;
            border-color: #d1d5db;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
        }
        div.dataTables_wrapper div.dataTables_length select {
            border-radius: 0.375rem;
            border-color: #d1d5db;
            padding-top: 0.375rem;
            padding-bottom: 0.375rem;
            font-size: 0.875rem;
        }
        table.dataTable.no-footer {
            border-bottom: 1px solid #e5e7eb;
        }
        table.dataTable thead th, table.dataTable thead td {
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
            font-weight: 600;
            background-color: #f9fafb;
            font-size: 0.75rem;
            text-transform: uppercase;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }
        table.dataTable tbody td {
            border-bottom: 1px solid #f3f4f6;
            color: #4b5563;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            font-size: 0.875rem;
        }
    </style>
@endpush

@section('content')
<!-- Page Header Card -->
<div class="bg-white rounded-lg border border-gray-200 p-6 mb-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-xl font-bold text-gray-900 leading-tight flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-blue-500"></i> Daftar Pengajuan Lembur
        </h1>
        <p class="text-xs text-gray-500 mt-1">Kelola dan tinjau permohonan karyawan yang lupa melakukan absensi masuk atau keluar.</p>
    </div>
    
    @if(auth()->user()->can('approval-absensi-lembur-create'))
    <a href="{{ route('master.persetujuan-absensi-lembur.create') }}" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Pengajuan
    </a>
    @endif
</div>

@if (session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 shadow-sm flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-lg"></i>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
@endif
@if (session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 shadow-sm flex items-center gap-3">
        <i class="fa-solid fa-circle-exclamation text-lg"></i>
        <p class="text-sm font-medium">{{ session('error') }}</p>
    </div>
@endif

<!-- Table Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-4 sm:p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <h3 class="text-base font-bold text-gray-800">Riwayat Pengajuan</h3>
        <button onclick="$('#dataTable').DataTable().ajax.reload()" class="text-gray-500 hover:text-blue-600 transition-colors p-1" title="Segarkan Data">
            <i class="fa-solid fa-arrows-rotate"></i>
        </button>
    </div>
    <div class="p-4 sm:p-6">
        <div class="overflow-x-auto custom-scrollbar">
            <table id="dataTable" class="w-full text-sm text-left whitespace-nowrap">
                <thead>
                    <tr>
                        <th class="px-4">No</th>
                        <th class="px-4">Karyawan</th>
                        <th class="px-4">NIK</th>
                        <th class="px-4">Tanggal</th>
                        <th class="px-4">Jam Mulai</th>
                        <th class="px-4">Keterangan</th>
                        <th class="px-4">Foto</th>
                        <th class="px-4">Status</th>
                        <th class="px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables Content -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div id="rejectModal" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeRejectModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100">
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-gray-900" id="modal-title">
                            Tolak Pengajuan
                        </h3>
                        <button type="button" onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <label for="catatan_admin" class="block text-sm font-medium text-gray-700 mb-2">Keterangan Penolakan <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <textarea id="catatan_admin" name="catatan_admin" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 text-sm transition-colors" rows="3" placeholder="Masukkan keterangan kenapa pengajuan ditolak..."></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                    <button type="submit" class="inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        Tolak Pengajuan
                    </button>
                    <button type="button" onclick="closeRejectModal()" class="inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.tailwindcss.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            var table = $('#dataTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: "{{ route('master.persetujuan-absensi-lembur.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center w-10'},
                    {data: 'karyawan_nama', name: 'karyawan.nama', className: 'font-medium text-gray-900'},
                    {data: 'karyawan_nik', name: 'karyawan.nik', className: 'text-gray-500'},
                    {data: 'tanggal_format', name: 'tanggal'},
                    {data: 'jam_mulai_format', name: 'jam_mulai', className: 'text-sm'},
                    {data: 'keterangan', name: 'keterangan', className: 'text-sm', render: function(data) {
                        return data ? '<span class="truncate block max-w-[200px]" title="'+data+'">'+data+'</span>' : '-';
                    }},
                    {data: 'foto', name: 'foto', className: 'text-sm text-center', orderable: false, searchable: false},
                    {data: 'status_badge', name: 'status', className: 'text-sm text-center'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right'},
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                    search: "Cari data:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    infoEmpty: "",
                    emptyTable: "Belum ada pengajuan Lembur.",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Selanjutnya"
                    }
                },
                order: [], // Let server set initial order (created_at desc) or just leave empty
                pageLength: 10,
                drawCallback: function(settings) {
                    $('.dataTables_paginate > .pagination').addClass('flex items-center justify-end gap-1 mt-4');
                    
                    // Hide pagination if only 1 page or empty
                    var api = this.api();
                    var pageInfo = api.page.info();
                    
                    if (pageInfo.pages <= 1) {
                        $('.dataTables_paginate').hide();
                    } else {
                        $('.dataTables_paginate').show();
                    }
                    
                    if (pageInfo.recordsTotal === 0) {
                        $('.dataTables_info').hide();
                    } else {
                        $('.dataTables_info').show();
                    }
                }
            });

            // Delete Action
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'rounded-lg px-4 py-2 font-semibold',
                        cancelButton: 'rounded-lg px-4 py-2 font-semibold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Terhapus!', 
                                        text: response.message, 
                                        icon: 'success',
                                        confirmButtonColor: '#3b82f6'
                                    });
                                    table.ajax.reload(null, false); // Reload without resetting pagination
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                            }
                        });
                    }
                });
            });

            // Approve Action
            $(document).on('click', '.btn-approve', function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                Swal.fire({
                    title: 'Setujui Pengajuan?',
                    text: "Pengajuan ini akan dicatat sebagai absensi yang sah.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Setujui',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'rounded-lg px-4 py-2 font-semibold',
                        cancelButton: 'rounded-lg px-4 py-2 font-semibold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        function openRejectModal(url) {
            $('#rejectForm').attr('action', url);
            $('#rejectModal').removeClass('hidden');
        }

        function closeRejectModal() {
            $('#rejectModal').addClass('hidden');
            $('#rejectForm')[0].reset();
        }
    </script>
@endpush
