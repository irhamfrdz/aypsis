@extends('layouts.app')
@section('title', 'Posisi Kontainer Gudang')
@section('page_title', 'Posisi Kontainer Gudang')
@section('content')
    @include('denah-gudang.planner', ['mode' => 'positions'])
@endsection
