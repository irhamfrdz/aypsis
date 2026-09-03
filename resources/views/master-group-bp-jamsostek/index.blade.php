@extends('layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'Master Group BP Jamsostek')
@section('page_title', 'Master Group BP Jamsostek')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Group BP Jamsostek</h2>
        <div class="flex items-center space-x-3">
            <button type="button" onclick="openModal('create')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1.5 px-3 rounded transition duration-300 text-sm">
                + Tambah Group
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mt-2 list-disc pl-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Group</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-gray-700 text-sm">
                @forelse ($groups as $index => $group)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 font-medium">{{ $group->nama_group }}</td>
                        <td class="px-4 py-2">{{ $group->keterangan ?? '-' }}</td>
                        <td class="px-4 py-2 text-center">
                            @php $s = strtolower($group->status ?? ''); @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $s == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($s) ?: '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-center">
                            <div class="flex items-center justify-center space-x-3 text-sm">
                                <button type="button"
                                        onclick="openModal('edit', {{ json_encode($group) }})"
                                        class="text-blue-600 hover:text-blue-800 font-medium"
                                        title="Edit Data">
                                    Edit
                                </button>
                                <span class="text-gray-300">|</span>
                                <form action="{{ route('master-group-bp-jamsostek.destroy', $group->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium cursor-pointer border-none bg-transparent p-0" title="Hapus Data">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500 text-sm">Tidak ada data group bp jamsostek.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Create/Edit -->
<div id="formModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">Tambah Group BP Jamsostek</h3>
                    <div class="mt-4">
                        <form id="groupForm" method="POST" action="{{ route('master-group-bp-jamsostek.store') }}">
                            @csrf
                            <input type="hidden" name="_method" id="formMethod" value="POST">
                            
                            <div class="mb-4">
                                <label for="nama_group" class="block text-sm font-medium text-gray-700">Nama Group <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_group" id="nama_group" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                            </div>

                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                                <select name="status" id="status" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                                    <option value="aktif">Aktif</option>
                                    <option value="tidak aktif">Tidak Aktif</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border"></textarea>
                            </div>

                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Simpan
                                </button>
                                <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(type, data = null) {
        const modal = document.getElementById('formModal');
        const form = document.getElementById('groupForm');
        const title = document.getElementById('modal-title');
        const methodInput = document.getElementById('formMethod');
        
        modal.classList.remove('hidden');
        
        if (type === 'create') {
            title.textContent = 'Tambah Group BP Jamsostek';
            form.action = "{{ route('master-group-bp-jamsostek.store') }}";
            methodInput.value = 'POST';
            
            document.getElementById('nama_group').value = '';
            document.getElementById('status').value = 'aktif';
            document.getElementById('keterangan').value = '';
        } else if (type === 'edit' && data) {
            title.textContent = 'Edit Group BP Jamsostek';
            // Laravel requires actual ID for the route, since we use resource routes, 
            // the route logic would normally be handled cleanly but in JS we manually append it.
            form.action = `/master-group-bp-jamsostek/${data.id}`;
            methodInput.value = 'PUT';
            
            document.getElementById('nama_group').value = data.nama_group;
            document.getElementById('status').value = data.status || 'aktif';
            document.getElementById('keterangan').value = data.keterangan || '';
        }
    }

    function closeModal() {
        document.getElementById('formModal').classList.add('hidden');
    }
</script>
@endsection
