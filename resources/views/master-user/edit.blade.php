@extends('layouts.app')

@section('title', 'Edit Pengguna')
@section('page_title', 'Edit Pengguna')

@section('content')

<h2 class="text-xl font-bold text-gray-800 mb-4">Edit Pengguna</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

<form action="{{route('master.user.update', $user->id)}}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- Informasi Pengguna --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
        </div>

        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username <span class="text-red-500">*</span></label>
            <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi Baru</label>
            <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Kata Sandi</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        <div>
            <label for="karyawan_id">Hubungkan dengan karyawan</label>
            <select name="karyawan_id" id="karyawan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">-- Pilih Karyawan --</option>
                @foreach ($karyawans as $karyawan )
                    <option value="{{ $karyawan->id }}" {{ old('karyawan_id', $user->karyawan_id) == $karyawan->id ? 'selected' : '' }}>
                        {{ $karyawan->nama_lengkap }}
                        @if($karyawan->nik)
                            ({{ $karyawan->nik }})
                        @endif
                    </option>
                @endforeach
            </select>
        </div>
    </div>


    {{-- Izin Akses --}}
    <div class="border-t border-gray-200 pt-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Izin Akses</h3>
            <div class="space-y-4">
                @php
                    // Group permissions by first token (support both dot and dash separators)
                    $groupedPermissions = $permissions->groupBy(function($item) {
                        $parts = preg_split('/[\.\-]/', $item->name);
                        return $parts[0] ?? $item->name;
                    });
                @endphp
                @foreach ($groupedPermissions as $groupName => $groupPermissions)
                    <div class="bg-gray-50 p-4 rounded-md">
                        @php
                            // create a safe id for the group checkbox
                            $groupId = 'group-' . preg_replace('/[^a-z0-9]+/i', '-', strtolower($groupName));
                        @endphp
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-bold text-sm text-gray-600">{{ ucfirst($groupName) }}</h4>
                            <label class="inline-flex items-center text-sm text-gray-600">
                                <input type="checkbox" id="{{ $groupId }}" class="mr-2 h-4 w-4 rounded border-gray-300 text-indigo-600 group-toggle" data-group="{{ $groupId }}">
                                Pilih semua
                            </label>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($groupPermissions as $permission)
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="permission-{{ $permission->id }}" name="permissions[]" type="checkbox" value="{{ $permission->id }}" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded perm-checkbox" data-group="{{ $groupId }}"
                                        @if(in_array($permission->id, $userPermissions)) checked @endif>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        @php
                                            // Shorten description if it's the auto-generated "Izin untuk route: ..."
                                            $raw = $permission->description ?? $permission->name;
                                            $short = preg_replace('/^Izin untuk route:\s*/i', '', $raw);
                                            // humanize the name/route: replace dots and dashes with spaces
                                            $short = str_replace(['.', '-'], ' ', $short);
                                            $short = trim($short);
                                            $short = strlen($short) > 0 ? ucfirst($short) : $permission->name;
                                        @endphp
                                        <label for="permission-{{ $permission->id }}" class="font-medium text-gray-700">{{ $short }}</label>
                                        @if(!empty($permission->description) && $permission->description !== $short)
                                            <div class="text-xs text-gray-400">{{ $permission->description }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('master.user.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Perbarui
            </button>
        </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // When a group toggle is clicked, toggle child checkboxes
    document.querySelectorAll('.group-toggle').forEach(function (groupCheckbox) {
        var groupId = groupCheckbox.dataset.group;
        groupCheckbox.addEventListener('change', function () {
            var checked = groupCheckbox.checked;
            document.querySelectorAll('.perm-checkbox[data-group="' + groupId + '"]').forEach(function (cb) {
                cb.checked = checked;
            });
        });
    });

    // When any permission checkbox changes, update its group header state
    function updateGroupState(groupId) {
        var all = Array.from(document.querySelectorAll('.perm-checkbox[data-group="' + groupId + '"]'));
        if (!all.length) return;
        var checked = all.filter(c => c.checked).length;
        var header = document.getElementById(groupId);
        if (!header) return;
        if (checked === 0) {
            header.checked = false;
            header.indeterminate = false;
        } else if (checked === all.length) {
            header.checked = true;
            header.indeterminate = false;
        } else {
            header.checked = false;
            header.indeterminate = true;
        }
    }

    document.querySelectorAll('.perm-checkbox').forEach(function (cb) {
        var groupId = cb.dataset.group;
        cb.addEventListener('change', function () {
            updateGroupState(groupId);
        });
        // initialize state on load
        updateGroupState(cb.dataset.group);
    });
});
</script>
@endpush
