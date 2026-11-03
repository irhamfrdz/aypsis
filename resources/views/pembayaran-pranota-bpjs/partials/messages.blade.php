@if(session('success'))<div role="status" class="p-4 bg-green-50 text-green-800 rounded-lg">{{ session('success') }}</div>@endif
@if(session('error'))<div role="alert" class="p-4 bg-red-50 text-red-800 rounded-lg">{{ session('error') }}</div>@endif
@if($errors->any())
<div role="alert" class="p-4 bg-red-50 text-red-800 rounded-lg"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
