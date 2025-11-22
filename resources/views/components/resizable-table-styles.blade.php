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
