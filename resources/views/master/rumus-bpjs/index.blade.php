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

    @php
        $rumusJamsostekBpu = $rumusJamsostek->filter(fn($item) => !str_contains(strtoupper($item->group_name), 'PPU'));
        $rumusJamsostekPpu = $rumusJamsostek->filter(fn($item) => str_contains(strtoupper($item->group_name), 'PPU'));
    @endphp

    <div>
        <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Group BP Jamsostek (BPU)</h3>
        <div class="overflow-x-auto mb-8">
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
                    @forelse($rumusJamsostekBpu as $index => $item)
                        <tr class="hover:bg-gray-50 transition duration-150 border-b">
                            <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $item->group_name }}
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">BPU</span>
                            </td>
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
                            <td colspan="7" class="px-4 py-4 text-center text-gray-500">Belum ada data Group BP Jamsostek BPU.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Group BP Jamsostek (PPU)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 text-xs uppercase tracking-wider">
                        <th class="px-3 py-3 border-b w-10 text-center">No</th>
                        <th class="px-3 py-3 border-b">Group Name</th>
                        <th class="px-3 py-3 border-b text-center">JHT 3.7% Biaya</th>
                        <th class="px-3 py-3 border-b text-center">JHT 2% Hutang</th>
                        <th class="px-3 py-3 border-b text-center">JKK 0.24% Tunjangan</th>
                        <th class="px-3 py-3 border-b text-center">JKM 0.3% Tunjangan</th>
                        <th class="px-3 py-3 border-b text-center">JP 2% Biaya</th>
                        <th class="px-3 py-3 border-b text-center">JP 1% Hutang</th>
                        <th class="px-3 py-3 border-b text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600">
                    @forelse($rumusJamsostekPpu as $index => $item)
                        <tr class="hover:bg-gray-50 transition duration-150 border-b text-sm">
                            <td class="px-3 py-3 text-center">{{ $loop->iteration }}</td>
                            <td class="px-3 py-3 font-medium text-gray-800">
                                {{ $item->group_name }}
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">PPU</span>
                            </td>
                            <td class="px-3 py-3 text-center font-medium">{{ $item->jht_biaya ? $item->jht_biaya . '%' : '-' }}</td>
                            <td class="px-3 py-3 text-center font-medium">{{ $item->jht_hutang ? $item->jht_hutang . '%' : '-' }}</td>
                            <td class="px-3 py-3 text-center font-medium">{{ $item->jkk_tunjangan ? $item->jkk_tunjangan . '%' : '-' }}</td>
                            <td class="px-3 py-3 text-center font-medium">{{ $item->jkm_tunjangan ? $item->jkm_tunjangan . '%' : '-' }}</td>
                            <td class="px-3 py-3 text-center font-medium">{{ $item->jp_biaya ? $item->jp_biaya . '%' : '-' }}</td>
                            <td class="px-3 py-3 text-center font-medium">{{ $item->jp_hutang ? $item->jp_hutang . '%' : '-' }}</td>
                            <td class="px-3 py-3 text-center">
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
                            <td colspan="9" class="px-4 py-4 text-center text-gray-500">Belum ada data Group BP Jamsostek PPU.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Tambah -->
