@extends('layouts.app')

@section('title', 'Audit Log')
@section('page_title', 'Log Aktivitas Sistem')

@section('content')
<div class="max-w-full mx-auto px-4">
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <!-- Header Section -->
        <div class="px-6 py-4 border-b bg-white">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Log Aktivitas Sistem</h2>
                        <p class="text-sm text-gray-600 mt-1">Pantau semua aktivitas perubahan data di sistem</p>
                    </div>

                    <!-- Export Button -->
                    <div class="flex gap-2">
                        <a href="{{ route('audit-logs.export', request()->all()) }}"
                           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export CSV
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <form method="GET" action="{{ route('audit-logs.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari deskripsi, user..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Module Filter -->
                    <div>
                        <select name="module" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Modul</option>
                            @foreach($modules as $module)
                                <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('-', ' ', $module)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Filter -->
                    <div>
                        <select name="action" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Aksi</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                    {{ ucfirst($action) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- User Filter -->
                    <div>
                        <select name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->username }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'module', 'action', 'user_id', 'start_date', 'end_date']))
                            <a href="{{ route('audit-logs.index') }}"
                               class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-all">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Date Range Filter -->
                <form method="GET" action="{{ route('audit-logs.index') }}" class="flex gap-4 items-end">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="module" value="{{ request('module') }}">
                    <input type="hidden" name="action" value="{{ request('action') }}">
                    <input type="hidden" name="user_id" value="{{ request('user_id') }}">

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date"
                               name="start_date"
                               value="{{ request('start_date') }}"
                               class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                        <input type="date"
                               name="end_date"
                               value="{{ request('end_date') }}"
                               class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-all">
                        Filter Tanggal
                    </button>
                </form>
            </div>
        </div>

        <!-- Summary Info -->
        <div class="px-6 py-3 bg-gray-50 border-b">
            <div class="flex items-center justify-between text-sm text-gray-600">
                <span>Total: {{ $auditLogs->total() }} log aktivitas</span>
                <span>Halaman {{ $auditLogs->currentPage() }} dari {{ $auditLogs->lastPage() }}</span>
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Waktu & User
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aktivitas
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Detail
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Info Tambahan
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($auditLogs as $log)
                        <tr class="hover:bg-gray-50">
                            <!-- Waktu & User -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">
                                        {{ $log->getUserDisplayName() }}
                                    </div>
                                    <div class="text-gray-500">
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </div>
                                    @if($log->ip_address)
                                        <div class="text-xs text-gray-400">
                                            IP: {{ $log->ip_address }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Aktivitas -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @php
                                        $badgeColors = [
                                            'created' => 'bg-green-100 text-green-800',
                                            'updated' => 'bg-blue-100 text-blue-800',
                                            'deleted' => 'bg-red-100 text-red-800',
                                            'viewed' => 'bg-gray-100 text-gray-800',
                                            'imported' => 'bg-purple-100 text-purple-800',
                                            'exported' => 'bg-yellow-100 text-yellow-800'
                                        ];
                                        $badgeColor = $badgeColors[$log->action] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $badgeColor }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                    <span class="ml-2 text-sm font-medium text-gray-900">
                                        {{ ucfirst(str_replace('-', ' ', $log->module)) }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600 mt-1">
                                    {{ $log->description }}
                                </div>
                            </td>

                            <!-- Detail -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <strong>Model:</strong> {{ class_basename($log->auditable_type) }}
                                    <br>
                                    <strong>ID:</strong> {{ $log->auditable_id }}
                                </div>

                                @php $changes = $log->getFormattedChanges(); @endphp
                                @if($changes && count($changes) > 0)
                                    <div class="mt-2">
                                        <button onclick="toggleChanges('changes-{{ $log->id }}')"
                                                class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            Lihat Perubahan ({{ count($changes) }})
                                        </button>
                                        <div id="changes-{{ $log->id }}" class="hidden mt-2 p-2 bg-gray-50 rounded-md text-xs">
                                            @foreach($changes as $change)
                                                <div class="mb-1">
                                                    <span class="font-medium">{{ $change['field'] }}:</span>
                                                    <span class="text-red-600">"{{ $change['old'] ?: '-' }}"</span>
                                                    <span class="text-gray-400">→</span>
                                                    <span class="text-green-600">"{{ $change['new'] ?: '-' }}"</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <!-- Info Tambahan -->
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if($log->user_agent)
                                    <div class="mb-1">
                                        <strong>Browser:</strong>
                                        <span class="text-xs">{{ Str::limit($log->user_agent, 30) }}</span>
                                    </div>
                                @endif
                                @if($log->url)
                                    <div>
                                        <strong>URL:</strong>
                                        <span class="text-xs">{{ Str::limit($log->url, 40) }}</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('audit-logs.show', $log->id) }}"
                                   class="text-blue-600 hover:text-blue-800 font-medium"
                                   title="Lihat Detail">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <p class="text-sm font-medium">Tidak ada log aktivitas ditemukan</p>
                                    <p class="text-xs mt-1">Coba ubah filter atau kriteria pencarian</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($auditLogs->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t">
                @include('components.modern-pagination', ['paginator' => $auditLogs])
                @include('components.rows-per-page')
            </div>
        @endif
    </div>
</div>

<script>
function toggleChanges(elementId) {
    const element = document.getElementById(elementId);
    element.classList.toggle('hidden');
}
</script>

@endsection
