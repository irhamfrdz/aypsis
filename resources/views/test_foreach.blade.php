@extends('layouts.app')

@section('content')
<div>
    Test @foreach

    @foreach(($statusOptions ?? ['ongoing' => 'Container Ongoing', 'selesai' => 'Container Selesai']) as $value => $label)
        <div>{{ $value }} - {{ $label }}</div>
    @endforeach
</div>
@endsection