<div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeCreateModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
            <form id="createForm" method="POST" action="{{ route('master-rumus-bpjs.store') }}">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[80vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Tambah Rumus BPJS</h3>
                        <button type="button" onclick="addRow()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-3 rounded text-sm transition duration-150">
                            <i class="fas fa-plus mr-1"></i> Tambah Baris
                        </button>
                    </div>
                    
                    <div id="dynamic-rows-container">
                        <div class="bpjs-row border border-gray-200 p-4 rounded-lg mb-4 relative bg-gray-50">
                            <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 hidden remove-row-btn" onclick="removeRow(this)" title="Hapus Baris">
                                <i class="fas fa-times"></i>
                            </button>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis BPJS</label>
                                    <select name="jenis[]" class="form-select w-full border-gray-300 rounded-md shadow-sm" required>
                                        <option value="jkn">Group JKN</option>
                                        <option value="jamsostek">Group BP Jamsostek</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Group</label>
                                    <input type="text" name="group_name[]" class="form-input w-full border-gray-300 rounded-md shadow-sm" required placeholder="Contoh: JKN-KIS-HARIAN" oninput="togglePpuFields(this)">
                                </div>
                            </div>

                            <div class="bpu-fields grid grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tunjangan (%)</label>
                                    <input type="number" name="tunjangan_persen[]" class="form-input w-full border-gray-300 rounded-md shadow-sm" step="0.01" min="0" placeholder="Cth: 4">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Hutang (%)</label>
                                    <input type="number" name="hutang_persen[]" class="form-input w-full border-gray-300 rounded-md shadow-sm" step="0.01" min="0" placeholder="Cth: 1">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya (%)</label>
                                    <input type="number" name="biaya_persen[]" class="form-input w-full border-gray-300 rounded-md shadow-sm" step="0.01" min="0" placeholder="Cth: 5">
                                </div>
                            </div>
                            
                            <div class="ppu-fields hidden mb-4 bg-emerald-50 p-3 rounded-md border border-emerald-100">
                                <p class="text-xs font-semibold text-emerald-800 mb-2">Variabel PPU Jamsostek</p>
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">JHT Biaya (%)</label>
                                        <input type="number" name="jht_biaya[]" class="form-input w-full border-gray-300 rounded-md shadow-sm text-sm" step="0.01" min="0" placeholder="Cth: 3.7">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">JHT Hutang (%)</label>
                                        <input type="number" name="jht_hutang[]" class="form-input w-full border-gray-300 rounded-md shadow-sm text-sm" step="0.01" min="0" placeholder="Cth: 2">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">JKK Tunjangan (%)</label>
                                        <input type="number" name="jkk_tunjangan[]" class="form-input w-full border-gray-300 rounded-md shadow-sm text-sm" step="0.01" min="0" placeholder="Cth: 0.24">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">JKM Tunjangan (%)</label>
                                        <input type="number" name="jkm_tunjangan[]" class="form-input w-full border-gray-300 rounded-md shadow-sm text-sm" step="0.01" min="0" placeholder="Cth: 0.3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">JP Biaya (%)</label>
                                        <input type="number" name="jp_biaya[]" class="form-input w-full border-gray-300 rounded-md shadow-sm text-sm" step="0.01" min="0" placeholder="Cth: 2">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">JP Hutang (%)</label>
                                        <input type="number" name="jp_hutang[]" class="form-input w-full border-gray-300 rounded-md shadow-sm text-sm" step="0.01" min="0" placeholder="Cth: 1">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Rumus Custom</label>
                                <textarea name="keterangan_custom[]" class="form-input w-full border-gray-300 rounded-md shadow-sm" rows="1" placeholder="Cth: Maksimal (2 Orang Tua 3 Anak @ 35.000) / 2"></textarea>
                                <p class="text-xs text-gray-500 mt-1">Kosongkan kolom persentase jika menggunakan perhitungan custom.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan
                    </button>
                    <button type="button" onclick="closeCreateModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Form Edit -->
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeEditModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Rumus BPJS</h3>
                    
                    <div class="mb-4">
                        <label for="edit_jenis" class="block text-sm font-medium text-gray-700 mb-1">Jenis BPJS</label>
                        <select id="edit_jenis" name="jenis" class="form-select w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="jkn">Group JKN</option>
                            <option value="jamsostek">Group BP Jamsostek</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="edit_group_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Group</label>
                        <input type="text" id="edit_group_name" name="group_name" class="form-input w-full border-gray-300 rounded-md shadow-sm" required placeholder="Contoh: JKN-KIS-HARIAN" oninput="toggleEditPpuFields(this)">
                    </div>

                    <div id="edit_bpu_fields" class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label for="edit_tunjangan_persen" class="block text-sm font-medium text-gray-700 mb-1">Tunjangan (%)</label>
                            <input type="number" id="edit_tunjangan_persen" name="tunjangan_persen" class="form-input w-full border-gray-300 rounded-md shadow-sm" step="0.01" min="0" placeholder="Cth: 4">
                        </div>
                        <div>
                            <label for="edit_hutang_persen" class="block text-sm font-medium text-gray-700 mb-1">Hutang (%)</label>
                            <input type="number" id="edit_hutang_persen" name="hutang_persen" class="form-input w-full border-gray-300 rounded-md shadow-sm" step="0.01" min="0" placeholder="Cth: 1">
                        </div>
                        <div>
                            <label for="edit_biaya_persen" class="block text-sm font-medium text-gray-700 mb-1">Biaya (%)</label>
                            <input type="number" id="edit_biaya_persen" name="biaya_persen" class="form-input w-full border-gray-300 rounded-md shadow-sm" step="0.01" min="0" placeholder="Cth: 5">
                        </div>
                    </div>
                    
                    <div id="edit_ppu_fields" class="hidden mb-4 bg-emerald-50 p-3 rounded-md border border-emerald-100">
                        <p class="text-xs font-semibold text-emerald-800 mb-2">Variabel PPU Jamsostek</p>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label for="edit_jht_biaya" class="block text-xs font-medium text-gray-700 mb-1">JHT Biaya (%)</label>
                                <input type="number" id="edit_jht_biaya" name="jht_biaya" class="form-input w-full border-gray-300 rounded-md shadow-sm text-sm" step="0.01" min="0" placeholder="Cth: 3.7">
                            </div>
                            <div>
                                <label for="edit_jht_hutang" class="block text-xs font-medium text-gray-700 mb-1">JHT Hutang (%)</label>
                                <input type="number" id="edit_jht_hutang" name="jht_hutang" class="form-input w-full border-gray-300 rounded-md shadow-sm text-sm" step="0.01" min="0" placeholder="Cth: 2">
                            </div>
                            <div>
                                <label for="edit_jkk_tunjangan" class="block text-xs font-medium text-gray-700 mb-1">JKK Tunjangan (%)</label>
                                <input type="number" id="edit_jkk_tunjangan" name="jkk_tunjangan" class="form-input w-full border-gray-300 rounded-md shadow-sm text-sm" step="0.01" min="0" placeholder="Cth: 0.24">
                            </div>
                            <div>
                                <label for="edit_jkm_tunjangan" class="block text-xs font-medium text-gray-700 mb-1">JKM Tunjangan (%)</label>
                                <input type="number" id="edit_jkm_tunjangan" name="jkm_tunjangan" class="form-input w-full border-gray-300 rounded-md shadow-sm text-sm" step="0.01" min="0" placeholder="Cth: 0.3">
                            </div>
                            <div>
                                <label for="edit_jp_biaya" class="block text-xs font-medium text-gray-700 mb-1">JP Biaya (%)</label>
                                <input type="number" id="edit_jp_biaya" name="jp_biaya" class="form-input w-full border-gray-300 rounded-md shadow-sm text-sm" step="0.01" min="0" placeholder="Cth: 2">
                            </div>
                            <div>
                                <label for="edit_jp_hutang" class="block text-xs font-medium text-gray-700 mb-1">JP Hutang (%)</label>
                                <input type="number" id="edit_jp_hutang" name="jp_hutang" class="form-input w-full border-gray-300 rounded-md shadow-sm text-sm" step="0.01" min="0" placeholder="Cth: 1">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="edit_keterangan_custom" class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Rumus Custom</label>
                        <textarea id="edit_keterangan_custom" name="keterangan_custom" class="form-input w-full border-gray-300 rounded-md shadow-sm" rows="2" placeholder="Cth: Maksimal (2 Orang Tua 3 Anak @ 35.000) / 2"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Kosongkan kolom persentase di atas jika group ini menggunakan perhitungan custom / manual.</p>
                    </div>

                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan Perubahan
                    </button>
                    <button type="button" onclick="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal() {
        const container = document.getElementById('dynamic-rows-container');
        const rows = container.getElementsByClassName('bpjs-row');
        while(rows.length > 1) {
            rows[1].remove();
        }
        
        const firstRow = rows[0];
        firstRow.querySelector('select[name="jenis[]"]').value = 'jkn';
        firstRow.querySelector('input[name="group_name[]"]').value = '';
        firstRow.querySelector('input[name="tunjangan_persen[]"]').value = '';
        firstRow.querySelector('input[name="hutang_persen[]"]').value = '';
        firstRow.querySelector('input[name="biaya_persen[]"]').value = '';
        firstRow.querySelector('textarea[name="keterangan_custom[]"]').value = '';
        firstRow.querySelector('input[name="jht_biaya[]"]').value = '';
        firstRow.querySelector('input[name="jht_hutang[]"]').value = '';
        firstRow.querySelector('input[name="jkk_tunjangan[]"]').value = '';
        firstRow.querySelector('input[name="jkm_tunjangan[]"]').value = '';
        firstRow.querySelector('input[name="jp_biaya[]"]').value = '';
        firstRow.querySelector('input[name="jp_hutang[]"]').value = '';
        
        togglePpuFields(firstRow.querySelector('input[name="group_name[]"]'));
        updateRemoveButtons();
        
        document.getElementById('createModal').classList.remove('hidden');
    }
    
    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    function addRow() {
        const container = document.getElementById('dynamic-rows-container');
        const firstRow = container.querySelector('.bpjs-row');
        const newRow = firstRow.cloneNode(true);
        
        newRow.querySelector('select[name="jenis[]"]').value = 'jkn';
        newRow.querySelector('input[name="group_name[]"]').value = '';
        newRow.querySelector('input[name="tunjangan_persen[]"]').value = '';
        newRow.querySelector('input[name="hutang_persen[]"]').value = '';
        newRow.querySelector('input[name="biaya_persen[]"]').value = '';
        newRow.querySelector('textarea[name="keterangan_custom[]"]').value = '';
        newRow.querySelector('input[name="jht_biaya[]"]').value = '';
        newRow.querySelector('input[name="jht_hutang[]"]').value = '';
        newRow.querySelector('input[name="jkk_tunjangan[]"]').value = '';
        newRow.querySelector('input[name="jkm_tunjangan[]"]').value = '';
        newRow.querySelector('input[name="jp_biaya[]"]').value = '';
        newRow.querySelector('input[name="jp_hutang[]"]').value = '';
        
        container.appendChild(newRow);
        togglePpuFields(newRow.querySelector('input[name="group_name[]"]'));
        updateRemoveButtons();
    }
    
    function removeRow(btn) {
        const row = btn.closest('.bpjs-row');
        const container = document.getElementById('dynamic-rows-container');
        if (container.querySelectorAll('.bpjs-row').length > 1) {
            row.remove();
            updateRemoveButtons();
        }
    }
    
    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.bpjs-row');
        rows.forEach(row => {
            const btn = row.querySelector('.remove-row-btn');
            if (rows.length > 1) {
                btn.classList.remove('hidden');
            } else {
                btn.classList.add('hidden');
            }
        });
    }

    function editModal(data) {
        document.getElementById('editForm').action = `/master-rumus-bpjs/${data.id}`;
        
        document.getElementById('edit_jenis').value = data.jenis;
        document.getElementById('edit_group_name').value = data.group_name;
        document.getElementById('edit_tunjangan_persen').value = data.tunjangan_persen;
        document.getElementById('edit_hutang_persen').value = data.hutang_persen;
        document.getElementById('edit_biaya_persen').value = data.biaya_persen;
        document.getElementById('edit_keterangan_custom').value = data.keterangan_custom;
        
        document.getElementById('edit_jht_biaya').value = data.jht_biaya;
        document.getElementById('edit_jht_hutang').value = data.jht_hutang;
        document.getElementById('edit_jkk_tunjangan').value = data.jkk_tunjangan;
        document.getElementById('edit_jkm_tunjangan').value = data.jkm_tunjangan;
        document.getElementById('edit_jp_biaya').value = data.jp_biaya;
        document.getElementById('edit_jp_hutang').value = data.jp_hutang;
        
        toggleEditPpuFields(document.getElementById('edit_group_name'));

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function togglePpuFields(input) {
        const row = input.closest('.bpjs-row');
        const ppuFields = row.querySelector('.ppu-fields');
        const bpuFields = row.querySelector('.bpu-fields');
        if (input.value.toUpperCase().includes('PPU')) {
            ppuFields.classList.remove('hidden');
            bpuFields.classList.add('hidden');
        } else {
            ppuFields.classList.add('hidden');
            bpuFields.classList.remove('hidden');
        }
    }

    function toggleEditPpuFields(input) {
        const ppuFields = document.getElementById('edit_ppu_fields');
        const bpuFields = document.getElementById('edit_bpu_fields');
        if (input.value.toUpperCase().includes('PPU')) {
            ppuFields.classList.remove('hidden');
            bpuFields.classList.add('hidden');
        } else {
            ppuFields.classList.add('hidden');
            bpuFields.classList.remove('hidden');
        }
    }
</script>
@endsection
