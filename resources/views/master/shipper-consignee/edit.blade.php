@extends('layouts.app')

@section('title', 'Edit Shipper / Consignee')
@section('page_title', 'Edit Shipper / Consignee')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 max-w-4xl mx-auto" style="font-family: Arial, sans-serif; font-size: 11px;">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">Edit Data Shipper / Consignee</h2>
        <a href="{{ route('master.shipper-consignee.index') }}" class="text-gray-600 hover:text-gray-900 font-medium flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Ada {{ $errors->count() }} kesalahan dengan isian Anda:</h3>
                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('master.shipper-consignee.update', $shipper_consignee) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Umum Section -->
            <div class="col-span-1 md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-sm font-bold text-gray-700 mb-3 border-b pb-2">Informasi Umum</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="telepon" class="block text-xs font-semibold text-gray-700 mb-1">Telepon</label>
                        <input type="text" name="telepon" id="telepon" value="{{ old('telepon', $shipper_consignee->telepon) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                    </div>
                    <div>
                        <label for="alamat_email" class="block text-xs font-semibold text-gray-700 mb-1">Alamat Email</label>
                        <input type="email" name="alamat_email" id="alamat_email" value="{{ old('alamat_email', $shipper_consignee->alamat_email) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                    </div>
                    <div>
                        <label for="hs_code" class="block text-xs font-semibold text-gray-700 mb-1">HS Code</label>
                        <input type="text" name="hs_code" id="hs_code" value="{{ old('hs_code', $shipper_consignee->hs_code) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                    </div>
                    <div>
                        <label for="commodity" class="block text-xs font-semibold text-gray-700 mb-1">Commodity</label>
                        <input type="text" name="commodity" id="commodity" value="{{ old('commodity', $shipper_consignee->commodity) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                    </div>
                    <div class="md:col-span-2">
                        <label for="delivery_address" class="block text-xs font-semibold text-gray-700 mb-1">Delivery Address & Contact Person</label>
                        <textarea name="delivery_address" id="delivery_address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">{{ old('delivery_address', $shipper_consignee->delivery_address) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Shipper Section -->
            <div class="col-span-1 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-sm font-bold text-gray-700 mb-3 border-b pb-2">Informasi Shipper</h3>
                <div class="space-y-4">
                    <div>
                        <label for="shipper" class="block text-xs font-semibold text-gray-700 mb-1">Shipper</label>
                        <input type="text" name="shipper" id="shipper" value="{{ old('shipper', $shipper_consignee->shipper) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                    </div>
                    <div>
                        <label for="alamat_shipper" class="block text-xs font-semibold text-gray-700 mb-1">Address (Shipper)</label>
                        <textarea name="alamat_shipper" id="alamat_shipper" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">{{ old('alamat_shipper', $shipper_consignee->alamat_shipper) }}</textarea>
                    </div>
                    <div>
                        <label for="npwp_shipper" class="block text-xs font-semibold text-gray-700 mb-1">No Identitas (NPWP Shipper)</label>
                        <input type="text" name="npwp_shipper" id="npwp_shipper" value="{{ old('npwp_shipper', $shipper_consignee->npwp_shipper) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                    </div>
                    <div>
                        <label for="nitku_shipper" class="block text-xs font-semibold text-gray-700 mb-1">NITKU Shipper</label>
                        <input type="text" name="nitku_shipper" id="nitku_shipper" value="{{ old('nitku_shipper', $shipper_consignee->nitku_shipper) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                    </div>
                </div>
            </div>

            <!-- Consignee Section -->
            <div class="col-span-1 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-sm font-bold text-gray-700 mb-3 border-b pb-2">Informasi Consignee</h3>
                <div class="space-y-4">
                    <div>
                        <label for="consignee" class="block text-xs font-semibold text-gray-700 mb-1">Consignee</label>
                        <input type="text" name="consignee" id="consignee" value="{{ old('consignee', $shipper_consignee->consignee) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                    </div>
                    <div>
                        <label for="alamat_consignee" class="block text-xs font-semibold text-gray-700 mb-1">Address (Consignee)</label>
                        <textarea name="alamat_consignee" id="alamat_consignee" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">{{ old('alamat_consignee', $shipper_consignee->alamat_consignee) }}</textarea>
                    </div>
                    <div>
                        <label for="npwp_consignee" class="block text-xs font-semibold text-gray-700 mb-1">No Identitas (NPWP Consignee)</label>
                        <input type="text" name="npwp_consignee" id="npwp_consignee" value="{{ old('npwp_consignee', $shipper_consignee->npwp_consignee) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                    </div>
                    <div>
                        <label for="nitku_consignee" class="block text-xs font-semibold text-gray-700 mb-1">NITKU Consignee</label>
                        <input type="text" name="nitku_consignee" id="nitku_consignee" value="{{ old('nitku_consignee', $shipper_consignee->nitku_consignee) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                    </div>
                </div>
            </div>

            <!-- Notify Party Section -->
            <div class="col-span-1 md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-sm font-bold text-gray-700 mb-3 border-b pb-2">Informasi Notify Party (Consignee)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="notify_party_consignee" class="block text-xs font-semibold text-gray-700 mb-1">Notify Party (Consignee)</label>
                        <input type="text" name="notify_party_consignee" id="notify_party_consignee" value="{{ old('notify_party_consignee', $shipper_consignee->notify_party_consignee) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                    </div>
                    <div>
                        <label for="npwp_notify_party_consignee" class="block text-xs font-semibold text-gray-700 mb-1">No Identitas (Notify Party Consignee)</label>
                        <input type="text" name="npwp_notify_party_consignee" id="npwp_notify_party_consignee" value="{{ old('npwp_notify_party_consignee', $shipper_consignee->npwp_notify_party_consignee) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                    </div>
                    <div class="md:col-span-2">
                        <label for="alamat_notify_party_consignee" class="block text-xs font-semibold text-gray-700 mb-1">Address (Notify Party Consignee)</label>
                        <textarea name="alamat_notify_party_consignee" id="alamat_notify_party_consignee" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">{{ old('alamat_notify_party_consignee', $shipper_consignee->alamat_notify_party_consignee) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300 shadow-sm">
                Perbarui Data
            </button>
        </div>
    </form>
</div>
@endsection
