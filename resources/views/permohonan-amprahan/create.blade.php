@extends('layouts.app')

@section('title', 'Buat Permintaan Amprahan')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-5xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Buat Permintaan Amprahan</h1>
            <p class="text-gray-600 mt-1">Pilih tujuan penggunaan dan masukkan barang yang dibutuhkan.</p>
        </div>
        <a href="{{ route('permohonan-amprahan.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Kembali</a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-5">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('permohonan-amprahan.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Tujuan Permintaan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="jenis_amprahan" class="block text-sm font-medium text-gray-700 mb-1">Jenis Amprahan <span class="text-red-500">*</span></label>
                    <select name="jenis_amprahan" id="jenis_amprahan" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-200">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="kapal" {{ old('jenis_amprahan') === 'kapal' ? 'selected' : '' }}>Kapal</option>
                        <option value="kendaraan" {{ old('jenis_amprahan') === 'kendaraan' ? 'selected' : '' }}>Kendaraan</option>
                        <option value="alat_berat" {{ old('jenis_amprahan') === 'alat_berat' ? 'selected' : '' }}>Alat Berat</option>
                    </select>
                </div>

                <div id="kapal-wrapper" class="hidden">
                    <label for="kapal_id" class="block text-sm font-medium text-gray-700 mb-1">Kapal <span class="text-red-500">*</span></label>
                    <select name="kapal_id" id="kapal_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-200">
                        <option value="">-- Pilih Kapal --</option>
                        @foreach($kapals as $kapal)
                            <option value="{{ $kapal->id }}" {{ old('kapal_id') == $kapal->id ? 'selected' : '' }}>{{ $kapal->nama_kapal }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="mobil-wrapper" class="hidden">
                    <label for="mobil_id" class="block text-sm font-medium text-gray-700 mb-1">Kendaraan <span class="text-red-500">*</span></label>
                    <select name="mobil_id" id="mobil_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-200">
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach($mobils as $mobil)
                            <option value="{{ $mobil->id }}" {{ old('mobil_id') == $mobil->id ? 'selected' : '' }}>
                                {{ $mobil->nomor_polisi ?: $mobil->kode_no }}{{ $mobil->nickname ? ' - '.$mobil->nickname : '' }}{{ $mobil->jenis ? ' ('.$mobil->jenis.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Data kendaraan diambil dari master kendaraan.</p>
                </div>

                <div id="alat-berat-wrapper" class="hidden">
                    <label for="alat_berat_id" class="block text-sm font-medium text-gray-700 mb-1">Alat Berat <span class="text-red-500">*</span></label>
                    <select name="alat_berat_id" id="alat_berat_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-200">
                        <option value="">-- Pilih Alat Berat --</option>
                        @foreach($alatBerats as $alatBerat)
                            <option value="{{ $alatBerat->id }}" {{ old('alat_berat_id') == $alatBerat->id ? 'selected' : '' }}>
                                {{ $alatBerat->nama ?: $alatBerat->kode_alat }}{{ $alatBerat->jenis ? ' ('.$alatBerat->jenis.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="nomor_voyage" class="block text-sm font-medium text-gray-700 mb-1">Nomor Voyage <span class="text-gray-400">(opsional)</span></label>
                    <input type="text" name="nomor_voyage" id="nomor_voyage" value="{{ old('nomor_voyage') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-200" placeholder="Masukkan nomor voyage">
                </div>

                <div>
                    <label for="tujuan_permintaan" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Permintaan Lainnya <span class="text-gray-400">(opsional)</span></label>
                    <input type="text" name="tujuan_permintaan" id="tujuan_permintaan" value="{{ old('tujuan_permintaan') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-200" placeholder="Masukkan tujuan permintaan lainnya">
                </div>

                <div class="md:col-span-2">
                    <label for="keterangan_umum" class="block text-sm font-medium text-gray-700 mb-1">Keterangan Umum</label>
                    <textarea name="keterangan_umum" id="keterangan_umum" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-200" placeholder="Keterangan tambahan...">{{ old('keterangan_umum') }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Barang yang Diminta</h2>
                <button type="button" id="add-item" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg"><i class="fas fa-plus mr-1"></i> Tambah Barang</button>
            </div>
            <div id="items-container" class="space-y-4"></div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('permohonan-amprahan.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg"><i class="fas fa-paper-plane mr-2"></i>Kirim Permintaan</button>
        </div>
    </form>
</div>

<template id="item-template">
    <div class="item-row border border-gray-200 rounded-lg p-4 relative">
        <button type="button" class="remove-item absolute top-3 right-3 text-red-500 hover:text-red-700" title="Hapus barang"><i class="fas fa-trash"></i></button>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pr-8">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                <input type="text" data-field="nama_barang" required class="w-full border-gray-300 rounded-lg shadow-sm" placeholder="Nama barang">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Link Barang</label>
                <input type="url" data-field="link_barang" class="w-full border-gray-300 rounded-lg shadow-sm" placeholder="https://..."></div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                <input type="number" data-field="jumlah" required min="0.01" step="0.01" class="w-full border-gray-300 rounded-lg shadow-sm" value="1">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                <input type="text" data-field="satuan" required class="w-full border-gray-300 rounded-lg shadow-sm" placeholder="pcs, unit, liter, dll.">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <input type="text" data-field="keterangan" class="w-full border-gray-300 rounded-lg shadow-sm" placeholder="Keterangan barang (opsional)">
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('jenis_amprahan');
    const kapalWrapper = document.getElementById('kapal-wrapper');
    const mobilWrapper = document.getElementById('mobil-wrapper');
    const alatBeratWrapper = document.getElementById('alat-berat-wrapper');
    const kapalSelect = document.getElementById('kapal_id');
    const mobilSelect = document.getElementById('mobil_id');
    const alatBeratSelect = document.getElementById('alat_berat_id');
    const container = document.getElementById('items-container');
    const template = document.getElementById('item-template');

    function updateTargetFields() {
        const isKapal = typeSelect.value === 'kapal';
        const isKendaraan = typeSelect.value === 'kendaraan';
        const isAlatBerat = typeSelect.value === 'alat_berat';
        kapalWrapper.classList.toggle('hidden', !isKapal);
        mobilWrapper.classList.toggle('hidden', !isKendaraan);
        alatBeratWrapper.classList.toggle('hidden', !isAlatBerat);
        kapalSelect.required = isKapal;
        mobilSelect.required = isKendaraan;
        alatBeratSelect.required = isAlatBerat;
        if (!isKapal) kapalSelect.value = '';
        if (!isKendaraan) mobilSelect.value = '';
        if (!isAlatBerat) alatBeratSelect.value = '';
    }

    function addItem() {
        const index = container.children.length;
        const row = template.content.cloneNode(true);
        row.querySelectorAll('[data-field]').forEach(function (input) {
            input.name = `items[${index}][${input.dataset.field}]`;
        });
        row.querySelector('.remove-item').addEventListener('click', function () {
            this.closest('.item-row').remove();
            renumberItems();
        });
        container.appendChild(row);
    }

    function renumberItems() {
        container.querySelectorAll('.item-row').forEach(function (row, index) {
            row.querySelectorAll('[data-field]').forEach(function (input) {
                input.name = `items[${index}][${input.dataset.field}]`;
            });
        });
    }

    typeSelect.addEventListener('change', updateTargetFields);
    document.getElementById('add-item').addEventListener('click', addItem);
    updateTargetFields();
    addItem();
});
</script>
@endsection
