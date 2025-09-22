@extends('layouts.app')

@section('title', 'Approval Perbaikan Kontainer')
@section('page_title', 'Approval Perbaikan Kontainer')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-4 space-y-4">

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-3 rounded-r-lg">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-3 rounded-r-lg">
            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Header --}}
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Approval Perbaikan Kontainer</h1>
                <p class="text-sm text-gray-600">{{ $permohonan->nomor_memo }}</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                Approval
            </span>
        </div>
    </div>

    {{-- Info Permohonan --}}
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-3">Informasi Permohonan</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
            <div>
                <span class="text-gray-600">Supir:</span>
                <div class="font-medium">{{ $permohonan->supir->nama_panggilan ?? 'N/A' }}</div>
            </div>
            <div>
                <span class="text-gray-600">Kegiatan:</span>
                <div class="font-medium">{{ \App\Models\MasterKegiatan::where('kode_kegiatan', $permohonan->kegiatan)->value('nama_kegiatan') ?? $permohonan->kegiatan }}</div>
            </div>
            <div>
                <span class="text-gray-600">Vendor:</span>
                <div class="font-medium">{{ $permohonan->vendor_perusahaan ?? 'N/A' }}</div>
            </div>
            <div>
                <span class="text-gray-600">Kontainer:</span>
                <div class="font-medium">{{ $kontainerPerbaikan ? $kontainerPerbaikan->count() : 0 }} unit</div>
            </div>
        </div>
    </div>

    {{-- Detail Kontainer Perbaikan --}}
    @if($kontainerPerbaikan && $kontainerPerbaikan->count())
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-3">Detail Kontainer Perbaikan</h3>
        <div class="space-y-3">
            @foreach($kontainerPerbaikan as $kontainer)
            <div class="border border-gray-200 rounded-lg p-3">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold text-gray-900">{{ $kontainer->nomor_kontainer }}</span>
                        <span class="text-xs px-2 py-1 rounded-full {{ $kontainer->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $kontainer->status }}
                        </span>
                    </div>
                    <span class="text-sm text-gray-600">{{ $kontainer->ukuran ?? 'N/A' }}</span>
                </div>

                @if($kontainer->perbaikanKontainers && $kontainer->perbaikanKontainers->count())
                <div class="space-y-2">
                    @foreach($kontainer->perbaikanKontainers as $perbaikan)
                    <div class="bg-gray-50 rounded p-2 flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-sm font-medium">{{ $perbaikan->nomor_tagihan ?? 'N/A' }}</span>
                                <span class="text-xs px-1.5 py-0.5 rounded-full
                                    {{ $perbaikan->status_perbaikan === 'sudah_dibayar' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $perbaikan->status_label }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-600">{{ $perbaikan->deskripsi_perbaikan }}</p>
                        </div>
                        <div class="text-right ml-2">
                            <div class="text-sm font-bold text-green-600">
                                Rp {{ number_format($perbaikan->biaya_perbaikan ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Riwayat Checkpoint --}}
    @if($permohonan->checkpoints && $permohonan->checkpoints->count())
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-3">Riwayat Checkpoint</h3>
        <div class="space-y-2 max-h-32 overflow-y-auto">
            @foreach($permohonan->checkpoints->sortBy('tanggal_checkpoint') as $checkpoint)
            <div class="flex items-center justify-between py-1">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 rounded-full {{ $checkpoint->status == 'completed' ? 'bg-green-500' : 'bg-yellow-500' }}"></div>
                    <span class="text-sm">{{ $checkpoint->keterangan ?? 'Checkpoint' }}</span>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($checkpoint->tanggal_checkpoint)->format('d/m H:i') }}</div>
                    <span class="text-xs px-1.5 py-0.5 rounded-full
                        {{ $checkpoint->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $checkpoint->status }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Form Approval --}}
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-3">Form Approval</h3>
        <form action="{{ route('approval.store', $permohonan) }}" method="POST" enctype="multipart/form-data" id="approvalForm" class="space-y-4">
            @csrf

            {{-- Status Approval --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Status Approval <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-300 hover:bg-green-50 transition-all">
                        <input type="radio" name="status_permohonan" value="selesai" class="mr-2" required>
                        <div>
                            <div class="font-medium text-gray-900 text-sm">Selesai</div>
                            <div class="text-xs text-gray-600">Perbaikan telah selesai</div>
                        </div>
                    </label>

                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-300 hover:bg-red-50 transition-all">
                        <input type="radio" name="status_permohonan" value="bermasalah" class="mr-2">
                        <div>
                            <div class="font-medium text-gray-900 text-sm">Bermasalah</div>
                            <div class="text-xs text-gray-600">Ada masalah dalam perbaikan</div>
                        </div>
                    </label>
                </div>
                <div id="statusError" class="mt-2 text-sm text-red-600 hidden">Harap pilih status approval.</div>
            </div>

            {{-- Vendor/Bengkel --}}
            <div>
                <label for="vendor_bengkel" class="block text-sm font-medium text-gray-700 mb-2">
                    Vendor/Bengkel <span class="text-red-500">*</span>
                </label>
                <select id="vendor_bengkel" name="vendor_bengkel" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    <option value="">Pilih Vendor/Bengkel...</option>
                    @foreach($vendorBengkelOptions as $vendor)
                    <option value="{{ $vendor->nama_bengkel }}">{{ $vendor->nama_bengkel }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Pilih vendor atau bengkel yang melakukan perbaikan</p>
            </div>

            {{-- Catatan --}}
            <div>
                <label for="catatan_karyawan" class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan Karyawan
                </label>
                <textarea id="catatan_karyawan" name="catatan_karyawan" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm resize-none"
                    placeholder="Tambahkan catatan..."></textarea>
            </div>

            {{-- File Upload --}}
            <div>
                <label for="lampiran_kembali" class="block text-sm font-medium text-gray-700 mb-2">
                    Lampiran <span class="text-gray-500">(Opsional)</span>
                </label>
                <input id="lampiran_kembali" name="lampiran_kembali" type="file" accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                <p class="text-xs text-gray-500 mt-1">PDF, JPG, JPEG, PNG hingga 2MB</p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <a href="{{ route('approval.dashboard') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Kembali
                </a>
                <button type="submit"
                    class="px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-purple-600 hover:bg-purple-700">
                    Approve & Selesaikan
                </button>
            </div>
        </form>
    </div>

</div>

@endsection
