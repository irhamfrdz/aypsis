@extends('layouts.app')

@section('title','Kelola Izin Massal')
@section('page_title', 'Kelola Izin Massal')

@section('content')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <style>
        .bulk-container { max-width: 1200px; margin: 0 auto; }
        .user-list { max-height: 400px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 6px; }
        .user-item { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px; }
        .user-item:hover { background-color: #f9fafb; }
        .user-checkbox { width: 18px; height: 18px; }
        .permission-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px; }
        .permission-card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; }
        .permission-card h4 { margin-bottom: 12px; font-weight: 600; }
        .permission-list { max-height: 200px; overflow-y: auto; }
        .perm-item { display: flex; align-items: center; gap: 8px; padding: 4px 0; }
        .perm-checkbox { width: 16px; height: 16px; }
        .action-buttons { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 20px; }
        .btn { padding: 8px 16px; border-radius: 6px; border: 1px solid #d1d5db; background: white; cursor: pointer; }
        .btn:hover { background: #f9fafb; }
        .btn-primary { background: #4f46e5; color: white; border-color: #4f46e5; }
        .btn-primary:hover { background: #4338ca; }
        .btn-success { background: #10b981; color: white; border-color: #10b981; }
        .btn-success:hover { background: #059669; }
        .btn-warning { background: #f59e0b; color: white; border-color: #f59e0b; }
        .btn-warning:hover { background: #d97706; }
        .btn-danger { background: #ef4444; color: white; border-color: #ef4444; }
        .btn-danger:hover { background: #dc2626; }
        .stats { display: flex; gap: 20px; margin-bottom: 20px; }
        .stat-card { background: #f9fafb; padding: 16px; border-radius: 8px; border: 1px solid #e5e7eb; }
        .stat-number { font-size: 24px; font-weight: bold; color: #4f46e5; }
        .stat-label { color: #6b7280; font-size: 14px; }
    </style>
@endpush

<div class="bulk-container">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Kelola Izin Massal</h2>

        @if (isset($errors) && is_object($errors) && method_exists($errors, 'any') && $errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-6">
                <strong class="font-bold">Terjadi kesalahan:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Statistics --}}
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">{{ $users->count() }}</div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $permissions->count() }}</div>
                <div class="stat-label">Total Permissions</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="selected_users_count">0</div>
                <div class="stat-label">Users Selected</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="selected_perms_count">0</div>
                <div class="stat-label">Permissions Selected</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- User Selection --}}
            <div class="permission-card">
                <h4 class="text-lg font-semibold mb-4">Pilih Users</h4>

                <div class="mb-4 flex gap-2">
                    <button type="button" id="select_all_users" class="btn btn-primary">Pilih Semua</button>
                    <button type="button" id="deselect_all_users" class="btn">Hapus Semua</button>
                    <input id="user_search" placeholder="Cari user..." class="flex-1 p-2 rounded border" />
                </div>

                <div class="user-list" id="user_list">
                    @foreach($users as $user)
                        <div class="user-item" data-user-id="{{ $user->id }}" data-username="{{ $user->username }}">
                            <input type="checkbox" class="user-checkbox" value="{{ $user->id }}" id="user_{{ $user->id }}">
                            <label for="user_{{ $user->id }}" class="flex-1 cursor-pointer">
                                <div class="font-medium">{{ $user->username }}</div>
                                <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                            </label>
                            <div class="text-xs text-gray-400">{{ $user->permissions->count() }} izin</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Permission Selection --}}
            <div class="permission-card">
                <h4 class="text-lg font-semibold mb-4">Pilih Izin</h4>

                <div class="mb-4 flex gap-2">
                    <select id="perm_group_filter" class="p-2 rounded border">
                        <option value="">-- Semua Grup --</option>
                        @php $groups = config('permission_groups', []); @endphp
                        @foreach($groups as $key => $g)
                            <option value="{{ $key }}">{{ $g['label'] ?? $key }}</option>
                        @endforeach
                    </select>
                    <input id="perm_search" placeholder="Cari izin..." class="flex-1 p-2 rounded border" />
                </div>

                <div class="permission-list" id="permission_list">
                    @foreach($permissions as $permission)
                        @php
                            $groupKey = '';
                            foreach (config('permission_groups', []) as $k => $g) {
                                foreach ($g['prefixes'] as $pf) {
                                    if (strpos($permission->name, $pf) === 0) { $groupKey = $k; break 2; }
                                }
                            }
                        @endphp
                        <div class="perm-item" data-group="{{ $groupKey }}" data-name="{{ $permission->name }}">
                            <input type="checkbox" class="perm-checkbox" value="{{ $permission->id }}" id="perm_{{ $permission->id }}">
                            <label for="perm_{{ $permission->id }}" class="flex-1 cursor-pointer">
                                <div class="font-medium">{{ $permission->description ?: ucwords(str_replace('-', ' ', $permission->name)) }}</div>
                                <div class="text-sm text-gray-500">{{ $permission->name }}</div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="action-buttons">
            <button type="button" id="add_permissions" class="btn btn-success" disabled>
                ➕ Tambahkan Izin
            </button>
            <button type="button" id="remove_permissions" class="btn btn-warning" disabled>
                ➖ Hapus Izin
            </button>
            <button type="button" id="replace_permissions" class="btn btn-danger" disabled>
                🔄 Ganti Semua Izin
            </button>

            {{-- Template Actions --}}
            <select id="bulk_template_select" class="p-2 rounded border">
                <option value="">-- Terapkan Template --</option>
                @php $templates = config('permission_templates', []); @endphp
                @foreach($templates as $key => $template)
                    <option value="{{ $key }}">{{ $template['label'] ?? $key }}</option>
                @endforeach
            </select>
            <button type="button" id="apply_bulk_template" class="btn btn-primary" disabled>
                🎯 Terapkan Template
            </button>
        </div>

        {{-- Results Area --}}
        <div id="results_area" class="mt-6 hidden">
            <div class="bg-gray-50 border rounded-lg p-4">
                <h4 class="font-semibold mb-2">Hasil Operasi:</h4>
                <div id="results_content"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // User selection controls
    const userCheckboxes = () => Array.from(document.querySelectorAll('.user-checkbox'));
    const permCheckboxes = () => Array.from(document.querySelectorAll('.perm-checkbox'));

    function updateStats() {
        const selectedUsers = userCheckboxes().filter(cb => cb.checked).length;
        const selectedPerms = permCheckboxes().filter(cb => cb.checked).length;

        document.getElementById('selected_users_count').textContent = selectedUsers;
        document.getElementById('selected_perms_count').textContent = selectedPerms;

        // Enable/disable action buttons
        const hasUsers = selectedUsers > 0;
        const hasPerms = selectedPerms > 0;

        document.getElementById('add_permissions').disabled = !hasUsers || !hasPerms;
        document.getElementById('remove_permissions').disabled = !hasUsers || !hasPerms;
        document.getElementById('replace_permissions').disabled = !hasUsers || !hasPerms;
    }

    // Select all/deselect all users
    document.getElementById('select_all_users').addEventListener('click', () => {
        userCheckboxes().forEach(cb => cb.checked = true);
        updateStats();
    });

    document.getElementById('deselect_all_users').addEventListener('click', () => {
        userCheckboxes().forEach(cb => cb.checked = false);
        updateStats();
    });

    // User search
    document.getElementById('user_search').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.user-item').forEach(item => {
            const name = item.dataset.name.toLowerCase();
            const username = item.dataset.username.toLowerCase();
            item.style.display = (name.includes(query) || username.includes(query)) ? 'flex' : 'none';
        });
    });

    // Permission group filter
    document.getElementById('perm_group_filter').addEventListener('change', function() {
        const group = this.value;
        document.querySelectorAll('.perm-item').forEach(item => {
            item.style.display = (group === '' || item.dataset.group === group) ? 'block' : 'none';
        });
    });

    // Permission search
    document.getElementById('perm_search').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.perm-item').forEach(item => {
            const name = item.dataset.name.toLowerCase();
            const label = item.querySelector('label').textContent.toLowerCase();
            item.style.display = (name.includes(query) || label.includes(query)) ? 'block' : 'none';
        });
    });

    // Bulk actions
    async function performBulkAction(action) {
        const userIds = userCheckboxes().filter(cb => cb.checked).map(cb => cb.value);
        const permissionIds = permCheckboxes().filter(cb => cb.checked).map(cb => cb.value);

        if (userIds.length === 0 || permissionIds.length === 0) {
            alert('Pilih users dan permissions terlebih dahulu');
            return;
        }

        try {
            const response = await fetch('{{ route("master.user.bulk-assign-permissions") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    user_ids: userIds,
                    permission_ids: permissionIds,
                    action: action
                })
            });

            const result = await response.json();

            if (result.success) {
                showResults(`${result.message}`, 'success');
                // Refresh page after short delay
                setTimeout(() => location.reload(), 2000);
            } else {
                showResults('Error: ' + (result.error || 'Unknown error'), 'error');
            }
        } catch (error) {
            showResults('Network error: ' + error.message, 'error');
        }
    }

    document.getElementById('add_permissions').addEventListener('click', () => performBulkAction('add'));
    document.getElementById('remove_permissions').addEventListener('click', () => performBulkAction('remove'));
    document.getElementById('replace_permissions').addEventListener('click', () => performBulkAction('replace'));

    // Bulk template application
    document.getElementById('bulk_template_select').addEventListener('change', function() {
        document.getElementById('apply_bulk_template').disabled = !this.value;
    });

    document.getElementById('apply_bulk_template').addEventListener('click', async function() {
        const template = document.getElementById('bulk_template_select').value;
        const userIds = userCheckboxes().filter(cb => cb.checked).map(cb => cb.value);

        if (!template || userIds.length === 0) {
            alert('Pilih template dan users terlebih dahulu');
            return;
        }

        try {
            const promises = userIds.map(userId =>
                fetch(`{{ url('/master/user') }}/${userId}/assign-template`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ template: template })
                })
            );

            const results = await Promise.all(promises);
            const successCount = results.filter(r => r.ok).length;

            showResults(`Template diterapkan ke ${successCount}/${userIds.length} users`, 'success');
            setTimeout(() => location.reload(), 2000);
        } catch (error) {
            showResults('Error: ' + error.message, 'error');
        }
    });

    function showResults(message, type) {
        const resultsArea = document.getElementById('results_area');
        const resultsContent = document.getElementById('results_content');

        resultsContent.textContent = message;
        resultsContent.className = type === 'success' ? 'text-green-600' : 'text-red-600';
        resultsArea.classList.remove('hidden');
    }

    // Update stats on checkbox changes
    document.addEventListener('change', updateStats);

    // Initial stats update
    updateStats();
});
</script>
@endpush
