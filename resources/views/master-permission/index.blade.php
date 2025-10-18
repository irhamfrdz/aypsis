@extends('layouts.app')

@section('title', 'Master Izin')
@section('page_title', 'Master Izin')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Master Izin Akses</h1>
                    <p class="mt-1 text-sm text-gray-600">Kelola izin akses sistem secara terpusat</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="syncPermissions()"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-sync mr-2"></i>
                        Sinkronisasi
                    </button>
                    <a href="{{ route('master.permission.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Izin
                    </a>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="mx-6 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mx-6 mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filters and Search -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <div class="flex-1 max-w-lg">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text"
                               id="searchPermissions"
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="Cari nama izin atau deskripsi...">
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <select id="filterGroup"
                            class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Semua Grup</option>
                        <option value="user">User Management</option>
                        <option value="payment">Payment</option>
                        <option value="pranota">Pranota</option>
                        <option value="report">Report</option>
                        <option value="system">System</option>
                    </select>
                    <span class="text-sm text-gray-500">
                        Total: <span id="totalCount">{{ $permissions->total() ?? 0 }}</span> izin
                    </span>
                </div>
            </div>
        </div>

        {{-- Rows Per Page Selection --}}
        @include('components.rows-per-page', [
            'routeName' => 'master.permission.index',
            'paginator' => $permissions,
            'entityName' => 'izin',
            'entityNamePlural' => 'izin'
        ])

        <div class="p-6">
            <!-- Permission Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Izin (Key)
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Deskripsi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Grup
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pengguna
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="permissionsTableBody">
                        @forelse ($permissions as $index => $permission)
                            <tr class="hover:bg-gray-50 permission-row" data-permission-name="{{ $permission->name }}" data-permission-group="{{ $permission->group ?? 'other' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="selected_permissions[]" value="{{ $permission->id }}" class="permission-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $permissions->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-key text-gray-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 font-mono">
                                                {{ $permission->name }}
                                            </div>
                                            @if($permission->guard_name)
                                                <div class="text-xs text-gray-500">
                                                    Guard: {{ $permission->guard_name }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ $permission->description ?? 'Tidak ada deskripsi' }}
                                    </div>
                                    @if($permission->created_at)
                                        <div class="text-xs text-gray-500">
                                            Dibuat: {{ $permission->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $group = $permission->group ?? 'other';
                                        $groupColors = [
                                            'user' => 'bg-blue-100 text-blue-800',
                                            'payment' => 'bg-green-100 text-green-800',
                                            'pranota' => 'bg-purple-100 text-purple-800',
                                            'report' => 'bg-yellow-100 text-yellow-800',
                                            'system' => 'bg-red-100 text-red-800',
                                            'other' => 'bg-gray-100 text-gray-800'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $groupColors[$group] ?? $groupColors['other'] }}">
                                        {{ ucfirst($group) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm text-gray-900">
                                        {{ $permission->users_count ?? 0 }} pengguna
                                    </div>
                                    @if(($permission->users_count ?? 0) > 0)
                                        <button onclick="showUsers({{ $permission->id }})"
                                                class="text-xs text-indigo-600 hover:text-indigo-900">
                                            Lihat detail
                                        </button>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('master.permission.edit', $permission->id) }}"
                                           class="inline-flex items-center px-3 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 text-xs font-medium rounded-full transition-colors duration-200">
                                            <i class="fas fa-edit mr-1"></i>
                                            Edit
                                        </a>
                                        <button onclick="assignToUsers({{ $permission->id }})"
                                                class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-medium rounded-full transition-colors duration-200">
                                            <i class="fas fa-users mr-1"></i>
                                            Assign
                                        </button>
                                        <form action="{{ route('master.permission.destroy', $permission->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus izin ini? Tindakan ini tidak dapat dibatalkan.');"
                                              class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-1 bg-red-100 hover:bg-red-200 text-red-800 text-xs font-medium rounded-full transition-colors duration-200">
                                                <i class="fas fa-trash mr-1"></i>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            
                                    <td>
                                        @can('audit-log-view')
                                            <button type="button" class="btn btn-info btn-sm" 
                                                    onclick="showAuditLog('Permission', {{ $permission->id }})"
                                                    title="Lihat Riwayat">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        @endcan
                                    </td></tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-key text-gray-400 text-4xl mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada izin ditemukan</h3>
                                        <p class="text-gray-500 mb-4">Mulai dengan membuat izin akses pertama Anda.</p>
                                        <a href="{{ route('master.permission.create') }}"
                                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                            <i class="fas fa-plus mr-2"></i>
                                            Tambah Izin Pertama
                                        </a>
                                    </div>
                                </td>
                            
                                    <td>
                                        @can('audit-log-view')
                                            <button type="button" class="btn btn-info btn-sm" 
                                                    onclick="showAuditLog('Permission', {{ $permission->id }})"
                                                    title="Lihat Riwayat">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        @endcan
                                    </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Bulk Actions -->
            <div id="bulkActions" class="px-6 py-3 bg-gray-50 border-t border-gray-200 hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-sm text-gray-700">
                            <span id="selectedCount">0</span> izin dipilih
                        </span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button onclick="bulkAssign()"
                                class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                            <i class="fas fa-users mr-1"></i>
                            Assign ke Pengguna
                        </button>
                        <button onclick="bulkDelete()"
                                class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                            <i class="fas fa-trash mr-1"></i>
                            Hapus Terpilih
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @include('components.modern-pagination', ['paginator' => $permissions, 'routeName' => 'master.permission.index'])
        </div>
    </div>
</div>

<!-- Modals -->
<!-- User Assignment Modal -->
<div id="userModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Assign Izin ke Pengguna</h3>
            <div id="userModalContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeUserModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Batal
                </button>
                <button onclick="saveUserAssignment()"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchPermissions');
    const filterGroup = document.getElementById('filterGroup');
    const permissionRows = document.querySelectorAll('.permission-row');

    function filterPermissions() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedGroup = filterGroup.value;
        let visibleCount = 0;

        permissionRows.forEach(row => {
            const permissionName = row.dataset.permissionName.toLowerCase();
            const permissionGroup = row.dataset.permissionGroup;
            const description = row.querySelector('td:nth-child(4)').textContent.toLowerCase();

            const matchesSearch = permissionName.includes(searchTerm) || description.includes(searchTerm);
            const matchesGroup = !selectedGroup || permissionGroup === selectedGroup;

            if (matchesSearch && matchesGroup) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('totalCount').textContent = visibleCount;
    }

    searchInput.addEventListener('input', filterPermissions);
    filterGroup.addEventListener('change', filterPermissions);

    // Checkbox functionality
    const selectAll = document.getElementById('selectAll');
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    function updateBulkActions() {
        const checked = document.querySelectorAll('.permission-checkbox:checked').length;
        selectedCount.textContent = checked;

        if (checked > 0) {
            bulkActions.classList.remove('hidden');
        } else {
            bulkActions.classList.add('hidden');
        }
    }

    selectAll.addEventListener('change', function() {
        permissionCheckboxes.forEach(checkbox => {
            if (checkbox.closest('tr').style.display !== 'none') {
                checkbox.checked = this.checked;
            }
        });
        updateBulkActions();
    });

    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});

