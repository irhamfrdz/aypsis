<div class="flex items-center justify-end space-x-2">
    @if($row->status == 'pending')
        @if(auth()->user()->can('approval-absensi-lupa-approve'))
            <form action="{{ route('master.persetujuan-absensi-lupa.approve', $row->id) }}" method="POST" class="inline">
                @csrf
                <button type="button" class="btn-approve text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 p-1.5 rounded-md transition-colors" title="Setujui">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </form>
            <button type="button" onclick="openRejectModal('{{ route('master.persetujuan-absensi-lupa.reject', $row->id) }}')" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded-md transition-colors" title="Tolak">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endif
        
        @if(auth()->user()->can('approval-absensi-lupa-edit'))
            <a href="{{ route('master.persetujuan-absensi-lupa.edit', $row->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-md transition-colors" title="Edit">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
        @endif
        
        @if(auth()->user()->can('approval-absensi-lupa-delete'))
            <button type="button" data-url="{{ route('master.persetujuan-absensi-lupa.destroy', $row->id) }}" class="btn-delete text-gray-600 hover:text-red-900 bg-gray-50 hover:bg-red-100 p-1.5 rounded-md transition-colors" title="Hapus">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        @endif
    @else
        <span class="text-xs text-gray-500 italic">Sudah diproses</span>
    @endif
</div>
