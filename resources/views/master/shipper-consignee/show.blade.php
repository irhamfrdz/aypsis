@extends('layouts.app')

@section('title', 'Detail Shipper / Consignee')
@section('page_title', 'Detail Shipper / Consignee')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 max-w-4xl mx-auto" style="font-family: Arial, sans-serif; font-size: 11px;">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">Detail Shipper / Consignee</h2>
        <div class="flex space-x-2">
            <a href="{{ route('master.shipper-consignee.edit', $shipper_consignee) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1.5 px-3 rounded-lg transition duration-200 text-xs">
                Edit
            </a>
            <a href="{{ route('master.shipper-consignee.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-1.5 px-3 rounded-lg transition duration-200 text-xs flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Shipper Section -->
        <div class="col-span-1 bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-sm font-bold text-gray-700 mb-3 border-b pb-2">Informasi Shipper</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-gray-500 text-[10px] font-bold uppercase">Shipper</label>
                    <p class="text-gray-900 font-semibold text-xs">{{ $shipper_consignee->shipper ?: '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-500 text-[10px] font-bold uppercase">Address (Shipper)</label>
                    <p class="text-gray-900 whitespace-pre-line text-xs">{{ $shipper_consignee->alamat_shipper ?: '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-500 text-[10px] font-bold uppercase">No. Identitas (NPWP Shipper)</label>
                    <p class="text-gray-900 text-xs">{{ $shipper_consignee->npwp_shipper ?: '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Consignee Section -->
        <div class="col-span-1 bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-sm font-bold text-gray-700 mb-3 border-b pb-2">Informasi Consignee</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-gray-500 text-[10px] font-bold uppercase">Consignee</label>
                    <p class="text-gray-900 font-semibold text-xs">{{ $shipper_consignee->consignee ?: '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-500 text-[10px] font-bold uppercase">Address (Consignee)</label>
                    <p class="text-gray-900 whitespace-pre-line text-xs">{{ $shipper_consignee->alamat_consignee ?: '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-500 text-[10px] font-bold uppercase">No. Identitas (NPWP Consignee)</label>
                    <p class="text-gray-900 text-xs">{{ $shipper_consignee->npwp_consignee ?: '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Notify Party Section -->
        <div class="col-span-1 md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-sm font-bold text-gray-700 mb-3 border-b pb-2">Informasi Notify Party (Consignee)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-500 text-[10px] font-bold uppercase">Notify Party (Consignee)</label>
                    <p class="text-gray-900 font-medium text-xs">{{ $shipper_consignee->notify_party_consignee ?: '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-500 text-[10px] font-bold uppercase">No. Identitas (NPWP Notify Party Consignee)</label>
                    <p class="text-gray-900 text-xs">{{ $shipper_consignee->npwp_notify_party_consignee ?: '-' }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-gray-500 text-[10px] font-bold uppercase">Address (Notify Party Consignee)</label>
                    <p class="text-gray-900 whitespace-pre-line text-xs">{{ $shipper_consignee->alamat_notify_party_consignee ?: '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Pengiriman & Dokumen Section -->
        <div class="col-span-1 md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-sm font-bold text-gray-700 mb-3 border-b pb-2">Informasi Pengiriman & Dokumen</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-gray-500 text-[10px] font-bold uppercase">Delivery Address & Contact Person</label>
                    <p class="text-gray-900 whitespace-pre-line text-xs">{{ $shipper_consignee->delivery_address_contact_person ?: '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-500 text-[10px] font-bold uppercase">Document PPFTZ-03</label>
                    <p class="text-gray-900 text-xs">{{ $shipper_consignee->document_ppftz_03 ?: '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-500 text-[10px] font-bold uppercase">Condition</label>
                    <p class="text-gray-900 text-xs">{{ $shipper_consignee->condition ?: '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-500 text-[10px] font-bold uppercase">Status</label>
                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold {{ $shipper_consignee->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $shipper_consignee->status ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
