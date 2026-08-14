@extends('layouts.supir')

@section('title', 'Dashboard Supir - AYPSIS')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="space-y-12">
        {{-- Section Permohonan --}}
        <div>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <span class="w-1.5 h-6 bg-indigo-600 rounded-full mr-3"></span>
                    Memo Permohonan
                </h2>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-full border border-indigo-100 italic">Tugas Aktif</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($permohonans as $permohonan)
                    @php
                        $sudahCheckpoint = $permohonan->kontainers->isNotEmpty();
                    @endphp
                    <a href="{{ route('supir.checkpoint.create', $permohonan) }}"
                       class="group bg-white rounded-2xl p-6 border {{ $sudahCheckpoint ? 'border-green-200 bg-green-50/30' : 'border-gray-200 hover:border-indigo-300' }} shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden">
                        
                        @if($sudahCheckpoint)
                            <div class="absolute -right-6 -top-6 w-16 h-16 bg-green-500/10 rounded-full flex items-center justify-center pt-4 pr-4">
                                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            </div>
                        @endif

                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-black {{ $sudahCheckpoint ? 'text-green-700' : 'text-gray-900 group-hover:text-indigo-600' }} tracking-tight">{{ $permohonan->nomor_memo }}</h3>
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Nomor Memo</div>
                            </div>
                            <span class="px-3 py-1 text-[10px] font-black rounded-lg uppercase tracking-tighter {{ $sudahCheckpoint ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-indigo-100 text-indigo-700 border border-indigo-200' }}">
                                {{ $permohonan->status }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-6 py-4 border-t border-gray-100">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tujuan</p>
                                <p class="text-sm font-bold text-gray-800">{{ $permohonan->tujuan }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Kegiatan</p>
                                <p class="text-sm font-bold text-gray-800">{{ $kegiatanMap[$permohonan->kegiatan] ?? ucfirst($permohonan->kegiatan) }}</p>
                            </div>
                        </div>

                        @if($sudahCheckpoint)
                            <div class="mt-4 flex items-center text-green-600 text-xs font-bold">
                                <i class="fas fa-check-double mr-2"></i> Sudah input nomor kontainer
                            </div>
                        @else
                            <div class="mt-4 flex items-center text-indigo-600 text-xs font-bold opacity-0 group-hover:opacity-100 transition-opacity">
                                Klik untuk proses <i class="fas fa-arrow-right ml-2 animate-bounce-x"></i>
                            </div>
                        @endif
                    </a>
                @empty
                    <div class="col-span-1 md:col-span-2 bg-gray-50 rounded-3xl p-12 text-center border-2 border-dashed border-gray-200">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-sm mx-auto mb-4">
                            <i class="fas fa-clipboard-list text-gray-300 text-2xl"></i>
                        </div>
                        <h3 class="text-gray-900 font-bold">Tidak Ada Tugas Aktif</h3>
                        <p class="text-gray-500 text-sm mt-1">Saat ini tidak ada memo permohonan yang ditugaskan kepada Anda.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Section Surat Jalan --}}
        <div>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <span class="w-1.5 h-6 bg-amber-500 rounded-full mr-3"></span>
                    Surat Jalan
                </h2>
                <span class="px-3 py-1 bg-amber-50 text-amber-600 text-xs font-bold rounded-full border border-amber-100 italic">Perlu Checkpoint</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($suratJalans as $suratJalan)
                    @php
                        $needsCheckpoint = $suratJalan->status === 'belum masuk checkpoint';
                        $checkpointCompleted = $suratJalan->status === 'checkpoint_completed';
                        $isBongkaran = isset($suratJalan->is_bongkaran) && $suratJalan->is_bongkaran;
                        $checkpointRoute = $isBongkaran 
                            ? route('supir.checkpoint.create-surat-jalan-bongkaran', $suratJalan->id)
                            : route('supir.checkpoint.create-surat-jalan', $suratJalan->id);
                    @endphp
                    <a href="{{ $checkpointRoute }}"
                       class="group bg-white rounded-2xl p-6 border {{ $checkpointCompleted ? 'border-green-200 bg-green-50/30' : ($needsCheckpoint ? 'border-amber-200 bg-amber-50/30' : 'border-gray-200') }} shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden">
                        
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-lg font-black {{ $checkpointCompleted ? 'text-green-700' : ($needsCheckpoint ? 'text-amber-700' : 'text-gray-900') }} tracking-tight">
                                    {{ $suratJalan->no_surat_jalan ?? $suratJalan->nomor_surat_jalan }}
                                </h3>
                                @if($isBongkaran)
                                    <span class="mt-1 inline-block px-2 py-0.5 text-[10px] font-black rounded uppercase bg-purple-100 text-purple-700 border border-purple-200">Bongkaran</span>
                                @else
                                    <span class="mt-1 inline-block px-2 py-0.5 text-[10px] font-black rounded uppercase bg-indigo-100 text-indigo-700 border border-indigo-200">Muat</span>
                                @endif
                            </div>
                            <span class="px-3 py-1 text-[10px] font-black rounded-lg uppercase tracking-tighter
                                {{ $checkpointCompleted ? 'bg-green-100 text-green-700 border border-green-200' :
                                   ($needsCheckpoint ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-blue-100 text-blue-700') }}">
                                {{ str_replace('_', ' ', $suratJalan->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-y-6 gap-x-4 py-4 border-t border-gray-100">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Pengambilan</p>
                                <p class="text-xs font-bold text-gray-800">{{ $suratJalan->tujuan_pengambilan ?? $suratJalan->order->tujuan_ambil ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Pengiriman</p>
                                <p class="text-xs font-bold text-gray-800">{{ $suratJalan->tujuan_pengiriman ?? $suratJalan->order->tujuan_kirim ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Kontainer</p>
                                <p class="text-xs font-bold text-gray-800">{{ $suratJalan->no_kontainer ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Seal</p>
                                <p class="text-xs font-bold text-gray-800">{{ $suratJalan->no_seal ?? '-' }}</p>
                            </div>
                        </div>

                        @if($needsCheckpoint)
                            <div class="mt-4 p-3 rounded-xl bg-amber-100/50 border border-amber-200 flex items-center text-amber-700 text-[10px] font-black uppercase tracking-wider">
                                <i class="fas fa-exclamation-triangle mr-2 text-sm"></i> Segera lakukan checkpoint
                            </div>
                        @elseif($checkpointCompleted)
                            <div class="mt-4 flex items-center text-green-600 text-xs font-bold">
                                <i class="fas fa-check-double mr-2"></i> Checkpoint selesai
                            </div>
                        @endif
                    </a>
                @empty
                    <div class="col-span-1 md:col-span-2 bg-gray-50 rounded-3xl p-12 text-center border-2 border-dashed border-gray-200">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-sm mx-auto mb-4">
                            <i class="fas fa-map-marked-alt text-gray-300 text-2xl"></i>
                        </div>
                        <h3 class="text-gray-900 font-bold">Tidak Ada Surat Jalan</h3>
                        <p class="text-gray-500 text-sm mt-1">Saat ini tidak ada surat jalan yang ditugaskan kepada Anda.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Profil Supir (Integrated into Dashboard) -->
<div id="profilModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden items-center justify-center z-[60] p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all">
        <div class="relative h-32 bg-indigo-600">
            <button onclick="closeModal('profilModal')" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-full text-white transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="px-8 pb-8 -mt-16 text-center">
            <div class="w-24 h-24 bg-white rounded-3xl flex items-center justify-center mx-auto mb-4 shadow-xl border-4 border-white">
                <div class="w-full h-full bg-indigo-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-user-tie text- indigo-600 text-3xl"></i>
                </div>
            </div>
            <h4 class="text-xl font-black text-gray-900">{{ Auth::user()->name }}</h4>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6">Driver Partner</div>
            
            <div class="space-y-3 text-left">
                <div class="p-4 bg-gray-50 rounded-2xl flex items-center">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm mr-4">
                        <i class="fas fa-envelope text-indigo-600"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Email</div>
                        <div class="text-sm font-bold text-gray-800">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 rounded-2xl flex items-center">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm mr-4">
                        <i class="fas fa-user-shield text-indigo-600"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status Akun</div>
                        <div class="text-sm font-bold text-green-600 uppercase">Aktif / Terverifikasi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    // Auto refresh dashboard every 60 seconds
    setTimeout(function() {
        location.reload();
    }, 60000);
</script>
@endpush
