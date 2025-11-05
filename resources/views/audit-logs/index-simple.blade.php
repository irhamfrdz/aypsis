@extends('layouts.app')

@section('title', 'Audit Log')
@section('page_title', 'Log Aktivitas Sistem')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Log Aktivitas Sistem</h3>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control"
                                       placeholder="Cari..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="module" class="form-control">
                                    <option value="">Semua Modul</option>
                                    @foreach($modules as $module)
                                        <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                                            {{ ucfirst($module) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="action" class="form-control">
                                    <option value="">Semua Aksi</option>
                                    @foreach($actions as $action)
                                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                            {{ ucfirst($action) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="user_id" class="form-control">
                                    <option value="">Semua User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->username }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <!-- Results -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Aksi</th>
                                    <th>Modul</th>
                                    <th>Deskripsi</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($auditLogs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $log->getUserDisplayName() }}</td>
                                        <td>
                                            <span class="badge badge-{{ $log->action === 'created' ? 'success' : ($log->action === 'updated' ? 'primary' : 'danger') }}">
                                                {{ ucfirst($log->action) }}
                                            </span>
                                        </td>
                                        <td>{{ ucfirst($log->module) }}</td>
                                        <td>{{ $log->description }}</td>
                                        <td>
                                            <a href="{{ route('audit-logs.show', $log->id) }}" class="btn btn-sm btn-info">
                                                Lihat
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data log aktivitas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($auditLogs->hasPages())
                        <div class="mt-4">
                            @include('components.modern-pagination', ['paginator' => $auditLogs])
                            @include('components.rows-per-page')
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
