@extends('layouts.app')

@section('title', 'Tambah Data Uang Makan')
@section('page_title', 'Tambah Data Uang Makan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Tambah Data Uang Makan</h2>
            </div>
            
            <div class="p-6">
                <form action="{{ route('uang-makan.store') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="filter_penempatan" class="block text-sm font-medium text-gray-700">Filter Penempatan</label>
                                <select id="filter_penempatan" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">Semua Penempatan</option>
                                    @foreach($penempatans as $penempatan)
                                        <option value="{{ $penempatan }}">{{ $penempatan }}</option>
                                    @endforeach
                                </select>
                                
                                <label for="filter_tunjangan" class="block text-sm font-medium text-gray-700 mt-4">Filter Tunjangan</label>
                                <select id="filter_tunjangan" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">Semua Tunjangan</option>
                                    <option value="UANG MAKAN">Uang Makan</option>
                                    <option value="TRANSPORTASI">Transportasi</option>
                                    <option value="BPJS">BPJS</option>
                                    <option value="CUTI TAHUNAN">Cuti Tahunan</option>
                                </select>
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-sm font-medium text-gray-700">Karyawan</label>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="check_all_karyawan" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="check_all_karyawan" class="ml-2 block text-xs text-gray-600">Pilih Semua</label>
                                    </div>
                                </div>
                                <div class="mb-2 mt-1">
                                    <input type="text" id="search_karyawan" placeholder="Cari nama karyawan atau NIK..." class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="mt-1 border border-gray-300 rounded-md p-3 h-64 overflow-y-auto bg-white" id="karyawan_list_container">
                                    @foreach($karyawans as $karyawan)
                                        <div class="flex items-center py-2 border-b border-gray-100 last:border-0 karyawan-item" data-penempatan="{{ $karyawan->penempatan }}" data-tunjangan="{{ implode(',', (array)$karyawan->tunjangan) }}" data-search="{{ strtolower($karyawan->nama_lengkap . ' ' . $karyawan->nik) }}">
                                            <input type="checkbox" name="karyawan_id[]" value="{{ $karyawan->id }}" id="karyawan_{{ $karyawan->id }}" data-nominal="{{ $karyawan->nominal_uang_makan ?? 0 }}" class="karyawan-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ (is_array(old('karyawan_id')) && in_array($karyawan->id, old('karyawan_id'))) ? 'checked' : '' }}>
                                            <label for="karyawan_{{ $karyawan->id }}" class="ml-3 block text-sm font-medium text-gray-700">
                                                {{ $karyawan->nama_lengkap }} <span class="text-xs text-gray-500 font-normal">({{ $karyawan->nik }})</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('karyawan_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                                <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('tanggal')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="nominal" class="block text-sm font-medium text-gray-700">Nominal (Rp)</label>
                                <input type="number" name="nominal" id="nominal" value="{{ old('nominal', 0) }}" required min="0" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('nominal')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                            <textarea id="keterangan" name="keterangan" rows="3" class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('uang-makan.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const penempatanSelect = document.getElementById('filter_penempatan');
        const tunjanganSelect = document.getElementById('filter_tunjangan');
        const searchInput = document.getElementById('search_karyawan');
        const karyawanItems = document.querySelectorAll('.karyawan-item');
        const checkAllBox = document.getElementById('check_all_karyawan');
        const karyawanCheckboxes = document.querySelectorAll('.karyawan-checkbox');
        const nominalInput = document.getElementById('nominal');

        function updateCheckAllState() {
            const visibleCheckboxes = Array.from(karyawanCheckboxes).filter(cb => cb.closest('.karyawan-item').style.display !== 'none');
            if (visibleCheckboxes.length === 0) {
                checkAllBox.checked = false;
                checkAllBox.indeterminate = false;
                return;
            }
            const allChecked = visibleCheckboxes.every(cb => cb.checked);
            const someChecked = visibleCheckboxes.some(cb => cb.checked);
            checkAllBox.checked = allChecked;
            checkAllBox.indeterminate = someChecked && !allChecked;
        }

        function applyFilters() {
            const selectedPenempatan = penempatanSelect.value;
            const selectedTunjangan = tunjanganSelect.value;
            const searchKeyword = searchInput.value.toLowerCase();
            
            karyawanItems.forEach(item => {
                const matchesPenempatan = (selectedPenempatan === "" || item.dataset.penempatan === selectedPenempatan);
                const matchesTunjangan = (selectedTunjangan === "" || item.dataset.tunjangan.includes(selectedTunjangan));
                const matchesSearch = (searchKeyword === "" || item.dataset.search.includes(searchKeyword));
                
                if (matchesPenempatan && matchesTunjangan && matchesSearch) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                    // Uncheck if hidden by filter
                    const cb = item.querySelector('.karyawan-checkbox');
                    if (cb) cb.checked = false;
                }
            });
            updateCheckAllState();
        }

        penempatanSelect.addEventListener('change', applyFilters);
        tunjanganSelect.addEventListener('change', applyFilters);
        searchInput.addEventListener('input', applyFilters);

        checkAllBox.addEventListener('change', function() {
            const isChecked = this.checked;
            karyawanCheckboxes.forEach(cb => {
                if (cb.closest('.karyawan-item').style.display !== 'none') {
                    cb.checked = isChecked;
                }
            });
            // Update nominal if only one is selected or if we want to take the first one
            updateNominalFromFirstChecked();
        });

        karyawanCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateCheckAllState();
                updateNominalFromFirstChecked();
            });
        });

        function updateNominalFromFirstChecked() {
            if (!nominalInput) return;
            const firstChecked = document.querySelector('.karyawan-checkbox:checked');
            if (firstChecked && firstChecked.dataset.nominal) {
                nominalInput.value = firstChecked.dataset.nominal;
            }
        }
    });
</script>
@endpush
