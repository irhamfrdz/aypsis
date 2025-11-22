{{-- 
    Resizable Table Component
    
    Usage: Add this to any blade file with a table
    1. Include this component after @extends and @section
    2. Add id="yourTableId" to your table element
    3. Add class="resizable-table" to your table element  
    4. Add class="resizable-th" to each <th> you want to be resizable
    5. Add style="position: relative;" to each resizable <th>
    6. Add <div class="resize-handle"></div> inside each resizable <th>
    7. Call initResizableTable('yourTableId') in your JavaScript
    
    Example:
    <table class="min-w-full divide-y divide-gray-200 resizable-table" id="myTable">
        <thead>
            <tr>
                <th class="resizable-th px-6 py-3" style="position: relative;">
                    Column Name
                    <div class="resize-handle"></div>
                </th>
            </tr>
        </thead>
    </table>
    
    <script>
    $(document).ready(function() {
        initResizableTable('myTable');
    });
    </script>
--}}

@push('styles')
<style>
.resize-handle {
    position: absolute;
    top: 0;
    right: 0;
    width: 8px;
    height: 100%;
    cursor: col-resize;
    user-select: none;
    background: linear-gradient(to right, transparent 0%, #d1d5db 50%, transparent 100%);
    z-index: 10;
}

.resize-handle:hover {
    background: linear-gradient(to right, transparent 0%, #6366f1 50%, transparent 100%);
    width: 10px;
}

.resize-handle::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 2px;
    height: 20px;
    background: #9ca3af;
}

.resize-handle:hover::after {
    background: #6366f1;
    height: 30px;
}

.resizable-th {
    min-width: 80px;
    max-width: 600px;
    border-right: 1px solid #e5e7eb;
}

.resizable-table th {
    white-space: nowrap;
    overflow: hidden;
}
</style>
@endpush

@push('scripts')
<script>
function initResizableTable(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const headers = table.querySelectorAll('.resizable-th');
    let currentHeader = null;
    let startX = 0;
    let startWidth = 0;
    
    headers.forEach(header => {
        const handle = header.querySelector('.resize-handle');
        if (!handle) return;
        
        handle.addEventListener('mousedown', function(e) {
            currentHeader = header;
            startX = e.pageX;
            startWidth = header.offsetWidth;
            
            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
            
            e.preventDefault();
        });
    });
    
    function onMouseMove(e) {
        if (!currentHeader) return;
        
        const diff = e.pageX - startX;
        const newWidth = Math.max(80, Math.min(600, startWidth + diff));
        currentHeader.style.width = newWidth + 'px';
        currentHeader.style.minWidth = newWidth + 'px';
        currentHeader.style.maxWidth = newWidth + 'px';
    }
    
    function onMouseUp() {
        currentHeader = null;
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);
    }
}
</script>
@endpush
