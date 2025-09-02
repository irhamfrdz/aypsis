@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Admin — Semua Fitur</h1>

    <h2>Permissions</h2>
    <table class="table table-sm">
        <thead>
            <tr><th>Nama</th><th>Deskripsi</th></tr>
        </thead>
        <tbody>
            @foreach($permissions as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Registered Routes</h2>
    <table class="table table-striped table-sm">
        <thead>
            <tr><th>URI</th><th>Name</th><th>Methods</th><th>Middleware</th><th>Action</th></tr>
        </thead>
        <tbody>
            @foreach($allRoutes as $r)
                <tr>
                    <td>{{ $r['uri'] }}</td>
                    <td>{{ $r['name'] }}</td>
                    <td>{{ $r['methods'] }}</td>
                    <td>{{ $r['middleware'] }}</td>
                    <td>{{ $r['action'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
