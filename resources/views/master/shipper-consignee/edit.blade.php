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
                    <div>
                        <label for="document_ppftz_03" class="block text-xs font-semibold text-gray-700 mb-1">Document PPFTZ-03</label>
                        <input type="text" name="document_ppftz_03" id="document_ppftz_03" value="{{ old('document_ppftz_03', $shipper_consignee->document_ppftz_03) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                    </div>
                    <div>
                        <label for="condition" class="block text-xs font-semibold text-gray-700 mb-1">Condition</label>
                        <input type="text" name="condition" id="condition" value="{{ old('condition', $shipper_consignee->condition) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                    </div>
                    <div class="md:col-span-2">
                        <label for="ip_bp_kawasan" class="block text-xs font-semibold text-gray-700 mb-1">IP BP Kawasan</label>
                        <input type="text" name="ip_bp_kawasan" id="ip_bp_kawasan" value="{{ old('ip_bp_kawasan', $shipper_consignee->ip_bp_kawasan) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                    </div>
                    <div class="md:col-span-2">
                        <label for="delivery_address" class="block text-xs font-semibold text-gray-700 mb-1">Delivery Address</label>
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
                    <div>
                        <label for="contact_person" class="block text-xs font-semibold text-gray-700 mb-1">Contact Person</label>
                        <div class="flex">
                            <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $shipper_consignee->contact_person) }}" class="w-full px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                            <button type="button" onclick="checkWhatsApp('contact_person')" class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-r-md text-xs font-semibold transition duration-150 flex items-center" title="Cek Nomor di WhatsApp">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                Cek WA
                            </button>
                        </div>
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
                        <label for="npwp_consignee_16_digit" class="block text-xs font-semibold text-gray-700 mb-1">NPWP Consignee (16 Digit)</label>
                        <input type="text" name="npwp_consignee_16_digit" id="npwp_consignee_16_digit" value="{{ old('npwp_consignee_16_digit', $shipper_consignee->npwp_consignee_16_digit) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-xs">
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

<script>
function checkWhatsApp(inputId) {
    let phone = document.getElementById(inputId).value;
    if (!phone) {
        alert("Silakan isi nomor kontak terlebih dahulu.");
        return;
    }
    // Hapus karakter selain angka
    phone = phone.replace(/\D/g, '');
    
    // Pastikan kode negara ada (jika dimulai 0, ganti jadi 62)
    if (phone.startsWith('0')) {
        phone = '62' + phone.substring(1);
    }
    
    window.open("https://wa.me/" + phone, "_blank");
}
</script>
@endsection
