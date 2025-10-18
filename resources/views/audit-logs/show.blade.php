@extends('layouts.app')

@section('title', 'Detail Audit Log')
@section('page_title', 'Detail Log Aktivitas')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Detail Log Aktivitas</h2>
                    <p class="text-sm text-gray-600 mt-1">ID: {{ $auditLog->id }}</p>
                </div>
                <a href="{{ route('audit-logs.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-6">
                    <!-- User & Time Info -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-blue-900 mb-3">Informasi User</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-blue-700">User:</span>
                                <span class="text-sm text-blue-900">{{ $auditLog->getUserDisplayName() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-blue-700">Waktu:</span>
                                <span class="text-sm text-blue-900">{{ $auditLog->created_at->format('d F Y, H:i:s') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-blue-700">IP Address:</span>
                                <span class="text-sm text-blue-900">{{ $auditLog->ip_address ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Info -->
                    <div class="bg-purple-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-purple-900 mb-3">Informasi Aktivitas</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-purple-700">Aksi:</span>
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                    {{ $auditLog->action === 'created' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $auditLog->action === 'updated' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $auditLog->action === 'deleted' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ !in_array($auditLog->action, ['created', 'updated', 'deleted']) ? 'bg-gray-100 text-gray-800' : '' }}
                                ">
                                    {{ ucfirst($auditLog->action) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-purple-700">Modul:</span>
                                <span class="text-sm text-purple-900">{{ ucfirst(str_replace('-', ' ', $auditLog->module)) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-purple-700">Deskripsi:</span>
                                <span class="text-sm text-purple-900">{{ $auditLog->description }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Model Info -->
                    <div class="bg-green-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-green-900 mb-3">Informasi Model</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-green-700">Model Type:</span>
                                <span class="text-sm text-green-900">{{ class_basename($auditLog->auditable_type) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-green-700">Model ID:</span>
                                <span class="text-sm text-green-900">{{ $auditLog->auditable_id }}</span>
                            </div>
                            @if($auditLog->auditable)
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-green-700">Model Exists:</span>
                                    <span class="text-sm text-green-900">✅ Ya</span>
                                </div>
                            @else
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-green-700">Model Exists:</span>
                                    <span class="text-sm text-red-600">❌ Tidak (mungkin sudah dihapus)</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Changes Information -->
                <div class="space-y-6">
                    @php $changes = $auditLog->getFormattedChanges(); @endphp

                    @if($changes && count($changes) > 0)
                        <!-- Data Changes -->
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-yellow-900 mb-3">
                                Perubahan Data ({{ count($changes) }} field)
                            </h3>
                            <div class="space-y-3">
                                @foreach($changes as $change)
                                    <div class="border-l-4 border-yellow-400 pl-4 py-2 bg-white rounded-r-md">
                                        <div class="flex justify-between items-start">
                                            <span class="text-sm font-medium text-gray-700">{{ $change['field'] }}:</span>
                                        </div>
                                        <div class="mt-1 space-y-1">
                                            <div class="text-sm">
                                                <span class="text-red-600 font-medium">Sebelum:</span>
                                                <span class="bg-red-50 px-2 py-1 rounded text-red-800">
                                                    "{{ $change['old'] ?: '-' }}"
                                                </span>
                                            </div>
                                            <div class="text-sm">
                                                <span class="text-green-600 font-medium">Sesudah:</span>
                                                <span class="bg-green-50 px-2 py-1 rounded text-green-800">
                                                    "{{ $change['new'] ?: '-' }}"
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Raw Data -->
                    @if($auditLog->old_values || $auditLog->new_values)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Raw Data</h3>

                            @if($auditLog->old_values)
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Old Values:</h4>
                                    <pre class="bg-white p-3 rounded border text-xs overflow-x-auto">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif

                            @if($auditLog->new_values)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">New Values:</h4>
                                    <pre class="bg-white p-3 rounded border text-xs overflow-x-auto">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Technical Info -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Teknis</h3>
                        <div class="space-y-2">
                            @if($auditLog->url)
                                <div>
                                    <span class="text-sm font-medium text-gray-700">URL:</span>
                                    <p class="text-xs text-gray-600 bg-white p-2 rounded border break-all">{{ $auditLog->url }}</p>
                                </div>
                            @endif

                            @if($auditLog->user_agent)
                                <div>
                                    <span class="text-sm font-medium text-gray-700">User Agent:</span>
                                    <p class="text-xs text-gray-600 bg-white p-2 rounded border">{{ $auditLog->user_agent }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Logs -->
            @if($auditLog->auditable_type && $auditLog->auditable_id)
                @php
                    $relatedLogs = \App\Models\AuditLog::where('auditable_type', $auditLog->auditable_type)
                        ->where('auditable_id', $auditLog->auditable_id)
                        ->where('id', '!=', $auditLog->id)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp

                @if($relatedLogs->count() > 0)
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Log Terkait ({{ $relatedLogs->count() }} terbaru)</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="space-y-3">
                                @foreach($relatedLogs as $related)
                                    <div class="bg-white p-3 rounded border hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                                    {{ $related->action === 'created' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $related->action === 'updated' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $related->action === 'deleted' ? 'bg-red-100 text-red-800' : '' }}
                                                    {{ !in_array($related->action, ['created', 'updated', 'deleted']) ? 'bg-gray-100 text-gray-800' : '' }}
                                                ">
                                                    {{ ucfirst($related->action) }}
                                                </span>
                                                <span class="text-sm text-gray-900">{{ $related->description }}</span>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $related->created_at->format('d/m/Y H:i') }} - {{ $related->getUserDisplayName() }}
                                            </div>
                                        </div>
                                        <div class="mt-2 flex justify-end">
                                            <a href="{{ route('audit-logs.show', $related->id) }}"
                                               class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                                Lihat Detail →
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
