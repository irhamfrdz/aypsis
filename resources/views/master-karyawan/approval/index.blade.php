@extends('layouts.app')

@section('title', 'Persetujuan Perubahan Karyawan')
@section('page_title', 'Persetujuan Perubahan Karyawan')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-5 border-b bg-gray-50 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">Persetujuan Perubahan Karyawan</h1>
                <p class="text-sm text-gray-500 mt-0.5">Daftar pengajuan edit data karyawan yang memerlukan verifikasi.</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200 uppercase tracking-wider">
                    {{ $requests->count() }} Menunggu
                </span>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Karyawan</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Diajukan Oleh</th>
                        <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($requests as $req)
                        <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-bold text-sm">
                                        {{ substr($req->karyawan->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 uppercase">
                                            {{ $req->karyawan->nama_lengkap }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            NIK: {{ $req->karyawan->nik }}
                                        </div>
                                        <div class="mt-1">
                                            <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100 uppercase italic">
                                                {{ $req->karyawan->catatan_pekerjaan }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-gray-900 font-medium">
                                    {{ $req->user->name }}
                                </div>
                                <div class="text-[10px] text-gray-400 mt-1 uppercase">
                                    {{ $req->created_at->translatedFormat('d F Y, H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('master.karyawan.show', $req->karyawan->id) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-bold uppercase rounded-lg transition-all shadow-sm">
                                        Detail
                                    </a>

                                    <form action="{{ route('master.karyawan.approval.approve', $req->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui perubahan ini?')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-[10px] font-bold uppercase rounded-lg transition-all shadow-sm">
                                            Setujui
                                        </button>
                                    </form>
                                    
                                    <button type="button" onclick="openRejectModal({{ $req->id }})" class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-[10px] font-bold uppercase rounded-lg transition-all shadow-sm">
                                        Tolak
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center text-gray-500 bg-gray-50/30">
                                <div class="flex flex-col items-center">
                                    <div class="h-16 w-16 bg-white rounded-full shadow-sm flex items-center justify-center border border-gray-100 mb-4">
                                        <i class="fas fa-check-double text-gray-300 text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 tracking-tight">Tidak Ada Pengajuan Pending</p>
                                    <p class="text-xs text-gray-400 mt-1">Semua pengajuan telah diproses atau belum ada pengajuan baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeRejectModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Tolak Pengajuan Perubahan</h3>
                            <div class="mt-4">
                                <label for="reject_reason" class="block text-xs font-bold text-gray-700 uppercase mb-2tracking-widest">Alasan Penolakan <span class="text-red-500">*</span></label>
                                <textarea id="reject_reason" name="reason" rows="3" required 
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all"
                                          placeholder="Masukkan alasan penolakan..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-sm font-bold uppercase tracking-widest text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto transition-all">
                        Matikan / Tolak
                    </button>
                    <button type="button" onclick="closeRejectModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-bold uppercase tracking-widest text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openRejectModal(requestId) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        form.action = `/master-karyawan/approval/${requestId}/reject`;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>
@endpush
@endsection
