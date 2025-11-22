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
