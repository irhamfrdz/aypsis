@extends('layouts.app')

@section('title', 'Master Rumus BPJS')
@section('page_title', 'Master Rumus BPJS')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6" style="font-family: Arial, sans-serif; font-size: 13px;">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Master Rumus BPJS</h2>
        <button type="button" onclick="openModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 flex items-center text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Rumus
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Group JKN</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 text-xs uppercase tracking-wider">
                        <th class="px-4 py-3 border-b w-10 text-center">No</th>
                        <th class="px-4 py-3 border-b">Group Name</th>
                        <th class="px-4 py-3 border-b text-center">Tunjangan (%)</th>
                        <th class="px-4 py-3 border-b text-center">Hutang (%)</th>
                        <th class="px-4 py-3 border-b text-center">Biaya (%)</th>
                        <th class="px-4 py-3 border-b">Keterangan / Custom</th>
                        <th class="px-4 py-3 border-b text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600">
                    @forelse($rumusJkn as $index => $item)
                        <tr class="hover:bg-gray-50 transition duration-150 border-b">
                            <td class="px-4 py-3 text-center">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $item->group_name }}</td>
                            <td class="px-4 py-3 text-center font-medium">{{ $item->tunjangan_persen ? $item->tunjangan_persen . '%' : '-' }}</td>
                            <td class="px-4 py-3 text-center font-medium">{{ $item->hutang_persen ? $item->hutang_persen . '%' : '-' }}</td>
                            <td class="px-4 py-3 text-center font-medium">{{ $item->biaya_persen ? $item->biaya_persen . '%' : '-' }}</td>
                            <td class="px-4 py-3">{{ $item->keterangan_custom ?: '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button onclick="editModal({{ $item->toJson() }})" class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('master-rumus-bpjs.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-4 text-center text-gray-500">Belum ada data Group JKN.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Group BP Jamsostek</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 text-xs uppercase tracking-wider">
                        <th class="px-4 py-3 border-b w-10 text-center">No</th>
                        <th class="px-4 py-3 border-b">Group Name</th>
                        <th class="px-4 py-3 border-b text-center">Tunjangan (%)</th>
                        <th class="px-4 py-3 border-b text-center">Hutang (%)</th>
                        <th class="px-4 py-3 border-b text-center">Biaya (%)</th>
                        <th class="px-4 py-3 border-b">Keterangan / Custom</th>
                        <th class="px-4 py-3 border-b text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600">
                    @forelse($rumusJamsostek as $index => $item)
                        <tr class="hover:bg-gray-50 transition duration-150 border-b">
                            <td class="px-4 py-3 text-center">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $item->group_name }}</td>
                            <td class="px-4 py-3 text-center font-medium">{{ $item->tunjangan_persen ? $item->tunjangan_persen . '%' : '-' }}</td>
                            <td class="px-4 py-3 text-center font-medium">{{ $item->hutang_persen ? $item->hutang_persen . '%' : '-' }}</td>
                            <td class="px-4 py-3 text-center font-medium">{{ $item->biaya_persen ? $item->biaya_persen . '%' : '-' }}</td>
                            <td class="px-4 py-3">{{ $item->keterangan_custom ?: '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button onclick="editModal({{ $item->toJson() }})" class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('master-rumus-bpjs.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-4 text-center text-gray-500">Belum ada data Group BP Jamsostek.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="formModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form id="modalForm" method="POST" action="{{ route('master-rumus-bpjs.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modalTitle">Tambah Rumus BPJS</h3>
                    
                    <div class="mb-4">
                        <label for="jenis" class="block text-sm font-medium text-gray-700 mb-1">Jenis BPJS</label>
                        <select id="jenis" name="jenis" class="form-select w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="jkn">Group JKN</option>
                            <option value="jamsostek">Group BP Jamsostek</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="group_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Group</label>
                        <input type="text" id="group_name" name="group_name" class="form-input w-full border-gray-300 rounded-md shadow-sm" required placeholder="Contoh: JKN-KIS-HARIAN">
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label for="tunjangan_persen" class="block text-sm font-medium text-gray-700 mb-1">Tunjangan (%)</label>
                            <input type="number" id="tunjangan_persen" name="tunjangan_persen" class="form-input w-full border-gray-300 rounded-md shadow-sm" step="0.01" min="0" placeholder="Cth: 4">
                        </div>
                        <div>
                            <label for="hutang_persen" class="block text-sm font-medium text-gray-700 mb-1">Hutang (%)</label>
                            <input type="number" id="hutang_persen" name="hutang_persen" class="form-input w-full border-gray-300 rounded-md shadow-sm" step="0.01" min="0" placeholder="Cth: 1">
                        </div>
                        <div>
                            <label for="biaya_persen" class="block text-sm font-medium text-gray-700 mb-1">Biaya (%)</label>
                            <input type="number" id="biaya_persen" name="biaya_persen" class="form-input w-full border-gray-300 rounded-md shadow-sm" step="0.01" min="0" placeholder="Cth: 5">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="keterangan_custom" class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Rumus Custom</label>
                        <textarea id="keterangan_custom" name="keterangan_custom" class="form-input w-full border-gray-300 rounded-md shadow-sm" rows="2" placeholder="Cth: Maksimal (2 Orang Tua 3 Anak @ 35.000) / 2"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Kosongkan kolom persentase di atas jika group ini menggunakan perhitungan custom / manual.</p>
                    </div>

                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan
                    </button>
                    <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('modalForm').action = "{{ route('master-rumus-bpjs.store') }}";
        document.getElementById('formMethod').value = "POST";
        document.getElementById('modalTitle').innerText = "Tambah Rumus BPJS";
        
        document.getElementById('jenis').value = 'jkn';
        document.getElementById('group_name').value = '';
        document.getElementById('tunjangan_persen').value = '';
        document.getElementById('hutang_persen').value = '';
        document.getElementById('biaya_persen').value = '';
        document.getElementById('keterangan_custom').value = '';

        document.getElementById('formModal').classList.remove('hidden');
    }

    function editModal(data) {
        document.getElementById('modalForm').action = "/master-rumus-bpjs/" + data.id;
        document.getElementById('formMethod').value = "PUT";
        document.getElementById('modalTitle').innerText = "Edit Rumus BPJS";
        
        document.getElementById('jenis').value = data.jenis;
        document.getElementById('group_name').value = data.group_name;
        document.getElementById('tunjangan_persen').value = data.tunjangan_persen;
        document.getElementById('hutang_persen').value = data.hutang_persen;
        document.getElementById('biaya_persen').value = data.biaya_persen;
        document.getElementById('keterangan_custom').value = data.keterangan_custom;

        document.getElementById('formModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('formModal').classList.add('hidden');
    }
</script>
@endsection
