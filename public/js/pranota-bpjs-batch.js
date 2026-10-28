(function (root) {
    root.processBpjsBatch = async function (items, processItem, progress = () => {},
        pause = () => new Promise(resolve => setTimeout(resolve, 0))) {
        progress(0, items.length);
        await pause();
        for (let start = 0; start < items.length; start += 12) {
            const end = Math.min(start + 12, items.length);
            for (let index = start; index < end; index++) processItem(items[index], index);
            progress(end, items.length);
            if (end < items.length) await pause();
        }
    };
})(globalThis);
