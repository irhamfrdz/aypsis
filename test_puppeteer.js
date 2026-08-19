const puppeteer = require('puppeteer');
(async () => {
    try {
        const browser = await puppeteer.launch({headless: 'new'});
        const page = await browser.newPage();
        
        page.on('console', msg => console.log('PAGE LOG:', msg.text()));
        page.on('pageerror', error => console.log('PAGE ERROR:', error.message));
        
        await page.goto('http://localhost:8000/master/karyawan');
        
        console.log('Clicking button...');
        await page.evaluate(() => {
            document.querySelector('button[onclick=\"openImportDppModal()\"]').click();
        });
        
        await new Promise(r => setTimeout(r, 1000));
        await browser.close();
    } catch(e) {
        console.error(e);
    }
})();
