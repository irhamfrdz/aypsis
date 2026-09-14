@extends('layouts.app')
@section('title', 'Edit Type Bon Amprahan')
@section('page_title', 'Edit Type Bon Amprahan')
@section('content')
<div class="container mx-auto px-4 py-6"><div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6"><h2 class="text-xl font-bold mb-6">Edit Type Bon Amprahan</h2>
    <form method="POST" action="{{ route('master.type-bon-amprahan.update', $typeBonAmprahan) }}">@csrf @method('PUT') @include('master-type-bon-amprahan._form')</form>
</div></div>
@endsection
