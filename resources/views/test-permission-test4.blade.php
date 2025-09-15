@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Test Permission User: {{ $user->name ?? $user->username }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Permission</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $result)
                                <tr>
                                    <td>{{ $result['permission'] }}</td>
                                    <td>
                                        <span class="{{ strpos($result['has_permission'], '✅') !== false ? 'text-success' : 'text-danger' }}">
                                            {{ $result['has_permission'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('karyawan.index') }}" class="btn btn-primary">
                            Test Akses Karyawan Page
                        </a>
                        <a href="{{ route('karyawan.create') }}" class="btn btn-warning">
                            Test Tambah Karyawan
                        </a>
                        <a href="{{ route('master.karyawan.import') }}" class="btn btn-info">
                            Test Import Karyawan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