// Permission management functions
function syncPermissions() {
    if (confirm('Sinkronisasi akan memperbarui daftar izin dari kode aplikasi. Lanjutkan?')) {
        fetch('{{ route("master.permission.sync") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal melakukan sinkronisasi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat sinkronisasi');
        });
    }
}

function assignToUsers(permissionId) {
    // Show user assignment modal
    document.getElementById('userModal').classList.remove('hidden');
    // Load users list via AJAX
    // Implementation depends on your user management system
}

function showUsers(permissionId) {
    // Show users who have this permission
    // Implementation depends on your requirements
}

function closeUserModal() {
    document.getElementById('userModal').classList.add('hidden');
}

function saveUserAssignment() {
    // Save user assignment
    // Implementation depends on your requirements
}

function bulkAssign() {
    const selectedPermissions = Array.from(document.querySelectorAll('.permission-checkbox:checked'))
        .map(cb => cb.value);

    if (selectedPermissions.length === 0) {
        alert('Pilih izin yang akan di-assign terlebih dahulu');
        return;
    }

    // Show bulk assignment modal
    assignToUsers(selectedPermissions);
}

function bulkDelete() {
    const selectedPermissions = Array.from(document.querySelectorAll('.permission-checkbox:checked'))
        .map(cb => cb.value);

    if (selectedPermissions.length === 0) {
        alert('Pilih izin yang akan dihapus terlebih dahulu');
        return;
    }

    if (confirm(`Apakah Anda yakin ingin menghapus ${selectedPermissions.length} izin terpilih? Tindakan ini tidak dapat dibatalkan.`)) {
        fetch('{{ route("master.permission.bulk-delete") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ permissions: selectedPermissions })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal menghapus izin: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus izin');
        });
    }
}
</script>
@endsection

