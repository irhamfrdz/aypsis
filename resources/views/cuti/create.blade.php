@extends('layouts.app')

@section('title', 'Tambah Data Cuti')
@section('page_title', 'Tambah Data Cuti')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Tambah Data Cuti</h2>
            </div>
            
            <div class="p-6">
                <form action="{{ route('cuti.store') }}" method="POST">
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
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-sm font-medium text-gray-700">Karyawan</label>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="check_all_karyawan" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="check_all_karyawan" class="ml-2 block text-xs text-gray-600">Pilih Semua</label>
                                    </div>
                                </div>
                                <div class="mt-1 border border-gray-300 rounded-md p-3 h-64 overflow-y-auto bg-white" id="karyawan_list_container">
                                    @foreach($karyawans as $karyawan)
                                        <div class="flex items-center py-2 border-b border-gray-100 last:border-0 karyawan-item" data-penempatan="{{ $karyawan->penempatan }}">
                                            <input type="checkbox" name="karyawan_id[]" value="{{ $karyawan->id }}" id="karyawan_{{ $karyawan->id }}" class="karyawan-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ (is_array(old('karyawan_id')) && in_array($karyawan->id, old('karyawan_id'))) ? 'checked' : '' }}>
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
                                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('tanggal_mulai')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('tanggal_selesai')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="jenis_cuti" class="block text-sm font-medium text-gray-700">Jenis Cuti</label>
                            <input type="text" name="jenis_cuti" id="jenis_cuti" value="{{ old('jenis_cuti') }}" required placeholder="Contoh: Cuti Tahunan, Sakit, Izin" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('jenis_cuti')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                        <a href="{{ route('cuti.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
        const karyawanItems = document.querySelectorAll('.karyawan-item');
        const checkAllBox = document.getElementById('check_all_karyawan');
        const karyawanCheckboxes = document.querySelectorAll('.karyawan-checkbox');

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

        penempatanSelect.addEventListener('change', function() {
            const selectedPenempatan = this.value;
            
            karyawanItems.forEach(item => {
                if (selectedPenempatan === "" || item.dataset.penempatan === selectedPenempatan) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                    // Uncheck if hidden by filter
                    const cb = item.querySelector('.karyawan-checkbox');
                    if (cb) cb.checked = false;
                }
            });
            updateCheckAllState();
        });

        checkAllBox.addEventListener('change', function() {
            const isChecked = this.checked;
            karyawanCheckboxes.forEach(cb => {
                if (cb.closest('.karyawan-item').style.display !== 'none') {
                    cb.checked = isChecked;
                }
            });
        });

        karyawanCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateCheckAllState();
            });
        });
    });
</script>
@endpush
