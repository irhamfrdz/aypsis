@extends('layouts.app')

@section('title', 'Edit Term Order')
@section('page_title', 'Edit Term Order')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Term Order</h1>
                    <p class="mt-1 text-sm text-gray-600">Update term pembayaran untuk order</p>
                </div>
                <a href="{{ route('approval-order.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Order Details Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Detail Order</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">No. Order</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $order->nomor_order }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Order</label>
                    <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($order->tanggal_order)->format('d M Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Pengirim</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $order->pengirim->nama ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jenis Barang</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $order->jenisBarang->nama_barang ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Term Saat Ini</label>
                    @if($order->term)
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $order->term->kode }} - {{ $order->term->nama_status }}
                            </span>
                        </p>
                    @else
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Belum ada term
                            </span>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Update Term Pembayaran</h2>

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('approval-order.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="term_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Term <span class="text-red-500">*</span>
                    </label>
                    <select name="term_id" id="term_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('term_id') border-red-300 @enderror">
                        <option value="">-- Pilih Term --</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" 
                                    {{ old('term_id', $order->term_id) == $term->id ? 'selected' : '' }}>
                                {{ $term->kode }} - {{ $term->nama_status }}
                            </option>
                        @endforeach
                    </select>
                    @error('term_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informasi Penerima Section -->
                <div class="mb-6 border-t border-gray-200 pt-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Informasi Penerima</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="penerima_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Penerima
                            </label>
                            <select name="penerima_id" id="penerima_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('penerima_id') border-red-300 @enderror">
                                <option value="">-- Pilih Penerima --</option>
                                @foreach($penerimas as $penerima)
                                    <option value="{{ $penerima->id }}" 
                                            {{ old('penerima_id', $order->penerima_id) == $penerima->id ? 'selected' : '' }}>
                                        {{ $penerima->nama_penerima }}
                                    </option>
                                @endforeach
                            </select>
                            @error('penerima_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="kontak_penerima" class="block text-sm font-medium text-gray-700 mb-2">
                                Kontak Penerima
                            </label>
                            <input type="text" name="kontak_penerima" id="kontak_penerima" 
                                   value="{{ old('kontak_penerima', $order->kontak_penerima) }}"
                                   placeholder="Nomor telepon/HP penerima"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('kontak_penerima') border-red-300 @enderror">
                            @error('kontak_penerima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="alamat_penerima" class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat Penerima
                            </label>
                            <textarea name="alamat_penerima" id="alamat_penerima" rows="3"
                                      placeholder="Alamat lengkap penerima"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('alamat_penerima') border-red-300 @enderror">{{ old('alamat_penerima', $order->alamat_penerima) }}</textarea>
                            @error('alamat_penerima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Tipe Dokumen Section -->
                <div class="mb-6 border-t border-gray-200 pt-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Tipe Dokumen</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <!-- FTZ03 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">FTZ03</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" id="exclude_ftz03" name="ftz03_option" value="exclude"
                                           {{ old('ftz03_option', $order->exclude_ftz03 ? 'exclude' : ($order->include_ftz03 ? 'include' : 'none')) == 'exclude' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    <label for="exclude_ftz03" class="ml-2 text-sm text-gray-700">Exclude FTZ03</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="include_ftz03" name="ftz03_option" value="include"
                                           {{ old('ftz03_option', $order->exclude_ftz03 ? 'exclude' : ($order->include_ftz03 ? 'include' : 'none')) == 'include' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    <label for="include_ftz03" class="ml-2 text-sm text-gray-700">Include FTZ03</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="none_ftz03" name="ftz03_option" value="none"
                                           {{ old('ftz03_option', $order->exclude_ftz03 ? 'exclude' : ($order->include_ftz03 ? 'include' : 'none')) == 'none' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    <label for="none_ftz03" class="ml-2 text-sm text-gray-700">Tidak ada</label>
                                </div>
                            </div>
                        </div>

                        <!-- SPPB -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">SPPB</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" id="exclude_sppb" name="sppb_option" value="exclude"
                                           {{ old('sppb_option', $order->exclude_sppb ? 'exclude' : ($order->include_sppb ? 'include' : 'none')) == 'exclude' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    <label for="exclude_sppb" class="ml-2 text-sm text-gray-700">Exclude SPPB</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="include_sppb" name="sppb_option" value="include"
                                           {{ old('sppb_option', $order->exclude_sppb ? 'exclude' : ($order->include_sppb ? 'include' : 'none')) == 'include' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    <label for="include_sppb" class="ml-2 text-sm text-gray-700">Include SPPB</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="none_sppb" name="sppb_option" value="none"
                                           {{ old('sppb_option', $order->exclude_sppb ? 'exclude' : ($order->include_sppb ? 'include' : 'none')) == 'none' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    <label for="none_sppb" class="ml-2 text-sm text-gray-700">Tidak ada</label>
                                </div>
                            </div>
                        </div>

                        <!-- Buruh Bongkar -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Buruh Bongkar</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" id="exclude_buruh_bongkar" name="buruh_bongkar_option" value="exclude"
                                           {{ old('buruh_bongkar_option', $order->exclude_buruh_bongkar ? 'exclude' : ($order->include_buruh_bongkar ? 'include' : 'none')) == 'exclude' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    <label for="exclude_buruh_bongkar" class="ml-2 text-sm text-gray-700">Exclude Buruh Bongkar</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="include_buruh_bongkar" name="buruh_bongkar_option" value="include"
                                           {{ old('buruh_bongkar_option', $order->exclude_buruh_bongkar ? 'exclude' : ($order->include_buruh_bongkar ? 'include' : 'none')) == 'include' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    <label for="include_buruh_bongkar" class="ml-2 text-sm text-gray-700">Include Buruh Bongkar</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="none_buruh_bongkar" name="buruh_bongkar_option" value="none"
                                           {{ old('buruh_bongkar_option', $order->exclude_buruh_bongkar ? 'exclude' : ($order->include_buruh_bongkar ? 'include' : 'none')) == 'none' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    <label for="none_buruh_bongkar" class="ml-2 text-sm text-gray-700">Tidak ada</label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('approval-order.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Term
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
