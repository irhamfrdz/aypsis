@extends('layouts.app')

@section('title', 'Tambah Master Uang Lembur')
@section('page_title', 'Tambah Master Uang Lembur')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">Tambah Master Tarif Lembur</h2>
                <a href="{{ route('uang-lembur.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    &larr; Kembali
                </a>
            </div>
            
            <div class="p-6">
                @if (session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('uang-lembur.store') }}" method="POST" id="lemburForm">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label for="group" class="block text-sm font-medium text-gray-700">Group <span class="text-red-500">*</span></label>
                            <input type="text" name="group" id="group" value="LEMBUR" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        </div>

                        <div>
                            <label for="sub_group" class="block text-sm font-medium text-gray-700">Sub Group <span class="text-red-500">*</span></label>
                            <input type="text" name="sub_group" id="sub_group" placeholder="Contoh: PELABUHAN 1, GARASI" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Aturan Jam & Tarif</h3>
                            <button type="button" id="btnAddRule" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none">
                                + Tambah Jam
                            </button>
                        </div>

                        <div id="rulesContainer" class="space-y-4">
                            <!-- Template for rules will be inserted here by JS -->
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<template id="ruleTemplate">
    <div class="rule-row grid grid-cols-12 gap-4 items-end bg-gray-50 p-4 rounded-lg border border-gray-200 relative">
        <div class="col-span-12 md:col-span-2">
            <label class="block text-xs font-medium text-gray-700">Tipe Hari</label>
            <select name="rules[__INDEX__][tipe_hari]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                <option value="Hari Biasa">Hari Biasa</option>
                <option value="Hari Libur">Hari Libur</option>
            </select>
        </div>
        
        <div class="col-span-12 md:col-span-2">
            <label class="block text-xs font-medium text-gray-700">Jam Mulai</label>
            <input type="time" name="rules[__INDEX__][jam_mulai]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        
        <div class="col-span-12 md:col-span-2">
            <label class="block text-xs font-medium text-gray-700">Jam Selesai</label>
            <input type="time" name="rules[__INDEX__][jam_selesai]" class="jam-selesai-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>

        <div class="col-span-12 md:col-span-2 pb-2">
            <div class="flex items-center">
                <input type="checkbox" name="rules[__INDEX__][is_sampai_selesai]" value="1" class="is-sampai-selesai h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label class="ml-2 block text-xs text-gray-900 whitespace-nowrap">s/d Selesai</label>
            </div>
        </div>

        <div class="col-span-12 md:col-span-1">
            <label class="block text-xs font-medium text-gray-700">Satuan</label>
            <select name="rules[__INDEX__][satuan]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                <option value="Hari">Hari</option>
                <option value="Jam">Jam</option>
            </select>
        </div>

        <div class="col-span-12 md:col-span-2">
            <label class="block text-xs font-medium text-gray-700">Nominal</label>
            <input type="number" name="rules[__INDEX__][nominal]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
        </div>

        <div class="col-span-12 md:col-span-1 text-right">
            <button type="button" class="btnRemoveRule text-red-600 hover:text-red-900 font-medium text-xs">Hapus</button>
        </div>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('rulesContainer');
        const btnAdd = document.getElementById('btnAddRule');
        const template = document.getElementById('ruleTemplate').innerHTML;
        let ruleIndex = 0;

        function addRule() {
            const html = template.replace(/__INDEX__/g, ruleIndex);
            container.insertAdjacentHTML('beforeend', html);
            
            const newRow = container.lastElementChild;
            
            // Setup checkbox listener for this row
            const checkbox = newRow.querySelector('.is-sampai-selesai');
            const jamSelesai = newRow.querySelector('.jam-selesai-input');
            
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    jamSelesai.value = '';
                    jamSelesai.disabled = true;
                    jamSelesai.classList.add('bg-gray-100');
                } else {
                    jamSelesai.disabled = false;
                    jamSelesai.classList.remove('bg-gray-100');
                }
            });

            // Setup remove listener
            const btnRemove = newRow.querySelector('.btnRemoveRule');
            btnRemove.addEventListener('click', function() {
                if (container.children.length > 1) {
                    newRow.remove();
                } else {
                    alert('Minimal harus ada 1 aturan jam.');
                }
            });

            ruleIndex++;
        }

        // Add first rule on load
        addRule();

        btnAdd.addEventListener('click', addRule);
    });
</script>
@endsection
