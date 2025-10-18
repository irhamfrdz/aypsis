{{--
Component: Audit Log Modal - Universal untuk semua halaman
Usage: @include('components.audit-log-modal')
Letakkan di bawah content halaman
--}}

<!-- Audit Log Modal -->
<div id="auditLogModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 transition-all duration-300">
    <div class="relative top-10 mx-auto p-0 border-0 w-full max-w-4xl shadow-2xl rounded-xl bg-white transform transition-all duration-300">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-purple-100">
                    <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Riwayat Perubahan Data</h3>
                    <p class="text-sm text-gray-600" id="auditLogItemName">-</p>
                </div>
            </div>
            <button type="button" onclick="closeAuditLogModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 max-h-96 overflow-y-auto">
            <div id="auditLogLoading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                <p class="mt-2 text-gray-600">Memuat riwayat perubahan...</p>
            </div>

            <div id="auditLogContent" class="hidden">
                <div id="auditLogList" class="space-y-4">
                    <!-- Audit logs will be loaded here -->
                </div>

                <div id="auditLogEmpty" class="text-center py-8 hidden">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="mt-2 text-gray-600">Belum ada riwayat perubahan</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 rounded-b-xl border-t border-gray-200">
            <div class="flex justify-end">
                <button type="button"
                        onclick="closeAuditLogModal()"
                        class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 shadow-sm hover:shadow-md transition-all duration-200">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Universal Audit Log Functions
function showAuditLog(modelType, modelId, itemName) {
    // Set item name
    document.getElementById('auditLogItemName').textContent = itemName;

    // Show modal
    const modal = document.getElementById('auditLogModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Show loading
    document.getElementById('auditLogLoading').classList.remove('hidden');
    document.getElementById('auditLogContent').classList.add('hidden');

    // Fetch audit logs
    fetch('/audit-logs/model', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            model_type: modelType,
            model_id: modelId
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('auditLogLoading').classList.add('hidden');
        document.getElementById('auditLogContent').classList.remove('hidden');

        if (data.success && data.data.length > 0) {
            displayAuditLogs(data.data);
            document.getElementById('auditLogEmpty').classList.add('hidden');
        } else {
            document.getElementById('auditLogList').innerHTML = '';
            document.getElementById('auditLogEmpty').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error fetching audit logs:', error);
        document.getElementById('auditLogLoading').classList.add('hidden');
        document.getElementById('auditLogContent').classList.remove('hidden');
        document.getElementById('auditLogList').innerHTML = '<p class="text-red-600 text-center">Gagal memuat riwayat perubahan</p>';
    });
}

function displayAuditLogs(auditLogs) {
    const container = document.getElementById('auditLogList');
    container.innerHTML = '';

    auditLogs.forEach(log => {
        const logElement = document.createElement('div');
        logElement.className = 'border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors';

        let changesHtml = '';
        if (log.changes && log.changes.length > 0) {
            changesHtml = `
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <h5 class="text-xs font-medium text-gray-700 mb-2">Perubahan:</h5>
                    <div class="space-y-1">
                        ${log.changes.map(change => `
                            <div class="text-xs text-gray-600">
                                <span class="font-medium">${change.field}:</span>
                                <span class="text-red-600">"${change.old || '-'}"</span>
                                <span class="text-gray-400">→</span>
                                <span class="text-green-600">"${change.new || '-'}"</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        const actionBadge = getActionBadge(log.action);

        logElement.innerHTML = `
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-1">
                        ${actionBadge}
                        <span class="text-sm font-medium text-gray-900">${log.description}</span>
                    </div>
                    <div class="flex items-center space-x-4 text-xs text-gray-500">
                        <span>👤 ${log.user_name}</span>
                        <span>📅 ${log.created_at}</span>
                    </div>
                    ${changesHtml}
                </div>
            </div>
        `;

        container.appendChild(logElement);
    });
}

function getActionBadge(action) {
    const badges = {
        'created': '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Dibuat</span>',
        'updated': '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Diubah</span>',
        'deleted': '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Dihapus</span>',
        'viewed': '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Dilihat</span>'
    };

    return badges[action] || `<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">${action}</span>`;
}

function closeAuditLogModal() {
    const modal = document.getElementById('auditLogModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close audit log modal when clicking outside
document.getElementById('auditLogModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAuditLogModal();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAuditLogModal();
    }
});
</script>
@endpush
