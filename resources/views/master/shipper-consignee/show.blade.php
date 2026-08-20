@extends('layouts.app')

@section('title', 'Detail Shipper / Consignee')
@section('page_title', 'Detail Shipper / Consignee')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Detail Shipper / Consignee</h2>
        <a href="{{ route('master.shipper-consignee.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg transition duration-300 flex items-center text-[11px] sm:text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Informasi Umum</h3>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">Telepon</label>
                <p class="text-gray-900">{{ $shipper_consignee->telepon ?: '-' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">Alamat Email</label>
                <p class="text-gray-900">{{ $shipper_consignee->alamat_email ?: '-' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">HS Code</label>
                <p class="text-gray-900">{{ $shipper_consignee->hs_code ?: '-' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">Commodity</label>
                <p class="text-gray-900">{{ $shipper_consignee->commodity ?: '-' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">Delivery Address</label>
                <p class="text-gray-900">{{ $shipper_consignee->delivery_address ?: '-' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">Document PPFTZ-03</label>
                <p class="text-gray-900">{{ $shipper_consignee->document_ppftz_03 ?: '-' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">Condition</label>
                <p class="text-gray-900">{{ $shipper_consignee->condition ?: '-' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">IP BP Kawasan</label>
                <p class="text-gray-900">{{ $shipper_consignee->ip_bp_kawasan ?: '-' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">Status</label>
                <p class="text-gray-900">{{ $shipper_consignee->status ?: '-' }}</p>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Informasi Shipper</h3>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">Shipper</label>
                <p class="text-gray-900 font-medium">{{ $shipper_consignee->shipper ?: '-' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">Alamat Shipper</label>
                <p class="text-gray-900">{{ $shipper_consignee->alamat_shipper ?: '-' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">NPWP Shipper</label>
                <p class="text-gray-900">{{ $shipper_consignee->npwp_shipper ?: '-' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">NITKU Shipper</label>
                <p class="text-gray-900">{{ $shipper_consignee->nitku_shipper ?: '-' }}</p>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Informasi Consignee</h3>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">Consignee</label>
                <p class="text-gray-900 font-medium">{{ $shipper_consignee->consignee ?: '-' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">Alamat Consignee</label>
                <p class="text-gray-900">{{ $shipper_consignee->alamat_consignee ?: '-' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">NPWP Consignee</label>
                <p class="text-gray-900">{{ $shipper_consignee->npwp_consignee ?: '-' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">NPWP Consignee (16 Digit)</label>
                <p class="text-gray-900">{{ $shipper_consignee->npwp_consignee_16_digit ?: '-' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">NITKU Consignee</label>
                <p class="text-gray-900">{{ $shipper_consignee->nitku_consignee ?: '-' }}</p>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Informasi Notify Party (Consignee)</h3>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">Notify Party (Consignee)</label>
                <p class="text-gray-900 font-medium">{{ $shipper_consignee->notify_party_consignee ?: '-' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">Alamat Notify Party (Consignee)</label>
                <p class="text-gray-900">{{ $shipper_consignee->alamat_notify_party_consignee ?: '-' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-500 text-xs font-bold mb-1">NPWP Notify Party (Consignee)</label>
                <p class="text-gray-900">{{ $shipper_consignee->npwp_notify_party_consignee ?: '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
