@extends('layouts.app')
@section('title', 'Layout Gudang')
@section('page_title', 'Layout Gudang')
@section('content')
    @include('denah-gudang.planner', ['mode' => 'layout'])
@endsection
