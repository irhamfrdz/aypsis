@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-ship mr-3 text-purple-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Naik Kapal</h1>
                    <p class="text-gray-600">Pilih kapal dan nomor voyage untuk melihat data naik kapal</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('naik-kapal.download.template') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-download mr-2"></i>Download Template
                </a>
                <a href="{{ url()->previous() }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Select Form --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('naik-kapal.index') }}" id="naikKapalSelectForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kapal_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Kapal <span class="text-red-500">*</span>
                    </label>
                    <select id="kapal_id" name="kapal_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="">--Pilih Kapal--</option>
                        @php
                            $masterKapals = \App\Models\MasterKapal::orderBy('nama_kapal')->get();
                        @endphp
                        @foreach($masterKapals as $kapal)
                            <option value="{{ $kapal->id }}" {{ request('kapal_id') == $kapal->id ? 'selected' : '' }}>
                                {{ $kapal->nama_kapal }} {{ $kapal->nickname ? '('.$kapal->nickname.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="no_voyage" class="block text-sm font-medium text-gray-700 mb-2">
                        No Voyage <span class="text-red-500">*</span>
                    </label>
                    <select id="no_voyage" name="no_voyage" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>
                    </select>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-md transition duration-200">
                    <i class="fas fa-search mr-2"></i>
                    Lihat Data Naik Kapal
                </button>
            </div>
        </form>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Data Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 resizable-table" id="naikKapalTable">
                <thead class="bg-gray-50">
                    <tr><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Kontainer<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Barang<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Tipe Kontainer<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Kapal & Voyage<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Volume & Tonase<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Tanggal Muat<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Prospek<div class="resize-handle"></div></th><th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th></tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($naikKapals as $naikKapal)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $naikKapal->nomor_kontainer }}</div>
                                <div class="text-sm text-gray-500">{{ $naikKapal->ukuran_kontainer }}</div>
                                @if($naikKapal->no_seal)
                                    <div class="text-xs text-blue-600">Seal: {{ $naikKapal->no_seal }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $naikKapal->jenis_barang ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $naikKapal->tipe_kontainer }}</div>
                                @if($naikKapal->tipe_kontainer_detail)
                                    <div class="text-sm text-gray-500">{{ $naikKapal->tipe_kontainer_detail }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $naikKapal->nama_kapal }}</div>
                                @if($naikKapal->no_voyage)
                                    <div class="text-sm text-gray-500">Voyage: {{ $naikKapal->no_voyage }}</div>
                                @endif
                                @if($naikKapal->pelabuhan_tujuan)
                                    <div class="text-xs text-green-600">→ {{ $naikKapal->pelabuhan_tujuan }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-blue-600">
                                    <i class="fas fa-cube mr-1"></i>
                                    {{ $naikKapal->formatted_volume }} m³
                                </div>
                                <div class="text-sm text-green-600">
                                    <i class="fas fa-weight-hanging mr-1"></i>
                                    {{ $naikKapal->formatted_tonase }} Ton
                                </div>
                                @if($naikKapal->kuantitas)
                                    <div class="text-xs text-gray-500">Qty: {{ number_format($naikKapal->kuantitas) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($naikKapal->tanggal_muat)
                                    <div class="text-sm text-gray-900">{{ $naikKapal->tanggal_muat_formatted }}</div>
                                    @if($naikKapal->jam_muat)
                                        <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($naikKapal->jam_muat)->format('H:i') }}</div>
                                    @endif
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($naikKapal->prospek)
                                    <div class="text-sm text-gray-900">{{ $naikKapal->prospek->nama_supir }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $naikKapal->prospek->id }}</div>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('naik-kapal.show', $naikKapal) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('naik-kapal.edit', $naikKapal) }}" class="text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('naik-kapal.destroy', $naikKapal) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin menghapus data naik kapal ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                <i class="fas fa-ship text-4xl mb-4 text-gray-300"></i>
                                <p class="text-lg">Belum ada data naik kapal</p>
                                <p class="text-sm">Data naik kapal akan muncul ketika tersedia</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($naikKapals->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                @include('components.modern-pagination', ['paginator' => $naikKapals])
                @include('components.rows-per-page')
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kapalSelect = document.getElementById('kapal_id');
    const voyageSelect = document.getElementById('no_voyage');
    const selectedVoyage = "{{ request('no_voyage') }}";

    // Load voyages if kapal is already selected
    if (kapalSelect.value) {
        loadVoyages(kapalSelect.value, selectedVoyage);
    }

    kapalSelect.addEventListener('change', function() {
        const kapalId = this.value;
        loadVoyages(kapalId);
    });

    function loadVoyages(kapalId, selectVoyage = '') {
        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        voyageSelect.disabled = true;

        if (!kapalId) {
            voyageSelect.innerHTML = '<option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>';
            voyageSelect.disabled = false;
            return;
        }

        fetch(`{{ route('prospek.get-voyage-by-kapal') }}?kapal_id=${kapalId}`, {
            method: 'GET',
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            voyageSelect.innerHTML = '';
            if (data.success && data.voyages && data.voyages.length) {
                voyageSelect.innerHTML = '<option value="">--Pilih Voyage--</option>';
                data.voyages.forEach(v => {
                    const selected = selectVoyage === v ? 'selected' : '';
                    voyageSelect.innerHTML += `<option value="${v}" ${selected}>${v}</option>`;
                });
            } else {
                voyageSelect.innerHTML = '<option value="">Belum ada voyage untuk kapal ini</option>';
            }
            voyageSelect.disabled = false;
        })
        .catch(err => {
            voyageSelect.innerHTML = '<option value="">Error loading voyage</option>';
            voyageSelect.disabled = false;
            console.error(err);
        });
    }
});
</script>

@endsection

@include('components.resizable-table')

@push('scripts')
<script>
$(document).ready(function() {
    initResizableTable('naikKapalTable');
});
</script>
@endpush