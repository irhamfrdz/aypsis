@extends('layouts.app')

@section('title', 'Penyelesaian Tugas')
@section('page_title', 'Daftar Tugas untuk Diselesaikan')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <!-- Navigation Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="flex space-x-8">
                <a href="{{ route('approval.dashboard') }}" class="text-indigo-600 whitespace-nowrap py-2 px-1 border-b-2 border-indigo-500 font-medium text-sm">
                    📋 Dashboard Approval
                </a>
                <a href="{{ route('approval.riwayat') }}" class="text-gray-500 hover:text-gray-700 whitespace-nowrap py-2 px-1 border-b-2 border-transparent font-medium text-sm">
                    📚 Riwayat Approval
                </a>
            </nav>
        </div>

            <form method="GET" action="" class="mb-4 flex items-center gap-4">
                <label for="vendor" class="font-semibold text-gray-700">Filter Vendor:</label>
                <select name="vendor" id="vendor" class="border rounded px-3 py-2">
                    <option value="">Semua Vendor</option>
                    <option value="AYP" {{ request('vendor') == 'AYP' ? 'selected' : '' }}>AYP</option>
                    <option value="ZONA" {{ request('vendor') == 'ZONA' ? 'selected' : '' }}>ZONA</option>
                    <option value="SOC" {{ request('vendor') == 'SOC' ? 'selected' : '' }}>SOC</option>
                    <option value="DPE" {{ request('vendor') == 'DPE' ? 'selected' : '' }}>DPE</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Filter</button>
            </form>
            <div class="overflow-x-auto">
            <form method="POST" action="{{ route('approval.mass_process') }}" class="w-full">
                @csrf
                <table class="min-w-full divide-y divide-gray-200 text-center">
                <thead class="bg-gray-50">
                        <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase">Nomor Memo</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase">Supir</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase">Kegiatan</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase">Tujuan</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase">Vendor</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase">Nomor Kontainer</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase">Tanggal Checkpoint Supir</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase">Masa</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($permohonans as $permohonan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    <div class="flex items-center justify-center gap-2">
                                        @php $hasCheckpoint = $permohonan->checkpoints && $permohonan->checkpoints->count(); @endphp
                                        <input type="checkbox" name="permohonan_ids[]" value="{{ $permohonan->id }}" class="permohonan-checkbox align-middle" {{ $hasCheckpoint ? '' : 'disabled' }}>
                                        <span class="inline-block min-w-[110px] text-center">{{ $permohonan->nomor_memo }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $permohonan->supir->nama_panggilan ?? '-' }}</td>
                                @php
                                    $kegiatanLabel = \App\Models\MasterKegiatan::where('kode_kegiatan', $permohonan->kegiatan)->value('nama_kegiatan') ?? (isset($permohonan->kegiatan) ? ucfirst($permohonan->kegiatan) : '-');
                                @endphp
                                <td class="px-4 py-3">{{ $kegiatanLabel }}</td>
                                <td class="px-4 py-3">{{ $permohonan->tujuan }}</td>
                                <td class="px-4 py-3">{{ $permohonan->vendor_perusahaan ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if ($permohonan->kontainers && $permohonan->kontainers->count())
                                        <div>
                                            <span class="block text-sm text-gray-700">{{ $permohonan->kontainers->map(function($k) { return $k->nomor_kontainer; })->implode(', ') }}</span>
                                            {{-- send container data (size) along with the permohonan when processing mass approvals --}}
                                            @foreach($permohonan->kontainers as $k)
                                                <input type="hidden" name="kontainers[{{ $permohonan->id }}][{{ $k->nomor_kontainer }}][nomor]" value="{{ $k->nomor_kontainer }}" />
                                                <input type="hidden" name="kontainers[{{ $permohonan->id }}][{{ $k->nomor_kontainer }}][size]" value="{{ $k->size ?? '' }}" />
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $lastCheckpoint = $permohonan->checkpoints?->sortByDesc('tanggal_checkpoint')->first();
                                    @endphp
                                    @if ($lastCheckpoint)
                                        <span class="block text-sm text-gray-700">{{ \Carbon\Carbon::parse($lastCheckpoint->tanggal_checkpoint)->format('d-m-Y') }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($lastCheckpoint)
                                        @php
                                            $start = \Carbon\Carbon::parse($lastCheckpoint->tanggal_checkpoint)->locale('id')->isoFormat('D MMMM YYYY');
                                            $end = \Carbon\Carbon::parse($lastCheckpoint->tanggal_checkpoint)->addMonth()->subDay()->locale('id')->isoFormat('D MMMM YYYY');
                                        @endphp
                                        <span class="block text-sm text-gray-700">{{ $start }} - {{ $end }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        {{ $permohonan->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm font-medium">
                                    @if($permohonan->checkpoints && $permohonan->checkpoints->count())
                                        <a href="{{ route('approval.create', $permohonan) }}" class="inline-block px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg shadow hover:bg-indigo-100 transition">Proses & Selesaikan</a>
                                    @else
                                        <button type="button" class="inline-block px-4 py-2 bg-gray-100 text-gray-400 rounded-lg shadow cursor-not-allowed" disabled>Belum ada checkpoint</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-3 text-center text-sm text-gray-500">
                                    Tidak ada tugas yang perlu diselesaikan saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="9" class="px-4 py-3 text-right">
                                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-md shadow transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 00-4-4H5a4 4 0 014-4h2a4 4 0 014 4v2a4 4 0 01-4 4H9z" /></svg>
                                    Proses Masal
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </form>
        </div>
        <div class="mt-4">
            {{ $permohonans->links() }}
        </div>
    </div>
</div>
@endsection
